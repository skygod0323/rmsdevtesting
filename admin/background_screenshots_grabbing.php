<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'] <> '')
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_servers.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';

$grabbing_id = intval($_SERVER['argv'][1]);

if ($grabbing_id < 1 || !is_file("$config[temporary_path]/grabbing-$grabbing_id/task.dat"))
{
	log_pc('error1');
	die;
}
$data = @unserialize(file_get_contents("$config[temporary_path]/grabbing-$grabbing_id/task.dat"), ['allowed_classes' => false]);
if (!is_array($data))
{
	log_pc('error2');
	die;
}

$video_id = $data['video_id'];
$method = $data['method'];
$source_file_id = $data['source_file_id'];
$interval = $data['interval'];
$offset = $data['screenshots_offset'];
$slow_method = $data['slow_method'];
$display_size = $data['display_size'];
if ($display_size <> '')
{
	$display_size = explode("x", $display_size);
}

$result = sql("select * from $config[tables_prefix]videos where video_id=$video_id");
if (mr2rows($result) > 0)
{
	$data_video = mr2array_single($result);
} else
{
	log_pc('error3');
	die;
}

$output_dir = "$config[content_path_videos_screenshots]/temp/$grabbing_id";
if (!mkdir_recursive("$config[content_path_videos_screenshots]/temp/$grabbing_id"))
{
	log_pc('error14');
	die;
}

$options = get_options();

if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
{
	$priority = intval($options['GLOBAL_CONVERTATION_PRIORITY']);
	$priority_prefix = "nice -n $priority ";
}

$pc = 0;
$thumbs_count = 0;
$dir_path = get_dir_by_id($video_id);
if ($method == 1)
{
	// use timeline screenshots
	$list_formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
	$video_formats = get_video_formats($video_id, $data_video['file_formats']);

	$timeline_directory = '';
	foreach ($list_formats_videos as $format)
	{
		if ($format['format_video_id'] == $source_file_id)
		{
			foreach ($video_formats as $format_rec)
			{
				if ($format_rec['postfix'] == $format['postfix'])
				{
					$timeline_directory = $format['timeline_directory'];
					$thumbs_count = $format_rec['timeline_screen_amount'];
					break;
				}
			}
			break;
		}
	}
	if ($thumbs_count == 0 || $timeline_directory == '')
	{
		log_pc('error4');
		die;
	}
	for ($i = 1; $i <= $thumbs_count; $i++)
	{
		copy("$config[content_path_videos_sources]/$dir_path/$video_id/timelines/$timeline_directory/$i.jpg", "$output_dir/result.jpg");
		apply_crop("$output_dir/result.jpg", "$output_dir/$i.jpg", $data);
		if (is_array($display_size))
		{
			$img_size = getimagesize("$output_dir/$i.jpg");
			if ($img_size[0] > $display_size[0])
			{
				$img_size[1] = ceil($display_size[0] * $img_size[1] / $img_size[0]);
				$img_size[0] = $display_size[0];
				resize_image('need_size', "$output_dir/$i.jpg", "$output_dir/{$i}r.jpg", "$img_size[0]x$img_size[1]");
			}
		}
		unlink("$output_dir/result.jpg");
		$pc += 100 / $thumbs_count;
		log_pc($pc);
	}
} else
{
	// create new screenshots
	if ($source_file_id == 0)
	{
		$source_file = "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp";
		if (!is_file($source_file))
		{
			if ($data_video['load_type_id'] == 2 || $data_video['load_type_id'] == 3)
			{
				$source_file = "$config[temporary_path]/grabbing-$grabbing_id/source.tmp";
				save_file_from_url($data_video['file_url'], $source_file, "");
				if (!is_file($source_file) || get_video_duration($source_file) < 1)
				{
					log_pc('error5');
					die;
				}
			} else
			{
				log_pc('error6');
				die;
			}
		}
	} else
	{
		$list_formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
		foreach ($list_formats_videos as $format)
		{
			if ($format['format_video_id'] == $source_file_id)
			{
				$data_format = $format;
				break;
			}
		}
		if (!isset($data_format))
		{
			log_pc('error7');
			die;
		}
		$source_file = "$config[temporary_path]/grabbing-$grabbing_id/source.tmp";
		$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers where group_id=$data_video[server_group_id]"));
		foreach ($storage_servers as $server)
		{
			if (get_file("$data_video[video_id]{$data_format['postfix']}", "$dir_path/$data_video[video_id]", "$config[temporary_path]/grabbing-$grabbing_id", $server))
			{
				rename("$config[temporary_path]/grabbing-$grabbing_id/$data_video[video_id]{$data_format['postfix']}", $source_file);
				break;
			}
		}
		if (!is_file($source_file) || get_video_duration($source_file) < 1)
		{
			log_pc('error8');
			die;
		}
	}
	if (!is_file($source_file))
	{
		log_pc('error9');
		die;
	}
	$source_file_duration = get_video_duration($source_file);
	if ($source_file_duration < 1)
	{
		log_pc('error10');
		die;
	}

	$pc = 10;
	log_pc($pc);

	$step = $interval;
	if ($step < 1)
	{
		$step = 1;
	}
	$step_target = intval($offset);
	$thumbs_count = floor(($source_file_duration - 1 - $step_target) / $step) + 1;

	$i_thumb = 0;
	for ($is = 0; $is < 99999; $is++)
	{
		if ($step_target > $source_file_duration - 1)
		{
			break;
		}
		$exec_str = "{$priority_prefix}$config[ffmpeg_path] -ss $step_target -i $source_file -vframes 1 -y -f mjpeg -qscale 1 $output_dir/result.jpg 2>&1";
		exec($exec_str);

		if (!is_file("$output_dir/result.jpg") || !analyze_screenshot("$output_dir/result.jpg"))
		{
			$exec_str = "{$priority_prefix}$config[ffmpeg_path] -i $source_file -ss $step_target -vframes 1 -y -f mjpeg -qscale 1 $output_dir/result.jpg 2>&1";
			exec($exec_str);
		}

		if (is_file("$output_dir/result.jpg"))
		{
			$i_thumb++;
			apply_crop("$output_dir/result.jpg", "$output_dir/$i_thumb.jpg", $data);
			if (is_array($display_size))
			{
				$img_size = getimagesize("$output_dir/$i_thumb.jpg");
				if ($img_size[0] > $display_size[0])
				{
					$img_size[1] = ceil($display_size[0] * $img_size[1] / $img_size[0]);
					$img_size[0] = $display_size[0];
					resize_image('need_size', "$output_dir/$i_thumb.jpg", "$output_dir/{$i_thumb}r.jpg", "$img_size[0]x$img_size[1]");
					if (!is_file("$output_dir/{$i_thumb}r.jpg"))
					{
						log_pc('error11');
						die;
					}
				}
			}
			if (!is_file("$output_dir/$i_thumb.jpg"))
			{
				log_pc('error12');
				die;
			}
			unlink("$output_dir/result.jpg");
		} else
		{
			log_pc('error13');
			die;
		}
		$step_target += $step;

		$pc += 90 / $thumbs_count;
		log_pc($pc);
	}
}

if ($pc < 100)
{
	log_pc(100);
}

@unlink("$config[temporary_path]/grabbing-$grabbing_id/source.tmp");
@unlink("$config[temporary_path]/grabbing-$grabbing_id/task.dat");

function log_pc($pc)
{
	global $config, $grabbing_id;

	file_put_contents("$config[temporary_path]/grabbing-$grabbing_id/progress.dat", "$pc", LOCK_EX);
}

function apply_crop($input_file, $output_file, $crop_data)
{
	global $config, $priority_prefix;

	if (intval($crop_data['screenshots_crop_trim']) == 1)
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] $input_file -fuzz 7% -trim $input_file";
		exec($exec_str);
	}

	$img_size = getimagesize($input_file);

	if ($crop_data['screenshots_crop_left_unit'] == 1)
	{
		$screenshots_crop_left = intval($crop_data['screenshots_crop_left']);
	} else
	{
		$screenshots_crop_left = intval($crop_data['screenshots_crop_left'] / 100 * $img_size[0]);
	}
	if ($crop_data['screenshots_crop_right_unit'] == 1)
	{
		$screenshots_crop_right = intval($crop_data['screenshots_crop_right']);
	} else
	{
		$screenshots_crop_right = intval($crop_data['screenshots_crop_right'] / 100 * $img_size[0]);
	}
	if ($crop_data['screenshots_crop_top_unit'] == 1)
	{
		$screenshots_crop_top = intval($crop_data['screenshots_crop_top']);
	} else
	{
		$screenshots_crop_top = intval($crop_data['screenshots_crop_top'] / 100 * $img_size[1]);
	}
	if ($crop_data['screenshots_crop_bottom_unit'] == 1)
	{
		$screenshots_crop_bottom = intval($crop_data['screenshots_crop_bottom']);
	} else
	{
		$screenshots_crop_bottom = intval($crop_data['screenshots_crop_bottom'] / 100 * $img_size[1]);
	}
	if ($screenshots_crop_left + $screenshots_crop_right + $screenshots_crop_top + $screenshots_crop_bottom > 0)
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] -crop +$screenshots_crop_left+$screenshots_crop_top -crop -$screenshots_crop_right-$screenshots_crop_bottom $input_file $output_file";
		exec($exec_str);
	} else
	{
		copy($input_file, $output_file);
	}
}

function analyze_screenshot($screenshot)
{
	global $slow_method;

	if ($slow_method == 0)
	{
		return true;
	}

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
