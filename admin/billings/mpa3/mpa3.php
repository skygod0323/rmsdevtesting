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

class KvsPaymentProcessorMpa3 extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "mpa3";
	}

	protected function requires_auto_expire()
	{
		return true;
	}

	protected function get_logged_request_params()
	{
		return array("conversion", "rebill", "credit", "chargeback", "transaction_id", "subscription_id", "product_id", "username", "password", "email", "trial", "free_trial", "expiration_date", "subscription_date", "trn_date");
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		return $access_package["payment_page_url"];
	}

	protected function process_request_impl()
	{
		$transaction_id = trim($_REQUEST["transaction_id"]);
		$transaction_guid = trim($_REQUEST["transaction_id"]);
		$subscription_id = trim($_REQUEST["subscription_id"]);
		$price = 0;
		$currency_code = "";
		$access_package_id = trim($_REQUEST["product_id"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["email"]);
		$ip = "";
		$country_code = "";
		$is_trial = intval($_REQUEST["trial"]);
		if (intval($_REQUEST["free_trial"]) == 1)
		{
			$is_trial = 1;
		}

		$related_transaction_id = "";

		if (intval($_REQUEST["conversion"]) == 1 || intval($_REQUEST["rebill"]) == 1)
		{
			return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username, $password, $email, $ip, $country_code);
		} elseif (intval($_REQUEST["credit"]) == 1)
		{
			return $this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
		} elseif (intval($_REQUEST["chargeback"]) == 1)
		{
			return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
		}

		return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code);
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorMpa3();
	if ($processor->process_request())
	{
		echo "";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("mpa3", "KvsPaymentProcessorMpa3");
