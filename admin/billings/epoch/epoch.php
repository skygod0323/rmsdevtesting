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

class KvsPaymentProcessorEpoch extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "epoch";
	}

	public function get_example_payment_url()
	{
		return "https://wnu.com/secure/services/?api=join";
	}

	protected function get_logged_request_params()
	{
		return array("pi_code", "transaction_id", "username", "password", "email", "order_id", "ipaddress", "country", "amount", "ets_transaction_id", "ets_member_idx", "ets_transaction_type", "ets_transaction_amount", "mcs_or_idx");
	}

	protected function get_shredded_request_params()
	{
		return array("ipaddress", "country");
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
		if (strpos($url, "pi_code=$access_package[external_id]") === false)
		{
			$url .= "pi_code=$access_package[external_id]&";
		}

		return "{$url}username=" . urlencode($user_data["username"]) . "&password=" . urlencode($user_data["pass"]) . "&email=" . urlencode($user_data["email"]);
	}

	protected function is_request_allowed()
	{
		$provider_data = $this->get_provider_data();

		if ($provider_data["postback_ip_protection"])
		{
			$ip_masks = explode("|", $provider_data["postback_ip_protection"]);
			$ip_allowed = false;
			foreach ($ip_masks as $mask)
			{
				$mask = trim($mask);
				if ($mask && strpos($_SERVER["REMOTE_ADDR"], $mask) === 0)
				{
					$ip_allowed = true;
					break;
				}
			}
			if (!$ip_allowed)
			{
				$this->log_message(self::MESSAGE_TYPE_ERROR, "IP $_SERVER[REMOTE_ADDR] is not allowed");

				return false;
			}
		}

		return true;
	}


	protected function process_request_impl()
	{
		$transaction_id = trim($_REQUEST["transaction_id"]);
		$transaction_guid = trim($_REQUEST["transaction_id"]);
		$subscription_id = trim($_REQUEST["order_id"]);
		$price = floatval($_REQUEST["amount"]);
		$currency_code = "USD";
		$access_package_id = trim($_REQUEST["pi_code"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["email"]);
		$ip = trim($_REQUEST["ipaddress"]);
		$country_code = trim($_REQUEST["country"]);

		if (intval($_REQUEST["ets_transaction_id"]) > 0)
		{
			$transaction_id = trim($_REQUEST["ets_transaction_id"]);
			$transaction_guid = trim($_REQUEST["ets_transaction_id"]);
			$subscription_id = trim($_REQUEST["ets_member_idx"]);
			$price = floatval($_REQUEST["ets_transaction_amount"]);
			$currency_code = "USD";

			$related_transaction_id = "";

			$trantype = trim($_REQUEST["ets_transaction_type"]);
			if ($trantype == "C")
			{
				return $this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			} elseif ($trantype == "D")
			{
				return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			} elseif ($trantype == "N" || $trantype == "U")
			{
				return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username, $password, $email, $ip, $country_code);
			} elseif ($trantype == "X" || $trantype == "A")
			{
				return $this->process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			} else
			{
				$this->log_message(self::MESSAGE_TYPE_DEBUG, "Skipped transaction type $trantype");

				return true;
			}
		} elseif (intval($_REQUEST["mcs_or_idx"]) > 0)
		{
			$subscription_id = trim($_REQUEST["mcs_or_idx"]);

			return $this->process_cancellation($subscription_id, $username);
		}

		return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code);
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorEpoch();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("epoch", "KvsPaymentProcessorEpoch");
