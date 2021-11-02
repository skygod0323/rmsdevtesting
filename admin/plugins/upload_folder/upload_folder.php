<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function upload_folderInit()
{
	global $config;

	mkdir_recursive("$config[project_path]/admin/data/plugins/upload_folder");
}

function upload_folderIsEnabled()
{
	global $config;

	upload_folderInit();
	$plugin_path="$config[project_path]/admin/data/plugins/upload_folder";
	return is_file("$plugin_path/cron.dat");
}

function upload_folderShow()
{
	global $config,$lang,$errors,$page_name;

	upload_folderInit();
	$plugin_path="$config[project_path]/admin/data/plugins/upload_folder";

	$errors = null;

	if ($_GET['action']=='validate_progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=upload_folder&amp;action=confirm&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif ($_GET['action']=='import_progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=upload_folder&amp;action=complete&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif (isset($_POST['action_back']) || $_POST['action']=='close')
	{
		return_ajax_success("$page_name?plugin_id=upload_folder");
	} elseif ($_POST['action']=='validate' || $_POST['action']=='import')
	{
		$import_items=$_POST['import_items'];

		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if ($_POST['folder_standard_videos']<>'')
		{
			if (strpos($_POST['folder_standard_videos'],"/")!==0)
			{
				$_POST['folder_standard_videos']="$config[project_path]/$_POST[folder_standard_videos]";
			}

			if (strpos($_POST['folder_standard_videos'],"..")!==false)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_standard_videos']);
			} elseif (strpos($_POST['folder_standard_videos'],$config['project_path'])!==0)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_standard_videos']);
			} elseif (rtrim($_POST['folder_standard_videos'],'/')==$config['project_path'])
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_standard_videos']);
			} elseif (!is_dir($_POST['folder_standard_videos']))
			{
				$errors[]=get_aa_error('server_path_invalid',$lang['plugins']['upload_folder']['field_folder_standard_videos']);
			} elseif (!is_readable($_POST['folder_standard_videos']))
			{
				$errors[]=get_aa_error('filesystem_permission_read',$_POST['folder_standard_videos']);
			} elseif (intval($_POST['delete_files'])==1 && !is_writable($_POST['folder_standard_videos']))
			{
				$errors[]=get_aa_error('filesystem_permission_write',$_POST['folder_standard_videos']);
			} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=0")) == 0)
			{
				$errors[]=get_aa_error('video_type_no_formats',$lang['plugins']['upload_folder']['field_folder_standard_videos']);
			}
		}
		if ($_POST['folder_premium_videos']<>'')
		{
			if (strpos($_POST['folder_premium_videos'],"/")!==0)
			{
				$_POST['folder_premium_videos']="$config[project_path]/$_POST[folder_premium_videos]";
			}

			if (strpos($_POST['folder_premium_videos'],"..")!==false)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_premium_videos']);
			} elseif (strpos($_POST['folder_premium_videos'],$config['project_path'])!==0)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_premium_videos']);
			} elseif (rtrim($_POST['folder_premium_videos'],'/')==$config['project_path'])
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_premium_videos']);
			} elseif (!is_dir($_POST['folder_premium_videos']))
			{
				$errors[]=get_aa_error('server_path_invalid',$lang['plugins']['upload_folder']['field_folder_premium_videos']);
			} elseif (!is_readable($_POST['folder_premium_videos']))
			{
				$errors[]=get_aa_error('filesystem_permission_read',$_POST['folder_premium_videos']);
			} elseif (intval($_POST['delete_files'])==1 && !is_writable($_POST['folder_premium_videos']))
			{
				$errors[]=get_aa_error('filesystem_permission_write',$_POST['folder_premium_videos']);
			} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=1")) == 0)
			{
				$errors[]=get_aa_error('video_type_no_formats',$lang['plugins']['upload_folder']['field_folder_premium_videos']);
			}
		}
		if ($_POST['folder_albums']<>'')
		{
			if (strpos($_POST['folder_albums'],"/")!==0)
			{
				$_POST['folder_albums']="$config[project_path]/$_POST[folder_albums]";
			}

			if (strpos($_POST['folder_albums'],"..")!==false)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_albums']);
			} elseif (strpos($_POST['folder_albums'],$config['project_path'])!==0)
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_albums']);
			} elseif (rtrim($_POST['folder_albums'],'/')==$config['project_path'])
			{
				$errors[]=get_aa_error('folder_should_be_project_child',$lang['plugins']['upload_folder']['field_folder_albums']);
			} elseif (!is_dir($_POST['folder_albums']))
			{
				$errors[]=get_aa_error('server_path_invalid',$lang['plugins']['upload_folder']['field_folder_albums']);
			} elseif (!is_readable($_POST['folder_albums']))
			{
				$errors[]=get_aa_error('filesystem_permission_read',$_POST['folder_albums']);
			} elseif (intval($_POST['delete_files'])==1 && !is_writable($_POST['folder_albums']))
			{
				$errors[]=get_aa_error('filesystem_permission_write',$_POST['folder_albums']);
			}
		}
		if ($_POST['charset']<>'')
		{
			if (!function_exists('iconv'))
			{
				$errors[]=str_replace('%1%',$lang['plugins']['upload_folder']['field_filenames_encoding'],$lang['plugins']['upload_folder']['validation_error_iconv']);
			}
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			if ($_POST['action']=='import' && intval($_POST['task_id'])>0)
			{
				$task_id=intval($_POST['task_id']);
				$admin_id=$_SESSION['userdata']['user_id'];

				$task_data=@unserialize(@file_get_contents("$plugin_path/$task_id.dat"));
				$task_data['import_items']=$import_items;
				file_put_contents("$plugin_path/$task_id.dat", serialize($task_data), LOCK_EX);

				exec("$config[php_path] $config[project_path]/admin/plugins/upload_folder/upload_folder.php import $task_id $admin_id > $config[project_path]/admin/logs/plugins/upload_folder.txt &");
				return_ajax_success("$page_name?plugin_id=upload_folder&amp;action=import_progress&amp;task_id=$task_id&amp;rand=\${rand}",2);
			} else {
				$save_data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
				$save_data['folder_standard_videos']=$_POST['folder_standard_videos'];
				$save_data['folder_premium_videos']=$_POST['folder_premium_videos'];
				$save_data['folder_albums']=$_POST['folder_albums'];
				$save_data['video_formats']=$_POST['video_formats'];
				$save_data['video_screenshots']=$_POST['video_screenshots'];
				$save_data['charset']=$_POST['charset'];
				$save_data['delete_files']=$_POST['delete_files'];
				$save_data['randomize']=$_POST['randomize'];
				$save_data['content_status']=$_POST['content_status'];

				file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

				mt_srand(time());
				$rnd=mt_rand(10000000,99999999);

				file_put_contents("$plugin_path/$rnd.dat", serialize($save_data), LOCK_EX);

				exec("$config[php_path] $config[project_path]/admin/plugins/upload_folder/upload_folder.php validate $rnd > $config[project_path]/admin/logs/plugins/upload_folder.txt &");
				return_ajax_success("$page_name?plugin_id=upload_folder&amp;action=validate_progress&amp;task_id=$rnd&amp;rand=\${rand}",2);
			}
		} else {
			return_ajax_errors($errors);
		}
	}

	if ($_GET['action']=='confirm' && intval($_GET['task_id'])>0)
	{
		$task_id=intval($_GET['task_id']);
		$_POST=@unserialize(@file_get_contents("$plugin_path/$task_id.dat"));
	} elseif ($_GET['action']=='complete' && intval($_GET['task_id'])>0)
	{
		$task_id=intval($_GET['task_id']);
		$_POST=@unserialize(@file_get_contents("$plugin_path/$task_id.dat"));
	} elseif (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat")));
	}

	if (is_writable("$plugin_path"))
	{
		exec("find $plugin_path \( -iname \"*.dat\" ! -iname \"data.dat\" \) -mtime +2 -delete");
	}
}

function upload_folderEndsWith($haystack,$needle)
{
	if (is_array($needle))
	{
		foreach ($needle as $needle_item)
		{
			if (upload_folderEndsWith($haystack,$needle_item))
			{
				return true;
			}
		}
		return false;
	}

	$length=strlen($needle);
	if ($length==0)
	{
		return false;
	}

	return (substr(strtolower($haystack),$length*-1)===$needle);
}

function upload_folderDecode($charset,$string)
{
	if ($charset<>'')
	{
		return iconv($charset,'UTF-8',$string);
	}
	return $string;
}

function upload_folderParseVideo($base_path,$base_file,$formats,$charset,$delete_files,$screenshots_option)
{
	global $config;

	upload_folderLog("Parsing video $base_path/$base_file");

	$video_allowed_ext=explode(",",$config['video_allowed_ext']);

	$result_item=array();

	$video_files=array($base_file);
	if (is_dir("$base_path/$base_file"))
	{
		$base_path="$base_path/$base_file";
		$video_files=get_contents_from_dir($base_path,1);
		sort($video_files);
		$result_item['title']=upload_folderDecode($charset,$base_file);
		$result_item['folder_name']=$base_file;
		$result_item['folder_title']=upload_folderDecode($charset,$base_file);
		$video_allowed_ext[]='jpg';
		$video_allowed_ext[]='txt';
		$video_allowed_ext[]='zip';
	} else {
		$ext=strtolower(end(explode(".",$base_file)));
		if (strlen($base_file)>strlen($ext)+1)
		{
			$result_item['title']=upload_folderDecode($charset,substr($base_file,0,strlen($base_file)-strlen($ext)-1));
		} else {
			$result_item['title']=upload_folderDecode($charset,$base_file);
		}
	}

	$result_item['external_key']=md5("$base_path/$base_file");
	$duplicate_video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where external_key=? order by video_id asc limit 1",$result_item['external_key']));
	if ($duplicate_video_id>0)
	{
		$result_item['duplicate_id']=$duplicate_video_id;
		return $result_item;
	}

	$result_item['files']=array();
	if (count($video_files)==0)
	{
		$video_file_item=array();
		$video_file_item['file_name']=$base_file;
		$video_file_item['file_title']=upload_folderDecode($charset,$base_file);
		$video_file_item['file_size']=sizeToHumanString(0);
		$video_file_item['file_type']=0;
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_video_files';
		$result_item['files'][]=$video_file_item;
		return $result_item;
	}

	$max_source_file='';
	$max_source_file_size=0;
	$format_files=array();
	foreach ($video_files as $video_file)
	{
		$video_file_item=array();
		$video_file_item['file_name']=$video_file;
		$video_file_item['file_title']=upload_folderDecode($charset,$video_file);
		$video_file_item['file_size']=sizeToHumanString(sprintf("%.0f",filesize("$base_path/$video_file")));

		$ext=strtolower(end(explode(".",$video_file)));
		if (!is_readable("$base_path/$video_file"))
		{
			$video_file_item['file_type']=-1;
			$video_file_item['error']='dg_contents_errors_unreadable_file';
			$result_item['files'][]=$video_file_item;
		} elseif (!in_array($ext,$video_allowed_ext))
		{
			$video_file_item['file_type']=0;
			$result_item['files'][]=$video_file_item;
		} elseif (upload_folderEndsWith($video_file,'zip'))
		{
			$zip = new PclZip("$base_path/$video_file");
			$data=process_zip_images($zip->listContent());

			$video_file_item['file_type']=3;
			$video_file_item['file_count']=count($data);
			if (count($data)==0)
			{
				$video_file_item['file_type']=-1;
				$video_file_item['error']='dg_contents_errors_invalid_zip_file';
			}

			$result_item['screenshots_zip']=$video_file;
			$result_item['files'][]=$video_file_item;
		} elseif (upload_folderEndsWith($video_file,'jpg'))
		{
			$result_item['screenshots'][]=$video_file;
		} elseif (upload_folderEndsWith($video_file,'txt'))
		{
			if ($result_item['description']=='')
			{
				$video_file_item['file_type']=8;
				$result_item['description']=file_get_contents("$base_path/$video_file");
			}
			$result_item['files'][]=$video_file_item;
		} else {
			$is_format_file=false;
			foreach ($formats as $format)
			{
				if (upload_folderEndsWith($video_file,$format['postfix']))
				{
					$video_file_item['file_type']=2;
					$video_file_item['format_title']=$format['title'];
					$video_file_item['format_postfix']=$format['postfix'];
					$video_file_item['is_use_as_source']=$format['is_use_as_source'];
					$is_format_file=true;
					if (!isset($format_files[$format['postfix']]))
					{
						$format_files[$format['postfix']]=array('max_file_size'=>0,'max_file'=>$video_file);
					}
					if (sprintf("%.0f",filesize("$base_path/$video_file"))>$format_files[$format['postfix']]['max_file_size'])
					{
						$format_files[$format['postfix']]['max_file']=$video_file;
						$format_files[$format['postfix']]['max_file_size']=sprintf("%.0f",filesize("$base_path/$video_file"));
					}
					break;
				}
			}
			if (!$is_format_file)
			{
				$video_file_item['file_type']=1;
				if (sprintf("%.0f",filesize("$base_path/$video_file"))>$max_source_file_size)
				{
					$max_source_file=$video_file;
					$max_source_file_size=sprintf("%.0f",filesize("$base_path/$video_file"));
				}
			}
			$duration=get_video_duration("$base_path/$video_file");
			if ($duration==0)
			{
				$video_file_item['file_type']=-1;
				$video_file_item['error']='dg_contents_errors_invalid_video_file';
			}
			$video_file_item['file_duration']=durationToHumanString($duration);
			$result_item['files'][]=$video_file_item;
		}
	}
	if (@count($result_item['screenshots'])>0)
	{
		$video_file_item=array();
		if (@count($result_item['screenshots'])>1)
		{
			$video_file_item['file_name']="*.jpg";
			$video_file_item['file_title']="*.jpg";
		} else {
			$video_file_item['file_name']=$result_item['screenshots'][0];
			$video_file_item['file_title']=upload_folderDecode($charset,$result_item['screenshots'][0]);
		}
		$screenshots_size=0;
		foreach ($result_item['screenshots'] as $screenshot)
		{
			$screenshots_size+=filesize("$base_path/$screenshot");
		}
		$video_file_item['file_size']=sizeToHumanString($screenshots_size);
		$video_file_item['file_count']=@count($result_item['screenshots']);
		if (!isset($result_item['screenshots_zip']))
		{
			if (@count($result_item['screenshots'])>1 || $screenshots_option==2)
			{
				$video_file_item['file_type']=5;
			} else {
				$video_file_item['file_type']=4;
			}
		} else {
			$video_file_item['file_type']=0;
		}
		foreach ($result_item['screenshots'] as $screenshot)
		{
			$img_size=getimagesize("$base_path/$screenshot");
			if ($img_size[0]<1 || $img_size[1]<1)
			{
				$video_file_item['file_type']=-1;
				$video_file_item['file_name']="$screenshot";
				$video_file_item['file_title']=upload_folderDecode($charset,"$screenshot");
				$video_file_item['file_size']=sizeToHumanString(sprintf("%.0f",filesize("$base_path/$screenshot")));
				$video_file_item['error']='dg_contents_errors_invalid_image_file';
				break;
			}
		}
		$result_item['files'][]=$video_file_item;
	}

	$has_required_files=false;
	$has_errors_on_files=false;
	foreach ($result_item['files'] as $k=>$v)
	{
		if ($v['file_type']==-1)
		{
			$has_errors_on_files=true;
		}
		if ($v['file_type']==1 && $v['file_name']<>$max_source_file)
		{
			$result_item['files'][$k]['file_type']=0;
		}
		if ($v['file_type']==2)
		{
			if ($v['file_name']<>$format_files[$v['format_postfix']]['max_file'])
			{
				$result_item['files'][$k]['file_type']=0;
			}
		}
		if ($v['file_type']==1 || $v['file_type']==2)
		{
			$has_required_files=true;
		}
	}

	if ($has_errors_on_files)
	{
		$result_item['has_error']=1;
	} elseif (!$has_required_files)
	{
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_video_files';
	} elseif ($result_item['folder_name']<>'' && !is_writable($base_path) && $delete_files==1)
	{
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_delete_permissions';
	}
	return $result_item;
}

function upload_folderParseAlbum($base_path,$base_file,$charset,$delete_files)
{
	global $config;

	upload_folderLog("Parsing album $base_path/$base_file");

	$result_item=array();

	$album_files=array($base_file);
	if (is_dir("$base_path/$base_file"))
	{
		$base_path="$base_path/$base_file";
		$album_files=get_contents_from_dir($base_path,1);
		sort($album_files);
		$result_item['title']=upload_folderDecode($charset,$base_file);
		$result_item['folder_name']=$base_file;
		$result_item['folder_title']=upload_folderDecode($charset,$base_file);
	} else {
		$ext=strtolower(end(explode(".",$base_file)));
		if (strlen($base_file)>strlen($ext)+1)
		{
			$result_item['title']=upload_folderDecode($charset,substr($base_file,0,strlen($base_file)-strlen($ext)-1));
		} else {
			$result_item['title']=upload_folderDecode($charset,$base_file);
		}
	}

	$result_item['external_key']=md5("$base_path/$base_file");
	$duplicate_album_id=mr2number(sql_pr("select album_id from $config[tables_prefix]albums where external_key=? order by album_id asc limit 1",$result_item['external_key']));
	if ($duplicate_album_id>0)
	{
		$result_item['duplicate_id']=$duplicate_album_id;
		return $result_item;
	}

	$result_item['files']=array();
	if (count($album_files)==0)
	{
		$album_file_item=array();
		$album_file_item['file_name']=$base_file;
		$album_file_item['file_title']=upload_folderDecode($charset,$base_file);
		$album_file_item['file_size']=sizeToHumanString(0);
		$album_file_item['file_type']=0;
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_image_files';
		$result_item['files'][]=$album_file_item;
		return $result_item;
	}

	foreach ($album_files as $album_file)
	{
		$album_file_item=array();
		$album_file_item['file_name']=$album_file;
		$album_file_item['file_title']=upload_folderDecode($charset,$album_file);
		$album_file_item['file_size']=sizeToHumanString(sprintf("%.0f",filesize("$base_path/$album_file")));

		if (!is_readable("$base_path/$album_file"))
		{
			$album_file_item['file_type']=-1;
			$album_file_item['error']='dg_contents_errors_unreadable_file';
			$result_item['files'][]=$album_file_item;
		} elseif (upload_folderEndsWith($album_file,'zip'))
		{
			$zip = new PclZip("$base_path/$album_file");
			$data=process_zip_images($zip->listContent());

			$album_file_item['file_type']=6;
			$album_file_item['file_count']=count($data);
			if (count($data)==0)
			{
				$album_file_item['file_type']=-1;
				$album_file_item['error']='dg_contents_errors_invalid_zip_file';
			}

			$result_item['images_zip']=$album_file;
			$result_item['files'][]=$album_file_item;
		} elseif (upload_folderEndsWith($album_file,array_map('trim',explode(',',$config['image_allowed_ext']))))
		{
			$result_item['images'][]=$album_file;
		} elseif (upload_folderEndsWith($album_file,'txt') && $result_item['folder_name']!='')
		{
			if ($result_item['description']=='')
			{
				$album_file_item['file_type']=8;
				$result_item['description']=file_get_contents("$base_path/$album_file");
			}
			$result_item['files'][]=$album_file_item;
		} else {
			$album_file_item['file_type']=0;
			$result_item['files'][]=$album_file_item;
		}
	}
	if (@count($result_item['images'])>0)
	{
		$album_file_item=array();
		if (@count($result_item['images'])>1)
		{
			$album_file_item['file_name']="*.jpg";
			$album_file_item['file_title']="*.*";
		} else {
			$album_file_item['file_name']=$result_item['images'][0];
			$album_file_item['file_title']=upload_folderDecode($charset,$result_item['images'][0]);
		}
		$images_size=0;
		foreach ($result_item['images'] as $image)
		{
			$images_size+=filesize("$base_path/$image");
		}
		$album_file_item['file_size']=sizeToHumanString($images_size);
		$album_file_item['file_count']=@count($result_item['images']);
		if (!isset($result_item['images_zip']))
		{
			$album_file_item['file_type']=7;
		} else {
			$album_file_item['file_type']=0;
		}
		foreach ($result_item['images'] as $image)
		{
			$img_size=getimagesize("$base_path/$image");
			if ($img_size[0]<1 || $img_size[1]<1)
			{
				$album_file_item['file_type']=-1;
				$album_file_item['file_name']="$image";
				$album_file_item['file_title']=upload_folderDecode($charset,"$image");
				$album_file_item['file_size']=sizeToHumanString(sprintf("%.0f",filesize("$base_path/$image")));
				$album_file_item['error']='dg_contents_errors_invalid_image_file';
				break;
			}
		}
		$result_item['files'][]=$album_file_item;
	}

	$has_errors_on_files=false;
	foreach ($result_item['files'] as $v)
	{
		if ($v['file_type']==-1)
		{
			$has_errors_on_files=true;
		}
	}

	if ($has_errors_on_files)
	{
		$result_item['has_error']=1;
	} elseif (@count($result_item['images'])==0 && !isset($result_item['images_zip']))
	{
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_image_files';
	} elseif ($result_item['folder_name']<>'' && !is_writable($base_path) && $delete_files==1)
	{
		$result_item['has_error']=1;
		$result_item['error']='dg_contents_errors_no_delete_permissions';
	}

	return $result_item;
}

if ($_SERVER['argv'][1]=='validate' && intval($_SERVER['argv'][2])>0 && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions.php');
	require_once('include/pclzip.lib.php');

	ini_set("display_error",1);

	upload_folderLog("Upload folders validation started");

	$task_id=intval($_SERVER['argv'][2]);
	$plugin_path="$config[project_path]/admin/data/plugins/upload_folder";

	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	$data['content']=array();
	$data['found_objects']=0;
	$data['existing_objects']=0;
	$data['errors']=0;

	$total_amount_of_work=0;
	$done_amount_of_work=0;

	if ($data['folder_standard_videos']<>'')
	{
		$contents=get_contents_from_dir($data['folder_standard_videos'],0);
		$total_amount_of_work+=count($contents);
	}
	if ($data['folder_premium_videos']<>'')
	{
		$contents=get_contents_from_dir($data['folder_premium_videos'],0);
		$total_amount_of_work+=count($contents);
	}
	if ($config['installation_type']==4)
	{
		if ($data['folder_albums']<>'')
		{
			$contents=get_contents_from_dir($data['folder_albums'],0);
			$total_amount_of_work+=count($contents);
		}
	}

	if ($data['folder_standard_videos']<>'')
	{
		$formats=array();
		if ($data['video_formats']==1)
		{
			$formats=mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=0 order by LENGTH(postfix) desc"));
		}
		$contents=get_contents_from_dir($data['folder_standard_videos'],0);
		if ($data['randomize']==1)
		{
			shuffle($contents);
		} else
		{
			sort($contents);
		}
		foreach ($contents as $file)
		{
			$data['found_objects']++;
			$item=upload_folderParseVideo($data['folder_standard_videos'],$file,$formats,$data['charset'],$data['delete_files'],$data['video_screenshots']);
			if (is_array($item) && intval($item['duplicate_id'])==0)
			{
				$item['type']=1;
				$data['content'][]=$item;
				if ($item['has_error']==1)
				{
					$data['errors']++;
				}
			} else {
				$data['existing_objects']++;
				$item['type']=1;
				$data['duplicates'][]=$item;
			}

			$done_amount_of_work++;
			$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
		}
	}

	if ($data['folder_premium_videos']<>'')
	{
		$formats=array();
		if ($data['video_formats']==1)
		{
			$formats=mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=1 order by LENGTH(postfix) desc"));
		}
		$contents=get_contents_from_dir($data['folder_premium_videos'],0);
		if ($data['randomize']==1)
		{
			shuffle($contents);
		} else
		{
			sort($contents);
		}
		foreach ($contents as $file)
		{
			$data['found_objects']++;
			$item=upload_folderParseVideo($data['folder_premium_videos'],$file,$formats,$data['charset'],$data['delete_files'],$data['video_screenshots']);
			if (is_array($item) && intval($item['duplicate_id'])==0)
			{
				$item['type']=2;
				$data['content'][]=$item;
				if ($item['has_error']==1)
				{
					$data['errors']++;
				}
			} else {
				$data['existing_objects']++;
				$item['type']=2;
				$data['duplicates'][]=$item;
			}

			$done_amount_of_work++;
			$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
		}
	}

	if ($config['installation_type']==4)
	{
		if ($data['folder_albums']<>'')
		{
			$contents=get_contents_from_dir($data['folder_albums'],0);
			if ($data['randomize']==1)
			{
				shuffle($contents);
			} else
			{
				sort($contents);
			}
			foreach ($contents as $file)
			{
				$data['found_objects']++;
				$item=upload_folderParseAlbum($data['folder_albums'],$file,$data['charset'],$data['delete_files']);
				if (is_array($item) && intval($item['duplicate_id'])==0)
				{
					$item['type']=3;
					$data['content'][]=$item;
					if ($item['has_error']==1)
					{
						$data['errors']++;
					}
				} else {
					$data['existing_objects']++;
					$item['type']=3;
					$data['duplicates'][]=$item;
				}

				$done_amount_of_work++;
				$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
				file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
			}
		}
	}

	file_put_contents("$plugin_path/$task_id.dat", serialize($data), LOCK_EX);
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);

	upload_folderLog("Upload folders validation finished");
}

if ($_SERVER['argv'][1]=='import' && intval($_SERVER['argv'][2])>0 && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions_screenshots.php');
	require_once('include/functions.php');
	require_once('include/pclzip.lib.php');

	ini_set("display_error",1);
	upload_folderLog("Content import started");

	$task_id=intval($_SERVER['argv'][2]);
	$admin_id=intval($_SERVER['argv'][3]);
	$config['sql_safe_mode'] = 1;
	$admin_username=mr2string(sql_pr("select login from $config[tables_prefix]admin_users where user_id=?",$admin_id));
	unset($config['sql_safe_mode']);
	$plugin_path="$config[project_path]/admin/data/plugins/upload_folder";

	$data=@unserialize(@file_get_contents("$plugin_path/$task_id.dat"));

	$options=get_options();

	$total_amount_of_work=count($data['content']);
	$done_amount_of_work=0;
	$data['processed_content']=array();
	foreach ($data['content'] as $item)
	{
		$done_amount_of_work++;
		$pc=max(floor(($done_amount_of_work/$total_amount_of_work)*100)-1,0);
		file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);

		if (isset($item['has_error']))
		{
			continue;
		}
		if (!in_array($item['external_key'],$data['import_items']))
		{
			continue;
		}
		if ($item['type']==1 || $item['type']==2)
		{
			upload_folderLog("Importing video $item[title]");
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?",$item['external_key']))>0)
			{
				upload_folderLog("Skipped, duplicate video");
				continue;
			}
			$insert_data=array();
			$insert_data['title']=$item['title'];
			$insert_data['description']=trim($item['description']);
			$insert_data['external_key']=$item['external_key'];
			$insert_data['load_type_id']=1;
			$insert_data['status_id']=3;
			$insert_data['rating']=$options['VIDEO_INITIAL_RATING'];
			$insert_data['rating_amount']=1;
			$insert_data['user_id']=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']));
			$insert_data['admin_user_id']=$admin_id;

			if ($item['type']==2)
			{
				$insert_data['is_private']=2;
			}

			if ($options['USE_POST_DATE_RANDOMIZATION']=='0')
			{
				$insert_data['post_date']=date("Y-m-d");
			} elseif ($options['USE_POST_DATE_RANDOMIZATION']=='1')
			{
				$insert_data['post_date']=date("Y-m-d H:i",strtotime(date("Y-m-d"))+mt_rand(0,86399));
			} elseif ($options['USE_POST_DATE_RANDOMIZATION']=='2')
			{
				$insert_data['post_date']=date("Y-m-d H:i");
			}
			$insert_data['last_time_view_date']=date("Y-m-d H:i:s");

			$dir=get_correct_dir_name($insert_data['title']);
			$temp_dir=$dir;
			for ($i=2;$i<999999;$i++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=?",$temp_dir))==0)
				{
					$dir=$temp_dir;break;
				}
				$temp_dir=$dir.$i;
			}
			$insert_data['dir']=$dir;
			$insert_data['added_date']=date("Y-m-d H:i:s");

			$video_id=sql_insert("insert into $config[tables_prefix]videos set ?%",$insert_data);

			$dir_path = get_dir_by_id($video_id);
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$video_id", $video_id);
			}

			$base_path=rtrim($data['folder_standard_videos'],'/');
			if ($item['type']==2)
			{
				$base_path=rtrim($data['folder_premium_videos'],'/');
			}
			if ($item['folder_name']<>'')
			{
				$base_path.="/$item[folder_name]";
			}

			$has_source_file = false;
			$max_format_file = '';
			$max_format_file_size = 0;
			$forced_source = '';
			foreach ($item['files'] as $file_item)
			{
				switch ($file_item['file_type'])
				{
					case 0:
						if ($data['delete_files'] == 1)
						{
							@unlink("$base_path/$file_item[file_name]");
						}
						break;
					case 1:
						$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp";
						if ($data['delete_files'] == 1)
						{
							if (!@rename("$base_path/$file_item[file_name]", $target_path))
							{
								log_video("ERROR  Failed to move file to directory: $target_path", $video_id);
							}
						} else
						{
							if (!@copy("$base_path/$file_item[file_name]", $target_path))
							{
								log_video("ERROR  Failed to copy file to directory: $target_path", $video_id);
							}
						}
						$has_source_file = true;
						break;
					case 2:
						$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/$video_id{$file_item['format_postfix']}";
						if ($data['delete_files'] == 1)
						{
							if (!@rename("$base_path/$file_item[file_name]", $target_path))
							{
								log_video("ERROR  Failed to move file to directory: $target_path", $video_id);
							}
						} else
						{
							if (!@copy("$base_path/$file_item[file_name]", $target_path))
							{
								log_video("ERROR  Failed to copy file to directory: $target_path", $video_id);
							}
						}
						if (@sprintf("%.0f", filesize($target_path)) > $max_format_file_size)
						{
							$max_format_file = "$video_id{$file_item['format_postfix']}";
							$max_format_file_size = @sprintf("%.0f", filesize($target_path));
						}
						if ($file_item['is_use_as_source'] == 1)
						{
							$forced_source = "$video_id{$file_item['format_postfix']}";
						}
						break;
					case 8:
						if ($data['delete_files'] == 1)
						{
							@unlink("$base_path/$file_item[file_name]");
						}
				}
			}
			if ($forced_source)
			{
				$max_format_file = $forced_source;
			}
			if ($item['screenshots_zip'])
			{
				if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video_id/temp"))
				{
					log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$video_id/temp", $video_id);
				}

				if ($data['video_screenshots'] == 2)
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters.zip";
				} else
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots.zip";
				}
				if ($data['delete_files'] == 1)
				{
					if (!@rename("$base_path/$item[screenshots_zip]", $target_path))
					{
						if (!@copy("$base_path/$item[screenshots_zip]", $target_path))
						{
							log_video("ERROR  Failed to move file to directory: $target_path", $video_id);
						}
					}
				} else
				{
					if (!@copy("$base_path/$item[screenshots_zip]", $target_path))
					{
						log_video("ERROR  Failed to copy file to directory: $target_path", $video_id);
					}
				}
			} elseif (is_array($item['screenshots']))
			{
				if ($data['video_screenshots'] == 2)
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters";
				} else
				{
					$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots";
				}
				if (!mkdir_recursive($target_path))
				{
					log_video("ERROR  Failed to create directory: $target_path", $video_id);
				}

				$screenshot_index = 1;
				foreach ($item['screenshots'] as $screenshot_path)
				{
					if ($data['video_screenshots'] == 2)
					{
						$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/posters/poster{$screenshot_index}.jpg";
					} else
					{
						$target_path = "$config[content_path_videos_sources]/$dir_path/$video_id/temp/screenshots/screenshot{$screenshot_index}.jpg";
					}
					if ($data['delete_files'] == 1)
					{
						if (!@rename("$base_path/$screenshot_path", $target_path))
						{
							if (!@copy("$base_path/$screenshot_path", $target_path))
							{
								log_video("ERROR  Failed to move file to directory: $target_path", $video_id);
							}
						}
					} else
					{
						if (!@copy("$base_path/$screenshot_path", $target_path))
						{
							log_video("ERROR  Failed to copy file to directory: $target_path", $video_id);
						}
					}
					$screenshot_index++;
				}
			}
			$item['item_id']=$video_id;

			$background_task=array();
			$background_task['status_id']=intval($data['content_status']);
			if ($has_source_file)
			{
				$background_task['source']="$video_id.tmp";
			} else {
				$background_task['source']=$max_format_file;
			}
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=1, video_id=?, data=?, added_date=?",$video_id,serialize($background_task),date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=130, object_id=?, object_type_id=1, added_date=?",$admin_id,$admin_username,$video_id,date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]users_events set event_type_id=1, user_id=?, video_id=?, added_date=?",$insert_data['user_id'],$video_id,$insert_data['post_date']);
		} else {
			upload_folderLog("Importing album $item[title]");
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?",$item['external_key']))>0)
			{
				upload_folderLog("Skipped, duplicate album");
				continue;
			}
			$insert_data=array();
			$insert_data['title']=$item['title'];
			$insert_data['description']=trim($item['description']);
			$insert_data['external_key']=$item['external_key'];
			$insert_data['status_id']=3;
			$insert_data['rating']=$options['ALBUM_INITIAL_RATING'];
			$insert_data['rating_amount']=1;
			$insert_data['user_id']=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM']));
			$insert_data['admin_user_id']=$admin_id;

			if ($options['USE_POST_DATE_RANDOMIZATION_ALBUM']=='0')
			{
				$insert_data['post_date']=date("Y-m-d");
			} elseif ($options['USE_POST_DATE_RANDOMIZATION_ALBUM']=='1')
			{
				$insert_data['post_date']=date("Y-m-d H:i",strtotime(date("Y-m-d"))+mt_rand(0,86399));
			} elseif ($options['USE_POST_DATE_RANDOMIZATION_ALBUM']=='2')
			{
				$insert_data['post_date']=date("Y-m-d H:i");
			}
			$insert_data['last_time_view_date']=date("Y-m-d H:i:s");

			$dir=get_correct_dir_name($insert_data['title']);
			$temp_dir=$dir;
			for ($i=2;$i<999999;$i++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where dir=?",$temp_dir))==0)
				{
					$dir=$temp_dir;break;
				}
				$temp_dir=$dir.$i;
			}
			$insert_data['dir']=$dir;
			$insert_data['added_date']=date("Y-m-d H:i:s");

			$album_id=sql_insert("insert into $config[tables_prefix]albums set ?%",$insert_data);

			$dir_path = get_dir_by_id($album_id);
			if (!mkdir_recursive("$config[content_path_albums_sources]/$dir_path/$album_id"))
			{
				log_album("ERROR  Failed to create directory: $config[content_path_albums_sources]/$dir_path/$album_id", $album_id);
			}

			$base_path=rtrim($data['folder_albums'],'/');
			if ($item['folder_name']<>'')
			{
				$base_path.="/$item[folder_name]";
			}

			if ($item['images_zip']<>'')
			{
				if ($data['delete_files']==1)
				{
					if (!rename("$base_path/$item[images_zip]","$config[content_path_albums_sources]/$dir_path/$album_id/source.zip"))
					{
						log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$album_id/source.zip",$album_id);
					}
				} else {
					if (!copy("$base_path/$item[images_zip]","$config[content_path_albums_sources]/$dir_path/$album_id/source.zip"))
					{
						log_album("ERROR  Failed to copy file to directory: $config[content_path_albums_sources]/$dir_path/$album_id/source.zip",$album_id);
					}
				}
			} elseif (is_array($item['images']))
			{
				$zip_files_to_add=array();
				foreach ($item['images'] as $image_path)
				{
					$zip_files_to_add[]="$base_path/$image_path";
				}
				$zip = new PclZip("$config[content_path_albums_sources]/$dir_path/$album_id/source.zip");
				$zip->create($zip_files_to_add,$p_add_dir="",$p_remove_dir="$base_path");
				if ($data['delete_files']==1)
				{
					foreach ($item['images'] as $image_path)
					{
						unlink("$base_path/$image_path");
					}
				}

				if (!is_file("$config[content_path_albums_sources]/$dir_path/$album_id/source.zip"))
				{
					log_album("ERROR  Failed to copy file to directory: $config[content_path_albums_sources]/$dir_path/$album_id/source.zip",$album_id);
				}
			}
			if ($data['delete_files']==1)
			{
				foreach ($item['files'] as $file_item)
				{
					if ($file_item['file_type']==0 || $file_item['file_type']==8)
					{
						unlink("$base_path/$file_item[file_name]");
					}
				}
			}
			$item['item_id']=$album_id;

			$background_task=array();
			$background_task['status_id']=intval($data['content_status']);
			$background_task['source_file']="source.zip";
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=10, album_id=?, data=?, added_date=?",$album_id,serialize($background_task),date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=130, object_id=?, object_type_id=2, added_date=?",$admin_id,$admin_username,$album_id,date("Y-m-d H:i:s"));
			sql_pr("insert into $config[tables_prefix]users_events set event_type_id=2, user_id=?, album_id=?, added_date=?",$insert_data['user_id'],$album_id,$insert_data['post_date']);
		}

		if ($item['folder_name']<>'' && $data['delete_files']==1)
		{
			rmdir($base_path);
		}
		$data['processed_content'][]=$item;
	}

	file_put_contents("$plugin_path/$task_id.dat", serialize($data), LOCK_EX);
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);

	upload_folderLog("Content import finished");
}

function upload_folderLog($message)
{
	echo date("[Y-m-d H:i:s] ").$message."\n";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
