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

class KvsPaymentProcessorSegpay extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "segpay";
	}

	public function get_example_payment_url()
	{
		return "https://secure2.segpay.com/billing/poset.cgi";
	}

	public function get_example_oneclick_url()
	{
		return "https://secure2.segpay.com/billing/OneClick.aspx";
	}

	protected function get_logged_request_params()
	{
		return array("action", "stage", "approved", "trantype", "purchaseid", "eticketid", "tranid", "transguid", "relatedtranid", "price", "currencycode", "username", "password", "email", "country", "ip", "ip_address", "initialvalue", "initialperiod", "recurringvalue", "recurringperiod", "merchantpartnerid", "standin", "xsellnum", "billertranstime");
	}

	protected function get_shredded_request_params()
	{
		return array("country", "ip_address", "ip");
	}

	public function get_payment_page_url($access_package, $signup_page_url, $user_data)
	{
		if ($_SESSION["user_id"] > 0)
		{
			if ($access_package["oneclick_page_url"])
			{
				$initial_transaction = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}bill_transactions WHERE user_id=? AND internal_provider_id=? AND type_id IN (1,10) ORDER BY transaction_id DESC LIMIT 1", $_SESSION["user_id"], $this->get_provider_id()));
				if ($initial_transaction["external_purchase_id"])
				{
					$url = $access_package["oneclick_page_url"];
					if (strpos($url, "?") === false)
					{
						$url .= "?";
					} else
					{
						$url .= "&";
					}
					if (strpos($url, "x-eticketid=$access_package[external_id]") === false)
					{
						$url .= "x-eticketid=$access_package[external_id]&";
					}

					return "{$url}OCToken=" . urlencode($initial_transaction["external_purchase_id"]) . "&username=" . urlencode($_SESSION["username"]) .
					"&x-auth-link=" . urlencode("$signup_page_url?action=payment_done") . "&x-auth-text=" . urlencode("Click here to return back to website.") .
					"&x-decl-link=" . urlencode("$signup_page_url?action=payment_failed") . "&x-decl-text=" . urlencode("Click here for using another payment method.");
				}
			}
			$user_data["pass"] = "";
		}

		$url = $access_package["payment_page_url"];
		if (strpos($url, "?") === false)
		{
			$url .= "?";
		} else
		{
			$url .= "&";
		}
		if (strpos($url, "x-eticketid=$access_package[external_id]") === false)
		{
			$url .= "x-eticketid=$access_package[external_id]&";
		}

		return "{$url}username=" . urlencode($user_data["username"]) . "&password=" . urlencode($user_data["pass"]) . "&x-billemail=" . urlencode($user_data["email"]) .
		"&x-auth-link=" . urlencode("$signup_page_url?action=payment_done") . "&x-auth-text=" . urlencode("Click here to return back to website.") .
		"&x-decl-link=" . urlencode("$signup_page_url?action=payment_failed") . "&x-decl-text=" . urlencode("Click here for using another payment method.");
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
		$trantype = strtolower(trim($_REQUEST["trantype"]));
		$action = strtolower(trim($_REQUEST["action"]));
		$stage = strtolower(trim($_REQUEST["stage"]));
		$approved = strtolower(trim($_REQUEST["approved"]));

		$transaction_id = trim($_REQUEST["tranid"]);
		$transaction_guid = trim($_REQUEST["transguid"]);
		$subscription_id = trim($_REQUEST["purchaseid"]);
		$price = floatval($_REQUEST["price"]);
		$currency_code = trim($_REQUEST["currencycode"]);
		$access_package_id = trim($_REQUEST["eticketid"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["email"]);
		$ip = nvl(trim($_REQUEST["ip_address"]), trim($_REQUEST["ip"]));
		$country_code = trim($_REQUEST["country"]);

		$related_transaction_id = trim($_REQUEST["relatedtranid"]);

		if ($_REQUEST["action"] && $_REQUEST["stage"] && $_REQUEST["trantype"])
		{
			switch ($trantype)
			{
				case "sale":
					if ($action == "auth")
					{
						if ($approved == "yes")
						{
							switch ($stage)
							{
								case "initial":
									$is_trial = false;
									if (floatval($_REQUEST["initialvalue"]) > 0 && floatval($_REQUEST["recurringvalue"]) > 0 && floatval($_REQUEST["initialvalue"]) != floatval($_REQUEST["recurringvalue"]))
									{
										$is_trial = true;
									}

									return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code);
								case "conversion":
								case "instantconversion":
								case "rebill":
									return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username, $password, $email, $ip, $country_code);
							}
						} elseif ($approved == "no" && $stage == "initial")
						{
							return $this->process_decline($transaction_id);
						}
					} elseif ($action == "void")
					{
						return $this->process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
					}
					break;
				case "credit":

					return $this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
				case "charge":

					return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			}
		}

		if ($action == "disable")
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

	$processor = new KvsPaymentProcessorSegpay();
	if ($processor->process_request())
	{
		echo "done";
	} else
	{
		echo "error";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("segpay", "KvsPaymentProcessorSegpay");