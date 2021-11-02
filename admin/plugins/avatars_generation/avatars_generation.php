<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function avatars_generationInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/avatars_generation");
}

function avatars_generationIsEnabled()
{
	global $config;

	avatars_generationInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/avatars_generation";
	return is_file("$plugin_path/cron.dat");
}

function avatars_generationShow()
{
	global $config, $lang, $errors, $page_name;

	avatars_generationInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/avatars_generation";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		$log_file = "plugins/avatars_generation.txt";
		header("Content-Type: text/plain; charset=utf8");
		$log_size = sprintf("%.0f", filesize("$config[project_path]/admin/logs/$log_file"));
		if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
		{
			$fh = fopen("$config[project_path]/admin/logs/$log_file", "r");
			fseek($fh, $log_size - 1024 * 1024);
			header("Content-Length: " . (1024 * 1024 + 29));
			echo "Showing last 1MB of file...\n\n";
			echo fread($fh, 1024 * 1024 + 1);
		} else
		{
			if (isset($_REQUEST['download']))
			{
				header("Content-Disposition: attachment; filename=\"$log_file\"");
			}
			header("Content-Length: $log_size");
			readfile("$config[project_path]/admin/logs/$log_file");
		}
		die;
	} elseif ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['is_enabled']) > 0)
		{
			validate_field('empty_int', $_POST['interval'], $lang['plugins']['avatars_generation']['field_schedule']);
		}

		if (validate_field('empty', $_POST['im_options'], $lang['plugins']['avatars_generation']['field_im_options']))
		{
			if (strpos($_POST['im_options'], '%INPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['plugins']['avatars_generation']['field_im_options'], '%INPUT_FILE%');
			} elseif (strpos($_POST['im_options'], '%OUTPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['plugins']['avatars_generation']['field_im_options'], '%OUTPUT_FILE%');
			} elseif (strpos($_POST['im_options'], '%SIZE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['plugins']['avatars_generation']['field_im_options'], '%SIZE%');
			}
		}

		if (!$_POST['crop_options_left'])
		{
			$_POST['crop_options_left'] = '0';
		}
		if (!$_POST['crop_options_top'])
		{
			$_POST['crop_options_top'] = '0';
		}
		if (!$_POST['crop_options_right'])
		{
			$_POST['crop_options_right'] = '0';
		}
		if (!$_POST['crop_options_bottom'])
		{
			$_POST['crop_options_bottom'] = '0';
		}

		$crop_ok = 1;
		if ($_POST['crop_options_left'] != '0')
		{
			$crop_ok = validate_field('empty_int', $_POST['crop_options_left'], $lang['plugins']['avatars_generation']['field_crop_options']);
		}
		if ($_POST['crop_options_top'] != '0' && $crop_ok == 1)
		{
			$crop_ok = validate_field('empty_int', $_POST['crop_options_top'], $lang['plugins']['avatars_generation']['field_crop_options']);
		}
		if ($_POST['crop_options_right'] != '0' && $crop_ok == 1)
		{
			$crop_ok = validate_field('empty_int', $_POST['crop_options_right'], $lang['plugins']['avatars_generation']['field_crop_options']);
		}
		if ($_POST['crop_options_bottom'] != '0' && $crop_ok == 1)
		{
			validate_field('empty_int', $_POST['crop_options_bottom'], $lang['plugins']['avatars_generation']['field_crop_options']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			$save_data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
			$save_data['is_enabled'] = intval($_POST['is_enabled']);
			$save_data['interval'] = intval($_POST['interval']);
			$save_data['tod'] = intval($_POST['tod']);
			$save_data['sort_by'] = $_POST['sort_by'];
			$save_data['sort_by_albums'] = $_POST['sort_by_albums'];
			$save_data['im_options'] = $_POST['im_options'];
			$save_data['crop_options_left'] = intval($_POST['crop_options_left']);
			$save_data['crop_options_left_unit'] = intval($_POST['crop_options_left_unit']);
			$save_data['crop_options_top'] = intval($_POST['crop_options_top']);
			$save_data['crop_options_top_unit'] = intval($_POST['crop_options_top_unit']);
			$save_data['crop_options_right'] = intval($_POST['crop_options_right']);
			$save_data['crop_options_right_unit'] = intval($_POST['crop_options_right_unit']);
			$save_data['crop_options_bottom'] = intval($_POST['crop_options_bottom']);
			$save_data['crop_options_bottom_unit'] = intval($_POST['crop_options_bottom_unit']);

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			if (intval($_POST['is_enabled']) > 0)
			{
				if (!is_file("$plugin_path/cron.dat") || $save_data['tod'] > 0)
				{
					$current_hour = date('H');
					if ($save_data['tod'] == 0)
					{
						$next_date = time();
					} elseif ($current_hour < $save_data['tod'] - 1)
					{
						$next_date = strtotime(date('Y-m-d ') . ($save_data['tod'] - 1) . ':00:00');
					} else
					{
						$next_date = strtotime(date('Y-m-d ') . ($save_data['tod'] - 1) . ':00:00') + 86400;
					}
					file_put_contents("$plugin_path/cron.dat", $next_date, LOCK_EX);
				}
			} else
			{
				@unlink("$plugin_path/cron.dat");
			}

			if (isset($_POST['save_regenerate']))
			{
				@unlink("$plugin_path/sources.dat");
				file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
			}

			return_ajax_success("$page_name?plugin_id=avatars_generation");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST = [];
		$_POST['is_enabled'] = 0;
		$_POST['interval'] = 8;
		$_POST['tod'] = 0;
		$_POST['sort_by'] = 'popularity_day';
		$_POST['sort_by_albums'] = 'popularity_day';
		$_POST['im_options'] = '-enhance -strip -unsharp 1.0x1.0+0.5 -unsharp 1.0x1.0+0.5 -modulate 110,102,100 -unsharp 1.0x1.0+0.5 -contrast -gamma 1.2 -resize %SIZE% %INPUT_FILE% -filter Lanczos -filter Blackman -quality 80 %OUTPUT_FILE%';
		$_POST['crop_options_left_unit'] = 1;
		$_POST['crop_options_top_unit'] = 1;
		$_POST['crop_options_right_unit'] = 1;
		$_POST['crop_options_bottom_unit'] = 1;
		$_POST['last_exec_date'] = '0000-00-00 00:00:00';
		$_POST['duration'] = '0';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	}

	$_POST['next_exec_date'] = '0000-00-00 00:00:00';
	if (is_file("$plugin_path/cron.dat"))
	{
		$_POST['next_exec_date'] = date("Y-m-d H:i:s", file_get_contents("$plugin_path/cron.dat"));
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat")));
	}
}

function avatars_generationCron()
{
	global $config;

	$start = time();

	require_once('setup.php');
	require_once('functions_base.php');
	require_once('functions_servers.php');
	require_once('functions_screenshots.php');

	$plugin_path = "$config[project_path]/admin/data/plugins/avatars_generation";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data) || $data['is_enabled'] == 0)
	{
		return;
	}

	avatars_generationLog("Started");

	$options = get_options(['CATEGORY_AVATAR_SIZE', 'CATEGORY_AVATAR_TYPE', 'CATEGORY_AVATAR_2_SIZE', 'CATEGORY_AVATAR_2_TYPE', 'CATEGORY_AVATAR_OPTION', 'GLOBAL_CONVERTATION_PRIORITY']);
	foreach ($options as $k => $v)
	{
		avatars_generationLog("$k = $v");
	}

	$category_sizes_hash = md5("$options[CATEGORY_AVATAR_SIZE]|$options[CATEGORY_AVATAR_TYPE]|$options[CATEGORY_AVATAR_2_SIZE]|$options[CATEGORY_AVATAR_2_TYPE]|$options[CATEGORY_AVATAR_OPTION]");

	$generated_from = @unserialize(@file_get_contents("$plugin_path/sources.dat"));
	if (!is_array($generated_from) || $data['last_category_avatar_size'] != $category_sizes_hash)
	{
		$generated_from = [];
	}

	$aspect_ratio_id = 2;
	switch ($options['CATEGORY_AVATAR_TYPE'])
	{
		case 'max_size':
			$aspect_ratio_id = 3;
			break;
		case 'max_width':
			$aspect_ratio_id = 5;
			break;
		case 'max_height':
			$aspect_ratio_id = 4;
			break;
	}
	$avatar_format1 = [
			'format_screenshot_id' => 'na',
			'size' => $options['CATEGORY_AVATAR_SIZE'],
			'aspect_ratio_id' => $aspect_ratio_id,
			'im_options' => $data['im_options']
	];

	$aspect_ratio_id = 2;
	switch ($options['CATEGORY_AVATAR_2_TYPE'])
	{
		case 'max_size':
			$aspect_ratio_id = 3;
			break;
		case 'max_width':
			$aspect_ratio_id = 5;
			break;
		case 'max_height':
			$aspect_ratio_id = 4;
			break;
	}
	$avatar_format2 = [
			'format_screenshot_id' => 'na',
			'size' => $options['CATEGORY_AVATAR_2_SIZE'],
			'aspect_ratio_id' => $aspect_ratio_id,
			'im_options' => $data['im_options']
	];

	$custom_crop_options = [
			intval($data['crop_options_left']) . ($data['crop_options_left_unit'] == 1 ? '' : '%'),
			intval($data['crop_options_top']) . ($data['crop_options_top_unit'] == 1 ? '' : '%'),
			intval($data['crop_options_right']) . ($data['crop_options_right_unit'] == 1 ? '' : '%'),
			intval($data['crop_options_bottom']) . ($data['crop_options_bottom_unit'] == 1 ? '' : '%'),
	];
	$custom_crop_options = implode(',', $custom_crop_options);

	if ($data['is_enabled'] == 1)
	{
		if ($data['sort_by'] == 'rating_day')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_week')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_month')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_all')
		{
			$sort_by = "rating/rating_amount desc, rating_amount desc";
		} elseif ($data['sort_by'] == 'popularity_day')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_week')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_month')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_all')
		{
			$sort_by = "video_viewed desc";
		} elseif ($data['sort_by'] == 'most_favourited')
		{
			$sort_by = "favourites_count desc";
		} elseif ($data['sort_by'] == 'most_commented')
		{
			$sort_by = "comments_count desc";
		} elseif ($data['sort_by'] == 'ctr')
		{
			$sort_by = "r_ctr desc";
		} else
		{
			$sort_by = "post_date desc, video_id desc";
		}

		$rotator_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));

		$now_date = date("Y-m-d H:i:s");
		$selected_video_ids = [0];
		$categories = mr2array(sql_pr("select * from $config[tables_prefix]categories order by total_videos"));
		foreach ($categories as $category)
		{
			$category_id = $category['category_id'];
			$not_clause = "video_id not in (" . implode(',', $selected_video_ids) . ")";

			if ($data['sort_by'] == 'ctr' && $rotator_params['ROTATOR_VIDEOS_CATEGORIES_ENABLE'] == 1)
			{
				$sort_by = "(select cr_ctr from $config[tables_prefix]categories_videos where video_id=$config[tables_prefix]videos.video_id and category_id=$category_id) desc";
			}

			$res = mr2array_single(sql_pr("select video_id, screen_main, screen_main_temp from $config[tables_prefix]videos where $not_clause and status_id=1 and relative_post_date>=0 and post_date<=? and exists (select category_id from $config[tables_prefix]categories_videos where video_id=$config[tables_prefix]videos.video_id and category_id=?) order by $sort_by limit 1", $now_date, $category_id));
			if (intval($res['video_id']) == 0)
			{
				$res = mr2array_single(sql_pr("select video_id, screen_main, screen_main_temp from $config[tables_prefix]videos where status_id=1 and relative_post_date>=0 and post_date<=? and exists (select category_id from $config[tables_prefix]categories_videos where video_id=$config[tables_prefix]videos.video_id and category_id=?) order by $sort_by limit 1", $now_date, $category_id));
			}
			if (intval($res['video_id']) > 0)
			{
				$video_id = intval($res['video_id']);
				$screen_main = intval($res['screen_main']);
				if ($rotator_params['ROTATOR_SCREENSHOTS_ENABLE'] == 1 && intval($res['screen_main_temp']) > 0)
				{
					$screen_main = intval($res['screen_main_temp']);
				}
				if ($video_id > 0 && $screen_main > 0)
				{
					if ($generated_from[$category_id] == "video$video_id")
					{
						avatars_generationLog("Skipped category \"$category[title]\" avatars based on video #$video_id");
					} else
					{
						avatars_generationLog("Generating category \"$category[title]\" avatars based on video #$video_id");
						$dir_path = get_dir_by_id($video_id);

						$screen_path = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/$screen_main.jpg";
						if (is_file($screen_path))
						{
							$target_dir = "$config[content_path_categories]/$category_id";
							if (!mkdir_recursive($target_dir))
							{
								avatars_generationLog("[ERROR] Directory creation failed: $target_dir");
								continue;
							}

							avatars_generationLog("Generating screenshot #1");
							$target_file = "s1_$category_id.jpg";
							$exec_str = make_image_from_source($screen_path, "$target_dir/$target_file", $avatar_format1, ['GLOBAL_CONVERTATION_PRIORITY' => $options['GLOBAL_CONVERTATION_PRIORITY']], $custom_crop_options);
							if ($exec_str)
							{
								avatars_generationLog("[ERROR] ImageMagick operation failed: $exec_str");
								continue;
							}
							sql_update("update $config[tables_prefix]categories set screenshot1=? where category_id=?", $target_file, $category_id);

							if (intval($options['CATEGORY_AVATAR_OPTION']) == 1)
							{
								avatars_generationLog("Generating screenshot #2");
								$target_file = "s2_$category_id.jpg";
								$exec_str = make_image_from_source($screen_path, "$target_dir/$target_file", $avatar_format2, ['GLOBAL_CONVERTATION_PRIORITY' => $options['GLOBAL_CONVERTATION_PRIORITY']], $custom_crop_options);
								if ($exec_str)
								{
									avatars_generationLog("[ERROR] ImageMagick operation failed: $exec_str");
									continue;
								}
								sql_update("update $config[tables_prefix]categories set screenshot2=? where category_id=?", $target_file, $category_id);
							}

							$generated_from[$category_id] = "video$video_id";
						} else
						{
							avatars_generationLog("[ERROR] No screenshot source file available: $screen_path");
						}
					}
				}
				$selected_video_ids[] = $video_id;
			}
		}
	} else
	{
		if ($data['sort_by'] == 'rating_day')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_week')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_month')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'rating_all')
		{
			$sort_by = "rating/rating_amount desc, rating_amount desc";
		} elseif ($data['sort_by'] == 'popularity_day')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_week')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 6, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_month')
		{
			$date_from = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 30, date("Y")));
			$date_to = date("Y-m-d");
			$sort_by = "(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($data['sort_by'] == 'popularity_all')
		{
			$sort_by = "album_viewed desc";
		} elseif ($data['sort_by'] == 'most_favourited')
		{
			$sort_by = "favourites_count desc";
		} elseif ($data['sort_by'] == 'most_commented')
		{
			$sort_by = "comments_count desc";
		} elseif ($data['sort_by'] == 'ctr')
		{
			$sort_by = "r_ctr desc";
		} else
		{
			$sort_by = "post_date desc, album_id desc";
		}

		$now_date = date("Y-m-d H:i:s");
		$selected_album_ids = [0];
		$categories = mr2array(sql_pr("select * from $config[tables_prefix]categories order by total_albums"));
		foreach ($categories as $category)
		{
			$category_id = $category['category_id'];
			$not_clause = "album_id not in (" . implode(',', $selected_album_ids) . ")";
			$res = mr2array_single(sql_pr("select album_id, main_photo_id, server_group_id from $config[tables_prefix]albums where $not_clause and status_id=1 and relative_post_date>=0 and post_date<=? and exists (select category_id from $config[tables_prefix]categories_albums where album_id=$config[tables_prefix]albums.album_id and category_id=?) order by $sort_by limit 1", $now_date, $category_id));
			if (intval($res['album_id']) == 0)
			{
				$res = mr2array_single(sql_pr("select album_id, main_photo_id, server_group_id from $config[tables_prefix]albums where status_id=1 and relative_post_date>=0 and post_date<=? and exists (select category_id from $config[tables_prefix]categories_albums where album_id=$config[tables_prefix]albums.album_id and category_id=?) order by $sort_by limit 1", $now_date, $category_id));
			}
			if (intval($res['album_id']) > 0)
			{
				$album_id = intval($res['album_id']);
				$main_photo_id = intval($res['main_photo_id']);
				$server_group_id = intval($res['server_group_id']);
				if ($album_id > 0 && $main_photo_id > 0 && $server_group_id > 0)
				{
					if ($generated_from[$category_id] == "album$album_id")
					{
						avatars_generationLog("Skipped category \"$category[title]\" avatars based on album #$album_id");
					} else
					{
						avatars_generationLog("Generating category \"$category[title]\" avatars based on album #$album_id");
						$dir_path = get_dir_by_id($album_id);

						$rnd = mt_rand(1000000, 9999999);
						if (!mkdir_recursive("$config[temporary_path]/$rnd"))
						{
							avatars_generationLog("[ERROR] Directory creation failed: $config[temporary_path]/$rnd");
							continue;
						}

						$storage_servers = mr2array(sql("select * from $config[tables_prefix]admin_servers where group_id=$server_group_id"));
						foreach ($storage_servers as $server)
						{
							if (get_file("$main_photo_id.jpg", "sources/$dir_path/$album_id", "$config[temporary_path]/$rnd", $server))
							{
								break;
							}
						}

						$image_path = "$config[temporary_path]/$rnd/$main_photo_id.jpg";
						if (is_file($image_path))
						{
							$target_dir = "$config[content_path_categories]/$category_id";
							if (!mkdir_recursive($target_dir))
							{
								avatars_generationLog("[ERROR] Directory creation failed: $target_dir");
								continue;
							}

							avatars_generationLog("Generating screenshot #1");
							$target_file = "s1_$category_id.jpg";
							$exec_str = make_image_from_source($image_path, "$target_dir/$target_file", $avatar_format1, ['GLOBAL_CONVERTATION_PRIORITY' => $options['GLOBAL_CONVERTATION_PRIORITY']], $custom_crop_options);
							if ($exec_str)
							{
								avatars_generationLog("[ERROR] ImageMagick operation failed: $exec_str");
								continue;
							}
							sql_update("update $config[tables_prefix]categories set screenshot1=? where category_id=?", $target_file, $category_id);

							if (intval($options['CATEGORY_AVATAR_OPTION']) == 1)
							{
								avatars_generationLog("Generating screenshot #2");
								$target_file = "s2_$category_id.jpg";
								$exec_str = make_image_from_source($image_path, "$target_dir/$target_file", $avatar_format2, ['GLOBAL_CONVERTATION_PRIORITY' => $options['GLOBAL_CONVERTATION_PRIORITY']], $custom_crop_options);
								if ($exec_str)
								{
									avatars_generationLog("[ERROR] ImageMagick operation failed: $exec_str");
									continue;
								}
								sql_update("update $config[tables_prefix]categories set screenshot2=? where category_id=?", $target_file, $category_id);
							}

							$generated_from[$category_id] = "album$album_id";
						} else
						{
							avatars_generationLog("[ERROR] No image source file available on storage servers: $main_photo_id.jpg");
						}
						rmdir_recursive("$config[temporary_path]/$rnd");
					}
				}
				$selected_album_ids[] = $album_id;
			}
		}
	}

	file_put_contents("$plugin_path/sources.dat", serialize($generated_from), LOCK_EX);

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (is_array($data))
	{
		$data['last_exec_date'] = $start;
		$data['last_category_avatar_size'] = $category_sizes_hash;
		$data['duration'] = time() - $start;
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

		$next_date = $start + $data['interval'] * 60 * 60;
		if ($data['tod'] > 0)
		{
			$next_hour = date('H', $next_date);
			if ($next_hour < $data['tod'])
			{
				$next_date = strtotime(date('Y-m-d ', $next_date) . ($data['tod'] - 1) . ':00:00');
			} else
			{
				$next_date = strtotime(date('Y-m-d ', $next_date) . ($data['tod'] - 1) . ':00:00') + 86400;
			}
		}
		file_put_contents("$plugin_path/cron.dat", $next_date, LOCK_EX);
	}

	$duration = time() - $start;
	avatars_generationLog("Finished in $duration seconds");
}

function avatars_generationLog($message)
{
	global $config;

	echo date("[Y-m-d H:i:s] ") . $message . "\n";
	file_put_contents("$config[project_path]/admin/logs/plugins/avatars_generation.txt", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
