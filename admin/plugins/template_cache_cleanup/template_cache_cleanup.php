<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function template_cache_cleanupInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
	if (!is_file("$plugin_path/data.dat"))
	{
		$data=array();
		$data['is_enabled']=1;
		$data['interval']=24;
		$data['tod']=0;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
		file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
	}
}

function template_cache_cleanupIsEnabled()
{
	global $config;

	template_cache_cleanupInit();
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";
	return is_file("$plugin_path/cron.dat");
}

function template_cache_cleanupShow()
{
	global $config,$lang,$errors,$page_name;

	template_cache_cleanupInit();
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		$log_file = "plugins/template_cache_cleanup.txt";
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
	} elseif ($_GET['action']=='progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=template_cache_cleanup&amp;result_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif ($_POST['action']=='change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['is_enabled'])==1)
		{
			validate_field('empty_int',$_POST['interval'],$lang['plugins']['template_cache_cleanup']['field_schedule']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
		}

		if (isset($_POST['calculate_stats']))
		{
			mt_srand(time());
			$rnd=mt_rand(10000000,99999999);

			exec("$config[php_path] $config[project_path]/admin/plugins/template_cache_cleanup/template_cache_cleanup.php calculate $rnd > /dev/null &");
			return_ajax_success("$page_name?plugin_id=template_cache_cleanup&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}",2);
		} elseif (isset($_POST['start_now']))
		{
			exec("$config[php_path] $config[project_path]/admin/plugins/template_cache_cleanup/template_cache_cleanup.php manual > /dev/null &");
			return_ajax_success("$page_name?plugin_id=template_cache_cleanup");
		} else {
			if (!is_array($errors))
			{
				$save_data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
				$save_data['is_enabled']=intval($_POST['is_enabled']);
				$save_data['interval']=intval($_POST['interval']);
				$save_data['tod']=intval($_POST['tod']);

				file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

				if (intval($_POST['is_enabled']) == 1)
				{
					if (!is_file("$plugin_path/cron.dat") || $save_data['tod'] > 0)
					{
						$current_hour = date('H');
						if ($save_data['tod'] == 0)
						{
							$next_date = time();
						} elseif ($current_hour < $save_data['tod'] - 1)
						{
							$next_date = strtotime(date('Y-m-d ') . ($save_data['tod'] - 1) . ':00:00');
						} else
						{
							$next_date = strtotime(date('Y-m-d ') . ($save_data['tod'] - 1) . ':00:00') + 86400;
						}
						file_put_contents("$plugin_path/cron.dat", $next_date, LOCK_EX);
					}
				} else
				{
					@unlink("$plugin_path/cron.dat");
				}

				return_ajax_success("$page_name?plugin_id=template_cache_cleanup");
			} else {
				return_ajax_errors($errors);
			}
		}
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		$_POST['is_enabled']=0;
		$_POST['interval']=24;
		$_POST['tod']=0;
		$_POST['last_exec_date']='0000-00-00 00:00:00';
		$_POST['duration']='0';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}

	$_POST['next_exec_date']='0000-00-00 00:00:00';
	if (is_file("$plugin_path/cron.dat"))
	{
		$_POST['next_exec_date']=date("Y-m-d H:i:s",file_get_contents("$plugin_path/cron.dat"));
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat")));
	}

	require_once('include/setup_smarty_site.php');
	$smarty_site=new mysmarty_site();
	$_POST['cache_dir']=$smarty_site->cache_dir;
	$_POST['storage_dir']="$config[project_path]/admin/data/engine/storage";

	if (is_writable("$plugin_path"))
	{
		exec("find $plugin_path -name '*.dat' -mtime +6 -delete");
	}

	if (intval($_GET['result_id'])>0)
	{
		$result_id=intval($_GET['result_id']);
		$result=@unserialize(@file_get_contents("$plugin_path/task-$result_id.dat"));
		$_POST['cache_size']=$result['cache_size'];
		$_POST['storage_size']=$result['storage_size'];
	}
}

function template_cache_cleanupCleanup($folder,$max_cache_time=86400)
{
	$sleep=5000;
	$result=0;

	if ($max_cache_time==0)
	{
		$max_cache_time=86400;
	}

	if (is_dir($folder))
	{
		$handle=opendir($folder);
		if ($handle)
		{
			while (false !== ($entry=readdir($handle)))
			{
				if ($entry<>'.' && $entry<>'..')
				{
					if (is_file("$folder/$entry"))
					{
						if (time()-filectime("$folder/$entry")>$max_cache_time)
						{
							if (@unlink("$folder/$entry"))
							{
								$result++;
							}
						}
					} elseif (is_dir("$folder/$entry"))
					{
						$result+=template_cache_cleanupCleanup("$folder/$entry",$max_cache_time);
						@rmdir("$folder/$entry");
					}
					usleep($sleep);
				}
			}
			closedir($handle);
		}
	}
	return $result;
}

function template_cache_cleanupCron()
{
	global $config;

	require_once('setup.php');
	require_once('functions_base.php');
	require_once('setup_smarty_site.php');

	$start=time();
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";
	template_cache_cleanupLog("Started");

	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data) || $data['is_enabled']==0)
	{
		return;
	}

	$max_cache_time=template_cache_cleanupDetectMaxCacheTime();
	template_cache_cleanupLog("Max cache time detected: $max_cache_time");

	$smarty_site=new mysmarty_site();
	$total_cnt=0;

	$cnt=template_cache_cleanupCleanup($smarty_site->cache_dir,$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in cache folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/storage",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in storage folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/videos_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in videos_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/albums_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in albums_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/comments_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in comments_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/cs_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in cs_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/dvds_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in dvds_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/feeds_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in feeds_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/models_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in models_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/posts_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in posts_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/playlists_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in playlists_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_video",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in random_video folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_album",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in random_album folder");

	$sleep=5000;
	$cnt=0;
	$rotator_dirs=get_contents_from_dir("$config[project_path]/admin/data/engine/rotator/videos/list",2);
	foreach ($rotator_dirs as $rotator_dir)
	{
		$rotator_files=get_contents_from_dir("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir",1);
		foreach ($rotator_files as $rotator_file)
		{
			if (time()-filectime("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir/$rotator_file")>$max_cache_time)
			{
				if (@unlink("$config[project_path]/admin/data/engine/rotator/videos/list/$rotator_dir/$rotator_file"))
				{
					$cnt++;
				}
			}
			usleep($sleep);
		}
	}
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in rotator folder");

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (is_array($data))
	{
		$data['last_exec_date'] = $start;
		$data['duration'] = time() - $start;
		$data['deleted_files'] = $total_cnt;
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

		$next_date = $start + $data['interval'] * 60 * 60;
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

	$duration = time() - $start;
	template_cache_cleanupLog("Finished in $duration seconds");
}

function template_cache_cleanupLog($message)
{
	global $config;

	echo date("[Y-m-d H:i:s] ").$message."\n";
	file_put_contents("$config[project_path]/admin/logs/plugins/template_cache_cleanup.txt",date("[Y-m-d H:i:s] ").$message."\n",FILE_APPEND);
}

function template_cache_cleanupDetectMaxCacheTime()
{
	global $config,$regexp_valid_external_id,$regexp_valid_block_name;

	require_once("$config[project_path]/admin/include/functions_admin.php");

	$max_cache_time=0;
	$pages=get_site_pages();
	$templates_data = get_site_parsed_templates();
	foreach ($pages as $v)
	{
		if (!preg_match($regexp_valid_external_id,$v['external_id']))
		{
			continue;
		}
		$template_info=$templates_data["$v[external_id].tpl"];
		if (isset($template_info))
		{
			foreach ($template_info['block_inserts'] as $block_insert)
			{
				$block_id=trim($block_insert['block_id']);
				$block_name=trim($block_insert['block_name']);

				if (preg_match($regexp_valid_external_id,$block_id) && preg_match($regexp_valid_block_name,$block_name))
				{
					$block_name=strtolower(str_replace(" ","_",$block_name));
					$file_data=@file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat");
					$temp_bl=explode("||",$file_data);
					$cache_time=intval($temp_bl[0]);
					if ($cache_time>$max_cache_time)
					{
						$max_cache_time=$cache_time;
					}
				}
			}
		}
	}
	return $max_cache_time;
}

if ($_SERVER['argv'][1]=='calculate' && intval($_SERVER['argv'][2])>0 && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/setup_smarty_site.php');

	$task_id=intval($_SERVER['argv'][2]);
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";

	$result=array();
	$smarty_site=new mysmarty_site();
	$cache_dir=$smarty_site->cache_dir;

	unset($res);
	exec("du -m -s $cache_dir",$res);
	$size=intval(trim($res[0]));
	$result['cache_size']=$size;

	file_put_contents("$plugin_path/task-progress-$task_id.dat", "50", LOCK_EX);

	unset($res);
	exec("du -m -s $config[project_path]/admin/data/engine/storage",$res);
	$size=intval(trim($res[0]));
	$result['storage_size']=$size;

	file_put_contents("$plugin_path/task-$task_id.dat", serialize($result), LOCK_EX);
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
} elseif ($_SERVER['argv'][1]=='manual' && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/setup_smarty_site.php');

	$start=time();
	$plugin_path="$config[project_path]/admin/data/plugins/template_cache_cleanup";

	@unlink("$config[project_path]/admin/logs/plugins/template_cache_cleanup.txt");

	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));

	$max_cache_time=template_cache_cleanupDetectMaxCacheTime();
	template_cache_cleanupLog("Max cache time detected: $max_cache_time");

	$smarty_site=new mysmarty_site();
	$total_cnt=0;

	$cnt=template_cache_cleanupCleanup($smarty_site->cache_dir,$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in cache folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/storage",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in storage folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/videos_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in videos_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/albums_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in albums_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/comments_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in comments_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/cs_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in cs_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/dvds_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in dvds_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/feeds_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in feeds_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/models_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in models_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/posts_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in posts_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/playlists_info",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in playlists_info folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_video",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in random_video folder");

	$cnt=template_cache_cleanupCleanup("$config[project_path]/admin/data/engine/random_album",$max_cache_time);
	$total_cnt+=$cnt;
	template_cache_cleanupLog("Removed $cnt files in random_album folder");

	$data['last_exec_date']=$start;
	$data['duration']=time()-$start;
	$data['deleted_files']=$total_cnt;

	file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

	template_cache_cleanupLog("Finished in $data[duration] seconds");
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
