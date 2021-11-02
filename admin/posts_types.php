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
$table_fields[] = array('id' => 'post_type_id', 'title' => $lang['posts']['post_type_field_id'],           'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',        'title' => $lang['posts']['post_type_field_title'],        'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'description',  'title' => $lang['posts']['post_type_field_description'],  'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'external_id',  'title' => $lang['posts']['post_type_field_external_id'],  'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'url_pattern',  'title' => $lang['posts']['post_type_field_url_pattern'],  'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'posts_amount', 'title' => $lang['posts']['post_type_field_posts_count'],  'is_default' => 1, 'type' => 'number', 'link' => 'posts.php?post_type_id=%id%', 'link_id' => 'post_type_id', 'permission' => 'posts|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',   'title' => $lang['posts']['post_type_field_added_date'],   'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "post_type_id";
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
$search_fields[] = array('id' => 'post_type_id', 'title' => $lang['posts']['post_type_field_id']);
$search_fields[] = array('id' => 'title',        'title' => $lang['posts']['post_type_field_title']);
$search_fields[] = array('id' => 'description',  'title' => $lang['posts']['post_type_field_description']);
$search_fields[] = array('id' => 'external_id',  'title' => $lang['posts']['post_type_field_external_id']);
$search_fields[] = array('id' => 'url_pattern',  'title' => $lang['posts']['post_type_field_url_pattern']);

$table_name = "$config[tables_prefix]posts_types";
$table_key_name = "post_type_id";
$table_selector = "$table_name.*, (select count(*) from $config[tables_prefix]posts where post_type_id=$table_name.post_type_id) as posts_amount";
$table_projector = "$table_name";

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
	$_SESSION['save'][$page_name]['se_field'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
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

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/description':
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'filled/description':
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
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

	validate_field('uniq', $_POST['title'], $lang['posts']['post_type_field_title'], array('field_name_in_base' => 'title'));
	if ($_POST['action'] == 'add_new_complete')
	{
		if (validate_field('external_id', $_POST['external_id'], $lang['posts']['post_type_field_external_id']))
		{
			validate_field('uniq', $_POST['external_id'], $lang['posts']['post_type_field_external_id'], array('field_name_in_base' => 'external_id'));
		}
	}
	if (validate_field('uniq', $_POST['url_pattern'], $lang['posts']['post_type_field_url_pattern'], array('field_name_in_base' => 'url_pattern')))
	{
		if (strpos($_POST['url_pattern'], '%DIR%') === false && strpos($_POST['url_pattern'], '%ID%') === false)
		{
			$errors[] = get_aa_error('token_required', $lang['posts']['post_type_field_url_pattern'], '%DIR%');
		}
	}

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);

		$update_array = array(
			'title' => $_POST['title'],
			'url_pattern' => $_POST['url_pattern'],
			'description' => $_POST['description']
		);

		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array['external_id'] = strtolower($_POST['external_id']);
			$update_array['added_date'] = date("Y-m-d H:i:s");
			$item_id = sql_insert("insert into $table_name set ?%", $update_array);

			for ($i = 1; $i <= 10; $i++)
			{
				sql("insert into $config[tables_prefix]options set variable='ENABLE_POST_{$item_id}_FIELD_{$i}', value=0");
				sql("insert into $config[tables_prefix]options set variable='ENABLE_POST_{$item_id}_FILE_FIELD_{$i}', value=0");
				sql("insert into $config[tables_prefix]options set variable='POST_{$item_id}_FIELD_{$i}_NAME', value=''");
				sql("insert into $config[tables_prefix]options set variable='POST_{$item_id}_FILE_FIELD_{$i}_NAME', value=''");
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=11, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($old_data[$k] <> $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, strlen($update_details) - 2);
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=11, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
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
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete' || $_REQUEST['batch_action'] == 'delete_with_content')
	{
		if ($_REQUEST['batch_action'] != 'delete_with_content')
		{
			$temp_data = mr2array(sql("select *, (select count(*) from $config[tables_prefix]posts where $table_key_name=$table_name.$table_key_name) as posts_amount from $table_name where $table_key_name in ($row_select)"));
			foreach ($temp_data as $res)
			{
				if ($res['posts_amount'] > 0)
				{
					$errors[] = get_aa_error('post_type_cannot_be_deleted', $res['title']);
				}
			}
		}
		if (is_array($errors))
		{
			return_ajax_errors($errors);
		} else
		{
			$temp_data = mr2array_list(sql("select $table_key_name from $table_name where $table_key_name in ($row_select)"));

			sql("delete from $table_name where $table_key_name in ($row_select)");
			foreach ($temp_data as $item_id)
			{
				for ($i = 1; $i <= 10; $i++)
				{
					sql("delete from $config[tables_prefix]options where variable='ENABLE_POST_{$item_id}_FIELD_{$i}'");
					sql("delete from $config[tables_prefix]options where variable='ENABLE_POST_{$item_id}_FILE_FIELD_{$i}'");
					sql("delete from $config[tables_prefix]options where variable='POST_{$item_id}_FIELD_{$i}_NAME'");
					sql("delete from $config[tables_prefix]options where variable='POST_{$item_id}_FILE_FIELD_{$i}_NAME'");
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=11, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			}

			$posts_ids = mr2array_list(sql("select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select)"));
			sql("delete from $config[tables_prefix]tags_posts where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]categories_posts where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]models_posts where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]flags_posts where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]flags_history where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]flags_messages where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]users_events where post_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select))");
			sql("delete from $config[tables_prefix]comments where object_id in (select post_id from $config[tables_prefix]posts where $table_key_name in ($row_select)) and object_type_id=12");
			sql("delete from $config[tables_prefix]posts where $table_key_name in ($row_select)");

			foreach ($posts_ids as $item_id)
			{
				$dir_path = get_dir_by_id($item_id);
				$custom_files = get_contents_from_dir("$config[content_path_posts]/$dir_path/$item_id", 1);
				foreach ($custom_files as $custom_file)
				{
					@unlink("$config[content_path_posts]/$dir_path/$item_id/$custom_file");
				}
				@rmdir("$config[content_path_posts]/$dir_path/$item_id");
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=12, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			}
		}

		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['url_pattern'] = 'posts/%ID%/%DIR%/';
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

// =====================================================================================================================
// display
// =====================================================================================================================

$locked_post_type_support = 0;
if (is_file("$config[project_path]/admin/.htaccess"))
{
	if (strpos(file_get_contents("$config[project_path]/admin/.htaccess"), "posts_for_types.php?post_type_external_id") !== false)
	{
		$locked_post_type_support = 1;
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_posts.tpl');
$smarty->assign('list_types', mr2array(sql("select * from $config[tables_prefix]posts_types order by title asc")));
$smarty->assign('locked_post_type_support', $locked_post_type_support);
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

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['posts']['post_type_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['posts']['post_type_add']);
} else
{
	$smarty->assign('page_title', $lang['posts']['submenu_option_post_types_list']);
}

$smarty->display("layout.tpl");
