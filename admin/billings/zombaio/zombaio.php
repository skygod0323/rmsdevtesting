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

class KvsPaymentProcessorZombaio extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "zombaio";
	}

	public function get_example_payment_url()
	{
		return "https://secure.zombaio.com/get_proxy.asp?SiteID=XXXXXXXXX";
	}

	protected function get_logged_request_params()
	{
		return array("ZombaioGWPass", "Action", "Success", "username", "password", "EMAIL", "COUNTRY", "PRICING_ID", "TRANSACTION_ID", "SUBSCRIPTION_ID", "VISITOR_IP", "Amount", "Amount_Currency");
	}

	protected function get_shredded_request_params()
	{
		return array("COUNTRY", "VISITOR_IP");
	}

	protected function is_request_allowed()
	{
		$provider_data = $this->get_provider_data();
		if ($provider_data["postback_password"] && $provider_data["postback_password"] != $_REQUEST["ZombaioGWPass"])
		{
			return false;
		}

		return true;
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
		if (strpos($url, "PricingID=$access_package[external_id]") === false)
		{
			$url .= "PricingID=$access_package[external_id]&";
		}

		return "{$url}Username=" . urlencode($user_data["username"]) . "&Password=" . urlencode($user_data["pass"]) . "&Email=" . urlencode($user_data["email"]) .
			"&return_url_approve=" . urlencode("$signup_page_url?action=payment_done") . "&return_url_decline=" . urlencode("$signup_page_url?action=payment_failed");
	}

	protected function process_request_impl()
	{
		$transaction_id = trim($_REQUEST["TRANSACTION_ID"]);
		$transaction_guid = trim($_REQUEST["TRANSACTION_ID"]);
		$subscription_id = trim($_REQUEST["SUBSCRIPTION_ID"]);
		$price = trim($_REQUEST["Amount"]);
		$currency_code = trim($_REQUEST["Amount_Currency"]);
		$access_package_id = trim($_REQUEST["PRICING_ID"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["EMAIL"]);
		$ip = trim($_REQUEST["VISITOR_IP"]);
		$country_code = trim($_REQUEST["COUNTRY"]);

		$related_transaction_id = "";

		if ($_REQUEST["Action"] == "user.add")
		{
			return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code);
		} elseif ($_REQUEST["Action"] == "user.delete")
		{
			return $this->process_expiration($subscription_id, $username);
		} elseif ($_REQUEST["Action"] == "declined")
		{
			return $this->process_decline($transaction_id);
		} elseif ($_REQUEST["Action"] == "rebill" && $_REQUEST["Success"] == "1")
		{
			return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username, $password, $email, $ip, $country_code);
		} elseif ($_REQUEST["Action"] == "chargeback")
		{
			return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
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

	$processor = new KvsPaymentProcessorZombaio();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("zombaio", "KvsPaymentProcessorZombaio");
