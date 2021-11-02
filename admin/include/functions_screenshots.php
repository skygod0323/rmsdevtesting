<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function process_screen_source(string $input_file, array $options, bool $is_uploaded_manually, ?string $custom_crop): string
{
	global $config;

	if ($is_uploaded_manually && $options['SCREENSHOTS_UPLOADED_CROP'] == 0)
	{
		return '';
	}

	$priority_prefix = '';
	if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
	{
		$priority = intval($options['GLOBAL_CONVERTATION_PRIORITY']);
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
		$jpeg_quality = intval($config['imagemagick_default_jpeg_quality']);
		if ($jpeg_quality < 80)
		{
			$jpeg_quality = 80;
		}

		$exec_str = "{$priority_prefix}$config[image_magick_path] $input_file -quality $jpeg_quality $crop_options $input_file";

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
	global $config;

	$priority_prefix = '';
	if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
	{
		$priority = intval($options['GLOBAL_CONVERTATION_PRIORITY']);
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

	$rnd = mt_rand(1000000, 9999999);
	$output_temp_file = "$config[temporary_path]/$rnd.bmp";
	$exec_str = "{$priority_prefix}$config[image_magick_path] " . str_replace("%SIZE%", "$resize_size[0]x$resize_size[1]", str_replace("%INPUT_FILE%", $input_file, str_replace("%OUTPUT_FILE%", $output_temp_file, $format['im_options'])));

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_temp_file) || filesize($output_temp_file) == 0)
	{
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str: $res";
	}

	$jpeg_quality = intval($config['imagemagick_default_jpeg_quality']);
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
		$watermark_path = "$config[project_path]/admin/data/other/watermark_screen_{$format['format_screenshot_id']}.png";
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
		$rnd = mt_rand(10000000, 99999999);
		$background_image = "$config[temporary_path]/$rnd.bmp";
		$exec_str = "{$priority_prefix}$config[image_magick_path] -resize $required_size[0]x$required_size[1]^ -gravity center -extent $required_size[0]x$required_size[1] $output_temp_file -blur 0x6 -modulate 100,60 $background_image";

		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($background_image) || filesize($background_image) == 0)
		{
			@unlink($output_temp_file);
			if (is_array($res))
			{
				$res = $res[0];
			}
			return "$exec_str: $res";
		}
	}

	if ($format['image_type'] == 1)
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $webp_artifacts webp:$output_file";
	} else
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $jpeg_artifacts $output_file";
	}

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
		return "$exec_str: $res";
	}

	@unlink($output_temp_file);
	@unlink($background_image);
	return '';
}

function make_image_from_source(string $input_file, string $output_file, array $format, array $options, ?string $custom_crop): string
{
	global $config;

	$priority_prefix = '';
	if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
	{
		$priority = intval($options['GLOBAL_CONVERTATION_PRIORITY']);
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

	$rnd = mt_rand(1000000, 9999999);
	$output_temp_file = "$config[temporary_path]/$rnd.bmp";
	$exec_str = "{$priority_prefix}$config[image_magick_path] $crop_options " . str_replace("%SIZE%", "$resize_size[0]x$resize_size[1]", str_replace("%INPUT_FILE%", $input_file, str_replace("%OUTPUT_FILE%", $output_temp_file, $format['im_options'])));

	unset($res);
	exec("$exec_str 2>&1", $res);
	if (!is_file($output_temp_file) || filesize($output_temp_file) == 0)
	{
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str: $res";
	}

	$jpeg_quality = intval($config['imagemagick_default_jpeg_quality']);
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

	$watermark_path = "$config[project_path]/admin/data/other/watermark_album_{$format['format_album_id']}.png";
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
			$watermark_new_path = "$config[temporary_path]/watermark_$rnd.png";

			unset($res);
			$exec_str = "{$priority_prefix}$config[image_magick_path] $watermark_path -resize {$watermark_new_width}x{$watermark_new_height} $watermark_new_path 2>&1";
			exec($exec_str, $res);
			if (!is_file($watermark_new_path) || filesize($watermark_new_path) == 0)
			{
				if (is_array($res))
				{
					$res = $res[0];
				}
				return "$exec_str: $res";
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
		$rnd = mt_rand(10000000, 99999999);
		$background_image = "$config[temporary_path]/$rnd.bmp";
		$exec_str = "{$priority_prefix}$config[image_magick_path] -resize $required_size[0]x$required_size[1]^ -gravity center -extent $required_size[0]x$required_size[1] $output_temp_file -blur 0x6 -modulate 100,60 $background_image";

		unset($res);
		exec("$exec_str 2>&1", $res);
		if (!is_file($background_image) || filesize($background_image) == 0)
		{
			@unlink($output_temp_file);
			if (is_array($res))
			{
				$res = $res[0];
			}
			return "$exec_str: $res";
		}
	}

	if ($format['image_type'] == 1)
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $webp_artifacts webp:$output_file";
	} else
	{
		$exec_str = "{$priority_prefix}$config[image_magick_path] -quality $jpeg_quality $advanced_options -size $required_size[0]x$required_size[1] $background_image $output_temp_file -gravity $aspect_ratio_gravity -composite $watermark_options $jpeg_artifacts $output_file";
	}

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
		return "$exec_str: $res";
	}

	@unlink($output_temp_file);
	@unlink($background_image);
	if ($watermark_new_path)
	{
		@unlink($watermark_new_path);
	}
	return '';
}

function correct_orientation($image_path)
{
	global $config, $options;

	$priority_prefix = '';
	if ($options['GLOBAL_CONVERTATION_PRIORITY'] > 0)
	{
		settype($options['GLOBAL_CONVERTATION_PRIORITY'], "integer");
		$priority_prefix = "nice -n $options[GLOBAL_CONVERTATION_PRIORITY] ";
	}
	if ($config['image_magick_identify_path'] != '')
	{
		$identify_path = $config['image_magick_identify_path'];
	} else
	{
		$identify_path = str_replace('convert', 'identify', $config['image_magick_path']);
	}

	unset($res);
	exec("{$priority_prefix}$identify_path -format '%[exif:orientation]' $image_path 2>&1", $res);
	$orientation = intval($res[0]);
	if ($orientation == 8 || $orientation == 3 || $orientation == 6)
	{
		$rnd = mt_rand(1000000, 9999999);
		unset($res);
		$exec_str = "{$priority_prefix}$config[image_magick_path] -auto-orient $image_path $config[temporary_path]/$rnd.jpg 2>&1";
		exec($exec_str, $res);

		if (is_file("$config[temporary_path]/$rnd.jpg") && filesize("$config[temporary_path]/$rnd.jpg") > 0)
		{
			unlink($image_path);
			rename("$config[temporary_path]/$rnd.jpg", $image_path);
			return $orientation;
		}

		@unlink("$config[temporary_path]/$rnd.jpg");
		if (is_array($res))
		{
			$res = $res[0];
		}
		return "$exec_str: $res";
	}

	return 0;
}

function log_video($message, $video_id, $no_date = 0)
{
	global $config;

	if (intval($video_id) > 0)
	{
		if ($message != '')
		{
			if (intval($no_date) == 0)
			{
				$message = date("[Y-m-d H:i:s] ") . $message;
			}
		}
		file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}

function log_album($message, $album_id, $no_date = 0)
{
	global $config;

	if (intval($album_id) > 0)
	{
		if ($message != '')
		{
			if (intval($no_date) == 0)
			{
				$message = date("[Y-m-d H:i:s] ") . $message;
			}
		}
		file_put_contents("$config[project_path]/admin/logs/albums/$album_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}
