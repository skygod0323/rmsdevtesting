<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

/** @noinspection PhpUnusedParameterInspection, ReturnTypeCanBeDeclaredInspection, AccessModifierPresentedInspection */

require_once "$config[project_path]/admin/include/functions_base.php";
require_once "$config[project_path]/admin/include/functions.php";
require_once "$config[project_path]/admin/include/functions_admin.php";

abstract class KvsPaymentProcessor
{
	const MESSAGE_TYPE_DEBUG = 0;
	const MESSAGE_TYPE_INFO = 1;
	const MESSAGE_TYPE_ERROR = 2;

	const TRANSACTION_TYPE_INITIAL = 1;
	const TRANSACTION_TYPE_CONVERSION = 2;
	const TRANSACTION_TYPE_REBILL = 3;
	const TRANSACTION_TYPE_CHARGEBACK = 4;
	const TRANSACTION_TYPE_REFUND = 5;
	const TRANSACTION_TYPE_VOID = 6;
	const TRANSACTION_TYPE_TOKENS = 10;

	const TRANSACTION_STATUS_OPEN = 1;
	const TRANSACTION_STATUS_CLOSED = 2;
	const TRANSACTION_STATUS_CANCELLED = 3;

	const USER_STATUS_STANDARD = 2;
//	const USER_STATUS_PREMIUM = 2;
	const USER_STATUS_PREMIUM = 3;

	/** @var string */
	private $project_path;

	/** @var string */
	private $project_url;

	/** @var array */
	private $provider_data;

	/** @var bool */
	private $error_logged = false;

	/** @var bool */
	private $is_postback = false;

	/** @var string */
	protected $request_log = "";

	/** @var string */
	protected $tables_prefix;

	/**
	 * Constructor.
	 */
	function __construct()
	{
		global $config;

		$this->project_path = $config["project_path"];
		$this->project_url = $config["project_url"];
		$this->tables_prefix = $config["tables_prefix"];
	}

	/**
	 * Returns provider ID.
	 *
	 * @return string
	 */
	abstract public function get_provider_id();

	/**
	 * Returns example payment URL for the documentation.
	 *
	 * @return string
	 */
	public function get_example_payment_url()
	{
		return "";
	}

	/**
	 * Returns example payment URL for the documentation.
	 *
	 * @return string
	 */
	public function get_example_oneclick_url()
	{
		return "";
	}

	/**
	 * Returns payment page URL for the given access package and user data (username, password, email).
	 *
	 * @param array $access_package
	 * @param string $signup_page_url
	 * @param array $user_data
	 *
	 * @return string
	 */
	abstract public function get_payment_page_url($access_package, $signup_page_url, $user_data);

	/**
	 * Processes request.
	 */
	public function process_request()
	{
		$this->is_postback = true;
		$this->provider_data = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_providers WHERE internal_id=?", $this->get_provider_id()));

		if ($this->is_request_requires_basic_auth())
		{
			if (!isset($_SERVER["PHP_AUTH_USER"]))
			{
				header("WWW-Authenticate: Basic realm=\"KVS Payment Processor\"");
				header("HTTP/1.1 401 Unauthorized");

				return false;
			}
		}

		$result = false;
		if ($this->is_request_allowed())
		{
			$this->request_log = "";

			$logged_params = $this->get_logged_request_params();
			$shredded_params = $this->get_shredded_request_params();
			foreach ($_REQUEST as $k => $v)
			{
				if (in_array($k, $shredded_params))
				{
					if ($v != "")
					{
						$v = $this->shred_param_value($k, $v);
					}
				}
				if (in_array($k, $logged_params))
				{
					$this->request_log .= "$k: $v\n";
				}
			}
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Postback triggered from IP $_SERVER[REMOTE_ADDR]", $this->request_log);

			$result = $this->process_request_impl();
			if (!$result && !$this->error_logged)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Failed to process request");
			}
		}

		if ($this->provider_data["postback_repost_url"] != "")
		{
			$post_fields = array();
			$repost_url = $this->provider_data["postback_repost_url"];

			foreach ($_REQUEST as $k => $v)
			{
				$post_fields[$k] = urlencode($v);
				if ($_SERVER["REQUEST_METHOD"] == "GET")
				{
					if (strpos($repost_url, "?") === false)
					{
						$repost_url .= "?$k=" . urlencode($v);
					} else
					{
						$repost_url .= "&$k=" . urlencode($v);
					}
				}
			}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_USERAGENT, "KVS");
			curl_setopt($ch, CURLOPT_URL, $repost_url);
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
			}
			curl_setopt($ch, CURLOPT_TIMEOUT, 20);
			curl_exec($ch);
			if (curl_errno($ch) > 0)
			{
				file_put_contents("{$this->project_path}/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] [" . curl_errno($ch) . "] " . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
			}
			curl_close($ch);
		}

		return $result;
	}

	/**
	 * Processes schedule every 10 minutes.
	 */
	public function process_schedule()
	{
		$this->provider_data = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_providers WHERE internal_id=?", $this->get_provider_id()));

		try
		{
			$this->process_schedule_impl();
		} catch (Exception $e)
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Failed to process schedule", get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString());
		}
	}

	/**
	 * Processes password change of the user added by this payment processor.
	 *
	 * @param string $user_id
	 * @param string $new_password
	 *
	 * @return bool
	 */
	public function process_password_change($user_id, $new_password)
	{
		return true;
	}

	/**
	 * Processes email change of the user added by this payment processor.
	 *
	 * @param string $user_id
	 * @param string $new_email
	 *
	 * @return bool
	 */
	public function process_email_change($user_id, $new_email)
	{
		return true;
	}

	/**
	 * Returns whether this provider does not support transaction expiration from postbacks and requires them to expire
	 * automatically.
	 *
	 * @return bool
	 */
	protected function requires_auto_expire()
	{
		return false;
	}

	/**
	 * Checks access protection settings if any.
	 *
	 * @return bool
	 */
	protected function is_request_allowed()
	{
		if ($this->is_request_requires_basic_auth())
		{
			if ($this->provider_data["postback_username"] != $_SERVER["PHP_AUTH_USER"] || $this->provider_data["postback_password"] != $_SERVER["PHP_AUTH_PW"])
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Username <$_SERVER[PHP_AUTH_USER]> and password <$_SERVER[PHP_AUTH_PW]> are not allowed");

				header("WWW-Authenticate: Basic realm=\"KVS Payment Processor\"");
				header("HTTP/1.1 401 Unauthorized");

				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if request processing requires basic auth to proceed.
	 *
	 * @return bool
	 */
	protected function is_request_requires_basic_auth()
	{
		return false;
	}

	/**
	 * Processor-specific implementation.
	 *
	 * @return bool
	 */
	abstract protected function process_request_impl();

	/**
	 * Processor-specific implementation.
	 */
	protected function process_schedule_impl()
	{
	}

	/**
	 * Returns list of request parameters that should be logged and stored in KVS logs.
	 *
	 * @return array
	 */
	protected function get_logged_request_params()
	{
		return array();
	}

	/**
	 * Returns list of request parametersm which values should be discarded from logging (e.g. passwords, names,
	 * privacy-related data).
	 *
	 * @return array
	 */
	protected function get_shredded_request_params()
	{
		return array();
	}

	/**
	 * Returns shredded value for a parameter, may be used to partially shred compound parameters.
	 *
	 * @param string $name
	 * @param string $value
	 *
	 * @return string
	 */
	protected function shred_param_value($name, $value)
	{
		return "[deleted]";
	}

	/**
	 * Processes declined transaction.
	 *
	 * @param string $transaction_id
	 *
	 * @return bool
	 */
	protected function process_decline($transaction_id)
	{
		if (sql_update("UPDATE {$this->tables_prefix}bill_outs SET declines_amount=declines_amount+1 WHERE added_date=?", date("Y-m-d")) == 0)
		{
			sql_insert("INSERT INTO {$this->tables_prefix}bill_outs SET declines_amount=1, added_date=?", date("Y-m-d"));
		}
		$this->log_message(self::MESSAGE_TYPE_INFO, "Transaction declined (transaction #$transaction_id)", $this->request_log);

		return true;
	}

	/**
	 * Returns true if the given username exist in KVS database and cannot be used to register new member.
	 *
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_username_check($username)
	{
		if (mr2number(sql_pr("SELECT COUNT(*) FROM {$this->tables_prefix}users WHERE username=?", $username)) > 0)
		{
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Username check: <$username> exists");

			return true;
		} else
		{
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Username check: <$username> available");

			return false;
		}
	}

	/**
	 * Processes initial subscription or tokens purchase for a user. If user with the given username doesn't exist, it
	 * will be created.
	 *
	 * @param string $transaction_id
	 * @param string $transaction_guid
	 * @param string $subscription_id
	 * @param float $price
	 * @param string $currency_code
	 * @param string $access_package_id
	 * @param bool $is_trial
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $ip
	 * @param string $country_code
	 * @param int $duration_initial
	 * @param int $duration_rebill
	 *
	 * @return bool
	 */
	protected function process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code, $duration_initial = 0, $duration_rebill = 0)
	{
		$transaction_id = trim($transaction_id);
		$transaction_guid = trim($transaction_guid);
		$subscription_id = trim($subscription_id);
		$price = floatval($price);
		$currency_code = trim($currency_code);
		$access_package_id = trim($access_package_id);
		$username = trim($username);
		$password = trim($password);
		$email = trim($email);
		$ip = '' . ip2int(trim($ip));
		$country_code = strtolower(trim($country_code));
		$duration_initial = intval($duration_initial);
		$duration_rebill = intval($duration_rebill);

		if (!$username)
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Username is empty");
			return false;
		}

		if ($access_package_id)
		{
			$package = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_packages WHERE provider_id=? AND external_id=? LIMIT 1", $this->provider_data["provider_id"], $access_package_id));
			if (intval($package["package_id"]) == 0)
			{
				if ($duration_initial > 0)
				{
					$package = array("tokens" => 0, "duration_initial" => 0, "duration_rebill" => 0);
				} else
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Access package is not found for ID $access_package_id");
					return false;
				}
			}
		} else
		{
			$package = array("tokens" => 0, "duration_initial" => 0, "duration_rebill" => 0);
		}

		$reseller_code = "";
		if ($this->provider_data["postback_reseller_param"])
		{
			$reseller_code = trim($_REQUEST[$this->provider_data["postback_reseller_param"]]);
		}

		$tokens_granted = intval($package["tokens"]);
		if (!$duration_initial)
		{
			$duration_initial = intval($package["duration_initial"]);
		}
		if (!$duration_rebill)
		{
			$duration_rebill = intval($package["duration_rebill"]);
		}
		if (!$is_trial && $duration_rebill > 0 && $duration_rebill != $duration_initial)
		{
			$is_trial = true;
		}
		$access_start_date = date("Y-m-d H:i:s");
		$access_end_date = $access_start_date;
		$access_is_unlimited = 0;
		$transaction_type_id = self::TRANSACTION_TYPE_INITIAL;
		$transaction_status_id = self::TRANSACTION_STATUS_OPEN;
		$user_status_id = self::USER_STATUS_PREMIUM;
		$is_auto_expire = 0;

		if ($is_trial && $this->provider_data["options"])
		{
			$provider_options = @unserialize($this->provider_data["options"]);
			if (is_array($provider_options) && intval($provider_options["is_trials_as_active"]) == 1)
			{
				$tokens_granted = intval($provider_options["trial_tokens"]);
				$user_status_id = self::USER_STATUS_STANDARD;
			}
		}

		if ($tokens_granted > 0)
		{
			$transaction_type_id = self::TRANSACTION_TYPE_TOKENS;
			$transaction_status_id = self::TRANSACTION_STATUS_CLOSED;
			$user_status_id = self::USER_STATUS_STANDARD;
		} elseif ($duration_initial > 0)
		{
			$access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $duration_initial, date("Y")));
			if ($this->requires_auto_expire())
			{
				$is_auto_expire = 1;
				// give 12 more hours to allow provider send another rebill, if not expired
				$access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $duration_initial, date("Y")) + 43200);
			}
		} else
		{
			$access_end_date = "2070-01-01 00:00:00";
			$access_is_unlimited = 1;
		}


		$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
		if ($user_id > 0)
		{
			// user exists, update their status
			if ($transaction_type_id == self::TRANSACTION_TYPE_TOKENS)
			{
				// for tokens transaction we want just to increment tokens
				if (!$password)
				{
					sql_update("UPDATE {$this->tables_prefix}users SET tokens_available=tokens_available+? WHERE user_id=?", $tokens_granted, $user_id);
				} else
				{
					sql_update("UPDATE {$this->tables_prefix}users SET tokens_available=tokens_available+?, pass_bill=? WHERE user_id=?", $tokens_granted, generate_password_hash($password), $user_id);
				}
			} else
			{
				// for subscription we want to update status and close any other open transactions
				if (!$password)
				{
					sql_update("UPDATE {$this->tables_prefix}users SET status_id=?, is_trial=?, reseller_code=? WHERE user_id=?", $user_status_id, ($is_trial ? 1 : 0), $reseller_code, $user_id);
				} else
				{
					sql_update("UPDATE {$this->tables_prefix}users SET status_id=?, is_trial=?, reseller_code=?, pass_bill=? WHERE user_id=?", $user_status_id, ($is_trial ? 1 : 0), $reseller_code, generate_password_hash($password), $user_id);
				}

				// close any open transactions for this user
				sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=2, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
			}
		} else
		{
			// create new user
			if (!$username || !$email)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Username or email is not provided to create a new user");

				return false;
			}
			$country_id = ($country_code ? mr2number(sql_pr("SELECT country_id FROM {$this->tables_prefix}list_countries WHERE country_code=? LIMIT 1", $country_code)) : 0);
			$user_id = sql_insert("INSERT INTO {$this->tables_prefix}users SET ip=?, country_id=?, status_id=?, is_trial=?, username=?, pass=?, email=?, display_name=?, reseller_code=?, tokens_available=?, added_date=?", $ip, $country_id, $user_status_id, ($is_trial ? 1 : 0), $username, generate_password_hash($password), $email, $username, $reseller_code, $tokens_granted, date("Y-m-d H:i:s"));
		}

		sql_insert("INSERT INTO {$this->tables_prefix}bill_transactions SET bill_type_id=2, external_id=?, external_guid=?, external_purchase_id=?, external_package_id=?, internal_provider_id=?, status_id=?, type_id=?, user_id=?, is_trial=?, duration_rebill=?, transaction_log=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, is_auto_expire=?, ip=?, country_code=?, tokens_granted=?, price=?, currency_code=?",
			$transaction_id, $transaction_guid, $subscription_id, $access_package_id, $this->get_provider_id(), $transaction_status_id, $transaction_type_id, $user_id, ($is_trial ? 1 : 0), $duration_rebill, $this->request_log, $access_start_date, $access_end_date, $access_is_unlimited, $is_auto_expire, $ip, $country_code, $tokens_granted, $price, $currency_code
		);

		if ($tokens_granted > 0)
		{
			$this->log_message(self::MESSAGE_TYPE_INFO, "User_$user_id purchased $tokens_granted tokens (transaction #$transaction_id)", $this->request_log);
		} elseif ($is_trial)
		{
			$this->log_message(self::MESSAGE_TYPE_INFO, "Trial {$duration_initial}-day subscription $subscription_id is started for User_$user_id (transaction #$transaction_id)", $this->request_log);
		} elseif ($access_is_unlimited)
		{
			$this->log_message(self::MESSAGE_TYPE_INFO, "Unlimited subscription $subscription_id is started for User_$user_id (transaction #$transaction_id)", $this->request_log);
		} else
		{
			$this->log_message(self::MESSAGE_TYPE_INFO, "Initial {$duration_initial}-day subscription $subscription_id is started for User_$user_id (transaction #$transaction_id)", $this->request_log);
		}

		return true;
	}

	/**
	 * Processes rebill or conversion for the subscription. Either subscription ID or username should be provided.
	 *
	 * @param string $transaction_id
	 * @param string $transaction_guid
	 * @param string $subscription_id
	 * @param float $price
	 * @param string $currency_code
	 * @param string $access_package_id
	 * @param string $username
	 * @param string $password
	 * @param string $email
	 * @param string $ip
	 * @param string $country_code
	 * @param int $duration_initial
	 *
	 * @return bool
	 */
	protected function process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id = "", $username = "", $password = "", $email = "", $ip = "", $country_code = "", $duration_initial = 0)
	{
		$transaction_id = trim($transaction_id);
		$transaction_guid = trim($transaction_guid);
		$subscription_id = trim($subscription_id);
		$price = floatval($price);
		$currency_code = trim($currency_code);
		$access_package_id = trim($access_package_id);
		$username = trim($username);
		$password = trim($password);
		$email = trim($email);
		$ip = trim($ip);
		$country_code = trim($country_code);

		$initial_transaction = array();
		if ($subscription_id)
		{
			$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY type_id ASC, transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
		}
		if (intval($initial_transaction["transaction_id"]) == 0 && $username)
		{
			$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
			if ($user_id > 0)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY type_id ASC, transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
			} elseif ($password && $email)
			{
				// looks to be rebill for non-existing user, if password and email are provided, create initial transaction
				return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code, $duration_initial, $duration_initial);
			}
		}

		if (intval($initial_transaction["transaction_id"]) == 0)
		{
			if ($subscription_id)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
			} else
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
			}

			return false;
		}

		$user_id = intval($initial_transaction["user_id"]);

		$duration_rebill = 0;
		$transaction_type_id = self::TRANSACTION_TYPE_REBILL;
		$transaction_status_id = self::TRANSACTION_STATUS_OPEN;
		$tokens_granted = 0;
		$access_start_date = date("Y-m-d H:i:s");
		$access_end_date = $access_start_date;
		$access_is_unlimited = 0;
		$is_auto_expire = 0;

		if ($access_package_id)
		{
			$package = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_packages WHERE provider_id=? AND external_id=? LIMIT 1", $this->provider_data["provider_id"], $access_package_id));
			if (intval($package["tokens"]) > 0)
			{
				// rebill comes with tokens access package, handle it as tokens transaction
				$transaction_type_id = self::TRANSACTION_TYPE_TOKENS;
				$transaction_status_id = self::TRANSACTION_STATUS_CLOSED;
				$tokens_granted = intval($package["tokens"]);
			}
		}

		if (intval($initial_transaction["type_id"]) == self::TRANSACTION_TYPE_TOKENS && intval($initial_transaction["is_trial"]) == 0)
		{
			// tokens transaction looks to be repeated by payment processor
			$transaction_type_id = self::TRANSACTION_TYPE_TOKENS;
			$transaction_status_id = self::TRANSACTION_STATUS_CLOSED;
			$tokens_granted = intval($initial_transaction["tokens_granted"]);
		} else
		{
			if (mr2number(sql_pr("SELECT is_trial FROM {$this->tables_prefix}users WHERE user_id=?", $user_id)) == 1)
			{
				$transaction_type_id = self::TRANSACTION_TYPE_CONVERSION;
			}

			$duration_rebill = intval($initial_transaction["duration_rebill"]);
			if ($duration_rebill == 0)
			{
				$access_end_date = "2070-01-01 00:00:00";
				$access_is_unlimited = 1;
			} else
			{
				$access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $duration_rebill, date("Y")));
				if ($this->requires_auto_expire())
				{
					$is_auto_expire = 1;
					// give 24 more hours to allow provider send another rebill, if not expired
					$access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + $duration_rebill, date("Y")) + 43200);
				}
			}
		}

		if ($transaction_type_id != self::TRANSACTION_TYPE_TOKENS)
		{
			// close any previous open transactions
			sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=2, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
		}

		// new transaction
		sql_insert("INSERT INTO {$this->tables_prefix}bill_transactions SET bill_type_id=2, external_id=?, external_guid=?, external_purchase_id=?, internal_provider_id=?, status_id=?, type_id=?, user_id=?, duration_rebill=?, transaction_log=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, is_auto_expire=?, tokens_granted=?, price=?, currency_code=?",
			$transaction_id, $transaction_guid, $subscription_id, $this->get_provider_id(), $transaction_status_id, $transaction_type_id, $user_id, $duration_rebill, $this->request_log, $access_start_date, $access_end_date, $access_is_unlimited, $is_auto_expire, $tokens_granted, $price, $currency_code
		);

		if ($transaction_type_id == self::TRANSACTION_TYPE_TOKENS)
		{
			sql_update("UPDATE {$this->tables_prefix}users SET tokens_available=tokens_available+? WHERE user_id=?", $tokens_granted, $user_id);
			$this->log_message(self::MESSAGE_TYPE_INFO, "User_$user_id purchased $tokens_granted tokens (transaction #$transaction_id)", $this->request_log);
		} else
		{
			sql_update("UPDATE {$this->tables_prefix}users SET status_id=3, is_trial=0 WHERE user_id=?", $user_id);
			if ($transaction_type_id == self::TRANSACTION_TYPE_CONVERSION)
			{
				$this->log_message(self::MESSAGE_TYPE_INFO, "Conversion on subscription $subscription_id for User_$user_id (transaction #$transaction_id)", $this->request_log);
			} else
			{
				$this->log_message(self::MESSAGE_TYPE_INFO, "Rebill on subscription $subscription_id for User_$user_id (transaction #$transaction_id)", $this->request_log);
			}
		}

		return true;
	}

	/**
	 * Processes chargeback of the given transaction. If no related transaction ID is available, either subscription ID or
	 * username should be provided.
	 *
	 * @param string $transaction_id
	 * @param string $transaction_guid
	 * @param string $subscription_id
	 * @param float $price
	 * @param string $currency_code
	 * @param string $related_transaction_id
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id = "", $username = "")
	{
		$transaction_id = trim($transaction_id);
		$transaction_guid = trim($transaction_guid);
		$subscription_id = trim($subscription_id);
		$price = floatval($price);
		$currency_code = trim($currency_code);
		$related_transaction_id = trim($related_transaction_id);
		$username = trim($username);

		$user_id = 0;
		if ($related_transaction_id)
		{
			$related_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE (external_id=? OR external_guid=?) AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $related_transaction_id, $related_transaction_id, $this->get_provider_id()));
			$user_id = intval($related_transaction["user_id"]);
			if ($user_id > 0)
			{
				if ($related_transaction["type_id"] == self::TRANSACTION_TYPE_TOKENS && $related_transaction["tokens_granted"] > 0)
				{
					// tokens transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3 WHERE transaction_id=?", $related_transaction["transaction_id"]);
					revoke_tokens_from_user($user_id, $related_transaction["tokens_granted"]);
					$this->log_message(self::MESSAGE_TYPE_INFO, "Chargeback on $related_transaction[tokens_granted] tokens purchase for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				} else
				{
					// subscription transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=CASE WHEN status_id=1 THEN ? ELSE access_end_date END, is_unlimited_access=0 WHERE transaction_id=?", date("Y-m-d H:i:s"), $related_transaction["transaction_id"]);
					if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
					{
						// no other open transactions for this user, make sure to change their premium status
						sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
					}
					$this->log_message(self::MESSAGE_TYPE_INFO, "Chargeback on subscription $related_transaction[external_purchase_id] for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				}
			}
		}

		if ($user_id == 0)
		{
			// no related transaction ID is provided or such transaction does not exist
			$initial_transaction = array();
			if ($subscription_id)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
			}
			if (intval($initial_transaction["transaction_id"]) == 0 && $username)
			{
				$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
				if ($user_id > 0)
				{
					$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
				}
			}

			if (intval($initial_transaction["transaction_id"]) == 0)
			{
				if ($subscription_id)
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
				} else
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
				}

				return false;
			}

			$user_id = intval($initial_transaction["user_id"]);

			// assuming that chargeback is for the last open transaction
			sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
			if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
			{
				// no other open transactions for this user, make sure to change their premium status
				sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
			}
			$this->log_message(self::MESSAGE_TYPE_INFO, "Chargeback on subscription $initial_transaction[external_purchase_id] for User_$user_id (no transaction info)", $this->request_log);
		}

		sql_insert("INSERT INTO {$this->tables_prefix}bill_transactions SET bill_type_id=2, status_id=2, type_id=4, external_id=?, external_guid=?, external_purchase_id=?, internal_provider_id=?, user_id=?, transaction_log=?, access_start_date=?, access_end_date=?, price=?, currency_code=?",
			$transaction_id, $transaction_guid, $subscription_id, $this->get_provider_id(), $user_id, $this->request_log, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $price, $currency_code
		);

		return true;
	}

	/**
	 * Processes refund of the given transaction. If no related transaction ID is available, either subscription ID or
	 * username should be provided.
	 *
	 * @param string $transaction_id
	 * @param string $transaction_guid
	 * @param string $subscription_id
	 * @param float $price
	 * @param string $currency_code
	 * @param string $related_transaction_id
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id = "", $username = "")
	{
		$transaction_id = trim($transaction_id);
		$transaction_guid = trim($transaction_guid);
		$subscription_id = trim($subscription_id);
		$price = floatval($price);
		$currency_code = trim($currency_code);
		$related_transaction_id = trim($related_transaction_id);
		$username = trim($username);

		$user_id = 0;
		if ($related_transaction_id)
		{
			$related_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE (external_id=? OR external_guid=?) AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $related_transaction_id, $related_transaction_id, $this->get_provider_id()));
			$user_id = intval($related_transaction["user_id"]);
			if ($user_id > 0)
			{
				if ($related_transaction["type_id"] == self::TRANSACTION_TYPE_TOKENS && $related_transaction["tokens_granted"] > 0)
				{
					// tokens transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3 WHERE transaction_id=?", $related_transaction["transaction_id"]);
					revoke_tokens_from_user($user_id, $related_transaction["tokens_granted"]);
					$this->log_message(self::MESSAGE_TYPE_INFO, "Refund on $related_transaction[tokens_granted] tokens purchase for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				} else
				{
					// subscription transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=CASE WHEN status_id=1 THEN ? ELSE access_end_date END, is_unlimited_access=0 WHERE transaction_id=?", date("Y-m-d H:i:s"), $related_transaction["transaction_id"]);
					if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
					{
						// no other open transactions for this user, make sure to change their premium status
						sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
					}
					$this->log_message(self::MESSAGE_TYPE_INFO, "Refund on subscription $related_transaction[external_purchase_id] for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				}
			}
		}

		if ($user_id == 0)
		{
			// no related transaction ID is provided or such transaction does not exist
			$initial_transaction = array();
			if ($subscription_id)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
			}
			if (intval($initial_transaction["transaction_id"]) == 0 && $username)
			{
				$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
				if ($user_id > 0)
				{
					$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
				}
			}

			if (intval($initial_transaction["transaction_id"]) == 0)
			{
				if ($subscription_id)
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
				} else
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
				}

				return false;
			}

			$user_id = intval($initial_transaction["user_id"]);

			// assuming that refund is for the last open transaction
			sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
			if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
			{
				// no other open transactions for this user, make sure to change their premium status
				sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
			}
			$this->log_message(self::MESSAGE_TYPE_INFO, "Refund on subscription $initial_transaction[external_purchase_id] for User_$user_id (no transaction info)", $this->request_log);
		}

		sql_insert("INSERT INTO {$this->tables_prefix}bill_transactions SET bill_type_id=2, status_id=2, type_id=5, external_id=?, external_guid=?, external_purchase_id=?, internal_provider_id=?, user_id=?, transaction_log=?, access_start_date=?, access_end_date=?, price=?, currency_code=?",
			$transaction_id, $transaction_guid, $subscription_id, $this->get_provider_id(), $user_id, $this->request_log, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $price, $currency_code
		);

		return true;
	}

	/**
	 * Processes void of the given transaction. If no related transaction ID is available, either subscription ID or
	 * username should be provided.
	 *
	 * @param string $transaction_id
	 * @param string $transaction_guid
	 * @param string $subscription_id
	 * @param float $price
	 * @param string $currency_code
	 * @param string $related_transaction_id
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id = "", $username = "")
	{
		$transaction_id = trim($transaction_id);
		$transaction_guid = trim($transaction_guid);
		$subscription_id = trim($subscription_id);
		$price = floatval($price);
		$currency_code = trim($currency_code);
		$related_transaction_id = trim($related_transaction_id);
		$username = trim($username);

		$user_id = 0;
		if ($related_transaction_id)
		{
			$related_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE (external_id=? OR external_guid=?) AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $related_transaction_id, $related_transaction_id, $this->get_provider_id()));
			$user_id = intval($related_transaction["user_id"]);
			if ($user_id > 0)
			{
				if ($related_transaction["type_id"] == self::TRANSACTION_TYPE_TOKENS && $related_transaction["tokens_granted"] > 0)
				{
					// tokens transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3 WHERE transaction_id=?", $related_transaction["transaction_id"]);
					revoke_tokens_from_user($user_id, $related_transaction["tokens_granted"]);
					$this->log_message(self::MESSAGE_TYPE_INFO, "Void on $related_transaction[tokens_granted] tokens purchase for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				} else
				{
					// subscription transaction
					sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=CASE WHEN status_id=1 THEN ? ELSE access_end_date END, is_unlimited_access=0 WHERE transaction_id=?", date("Y-m-d H:i:s"), $related_transaction["transaction_id"]);
					if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
					{
						// no other open transactions for this user, make sure to change their premium status
						sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
					}
					$this->log_message(self::MESSAGE_TYPE_INFO, "Void on subscription $related_transaction[external_purchase_id] for User_$user_id (transaction #$related_transaction_id)", $this->request_log);
				}
			}
		}

		if ($user_id == 0)
		{
			// no related transaction ID is provided or such transaction does not exist
			$initial_transaction = array();
			if ($subscription_id)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
			}
			if (intval($initial_transaction["transaction_id"]) == 0 && $username)
			{
				$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
				if ($user_id > 0)
				{
					$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
				}
			}

			if (intval($initial_transaction["transaction_id"]) == 0)
			{
				if ($subscription_id)
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
				} else
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
				}

				return false;
			}

			$user_id = intval($initial_transaction["user_id"]);

			// assuming that void is for the last open transaction
			sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=3, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
			if (mr2number(sql_pr("SELECT count(*) FROM {$this->tables_prefix}bill_transactions WHERE status_id=1 AND user_id=?", $user_id)) == 0)
			{
				// no other open transactions for this user, make sure to change their premium status
				sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
			}
			$this->log_message(self::MESSAGE_TYPE_INFO, "Void on subscription $initial_transaction[external_purchase_id] for User_$user_id (no transaction info)", $this->request_log);
		}

		sql_insert("INSERT INTO {$this->tables_prefix}bill_transactions SET bill_type_id=2, status_id=2, type_id=6, external_id=?, external_guid=?, external_purchase_id=?, internal_provider_id=?, user_id=?, transaction_log=?, access_start_date=?, access_end_date=?, price=?, currency_code=?",
			$transaction_id, $transaction_guid, $subscription_id, $this->get_provider_id(), $user_id, $this->request_log, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $price, $currency_code
		);

		return true;
	}

	/**
	 * Processes subscription expiration. Either subscription ID or username should be provided.
	 *
	 * @param string $subscription_id
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_expiration($subscription_id, $username = "")
	{
		$subscription_id = trim($subscription_id);
		$username = trim($username);

		$initial_transaction = array();
		if ($subscription_id)
		{
			$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
		}
		if (intval($initial_transaction["transaction_id"]) == 0 && $username)
		{
			$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
			if ($user_id > 0)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
			}
		}

		if (intval($initial_transaction["transaction_id"]) == 0)
		{
			if ($subscription_id)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
			} else
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
			}

			return false;
		}

		$user_id = intval($initial_transaction["user_id"]);

		sql_update("UPDATE {$this->tables_prefix}bill_transactions SET status_id=2, access_end_date=?, is_unlimited_access=0 WHERE status_id=1 AND user_id=?", date("Y-m-d H:i:s"), $user_id);
		sql_update("UPDATE {$this->tables_prefix}users SET status_id=? WHERE status_id=3 AND user_id=?", $this->get_status_after_premium(), $user_id);
		$this->log_message(self::MESSAGE_TYPE_INFO, "Expire of subscription $initial_transaction[external_purchase_id] for User_$user_id", $this->request_log);

		return true;
	}

	/**
	 * Processes subscription cancellation. Either subscription ID or username should be provided.
	 *
	 * @param string $subscription_id
	 * @param string $username
	 *
	 * @return bool
	 */
	protected function process_cancellation($subscription_id, $username = "")
	{
		$subscription_id = trim($subscription_id);
		$username = trim($username);

		$initial_transaction = array();
		if ($subscription_id)
		{
			$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND external_purchase_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $subscription_id, $this->get_provider_id()));
		}
		if (intval($initial_transaction["transaction_id"]) == 0 && $username)
		{
			$user_id = mr2number(sql_pr("SELECT user_id FROM {$this->tables_prefix}users WHERE username=?", $username));
			if ($user_id > 0)
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE type_id IN (1,10) AND user_id=? AND internal_provider_id=? ORDER BY transaction_id DESC limit 1", $user_id, $this->get_provider_id()));
			}
		}

		if (intval($initial_transaction["transaction_id"]) == 0)
		{
			if ($subscription_id)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for subscription $subscription_id");
			} else
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Initial transaction is not found for user $username");
			}

			return false;
		}

		$user_id = intval($initial_transaction["user_id"]);

		sql_update("UPDATE {$this->tables_prefix}bill_transactions SET is_auto_expire=1 WHERE status_id=1 AND user_id=?", $user_id);
		$this->log_message(self::MESSAGE_TYPE_INFO, "Cancel of subscription $initial_transaction[external_purchase_id] for User_$user_id", $this->request_log);

		return true;
	}

	/**
	 * Logs message into bill log.
	 *
	 * @param int $message_type
	 * @param string $message_text
	 * @param string $message_details
	 */
	protected function log_message($message_type, $message_text, $message_details = "")
	{
		if (!in_array($message_type, array(self::MESSAGE_TYPE_DEBUG, self::MESSAGE_TYPE_ERROR, self::MESSAGE_TYPE_INFO)))
		{
			$message_type = self::MESSAGE_TYPE_ERROR;
		}

		$is_alert = 0;
		if ($message_type == self::MESSAGE_TYPE_ERROR)
		{
			$is_alert = 1;
			if (!$message_details)
			{
				$message_details = $this->request_log;
			}
			$this->error_logged = true;
		}

		$is_postback = 0;
		if ($this->is_postback)
		{
			$is_postback = 1;
		}

		sql_insert("INSERT INTO {$this->tables_prefix}bill_log SET internal_provider_id=?, message_type=?, message_text=?, message_details=?, is_alert=?, is_postback=?, added_date=?", $this->get_provider_id(), $message_type, trim($message_text), trim($message_details), $is_alert, $is_postback, date("Y-m-d H:i:s"));
	}

	/**
	 * Returns project path.
	 *
	 * @return string
	 */
	final protected function get_project_path()
	{
		return $this->project_path;
	}

	/**
	 * Returns project URL.
	 *
	 * @return string
	 */
	final protected function get_project_url()
	{
		return $this->project_url;
	}

	/**
	 * Returns provider info.
	 *
	 * @return array
	 */
	final protected function get_provider_data()
	{
		if (!$this->provider_data)
		{
			$this->provider_data = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_providers WHERE internal_id=?", $this->get_provider_id()));
		}

		return $this->provider_data;
	}

	/**
	 * Returns status for a user after premium access ended.
	 *
	 * @return int
	 */
	final protected function get_status_after_premium()
	{
		$memberzone_data = @unserialize(file_get_contents("{$this->project_path}/admin/data/system/memberzone_params.dat"));

		return intval($memberzone_data["STATUS_AFTER_PREMIUM"]);
	}
}

class KvsPaymentProcessorFactory
{
	private static $registry = array();

	/**
	 * Registers payment processor classname.
	 *
	 * @param string $internal_id
	 * @param string $classname
	 */
	public static function register_payment_processor($internal_id, $classname)
	{
		self::$registry[$internal_id] = $classname;
	}

	/**
	 * Creates payment processor instance by internal ID.
	 *
	 * @param string $internal_id
	 *
	 * @return KvsPaymentProcessor
	 * @throws ReflectionException
	 */
	public static function create_instance($internal_id)
	{
		$classname = self::$registry[$internal_id];
		if ($classname && class_exists($classname))
		{
			$class = new ReflectionClass($classname);
			if ($class->isSubclassOf('KvsPaymentProcessor'))
			{
				/** @noinspection PhpIncompatibleReturnTypeInspection */
				return $class->newInstance();
			}
		}

		return null;
	}
}