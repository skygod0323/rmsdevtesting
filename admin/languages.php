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

$list_translation_scope_values = array(
	'' => $lang['settings']['language_field_scope_all'],
	0 => $lang['settings']['language_field_scope_all'],
	1 => $lang['settings']['language_field_scope_title_only'],
);

$table_fields = array();
$table_fields[] = array('id' => 'language_id',                              'title' => $lang['settings']['language_field_id'],                   'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                                    'title' => $lang['settings']['language_field_title'],                'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'code',                                     'title' => $lang['settings']['language_field_code'],                 'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'translation_scope_videos',                 'title' => $lang['common']['object_type_videos'],                    'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_albums',                 'title' => $lang['common']['object_type_albums'],                    'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_content_sources',        'title' => $lang['common']['object_type_content_sources'],           'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_content_sources_groups', 'title' => $lang['common']['object_type_content_source_groups'],     'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_models',                 'title' => $lang['common']['object_type_models'],                    'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_models_groups',          'title' => $lang['common']['object_type_model_groups'],              'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_dvds',                   'title' => $lang['common']['object_type_dvds'],                      'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_dvds_groups',            'title' => $lang['common']['object_type_dvd_groups'],                'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_categories',             'title' => $lang['common']['object_type_categories'],                'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'translation_scope_categories_groups',      'title' => $lang['common']['object_type_category_groups'],           'is_default' => 0, 'type' => 'choice', 'values' => $list_translation_scope_values);
$table_fields[] = array('id' => 'is_directories_localize',                  'title' => $lang['settings']['language_field_directories_localize'], 'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'is_directories_translit',                  'title' => $lang['settings']['language_field_directories_translit'], 'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'added_date',                               'title' => $lang['settings']['language_field_added_date'],           'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "language_id";
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

$table_name="$config[tables_prefix]languages";
$table_key_name="language_id";

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
	$where .= " and ($table_key_name='$q' or title like '%$q%' or code like '%$q%' or directories_translit_rules like '%$q%') ";
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

	validate_field('uniq', $_POST['title'], $lang['settings']['language_field_title'], array('field_name_in_base' => 'title'));
	if ($_POST['action'] == 'add_new_complete')
	{
		if (validate_field('uniq', $_POST['code'], $lang['settings']['language_field_code'], array('field_name_in_base' => 'code')))
		{
			validate_field('external_id', $_POST['code'], $lang['settings']['language_field_code']);
		}
	}

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);
		$code = strtolower($_POST['code']);

		languages_add_columns_to($code, "$config[tables_prefix]tags", 1);
		languages_add_columns_to($code, "$config[tables_prefix]categories", 0);
		languages_add_columns_to($code, "$config[tables_prefix]categories_groups", 0);
		languages_add_columns_to($code, "$config[tables_prefix]models", 0);
		languages_add_columns_to($code, "$config[tables_prefix]models_groups", 0);
		languages_add_columns_to($code, "$config[tables_prefix]content_sources", 0);
		languages_add_columns_to($code, "$config[tables_prefix]content_sources_groups", 0);
		languages_add_columns_to($code, "$config[tables_prefix]dvds", 0);
		languages_add_columns_to($code, "$config[tables_prefix]dvds_groups", 0);
		languages_add_columns_to($code, "$config[tables_prefix]videos", 0);
		languages_add_columns_to($code, "$config[tables_prefix]albums", 0);

		if (mr2rows(sql("show index from `$config[tables_prefix]videos` where Key_name='title_$code'")) == 0)
		{
			sql("alter table `$config[tables_prefix]videos` add fulltext `title_$code` (`title_$code`,`description_$code`)");
		}
		if (mr2rows(sql("show index from `$config[tables_prefix]videos` where Key_name='title_related_$code'")) == 0)
		{
			sql("alter table `$config[tables_prefix]videos` add fulltext `title_related_$code` (`title_$code`)");
		}
		if (mr2rows(sql("show index from `$config[tables_prefix]albums` where Key_name='title_$code'")) == 0)
		{
			sql("alter table `$config[tables_prefix]albums` add fulltext `title_$code` (`title_$code`,`description_$code`)");
		}
		if (mr2rows(sql("show index from `$config[tables_prefix]albums` where Key_name='title_related_$code'")) == 0)
		{
			sql("alter table `$config[tables_prefix]albums` add fulltext `title_related_$code` (`title_$code`)");
		}

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_permissions where title=?", "localization|$code")) == 0)
		{
			sql_pr("insert into $config[tables_prefix]admin_permissions set title=?, sort_id=2, group_sort_id=2300", "localization|$code");
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, code=?, translation_scope_videos=?, translation_scope_albums=?, translation_scope_content_sources=?, translation_scope_content_sources_groups=?, translation_scope_models=?, translation_scope_models_groups=?, translation_scope_dvds=?, translation_scope_dvds_groups=?, translation_scope_categories=?, translation_scope_categories_groups=?, is_directories_localize=?, is_directories_translit=?, directories_translit_rules=?, added_date=?",
					$_POST['title'], $code, intval($_POST['translation_scope_videos']), intval($_POST['translation_scope_albums']), intval($_POST['translation_scope_content_sources']), intval($_POST['translation_scope_content_sources_groups']), intval($_POST['translation_scope_models']), intval($_POST['translation_scope_models_groups']), intval($_POST['translation_scope_dvds']), intval($_POST['translation_scope_dvds_groups']), intval($_POST['translation_scope_categories']), intval($_POST['translation_scope_categories_groups']), intval($_POST['is_directories_localize']), intval($_POST['is_directories_translit']), $_POST['directories_translit_rules'], date("Y-m-d H:i:s")
			);
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, translation_scope_videos=?, translation_scope_albums=?, translation_scope_content_sources=?, translation_scope_content_sources_groups=?, translation_scope_models=?, translation_scope_models_groups=?, translation_scope_dvds=?, translation_scope_dvds_groups=?, translation_scope_categories=?, translation_scope_categories_groups=?, is_directories_localize=?, is_directories_translit=?, directories_translit_rules=? where $table_key_name=?",
				$_POST['title'], intval($_POST['translation_scope_videos']), intval($_POST['translation_scope_albums']), intval($_POST['translation_scope_content_sources']), intval($_POST['translation_scope_content_sources_groups']), intval($_POST['translation_scope_models']), intval($_POST['translation_scope_models_groups']), intval($_POST['translation_scope_dvds']), intval($_POST['translation_scope_dvds_groups']), intval($_POST['translation_scope_categories']), intval($_POST['translation_scope_categories_groups']), intval($_POST['is_directories_localize']), intval($_POST['is_directories_translit']), $_POST['directories_translit_rules'], $item_id);
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
		$language_codes = mr2array_list(sql("select code from $table_name where $table_key_name in ($row_select)"));
		foreach ($language_codes as $code)
		{
			sql_pr("delete from $config[tables_prefix]admin_permissions where title=?", "localization|$code");

			languages_delete_columns_from($code, "$config[tables_prefix]tags", 1);
			languages_delete_columns_from($code, "$config[tables_prefix]categories", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]categories_groups", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]models", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]models_groups", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]content_sources", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]content_sources_groups", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]dvds", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]dvds_groups", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]videos", 0);
			languages_delete_columns_from($code, "$config[tables_prefix]albums", 0);

			if (mr2rows(sql("show index from `$config[tables_prefix]videos` where Key_name='title_$code'")) >= 1)
			{
				sql("alter table `$config[tables_prefix]videos` drop index `title_$code`");
			}
			if (mr2rows(sql("show index from `$config[tables_prefix]videos` where Key_name='title_related_$code'")) >= 1)
			{
				sql("alter table `$config[tables_prefix]videos` drop index `title_related_$code`");
			}
			if (mr2rows(sql("show index from `$config[tables_prefix]albums` where Key_name='title_$code'")) >= 1)
			{
				sql("alter table `$config[tables_prefix]albums` drop index `title_$code`");
			}
			if (mr2rows(sql("show index from `$config[tables_prefix]albums` where Key_name='title_related_$code'")) >= 1)
			{
				sql("alter table `$config[tables_prefix]albums` drop index `title_related_$code`");
			}
		}
		sql("delete from $table_name where $table_key_name in ($row_select)");
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
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
$data = mr2array(sql("select * from $table_name $where order by $sort_by"));
$data[] = array(
	'title' => $lang['settings']['language_field_title_default'],
	'code' => '-',
	'is_editing_forbidden' => 1,
);

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_options.tpl');

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
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['language_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['language_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_languages_list']);
}

$smarty->display("layout.tpl");

function languages_add_columns_to($language, $table_name, $is_tags)
{
	if ($is_tags == 0)
	{
		if (mr2rows(sql("show columns from `$table_name` like '%_$language'")) == 0)
		{
			sql("alter table `$table_name` add column `title_$language` varchar(255) not null, add column `description_$language` text not null, add column `dir_$language` varchar(255) not null");
		} else
		{
			if (mr2rows(sql("show columns from `$table_name` like 'title_$language'")) == 0)
			{
				sql("alter table `$table_name` add column `title_$language` varchar(255) not null");
			}
			if (mr2rows(sql("show columns from `$table_name` like 'description_$language'")) == 0)
			{
				sql("alter table `$table_name` add column `description_$language` text not null");
			}
			if (mr2rows(sql("show columns from `$table_name` like 'dir_$language'")) == 0)
			{
				sql("alter table `$table_name` add column `dir_$language` varchar(255) not null");
			}
		}
		if (mr2rows(sql("show index from `$table_name` where Key_name='dir_$language'")) == 0)
		{
			sql("alter table `$table_name` add index `dir_$language` (`dir_$language`)");
		}
	} else
	{
		if (mr2rows(sql("show columns from `$table_name` like '%_$language'")) == 0)
		{
			sql("alter table `$table_name` add column `tag_$language` varchar(150) not null, add column `tag_dir_$language` varchar(150) not null");
		} else
		{
			if (mr2rows(sql("show columns from `$table_name` like 'tag_$language'")) == 0)
			{
				sql("alter table `$table_name` add column `tag_$language` varchar(150) not null");
			}
			if (mr2rows(sql("show columns from `$table_name` like 'tag_dir_$language'")) == 0)
			{
				sql("alter table `$table_name` add column `tag_dir_$language` varchar(150) not null");
			}
		}
		if (mr2rows(sql("show index from `$table_name` where Key_name='tag_dir_$language'")) == 0)
		{
			sql("alter table `$table_name` add index `tag_dir_$language` (`tag_dir_$language`)");
		}
	}
}

function languages_delete_columns_from($language, $table_name, $is_tags)
{
	if ($is_tags == 0)
	{
		if (mr2rows(sql("show index from `$table_name` where Key_name='dir_$language'")) >= 1)
		{
			sql("alter table `$table_name` drop index `dir_$language`");
		}
	} else
	{
		if (mr2rows(sql("show index from `$table_name` where Key_name='tag_dir_$language'")) >= 1)
		{
			sql("alter table `$table_name` drop index `tag_dir_$language`");
		}
	}
}
