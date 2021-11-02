<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'include/setup.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'langs/english.php';

$errors = null;

$username = trim($_POST['username']);
$password = trim($_POST['password']);

validate_field('empty', $username, $lang['login']['field_username']);
if ($password == "d41d8cd98f00b204e9800998ecf8427e")
{
	$errors[] = get_aa_error('required_field', $lang['login']['field_password']);
}

if (!is_array($errors))
{
	$ip_tries = mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]log_logins where (UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(login_date))<=180 and is_failed=1 and ip=?", date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR'])));
	if ($ip_tries > 3)
	{
		$errors[] = get_aa_error('login_error_limit2');
		return_ajax_errors($errors);
		die;
	}

	$config['sql_safe_mode'] = 1;
	$result = sql_pr("select * from $config[tables_prefix_multi]admin_users where login=? and (pass=? or pass=? or pass=?)", $username, generate_password_hash($password), md5("pass:$password"), $password);
	unset($config['sql_safe_mode']);

	if (mr2rows($result) > 0)
	{
		$admin_data = mr2array_single($result);
		if (($admin_data['is_superadmin'] == 2 || $admin_data['login'] == 'kvs_support') && (mr2string(sql("select value from $config[tables_prefix]options where variable='ENABLE_KVS_SUPPORT_ACCESS'")) <> '1' || (strpos($_SERVER['REMOTE_ADDR'], '88.85.69.2') === false && $_SERVER['SERVER_ADDR'] <> $_SERVER['REMOTE_ADDR'])))
		{
			$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
		} else
		{
			if ($admin_data['is_superadmin'] == 0 && $admin_data['status_id'] == 0)
			{
				sql_pr("insert into $config[tables_prefix_multi]log_logins set is_failed=1, session_id='', user_id='0', login_date=?, last_request_date=?, duration=0, ip=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']));
				$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
			} else
			{
				session_start();
				$_SESSION['userdata'] = $admin_data;
				$_SESSION['userdata']['ip'] = $_SERVER['REMOTE_ADDR'];
				$_SESSION['userdata']['session_id'] = md5(mt_rand(0, 999999999));
				$_SESSION['userdata']['last_login'] = @mr2array_single(sql_pr("select login_date,ip,duration from $config[tables_prefix_multi]log_logins where user_id=? order by login_date desc limit 1", $_SESSION['userdata']['user_id']));
				$_SESSION['userdata']['pass'] = md5($_SESSION['userdata']['pass']);
				$_SESSION['userdata']['login_gate'] = $config['project_url'];
				if ($_SESSION['userdata']['last_login']['ip'] <> '')
				{
					$_SESSION['userdata']['last_login']['ip'] = int2ip($_SESSION['userdata']['last_login']['ip']);
				}

				$_SESSION['save'] = unserialize($_SESSION['userdata']['preference']);
				unset($_SESSION['userdata']['preference']);

				sql_pr("insert into $config[tables_prefix_multi]log_logins set session_id=?, user_id=?, login_date=?, last_request_date=?, duration=0, ip=?", $_SESSION['userdata']['session_id'], $_SESSION['userdata']['user_id'], date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']));
				sql_pr("update $config[tables_prefix_multi]admin_users set last_ip=? where user_id=?", ip2int($_SERVER['REMOTE_ADDR']), $_SESSION['userdata']['user_id']);

				if ($_COOKIE['kt_redirect_to'] <> '')
				{
					return_ajax_success(str_replace("&", "&amp;", $_COOKIE['kt_redirect_to']), 1);
				} elseif (strpos($_SESSION['referer'], '/admin/') !== false)
				{
					return_ajax_success("$_SESSION[referer]");
				} else
				{
					return_ajax_success("start.php");
				}
			}
		}
	} else
	{
		if (sql_error_code() > 0)
		{
			$errors[] = get_aa_error('login_error_sql', sql_error_code(), sql_error_message());
		} else
		{
			sql_pr("insert into $config[tables_prefix_multi]log_logins set is_failed=1, session_id='', user_id='0', login_date=?, last_request_date=?, duration=0, ip=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']));
			$errors[] = get_aa_error('login_error_limit', 3 - $ip_tries);
		}
	}
}

if (is_array($errors))
{
	return_ajax_errors($errors);
}
