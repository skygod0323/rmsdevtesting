<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

/** @noinspection PhpUnusedParameterInspection, ReturnTypeCanBeDeclaredInspection, AccessModifierPresentedInspection */

$is_postback_request = false;
if (!isset($config) || !is_array($config))
{
	$is_postback_request = true;
	require_once "../../include/setup.php";
}

require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";

class KvsPaymentProcessorCCbill extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "ccbill";
	}

	public function get_example_payment_url()
	{
		return "https://bill.ccbill.com/jpost/signup.cgi?clientAccnum=XXXXXX&clientSubacc=YYYY";
	}

	protected function get_logged_request_params()
	{
		return array("reasonForDeclineCode", "subscription_id", "clientAccnum", "username", "password", "email", "id", "typeId", "ip_address", "country", "initialPeriod", "recurringPeriod", "accountingAmount");
	}

	protected function get_shredded_request_params()
	{
		return array("ip_address", "country");
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		$url = $access_package["payment_page_url"];
		if (strpos($url, "?") === false)
		{
			$url .= "?";
		} else
		{
			$url .= "&";
		}

		return "{$url}username="
			. urlencode($user_data["username"])
			. "&password="
			. urlencode($user_data["pass"])
			. "&email="
			. urlencode($user_data["email"])
			. "&country_id="
			. urlencode($_POST["country_id"])
			. "&custom1="
			. urlencode($_POST["custom1"])
			. "&confirm_password=0";
	}

	protected function process_request_impl()
	{
		$transaction_id = trim($_REQUEST["id"]);
		$transaction_guid = trim($_REQUEST["id"]);
		$subscription_id = trim($_REQUEST["subscription_id"]);
		$price = floatval($_REQUEST["accountingAmount"]);
		$currency_code = "USD";
		$access_package_id = trim($_REQUEST["typeId"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["email"]);
		$ip = $_REQUEST["ip_address"];
		$country_code = trim($_REQUEST["country"]);
		$duration_initial = intval($_REQUEST["initialPeriod"]);
		$duration_rebill = intval($_REQUEST["recurringPeriod"]);
		$country_id = trim($_REQUEST["X-country_id"]);
		$custom1 = trim($_REQUEST["X-custom1"]);

		$this->logRequest($_REQUEST);

		if (trim($_REQUEST["reasonForDeclineCode"]) !== "" && $subscription_id == "")
		{
			return $this->process_decline($transaction_id);
		}

		return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code, $duration_initial, $duration_rebill, compact('custom1','country_id'));
	}

	function logRequest(array $req) {
		$json = json_encode($req);
		$file = "{$this->project_path}/admin/data/billings/ccbill.log";
		file_put_contents($file, $json . "\n", FILE_APPEND);
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
	 * @param array $additional additional fields
	 *
	 * @return bool
	 */
	protected function process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code, $duration_initial = 0, $duration_rebill = 0, array $additional = [])
	{
		if (!parent::process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code, $duration_initial = 0, $duration_rebill = 0)) {
			return false;
		}

		$update_array = array();

		if (count($additional) > 0) {
			foreach ($additional as $field => $value) {
				$update_array[$field] = process_blocked_words(trim($value), true);
			}

			if (sql_pr("update {$this->tables_prefix}users set ?% where username=?", $update_array, $username) === false) {
				return false;
			}
		}

		return true;
	}

	protected function process_schedule_impl()
	{
		$provider_data = $this->get_provider_data();
		if ($provider_data["account_id"] == "" || $provider_data["sub_account_id"] == "")
		{
			return;
		}

		if ($provider_data["datalink_last_check_date"] == "0000-00-00 00:00:00")
		{
			sql_update("UPDATE {$this->tables_prefix}card_bill_providers SET datalink_last_exec_date=?, datalink_last_check_date=? WHERE internal_id=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $this->get_provider_id());
			$this->log_message(self::MESSAGE_TYPE_INFO, "Datalink first time check executed");

			return;
		}

		if ($provider_data["datalink_last_exec_date"] == "0000-00-00 00:00:00" || (strtotime($provider_data["datalink_last_exec_date"]) + 120 * 60 < time() && in_array(floor((time() % 86400) / 3600), array(1, 5, 9, 13, 17, 21))))
		{
			$old_timezone = date_default_timezone_get();
			date_default_timezone_set("America/Phoenix");

			$date_check_to = time();
			$date_check_from = strtotime($provider_data["datalink_last_check_date"]) + 1;
			if ($date_check_to > $date_check_from + 86400 - 1)
			{
				$date_check_to = $date_check_from + 86400 - 1;
			}

			$date_check_from_str = date("YmdHis", $date_check_from);
			$date_check_to_str = date("YmdHis", $date_check_to);

			$date_check_from_str2 = date("Y-m-d H:i:s", $date_check_from);
			$date_check_to_str2 = date("Y-m-d H:i:s", $date_check_to);

			$expire_date = date("Y-m-d", $date_check_from);
			$expire_users = false;
			if (date("Ymd", $date_check_from) <> date("Ymd", $date_check_to))
			{
				$expire_users = true;
			}

			date_default_timezone_set($old_timezone);

			$clientAcc = $provider_data["account_id"];
			$clientSubAcc = $provider_data["sub_account_id"];
			$username = $provider_data["datalink_username"];
			$password = $provider_data["datalink_password"];

			$request = "https://datalink.ccbill.com/data/main.cgi?clientAccnum=$clientAcc&clientSubacc=$clientSubAcc&transactionTypes=REBILL,REFUND,VOID,EXPIRE,CHARGEBACK&startTime=$date_check_from_str&endTime=$date_check_to_str&username=$username&password=$password";
			$response = get_page("", $request, "", "", 1, 0, 50, "", array("use_ip" => $provider_data["datalink_use_ip"], "return_error" => 1));
			if (strpos($response, "Error:") === 0)
			{
				sql_update("UPDATE {$this->tables_prefix}card_bill_providers SET datalink_last_exec_date=? WHERE internal_id=?", date("Y-m-d H:i:s"), $this->get_provider_id());
				$response = trim(str_replace("Error:", "", $response));
				$this->log_message(self::MESSAGE_TYPE_ERROR, "Datalink error: $response", $request);

				return;
			}

			$rows = explode("\n", $response);
			foreach ($rows as $row)
			{
				$row = trim($row);
				$columns = explode("\",\"", $row);
				if (count($columns) == 1)
				{
					continue;
				}

				$transaction_id = "";
				$transaction_guid = "";
				$currency = "USD";

				$subscription_id = trim($columns[3], " \"");
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE external_purchase_id=? AND internal_provider_id=? LIMIT 1", $subscription_id, $this->get_provider_id()));
				if (intval($initial_transaction["user_id"]) == 0)
				{
					$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE external_id=? AND internal_provider_id=? LIMIT 1", $subscription_id, $this->get_provider_id()));
				}

				$this->request_log = $row;

				$tran_type = strtolower(trim($columns[0], " \""));
				switch ($tran_type)
				{
					case "rebill":
						$transaction_id = trim($columns[5], " \"");
						$transaction_guid = trim($columns[5], " \"");
						$price = floatval(trim($columns[6], " \""));

						if (mr2number(sql_pr("SELECT COUNT(*) FROM {$this->tables_prefix}bill_transactions WHERE external_id=? AND internal_provider_id=?", $transaction_id, $this->get_provider_id())) == 0)
						{
							$this->process_rebill_or_conversion($transaction_id, $transaction_guid, $initial_transaction["external_purchase_id"], $price, $currency, $initial_transaction["external_package_id"]);
						}
						break;
					case "refund":
						$price = -floatval(trim($columns[5], " \""));
						$this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency);
						break;
					case "void":
						$price = -floatval(trim($columns[5], " \""));
						$this->process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency);
						break;
					case "expire":
						if ($expire_users && $expire_date == $columns[4])
						{
							$this->process_expiration($subscription_id);
						}
						break;
					case "chargeback":
						$price = -floatval(trim($columns[5], " \""));
						$this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency);
						break;
				}
			}

			$this->request_log = "";

			sql_update("UPDATE {$this->tables_prefix}card_bill_providers SET datalink_last_exec_date=?, datalink_last_check_date=? WHERE internal_id=?", date("Y-m-d H:i:s"), $date_check_to_str2, $this->get_provider_id());
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Executed datalink check for period from $date_check_from_str2 to $date_check_to_str2", $response);
		}
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorCCbill();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("ccbill", "KvsPaymentProcessorCCbill");
