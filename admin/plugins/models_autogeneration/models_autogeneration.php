<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function models_autogenerationInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/models_autogeneration";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function models_autogenerationIsEnabled()
{
	global $config;

	models_autogenerationInit();
	$plugin_path="$config[project_path]/admin/data/plugins/models_autogeneration";
	if (is_file("$plugin_path/data.dat"))
	{
		$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
		if ($data['enabled']==1)
		{
			return true;
		}
	}
	return false;
}

function models_autogenerationShow()
{
	global $config,$errors,$page_name;

	models_autogenerationInit();
	$plugin_path="$config[project_path]/admin/data/plugins/models_autogeneration";

	$errors = null;

	if ($_POST['action']=='save')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		$data=array();
		$data['enable_for_videos']=intval($_POST['enable_for_videos']);
		$data['enable_for_albums']=intval($_POST['enable_for_albums']);
		if (intval($_POST['enable_for_videos'])+intval($_POST['enable_for_albums'])>0)
		{
			$data['enabled']=1;
		} else {
			$data['enabled']=0;
		}
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

		if (!is_file("$plugin_path/data.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			return_ajax_success("$page_name?plugin_id=models_autogeneration");
		} else {
			return_ajax_errors($errors);
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		$_POST['enabled']=0;
		$_POST['enable_for_videos']=0;
		$_POST['enable_for_albums']=0;

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}
}

function models_autogenerationGenerate($title,$description,$tags,$models_all)
{
	$models_found=array();

	$tags=explode(',',$tags);
	foreach ($models_all as $k=>$v)
	{
		if (strpos($k,' ')!==false || strpos($k,'-')!==false)
		{
			$pos=strpos($title,$k);
			if ($pos!==false)
			{
				if (($pos==0 || preg_replace('/\P{L}/u',"xxx",$title[$pos-1])=='xxx') && ($pos==strlen($title)-strlen($k) || preg_replace('/\P{L}/u',"xxx",$title[$pos+strlen($k)])=='xxx'))
				{
					$models_found[$k]=$v;
				}
			}
			$pos=strpos($description,$k);
			if ($pos!==false)
			{
				if (($pos==0 || preg_replace('/\P{L}/u',"xxx",$description[$pos-1])=='xxx') && ($pos==strlen($description)-strlen($k) || preg_replace('/\P{L}/u',"xxx",$description[$pos+strlen($k)])=='xxx'))
				{
					$models_found[$k]=$v;
				}
			}

			foreach ($tags as $tag)
			{
				if ($k==$tag)
				{
					$models_found[$k]=$v;
				}
			}
		}
	}
	unset($temp);
	unset($temp2);
	preg_match_all('/([\p{N}\p{L}-_#@]+)/u',$title,$temp);
	preg_match_all('/([\p{N}\p{L}-_#@]+)/u',$description,$temp2);

	$temp=array_merge($temp[0],$temp2[0],$tags);
	foreach ($temp as $candidate)
	{
		$candidate=trim($candidate);
		if (strlen($candidate)<1 || $models_found[$candidate]>0)
		{
			continue;
		}
		if ($models_all[$candidate]>0)
		{
			$models_found[$candidate]=$models_all[$candidate];
		}
	}
	return $models_found;
}

if ($_SERVER['argv'][1]=='exec' && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('setup.php');
	require_once('functions_base.php');

	$object_type=$_SERVER['argv'][2];
	$object_id=intval($_SERVER['argv'][3]);

	$plugin_path="$config[project_path]/admin/data/plugins/models_autogeneration";
	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));

	if ($object_type=='video' && $data['enable_for_videos']>0)
	{
		$video_id=$object_id;
		if ($data['enable_for_videos']==2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models_videos where video_id=?",$video_id))>0)
			{
				echo "Video already has models, generation skipped";
				return;
			}
		}
		$res_video=mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=$video_id"));
		$res_video['tags']=implode(",",mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) as tag from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=?",$video_id)));

		$models_all=array();
		$temp=mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
		foreach ($temp as $model)
		{
			$models_all[mb_lowercase($model['title'])]=$model['model_id'];
			$temp_syn=explode(",",$model['alias']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn=trim($syn);
					if (strlen($syn)>0)
					{
						$models_all[mb_lowercase($syn)]=$model['model_id'];
					}
				}
			}
		}

		$models_found=models_autogenerationGenerate(mb_lowercase($res_video['title']),mb_lowercase($res_video['description']),mb_lowercase($res_video['tags']),$models_all);
		$models_found_str='';
		$models_added=array();
		foreach ($models_found as $k=>$model_id)
		{
			if (in_array($model_id,$models_added)) {continue;}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models_videos where model_id=? and video_id=?",$model_id,$video_id))==0)
			{
				sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?",$model_id,$video_id);
				$models_found_str.="$k, ";
			}
			$models_added[]=$model_id;
		}
		if ($models_found_str<>'')
		{
			echo "Autogenerated models: ".substr($models_found_str,0,strlen($models_found_str)-2);
		} else {
			echo "No autogenerated models";
		}
	} elseif ($object_type=='album' && $data['enable_for_albums']>0)
	{
		$album_id=$object_id;
		if ($data['enable_for_albums']==2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models_albums where album_id=?",$album_id))>0)
			{
				echo "Album already has models, generation skipped";
				return;
			}
		}
		$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));
		$res_album['tags']=implode(",",mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_albums.tag_id) as tag from $config[tables_prefix]tags_albums where $config[tables_prefix]tags_albums.album_id=?",$album_id)));

		$models_all=array();
		$temp=mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
		foreach ($temp as $model)
		{
			$models_all[mb_lowercase($model['title'])]=$model['model_id'];
			$temp_syn=explode(",",$model['alias']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn=trim($syn);
					if (strlen($syn)>0)
					{
						$models_all[mb_lowercase($syn)]=$model['model_id'];
					}
				}
			}
		}

		$models_found=models_autogenerationGenerate(mb_lowercase($res_album['title']),mb_lowercase($res_album['description']),mb_lowercase($res_album['tags']),$models_all);
		$models_found_str='';
		$models_added=array();
		foreach ($models_found as $k=>$model_id)
		{
			if (in_array($model_id,$models_added)) {continue;}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models_albums where model_id=? and album_id=?",$model_id,$album_id))==0)
			{
				sql_pr("insert into $config[tables_prefix]models_albums set model_id=?, album_id=?",$model_id,$album_id);
				$models_found_str.="$k, ";
			}
			$models_added[]=$model_id;
		}
		if ($models_found_str<>'')
		{
			echo "Autogenerated models: ".substr($models_found_str,0,strlen($models_found_str)-2);
		} else {
			echo "No autogenerated models";
		}
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
