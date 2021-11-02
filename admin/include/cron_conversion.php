<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'])
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		header('HTTP/1.0 403 Forbidden');
		die('Access denied');
	}
	header('Content-Type: text/plain; charset=utf8');
}

require_once 'setup.php';
require_once 'functions_base.php';
require_once 'functions_servers.php';
require_once 'functions_screenshots.php';
require_once 'functions_admin.php';
require_once 'functions.php';
require_once 'pclzip.lib.php';

if (!is_file("$config[project_path]/admin/data/system/cron_conversion.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_conversion.lock", '1', LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_conversion.lock", 'r+');
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors', 1);
sql_pr('set wait_timeout=86400');

$options = get_options();

$memory_limit = $options['LIMIT_MEMORY'];
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id in (0,1)")) == 0)
{
	die('No tasks to process');
}

file_put_contents("$config[project_path]/admin/logs/cron_conversion.txt", '', LOCK_EX);

log_output('INFO  Tasks processor started');
log_output('INFO  Memory limit: ' . ini_get('memory_limit'));

// get initial data
$conversion_servers = [];
$temp = mr2array(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where status_id in (0,1) order by rand()"));
foreach ($temp as $res)
{
	$conversion_servers[$res['server_id']] = $res;
}

log_output('INFO  Conversion servers: ' . count($temp));

$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_id asc"));
$formats_screenshots = mr2array(sql_pr("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) order by format_screenshot_id asc"));
$formats_albums = mr2array(sql_pr("select * from $config[tables_prefix]formats_albums where status_id in (0,1) order by format_album_id asc"));

log_output('INFO  Active video formats: ' . count($formats_videos));
log_output('INFO  Active screenshot formats: ' . count($formats_screenshots));
log_output('INFO  Active album formats: ' . count($formats_albums));

$source_download_base_url = $config['project_url'];
if ($config['primary_server_url'])
{
	$source_download_base_url = $config['primary_server_url'];
}

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
sort($plugins_list);

$plugins_on_new = array();
$plugins_on_new_str = 'none';
foreach ($plugins_list as $k => $v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
	{
		continue;
	}
	$file_data = file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
	preg_match("|<plugin_types>(.*?)</plugin_types>|is", $file_data, $temp_find);
	$plugin_types = explode(',', trim($temp_find[1]));
	$is_on_new = 0;
	foreach ($plugin_types as $type)
	{
		if ($type == 'process_object')
		{
			$is_on_new = 1;
		}
	}

	if ($is_on_new == 1)
	{
		require_once "$config[project_path]/admin/plugins/$v/$v.php";
		$process_plugin_function = "{$v}IsEnabled";
		if (function_exists($process_plugin_function))
		{
			if ($process_plugin_function())
			{
				$plugins_on_new[] = $v;
				if ($plugins_on_new_str == 'none')
				{
					$plugins_on_new_str = $v;
				} else
				{
					$plugins_on_new_str .= ", $v";
				}
			}
		}
	}
}
log_output("INFO  Active plugins for new objects: $plugins_on_new_str");

$latest_api_version = $options['SYSTEM_CONVERSION_API_VERSION'];
log_output("INFO  Latest conversion API version: $latest_api_version");

log_output('');

if (floatval($options['LIMIT_CONVERSION_LA']) > 0)
{
	if (get_LA() > floatval($options['LIMIT_CONVERSION_LA']))
	{
		log_output('INFO  Conversion engine is limited by LA, the current LA is ' . get_LA());
		die;
	}
}
if ($options['LIMIT_CONVERSION_TIME_FROM'])
{
	$temp = explode(':', $options['LIMIT_CONVERSION_TIME_FROM']);
	if (count($temp) == 2)
	{
		$current_date = getdate();
		if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) < intval($temp[0]) * 3600 + intval($temp[1]) * 60)
		{
			log_output('INFO  Conversion engine is limited by time before ' . $options['LIMIT_CONVERSION_TIME_FROM']);
			die;
		}
	}
}
if ($options['LIMIT_CONVERSION_TIME_TO'])
{
	$temp = explode(':', $options['LIMIT_CONVERSION_TIME_TO']);
	if (count($temp) == 2)
	{
		$current_date = getdate();
		if (intval($current_date['hours']) * 3600 + intval($current_date['minutes']) * 60 + intval($current_date['seconds']) > intval($temp[0]) * 3600 + intval($temp[1]) * 60)
		{
			log_output('INFO  Conversion engine is limited by time after ' . $options['LIMIT_CONVERSION_TIME_TO']);
			die;
		}
	}
}

$tasks_per_loop = 10;

// get delete tasks first
$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where status_id=0 and type_id in (2,11) order by priority desc, task_id asc limit $tasks_per_loop"));
$tasks_count = count($data);

$delete_tasks_processed = 0;
if ($tasks_count > 0)
{
	while ($tasks_count > 0)
	{
		log_output('');
		log_output('INFO  Delete tasks (top priority): ' . count($data));

		if ($delete_tasks_processed >= 100 && $options['ENABLE_BACKGROUND_TASKS_PAUSE'] != 1)
		{
			log_output('INFO  Max 100 delete tasks reached, skipping for another tasks');
			break;
		}

		foreach ($data as $res)
		{
			$delete_tasks_processed++;
			$global_current_task_id = $res['task_id'];
			log_output('');
			log_output("INFO  Starting task $res[task_id]");

			if ($res['data'])
			{
				$res['data'] = @unserialize($res['data']);
			}

			// update start time
			$res['start_date'] = date('Y-m-d H:i:s');
			sql_update("update $config[tables_prefix]background_tasks set start_date=? where task_id=?", $res['start_date'], $res['task_id']);

			switch ($res['type_id'])
			{
				case 2:
					exec_delete_video($res);
					break;
				case 11:
					exec_delete_album($res, $formats_albums);
					break;
			}
		}

		$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where status_id=0 and type_id in (2,11) order by priority desc, task_id asc limit $tasks_per_loop"));
		$tasks_count = count($data);
	}
} else
{
	log_output('');
	log_output('INFO  Delete tasks (top priority): ' . count($data));
}
$global_current_task_id = 0;

if ($options['ENABLE_BACKGROUND_TASKS_PAUSE'] == 1 || is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id=1 and type_id not in (50,51,52,53)")) == 0)
	{
		log_output('WARN  Background tasks are paused');
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			file_put_contents("$config[project_path]/admin/data/system/background_tasks_pause.dat", '1', LOCK_EX);
		}
		die;
	}
}

// get tasks in process
$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where status_id=1 and type_id not in (50,51,52,53) order by priority desc, task_id asc"));
log_output('');
log_output('INFO  Tasks in process: ' . count($data));

foreach ($data as $res)
{
	if ($res['type_id'] == 50 || $res['type_id'] == 51 || $res['type_id'] == 52 || $res['type_id'] == 53)
	{
		log_output('');
		log_output("INFO  Skipped fake task $res[task_id]");
		continue;
	}

	$global_current_task_id = $res['task_id'];
	log_output('');
	log_output("INFO  Starting task $res[task_id]");

	if ($res['data'])
	{
		$res['data'] = @unserialize($res['data']);
	}
	$video_id = intval($res['video_id']);
	if ($video_id > 0)
	{
		$dir_path = get_dir_by_id($video_id);
		mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id");
		mkdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id");
	}
	$album_id = intval($res['album_id']);
	if ($album_id > 0)
	{
		$dir_path = get_dir_by_id($album_id);
		mkdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id");
	}

	switch ($res['type_id'])
	{
		case 1:
			exec_new_video($res, $conversion_servers[$res['server_id']], $formats_videos, $formats_screenshots);
			break;
		case 2:
			exec_delete_video($res);
			break;
		case 3:
			exec_upload_video_file($res, $conversion_servers[$res['server_id']], $formats_screenshots);
			break;
		case 4:
			exec_create_video_file($res, $conversion_servers[$res['server_id']], $formats_videos, $formats_screenshots);
			break;
		case 5:
			exec_delete_video_file($res);
			break;
		case 6:
			exec_delete_format_videos($res);
			break;
		case 7:
			exec_create_format_screenshots($res, $formats_videos);
			break;
		case 8:
			exec_create_video_timeline_screenshots($res, $conversion_servers[$res['server_id']], $formats_screenshots);
			break;
		case 9:
			exec_delete_format_screenshots($res, $formats_videos);
			break;
		case 10:
			exec_new_album($res, $conversion_servers[$res['server_id']], $formats_albums);
			break;
		case 11:
			exec_delete_album($res, $formats_albums);
			break;
		case 12:
			exec_create_format_albums($res);
			break;
		case 13:
			exec_delete_format_albums($res);
			break;
		case 14:
			exec_upload_album_images($res, $conversion_servers[$res['server_id']], $formats_albums);
			break;
		case 15:
			exec_migrate_video($res);
			break;
		case 16:
			exec_create_zip_screenshots($res, $formats_videos);
			break;
		case 17:
			exec_delete_zip_screenshots($res, $formats_videos);
			break;
		case 18:
			exec_create_zip_images($res);
			break;
		case 19:
			exec_delete_zip_images($res);
			break;
		case 20:
			exec_delete_timeline_screenshots($res);
			break;
		case 22:
			exec_change_album_images($res, $conversion_servers[$res['server_id']], $formats_albums);
			break;
		case 23:
			exec_migrate_album($res, $formats_albums);
			break;
		case 24:
			exec_create_video_overview_screenshots($res, $formats_videos, $formats_screenshots);
			break;
		case 27:
			exec_sync_storage_server($res, $formats_albums);
			break;
		case 28:
			exec_delete_video_overview_screenshots($res, $formats_screenshots);
			break;
		case 29:
			exec_create_video_formats_screenshots($res, $formats_videos);
			break;
		case 30:
			exec_create_album_formats_albums($res);
			break;
	}
}
$global_current_task_id = 0;

if ($options['ENABLE_BACKGROUND_TASKS_PAUSE'] == 1 || is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	log_output('WARN  Background tasks are paused');
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id=1 and type_id not in (50,51,52,53)")) == 0)
	{
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			file_put_contents("$config[project_path]/admin/data/system/background_tasks_pause.dat", '1', LOCK_EX);
		}
	}
	die;
}

$start_time = time();

$tasks_per_loop = 10;

// get new tasks
$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where status_id=0 and type_id not in (2,11,50,51,52,53) order by priority desc, type_id asc, task_id asc limit $tasks_per_loop"));
$tasks_count = count($data);

$last_skipped_id = 0;
$processed_tasks = [];
if ($tasks_count > 0)
{
	// get conversion servers utilization
	$conversion_servers = mr2array(sql_pr("select *, 1 as is_conversion_server, (select count(*) from $config[tables_prefix]background_tasks where $config[tables_prefix]background_tasks.status_id in (0,1) and $config[tables_prefix]background_tasks.server_id=$config[tables_prefix]admin_conversion_servers.server_id) as tasks_count from $config[tables_prefix]admin_conversion_servers where status_id=1 order by rand()"));
	$has_old_api_server = false;
	foreach ($conversion_servers as $k => $res)
	{
		if (intval(str_replace('.', '', $res['api_version'])) < intval(str_replace('.', '', $latest_api_version)))
		{
			if (!$has_old_api_server)
			{
				log_output('');
			}
			log_output("WARN  Server \"$res[title]\" API version is obsolete: $res[api_version]");
			unset($conversion_servers[$k]);
			$has_old_api_server = true;
		}
	}

	while ($tasks_count > 0)
	{
		$global_current_task_id = 0;
		log_output('');
		log_output("INFO  New tasks: $tasks_count");
		$had_any_task = false;
		foreach ($data as $res)
		{
			if ($options['ENABLE_BACKGROUND_TASKS_PAUSE'] == 1)
			{
				log_output('Background tasks are paused');
				die;
			}
			if (isset($processed_tasks[$res['task_id']]))
			{
				$last_skipped_id = $res['task_id'];
				log_output('');
				log_output("INFO  Skipping task $res[task_id]");
				continue;
			}
			$had_any_task = true;

			if ($res['data'])
			{
				$res['data'] = @unserialize($res['data']);
			}
			$video_id = intval($res['video_id']);
			if ($video_id > 0)
			{
				$dir_path = get_dir_by_id($video_id);
				mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id");
				mkdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id");
			}
			$album_id = intval($res['album_id']);
			if ($album_id > 0)
			{
				$dir_path = get_dir_by_id($album_id);
				mkdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id");
			}

			// find free conversion server if it is required
			$needs_conversion_server = 0;
			$server_data = null;
			if (in_array($res['type_id'], array(1, 3, 4, 8, 10, 14, 22)))
			{
				$needs_conversion_server = 1;
				shuffle($conversion_servers);
				foreach ($conversion_servers as $res_server)
				{
					if ($res_server['tasks_count'] < $res_server['max_tasks'])
					{
						if (test_connection_status($res_server))
						{
							$server_data = $res_server;
							break;
						} else
						{
							log_output("WARN  Failed to test connection to $res_server[title] conversion server");
						}
					}
				}
				if (!isset($server_data))
				{
					log_output("INFO  No free conversion server is found, skipping task $res[task_id]");
					$last_skipped_id = $res['task_id'];
					continue;
				} else
				{
					$global_current_task_id = $res['task_id'];
					log_output('');
					log_output("INFO  Starting task $res[task_id]");
					log_output("INFO  Free conversion server is found: $server_data[title]");
				}
			} else
			{
				$global_current_task_id = $res['task_id'];
				log_output('');
				log_output("INFO  Starting task $res[task_id]");
			}

			// update start time
			$res['start_date'] = date('Y-m-d H:i:s');
			sql_update("update $config[tables_prefix]background_tasks set start_date=? where task_id=?", $res['start_date'], $res['task_id']);

			// execute task
			switch ($res['type_id'])
			{
				case 1:
					$result_function = exec_new_video($res, $server_data, $formats_videos, $formats_screenshots);
					break;
				case 2:
					$result_function = exec_delete_video($res);
					break;
				case 3:
					$result_function = exec_upload_video_file($res, $server_data, $formats_screenshots);
					break;
				case 4:
					$result_function = exec_create_video_file($res, $server_data, $formats_videos, $formats_screenshots);
					break;
				case 5:
					$result_function = exec_delete_video_file($res);
					break;
				case 6:
					$result_function = exec_delete_format_videos($res);
					break;
				case 7:
					$result_function = exec_create_format_screenshots($res, $formats_videos);
					break;
				case 8:
					$result_function = exec_create_video_timeline_screenshots($res, $server_data, $formats_screenshots);
					break;
				case 9:
					$result_function = exec_delete_format_screenshots($res, $formats_videos);
					break;
				case 10:
					$result_function = exec_new_album($res, $server_data, $formats_albums);
					break;
				case 11:
					$result_function = exec_delete_album($res, $formats_albums);
					break;
				case 12:
					$result_function = exec_create_format_albums($res);
					break;
				case 13:
					$result_function = exec_delete_format_albums($res);
					break;
				case 14:
					$result_function = exec_upload_album_images($res, $server_data, $formats_albums);
					break;
				case 15:
					$result_function = exec_migrate_video($res);
					break;
				case 16:
					$result_function = exec_create_zip_screenshots($res, $formats_videos);
					break;
				case 17:
					$result_function = exec_delete_zip_screenshots($res, $formats_videos);
					break;
				case 18:
					$result_function = exec_create_zip_images($res);
					break;
				case 19:
					$result_function = exec_delete_zip_images($res);
					break;
				case 20:
					$result_function = exec_delete_timeline_screenshots($res);
					break;
				case 22:
					$result_function = exec_change_album_images($res, $server_data, $formats_albums);
					break;
				case 23:
					$result_function = exec_migrate_album($res, $formats_albums);
					break;
				case 24:
					$result_function = exec_create_video_overview_screenshots($res, $formats_videos, $formats_screenshots);
					break;
				case 27:
					$result_function = exec_sync_storage_server($res, $formats_albums);
					break;
				case 28:
					$result_function = exec_delete_video_overview_screenshots($res, $formats_screenshots);
					break;
				case 29:
					$result_function = exec_create_video_formats_screenshots($res, $formats_videos);
					break;
				case 30:
					$result_function = exec_create_album_formats_albums($res);
					break;
			}

			if ($result_function === true && $needs_conversion_server == 1)
			{
				foreach ($conversion_servers as $k => $v)
				{
					if ($v['server_id'] == $server_data['server_id'])
					{
						$conversion_servers[$k]['tasks_count']++;
					}
				}
			}
			$processed_tasks[$res['task_id']] = true;
		}

		if ($last_skipped_id > 0)
		{
			$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where task_id>$last_skipped_id and status_id=0 and type_id not in (1,3,4,8,10,14,22,2,11,50,51,52,53) order by priority desc, type_id asc, task_id asc limit $tasks_per_loop"));
		} else
		{
			$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where status_id=0 and type_id not in (2,11,50,51,52,53) order by priority desc, type_id asc, task_id asc limit $tasks_per_loop"));
		}
		$tasks_count = count($data);
		if (!$had_any_task)
		{
			$tasks_count = 0;
		}
		if (time() - $start_time > 585)
		{
			$tasks_count = 0;
			log_output('');
			log_output('INFO  Time limit reached for single conversion execution');
		}
	}
} else
{
	log_output('');
	log_output("INFO  New tasks: $tasks_count");
}
$global_current_task_id = 0;

disconnect_all_servers();

log_output('');
log_output('INFO  Tasks processor finished');
flock($lock, LOCK_UN);
fclose($lock);

function exec_new_video($task_data,$server_data,$formats_videos,$formats_screenshots)
{
	global $config, $options, $plugins_on_new, $source_download_base_url;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}

	$dir_path = get_dir_by_id($video_id);
	if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		cancel_task(5, "Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id", 0, $task_data['task_id'], $server_data);
		return false;
	}

	$server_data = mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($server_data['server_id'])));

	// retain only needed formats
	$uploaded_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id", 1);
	if ($res_video['load_type_id'] == 1)
	{
		foreach ($formats_videos as $k => $format)
		{
			if ($format['status_id'] == 0)
			{
				unset($formats_videos[$k]);
				continue;
			}
			if ($format['video_type_id'] == 0 && $res_video['is_private'] != 0 && $res_video['is_private'] != 1)
			{
				unset($formats_videos[$k]);
				continue;
			}
			if ($format['video_type_id'] == 1 && $res_video['is_private'] != 2)
			{
				unset($formats_videos[$k]);
				continue;
			}
			if ($task_data['status_id'] == 0)
			{
				$is_uploaded = 0;
				foreach ($uploaded_files as $file)
				{
					if ($file == "$video_id{$format['postfix']}")
					{
						$is_uploaded = 1;
					}
				}
				if ($is_uploaded == 1)
				{
					$formats_videos[$k]['is_uploaded_manually'] = 1;
				}
			}
		}
	} else
	{
		$formats_videos = [];
	}

	$custom_crop_options = '';
	if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0 && $res_video['content_source_id'] > 0)
	{
		$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
		$custom_crop_options = $res_content_source["custom{$options['SCREENSHOTS_CROP_CUSTOMIZE']}"];
	}

	if ($task_data['status_id'] == 0)
	{
		if (!isset($server_data))
		{
			warn_task("Conversion server is not available in the database, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}

		if (is_array($task_data['data']['sources_download']) || $task_data['data']['source_download'])
		{
			if (min(@disk_free_space($config['project_path']), @disk_free_space($config['content_path_videos_sources'])) < $options['MAIN_SERVER_MIN_FREE_SPACE_MB'] * 1024 * 1024)
			{
				warn_task("Server free space is lower than $options[MAIN_SERVER_MIN_FREE_SPACE_MB]M, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
				return false;
			}
		}

		log_output('', $video_id, 1, 1);
		log_output("INFO  New video creation task is started for video $video_id [PH-P]", $video_id);

		if ($task_data['data']['import_data'])
		{
			log_output("INFO  Imported using the following data:", $video_id);
			log_output($task_data['data']['import_data'], $video_id, 1);
		}

		if (is_array($task_data['data']['sources_download']))
		{
			log_output("INFO  Downloading source files [PH-P-1]", $video_id);
			foreach ($task_data['data']['sources_download'] as $source_download_key => $source_download_url)
			{
				$supports_download_format = 0;
				foreach ($formats_videos as $format)
				{
					if ("$video_id{$source_download_key}" == "$video_id{$format['postfix']}")
					{
						$supports_download_format = 1;
						break;
					}
				}
				if ($supports_download_format == 1)
				{
					log_output("INFO  ....Downloading source file from $source_download_url", $video_id);
					@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$source_download_key}");
					save_file_from_url($source_download_url, "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$source_download_key}", trim($task_data['data']['source_download_referer']));

					$downloaded_file_existed = false;
					foreach ($uploaded_files as $uploaded_file)
					{
						if ($uploaded_file == "$video_id{$source_download_key}")
						{
							$downloaded_file_existed = true;
						}
					}
					if (!$downloaded_file_existed)
					{
						$uploaded_files[] = "$video_id{$source_download_key}";
						foreach ($formats_videos as $k => $format)
						{
							if ("$video_id{$source_download_key}" == "$video_id{$format['postfix']}")
							{
								$formats_videos[$k]['is_uploaded_manually'] = 1;
							}
						}
					}
				} else
				{
					log_output("WARN  ....Skipped downloading $source_download_key file from $source_download_url", $video_id);
				}
			}
		} elseif ($task_data['data']['source_download'])
		{
			log_output("INFO  Downloading source files [PH-P-1]", $video_id);
			log_output("INFO  ....Downloading source file from " . $task_data['data']['source_download'], $video_id);
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/" . $task_data['data']['source']);
			save_file_from_url($task_data['data']['source_download'], "$config[content_path_videos_sources]/$dir_path/$video_id/" . $task_data['data']['source'], trim($task_data['data']['source_download_referer']));

			$downloaded_file_existed = false;
			foreach ($uploaded_files as $uploaded_file)
			{
				if ($uploaded_file == $task_data['data']['source'])
				{
					$downloaded_file_existed = true;
				}
			}
			if (!$downloaded_file_existed)
			{
				$uploaded_files[] = $task_data['data']['source'];
				foreach ($formats_videos as $k => $format)
				{
					if ($task_data['data']['source'] == "$video_id{$format['postfix']}")
					{
						$formats_videos[$k]['is_uploaded_manually'] = 1;
					}
				}
			}
		}

		if (count($uploaded_files) > 0)
		{
			$uploaded_files_str = '';
			foreach ($uploaded_files as $uploaded_file)
			{
				$uploaded_file_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$uploaded_file"));
				$uploaded_files_str .= "$uploaded_file [$uploaded_file_size], ";
			}
			$uploaded_files_str = trim($uploaded_files_str, ' ,');
			log_output("INFO  Files uploaded for new video: $uploaded_files_str", $video_id);

			foreach ($uploaded_files as $uploaded_file)
			{
				if (strtolower(pathinfo($uploaded_file, PATHINFO_EXTENSION)) != 'zip')
				{
					if (is_video_secure("$config[content_path_videos_sources]/$dir_path/$video_id/$uploaded_file"))
					{
						unset($res);
						exec("$config[ffmpeg_path] -i \"$config[content_path_videos_sources]/$dir_path/$video_id/$uploaded_file\"  2>&1", $res);
						$video_info = implode("\n....", $res);
						if (strpos($video_info, 'Input #0') !== false)
						{
							$video_info = substr($video_info, strpos($video_info, 'Input #0'));
						}

						log_output("INFO  $uploaded_file file information:", $video_id);
						log_output("...." . $video_info, $video_id, 1);
					}
				}
			}
		}

		log_output("INFO  Preparing task for conversion server [PH-P-2]", $video_id);

		$task_info = [];
		$task_info['video_id'] = $video_id;
		$task_info['options']['PROCESS_PRIORITY'] = $options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['SCREENSHOTS_COUNT_UNIT'] = $options['SCREENSHOTS_COUNT_UNIT'];
		$task_info['options']['SCREENSHOTS_COUNT'] = $options['SCREENSHOTS_COUNT'];
		$task_info['options']['SCREENSHOTS_SECONDS_OFFSET'] = $options['SCREENSHOTS_SECONDS_OFFSET'];
		$task_info['options']['SCREENSHOTS_SECONDS_OFFSET_END'] = $options['SCREENSHOTS_SECONDS_OFFSET_END'];
		$task_info['options']['SCREENSHOTS_CROP_LEFT_UNIT'] = $options['SCREENSHOTS_CROP_LEFT_UNIT'];
		$task_info['options']['SCREENSHOTS_CROP_RIGHT_UNIT'] = $options['SCREENSHOTS_CROP_RIGHT_UNIT'];
		$task_info['options']['SCREENSHOTS_CROP_TOP_UNIT'] = $options['SCREENSHOTS_CROP_TOP_UNIT'];
		$task_info['options']['SCREENSHOTS_CROP_BOTTOM_UNIT'] = $options['SCREENSHOTS_CROP_BOTTOM_UNIT'];
		$task_info['options']['SCREENSHOTS_CROP_LEFT'] = $options['SCREENSHOTS_CROP_LEFT'];
		$task_info['options']['SCREENSHOTS_CROP_RIGHT'] = $options['SCREENSHOTS_CROP_RIGHT'];
		$task_info['options']['SCREENSHOTS_CROP_TOP'] = $options['SCREENSHOTS_CROP_TOP'];
		$task_info['options']['SCREENSHOTS_CROP_BOTTOM'] = $options['SCREENSHOTS_CROP_BOTTOM'];
		$task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE'] = $custom_crop_options;
		$task_info['options']['SCREENSHOTS_CROP_TRIM_SIDES'] = $options['SCREENSHOTS_CROP_TRIM_SIDES'];
		$task_info['options']['SCREENSHOTS_UPLOADED_CROP'] = $options['SCREENSHOTS_UPLOADED_CROP'];
		$task_info['options']['SCREENSHOTS_UPLOADED_WATERMARK'] = $options['SCREENSHOTS_UPLOADED_WATERMARK'];
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY'] = $config['imagemagick_default_jpeg_quality'];
		if ($server_data['connection_type_id'] == 0)
		{
			$task_info['source_dir'] = "$config[content_path_videos_sources]/$dir_path/$video_id";
		}

		if ($config['installation_type'] >= 3)
		{
			$task_info['options']['PROCESS_PRIORITY'] = intval($server_data['process_priority']);
		}
		log_output("INFO  Conversion priority level is set to " . $task_info['options']['PROCESS_PRIORITY'], $video_id);

		// populate formats that are needed to be created / or timeline screenshots need to be created from them
		$formats_to_create = [];
		$formats_to_postprocess = [];
		$formats_to_make_timelines = [];
		if (count($formats_videos) > 0)
		{
			foreach ($formats_videos as $format)
			{
				if ($format['is_uploaded_manually'] == 1)
				{
					log_output("INFO  Video format file has been uploaded: \"$format[title]\"", $video_id);
				}
				$create_format = false;
				if ($format['status_id'] == 1 && $format['is_uploaded_manually'] != 1)
				{
					$create_format = true;
				}
				if ($format['status_id'] == 2 && $format['is_conditional'] == 1 && $format['is_uploaded_manually'] != 1)
				{
					if (intval($options['VIDEOS_HALF_PROCESSING']) == 1 && is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
					{
						log_output("INFO  Format \"$format[title]\" creation will be postponed", $video_id);
					} else
					{
						$create_format = true;
					}
				}
				if ($create_format)
				{
					if ($format['customize_duration_id'] > 0 || $format['customize_offset_start_id'] > 0 || $format['customize_offset_end_id'] > 0)
					{
						if ($res_video['content_source_id'] > 0)
						{
							$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
							if ($format['customize_duration_id'] > 0 && intval($res_content_source["custom{$format['customize_duration_id']}"]) > 0)
							{
								$format['limit_total_duration'] = intval($res_content_source["custom{$format['customize_duration_id']}"]);
								$format['limit_total_duration_unit_id'] = 0;
								log_output("INFO  Format \"$format[title]\": duration $format[limit_total_duration] is taken from content source custom field #$format[customize_duration_id]", $video_id);
							}
							if ($format['customize_offset_start_id'] > 0 && intval($res_content_source["custom{$format['customize_offset_start_id']}"]) > 0)
							{
								$format['limit_offset_start'] = intval($res_content_source["custom{$format['customize_offset_start_id']}"]);
								$format['limit_offset_start_unit_id'] = 0;
								log_output("INFO  Format \"$format[title]\": start offset $format[limit_offset_start] is taken from content source custom field #$format[customize_offset_start_id]", $video_id);
							}
							if ($format['customize_offset_end_id'] > 0 && intval($res_content_source["custom{$format['customize_offset_end_id']}"]) > 0)
							{
								$format['limit_offset_end'] = intval($res_content_source["custom{$format['customize_offset_end_id']}"]);
								$format['limit_offset_end_unit_id'] = 0;
								log_output("INFO  Format \"$format[title]\": end offset $format[limit_offset_end] is taken from content source custom field #$format[customize_offset_end_id]", $video_id);
							}
						}
					}
					$formats_to_create[] = $format;
				}
			}
			$task_info['videos_creation_list'] = $formats_to_create;
			log_output("INFO  Video formats will be created: " . print_formats_list($formats_to_create), $video_id);

			if (count($formats_to_create) > 0 && is_file("$config[project_path]/admin/include/kvs_filter_video.php"))
			{
				require_once "$config[project_path]/admin/include/kvs_filter_video.php";
				$filter_custom_function = 'kvs_filter_video';
				if (function_exists($filter_custom_function))
				{
					$custom_source_filter = $filter_custom_function($res_video);
					if ($custom_source_filter && is_array($custom_source_filter))
					{
						$task_info['source_filter'] = $custom_source_filter;
						log_output("INFO  Using custom source filter: " . print_object($custom_source_filter), $video_id);
					}
				}
			}

			foreach ($formats_videos as $format)
			{
				if (strpos($format['postfix'], ".mp4") !== false)
				{
					if ($format['is_uploaded_manually'] == 1)
					{
						$formats_to_postprocess[] = $format;
					}
				}
			}
			$task_info['videos_post_process_list'] = $formats_to_postprocess;
			log_output("INFO  Video files will be post-processed: " . print_formats_list($formats_to_postprocess), $video_id);

			foreach ($formats_videos as $format)
			{
				if ($format['is_timeline_enabled'] == 1)
				{
					if ($format['is_uploaded_manually'] == 1 || $format['status_id'] == 1 || ($format['status_id'] == 2 && $format['is_conditional'] == 1))
					{
						$formats_to_make_timelines[] = $format;
					}
				}
			}
			$task_info['timelines_creation_list'] = $formats_to_make_timelines;
			log_output("INFO  Timeline screenshots will be created for video formats: " . print_formats_list($formats_to_make_timelines), $video_id);
		}

		// check if overview screenshots should be created
		$make_screens = 1;
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots.zip"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
			{
				cancel_task(5, "Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			$zip = new PclZip("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots.zip");
			$data = process_zip_images($zip->listContent());
			$counter = 0;
			foreach ($data as $v)
			{
				$counter++;
				$file_base_name = $v['filename'];
				$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
				$fstream = $content[0]['content'];
				if ($fstream == '')
				{
					cancel_task(9, "Failed to extract $file_base_name from ZIP, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$fp = fopen("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot$counter.jpg", "w");
				fwrite($fp, $fstream);
				fclose($fp);
			}
		}
		if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
		{
			$screen_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", 1);
			$screen_sizes = [];
			for ($i = 1; $i <= count($screen_files); $i++)
			{
				$screen_dimensions = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot{$i}.jpg");
				if (!$screen_dimensions || intval($screen_dimensions[0]) == 0 || intval($screen_dimensions[1]) == 0)
				{
					cancel_task(9, "Invalid screenshot image: $config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot{$i}.jpg", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$screen_sizes[] = "$screen_dimensions[0]x$screen_dimensions[1]";
			}

			if (count($screen_files) == 0)
			{
				cancel_task(1, "Invalid screenshot directory: $config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			if (count($screen_files) == 1)
			{
				if ($options['SCREENSHOTS_COUNT_UNIT'] == 1 && $options['SCREENSHOTS_COUNT'] == 1)
				{
					$make_screens = 0;
				}
				if (($res_video['load_type_id'] == 2 || $res_video['load_type_id'] == 3 || $res_video['load_type_id'] == 5) && !$task_data['data']['video_url'])
				{
					$make_screens = 0;
				}
				log_output("INFO  Video main screenshot has been uploaded: $screen_sizes[0]", $video_id);
			} else
			{
				$make_screens = 0;
				log_output("INFO  Video overview screenshots has been uploaded: " . count($screen_files) . " images [" . implode(', ', $screen_sizes) . "]", $video_id);
			}
		}
		if ($make_screens == 1)
		{
			log_output("INFO  Sources for overview screenshots will be created", $video_id);
			$task_info['make_screens'] = 1;
		}
		if ($custom_crop_options)
		{
			log_output("INFO  Using custom crop options for overview screenshots: $custom_crop_options", $video_id);
		}

		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters.zip"))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters"))
			{
				cancel_task(5, "Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			$zip = new PclZip("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters.zip");
			$data = process_zip_images($zip->listContent());
			$counter = 0;
			foreach ($data as $v)
			{
				$counter++;
				$file_base_name = $v['filename'];
				$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
				$fstream = $content[0]['content'];
				if ($fstream == '')
				{
					cancel_task(9, "Failed to extract $file_base_name from ZIP, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$fp = fopen("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters/poster$counter.jpg", "w");
				fwrite($fp, $fstream);
				fclose($fp);
			}
		}
		if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters"))
		{
			$poster_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", 1);
			$poster_sizes = [];
			for ($i = 1; $i <= count($poster_files); $i++)
			{
				$poster_dimensions = @getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters/poster{$i}.jpg");
				if (!$poster_dimensions || intval($poster_dimensions[0]) == 0 || intval($poster_dimensions[1]) == 0)
				{
					cancel_task(9, "Invalid poster image: $config[content_path_videos_sources]/$dir_path/$video_id/temp/posters/poster{$i}.jpg", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$poster_sizes[] = "$poster_dimensions[0]x$poster_dimensions[1]";
			}

			if (count($poster_files) == 0)
			{
				cancel_task(1, "Invalid poster directory: $config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			log_output("INFO  Video posters has been uploaded: " . count($poster_files) . " images [" . implode(', ', $poster_sizes) . "]", $video_id);
		}

		switch ($res_video['load_type_id'])
		{
			case 1:
				// file upload

				if ($server_data['option_storage_servers'] == 1)
				{
					// detect server group and check storage server
					$server_group_id = 0;
					if (intval($res_video['server_group_id']) > 0)
					{
						$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($res_video['server_group_id'])));
					} elseif (intval($task_data['data']['server_group_id']) > 0)
					{
						$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($task_data['data']['server_group_id'])));
					} elseif ($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'] == 'rand')
					{
						$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and status_id=1 order by rand() limit 1"));
					} elseif (intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO']) > 0)
					{
						$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'])));
					}
					if (intval($server_group_id) == 0)
					{
						$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and status_id=1 order by (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) desc"));
					}
					if (intval($server_group_id) == 0)
					{
						warn_task("No server group found, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
						return false;
					}
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where group_id=? and status_id=1", $server_group_id)) == 0)
					{
						warn_task("No active servers found in server group $server_group_id, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
						return false;
					}

					$has_local_storage = false;
					$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
					foreach ($storage_servers as $server)
					{
						if ($server['connection_type_id'] != 2)
						{
							$has_local_storage = true;
						}
						if (!test_connection_status($server))
						{
							warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
							return false;
						}
						if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
						{
							warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
							return false;
						}
					}
					if ($has_local_storage && $server_data['connection_type_id'] != 0)
					{
						log_output("WARN  Remote conversion server cannot be used to copy content to local storage", $video_id);
					} else
					{
						log_output("INFO  Selected server group: $server_group_id", $video_id);
						sql_update("update $config[tables_prefix]videos set server_group_id=? where video_id=?", $server_group_id, $video_id);
						$task_info['storage_servers'] = $storage_servers;
					}
				}

				$sources_count = intval(@count($task_data['data']['sources']));
				if ($sources_count > 1)
				{
					log_output("INFO  Source files detected: $sources_count", $video_id);

					for ($i = 0; $i < $sources_count; $i++)
					{
						$i1 = $i + 1;
						$source_file = $task_data['data']['sources'][$i];
						if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
						{
							cancel_task(9, "No source file available: $config[content_path_videos_sources]/$dir_path/$video_id/$source_file", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						$duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
						$dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
						$filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"));
						if ($duration == 0)
						{
							cancel_task(9, "Invalid duration for source file $i1", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						if ($dimensions[0] == 0 || $dimensions[1] == 0)
						{
							if (!is_audio("$config[content_path_videos_sources]/$dir_path/$video_id/" . $source_file))
							{
								cancel_task(9, "Invalid dimensions for source file $i1: $dimensions[0]x$dimensions[1]", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}

						if ($i == 0)
						{
							$task_info['source_dimensions'] = $dimensions;
						} elseif ($dimensions[0] != $task_info['source_dimensions'][0] || $dimensions[1] != $task_info['source_dimensions'][1])
						{
							cancel_task(9, "Dimensions of source file $i1 are different: $dimensions[0]x$dimensions[1]", $video_id, $task_data['task_id'], $server_data);
							return false;
						}

						log_output("INFO  Source file $i1 parameters are: duration - $duration sec, dimensions - $dimensions[0]x$dimensions[1], filesize - $filesize bytes", $video_id);

						$task_info['source_files'][] = $source_file;
						if ($server_data['connection_type_id'] != 0)
						{
							if ($server_data['option_pull_source_files'] == 1)
							{
								$hash = md5($config['cv'] . "$dir_path/$video_id/$source_file");
								$task_info['download_urls'][$source_file] = [
										'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$source_file/",
										'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
								];
							} else
							{
								log_output("INFO  Copying source file $source_file to conversion server [PH-P-2-2]", $video_id);
								if (!put_file($source_file, "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put $source_file file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
						}
					}
				} else
				{
					$duration = intval($task_data['data']['duration']);
					if ($duration == 0)
					{
						$duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}");
					}
					$dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}");
					$filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}"));
					$filekey = md5_file("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}");

					if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}"))
					{
						cancel_task(9, "No source file available: $config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					if ($options['VIDEOS_DUPLICATE_FILE_OPTION'] > 0)
					{
						$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where file_key=? and video_id!=? limit 1", $filekey, $video_id));
						if ($duplicate_video_id > 0)
						{
							cancel_task(9, "Duplicate source file, was already used in video $duplicate_video_id", $video_id, $task_data['task_id'], $server_data);
							return false;
						} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION'] == 2)
						{
							$duplicate_video_id = mr2number(sql_pr("select object_id from $config[tables_prefix]deleted_content where file_key=? limit 1", $filekey));
							if ($duplicate_video_id > 0)
							{
								cancel_task(9, "Duplicate source file, was already used in video $duplicate_video_id, which was then deleted", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}
					}

					if ($duration == 0)
					{
						cancel_task(9, "Invalid duration for source file", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
					if ($dimensions[0] == 0 || $dimensions[1] == 0)
					{
						if (!is_audio("$config[content_path_videos_sources]/$dir_path/$video_id/{$task_data['data']['source']}"))
						{
							cancel_task(9, "Invalid dimensions for source file: $dimensions[0]x$dimensions[1]", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}

					$task_info['source_dimensions'] = $dimensions;

					sql_update("update $config[tables_prefix]videos set duration=?, file_dimensions=?, file_size=?, file_key=? where video_id=?", $duration, "$dimensions[0]x$dimensions[1]", $filesize, $filekey, $video_id);
					log_output("INFO  Source video parameters are: duration - $duration sec, dimensions - $dimensions[0]x$dimensions[1], filesize - $filesize bytes", $video_id);

					if (count($formats_to_create) == 0 && count($formats_to_make_timelines) == 0 && count($formats_to_postprocess) == 0 && $make_screens == 0)
					{
						log_output("INFO  No need for background task on conversion server, skipping conversion process", $video_id);
						sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);

						mark_task_progress($task_data['task_id'], 10);
						mark_task_duration($task_data['task_id'], time() - $task_start_time);
						$task_data['status_id'] = 1;

						// finalize task processing
						return exec_new_video($task_data, $server_data, $formats_videos, $formats_screenshots);
					}

					$task_info['source_file'] = $task_data['data']['source'];
					if ($server_data['connection_type_id'] != 0)
					{
						if ($server_data['option_pull_source_files'] == 1)
						{
							$hash = md5($config['cv'] . "$dir_path/$video_id/$task_info[source_file]");
							$task_info['download_urls']["$task_info[source_file]"] = [
									'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$task_info[source_file]/",
									'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$task_info[source_file]"))
							];
						} else
						{
							log_output("INFO  Copying source file $task_info[source_file] to conversion server [PH-P-2-2]", $video_id);
							if (!put_file("$task_info[source_file]", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
							{
								cancel_task(2, "Failed to put $task_info[source_file] file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}
					}
				}
				foreach ($formats_to_postprocess as $format)
				{
					if ($server_data['connection_type_id'] != 0)
					{
						if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}") && check_file("$video_id{$format['postfix']}", "$task_data[task_id]", $server_data) == 0)
						{
							if ($server_data['option_pull_source_files'] == 1)
							{
								$hash = md5($config['cv'] . "$dir_path/$video_id/$video_id{$format['postfix']}");
								$task_info['download_urls']["$video_id{$format['postfix']}"] = [
										'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$video_id{$format['postfix']}/",
										'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}"))
								];
							} else
							{
								log_output("INFO  Copying format file $video_id{$format['postfix']} to conversion server [PH-P-2-2]", $video_id);
								if (!put_file("$video_id{$format['postfix']}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put $video_id{$format['postfix']} file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
						}
					}
				}
				foreach ($formats_to_make_timelines as $format)
				{
					if ($server_data['connection_type_id'] != 0)
					{
						if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}") && check_file("$video_id{$format['postfix']}", "$task_data[task_id]", $server_data) == 0)
						{
							if ($server_data['option_pull_source_files'] == 1)
							{
								$hash = md5($config['cv'] . "$dir_path/$video_id/$video_id{$format['postfix']}");
								$task_info['download_urls']["$video_id{$format['postfix']}"] = [
										'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$video_id{$format['postfix']}/",
										'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}"))
								];
							} else
							{
								log_output("INFO  Copying format file $video_id{$format['postfix']} to conversion server [PH-P-2-2]", $video_id);
								if (!put_file("$video_id{$format['postfix']}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put $video_id{$format['postfix']} file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
						}
					}
				}
				foreach ($formats_to_create as $format)
				{
					$format_watermark = '';
					$format_watermark_folder = '';
					if (is_file("$config[project_path]/admin/include/kvs_watermark_video.php"))
					{
						require_once "$config[project_path]/admin/include/kvs_watermark_video.php";
						$watermark_custom_function = 'kvs_watermark_video';
						if (function_exists($watermark_custom_function))
						{
							$temp_watermark_file = $watermark_custom_function($format['postfix'], $res_video);
							if ($temp_watermark_file && is_file($temp_watermark_file))
							{
								$rnd = mt_rand(1000000, 9999999);
								if (!mkdir_recursive("$config[temporary_path]/$rnd"))
								{
									cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
								rename($temp_watermark_file, "$config[temporary_path]/$rnd/watermark_video_{$format['format_video_id']}.png");
								$format_watermark = "watermark_video_{$format['format_video_id']}.png";
								$format_watermark_folder = "$config[temporary_path]/$rnd";
								log_output("INFO  Format \"$format[title]\": watermark image is generated by API function", $video_id);
							}
						}
					}
					if (!$format_watermark)
					{
						if ($format['customize_watermark_id'] > 0)
						{
							if ($res_video['content_source_id'] > 0)
							{
								$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
								if ($res_content_source["custom_file{$format['customize_watermark_id']}"])
								{
									if (is_file("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark_id']}"]))
									{
										$rnd = mt_rand(1000000, 9999999);
										if (!mkdir_recursive("$config[temporary_path]/$rnd"))
										{
											cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
											return false;
										}
										copy("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark_id']}"], "$config[temporary_path]/$rnd/watermark_video_{$format['format_video_id']}.png");
										$format_watermark = "watermark_video_{$format['format_video_id']}.png";
										$format_watermark_folder = "$config[temporary_path]/$rnd";
										log_output("INFO  Format \"$format[title]\": watermark image is taken from content source custom file field #$format[customize_watermark_id]", $video_id);
									}
								}
							}
						}
					}
					if (!$format_watermark)
					{
						if (is_file("$config[project_path]/admin/data/other/watermark_video_{$format['format_video_id']}.png"))
						{
							$format_watermark = "watermark_video_{$format['format_video_id']}.png";
							$format_watermark_folder = "$config[project_path]/admin/data/other";
							log_output("INFO  Format \"$format[title]\": watermark image is taken from video format", $video_id);
						}
					}
					if ($format_watermark && $format_watermark_folder)
					{
						if (!put_file($format_watermark, $format_watermark_folder, "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put $format_watermark file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}

					$format_watermark2 = '';
					$format_watermark2_folder = '';
					if (is_file("$config[project_path]/admin/include/kvs_watermark2_video.php"))
					{
						require_once "$config[project_path]/admin/include/kvs_watermark2_video.php";
						$watermark2_custom_function = 'kvs_watermark2_video';
						if (function_exists($watermark2_custom_function))
						{
							$temp_watermark2_file = $watermark2_custom_function($format['postfix'], $res_video);
							if ($temp_watermark2_file && is_file($temp_watermark2_file))
							{
								$rnd = mt_rand(1000000, 9999999);
								if (!mkdir_recursive("$config[temporary_path]/$rnd"))
								{
									cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
								rename($temp_watermark2_file, "$config[temporary_path]/$rnd/watermark2_video_{$format['format_video_id']}.png");
								$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
								$format_watermark2_folder = "$config[temporary_path]/$rnd";
								log_output("INFO  Format \"$format[title]\": watermark2 image is generated by API function", $video_id);
							}
						}
					}
					if (!$format_watermark2)
					{
						if ($format['customize_watermark2_id'] > 0)
						{
							if ($res_video['content_source_id'] > 0)
							{
								$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
								if ($res_content_source["custom_file{$format['customize_watermark2_id']}"])
								{
									if (is_file("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark2_id']}"]))
									{
										$rnd = mt_rand(1000000, 9999999);
										if (!mkdir_recursive("$config[temporary_path]/$rnd"))
										{
											cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
											return false;
										}
										copy("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark2_id']}"], "$config[temporary_path]/$rnd/watermark2_video_{$format['format_video_id']}.png");
										$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
										$format_watermark2_folder = "$config[temporary_path]/$rnd";
										log_output("INFO  Format \"$format[title]\": watermark2 image is taken from content source custom file field #$format[customize_watermark2_id]", $video_id);
									}
								}
							}
						}
					}
					if (!$format_watermark2)
					{
						if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$format['format_video_id']}.png"))
						{
							$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
							$format_watermark2_folder = "$config[project_path]/admin/data/other";
							log_output("INFO  Format \"$format[title]\": watermark2 image is taken from video format", $video_id);
						}
					}
					if ($format_watermark2 && $format_watermark2_folder)
					{
						if (!put_file($format_watermark2, $format_watermark2_folder, "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put $format_watermark2 file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
				}

				log_output("INFO  Copying screenshot files to conversion server [PH-P-2-2]", $video_id);

				$task_info['formats_screenshots'] = $formats_screenshots;
				foreach ($formats_screenshots as $format)
				{
					if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png"))
					{
						if (!put_file("watermark_screen_{$format['format_screenshot_id']}.png", "$config[project_path]/admin/data/other", "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put watermark_screen_{$format['format_screenshot_id']}.png file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
				}

				if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
				{
					$screen_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", 1);
					for ($i = 1; $i <= count($screen_files); $i++)
					{
						if (!put_file("screenshot{$i}.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put screenshot{$i}.jpg file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
					$task_info['uploaded_screenshots_count'] = count($screen_files);
				}

				if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters"))
				{
					$poster_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", 1);
					for ($i = 1; $i <= count($poster_files); $i++)
					{
						if (!put_file("poster{$i}.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put poster{$i}.jpg file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
					$task_info['uploaded_posters_count'] = count($poster_files);
				}

				file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat", serialize($task_info), LOCK_EX);
				if (!put_file('task.dat', "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
				{
					cancel_task(2, "Failed to put task.dat file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				unlink("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat");

				log_output("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]", $video_id);
				sql_update("update $config[tables_prefix]background_tasks set status_id=1, server_id=? where task_id=?", $server_data['server_id'], $task_data['task_id']);

				mark_task_progress($task_data['task_id'], 10);
				mark_task_duration($task_data['task_id'], time() - $task_start_time);
				return true;
			break;
			case 2:
			case 3:
			case 5:
				// hotlink and embed and pseudo
				$duration = intval($task_data['data']['duration']);
				if ($make_screens == 1 || $duration == 0 || (intval($options['KEEP_VIDEO_SOURCE_FILES']) == 1 && $task_data['data']['video_url']))
				{
					if (!$task_data['data']['video_url'])
					{
						cancel_task(9, "Remote file URL is not specified but required", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
					log_output("INFO  Downloading file: {$task_data['data']['video_url']} [PH-P-2-1]", $video_id);
					save_file_from_url(trim($task_data['data']['video_url']), "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp");

					if ($duration == 0)
					{
						$duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp");
					}
					if ($duration == 0)
					{
						cancel_task(9, "Invalid video after downloading remote file", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					$dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp");
					$filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"));

					if ($dimensions[0] == 0 || $dimensions[1] == 0)
					{
						if (!is_audio("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
						{
							cancel_task(9, "Invalid dimensions for source file: $dimensions[0]x$dimensions[1]", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}

					sql_update("update $config[tables_prefix]videos set duration=?, file_dimensions=?, file_size=? where video_id=?", $duration, "$dimensions[0]x$dimensions[1]", $filesize, $video_id);
					log_output("INFO  Source video parameters are: duration - $duration sec, dimensions - $dimensions[0]x$dimensions[1], filesize - $filesize bytes", $video_id);

					if ($make_screens == 1)
					{
						$task_info['source_dimensions'] = $dimensions;
						$task_info['source_file'] = "$video_id.tmp";
						if ($server_data['connection_type_id'] != 0)
						{
							if ($server_data['option_pull_source_files'] == 1)
							{
								$hash = md5($config['cv'] . "$dir_path/$video_id/$video_id.tmp");
								$task_info['download_urls']["$video_id.tmp"] = [
										'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$video_id.tmp/",
										'file_size' => $filesize
								];
							} else
							{
								log_output("INFO  Copying source file $video_id.tmp to conversion server [PH-P-2-2]", $video_id);
								if (!put_file("$video_id.tmp", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put $video_id.tmp file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
						}

						log_output("INFO  Copying screenshot files to conversion server [PH-P-2-2]", $video_id);

						$task_info['formats_screenshots'] = $formats_screenshots;
						foreach ($formats_screenshots as $format)
						{
							if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png"))
							{
								if (!put_file("watermark_screen_{$format['format_screenshot_id']}.png", "$config[project_path]/admin/data/other", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put watermark_screen_{$format['format_screenshot_id']}.png file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
						}

						if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
						{
							$screen_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", 1);
							for ($i = 1; $i <= count($screen_files); $i++)
							{
								if (!put_file("screenshot{$i}.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put screenshot{$i}.jpg file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
							$task_info['uploaded_screenshots_count'] = count($screen_files);
						}

						if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters"))
						{
							$poster_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", 1);
							for ($i = 1; $i <= count($poster_files); $i++)
							{
								if (!put_file("poster{$i}.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", "$task_data[task_id]", $server_data))
								{
									cancel_task(2, "Failed to put poster{$i}.jpg file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
									return false;
								}
							}
							$task_info['uploaded_posters_count'] = count($poster_files);
						}

						file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat", serialize($task_info), LOCK_EX);
						if (!put_file('task.dat', "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put task.dat file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						unlink("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat");

						log_output("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]", $video_id);
						sql_update("update $config[tables_prefix]background_tasks set status_id=1, server_id=? where task_id=?", $server_data['server_id'], $task_data['task_id']);

						mark_task_progress($task_data['task_id'], 10);
						mark_task_duration($task_data['task_id'], time() - $task_start_time);
						return true;
					}
				}

				if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
				{
					$dimensions = [0, 0];
					if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
					{
						$dimensions = getimagesize("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot1.jpg");
					}

					if ($res_video['load_type_id'] == 3 && $res_video['embed'])
					{
						unset($temp);
						preg_match("|width\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", $res_video['embed'], $temp);
						$embed_width = trim($temp[1]);

						unset($temp);
						preg_match("|height\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", $res_video['embed'], $temp);
						$embed_height = trim($temp[1]);

						if (strpos($embed_width, '%') === false && strpos($embed_height, '%') === false)
						{
							if ($embed_width > 0 && $embed_height > 0)
							{
								$dimensions = [intval($embed_width), intval($embed_height)];
							}
						}
					}

					sql_update("update $config[tables_prefix]videos set duration=?, file_dimensions=? where video_id=?", $duration, "$dimensions[0]x$dimensions[1]", $video_id);
					log_output("INFO  Source video parameters are: duration - $duration sec, dimensions - $dimensions[0]x$dimensions[1]", $video_id);
				}

				log_output("INFO  No need for background task on conversion server, skipping conversion process", $video_id);
				sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);

				mark_task_progress($task_data['task_id'], 10);
				mark_task_duration($task_data['task_id'], time() - $task_start_time);
				$task_data['status_id'] = 1;

				// finalize task processing
				return exec_new_video($task_data, $server_data, $formats_videos, $formats_screenshots);
			break;
			default:
				cancel_task(1, "Unknown video load type, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
				break;
		}
	} else {
		// check conversion task
		$conversion_log = '';
		$task_conversion_duration = 0;
		$result_data = [];
		if ($task_data['server_id'] > 0)
		{
			if (!isset($server_data))
			{
				cancel_task(1, "Conversion server $task_data[server_id] is not available in the database, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			if (check_file('progress.dat', "$task_data[task_id]", $server_data))
			{
				get_file('progress.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data);
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat"))
				{
					mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat")) * 0.6));
					unlink("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat");
				}
			}

			if (check_file('result.dat', "$task_data[task_id]", $server_data) == 0)
			{
				if (check_file('task.dat', "$task_data[task_id]", $server_data) > 0)
				{
					return false;
				} else
				{
					if (test_connection($server_data) === true)
					{
						cancel_task(2, "Task directory is not available on conversion server, cancelling this task", $video_id, $task_data['task_id']);
					} else
					{
						warn_task("Conversion server connection is lost, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					}
					return false;
				}
			}

			// check result file
			if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
			if (!is_array($result_data))
			{
				sleep(1);
				if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
				{
					cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
				if (!is_array($result_data))
				{
					cancel_task(6, "Unexpected error on conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
			$task_conversion_duration = intval($result_data['duration']);
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat");

			// check log file
			if (check_file('log.txt', "$task_data[task_id]", $server_data) > 0)
			{
				if (!get_file('log.txt', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
				{
					cancel_task(2, "Failed to get log.txt file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}

				if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt")) > 10 * 1000 * 1000)
				{
					$conversion_log = 'Conversion log is more than 10mb';
				} else
				{
					$conversion_log = trim(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt"));
				}
			}
			if (!$conversion_log)
			{
				cancel_task(3, "No conversion log is available, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt");

			// check if conversion result contains any error
			if ($result_data['is_error'] == 1)
			{
				log_output('', $video_id);
				log_output($conversion_log, $video_id, 1);
				cancel_task($result_data['error_code'] ?: 7, $result_data['error_message'] ?: "Conversion error, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
		}

		// detect server group and check storage server
		$storage_servers = [];
		if ($res_video['load_type_id'] == 1)
		{
			if ($res_video['server_group_id'] == 0)
			{
				$server_group_id = 0;
				if (intval($res_video['server_group_id']) > 0)
				{
					$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($res_video['server_group_id'])));
				} elseif (intval($task_data['data']['server_group_id']) > 0)
				{
					$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($task_data['data']['server_group_id'])));
				} elseif ($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'] == 'rand')
				{
					$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and status_id=1 order by rand() limit 1"));
				} elseif (intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO']) > 0)
				{
					$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and group_id=?", intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'])));
				}
				if (intval($server_group_id) == 0)
				{
					$server_group_id = mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=1 and status_id=1 order by (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) desc"));
				}
				if (intval($server_group_id) == 0)
				{
					warn_task("No server group found, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where group_id=? and status_id=1", $server_group_id)) == 0)
				{
					warn_task("No active servers found in server group $server_group_id, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
			} else
			{
				$server_group_id = $res_video['server_group_id'];
			}

			$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
			foreach ($storage_servers as $server)
			{
				if (!test_connection_status($server))
				{
					warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
				if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
				{
					warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
			}
		}

		mark_task_progress($task_data['task_id'], 70);

		// log conversion process
		if ($task_data['server_id'] > 0)
		{
			log_output('', $video_id);
			log_output($conversion_log, $video_id, 1);
		}

		log_output('', $video_id);
		log_output("INFO  New video creation task is continued for video $video_id [PH-F]", $video_id);

		if ($res_video['load_type_id'] == 1)
		{
			if (!isset($server_group_id))
			{
				$server_group_id = 0;
			}
			if ($res_video['server_group_id'] == 0)
			{
				log_output("INFO  Selected server group: $server_group_id", $video_id);
				sql_update("update $config[tables_prefix]videos set server_group_id=? where video_id=?", intval($server_group_id), $video_id);
			}
		}

		// copy video files from conversion server
		if ($task_data['server_id'] > 0)
		{
			if (is_array($result_data['video_files']) && count($result_data['video_files']) > 0)
			{
				log_output("INFO  Copying video files from conversion server [PH-F-1]", $video_id);
				foreach ($result_data['video_files'] as $file)
				{
					if (!get_file($file, "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data, true))
					{
						cancel_task(2, "Failed to get $file file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}
			}
		}

		// copy merged source file if there were several sources
		$sources_count = intval(@count($task_data['data']['sources']));
		if ($sources_count > 0)
		{
			log_output("INFO  Copying merged source file from conversion server [PH-F-2]", $video_id);

			if (!get_file("$video_id.tmp", "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data, true))
			{
				cancel_task(2, "Failed to get $video_id.tmp file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			$duration = intval($task_data['data']['duration']);
			if ($duration == 0)
			{
				$duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp");
			}
			$dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp");
			$filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"));

			sql_update("update $config[tables_prefix]videos set duration=?, file_dimensions=?, file_size=? where video_id=?", $duration, "$dimensions[0]x$dimensions[1]", $filesize, $video_id);
			foreach ($task_data['data']['sources'] as $source_file)
			{
				if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
				{
					log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$source_file", $video_id);
				}
			}
		}

		mark_task_progress($task_data['task_id'], 80);

		// process all video formats
		$video_files_completed = $result_data['video_files_completed'];
		$video_formats = [];
		if (count($formats_videos) > 0)
		{
			$duration = 0;
			$duration_title = '';
			$invalidate_files = [];
			foreach ($formats_videos as $format)
			{
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}") || (is_array($video_files_completed) && is_array($video_files_completed[$format['postfix']])))
				{
					log_output("INFO  Finalizing processing for video format \"$format[title]\" [PH-F-3:$format[title]]", $video_id);
					if (is_array($video_files_completed) && is_array($video_files_completed[$format['postfix']]))
					{
						$video_dimension = $video_files_completed[$format['postfix']]['dimensions'];
						$video_duration = $video_files_completed[$format['postfix']]['duration'];
						$video_size = $video_files_completed[$format['postfix']]['size'];
					} else
					{
						foreach ($storage_servers as $server)
						{
							log_output("INFO  ....Copying video file to \"$server[title]\" [PH-F-3-1:$server[title]]", $video_id);
							if (!put_file("$video_id{$format['postfix']}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$dir_path/$video_id", $server))
							{
								cancel_task(4, "Failed to put $video_id{$format['postfix']} file to storage server \"$server[title]\", cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}
						$video_dimension = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}");
						$video_duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}");
						$video_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}"));
						log_output("INFO  ....Copied format file to storage: $video_id{$format['postfix']}, $video_dimension[0]x$video_dimension[1], $video_duration sec, $video_size bytes", $video_id);
					}
					$invalidate_files[] = "$dir_path/$video_id/$video_id{$format['postfix']}";

					$timeline_screenshots_count = 0;
					$timeline_screenshots_interval = 0;
					if (is_array($result_data['timeline_screenshots_count']))
					{
						$timeline_screenshots_count = intval($result_data['timeline_screenshots_count'][$format['postfix']]);
					}
					if (is_array($result_data['timeline_screenshots_interval']))
					{
						$timeline_screenshots_interval = intval($result_data['timeline_screenshots_interval'][$format['postfix']]);
					}

					$new_format = [];
					$new_format['postfix'] = $format['postfix'];
					$new_format['dimensions'] = $video_dimension;
					$new_format['duration'] = $video_duration;
					$new_format['file_size'] = $video_size;
					$new_format['timeline_screen_amount'] = $timeline_screenshots_count;
					$new_format['timeline_screen_interval'] = $timeline_screenshots_interval;
					$video_formats[] = $new_format;

					if ($timeline_screenshots_count > 0)
					{
						if ($timeline_screenshots_interval == 0)
						{
							cancel_task(3, "Conversion server API should be updated", $video_id, $task_data['task_id'], $server_data);
							return false;
						}

						log_output("INFO  ....Copying timeline screenshots from conversion server [PH-F-3-2]", $video_id);

						$timeline_dir = $format['timeline_directory'];
						$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
						if (!mkdir_recursive($screenshots_source_dir))
						{
							cancel_task(5, "Failed to create directory $screenshots_source_dir", $video_id, $task_data['task_id'], $server_data);
							return false;
						}

						// copy timeline screenshot sources from conversion server
						for ($i = 1; $i <= $timeline_screenshots_count; $i++)
						{
							if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines", $screenshots_source_dir, $server_data, true))
							{
								cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
							rename("$screenshots_source_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_source_dir/$i.jpg");
						}

						// copy timeline screenshot formats from conversion server
						foreach ($formats_screenshots as $format_scr)
						{
							if ($format_scr['group_id'] == 2)
							{
								$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format_scr[size]";
								if (!mkdir_recursive($screenshots_target_dir))
								{
									cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
									return false;
								}

								log_output("INFO  ....Copying timeline screenshots from conversion server for \"$format_scr[title]\" format", $video_id);
								for ($i = 1; $i <= $timeline_screenshots_count; $i++)
								{
									if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines/$format_scr[size]", $screenshots_target_dir, $server_data, true))
									{
										cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
										return false;
									}
									rename("$screenshots_target_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_target_dir/$i.jpg");
								}

								if ($format_scr['is_create_zip'] == 1)
								{
									log_output("INFO  ....Creating timeline screenshots ZIP for \"$format_scr[title]\" format [PH-F-3-3:$format_scr[title]]", $video_id);
									$zip_files_to_add = [];
									for ($i = 1; $i <= $timeline_screenshots_count; $i++)
									{
										$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
									}
									$zip = new PclZip("$screenshots_target_dir/$video_id-$format_scr[size].zip");
									$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
								}
							}
						}
						log_output("INFO  ....Saved $timeline_screenshots_count timeline screenshots", $video_id);
					}

					if (($res_video['is_private'] == 0 || $res_video['is_private'] == 1) && $format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_STD'])
					{
						$duration = $video_duration;
						$duration_title = $format['title'];
					} elseif ($res_video['is_private'] == 2 && $format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM'])
					{
						$duration = $video_duration;
						$duration_title = $format['title'];
					}
				}
			}
			if ($duration > 0)
			{
				log_output("INFO  Video duration ($duration) is taken from format file \"$duration_title\"", $video_id);
				sql_update("update $config[tables_prefix]videos set duration=? where video_id=?", $duration, $video_id);
			}

			foreach ($storage_servers as $server)
			{
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, ["$dir_path/$video_id"], $invalidate_files, "add");
				}
			}
		}

		mark_task_progress($task_data['task_id'], 90);

		// process overview screenshots
		log_output("INFO  Finalizing processing for overview screenshots [PH-F-4]", $video_id);

		$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
		if (!mkdir_recursive($screenshots_source_dir))
		{
			cancel_task(5, "Failed to create directory $screenshots_source_dir", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		$screenshots_count = 0;
		if ($task_data['server_id'] > 0)
		{
			log_output("INFO  ....Copying overview screenshots from conversion server", $video_id);

			// copy overview screenshot sources from conversion server
			$screenshots_count = count($result_data['screenshots_data']);
			for ($i = 1; $i <= $screenshots_count; $i++)
			{
				if (!get_file("$i.jpg", "$task_data[task_id]/screenshots", $screenshots_source_dir, $server_data, true))
				{
					cancel_task(2, "Failed to get $i.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}

			// copy overview screenshot formats from conversion server
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 1)
				{
					$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
					if (!mkdir_recursive($screenshots_target_dir))
					{
						cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					log_output("INFO  ....Copying screenshots from conversion server for \"$format[title]\" format", $video_id);
					for ($i = 1; $i <= $screenshots_count; $i++)
					{
						if (!get_file("$i.jpg", "$task_data[task_id]/screenshots/$format[size]", $screenshots_target_dir, $server_data, true))
						{
							cancel_task(2, "Failed to get $i.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
				}
			}

			file_put_contents("$screenshots_source_dir/info.dat", serialize($result_data['screenshots_data'] ?? []), LOCK_EX);
		} else
		{
			log_output("INFO  ....Processing uploaded overview screenshots", $video_id);
			if ($options['SCREENSHOTS_UPLOADED_CROP'] == 1)
			{
				log_output("INFO  ....Applying crop for uploaded screenshots", $video_id);
			}

			$screenshots_data = [];

			// process uploaded overview screenshots
			if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
			{
				$screen_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", 1);
				for ($i = 1; $i <= count($screen_files); $i++)
				{
					copy("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot{$i}.jpg", "$screenshots_source_dir/$i.jpg");
					$exec_res = process_screen_source("$screenshots_source_dir/$i.jpg", $options, true, $custom_crop_options);
					if ($exec_res)
					{
						log_output("ERROR IM operation failed: $exec_res", $video_id);
						cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					$screenshots_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$screenshots_source_dir/$i.jpg")];
				}
				$screenshots_count = count($screen_files);
			}
			file_put_contents("$screenshots_source_dir/info.dat", serialize($screenshots_data), LOCK_EX);

			// create all overview formats
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 1)
				{
					$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
					if (!mkdir_recursive($screenshots_target_dir))
					{
						cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
					log_output("INFO  ....Creating screenshots for \"$format[title]\" format", $video_id);
					for ($i = 1; $i <= $screenshots_count; $i++)
					{
						$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $format, $options, true);
						if ($exec_res)
						{
							log_output("ERROR IM operation failed: $exec_res", $video_id);
							cancel_task(8, "Error during screenshots creation for \"$format[title]\" format, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
				}
			}
		}

		$screen_main = intval($options['SCREENSHOTS_MAIN_NUMBER']);
		if (intval($task_data['data']['screen_main']) > 1)
		{
			$screen_main = intval($task_data['data']['screen_main']);
			if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
			{
				$screen_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots", 1);
				if (count($screen_files) == 1)
				{
					$screen_main = 1;
				}
			}
		} elseif (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots"))
		{
			$screen_main = 1;
		}

		if ($screenshots_count == 0)
		{
			cancel_task(8, "No overview screenshots created, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		if ($screen_main < 1 || $screen_main > $screenshots_count)
		{
			$screen_main = 1;
		}

		log_output("INFO  ....Main screenshot is set to $screen_main", $video_id);
		log_output("INFO  ....Saved $screenshots_count screenshots", $video_id);

		// create ZIP files for overview screenshots
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 1)
			{
				$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
				if ($format['is_create_zip'] == 1)
				{
					log_output("INFO  ....Creating screenshots ZIP for \"$format[title]\" format [PH-F-4-1:$format[title]]", $video_id);
					$zip_files_to_add = [];
					for ($i = 1; $i <= $screenshots_count; $i++)
					{
						$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
					}
					$zip = new PclZip("$screenshots_target_dir/$video_id-$format[size].zip");
					$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
				}
			}
		}

		// create preview files
		log_output("INFO  ....Creating player preview files [PH-F-4-2]", $video_id);
		copy("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$screen_main.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg");
		foreach ($video_formats as $format)
		{
			resize_image('need_size_no_composite', "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
		}

		// process posters
		$posters_count = 0;
		$poster_main = 0;
		if (is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters"))
		{
			log_output("INFO  Finalizing processing for posters [PH-F-5]", $video_id);

			$posters_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/posters";
			if (!mkdir_recursive($posters_source_dir))
			{
				cancel_task(5, "Failed to create directory $posters_source_dir", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			$posters_data = [];

			$poster_files = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters", 1);
			for ($i = 1; $i <= count($poster_files); $i++)
			{
				copy("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters/poster{$i}.jpg", "$posters_source_dir/$i.jpg");

				$posters_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$posters_source_dir/$i.jpg")];
			}
			$posters_count = count($poster_files);
			file_put_contents("$posters_source_dir/info.dat", serialize($posters_data), LOCK_EX);

			if ($task_data['server_id'] > 0)
			{
				log_output("INFO  ....Copying posters from conversion server", $video_id);

				// copy poster formats from conversion server
				foreach ($formats_screenshots as $format)
				{
					if ($format['group_id'] == 3)
					{
						$posters_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]";
						if (!mkdir_recursive($posters_target_dir))
						{
							cancel_task(5, "Failed to create directory $posters_target_dir", $video_id, $task_data['task_id'], $server_data);
							return false;
						}

						log_output("INFO  ....Copying posters from conversion server for \"$format[title]\" format", $video_id);
						for ($i = 1; $i <= $posters_count; $i++)
						{
							if (!get_file("$i.jpg", "$task_data[task_id]/posters/$format[size]", $posters_target_dir, $server_data, true))
							{
								cancel_task(2, "Failed to get $i.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}
					}
				}

				file_put_contents("$posters_source_dir/info.dat", serialize($result_data['posters_data'] ?? []), LOCK_EX);
			} else
			{
				log_output("INFO  ....Processing uploaded posters", $video_id);

				// create all overview formats
				foreach ($formats_screenshots as $format)
				{
					if ($format['group_id'] == 3)
					{
						$posters_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]";
						if (!mkdir_recursive($posters_target_dir))
						{
							cancel_task(5, "Failed to create directory $posters_target_dir", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						log_output("INFO  ....Creating posters for \"$format[title]\" format", $video_id);
						for ($i = 1; $i <= $posters_count; $i++)
						{
							$exec_res = make_screen_from_source("$posters_source_dir/$i.jpg", "$posters_target_dir/$i.jpg", $format, $options, false);
							if ($exec_res)
							{
								log_output("ERROR IM operation failed: $exec_res", $video_id);
								cancel_task(8, "Error during posters creation for \"$format[title]\" format, cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
						}
					}
				}
			}

			$poster_main = 1;
			if (intval($task_data['data']['poster_main']) > 1)
			{
				$poster_main = intval($task_data['data']['poster_main']);
			}
			if ($poster_main < 1 || $poster_main > $posters_count)
			{
				$poster_main = 1;
			}

			log_output("INFO  ....Main poster is set to $poster_main", $video_id);
			log_output("INFO  ....Saved $posters_count posters", $video_id);

			// create ZIP files for posters
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 3)
				{
					$posters_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$format[size]";
					if ($format['is_create_zip'] == 1)
					{
						log_output("INFO  ....Creating posters ZIP for \"$format[title]\" format [PH-F-5-1:$format[title]]", $video_id);
						$zip_files_to_add = [];
						for ($i = 1; $i <= $posters_count; $i++)
						{
							$zip_files_to_add[] = "$posters_target_dir/$i.jpg";
						}
						$zip = new PclZip("$posters_target_dir/$video_id-$format[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $posters_target_dir);
					}
				}
			}
		}

		// check HD quality
		$video_is_hd = 0;
		if (count($video_formats) > 0)
		{
			foreach ($video_formats as $format)
			{
				if ($format['dimensions'][1] >= 700)
				{
					$video_is_hd = 1;
					break;
				}
			}
		} else
		{
			$video_dimension = explode('x', $res_video['file_dimensions']);
			if ($video_dimension[1] >= 700)
			{
				$video_is_hd = 1;
			}
		}
		if ($video_is_hd == 1)
		{
			log_output("INFO  Video is considered as HD quality", $video_id);
		}

		// check title duplicates
		if ($options['VIDEOS_DUPLICATE_TITLE_OPTION'] == 1 && $res_video['title'])
		{
			$video_title = $res_video['title'];
			$titles = mr2array_list(sql_pr("select title from $config[tables_prefix]videos where video_id!=? and title like ? and status_id in (0,1)", $video_id, "$res_video[title]%"));
			if (in_array($video_title, $titles))
			{
				for ($i = 2; $i < 999; $i++)
				{
					if (!in_array("$video_title " . str_replace("%NUM%", $i, $options['VIDEOS_DUPLICATE_TITLE_POSTFIX']), $titles))
					{
						$video_title = "$video_title " . str_replace("%NUM%", $i, $options['VIDEOS_DUPLICATE_TITLE_POSTFIX']);
						sql_update("update $config[tables_prefix]videos set title=? where video_id=?", $video_title, $video_id);
						log_output("INFO  Replaced video title with \"$video_title\"", $video_id);
						break;
					}
				}
			}
		}

		// check status
		$video_status_id = intval($task_data['data']['status_id']);
		if ($video_status_id == 1 && !trim($res_video['title']))
		{
			log_output("WARN  Video cannot be activated with empty title", $video_id);
			$video_status_id = 0;
		}

		// update video
		sql_update("update $config[tables_prefix]videos set status_id=?, is_hd=?, file_formats=?, screen_amount=?, screen_main=?, poster_amount=?, poster_main=? where video_id=?", $video_status_id, $video_is_hd, pack_video_formats($video_formats), $screenshots_count, $screen_main, $posters_count, $poster_main, $video_id);
		sql_update("update $config[tables_prefix]users set
						public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_videos_count=public_videos_count+private_videos_count+premium_videos_count
					where user_id=?", $res_video['user_id']
		);

		if ($video_status_id == 1)
		{
			$memberzone_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if (intval($memberzone_data['AWARDS_VIDEO_UPLOAD']) > 0 && $res_video['duration'] >= intval($memberzone_data['AWARDS_VIDEO_UPLOAD_CONDITION']))
			{
				$anonymous_user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
				if ($res_video['user_id'] != $anonymous_user_id)
				{
					sql_insert("insert into $config[tables_prefix]log_awards_users set award_type=4, user_id=?, video_id=?, tokens_granted=?, added_date=?", $res_video['user_id'], $video_id, intval($memberzone_data['AWARDS_VIDEO_UPLOAD']), date('Y-m-d H:i:s'));
					sql_update("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", intval($memberzone_data['AWARDS_VIDEO_UPLOAD']), $res_video['user_id']);
				}
			}
		}

		// hook plugins
		log_output("INFO  Post-processing [PH-F-6]", $video_id);
		foreach ($plugins_on_new as $plugin)
		{
			log_output("INFO  ....Executing $plugin plugin [PH-F-6-1:$plugin]", $video_id);
			unset($res);
			exec("$config[php_path] $config[project_path]/admin/plugins/$plugin/$plugin.php exec video $video_id 2>&1", $res);
			if ($res[0])
			{
				log_output("...." . implode("\n....", $res), $video_id, 1);
			} else
			{
				log_output("....no response", $video_id, 1);
			}
		}

		// delete task on conversion server
		delete_task_folder($task_data['task_id'], $server_data);

		// delete uploaded screenshots
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters");
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots");
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp");

		// delete uploaded format files
		if (count($formats_videos) > 0)
		{
			foreach ($formats_videos as $format)
			{
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}"))
				{
					if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}"))
					{
						log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$format['postfix']}", $video_id);
					}
				}
			}
		}

		// create format tasks if semi-processing is enabled
		$submitted_phase_two_tasks = false;
		if ($options['VIDEOS_HALF_PROCESSING'] == 1 && is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
		{
			if (count($formats_videos) > 0)
			{
				foreach ($formats_videos as $format)
				{
					if ($format['status_id'] == 2 && $format['is_conditional'] == 1)
					{
						$has_format_created = false;
						foreach ($video_formats as $format_rec)
						{
							if ($format['postfix'] == $format_rec['postfix'])
							{
								$has_format_created = true;
								break;
							}
						}
						if (!$has_format_created)
						{
							sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, priority=?, added_date=?", $video_id, serialize(['format_postfix' => $format['postfix']]), intval($task_data['priority']), date('Y-m-d H:i:s'));
							log_output("INFO  Format \"$format[title]\" creation task submitted", $video_id);
							$submitted_phase_two_tasks = true;
						}
					}
				}
			}
		}

		// delete source file if necessary
		if (intval($options['KEEP_VIDEO_SOURCE_FILES']) == 0)
		{
			if (!$submitted_phase_two_tasks)
			{
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
				{
					log_output("INFO  Deleted video source file, source files are not saved", $video_id);
					if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
					{
						log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp", $video_id);
					}
				}
			} else
			{
				sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=6, video_id=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 30 MINUTE)", $video_id, date('Y-m-d H:i:s'));
			}
		} else
		{
			log_output("INFO  Keeping source file, source files are saved", $video_id);
		}

		// update categorization
		$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_videos where video_id=?", $video_id)));
		$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_videos where video_id=?", $video_id)));
		$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_videos where video_id=?", $video_id)));
		update_categories_videos_totals($list_ids_categories);
		update_models_videos_totals($list_ids_models);
		update_tags_videos_totals($list_ids_tags);
		update_content_sources_videos_totals([$res_video['content_source_id']]);
		update_dvds_videos_totals([$res_video['dvd_id']]);

		log_output("INFO  New video creation task is completed for video $video_id [PH-FE]", $video_id);
		finish_task($task_data, time() - $task_start_time + intval($task_conversion_duration));
	}

	return false;
}

function exec_delete_video($task_data)
{
	global $config, $conversion_servers;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
	foreach ($storage_servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	$is_soft_delete = intval($task_data['data']['soft_delete']);

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Video removal task is started for video $video_id [PH-P]", $video_id);
	if ($is_soft_delete == 1)
	{
		log_output("INFO  Video is only marked as deleted", $video_id);
	}

	$dir_path = get_dir_by_id($video_id);
	$formats = get_video_formats($video_id, $res_video['file_formats']);
	foreach ($storage_servers as $server)
	{
		if (!delete_dir("$dir_path/$video_id", $server))
		{
			log_output("WARN  Failed to delete directory $dir_path/$video_id on storage server \"$server[title]\"", $video_id);
		}
		if ($server['streaming_type_id'] == 4) // CDN
		{
			$invalidate_files = [];
			foreach ($formats as $format_rec)
			{
				$invalidate_files[] = "$dir_path/$video_id/$video_id{$format_rec['postfix']}";
			}
			cdn_invalidate_video($video_id, $server, ["$dir_path/$video_id"], $invalidate_files, 'delete');
		}
	}

	$folders = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/timelines", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$folder");
	}

	$folders = get_contents_from_dir("$config[content_path_videos_sources]/$dir_path/$video_id/temp", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/$folder");
	}

	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines");
	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/posters");
	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots");
	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots");
	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp");
	rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id");

	$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines", 2);
	foreach ($folders as $folder)
	{
		$folders_inner = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$folder", 2);
		foreach ($folders_inner as $folder_inner)
		{
			rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$folder/$folder_inner");
		}
		rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$folder");
	}

	$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$folder");
	}

	$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/$folder");
	}

	rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id");

	$list_ids_comments_str = implode(",", array_map("intval", mr2array_list(sql_pr("select distinct user_id from $config[tables_prefix]comments where object_id=? and object_type_id=1", $video_id))));
	$list_ids_playlists_str = implode(",", array_map("intval", mr2array_list(sql_pr("select distinct playlist_id from $config[tables_prefix]fav_videos where video_id=?", $video_id))));

	$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_videos where video_id=?", $video_id)));
	$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_videos where video_id=?", $video_id)));
	$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_videos where video_id=?", $video_id)));

	sql_delete("delete from $config[tables_prefix]stats_videos where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]users_events where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]fav_videos where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]rating_history where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]flags_videos where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]flags_history where video_id=?", $video_id);
	sql_delete("delete from $config[tables_prefix]flags_messages where video_id=?", $video_id);

	sql_update("update $config[tables_prefix]users_purchases set expiry_date=? where video_id=?", date('Y-m-d H:i:s'), $video_id);

	sql_update("update $config[tables_prefix]albums set connected_video_id=0 where connected_video_id=?", $video_id);
	sql_update("update $config[tables_prefix]posts set connected_video_id=0 where connected_video_id=?", $video_id);

	sql_update("update $config[tables_prefix]users set
					public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_videos_count=public_videos_count+private_videos_count+premium_videos_count
				where user_id=?", $res_video['user_id']
	);

	if ($is_soft_delete == 0)
	{
		sql_delete("delete from $config[tables_prefix]videos where video_id=?", $video_id);
		sql_delete("delete from $config[tables_prefix]comments where object_id=? and object_type_id=1", $video_id);
		sql_delete("delete from $config[tables_prefix]categories_videos where video_id=?", $video_id);
		sql_delete("delete from $config[tables_prefix]models_videos where video_id=?", $video_id);
		sql_delete("delete from $config[tables_prefix]tags_videos where video_id=?", $video_id);
	} else
	{
		sql_update("update $config[tables_prefix]videos set file_formats='', favourites_count=0, purchases_count=0, screen_amount=0, poster_amount=0, server_group_id=0, admin_user_id=0, admin_flag_id=0, has_errors=0 where video_id=?", $video_id);
	}

	if ($list_ids_comments_str)
	{
		sql_update("update $config[tables_prefix]users set
						comments_videos_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=1),
						comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
					where user_id in ($list_ids_comments_str)");
	}
	if ($list_ids_playlists_str)
	{
		sql_update("update $config[tables_prefix]playlists set total_videos=(select count(*) from $config[tables_prefix]fav_videos where $config[tables_prefix]playlists.playlist_id=$config[tables_prefix]fav_videos.playlist_id) where playlist_id in ($list_ids_playlists_str)");
	}
	update_categories_videos_totals($list_ids_categories);
	update_models_videos_totals($list_ids_models);
	update_tags_videos_totals($list_ids_tags);
	update_content_sources_videos_totals([$res_video['content_source_id']]);
	update_dvds_videos_totals([$res_video['dvd_id']]);

	$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
	$video_url = '';
	if ($res_video['dir'])
	{
		$video_url = "$config[project_url]/" . str_replace("%ID%", $video_id, str_replace("%DIR%", $res_video['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
	}
	sql_insert("insert into $config[tables_prefix]deleted_content set object_id=?, object_type_id=1, dir=?, url=?, external_key=?, file_key=?, deleted_date=?", $video_id, trim($res_video['dir']), $video_url, trim($res_video['external_key']), trim($res_video['file_key']), date('Y-m-d H:i:s'));

	inc_block_version_admin('videos_info', 'video', $res_video['video_id'], $res_video['dir']);

	$running_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where video_id=? and server_id>0", $video_id));
	foreach ($running_tasks as $running_task)
	{
		if (isset($conversion_servers[$running_task['server_id']]))
		{
			delete_task_folder($running_task['task_id'], $conversion_servers[$running_task['server_id']]);
		}
		@unlink("$config[project_path]/admin/data/engine/tasks/{$running_task['task_id']}.dat");
		@unlink("$config[project_path]/admin/data/engine/tasks/{$running_task['task_id']}_duration.dat");
	}

	log_output("INFO  Video removal task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);

	sql_delete("delete from $config[tables_prefix]background_tasks where video_id=?", $video_id);
	return false;
}

function exec_upload_video_file($task_data,$server_data,$formats_screenshots)
{
	global $config, $options, $source_download_base_url;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}
	if ($res_video['load_type_id'] != 1)
	{
		cancel_task(1, "Video $video_id has load type $res_video[load_type_id], cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}
	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}

	$dir_path = get_dir_by_id($video_id);
	if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		cancel_task(5, "Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id", 0, $task_data['task_id'], $server_data);
		return false;
	}
	$server_data = mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($server_data['server_id'])));

	$timeline_screenshots_count = 0;
	$timeline_screenshots_interval = 0;

	if ($task_data['status_id'] == 0)
	{
		log_output('', $video_id, 1, 1);
		log_output("INFO  Video file \"$res_format[title]\" uploading task is started for video $video_id [PH-P]", $video_id);

		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
		{
			cancel_task(9, "Video file $video_id{$postfix} not found in source directory", $video_id, $task_data['task_id'], $server_data);
			return false;
		} else
		{
			if (is_video_secure("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
			{
				unset($res);
				exec("$config[ffmpeg_path] -i \"$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}\"  2>&1", $res);
				$video_info = implode("\n....", $res);
				if (strpos($video_info, 'Input #0') !== false)
				{
					$video_info = substr($video_info, strpos($video_info, 'Input #0'));
				}

				log_output("INFO  Video file information:", $video_id);
				log_output("...." . $video_info, $video_id, 1);
			}
		}
	}

	$video_files_completed = null;
	$task_conversion_duration = 0;
	if ($res_format['is_timeline_enabled'] == 1 || strpos($res_format['postfix'], ".mp4") !== false)
	{
		// task requires conversion server
		if ($task_data['status_id'] == 0)
		{
			if (!isset($server_data))
			{
				warn_task("Conversion server is not available in the database, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
				return false;
			}

			$has_local_storage = false;
			$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
			foreach ($storage_servers as $server)
			{
				if ($server['connection_type_id'] != 2)
				{
					$has_local_storage = true;
				}
				if (!test_connection_status($server))
				{
					warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
				if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
				{
					warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
			}

			log_output("INFO  Preparing task for conversion server [PH-P-2]", $video_id);

			$task_info = [];
			$task_info['video_id'] = $video_id;
			$task_info['options']['PROCESS_PRIORITY'] = $options['GLOBAL_CONVERTATION_PRIORITY'];
			$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY'] = $config['imagemagick_default_jpeg_quality'];
			if ($server_data['connection_type_id'] == 0)
			{
				$task_info['source_dir'] = "$config[content_path_videos_sources]/$dir_path/$video_id";
			}

			if ($config['installation_type'] >= 3)
			{
				$task_info['options']['PROCESS_PRIORITY'] = intval($server_data['process_priority']);
			}
			log_output("INFO  Conversion priority level is set to " . $task_info['options']['PROCESS_PRIORITY'], $video_id);

			if ($server_data['option_storage_servers'] == 1)
			{
				if ($has_local_storage && $server_data['connection_type_id'] != 0)
				{
					log_output("WARN  Remote conversion server cannot be used to copy content to local storage", $video_id);
				} else
				{
					$task_info['storage_servers'] = $storage_servers;
				}
			}

			$formats_to_make_timelines = [];
			if ($res_format['is_timeline_enabled'] == 1)
			{
				$formats_to_make_timelines = [$res_format];
				$task_info['timelines_creation_list'] = $formats_to_make_timelines;

				$task_info['formats_screenshots'] = $formats_screenshots;
				foreach ($formats_screenshots as $format)
				{
					if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png"))
					{
						if (!put_file("watermark_screen_{$format['format_screenshot_id']}.png", "$config[project_path]/admin/data/other", "$task_data[task_id]", $server_data))
						{
							cancel_task(2, "Failed to put watermark_screen_{$format['format_screenshot_id']}.png file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
					}
				}
			}
			log_output("INFO  Timeline screenshots will be created for video formats: " . print_formats_list($formats_to_make_timelines), $video_id);

			$formats_to_postprocess = [];
			if (strpos($res_format['postfix'], ".mp4") !== false)
			{
				$formats_to_postprocess = [$res_format];
				$task_info['videos_post_process_list'] = $formats_to_postprocess;
			}
			log_output("INFO  Video files will be post-processed: " . print_formats_list($formats_to_postprocess), $video_id);

			if ($server_data['connection_type_id'] != 0)
			{
				if ($server_data['option_pull_source_files'] == 1)
				{
					$hash = md5($config['cv'] . "$dir_path/$video_id/$video_id{$postfix}");
					$task_info['download_urls']["$video_id{$postfix}"] = [
							'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$video_id{$postfix}/",
							'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
					];
				} else
				{
					log_output("INFO  Copying format file $video_id{$postfix} to conversion server [PH-P-2-2]", $video_id);
					if (!put_file("$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
					{
						cancel_task(2, "Failed to put $video_id{$postfix} file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}
			}

			file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat", serialize($task_info), LOCK_EX);
			if (!put_file('task.dat', "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
			{
				cancel_task(2, "Failed to put task.dat file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			unlink("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat");

			log_output("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]", $video_id);
			sql_update("update $config[tables_prefix]background_tasks set status_id=1, server_id=? where task_id=?", $server_data['server_id'], $task_data['task_id']);

			mark_task_progress($task_data['task_id'], 10);
			mark_task_duration($task_data['task_id'], time() - $task_start_time);
			return true;
		} else
		{
			if (!isset($server_data))
			{
				cancel_task(1, "Conversion server $task_data[server_id] is not available in the database, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			if (check_file('progress.dat', "$task_data[task_id]", $server_data))
			{
				get_file('progress.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data);
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat"))
				{
					mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat")) * 0.7));
					unlink("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat");
				}
			}

			if (check_file('result.dat', "$task_data[task_id]", $server_data) == 0)
			{
				if (check_file('task.dat', "$task_data[task_id]", $server_data) > 0)
				{
					return false;
				} else
				{
					if (test_connection($server_data) === true)
					{
						cancel_task(2, "Task directory is not available on conversion server, cancelling this task", $video_id, $task_data['task_id']);
					} else
					{
						warn_task("Conversion server connection is lost, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					}
					return false;
				}
			}

			// check result file
			if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
			if (!is_array($result_data))
			{
				sleep(1);
				if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
				{
					cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
				if (!is_array($result_data))
				{
					cancel_task(6, "Unexpected error on conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
			$task_conversion_duration = intval($result_data['duration']);
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat");

			// check log file
			$conversion_log = '';
			if (check_file('log.txt', "$task_data[task_id]", $server_data) > 0)
			{
				if (!get_file('log.txt', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
				{
					cancel_task(2, "Failed to get log.txt file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}

				if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt")) > 10 * 1000 * 1000)
				{
					$conversion_log = 'Conversion log is more than 10mb';
				} else
				{
					$conversion_log = trim(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt"));
				}
			}
			if (!$conversion_log)
			{
				cancel_task(3, "No conversion log is available, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt");

			// check if conversion result contains any error
			if ($result_data['is_error'] == 1)
			{
				log_output('', $video_id);
				log_output($conversion_log, $video_id, 1);
				cancel_task($result_data['error_code'] ?: 7, $result_data['error_message'] ?: "Conversion error, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			$video_files_completed = $result_data['video_files_completed'];

			// check storage servers
			if (is_array($result_data['video_files']))
			{
				$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
				foreach ($storage_servers as $server)
				{
					if (!test_connection_status($server))
					{
						warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
						return false;
					}
					if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
					{
						warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
						return false;
					}
				}
			}

			mark_task_progress($task_data['task_id'], 80);

			// log conversion process
			log_output('', $video_id);
			log_output($conversion_log, $video_id, 1);

			log_output('', $video_id);
			log_output("INFO  Video file uploading task is continued for video $video_id [PH-F]", $video_id);

			// copy video files from conversion server
			if (is_array($result_data['video_files']) && count($result_data['video_files']) > 0)
			{
				log_output("INFO  Copying video files from conversion server [PH-F-1]", $video_id);
				foreach ($result_data['video_files'] as $file)
				{
					if (!get_file($file, "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data, true))
					{
						cancel_task(2, "Failed to get $file file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}
			}

			log_output("INFO  Finalizing processing for video format \"$res_format[title]\" [PH-F-3:$res_format[title]]", $video_id);

			// clean up timelines folders if they are not empty
			$timeline_dir = $res_format['timeline_directory'];
			if ($timeline_dir)
			{
				rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir");
				$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir", 2);
				foreach ($folders as $folder)
				{
					rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$folder");
				}
				@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json");
			}

			// create timeline screenshots
			if (is_array($result_data['timeline_screenshots_count']))
			{
				$timeline_screenshots_count = intval($result_data['timeline_screenshots_count'][$postfix]);
			}
			if (is_array($result_data['timeline_screenshots_interval']))
			{
				$timeline_screenshots_interval = intval($result_data['timeline_screenshots_interval'][$postfix]);
			}
			if ($timeline_screenshots_count > 0)
			{
				if ($timeline_screenshots_interval == 0)
				{
					cancel_task(3, "Conversion server API should be updated", $video_id, $task_data['task_id'], $server_data);
					return false;
				}

				log_output("INFO  ....Copying timeline screenshots from conversion server [PH-F-3-2]", $video_id);

				$timeline_dir = $res_format['timeline_directory'];
				$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
				if (!mkdir_recursive($screenshots_source_dir))
				{
					cancel_task(5, "Failed to create directory $screenshots_source_dir", $video_id, $task_data['task_id'], $server_data);
					return false;
				}

				// copy timeline screenshot sources from conversion server
				for ($i = 1; $i <= $timeline_screenshots_count; $i++)
				{
					if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines", $screenshots_source_dir, $server_data, true))
					{
						cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
					rename("$screenshots_source_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_source_dir/$i.jpg");
				}

				// copy timeline screenshot formats from conversion server
				foreach ($formats_screenshots as $format_scr)
				{
					if ($format_scr['group_id'] == 2)
					{
						$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format_scr[size]";
						if (!mkdir_recursive($screenshots_target_dir))
						{
							cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
							return false;
						}

						log_output("INFO  ....Copying timeline screenshots from conversion server for \"$format_scr[title]\" format", $video_id);
						for ($i = 1; $i <= $timeline_screenshots_count; $i++)
						{
							if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines/$format_scr[size]", $screenshots_target_dir, $server_data, true))
							{
								cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
							rename("$screenshots_target_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_target_dir/$i.jpg");
						}

						if ($format_scr['is_create_zip'] == 1)
						{
							log_output("INFO  ....Creating timeline screenshots ZIP for \"$format_scr[title]\" format [PH-F-3-3:$format_scr[title]]", $video_id);
							$zip_files_to_add = [];
							for ($i = 1; $i <= $timeline_screenshots_count; $i++)
							{
								$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
							}
							$zip = new PclZip("$screenshots_target_dir/$video_id-$format_scr[size].zip");
							$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
						}
					}
				}
				log_output("INFO  ....Saved $timeline_screenshots_count timeline screenshots", $video_id);
			}
		}
	} else
	{
		log_output('', $video_id);
		log_output("INFO  Video file uploading task is continued for video $video_id [PH-F]", $video_id);
		log_output("INFO  Finalizing processing for video format \"$res_format[title]\" [PH-F-3:$res_format[title]]", $video_id);

		// check storage servers
		$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
		foreach ($storage_servers as $server)
		{
			if (!test_connection_status($server))
			{
				warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
				return false;
			}
			if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
			{
				warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
				return false;
			}
		}

		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	}

	mark_task_progress($task_data['task_id'], 90);

	$invalidate_operation = 'add';
	foreach (get_video_formats($video_id, $res_video['file_formats']) as $v)
	{
		if ($v['postfix'] == $postfix)
		{
			$invalidate_operation = 'change';
			break;
		}
	}

	if (is_array($video_files_completed) && is_array($video_files_completed[$postfix]))
	{
		// invalidate storage servers
		if (isset($storage_servers))
		{
			foreach ($storage_servers as $server)
			{
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], $invalidate_operation);
				}
			}
		}
		$video_dimension = $video_files_completed[$postfix]['dimensions'];
		$video_duration = $video_files_completed[$postfix]['duration'];
		$video_size = $video_files_completed[$postfix]['size'];
	} else
	{
		// copy video file to storage servers
		if (isset($storage_servers))
		{
			foreach ($storage_servers as $server)
			{
				log_output("INFO  ....Copying video file to \"$server[title]\" [PH-F-3-1:$server[title]]", $video_id);
				if (!put_file("$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$dir_path/$video_id", $server))
				{
					cancel_task(4, "Failed to put $video_id{$postfix} file to storage server \"$server[title]\", cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], $invalidate_operation);
				}
			}
		}
		$video_dimension = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
		$video_duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
		$video_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"));
		log_output("INFO  ....Copied format file to storage: $video_id{$postfix}, $video_dimension[0]x$video_dimension[1], $video_duration sec, $video_size bytes", $video_id);
	}

	// update video duration if needed
	$duration = 0;
	$duration_title = '';
	if (($res_video['is_private'] == 0 || $res_video['is_private'] == 1) && $res_format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_STD'])
	{
		$duration = $video_duration;
		$duration_title = $res_format['title'];
	} elseif ($res_video['is_private'] == 2 && $res_format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM'])
	{
		$duration = $video_duration;
		$duration_title = $res_format['title'];
	}
	if ($duration > 0)
	{
		log_output("INFO  Video duration ($duration) is updated from format file \"$duration_title\"", $video_id);
		sql_update("update $config[tables_prefix]videos set duration=? where video_id=?", $duration, $video_id);
	}

	// create preview file
	@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg");
	resize_image('need_size_no_composite', "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg", $video_dimension[0] . 'x' . $video_dimension[1]);

	// update video formats data
	$formats = get_video_formats($video_id, $res_video['file_formats']);
	$had_format = 0;
	foreach ($formats as $k => $v)
	{
		if ($v['postfix'] == $postfix)
		{
			$formats[$k]['dimensions'] = $video_dimension;
			$formats[$k]['duration'] = $video_duration;
			$formats[$k]['file_size'] = $video_size;
			$formats[$k]['timeline_screen_amount'] = $timeline_screenshots_count;
			$formats[$k]['timeline_screen_interval'] = $timeline_screenshots_interval;
			$formats[$k]['timeline_cuepoints'] = 0;
			$had_format = 1;
			break;
		}
	}
	if ($had_format == 0)
	{
		$new_format = [];
		$new_format['postfix'] = $postfix;
		$new_format['dimensions'] = $video_dimension;
		$new_format['duration'] = $video_duration;
		$new_format['file_size'] = $video_size;
		$new_format['timeline_screen_amount'] = $timeline_screenshots_count;
		$new_format['timeline_screen_interval'] = $timeline_screenshots_interval;
		$formats[] = $new_format;
	}

	// check HD quality
	$video_is_hd = 0;
	foreach ($formats as $k => $v)
	{
		if ($v['dimensions'][1] > 700)
		{
			$video_is_hd = 1;
			break;
		}
	}
	if ($res_video['is_hd'] != $video_is_hd)
	{
		if ($video_is_hd == 1)
		{
			log_output("INFO  Video is considered as HD quality", $video_id);
		} else
		{
			log_output("INFO  Video quality is lowered to non-HD", $video_id);
		}
	}

	sql_update("update $config[tables_prefix]videos set file_formats=?, is_hd=? where video_id=?", pack_video_formats($formats), $video_is_hd, $video_id);

	// clean up temp source files
	if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
	{
		log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $video_id);
	}

	// complete task
	delete_task_folder($task_data['task_id'], $server_data);

	log_output("INFO  Video file \"$res_format[title]\" uploading task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time + $task_conversion_duration);
	return false;
}

function exec_create_video_file($task_data,$server_data,$formats_videos,$formats_screenshots)
{
	global $config, $options, $source_download_base_url;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}
	if ($res_video['load_type_id'] != 1)
	{
		cancel_task(1, "Video $video_id has load type $res_video[load_type_id], cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}
	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}

	$dir_path = get_dir_by_id($video_id);
	if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id"))
	{
		cancel_task(5, "Failed to create directory $config[content_path_videos_sources]/$dir_path/$video_id", 0, $task_data['task_id'], $server_data);
		return false;
	}
	$server_data = mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($server_data['server_id'])));

	if ($task_data['status_id'] == 0)
	{
		if (!isset($server_data))
		{
			warn_task("Conversion server is not available in the database, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}

		$has_local_storage = false;
		$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
		foreach ($storage_servers as $server)
		{
			if ($server['connection_type_id'] != 2)
			{
				$has_local_storage = true;
			}
			if (!test_connection_status($server))
			{
				warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
				return false;
			}
			if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
			{
				warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
				return false;
			}
		}

		log_output('', $video_id);
		log_output("INFO  Video file \"$res_format[title]\" creation task is started for video $video_id [PH-P]", $video_id);
		log_output("INFO  Preparing task for conversion server [PH-P-2]", $video_id);

		$task_info = [];
		$task_info['video_id'] = $video_id;
		$task_info['options']['PROCESS_PRIORITY'] = $options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY'] = $config['imagemagick_default_jpeg_quality'];
		if ($server_data['connection_type_id'] == 0)
		{
			$task_info['source_dir'] = "$config[content_path_videos_sources]/$dir_path/$video_id";
		}

		if ($config['installation_type'] >= 3)
		{
			$task_info['options']['PROCESS_PRIORITY'] = intval($server_data['process_priority']);
		}
		log_output("INFO  Conversion priority level is set to " . $task_info['options']['PROCESS_PRIORITY'], $video_id);

		if ($server_data['option_storage_servers'] == 1)
		{
			if ($has_local_storage && $server_data['connection_type_id'] != 0)
			{
				log_output("WARN  Remote conversion server cannot be used to copy content to local storage", $video_id);
			} else
			{
				$task_info['storage_servers'] = $storage_servers;
			}
		}

		if ($res_format['customize_duration_id'] > 0 || $res_format['customize_offset_start_id'] > 0 || $res_format['customize_offset_end_id'] > 0)
		{
			if ($res_video['content_source_id'] > 0)
			{
				$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
				if ($res_format['customize_duration_id'] > 0 && intval($res_content_source["custom{$res_format['customize_duration_id']}"]) > 0)
				{
					$res_format['limit_total_duration'] = intval($res_content_source["custom{$res_format['customize_duration_id']}"]);
					$res_format['limit_total_duration_unit_id'] = 0;
					log_output("INFO  Format \"$res_format[title]\": duration $res_format[limit_total_duration] is taken from content source custom field #$res_format[customize_duration_id]", $video_id);
				}
				if ($res_format['customize_offset_start_id'] > 0 && intval($res_content_source["custom{$res_format['customize_offset_start_id']}"]) > 0)
				{
					$res_format['limit_offset_start'] = intval($res_content_source["custom{$res_format['customize_offset_start_id']}"]);
					$res_format['limit_offset_start_unit_id'] = 0;
					log_output("INFO  Format \"$res_format[title]\": start offset $res_format[limit_offset_start] is taken from content source custom field #$res_format[customize_offset_start_id]", $video_id);
				}
				if ($res_format['customize_offset_end_id'] > 0 && intval($res_content_source["custom{$res_format['customize_offset_end_id']}"]) > 0)
				{
					$res_format['limit_offset_end'] = intval($res_content_source["custom{$res_format['customize_offset_end_id']}"]);
					$res_format['limit_offset_end_unit_id'] = 0;
					log_output("INFO  Format \"$res_format[title]\": end offset $res_format[limit_offset_end] is taken from content source custom field #$res_format[customize_offset_end_id]", $video_id);
				}
			}
		}
		$formats_to_create = [$res_format];
		$task_info['videos_creation_list'] = $formats_to_create;
		log_output("INFO  Video formats will be created: " . print_formats_list($formats_to_create), $video_id);

		if (is_file("$config[project_path]/admin/include/kvs_filter_video.php"))
		{
			require_once "$config[project_path]/admin/include/kvs_filter_video.php";
			$filter_custom_function = 'kvs_filter_video';
			if (function_exists($filter_custom_function))
			{
				$custom_source_filter = $filter_custom_function($res_video);
				if ($custom_source_filter && is_array($custom_source_filter))
				{
					$task_info['source_filter'] = $custom_source_filter;
					log_output("INFO  Using custom source filter: " . print_object($custom_source_filter), $video_id);
				}
			}
		}

		$formats_to_make_timelines = [];
		if ($res_format['is_timeline_enabled'] == 1)
		{
			$formats_to_make_timelines = [$res_format];
			$task_info['timelines_creation_list'] = $formats_to_make_timelines;

			$task_info['formats_screenshots'] = $formats_screenshots;
			foreach ($formats_screenshots as $format)
			{
				if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png"))
				{
					if (!put_file("watermark_screen_{$format['format_screenshot_id']}.png", "$config[project_path]/admin/data/other", "$task_data[task_id]", $server_data))
					{
						cancel_task(2, "Failed to put watermark_screen_{$format['format_screenshot_id']}.png file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}
			}
		}
		log_output("INFO  Timeline screenshots will be created for video formats: " . print_formats_list($formats_to_make_timelines), $video_id);

		$source_file = "$video_id.tmp";
		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
		{
			$source_file = "$video_id.tmp2";
			if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
			{
				$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
				$video_formats = get_video_formats($video_id, $res_video['file_formats']);
				$max_filesize = 0;
				$max_postfix = '';
				foreach ($video_formats as $format_rec)
				{
					foreach ($formats_videos as $format)
					{
						if ($format_rec['postfix'] == $format['postfix'] && $format['is_use_as_source'] == 1)
						{
							$max_postfix = $format_rec['postfix'];
							break 2;
						}
					}
					if ($format_rec['file_size'] > $max_filesize)
					{
						$max_filesize = $format_rec['file_size'];
						$max_postfix = $format_rec['postfix'];
					}
				}

				if ($max_postfix)
				{
					log_output("INFO  Source file is not available, using \"$max_postfix\" file [PH-P-2-1]", $video_id);
					$file_copied = false;
					foreach ($storage_servers as $server)
					{
						if (get_file("$video_id{$max_postfix}", "$dir_path/$video_id", "$config[content_path_videos_sources]/$dir_path/$video_id", $server))
						{
							$file_copied = true;
							rename("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$max_postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
							break;
						}
					}
					if (!$file_copied)
					{
						foreach ($storage_servers as $server)
						{
							if (intval($server['streaming_type_id']) == 4)
							{
								log_output("WARN  Failed to get $video_id{$max_postfix} via server connection, trying to download it from CDN cache", $video_id);
								if (cdn_download_video_file($server, "$dir_path/$video_id/$video_id{$max_postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$source_file", $max_filesize))
								{
									break;
								}
							}
						}
					}
				}
			}
		}
		$duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
		$dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
		$filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"));

		if ($duration < 1)
		{
			cancel_task(9, "No source file found for creating new video file", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		$task_info['source_dimensions'] = $dimensions;
		log_output("INFO  Source video parameters are: duration - $duration sec, dimensions - $dimensions[0]x$dimensions[1], filesize - $filesize bytes", $video_id);

		$task_info['source_file'] = $source_file;
		if ($server_data['connection_type_id'] != 0)
		{
			if ($server_data['option_pull_source_files'] == 1)
			{
				$hash = md5($config['cv'] . "$dir_path/$video_id/$task_info[source_file]");
				$task_info['download_urls']["$task_info[source_file]"] = [
						'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$task_info[source_file]/",
						'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$task_info[source_file]"))
				];
			} else
			{
				log_output("INFO  Copying source file $task_info[source_file]to conversion server [PH-P-2-2]", $video_id);
				if (!put_file("$task_info[source_file]", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
				{
					cancel_task(2, "Failed to put $task_info[source_file] file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
		}

		if ($task_data['data']['skip_watermark'] != 1)
		{
			foreach ($formats_to_create as $format)
			{
				$format_watermark = '';
				$format_watermark_folder = '';
				if (is_file("$config[project_path]/admin/include/kvs_watermark_video.php"))
				{
					require_once "$config[project_path]/admin/include/kvs_watermark_video.php";
					$watermark_custom_function = 'kvs_watermark_video';
					if (function_exists($watermark_custom_function))
					{
						$temp_watermark_file = $watermark_custom_function($format['postfix'], $res_video);
						if ($temp_watermark_file && is_file($temp_watermark_file))
						{
							$rnd = mt_rand(1000000, 9999999);
							if (!mkdir_recursive("$config[temporary_path]/$rnd"))
							{
								cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
							rename($temp_watermark_file, "$config[temporary_path]/$rnd/watermark_video_{$format['format_video_id']}.png");
							$format_watermark = "watermark_video_{$format['format_video_id']}.png";
							$format_watermark_folder = "$config[temporary_path]/$rnd";
							log_output("INFO  Format \"$format[title]\": watermark image is generated by API function", $video_id);
						}
					}
				}
				if (!$format_watermark)
				{
					if ($format['customize_watermark_id'] > 0)
					{
						if ($res_video['content_source_id'] > 0)
						{
							$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
							if ($res_content_source["custom_file{$format['customize_watermark_id']}"])
							{
								if (is_file("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark_id']}"]))
								{
									$rnd = mt_rand(1000000, 9999999);
									if (!mkdir_recursive("$config[temporary_path]/$rnd"))
									{
										cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
										return false;
									}
									copy("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark_id']}"], "$config[temporary_path]/$rnd/watermark_video_{$format['format_video_id']}.png");
									$format_watermark = "watermark_video_{$format['format_video_id']}.png";
									$format_watermark_folder = "$config[temporary_path]/$rnd";
									log_output("INFO  Format \"$format[title]\": watermark image is taken from content source custom file field #$format[customize_watermark_id]", $video_id);
								}
							}
						}
					}
				}
				if (!$format_watermark)
				{
					if (is_file("$config[project_path]/admin/data/other/watermark_video_{$format['format_video_id']}.png"))
					{
						$format_watermark = "watermark_video_{$format['format_video_id']}.png";
						$format_watermark_folder = "$config[project_path]/admin/data/other";
						log_output("INFO  Format \"$format[title]\": watermark image is taken from video format", $video_id);
					}
				}
				if ($format_watermark && $format_watermark_folder)
				{
					if (!put_file($format_watermark, $format_watermark_folder, "$task_data[task_id]", $server_data))
					{
						cancel_task(2, "Failed to put $format_watermark file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}

				$format_watermark2 = '';
				$format_watermark2_folder = '';
				if (is_file("$config[project_path]/admin/include/kvs_watermark2_video.php"))
				{
					require_once "$config[project_path]/admin/include/kvs_watermark2_video.php";
					$watermark2_custom_function = 'kvs_watermark2_video';
					if (function_exists($watermark2_custom_function))
					{
						$temp_watermark2_file = $watermark2_custom_function($format['postfix'], $res_video);
						if ($temp_watermark2_file && is_file($temp_watermark2_file))
						{
							$rnd = mt_rand(1000000, 9999999);
							if (!mkdir_recursive("$config[temporary_path]/$rnd"))
							{
								cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
								return false;
							}
							rename($temp_watermark2_file, "$config[temporary_path]/$rnd/watermark2_video_{$format['format_video_id']}.png");
							$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
							$format_watermark2_folder = "$config[temporary_path]/$rnd";
							log_output("INFO  Format \"$format[title]\": watermark2 image is generated by API function", $video_id);
						}
					}
				}
				if (!$format_watermark2)
				{
					if ($format['customize_watermark2_id'] > 0)
					{
						if ($res_video['content_source_id'] > 0)
						{
							$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
							if ($res_content_source["custom_file{$format['customize_watermark2_id']}"])
							{
								if (is_file("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark2_id']}"]))
								{
									$rnd = mt_rand(1000000, 9999999);
									if (!mkdir_recursive("$config[temporary_path]/$rnd"))
									{
										cancel_task(5, "Failed to create directory $config[temporary_path]/$rnd", $video_id, $task_data['task_id'], $server_data);
										return false;
									}
									copy("$config[content_path_content_sources]/$res_video[content_source_id]/" . $res_content_source["custom_file{$format['customize_watermark2_id']}"], "$config[temporary_path]/$rnd/watermark2_video_{$format['format_video_id']}.png");
									$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
									$format_watermark2_folder = "$config[temporary_path]/$rnd";
									log_output("INFO  Format \"$format[title]\": watermark2 image is taken from content source custom file field #$format[customize_watermark2_id]", $video_id);
								}
							}
						}
					}
				}
				if (!$format_watermark2)
				{
					if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$format['format_video_id']}.png"))
					{
						$format_watermark2 = "watermark2_video_{$format['format_video_id']}.png";
						$format_watermark2_folder = "$config[project_path]/admin/data/other";
						log_output("INFO  Format \"$format[title]\": watermark2 image is taken from video format", $video_id);
					}
				}
				if ($format_watermark2 && $format_watermark2_folder)
				{
					if (!put_file($format_watermark2, $format_watermark2_folder, "$task_data[task_id]", $server_data))
					{
						cancel_task(2, "Failed to put $format_watermark2 file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
						return false;
					}
				}
			}
		} else
		{
			log_output("INFO  Format \"$res_format[title]\": watermark image is skipped", $video_id);
		}

		file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat", serialize($task_info), LOCK_EX);
		if (!put_file('task.dat', "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
		{
			cancel_task(2, "Failed to put task.dat file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		unlink("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat");

		log_output("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]", $video_id);
		sql_update("update $config[tables_prefix]background_tasks set status_id=1, server_id=? where task_id=?", $server_data['server_id'], $task_data['task_id']);

		mark_task_progress($task_data['task_id'], 10);
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		return true;
	} else
	{
		if (!isset($server_data))
		{
			cancel_task(1, "Conversion server $task_data[server_id] is not available in the database, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		if (check_file('progress.dat', "$task_data[task_id]", $server_data))
		{
			get_file('progress.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data);
			if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat"))
			{
				mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat")) * 0.7));
				unlink("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat");
			}
		}

		if (check_file('result.dat', "$task_data[task_id]", $server_data) == 0)
		{
			if (check_file('task.dat', "$task_data[task_id]", $server_data) > 0)
			{
				return false;
			} else
			{
				if (test_connection($server_data) === true)
				{
					cancel_task(2, "Task directory is not available on conversion server, cancelling this task", $video_id, $task_data['task_id']);
				} else
				{
					warn_task("Conversion server connection is lost, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
				}
				return false;
			}
		}

		// check result file
		if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
		{
			cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
		if (!is_array($result_data))
		{
			sleep(1);
			if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
			if (!is_array($result_data))
			{
				cancel_task(6, "Unexpected error on conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
		}
		$task_conversion_duration = intval($result_data['duration']);
		@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat");

		// check log file
		$conversion_log = '';
		if (check_file('log.txt', "$task_data[task_id]", $server_data) > 0)
		{
			if (!get_file('log.txt', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get log.txt file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt")) > 10 * 1000 * 1000)
			{
				$conversion_log = 'Conversion log is more than 10mb';
			} else
			{
				$conversion_log = trim(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt"));
			}
		}
		if (!$conversion_log)
		{
			cancel_task(3, "No conversion log is available, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt");

		// check if conversion result contains any error
		if ($result_data['is_error'] == 1)
		{
			log_output('', $video_id);
			log_output($conversion_log, $video_id, 1);
			cancel_task($result_data['error_code'] ?: 7, $result_data['error_message'] ?: "Conversion error, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		$video_files_completed = $result_data['video_files_completed'];

		// check storage servers
		$storage_servers = [];
		if (is_array($result_data['video_files']))
		{
			$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
			foreach ($storage_servers as $server)
			{
				if (!test_connection_status($server))
				{
					warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
				if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
				{
					warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
					return false;
				}
			}
		}

		mark_task_progress($task_data['task_id'], 80);

		// log conversion process
		log_output('', $video_id);
		log_output($conversion_log, $video_id, 1);

		log_output('', $video_id);
		log_output("INFO  Video file creation task is continued for video $video_id [PH-F]", $video_id);

		if ($res_format['status_id'] == 2 && $res_format['is_conditional'] == 1 && (count($result_data['video_files']) == 0 && count($result_data['video_files_completed']) == 0))
		{
			delete_task_folder($task_data['task_id'], $server_data);

			if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
			{
				if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
				{
					log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2", $video_id);
				}
			}

			log_output("INFO  Conditional video file has not been created, finishing this task [PH-FE]", $video_id);
			finish_task($task_data, time() - $task_start_time + $task_conversion_duration);
			return false;
		}

		// copy video files from conversion server
		if (is_array($result_data['video_files']) && count($result_data['video_files']) > 0)
		{
			log_output("INFO  Copying video files from conversion server [PH-F-1]", $video_id);
			foreach ($result_data['video_files'] as $file)
			{
				if (!get_file($file, "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data, true))
				{
					cancel_task(2, "Failed to get $file file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
		}

		log_output("INFO  Finalizing processing for video format \"$res_format[title]\" [PH-F-3:$res_format[title]]", $video_id);

		$invalidate_operation = 'add';
		foreach (get_video_formats($video_id, $res_video['file_formats']) as $v)
		{
			if ($v['postfix'] == $postfix)
			{
				$invalidate_operation = 'change';
				break;
			}
		}

		if (is_array($video_files_completed) && is_array($video_files_completed[$postfix]))
		{
			// invalidate storage servers
			foreach ($storage_servers as $server)
			{
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], $invalidate_operation);
				}
			}
			$video_dimension = $video_files_completed[$postfix]['dimensions'];
			$video_duration = $video_files_completed[$postfix]['duration'];
			$video_size = $video_files_completed[$postfix]['size'];
		} else
		{
			// copy video file to storage servers
			foreach ($storage_servers as $server)
			{
				log_output("INFO  ....Copying video file to \"$server[title]\" [PH-F-3-1:$server[title]]", $video_id);
				if (!put_file("$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$dir_path/$video_id", $server))
				{
					cancel_task(4, "Failed to put $video_id{$postfix} file to storage server \"$server[title]\", cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], $invalidate_operation);
				}
			}
			$video_dimension = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
			$video_duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
			$video_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"));
			log_output("INFO  ....Copied format file to storage: $video_id{$postfix}, $video_dimension[0]x$video_dimension[1], $video_duration sec, $video_size bytes", $video_id);
			if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
			{
				log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $video_id);
			}
		}

		mark_task_progress($task_data['task_id'], 90);

		// clean up timelines folders if they are not empty
		$timeline_dir = $res_format['timeline_directory'];
		if ($timeline_dir)
		{
			rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir");
			$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir", 2);
			foreach ($folders as $folder)
			{
				rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$folder");
			}
			@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/cuepoints.json");
		}

		// create timeline screenshots
		$timeline_screenshots_count = 0;
		$timeline_screenshots_interval = 0;
		if (is_array($result_data['timeline_screenshots_count']))
		{
			$timeline_screenshots_count = intval($result_data['timeline_screenshots_count'][$postfix]);
		}
		if (is_array($result_data['timeline_screenshots_interval']))
		{
			$timeline_screenshots_interval = intval($result_data['timeline_screenshots_interval'][$postfix]);
		}
		if ($timeline_screenshots_count > 0)
		{
			if ($timeline_screenshots_interval == 0)
			{
				cancel_task(3, "Conversion server API should be updated", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			log_output("INFO  ....Copying timeline screenshots from conversion server [PH-F-3-2]", $video_id);

			$timeline_dir = $res_format['timeline_directory'];
			$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
			if (!mkdir_recursive($screenshots_source_dir))
			{
				cancel_task(5, "Failed to create directory $screenshots_source_dir", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			// copy timeline screenshot sources from conversion server
			for ($i = 1; $i <= $timeline_screenshots_count; $i++)
			{
				if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines", $screenshots_source_dir, $server_data, true))
				{
					cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				rename("$screenshots_source_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_source_dir/$i.jpg");
			}

			// copy timeline screenshot formats from conversion server
			foreach ($formats_screenshots as $format_scr)
			{
				if ($format_scr['group_id'] == 2)
				{
					$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format_scr[size]";
					if (!mkdir_recursive($screenshots_target_dir))
					{
						cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					log_output("INFO  ....Copying timeline screenshots from conversion server for \"$format_scr[title]\" format", $video_id);
					for ($i = 1; $i <= $timeline_screenshots_count; $i++)
					{
						if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines/$format_scr[size]", $screenshots_target_dir, $server_data, true))
						{
							cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						rename("$screenshots_target_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_target_dir/$i.jpg");
					}

					if ($format_scr['is_create_zip'] == 1)
					{
						log_output("INFO  ....Creating timeline screenshots ZIP for \"$format_scr[title]\" format [PH-F-3-3:$format_scr[title]]", $video_id);
						$zip_files_to_add = [];
						for ($i = 1; $i <= $timeline_screenshots_count; $i++)
						{
							$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
						}
						$zip = new PclZip("$screenshots_target_dir/$video_id-$format_scr[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
					}
				}
			}
			log_output("INFO  ....Saved $timeline_screenshots_count timeline screenshots", $video_id);
		}

		// update video duration if needed
		$duration = 0;
		$duration_title = '';
		if (($res_video['is_private'] == 0 || $res_video['is_private'] == 1) && $res_format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_STD'])
		{
			$duration = $video_duration;
			$duration_title = $res_format['title'];
		} elseif ($res_video['is_private'] == 2 && $res_format['postfix'] == $options['TAKE_VIDEO_DURATION_FROM_FORMAT_PREMIUM'])
		{
			$duration = $video_duration;
			$duration_title = $res_format['title'];
		}
		if ($duration > 0)
		{
			log_output("INFO  Video duration ($duration) is updated from format file \"$duration_title\"", $video_id);
			sql_update("update $config[tables_prefix]videos set duration=? where video_id=?", $duration, $video_id);
		}

		// create preview file
		@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg");
		resize_image('need_size_no_composite', "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg", $video_dimension[0] . 'x' . $video_dimension[1]);

		// update video formats data
		$formats = get_video_formats($video_id, $res_video['file_formats']);
		$had_format = 0;
		foreach ($formats as $k => $v)
		{
			if ($v['postfix'] == $postfix)
			{
				$formats[$k]['dimensions'] = $video_dimension;
				$formats[$k]['duration'] = $video_duration;
				$formats[$k]['file_size'] = $video_size;
				$formats[$k]['timeline_screen_amount'] = $timeline_screenshots_count;
				$formats[$k]['timeline_screen_interval'] = $timeline_screenshots_interval;
				$formats[$k]['timeline_cuepoints'] = 0;
				$had_format = 1;
				break;
			}
		}
		if ($had_format == 0)
		{
			$new_format = [];
			$new_format['postfix'] = $postfix;
			$new_format['dimensions'] = $video_dimension;
			$new_format['duration'] = $video_duration;
			$new_format['file_size'] = $video_size;
			$new_format['timeline_screen_amount'] = $timeline_screenshots_count;
			$new_format['timeline_screen_interval'] = $timeline_screenshots_interval;
			$formats[] = $new_format;
		}

		// check HD quality
		$video_is_hd = 0;
		foreach ($formats as $k => $v)
		{
			if ($v['dimensions'][1] > 700)
			{
				$video_is_hd = 1;
				break;
			}
		}
		if ($res_video['is_hd'] != $video_is_hd)
		{
			if ($video_is_hd == 1)
			{
				log_output("INFO  Video is considered as HD quality", $video_id);
			} else
			{
				log_output("INFO  Video quality is lowered to non-HD", $video_id);
			}
		}

		sql_update("update $config[tables_prefix]videos set file_formats=?, is_hd=? where video_id=?", pack_video_formats($formats), $video_is_hd, $video_id);

		// clean up temp source files
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
		{
			if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
			{
				log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2", $video_id);
			}
		}
		if ($task_data['data']['temp_source_file'] == 1)
		{
			sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=6, video_id=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 30 MINUTE)", $video_id, date('Y-m-d H:i:s'));
		}

		// complete task
		delete_task_folder($task_data['task_id'], $server_data);

		log_output("INFO  Video file \"$res_format[title]\" creation task is completed for video $video_id [PH-FE]", $video_id);
		finish_task($task_data, time() - $task_start_time + $task_conversion_duration);
	}
	return false;
}

function exec_delete_video_file($task_data)
{
	global $config;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}
	if ($res_video['load_type_id'] != 1)
	{
		cancel_task(1, "Video $video_id has load type $res_video[load_type_id], cancelling this task", 0, $task_data['task_id']);
		return false;
	}
	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
	foreach ($storage_servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Video file \"$res_format[title]\" removal task is started for video $video_id [PH-P]", $video_id);

	$dir_path = get_dir_by_id($video_id);
	foreach ($storage_servers as $server)
	{
		delete_file("$video_id{$postfix}", "$dir_path/$video_id", $server);
		if ($server['streaming_type_id'] == 4) // CDN
		{
			cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], 'delete');
		}
	}
	if ($res_format['timeline_directory'])
	{
		rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
		$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]", 2);
		foreach ($folders as $folder)
		{
			rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]/$folder");
		}
		rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
	}
	@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg");

	$formats = get_video_formats($video_id, $res_video['file_formats']);
	foreach ($formats as $k => $v)
	{
		if ($v['postfix'] == $postfix)
		{
			unset($formats[$k]);
			continue;
		}
	}

	// check HD quality
	$video_is_hd = 0;
	foreach ($formats as $k => $v)
	{
		if ($v['dimensions'][1] > 700)
		{
			$video_is_hd = 1;
			break;
		}
	}
	if ($res_video['is_hd'] != $video_is_hd)
	{
		if ($video_is_hd == 1)
		{
			log_output("INFO  Video is considered as HD quality", $video_id);
		} else
		{
			log_output("INFO  Video quality is lowered to non-HD", $video_id);
		}
	}

	sql_update("update $config[tables_prefix]videos set file_formats=?, is_hd=? where video_id=?", pack_video_formats($formats), $video_is_hd, $video_id);
	log_output("INFO  Removed format file: $video_id{$postfix}", $video_id);

	log_output("INFO  Video file \"$res_format[title]\" removal task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_delete_format_videos($task_data)
{
	global $config;

	$task_start_time = time();

	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]videos)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Format removal task is continued for video format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Format removal task is started for video format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like ?", "%||$postfix|%"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like ? and video_id<=?", "%||$postfix|%", intval($task_data['last_processed_id'])));

	$videos = mr2array(sql_pr("select video_id, server_group_id, file_formats from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like ? and video_id>? order by video_id asc limit $iteration_step", "%||$postfix|%", intval($task_data['last_processed_id'])));
	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		$servers = $storage_servers[$video['server_group_id']];
		if (is_array($servers))
		{
			foreach ($servers as $server)
			{
				delete_file("$video_id{$postfix}", "$dir_path/$video_id", $server);
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_video($video_id, $server, [], ["$dir_path/$video_id/$video_id{$postfix}"], 'delete');
				}
			}
		}
		if ($res_format['timeline_directory'])
		{
			rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
			$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]", 2);
			foreach ($folders as $folder)
			{
				rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]/$folder");
			}
			rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
		}
		@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$postfix}.jpg");

		$formats = get_video_formats($video_id, $video['file_formats']);
		$video_is_hd = 0;
		foreach ($formats as $k => $v)
		{
			if ($v['postfix'] == $postfix)
			{
				unset($formats[$k]);
				continue;
			}
			if ($v['dimensions'][1] > 700)
			{
				$video_is_hd = 1;
			}
		}

		sql_update("update $config[tables_prefix]videos set file_formats=?, is_hd=? where video_id=?", pack_video_formats($formats), $video_is_hd, $video_id);

		log_output('', $video_id, 1, 1);
		log_output("INFO  Deleted \"$res_format[title]\" video format for video $video_id", $video_id);

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  Format removal task is completed for video format \"$res_format[title]\" [PH-FE]");
		sql_delete("delete from $config[tables_prefix]formats_videos where postfix=?", $postfix);
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_create_format_screenshots($task_data, $formats_videos)
{
	global $config, $options;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Format creation task is continued for screenshot format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Format creation task is started for screenshot format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1)"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and video_id<=?", intval($task_data['last_processed_id'])));

	if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0)
	{
		$custom_field_id = 'custom' . intval($options['SCREENSHOTS_CROP_CUSTOMIZE']);
		$videos = mr2array(sql_pr("select v.video_id, v.screen_amount, v.poster_amount, v.file_formats, coalesce(c.$custom_field_id, '') as custom_crop_options from $config[tables_prefix]videos v left join $config[tables_prefix]content_sources c on v.content_source_id=c.content_source_id where v.status_id in (0,1) and v.video_id>? order by v.video_id asc limit $iteration_step", intval($task_data['last_processed_id'])));
	} else
	{
		$videos = mr2array(sql_pr("select v.video_id, v.screen_amount, v.poster_amount, v.file_formats from $config[tables_prefix]videos v where v.status_id in (0,1) and v.video_id>? order by v.video_id asc limit $iteration_step", intval($task_data['last_processed_id'])));
	}

	$failed_videos = [];
	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		$is_video_skipped = true;
		if ($res_format['group_id'] == 1)
		{
			$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
			$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$res_format[size]";
			if (!mkdir_recursive($screenshots_target_dir))
			{
				$failed_videos[] = ['video_id' => $video_id, 'filesystem' => $screenshots_target_dir];
				continue;
			} else
			{
				$screenshots_data = @unserialize(file_get_contents("$screenshots_source_dir/info.dat")) ?: [];
				for ($i = 1; $i <= $video['screen_amount']; $i++)
				{
					if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$i.jpg")) == 0)
					{
						$is_video_skipped = false;

						if (isset($screenshots_data[$i]))
						{
							$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, $screenshots_data[$i]['type'] == 'uploaded');
						} else
						{
							$rnd = mt_rand(1000000, 999999999);
							@copy("$screenshots_source_dir/$i.jpg", "$config[temporary_path]/$rnd.jpg");
							$exec_res = process_screen_source("$config[temporary_path]/$rnd.jpg", $options, false, $video['custom_crop_options']);
							if ($exec_res)
							{
								$failed_videos[] = ['video_id' => $video_id, 'exec' => $exec_res];
								continue 2;
							}
							$exec_res = make_screen_from_source("$config[temporary_path]/$rnd.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
							unlink("$config[temporary_path]/$rnd.jpg");
						}
						if ($exec_res)
						{
							$failed_videos[] = ['video_id' => $video_id, 'exec' => $exec_res];
							continue 2;
						}
					}
				}
				if ($res_format['is_create_zip'] == 1)
				{
					if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
					{
						$is_video_skipped = false;

						$zip_files_to_add = [];
						for ($i = 1; $i <= $video['screen_amount']; $i++)
						{
							$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
						}
						$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
					}
				}
			}
		} elseif ($res_format['group_id'] == 2)
		{
			$formats = get_video_formats($video_id, $video['file_formats']);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['timeline_screen_amount'] > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format['postfix'] == $format_rec['postfix'])
						{
							$timeline_dir = $format['timeline_directory'];
							$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
							$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$res_format[size]";

							if (!mkdir_recursive($screenshots_target_dir))
							{
								$failed_videos[] = ['video_id' => $video_id, 'filesystem' => $screenshots_target_dir];
								continue 3;
							} else
							{
								for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
								{
									if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$i.jpg")) == 0)
									{
										$is_video_skipped = false;

										$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
										if ($exec_res)
										{
											$failed_videos[] = ['video_id' => $video_id, 'exec' => $exec_res];
											continue 4;
										}
									}
								}
								if ($res_format['is_create_zip'] == 1)
								{
									if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
									{
										$is_video_skipped = false;

										$zip_files_to_add = [];
										for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
										{
											$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
										}
										$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
										$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
									}
								}
							}
						}
					}
				}
			}
		} elseif ($res_format['group_id'] == 3)
		{
			if ($video['poster_amount'] > 0)
			{
				$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/posters";
				$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$res_format[size]";
				if (!mkdir_recursive($screenshots_target_dir))
				{
					$failed_videos[] = ['video_id' => $video_id, 'filesystem' => $screenshots_target_dir];
					continue;
				} else
				{
					for ($i = 1; $i <= $video['poster_amount']; $i++)
					{
						if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$i.jpg")) == 0)
						{
							$is_video_skipped = false;

							$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
							if ($exec_res)
							{
								$failed_videos[] = ['video_id' => $video_id, 'exec' => $exec_res];
								continue 2;
							}
						}
					}
					if ($res_format['is_create_zip'] == 1)
					{
						if ($task_data['data']['recreate'] == 1 || intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
						{
							$is_video_skipped = false;

							$zip_files_to_add = [];
							for ($i = 1; $i <= $video['poster_amount']; $i++)
							{
								$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
							}
							$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
							$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
						}
					}
				}
			}
		}

		if (!$is_video_skipped)
		{
			log_output('', $video_id, 1, 1);
			log_output("INFO  Created screenshot format \"$res_format[title]\" for video $video_id", $video_id);
		}

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if (count($failed_videos) > 0)
	{
		// fail task
		$failed_videos_ids = [];
		foreach ($failed_videos as $video_failure_rec)
		{
			$failed_videos_ids[] = $video_failure_rec['video_id'];
			log_output('', $video_failure_rec['video_id'], 1, 1);
			if ($video_failure_rec['filesystem'])
			{
				log_output("WARN  Failed to create screenshot format \"$res_format[title]\" for video $video_failure_rec[video_id]: failed to create directory $video_failure_rec[filesystem]", $video_failure_rec['video_id']);
			} elseif ($video_failure_rec['exec'])
			{
				log_output("WARN  Failed to create screenshot format \"$res_format[title]\" for video $video_failure_rec[video_id]: $video_failure_rec[exec]", $video_failure_rec['video_id']);
			}
		}

		log_output("WARN  Videos with errors are: " . implode(', ', $failed_videos_ids));
		cancel_task(8, "Error during screenshots creation for screenshot format \"$res_format[title]\" for some videos, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  Format creation task is completed for screenshot format \"$res_format[title]\" [PH-FE]");
		sql_update("update $config[tables_prefix]formats_screenshots set status_id=1 where format_screenshot_id=?", $format_id);
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_create_video_overview_screenshots($task_data, $formats_videos, $formats_screenshots)
{
	global $config, $options;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Overview screenshots creation task is started for video $video_id [PH-P]", $video_id);

	$dir_path = get_dir_by_id($video_id);
	$source_file = "$video_id.tmp";
	if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"))
	{
		$source_file = "$video_id.tmp3";
		if ($res_video['load_type_id'] == 2)
		{
			if (!$res_video['file_url'])
			{
				cancel_task(3, "Video $video_id has no hotlink URL specified", $video_id, $task_data['task_id']);
				return false;
			}
			log_output("INFO  Source file is not available, downloading file from $res_video[file_url] [PH-P-1]", $video_id);
			save_file_from_url($res_video['file_url'], "$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
		} elseif ($res_video['load_type_id'] == 1)
		{
			$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
			$video_formats = get_video_formats($video_id, $res_video['file_formats']);
			$max_filesize = 0;
			$max_postfix = '';
			foreach ($video_formats as $format_rec)
			{
				foreach ($formats_videos as $format)
				{
					if ($format_rec['postfix'] == $format['postfix'] && $format['is_use_as_source'] == 1)
					{
						$max_postfix = $format_rec['postfix'];
						break 2;
					}
				}
				if ($format_rec['file_size'] > $max_filesize)
				{
					$max_filesize = $format_rec['file_size'];
					$max_postfix = $format_rec['postfix'];
				}
			}

			if ($max_postfix)
			{
				log_output("INFO  Source file is not available, using \"$max_postfix\" file [PH-P-1]", $video_id);
				$file_copied = false;
				foreach ($storage_servers as $server)
				{
					if (get_file("$video_id{$max_postfix}", "$dir_path/$video_id", "$config[content_path_videos_sources]/$dir_path/$video_id", $server))
					{
						$file_copied = true;
						rename("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$max_postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
						break;
					}
				}
				if (!$file_copied)
				{
					foreach ($storage_servers as $server)
					{
						if (intval($server['streaming_type_id']) == 4)
						{
							log_output("WARN  Failed to get $video_id{$max_postfix} via server connection, trying to download it from CDN cache", $video_id);
							if (cdn_download_video_file($server, "$dir_path/$video_id/$video_id{$max_postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$source_file", $max_filesize))
							{
								break;
							}
						}
					}
				}
			}
		}
	}

	$source_duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
	$source_dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file");
	$source_filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$source_file"));

	if ($source_duration < 1)
	{
		cancel_task(9, "No source file found for creating overview screenshots", $video_id, $task_data['task_id']);
		return false;
	}

	log_output("INFO  Source video parameters are: duration - $source_duration sec, dimensions - $source_dimensions[0]x$source_dimensions[1], filesize - $source_filesize bytes", $video_id);
	log_output("INFO  Creating overview screenshots [PH-F-7]", $video_id);

	$priority_prefix = '';
	if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
	{
		settype($options['GLOBAL_CONVERTATION_PRIORITY'], "integer");
		$priority_prefix = "nice -n $options[GLOBAL_CONVERTATION_PRIORITY] ";
	}

	$screen_sources_target_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
	$screen_sources_working_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/temp";
	$screen_formats_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id";
	if (!mkdir_recursive($screen_sources_target_dir))
	{
		cancel_task(5, "Failed to create directory $screen_sources_target_dir", $video_id, $task_data['task_id']);
		return false;
	}
	if (!mkdir_recursive($screen_sources_working_dir))
	{
		cancel_task(5, "Failed to create directory $screen_sources_working_dir", $video_id, $task_data['task_id']);
		return false;
	}

	$custom_crop_options = '';
	if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0 && $res_video['content_source_id'] > 0)
	{
		$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
		$custom_crop_options = $res_content_source["custom{$options['SCREENSHOTS_CROP_CUSTOMIZE']}"];
	}

	$vf_scale = '';
	unset($res);
	$exec_str = "{$priority_prefix}$config[ffmpeg_path] -y -i $config[content_path_videos_sources]/$dir_path/$video_id/$source_file 2>&1";
	exec($exec_str, $res);
	if (preg_match("|SAR (\d+:\d+) |is", implode("\r\n", $res), $match))
	{
		$sar = explode(':', $match[1]);
		if ($sar[0] > 1 && $sar[1] > 1)
		{
			log_output("INFO  Source video SAR is not square: $match[1]", $video_id);
			$vf_scale = "-vf \"scale=trunc(iw*sar/2)*2:ih\"";
		}
	}

	if ($source_duration > intval($options['SCREENSHOTS_SECONDS_OFFSET_END']))
	{
		$source_duration -= intval($options['SCREENSHOTS_SECONDS_OFFSET_END']);
	} else
	{
		log_output("WARN  Last screenshot offset $options[SCREENSHOTS_SECONDS_OFFSET_END] cannot be used for video with duration $source_duration", $video_id);
	}

	$screenshots_data = [];
	$i_thumb = 0;
	if ($options['SCREENSHOTS_COUNT_UNIT'] == 1)
	{
		$amount = intval($options['SCREENSHOTS_COUNT']);
		$step_target = intval($options['SCREENSHOTS_SECONDS_OFFSET']);
		if ($step_target > $source_duration)
		{
			log_output("WARN  First screenshot offset $step_target cannot be used for video with duration $source_duration", $video_id);
			$step_target = 0;
		}
		$step = floor(($source_duration - $step_target - 1) / $amount);
		if ($step == 0)
		{
			$step = ($source_duration - $step_target - 1) / $amount;
		}
		$step_str = str_replace(',', '.', "$step");
		log_output("INFO  Creating $amount overview screenshots starting from $step_target sec with step $step_str [PH-F-7-1]", $video_id);

		for ($is = 0; $is < $amount; $is++)
		{
			unset($res);
			$step_target_str = str_replace(',', '.', "$step_target");
			$exec_str = "{$priority_prefix}$config[ffmpeg_path] -ss $step_target_str -i $config[content_path_videos_sources]/$dir_path/$video_id/$source_file -vframes 1 -y -f mjpeg -qscale 1 $vf_scale $screen_sources_working_dir/result.jpg 2>&1";
			exec($exec_str, $res);

			if (is_file("$screen_sources_working_dir/result.jpg") && filesize("$screen_sources_working_dir/result.jpg") > 0 && analyze_screenshot("$screen_sources_working_dir/result.jpg"))
			{
				$i_thumb++;
				rename("$screen_sources_working_dir/result.jpg", "$screen_sources_working_dir/$i_thumb.jpg");
				$exec_res = process_screen_source("$screen_sources_working_dir/$i_thumb.jpg", $options, false, $custom_crop_options);
				if ($exec_res)
				{
					log_output("ERROR IM operation failed: $exec_res", $video_id);
					cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id']);
					return false;
				}

				log_output("INFO  Created screenshot $i_thumb at $step_target_str sec", $video_id);
				$screenshots_data[$i_thumb] = ['type' => 'auto', 'filesize' => filesize("$screen_sources_working_dir/$i_thumb.jpg")];
			} else
			{
				log_output("WARN  No screenshot using quick method at $step_target_str sec", $video_id);

				unset($res);
				$exec_str = "{$priority_prefix}$config[ffmpeg_path] -i $config[content_path_videos_sources]/$dir_path/$video_id/$source_file -ss $step_target_str -vframes 1 -y -f mjpeg -qscale 1 $vf_scale $screen_sources_working_dir/result.jpg 2>&1";
				exec($exec_str, $res);

				if (is_file("$screen_sources_working_dir/result.jpg") && filesize("$screen_sources_working_dir/result.jpg") > 0)
				{
					$i_thumb++;
					rename("$screen_sources_working_dir/result.jpg", "$screen_sources_working_dir/$i_thumb.jpg");
					$exec_res = process_screen_source("$screen_sources_working_dir/$i_thumb.jpg", $options, false, $custom_crop_options);
					if ($exec_res)
					{
						log_output("ERROR IM operation failed: $exec_res", $video_id);
						cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id']);
						return false;
					}

					log_output("INFO  Created screenshot $i_thumb at $step_target_str sec", $video_id);
					$screenshots_data[$i_thumb] = ['type' => 'auto', 'filesize' => filesize("$screen_sources_working_dir/$i_thumb.jpg")];
				} else
				{
					log_output("...." . implode("\n....", $res), $video_id, 1);
					log_output("WARN  No screenshot using slow method at $step_target_str sec", $video_id);
				}
			}
			$step_target += $step;
		}
	} else
	{
		$step = intval($options['SCREENSHOTS_COUNT']);
		if ($step < 1)
		{
			$step = 1;
		}
		$step_target = intval($options['SCREENSHOTS_SECONDS_OFFSET']);
		if ($step_target > $source_duration)
		{
			log_output("WARN  First screenshot offset $step_target cannot be used for video with duration $source_duration", $video_id);
			$step_target = 0;
		}
		log_output("INFO  Creating overview screenshots starting from $step_target sec with step $step [PH-F-7-1]", $video_id);

		for ($is = 0; $is < 99999; $is++)
		{
			if ($step_target > $source_duration - 1)
			{
				break;
			}
			unset($res);
			$step_target_str = str_replace(',', '.', "$step_target");
			$exec_str = "{$priority_prefix}$config[ffmpeg_path] -ss $step_target_str -i $config[content_path_videos_sources]/$dir_path/$video_id/$source_file -vframes 1 -y -f mjpeg -qscale 1 $vf_scale $screen_sources_working_dir/result.jpg 2>&1";
			exec($exec_str, $res);

			if (is_file("$screen_sources_working_dir/result.jpg") && filesize("$screen_sources_working_dir/result.jpg") > 0 && analyze_screenshot("$screen_sources_working_dir/result.jpg"))
			{
				$i_thumb++;
				rename("$screen_sources_working_dir/result.jpg", "$screen_sources_working_dir/$i_thumb.jpg");
				$exec_res = process_screen_source("$screen_sources_working_dir/$i_thumb.jpg", $options, false, $custom_crop_options);
				if ($exec_res)
				{
					log_output("ERROR IM operation failed: $exec_res", $video_id);
					cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id']);
					return false;
				}

				log_output("INFO  Created screenshot $i_thumb at $step_target_str sec", $video_id);
				$screenshots_data[$i_thumb] = ['type' => 'auto', 'filesize' => filesize("$screen_sources_working_dir/$i_thumb.jpg")];
			} else
			{
				log_output("WARN  No screenshot using quick method at $step_target_str sec", $video_id);

				unset($res);
				$exec_str = "{$priority_prefix}$config[ffmpeg_path] -i $config[content_path_videos_sources]/$dir_path/$video_id/$source_file -ss $step_target_str -vframes 1 -y -f mjpeg -qscale 1 $vf_scale $screen_sources_working_dir/result.jpg 2>&1";
				exec($exec_str, $res);

				if (is_file("$screen_sources_working_dir/result.jpg") && filesize("$screen_sources_working_dir/result.jpg") > 0)
				{
					$i_thumb++;
					rename("$screen_sources_working_dir/result.jpg", "$screen_sources_working_dir/$i_thumb.jpg");
					$exec_res = process_screen_source("$screen_sources_working_dir/$i_thumb.jpg", $options, false, $custom_crop_options);
					if ($exec_res)
					{
						log_output("ERROR IM operation failed: $exec_res", $video_id);
						cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id']);
						return false;
					}

					log_output("INFO  Created screenshot $i_thumb at $step_target_str sec", $video_id);
					$screenshots_data[$i_thumb] = ['type' => 'auto', 'filesize' => filesize("$screen_sources_working_dir/$i_thumb.jpg")];
				} else
				{
					log_output("...." . implode("\n....", $res), $video_id, 1);
					log_output("WARN  No screenshot using slow method at $step_target_str sec", $video_id);
				}
			}
			$step_target += $step;
		}
	}
	mark_task_progress($task_data['task_id'], 50);

	$old_screen_amount = $res_video['screen_amount'];
	$new_screen_amount = $i_thumb;

	if ($new_screen_amount == 0)
	{
		rmdir_recursive($screen_sources_working_dir);
		cancel_task(8, "No overview screenshots created, cancelling this task", $video_id, $task_data['task_id']);
		return false;
	}

	file_put_contents("$screen_sources_working_dir/info.dat", serialize($screenshots_data));

	// create all overview formats
	foreach ($formats_screenshots as $format)
	{
		if ($format['group_id'] == 1)
		{
			$screenshots_target_dir = "$screen_sources_working_dir/$format[size]";
			log_output("INFO  Creating screenshots for \"$format[title]\" format [PH-F-7-2:$format[title]]", $video_id);
			if (!mkdir_recursive($screenshots_target_dir))
			{
				cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id']);
				return false;
			}
			for ($i = 1; $i <= $new_screen_amount; $i++)
			{
				$exec_res = make_screen_from_source("$screen_sources_working_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $format, $options, false);
				if ($exec_res)
				{
					log_output("ERROR IM operation failed: $exec_res", $video_id);
					cancel_task(8, "Error during overview screenshots creation for \"$format[title]\" format, cancelling this task", $video_id, $task_data['task_id']);
					return false;
				}
			}
			if ($format['is_create_zip'] == 1)
			{
				log_output("INFO  Creating screenshots ZIP for \"$format[title]\" format [PH-F-7-3:$format[title]]", $video_id);
				$zip_files_to_add = [];
				for ($i = 1; $i <= $new_screen_amount; $i++)
				{
					$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
				}

				$zip = new PclZip("$screenshots_target_dir/$video_id-$format[size].zip");
				$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
			}
		}
	}
	mark_task_progress($task_data['task_id'], 80);

	// copy temp files to the final location
	for ($i = 1; $i <= $new_screen_amount; $i++)
	{
		if (!rename("$screen_sources_working_dir/$i.jpg", "$screen_sources_target_dir/$i.jpg"))
		{
			cancel_task(5, "Failed to replace file $screen_sources_target_dir/$i.jpg", $video_id, $task_data['task_id']);
			return false;
		}
	}
	if (!rename("$screen_sources_working_dir/info.dat", "$screen_sources_target_dir/info.dat"))
	{
		cancel_task(5, "Failed to replace file $screen_sources_target_dir/info.dat", $video_id, $task_data['task_id']);
		return false;
	}
	foreach ($formats_screenshots as $format)
	{
		if ($format['group_id'] == 1)
		{
			if (!mkdir_recursive("$screen_formats_target_dir/$format[size]"))
			{
				cancel_task(5, "Failed to create directory $screen_formats_target_dir/$format[size]", $video_id, $task_data['task_id']);
				return false;
			}
			for ($i = 1; $i <= $new_screen_amount; $i++)
			{
				if (!rename("$screen_sources_working_dir/$format[size]/$i.jpg", "$screen_formats_target_dir/$format[size]/$i.jpg"))
				{
					cancel_task(5, "Failed to replace file $screen_formats_target_dir/$format[size]/$i.jpg", $video_id, $task_data['task_id']);
					return false;
				}
			}
			if ($format['is_create_zip'] == 1)
			{
				if (!rename("$screen_sources_working_dir/$format[size]/$video_id-$format[size].zip", "$screen_formats_target_dir/$format[size]/$video_id-$format[size].zip"))
				{
					cancel_task(5, "Failed to replace file $screen_formats_target_dir/$format[size]/$video_id-$format[size].zip", $video_id, $task_data['task_id']);
					return false;
				}
			}
		}
	}

	$screen_main = intval($options['SCREENSHOTS_MAIN_NUMBER']);
	if ($screen_main > $new_screen_amount)
	{
		$screen_main = 1;
	}
	log_output("INFO  Main screenshot is set to $screen_main", $video_id);
	log_output("INFO  Saved $new_screen_amount screenshots", $video_id);
	sql_update("update $config[tables_prefix]videos set screen_main=?, screen_main_temp=0, screen_amount=? where video_id=?", $screen_main, $new_screen_amount, $video_id);

	// create preview files
	@unlink("$screen_formats_target_dir/preview.jpg");
	copy("$screen_sources_target_dir/$screen_main.jpg", "$screen_formats_target_dir/preview.jpg");
	$video_formats = get_video_formats($video_id, $res_video['file_formats']);
	foreach ($video_formats as $format)
	{
		@unlink("$screen_formats_target_dir/preview{$format['postfix']}.jpg");
		resize_image('need_size_no_composite', "$screen_formats_target_dir/preview.jpg", "$screen_formats_target_dir/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
	}

	// cleanup old screenshots
	for ($i = $new_screen_amount + 1; $i <= $old_screen_amount; $i++)
	{
		@unlink("$screen_sources_target_dir/$i.jpg");
		foreach ($formats_screenshots as $format)
		{
			if ($format['group_id'] == 1)
			{
				@unlink("$screen_formats_target_dir/$format[size]/$i.jpg");
			}
		}
	}
	@unlink("$screen_sources_target_dir/rotator.dat");

	// cleanup temp folders
	$folders = get_contents_from_dir("$screen_sources_working_dir", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$screen_sources_working_dir/$folder");
	}
	rmdir_recursive("$screen_sources_working_dir");

	if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3"))
	{
		if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3"))
		{
			log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3", $video_id);
		}
	}

	if ($task_data['data']['temp_source_file'] == 1)
	{
		sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=6, video_id=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 30 MINUTE)", $video_id, date('Y-m-d H:i:s'));
	}

	log_output("INFO  Overview screenshots creation task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_delete_video_overview_screenshots($task_data, $formats_screenshots)
{
	global $config;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Overview screenshots deletion task is started for video $video_id [PH-P]", $video_id);

	$dir_path = get_dir_by_id($video_id);
	for ($i = 1; $i <= $res_video['screen_amount']; $i++)
	{
		if ($i == $res_video['screen_main'])
		{
			if ($i > 1)
			{
				if (!@rename("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/1.jpg"))
				{
					cancel_task(5, "Failed to replace file $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/1.jpg", $video_id, $task_data['task_id']);
					return false;
				} else
				{
					foreach ($formats_screenshots as $format)
					{
						if ($format['group_id'] == 1)
						{
							$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
							if (!@rename("$screenshots_target_dir/$i.jpg", "$screenshots_target_dir/1.jpg"))
							{
								cancel_task(5, "Failed to replace file $screenshots_target_dir/1.jpg", $video_id, $task_data['task_id']);
								return false;
							}
						}
					}
				}
			}
		} else
		{
			if (!@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg"))
			{
				log_output("WARN  Failed to delete $config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg", $video_id);
			}
			foreach ($formats_screenshots as $format)
			{
				if ($format['group_id'] == 1)
				{
					$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
					if (!@unlink("$screenshots_target_dir/$i.jpg"))
					{
						log_output("WARN  Failed to delete $screenshots_target_dir/$i.jpg", $video_id);
					}
				}
			}
		}
	}

	$screenshots_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat")) ?: [];
	if (isset($screenshots_data[$res_video['screen_main']]))
	{
		$screenshots_data = [1 => $screenshots_data[$res_video['screen_main']]];
	} else
	{
		$screenshots_data = [1 => ['type' => 'auto', 'filesize' => filesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/1.jpg")]];
	}
	file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/info.dat", serialize($screenshots_data), LOCK_EX);

	sql_update("update $config[tables_prefix]videos set screen_amount=1, screen_main=1 where video_id=?", $video_id);
	log_output("INFO  Main screenshot is set to 1", $video_id);

	@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat");

	log_output("INFO  Overview screenshots deletion task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_create_video_timeline_screenshots($task_data,$server_data,$formats_screenshots)
{
	global $config, $options, $source_download_base_url;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}
	if ($res_video['load_type_id'] != 1)
	{
		cancel_task(1, "Video $video_id has load type $res_video[load_type_id], cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id'], $server_data);
		return false;
	}

	$format_filesize = 0;
	$formats = get_video_formats($video_id, $res_video['file_formats']);
	foreach ($formats as $format_rec)
	{
		if ($format_rec['postfix'] == $res_format['postfix'])
		{
			$format_filesize = $format_rec['file_size'];
			if ($format_rec['timeline_screen_amount'] > 0)
			{
				log_output("INFO  Video $video_id already has timeline screenshots");
				finish_task($task_data, time() - $task_start_time);
				return false;
			}
		}
	}

	$dir_path = get_dir_by_id($video_id);
	$server_data = mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($server_data['server_id'])));

	if ($task_data['status_id'] == 0)
	{
		if (!isset($server_data))
		{
			warn_task("Conversion server is not available in the database, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}

		log_output('', $video_id);
		log_output("INFO  Timeline screenshots creation task is started for video $video_id [PH-P]", $video_id);
		log_output("INFO  Preparing task for conversion server [PH-P-2]", $video_id);

		$task_info = [];
		$task_info['video_id'] = $video_id;
		$task_info['options']['PROCESS_PRIORITY'] = $options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY'] = $config['imagemagick_default_jpeg_quality'];
		if ($server_data['connection_type_id'] == 0)
		{
			$task_info['source_dir'] = "$config[content_path_videos_sources]/$dir_path/$video_id";
		}

		if ($config['installation_type'] >= 3)
		{
			$task_info['options']['PROCESS_PRIORITY'] = intval($server_data['process_priority']);
		}
		log_output("INFO  Conversion priority level is set to " . $task_info['options']['PROCESS_PRIORITY'], $video_id);

		$formats_to_make_timelines = [$res_format];
		$task_info['timelines_creation_list'] = $formats_to_make_timelines;
		log_output("INFO  Timeline screenshots will be created for video formats: " . print_formats_list($formats_to_make_timelines), $video_id);

		$task_info['formats_screenshots'] = $formats_screenshots;
		foreach ($formats_screenshots as $format)
		{
			if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png"))
			{
				if (!put_file("watermark_screen_{$format['format_screenshot_id']}.png", "$config[project_path]/admin/data/other", "$task_data[task_id]", $server_data))
				{
					cancel_task(2, "Failed to put watermark_screen_{$format['format_screenshot_id']}.png file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
		}

		log_output("INFO  Downloading video file from storage server [PH-P-2-1]", $video_id);
		$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
		foreach ($storage_servers as $server)
		{
			if (get_file("$video_id{$postfix}", "$dir_path/$video_id", "$config[content_path_videos_sources]/$dir_path/$video_id", $server))
			{
				break;
			}
		}
		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
		{
			foreach ($storage_servers as $server)
			{
				if (intval($server['streaming_type_id']) == 4)
				{
					log_output("WARN  Failed to get $video_id{$postfix} via server connection, trying to download it from CDN cache", $video_id);
					if (cdn_download_video_file($server, "$dir_path/$video_id/$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $format_filesize))
					{
						break;
					}
				}
			}
		}
		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
		{
			cancel_task(4, "Failed to get $video_id{$postfix} file from storage servers, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		$file_duration = get_video_duration("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
		$file_dimensions = get_video_dimensions("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
		$file_filesize = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"));

		log_output("INFO  Video file parameters are: duration - $file_duration sec, dimensions - $file_dimensions[0]x$file_dimensions[1], filesize - $file_filesize bytes", $video_id);

		if ($server_data['connection_type_id'] != 0)
		{
			if ($server_data['option_pull_source_files'] == 1)
			{
				$hash = md5($config['cv'] . "$dir_path/$video_id/$video_id{$postfix}");
				$task_info['download_urls']["$video_id{$postfix}"] = [
						'url' => "$source_download_base_url/get_file/0/$hash/$dir_path/$video_id/$video_id{$postfix}/",
						'file_size' => sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
				];
			} else
			{
				log_output("INFO  Copying video file $video_id{$postfix} to conversion server [PH-P-2-2]", $video_id);
				if (!put_file("$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
				{
					cancel_task(2, "Failed to put $video_id{$postfix} file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
			}
		}

		file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat", serialize($task_info), LOCK_EX);
		if (!put_file('task.dat', "$config[content_path_videos_sources]/$dir_path/$video_id", "$task_data[task_id]", $server_data))
		{
			cancel_task(2, "Failed to put task.dat file to conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		unlink("$config[content_path_videos_sources]/$dir_path/$video_id/task.dat");

		log_output("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]", $video_id);
		sql_update("update $config[tables_prefix]background_tasks set status_id=1, server_id=? where task_id=?", $server_data['server_id'], $task_data['task_id']);

		mark_task_progress($task_data['task_id'], 10);
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		return true;
	} else
	{
		if (!isset($server_data))
		{
			cancel_task(1, "Conversion server $task_data[server_id] is not available in the database, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		if (check_file('progress.dat', "$task_data[task_id]", $server_data))
		{
			get_file('progress.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data);
			if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat"))
			{
				mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat")) * 0.8));
				unlink("$config[content_path_videos_sources]/$dir_path/$video_id/progress.dat");
			}
		}

		if (check_file('result.dat', "$task_data[task_id]", $server_data) == 0)
		{
			if (check_file('task.dat', "$task_data[task_id]", $server_data) > 0)
			{
				return false;
			} else
			{
				if (test_connection($server_data) === true)
				{
					cancel_task(2, "Task directory is not available on conversion server, cancelling this task", $video_id, $task_data['task_id']);
				} else
				{
					warn_task("Conversion server connection is lost, skipping this task", $video_id, $task_data['task_id'], time() - $task_start_time);
				}
				return false;
			}
		}

		// check result file
		if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
		{
			cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
		if (!is_array($result_data))
		{
			sleep(1);
			if (!get_file('result.dat', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get result.dat file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
			$result_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat"));
			if (!is_array($result_data))
			{
				cancel_task(6, "Unexpected error on conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}
		}
		$task_conversion_duration = intval($result_data['duration']);
		@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/result.dat");

		// check log file
		$conversion_log = '';
		if (check_file('log.txt', "$task_data[task_id]", $server_data) > 0)
		{
			if (!get_file('log.txt', "$task_data[task_id]", "$config[content_path_videos_sources]/$dir_path/$video_id", $server_data))
			{
				cancel_task(2, "Failed to get log.txt file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt")) > 10 * 1000 * 1000)
			{
				$conversion_log = 'Conversion log is more than 10mb';
			} else
			{
				$conversion_log = trim(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt"));
			}
		}
		if (!$conversion_log)
		{
			cancel_task(3, "No conversion log is available, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}
		@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/log.txt");

		// check if conversion result contains any error
		if ($result_data['is_error'] == 1)
		{
			log_output('', $video_id);
			log_output($conversion_log, $video_id, 1);
			cancel_task($result_data['error_code'] ?: 7, $result_data['error_message'] ?: "Conversion error, cancelling this task", $video_id, $task_data['task_id'], $server_data);
			return false;
		}

		mark_task_progress($task_data['task_id'], 90);

		// log conversion process
		log_output('', $video_id);
		log_output($conversion_log, $video_id, 1);

		log_output('', $video_id);
		log_output("INFO  Timeline screenshots creation task is continued for video $video_id [PH-F]", $video_id);

		log_output("INFO  Finalizing processing for video format \"$res_format[title]\" [PH-F-3:$res_format[title]]", $video_id);

		// create timeline screenshots
		$timeline_screenshots_count = 0;
		$timeline_screenshots_interval = 0;
		if (is_array($result_data['timeline_screenshots_count']))
		{
			$timeline_screenshots_count = intval($result_data['timeline_screenshots_count'][$postfix]);
		}
		if (is_array($result_data['timeline_screenshots_interval']))
		{
			$timeline_screenshots_interval = intval($result_data['timeline_screenshots_interval'][$postfix]);
		}
		if ($timeline_screenshots_count > 0)
		{
			if ($timeline_screenshots_interval == 0)
			{
				cancel_task(3, "Conversion server API should be updated", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			log_output("INFO  ....Copying timeline screenshots from conversion server [PH-F-3-2]", $video_id);

			$timeline_dir = $res_format['timeline_directory'];
			$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
			if (!mkdir_recursive($screenshots_source_dir))
			{
				cancel_task(5, "Failed to create directory $screenshots_source_dir", $video_id, $task_data['task_id'], $server_data);
				return false;
			}

			// copy timeline screenshot sources from conversion server
			for ($i = 1; $i <= $timeline_screenshots_count; $i++)
			{
				if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines", $screenshots_source_dir, $server_data, true))
				{
					cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
					return false;
				}
				rename("$screenshots_source_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_source_dir/$i.jpg");
			}

			// copy timeline screenshot formats from conversion server
			foreach ($formats_screenshots as $format_scr)
			{
				if ($format_scr['group_id'] == 2)
				{
					$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$format_scr[size]";
					if (!mkdir_recursive($screenshots_target_dir))
					{
						cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id'], $server_data);
						return false;
					}

					log_output("INFO  ....Copying timeline screenshots from conversion server for \"$format_scr[title]\" format", $video_id);
					for ($i = 1; $i <= $timeline_screenshots_count; $i++)
					{
						if (!get_file("{$timeline_dir}_{$i}.jpg", "$task_data[task_id]/timelines/$format_scr[size]", $screenshots_target_dir, $server_data, true))
						{
							cancel_task(2, "Failed to get {$timeline_dir}_{$i}.jpg file from conversion server, cancelling this task", $video_id, $task_data['task_id'], $server_data);
							return false;
						}
						rename("$screenshots_target_dir/{$timeline_dir}_{$i}.jpg", "$screenshots_target_dir/$i.jpg");
					}

					if ($format_scr['is_create_zip'] == 1)
					{
						log_output("INFO  ....Creating timeline screenshots ZIP for \"$format_scr[title]\" format [PH-F-3-3:$format_scr[title]]", $video_id);
						$zip_files_to_add = [];
						for ($i = 1; $i <= $timeline_screenshots_count; $i++)
						{
							$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
						}
						$zip = new PclZip("$screenshots_target_dir/$video_id-$format_scr[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
					}
				}
			}
			log_output("INFO  ....Saved $timeline_screenshots_count timeline screenshots", $video_id);
		}

		// update video formats data
		$formats = get_video_formats($video_id, $res_video['file_formats']);
		foreach ($formats as $k => $v)
		{
			if ($v['postfix'] == $postfix)
			{
				$formats[$k]['timeline_screen_amount'] = $timeline_screenshots_count;
				$formats[$k]['timeline_screen_interval'] = $timeline_screenshots_interval;
				$formats[$k]['timeline_cuepoints'] = 0;
				break;
			}
		}
		@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]/cuepoints.json");

		if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
		{
			log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $video_id);
		}

		sql_update("update $config[tables_prefix]videos set file_formats=? where video_id=?", pack_video_formats($formats), $video_id);

		// complete task
		delete_task_folder($task_data['task_id'], $server_data);

		log_output("INFO  Timeline screenshots creation task is completed for video $video_id [PH-FE]", $video_id);
		finish_task($task_data, time() - $task_start_time + $task_conversion_duration);
	}
	return false;
}

function exec_delete_format_screenshots($task_data, $formats_videos)
{
	global $config;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Format removal task is continued for screenshot format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Format removal task is started for screenshot format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id<=?", intval($task_data['last_processed_id'])));

	$videos = mr2array(sql_pr("select video_id, file_formats from $config[tables_prefix]videos where video_id>? order by video_id asc limit ?", intval($task_data['last_processed_id']), $iteration_step));
	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		if ($res_format['group_id'] == 1)
		{
			rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/$res_format[size]");
		} elseif ($res_format['group_id'] == 2)
		{
			$formats = get_video_formats($video_id, $video['file_formats']);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['timeline_screen_amount'] > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format['postfix'] == $format_rec['postfix'])
						{
							rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$format[timeline_directory]/$res_format[size]");
						}
					}
				}
			}
		} elseif ($res_format['group_id'] == 3)
		{
			rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$res_format[size]");
		}

		log_output('', $video_id, 1, 1);
		log_output("INFO  Removed screenshot format \"$res_format[title]\" for video $video_id", $video_id);

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  Format removal task is completed for screenshot format \"$res_format[title]\" [PH-FE]");
		sql_delete("delete from $config[tables_prefix]formats_screenshots where format_screenshot_id=?", $format_id);
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_create_video_formats_screenshots($task_data, $formats_videos)
{
	global $config, $options;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$res_formats = [];
	$format_ids = $task_data['data']['format_ids'];
	foreach ($format_ids as $format_id)
	{
		$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=? and status_id=1", $format_id));
		if (!isset($res_format))
		{
			cancel_task(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
			return false;
		}
		$res_formats[] = $res_format;
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Screenshot formats re-creation task is started for video $video_id [PH-P]", $video_id);

	$dir_path = get_dir_by_id($video_id);

	$custom_crop_options = '';
	if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0 && $res_video['content_source_id'] > 0)
	{
		$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_video['content_source_id']));
		$custom_crop_options = $res_content_source["custom{$options['SCREENSHOTS_CROP_CUSTOMIZE']}"];
	}

	foreach ($res_formats as $res_format)
	{
		if ($res_format['group_id'] == 1)
		{
			$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
			$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$res_format[size]";
			if (!mkdir_recursive($screenshots_target_dir))
			{
				cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id']);
				return false;
			}

			$screenshots_data = @unserialize(file_get_contents("$screenshots_source_dir/info.dat")) ?: [];
			for ($i = 1; $i <= $res_video['screen_amount']; $i++)
			{
				if (isset($screenshots_data[$i]))
				{
					$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, $screenshots_data[$i]['type'] == 'uploaded');
				} else
				{
					$rnd = mt_rand(1000000, 999999999);
					@copy("$screenshots_source_dir/$i.jpg", "$config[temporary_path]/$rnd.jpg");
					$exec_res = process_screen_source("$config[temporary_path]/$rnd.jpg", $options, false, $custom_crop_options);
					if ($exec_res)
					{
						log_output("ERROR IM operation failed: $exec_res", $video_id);
						cancel_task(8, "Error during screenshots sources processing, cancelling this task", $video_id, $task_data['task_id']);
						return false;
					}
					$exec_res = make_screen_from_source("$config[temporary_path]/$rnd.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
					unlink("$config[temporary_path]/$rnd.jpg");
				}
				if ($exec_res)
				{
					log_output("ERROR IM operation failed: $exec_res", $video_id);
					cancel_task(8, "Error during overview screenshots creation for screenshot format \"$res_format[title]\", cancelling this task", $video_id, $task_data['task_id']);
					return false;
				}
			}
			if ($res_format['is_create_zip'] == 1)
			{
				$zip_files_to_add = [];
				for ($i = 1; $i <= $res_video['screen_amount']; $i++)
				{
					$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
				}
				$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
				$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
			}
		} elseif ($res_format['group_id'] == 2)
		{
			$formats = get_video_formats($video_id, $res_video['file_formats']);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['timeline_screen_amount'] > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format['postfix'] == $format_rec['postfix'])
						{
							$timeline_dir = $format['timeline_directory'];
							$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_dir";
							$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$res_format[size]";
							if (!mkdir_recursive($screenshots_target_dir))
							{
								cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id']);
								return false;
							}

							for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
							{
								$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
								if ($exec_res)
								{
									log_output("ERROR IM operation failed: $exec_res", $video_id);
									cancel_task(8, "Error during timeline screenshots creation for screenshot format \"$res_format[title]\", cancelling this task", $video_id, $task_data['task_id']);
									return false;
								}
							}
							if ($res_format['is_create_zip'] == 1)
							{
								$zip_files_to_add = [];
								for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
								{
									$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
								}
								$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
								$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
							}
						}
					}
				}
			}
		} elseif ($res_format['group_id'] == 3)
		{
			if ($res_video['poster_amount'] > 0)
			{
				$screenshots_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/posters";
				$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$res_format[size]";
				if (!mkdir_recursive($screenshots_target_dir))
				{
					cancel_task(5, "Failed to create directory $screenshots_target_dir", $video_id, $task_data['task_id']);
					return false;
				}

				for ($i = 1; $i <= $res_video['poster_amount']; $i++)
				{
					$exec_res = make_screen_from_source("$screenshots_source_dir/$i.jpg", "$screenshots_target_dir/$i.jpg", $res_format, $options, false);
					if ($exec_res)
					{
						log_output("ERROR IM operation failed: $exec_res", $video_id);
						cancel_task(8, "Error during posters creation for screenshot format \"$res_format[title]\", cancelling this task", $video_id, $task_data['task_id']);
						return false;
					}
				}
				if ($res_format['is_create_zip'] == 1)
				{
					$zip_files_to_add = [];
					for ($i = 1; $i <= $res_video['poster_amount']; $i++)
					{
						$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
					}
					$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
					$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
				}
			}
		}
		log_output("INFO  Re-created screenshot format \"$res_format[title]\"", $video_id);
	}

	log_output("INFO  Screenshot formats re-creation task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_new_album($task_data,$server_data,$formats_albums)
{
	global $config,$options,$plugins_on_new;

	$task_start_time=time();

	$album_id=intval($task_data['album_id']);
	$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));
	if (!isset($res_album))
	{
		cancel_task_album(1,"Album $album_id is not available in the database, cancelling this task",0,$task_data['task_id'],$server_data);
		return false;
	}

	$dir_path=get_dir_by_id($album_id);
	$server_data=mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?",intval($server_data['server_id'])));

	$source_file=$task_data['data']['source_file'];
	$source_files=array();
	if ($source_file<>'')
	{
		$source_files[]=$source_file;
	} else {
		$source_files=$task_data['data']['source_files'];
	}

	$custom_crop_options='';
	if (intval($options['ALBUMS_CROP_CUSTOMIZE'])>0 && $res_album['content_source_id']>0)
	{
		$res_content_source=mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?",$res_album['content_source_id']));
		$custom_crop_options=$res_content_source["custom{$options['ALBUMS_CROP_CUSTOMIZE']}"];
	}

	if ($task_data['status_id']==0)
	{
		if (!isset($server_data))
		{
			mark_task_duration($task_data['task_id'],time()-$task_start_time);
			warn_task_album("Conversion server is not available in the database, skipping this task",0,$task_data['task_id']);
			return false;
		}

		if (is_array($task_data['data']['source_urls']))
		{
			if (min(@disk_free_space($config['project_path']),@disk_free_space($config['content_path_albums_sources']))<$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Server free space is lower than $options[MAIN_SERVER_MIN_FREE_SPACE_MB]M, skipping this task",0,$task_data['task_id']);
				return false;
			}
		}

		log_output_album("INFO  New album creation task is started for album $album_id [PH-P]",$album_id);

		if ($task_data['data']['import_data']!='')
		{
			log_output_album("INFO  Imported using the following data:",$album_id);
			log_output_album($task_data['data']['import_data'],$album_id,1);
		}

		if (is_array($task_data['data']['source_urls']))
		{
			$source_files=array();
			$source_file_index=1;
			log_output_album("INFO  Downloading source files [PH-P-1]",$album_id);
			foreach ($task_data['data']['source_urls'] as $source_url)
			{
				log_output_album("INFO  Downloading source file from $source_url",$album_id);
				@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file_index.jpg");
				save_file_from_url($source_url,"$config[content_path_albums_sources]/$dir_path/$album_id/$source_file_index.jpg");
				$source_files[]="$source_file_index.jpg";
				$source_file_index++;
			}
		}

		if (@count($source_files)==0)
		{
			cancel_task_album(9,"No source file(s) uploaded, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		foreach ($source_files as $source_file)
		{
			if (!is_file("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file"))
			{
				cancel_task_album(9,"Source file $source_file does not exist",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}

		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);
		}

		$uploaded_files=get_contents_from_dir("$config[content_path_albums_sources]/$dir_path/$album_id",1);
		if (count($uploaded_files)>0)
		{
			$uploaded_files_str='';
			foreach ($uploaded_files as $uploaded_file)
			{
				$uploaded_file_size=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/$uploaded_file"));
				$uploaded_files_str.="$uploaded_file [$uploaded_file_size], ";
			}
			if (strlen($uploaded_files_str)>2)
			{
				$uploaded_files_str=substr($uploaded_files_str,0, -2);
			}
			log_output_album("INFO  Files uploaded for new album: $uploaded_files_str",$album_id);
		}

		$images_amount=1;
		foreach ($source_files as $source_file)
		{
			$ext=strtolower(end(explode(".",$source_file)));
			if ($ext=='zip')
			{
				// extract images and copy them to source directory
				$zip = new PclZip("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file");
				$data=process_zip_images($zip->listContent());
				foreach ($data as $v)
				{
					$file_base_name=$v['filename'];
					$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
					$fstream=$content[0]['content'];
					if ($fstream=='')
					{
						cancel_task_album(9,"Failed to extract $file_base_name from ZIP, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
					$fp=fopen("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$images_amount.jpg","w");
					fwrite($fp,$fstream);
					fclose($fp);
					$images_amount++;
				}
			} else {
				copy("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$images_amount.jpg");
				$images_amount++;
			}
		}
		$images_amount--;

		if ($images_amount==0)
		{
			cancel_task_album(9,"No images uploaded, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		log_output_album("INFO  Preparing task for conversion server [PH-P-2]",$album_id);

		for ($i=1;$i<=$images_amount;$i++)
		{
			$orientation_res=correct_orientation("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$i.jpg");
			if (intval($orientation_res)>0)
			{
				log_output_album("INFO  Image $i is not oriented properly, EXIF info: ".intval($orientation_res),$album_id);
			} elseif ($orientation_res!=0) {
				log_output_album("ERROR IM operation failed: $orientation_res",$album_id);
				cancel_task_album(9,"Failed to change orientation",$album_id,$task_data['task_id'],$server_data);
			}
		}

		for ($i=1;$i<=$images_amount;$i++)
		{
			if (!put_file("$i.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]","$task_data[task_id]",$server_data))
			{
				cancel_task_album(2,"Failed to put $i.jpg file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}

		$task_info=array();
		$task_info['album_id']=$album_id;
		$task_info['source_images_count']=$images_amount;
		$task_info['options']['PROCESS_PRIORITY']=$options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['ALBUMS_CROP_LEFT_UNIT']=$options['ALBUMS_CROP_LEFT_UNIT'];
		$task_info['options']['ALBUMS_CROP_RIGHT_UNIT']=$options['ALBUMS_CROP_RIGHT_UNIT'];
		$task_info['options']['ALBUMS_CROP_TOP_UNIT']=$options['ALBUMS_CROP_TOP_UNIT'];
		$task_info['options']['ALBUMS_CROP_BOTTOM_UNIT']=$options['ALBUMS_CROP_BOTTOM_UNIT'];
		$task_info['options']['ALBUMS_CROP_LEFT']=$options['ALBUMS_CROP_LEFT'];
		$task_info['options']['ALBUMS_CROP_RIGHT']=$options['ALBUMS_CROP_RIGHT'];
		$task_info['options']['ALBUMS_CROP_TOP']=$options['ALBUMS_CROP_TOP'];
		$task_info['options']['ALBUMS_CROP_BOTTOM']=$options['ALBUMS_CROP_BOTTOM'];
		$task_info['options']['ALBUMS_CROP_CUSTOMIZE']=$custom_crop_options;
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']=$config['imagemagick_default_jpeg_quality'];

		if ($config['installation_type']>=3)
		{
			$task_info['options']['PROCESS_PRIORITY']=intval($server_data['process_priority']);
		}
		log_output_album("INFO  Conversion priority level is set to ".$task_info['options']['PROCESS_PRIORITY'],$album_id);

		$task_info['formats_albums']=$formats_albums;
		foreach ($formats_albums as $format)
		{
			if (is_file("$config[project_path]/admin/data/other/watermark_album_{$format['format_album_id']}.png"))
			{
				if (!put_file("watermark_album_{$format['format_album_id']}.png","$config[project_path]/admin/data/other","$task_data[task_id]",$server_data))
				{
					cancel_task_album(2,"Failed to put watermark_album_{$format['format_album_id']}.png file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
		}

		$main_image=1;
		if (intval($task_data['data']['image_main'])>1)
		{
			$main_image=intval($task_data['data']['image_main']);
		}
		if ($main_image>$images_amount)
		{
			$main_image=1;
		}
		$task_info['main_image']=$main_image;
		if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg"))
		{
			$orientation_res=correct_orientation("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg");
			if (intval($orientation_res)>0)
			{
				log_output_album("INFO  Preview image is not oriented properly, EXIF info: ".intval($orientation_res),$album_id);
			} elseif ($orientation_res!=0) {
				log_output_album("ERROR IM operation failed: $orientation_res",$album_id);
				cancel_task_album(9,"Failed to change orientation",$album_id,$task_data['task_id'],$server_data);
			}

			if (!put_file("preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id","$task_data[task_id]",$server_data))
			{
				cancel_task_album(2,"Failed to put preview.jpg file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			$task_info['preview_source']="preview.jpg";
		}

		$fp=fopen("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat","w");
		fwrite($fp,serialize($task_info));
		fclose($fp);

		if (!put_file('task.dat',"$config[content_path_albums_sources]/$dir_path/$album_id","$task_data[task_id]",$server_data))
		{
			cancel_task_album(2,"Failed to put task.dat file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		unlink("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat");

		log_output_album("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]",$album_id);
		sql_pr("update $config[tables_prefix]background_tasks set status_id=1, server_id=$server_data[server_id] where task_id=$task_data[task_id]");
		mark_task_progress($task_data['task_id'],10);
		mark_task_duration($task_data['task_id'],time()-$task_start_time);
		return true;
	} else {
		// check conversion task
		if (!isset($server_data))
		{
			cancel_task_album(1,"Conversion server $server_data[server_id] is not available in the database, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		if (check_file('progress.dat', "$task_data[task_id]", $server_data))
		{
			get_file('progress.dat', "$task_data[task_id]", "$config[content_path_albums_sources]/$dir_path/$album_id", $server_data);
			if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat"))
			{
				mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat")) * 0.5));
				unlink("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat");
			}
		}

		if (check_file('result.dat',"$task_data[task_id]",$server_data)==0)
		{
			if (check_file('task.dat',"$task_data[task_id]",$server_data)>0)
			{
				return false;
			} else {
				if (test_connection($server_data)===true)
				{
					cancel_task_album(2,"Task directory is not available on conversion server, cancelling this task",$album_id,$task_data['task_id']);
				} else {
					warn_task_album("Conversion server connection is lost, skipping this task",$album_id,$task_data['task_id']);
				}
				return false;
			}
		}

		// check result file
		if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
		{
			cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
		if (!is_array($result_data))
		{
			sleep(1);
			if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
			{
				cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
			if (!is_array($result_data))
			{
				cancel_task_album(6,"Unexpected error on conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}
		$task_conversion_duration = intval($result_data['duration']);
		@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat");

		// check log file
		$conversion_log = '';
		if (check_file('log.txt',"$task_data[task_id]",$server_data)>0)
		{
			if (!get_file('log.txt',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
			{
				cancel_task_album(2,"Failed to get log.txt file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}

			if (sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt")) > 10 * 1000 * 1000)
			{
				$conversion_log = 'Conversion log is more than 10mb';
			} else
			{
				$conversion_log = trim(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt"));
			}
		}
		if ($conversion_log === '')
		{
			cancel_task_album(3,"No conversion log is available, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt");

		// check if conversion result contains any error
		if ($result_data['is_error']==1)
		{
			log_output_album('',$album_id);
			log_output_album($conversion_log,$album_id,1);
			cancel_task_album(intval($result_data['error_code'])>0?$result_data['error_code']:7,$result_data['error_message']!=''?$result_data['error_message']:"Conversion error, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		$server_group_id=0;
		if (intval($res_album['server_group_id'])>0)
		{
			$server_group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where group_id=?",intval($res_album['server_group_id'])));
		} elseif (intval($task_data['data']['server_group_id'])>0)
		{
			$server_group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where group_id=?",intval($task_data['data']['server_group_id'])));
		} elseif ($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM']=='rand')
		{
			$server_group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=2 and status_id=1 order by rand() limit 1"));
		} elseif (intval($options['DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM'])>0)
		{
			$server_group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where group_id=$options[DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM]"));
		}
		if (intval($server_group_id)==0)
		{
			$server_group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers_groups where content_type_id=2 and status_id=1 order by (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) desc"));
		}
		if (intval($server_group_id)==0)
		{
			mark_task_duration($task_data['task_id'],time()-$task_start_time);
			warn_task_album("No server group found, skipping this task",$album_id,$task_data['task_id']);
			return false;
		}
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]admin_servers where group_id=$server_group_id and status_id=1"))==0)
		{
			mark_task_duration($task_data['task_id'],time()-$task_start_time);
			warn_task_album("No active servers found in server group $server_group_id, skipping this task",$album_id,$task_data['task_id']);
			return false;
		}

		$storage_servers=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=$server_group_id"));
		foreach ($storage_servers as $server)
		{
			if (!test_connection_status($server))
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
			if ($server['free_space']<$options['SERVER_GROUP_MIN_FREE_SPACE_MB']*1024*1024)
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
		}

		mark_task_progress($task_data['task_id'],60);

		// log conversion process
		log_output_album('',$album_id);
		log_output_album($conversion_log,$album_id,1);

		log_output_album('',$album_id);
		log_output_album("INFO  New album creation task is continued for album $album_id [PH-F]",$album_id);

		log_output_album("INFO  Selected server group: $server_group_id",$album_id);
		sql_pr("update $config[tables_prefix]albums set server_group_id=? where album_id=?",intval($server_group_id),$album_id);

		$image_ids=array();
		$image_ids_to_format=array();

		// copy main images
		log_output_album("INFO  Copying main images from conversion server [PH-F-8]",$album_id);
		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/sources"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);
		}
		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);
		}
		foreach ($result_data['images'] as $image_number)
		{
			$image_title='';
			if (isset($task_data['data']['titles']) && $task_data['data']['titles'][$image_number]!='')
			{
				$image_title=$task_data['data']['titles'][$image_number];
			}
			$image_format=get_image_format_id("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$image_number.jpg");

			$image_id=sql_insert("insert into $config[tables_prefix]albums_images set album_id=?, title=?, format=?, added_date=?, rating=?, rating_amount=1",$album_id,$image_title,$image_format,date('Y-m-d H:i:s'),intval($options['ALBUM_INITIAL_RATING']));
			$image_ids[]=$image_id;
			$image_ids_to_format[$image_id]=$image_format;

			copy("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$image_number.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg");

			$image_formats=array();

			$format_rec=array();
			$format_rec['size']='source';
			$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg");
			$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg"));
			$image_formats[]=$format_rec;
			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);}

					if (!get_file("$format[format_album_id]-$image_number.jpg","$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",$server_data))
					{
						cancel_task_album(2,"Failed to get $format[format_album_id]-$image_number.jpg file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
					@rename("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$format[format_album_id]-$image_number.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");

					$format_rec=array();
					$format_rec['size']=$format['size'];
					$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");
					$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg"));
					$image_formats[]=$format_rec;
				}
			}
			sql_pr("update $config[tables_prefix]albums_images set image_formats=? where image_id=?",pack_image_formats($image_formats),$image_id);
		}
		$images_amount=count($image_ids);

		$main_image=1;
		if (intval($task_data['data']['image_main'])>1)
		{
			$main_image=intval($task_data['data']['image_main']);
		}
		if ($main_image>$images_amount)
		{
			$main_image=1;
		}
		$main_image=$image_ids[$main_image-1];

		log_output_album("INFO  Saved $images_amount images",$album_id);
		log_output_album("INFO  Main image is set to $main_image",$album_id);

		mark_task_progress($task_data['task_id'],70);

		// copy preview images
		log_output_album("INFO  Copying preview images from conversion server [PH-F-9]",$album_id);
		if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/preview"))
		{
			mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/preview",0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$album_id/preview",0777);
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==2)
			{
				if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]",0777);}

				if (!get_file("$format[format_album_id]-preview.jpg","$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]",$server_data))
				{
					cancel_task_album(2,"Failed to get $format[format_album_id]-preview.jpg file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
				@rename("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]/$format[format_album_id]-preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]/preview.jpg");
			}
		}

		mark_task_progress($task_data['task_id'],80);

		$zip_files=array();

		// create necessary zip files
		if ($options['ALBUMS_SOURCE_FILES_CREATE_ZIP']==1)
		{
			log_output_album("INFO  Creating ZIP with source files [PH-F-10:source]",$album_id);
			$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/sources";
			$zip_files_to_add=array();
			foreach ($image_ids as $image_id)
			{
				if ($image_ids_to_format[$image_id] && $image_ids_to_format[$image_id]!='jpg')
				{
					copy("$source_folder/$image_id.jpg","$source_folder/$image_id.{$image_ids_to_format[$image_id]}");
					$zip_files_to_add[]="$source_folder/$image_id.{$image_ids_to_format[$image_id]}";
				} else
				{
					$zip_files_to_add[]="$source_folder/$image_id.jpg";
				}
			}
			$zip = new PclZip("$source_folder/$album_id.zip");
			$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

			$zip_rec=array();
			$zip_rec['size']='source';
			$zip_rec['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id.zip"));
			$zip_files[]=$zip_rec;
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1 && $format['is_create_zip']==1)
			{
				log_output_album("INFO  Creating images ZIP for \"$format[title]\" format [PH-F-10:$format[title]]",$album_id);
				$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]";
				$zip_files_to_add=array();
				foreach ($image_ids as $image_id)
				{
					$zip_files_to_add[]="$source_folder/$image_id.jpg";
				}
				$zip = new PclZip("$source_folder/$album_id-$format[size].zip");
				$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

				$zip_rec=array();
				$zip_rec['size']=$format['size'];
				$zip_rec['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id-$format[size].zip"));
				$zip_files[]=$zip_rec;
			}
		}

		mark_task_progress($task_data['task_id'],90);

		// copying all data to storage servers
		$invalidate_files=array();
		$invalidate_folders=array("sources/$dir_path/$album_id");
		log_output_album("INFO  Copying content to storage servers [PH-F-11]",$album_id);
		foreach ($image_ids as $image_id)
		{
			foreach ($storage_servers as $server)
			{
				if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/sources","sources/$dir_path/$album_id",$server))
				{
					cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
			$invalidate_files[]="sources/$dir_path/$album_id/$image_id.jpg";

			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					foreach ($storage_servers as $server)
					{
						if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
						{
							cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
					}
					$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$image_id.jpg";
				}
			}
		}

		if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$album_id.zip"))
		{
			foreach ($storage_servers as $server)
			{
				if (!put_file("$album_id.zip","$config[content_path_albums_sources]/$dir_path/$album_id/sources","sources/$dir_path/$album_id",$server))
				{
					cancel_task_album(4,"Failed to put $album_id.zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
			$invalidate_files[]="sources/$dir_path/$album_id/$album_id.zip";
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1)
			{
				if ($format['is_create_zip']==1)
				{
					foreach ($storage_servers as $server)
					{
						if (!put_file("$album_id-$format[size].zip","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
						{
							cancel_task_album(4,"Failed to put $album_id-$format[size].zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
					}
					$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$album_id-$format[size].zip";
				}
				$invalidate_folders[]="main/$format[size]/$dir_path/$album_id";
				rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]");
			}
			if ($format['group_id']==2)
			{
				foreach ($storage_servers as $server)
				{
					if (!put_file("preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]","preview/$format[size]/$dir_path/$album_id",$server))
					{
						cancel_task_album(4,"Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				$invalidate_folders[]="preview/$format[size]/$dir_path/$album_id";
				$invalidate_files[]="preview/$format[size]/$dir_path/$album_id/preview.jpg";
				rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]");
			}
		}

		$has_preview=0;
		if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg"))
		{
			foreach ($storage_servers as $server)
			{
				if (!put_file("preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id","sources/$dir_path/$album_id",$server))
				{
					cancel_task_album(4,"Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
			$invalidate_files[]="sources/$dir_path/$album_id/preview.jpg";
			$has_preview=1;
			unlink("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg");
		}

		foreach ($storage_servers as $server)
		{
			if ($server['streaming_type_id']==4) // CDN
			{
				cdn_invalidate_album($album_id,$server,$invalidate_folders,$invalidate_files,"add");
			}
		}

		// hook plugins
		foreach ($plugins_on_new as $plugin)
		{
			log_output_album("INFO  Executing $plugin plugin [PH-F-6:$plugin]",$album_id);
			unset($res);
			exec("$config[php_path] $config[project_path]/admin/plugins/$plugin/$plugin.php exec album $album_id 2>&1",$res);
			if ($res[0]<>'')
			{
				log_output_album("....".implode("\n....",$res),$album_id,1);
			} else {
				log_output_album("....no response",$album_id,1);
			}
		}

		// check title duplicates
		if ($options['ALBUMS_DUPLICATE_TITLE_OPTION']==1 && $res_album['title']!='')
		{
			$album_title=$res_album['title'];
			$titles=mr2array_list(sql_pr("select title from $config[tables_prefix]albums where album_id<>$res_album[album_id] and title like ? and status_id in (0,1)","$res_album[title]%"));
			if (in_array($album_title,$titles))
			{
				for ($i=2;$i<999;$i++)
				{
					if (!in_array("$album_title ".str_replace("%NUM%",$i,$options['ALBUMS_DUPLICATE_TITLE_POSTFIX']),$titles))
					{
						$album_title="$album_title ".str_replace("%NUM%",$i,$options['ALBUMS_DUPLICATE_TITLE_POSTFIX']);
						sql_pr("update $config[tables_prefix]albums set title=? where album_id=?",$album_title,$album_id);
						log_output_album("INFO  Replaced album title with \"$album_title\"",$album_id);
						break;
					}
				}
			}
		}

		// delete task on conversion server
		delete_task_folder($task_data['task_id'],$server_data);

		// remove temp files and directories
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/sources");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id");

		if (intval($task_data['data']['status_id']) == 1 && !trim($res_album['title']))
		{
			log_output_album("WARN  Album cannot be activated with empty title", $album_id);
			$task_data['data']['status_id'] = 0;
		}

		// complete task
		sql_pr("update $config[tables_prefix]albums set status_id=?, photos_amount=?, main_photo_id=?, zip_files=?, has_preview=? where album_id=?",intval($task_data['data']['status_id']),$images_amount,$main_image,pack_album_zip_files($zip_files),$has_preview,$album_id);
		sql_pr("update $config[tables_prefix]users set
						public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_albums_count=public_albums_count+private_albums_count+premium_albums_count
					where user_id = ?",$res_album['user_id']
		);

		if (intval($task_data['data']['status_id'])==1)
		{
			$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if (intval($memberzone_data['AWARDS_ALBUM_UPLOAD'])>0 && $images_amount>=intval($memberzone_data['AWARDS_ALBUM_UPLOAD_CONDITION']))
			{
				$anonymous_user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
				if ($res_album['user_id']<>$anonymous_user_id)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=5, user_id=?, album_id=?, tokens_granted=?, added_date=?",$res_album['user_id'],$album_id,intval($memberzone_data['AWARDS_ALBUM_UPLOAD']),date('Y-m-d H:i:s'));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_ALBUM_UPLOAD']),$res_album['user_id']);
				}
			}
		}

		$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_albums where album_id=$album_id")));
		$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_albums where album_id=$album_id")));
		$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_albums where album_id=$album_id")));
		update_categories_albums_totals($list_ids_categories);
		update_models_albums_totals($list_ids_models);
		update_tags_albums_totals($list_ids_tags);
		update_content_sources_albums_totals(array($res_album['content_source_id']));

		log_output_album("INFO  New album creation task is completed for album $album_id [PH-FE]",$album_id);
		mark_task_duration($task_data['task_id'],time()-$task_start_time+$task_conversion_duration);
		finish_task($task_data);
	}

	return false;
}

function exec_delete_album($task_data, $formats_albums)
{
	global $config, $conversion_servers;

	$task_start_time = time();

	$album_id = intval($task_data['album_id']);
	$res_album = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=?", $album_id));
	if (!isset($res_album))
	{
		cancel_task_album(1, "Album $album_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$storage_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_album['server_group_id']));
	foreach ($storage_servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	$is_soft_delete = intval($task_data['data']['soft_delete']);

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output_album('', $album_id, 1, 1);
	log_output_album("INFO  Album removal task is started for album $album_id [PH-P]", $album_id);
	if ($is_soft_delete == 1)
	{
		log_output_album("INFO  Album is only marked as deleted", $album_id);
	}

	$dir_path = get_dir_by_id($album_id);
	foreach ($storage_servers as $server)
	{
		if (!delete_dir("sources/$dir_path/$album_id", $server))
		{
			log_output_album("WARN  Failed to delete directory sources/$dir_path/$album_id on storage server \"$server[title]\"", $album_id);
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id'] == 1)
			{
				if (!delete_dir("main/$format[size]/$dir_path/$album_id", $server))
				{
					log_output_album("WARN  Failed to delete directory main/$format[size]/$dir_path/$album_id on storage server \"$server[title]\"", $album_id);
				}
			}
			if ($format['group_id'] == 2)
			{
				if (!delete_dir("preview/$format[size]/$dir_path/$album_id", $server))
				{
					log_output_album("WARN  Failed to delete directory preview/$format[size]/$dir_path/$album_id on storage server \"$server[title]\"", $album_id);
				}
			}
		}
		if ($server['streaming_type_id'] == 4) // CDN
		{
			$image_ids = mr2array_list(sql_pr("select image_id from $config[tables_prefix]albums_images where album_id=? order by image_id asc", $album_id));

			$invalidate_folders = ["sources/$dir_path/$album_id"];
			$invalidate_files = [];
			foreach ($image_ids as $image_id)
			{
				$invalidate_files[] = "sources/$dir_path/$album_id/$image_id.jpg";
			}
			$invalidate_files[] = "sources/$dir_path/$album_id/$album_id.zip";
			$invalidate_files[] = "sources/$dir_path/$album_id/preview.jpg";
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 1)
				{
					$invalidate_folders[] = "main/$format[size]/$dir_path/$album_id";
					foreach ($image_ids as $image_id)
					{
						$invalidate_files[] = "main/$format[size]/$dir_path/$album_id/$image_id.jpg";
					}
					$invalidate_files[] = "main/$format[size]/$dir_path/$album_id/$album_id-$format[size].zip";
				}
				if ($format['group_id'] == 2)
				{
					$invalidate_folders[] = "preview/$format[size]/$dir_path/$album_id";
					$invalidate_files[] = "preview/$format[size]/$dir_path/$album_id/preview.jpg";
				}
			}
			cdn_invalidate_album($album_id, $server, $invalidate_folders, $invalidate_files, 'delete');
		}
	}

	foreach ($formats_albums as $format)
	{
		if ($format['group_id'] == 1)
		{
			rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]");
		}
		if ($format['group_id'] == 2)
		{
			rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]");
		}
	}

	$folders = get_contents_from_dir("$config[content_path_albums_sources]/$dir_path/$album_id", 2);
	foreach ($folders as $folder)
	{
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/$folder");
	}
	rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id");

	$list_ids_comments_str = implode(",", array_map("intval", mr2array_list(sql_pr("select distinct user_id from $config[tables_prefix]comments where object_id=? and object_type_id=2", $album_id))));

	$list_ids_categories = array_map("intval", mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_albums where album_id=?", $album_id)));
	$list_ids_models = array_map("intval", mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_albums where album_id=?", $album_id)));
	$list_ids_tags = array_map("intval", mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_albums where album_id=?", $album_id)));

	sql_delete("delete from $config[tables_prefix]albums_images where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]stats_albums where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]users_events where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]fav_albums where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]rating_history where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]flags_albums where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]flags_history where album_id=?", $album_id);
	sql_delete("delete from $config[tables_prefix]flags_messages where album_id=?", $album_id);

	sql_update("update $config[tables_prefix]users_purchases set expiry_date=? where album_id=?", date('Y-m-d H:i:s'), $album_id);

	sql_update("update $config[tables_prefix]users set
					public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_albums_count=public_albums_count+private_albums_count+premium_albums_count
				where user_id = ?", $res_album['user_id']
	);

	if ($is_soft_delete == 0)
	{
		sql_delete("delete from $config[tables_prefix]albums where album_id=?", $album_id);
		sql_delete("delete from $config[tables_prefix]comments where object_id=? and object_type_id=2", $album_id);
		sql_delete("delete from $config[tables_prefix]categories_albums where album_id=?", $album_id);
		sql_delete("delete from $config[tables_prefix]models_albums where album_id=?", $album_id);
		sql_delete("delete from $config[tables_prefix]tags_albums where album_id=?", $album_id);
	} else
	{
		sql_update("update $config[tables_prefix]albums set zip_files='', favourites_count=0, purchases_count=0, photos_amount=0, server_group_id=0, admin_user_id=0, admin_flag_id=0, has_errors=0 where album_id=?", $album_id);
	}

	if ($list_ids_comments_str)
	{
		sql_pr("update $config[tables_prefix]users set
						comments_albums_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=2),
						comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
					where user_id in ($list_ids_comments_str)"
		);
	}
	update_categories_albums_totals($list_ids_categories);
	update_models_albums_totals($list_ids_models);
	update_tags_albums_totals($list_ids_tags);
	update_content_sources_albums_totals([$res_album['content_source_id']]);

	$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
	$album_url = '';
	if ($res_album['dir'] != '')
	{
		$album_url = "$config[project_url]/" . str_replace("%ID%", $album_id, str_replace("%DIR%", $res_album['dir'], $website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
	}
	sql_insert("insert into $config[tables_prefix]deleted_content set object_id=?, object_type_id=2, dir=?, url=?, external_key=?, deleted_date=?", $album_id, trim($res_album['dir']), $album_url, trim($res_album['external_key']), date('Y-m-d H:i:s'));

	inc_block_version_admin('albums_info', 'album', $res_album['album_id'], $res_album['dir']);

	$running_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where album_id=? and server_id>0", $album_id));
	foreach ($running_tasks as $running_task)
	{
		if (isset($conversion_servers[$running_task['server_id']]))
		{
			delete_task_folder($running_task['task_id'], $conversion_servers[$running_task['server_id']]);
		}
		@unlink("$config[project_path]/admin/data/engine/tasks/{$running_task['task_id']}.dat");
		@unlink("$config[project_path]/admin/data/engine/tasks/{$running_task['task_id']}_duration.dat");
	}

	log_output_album("INFO  Album removal task is completed for album $album_id [PH-FE]", $album_id);
	finish_task($task_data, time() - $task_start_time);

	sql_delete("delete from $config[tables_prefix]background_tasks where album_id=?", $album_id);
	return false;
}

function exec_create_format_albums($task_data)
{
	global $config, $options;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where format_album_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task_album(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]albums)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Format creation task is continued for album format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Format creation task is started for album format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1)"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1) and album_id<=?", intval($task_data['last_processed_id'])));

	if (intval($options['ALBUMS_CROP_CUSTOMIZE']) > 0)
	{
		$custom_field_id = 'custom' . intval($options['ALBUMS_CROP_CUSTOMIZE']);
		$albums = mr2array(sql_pr("select a.album_id, a.server_group_id, a.has_preview, a.main_photo_id, a.zip_files, coalesce(c.$custom_field_id, '') as custom_crop_options from $config[tables_prefix]albums a left join $config[tables_prefix]content_sources c on a.content_source_id=c.content_source_id where a.status_id in (0,1) and a.album_id>? order by a.album_id asc limit $iteration_step", intval($task_data['last_processed_id'])));
	} else
	{
		$albums = mr2array(sql_pr("select a.album_id, a.server_group_id, a.has_preview, a.main_photo_id, a.zip_files from $config[tables_prefix]albums a where a.status_id in (0,1) and a.album_id>? order by a.album_id asc limit $iteration_step", intval($task_data['last_processed_id'])));
	}

	$failed_albums = [];
	foreach ($albums as $album)
	{
		$album_id = $album['album_id'];
		$dir_path = get_dir_by_id($album_id);

		$last_object_id = $album_id;

		$images_source_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]";
		if (!mkdir_recursive($images_source_dir))
		{
			$failed_albums[] = ['album_id' => $album_id, 'filesystem' => $images_source_dir];
			continue;
		}

		$images_target_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$res_format[size]";
		if (!mkdir_recursive($images_target_dir))
		{
			$failed_albums[] = ['album_id' => $album_id, 'filesystem' => $images_target_dir];
			continue;
		}

		$is_album_skipped = true;
		try
		{
			if ($res_format['group_id'] == 1)
			{
				$invalidate_files = [];
				$zip_files = get_album_zip_files($album_id, $album['zip_files']);

				$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=? order by image_id asc", $album_id));
				foreach ($images as $image)
				{
					$image_id = $image['image_id'];
					$image_formats = get_image_formats($album_id, $image['image_formats']);

					if ($task_data['data']['recreate'] == 1 || !isset($image_formats[$res_format['size']]))
					{
						$is_album_skipped = false;

						foreach ($storage_servers[$album['server_group_id']] as $server)
						{
							if (get_file("$image_id.jpg", "sources/$dir_path/$album_id", $images_source_dir, $server))
							{
								break;
							}
						}
						if (!is_file("$images_source_dir/$image_id.jpg"))
						{
							$failed_albums[] = ['album_id' => $album_id, 'get' => "$image_id.jpg"];
							throw new RuntimeException('Get file failed');
						}

						$exec_res = make_image_from_source("$images_source_dir/$image_id.jpg", "$images_target_dir/$image_id.jpg", $res_format, $options, $album['custom_crop_options']);
						if ($exec_res)
						{
							$failed_albums[] = ['album_id' => $album_id, 'exec' => $exec_res];
							throw new RuntimeException('Exec failed');
						}

						foreach ($storage_servers[$album['server_group_id']] as $server)
						{
							if (!put_file("$image_id.jpg", $images_target_dir, "main/$res_format[size]/$dir_path/$album_id", $server))
							{
								$failed_albums[] = ['album_id' => $album_id, 'put' => "$image_id.jpg"];
								throw new RuntimeException('Put file failed');
							}
						}
						$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$image_id.jpg";

						$had_format = false;
						foreach ($image_formats as $k => $v)
						{
							if ($v['size'] == $res_format['size'])
							{
								$image_formats[$k]['dimensions'] = getimagesize("$images_target_dir/$image_id.jpg");
								$image_formats[$k]['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$image_id.jpg"));
								$had_format = true;
								break;
							}
						}
						if (!$had_format)
						{
							$new_format = [];
							$new_format['size'] = $res_format['size'];
							$new_format['dimensions'] = getimagesize("$images_target_dir/$image_id.jpg");
							$new_format['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$image_id.jpg"));
							$image_formats[] = $new_format;
						}
						sql_update("update $config[tables_prefix]albums_images set image_formats=? where image_id=?", pack_image_formats($image_formats), $image_id);
						usleep(2000);
					}

					if ($res_format['is_create_zip'] == 1)
					{
						if ($task_data['data']['recreate'] == 1 || !isset($zip_files[$res_format['size']]))
						{
							if (!is_file("$images_target_dir/$image_id.jpg"))
							{
								foreach ($storage_servers[$album['server_group_id']] as $server)
								{
									if (get_file("$image_id.jpg", "main/$res_format[size]/$dir_path/$album_id", $images_target_dir, $server))
									{
										break;
									}
								}
								if (!is_file("$images_target_dir/$image_id.jpg"))
								{
									$failed_albums[] = ['album_id' => $album_id, 'get' => "$image_id.jpg"];
									throw new RuntimeException('Get file failed');
								}
							}
						}
					}
				}

				if ($res_format['is_create_zip'] == 1)
				{
					if ($task_data['data']['recreate'] == 1 || !isset($zip_files[$res_format['size']]))
					{
						$is_album_skipped = false;

						$zip_files_to_add = [];
						foreach ($images as $image)
						{
							$zip_files_to_add[] = "$images_target_dir/$image[image_id].jpg";
						}
						$zip = new PclZip("$images_target_dir/$album_id-$res_format[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $images_target_dir);

						foreach ($storage_servers[$album['server_group_id']] as $server)
						{
							if (!put_file("$album_id-$res_format[size].zip", $images_target_dir, "main/$res_format[size]/$dir_path/$album_id", $server))
							{
								$failed_albums[] = ['album_id' => $album_id, 'put' => "$album_id-$res_format[size].zip"];
								throw new RuntimeException('Put file failed');
							}
						}
						$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$album_id-$res_format[size].zip";

						$had_zip = false;
						foreach ($zip_files as $k => $v)
						{
							if ($v['size'] == $res_format['size'])
							{
								$zip_files[$k]['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$album_id-$res_format[size].zip"));
								$had_zip = true;
								break;
							}
						}
						if (!$had_zip)
						{
							$new_zip = [];
							$new_zip['size'] = $res_format['size'];
							$new_zip['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$album_id-$res_format[size].zip"));
							$zip_files[] = $new_zip;
						}
						sql_update("update $config[tables_prefix]albums set zip_files=? where album_id=?", pack_album_zip_files($zip_files), $album_id);
					}
				}

				if (count($invalidate_files) > 0)
				{
					foreach ($storage_servers[$album['server_group_id']] as $server)
					{
						if ($server['streaming_type_id'] == 4) // CDN
						{
							cdn_invalidate_album($album_id, $server, ["main/$res_format[size]/$dir_path/$album_id"], $invalidate_files, 'add');
						}
					}
				}
			} elseif ($res_format['group_id'] == 2)
			{
				$has_format = true;
				if ($task_data['data']['recreate'] == 1)
				{
					$has_format = false;
				} else
				{
					foreach ($storage_servers[$album['server_group_id']] as $server)
					{
						if (check_file("preview.jpg", "preview/$res_format[size]/$dir_path/$album_id", $server) == 0)
						{
							$has_format = false;
							break;
						}
					}
				}

				if (!$has_format)
				{
					$is_album_skipped = false;

					$image_id = $album['main_photo_id'];
					$preview_source = "$image_id.jpg";
					if ($album['has_preview'] == 1)
					{
						$preview_source = "preview.jpg";
					}

					if (!is_file("$images_source_dir/$preview_source"))
					{
						foreach ($storage_servers[$album['server_group_id']] as $server)
						{
							if (get_file($preview_source, "sources/$dir_path/$album_id", $images_source_dir, $server))
							{
								break;
							}
						}
						if (!is_file("$images_source_dir/$preview_source"))
						{
							$failed_albums[] = ['album_id' => $album_id, 'get' => $preview_source];
							throw new RuntimeException('Get file failed');
						}
					}

					$exec_res = make_image_from_source("$images_source_dir/$preview_source", "$images_target_dir/preview.jpg", $res_format, $options, $album['custom_crop_options']);
					if ($exec_res)
					{
						$failed_albums[] = ['album_id' => $album_id, 'exec' => $exec_res];
						throw new RuntimeException('Exec failed');
					}

					foreach ($storage_servers[$album['server_group_id']] as $server)
					{
						if (!put_file("preview.jpg", $images_target_dir, "preview/$res_format[size]/$dir_path/$album_id", $server))
						{
							$failed_albums[] = ['album_id' => $album_id, 'put' => "preview.jpg"];
							throw new RuntimeException('Put file failed');
						}
						if ($server['streaming_type_id'] == 4) // CDN
						{
							cdn_invalidate_album($album_id, $server, ["preview/$res_format[size]/$dir_path/$album_id"], ["preview/$res_format[size]/$dir_path/$album_id/preview.jpg"], 'add');
						}
					}
				}
			}
		} catch (Exception $e)
		{
			if (rmdir_recursive($images_target_dir) && rmdir_recursive($images_source_dir))
			{
				@rmdir(dirname($images_source_dir));
			}
			continue;
		}

		if (rmdir_recursive($images_target_dir) && rmdir_recursive($images_source_dir))
		{
			@rmdir(dirname($images_source_dir));
		}

		if (!$is_album_skipped)
		{
			log_output_album('', $album_id, 1, 1);
			log_output_album("INFO  Created album format \"$res_format[title]\" for album $album_id", $album_id);
		}

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if (count($failed_albums) > 0)
	{
		// fail task
		$failed_albums_ids = [];
		foreach ($failed_albums as $album_failure_rec)
		{
			$failed_albums_ids[] = $album_failure_rec['album_id'];
			log_output_album('', $album_failure_rec['album_id'], 1, 1);
			if ($album_failure_rec['get'])
			{
				log_output_album("WARN  Failed to create album format \"$res_format[title]\" for album $album_failure_rec[album_id]: failed to get $album_failure_rec[get] file from storage servers", $album_failure_rec['album_id']);
			} elseif ($album_failure_rec['put'])
			{
				log_output_album("WARN  Failed to create album format \"$res_format[title]\" for album $album_failure_rec[album_id]: failed to put $album_failure_rec[put] file to storage servers", $album_failure_rec['album_id']);
			} elseif ($album_failure_rec['filesystem'])
			{
				log_output_album("WARN  Failed to create album format \"$res_format[title]\" for album $album_failure_rec[album_id]: failed to create directory $album_failure_rec[filesystem]", $album_failure_rec['album_id']);
			} elseif ($album_failure_rec['exec'])
			{
				log_output_album("WARN  Failed to create album format \"$res_format[title]\" for album $album_failure_rec[album_id]: $album_failure_rec[exec]", $album_failure_rec['album_id']);
			}
		}

		log_output_album("WARN  Albums with errors are: " . implode(', ', $failed_albums_ids));
		cancel_task_album(8, "Error during images creation for album format \"$res_format[title]\" for some albums, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output_album("INFO  Iteration processed $last_iteration_processed albums [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output_album("INFO  Iteration processed $last_iteration_processed albums");
		log_output_album("INFO  Format creation task is completed for album format \"$res_format[title]\" [PH-FE]");
		sql_update("update $config[tables_prefix]formats_albums set status_id=1 where format_album_id=?", $format_id);
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_delete_format_albums($task_data)
{
	global $config;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where format_album_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task_album(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]albums)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output_album("INFO  Format removal task is continued for album format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output_album("INFO  Format removal task is started for album format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where album_id<=?", intval($task_data['last_processed_id'])));

	$dir_paths_to_delete = [];
	if ($res_format['group_id'] == 1)
	{
		$root_folder = 'main';
	} else
	{
		$root_folder = 'preview';
	}

	$albums = mr2array(sql_pr("select album_id, server_group_id, zip_files from $config[tables_prefix]albums where album_id>? order by album_id asc limit ?", intval($task_data['last_processed_id']), $iteration_step));
	foreach ($albums as $album)
	{
		$album_id = $album['album_id'];
		$dir_path = get_dir_by_id($album_id);

		$last_object_id = $album_id;

		if (!isset($storage_servers[$album['server_group_id']]))
		{
			continue;
		}

		foreach ($storage_servers[$album['server_group_id']] as $server)
		{
			delete_dir("$root_folder/$res_format[size]/$dir_path/$album_id", $server);
			$dir_paths_to_delete[intval($dir_path)] = $dir_path;
		}

		if ($res_format['group_id'] == 1)
		{
			$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=?", $album_id));
			foreach ($images as $image)
			{
				$image_formats = get_image_formats($album_id, $image['image_formats']);
				unset($image_formats[$res_format['size']]);
				sql_update("update $config[tables_prefix]albums_images set image_formats=? where image_id=?", pack_image_formats($image_formats), $image['image_id']);
			}

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			if (isset($zip_files[$res_format['size']]))
			{
				unset($zip_files[$res_format['size']]);
				sql_update("update $config[tables_prefix]albums set zip_files=? where album_id=?", pack_album_zip_files($zip_files), $album_id);
			}

			foreach ($storage_servers[$album['server_group_id']] as $server)
			{
				if ($server['streaming_type_id'] == 4) // CDN
				{
					$invalidate_files = [];
					foreach ($images as $image)
					{
						$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$image[image_id].jpg";
					}
					if ($res_format['is_create_zip'] == 1)
					{
						$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$album_id-$res_format[size].zip";
					}
					cdn_invalidate_album($album_id, $server, ["main/$res_format[size]/$dir_path/$album_id"], $invalidate_files, 'delete');
				}
			}
		} elseif ($res_format['group_id'] == 2)
		{
			foreach ($storage_servers[$album['server_group_id']] as $server)
			{
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_album($album_id, $server, [], ["preview/$res_format[size]/$dir_path/$album_id/preview.jpg"], 'delete');
				}
			}
		}

		log_output_album('', $album_id, 1, 1);
		log_output_album("INFO  Removed album format \"$res_format[title]\" for album $album_id", $album_id);

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	foreach ($storage_servers as $servers)
	{
		foreach ($servers as $server)
		{
			foreach ($dir_paths_to_delete as $dir_path)
			{
				delete_dir("$root_folder/$res_format[size]/$dir_path", $server);
			}
			delete_dir("$root_folder/$res_format[size]", $server);
		}
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed albums [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed albums");
		log_output("INFO  Format removal task is completed for album format \"$res_format[title]\" [PH-FE]");
		sql_delete("delete from $config[tables_prefix]formats_albums where format_album_id=?", $format_id);
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_upload_album_images($task_data,$server_data,$formats_albums)
{
	global $config,$options;

	$task_start_time=time();

	$album_id=intval($task_data['album_id']);
	$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));
	if (!isset($res_album))
	{
		cancel_task_album(1,"Album $album_id is not available in the database, cancelling this task",0,$task_data['task_id'],$server_data);
		return false;
	}

	$dir_path=get_dir_by_id($album_id);
	$server_data=mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?",intval($server_data['server_id'])));

	$source_file=$task_data['data']['source_file'];
	$source_files=array();
	if ($source_file<>'')
	{
		$source_files[]=$source_file;
	} else {
		$source_files=$task_data['data']['source_files'];
	}

	$custom_crop_options='';
	if (intval($options['ALBUMS_CROP_CUSTOMIZE'])>0 && $res_album['content_source_id']>0)
	{
		$res_content_source=mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?",$res_album['content_source_id']));
		$custom_crop_options=$res_content_source["custom{$options['ALBUMS_CROP_CUSTOMIZE']}"];
	}

	if ($task_data['status_id']==0)
	{
		log_output_album('',$album_id, 1, 1);
		log_output_album("INFO  Album images uploading task is started for album $album_id [PH-P]",$album_id);

		if (!isset($server_data))
		{
			mark_task_duration($task_data['task_id'],time()-$task_start_time);
			warn_task_album("Conversion server is not available in the database, skipping this task",0,$task_data['task_id']);
			return false;
		}

		if (@count($source_files)==0)
		{
			cancel_task_album(9,"No source file(s) uploaded, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		foreach ($source_files as $source_file)
		{
			if (!is_file("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file"))
			{
				cancel_task_album(9,"Source file $source_file does not exist",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}

		mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);
		chmod("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);

		$images_amount=1;
		foreach ($source_files as $source_file)
		{
			$ext=strtolower(end(explode(".",$source_file)));
			if ($ext=='zip')
			{
				// extract images and copy them to source directory
				$zip = new PclZip("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file");
				$data=process_zip_images($zip->listContent());
				foreach ($data as $v)
				{
					$file_base_name=$v['filename'];
					$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
					$fstream=$content[0]['content'];
					if ($fstream=='')
					{
						cancel_task_album(9,"Failed to extract $file_base_name from ZIP, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
					$fp=fopen("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$images_amount.jpg","w");
					fwrite($fp,$fstream);
					fclose($fp);
					$images_amount++;
				}
			} else {
				copy("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$images_amount.jpg");
				$images_amount++;
			}
		}
		$images_amount--;

		if ($images_amount==0)
		{
			cancel_task_album(9,"No images uploaded, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		log_output_album("INFO  Preparing task for conversion server [PH-P-2]",$album_id);

		for ($i=1;$i<=$images_amount;$i++)
		{
			$orientation_res=correct_orientation("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$i.jpg");
			if (intval($orientation_res)>0)
			{
				log_output_album("INFO  Image $i is not oriented properly, EXIF info: ".intval($orientation_res),$album_id);
			} elseif ($orientation_res!=0) {
				log_output_album("ERROR IM operation failed: $orientation_res",$album_id);
				cancel_task_album(9,"Failed to change orientation",$album_id,$task_data['task_id'],$server_data);
			}
		}

		for ($i=1;$i<=$images_amount;$i++)
		{
			if (!put_file("$i.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]","$task_data[task_id]",$server_data))
			{
				cancel_task_album(2,"Failed to put $i.jpg file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}

		$task_info=array();
		$task_info['album_id']=$album_id;
		$task_info['source_images_count']=$images_amount;
		$task_info['options']['PROCESS_PRIORITY']=$options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['ALBUMS_CROP_LEFT_UNIT']=$options['ALBUMS_CROP_LEFT_UNIT'];
		$task_info['options']['ALBUMS_CROP_RIGHT_UNIT']=$options['ALBUMS_CROP_RIGHT_UNIT'];
		$task_info['options']['ALBUMS_CROP_TOP_UNIT']=$options['ALBUMS_CROP_TOP_UNIT'];
		$task_info['options']['ALBUMS_CROP_BOTTOM_UNIT']=$options['ALBUMS_CROP_BOTTOM_UNIT'];
		$task_info['options']['ALBUMS_CROP_LEFT']=$options['ALBUMS_CROP_LEFT'];
		$task_info['options']['ALBUMS_CROP_RIGHT']=$options['ALBUMS_CROP_RIGHT'];
		$task_info['options']['ALBUMS_CROP_TOP']=$options['ALBUMS_CROP_TOP'];
		$task_info['options']['ALBUMS_CROP_BOTTOM']=$options['ALBUMS_CROP_BOTTOM'];
		$task_info['options']['ALBUMS_CROP_CUSTOMIZE']=$custom_crop_options;
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']=$config['imagemagick_default_jpeg_quality'];

		if ($config['installation_type']>=3)
		{
			$task_info['options']['PROCESS_PRIORITY']=intval($server_data['process_priority']);
		}
		log_output_album("INFO  Conversion priority level is set to ".$task_info['options']['PROCESS_PRIORITY'],$album_id);

		$task_info['formats_albums']=$formats_albums;
		foreach ($formats_albums as $format)
		{
			if (is_file("$config[project_path]/admin/data/other/watermark_album_{$format['format_album_id']}.png"))
			{
				if (!put_file("watermark_album_{$format['format_album_id']}.png","$config[project_path]/admin/data/other","$task_data[task_id]",$server_data))
				{
					cancel_task_album(2,"Failed to put watermark_album_{$format['format_album_id']}.png file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
		}

		$fp=fopen("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat","w");
		fwrite($fp,serialize($task_info));
		fclose($fp);

		if (!put_file('task.dat',"$config[content_path_albums_sources]/$dir_path/$album_id","$task_data[task_id]",$server_data))
		{
			cancel_task_album(2,"Failed to put task.dat file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		unlink("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat");

		log_output_album("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]",$album_id);
		sql_pr("update $config[tables_prefix]background_tasks set status_id=1, server_id=$server_data[server_id] where task_id=$task_data[task_id]");
		mark_task_progress($task_data['task_id'],10);
		mark_task_duration($task_data['task_id'],time()-$task_start_time);
		return true;
	} else {
		// check conversion task
		if (!isset($server_data))
		{
			cancel_task_album(1,"Conversion server $task_data[server_id] is not available in the database, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		if (check_file('progress.dat', "$task_data[task_id]", $server_data))
		{
			get_file('progress.dat', "$task_data[task_id]", "$config[content_path_albums_sources]/$dir_path/$album_id", $server_data);
			if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat"))
			{
				mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat")) * 0.5));
				unlink("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat");
			}
		}

		if (check_file('result.dat',"$task_data[task_id]",$server_data)==0)
		{
			if (check_file('task.dat',"$task_data[task_id]",$server_data)>0)
			{
				return false;
			} else {
				if (test_connection($server_data)===true)
				{
					cancel_task_album(2,"Task directory is not available on conversion server, cancelling this task",$album_id,$task_data['task_id']);
				} else {
					warn_task_album("Conversion server connection is lost, skipping this task",$album_id,$task_data['task_id']);
				}
				return false;
			}
		}

		// check result file
		if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
		{
			cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
		if (!is_array($result_data))
		{
			sleep(1);
			if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
			{
				cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
			if (!is_array($result_data))
			{
				cancel_task_album(6,"Unexpected error on conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}
		$task_conversion_duration = intval($result_data['duration']);
		@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat");

		// check log file
		$conversion_log='';
		if (check_file('log.txt',"$task_data[task_id]",$server_data)>0)
		{
			if (!get_file('log.txt',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
			{
				cancel_task_album(2,"Failed to get log.txt file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}

			if (sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt")) > 10 * 1000 * 1000)
			{
				$conversion_log = 'Conversion log is more than 10mb';
			} else
			{
				$conversion_log = trim(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt"));
			}
		}
		if ($conversion_log === '')
		{
			cancel_task_album(3,"No conversion log is available, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt");

		// check if conversion result contains any error
		if ($result_data['is_error']==1)
		{
			log_output_album('',$album_id);
			log_output_album($conversion_log,$album_id,1);
			cancel_task_album(intval($result_data['error_code'])>0?$result_data['error_code']:7,$result_data['error_message']!=''?$result_data['error_message']:"Conversion error, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}

		$storage_servers=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=$res_album[server_group_id]"));
		foreach ($storage_servers as $server)
		{
			if (!test_connection_status($server))
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
			if ($server['free_space']<$options['SERVER_GROUP_MIN_FREE_SPACE_MB']*1024*1024)
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
		}

		mark_task_progress($task_data['task_id'],60);

		// log conversion process
		log_output_album('',$album_id);
		log_output_album($conversion_log,$album_id,1);

		log_output_album('',$album_id);
		log_output_album("INFO  Album images uploading task is continued for album $album_id [PH-F]",$album_id);

		// copy main formats
		log_output_album("INFO  Copying main images from conversion server [PH-F-8]",$album_id);
		mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);
		chmod("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);
		mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);
		chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);
		$image_ids=array();
		foreach ($result_data['images'] as $image_number)
		{
			$image_title='';
			if (isset($task_data['data']['titles']) && $task_data['data']['titles'][$image_number]!='')
			{
				$image_title=$task_data['data']['titles'][$image_number];
			}
			$image_format=get_image_format_id("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$image_number.jpg");

			$image_id=sql_insert("insert into $config[tables_prefix]albums_images set album_id=?, title=?, format=?, added_date=?, rating=?, rating_amount=1",$album_id,$image_title,$image_format,date('Y-m-d H:i:s'),intval($options['ALBUM_INITIAL_RATING']));
			$image_ids[]=$image_id;

			copy("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$image_number.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg");

			$image_formats=array();

			$format_rec=array();
			$format_rec['size']='source';
			$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg");
			$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$image_id.jpg"));
			$image_formats[]=$format_rec;
			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);}

					if (!get_file("$format[format_album_id]-$image_number.jpg","$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",$server_data))
					{
						cancel_task_album(2,"Failed to get $format[format_album_id]-$image_number.jpg file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
					@rename("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$format[format_album_id]-$image_number.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");

					$format_rec=array();
					$format_rec['size']=$format['size'];
					$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");
					$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg"));
					$image_formats[]=$format_rec;
				}
			}
			sql_pr("update $config[tables_prefix]albums_images set image_formats=? where image_id=?",pack_image_formats($image_formats),$image_id);
		}
		$images_amount=count($image_ids);
		log_output_album("INFO  Saved $images_amount images",$album_id);

		mark_task_progress($task_data['task_id'],70);

		log_output_album("INFO  Copying new content to storage servers [PH-F-11]",$album_id);
		$invalidate_files=array();
		foreach ($image_ids as $image_id)
		{
			foreach ($storage_servers as $server)
			{
				if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/sources","sources/$dir_path/$album_id",$server))
				{
					cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
			$invalidate_files[]="sources/$dir_path/$album_id/$image_id.jpg";

			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					foreach ($storage_servers as $server)
					{
						if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
						{
							cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
					}
					$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$image_id.jpg";
				}
			}
		}
		if (count($invalidate_files)>0)
		{
			foreach ($storage_servers as $server)
			{
				if ($server['streaming_type_id']==4) // CDN
				{
					cdn_invalidate_album($album_id,$server,array(),$invalidate_files,"add");
				}
			}
		}

		mark_task_progress($task_data['task_id'],80);

		$zip_files=get_album_zip_files($album_id,$res_album['zip_files']);

		// update necessary zip files
		$images=mr2array(sql_pr("select image_id, format from $config[tables_prefix]albums_images where album_id=$album_id"));
		if (isset($zip_files['source']))
		{
			log_output_album("INFO  Updating ZIP with source files [PH-F-10:source]",$album_id);
			$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/sources";
			$zip_files_to_add=array();
			foreach ($images as $image)
			{
				$image_id=$image['image_id'];
				if (!is_file("$source_folder/$image_id.jpg"))
				{
					foreach ($storage_servers as $server)
					{
						if (get_file("$image_id.jpg","sources/$dir_path/$album_id",$source_folder,$server))
						{
							break;
						}
					}
					if (!is_file("$source_folder/$image_id.jpg"))
					{
						cancel_task_album(4,"Failed to get $image_id.jpg file from storage servers, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				if ($image['format'] && $image['format']!='jpg')
				{
					copy("$source_folder/$image_id.jpg","$source_folder/$image_id.{$image['format']}");
					$zip_files_to_add[]="$source_folder/$image_id.{$image['format']}";
				} else
				{
					$zip_files_to_add[]="$source_folder/$image_id.jpg";
				}
			}
			$zip = new PclZip("$source_folder/$album_id.zip");
			$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

			$zip_files['source']['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id.zip"));
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1 && isset($zip_files[$format['size']]))
			{
				log_output_album("INFO  Updating images ZIP for \"$format[title]\" format [PH-F-10:$format[title]]",$album_id);
				$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]";
				$zip_files_to_add=array();
				foreach ($images as $image)
				{
					$image_id=$image['image_id'];
					if (!is_file("$source_folder/$image_id.jpg"))
					{
						foreach ($storage_servers as $server)
						{
							if (get_file("$image_id.jpg","main/$format[size]/$dir_path/$album_id",$source_folder,$server))
							{
								break;
							}
						}
						if (!is_file("$source_folder/$image_id.jpg"))
						{
							cancel_task_album(4,"Failed to get $image_id.jpg file from storage servers, cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
					}
					$zip_files_to_add[]="$source_folder/$image_id.jpg";
				}
				$zip = new PclZip("$source_folder/$album_id-$format[size].zip");
				$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

				$zip_files[$format['size']]['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id-$format[size].zip"));
			}
		}

		mark_task_progress($task_data['task_id'],90);

		log_output_album("INFO  Copying ZIP files to storage servers [PH-F-11]",$album_id);
		$invalidate_files=array();
		if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$album_id.zip"))
		{
			foreach ($storage_servers as $server)
			{
				if (!put_file("$album_id.zip","$config[content_path_albums_sources]/$dir_path/$album_id/sources","sources/$dir_path/$album_id",$server))
				{
					cancel_task_album(4,"Failed to put $album_id.zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
				$invalidate_files[]="sources/$dir_path/$album_id/$album_id.zip";
			}
		}
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1)
			{
				if ($format['is_create_zip']==1)
				{
					foreach ($storage_servers as $server)
					{
						if (!put_file("$album_id-$format[size].zip","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
						{
							cancel_task_album(4,"Failed to put $album_id-$format[size].zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
						$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$album_id-$format[size].zip";
					}
				}
				rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]");
			}
		}
		if (count($invalidate_files)>0)
		{
			foreach ($storage_servers as $server)
			{
				if ($server['streaming_type_id']==4) // CDN
				{
					cdn_invalidate_album($album_id,$server,array(),$invalidate_files,"change");
				}
			}
		}

		// delete task on conversion server
		delete_task_folder($task_data['task_id'],$server_data);

		// remove temp files and directories
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/sources");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]");
		foreach ($source_files as $source_file)
		{
			@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/$source_file");
		}
		@rmdir("$config[content_path_albums_sources]/$dir_path/$album_id");

		// complete task
		sql_pr("update $config[tables_prefix]albums set zip_files=?, photos_amount=(select count(*) from $config[tables_prefix]albums_images where $config[tables_prefix]albums.album_id=$config[tables_prefix]albums_images.album_id) where album_id=?",pack_album_zip_files($zip_files),$album_id);

		log_output_album("INFO  Album images uploading task is completed for album $album_id [PH-FE]",$album_id);
		mark_task_duration($task_data['task_id'],time()-$task_start_time+$task_conversion_duration);
		finish_task($task_data);
	}

	return false;
}

function exec_create_zip_screenshots($task_data, $formats_videos)
{
	global $config;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  ZIP creation task is continued for screenshot format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  ZIP creation task is started for screenshot format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1)"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and video_id<=?", intval($task_data['last_processed_id'])));

	$videos = mr2array(sql_pr("select video_id, screen_amount, poster_amount, file_formats from $config[tables_prefix]videos where status_id in (0,1) and video_id>? order by video_id asc limit $iteration_step", intval($task_data['last_processed_id'])));

	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		if ($res_format['group_id'] == 1)
		{
			$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$res_format[size]";
			if (intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
			{
				$zip_files_to_add = [];
				for ($i = 1; $i <= $video['screen_amount']; $i++)
				{
					$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
				}
				$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
				$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
			}
		} elseif ($res_format['group_id'] == 2)
		{
			$formats = get_video_formats($video_id, $video['file_formats']);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['timeline_screen_amount'] > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format['postfix'] == $format_rec['postfix'])
						{
							$timeline_dir = $format['timeline_directory'];
							$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$res_format[size]";
							if (intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
							{
								$zip_files_to_add = [];
								for ($i = 1; $i <= $format_rec['timeline_screen_amount']; $i++)
								{
									$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
								}
								$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
								$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
							}
						}
					}
				}
			}
		} elseif ($res_format['group_id'] == 3)
		{
			if ($video['poster_amount'] > 0)
			{
				$screenshots_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$res_format[size]";
				if (intval(@filesize("$screenshots_target_dir/$video_id-$res_format[size].zip")) == 0)
				{
					$zip_files_to_add = [];
					for ($i = 1; $i <= $video['poster_amount']; $i++)
					{
						$zip_files_to_add[] = "$screenshots_target_dir/$i.jpg";
					}
					$zip = new PclZip("$screenshots_target_dir/$video_id-$res_format[size].zip");
					$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screenshots_target_dir);
				}
			}
		}

		log_output('', $video_id, 1, 1);
		log_output("INFO  Created ZIP for screenshot format \"$res_format[title]\" for video $video_id", $video_id);

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  ZIP creation task is completed for screenshot format \"$res_format[title]\" [PH-FE]");
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_delete_zip_screenshots($task_data, $formats_videos)
{
	global $config;

	$task_start_time = time();

	$format_id = intval($task_data['data']['format_id']);
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=?", $format_id));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  ZIP removal task is continued for screenshot format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  ZIP removal task is started for screenshot format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id<=?", intval($task_data['last_processed_id'])));

	$videos = mr2array(sql_pr("select video_id, file_formats from $config[tables_prefix]videos where video_id>? order by video_id asc limit ?", intval($task_data['last_processed_id']), $iteration_step));
	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		if ($res_format['group_id'] == 1)
		{
			@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/$res_format[size]/$video_id-$res_format[size].zip");
		} elseif ($res_format['group_id'] == 2)
		{
			$formats = get_video_formats($video_id, $video['file_formats']);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['timeline_screen_amount'] > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format['postfix'] == $format_rec['postfix'])
						{
							$timeline_dir = $format['timeline_directory'];
							@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_dir/$res_format[size]/$video_id-$res_format[size].zip");
						}
					}
				}
			}
		} elseif ($res_format['group_id'] == 3)
		{
			@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/posters/$res_format[size]/$video_id-$res_format[size].zip");
		}

		log_output('', $video_id, 1, 1);
		log_output("INFO  Removed ZIP for screenshot format \"$res_format[title]\" for video $video_id", $video_id);

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  ZIP removal task is completed for screenshot format \"$res_format[title]\" [PH-FE]");
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_create_zip_images($task_data)
{
	global $config, $options;

	$task_start_time = time();

	$res_format = null;
	$format_name_log_text = 'source images';
	if ($task_data['data']['format_id'] != 'source')
	{
		$format_id = intval($task_data['data']['format_id']);
		$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where format_album_id=?", $format_id));
		if (!isset($res_format))
		{
			cancel_task_album(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
			return false;
		}

		$format_name_log_text = "album format \"$res_format[title]\"";
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]albums)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  ZIP creation task is continued for $format_name_log_text [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  ZIP creation task is started for $format_name_log_text [PH-P]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1)"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1) and album_id<=?", intval($task_data['last_processed_id'])));

	$albums = mr2array(sql_pr("select album_id, server_group_id, zip_files from $config[tables_prefix]albums where status_id in (0,1) and album_id>? order by album_id asc limit $iteration_step", intval($task_data['last_processed_id'])));

	$failed_albums = [];
	foreach ($albums as $album)
	{
		$album_id = $album['album_id'];
		$dir_path = get_dir_by_id($album_id);

		$last_object_id = $album_id;

		$images_source_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]";
		if (!mkdir_recursive($images_source_dir))
		{
			$failed_albums[] = ['album_id' => $album_id, 'filesystem' => $images_source_dir];
			continue;
		}

		$is_album_skipped = true;
		try
		{
			$zip_file_id = 'source';
			$zip_file_name = "$album_id.zip";
			$remote_dir = 'sources';
			if (isset($res_format))
			{
				$zip_file_id = $res_format['size'];
				$zip_file_name = "$album_id-$res_format[size].zip";
				$remote_dir = "main/$res_format[size]";
			}

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			if (!isset($zip_files[$zip_file_id]))
			{
				$is_album_skipped = false;
				$zip_files_to_add = [];

				$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=? order by image_id asc", $album_id));
				foreach ($images as $image)
				{
					$image_id = $image['image_id'];
					foreach ($storage_servers[$album['server_group_id']] as $server)
					{
						if (get_file("$image_id.jpg", "$remote_dir/$dir_path/$album_id", $images_source_dir, $server))
						{
							break;
						}
					}
					if (!is_file("$images_source_dir/$image_id.jpg"))
					{
						$failed_albums[] = ['album_id' => $album_id, 'get' => "$image_id.jpg"];
						throw new RuntimeException('Get file failed');
					}

					if (!isset($res_format) && in_array($image['format'], ['gif', 'png']))
					{
						copy("$images_source_dir/$image_id.jpg", "$images_source_dir/$image_id.{$image['format']}");
						$zip_files_to_add[] = "$images_source_dir/$image_id.{$image['format']}";
					} else
					{
						$zip_files_to_add[] = "$images_source_dir/$image_id.jpg";
					}
				}

				$zip = new PclZip("$images_source_dir/$zip_file_name");
				$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $images_source_dir);

				foreach ($storage_servers[$album['server_group_id']] as $server)
				{
					if (!put_file($zip_file_name, $images_source_dir, "$remote_dir/$dir_path/$album_id", $server))
					{
						$failed_albums[] = ['album_id' => $album_id, 'put' => $zip_file_name];
						throw new RuntimeException('Put file failed');
					}
					if ($server['streaming_type_id'] == 4) // CDN
					{
						cdn_invalidate_album($album_id, $server, [], ["$remote_dir/$dir_path/$album_id/$zip_file_name"], 'add');
					}
				}

				$new_zip = [];
				$new_zip['size'] = $zip_file_id;
				$new_zip['file_size'] = sprintf("%.0f", filesize("$images_source_dir/$zip_file_name"));
				$zip_files[] = $new_zip;
				sql_update("update $config[tables_prefix]albums set zip_files=? where album_id=?", pack_album_zip_files($zip_files), $album_id);
			}
		} catch (Exception $e)
		{
			if (rmdir_recursive($images_source_dir))
			{
				@rmdir(dirname($images_source_dir));
			}
			continue;
		}

		if (rmdir_recursive($images_source_dir))
		{
			@rmdir(dirname($images_source_dir));
		}

		if (!$is_album_skipped)
		{
			log_output_album('', $album_id, 1, 1);
			log_output_album("INFO  Created ZIP for $format_name_log_text for album $album_id", $album_id);
		}

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if (count($failed_albums) > 0)
	{
		// fail task
		$failed_albums_ids = [];
		foreach ($failed_albums as $album_failure_rec)
		{
			$failed_albums_ids[] = $album_failure_rec['album_id'];
			log_output_album('', $album_failure_rec['album_id'], 1, 1);
			if ($album_failure_rec['get'])
			{
				log_output_album("WARN  Failed to create $format_name_log_text for album $album_failure_rec[album_id]: failed to get $album_failure_rec[get] file from storage servers", $album_failure_rec['album_id']);
			} elseif ($album_failure_rec['put'])
			{
				log_output_album("WARN  Failed to create $format_name_log_text for album $album_failure_rec[album_id]: failed to put $album_failure_rec[put] file to storage servers", $album_failure_rec['album_id']);
			} elseif ($album_failure_rec['filesystem'])
			{
				log_output_album("WARN  Failed to create $format_name_log_text for album $album_failure_rec[album_id]: failed to create directory $album_failure_rec[filesystem]", $album_failure_rec['album_id']);
			}
		}

		log_output_album("WARN  Albums with errors are: " . implode(', ', $failed_albums_ids));
		cancel_task_album(8, "Error during ZIP creation for $format_name_log_text for some albums, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed albums [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed albums");
		log_output("INFO  ZIP creation task is completed for $format_name_log_text [PH-FE]");
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_delete_zip_images($task_data)
{
	global $config;

	$task_start_time = time();

	$res_format = null;
	$format_name_log_text = 'source images';
	if ($task_data['data']['format_id'] != 'source')
	{
		$format_id = intval($task_data['data']['format_id']);
		$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where format_album_id=?", $format_id));
		if (!isset($res_format))
		{
			cancel_task_album(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
			return false;
		}

		$format_name_log_text = "album format \"$res_format[title]\"";
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]albums)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  ZIP removal task is continued for $format_name_log_text [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  ZIP removal task is started for $format_name_log_text [PH-P]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where album_id<=?", intval($task_data['last_processed_id'])));

	$albums = mr2array(sql_pr("select album_id, server_group_id, zip_files from $config[tables_prefix]albums where album_id>? order by album_id asc limit $iteration_step", intval($task_data['last_processed_id'])));

	foreach ($albums as $album)
	{
		$album_id = $album['album_id'];
		$dir_path = get_dir_by_id($album_id);

		$last_object_id = $album_id;

		$zip_file_id = 'source';
		$zip_file_name = "$album_id.zip";
		$remote_dir = 'sources';
		if (isset($res_format))
		{
			$zip_file_id = $res_format['size'];
			$zip_file_name = "$album_id-$res_format[size].zip";
			$remote_dir = "main/$res_format[size]";
		}

		$zip_files = get_album_zip_files($album_id, $album['zip_files']);
		if (isset($zip_files[$zip_file_id]))
		{
			foreach ($storage_servers[$album['server_group_id']] as $server)
			{
				delete_file($zip_file_name, "$remote_dir/$dir_path/$album_id", $server);
				if ($server['streaming_type_id'] == 4) // CDN
				{
					cdn_invalidate_album($album_id, $server, [], ["$remote_dir/$dir_path/$album_id/$zip_file_name"], 'delete');
				}
			}

			unset($zip_files[$zip_file_id]);
			sql_update("update $config[tables_prefix]albums set zip_files=? where album_id=?", pack_album_zip_files($zip_files), $album_id);

			log_output_album('', $album_id, 1, 1);
			log_output_album("INFO  Removed ZIP for $format_name_log_text for album $album_id", $album_id);
		}

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		usleep(2000);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed albums [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed albums");
		log_output("INFO  ZIP removal task is completed for $format_name_log_text [PH-FE]");
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_create_album_formats_albums($task_data)
{
	global $config, $options;

	$task_start_time = time();

	$album_id = intval($task_data['album_id']);
	$res_album = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=?", $album_id));
	if (!isset($res_album))
	{
		cancel_task_album(1, "Album $album_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$res_formats = [];
	$format_ids = $task_data['data']['format_ids'];
	foreach ($format_ids as $format_id)
	{
		$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_albums where format_album_id=? and status_id=1", $format_id));
		if (!isset($res_format))
		{
			cancel_task_album(1, "Format \"$format_id\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
			return false;
		}
		$res_formats[] = $res_format;
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id in (select distinct server_group_id from $config[tables_prefix]albums)"));
	$storage_servers = [];
	foreach ($temp as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		$storage_servers[$server['group_id']][] = $server;
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output_album('', $album_id, 1, 1);
	log_output_album("INFO  Formats re-creation task is started for album format $album_id [PH-P]", $album_id);

	$dir_path = get_dir_by_id($album_id);

	$custom_crop_options = '';
	if (intval($options['ALBUMS_CROP_CUSTOMIZE']) > 0 && $res_album['content_source_id'] > 0)
	{
		$res_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $res_album['content_source_id']));
		$custom_crop_options = $res_content_source["custom{$options['ALBUMS_CROP_CUSTOMIZE']}"];
	}

	$images_source_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]";
	if (!mkdir_recursive($images_source_dir))
	{
		cancel_task_album(5, "Failed to create directory $images_source_dir", $album_id, $task_data['task_id']);
		return false;
	}

	$invalidate_files = [];
	$invalidate_folders = [];

	try
	{
		$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=? order by image_id asc", $album_id));
		foreach ($res_formats as $res_format)
		{
			$images_target_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$res_format[size]";
			if (!mkdir_recursive($images_target_dir))
			{
				cancel_task_album(5, "Failed to create directory $images_target_dir", $album_id, $task_data['task_id']);
				return false;
			}

			if ($res_format['group_id'] == 1)
			{
				foreach ($images as $image)
				{
					$image_id = $image['image_id'];

					if (!is_file("$images_source_dir/$image_id.jpg"))
					{
						foreach ($storage_servers[$res_album['server_group_id']] as $server)
						{
							if (get_file("$image_id.jpg", "sources/$dir_path/$album_id", $images_source_dir, $server))
							{
								break;
							}
						}
					}
					if (!is_file("$images_source_dir/$image_id.jpg"))
					{
						cancel_task_album(4, "Failed to get $image_id.jpg file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
						return false;
					}

					$exec_res = make_image_from_source("$images_source_dir/$image_id.jpg", "$images_target_dir/$image_id.jpg", $res_format, $options, $custom_crop_options);
					if ($exec_res)
					{
						log_output_album("ERROR IM operation failed: $exec_res", $album_id);
						cancel_task_album(8, "Error during images creation for album format \"$res_format[title]\" for image $image_id, cancelling this task", $album_id, $task_data['task_id']);
						return false;
					}

					foreach ($storage_servers[$res_album['server_group_id']] as $server)
					{
						if (!put_file("$image_id.jpg", $images_target_dir, "main/$res_format[size]/$dir_path/$album_id", $server))
						{
							cancel_task_album(4, "Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
							return false;
						}
					}
					$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$image_id.jpg";

					$had_format = false;
					$image_formats = get_image_formats($album_id, $image['image_formats']);
					foreach ($image_formats as $k => $v)
					{
						if ($v['size'] == $res_format['size'])
						{
							$image_formats[$k]['dimensions'] = getimagesize("$images_target_dir/$image_id.jpg");
							$image_formats[$k]['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$image_id.jpg"));
							$had_format = true;
							break;
						}
					}
					if (!$had_format)
					{
						$new_format = [];
						$new_format['size'] = $res_format['size'];
						$new_format['dimensions'] = getimagesize("$images_target_dir/$image_id.jpg");
						$new_format['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$image_id.jpg"));
						$image_formats[] = $new_format;
					}
					sql_update("update $config[tables_prefix]albums_images set image_formats=? where image_id=?", pack_image_formats($image_formats), $image_id);

					usleep(2000);
				}

				if ($res_format['is_create_zip'] == 1)
				{
					$zip_files = get_album_zip_files($album_id, $res_album['zip_files']);

					$zip_files_to_add = [];
					foreach ($images as $image)
					{
						$zip_files_to_add[] = "$images_target_dir/$image[image_id].jpg";
					}
					$zip = new PclZip("$images_target_dir/$album_id-$res_format[size].zip");
					$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $images_target_dir);

					foreach ($storage_servers[$res_album['server_group_id']] as $server)
					{
						if (!put_file("$album_id-$res_format[size].zip", $images_target_dir, "main/$res_format[size]/$dir_path/$album_id", $server))
						{
							cancel_task_album(4, "Failed to put $album_id-$res_format[size].zip file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
							return false;
						}
					}
					$invalidate_files[] = "main/$res_format[size]/$dir_path/$album_id/$album_id-$res_format[size].zip";

					$had_zip = false;
					foreach ($zip_files as $k => $v)
					{
						if ($v['size'] == $res_format['size'])
						{
							$zip_files[$k]['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$album_id-$res_format[size].zip"));
							$had_zip = true;
							break;
						}
					}
					if (!$had_zip)
					{
						$new_zip = [];
						$new_zip['size'] = $res_format['size'];
						$new_zip['file_size'] = sprintf("%.0f", filesize("$images_target_dir/$album_id-$res_format[size].zip"));
						$zip_files[] = $new_zip;
					}
					sql_update("update $config[tables_prefix]albums set zip_files=? where album_id=?", pack_album_zip_files($zip_files), $album_id);
				}

				$invalidate_folders[] = "main/$res_format[size]/$dir_path/$album_id";
			} else
			{
				$image_id = $res_album['main_photo_id'];
				$preview_source = "$image_id.jpg";
				if ($res_album['has_preview'] == 1)
				{
					$preview_source = "preview.jpg";
				}

				if (!is_file("$images_source_dir/$preview_source"))
				{
					foreach ($storage_servers[$res_album['server_group_id']] as $server)
					{
						if (get_file($preview_source, "sources/$dir_path/$album_id", $images_source_dir, $server))
						{
							break;
						}
					}
				}
				if (!is_file("$images_source_dir/$preview_source"))
				{
					cancel_task_album(4, "Failed to get $preview_source file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}

				$exec_res = make_image_from_source("$images_source_dir/$preview_source", "$images_target_dir/preview.jpg", $res_format, $options, $custom_crop_options);
				if ($exec_res)
				{
					log_output_album("ERROR IM operation failed: $exec_res", $album_id);
					cancel_task_album(8, "Error during preview image creation for album format \"$res_format[title]\", cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}

				foreach ($storage_servers[$res_album['server_group_id']] as $server)
				{
					if (!put_file("preview.jpg", $images_target_dir, "preview/$res_format[size]/$dir_path/$album_id", $server))
					{
						cancel_task_album(4, "Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
						return false;
					}
				}
				$invalidate_files[] = "preview/$res_format[size]/$dir_path/$album_id/preview.jpg";
				$invalidate_folders[] = "preview/$res_format[size]/$dir_path/$album_id";
			}

			log_output_album("INFO  Re-created album format \"$res_format[title]\"", $album_id);
		}

		foreach ($storage_servers[$res_album['server_group_id']] as $server)
		{
			if ($server['streaming_type_id'] == 4) // CDN
			{
				cdn_invalidate_album($album_id, $server, $invalidate_folders, $invalidate_files, 'change');
			}
		}
	} finally
	{
		foreach ($res_formats as $res_format)
		{
			@rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$res_format[size]");
		}
		if (@rmdir_recursive($images_source_dir))
		{
			@rmdir(dirname($images_source_dir));
		}
	}

	log_output_album("INFO  Formats re-creation task is completed for album $album_id [PH-FE]", $album_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_delete_timeline_screenshots($task_data)
{
	global $config;

	$task_start_time = time();

	$postfix = $task_data['data']['format_postfix'];
	$res_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_videos where postfix=?", $postfix));
	if (!isset($res_format))
	{
		cancel_task(1, "Format \"$postfix\" is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}
	if ($res_format['is_timeline_enabled'] == 1)
	{
		cancel_task(1, "Format \"$postfix\" has timeline screenshots enabled, cancelling this task", 0, $task_data['task_id']);
		return false;
	}
	if (!$res_format['timeline_directory'])
	{
		cancel_task(1, "Format \"$postfix\" has no info about timeline directory, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Timeline screenshots removal task is continued for video format \"$res_format[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Timeline screenshots removal task is started for video format \"$res_format[title]\" [PH-I]");
	}

	$iteration_step = 1000;

	$last_object_id = 0;
	$last_iteration_processed = 0;

	$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where load_type_id=1 and file_formats like ?", "%||$postfix|%"));
	$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where load_type_id=1 and file_formats like ? and video_id<=?", "%||$postfix|%", intval($task_data['last_processed_id'])));

	$videos = mr2array(sql_pr("select video_id, server_group_id, file_formats from $config[tables_prefix]videos where load_type_id=1 and file_formats like ? and video_id>? order by video_id asc limit $iteration_step", "%||$postfix|%", intval($task_data['last_processed_id'])));

	foreach ($videos as $video)
	{
		$video_id = $video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		$last_object_id = $video_id;

		$formats = get_video_formats($video_id, $video['file_formats']);
		$has_timeline_screenshots = false;
		foreach ($formats as $v)
		{
			if ($v['postfix'] == $postfix)
			{
				if ($v['timeline_screen_amount'] > 0)
				{
					$has_timeline_screenshots = true;
				}
				break;
			}
		}

		if ($has_timeline_screenshots)
		{
			rmdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
			if (is_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]"))
			{
				$folders = get_contents_from_dir("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]", 2);
				foreach ($folders as $folder)
				{
					rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]/$folder");
				}
				rmdir_recursive("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$res_format[timeline_directory]");
			}

			foreach ($formats as $k => $v)
			{
				if ($v['postfix'] == $postfix)
				{
					$formats[$k]['timeline_screen_amount'] = 0;
					$formats[$k]['timeline_screen_interval'] = 0;
					$formats[$k]['timeline_cuepoints'] = 0;
					break;
				}
			}

			sql_update("update $config[tables_prefix]videos set file_formats=? where video_id=?", pack_video_formats($formats), $video_id);
			log_output('', $video_id, 1, 1);
			log_output("INFO  Deleted timeline screenshots for video format \"$res_format[title]\" for video $video_id", $video_id);

			usleep(2000);
		}

		$last_iteration_processed++;
		mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration processed $last_iteration_processed videos [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration processed $last_iteration_processed videos");
		log_output("INFO  Timeline screenshots removal task is completed for video format \"$res_format[title]\" [PH-FE]");
		finish_task($task_data, time() - $task_start_time);
	}

	return false;
}

function exec_change_album_images($task_data,$server_data,$formats_albums)
{
	global $config,$options;

	$task_start_time=time();

	$album_id=intval($task_data['album_id']);
	$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));
	if (!isset($res_album))
	{
		cancel_task_album(1,"Album $album_id is not available in the database, cancelling this task",0,$task_data['task_id'],$server_data);
		return false;
	}

	$dir_path=get_dir_by_id($album_id);
	$server_data=mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where server_id=?",intval($server_data['server_id'])));

	$custom_crop_options='';
	if (intval($options['ALBUMS_CROP_CUSTOMIZE'])>0 && $res_album['content_source_id']>0)
	{
		$res_content_source=mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?",$res_album['content_source_id']));
		$custom_crop_options=$res_content_source["custom{$options['ALBUMS_CROP_CUSTOMIZE']}"];
	}

	if ($task_data['status_id']==0)
	{
		if (!isset($server_data))
		{
			mark_task_duration($task_data['task_id'],time()-$task_start_time);
			warn_task_album("Conversion server is not available in the database, skipping this task",0,$task_data['task_id']);
			return false;
		}

		log_output_album('',$album_id,1,1);
		log_output_album("INFO  Album images manipulation task is started for album $album_id [PH-P]",$album_id);

		if (@count($task_data['data']['changed_image_ids'])==0)
		{
			log_output_album("INFO  No need for background task on conversion server, skipping conversion process",$album_id);
			sql_pr("update $config[tables_prefix]background_tasks set status_id=1 where task_id=$task_data[task_id]");
			mark_task_progress($task_data['task_id'],10);
			return false;
		}

		log_output_album("INFO  Preparing task for conversion server [PH-P-2]",$album_id);

		mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);
		chmod("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]",0777);

		$i=1;
		foreach ($task_data['data']['changed_image_ids'] as $image_id)
		{
			$orientation_res=correct_orientation("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg");
			if (intval($orientation_res)>0)
			{
				log_output_album("INFO  Image $i is not oriented properly, EXIF info: ".intval($orientation_res),$album_id);
			} elseif ($orientation_res!=0) {
				log_output_album("ERROR IM operation failed: $orientation_res",$album_id);
				cancel_task_album(9,"Failed to change orientation",$album_id,$task_data['task_id'],$server_data);
			}

			copy("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]/$i.jpg");
			if (!put_file("$i.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]","$task_data[task_id]",$server_data))
			{
				cancel_task_album(2,"Failed to put $i.jpg file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			$i++;
		}

		$task_info=array();
		$task_info['album_id']=$album_id;
		$task_info['source_images_count']=count($task_data['data']['changed_image_ids']);
		$task_info['options']['PROCESS_PRIORITY']=$options['GLOBAL_CONVERTATION_PRIORITY'];
		$task_info['options']['ALBUMS_CROP_LEFT_UNIT']=$options['ALBUMS_CROP_LEFT_UNIT'];
		$task_info['options']['ALBUMS_CROP_RIGHT_UNIT']=$options['ALBUMS_CROP_RIGHT_UNIT'];
		$task_info['options']['ALBUMS_CROP_TOP_UNIT']=$options['ALBUMS_CROP_TOP_UNIT'];
		$task_info['options']['ALBUMS_CROP_BOTTOM_UNIT']=$options['ALBUMS_CROP_BOTTOM_UNIT'];
		$task_info['options']['ALBUMS_CROP_LEFT']=$options['ALBUMS_CROP_LEFT'];
		$task_info['options']['ALBUMS_CROP_RIGHT']=$options['ALBUMS_CROP_RIGHT'];
		$task_info['options']['ALBUMS_CROP_TOP']=$options['ALBUMS_CROP_TOP'];
		$task_info['options']['ALBUMS_CROP_BOTTOM']=$options['ALBUMS_CROP_BOTTOM'];
		$task_info['options']['ALBUMS_CROP_CUSTOMIZE']=$custom_crop_options;
		$task_info['options']['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']=$config['imagemagick_default_jpeg_quality'];

		if ($config['installation_type']>=3)
		{
			$task_info['options']['PROCESS_PRIORITY']=intval($server_data['process_priority']);
		}
		log_output_album("INFO  Conversion priority level is set to ".$task_info['options']['PROCESS_PRIORITY'],$album_id);

		$task_info['formats_albums']=$formats_albums;
		foreach ($formats_albums as $format)
		{
			if (is_file("$config[project_path]/admin/data/other/watermark_album_{$format['format_album_id']}.png"))
			{
				if (!put_file("watermark_album_{$format['format_album_id']}.png","$config[project_path]/admin/data/other","$task_data[task_id]",$server_data))
				{
					cancel_task_album(2,"Failed to put watermark_album_{$format['format_album_id']}.png file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
		}

		$fp=fopen("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat","w");
		fwrite($fp,serialize($task_info));
		fclose($fp);

		if (!put_file('task.dat',"$config[content_path_albums_sources]/$dir_path/$album_id","$task_data[task_id]",$server_data))
		{
			cancel_task_album(2,"Failed to put task.dat file to conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
			return false;
		}
		unlink("$config[content_path_albums_sources]/$dir_path/$album_id/task.dat");

		log_output_album("INFO  Task data has been copied to conversion server $server_data[title] [PH-PE]",$album_id);
		sql_pr("update $config[tables_prefix]background_tasks set status_id=1, server_id=$server_data[server_id] where task_id=$task_data[task_id]");
		mark_task_progress($task_data['task_id'],10);
		mark_task_duration($task_data['task_id'],time()-$task_start_time);
		return true;
	} else {
		// check conversion task
		$conversion_log='';
		$task_conversion_duration=0;
		if ($task_data['server_id']<>0)
		{
			if (!isset($server_data))
			{
				cancel_task_album(1,"Conversion server $task_data[server_id] is not available in the database, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}

			if (check_file('progress.dat', "$task_data[task_id]", $server_data))
			{
				get_file('progress.dat', "$task_data[task_id]", "$config[content_path_albums_sources]/$dir_path/$album_id", $server_data);
				if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat"))
				{
					mark_task_progress($task_data['task_id'], 10 + floor(intval(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat")) * 0.4));
					unlink("$config[content_path_albums_sources]/$dir_path/$album_id/progress.dat");
				}
			}

			if (check_file('result.dat',"$task_data[task_id]",$server_data)==0)
			{
				if (check_file('task.dat',"$task_data[task_id]",$server_data)>0)
				{
					return false;
				} else {
					if (test_connection($server_data)===true)
					{
						cancel_task_album(2,"Task directory is not available on conversion server, cancelling this task",$album_id,$task_data['task_id']);
					} else {
						warn_task_album("Conversion server connection is lost, skipping this task",$album_id,$task_data['task_id']);
					}
					return false;
				}
			}

			// check result file
			if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
			{
				cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
			if (!is_array($result_data))
			{
				sleep(1);
				if (!get_file('result.dat',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
				{
					cancel_task_album(2,"Failed to get result.dat file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
				$result_data=@unserialize(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat"));
				if (!is_array($result_data))
				{
					cancel_task_album(6,"Unexpected error on conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}
			}
			$task_conversion_duration = intval($result_data['duration']);
			@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/result.dat");

			// check log file
			if (check_file('log.txt',"$task_data[task_id]",$server_data)>0)
			{
				if (!get_file('log.txt',"$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id",$server_data))
				{
					cancel_task_album(2,"Failed to get log.txt file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
					return false;
				}

				if (sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt")) > 10 * 1000 * 1000)
				{
					$conversion_log = 'Conversion log is more than 10mb';
				} else
				{
					$conversion_log = trim(@file_get_contents("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt"));
				}
			}
			if ($conversion_log === '')
			{
				cancel_task_album(3,"No conversion log is available, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
			@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/log.txt");

			// check if conversion result contains any error
			if ($result_data['is_error']==1)
			{
				log_output_album('',$album_id);
				log_output_album($conversion_log,$album_id,1);
				cancel_task_album(intval($result_data['error_code'])>0?$result_data['error_code']:7,$result_data['error_message']!=''?$result_data['error_message']:"Conversion error, cancelling this task",$album_id,$task_data['task_id'],$server_data);
				return false;
			}
		}

		$storage_servers=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=$res_album[server_group_id]"));
		foreach ($storage_servers as $server)
		{
			if (!test_connection_status($server))
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
			if ($server['free_space']<$options['SERVER_GROUP_MIN_FREE_SPACE_MB']*1024*1024)
			{
				mark_task_duration($task_data['task_id'],time()-$task_start_time);
				warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task",$album_id,$task_data['task_id']);
				return false;
			}
		}

		mark_task_progress($task_data['task_id'],50);

		// log conversion process
		if ($task_data['server_id']<>0)
		{
			log_output_album('',$album_id);
			log_output_album($conversion_log,$album_id,1);
		}

		log_output_album('',$album_id);
		log_output_album("INFO  Album images manipulation task is continued for album $album_id [PH-F]",$album_id);

		mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);
		chmod("$config[content_path_albums_sources]/$dir_path/$album_id/sources",0777);

		$invalidate_files=array();

		if (@count($task_data['data']['changed_image_ids'])>0)
		{
			// update main images
			log_output_album("INFO  Updating main images [PH-F-8]",$album_id);
			mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);
			chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);

			$image_number=1;
			foreach ($task_data['data']['changed_image_ids'] as $image_id)
			{
				$image_formats=array();

				$format_rec=array();
				$format_rec['size']='source';
				$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg");
				$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg"));
				$image_formats[]=$format_rec;
				foreach ($formats_albums as $format)
				{
					if ($format['group_id']==1)
					{
						if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);}

						if (!get_file("$format[format_album_id]-$image_number.jpg","$task_data[task_id]","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",$server_data))
						{
							cancel_task_album(2,"Failed to get $format[format_album_id]-$image_number.jpg file from conversion server, cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
						@rename("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$format[format_album_id]-$image_number.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");

						foreach ($storage_servers as $server)
						{
							if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
							{
								cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
								return false;
							}
						}

						$format_rec=array();
						$format_rec['size']=$format['size'];
						$format_rec['dimensions']=getimagesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg");
						$format_rec['file_size']=sprintf("%.0f",filesize("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]/$image_id.jpg"));
						$image_formats[]=$format_rec;

						$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$image_id.jpg";
					}
				}

				foreach ($storage_servers as $server)
				{
					if (!put_file("$image_id.jpg","$config[content_path_albums_sources]/$dir_path/$album_id","sources/$dir_path/$album_id",$server))
					{
						cancel_task_album(4,"Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				$invalidate_files[]="sources/$dir_path/$album_id/$image_id.jpg";

				$image_format=get_image_format_id("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg");

				sql_pr("update $config[tables_prefix]albums_images set format=?, image_formats=? where image_id=?",$image_format,pack_image_formats($image_formats),$image_id);
				$image_number++;
			}
		}

		mark_task_progress($task_data['task_id'],60);

		if ($task_data['data']['main_image_changed']==1)
		{
			// update preview image
			log_output_album("INFO  Updating preview image [PH-F-9]",$album_id);
			$preview_image_source='';
			if ($res_album['has_preview']==0)
			{
				$main=$res_album['main_photo_id'];
				if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/$main.jpg"))
				{
					copy("$config[content_path_albums_sources]/$dir_path/$album_id/$main.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/sources/$main.jpg");
				} else {
					$storage_servers=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=$res_album[server_group_id]"));
					foreach ($storage_servers as $server)
					{
						if (get_file("$main.jpg","sources/$dir_path/$album_id","$config[content_path_albums_sources]/$dir_path/$album_id/sources",$server))
						{
							break;
						}
					}
					if (!is_file("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$main.jpg"))
					{
						cancel_task_album(4,"Failed to get $main.jpg file from storage servers, cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				$preview_image_source="$config[content_path_albums_sources]/$dir_path/$album_id/sources/$main.jpg";
				foreach ($storage_servers as $server)
				{
					delete_file("preview.jpg","sources/$dir_path/$album_id",$server);
				}
				$invalidate_files[]="sources/$dir_path/$album_id/preview.jpg";
			} elseif (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg"))
			{
				$preview_image_source="$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg";
				foreach ($storage_servers as $server)
				{
					if (!put_file("preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id","sources/$dir_path/$album_id",$server))
					{
						cancel_task_album(4,"Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				$invalidate_files[]="sources/$dir_path/$album_id/preview.jpg";
			}

			if ($preview_image_source!='')
			{
				mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/preview",0777);
				chmod("$config[content_path_albums_sources]/$dir_path/$album_id/preview",0777);
				foreach ($formats_albums as $format)
				{
					if ($format['group_id']==2)
					{
						if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]",0777);}

						$exec_res=make_image_from_source($preview_image_source,"$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]/preview.jpg",$format,$options,$custom_crop_options);
						if ($exec_res)
						{
							log_output_album("ERROR IM operation failed: $exec_res",$album_id);
							cancel_task_album(8,"Error during preview image creation for \"$format[title]\" format, cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}

						foreach ($storage_servers as $server)
						{
							if (!put_file("preview.jpg","$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]","preview/$format[size]/$dir_path/$album_id",$server))
							{
								cancel_task_album(4,"Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
								return false;
							}
						}
						$invalidate_files[]="preview/$format[size]/$dir_path/$album_id/preview.jpg";
					}
				}
			}
		}

		mark_task_progress($task_data['task_id'],70);

		if (@count($task_data['data']['deleted_image_ids'])>0)
		{
			// delete main images
			log_output_album("INFO  Deleting images [PH-F-11]",$album_id);
			foreach ($task_data['data']['deleted_image_ids'] as $image_id)
			{
				foreach ($storage_servers as $server)
				{
					delete_file("$image_id.jpg","sources/$dir_path/$album_id",$server);
					$invalidate_files[]="sources/$dir_path/$album_id/$image_id.jpg";
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							delete_file("$image_id.jpg","main/$format[size]/$dir_path/$album_id",$server);
							$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$image_id.jpg";
						}
					}
				}
			}
		}

		mark_task_progress($task_data['task_id'],80);

		if (@count($task_data['data']['changed_image_ids'])>0 || @count($task_data['data']['deleted_image_ids'])>0)
		{
			if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main",0777);}

			$zip_files=get_album_zip_files($album_id,$res_album['zip_files']);

			// update necessary zip files
			$images=mr2array(sql_pr("select image_id, format from $config[tables_prefix]albums_images where album_id=$album_id"));
			if (isset($zip_files['source']))
			{
				log_output_album("INFO  Updating ZIP with source files [PH-F-10:source]",$album_id);
				$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/sources";
				$zip_files_to_add=array();
				foreach ($images as $image)
				{
					$image_id=$image['image_id'];
					if (!is_file("$source_folder/$image_id.jpg"))
					{
						foreach ($storage_servers as $server)
						{
							if (get_file("$image_id.jpg","sources/$dir_path/$album_id",$source_folder,$server))
							{
								break;
							}
						}
						if (!is_file("$source_folder/$image_id.jpg"))
						{
							cancel_task_album(4,"Failed to get $image_id.jpg file from storage servers, cancelling this task",$album_id,$task_data['task_id'],$server_data);
							return false;
						}
					}
					if ($image['format'] && $image['format']!='jpg')
					{
						copy("$source_folder/$image_id.jpg","$source_folder/$image_id.{$image['format']}");
						$zip_files_to_add[]="$source_folder/$image_id.{$image['format']}";
					} else
					{
						$zip_files_to_add[]="$source_folder/$image_id.jpg";
					}
				}
				$zip = new PclZip("$source_folder/$album_id.zip");
				$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

				$zip_files['source']['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id.zip"));
			}
			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1 && isset($zip_files[$format['size']]))
				{
					if (!is_dir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]")) {mkdir("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);chmod("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]",0777);}

					log_output_album("INFO  Updating images ZIP for \"$format[title]\" format [PH-F-10:$format[title]]",$album_id);
					$source_folder="$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]";
					$zip_files_to_add=array();
					foreach ($images as $image)
					{
						$image_id=$image['image_id'];
						if (!is_file("$source_folder/$image_id.jpg"))
						{
							foreach ($storage_servers as $server)
							{
								if (get_file("$image_id.jpg","main/$format[size]/$dir_path/$album_id",$source_folder,$server))
								{
									break;
								}
							}
							if (!is_file("$source_folder/$image_id.jpg"))
							{
								cancel_task_album(4,"Failed to get $image_id.jpg file from storage servers, cancelling this task",$album_id,$task_data['task_id'],$server_data);
								return false;
							}
						}
						$zip_files_to_add[]="$source_folder/$image_id.jpg";
					}
					$zip = new PclZip("$source_folder/$album_id-$format[size].zip");
					$zip->create($zip_files_to_add,$p_add_dir='',$p_remove_dir="$source_folder");

					$zip_files[$format['size']]['file_size']=sprintf("%.0f",filesize("$source_folder/$album_id-$format[size].zip"));
				}
			}

			mark_task_progress($task_data['task_id'],90);

			log_output_album("INFO  Copying ZIP files to storage servers [PH-F-11]",$album_id);
			if (is_file("$config[content_path_albums_sources]/$dir_path/$album_id/sources/$album_id.zip"))
			{
				foreach ($storage_servers as $server)
				{
					if (!put_file("$album_id.zip","$config[content_path_albums_sources]/$dir_path/$album_id/sources","sources/$dir_path/$album_id",$server))
					{
						cancel_task_album(4,"Failed to put $album_id.zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
						return false;
					}
				}
				$invalidate_files[]="sources/$dir_path/$album_id/$album_id.zip";
			}
			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					if ($format['is_create_zip']==1)
					{
						foreach ($storage_servers as $server)
						{
							if (!put_file("$album_id-$format[size].zip","$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]","main/$format[size]/$dir_path/$album_id",$server))
							{
								cancel_task_album(4,"Failed to put $album_id-$format[size].zip file to storage server \"$server[title]\", cancelling this task",$album_id,$task_data['task_id'],$server_data);
								return false;
							}
						}
						$invalidate_files[]="main/$format[size]/$dir_path/$album_id/$album_id-$format[size].zip";
					}
				}
			}
			sql_pr("update $config[tables_prefix]albums set zip_files=? where album_id=?",pack_album_zip_files($zip_files),$album_id);
		}

		foreach ($storage_servers as $server)
		{
			if ($server['streaming_type_id']==4) // CDN
			{
				cdn_invalidate_album($album_id,$server,array(),$invalidate_files,"multiple");
			}
		}

		// delete task on conversion server
		delete_task_folder($task_data['task_id'],$server_data);

		// remove temp files and folders
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1)
			{
				rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main/$format[size]");
			}
			if ($format['group_id']==2)
			{
				rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview/$format[size]");
			}
		}
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/sources");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/main");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/preview");
		rmdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]");
		if (@count($task_data['data']['changed_image_ids'])>0)
		{
			foreach ($task_data['data']['changed_image_ids'] as $image_id)
			{
				@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/$image_id.jpg");
			}
		}
		@unlink("$config[content_path_albums_sources]/$dir_path/$album_id/preview.jpg");
		rmdir("$config[content_path_albums_sources]/$dir_path/$album_id");

		// complete task
		log_output_album("INFO  Album images manipulation task is completed for album $album_id [PH-FE]",$album_id);
		mark_task_duration($task_data['task_id'],time()-$task_start_time+$task_conversion_duration);
		finish_task($task_data);
	}

	return false;
}

function exec_migrate_video($task_data)
{
	global $config, $options;

	$task_start_time = time();

	$video_id = intval($task_data['video_id']);
	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if (!isset($res_video))
	{
		cancel_task(1, "Video $video_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$server_group_id = intval($task_data['data']['server_group_id']);
	if ($res_video['server_group_id'] == $server_group_id)
	{
		log_output("Video already stored on server group $server_group_id, marking this task as complete");
		finish_task($task_data, time() - $task_start_time);
		return false;
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where video_id=? and status_id in (0,1) and task_id!=?", $video_id, $task_data['task_id'])))
	{
		warn_task("Video $video_id has other tasks that should be completed first, skipping this task", 0, $task_data['task_id']);
		return false;
	}

	$servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
	if (count($servers) == 0)
	{
		warn_task("No active servers found in server group $server_group_id, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
		return false;
	}
	foreach ($servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			warn_task("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	$postponed_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks_postponed where type_id=1 and video_id=?", $video_id));
	foreach ($postponed_tasks as $postponed_task)
	{
		$postponed_task['data'] = @unserialize($postponed_task['data']);
		if (intval($postponed_task['data']['old_server_group_id']) == $server_group_id)
		{
			sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $postponed_task['task_id']);
		}
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output('', $video_id, 1, 1);
	log_output("INFO  Video migration task is started for video $video_id [PH-P]", $video_id);

	$dir_path = get_dir_by_id($video_id);

	$old_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_video['server_group_id']));
	$video_formats = get_video_formats($video_id, $res_video['file_formats']);
	$invalidate_files = [];
	foreach ($video_formats as $video_format)
	{
		$postfix = $video_format['postfix'];
		log_output("INFO  Downloading video file \"$video_id{$postfix}\" from old storage group", $video_id);

		$file_copied = false;
		foreach ($old_servers as $server)
		{
			if (get_file("$video_id{$postfix}", "$dir_path/$video_id", "$config[content_path_videos_sources]/$dir_path/$video_id", $server))
			{
				$file_copied = true;
				break;
			}
		}
		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}") || !$file_copied)
		{
			foreach ($old_servers as $server)
			{
				if (intval($server['streaming_type_id']) == 4)
				{
					log_output("WARN  Failed to sync $video_id{$postfix} via server connection, trying to download it from CDN cache", $video_id);
					if (cdn_download_video_file($server, "$dir_path/$video_id/$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $video_format['file_size']))
					{
						$file_copied = true;
						break;
					}
				}
			}
		}
		if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}") || !$file_copied)
		{
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
			foreach ($invalidate_files as $invalidate_file)
			{
				@unlink("$config[content_path_videos_sources]/$invalidate_file");
			}
			cancel_task(4, "Failed to get $video_id{$postfix} file from storage servers, cancelling this task", $video_id, $task_data['task_id']);
			return false;
		}

		log_output("INFO  Uploading video file \"$video_id{$postfix}\" to new storage group", $video_id);
		foreach ($servers as $server)
		{
			if (!put_file("$video_id{$postfix}", "$config[content_path_videos_sources]/$dir_path/$video_id", "$dir_path/$video_id", $server))
			{
				@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}");
				foreach ($invalidate_files as $invalidate_file)
				{
					@unlink("$config[content_path_videos_sources]/$invalidate_file");
				}
				cancel_task(4, "Failed to put $video_id{$postfix} file to storage server \"$server[title]\", cancelling this task", $video_id, $task_data['task_id']);
				return false;
			}
		}
		if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}"))
		{
			log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$postfix}", $video_id);
		}
		$invalidate_files[] = "$dir_path/$video_id/$video_id{$postfix}";
	}

	log_output("INFO  Migrated video from group $res_video[server_group_id] to group $server_group_id", $video_id);

	foreach ($servers as $server)
	{
		if ($server['streaming_type_id'] == 4) // CDN
		{
			cdn_invalidate_video($video_id, $server, ["$dir_path/$video_id"], $invalidate_files, "add");
		}
	}

	sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=1, video_id=?, data=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 24 HOUR)", $video_id, serialize(['old_server_group_id' => $res_video['server_group_id']]), date('Y-m-d H:i:s'));

	// complete task
	sql_update("update $config[tables_prefix]videos set server_group_id=? where video_id=?", $server_group_id, $video_id);

	log_output("INFO  Video migration task is completed for video $video_id [PH-FE]", $video_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_migrate_album($task_data, $formats_albums)
{
	global $config, $options;

	$task_start_time = time();

	$album_id = intval($task_data['album_id']);
	$res_album = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=?", $album_id));
	if (!isset($res_album))
	{
		cancel_task_album(1, "Album $album_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	$server_group_id = intval($task_data['data']['server_group_id']);
	if ($res_album['server_group_id'] == $server_group_id)
	{
		log_output_album("Album already stored on server group $server_group_id, marking this task as complete");
		finish_task($task_data, time() - $task_start_time);
		return false;
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where album_id=? and status_id in (0,1) and task_id!=?", $album_id, $task_data['task_id'])) > 0)
	{
		warn_task_album("Album $album_id has other tasks that should be completed first, skipping this task", 0, $task_data['task_id']);
		return false;
	}
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where type_id in (12,13,18,19)")) > 0)
	{
		warn_task_album("There are some album formats related tasks, skipping this task", 0, $task_data['task_id']);
		return false;
	}

	$servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
	if (count($servers) == 0)
	{
		warn_task_album("No active servers found in server group $server_group_id, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
		return false;
	}
	foreach ($servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task_album("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
		if ($server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			warn_task_album("Storage server \"$server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	$postponed_tasks = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks_postponed where type_id=2 and album_id=?", $album_id));
	foreach ($postponed_tasks as $postponed_task)
	{
		$postponed_task['data'] = @unserialize($postponed_task['data']);
		if (intval($postponed_task['data']['old_server_group_id']) == $server_group_id)
		{
			sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $postponed_task['task_id']);
		}
	}

	sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
	log_output_album('', $album_id, 1, 1);
	log_output_album("INFO  Album migration task is started for album $album_id [PH-P]", $album_id);

	$dir_path = get_dir_by_id($album_id);

	$old_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_album['server_group_id']));

	$images_source_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/sources";
	if (!mkdir_recursive($images_source_dir))
	{
		cancel_task_album(5, "Failed to create directory $images_source_dir", $album_id, $task_data['task_id']);
		return false;
	}

	try
	{
		$invalidate_files = [];
		if ($res_album['has_preview'] == 1)
		{
			$file_copied = false;
			foreach ($old_servers as $server)
			{
				if (get_file("preview.jpg", "sources/$dir_path/$album_id", $images_source_dir, $server))
				{
					$file_copied = true;
					break;
				}
			}
			if (!is_file("$images_source_dir/preview.jpg") || !$file_copied)
			{
				cancel_task_album(4, "Failed to get preview.jpg file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
				return false;
			}

			foreach ($servers as $server)
			{
				if (!put_file("preview.jpg", $images_source_dir, "sources/$dir_path/$album_id", $server))
				{
					cancel_task_album(4, "Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}
			}

			$invalidate_files[] = "sources/$dir_path/$album_id/preview.jpg";
			unlink("$images_source_dir/preview.jpg");
		}

		$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=?", $album_id));
		foreach ($images as $image)
		{
			$image_id = $image['image_id'];

			$file_copied = false;
			foreach ($old_servers as $server)
			{
				if (get_file("$image_id.jpg", "sources/$dir_path/$album_id", $images_source_dir, $server))
				{
					$file_copied = true;
					break;
				}
			}
			if (!is_file("$images_source_dir/$image_id.jpg") || !$file_copied)
			{
				cancel_task_album(4, "Failed to get $image_id.jpg file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
				return false;
			}

			foreach ($servers as $server)
			{
				if (!put_file("$image_id.jpg", $images_source_dir, "sources/$dir_path/$album_id", $server))
				{
					cancel_task_album(4, "Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}
			}

			$invalidate_files[] = "sources/$dir_path/$album_id/$image_id.jpg";
			unlink("$images_source_dir/$image_id.jpg");

			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 1)
				{
					$file_copied = false;
					foreach ($old_servers as $server)
					{
						if (get_file("$image_id.jpg", "main/$format[size]/$dir_path/$album_id", $images_source_dir, $server))
						{
							$file_copied = true;
							break;
						}
					}
					if (!is_file("$images_source_dir/$image_id.jpg") || !$file_copied)
					{
						cancel_task_album(4, "Failed to get $image_id.jpg file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
						return false;
					}

					foreach ($servers as $server)
					{
						if (!put_file("$image_id.jpg", $images_source_dir, "main/$format[size]/$dir_path/$album_id", $server))
						{
							cancel_task_album(4, "Failed to put $image_id.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
							return false;
						}
					}

					$invalidate_files[] = "main/$format[size]/$dir_path/$album_id/$image_id.jpg";
					unlink("$images_source_dir/$image_id.jpg");
				}
			}
		}

		foreach ($formats_albums as $format)
		{
			if ($format['group_id'] == 2)
			{
				$file_copied = false;
				foreach ($old_servers as $server)
				{
					if (get_file("preview.jpg", "preview/$format[size]/$dir_path/$album_id", $images_source_dir, $server))
					{
						$file_copied = true;
						break;
					}
				}
				if (!is_file("$images_source_dir/preview.jpg") || !$file_copied)
				{
					cancel_task_album(4, "Failed to get preview.jpg file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}

				foreach ($servers as $server)
				{
					if (!put_file("preview.jpg", $images_source_dir, "preview/$format[size]/$dir_path/$album_id", $server))
					{
						cancel_task_album(4, "Failed to put preview.jpg file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
						return false;
					}
				}

				$invalidate_files[] = "preview/$format[size]/$dir_path/$album_id/preview.jpg";
				unlink("$images_source_dir/preview.jpg");
			}
		}

		$zip_files = get_album_zip_files($album_id, $res_album['zip_files']);
		foreach ($zip_files as $zip_file)
		{
			if ($zip_file['size'] == 'source')
			{
				$remote_dir = "sources/$dir_path/$album_id";
				$zip_filename = "$album_id.zip";
			} else
			{
				$remote_dir = "main/$zip_file[size]/$dir_path/$album_id";
				$zip_filename = "$album_id-$zip_file[size].zip";
			}

			$file_copied = false;
			foreach ($old_servers as $server)
			{
				if (get_file($zip_filename, $remote_dir, $images_source_dir, $server))
				{
					$file_copied = true;
					break;
				}
			}
			if (!is_file("$images_source_dir/$zip_filename") || !$file_copied)
			{
				cancel_task_album(4, "Failed to get $zip_filename file from storage servers, cancelling this task", $album_id, $task_data['task_id']);
				return false;
			}

			foreach ($servers as $server)
			{
				if (!put_file($zip_filename, $images_source_dir, $remote_dir, $server))
				{
					cancel_task_album(4, "Failed to put $zip_filename file to storage server \"$server[title]\", cancelling this task", $album_id, $task_data['task_id']);
					return false;
				}
			}

			$invalidate_files[] = "$remote_dir/$zip_filename";
			unlink("$images_source_dir/$zip_filename");
		}
	} finally
	{
		if (rmdir_recursive($images_source_dir))
		{
			@rmdir(dirname($images_source_dir));
		}
	}

	foreach ($servers as $server)
	{
		if ($server['streaming_type_id'] == 4) // CDN
		{
			$invalidate_folders = array("sources/$dir_path/$album_id");
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 1)
				{
					$invalidate_folders[] = "main/$format[size]/$dir_path/$album_id";
				}
				if ($format['group_id'] == 2)
				{
					$invalidate_folders[] = "preview/$format[size]/$dir_path/$album_id";
				}
			}
			cdn_invalidate_album($album_id, $server, $invalidate_folders, $invalidate_files, 'add');
		}
	}

	log_output_album("INFO  Migrated album from group $res_album[server_group_id] to group $server_group_id", $album_id);

	sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=2, album_id=?, data=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 24 HOUR)", $album_id, serialize(['old_server_group_id' => $res_album['server_group_id']]), date('Y-m-d H:i:s'));

	// complete task
	sql_update("update $config[tables_prefix]albums set server_group_id=? where album_id=?", $server_group_id, $album_id);

	log_output_album("INFO  Album migration task is completed for album $album_id [PH-FE]", $album_id);
	finish_task($task_data, time() - $task_start_time);
	return false;
}

function exec_sync_storage_server($task_data, $formats_albums)
{
	global $config, $options;

	$task_start_time = time();

	$server_id = intval($task_data['data']['server_id']);
	$res_server = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers where server_id=?", $server_id));
	if (!isset($res_server))
	{
		cancel_task(1, "Server $server_id is not available in the database, cancelling this task", 0, $task_data['task_id']);
	}

	$res_server_group = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers_groups where group_id=?", $res_server['group_id']));
	if (!isset($res_server_group))
	{
		cancel_task(1, "Server group $res_server[group_id] is not available in the database, cancelling this task", 0, $task_data['task_id']);
	}

	$servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $res_server['group_id']));
	if (count($servers) <= 1)
	{
		cancel_task(4, "Storage server \"$res_server[title]\" cannot be synced, cancelling this task", 0, $task_data['task_id']);
		return false;
	}

	if ($res_server['free_space'] < $options['SERVER_GROUP_MIN_FREE_SPACE_MB'] * 1024 * 1024)
	{
		warn_task("Storage server \"$res_server[title]\" has free space less than allowed, skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
		return false;
	}

	foreach ($servers as $server)
	{
		if (!test_connection_status($server))
		{
			warn_task("Failed to connect to storage server \"$server[title]\", skipping this task", 0, $task_data['task_id'], time() - $task_start_time);
			return false;
		}
	}

	if ($task_data['status_id'] == 1)
	{
		log_output("INFO  Server sync task is continued for server \"$res_server[title]\" [PH-I]");
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks set status_id=1 where task_id=?", $task_data['task_id']);
		log_output("INFO  Server sync task is started for server \"$res_server[title]\" [PH-I]");
	}

	$iteration_step = 100;

	$last_object_id = 0;
	$last_error_id = 0;
	$last_iteration_processed = 0;

	if ($res_server_group['content_type_id'] == 1)
	{
		$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and server_group_id=?", intval($res_server['group_id'])));
		$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and server_group_id=? and video_id<=?", intval($res_server['group_id']), intval($task_data['last_processed_id'])));

		$videos = mr2array(sql_pr("select video_id, file_formats from $config[tables_prefix]videos where status_id in (0,1) and server_group_id=? and video_id>? order by video_id asc limit $iteration_step", intval($res_server['group_id']), intval($task_data['last_processed_id'])));
		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$last_object_id = $video_id;
			$dir_path = get_dir_by_id($video_id);

			$video_task_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/$task_data[task_id]";
			if (!mkdir_recursive($video_task_dir))
			{
				$last_error_id = $video_id;
				log_output("ERROR Failed to create source directory for video $video_id: $video_task_dir");
			} else
			{
				$video_formats = get_video_formats($video_id, $video['file_formats']);
				foreach ($video_formats as $video_format)
				{
					$postfix = $video_format['postfix'];
					if (check_file("$video_id{$postfix}", "$dir_path/$video_id", $res_server) != $video_format['file_size'])
					{
						$file_copied = false;
						foreach ($servers as $server)
						{
							if ($server['server_id'] != $res_server['server_id'])
							{
								if (get_file("$video_id{$postfix}", "$dir_path/$video_id", $video_task_dir, $server))
								{
									$file_copied = true;
									break;
								}
							}
						}

						if (!is_file("$video_task_dir/$video_id{$postfix}") || !$file_copied)
						{
							foreach ($servers as $server)
							{
								if (intval($server['streaming_type_id']) == 4 && $server['server_id'] != $res_server['server_id'])
								{
									log_output("WARN  Failed to sync $video_id{$postfix} via server connection, trying to download it from CDN cache");
									if (cdn_download_video_file($server, "$dir_path/$video_id/$video_id{$postfix}", "$video_task_dir/$video_id{$postfix}", $video_format['file_size']))
									{
										$file_copied = true;
										break;
									}
								}
							}
						}

						if (!is_file("$video_task_dir/$video_id{$postfix}") || !$file_copied || !put_file("$video_id{$postfix}", $video_task_dir, "$dir_path/$video_id", $res_server))
						{
							$last_error_id = $video_id;
							log_output("ERROR Failed to sync $video_id{$postfix} for video $video_id");
						}
						@unlink("$video_task_dir/$video_id{$postfix}");
					}
				}
			}

			if ($last_error_id != $video_id)
			{
				log_output('', $video_id, 1, 1);
				log_output("INFO  Synced video $video_id to server \"$res_server[title]\"", $video_id);
			}

			rmdir_recursive($video_task_dir);

			$last_iteration_processed++;
			mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		}
	} else if ($res_server_group['content_type_id'] == 2)
	{
		$total_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1) and server_group_id=?", intval($res_server['group_id'])));
		$processed_objects = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where status_id in (0,1) and server_group_id=? and album_id<=?", intval($res_server['group_id']), intval($task_data['last_processed_id'])));

		$albums = mr2array(sql_pr("select album_id, zip_files, has_preview from $config[tables_prefix]albums where status_id in (0,1) and server_group_id=? and album_id>? order by album_id asc limit $iteration_step", intval($res_server['group_id']), intval($task_data['last_processed_id'])));
		foreach ($albums as $album)
		{
			$album_id = $album['album_id'];
			$last_object_id = $album_id;
			$dir_path = get_dir_by_id($album_id);

			$album_task_dir = "$config[content_path_albums_sources]/$dir_path/$album_id/$task_data[task_id]";
			if (!mkdir_recursive($album_task_dir))
			{
				$last_error_id = $album_id;
				log_output("ERROR Failed to create source directory for album $album_id: $album_task_dir");
			} else
			{
				if ($album['has_preview'] == 1)
				{
					if (!check_file("preview.jpg", "sources/$dir_path/$album_id", $res_server))
					{
						$file_copied = false;
						foreach ($servers as $server)
						{
							if ($server['server_id'] != $res_server['server_id'])
							{
								if (get_file("preview.jpg", "sources/$dir_path/$album_id", $album_task_dir, $server))
								{
									$file_copied = true;
									break;
								}
							}
						}

						if (!is_file("$album_task_dir/preview.jpg") || !$file_copied || !put_file("preview.jpg", $album_task_dir, "sources/$dir_path/$album_id", $res_server))
						{
							$last_error_id = $album_id;
							log_output("ERROR Failed to sync sources/preview.jpg for album $album_id");
						}
						@unlink("$album_task_dir/preview.jpg");
					}
				}

				foreach ($formats_albums as $format)
				{
					if ($format['group_id'] == 2)
					{
						if (!check_file("preview.jpg", "preview/$format[size]/$dir_path/$album_id", $res_server))
						{
							$file_copied = false;
							foreach ($servers as $server)
							{
								if ($server['server_id'] != $res_server['server_id'])
								{
									if (get_file("preview.jpg", "preview/$format[size]/$dir_path/$album_id", $album_task_dir, $server))
									{
										$file_copied = true;
										break;
									}
								}
							}
							if (!is_file("$album_task_dir/preview.jpg") || !$file_copied || !put_file("preview.jpg", $album_task_dir, "preview/$format[size]/$dir_path/$album_id", $res_server))
							{
								$last_error_id = $album_id;
								log_output("ERROR Failed to sync preview/$format[size]/preview.jpg for album $album_id");
							}
							@unlink("$album_task_dir/preview.jpg");
						}
					}
				}

				$images = mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=?", $album_id));
				foreach ($images as $image)
				{
					$image_id = $image['image_id'];
					$image_formats = get_image_formats($album_id, $image['image_formats']);

					foreach ($image_formats as $image_format)
					{
						$size = $image_format['size'];
						$basedir = "main/$image_format[size]";
						if ($size == 'source')
						{
							$basedir = 'sources';
						}
						if (check_file("$image_id.jpg", "$basedir/$dir_path/$album_id", $res_server) != $image_format['file_size'])
						{
							$file_copied = false;
							foreach ($servers as $server)
							{
								if ($server['server_id'] != $res_server['server_id'])
								{
									if (get_file("$image_id.jpg", "$basedir/$dir_path/$album_id", $album_task_dir, $server))
									{
										$file_copied = true;
										break;
									}
								}
							}
							if (!is_file("$album_task_dir/$image_id.jpg") || !$file_copied || !put_file("$image_id.jpg", $album_task_dir, "$basedir/$dir_path/$album_id", $res_server))
							{
								$last_error_id = $album_id;
								log_output("ERROR Failed to sync $basedir/$image_id.jpg for album $album_id");
							}
							@unlink("$album_task_dir/$image_id.jpg");
						}
					}
				}

				$zip_files = get_album_zip_files($album_id, $album['zip_files']);
				foreach ($zip_files as $zip_file)
				{
					$size = $zip_file['size'];
					$basedir = "main/$zip_file[size]";
					$filename = "$album_id-$size.zip";
					if ($size == 'source')
					{
						$basedir = 'sources';
						$filename = "$album_id.zip";
					}

					if (check_file($filename, "$basedir/$dir_path/$album_id", $res_server) != $zip_file['file_size'])
					{
						$file_copied = false;
						foreach ($servers as $server)
						{
							if ($server['server_id'] != $res_server['server_id'])
							{
								if (get_file($filename, "$basedir/$dir_path/$album_id", $album_task_dir, $server))
								{
									$file_copied = true;
									break;
								}
							}
						}
						if (!is_file("$album_task_dir/$filename") || !$file_copied || !put_file($filename, $album_task_dir, "$basedir/$dir_path/$album_id", $res_server))
						{
							$last_error_id = $album_id;
							log_output("ERROR Failed to sync $filename for album $album_id");
						}
						@unlink("$album_task_dir/$filename");
					}
				}
			}

			if ($last_error_id != $album_id)
			{
				log_output_album('', $album_id, 1, 1);
				log_output_album("INFO  Synced album $album_id to server \"$res_server[title]\"", $album_id);
			}

			if (rmdir_recursive($album_task_dir))
			{
				@rmdir(dirname($album_task_dir));
			}

			$last_iteration_processed++;
			mark_task_progress($task_data['task_id'], floor(100 * ($last_iteration_processed + $processed_objects) / $total_objects));
		}
	}

	if ($last_error_id > 0)
	{
		sql_update("update $config[tables_prefix]background_tasks set last_error_id=? where task_id=?", $last_error_id, $task_data['task_id']);
	}

	if ($last_iteration_processed == $iteration_step)
	{
		// postpone task
		log_output("INFO  Iteration synced $last_iteration_processed objects [PH-IE]");
		mark_task_duration($task_data['task_id'], time() - $task_start_time);
		sql_update("update $config[tables_prefix]background_tasks set last_processed_id=? where task_id=?", $last_object_id, $task_data['task_id']);
	} else
	{
		// complete task
		log_output("INFO  Iteration synced $last_iteration_processed objects");
		if ($last_error_id > 0 || $task_data['last_error_id'] > 0)
		{
			cancel_task(6, "Failed to sync some content, cancelling this task", 0, $task_data['task_id']);
		} else
		{
			log_output("INFO  Server sync task is finished for server \"$res_server[title]\" [PH-FE]");
			finish_task($task_data, time() - $task_start_time);
		}
	}

	return false;
}

function cdn_invalidate_video($video_id, $server, $folders, $files, $operation)
{
	global $config;

	$postponed_task = [];
	$postponed_task['streaming_script'] = $server['streaming_script'];
	$postponed_task['server_url'] = $server['urls'];
	if (isset($folders))
	{
		$postponed_task['folders'] = $folders;
	}
	$postponed_task['files'] = $files;
	$postponed_task['operation'] = $operation;
	sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=3, video_id=?, data=?, added_date=?, due_date=added_date", $video_id, serialize($postponed_task), date('Y-m-d H:i:s'));

	return true;
}

function cdn_invalidate_album($album_id, $server, $folders, $files, $operation)
{
	global $config;

	$postponed_task = [];
	$postponed_task['streaming_script'] = $server['streaming_script'];
	$postponed_task['server_url'] = $server['urls'];
	if (isset($folders))
	{
		$postponed_task['folders'] = $folders;
	}
	$postponed_task['files'] = $files;
	$postponed_task['operation'] = $operation;
	sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=4, album_id=?, data=?, added_date=?, due_date=added_date", $album_id, serialize($postponed_task), date('Y-m-d H:i:s'));

	return true;
}

function cdn_download_video_file($server, $remote_path, $local_path, $required_filesize)
{
	global $config;

	if (intval($server['streaming_type_id']) == 4)
	{
		$cdn_api_script = $server['streaming_script'];
		$cdn_api_name = str_replace('.php', '', $cdn_api_script);
		if (is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
		{
			require_once "$config[project_path]/admin/cdn/$cdn_api_script";
			$get_video_function = "{$cdn_api_name}_get_video";
			if (function_exists($get_video_function))
			{
				$target_url = "$server[urls]/$remote_path";
				$target_file = substr($target_url, strpos($target_url, '/', 8));
				$remote_url = $get_video_function($target_file, $target_url, null, 0, $server['streaming_key']);
				if (strpos($remote_url, '//') === 0)
				{
					$remote_url = "http:$remote_url";
				}
				save_file_from_url($remote_url, $local_path);
				if (sprintf("%.0f", filesize($local_path)) == $required_filesize)
				{
					return true;
				}
			}
		}
	}
	return false;
}

function print_formats_list($formats)
{
	$result = '';
	for ($i = 0; $i < count($formats); $i++)
	{
		$result .= '"' . $formats[$i]['title'] . '"';
		if ($i < count($formats) - 1)
		{
			$result .= ', ';
		}
	}
	if ($result == '')
	{
		$result = 'none';
	}
	return $result;
}

function print_object($obj)
{
	$result = '';
	if ($obj && is_array($obj))
	{
		foreach ($obj as $k => $v)
		{
			$result .= "$k: $v, ";
		}
	}
	if ($result == '')
	{
		$result = 'none';
	}
	return trim($result, ", ");
}

function analyze_screenshot($screenshot)
{
	if (!function_exists('imagecreatefromjpeg'))
	{
		return true;
	}

	$im = imagecreatefromjpeg($screenshot);
	$num_grey = 0;
	for ($i = 0; $i < imagesx($im); $i++)
	{
		for ($j = 0; $j < imagesy($im); $j++)
		{
			$rgb = imagecolorat($im, $i, $j);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			if ($r > 120 && $r < 140 && $g > 120 && $g < 140 && $b > 120 && $b < 140)
			{
				$num_grey++;
			}
		}
	}

	$result = true;
	if ($num_grey > imagesx($im) * imagesy($im) * 0.7)
	{
		$result = false;
	}
	imagedestroy($im);
	return $result;
}

function finish_task($task_data, $duration = 0)
{
	global $config;

	if (is_array($task_data['data']))
	{
		$task_data['data'] = serialize($task_data['data']);
	} else
	{
		$task_data['data'] = '';
	}

	sql_delete("delete from $config[tables_prefix]background_tasks where task_id=?", $task_data['task_id']);

	$task_duration = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/$task_data[task_id]_duration.dat")) + $duration;
	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks_history where task_id=?", $task_data['task_id'])) > 0)
	{
		sql_delete("delete from $config[tables_prefix]background_tasks_history where task_id=?", $task_data['task_id']);
	}
	sql_insert("insert into $config[tables_prefix]background_tasks_history set task_id=?, status_id=3, type_id=?, video_id=?, album_id=?, server_id=?, data=?, start_date=?, end_date=?, effective_duration=?", $task_data['task_id'], $task_data['type_id'], intval($task_data['video_id']), intval($task_data['album_id']), intval($task_data['server_id']), $task_data['data'], $task_data['start_date'], date('Y-m-d H:i:s'), $task_duration);

	@unlink("$config[project_path]/admin/data/engine/tasks/$task_data[task_id].dat");
	@unlink("$config[project_path]/admin/data/engine/tasks/$task_data[task_id]_duration.dat");
}

function cancel_task($error_code, $message, $video_id, $task_id, $server_data = null)
{
	global $config;

	log_output("ERROR($error_code)  $message [PH-E]", $video_id);
	sql_update("update $config[tables_prefix]background_tasks set status_id=2, last_processed_id=0, last_error_id=0, message=?, error_code=? where task_id=?", $message, $error_code, $task_id);

	if ($video_id > 0 && mr2number(sql_pr("select type_id from $config[tables_prefix]background_tasks where task_id=?", $task_id)) == 1)
	{
		// new video task
		sql_update("update $config[tables_prefix]videos set status_id=2 where status_id=3 and video_id=?", $video_id);
	}
	if (isset($server_data) && is_array($server_data))
	{
		delete_task_folder($task_id, $server_data);
	}
	@unlink("$config[project_path]/admin/data/engine/tasks/{$task_id}.dat");
	@unlink("$config[project_path]/admin/data/engine/tasks/{$task_id}_duration.dat");

	if ($video_id > 0)
	{
		$dir_path = get_dir_by_id($video_id);
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
		{
			sql_insert("insert into $config[tables_prefix]background_tasks_postponed set type_id=7, video_id=?, added_date=?, due_date=DATE_ADD(added_date, INTERVAL 1 HOUR)", $video_id, date('Y-m-d H:i:s'));
		}
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3"))
		{
			if (!unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3"))
			{
				log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp3", $video_id);
			}
		}
	}
}

function cancel_task_album($error_code, $message, $album_id, $task_id, $server_data = null)
{
	global $config;

	log_output_album("ERROR($error_code)  $message", $album_id);
	sql_update("update $config[tables_prefix]background_tasks set status_id=2, last_processed_id=0, last_error_id=0, message=?, error_code=? where task_id=?", $message, $error_code, $task_id);

	if ($album_id > 0 && mr2number(sql_pr("select type_id from $config[tables_prefix]background_tasks where task_id=?", $task_id)) == 10)
	{
		// new album task
		sql_update("update $config[tables_prefix]albums set status_id=2 where status_id=3 and album_id=?", $album_id);
	}
	if (isset($server_data) && is_array($server_data))
	{
		delete_task_folder($task_id, $server_data);
	}
	@unlink("$config[project_path]/admin/data/engine/tasks/{$task_id}.dat");
	@unlink("$config[project_path]/admin/data/engine/tasks/{$task_id}_duration.dat");
}

function warn_task($message, $video_id, $task_id, $duration = 0)
{
	global $config;

	log_output("WARN  $message", $video_id);
	sql_update("update $config[tables_prefix]background_tasks set message=? where task_id=?", $message, $task_id);

	if ($duration)
	{
		mark_task_duration($task_id, $duration);
	}
}

function warn_task_album($message, $album_id, $task_id, $duration = 0)
{
	global $config;

	log_output_album("WARN  $message", $album_id);
	sql_update("update $config[tables_prefix]background_tasks set message=? where task_id=?", $message, $task_id);

	if ($duration)
	{
		mark_task_duration($task_id, $duration);
	}
}

function delete_task_folder($task_id, $server_data)
{
	global $config;

	if (isset($server_data) && $server_data['server_id'] > 0)
	{
		$rnd = mt_rand(1000000, 9999999);
		if (mkdir_recursive("$config[temporary_path]/$rnd"))
		{
			file_put_contents("$config[temporary_path]/$rnd/deleted.dat", '1', LOCK_EX);
			put_file('deleted.dat', "$config[temporary_path]/$rnd", "$task_id", $server_data);
			rmdir_recursive("$config[temporary_path]/$rnd");
		}
	}
}

function mark_task_progress($task_id, $pc)
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/engine/tasks");
	if (intval($pc) == 100)
	{
		@unlink("$config[project_path]/admin/data/engine/tasks/$task_id.dat");
	} else
	{
		file_put_contents("$config[project_path]/admin/data/engine/tasks/$task_id.dat", intval($pc), LOCK_EX);
	}
}

function mark_task_duration($task_id, $duration)
{
	global $config;

	if (!$task_id || !$duration)
	{
		return;
	}

	mkdir_recursive("$config[project_path]/admin/data/engine/tasks");

	$old_duration = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/{$task_id}_duration.dat"));
	file_put_contents("$config[project_path]/admin/data/engine/tasks/{$task_id}_duration.dat", $old_duration + intval($duration), LOCK_EX);
}

function log_output($message, $video_id = 0, $no_date = 0, $no_task = 0)
{
	global $config, $global_current_task_id;

	if ($message)
	{
		if (intval($no_date) == 0)
		{
			$message = date("[Y-m-d H:i:s] ") . $message;
		}
	}
	if ($no_task == 0)
	{
		echo "$message\n";
		file_put_contents("$config[project_path]/admin/logs/cron_conversion.txt", "$message\n", FILE_APPEND | LOCK_EX);

		if (intval($global_current_task_id) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/tasks/$global_current_task_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
		}
	}

	if (intval($video_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}

function log_output_album($message, $album_id = 0, $no_date = 0, $no_task = 0)
{
	global $config, $global_current_task_id;

	if ($message)
	{
		if (intval($no_date) == 0)
		{
			$message = date("[Y-m-d H:i:s] ") . $message;
		}
	}
	if ($no_task == 0)
	{
		echo "$message\n";
		file_put_contents("$config[project_path]/admin/logs/cron_conversion.txt", "$message\n", FILE_APPEND | LOCK_EX);

		if (intval($global_current_task_id) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/tasks/$global_current_task_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
		}
	}

	if (intval($album_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/albums/$album_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}
