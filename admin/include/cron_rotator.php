<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'] <> '')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_servers.php";
require_once "functions_screenshots.php";
require_once "functions.php";
require_once "pclzip.lib.php";

if (!is_file("$config[project_path]/admin/data/system/cron_rotator.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_rotator.lock", "1", LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_rotator.lock", "r+");
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors', 1);
sql("set low_priority_updates=1");

log_output("INFO  Rotator processor started");

$options = get_options();

$now_time = explode(':', date("H:i"));
$now_time = intval($now_time[0]) * 3600 + intval($now_time[1]) * 60;

$pause_start_time = 0;
$pause_end_time = 0;
if ($options['ROTATOR_SCHEDULE_PAUSE_FROM'] != '')
{
	$temp = explode(":", $options['ROTATOR_SCHEDULE_PAUSE_FROM']);
	if (count($temp) == 2)
	{
		$pause_start_time = intval($temp[0]) * 3600 + intval($temp[1]) * 60;
	}
}
if ($options['ROTATOR_SCHEDULE_PAUSE_TO'] != '')
{
	$temp = explode(":", $options['ROTATOR_SCHEDULE_PAUSE_TO']);
	if (count($temp) == 2)
	{
		$pause_end_time = intval($temp[0]) * 3600 + intval($temp[1]) * 60;
	}
}

if ($pause_start_time > 0 && $pause_end_time > 0)
{
	$pause_rotator = true;
	if ($now_time < $pause_start_time || $now_time > $pause_end_time)
	{
		$pause_rotator = false;
		if ($pause_start_time > $pause_end_time)
		{
			if (($now_time > $pause_start_time && $now_time < 86400) || $now_time < $pause_end_time)
			{
				$pause_rotator = true;
			}
		}
	}
	if ($pause_rotator)
	{
		log_output("INFO  Rotator is paused");
		@unlink("$config[project_path]/admin/data/engine/rotator/videos/views.dat");
		@unlink("$config[project_path]/admin/data/engine/rotator/videos/clicks.dat");
		die;
	}
}

$working_file = "$config[project_path]/admin/data/engine/rotator/videos/working.dat";

$result = array();
$result_categories = array();
$result_tags = array();
$result_pos_matrix = array();
$result_page_matrix = array();

// process clicks
$file_descriptor = null;
$file = "$config[project_path]/admin/data/engine/rotator/videos/clicks.dat";
if (is_file($file))
{
	if (intval($options['ROTATOR_VIDEOS_ENABLE']) == 1)
	{
		if (rename($file, $working_file))
		{
			$file_descriptor = fopen($working_file, 'r');
		} else
		{
			log_output("ERROR Failed to rename clicks.dat");
			unlink($file);
		}
	} else
	{
		log_output("INFO  Videos rotator is disabled");
		unlink($file);
	}
}

$clicks_count = 0;
while ($file_descriptor && !feof($file_descriptor))
{
	$line = fgets($file_descriptor);
	if (!$line)
	{
		continue;
	}

	[$unused, $place_num, $matrix_key, $screen, $video_id, $page_num, $context] = array_map('trim', explode(':', $line, 7));
	$place_num = intval($place_num);
	$screen = intval($screen);
	$video_id = intval($video_id);
	$page_num = intval($page_num);

	if ($place_num == 0)
	{
		continue;
	}
	if (strlen($matrix_key) == 0)
	{
		continue;
	}

	if ($page_num == 0)
	{
		$page_num = 1;
	}
	if ($page_num > 10)
	{
		$page_num = 10;
	}

	if (!is_array($result_pos_matrix[$matrix_key]))
	{
		if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/$matrix_key.dat"))
		{
			$result_pos_matrix[$matrix_key] = @unserialize(@file_get_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/$matrix_key.dat"));
		}
	}
	if (!is_array($result_page_matrix[$matrix_key]))
	{
		if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"))
		{
			$result_page_matrix[$matrix_key] = @unserialize(@file_get_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"));
		}
	}

	if (!is_array($result_pos_matrix[$matrix_key]))
	{
		$result_pos_matrix[$matrix_key] = array();
		$result_pos_matrix[$matrix_key][0] = 0;
	}
	if (isset($result_pos_matrix[$matrix_key][$place_num]))
	{
		$result_pos_matrix[$matrix_key][$place_num] += 1;
	} else
	{
		$result_pos_matrix[$matrix_key][$place_num] = 1;
	}
	$result_pos_matrix[$matrix_key][0] += 1;

	if (!is_array($result_page_matrix[$matrix_key]))
	{
		$result_page_matrix[$matrix_key] = array();
		$result_page_matrix[$matrix_key][0] = 0;
	}
	if (isset($result_page_matrix[$matrix_key][$page_num]))
	{
		$result_page_matrix[$matrix_key][$page_num] += 1;
	} else
	{
		$result_page_matrix[$matrix_key][$page_num] = 1;
	}
	$result_page_matrix[$matrix_key][0] += 1;

	$click_weight = $result_pos_matrix[$matrix_key][$place_num] / $result_pos_matrix[$matrix_key][0];
	if ($click_weight < 0.01)
	{
		$click_weight = 0.01;
	}
	$click_weight = sqrt(1 / $click_weight);

	if (!is_array($result[$video_id]))
	{
		$result[$video_id] = array();
		$result[$video_id]['video_id'] = $video_id;
		$result[$video_id]['r_dlist'] = 0;
		$result[$video_id]['r_ccount'] = 1;
		$result[$video_id]['r_cweight'] = $click_weight;
		$result[$video_id]['rs_dlist'] = 0;
		if ($screen > 0)
		{
			$result[$video_id]['rs_ccount'] = 1;
		} else
		{
			$result[$video_id]['rs_ccount'] = 0;
		}
		if ($video_id > 0)
		{
			$dir_path = get_dir_by_id($video_id);
			if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"))
			{
				$result[$video_id]['screenshots'] = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"));
			}
		}
		if (!is_array($result[$video_id]['screenshots']))
		{
			$result[$video_id]['screenshots'] = array();
		}
	} else
	{
		$result[$video_id]['r_ccount'] += 1;
		$result[$video_id]['r_cweight'] += $click_weight;
		if ($screen > 0)
		{
			$result[$video_id]['rs_ccount'] += 1;
		}
	}
	if ($screen > 0)
	{
		if (!isset($result[$video_id]['screenshots'][$screen]))
		{
			$new_weight = $click_weight;
			$new_clicks = 1;
		} else
		{
			$temp = explode("|", $result[$video_id]['screenshots'][$screen]);
			$new_weight = intval($temp[0]) + $click_weight;
			$new_clicks = intval($temp[1]) + 1;
		}
		$result[$video_id]['screenshots'][$screen] = "$new_weight|$new_clicks";
	}

	if (strpos($context, 'cat') === 0)
	{
		if (intval($options['ROTATOR_VIDEOS_CATEGORIES_ENABLE']) == 1)
		{
			$category_id = intval(substr($context, 3));
			if (!is_array($result_categories[$category_id]))
			{
				$result_categories[$category_id] = array();
			}
			if (!is_array($result_categories[$category_id][$video_id]))
			{
				$result_categories[$category_id][$video_id] = array();
				$result_categories[$category_id][$video_id]['cr_dlist'] = 0;
				$result_categories[$category_id][$video_id]['cr_ccount'] = 1;
				$result_categories[$category_id][$video_id]['cr_cweight'] = $click_weight;
			} else
			{
				$result_categories[$category_id][$video_id]['cr_ccount'] += 1;
				$result_categories[$category_id][$video_id]['cr_cweight'] += $click_weight;
			}
		}
	} elseif (strpos($context, 'tag') === 0)
	{
		if (intval($options['ROTATOR_VIDEOS_TAGS_ENABLE']) == 1)
		{
			$tag_id = intval(substr($context, 3));
			if (!is_array($result_tags[$tag_id]))
			{
				$result_tags[$tag_id] = array();
			}
			if (!is_array($result_tags[$tag_id][$video_id]))
			{
				$result_tags[$tag_id][$video_id] = array();
				$result_tags[$tag_id][$video_id]['cr_dlist'] = 0;
				$result_tags[$tag_id][$video_id]['cr_ccount'] = 1;
				$result_tags[$tag_id][$video_id]['cr_cweight'] = $click_weight;
			} else
			{
				$result_tags[$tag_id][$video_id]['cr_ccount'] += 1;
				$result_tags[$tag_id][$video_id]['cr_cweight'] += $click_weight;
			}
		}
	}
	$clicks_count++;
}
if ($file_descriptor)
{
	fclose($file_descriptor);
}
@unlink($working_file);
log_output("INFO  Processed $clicks_count clicks");


// process views
$file_descriptor = null;
$file = "$config[project_path]/admin/data/engine/rotator/videos/views.dat";
if (is_file($file))
{
	if (intval($options['ROTATOR_VIDEOS_ENABLE']) == 1)
	{
		if (rename($file, $working_file))
		{
			$file_descriptor = fopen($working_file, 'r');
		} else
		{
			log_output("ERROR Failed to rename views.dat");
			unlink($file);
		}
	} else
	{
		log_output("INFO  Videos rotator is disabled");
		unlink($file);
	}
}

$views_count = 0;
while ($file_descriptor && !feof($file_descriptor))
{
	$line = fgets($file_descriptor);
	if (!$line)
	{
		continue;
	}
	[$videos_list, $videos_context, $view_mode] = array_map('trim', explode('|', $line, 3));

	$res = array_map('trim', explode(',', $videos_list));
	foreach ($res as $video_id)
	{
		if (intval($video_id) == 0)
		{
			continue;
		}
		if (!is_array($result[$video_id]))
		{
			$result[$video_id] = array();
			$result[$video_id]['r_dlist'] = 1;
			$result[$video_id]['r_ccount'] = 0;
			$result[$video_id]['r_cweight'] = 0;
			if (intval($view_mode) > 0)
			{
				$result[$video_id]['rs_dlist'] = 1;
			} else
			{
				$result[$video_id]['rs_dlist'] = 0;
			}
			$result[$video_id]['rs_ccount'] = 0;
		} else
		{
			$result[$video_id]['r_dlist']++;
			if (intval($view_mode) > 0)
			{
				$result[$video_id]['rs_dlist']++;
			}
		}

		if (strpos($videos_context, 'cat') === 0)
		{
			if (intval($options['ROTATOR_VIDEOS_CATEGORIES_ENABLE']) == 1)
			{
				$category_id = intval(substr($videos_context, 3));
				if (!is_array($result_categories[$category_id]))
				{
					$result_categories[$category_id] = array();
				}
				if (!is_array($result_categories[$category_id][$video_id]))
				{
					$result_categories[$category_id][$video_id] = array();
					$result_categories[$category_id][$video_id]['cr_dlist'] = 1;
					$result_categories[$category_id][$video_id]['cr_ccount'] = 0;
					$result_categories[$category_id][$video_id]['cr_cweight'] = 0;
				} else
				{
					$result_categories[$category_id][$video_id]['cr_dlist']++;
				}
			}
		} elseif (strpos($videos_context, 'tag') === 0)
		{
			if (intval($options['ROTATOR_VIDEOS_TAGS_ENABLE']) == 1)
			{
				$tag_id = intval(substr($videos_context, 3));
				if (!is_array($result_tags[$tag_id]))
				{
					$result_tags[$tag_id] = array();
				}
				if (!is_array($result_tags[$tag_id][$video_id]))
				{
					$result_tags[$tag_id][$video_id] = array();
					$result_tags[$tag_id][$video_id]['cr_dlist'] = 1;
					$result_tags[$tag_id][$video_id]['cr_ccount'] = 0;
					$result_tags[$tag_id][$video_id]['cr_cweight'] = 0;
				} else
				{
					$result_tags[$tag_id][$video_id]['cr_dlist']++;
				}
			}
		}
		$views_count++;
	}
}
if ($file_descriptor)
{
	fclose($file_descriptor);
}
@unlink($working_file);
log_output("INFO  Processed $views_count views");


// update matrix
if (!is_dir("$config[project_path]/admin/data/engine/rotator"))
{
	mkdir("$config[project_path]/admin/data/engine/rotator");
	chmod("$config[project_path]/admin/data/engine/rotator", 0777);
}
if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos"))
{
	mkdir("$config[project_path]/admin/data/engine/rotator/videos");
	chmod("$config[project_path]/admin/data/engine/rotator/videos", 0777);
}
if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos/matrix"))
{
	mkdir("$config[project_path]/admin/data/engine/rotator/videos/matrix");
	chmod("$config[project_path]/admin/data/engine/rotator/videos/matrix", 0777);
}
foreach ($result_pos_matrix as $k => $v)
{
	if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/$k.dat"))
	{
		unlink("$config[project_path]/admin/data/engine/rotator/videos/matrix/$k.dat");
	}
	file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/$k.dat", serialize($v), LOCK_EX);
}
foreach ($result_page_matrix as $k => $v)
{
	if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$k}_page.dat"))
	{
		unlink("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$k}_page.dat");
	}
	file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$k}_page.dat", serialize($v), LOCK_EX);
}
log_output("INFO  Updated click distribution matrices");


// update videos
foreach ($result as $k => $v)
{
	$inc_update_temp_main = '';
	if ($v['video_id'] > 0 && !isset($v['screenshots'][0]))
	{
		$video_id = $v['video_id'];
		$dir_path = get_dir_by_id($video_id);
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"))
		{
			unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat");
		} else
		{
			if (!is_dir("$config[content_path_videos_sources]/$dir_path"))
			{
				mkdir("$config[content_path_videos_sources]/$dir_path");
				chmod("$config[content_path_videos_sources]/$dir_path", 0777);
			}
			if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id"))
			{
				mkdir("$config[content_path_videos_sources]/$dir_path/$video_id");
				chmod("$config[content_path_videos_sources]/$dir_path/$video_id", 0777);
			}
			if (!is_dir("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots"))
			{
				mkdir("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots");
				chmod("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots", 0777);
			}
		}
		file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat", serialize($v['screenshots']), LOCK_EX);

		$max_k = 1;
		$max_v = 0;
		foreach ($v['screenshots'] as $k2 => $v2)
		{
			if ($v2 > $max_v)
			{
				$max_v = $v2;
				$max_k = $k2;
			}
		}
		$inc_update_temp_main = "screen_main_temp=$max_k,";
	}
	sql_pr("update $config[tables_prefix]videos set $inc_update_temp_main r_dlist=r_dlist+?, r_ccount=r_ccount+?, r_cweight=r_cweight+?, rs_dlist=rs_dlist+?, rs_ccount=rs_ccount+? where video_id=?", $v['r_dlist'], $v['r_ccount'], $v['r_cweight'], $v['rs_dlist'], $v['rs_ccount'], $k);
	usleep(2000);
}
if (count($result) > 0)
{
	sql_pr("update $config[tables_prefix]videos set r_ctr=r_cweight/(r_dlist+1)");
}
log_output("INFO  Updated videos");


// update categories
if (count($result_categories) > 0)
{
	foreach ($result_categories as $category_id => $video)
	{
		foreach ($video as $video_id => $stats)
		{
			sql_pr("update $config[tables_prefix]categories_videos set cr_dlist=cr_dlist+?, cr_ccount=cr_ccount+?, cr_cweight=cr_cweight+? where category_id=? and video_id=?", $stats['cr_dlist'], $stats['cr_ccount'], $stats['cr_cweight'], $category_id, $video_id);
			usleep(1000);
		}
	}

	sql_pr("update $config[tables_prefix]categories_videos set cr_ctr=cr_cweight/(cr_dlist+1)");
	log_output("INFO  Updated video categories");
}


// update tags
if (count($result_tags) > 0)
{
	foreach ($result_tags as $tag_id => $video)
	{
		foreach ($video as $video_id => $stats)
		{
			sql_pr("update $config[tables_prefix]tags_videos set cr_dlist=cr_dlist+?, cr_ccount=cr_ccount+?, cr_cweight=cr_cweight+? where tag_id=? and video_id=?", $stats['cr_dlist'], $stats['cr_ccount'], $stats['cr_cweight'], $tag_id, $video_id);
			usleep(1000);
		}
	}

	sql_pr("update $config[tables_prefix]tags_videos set cr_ctr=cr_cweight/(cr_dlist+1)");
	log_output("INFO  Updated video tags");
}

$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));

if (intval($options['ROTATOR_SCREENSHOTS_ENABLE']) == 1)
{
	if (!is_dir("$config[project_path]/admin/data/engine/rotator"))
	{
		mkdir("$config[project_path]/admin/data/engine/rotator");
		chmod("$config[project_path]/admin/data/engine/rotator", 0777);
	}
	if (!is_dir("$config[project_path]/admin/data/engine/rotator/trash"))
	{
		mkdir("$config[project_path]/admin/data/engine/rotator/trash");
		chmod("$config[project_path]/admin/data/engine/rotator/trash", 0777);
	}

	$min_shows = intval($options['ROTATOR_SCREENSHOTS_MIN_SHOWS']);
	$min_clicks = intval($options['ROTATOR_SCREENSHOTS_MIN_CLICKS']);
	if ($min_shows > 0 && $min_clicks > 0)
	{
		$videos = mr2array(sql("select video_id, screen_amount, screen_main, file_formats from $config[tables_prefix]videos where rs_dlist>=$min_shows and rs_ccount>=$min_clicks and rs_completed=0"));
		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$dir_path = get_dir_by_id($video_id);
			$screen_amount = $video['screen_amount'];
			$main = $video['screen_main'];

			log_video("", $video_id);
			log_video("INFO  Finishing screenshots rotation for video $video_id", $video_id);

			$rotator_data = @unserialize(@file_get_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat"));
			if (intval(@count($rotator_data)) > 0)
			{
				if (isset($rotator_data[0]))
				{
					unset($rotator_data[0]);
				}
				$str = '';
				foreach ($rotator_data as $k => $v)
				{
					$str .= "screenshot #$k: $v, ";
				}
				log_video("INFO  Screenshots rotator weights are: $str", $video_id);
			}

			$scr_weights = array();
			$scr_numbers = array();
			if (intval(@count($rotator_data)) > 0)
			{
				for ($i = 0; $i < $screen_amount; $i++)
				{
					$i1 = $i + 1;
					if (!isset($rotator_data[$i1]))
					{
						$rotator_data[$i1] = "0|0";
					}
					$temp = explode("|", $rotator_data[$i1]);
					$scr_weights[] = floatval($temp[0]);
					$scr_numbers[] = $i1;
				}
			}
			array_multisort($scr_weights, SORT_NUMERIC, SORT_DESC, $scr_numbers);

			if (intval($options['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']) > 0 && $screen_amount > intval($options['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']))
			{
				$i = 0;
				foreach ($scr_weights as $k => $v)
				{
					$screen = $scr_numbers[$k];
					if ($i == 0)
					{
						$main = $screen;
					}
					$i++;
					if ($i <= intval($options['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']))
					{
						log_video("INFO  Keeping overview screenshot #{$screen} with weight $v by rotator", $video_id);
						continue;
					}
					log_video("INFO  Removing overview screenshot #{$screen} with weight $v by rotator", $video_id);
					@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$screen.jpg");
					foreach ($list_formats_overview as $format)
					{
						@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$screen.jpg");
					}
					if (isset($rotator_data[$screen]))
					{
						unset($rotator_data[$screen]);
					}
				}

				$last_index = 0;
				for ($i = 1; $i <= $screen_amount; $i++)
				{
					if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg"))
					{
						if ($last_index == $i - 1)
						{
							$last_index++;
						} else
						{
							$last_index++;
							if ($i == $main)
							{
								$main = $last_index;
							}
							rename("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$last_index.jpg");
							foreach ($list_formats_overview as $format)
							{
								rename("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$last_index.jpg");
							}
							if (isset($rotator_data[$i]))
							{
								$rotator_data[$last_index] = $rotator_data[$i];
								unset($rotator_data[$i]);
							}
						}
					}
				}
				for ($i = 1; $i <= $screen_amount; $i++)
				{
					if (!is_file("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg"))
					{
						copy("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$main.jpg", "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
						foreach ($list_formats_overview as $format)
						{
							copy("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$main.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
						}
					}
				}

				file_put_contents("$config[project_path]/admin/data/engine/rotator/trash/video_$video_id.dat", "$video_id|$screen_amount", LOCK_EX);

				$screen_amount = intval($options['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']);
				foreach ($list_formats_overview as $format)
				{
					if ($format['is_create_zip'] == 1)
					{
						log_video("INFO  Replacing screenshots ZIP for \"$format[title]\" format", $video_id);
						$source_folder = "$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]";
						@unlink("$source_folder/$video_id-$format[size].zip");

						$zip_files_to_add = array();
						for ($i = 1; $i <= $screen_amount; $i++)
						{
							$zip_files_to_add[] = "$source_folder/$i.jpg";
						}
						$zip = new PclZip("$source_folder/$video_id-$format[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
					}
				}
			} else
			{
				$i = 0;
				foreach ($scr_weights as $k => $v)
				{
					$screen = $scr_numbers[$k];
					if ($i == 0)
					{
						$main = $screen;
						break;
					}
				}
			}

			$rotator_data[0] = 'finished';
			file_put_contents("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat", serialize($rotator_data), LOCK_EX);

			$video_formats = get_video_formats($video_id, $video['file_formats']);
			copy("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$main.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg");
			foreach ($video_formats as $format)
			{
				resize_image('need_size_no_composite', "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$video_id/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
			}

			sql_pr("update $config[tables_prefix]videos set screen_amount=?, screen_main=?, screen_main_temp=0, rs_completed=1 where video_id=?", $screen_amount, $main, $video_id);
			log_video("INFO  Screenshots rotation is finished, main screenshot is set to #$main", $video_id);
			usleep(2000);
		}

		$videos_count = count($videos);
		if ($videos_count > 0)
		{
			log_output("INFO  Finished screenshots rotation for $videos_count videos");
		}
	}
}
log_output("INFO  Updated video screenshots");


// process trash
if (is_dir("$config[project_path]/admin/data/engine/rotator/trash"))
{
	$data = scandir("$config[project_path]/admin/data/engine/rotator/trash");
	foreach ($data as $file)
	{
		if (is_file("$config[project_path]/admin/data/engine/rotator/trash/$file") && time() - filectime("$config[project_path]/admin/data/engine/rotator/trash/$file") > 3 * 86400)
		{
			$temp = explode("|", file_get_contents("$config[project_path]/admin/data/engine/rotator/trash/$file"));
			$video_id = intval($temp[0]);
			$count = intval($temp[1]);
			if ($video_id > 0 && $count > 0)
			{
				$dir_path = get_dir_by_id($video_id);
				$new_count = mr2number(sql_pr("select screen_amount from $config[tables_prefix]videos where video_id=?", $video_id));
				if ($new_count > 0)
				{
					for ($i = $new_count + 1; $i <= $count; $i++)
					{
						@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$i.jpg");
						foreach ($list_formats_overview as $format)
						{
							@unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/$format[size]/$i.jpg");
						}
					}
				}
			}
			unlink("$config[project_path]/admin/data/engine/rotator/trash/$file");
			usleep(2000);
		}
	}
	log_output("INFO  Removed trash files");
}

if (is_dir("$config[project_path]/admin/data/engine/rotator/videos"))
{
	//remove old artifacts
	$data = scandir("$config[project_path]/admin/data/engine/rotator/videos");
	foreach ($data as $file)
	{
		if (strpos($file, 'views_') === 0)
		{
			unlink("$config[project_path]/admin/data/engine/rotator/videos/$file");
		}
	}
}

$top_videos = mr2array(sql_pr("select video_id, title, 100 * r_ctr as r_ctr from $config[tables_prefix]videos order by r_ctr desc limit 10"));

$top_videos_str = '';
foreach ($top_videos as $video)
{
	$top_videos_str .= str_pad($video['video_id'], 10) . ' ' . str_pad(number_format($video['r_ctr'], 2), 8) . ' ' . $video['title'] . "\n";
}

log_output("");
log_output("INFO  Top 10 videos by CTR:\n$top_videos_str");

flock($lock, LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message == '')
	{
		echo "\n";
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
	}
}