<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_bill_type_values = array(
	1 => $lang['users']['bill_transaction_field_bill_type_manual'],
	2 => $lang['users']['bill_transaction_field_bill_type_card'],
	3 => $lang['users']['bill_transaction_field_bill_type_sms'],
	4 => $lang['users']['bill_transaction_field_bill_type_api'],
	5 => $lang['users']['bill_transaction_field_bill_type_htpasswd'],
);

$list_type_values = array(
	1 => $lang['users']['bill_transaction_field_type_initial'],
	2 => $lang['users']['bill_transaction_field_type_conversion'],
	3 => $lang['users']['bill_transaction_field_type_rebill'],
	4 => $lang['users']['bill_transaction_field_type_chargeback'],
	5 => $lang['users']['bill_transaction_field_type_refund'],
	6 => $lang['users']['bill_transaction_field_type_void'],
	10 => $lang['users']['bill_transaction_field_type_tokens'],
);

$list_status_values = array(
	0 => $lang['users']['bill_transaction_field_status_approval'],
	1 => $lang['users']['bill_transaction_field_status_open'],
	2 => $lang['users']['bill_transaction_field_status_closed'],
	3 => $lang['users']['bill_transaction_field_status_cancelled'],
	4 => $lang['users']['bill_transaction_field_status_pending'],
);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[0] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_code']] = $country['title'];
}

$table_fields = array();
$table_fields[] = array('id' => 'transaction_id',    'title' => $lang['users']['bill_transaction_field_id'],            'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'user',              'title' => $lang['users']['bill_transaction_field_user'],          'is_default' => 1, 'type' => 'user');
$table_fields[] = array('id' => 'bill_type_id',      'title' => $lang['users']['bill_transaction_field_bill_type'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_bill_type_values, 'append' => array(2 => 'internal_provider', 3 => 'internal_provider'), 'is_nowrap' => 1);
$table_fields[] = array('id' => 'type_id',           'title' => $lang['users']['bill_transaction_field_type'],          'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values, 'append' => array(1 => 'is_trial_message', 10 => 'tokens_granted'), 'is_nowrap' => 1);
$table_fields[] = array('id' => 'status_id',         'title' => $lang['users']['bill_transaction_field_status'],        'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(1 => 'days_left_message'), 'is_nowrap' => 1);
$table_fields[] = array('id' => 'access_start_date', 'title' => $lang['users']['bill_transaction_field_start_date'],    'is_default' => 1, 'type' => 'datetime', 'min_date_label' => $lang['common']['undefined']);
$table_fields[] = array('id' => 'access_end_date',   'title' => $lang['users']['bill_transaction_field_end_date'],      'is_default' => 1, 'type' => 'datetime', 'min_date_label' => $lang['common']['undefined'], 'max_date_label' => $lang['users']['bill_transaction_field_end_date_unlimited']);
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',            'title' => $lang['users']['bill_transaction_field_ip'],            'is_default' => 0, 'type' => 'ip');
}
$table_fields[] = array('id' => 'country_code',      'title' => $lang['users']['bill_transaction_field_country'],       'is_default' => 0, 'type' => 'choice', 'values' => $list_country_values);
$table_fields[] = array('id' => 'price',             'title' => $lang['users']['bill_transaction_field_price'],         'is_default' => 0, 'type' => 'currency');
$table_fields[] = array('id' => 'currency_code',     'title' => $lang['users']['bill_transaction_field_currency'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'access_code',       'title' => $lang['users']['bill_transaction_field_access_code'],   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'reseller_code',     'title' => $lang['users']['bill_transaction_field_reseller_code'], 'is_default' => 0, 'type' => 'text');

$sort_def_field = "transaction_id";
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

$search_fields = array();
$search_fields[] = array('id' => 'transaction_id',  'title' => $lang['users']['bill_transaction_field_id']);
$search_fields[] = array('id' => 'transaction_log', 'title' => $lang['users']['bill_transaction_field_log']);
$search_fields[] = array('id' => 'ip',              'title' => $lang['users']['bill_transaction_field_ip']);
$search_fields[] = array('id' => 'user',            'title' => $lang['users']['bill_transaction_field_user']);

$table_name = "$config[tables_prefix]bill_transactions";
$table_key_name = "transaction_id";
$table_selector = "$table_name.*, $table_name.currency_code as price_currency, case when $config[tables_prefix]card_bill_providers.title!='' then $config[tables_prefix]card_bill_providers.title when $config[tables_prefix]sms_bill_providers.title!='' then $config[tables_prefix]sms_bill_providers.title end as internal_provider, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, $config[tables_prefix]users.reseller_code";
$table_projector = "$table_name left join $config[tables_prefix]card_bill_providers on $config[tables_prefix]card_bill_providers.internal_id=$table_name.internal_provider_id left join $config[tables_prefix]sms_bill_providers on $config[tables_prefix]sms_bill_providers.internal_id=$table_name.internal_provider_id left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";

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
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_bill_type_id'] = '';
	$_SESSION['save'][$page_name]['se_type_id'] = '';
	$_SESSION['save'][$page_name]['se_start_date_from'] = '';
	$_SESSION['save'][$page_name]['se_start_date_to'] = '';
	$_SESSION['save'][$page_name]['se_end_date_from'] = '';
	$_SESSION['save'][$page_name]['se_end_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_bill_type_id']))
	{
		$_SESSION['save'][$page_name]['se_bill_type_id'] = trim($_GET['se_bill_type_id']);
	}
	if (isset($_GET['se_type_id']))
	{
		$_SESSION['save'][$page_name]['se_type_id'] = trim($_GET['se_type_id']);
	}
	if (isset($_GET['se_start_date_from_Day']) && isset($_GET['se_start_date_from_Month']) && isset($_GET['se_start_date_from_Year']))
	{
		if (intval($_GET['se_start_date_from_Day']) > 0 && intval($_GET['se_start_date_from_Month']) > 0 && intval($_GET['se_start_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_start_date_from'] = intval($_GET['se_start_date_from_Year']) . "-" . intval($_GET['se_start_date_from_Month']) . "-" . intval($_GET['se_start_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_start_date_from'] = "";
		}
	}
	if (isset($_GET['se_start_date_to_Day']) && isset($_GET['se_start_date_to_Month']) && isset($_GET['se_start_date_to_Year']))
	{
		if (intval($_GET['se_start_date_to_Day']) > 0 && intval($_GET['se_start_date_to_Month']) > 0 && intval($_GET['se_start_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_start_date_to'] = intval($_GET['se_start_date_to_Year']) . "-" . intval($_GET['se_start_date_to_Month']) . "-" . intval($_GET['se_start_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_start_date_to'] = "";
		}
	}
	if (isset($_GET['se_end_date_from_Day']) && isset($_GET['se_end_date_from_Month']) && isset($_GET['se_end_date_from_Year']))
	{
		if (intval($_GET['se_end_date_from_Day']) > 0 && intval($_GET['se_end_date_from_Month']) > 0 && intval($_GET['se_end_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_end_date_from'] = intval($_GET['se_end_date_from_Year']) . "-" . intval($_GET['se_end_date_from_Month']) . "-" . intval($_GET['se_end_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_end_date_from'] = "";
		}
	}
	if (isset($_GET['se_end_date_to_Day']) && isset($_GET['se_end_date_to_Month']) && isset($_GET['se_end_date_to_Year']))
	{
		if (intval($_GET['se_end_date_to_Day']) > 0 && intval($_GET['se_end_date_to_Month']) > 0 && intval($_GET['se_end_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_end_date_to'] = intval($_GET['se_end_date_to_Year']) . "-" . intval($_GET['se_end_date_to_Month']) . "-" . intval($_GET['se_end_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_end_date_to'] = "";
		}
	}
}

$where = '';
$table_filtered = 0;

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where_search = '1=0';
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
		if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
		{
			if ($search_field['id'] == $table_key_name)
			{
				if (preg_match("/^([\ ]*[0-9]+[\ ]*,[\ ]*)+[0-9]+[\ ]*$/is", $q))
				{
					$search_ids_array = array_map('intval', array_map('trim', explode(',', $q)));
					$where_search .= " or $table_name.$search_field[id] in (" . implode(',', $search_ids_array) . ")";
				} else
				{
					$where_search .= " or $table_name.$search_field[id]='$q'";
				}
			} elseif ($search_field['id'] == 'ip')
			{
				$q_ip = ip2int($q);
				if ($q_ip > 0)
				{
					$where_search .= " or $table_name.ip='$q_ip'";
				}
			} elseif ($search_field['id'] == 'user')
			{
				$where_search .= " or $config[tables_prefix]users.username like '%$q%'";
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
	$where .= " and ($config[tables_prefix]users.username='$q') ";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_status_id'])
{
	case '0':
	case '1':
	case '2':
	case '3':
	case '4':
		$where .= " and $table_name.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
		$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_bill_type_id'])
{
	case '1':
	case '2':
	case '3':
	case '4':
	case '5':
		$where .= " and $table_name.bill_type_id=" . intval($_SESSION['save'][$page_name]['se_bill_type_id']);
		$table_filtered = 1;
		break;
	default:
		if ($_SESSION['save'][$page_name]['se_bill_type_id'])
		{
			$where .= " and $table_name.internal_provider_id='" . sql_escape($_SESSION['save'][$page_name]['se_bill_type_id']) . "'";
			$table_filtered = 1;
		}
}

switch ($_SESSION['save'][$page_name]['se_type_id'])
{
	case '1':
	case '2':
	case '3':
	case '4':
	case '5':
	case '6':
	case '10':
		$where .= " and $table_name.type_id=" . intval($_SESSION['save'][$page_name]['se_type_id']);
		$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_start_date_from'] <> "")
{
	$where .= " and $table_name.access_start_date>='" . $_SESSION['save'][$page_name]['se_start_date_from'] . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_start_date_to'] <> "")
{
	$where .= " and $table_name.access_start_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_start_date_to']) + 86399) . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_end_date_from'] <> "")
{
	$where .= " and $table_name.access_end_date>='" . $_SESSION['save'][$page_name]['se_end_date_from'] . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_end_date_to'] <> "")
{
	$where .= " and $table_name.access_end_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_end_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'reseller_code')
{
	$sort_by = "$config[tables_prefix]users.reseller_code";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_POST['item_id']);

	if ($_POST['action'] == 'add_new_complete')
	{
		if (validate_field('empty', $_POST['user'], $lang['users']['bill_transaction_field_user']))
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['users']['bill_transaction_field_user']);
			} else
			{
				$user_existing_status = mr2number(sql_pr("select status_id from $config[tables_prefix]users where username=?", $_POST['user']));
				if (!in_array($user_existing_status, array(2)))
				{
					$errors[] = get_aa_error('bill_invalid_user_status', $lang['users']['bill_transaction_field_user']);
				}
			}
		}

		if (intval($_POST['access_type']) == 1)
		{
			$_POST['duration'] = 0;
			$_POST['tokens'] = 0;
		} elseif (intval($_POST['access_type']) == 2)
		{
			validate_field('empty_int', $_POST['duration'], $lang['users']['bill_transaction_field_access_type']);
			$_POST['tokens'] = 0;
		} elseif (intval($_POST['access_type']) == 3)
		{
			validate_field('empty_int', $_POST['tokens'], $lang['users']['bill_transaction_field_access_type']);
			$_POST['duration'] = 0;
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? and status_id!=4", $_POST['user']));
			$access_start_date = date("Y-m-d H:i:s");
			$user_status_id = 3;
			$transaction_status_id = 1;
			$type_id = 1;
			if ($_POST['duration'] == '0')
			{
				$access_end_date = "2070-01-01 00:00:00";
				$is_unlimited = 1;
			} else
			{
				$access_end_date = date("Y-m-d H:i:s", mktime(date("H"), date("i"), date("s"), date("m"), date("d") + intval($_POST['duration']), date("Y")));
				$is_unlimited = 0;
			}
			if (intval($_POST['tokens']) > 0)
			{
				$type_id = 10;
				$access_end_date = $access_start_date;
				$is_unlimited = 0;
				$user_status_id = 2;
				$transaction_status_id = 2;
			}
			sql_pr("update $table_name set status_id=3 where user_id=? and status_id=1", $user_id);
			sql_pr("insert into $table_name set user_id=?, status_id=?, bill_type_id=1, type_id=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, tokens_granted=?, transaction_log=?", $user_id, $transaction_status_id, $type_id, $access_start_date, $access_end_date, intval($is_unlimited), intval($_POST['tokens']), $_POST['transaction_log']);
			sql_pr("update $config[tables_prefix]users set status_id=?, tokens_available=tokens_available+? where user_id=?", $user_status_id, intval($_POST['tokens']), $user_id);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} elseif (intval($_POST['status_id']) == 3)
		{
			$transaction_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
			if ($transaction_data['type_id'] == 10)
			{
				revoke_tokens_from_user($transaction_data['user_id'], $transaction_data['tokens_granted']);
				sql_pr("update $table_name set status_id=3 where $table_key_name=?", $item_id);
			} else
			{
				$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				sql_pr("update $config[tables_prefix]users set status_id=? where status_id in (2,3) and user_id=?", intval($memberzone_data['STATUS_AFTER_PREMIUM']), $transaction_data['user_id']);
				sql_pr("update $table_name set status_id=3, access_end_date=?, is_unlimited_access=0 where $table_key_name=?", date("Y-m-d H:i:s"), $item_id);
			}
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

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
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select']) > 0)
{
	$row_select = array_map("intval", $_REQUEST['row_select']);
	if ($_REQUEST['batch_action'] == 'cancel')
	{
		$memberzone_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
		foreach ($_REQUEST['row_select'] as $transaction_id)
		{
			$transaction_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $transaction_id));
			if ($transaction_data['type_id'] == 10)
			{
				revoke_tokens_from_user($transaction_data['user_id'], $transaction_data['tokens_granted']);
				sql_pr("update $table_name set status_id=3 where $table_key_name=?", $transaction_id);
			} else
			{
				sql_pr("update $config[tables_prefix]users set status_id=? where status_id in (2,3) and user_id=?", intval($memberzone_data['STATUS_AFTER_PREMIUM']), $transaction_data['user_id']);
				sql_pr("update $table_name set status_id=3, access_end_date=?, is_unlimited_access=0 where $table_key_name=?", date("Y-m-d H:i:s"), $transaction_id);
			}
		}

		$_SESSION['messages'][] = $lang['users']['bill_transaction_success_message_cancelled'];
		return_ajax_success($page_name);
	}
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_projector $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if ($v['status_id'] == 0 && $v['user'] == '')
	{
		$data[$k]['user'] = $lang['users']['bill_transaction_field_user_waiting'];
	}
	if ($v['is_trial'] == 1)
	{
		$data[$k]['is_trial_message'] = $lang['users']['bill_transaction_field_type_initial_trial'];
	}
	if ($v['status_id'] == 1)
	{
		if ($v['is_unlimited_access'])
		{
			$data[$k]['days_left_message'] = $lang['users']['bill_transaction_field_status_open_unlimited'];
		} else
		{
			$data[$k]['days_left_message'] = str_replace("%1%", round((strtotime($data[$k]['access_end_date']) - time()) / 86400), $lang['users']['bill_transaction_field_status_open_days']);
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');
$smarty->assign('list_providers', mr2array(sql("select * from (select internal_id, title from $config[tables_prefix]card_bill_providers) X order by title asc")));

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%2%", $_POST['username'], str_replace("%1%", $_POST['transaction_id'], $lang['users']['bill_transaction_edit'])));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['bill_transaction_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_billing_transactions']);
}

$smarty->display("layout.tpl");
