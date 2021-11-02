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
require_once("functions.php");

if (!is_file("$config[project_path]/admin/data/system/cron_cleanup.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_cleanup.lock", "1", LOCK_EX);
}

$lock=fopen("$config[project_path]/admin/data/system/cron_cleanup.lock","r+");
if (!flock($lock,LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

$stats_cron=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/cron_cleanup.dat"));

//clear temp directory
if (time()-$stats_cron['clear_temp_directory']>14400)
{
	if ($config['temporary_path']<>'' && $config['temporary_path']<>$config['project_path'] && is_writable($config['temporary_path']))
	{
		exec("find $config[temporary_path] \( -iname \"*\" ! -iname \".htaccess\" \) -mtime +1 -not -path $config[temporary_path] -delete");
	}
	if ($config['content_path_videos_screenshots']<>'' && is_writable("$config[content_path_videos_screenshots]/temp"))
	{
		exec("find $config[content_path_videos_screenshots]/temp -mtime +1 -delete");
	}
	$stats_cron['clear_temp_directory']=time();
	log_output("Temp directory cleanup done");
}

//clear old stats
if (time()-$stats_cron['clear_old_stats']>86400)
{
	$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));

	if (intval($stats_settings['keep_traffic_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_traffic_stats_period'])*86400);
		log_output("Removing traffic stats before $keep_from_date");

		sql("delete from $config[tables_prefix_multi]stats_cs_out where added_date<'$keep_from_date'");
		sql("delete from $config[tables_prefix_multi]stats_adv_out where added_date<'$keep_from_date'");
		sql("delete from $config[tables_prefix_multi]stats_in where added_date<'$keep_from_date'");
		sql("delete from $config[tables_prefix_multi]stats_embed where added_date<'$keep_from_date'");
		sql("delete from $config[tables_prefix_multi]stats_overload_protection where added_date<'$keep_from_date'");
	}
	if (intval($stats_settings['keep_player_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_player_stats_period'])*86400);
		log_output("Removing player before $keep_from_date");

		sql("delete from $config[tables_prefix_multi]stats_player where added_date<'$keep_from_date'");
	}
	if (intval($stats_settings['keep_videos_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_videos_stats_period'])*86400);
		log_output("Removing video stats before $keep_from_date");

		sql("delete from $config[tables_prefix]stats_videos where added_date<'$keep_from_date'");
	}
	if (intval($stats_settings['keep_albums_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_albums_stats_period'])*86400);
		log_output("Removing album stats before $keep_from_date");

		sql("delete from $config[tables_prefix]stats_albums where added_date<'$keep_from_date'");
	}
	if (intval($stats_settings['keep_search_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_search_stats_period'])*86400);
		log_output("Removing search stats before $keep_from_date");

		sql("delete from $config[tables_prefix_multi]stats_search where is_manual=0 and added_date<'$keep_from_date'");
	}
	if (intval($stats_settings['keep_memberzone_stats_period'])>0)
	{
		$keep_from_date=date("Y-m-d H:i:s",time()-intval($stats_settings['keep_memberzone_stats_period'])*86400);
		log_output("Removing memberzone stats before $keep_from_date");

		sql("delete from $config[tables_prefix]log_content_users where added_date<'$keep_from_date'");
	}

	$keep_from_date=date("Y-m-d H:i:s",time()-365*86400);
	log_output("Removing rating and flagging history before $keep_from_date");

	sql("delete from $config[tables_prefix]rating_history where added_date<'$keep_from_date'");
	sql("delete from $config[tables_prefix]flags_history where added_date<'$keep_from_date'");

	$keep_from_date=date("Y-m-d H:i:s",time()-86400);
	log_output("Removing content uniqueness records before $keep_from_date");

	sql("delete from $config[tables_prefix]albums_visits where added_date<'$keep_from_date'");
	sql("delete from $config[tables_prefix]videos_visits where added_date<'$keep_from_date'");

	$keep_from_date=date("Y-m-d H:i:s",time()-86400);
	log_output("Removing failed logins before $keep_from_date");

	sql("delete from $config[tables_prefix]log_logins_users where is_failed=1 and login_date<'$keep_from_date'");

	$feeds = mr2array(sql_pr("select feed_id, keep_log_days from $config[tables_prefix]videos_feeds_import where keep_log_days>0"));
	foreach ($feeds as $feed)
	{
		$keep_from_date = date("Y-m-d H:i:s", time() - $feed['keep_log_days'] * 86400);
		log_output("Removing feed #$feed[feed_id] log before $keep_from_date");
		sql_delete("delete from $config[tables_prefix]feeds_log where feed_id=? and added_date<?", $feed['feed_id'], $keep_from_date);
	}

	$keep_from_date = date("Y-m-d H:i:s", time() - 90 * 86400);
	log_output("Removing import log before $keep_from_date");

	sql_delete("delete from $config[tables_prefix]background_imports_data where import_id in (select import_id from $config[tables_prefix]background_imports where status_id in (2,3) and added_date<?)", $keep_from_date);
	sql_delete("delete from $config[tables_prefix]background_imports where status_id in (2,3) and added_date<?", $keep_from_date);

	$stats_cron['clear_old_stats']=time();
	log_output("Old stats cleanup done");
}

//clear log files
if (time() - $stats_cron['clear_log_files'] > 86400)
{
	$log_dirs = ["$config[project_path]/admin/logs/videos", "$config[project_path]/admin/logs/albums", "$config[project_path]/admin/logs/tasks"];
	foreach ($log_dirs as $log_dir)
	{
		$max_log_id = 0;
		if (is_dir($log_dir))
		{
			$dir_fp = opendir($log_dir);
			if ($dir_fp)
			{
				while (($entry = readdir($dir_fp)) !== false)
				{
					if (intval($entry) > $max_log_id)
					{
						$max_log_id = intval($entry);
					}
				}
				closedir($dir_fp);
			}
		}
		for ($i = 0; $i < 99999; $i++)
		{
			$log_id = $i * 1000;
			$archive_start_id = $log_id;
			$archive_end_id = ($i + 1) * 1000 - 1;
			$archive_files_list = '';

			if (!is_file("$log_dir/$log_id.tar.gz"))
			{
				for ($j = $archive_start_id; $j <= $archive_end_id; $j++)
				{
					if (is_file("$log_dir/$j.txt") && filemtime("$log_dir/$j.txt") < time() - 86400 * 5)
					{
						$archive_files_list .= "$j.txt\n";
						for ($k = 1; $k < 10000; $k++)
						{
							if (is_file("$log_dir/{$j}_$k.txt"))
							{
								if (filemtime("$log_dir/{$j}_$k.txt") < time() - 86400 * 5)
								{
									$archive_files_list .= "{$j}_$k.txt\n";
								}
							} else
							{
								break;
							}
						}
					}
				}
				if (trim($archive_files_list))
				{
					if (file_put_contents("$log_dir/$log_id.tar.gz.txt", $archive_files_list, LOCK_EX))
					{
						exec("cd $log_dir && tar --create --gzip --file=$log_id.tar.gz --files-from=$log_id.tar.gz.txt");
						unlink("$log_dir/$log_id.tar.gz.txt");

						$archive_files_list_array = explode("\n", $archive_files_list);
						foreach ($archive_files_list_array as $archive_files_list_item)
						{
							@unlink("$log_dir/$archive_files_list_item");
						}
					}
				} else
				{
					if ($archive_end_id > $max_log_id)
					{
						break;
					}
				}
			} else
			{
				unset($list);
				exec("cd $log_dir && tar --list --file=$log_id.tar.gz", $list);
				$list = array_flip($list);

				for ($j = $archive_start_id; $j <= $archive_end_id; $j++)
				{
					if (is_file("$log_dir/$j.txt") && filemtime("$log_dir/$j.txt") < time() - 86400 * 5)
					{
						$item_filename = "$j.txt";
						if (isset($list[$item_filename]))
						{
							for ($k = 1; $k < 10000; $k++)
							{
								$item_filename = "{$j}_$k.txt";
								if (!isset($list[$item_filename]))
								{
									break;
								}
							}
						}
						$archive_files_list .= "$item_filename\n";
						if ($item_filename != "$j.txt" && !is_file("$log_dir/$item_filename"))
						{
							rename("$log_dir/$j.txt", "$log_dir/$item_filename");
						}
					}
					if (is_file("$log_dir/{$j}_1.txt") && filemtime("$log_dir/{$j}_1.txt") < time() - 86400 * 5)
					{
						for ($k = 1; $k < 100; $k++)
						{
							if (is_file("$log_dir/{$j}_$k.txt") && filemtime("$log_dir/{$j}_$k.txt") < time() - 86400 * 5 && !isset($list["{$j}_$k.txt"]))
							{
								$archive_files_list .= "{$j}_$k.txt\n";
							}
						}
					}
				}
				if (trim($archive_files_list))
				{
					if (file_put_contents("$log_dir/$log_id.tar.gz.txt", $archive_files_list, LOCK_EX))
					{
						exec("cd $log_dir && gunzip $log_id.tar.gz && tar --append --file=$log_id.tar --files-from=$log_id.tar.gz.txt && gzip $log_id.tar");
						unlink("$log_dir/$log_id.tar.gz.txt");

						$archive_files_list_array = explode("\n", $archive_files_list);
						foreach ($archive_files_list_array as $archive_files_list_item)
						{
							@unlink("$log_dir/$archive_files_list_item");
						}
					}
				}
			}
			usleep(50000);
		}
	}

	$stats_cron['clear_log_files'] = time();
	log_output("Log files cleanup done");
}

if ($config['safe_mode']=='true')
{
	sql("update $config[tables_prefix]videos set ip=0 where ip>0");
	sql("update $config[tables_prefix]albums set ip=0 where ip>0");
	sql("update $config[tables_prefix]posts set ip=0 where ip>0");
	sql("update $config[tables_prefix]users set ip=0 where ip>0");
	sql("update $config[tables_prefix]messages set ip=0 where ip>0");
	sql("update $config[tables_prefix]comments set ip=0, country_code='' where ip>0");
	sql("update $config[tables_prefix]feedbacks set ip=0, country_code='' where ip>0");
	sql("update $config[tables_prefix]bill_transactions set ip=0, country_code=''");
	sql("delete from $config[tables_prefix]log_logins_users");
	log_output("IP info cleanup done");
}

file_put_contents("$config[project_path]/admin/data/system/cron_cleanup.dat", serialize($stats_cron), LOCK_EX);

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
