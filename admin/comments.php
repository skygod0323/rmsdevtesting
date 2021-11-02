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

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
$patterns = array(
	1 => $website_ui_data['WEBSITE_LINK_PATTERN'],
	2 => $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM'],
	3 => $website_ui_data['WEBSITE_LINK_PATTERN_CS'],
	4 => $website_ui_data['WEBSITE_LINK_PATTERN_MODEL'],
	5 => $website_ui_data['WEBSITE_LINK_PATTERN_DVD'],
	13 => $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']
);

$post_types = mr2array(sql("select * from $config[tables_prefix]posts_types"));
foreach ($post_types as $post_type)
{
	$patterns["12/$post_type[post_type_id]"] = $post_type['url_pattern'];
}


$table_fields = array();
$table_fields[] = array('id' => 'comment_id',   'title' => $lang['users']['comment_field_id'],           'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'comment',      'title' => $lang['users']['comment_field_comment'],      'is_default' => 1, 'type' => 'longtext', 'ifdisable' => 'is_deleted');
$table_fields[] = array('id' => 'comment_full', 'title' => $lang['users']['comment_field_comment_full'], 'is_default' => 0, 'type' => 'longtext', 'ifdisable' => 'is_deleted', 'no_truncate' => 1);
$table_fields[] = array('id' => 'object',       'title' => $lang['users']['comment_field_object'],       'is_default' => 1, 'type' => 'object');
$table_fields[] = array('id' => 'user',         'title' => $lang['users']['comment_field_user'],         'is_default' => 1, 'type' => 'user');
if ($config['safe_mode'] != 'true')
{
	$table_fields[] = array('id' => 'ip',       'title' => $lang['users']['comment_field_ip'],           'is_default' => 1, 'type' => 'ip');
}
$table_fields[] = array('id' => 'country',      'title' => $lang['users']['comment_field_country'],      'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'rating',       'title' => $lang['users']['comment_field_rating'],       'is_default' => 0, 'type' => 'number');
$table_fields[] = array('id' => 'is_approved',  'title' => $lang['users']['comment_field_approved'],     'is_default' => 0, 'type' => 'bool');
$table_fields[] = array('id' => 'added_date',   'title' => $lang['users']['comment_field_added_date'],   'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "comment_id";
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
$search_fields[] = array('id' => 'comment_id', 'title' => $lang['users']['comment_field_id']);
$search_fields[] = array('id' => 'comment',    'title' => $lang['users']['comment_field_comment']);
$search_fields[] = array('id' => 'user',       'title' => $lang['users']['comment_field_user']);
$search_fields[] = array('id' => 'ip',         'title' => $lang['users']['comment_field_ip']);

$language_code = $lang['system']['language_code'];

$table_name = "$config[tables_prefix]comments";
$table_key_name = "comment_id";
$table_selector = "$table_name.*, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, countries.country,
						case $table_name.object_type_id when 1 then $config[tables_prefix]videos.title when 2 then $config[tables_prefix]albums.title when 3 then $config[tables_prefix]content_sources.title when 4 then $config[tables_prefix]models.title when 5 then $config[tables_prefix]dvds.title when 12 then $config[tables_prefix]posts.title when 13 then $config[tables_prefix]playlists.title end as object,
						case $table_name.object_type_id when 1 then $config[tables_prefix]videos.dir when 2 then $config[tables_prefix]albums.dir when 3 then $config[tables_prefix]content_sources.dir when 4 then $config[tables_prefix]models.dir when 5 then $config[tables_prefix]dvds.dir when 12 then $config[tables_prefix]posts.dir when 13 then $config[tables_prefix]playlists.dir end as object_dir,
						case $table_name.object_type_id when 1 then 0 when 2 then 0 when 3 then 0 when 4 then 0 when 5 then 0 when 12 then $config[tables_prefix]posts.post_type_id when 13 then 0 end as post_type_id
";
$table_projector = "$table_name left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id
						left join $config[tables_prefix]videos on $config[tables_prefix]videos.video_id=$table_name.object_id
						left join $config[tables_prefix]albums on $config[tables_prefix]albums.album_id=$table_name.object_id
						left join $config[tables_prefix]content_sources on $config[tables_prefix]content_sources.content_source_id=$table_name.object_id
						left join $config[tables_prefix]models on $config[tables_prefix]models.model_id=$table_name.object_id
						left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.object_id
						left join $config[tables_prefix]posts on $config[tables_prefix]posts.post_id=$table_name.object_id
						left join $config[tables_prefix]playlists on $config[tables_prefix]playlists.playlist_id=$table_name.object_id
						left join (select country_code, title as country from $config[tables_prefix]list_countries where language_code='$language_code') countries on $table_name.country_code=countries.country_code
";

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
	$_SESSION['save'][$page_name]['se_object_type_id'] = '';
	$_SESSION['save'][$page_name]['se_object_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = intval($_GET['se_status_id']);
	}
	if (isset($_GET['se_object_type_id']))
	{
		$_SESSION['save'][$page_name]['se_object_type_id'] = intval($_GET['se_object_type_id']);
	}
	if (isset($_GET['se_object_id']))
	{
		$_SESSION['save'][$page_name]['se_object_id'] = trim($_GET['se_object_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
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
			} elseif ($search_field['id'] == 'ip')
			{
				$q_ip = ip2int($q);
				if ($q_ip > 0)
				{
					$where_search .= " or $table_name.ip='$q_ip'";
				}
			} elseif ($search_field['id'] == 'user')
			{
				$where_search .= " or $table_name.anonymous_username like '%$q%' or $config[tables_prefix]users.username like '%$q%'";
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

if ($_SESSION['save'][$page_name]['se_status_id'] == 1)
{
	$where .= " and $table_name.is_review_needed=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == 2)
{
	$where .= " and $table_name.is_approved=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == 3)
{
	$where .= " and $table_name.is_approved=0";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_object_type_id'] != '')
{
	$where .= " and $table_name.object_type_id=" . intval($_SESSION['save'][$page_name]['se_object_type_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_object_id'] != '')
{
	$where .= " and $table_name.object_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.anonymous_username";
} elseif ($sort_by == 'object')
{
	$sort_by = 'object_type_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', object_id';
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

	$added_time = array('00', '00');
	if (strpos($_POST['added_time'], ":") !== false)
	{
		$temp = explode(":", $_POST['added_time']);
		if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
		{
			$post_time[0] = $temp[0];
		}
		if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
		{
			$post_time[1] = $temp[1];
		}
	}

	if ($_POST['action'] == 'add_new_complete')
	{
		$_POST['added_date'] = date("Y-m-d H:i:s", strtotime(intval($_POST['added_date_Year']) . "-" . intval($_POST['added_date_Month']) . "-" . intval($_POST['added_date_Day']) . " " . intval($post_time[0]) . ":" . intval($post_time[1])));

		if (intval($_POST['user_type']) == 1)
		{
			if (validate_field('empty', $_POST['user'], $lang['users']['comment_field_user']))
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user'])) == 0)
				{
					$errors[] = get_aa_error('invalid_user', $lang['users']['comment_field_user']);
				}
			}
		} else
		{
			validate_field('empty', $_POST['anonymous_username'], $lang['users']['comment_field_user']);
		}
		validate_field('date', 'added_date_', $lang['users']['comment_field_post_date']);
	}
	validate_field('empty', $_POST['comment'], $lang['users']['comment_field_comment']);

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			if (intval($_POST['user_type']) == 1)
			{
				$_POST['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=? limit 1", $_POST['user']));
			} else
			{
				$_POST['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
			}
			$comment_id = sql_insert("insert into $table_name set object_id=?, object_type_id=?, user_id=?, anonymous_username=?, is_approved=1, comment=?, ip=?, country_code=lower(?), added_date=?",
				intval($_POST['object_id']), intval($_POST['object_type_id']), intval($_POST['user_id']), nvl($_POST['anonymous_username']), $_POST['comment'], ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), $_POST['added_date']
			);

			$tokens_granted = 0;
			if (intval($_POST['user_type']) == 1 && $comment_id > 0)
			{
				$memberzone_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if (intval($_POST['object_type_id']) == 1)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=4, user_id=?, video_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_VIDEO_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_VIDEO']);
					}
				}
				if (intval($_POST['object_type_id']) == 2)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=5, user_id=?, album_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_ALBUM_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_ALBUM']);
					}
				}
				if (intval($_POST['object_type_id']) == 3)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=15, user_id=?, content_source_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_CS_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_CS']);
					}
				}
				if (intval($_POST['object_type_id']) == 4)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=14, user_id=?, model_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_MODEL_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_MODEL']);
					}
				}
				if (intval($_POST['object_type_id']) == 5)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=16, user_id=?, dvd_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_DVD_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_DVD']);
					}
				}
				if (intval($_POST['object_type_id']) == 12)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=21, user_id=?, post_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_POST_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_POST']);
					}
				}
				if (intval($_POST['object_type_id']) == 13)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=20, user_id=?, playlist_id=?, comment_id=?, added_date=?", intval($_POST['user_id']), intval($_POST['object_id']), $comment_id, $_POST['added_date']);
					if (strlen($_POST['comment']) >= intval($memberzone_data['AWARDS_COMMENT_PLAYLIST_CONDITION']))
					{
						$tokens_granted = intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']);
					}
				}
				if ($tokens_granted > 0)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", intval($_POST['user_id']), $comment_id, $tokens_granted, date("Y-m-d H:i:s"));
				}
			}

			sql_pr("update $config[tables_prefix]users set
					tokens_available=tokens_available+$tokens_granted,
					comments_videos_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=1),
					comments_albums_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=2),
					comments_cs_count       =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=3),
					comments_models_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=4),
					comments_dvds_count     =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=5),
					comments_posts_count    =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
					comments_playlists_count=(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
					comments_total_count    =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1)
				where user_id=?
			", intval($_POST['user_id']));

			sql_pr("update $config[tables_prefix]videos set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]videos.video_id and object_type_id=1 and is_approved=1) where video_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]albums set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]albums.album_id and object_type_id=2 and is_approved=1) where album_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]content_sources set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]content_sources.content_source_id and object_type_id=3 and is_approved=1) where content_source_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]models set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]models.model_id and object_type_id=4 and is_approved=1) where model_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]dvds set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]dvds.dvd_id and object_type_id=5 and is_approved=1) where dvd_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]posts set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]posts.post_id and object_type_id=12 and is_approved=1) where post_id=?", intval($_POST['object_id']));
			sql_pr("update $config[tables_prefix]playlists set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]playlists.playlist_id and object_type_id=13 and is_approved=1) where playlist_id=?", intval($_POST['object_id']));

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=100, object_id=?, object_type_id=15, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $comment_id, date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_POST['item_id'])));

			sql_pr("update $table_name set comment=? where $table_key_name=?", $_POST['comment'], intval($_POST['item_id']));

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
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=15, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], intval($_POST['item_id']), $update_details, date("Y-m-d H:i:s"));
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

	$list_ids_users = mr2array_list(sql("select distinct user_id from (select distinct user_id from $table_name where $table_key_name in ($row_select) union all select distinct user_id from $table_name where is_review_needed=1) X"));
	$list_ids_objects = mr2array_list(sql("select distinct object_id from (select distinct object_id from $table_name where $table_key_name in ($row_select) union all select distinct object_id from $table_name where is_review_needed=1) X"));

	$approved_comment_ids = array();
	$deleted_comment_ids = array();
	if ($_REQUEST['batch_action'] == 'delete')
	{
		$deleted_comment_ids = $_REQUEST['row_select'];
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]users_events where comment_id in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	if ($_REQUEST['batch_action'] == 'approve')
	{
		$approved_comment_ids = mr2array_list(sql("select $table_key_name from $table_name where is_approved=0 and $table_key_name in ($row_select)"));
		sql("update $table_name set is_approved=1, is_review_needed=0 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['users']['comment_success_message_approved'];
	}
	if ($_REQUEST['batch_action'] == 'approve_and_delete')
	{
		$approved_comment_ids = mr2array_list(sql("select $table_key_name from $table_name where is_approved=0 and $table_key_name in ($row_select)"));
		sql("update $table_name set is_approved=1, is_review_needed=0 where $table_key_name in ($row_select)");

		$ids_to_delete = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
		if (count($ids_to_delete) > 0)
		{
			$ids_to_delete = implode(",", array_map("intval", $ids_to_delete));
			$ids_to_delete = mr2array_list(sql("select $table_key_name from $table_name where is_review_needed=1 and $table_key_name in ($ids_to_delete)"));
			if (count($ids_to_delete) > 0)
			{
				$deleted_comment_ids = $ids_to_delete;
				$ids_to_delete = implode(",", array_map("intval", $ids_to_delete));
				sql("delete from $config[tables_prefix]users_events where comment_id in ($ids_to_delete)");
				sql("delete from $table_name where comment_id in ($ids_to_delete)");
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_completed'];
	}
	if ($_REQUEST['batch_action'] == 'delete_and_approve')
	{
		$deleted_comment_ids = $_REQUEST['row_select'];
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]users_events where comment_id in ($row_select)");

		$ids_to_approve = array_diff($_REQUEST['row_all'], $_REQUEST['row_select']);
		if (count($ids_to_approve) > 0)
		{
			$ids_to_approve = implode(",", array_map("intval", $ids_to_approve));
			$approved_comment_ids = mr2array_list(sql("select $table_key_name from $table_name where is_approved=0 and comment_id in ($ids_to_approve)"));
			sql("update $table_name set is_approved=1, is_review_needed=0 where comment_id in ($ids_to_approve)");
		}
		$_SESSION['messages'][] = $lang['common']['success_message_completed'];
	}

	$list_ids_users = implode(",", array_map("intval", $list_ids_users));
	sql("update $config[tables_prefix]users set
			comments_videos_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=1),
			comments_albums_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=2),
			comments_cs_count       =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=3),
			comments_models_count   =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=4),
			comments_dvds_count     =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=5),
			comments_posts_count    =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
			comments_playlists_count=(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
			comments_total_count    =(select count(*) from $table_name where user_id=$config[tables_prefix]users.user_id and is_approved=1)
		where user_id in ($list_ids_users)
	");

	$list_ids_objects = implode(",", array_map("intval", $list_ids_objects));
	sql("update $config[tables_prefix]videos set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]videos.video_id and object_type_id=1 and is_approved=1) where video_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]albums set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]albums.album_id and object_type_id=2 and is_approved=1) where album_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]content_sources set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]content_sources.content_source_id and object_type_id=3 and is_approved=1) where content_source_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]models set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]models.model_id and object_type_id=4 and is_approved=1) where model_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]dvds set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]dvds.dvd_id and object_type_id=5 and is_approved=1) where dvd_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]posts set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]posts.post_id and object_type_id=12 and is_approved=1) where post_id in ($list_ids_objects)");
	sql("update $config[tables_prefix]playlists set comments_count=(select count(*) from $table_name where object_id=$config[tables_prefix]playlists.playlist_id and object_type_id=13 and is_approved=1) where playlist_id in ($list_ids_objects)");

	if (count($approved_comment_ids) > 0)
	{
		foreach ($approved_comment_ids as $approved_comment_id)
		{
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=150, object_id=?, object_type_id=15, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $approved_comment_id, 'is_approved', date("Y-m-d H:i:s"));
		}

		$memberzone_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
		if (intval($memberzone_data['AWARDS_COMMENT_VIDEO']) > 0 || intval($memberzone_data['AWARDS_COMMENT_ALBUM']) > 0 || intval($memberzone_data['AWARDS_COMMENT_CS']) > 0 || intval($memberzone_data['AWARDS_COMMENT_MODEL']) > 0 || intval($memberzone_data['AWARDS_COMMENT_DVD']) > 0 || intval($memberzone_data['AWARDS_COMMENT_POST']) > 0 || intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']) > 0)
		{
			$anonymous_user_id = mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
			$approved_comment_ids = implode(",", $approved_comment_ids);
			$approved_comments = mr2array(sql("select comment_id, object_type_id, user_id, comment from $table_name where $table_key_name in ($approved_comment_ids)"));
			foreach ($approved_comments as $comment)
			{
				if (intval($memberzone_data['AWARDS_COMMENT_VIDEO']) > 0 && $comment['object_type_id'] == 1 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_VIDEO_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_VIDEO']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_VIDEO']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_ALBUM']) > 0 && $comment['object_type_id'] == 2 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_ALBUM_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_ALBUM']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_ALBUM']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_CS']) > 0 && $comment['object_type_id'] == 3 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_CS_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_CS']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_CS']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_MODEL']) > 0 && $comment['object_type_id'] == 4 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_MODEL_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_MODEL']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_MODEL']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_DVD']) > 0 && $comment['object_type_id'] == 5 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_DVD_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_DVD']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_DVD']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_POST']) > 0 && $comment['object_type_id'] == 12 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_POST_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_POST']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_POST']), $comment['user_id']);
				}
				if (intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']) > 0 && $comment['object_type_id'] == 13 && strlen($comment['comment']) >= intval($memberzone_data['AWARDS_COMMENT_PLAYLIST_CONDITION']) && $comment['user_id'] <> $anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?", $comment['user_id'], $comment['comment_id'], intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']), date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']), $comment['user_id']);
				}
			}
		}
	}

	if (count($deleted_comment_ids) > 0)
	{
		foreach ($deleted_comment_ids as $deleted_comment_id)
		{
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=15, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $deleted_comment_id, date("Y-m-d H:i:s"));
		}
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

	if ($_POST['user_status_id'] == 4)
	{
		if ($_POST['anonymous_username'] != '')
		{
			$_POST['user'] = $_POST['anonymous_username'];
		}
		$_POST['user'] .= " [A]";
	}
	if ($_POST['object'] == '')
	{
		$_POST['object'] = $_POST['object_id'];
	}

	$_POST['ip'] = int2ip($_POST['ip']);

	if ($_POST['object_dir'] <> '')
	{
		if ($patterns[$_POST['object_type_id']] <> '')
		{
			$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['object_id'], str_replace("%DIR%", $_POST['object_dir'], $patterns[$_POST['object_type_id']]));
		} elseif ($_POST['post_type_id'] <> '' && $patterns[$_POST['object_type_id'] . "/" . $_POST['post_type_id']] <> '')
		{
			$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['object_id'], str_replace("%DIR%", $_POST['object_dir'], $patterns[$_POST['object_type_id'] . "/" . $_POST['post_type_id']]));
		}
	}
}

if ($_GET['action'] == 'add_new' && intval($_GET['object_id']) > 0 && intval($_GET['object_type_id']) > 0)
{
	$_POST['object_id'] = intval($_GET['object_id']);
	$_POST['object_type_id'] = intval($_GET['object_type_id']);
	$_POST['added_date'] = date("Y-m-d H:i");

	if ($_POST['object_type_id'] == 1)
	{
		$result = sql_pr("select title, dir, post_date from $config[tables_prefix]videos where video_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 2)
	{
		$result = sql_pr("select title, dir, post_date from $config[tables_prefix]albums where album_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 3)
	{
		$result = sql_pr("select title, dir from $config[tables_prefix]content_sources where content_source_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 4)
	{
		$result = sql_pr("select title, dir from $config[tables_prefix]models where model_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 5)
	{
		$result = sql_pr("select title, dir from $config[tables_prefix]dvds where dvd_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 12)
	{
		$result = sql_pr("select title, dir, post_date from $config[tables_prefix]posts where post_id=?", $_POST['object_id']);
	} elseif ($_POST['object_type_id'] == 13)
	{
		$result = sql_pr("select title, dir from $config[tables_prefix]playlists where playlist_id=?", $_POST['object_id']);
	}
	if (mr2rows($result) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$object_data = mr2array_single($result);

	$_POST['object'] = $object_data['title'];
	$_POST['object_dir'] = $object_data['dir'];
	if ($_POST['object'] == '')
	{
		$_POST['object'] = $_POST['object_id'];
	}
	$_POST['object_post_date'] = $object_data['post_date'];

	if ($_POST['object_dir'] <> '')
	{
		if ($patterns[$_POST['object_type_id']] <> '')
		{
			$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['object_id'], str_replace("%DIR%", $_POST['object_dir'], $patterns[$_POST['object_type_id']]));
		} elseif ($_POST['post_type_id'] <> '' && $patterns[$_POST['object_type_id'] . "/" . $_POST['post_type_id']] <> '')
		{
			$_POST['website_link'] = "$config[project_url]/" . str_replace("%ID%", $_POST['object_id'], str_replace("%DIR%", $_POST['object_dir'], $patterns[$_POST['object_type_id'] . "/" . $_POST['post_type_id']]));
		}
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
	if ($data[$k]['comment'] == '')
	{
		$data[$k]['comment'] = $lang['users']['comment_field_comment_deleted'];
		$data[$k]['is_deleted'] = 1;
	}
	if ($data[$k]['user_status_id'] == 4)
	{
		$data[$k]['user_id'] = 0;
		if ($data[$k]['anonymous_username'] != '')
		{
			$data[$k]['user'] = $data[$k]['anonymous_username'];
		}
	}
	$data[$k]['comment_full'] = $data[$k]['comment'];
	if ($data[$k]['object_dir'] <> '')
	{
		if ($patterns[$data[$k]['object_type_id']] <> '')
		{
			$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k]['object_id'], str_replace("%DIR%", $data[$k]['object_dir'], $patterns[$data[$k]['object_type_id']]));
		} elseif ($data[$k]['post_type_id'] <> '' && $patterns[$data[$k]['object_type_id'] . "/" . $data[$k]['post_type_id']] <> '')
		{
			$data[$k]['website_link'] = "$config[project_url]/" . str_replace("%ID%", $data[$k]['object_id'], str_replace("%DIR%", $data[$k]['object_dir'], $patterns[$data[$k]['object_type_id'] . "/" . $data[$k]['post_type_id']]));
		}
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
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['comment_id'], $lang['users']['comment_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['comment_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_comments_list']);
}

$smarty->display("layout.tpl");
