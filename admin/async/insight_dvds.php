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

$sortings = array(
	array(
		'id' => "alphabet",
		'title' => $lang['common']['insight_sort_by_alphabet'],
		'value' => "$config[tables_prefix]dvds.title asc"
	),
	array(
		'id' => "most_used",
		'title' => $lang['common']['insight_sort_by_most_used'],
		'value' => "($config[tables_prefix]dvds.total_videos) desc"
	),
	array(
		'id' => "least_used",
		'title' => $lang['common']['insight_sort_by_least_used'],
		'value' => "($config[tables_prefix]dvds.total_videos) asc"
	)
);
$sort_by = $_SESSION['save']['insight_dvds.php']['sort_by'];
if (isset($_REQUEST['sort_by']))
{
	$sort_by = $_REQUEST['sort_by'];
	$_SESSION['save']['insight_dvds.php']['sort_by'] = $sort_by;
}
$applied_sorting = null;
foreach ($sortings as $sorting)
{
	if ($sorting['id'] == $sort_by)
	{
		$applied_sorting = $sorting;
		break;
	}
}
if (!$applied_sorting)
{
	$applied_sorting = $sortings[0];
}

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

$selected_status = $_SESSION['save']['insight_dvds.php']['status'];
if (isset($_REQUEST['status']))
{
	$selected_status = $_REQUEST['status'];
	$_SESSION['save']['insight_dvds.php']['status'] = $selected_status;
}

$where_status = '1=1';
if ($selected_status == '0')
{
	$where_status = "$config[tables_prefix]dvds.status_id=0";
} elseif ($selected_status == '1')
{
	$where_status = "$config[tables_prefix]dvds.status_id=1";
}

if (isset($_REQUEST['full_list']))
{
	$group_by = $_SESSION['save']['insight_dvds.php']['group_by'];
	if (isset($_REQUEST['group_by']))
	{
		$group_by = $_REQUEST['group_by'];
		$_SESSION['save']['insight_dvds.php']['group_by'] = $group_by;
	}

	if ($group_by == 'group')
	{
		$list_dvds = array();
		$temp = mr2array(sql("select dvd_id as id, $config[tables_prefix]dvds.dvd_group_id, $config[tables_prefix]dvds.title as title, $config[tables_prefix]dvds_groups.title as group_title, $config[tables_prefix]dvds.status_id from $config[tables_prefix]dvds left join $config[tables_prefix]dvds_groups on $config[tables_prefix]dvds_groups.dvd_group_id=$config[tables_prefix]dvds.dvd_group_id where $where_status order by $config[tables_prefix]dvds_groups.title asc, $applied_sorting[value]"));
		foreach ($temp as $res)
		{
			if ($config['dvds_mode'] == 'series' && $res['group_title'] == '')
			{
				$res['group_title'] = $lang['videos']['dvd_field_group_none'];
			}
			$list_dvds[$res['dvd_group_id']][] = $res;
		}
	} else
	{
		$list_dvds = mr2array(sql("select dvd_id as id, title, status_id from $config[tables_prefix]dvds where $where_status order by $applied_sorting[value]"));
	}

	header("Content-Type: text/html; charset=utf-8");

	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', $list_dvds);
	if ($group_by == 'group')
	{
		$smarty->assign('is_grouped', 1);
	}
	$smarty->assign('is_grouping_supported', 1);
	$smarty->assign('sortings', $sortings);
	$smarty->assign('selected_sorting', $applied_sorting['id']);
	$smarty->assign('statuses', $statuses);
	$smarty->assign('selected_status', $selected_status);
	$smarty->display("insight_list.tpl");
} elseif (isset($_REQUEST['formulti']))
{
	$result_for = $_REQUEST['formulti'];
	$result_for = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $result_for);
	unset($where);
	$items = explode(',', $_REQUEST['formulti']);
	$items_mapped = array();
	$items_titles = array();
	foreach ($items as $item)
	{
		$item = trim($item);
		if ($item <> '')
		{
			$q = sql_escape($item);
			$where .= " or title='$q'";
		}
	}
	if ($where == '')
	{
		die;
	}
	echo "<insight for=\"$result_for\">\n";
	$where = substr($where, 4);

	$data = mr2array(sql("select dvd_id, title from $config[tables_prefix]dvds where $where"));
	foreach ($data as $dvd)
	{
		$items_mapped[mb_lowercase($dvd['title'])] = $dvd['dvd_id'];
		$items_titles[$dvd['dvd_id']] = $dvd['title'];
	}
	foreach ($items as $item)
	{
		$item = trim($item);
		if ($item != '')
		{
			if (isset($items_mapped[mb_lowercase($item)]))
			{
				$id = $items_mapped[mb_lowercase($item)];
				$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $items_titles[$id]);
				echo "<value id=\"$id\">$title</value>\n";
			} else
			{
				$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $item);
				$id = "new_$title";
				echo "<value id=\"$id\">$title</value>\n";
			}
		}
	}
	echo "</insight>";
} else
{
	$result_for = $_REQUEST['for'];
	$result_for = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $result_for);
	if (strlen($_REQUEST['for']) > 1)
	{
		echo "<insight for=\"$result_for\">\n";
		$q = sql_escape($_REQUEST['for']);
		$data = mr2array(sql("select dvd_id, title, status_id from $config[tables_prefix]dvds where title like '%$q%' and $where_status order by $applied_sorting[value]"));
		foreach ($data as $dvd)
		{
			$id = $dvd['dvd_id'];
			$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $dvd['title']);
			$disabled = '';
			if ($dvd['status_id'] == 0)
			{
				$disabled = 'disabled="1"';
			}
			echo "<value id=\"$id\" $disabled>$title</value>\n";
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
