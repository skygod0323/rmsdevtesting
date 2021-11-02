<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id']<1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once("setup.php");
require_once("functions_base.php");

if (!is_file("$config[project_path]/admin/data/system/cron_plugins.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_plugins.lock", "1", LOCK_EX);
}

$lock=fopen("$config[project_path]/admin/data/system/cron_plugins.lock","r+");
if (!flock($lock,LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors',1);

$memory_limit=mr2number(sql("select value from $config[tables_prefix]options where variable='LIMIT_MEMORY'"));
if ($memory_limit==0)
{
	$memory_limit=512;
}
ini_set('memory_limit',"{$memory_limit}M");

sql("set wait_timeout=86400");

$plugins_to_execute=array();
$plugins_list=get_contents_from_dir("$config[project_path]/admin/plugins",2);
foreach ($plugins_list as $k=>$v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php"))
	{
		continue;
	}
	require_once("$config[project_path]/admin/plugins/$v/$v.php");
	$init_function="{$v}Init";
	$cron_function="{$v}Cron";
	if (!function_exists($init_function) || !function_exists($cron_function))
	{
		continue;
	}
	$init_function();
	if (!is_file("$config[project_path]/admin/data/plugins/$v/cron.dat"))
	{
		log_output("DEBUG Plugin $v is not scheduled on cron");
		continue;
	}
	$next_start_date=intval(file_get_contents("$config[project_path]/admin/data/plugins/$v/cron.dat"));
	if (time()>=$next_start_date)
	{
		log_output("DEBUG Plugin $v will be executed now");
		$plugins_to_execute[]=$v;
	} else {
		$ttw=$next_start_date-time();
		log_output("DEBUG Plugin $v will be executed in $ttw seconds");
	}
}

if (count($plugins_to_execute)==0)
{
	die('No plugins to process now');
}

log_output("INFO  Plugins processor started");
log_output("INFO  Memory limit: ".ini_get('memory_limit'));

// execute plugins
foreach ($plugins_to_execute as $v)
{
	require_once("$config[project_path]/admin/plugins/$v/$v.php");
	$cron_function="{$v}Cron";
	if (function_exists($cron_function))
	{
		log_output("INFO  Starting $v plugin");
		@unlink("$config[project_path]/admin/logs/plugins/$v.txt");
		$cron_function();
	}
}

log_output("INFO  Plugins processor finished");

flock($lock, LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message=='')
	{
		echo "\n";
	} else {
		echo date("[Y-m-d H:i:s] ").$message."\n";
	}
}
