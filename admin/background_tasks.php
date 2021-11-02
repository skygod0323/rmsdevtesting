<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_servers.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_status_values = array(
	0 => $lang['settings']['background_task_field_status_scheduled'],
	1 => $lang['settings']['background_task_field_status_in_process'],
	2 => $lang['settings']['background_task_field_status_error'],
);

$list_type_values = array(
	1  => $lang['settings']['common_background_task_type_new_video'],
	2  => $lang['settings']['common_background_task_type_delete_video'],
	3  => $lang['settings']['common_background_task_type_upload_video_format_file'],
	4  => $lang['settings']['common_background_task_type_create_video_format_file'],
	5  => $lang['settings']['common_background_task_type_delete_video_format_file'],
	6  => $lang['settings']['common_background_task_type_delete_video_format'],
	24 => $lang['settings']['common_background_task_type_create_overview_screenshots'],
	28 => $lang['settings']['common_background_task_type_delete_overview_screenshots'],
	8  => $lang['settings']['common_background_task_type_create_timeline_screenshots'],
	20 => $lang['settings']['common_background_task_type_delete_timeline_screenshots'],
	7  => $lang['settings']['common_background_task_type_create_screenshot_format'],
	9  => $lang['settings']['common_background_task_type_delete_screenshot_format'],
	16 => $lang['settings']['common_background_task_type_create_screenshots_zip'],
	17 => $lang['settings']['common_background_task_type_delete_screenshots_zip'],
	29 => $lang['settings']['common_background_task_type_recreate_screenshot_formats'],
	10 => $lang['settings']['common_background_task_type_new_album'],
	11 => $lang['settings']['common_background_task_type_delete_album'],
	12 => $lang['settings']['common_background_task_type_create_album_format'],
	13 => $lang['settings']['common_background_task_type_delete_album_format'],
	14 => $lang['settings']['common_background_task_type_upload_album_images'],
	18 => $lang['settings']['common_background_task_type_create_images_zip'],
	19 => $lang['settings']['common_background_task_type_delete_images_zip'],
	22 => $lang['settings']['common_background_task_type_album_images_manipulation'],
	30 => $lang['settings']['common_background_task_type_recreate_album_formats'],
	15 => $lang['settings']['common_background_task_type_change_storage_group_video'],
	23 => $lang['settings']['common_background_task_type_change_storage_group_album'],
	27 => $lang['settings']['common_background_task_type_sync_storage_server'],
	50 => $lang['settings']['common_background_task_type_videos_import'],
	51 => $lang['settings']['common_background_task_type_albums_import'],
	52 => $lang['settings']['common_background_task_type_videos_mass_edit'],
	53 => $lang['settings']['common_background_task_type_albums_mass_edit'],
);

$list_error_code_values = array(
	1 => $lang['settings']['common_background_task_error_codes']['1'],
	2 => $lang['settings']['common_background_task_error_codes']['2'],
	3 => $lang['settings']['common_background_task_error_codes']['3'],
	4 => $lang['settings']['common_background_task_error_codes']['4'],
	5 => $lang['settings']['common_background_task_error_codes']['5'],
	6 => $lang['settings']['common_background_task_error_codes']['6'],
	7 => $lang['settings']['common_background_task_error_codes']['7'],
	8 => $lang['settings']['common_background_task_error_codes']['8'],
	9 => $lang['settings']['common_background_task_error_codes']['9'],
);

$table_fields = array();
$table_fields[] = array('id' => 'task_id',    'title' => $lang['settings']['background_task_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'status_id',  'title' => $lang['settings']['background_task_field_status'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(1 => 'pc_complete', 2 => 'error_code'), 'is_nowrap' => 1, 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'error_code', 'title' => $lang['settings']['background_task_field_error_code'], 'is_default' => 1, 'type' => 'choice', 'values' => $list_error_code_values, 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'message',    'title' => $lang['settings']['background_task_field_message'],    'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'type_id',    'title' => $lang['settings']['background_task_field_type'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values, 'append' => array(3 => 'format_postfix', 4 => 'format_postfix', 5 => 'format_postfix', 6 => 'format_postfix', 7 => 'format_size', 8 => 'format_postfix', 9 => 'format_size', 12 => 'format_size', 13 => 'format_size', 16 => 'format_size', 17 => 'format_size', 18 => 'format_size', 19 => 'format_size', 20 => 'format_postfix'));
$table_fields[] = array('id' => 'server',     'title' => $lang['settings']['background_task_field_server'],     'is_default' => 1, 'type' => 'refid', 'link' => $config['installation_type'] >= 3 ? 'servers_conversion.php?action=change&item_id=%id%' : 'servers_conversion_basic.php', 'link_id' => 'server_id', 'permission' => 'system|servers');
$table_fields[] = array('id' => 'object',     'title' => $lang['settings']['background_task_field_object'],     'is_default' => 1, 'type' => 'object');
$table_fields[] = array('id' => 'priority',   'title' => $lang['settings']['background_task_field_priority'],   'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'added_date', 'title' => $lang['settings']['background_task_field_added_date'], 'is_default' => 1, 'type' => 'datetime');
$table_fields[] = array('id' => 'start_date', 'title' => $lang['settings']['background_task_field_start_date'], 'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "task_id";
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

$table_name = "$config[tables_prefix]background_tasks";
$table_key_name = "task_id";

$table_selector = "$table_name.*, $config[tables_prefix]admin_conversion_servers.title as server, case when video_id>0 then video_id when album_id>0 then album_id end as object_id, case when video_id>0 then video_id when album_id>0 then album_id end as object, case when video_id>0 then 1 when album_id>0 then 2 end as object_type_id";
$table_projector = "$table_name left join $config[tables_prefix]admin_conversion_servers on $table_name.server_id=$config[tables_prefix]admin_conversion_servers.server_id";

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
	$_SESSION['save'][$page_name]['se_status_id'] = "";
	$_SESSION['save'][$page_name]['se_type_id'] = "";
	$_SESSION['save'][$page_name]['se_error_code'] = "";
	$_SESSION['save'][$page_name]['se_server_id'] = "";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_type_id']))
	{
		$_SESSION['save'][$page_name]['se_type_id'] = intval($_GET['se_type_id']);
	}
	if (isset($_GET['se_error_code']))
	{
		$_SESSION['save'][$page_name]['se_error_code'] = intval($_GET['se_error_code']);
	}
	if (isset($_GET['se_server_id']))
	{
		$_SESSION['save'][$page_name]['se_server_id'] = intval($_GET['se_server_id']);
	}
}

$table_filtered = 0;
$where = '';

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
}

if ($_SESSION['save'][$page_name]['se_type_id'] > 0)
{
	$where .= " and $table_name.type_id=" . intval($_SESSION['save'][$page_name]['se_type_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_error_code'] > 0)
{
	$where .= " and $table_name.error_code=" . intval($_SESSION['save'][$page_name]['se_error_code']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_server_id'] > 0)
{
	$where .= " and $table_name.server_id=" . intval($_SESSION['save'][$page_name]['se_server_id']);
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'object')
{
	$sort_by = 'video_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', album_id';
} elseif ($sort_by == 'server')
{
	$sort_by = "$config[tables_prefix]admin_conversion_servers.title";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// additional actions
// =====================================================================================================================

if ($_REQUEST['action'] == 'conversion_log' && intval($_REQUEST['item_id']) > 0)
{
	header("Content-Type: text/plain; charset=utf8");

	$task_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_REQUEST['item_id'])));
	if ($task_data['server_id'] > 0)
	{
		$server_data = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_conversion_servers where server_id=?", $task_data['server_id']));
		$rnd = mt_rand(10000000, 99999999);
		if (mkdir_recursive("$config[temporary_path]/$rnd"))
		{
			get_file("log.txt", "$task_data[task_id]", "$config[temporary_path]/$rnd", $server_data);
			if (is_file("$config[temporary_path]/$rnd/log.txt"))
			{
				@readfile("$config[temporary_path]/$rnd/log.txt");
			} else
			{
				echo $lang['settings']['dg_background_tasks_action_view_log_conversion_none'];
			}
		} else
		{
			echo "ERROR: Failed to create directory $config[temporary_path]/$rnd";
		}
	}
	die;
}

if ($_REQUEST['action'] == 'task_log' && intval($_REQUEST['item_id']) > 0)
{
	header("Content-Type: text/plain; charset=utf8");

	$item_id = intval($_REQUEST['item_id']);
	$dir_path = get_dir_by_id($item_id);
	if (is_file("$config[project_path]/admin/logs/tasks/$dir_path.tar.gz"))
	{
		unset($list);
		exec("tar --list --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz", $list);
		$list = array_flip($list);
		if (isset($list["$item_id.txt"]))
		{
			unset($temp);
			exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz $item_id.txt", $temp);
			echo "-------------------------------------- {$item_id}.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";

			for ($k = 1; $k < 10000; $k++)
			{
				if (isset($list["{$item_id}_$k.txt"]))
				{
					unset($temp);
					exec("tar --extract --to-stdout --file=$config[project_path]/admin/logs/tasks/$dir_path.tar.gz {$item_id}_$k.txt", $temp);
					echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(implode("\n", $temp)) . "\n\n";
				} else
				{
					break;
				}
			}
		}
	}

	if (is_file("$config[project_path]/admin/logs/tasks/$item_id.txt"))
	{
		echo "-------------------------------------- {$item_id}.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/tasks/$item_id.txt")) . "\n\n";

		for ($k = 1; $k < 10000; $k++)
		{
			if (is_file("$config[project_path]/admin/logs/tasks/{$item_id}_$k.txt"))
			{
				echo "-------------------------------------- {$item_id}_$k.txt\n\n" . trim(file_get_contents("$config[project_path]/admin/logs/tasks/{$item_id}_$k.txt")) . "\n\n";
			} else
			{
				break;
			}
		}
	}
	die;
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && (count($_REQUEST['row_select']) > 0 || $_REQUEST['batch_action'] == 'restart_all' || $_REQUEST['batch_action'] == 'delete_all' || $_REQUEST['batch_action'] == 'delete_failed'))
{
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete' || $_REQUEST['batch_action'] == 'delete_all' || $_REQUEST['batch_action'] == 'delete_failed')
	{
		if ($_REQUEST['batch_action'] == 'delete')
		{
			$tasks = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
		} elseif ($_REQUEST['batch_action'] == 'delete_all')
		{
			$tasks = mr2array(sql("select * from $table_name"));
		} else
		{
			$tasks = mr2array(sql("select * from $table_name where status_id in (2)"));
		}

		foreach ($tasks as $task)
		{
			$task_data = @unserialize($task['data']);

			$task_type = $list_type_values[$task['type_id']];
			file_put_contents("$config[project_path]/admin/logs/tasks/$task[task_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Task was deleted manually ($task_type)\n", FILE_APPEND | LOCK_EX);
			if ($task['video_id'] > 0)
			{
				file_put_contents("$config[project_path]/admin/logs/videos/$task[video_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Video task was deleted manually ($task_type)\n", FILE_APPEND | LOCK_EX);
			}
			if ($task['album_id'] > 0)
			{
				file_put_contents("$config[project_path]/admin/logs/albums/$task[album_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Album task was deleted manually ($task_type)\n", FILE_APPEND | LOCK_EX);
			}

			switch ($task['type_id'])
			{
				case 1:
					sql_update("update $config[tables_prefix]videos set status_id=2 where status_id=3 and video_id=?", $task['video_id']);
					break;
				case 2:
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks_history where video_id=? and type_id=1 and status_id=3", $task['video_id'])) > 0)
					{
						sql_update("update $config[tables_prefix]videos set status_id=0 where status_id=4 and video_id=?", $task['video_id']);
					} else
					{
						sql_update("update $config[tables_prefix]videos set status_id=2 where status_id=4 and video_id=?", $task['video_id']);
					}
					break;
				case 3:
					$dir_path = get_dir_by_id($task['video_id']);
					$postfix = $task_data['format_postfix'];
					if (is_file("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id]$postfix"))
					{
						if (!unlink("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id]$postfix"))
						{
							file_put_contents("$config[project_path]/admin/logs/videos/$task[video_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "WARNING  Failed to delete video file: $config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id]$postfix\n", FILE_APPEND | LOCK_EX);
						}
					}
					break;
				case 4:
					$dir_path = get_dir_by_id($task['video_id']);
					if (is_file("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp2"))
					{
						if (!unlink("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp2"))
						{
							file_put_contents("$config[project_path]/admin/logs/videos/$task[video_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "WARNING  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp2\n", FILE_APPEND | LOCK_EX);
						}
					}
					break;
				case 6:
					sql_update("update $config[tables_prefix]formats_videos set status_id=4 where status_id=3 and postfix=?", $task_data['format_postfix']);
					break;
				case 7:
					sql_update("update $config[tables_prefix]formats_screenshots set status_id=2 where status_id=0 and format_screenshot_id=?", $task_data['format_id']);
					break;
				case 9:
					sql_update("update $config[tables_prefix]formats_screenshots set status_id=4 where status_id=3 and format_screenshot_id=?", $task_data['format_id']);
					break;
				case 10:
					sql_update("update $config[tables_prefix]albums set status_id=2 where status_id=3 and album_id=?", $task['album_id']);
					break;
				case 11:
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks_history where album_id=? and type_id=10 and status_id=3", $task['album_id'])) > 0)
					{
						sql_update("update $config[tables_prefix]albums set status_id=0 where status_id=4 and album_id=?", $task['album_id']);
					} else
					{
						sql_update("update $config[tables_prefix]albums set status_id=2 where status_id=4 and album_id=?", $task['album_id']);
					}
					break;
				case 12:
					sql_update("update $config[tables_prefix]formats_albums set status_id=2 where status_id=0 and format_album_id=?", $task_data['format_id']);
					break;
				case 13:
					sql_update("update $config[tables_prefix]formats_albums set status_id=4 where status_id=3 and format_album_id=?", $task_data['format_id']);
					break;
				case 16:
					sql_update("update $config[tables_prefix]formats_screenshots set is_create_zip=0 where format_screenshot_id=?", $task_data['format_id']);
					break;
				case 17:
					sql_update("update $config[tables_prefix]formats_screenshots set is_create_zip=1 where format_screenshot_id=?", $task_data['format_id']);
					break;
				case 18:
					if ($task_data['format_id'] == 'source')
					{
						sql_update("update $config[tables_prefix]options set value=0 where variable='ALBUMS_SOURCE_FILES_CREATE_ZIP'");
					} else
					{
						sql_update("update $config[tables_prefix]formats_albums set is_create_zip=0 where format_album_id=?", $task_data['format_id']);
					}
					break;
				case 24:
					$dir_path = get_dir_by_id($task['video_id']);
					if (is_file("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp3"))
					{
						if (!unlink("$config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp3"))
						{
							file_put_contents("$config[project_path]/admin/logs/videos/$task[video_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "WARNING  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$task[video_id]/$task[video_id].tmp3\n", FILE_APPEND | LOCK_EX);
						}
					}
					break;
			}

			$history_status_id = 4;
			if ($task['status_id'] == 2)
			{
				$history_status_id = 2;
			}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks_history where task_id=?", $task['task_id'])) == 0)
			{
				sql_pr("insert into $config[tables_prefix]background_tasks_history set task_id=?, status_id=?, type_id=?, video_id=?, album_id=?, data=?, message=?, error_code=?, start_date=?, end_date=?", $task['task_id'], $history_status_id, $task['type_id'], intval($task['video_id']), intval($task['album_id']), trim($task['data']), trim($task['message']), intval($task['error_code']), $task['start_date'], $task['start_date']);
			}
			if ($task['server_id'] > 0)
			{
				sql_pr("insert into $config[tables_prefix]background_tasks_postponed set type_id=5, data=?, added_date=?, due_date=?", serialize(array('task_id' => $task['task_id'], 'server_id' => $task['server_id'])), date("Y-m-d H:i:s"), date("Y-m-d H:i:s"));
			}
			@unlink("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat");
			@unlink("$config[project_path]/admin/data/engine/tasks/$task[task_id]_duration.dat");
		}

		if ($_REQUEST['batch_action'] == 'delete')
		{
			sql("delete from $table_name where $table_key_name in ($row_select)");
		} elseif ($_REQUEST['batch_action'] == 'delete_all')
		{
			sql("delete from $table_name");
		} elseif ($_REQUEST['batch_action'] == 'delete_failed')
		{
			sql("delete from $table_name where status_id in (2)");
		}

		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action'] == 'restart' || $_REQUEST['batch_action'] == 'restart_all')
	{
		if ($_REQUEST['batch_action'] == 'restart_all')
		{
			$tasks = mr2array(sql("select * from $table_name where status_id=2"));
		} else
		{
			$tasks = mr2array(sql("select * from $table_name where status_id=2 and $table_key_name in ($row_select)"));
		}
		foreach ($tasks as $task)
		{
			if ($task['video_id'] > 0)
			{
				sql("update $config[tables_prefix]videos set status_id=3 where status_id=2 and video_id=$task[video_id]");

				file_put_contents("$config[project_path]/admin/logs/videos/$task[video_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
			}
			if ($task['album_id'] > 0)
			{
				sql("delete from $config[tables_prefix]albums_images where album_id in (select album_id from $config[tables_prefix]albums where status_id=2 and album_id=$task[album_id])");
				sql("update $config[tables_prefix]albums set status_id=3 where status_id=2 and album_id=$task[album_id]");

				file_put_contents("$config[project_path]/admin/logs/albums/$task[album_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n", FILE_APPEND | LOCK_EX);
			}

			file_put_contents("$config[project_path]/admin/logs/tasks/$task[task_id].txt", "\n" . date("[Y-m-d H:i:s] ") . "INFO  Restarted task manually\n\n", FILE_APPEND | LOCK_EX);
		}
		if ($_REQUEST['batch_action'] == 'restart_all')
		{
			sql("update $table_name set status_id=0, server_id=0, message='' where status_id=2");
		} else
		{
			sql("update $table_name set status_id=0, server_id=0, message='' where status_id=2 and $table_key_name in ($row_select)");
		}
		$_SESSION['messages'][] = $lang['settings']['success_message_background_task_restarted'];
	} elseif ($_REQUEST['batch_action'] == 'inc_priority')
	{
		sql("update $table_name set priority=priority+10 where $table_key_name in ($row_select)");
	}
	return_ajax_success($page_name);
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
		if ($v['error_code'] == 0)
		{
			$data[$k]['error_code'] = '';
		}
		$data[$k]['is_editing_forbidden'] = 1;
		if ($v['status_id'] == 2)
		{
			$data[$k]['is_error'] = 1;
		}

		$task_data = unserialize($v['data']);
		if (is_array($task_data) && $task_data['format_postfix'] <> '')
		{
			$data[$k]['format_postfix'] = $task_data['format_postfix'];
		}
		if (is_array($task_data) && $task_data['format_size'] <> '')
		{
			$data[$k]['format_size'] = $task_data['format_size'];
		}
		if (is_file("$config[project_path]/admin/data/engine/tasks/$v[task_id].dat"))
		{
			$data[$k]['pc_complete'] = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/$v[task_id].dat")) . "%";
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');

$smarty->assign('list_status_values', $list_status_values);
$smarty->assign('list_type_values', $list_type_values);
$smarty->assign('list_error_code_values', $list_error_code_values);

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['settings']['submenu_option_background_tasks']);

$smarty->display("layout.tpl");
