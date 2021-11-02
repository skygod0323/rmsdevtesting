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

$currency_codes = mr2array_list(sql("select distinct currency_code from $config[tables_prefix]bill_transactions where currency_code!='' order by currency_code asc"));

$table_fields = array();
$table_fields[] = array('id' => 'stats_date', 'title' => $lang['stats']['users_transactions_field_date'],       'is_default' => 1, 'type' => 'date');
$table_fields[] = array('id' => 'initial',    'title' => $lang['stats']['users_transactions_field_initial'],    'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'tokens',     'title' => $lang['stats']['users_transactions_field_tokens'],     'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'conversion', 'title' => $lang['stats']['users_transactions_field_conversion'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'rebill',     'title' => $lang['stats']['users_transactions_field_rebill'],     'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'chargeback', 'title' => $lang['stats']['users_transactions_field_chargeback'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'refund',     'title' => $lang['stats']['users_transactions_field_refund'],     'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'void',       'title' => $lang['stats']['users_transactions_field_void'],       'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'outed',      'title' => $lang['stats']['users_transactions_field_outed'],      'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'declined',   'title' => $lang['stats']['users_transactions_field_declined'],   'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'manual',     'title' => $lang['stats']['users_transactions_field_manual'],     'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'sms',        'title' => $lang['stats']['users_transactions_field_sms'],        'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'api',        'title' => $lang['stats']['users_transactions_field_api'],        'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'htpasswd',   'title' => $lang['stats']['users_transactions_field_htpasswd'],   'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);

for ($i = 0; $i < count($currency_codes); $i++)
{
	$table_fields[] = array('id' => "revenue$i",    'title' => str_replace("%1%", $currency_codes[$i], $lang['stats']['users_transactions_field_revenue']),    'is_default' => 1, 'type' => 'currency', 'ifdisable_zero' => 1);
}

$sort_def_field = "stats_date";
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
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name]['grid_columns'][$field['id']]);
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

$table_selector = "stats_date, sum(manual) as manual, sum(sms) as sms, sum(api) as api, sum(htpasswd) as htpasswd, sum(initial) as initial, sum(conversion) as conversion, sum(rebill) as rebill, sum(chargeback) as chargeback, sum(refund) as refund, sum(void) as void, sum(tokens) as tokens, sum(outed) as outed, sum(declined) as declined";
$table_summary_selector = "sum(manual) as manual, sum(sms) as sms, sum(api) as api, sum(htpasswd) as htpasswd, sum(initial) as initial, sum(conversion) as conversion, sum(rebill) as rebill, sum(chargeback) as chargeback, sum(refund) as refund, sum(void) as void, sum(tokens) as tokens, sum(outed) as outed, sum(declined) as declined";

for ($i = 0; $i < count($currency_codes); $i++)
{
	$currency_code = sql_escape($currency_codes[$i]);
	$table_selector .= ", sum(case when currency_code='$currency_code' then price else 0 end) as revenue$i, '$currency_code' as revenue{$i}_currency";
	$table_summary_selector .= ", sum(case when currency_code='$currency_code' then price else 0 end) as revenue$i, '$currency_code' as revenue{$i}_currency";
}

$table_summary_field_name = "stats_date";
$table_projector = "(
	select
		DATE_FORMAT(access_start_date, '%Y-%m-%d') as stats_date,
		case when bill_type_id = 1 then 1 else 0 end as manual,
		case when bill_type_id = 4 then 1 else 0 end as api,
		case when bill_type_id = 3 then 1 else 0 end as sms,
		case when bill_type_id = 5 then 1 else 0 end as htpasswd,
		case when bill_type_id = 2 and type_id = 1 then 1 else 0 end as initial,
		case when bill_type_id = 2 and type_id = 2 then 1 else 0 end as conversion,
		case when bill_type_id = 2 and type_id = 3 then 1 else 0 end as rebill,
		case when bill_type_id = 2 and type_id = 4 then 1 else 0 end as chargeback,
		case when bill_type_id = 2 and type_id = 5 then 1 else 0 end as refund,
		case when bill_type_id = 2 and type_id = 6 then 1 else 0 end as void,
		case when bill_type_id = 2 and type_id = 10 then 1 else 0 end as tokens,
		0 as outed,
		0 as declined,
		price,
		currency_code
	from $config[tables_prefix]bill_transactions
	union all select
		added_date as stats_date,
		0 as manual,
		0 as api,
		0 as sms,
		0 as htpasswd,
		0 as initial,
		0 as conversion,
		0 as rebill,
		0 as chargeback,
		0 as refund,
		0 as void,
		0 as tokens,
		outs_amount as outed,
		declines_amount as declined,
		0 as price,
		'' as currency_code
	from $config[tables_prefix]bill_outs
) x";
$table_group_by = "stats_date";

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
	$where .= " and STR_TO_DATE(stats_date,'%Y-%m-%d')>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and STR_TO_DATE(stats_date,'%Y-%m-%d')<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
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

$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));

if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_projector $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

if ($list_grouping == 'date')
{
	foreach ($data as $k => $v)
	{
		if ($v['added_date'] == date('Y-m-d'))
		{
			$data[$k]['is_today'] = 1;
			break;
		}
	}
}

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];

	if ($total_num > 1)
	{
		$where_not_today = "stats_date!='" . date('Y-m-d') . "'";
		if ($where)
		{
			$where .= " and $where_not_today";
		} else
		{
			$where .= "where $where_not_today";
		}
		$summary_data = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
		$summary_data[$table_summary_field_name] = $lang['common']['total'];

		$summary_count = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));

		foreach ($summary_data as $k => $v)
		{
			$total[1][$k] = $v;
			foreach ($table_fields as $table_field)
			{
				if ($table_field['id'] == $k && in_array($table_field['type'], ['number', 'currency', 'duration', 'traffic']))
				{
					if ($summary_count > 0)
					{
						$total[1][$k] /= $summary_count;
					} else
					{
						$total[1][$k] = 0;
					}
				}
			}
		}
		$total[1][$table_summary_field_name] = $lang['common']['average'];
	}
}

$smarty = new mysmarty();
$smarty->assign('list_countries', $list_countries);
$smarty->assign('left_menu', 'menu_stats.tpl');

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('average', $total[1]);
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

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_transactions']);

$smarty->display("layout.tpl");
