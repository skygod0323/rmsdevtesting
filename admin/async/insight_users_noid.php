<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once '../include/setup.php';
require_once '../include/setup_smarty.php';
require_once '../include/functions_base.php';
require_once '../include/functions.php';
require_once '../include/check_access.php';

header("Content-Type: text/xml; charset=utf-8");

if (isset($_REQUEST['full_list']))
{
	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', mr2array(sql("select username as id, username as title from $config[tables_prefix]users where status_id not in (1) order by username asc")));

	header("Content-Type: text/html; charset=utf-8");

	$smarty->display("insight_list.tpl");
} else
{
	$result_for = $_REQUEST['for'];
	$result_for = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $result_for);
	if (strlen($_REQUEST['for']) > 1)
	{
		echo "<insight for=\"$result_for\">\n";
		$q = sql_escape($_REQUEST['for']);
		$data = mr2array(sql("select user_id, username, status_id from $config[tables_prefix]users where status_id not in (1) and username like '%$q%' order by username asc"));
		foreach ($data as $user)
		{
			$id = $user['user_id'];
			$name = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $user['username']);
			$disabled = '';
			if ($user['status_id'] == 0)
			{
				$disabled = 'disabled="1"';
			}
			echo "<value $disabled>$name</value>\n";
		}
		foreach ($data as $user)
		{
			$user = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $user);
			echo "<value>$user</value>\n";
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
