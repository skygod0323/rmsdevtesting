<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'])
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

require_once 'setup.php';
require_once 'setup_smarty_site.php';
require_once 'functions_base.php';
require_once 'functions_admin.php';
require_once 'functions_servers.php';
require_once 'functions.php';
require_once 'database_tables.php';

if (!is_file("$config[project_path]/admin/data/system/cron_check_db.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_check_db.lock", "1", LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_check_db.lock", "r+");
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

$config['sql_safe_mode'] = 1;
foreach ($database_tables as $table)
{
	check_table_status($table);
}
unset($config['sql_safe_mode']);

@unlink("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat");
log_output('MySQL check done');

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');

$temp = mr2array(sql_pr("select path, hash from $config[tables_prefix_multi]file_changes"));
$hashs = [];
foreach ($temp as $k)
{
	$hashs["$config[project_path]{$k['path']}"] = $k['hash'];
}

$path = "$config[project_path]/.htaccess";
check_path($path, $hashs[$path]);

$pages = get_site_pages();
$pages[] = array("external_id" => "\$global");
foreach ($pages as $page)
{
	$path = "$config[project_path]/$page[external_id].php";
	check_path($path, $hashs[$path]);
	if (is_dir("$site_templates_path/blocks/$page[external_id]"))
	{
		$block_templates = scandir("$site_templates_path/blocks/$page[external_id]");
		foreach ($block_templates as $block_template)
		{
			if (strpos($block_template, '.tpl') !== false)
			{
				$path = "$site_templates_path/blocks/$page[external_id]/$block_template";
				check_path($path, $hashs[$path]);
			}
		}
	}
}

$templates = scandir($site_templates_path);
foreach ($templates as $template)
{
	if (strpos($template, '.tpl') !== false)
	{
		$path = "$site_templates_path/$template";
		check_path($path, $hashs[$path]);
	}
}

if (is_dir("$config[project_path]/js"))
{
	$static_files = scandir("$config[project_path]/js");
	foreach ($static_files as $static_file)
	{
		if (strpos($static_file, '.js') !== false)
		{
			$path = "$config[project_path]/js/$static_file";
			check_path($path, $hashs[$path]);
		}
	}
}

if (is_dir("$config[project_path]/styles"))
{
	$static_files = scandir("$config[project_path]/styles");
	foreach ($static_files as $static_file)
	{
		if (strpos($static_file, '.css') !== false)
		{
			$path = "$config[project_path]/styles/$static_file";
			check_path($path, $hashs[$path]);
		}
	}
}

if (is_dir("$config[project_path]/css"))
{
	$static_files = scandir("$config[project_path]/css");
	foreach ($static_files as $static_file)
	{
		if (strpos($static_file, '.css') !== false)
		{
			$path = "$config[project_path]/css/$static_file";
			check_path($path, $hashs[$path]);
		}
	}
}

if (is_dir("$config[project_path]/static"))
{
	$static_files = scandir("$config[project_path]/static");
	foreach ($static_files as $static_file)
	{
		if (strpos($static_file, '.js') !== false || strpos($static_file, '.css') !== false)
		{
			$path = "$config[project_path]/static/$static_file";
			check_path($path, $hashs[$path]);
		}
	}

	if (is_dir("$config[project_path]/static/js"))
	{
		$static_files = scandir("$config[project_path]/static/js");
		foreach ($static_files as $static_file)
		{
			if (strpos($static_file, '.js') !== false)
			{
				$path = "$config[project_path]/static/js/$static_file";
				check_path($path, $hashs[$path]);
			}
		}
	}

	if (is_dir("$config[project_path]/static/styles"))
	{
		$static_files = scandir("$config[project_path]/static/styles");
		foreach ($static_files as $static_file)
		{
			if (strpos($static_file, '.css') !== false)
			{
				$path = "$config[project_path]/static/styles/$static_file";
				check_path($path, $hashs[$path]);
			}
		}
	}

	if (is_dir("$config[project_path]/static/css"))
	{
		$static_files = scandir("$config[project_path]/static/css");
		foreach ($static_files as $static_file)
		{
			if (strpos($static_file, '.css') !== false)
			{
				$path = "$config[project_path]/static/css/$static_file";
				check_path($path, $hashs[$path]);
			}
		}
	}
}

$other_paths = [
		"$config[project_path]/admin/include/pre_initialize_page_code.php",
		"$config[project_path]/admin/include/pre_display_page_code.php",
		"$config[project_path]/admin/include/pre_process_page_code.php",
		"$config[project_path]/admin/include/pre_async_action_code.php",
		"$config[project_path]/admin/include/post_process_page_code.php",
		"$config[project_path]/admin/data/.htaccess",
		"$config[project_path]/admin/logs/.htaccess",
		"$config[project_path]/admin/plugins/.htaccess",
		"$config[project_path]/admin/smarty/.htaccess",
		"$config[project_path]/admin/stamp/.htaccess",
		"$config[project_path]/admin/template/.htaccess",
		"$config[project_path]/admin/tools/.htaccess",
		"$config[project_path]/blocks/.htaccess",
		"$config[project_path]/langs/default.php",
		"$config[project_path]/langs/.htaccess",
		"$config[project_path]/template/.htaccess",
		"$config[project_path]/tmp/.htaccess",
];
foreach ($other_paths as $other_path)
{
	check_path($other_path, $hashs[$other_path]);
}

log_output('Theme files check done');

$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];
$new_vast_key_data = @json_decode(get_page('', "https://www.kernel-scripts.com/get_vast.php?domain=$config[project_licence_domain]&license_code=$config[player_license_code]", '', '', 1, 0, 5, ''), true);
if (is_array($new_vast_key_data) && $new_vast_key_data['domain'] == $config['project_licence_domain'])
{
	if (!$vast_key_data['primary_vast_key'])
	{
		mkdir_recursive("$config[project_path]/admin/data/player/vast");
		file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
		file_put_contents("$config[project_path]/admin/data/player/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
		file_put_contents("$config[project_path]/admin/data/player/embed/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
		log_output("Player VAST key updated to: $new_vast_key_data[primary_vast_key]");
	} else
	{
		$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
		$new_vast_key_valid = intval(substr($new_vast_key_data['primary_vast_key'], 0, 10));
		if ($new_vast_key_valid > $vast_key_valid || $new_vast_key_data['aliases_hash'] != $vast_key_data['aliases_hash'])
		{
			mkdir_recursive("$config[project_path]/admin/data/player/vast");
			file_put_contents("$config[project_path]/admin/data/player/vast/key.dat", serialize($new_vast_key_data), LOCK_EX);
			file_put_contents("$config[project_path]/admin/data/player/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
			file_put_contents("$config[project_path]/admin/data/player/embed/version.dat", md5(serialize($new_vast_key_data)), LOCK_EX);
			log_output("Player VAST key updated to: $new_vast_key_data[primary_vast_key]");
		}
	}
}

flock($lock, LOCK_UN);
fclose($lock);

function check_table_status($table)
{
	global $config;

	if (is_file("$config[project_path]/admin/data/engine/checks/mysql_corrupted.dat"))
	{
		$result = mr2array(sql("check table $table medium"));
		foreach ($result as $row)
		{
			if (strtolower($row['Msg_type']) == 'error' || strtolower($row['Msg_type']) == 'warning')
			{
				log_output("Repairing table $table");
				sql("repair table $table");
				return;
			}
		}
	} else
	{
		$result = mr2string(sql("select count(*) from $table"));
		if ($result === '')
		{
			log_output("Repairing table $table");
			sql("repair table $table");
		}
	}
}

function check_path($file, $hash)
{
	global $config;

	if (!is_file($file))
	{
		return;
	}
	if (filesize($file) > 1024 * 1024)
	{
		return;
	}
	$path = str_replace($config['project_path'], '', $file);
	$file_content = @file_get_contents($file);
	$hash_check = md5($file_content);
	if ($hash == '')
	{
		sql_insert("insert into $config[tables_prefix_multi]file_changes set path=?, hash=?, file_content=?, modified_date=?, is_modified=0", $path, $hash_check, $file_content, date('Y-m-d H:i:s', filectime($file)));
	} elseif ($hash <> $hash_check)
	{
		sql_update("update $config[tables_prefix_multi]file_changes set modified_date=?, is_modified=1 where path=?", date('Y-m-d H:i:s', filectime($file)), $path);
	}

	$last_version = mr2array_single(sql_pr("select version, hash from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
	if ($hash_check != $last_version['hash'])
	{
		sql_insert("insert into $config[tables_prefix_multi]file_history set path=?, hash=?, version=?, file_content=?, user_id=0, username='filesystem', added_date=?", $path, $hash_check, intval($last_version['version']) + 1, $file_content, date('Y-m-d H:i:s', filectime($file)));
	}
}

function log_output($message)
{
	if (!$message)
	{
		echo "\n";
	} else
	{
		echo date('[Y-m-d H:i:s] ') . $message . "\n";
	}
}
