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

class KvsPaymentProcessorNats extends KvsPaymentProcessor
{
	public function get_provider_id()
	{
		return "nats";
	}

	protected function get_logged_request_params()
	{
		return array("post_type", "statid", "memberid", "member_subscription_id", "optionid", "username", "password", "email", "country", "ip", "trial", "stamp", "expires");
	}

	protected function get_shredded_request_params()
	{
		return array("country", "ip");
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
		if (strpos($url, "signup[optionid]=$access_package[external_id]") === false)
		{
			$url .= "signup[optionid]=$access_package[external_id]&";
		}

		return "{$url}signup[username]=" . urlencode($user_data["username"]) . "&signup[password]=" . urlencode($user_data["pass"]) . "&signup[email]=" . urlencode($user_data["email"]);
	}

	protected function process_request_impl()
	{
		$transaction_id = trim($_REQUEST["statid"]);
		$transaction_guid = trim($_REQUEST["memberid"]);
		$subscription_id = trim($_REQUEST["member_subscription_id"]);
		$price = 0;
		$currency_code = "";
		$access_package_id = trim($_REQUEST["optionid"]);
		$username = trim($_REQUEST["username"]);
		$password = trim($_REQUEST["password"]);
		$email = trim($_REQUEST["email"]);
		$ip = int2ip(intval($_REQUEST["ip"]));
		$country_code = trim($_REQUEST["country"]);
		$is_trial = intval($_REQUEST["trial"]);
		$duration_initial = 0;
		$duration_rebill = 0;

		$related_transaction_id = "";

		$provider_data = $this->get_provider_data();
		if ($access_package_id)
		{
			$package = mr2array_single(sql_pr("SELECT * FROM {$this->tables_prefix}card_bill_packages WHERE provider_id=? AND external_id=? LIMIT 1", $provider_data["provider_id"], $access_package_id));
			if (intval($package["package_id"]) == 0)
			{
				// for NATS we not require access packages to exist
				$access_package_id = 0;
				if (intval($_REQUEST["expires"]) > 0)
				{
					$duration_initial = (intval($_REQUEST["expires"]) - intval($_REQUEST["stamp"])) / 86400;
					if ($is_trial == 1)
					{
						$duration_rebill = 30;
					} else
					{
						$duration_rebill = $duration_initial;
					}
				} else
				{
					if ($is_trial == 1)
					{
						$duration_initial = 30;
						$duration_rebill = 30;
					}
				}
			}
		}

		switch (trim($_REQUEST["post_type"]))
		{
			case "":
				if ($this->process_username_check($username))
				{
					echo "*exists*";
				} else
				{
					echo "*available*";
				}
				die;
			case "approvalpost":
				return $this->process_purchase($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $is_trial, $username, $password, $email, $ip, $country_code, $duration_initial, $duration_rebill);
			case "upgradepost":
				return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username, $password, $email, $ip, $country_code, $duration_initial);
			case "rebillpost":
				return $this->process_rebill_or_conversion($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $access_package_id, $username, $password, $email, $ip, $country_code, $duration_initial);
			case "voidpost":
				return $this->process_void($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			case "creditpost":
				return $this->process_refund($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			case "chargebackpost":
				return $this->process_chargeback($transaction_id, $transaction_guid, $subscription_id, $price, $currency_code, $related_transaction_id, $username);
			case "expirepost":
				return $this->process_expiration($subscription_id, $username);
			case "denypost":
			case "denialpost":
				return $this->process_decline($transaction_id);
		}

		return true;
	}

	public function process_password_change($user_id, $new_password)
	{
		$provider_data = $this->get_provider_data();
		if (!$provider_data['datalink_url'] || !$provider_data['datalink_username'] || !$provider_data['datalink_password'])
		{
			return true;
		}

		$nats_memberid = mr2string(sql_pr("SELECT external_guid FROM {$this->tables_prefix}bill_transactions WHERE user_id=? AND internal_provider_id=? AND external_guid!='' ORDER BY access_start_date ASC LIMIT 1", $user_id, $this->get_provider_id()));
		if (!$nats_memberid)
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Failed to notify NATS about member password change, no memberid found for User_$user_id");
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "KVS");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $provider_data['datalink_password'], 'api-username: ' . $provider_data['datalink_username']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('memberid' => $nats_memberid, 'password' => $new_password)));
		curl_setopt($ch, CURLOPT_URL, rtrim($provider_data['datalink_url'], '/') . '/api/member/details');
		if ($provider_data['datalink_use_ip'])
		{
			curl_setopt($ch, CURLOPT_INTERFACE, $provider_data['datalink_use_ip']);
		}

		$result = curl_exec($ch);
		$this->log_message(self::MESSAGE_TYPE_INFO, "Notified NATS about User_$user_id changed password", $result);

		return true;
	}

	public function process_email_change($user_id, $new_email)
	{
		$provider_data = $this->get_provider_data();
		if (!$provider_data['datalink_url'] || !$provider_data['datalink_username'] || !$provider_data['datalink_password'])
		{
			return true;
		}

		$nats_memberid = mr2string(sql_pr("SELECT external_guid FROM {$this->tables_prefix}bill_transactions WHERE user_id=? AND internal_provider_id=? AND external_guid!='' ORDER BY access_start_date ASC LIMIT 1", $user_id, $this->get_provider_id()));
		if (!$nats_memberid)
		{
			$this->log_message(self::MESSAGE_TYPE_ERROR, "Failed to notify NATS about member email change, no memberid found for User_$user_id");
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "KVS");
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('api-key: ' . $provider_data['datalink_password'], 'api-username: ' . $provider_data['datalink_username']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('memberid' => $nats_memberid, 'email' => $new_email)));
		curl_setopt($ch, CURLOPT_URL, rtrim($provider_data['datalink_url'], '/') . '/api/member/details');
		if ($provider_data['datalink_use_ip'])
		{
			curl_setopt($ch, CURLOPT_INTERFACE, $provider_data['datalink_use_ip']);
		}

		$result = curl_exec($ch);
		$this->log_message(self::MESSAGE_TYPE_INFO, "Notified NATS about User_$user_id changed email", $result);

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

	$processor = new KvsPaymentProcessorNats();
	if ($processor->process_request())
	{
		echo "";
	} else
	{
		echo "ERROR";
	}
}

KvsPaymentProcessorFactory::register_payment_processor("nats", "KvsPaymentProcessorNats");
