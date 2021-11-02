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

class KvsPaymentProcessorCCbillDynamicPricing extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "ccbilldyn";
	}

	public function get_example_payment_url()
	{
		return "https://bill.ccbill.com/jpost/signup.cgi?clientAccnum=XXXXXX&clientSubacc=YYYY&formName=ZZZZZ";
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

		$provider_data = $this->get_provider_data();
		$signature_key = $provider_data["signature"];

		$initial_price = $access_package["price_initial"];
		$initial_price_currency = "000";
		switch ($access_package["price_initial_currency"])
		{
			case "USD":
				$initial_price_currency = "840";
				break;
			case "EUR":
				$initial_price_currency = "978";
				break;
			case "GBP":
				$initial_price_currency = "826";
				break;
			case "AUD":
				$initial_price_currency = "036";
				break;
			case "CAD":
				$initial_price_currency = "124";
				break;
		}
		$initial_period = $access_package["duration_initial"];

		$recurring_price = $access_package["price_rebill"];
		$recurring_period = $access_package["duration_rebill"];

		if ($recurring_period > 0)
		{
			$url .= "formPrice=$initial_price&formPeriod=$initial_period&currencyCode=$initial_price_currency&formRecurringPrice=$recurring_price&formRecurringPeriod=$recurring_period&formRebills=99&";
			$url .= "formDigest=" . md5("{$initial_price}{$initial_period}{$recurring_price}{$recurring_period}99{$initial_price_currency}{$signature_key}") . "&";
		} else
		{
			$url .= "formPrice=$initial_price&formPeriod=$initial_period&currencyCode=$initial_price_currency&";
			$url .= "formDigest=" . md5("{$initial_price}{$initial_period}{$initial_price_currency}{$signature_key}") . "&";
		}

		return "{$url}username=" . urlencode($user_data["username"]) . "&password=" . urlencode($user_data["pass"]) . "&email=" . urlencode($user_data["email"]) . "&confirm_password=0";
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

		if (trim($_REQUEST["reasonForDeclineCode"]) !== "" && $subscription_id == "")
		{
			return $this->process_decline($transaction_id);
		}

		return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code, $duration_initial, $duration_rebill);
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

		if ($provider_data["datalink_last_exec_date"] == "0000-00-00 00:00:00" || (strtotime($provider_data["datalink_last_exec_date"]) + 120 * 60 < time() && in_array(floor((time() % 86400) / 3600), array(3, 7, 11, 15, 19, 23))))
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

	$processor = new KvsPaymentProcessorCCbillDynamicPricing();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("ccbilldyn", "KvsPaymentProcessorCCbillDynamicPricing");
