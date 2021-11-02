<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function grabbersInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins", 0777);
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path, 0777);
		chmod($plugin_path, 0777);
	}

	if (!is_dir("$plugin_path/storage"))
	{
		mkdir("$plugin_path/storage", 0777);
		chmod("$plugin_path/storage", 0777);
	}

	require_once("$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php");

	$grabber_files = get_contents_from_dir($plugin_path, 1);
	foreach ($grabber_files as $grabber_file)
	{
		if (strtolower(end(explode('.', $grabber_file, 2))) == 'inc' && strpos($grabber_file, 'grabber_') === 0)
		{
			$grabber = require_once("$plugin_path/$grabber_file");
			if ($grabber instanceof KvsGrabber)
			{
				if (!is_file("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat"))
				{
					$grabber_settings = $grabber->create_default_settings();
					if ($grabber_settings)
					{
						file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
					}
				}
			}
		}
	}

	$grabbers_info = array();
	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabbers_info[$grabber->get_grabber_id()] = array(
				'version' => $grabber->get_grabber_version(),
			);
		}
	}
	file_put_contents("$plugin_path/grabbers.dat", serialize($grabbers_info), LOCK_EX);

	if (count(KvsGrabberFactory::get_registered_grabber_classes()) > 0)
	{
		if (!is_file("$plugin_path/cron.dat"))
		{
			file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
		}
	} else
	{
		@unlink("$plugin_path/cron.dat");
	}

	if (!is_file("$plugin_path/ydl.dat"))
	{
		$ydl_binary = "/usr/local/bin/youtube-dl";
		unset($res);
		exec("$ydl_binary 2>&1", $res);
		if (!preg_match("|\[OPTIONS\]|is", trim(implode(" ", $res))))
		{
			$ydl_binary = "/usr/bin/youtube-dl";
			unset($res);
			exec("$ydl_binary 2>&1", $res);
			if (!preg_match("|\[OPTIONS\]|is", trim(implode(" ", $res))))
			{
				$ydl_binary = "";
			}
		}
		if ($ydl_binary)
		{
			unset($res);
			exec("$ydl_binary --version 2>&1", $res);
			$ydl_version = trim(implode(" ", $res));
			file_put_contents("$plugin_path/ydl.dat", serialize(array('ydl_binary' => $ydl_binary, 'ydl_version' => $ydl_version)), LOCK_EX);
		}
	} elseif (time() - filemtime("$plugin_path/ydl.dat") > 600)
	{
		$ydl_info = @unserialize(@file_get_contents("$plugin_path/ydl.dat"));
		if (is_array($ydl_info) && $ydl_info['ydl_binary'])
		{
			$ydl_binary = escapeshellcmd($ydl_info['ydl_binary']);
			if ($ydl_binary)
			{
				unset($res);
				exec("$ydl_binary --version 2>&1", $res);
				$ydl_version = trim(implode(" ", $res));
				file_put_contents("$plugin_path/ydl.dat", serialize(array('ydl_binary' => $ydl_binary, 'ydl_version' => $ydl_version)), LOCK_EX);
			}
		}
	}
}

function grabbersIsEnabled()
{
	global $config;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabber_settings = new KvsGrabberSettings();
			$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
			if ($grabber_settings->is_autopilot())
			{
				return true;
			}
		}
	}

	return false;
}

function grabbersShow()
{
	global $config, $lang, $errors, $page_name;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$errors = null;

	if ($_GET['action'] == 'mass_import_progress')
	{
		header("Content-Type: text/xml");

		$task_id = intval($_GET['task_id']);
		$pc = intval(@file_get_contents("$plugin_path/import/task-progress-$task_id.dat"));

		$location = '';
		if ($pc == 100)
		{
			$location = "<location>plugins.php?plugin_id=grabbers&amp;action=upload_confirm&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>";
		die;
	} elseif ($_GET['action'] == 'grabbers_list')
	{
		header("Content-Type: text/xml; charset=utf-8");
		$kvs_grabbers_info = get_page("", "https://www.kernel-scripts.com/grabbers/list.php?domain=$config[project_licence_domain]", "", "", 1, 0, 20, '');
		if ($kvs_grabbers_info)
		{
			$kvs_grabbers_info = @unserialize($kvs_grabbers_info);
		}
		if (isset($_POST['full_list']))
		{
			$list_grabbers = array();
			if ($kvs_grabbers_info && is_array($kvs_grabbers_info))
			{
				foreach ($kvs_grabbers_info as $grabber_info)
				{
					$list_grabbers[$grabber_info['grabber_type']][] = array(
						'id' => $grabber_info['grabber_id'],
						'title' => $grabber_info['grabber_title'],
						'group_title' => $lang['plugins']['grabbers']['divider_grabbers_' . $grabber_info['grabber_type']]
					);
				}
			}

			$smarty = new mysmarty();
			$smarty->assign('lang', $lang);
			$smarty->assign('data', $list_grabbers);
			$smarty->assign('is_grouped', 1);
			$smarty->display("insight_list.tpl");
		} else
		{
			$result_for = $_POST['for'];
			$result_for = str_replace("&", "&amp;", $result_for);
			$result_for = str_replace(">", "&gt;", $result_for);
			$result_for = str_replace("<", "&lt;", $result_for);
			if ($kvs_grabbers_info && is_array($kvs_grabbers_info) && strlen($_POST['for']) > 1)
			{
				echo "<insight for=\"$result_for\">\n";
				foreach ($kvs_grabbers_info as $grabber_info)
				{
					$id = $grabber_info['grabber_id'];
					$title = str_replace("&", "&amp;", $grabber_info['grabber_title']);
					$title = str_replace(">", "&gt;", $title);
					$title = str_replace("<", "&lt;", $title);

					echo "<value id=\"$id\">$title</value>\n";
				}
				echo "</insight>";
			} else
			{
				echo "<insight for=\"$result_for\"></insight>";
			}
		}
		die;
	}

	if ($_POST['action'] == 'save_grabber')
	{
		$current_grabber = null;
		foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
		{
			$grabber_class = new ReflectionClass($grabber_class);
			$grabber = $grabber_class->newInstance();
			if ($grabber instanceof KvsGrabber && $grabber->get_grabber_id() == $_POST['grabber_id'])
			{
				$current_grabber = $grabber;
				break;
			}
		}

		if ($current_grabber instanceof KvsGrabberVideoYDL)
		{
			$ydl_info = @unserialize(@file_get_contents("$plugin_path/ydl.dat"));
			$ydl_version = trim($ydl_info['ydl_version']);
			if (!$ydl_version)
			{
				$errors[] = $lang['plugins']['grabbers']['error_grabber_noydl'];
			}
		}

		validate_field('empty', $_POST['grabber_id'], $lang['plugins']['grabbers']['field_grabber_id']);
		validate_field('empty', $_POST['mode'], $lang['plugins']['grabbers']['field_mode']);

		if ($current_grabber instanceof KvsGrabberVideo)
		{
			if ($_POST['mode'] == 'download' && $_POST['quality'] == '*')
			{
				$current_grabber_formats = $current_grabber->get_supported_qualities();
				if (is_array($current_grabber_formats))
				{
					$multiple_formats_selected = false;
					$multiple_formats_selected_map = array();
					foreach ($current_grabber_formats as $current_grabber_format)
					{
						if (isset($_POST["download_format_{$current_grabber_format}"]) && $_POST["download_format_{$current_grabber_format}"] != '')
						{
							$multiple_formats_selected = true;
							$multiple_formats_selected_map[$_POST["download_format_{$current_grabber_format}"]]++;
						}
					}
					if (!$multiple_formats_selected)
					{
						validate_field('empty', '', $lang['plugins']['grabbers']['field_quality']);
					} else {
						foreach ($multiple_formats_selected_map as $multiple_formats_selected_map_count)
						{
							if ($multiple_formats_selected_map_count > 1)
							{
								$errors[] = str_replace("%1%", $lang['plugins']['grabbers']['field_quality'], $lang['plugins']['grabbers']['error_same_formats_multiple_quality']);
							}
						}
					}
				}
			}
		}

		validate_field('empty_int', $_POST['timeout'], $lang['plugins']['grabbers']['field_timeout']);

		if (intval($_POST['is_autopilot']) == 1)
		{
			validate_field('empty_int', $_POST['autopilot_interval'], $lang['plugins']['grabbers']['field_autopilot_interval']);
			if ($_POST['title_limit'] != '')
			{
				validate_field('empty_int', $_POST['title_limit'], $lang['plugins']['grabbers']['field_limit_title']);
			}
			if ($_POST['description_limit'] != '')
			{
				validate_field('empty_int', $_POST['description_limit'], $lang['plugins']['grabbers']['field_limit_description']);
			}
			if (validate_field('empty', $_POST['upload_list'], $lang['plugins']['grabbers']['field_upload_list']))
			{
				if ($current_grabber instanceof KvsGrabber && !$current_grabber->is_default())
				{
					$urls = explode("\n", $_POST['upload_list']);
					foreach ($urls as $url_count_pair)
					{
						$url_count_pair = explode('|', $url_count_pair, 2);
						$url = trim($url_count_pair[0]);
						if (str_replace('www.', '', parse_url($url, PHP_URL_HOST)) != $current_grabber->get_grabber_domain())
						{
							$errors[] = str_replace("%1%", $lang['plugins']['grabbers']['field_upload_list'], str_replace("%2%", $url, $lang['plugins']['grabbers']['error_autopilot_url_not_supported']));
						}
					}
				}
			}
		}

		if ($_POST['grabber_id'] != '' && is_file("$plugin_path/grabber_$_POST[grabber_id].dat"))
		{
			if (!is_writable("$plugin_path/grabber_$_POST[grabber_id].dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/grabber_$_POST[grabber_id].dat"));
			}
		}

		if (!is_array($errors))
		{
			$settings = new KvsGrabberSettings();
			$settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_$_POST[grabber_id].dat")));

			$settings->set_mode($_POST['mode']);
			$settings->set_content_source_id($_POST['content_source_id']);
			$settings->set_quality($_POST['quality']);
			$settings->set_quality_missing($_POST['quality_missing']);
			$settings->set_replacements($_POST['replacements']);
			$settings->set_proxies($_POST['proxies']);
			$settings->set_account($_POST['account']);
			$settings->set_url_postfix($_POST['url_postfix']);
			$settings->set_timeout($_POST['timeout']);
			$settings->set_import_categories_as_tags(intval($_POST["is_import_categories_as_tags"]) == 1);
			if (isset($_POST['download_format']))
			{
				$settings->set_download_format($_POST['download_format']);
			}
			$settings->set_filter_quantity_from(intval($_POST['filter_quantity_from']));
			$settings->set_filter_quantity_to(intval($_POST['filter_quantity_to']));
			$settings->set_filter_rating_from(intval($_POST['filter_rating_from']));
			$settings->set_filter_rating_to(intval($_POST['filter_rating_to']));
			$settings->set_filter_views_from(intval($_POST['filter_views_from']));
			$settings->set_filter_views_to(intval($_POST['filter_views_to']));
			$settings->set_filter_date_from(intval($_POST['filter_date_from']));
			$settings->set_filter_date_to(intval($_POST['filter_date_to']));
			$settings->set_filter_terminology(trim($_POST['filter_terminology']));
			$settings->set_filter_quality_from(trim($_POST['filter_quality_from']));

			$settings->clear_data();

			settype($_POST['data'], "array");
			foreach ($_POST['data'] as $field)
			{
				$settings->add_data($field);
			}

			if ($current_grabber instanceof KvsGrabberVideo)
			{
				if ($_POST['mode'] == 'download' && $_POST['quality'] == '*')
				{
					$settings->clear_download_formats_mapping();

					$current_grabber_formats = $current_grabber->get_supported_qualities();
					if (is_array($current_grabber_formats))
					{
						foreach ($current_grabber_formats as $current_grabber_format)
						{
							if (isset($_POST["download_format_{$current_grabber_format}"]) && $_POST["download_format_{$current_grabber_format}"] != '')
							{
								$settings->add_download_format_mapping($current_grabber_format, $_POST["download_format_{$current_grabber_format}"]);
							}
						}
					}
				}
			}

			$settings->set_autodelete(intval($_POST["is_autodelete"]) == 1);

			$settings->set_autopilot(intval($_POST["is_autopilot"]) == 1);
			$settings->set_autopilot_interval(intval($_POST["autopilot_interval"]));
			$settings->set_autopilot_threads(intval($_POST["threads"]));
			$settings->set_autopilot_title_limit(intval($_POST["title_limit"]));
			$settings->set_autopilot_title_limit_option(intval($_POST["title_limit_type_id"]));
			$settings->set_autopilot_description_limit(intval($_POST["description_limit"]));
			$settings->set_autopilot_description_limit_option(intval($_POST["description_limit_type_id"]));
			$settings->set_autopilot_new_content_disabled(intval($_POST["status_after_import_id"]) == 1);
			$settings->set_autopilot_skip_duplicate_titles(intval($_POST["is_skip_duplicate_titles"]) == 1);
			$settings->set_autopilot_skip_new_categories(intval($_POST["is_skip_new_categories"]) == 1);
			$settings->set_autopilot_skip_new_models(intval($_POST["is_skip_new_models"]) == 1);
			$settings->set_autopilot_skip_new_content_sources(intval($_POST["is_skip_new_content_sources"]) == 1);
			$settings->set_autopilot_skip_new_channels(intval($_POST["is_skip_new_channels"]) == 1);
			$settings->set_autopilot_review_needed(intval($_POST["is_review_needed"]) == 1);
			$settings->set_autopilot_randomize_time(intval($_POST["is_randomize_time"]) == 1);
			$settings->set_autopilot_urls($_POST["upload_list"]);

			file_put_contents("$plugin_path/grabber_$_POST[grabber_id].dat", serialize($settings->to_array()), LOCK_EX);

			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'mass_import')
	{
		if (!KvsGrabberFactory::is_grabbers_installed())
		{
			$errors[] = $lang['plugins']['grabbers']['error_no_grabbers_installed'];
		}

		validate_field('empty', $_POST['upload_type'], $lang['plugins']['grabbers']['field_upload_type']);
		validate_field('empty', $_POST['upload_list'], $lang['plugins']['grabbers']['field_upload_list']);
		if ($_POST['title_limit'] != '')
		{
			validate_field('empty_int', $_POST['title_limit'], $lang['plugins']['grabbers']['field_limit_title']);
		}
		if ($_POST['description_limit'] != '')
		{
			validate_field('empty_int', $_POST['description_limit'], $lang['plugins']['grabbers']['field_limit_description']);
		}

		if (!is_array($errors))
		{
			$rnd = mt_rand(10000000, 99999999);

			if (!is_dir("$plugin_path/import"))
			{
				mkdir("$plugin_path/import", 0777);
				chmod("$plugin_path/import", 0777);
			}

			$data = array();
			$data['threads'] = intval($_POST['threads']);
			$data['status_after_import_id'] = intval($_POST['status_after_import_id']);
			$data['title_limit'] = intval($_POST['title_limit']);
			$data['title_limit_type_id'] = intval($_POST['title_limit_type_id']);
			$data['description_limit'] = intval($_POST['description_limit']);
			$data['description_limit_type_id'] = intval($_POST['description_limit_type_id']);
			$data['is_skip_duplicate_titles'] = intval($_POST['is_skip_duplicate_titles']);
			$data['is_skip_new_categories'] = intval($_POST['is_skip_new_categories']);
			$data['is_skip_new_models'] = intval($_POST['is_skip_new_models']);
			$data['is_skip_new_content_sources'] = intval($_POST['is_skip_new_content_sources']);
			$data['is_skip_new_channels'] = intval($_POST['is_skip_new_channels']);
			$data['is_review_needed'] = intval($_POST['is_review_needed']);
			$data['is_randomize_time'] = intval($_POST['is_randomize_time']);

			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			file_put_contents("$plugin_path/import/$rnd.dat", serialize($_POST), LOCK_EX);

			exec("$config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php mass_import $rnd > $config[project_path]/admin/logs/plugins/grabbers.txt &");
			return_ajax_success("$page_name?plugin_id=grabbers&amp;action=mass_import_progress&amp;task_id=$rnd&amp;rand=\${rand}", 2);
		} else
		{
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action'] == 'mass_import_confirm')
	{
		validate_field('empty_int', $_POST['task_id'], "task_id");

		if (isset($_POST['back_mass_import']))
		{
			$task_id = intval($_POST['task_id']);
			return_ajax_success("$page_name?plugin_id=grabbers&amp;action=back_upload&amp;task_id=$task_id");
		}

		if (!is_array($errors))
		{
			grabbersCreateImport(intval($_POST['task_id']), intval($_SESSION['userdata']['user_id']));
			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
		die;
	} elseif ($_POST['action'] == 'manage_grabbers')
	{
		if (!is_writable("$plugin_path"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path"));
		}

		$ydl_binary = escapeshellcmd(trim($_POST['ydl_binary']));

		if ($ydl_binary)
		{
			unset($res);
			exec("$ydl_binary 2>&1", $res);
			if (!preg_match("|\[OPTIONS\]|is", trim(implode(" ", $res))))
			{
				$errors[] = str_replace("%1%", $lang['plugins']['grabbers']['field_ydl_binary'], $lang['plugins']['grabbers']['error_ydl_path_invalid']);
			}
		}

		$custom_grabber_id = '';
		$temp_grabber_file = '';
		if ($_POST['custom_grabber_hash'] != '')
		{
			$temp_grabber_file = "$config[temporary_path]/$_POST[custom_grabber_hash].tmp";

			unset($res);
			exec("$config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $temp_grabber_file", $res);

			unset($temp);
			if (!preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res)), $temp))
			{
				$errors[] = str_replace('%1%', $lang['plugins']['grabbers']['field_custom_grabber'], $lang['plugins']['grabbers']['error_invalid_grabber_file']);
			} else
			{
				$custom_grabber_id = trim($temp[1]);
			}
		}

		if (!is_array($errors))
		{
			if (is_array($_POST['delete']))
			{
				foreach ($_POST['delete'] as $delete_grabber_id)
				{
					@unlink("$plugin_path/grabber_$delete_grabber_id.inc");
					@unlink("$plugin_path/grabber_$delete_grabber_id.dat");
					@unlink("$plugin_path/grabber_$delete_grabber_id.log");
				}
			}

			if ($temp_grabber_file && is_file($temp_grabber_file) && $custom_grabber_id != '')
			{
				rename($temp_grabber_file, "$plugin_path/grabber_$custom_grabber_id.inc");
			}
			if (is_array($_POST['grabber_ids']))
			{
				foreach ($_POST['grabber_ids'] as $install_grabber_id)
				{
					$install_grabber_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".tmp";
					save_file_from_url("https://www.kernel-scripts.com/grabbers/download.php?domain=$config[project_licence_domain]&grabber_id=$install_grabber_id", $install_grabber_file);

					unset($res);
					exec("$config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $install_grabber_file", $res);
					if (preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res))))
					{
						rename($install_grabber_file, "$plugin_path/grabber_$install_grabber_id.inc");
					}
				}
			}

			if ($ydl_binary)
			{
				unset($res);
				exec("$ydl_binary --version 2>&1", $res);
				$ydl_version = trim(implode(" ", $res));
				file_put_contents("$plugin_path/ydl.dat", serialize(array('ydl_binary' => $ydl_binary, 'ydl_version' => $ydl_version)), LOCK_EX);
			} else
			{
				@unlink("$plugin_path/ydl.dat");
			}
			return_ajax_success("$page_name?plugin_id=grabbers");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$ydl_info = @unserialize(@file_get_contents("$plugin_path/ydl.dat"));
	$ydl_binary = escapeshellcmd($ydl_info['ydl_binary']);
	$ydl_version = trim($ydl_info['ydl_version']);

	if ($_GET['action'] == 'upload' || $_GET['action'] == 'back_upload')
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	}
	if (intval($_GET['task_id']) > 0)
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/import/" . intval($_GET['task_id']) . ".dat"));
		$_POST['task_id'] = intval($_GET['task_id']);
	}

	$_POST['ydl_binary'] = $ydl_binary;
	$_POST['ydl_version'] = $ydl_version;

	foreach (KvsGrabberFactory::get_supported_grabber_types($config['installation_type']) as $grabber_type)
	{
		$_POST['grabbers'][$grabber_type] = array();
	}

	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabber_id = $grabber->get_grabber_id();
			$grabber_info = array(
				"grabber_id" => $grabber->get_grabber_id(),
				"grabber_name" => $grabber->get_grabber_name(),
				"grabber_version" => $grabber->get_grabber_version(),
				"grabber_type" => $grabber->get_grabber_type(),
				"is_default" => $grabber->is_default() ? 1 : 0,
				"is_autodelete_supported" => $grabber->can_autodelete() ? 1 : 0,
				"is_autopilot_supported" => $grabber->can_grab_lists() ? 1 : 0,
				"is_ydl" => $grabber instanceof KvsGrabberVideoYDL ? 1 : 0,
			);

			if (is_file("$plugin_path/grabber_$grabber_id.dat"))
			{
				$grabber_info['settings'] = @unserialize(file_get_contents("$plugin_path/grabber_$grabber_id.dat"));
			}

			$grabber_info['supported_modes'] = $grabber->get_supported_modes();
			$grabber_info['supported_data'] = $grabber->get_supported_data();
			$grabber_info['supported_qualities'] = $grabber->get_supported_qualities();
			if ($grabber instanceof KvsGrabberVideo)
			{
				if ($grabber->get_downloadable_video_format() != '')
				{
					$formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=0"));
					foreach ($formats_videos as $key => $format)
					{
						if (strpos($format['postfix'], '.' . $grabber->get_downloadable_video_format()) === false)
						{
							unset($formats_videos[$key]);
						}
					}
					$grabber_info['supported_video_formats'] = $formats_videos;
				}
			}

			if (is_array($_POST['grabbers'][$grabber->get_grabber_type()]))
			{
				$_POST['grabbers'][$grabber->get_grabber_type()][] = $grabber_info;
			}

			if ($_GET['grabber_id'] == $grabber_id)
			{
				if ($_GET['action'] == 'log')
				{
					header("Content-Type: text/plain; charset=utf8");
					echo @file_get_contents("$plugin_path/grabber_{$grabber_id}.log");
					die;
				}
				$_POST['grabber_info'] = $grabber_info;
				if ($_POST['grabber_info']['settings']['is_broken'] == 1)
				{
					$_POST['errors'][] = bb_code_process($lang['plugins']['grabbers']['error_grabber_broken']);
				}
				if ($grabber instanceof KvsGrabberVideoYDL)
				{
					if (!$ydl_version)
					{
						$_POST['errors'][] = bb_code_process($lang['plugins']['grabbers']['error_grabber_noydl']);
					}
				}
			}
		}
	}

	$list_content_sources = array();
	$temp = mr2array(sql("select content_source_id, $config[tables_prefix]content_sources_groups.content_source_group_id, $config[tables_prefix]content_sources.title, $config[tables_prefix]content_sources_groups.title as content_source_group_title from $config[tables_prefix]content_sources left join $config[tables_prefix]content_sources_groups on $config[tables_prefix]content_sources_groups.content_source_group_id=$config[tables_prefix]content_sources.content_source_group_id order by $config[tables_prefix]content_sources_groups.title asc, $config[tables_prefix]content_sources.title asc"));
	foreach ($temp as $res)
	{
		$list_content_sources[$res['content_source_group_id']][] = $res;
	}
	$_POST['content_sources'] = $list_content_sources;

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}
	if ($_GET['action'] == 'upload' && !KvsGrabberFactory::is_grabbers_installed())
	{
		$_POST['errors'][] = bb_code_process($lang['plugins']['grabbers']['error_no_grabbers_installed']);
	}
	if (!function_exists('dom_import_simplexml'))
	{
		$_POST['errors'][] = bb_code_process($lang['plugins']['grabbers']['error_no_dom_module_installed']);
	}

	if (is_dir("$plugin_path/import") && is_writable("$plugin_path/import"))
	{
		exec("find $plugin_path/import \( -iname \"*.dat\" \) -mtime +1 -delete");
	}
}

function grabbersLog($message, $grabber_id = '')
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	if ($message == '')
	{
		echo "\n";
		file_put_contents("$config[project_path]/admin/logs/plugins/grabbers.txt", "\n", FILE_APPEND | LOCK_EX);
		if ($grabber_id != '')
		{
			file_put_contents("$plugin_path/grabber_$grabber_id.log", "\n", FILE_APPEND | LOCK_EX);
		}
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
		file_put_contents("$config[project_path]/admin/logs/plugins/grabbers.txt", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND | LOCK_EX);
		if ($grabber_id != '')
		{
			file_put_contents("$plugin_path/grabber_$grabber_id.log", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND | LOCK_EX);
		}
	}
}

function grabbersCron()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$installed_grabbers_info = @unserialize(@file_get_contents("$plugin_path/grabbers.dat"));
	if (is_array($installed_grabbers_info) && count($installed_grabbers_info) > 0)
	{
		grabbersLog("Checking for updates");
		$kvs_grabbers_info = get_page("", "https://www.kernel-scripts.com/grabbers/list.php?domain=$config[project_licence_domain]", "", "", 1, 0, 20, '');
		if ($kvs_grabbers_info)
		{
			$kvs_grabbers_info = @unserialize($kvs_grabbers_info);
			if ($kvs_grabbers_info && is_array($kvs_grabbers_info))
			{
				foreach ($kvs_grabbers_info as $grabber_info)
				{
					if (isset($installed_grabbers_info[$grabber_info['grabber_id']]))
					{
						$installed_grabber_info = $installed_grabbers_info[$grabber_info['grabber_id']];
						if (intval($grabber_info['grabber_version']) > intval($installed_grabber_info['version']))
						{
							$install_grabber_id = $grabber_info['grabber_id'];
							$install_grabber_file = "$config[temporary_path]/" . md5($install_grabber_id) . ".tmp";
							save_file_from_url("https://www.kernel-scripts.com/grabbers/download.php?domain=$config[project_licence_domain]&grabber_id=$install_grabber_id", $install_grabber_file);

							unset($res);
							exec("cd $config[project_path]/admin && $config[php_path] $config[project_path]/admin/plugins/grabbers/grabbers.php check_grabber $install_grabber_file", $res);
							if (preg_match("|OKGRABBER\(([A-Za-z0-9_]+)\)|is", trim(implode("", $res))))
							{
								rename($install_grabber_file, "$plugin_path/grabber_$install_grabber_id.inc");
							}
							grabbersLog("Grabber $grabber_info[grabber_id] was updated to version $grabber_info[grabber_version]");
						}

						$settings_file = "$plugin_path/grabber_{$grabber_info['grabber_id']}.dat";
						if (is_file($settings_file))
						{
							$grabber_settings = new KvsGrabberSettings();
							$grabber_settings->from_array(@unserialize(@file_get_contents($settings_file)));
							if ($grabber_info['is_broken'] == 1)
							{
								if (!$grabber_settings->is_broken())
								{
									$grabber_settings->set_broken(true);
									file_put_contents($settings_file, serialize($grabber_settings->to_array()), LOCK_EX);
									grabbersLog("Grabber $grabber_info[grabber_id] was marked as broken");
								}
							} else
							{
								if ($grabber_settings->is_broken())
								{
									$grabber_settings->set_broken(false);
									file_put_contents($settings_file, serialize($grabber_settings->to_array()), LOCK_EX);
									grabbersLog("Grabber $grabber_info[grabber_id] was marked as fixed");
								}
							}
						}
					}
				}
			}
		}
	}

	grabbersInit();

	require_once('functions_base.php');
	require_once('functions.php');

	$grabbers_installed = 0;
	$grabbers_on_autopilot = 0;
	$grabbers_on_autodelete = 0;
	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber = $grabber_class->newInstance();
		if ($grabber instanceof KvsGrabber)
		{
			$grabbers_installed++;
			@unlink("$plugin_path/grabber_{$grabber->get_grabber_id()}.log");
			$grabber_settings = new KvsGrabberSettings();
			$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat")));
			$grabber->init($grabber_settings, "$plugin_path/storage");
			if ($grabber_settings->is_autopilot())
			{
				$grabbers_on_autopilot++;
				if (time() - $grabber_settings->get_autopilot_last_exec_time() > $grabber_settings->get_autopilot_interval() * 3600)
				{
					$start_time = time();
					grabbersLog("Starting {$grabber->get_grabber_id()} grabber", $grabber->get_grabber_id());

					$rnd = mt_rand(10000000, 99999999);

					if (!is_dir("$plugin_path/import"))
					{
						mkdir("$plugin_path/import", 0777);
						chmod("$plugin_path/import", 0777);
					}

					$task_data = array();
					$task_data['upload_type'] = $grabber->get_grabber_type();
					$task_data['upload_list'] = $grabber_settings->get_autopilot_urls();
					$task_data['threads'] = $grabber_settings->get_autopilot_threads();
					$task_data['status_after_import_id'] = $grabber_settings->is_autopilot_new_content_disabled() ? 1 : 0;
					$task_data['title_limit'] = $grabber_settings->get_autopilot_title_limit();
					$task_data['title_limit_type_id'] = $grabber_settings->get_autopilot_title_limit_option();
					$task_data['description_limit'] = $grabber_settings->get_autopilot_description_limit();
					$task_data['description_limit_type_id'] = $grabber_settings->get_autopilot_description_limit_option();
					$task_data['is_skip_duplicate_titles'] = $grabber_settings->is_autopilot_skip_duplicate_titles() ? 1 : 0;
					$task_data['is_skip_new_categories'] = $grabber_settings->is_autopilot_skip_new_categories() ? 1 : 0;
					$task_data['is_skip_new_models'] = $grabber_settings->is_autopilot_skip_new_models() ? 1 : 0;
					$task_data['is_skip_new_content_sources'] = $grabber_settings->is_autopilot_skip_new_content_sources() ? 1 : 0;
					$task_data['is_skip_new_channels'] = $grabber_settings->is_autopilot_skip_new_channels() ? 1 : 0;
					$task_data['is_review_needed'] = $grabber_settings->is_autopilot_review_needed() ? 1 : 0;
					$task_data['is_randomize_time'] = $grabber_settings->is_autopilot_randomize_time() ? 1 : 0;

					file_put_contents("$plugin_path/import/$rnd.dat", serialize($task_data), LOCK_EX);
					grabbersProcessUrls($rnd);

					$new_content = 0;
					$duplicate_content = 0;

					$task_data = @unserialize(@file_get_contents("$plugin_path/import/$rnd.dat"));
					if (is_array($task_data))
					{
						if (is_array($task_data['grabbers_usage']))
						{
							foreach ($task_data['grabbers_usage'] as $grabbers_usage_item)
							{
								if ($grabbers_usage_item['type'] == 'valid')
								{
									grabbersLog("", $grabber->get_grabber_id());
									grabbersLog("New content to grab (" . count($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
									foreach ($grabbers_usage_item['urls'] as $url)
									{
										grabbersLog($url, $grabber->get_grabber_id());
									}
									$new_content += count($grabbers_usage_item['urls']);
								} elseif ($grabbers_usage_item['type'] == 'duplicates')
								{
									grabbersLog("", $grabber->get_grabber_id());
									grabbersLog("Duplicate content (" . count($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
									foreach ($grabbers_usage_item['urls'] as $url)
									{
										grabbersLog($url, $grabber->get_grabber_id());
									}
									$duplicate_content += count($grabbers_usage_item['urls']);
								} elseif ($grabbers_usage_item['type'] == 'missing')
								{
									grabbersLog("", $grabber->get_grabber_id());
									grabbersLog("Invalid URLs (" . count($grabbers_usage_item['urls']) . "):", $grabber->get_grabber_id());
									foreach ($grabbers_usage_item['urls'] as $url)
									{
										grabbersLog($url, $grabber->get_grabber_id());
									}
								}
							}
						} else
						{
							grabbersLog("ERROR: no data about grabbers usage", $grabber->get_grabber_id());
						}
					} else
					{
						grabbersLog("ERROR: failed to process URLs", $grabber->get_grabber_id());
					}
					grabbersLog("", $grabber->get_grabber_id());
					grabbersCreateImport($rnd);

					$grabber_settings->set_autopilot_last_exec_time(time());
					$grabber_settings->set_autopilot_last_exec_duration(time() - $start_time);
					$grabber_settings->set_autopilot_last_exec_added($new_content);
					$grabber_settings->set_autopilot_last_exec_duplicates($duplicate_content);
					file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
				} else
				{
					$next_exec_time = $grabber_settings->get_autopilot_interval() * 3600 - (time() - $grabber_settings->get_autopilot_last_exec_time());
					grabbersLog("Grabber {$grabber->get_grabber_id()} next execution in $next_exec_time seconds", $grabber->get_grabber_id());
				}
			}
			if ($grabber_settings->is_autodelete())
			{
				$grabbers_on_autodelete++;
				if (time() - $grabber_settings->get_autodelete_last_exec_time() > 8 * 3600)
				{
					grabbersLog("Autodelete for {$grabber->get_grabber_id()} grabber", $grabber->get_grabber_id());

					$list_urls_to_be_deleted = $grabber->get_deleted_urls();
					grabbersLog("New videos deleted on source site: " . count($list_urls_to_be_deleted), $grabber->get_grabber_id());

					if (count($list_urls_to_be_deleted) > 100)
					{
						$all_videos = mr2array(sql_pr("select video_id, gallery_url from $config[tables_prefix]videos where gallery_url like ?", '%' . $grabber->get_grabber_domain() . '%'));
						foreach ($all_videos as $video_info)
						{
							foreach ($list_urls_to_be_deleted as $url_to_be_deleted)
							{
								if ($url_to_be_deleted)
								{
									$video_id = 0;
									if ($url_to_be_deleted[0] == '~')
									{
										$url_to_be_deleted = substr($url_to_be_deleted, 1);
										if (strpos($video_info['gallery_url'], $url_to_be_deleted) !== false)
										{
											$video_id = $video_info['video_id'];
										}
									} elseif ($url_to_be_deleted == $video_info['gallery_url'])
									{
										$video_id = $video_info['video_id'];
									}
									if ($video_id > 0)
									{
										grabbersLog("Deleting video $video_id", $grabber->get_grabber_id());

										sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id);
										sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=180, object_id=?, object_type_id=1, added_date=?", $video_id, date("Y-m-d H:i:s"));
										sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize(array()), date("Y-m-d H:i:s"));
									}
								}
							}
						}
					} else
					{
						foreach ($list_urls_to_be_deleted as $url_to_be_deleted)
						{
							if ($url_to_be_deleted && $url_to_be_deleted[0] == '~')
							{
								$url_to_be_deleted = sql_escape(substr($url_to_be_deleted, 1));
								$video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where gallery_url like ? and gallery_url like ? limit 1", '%' . $grabber->get_grabber_domain() . '%', "%$url_to_be_deleted%"));
							} else
							{
								$video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where external_key=?", md5($url_to_be_deleted)));
							}
							if ($video_id > 0)
							{
								grabbersLog("Deleting video $video_id", $grabber->get_grabber_id());

								sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id);
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=0, username='grabbers', action_id=180, object_id=?, object_type_id=1, added_date=?", $video_id, date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize(array()), date("Y-m-d H:i:s"));
							}
						}
					}

					$grabber_settings->set_autodelete_last_exec_time(time());
					file_put_contents("$plugin_path/grabber_{$grabber->get_grabber_id()}.dat", serialize($grabber_settings->to_array()), LOCK_EX);
				} else
				{
					$next_exec_time = 8 * 3600 - (time() - $grabber_settings->get_autodelete_last_exec_time());
					grabbersLog("Grabber {$grabber->get_grabber_id()} next autodelete in $next_exec_time seconds", $grabber->get_grabber_id());
				}
			}
		}
	}
	grabbersLog("Summary: $grabbers_installed grabbers installed; $grabbers_on_autopilot grabbers enabled auto-pilot; $grabbers_on_autodelete grabbers enabled auto-delete");
}

function grabbersFindGrabber($url, $grabber_type)
{
	global $config;

	grabbersInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$grabber_to_use = null;
	$default_grabber = null;

	foreach (KvsGrabberFactory::get_registered_grabber_classes() as $grabber_class)
	{
		$grabber_class = new ReflectionClass($grabber_class);
		$grabber_subclass = 'KvsGrabber';
		if ($grabber_type == KvsGrabberVideo::GRABBER_TYPE_VIDEOS)
		{
			$grabber_subclass = 'KvsGrabberVideo';
		} elseif ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
		{
			$grabber_subclass = 'KvsGrabberAlbum';
		}
		if ($grabber_class->isSubclassOf($grabber_subclass))
		{
			$grabber = $grabber_class->newInstance();
			if ($grabber instanceof KvsGrabber)
			{
				if ($grabber->is_default())
				{
					$default_grabber = $grabber;
				} else
				{
					if (str_replace('www.', '', parse_url($url, PHP_URL_HOST)) == $grabber->get_grabber_domain())
					{
						$grabber_to_use = $grabber;
						break;
					}
				}
			}
		}
	}

	if (!$grabber_to_use)
	{
		$grabber_to_use = $default_grabber;
	}
	if (!$grabber_to_use)
	{
		return null;
	}

	$grabber_settings = new KvsGrabberSettings();
	$grabber_settings->from_array(@unserialize(@file_get_contents("$plugin_path/grabber_{$grabber_to_use->get_grabber_id()}.dat")));
	$grabber_to_use->init($grabber_settings, "$plugin_path/storage");

	if ($grabber_to_use instanceof KvsGrabberVideoYDL)
	{
		$ydl_info = @unserialize(@file_get_contents("$plugin_path/ydl.dat"));
		if (is_array($ydl_info) && $ydl_info['ydl_binary'])
		{
			$grabber_to_use->set_ydl_binary($ydl_info['ydl_binary']);
		}
	}

	if (!$grabber_settings->get_mode() || $grabber_settings->is_broken())
	{
		return null;
	}

	return $grabber_to_use;
}

function grabbersCreateImport($task_id, $admin_id = 0)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$task_data = @unserialize(@file_get_contents("$plugin_path/import/$task_id.dat"));
	if (!is_array($task_data))
	{
		return;
	}

	$grabber_type = KvsGrabberVideo::GRABBER_TYPE_VIDEOS;
	if (in_array($task_data['upload_type'], array(KvsGrabberVideo::GRABBER_TYPE_VIDEOS, KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)))
	{
		$grabber_type = $task_data['upload_type'];
	}

	$threads = 0;
	if (is_array($task_data['grabbers_usage']))
	{
		foreach ($task_data['grabbers_usage'] as $grabber_usage)
		{
			if ($grabber_usage['type'] == 'valid')
			{
				$threads++;
			}
		}
	}

	if ($threads > 0)
	{
		$import_id = mt_rand(10000000, 99999999);
		for ($i = 0; $i < 999; $i++)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports where import_id=?", $import_id)) > 0)
			{
				$import_id = mt_rand(10000000, 99999999);
			} else
			{
				break;
			}
		}

		$import_options = array(
			'separator' => "|",
			'separator_modified' => "|",
			'line_separator' => "\\n",
			'line_separator_modified' => "\n",
			'field1' => "gallery_url",
			'status_after_import_id' => intval($task_data["status_after_import_id"]),
			'title_limit' => intval($task_data["title_limit"]),
			'title_limit_type_id' => intval($task_data["title_limit_type_id"]),
			'description_limit' => intval($task_data["description_limit"]),
			'description_limit_type_id' => intval($task_data["description_limit_type_id"]),
			'is_post_time_randomization' => intval($task_data["is_randomize_time"]),
			'post_time_randomization_from' => "00:00",
			'post_time_randomization_to' => "23:59",
			'is_make_directories' => "1",
			'is_skip_duplicate_urls' => "1",
			'is_skip_duplicate_titles' => intval($task_data["is_skip_duplicate_titles"]),
			'is_skip_new_categories' => intval($task_data["is_skip_new_categories"]),
			'is_skip_new_models' => intval($task_data["is_skip_new_models"]),
			'is_skip_new_content_sources' => intval($task_data["is_skip_new_content_sources"]),
			'is_skip_new_dvds' => intval($task_data["is_skip_new_channels"]),
			'is_review_needed' => intval($task_data["is_review_needed"]),
		);

		$background_task_type_id = 50;
		$import_type_id = 1;
		if ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
		{
			$background_task_type_id = 51;
			$import_type_id = 2;
		}

		$background_task_id = sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=$background_task_type_id, added_date=?", date("Y-m-d H:i:s"));

		sql_pr("insert into $config[tables_prefix]background_imports set import_id=?, task_id=?, admin_id=?, status_id=0, type_id=?, threads=?, options=?, added_date=?",
			$import_id, $background_task_id, $admin_id, $import_type_id, $threads, serialize($import_options), date("Y-m-d H:i:s")
		);

		$threads_per_grabber = max(1, intval($task_data['threads']));
		$thread_id = 0;
		$max_thread_id = 0;
		$lines_counter = 0;
		foreach ($task_data['grabbers_usage'] as $grabber_usage)
		{
			if ($grabber_usage['type'] == 'valid')
			{
				$thread_id++;
				$initial_thread_id = $thread_id;
				$max_thread_id = max($max_thread_id, $thread_id);
				foreach ($grabber_usage['urls'] as $url)
				{
					$lines_counter++;
					sql_pr("insert into $config[tables_prefix]background_imports_data set import_id=?, line_id=?, status_id=0, thread_id=?, data=?",
						$import_id, $lines_counter, $thread_id, $url
					);
					if ($threads_per_grabber > 1 && count($grabber_usage['urls']) > 1)
					{
						$thread_id++;
						if ($thread_id - $initial_thread_id >= $threads_per_grabber)
						{
							$thread_id = $initial_thread_id;
						}
						$max_thread_id = max($max_thread_id, $thread_id);
					}
				}
				if ($threads_per_grabber > 1)
				{
					$thread_id = $max_thread_id;
				}
			}
		}

		sql_pr("update $config[tables_prefix]background_imports set threads=? where import_id=?", $max_thread_id, $import_id);
	}

	unlink("$plugin_path/import/$task_id.dat");
	unlink("$plugin_path/import/task-progress-$task_id.dat");
}

function grabbersProcessUrls($task_id)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/grabbers";

	$data = @unserialize(@file_get_contents("$plugin_path/import/$task_id.dat"));
	if (!is_array($data))
	{
		return;
	}

	$grabbers_usage = array();

	$grabber_type = KvsGrabberVideo::GRABBER_TYPE_VIDEOS;
	if (in_array($data['upload_type'], array(KvsGrabberVideo::GRABBER_TYPE_VIDEOS, KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)))
	{
		$grabber_type = $data['upload_type'];
	}

	$urls = explode("\n", $data['upload_list']);

	$total_amount_of_work = count($urls);
	$done_amount_of_work = 0;

	foreach ($urls as $url_count_pair)
	{
		$url_count_pair = explode('|', $url_count_pair, 2);
		$url = trim($url_count_pair[0]);
		$count = intval($url_count_pair[1]);
		if ($url != '' && $count > 0)
		{
			$total_amount_of_work += $count;
		}
	}

	$processed_urls = array();

	foreach ($urls as $url_count_pair)
	{
		$url_count_pair = explode('|', $url_count_pair, 2);
		$url = trim($url_count_pair[0]);
		$count = intval($url_count_pair[1]);
		if ($url != '')
		{
			$grabber_urls = array();
			$grabber_error = '';

			$grabber = grabbersFindGrabber($url, $grabber_type);
			if ($grabber instanceof KvsGrabber)
			{
				if ($grabber->is_content_url($url))
				{
					$grabber_urls[] = $url;
				} elseif ($grabber->can_grab_lists())
				{
					if ($count > 0)
					{
						$grabber->set_progress_callback(function($progress) use ($plugin_path, $task_id, $done_amount_of_work, $total_amount_of_work) {
							$pc = floor((($done_amount_of_work + $progress) / $total_amount_of_work) * 100);
							file_put_contents("$plugin_path/import/task-progress-$task_id.dat", "$pc", LOCK_EX);
						});
					}

					$list_result = $grabber->grab_list($url, $count);
					if ($list_result)
					{
						foreach ($list_result->get_content_pages() as $content_page)
						{
							$grabber_urls[] = $content_page;
						}
						if ($list_result->get_error_code() > 0)
						{
							$grabber_error = $list_result->get_error_message();
						}
					}
				}
			}

			if (count($grabber_urls) > 0 && $grabber instanceof KvsGrabber)
			{
				foreach ($grabber_urls as $grabber_url)
				{
					$is_duplicate = false;
					if ($grabber_type == KvsGrabberVideo::GRABBER_TYPE_VIDEOS)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where gallery_url=?", $grabber_url)) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", md5($grabber_url))) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=?", $grabber_url)) > 0 ||
							isset($processed_urls[md5($grabber_url)])
						)
						{
							$is_duplicate = true;
						}
					}
					if ($grabber_type == KvsGrabberAlbum::GRABBER_TYPE_ALBUMS)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where gallery_url=?", $grabber_url)) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?", md5($grabber_url))) > 0 ||
							mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=?", $grabber_url)) > 0 ||
							isset($processed_urls[md5($grabber_url)])
						)
						{
							$is_duplicate = true;
						}
					}
					if (!$is_duplicate)
					{
						if (!isset($grabbers_usage[$grabber->get_grabber_id()]))
						{
							$grabber_settings = $grabber->get_settings();
							$grabbers_usage[$grabber->get_grabber_id()] = array(
								'type' => 'valid',
								'name' => $grabber->get_grabber_name(),
								'mode' => $grabber_settings->get_mode(),
								'urls' => array(),
							);
						}
						$grabbers_usage[$grabber->get_grabber_id()]['urls'][] = $grabber_url;

						$processed_urls[md5($grabber_url)] = 1;
					} else
					{
						if (!isset($grabbers_usage['duplicates']))
						{
							$grabbers_usage['duplicates'] = array(
								'type' => 'duplicates',
								'urls' => array(),
							);
						}
						$grabbers_usage['duplicates']['urls'][] = $grabber_url;
					}
				}
			} else
			{
				if ($grabber_error)
				{
					if (!isset($grabbers_usage['error']))
					{
						$grabbers_usage['error'] = array(
							'type' => 'error',
							'urls' => array(),
							'errors' => array(),
						);
					}
					$grabbers_usage['error']['urls'][] = $url;
					$grabbers_usage['error']['errors'][] = $grabber_error;
				} else
				{
					if (!isset($grabbers_usage['missing']))
					{
						$grabbers_usage['missing'] = array(
							'type' => 'missing',
							'urls' => array(),
						);
					}
					$grabbers_usage['missing']['urls'][] = $url;
				}
			}

			if ($count > 0)
			{
				$done_amount_of_work += $count;
			}
		}

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/import/task-progress-$task_id.dat", "$pc", LOCK_EX);
	}

	$grabbers_usage_missing = $grabbers_usage['missing'];
	$grabbers_usage_duplicates = $grabbers_usage['duplicates'];
	unset($grabbers_usage['missing']);
	unset($grabbers_usage['duplicates']);
	if (isset($grabbers_usage_missing))
	{
		$grabbers_usage['missing'] = $grabbers_usage_missing;
	}
	if (isset($grabbers_usage_duplicates))
	{
		$grabbers_usage['duplicates'] = $grabbers_usage_duplicates;
	}

	$data['grabbers_usage'] = $grabbers_usage;
	file_put_contents("$plugin_path/import/$task_id.dat", serialize($data), LOCK_EX);

	file_put_contents("$plugin_path/import/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1] == 'check_grabber' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once("$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php");

	$grabber_path = $_SERVER['argv'][2];
	$grabber = require_once($grabber_path);
	if ($grabber instanceof KvsGrabber)
	{
		$grabber_id = $grabber->get_grabber_id();
		if (preg_match($regexp_valid_external_id, $grabber_id))
		{
			echo "OKGRABBER($grabber_id)";
		}
	}
	die;
}

if ($_SERVER['argv'][1] == 'mass_import' && intval($_SERVER['argv'][2]) > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions.php');
	require_once("$config[project_path]/admin/plugins/grabbers/classes/KvsGrabber.php");

	ini_set("display_error", 1);

	grabbersProcessUrls(intval($_SERVER['argv'][2]));
	die;
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}