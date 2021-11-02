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

class KvsPaymentProcessorVerotel extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "verotel";
	}

	public function get_example_payment_url()
	{
		return "https://secure.verotel.com/startorder?shopID=XXX";
	}

	protected function get_logged_request_params()
	{
		return array("signature", "event", "saleID", "custom1", "custom2", "custom3", "priceCurrency", "trialAmount", "priceAmount", "shopID", "amount", "currency", "subscriptionType");
	}

	protected function get_shredded_request_params()
	{
		return array("custom1");
	}

	protected function shred_param_value($name, $value)
	{
		if ($name == "custom1")
		{
			$value_shredded = explode(":::", trim($value), 3);
			return "$value_shredded[0]:::$value_shredded[1]:::[deleted]";
		}
		return parent::shred_param_value($name, $value);
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		global $runtime_params;

		$params = array();
		$params["version"] = "3";

		$payment_page_url = $access_package["payment_page_url"];
		if (is_array($runtime_params))
		{
			foreach ($runtime_params as $param)
			{
				$var = trim($param["name"]);
				$val = $_SESSION["runtime_params"][$var];
				if ($val == '')
				{
					$val = trim($param["default_value"]);
				}
				if ($var)
				{
					$payment_page_url = str_replace("%$var%", $val, $payment_page_url);
				}
			}
		}

		$url_params = parse_url($payment_page_url, PHP_URL_QUERY);
		if ($url_params)
		{
			$url_params = explode("&", $url_params);
			foreach ($url_params as $url_param)
			{
				$url_param = explode("=", $url_param);
				if ($url_param[1])
				{
					$params[$url_param[0]] = urldecode($url_param[1]);
				}
			}
		}

		if ($_SESSION["user_id"] > 0)
		{
			$params["custom1"] = $user_data["username"];
		} else
		{
			$params["custom1"] = $user_data["username"] . ":::" . $user_data["pass"] . ":::" . $_SERVER["REMOTE_ADDR"];
			$params["email"] = $user_data["email"];
		}
		$params["custom2"] = $access_package["external_id"];

		if ($access_package["tokens"] > 0 || $access_package["duration_initial"] == 0)
		{
			$params["type"] = "purchase";
			$params["description"] = $access_package["title"];
			$params["priceAmount"] = $access_package["price_initial"];
			$params["priceCurrency"] = $access_package["price_initial_currency"];
		} else
		{
			$params["type"] = "subscription";
			$params["name"] = $access_package["title"];
			if ($access_package["duration_rebill"] > 0)
			{
				$params["subscriptionType"] = "recurring";
				$params["priceAmount"] = $access_package["price_rebill"];
				$params["priceCurrency"] = $access_package["price_rebill_currency"];
				$params["period"] = "P$access_package[duration_rebill]D";
				$params["trialAmount"] = $access_package["price_initial"];
				$params["trialPeriod"] = "P$access_package[duration_initial]D";
			} else
			{
				$params["subscriptionType"] = "one-time";
				$params["priceAmount"] = $access_package["price_initial"];
				$params["priceCurrency"] = $access_package["price_initial_currency"];
				$params["period"] = "P$access_package[duration_initial]D";
			}
		}

		ksort($params, SORT_STRING);

		$provider_data = $this->get_provider_data();
		$signature_key = $provider_data["signature"];
		$signature = "$signature_key";
		foreach ($params as $name => $value)
		{
			if ($name != "email")
			{
				$signature .= ":$name=$value";
			}
		}
		$params["signature"] = sha1($signature);
		unset($params["shopID"], $params["referenceID"]);

		$url = $payment_page_url;
		foreach ($params as $name => $value)
		{
			$url .= "&$name=" . urlencode($value);
		}

		return $url;
	}

	protected function process_request_impl()
	{
		$provider_data = $this->get_provider_data();

		$signature_key = $provider_data["signature"];
		$signature = "$signature_key";
		foreach ($_REQUEST as $name => $value)
		{
			if ($name != "email" && $name != "signature")
			{
				$signature .= ":$name=$value";
			}
		}
		$signature = sha1($signature);

		if ($signature != $_REQUEST["signature"])
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Signature is not valid, the valid signature is $signature");
			return false;
		}

		$custom1 = explode(":::", trim($_REQUEST["custom1"]), 3);

		$transaction_id = trim($_REQUEST["saleID"]);
		$transaction_guid = trim($_REQUEST["signature"]);
		$subscription_id = trim($custom1[0]);
		$access_package_id = trim($_REQUEST["custom2"]);
		$currency_code = trim($_REQUEST["priceCurrency"]);
		if ($_REQUEST["trialAmount"])
		{
			$price = floatval($_REQUEST["trialAmount"]);
		} else
		{
			$price = floatval($_REQUEST["priceAmount"]);
		}

		$username = trim($custom1[0]);
		$password = trim($custom1[1]);

		if ($_REQUEST["event"]=="initial")
		{
			$is_trial = false;
			if (trim($_REQUEST["subscriptionType"]) == "recurring" && floatval($_REQUEST["trialAmount"]) > 0 && floatval($_REQUEST["trialAmount"]) != floatval($_REQUEST["priceAmount"]))
			{
				$is_trial = true;
			}

			$sale_status = $this->get_sale_status($transaction_id, trim($_REQUEST["shopID"]), $provider_data["signature"]);

			$email = trim($sale_status["email"]);
			$country_code = trim($sale_status["country"]);
			$ip = trim($custom1[2]);
			
			return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code);
		} elseif ($_REQUEST["event"] == "rebill")
		{
			$price = trim($_REQUEST["amount"]);
			$currency_code = trim($_REQUEST["currency"]);

			return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username);
		} elseif ($_REQUEST["event"] == "expiry")
		{
			return $this->process_expiration($subscription_id, $username);
		}

		return true;
	}

	private function get_sale_status($transaction_id, $shop_id, $signature_key)
	{
		$params = array();
		$params["version"] = "3";
		$params["saleID"] = $transaction_id;
		$params["shopID"] = $shop_id;

		ksort($params, SORT_STRING);
		$signature = "$signature_key";
		foreach ($params as $name => $value)
		{
			if ($name != "email")
			{
				$signature .= ":$name=$value";
			}
		}
		$params["signature"] = sha1($signature);

		$url = "https://secure.verotel.com/status/order?";
		foreach ($params as $name => $value)
		{
			$url .= "$name=$value&";
		}
		$url = trim($url, "&");

		$result = array();
		$result["email"] = "";
		$result["country"] = "";

		$status_response = get_page("", $url, "", "", 1, 0, 10, "");
		if ($status_response)
		{
			$status_response = explode("\n", $status_response);
			foreach ($status_response as $line)
			{
				$line = trim($line);
				if (strpos($line, "email:") === 0)
				{
					$result["email"] = trim(substr($line, 6));
				} elseif (strpos($line, "country:") === 0)
				{
					$result["country"] = trim(substr($line, 8));
				}
			}
		}

		return $result;
	}
}

if ($is_postback_request)
{
	if (strpos($_SERVER["REQUEST_URI"], $_SERVER["SCRIPT_NAME"]) !== false)
	{
		header("HTTP/1.0 403 Forbidden");
		die;
	}

	$processor = new KvsPaymentProcessorVerotel();
	if ($processor->process_request())
	{
		echo "OK";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("verotel", "KvsPaymentProcessorVerotel");
