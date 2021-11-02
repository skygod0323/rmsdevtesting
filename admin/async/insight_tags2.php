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
				'value' => "tag asc"
		),
		array(
				'id' => "most_used",
				'title' => $lang['common']['insight_sort_by_most_used'],
				'value' => "(total_videos + total_albums + total_posts) desc"
		),
		array(
				'id' => "least_used",
				'title' => $lang['common']['insight_sort_by_least_used'],
				'value' => "(total_videos + total_albums + total_posts) asc"
		)
);
$sort_by = $_SESSION['save']['insight_tags.php']['sort_by'];
if (isset($_REQUEST['sort_by']))
{
	$sort_by = $_REQUEST['sort_by'];
	$_SESSION['save']['insight_tags.php']['sort_by'] = $sort_by;
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

$selected_status = $_SESSION['save']['insight_tags.php']['status'];
if (isset($_REQUEST['status']))
{
	$selected_status = $_REQUEST['status'];
	$_SESSION['save']['insight_tags.php']['status'] = $selected_status;
}

$where_status = '1=1';
if ($selected_status == '0')
{
	$where_status = "$config[tables_prefix]tags.status_id=0";
} elseif ($selected_status == '1')
{
	$where_status = "$config[tables_prefix]tags.status_id=1";
}

if (isset($_REQUEST['full_list']))
{
	header("Content-Type: text/html; charset=utf-8");

	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', mr2array(sql("select tag_id as id, tag as title, status_id from $config[tables_prefix]tags where $where_status order by $applied_sorting[value]")));
	$smarty->assign('sortings', $sortings);
	$smarty->assign('selected_sorting', $applied_sorting['id']);
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
		$data = mr2array(sql("select tag_id, tag, synonyms, status_id from $config[tables_prefix]tags where (tag like '%$q%' or synonyms like '%$q%') and $where_status order by $applied_sorting[value]"));
		foreach ($data as $item)
		{
			$id = $item['tag_id'];
			$tag = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $item['tag']);
			$disabled = '';
			if ($item['status_id'] == 0)
			{
				$disabled = 'disabled="1"';
			}

			$synonyms = $item['synonyms'];
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

				echo "<value id=\"$id\" synonyms=\"$synonyms\" $disabled>$tag</value>\n";
			} else
			{
				echo "<value id=\"$id\" $disabled>$tag</value>\n";
			}
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
