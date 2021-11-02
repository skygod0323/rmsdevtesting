<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();
for ($i = 1; $i <= 5; $i++)
{
	if ($options["FEEDBACK_FIELD_{$i}_NAME"] == '')
	{
		$options["FEEDBACK_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_status_values = array(
	1 => $lang['users']['feedback_field_status_new'],
	2 => $lang['users']['feedback_field_status_closed'],
	21 => $lang['users']['feedback_field_status_replied'],
);

$table_fields = array();
$table_fields[] = array('id' => 'feedback_id',    'title' => $lang['users']['feedback_field_id'],           'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'user',           'title' => $lang['users']['feedback_field_user'],         'is_default' => 1, 'type' => 'user');
$table_fields[] = array('id' => 'subject',        'title' => $lang['users']['feedback_field_subject'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'message',        'title' => $lang['users']['feedback_field_message'],      'is_default' => 1, 'type' => 'longtext');

for ($i = 1; $i <= 10; $i++)
{
	if ($options["ENABLE_FEEDBACK_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["FEEDBACK_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'status_id',      'title' => $lang['users']['feedback_field_status'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'is_nowrap' => 1);
$table_fields[] = array('id' => 'ip',             'title' => $lang['users']['feedback_field_ip'],           'is_default' => 0, 'type' => 'ip');
$table_fields[] = array('id' => 'country',        'title' => $lang['users']['feedback_field_country'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'user_agent',     'title' => $lang['users']['feedback_field_user_agent'],   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'referer',        'title' => $lang['users']['feedback_field_referer'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'added_date',     'title' => $lang['users']['feedback_field_added_date'],   'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'closed_date',    'title' => $lang['users']['feedback_field_closed_date'],  'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "feedback_id";
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
$search_fields[] = array('id' => 'feedback_id', 'title' => $lang['users']['feedback_field_id']);
$search_fields[] = array('id' => 'subject',     'title' => $lang['users']['feedback_field_subject']);
$search_fields[] = array('id' => 'message',     'title' => $lang['users']['feedback_field_message']);
$search_fields[] = array('id' => 'user',        'title' => $lang['users']['feedback_field_user']);
$search_fields[] = array('id' => 'ip',          'title' => $lang['users']['feedback_field_ip']);
$search_fields[] = array('id' => 'custom',      'title' => $lang['common']['dg_filter_search_in_custom']);

$language_code = $lang['system']['language_code'];

$table_name = "$config[tables_prefix]feedbacks";
$table_key_name = "feedback_id";
$table_selector = "$table_name.*, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, $config[tables_prefix]users.email as user_email, countries.country";
$table_projector = "$table_name left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id left join (select country_code, title as country from $config[tables_prefix]list_countries where language_code='$language_code') countries on $table_name.country_code=countries.country_code";

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
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = intval($_GET['se_status_id']);
	}
}

$table_filtered = 0;
$where = '';

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
				$where_search .= " or $table_name.email like '%$q%' or $config[tables_prefix]users.username like '%$q%'";
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= 10; $i++)
				{
					if ($options["ENABLE_FEEDBACK_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if (intval($_SESSION['save'][$page_name]['se_status_id']) > 0)
{
	$where .= " and $table_name.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.email";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (intval($_POST['do_reply']) == 1)
	{
		validate_field('empty', $_POST['response_subject'], $lang['users']['feedback_field_response_subject']);
		validate_field('empty', $_POST['response_headers'], $lang['users']['feedback_field_response_headers']);
		validate_field('empty', $_POST['response_body'], $lang['users']['feedback_field_response_body']);
	}

	if (!is_array($errors))
	{
		if (intval($_POST['do_reply']) == 1)
		{
			$_POST['status_id'] = 2;

			if (!send_mail($_POST['response_email'], $_POST['response_subject'], $_POST['response_body'], $_POST['response_headers']))
			{
				$errors[] = get_aa_error('failed_to_send_email');
				return_ajax_errors($errors);
			}

			$original_subject = mr2string(sql_pr("select subject from $table_name where $table_key_name=?", intval($_POST['item_id'])));

			$_SESSION['save'][$page_name]['headers'] = $_POST['response_headers'];
			if ($original_subject == '')
			{
				$_SESSION['save'][$page_name]['subject'] = $_POST['response_subject'];
			}
		} else
		{
			$_POST['response_body'] = '';
		}

		if (intval($_POST['status_id']) == 2)
		{
			sql_pr("update $table_name set status_id=?, response=?, closed_date=? where $table_key_name=?", intval($_POST['status_id']), $_POST['response_body'], date("Y-m-d H:i:s"), intval($_POST['item_id']));
		} else
		{
			sql_pr("update $table_name set status_id=? where $table_key_name=?", intval($_POST['status_id']), intval($_POST['item_id']));
		}
		$_SESSION['messages'][] = $lang['common']['success_message_modified'];
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
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		sql_pr("delete from $table_name where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action'] == 'close')
	{
		sql_pr("update $table_name set status_id=2, closed_date=? where $table_key_name in ($row_select) and status_id=1", date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['users']['feedback_success_message_closed'];
	}
	return_ajax_success($page_name);
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

	$_POST['ip'] = int2ip($_POST['ip']);
	if ($_POST['email'] == '' && $_POST['user_email'] != '')
	{
		$_POST['email'] = $_POST['user_email'];
	}
	if ($_POST['status_id'] == 1 && $_POST['email'] != '')
	{
		$_POST['response'] = "\n\n\n> " . implode("\n> ", explode("\n", $_POST['message']));
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
	if ($v['user'] == '')
	{
		$data[$k]['user'] = $v['email'];
	}
	if ($v['status_id'] == 2 && $v['response'] != '')
	{
		$data[$k]['status_id'] = 21;
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');
$smarty->assign('options', $options);

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
	$smarty->assign('page_title', str_replace("%1%", $_POST['feedback_id'], $lang['users']['feedback_edit']));
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_feedbacks']);
}

$smarty->display("layout.tpl");
