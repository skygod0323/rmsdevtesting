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

$list_providers = array();
$list_providers_temp = mr2array(sql("select * from (select internal_id, title from $config[tables_prefix]card_bill_providers union all select internal_id, title from $config[tables_prefix]sms_bill_providers union all select 'cron', 'Cron') X order by title asc"));
$list_providers_temp[] = array('internal_id' => "htpasswd", 'title' => "Htpasswd");
foreach ($list_providers_temp as $key => $provider)
{
	$list_providers[$provider['internal_id']] = $provider['title'];
}

$list_message_types = array(
		0 => $lang['settings']['bill_log_field_message_type_debug'],
		1 =>  $lang['settings']['bill_log_field_message_type_info'],
		2 => $lang['settings']['bill_log_field_message_type_error'],
);

$table_fields = array();
$table_fields[] = array('id' => 'record_id',            'title' => $lang['settings']['bill_log_field_id'],           'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'internal_provider_id', 'title' => $lang['settings']['bill_log_field_provider'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_providers);
$table_fields[] = array('id' => 'message_type',         'title' => $lang['settings']['bill_log_field_message_type'], 'is_default' => 1, 'type' => 'choice', 'values' => $list_message_types, 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'message_text',         'title' => $lang['settings']['bill_log_field_message'],      'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'message_details',      'title' => $lang['settings']['bill_log_field_details'],      'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'added_date',           'title' => $lang['settings']['bill_log_field_datetime'],     'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "record_id";
$sort_def_direction = "desc";
$sort_array = array();
$sidebar_fields = array();
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

$table_name = "$config[tables_prefix]bill_log";
$table_key_name = "record_id";

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
	$_SESSION['save'][$page_name]['se_internal_provider_id'] = '';
	$_SESSION['save'][$page_name]['se_text'] = "";
	$_SESSION['save'][$page_name]['se_show_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = "";
	$_SESSION['save'][$page_name]['se_date_to'] = "";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_internal_provider_id']))
	{
		$_SESSION['save'][$page_name]['se_internal_provider_id'] = trim($_GET['se_internal_provider_id']);
	}
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_show_id']))
	{
		$_SESSION['save'][$page_name]['se_show_id'] = intval($_GET['se_show_id']);
	}
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
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (message_text like '%$q%' or message_details like '%$q%') ";
}
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
if ($_SESSION['save'][$page_name]['se_show_id'] == 1)
{
	$where .= " and message_type>=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 2)
{
	$where .= " and message_type>=2";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_internal_provider_id'] != '')
{
	$where .= " and internal_provider_id='" . sql_escape($_SESSION['save'][$page_name]['se_internal_provider_id']) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select']) > 0)
{
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'repeat')
	{
		$data = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
		foreach ($data as $postback)
		{
			if ($postback['is_postback'] == 1 && $postback['message_details'])
			{
				if (is_file("$config[project_path]/admin/billings/$postback[internal_provider_id]/$postback[internal_provider_id].php"))
				{
					$postback_url = "$config[project_url]/admin/billings/$postback[internal_provider_id]/$config[billing_scripts_name].php";
					$postback_data = array();
					$postback_data_temp = explode("\n", $postback['message_details']);
					foreach ($postback_data_temp as $postback_data_item)
					{
						if ($postback_data_item)
						{
							$postback_data_item = explode(':', $postback_data_item, 2);
							$postback_data[trim($postback_data_item[0])] = trim($postback_data_item[1]);
						}
					}
					get_page('', $postback_url, $postback_data, '', 1, 0, 10, '');
				}
			}
		}
		$_SESSION['messages'][] = $lang['settings']['success_message_postback_repeated'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
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
	if ($v['message_type'] == 2)
	{
		$data[$k]['is_error'] = 1;
	}
	if (!$v['message_details'])
	{
		$data[$k]['is_editing_forbidden'] = 1;
	}
}

if ($_GET['reset_errors'] == 1)
{
	sql_update("update $table_name set is_alert=0 where is_alert=1");
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');
$smarty->assign('list_providers', $list_providers);

$smarty->assign('data', $data);
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', $lang['settings']['bill_log_view']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_bill_log']);
}

$smarty->display("layout.tpl");
