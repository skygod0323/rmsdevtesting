<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function movie_from_imageInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/movie_from_image";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function movie_from_imageShow()
{
	global $config,$lang,$errors,$page_name;

	movie_from_imageInit();
	$plugin_path="$config[project_path]/admin/data/plugins/movie_from_image";

	$errors = null;

	if ($_GET['action']=='get_result')
	{
		$task_id=intval($_GET['task_id']);
		if (is_file("$plugin_path/task-$task_id.mp4"))
		{
			header("Content-Type: video/mp4");
			header("Content-Disposition: attachment; filename=\"$task_id.mp4\"");
			header("Content-Length: ".filesize("$plugin_path/task-$task_id.mp4"));
			ob_end_clean();
			readfile("$plugin_path/task-$task_id.mp4");
			die;
		} else {
			header("Content-Type: text/plain");
			header("Content-Disposition: attachment; filename=\"$task_id-log.txt\"");
			header("Content-Length: ".filesize("$plugin_path/task-log-$task_id.txt"));
			ob_end_clean();
			readfile("$plugin_path/task-log-$task_id.txt");
			die;
		}
	} elseif ($_GET['action']=='progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=movie_from_image&amp;action=get_result&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif ($_POST['action']=='create')
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

		if (validate_field('empty',$_POST['source_image'],$lang['plugins']['movie_from_image']['field_image']))
		{
			validate_field('file','source_image',$lang['plugins']['movie_from_image']['field_image'],array('allowed_ext'=>'jpg','is_image'=>1));
		}
		validate_field('empty_int',$_POST['duration'],$lang['plugins']['movie_from_image']['field_duration']);
		validate_field('empty',$_POST['quality'],$lang['plugins']['movie_from_image']['field_quality']);

		if (!is_array($errors))
		{
			transfer_uploaded_file('source_image',"$plugin_path/task-$rnd.jpg");

			$data=array();
			$data['duration']=intval($_POST['duration']);
			$data['quality']=$_POST['quality'];

			file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);
			if (!is_file("$plugin_path/task-$rnd.dat"))
			{
				$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/task-$rnd.dat"));
			}

			if (!is_array($errors))
			{
				exec("$config[php_path] $config[project_path]/admin/plugins/movie_from_image/movie_from_image.php $rnd > /dev/null &");
				return_ajax_success("$page_name?plugin_id=movie_from_image&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}",2);
			} else {
				return_ajax_errors($errors);
			}
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
	require_once('include/functions.php');

	$plugin_path="$config[project_path]/admin/data/plugins/movie_from_image";

	$data=@unserialize(file_get_contents("$plugin_path/task-$task_id.dat"));
	$images_count=$data['duration']*30;

	$total_amount_of_work=$images_count+30;
	$done_amount_of_work=0;

	if (is_file("$plugin_path/task-$task_id.jpg"))
	{
		$size=getimagesize("$plugin_path/task-$task_id.jpg");
		if ($size[0]%2!=0 || $size[1]%2!=0)
		{
			if ($size[0]%2!=0)
			{
				$size[0]=$size[0]-1;
			}
			if ($size[1]%2!=0)
			{
				$size[1]=$size[1]-1;
			}
			resize_image('need_size',"$plugin_path/task-$task_id.jpg","$plugin_path/task-$task_id.jpg","$size[0]x$size[1]");
		}
		for ($i=1;$i<=$images_count;$i++)
		{
			copy("$plugin_path/task-$task_id.jpg","$plugin_path/task-$task_id-$i.jpg");
			$done_amount_of_work++;
			$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
		}
		exec("$config[ffmpeg_path] -i $plugin_path/task-$task_id-%d.jpg $data[quality] -t $data[duration] -r 30 -f mp4 $plugin_path/task-$task_id.mp4 2>&1",$res);
		for ($i=1;$i<=$images_count;$i++)
		{
			unlink("$plugin_path/task-$task_id-$i.jpg");
		}
		unlink("$plugin_path/task-$task_id.jpg");

		file_put_contents("$plugin_path/task-log-$task_id.txt", implode("\n",$res), LOCK_EX);
	}

	@unlink("$plugin_path/task-$task_id.dat");
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
