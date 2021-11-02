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

$selected_status = $_SESSION['save']['insight_admins.php']['status'];
if (isset($_REQUEST['status']))
{
	$selected_status = $_REQUEST['status'];
	$_SESSION['save']['insight_admins.php']['status'] = $selected_status;
}

$where_status = "($config[tables_prefix_multi]admin_users.is_superadmin!=2)";
if ($selected_status == '0')
{
	$where_status = "($config[tables_prefix_multi]admin_users.is_superadmin=0 and $config[tables_prefix_multi]admin_users.status_id=0)";
} elseif ($selected_status == '1')
{
	$where_status = "($config[tables_prefix_multi]admin_users.is_superadmin=1 or $config[tables_prefix_multi]admin_users.status_id=1)";
}

if (isset($_REQUEST['full_list']))
{
	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	if (in_array('system|administration', $_SESSION['permissions']))
	{
		$smarty->assign('data', mr2array(sql("select user_id as id, login as title, case when is_superadmin>0 then 1 else status_id end as status_id from $config[tables_prefix_multi]admin_users where $where_status order by login asc")));
	} else
	{
		$smarty->assign('data', mr2array(sql_pr("select user_id as id, login as title, case when is_superadmin>0 then 1 else status_id end as status_id from $config[tables_prefix_multi]admin_users where login=?", $_SESSION['userdata']['login'])));
	}

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
		if (in_array('system|administration', $_SESSION['permissions']))
		{
			$data = mr2array(sql("select user_id, login, case when is_superadmin>0 then 1 else status_id end as status_id from $config[tables_prefix_multi]admin_users where login like '%$q%' and $where_status order by login asc"));
		} else
		{
			$data = mr2array(sql_pr("select user_id, login, case when is_superadmin>0 then 1 else status_id end as status_id from $config[tables_prefix_multi]admin_users where login like '%$q%' and login=?", $_SESSION['userdata']['login']));
		}
		foreach ($data as $user)
		{
			$id = $user['user_id'];
			$name = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $user['login']);
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
