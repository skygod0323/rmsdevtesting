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
	$group_by = $_SESSION['save']['insight_countries.php']['group_by'];
	if (isset($_REQUEST['group_by']))
	{
		$group_by = $_REQUEST['group_by'];
		$_SESSION['save']['insight_countries.php']['group_by'] = $group_by;
	}

	if ($group_by == 'group')
	{
		$list_countries = array();
		$temp = mr2array(sql_pr("select country_code as id, $config[tables_prefix]list_countries.continent_code, $config[tables_prefix]list_countries.title as title, $config[tables_prefix]list_countries.continent_code as group_title from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by $config[tables_prefix]list_countries.continent_code asc, title asc", $lang['system']['language_code']));
		foreach ($temp as $res)
		{
			$res['group_title'] = $lang['continents'][$res['group_title']];
			$list_countries[$res['continent_code']][] = $res;
		}
	} else
	{
		$list_countries = mr2array(sql_pr("select country_code as id, title from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
	}

	header("Content-Type: text/html; charset=utf-8");

	$smarty = new mysmarty();
	$smarty->assign('lang', $lang);
	$smarty->assign('data', $list_countries);
	if ($group_by == 'group')
	{
		$smarty->assign('is_grouped', 1);
	}
	$smarty->assign('is_grouping_supported', 1);
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

	$data = mr2array(sql_pr("select country_code, title from $config[tables_prefix]list_countries where language_code=? and is_system=0 and $where", $lang['system']['language_code']));
	foreach ($data as $country)
	{
		$items_mapped[mb_lowercase($country['title'])] = $country['country_code'];
		$items_titles[$country['country_code']] = $country['title'];
	}
	foreach ($items as $item)
	{
		$item = trim($item);
		if (isset($items_mapped[mb_lowercase($item)]))
		{
			$id = $items_mapped[mb_lowercase($item)];
			$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $items_titles[$id]);
			echo "<value id=\"$id\">$title</value>\n";
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
		$data = mr2array(sql_pr("select country_code, title from $config[tables_prefix]list_countries where is_system=0 and (title like '%$q%' or (country_code like '%$q%' and language_code=?)) order by title asc", $lang['system']['language_code']));
		foreach ($data as $country)
		{
			$id = $country['country_code'];
			$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $country['title']);

			$code = strtoupper($country['country_code']);

			echo "<value id=\"$id\" synonyms=\"$code\">$title</value>\n";
		}
		echo "</insight>";
	} else
	{
		echo "<insight for=\"$result_for\"></insight>";
	}
}
