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

class KvsPaymentProcessorPaypal extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "paypal";
	}

	public function get_example_payment_url()
	{
		return "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XXXXXXXXXXXXX";
	}

	protected function get_logged_request_params()
	{
		return array("txn_id", "subscr_id", "custom", "payer_email", "payment_status", "address_country_code", "mc_gross", "mc_fee", "mc_currency", "txn_type", "parent_txn_id", "test_ipn", "payer_id", "period1", "period2", "period3", "mc_amount1", "mc_amount2", "mc_amount3", "recurring");
	}

	protected function get_shredded_request_params()
	{
		return array("address_country_code", "custom");
	}

	protected function shred_param_value($name, $value)
	{
		if ($name == "custom")
		{
			$value_shredded = explode(":::", trim($value), 4);

			return "$value_shredded[0]:::$value_shredded[1]:::$value_shredded[2]:::[deleted]";
		}

		return parent::shred_param_value($name, $value);
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		$params = array();

		$url = $access_package["payment_page_url"];

		$params["return"] = "$signup_page_url?action=payment_done";
		$params["no_note"] = "1";

		if ($_SESSION["user_id"] > 0)
		{
			$params["custom"] = "$access_package[external_id]:::$user_data[username]::::::$_SERVER[REMOTE_ADDR]";
		} else
		{
			$params["payer_email"] = $user_data["email"];
			$params["custom"] = "$access_package[external_id]:::$user_data[username]:::$user_data[pass]:::$_SERVER[REMOTE_ADDR]";
		}

		foreach ($params as $name => $value)
		{
			if (strpos($url, "?") === false)
			{
				$url .= "?$name=" . urlencode($value);
			} else
			{
				$url .= "&$name=" . urlencode($value);
			}
		}

		return $url;
	}

	protected function is_request_requires_basic_auth()
	{
		$provider_data = $this->get_provider_data();
		if ($provider_data["postback_username"] && $provider_data["postback_password"])
		{
			return true;
		}

		return false;
	}

	protected function process_request_impl()
	{
		$verify_message = "cmd=_notify-validate";
		foreach ($_REQUEST as $k => $v)
		{
			$verify_message .= "&$k=" . urlencode($v);
		}

		if (intval($_REQUEST["test_ipn"]) == 1)
		{
			$verification_result = get_page("", "https://ipnpb.sandbox.paypal.com/cgi-bin/webscr?$verify_message", "", "", 1, 0, 20, "");
		} else
		{
			$verification_result = get_page("", "https://ipnpb.paypal.com/cgi-bin/webscr?$verify_message", "", "", 1, 0, 20, "");
		}
		if ($verification_result != "VERIFIED")
		{
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Verification failed: $verification_result", "https://ipnpb.paypal.com/cgi-bin/webscr?$verify_message");
		}

		$transaction_id = trim($_REQUEST["txn_id"]);
		$transaction_guid = trim($_REQUEST["txn_id"]);
		$subscription_id = trim($_REQUEST["subscr_id"]);
		$price = floatval($_REQUEST["mc_gross"]) - floatval($_REQUEST["mc_fee"]);
		$currency_code = trim($_REQUEST["mc_currency"]);
		$access_package_id = "";
		$username = "";
		$password = "";
		$email = trim($_REQUEST["payer_email"]);
		$ip = "";
		$country_code = trim($_REQUEST["address_country_code"]);

		$custom = explode(":::", $_REQUEST["custom"], 4);
		if (count($custom) > 0)
		{
			$access_package_id = trim($custom[0]);
		}
		if (count($custom) > 1)
		{
			$username = trim($custom[1]);
		}
		if (count($custom) > 2)
		{
			$password = trim($custom[2]);
		}
		if (count($custom) > 3)
		{
			$ip = trim($custom[3]);
		}

		$related_transaction_id = trim($_REQUEST["parent_txn_id"]);
		$txn_type = strtolower(trim($_REQUEST["txn_type"]));

		if ($txn_type == "send_money")
		{
			$this->log_message(self::MESSAGE_TYPE_DEBUG, "Unsupported transaction type: send_money");
			return true;
		}

		switch (strtolower(trim($_REQUEST["payment_status"])))
		{
			case "completed":
				if ($txn_type == "web_accept") // simple purchase button
				{
					if ($this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code))
					{
						// if one-time purchase button was used for durable access package, configure it expire automatically
						sql_update("UPDATE {$this->tables_prefix}bill_transactions SET is_auto_expire=1 WHERE external_id=? AND internal_provider_id=? AND status_id=1", $transaction_id, $this->get_provider_id());

						return true;
					}

					return false;
				} elseif ($txn_type == "subscr_payment")
				{
					$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE external_purchase_id=? AND internal_provider_id=? AND type_id=1 LIMIT 1", $subscription_id, $this->get_provider_id()));
					if ($initial_transaction["transaction_id"] > 0) // rebill or conversion
					{
						return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id);
					} else // initial
					{
						return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code);
					}
				} else
				{
					$this->log_message(self::MESSAGE_TYPE_ERROR, "Unknown payment type: $txn_type");

					return true;
				}

			case "denied":
			case "failed":
				return $this->process_decline($transaction_id);

			case "refunded": // refund
				$price = floatval($_REQUEST["mc_gross"]);

				return $this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);

			case "reversed": // chargeback
				$price = floatval($_REQUEST["mc_gross"]);

				return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);

			case "voided": // void
				$price = floatval($_REQUEST["mc_gross"]);

				return $this->process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
		}

		if ($txn_type == "subscr_signup")
		{
			if ($_REQUEST["mc_amount1"] == "0.00")
			{
				// initial free trial
				return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, 0, $currency_code, $access_package_id, true, $username, $password, $email, $ip, $country_code);
			}
		}

		if ($txn_type == "subscr_eot")
		{
			return $this->process_expiration($subscription_id, $username);
		}

		return true;
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorPaypal();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("paypal", "KvsPaymentProcessorPaypal");
