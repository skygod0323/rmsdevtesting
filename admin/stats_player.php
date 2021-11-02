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

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if (!in_array($_SESSION['save'][$page_name]['se_group_by'], array('date', 'referer', 'country', 'device', 'embed_profile')))
{
	$_SESSION['save'][$page_name]['se_group_by'] = "date";
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

$list_device_values = array(
		0 => $lang['stats']['common_device_unknown'],
		1 => $lang['stats']['common_device_desktop'],
		2 => $lang['stats']['common_device_phone'],
		3 => $lang['stats']['common_device_tablet'],
);

$list_embed_profile_values = array();
$list_embed_profile_values['default'] = $lang['stats']['player_field_embed_profile_default'];

$embed_folders = get_contents_from_dir("$config[project_path]/admin/data/player/embed", 2);
foreach ($embed_folders as $embed_folder)
{
	if (is_file("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"))
	{
		$embed_profile = @unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/$embed_folder/config.dat"));
		$list_embed_profile_values[$embed_folder] = $embed_profile['embed_profile_name'];
	}
}

$table_fields = array();

if ($list_grouping == "date")
{
	$table_fields[] = array('id' => 'added_date',       'title' => $lang['stats']['player_field_date'],          'is_default' => 1, 'type' => 'date');
} elseif ($list_grouping == "referer")
{
	$table_fields[] = array('id' => 'referer',          'title' => $lang['stats']['player_field_referer'],       'is_default' => 1, 'type' => 'text', 'zero_label' => $lang['stats']['common_referer_other']);
} elseif ($list_grouping == "country")
{
	$table_fields[] = array('id' => 'country',          'title' => $lang['stats']['player_field_country'],       'is_default' => 1, 'type' => 'text', 'zero_label' => '-');
} elseif ($list_grouping == "device")
{
	$table_fields[] = array('id' => 'device',           'title' => $lang['stats']['player_field_device'],        'is_default' => 1, 'type' => 'choice', 'values' => $list_device_values);
} elseif ($list_grouping == "embed_profile")
{
	$table_fields[] = array('id' => 'embed_profile_id', 'title' => $lang['stats']['player_field_embed_profile'], 'is_default' => 1, 'type' => 'choice', 'values' => $list_embed_profile_values);
}

if ($list_grouping == "date" || $list_grouping == "referer" || $list_grouping == "country" || $list_grouping == "device" || $list_grouping == "embed_profile")
{
	$table_fields[] = array('id' => 'player_loads',         'title' => $lang['stats']['player_field_event_player_loaded'],      'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'player_fullscreens',   'title' => $lang['stats']['player_field_event_player_fullscreen'],  'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'player_mutes',         'title' => $lang['stats']['player_field_event_player_muted'],       'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'player_unmutes',       'title' => $lang['stats']['player_field_event_player_unmuted'],     'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'video_starts',         'title' => $lang['stats']['player_field_event_video_started'],      'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'video_pauses',         'title' => $lang['stats']['player_field_event_video_paused'],       'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'video_skips',          'title' => $lang['stats']['player_field_event_video_skipped'],      'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'video_ends',           'title' => $lang['stats']['player_field_event_video_ended'],        'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'video_errors',         'title' => $lang['stats']['player_field_event_video_errors'],       'is_default' => 1, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'start_ad_views',       'title' => $lang['stats']['player_field_event_start_ad_views'],     'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'start_ad_clicks',      'title' => $lang['stats']['player_field_event_start_ad_clicks'],    'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'start_ad_ctr',         'title' => $lang['stats']['player_field_event_start_ad_ctr'],       'is_default' => 0, 'type' => 'double', 'format' => 'percent');
	$table_fields[] = array('id' => 'start_ad_errors',      'title' => $lang['stats']['player_field_event_start_ad_errors'],    'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pre_ad_views',         'title' => $lang['stats']['player_field_event_pre_ad_views'],       'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pre_ad_clicks',        'title' => $lang['stats']['player_field_event_pre_ad_clicks'],      'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pre_ad_ctr',           'title' => $lang['stats']['player_field_event_pre_ad_ctr'],         'is_default' => 0, 'type' => 'double', 'format' => 'percent');
	$table_fields[] = array('id' => 'pre_ad_skips',         'title' => $lang['stats']['player_field_event_pre_ad_skips'],       'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pre_ad_errors',        'title' => $lang['stats']['player_field_event_pre_ad_errors'],      'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'post_ad_views',        'title' => $lang['stats']['player_field_event_post_ad_views'],      'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'post_ad_clicks',       'title' => $lang['stats']['player_field_event_post_ad_clicks'],     'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'post_ad_ctr',          'title' => $lang['stats']['player_field_event_post_ad_ctr'],        'is_default' => 0, 'type' => 'double', 'format' => 'percent');
	$table_fields[] = array('id' => 'post_ad_skips',        'title' => $lang['stats']['player_field_event_post_ad_skips'],      'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'post_ad_errors',       'title' => $lang['stats']['player_field_event_post_ad_errors'],     'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pause_ad_views',       'title' => $lang['stats']['player_field_event_pause_ad_views'],     'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pause_ad_clicks',      'title' => $lang['stats']['player_field_event_pause_ad_clicks'],    'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'pause_ad_ctr',         'title' => $lang['stats']['player_field_event_pause_ad_ctr'],       'is_default' => 0, 'type' => 'double', 'format' => 'percent');
	$table_fields[] = array('id' => 'pause_ad_errors',      'title' => $lang['stats']['player_field_event_pause_ad_errors'],    'is_default' => 0, 'type' => 'traffic', 'ifdisable_zero' => 1);
}

if ($list_grouping == "date")
{
	$sort_def_field = "added_date";
	$sort_def_direction = "desc";
} elseif ($list_grouping == "referer")
{
	$sort_def_field = "referer";
	$sort_def_direction = "asc";
} elseif ($list_grouping == "country")
{
	$sort_def_field = "country";
	$sort_def_direction = "asc";
} elseif ($list_grouping == "device")
{
	$sort_def_field = "device";
	$sort_def_direction = "asc";
} elseif ($list_grouping == "embed_profile")
{
	$sort_def_field = "embed_profile_id";
	$sort_def_direction = "asc";
}

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
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']]);
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
	$_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']))
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
	foreach ($_SESSION['save'][$page_name][$list_grouping]['grid_columns_order'] as $table_field_id)
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
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name][$list_grouping]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix_multi]stats_player";

$temp_stats_columns = ['player_loads', 'player_fullscreens', 'player_mutes', 'player_unmutes', 'video_starts', 'video_pauses', 'video_skips', 'video_ends', 'video_errors', 'start_ad_views', 'start_ad_clicks', 'start_ad_errors', 'pre_ad_views', 'pre_ad_clicks', 'pre_ad_skips', 'pre_ad_errors', 'post_ad_views', 'post_ad_clicks', 'post_ad_skips', 'post_ad_errors', 'pause_ad_views', 'pause_ad_clicks', 'pause_ad_errors'];
$temp_stats_columns_sum_str = '';
foreach ($temp_stats_columns as $temp_stats_column)
{
	$temp_stats_columns_sum_str .= "sum($table_name.$temp_stats_column) as $temp_stats_column, ";
}
$temp_stats_columns_sum_str .= "case when sum($table_name.start_ad_views) > 0 then 100 * (sum($table_name.start_ad_clicks) / sum($table_name.start_ad_views)) else 0 end as start_ad_ctr, ";
$temp_stats_columns_sum_str .= "case when sum($table_name.pre_ad_views) > 0 then 100 * (sum($table_name.pre_ad_clicks) / sum($table_name.pre_ad_views)) else 0 end as pre_ad_ctr, ";
$temp_stats_columns_sum_str .= "case when sum($table_name.post_ad_views) > 0 then 100 * (sum($table_name.post_ad_clicks) / sum($table_name.post_ad_views)) else 0 end as post_ad_ctr, ";
$temp_stats_columns_sum_str .= "case when sum($table_name.pause_ad_views) > 0 then 100 * (sum($table_name.pause_ad_clicks) / sum($table_name.pause_ad_views)) else 0 end as pause_ad_ctr, ";

$temp_stats_columns_sum_str = trim($temp_stats_columns_sum_str, ', ');

if ($list_grouping == "date")
{
	$table_selector = "$table_name.added_date, $temp_stats_columns_sum_str";
	$table_summary_selector = $temp_stats_columns_sum_str;
	$table_summary_field_name = "added_date";
	$table_projector = $table_name;
	$table_group_by = "$table_name.added_date";
} elseif ($list_grouping == "referer")
{
	$table_selector = "max($config[tables_prefix_multi]stats_referers_list.title) as referer, $temp_stats_columns_sum_str";
	$table_summary_selector = $temp_stats_columns_sum_str;
	$table_summary_field_name = "referer";
	$table_projector = "$table_name left join $config[tables_prefix_multi]stats_referers_list on $table_name.referer_id=$config[tables_prefix_multi]stats_referers_list.referer_id";
	$table_group_by = "$table_name.referer_id";
} elseif ($list_grouping == "country")
{
	$table_selector = "max($config[tables_prefix]list_countries.title) as country, $temp_stats_columns_sum_str";
	$table_summary_selector = $temp_stats_columns_sum_str;
	$table_summary_field_name = "country";
	$table_projector = "$table_name left join $config[tables_prefix]list_countries on $table_name.country_code=$config[tables_prefix]list_countries.country_code and $config[tables_prefix]list_countries.language_code='" . sql_escape($lang['system']['language_code']) . "'";
	$table_group_by = "$table_name.country_code";
} elseif ($list_grouping == "device")
{
	$table_selector = "$table_name.device, $temp_stats_columns_sum_str";
	$table_summary_selector = $temp_stats_columns_sum_str;
	$table_summary_field_name = "device";
	$table_projector = $table_name;
	$table_group_by = "$table_name.device";
} elseif ($list_grouping == "embed_profile")
{
	$table_selector = "CASE WHEN $table_name.embed_profile_id='' THEN 'default' ELSE $table_name.embed_profile_id END AS embed_profile_id, $temp_stats_columns_sum_str";
	$table_summary_selector = $temp_stats_columns_sum_str;
	$table_summary_field_name = "embed_profile_id";
	$table_projector = $table_name;
	$table_group_by = "$table_name.embed_profile_id";
}

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name][$list_grouping]['sort_by'] == '')
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name][$list_grouping]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = 'desc';
	}
}
$_SESSION['save'][$page_name]['sort_by'] = $_SESSION['save'][$page_name][$list_grouping]['sort_by'];
$_SESSION['save'][$page_name]['sort_direction'] = $_SESSION['save'][$page_name][$list_grouping]['sort_direction'];

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
	$_SESSION['save'][$page_name][$list_grouping]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name][$list_grouping]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_location'] = '';
	$_SESSION['save'][$page_name]['se_referer'] = '';
	$_SESSION['save'][$page_name]['se_country'] = '';
	$_SESSION['save'][$page_name]['se_device'] = '';
	$_SESSION['save'][$page_name]['se_embed_profile'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_location']))
	{
		$_SESSION['save'][$page_name]['se_location'] = trim($_GET['se_location']);
	}
	if (isset($_GET['se_referer']))
	{
		$_SESSION['save'][$page_name]['se_referer'] = trim($_GET['se_referer']);
	}
	if (isset($_GET['se_country']))
	{
		$_SESSION['save'][$page_name]['se_country'] = trim($_GET['se_country']);
	}
	if (isset($_GET['se_device']))
	{
		$_SESSION['save'][$page_name]['se_device'] = trim($_GET['se_device']);
	}
	if (isset($_GET['se_embed_profile']))
	{
		$_SESSION['save'][$page_name]['se_embed_profile'] = trim($_GET['se_embed_profile']);
	}
	if (isset($_GET['se_period_id']))
	{
		$_SESSION['save'][$page_name]['se_period_id'] = intval($_GET['se_period_id']);
		switch ($_SESSION['save'][$page_name]['se_period_id'])
		{
			case 0:
				$_SESSION['save'][$page_name]['se_date_from'] = '';
				$_SESSION['save'][$page_name]['se_date_to'] = '';
				break;
			case 1:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 6);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 2:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 30);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 3:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 4:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1', strtotime(date('Y-m-1 00:00:00')) - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', strtotime(date('Y-m-1 00:00:00')) - 86400);
				break;
			case 5:
				if (isset($_GET['se_date_from_Day'], $_GET['se_date_from_Month'], $_GET['se_date_from_Year']))
				{
					if (intval($_GET['se_date_from_Day']) > 0 && intval($_GET['se_date_from_Month']) > 0 && intval($_GET['se_date_from_Year']) > 0)
					{
						$_SESSION['save'][$page_name]['se_date_from'] = intval($_GET['se_date_from_Year']) . "-" . intval($_GET['se_date_from_Month']) . "-" . intval($_GET['se_date_from_Day']);
					} else
					{
						$_SESSION['save'][$page_name]['se_date_from'] = "";
					}
				}
				if (isset($_GET['se_date_to_Day'], $_GET['se_date_to_Month'], $_GET['se_date_to_Year']))
				{
					if (intval($_GET['se_date_to_Day']) > 0 && intval($_GET['se_date_to_Month']) > 0 && intval($_GET['se_date_to_Year']) > 0)
					{
						$_SESSION['save'][$page_name]['se_date_to'] = intval($_GET['se_date_to_Year']) . "-" . intval($_GET['se_date_to_Month']) . "-" . intval($_GET['se_date_to_Day']);
					} else
					{
						$_SESSION['save'][$page_name]['se_date_to'] = "";
					}
				}
				break;
		}
	}
}

$table_filtered = 0;
$where = '';

if ($list_grouping == "embed_profile")
{
	$where .= " and $table_name.is_embed=1";
}

if ($_SESSION['save'][$page_name]['se_location'] == 'own')
{
	$where .= " and $table_name.is_embed=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_location'] == 'embed')
{
	$where .= " and $table_name.is_embed=1";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_referer'] != '')
{
	$referer_id = mr2number(sql_pr("select referer_id from $config[tables_prefix_multi]stats_referers_list where title=?", $_SESSION['save'][$page_name]['se_referer']));
	if ($referer_id > 0)
	{
		$where .= " and $table_name.referer_id=$referer_id";
	} else
	{
		$where .= " and 1=0";
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] != "")
{
	$where .= " and $table_name.added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] != "")
{
	$where .= " and $table_name.added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_country'] != '')
{
	$country_code = mr2string(sql_pr("select country_code from $config[tables_prefix]list_countries where title=?", $_SESSION['save'][$page_name]['se_country']));
	if ($country_code)
	{
		$where .= " and $table_name.country_code='" . sql_escape($country_code) . "'";
	} else
	{
		$where .= " and 1=0";
	}
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_device'] != '')
{
	$where .= " and $table_name.device=" . intval($_SESSION['save'][$page_name]['se_device']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_embed_profile'] != '')
{
	$where .= " and $table_name.is_embed=1 and $table_name.embed_profile_id='" . ($_SESSION['save'][$page_name]['se_embed_profile'] == 'default' ? '' : $_SESSION['save'][$page_name]['se_embed_profile']) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'added_date')
{
	$sort_by = "$table_name.$sort_by";
} elseif ($sort_by == 'referer')
{
	$sort_by = "$config[tables_prefix_multi]stats_referers_list.title";
} elseif ($sort_by == 'country')
{
	$sort_by = "$config[tables_prefix]list_countries.title";
} elseif ($sort_by == 'device')
{
	$sort_by = "$table_name.$sort_by";
} elseif ($sort_by == 'embed_profile_id')
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($table_group_by != '')
{
	$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
} else
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
}
if (($_SESSION['save'][$page_name][$list_grouping]['from'] >= $total_num || $_SESSION['save'][$page_name][$list_grouping]['from'] < 0) || ($_SESSION['save'][$page_name][$list_grouping]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name][$list_grouping]['from'] = 0;
}
if ($table_group_by != '')
{
	$data = mr2array(sql("select $table_selector from $table_projector $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name][$list_grouping]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
} else
{
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name][$list_grouping]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
}

if ($list_grouping == 'date')
{
	foreach ($data as $k => $v)
	{
		if ($v['added_date'] == date('Y-m-d'))
		{
			$data[$k]['is_today'] = 1;
			break;
		}
	}
}

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];

	if ($total_num > 1)
	{
		$summary_data = $total[0];
		$summary_count = $total_num;
		if ($list_grouping == "date")
		{
			$where_not_today = "$table_name.added_date!='" . date('Y-m-d') . "'";
			if ($where)
			{
				$where .= " and $where_not_today";
			} else
			{
				$where .= "where $where_not_today";
			}
			$summary_data = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
			$summary_data[$table_summary_field_name] = $lang['common']['total'];

			$summary_count = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
		}

		foreach ($summary_data as $k => $v)
		{
			$total[1][$k] = $v;
			foreach ($table_fields as $table_field)
			{
				if ($table_field['id'] == $k && in_array($table_field['type'], ['number', 'currency', 'duration', 'traffic']))
				{
					if ($summary_count > 0)
					{
						$total[1][$k] /= $summary_count;
					} else
					{
						$total[1][$k] = 0;
					}
				}
			}
		}
		$total[1][$table_summary_field_name] = $lang['common']['average'];
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');
$smarty->assign('list_device_values', $list_device_values);
$smarty->assign('list_embed_profile_values', $list_embed_profile_values);

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('average', $total[1]);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name][$list_grouping]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_player']);

$smarty->display("layout.tpl");
