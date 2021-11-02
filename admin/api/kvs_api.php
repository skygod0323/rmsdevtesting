<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if (strpos($_SERVER['REQUEST_URI'], 'admin/api/kvs_api.php') !== false)
{
	header("HTTP/1.0 403 Forbidden");
	die;
}

require_once '../include/setup.php';
require_once '../include/functions.php';
require_once '../include/functions_base.php';

$api_config = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/api.dat"));
if (intval($api_config['API_ENABLE']) == 0)
{
	header("HTTP/1.0 403 Forbidden");
	echo "API is disabled";
	die;
}

if (is_array($_REQUEST) && count($_REQUEST) > 0)
{
	foreach ($_REQUEST as $k => $v)
	{
		$log .= "$k: $_REQUEST[$k]; ";
	}
	settype($log, "string");
	api_log_message("INFO  Request from $_SERVER[REMOTE_ADDR]: $log");
}

if ($_POST['action'] == '')
{
	echo "KVS API v$config[project_version]";
	die;
}

if ($_POST['action'] == 'check_username')
{
	api_check_username();
	die;
} elseif ($_POST['action'] == 'create_user')
{
	api_create_user();
	die;
} elseif ($_POST['action'] == 'create_transaction')
{
	api_create_transaction();
	die;
} elseif ($_POST['action'] == 'cancel_transaction')
{
	api_cancel_transaction();
	die;
} elseif ($_POST['action'] == 'add_tokens')
{
	api_add_tokens();
	die;
} elseif ($_POST['action'] == 'delete_user')
{
	api_delete_user();
	die;
}

echo "[ERROR] action is not supported";
die;

function api_create_user()
{
	global $config, $api_config, $regexp_check_alpha_numeric;

	// required
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	$email = trim($_POST['email']);
	$sign = trim($_POST['sign']);

	// optional
	$country_code = trim($_POST['country_code']);
	$city = trim($_POST['city']);
	$gender_id = intval($_POST['gender_id']);
	$custom1 = trim($_POST['custom1']);
	$custom2 = trim($_POST['custom2']);
	$custom3 = trim($_POST['custom3']);
	$custom4 = trim($_POST['custom4']);
	$custom5 = trim($_POST['custom5']);
	$custom6 = trim($_POST['custom6']);
	$custom7 = trim($_POST['custom7']);
	$custom8 = trim($_POST['custom8']);
	$custom9 = trim($_POST['custom9']);
	$custom10 = trim($_POST['custom10']);
	$tokens = intval($_POST['tokens']);

	// signature validation
	$check = md5("$username::$password::$email::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data validation
	if (!$username)
	{
		echo "[ERROR] username is required";
		die;
	}
	if (strlen($username) < 3)
	{
		echo "[ERROR] username should be more than 2 symbols length";
		die;
	}
	if (!preg_match($regexp_check_alpha_numeric, $username))
	{
		echo "[ERROR] username should only contain alphanumeric symbols";
		die;
	}
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $username)) > 0)
	{
		echo "[ERROR] such username already exists";
		die;
	}
	if (!$password)
	{
		echo "[ERROR] password is required";
		die;
	}
	if (strlen($password) < 5)
	{
		echo "[ERROR] password should be more than 4 symbols length";
		die;
	}
	if (!$email)
	{
		echo "[ERROR] email is required";
		die;
	}

	// data processing
	$country_id = 0;
	if ($country_code)
	{
		$country_id = mr2number(sql_pr("select country_id from $config[tables_prefix]list_countries where country_code=? and is_system=0 limit 1", $country_code));
	}

	$user_id = sql_insert("insert into $config[tables_prefix]users set country_id=?, status_id=2, tokens_available=?, username=?, pass=?, email=?, display_name=?, gender_id=?, city=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=?, custom6=?, custom7=?, custom8=?, custom9=?, custom10=?, added_date=?",
		$country_id, $tokens, $username, generate_password_hash($password), $email, $username, $gender_id, $city, $custom1, $custom2, $custom3, $custom4, $custom5, $custom6, $custom7, $custom8, $custom9, $custom10, date("Y-m-d H:i:s")
	);

	if ($tokens > 0)
	{
		sql_insert("insert into $config[tables_prefix]bill_transactions set user_id=?, status_id=2, bill_type_id=4, type_id=10, access_start_date=?, access_end_date=?, tokens_granted=?", $user_id, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $tokens);
	}

	echo "[SUCCESS] the user was successfully created";
	die;
}

function api_create_transaction()
{
	global $config, $api_config;

	// required
	$username = trim($_POST['username']);
	$duration_days = intval($_POST['duration']);
	$sign = trim($_POST['sign']);

	// signature validation
	$check = md5("$username::$duration_days::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data validation
	if (!$username)
	{
		echo "[ERROR] username is required";
		die;
	}
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? and status_id!=4", $username));
	if ($user_id == 0)
	{
		echo "[ERROR] user with such username doesn't exist";
		die;
	}

	// data processing
	$access_start_date = date("Y-m-d H:i:s");
	$type_id = 1;
	if ($duration_days == 0)
	{
		$access_end_date = "2070-01-01 00:00:00";
		$is_unlimited = 1;
	} else
	{
		$existing_access_hours = max(0, floor(mr2number(sql_pr("select unix_timestamp(access_end_date)-unix_timestamp(?) from $config[tables_prefix]bill_transactions where user_id=? and status_id=1", date("Y-m-d H:i:s"), $user_id)) / 3600));
		$access_end_date = date("Y-m-d H:i:s", mktime(date("H") + $existing_access_hours, date("i"), date("s"), date("m"), date("d") + $duration_days, date("Y")));
		$is_unlimited = 0;
	}
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_transactions where user_id=? and status_id=1", $user_id)) > 0)
	{
		$type_id = 3;
	}
	sql_update("update $config[tables_prefix]bill_transactions set status_id=2, access_end_date=?, is_unlimited_access=0 where status_id=1 and user_id=?", date("Y-m-d H:i:s"), $user_id);
	sql_insert("insert into $config[tables_prefix]bill_transactions set user_id=?, status_id=1, type_id=$type_id, bill_type_id=4, access_start_date=?, access_end_date=?, is_unlimited_access=?", $user_id, $access_start_date, $access_end_date, intval($is_unlimited));
	sql_update("update $config[tables_prefix]users set status_id=3 where user_id=?", $user_id);

	echo "[SUCCESS] the transaction was successfully created";
	die;
}

function api_cancel_transaction()
{
	global $config, $api_config;

	// required
	$username = trim($_POST['username']);
	$sign = trim($_POST['sign']);

	// signature validation
	$check = md5("$username::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data validation
	if (!$username)
	{
		echo "[ERROR] username is required";
		die;
	}
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
	if ($user_id == 0)
	{
		echo "[ERROR] user with such username doesn't exist";
		die;
	}

	// data processing
	$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	sql_update("update $config[tables_prefix]bill_transactions set status_id=3, access_end_date=?, is_unlimited_access=0 where status_id=1 and user_id=?", date("Y-m-d H:i:s"), $user_id);
	sql_update("update $config[tables_prefix]users set status_id=? where user_id=? and status_id=3", intval($memberzone_data['STATUS_AFTER_PREMIUM']), $user_id);

	echo "[SUCCESS] the transaction was successfully cancelled";
	die;
}

function api_check_username()
{
	global $config, $api_config;

	// required
	$username = trim($_POST['username']);
	$sign = trim($_POST['sign']);

	// signature validation
	$check = md5("$username::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data processing
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
	if ($user_id == 0)
	{
		echo "[FREE] username doesn't exist";
		die;
	} else
	{
		echo "[EXISTS] username exists";
		die;
	}
}

function api_add_tokens()
{
	global $config, $api_config;

	// required
	$username = trim($_POST['username']);
	$tokens = intval($_POST['tokens']);
	$sign = trim($_POST['sign']);

	// signature validation
	$check = md5("$username::$tokens::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data validation
	if (!$username)
	{
		echo "[ERROR] username is required";
		die;
	}
	if ($tokens == 0)
	{
		echo "[ERROR] tokens is required";
		die;
	}
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
	if ($user_id == 0)
	{
		echo "[ERROR] user with such username doesn't exist";
		die;
	}

	// data processing
	sql_update("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $tokens, $user_id);
	sql_insert("insert into $config[tables_prefix]bill_transactions set user_id=?, status_id=2, bill_type_id=4, type_id=10, access_start_date=?, access_end_date=?, tokens_granted=?", $user_id, date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $tokens);

	echo "[SUCCESS] $tokens tokens were assigned to user $user_id";
	die;
}

function api_delete_user()
{
	global $config, $api_config;

	// required
	$username = trim($_POST['username']);
	$sign = trim($_POST['sign']);

	// signature validation
	$check = md5("$username::$api_config[API_PASSWORD]");
	if ($check != $sign)
	{
		echo "[ERROR] signature is not valid";
		die;
	}

	// data validation
	if (!$username)
	{
		echo "[ERROR] username is required";
		die;
	}
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $username));
	if ($user_id == 0)
	{
		echo "[ERROR] user with such username doesn't exist";
		die;
	}

	// data processing
	sql_update("update $config[tables_prefix]users set status_id=0 where user_id=?", $user_id);

	echo "[SUCCESS] the user was successfully deleted";
	die;
}

function api_log_message($message, $time = 0)
{
	global $config;

	$fp = fopen("$config[project_path]/admin/logs/api.txt", "a+");
	flock($fp, LOCK_EX);
	if ($time > 0)
	{
		fwrite($fp, "[$time]: $message\n\n");
	} else
	{
		fwrite($fp, date("[Y-m-d H:i:s]: ") . "$message\n\n");
	}
	fclose($fp);
}