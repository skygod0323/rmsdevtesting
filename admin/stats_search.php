<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$table_fields = array();
$table_fields[] = array('id' => 'query',                'title' => $lang['stats']['search_field_query'],                'is_default' => 1, 'type' => 'text', 'link' => 'custom');
$table_fields[] = array('id' => 'amount',               'title' => $lang['stats']['search_field_total'],                'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'query_results_videos', 'title' => $lang['stats']['search_field_query_results_videos'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'query_results_albums', 'title' => $lang['stats']['search_field_query_results_albums'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'query_results_total',  'title' => $lang['stats']['search_field_query_results_total'],  'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',           'title' => $lang['stats']['search_field_added_date'],           'is_default' => 1, 'type' => 'date');

$sort_def_field = "amount";
$sort_def_direction = "desc";

$sort_array = array();
foreach ($table_fields as $k => $field)
{
	if ($field['type'] != 'longtext' && $field['type'] != 'list' && $field['type'] != 'rename' && $field['type'] != 'thumb')
	{
		$sort_array[] = $field['id'];
		$table_fields[$k]['is_sortable'] = 1;
	}
	if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
	{
		if (in_array($field['id'], $_GET['grid_columns']))
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']]);
	} else
	{
		$table_fields[$k]['is_enabled'] = intval($field['is_default']);
	}
	if ($field['type'] == 'id')
	{
		$table_fields[$k]['is_enabled'] = 1;
	}
}
if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
{
	$_SESSION['save'][$page_name]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name]['grid_columns_order']))
{
	$temp_table_fields = array();
	foreach ($table_fields as $table_field)
	{
		if ($table_field['type'] == 'id')
		{
			$temp_table_fields[] = $table_field;
			break;
		}
	}
	foreach ($_SESSION['save'][$page_name]['grid_columns_order'] as $table_field_id)
	{
		foreach ($table_fields as $table_field)
		{
			if ($table_field['id'] == $table_field_id)
			{
				$temp_table_fields[] = $table_field;
				break;
			}
		}
	}
	foreach ($table_fields as $table_field)
	{
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix_multi]stats_search";

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name]['sort_by'] == '')
{
	$_SESSION['save'][$page_name]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name]['sort_direction'] = 'desc';
	}
}

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (query like '%$q%') ";
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if ($_POST['action'] == 'add_new_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	validate_field('empty', $_POST['queries'], $lang['stats']['search_field_queries']);

	if (!is_array($errors))
	{
		$queries = explode("\n", $_POST['queries']);
		foreach ($queries as $query)
		{
			$query = trim($query);
			if (mr2number(sql_pr("select count(*) from $table_name where query_md5=md5(lower(?))", $query)) == 0)
			{
				sql_pr("insert into $table_name set is_manual=1, query=?, query_md5=md5(lower(query)), query_length=length(query), added_date=?", $query, date("Y-m-d H:i:s"));
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_added'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && count($_REQUEST['row_select']) > 0)
{
	if ($_REQUEST['batch_action'] == 'delete')
	{
		foreach ($_REQUEST['row_select'] as $query)
		{
			sql_pr("delete from $table_name where query=?", $query);
		}
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));

if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select * from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if ($website_ui_data['WEBSITE_LINK_PATTERN_SEARCH'] <> '')
	{
		$query = $data[$k]['query'];
		$query = str_replace(array("&", "?", "/", " "), array("%26", "%3F", "%2F", "-"), $query);
		$data[$k]['query_link'] = "$config[project_url]/" . str_replace("%QUERY%", rawurlencode($query), $website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']);
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_GET['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['stats']['search_add']);
} else
{
	$smarty->assign('page_title', $lang['stats']['submenu_option_stats_search']);
}

$smarty->display("layout.tpl");
