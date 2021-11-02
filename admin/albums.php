<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_admin.php');
require_once('include/functions_base.php');
require_once('include/functions_screenshots.php');
require_once('include/functions.php');
require_once('include/check_access.php');
require_once('include/pclzip.lib.php');
require_once("include/database_selectors.php");

// =====================================================================================================================
// initialization
// =====================================================================================================================

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));
$options = get_options();

for ($i = 1; $i <= 3; $i++)
{
	if ($options["ALBUM_FIELD_{$i}_NAME"] == '')
	{
		$options["ALBUM_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['albums']['album_field_status_disabled'],
	1 => $lang['albums']['album_field_status_active'],
	2 => $lang['albums']['album_field_status_error'],
	3 => $lang['albums']['album_field_status_in_process'],
	4 => $lang['albums']['album_field_status_deleting'],
	5 => $lang['albums']['album_field_status_deleted'],
);

$list_type_values = array(
	0 => $lang['albums']['album_field_type_public'],
	1 => $lang['albums']['album_field_type_private'],
	2 => $lang['albums']['album_field_type_premium'],
);

$list_access_level_values = array(
	0 => $lang['albums']['album_field_access_level_inherit'],
	1 => $lang['albums']['album_field_access_level_all'],
	2 => $lang['albums']['album_field_access_level_members'],
	3 => $lang['albums']['album_field_access_level_premium'],
);

$list_upload_zone_values = array(
	0 => $lang['albums']['album_field_af_upload_zone_site'],
	1 => $lang['albums']['album_field_af_upload_zone_memberarea'],
);

$table_fields = array();
$table_fields[] = array('id' => 'album_id',            'title' => $lang['albums']['album_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'thumb',               'title' => $lang['albums']['album_field_thumb'],          'is_default' => 0, 'type' => 'thumb', 'link' => 'albums.php?action=manage_images&item_id=%id%', 'link_id' => 'album_id', 'link_is_editor' => 1);
$table_fields[] = array('id' => 'title',               'title' => $lang['albums']['album_field_title'],          'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'dir',                 'title' => $lang['albums']['album_field_directory'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',         'title' => $lang['albums']['album_field_description'],    'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'user',                'title' => $lang['albums']['album_field_user'],           'is_default' => 1, 'type' => 'user');
if (in_array('system|administration',$_SESSION['permissions']))
{
	$table_fields[] = array('id' => 'admin_user',      'title' => $lang['albums']['album_field_admin'],          'is_default' => 0, 'type' => 'admin');
}
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',              'title' => $lang['albums']['album_field_ip'],             'is_default' => 0, 'type' => 'ip');
}
if (is_array($config['advanced_filtering']) && in_array('upload_zone', $config['advanced_filtering']))
{
	$table_fields[] = array('id' => 'af_upload_zone',  'title' => $lang['albums']['album_field_af_upload_zone'], 'is_default' => 0, 'type' => 'choice', 'values' => $list_upload_zone_values);
}
$table_fields[] = array('id' => 'status_id',           'title' => $lang['albums']['album_field_status'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'is_private',          'title' => $lang['albums']['album_field_type'],           'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values);
$table_fields[] = array('id' => 'access_level_id',     'title' => $lang['albums']['album_field_access_level'],   'is_default' => 0, 'type' => 'choice', 'values' => $list_access_level_values);
$table_fields[] = array('id' => 'tokens_required',     'title' => $lang['albums']['album_field_tokens_cost'],    'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'rating',              'title' => $lang['albums']['album_field_rating'],         'is_default' => 1, 'type' => 'float');
$table_fields[] = array('id' => 'album_viewed',        'title' => $lang['albums']['album_field_visits'],         'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'album_viewed_unique', 'title' => $lang['albums']['album_field_unique_visits'],  'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'photos_amount',       'title' => $lang['albums']['album_field_images'],         'is_default' => 1, 'type' => 'number', 'link' => 'albums.php?action=manage_images&item_id=%id%', 'link_id' => 'album_id', 'link_is_editor' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'server_group',        'title' => $lang['albums']['album_field_server_group'],   'is_default' => 0, 'type' => 'refid', 'link' => 'servers.php?action=change_group&item_id=%id%', 'link_id' => 'server_group_id', 'permission' => 'system|servers');
$table_fields[] = array('id' => 'admin_flag',          'title' => $lang['albums']['album_field_admin_flag'],     'is_default' => 0, 'type' => 'refid', 'link' => 'flags.php?action=change&item_id=%id%', 'link_id' => 'admin_flag_id', 'permission' => 'flags|view');
$table_fields[] = array('id' => 'content_source',      'title' => $lang['albums']['album_field_content_source'], 'is_default' => 0, 'type' => 'refid', 'link' => 'content_sources.php?action=change&item_id=%id%', 'link_id' => 'content_source_id', 'permission' => 'content_sources|view');
$table_fields[] = array('id' => 'tags',                'title' => $lang['albums']['album_field_tags'],           'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view');
$table_fields[] = array('id' => 'categories',          'title' => $lang['albums']['album_field_categories'],     'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'models',              'title' => $lang['albums']['album_field_models'],         'is_default' => 0, 'type' => 'list', 'link' => 'models.php?action=change&item_id=%id%', 'permission' => 'models|view');

for ($i = 1; $i <= 3; $i++)
{
	if ($options["ENABLE_ALBUM_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}",  'title' => $options["ALBUM_FIELD_{$i}_NAME"],             'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => "gallery_url",         'title' => $lang['albums']['album_field_gallery_url'],    'is_default' => 0, 'type' => 'url');
$table_fields[] = array('id' => 'comments_count',      'title' => $lang['albums']['album_field_comments'],       'is_default' => 0, 'type' => 'number', 'link' => 'comments.php?no_filter=true&se_object_type_id=2&se_object_id=%id%', 'link_id' => 'album_id', 'permission' => 'users|manage_comments', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'favourites_count',    'title' => $lang['albums']['album_field_favourites'],     'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'purchases_count',     'title' => $lang['albums']['album_field_purchases'],      'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'post_date',           'title' => $lang['albums']['album_field_post_date'],      'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'added_date',          'title' => $lang['albums']['album_field_added_date'],     'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'last_time_view_date', 'title' => $lang['albums']['album_field_last_view_date'], 'is_default' => 0, 'type' => 'datetime');
$table_fields[] = array('id' => 'is_locked',           'title' => $lang['albums']['album_field_lock_website'],   'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'is_review_needed',    'title' => $lang['albums']['album_field_needs_review'],   'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'has_errors',          'title' => $lang['albums']['album_field_has_errors'],     'is_default' => 0, 'type' => 'bool', 'ifhighlight' => 'has_errors');

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

$sort_def_field = "album_id";
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
$search_fields[] = array('id' => 'album_id',     'title' => $lang['albums']['album_field_id']);
$search_fields[] = array('id' => 'title',        'title' => $lang['albums']['album_field_title']);
$search_fields[] = array('id' => 'dir',          'title' => $lang['albums']['album_field_directory']);
$search_fields[] = array('id' => 'description',  'title' => $lang['albums']['album_field_description']);
$search_fields[] = array('id' => 'website_link', 'title' => $lang['albums']['album_field_website_link']);
$search_fields[] = array('id' => 'gallery_url',  'title' => $lang['albums']['album_field_gallery_url']);
$search_fields[] = array('id' => 'custom',       'title' => $lang['common']['dg_filter_search_in_custom']);
if (count($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name = "$config[tables_prefix]albums";
$table_key_name = "album_id";
$table_selector = "$table_name.*, $table_name.rating / $table_name.rating_amount as rating";
$table_projector = "$table_name";

$table_name_categories = "$config[tables_prefix]categories_albums";
$table_name_tags = "$config[tables_prefix]tags_albums";
$table_name_models = "$config[tables_prefix]models_albums";

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
	$_SESSION['save'][$page_name]['se_ids'] = '';
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_is_private'] = '';
	$_SESSION['save'][$page_name]['se_access_level_id'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_review_flag'] = '';
	$_SESSION['save'][$page_name]['se_posted'] = '';
	$_SESSION['save'][$page_name]['se_admin_user_id'] = '';
	$_SESSION['save'][$page_name]['se_is_locked'] = '';
	$_SESSION['save'][$page_name]['se_has_errors'] = '';
	$_SESSION['save'][$page_name]['se_content_source'] = '';
	$_SESSION['save'][$page_name]['se_category'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
	$_SESSION['save'][$page_name]['se_model'] = '';
	$_SESSION['save'][$page_name]['se_flag_id'] = '';
	$_SESSION['save'][$page_name]['se_flag_values_amount'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_show_id'] = '';
	$_SESSION['save'][$page_name]['se_storage_group_id'] = '';
	$_SESSION['save'][$page_name]['se_post_date_from'] = '';
	$_SESSION['save'][$page_name]['se_post_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_ids']))
	{
		$_SESSION['save'][$page_name]['se_ids'] = trim($_GET['se_ids']);
	}
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_is_private']))
	{
		$_SESSION['save'][$page_name]['se_is_private'] = trim($_GET['se_is_private']);
	}
	if (isset($_GET['se_access_level_id']))
	{
		$_SESSION['save'][$page_name]['se_access_level_id'] = trim($_GET['se_access_level_id']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_posted']))
	{
		$_SESSION['save'][$page_name]['se_posted'] = trim($_GET['se_posted']);
	}
	if (isset($_GET['se_review_flag']))
	{
		$_SESSION['save'][$page_name]['se_review_flag'] = intval($_GET['se_review_flag']);
	}
	if (isset($_GET['se_admin_user_id']))
	{
		$_SESSION['save'][$page_name]['se_admin_user_id'] = intval($_GET['se_admin_user_id']);
	}
	if (isset($_GET['se_is_locked']))
	{
		$_SESSION['save'][$page_name]['se_is_locked'] = intval($_GET['se_is_locked']);
	}
	if (isset($_GET['se_has_errors']))
	{
		$_SESSION['save'][$page_name]['se_has_errors'] = intval($_GET['se_has_errors']);
	}
	if (isset($_GET['se_content_source']))
	{
		$_SESSION['save'][$page_name]['se_content_source'] = trim($_GET['se_content_source']);
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
	if (isset($_GET['se_show_id']))
	{
		$_SESSION['save'][$page_name]['se_show_id'] = trim($_GET['se_show_id']);
	}
	if (isset($_GET['se_storage_group_id']))
	{
		$_SESSION['save'][$page_name]['se_storage_group_id'] = intval($_GET['se_storage_group_id']);
	}

	if (isset($_GET['se_post_date_from_Day']) && isset($_GET['se_post_date_from_Month']) && isset($_GET['se_post_date_from_Year']))
	{
		if (intval($_GET['se_post_date_from_Day']) > 0 && intval($_GET['se_post_date_from_Month']) > 0 && intval($_GET['se_post_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_post_date_from'] = intval($_GET['se_post_date_from_Year']) . "-" . intval($_GET['se_post_date_from_Month']) . "-" . intval($_GET['se_post_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_post_date_from'] = "";
		}
	}
	if (isset($_GET['se_post_date_to_Day']) && isset($_GET['se_post_date_to_Month']) && isset($_GET['se_post_date_to_Year']))
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

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1 || $field['id'] == $_SESSION['save'][$page_name]['sort_by'])
	{
		if ($field['id'] == 'user')
		{
			$table_selector .= ", $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id";
			$table_projector .= " left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";
		}
		if ($field['id'] == 'admin_user')
		{
			$table_selector .= ", $config[tables_prefix]admin_users.login as admin_user, $config[tables_prefix]admin_users.is_superadmin as admin_user_is_superadmin";
			$table_projector .= " left join $config[tables_prefix]admin_users on $config[tables_prefix]admin_users.user_id=$table_name.admin_user_id";
		}
		if ($field['id'] == 'content_source')
		{
			$table_selector .= ", $config[tables_prefix]content_sources.title as content_source";
			$table_projector .= " left join $config[tables_prefix]content_sources on $config[tables_prefix]content_sources.content_source_id=$table_name.content_source_id";
		}
		if ($field['id'] == 'admin_flag')
		{
			$table_selector .= ", $config[tables_prefix]flags.title as admin_flag";
			$table_projector .= " left join $config[tables_prefix]flags on $config[tables_prefix]flags.flag_id=$table_name.admin_flag_id";
		}
		if ($field['id'] == 'server_group')
		{
			$table_selector .= ", $config[tables_prefix]admin_servers_groups.title as server_group";
			$table_projector .= " left join $config[tables_prefix]admin_servers_groups on $config[tables_prefix]admin_servers_groups.group_id=$table_name.server_group_id";
		}
		if ($field['id'] == 'comments_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=2) as comments_count";
		}
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
			} elseif ($search_field['id'] == 'website_link')
			{
				if (is_url($q))
				{
					$search_id = 0;
					$search_dir = '';

					$pattern_check = str_replace('%ID%', '([0-9]+)', str_replace('%DIR%', '(.*)', $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
					preg_match("|$pattern_check|is", $q, $temp);
					if (strpos($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'], '%ID%') !== false)
					{
						if (strpos($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'], '%DIR%') === false)
						{
							$search_id = intval($temp[1]);
						} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'], '%ID%') > strpos($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'], '%DIR%'))
						{
							$search_id = intval($temp[2]);
						} else
						{
							$search_id = intval($temp[1]);
						}
					} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'], '%DIR%') !== false)
					{
						$search_dir = trim($temp[1]);
					}
					if ($search_id > 0)
					{
						$where_search .= " or $table_name.$table_key_name='$search_id'";
					} elseif ($search_dir != '')
					{
						$where_search .= " or $table_name.dir='$search_dir'";
					}
				}
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= 3; $i++)
				{
					if ($options["ENABLE_ALBUM_FIELD_{$i}"] == 1)
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

if ($_SESSION['save'][$page_name]['se_ids'] != '')
{
	$search_ids_array = array_map('intval', array_map('trim', explode(',', $_SESSION['save'][$page_name]['se_ids'])));
	$where .= "and $table_name.$table_key_name in (" . implode(',', $search_ids_array) . ")";
	$table_filtered = 1;
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

if ($_SESSION['save'][$page_name]['se_content_source'] != '')
{
	$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_SESSION['save'][$page_name]['se_content_source']));
	if ($content_source_id == 0)
	{
		$where .= " and 0=1";
	} else
	{
		$where .= " and $table_name.content_source_id=$content_source_id";
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

if ($_SESSION['save'][$page_name]['se_is_private'] == '0')
{
	$where .= " and $table_name.is_private=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_private'] == '1')
{
	$where .= " and $table_name.is_private=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_private'] == '2')
{
	$where .= " and $table_name.is_private=2";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_access_level_id'] == '0')
{
	$where .= " and $table_name.access_level_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '1')
{
	$where .= " and $table_name.access_level_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '2')
{
	$where .= " and $table_name.access_level_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_access_level_id'] == '3')
{
	$where .= " and $table_name.access_level_id=3";
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
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '2')
{
	$where .= " and $table_name.status_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '3')
{
	$where .= " and $table_name.status_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '4')
{
	$where .= " and $table_name.status_id=4";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '5')
{
	$where .= " and $table_name.status_id=5";
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

if ($_SESSION['save'][$page_name]['se_admin_user_id'] > 0)
{
	$where .= " and $table_name.admin_user_id=" . intval($_SESSION['save'][$page_name]['se_admin_user_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_locked'] == '1')
{
	$where .= " and $table_name.is_locked=1";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_has_errors'] == '1')
{
	$where .= " and $table_name.has_errors=1";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/title':
	case 'empty/description':
	case 'empty/gallery_url':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/content_source':
		$where .= " and $table_name.content_source_id=0";
		$table_filtered = 1;
		break;
	case 'empty/admin':
		$where .= " and $table_name.admin_user_id=0";
		$table_filtered = 1;
		break;
	case 'empty/admin_flag':
		$where .= " and $table_name.admin_flag_id=0";
		$table_filtered = 1;
		break;
	case 'empty/tokens_required':
		$where .= " and $table_name.tokens_required=0";
		$table_filtered = 1;
		break;
	case 'empty/album_viewed':
		$where .= " and $table_name.album_viewed=0";
		$table_filtered = 1;
		break;
	case 'empty/album_viewed_unique':
		$where .= " and $table_name.album_viewed_unique=0";
		$table_filtered = 1;
		break;
	case 'empty/comments':
		$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=2)=0";
		$table_filtered = 1;
		break;
	case 'empty/favourites':
		$where .= " and favourites_count=0";
		$table_filtered = 1;
		break;
	case 'empty/purchases':
		$where .= " and purchases_count=0";
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
	case 'filled/gallery_url':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/content_source':
		$where .= " and $table_name.content_source_id>0";
		$table_filtered = 1;
		break;
	case 'filled/admin':
		$where .= " and $table_name.admin_user_id>0";
		$table_filtered = 1;
		break;
	case 'filled/admin_flag':
		$where .= " and $table_name.admin_flag_id>0";
		$table_filtered = 1;
		break;
	case 'filled/tokens_required':
		$where .= " and $table_name.tokens_required>0";
		$table_filtered = 1;
		break;
	case 'filled/album_viewed':
		$where .= " and $table_name.album_viewed>0";
		$table_filtered = 1;
		break;
	case 'filled/album_viewed_unique':
		$where .= " and $table_name.album_viewed_unique>0";
		$table_filtered = 1;
		break;
	case 'filled/comments':
		$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=2)>0";
		$table_filtered = 1;
		break;
	case 'filled/favourites':
		$where .= " and favourites_count>0";
		$table_filtered = 1;
		break;
	case 'filled/purchases':
		$where .= " and purchases_count>0";
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

if ($_SESSION['save'][$page_name]['se_show_id'] == 13)
{
	$where .= " and $table_name.admin_user_id>0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 14)
{
	$where .= " and $table_name.admin_user_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_show_id'] == 15)
{
	$where .= " and $table_name.admin_user_id=0 and $config[tables_prefix]users.status_id=6";
	$table_filtered = 1;
} elseif (strlen($_SESSION['save'][$page_name]['se_show_id']) > 0)
{
	if (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wl/') === 0)
	{
		$lang_existing = array();
		foreach ($languages as $language)
		{
			if (str_replace('wl/', '', $_SESSION['save'][$page_name]['se_show_id']) == $language['code'])
			{
				$lang_existing = $language;
				break;
			}
		}
		if ($lang_existing <> '')
		{
			if ($lang_existing['translation_scope_albums'] == 0)
			{
				if ($lang_existing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
				} else
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
				}
			} else
			{
				if ($lang_existing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='')";
				} else
				{
					$where .= " and ($table_name.title_$lang_existing[code]!='')";
				}
			}
			$table_filtered = 1;
		} else
		{
			$_SESSION['save'][$page_name]['se_show_id'] = '';
		}
	} elseif (strpos($_SESSION['save'][$page_name]['se_show_id'], 'wol/') === 0)
	{
		$lang_missing = array();
		foreach ($languages as $language)
		{
			if (str_replace('wol/', '', $_SESSION['save'][$page_name]['se_show_id']) == $language['code'])
			{
				$lang_missing = $language;
				break;
			}
		}
		if ($lang_missing <> '')
		{
			if ($lang_missing['translation_scope_albums'] == 0)
			{
				if ($lang_missing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
				} else
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
				}
			} else
			{
				if ($lang_missing['is_directories_localize'] == 1)
				{
					$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='')";
				} else
				{
					$where .= " and ($table_name.title_$lang_missing[code]='')";
				}
			}
			$table_filtered = 1;
		} else
		{
			$_SESSION['save'][$page_name]['se_show_id'] = '';
		}
	}
}

if ($_SESSION['save'][$page_name]['se_storage_group_id'] > 0)
{
	$where .= " and $table_name.server_group_id=" . intval($_SESSION['save'][$page_name]['se_storage_group_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_posted'] == "yes")
{
	$where .= " and $database_selectors[where_albums]";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_posted'] == "no")
{
	$where .= " and not ($database_selectors[where_albums])";
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

if ($_SESSION['save'][$page_name]['se_flag_id'] > 0)
{
	$flag_amount = max(1, intval($_SESSION['save'][$page_name]['se_flag_values_amount']));
	$where .= " and ($table_name.admin_flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']) . " or (select sum(votes) from $config[tables_prefix]flags_albums where album_id=$table_name.album_id and flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']) . ")>=$flag_amount)";
	$table_filtered = 1;
}

if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and $table_name.admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and $table_name.status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and $table_name.admin_flag_id>0 and $table_name.admin_flag_id in ($flags_access_limit)";
}
if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($config['relative_post_dates'] == 'true' && $sort_by == 'post_date')
{
	$sort_by = "$table_name.post_date " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.relative_post_date";
} elseif ($sort_by == 'rating')
{
	$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.rating_amount";
} elseif ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'admin_user')
{
	$sort_by = "$config[tables_prefix]admin_users.login";
} elseif ($sort_by == 'content_source')
{
	$sort_by = "$config[tables_prefix]content_sources.title";
} elseif ($sort_by == 'admin_flag')
{
	$sort_by = "$config[tables_prefix]flags.title";
} elseif ($sort_by == 'server_group')
{
	$sort_by = "$config[tables_prefix]admin_servers_groups.title";
} elseif ($sort_by == 'comments_count')
{
	$sort_by = "(select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=2)";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// mark and change deleted
// =====================================================================================================================

if ($_POST['action'] == 'mark_deleted_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (intval($_POST['delete_send_email']) == 1)
	{
		validate_field('empty', $_POST['delete_send_email_to'], $lang['albums']['album_field_delete_email_to']);
		validate_field('empty', $_POST['delete_send_email_subject'], $lang['albums']['album_field_delete_email_subject']);
		validate_field('empty', $_POST['delete_send_email_body'], $lang['albums']['album_field_delete_email_body']);
	}

	if (!is_array($errors))
	{
		$delete_id = intval($_POST['delete_id']);
		if ($delete_id < 1 || !is_file("$config[temporary_path]/delete-albums-$delete_id.dat"))
		{
			return_ajax_success($page_name, 1);
			die;
		}
		$delete_data = @unserialize(file_get_contents("$config[temporary_path]/delete-albums-$delete_id.dat"));
		if (!is_array($delete_data))
		{
			return_ajax_success($page_name, 1);
			die;
		}
		unlink("$config[temporary_path]/delete-albums-$delete_id.dat");

		$delete_ids_str = implode(",", $delete_data['ids']);

		$where_deleting = '';
		if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
		{
			$admin_id = intval($_SESSION['userdata']['user_id']);
			$where_deleting .= " and admin_user_id=$admin_id ";
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
		{
			$where_deleting .= " and status_id=0 ";
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			$where_deleting .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
		}

		$delete_album_ids = mr2array_list(sql("select album_id from $table_name where status_id in (0,1,2) and (album_id in ($delete_ids_str)) $where_deleting"));

		if (intval($_POST['delete_send_email']) == 1)
		{
			$delete_album_urls = '';
			foreach ($delete_album_ids as $album_id)
			{
				$album_data = mr2array_single(sql_pr("select album_id, dir from $table_name where album_id=?", $album_id));
				$delete_album_urls .= "\n$config[project_url]/" . str_replace("%ID%", $album_data['album_id'], str_replace("%DIR%", $album_data['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			}
			$delete_album_urls = trim($delete_album_urls);

			if (!send_mail($_POST['delete_send_email_to'], $_POST['delete_send_email_subject'], str_replace("%URLS%", $delete_album_urls, $_POST['delete_send_email_body']), $config['default_email_headers']))
			{
				$errors[] = get_aa_error('failed_to_send_email');
				return_ajax_errors($errors);
			}
			$_SESSION['save'][$page_name]['delete_send_email_subject'] = $_POST['delete_send_email_subject'];
			$_SESSION['save'][$page_name]['delete_send_email_body'] = $_POST['delete_send_email_body'];
		}

		foreach ($delete_album_ids as $album_id)
		{
			sql_pr("update $table_name set status_id=5, delete_reason=? where $table_key_name=?", trim($_POST['delete_reason']), $album_id);
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, action_details='status_id, delete_reason', object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $album_id, date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $album_id, serialize(array('soft_delete' => 1)), date("Y-m-d H:i:s"));
		}

		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name, 1);
	} else
	{
		return_ajax_errors($errors);
	}
	die;
}

if ($_POST['action'] == 'change_deleted_complete')
{
	$item_id = intval($_POST['item_id']);

	$old_album_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($old_album_data['admin_user_id'] != $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($old_album_data['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($old_album_data['admin_flag_id'] == 0 || !in_array($old_album_data['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	sql_pr("update $table_name set delete_reason=? where $table_key_name=?", trim($_POST['delete_reason']), $item_id);
	sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, action_details='delete_reason', object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

	$_SESSION['messages'][] = $lang['common']['success_message_modified'];
	return_ajax_success($page_name, 1);
	die;
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

	settype($_POST['custom1'], "string");
	settype($_POST['custom2'], "string");
	settype($_POST['custom3'], "string");
	settype($_POST['model_ids'], "array");
	settype($_POST['category_ids'], "array");
	settype($_POST['delete_flags'], "array");

	$item_id = intval($_POST['item_id']);

	if (intval($_POST['status_id']) == 1)
	{
		if (validate_field('empty', $_POST['title'], $lang['albums']['album_field_title']))
		{
			if (intval($options['ALBUM_CHECK_DUPLICATE_TITLES']) == 1)
			{
				validate_field('uniq', $_POST['title'], $lang['albums']['album_field_title'], array('field_name_in_base' => 'title'));
			}
		}
	} elseif ($_POST['title'] != '' && intval($options['ALBUM_CHECK_DUPLICATE_TITLES']) == 1)
	{
		validate_field('uniq', $_POST['title'], $lang['albums']['album_field_title'], array('field_name_in_base' => 'title'));
	}
	if ($_POST['dir'] <> '' && $_POST['action'] == 'change_complete')
	{
		if ($options['ALBUM_REGENERATE_DIRECTORIES'] != 1)
		{
			validate_field('uniq', $_POST['dir'], $lang['albums']['album_field_directory'], array('field_name_in_base' => 'dir'));
		}
	}
	if (intval($_POST['post_date_option']) == 0)
	{
		validate_field('date', 'post_date_', $lang['albums']['album_field_post_date']);
	} else
	{
		validate_field('empty_int_ext', $_POST['relative_post_date'], $lang['albums']['album_field_post_date']);
	}
	if (validate_field('empty', $_POST['user'], $lang['albums']['album_field_user']))
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
		{
			$errors[] = get_aa_error('invalid_user', $lang['albums']['album_field_user']);
		}
	}
	if ($_POST['tokens_required'] <> '' && $_POST['tokens_required'] <> '0')
	{
		validate_field('empty_int', $_POST['tokens_required'], $lang['albums']['album_field_tokens_cost']);
	}

	if ($_POST['action'] == 'add_new_complete')
	{
		$min_image_size = array(0 => 0, 1 => 0);
		if ($options['ALBUM_VALIDATE_IMAGE_SIZES'] == 1)
		{
			$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_albums where status_id in (0,1) and aspect_ratio_id not in (3,4,5)"));
			foreach ($sizes as $size)
			{
				$temp_size = explode("x", $size);
				if (intval($temp_size[0]) > $min_image_size[0])
				{
					$min_image_size[0] = intval($temp_size[0]);
				}
				if (intval($temp_size[1]) > $min_image_size[1])
				{
					$min_image_size[1] = intval($temp_size[1]);
				}
			}
		}
		$ext = strtolower(end(explode(".", $_POST['images'])));
		if ($ext == 'zip')
		{
			validate_field('archive_or_images', 'images', $lang['albums']['album_field_images'], array('is_required' => 1, 'image_types' => $config['image_allowed_ext'], 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			$source_file = 'source_1.zip';
		} else
		{
			validate_field('file', 'images', $lang['albums']['album_field_images'], array('is_required' => 1, 'allowed_ext' => $config['image_allowed_ext'], 'is_image' => '1', 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			$source_file = 'source_1.jpg';
		}
		for ($i = 2; $i <= 100; $i++)
		{
			if (isset($_POST["image_{$i}_hash"]))
			{
				validate_field('file', "image_{$i}", str_replace("%1%", $i, $lang['albums']['album_field_image_next']), array('is_required' => 0, 'allowed_ext' => $config['image_allowed_ext'], 'is_image' => '1', 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			}
		}

		if (isset($_POST["preview_hash"]))
		{
			validate_field('file', "preview", $lang['albums']['album_field_image_preview'], array('is_required' => 0, 'allowed_ext' => $config['image_allowed_ext'], 'is_image' => '1'));
		}
	} else
	{
		$old_album_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
		if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
		{
			if ($old_album_data['admin_user_id'] <> $_SESSION['userdata']['user_id'])
			{
				header("Location: error.php?error=permission_denied");
				die;
			}
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
		{
			if ($old_album_data['status_id'] <> 0)
			{
				header("Location: error.php?error=permission_denied");
				die;
			}
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			if ($old_album_data['admin_flag_id'] == 0 || !in_array($old_album_data['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
			{
				header("Location: error.php?error=permission_denied");
				die;
			}
		}

		if (intval($options['ALBUM_CHECK_DUPLICATE_TITLES']) == 1)
		{
			foreach ($languages as $language)
			{
				if (in_array("localization|$language[code]", $_SESSION['permissions']))
				{
					if (isset($_POST["title_$language[code]"]) && $_POST["title_$language[code]"] != '')
					{
						validate_field('uniq', $_POST["title_$language[code]"], str_replace('%1%', $language['title'], $lang['common']['title_translation']), array('field_name_in_base' => "title_$language[code]"));
					}
				}
			}
		}
	}

	if (!is_array($errors))
	{
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
			$_POST['post_date'] = date("Y-m-d H:i:s", strtotime(intval($_POST['post_date_Year']) . "-" . intval($_POST['post_date_Month']) . "-" . intval($_POST['post_date_Day']) . " " . intval($post_time[0]) . ":" . intval($post_time[1]) . ":00"));
			$_POST['relative_post_date'] = 0;
		} else
		{
			$_POST['post_date'] = '1971-01-01 00:00:00';
		}

		$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user']));
		if ($_POST['action'] == 'add_new_complete')
		{
			$_POST['dir'] = get_correct_dir_name($_POST['title']);
			if ($_POST['dir'] <> '')
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

			$item_id = sql_insert("insert into $table_name set user_id=?, admin_user_id=?, post_date=?, relative_post_date=?, admin_flag_id=?, content_source_id=?, is_private=?, access_level_id=?, tokens_required=?, status_id=3, title=?, dir=?, description=?, rating=?, rating_amount=1, custom1=?, custom2=?, custom3=?, added_date=?",
				$user_id, $_SESSION['userdata']['user_id'], $_POST['post_date'], intval($_POST['relative_post_date']), intval($_POST['admin_flag_id']), intval($_POST['content_source_id']), intval($_POST['is_private']), intval($_POST['access_level_id']), intval($_POST['tokens_required']), $_POST['title'], $_POST['dir'], $_POST['description'], intval($options['ALBUM_INITIAL_RATING']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], date("Y-m-d H:i:s")
			);

			$dir_path = get_dir_by_id($item_id);
			if (!is_dir("$config[content_path_albums_sources]/$dir_path"))
			{
				mkdir("$config[content_path_albums_sources]/$dir_path", 0777);
				chmod("$config[content_path_albums_sources]/$dir_path", 0777);
			}
			if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
			{
				mkdir("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
				chmod("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
			}

			if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
			{
				log_album("ERROR  Failed to create directory: $config[content_path_albums_sources]/$dir_path/$item_id", $item_id);
			}

			$source_files = array();

			if (!transfer_uploaded_file('images', "$config[content_path_albums_sources]/$dir_path/$item_id/$source_file"))
			{
				log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/$source_file", $item_id);
			}
			$source_files[] = $source_file;

			for ($i = 2; $i <= 100; $i++)
			{
				if (isset($_POST["image_{$i}_hash"]) && $_POST["image_{$i}_hash"] <> '')
				{
					if (!transfer_uploaded_file("image_{$i}", "$config[content_path_albums_sources]/$dir_path/$item_id/source_$i.jpg"))
					{
						log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/source_$i.jpg", $item_id);
					}
					$source_files[] = "source_$i.jpg";
				}
			}
			if (isset($_POST["preview_hash"]) && $_POST["preview_hash"] <> '')
			{
				if (!transfer_uploaded_file("preview", "$config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg"))
				{
					log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg", $item_id);
				}
			}

			$background_task = array();
			$background_task['source_files'] = $source_files;
			$background_task['status_id'] = intval($_POST['status_id']);
			if (intval($_POST['server_group_id']) > 0)
			{
				$background_task['server_group_id'] = intval($_POST['server_group_id']);
			}

			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=10, album_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]users_events set event_type_id=2, user_id=?, album_id=?, added_date=?", $user_id, $item_id, $_POST['post_date']);
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_album_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			$next_item_id = 0;
			if (isset($_POST['save_and_edit']) || isset($_POST['delete_and_edit']))
			{
				if ($where <> '')
				{
					$where2 = " and ($table_name.status_id=0 or $table_name.status_id=1)";
				} else
				{
					$where2 = " where ($table_name.status_id=0 or $table_name.status_id=1)";
				}
				$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by, $table_name.$table_key_name"));

				$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
				if ($next_item_id == 0)
				{
					$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by limit 1"));
				}
				if ($next_item_id == $item_id)
				{
					$next_item_id = 0;
				}

				if (isset($_POST['delete_and_edit']))
				{
					if (in_array('albums|delete', $_SESSION['permissions']))
					{
						sql("update $table_name set status_id=4 where $table_key_name=$item_id");
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $item_id, serialize(array()), date("Y-m-d H:i:s"));
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

			if ($_POST['dir'] == '' || $options['ALBUM_REGENERATE_DIRECTORIES'] == 1)
			{
				$_POST['dir'] = get_correct_dir_name($_POST['title']);
			}
			if ($_POST['dir'] <> '')
			{
				$temp_dir = $_POST['dir'];
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $table_name where dir=? and album_id<>?", $temp_dir, $item_id)) == 0)
					{
						$_POST['dir'] = $temp_dir;
						break;
					}
					$temp_dir = $_POST['dir'] . $i;
				}
			}

			if (intval($_POST['server_group_id']) > 0 && $old_album_data['server_group_id'] > 0 && intval($_POST['server_group_id']) != $old_album_data['server_group_id'])
			{
				$background_task = array();
				$background_task['server_group_id'] = intval($_POST['server_group_id']);
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=23, album_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
			}

			sql_pr("update $table_name set user_id=?, post_date=?, relative_post_date=?, content_source_id=?, admin_flag_id=?, is_private=?, access_level_id=?, tokens_required=?, status_id=?, title=?, dir=?, description=?, is_locked=?, custom1=?, custom2=?, custom3=? where $table_key_name=?",
				$user_id, $_POST['post_date'], intval($_POST['relative_post_date']), intval($_POST['content_source_id']), intval($_POST['admin_flag_id']), intval($_POST['is_private']), intval($_POST['access_level_id']), intval($_POST['tokens_required']), intval($_POST['status_id']), $_POST['title'], $_POST['dir'], $_POST['description'], intval($_POST['is_locked']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $item_id
			);

			$update_details = '';
			if ($_POST['title'] <> $old_album_data['title'])
			{
				$update_details .= "title, ";
			}
			if ($_POST['description'] <> $old_album_data['description'])
			{
				$update_details .= "description, ";
			}
			if ($_POST['dir'] <> $old_album_data['dir'])
			{
				$update_details .= "dir, ";
			}
			if (intval($_POST['content_source_id']) <> intval($old_album_data['content_source_id']))
			{
				$update_details .= "content_source_id, ";
			}
			if (intval($_POST['admin_flag_id']) <> intval($old_album_data['admin_flag_id']))
			{
				$update_details .= "admin_flag_id, ";
			}
			if (intval($_POST['is_private']) <> intval($old_album_data['is_private']))
			{
				$update_details .= "is_private, ";
			}
			if (intval($_POST['access_level_id']) <> intval($old_album_data['access_level_id']))
			{
				$update_details .= "access_level_id, ";
			}
			if (intval($_POST['tokens_required']) <> intval($old_album_data['tokens_required']))
			{
				$update_details .= "tokens_required, ";
			}
			if (intval($_POST['is_locked']) <> intval($old_album_data['is_locked']))
			{
				$update_details .= "is_locked, ";
			}
			if (intval($_POST['status_id']) <> intval($old_album_data['status_id']))
			{
				$update_details .= "status_id, ";
			}
			if (intval($user_id) <> intval($old_album_data['user_id']))
			{
				$update_details .= "user_id, ";
			}
			if ($_POST['post_date'] <> $old_album_data['post_date'])
			{
				$update_details .= "post_date, ";
			}
			if ($_POST['relative_post_date'] <> $old_album_data['relative_post_date'])
			{
				$update_details .= "relative_post_date, ";
			}
			if ($_POST['custom1'] <> $old_album_data['custom1'])
			{
				$update_details .= "custom1, ";
			}
			if ($_POST['custom2'] <> $old_album_data['custom2'])
			{
				$update_details .= "custom2, ";
			}
			if ($_POST['custom3'] <> $old_album_data['custom3'])
			{
				$update_details .= "custom3, ";
			}

			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, strlen($update_details) - 2);
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=2, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));

			foreach ($languages as $language)
			{
				if (in_array("localization|$language[code]", $_SESSION['permissions']))
				{
					if (isset($_POST["title_$language[code]"]) && $_POST["title_$language[code]"] <> $old_album_data["title_$language[code]"])
					{
						$language_update_array["title_$language[code]"] = $_POST["title_$language[code]"];
					}

					if ($language['is_directories_localize'] == 1)
					{
						if (isset($_POST["dir_$language[code]"]))
						{
							if ($_POST["dir_$language[code]"] == '')
							{
								$_POST["dir_$language[code]"] = get_correct_dir_name($_POST["title_$language[code]"], $language);
							}
							if ($_POST["dir_$language[code]"] != '')
							{
								$temp_dir = $_POST["dir_$language[code]"];
								for ($i = 2; $i < 999999; $i++)
								{
									if (mr2number(sql_pr("select count(*) from $table_name where dir_$language[code]=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
									{
										$_POST["dir_$language[code]"] = $temp_dir;
										break;
									}
									$temp_dir = $_POST["dir_$language[code]"] . $i;
								}
							}
							if ($_POST["dir_$language[code]"] <> $old_album_data["dir_$language[code]"])
							{
								$language_update_array["dir_$language[code]"] = $_POST["dir_$language[code]"];
							}
						}
					}

					if (isset($_POST["description_$language[code]"]) && $_POST["description_$language[code]"] <> $old_album_data["description_$language[code]"])
					{
						$language_update_array["description_$language[code]"] = $_POST["description_$language[code]"];
					}
				}
			}
			if (is_array($language_update_array))
			{
				sql_pr("update $table_name set ?% where $table_key_name=?", $language_update_array, $item_id);

				$update_details = '';
				foreach ($language_update_array as $k => $v)
				{
					$update_details .= "$k, ";
				}
				if (strlen($update_details) > 0)
				{
					$update_details = substr($update_details, 0, strlen($update_details) - 2);
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=200, object_id=?, object_type_id=2, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
				}
			}

			if (count($_POST['delete_flags']) > 0)
			{
				$delete_flags = implode(",", array_map("intval", $_REQUEST['delete_flags']));
				sql_pr("delete from $config[tables_prefix]flags_albums where album_id=? and flag_id in ($delete_flags)", $item_id);
			}

			if ($old_album_data['user_id'] <> $user_id)
			{
				sql_pr("update $config[tables_prefix]users_events set user_id=? where event_type_id in (2,8,9) and album_id=?", $user_id, $item_id);
			}
			if ($old_album_data['is_private'] <> intval($_POST['is_private']))
			{
				if ($_POST['relative_post_date'] == 0)
				{
					if ($old_album_data['is_private'] == 1 && intval($_POST['is_private']) == 0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=9, user_id=?, album_id=?, added_date=?", $user_id, $item_id, date("Y-m-d H:i:s"));
					} elseif ($old_album_data['is_private'] == 0 && intval($_POST['is_private']) == 1)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=8, user_id=?, album_id=?, added_date=?", $user_id, $item_id, date("Y-m-d H:i:s"));
					}
				}
			}
			if ($old_album_data['post_date'] <> $_POST['post_date'])
			{
				if ($_POST['relative_post_date'] == 0)
				{
					sql_pr("update $config[tables_prefix]comments set added_date=date_add(?, INTERVAL UNIX_TIMESTAMP(added_date) - UNIX_TIMESTAMP(?) SECOND) where object_id=? and object_type_id=2", $_POST['post_date'], $old_album_data['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]comments set added_date=greatest(?, ?) where object_id=? and object_type_id=2 and added_date>?", $_POST['post_date'], date("Y-m-d H:i:s"), $item_id, date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where album_id=? and event_type_id=5", $item_id);
				} else
				{
					sql_pr("update $config[tables_prefix]comments set added_date=? where object_id=? and object_type_id=2", $_POST['post_date'], $item_id);
					sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where album_id=? and event_type_id=5", $item_id);
				}
				sql("update $config[tables_prefix]users_events set added_date=(select post_date from $table_name where $table_name.album_id=$config[tables_prefix]users_events.album_id) where album_id=$item_id and event_type_id=2");
				sql("delete from $config[tables_prefix]users_events where event_type_id in (8,9) and album_id=$item_id");
			}

			if (intval($_POST['is_reviewed']) == 1)
			{
				sql_pr("update $table_name set is_review_needed=0, af_upload_zone=? where $table_key_name=?", intval($_POST['af_upload_zone']), $item_id);
				if (intval($_POST['is_reviewed_activate']) == 1 && $_POST['title'] != '')
				{
					sql_pr("update $table_name set status_id=1 where $table_key_name=? and status_id=0", $item_id);
					$_POST['status_id'] = 1;
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
					$ip = int2ip($old_album_data['ip']);
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
					$ip_mask = ip2mask(int2ip($old_album_data['ip']));
					if ($ip_mask != '0.0.0.*')
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=?", $ip_mask)) == 0)
						{
							$max_sort_id = mr2number(sql_pr("select max(sort_id) from $config[tables_prefix]users_blocked_ips")) + 1;
							sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $ip_mask, $max_sort_id);
						}
					}
				}
				if (in_array('albums|delete', $_SESSION['permissions']))
				{
					$delete_album_ids = array();
					if (intval($_POST['is_delete_all_albums_from_user']) == 1)
					{
						$delete_album_ids = mr2array_list(sql_pr("select $table_key_name from $table_name where is_review_needed=1 and $table_key_name<>? and user_id=(select user_id from $table_name where $table_key_name=?) and status_id<>4", $item_id, $item_id));
						$delete_album_ids_limit = intval($config['max_delete_on_review']);
						if ($delete_album_ids_limit == 0)
						{
							$delete_album_ids_limit = 30;
						}
						if (count($delete_album_ids) > $delete_album_ids_limit)
						{
							$delete_album_ids = array();
						}
					}
					$delete_album_ids[] = $item_id;

					foreach ($delete_album_ids as $delete_album_id)
					{
						sql("update $table_name set status_id=4 where $table_key_name=$delete_album_id");
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $delete_album_id, date("Y-m-d H:i:s"));
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $delete_album_id, serialize(array()), date("Y-m-d H:i:s"));
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

			if (intval($_POST['status_id']) == 1 && $old_album_data['status_id'] == 0)
			{
				process_activated_albums(array($item_id));
			}

			sql("update $config[tables_prefix]users set
					public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_albums_count=public_albums_count+private_albums_count+premium_albums_count
				where user_id in ($user_id,$old_album_data[user_id])"
			);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name=?", $item_id)));
		sql_pr("delete from $table_name_categories where $table_key_name=?", $item_id);
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
		update_categories_albums_totals($list_ids_categories);

		$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name=?", $item_id)));
		sql_pr("delete from $table_name_models where $table_key_name=?", $item_id);
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
		update_models_albums_totals($list_ids_models);

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
		update_tags_albums_totals($list_ids_tags);

		update_content_sources_albums_totals(array($_POST['content_source_id'], $old_album_data['content_source_id']));

		if ($_POST['action'] == 'change_complete')
		{
			if (in_array('albums|edit_all', $_SESSION['permissions']) && isset($_POST['post_process_plugins']) && is_array($_POST['post_process_plugins']))
			{
				foreach ($_POST['post_process_plugins'] as $plugin)
				{
					if (!is_file("$config[project_path]/admin/plugins/$plugin/$plugin.php"))
					{
						continue;
					}
					log_album("", $item_id);
					log_album("INFO  Executing $plugin plugin", $item_id);
					unset($res);
					exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/$plugin/$plugin.php exec album $item_id 2>&1", $res);
					if ($res[0] <> '')
					{
						log_album("...." . implode("\n....", $res), $item_id, 1);
					} else
					{
						log_album("....no response", $item_id, 1);
					}
				}
			}
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
// images replace
// =====================================================================================================================

if ($_POST['action'] == 'upload_images' && intval($_POST['item_id']) > 0)
{
	$item_id = intval($_POST['item_id']);
	$dir_path = get_dir_by_id($item_id);
	$album_info = mr2array_single(sql("select * from $table_name where $table_key_name=$item_id"));
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($album_info['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($album_info['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($album_info['admin_flag_id'] == 0 || !in_array($album_info['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	$min_image_size = array(0 => 0, 1 => 0);
	if ($options['ALBUM_VALIDATE_IMAGE_SIZES'] == 1)
	{
		$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_albums where status_id in (0,1) and aspect_ratio_id not in (3,4,5)"));
		foreach ($sizes as $size)
		{
			$temp_size = explode("x", $size);
			if (intval($temp_size[0]) > $min_image_size[0])
			{
				$min_image_size[0] = intval($temp_size[0]);
			}
			if (intval($temp_size[1]) > $min_image_size[1])
			{
				$min_image_size[1] = intval($temp_size[1]);
			}
		}
	}

	if (validate_field('empty', $_POST['images'], $lang['albums']['images_upload_field_image_first']))
	{
		$ext = strtolower(end(explode(".", $_POST['images'])));
		$rnd = mt_rand(10000000, 99999999);
		if ($ext == 'zip')
		{
			validate_field('archive_or_images', 'images', $lang['albums']['images_upload_field_image_first'], array('image_types' => $config['image_allowed_ext'], 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			$source_file = 'source_1.zip';
		} else
		{
			validate_field('file', 'images', $lang['albums']['images_upload_field_image_first'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			$source_file = 'source_1.jpg';
		}
		for ($i = 2; $i <= 100; $i++)
		{
			if (isset($_POST["image_{$i}_hash"]))
			{
				validate_field('file', "image_{$i}", str_replace("%1%", $i, $lang['albums']['images_upload_field_image_next']), array('is_required' => 0, 'allowed_ext' => $config['image_allowed_ext'], 'is_image' => '1', 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			}
		}
	}
	if (!is_array($errors))
	{
		log_album("", $item_id);
		log_album("INFO  Replacing images in admin panel", $item_id);

		if (!is_dir("$config[content_path_albums_sources]/$dir_path"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path", 0777);
			chmod("$config[content_path_albums_sources]/$dir_path", 0777);
		}
		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
		}

		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
		{
			log_album("ERROR  Failed to create directory: $config[content_path_albums_sources]/$dir_path/$item_id", $item_id);
		}

		$source_files = array();

		if (!transfer_uploaded_file('images', "$config[content_path_albums_sources]/$dir_path/$item_id/$source_file"))
		{
			log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/$source_file", $item_id);
		}
		$source_files[] = $source_file;

		for ($i = 2; $i <= 100; $i++)
		{
			if (isset($_POST["image_{$i}_hash"]) && $_POST["image_{$i}_hash"] <> '')
			{
				if (!transfer_uploaded_file("image_{$i}", "$config[content_path_albums_sources]/$dir_path/$item_id/source_$i.jpg"))
				{
					log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/source_$i.jpg", $item_id);
				}
				$source_files[] = "source_$i.jpg";
			}
		}

		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=152, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

		$background_task = array();
		$background_task['source_files'] = $source_files;
		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=14, album_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));

		log_album("INFO  Done images changes", $item_id);

		$_SESSION['messages'][] = $lang['albums']['images_upload_success_message'];
		return_ajax_success($page_name . "?action=manage_images&amp;item_id=$item_id", 1);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// images edit
// =====================================================================================================================

if ($_POST['action'] == 'process_images' && intval($_POST['item_id']) > 0)
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id = intval($_POST['item_id']);
	$dir_path = get_dir_by_id($item_id);
	$album_info = mr2array_single(sql("select * from $table_name where $table_key_name=$item_id"));
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($album_info['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($album_info['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($album_info['admin_flag_id'] == 0 || !in_array($album_info['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	$min_image_size = array(0 => 0, 1 => 0);
	if ($options['ALBUM_VALIDATE_IMAGE_SIZES'] == 1)
	{
		$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_albums where status_id in (0,1) and aspect_ratio_id not in (3,4,5)"));
		foreach ($sizes as $size)
		{
			$temp_size = explode("x", $size);
			if (intval($temp_size[0]) > $min_image_size[0])
			{
				$min_image_size[0] = intval($temp_size[0]);
			}
			if (intval($temp_size[1]) > $min_image_size[1])
			{
				$min_image_size[1] = intval($temp_size[1]);
			}
		}
	}

	$image_ids = mr2array_list(sql("select image_id from $config[tables_prefix]albums_images where album_id=$item_id order by image_id"));
	if (is_array($_POST['delete']))
	{
		$delete_ids = array_map("intval", $_POST['delete']);
	} else
	{
		$delete_ids = array();
	}

	if (count($delete_ids) >= count($image_ids))
	{
		$errors[] = get_aa_error('album_image_delete_all_forbidded');
	}

	if ($_POST["preview_hash"] != '')
	{
		validate_field('file', "preview", $lang['albums']['images_mgmt_field_image_preview'], array('is_required' => 0, 'allowed_ext' => $config['image_allowed_ext'], 'is_image' => '1'));
	}

	foreach ($image_ids as $image_id)
	{
		if (!in_array($image_id, $delete_ids) && $_POST["file_$image_id"] <> '')
		{
			validate_field('file', "file_$image_id", str_replace("%1%", $image_id, $lang['albums']['images_mgmt_file_title_image']), array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
		}
	}

	if (in_array('albums|edit_all', $_SESSION['permissions']))
	{
		if (intval($_POST['status_id']) == 1)
		{
			if ($album_info['title'] == '')
			{
				$errors[] = get_aa_error('album_image_activate');
			}
		}
	}

	if (!is_array($errors))
	{
		$next_item_id = 0;
		if (isset($_POST['save_and_edit']) || isset($_POST['delete_and_edit']))
		{
			if ($where <> '')
			{
				$where2 = " and ($table_name.status_id=0 or $table_name.status_id=1)";
			} else
			{
				$where2 = " where ($table_name.status_id=0 or $table_name.status_id=1)";
			}
			$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by, $table_name.$table_key_name"));

			$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
			if ($next_item_id == 0)
			{
				$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by limit 1"));
			}
			if ($next_item_id == $item_id)
			{
				$next_item_id = 0;
			}

			if (isset($_POST['delete_and_edit']))
			{
				if (in_array('albums|delete', $_SESSION['permissions']))
				{
					sql("update $table_name set status_id=4 where $table_key_name=$item_id");
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $item_id, serialize(array()), date("Y-m-d H:i:s"));
					$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

					if ($next_item_id == 0)
					{
						return_ajax_success($page_name, 1);
					} else
					{
						return_ajax_success($page_name . "?action=manage_images&amp;item_id=$next_item_id", 1);
					}
				} else
				{
					header("Location: error.php?error=permission_denied");
					die;
				}
			}
		}

		log_album("", $item_id);
		log_album("INFO  Saving images in admin panel", $item_id);

		if (!is_dir("$config[content_path_albums_sources]/$dir_path"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path", 0777);
			chmod("$config[content_path_albums_sources]/$dir_path", 0777);
		}
		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$item_id", 0777);
		}

		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
		{
			log_album("ERROR  Failed to create directory: $config[content_path_albums_sources]/$dir_path/$item_id", $item_id);
		}

		$main = intval($_POST['main']);
		$first_id_no_delete = 0;
		$changed_ids = array();

		foreach ($image_ids as $image_id)
		{
			if (!in_array($image_id, $delete_ids) && $_POST["file_$image_id"] <> '')
			{
				log_album("INFO  Replacing image #$image_id by manual upload", $item_id);
				if (!transfer_uploaded_file("file_$image_id", "$config[content_path_albums_sources]/$dir_path/$item_id/$image_id.jpg"))
				{
					log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/$image_id.jpg", $item_id);
				}
				$changed_ids[] = $image_id;
			}
			if (!in_array($image_id, $delete_ids))
			{
				if ($first_id_no_delete == 0)
				{
					$first_id_no_delete = $image_id;
				}
				if (isset($_POST["title_$image_id"]))
				{
					sql_pr("update $config[tables_prefix]albums_images set title=? where image_id=?", $_POST["title_$image_id"], $image_id);
				}
			}
		}
		if (in_array($main, $delete_ids))
		{
			$main = $first_id_no_delete;
		}
		if ($main <> $album_info['main_photo_id'])
		{
			log_album("INFO  Changing preview image from #$album_info[main_photo_id] to #$main by manual operation", $item_id);
			sql_pr("update $table_name set main_photo_id=$main where $table_key_name=$item_id");
		}
		if (count($delete_ids) > 0)
		{
			$delete_ids_str = implode(",", $delete_ids);
			sql("delete from $config[tables_prefix]albums_images where album_id=$item_id and image_id in ($delete_ids_str)");
			sql("delete from $config[tables_prefix]rating_history where album_id=$item_id and image_id in ($delete_ids_str)");
			sql("update $config[tables_prefix]comments set object_sub_id=0 where object_sub_id in ($delete_ids_str) and object_type_id=2");

			$delete_cnt = count($delete_ids);
			sql("update $table_name set photos_amount=photos_amount-$delete_cnt where $table_key_name=$item_id");
			foreach ($delete_ids as $delete_id)
			{
				log_album("INFO  Removing image #$delete_id by manual delete", $item_id);
			}
		}

		$preview_image_changed = false;
		if (isset($_POST["preview_hash"]) && $_POST["preview_hash"] <> '')
		{
			log_album("INFO  Uploading new preview image", $item_id);
			if (!transfer_uploaded_file("preview", "$config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg"))
			{
				log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg", $item_id);
			}
			sql("update $table_name set has_preview=1 where $table_key_name=$item_id");
			$preview_image_changed = true;
		} elseif ($_POST["preview"] == '' && $album_info['has_preview'] == 1)
		{
			log_album("INFO  Deleting preview image", $item_id);
			sql("update $table_name set has_preview=0 where $table_key_name=$item_id");
			$preview_image_changed = true;
		}

		if (count($changed_ids) > 0 || $main <> $album_info['main_photo_id'] || count($delete_ids) > 0 || $preview_image_changed)
		{
			$background_task = array();
			$background_task['changed_image_ids'] = $changed_ids;
			$background_task['deleted_image_ids'] = $delete_ids;
			if ($main <> $album_info['main_photo_id'] || $_POST["file_$main"] <> '' || $preview_image_changed)
			{
				$background_task['main_image_changed'] = 1;
			}
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=22, album_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
		}
		log_album("INFO  Done images changes", $item_id);

		if (in_array('albums|edit_all', $_SESSION['permissions']))
		{
			sql_pr("update $table_name set status_id=?, admin_flag_id=? where $table_key_name=$item_id", intval($_POST['status_id']), intval($_POST['admin_flag_id']));
		}

		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=152, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));

		$_SESSION['messages'][] = $lang['albums']['images_mgmt_success_message'];

		if (isset($_POST['save_and_edit']))
		{
			if ($next_item_id == 0)
			{
				$_POST['save_and_close'] = $_POST['save_and_edit'];
				return_ajax_success($page_name, 1);
			} else
			{
				return_ajax_success($page_name . "?action=manage_images&amp;item_id=$next_item_id", 1);
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

if ($_REQUEST['action'] == 'album_log' && intval($_REQUEST['item_id']) > 0)
{
	header("Content-Type: text/plain; charset=utf8");

	$item_id = intval($_REQUEST['item_id']);
	$dir_path = get_dir_by_id($item_id);
	if (is_file("$config[project_path]/admin/logs/albums/$dir_path.tar.gz"))
	{
		unset($list);
		exec("tar --list --file=$config[project_path]/admin/logs/albums/$dir_path.tar.gz", $list);
		$list = array_flip($list);
		if (isset($list["$item_id.txt"]))
		{
			unset($temp);
			exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/albums/$dir_path.tar.gz $item_id.txt", $temp);
			echo "-------------------------------------- {$item_id}.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";

			for ($k = 1; $k < 10000; $k++)
			{
				if (isset($list["{$item_id}_$k.txt"]))
				{
					unset($temp);
					exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/albums/$dir_path.tar.gz {$item_id}_$k.txt", $temp);
					echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";
				} else
				{
					break;
				}
			}
		}
	}

	if (is_file("$config[project_path]/admin/logs/albums/$item_id.txt"))
	{
		echo "-------------------------------------- {$item_id}.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/albums/$item_id.txt")) . "\n\n";
	}
	die;
} elseif ($_REQUEST['action'] == 'album_validate' && intval($_REQUEST['item_id']) > 0)
{
	$item_id = intval($_REQUEST['item_id']);
	$data = mr2array_single(sql_pr("select * from $table_name where status_id in (0,1) and $table_key_name=$item_id"));
	header("Content-Type: text/plain; charset=utf8");
	if ($data['album_id'] > 0)
	{
		echo validate_album($data);
	}
	die;
}

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && (count($_REQUEST['row_select']) > 0 || $_REQUEST['batch_action'] == 'mass_edit_all' || $_REQUEST['batch_action'] == 'mass_edit_filtered' || $_REQUEST['batch_action'] == 'restart'))
{
	if ($_REQUEST['batch_action'] == 'mass_edit' || $_REQUEST['batch_action'] == 'mass_edit_all' || $_REQUEST['batch_action'] == 'mass_edit_filtered')
	{
		$mass_edit_data = array();
		if ($_REQUEST['batch_action'] == 'mass_edit_all')
		{
			$mass_edit_data['all'] = 1;
		} elseif ($_REQUEST['batch_action'] == 'mass_edit_filtered')
		{
			$mass_edit_data['all'] = 0;
			$mass_edit_data['ids'] = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where"));
		} else
		{
			$mass_edit_data['all'] = 0;
			$mass_edit_data['ids'] = array_map("intval", $_REQUEST['row_select']);
		}

		$rnd = mt_rand(10000000, 99999999);
		file_put_contents("$config[temporary_path]/mass-edit-$rnd.dat", serialize($mass_edit_data));

		return_ajax_success("albums_mass_edit.php?edit_id=$rnd");
	} elseif ($_REQUEST['batch_action'] == 'soft_delete')
	{
		$delete_data = array();
		$delete_data['ids'] = array_map("intval", $_REQUEST['row_select']);

		$rnd = mt_rand(10000000, 99999999);
		file_put_contents("$config[temporary_path]/delete-albums-$rnd.dat", serialize($delete_data));

		return_ajax_success("$page_name?action=mark_deleted&amp;delete_id=$rnd");
	}

	if ($_REQUEST['batch_action'] == 'restart')
	{
		$where_batch = '';
		if (count($_REQUEST['row_select']) > 0)
		{
			$row_select_str = implode(",", array_map("intval", $_REQUEST['row_select']));
			$where_batch = " and $table_key_name in ($row_select_str)";
		}
		$album_ids = mr2array_list(sql("select $table_key_name from $table_name where status_id=2 $where_batch"));
		foreach ($album_ids as $album_id)
		{
			$background_task_id = mr2number(sql("select task_id from $config[tables_prefix]background_tasks where status_id=2 and type_id=10 and album_id=$album_id"));
			if ($background_task_id > 0)
			{
				sql("delete from $config[tables_prefix]albums_images where album_id in (select album_id from $config[tables_prefix]albums where status_id=2 and album_id=$album_id)");
				sql("update $config[tables_prefix]albums set status_id=3 where status_id=2 and album_id=$album_id");
				sql("update $config[tables_prefix]background_tasks set status_id=0, server_id=0, message='' where status_id=2 and task_id=$background_task_id");

				file_put_contents("$config[project_path]/admin/logs/albums/$album_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
				file_put_contents("$config[project_path]/admin/logs/tasks/$background_task_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n\n", FILE_APPEND | LOCK_EX);
			} else
			{
				$background_task_id = mr2number(sql("select task_id from $config[tables_prefix]background_tasks_history where type_id=10 and album_id=$album_id"));
				if ($background_task_id > 0)
				{
					sql("delete from $config[tables_prefix]albums_images where album_id in (select album_id from $config[tables_prefix]albums where status_id=2 and album_id=$album_id)");
					sql("update $config[tables_prefix]albums set status_id=3 where status_id=2 and album_id=$album_id");
					sql_pr("insert into $config[tables_prefix]background_tasks (status_id, type_id, album_id, data, added_date) select 0, 10, ?, data, ? from $config[tables_prefix]background_tasks_history where task_id=?", $album_id, date("Y-m-d H:i:s"), $background_task_id);

					file_put_contents("$config[project_path]/admin/logs/albums/$album_id.txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
				}
			}
		}
		$_SESSION['messages'][] = $lang['albums']['success_message_conversion_restarted'];
		return_ajax_success($page_name);
	}

	$where_batch = '';
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		$admin_id = intval($_SESSION['userdata']['user_id']);
		$where_batch .= " and admin_user_id=$admin_id ";
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		$where_batch .= " and status_id=0 ";
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
		$where_batch .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
	}

	$row_select_str = implode(",", array_map("intval", $_REQUEST['row_select']));
	$row_select = mr2array_list(sql("select album_id from $table_name where $table_key_name in ($row_select_str) $where_batch"));
	$row_select_str = implode(",", array_map("intval", $row_select));
	if (count($row_select) == 0)
	{
		return_ajax_success($page_name);
	}

	$list_ids = mr2array_list(sql("select user_id from $table_name where $table_key_name in ($row_select_str)"));
	$list_ids_str = implode(",", array_map("intval", $list_ids));

	$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
	$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name in ($row_select_str)")));
	$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));
	$list_ids_content_sources = array_map("intval", mr2array_list(sql_pr("select distinct content_source_id from $table_name where $table_key_name in ($row_select_str)")));

	if ($_REQUEST['batch_action'] == 'delete' || $_REQUEST['batch_action'] == 'delete_and_activate')
	{
		if ($_REQUEST['batch_action'] == 'delete_and_activate')
		{
			$ids_to_activate = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
			if (count($ids_to_activate) > 0)
			{
				$ids_to_activate_str = implode(",", array_map("intval", $ids_to_activate));
				$temp_amount = mr2number(sql("select count(*) from $table_name where (title='' or dir='' or (select count(*) from $config[tables_prefix]albums_images where album_id=$table_name.album_id)=0) and $table_key_name in ($ids_to_activate_str)"));
				if ($temp_amount > 0)
				{
					$errors[] = get_aa_error('album_cannot_be_activated', $temp_amount);
					return_ajax_errors($errors);
				}
			}

			sql("update $table_name set status_id=1, is_review_needed=0 where status_id=0 and $table_key_name in ($ids_to_activate_str)");

			$list_ids = mr2array_list(sql("select user_id from $table_name where $table_key_name in ($ids_to_activate_str)"));
			$list_ids_str = implode(",", array_map("intval", $list_ids));
			sql("update $config[tables_prefix]users set
					public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_albums_count=public_albums_count+private_albums_count+premium_albums_count
				where user_id in ($list_ids_str)"
			);

			$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $table_name_categories where $table_key_name in ($ids_to_activate_str)")));
			$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $table_name_models where $table_key_name in ($ids_to_activate_str)")));
			$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $table_name_tags where $table_key_name in ($ids_to_activate_str)")));
			$list_ids_content_sources = array_map("intval", mr2array_list(sql_pr("select distinct content_source_id from $table_name where $table_key_name in ($ids_to_activate_str)")));

			process_activated_albums($ids_to_activate);
			update_categories_albums_totals($list_ids_categories);
			update_models_albums_totals($list_ids_models);
			update_tags_albums_totals($list_ids_tags);
			update_content_sources_albums_totals($list_ids_content_sources);
		}

		sql("update $table_name set status_id=4 where $table_key_name in ($row_select_str)");

		foreach ($row_select as $album_id)
		{
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $album_id, date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $album_id, serialize(array()), date("Y-m-d H:i:s"));
		}
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'activate' || $_REQUEST['batch_action'] == 'activate_and_delete')
	{
		$temp_amount = mr2number(sql("select count(*) from $table_name where (title='' or dir='' or (select count(*) from $config[tables_prefix]albums_images where album_id=$table_name.album_id)=0) and $table_key_name in ($row_select_str)"));
		if ($temp_amount > 0)
		{
			$errors[] = get_aa_error('album_cannot_be_activated', $temp_amount);
			return_ajax_errors($errors);
		} else
		{
			sql("update $table_name set status_id=1, is_review_needed=0 where status_id=0 and $table_key_name in ($row_select_str)");

			sql("update $config[tables_prefix]users set
					public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_albums_count=public_albums_count+private_albums_count+premium_albums_count
				where user_id in ($list_ids_str)"
			);

			process_activated_albums($row_select);
			update_categories_albums_totals($list_ids_categories);
			update_models_albums_totals($list_ids_models);
			update_tags_albums_totals($list_ids_tags);
			update_content_sources_albums_totals($list_ids_content_sources);

			if ($_REQUEST['batch_action'] == 'activate_and_delete')
			{
				$ids_to_delete = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
				if (count($ids_to_delete) > 0)
				{
					$ids_to_delete_str = implode(",", array_map("intval", $ids_to_delete));
					$ids_to_delete = mr2array_list(sql_pr("select $table_key_name from $table_name where status_id=0 and $table_key_name in ($ids_to_delete_str)"));
					if (count($ids_to_delete) > 0)
					{
						$ids_to_delete_str = implode(",", array_map("intval", $ids_to_delete));
						sql("update $table_name set status_id=4 where $table_key_name in ($ids_to_delete_str)");

						foreach ($ids_to_delete as $album_id)
						{
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $album_id, date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $album_id, serialize(array()), date("Y-m-d H:i:s"));
						}
					}
				}
			}

			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
			return_ajax_success($page_name);
		}
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where status_id=1 and $table_key_name in ($row_select_str)");

		sql("update $config[tables_prefix]users set
				public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
				private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
				premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
				total_albums_count=public_albums_count+private_albums_count+premium_albums_count
			where user_id in ($list_ids_str)"
		);

		update_categories_albums_totals($list_ids_categories);
		update_models_albums_totals($list_ids_models);
		update_tags_albums_totals($list_ids_tags);
		update_content_sources_albums_totals($list_ids_content_sources);

		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'mark_reviewed')
	{
		sql("update $table_name set is_review_needed=0 where $table_key_name in ($row_select_str)");
		$_SESSION['messages'][] = $lang['common']['success_message_marked_reviewed'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'inc_priority')
	{
		sql("update $config[tables_prefix]background_tasks set priority=priority+10 where album_id in ($row_select_str) and status_id=0");
		$_SESSION['messages'][] = $lang['common']['success_message_completed'];
		return_ajax_success($page_name);
	}
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'mark_deleted' && intval($_GET['delete_id']) > 0)
{
	$delete_id = intval($_REQUEST['delete_id']);
	if ($delete_id < 1 || !is_file("$config[temporary_path]/delete-albums-$delete_id.dat"))
	{
		header("Location: $page_name");
		die;
	}
	$delete_data = @unserialize(file_get_contents("$config[temporary_path]/delete-albums-$delete_id.dat"));
	if (!is_array($delete_data))
	{
		header("Location: $page_name");
		die;
	}

	$delete_ids_str = implode(",", $delete_data['ids']);

	$where_deleting = '';
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		$admin_id = intval($_SESSION['userdata']['user_id']);
		$where_deleting .= " and admin_user_id=$admin_id ";
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		$where_deleting .= " and status_id=0 ";
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
		$where_deleting .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
	}

	$_POST['delete_albums'] = mr2array(sql("select album_id, title from $table_name where status_id in (0,1,2) and (album_id in ($delete_ids_str)) $where_deleting"));
	$_POST['top_delete_reasons'] = mr2array(sql("select delete_reason, count($table_key_name) as total_albums from $table_name where status_id=5 group by delete_reason order by count($table_key_name) desc limit 10"));
}

if ($_GET['action'] == 'change_deleted' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where status_id in (5) and $table_key_name=$item_id"));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		header("Location: error.php?error=permission_denied");
		die;
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($_POST['admin_flag_id'] == 0 || !in_array($_POST['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	if ($_POST['dir'] <> '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['album_id'], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
	}
	$_POST['top_delete_reasons'] = mr2array(sql("select delete_reason, count($table_key_name) as total_albums from $table_name where status_id=5 group by delete_reason order by count($table_key_name) desc limit 10"));
}

if ($_GET['action'] == 'add_new')
{
	$_POST['user'] = $options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM'];
	$_POST['status_id'] = $options['DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM'];
	if ($options['USE_POST_DATE_RANDOMIZATION_ALBUM'] == '0')
	{
		$_POST['post_date'] = date("Y-m-d");
	} elseif ($options['USE_POST_DATE_RANDOMIZATION_ALBUM'] == '1')
	{
		$_POST['post_date'] = date("Y-m-d H:i", strtotime(date("Y-m-d")) + mt_rand(0, 86399));
	} elseif ($options['USE_POST_DATE_RANDOMIZATION_ALBUM'] == '2')
	{
		$_POST['post_date'] = date("Y-m-d H:i");
	}
	if (intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM']) > 0)
	{
		$_POST['server_group_id'] = intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM']);
	}
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where status_id in (0,1) and $table_key_name=$item_id"));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($_POST['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($_POST['admin_flag_id'] == 0 || !in_array($_POST['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	if ($_POST['dir'] <> '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['album_id'], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
	}

	$_POST['post_date_option'] = 0;
	if ($config['relative_post_dates'] == 'true')
	{
		if ($_POST['relative_post_date'] <> 0)
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
	if ($_POST['server_group_id'] > 0)
	{
		$_POST['server_group'] = mr2array_single(sql_pr("select *, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space from $config[tables_prefix]admin_servers_groups where group_id=?", $_POST['server_group_id']));
		$_POST['server_group']['free_space'] = sizeToHumanString($_POST['server_group']['free_space'], 2);
		$_POST['server_group']['total_space'] = sizeToHumanString($_POST['server_group']['total_space'], 2);
	}

	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=$item_id order by id asc"));
	$_POST['models'] = mr2array(sql_pr("select model_id, (select title from $config[tables_prefix]models where model_id=$table_name_models.model_id) as title from $table_name_models where $table_key_name=$item_id order by id asc"));
	$_POST['tags'] = implode(", ", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as tag from $table_name_tags where $table_name_tags.$table_key_name=$item_id order by id asc")));
	$_POST['flags'] = mr2array(sql_pr("select flag_id, title, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2 having votes>0 order by title asc", $item_id));
	$_POST['ip'] = int2ip($_POST['ip']);

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

	$dir_path = get_dir_by_id($item_id);
	if (preg_match("|^[1-9]+\d*x[1-9]+\d*$|is", $_SESSION['save']['options']['images_on_album_edit']))
	{
		$size = $_SESSION['save']['options']['images_on_album_edit'];
		$temp_images = mr2array(sql("select * from $config[tables_prefix]albums_images where album_id=$item_id order by image_id asc"));
		$required_count = intval($_SESSION['save']['options']['images_on_album_edit_count']);

		$_POST['list_images'][] = $temp_images[0];
		if ($required_count == 2)
		{
			if (count($temp_images) > 1)
			{
				$_POST['list_images'][] = $temp_images[count($temp_images) - 1];
			}
		} elseif ($required_count > 2)
		{
			if (count($temp_images) > $required_count)
			{
				$step = floatval(count($temp_images)) / floatval($required_count);
				$it = $step + 1;
				while ($it < count($temp_images) - 1)
				{
					$_POST['list_images'][] = $temp_images[intval(round($it))];
					if (count($_POST['list_images']) == $required_count - 1)
					{
						break;
					}
					$it += $step;
				}
				$_POST['list_images'][] = $temp_images[count($temp_images) - 1];
			} else
			{
				$_POST['list_images'] = $temp_images;
			}
		}
		foreach ($_POST['list_images'] as $k => $v)
		{
			$file_path = "main/$size/$dir_path/$item_id/$v[image_id].jpg";
			$hash = md5($config['cv'] . $file_path);
			$file_path = "$hash/$file_path";
			$_POST['list_images'][$k]['file_path'] = $file_path;

			$source_path = "sources/$dir_path/$item_id/$v[image_id].jpg";
			$hash = md5($config['cv'] . $source_path);
			$source_path = "$hash/$source_path";
			$_POST['list_images'][$k]['source_path'] = $source_path;
		}
	}

	$_POST['server_group_migration_not_finished'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id=23 and album_id=?", $item_id));

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
		$_POST['other_albums_need_review'] = mr2number(sql_pr("select count(*) from $table_name where user_id=? and $table_key_name<>? and is_review_needed=1 and status_id<>4", $_POST['user_id'], $_POST[$table_key_name]));
	}
}

if ($_REQUEST['action'] == 'manage_images' && intval($_REQUEST['item_id']) > 0)
{
	$item_id = intval($_REQUEST['item_id']);
	$dir_path = get_dir_by_id($item_id);

	$format_id = trim($_REQUEST['format_id']);
	if ($format_id == '')
	{
		$format_id = $_SESSION['save'][$page_name]['format_id'];
	}

	$list_formats_main = mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=1 order by title asc"));
	foreach ($list_formats_main as $format)
	{
		if ($format['format_album_id'] == $format_id)
		{
			$_POST['format_info'] = $format;
			break;
		}
	}
	if (!isset($_POST['format_info']))
	{
		$format_id = 'sources';
	}

	$_POST['album_info'] = mr2array_single(sql("select * from $table_name where status_id in (0,1) and album_id=$item_id"));
	if (count($_POST['album_info']) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($_POST['album_info']['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($_POST['album_info']['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($_POST['album_info']['admin_flag_id'] == 0 || !in_array($_POST['album_info']['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where album_id=? and type_id in (10,14)", $item_id)) > 0)
	{
		$_POST['has_background_task'] = 1;
	}

	$_POST['zip_files'] = get_album_zip_files($_POST['album_info']['album_id'], $_POST['album_info']['zip_files'], $_POST['album_info']['server_group_id']);
	if ($_POST['album_info']['has_preview'] == 1)
	{
		$file_path = "sources/$dir_path/$item_id/preview.jpg";
		$hash = md5($config['cv'] . $file_path);
		$file_path = "$hash/$file_path";
		$_POST['preview_image'] = "$config[project_url]/get_image/" . $_POST['album_info']['server_group_id'] . "/$file_path/";
	}

	$_POST['list_formats_main'] = $list_formats_main;
	$_POST['format_id'] = $format_id;
	$_POST['list_images'] = mr2array(sql("select * from $config[tables_prefix]albums_images where album_id=$item_id order by image_id asc"));

	foreach ($_POST['list_images'] as $k => $v)
	{
		if (isset($_POST['format_info']))
		{
			$file_path = "main/" . $_POST['format_info']['size'] . "/$dir_path/$item_id/$v[image_id].jpg";
		} else
		{
			$file_path = "sources/$dir_path/$item_id/$v[image_id].jpg";
		}
		$hash = md5($config['cv'] . $file_path);
		$file_path = "$hash/$file_path";
		$_POST['list_images'][$k]['file_url'] = "$config[project_url]/get_image/" . $_POST['album_info']['server_group_id'] . "/$file_path/";

		$source_path = "sources/$dir_path/$item_id/$v[image_id].jpg";
		$hash = md5($config['cv'] . $source_path);
		$source_path = "$hash/$source_path";
		$_POST['list_images'][$k]['source_url'] = "$config[project_url]/get_image/" . $_POST['album_info']['server_group_id'] . "/$source_path/";
	}

	$_SESSION['save'][$page_name]['format_id'] = $format_id;
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

	$storage_servers = array();
	$temp = mr2array(sql("select * from $config[tables_prefix]admin_servers order by rand()"));
	foreach ($temp as $v)
	{
		if (!isset($storage_servers[$v['group_id']]))
		{
			$storage_servers[$v['group_id']] = $v;
		}
	}

	$min_format = '';
	$min_format_width = 0;
	$min_format_size = '';
	$list_formats_preview = mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=2 order by title asc"));
	foreach ($list_formats_preview as $format)
	{
		$format_size = explode('x', $format['size']);
		if (!$min_format || $format_size[0] < $min_format_width)
		{
			$min_format = $format['size'];
			$min_format_width = $format_size[0];
			if (in_array($format['aspect_ratio_id'], array(1, 2)))
			{
				$min_format_size = $format['size'];
			} else
			{
				$min_format_size = '';
			}
		}
	}
	if ($min_format_size)
	{
		$min_format_size = explode('x', $min_format_size);
		$max_display_size = explode('x', $_SESSION['save']['options']['maximum_thumb_size'] ?: '150x150');
		if ($min_format_size[0] > $max_display_size[0])
		{
			$ratio = $min_format_size[0] / $max_display_size[0];
			$min_format_size[0] = intval($min_format_size[0] / $ratio);
			$min_format_size[1] = intval($min_format_size[1] / $ratio);
		}
		if ($min_format_size[1] > $max_display_size[1])
		{
			$ratio = $min_format_size[1] / $max_display_size[1];
			$min_format_size[0] = intval($min_format_size[0] / $ratio);
			$min_format_size[1] = intval($min_format_size[1] / $ratio);
		}

		$min_format_size = implode('x', $min_format_size);
	}

	foreach ($data as $k => $v)
	{
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

		if ($_SESSION['save'][$page_name]['grid_columns']['thumb'] == 1)
		{
			if ($min_format && ($data[$k]['status_id'] == 0 || $data[$k]['status_id'] == 1) && isset($storage_servers[$data[$k]['server_group_id']]))
			{
				$album_id = $data[$k]['album_id'];
				$dir_path = get_dir_by_id($album_id);
				$data[$k]['thumb'] = "{$storage_servers[$data[$k]['server_group_id']]['urls']}/preview/$min_format/$dir_path/$album_id/preview.jpg";
				$data[$k]['thumb_size'] = $min_format_size;
			}
		}

		if ($data[$k]['dir'] <> '')
		{
			$allowed_statuses = array(0, 1, 5);
			if (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY']) == 2)
			{
				$allowed_statuses = array(0, 1, 2, 3, 5);
			}
			if (in_array($data[$k]['status_id'], $allowed_statuses))
			{
				$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			}
		}

		if (in_array($data[$k]['status_id'], array(2, 3, 4, 5)))
		{
			$data[$k]['is_editing_forbidden'] = 1;
		}
		if ($data[$k]['status_id'] == 2)
		{
			$data[$k]['is_error'] = 1;
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$list_content_sources = array();
$temp = mr2array(sql("select content_source_id, $config[tables_prefix]content_sources_groups.content_source_group_id, $config[tables_prefix]content_sources.title, $config[tables_prefix]content_sources_groups.title as content_source_group_title from $config[tables_prefix]content_sources left join $config[tables_prefix]content_sources_groups on $config[tables_prefix]content_sources_groups.content_source_group_id=$config[tables_prefix]content_sources.content_source_group_id order by $config[tables_prefix]content_sources_groups.title asc,$config[tables_prefix]content_sources.title asc"));
foreach ($temp as $res)
{
	$list_content_sources[$res['content_source_group_id']][] = $res;
}

$list_server_groups = mr2array(sql("select * from (select group_id, title, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=2) x where free_space>0 order by title asc"));
foreach ($list_server_groups as $k => $v)
{
	$list_server_groups[$k]['free_space'] = sizeToHumanString($v['free_space'], 2);
	$list_server_groups[$k]['total_space'] = sizeToHumanString($v['total_space'], 2);
}

$smarty = new mysmarty();
$smarty->assign('list_content_sources', $list_content_sources);
$smarty->assign('list_server_groups', $list_server_groups);

if (in_array($_REQUEST['action'], array('change', 'change_deleted', 'manage_images')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('options', $options);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_flags_albums', mr2array(sql("select * from $config[tables_prefix]flags where group_id=2 order by title asc")));
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where group_id=2 and is_admin_flag=1 order by title asc")));
$smarty->assign('list_languages', $languages);
if (in_array('system|administration', $_SESSION['permissions']))
{
	$smarty->assign('list_admin_users', mr2array(sql("select user_id, login from $config[tables_prefix]admin_users order by login asc")));
} else
{
	$smarty->assign('list_admin_users', mr2array(sql_pr("select user_id, login from $config[tables_prefix]admin_users where login=?", $_SESSION['userdata']['login'])));
}
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));
$smarty->assign('memberzone_data', unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat")));

$where = '';
if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}
$smarty->assign('mass_edit_all_count', mr2number(sql("select count(*) from $table_name where 1=1 $where")));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['title'] <> '' ? $_POST['title'] : $_POST['album_id']), $lang['albums']['album_edit']));

	$plugins_list = array('categories_autogeneration', 'tags_autogeneration', 'models_autogeneration');
	sort($plugins_list);
	$list_post_process_plugins = array();
	foreach ($plugins_list as $k => $v)
	{
		if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
		{
			continue;
		}
		$file_data = file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
		preg_match("|<plugin_types>(.*?)</plugin_types>|is", $file_data, $temp_find);
		$plugin_types = explode(',', trim($temp_find[1]));
		$is_process_plugin = 0;
		foreach ($plugin_types as $type)
		{
			if ($type == 'process_object')
			{
				$is_process_plugin = 1;
			}
		}

		if ($is_process_plugin == 1)
		{
			require_once("$config[project_path]/admin/plugins/$v/$v.php");
			$process_plugin_function = "{$v}IsEnabled";
			if (function_exists($process_plugin_function))
			{
				if ($process_plugin_function())
				{
					if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
					{
						require_once("$config[project_path]/admin/plugins/$v/langs/english.php");
					}
					if (($_SESSION['userdata']['lang'] != 'english') && (is_file("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php")))
					{
						require_once("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php");
					}
					$list_post_process_plugins[] = array('plugin_id' => $v, 'title' => $lang['plugins'][$v]['title']);
				}
			}
		}
	}
	$smarty->assign('list_post_process_plugins', $list_post_process_plugins);
} elseif ($_REQUEST['action'] == 'manage_images')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['album_info']['title'] <> '' ? $_POST['album_info']['title'] : $_POST['album_info']['album_id']), $lang['albums']['images_header_mgmt']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['albums']['album_add']);
} elseif ($_REQUEST['action'] == 'change_deleted')
{
	$smarty->assign('page_title', str_replace("%1%", ($_POST['title'] <> '' ? $_POST['title'] : $_POST['album_id']), $lang['albums']['album_edit_deleted']));
} elseif ($_REQUEST['action'] == 'mark_deleted')
{
	$smarty->assign('page_title', $lang['albums']['album_mark_deleted']);
} else
{
	$smarty->assign('page_title', $lang['albums']['submenu_option_albums_list']);
}

$content_scheduler_days = intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days = '';
	$sorting_content_scheduler_days = 'desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option']) == 1)
	{
		$now_date = date("Y-m-d H:i:s");
		$where_content_scheduler_days = " and post_date>'$now_date'";
		$sorting_content_scheduler_days = 'asc';
	}
	$smarty->assign('list_updates', mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]albums where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
