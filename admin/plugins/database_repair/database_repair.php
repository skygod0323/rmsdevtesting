<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function database_repairInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins");
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/database_repair";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path);
		chmod($plugin_path, 0777);
	}
}

function database_repairShow()
{
	global $config, $page_name, $database_tables;

	database_repairInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/database_repair";

	if ($_GET['action'] == 'progress')
	{
		$task_id = intval($_GET['task_id']);
		$pc = intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location = '';
		if ($pc == 100)
		{
			$location = "<location>plugins.php?plugin_id=database_repair&amp;full_check=true\");</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>";
		die;
	} elseif ($_POST['action'] == 'repair')
	{
		if (isset($_POST['full_check']))
		{
			return_ajax_success("$page_name?plugin_id=database_repair&amp;full_check=true");
		} elseif (isset($_POST['repair']))
		{
			mt_srand(time());
			$rnd = mt_rand(10000000, 99999999);

			exec("$config[php_path] $config[project_path]/admin/plugins/database_repair/database_repair.php $rnd > /dev/null &");
			return_ajax_success("$page_name?plugin_id=database_repair&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}", 2);
		} else
		{
			foreach ($_POST['kill_queries'] as $query_id)
			{
				sql('kill query ' . intval($query_id));
			}
		}
		return_ajax_success("$page_name?plugin_id=database_repair");
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}

	require_once 'include/database_tables.php';

	$config['sql_safe_mode'] = 1;

	$data = array();
	foreach ($database_tables as $table)
	{
		$data[] = database_repairCheckTableStatus($table, $_REQUEST['full_check'] == 'true');
	}

	unset($config['sql_safe_mode']);

	$has_errors = 0;
	foreach ($data as $table_rec)
	{
		foreach ($table_rec['status'] as $status_rec)
		{
			if (strtolower($status_rec['Msg_type']) == 'error')
			{
				$has_errors = 1;
				break 2;
			}
		}
	}

	$queries = mr2array(sql("show full processlist"));
	foreach ($queries as $k => $v)
	{
		if ($v['Command'] == 'Sleep' || $v['Info'] == 'show full processlist')
		{
			unset($queries[$k]);
		}
	}

	$version = '';
	$version_comment = '';
	$version_vars = mr2array(sql("show variables like '%version%'"));
	foreach ($version_vars as $version_var)
	{
		if ($version_var['Variable_name'] == 'version')
		{
			$version = $version_var['Value'];
		} elseif ($version_var['Variable_name'] == 'version_comment')
		{
			$version_comment = $version_var['Value'];
		}
	}

	if ($version)
	{
		$_POST['database_version'] = "$version ($version_comment)";
	}
	$_POST['queries'] = $queries;
	$_POST['data'] = $data;
	$_POST['has_errors'] = $has_errors;
}

function database_repairCheckTableStatus($table, $full_check)
{
	$info = mr2array_single(sql("show table status in `" . DB_DEVICE . "` where Name='$table'"));
	if ($full_check)
	{
		$status = mr2array(sql("check table $table medium"));
	} else
	{
		$status = [['Msg_type' => '?', 'Msg_text' => '']];
	}
	return array('table' => $table, 'engine' => $info['Engine'], 'rows' => $info['Rows'], 'size' => sizeToHumanString($info['Data_length']), 'status' => $status);
}

$task_id = intval($_SERVER['argv'][1]);

if ($task_id > 0 && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'include/setup.php';
	require_once 'include/functions_base.php';
	require_once 'include/functions.php';
	require_once 'include/database_tables.php';

	$plugin_path = "$config[project_path]/admin/data/plugins/database_repair";

	$config['sql_safe_mode'] = 1;

	$data = array();
	foreach ($database_tables as $table)
	{
		$data[] = database_repairCheckTableStatus($table, true);
	}

	foreach ($data as $table_rec)
	{
		foreach ($table_rec['status'] as $status_rec)
		{
			if (strtolower($status_rec['Msg_type']) == 'error')
			{
				sql("repair table $table_rec[table]");
			}
		}
	}

	unset($config['sql_safe_mode']);

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
