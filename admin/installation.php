<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$log_files = array('cron.txt', 'cron_import.txt', 'cron_check_db.txt', 'cron_stats.txt', 'cron_servers.txt', 'cron_cleanup.txt', 'cron_optimize.txt', 'cron_feeds.txt', 'cron_rotator.txt', 'cron_custom.txt', 'cron_conversion.txt', 'cron_postponed_tasks.txt', 'cron_plugins.txt', 'cron_clone_db.txt', 'log_mysql_errors.txt', 'log_php_errors.txt', 'log_curl_errors.txt', 'log_player_errors.txt', 'api.txt', 'overload.txt', 'blocked_ips.txt', 'uploader.txt');
$engine_customization_files = array('admin/include/setup.php', 'admin/include/setup_smarty.php', 'admin/include/setup_smarty_site.php', 'admin/include/pre_initialize_page_code.php', 'admin/include/pre_process_page_code.php', 'admin/include/pre_display_page_code.php', 'admin/include/pre_async_action_code.php', 'admin/include/post_process_page_code.php', 'admin/include/cron_custom.php', '.htaccess', '.htaccess_mobile');

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
foreach ($plugins_list as $plugin_id)
{
	if (is_file("$config[project_path]/admin/logs/plugins/$plugin_id.txt"))
	{
		$log_files[] = "plugins/$plugin_id.txt";
	}
}

if ($_REQUEST['action'] == 'get_info')
{
	/** @noinspection ForgottenDebugOutputInspection */
	phpinfo();
	die;
}

if ($_REQUEST['action'] == 'get_log' && intval($_REQUEST['log_index']) > 0)
{
	$log_file = $log_files[intval($_REQUEST['log_index']) - 1];
	if (is_file("$config[project_path]/admin/logs/$log_file"))
	{
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
	}
	die;
}
if ($_REQUEST['action'] == 'get_customization_file' && intval($_REQUEST['file_index']) > 0)
{
	$customization_file = $engine_customization_files[intval($_REQUEST['file_index']) - 1];
	if (is_file("$config[project_path]/$customization_file"))
	{
		header("Content-Type: text/plain; charset=utf8");
		header("Content-Disposition: inline; filename=\"$customization_file\"");
		readfile("$config[project_path]/$customization_file");
	}
	die;
}


$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');

$data = array();
foreach ($config as $k => $v)
{
	$item = array();
	$item['key'] = $k;
	$item['value'] = $v;
	$data[] = $item;
}

$logs = array();
$i = 1;
foreach ($log_files as $log_file)
{
	if (is_file("$config[project_path]/admin/logs/$log_file"))
	{
		$item = array();
		$item['file_index'] = $i;
		$item['file_name'] = $log_file;
		$item['file_time'] = filemtime("$config[project_path]/admin/logs/$log_file");
		$item['file_size'] = sizeToHumanString(filesize("$config[project_path]/admin/logs/$log_file"), 2);
		$logs[] = $item;
	}
	$i++;
}

$engine_customizations = array();
$i = 1;
foreach ($engine_customization_files as $customization_file)
{
	if (is_file("$config[project_path]/$customization_file"))
	{
		$item = array();
		$item['file_index'] = $i;
		$item['file_name'] = $customization_file;
		$item['file_time'] = filemtime("$config[project_path]/$customization_file");
		$item['file_size'] = sizeToHumanString(filesize("$config[project_path]/$customization_file"), 2);
		$engine_customizations[] = $item;
	}
	$i++;
}

$monitored_vars = array('date.timezone', 'allow_url_fopen', 'file_uploads', 'max_execution_time', 'max_input_time', 'max_input_vars', 'memory_limit', 'post_max_size', 'open_basedir', 'sendmail_path', 'session.cookie_domain', 'session.save_handler', 'session.save_path', 'session.gc_maxlifetime', 'upload_max_filesize', 'upload_tmp_dir', 'max_file_uploads', 'disable_functions');
$ini_vars = ini_get_all();
foreach ($ini_vars as $k => $v)
{
	if (!in_array($k, $monitored_vars))
	{
		unset($ini_vars[$k]);
	}
}
$ini_vars['date.timezone']['local_value'] = date_default_timezone_get();

$memcache_stats = array();
if ($config['memcache_server'] <> '' && class_exists('Memcached'))
{
	$memcache = new Memcached();
	if ($memcache->addServer($config['memcache_server'], $config['memcache_port']))
	{
		$memcache_total_bytes = 0;
		$memcache_used_bytes = 0;
		$memcache_get_hits = 0;
		$memcache_get_misses = 0;
		foreach ($memcache->getStats() as $server)
		{
			$memcache_total_bytes += $server['limit_maxbytes'];
			$memcache_used_bytes += $server['bytes'];
			$memcache_get_hits += $server['get_hits'];
			$memcache_get_misses += $server['get_misses'];
		}
		if ($memcache_total_bytes > 0)
		{
			$memcache_stats['memcache_usage_percent'] = floor($memcache_used_bytes / $memcache_total_bytes * 100);
			$memcache_stats['memcache_total_memory'] = sizeToHumanString($memcache_total_bytes);
			$memcache_stats['memcache_used_memory'] = sizeToHumanString($memcache_used_bytes);
			$memcache_stats['memcache_total_hits'] = $memcache_get_hits + $memcache_get_misses;
			$memcache_stats['memcache_success_hits'] = $memcache_get_hits;
			$memcache_stats['memcache_success_percent'] = @floor($memcache_get_hits / ($memcache_get_hits + $memcache_get_misses) * 100);
		}
	}
}

$system = array();

$exec_res = array();
exec("$config[php_path] -v 2>&1", $exec_res);
$system[] = array('name' => 'PHP CLI', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

$exec_res = array();
exec("$config[ffmpeg_path] -version 2>&1", $exec_res);
$system[] = array('name' => 'FFmpeg', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

$exec_res = array();
exec("$config[image_magick_path] 2>&1", $exec_res);
$system[] = array('name' => 'ImageMagick', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

$exec_res = array();
$wget_path = $config['wget_path'];
if ($wget_path == '' || $wget_path == 'disabled')
{
	$wget_path = 'wget';
}
exec("$wget_path -V 2>&1", $exec_res);
$system[] = array('name' => 'WGet', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

$curl_test_url = "http://www.google.com";
if ($_REQUEST['curl_test_url'] != '')
{
	$curl_test_url = trim($_REQUEST['curl_test_url']);
}

$exec_res = array();
exec("curl -I -L $curl_test_url", $exec_res);
if (trim(implode("\n", $exec_res)) == '')
{
	$exec_res = array();
	exec("curl -I -L $curl_test_url 2>&1", $exec_res);
}
$system[] = array('name' => 'cURL (console)', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

if (function_exists('curl_init'))
{
	$exec_res = get_page("", $curl_test_url, "", "", 0, 1, 10, "", array('return_error' => true));
	$system[] = array('name' => 'cURL (PHP)', 'type' => 'multiline', 'value' => $exec_res);
}

$exec_res = array();
exec("ps -ax", $exec_res);
foreach ($exec_res as $k => $v)
{
	if (strpos($v, 'php') === false && strpos($v, 'convert') === false && strpos($v, 'ffmpeg') === false)
	{
		unset($exec_res[$k]);
	}
}
$system[] = array('name' => 'ProcessStats', 'type' => 'multiline', 'value' => implode("\n", $exec_res));

$sql_queries = array();
$exec_res = mr2array(sql_pr("show full processlist"));
foreach ($exec_res as $v)
{
	if ($v['Command'] != 'Sleep' && $v['Info'] != 'show full processlist')
	{
		$sql_queries[] = "Query $v[Id]: $v[Info] ($v[State], $v[Time]s)";
	}
}
$system[] = array('name' => 'DatabaseStats', 'type' => 'multiline', 'value' => implode("\n", $sql_queries));

$system[] = array('name' => 'Cron', 'value' => "cd $config[project_path]/admin/include && $config[php_path] cron.php > /dev/null 2>&1");

$smarty->assign('data', $data);
$smarty->assign('phpversion', PHP_VERSION);
$smarty->assign('system', $system);
$smarty->assign('logs', $logs);
$smarty->assign('engine_customizations', $engine_customizations);
$smarty->assign('ini_vars', $ini_vars);
$smarty->assign('memcache_stats', $memcache_stats);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['installation_header']);

$smarty->display("layout.tpl");
