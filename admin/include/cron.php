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

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_servers.php";
require_once "functions.php";

$stats_cron=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/cron.dat"));

if ($_SERVER['PWD']<>'')
{
	if ($_SERVER['PWD']<>"$config[project_path]/admin/include")
	{
		$stats_cron['cron_error']=1;
		$stats_cron['folder']=$_SERVER['PWD'];

		file_put_contents("$config[project_path]/admin/data/system/cron.dat", serialize($stats_cron), LOCK_EX);
		die('PWD is not valid');
	}
}

$options = get_options(['CRON_TIME', 'CRON_UID', 'ROTATOR_SCHEDULE_INTERVAL']);
if ($config['is_clone_db'] != "true")
{
	$cron_uid = gethostname() . ':' . $config['project_path'];
	if (intval($options['CRON_TIME']) > 0 && $options['CRON_UID'])
	{
		if (time() - intval($options['CRON_TIME']) < 15 * 60 && $cron_uid != $options['CRON_UID'])
		{
			$stats_cron['cron_error']=2;
			$stats_cron['cron_uid']=$options['CRON_UID'];

			file_put_contents("$config[project_path]/admin/data/system/cron.dat", serialize($stats_cron), LOCK_EX);
			die('Duplicate cron operation');
		}
	}
	sql_update("update $config[tables_prefix]options set value=(case variable when 'CRON_TIME' then ? when 'CRON_UID' then ? end) where variable in ('CRON_TIME', 'CRON_UID')", time(), $cron_uid);
}

if (time()-$stats_cron['cron_last_time']<55) {die('Already started');}

file_put_contents("$config[project_path]/admin/logs/cron.txt", "", LOCK_EX);

$start_time=time();

if (!is_file("$config[project_path]/admin/data/system/initial_version.dat"))
{
	require_once "$config[project_path]/admin/tools/post_install.php";
	kvs_post_install();
}

$la=get_LA();
if ($la>$config['overload_max_la_cron'])
{
	file_put_contents("$config[project_path]/admin/data/stats/overload.dat", date("Y-m-d")."|5\r\n", FILE_APPEND | LOCK_EX);
} else {
	if ($config['is_clone_db']<>"true")
	{
		if (time()-$stats_cron['check_db']>3600)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_check_db.php > $config[project_path]/admin/logs/cron_check_db.txt 2>&1 &");
			log_output("Executed database check (cron_check_db)");
			$stats_cron['check_db']=time();
		}
		if (time()-$stats_cron['update_stats']>300)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_stats.php > $config[project_path]/admin/logs/cron_stats.txt 2>&1 &");
			log_output("Executed stats update (cron_stats)");
			$stats_cron['update_stats']=time();
		}
		if (time()-$stats_cron['update_servers']>300)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_servers.php > $config[project_path]/admin/logs/cron_servers.txt 2>&1 &");
			log_output("Executed servers check (cron_servers)");
			$stats_cron['update_servers']=time();
		}
		if (time()-$stats_cron['cleanup']>14400)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_cleanup.php > $config[project_path]/admin/logs/cron_cleanup.txt 2>&1 &");
			log_output("Executed cleanup (cron_cleanup)");
			$stats_cron['cleanup']=time();
		}
		if (time()-$stats_cron['optimize']>3600)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_optimize.php > $config[project_path]/admin/logs/cron_optimize.txt 2>&1 &");
			log_output("Executed optimization (cron_optimize)");
			$stats_cron['optimize']=time();
		}

		$rotator_interval=intval($options['ROTATOR_SCHEDULE_INTERVAL'])*60;
		if ($rotator_interval==0)
		{
			$rotator_interval=300;
		}
		if (time()-$stats_cron['rotator']>$rotator_interval)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_rotator.php > $config[project_path]/admin/logs/cron_rotator.txt 2>&1 &");
			log_output("Executed rotator (cron_rotator)");
			$stats_cron['rotator']=time();
		}
		if (is_file("$config[project_path]/admin/include/cron_custom.php"))
		{
			if (time()-$stats_cron['custom']>300)
			{
				exec("$config[php_path] $config[project_path]/admin/include/cron_custom.php > $config[project_path]/admin/logs/cron_custom.txt 2>&1 &");
				log_output("Executed custom cron (cron_custom)");
				$stats_cron['custom']=time();
			}
		}

		exec("$config[php_path] $config[project_path]/admin/include/cron_import.php > $config[project_path]/admin/logs/cron_import.txt 2>&1 &");
		log_output("Executed import (cron_import)");

		exec("$config[php_path] $config[project_path]/admin/include/cron_feeds.php > $config[project_path]/admin/logs/cron_feeds.txt 2>&1 &");
		log_output("Executed feeds (cron_feeds)");

		exec("$config[php_path] $config[project_path]/admin/include/cron_conversion.php > /dev/null &");
		exec("$config[php_path] $config[project_path]/admin/include/cron_postponed_tasks.php > /dev/null &");
		log_output("Executed conversion engine (cron_conversion)");

		$servers_conversion=mr2array(sql("select * from $config[tables_prefix]admin_conversion_servers where connection_type_id=0 and status_id in (1,2)"));
		foreach ($servers_conversion as $server)
		{
			if (is_file("$server[path]/remote_cron.php"))
			{
				chdir($server['path']);
				exec("$config[php_path] $server[path]/remote_cron.php > $server[path]/cron_log.txt 2>&1 &");
				log_output("Executed local conversion server ($server[title])");
			}
		}
		chdir("$config[project_path]/admin/include");

		if (time()-$stats_cron['plugins']>3600)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_plugins.php > $config[project_path]/admin/logs/cron_plugins.txt 2>&1 &");
			log_output("Executed plugins (cron_plugins)");
			$stats_cron['plugins']=time();
		}
		if (time()-$stats_cron['billings']>10*60)
		{
			// execute cron for billing processors
			$processors_list=mr2array_list(sql("select internal_id from $config[tables_prefix]card_bill_providers"));
			foreach ($processors_list as $processor_internal_id)
			{
				if (is_file("$config[project_path]/admin/billings/$processor_internal_id/$processor_internal_id.php"))
				{
					require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
					require_once "$config[project_path]/admin/billings/$processor_internal_id/$processor_internal_id.php";
					$payment_processor = KvsPaymentProcessorFactory::create_instance($processor_internal_id);
					if ($payment_processor instanceof KvsPaymentProcessor)
					{
						$payment_processor->process_schedule();
					}
				}
			}
			$stats_cron['billings']=time();
			log_output("Executed billings");
		}
	} else {
		if (time()-$stats_cron['check_db']>3600)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_check_db.php > $config[project_path]/admin/logs/cron_check_db.txt 2>&1 &");
			log_output("Executed database check (cron_check_db)");
			$stats_cron['check_db']=time();
		}
		if (time()-$stats_cron['update_stats']>300)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_stats.php > $config[project_path]/admin/logs/cron_stats.txt 2>&1 &");
			log_output("Executed stats update (cron_stats)");
			$stats_cron['update_stats']=time();
		}
		if (time()-$stats_cron['cleanup']>14400)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_cleanup.php > $config[project_path]/admin/logs/cron_cleanup.txt 2>&1 &");
			log_output("Executed cleanup (cron_cleanup)");
			$stats_cron['cleanup']=time();
		}
		if (time()-$stats_cron['clone_db']>60)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_clone_db.php > $config[project_path]/admin/logs/cron_clone_db.txt 2>&1 &");
			log_output("Executed database clone check (cron_clone_db)");
			$stats_cron['clone_db']=time();
		}
		if (time()-$stats_cron['plugins']>3600)
		{
			exec("$config[php_path] $config[project_path]/admin/include/cron_plugins.php > $config[project_path]/admin/logs/cron_plugins.txt 2>&1 &");
			log_output("Executed plugins (cron_plugins)");
			$stats_cron['plugins']=time();
		}

		if (is_file("$config[project_path]/admin/include/cron_custom.php"))
		{
			if (time()-$stats_cron['custom']>300)
			{
				exec("$config[php_path] $config[project_path]/admin/include/cron_custom.php > $config[project_path]/admin/logs/cron_custom.txt 2>&1 &");
				log_output("Executed custom cron (cron_custom)");
				$stats_cron['custom']=time();
			}
		}
	}
}



// update cron execution time
$stats_cron['cron_error']=0;
$stats_cron['cron_last_time']=time();

file_put_contents("$config[project_path]/admin/data/system/cron.dat", serialize($stats_cron), LOCK_EX);

$time=time()-$start_time;
log_output("Finished in $time seconds");

function log_output($message)
{
	global $config;

	if ($message=='')
	{
		$message="\n";
	} else {
		$message=date("[Y-m-d H:i:s] ").$message."\n";
	}

	echo $message;
	file_put_contents("$config[project_path]/admin/logs/cron.txt", $message, FILE_APPEND | LOCK_EX);
}
