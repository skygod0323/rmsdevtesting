<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once("admin/include/setup.php");

session_start();

if ($_SESSION['user_id'] > 0)
{
	$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
	if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH'] == 1)
	{
		require_once("admin/include/functions_base.php");
		sql_pr("update $config[tables_prefix]users set last_online_date=? where user_id=?", date("Y-m-d H:i:s"), $_SESSION['user_id']);
	}
}

$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
setcookie("kt_member", '', time() - 86400, "/", ".$domain");

foreach ($_SESSION as $key => $value)
{
	if ($key != 'userdata' && $key != 'save' && $key != 'runtime_params' && $key != 'lock_ips')
	{
		unset($_SESSION[$key]);
	}
}

if (isset($_REQUEST["redirect_to"]))
{
	header("Location: $config[project_url]$_REQUEST[redirect_to]");
} else
{
	header("Location: $config[project_url]");
}
