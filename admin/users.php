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

$custom_text_fields_count = 10;

$options = get_options();
for ($i = 1; $i <= $custom_text_fields_count; $i++)
{
	if ($options["USER_FIELD_{$i}_NAME"] == '')
	{
		$options["USER_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[0] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_id']] = $country['title'];
}

$list_status_values = array(
	0 => $lang['users']['user_field_status_disabled'],
	1 => $lang['users']['user_field_status_not_confirmed'],
	2 => $lang['users']['user_field_status_active'],
	3 => $lang['users']['user_field_status_premium'],
	4 => $lang['users']['user_field_status_anonymous'],
	6 => $lang['users']['user_field_status_webmaster'],
);

$list_gender_values = array(
	0 => ' ',
	1 => $lang['users']['user_field_gender_male'],
	2 => $lang['users']['user_field_gender_female'],
	3 => $lang['users']['user_field_gender_couple'],
	4 => $lang['users']['user_field_gender_transsexual'],
);

$list_relationship_status_values = array(
	0 => ' ',
	1 => $lang['users']['user_field_relationship_status_single'],
	2 => $lang['users']['user_field_relationship_status_married'],
	3 => $lang['users']['user_field_relationship_status_open'],
	4 => $lang['users']['user_field_relationship_status_divorced'],
	5 => $lang['users']['user_field_relationship_status_widowed'],
);

$list_orientation_values = array(
	0 => ' ',
	1 => $lang['users']['user_field_orientation_unknown'],
	2 => $lang['users']['user_field_orientation_straight'],
	3 => $lang['users']['user_field_orientation_gay'],
	4 => $lang['users']['user_field_orientation_lesbian'],
	5 => $lang['users']['user_field_orientation_bisexual'],
);

$table_fields = array();
$table_fields[] = array('id' => 'user_id',                'title' => $lang['users']['user_field_id'],                  'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'thumb',                  'title' => $lang['users']['user_field_thumb'],               'is_default' => 0, 'type' => 'thumb');
$table_fields[] = array('id' => 'username',               'title' => $lang['users']['user_field_username'],            'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'display_name',           'title' => $lang['users']['user_field_display_name'],        'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'email',                  'title' => $lang['users']['user_field_email'],               'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',            'title' => $lang['users']['user_field_description'],         'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'status_id',              'title' => $lang['users']['user_field_status'],              'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(3 => 'days_left_message'), 'is_nowrap' => 1);
$table_fields[] = array('id' => 'profile_viewed',         'title' => $lang['users']['user_field_visits'],              'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'birth_date',             'title' => $lang['users']['user_field_birth_date'],          'is_default' => 0, 'type' => 'date');
$table_fields[] = array('id' => 'country_id',             'title' => $lang['users']['user_field_country'],             'is_default' => 0, 'type' => 'choice', 'values' => $list_country_values);
$table_fields[] = array('id' => 'city',                   'title' => $lang['users']['user_field_city'],                'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'avatar',                 'title' => $lang['users']['user_field_avatar'],              'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'cover',                  'title' => $lang['users']['user_field_cover'],               'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'gender_id',              'title' => $lang['users']['user_field_gender'],              'is_default' => 0, 'type' => 'choice', 'values' => $list_gender_values);
$table_fields[] = array('id' => 'relationship_status_id', 'title' => $lang['users']['user_field_relationship_status'], 'is_default' => 0, 'type' => 'choice', 'values' => $list_relationship_status_values);
$table_fields[] = array('id' => 'orientation_id',         'title' => $lang['users']['user_field_orientation'],         'is_default' => 0, 'type' => 'choice', 'values' => $list_orientation_values);
$table_fields[] = array('id' => 'favourite_category',     'title' => $lang['users']['user_field_fav_category'],        'is_default' => 0, 'type' => 'refid', 'link' => 'categories.php?action=change&item_id=%id%', 'link_id' => 'favourite_category_id', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'website',                'title' => $lang['users']['user_field_website'],             'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'education',              'title' => $lang['users']['user_field_education'],           'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'occupation',             'title' => $lang['users']['user_field_occupation'],          'is_default' => 0, 'type' => 'text');

for ($i = 1; $i <= $custom_text_fields_count; $i++)
{
	if ($options["ENABLE_USER_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}",     'title' => $options["USER_FIELD_{$i}_NAME"],                 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'is_trusted',             'title' => $lang['users']['user_field_trusted'],             'is_default' => 0, 'type' => 'bool');
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',                 'title' => $lang['users']['user_field_ip'],                  'is_default' => 0, 'type' => 'ip');
}
if (intval($options['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']) == 1)
{
	$table_fields[] = array('id' => 'tokens_required',    'title' => $lang['users']['user_field_tokens_required'],     'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
}
$table_fields[] = array('id' => 'tokens_available',       'title' => $lang['users']['user_field_tokens_available'],    'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'reseller_code',          'title' => $lang['users']['user_field_reseller_code'],       'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'video_watched',          'title' => $lang['users']['user_field_video_watched'],       'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'album_watched',          'title' => $lang['users']['user_field_album_watched'],       'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'logins_count',           'title' => $lang['users']['user_field_logins'],              'is_default' => 1, 'type' => 'number', 'link' => 'stats_users_logins.php?no_filter=true&se_group_by=log&se_user=%id%', 'link_id' => 'username', 'permission' => 'stats|view_user_stats', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'videos_count',           'title' => $lang['users']['user_field_videos'],              'is_default' => 0, 'type' => 'number', 'link' => 'videos.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'videos|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'albums_count',           'title' => $lang['users']['user_field_albums'],              'is_default' => 0, 'type' => 'number', 'link' => 'albums.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'albums|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'posts_count',            'title' => $lang['users']['user_field_posts'],               'is_default' => 0, 'type' => 'number', 'link' => 'posts.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'posts|view', 'ifdisable_zero' => 1);
if ($config['dvds_mode'] == 'channels')
{
	$table_fields[] = array('id' => 'dvds_count',         'title' => $lang['users']['user_field_dvds'],                'is_default' => 0, 'type' => 'number', 'link' => 'dvds.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'dvds|view', 'ifdisable_zero' => 1);
}
$table_fields[] = array('id' => 'playlists_count',        'title' => $lang['users']['user_field_playlists'],           'is_default' => 0, 'type' => 'number', 'link' => 'playlists.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'playlists|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'comments_count',         'title' => $lang['users']['user_field_comments'],            'is_default' => 0, 'type' => 'number', 'link' => 'comments.php?no_filter=true&se_user=%id%', 'link_id' => 'username', 'permission' => 'comments|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'friends_count',          'title' => $lang['users']['user_field_friends'],             'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'activity_rank',          'title' => $lang['users']['user_field_activity_rank'],       'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',             'title' => $lang['users']['user_field_added_date'],          'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'last_login_date',        'title' => $lang['users']['user_field_last_login_date'],     'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "user_id";
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
$search_fields[] = array('id' => 'user_id',      'title' => $lang['users']['user_field_id']);
$search_fields[] = array('id' => 'username',     'title' => $lang['users']['user_field_username']);
$search_fields[] = array('id' => 'display_name', 'title' => $lang['users']['user_field_display_name']);
$search_fields[] = array('id' => 'email',        'title' => $lang['users']['user_field_email']);
$search_fields[] = array('id' => 'city',         'title' => $lang['users']['user_field_city']);
$search_fields[] = array('id' => 'additional',   'title' => $lang['users']['user_divider_additional']);
$search_fields[] = array('id' => 'custom',       'title' => $lang['common']['dg_filter_search_in_custom']);

$table_name = "$config[tables_prefix]users";
$table_key_name = "user_id";
$table_selector = "*";
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
	$_SESSION['save'][$page_name]['se_country_id'] = '';
	$_SESSION['save'][$page_name]['se_gender_id'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_activity'] = '';
	$_SESSION['save'][$page_name]['se_banned_status'] = '';
	$_SESSION['save'][$page_name]['se_is_removal_requested'] = '';
	$_SESSION['save'][$page_name]['se_is_trusted'] = '';
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
	if (isset($_GET['se_country_id']))
	{
		$_SESSION['save'][$page_name]['se_country_id'] = intval($_GET['se_country_id']);
	}
	if (isset($_GET['se_gender_id']))
	{
		$_SESSION['save'][$page_name]['se_gender_id'] = trim($_GET['se_gender_id']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_activity']))
	{
		$_SESSION['save'][$page_name]['se_activity'] = trim($_GET['se_activity']);
	}
	if (isset($_GET['se_banned_status']))
	{
		$_SESSION['save'][$page_name]['se_banned_status'] = intval($_GET['se_banned_status']);
	}
	if (isset($_GET['se_is_removal_requested']))
	{
		$_SESSION['save'][$page_name]['se_is_removal_requested'] = intval($_GET['se_is_removal_requested']);
	}
	if (isset($_GET['se_is_trusted']))
	{
		$_SESSION['save'][$page_name]['se_is_trusted'] = intval($_GET['se_is_trusted']);
	}
}

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1 || $_GET['action'] == 'change' || $field['id'] == $_SESSION['save'][$page_name]['sort_by'])
	{
		if ($field['id'] == 'videos_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]videos where user_id=$table_name.$table_key_name) as videos_count";
		}
		if ($field['id'] == 'albums_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]albums where user_id=$table_name.$table_key_name) as albums_count";
		}
		if ($field['id'] == 'dvds_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]dvds where user_id=$table_name.$table_key_name) as dvds_count";
		}
		if ($field['id'] == 'playlists_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]playlists where user_id=$table_name.$table_key_name) as playlists_count";
		}
		if ($field['id'] == 'posts_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]posts where user_id=$table_name.$table_key_name) as posts_count";
		}
		if ($field['id'] == 'comments_count')
		{
			$table_selector .= ", (select count(*) from $config[tables_prefix]comments where user_id=$table_name.$table_key_name) as comments_count";
		}
		if ($field['id'] == 'favourite_category')
		{
			$table_selector .= ", (select title from $config[tables_prefix]categories where category_id=$table_name.favourite_category_id) as favourite_category";
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
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= $custom_text_fields_count; $i++)
				{
					if ($options["ENABLE_USER_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'additional')
			{
				$where_search .= " or $table_name.website like '%$q%'";
				$where_search .= " or $table_name.education like '%$q%'";
				$where_search .= " or $table_name.occupation like '%$q%'";
				$where_search .= " or $table_name.about_me like '%$q%'";
				$where_search .= " or $table_name.interests like '%$q%'";
				$where_search .= " or $table_name.favourite_movies like '%$q%'";
				$where_search .= " or $table_name.favourite_music like '%$q%'";
				$where_search .= " or $table_name.favourite_books like '%$q%'";
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
	$where .= " and status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and status_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '2')
{
	$where .= " and status_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '3')
{
	$where .= " and status_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '4')
{
	$where .= " and status_id=4";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '6')
{
	$where .= " and status_id=6";
	$table_filtered = 1;
}

if (intval($_SESSION['save'][$page_name]['se_country_id']) > 0)
{
	$where .= " and country_id=" . intval($_SESSION['save'][$page_name]['se_country_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_gender_id'] == '1')
{
	$where .= " and gender_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_gender_id'] == '2')
{
	$where .= " and gender_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_gender_id'] == '3')
{
	$where .= " and gender_id=3";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_gender_id'] == '4')
{
	$where .= " and gender_id=4";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/description':
	case 'empty/avatar':
	case 'empty/cover':
	case 'empty/city':
	case 'empty/website':
	case 'empty/education':
	case 'empty/occupation':
	case 'empty/about_me':
	case 'empty/interests':
	case 'empty/favourite_movies':
	case 'empty/favourite_music':
	case 'empty/favourite_books':
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
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'empty/favourite_category_id':
	case 'empty/country_id':
	case 'empty/gender_id':
	case 'empty/relationship_status_id':
	case 'empty/orientation_id':
	case 'empty/profile_viewed':
	case 'empty/tokens_available':
	case 'empty/tokens_required':
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=0";
		$table_filtered = 1;
		break;
	case 'empty/birth_date':
		$where .= " and birth_date='0000-00-00'";
		$table_filtered = 1;
		break;
	case 'filled/description':
	case 'filled/avatar':
	case 'filled/cover':
	case 'filled/city':
	case 'filled/website':
	case 'filled/education':
	case 'filled/occupation':
	case 'filled/about_me':
	case 'filled/interests':
	case 'filled/favourite_movies':
	case 'filled/favourite_music':
	case 'filled/favourite_books':
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
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
	case 'filled/favourite_category_id':
	case 'filled/country_id':
	case 'filled/gender_id':
	case 'filled/relationship_status_id':
	case 'filled/orientation_id':
	case 'filled/profile_viewed':
	case 'filled/tokens_available':
	case 'filled/tokens_required':
		$where .= " and " . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=0";
		$table_filtered = 1;
		break;
	case 'filled/birth_date':
		$where .= " and birth_date!='0000-00-00'";
		$table_filtered = 1;
		break;
}

$now_date = date("Y-m-d H:i:s");
switch ($_SESSION['save'][$page_name]['se_activity'])
{
	case 'have/logins':
		$where .= " and logins_count>0";
		$table_filtered = 1;
		break;
	case 'have/logins_week':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))<604800";
		$table_filtered = 1;
		break;
	case 'have/logins_month':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))<2592000";
		$table_filtered = 1;
		break;
	case 'have/logins_year':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))<31536000";
		$table_filtered = 1;
		break;
	case 'have/videos':
		$where .= " and exists (select video_id from $config[tables_prefix]videos where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'have/albums':
		$where .= " and exists (select album_id from $config[tables_prefix]albums where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'have/dvds':
		$where .= " and exists (select dvd_id from $config[tables_prefix]dvds where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'have/playlists':
		$where .= " and exists (select playlist_id from $config[tables_prefix]playlists where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'have/comments':
		$where .= " and exists (select comment_id from $config[tables_prefix]comments where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'have/friends':
		$where .= " and friends_count>0";
		$table_filtered = 1;
		break;
	case 'no/logins':
		$where .= " and logins_count=0";
		$table_filtered = 1;
		break;
	case 'no/logins_week':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))>604800";
		$table_filtered = 1;
		break;
	case 'no/logins_month':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))>2592000";
		$table_filtered = 1;
		break;
	case 'no/logins_year':
		$where .= " and (UNIX_TIMESTAMP('$now_date')-UNIX_TIMESTAMP(last_login_date))>31536000";
		$table_filtered = 1;
		break;
	case 'no/videos':
		$where .= " and not exists (select video_id from $config[tables_prefix]videos where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'no/albums':
		$where .= " and not exists (select album_id from $config[tables_prefix]albums where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'no/dvds':
		$where .= " and not exists (select dvd_id from $config[tables_prefix]dvds where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'no/playlists':
		$where .= " and not exists (select playlist_id from $config[tables_prefix]playlists where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'no/comments':
		$where .= " and not exists (select comment_id from $config[tables_prefix]comments where user_id=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'no/friends':
		$where .= " and friends_count=0";
		$table_filtered = 1;
		break;
}

if ($_SESSION['save'][$page_name]['se_banned_status'] == '1')
{
	$where .= " and (login_protection_is_banned=1 and login_protection_restore_code<>0)";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_banned_status'] == '2')
{
	$where .= " and (login_protection_is_banned=1 and login_protection_restore_code=0)";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_removal_requested'] == '1')
{
	$where .= " and is_removal_requested=1";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_is_trusted'] == '1')
{
	$where .= " and is_trusted=1";
	$table_filtered = 1;
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

	validate_field('uniq', $_POST['username'], $lang['users']['user_field_username'], array('field_name_in_base' => 'username'));

	if ($_POST['action'] == 'add_new_complete' || $_POST['pass1'] <> '')
	{
		if (validate_field('empty', $_POST['pass1'], $lang['users']['user_field_password']) && validate_field('empty', $_POST['pass2'], $lang['users']['user_field_password_confirm']))
		{
			if ($_POST['pass1'] <> $_POST['pass2'])
			{
				$errors[] = get_aa_error('password_confirmation', $lang['users']['user_field_password_confirm']);
			}
		}
	}

	$validate_avatar = 'min_image_size';
	$resize_avatar = $options['USER_AVATAR_TYPE'];
	switch ($resize_avatar)
	{
		case 'max_size':
			$validate_avatar = 'min_image_width_or_height';
			break;
		case 'max_width':
			$validate_avatar = 'min_image_width';
			break;
		case 'max_height':
			$validate_avatar = 'min_image_height';
			break;
	}
	if (!in_array($resize_avatar, array('need_size', 'max_size', 'max_width', 'max_height')))
	{
		$resize_avatar = 'need_size';
	}

	$validate_cover = 'min_image_size';
	$resize_cover = $options['USER_COVER_TYPE'];
	switch ($resize_cover)
	{
		case 'max_size':
			$validate_cover = 'min_image_width_or_height';
			break;
		case 'max_width':
			$validate_cover = 'min_image_width';
			break;
		case 'max_height':
			$validate_cover = 'min_image_height';
			break;
	}
	if (!in_array($resize_cover, array('need_size', 'max_size', 'max_width', 'max_height')))
	{
		$resize_cover = 'need_size';
	}

	validate_field('email', $_POST['email'], $lang['users']['user_field_email']);
	validate_field('uniq', $_POST['display_name'], $lang['users']['user_field_display_name'], array('field_name_in_base' => 'display_name'));
	validate_field('file', 'avatar', $lang['users']['user_field_avatar'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_avatar => $options['USER_AVATAR_SIZE']));
	validate_field('file', 'cover', $lang['users']['user_field_cover'], array('is_image' => '1', 'allowed_ext' => $config['image_allowed_ext'], $validate_cover => $options['USER_COVER_SIZE']));

	if (isset($_POST["tokens_required"]))
	{
		if ($_POST["tokens_required"] != '0')
		{
			validate_field('empty_int', $_POST['tokens_required'], $lang['users']['user_field_tokens_required']);
		}
	}

	if (!is_array($errors))
	{
		if ($options['USER_COVER_OPTION'] == 1)
		{
			if ($_POST['avatar_hash'] <> '' && $_POST['cover_hash'] == '')
			{
				$_POST['cover'] = $_POST['avatar'];
				$_POST['cover_hash'] = md5($_POST['avatar_hash']);
				@copy("$config[temporary_path]/$_POST[avatar_hash].tmp", "$config[temporary_path]/$_POST[cover_hash].tmp");
			}
		}

		$birth_date = intval($_POST['birth_date_Year']) . "-" . intval($_POST['birth_date_Month']) . "-" . intval($_POST['birth_date_Day']);
		$item_id = intval($_POST['item_id']);

		$update_array = array(
			'country_id' => intval($_POST['country_id']),
			'favourite_category_id' => intval($_POST['favourite_category_id']),
			'content_source_group_id' => intval($_POST['content_source_group_id']),
			'gender_id' => intval($_POST['gender_id']),
			'relationship_status_id' => intval($_POST['relationship_status_id']),
			'orientation_id' => intval($_POST['orientation_id']),
			'status_id' => intval($_POST['status_id']),
			'username' => $_POST['username'],
			'email' => $_POST['email'],
			'description' => $_POST['description'],
			'display_name' => $_POST['display_name'],
			'birth_date' => $birth_date,
			'website' => $_POST['website'],
			'city' => $_POST['city'],
			'education' => $_POST['education'],
			'occupation' => $_POST['occupation'],
			'about_me' => $_POST['about_me'],
			'interests' => $_POST['interests'],
			'favourite_movies' => $_POST['favourite_movies'],
			'favourite_music' => $_POST['favourite_music'],
			'favourite_books' => $_POST['favourite_books'],
			'login_protection_is_skipped' => intval($_POST['login_protection_is_skipped']),
			'is_trusted' => intval($_POST['is_trusted']),
		);
		for ($i = 1; $i <= $custom_text_fields_count; $i++)
		{
			if (isset($_POST["custom{$i}"]))
			{
				$update_array["custom{$i}"] = $_POST["custom{$i}"];
			}
		}
		if (isset($_POST["account_paypal"]))
		{
			$update_array["account_paypal"] = $_POST["account_paypal"];
		}
		if (isset($_POST["tokens_required"]))
		{
			$update_array["tokens_required"] = intval($_POST["tokens_required"]);
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array['pass'] = generate_password_hash($_POST['pass1']);
			$update_array['added_date'] = date("Y-m-d H:i:s");
			$item_id = sql_insert("insert into $table_name set ?%", $update_array);

			if ($_POST['avatar'] <> '')
			{
				$target_path = get_dir_by_id($item_id);
				$avatar_ext = strtolower(end(explode(".", $_POST['avatar'])));
				$avatar_filename = "$item_id.$avatar_ext";
				transfer_uploaded_file('avatar', "$config[content_path_avatars]/$target_path/$avatar_filename");
				resize_image($resize_avatar, "$config[content_path_avatars]/$target_path/$avatar_filename", "$config[content_path_avatars]/$target_path/$avatar_filename", $options['USER_AVATAR_SIZE']);
				sql_pr("update $table_name set avatar=? where $table_key_name=?", "$target_path/$avatar_filename", $item_id);
			}
			if ($_POST['cover'] <> '')
			{
				$target_path = get_dir_by_id($item_id);
				$cover_ext = strtolower(end(explode(".", $_POST['cover'])));
				$cover_filename = "{$item_id}c.$cover_ext";
				transfer_uploaded_file('cover', "$config[content_path_avatars]/$target_path/$cover_filename");
				resize_image($resize_cover, "$config[content_path_avatars]/$target_path/$cover_filename", "$config[content_path_avatars]/$target_path/$cover_filename", $options['USER_COVER_SIZE']);
				sql_pr("update $table_name set cover=? where $table_key_name=?", "$target_path/$cover_filename", $item_id);
			}
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			if ($_POST['pass1'] <> '')
			{
				$update_array['pass'] = generate_password_hash($_POST['pass1']);
				$update_array['pass_bill'] = '';
			}
			if (intval($_POST['decline_removal']) == 1)
			{
				$update_array['is_removal_requested'] = 0;
				$update_array['removal_reason'] = '';
			}
			if (intval($_POST['is_unbanned']) == 1)
			{
				$update_array['login_protection_is_banned'] = 0;
				$update_array['login_protection_date_from'] = date("Y-m-d H:i:s");
				$update_array['login_protection_restore_code'] = 0;
				$update_array['login_protection_bans_count'] = 0;
			}
			sql_pr("update $table_name set ?% where $table_key_name=$item_id", $update_array);

			if ($_POST['avatar_hash'] <> '')
			{
				if ($old_data['avatar'] != "")
				{
					@unlink("$config[content_path_avatars]/$old_data[avatar]");
				}

				$target_path = get_dir_by_id($item_id);
				$avatar_ext = strtolower(end(explode(".", $_POST['avatar'])));
				$avatar_filename = "$item_id.$avatar_ext";
				transfer_uploaded_file('avatar', "$config[content_path_avatars]/$target_path/$avatar_filename");
				resize_image($resize_avatar, "$config[content_path_avatars]/$target_path/$avatar_filename", "$config[content_path_avatars]/$target_path/$avatar_filename", $options['USER_AVATAR_SIZE']);
				sql_pr("update $table_name set avatar=? where $table_key_name=?", "$target_path/$avatar_filename", $item_id);
			} elseif ($_POST['avatar'] == '')
			{
				if ($old_data['avatar'] != "")
				{
					@unlink("$config[content_path_avatars]/$old_data[avatar]");
				}
				sql_pr("update $table_name set avatar='' where $table_key_name=?", $item_id);
			}
			if ($_POST['cover_hash'] <> '')
			{
				if ($old_data['cover'] != "")
				{
					@unlink("$config[content_path_avatars]/$old_data[cover]");
				}

				$target_path = get_dir_by_id($item_id);
				$cover_ext = strtolower(end(explode(".", $_POST['cover'])));
				$cover_filename = "{$item_id}c.$cover_ext";
				transfer_uploaded_file('cover', "$config[content_path_avatars]/$target_path/$cover_filename");
				resize_image($resize_cover, "$config[content_path_avatars]/$target_path/$cover_filename", "$config[content_path_avatars]/$target_path/$cover_filename", $options['USER_COVER_SIZE']);
				sql_pr("update $table_name set cover=? where $table_key_name=?", "$target_path/$cover_filename", $item_id);
			} elseif ($_POST['cover'] == '')
			{
				if ($old_data['cover'] != "")
				{
					@unlink("$config[content_path_avatars]/$old_data[cover]");
				}
				sql_pr("update $table_name set cover='' where $table_key_name=?", $item_id);
			}

			if ($options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO'] == $old_data['username'])
			{
				sql_pr("update $config[tables_prefix]options set value=? where variable='DEFAULT_USER_IN_ADMIN_ADD_VIDEO'", $_POST['username']);
			}
			if ($options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM'] == $old_data['username'])
			{
				sql_pr("update $config[tables_prefix]options set value=? where variable='DEFAULT_USER_IN_ADMIN_ADD_ALBUM'", $_POST['username']);
			}
			if ($options['DEFAULT_USER_IN_ADMIN_ADD_POST'] == $old_data['username'])
			{
				sql_pr("update $config[tables_prefix]options set value=? where variable='DEFAULT_USER_IN_ADMIN_ADD_POST'", $_POST['username']);
			}

			if ($_POST['pass1'] != '' || $_POST['email'] != $old_data['email'])
			{
				$user_billings = mr2array_list(sql_pr("select distinct internal_provider_id from $config[tables_prefix]bill_transactions where user_id=?", $item_id));
				if (count($user_billings) > 0)
				{
					require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
					foreach ($user_billings as $user_billing_id)
					{
						if (is_file("$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php"))
						{
							require_once "$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php";
							$payment_processor = KvsPaymentProcessorFactory::create_instance($user_billing_id);
							if ($payment_processor instanceof KvsPaymentProcessor)
							{
								if ($_POST['pass1'] != '')
								{
									$payment_processor->process_password_change($item_id, $_POST['pass1']);
								}
								if ($_POST['email'] != $old_data['email'])
								{
									$payment_processor->process_email_change($item_id, $_POST['email']);
								}
							}
						}
					}
				}
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

if ($_REQUEST['action'] == 'login')
{
	foreach ($_SESSION as $key => $value)
	{
		if ($key != 'userdata' && $key != 'save' && $key != 'runtime_params' && $key != 'lock_ips')
		{
			unset($_SESSION[$key]);
		}
	}

	$user_id = intval($_REQUEST['user_id']);
	$remember_me_key = mr2string(sql_pr("select remember_me_key from $config[tables_prefix]users where remember_me_valid_for>=? and user_id=? and status_id not in (0,1,4)", date("Y-m-d H:i:s"), $user_id));
	if (!$remember_me_key)
	{
		$rnd = mt_rand(10000000, 99999999);
		$remember_me_key = md5($config['installation_id'] . $rnd);
		sql_pr("update $config[tables_prefix]users set remember_me_key=?, remember_me_valid_for=DATE_ADD(?, INTERVAL 1 DAY) where user_id=?", $remember_me_key, date("Y-m-d H:i:s"), $user_id);
	}
	$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
	setcookie("kt_member", $remember_me_key, time() + 86400, "/", ".$domain");
	header("Location: $config[project_url]");
	die;
}

if ($_REQUEST['action'] == 'send_message' && intval($_REQUEST['item_id']) > 0)
{
	$username = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", intval($_REQUEST['item_id'])));
	return_ajax_success("messages.php?action=add_new&amp;user=$username");
}

if ($_REQUEST['action'] == 'add_transaction' && intval($_REQUEST['item_id']) > 0)
{
	$username = mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?", intval($_REQUEST['item_id'])));
	return_ajax_success("bill_transactions.php?action=add_new&amp;user=$username");
}

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
			$temp_data = mr2array(sql("select *, (select count(*) from $config[tables_prefix]videos where user_id=$table_name.$table_key_name) as videos_amount, (select count(*) from $config[tables_prefix]albums where user_id=$table_name.$table_key_name) as albums_amount, (select count(*) from $config[tables_prefix]posts where user_id=$table_name.$table_key_name) as posts_amount, (select count(*) from $config[tables_prefix]playlists where user_id=$table_name.$table_key_name) as playlists_amount, (select count(*) from $config[tables_prefix]comments where user_id=$table_name.$table_key_name) as comments_amount from $table_name where $table_key_name in ($row_select)"));
			foreach ($temp_data as $res)
			{
				if ($res['videos_amount'] > 0 || $res['albums_amount'] > 0 || $res['posts_amount'] > 0 || $res['playlists_amount'] > 0 || $res['comments_amount'] > 0)
				{
					$errors[] = get_aa_error('user_cannot_be_deleted', $res['username']);
				}
			}
		}
		if (is_array($errors))
		{
			return_ajax_errors($errors);
		} else
		{
			if ($_REQUEST['batch_action'] == 'delete_with_content')
			{
				$video_ids = mr2array_list(sql("select video_id from $config[tables_prefix]videos where user_id in ($row_select)"));
				foreach ($video_ids as $video_id)
				{
					sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id);
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $video_id, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize(array()), date("Y-m-d H:i:s"));
				}
				$album_ids = mr2array_list(sql("select album_id from $config[tables_prefix]albums where user_id in ($row_select)"));
				foreach ($album_ids as $album_id)
				{
					sql_pr("update $config[tables_prefix]albums set status_id=4 where album_id=?", $album_id);
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=2, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $album_id, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?", $album_id, serialize(array()), date("Y-m-d H:i:s"));
				}

				$playlist_ids_str = implode(',', mr2array_list(sql("select playlist_id from $config[tables_prefix]playlists where user_id in ($row_select)")));
				if ($playlist_ids_str != '')
				{
					$list_ids_playlists_comments = array_map("intval", mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($playlist_ids_str) and object_type_id=13")));
					$list_ids_playlists_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $config[tables_prefix]categories_playlists where playlist_id in ($playlist_ids_str)")));
					$list_ids_playlists_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $config[tables_prefix]tags_playlists where playlist_id in ($playlist_ids_str)")));

					sql("delete from $config[tables_prefix]fav_videos where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]categories_playlists where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]tags_playlists where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]flags_playlists where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]flags_history where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]flags_messages where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]users_events where playlist_id in ($playlist_ids_str)");
					sql("delete from $config[tables_prefix]comments where object_id in ($playlist_ids_str) and object_type_id=13");
					sql("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($playlist_ids_str) and subscribed_type_id=13");
					sql("delete from $config[tables_prefix]playlists where playlist_id in ($playlist_ids_str)");

					if (count($list_ids_playlists_comments) > 0)
					{
						$list_ids_playlists_comments = implode(',', $list_ids_playlists_comments);
						sql("update $config[tables_prefix]users set
								comments_playlists_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
								comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_playlists_comments)"
						);
					}
					if (count($list_ids_playlists_categories) > 0)
					{
						$list_ids_playlists_categories = implode(',', $list_ids_playlists_categories);
						sql_pr("update $config[tables_prefix]categories set total_playlists=(select count(*) from $config[tables_prefix]categories_playlists where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_playlists_categories)");
					}
					if (count($list_ids_playlists_tags) > 0)
					{
						$list_ids_playlists_tags = implode(',', $list_ids_playlists_tags);
						sql_pr("update $config[tables_prefix]tags set total_playlists=(select count(*) from $config[tables_prefix]tags_playlists where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_playlists_tags)");
					}
				}

				$post_ids = array_map("intval", mr2array_list(sql("select post_id from $config[tables_prefix]posts where user_id in ($row_select)")));
				if (count($post_ids) > 0)
				{
					$post_ids_str = implode(',', $post_ids);
					$list_ids_posts_comments = array_map("intval", mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($post_ids_str) and object_type_id=12")));

					sql("delete from $config[tables_prefix]posts where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]tags_posts where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]categories_posts where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]models_posts where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]flags_posts where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]flags_history where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]flags_messages where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]users_events where post_id in ($post_ids_str)");
					sql("delete from $config[tables_prefix]comments where object_id in ($post_ids_str) and object_type_id=12");

					if (count($list_ids_posts_comments) > 0)
					{
						$list_ids_posts_comments = implode(',', $list_ids_posts_comments);
						sql("update $config[tables_prefix]users set
								comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
								comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_posts_comments)"
						);
					}

					foreach ($post_ids as $item_id)
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

				$list_ids_comments = mr2array_list(sql("select distinct object_id from $config[tables_prefix]comments where $table_key_name in ($row_select)"));
				$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));
				sql("delete from $config[tables_prefix]comments where $table_key_name in ($row_select)");
				sql("update $config[tables_prefix]videos set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]videos.video_id and object_type_id=1 and is_approved=1) where video_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]albums set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]albums.album_id and object_type_id=2 and is_approved=1) where album_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]content_sources set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]content_sources.content_source_id and object_type_id=3 and is_approved=1) where content_source_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]models set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]models.model_id and object_type_id=4 and is_approved=1) where model_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]dvds set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]dvds.dvd_id and object_type_id=5 and is_approved=1) where dvd_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]posts set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]posts.post_id and object_type_id=12 and is_approved=1) where post_id in ($list_ids_comments)");
				sql("update $config[tables_prefix]playlists set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]playlists.playlist_id and object_type_id=13 and is_approved=1) where playlist_id in ($list_ids_comments)");
			}

			$friend_ids = mr2array_list(sql("select distinct user_id from $config[tables_prefix]friends where friend_id in ($row_select) union all select distinct friend_id from $config[tables_prefix]friends where user_id in ($row_select)"));

			$data = mr2array(sql("select avatar, cover from $table_name where $table_key_name in ($row_select)"));
			foreach ($data as $k => $v)
			{
				if ($v['avatar'] <> '')
				{
					@unlink($config['content_path_avatars'] . "/" . $v['avatar']);
				}
				if ($v['cover'] <> '')
				{
					@unlink($config['content_path_avatars'] . "/" . $v['cover']);
				}
			}
			sql("delete from $table_name where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]users_events where $table_key_name in ($row_select) or user_target_id in ($row_select)");
			sql("delete from $config[tables_prefix]users_subscriptions where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($row_select) and subscribed_type_id=1");
			sql("delete from $config[tables_prefix]users_ignores where user_id in ($row_select) or ignored_user_id in ($row_select)");
			sql("delete from $config[tables_prefix]users_blocked_passwords where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]fav_videos where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]fav_albums where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]comments where $table_key_name in ($row_select)");
			sql("delete from $config[tables_prefix]users_blogs where $table_key_name in ($row_select) or user_from_id in ($row_select)");
			sql("delete from $config[tables_prefix]friends where $table_key_name in ($row_select) or friend_id in ($row_select)");
			sql("delete from $config[tables_prefix]messages where $table_key_name in ($row_select) or user_from_id in ($row_select)");
			sql("delete from $config[tables_prefix]log_logins_users where $table_key_name in ($row_select)");

			sql("update $config[tables_prefix]feedbacks set $table_key_name=0 where $table_key_name in ($row_select)");
			sql("update $config[tables_prefix]dvds set $table_key_name=0 where $table_key_name in ($row_select)");
			sql("update $config[tables_prefix]bill_transactions set ip=0, country_code='' where $table_key_name in ($row_select)");
			sql_pr("update $config[tables_prefix]bill_transactions set status_id=3, access_end_date=?, is_unlimited_access=0 where status_id in (1, 4) and $table_key_name in ($row_select)", date("Y-m-d H:i:s"));
			sql_pr("update $config[tables_prefix]users_purchases set expiry_date=? where profile_id in ($row_select)", date("Y-m-d H:i:s"));

			if (count($friend_ids)>0)
			{
				friends_changed($friend_ids);
			}

			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		}
	} elseif ($_REQUEST['batch_action'] == 'unban')
	{
		sql_pr("update $table_name set login_protection_is_banned=0, login_protection_date_from=?, login_protection_restore_code=0, login_protection_bans_count=0 where $table_key_name in ($row_select) and login_protection_is_banned=1", date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['users']['user_success_message_users_unbanned'];
	} elseif ($_REQUEST['batch_action'] == 'confirm')
	{
		sql("update $table_name set status_id=2 where $table_key_name in ($row_select) and status_id=1");
		$_SESSION['messages'][] = $lang['users']['user_success_message_users_confirmed'];
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where $table_key_name in ($row_select) and status_id in (2,3,6)");
		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		$row_select = explode(",", $row_select);
		foreach ($row_select as $user_id)
		{
			if (mr2number(sql("select count(*) from $config[tables_prefix]bill_transactions where user_id=$user_id and status_id=1")) > 0)
			{
				$new_status_id = 3;
			} else
			{
				$new_status_id = 2;
			}
			sql("update $table_name set status_id=$new_status_id where $table_key_name=$user_id and status_id=0");
		}
		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['last_login_date'] != '0000-00-00 00:00:00')
	{
		$_POST['last_login_days'] = floor((time() - strtotime($_POST['last_login_date'])) / 86400);
	} else {
		$_POST['last_login_days'] = -1;
	}

	$_POST['avg_sess_duration'] = durationToHumanString($_POST['avg_sess_duration']);
	$_POST['transactions'] = mr2array(sql_pr("select * from $config[tables_prefix]bill_transactions where user_id=? order by transaction_id desc", intval($_GET['item_id'])));
	$_POST['payouts'] = mr2array(sql_pr("
			select upu.*, up.status_id
			from $config[tables_prefix]users_payouts_users upu inner join $config[tables_prefix]users_payouts up on upu.payout_id=up.payout_id
			where upu.user_id=? and up.status_id in (1,2)
			order by upu.added_date desc
	", intval($_GET['item_id'])));

	$last_open_transaction = mr2array_single(sql_pr("select transaction_id, status_id, unix_timestamp(access_end_date)-unix_timestamp(?) as time_left, duration_rebill, is_unlimited_access from $config[tables_prefix]bill_transactions where user_id=? and status_id in (1, 4) order by transaction_id desc limit 1", date("Y-m-d H:i:s"), intval($_GET['item_id'])));
	if ($last_open_transaction['transaction_id'] > 0)
	{
		if ($last_open_transaction['is_unlimited_access'] == 0)
		{
			if ($last_open_transaction['status_id'] == 4)
			{
				$last_open_transaction['time_left'] = $last_open_transaction['duration_rebill'];
			} else
			{
				$last_open_transaction['time_left'] = max(0, ceil($last_open_transaction['time_left'] / 86400));
			}
		}
		$_POST['last_open_transaction'] = $last_open_transaction;
	}

	$_POST['tokens_earned'] = mr2number(sql_pr("select sum(tokens_granted) from $config[tables_prefix]log_awards_users where user_id=?", intval($_GET['item_id'])));
	$_POST['tokens_paid'] = mr2number(sql_pr("select sum(upu.tokens) from $config[tables_prefix]users_payouts_users upu inner join $config[tables_prefix]users_payouts up on upu.payout_id=up.payout_id where upu.user_id=? and up.status_id in (1,2)", intval($_GET['item_id'])));

	$_POST['total_payments'] = '';
	$total_payments = mr2array(sql_pr("select sum(price) as price, currency_code from $config[tables_prefix]bill_transactions where user_id=? and price>0 and currency_code!='' group by currency_code order by sum(price) desc", intval($_GET['item_id'])));
	foreach ($total_payments as $total_payments_item)
	{
		$_POST['total_payments'] .= number_format($total_payments_item['price'], 2) . " $total_payments_item[currency_code], ";
	}
	$_POST['total_payments'] = trim($_POST['total_payments'], ' ,');

	if ($_POST['login_protection_is_banned'] == 1)
	{
		if ($_POST['login_protection_date_from'] <> '0000-00-00 00:00:00')
		{
			$logins_where = "and login_date>'$_POST[login_protection_date_from]'";
		}
		$logins_data = mr2array(sql_pr("select * from $config[tables_prefix]log_logins_users where user_id=? $logins_where", intval($_GET['item_id'])));
		$unique_ips = array();
		$unique_ipmasks = array();
		$unique_countries = array();
		$unique_browsers = array();
		foreach ($logins_data as $login)
		{
			if (!isset($unique_ips[$login['ip']]))
			{
				$unique_ips[$login['ip']] = 1;
			}
			$parts = explode(".", int2ip($login['ip']));
			$ipmask = "$parts[0].$parts[1].$parts[2].0";
			if (!isset($unique_ipmasks[$ipmask]))
			{
				$unique_ipmasks[$ipmask] = 1;
			}
			if (!isset($unique_countries[$login['country_code']]))
			{
				$unique_countries[$login['country_code']] = 1;
			}
			if (!isset($unique_browsers[$login['user_agent']]))
			{
				$unique_browsers[$login['user_agent']] = 1;
			}
		}
		$_POST['unique_ips'] = count($unique_ips);
		$_POST['unique_ipmasks'] = count($unique_ipmasks);
		$_POST['unique_countries'] = count($unique_countries);
		$_POST['unique_browsers'] = count($unique_browsers);
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['birth_date'] = "0000-00-00";
	$_POST['status_id'] = 2;
	$_POST['tokens_required'] = 0;
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
		if ($v['country_id'] > 0)
		{
			$data[$k]['country'] = $list_countries['name'][$v['country_id']];
		}
		if ($v['avatar'])
		{
			$data[$k]['avatar_url'] = "$config[content_url_avatars]/$v[avatar]";
			$data[$k]['avatar'] = substr($data[$k]['avatar'], strpos($v['avatar'], '/') + 1);
			$data[$k]['thumb'] = $data[$k]['avatar_url'];
		}
		if ($v['cover'])
		{
			$data[$k]['cover_url'] = "$config[content_url_avatars]/$v[cover]";
			$data[$k]['cover'] = substr($data[$k]['cover'], strpos($v['cover'], '/') + 1);
		}
		if ($v['status_id'] == 3)
		{
			$last_open_transaction = mr2array_single(sql_pr("select * from $config[tables_prefix]bill_transactions where status_id in (1, 4) and user_id=? order by transaction_id desc limit 1", $v["user_id"]));
			if ($last_open_transaction['is_unlimited_access'] == 1)
			{
				$data[$k]['days_left_message'] = $lang['users']['user_field_status_premium_unlimited'];
			} elseif ($last_open_transaction['status_id'] == 4)
			{
				$data[$k]['days_left_message'] = str_replace("%1%", $last_open_transaction['duration_rebill'], $lang['users']['user_field_status_premium_left']);
			} else
			{
				$data[$k]['days_left_message'] = str_replace("%1%", round((strtotime($last_open_transaction['access_end_date']) - time()) / 86400), $lang['users']['user_field_status_premium_left']);
			}
			if ($v["is_trial"] == 1)
			{
				$data[$k]['days_left_message'] .= ", " . $lang['users']['user_field_status_premium_trial'];
			}
			$data[$k]['country'] = $list_countries['name'][$v['country_id']];
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$custom_text_fields = array();
for ($i = 1; $i <= $custom_text_fields_count; $i++)
{
	if ($options["ENABLE_USER_FIELD_{$i}"])
	{
		$field_info = array(
			'name' => $options["USER_FIELD_{$i}_NAME"],
			'field_name' => "custom{$i}",
			'value' => $_POST["custom{$i}"]
		);
		$custom_text_fields[] = $field_info;
	}
}

$smarty = new mysmarty();
$smarty->assign('list_categories', mr2array(sql("select category_id, title from $config[tables_prefix]categories order by title asc")));
$smarty->assign('list_cs_groups', mr2array(sql("select content_source_group_id, title from $config[tables_prefix]content_sources_groups order by title asc")));
$smarty->assign('list_countries', $list_countries);
$smarty->assign('options', $options);

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
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('custom_text_fields', $custom_text_fields);
$smarty->assign('custom_file_fields', array());

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['username'], $lang['users']['user_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['user_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_users_list']);
}

$smarty->display("layout.tpl");
