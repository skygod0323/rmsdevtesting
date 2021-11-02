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

$statuses = array(
		array(
				'id' => "",
				'title' => $lang['common']['insight_status_all'],
		),
		array(
				'id' => "1",
				'title' => $lang['common']['insight_status_active'],
		),
		array(
				'id' => "0",
				'title' => $lang['common']['insight_status_disabled'],
		)
);

$selected_status = $_SESSION['save']['insight_users.php']['status'];
if (isset($_REQUEST['status']))
{
	$selected_status = $_REQUEST['status'];
	$_SESSION['save']['insight_users.php']['status'] = $selected_status;
}

$where_status = "$config[tables_prefix_multi]users.status_id not in (1)";
if ($selected_status == '0')
{
	$where_status = "$config[tables_prefix_multi]users.status_id=0";
} elseif ($selected_status == '1')
{
	$where_status = "$config[tables_prefix_multi]users.status_id not in (0, 1)";
}

if (isset($_REQUEST['full_list']))
{
	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', mr2array(sql("select user_id as id, username as title, status_id from $config[tables_prefix]users where $where_status order by username asc")));

	header("Content-Type: text/html; charset=utf-8");

	$smarty->assign('statuses', $statuses);
	$smarty->assign('selected_status', $selected_status);
	$smarty->display("insight_list.tpl");
} else
{
	$result_for = $_REQUEST['for'];
	$result_for = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $result_for);
	if (strlen($_REQUEST['for']) > 1)
	{
		echo "<insight for=\"$result_for\">\n";
		$q = sql_escape($_REQUEST['for']);
		$data = mr2array(sql("select user_id, username, status_id from $config[tables_prefix]users where $where_status and username like '%$q%' order by username asc"));
		foreach ($data as $user)
		{
			$id = $user['user_id'];
			$name = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $user['username']);
			$disabled = '';
			if ($user['status_id'] == 0)
			{
				$disabled = 'disabled="1"';
			}
			echo "<value id=\"$id\" $disabled>$name</value>\n";
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
