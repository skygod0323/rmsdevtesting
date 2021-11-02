<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function migratorInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins", 0777);
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/migrator";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path, 0777);
		chmod($plugin_path, 0777);
	}
}

function migratorShow()
{
	global $config, $lang, $errors, $page_name;

	migratorInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/migrator";

	$errors = null;

	$task_id = 0;
	if ($_GET['action'] == 'progress')
	{
		$task_id = intval($_GET['task_id']);
		$pc = intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc == 100)
		{
			$location = "<location>plugins.php?plugin_id=migrator&amp;action=display_result&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>";
		die;
	} elseif ($_GET['action'] == 'display_result')
	{
		$task_id = intval($_GET['task_id']);
		$_POST = @unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
		$_POST['log_file'] = "$task_id";
		$_POST['log_file_size'] = sizeToHumanString(filesize("$plugin_path/task-log-$task_id.dat"),2);
	} elseif ($_GET['action'] == 'log')
	{
		$task_id = intval($_GET['task_id']);
		header("Content-Type: text/plain; charset=utf-8");
		if (is_file("$plugin_path/task-log-$task_id.dat"))
		{
			echo @file_get_contents("$plugin_path/task-log-$task_id.dat");
		}
		echo die;
	} elseif ($_POST['action'] == 'start_migration')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (validate_field('empty', $_POST['old_path'], $lang['plugins']['migrator']['field_old_path']))
		{
			if (!is_dir($_POST['old_path']))
			{
				$errors[] = get_aa_error('server_path_invalid', $lang['plugins']['migrator']['field_old_path']);
			}
		}
		validate_field('url', $_POST['old_url'], $lang['plugins']['migrator']['field_old_url']);

		$has_mysql_error = false;
		if (!validate_field('empty', $_POST['old_mysql_url'], $lang['plugins']['migrator']['field_old_mysql_url']))
		{
			$has_mysql_error = true;
		}
		if (!validate_field('empty_int', $_POST['old_mysql_port'], $lang['plugins']['migrator']['field_old_mysql_port']))
		{
			$has_mysql_error = true;
		}
		if (!validate_field('empty', $_POST['old_mysql_user'], $lang['plugins']['migrator']['field_old_mysql_user']))
		{
			$has_mysql_error = true;
		}
		if (!validate_field('empty', $_POST['old_mysql_pass'], $lang['plugins']['migrator']['field_old_mysql_pass']))
		{
			$has_mysql_error = true;
		}
		if (!validate_field('empty', $_POST['old_mysql_name'], $lang['plugins']['migrator']['field_old_mysql_name']))
		{
			$has_mysql_error = true;
		}
		if (!validate_field('empty', $_POST['old_mysql_charset'], $lang['plugins']['migrator']['field_old_mysql_charset']))
		{
			$has_mysql_error = true;
		}
		if (!$has_mysql_error)
		{
			if (function_exists('mysqli_connect'))
			{
				$mysql_link = new mysqli($_POST['old_mysql_url'], $_POST['old_mysql_user'], $_POST['old_mysql_pass'], $_POST['old_mysql_name'], intval($_POST['old_mysql_port']));
				if ($mysql_link->connect_error)
				{
					$errors[] = str_replace("%1%", $mysql_link->connect_error, $lang['plugins']['migrator']['error_failed_to_connect']);
				}
			} else
			{
				$errors[] = $lang['plugins']['migrator']['error_php_mysqli'];
			}
		}

		if (intval($_POST['test_mode']) == 1)
		{
			validate_field('empty', $_POST['test_mode_limit'], $lang['plugins']['migrator']['field_test_mode']);
		}

		mt_srand(time());
		$rnd = mt_rand(10000000, 99999999);

		@unlink("$plugin_path/task-$rnd.dat");
		@unlink("$plugin_path/task-log-$rnd.dat");

		$data = $_POST;

		file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);
		if (!is_file("$plugin_path/task-$rnd.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/task-$rnd.dat"));
		}

		if (!is_array($errors))
		{
			exec("$config[php_path] $config[project_path]/admin/plugins/migrator/migrator.php $rnd > $plugin_path/task-log-$rnd.dat &");
			return_ajax_success("$page_name?plugin_id=migrator&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}", 2);
		} else
		{
			@unlink("$plugin_path/task-$rnd.dat");
			return_ajax_errors($errors);
		}
	}

	require_once("$config[project_path]/admin/plugins/migrator/classes/KvsDataMigrator.php");

	$migrator_scripts = get_contents_from_dir("$config[project_path]/admin/plugins/migrator/classes", 1);
	foreach ($migrator_scripts as $migrator_script)
	{
		if (strtolower(end(explode('.', $migrator_script, 2))) == 'php')
		{
			require_once("$config[project_path]/admin/plugins/migrator/classes/$migrator_script");
			$migrator = substr($migrator_script, 0, strlen($migrator_script) - 4);
			if ($migrator != 'KvsDataMigrator' && class_exists($migrator))
			{
				$migrator_class = new ReflectionClass($migrator);
				if ($migrator_class->isSubclassOf('KvsDataMigrator'))
				{
					/** @var $migrator_instance KvsDataMigrator */
					$migrator_instance = $migrator_class->newInstance($config);
					$_POST['migrators'][$migrator_instance->get_migrator_id()] = $migrator_instance->get_migrator_name();

					$migrator_supported_data = $migrator_instance->get_migrator_supported_data();
					$_POST['migrators_supported_data'][$migrator_instance->get_migrator_id()] = $migrator_supported_data->to_array();

					$migrator_options = $migrator_instance->get_migrator_additional_options();
					foreach ($migrator_options as $option)
					{
						$_POST['migrators_options'][$migrator_instance->get_migrator_id()][$option] = trim($_POST[$option]);
					}

					if ($migrator_instance->is_migrator_default())
					{
						$_POST['migrators_default'] = $migrator_instance->get_migrator_id();
					}
				}
			}
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}

	$results=scandir($plugin_path);
	$results_time=array();
	$results_values=array();
	foreach ($results as $file)
	{
		if (is_file("$plugin_path/$file") && strpos($file,'task-')===0 && strpos($file,'task-progress')===false && strpos($file,'task-log')===false)
		{
			$results_time[]=filectime("$plugin_path/$file");
			$results_values[]=$file;
		}
	}
	array_multisort($results_time,SORT_NUMERIC,SORT_DESC,$results_values);
	$results=array();
	foreach ($results_values as $file)
	{
		if ($task_id>0 && $file=="task-$task_id.dat")
		{
			continue;
		}
		$check_result=@unserialize(@file_get_contents("$plugin_path/$file"));
		$key=substr($file,5,strpos($file,'.')-5);

		$results[$file]=array();
		$results[$file]['date']=filectime("$plugin_path/$file");
		$results[$file]['inserted_count']=0;
		$results[$file]['updated_count']=0;
		$results[$file]['errors_count']=0;
		foreach ($check_result['summary']['migration'] as $item)
		{
			$results[$file]['inserted_count']+=$item['inserted'];
			$results[$file]['updated_count']+=$item['updated'];
			$results[$file]['errors_count']+=$item['errors'];
		}
		$results[$file]['key']=$key;
		if (is_file("$plugin_path/task-progress-$key.dat"))
		{
			$pc=intval(@file_get_contents("$plugin_path/task-progress-$key.dat"));
			if ($pc<100)
			{
				$results[$file]['process']=$pc+1;
			}
		}
		if (is_file("$plugin_path/task-log-$key.dat"))
		{
			$results[$file]['has_log']=1;
		}
	}
	$_POST['recent_migrations']=$results;
}

$task_id = intval($_SERVER['argv'][1]);

if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions.php');
	require_once('include/pclzip.lib.php');

	ini_set('display_errors', true);

	$plugin_path = "$config[project_path]/admin/data/plugins/migrator";

	$data = @unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
	if (!is_array($data))
	{
		error_reporting(E_ALL);
		unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));
		die;
	}

	$admin_user_id = 1;

	require_once("$config[project_path]/admin/plugins/migrator/classes/KvsDataMigrator.php");

	/** @var $migrators KvsDataMigrator[] */
	$migrators = array();

	$migrator_scripts = get_contents_from_dir("$config[project_path]/admin/plugins/migrator/classes", 1);
	foreach ($migrator_scripts as $migrator_script)
	{
		if (strtolower(end(explode('.', $migrator_script, 2))) == 'php')
		{
			require_once("$config[project_path]/admin/plugins/migrator/classes/$migrator_script");
			$migrator = substr($migrator_script, 0, strlen($migrator_script) - 4);
			if ($migrator != 'KvsDataMigrator' && class_exists($migrator))
			{
				$migrator_class = new ReflectionClass($migrator);
				if ($migrator_class->isSubclassOf('KvsDataMigrator'))
				{
					/** @var $migrator_instance KvsDataMigrator */
					$migrator_instance = $migrator_class->newInstance($config);
					$migrators[$migrator_instance->get_migrator_id()] = $migrator_instance;
				}
			}
		}
	}

	$migrator = $migrators[$data['old_script']];
	if ($migrator)
	{
		$data_to_migrate = new KvsDataMigratorDataToMigrate(
			intval($data['migrate_tags']) == 1,
			intval($data['migrate_categories']) == 1,
			intval($data['migrate_models']) == 1,
			intval($data['migrate_content_sources']) == 1,
			intval($data['migrate_dvds']) == 1,
			intval($data['migrate_videos']) == 1,
			intval($data['migrate_videos_screenshots']) == 1,
			intval($data['migrate_albums']) == 1,
			intval($data['migrate_comments']) == 1,
			intval($data['migrate_users']) == 1,
			intval($data['migrate_favourites']) == 1,
			intval($data['migrate_friends']) == 1,
			intval($data['migrate_messages']) == 1,
			intval($data['migrate_subscriptions']) == 1,
			intval($data['migrate_playlists']) == 1
		);

		$test_mode_limit = 0;
		if ($data['test_mode'] == 1)
		{
			$test_mode_limit = $data['test_mode_limit'];
		}

		$migrator_options = array();
		foreach ($migrator->get_migrator_additional_options() as $option)
		{
			if ($data[$option] != '')
			{
				$migrator_options[$option] = $data[$option];
			}
		}

		$summary = $migrator->start($admin_user_id, $data['old_path'], $data['old_url'], $data['old_mysql_url'], $data['old_mysql_port'], $data['old_mysql_user'], $data['old_mysql_pass'], $data['old_mysql_name'], $data['old_mysql_charset'], $data_to_migrate, intval($data['override_objects']) == 1, intval($data['upload_hotlinked_videos']) == 1, $test_mode_limit, $migrator_options, function ($progress)
		{
			global $plugin_path, $task_id;

			file_put_contents("$plugin_path/task-progress-$task_id.dat", intval($progress), LOCK_EX);
		});

		$data['summary'] = $summary;
		file_put_contents("$plugin_path/task-$task_id.dat", serialize($data));
	}

	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}