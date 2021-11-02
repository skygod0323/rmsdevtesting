<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'langs/english.php';

if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0)
{
	$domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	header("Location: //{$domain}$_SERVER[REQUEST_URI]");
	die;
}

require_once "include/functions_base.php";

sql("select count(*) from $config[tables_prefix]options");

if (mr2number(sql("select count(*) from $config[tables_prefix]options")) == 0)
{
	$smarty = new mysmarty();
	$smarty->assign("config", $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('session_error', $lang['login']['error_database2']);
	$smarty->display("login.tpl");
	die;
}
$session_error = '';

$old_error_handler = set_error_handler('error_handler');
$session_error_track = true;
session_start();
$_SESSION['test'] = 1;
session_write_close();
$session_error_track = false;
set_error_handler($old_error_handler);

session_start();

if ($_SESSION['userdata']['login'] != '' && ($_SESSION['userdata']['is_ip_protection_disabled'] == 1 || $_SESSION['userdata']['ip'] == $_SERVER['REMOTE_ADDR']) && !isset($_REQUEST['force_relogin']))
{
	header("Location: start.php");
} else
{
	$smarty = new mysmarty();
	$smarty->assign("config", $config);
	$smarty->assign('lang', $lang);
	$smarty->assign('session_error', $session_error);
	$smarty->assign('ip_address', $_SERVER['REMOTE_ADDR']);
	if (strpos($_SERVER['REMOTE_ADDR'], '88.85.69.2') !== false || $_SERVER['SERVER_ADDR'] == $_SERVER['REMOTE_ADDR'])
	{
		$smarty->assign('show_version', 1);
	}
	$smarty->display("login.tpl");
}

function error_handler($errno, $errstr, $errfile, $errline)
{
	global $session_error, $session_error_track;

	if ($session_error_track)
	{
		$session_error = $errstr;
	}
}