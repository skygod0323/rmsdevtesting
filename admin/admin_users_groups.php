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
$table_fields[] = array('id' => 'group_id',     'title' => $lang['settings']['admin_user_group_field_id'],          'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',        'title' => $lang['settings']['admin_user_group_field_title'],       'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'description',  'title' => $lang['settings']['admin_user_group_field_description'], 'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'users_amount', 'title' => $lang['settings']['admin_user_group_field_users_count'], 'is_default' => 1, 'type' => 'number', 'link' => 'admin_users.php?no_filter=true&se_group_id=%id%', 'link_id' => 'group_id', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',   'title' => $lang['settings']['admin_user_group_field_added_date'],  'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "group_id";
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

$table_name = "$config[tables_prefix_multi]admin_users_groups";
$table_key_name = "group_id";

$table_selector = "*, (select count(*) from $config[tables_prefix_multi]admin_users where group_id=$table_name.group_id) as users_amount";

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
	$where .= " and (title like '%$q%' or description like '%$q%') ";
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
	settype($_POST['permissions_ids'], "array");

	validate_field('uniq', $_POST['title'], $lang['settings']['admin_user_group_field_title'], array('field_name_in_base' => 'title'));

	$list_groups = array();
	$list_temp = mr2array(sql("select permission_id, title from $config[tables_prefix_multi]admin_permissions order by group_sort_id asc, sort_id asc"));
	foreach ($list_temp as $k => $v)
	{
		$temp = substr($v['title'], 0, strpos($v['title'], "|"));
		if (!in_array($temp, $list_groups))
		{
			$list_groups[] = $temp;
		}
	}

	$is_permissions_selected = 0;
	foreach ($list_groups as $group_prefix)
	{
		$list_group_permissions = mr2array(sql_pr("select permission_id,title from $config[tables_prefix_multi]admin_permissions where title like ? order by group_sort_id asc, sort_id asc", "$group_prefix|%"));

		if ($_POST["access_level_$group_prefix"] == "read")
		{
			foreach ($list_group_permissions as $k => $v)
			{
				if ($v['title'] == "$group_prefix|view")
				{
					$is_permissions_selected = 1;
					break 2;
				}
			}
		} elseif ($_POST["access_level_$group_prefix"] == "full")
		{
			foreach ($list_group_permissions as $k => $v)
			{
				$is_permissions_selected = 1;
				break 2;
			}
		} elseif ($_POST["access_level_$group_prefix"] <> "no")
		{
			foreach ($list_group_permissions as $k => $v)
			{
				if (in_array($v['permission_id'], $_POST['permissions_ids']))
				{
					$is_permissions_selected = 1;
					break 2;
				}
			}
		}
	}
	if ($is_permissions_selected == 0)
	{
		$errors[] = get_aa_error('permissions_required', $lang['settings']['admin_user_group_field_permissions']);
	}

	$_POST['is_access_to_content_flagged_with'] = (intval($_POST['is_access_to_content_flagged_with']) == 1 ? implode(',', $_POST['is_access_to_content_flagged_with_flags'] ?? []) : '');

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);
		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, description=?, is_access_to_own_content=?, is_access_to_disabled_content=?, is_access_to_content_flagged_with=?, added_date=?",
				$_POST['title'], $_POST['description'], intval($_POST['is_access_to_own_content']), intval($_POST['is_access_to_disabled_content']), trim($_POST['is_access_to_content_flagged_with']), date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, description=?, is_access_to_own_content=?, is_access_to_disabled_content=?, is_access_to_content_flagged_with=? where $table_key_name=?",
				$_POST['title'], $_POST['description'], intval($_POST['is_access_to_own_content']), intval($_POST['is_access_to_disabled_content']), trim($_POST['is_access_to_content_flagged_with']), $item_id);
			sql_pr("delete from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?", $item_id);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		foreach ($list_groups as $group_prefix)
		{
			$list_group_permissions = mr2array(sql_pr("select permission_id,title from $config[tables_prefix_multi]admin_permissions where title like ? order by group_sort_id asc, sort_id asc", "$group_prefix|%"));

			if ($_POST["access_level_$group_prefix"] == "read")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if ($v['title'] == "$group_prefix|view")
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_groups_permissions set group_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
				}
			} elseif ($_POST["access_level_$group_prefix"] == "full")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					sql_pr("insert into $config[tables_prefix_multi]admin_users_groups_permissions set group_id=?, permission_id=?", $item_id, $v['permission_id']);
				}
			} elseif ($_POST["access_level_$group_prefix"] <> "no")
			{
				foreach ($list_group_permissions as $k => $v)
				{
					if (in_array($v['permission_id'], $_POST['permissions_ids']))
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_groups_permissions set group_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
					if ($v['title'] == "$group_prefix|view")
					{
						sql_pr("insert into $config[tables_prefix_multi]admin_users_groups_permissions set group_id=?, permission_id=?", $item_id, $v['permission_id']);
					}
				}
			}
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
	$temp_data = mr2array(sql("select *, (select count(*) from $config[tables_prefix_multi]admin_users where group_id=$table_name.group_id) as users_amount from $table_name where $table_key_name in ($row_select)"));
	foreach ($temp_data as $res)
	{
		if ($res['users_amount'] > 0)
		{
			$errors[] = get_aa_error('admin_user_group_cannot_be_deleted', $res['title']);
		}
	}
	if (is_array($errors))
	{
		return_ajax_errors($errors);
	} else
	{
		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql("delete from $table_name where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix_multi]admin_users_groups_permissions where group_id in ($row_select)");
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		}
		return_ajax_success($page_name);
	}
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
	$_POST['permissions_ids'] = mr2array_list(sql_pr("select permission_id from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?", $_POST['group_id']));
	$_POST['is_access_to_content_flagged_with'] = ($_POST['is_access_to_content_flagged_with'] ? array_map('trim', explode(',', $_POST['is_access_to_content_flagged_with'])) : []);
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}

$data = mr2array(sql("select $table_selector from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

// =====================================================================================================================
// display
// =====================================================================================================================

$list_permissions = array();
$list_temp = mr2array(sql("select permission_id, title from $config[tables_prefix_multi]admin_permissions order by group_sort_id asc, sort_id asc"));
foreach ($list_temp as $k => $v)
{
	$temp = substr($v['title'], 0, strpos($v['title'], "|"));
	$list_permissions[$temp][$v['permission_id']] = $v['title'];
}

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
foreach ($plugins_list as $k => $v)
{
	if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/english.php";
	}
	if ($_SESSION['userdata']['lang'] != 'english' && is_file("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php";
	}
}

$languages_list = mr2array(sql("select * from $config[tables_prefix]languages"));
foreach ($languages_list as $language)
{
	$lang['permissions']["localization|$language[code]"] = $language['title'];
}

$smarty = new mysmarty();
$smarty->assign('list_permissions', $list_permissions);
$smarty->assign('left_menu', 'menu_administration.tpl');

if ($_REQUEST['action'] == 'change')
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
$smarty->assign('total_num', $total_num);
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where is_admin_flag=1 order by group_id, title asc")));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['admin_user_group_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['admin_user_group_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_groups_list']);
}

$smarty->display("layout.tpl");
