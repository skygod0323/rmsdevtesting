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

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));
$options = get_options();

for ($i = 1; $i <= 5; $i++)
{
	if ($options["CS_GROUP_FIELD_{$i}_NAME"] == '')
	{
		$options["CS_GROUP_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['categorization']['content_source_group_field_status_disabled'],
	1 => $lang['categorization']['content_source_group_field_status_active'],
);

$table_fields = array();
$table_fields[] = array('id' => 'content_source_group_id', 'title' => $lang['categorization']['content_source_group_field_id'],              'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                   'title' => $lang['categorization']['content_source_group_field_title'],           'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',                     'title' => $lang['categorization']['content_source_group_field_directory'],       'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',             'title' => $lang['categorization']['content_source_group_field_description'],     'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'external_id',             'title' => $lang['categorization']['content_source_group_field_external_id'],     'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'status_id',               'title' => $lang['categorization']['content_source_group_field_status'],          'is_default' => 0, 'type' => 'choice', 'values' => $list_status_values);

for ($i = 1; $i <= 10; $i++)
{
	if ($options["ENABLE_CS_GROUP_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["CS_GROUP_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'content_sources_amount',  'title' => $lang['categorization']['content_source_group_field_content_sources'], 'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'link' => 'content_sources.php?no_filter=true&se_content_source_group_id=%id%', 'link_id' => 'content_source_group_id', 'permission' => 'content_sources|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',              'title' => $lang['categorization']['content_source_group_field_added_date'],      'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'sort_id',                 'title' => $lang['categorization']['content_source_group_field_order'],           'is_default' => 1, 'type' => 'sorting');

$sort_def_field = "content_source_group_id";
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
	if ($field['show_in_sidebar'] == 1)
	{
		$sidebar_fields[] = $field;
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
$search_fields[] = array('id' => 'content_source_group_id', 'title' => $lang['categorization']['content_source_group_field_id']);
$search_fields[] = array('id' => 'title',                   'title' => $lang['categorization']['content_source_group_field_title']);
$search_fields[] = array('id' => 'dir',                     'title' => $lang['categorization']['content_source_group_field_directory']);
$search_fields[] = array('id' => 'description',             'title' => $lang['categorization']['content_source_group_field_description']);
$search_fields[] = array('id' => 'external_id',             'title' => $lang['categorization']['content_source_group_field_external_id']);
$search_fields[] = array('id' => 'custom',                  'title' => $lang['common']['dg_filter_search_in_custom']);
if (count($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name = "$config[tables_prefix]content_sources_groups";
$table_key_name = "content_source_group_id";

$table_selector_content_sources_count = "(select count(*) from $config[tables_prefix]content_sources where $table_key_name=$table_name.$table_key_name)";
$table_selector = "$table_name.*, $table_selector_content_sources_count as content_sources_amount";
$table_selector_single = $table_selector;

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
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_usage'] = '';
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
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_usage']))
	{
		$_SESSION['save'][$page_name]['se_usage'] = trim($_GET['se_usage']);
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
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= 10; $i++)
				{
					if ($options["ENABLE_CS_GROUP_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'translations')
			{
				foreach ($languages as $language)
				{
					if (intval($_SESSION['save'][$page_name]["se_text_title"]) == 1)
					{
						$where_search .= " or $table_name.title_{$language['code']} like '%$q%'";
					}
					if (intval($_SESSION['save'][$page_name]["se_text_description"]) == 1)
					{
						$where_search .= " or $table_name.description_{$language['code']} like '%$q%'";
					}
					if (intval($_SESSION['save'][$page_name]["se_text_dir"]) == 1)
					{
						$where_search .= " or $table_name.dir_{$language['code']} like '%$q%'";
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

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and $table_name.status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and $table_name.status_id=1";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/description':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
	case 'empty/custom4':
	case 'empty/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'filled/description':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
	case 'filled/custom4':
	case 'filled/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_usage'])
{
	case 'used/content_sources':
		$where .= " and exists (select content_source_id from $config[tables_prefix]content_sources where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'notused/content_sources':
		$where .= " and not exists (select content_source_id from $config[tables_prefix]content_sources where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'content_sources_amount')
{
	$sort_by = "$table_selector_content_sources_count";
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

	validate_field('uniq', $_POST['title'], $lang['categorization']['content_source_group_field_title'], array('field_name_in_base' => 'title'));
	if ($_POST['external_id'])
	{
		validate_field('uniq', $_POST['external_id'], $lang['categorization']['content_source_group_field_external_id'], array('field_name_in_base' => 'external_id'));
	}

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);

		if ($_POST['dir'] == '')
		{
			$_POST['dir'] = get_correct_dir_name($_POST['title']);
		}
		if ($_POST['dir'] <> '')
		{
			$temp_dir = $_POST['dir'];
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $table_name where dir=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
				{
					$_POST['dir'] = $temp_dir;
					break;
				}
				$temp_dir = $_POST['dir'] . $i;
			}
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, dir=?, description=?, status_id=?, external_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=?, added_date=?",
				$_POST['title'], $_POST['dir'], $_POST['description'], intval($_POST['status_id']), $_POST['external_id'], $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], date("Y-m-d H:i:s")
			);

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=8, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_POST['item_id'])));

			$next_item_id = 0;
			if (isset($_POST['save_and_edit']))
			{
				$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where order by $sort_by, $table_name.$table_key_name"));
				$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
				if ($next_item_id == 0)
				{
					$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where order by $sort_by limit 1"));
				}
				if ($next_item_id == $item_id)
				{
					$next_item_id = 0;
				}
			}

			sql_pr("update $table_name set title=?, dir=?, description=?, status_id=?, external_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=? where $table_key_name=?",
				$_POST['title'], $_POST['dir'], $_POST['description'], intval($_POST['status_id']), $_POST['external_id'], $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $item_id
			);

			$update_details = '';
			foreach ($old_data as $k => $v)
			{
				if (isset($_POST[$k]) && $_POST[$k] <> $v)
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, strlen($update_details) - 2);
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=8, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if (isset($_POST['save_and_edit']))
		{
			if ($next_item_id == 0)
			{
				$_POST['save_and_close'] = $_POST['save_and_edit'];
				return_ajax_success($page_name, 1);
			} else
			{
				return_ajax_success($page_name . "?action=change&amp;item_id=$next_item_id", 1);
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
	if ($_REQUEST['batch_action'] == 'delete')
	{
		$data = mr2array(sql("select $table_key_name from $table_name where $table_key_name in ($row_select)"));
		foreach ($data as $k => $v)
		{
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=8, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $v[$table_key_name], date("Y-m-d H:i:s"));
		}

		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("update $config[tables_prefix]content_sources set content_source_group_id=0 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		sql("update $table_name set status_id=1 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// reorder
// =====================================================================================================================

if (isset($_REQUEST['reorder']))
{
	$data = mr2array(sql("select $table_key_name from $table_name"));
	foreach ($data as $res)
	{
		$temp_field_id = intval($res[$table_key_name]);
		$temp_sort_id = intval($_REQUEST["sorting_$temp_field_id"]);

		if (isset($_REQUEST["sorting_$temp_field_id"]))
		{
			sql("update $table_name set sort_id=$temp_sort_id where $table_key_name=$temp_field_id");
		}
	}
	$_SESSION['messages'][] = $lang['common']['success_message_reordered'];
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector_single from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = 1;
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($_GET['action'] == '')
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
	if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
	{
		$_SESSION['save'][$page_name]['from'] = 0;
	}
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_categorization.tpl');
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
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['categorization']['content_source_group_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['categorization']['content_source_group_add']);
} else
{
	$smarty->assign('page_title', $lang['categorization']['submenu_option_content_source_groups_list']);
}

$smarty->display("layout.tpl");
