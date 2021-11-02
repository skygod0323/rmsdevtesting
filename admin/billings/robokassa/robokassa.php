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

class KvsPaymentProcessorRobokassa extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "robokassa";
	}

	public function get_example_payment_url()
	{
		return "https://merchant.roboxchange.com/Index.aspx?MrchLogin=XXXXXX";
	}

	protected function requires_auto_expire()
	{
		return true;
	}

	protected function get_logged_request_params()
	{
		return array("InvId", "OutSum", "Shp_email", "Shp_login", "Shp_password", "Shp_xpackage", "SignatureValue");
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		$provider_data = $this->get_provider_data();

		$signature_params = array();

		$url_params = parse_url($access_package["payment_page_url"], PHP_URL_QUERY);
		if ($url_params)
		{
			$url_params = explode("&", $url_params);
			foreach ($url_params as $url_param)
			{
				$url_param = explode("=", $url_param);
				if ($url_param[0] == "MrchLogin")
				{
					$signature_params[] = urldecode($url_param[1]);
				}
			}
		}

		$params = array();
		$params["InvId"] = "0";
		$params["InvDesc"] = $access_package["title"];
		$params["OutSum"] = $access_package["price_initial"];
		$params["OutSumCurrency"] = str_replace("RUB", "RUR", $access_package["price_initial_currency"]);
		$params["Email"] = $user_data["email"];
		//$params["IsTest"] = "1";

		$signature_params[] = $params["OutSum"];
		$signature_params[] = $params["InvId"];
		$signature_params[] = $params["OutSumCurrency"];

		$other = array();
		if ($_SESSION["user_id"] > 0)
		{
			$other["Shp_login"] = $user_data["username"];
		} else
		{
			$other["Shp_email"] = $user_data["email"];
			$other["Shp_login"] = $user_data["username"];
			$other["Shp_password"] = $user_data["pass"];
		}
		$other["Shp_xpackage"] = $access_package["external_id"];

		$signature_params[] = $provider_data["signature"];

		foreach ($other as $other_param_name => $other_param_value)
		{
			$signature_params[] = "$other_param_name=" . urlencode($other_param_value);
		}

		$url = $access_package["payment_page_url"];

		$params["SignatureValue"] = md5(implode(":", $signature_params));
		foreach ($params as $param_name => $param_value)
		{
			$url .= "&$param_name=" . urlencode($param_value);
		}
		foreach ($other as $param_name => $param_value)
		{
			$url .= "&$param_name=" . urlencode(urlencode($param_value));
		}

		return $url;
	}

	protected function process_request_impl()
	{
		$provider_data = $this->get_provider_data();

		$transaction_id = trim($_REQUEST["InvId"]);
		$transaction_guid = trim($_REQUEST["InvId"]);
		$subscription_id = trim($_REQUEST["InvId"]);
		$price = floatval($_REQUEST["OutSum"]);
		$fee = floatval($_REQUEST["Fee"]);
		$currency_code = "RUB";
		$access_package_id = trim($_REQUEST["Shp_xpackage"]);
		$username = trim($_REQUEST["Shp_login"]);
		$password = trim($_REQUEST["Shp_password"]);
		$email = trim($_REQUEST["Shp_email"]);
		$ip = "";
		$country_code = "";

		$check_signature = "$price:$transaction_id:$provider_data[signature]2";
		if ($email != "")
		{
			$check_signature .= ":Shp_email=$email";
		}
		if ($username != "")
		{
			$check_signature .= ":Shp_login=$username";
		}
		if ($password != "")
		{
			$check_signature .= ":Shp_password=$password";
		}
		if ($access_package_id != "")
		{
			$check_signature .= ":Shp_xpackage=$access_package_id";
		}
		$check_signature = md5($check_signature);

		if ($check_signature != $_REQUEST["SignatureValue"])
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Signature is not valid, the valid signature is $check_signature");

			return false;
		}

		return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price - $fee, $currency_code, $access_package_id, false, $username, $password, $email, $ip, $country_code);
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorRobokassa();
	if ($processor->process_request())
	{
		echo "OK" . trim($_REQUEST["InvId"]);
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("robokassa", "KvsPaymentProcessorRobokassa");
