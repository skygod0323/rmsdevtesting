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
$table_fields[] = array('id' => 'added_date',                 'title' => $lang['stats']['overload_field_date'],           'is_default' => 1, 'type' => 'date');
$table_fields[] = array('id' => 'amount_max_la_pages',        'title' => $lang['stats']['overload_field_pages_la'],       'is_default' => 1, 'type' => 'text', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'amount_max_la_blocks',       'title' => $lang['stats']['overload_field_blocks_la'],      'is_default' => 1, 'type' => 'text', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'amount_max_mysql_processes', 'title' => $lang['stats']['overload_field_blocks_mysql'],   'is_default' => 1, 'type' => 'text', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'amount_max_timeout_blocks',  'title' => $lang['stats']['overload_field_blocks_failure'], 'is_default' => 1, 'type' => 'text', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'amount_max_la_cron',         'title' => $lang['stats']['overload_field_cron_la'],        'is_default' => 1, 'type' => 'text', 'ifdisable_zero' => 1);

$sort_def_field = "added_date";
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

$table_name = "$config[tables_prefix_multi]stats_overload_protection";

$table_selector = "added_date, sum(amount_max_la_pages) as amount_max_la_pages, sum(amount_max_la_pages)-sum(last_amount_max_la_pages) as new_max_la_pages, sum(amount_max_la_blocks) as amount_max_la_blocks, sum(amount_max_la_blocks)-sum(last_amount_max_la_blocks) as new_max_la_blocks, sum(amount_max_mysql_processes) as amount_max_mysql_processes, sum(amount_max_mysql_processes)-sum(last_amount_max_mysql_processes) as new_max_mysql_processes, sum(amount_max_timeout_blocks) as amount_max_timeout_blocks, sum(amount_max_timeout_blocks)-sum(last_amount_max_timeout_blocks) as new_max_timeout_blocks, sum(amount_max_la_cron) as amount_max_la_cron, sum(amount_max_la_cron)-sum(last_amount_max_la_cron) as new_max_la_cron";
$table_summary_selector = "sum(amount_max_la_pages) as amount_max_la_pages, sum(amount_max_la_blocks) as amount_max_la_blocks, sum(amount_max_mysql_processes) as amount_max_mysql_processes, sum(amount_max_timeout_blocks) as amount_max_timeout_blocks, sum(amount_max_la_cron) as amount_max_la_cron";
$table_summary_field_name = "added_date";
$table_group_by = "added_date";

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
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_period_id']))
	{
		$_SESSION['save'][$page_name]['se_period_id'] = intval($_GET['se_period_id']);
		switch ($_SESSION['save'][$page_name]['se_period_id'])
		{
			case 0:
				$_SESSION['save'][$page_name]['se_date_from'] = '';
				$_SESSION['save'][$page_name]['se_date_to'] = '';
				break;
			case 1:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 6);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 2:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 30);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 3:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 4:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1', strtotime(date('Y-m-1 00:00:00')) - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', strtotime(date('Y-m-1 00:00:00')) - 86400);
				break;
			case 5:
				if (isset($_GET['se_date_from_Day'], $_GET['se_date_from_Month'], $_GET['se_date_from_Year']))
				{
					if (intval($_GET['se_date_from_Day']) > 0 && intval($_GET['se_date_from_Month']) > 0 && intval($_GET['se_date_from_Year']) > 0)
					{
						$_SESSION['save'][$page_name]['se_date_from'] = intval($_GET['se_date_from_Year']) . "-" . intval($_GET['se_date_from_Month']) . "-" . intval($_GET['se_date_from_Day']);
					} else
					{
						$_SESSION['save'][$page_name]['se_date_from'] = "";
					}
				}
				if (isset($_GET['se_date_to_Day'], $_GET['se_date_to_Month'], $_GET['se_date_to_Year']))
				{
					if (intval($_GET['se_date_to_Day']) > 0 && intval($_GET['se_date_to_Month']) > 0 && intval($_GET['se_date_to_Year']) > 0)
					{
						$_SESSION['save'][$page_name]['se_date_to'] = intval($_GET['se_date_to_Year']) . "-" . intval($_GET['se_date_to_Month']) . "-" . intval($_GET['se_date_to_Day']);
					} else
					{
						$_SESSION['save'][$page_name]['se_date_to'] = "";
					}
				}
				break;
		}
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_name $where"));

if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_name $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if ($v['new_max_la_pages'] > 0)
	{
		$data[$k]['amount_max_la_pages'] .= " (+$v[new_max_la_pages])";
	}
	if ($v['new_max_la_blocks'] > 0)
	{
		$data[$k]['amount_max_la_blocks'] .= " (+$v[new_max_la_blocks])";
	}
	if ($v['new_max_mysql_processes'] > 0)
	{
		$data[$k]['amount_max_mysql_processes'] .= " (+$v[new_max_mysql_processes])";
	}
	if ($v['new_max_timeout_blocks'] > 0)
	{
		$data[$k]['amount_max_timeout_blocks'] .= " (+$v[new_max_timeout_blocks])";
	}
	if ($v['new_max_la_cron'] > 0)
	{
		$data[$k]['amount_max_la_cron'] .= " (+$v[new_max_la_cron])";
	}
}

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_name $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];
}

sql("update $table_name set is_warning=0, last_amount_max_la_pages=amount_max_la_pages, last_amount_max_la_blocks=amount_max_la_blocks, last_amount_max_mysql_processes=amount_max_mysql_processes, last_amount_max_la_cron=amount_max_la_cron, last_amount_max_timeout_blocks=amount_max_timeout_blocks");

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_overload']);

$smarty->display("layout.tpl");
