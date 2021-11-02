<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';

$options = get_options();

$memory_limit=intval($options['LIMIT_MEMORY']);
if ($memory_limit==0)
{
	$memory_limit=512;
}
ini_set('memory_limit',"{$memory_limit}M");

$mass_edit_id=intval($_SERVER['argv'][1]);
$admin_id=intval($_SERVER['argv'][2]);
$is_access_to_own_content=intval($_SERVER['argv'][3]);
$is_access_to_disabled_content=intval($_SERVER['argv'][4]);
$is_access_to_content_flagged_with=trim($_SERVER['argv'][5]);
$background_task_id=intval($_SERVER['argv'][6]);

$config['sql_safe_mode'] = 1;
$admin_username=mr2string(sql_pr("select login from $config[tables_prefix]admin_users where user_id=?",$admin_id));
unset($config['sql_safe_mode']);

if ($mass_edit_id < 1 || !is_file("$config[temporary_path]/mass-edit-$mass_edit_id.dat")) {die;}
$data=@unserialize(file_get_contents("$config[temporary_path]/mass-edit-$mass_edit_id.dat"));
if (!is_array($data)) {die;}

log_massedit("Started massedit $mass_edit_id");

if (intval($data['all'])==0)
{
	$ids_str=implode(",",$data['ids']);
	$all_str='';
} else {
	$ids_str='0';
	$all_str=' or 1=1 ';
}

unset($where);
if ($is_access_to_own_content==1)
{
	$where.=" and admin_user_id=$admin_id ";
}
if ($is_access_to_disabled_content==1)
{
	$where.=" and status_id=0 ";
}
if ($is_access_to_content_flagged_with > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $is_access_to_content_flagged_with)));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}

$videos_result=sql("select * from $config[tables_prefix]videos where status_id in (0,1) and (video_id in ($ids_str) $all_str) $where order by video_id asc");

$total=mr2rows($videos_result);
$done=0;

if ($data['regenerate_directories']==1)
{
	if ($data['regenerate_directories_language']=='')
	{
		sql("update $config[tables_prefix]videos set dir=concat(dir, '-') where title!='' and status_id in (0,1) and (video_id in ($ids_str) $all_str) $where");
	} else {
		$directories_language=mr2array_single(sql_pr("select * from $config[tables_prefix]languages where code=?",$data['regenerate_directories_language']));
		if ($directories_language['code']!='')
		{
			sql("update $config[tables_prefix]videos set dir_{$data['regenerate_directories_language']}=concat(dir_{$data['regenerate_directories_language']}, '-') where title_{$data['regenerate_directories_language']}!='' and status_id in (0,1) and (video_id in ($ids_str) $all_str) $where");
		} else {
			$data['regenerate_directories']=0;
			$data['regenerate_directories_language']='';
		}
	}
}

$formats_videos=mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));
$formats_videos_to_create=array();
$formats_videos_to_delete=array();
if (@count($data['video_format_create_ids'])>0)
{
	foreach ($formats_videos as $formats_video)
	{
		if (in_array($formats_video['postfix'],$data['video_format_create_ids']))
		{
			$formats_videos_to_create[]=$formats_video;
		}
	}
}
if (@count($data['video_format_delete_ids'])>0)
{
	foreach ($formats_videos as $formats_video)
	{
		if (in_array($formats_video['postfix'],$data['video_format_delete_ids']) && in_array($formats_video['status_id'],array(0,2)))
		{
			$formats_videos_to_delete[]=$formats_video;
		}
	}
}

$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
$anonymous_user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));

while ($video = mr2array_single($videos_result))
{
	log_massedit("Processing video $video[video_id]");

	$update_array=array();
	if (@count($data['admin_user_ids'])>0)
	{
		$config['sql_safe_mode'] = 1;
		$admin_user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]admin_users where user_id=?",$data['admin_user_ids'][mt_rand(1,count($data['admin_user_ids']))-1]));
		unset($config['sql_safe_mode']);
		if ($admin_user_id>0)
		{
			$update_array['admin_user_id']=$admin_user_id;
		}
	}
	if ($data['regenerate_directories']==1)
	{
		if ($data['regenerate_directories_language']=='' && $video['title']!='')
		{
			$update_array['dir']=get_correct_dir_name($video['title']);

			if ($update_array['dir']<>'')
			{
				$temp_dir=$update_array['dir'];
				for ($i=2;$i<999999;$i++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=?",$temp_dir))==0)
					{
						$update_array['dir']=$temp_dir;break;
					}
					$temp_dir=$update_array['dir'].$i;
				}
			}
		} elseif ($data['regenerate_directories_language']!='' && $video["title_{$data['regenerate_directories_language']}"]!='')
		{
			$update_array["dir_{$data['regenerate_directories_language']}"]=get_correct_dir_name($video["title_{$data['regenerate_directories_language']}"],$directories_language);

			if ($update_array["dir_{$data['regenerate_directories_language']}"]<>'')
			{
				$temp_dir=$update_array["dir_{$data['regenerate_directories_language']}"];
				for ($i=2;$i<999999;$i++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir_{$data['regenerate_directories_language']}=?",$temp_dir))==0)
					{
						$update_array["dir_{$data['regenerate_directories_language']}"]=$temp_dir;break;
					}
					$temp_dir=$update_array["dir_{$data['regenerate_directories_language']}"].$i;
				}
			}
		}
	}
	if ($data['status_id']<>'')
	{
		if (intval($data['status_id'])==0 || (intval($data['status_id'])==1 && $video['title']<>''))
		{
			$update_array['status_id']=intval($data['status_id']);
		}
	}
	if ($data['is_private']<>'')
	{
		if (intval($data['is_private'])==0)
		{
			if ($video['is_private']==1)
			{
				$update_array['is_private']=0;
			}
		} elseif (intval($data['is_private'])==1)
		{
			if ($video['is_private']==0)
			{
				$update_array['is_private']=1;
			}
		}
	}
	if ($data['access_level_id']<>'')
	{
		$update_array['access_level_id']=intval($data['access_level_id']);
	}
	if ($data['tokens_required']<>'')
	{
		$update_array['tokens_required']=intval($data['tokens_required']);
	}
	if ($data['release_year']<>'')
	{
		$update_array['release_year']=intval($data['release_year']);
	}
	if ($data['users']<>'')
	{
		$users=explode(",",trim($data['users']));
		if (count($users)>0)
		{
			$video['old_user_id']=$video['user_id'];

			$user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$users[mt_rand(1,count($users))-1]));
			if ($user_id>0)
			{
				$update_array['user_id']=$user_id;
				$video['user_id']=$user_id;
			}
		}
	}
	if ($data['content_source_id']<>'')
	{
		if ($data['content_source_id']=='-1')
		{
			$update_array['content_source_id']=0;
		} else {
			$update_array['content_source_id']=intval($data['content_source_id']);
		}
	}
	if ($data['dvd_id']<>'')
	{
		if ($data['dvd_id']=='-1')
		{
			$update_array['dvd_id']=0;
		} else {
			$update_array['dvd_id']=intval($data['dvd_id']);
		}
	}
	if ($data['admin_flag_id']<>'')
	{
		if ($data['admin_flag_id']=='-1')
		{
			$update_array['admin_flag_id']=0;
		} else {
			$update_array['admin_flag_id']=intval($data['admin_flag_id']);
		}
	}
	if ($data['is_locked']<>'')
	{
		$update_array['is_locked']=intval($data['is_locked']);
	}
	if ($data['is_review_needed']<>'')
	{
		$update_array['is_review_needed']=intval($data['is_review_needed']);
	}
	if ($data['change_rating']==1)
	{
		$rating_min=floatval($data['rating_min']);
		$rating_max=floatval($data['rating_max']);
		$votes_min=intval($data['rating_amount_min']);
		$votes_max=intval($data['rating_amount_max']);

		$rating=floatval(mt_rand($rating_min*10,$rating_max*10))/10;
		$votes=mt_rand($votes_min,$votes_max);
		$update_array['rating']=$rating*$votes;
		$update_array['rating_amount']=$votes;
	}
	if ($data['change_visits']==1)
	{
		$update_array['video_viewed']=mt_rand(intval($data['visits_min']),intval($data['visits_max']));
	}
	if ($data['rotator_reset_main_stats']==1)
	{
		$update_array['r_dlist']=0;
		$update_array['r_ccount']=0;
		$update_array['r_cweight']=0;
		$update_array['r_ctr']=0;
	}
	if ($data['rotator_reset_screenshots_stats']==1)
	{
		$update_array['rs_dlist']=0;
		$update_array['rs_ccount']=0;
		$update_array['rs_completed']=0;
		$update_array['screen_main_temp']=0;
	}
	if ($data['change_post_date_fixed']==1)
	{
		$old_post_date_array=getdate(strtotime($video['post_date']));
		$post_date_from=strtotime($data["post_date_from"]);
		$post_date_from_array=getdate($post_date_from);
		$post_date_to=strtotime($data["post_date_to"]);
		$days=ceil(($post_date_to-$post_date_from)/86400);
		$post_date=date("Y-m-d H:i:s",mktime(intval($old_post_date_array['hours']),intval($old_post_date_array['minutes']),intval($old_post_date_array['seconds']),intval($post_date_from_array['mon']),intval($post_date_from_array['mday'])+mt_rand(0,$days),intval($post_date_from_array['year'])));

		if (intval($data['post_time_change'])==1)
		{
			$post_date=date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($post_date)))+mt_rand(intval($data['post_time_from']),intval($data['post_time_to'])));
		}
		$update_array['post_date']=$post_date;
		$update_array['relative_post_date']=0;
	} elseif ($data['change_post_date_relative']==1)
	{
		$relative_post_date_from=intval($data['relative_post_date_from']);
		$relative_post_date_to=intval($data['relative_post_date_to']);
		$relative_post_date=intval(mt_rand($relative_post_date_from,$relative_post_date_to));
		if ($relative_post_date==0)
		{
			for ($i=0;$i<9999;$i++)
			{
				$relative_post_date=intval(mt_rand($relative_post_date_from,$relative_post_date_to));
				if ($relative_post_date<>0)
				{
					break;
				}
			}
		}
		if ($relative_post_date<>0)
		{
			$update_array['post_date']='1971-01-01 00:00:00';
			$update_array['relative_post_date']=$relative_post_date;
		}
	} elseif (intval($data['post_time_change'])==1)
	{
		if (intval($video['relative_post_date'])==0)
		{
			$update_array['post_date']=date("Y-m-d H:i:s",strtotime(date('Y-m-d',strtotime($video['post_date'])))+mt_rand(intval($data['post_time_from']),intval($data['post_time_to'])));
		}
	}
	if ($data['video_format_duration_id']<>'')
	{
		$available_formats=get_video_formats($video['video_id'],$video['file_formats']);
		if (isset($available_formats[$data['video_format_duration_id']]))
		{
			$format_for_duration=$available_formats[$data['video_format_duration_id']];
			if ($format_for_duration['duration']>0) {
				$update_array['duration']=$format_for_duration['duration'];
			}
		}
	}
	if (count($update_array)>0)
	{
		sql_pr("update $config[tables_prefix]videos set ?% where video_id=? and status_id in (0,1)",$update_array,$video['video_id']);
	}
	if (isset($update_array['user_id']))
	{
		sql_pr("update $config[tables_prefix]users_events set user_id=? where event_type_id in (1,6,7) and video_id=?",$video['user_id'],$video['video_id']);
	}
	if (isset($update_array['is_private']))
	{
		if (intval($update_array['relative_post_date'])==0 && $video['relative_post_date']==0)
		{
			$event_type_id=6;
			if ($update_array['is_private']==0)
			{
				$event_type_id=7;
			}
			sql_pr("insert into $config[tables_prefix]users_events set event_type_id=?, user_id=?, video_id=?, added_date=?",$event_type_id,$video['user_id'],$video['video_id'],date("Y-m-d H:i:s"));
		}
	}
	if (isset($update_array['post_date']))
	{
		if ($update_array['relative_post_date']==0)
		{
			sql_pr("update $config[tables_prefix]comments set added_date=date_add(?, INTERVAL UNIX_TIMESTAMP(added_date) - UNIX_TIMESTAMP(?) SECOND) where object_id=? and object_type_id=1",$update_array['post_date'],$video['post_date'],$video['video_id']);
			sql_pr("update $config[tables_prefix]comments set added_date=greatest(?, ?) where object_id=? and object_type_id=1 and added_date>?", $update_array['post_date'], date("Y-m-d H:i:s"), $video['video_id'], date("Y-m-d H:i:s"));
			sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4",$video['video_id']);
		} else {
			sql_pr("update $config[tables_prefix]comments set added_date=? where object_id=? and object_type_id=1",$update_array['post_date'],$video['video_id']);
			sql_pr("update $config[tables_prefix]users_events set added_date=(select added_date from $config[tables_prefix]comments where $config[tables_prefix]comments.comment_id=$config[tables_prefix]users_events.comment_id) where video_id=? and event_type_id=4",$video['video_id']);
		}
		sql("update $config[tables_prefix]users_events set added_date=(select post_date from $config[tables_prefix]videos where $config[tables_prefix]videos.video_id=$config[tables_prefix]users_events.video_id) where video_id=$video[video_id] and event_type_id=1");
		sql("delete from $config[tables_prefix]users_events where event_type_id in (6,7) and video_id=$video[video_id]");
	}
	if (isset($update_array['user_id']) || isset($update_array['is_private']))
	{
		sql_pr("update $config[tables_prefix]users set
					public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
					private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
					premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
					total_videos_count=public_videos_count+private_videos_count+premium_videos_count
				where user_id in (?,?)",intval($video['user_id']),intval($video['old_user_id'])
		);
	}

	if (is_array($data['category_ids_add']))
	{
		foreach ($data['category_ids_add'] as $category_id)
		{
			if ($category_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_videos where category_id=? and video_id=?",$category_id,$video['video_id']))==0)
				{
					sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?",$category_id,$video['video_id']);
				}
			}
		}
	}
	if (is_array($data['category_ids_delete']))
	{
		foreach ($data['category_ids_delete'] as $category_id)
		{
			sql_pr("delete from $config[tables_prefix]categories_videos where category_id=? and video_id=?",$category_id,$video['video_id']);
		}
	}
	if (is_array($data['model_ids_add']))
	{
		foreach ($data['model_ids_add'] as $model_id)
		{
			if ($model_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models_videos where model_id=? and video_id=?",$model_id,$video['video_id']))==0)
				{
					sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?",$model_id,$video['video_id']);
				}
			}
		}
	}
	if (is_array($data['model_ids_delete']))
	{
		foreach ($data['model_ids_delete'] as $model_id)
		{
			sql_pr("delete from $config[tables_prefix]models_videos where model_id=? and video_id=?",$model_id,$video['video_id']);
		}
	}
	if (is_array($data['tags_add']))
	{
		foreach ($data['tags_add'] as $tag)
		{
			if (strlen(trim($tag))>0)
			{
				$tag_id=find_or_create_tag($tag,$options);
				if ($tag_id>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]tags_videos where tag_id=? and video_id=?",$tag_id,$video['video_id']))==0)
				{
					sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?",$tag_id,$video['video_id']);
				}
			}
		}
	}
	if (is_array($data['tags_delete']))
	{
		foreach ($data['tags_delete'] as $tag)
		{
			$tag_id=mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?",$tag));
			if ($tag_id>0)
			{
				sql_pr("delete from $config[tables_prefix]tags_videos where tag_id=? and video_id=?",$tag_id,$video['video_id']);
			}
		}
	}
	if ($data['flag_id']<>'')
	{
		sql_pr("delete from $config[tables_prefix]flags_videos where flag_id=? and video_id=?",intval($data['flag_id']),$video['video_id']);
	}
	if ($data['delete_source_files']==1)
	{
		$dir_path=get_dir_by_id($video['video_id']);
		@unlink("$config[content_path_videos_sources]/$dir_path/$video[video_id]/$video[video_id].tmp");
	}
	if ($data['recreate_overview_screenshots']==1)
	{
		$dir_path=get_dir_by_id($video['video_id']);
		if (is_file("$config[content_path_videos_sources]/$dir_path/$video[video_id]/$video[video_id].tmp") || $video['load_type_id']==1 || $video['load_type_id']==2)
		{
			$background_task_types=mr2array_list(sql_pr("select type_id from $config[tables_prefix]background_tasks where video_id=$video[video_id]"));
			if (!in_array(24,$background_task_types))
			{
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=24, video_id=?, data=?, added_date=?",$video['video_id'],serialize(array()),date("Y-m-d H:i:s"));
			}
		}
	}
	if ($data['delete_overview_screenshots'] == 1)
	{
		if ($video['screen_amount'] > 1)
		{
			$background_task_types = mr2array_list(sql_pr("select type_id from $config[tables_prefix]background_tasks where video_id=$video[video_id]"));
			if (!in_array(28, $background_task_types))
			{
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=28, video_id=?, data=?, added_date=?", $video['video_id'], serialize(array()), date("Y-m-d H:i:s"));
			}
		}
	}
	if (@count($data['screenshot_format_recreate_ids']) > 0)
	{
		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=29, video_id=?, data=?, added_date=?", $video['video_id'], serialize(['format_ids' => $data['screenshot_format_recreate_ids']]), date("Y-m-d H:i:s"));
	}
	if ($data['rotator_reset_screenshots_stats']==1)
	{
		$dir_path=get_dir_by_id($video['video_id']);
		@unlink("$config[content_path_videos_sources]/$dir_path/$video[video_id]/screenshots/rotator.dat");
		log_video("",$video['video_id']);
		log_video("INFO  Screenshots rotator stats are reset",$video['video_id']);
	}
	if ($data['new_storage_group_id']<>'' && $video['server_group_id']>0 && $video['server_group_id']<>$data['new_storage_group_id'])
	{
		$background_task=array();
		$background_task['server_group_id']=intval($data['new_storage_group_id']);
		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=15, video_id=?, data=?, added_date=?",$video['video_id'],serialize($background_task),date("Y-m-d H:i:s"));
	}
	if (@count($formats_videos_to_create)>0)
	{
		if ($video['load_type_id']==1)
		{
			foreach ($formats_videos_to_create as $formats_videos_to_create_item)
			{
				if ($formats_videos_to_create_item['video_type_id']==0 && ($video['is_private']==0 || $video['is_private']==1))
				{
					$background_task=array();
					$background_task['format_postfix']=$formats_videos_to_create_item['postfix'];
					if ($data['video_format_create_disable_wm']==1)
					{
						$background_task['skip_watermark']=1;
					}
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, added_date=?",$video['video_id'],serialize($background_task),date("Y-m-d H:i:s"));
				} elseif ($formats_videos_to_create_item['video_type_id']==1 && $video['is_private']==2)
				{
					$background_task=array();
					$background_task['format_postfix']=$formats_videos_to_create_item['postfix'];
					if ($data['video_format_create_disable_wm']==1)
					{
						$background_task['skip_watermark']=1;
					}
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=4, video_id=?, data=?, added_date=?",$video['video_id'],serialize($background_task),date("Y-m-d H:i:s"));
				}
			}
		}
	}
	if ($data['video_format_upload_id'])
	{
		if ($video['load_type_id'] == 1)
		{
			$video_formats = get_video_formats($video['video_id'], $video['file_formats']);
			if (isset($video_formats[$data['video_format_upload_id']]))
			{
				$dir_path = get_dir_by_id($video['video_id']);
				@unlink("$config[content_path_videos_sources]/$dir_path/$video[video_id]/$video[video_id]{$data['video_format_upload_id']}");
				mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$video[video_id]");
				copy($data['video_format_upload_file'], "$config[content_path_videos_sources]/$dir_path/$video[video_id]/$video[video_id]{$data['video_format_upload_id']}");

				$background_task = array();
				$background_task['format_postfix'] = $data['video_format_upload_id'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=3, video_id=?, data=?, added_date=?", $video['video_id'], serialize($background_task), date("Y-m-d H:i:s"));
			}
		}
	}
	if (@count($formats_videos_to_delete)>0)
	{
		$available_formats=get_video_formats($video['video_id'],$video['file_formats']);
		foreach ($formats_videos_to_delete as $formats_videos_to_delete_item)
		{
			if (isset($available_formats[$formats_videos_to_delete_item['postfix']]))
			{
				$background_task=array();
				$background_task['format_postfix']=$formats_videos_to_delete_item['postfix'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=5, video_id=?, data=?, added_date=?",$video['video_id'],serialize($background_task),date("Y-m-d H:i:s"));
			}
		}
	}

	if (intval($memberzone_data['AWARDS_VIDEO_UPLOAD'])>0)
	{
		if ($data['status_id']==1 && $video['user_id']<>$anonymous_user_id && $video['duration']>=intval($memberzone_data['AWARDS_VIDEO_UPLOAD_CONDITION']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=4 and video_id=?",$video['video_id']))==0)
		{
			sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=4, user_id=?, video_id=?, tokens_granted=?, added_date=?",$video['user_id'],$video['video_id'],intval($memberzone_data['AWARDS_VIDEO_UPLOAD']),date("Y-m-d H:i:s"));
			sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_VIDEO_UPLOAD']),$video['user_id']);
		}
	}

	if (isset($data['post_process_plugins']) && is_array($data['post_process_plugins']))
	{
		foreach ($data['post_process_plugins'] as $plugin)
		{
			if (!is_file("$config[project_path]/admin/plugins/$plugin/$plugin.php"))
			{
				continue;
			}
			log_video("",$video['video_id']);
			log_video("INFO  Executing $plugin plugin",$video['video_id']);
			unset($res);
			exec("cd $config[project_path]/admin/include && $config[php_path] $config[project_path]/admin/plugins/$plugin/$plugin.php exec video $video[video_id] 2>&1",$res);
			if ($res[0]<>'')
			{
				log_video("....".implode("\n....",$res),$video['video_id'],1);
			} else {
				log_video("....no response",$video['video_id'],1);
			}
		}
	}

	$update_details='';
	if (count($update_array)>0)
	{
		foreach ($update_array as $k=>$v)
		{
			$update_details.="$k, ";
		}
		if (strlen($update_details)>0)
		{
			$update_details=substr($update_details,0, -2);
		}
	}
	sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=160, object_id=?, object_type_id=1, action_details=?, added_date=?",$admin_id,$admin_username,$video['video_id'],$update_details,date("Y-m-d H:i:s"));

	$done++;
	$pc=floor(($done/$total)*100);

	file_put_contents("$config[temporary_path]/mass-edit-progress-$mass_edit_id.dat","$pc",LOCK_EX);
	file_put_contents("$config[project_path]/admin/data/engine/tasks/$background_task_id.dat","$pc",LOCK_EX);

	usleep(20000);
}

if ($data['video_format_upload_file']!='')
{
	@unlink($data['video_format_upload_file']);
}
@unlink("$config[temporary_path]/mass-edit-$mass_edit_id.dat");

$task_data=mr2array_single(sql_pr("select * from $config[tables_prefix]background_tasks where task_id=?",$background_task_id));
sql_pr("delete from $config[tables_prefix]background_tasks where task_id=?",$background_task_id);
if ($task_data['task_id']>0)
{
	sql_pr("insert into $config[tables_prefix]background_tasks_history set task_id=?, status_id=3, type_id=52, start_date=?, end_date=?, effective_duration=UNIX_TIMESTAMP(end_date)-UNIX_TIMESTAMP(start_date)",$task_data['task_id'],$task_data['start_date'],date("Y-m-d H:i:s"));
}
@unlink("$config[project_path]/admin/data/engine/tasks/$background_task_id.dat");

function log_massedit($message)
{
	echo date("[Y-m-d H:i:s] ").$message."\n";
}
