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

$list_status_values = array(
	0 => $lang['users']['playlist_field_status_disabled'],
	1 => $lang['users']['playlist_field_status_active'],
);

$list_type_values = array(
	0 => $lang['users']['playlist_field_type_public'],
	1 => $lang['users']['playlist_field_type_private'],
);

$table_fields = array();
$table_fields[] = array('id' => 'playlist_id',     'title' => $lang['users']['playlist_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',           'title' => $lang['users']['playlist_field_title'],          'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',             'title' => $lang['users']['playlist_field_directory'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'description',     'title' => $lang['users']['playlist_field_description'],    'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'status_id',       'title' => $lang['users']['playlist_field_status'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'is_private',      'title' => $lang['users']['playlist_field_type'],           'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values);
$table_fields[] = array('id' => 'user',            'title' => $lang['users']['playlist_field_user'],           'is_default' => 1, 'type' => 'user');
$table_fields[] = array('id' => 'rating',          'title' => $lang['users']['playlist_field_rating'],         'is_default' => 1, 'type' => 'float');
$table_fields[] = array('id' => 'playlist_viewed', 'title' => $lang['users']['playlist_field_visits'],         'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'tags',            'title' => $lang['users']['playlist_field_tags'],           'is_default' => 0, 'type' => 'list', 'link' => 'tags.php?action=change&item_id=%id%', 'permission' => 'tags|view');
$table_fields[] = array('id' => 'categories',      'title' => $lang['users']['playlist_field_categories'],     'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'is_locked',       'title' => $lang['users']['playlist_field_lock_website'],   'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'videos_amount',   'title' => $lang['users']['playlist_field_videos_count'],   'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'comments_amount', 'title' => $lang['users']['playlist_field_comments_count'], 'is_default' => 0, 'type' => 'number', 'link' => 'comments.php?no_filter=true&se_object_type_id=13&se_object_id=%id%', 'link_id' => 'playlist_id', 'permission' => 'users|manage_comments', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',      'title' => $lang['users']['playlist_field_added_date'],     'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "playlist_id";
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
$search_fields[] = array('id' => 'playlist_id', 'title' => $lang['users']['playlist_field_id']);
$search_fields[] = array('id' => 'title',       'title' => $lang['users']['playlist_field_title']);
$search_fields[] = array('id' => 'dir',         'title' => $lang['users']['playlist_field_directory']);
$search_fields[] = array('id' => 'description', 'title' => $lang['users']['playlist_field_description']);

$table_name = "$config[tables_prefix]playlists";
$table_key_name = "playlist_id";

$table_selector = "$table_name.*, $table_name.rating / $table_name.rating_amount as rating, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, (select count(*) from $config[tables_prefix]fav_videos where $table_key_name=$table_name.$table_key_name) as videos_amount, (select count(*) from $config[tables_prefix]comments where object_type_id=13 and object_id=$table_name.$table_key_name) as comments_amount";
$table_projector = "$table_name left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";

$table_name_categories = "$config[tables_prefix]categories_playlists";
$table_name_tags = "$config[tables_prefix]tags_playlists";
$column_name_total = "total_playlists";

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
	$_SESSION['save'][$page_name]['se_is_private'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_category'] = '';
	$_SESSION['save'][$page_name]['se_tag'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_flag_id'] = '';
	$_SESSION['save'][$page_name]['se_flag_values_amount'] = '';
	$_SESSION['save'][$page_name]['se_review_flag'] = '';
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
	if (isset($_GET['se_is_private']))
	{
		$_SESSION['save'][$page_name]['se_is_private'] = trim($_GET['se_is_private']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_category']))
	{
		$_SESSION['save'][$page_name]['se_category'] = trim($_GET['se_category']);
	}
	if (isset($_GET['se_tag']))
	{
		$_SESSION['save'][$page_name]['se_tag'] = trim($_GET['se_tag']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_flag_id']))
	{
		$_SESSION['save'][$page_name]['se_flag_id'] = intval($_GET['se_flag_id']);
	}
	if (isset($_GET['se_flag_values_amount']))
	{
		$_SESSION['save'][$page_name]['se_flag_values_amount'] = intval($_GET['se_flag_values_amount']);
	}
	if (isset($_GET['se_review_flag']))
	{
		$_SESSION['save'][$page_name]['se_review_flag'] = intval($_GET['se_review_flag']);
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

if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
	$where .= " and $config[tables_prefix]users.username='$q'";
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
		$where .= " and $table_name.description=''";
		$table_filtered = 1;
		break;
	case 'empty/playlist_viewed':
		$where .= " and $table_name.playlist_viewed=0";
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
	case 'empty/videos':
		$where .= " and not exists (select video_id from $config[tables_prefix]fav_videos where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
	case 'filled/description':
		$where .= " and $table_name.description!=''";
		$table_filtered = 1;
		break;
	case 'filled/playlist_viewed':
		$where .= " and $table_name.playlist_viewed!=0";
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
	case 'filled/videos':
		$where .= " and exists (select video_id from $config[tables_prefix]fav_videos where $table_key_name=$table_name.$table_key_name)";
		$table_filtered = 1;
		break;
}

if ($_SESSION['save'][$page_name]['se_flag_id'] > 0)
{
	$flag_amount = max(1, intval($_SESSION['save'][$page_name]['se_flag_values_amount']));
	$where .= " and (select sum(votes) from $config[tables_prefix]flags_playlists where $table_key_name=$table_name.$table_key_name and flag_id=" . $_SESSION['save'][$page_name]['se_flag_id'] . ")>=$flag_amount";
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

if ($_SESSION['save'][$page_name]['se_is_private'] == '0')
{
	$where .= " and $table_name.is_private=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_is_private'] == '1')
{
	$where .= " and $table_name.is_private=1";
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

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'rating')
{
	$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.rating_amount";
} elseif ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'videos_amount')
{
	$sort_by = "videos_amount";
} elseif ($sort_by == 'comments_amount')
{
	$sort_by = "comments_amount";
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

	validate_field('empty', $_POST['title'], $lang['users']['playlist_field_title']);
	if (validate_field('empty', $_POST['user'], $lang['users']['playlist_field_user']))
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
		{
			$errors[] = get_aa_error('invalid_user', $lang['users']['playlist_field_user']);
		}
	}

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);

		if ($_POST['dir'] == '')
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

		$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_POST['user']));

		$update_array = array();
		$update_array['title'] = $_POST['title'];
		$update_array['dir'] = $_POST['dir'];
		$update_array['description'] = $_POST['description'];
		$update_array['user_id'] = $user_id;
		$update_array['status_id'] = intval($_POST['status_id']);
		$update_array['is_private'] = intval($_POST['is_private']);
		$update_array['is_locked'] = intval($_POST['is_locked']);

		if ($update_array['is_private'] == 1)
		{
			$update_array['status_id'] = 1;
			$update_array['is_locked'] = 0;
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array['rating'] = 0;
			$update_array['rating_amount'] = 1;
			$update_array['added_date'] = date("Y-m-d H:i:s");
			$update_array['last_content_date'] = date("Y-m-d H:i:s");

			$item_id = sql_insert("insert into $table_name set ?%", $update_array);

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=13, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_playlist_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			if (count($_POST['delete_flags']) > 0)
			{
				$delete_flags = implode(",", array_map("intval", $_REQUEST['delete_flags']));
				sql_pr("delete from $config[tables_prefix]flags_playlists where $table_key_name=? and flag_id in ($delete_flags)", $item_id);
			}

			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

			if ($user_id != $old_playlist_data['user_id'])
			{
				sql_pr("update $config[tables_prefix]fav_videos set user_id=? where $table_key_name=?", $user_id, $item_id);
			}

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($old_playlist_data[$k] != $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, strlen($update_details) - 2);
			}
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=13, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];

			if ($_POST['is_reviewed'] == 1)
			{
				sql_pr("update $table_name set is_review_needed=0 where $table_key_name=?", $item_id);
			}
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

		settype($_POST['delete_video_ids'], "array");
		foreach ($_POST['delete_video_ids'] as $del_video_id)
		{
			sql_pr("delete from $config[tables_prefix]fav_videos where user_id=? and video_id=? and fav_type=10 and playlist_id=?", $user_id, intval($del_video_id), $item_id);
		}

		$negative_positions = array();

		$list_ids_videos = mr2array_list(sql_pr("select video_id from $config[tables_prefix]fav_videos where playlist_id=?", $item_id));
		foreach ($list_ids_videos as $video_id)
		{
			if (isset($_REQUEST["video_sorting_$video_id"]) && intval($_REQUEST["video_sorting_$video_id"])<0)
			{
				$negative_positions[] = intval($_REQUEST["video_sorting_$video_id"]);
			}
		}
		sort($negative_positions, SORT_ASC);

		foreach ($list_ids_videos as $video_id)
		{
			if (isset($_REQUEST["video_sorting_$video_id"]))
			{
				$video_sorting = intval($_REQUEST["video_sorting_$video_id"]);
				if ($video_sorting > 0)
				{
					$video_sorting += count($negative_positions);
				} elseif ($video_sorting < 0)
				{
					$video_sorting = intval(array_search($video_sorting, $negative_positions)) + 1;
				}
				sql_pr("update $config[tables_prefix]fav_videos set playlist_sort_id=? where playlist_id=? and video_id=?", $video_sorting, $item_id, $video_id);
			}
		}

		settype($_POST['add_video_ids'], "array");
		foreach ($_POST['add_video_ids'] as $add_video_id)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]fav_videos where user_id=? and video_id=? and fav_type=10 and playlist_id=?", $user_id, intval($add_video_id), $item_id)) == 0)
			{
				sql_pr("insert into $config[tables_prefix]fav_videos set user_id=?, video_id=?, fav_type=10, playlist_id=?, added_date=?", $user_id, intval($add_video_id), $item_id, date("Y-m-d H:i:s"));
			}
		}

		sql_pr("update $table_name set total_videos=(select count(*) from $config[tables_prefix]fav_videos where $table_name.$table_key_name=$config[tables_prefix]fav_videos.$table_key_name) where $table_key_name=?", $item_id);

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
	$row_select_str = implode(",", array_map("intval", $_REQUEST['row_select']));
	$row_select = mr2array_list(sql("select $table_key_name from $table_name where $table_key_name in ($row_select_str)"));
	$row_select_str = implode(",", array_map("intval", $row_select));
	if (count($row_select) == 0)
	{
		return_ajax_success($page_name);
	}

	if ($_REQUEST['batch_action'] == 'delete')
	{
		$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($row_select_str) and object_type_id=13"));
		$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

		$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $table_name_categories where $table_key_name in ($row_select_str)")));
		$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $table_name_tags where $table_key_name in ($row_select_str)")));

		// this logic is duplicated in users.php
		sql("delete from $table_name where $table_key_name in ($row_select_str)");
		sql("delete from $table_name_categories where $table_key_name in ($row_select_str)");
		sql("delete from $table_name_tags where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_playlists where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_history where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]flags_messages where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]fav_videos where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]users_events where $table_key_name in ($row_select_str)");
		sql("delete from $config[tables_prefix]comments where object_id in ($row_select_str) and object_type_id=13");
		sql("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($row_select_str) and subscribed_type_id=13");

		if (strlen($list_ids_comments) > 0)
		{
			sql("update $config[tables_prefix]users set
					comments_playlists_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
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

		foreach ($row_select as $item_id)
		{
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=13, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
		}

		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		sql("update $table_name set status_id=1 where status_id=0 and $table_key_name in ($row_select_str)");
		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
		return_ajax_success($page_name);
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where status_id=1 and is_private=0 and $table_key_name in ($row_select_str)");
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
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST'] <> '')
	{
		$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST[$table_key_name], str_replace("%DIR%", $_POST['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
	}

	$_POST['categories'] = mr2array(sql_pr("select category_id, (select title from $config[tables_prefix]categories where category_id=$table_name_categories.category_id) as title from $table_name_categories where $table_key_name=? order by id asc", $_POST[$table_key_name]));
	$_POST['tags'] = implode(", ", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$table_name_tags.tag_id) as tag from $table_name_tags where $table_name_tags.$table_key_name=? order by id asc", $_POST[$table_key_name])));
	$_POST['flags'] = mr2array(sql_pr("select flag_id, title, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_playlists where $config[tables_prefix]flags_playlists.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_playlists.$table_key_name=?) as votes from $config[tables_prefix]flags where group_id=5 having votes>0 order by title asc", $_POST[$table_key_name]));

	$_POST['videos'] = mr2array(sql_pr("select $config[tables_prefix]videos.*, $config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating, $config[tables_prefix]fav_videos.playlist_sort_id as sort_id from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $config[tables_prefix]fav_videos.$table_key_name=? order by $config[tables_prefix]fav_videos.playlist_sort_id asc, $config[tables_prefix]fav_videos.added_date desc", $_POST[$table_key_name]));
	foreach ($_POST['videos'] as $k => $v)
	{
		$_POST['videos'][$k]['duration'] = durationToHumanString($v['duration']);
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = 1;
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
	if ($data[$k]['dir'] <> '' && $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST'] <> '')
	{
		$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k][$table_key_name], str_replace("%DIR%", $data[$k]['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
	}
	if ($_SESSION['save'][$page_name]['grid_columns']['categories'] == 1)
	{
		$data[$k]['categories'] = mr2array(sql_pr("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories inner join $table_name_categories on $config[tables_prefix]categories.category_id=$table_name_categories.category_id where $table_name_categories.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_categories.id asc"));
	}
	if ($_SESSION['save'][$page_name]['grid_columns']['tags'] == 1)
	{
		$data[$k]['tags'] = mr2array(sql_pr("select $config[tables_prefix]tags.tag_id as id, $config[tables_prefix]tags.tag as title from $config[tables_prefix]tags inner join $table_name_tags on $config[tables_prefix]tags.tag_id=$table_name_tags.tag_id where $table_name_tags.$table_key_name=" . $data[$k][$table_key_name] . " order by $table_name_tags.id asc"));
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_flags_playlists', mr2array(sql("select * from $config[tables_prefix]flags where group_id=5")));
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['users']['playlist_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['playlist_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_playlists_list']);
}

$smarty->display("layout.tpl");
