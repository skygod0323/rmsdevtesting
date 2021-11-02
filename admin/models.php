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

for ($i = 1; $i <= 10; $i++)
{
	if ($options["MODEL_FIELD_{$i}_NAME"] == '')
	{
		$options["MODEL_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}
for ($i = 1; $i <= 5; $i++)
{
	if ($options["MODEL_FILE_FIELD_{$i}_NAME"] == '')
	{
		$options["MODEL_FILE_FIELD_{$i}_NAME"] = $lang['settings']["custom_file_field_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['categorization']['model_field_status_disabled'],
	1 => $lang['categorization']['model_field_status_active'],
);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[0] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_id']] = $country['title'];
}

$list_gender_values = array(
	0 => $lang['categorization']['model_field_gender_female'],
	1 => $lang['categorization']['model_field_gender_male'],
	2 => $lang['categorization']['model_field_gender_other'],
);

$list_hair_values = array(
	0 => ' ',
	1 => $lang['categorization']['model_field_hair_black'],
	2 => $lang['categorization']['model_field_hair_dark'],
	3 => $lang['categorization']['model_field_hair_red'],
	4 => $lang['categorization']['model_field_hair_brown'],
	5 => $lang['categorization']['model_field_hair_blond'],
	6 => $lang['categorization']['model_field_hair_grey'],
	7 => $lang['categorization']['model_field_hair_bald'],
	8 => $lang['categorization']['model_field_hair_wig'],
);

$list_eye_color_values = array(
	0 => ' ',
	1 => $lang['categorization']['model_field_eye_color_blue'],
	2 => $lang['categorization']['model_field_eye_color_gray'],
	3 => $lang['categorization']['model_field_eye_color_green'],
	4 => $lang['categorization']['model_field_eye_color_amber'],
	5 => $lang['categorization']['model_field_eye_color_brown'],
	6 => $lang['categorization']['model_field_eye_color_hazel'],
	7 => $lang['categorization']['model_field_eye_color_black'],
);

$list_access_level_values = array(
	0 => $lang['categorization']['model_field_access_level_any'],
	1 => $lang['categorization']['model_field_access_level_member'],
	2 => $lang['categorization']['model_field_access_level_premium'],
);

$list_age_values = array(
	0 => ' ',
);

$table_fields = array();
$table_fields[] = array('id' => 'model_id',        'title' => $lang['categorization']['model_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'thumb',           'title' => $lang['categorization']['model_field_thumb'],          'is_default' => 0, 'type' => 'thumb');
$table_fields[] = array('id' => 'title',           'title' => $lang['categorization']['model_field_title'],          'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',             'title' => $lang['categorization']['model_field_directory'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',     'title' => $lang['categorization']['model_field_description'],    'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'alias',           'title' => $lang['categorization']['model_field_alias'],          'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'status_id',       'title' => $lang['categorization']['model_field_status'],         'is_default' => 0, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'screenshot1',     'title' => $lang['categorization']['model_field_screenshot1'],    'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'screenshot2',     'title' => $lang['categorization']['model_field_screenshot2'],    'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'model_group',     'title' => $lang['categorization']['model_field_group'],          'is_default' => 1, 'type' => 'refid', 'link' => 'models_groups.php?action=change&item_id=%id%', 'link_id' => 'model_group_id', 'permission' => 'models_groups|view');
$table_fields[] = array('id' => 'rating',          'title' => $lang['categorization']['model_field_rating'],         'is_default' => 1, 'type' => 'float', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'model_viewed',    'title' => $lang['categorization']['model_field_visits'],         'is_default' => 1, 'type' => 'traffic', 'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'access_level_id', 'title' => $lang['categorization']['model_field_access_level'],   'is_default' => 0, 'type' => 'choice', 'values' => $list_access_level_values);
$table_fields[] = array('id' => 'country_id',      'title' => $lang['categorization']['model_field_country'],        'is_default' => 0, 'type' => 'choice', 'values' => $list_country_values);
$table_fields[] = array('id' => 'city',            'title' => $lang['categorization']['model_field_city'],           'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'state',           'title' => $lang['categorization']['model_field_state'],          'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'gender_id',       'title' => $lang['categorization']['model_field_gender'],         'is_default' => 0, 'type' => 'choice', 'values' => $list_gender_values);
$table_fields[] = array('id' => 'height',          'title' => $lang['categorization']['model_field_height'],         'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'weight',          'title' => $lang['categorization']['model_field_weight'],         'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'hair_id',         'title' => $lang['categorization']['model_field_hair'],           'is_default' => 0, 'type' => 'choice', 'values' => $list_hair_values);
$table_fields[] = array('id' => 'eye_color_id',    'title' => $lang['categorization']['model_field_eye_color'],      'is_default' => 0, 'type' => 'choice', 'values' => $list_eye_color_values);
$table_fields[] = array('id' => 'measurements',    'title' => $lang['categorization']['model_field_measurements'],   'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'birth_date',      'title' => $lang['categorization']['model_field_birth_date'],     'is_default' => 0, 'type' => 'date');
$table_fields[] = array('id' => 'death_date',      'title' => $lang['categorization']['model_field_death_date'],     'is_default' => 0, 'type' => 'date');
$table_fields[] = array('id' => 'age',             'title' => $lang['categorization']['model_field_age'],            'is_default' => 0, 'type' => 'choice', 'values' => $list_age_values);
$table_fields[] = array('id' => 'tags',            'title' => $lang['categorization']['model_field_tags'],           'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view');
$table_fields[] = array('id' => 'categories',      'title' => $lang['categorization']['model_field_categories'],     'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');

for ($i = 1; $i <= 10; $i++)
{
	if ($options["ENABLE_MODEL_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["MODEL_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}
for ($i = 1; $i <= 5; $i++)
{
	if ($options["ENABLE_MODEL_FILE_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom_file{$i}", 'title' => $options["MODEL_FILE_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'file');
	}
}

$table_fields[] = array('id' => 'videos_amount',      'title' => $lang['categorization']['model_field_videos'],      'is_default' => 1, 'type' => 'number',   'show_in_sidebar' => 1, 'link' => 'videos.php?no_filter=true&se_model=%id%', 'link_id' => 'title', 'permission' => 'videos|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'albums_amount',      'title' => $lang['categorization']['model_field_albums'],      'is_default' => 1, 'type' => 'number',   'show_in_sidebar' => 1, 'link' => 'albums.php?no_filter=true&se_model=%id%', 'link_id' => 'title', 'permission' => 'albums|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'posts_amount',       'title' => $lang['categorization']['model_field_posts'],       'is_default' => 1, 'type' => 'number',   'show_in_sidebar' => 1, 'link' => 'posts.php?no_filter=true&se_model=%id%', 'link_id' => 'title', 'permission' => 'posts|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'other_amount',       'title' => $lang['categorization']['model_field_other'],       'is_default' => 1, 'type' => 'number',   'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'all_amount',         'title' => $lang['categorization']['model_field_all'],         'is_default' => 1, 'type' => 'number',   'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'comments_amount',    'title' => $lang['categorization']['model_field_comments'],    'is_default' => 0, 'type' => 'number',   'show_in_sidebar' => 1, 'link' => 'comments.php?no_filter=true&se_object_type_id=4&se_object_id=%id%', 'link_id' => 'model_id', 'permission' => 'users|manage_comments', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'subscribers_amount', 'title' => $lang['categorization']['model_field_subscribers'], 'is_default' => 0, 'type' => 'number',   'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',         'title' => $lang['categorization']['model_field_added_date'],  'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'rank',               'title' => $lang['categorization']['model_field_rank'],        'is_default' => 0, 'type' => 'text',     'show_in_sidebar' => 1);
$table_fields[] = array('id' => 'sort_id',            'title' => $lang['categorization']['model_field_order'],       'is_default' => 1, 'type' => 'sorting');

$sort_def_field = "model_id";
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
$search_fields[] = array('id' => 'model_id',    'title' => $lang['categorization']['model_field_id']);
$search_fields[] = array('id' => 'title',       'title' => $lang['categorization']['model_field_title']);
$search_fields[] = array('id' => 'dir',         'title' => $lang['categorization']['model_field_directory']);
$search_fields[] = array('id' => 'description', 'title' => $lang['categorization']['model_field_description']);
$search_fields[] = array('id' => 'alias',       'title' => $lang['categorization']['model_field_alias']);
$search_fields[] = array('id' => 'custom',      'title' => $lang['common']['dg_filter_search_in_custom']);
$search_fields[] = array('id' => 'filenames',   'title' => $lang['common']['dg_filter_search_in_filenames']);
if (count($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name = "$config[tables_prefix]models";
$table_key_name = "model_id";

$table_selector_videos_count = "(select count(*) from $config[tables_prefix]models_videos where $table_key_name=$table_name.$table_key_name)";
$table_selector_albums_count = "(select count(*) from $config[tables_prefix]models_albums where $table_key_name=$table_name.$table_key_name)";
$table_selector_posts_count = "(select count(*) from $config[tables_prefix]models_posts where $table_key_name=$table_name.$table_key_name)";
$table_selector_other_count = "($table_name.total_dvds + $table_name.total_dvd_groups)";
$table_selector_all_count = "($table_selector_videos_count + $table_selector_albums_count + $table_selector_posts_count + $table_selector_other_count)";
$table_selector_comments_count = "(select count(*) from $config[tables_prefix]comments where object_type_id=4 and object_id=$table_name.$table_key_name)";
$table_selector_subscribers_count = "$table_name.subscribers_count";

$table_selector = "$table_name.*, $table_name.rating / $table_name.rating_amount as rating, $config[tables_prefix]models_groups.title as model_group";
$table_selector_single = "$table_selector, $table_selector_videos_count as videos_amount, $table_selector_albums_count as albums_amount, $table_selector_posts_count as posts_amount, $table_selector_other_count as other_amount, $table_selector_all_count as all_amount, $table_selector_comments_count as comments_amount, $table_selector_subscribers_count as subscribers_amount";

$table_projector = "$table_name left join $config[tables_prefix]models_groups on $table_name.model_group_id=$config[tables_prefix]models_groups.model_group_id";

$table_name_categories = "$config[tables_prefix]categories_models";
$table_name_tags = "$config[tables_prefix]tags_models";
$column_name_total = "total_models";

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
	$_SESSION['save'][$page_name]['se_model_group_id'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
	$_SESSION['save'][$page_name]['se_category'] = '';
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
	if (isset($_GET['se_model_group_id']))
	{
		$_SESSION['save'][$page_name]['se_model_group_id'] = intval($_GET['se_model_group_id']);
	}
	if (isset($_GET['se_tag']))
	{
		$_SESSION['save'][$page_name]['se_tag'] = trim($_GET['se_tag']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
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
					if ($options["ENABLE_MODEL_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'filenames')
			{
				$where_search .= " or $table_name.screenshot1 like '%$q%'";
				$where_search .= " or $table_name.screenshot2 like '%$q%'";
				if (intval($_SESSION['save'][$page_name]["se_text_custom"]) == 1)
				{
					for ($i = 1; $i <= 5; $i++)
					{
						if ($options["ENABLE_MODEL_FILE_FIELD_{$i}"] == 1)
						{
							$where_search .= " or $table_name.custom_file{$i} like '%$q%'";
						}
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

if ($_SESSION['save'][$page_name]['se_model_group_id'] > 0)
{
	$where .= " and $table_name.model_group_id=" . intval($_SESSION['save'][$page_name]['se_model_group_id']);
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

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/description':
	case 'empty/alias':
	case 'empty/screenshot1':
	case 'empty/screenshot2':
	case 'empty/city':
	case 'empty/state':
	case 'empty/height':
	case 'empty/weight':
	case 'empty/measurements':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
	case 'empty/custom4':
	case 'empty/custom5':
	case 'empty/custom6':
	case 'empty/custom7':
	case 'empty/custom8':
	case 'empty/custom9':
	case 'empty/custom10':
	case 'empty/custom_file1':
	case 'empty/custom_file2':
	case 'empty/custom_file3':
	case 'empty/custom_file4':
	case 'empty/custom_file5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/group':
		$where .= " and $table_name.model_group_id=0";
		$table_filtered = 1;
		break;
	case 'empty/country_id':
	case 'empty/hair_id':
	case 'empty/eye_color_id':
	case 'empty/age':
	case 'empty/model_viewed':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=0";
		$table_filtered = 1;
		break;
	case 'empty/rating':
		$where .= " and ($table_name.rating=0 and $table_name.rating_amount=1)";
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
	case 'filled/description':
	case 'filled/alias':
	case 'filled/screenshot1':
	case 'filled/screenshot2':
	case 'filled/city':
	case 'filled/state':
	case 'filled/height':
	case 'filled/weight':
	case 'filled/measurements':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
	case 'filled/custom4':
	case 'filled/custom5':
	case 'filled/custom6':
	case 'filled/custom7':
	case 'filled/custom8':
	case 'filled/custom9':
	case 'filled/custom10':
	case 'filled/custom_file1':
	case 'filled/custom_file2':
	case 'filled/custom_file3':
	case 'filled/custom_file4':
	case 'filled/custom_file5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/group':
		$where .= " and $table_name.model_group_id!=0";
		$table_filtered = 1;
		break;
	case 'filled/country_id':
	case 'filled/hair_id':
	case 'filled/eye_color_id':
	case 'filled/age':
	case 'filled/model_viewed':
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=0";
		$table_filtered = 1;
		break;
	case 'filled/rating':
		$where .= " and ($table_name.rating>0 or $table_name.rating_amount>1)";
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
}

switch ($_SESSION['save'][$page_name]['se_usage'])
{
	case 'used/videos':
		$where .= " and $table_selector_videos_count>0";
		$table_filtered = 1;
		break;
	case 'used/albums':
		$where .= " and $table_selector_albums_count>0";
		$table_filtered = 1;
		break;
	case 'used/posts':
		$where .= " and $table_selector_posts_count>0";
		$table_filtered = 1;
		break;
	case 'used/other':
		$where .= " and $table_selector_other_count>0";
		$table_filtered = 1;
		break;
	case 'used/all':
		$where .= " and $table_selector_all_count>0";
		$table_filtered = 1;
		break;
	case 'notused/videos':
		$where .= " and $table_selector_videos_count=0";
		$table_filtered = 1;
		break;
	case 'notused/albums':
		$where .= " and $table_selector_albums_count=0";
		$table_filtered = 1;
		break;
	case 'notused/posts':
		$where .= " and $table_selector_posts_count=0";
		$table_filtered = 1;
		break;
	case 'notused/other':
		$where .= " and $table_selector_other_count=0";
		$table_filtered = 1;
		break;
	case 'notused/all':
		$where .= " and $table_selector_all_count=0";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'model_group')
{
	$sort_by = "$config[tables_prefix]models_groups.title";
} elseif ($sort_by == 'videos_amount')
{
	$sort_by = "$table_selector_videos_count";
} elseif ($sort_by == 'albums_amount')
{
	$sort_by = "$table_selector_albums_count";
} elseif ($sort_by == 'posts_amount')
{
	$sort_by = "$table_selector_posts_count";
} elseif ($sort_by == 'other_amount')
{
	$sort_by = "$table_selector_other_count";
} elseif ($sort_by == 'all_amount')
{
	$sort_by = "$table_selector_all_count";
} elseif ($sort_by == 'comments_amount')
{
	$sort_by = "$table_selector_comments_count";
} elseif ($sort_by == 'subscribers_amount')
{
	$sort_by = "$table_selector_subscribers_count";
} elseif ($sort_by == 'rating')
{
	$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.rating_amount";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1)
	{
		if ($field['id'] == 'videos_amount')
		{
			$table_selector .= ", $table_selector_videos_count as videos_amount";
		}
		if ($field['id'] == 'albums_amount')
		{
			$table_selector .= ", $table_selector_albums_count as albums_amount";
		}
		if ($field['id'] == 'posts_amount')
		{
			$table_selector .= ", $table_selector_posts_count as posts_amount";
		}
		if ($field['id'] == 'other_amount')
		{
			$table_selector .= ", $table_selector_other_count as other_amount";
		}
		if ($field['id'] == 'all_amount')
		{
			$table_selector .= ", $table_selector_all_count as all_amount";
		}
		if ($field['id'] == 'comments_amount')
		{
			$table_selector .= ", $table_selector_comments_count as comments_amount";
		}
		if ($field['id'] == 'subscribers_amount')
		{
			$table_selector .= ", $table_selector_subscribers_count as subscribers_amount";
		}
	}
}

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

	$validate_screenshot1 = 'min_image_size';
	$resize_screenshot1 = $options['MODELS_SCREENSHOT_1_TYPE'];
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
	$resize_screenshot2 = $options['MODELS_SCREENSHOT_2_TYPE'];
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

	validate_field('uniq', $_POST['title'], $lang['categorization']['model_field_title'], array('field_name_in_base' => 'title'));
	validate_field('file', 'screenshot1', $lang['categorization']['model_field_screenshot1'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_screenshot1 => $options['MODELS_SCREENSHOT_1_SIZE'], 'strict_mode' => '1'));
	validate_field('file', 'screenshot2', $lang['categorization']['model_field_screenshot2'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_screenshot2 => $options['MODELS_SCREENSHOT_2_SIZE'], 'strict_mode' => '1'));
	if (validate_field('empty_int', $_POST['rating_amount'], $lang['categorization']['model_field_rating']))
	{
		if ($_POST['avg_rating'] <> '0')
		{
			validate_field('empty_float', $_POST['avg_rating'], $lang['categorization']['model_field_rating']);
		}
		$rating = floatval($_POST['avg_rating']);
		if ($rating < 0 || $rating > 10)
		{
			$errors[] = get_aa_error('invalid_rating', $lang['categorization']['model_field_rating']);
		}
	}
	$_POST['birth_date'] = "0000-00-00";
	$_POST['death_date'] = "0000-00-00";
	if (intval($_POST['age_option']) == 1)
	{
		if (intval($_POST['birth_date_Year']) > 0 || intval($_POST['birth_date_Month']) > 0 || intval($_POST['birth_date_Day']) > 0)
		{
			$_POST['birth_date'] = date("Y-m-d", strtotime(intval($_POST['birth_date_Year']) . "-" . intval($_POST['birth_date_Month']) . "-" . intval($_POST['birth_date_Day'])));
			if (validate_field('date', 'birth_date_', $lang['categorization']['model_field_age']))
			{
				if (strtotime($_POST['birth_date']) > time())
				{
					$errors[] = get_aa_error('invalid_date_range', $lang['categorization']['model_field_age']);
				}
			}
		}
		if (intval($_POST['death_date_Year']) > 0 || intval($_POST['death_date_Month']) > 0 || intval($_POST['death_date_Day']) > 0)
		{
			$_POST['death_date'] = date("Y-m-d", strtotime(intval($_POST['death_date_Year']) . "-" . intval($_POST['death_date_Month']) . "-" . intval($_POST['death_date_Day'])));
			if (validate_field('date', 'death_date_', $lang['categorization']['model_field_age']))
			{
				if (strtotime($_POST['death_date']) > time() || ($_POST['birth_date'] != "0000-00-00" && strtotime($_POST['death_date']) < strtotime($_POST['birth_date'])))
				{
					$errors[] = get_aa_error('invalid_date_range', $lang['categorization']['model_field_age']);
				}
			}
		}
		if ($_POST['birth_date'] != "0000-00-00")
		{
			if ($_POST['death_date'] != "0000-00-00")
			{
				$_POST['age'] = get_age(strtotime($_POST['birth_date']), strtotime($_POST['death_date']));
			} else
			{
				$_POST['age'] = get_age(strtotime($_POST['birth_date']));
			}
		}
	} elseif (intval($_POST['age_option']) == 2)
	{
		if ($_POST['age'] <> '')
		{
			validate_field('empty_int', $_POST['age'], $lang['categorization']['model_field_age']);
		}
	}
	validate_field('file', 'custom_file1', $options['MODEL_FILE_FIELD_1_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file2', $options['MODEL_FILE_FIELD_2_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file3', $options['MODEL_FILE_FIELD_3_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file4', $options['MODEL_FILE_FIELD_4_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));
	validate_field('file', 'custom_file5', $options['MODEL_FILE_FIELD_5_NAME'], array('allowed_ext' => $allowed_file_ext, 'strict_mode' => '1'));

	if ($options['MODELS_SCREENSHOT_OPTION'] == 1)
	{
		if ($_POST['screenshot1_hash'] <> '' && $_POST['screenshot2_hash'] == '')
		{
			$_POST['screenshot2'] = $_POST['screenshot1'];
			$_POST['screenshot2_hash'] = md5($_POST['screenshot1_hash']);
			@copy("$config[temporary_path]/$_POST[screenshot1_hash].tmp", "$config[temporary_path]/$_POST[screenshot2_hash].tmp");
		}
	}

	$post_file_fields = array('screenshot1' => 's1_', 'screenshot2' => 's2_', 'custom_file1' => 'c1_', 'custom_file2' => 'c2_', 'custom_file3' => 'c3_', 'custom_file4' => 'c4_', 'custom_file5' => 'c5_');
	foreach ($post_file_fields as $k => $v)
	{
		if ($_POST["{$k}_hash"] <> '')
		{
			$_POST[$k] = "{$v}$_POST[$k]";
		}
	}

	if (!is_array($errors))
	{
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
			$item_id = sql_insert("insert into $table_name set model_group_id=?, title=?, dir=?, alias=?, description=?, status_id=?, screenshot1=?, screenshot2=?, rating=?, rating_amount=?, access_level_id=?, country_id=?, state=?, city=?, height=?, weight=?, hair_id=?, eye_color_id=?, measurements=?, gender_id=?, birth_date=?, death_date=?, age=?,
						custom1=?, custom2=?, custom3=?, custom4=?, custom5=?, custom6=?, custom7=?, custom8=?, custom9=?, custom10=?, custom_file1=?, custom_file2=?, custom_file3=?, custom_file4=?, custom_file5=?, added_date=?",
				intval($_POST['model_group_id']), $_POST['title'], $_POST['dir'], $_POST['alias'], $_POST['description'], intval($_POST['status_id']), $_POST['screenshot1'], $_POST['screenshot2'], floatval($_POST['avg_rating']) * intval($_POST['rating_amount']), intval($_POST['rating_amount']), intval($_POST['access_level_id']), intval($_POST['country_id']), $_POST['state'], $_POST['city'], $_POST['height'], $_POST['weight'], intval($_POST['hair_id']), intval($_POST['eye_color_id']), $_POST['measurements'], intval($_POST['gender_id']), $_POST['birth_date'], $_POST['death_date'], intval($_POST['age']),
				$_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $_POST['custom6'], $_POST['custom7'], $_POST['custom8'], $_POST['custom9'], $_POST['custom10'], $_POST['custom_file1'], $_POST['custom_file2'], $_POST['custom_file3'], $_POST['custom_file4'], $_POST['custom_file5'], date("Y-m-d H:i:s")
			);

			if ($_POST['screenshot1'] <> '')
			{
				transfer_uploaded_file('screenshot1', "$config[content_path_models]/$item_id/$_POST[screenshot1]");
				resize_image($resize_screenshot1, "$config[content_path_models]/$item_id/$_POST[screenshot1]", "$config[content_path_models]/$item_id/$_POST[screenshot1]", $options['MODELS_SCREENSHOT_1_SIZE']);
			}
			if ($_POST['screenshot2'] <> '')
			{
				transfer_uploaded_file('screenshot2', "$config[content_path_models]/$item_id/$_POST[screenshot2]");
				resize_image($resize_screenshot2, "$config[content_path_models]/$item_id/$_POST[screenshot2]", "$config[content_path_models]/$item_id/$_POST[screenshot2]", $options['MODELS_SCREENSHOT_2_SIZE']);
			}
			transfer_uploaded_file('custom_file1', "$config[content_path_models]/$item_id/$_POST[custom_file1]");
			transfer_uploaded_file('custom_file2', "$config[content_path_models]/$item_id/$_POST[custom_file2]");
			transfer_uploaded_file('custom_file3', "$config[content_path_models]/$item_id/$_POST[custom_file3]");
			transfer_uploaded_file('custom_file4', "$config[content_path_models]/$item_id/$_POST[custom_file4]");
			transfer_uploaded_file('custom_file5', "$config[content_path_models]/$item_id/$_POST[custom_file5]");

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=4, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
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

			sql_pr("update $table_name set model_group_id=?, title=?, dir=?, alias=?, description=?, status_id=?, rating=?, rating_amount=?, access_level_id=?, country_id=?, state=?, city=?, height=?, weight=?, hair_id=?, eye_color_id=?, measurements=?, gender_id=?, birth_date=?, death_date=?, age=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=?, custom6=?, custom7=?, custom8=?, custom9=?, custom10=? where $table_key_name=?",
				intval($_POST['model_group_id']), $_POST['title'], $_POST['dir'], $_POST['alias'], $_POST['description'], intval($_POST['status_id']), floatval($_POST['avg_rating']) * intval($_POST['rating_amount']), intval($_POST['rating_amount']), intval($_POST['access_level_id']), intval($_POST['country_id']), $_POST['state'], $_POST['city'], $_POST['height'], $_POST['weight'], intval($_POST['hair_id']), intval($_POST['eye_color_id']), $_POST['measurements'], intval($_POST['gender_id']), $_POST['birth_date'], $_POST['death_date'], intval($_POST['age']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $_POST['custom6'], $_POST['custom7'], $_POST['custom8'], $_POST['custom9'], $_POST['custom10'], $item_id
			);

			if ($_POST['screenshot1_hash'] <> '')
			{
				$old_file = $old_data['screenshot1'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('screenshot1', "$config[content_path_models]/$item_id/$_POST[screenshot1]");
				resize_image($resize_screenshot1, "$config[content_path_models]/$item_id/$_POST[screenshot1]", "$config[content_path_models]/$item_id/$_POST[screenshot1]", $options['MODELS_SCREENSHOT_1_SIZE']);
				sql_pr("update $table_name set screenshot1=? where $table_key_name=?", $_POST['screenshot1'], $item_id);
			} elseif ($_POST['screenshot1'] == '')
			{
				$old_file = $old_data['screenshot1'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set screenshot1='' where $table_key_name=?", $item_id);
			}
			if ($_POST['screenshot2_hash'] <> '')
			{
				$old_file = $old_data['screenshot2'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('screenshot2', "$config[content_path_models]/$item_id/$_POST[screenshot2]");
				resize_image($resize_screenshot2, "$config[content_path_models]/$item_id/$_POST[screenshot2]", "$config[content_path_models]/$item_id/$_POST[screenshot2]", $options['MODELS_SCREENSHOT_2_SIZE']);
				sql_pr("update $table_name set screenshot2=? where $table_key_name=?", $_POST['screenshot2'], $item_id);
			} elseif ($_POST['screenshot2'] == '')
			{
				$old_file = $old_data['screenshot2'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set screenshot2='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file1_hash'] <> '')
			{
				$old_file = $old_data['custom_file1'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file1', "$config[content_path_models]/$item_id/$_POST[custom_file1]");
				sql_pr("update $table_name set custom_file1=? where $table_key_name=?", $_POST['custom_file1'], $item_id);
			} elseif ($_POST['custom_file1'] == '')
			{
				$old_file = $old_data['custom_file1'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file1='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file2_hash'] <> '')
			{
				$old_file = $old_data['custom_file2'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file2', "$config[content_path_models]/$item_id/$_POST[custom_file2]");
				sql_pr("update $table_name set custom_file2=? where $table_key_name=?", $_POST['custom_file2'], $item_id);
			} elseif ($_POST['custom_file2'] == '')
			{
				$old_file = $old_data['custom_file2'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file2='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file3_hash'] <> '')
			{
				$old_file = $old_data['custom_file3'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file3', "$config[content_path_models]/$item_id/$_POST[custom_file3]");
				sql_pr("update $table_name set custom_file3=? where $table_key_name=?", $_POST['custom_file3'], $item_id);
			} elseif ($_POST['custom_file3'] == '')
			{
				$old_file = $old_data['custom_file3'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file3='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file4_hash'] <> '')
			{
				$old_file = $old_data['custom_file4'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file4', "$config[content_path_models]/$item_id/$_POST[custom_file4]");
				sql_pr("update $table_name set custom_file4=? where $table_key_name=?", $_POST['custom_file4'], $item_id);
			} elseif ($_POST['custom_file4'] == '')
			{
				$old_file = $old_data['custom_file4'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file4='' where $table_key_name=?", $item_id);
			}
			if ($_POST['custom_file5_hash'] <> '')
			{
				$old_file = $old_data['custom_file5'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				transfer_uploaded_file('custom_file5', "$config[content_path_models]/$item_id/$_POST[custom_file5]");
				sql_pr("update $table_name set custom_file5=? where $table_key_name=?", $_POST['custom_file5'], $item_id);
			} elseif ($_POST['custom_file5'] == '')
			{
				$old_file = $old_data['custom_file5'];
				if (is_file("$config[content_path_models]/$item_id/$old_file"))
				{
					unlink("$config[content_path_models]/$item_id/$old_file");
				}
				sql_pr("update $table_name set custom_file5='' where $table_key_name=?", $item_id);
			}

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
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=4, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
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

		if (count($list_ids_categories) > 0)
		{
			$list_ids_categories = implode(',', $list_ids_categories);
			sql_pr("update $config[tables_prefix]categories set $column_name_total=(select count(*) from $table_name_categories where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
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
			if (is_file("$config[content_path_models]/$v[model_id]/$v[screenshot1]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[screenshot1]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[screenshot2]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[screenshot2]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[custom_file1]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[custom_file1]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[custom_file2]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[custom_file2]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[custom_file3]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[custom_file3]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[custom_file4]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[custom_file4]");
			}
			if (is_file("$config[content_path_models]/$v[model_id]/$v[custom_file5]"))
			{
				@unlink("$config[content_path_models]/$v[model_id]/$v[custom_file5]");
			}
			if (is_dir("$config[content_path_models]/$v[model_id]"))
			{
				@rmdir("$config[content_path_models]/$v[model_id]");
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=4, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $v['model_id'], date("Y-m-d H:i:s"));
		}
		$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($row_select) and object_type_id=4"));
		$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

		$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select)")));

		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $table_name_categories where $table_key_name in ($row_select)");
		sql("delete from $table_name_tags where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]models_albums where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]models_posts where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]models_videos where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]models_dvds where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]models_dvds_groups where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]users_events where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]comments where object_id in ($row_select) and object_type_id=4");
		sql("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($row_select) and subscribed_type_id=4");

		if (strlen($list_ids_comments) > 0)
		{
			sql("update $config[tables_prefix]users set
					comments_models_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=4),
					comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
				where user_id in ($list_ids_comments)"
			);
		}

		if (count($list_ids_categories) > 0)
		{
			$list_ids_categories = implode(',', $list_ids_categories);
			sql_pr("update $config[tables_prefix]categories set $column_name_total=(select count(*) from $table_name_categories where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
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
	$_POST['birth_date'] = "0000-00-00";
	$_POST['death_date'] = "0000-00-00";
	$_POST['rating_amount'] = 1;
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector_single from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
	$_POST['rank'] = '#' . $_POST['rank'];

	if ($_POST['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_MODEL'] <> '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
	}

	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=$_POST[$table_key_name] order by id asc"));
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
		if ($data[$k]['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_MODEL'] <> '')
		{
			$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
		}
		$data[$k]['rank'] = '#' . $data[$k]['rank'];

		if ($_SESSION['save'][$page_name]['grid_columns']['categories'] == 1)
		{
			$data[$k]['categories'] = mr2array(sql_pr("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories inner join $table_name_categories on $config[tables_prefix]categories.category_id=$table_name_categories.category_id where $table_name_categories.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_categories.id asc"));
		}
		if ($_SESSION['save'][$page_name]['grid_columns']['tags'] == 1)
		{
			$data[$k]['tags'] = mr2array(sql_pr("select $config[tables_prefix]tags.tag_id as id, $config[tables_prefix]tags.tag as title from $config[tables_prefix]tags inner join $table_name_tags on $config[tables_prefix]tags.tag_id=$table_name_tags.tag_id where $table_name_tags.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_tags.id asc"));
		}

		if ($v["screenshot1"])
		{
			$data[$k]["screenshot1_url"] = "$config[content_url_models]/$v[$table_key_name]/$v[screenshot1]";
		}
		if ($v["screenshot2"])
		{
			$data[$k]["screenshot2_url"] = "$config[content_url_models]/$v[$table_key_name]/$v[screenshot2]";
		}
		for ($i = 1; $i <= 5; $i++)
		{
			if ($v["custom_file{$i}"])
			{
				$data[$k]["custom_file{$i}_url"] = "$config[content_url_models]/$v[$table_key_name]/" . $v["custom_file{$i}"];
			}
		}

		$thumb_field = 'screenshot1';
		if ($options['MODELS_SCREENSHOT_OPTION'] > 0)
		{
			$image_size1 = explode('x', $options['MODELS_SCREENSHOT_1_SIZE']);
			$image_size2 = explode('x', $options['MODELS_SCREENSHOT_2_SIZE']);
			if (($image_size1[0] > $image_size2[0] || !$v["screenshot1"]) && $v["screenshot2"])
			{
				$thumb_field = 'screenshot2';
			}
		}
		if ($v[$thumb_field])
		{
			$data[$k]['thumb'] = "$config[content_url_models]/$v[$table_key_name]/$v[$thumb_field]";
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_categorization.tpl');
$smarty->assign('list_models_groups', mr2array(sql("select model_group_id, title from $config[tables_prefix]models_groups order by title asc")));
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
$smarty->assign('list_countries', $list_countries);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['categorization']['model_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['categorization']['model_add']);
} else
{
	$smarty->assign('page_title', $lang['categorization']['submenu_option_models_list']);
}

$smarty->display("layout.tpl");
