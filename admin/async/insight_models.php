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
		'value' => "$config[tables_prefix]models.title asc"
	),
	array(
		'id' => "most_used",
		'title' => $lang['common']['insight_sort_by_most_used'],
		'value' => "($config[tables_prefix]models.total_videos + $config[tables_prefix]models.total_albums + $config[tables_prefix]models.total_posts) desc"
	),
	array(
		'id' => "least_used",
		'title' => $lang['common']['insight_sort_by_least_used'],
		'value' => "($config[tables_prefix]models.total_videos + $config[tables_prefix]models.total_albums + $config[tables_prefix]models.total_posts) asc"
	)
);
$sort_by = $_SESSION['save']['insight_models.php']['sort_by'];
if (isset($_REQUEST['sort_by']))
{
	$sort_by = $_REQUEST['sort_by'];
	$_SESSION['save']['insight_models.php']['sort_by'] = $sort_by;
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

$selected_status = $_SESSION['save']['insight_models.php']['status'];
if (isset($_REQUEST['status']))
{
	$selected_status = $_REQUEST['status'];
	$_SESSION['save']['insight_models.php']['status'] = $selected_status;
}

$where_status = '1=1';
if ($selected_status == '0')
{
	$where_status = "$config[tables_prefix]models.status_id=0";
} elseif ($selected_status == '1')
{
	$where_status = "$config[tables_prefix]models.status_id=1";
}

if (isset($_REQUEST['full_list']))
{
	$group_by = $_SESSION['save']['insight_models.php']['group_by'];
	if (isset($_REQUEST['group_by']))
	{
		$group_by = $_REQUEST['group_by'];
		$_SESSION['save']['insight_models.php']['group_by'] = $group_by;
	}

	if ($group_by == 'group')
	{
		$list_models = array();
		$temp = mr2array(sql("select model_id as id, $config[tables_prefix]models.model_group_id, $config[tables_prefix]models.title as title, $config[tables_prefix]models_groups.title as group_title, $config[tables_prefix]models.status_id from $config[tables_prefix]models left join $config[tables_prefix]models_groups on $config[tables_prefix]models_groups.model_group_id=$config[tables_prefix]models.model_group_id where $where_status order by $config[tables_prefix]models_groups.title asc, $applied_sorting[value]"));
		foreach ($temp as $res)
		{
			$list_models[$res['model_group_id']][] = $res;
		}
	} else
	{
		$list_models = mr2array(sql("select model_id as id, title, status_id from $config[tables_prefix]models where $where_status order by $applied_sorting[value]"));
	}

	header("Content-Type: text/html; charset=utf-8");

	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', $list_models);
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

	$data = mr2array(sql("select model_id, title from $config[tables_prefix]models where $where"));
	foreach ($data as $model)
	{
		$items_mapped[mb_lowercase($model['title'])] = $model['model_id'];
		$items_titles[$model['model_id']] = $model['title'];
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
		$data = mr2array(sql("select model_id, title, alias, status_id from $config[tables_prefix]models where (title like '%$q%' or alias like '%$q%') and $where_status order by $applied_sorting[value]"));
		foreach ($data as $model)
		{
			$id = $model['model_id'];
			$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $model['title']);
			$disabled = '';
			if ($model['status_id'] == 0)
			{
				$disabled = 'disabled="1"';
			}

			$synonyms = $model['alias'];
			$matched_synonyms = array_map('trim', explode(',', $synonyms));
			foreach ($matched_synonyms as $key => $synonym)
			{
				if (!mb_contains($synonym, $_REQUEST['for']))
				{
					unset($matched_synonyms[$key]);
				}
			}
			$synonyms = implode(', ', $matched_synonyms);
			if ($synonyms != '')
			{
				$synonyms = str_replace(array("&", ">", "<", "\""), array("&amp;", "&gt;", "&lt;", "&quot;"), $synonyms);

				echo "<value id=\"$id\" synonyms=\"$synonyms\" $disabled>$title</value>\n";
			} else
			{
				echo "<value id=\"$id\" $disabled>$title</value>\n";
			}
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
