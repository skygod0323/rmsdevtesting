<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'] <> '')
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}

require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');

$rnd = intval($_SERVER['argv'][1]);
if ($rnd < 1 || !is_file("$config[temporary_path]/emailing-$rnd.txt"))
{
	die;
}

$_POST = unserialize(file_get_contents("$config[temporary_path]/emailing-$rnd.txt"));
settype($_POST['delay'], "integer");
settype($_POST['user_status_ids'], "array");

$status_ids = implode(',', array_map("intval", $_POST['user_status_ids']));
if (strlen($status_ids) == 0)
{
	die;
}

if ($_POST['send_to'] == '3')
{
	$user_from_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user_from']));

	$users = mr2array(sql("select user_id, display_name from $config[tables_prefix]users where status_id in ($status_ids)"));

	foreach ($users as $user)
	{
		$body = $_POST['body'];

		$body = str_replace("%USER_DISPLAY_NAME%", $user['display_name'], $body);

		sql_pr("insert into $config[tables_prefix]messages set message=?, user_id=?, user_from_id=?, is_hidden_from_user_from_id=1, added_date=?", $body, $user['user_id'], $user_from_id, date("Y-m-d H:i:s"));

		if ($_POST['delay'] > 0)
		{
			sleep($_POST['delay']);
		}
	}
} else
{
	if ($_POST['send_to'] == '1')
	{
		$users[0]['email'] = $_POST['test_email'];
		$users[0]['display_name'] = "Display Name Here";
	} else
	{
		$users = mr2array(sql("select email, display_name from $config[tables_prefix]users where status_id in ($status_ids)"));
	}

	foreach ($users as $user)
	{
		$subject = $_POST['subject'];
		$body = $_POST['body'];

		$subject = str_replace("%USER_DISPLAY_NAME%", $user['display_name'], $subject);
		$body = str_replace("%USER_DISPLAY_NAME%", $user['display_name'], $body);

		send_mail($user['email'], $subject, $body, $_POST['headers']);

		if ($_POST['delay'] > 0)
		{
			sleep($_POST['delay']);
		}
	}
}

unlink("$config[temporary_path]/emailing-$rnd.txt");
