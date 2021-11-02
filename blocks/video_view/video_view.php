<?php
function video_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$formats_videos,$website_ui_data,$database_selectors,$hotlink_data;

	$formats_videos=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_videos.dat"));

	if (isset($block_config['var_video_id']) && intval($_REQUEST[$block_config['var_video_id']])>0)
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_admin] and video_id=?",intval($_REQUEST[$block_config['var_video_id']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_active_deleted] and video_id=?",intval($_REQUEST[$block_config['var_video_id']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==2)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_all] and video_id=?",intval($_REQUEST[$block_config['var_video_id']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_active_disabled_deleted] and video_id=?",intval($_REQUEST[$block_config['var_video_id']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_video_dir']])<>'' && trim($_REQUEST[$block_config['var_video_dir']])<>$data['dir'])
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['video_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_video_dir']])<>'')
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_admin] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_video_dir']]),trim($_REQUEST[$block_config['var_video_dir']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_active_deleted] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_video_dir']]),trim($_REQUEST[$block_config['var_video_dir']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==2)
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_all] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_video_dir']]),trim($_REQUEST[$block_config['var_video_dir']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_active_disabled_deleted] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_video_dir']]),trim($_REQUEST[$block_config['var_video_dir']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	if ($data['load_type_id']==5)
	{
		if (intval($website_ui_data['PSEUDO_VIDEO_BEHAVIOR'])==0)
		{
			if ($data['pseudo_url']<>'')
			{
				return "status_302:$data[pseudo_url]";
			} else {
				return "status_302:$config[project_url]";
			}
		}
	}

	$data['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$data['user_id']));
	if ($data['user']['avatar']!='')
	{
		$data['user']['avatar_url']=$config['content_url_avatars']."/".$data['user']['avatar'];
	}
	$data['username']=$data['user']['display_name'];
	$data['user_avatar']=$data['user']['avatar'];
	if ($_SESSION['user_id']>0)
	{
		if ($_SESSION['user_id']<>$data['user_id'])
		{
			$data['user']['is_subscribed']=0;
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=1",$_SESSION['user_id'],$data['user_id']))>0)
			{
				$data['user']['is_subscribed']=1;
			}

			$data['user']['is_purchased']=0;
			if (video_viewHasPremiumAccessByTokens(0,$data['user_id'],0))
			{
				$data['user']['is_purchased']=1;
			}
		}
		$data['is_favourited']=0;
		$data['favourite_types']=array();
		$data['favourite_playlists']=array();
		$fav_types=mr2array_list(sql_pr("select distinct fav_type from $config[tables_prefix]fav_videos where user_id=? and video_id=?",$_SESSION['user_id'],$data['video_id']));
		if (count($fav_types)>0)
		{
			$data['is_favourited']=1;
			$data['favourite_types']=$fav_types;
			if (in_array(10,$fav_types))
			{
				$data['favourite_playlists']=mr2array_list(sql_pr("select distinct playlist_id from $config[tables_prefix]fav_videos where user_id=? and video_id=?",$_SESSION['user_id'],$data['video_id']));
			}
		}
	}

	if ($data['content_source_id']>0)
	{
		$result=sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=?",$data['content_source_id']);
		if (mr2rows($result)>0)
		{
			$data['content_source']=mr2array_single($result);
			$data['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data['content_source']['content_source_id'];
			$data['content_source_as_string']=$data['content_source']['title'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
			{
				$pattern=str_replace("%ID%",$data['content_source']['content_source_id'],str_replace("%DIR%",$data['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
				$data['content_source']['view_page_url']="$config[project_url]/$pattern";
			}
			if ($data['content_source']['content_source_group_id']>0)
			{
				$result=sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] and content_source_group_id=?",$data['content_source']['content_source_group_id']);
				if (mr2rows($result)>0)
				{
					$data['content_source_group']=mr2array_single($result);
				}
			}
			if ($_SESSION['user_id']>0)
			{
				$data['content_source']['is_subscribed']=0;
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=3",$_SESSION['user_id'],$data['content_source_id']))>0)
				{
					$data['content_source']['is_subscribed']=1;
				}
			}
		}
	}

	if ($data['dvd_id']>0)
	{
		$result=sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=?",$data['dvd_id']);
		if (mr2rows($result)>0)
		{
			$data['dvd']=mr2array_single($result);
			$data['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data['dvd']['dvd_id'];
			$data['dvd_as_string']=$data['dvd']['title'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
			{
				$pattern=str_replace("%ID%",$data['dvd']['dvd_id'],str_replace("%DIR%",$data['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
				$data['dvd']['view_page_url']="$config[project_url]/$pattern";
			}
			if ($data['dvd']['dvd_group_id']>0)
			{
				$result=sql_pr("select $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups] and dvd_group_id=?",$data['dvd']['dvd_group_id']);
				if (mr2rows($result)>0)
				{
					$data['dvd_group']=mr2array_single($result);
					$data['dvd_group']['base_files_url']=$config['content_url_dvds'].'/groups/'.$data['dvd_group']['dvd_group_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
					{
						$pattern=str_replace("%ID%",$data['dvd_group']['dvd_group_id'],str_replace("%DIR%",$data['dvd_group']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
						$data['dvd_group']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if ($_SESSION['user_id']>0)
			{
				$data['dvd']['is_subscribed']=0;
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=5",$_SESSION['user_id'],$data['dvd_id']))>0)
				{
					$data['dvd']['is_subscribed']=1;
				}

				$data['dvd']['is_purchased']=0;
				if (video_viewHasPremiumAccessByTokens(0,0,$data['dvd_id']))
				{
					$data['dvd']['is_purchased']=1;
				}
			}
		}
	}

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data['video_id']));
	$data['flags']=array();
	foreach($flags as $flag)
	{
		$data['flags'][$flag['external_id']]=$flag['votes'];
	}

	if ($data['admin_flag_id']>0)
	{
		$data['admin_flag']=mr2string(sql_pr("select external_id from $config[tables_prefix]flags where flag_id=?",$data['admin_flag_id']));
	}
	unset($data['admin_flag_id']);

	$video_log=view_videoGetVideoLog($block_config);
	if (is_array($video_log))
	{
		if (count($video_log['video_log'])>=$video_log['videos_amount'])
		{
			$storage[$object_id]['is_limit_over']=1;
			$smarty->assign("is_limit_over",1);
		}
		$storage[$object_id]['video_log_videos_amount']=count($video_log['video_log']);
		$smarty->assign("video_log_videos_amount",count($video_log['video_log']));
	}

	$screen_url_base=load_balance_screenshots_url();

	$data['file_dimensions']=explode("x",$data['file_dimensions']);
	$data['time_passed_from_adding']=get_time_passed($data['post_date']);
	$data['duration_array']=get_duration_splitted($data['duration']);
	$data['screen_url']=$screen_url_base.'/'.get_dir_by_id($data['video_id']).'/'.$data['video_id'];

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=$data[video_id] order by id asc"));
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
	if (count($data['categories'])>0)
	{
		$data['category']=$data['categories'][0];
		$data['category_id']=$data['category']['category_id'];
	}

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=$data[video_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=$data[video_id] order by id asc"));
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

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and $database_selectors[generic_post_date_selector]<? and video_id<>? order by $database_selectors[generic_post_date_selector] desc, video_id desc limit 1",$data['post_date'],intval($data['video_id']));
			$result_previous=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and $database_selectors[generic_post_date_selector]>? and video_id<>? order by $database_selectors[generic_post_date_selector] asc, video_id asc limit 1",$data['post_date'],intval($data['video_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==1 && $data['dvd_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and dvd_id=? and $database_selectors[generic_post_date_selector]<? and video_id<>? order by $database_selectors[generic_post_date_selector] desc, video_id desc limit 1",$data['dvd_id'],$data['post_date'],intval($data['video_id']));
			$result_previous=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and dvd_id=? and $database_selectors[generic_post_date_selector]>? and video_id<>? order by $database_selectors[generic_post_date_selector] asc, video_id asc limit 1",$data['dvd_id'],$data['post_date'],intval($data['video_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==2 && $data['content_source_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and content_source_id=? and $database_selectors[generic_post_date_selector]<? and video_id<>? order by $database_selectors[generic_post_date_selector] desc, video_id desc limit 1",$data['content_source_id'],$data['post_date'],intval($data['video_id']));
			$result_previous=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and content_source_id=? and $database_selectors[generic_post_date_selector]>? and video_id<>? order by $database_selectors[generic_post_date_selector] asc, video_id asc limit 1",$data['content_source_id'],$data['post_date'],intval($data['video_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==3)
		{
			$result_next=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=? and $database_selectors[generic_post_date_selector]<? and video_id<>? order by $database_selectors[generic_post_date_selector] desc, video_id desc limit 1",$data['user_id'],$data['post_date'],intval($data['video_id']));
			$result_previous=sql_pr("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=? and $database_selectors[generic_post_date_selector]>? and video_id<>? order by $database_selectors[generic_post_date_selector] asc, video_id asc limit 1",$data['user_id'],$data['post_date'],intval($data['video_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$pattern=str_replace("%ID%",$object_next['video_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$object_next['view_page_url']="$config[project_url]/$pattern";
			$object_next['screen_url']=$screen_url_base.'/'.get_dir_by_id($object_next['video_id']).'/'.$object_next['video_id'];

			$smarty->assign("next_video",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$pattern=str_replace("%ID%",$object_previous['video_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$object_previous['view_page_url']="$config[project_url]/$pattern";
			$object_previous['screen_url']=$screen_url_base.'/'.get_dir_by_id($object_previous['video_id']).'/'.$object_previous['video_id'];

			$smarty->assign("previous_video",$object_previous);
		}
	}

	$formats=get_video_formats($data['video_id'],$data['file_formats'],$data['server_group_id']);
	$allowed_postfixes_for_view=video_viewGetAllowedPostfixesForCurrentUser($data['video_id'],$data['user_id'],$data['dvd_id']);
	$allowed_postfixes_for_download=video_viewGetAllowedPostfixesForDownload($data['video_id'],$data['user_id'],$data['dvd_id']);
	$download_formats=array();
	$download_formats_order=array();
	$named_formats=array();
	foreach ($formats as $format_rec)
	{
		if (!in_array($format_rec['postfix'],$allowed_postfixes_for_view))
		{
			$format_rec['is_forbidden']=1;
		}
		$bitrate_limit=0;
		foreach ($formats_videos as $format)
		{
			if ($format['postfix']==$format_rec['postfix'])
			{
				$format_rec['timeline_directory']=$format['timeline_directory'];
				$format_rec['title']=$format['title'];
				if ($format['limit_speed_option']==2 || $format['limit_speed_guests_option']==2 || $format['limit_speed_standard_option']==2 || $format['limit_speed_premium_option']==2)
				{
					$bitrate_limit=intval($format_rec['file_size']/1024/$format_rec['duration']*8);
				}
				break;
			}
		}
		$named_formats[$format_rec['postfix']]=$format_rec;
		if (in_array($format_rec['postfix'],$allowed_postfixes_for_download))
		{
			$download_formats[$format_rec['postfix']]=$format_rec;
			$download_formats[$format_rec['postfix']]['file_url'].=(strpos($download_formats[$format_rec['postfix']]['file_url'],'?')===false?'?':'&')."download=true&download_filename=$data[dir]$format_rec[postfix]";
			if ($data['user_id']==$_SESSION['user_id'] || video_viewHasPremiumAccessByTokens(0,$data['user_id'],$data['dvd_id']))
			{
				$download_formats[$format_rec['postfix']]['file_url'].='&ov='.md5($config['cv'].$_SESSION['user_id']);
			}
			if ($bitrate_limit>0)
			{
				$download_formats[$format_rec['postfix']]['file_url'].='&br='.$bitrate_limit;
			}

			foreach ($formats_videos as $format)
			{
				if ($format['postfix']==$format_rec['postfix'])
				{
					$download_formats_order[]=$format['download_order'];
					break;
				}
			}
		}
	}
	array_multisort($download_formats_order,SORT_NUMERIC,SORT_DESC,$download_formats);

	$data['formats']=$named_formats;
	$data['download_formats']=$download_formats;

	$dir_path=get_dir_by_id($data['video_id']);
	$data['dir_path']=$dir_path;

	$data['screenshot_sources'] = [];
	for ($i = 1; $i <= $data['screen_amount']; $i++)
	{
		$data['screenshot_sources'][] = get_video_source_url($data['video_id'], "screenshots/$i.jpg");
	}

	$data['poster_sources'] = [];
	for ($i = 1; $i <= $data['poster_amount']; $i++)
	{
		$data['poster_sources'][] = get_video_source_url($data['video_id'], "posters/$i.jpg");
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if ($data['is_private']==2)
	{
		if (intval($memberzone_data['ENABLE_TOKENS_PREMIUM_VIDEO'])==1)
		{
			if (intval($data['tokens_required'])==0)
			{
				$data['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_PREMIUM_VIDEO']);
			}
			$data['tokens_required_period']=intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']);
		} else {
			$data['tokens_required']=0;
		}
	} else {
		if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_VIDEO'])==1)
		{
			if (intval($data['tokens_required'])==0)
			{
				$data['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_STANDARD_VIDEO']);
			}
			$data['tokens_required_period']=intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']);
		} else {
			$data['tokens_required']=0;
		}
	}

	$data['is_purchased_video']=0;
	if ($_SESSION['user_id']==$data['user_id'] || video_viewHasPremiumAccessByTokens($data['video_id'],$data['user_id'],$data['dvd_id']))
	{
		$data['is_purchased_video']=1;

		$storage[$object_id]['is_limit_over']=0;
		$smarty->assign("is_limit_over",0);
	}

	$data['can_watch']=1;
	if ($data['is_private']==0 && $_SESSION['user_id']<>$data['user_id'])
	{
		$data['can_watch']=0;
		$access_option=intval($memberzone_data['PUBLIC_VIDEOS_ACCESS']);
		if (intval($data['access_level_id'])==1)
		{
			$access_option=0;
		} elseif (intval($data['access_level_id'])==2)
		{
			$access_option=1;
		} elseif (intval($data['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				$data['can_watch_option']="all";
				$data['can_watch']=1;
				break;
			case 1:
				$data['can_watch_option']="members";
				if ($_SESSION['user_id']>0)
				{
					$data['can_watch']=1;
				}
				break;
			case 2:
				$data['can_watch_option']="premium";
				if ($_SESSION['status_id']==3 || $data['is_purchased_video']==1)
				{
					$data['can_watch']=1;
				}
				break;
		}
	}
	if ($data['is_private']==1 && $_SESSION['user_id']<>$data['user_id'])
	{
		$data['can_watch']=0;
		$access_option=intval($memberzone_data['PRIVATE_VIDEOS_ACCESS']);
		if (intval($data['access_level_id'])==1)
		{
			$access_option=3;
		} elseif (intval($data['access_level_id'])==2)
		{
			$access_option=0;
		} elseif (intval($data['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				$data['can_watch_option']="members";
				if ($_SESSION['user_id']>0)
				{
					$data['can_watch']=1;
				}
				break;
			case 1:
				$data['can_watch_option']="friends";
				if ($_SESSION['user_id']>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",$_SESSION['user_id'],$data['user_id'],$_SESSION['user_id'],$data['user_id']))>0)
				{
					$data['can_watch']=1;
				}
				break;
			case 2:
				$data['can_watch_option']="premium";
				if ($_SESSION['status_id']==3 || $data['is_purchased_video']==1)
				{
					$data['can_watch']=1;
				}
				break;
			case 3:
				$data['can_watch_option']="all";
				$data['can_watch']=1;
				break;
		}
	}
	if ($data['is_private']==2 && $_SESSION['user_id']<>$data['user_id'])
	{
		$data['can_watch']=0;
		$access_option=intval($memberzone_data['PREMIUM_VIDEOS_ACCESS']);
		if (intval($data['access_level_id'])==1)
		{
			$access_option=0;
		} elseif (intval($data['access_level_id'])==2)
		{
			$access_option=1;
		} elseif (intval($data['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				$data['can_watch_option']="all";
				$data['can_watch']=1;
				break;
			case 1:
				$data['can_watch_option']="members";
				if ($_SESSION['user_id']>0)
				{
					$data['can_watch']=1;
				}
				break;
			case 2:
				$data['can_watch_option']="premium";
				if ($_SESSION['status_id']==3 || $data['is_purchased_video']==1)
				{
					$data['can_watch']=1;
				}
				break;
		}
	}

	$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['video_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));

	$storage[$object_id]['video_id']=$data['video_id'];
	$storage[$object_id]['dir_path']=$data['dir_path'];
	$storage[$object_id]['dir']=$data['dir'];
	if (isset($database_selectors['locales']))
	{
		foreach ($database_selectors['locales'] as $lang_code)
		{
			$storage[$object_id]["dir_$lang_code"]=$data["dir_$lang_code"];
		}
	}
	$storage[$object_id]['title']=$data['title'];
	if (isset($data['title_default']))
	{
		$storage[$object_id]['title_default']=$data['title_default'];
	}
	$storage[$object_id]['description']=$data['description'];
	$storage[$object_id]['file_dimensions']=$data['file_dimensions'];
	$storage[$object_id]['duration']=$data['duration_array']['minutes'].":".$data['duration_array']['seconds'];
	$storage[$object_id]['duration_minutes']=$data['duration_array']['minutes'];
	$storage[$object_id]['duration_seconds']=$data['duration_array']['seconds'];
	$storage[$object_id]['rating']=$data['rating'];
	$storage[$object_id]['rating_amount']=$data['rating_amount'];
	$storage[$object_id]['video_viewed']=$data['video_viewed'];
	$storage[$object_id]['post_date']=$data['post_date'];
	$storage[$object_id]['tags']=$data['tags'];
	$storage[$object_id]['tags_as_string']=$data['tags_as_string'];
	$storage[$object_id]['models']=$data['models'];
	$storage[$object_id]['models_as_string']=$data['models_as_string'];
	$storage[$object_id]['categories']=$data['categories'];
	$storage[$object_id]['categories_as_string']=$data['categories_as_string'];
	$storage[$object_id]['content_source']=$data['content_source'];
	$storage[$object_id]['content_source_as_string']=$data['content_source_as_string'];
	$storage[$object_id]['content_source_group']=$data['content_source_group'];
	$storage[$object_id]['dvd']=$data['dvd'];
	$storage[$object_id]['dvd_as_string']=$data['dvd_as_string'];
	$storage[$object_id]['dvd_group']=$data['dvd_group'];
	$storage[$object_id]['screen_url']=$data['screen_url'];
	$storage[$object_id]['screen_main']=$data['screen_main'];
	$storage[$object_id]['comments_count']=$data['comments_count'];
	$storage[$object_id]['user_id']=$data['user_id'];
	$storage[$object_id]['username']=$data['username'];
	$storage[$object_id]['user_avatar']=$data['user_avatar'];
	$storage[$object_id]['release_year']=$data['release_year'];
	$storage[$object_id]['status_id']=$data['status_id'];
	$storage[$object_id]['is_private']=$data['is_private'];
	$storage[$object_id]['is_hd']=$data['is_hd'];
	$storage[$object_id]['custom1']=$data['custom1'];
	$storage[$object_id]['custom2']=$data['custom2'];
	$storage[$object_id]['custom3']=$data['custom3'];
	$storage[$object_id]['canonical_url']=$data['canonical_url'];
	$storage[$object_id]['admin_flag']=$data['admin_flag'];

	$hotlink_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat"));

	$flash_vars=video_viewGetFlashVars($data,0);
	$flash_vars_embed=video_viewGetFlashVars($data,1,$block_config['embed_player_profile_id']);
	if ($flash_vars_embed['video_url']=='')
	{
		$flash_vars['embed']='0';
		$flash_vars_embed['embed']='0';
	} else {
		if (!isset($flash_vars['embed']))
		{
			$flash_vars['embed']='1';
		}
		if (!isset($flash_vars_embed['embed']))
		{
			$flash_vars_embed['embed']='1';
		}
	}

	if ($flash_vars['video_url']!='')
	{
		$storage[$object_id]['video_url']=$flash_vars['video_url'];
	}
	if ($flash_vars['preview_url']!='')
	{
		$storage[$object_id]['preview_url']=$flash_vars['preview_url'];
	}

	$player_size = [$flash_vars['player_width'], $flash_vars['player_height']];
	$player_size_embed = [$flash_vars_embed['player_width'], $flash_vars_embed['player_height']];
	if ($data['load_type_id']==3)
	{
		$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/config.dat"));
		if (intval($player_data['adjust_embed_codes'])==1)
		{
			unset($temp);
			preg_match("|width\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is",$data['embed'],$temp);
			$embed_width=trim($temp[1]);

			unset($temp);
			preg_match("|height\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is",$data['embed'],$temp);
			$embed_height=trim($temp[1]);

			if (strpos($embed_width,'%')===false && strpos($embed_height,'%')===false)
			{
				$embed_width=intval($embed_width);
				$embed_height=intval($embed_height);
				if ($embed_width>0 && $embed_height>0)
				{
					if (intval($player_data['height_option'])==0)
					{
						if ($embed_width!=$player_size[0])
						{
							$aspect_ratio=$embed_width/$embed_height;
							$embed_width=$player_size[0];
							$embed_height=round($embed_width/$aspect_ratio);
						}
					} else {
						$embed_width=intval($player_size[0]);
						$embed_height=intval($player_size[1]);
					}
					$data['embed']=preg_replace("|width\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "width=\"$embed_width\"",$data['embed']);
					$data['embed']=preg_replace("|height\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "height=\"$embed_height\"",$data['embed']);
				}
			} else {
				$data['embed']=preg_replace("|width\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "width=\"$player_size[0]\"",$data['embed']);
				$data['embed']=preg_replace("|height\ *=\ *['\"]?\ *([0-9]+%?)\ *['\"]?|is", "height=\"$player_size[1]\"",$data['embed']);
			}
		}
	}

	if (isset($data['embed']))
	{
		$storage[$object_id]['embed']=$data['embed'];
	}

	$smarty->assign("data",$data);
	$smarty->assign("session_name",session_name());
	$smarty->assign("flashvars",$flash_vars);
	$smarty->assign("flashvars_embed",$flash_vars_embed);
	$smarty->assign("player_size",$player_size);
	$smarty->assign("player_size_embed",$player_size_embed);
	$smarty->assign("embed_mode",$flash_vars_embed['embed_mode']);

	if (isset($block_config['show_stats']))
	{
		$smarty->assign("stats",mr2array(sql_pr("select added_date, viewed, unique_viewed from $config[tables_prefix]stats_videos where video_id=? order by added_date asc",$data['video_id'])));
	}

	if ($data['status_id']==2 || $data['status_id']==3)
	{
		$smarty->caching=0;
		return 'nocache';
	}

	return '';
}

function video_viewGetHash($block_config)
{
	global $config;

	$dir=trim($_REQUEST[$block_config['var_video_dir']]);
	$id=intval($_REQUEST[$block_config['var_video_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$is_limit_over=0;
	$video_log=view_videoGetVideoLog($block_config);
	if (is_array($video_log))
	{
		if (count($video_log['video_log'])>=$video_log['videos_amount'])
		{
			$is_limit_over=1;
		}
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('videos_info','video',$id,$dir);
	}

	if (isset($block_config['embed_player_profile_id']))
	{
		$player_settings_version = @trim(file_get_contents("$config[project_path]/admin/data/player/embed/version.dat"));
	} else
	{
		$player_settings_version = @trim(file_get_contents("$config[project_path]/admin/data/player/version.dat"));
	}

	return "$dir|$id|$is_limit_over|$current_version|$player_settings_version";
}

function video_viewCacheControl($block_config)
{
	return "user_nocache";
}

function video_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_video_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_video_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,1,2,3]", "is_required"=>0),
		array("name"=>"show_stats",                  "group"=>"additional_data", "type"=>"",                "is_required"=>0),

		// limit views
		array("name"=>"limit_unknown_user",   "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
		array("name"=>"limit_member",         "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
		array("name"=>"limit_premium_member", "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
	);
}

function video_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingVideoView.js?v={$config['project_version']}";
}

function video_viewPreProcess($block_config,$object_id)
{
	global $config, $database_selectors;

	video_viewProcessRotatorParams($block_config);

	$video_id=0;
	$video_dir='';
	if (intval($_REQUEST[$block_config['var_video_id']])>0)
	{
		if ($_REQUEST['no_stats']!='true')
		{
			file_put_contents("$config[project_path]/admin/data/stats/videos_id.dat", intval($_REQUEST[$block_config['var_video_id']])."||".intval($_SESSION['user_id'])."||0||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||0\r\n", LOCK_EX | FILE_APPEND);
		}
		$video_id=intval($_REQUEST[$block_config['var_video_id']]);
	} elseif (trim($_REQUEST[$block_config['var_video_dir']])<>'')
	{
		if ($_REQUEST['no_stats']!='true')
		{
			file_put_contents("$config[project_path]/admin/data/stats/videos_dir.dat", trim($_REQUEST[$block_config['var_video_dir']])."||".intval($_SESSION['user_id'])."||0||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||0\r\n", LOCK_EX | FILE_APPEND);
		}
		$video_dir=trim($_REQUEST[$block_config['var_video_dir']]);
	}

	$video_log = view_videoGetVideoLog($block_config);
	if (is_array($video_log))
	{
		if (count($video_log['video_log']) < $video_log['videos_amount'])
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$now_date = date("Y-m-d H:i:s");
			if ($video_id > 0)
			{
				sql_pr("insert into $config[tables_prefix]videos_visits set ip=?, video_id=?, flag=1, added_date='$now_date'", ip2int($_SERVER['REMOTE_ADDR']), $video_id);
			} elseif ($video_dir != '')
			{
				sql_pr("insert into $config[tables_prefix]videos_visits set ip=?, video_id=(select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])), flag=1, added_date='$now_date'", ip2int($_SERVER['REMOTE_ADDR']), $video_dir, $video_dir);
			}
		}
	}
}

function video_viewAsync($block_config)
{
	global $config,$database_selectors,$stats_params;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['video_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'video_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where video_id=? and ip=?",$video_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'video_view')));
			} else {
				$now_date=date("Y-m-d");
				sql_pr("insert into $config[tables_prefix]rating_history set video_id=?, ip=?, added_date=?",$video_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]videos set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where video_id=$video_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_videos_count=ratings_videos_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
				if (intval($stats_settings['collect_videos_stats'])==1)
				{
					if (sql_update("update $config[tables_prefix]stats_videos set rating=rating+$rating, rating_amount=rating_amount+1 where video_id=$video_id and added_date='$now_date'")==0)
					{
						sql("insert into $config[tables_prefix]stats_videos set rating=$rating, rating_amount=1, video_id=$video_id, added_date='$now_date'");
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]videos where video_id=$video_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='flag' && intval($_REQUEST['video_id'])>0)
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'video_view')));
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_view')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'video_view')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and video_id=? and ip=?",$flag['flag_id'],$video_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'video_view')));
				} else {
					$now_date=date("Y-m-d");
					sql_pr("insert into $config[tables_prefix]flags_history set video_id=?, flag_id=?, ip=?, added_date=?",$video_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_videos set votes=votes+1 where video_id=? and flag_id=?",$video_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_videos set votes=1, video_id=?, flag_id=?",$video_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]videos set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where video_id=$video_id");
						if (intval($_SESSION['user_id'])>0)
						{
							sql_pr("update $config[tables_prefix]users set ratings_videos_count=ratings_videos_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
						}

						$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
						if (intval($stats_settings['collect_videos_stats'])==1)
						{
							if (sql_update("update $config[tables_prefix]stats_videos set rating=rating+$rating, rating_amount=rating_amount+1 where video_id=$video_id and added_date='$now_date'")==0)
							{
								sql("insert into $config[tables_prefix]stats_videos set rating=$rating, rating_amount=1, video_id=$video_id, added_date='$now_date'");
							}
						}
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, video_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$video_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, video_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$video_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select votes from $config[tables_prefix]flags_videos where video_id=$video_id and flag_id=?) as flags from $config[tables_prefix]videos where video_id=$video_id",$flag['flag_id']));
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
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'video_view')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='create_playlist')
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$user_id=intval($_SESSION['user_id']);
			$title=trim($_REQUEST['playlist_title']);

			if ($title=='')
			{
				async_return_request_status(array(array('error_field_name'=>'playlist_title','error_code'=>'required','block'=>'video_view')));
			} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where user_id=? and title=?",$user_id,$title))>0)
			{
				async_return_request_status(array(array('error_field_name'=>'playlist_title','error_code'=>'exists','block'=>'video_view')));
			}

			$title=process_blocked_words($title,true);
			$playlist_id=sql_insert("insert into $config[tables_prefix]playlists set user_id=?, title=?, status_id=1, is_private=1, rating_amount=1, added_date=?, last_content_date=?",$user_id,$title,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));

			$_SESSION['playlists']=mr2array(sql("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=$_SESSION[user_id] order by title asc"));
			$_SESSION['playlists_amount']=count($_SESSION['playlists']);

			async_return_request_status(null,null,array('playlist_id'=>$playlist_id,'playlist_title'=>$title));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='add_to_favourites' && intval($_REQUEST['video_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'video_view')));
			}

			$fav_type=intval($_REQUEST['fav_type']);
			$playlist_id=intval($_REQUEST['playlist_id']);
			if ($playlist_id>0)
			{
				if (mr2number(sql("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id and user_id=$user_id"))==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_playlist','error_field_code'=>'error_1','block'=>'video_view')));
				}
				$fav_type=10;
			}

			if (mr2number(sql("select count(*) from $config[tables_prefix]fav_videos where user_id=$user_id and video_id=$video_id and fav_type=$fav_type and playlist_id=$playlist_id"))==0)
			{
				sql_pr("insert into $config[tables_prefix]fav_videos set video_id=$video_id, user_id=$user_id, fav_type=$fav_type, playlist_id=$playlist_id, added_date=?",date("Y-m-d H:i:s"));
				if ($playlist_id>0)
				{
					sql_pr("update $config[tables_prefix]playlists set last_content_date=? where playlist_id=$playlist_id",date("Y-m-d H:i:s"));
				}
				fav_videos_changed($video_id,$fav_type);
			}

			$result_data=array();
			$result_data['favourites_total']=intval($_SESSION['favourite_videos_amount']);
			if ($playlist_id>0)
			{
				foreach ($_SESSION['playlists'] as $playlist)
				{
					if ($playlist['playlist_id']==$playlist_id)
					{
						$result_data['favourites_playlist']=intval($playlist['total_videos']);
						break;
					}
				}
			} else
			{
				$result_data['favourites_type']=intval($_SESSION['favourite_videos_summary'][$fav_type]['amount']);
			}
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='delete_from_favourites' && intval($_REQUEST['video_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'video_view')));
			}

			$fav_type=intval($_REQUEST['fav_type']);
			$playlist_id=intval($_REQUEST['playlist_id']);
			if ($playlist_id>0)
			{
				if (mr2number(sql("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id and user_id=$user_id and is_locked=0"))==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'video_view')));
				}
				$fav_type=10;
			}

			sql("delete from $config[tables_prefix]fav_videos where video_id=$video_id and user_id=$user_id and fav_type=$fav_type and playlist_id=$playlist_id");
			fav_videos_changed($video_id,$fav_type);

			$result_data=array();
			$result_data['favourites_total']=intval($_SESSION['favourite_videos_amount']);
			if ($playlist_id>0)
			{
				foreach ($_SESSION['playlists'] as $playlist)
				{
					if ($playlist['playlist_id']==$playlist_id)
					{
						$result_data['favourites_playlist']=intval($playlist['total_videos']);
						break;
					}
				}
			} else
			{
				$result_data['favourites_type']=intval($_SESSION['favourite_videos_summary'][$fav_type]['amount']);
			}
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='purchase_video' && intval($_REQUEST['video_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$video_id=intval($_REQUEST['video_id']);

			$video=mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=?",$video_id));
			if (intval($video['video_id'])==0 || intval($video['user_id'])==$user_id)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','error_field_code'=>'error_1','block'=>'video_view')));
			}
			$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if ($video['is_private']==2)
			{
				if (intval($memberzone_data['ENABLE_TOKENS_PREMIUM_VIDEO'])==1)
				{
					if (intval($video['tokens_required'])==0)
					{
						$video['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_PREMIUM_VIDEO']);
					}
				} else {
					$video['tokens_required']=0;
				}
			} else {
				if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_VIDEO'])==1)
				{
					if (intval($video['tokens_required'])==0)
					{
						$video['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_STANDARD_VIDEO']);
					}
				} else {
					$video['tokens_required']=0;
				}
			}
			$tokens=intval($video['tokens_required']);
			if ($tokens==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','error_field_code'=>'error_1','block'=>'video_view')));
			}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where user_id=$user_id and video_id=$video_id and expiry_date>?",date("Y-m-d H:i:s")))==0)
			{
				$tokens_available=mr2number(sql("select tokens_available from $config[tables_prefix]users where user_id=$user_id"));
				if ($tokens_available<$tokens)
				{
					async_return_request_status(array(array('error_code'=>'not_enough_tokens','error_field_code'=>'error_2','block'=>'video_view')));
				}

				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				$expiry_period=intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']);
				$added_date=date("Y-m-d H:i:s");
				$expiry_date="2070-01-01 00:00:00";
				if ($expiry_period>0)
				{
					$expiry_date=date("Y-m-d H:i:s",time()+$expiry_period*86400);
				}

				$assign_tokens=0;
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_VIDEOS'])==1)
				{
					$assign_tokens=$tokens-ceil($tokens*min(100,intval($memberzone_data['TOKENS_SALE_INTEREST']))/100);

					$exclude_users=array_map('trim',explode(",",$memberzone_data['TOKENS_SALE_EXCLUDES']));
					$username=mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?",$video['user_id']));
					if ($username && in_array($username,$exclude_users))
					{
						$assign_tokens=0;
					}

					if ($assign_tokens>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$assign_tokens,$video['user_id']);
						sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=6, user_id=?, video_id=?, tokens_granted=?, added_date=?",$video['user_id'],$video['video_id'],$assign_tokens,date("Y-m-d H:i:s"));
					} else {
						$assign_tokens=0;
					}
				}
				$tokens_revenue=$tokens-$assign_tokens;

				sql_pr("insert into $config[tables_prefix]users_purchases set video_id=$video_id, user_id=$user_id, owner_user_id=$video[user_id], tokens=?, tokens_revenue=?, added_date=?, expiry_date=?",$tokens,$tokens_revenue,$added_date,$expiry_date);
				sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens, 0) where user_id=$user_id");
				sql_pr("update $config[tables_prefix]videos set purchases_count=(select count(*) from $config[tables_prefix]users_purchases where $config[tables_prefix]users_purchases.video_id=$config[tables_prefix]videos.video_id) where video_id=$video_id");

				$_SESSION['tokens_available']=mr2number(sql("select tokens_available from $config[tables_prefix]users where user_id=$user_id"));
				$_SESSION['content_purchased'][]=array('video_id'=>$video_id);
				$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);
			}

			$result_data=array();
			$result_data['tokens_spend']=intval($tokens);
			$result_data['tokens_available']=intval($_SESSION['tokens_available']);
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_view')));
		}
	} elseif ($_REQUEST['action']=='redirect_cs' && $_REQUEST['id']>0)
	{
		// left for backward compatibility
		require_once("$config[project_path]/redirect_cs.php");
		die;
	} elseif ($_REQUEST['action']=='js_stats_view_video')
	{
		$stats_referer_host='';
		if ($_SERVER['HTTP_REFERER']!='')
		{
			$stats_referer_host=trim(parse_url($_SERVER['HTTP_REFERER'],PHP_URL_HOST));
			if ($stats_referer_host)
			{
				$stats_referer_host=str_replace('www.','',$stats_referer_host);
			}
		}
		if ($_REQUEST['no_stats']!='true' && intval($stats_params['collect_traffic_stats'])==1 && ($stats_referer_host=='' || $stats_referer_host==str_replace('www.','',$_SERVER['HTTP_HOST'])))
		{
			$device_type = 0;
			if (intval($stats_params['collect_traffic_stats_devices']) == 1)
			{
				require_once("$config[project_path]/admin/include/functions_base.php");
				$device_type = get_device_type();
			}

			if (intval($_REQUEST[$block_config['var_video_id']])>0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/videos_id.dat", intval($_REQUEST[$block_config['var_video_id']])."||".intval($_SESSION['user_id'])."||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			} elseif (trim($_REQUEST[$block_config['var_video_dir']])<>'')
			{
				file_put_contents("$config[project_path]/admin/data/stats/videos_dir.dat", trim($_REQUEST[$block_config['var_video_dir']])."||".intval($_SESSION['user_id'])."||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}
		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	}
}

function view_videoGetVideoLog($block_config)
{
	global $config;

	if (isset($block_config['limit_premium_member']) || isset($block_config['limit_member']) || isset($block_config['limit_unknown_user']))
	{
		if ($_SESSION['status_id'] == 3)
		{
			$temp = explode("/", $block_config['limit_premium_member']);
		} elseif ($_SESSION['user_id'] > 0)
		{
			$temp = explode("/", $block_config['limit_member']);
		} else
		{
			$temp = explode("/", $block_config['limit_unknown_user']);
		}

		$videos_amount = intval($temp[0]);
		$time = intval($temp[1]);

		if ($videos_amount > 0 && $time > 0)
		{
			if (isset($config['context']['video_view_log']) && is_array($config['context']['video_view_log']))
			{
				$video_log = $config['context']['video_view_log'];
			} else
			{
				require_once("$config[project_path]/admin/include/functions_base.php");
				require_once("$config[project_path]/admin/include/functions.php");

				$video_log = mr2array_list(sql_pr("select video_id from $config[tables_prefix]videos_visits where ip=? and flag=1 and added_date>?", ip2int($_SERVER['REMOTE_ADDR']), date("Y-m-d H:i:s", time() - $time)));
				$config['context']['video_view_log'] = $video_log;
			}
			return array("video_log" => $video_log, "videos_amount" => $videos_amount);
		}
	}

	return false;
}

function video_viewGetFlashVars($video_data,$is_embed,$embed_player_profile_id='')
{
	global $config,$website_ui_data,$formats_videos,$hotlink_data,$stats_params;

	$flash_vars=array();
	if ($is_embed==1)
	{
		if ($embed_player_profile_id!='' && is_file("$config[project_path]/admin/data/player/embed/$embed_player_profile_id/config.dat"))
		{
			$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/$embed_player_profile_id/config.dat"));
			$player_url="$config[content_url_other]/player/embed/$embed_player_profile_id";
		} else {
			$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/config.dat"));
			$player_url="$config[content_url_other]/player/embed";
		}
		$flash_vars['embed_mode']=1;
	} else {
		if ($_SESSION['status_id']==3 || $video_data['user_id']==$_SESSION['user_id'] || video_viewHasPremiumAccessByTokens($video_data['video_id'],$video_data['user_id'],$video_data['dvd_id']))
		{
			$player_dir="$config[project_path]/admin/data/player/premium";
			$player_url="$config[content_url_other]/player/premium";
		} elseif ($_SESSION['user_id']>0)
		{
			$player_dir="$config[project_path]/admin/data/player/active";
			$player_url="$config[content_url_other]/player/active";
		} else {
			$player_dir="$config[project_path]/admin/data/player";
			$player_url="$config[content_url_other]/player";
		}
		if (is_file("$player_dir/config.dat"))
		{
			$player_data=@unserialize(file_get_contents("$player_dir/config.dat"));
		} else {
			$player_data=@unserialize(file_get_contents("$config[project_path]/admin/data/player/config.dat"));
			$player_url="$config[content_url_other]/player";
		}
	}

	$video_id=$video_data['video_id'];
	$dir_path=get_dir_by_id($video_id);

	$category_ids='';
	foreach ($video_data['categories'] as $category)
	{
		$category_ids.="$category[category_id],";
	}
	$category_ids=trim($category_ids,',');

	$flash_vars['video_id']=$video_id;
	$flash_vars['license_code']=$config['player_license_code'];
	if ($config['player_lrc']<>'')
	{
		$flash_vars['lrc']=$config['player_lrc'];
	}

	if (intval($stats_params['collect_player_stats']) == 1)
	{
		if (intval($stats_params['player_stats_reporting']) == 0 || intval($stats_params['player_stats_reporting']) == 2)
		{
			$flash_vars['event_reporting'] = "$config[project_url]/player/stats.php?embed=$is_embed";
			if (intval($stats_params['collect_player_stats_embed_profiles']) == 1 && $is_embed == 1)
			{
				$flash_vars['event_reporting'] .= "&embed_profile_id=$embed_player_profile_id";
			}
			if (intval($stats_params['collect_player_stats_devices']) == 1)
			{
				require_once("$config[project_path]/admin/include/functions_base.php");
				$device_type = get_device_type();
				$flash_vars['event_reporting'] .= "&device_type=$device_type";
			}
		}
		if (intval($stats_params['player_stats_reporting']) == 1 || intval($stats_params['player_stats_reporting']) == 2)
		{
			$flash_vars['reporting'] = "true";
		}
	}
	if (intval($stats_params['collect_videos_stats_video_plays']) == 1)
	{
		$flash_vars['play_reporting'] = "$config[project_url]/player/stats.php?event=FirstPlay&video_id=$video_data[video_id]";
		if (intval($stats_params['collect_player_stats_devices']) == 1)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			$device_type = get_device_type();
			$flash_vars['play_reporting'] .= "&device_type=$device_type";
		}
	}

	if (intval($player_data['error_logging']) == 1)
	{
		$flash_vars['error_reporting'] = "$config[project_url]/player/error.php";
	}

	$slots=array();
	if (is_array($player_data['slots']))
	{
		if ($video_data['is_private']==0 || $video_data['is_private']==1)
		{
			$slots=$player_data['slots'][0];
		} elseif ($video_data['is_private']==2)
		{
			$slots=$player_data['slots'][1];
		}
	}

	$formats=get_video_formats($video_data['video_id'],$video_data['file_formats']);
	if (count($formats)==0)
	{
		$slots=array();
	}

	unset($display_slot);
	if (count($slots)>0)
	{
		$allowed_postfixes=video_viewGetAllowedPostfixesForCurrentUser($video_data['video_id'],$video_data['user_id'],$video_data['dvd_id']);
		foreach ($slots as $k=>$slot)
		{
			if ($slot['type']=='redirect')
			{
				if ($player_data['format_redirect_url_source']==1)
				{
					$slots[$k]['redirect_url']=process_url($player_data['format_redirect_url']);
				} elseif ($player_data['format_redirect_url_source']==2)
				{
					$pattern=str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
					$slots[$k]['redirect_url']=video_viewAddAffiliateParam("$config[project_url]/$pattern",$player_data);
				} elseif ($video_data['content_source_id']>0)
				{
					$slots[$k]['redirect_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
				} else {
					$slots[$k]['redirect_url']=process_url($player_data['format_redirect_url']);
				}
				continue;
			}
			if ($slot['is_default']==1 && isset($formats[$slot['type']]))
			{
				$default_slot=$slot;
			}
			if (!in_array($slot['type'],$allowed_postfixes))
			{
				if (!isset($formats[$slot['type']]))
				{
					unset($slots[$k]);
				} else {
					if ($formats[$slot['type']]['dimensions'][1]>=700)
					{
						$slots[$k]['is_hd']=1;
					}
					$slots[$k]['type']='redirect';
					if ($player_data['format_redirect_url_source']==1)
					{
						$slots[$k]['redirect_url']=process_url($player_data['format_redirect_url']);
					} elseif ($player_data['format_redirect_url_source']==2)
					{
						$pattern=str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
						$slots[$k]['redirect_url']=video_viewAddAffiliateParam("$config[project_url]/$pattern",$player_data);
					} elseif ($video_data['content_source_id']>0)
					{
						$slots[$k]['redirect_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
					} else {
						$slots[$k]['redirect_url']=process_url($player_data['format_redirect_url']);
					}
				}
				continue;
			}
			foreach ($formats as $format_rec)
			{
				if ($format_rec['postfix']==$slot['type'])
				{
					$hash=md5($config['cv']."$dir_path/$video_id/$video_id{$format_rec['postfix']}");
					if ($hotlink_data['ANTI_HOTLINK_ENCODE_LINKS']==1 && strpos($config['project_url'],'/',10)===false)
					{
						for ($i=0;$i<strlen($hash);$i++)
						{
							$new_pos=$i;
							for ($j=$i;$j<strlen($config['ahv']);$j++)
							{
								$val=intval($config['ahv'][$j]);
								$new_pos+=$val;
							}
							while ($new_pos>=strlen($hash))
							{
								$new_pos=$new_pos-strlen($hash);
							}
							$t=$hash[$i];
							$hash[$i]=$hash[$new_pos];
							$hash[$new_pos]=$t;
						}
					}

					$bitrate_limit=0;
					foreach ($formats_videos as $temp)
					{
						if ($temp['postfix']==$format_rec['postfix'])
						{
							if ($temp['limit_speed_option']==2 || $temp['limit_speed_guests_option']==2 || $temp['limit_speed_standard_option']==2 || $temp['limit_speed_premium_option']==2 || ($is_embed==1 && $temp['limit_speed_embed_option']==2))
							{
								$bitrate_limit=intval($format_rec['file_size']/1024/$format_rec['duration']*8);
							}
							break;
						}
					}
					$slots[$k]['video_url']="$config[project_url]/get_file/$video_data[server_group_id]/$hash/$dir_path/$video_id/$video_id{$format_rec['postfix']}/".($bitrate_limit>0?"?br=$bitrate_limit":'');
					if ($video_data['user_id']==$_SESSION['user_id'] || video_viewHasPremiumAccessByTokens(0,$video_data['user_id'],$video_data['dvd_id']))
					{
						$slots[$k]['video_url'].=(strpos($slots[$k]['video_url'],'?')===false?'?':'&').'ov='.md5($config['cv'].$_SESSION['user_id']);
					}
					if ($is_embed==1)
					{
						$slots[$k]['video_url'].=(strpos($slots[$k]['video_url'],'?')===false?'?':'&').'embed=true';
					}
					if ($hotlink_data['ANTI_HOTLINK_ENCODE_LINKS']==1 && strpos($config['project_url'],'/',10)===false)
					{
						$slots[$k]['video_url']="function/0/".$slots[$k]['video_url'];
					}
					if ($format_rec['dimensions'][1]>=700)
					{
						$slots[$k]['is_hd']=1;
					}
					if (!isset($display_slot))
					{
						$display_slot=$slots[$k];
						$display_slot['preview_format']=$slot['type'];
					}
					break;
				}
			}
			if (!isset($slots[$k]['video_url']))
			{
				unset($slots[$k]);
			}
		}
		$flash_vars['rnd']=time();
	}

	if ($video_data['load_type_id']==2)
	{
		$flash_vars['video_url']=$video_data['file_url'];
	} elseif ($video_data['load_type_id']==1 && isset($display_slot)) {
		$flash_vars['video_url']=$display_slot['video_url'];
		if ($display_slot['is_hd']==1)
		{
			$flash_vars['video_url_hd']=1;
		}
		$flash_vars['postfix']=$display_slot['type'];
		if (count($slots)>1)
		{
			$i=1;
			foreach ($slots as $slot)
			{
				if ($slot['type']=='redirect')
				{
					if ($i==1)
					{
						$flash_vars['video_alt_url']=$slot['redirect_url'];
						$flash_vars['video_alt_url_text']=$slot['title'];
						$flash_vars['video_alt_url_redirect']='1';
						if ($slot['is_hd']==1)
						{
							$flash_vars['video_alt_url_hd']=1;
						}
						$i++;
					} elseif ($i>=2)
					{
						$flash_vars["video_alt_url{$i}"]=$slot['redirect_url'];
						$flash_vars["video_alt_url{$i}_text"]=$slot['title'];
						$flash_vars["video_alt_url{$i}_redirect"]='1';
						if ($slot['is_hd']==1)
						{
							$flash_vars["video_alt_url{$i}_hd"]=1;
						}
						$i++;
					}
				} elseif ($slot['type']==$display_slot['type'])
				{
					$flash_vars['video_url_text']=$slot['title'];
				} else {
					if ($i==1)
					{
						$flash_vars['video_alt_url']=$slot['video_url'];
						$flash_vars['video_alt_url_text']=$slot['title'];
						if ($slot['is_hd']==1)
						{
							$flash_vars['video_alt_url_hd']=1;
						}
						$i++;
					} elseif ($i>=2)
					{
						$flash_vars["video_alt_url{$i}"]=$slot['video_url'];
						$flash_vars["video_alt_url{$i}_text"]=$slot['title'];
						if ($slot['is_hd']==1)
						{
							$flash_vars["video_alt_url{$i}_hd"]=1;
						}
						$i++;
					}
					if ($slot['is_default']==1)
					{
						$flash_vars["default_slot"]=$i;
					}
				}
			}
		}
		if ($player_data['timeline_screenshots_size'])
		{
			$display_slot_format_rec=null;
			foreach ($formats as $format_rec)
			{
				if ($format_rec['postfix']==$display_slot['type'])
				{
					$display_slot_format_rec=$format_rec;
					$count=$format_rec['timeline_screen_amount'];
					$interval=$format_rec['timeline_screen_interval'];
					if ($count>0 && $interval>0)
					{
						foreach ($formats_videos as $format_video)
						{
							if ($format_video['postfix']==$format_rec['postfix'])
							{
								$flash_vars['timeline_screens_url']="$video_data[screen_url]/timelines/$format_video[timeline_directory]/$player_data[timeline_screenshots_size]/{time}.jpg";
								if ($player_data['timeline_screenshots_webp_size'])
								{
									$flash_vars['timeline_screens_webp_url']="$video_data[screen_url]/timelines/$format_video[timeline_directory]/$player_data[timeline_screenshots_webp_size]/{time}.jpg";
								}
								$flash_vars['timeline_screens_interval']=$interval;
								$flash_vars['timeline_screens_count']=$format_rec['timeline_screen_amount'];
								if (intval($player_data['timeline_screenshots_cuepoints'])==1 && $format_rec['timeline_cuepoints']>0)
								{
									$flash_vars['cuepoints']="$video_data[screen_url]/timelines/$format_video[timeline_directory]/cuepoints.json";
								}
								break;
							}
						}
					}
					break;
				}
			}
			if ($flash_vars['timeline_screens_url']=='' && isset($display_slot_format_rec))
			{
				foreach ($formats as $format_rec)
				{
					if ($format_rec['duration']==$display_slot_format_rec['duration'])
					{
						$count=$format_rec['timeline_screen_amount'];
						$interval=$format_rec['timeline_screen_interval'];
						if ($count>0 && $interval>0)
						{
							foreach ($formats_videos as $format_video)
							{
								if ($format_video['postfix']==$format_rec['postfix'])
								{
									$flash_vars['timeline_screens_url']="$video_data[screen_url]/timelines/$format_video[timeline_directory]/$player_data[timeline_screenshots_size]/{time}.jpg";
									$flash_vars['timeline_screens_interval']=$interval;
									$flash_vars['timeline_screens_count']=$format_rec['timeline_screen_amount'];
									if (intval($player_data['timeline_screenshots_cuepoints'])==1 && $format_rec['timeline_cuepoints']>0)
									{
										$flash_vars['cuepoints']="$video_data[screen_url]/timelines/$format_video[timeline_directory]/cuepoints.json";
									}
									break;
								}
							}
							break;
						}
					}
				}
			}
		}
	}
	if (intval($player_data['use_uploaded_poster']) == 1 && $video_data['poster_amount'] > 0 && $video_data['poster_main'] > 0)
	{
		$flash_vars['preview_url'] = get_video_source_url($video_data['video_id'], "posters/$video_data[poster_main].jpg");
	} elseif (intval($player_data['use_preview_source']) == 1)
	{
		$flash_vars['preview_url'] = "$video_data[screen_url]/preview.jpg";
	} elseif (isset($default_slot))
	{
		$flash_vars['preview_url'] = "$video_data[screen_url]/preview{$default_slot['type']}.jpg";
	} elseif (isset($display_slot))
	{
		$flash_vars['preview_url'] = "$video_data[screen_url]/preview{$display_slot['preview_format']}.jpg";
	} else
	{
		$flash_vars['preview_url'] = "$video_data[screen_url]/preview.jpg";
	}

	$flash_vars['skin']=$player_data['skin'];

	if ($player_data['logo_source']==0)
	{
		if ($player_data['logo']!='')
		{
			$flash_vars['logo_src']="$player_url/$player_data[logo]";
		}
	} else {
		$logo_file=video_viewGetFromCS($video_data['content_source'],$player_data['logo_source']);
		if ($logo_file!='')
		{
			$flash_vars['logo_src']=$logo_file;
		} elseif ($player_data['logo']!='')
		{
			$flash_vars['logo_src']="$player_url/$player_data[logo]";
		}
	}
	if ($player_data['logo_text_source']==0)
	{
		if ($player_data['logo_text']!='')
		{
			$flash_vars['logo_text']=$player_data['logo_text'];
		}
	} else {
		$cs_field='custom'.intval($player_data['logo_text_source']);
		$logo_text=$video_data['content_source'][$cs_field];
		if ($logo_text!='')
		{
			$flash_vars['logo_text']=preg_replace("/\s+/"," ",$logo_text);
		} elseif ($player_data['logo_text']!='')
		{
			$flash_vars['logo_text']=$player_data['logo_text'];
		}
	}

	$flash_vars['logo_position']=$player_data['logo_position_x'].",".$player_data['logo_position_y'];
	$flash_vars['logo_anchor']=$player_data['logo_anchor'];
	if ($player_data['logo_hide']==1)
	{
		$flash_vars['logo_hide']='true';
	}

	if ($flash_vars['logo_src']!='' || $flash_vars['logo_text']!='')
	{
		if ($player_data['logo_url_source']==1)
		{
			if ($player_data['logo_url'])
			{
				$flash_vars['logo_url']=process_url($player_data['logo_url']);
			}
		} elseif ($player_data['logo_url_source']==2)
		{
			$pattern=str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$flash_vars['logo_url']=video_viewAddAffiliateParam("$config[project_url]/$pattern",$player_data);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars['logo_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars['logo_url']=process_url($player_data['logo_url']);
		}
	}
	if ($player_data['enable_video_click']==1)
	{
		if ($player_data['video_click_url_source']==1)
		{
			$flash_vars['video_click_url']=process_url($player_data['video_click_url']);
		} elseif ($player_data['video_click_url_source']==2)
		{
			$pattern=str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$flash_vars['video_click_url']=video_viewAddAffiliateParam("$config[project_url]/$pattern",$player_data);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars['video_click_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars['video_click_url']=process_url($player_data['video_click_url']);
		}
	}
	if ($player_data['enable_popunder']==1)
	{
		if ($player_data['popunder_url_source']==1)
		{
			$flash_vars['popunder_url']=process_url($player_data['popunder_url']);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars['popunder_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars['popunder_url']=process_url($player_data['popunder_url']);
		}
		$flash_vars['popunder_duration']=intval($player_data['popunder_duration']) * 60;
		if (intval($player_data['popunder_autoplay_only'])==1)
		{
			$flash_vars['popunder_autoplay_only']='true';
		}
	}
	if ($player_data['volume']!='')
	{
		$flash_vars['volume']=$player_data['volume'];
	}
	if ($player_data['preload_metadata']==1)
	{
		$flash_vars['preload']='metadata';
	} elseif ($player_data['preload_metadata']==2)
	{
		$flash_vars['preload']='auto';
	}
	$flash_vars['hide_controlbar']=$player_data['controlbar'];
	if ($player_data['controlbar_hide_style']==1)
	{
		$flash_vars['hide_style']='fade';
	}
	if ($player_data['controlbar_ad_text']<>'')
	{
		$flash_vars['mlogo']=$player_data['controlbar_ad_text'];
	}
	if ($player_data['controlbar_ad_url_source']==1)
	{
		if ($player_data['controlbar_ad_url']<>'')
		{
			$flash_vars['mlogo_link']=process_url($player_data['controlbar_ad_url']);
		}
	} elseif ($player_data['controlbar_ad_url_source']==2)
	{
		$pattern=str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
		$flash_vars['mlogo_link']=video_viewAddAffiliateParam("$config[project_url]/$pattern",$player_data);
	} elseif ($video_data['content_source_id']>0)
	{
		$flash_vars['mlogo_link']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
	} else {
		$flash_vars['mlogo_link']=process_url($player_data['controlbar_ad_url']);
	}
	if ($player_data['enable_stream']==0)
	{
		$flash_vars['flv_stream']='false';
	}
	if ($player_data['enable_autoplay']==1)
	{
		$flash_vars['autoplay']='true';
	}
	if ($player_data['enable_urls_in_same_window']==1)
	{
		$flash_vars['urls_in_same_window']='true';
	}
	if ($player_data['enable_related_videos']==1)
	{
		$flash_vars['related_src']=$config['project_url']."/related_videos_html/$video_id/";
	}
	if ($player_data['enable_related_videos_on_pause']==1)
	{
		$flash_vars['related_on_pause']='true';
	}
	if ($player_data['disable_selected_slot_restoring']==1)
	{
		$flash_vars['skip_selected_format']="true";
	}
	if ($player_data['show_global_duration']==1)
	{
		$flash_vars['duration']=$video_data['duration'];
	}
	if ($player_data['enable_start_html']==1)
	{
		$flash_vars['adv_start_html']="$config[project_url]/player/html.php?aid=start_html&video_id=$video_id&cs_id=$video_data[content_source_id]&category_ids=$category_ids".($is_embed==1?'&embed=true':'');
		if ($player_data['start_html_adaptive']==1)
		{
			$flash_vars['adv_start_html_adaptive']=intval($player_data['start_html_adaptive_width']).'x'.intval($player_data['start_html_adaptive_height']);
		}
	}
	if ($player_data['enable_pre_roll']==1)
	{
		if ($player_data['pre_roll_duration']>0) {$flash_vars['adv_pre_duration']=$player_data['pre_roll_duration'];}
		if ($player_data['pre_roll_duration_text']!='') {$flash_vars['adv_pre_duration_text']=$player_data['pre_roll_duration_text'];}

		if (intval($player_data['pre_roll_replay_after'])>0)
		{
			$flash_vars['adv_pre_replay_after']=intval($player_data['pre_roll_replay_after']);
			$flash_vars['adv_pre_replay_after_type']=intval($player_data['pre_roll_replay_after_type']);
		}

		if ($player_data['enable_pre_roll_skip']==1)
		{
			$flash_vars['adv_pre_skip_duration']=$player_data['pre_roll_skip_duration'];
			$flash_vars['adv_pre_skip_text_time']=$player_data['pre_roll_skip_text1'];
			$flash_vars['adv_pre_skip_text']=$player_data['pre_roll_skip_text2'];
		}

		if ($player_data['pre_roll_url_source']==1)
		{
			$flash_vars['adv_pre_url']=process_url($player_data['pre_roll_url']);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars['adv_pre_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars['adv_pre_url']=process_url($player_data['pre_roll_url']);
		}
		if ($player_data['pre_roll_file_source']==1)
		{
			$flash_vars['adv_pre_src']="$player_url/$player_data[pre_roll_file]";
		} else {
			$pre_roll_file=video_viewGetFromCS($video_data['content_source'],$player_data['pre_roll_file_source']-1);
			if ($pre_roll_file<>'')
			{
				$flash_vars['adv_pre_src']=$pre_roll_file;
			} elseif ($pre_roll_file=='' && $player_data['pre_roll_file']<>'')
			{
				$flash_vars['adv_pre_src']="$player_url/$player_data[pre_roll_file]";
			}
		}
	}
	if ($player_data['enable_pre_roll_html']==1)
	{
		if ($player_data['pre_roll_duration']>0) {$flash_vars['adv_pre_duration']=$player_data['pre_roll_duration'];}
		if ($player_data['pre_roll_duration_text']!='') {$flash_vars['adv_pre_duration_text']=$player_data['pre_roll_duration_text'];}

		if (intval($player_data['pre_roll_replay_after'])>0)
		{
			$flash_vars['adv_pre_replay_after']=intval($player_data['pre_roll_replay_after']);
			$flash_vars['adv_pre_replay_after_type']=intval($player_data['pre_roll_replay_after_type']);
		}

		if ($player_data['enable_pre_roll_skip']==1)
		{
			$flash_vars['adv_pre_skip_duration']=$player_data['pre_roll_skip_duration'];
			$flash_vars['adv_pre_skip_text_time']=$player_data['pre_roll_skip_text1'];
			$flash_vars['adv_pre_skip_text']=$player_data['pre_roll_skip_text2'];
		}

		$flash_vars['adv_pre_html']="$config[project_url]/player/html.php?aid=pre_roll_html&video_id=$video_id&cs_id=$video_data[content_source_id]&category_ids=$category_ids".($is_embed==1?'&embed=true':'');
		if ($player_data['pre_roll_html_adaptive']==1)
		{
			$flash_vars['adv_pre_html_adaptive']=intval($player_data['pre_roll_html_adaptive_width']).'x'.intval($player_data['pre_roll_html_adaptive_height']);
		}
	}
	if ($player_data['enable_pre_roll_vast']==1)
	{
		$vast_url=$player_data['pre_roll_vast_url'];
		switch (intval($player_data['pre_roll_vast_provider']))
		{
			case 1:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'ref=kernelteam';
				break;
			case 2:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'external_subid=kernelteam';
				break;
			case 3:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'ref=kernelteam';
				break;
		}
		$flash_vars['adv_pre_vast']=$vast_url;
		if (strpos($player_data['pre_roll_vast_provider'],'vast_profile_')!==false)
		{
			$flash_vars['adv_pre_vast']='%KTV:'.substr($player_data['pre_roll_vast_provider'],13).'%';
		}
		if ($player_data['pre_roll_vast_alt_url'])
		{
			$alternate_vasts = [];
			foreach (array_map('trim', explode("\n", $player_data['pre_roll_vast_alt_url'])) as $vast)
			{
				if ($vast)
				{
					$alternate_vasts[] = $vast;
				}
			}
			if (count($alternate_vasts) > 0)
			{
				$flash_vars['adv_pre_vast_alt'] = implode('|', $alternate_vasts);
			}
		}

		if ($player_data['pre_roll_duration']>0) {$flash_vars['adv_pre_duration']=$player_data['pre_roll_duration'];}
		if ($player_data['pre_roll_duration_text']!='') {$flash_vars['adv_pre_duration_text']=$player_data['pre_roll_duration_text'];}

		if (intval($player_data['pre_roll_replay_after'])>0)
		{
			$flash_vars['adv_pre_replay_after']=intval($player_data['pre_roll_replay_after']);
			$flash_vars['adv_pre_replay_after_type']=intval($player_data['pre_roll_replay_after_type']);
		}

		if ($player_data['enable_pre_roll_skip']==1)
		{
			$flash_vars['adv_pre_skip_duration']=$player_data['pre_roll_skip_duration'];
			$flash_vars['adv_pre_skip_text_time']=$player_data['pre_roll_skip_text1'];
			$flash_vars['adv_pre_skip_text']=$player_data['pre_roll_skip_text2'];
		} else
		{
			$flash_vars['adv_pre_skip_text_time']='Skip ad in %time';
			$flash_vars['adv_pre_skip_text']='Skip ad';
		}

		if ($player_data['pre_roll_vast_logo']==1)
		{
			$flash_vars['adv_pre_vast_logo']='true';
			if ($player_data['pre_roll_vast_logo_click']==1)
			{
				$flash_vars['adv_pre_vast_logo_click']='true';
			}
		}
	}
	$post_roll_ad_prefix="adv_post";
	if ($player_data['post_roll_mode']==1)
	{
		$post_roll_ad_prefix="adv_postpause";
	}
	if ($player_data['enable_post_roll']==1)
	{
		if ($player_data['post_roll_duration']>0) {$flash_vars["{$post_roll_ad_prefix}_duration"]=$player_data['post_roll_duration'];}
		if ($player_data['post_roll_duration_text']!='') {$flash_vars["{$post_roll_ad_prefix}_duration_text"]=$player_data['post_roll_duration_text'];}

		if ($player_data['enable_post_roll_skip']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_skip_duration"]=$player_data['post_roll_skip_duration'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text_time"]=$player_data['post_roll_skip_text1'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text"]=$player_data['post_roll_skip_text2'];
		}

		if ($player_data['post_roll_url_source']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_url"]=process_url($player_data['post_roll_url']);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars["{$post_roll_ad_prefix}_url"]="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars["{$post_roll_ad_prefix}_url"]=process_url($player_data['post_roll_url']);
		}
		if ($player_data['post_roll_file_source']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_src"]="$player_url/$player_data[post_roll_file]";
		} else {
			$post_roll_file=video_viewGetFromCS($video_data['content_source'],$player_data['post_roll_file_source']-1);
			if ($post_roll_file<>'')
			{
				$flash_vars["{$post_roll_ad_prefix}_src"]=$post_roll_file;
			} elseif ($post_roll_file=='' && $player_data['post_roll_file']<>'')
			{
				$flash_vars["{$post_roll_ad_prefix}_src"]="$player_url/$player_data[post_roll_file]";
			}
		}
	}
	if ($player_data['enable_post_roll_html']==1)
	{
		if ($player_data['post_roll_duration']>0) {$flash_vars["{$post_roll_ad_prefix}_duration"]=$player_data['post_roll_duration'];}
		if ($player_data['post_roll_duration_text']!='') {$flash_vars["{$post_roll_ad_prefix}_duration_text"]=$player_data['post_roll_duration_text'];}

		if ($player_data['enable_post_roll_skip']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_skip_duration"]=$player_data['post_roll_skip_duration'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text_time"]=$player_data['post_roll_skip_text1'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text"]=$player_data['post_roll_skip_text2'];
		}

		$flash_vars["{$post_roll_ad_prefix}_html"]="$config[project_url]/player/html.php?aid=post_roll_html&video_id=$video_id&cs_id=$video_data[content_source_id]&category_ids=$category_ids".($is_embed==1?'&embed=true':'');
		if ($player_data['post_roll_html_adaptive']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_html_adaptive"]=intval($player_data['post_roll_html_adaptive_width']).'x'.intval($player_data['post_roll_html_adaptive_height']);
		}
	}
	if ($player_data['enable_post_roll_vast']==1)
	{
		$vast_url=$player_data['post_roll_vast_url'];
		switch (intval($player_data['post_roll_vast_provider']))
		{
			case 1:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'ref=kernelteam';
				break;
			case 2:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'external_subid=kernelteam';
				break;
			case 3:
				$vast_url.=(strpos($vast_url,'?')===false?'?':'&').'ref=kernelteam';
				break;
		}
		$flash_vars["{$post_roll_ad_prefix}_vast"]=$vast_url;
		if (strpos($player_data['post_roll_vast_provider'],'vast_profile_')!==false)
		{
			$flash_vars["{$post_roll_ad_prefix}_vast"]='%KTV:'.substr($player_data['post_roll_vast_provider'],13).'%';
		}
		if ($player_data['post_roll_vast_alt_url'])
		{
			$alternate_vasts = [];
			foreach (array_map('trim', explode("\n", $player_data['post_roll_vast_alt_url'])) as $vast)
			{
				if ($vast)
				{
					$alternate_vasts[] = $vast;
				}
			}
			if (count($alternate_vasts) > 0)
			{
				$flash_vars["{$post_roll_ad_prefix}_vast_alt"] = implode('|', $alternate_vasts);
			}
		}

		if ($player_data['post_roll_duration']>0) {$flash_vars["{$post_roll_ad_prefix}_duration"]=$player_data['post_roll_duration'];}
		if ($player_data['post_roll_duration_text']!='') {$flash_vars["{$post_roll_ad_prefix}_duration_text"]=$player_data['post_roll_duration_text'];}

		if ($player_data['enable_post_roll_skip']==1)
		{
			$flash_vars["{$post_roll_ad_prefix}_skip_duration"]=$player_data['post_roll_skip_duration'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text_time"]=$player_data['post_roll_skip_text1'];
			$flash_vars["{$post_roll_ad_prefix}_skip_text"]=$player_data['post_roll_skip_text2'];
		} else
		{
			$flash_vars["{$post_roll_ad_prefix}_skip_text_time"]='Skip ad in %time';
			$flash_vars["{$post_roll_ad_prefix}_skip_text"]='Skip ad';
		}
	}
	if ($player_data['enable_pause']==1)
	{
		if ($player_data['pause_url_source']==1)
		{
			$flash_vars['adv_pause_url']=process_url($player_data['pause_url']);
		} elseif ($video_data['content_source_id']>0)
		{
			$flash_vars['adv_pause_url']="$config[project_url]/redirect_cs.php?id=$video_data[content_source_id]";
		} else {
			$flash_vars['adv_pause_url']=process_url($player_data['pause_url']);
		}
		if ($player_data['pause_file_source']==1)
		{
			$flash_vars['adv_pause_src']="$player_url/$player_data[pause_file]";
		} else {
			$pause_file=video_viewGetFromCS($video_data['content_source'],$player_data['pause_file_source']-1);
			if ($pause_file<>'')
			{
				$flash_vars['adv_pause_src']=$pause_file;
			} elseif ($pause_file=='' && $player_data['pause_file']<>'')
			{
				$flash_vars['adv_pause_src']="$player_url/$player_data[pause_file]";
			}
		}
	}
	if ($player_data['enable_pause_html']==1)
	{
		$flash_vars['adv_pause_html']="$config[project_url]/player/html.php?aid=pause_html&video_id=$video_id&cs_id=$video_data[content_source_id]&category_ids=$category_ids".($is_embed==1?'&embed=true':'');
		if ($player_data['pause_html_adaptive']==1)
		{
			$flash_vars['adv_pause_html_adaptive']=intval($player_data['pause_html_adaptive_width']).'x'.intval($player_data['pause_html_adaptive_height']);
		}
	}
	for ($i=1;$i<=4;$i++)
	{
		if ($player_data["enable_float$i"])
		{
			$flash_vars['float_src']="$config[project_url]/player/float.php?video_id=$video_id&cs_id=$video_data[content_source_id]".($is_embed==1?'&embed=true':'');
			break;
		}
	}
	if ($player_data['enable_float_replay']==1)
	{
		$flash_vars['float_replay']='true';
	}
	if ($player_data['disable_embed_code']==1)
	{
		$flash_vars['embed']='0';
	}
	if ($player_data['disable_preview_resize']==1 || $video_data['file_dimensions'][1]>$video_data['file_dimensions'][0])
	{
		$flash_vars['disable_preview_resize']='true';
	}
	if ($player_data['loop']==1)
	{
		$flash_vars['loop']='true';
	} elseif ($player_data['loop']==2 && $video_data['duration']<$player_data['loop_duration'])
	{
		$flash_vars['loop']='true';
	}
	if ($player_data['enable_adblock_protection']==1)
	{
		$flash_vars['protect_block']="$config[project_url]/player/player_ads.html";
		$flash_vars['protect_block_html']=str_replace('%POSTER_URL%',$flash_vars['preview_url'],str_replace("\n",' ',str_replace("\r",' ',$player_data['adblock_protection_html'])));
		$flash_vars['protect_block_html_after']=$player_data['adblock_protection_html_after'];
	}

	if ($player_data['enable_pre_roll_vast'] == 1 || $player_data['enable_post_roll_vast'] == 1)
	{
		$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];
		if ($vast_key_data['primary_vast_key'])
		{
			$flash_vars['lrcv'] = $vast_key_data['primary_vast_key'];
			if ($config['project_licence_domain'] != $vast_key_data['domain'] && @count($vast_key_data['aliases']) > 0)
			{
				foreach ($vast_key_data['aliases'] as $alias)
				{
					if ($config['project_licence_domain'] == $alias['domain'])
					{
						$flash_vars['lrcv'] = $alias['key'];
						break;
					}
				}
			}
		}
		if (intval($player_data['pre_roll_vast_timeout']) > 0)
		{
			$flash_vars['vast_timeout1'] = intval($player_data['pre_roll_vast_timeout']);
		}
	}

	$player_width = 0;
	$player_height = 0;
	if ($is_embed == 1)
	{
		if (intval($player_data['embed_size_option']) == 1)
		{
			$player_width = $player_data['width'];
			$player_height = $player_data['height'];
		}
	} else
	{
		$player_width = $player_data['width'];
		$player_height = $player_data['height'];
	}

	$video_size = null;
	if (isset($display_slot))
	{
		foreach ($formats as $format_rec)
		{
			if ($format_rec['postfix'] == $display_slot['type'])
			{
				$video_size = $format_rec['dimensions'];
				break;
			}
		}
	}
	if (!isset($video_size))
	{
		$video_size = $video_data['file_dimensions'];
	}
	if (intval($video_size[0]) == 0 || intval($video_size[1]) == 0)
	{
		$video_size = [800, 356];
	}

	if ($player_width > 0)
	{
		if (intval($player_data['height_option']) == 0 && $video_size[1] < $video_size[0])
		{
			$player_height = ceil($video_size[1] / $video_size[0] * $player_width);
		}
	} else
	{
		$player_width = $video_size[0];
		if ($video_size[1] > $video_size[0])
		{
			$player_height = round($video_size[0] * 9 / 16);
		} else
		{
			$player_height = $video_size[1];
		}
	}
	$flash_vars['player_width'] = $player_width;
	$flash_vars['player_height'] = $player_height;

	return $flash_vars;
}

function video_viewGetFromCS($cs_data,$file_num)
{
	global $config;

	if (is_array($cs_data))
	{
		$custom_file="custom_file{$file_num}";
		if ($cs_data[$custom_file]<>'')
		{
			return "$config[content_url_content_sources]/$cs_data[content_source_id]/$cs_data[$custom_file]";
		}
	}
	return '';
}

function video_viewGetAllowedPostfixesForCurrentUser($video_id,$owner_id,$dvd_id)
{
	global $formats_videos;

	$result=array();
	foreach ($formats_videos as $format)
	{
		if ($_SESSION['user_id']>0 && $_SESSION['user_id']==$owner_id)
		{
			$result[]=$format['postfix'];
		} elseif ($format['access_level_id']==0)
		{
			$result[]=$format['postfix'];
		} elseif ($format['access_level_id']==1 && $_SESSION['user_id']>0)
		{
			$result[]=$format['postfix'];
		} elseif ($format['access_level_id']==2 && ($_SESSION['status_id']==3 || $owner_id==$_SESSION['user_id'] || video_viewHasPremiumAccessByTokens($video_id,$owner_id,$dvd_id)))
		{
			$result[]=$format['postfix'];
		}
	}
	return $result;
}

function video_viewGetAllowedPostfixesForEveryone()
{
	global $formats_videos;

	$result=array();
	foreach ($formats_videos as $format)
	{
		if ($format['access_level_id']==0)
		{
			$result[]=$format['postfix'];
		}
	}
	return $result;
}

function video_viewGetAllowedPostfixesForDownload($video_id,$owner_id,$dvd_id)
{
	global $formats_videos;

	$result=array();
	foreach ($formats_videos as $format)
	{
		if ($format['is_download_enabled']==1)
		{
			if ($_SESSION['user_id']>0 && $_SESSION['user_id']==$owner_id)
			{
				$result[]=$format['postfix'];
			} elseif ($format['access_level_id']==0)
			{
				$result[]=$format['postfix'];
			} elseif ($format['access_level_id']==1 && $_SESSION['user_id']>0)
			{
				$result[]=$format['postfix'];
			} elseif ($format['access_level_id']==2 && ($_SESSION['status_id']==3 || $owner_id==$_SESSION['user_id'] || video_viewHasPremiumAccessByTokens($video_id,$owner_id,$dvd_id)))
			{
				$result[]=$format['postfix'];
			}
		}
	}
	return $result;
}

function video_viewHasPremiumAccessByTokens($video_id,$owner_id,$dvd_id)
{
	if ($_SESSION['status_id']==2)
	{
		foreach ($_SESSION['content_purchased'] as $purchase)
		{
			if ($video_id>0 && $purchase['video_id']==$video_id)
			{
				return true;
			}
			if ($owner_id>0 && $purchase['profile_id']==$owner_id)
			{
				return true;
			}
			if ($dvd_id>0 && $purchase['dvd_id']==$dvd_id)
			{
				return true;
			}
		}
	}
	return false;
}

function video_viewProcessRotatorParams($block_config)
{
	global $config;

	if (strpos(str_replace("www.","",$_SERVER['HTTP_REFERER']),str_replace("www.","",$config['project_url']))!==0)
	{
		return;
	}
	if (trim($_GET['pqr'])=='')
	{
		return;
	}
	if ($_SESSION['userdata']['user_id']>0)
	{
		return;
	}

	$video_dir=trim($_GET[$block_config['var_video_dir']]);
	$pqr=trim($_GET['pqr']);

	if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator",0777);chmod("$config[project_path]/admin/data/engine/rotator",0777);}
	if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}
	$fh=fopen("$config[project_path]/admin/data/engine/rotator/videos/clicks.dat","a+");
	flock($fh,LOCK_EX);
	fwrite($fh,"$video_dir:$pqr\r\n");
	fclose($fh);
}

function video_viewAddAffiliateParam($url,$player_data)
{
	if ($player_data['affiliate_param_name']!='')
	{
		if (strpos($url,'?')!==false)
		{
			$url.="&";
		} else {
			$url.="?";
		}
		return "$url$player_data[affiliate_param_name]=%$player_data[affiliate_param_name]%";
	}
	return $url;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
