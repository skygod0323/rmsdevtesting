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
	if ($options["DVD_GROUP_FIELD_{$i}_NAME"] == '')
	{
		$options["DVD_GROUP_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['videos']['dvd_group_field_status_disabled'],
	1 => $lang['videos']['dvd_group_field_status_active'],
);

$table_fields = array();
$table_fields[] = array('id' => 'dvd_group_id', 'title' => $lang['videos']['dvd_group_field_id'],          'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'thumb',        'title' => $lang['videos']['dvd_group_field_thumb'],       'is_default' => 0, 'type' => 'thumb');
$table_fields[] = array('id' => 'title',        'title' => $lang['videos']['dvd_group_field_title'],       'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',          'title' => $lang['videos']['dvd_group_field_directory'],   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',  'title' => $lang['videos']['dvd_group_field_description'], 'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'external_id',  'title' => $lang['videos']['dvd_group_field_external_id'], 'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'status_id',    'title' => $lang['videos']['dvd_group_field_status'],      'is_default' => 0, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'cover1',       'title' => $lang['videos']['dvd_group_field_cover1'],      'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'cover2',       'title' => $lang['videos']['dvd_group_field_cover2'],      'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'tags',         'title' => $lang['videos']['dvd_group_field_tags'],        'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view');
$table_fields[] = array('id' => 'categories',   'title' => $lang['videos']['dvd_group_field_categories'],  'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'models',       'title' => $lang['videos']['dvd_group_field_models'],      'is_default' => 0, 'type' => 'list', 'link' => 'models.php?action=change&item_id=%id%', 'permission' => 'models|view');

for ($i = 1; $i <= 10; $i++)
{
	if ($options["ENABLE_DVD_GROUP_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["DVD_GROUP_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'dvds_amount',  'title' => $lang['videos']['dvd_group_field_dvds'],        'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'link' => 'dvds.php?no_filter=true&se_dvd_group_id=%id%', 'link_id' => 'dvd_group_id', 'permission' => 'dvds|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',   'title' => $lang['videos']['dvd_group_field_added_date'],  'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'sort_id',      'title' => $lang['videos']['dvd_group_field_order'],       'is_default' => 1, 'type' => 'sorting');

$sort_def_field = "dvd_group_id";
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
$search_fields[] = array('id' => 'dvd_group_id', 'title' => $lang['videos']['dvd_group_field_id']);
$search_fields[] = array('id' => 'title',        'title' => $lang['videos']['dvd_group_field_title']);
$search_fields[] = array('id' => 'dir',          'title' => $lang['videos']['dvd_group_field_directory']);
$search_fields[] = array('id' => 'description',  'title' => $lang['videos']['dvd_group_field_description']);
$search_fields[] = array('id' => 'external_id',  'title' => $lang['videos']['dvd_group_field_external_id']);
$search_fields[] = array('id' => 'custom',       'title' => $lang['common']['dg_filter_search_in_custom']);
$search_fields[] = array('id' => 'filenames',    'title' => $lang['common']['dg_filter_search_in_filenames']);
if (count($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name = "$config[tables_prefix]dvds_groups";
$table_key_name = "dvd_group_id";

$table_selector_dvds_count = "(select count(*) from $config[tables_prefix]dvds where $table_key_name=$table_name.$table_key_name)";
$table_selector = "$table_name.*, $table_selector_dvds_count as dvds_amount";
$table_selector_single = $table_selector;

$table_projector = "$table_name";

$table_name_categories = "$config[tables_prefix]categories_dvds_groups";
$table_name_tags = "$config[tables_prefix]tags_dvds_groups";
$table_name_models = "$config[tables_prefix]models_dvds_groups";
$column_name_total = "total_dvd_groups";

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

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
	$_SESSION['save'][$page_name]['se_category'] = '';
	$_SESSION['save'][$page_name]['se_model'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
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
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_usage'] = trim($_GET['se_usage']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
	}
	if (isset($_GET['se_model']))
	{
		$_SESSION['save'][$page_name]['se_model'] = trim($_GET['se_model']);
	}
	if (isset($_GET['se_tag']))
	{
		$_SESSION['save'][$page_name]['se_tag'] = trim($_GET['se_tag']);
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
					if ($options["ENABLE_DVD_GROUP_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'filenames')
			{
				$where_search .= " or $table_name.cover1 like '%$q%'";
				$where_search .= " or $table_name.cover2 like '%$q%'";
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

if ($_SESSION['save'][$page_name]['se_tag'] != '')
{
	$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?", $_SESSION['save'][$page_name]['se_tag']));
	$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name and tag_id=$tag_id)";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_category'] != '')
{
	$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $_SESSION['save'][$page_name]['se_category']));
	$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name and category_id=$category_id)";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_model'] != '')
{
	$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $_SESSION['save'][$page_name]['se_model']));
	$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name and model_id=$model_id)";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/description':
	case 'empty/cover1':
	case 'empty/cover2':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
	case 'empty/custom4':
	case 'empty/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/tags':
		$where .= " and not exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'empty/categories':
		$where .= " and not exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'empty/models':
		$where .= " and not exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/description':
	case 'filled/cover1':
	case 'filled/cover2':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
	case 'filled/custom4':
	case 'filled/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/tags':
		$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/categories':
		$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/models':
		$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_usage'])
{
	case 'used/dvds':
		$where .= " and exists (select dvd_id from $config[tables_prefix]dvds where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'notused/dvds':
		$where .= " and not exists (select dvd_id from $config[tables_prefix]dvds where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'dvds_amount')
{
	$sort_by = "$table_selector_dvds_count";
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

	$validate_screenshot1 = 'min_image_size';
	$resize_screenshot1 = $options['DVD_GROUP_COVER_1_TYPE'];
	switch ($resize_screenshot1)
	{
		case 'max_size':
			$validate_screenshot1 = 'min_image_width_or_height';
			break;
		case 'max_width':
			$validate_screenshot1 = 'min_image_width';
			break;
		case 'max_height':
			$validate_screenshot1 = 'min_image_height';
			break;
	}
	if (!in_array($resize_screenshot1, array('need_size', 'max_size', 'max_width', 'max_height')))
	{
		$resize_screenshot1 = 'need_size';
	}

	$validate_screenshot2 = 'min_image_size';
	$resize_screenshot2 = $options['DVD_GROUP_COVER_2_TYPE'];
	switch ($resize_screenshot2)
	{
		case 'max_size':
			$validate_screenshot2 = 'min_image_width_or_height';
			break;
		case 'max_width':
			$validate_screenshot2 = 'min_image_width';
			break;
		case 'max_height':
			$validate_screenshot2 = 'min_image_height';
			break;
	}
	if (!in_array($resize_screenshot2, array('need_size', 'max_size', 'max_width', 'max_height')))
	{
		$resize_screenshot2 = 'need_size';
	}

	validate_field('uniq', $_POST['title'], $lang['videos']['dvd_group_field_title'], array('field_name_in_base' => 'title'));
	if ($_POST['external_id'])
	{
		validate_field('uniq', $_POST['external_id'], $lang['videos']['dvd_group_field_external_id'], array('field_name_in_base' => 'external_id'));
	}
	validate_field('file', 'cover1', $lang['videos']['dvd_group_field_cover1'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_screenshot1 => $options['DVD_GROUP_COVER_1_SIZE'], 'strict_mode' => '1'));
	validate_field('file', 'cover2', $lang['videos']['dvd_group_field_cover2'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_screenshot2 => $options['DVD_GROUP_COVER_2_SIZE'], 'strict_mode' => '1'));

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

		if ($options['DVD_GROUP_COVER_OPTION'] == 1)
		{
			if ($_POST['cover1_hash'] <> '' && $_POST['cover2_hash'] == '')
			{
				$_POST['cover2'] = $_POST['cover1'];
				$_POST['cover2_hash'] = md5($_POST['cover1_hash']);
				@copy("$config[temporary_path]/$_POST[cover1_hash].tmp", "$config[temporary_path]/$_POST[cover2_hash].tmp");
			}
		}
		$post_file_fields = array('cover1' => 'c1_', 'cover2' => 'c2_');
		foreach ($post_file_fields as $k => $v)
		{
			if ($_POST["{$k}_hash"] <> '')
			{
				$_POST[$k] = "{$v}$_POST[$k]";
			}
		}

		$update_array = array(
			'title' => $_POST['title'],
			'dir' => $_POST['dir'],
			'description' => $_POST['description'],
			'external_id' => $_POST['external_id'],
			'status_id' => intval($_POST['status_id']),
			'custom1' => $_POST['custom1'],
			'custom2' => $_POST['custom2'],
			'custom3' => $_POST['custom3'],
			'custom4' => $_POST['custom4'],
			'custom5' => $_POST['custom5']
		);

		if (!is_dir("$config[content_path_dvds]/groups"))
		{
			mkdir("$config[content_path_dvds]/groups", 0777);
			chmod("$config[content_path_dvds]/groups", 0777);
		}
		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array['cover1'] = $_POST['cover1'];
			$update_array['cover2'] = $_POST['cover2'];
			$update_array['added_date'] = date("Y-m-d H:i:s");

			$item_id = sql_insert("insert into $table_name set ?%", $update_array);

			if ($_POST['cover1'] <> '')
			{
				transfer_uploaded_file('cover1', "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]");
				resize_image($resize_screenshot1, "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]", "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]", $options['DVD_GROUP_COVER_1_SIZE']);
			}
			if ($_POST['cover2'] <> '')
			{
				transfer_uploaded_file('cover2', "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]");
				resize_image($resize_screenshot2, "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]", "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]", $options['DVD_GROUP_COVER_2_SIZE']);
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=10, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

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

			if ($_POST['cover1_hash'] <> '')
			{
				$old_file = $old_data['cover1'];
				if (is_file("$config[content_path_dvds]/groups/$item_id/$old_file"))
				{
					unlink("$config[content_path_dvds]/groups/$item_id/$old_file");
				}
				transfer_uploaded_file('cover1', "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]");
				resize_image($resize_screenshot1, "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]", "$config[content_path_dvds]/groups/$item_id/$_POST[cover1]", $options['DVD_GROUP_COVER_1_SIZE']);
				$update_array['cover1'] = $_POST['cover1'];
			} elseif ($_POST['cover1'] == '')
			{
				$old_file = $old_data['cover1'];
				if (is_file("$config[content_path_dvds]/groups/$item_id/$old_file"))
				{
					unlink("$config[content_path_dvds]/groups/$item_id/$old_file");
				}
				$update_array['cover1'] = '';
			}
			if ($_POST['cover2_hash'] <> '')
			{
				$old_file = $old_data['cover2'];
				if (is_file("$config[content_path_dvds]/groups/$item_id/$old_file"))
				{
					unlink("$config[content_path_dvds]/groups/$item_id/$old_file");
				}
				transfer_uploaded_file('cover2', "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]");
				resize_image($resize_screenshot2, "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]", "$config[content_path_dvds]/groups/$item_id/$_POST[cover2]", $options['DVD_GROUP_COVER_2_SIZE']);
				$update_array['cover2'] = $_POST['cover2'];
			} elseif ($_POST['cover2'] == '')
			{
				$old_file = $old_data['cover2'];
				if (is_file("$config[content_path_dvds]/groups/$item_id/$old_file"))
				{
					unlink("$config[content_path_dvds]/groups/$item_id/$old_file");
				}
				$update_array['cover2'] = '';
			}

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
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=10, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name=?", $item_id)));
		sql_pr("delete from $table_name_tags where $table_key_name=?", $item_id);
		$temp = explode(",", $_POST['tags']);
		if (is_array($temp))
		{
			$temp = array_map("trim", $temp);
			$temp = array_unique($temp);
			$inserted_tags = array();
			foreach ($temp as $tag)
			{
				$tag = trim($tag);
				if (in_array(mb_lowercase($tag), $inserted_tags))
				{
					continue;
				}

				$tag_id = find_or_create_tag($tag, $options);
				if ($tag_id > 0)
				{
					sql_pr("insert into $table_name_tags set tag_id=?, $table_key_name=?", $tag_id, $item_id);
					$inserted_tags[] = mb_lowercase($tag);
					$list_ids_tags[] = $tag_id;
				}
			}
		}

		$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name=?", $item_id)));
		sql_pr("delete from $table_name_categories where $table_key_name=?", $item_id);
		settype($_POST['category_ids'], "array");
		foreach ($_POST['category_ids'] as $category_id)
		{
			if (strpos($category_id, 'new_') === 0)
			{
				$category_title = substr($category_id, 4);
				$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $category_title));
				if ($category_id == 0)
				{
					$cat_dir = get_correct_dir_name($category_title);
					$temp_dir = $cat_dir;
					for ($it = 2; $it < 999999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
						{
							$cat_dir = $temp_dir;
							break;
						}
						$temp_dir = $cat_dir . $it;
					}
					$category_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $category_title, $cat_dir, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=6, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $category_id, date("Y-m-d H:i:s"));
				}
			}
			sql_pr("insert into $table_name_categories set category_id=?, $table_key_name=?", $category_id, $item_id);
			$list_ids_categories[] = $category_id;
		}

		$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name=?", $item_id)));
		sql_pr("delete from $table_name_models where $table_key_name=?", $item_id);
		settype($_POST['model_ids'], "array");
		foreach ($_POST['model_ids'] as $model_id)
		{
			if (strpos($model_id, 'new_') === 0)
			{
				$model_title = substr($model_id, 4);
				$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
				if ($model_id == 0)
				{
					$model_dir = get_correct_dir_name($model_title);
					$temp_dir = $model_dir;
					for ($it = 2; $it < 999999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
						{
							$model_dir = $temp_dir;
							break;
						}
						$temp_dir = $model_dir . $it;
					}
					$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=4, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $model_id, date("Y-m-d H:i:s"));
				}
			}
			sql_pr("insert into $table_name_models set model_id=?, $table_key_name=?", $model_id, $item_id);
			$list_ids_models[] = $model_id;
		}

		settype($_POST['delete_dvd_ids'], "array");
		foreach ($_POST['delete_dvd_ids'] as $del_dvd_id)
		{
			sql_pr("update $config[tables_prefix]dvds set dvd_group_id=0 where dvd_id=?", intval($del_dvd_id));
		}

		$negative_positions = array();

		$list_ids_dvds = mr2array_list(sql_pr("select dvd_id from $config[tables_prefix]dvds where dvd_group_id=?", $item_id));
		foreach ($list_ids_dvds as $dvd_id)
		{
			if (isset($_REQUEST["dvd_sorting_$dvd_id"]) && intval($_REQUEST["dvd_sorting_$dvd_id"])<0)
			{
				$negative_positions[] = intval($_REQUEST["dvd_sorting_$dvd_id"]);
			}
		}
		sort($negative_positions, SORT_ASC);

		foreach ($list_ids_dvds as $dvd_id)
		{
			if (isset($_REQUEST["dvd_sorting_$dvd_id"]))
			{
				$dvd_sorting = intval($_REQUEST["dvd_sorting_$dvd_id"]);
				if ($dvd_sorting > 0)
				{
					$dvd_sorting += count($negative_positions);
				} elseif ($dvd_sorting < 0)
				{
					$dvd_sorting = intval(array_search($dvd_sorting, $negative_positions)) + 1;
				}
				sql_pr("update $config[tables_prefix]dvds set sort_id=? where dvd_id=?", $dvd_sorting, $dvd_id);
			}
		}

		$list_ids_groups = array($item_id);
		settype($_POST['add_dvd_ids'], "array");
		if (count($_POST['add_dvd_ids']) > 0)
		{
			$list_ids_add_dvds = implode(',', array_map('intval', $_POST['add_dvd_ids']));
			$list_ids_groups = array_merge($list_ids_groups, mr2array_list(sql("select dvd_group_id from $config[tables_prefix]dvds where dvd_id in ($list_ids_add_dvds)")));
		}
		foreach ($_POST['add_dvd_ids'] as $add_dvd_id)
		{
			if (strpos($add_dvd_id, 'new_') === 0)
			{
				$add_dvd_title = substr($add_dvd_id, 4);
				$add_dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $add_dvd_title));
				if ($add_dvd_id == 0)
				{
					$dvd_dir = get_correct_dir_name($add_dvd_title);
					$temp_dir = $dvd_dir;
					for ($it = 2; $it < 999999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
						{
							$dvd_dir = $temp_dir;
							break;
						}
						$temp_dir = $dvd_dir . $it;
					}
					$add_dvd_id = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $add_dvd_title, $dvd_dir, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=5, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $add_dvd_id, date("Y-m-d H:i:s"));
				}
			}
			sql_pr("update $config[tables_prefix]dvds set dvd_group_id=? where dvd_id=?", $item_id, intval($add_dvd_id));
		}

		if (count($list_ids_groups) > 0)
		{
			$list_ids_groups = implode(',', $list_ids_groups);
			sql_pr("update $config[tables_prefix]dvds_groups set total_dvds=(select count(*) from $config[tables_prefix]dvds where $config[tables_prefix]dvds.dvd_group_id=$config[tables_prefix]dvds_groups.dvd_group_id) where dvd_group_id in ($list_ids_groups)");
		}

		if (count($list_ids_categories) > 0)
		{
			$list_ids_categories = implode(',', $list_ids_categories);
			sql_pr("update $config[tables_prefix]categories set $column_name_total=(select count(*) from $table_name_categories where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
		}
		if (count($list_ids_models) > 0)
		{
			$list_ids_models = implode(',', $list_ids_models);
			sql_pr("update $config[tables_prefix]models set $column_name_total=(select count(*) from $table_name_models where model_id=$config[tables_prefix]models.model_id) where model_id in ($list_ids_models)");
		}
		if (count($list_ids_tags) > 0)
		{
			$list_ids_tags = implode(',', $list_ids_tags);
			sql_pr("update $config[tables_prefix]tags set $column_name_total=(select count(*) from $table_name_tags where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_tags)");
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
		$data = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
		foreach ($data as $k => $v)
		{
			if (is_file("$config[content_path_dvds]/groups/$v[$table_key_name]/$v[cover1]"))
			{
				@unlink("$config[content_path_dvds]/groups/$v[$table_key_name]/$v[cover1]");
			}
			if (is_file("$config[content_path_dvds]/groups/$v[$table_key_name]/$v[cover2]"))
			{
				@unlink("$config[content_path_dvds]/groups/$v[$table_key_name]/$v[cover2]");
			}
			if (is_file("$config[content_path_dvds]/groups/$v[$table_key_name]"))
			{
				@unlink("$config[content_path_dvds]/groups/$v[$table_key_name]");
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=10, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $v[$table_key_name], date("Y-m-d H:i:s"));
		}

		$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select)")));
		$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name in ($row_select)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select)")));

		sql("delete from $table_name_categories where $table_key_name in ($row_select)");
		sql("delete from $table_name_models where $table_key_name in ($row_select)");
		sql("delete from $table_name_tags where $table_key_name in ($row_select)");
		sql("delete from $table_name where $table_key_name in ($row_select)");

		sql("update $config[tables_prefix]dvds set $table_key_name=0 where $table_key_name in ($row_select)");
		if (count($list_ids_categories) > 0)
		{
			$list_ids_categories = implode(',', $list_ids_categories);
			sql_pr("update $config[tables_prefix]categories set $column_name_total=(select count(*) from $table_name_categories where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
		}
		if (count($list_ids_models) > 0)
		{
			$list_ids_models = implode(',', $list_ids_models);
			sql_pr("update $config[tables_prefix]models set $column_name_total=(select count(*) from $table_name_models where model_id=$config[tables_prefix]models.model_id) where model_id in ($list_ids_models)");
		}
		if (count($list_ids_tags) > 0)
		{
			$list_ids_tags = implode(',', $list_ids_tags);
			sql_pr("update $config[tables_prefix]tags set $column_name_total=(select count(*) from $table_name_tags where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_tags)");
		}

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

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = 1;
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector_single from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP'] <> '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
	}

	$_POST['dvds'] = mr2array(sql_pr("select *, rating/rating_amount as rating, (select count(*) from $config[tables_prefix]videos where $config[tables_prefix]videos.dvd_id=$config[tables_prefix]dvds.dvd_id) as total_videos from $config[tables_prefix]dvds where $table_key_name=$_POST[$table_key_name] order by sort_id asc"));
	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=$_POST[$table_key_name] order by id asc"));
	$_POST['models'] = mr2array(sql_pr("select model_id, (select title from $config[tables_prefix]models where model_id=$table_name_models.model_id) as title from $table_name_models where $table_key_name=$_POST[$table_key_name] order by id asc"));
	$_POST['tags'] = implode(", ", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as tag from $table_name_tags where $table_key_name=$_POST[$table_key_name] order by id asc")));
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

	foreach ($data as $k => $v)
	{
		if ($data[$k]['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP'] <> '')
		{
			$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
		}

		if ($_SESSION['save'][$page_name]['grid_columns']['categories'] == 1)
		{
			$data[$k]['categories'] = mr2array(sql_pr("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories inner join $table_name_categories on $config[tables_prefix]categories.category_id=$table_name_categories.category_id where $table_name_categories.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_categories.id asc"));
		}
		if ($_SESSION['save'][$page_name]['grid_columns']['tags'] == 1)
		{
			$data[$k]['tags'] = mr2array(sql_pr("select $config[tables_prefix]tags.tag_id as id, $config[tables_prefix]tags.tag as title from $config[tables_prefix]tags inner join $table_name_tags on $config[tables_prefix]tags.tag_id=$table_name_tags.tag_id where $table_name_tags.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_tags.id asc"));
		}
		if ($_SESSION['save'][$page_name]['grid_columns']['models'] == 1)
		{
			$data[$k]['models'] = mr2array(sql_pr("select $config[tables_prefix]models.model_id as id, $config[tables_prefix]models.title from $config[tables_prefix]models inner join $table_name_models on $config[tables_prefix]models.model_id=$table_name_models.model_id where $table_name_models.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_models.id asc"));
		}

		if ($v["cover1"])
		{
			$data[$k]["cover1_url"] = "$config[content_url_dvds]/groups/$v[$table_key_name]/$v[cover1]";
		}
		if ($v["cover2"])
		{
			$data[$k]["cover2_url"] = "$config[content_url_dvds]/groups/$v[$table_key_name]/$v[cover2]";
		}

		$thumb_field = 'cover1';
		if ($options['DVD_GROUP_COVER_OPTION'] > 0)
		{
			$image_size1 = explode('x', $options['DVD_GROUP_COVER_1_SIZE']);
			$image_size2 = explode('x', $options['DVD_GROUP_COVER_2_SIZE']);
			if (($image_size1[0] > $image_size2[0] || !$v["cover1"]) && $v["cover2"])
			{
				$thumb_field = 'cover2';
			}
		}
		if ($v[$thumb_field])
		{
			$data[$k]['thumb'] = "$config[content_url_dvds]/groups/$v[$table_key_name]/$v[$thumb_field]";
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_videos.tpl');
$smarty->assign('options', $options);
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
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
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['videos']['dvd_group_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['videos']['dvd_group_add']);
} else
{
	$smarty->assign('page_title', $lang['videos']['submenu_option_dvd_groups_list']);
}

$content_scheduler_days=intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days='';
	$sorting_content_scheduler_days='desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$now_date=date("Y-m-d H:i:s");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days='asc';
	}
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
