<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function tags_autogenerationInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/tags_autogeneration";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function tags_autogenerationIsEnabled()
{
	global $config;

	tags_autogenerationInit();
	$plugin_path="$config[project_path]/admin/data/plugins/tags_autogeneration";
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

function tags_autogenerationShow()
{
	global $config,$errors,$lang,$page_name;

	tags_autogenerationInit();
	$plugin_path="$config[project_path]/admin/data/plugins/tags_autogeneration";

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

		if (intval($_POST['lenient'])==2)
		{
			validate_field('empty',$_POST['lenient_list'],$lang['plugins']['tags_autogeneration']['field_lenient']);
		}

		if (!is_array($errors))
		{
			$data=array();
			$data['enable_for_videos']=intval($_POST['enable_for_videos']);
			$data['enable_for_albums']=intval($_POST['enable_for_albums']);
			$data['lenient']=intval($_POST['lenient']);
			$data['lenient_list']=trim($_POST['lenient_list']);

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
				return_ajax_success("$page_name?plugin_id=tags_autogeneration");
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

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		$_POST['enabled']=0;
		$_POST['enable_for_videos']=0;
		$_POST['enable_for_albums']=0;
		$_POST['lenient']=0;
		$_POST['lenient_list']='';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}
}

function tags_autogenerationGenerate($title,$description,$tags_all,$lenient,$lenient_list)
{
	$tags_found=array();

	$punkt=array(".",",",":",";","-","+","=","'","\"","(",")","`");
	foreach ($tags_all as $k=>$v)
	{
		if (strpos($k,' ')!==false || strpos($k,'-')!==false)
		{
			if (strpos($title,$k)!==false || strpos($description,$k)!==false)
			{
				$tags_found[$k]=$v;
			} elseif ($lenient==1 || ($lenient==2 && is_array($lenient_list) && $lenient_list[$k]>0))
			{
				$lenient_words=explode(' ',$k);
				$all_words_match=true;
				foreach ($lenient_words as $lenient_word)
				{
					if (strpos($title,$lenient_word)===false && strpos($description,$lenient_word)===false)
					{
						$all_words_match=false;
						break;
					}
				}
				if ($all_words_match)
				{
					$tags_found[$k]=$v;
				}
			}
		}
	}
	$title=str_replace($punkt," ",$title);
	$description=str_replace($punkt," ",$description);

	$temp=array_merge(explode(" ",$title),explode(" ",$description));
	foreach ($temp as $candidate)
	{
		$candidate=trim($candidate);
		if (strlen($candidate)<1 || $tags_found[$candidate]>0)
		{
			continue;
		}
		if ($tags_all[$candidate]>0)
		{
			$tags_found[$candidate]=$tags_all[$candidate];
		}
	}
	return $tags_found;
}

if ($_SERVER['argv'][1]=='exec' && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('setup.php');
	require_once('functions_base.php');

	$object_type=$_SERVER['argv'][2];
	$object_id=intval($_SERVER['argv'][3]);

	$plugin_path="$config[project_path]/admin/data/plugins/tags_autogeneration";
	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));

	$lenient_list=array();
	if ($data['lenient']==2)
	{
		$temp=explode(',',$data['lenient_list']);
		foreach ($temp as $list_item)
		{
			$lenient_list[mb_lowercase(trim($list_item))] = 1;
		}
	}

	if ($object_type=='video' && $data['enable_for_videos']>0)
	{
		$video_id=$object_id;
		if ($data['enable_for_videos']==2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_videos where video_id=?",$video_id))>0)
			{
				echo "Video already has tags, generation skipped";
				return;
			}
		}
		$res_video=mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=$video_id"));

		$tags_all=array();
		$temp=mr2array(sql_pr("select tag_id, tag, synonyms from $config[tables_prefix]tags"));
		foreach ($temp as $tag)
		{
			$tags_all[mb_lowercase($tag['tag'])]=$tag['tag_id'];
			$temp_syn=explode(",",$tag['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn=trim($syn);
					if (strlen($syn)>0)
					{
						$tags_all[mb_lowercase($syn)]=$tag['tag_id'];
					}
				}
			}
		}

		$tags_found=tags_autogenerationGenerate(mb_lowercase($res_video['title']),mb_lowercase($res_video['description']),$tags_all,$data['lenient'],$lenient_list);
		$tags_found_str='';
		$tags_added=array();
		foreach ($tags_found as $k=>$tag_id)
		{
			if (in_array($tag_id,$tags_added)) {continue;}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_videos where tag_id=? and video_id=?",$tag_id,$video_id))==0)
			{
				sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?",$tag_id,$video_id);
				$tags_found_str.="$k, ";
			}
			$tags_added[]=$tag_id;
		}
		if ($tags_found_str<>'')
		{
			echo "Autogenerated tags: ".substr($tags_found_str,0,strlen($tags_found_str)-2);
		} else {
			echo "No autogenerated tags";
		}
	} elseif ($object_type=='album' && $data['enable_for_albums']>0)
	{
		$album_id=$object_id;
		if ($data['enable_for_albums']==2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_albums where album_id=?",$album_id))>0)
			{
				echo "Album already has tags, generation skipped";
				return;
			}
		}
		$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));

		$tags_all=array();
		$temp=mr2array(sql_pr("select tag_id, tag, synonyms from $config[tables_prefix]tags"));
		foreach ($temp as $tag)
		{
			$tags_all[mb_lowercase($tag['tag'])]=$tag['tag_id'];
			$temp_syn=explode(",",$tag['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn=trim($syn);
					if (strlen($syn)>0)
					{
						$tags_all[mb_lowercase($syn)]=$tag['tag_id'];
					}
				}
			}
		}

		$tags_found=tags_autogenerationGenerate(mb_lowercase($res_album['title']),mb_lowercase($res_album['description']),$tags_all,$data['lenient'],$lenient_list);
		$tags_found_str='';
		$tags_added=array();
		foreach ($tags_found as $k=>$tag_id)
		{
			if (in_array($tag_id,$tags_added)) {continue;}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_albums where tag_id=? and album_id=?",$tag_id,$album_id))==0)
			{
				sql_pr("insert into $config[tables_prefix]tags_albums set tag_id=?, album_id=?",$tag_id,$album_id);
				$tags_found_str.="$k, ";
			}
			$tags_added[]=$tag_id;
		}
		if ($tags_found_str<>'')
		{
			echo "Autogenerated tags: ".substr($tags_found_str,0,strlen($tags_found_str)-2);
		} else {
			echo "No autogenerated tags";
		}
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
