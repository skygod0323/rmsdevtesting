<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function content_statsInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/content_stats");
}

function content_statsShow()
{
	global $config, $page_name;

	content_statsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/content_stats";
	$old_results_lifetime_days = 30;

	if ($_GET['action'] == 'progress')
	{
		$task_id = intval($_GET['task_id']);
		$pc = intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header('Content-Type: text/xml');

		$location = '';
		if ($pc == 100)
		{
			$location = "<location>plugins.php?plugin_id=content_stats&amp;action=results&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>";
		die;
	} elseif ($_GET['action'] == 'log')
	{
		header('Content-Type: text/plain; charset=utf8');
		$task_id = intval($_GET['task_id']);
		if (is_file("$plugin_path/task-log-$task_id.dat"))
		{
			echo @file_get_contents("$plugin_path/task-log-$task_id.dat");
		}
		die;
	} elseif ($_POST['action'] == 'calculate')
	{
		if (is_writable("$plugin_path"))
		{
			exec("find $plugin_path -name '*.dat' -mtime +$old_results_lifetime_days -delete");
		}
		$rnd = mt_rand(10000000, 99999999);
		exec("$config[php_path] $config[project_path]/admin/plugins/content_stats/content_stats.php $rnd > $plugin_path/task-log-$rnd.dat &");
		return_ajax_success("$page_name?plugin_id=content_stats&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}", 2);
	}

	if (intval($_GET['task_id']) > 0)
	{
		$task_id = intval($_GET['task_id']);
		if (is_file("$plugin_path/task-$task_id.dat"))
		{
			$result = @unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
			if (isset($result['stats'], $result['end_date']))
			{
				$_POST['result'] = $result;
			}
		}
	}

	$results_time = [];
	$results_values = [];
	$results = scandir($plugin_path);
	foreach ($results as $file)
	{
		if (is_file("$plugin_path/$file") && strpos($file, 'task-') === 0 && strpos($file, 'task-progress') === false && strpos($file, 'task-log') === false)
		{
			$results_data = @unserialize(file_get_contents("$plugin_path/$file"));
			if (isset($results_data['task_id']) && time() - intval($results_data['start_date']) < $old_results_lifetime_days * 86400)
			{
				if (!isset($results_data['endtime']))
				{
					$task_id = intval($results_data['task_id']);
					$results_data['progress'] = @intval(file_get_contents("$plugin_path/task-progress-$task_id.dat"));
				}
				$results_time[] = filectime("$plugin_path/$file");
				$results_values[] = $results_data;
			}
		}
	}
	array_multisort($results_time, SORT_NUMERIC, SORT_DESC, $results_values);
	$_POST['recent_calculations'] = $results_values;

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}

	$temp = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers_groups"));
	foreach ($temp as $server_group)
	{
		$_POST['server_groups'][$server_group['group_id']] = $server_group;
	}
}

function content_statsLogMessage($message)
{
	if ($message)
	{
		echo date("[Y-m-d H:i:s]: ") . "$message\n";
	} else
	{
		echo "\n";
	}
}

$task_id = intval($_SERVER['argv'][1]);
if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'include/setup.php';
	require_once 'include/functions_base.php';
	require_once 'include/functions.php';

	ini_set('display_errors', 1);

	$plugin_path = "$config[project_path]/admin/data/plugins/content_stats";

	$start_date = time();
	file_put_contents("$plugin_path/task-$task_id.dat", serialize(['start_date' => $start_date, 'task_id' => $task_id]));

	$options = get_options(['LIMIT_MEMORY']);
	$memory_limit = intval($options['LIMIT_MEMORY']);
	if ($memory_limit == 0)
	{
		$memory_limit = 512;
	}
	ini_set('memory_limit', "{$memory_limit}M");

	content_statsLogMessage("Content stats calculation started");
	content_statsLogMessage("Memory limit: " . ini_get('memory_limit'));

	$result = [];

	$formats_screenshots = mr2array(sql_pr("select * from $config[tables_prefix]formats_screenshots"));
	$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos"));
	$formats_albums = mr2array(sql_pr("select * from $config[tables_prefix]formats_albums"));

	$temp = [];
	foreach ($formats_videos as $format)
	{
		$temp[$format['postfix']] = $format;
	}
	$formats_videos = $temp;

	$temp = [];
	foreach ($formats_albums as $format)
	{
		$temp[$format['size']] = $format;
	}
	$formats_albums = $temp;

	$total_videos = mr2number(sql_pr("select count(*) from $config[tables_prefix]videos"));
	$total_albums = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums"));
	$total_images = mr2number(sql_pr("select count(*) from $config[tables_prefix]albums_images"));

	$average_images_per_album = floatval($total_images / $total_albums);
	if (!$average_images_per_album)
	{
		$average_images_per_album = 1;
	}

	$done_amount_of_work = 0;
	$total_amount_of_work = $total_videos * $average_images_per_album + $total_albums + $total_images;
	$last_pc = 0;

	$iteration_step = 1000;

	content_statsLogMessage('');
	content_statsLogMessage('Video stats calculation started');

	$start_time = time();
	$start_memory = memory_get_peak_usage();
	$last_object_id = 0;
	$processed_items = 0;
	while (true)
	{
		$videos = mr2array(sql_pr("select video_id, server_group_id, file_formats, screen_amount from $config[tables_prefix]videos where video_id>? order by video_id asc limit ?", $last_object_id, $iteration_step));
		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$last_object_id = $video_id;
			$dir_path = get_dir_by_id($video_id);

			$key = "100/videos";
			if (!$result[$key])
			{
				$result[$key] = ['type' => 'videos', 'total' => 0, 'is_group' => 1];
			}
			$result[$key]['total']++;

			if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
			{
				$key = "101/video_sources";
				if (!$result[$key])
				{
					$result[$key] = ['type' => 'video_sources', 'storage' => 'main_server', 'size' => 0, 'files' => 0];
				}
				$result[$key]['size'] += 0 + floatval(sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp")));
				$result[$key]['files']++;
			}

			if ($video['file_formats'])
			{
				$video_formats = get_video_formats($video_id, $video['file_formats']);
				foreach ($video_formats as $format_data)
				{
					$postfix = $format_data['postfix'];
					$title = $formats_videos[$postfix]['title'];
					$timeline_directory = $formats_videos[$postfix]['timeline_directory'];

					$key = "102/video_format[$postfix]";
					if (!$result[$key])
					{
						$result[$key] = ['type' => 'video_formats', 'format' => $title, 'storage' => 'content_server', 'size' => 0, 'files' => 0];
					}
					$result[$key]['size'] += 0 + floatval(sprintf("%.0f", $format_data['file_size']));
					$result[$key]['files']++;

					$key = "102/video_format[$postfix]/server[$video[server_group_id]]";
					if (!$result[$key])
					{
						$result[$key] = ['type' => 'video_formats', 'format' => $title, 'storage' => 'content_server', 'server_group_id' => $video['server_group_id'], 'parent_key' => "102/video_format[$postfix]", 'size' => 0, 'files' => 0];
					}
					$result[$key]['size'] += 0 + floatval(sprintf("%.0f", $format_data['file_size']));
					$result[$key]['files']++;

					if ($format_data['timeline_screen_amount'] > 0 && $timeline_directory)
					{
						$key = "102/video_format[$postfix]/timelines";
						if (!$result[$key])
						{
							$result[$key] = ['type' => 'video_timelines', 'format' => $title, 'storage' => 'main_server', 'size' => 0, 'files' => 0];
						}
						for ($i = 1; $i <= $format_data['timeline_screen_amount']; $i++)
						{
							$result[$key]['size'] += filesize("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_directory/$i.jpg");
							$result[$key]['files']++;
						}
						foreach ($formats_screenshots as $format_screenshots)
						{
							if ($format_screenshots['group_id'] == 2)
							{
								for ($i = 1; $i <= $format_data['timeline_screen_amount']; $i++)
								{
									$result[$key]['size'] += filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_directory/$format_screenshots[size]/$i.jpg");
									$result[$key]['files']++;
								}
								if (is_file("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_directory/$format_screenshots[size]/$video_id-$format_screenshots[size].zip"))
								{
									$result[$key]['size'] += filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_directory/$format_screenshots[size]/$video_id-$format_screenshots[size].zip");
									$result[$key]['files']++;
								}
							}
						}
					}
				}
			}

			$key = "103/video_screenshots[source]";
			if (!$result[$key])
			{
				$result[$key] = ['type' => 'video_screenshots_sources', 'storage' => 'main_server', 'size' => 0, 'files' => 0];
			}
			for ($i = 1; $i <= $video['screen_amount']; $i++)
			{
				if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg"))
				{
					$result[$key]['size'] += filesize("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
					$result[$key]['files']++;
				}
			}
			foreach ($formats_screenshots as $format_screenshots)
			{
				if ($format_screenshots['group_id'] == 1)
				{
					$key = "104/video_screenshots[$format_screenshots[format_screenshot_id]]";
					if (!$result[$key])
					{
						$result[$key] = ['type' => 'video_screenshots_formats', 'format' => $format_screenshots['title'], 'storage' => 'main_server', 'size' => 0, 'files' => 0];
					}
					for ($i = 1; $i <= $video['screen_amount']; $i++)
					{
						if (is_file("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format_screenshots[size]/$i.jpg"))
						{
							$result[$key]['size'] += filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format_screenshots[size]/$i.jpg");
							$result[$key]['files']++;
						}
					}

					if (is_file("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format_screenshots[size]/$video_id-$format_screenshots[size].zip"))
					{
						$key = "104/video_screenshots_zip[$format_screenshots[format_screenshot_id]]";
						if (!$result[$key])
						{
							$result[$key] = ['type' => 'video_screenshots_zip', 'format' => $format_screenshots['title'], 'storage' => 'main_server', 'size' => 0, 'files' => 0];
						}
						$result[$key]['size'] += filesize("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format_screenshots[size]/$video_id-$format_screenshots[size].zip");
						$result[$key]['files']++;
					}
				}
			}

			$done_amount_of_work += $average_images_per_album;
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			if ($pc > $last_pc)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
				$last_pc = $pc;
			}
			usleep(2000);
		}
		$processed_items += count($videos);
		content_statsLogMessage("$processed_items ($last_pc%)...");

		if (count($videos) < $iteration_step)
		{
			break;
		}
		usleep(20000);
	}

	unset($res);
	exec("du -b $config[project_path]/admin/logs/videos", $res);
	$size = intval(trim($res[0]));
	unset($res);
	exec("ls $config[project_path]/admin/logs/videos | wc -l", $res);
	$count = intval(trim($res[0]));
	$result['110/video_logs'] = ['type' => 'video_logs', 'storage' => 'main_server', 'size' => $size, 'files' => $count];

	$end_time = time() - $start_time;
	$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
	content_statsLogMessage("Video stats calculation in {$end_time}s using $end_memory of memory ($last_pc%)");

	content_statsLogMessage('');
	content_statsLogMessage('Image stats calculation started');

	$start_time = time();
	$start_memory = memory_get_peak_usage();
	$last_object_id = 0;
	$processed_items = 0;
	while (true)
	{
		$images = mr2array(sql_pr("select ai.image_id, ai.album_id, a.server_group_id, ai.image_formats from $config[tables_prefix]albums_images ai inner join $config[tables_prefix]albums a on a.album_id=ai.album_id where ai.image_id>? order by ai.image_id asc limit ?", $last_object_id, $iteration_step));
		foreach ($images as $image)
		{
			$image_id = $image['image_id'];
			$album_id = $image['album_id'];
			$last_object_id = $image_id;

			$image_formats = get_image_formats($album_id, $image['image_formats']);
			foreach ($image_formats as $format_data)
			{
				$key = "201/album_images[$format_data[size]]";
				if (!$result[$key])
				{
					$result[$key] = ['storage' => 'content_server', 'size' => 0, 'files' => 0];
				}
				if ($format_data['size'] == 'source')
				{
					$result[$key]['type'] = 'album_images_sources';
				} else
				{
					$result[$key]['type'] = 'album_images_formats';
					$result[$key]['format'] = $formats_albums[$format_data['size']]['title'];
				}
				$result[$key]['size'] += $format_data['file_size'];
				$result[$key]['files']++;

				$key = "201/album_images[$format_data[size]]/server[$image[server_group_id]]";
				if (!$result[$key])
				{
					$result[$key] = ['storage' => 'content_server', 'server_group_id' => $image['server_group_id'], 'parent_key' => "201/album_images[$format_data[size]]", 'size' => 0, 'files' => 0];
				}
				if ($format_data['size'] == 'source')
				{
					$result[$key]['type'] = 'album_images_sources';
				} else
				{
					$result[$key]['type'] = 'album_images_formats';
					$result[$key]['format'] = $formats_albums[$format_data['size']]['title'];
				}
				$result[$key]['size'] += $format_data['file_size'];
				$result[$key]['files']++;
			}

			$done_amount_of_work++;
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			if ($pc > $last_pc)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
				$last_pc = $pc;
			}
		}
		$processed_items += count($images);
		content_statsLogMessage("$processed_items ($last_pc%)...");

		if (count($images) < $iteration_step)
		{
			break;
		}
		usleep(20000);
	}

	$end_time = time() - $start_time;
	$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
	content_statsLogMessage("Image stats calculation in {$end_time}s using $end_memory of memory ($last_pc%)");

	content_statsLogMessage('');
	content_statsLogMessage('Album stats calculation started');

	$start_time = time();
	$start_memory = memory_get_peak_usage();
	$last_object_id = 0;
	$processed_items = 0;
	while (true)
	{
		$albums = mr2array(sql_pr("select album_id, status_id, server_group_id, zip_files from $config[tables_prefix]albums where album_id>? order by album_id asc limit ?", $last_object_id, $iteration_step));
		foreach ($albums as $album)
		{
			$album_id = $album['album_id'];
			$last_object_id = $album_id;

			$key = "200/albums";
			if (!$result[$key])
			{
				$result[$key] = ['type' => 'albums', 'total' => 0, 'is_group' => 1];
			}
			$result[$key]['total']++;

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			foreach ($zip_files as $zip_file)
			{
				$key = "202/album_images_zip[$zip_file[size]]";
				if (!$result[$key])
				{
					$result[$key] = ['storage' => 'content_server', 'size' => 0, 'files' => 0];
				}
				if ($zip_file['size'] == 'source')
				{
					$result[$key]['type'] = 'album_images_sources_zip';
				} else
				{
					$result[$key]['type'] = 'album_images_zip';
					$result[$key]['format'] = $formats_albums[$zip_file['size']]['title'];
				}
				$result[$key]['size'] += $zip_file['file_size'];
				$result[$key]['files']++;

				$key = "202/album_images_zip[$zip_file[size]]/server[$album[server_group_id]]";
				if (!$result[$key])
				{
					$result[$key] = ['storage' => 'content_server', 'server_group_id' => $album['server_group_id'], 'parent_key' => "202/album_images_zip[$zip_file[size]]", 'size' => 0, 'files' => 0];
				}
				if ($zip_file['size'] == 'source')
				{
					$result[$key]['type'] = 'album_images_sources_zip';
				} else
				{
					$result[$key]['type'] = 'album_images_zip';
					$result[$key]['format'] = $formats_albums[$zip_file['size']]['title'];
				}
				$result[$key]['size'] += $zip_file['file_size'];
				$result[$key]['files']++;
			}

			$done_amount_of_work++;
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			if ($pc > $last_pc)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
				$last_pc = $pc;
			}
		}
		$processed_items += count($albums);
		content_statsLogMessage("$processed_items ($last_pc%)...");

		if (count($albums) < $iteration_step)
		{
			break;
		}
		usleep(20000);
	}

	unset($res);
	exec("du -b $config[project_path]/admin/logs/albums", $res);
	$size = intval(trim($res[0]));
	unset($res);
	exec("ls $config[project_path]/admin/logs/albums | wc -l", $res);
	$count = intval(trim($res[0]));
	$result['210/album_logs'] = ['type' => 'album_logs', 'storage' => 'main_server', 'size' => $size, 'files' => $count];

	$end_time = time() - $start_time;
	$end_memory = sizeToHumanString(memory_get_peak_usage() - $start_memory);
	content_statsLogMessage("Album stats calculation in {$end_time}s using $end_memory of memory ($last_pc%)");

	ksort($result, SORT_ASC);

	$total_main = ['type' => 'total_main', 'storage' => 'main_server', 'size' => 0, 'files' => 0];
	$total_content = ['type' => 'total_content', 'storage' => 'content_server', 'size' => 0, 'files' => 0];
	$total_content_servers = [];
	foreach ($result as $k => $v)
	{
		if ($v['storage'] == 'main_server')
		{
			$total_main['size'] += $v['size'];
			$total_main['files'] += $v['files'];
		}
		if ($v['storage'] == 'content_server')
		{
			if ($v['server_group_id'] > 0)
			{
				if (!isset($total_content_servers[$v['server_group_id']]))
				{
					$total_content_servers[$v['server_group_id']] = ['type' => 'total_content', 'storage' => 'content_server', 'server_group_id' => $v['server_group_id'], 'parent_key' => "900/content",'size' => 0, 'files' => 0];
				}
				$total_content_servers[$v['server_group_id']]['size'] += $v['size'];
				$total_content_servers[$v['server_group_id']]['files'] += $v['files'];
			} else
			{
				$total_content['size'] += $v['size'];
				$total_content['files'] += $v['files'];
			}
		}
	}
	$result['900/main'] = $total_main;
	$result['900/content'] = $total_content;
	foreach ($total_content_servers as $server_group_id => $server)
	{
		$result["900/content/server[$server_group_id]"] = $server;
	}

	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100");
	file_put_contents("$plugin_path/task-$task_id.dat", serialize(['stats' => $result, 'start_date' => $start_date, 'end_date' => time(), 'task_id' => $task_id]));
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
