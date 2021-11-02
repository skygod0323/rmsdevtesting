<?php
function post_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	$post_types=array();
	$post_types_temp=mr2array(sql("select * from $config[tables_prefix]posts_types"));
	foreach ($post_types_temp as $post_type_temp)
	{
		$post_types[$post_type_temp['post_type_id']]=$post_type_temp;
	}

	if (isset($block_config['var_post_id']) && intval($_REQUEST[$block_config['var_post_id']])>0)
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts_admin] and post_id=?",intval($_REQUEST[$block_config['var_post_id']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts] and post_id=?",intval($_REQUEST[$block_config['var_post_id']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts_active_disabled] and post_id=?",intval($_REQUEST[$block_config['var_post_id']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_post_dir']])<>'' && trim($_REQUEST[$block_config['var_post_dir']])<>$data['dir'] && isset($post_types[$data['post_type_id']]))
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['post_id'],str_replace("%DIR%",$data['dir'],$post_types[$data['post_type_id']]['url_pattern']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_post_dir']])<>'')
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts_admin] and dir=?",trim($_REQUEST[$block_config['var_post_dir']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts] and dir=?",trim($_REQUEST[$block_config['var_post_dir']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where $database_selectors[where_posts_active_disabled] and dir=?",trim($_REQUEST[$block_config['var_post_dir']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	$data['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$data['user_id']));
	$data['username']=$data['user']['display_name'];
	$data['user_avatar']=$data['user']['avatar'];

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_posts where $config[tables_prefix]flags_posts.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_posts.post_id=?) as votes from $config[tables_prefix]flags where group_id=4",$data['post_id']));
	$data['flags']=array();
	foreach($flags as $flag)
	{
		$data['flags'][$flag['external_id']]=$flag['votes'];
	}

	$data['time_passed_from_adding']=get_time_passed($data['post_date']);

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_posts on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_posts.category_id where $database_selectors[where_categories] and post_id=$data[post_id]"));
	$category_group_ids=array();
	foreach ($data['categories'] as $k=>$v)
	{
		$data['categories_as_string'].=$v['title'].", ";
		$data['categories'][$k]['base_files_url']=$config['content_url_categories'].'/'.$v['category_id'];
		if ($v['category_group_id']>0)
		{
			$category_group_ids[]=$v['category_group_id'];
		}
	}
	$data['categories_as_string']=rtrim($data['categories_as_string'],", ");

	if (count($category_group_ids)>0)
	{
		$category_group_ids=implode(',',array_unique($category_group_ids));
		$category_groups=mr2array(sql("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] and category_group_id in ($category_group_ids)"));
		foreach ($data['categories'] as $k=>$v)
		{
			if ($v['category_group_id']>0)
			{
				foreach ($category_groups as $category_group)
				{
					if ($v['category_group_id']==$category_group['category_group_id'])
					{
						$category_group['base_files_url']=$config['content_url_categories'].'/groups/'.$category_group['category_group_id'];
						$data['categories'][$k]['category_group']=$category_group;
						break;
					}
				}
			}
		}
	}

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_posts on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_posts.tag_id where $database_selectors[where_tags] and post_id=$data[post_id]"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_posts on $config[tables_prefix]models.model_id=$config[tables_prefix]models_posts.model_id where $database_selectors[where_models] and post_id=$data[post_id]"));
	$model_group_ids=array();
	foreach ($data['models'] as $k=>$v)
	{
		$data['models'][$k]['base_files_url']=$config['content_url_models'].'/'.$v['model_id'];
		if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
		{
			$pattern=str_replace("%ID%",$v['model_id'],str_replace("%DIR%",$v['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
			$data['models'][$k]['view_page_url']="$config[project_url]/$pattern";
		}
		$data['models_as_string'].=$v['title'].", ";
		if ($v['model_group_id']>0)
		{
			$model_group_ids[]=$v['model_group_id'];
		}
	}
	$data['models_as_string']=rtrim($data['models_as_string'],", ");

	if (count($model_group_ids)>0)
	{
		$model_group_ids=implode(',',array_unique($model_group_ids));
		$model_groups=mr2array(sql("select $database_selectors[models_groups] from $config[tables_prefix]models_groups where $database_selectors[where_models_groups] and model_group_id in ($model_group_ids)"));
		foreach ($data['models'] as $k=>$v)
		{
			if ($v['model_group_id']>0)
			{
				foreach ($model_groups as $model_group)
				{
					if ($v['model_group_id']==$model_group['model_group_id'])
					{
						$model_group['base_files_url']=$config['content_url_models'].'/groups/'.$model_group['model_group_id'];
						$data['models'][$k]['model_group']=$model_group;
						break;
					}
				}
			}
		}
	}

	if ($data['connected_video_id']>0)
	{
		$connected_video_info=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and video_id=".$data['connected_video_id']));
		if ($connected_video_info['video_id']>0)
		{
			$connected_video_info['time_passed_from_adding']=get_time_passed($connected_video_info['post_date']);
			$connected_video_info['duration_array']=get_duration_splitted($connected_video_info['duration']);
			$connected_video_info['formats']=get_video_formats($connected_video_info['video_id'],$connected_video_info['file_formats']);
			$connected_video_info['dir_path']=get_dir_by_id($connected_video_info['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$connected_video_info['screen_url']=$screen_url_base.'/'.get_dir_by_id($connected_video_info['video_id']).'/'.$connected_video_info['video_id'];

			$pattern=str_replace("%ID%",$connected_video_info['video_id'],str_replace("%DIR%",$connected_video_info['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$connected_video_info['view_page_url']="$config[project_url]/$pattern";
			$data['connected_video']=$connected_video_info;
		}
	}

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where post_type_id=? and $database_selectors[where_posts] and post_date<? and post_id<>? order by post_date desc, post_id desc limit 1",intval($data['post_type_id']),$data['post_date'],intval($data['post_id']));
			$result_previous=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where post_type_id=? and $database_selectors[where_posts] and post_date>? and post_id<>? order by post_date asc, post_id asc limit 1",intval($data['post_type_id']),$data['post_date'],intval($data['post_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==3 && $data['user_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where post_type_id=? and user_id=? and $database_selectors[where_posts] and post_date<? and post_id<>? order by post_date desc, post_id desc limit 1",intval($data['post_type_id']),$data['user_id'],$data['post_date'],intval($data['post_id']));
			$result_previous=sql_pr("SELECT $database_selectors[posts] from $config[tables_prefix]posts where post_type_id=? and user_id=? and $database_selectors[where_posts] and post_date>? and post_id<>? order by post_date asc, post_id asc limit 1",intval($data['post_type_id']),$data['user_id'],$data['post_date'],intval($data['post_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$object_next['base_files_url']=$config['content_url_posts'].'/'.get_dir_by_id($object_next['post_id']).'/'.$object_next['post_id'];
			if (isset($post_types[$object_next['post_type_id']]))
			{
				$pattern=str_replace("%ID%",$object_next['post_id'],str_replace("%DIR%",$object_next['dir'],$post_types[$object_next['post_type_id']]['url_pattern']));
				$object_next['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("next_post",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$object_previous['base_files_url']=$config['content_url_posts'].'/'.get_dir_by_id($object_previous['post_id']).'/'.$object_previous['post_id'];
			if (isset($post_types[$object_previous['post_type_id']]))
			{
				$pattern=str_replace("%ID%",$object_previous['post_id'],str_replace("%DIR%",$object_previous['dir'],$post_types[$object_previous['post_type_id']]['url_pattern']));
				$object_previous['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("previous_post",$object_previous);
		}
	}

	$data['base_files_url']=$config['content_url_posts'].'/'.get_dir_by_id($data['post_id']).'/'.$data['post_id'];
	if (isset($post_types[$data['post_type_id']]))
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['post_id'],str_replace("%DIR%",$data['dir'],$post_types[$data['post_type_id']]['url_pattern']));
	}

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	$smarty->assign("data",$data);
	return '';
}

function post_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_post_dir']]);
	$id=intval($_REQUEST[$block_config['var_post_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('posts_info','post',$id,$dir);
	}

	return "$dir|$id|$current_version";
}

function post_viewCacheControl($block_config)
{
	return "user_nocache";
}

function post_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_post_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_post_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,3]", "is_required"=>0),
	);
}

function post_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingPostView.js?v={$config['project_version']}";
}

function post_viewPreProcess($block_config,$object_id)
{
	global $config;

	if (intval($_REQUEST[$block_config['var_post_id']])>0)
	{
		if ($_REQUEST['no_stats']!='true')
		{
			$stats_str=intval($_REQUEST[$block_config['var_post_id']])."||".date("Y-m-d");
			$fh=fopen("$config[project_path]/admin/data/stats/posts_id.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,$stats_str."\r\n");
			fclose($fh);
		}
	} elseif (trim($_REQUEST[$block_config['var_post_dir']])<>'')
	{
		if ($_REQUEST['no_stats']!='true')
		{
			$stats_str=trim($_REQUEST[$block_config['var_post_dir']])."||".date("Y-m-d");
			$fh=fopen("$config[project_path]/admin/data/stats/posts_dir.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,$stats_str."\r\n");
			fclose($fh);
		}
	}
}

function post_viewAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['post_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$post_id=intval($_REQUEST['post_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]posts where post_id=$post_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_post','block'=>'post_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where post_id=? and ip=?",$post_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'post_view')));
			} else {
				sql_pr("insert into $config[tables_prefix]rating_history set post_id=?, ip=?, added_date=?",$post_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]posts set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where post_id=$post_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_posts_count=ratings_posts_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]posts where post_id=$post_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'post_view')));
		}
	} elseif ($_REQUEST['action']=='flag' && intval($_REQUEST['post_id'])>0)
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$post_id=intval($_REQUEST['post_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]posts where post_id=$post_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_post','block'=>'post_view')));
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=4 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'post_view')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'post_view')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and post_id=? and ip=?",$flag['flag_id'],$post_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'post_view')));
				} else {
					sql_pr("insert into $config[tables_prefix]flags_history set post_id=?, flag_id=?, ip=?, added_date=?",$post_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_posts set votes=votes+1 where post_id=? and flag_id=?",$post_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_posts set votes=1, post_id=?, flag_id=?",$post_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]posts set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where post_id=$post_id");
						if (intval($_SESSION['user_id'])>0)
						{
							sql_pr("update $config[tables_prefix]users set ratings_posts_count=ratings_posts_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
						}
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, post_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$post_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, post_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$post_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select votes from $config[tables_prefix]flags_posts where post_id=$post_id and flag_id=?) as flags from $config[tables_prefix]posts where post_id=$post_id",$flag['flag_id']));
				$result_data['flags']=intval($result_data['flags']);
				if ($flag['is_rating']==1)
				{
					$result_data['rating']=floatval($result_data['rating']);
					$result_data['rating_amount']=intval($result_data['rating_amount']);
				} else
				{
					unset($result_data['rating']);
					unset($result_data['rating_amount']);
				}
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					$result_data['tokens_spend']=intval($flag['tokens_required']);
					$result_data['tokens_available']=intval($_SESSION['tokens_available']);
				}
				async_return_request_status(null,null,$result_data);
			} else {
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'post_view')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'post_view')));
		}
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
