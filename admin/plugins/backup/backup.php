<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function backupInit()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";
	mkdir_recursive($plugin_path);

	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		$data['backup_folder'] = "$config[project_path]/admin/data/backup";
		$data['auto_backup_daily'] = 1;
		$data['auto_backup_weekly'] = 1;
		$data['auto_backup_monthly'] = 1;
		$data['auto_skip_content_auxiliary'] = 0;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
		file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
	}
}

function backupIsEnabled()
{
	global $config;

	backupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	return is_file("$plugin_path/cron.dat");
}

function backupGetWarnings()
{
	$warnings = [];
	if (!backupIsEnabled())
	{
		$warnings[] = 'warning_automatic_backup';
	}
	return $warnings;
}

function backupGetErrors()
{
	global $config;

	$errors = [];

	backupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if ($data['backup_folder'] && !is_writable($data['backup_folder']))
	{
		$errors[] = 'error_folder_permissions';
	}

	return $errors;
}

function backupShow()
{
	global $config, $lang, $errors, $page_name;

	backupInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		$log_file = "plugins/backup.txt";
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
	} elseif ($_GET['action'] == 'progress')
	{
		header("Content-Type: text/xml");

		$task_id = intval($_GET['task_id']);
		$contents = @file_get_contents("$plugin_path/task-progress-$task_id.dat");
		$location = '';
		if ($contents == 'error')
		{
			$pc = 100;
			$location = "<location>plugins.php?plugin_id=backup&amp;error=1</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		} elseif ($contents == 'error2')
		{
			$pc = 100;
			$location = "<location>plugins.php?plugin_id=backup&amp;error=2</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		} else
		{
			$pc = intval($contents);
			if ($pc == 100)
			{
				$location = "<location>plugins.php?plugin_id=backup</location>";
				@unlink("$plugin_path/task-progress-$task_id.dat");
			}
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>";
		die;
	} elseif ($_POST['action'] == 'save_backup')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		validate_field('empty', $_POST['backup_folder'], $lang['plugins']['backup']['field_backup_folder']);
		if (!is_writable("$_POST[backup_folder]"))
		{
			$errors[] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$_POST[backup_folder]")));
		}

		$backup_mysql = 0;
		$backup_website = 0;
		$backup_player = 0;
		$backup_kvs = 0;
		$backup_content = 0;
		if (isset($_POST['do_backup']))
		{
			$backup_mysql = intval($_POST['backup_mysql']);
			$backup_website = intval($_POST['backup_website']);
			$backup_player = intval($_POST['backup_player']);
			$backup_kvs = intval($_POST['backup_kvs']);
			$backup_content = intval($_POST['backup_content_auxiliary']);
			if ($backup_mysql + $backup_website + $backup_player + $backup_kvs + $backup_content == 0)
			{
				$errors[] = get_aa_error('required_field', $lang['plugins']['backup']['field_backup_options']);
			}
		}

		mt_srand(time());
		$rnd = mt_rand(10000000, 99999999);

		if (!is_array($errors))
		{
			$data = @unserialize(file_get_contents("$plugin_path/data.dat")) ?: [];
			$data['backup_folder'] = $_POST['backup_folder'];
			$data['auto_backup_daily'] = intval($_POST['auto_backup_daily']);
			$data['auto_backup_weekly'] = intval($_POST['auto_backup_weekly']);
			$data['auto_backup_monthly'] = intval($_POST['auto_backup_monthly']);
			$data['auto_skip_content_auxiliary'] = intval($_POST['auto_skip_content_auxiliary']);
			$data['tod'] = intval($_POST['tod']);

			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			if (!is_file("$plugin_path/data.dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
			}

			if (isset($_POST['do_backup']))
			{
				$data['backup_mysql'] = $backup_mysql;
				$data['backup_website'] = $backup_website;
				$data['backup_player'] = $backup_player;
				$data['backup_kvs'] = $backup_kvs;
				$data['backup_content_auxiliary'] = $backup_content;

				file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);
				if (!is_file("$plugin_path/task-$rnd.dat"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/task-$rnd.dat"));
				}
			}

			if (intval($_POST['auto_backup_daily']) == 1 || intval($_POST['auto_backup_weekly']) == 1 || intval($_POST['auto_backup_monthly']) == 1)
			{
				if (!is_file("$plugin_path/cron.dat") || $data['tod'] > 0)
				{
					$current_hour = date('H');
					if ($data['tod'] == 0)
					{
						$next_date = time();
					} elseif ($current_hour < $data['tod'] - 1)
					{
						$next_date = strtotime(date('Y-m-d ') . ($data['tod'] - 1) . ':00:00');
					} else
					{
						$next_date = strtotime(date('Y-m-d ') . ($data['tod'] - 1) . ':00:00') + 86400;
					}
					file_put_contents("$plugin_path/cron.dat", $next_date, LOCK_EX);
				}
			} else
			{
				@unlink("$plugin_path/cron.dat");
			}

			if (isset($_POST['do_backup']))
			{
				exec("$config[php_path] $config[project_path]/admin/plugins/backup/backup.php $rnd > $config[project_path]/admin/logs/plugins/backup.txt &");
				return_ajax_success("$page_name?plugin_id=backup&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}", 2);
			} else
			{
				return_ajax_success("$page_name?plugin_id=backup");
			}
		} else
		{
			@unlink("$plugin_path/task-$rnd.dat");
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));

	$_POST['next_exec_date'] = '0000-00-00 00:00:00';
	if (is_file("$plugin_path/cron.dat"))
	{
		$_POST['next_exec_date'] = date("Y-m-d H:i:s", file_get_contents("$plugin_path/cron.dat"));
	}

	unset($res);
	$mysqldump_path = "/usr/local/bin/mysqldump";
	if ($config['mysqldump_path'] <> '')
	{
		$mysqldump_path = $config['mysqldump_path'];
	}
	exec("$mysqldump_path 2>&1", $res);
	if (stripos(implode("\n", $res), '--help') === false)
	{
		unset($res);
		$mysqldump_path = "/usr/local/bin/mysqldump";
		exec("$mysqldump_path 2>&1", $res);
		if (stripos(implode("\n", $res), '--help') === false)
		{
			unset($res);
			$mysqldump_path = "/usr/bin/mysqldump";
			exec("$mysqldump_path 2>&1", $res);
			if (stripos(implode("\n", $res), '--help') === false)
			{
				$_POST['errors'][] = str_replace("%1%", $config['mysqldump_path'], $lang['plugins']['backup']['error_mysqldump_command']);
				$_POST['has_mysqldump_error'] = 1;
			}
		}
	}

	if ($_POST['backup_folder'] != '')
	{
		$results = scandir($_POST['backup_folder']);
		$results_time = [];
		$results_values = [];
		foreach ($results as $file)
		{
			if (is_file("$_POST[backup_folder]/$file") && (strpos($file, '.zip') !== false || strpos($file, '.gz') !== false))
			{
				$results_time[] = filemtime("$_POST[backup_folder]/$file");
				$results_values[] = $file;
			}
		}
		array_multisort($results_time, SORT_NUMERIC, SORT_DESC, $results_values);
		$results = [];
		foreach ($results_values as $file)
		{
			$item = [];
			$item['filename'] = $file;
			$item['filedate'] = filemtime("$_POST[backup_folder]/$file");
			$item['filesize'] = sizeToHumanString(filesize("$_POST[backup_folder]/$file"), 2);
			$item['contents'] = [];

			preg_match("|backup-([dwepsc]+)-.*|is", $file, $temp);
			if (strpos($temp[1], 'd') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_mysql'];
			}
			if (strpos($temp[1], 'w') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_website'];
			}
			if (strpos($temp[1], 'p') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_player'];
			}
			if (strpos($temp[1], 's') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_kvs'];
			}
			if (strpos($temp[1], 'c') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary'];
			}
			preg_match("|backup-auto-([dwepsc]+)-.*|is", $file, $temp);
			if (strpos($temp[1], 'd') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_mysql'];
			}
			if (strpos($temp[1], 'w') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_website'];
			}
			if (strpos($temp[1], 'p') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_player'];
			}
			if (strpos($temp[1], 's') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_kvs'];
			}
			if (strpos($temp[1], 'c') !== false)
			{
				$item['contents'][] = $lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary'];
			}

			$results[] = $item;
		}
		$_POST['backups'] = $results;

		if (!is_writable("$_POST[backup_folder]"))
		{
			$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$_POST[backup_folder]")));
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}
	if ($_GET['error'] == 1)
	{
		$_POST['errors'][] = bb_code_process($lang['plugins']['backup']['error_folder_permissions']);
	}
	if ($_GET['error'] == 2)
	{
		$_POST['errors'][] = bb_code_process($lang['plugins']['backup']['error_mysql_backup_failed']);
	}

	$_POST['open_basedir'] = trim(@ini_get('open_basedir'));
}

function backupCron()
{
	global $config;

	$start = time();

	require_once 'setup.php';
	require_once 'functions_base.php';
	require_once 'functions.php';

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";
	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));

	if ($data['backup_folder'] == '')
	{
		backupLog("No backup folder is configured");

		return;
	}
	if (!is_writable($data['backup_folder']))
	{
		backupLog("Backup folder is not writable: $data[backup_folder]");

		return;
	}

	mt_srand(time());
	$rnd = mt_rand(10000000, 99999999);

	$current_date = time();

	$results = scandir($data['backup_folder']);
	foreach ($results as $file)
	{
		if (strpos($file, "backup-auto-") === 0)
		{
			$created_time = filemtime("$data[backup_folder]/$file");
			if ($data['auto_backup_monthly'] == 1)
			{
				$date_info = getdate($created_time);
				if ($date_info['mday'] == 1 && time() - $created_time < 86400 * 365)
				{
					backupLog("Backup is kept as monthly backup: $file");
					continue;
				}
			}
			if ($data['auto_backup_weekly'] == 1)
			{
				$date_info = getdate($created_time);
				if ($date_info['wday'] == 1 && time() - $created_time < 86400 * 30)
				{
					backupLog("Backup is kept as weekly backup: $file");
					continue;
				}
			}
			if ($data['auto_backup_daily'] == 1)
			{
				if (time() - $created_time < 86400 * 7)
				{
					backupLog("Backup is kept as daily backup: $file");
					continue;
				}
			}

			if (unlink("$data[backup_folder]/$file"))
			{
				backupLog("Backup is deleted: $file");
			} else
			{
				backupLog("Backup failed to be deleted: $file");
			}
		}
	}

	$create_backup = 0;
	if ($data['auto_backup_daily'] == 1)
	{
		$create_backup = 1;
	} elseif ($data['auto_backup_weekly'] == 1)
	{
		$date_info = getdate($current_date);
		if ($date_info['wday'] == 1)
		{
			$create_backup = 1;
		}
	} elseif ($data['auto_backup_monthly'] == 1)
	{
		$date_info = getdate($current_date);
		if ($date_info['mday'] == 1)
		{
			$create_backup = 1;
		}
	}

	if ($create_backup == 1)
	{
		$result = backup_DoBackup($data['backup_folder'], $rnd, $config['is_clone_db'] == 'true' ? 0 : 1, 1, 1, 1, intval($data['auto_skip_content_auxiliary']) == 1 ? 0 : ($config['is_clone_db'] == 'true' ? 0 : 1), 1);
		if ($result == 'error')
		{
			backupLog("Not enough permissions for creating backup files in the specified backup folder");
		} elseif ($result == 'error2')
		{
			backupLog("MySQL backup failed");
		}
	}

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (is_array($data))
	{
		$data['last_exec_date'] = $start;
		$data['duration'] = time() - $start;
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

		$next_date = $start + 24 * 60 * 60;
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

	@unlink("$plugin_path/task-progress-$rnd.dat");

	$duration = time() - $start;
	backupLog("Finished in $duration seconds");
}

function backup_RecurseCopy($src, $dst, $public_permissions, $excludes = [])
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	if (!is_dir($dst))
	{
		mkdir($dst);
		chmod($dst, $public_permissions ? 0777 : 0755);
	}
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_dir("$src/$file"))
			{
				if (!in_array($file, $excludes))
				{
					backup_RecurseCopy("$src/$file", "$dst/$file", $public_permissions, $excludes);
				}
			} else
			{
				if (!in_array($file, $excludes))
				{
					copy("$src/$file", "$dst/$file");
					if ($file == '.htaccess')
					{
						chmod("$dst/$file", 0644);
					} else
					{
						chmod("$dst/$file", $public_permissions ? 0666 : 0644);
					}
				}
			}
		}
	}
	closedir($dir);
}

function backup_CopyFilesOnly($src, $dst, $public_permissions, $limit_size = 0, $excludes = [])
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	@mkdir($dst);
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_file("$src/$file") && !in_array($file, $excludes))
			{
				if (intval($limit_size) == 0 || intval($limit_size) > sprintf("%.0f", filesize("$src/$file")))
				{
					copy("$src/$file", "$dst/$file");
					chmod("$dst/$file", $public_permissions ? 0666 : 0644);
				}
			}
		}
	}
	closedir($dir);
}

function backup_RecurseDelete($src)
{
	if (!is_dir($src))
	{
		return;
	}
	$dir = opendir($src);
	while ($dir && false !== ($file = readdir($dir)))
	{
		if ($file <> '.' && $file <> '..')
		{
			if (is_dir("$src/$file"))
			{
				backup_RecurseDelete("$src/$file");
			} else
			{
				@unlink("$src/$file");
			}
		}
	}
	closedir($dir);
	@rmdir("$src");
}

function backup_DoBackup($backup_root, $task_id, $is_mysql, $is_website, $is_player, $is_kvs, $is_content, $is_auto)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";

	$backup_folder = "$backup_root/$task_id";
	if (!mkdir($backup_folder, 0755))
	{
		backupLog("Failed to create backup temp folder");

		return 'error';
	}

	$total_amount_of_work = $is_mysql + $is_website + $is_player + $is_kvs + $is_content + 1;
	$done_amount_of_work = 0;

	$backup_type_part = '';
	if ($is_mysql == 1)
	{
		mkdir("$backup_folder/mysql", 0755);

		unset($res);
		$mysqldump_path = "/usr/local/bin/mysqldump";
		if ($config['mysqldump_path'] <> '')
		{
			$mysqldump_path = $config['mysqldump_path'];
		}
		exec("$mysqldump_path 2>&1", $res);
		if (stripos(implode("\n", $res), '--help') === false)
		{
			unset($res);
			$mysqldump_path = "/usr/local/bin/mysqldump";
			exec("$mysqldump_path 2>&1", $res);
			if (stripos(implode("\n", $res), '--help') === false)
			{
				unset($res);
				$mysqldump_path = "/usr/bin/mysqldump";
				exec("$mysqldump_path 2>&1", $res);
				if (stripos(implode("\n", $res), '--help') === false)
				{
					$mysqldump_path = '';
				}
			}
		}

		backupLog("Using mysqldump command: $mysqldump_path");
		if ($mysqldump_path != '')
		{
			require_once "$config[project_path]/admin/include/setup_db.php";
			exec("$mysqldump_path --default-character-set=utf8 --user=" . DB_LOGIN . " --password='" . DB_PASS . "' --host=" . DB_HOST . " " . DB_DEVICE . " > $backup_folder/mysql/backup.sql");
			if (filesize("$backup_folder/mysql/backup.sql") < 10)
			{
				backupLog("MySQL backup failed");
			} else
			{
				backupLog("MySQL backup done");
				$backup_type_part .= 'd';
			}

			$done_amount_of_work++;
			$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
		}
	}

	$system_root_files = [
		"get_file.php",
		"get_image.php",
		"logout.php",
		"redirect_cs.php",
		"redirect_random_album.php",
		"redirect_random_video.php",
		"kvs_out.php"
	];

	$engine_customization_files = [
		"pre_process_page_code.php",
		"pre_display_page_code.php",
		"pre_initialize_page_code.php",
		"post_process_page_code.php",
		"pre_async_action_code.php"
	];

	if ($is_website == 1)
	{
		mkdir("$backup_folder/website", 0755);
		mkdir("$backup_folder/website/template");
		mkdir("$backup_folder/website/admin", 0755);
		mkdir("$backup_folder/website/admin/include", 0755);
		mkdir("$backup_folder/website/admin/data", 0755);
		mkdir("$backup_folder/website/admin/data/advertisements");
		mkdir("$backup_folder/website/admin/data/config");
		mkdir("$backup_folder/website/admin/data/system");
		if (is_dir("$config[project_path]/langs"))
		{
			mkdir("$backup_folder/website/langs");
		}
		if (is_dir("$config[project_path]/js"))
		{
			mkdir("$backup_folder/website/js", 0755);
		}
		if (is_dir("$config[project_path]/styles"))
		{
			mkdir("$backup_folder/website/styles", 0755);
		}
		if (is_dir("$config[project_path]/images"))
		{
			mkdir("$backup_folder/website/images", 0755);
		}
		if (is_dir("$config[project_path]/img"))
		{
			mkdir("$backup_folder/website/img", 0755);
		}
		if (is_dir("$config[project_path]/css"))
		{
			mkdir("$backup_folder/website/css", 0755);
		}
		if (is_dir("$config[project_path]/fonts"))
		{
			mkdir("$backup_folder/website/fonts", 0755);
		}
		if (is_dir("$config[project_path]/static"))
		{
			mkdir("$backup_folder/website/static", 0755);
		}

		foreach ($engine_customization_files as $engine_customization_file)
		{
			copy("$config[project_path]/admin/include/$engine_customization_file", "$backup_folder/website/admin/include/$engine_customization_file");
		}

		backup_RecurseCopy("$config[project_path]/template", "$backup_folder/website/template", true);
		backup_RecurseCopy("$config[project_path]/admin/data/advertisements", "$backup_folder/website/admin/data/advertisements", true);
		backup_RecurseCopy("$config[project_path]/admin/data/config", "$backup_folder/website/admin/data/config", true);
		backup_CopyFilesOnly("$config[project_path]", "$backup_folder/website", false, 1 * 1024 * 1024, $system_root_files);
		copy("$config[project_path]/admin/data/system/website_ui_params.dat", "$backup_folder/website/admin/data/system/website_ui_params.dat");
		chmod("$backup_folder/website/admin/data/system/website_ui_params.dat", 0666);
		copy("$config[project_path]/admin/data/system/runtime_params.dat", "$backup_folder/website/admin/data/system/runtime_params.dat");
		chmod("$backup_folder/website/admin/data/system/runtime_params.dat", 0666);
		copy("$config[project_path]/admin/data/system/blocked_words.dat", "$backup_folder/website/admin/data/system/blocked_words.dat");
		chmod("$backup_folder/website/admin/data/system/blocked_words.dat", 0666);
		if (is_dir("$config[project_path]/langs"))
		{
			backup_RecurseCopy("$config[project_path]/langs", "$backup_folder/website/langs", false);
		}
		if (is_dir("$config[project_path]/js"))
		{
			backup_RecurseCopy("$config[project_path]/js", "$backup_folder/website/js", false);
		}
		if (is_dir("$config[project_path]/styles"))
		{
			backup_RecurseCopy("$config[project_path]/styles", "$backup_folder/website/styles", false);
		}
		if (is_dir("$config[project_path]/images"))
		{
			backup_RecurseCopy("$config[project_path]/images", "$backup_folder/website/images", false);
		}
		if (is_dir("$config[project_path]/img"))
		{
			backup_RecurseCopy("$config[project_path]/img", "$backup_folder/website/img", false);
		}
		if (is_dir("$config[project_path]/css"))
		{
			backup_RecurseCopy("$config[project_path]/css", "$backup_folder/website/css", false);
		}
		if (is_dir("$config[project_path]/fonts"))
		{
			backup_RecurseCopy("$config[project_path]/fonts", "$backup_folder/website/fonts", false);
		}
		if (is_dir("$config[project_path]/static"))
		{
			backup_RecurseCopy("$config[project_path]/static", "$backup_folder/website/static", false);
		}

		backupLog("Website backup done");

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);

		$backup_type_part .= 'w';
	}

	if ($is_player == 1)
	{
		mkdir("$backup_folder/player", 0755);
		mkdir("$backup_folder/player/admin", 0755);
		mkdir("$backup_folder/player/admin/data", 0755);
		mkdir("$backup_folder/player/admin/data/player");
		mkdir("$backup_folder/player/contents", 0755);
		mkdir("$backup_folder/player/contents/other");
		mkdir("$backup_folder/player/contents/other/player");

		backup_RecurseCopy("$config[project_path]/admin/data/player", "$backup_folder/player/admin/data/player", true);
		backup_RecurseCopy("$config[content_path_other]/player", "$backup_folder/player/contents/other/player", true);

		backupLog("Player backup done");

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);

		$backup_type_part .= 'p';
	}

	if ($is_kvs == 1)
	{
		mkdir("$backup_folder/kvs", 0755);
		mkdir("$backup_folder/kvs/admin", 0755);
		mkdir("$backup_folder/kvs/admin/api", 0755);
		mkdir("$backup_folder/kvs/admin/async", 0755);
		mkdir("$backup_folder/kvs/admin/billings", 0755);
		if (is_dir("$config[project_path]/admin/cdn"))
		{
			mkdir("$backup_folder/kvs/admin/cdn", 0755);
		}
		mkdir("$backup_folder/kvs/admin/docs", 0755);
		mkdir("$backup_folder/kvs/admin/feeds", 0755);
		mkdir("$backup_folder/kvs/admin/images", 0755);
		mkdir("$backup_folder/kvs/admin/include", 0755);
		mkdir("$backup_folder/kvs/admin/js", 0755);
		mkdir("$backup_folder/kvs/admin/langs", 0755);
		mkdir("$backup_folder/kvs/admin/plugins", 0755);
		mkdir("$backup_folder/kvs/admin/smarty", 0755);
		mkdir("$backup_folder/kvs/admin/smarty/internals", 0755);
		mkdir("$backup_folder/kvs/admin/smarty/plugins", 0755);
		mkdir("$backup_folder/kvs/admin/stamp", 0755);
		mkdir("$backup_folder/kvs/admin/styles", 0755);
		mkdir("$backup_folder/kvs/admin/template", 0755);
		mkdir("$backup_folder/kvs/admin/tools", 0755);
		mkdir("$backup_folder/kvs/blocks", 0755);
		mkdir("$backup_folder/kvs/player", 0755);

		backup_CopyFilesOnly("$config[project_path]/admin", "$backup_folder/kvs/admin", false);
		backup_RecurseCopy("$config[project_path]/admin/api", "$backup_folder/kvs/admin/api", false);
		backup_RecurseCopy("$config[project_path]/admin/async", "$backup_folder/kvs/admin/async", false);
		backup_RecurseCopy("$config[project_path]/admin/billings", "$backup_folder/kvs/admin/billings", false);
		if (is_dir("$config[project_path]/admin/cdn"))
		{
			backup_RecurseCopy("$config[project_path]/admin/cdn", "$backup_folder/kvs/admin/cdn", false);
		}
		backup_RecurseCopy("$config[project_path]/admin/docs", "$backup_folder/kvs/admin/docs", false);
		backup_RecurseCopy("$config[project_path]/admin/feeds", "$backup_folder/kvs/admin/feeds", false);
		backup_RecurseCopy("$config[project_path]/admin/images", "$backup_folder/kvs/admin/images", false);
		backup_RecurseCopy("$config[project_path]/admin/include", "$backup_folder/kvs/admin/include", false, $engine_customization_files);
		backup_RecurseCopy("$config[project_path]/admin/js", "$backup_folder/kvs/admin/js", false);
		backup_RecurseCopy("$config[project_path]/admin/langs", "$backup_folder/kvs/admin/langs", false);
		backup_RecurseCopy("$config[project_path]/admin/plugins", "$backup_folder/kvs/admin/plugins", false);
		backup_CopyFilesOnly("$config[project_path]/admin/smarty", "$backup_folder/kvs/admin/smarty", false);
		backup_RecurseCopy("$config[project_path]/admin/smarty/internals", "$backup_folder/kvs/admin/smarty/internals", false);
		backup_RecurseCopy("$config[project_path]/admin/smarty/plugins", "$backup_folder/kvs/admin/smarty/plugins", false);
		backup_RecurseCopy("$config[project_path]/admin/stamp", "$backup_folder/kvs/admin/stamp", false);
		backup_RecurseCopy("$config[project_path]/admin/styles", "$backup_folder/kvs/admin/styles", false);
		backup_RecurseCopy("$config[project_path]/admin/template", "$backup_folder/kvs/admin/template", false);
		backup_RecurseCopy("$config[project_path]/admin/tools", "$backup_folder/kvs/admin/tools", false);
		backup_RecurseCopy("$config[project_path]/blocks", "$backup_folder/kvs/blocks", false);
		backup_RecurseCopy("$config[project_path]/player", "$backup_folder/kvs/player", false);

		foreach ($system_root_files as $system_root_file)
		{
			if (is_file("$config[project_path]/$system_root_file"))
			{
				copy("$config[project_path]/$system_root_file", "$backup_folder/kvs/$system_root_file");
				chmod("$backup_folder/kvs/$system_root_file", 0644);
			}
		}

		backupLog("System files backup done");

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);

		$backup_type_part .= 's';
	}

	if ($is_content == 1)
	{
		mkdir("$backup_folder/content", 0755);
		mkdir("$backup_folder/content/contents", 0755);
		mkdir("$backup_folder/content/contents/avatars");
		mkdir("$backup_folder/content/contents/categories");
		mkdir("$backup_folder/content/contents/content_sources");
		mkdir("$backup_folder/content/contents/models");
		mkdir("$backup_folder/content/contents/dvds");
		mkdir("$backup_folder/content/contents/posts");
		mkdir("$backup_folder/content/contents/referers");
		mkdir("$backup_folder/content/contents/other");

		backup_RecurseCopy("$config[content_path_avatars]", "$backup_folder/content/contents/avatars", true);
		backup_RecurseCopy("$config[content_path_categories]", "$backup_folder/content/contents/categories", true);
		backup_RecurseCopy("$config[content_path_content_sources]", "$backup_folder/content/contents/content_sources", true);
		backup_RecurseCopy("$config[content_path_models]", "$backup_folder/content/contents/models", true);
		backup_RecurseCopy("$config[content_path_dvds]", "$backup_folder/content/contents/dvds", true);
		backup_RecurseCopy("$config[content_path_posts]", "$backup_folder/content/contents/posts", true);
		backup_RecurseCopy("$config[content_path_referers]", "$backup_folder/content/contents/referers", true);
		backup_RecurseCopy("$config[content_path_other]", "$backup_folder/content/contents/other", true, ['player']);

		backupLog("Content backup done");

		$done_amount_of_work++;
		$pc = floor(($done_amount_of_work / $total_amount_of_work) * 100);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);

		$backup_type_part .= 'c';
	}

	if ($backup_type_part <> '')
	{
		copy("$config[project_path]/admin/plugins/backup/langs/readme.txt", "$backup_folder/readme.txt");
		$random_part = strtolower(generate_password());
		$now_date = date("Y-m-d-His");
		if ($is_auto == 1)
		{
			exec("tar -c -z -f $backup_root/backup-auto-$backup_type_part-$now_date-$random_part.tar.gz -C $backup_folder kvs mysql website player content readme.txt");
		} else
		{
			exec("tar -c -z -f $backup_root/backup-$backup_type_part-$now_date-$random_part.tar.gz -C $backup_folder kvs mysql website player content readme.txt");
		}

		backup_RecurseDelete("$backup_folder/kvs");
		backup_RecurseDelete("$backup_folder/mysql");
		backup_RecurseDelete("$backup_folder/website");
		backup_RecurseDelete("$backup_folder/player");
		backup_RecurseDelete("$backup_folder/content");
		rmdir_recursive($backup_folder);
	}

	backupLog("Backup finished");

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);

	return '';
}

function backupLog($message)
{
	global $config, $backup_no_file_log;

	echo date("[Y-m-d H:i:s] ") . $message . "\n";
	if (!$backup_no_file_log)
	{
		file_put_contents("$config[project_path]/admin/logs/plugins/backup.txt", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND);
	}
}

$task_id = intval($_SERVER['argv'][1]);

if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'include/setup.php';
	require_once 'include/functions_base.php';
	require_once 'include/functions.php';

	$plugin_path = "$config[project_path]/admin/data/plugins/backup";
	$backup_no_file_log = true;

	$data = @unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));

	$result = backup_DoBackup($data['backup_folder'], $task_id, $data['backup_mysql'], $data['backup_website'], $data['backup_player'], $data['backup_kvs'], $data['backup_content_auxiliary'], 0);
	if ($result != '')
	{
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$result", LOCK_EX);
		@unlink("$plugin_path/task-$task_id.dat");
		die;
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
