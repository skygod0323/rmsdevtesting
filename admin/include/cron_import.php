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

require_once("setup.php");
require_once("functions_base.php");
require_once("functions.php");

if (!is_file("$config[project_path]/admin/data/system/cron_import.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_import.lock", "1", LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_import.lock", "r+");
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors', 1);
chdir("$config[project_path]/admin");

$imports = mr2array(sql("select * from $config[tables_prefix]background_imports where status_id in (0,1)"));
foreach ($imports as $import)
{
	if ($import['status_id'] == 0)
	{
		log_output("Starting new import $import[import_id]", $import['task_id']);
		sql_pr("update $config[tables_prefix]background_imports set status_id=1 where import_id=?", $import['import_id']);
		sql_pr("update $config[tables_prefix]background_tasks set status_id=1, start_date=? where task_id=?", date("Y-m-d H:i:s"), $import['task_id']);
		for ($i = 1; $i <= max(1, intval($import['threads'])); $i++)
		{
			log_output("Starting import thread $i", $import['task_id']);
			$import_script = 'background_import.php';
			if ($import['type_id'] == 2)
			{
				$import_script = 'background_import_albums.php';
			}
			exec("$config[php_path] $config[project_path]/admin/$import_script $import[import_id] import english $import[admin_id] $import[task_id] $i >> $config[project_path]/admin/logs/tasks/$import[task_id]_$i.txt 2>&1 &");
			sleep(2);
		}
	} elseif ($import['status_id'] == 1)
	{
		log_output("Updating running import $import[import_id]");

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?", $import['task_id'])) == 0)
		{
			log_output("Interrupted by user", $import['task_id']);
			sql_pr("update $config[tables_prefix]background_imports set status_id=3 where import_id=?", $import['import_id']);
			continue;
		}

		$pc = 0;
		$import_stats = mr2array_single(sql_pr("select count(*) as total, sum(case when status_id=1 then 1 else 0 end) as finished from (select status_id from $config[tables_prefix]background_imports_data where import_id=?) x", $import['import_id']));
		if (intval($import_stats['total']) > 0)
		{
			$pc = floor(intval($import_stats['finished']) / intval($import_stats['total']) * 100);
			$fp = fopen("$config[project_path]/admin/data/engine/tasks/$import[task_id].dat", "w+");
			fwrite($fp, "$pc");
			fclose($fp);
		}
		log_output("$pc% done", $import['task_id']);

		if ($pc == 100)
		{
			sql_pr("update $config[tables_prefix]background_imports set status_id=2 where import_id=?", $import['import_id']);

			$task_data = mr2array_single(sql_pr("select * from $config[tables_prefix]background_tasks where task_id=?", $import['task_id']));
			sql_pr("delete from $config[tables_prefix]background_tasks where task_id=?", $import['task_id']);
			if ($task_data['task_id'] > 0)
			{
				sql_pr("insert into $config[tables_prefix]background_tasks_history set task_id=?, status_id=3, type_id=?, start_date=?, end_date=?, effective_duration=UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)", $task_data['task_id'], $task_data['type_id'], $task_data['start_date'], date("Y-m-d H:i:s"));
			}
			@unlink("$config[project_path]/admin/data/engine/tasks/$import[task_id].dat");
		} else
		{
			for ($i = 1; $i <= max(1, intval($import['threads'])); $i++)
			{
				$import_lock_file = "$config[project_path]/admin/data/engine/import/import_{$import['import_id']}_{$i}.lock";
				if (is_file($import_lock_file))
				{
					$import_lock = fopen($import_lock_file, "r+");
					if (flock($import_lock, LOCK_EX | LOCK_NB))
					{
						flock($import_lock, LOCK_UN);
						fclose($import_lock);

						log_output("Restarting import thread $i", $import['task_id']);
						$import_script = 'background_import.php';
						if ($import['type_id'] == 2)
						{
							$import_script = 'background_import_albums.php';
						}
						exec("$config[php_path] $config[project_path]/admin/$import_script $import[import_id] import english $import[admin_id] $import[task_id] $i >> $config[project_path]/admin/logs/tasks/$import[task_id]_$i.txt 2>&1 &");
						sleep(2);
					}
				} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports_data where import_id=? and thread_id=? and status_id=0", $import['import_id'], $i)) > 0)
				{
					log_output("Restarting import thread $i", $import['task_id']);
					$import_script = 'background_import.php';
					if ($import['type_id'] == 2)
					{
						$import_script = 'background_import_albums.php';
					}
					exec("$config[php_path] $config[project_path]/admin/$import_script $import[import_id] import english $import[admin_id] $import[task_id] $i >> $config[project_path]/admin/logs/tasks/$import[task_id]_$i.txt 2>&1 &");
					sleep(2);
				}
			}
		}
	}
}

flock($lock, LOCK_UN);
fclose($lock);

function log_output($message, $task_id = 0)
{
	global $config;

	if ($message != '')
	{
		$message = date("[Y-m-d H:i:s] ") . $message;
	}
	echo "$message\n";

	if (intval($task_id) > 0)
	{
		file_put_contents("$config[project_path]/admin/logs/tasks/$task_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}
