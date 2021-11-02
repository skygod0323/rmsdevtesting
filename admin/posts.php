<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/database_selectors.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();

$list_post_types = mr2array(sql("select * from $config[tables_prefix]posts_types order by title asc"));

$locked_post_type_id = 0;
$locked_post_type = null;
if ($_REQUEST['post_type_external_id'] != '')
{
	foreach ($list_post_types as $post_type)
	{
		if ($post_type['external_id'] == $_REQUEST['post_type_external_id'])
		{
			$locked_post_type_id = $post_type['post_type_id'];
			$locked_post_type = $post_type;
		}
	}
}

$list_custom_fields = array();
for ($i = 1; $i <= 10; $i++)
{
	$enabled_by_type = array();
	$titles_by_type = array();
	foreach ($list_post_types as $post_type)
	{
		if ($options["ENABLE_POST_{$post_type['post_type_id']}_FIELD_{$i}"] == 1)
		{
			$enabled_by_type[$post_type['post_type_id']] = 1;
			$titles_by_type[$post_type['post_type_id']] = $options["POST_{$post_type['post_type_id']}_FIELD_{$i}_NAME"] != '' ? $options["POST_{$post_type['post_type_id']}_FIELD_{$i}_NAME"] : $lang['settings']["custom_field_{$i}"];
		}
	}
	if (count($enabled_by_type) > 0)
	{
		$list_custom_fields[] = array(
			'field_name' => "custom$i",
			'enabled' => $enabled_by_type,
			'titles' => $titles_by_type,
			'is_text' => 1
		);
	}
}
for ($i = 1; $i <= 10; $i++)
{
	$enabled_by_type = array();
	$titles_by_type = array();
	foreach ($list_post_types as $post_type)
	{
		if ($options["ENABLE_POST_{$post_type['post_type_id']}_FILE_FIELD_{$i}"] == 1)
		{
			$enabled_by_type[$post_type['post_type_id']] = 1;
			$titles_by_type[$post_type['post_type_id']] = $options["POST_{$post_type['post_type_id']}_FILE_FIELD_{$i}_NAME"] != '' ? $options["POST_{$post_type['post_type_id']}_FILE_FIELD_{$i}_NAME"] : $lang['settings']["custom_file_field_{$i}"];
		}
	}
	if (count($enabled_by_type) > 0)
	{
		$list_custom_fields[] = array(
			'field_name' => "custom_file$i",
			'enabled' => $enabled_by_type,
			'titles' => $titles_by_type,
			'is_file' => 1
		);
	}
}

$list_status_values = array(
	0 => $lang['posts']['post_field_status_disabled'],
	1 => $lang['posts']['post_field_status_active'],
);

$list_upload_zone_values = array(
	0 => $lang['posts']['post_field_af_upload_zone_site'],
	1 => $lang['posts']['post_field_af_upload_zone_memberarea'],
);

$table_fields = array();
$table_fields[] = array('id' => 'post_id',             'title' => $lang['posts']['post_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',               'title' => $lang['posts']['post_field_title'],          'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',                 'title' => $lang['posts']['post_field_directory'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',         'title' => $lang['posts']['post_field_description'],    'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'type',                'title' => $lang['posts']['post_field_type'],           'is_default' => 1, 'type' => 'text', 'link' => 'posts_types.php?action=change&item_id=%id%', 'link_id' => 'post_type_id', 'permission' => 'posts_types|view');
$table_fields[] = array('id' => 'status_id',           'title' => $lang['posts']['post_field_status'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'user',                'title' => $lang['posts']['post_field_user'],           'is_default' => 0, 'type' => 'user');
if (in_array('system|administration',$_SESSION['permissions']))
{
	$table_fields[] = array('id' => 'admin',           'title' => $lang['posts']['post_field_admin'],          'is_default' => 0, 'type' => 'admin');
}
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',              'title' => $lang['posts']['post_field_ip'],             'is_default' => 0, 'type' => 'ip');
}
if (is_array($config['advanced_filtering']) && in_array('upload_zone', $config['advanced_filtering']))
{
	$table_fields[] = array('id' => 'af_upload_zone',  'title' => $lang['posts']['post_field_af_upload_zone'], 'is_default' => 0, 'type' => 'choice', 'values' => $list_upload_zone_values);
}
$table_fields[] = array('id' => 'rating',              'title' => $lang['posts']['post_field_rating'],         'is_default' => 1, 'type' => 'float');
$table_fields[] = array('id' => 'post_viewed',         'title' => $lang['posts']['post_field_visits'],         'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'tags',                'title' => $lang['posts']['post_field_tags'],           'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view');
$table_fields[] = array('id' => 'categories',          'title' => $lang['posts']['post_field_categories'],     'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'models',              'title' => $lang['posts']['post_field_models'],         'is_default' => 0, 'type' => 'list', 'link' => 'models.php?action=change&item_id=%id%', 'permission' => 'models|view');

if ($locked_post_type_id > 0)
{
	foreach ($list_custom_fields as $custom_field)
	{
		if ($custom_field['is_text'] == 1 && isset($custom_field['enabled'][$locked_post_type_id]))
		{
			$table_fields[] = array('id' => $custom_field['field_name'], 'title' => $custom_field['titles'][$locked_post_type_id], 'is_default' => 0, 'type' => 'longtext');
		}
	}
}

$table_fields[] = array('id' => 'comments_amount',     'title' => $lang['posts']['post_field_comments_count'], 'is_default' => 0, 'type' => 'number', 'link' => 'comments.php?no_filter=true&se_object_type_id=12&se_object_id=%id%', 'link_id' => 'post_id', 'permission' => 'users|manage_comments', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'post_date',           'title' => $lang['posts']['post_field_post_date'],      'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'added_date',          'title' => $lang['posts']['post_field_added_date'],     'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'last_time_view_date', 'title' => $lang['posts']['post_field_last_view_date'], 'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'is_locked',           'title' => $lang['posts']['post_field_lock_website'],   'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'is_review_needed',    'title' => $lang['posts']['post_field_needs_review'],   'is_default' => 0, 'type' => 'bool');

$sort_def_field = "post_id";
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
$search_fields[] = array('id' => 'post_id',     'title' => $lang['posts']['post_field_id']);
$search_fields[] = array('id' => 'title',       'title' => $lang['posts']['post_field_title']);
$search_fields[] = array('id' => 'dir',         'title' => $lang['posts']['post_field_directory']);
$search_fields[] = array('id' => 'description', 'title' => $lang['posts']['post_field_description']);
$search_fields[] = array('id' => 'content',     'title' => $lang['posts']['post_field_content']);
if ($locked_post_type_id > 0)
{
	$search_fields[] = array('id' => 'custom', 'title' => $lang['common']['dg_filter_search_in_custom']);
}

$table_name = "$config[tables_prefix]posts";
$table_key_name = "post_id";
$table_selector = "$table_name.*, $table_name.rating / $table_name.rating_amount as rating, $config[tables_prefix]posts_types.title as type, $config[tables_prefix]posts_types.url_pattern as url_pattern, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, $config[tables_prefix]admin_users.login as admin, (select count(*) from $config[tables_prefix]comments where object_type_id=12 and object_id=$table_name.$table_key_name) as comments_amount";
$table_projector = "$table_name left join $config[tables_prefix]posts_types on $config[tables_prefix]posts_types.post_type_id=$table_name.post_type_id left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id left join $config[tables_prefix]admin_users on $config[tables_prefix]admin_users.user_id=$table_name.admin_user_id";

$table_name_categories = "$config[tables_prefix]categories_posts";
$table_name_tags = "$config[tables_prefix]tags_posts";
$table_name_models = "$config[tables_prefix]models_posts";

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
	$_SESSION['save'][$page_name]['se_post_type_id'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_admin_user_id'] = '';
	$_SESSION['save'][$page_name]['se_category'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
	$_SESSION['save'][$page_name]['se_model'] = '';
	$_SESSION['save'][$page_name]['se_flag_id'] = '';
	$_SESSION['save'][$page_name]['se_flag_values_amount'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_review_flag'] = '';
	$_SESSION['save'][$page_name]['se_posted'] = '';
	$_SESSION['save'][$page_name]['se_post_date_from'] = '';
	$_SESSION['save'][$page_name]['se_post_date_to'] = '';
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
	if (isset($_GET['se_post_type_id']))
	{
		$_SESSION['save'][$page_name]['se_post_type_id'] = intval($_GET['se_post_type_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_admin_user_id']))
	{
		$_SESSION['save'][$page_name]['se_admin_user_id'] = intval($_GET['se_admin_user_id']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
	}
	if (isset($_GET['se_tag']))
	{
		$_SESSION['save'][$page_name]['se_tag'] = trim($_GET['se_tag']);
	}
	if (isset($_GET['se_model']))
	{
		$_SESSION['save'][$page_name]['se_model'] = trim($_GET['se_model']);
	}
	if (isset($_GET['se_flag_id']))
	{
		$_SESSION['save'][$page_name]['se_flag_id'] = intval($_GET['se_flag_id']);
	}
	if (isset($_GET['se_flag_values_amount']))
	{
		$_SESSION['save'][$page_name]['se_flag_values_amount'] = intval($_GET['se_flag_values_amount']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_posted']))
	{
		$_SESSION['save'][$page_name]['se_posted'] = trim($_GET['se_posted']);
	}
	if (isset($_GET['se_review_flag']))
	{
		$_SESSION['save'][$page_name]['se_review_flag'] = intval($_GET['se_review_flag']);
	}
	if (isset($_GET['se_post_date_from_Day'], $_GET['se_post_date_from_Month'], $_GET['se_post_date_from_Year']))
	{
		if (intval($_GET['se_post_date_from_Day']) > 0 && intval($_GET['se_post_date_from_Month']) > 0 && intval($_GET['se_post_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_post_date_from'] = intval($_GET['se_post_date_from_Year']) . "-" . intval($_GET['se_post_date_from_Month']) . "-" . intval($_GET['se_post_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_post_date_from'] = "";
		}
	}
	if (isset($_GET['se_post_date_to_Day'], $_GET['se_post_date_to_Month'], $_GET['se_post_date_to_Year']))
	{
		if (intval($_GET['se_post_date_to_Day']) > 0 && intval($_GET['se_post_date_to_Month']) > 0 && intval($_GET['se_post_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_post_date_to'] = intval($_GET['se_post_date_to_Year']) . "-" . intval($_GET['se_post_date_to_Month']) . "-" . intval($_GET['se_post_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_post_date_to'] = "";
		}
	}
}

$table_filtered = 0;
$where = '';

if ($locked_post_type_id > 0)
{
	$where .= " and $table_name.post_type_id=$locked_post_type_id";
}

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
				foreach ($list_custom_fields as $custom_field)
				{
					if ($custom_field['is_text'] == 1 && isset($custom_field['enabled'][$locked_post_type_id]))
					{
						$where_search .= " or $table_name.$custom_field[field_name] like '%$q%'";
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

if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user']));
	if ($user_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.user_id=$user_id";
	}
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

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and $table_name.status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and $table_name.status_id=1";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_post_type_id'] > 0 && $locked_post_type_id == 0)
{
	$where .= " and $table_name.post_type_id=" . intval($_SESSION['save'][$page_name]['se_post_type_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_admin_user_id'] > 0)
{
	$where .= " and $table_name.admin_user_id=" . intval($_SESSION['save'][$page_name]['se_admin_user_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_flag_id'] > 0)
{
	$flag_amount = max(1, intval($_SESSION['save'][$page_name]['se_flag_values_amount']));
	$where .= " and (select sum(votes) from $config[tables_prefix]flags_posts where $table_key_name=$table_name.$table_key_name and flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']) . ")>=$flag_amount";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/title':
	case 'empty/description':
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
	case 'empty/custom_file6':
	case 'empty/custom_file7':
	case 'empty/custom_file8':
	case 'empty/custom_file9':
	case 'empty/custom_file10':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/post_viewed':
		$where .= " and $table_name.post_viewed=0";
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
	case 'empty/models':
		$where .= " and not exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/title':
	case 'filled/description':
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
	case 'filled/custom_file6':
	case 'filled/custom_file7':
	case 'filled/custom_file8':
	case 'filled/custom_file9':
	case 'filled/custom_file10':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/post_viewed':
		$where .= " and $table_name.post_viewed!=0";
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
	case 'filled/models':
		$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

if ($_SESSION['save'][$page_name]['se_posted'] == "yes")
{
	$where .= " and $database_selectors[where_posts]";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_posted'] == "no")
{
	$where .= " and not ($database_selectors[where_posts])";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_review_flag'] == '1')
{
	$where .= " and $table_name.is_review_needed=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_review_flag'] == '2')
{
	$where .= " and $table_name.is_review_needed=0";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_post_date_from'] <> "")
{
	$where .= " and $table_name.post_date>='" . $_SESSION['save'][$page_name]['se_post_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_post_date_to'] <> "")
{
	$where .= " and $table_name.post_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_post_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and $table_name.status_id=0 ";
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'admin')
{
	$sort_by = "$config[tables_prefix]admin_users.login";
} elseif ($sort_by == 'type')
{
	$sort_by = "$config[tables_prefix]posts_types.title";
} elseif ($sort_by == 'rating')
{
	$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.rating_amount";
} elseif ($config['relative_post_dates'] == 'true' && $sort_by == 'post_date')
{
	$sort_by = "$table_name.post_date " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.relative_post_date";
} elseif ($sort_by == 'comments_amount')
{
	$sort_by = "comments_amount";
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

	if (in_array('posts|edit_all', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete' || in_array('posts|edit_title', $_SESSION['permissions']))
	{
		if (intval($_POST['status_id']) == 1)
		{
			if (validate_field('empty', $_POST['title'], $lang['posts']['post_field_title']))
			{
				if (intval($options['POST_CHECK_DUPLICATE_TITLES']) == 1)
				{
					validate_field('uniq', $_POST['title'], $lang['posts']['post_field_title'], array('field_name_in_base' => 'title'));
				}
			}
			if ($_POST['dir'] != '' && $_POST['action'] == 'change_complete' && in_array('posts|edit_all', $_SESSION['permissions']))
			{
				validate_field('uniq', $_POST['dir'], $lang['posts']['post_field_directory'], array('field_name_in_base' => 'dir'));
			}
		} elseif ($_POST['title'] != '' && intval($options['POST_CHECK_DUPLICATE_TITLES']) == 1)
		{
			validate_field('uniq', $_POST['title'], $lang['posts']['post_field_title'], array('field_name_in_base' => 'title'));
		}
	}

	if (in_array('posts|edit_all', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete' || in_array('posts|edit_type', $_SESSION['permissions']))
	{
		validate_field('empty_int', $_POST['post_type_id'], $lang['posts']['post_field_type']);
	}

	if (in_array('posts|edit_all', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete' || in_array('posts|edit_post_date', $_SESSION['permissions']))
	{
		if (intval($_POST['post_date_option']) == 0)
		{
			validate_field('date', 'post_date_', $lang['posts']['post_field_post_date']);
		} else
		{
			validate_field('empty_int_ext', $_POST['relative_post_date'], $lang['posts']['post_field_post_date']);
		}
	}

	if (in_array('posts|edit_all', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete' || in_array('posts|edit_user', $_SESSION['permissions']))
	{
		if (validate_field('empty', $_POST['user'], $lang['posts']['post_field_user']))
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['posts']['post_field_user']);
			}
		}
	}

	if (in_array('posts|edit_all', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete' || in_array('posts|edit_content', $_SESSION['permissions']))
	{
		validate_field('empty', $_POST['content'], $lang['posts']['post_field_content']);
	}

	$current_post_type = null;
	foreach ($list_post_types as $post_type)
	{
		if ($post_type['post_type_id'] == $_POST['post_type_id'])
		{
			$current_post_type = $post_type;
			break;
		}
	}
	$allowed_file_ext = "$config[video_allowed_ext],$config[image_allowed_ext]";
	if ($config['other_allowed_ext'] != '')
	{
		$allowed_file_ext .= ",$config[other_allowed_ext]";
	}
	for ($i = 1; $i <= 10; $i++)
	{
		if (isset($current_post_type) && $options["POST_{$current_post_type['post_type_id']}_FILE_FIELD_{$i}_NAME"] != '')
		{
			validate_field('file', "custom_file$i", $options["POST_{$current_post_type['post_type_id']}_FILE_FIELD_{$i}_NAME"], array('allowed_ext' => $allowed_file_ext));
		} else
		{
			validate_field('file', "custom_file$i", $lang['settings']["custom_file_field_{$i}"], array('allowed_ext' => $allowed_file_ext));
		}
	}

	if ($_POST['action'] == 'add_new_complete')
	{
		if (!is_writable("$config[content_path_posts]"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$config[content_path_posts]");
		}
		if (is_dir("$config[content_path_posts]"))
		{
			$dir_content = get_contents_from_dir("$config[content_path_posts]", 2);
			foreach ($dir_content as $sub_dir)
			{
				if (!is_writable("$config[content_path_posts]/$sub_dir"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', "$config[content_path_posts]/$sub_dir");
				}
			}
		}
	} else
	{
		$item_id = intval($_POST['item_id']);
		$dir_path = get_dir_by_id($item_id);
		if (is_dir("$config[content_path_posts]/$dir_path/$item_id"))
		{
			if (!is_writable("$config[content_path_posts]/$dir_path/$item_id"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$config[content_path_posts]/$dir_path/$item_id");
			}
		} elseif (is_dir("$config[content_path_posts]/$dir_path"))
		{
			if (!is_writable("$config[content_path_posts]/$dir_path"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', "$config[content_path_posts]/$dir_path");
			}
		} elseif (!is_writable("$config[content_path_posts]"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', "$config[content_path_posts]");
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = 0;
			$_POST['dir'] = get_correct_dir_name($_POST['title']);

			if ($_POST['dir'] != '')
			{
				$temp_dir = $_POST['dir'];
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where dir=?", $temp_dir)) == 0)
					{
						$_POST['dir'] = $temp_dir;
						break;
					}
					$temp_dir = $_POST['dir'] . $i;
				}
			}
		} else
		{
			$item_id = intval($_POST['item_id']);
			$dir_path = get_dir_by_id($item_id);
			$old_post_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
			if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
			{
				if ($old_post_data['admin_user_id'] != $_SESSION['userdata']['user_id'])
				{
					header("Location: error.php?error=permission_denied");
					die;
				}
			}
			if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
			{
				if ($old_post_data['status_id'] != 0)
				{
					header("Location: error.php?error=permission_denied");
					die;
				}
			}

			if ($_POST['dir'] == '' || $options['POST_REGENERATE_DIRECTORIES'] == 1)
			{
				$_POST['dir'] = get_correct_dir_name($_POST['title']);
			}
			if ($_POST['dir'] != '')
			{
				$temp_dir = $_POST['dir'];
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where dir=? and $table_key_name!=?", $temp_dir, $item_id)) == 0)
					{
						$_POST['dir'] = $temp_dir;
						break;
					}
					$temp_dir = $_POST['dir'] . $i;
				}
			}
		}

		$next_item_id = 0;
		if (isset($_POST['save_and_edit']) || isset($_POST['delete_and_edit']))
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

			if (isset($_POST['delete_and_edit']))
			{
				if (in_array('posts|delete', $_SESSION['permissions']))
				{
					$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id=$item_id and object_type_id=12"));
					$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

					$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name=$item_id")));
					$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name=$item_id")));
					$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name=$item_id")));

					sql("delete from $table_name where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]tags_posts where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]categories_posts where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]models_posts where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]rating_history where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]flags_posts where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]flags_history where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]flags_messages where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]users_events where $table_key_name=$item_id");
					sql("delete from $config[tables_prefix]comments where object_id=$item_id and object_type_id=12");

					if (strlen($list_ids_comments) > 0)
					{
						sql("update $config[tables_prefix]users set
								comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
								comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_comments)"
						);
					}

					update_tags_posts_totals($list_ids_tags);
					update_categories_posts_totals($list_ids_categories);
					update_models_posts_totals($list_ids_models);

					$custom_files = get_contents_from_dir("$config[content_path_posts]/$dir_path/$item_id", 1);
					foreach ($custom_files as $custom_file)
					{
						@unlink("$config[content_path_posts]/$dir_path/$item_id/$custom_file");
					}
					@rmdir("$config[content_path_posts]/$dir_path/$item_id");

					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=12, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
					$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

					if ($next_item_id == 0)
					{
						return_ajax_success($page_name, 1);
					} else
					{
						return_ajax_success($page_name . "?action=change&amp;item_id=$next_item_id", 1);
					}
				} else
				{
					header("Location: error.php?error=permission_denied");
					die;
				}
			}
		}

		$post_time = array('00', '00');
		if (strpos($_POST['post_time'], ":") !== false)
		{
			$temp = explode(":", $_POST['post_time']);
			if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
			{
				$post_time[0] = $temp[0];
			}
			if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
			{
				$post_time[1] = $temp[1];
			}
		}
		if (intval($_POST['post_date_option']) == 0)
		{
			$_POST['post_date'] = date("Y-m-d H:i:s", strtotime(intval($_POST['post_date_Year']) . "-" . intval($_POST['post_date_Month']) . "-" . intval($_POST['post_date_Day']) . " " . intval($post_time[0]) . ":" . intval($post_time[1])));
			$_POST['relative_post_date'] = 0;
		} else
		{
			$_POST['post_date'] = '1971-01-01 00:00:00';
		}

		if ($_POST['user'] != '')
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user']));
		} else
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4"));
		}

		$update_array = array();
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_title', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['title'] = $_POST['title'];
			if (isset($old_post_data) && ($old_post_data['dir'] == '' || $options['POST_REGENERATE_DIRECTORIES'] == 1))
			{
				$update_array['dir'] = $_POST['dir'];
			}
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_dir', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['dir'] = $_POST['dir'];
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_description', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['description'] = $_POST['description'];
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_content', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['content'] = $_POST['content'];
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_post_date', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['post_date'] = $_POST['post_date'];
			$update_array['relative_post_date'] = intval($_POST['relative_post_date']);
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_user', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['user_id'] = $user_id;
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_status', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['status_id'] = intval($_POST['status_id']);
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_type', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			$update_array['post_type_id'] = intval($_POST['post_type_id']);
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_custom', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
			for ($i = 1; $i <= 10; $i++)
			{
				$update_array["custom$i"] = $_POST["custom$i"];
			}
		}
		if (in_array('posts|edit_all', $_SESSION['permissions']))
		{
			$update_array["is_locked"] = intval($_POST["is_locked"]);
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array['admin_user_id'] = $_SESSION['userdata']['user_id'];
			if (intval($options['POST_INITIAL_RATING']) > 0)
			{
				$update_array['rating'] = intval($options['POST_INITIAL_RATING']);
				$update_array['rating_amount'] = 1;
			} else
			{
				$update_array['rating'] = 0;
				$update_array['rating_amount'] = 1;
			}
			$update_array['added_date'] = date("Y-m-d H:i:s");

			$item_id = sql_insert("insert into $table_name set ?%", $update_array);
			$dir_path = get_dir_by_id($item_id);

			if (intval($update_array['status_id']) == 1)
			{
				process_activated_posts(array($item_id));
			}

			$update_array = array();
			for ($i = 1; $i <= 10; $i++)
			{
				if ($_POST["custom_file$i"] != '')
				{
					if (!is_dir("$config[content_path_posts]/$dir_path"))
					{
						mkdir("$config[content_path_posts]/$dir_path");
						chmod("$config[content_path_posts]/$dir_path", 0777);
					}
					if (!is_dir("$config[content_path_posts]/$dir_path/$item_id"))
					{
						mkdir("$config[content_path_posts]/$dir_path/$item_id");
						chmod("$config[content_path_posts]/$dir_path/$item_id", 0777);
					}

					$processed_filename = get_correct_file_name($_POST["custom_file$i"], "$config[content_path_posts]/$dir_path/$item_id");
					transfer_uploaded_file("custom_file$i", "$config[content_path_posts]/$dir_path/$item_id/$processed_filename");
					$update_array["custom_file$i"] = $processed_filename;
				}
			}
			if (count($update_array) > 0)
			{
				sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=12, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_flags', $_SESSION['permissions']))
			{
				if (count($_POST['delete_flags']) > 0)
				{
					$delete_flags = implode(",", array_map("intval", $_REQUEST['delete_flags']));
					sql_pr("delete from $config[tables_prefix]flags_posts where $table_key_name=? and flag_id in ($delete_flags)", $item_id);
				}
			}

			if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_custom', $_SESSION['permissions']))
			{
				for ($i = 1; $i <= 10; $i++)
				{
					if ($_POST["custom_file{$i}_hash"] != '')
					{
						if (!is_dir("$config[content_path_posts]/$dir_path"))
						{
							mkdir("$config[content_path_posts]/$dir_path");
							chmod("$config[content_path_posts]/$dir_path", 0777);
						}
						if (!is_dir("$config[content_path_posts]/$dir_path/$item_id"))
						{
							mkdir("$config[content_path_posts]/$dir_path/$item_id");
							chmod("$config[content_path_posts]/$dir_path/$item_id", 0777);
						}

						$old_file = $old_post_data["custom_file{$i}"];
						@unlink("$config[content_path_posts]/$dir_path/$item_id/$old_file");

						$processed_filename = get_correct_file_name($_POST["custom_file$i"], "$config[content_path_posts]/$dir_path/$item_id");
						transfer_uploaded_file("custom_file$i", "$config[content_path_posts]/$dir_path/$item_id/$processed_filename");
						$update_array["custom_file$i"] = $processed_filename;
					} elseif ($_POST["custom_file$i"] == '')
					{
						$old_file = $old_post_data["custom_file{$i}"];
						@unlink("$config[content_path_posts]/$dir_path/$item_id/$old_file");
						$update_array["custom_file$i"] = '';
					}
				}
			}

			if (count($update_array) > 0)
			{
				sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

				$update_details = '';
				foreach ($update_array as $k => $v)
				{
					if ($old_post_data[$k] != $update_array[$k])
					{
						$update_details .= "$k, ";
					}
				}
				if (strlen($update_details) > 0)
				{
					$update_details = substr($update_details, 0, -2);
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=12, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
			}

			if (isset($update_array['post_date']) && $old_post_data['post_date'] <> $update_array['post_date'])
			{
				if ($update_array['relative_post_date'] == 0)
				{
					sql_pr("update $config[tables_prefix]comments set added_date=date_add(?, INTERVAL UNIX_TIMESTAMP(added_date) - UNIX_TIMESTAMP(?) SECOND) where object_id=? and object_type_id=12", $update_array['post_date'], $old_post_data['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]comments set added_date=greatest(?, ?) where object_id=? and object_type_id=12 and added_date>?", $update_array['post_date'], date("Y-m-d H:i:s"), $item_id, date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where post_id=? and event_type_id=21", $item_id);
				} else
				{
					sql_pr("update $config[tables_prefix]comments set added_date=? where object_id=? and object_type_id=12", $update_array['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where post_id=? and event_type_id=21", $item_id);
				}
			}

			if (in_array('posts|edit_all', $_SESSION['permissions']))
			{
				if (intval($_POST['is_reviewed']) == 1)
				{
					sql_pr("update $table_name set is_review_needed=0, af_upload_zone=? where $table_key_name=?", intval($_POST['af_upload_zone']), $item_id);
					if (intval($_POST['is_reviewed_activate']) == 1 && $_POST['title'] != '')
					{
						sql_pr("update $table_name set status_id=1 where $table_key_name=? and status_id=0", $item_id);
						$update_array['status_id'] = 1;
					}
				} elseif (intval($_POST['is_reviewed']) == 2)
				{
					if (intval($_POST['is_reviewed_disable_user']) == 1 && in_array('users|edit_all', $_SESSION['permissions']))
					{
						sql_pr("update $config[tables_prefix]users set status_id=0 where status_id!=4 and user_id=(select user_id from $table_name where $table_key_name=?)", $item_id);
					}
					if (intval($_POST['is_reviewed_block_domain']) == 1 && in_array('system|memberzone_settings', $_SESSION['permissions']))
					{
						$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=(select user_id from $table_name where $table_key_name=?)", $item_id));
						if (preg_match("/^[^@]+@([^@]+\.[^@]+)$/is", $email, $temp))
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_domains where domain=?", $temp[1])) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_domains")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_domains set domain=?, sort_id=?", $temp[1], $max_sort_id);
							}
						}
					}
					if (intval($_POST['is_reviewed_block_ip']) == 1 && in_array('system|memberzone_settings', $_SESSION['permissions']))
					{
						$ip = int2ip($old_post_data['ip']);
						if ($ip != '0.0.0.0')
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip)) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_ips")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $ip, $max_sort_id);
							}
						}
					}
					if (intval($_POST['is_reviewed_block_mask']) == 1 && in_array('system|memberzone_settings', $_SESSION['permissions']))
					{
						$ip_mask = ip2mask(int2ip($old_post_data['ip']));
						if ($ip_mask != '0.0.0.*')
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip_mask)) == 0)
							{
								$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_ips")) + 1;
								sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $ip_mask, $max_sort_id);
							}
						}
					}
					if (in_array('posts|delete', $_SESSION['permissions']))
					{
						$delete_post_ids = array();
						if (intval($_POST['is_delete_all_posts_from_user']) == 1)
						{
							$delete_post_ids = mr2array_list(sql_pr("select $table_key_name from $table_name where is_review_needed=1 and $table_key_name<>? and user_id=(select user_id from $table_name where $table_key_name=?)", $item_id, $item_id));
							$delete_post_ids_limit = intval($config['max_delete_on_review']);
							if ($delete_post_ids_limit == 0)
							{
								$delete_post_ids_limit = 30;
							}
							if (count($delete_post_ids) > $delete_post_ids_limit)
							{
								$delete_post_ids = array();
							}
						}
						$delete_post_ids[] = $item_id;

						foreach ($delete_post_ids as $delete_post_id)
						{
							$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id=$delete_post_id and object_type_id=12"));
							$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

							$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name=$delete_post_id")));
							$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name=$delete_post_id")));
							$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name=$delete_post_id")));

							sql("delete from $table_name where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]tags_posts where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]categories_posts where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]models_posts where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]rating_history where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]flags_posts where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]flags_history where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]flags_messages where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]users_events where $table_key_name=$delete_post_id");
							sql("delete from $config[tables_prefix]comments where object_id=$delete_post_id and object_type_id=12");

							if (strlen($list_ids_comments) > 0)
							{
								sql("update $config[tables_prefix]users set
										comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
										comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
									where user_id in ($list_ids_comments)"
								);
							}

							update_tags_posts_totals($list_ids_tags);
							update_categories_posts_totals($list_ids_categories);
							update_models_posts_totals($list_ids_models);

							$custom_files = get_contents_from_dir("$config[content_path_posts]/$dir_path/$delete_post_id", 1);
							foreach ($custom_files as $custom_file)
							{
								@unlink("$config[content_path_posts]/$dir_path/$delete_post_id/$custom_file");
							}
							@rmdir("$config[content_path_posts]/$dir_path/$delete_post_id");

							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=12, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $delete_post_id, date("Y-m-d H:i:s"));
						}
						$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

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
						} else
						{
							return_ajax_success($page_name, 1);
						}
					}
				}
			}

			if (isset($update_array['status_id']) && intval($update_array['status_id']) == 1 && $old_post_data['status_id'] == 0)
			{
				process_activated_posts(array($item_id));
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_tags', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
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
			update_tags_posts_totals($list_ids_tags);
		}

		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_categories', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
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
			update_categories_posts_totals($list_ids_categories);
		}

		if (in_array('posts|edit_all', $_SESSION['permissions']) || in_array('posts|edit_models', $_SESSION['permissions']) || $_POST['action'] == 'add_new_complete')
		{
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
			update_models_posts_totals($list_ids_models);
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
if ($_REQUEST['batch_action'] != '' && !isset($_REQUEST['reorder']) && (count($_REQUEST['row_select']) > 0))
{
	unset($where);
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		$admin_id = intval($_SESSION['userdata']['user_id']);
		$where .= " and admin_user_id=$admin_id ";
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		$where .= " and status_id=0 ";
	}

	$row_select_str = implode(",", array_map("intval", $_REQUEST['row_select']));
	$row_select = mr2array_list(sql("select $table_key_name from $table_name where $table_key_name in ($row_select_str) $where"));
	$row_select_str = implode(",", array_map("intval", $row_select));
	if (count($row_select) == 0)
	{
		return_ajax_success($page_name);
	}

	if ($_REQUEST['batch_action'] == 'delete')
	{
		$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($row_select_str) and object_type_id=12"));
		$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

		$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
		$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name in ($row_select_str)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));

		sql("delete from $table_name where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]tags_posts where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]categories_posts where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]models_posts where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]rating_history where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_posts where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_history where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_messages where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]users_events where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]comments where object_id in ($row_select_str) and object_type_id=12");

		if (strlen($list_ids_comments) > 0)
		{
			sql("update $config[tables_prefix]users set
					comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
					comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
				where user_id in ($list_ids_comments)"
			);
		}

		update_tags_posts_totals($list_ids_tags);
		update_categories_posts_totals($list_ids_categories);
		update_models_posts_totals($list_ids_models);

		foreach ($row_select as $item_id)
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
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		$temp_amount = mr2number(sql("select count(*) from $table_name where (title='' or dir='') and $table_key_name in ($row_select_str)"));
		if ($temp_amount > 0)
		{
			$errors[] = get_aa_error('post_cannot_be_activated', $temp_amount);
		}
		if (!is_array($errors))
		{
			sql("update $table_name set status_id=1 where status_id=0 and $table_key_name in ($row_select_str)");

			$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
			$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name in ($row_select_str)")));
			$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));

			process_activated_posts($row_select);
			update_tags_posts_totals($list_ids_tags);
			update_categories_posts_totals($list_ids_categories);
			update_models_posts_totals($list_ids_models);

			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where status_id=1 and $table_key_name in ($row_select_str)");

		$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
		$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $table_name_models where $table_key_name in ($row_select_str)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));
		update_tags_posts_totals($list_ids_tags);
		update_categories_posts_totals($list_ids_categories);
		update_models_posts_totals($list_ids_models);

		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'mark_reviewed')
	{
		sql("update $table_name set is_review_needed=0 where $table_key_name in ($row_select_str)");
		$_SESSION['messages'][] = $lang['common']['success_message_marked_reviewed'];
		return_ajax_success($page_name);
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);

	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", $item_id));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['admin_user_id'] != $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($_POST['status_id'] != 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	$_POST['dir_path'] = get_dir_by_id($item_id);
	if ($_POST['dir'] != '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $_POST['url_pattern']));
	}

	$_POST['ip'] = int2ip($_POST['ip']);

	$_POST['post_date_option'] = 0;
	if ($config['relative_post_dates'] == 'true')
	{
		if ($_POST['relative_post_date'] != 0)
		{
			$_POST['post_date'] = '0000-00-00 00:00:00';
			$_POST['post_date_option'] = 1;
		} else
		{
			$_POST['relative_post_date'] = '';
		}
	} else
	{
		$_POST['relative_post_date'] = '';
	}

	if ($_POST['user_id'] > 0)
	{
		$_POST['user'] = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", $_POST['user_id']));
	}

	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=$item_id order by id asc"));
	$_POST['models'] = mr2array(sql_pr("select model_id, (select title from $config[tables_prefix]models where model_id=$table_name_models.model_id) as title from $table_name_models where $table_key_name=? order by id asc", $item_id));
	$_POST['tags'] = implode(", ", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as tag from $table_name_tags where $table_name_tags.$table_key_name=? order by id asc", $item_id)));
	$_POST['flags'] = mr2array(sql_pr("select flag_id, title, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_posts where $config[tables_prefix]flags_posts.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_posts.$table_key_name=?) as votes from $config[tables_prefix]flags where group_id=4 having votes>0 order by title asc", $item_id));

	if ($_POST['connected_video_id'] > 0)
	{
		$connected_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $_POST['connected_video_id']));
		if ($connected_video['video_id'] > 0)
		{
			if ($connected_video['title'] != '')
			{
				$_POST['connected_video_title'] = "$connected_video[video_id] / $connected_video[title]";
			} else
			{
				$_POST['connected_video_title'] = "$connected_video[video_id]";
			}
		}
	}

	if ($_POST['is_review_needed'] == 1)
	{
		$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=?", $_POST['user_id']));
		if (preg_match("/^[^@]+@([^@]+\.[^@]+)$/is", $email, $temp))
		{
			$_POST['user_domain'] = "@$temp[1]";
			$_POST['user_domain_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_domains where domain=?", $temp[1]));
		}
		$ip_mask = ip2mask($_POST['ip']);
		$_POST['ip_mask'] = $ip_mask;
		$_POST['ip_mask_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip_mask));
		$_POST['ip_blocked'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $_POST['ip']));
		$_POST['other_posts_need_review'] = mr2number(sql_pr("select count(*) from $table_name where user_id=? and $table_key_name<>? and is_review_needed=1", $_POST['user_id'], $_POST[$table_key_name]));
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['user'] = $options['DEFAULT_USER_IN_ADMIN_ADD_POST'];
	$_POST['status_id'] = $options['DEFAULT_STATUS_IN_ADMIN_ADD_POST'];
	if ($options['USE_POST_DATE_RANDOMIZATION_POST'] == '0')
	{
		$_POST['post_date'] = date("Y-m-d");
	} elseif ($options['USE_POST_DATE_RANDOMIZATION_POST'] == '1')
	{
		$_POST['post_date'] = date("Y-m-d H:i", strtotime(date("Y-m-d")) + mt_rand(0, 86399));
	} elseif ($options['USE_POST_DATE_RANDOMIZATION_POST'] == '2')
	{
		$_POST['post_date'] = date("Y-m-d H:i");
	}

	if ($locked_post_type_id > 0)
	{
		$_POST['post_type_id'] = $locked_post_type_id;
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
	$item_id = $data[$k][$table_key_name];

	if ($_SESSION['save'][$page_name]['grid_columns']['categories'] == 1)
	{
		$data[$k]['categories'] = mr2array(sql_pr("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories inner join $table_name_categories on $config[tables_prefix]categories.category_id=$table_name_categories.category_id where $table_name_categories.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_categories.id asc"));
	}
	if ($_SESSION['save'][$page_name]['grid_columns']['models'] == 1)
	{
		$data[$k]['models'] = mr2array(sql_pr("select $config[tables_prefix]models.model_id as id, $config[tables_prefix]models.title from $config[tables_prefix]models inner join $table_name_models on $config[tables_prefix]models.model_id=$table_name_models.model_id where $table_name_models.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_models.id asc"));
	}
	if ($_SESSION['save'][$page_name]['grid_columns']['tags'] == 1)
	{
		$data[$k]['tags'] = mr2array(sql_pr("select $config[tables_prefix]tags.tag_id as id, $config[tables_prefix]tags.tag as title from $config[tables_prefix]tags inner join $table_name_tags on $config[tables_prefix]tags.tag_id=$table_name_tags.tag_id where $table_name_tags.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_tags.id asc"));
	}

	if ($data[$k]['dir'] != '')
	{
		$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $data[$k]['url_pattern']));
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$post_types_with_custom_fields = array();
foreach ($list_post_types as $post_type)
{
	foreach ($list_custom_fields as $custom_field)
	{
		if (isset($custom_field['enabled'][$post_type['post_type_id']]))
		{
			$post_types_with_custom_fields[] = $post_type['post_type_id'];
			break;
		}
	}
}

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
if (in_array('system|administration', $_SESSION['permissions']))
{
	$smarty->assign('list_admin_users', mr2array(sql("select user_id, login from $config[tables_prefix]admin_users order by login asc")));
} else
{
	$smarty->assign('list_admin_users', mr2array(sql_pr("select user_id, login from $config[tables_prefix]admin_users where login=?", $_SESSION['userdata']['login'])));
}
$smarty->assign('list_flags_posts', mr2array(sql("select * from $config[tables_prefix]flags where group_id=4 order by title asc")));
$smarty->assign('list_types', $list_post_types);
$smarty->assign('list_custom_fields', $list_custom_fields);
$smarty->assign('post_types_with_custom_fields', $post_types_with_custom_fields);
$smarty->assign('locked_post_type_support', $locked_post_type_support);
$smarty->assign('locked_post_type_id', $locked_post_type_id);
$smarty->assign('locked_post_type', $locked_post_type);
$smarty->assign('options', $options);
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
$smarty->assign('template', "posts.tpl");
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['title'] != '' ? $_POST['title'] : $_POST[$table_key_name]), $lang['posts']['post_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['posts']['post_add']);
} else
{
	if ($locked_post_type_id > 0)
	{
		$smarty->assign('page_title', $locked_post_type['title']);
	} else
	{
		$smarty->assign('page_title', $lang['posts']['submenu_option_posts_list']);
	}
}

$smarty->display("layout.tpl");
