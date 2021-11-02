<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function rotator_resetInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/rotator_reset";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function rotator_resetShow()
{
	global $config,$errors,$page_name;

	rotator_resetInit();
	$plugin_path="$config[project_path]/admin/data/plugins/rotator_reset";

	$errors = null;

	if ($_GET['action']=='progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=rotator_reset</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif ($_POST['action']=='reset')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		mt_srand(time());
		$rnd=mt_rand(10000000,99999999);

		$data=array();
		$data['reset_videos']=intval($_POST['reset_videos']);
		$data['reset_screenshots']=intval($_POST['reset_screenshots']);

		file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);
		if (!is_file("$plugin_path/task-$rnd.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/task-$rnd.dat"));
		}

		if (!is_array($errors))
		{
			exec("$config[php_path] $config[project_path]/admin/plugins/rotator_reset/rotator_reset.php $rnd > /dev/null &");
			return_ajax_success("$page_name?plugin_id=rotator_reset&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}",2);
		} else {
			return_ajax_errors($errors);
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	}
}

$task_id=intval($_SERVER['argv'][1]);

if ($task_id>0 && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions_screenshots.php');
	require_once('include/functions.php');

	$plugin_path="$config[project_path]/admin/data/plugins/rotator_reset";

	$data=@unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));

	$total_amount_of_work=0;
	$done_amount_of_work=0;
	$last_pc=0;

	if ($data['reset_videos']==1)
	{
		$total_amount_of_work+=10;
	}
	if ($data['reset_screenshots']==1)
	{
		$total_amount_of_work+=mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0,1)"));
	}

	if ($data['reset_videos']==1)
	{
		sql("update $config[tables_prefix]videos set r_dlist=0, r_ccount=0, r_cweight=0, r_ctr=0");
		sql("update $config[tables_prefix]categories_videos set cr_dlist=0, cr_ccount=0, cr_cweight=0, cr_ctr=0");
		sql("update $config[tables_prefix]tags_videos set cr_dlist=0, cr_ccount=0, cr_cweight=0, cr_ctr=0");
		$done_amount_of_work+=10;
		$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
		if ($pc>$last_pc)
		{
			file_put_contents("$plugin_path/task-progress-$task_id.dat","$pc",LOCK_EX);
			$last_pc=$pc;
		}
	}

	if ($data['reset_screenshots']==1)
	{
		$video_ids=mr2array_list(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1)"));

		foreach ($video_ids as $video_id)
		{
			$dir_path=get_dir_by_id($video_id);
			@unlink("$config[content_path_videos_sources]/$dir_path/$video_id/screenshots/rotator.dat");

			$done_amount_of_work+=1;
			$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
			if ($pc>$last_pc)
			{
				file_put_contents("$plugin_path/task-progress-$task_id.dat","$pc",LOCK_EX);
				$last_pc=$pc;
			}

			log_video("",$video_id);
			log_video("INFO  Screenshots rotator stats are reset from plugin",$video_id);
			usleep(2000);
		}

		sql("update $config[tables_prefix]videos set rs_dlist=0, rs_ccount=0, rs_completed=0");
	}

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
