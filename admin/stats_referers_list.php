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

for ($i = 1; $i <= 3; $i++)
{
	if ($options["REFERER_FIELD_{$i}_NAME"] == '')
	{
		$options["REFERER_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}
for ($i = 1; $i <= 3; $i++)
{
	if ($options["REFERER_FILE_FIELD_{$i}_NAME"] == '')
	{
		$options["REFERER_FILE_FIELD_{$i}_NAME"] = $lang['settings']["custom_file_field_{$i}"];
	}
}

$table_fields = array();
$table_fields[] = array('id' => 'referer_id',  'title' => $lang['stats']['referer_field_id'],          'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',       'title' => $lang['stats']['referer_field_title'],       'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'referer',     'title' => $lang['stats']['referer_field_referer'],     'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'category',    'title' => $lang['stats']['referer_field_category'],    'is_default' => 1, 'type' => 'refid', 'link' => 'categories.php?action=change&item_id=%id%', 'link_id' => 'category_id', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'description', 'title' => $lang['stats']['referer_field_description'], 'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'url',         'title' => $lang['stats']['referer_field_url'],         'is_default' => 0, 'type' => 'url');

for ($i = 1; $i <= 3; $i++)
{
	if ($options["ENABLE_REFERER_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["REFERER_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'added_date',  'title' => $lang['stats']['referer_field_added_date'],  'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "referer_id";
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

$search_fields = array();
$search_fields[] = array('id' => 'referer_id',  'title' => $lang['stats']['referer_field_id']);
$search_fields[] = array('id' => 'title',       'title' => $lang['stats']['referer_field_title']);
$search_fields[] = array('id' => 'referer',     'title' => $lang['stats']['referer_field_referer']);
$search_fields[] = array('id' => 'description', 'title' => $lang['stats']['referer_field_description']);
$search_fields[] = array('id' => 'url',         'title' => $lang['stats']['referer_field_url']);
$search_fields[] = array('id' => 'custom',      'title' => $lang['common']['dg_filter_search_in_custom']);

$table_name = "$config[tables_prefix_multi]stats_referers_list";
$table_key_name = "referer_id";
$table_selector = "$table_name.*, $config[tables_prefix]categories.title as category";
$table_projector = "$table_name left join $config[tables_prefix]categories on $config[tables_prefix]categories.category_id=$table_name.category_id";

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
	$_SESSION['save'][$page_name]['se_category'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
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
					if ($options["ENABLE_REFERER_FIELD_{$i}"] == 1)
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

if ($_SESSION['save'][$page_name]['se_category'] != '')
{
	$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $_SESSION['save'][$page_name]['se_category']));
	$where .= " and $table_name.category_id=$category_id";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'category')
{
	$sort_by = "$config[tables_prefix]categories.title";
} else {
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

	$allowed_file_ext = "$config[video_allowed_ext],$config[image_allowed_ext]";
	if ($config['other_allowed_ext'] <> '')
	{
		$allowed_file_ext .= ",$config[other_allowed_ext]";
	}

	validate_field('uniq', $_POST['title'], $lang['stats']['referer_field_title'], array('field_name_in_base' => 'title'));
	if (strlen($_POST['url']) <> 0)
	{
		validate_field('url', $_POST['url'], $lang['stats']['referer_field_url']);
	}
	if ($_POST['category'] <> '')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where title=?", $_POST['category'])) == 0)
		{
			$errors[] = get_aa_error('invalid_category', $lang['stats']['referer_field_category']);
		}
	}
	validate_field('uniq', $_POST['referer'], $lang['stats']['referer_field_referer'], array('field_name_in_base' => 'referer'));

	validate_field('file', 'custom_file1', $options['REFERER_FILE_FIELD_1_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file2', $options['REFERER_FILE_FIELD_2_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file3', $options['REFERER_FILE_FIELD_3_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));

	$post_file_fields = array('custom_file1' => 'c1_', 'custom_file2' => 'c2_', 'custom_file3' => 'c3_');
	foreach ($post_file_fields as $k => $v)
	{
		if ($_POST["{$k}_hash"] <> '')
		{
			$_POST[$k] = "{$v}$_POST[$k]";
		}
	}

	if (!is_array($errors))
	{
		$_POST['category_id'] = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", nvl($_POST['category'])));

		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, description=?, url=?, category_id=?, referer=?, custom1=?, custom2=?, custom3=?, custom_file1=?, custom_file2=?, custom_file3=?, added_date=?",
				$_POST['title'], $_POST['description'], $_POST['url'], $_POST['category_id'], $_POST['referer'], $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom_file1'], $_POST['custom_file2'], $_POST['custom_file3'], date("Y-m-d H:i:s")
			);

			transfer_uploaded_file('custom_file1', "$config[content_path_referers]/$item_id/$_POST[custom_file1]");
			transfer_uploaded_file('custom_file2', "$config[content_path_referers]/$item_id/$_POST[custom_file2]");
			transfer_uploaded_file('custom_file3', "$config[content_path_referers]/$item_id/$_POST[custom_file3]");

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_POST['item_id'])));

			sql_pr("update $table_name set title=?, description=?, url=?, category_id=?, referer=?, custom1=?, custom2=?, custom3=? where $table_key_name=?",
				$_POST['title'], $_POST['description'], $_POST['url'], $_POST['category_id'], $_POST['referer'], $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $item_id
			);

			if ($_POST['custom_file1_hash'] <> '')
			{
				$old_file = $old_data['custom_file1'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file1', "$config[content_path_referers]/$item_id/$_POST[custom_file1]");
				sql_pr("update $table_name set custom_file1=? where $table_key_name=?", $_POST['custom_file1'], $item_id);
			} elseif ($_POST['custom_file1'] == '')
			{
				$old_file = $old_data['custom_file1'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file1='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file2_hash'] <> '')
			{
				$old_file = $old_data['custom_file2'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file2', "$config[content_path_referers]/$item_id/$_POST[custom_file2]");
				sql_pr("update $table_name set custom_file2=? where $table_key_name=?", $_POST['custom_file2'], $item_id);
			} elseif ($_POST['custom_file2'] == '')
			{
				$old_file = $old_data['custom_file2'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file2='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file3_hash'] <> '')
			{
				$old_file = $old_data['custom_file3'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file3', "$config[content_path_referers]/$item_id/$_POST[custom_file3]");
				sql_pr("update $table_name set custom_file3=? where $table_key_name=?", $_POST['custom_file3'], $item_id);
			} elseif ($_POST['custom_file3'] == '')
			{
				$old_file = $old_data['custom_file3'];
				if (is_file("$config[content_path_referers]/$item_id/$old_file"))
				{
					unlink("$config[content_path_referers]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file3='' where $table_key_name=?", $item_id);
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
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		$data = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
		foreach ($data as $k => $v)
		{
			if (is_file("$config[content_path_referers]/$v[referer_id]/$v[custom_file1]"))
			{
				@unlink("$config[content_path_referers]/$v[referer_id]/$v[custom_file1]");
			}
			if (is_file("$config[content_path_referers]/$v[referer_id]/$v[custom_file2]"))
			{
				@unlink("$config[content_path_referers]/$v[referer_id]/$v[custom_file2]");
			}
			if (is_file("$config[content_path_referers]/$v[referer_id]/$v[custom_file3]"))
			{
				@unlink("$config[content_path_referers]/$v[referer_id]/$v[custom_file3]");
			}
			if (is_dir("$config[content_path_referers]/$v[referer_id]"))
			{
				@rmdir("$config[content_path_referers]/$v[referer_id]");
			}
		}
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("update $config[tables_prefix_multi]stats_in set referer_id=0 where $table_key_name in ($row_select)");
		sql("update $config[tables_prefix_multi]stats_cs_out set referer_id=0 where $table_key_name in ($row_select)");
		sql("update $config[tables_prefix_multi]stats_adv_out set referer_id=0 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
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

	if (intval($_POST['category_id']) > 0)
	{
		$_POST['category'] = mr2array_single(sql_pr("select * from $config[tables_prefix]categories where category_id=?", intval($_POST['category_id'])));
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

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');
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
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['stats']['referer_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['stats']['referer_add']);
} else
{
	$smarty->assign('page_title', $lang['stats']['submenu_option_referers_list']);
}

$smarty->display("layout.tpl");
