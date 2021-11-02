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

$table_fields = array();
$table_fields[] = array('id' => 'entry_id',    'title' => $lang['users']['blog_entry_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'entry',       'title' => $lang['users']['blog_entry_field_entry'],      'is_default' => 1, 'type' => 'longtext', 'ifdisable' => 'is_deleted');
$table_fields[] = array('id' => 'user_from',   'title' => $lang['users']['blog_entry_field_author'],     'is_default' => 1, 'type' => 'user');
$table_fields[] = array('id' => 'user',        'title' => $lang['users']['blog_entry_field_user_blog'],  'is_default' => 1, 'type' => 'user');
$table_fields[] = array('id' => 'is_approved', 'title' => $lang['users']['blog_entry_field_approved'],   'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'added_date',  'title' => $lang['users']['blog_entry_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "entry_id";
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
$search_fields[] = array('id' => 'entry_id', 'title' => $lang['users']['blog_entry_field_id']);
$search_fields[] = array('id' => 'entry',    'title' => $lang['users']['blog_entry_field_entry']);

$table_name = "$config[tables_prefix]users_blogs";
$table_key_name = "entry_id";
$table_selector = "$table_name.*, u1.username as user, u1.status_id as user_status_id, u2.username as user_from, u2.status_id as user_from_status_id";
$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id left join $config[tables_prefix]users u2 on u2.user_id=$table_name.user_from_id";

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
	$_SESSION['save'][$page_name]['se_user_from'] = '';
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
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_user_from']))
	{
		$_SESSION['save'][$page_name]['se_user_from'] = trim($_GET['se_user_from']);
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
	$where .= " and u1.username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_user_from'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user_from']);
	$where .= " and u2.username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] == 1)
{
	$where .= " and is_approved=0 ";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == 2)
{
	$where .= " and is_approved=1 ";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == 2)
{
	$where .= " and is_approved=0 ";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "u1.username";
} elseif ($sort_by == 'user_from')
{
	$sort_by = "u2.username";
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

	validate_field('empty', $_POST['entry'], $lang['users']['blog_entry_field_entry']);

	if (!is_array($errors))
	{
		sql_pr("update $table_name set entry=?, is_approved=? where $table_key_name=?", $_POST['entry'], intval($_POST['is_approved']), intval($_POST['item_id']));
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
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]users_events where entry_id in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	if ($_REQUEST['batch_action'] == 'approve')
	{
		sql("update $table_name set is_approved=1 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['users']['blog_entry_success_message_approved'];
	}
	if ($_REQUEST['batch_action'] == 'approve_and_delete')
	{
		sql("update $table_name set is_approved=1 where $table_key_name in ($row_select)");

		$ids_to_delete = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
		if (count($ids_to_delete) > 0)
		{
			$ids_to_delete = implode(",", array_map("intval", $ids_to_delete));
			$ids_to_delete = mr2array_list(sql("select $table_key_name from $table_name where is_approved=0 and $table_key_name in ($ids_to_delete)"));
			if (count($ids_to_delete) > 0)
			{
				$ids_to_delete = implode(",", array_map("intval", $ids_to_delete));
				sql("delete from $config[tables_prefix]users_events where entry_id in ($ids_to_delete)");
				sql("delete from $table_name where entry_id in ($ids_to_delete)");
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_completed'];
	}
	if ($_REQUEST['batch_action'] == 'delete_and_approve')
	{
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]users_events where entry_id in ($row_select)");

		$ids_to_approve = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
		if (count($ids_to_approve) > 0)
		{
			$ids_to_approve = implode(",", array_map("intval", $ids_to_approve));
			sql("update $table_name set is_approved=1 where entry_id in ($ids_to_approve)");
		}
		$_SESSION['messages'][] = $lang['common']['success_message_completed'];
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
	if ($data[$k]['entry'] == '')
	{
		$data[$k]['entry'] = $lang['users']['blog_entry_field_entry_deleted'];
		$data[$k]['is_deleted'] = 1;
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');

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
	$smarty->assign('page_title', str_replace("%1%", $_POST['entry_id'], $lang['users']['blog_entry_edit']));
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_blog_entries_list']);
}

$smarty->display("layout.tpl");
