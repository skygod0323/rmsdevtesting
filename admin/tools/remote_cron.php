<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

ini_set('display_errors', 1);
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);

if ($_SERVER['PWD'] == '')
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}
if (!is_dir($_SERVER['PWD']))
{
	die("No current directory information is available");
}
if (!is_file("$_SERVER[PWD]/remote_cron.php"))
{
	$_SERVER['PWD'] = dirname($_SERVER['PHP_SELF']);
}
if (!is_dir($_SERVER['PWD']))
{
	die("No current directory information is available");
}

$api_version = '5.2.0';
if ($_SERVER['argv'][1] == 'version')
{
	echo $api_version;
	die;
}

$GLOBAL_FTP_SERVERS = [];
umask(0);

$ffmpeg_path = "";
$imagemagick_path = "";
$time_offset = 0;
$screenshot_options = "-vframes 1 -f mjpeg -qscale 1";
$memory = '1024M';
$is_debug_enabled = false;
$logging_padding_level = 0;

if (!is_file("$_SERVER[PWD]/config.properties"))
{
	$ffmpeg_path = "/usr/bin/ffmpeg";
	unset($exec_res);
	exec("$ffmpeg_path -version 2>&1", $exec_res);
	if (stripos(implode("\n", $exec_res), 'ffmpeg version ') === false)
	{
		$ffmpeg_path = "/usr/local/bin/ffmpeg";
	}

	$imagemagick_path = "/usr/bin/convert";
	unset($exec_res);
	exec("$imagemagick_path 2>&1", $exec_res);
	if (stripos(implode("\n", $exec_res), 'imagemagick') === false)
	{
		$imagemagick_path = "/usr/local/bin/convert";
	}

	$default_config = '';
	$default_config .= "# ffmpeg is required\n";
	$default_config .= "ffmpeg = $ffmpeg_path\n\n";
	$default_config .= "# imagemagick (convert binary) is required\n";
	$default_config .= "imagemagick = $imagemagick_path\n\n";
	$default_config .= "# time offset in comparison to main server (in hours)\n";
	$default_config .= "timeoffset = 0\n\n";

	file_put_contents("$_SERVER[PWD]/config.properties", $default_config, LOCK_EX);
}

$config_data = explode("\n", @file_get_contents("$_SERVER[PWD]/config.properties"));
foreach ($config_data as $row)
{
	$row = array_map('trim', explode('=', $row, 2));
	switch ($row[0])
	{
		case 'ffmpeg':
			$ffmpeg_path = $row[1];
			break;
		case 'imagemagick':
			$imagemagick_path = $row[1];
			break;
		case 'timeoffset':
			$time_offset = floatval($row[1]);
			break;
		case 'memory':
			$memory = $row[1];
			break;
		case 'screenshot_options':
			$screenshot_options = $row[1];
			break;
		case 'debug':
			if ($row[1] == 'true')
			{
				$is_debug_enabled = true;
			}
			break;
	}
}

$libraries_errors = '';
$libraries['ffmpeg']['path'] = $ffmpeg_path;
$libraries['imagemagick']['path'] = $imagemagick_path;

if (is_file("$_SERVER[PWD]/../../include/setup.php"))
{
	$kvs_config = file_get_contents("$_SERVER[PWD]/../../include/setup.php");
}

// verify ffmpeg path
if ($ffmpeg_path)
{
	unset($exec_res);
	exec("$ffmpeg_path -version 2>&1", $exec_res);
	if (stripos(implode("\n", $exec_res), 'ffmpeg version ') === false && stripos(implode("\n", $exec_res), 'ffmpeg 0.') === false)
	{
		$has_valid_ffmpeg = false;
		unset($temp);
		if ($kvs_config && preg_match("/[$]config\[['\"\ ]+ffmpeg_path['\"\ ]+\]\ *=\ *['\"]([^\"']+)['\"]/is", $kvs_config, $temp))
		{
			$ffmpeg_path = $temp[1];

			$has_valid_ffmpeg = true;
			unset($exec_res);
			exec("$ffmpeg_path -version 2>&1", $exec_res);
			if (stripos(implode("\n", $exec_res), 'ffmpeg version ') === false && stripos(implode("\n", $exec_res), 'ffmpeg 0.') === false)
			{
				$has_valid_ffmpeg = false;
			} else
			{
				$libraries['ffmpeg']['path'] = $ffmpeg_path;
				$libraries['ffmpeg']['message'] = $exec_res[0];
				if (stripos($libraries['ffmpeg']['message'], 'trailing options') !== false)
				{
					$libraries['ffmpeg']['message'] = $exec_res[1];
				}
			}
		}

		if (!$has_valid_ffmpeg)
		{
			$libraries['ffmpeg']['is_error'] = 1;
			$libraries_errors .= "ffmpeg [$ffmpeg_path], ";
		}
	} else
	{
		$libraries['ffmpeg']['message'] = $exec_res[0];
		if (stripos($libraries['ffmpeg']['message'], 'trailing options') !== false)
		{
			$libraries['ffmpeg']['message'] = $exec_res[1];
		}
	}
} else
{
	$libraries['ffmpeg']['is_error'] = 1;
	$libraries_errors .= "ffmpeg [$ffmpeg_path], ";
}

// verify image magick path
if ($imagemagick_path)
{
	unset($exec_res);
	exec("$imagemagick_path 2>&1", $exec_res);
	if (stripos(implode("\n", $exec_res), 'imagemagick') === false)
	{
		$has_valid_imagemagick = false;
		unset($temp);
		if ($kvs_config && preg_match("/[$]config\[['\"\ ]+image_magick_path['\"\ ]+\]\ *=\ *['\"]([^\"']+)['\"]/is", $kvs_config, $temp))
		{
			$imagemagick_path = $temp[1];

			$has_valid_imagemagick = true;
			unset($exec_res);
			exec("$imagemagick_path -version 2>&1", $exec_res);
			if (stripos(implode("\n", $exec_res), 'imagemagick') === false)
			{
				$has_valid_imagemagick = false;
			} else
			{
				$libraries['imagemagick']['path'] = $imagemagick_path;
				$libraries['imagemagick']['message'] = $exec_res[0];
			}
		}

		if (!$has_valid_imagemagick)
		{
			$libraries['imagemagick']['is_error'] = 1;
			$libraries_errors .= "imagemagick [$imagemagick_path], ";
		}
	} else
	{
		$libraries['imagemagick']['message'] = $exec_res[0];
	}
} else
{
	$libraries['imagemagick']['is_error'] = 1;
	$libraries_errors .= "imagemagick [$imagemagick_path], ";
}

// update heartbeat

$heartbeat = [];
$heartbeat['la'] = get_LA();
$heartbeat['total_space'] = @disk_total_space($_SERVER['PWD']);
$heartbeat['free_space'] = @disk_free_space($_SERVER['PWD']);
$heartbeat['time'] = time() - $time_offset * 3600;
$heartbeat['last_activity'] = intval(@file_get_contents("$_SERVER[PWD]/last_activity.dat"));
$heartbeat['api_version'] = $api_version;
$heartbeat['libraries'] = $libraries;
if (function_exists('ftp_connect'))
{
	$heartbeat['ftp_supported'] = true;
}
if (function_exists('curl_init'))
{
	$heartbeat['curl_supported'] = true;
}

file_put_contents("$_SERVER[PWD]/heartbeat.dat", serialize($heartbeat), LOCK_EX);

if ($libraries_errors)
{
	$libraries_errors = substr($libraries_errors, 0, -2);
	file_put_contents("$_SERVER[PWD]/log.txt", "FATAL  Libraries are not defined: $libraries_errors", LOCK_EX);
	die("FATAL  Libraries are not defined: $libraries_errors");
}

if (!is_file("$_SERVER[PWD]/remote.lock"))
{
	file_put_contents("$_SERVER[PWD]/remote.lock", '1', LOCK_EX);
}

$lock = fopen("$_SERVER[PWD]/remote.lock", "r+");
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

//wait for 10 seconds to allow main engine copy tasks
sleep(10);

file_put_contents("$_SERVER[PWD]/log.txt", '', LOCK_EX);

// start working
log_info("Conversion processor started (v $api_version)");

log_info('');
foreach ($libraries as $k => $v)
{
	log_info("$k = $v[path]");
}
log_info("timeoffset = $time_offset");
log_info('');

ini_set('memory_limit', $memory);
log_info("memory = $memory");
log_info("screenshot_options = $screenshot_options");

$data = scandir($_SERVER['PWD']);
if (!$data)
{
	log_error("Failed to scan directory");
}
sort($data);

foreach ($data as $task_id)
{
	if ($task_id == '.' || $task_id == '..')
	{
		continue;
	}
	$task_dir = "$_SERVER[PWD]/$task_id";
	if (intval($task_id) > 0 && is_dir($task_dir) && filemtime($task_dir) < time() - 5 * 86400)
	{
		if (is_file("$task_dir/result.dat"))
		{
			log_info("Removing $task_id task dir as it is outdated");
			rmdir_recursive($task_dir);
		}
	}
	if (intval($task_id) > 0 && is_dir($task_dir) && @file_get_contents("$task_dir/deleted.dat") == '1')
	{
		log_info("Removing $task_id task dir as it is being marked to be deleted");
		rmdir_recursive($task_dir);
	}
}

foreach ($data as $task_id)
{
	$task_dir = "$_SERVER[PWD]/$task_id";
	if (!is_dir($task_dir) || !is_file("$task_dir/task.dat") || is_file("$task_dir/result.dat"))
	{
		continue;
	}

	if (intval($task_id) > 0 && @file_get_contents("$task_dir/deleted.dat") == '1')
	{
		log_info("Skipping $task_id task as it is being marked to be deleted");
		continue;
	}

	$task_info = unserialize(file_get_contents("$task_dir/task.dat"));
	if (!is_array($task_info))
	{
		continue;
	}

	$source_dir = $task_info['source_dir'] ?? $task_dir;
	if (!is_dir($source_dir))
	{
		log_error("Conversion task $task_id refers to non-existing source directory $source_dir");
		file_put_contents("$task_dir/result.dat", serialize(['is_error' => 1]));
		continue;
	}

	if ($task_info['options']['PROCESS_PRIORITY'] > 0)
	{
		$priority = intval($task_info['options']['PROCESS_PRIORITY']);
		$priority_prefix = "nice -n $priority ";
	}

	$task_iteration = 0;
	if (is_file("$task_dir/iteration.dat"))
	{
		$task_iteration = intval(@file_get_contents("$task_dir/iteration.dat"));
		if ($task_iteration >= 2)
		{
			log_error("Conversion task $task_id failed to be processed for more than 2 times");
			file_put_contents("$task_dir/result.dat", serialize(['is_error' => 1]));
			continue;
		}
	}
	$task_iteration++;
	file_put_contents("$task_dir/iteration.dat", $task_iteration);

	$task_result = [];
	if (intval($task_info['video_id']) > 0)
	{
		$task_progress = 0;
		$total_progress = 0;
		if (is_array($task_info['source_files']) && count($task_info['source_files']) > 1)
		{
			$total_progress++;
		}
		if (is_array($task_info['videos_creation_list']))
		{
			$total_progress += count($task_info['videos_creation_list']);
		}
		if (is_array($task_info['videos_post_process_list']))
		{
			$total_progress += count($task_info['videos_post_process_list']);
		}
		if (is_array($task_info['timelines_creation_list']))
		{
			$total_progress += count($task_info['timelines_creation_list']);
		}
		if ($task_info['make_screens'] == 1)
		{
			$total_progress++;
		}

		$source_file = "$source_dir/$task_info[source_file]";
		$source_files = [];
		if (is_array($task_info['source_files']))
		{
			foreach ($task_info['source_files'] as $source_file)
			{
				$source_files[] = "$source_dir/$source_file";
			}
		}

		$video_id = $task_info['video_id'];
		$dir_path = get_dir_by_id($video_id);
		$task_result['video_files'] = [];
		$task_result['video_files_completed'] = [];

		log_info('');
		log_info("Starting conversion task $task_id for video $video_id [PH-C]");
		$start_time = time();

		if (is_array($task_info['download_urls']))
		{
			log_info("Downloading source files [PH-C-1]");

			if (isset($task_info['source_dir']))
			{
				log_error("Not possible to download URLs when source DIR is provided");
				$task_result['is_error'] = 1;
			} elseif (!function_exists('curl_init'))
			{
				log_error("CURL functions not supported, this conversion server does not support source file download option");
				$task_result['is_error'] = 1;
			} else
			{
				foreach ($task_info['download_urls'] as $file_name => $download_url_rec)
				{
					log_info("Downloading source file from: $download_url_rec[url]");
					save_file_from_url($download_url_rec['url'], "$task_dir/$file_name");

					$downloaded_size = sprintf("%.0f", filesize("$task_dir/$file_name"));
					if ($downloaded_size != $download_url_rec['file_size'])
					{
						log_error("Download failed: only $downloaded_size bytes of $download_url_rec[file_size] was downloaded");
						$task_result['is_error'] = 1;
					} else
					{
						log_info("Downloaded $downloaded_size bytes");
					}
				}
			}
		}

		if (count($source_files) > 0 && $task_result['is_error'] != 1)
		{
			if (count($source_files) == 1)
			{
				$source_file = $source_files[0];
			} else
			{
				log_info("Multiple source files, merging them into single file [PH-C-2]");

				$ffprobe_path = str_replace("ffmpeg", "ffprobe", $ffmpeg_path);
				if (strpos($ffprobe_path, ' -') > 0)
				{
					$ffprobe_path = substr($ffprobe_path, 0, strpos($ffprobe_path, ' -'));
				}
				$exec_str = "{$priority_prefix}$ffprobe_path $source_files[0] -show_format 2>&1";

				unset($res);
				exec($exec_str, $res);

				$source_format_type = '';
				foreach ($res as $output_row)
				{
					if (strpos($output_row, 'format_name=') === 0)
					{
						$source_format_type = substr($output_row, 12);
						if (strpos($source_format_type, ',') !== false)
						{
							$source_format_type = substr($source_format_type, 0, strpos($source_format_type, ','));
						}
						break;
					}
				}
				if ($source_format_type == '')
				{
					log_error("Failed to detect source format type", $exec_str, $res, true);
					$task_result['is_error'] = 1;
				}

				$file_names_str = '';
				foreach ($source_files as $temp_file)
				{
					$file_names_str .= "file '$temp_file'\n";
				}

				file_put_contents("$task_dir/{$video_id}_merged.txt", trim($file_names_str));

				$temp_file = "$task_dir/merged.mp4";
				$exec_str = "{$priority_prefix}$ffmpeg_path -f concat -safe 0 -i $task_dir/{$video_id}_merged.txt -c copy -f $source_format_type $temp_file 2>&1";
				log_command($exec_str);

				unset($res);
				exec($exec_str, $res);
				log_console($res);

				if (!is_file($temp_file))
				{
					log_error("Failed to merge source files", $exec_str, $res);
					$task_result['is_error'] = 1;
				} else
				{
					$source_dimensions = get_video_dimensions($temp_file);
					$source_duration = get_video_duration($temp_file);
					$source_size = sprintf("%.0f", filesize($temp_file));
					if ($source_duration > 0)
					{
						log_info("Created video file merged.mp4: $source_dimensions[0]x$source_dimensions[1], $source_duration seconds, $source_size bytes");
					}

					$source_file = $temp_file;
				}

				$task_progress++;
				file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
			}
		}

		$source_dimensions = $task_info['source_dimensions'];
		$source_duration = get_video_duration($source_file);
		$source_ext = strtolower(end(explode(".", $source_file)));
		if ($source_ext == 'tmp' && $task_result['is_error'] != 1)
		{
			log_info("Pre-processing source file [PH-C-3]");
			if (is_array($task_info['videos_creation_list']))
			{
				unset($res);
				$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file 2>&1";
				exec($exec_str, $res);
				if (preg_match("|SAR (\d+:\d+) |is", implode("\r\n", $res), $match))
				{
					$sar = explode(':', $match[1]);
					if ($sar[0] > 1 && $sar[1] > 1 && abs(1 - $sar[0] / $sar[1]) > 0.1)
					{
						log_info("Source video SAR is not square ($match[1]), converting it to square pixel format [PH-C-3-2]");

						$temp_file = "$task_dir/square.mp4";
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file -c:v libx264 -preset ultrafast -crf 18 -c:a aac -strict -2 -vf \"format=yuv420p,scale=trunc(iw*sar/2)*2:ih\" $temp_file 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);

						if (!is_file($temp_file))
						{
							log_error("Failed to create square SAR source file", $exec_str, $res);
							$task_result['is_error'] = 1;
						} else
						{
							$source_dimensions = get_video_dimensions($temp_file);
							$source_duration = get_video_duration($temp_file);
							$source_size = sprintf("%.0f", filesize($temp_file));
							if ($source_duration > 0)
							{
								log_info("Created video file square.mp4: $source_dimensions[0]x$source_dimensions[1], $source_duration seconds, $source_size bytes");
								rename($temp_file, "$task_dir/$video_id.tmp");
								$source_file = "$task_dir/$video_id.tmp";
							} else
							{
								log_error("Created video file square.mp4: $source_dimensions[0]x$source_dimensions[1], $source_duration seconds, $source_size bytes", $exec_str, $res);
								$task_result['is_error'] = 1;
							}
						}
					}
				}
			}

			if ($task_result['is_error'] != 1)
			{
				if (isset($task_info['source_filter']))
				{
					log_info("Pre-processing source video file [PH-C-3-3]");

					$source_filter_crop_top = intval($task_info['source_filter']['crop_top']);
					$source_filter_crop_bottom = intval($task_info['source_filter']['crop_bottom']);
					$source_filter_offset_start = intval($task_info['source_filter']['offset_start']);
					$source_filter_offset_end = intval($task_info['source_filter']['offset_end']);
					$source_filter_rotate_degree = floatval($task_info['source_filter']['rotate_degree']);
					$source_filter_flip = intval($task_info['source_filter']['flip']);

					$source_filter_graph = '';
					if ($source_filter_flip == 1)
					{
						$source_filter_graph .= 'hflip,';
					}
					if ($source_filter_rotate_degree > 0)
					{
						$source_filter_rotate_degree_sign = '';
						if (mt_rand(0, 100) > 50)
						{
							$source_filter_rotate_degree_sign = '-';
						}
						$source_filter_rotate_degree_crop_h = ceil(tan($source_filter_rotate_degree * M_PI / 180) * $source_dimensions[1]);
						$source_filter_rotate_degree_crop_v = ceil(tan($source_filter_rotate_degree * M_PI / 180) * $source_dimensions[0]);
						if ($source_filter_rotate_degree_crop_h / 2 != round($source_filter_rotate_degree_crop_h / 2))
						{
							$source_filter_rotate_degree_crop_h++;
						}
						if ($source_filter_rotate_degree_crop_v / 2 != round($source_filter_rotate_degree_crop_v / 2))
						{
							$source_filter_rotate_degree_crop_v++;
						}
						$source_filter_graph .= "rotate={$source_filter_rotate_degree_sign}$source_filter_rotate_degree*PI/180,crop=in_w-$source_filter_rotate_degree_crop_h:in_h-$source_filter_rotate_degree_crop_v,";
					}
					if ($source_filter_crop_top > 0 || $source_filter_crop_bottom > 0)
					{
						if ($source_filter_crop_top + $source_filter_crop_bottom >= $source_dimensions[1] * 0.5)
						{
							log_warning("Source video cannot be cropped to $source_filter_crop_top from top and $source_filter_crop_bottom from bottom");
						} else
						{
							$source_filter_crop_top_v = $source_filter_crop_top + $source_filter_crop_bottom;
							$source_filter_graph .= "crop=in_w:in_h-$source_filter_crop_top_v:0:$source_filter_crop_top,";
						}
					}
					$source_filter_graph = trim($source_filter_graph, ', ');
					if ($source_filter_graph)
					{
						$source_filter_graph = "-vf \"$source_filter_graph\"";
					}

					$source_filter_offsets = '';
					if ($source_filter_offset_start > 0 || $source_filter_offset_end > 0)
					{
						if ($source_filter_offset_start + $source_filter_offset_end >= $source_duration - 1)
						{
							log_warning("Source video cannot be limited to {$source_filter_offset_start}s from start and {$source_filter_offset_end}s from end");
						} else
						{
							if ($source_filter_offset_start > 0)
							{
								$source_filter_offsets = "-ss $source_filter_offset_start -t " . ($source_duration - $source_filter_offset_start - $source_filter_offset_end);
							} else
							{
								$source_filter_offsets = "-t " . ($source_duration - $source_filter_offset_start - $source_filter_offset_end);
							}
						}
					}

					if ($source_filter_graph || $source_filter_offsets)
					{
						$temp_file = "$task_dir/preprocessed.mp4";
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file -c:v libx264 -preset ultrafast -crf 18 -c:a aac -strict -2 $source_filter_graph $source_filter_offsets $temp_file 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);
						if (!is_file($temp_file))
						{
							log_error("Failed to execute source file pre-processing", $exec_str, $res);
							$task_result['is_error'] = 1;
						} else
						{
							$source_dimensions = get_video_dimensions($temp_file);
							$source_duration = get_video_duration($temp_file);
							$source_size = sprintf("%.0f", filesize($temp_file));
							if ($source_duration > 0)
							{
								log_info("Created video file preprocessed.mp4: $source_dimensions[0]x$source_dimensions[1], $source_duration seconds, $source_size bytes");
								$source_file = $temp_file;
							} else
							{
								log_error("Created video file preprocessed.mp4: $source_dimensions[0]x$source_dimensions[1], $source_duration seconds, $source_size bytes", $exec_str, $res);
								$task_result['is_error'] = 1;
							}
						}
					}
				}
			}
		}

		$merged_hashes = [];
		if (is_array($task_info['videos_creation_list']) && $task_result['is_error'] != 1)
		{
			foreach ($task_info['videos_creation_list'] as $video_format)
			{
				log_info("Creating video format \"$video_format[title]\" [PH-C-5:$video_format[title]]");

				$resize_vf_string = '';
				$output_video_size = $source_dimensions;
				if ($video_format['resize_option'] == 1)
				{
					$required_size = explode("x", $video_format['size']);
					$source_size = $source_dimensions;

					if ($video_format['resize_option2'] == 2)
					{
						$size_to_convert = [ceil($required_size[1] * $source_size[0] / $source_size[1]), $required_size[1]];
						if ($size_to_convert[1] < 1)
						{
							$size_to_convert[1] = $required_size[1];
						}
					} else
					{
						$size_to_convert = [$required_size[0], ceil($required_size[0] * $source_size[1] / $source_size[0])];
						if ($size_to_convert[1] < 1)
						{
							$size_to_convert[1] = $required_size[1];
						}
					}

					if ($size_to_convert[0] / 2 != round($size_to_convert[0] / 2))
					{
						$size_to_convert[0]++;
					}
					if ($size_to_convert[1] / 2 != round($size_to_convert[1] / 2))
					{
						$size_to_convert[1]++;
					}

					$skip_resize = 0;
					if ($size_to_convert[0] > $source_size[0])
					{
						if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
						{
							log_info("Video file cannot be resized to $size_to_convert[0]x$size_to_convert[1], the format creation will be skipped");
							continue;
						} else
						{
							log_warning("Video file cannot be resized to $size_to_convert[0]x$size_to_convert[1]");
							$skip_resize = 1;

							if ($source_size[0] / 2 != round($source_size[0] / 2) || $source_size[1] / 2 != round($source_size[1] / 2))
							{
								$size_to_convert = [$source_size[0], $source_size[1]];
								if ($size_to_convert[0] / 2 != round($size_to_convert[0] / 2))
								{
									$size_to_convert[0]++;
								}
								if ($size_to_convert[1] / 2 != round($size_to_convert[1] / 2))
								{
									$size_to_convert[1]++;
								}

								log_warning("Video file will be forced to $size_to_convert[0]x$size_to_convert[1], as source size is not divisible by 2 ($source_size[0]x$source_size[1])");
								$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1]";
							}
						}
					}

					if ($skip_resize == 0)
					{
						if ($video_format['resize_option2'] == 1)
						{
							if ($size_to_convert[1] > $required_size[1])
							{
								$crop = intval(($size_to_convert[1] - $required_size[1]) / 2);
								$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1],crop=$required_size[0]:$required_size[1]";
								log_info("Resizing video file to $required_size[0]x$required_size[1], cropping $crop pixels from top and bottom");
								$output_video_size = $required_size;
							} elseif ($size_to_convert[1] < $required_size[1])
							{
								$size_to_convert = [ceil($source_size[0] * $required_size[1] / $source_size[1]), $required_size[1]];
								if ($size_to_convert[0] < 1)
								{
									$size_to_convert[0] = $required_size[0];
								}
								if ($size_to_convert[0] / 2 != round($size_to_convert[0] / 2))
								{
									$size_to_convert[0]++;
								}
								if ($size_to_convert[1] / 2 != round($size_to_convert[1] / 2))
								{
									$size_to_convert[1]++;
								}

								if ($size_to_convert[0] > $source_size[0])
								{
									if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
									{
										log_info("Video file cannot be resized to $size_to_convert[0]x$size_to_convert[1], the format creation will be skipped");
										continue;
									} else
									{
										log_warning("Video file cannot be resized to $size_to_convert[0]x$size_to_convert[1]");
										$skip_resize = 1;

										if ($source_size[0] / 2 != round($source_size[0] / 2) || $source_size[1] / 2 != round($source_size[1] / 2))
										{
											$size_to_convert = [$source_size[0], $source_size[1]];
											if ($size_to_convert[0] / 2 != round($size_to_convert[0] / 2))
											{
												$size_to_convert[0]++;
											}
											if ($size_to_convert[1] / 2 != round($size_to_convert[1] / 2))
											{
												$size_to_convert[1]++;
											}

											log_warning("Video file will be forced to $size_to_convert[0]x$size_to_convert[1], as source size is not divisible by 2 ($source_size[0]x$source_size[1])");
											$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1]";
										}
									}
								}

								if ($skip_resize == 0)
								{
									$crop = intval(($size_to_convert[0] - $required_size[0]) / 2);
									$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1],crop=$required_size[0]:$required_size[1]";
									log_info("Resizing video file to $required_size[0]x$required_size[1], cropping $crop pixels from left and right");
									$output_video_size = $required_size;
								}
							} else
							{
								$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1]";
								log_info("Resizing video file to $size_to_convert[0]x$size_to_convert[1]");
								$output_video_size = $size_to_convert;
							}
						} else
						{
							if ($size_to_convert[0] < $source_size[0] && $size_to_convert[1] < $source_size[1])
							{
								$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1]";
								log_info("Resizing video file to $size_to_convert[0]x$size_to_convert[1]");
								$output_video_size = $size_to_convert;
							}
						}
					}
				} else
				{
					$size_to_convert = [$source_dimensions[0], $source_dimensions[1]];
					if ($size_to_convert[0] / 2 != round($size_to_convert[0] / 2))
					{
						$size_to_convert[0]++;
					}
					if ($size_to_convert[1] / 2 != round($size_to_convert[1] / 2))
					{
						$size_to_convert[1]++;
					}
					if ($source_dimensions[0] != $size_to_convert[0] || $source_dimensions[1] != $size_to_convert[1])
					{
						$resize_vf_string = "scale=$size_to_convert[0]:$size_to_convert[1]";
					}
					$output_video_size = $size_to_convert;
				}

				unset($temp);
				preg_match('|-vf\ *"([^"]+)"|i', $video_format['ffmpeg_options'], $temp);
				if ($temp[1])
				{
					log_info("Considering custom filters: $temp[1]");
					if ($resize_vf_string)
					{
						$resize_vf_string = "$resize_vf_string, $temp[1]";
					} else
					{
						$resize_vf_string = $temp[1];
					}
					$video_format['ffmpeg_options'] = str_replace($temp[0], '', $video_format['ffmpeg_options']);
				}


				$duration_string = '';
				$file_duration = 0;
				$file_offset_start = 0;
				$file_offset_end = 0;
				if ($video_format['limit_total_duration'] > 0 || $video_format['limit_offset_start'] > 0 || $video_format['limit_offset_end'] > 0)
				{
					if ($video_format['limit_offset_start'] > 0)
					{
						if ($video_format['limit_offset_start_unit_id'] == 1)
						{
							if ($video_format['limit_offset_start'] >= 100)
							{
								$file_offset_start = $source_duration;
							} else
							{
								$file_offset_start = ceil($source_duration * $video_format['limit_offset_start'] / 100);
							}
						} else
						{
							$file_offset_start = $video_format['limit_offset_start'];
						}
					}
					if ($video_format['limit_offset_end'] > 0)
					{
						if ($video_format['limit_offset_end_unit_id'] == 1)
						{
							if ($video_format['limit_offset_end'] >= 100)
							{
								$file_offset_end = $source_duration;
							} else
							{
								$file_offset_end = ceil($source_duration * $video_format['limit_offset_end'] / 100);
							}
						} else
						{
							$file_offset_end = $video_format['limit_offset_end'];
						}
					}
					if ($video_format['limit_total_duration'] > 0)
					{
						if ($video_format['limit_total_duration_unit_id'] == 1)
						{
							if ($video_format['limit_total_duration'] >= 100)
							{
								$file_duration = $source_duration;
							} else
							{
								$file_duration = ceil($source_duration * $video_format['limit_total_duration'] / 100);
							}
							if ($video_format['limit_total_min_duration_sec'] > 0 && $file_duration < $video_format['limit_total_min_duration_sec'])
							{
								$file_duration = $video_format['limit_total_min_duration_sec'];
								if ($video_format['limit_total_min_duration_sec'] > $source_duration - $file_offset_start - $file_offset_end)
								{
									if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
									{
										log_info("Video format doesn't support source file with duration $source_duration seconds due to duration limits, the format creation will be skipped");
										continue;
									} else
									{
										log_error("Video format doesn't support source file with duration $source_duration seconds due to duration limits");
										$task_result['is_error'] = 1;
										break;
									}
								}
							}
							if ($video_format['limit_total_max_duration_sec'] > 0 && $file_duration > $video_format['limit_total_max_duration_sec'])
							{
								$file_duration = $video_format['limit_total_max_duration_sec'];
							}
						} else
						{
							$file_duration = $video_format['limit_total_duration'];
						}
					}
					if ($file_offset_start + $file_offset_end > $source_duration)
					{
						if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
						{
							log_info("Video format doesn't support source file with duration $source_duration seconds due to duration limits, the format creation will be skipped");
							continue;
						} else
						{
							log_info("Video format doesn't support source file with duration $source_duration seconds due to duration limits");
							$file_offset_start = 0;
							$file_offset_end = 0;
						}
					}
					if ($file_duration == 0)
					{
						$file_duration = $source_duration - $file_offset_start - $file_offset_end;
					} elseif ($file_duration > $source_duration - $file_offset_start - $file_offset_end)
					{
						$file_offset_start = 0;
						$file_offset_end = 0;
						if ($file_duration > $source_duration)
						{
							$file_duration = $source_duration;
						}
					}
					if ($file_offset_start > 0)
					{
						$duration_string = "-ss $file_offset_start -t $file_duration";
					} else
					{
						$duration_string = "-t $file_duration";
					}
					log_info("Video duration is limited to $file_duration (skip $file_offset_start from the beginning, skip $file_offset_end from the end)");
				} else
				{
					$file_duration = $source_duration;
				}

				$watermark_string = '';
				$watermark_image = "$task_dir/watermark_video_{$video_format['format_video_id']}.png";
				if (is_file($watermark_image))
				{
					$watermark_position = $video_format['watermark_position_id'];
					$watermark_x = 0;
					$watermark_y = 0;
					$watermark_size = getimagesize($watermark_image);
					$watermark_required_pc = intval($video_format['watermark_max_width']);
					if ($source_size[1] > $source_size[0])
					{
						$watermark_required_pc = intval($video_format['watermark_max_width_vertical']);
					}
					if ($watermark_required_pc > 0)
					{
						$watermark_actual_pc = floor($watermark_size[0] / $output_video_size[0] * 100);
						if ($watermark_actual_pc > $watermark_required_pc)
						{
							$watermark_new_width = floor($output_video_size[0] * $watermark_required_pc / 100);
							$watermark_new_height = floor($watermark_size[1] * ($watermark_new_width / $watermark_size[0]));
							$watermark_new_image = "$task_dir/watermark_video_{$video_format['format_video_id']}_resized.png";
							log_info("Scaling watermark image from $watermark_actual_pc% to $watermark_required_pc% ({$watermark_new_width}x{$watermark_new_height})");

							$exec_str = "$imagemagick_path $watermark_image -resize {$watermark_new_width}x{$watermark_new_height} $watermark_new_image 2>&1";

							unset($res);
							exec($exec_str, $res);
							if (!is_file($watermark_new_image) || filesize($watermark_new_image) == 0)
							{
								if (is_array($res))
								{
									$res = $res[0];
								}
								log_error("Failed to resize watermark image", $exec_str, $res, true);
								$task_result['is_error'] = 1;
								break;
							}
							$watermark_image = $watermark_new_image;
							$watermark_size = getimagesize($watermark_new_image);
						}
					}

					if ($watermark_position == 0)
					{
						$watermark_position = mt_rand(1, 4);
					}
					if ($watermark_position == 1)
					{
						$watermark_x = 0;
						$watermark_y = 0;
					} elseif ($watermark_position == 2)
					{
						$watermark_x = $output_video_size[0] - $watermark_size[0];
						$watermark_y = 0;
					} elseif ($watermark_position == 3)
					{
						$watermark_x = $output_video_size[0] - $watermark_size[0];
						$watermark_y = $output_video_size[1] - $watermark_size[1];
					} elseif ($watermark_position == 4)
					{
						$watermark_x = 0;
						$watermark_y = $output_video_size[1] - $watermark_size[1];
					} elseif ($watermark_position == 5 || $watermark_position == 6 || $watermark_position == 7)
					{
						$watermark_scrolling_duration = intval($video_format['watermark_scrolling_duration']);
						if ($watermark_scrolling_duration == 0)
						{
							log_error("Watermark scrolling duration is not specified");
							$task_result['is_error'] = 1;
							break;
						}

						$watermark_scrolling_times = array_map('trim', explode(',', $video_format['watermark_scrolling_times']));
						foreach ($watermark_scrolling_times as $k => $v)
						{
							if (strpos($v, '%') !== false)
							{
								$v = intval($v);
								if ($v > 100)
								{
									$v = 100;
								}
								$v = ceil($file_duration * $v / 100);
							} else
							{
								$v = intval($v);
								if ($v > $file_duration)
								{
									unset($watermark_scrolling_times[$k]);
									continue;
								}
							}
							if ($v < 0)
							{
								$v = 0;
							}
							if ($v > $file_duration - $watermark_scrolling_duration)
							{
								$v = $file_duration - $watermark_scrolling_duration;
							}
							$watermark_scrolling_times[$k] = $v;
						}
						sort($watermark_scrolling_times, SORT_ASC);
						$watermark_scrolling_times = array_unique($watermark_scrolling_times);
						log_info("Scrolling watermark positions: " . implode(', ', $watermark_scrolling_times));

						$watermark_scrolling_end_time = 0;
						$watermark_x = '-w-10000';
						$watermark_y_random = '0';
						if (mt_rand(1, 100000) % 2 == 0)
						{
							$watermark_y_random = 'H-h';
						}
						foreach ($watermark_scrolling_times as $watermark_scrolling_start_time)
						{
							$watermark_scrolling_start_time += $file_offset_start;
							if ($watermark_scrolling_start_time - $watermark_scrolling_end_time >= 0)
							{
								$watermark_scrolling_end_time = $watermark_scrolling_start_time + $watermark_scrolling_duration;
								if (intval($video_format['watermark_scrolling_direction']) == 1)
								{
									$watermark_x = "if(gte(t,$watermark_scrolling_start_time)*lte(t,$watermark_scrolling_end_time), W-((W+w)/$watermark_scrolling_duration)*mod(t-$watermark_scrolling_start_time,$watermark_scrolling_duration), $watermark_x)";
								} else
								{
									$watermark_x = "if(gte(t,$watermark_scrolling_start_time)*lte(t,$watermark_scrolling_end_time), ((W+w)/$watermark_scrolling_duration)*mod(t-$watermark_scrolling_start_time,$watermark_scrolling_duration)-w, $watermark_x)";
								}
								if (mt_rand(1, 100000) % 2 == 1)
								{
									$watermark_y_random = "if(gte(t,$watermark_scrolling_start_time)*lte(t,$watermark_scrolling_end_time), 0, $watermark_y_random)";
								} else
								{
									$watermark_y_random = "if(gte(t,$watermark_scrolling_start_time)*lte(t,$watermark_scrolling_end_time), H-h, $watermark_y_random)";
								}
							}
						}

						$watermark_y = '0';
						if ($watermark_position == 6)
						{
							$watermark_y = 'H-h';
						} elseif ($watermark_position == 7)
						{
							$watermark_y = $watermark_y_random;
						}

						$watermark_x = "'$watermark_x'";
						$watermark_y = "'$watermark_y'";
					} else
					{
						$watermark_x = 0;
						$watermark_y = 0;
					}
					$watermark_string = "movie=$watermark_image [wm];[in][wm] overlay=$watermark_x:$watermark_y";
					if ($resize_vf_string)
					{
						$resize_vf_string .= " [sc]";
						$watermark_string = "; movie=$watermark_image [wm];[sc][wm] overlay=$watermark_x:$watermark_y";
					}
				}

				$watermark2_image = "$task_dir/watermark2_video_{$video_format['format_video_id']}.png";
				if (is_file($watermark2_image))
				{
					$watermark2_position = $video_format['watermark2_position_id'];
					$watermark2_x = 0;
					$watermark2_y = 0;
					$watermark2_size = getimagesize($watermark2_image);
					$watermark2_required_pc = intval($video_format['watermark2_max_height']);
					if ($source_size[1] > $source_size[0])
					{
						$watermark2_required_pc = intval($video_format['watermark2_max_height_vertical']);
					}
					if ($watermark2_required_pc > 0)
					{
						$watermark2_actual_pc = floor($watermark2_size[1] / $output_video_size[1] * 100);
						if ($watermark2_actual_pc > $watermark2_required_pc)
						{
							$watermark2_new_height = floor($output_video_size[1] * $watermark2_required_pc / 100);
							$watermark2_new_width = floor($watermark2_size[0] * ($watermark2_new_height / $watermark2_size[1]));
							$watermark2_new_image = "$task_dir/watermark2_video_{$video_format['format_video_id']}_resized.png";
							log_info("Scaling watermark2 image from $watermark2_actual_pc% to $watermark2_required_pc% ({$watermark2_new_width}x{$watermark2_new_height})");

							$exec_str = "$imagemagick_path $watermark2_image -resize {$watermark2_new_width}x{$watermark2_new_height} $watermark2_new_image 2>&1";

							unset($res);
							exec($exec_str, $res);
							if (!is_file($watermark2_new_image) || filesize($watermark2_new_image) == 0)
							{
								if (is_array($res))
								{
									$res = $res[0];
								}
								log_error("Failed to resize watermark image", $exec_str, $res, true);
								$task_result['is_error'] = 1;
								break;
							}
							$watermark2_image = $watermark2_new_image;
							$watermark2_size = getimagesize($watermark2_new_image);
						}
					}

					if ($watermark2_position == 0)
					{
						$watermark2_position = mt_rand(1, 4);
					}
					if ($watermark2_position == 1)
					{
						$watermark2_x = 0;
						$watermark2_y = 0;
					} elseif ($watermark2_position == 2)
					{
						$watermark2_x = $output_video_size[0] - $watermark2_size[0];
						$watermark2_y = 0;
					} elseif ($watermark2_position == 3)
					{
						$watermark2_x = $output_video_size[0] - $watermark2_size[0];
						$watermark2_y = $output_video_size[1] - $watermark2_size[1];
					} elseif ($watermark2_position == 4)
					{
						$watermark2_x = 0;
						$watermark2_y = $output_video_size[1] - $watermark2_size[1];
					} elseif ($watermark2_position == 5 || $watermark2_position == 6 || $watermark2_position == 7)
					{
						$watermark2_scrolling_duration = intval($video_format['watermark2_scrolling_duration']);
						if ($watermark2_scrolling_duration == 0)
						{
							log_error("Watermark scrolling duration is not specified");
							$task_result['is_error'] = 1;
							break;
						}

						$watermark2_scrolling_times = array_map('trim', explode(',', $video_format['watermark2_scrolling_times']));
						foreach ($watermark2_scrolling_times as $k => $v)
						{
							if (strpos($v, '%') !== false)
							{
								$v = intval($v);
								if ($v > 100)
								{
									$v = 100;
								}
								$v = ceil($file_duration * $v / 100);
							} else
							{
								$v = intval($v);
								if ($v > $file_duration)
								{
									unset($watermark2_scrolling_times[$k]);
									continue;
								}
							}
							if ($v < 0)
							{
								$v = 0;
							}
							if ($v > $file_duration - $watermark2_scrolling_duration)
							{
								$v = $file_duration - $watermark2_scrolling_duration;
							}
							$watermark2_scrolling_times[$k] = $v;
						}
						sort($watermark2_scrolling_times, SORT_ASC);
						$watermark2_scrolling_times = array_unique($watermark2_scrolling_times);
						log_info("Scrolling watermark2 positions: " . implode(', ', $watermark2_scrolling_times));

						$watermark2_scrolling_end_time = 0;
						$watermark2_x = '-w-10000';
						$watermark2_y_random = '0';
						if (mt_rand(1, 100000) % 2 == 0)
						{
							$watermark2_y_random = 'H-h';
						}
						foreach ($watermark2_scrolling_times as $watermark2_scrolling_start_time)
						{
							$watermark2_scrolling_start_time += $file_offset_start;
							if ($watermark2_scrolling_start_time - $watermark2_scrolling_end_time >= 0)
							{
								$watermark2_scrolling_end_time = $watermark2_scrolling_start_time + $watermark2_scrolling_duration;
								if (intval($video_format['watermark2_scrolling_direction']) == 1)
								{
									$watermark2_x = "if(gte(t,$watermark2_scrolling_start_time)*lte(t,$watermark2_scrolling_end_time), W-((W+w)/$watermark2_scrolling_duration)*mod(t-$watermark2_scrolling_start_time,$watermark2_scrolling_duration), $watermark2_x)";
								} else
								{
									$watermark2_x = "if(gte(t,$watermark2_scrolling_start_time)*lte(t,$watermark2_scrolling_end_time), ((W+w)/$watermark2_scrolling_duration)*mod(t-$watermark2_scrolling_start_time,$watermark2_scrolling_duration)-w, $watermark2_x)";
								}
								if (mt_rand(1, 100000) % 2 == 1)
								{
									$watermark2_y_random = "if(gte(t,$watermark2_scrolling_start_time)*lte(t,$watermark2_scrolling_end_time), 0, $watermark2_y_random)";
								} else
								{
									$watermark2_y_random = "if(gte(t,$watermark2_scrolling_start_time)*lte(t,$watermark2_scrolling_end_time), H-h, $watermark2_y_random)";
								}
							}
						}

						$watermark2_y = '0';
						if ($watermark2_position == 6)
						{
							$watermark2_y = 'H-h';
						} elseif ($watermark2_position == 7)
						{
							$watermark2_y = $watermark2_y_random;
						}

						$watermark2_x = "'$watermark2_x'";
						$watermark2_y = "'$watermark2_y'";
					} else
					{
						$watermark2_x = 0;
						$watermark2_y = 0;
					}
					$watermark2_string = "movie=$watermark2_image [wm];[in][wm] overlay=$watermark2_x:$watermark2_y";
					if ($watermark_string)
					{
						$watermark_string .= " [wmsc]; movie=$watermark2_image [wm2];[wmsc][wm2] overlay=$watermark2_x:$watermark2_y";
					} elseif ($resize_vf_string)
					{
						$resize_vf_string .= " [sc]";
						$watermark_string = "; movie=$watermark2_image [wm];[sc][wm] overlay=$watermark2_x:$watermark2_y";
					} else
					{
						$watermark_string = $watermark2_string;
					}
				}

				$filters_string = '';
				$filters_string_components = '';
				if ($resize_vf_string || $watermark_string)
				{
					$filters_string_components = "$resize_vf_string{$watermark_string}";
					$filters_string = "-vf \"$filters_string_components\"";
				}

				$parts = $video_format['limit_number_parts'];
				if ($parts > 1 && $file_duration == $source_duration - $file_offset_start - $file_offset_end)
				{
					log_warning("Can't create multiple parts for such a small duration, only 1 part will be created");
					$parts = 1;
				}
				$had_second_path = false;
				if ($parts == 1)
				{
					log_info("Creating video file [PH-C-5-2]");
					if (stripos($video_format['postfix'], ".gif") !== false)
					{
						$filters_string = "-vf \"palettegen\"";
						if ($filters_string_components)
						{
							$filters_string = "-vf \"$filters_string_components, palettegen\"";
						}
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file $duration_string $filters_string $task_dir/$video_id{$video_format['postfix']}_palette.png 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);

						if (filesize("$task_dir/$video_id{$video_format['postfix']}_palette.png") == 0)
						{
							log_error("Failed to create GIF palette file", $exec_str, $res);
							$task_result['is_error'] = 1;
							break;
						}

						$filters_string = "-filter_complex \"[0:v][1:v] paletteuse\"";
						if ($filters_string_components)
						{
							$filters_string = "-filter_complex \"" . str_replace('[in]', '[0:v]', $filters_string_components) . " [out]; [out][1:v] paletteuse\"";
						}
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file -i $task_dir/$video_id{$video_format['postfix']}_palette.png $duration_string $video_format[ffmpeg_options] $filters_string $task_dir/$video_id$video_format[postfix] 2>&1";
					} else
					{
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file $duration_string $video_format[ffmpeg_options] $filters_string $task_dir/$video_id$video_format[postfix] 2>&1";
					}
					log_command($exec_str);

					unset($res);
					exec($exec_str, $res);
					log_console($res);

					$converted_filesize = sprintf("%.0f", filesize("$task_dir/$video_id$video_format[postfix]"));
					$converted_duration = get_video_duration("$task_dir/$video_id$video_format[postfix]");

					$has_divisable_error = false;
					if (($converted_filesize == 0 || $converted_duration == 0) && $resize_vf_string == '')
					{
						foreach ($res as $line)
						{
							if (strpos($line, "width not divisible by 2") !== false || strpos($line, "height not divisible by 2") !== false)
							{
								$has_divisable_error = true;
								break;
							}
						}
					}
					if ($has_divisable_error)
					{
						log_warning("Source file has size non divisable by 2, forcing resize");
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file -s $output_video_size[0]x$output_video_size[1] $duration_string $video_format[ffmpeg_options] $filters_string $task_dir/$video_id$video_format[postfix] 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);
						clearstatcache();
					}

					foreach ($res as $line)
					{
						if (strpos($line, "moving header") !== false || strpos($line, "moving the moov atom") !== false)
						{
							$had_second_path = true;
							break;
						}
					}
				} else
				{
					if ($file_duration == 0 || $file_duration == $source_duration - $file_offset_start - $file_offset_end)
					{
						if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
						{
							log_info("Video format doesn't support source file with duration $source_duration seconds due to duration limits, the format creation will be skipped");
							continue;
						} else
						{
							log_error("Video format doesn't support multi-parts without limiting duration");
							$task_result['is_error'] = 1;
							break;
						}
					}
					$part_length = floor($file_duration / $parts);
					$block_length = floor(($source_duration - $file_offset_start - $file_offset_end) / $parts);
					if ($part_length < 1)
					{
						log_error("Video format doesn't support part duration less than 1 seconds");
						$task_result['is_error'] = 1;
						break;
					}
					$crossfade = intval($video_format['limit_number_parts_crossfade']);
					if ($crossfade > 0 && ($part_length == 1 || $crossfade > $part_length / 2))
					{
						log_error("Video format doesn't support part duration less than crossfade");
						$task_result['is_error'] = 1;
						break;
					}

					$merged_hash = md5("$parts|$crossfade|$file_duration|$file_offset_start|$file_offset_end|$video_format[limit_is_last_part_from_end]");
					if (!is_file("$task_dir/merged_$merged_hash.mp4"))
					{
						log_info("Creating $parts parts with part duration of $part_length seconds [PH-C-5-1]");

						$files_target_str = '';
						$files_crossfade_str = '';
						$filter_complex_inputs_str = '';
						for ($i = 0; $i < $parts; $i++)
						{
							$j = $i - 1;
							$part_length_crossfade = $part_length;
							switch ($crossfade)
							{
								case 1:
									if ($i < $parts - 1)
									{
										$part_length_crossfade++;
									}
									break;
								case 2:
									$part_length_crossfade++;
									if ($i > 0 && $i < $parts - 1)
									{
										$part_length_crossfade++;
									}
									break;
							}
							$ss = $file_offset_start + $i * $block_length;
							if ($i == $parts - 1 && $video_format['limit_is_last_part_from_end'] == 1)
							{
								$ss = $source_duration - $file_offset_end - $part_length_crossfade;
							}
							$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_file -ss $ss -t $part_length_crossfade -c:v libx264 -preset ultrafast -crf 18 -strict -2 $task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4 2>&1";
							log_command($exec_str);

							unset($res);
							exec($exec_str, $res);
							log_console($res);

							if (!is_file("$task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4"))
							{
								log_error("Failed to create video fragment file", $exec_str, $res);
								$task_result['is_error'] = 1;
								break 2;
							} else
							{
								$converted_filesize = sprintf("%.0f", filesize("$task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4"));
								$converted_duration = get_video_duration("$task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4");
								if ($converted_filesize > 0 && $converted_duration > 0)
								{
									log_info("Created part$i file: $converted_duration seconds, $converted_filesize bytes");
								} else
								{
									log_error("Created part$i file: $converted_duration seconds, $converted_filesize bytes", $exec_str, $res);
									$task_result['is_error'] = 1;
									break 2;
								}
							}
							$files_target_str .= "file '$task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4'\n";
							$files_crossfade_str .= "-i $task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4 ";

							if ($crossfade > 0)
							{
								$crossfade_fragment = $part_length_crossfade - $crossfade;
								if ($i > 0)
								{
									$filter_complex_inputs_str .= "[$i:v]trim=start=0:end=$crossfade,setpts=PTS-STARTPTS[fadeinsrc$i];[fadeinsrc$i]format=pix_fmts=yuva420p,fade=t=in:st=0:d=$crossfade:alpha=1[fadein$i];[fadein$i]fifo[fadeinfifo$i];";
									$filter_complex_inputs_str .= "[fadeoutfifo$j][fadeinfifo$i]overlay[crossfade{$j}_{$i}];";
								}
								if ($i == 0)
								{
									$filter_complex_inputs_str .= "[$i:v]trim=start=0:end=$crossfade_fragment,setpts=PTS-STARTPTS[clip$i];";
								} elseif ($i == $parts - 1)
								{
									$filter_complex_inputs_str .= "[$i:v]trim=start=$crossfade:end=$part_length_crossfade,setpts=PTS-STARTPTS[clip$i];";
								} else
								{
									$filter_complex_inputs_str .= "[$i:v]trim=start=$crossfade:end=$crossfade_fragment,setpts=PTS-STARTPTS[clip$i];";
								}
								if ($i != $parts - 1)
								{
									$filter_complex_inputs_str .= "[$i:v]trim=start=$crossfade_fragment:end=$part_length_crossfade,setpts=PTS-STARTPTS[fadeoutsrc$i];[fadeoutsrc$i]format=pix_fmts=yuva420p,fade=t=out:st=0:d=$crossfade:alpha=1[fadeout$i];[fadeout$i]fifo[fadeoutfifo$i];";
								}
							}
						}
						if ($crossfade > 0)
						{
							$filter_complex_concat_str = '';
							$filter_complex_audio_str = '';
							$filter_complex_audio_sink = '';
							for ($i = 0; $i < $parts; $i++)
							{
								$j = $i - 1;
								if ($i == 0)
								{
									$filter_complex_concat_str .= "[clip$i]";
								} else
								{
									$filter_complex_concat_str .= "[crossfade{$j}_{$i}][clip$i]";
									if ($filter_complex_audio_sink == '')
									{
										$filter_complex_audio_str .= "[$j:a][$i:a] acrossfade=d=$crossfade:o=1 [audio{$j}_{$i}];";
									} else
									{
										$filter_complex_audio_str .= "[$filter_complex_audio_sink][$i:a] acrossfade=d=$crossfade:o=1 [audio{$j}_{$i}];";
									}
									$filter_complex_audio_sink = "audio{$j}_{$i}";
								}
							}
							$filter_complex_concat_parts = 2 * $parts - 1;
							$filter_complex_concat_str .= "concat=n=$filter_complex_concat_parts [output];";
							$filter_complex_audio_str = trim(trim($filter_complex_audio_str), ';');

							$filter_complex = "-filter_complex \"$filter_complex_inputs_str $filter_complex_concat_str $filter_complex_audio_str\"";

							$exec_str = "{$priority_prefix}$ffmpeg_path -y $files_crossfade_str $filter_complex -map \"[output]\" -map \"[$filter_complex_audio_sink]\" -strict -2 $task_dir/merged_$merged_hash.mp4 2>&1";
							log_command($exec_str);
						} else
						{
							file_put_contents("$task_dir/merged_$merged_hash.txt", trim($files_target_str));

							$exec_str = "{$priority_prefix}$ffmpeg_path -y -f concat -safe 0 -i $task_dir/merged_$merged_hash.txt -c copy $task_dir/merged_$merged_hash.mp4 2>&1";
							log_command($exec_str);
						}

						unset($res);
						exec($exec_str, $res);
						log_console($res);

						if (!is_file("$task_dir/merged_$merged_hash.mp4"))
						{
							log_error("Failed to merge video fragments", $exec_str, $res);
							$task_result['is_error'] = 1;
							break;
						} else
						{
							$converted_filesize = sprintf("%.0f", filesize("$task_dir/merged_$merged_hash.mp4"));
							$converted_duration = get_video_duration("$task_dir/merged_$merged_hash.mp4");
							if ($converted_filesize > 0 && $converted_duration > 0)
							{
								log_info("Created merged file: $converted_duration seconds, $converted_filesize bytes");
							} else
							{
								log_error("Created merged file: $converted_duration seconds, $converted_filesize bytes", $exec_str, $res);
								$task_result['is_error'] = 1;
								break;
							}
						}

						for ($i = 0; $i < $parts; $i++)
						{
							unlink("$task_dir/$video_id{$video_format['postfix']}_part{$i}.mp4");
						}
						@unlink("$task_dir/merged_$merged_hash.txt");
						$merged_hashes[] = $merged_hash;
					} else
					{
						log_info("Using already existing partitioned source file");
					}

					log_info("Creating video file [PH-C-5-2]");
					if (stripos($video_format['postfix'], ".gif") !== false)
					{
						$filters_string = "-vf \"palettegen\"";
						if ($filters_string_components)
						{
							$filters_string = "-vf \"$filters_string_components, palettegen\"";
						}
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $task_dir/merged_$merged_hash.mp4 $filters_string $task_dir/$video_id{$video_format['postfix']}_palette.png 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);

						if (filesize("$task_dir/$video_id{$video_format['postfix']}_palette.png") == 0)
						{
							log_error("Failed to create GIF palette file", $exec_str, $res);
							$task_result['is_error'] = 1;
							break;
						}

						$filters_string = "-filter_complex \"[0:v][1:v] paletteuse\"";
						if ($filters_string_components)
						{
							$filters_string = "-filter_complex \"" . str_replace('[in]', '[0:v]', $filters_string_components) . " [out]; [out][1:v] paletteuse\"";
						}
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $task_dir/merged_$merged_hash.mp4 -i $task_dir/$video_id{$video_format['postfix']}_palette.png $video_format[ffmpeg_options] $filters_string $task_dir/$video_id$video_format[postfix] 2>&1";
					} else
					{
						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $task_dir/merged_$merged_hash.mp4 $video_format[ffmpeg_options] $filters_string $task_dir/$video_id$video_format[postfix] 2>&1";
					}
					log_command($exec_str);

					unset($res);
					exec($exec_str, $res);
					log_console($res);

					foreach ($res as $line)
					{
						if (strpos($line, "moving header") !== false || strpos($line, "moving the moov atom") !== false)
						{
							$had_second_path = true;
							break;
						}
					}
				}

				@unlink("$task_dir/$video_id{$video_format['postfix']}_palette.png");

				if (!is_file("$task_dir/$video_id$video_format[postfix]"))
				{
					log_error("Failed to create video file", $exec_str, $res);
					$task_result['is_error'] = 1;
					break;
				}
				$converted_filesize = sprintf("%.0f", filesize("$task_dir/$video_id$video_format[postfix]"));
				$converted_duration = get_video_duration("$task_dir/$video_id$video_format[postfix]");
				if ($converted_filesize > 0 && $converted_duration > 0)
				{
					log_info("Created video file $video_id{$video_format['postfix']}: $converted_duration seconds, $converted_filesize bytes");
				} else
				{
					log_error("Created video file $video_id{$video_format['postfix']}: $converted_duration seconds, $converted_filesize bytes", $exec_str, $res);
					$task_result['is_error'] = 1;
					break;
				}

				if (stripos($video_format['postfix'], ".flv") !== false)
				{
					log_info("Injecting metadata [PH-C-5-3]");

					log_error("FLV files are not supported anymore");
					$task_result['is_error'] = 1;
					break;
				} elseif (stripos($video_format['postfix'], ".mp4") !== false)
				{
					log_info("Moving metadata to the file beginning [PH-C-5-3]");

					if (strpos($video_format['ffmpeg_options'], "faststart") !== false && $had_second_path)
					{
						log_info("Skipped, already done by FFmpeg");
					} else
					{
						log_error("FFmpeg options should define -movflags +faststart");
						$task_result['is_error'] = 1;
						break;
					}
				}
				if (is_array($task_info['storage_servers']) && $task_result['is_error'] != 1)
				{
					if (!function_exists('ftp_connect'))
					{
						log_error("FTP functions not supported, this conversion server does not support option to push files to storage servers");
						$task_result['is_error'] = 1;
						$task_result['error_code'] = 4;
						$task_result['error_message'] = "FTP functions not supported, this conversion server does not support option to push files to storage servers";
						break;
					}
					if (@file_get_contents("$task_dir/deleted.dat") == '1')
					{
						log_error("Skipping $task_id task as it is being marked to be deleted");
						$task_result['is_error'] = 1;
						break;
					}

					$video_dimension = get_video_dimensions("$task_dir/$video_id$video_format[postfix]");
					$video_duration = get_video_duration("$task_dir/$video_id$video_format[postfix]");
					$video_size = sprintf("%.0f", filesize("$task_dir/$video_id$video_format[postfix]"));
					foreach ($task_info['storage_servers'] as $server)
					{
						log_info("Copying video file to \"$server[title]\" [PH-C-5-4:$server[title]]");
						if (!put_file("$video_id$video_format[postfix]", "$task_dir", "$dir_path/$video_id", $server, count($task_info['storage_servers']) == 1))
						{
							log_error("Failed to put $video_id$video_format[postfix] file to storage server \"$server[title]\"");
							$task_result['is_error'] = 1;
							$task_result['error_code'] = 4;
							$task_result['error_message'] = "Failed to put $video_id$video_format[postfix] file to storage server \"$server[title]\"";
							break 2;
						}
					}
					$task_result['video_files_completed'][$video_format['postfix']] = ['dimensions' => $video_dimension, 'duration' => $video_duration, 'size' => $video_size];
					log_info("Copied video file to storage: $video_id$video_format[postfix], $video_dimension[0]x$video_dimension[1], $video_duration sec, $video_size bytes");
				} else
				{
					$task_result['video_files'][] = "$video_id$video_format[postfix]";
				}

				$task_progress++;
				file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
			}
		}

		if (is_array($task_info['videos_post_process_list']) && $task_result['is_error'] != 1)
		{
			foreach ($task_info['videos_post_process_list'] as $video_format)
			{
				log_info("Post-processing video format \"$video_format[title]\" [PH-C-6:$video_format[title]]");

				$target_file_dir = $source_dir;
				if (stripos($video_format['postfix'], ".mp4") !== false)
				{
					$needs_metadata_move = true;
					$fp = fopen("$source_dir/$video_id$video_format[postfix]", 'rb');
					$bytes = fread($fp, 100);
					$bytes = unpack('C*', $bytes);
					if ($bytes[4] > 0 && $bytes[5] == 102 && $bytes[6] == 116 && $bytes[7] == 121 && $bytes[8] == 112)
					{
						if ($bytes[$bytes[4] + 5] == 109 && $bytes[$bytes[4] + 6] == 111 && $bytes[$bytes[4] + 7] == 111 && $bytes[$bytes[4] + 8] == 118)
						{
							$needs_metadata_move = false;
						}
					}

					if ($needs_metadata_move)
					{
						log_info("Moving metadata to the file beginning [PH-C-6-1]");
						$target_file_dir = $task_dir;

						$exec_str = "{$priority_prefix}$ffmpeg_path -y -i $source_dir/$video_id$video_format[postfix] -acodec copy -vcodec copy -movflags +faststart -strict -2 $task_dir/{$video_id}_temp$video_format[postfix] 2>&1";
						log_command($exec_str);

						unset($res);
						exec($exec_str, $res);
						log_console($res);

						if (!is_file("$task_dir/{$video_id}_temp$video_format[postfix]"))
						{
							log_error("Failed to move metadata in video file", $exec_str, $res);
							$task_result['is_error'] = 1;
							break;
						}

						rename("$task_dir/{$video_id}_temp$video_format[postfix]", "$task_dir/$video_id$video_format[postfix]");
						$converted_filesize = sprintf("%.0f", @filesize("$task_dir/$video_id$video_format[postfix]"));
						$converted_duration = get_video_duration("$task_dir/$video_id$video_format[postfix]");
						if ($converted_filesize > 0 && $converted_duration > 0)
						{
							log_info("Updated video file $video_id{$video_format['postfix']}: $converted_duration seconds, $converted_filesize bytes");
						} else
						{
							log_error("Updated video file $video_id{$video_format['postfix']}: $converted_duration seconds, $converted_filesize bytes", $exec_str, $res);
							$task_result['is_error'] = 1;
							break;
						}
					} else
					{
						log_info("No need to move metadata, video file already has MOOV atom at the start");
					}
				}
				if (is_array($task_info['storage_servers']) && $task_result['is_error'] != 1)
				{
					if (!function_exists('ftp_connect'))
					{
						log_error("FTP functions not supported, this conversion server does not support option to push files to storage servers");
						$task_result['is_error'] = 1;
						$task_result['error_code'] = 4;
						$task_result['error_message'] = "FTP functions not supported, this conversion server does not support option to push files to storage servers";
						break;
					}
					if (@file_get_contents("$task_dir/deleted.dat") == '1')
					{
						log_error("Skipping $task_id task as it is being marked to be deleted");
						$task_result['is_error'] = 1;
						break;
					}

					$video_dimension = get_video_dimensions("$target_file_dir/$video_id$video_format[postfix]");
					$video_duration = get_video_duration("$target_file_dir/$video_id$video_format[postfix]");
					$video_size = sprintf("%.0f", filesize("$target_file_dir/$video_id$video_format[postfix]"));
					foreach ($task_info['storage_servers'] as $server)
					{
						log_info("Copying video file to \"$server[title]\" [PH-C-6-2:$server[title]]");
						if (!put_file("$video_id$video_format[postfix]", "$target_file_dir", "$dir_path/$video_id", $server, false))
						{
							log_error("Failed to put $video_id$video_format[postfix] file to storage server \"$server[title]\"");
							$task_result['is_error'] = 1;
							$task_result['error_code'] = 4;
							$task_result['error_message'] = "Failed to put $video_id$video_format[postfix] file to storage server \"$server[title]\"";
							break 2;
						}
					}
					$task_result['video_files_completed'][$video_format['postfix']] = ['dimensions' => $video_dimension, 'duration' => $video_duration, 'size' => $video_size];
					log_info("Copied video file to storage: $video_id$video_format[postfix], $video_dimension[0]x$video_dimension[1], $video_duration sec, $video_size bytes");
				} else
				{
					$task_result['video_files'][] = "$video_id$video_format[postfix]";
				}

				$task_progress++;
				file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
			}
		}

		$task_result['screenshots_data'] = [];

		if ($task_info['make_screens'] == 1 && $task_result['is_error'] != 1)
		{
			log_info("Creating overview screenshots [PH-C-7]");

			if (!is_dir("$task_dir/screenshots"))
			{
				mkdir("$task_dir/screenshots");
				chmod("$task_dir/screenshots", 0777);
			}
			$i_thumb = 0;

			$end_screen_offset = intval($task_info['options']['SCREENSHOTS_SECONDS_OFFSET_END']);
			if ($source_duration > $end_screen_offset)
			{
				$source_duration -= $end_screen_offset;
			} else
			{
				log_warning("Last screenshot offset $end_screen_offset cannot be used for video with duration $source_duration");
			}

			if ($task_info['options']['SCREENSHOTS_COUNT_UNIT'] == 1)
			{
				$amount = intval($task_info['options']['SCREENSHOTS_COUNT']);
				$step_target = intval($task_info['options']['SCREENSHOTS_SECONDS_OFFSET']);
				if ($step_target > $source_duration)
				{
					log_warning("First screenshot offset $step_target cannot be used for video with duration $source_duration");
					$step_target = 0;
				}
				$step = floor(($source_duration - $step_target - 1) / $amount);
				if ($step == 0)
				{
					$step = ($source_duration - $step_target - 1) / $amount;
				}
				log_info("Creating $amount overview screenshots starting from $step_target sec with step $step [PH-C-7-1]");

				for ($is = 0; $is < $amount; $is++)
				{
					unset($res);
					$exec_str = "{$priority_prefix}$ffmpeg_path -ss $step_target -i $source_file -y $screenshot_options $task_dir/result.jpg 2>&1";
					log_command($exec_str);

					exec($exec_str, $res);

					if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0 && analyze_screenshot("$task_dir/result.jpg"))
					{
						$i_thumb++;
						$output_file = "$task_dir/screenshots/$i_thumb.jpg";
						rename("$task_dir/result.jpg", $output_file);

						$exec_res = process_screen_source($output_file, $task_info['options'], false, $task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE']);
						if ($exec_res)
						{
							log_error("Failed to process screenshot source", $exec_res);
							$task_result['is_error'] = 1;
							break;
						}

						log_info("Created screenshot $i_thumb at $step_target sec");
						$task_result['screenshots_data'][$i_thumb] = ['type' => 'auto', 'filesize' => filesize($output_file)];
					} else
					{
						log_warning("No screenshot using quick method at $step_target sec");
						clearstatcache();

						unset($res);
						$exec_str = "{$priority_prefix}$ffmpeg_path -i $source_file -ss $step_target -y $screenshot_options $task_dir/result.jpg 2>&1";
						log_command($exec_str);

						exec($exec_str, $res);

						if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0)
						{
							$i_thumb++;
							$output_file = "$task_dir/screenshots/$i_thumb.jpg";
							rename("$task_dir/result.jpg", $output_file);

							$exec_res = process_screen_source($output_file, $task_info['options'], false, $task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE']);
							if ($exec_res)
							{
								log_error("Failed to process screenshot source", $exec_res);
								$task_result['is_error'] = 1;
								break;
							}

							log_info("Created screenshot $i_thumb at $step_target sec");
							$task_result['screenshots_data'][$i_thumb] = ['type' => 'auto', 'filesize' => filesize($output_file)];
						} else
						{
							log_console($res);
							log_warning("No screenshot using slow method at $step_target sec");
						}
					}
					$step_target += $step;
				}
			} else
			{
				$step = intval($task_info['options']['SCREENSHOTS_COUNT']);
				if ($step < 1)
				{
					$step = 1;
				}
				$step_target = intval($task_info['options']['SCREENSHOTS_SECONDS_OFFSET']);
				if ($step_target > $source_duration)
				{
					log_warning("First screenshot offset $step_target cannot be used for video with duration $source_duration");
					$step_target = 0;
				}
				log_info("Creating overview screenshots starting from $step_target sec with step $step [PH-C-7-1]");

				for ($is = 0; $is < 99999; $is++)
				{
					if ($step_target > $source_duration - 1)
					{
						break;
					}
					unset($res);
					$exec_str = "{$priority_prefix}$ffmpeg_path -ss $step_target -i $source_file -y $screenshot_options $task_dir/result.jpg 2>&1";
					log_command($exec_str);

					exec($exec_str, $res);

					if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0 && analyze_screenshot("$task_dir/result.jpg"))
					{
						$i_thumb++;
						$output_file = "$task_dir/screenshots/$i_thumb.jpg";
						rename("$task_dir/result.jpg", $output_file);

						$exec_res = process_screen_source($output_file, $task_info['options'], false, $task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE']);
						if ($exec_res)
						{
							log_error("Failed to process screenshot source", $exec_res);
							$task_result['is_error'] = 1;
							break;
						}

						log_info("Created screenshot $i_thumb at $step_target sec");
						$task_result['screenshots_data'][$i_thumb] = ['type' => 'auto', 'filesize' => filesize($output_file)];
					} else
					{
						log_warning("No screenshot using quick method at $step_target sec");
						clearstatcache();

						unset($res);
						$exec_str = "{$priority_prefix}$ffmpeg_path -i $source_file -ss $step_target -y $screenshot_options $task_dir/result.jpg 2>&1";
						log_command($exec_str);

						exec($exec_str, $res);

						if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0)
						{
							$i_thumb++;
							$output_file = "$task_dir/screenshots/$i_thumb.jpg";
							rename("$task_dir/result.jpg", $output_file);

							$exec_res = process_screen_source($output_file, $task_info['options'], false, $task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE']);
							if ($exec_res)
							{
								log_error("Failed to process screenshot source", $exec_res);
								$task_result['is_error'] = 1;
								break;
							}

							log_info("Created screenshot $i_thumb at $step_target sec");
							$task_result['screenshots_data'][$i_thumb] = ['type' => 'auto', 'filesize' => filesize($output_file)];
						} else
						{
							log_console($res);
							log_warning("No screenshot using slow method at $step_target sec");
						}
					}
					$step_target += $step;
				}
			}

			if (is_array($task_info['formats_screenshots']) && $task_result['is_error'] != 1)
			{
				// create all overview formats
				foreach ($task_info['formats_screenshots'] as $format)
				{
					if ($format['group_id'] == 1)
					{
						log_info("Creating thumbs for \"$format[title]\" format [PH-C-7-2:$format[title]]");
						if (!is_dir("$task_dir/screenshots/$format[size]"))
						{
							mkdir("$task_dir/screenshots/$format[size]");
							chmod("$task_dir/screenshots/$format[size]", 0777);
						}
						for ($i = 1; $i <= count($task_result['screenshots_data']); $i++)
						{
							$exec_res = make_screen_from_source("$task_dir/screenshots/$i.jpg", "$task_dir/screenshots/$format[size]/$i.jpg", $format, $task_info['options'], false);
							if ($exec_res)
							{
								log_error("Failed to create thumb format", $exec_res);
								$task_result['is_error'] = 1;
								break 2;
							}
						}
					}
				}
			}

			$task_progress++;
			file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
		}

		if ($task_info['uploaded_screenshots_count'] > 0 && $task_result['is_error'] != 1)
		{
			log_info("Processing $task_info[uploaded_screenshots_count] uploaded overview screenshots [PH-C-8]");

			if ($task_info['options']['SCREENSHOTS_UPLOADED_CROP'] == 1)
			{
				log_info("Applying crop for uploaded screenshots");
			}

			// create all overview formats
			if (!is_dir("$task_dir/screenshots"))
			{
				mkdir("$task_dir/screenshots");
				chmod("$task_dir/screenshots", 0777);
			}
			for ($i = 1; $i <= $task_info['uploaded_screenshots_count']; $i++)
			{
				copy("$task_dir/screenshot{$i}.jpg", "$task_dir/screenshots/$i.jpg");
				$exec_res = process_screen_source("$task_dir/screenshots/$i.jpg", $task_info['options'], true, $task_info['options']['SCREENSHOTS_CROP_CUSTOMIZE']);
				if ($exec_res)
				{
					log_error("Failed to process screenshot source", $exec_res);
					$task_result['is_error'] = 1;
					break;
				}
				$task_result['screenshots_data'][$i] = ['type' => 'uploaded', 'filesize' => filesize("$task_dir/screenshots/$i.jpg")];
			}

			if (is_array($task_info['formats_screenshots']) && $task_result['is_error'] != 1)
			{
				foreach ($task_info['formats_screenshots'] as $format)
				{
					if ($format['group_id'] == 1)
					{
						log_info("Creating thumbs for \"$format[title]\" format [PH-C-8-1:$format[title]]");
						if (!is_dir("$task_dir/screenshots/$format[size]"))
						{
							mkdir("$task_dir/screenshots/$format[size]");
							chmod("$task_dir/screenshots/$format[size]", 0777);
						}
						for ($i = 1; $i <= $task_info['uploaded_screenshots_count']; $i++)
						{
							$exec_res = make_screen_from_source("$task_dir/screenshots/$i.jpg", "$task_dir/screenshots/$format[size]/$i.jpg", $format, $task_info['options'], true);
							if ($exec_res)
							{
								log_error("Failed to create thumb format", $exec_res);
								$task_result['is_error'] = 1;
								break 2;
							}
						}
					}
				}
			}
		}

		if (is_array($task_info['timelines_creation_list']) && $task_result['is_error'] != 1)
		{
			if (!is_dir("$task_dir/timelines"))
			{
				mkdir("$task_dir/timelines");
				chmod("$task_dir/timelines", 0777);
			}
			foreach ($task_info['timelines_creation_list'] as $video_format)
			{
				log_info("Creating timeline screenshots for video format \"$video_format[title]\" [PH-C-9:$video_format[title]]");

				$timelines_source_file = "$task_dir/$video_id$video_format[postfix]";
				if (!is_file($timelines_source_file))
				{
					$timelines_source_file = "$source_dir/$video_id$video_format[postfix]";
				}
				if (!is_file($timelines_source_file))
				{
					if ($video_format['status_id'] == 2 && $video_format['is_conditional'] == 1)
					{
						log_info("Video format was not created, timeline screenshots creation will be skipped");
						continue;
					} else
					{
						log_error("No source file for creating timeline screenshots");
						$task_result['is_error'] = 1;
						break;
					}
				}

				$timeline_prefix = $video_format['timeline_directory'];

				$task_result['timeline_screenshots_count'][$video_format['postfix']] = 0;
				$i_thumb = 0;

				$format_duration = get_video_duration($timelines_source_file);
				if ($format_duration > 4 * 3600)
				{
					$format_duration = 4 * 3600;
					log_warning("Video file duration is greater than 4 hours, conversion engine will consider 4 hours");
				}

				if (intval($video_format['timeline_option']) == 1)
				{
					$step = round($format_duration / intval($video_format['timeline_amount']));
					if ($step < 10)
					{
						$step = 10;
					} elseif ($step % 10 <= 5)
					{
						$step -= ($step % 10);
					} else
					{
						$step -= ($step % 10);
						$step += 10;
					}
				} else
				{
					$step = intval($video_format['timeline_interval']);
				}
				$task_result['timeline_screenshots_interval'][$video_format['postfix']] = $step;

				if ($step < 1)
				{
					$step = 1;
				}
				$step_target = 1;
				log_info("Creating screenshots starting from $step_target sec with step $step [PH-C-9-1]");

				for ($is = 0; $is < 99999; $is++)
				{
					if ($step_target > $format_duration - 1)
					{
						break;
					}
					unset($res);
					$exec_str = "{$priority_prefix}$ffmpeg_path -ss $step_target -i $timelines_source_file -y $screenshot_options $task_dir/result.jpg 2>&1";
					log_command($exec_str);

					exec($exec_str, $res);

					if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0)
					{
						$i_thumb++;
						$output_file = "$task_dir/timelines/{$timeline_prefix}_{$i_thumb}.jpg";
						rename("$task_dir/result.jpg", $output_file);
						log_info("Created screenshot $i_thumb at $step_target sec");
					} else
					{
						log_warning("No screenshot using fast method at $step_target sec");
						if ($i_thumb > 0)
						{
							$i_thumb_old = $i_thumb;
							$i_thumb++;
							copy("$task_dir/timelines/{$timeline_prefix}_{$i_thumb_old}.jpg", "$task_dir/timelines/{$timeline_prefix}_{$i_thumb}.jpg");
						} else
						{
							unset($res);
							$exec_str = "{$priority_prefix}$ffmpeg_path -i $timelines_source_file -ss $step_target -y $screenshot_options $task_dir/result.jpg 2>&1";
							log_command($exec_str);

							exec($exec_str, $res);

							if (is_file("$task_dir/result.jpg") && filesize("$task_dir/result.jpg") > 0)
							{
								$i_thumb++;
								$output_file = "$task_dir/timelines/{$timeline_prefix}_{$i_thumb}.jpg";
								rename("$task_dir/result.jpg", $output_file);
								log_info("Created screenshot $i_thumb at $step_target sec");
							} else
							{
								log_error("Failed to create the first timeline screenshot using slow method", $exec_str, $res, true);
								$task_result['is_error'] = 1;
								break;
							}
						}
					}
					$step_target += $step;
					if ($is == 0)
					{
						$step_target--;
					}
				}
				$task_result['timeline_screenshots_count'][$video_format['postfix']] = $i_thumb;

				if (is_array($task_info['formats_screenshots']))
				{
					// create all timeline formats
					foreach ($task_info['formats_screenshots'] as $format)
					{
						if ($format['group_id'] == 2)
						{
							log_info("Creating thumbs for \"$format[title]\" format [PH-C-9-2:$format[title]]");
							if (!is_dir("$task_dir/timelines/$format[size]"))
							{
								mkdir("$task_dir/timelines/$format[size]");
								chmod("$task_dir/timelines/$format[size]", 0777);
							}
							for ($i = 1; $i <= $task_result['timeline_screenshots_count'][$video_format['postfix']]; $i++)
							{
								$exec_res = make_screen_from_source("$task_dir/timelines/{$timeline_prefix}_{$i}.jpg", "$task_dir/timelines/$format[size]/{$timeline_prefix}_{$i}.jpg", $format, $task_info['options'], false);
								if ($exec_res)
								{
									log_error("Failed to create thumb format", $exec_res);
									$task_result['is_error'] = 1;
									break 2;
								}
							}
						}
					}
				}

				$task_progress++;
				file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
			}
		}

		if ($task_info['uploaded_posters_count'] > 0 && $task_result['is_error'] != 1)
		{
			log_info("Processing $task_info[uploaded_posters_count] uploaded posters [PH-C-12]");

			for ($i = 1; $i <= $task_info['uploaded_posters_count']; $i++)
			{
				$task_result['posters_data'][$i] = ['type' => 'uploaded', 'filesize' => filesize("$task_dir/poster$i.jpg")];
			}

			// create all poster formats
			if (!is_dir("$task_dir/posters"))
			{
				mkdir("$task_dir/posters");
				chmod("$task_dir/posters", 0777);
			}

			if (is_array($task_info['formats_screenshots']) && $task_result['is_error'] != 1)
			{
				foreach ($task_info['formats_screenshots'] as $format)
				{
					if ($format['group_id'] == 3)
					{
						log_info("Creating thumbs for \"$format[title]\" format [PH-C-12-1:$format[title]]");
						if (!is_dir("$task_dir/posters/$format[size]"))
						{
							mkdir("$task_dir/posters/$format[size]");
							chmod("$task_dir/posters/$format[size]", 0777);
						}
						for ($i = 1; $i <= $task_info['uploaded_posters_count']; $i++)
						{
							$exec_res = make_screen_from_source("$task_dir/poster$i.jpg", "$task_dir/posters/$format[size]/$i.jpg", $format, $task_info['options'], false);
							if ($exec_res)
							{
								log_error("Failed to create thumb format", $exec_res);
								$task_result['is_error'] = 1;
								break 2;
							}
						}
					}
				}
			}
		}

		// delete created format files if they were copied to storage
		if (is_array($task_info['videos_creation_list']) && is_array($task_info['storage_servers']))
		{
			foreach ($task_info['videos_creation_list'] as $video_format)
			{
				@unlink("$task_dir/$video_id$video_format[postfix]");
				@unlink("$task_dir/watermark_video_{$video_format['format_video_id']}_resized.png");
			}
		}
		foreach ($merged_hashes as $merged_hash)
		{
			@unlink("$task_dir/merged_{$merged_hash}.mp4");
		}

		file_put_contents("$task_dir/progress.dat", 100, LOCK_EX);

		$duration = time() - $start_time;
		$task_result['duration'] = $duration;
		log_info("Conversion task $task_id for video $video_id is finished in $duration sec [PH-CE]");
	} elseif (intval($task_info['album_id']))
	{
		$task_progress = 0;
		$total_progress = $task_info['source_images_count'] + 1;

		$album_id = $task_info['album_id'];
		$task_result['images'] = [];

		log_info('');
		log_info("Starting conversion task $task_id for album $album_id [PH-C]");
		$start_time = time();

		log_info("Creating main image formats [PH-C-10]");
		for ($i = 1; $i <= $task_info['source_images_count']; $i++)
		{
			$source_file = "$task_dir/$i.jpg";
			if (!is_file($source_file))
			{
				$source_file = "$source_dir/$i.jpg";
			}
			// create main album formats
			log_info("Creating formats for album image #$i");
			foreach ($task_info['formats_albums'] as $format)
			{
				if ($format['group_id'] == 1)
				{
					$exec_res = make_image_from_source($source_file, "$task_dir/$format[format_album_id]-$i.jpg", $format, $task_info['options'], $task_info['options']['ALBUMS_CROP_CUSTOMIZE']);
					if ($exec_res)
					{
						log_error("Failed to create image format", $exec_res);
						$task_result['is_error'] = 1;
						break 2;
					}
				}
			}
			$task_result['images'][] = $i;

			$task_progress++;
			file_put_contents("$task_dir/progress.dat", floor(($task_progress / $total_progress) * 100), LOCK_EX);
		}

		if ((intval($task_info['main_image']) > 0 || $task_info['preview_source']) && $task_result['is_error'] != 1)
		{
			// create preview formats
			if ($task_info['preview_source'])
			{
				$preview_source = "$task_info[preview_source]";
				log_info("Creating preview formats from manually uploaded source [PH-C-11]");
			} else
			{
				$main_image = intval($task_info['main_image']);
				$preview_source = "$main_image.jpg";
				log_info("Creating preview formats for album image #$main_image [PH-C-11]");
			}

			$preview_source = (is_file("$task_dir/$preview_source") ? "$task_dir/$preview_source" : "$source_dir/$preview_source");
			foreach ($task_info['formats_albums'] as $format)
			{
				if ($format['group_id'] == 2)
				{
					$exec_res = make_image_from_source($preview_source, "$task_dir/$format[format_album_id]-preview.jpg", $format, $task_info['options'], $task_info['options']['ALBUMS_CROP_CUSTOMIZE']);
					if ($exec_res)
					{
						log_error("Failed to create image format", $exec_res);
						$task_result['is_error'] = 1;
						break;
					}
				}
			}
		}

		file_put_contents("$task_dir/progress.dat", 100, LOCK_EX);

		$duration = time() - $start_time;
		$task_result['duration'] = $duration;
		log_info("Conversion task $task_id for album $album_id is finished in $duration sec [PH-CE]");
	}

	disconnect_all_servers();

	file_put_contents("$task_dir/result.dat", serialize($task_result), LOCK_EX);
}

$task_id = 0;
log_info("Conversion processor finished");

fclose($lock);

function get_video_duration(string $file): int
{
	global $ffmpeg_path;

	$duration = 0;
	if (is_file($file))
	{
		unset($res);
		exec("$ffmpeg_path -i \"$file\"  2>&1", $res);
		preg_match("|Duration: (\d+:\d+:[0-9\.]+)|is", implode("\r\n", $res), $temp);
		$temp[1] = explode(":", $temp[1]);
		$duration = round($temp[1][0]) * 3600 + round($temp[1][1]) * 60 + round($temp[1][2]);
	}
	return $duration;
}

function get_video_dimensions(string $file): array
{
	global $ffmpeg_path;
	$video_width = 0;
	$video_height = 0;

	if (is_file($file))
	{
		$rnd = mt_rand(1000000, 999999999);
		exec("$ffmpeg_path -ss 0 -i \"$file\" -vframes 1 -y -f mjpeg -vf \"scale=trunc(iw*sar/2)*2:ih\" $_SERVER[PWD]/$rnd.jpg 2>&1 > /dev/null");
		if (is_file("$_SERVER[PWD]/$rnd.jpg") && function_exists('getimagesize'))
		{
			$size = getimagesize("$_SERVER[PWD]/$rnd.jpg");
			unlink("$_SERVER[PWD]/$rnd.jpg");
			[$video_width, $video_height] = $size;
			if ($video_width > 0 && $video_height > 0)
			{
				return [$video_width, $video_height];
			}
		}

		unset($res);
		exec("$ffmpeg_path -i \"$file\"  2>&1", $res);
		preg_match_all("|\d+x\d+|is", implode("\r\n", $res), $temp);
		foreach ($temp[0] as $potential_size)
		{
			$potential_size = explode("x", $potential_size);
			if (intval($potential_size[0]) > 0 && intval($potential_size[1]) > 0)
			{
				return [$potential_size[0], $potential_size[1]];
			}
		}
	}
	return [$video_width, $video_height];
}

function get_LA(): float
{
	$load = sys_getloadavg();
	return floatval($load[0]);
}

function process_screen_source(string $input_file, array $options, bool $is_uploaded_manually, ?string $custom_crop): string
{
	global $imagemagick_path;

	if ($is_uploaded_manually && $options['SCREENSHOTS_UPLOADED_CROP'] == 0)
	{
		return '';
	}

	$priority_prefix = '';
	if ($options['PROCESS_PRIORITY'] > 0)
	{
		$priority = intval($options['PROCESS_PRIORITY']);
		$priority_prefix = "nice -n $priority ";
	}

	$img_size = @getimagesize($input_file);
	if ($img_size[0] == 0 || $img_size[1] == 0)
	{
		return "Invalid image: $input_file";
	}

	$crop_options = '';
	if (trim($custom_crop))
	{
		$custom_crop_options = array_map('trim', explode(',', $custom_crop));
		if (count($custom_crop_options) != 4)
		{
			return "Invalid crop options: " . implode(',', $custom_crop_options);
		}
		if (strpos($custom_crop_options[0], '%') === false)
		{
			$crop_left = intval($custom_crop_options[0]);
		} else
		{
			$crop_left = intval(intval($custom_crop_options[0]) / 100 * $img_size[0]);
		}
		if (strpos($custom_crop_options[1], '%') === false)
		{
			$crop_top = intval($custom_crop_options[1]);
		} else
		{
			$crop_top = intval(intval($custom_crop_options[1]) / 100 * $img_size[1]);
		}
		if (strpos($custom_crop_options[2], '%') === false)
		{
			$crop_right = intval($custom_crop_options[2]);
		} else
		{
			$crop_right = intval(intval($custom_crop_options[2]) / 100 * $img_size[0]);
		}
		if (strpos($custom_crop_options[3], '%') === false)
		{
			$crop_bottom = intval($custom_crop_options[3]);
		} else
		{
			$crop_bottom = intval(intval($custom_crop_options[3]) / 100 * $img_size[1]);
		}
	} else
	{
		if ($options['SCREENSHOTS_CROP_LEFT_UNIT'] == 1)
		{
			$crop_left = intval($options['SCREENSHOTS_CROP_LEFT']);
		} else
		{
			$crop_left = intval($options['SCREENSHOTS_CROP_LEFT'] / 100 * $img_size[0]);
		}
		if ($options['SCREENSHOTS_CROP_RIGHT_UNIT'] == 1)
		{
			$crop_right = intval($options['SCREENSHOTS_CROP_RIGHT']);
		} else
		{
			$crop_right = intval($options['SCREENSHOTS_CROP_RIGHT'] / 100 * $img_size[0]);
		}
		if ($options['SCREENSHOTS_CROP_TOP_UNIT'] == 1)
		{
			$crop_top = intval($options['SCREENSHOTS_CROP_TOP']);
		} else
		{
			$crop_top = intval($options['SCREENSHOTS_CROP_TOP'] / 100 * $img_size[1]);
		}
		if ($options['SCREENSHOTS_CROP_BOTTOM_UNIT'] == 1)
		{
			$crop_bottom = intval($options['SCREENSHOTS_CROP_BOTTOM']);
		} else
		{
			$crop_bottom = intval($options['SCREENSHOTS_CROP_BOTTOM'] / 100 * $img_size[1]);
		}
	}
	if ($crop_left + $crop_right + $crop_top + $crop_bottom > 0)
	{
		$crop_options = "-crop +$crop_left+$crop_top -crop -$crop_right-$crop_bottom";
	}

	if ($options['SCREENSHOTS_CROP_TRIM_SIDES'] == 1)
	{
		$crop_options .= ' -fuzz 7% -trim';
	}

	if ($crop_options)
	{
		$jpeg_quality = intval($options['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']);
		if ($jpeg_quality < 80)
		{
			$jpeg_quality = 80;
		}

		$exec_str = "{$priority_prefix}$imagemagick_path $input_file -quality $jpeg_quality $crop_options $input_file";
		log_command($exec_str);

		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($input_file) || filesize($input_file) == 0)
		{
			if (is_array($res))
			{
				$res = $res[0];
			}
			return "$exec_str\n....$res";
		}
	}
	return '';
}

function make_screen_from_source(string $input_file, string $output_file, array $format, array $options, bool $is_uploaded_manually): string
{
	global $task_dir, $imagemagick_path;

	$priority_prefix = '';
	if ($options['PROCESS_PRIORITY'] > 0)
	{
		$priority = intval($options['PROCESS_PRIORITY']);
		$priority_prefix = "nice -n $priority ";
	}

	$img_size = @getimagesize($input_file);
	if ($img_size[0] == 0 || $img_size[1] == 0)
	{
		return "Invalid image: $input_file";
	}

	$aspect_ratio_id = intval($format['aspect_ratio_id']);
	$aspect_ratio_gravity = trim($format['aspect_ratio_gravity']);
	if ($img_size[1] > $img_size[0])
	{
		if (intval($format['vertical_aspect_ratio_id']) > 0)
		{
			$aspect_ratio_id = intval($format['vertical_aspect_ratio_id']);
			$aspect_ratio_gravity = trim($format['vertical_aspect_ratio_gravity']);
		}
	}
	if (!$aspect_ratio_gravity || $aspect_ratio_id != 2)
	{
		$aspect_ratio_gravity = 'Center';
	}

	if (!in_array($aspect_ratio_gravity, ['North', 'West', 'Center', 'East', 'South']))
	{
		return "Invalid gravity value: $aspect_ratio_gravity";
	}

	if ($format['size'] == 'source')
	{
		$required_size = [$img_size[0], $img_size[1]];
	} else
	{
		$required_size = explode("x", trim($format['size']));
	}

	if ($is_uploaded_manually)
	{
		if ($img_size[0] == $required_size[0] && $img_size[1] == $required_size[1] && $options['SCREENSHOTS_UPLOADED_WATERMARK'] == 0)
		{
			copy($input_file, $output_file);
			return '';
		}
	}

	$resize_size = $required_size;
	if ($aspect_ratio_id == 2)
	{
		if (($required_size[0] / $img_size[0]) > ($required_size[1] / $img_size[1]))
		{
			$k = $required_size[0] / $img_size[0];
		} else
		{
			$k = $required_size[1] / $img_size[1];
		}

		$resize_size[0] = round($img_size[0] * $k);
		$resize_size[1] = round($img_size[1] * $k);
	} elseif ($aspect_ratio_id == 3)
	{
		$k = 1;
		if ($img_size[0] > $required_size[0] || $img_size[1] > $required_size[1])
		{
			if (($required_size[0] / $img_size[0]) < ($required_size[1] / $img_size[1]))
			{
				$k = $required_size[0] / $img_size[0];
			} else
			{
				$k = $required_size[1] / $img_size[1];
			}
		}

		$resize_size[0] = round($img_size[0] * $k);
		$resize_size[1] = round($img_size[1] * $k);
		$required_size = $resize_size;
	} elseif ($aspect_ratio_id == 4)
	{
		$resize_size[0] = round($img_size[0] * ($required_size[1] / $img_size[1]));
		$resize_size[1] = round($required_size[1]);
		if ($resize_size[0] < $required_size[0])
		{
			$required_size[0] = $resize_size[0];
		}
	} elseif ($aspect_ratio_id == 5)
	{
		$resize_size[0] = round($required_size[0]);
		$resize_size[1] = round($img_size[1] * ($required_size[0] / $img_size[0]));
		if ($resize_size[1] < $required_size[1])
		{
			$required_size[1] = $resize_size[1];
		}
	}
	$resize_size[0]++;
	$resize_size[1]++;

	if ($img_size['mime'] == 'image/gif' && preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($input_file)))
	{
		$input_file = "$input_file\[0\]";
	}

	if ($is_uploaded_manually && $format['im_options_manual'])
	{
		$format['im_options'] = $format['im_options_manual'];
	}

	$output_temp_file = "$task_dir/temp.bmp";
	$exec_str = "{$priority_prefix}$imagemagick_path " . str_replace("%SIZE%", "$resize_size[0]x$resize_size[1]", str_replace("%INPUT_FILE%", $input_file, str_replace("%OUTPUT_FILE%", $output_temp_file, $format['im_options'])));
	log_command($exec_str);

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_temp_file) || filesize($output_temp_file) == 0)
	{
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str\n....$res";
	}

	$jpeg_quality = intval($options['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']);
	unset($res);
	preg_match("|-quality\ +(\d+)|is", $format['im_options'], $res);
	if (intval($res[1]) > 0)
	{
		$jpeg_quality = intval($res[1]);
	}

	$jpeg_artifacts = '';
	unset($res);
	preg_match_all("|-define\ +jpeg:[^=]+=\ *[^=\ ]+|is", $format['im_options'], $res);
	if (count($res[0]) > 0)
	{
		$jpeg_artifacts = implode(' ', $res[0]);
	}

	$webp_artifacts = '';
	unset($res);
	preg_match_all("|-define\ +webp:[^=]+=\ *[^=\ ]+|is", $format['im_options'], $res);
	if (count($res[0]) > 0)
	{
		$webp_artifacts = implode(' ', $res[0]);
	}

	$watermark_path = '';
	if (!$is_uploaded_manually || $options['SCREENSHOTS_UPLOADED_WATERMARK'] == 1)
	{
		$watermark_path = "$task_dir/watermark_screen_{$format['format_screenshot_id']}.png";
	}

	$watermark_options = '';
	if (is_file($watermark_path))
	{
		$position = $format['watermark_position_id'];
		if ($position == 0)
		{
			$position = mt_rand(1, 4);
		}
		if ($position == 1)
		{
			$position = "-gravity NorthWest";
		} elseif ($position == 2)
		{
			$position = "-gravity NorthEast";
		} elseif ($position == 3)
		{
			$position = "-gravity SouthEast";
		} elseif ($position == 4)
		{
			$position = "-gravity SouthWest";
		}
		$watermark_options = "$watermark_path $position -composite";
	}

	$advanced_options = '';
	switch ($format['interlace_id'])
	{
		case 1:
			$advanced_options .= "-interlace line ";
			break;
		case 2:
			$advanced_options .= "-interlace plane ";
			break;
	}

	if ($format['comment'])
	{
		$advanced_options .= "-comment \"$format[comment]\"";
	}

	$background_image = 'xc:"#000000"';
	if ($aspect_ratio_id == 1)
	{
		$background_image = "$task_dir/temp2.bmp";
		$exec_str = "{$priority_prefix}$imagemagick_path -resize $required_size[0]x$required_size[1]^ -gravity center -extent $required_size[0]x$required_size[1] $output_temp_file -blur 0x6 -modulate 100,60 $background_image";
		log_command($exec_str);

		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($background_image) || filesize($background_image) == 0)
		{
			@unlink($output_temp_file);
			if (is_array($res))
			{
				$res = $res[0];
			}
			return "$exec_str\n....$res";
		}
	}

	if ($format['image_type'] == 1)
	{
		$exec_str = "{$priority_prefix}$imagemagick_path -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $webp_artifacts webp:$output_file";
	} else
	{
		$exec_str = "{$priority_prefix}$imagemagick_path -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $jpeg_artifacts $output_file";
	}
	log_command($exec_str);

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_file) || filesize($output_file) == 0)
	{
		@unlink($output_temp_file);
		@unlink($background_image);
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str\n....$res";
	}

	@unlink($output_temp_file);
	@unlink($background_image);
	return '';
}

function make_image_from_source(string $input_file, string $output_file, array $format, array $options, string $custom_crop): string
{
	global $task_dir, $imagemagick_path;

	$priority_prefix = '';
	if ($options['PROCESS_PRIORITY'] > 0)
	{
		$priority = intval($options['PROCESS_PRIORITY']);
		$priority_prefix = "nice -n $priority ";
	}

	$img_size = @getimagesize($input_file);
	if ($img_size[0] == 0 || $img_size[1] == 0)
	{
		return "Invalid image: $input_file";
	}

	$aspect_ratio_id = intval($format['aspect_ratio_id']);
	$aspect_ratio_gravity = trim($format['aspect_ratio_gravity']);
	if ($img_size[1] > $img_size[0])
	{
		if (intval($format['vertical_aspect_ratio_id']) > 0)
		{
			$aspect_ratio_id = intval($format['vertical_aspect_ratio_id']);
			$aspect_ratio_gravity = trim($format['vertical_aspect_ratio_gravity']);
		}
	}
	if (!$aspect_ratio_gravity || $aspect_ratio_id != 2)
	{
		$aspect_ratio_gravity = 'Center';
	}

	if (!in_array($aspect_ratio_gravity, ['North', 'West', 'Center', 'East', 'South']))
	{
		return "Invalid gravity value: $aspect_ratio_gravity";
	}

	$crop_options = '';
	if (trim($custom_crop))
	{
		$custom_crop_options = array_map('trim', explode(',', $custom_crop));
		if (count($custom_crop_options) != 4)
		{
			return "Invalid crop options: " . implode(',', $custom_crop_options);
		}
		if (strpos($custom_crop_options[0], '%') === false)
		{
			$crop_left = intval($custom_crop_options[0]);
		} else
		{
			$crop_left = intval(intval($custom_crop_options[0]) / 100 * $img_size[0]);
		}
		if (strpos($custom_crop_options[1], '%') === false)
		{
			$crop_top = intval($custom_crop_options[1]);
		} else
		{
			$crop_top = intval(intval($custom_crop_options[1]) / 100 * $img_size[1]);
		}
		if (strpos($custom_crop_options[2], '%') === false)
		{
			$crop_right = intval($custom_crop_options[2]);
		} else
		{
			$crop_right = intval(intval($custom_crop_options[2]) / 100 * $img_size[0]);
		}
		if (strpos($custom_crop_options[3], '%') === false)
		{
			$crop_bottom = intval($custom_crop_options[3]);
		} else
		{
			$crop_bottom = intval(intval($custom_crop_options[3]) / 100 * $img_size[1]);
		}
	} else
	{
		if ($options['ALBUMS_CROP_LEFT_UNIT'] == 1)
		{
			$crop_left = intval($options['ALBUMS_CROP_LEFT']);
		} else
		{
			$crop_left = intval($options['ALBUMS_CROP_LEFT'] / 100 * $img_size[0]);
		}
		if ($options['ALBUMS_CROP_RIGHT_UNIT'] == 1)
		{
			$crop_right = intval($options['ALBUMS_CROP_RIGHT']);
		} else
		{
			$crop_right = intval($options['ALBUMS_CROP_RIGHT'] / 100 * $img_size[0]);
		}
		if ($options['ALBUMS_CROP_TOP_UNIT'] == 1)
		{
			$crop_top = intval($options['ALBUMS_CROP_TOP']);
		} else
		{
			$crop_top = intval($options['ALBUMS_CROP_TOP'] / 100 * $img_size[1]);
		}
		if ($options['ALBUMS_CROP_BOTTOM_UNIT'] == 1)
		{
			$crop_bottom = intval($options['ALBUMS_CROP_BOTTOM']);
		} else
		{
			$crop_bottom = intval($options['ALBUMS_CROP_BOTTOM'] / 100 * $img_size[1]);
		}
	}
	if (intval($format['is_skip_crop']) == 1)
	{
		$crop_left = 0;
		$crop_right = 0;
		$crop_top = 0;
		$crop_bottom = 0;
	}
	if ($crop_left + $crop_right + $crop_top + $crop_bottom > 0)
	{
		$crop_options = "-crop +$crop_left+$crop_top -crop -$crop_right-$crop_bottom";
	}
	$img_size[0] = $img_size[0] - $crop_left - $crop_right;
	$img_size[1] = $img_size[1] - $crop_top - $crop_bottom;

	$required_size = explode("x", trim($format['size']));

	$resize_size = $required_size;
	if ($aspect_ratio_id == 2)
	{
		if (($required_size[0] / $img_size[0]) > ($required_size[1] / $img_size[1]))
		{
			$k = $required_size[0] / $img_size[0];
		} else
		{
			$k = $required_size[1] / $img_size[1];
		}

		$resize_size[0] = round($img_size[0] * $k);
		$resize_size[1] = round($img_size[1] * $k);
	} elseif ($aspect_ratio_id == 3)
	{
		$k = 1;
		if ($img_size[0] > $required_size[0] || $img_size[1] > $required_size[1])
		{
			if (($required_size[0] / $img_size[0]) < ($required_size[1] / $img_size[1]))
			{
				$k = $required_size[0] / $img_size[0];
			} else
			{
				$k = $required_size[1] / $img_size[1];
			}
		}

		$resize_size[0] = round($img_size[0] * $k);
		$resize_size[1] = round($img_size[1] * $k);
		$required_size = $resize_size;
	} elseif ($aspect_ratio_id == 4)
	{
		$resize_size[0] = round($img_size[0] * ($required_size[1] / $img_size[1]));
		$resize_size[1] = round($required_size[1]);
		if ($resize_size[0] < $required_size[0])
		{
			$required_size[0] = $resize_size[0];
		}
	} elseif ($aspect_ratio_id == 5)
	{
		$resize_size[0] = round($required_size[0]);
		$resize_size[1] = round($img_size[1] * ($required_size[0] / $img_size[0]));
		if ($resize_size[1] < $required_size[1])
		{
			$required_size[1] = $resize_size[1];
		}
	}
	$resize_size[0]++;
	$resize_size[1]++;

	if ($img_size['mime'] == 'image/gif' && preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($input_file)))
	{
		$input_file = "$input_file\[0\]";
	}

	$output_temp_file = "$task_dir/temp.bmp";
	$exec_str = "{$priority_prefix}$imagemagick_path $crop_options " . str_replace("%SIZE%", "$resize_size[0]x$resize_size[1]", str_replace("%INPUT_FILE%", $input_file, str_replace("%OUTPUT_FILE%", $output_temp_file, $format['im_options'])));
	log_command($exec_str);

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_temp_file) || filesize($output_temp_file) == 0)
	{
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str\n....$res";
	}

	$jpeg_quality = intval($options['IMAGEMAGICK_DEFAULT_JPEG_QUALITY']);
	unset($res);
	preg_match("|-quality\ +(\d+)|is", $format['im_options'], $res);
	if (intval($res[1]) > 0)
	{
		$jpeg_quality = intval($res[1]);
	}

	$jpeg_artifacts = '';
	unset($res);
	preg_match_all("|-define\ +jpeg:[^=]+=\ *[^=\ ]+|is", $format['im_options'], $res);
	if (count($res[0]) > 0)
	{
		$jpeg_artifacts = implode(' ', $res[0]);
	}

	$webp_artifacts = '';
	unset($res);
	preg_match_all("|-define\ +webp:[^=]+=\ *[^=\ ]+|is", $format['im_options'], $res);
	if (count($res[0]) > 0)
	{
		$webp_artifacts = implode(' ', $res[0]);
	}

	$watermark_path = "$task_dir/watermark_album_{$format['format_album_id']}.png";
	$watermark_new_path = '';

	$watermark_required_pc = intval($format['watermark_max_width']);
	if ($img_size[1] > $img_size[0])
	{
		$watermark_required_pc = intval($format['watermark_max_width_vertical']);
	}
	if ($watermark_required_pc > 0 && is_file($watermark_path))
	{
		$watermark_size = getimagesize($watermark_path);
		$watermark_actual_pc = floor($watermark_size[0] / $resize_size[0] * 100);
		if ($watermark_actual_pc > $watermark_required_pc)
		{
			$watermark_new_width = floor($resize_size[0] * $watermark_required_pc / 100);
			$watermark_new_height = floor($watermark_size[1] * ($watermark_new_width / $watermark_size[0]));
			$watermark_new_path = "$task_dir/watermark_temp.png";

			unset($res);
			$exec_str = "{$priority_prefix}$imagemagick_path $watermark_path -resize {$watermark_new_width}x{$watermark_new_height} $watermark_new_path 2>&1";
			log_command($exec_str);

			exec($exec_str, $res);
			if (!is_file($watermark_new_path) || filesize($watermark_new_path) == 0)
			{
				if (is_array($res))
				{
					$res = $res[0];
				}
				return "$exec_str\n....$res";
			}
			$watermark_path = $watermark_new_path;
		}
	}

	$watermark_options = '';
	if (is_file($watermark_path))
	{
		$position = $format['watermark_position_id'];
		if ($position == 0)
		{
			$position = mt_rand(1, 4);
		}
		if ($position == 1)
		{
			$position = "-gravity NorthWest";
		} elseif ($position == 2)
		{
			$position = "-gravity NorthEast";
		} elseif ($position == 3)
		{
			$position = "-gravity SouthEast";
		} elseif ($position == 4)
		{
			$position = "-gravity SouthWest";
		}
		$watermark_options = "$watermark_path $position -composite";
	}

	$advanced_options = '';
	switch ($format['interlace_id'])
	{
		case 1:
			$advanced_options .= "-interlace line ";
			break;
		case 2:
			$advanced_options .= "-interlace plane ";
			break;
	}

	if ($format['comment'])
	{
		$advanced_options .= "-comment \"$format[comment]\"";
	}

	$background_image = 'xc:"#000000"';
	if ($aspect_ratio_id == 1)
	{
		$background_image = "$task_dir/temp2.bmp";
		$exec_str = "{$priority_prefix}$imagemagick_path -resize $required_size[0]x$required_size[1]^ -gravity center -extent $required_size[0]x$required_size[1] $output_temp_file -blur 0x6 -modulate 100,60 $background_image";
		log_command($exec_str);

		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($background_image) || filesize($background_image) == 0)
		{
			@unlink($output_temp_file);
			if (is_array($res))
			{
				$res = $res[0];
			}
			return "$exec_str\n....$res";
		}
	}

	if ($format['image_type'] == 1)
	{
		$exec_str = "{$priority_prefix}$imagemagick_path -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $webp_artifacts webp:$output_file";
	} else
	{
		$exec_str = "{$priority_prefix}$imagemagick_path -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $jpeg_artifacts $output_file";
	}
	log_command($exec_str);

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_file) || filesize($output_file) == 0)
	{
		@unlink($output_temp_file);
		@unlink($background_image);
		if ($watermark_new_path)
		{
			@unlink($watermark_new_path);
		}
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str\n....$res";
	}

	@unlink($output_temp_file);
	@unlink($background_image);
	if ($watermark_new_path)
	{
		@unlink($watermark_new_path);
	}
	return '';
}

function analyze_screenshot(string $screenshot): bool
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

function put_file(string $file_name, string $path_from, string $path_to, array $server_data, bool $rename_if_possible = false): bool
{
	if (!isset($server_data['connection_type_id'], $path_from, $path_to) || strlen($file_name) < 3)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('put_file: ' . trim("$path_to/$file_name", '/'), $server_data);

	$source_file = rtrim($path_from, '/') . '/' . $file_name;
	if (!is_file($source_file))
	{
		debug_server('ERROR: source file doesn\'t exist', $server_data);
		return false;
	}

	$filesize = sprintf("%.0f", @filesize($source_file));

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$target_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $path_to, '/');
		$target_file = $target_folder . '/' . $file_name;

		if (!mkdir_recursive($target_folder, 0777))
		{
			if (!is_dir($target_folder))
			{
				debug_server('ERROR: failed to create target directory', $server_data);
			} else
			{
				debug_server('ERROR: target directory is not writable', $server_data);
			}
			return false;
		}

		if ($rename_if_possible && $server_data['connection_type_id'] == 0)
		{
			if (@rename($source_file, $target_file))
			{
				debug_server("file renamed ($filesize bytes)", $server_data);
				return true;
			} else
			{
				debug_server('WARN: failed to rename file', $server_data);
			}
		}
		if (@copy($source_file, $target_file))
		{
			debug_server("file copied ($filesize bytes)", $server_data);
			return true;
		} else
		{
			debug_server('ERROR: failed to copy file', $server_data);
			return false;
		}
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}
		}

		$target_folder = trim($path_to, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$target_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $target_folder, '/');
		}
		$target_file = ltrim($target_folder . '/' . $file_name, '/');

		if ($target_folder)
		{
			$paths = explode('/', $target_folder);
			foreach ($paths as $path)
			{
				if (!isset($current_path))
				{
					$current_path = $path;
				} else
				{
					$current_path .= '/' . $path;
				}

				if (!@ftp_mkdir($conn_id, $current_path))
				{
					$conn_id = ftp_check_connection($conn_id, $server_data);
					if (!isset($conn_id))
					{
						debug_server('ERROR: failed to put file', $server_data);
						return false;
					}

					@ftp_mkdir($conn_id, $current_path);
				}
			}
		}

		if (!@ftp_put($conn_id, $target_file, $source_file, FTP_BINARY))
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}

			if (!@ftp_put($conn_id, $target_file, $source_file, FTP_BINARY))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}
		}

		debug_server("file copied ($filesize bytes)", $server_data);
		return true;
	}
	return false;
}

function ftp_check_connection($conn_id, array $server_data)
{
	if (!@ftp_pwd($conn_id))
	{
		debug_server('ERROR: connection failure', $server_data);
		$conn_id = ftp_get_connect_id($server_data, true);
	}
	return $conn_id;
}

function ftp_get_connect_id(array $server_data, bool $reconnect)
{
	global $GLOBAL_FTP_SERVERS;

	$key = "storage_$server_data[server_id]";
	if ($reconnect || !isset($GLOBAL_FTP_SERVERS[$key]))
	{
		if (isset($GLOBAL_FTP_SERVERS[$key]))
		{
			debug_server('closed connection on reconnect', $server_data);
			@ftp_close($GLOBAL_FTP_SERVERS[$key]);
			unset($GLOBAL_FTP_SERVERS[$key]);
		}

		if ($reconnect)
		{
			debug_server('reconnecting to server...', $server_data);
		} else
		{
			debug_server('connecting to server...', $server_data);
		}

		$ftp_host = $server_data['ftp_host'];
		$ftp_port = intval($server_data['ftp_port']) > 0 ? intval($server_data['ftp_port']) : 21;
		$ftp_timeout = intval($server_data['ftp_timeout']) > 0 ? intval($server_data['ftp_timeout']) : 10;

		$conn_id = @ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
		if (!$conn_id)
		{
			debug_server("ERROR: failed to establish connection to $ftp_host:$ftp_port", $server_data);
			return null;
		}

		if (@ftp_login($conn_id, $server_data['ftp_user'], $server_data['ftp_pass']))
		{
			debug_server('logged in', $server_data);
			if (!@ftp_pasv($conn_id, true))
			{
				debug_server('WARN: failed to turn passive mode on', $server_data);
			}
			$GLOBAL_FTP_SERVERS[$key] = $conn_id;
			return $conn_id;
		} else
		{
			debug_server("ERROR: failed to log in as $server_data[ftp_user]", $server_data);
		}
		return null;
	}
	return $GLOBAL_FTP_SERVERS[$key];
}

function debug_server(string $message, array $server_data): void
{
	if ($server_data['is_logging_enabled'] == 1 && $server_data['server_id'] > 0)
	{
		$process_id = getmypid();
		file_put_contents("$_SERVER[PWD]/debug_storage_server_$server_data[server_id].txt", date('[Y-m-d H:i:s]') . " [$process_id] $message\n", LOCK_EX | FILE_APPEND);
	} elseif ($server_data['is_logging_enabled'] == 0 && $server_data['server_id'] > 0)
	{
		if (is_file("$_SERVER[PWD]/debug_storage_server_$server_data[server_id].txt"))
		{
			@unlink("$_SERVER[PWD]/debug_storage_server_$server_data[server_id].txt");
		}
	}
}

function disconnect_all_servers(): void
{
	global $GLOBAL_FTP_SERVERS;

	foreach ($GLOBAL_FTP_SERVERS as $k => $conn_id)
	{
		@ftp_close($conn_id);
		unset($GLOBAL_FTP_SERVERS[$k]);
	}
}

function mkdir_recursive($dir, $permissions = 0777)
{
	if (is_dir($dir))
	{
		if (is_writable($dir))
		{
			@chmod($dir, $permissions);
			return true;
		} else
		{
			return false;
		}
	}

	$parent_dir = dirname($dir);
	if (!is_dir($parent_dir))
	{
		if (!mkdir_recursive($parent_dir, $permissions))
		{
			return false;
		}
	}
	@mkdir($dir, $permissions);
	@chmod($dir, $permissions);

	return is_dir($dir) && is_writable($dir);
}

function save_file_from_url(string $url, string $file_path): void
{
	global $api_version;

	$ch = curl_init($url);
	$fp = fopen($file_path, "w");
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, "KVS/$api_version");
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
	curl_setopt($ch, CURLOPT_TIMEOUT, 9999);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);

	if (!is_file($file_path) || sprintf("%.0f", filesize($file_path)) == 0)
	{
		sleep(5);
		$ch = curl_init($url);
		$fp = fopen($file_path, "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_USERAGENT, "KVS/$api_version");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
		curl_setopt($ch, CURLOPT_TIMEOUT, 9999);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
	clearstatcache();
}

function rmdir_recursive(string $src): void
{
	$dir = opendir($src);
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file != '.' && $file != '..')
		{
			if (is_dir("$src/$file"))
			{
				rmdir_recursive("$src/$file");
			} else
			{
				unlink("$src/$file");
			}
		}
	}
	closedir($dir);
	rmdir($src);
}

function get_dir_by_id(int $id): int
{
	return floor($id / 1000) * 1000;
}

function log_console(?array $lines, bool $force_command_log = false, string $level = 'DEBUG'): void
{
	global $is_debug_enabled;

	if (!$is_debug_enabled && !$force_command_log)
	{
		return;
	}

	if (!is_array($lines) || count($lines) == 0)
	{
		log_output($level, 'no response');
		return;
	}
	if (true)
	{
		if (count($lines) > 201)
		{
			$splice_length = count($lines) - 200;
			array_splice($lines, 100, $splice_length, ["removed $splice_length lines"]);
		}
		foreach ($lines as $k => $v)
		{
			if (stripos(trim($v), 'frame=') === 0)
			{
				$frame_lines = explode("\r", trim($v));
				$frame_lines_str = $v;
				if (count($frame_lines) > 1)
				{
					$first_frame_line = $frame_lines[0];
					$last_frame_line = $frame_lines[count($frame_lines) - 1];
					$frame_lines_str = $first_frame_line;
					$frame_index = 1;
					$max_frame_index = count($frame_lines) / 10 + 1;
					foreach ($frame_lines as $frame_line)
					{
						if (stripos(trim($frame_line), 'frame=') === 0)
						{
							if ($frame_index >= $max_frame_index)
							{
								$frame_lines_str .= "\n....$frame_line";
								$frame_index = 0;
							}
							$frame_index++;
						} else
						{
							$frame_lines_str .= "\n....$frame_line";
						}
					}
					$frame_lines_str .= "\n....$last_frame_line";
				}
				$lines[$k] = $frame_lines_str;
			}
			if (stripos(trim($v), 'buffer underflow') !== false)
			{
				unset($lines[$k]);
			}
			if (stripos(trim($v), 'packet too large') !== false)
			{
				unset($lines[$k]);
			}
			if (stripos(trim($v), 'Last message repeated') !== false)
			{
				unset($lines[$k]);
			}
		}
		log_output($level, implode("\n....", $lines));
	} else
	{
		log_output($level, implode("\n....", $lines));
	}
}

function log_info(string $info): void
{
	log_output($info ? 'INFO ' : '', $info);
}

function log_warning(string $warning): void
{
	log_output('WARN ', $warning);
}

function log_error(string $error, string $last_command = '', ?array $last_command_output = null, bool $force_command_log = false): void
{
	global $is_debug_enabled;

	if (!$is_debug_enabled || $force_command_log)
	{
		if ($last_command)
		{
			log_output('ERROR', $last_command);
		}
		if ($last_command_output)
		{
			log_console($last_command_output, true, 'ERROR');
		}
		log_output('ERROR', $error);
	} elseif ($last_command)
	{
		log_output('ERROR', $error);
	}
}

function log_command(string $command): void
{
	global $is_debug_enabled;

	if ($is_debug_enabled)
	{
		log_output('DEBUG', $command);
	}
}

function log_output(string $level, string $message): void
{
	global $time_offset, $task_id, $logging_padding_level;

	file_put_contents("$_SERVER[PWD]/last_activity.dat", time(), LOCK_EX);

	if (!$level && !$message)
	{
		echo "$message\n";
		file_put_contents("$_SERVER[PWD]/log.txt", "$message\n", LOCK_EX | FILE_APPEND);
		return;
	}

	$message_code = '';
	$message_code_search = strpos($message, '[PH-C');
	if ($message_code_search !== false)
	{
		$message_code = substr($message, $message_code_search + 1, strpos($message, ']', $message_code_search) - $message_code_search - 1);
		$logging_padding_level = max(0, substr_count($message_code, '-') - 2);
	}

	$padding = '';
	for ($i = 0; $i < $logging_padding_level; $i++)
	{
		$padding = "$padding....";
	}

	$message = date("[Y-m-d H:i:s] ", time() - $time_offset * 3600) . "$level $padding{$message}";

	echo "$message\n";
	file_put_contents("$_SERVER[PWD]/log.txt", "$message\n", LOCK_EX | FILE_APPEND);

	if ($task_id > 0)
	{
		file_put_contents("$_SERVER[PWD]/$task_id/log.txt", "$message\n", LOCK_EX | FILE_APPEND);
	}

	if ($message_code && $message_code != 'PH-CE')
	{
		$logging_padding_level++;
	}
}