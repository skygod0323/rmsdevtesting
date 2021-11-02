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

if (!is_file("$config[project_path]/admin/data/system/cron_postponed_tasks.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_postponed_tasks.lock", '1', LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_postponed_tasks.lock", 'r+');
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors', 1);
sql('set wait_timeout=86400');

file_put_contents("$config[project_path]/admin/logs/cron_postponed_tasks.txt", "", LOCK_EX);

log_output("INFO  Postponed tasks processor started");

// get initial data
$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by format_video_id asc"));
$formats_screenshots = mr2array(sql_pr("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) order by format_screenshot_id asc"));
$formats_albums = mr2array(sql_pr("select * from $config[tables_prefix]formats_albums where status_id in (0,1) order by format_album_id asc"));

log_output("INFO  Active video formats: " . count($formats_videos));
log_output("INFO  Active screenshot formats: " . count($formats_screenshots));
log_output("INFO  Active album formats: " . count($formats_albums));

log_output('');

// get postponed tasks
sleep(1);
$data = mr2array(sql_pr("select * from $config[tables_prefix]background_tasks_postponed where due_date<? order by task_id asc limit 1000", date('Y-m-d H:i:s')));
log_output('');
log_output("INFO  Postponed tasks: " . count($data));

foreach ($data as $res)
{
	log_output('');
	log_output("INFO  Starting task $res[task_id]");

	if ($res['data'])
	{
		$res['data'] = @unserialize($res['data']);
	}

	switch ($res['type_id'])
	{
		case 1:
			exec_postponed_video_migration($res);
			break;
		case 2:
			exec_postponed_album_migration($res, $formats_albums);
			break;
		case 3:
			exec_postponed_cdn_invalidate_video($res);
			break;
		case 4:
			exec_postponed_cdn_invalidate_album($res);
			break;
		case 5:
			exec_postponed_cleanup_remote_task($res);
			break;
		case 6:
			exec_postponed_cleanup_video_source_file($res);
			break;
		case 7:
			exec_postponed_cleanup_video_source_file2($res);
			break;
	}
}

disconnect_all_servers();

log_output('');
log_output("INFO  Postponed tasks processor finished");
flock($lock, LOCK_UN);
fclose($lock);

function exec_postponed_video_migration($task_data)
{
	global $config;

	$video_id = intval($task_data['video_id']);
	$server_group_id = intval($task_data['data']['old_server_group_id']);

	log_output('', $video_id);
	log_output("INFO  Video post-migration cleanup task is started for video $video_id", $video_id);

	$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?", $video_id));
	if ($res_video['server_group_id'] != $server_group_id)
	{
		$dir_path = get_dir_by_id($video_id);
		$old_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
		foreach ($old_servers as $server)
		{
			delete_dir("$dir_path/$video_id", $server);

			if ($res_video['video_id'] > 0 && $server['streaming_type_id'] == 4) // CDN
			{
				$formats = get_video_formats($video_id, $res_video['file_formats']);
				$invalidate_files = array();
				foreach ($formats as $format_rec)
				{
					$invalidate_files[] = "$dir_path/$video_id/$video_id{$format_rec['postfix']}";
				}
				exec_postponed_cdn_invalidate_video(['task_id' => 0, 'video_id' => $video_id, 'data' => ['streaming_script' => $server['streaming_script'], 'server_url' => $server['urls'], 'folders' => ["$dir_path/$video_id"], 'files' => $invalidate_files, 'operation' => 'delete']]);
			}
		}

		log_output("INFO  Deleted obsolete video files on group $server_group_id", $video_id);
	} else
	{
		log_output("WARN  Cleanup on group $server_group_id is not needed", $video_id);
	}

	// complete task
	log_output("INFO  Video post-migration cleanup task is completed for video $video_id", $video_id);
	sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
}

function exec_postponed_album_migration($task_data, $formats_albums)
{
	global $config;

	$album_id = intval($task_data['album_id']);
	$server_group_id = intval($task_data['data']['old_server_group_id']);

	log_output_album('', $album_id);
	log_output_album("INFO  Album post-migration cleanup task is started for album $album_id", $album_id);

	$res_album = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=?", $album_id));
	if ($res_album['server_group_id'] != $server_group_id)
	{
		$image_ids = mr2array_list(sql_pr("select image_id from $config[tables_prefix]albums_images where album_id=? order by image_id asc", $album_id));

		$dir_path = get_dir_by_id($album_id);
		$old_servers = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=?", $server_group_id));
		foreach ($old_servers as $server)
		{
			delete_dir("sources/$dir_path/$album_id", $server);
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 1)
				{
					delete_dir("main/$format[size]/$dir_path/$album_id", $server);
				}
				if ($format['group_id'] == 2)
				{
					delete_dir("preview/$format[size]/$dir_path/$album_id", $server);
				}
			}

			if ($server['streaming_type_id'] == 4) // CDN
			{
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
				exec_postponed_cdn_invalidate_album(['task_id' => 0, 'album_id' => $album_id, 'data' => ['streaming_script' => $server['streaming_script'], 'server_url' => $server['urls'], 'folders' => $invalidate_folders, 'files' => $invalidate_files, 'operation' => 'delete']]);
			}
		}

		log_output_album("INFO  Deleted obsolete album files on group $server_group_id", $album_id);
	} else
	{
		log_output_album("WARN  Cleanup on group $server_group_id is not needed", $album_id);
	}

	// complete task
	log_output_album("INFO  Album post-migration cleanup task is completed for album $album_id", $album_id);
	sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
}

function exec_postponed_cdn_invalidate_video($task_data)
{
	global $config;

	$video_id = intval($task_data['video_id']);
	$server_url = $task_data['data']['server_url'];
	$cdn_api_script = $task_data['data']['streaming_script'];
	$cdn_api_name = str_replace('.php', '', $cdn_api_script);

	log_output('', $video_id);
	log_output("INFO  CDN invalidation task is started for video $video_id", $video_id);

	if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
	{
		log_output("WARN  CDN control script is missing: /admin/cdn/$cdn_api_script", $video_id);
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	require_once "$config[project_path]/admin/cdn/$cdn_api_script";
	$invalidate_function = "{$cdn_api_name}_invalidate_resources";
	if (!function_exists($invalidate_function))
	{
		log_output("WARN  CDN control script does not contain $invalidate_function() function: /admin/cdn/$cdn_api_script", $video_id);
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	$invalidate_function($server_url, $task_data['data']['folders'], $task_data['data']['files'], $task_data['data']['operation']);
	log_output("INFO  Invalidated " . count($task_data['data']['files']) . " files", $video_id);

	sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
}

function exec_postponed_cdn_invalidate_album($task_data)
{
	global $config;

	$album_id = intval($task_data['album_id']);
	$server_url = $task_data['data']['server_url'];
	$cdn_api_script = $task_data['data']['streaming_script'];
	$cdn_api_name = str_replace('.php', '', $cdn_api_script);

	log_output_album('', $album_id);
	log_output_album("INFO  CDN invalidation task is started for album $album_id", $album_id);

	if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
	{
		log_output_album("WARN  CDN control script is missing: /admin/cdn/$cdn_api_script", $album_id);
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	require_once "$config[project_path]/admin/cdn/$cdn_api_script";
	$invalidate_function = "{$cdn_api_name}_invalidate_resources";
	if (!function_exists($invalidate_function))
	{
		log_output_album("WARN  CDN control script does not contain $invalidate_function() function: /admin/cdn/$cdn_api_script", $album_id);
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	$invalidate_function($server_url, $task_data['data']['folders'], $task_data['data']['files'], $task_data['data']['operation']);
	log_output_album("INFO  Invalidated " . count($task_data['data']['files']) . " files", $album_id);

	sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
}

function exec_postponed_cleanup_remote_task($task_data)
{
	global $config;

	$task_id = intval($task_data['data']['task_id']);
	$server_id = intval($task_data['data']['server_id']);

	log_output_task("INFO  Post cleanup task is started for deleted task $task_id", $task_id);

	$server_data = mr2array_single(sql_pr("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where status_id=1 and server_id=?", $server_id));
	if (isset($server_data))
	{
		$rnd = mt_rand(1000000, 9999999);
		if (mkdir_recursive("$config[temporary_path]/$rnd"))
		{
			file_put_contents("$config[temporary_path]/$rnd/deleted.dat", '1', LOCK_EX);
			put_file('deleted.dat', "$config[temporary_path]/$rnd", "$task_id", $server_data);
			rmdir_recursive("$config[temporary_path]/$rnd");
		}
	}
	sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
}

function exec_postponed_cleanup_video_source_file($task_data)
{
	global $config;

	$video_id = intval($task_data['video_id']);
	$dir_path = get_dir_by_id($video_id);

	if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
	{
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where video_id=?", $video_id)) == 0)
	{
		log_output("", $video_id);
		log_output("INFO  Video source file cleanup task is started for video $video_id", $video_id);
		if (@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
		{
			log_output("INFO  Video source file deleted", $video_id);
		} else
		{
			log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp", $video_id);
		}
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks_postponed set due_date=DATE_ADD(?, INTERVAL 1 HOUR) where task_id=?", date('Y-m-d H:i:s'), $task_data['task_id']);
	}
}

function exec_postponed_cleanup_video_source_file2($task_data)
{
	global $config;

	$video_id = intval($task_data['video_id']);
	$dir_path = get_dir_by_id($video_id);

	if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
	{
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
		return;
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where video_id=?", $video_id)) == 0)
	{
		log_output('', $video_id);
		log_output("INFO  Video source file cleanup task is started for video $video_id", $video_id);
		if (@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2"))
		{
			log_output("INFO  Video source file deleted", $video_id);
		} else
		{
			log_output("WARN  Failed to delete source file: $config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp2", $video_id);
		}
		sql_delete("delete from $config[tables_prefix]background_tasks_postponed where task_id=?", $task_data['task_id']);
	} else
	{
		sql_update("update $config[tables_prefix]background_tasks_postponed set due_date=DATE_ADD(?, INTERVAL 1 HOUR) where task_id=?", date('Y-m-d H:i:s'), $task_data['task_id']);
	}
}

function log_output($message, $video_id = 0, $no_date = 0)
{
	global $config;

	if ($message)
	{
		if (intval($no_date) == 0)
		{
			$message = date("[Y-m-d H:i:s] ") . $message;
		}
	}
	echo "$message\n";
	file_put_contents("$config[project_path]/admin/logs/cron_postponed_tasks.txt", "$message\n", FILE_APPEND | LOCK_EX);

	if (intval($video_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}

function log_output_album($message, $album_id = 0, $no_date = 0)
{
	global $config;

	if ($message)
	{
		if (intval($no_date) == 0)
		{
			$message = date("[Y-m-d H:i:s] ") . $message;
		}
	}
	echo "$message\n";
	file_put_contents("$config[project_path]/admin/logs/cron_postponed_tasks.txt", "$message\n", FILE_APPEND | LOCK_EX);

	if (intval($album_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/albums/$album_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}

function log_output_task($message, $task_id, $no_date = 0)
{
	global $config;

	if ($message)
	{
		if (intval($no_date) == 0)
		{
			$message = date("[Y-m-d H:i:s] ") . $message;
		}
	}
	echo "$message\n";
	file_put_contents("$config[project_path]/admin/logs/cron_postponed_tasks.txt", "$message\n", FILE_APPEND | LOCK_EX);
	file_put_contents("$config[project_path]/admin/logs/tasks/$task_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
}
