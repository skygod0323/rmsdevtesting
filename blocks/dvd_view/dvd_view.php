<?php
function dvd_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	if (isset($block_config['var_dvd_id']) && intval($_REQUEST[$block_config['var_dvd_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds_active_disabled] and dvd_id=?",intval($_REQUEST[$block_config['var_dvd_id']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_dvd_dir']])<>'' && trim($_REQUEST[$block_config['var_dvd_dir']])<>$data['dir'] && $website_ui_data['WEBSITE_LINK_PATTERN_DVD']!='')
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['dvd_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_dvd_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_dvd_dir']]),trim($_REQUEST[$block_config['var_dvd_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	$data['videos_count']=$data['total_videos'];

	$data['can_upload']=1;
	if ($data['is_video_upload_allowed']>0)
	{
		$data['can_upload']=0;
		if ($_SESSION['user_id']>0)
		{
			if ($data['user_id']==$_SESSION['user_id'])
			{
				$data['can_upload']=1;
			} elseif ($data['is_video_upload_allowed']==1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))", $_SESSION['user_id'],$data['user_id'],$_SESSION['user_id'],$data['user_id']))>0)
			{
				$data['can_upload']=1;
			}
		}
	}
	if ($_SESSION['user_id']==0)
	{
		$data['can_upload']=0;
	}

	if ($data['user_id']>0)
	{
		$data['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$data['user_id']));
		if ($data['user']['avatar']!='')
		{
			$data['user']['avatar_url']=$config['content_url_avatars']."/".$data['user']['avatar'];
		}
		$data['username']=$data['user']['display_name'];
	}
	if ($_SESSION['user_id']>0)
	{
		$data['is_subscribed']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=5",$_SESSION['user_id'],$data['dvd_id']))>0)
		{
			$data['is_subscribed']=1;
		}
	}

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_dvds where $config[tables_prefix]flags_dvds.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_dvds.dvd_id=?) as votes from $config[tables_prefix]flags where group_id=3",$data['dvd_id']));
	$data['flags']=array();
	foreach($flags as $flag)
	{
		$data['flags'][$flag['external_id']]=$flag['votes'];
	}

	$data['base_files_url']=$config['content_url_dvds'].'/'.$data['dvd_id'];
	if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']!='')
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['dvd_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_dvds on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_dvds.category_id where $database_selectors[where_categories] and dvd_id=$data[dvd_id] order by id asc"));
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
	}

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_dvds on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_dvds.tag_id where $database_selectors[where_tags] and dvd_id=$data[dvd_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_dvds on $config[tables_prefix]models.model_id=$config[tables_prefix]models_dvds.model_id where $database_selectors[where_models] and dvd_id=$data[dvd_id] order by id asc"));
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

	if ($data['dvd_group_id']>0)
	{
		$result=sql_pr("select $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups] and dvd_group_id=?",$data['dvd_group_id']);
		if (mr2rows($result)>0)
		{
			$data['dvd_group']=mr2array_single($result);
			$data['dvd_group']['base_files_url']=$config['content_url_dvds'].'/groups/'.$data['dvd_group']['dvd_group_id'];
			$data['group_as_string']=$data['dvd_group']['title'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
			{
				$pattern=str_replace("%ID%",$data['dvd_group']['dvd_group_id'],str_replace("%DIR%",$data['dvd_group']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
				$data['dvd_group']['view_page_url']="$config[project_url]/$pattern";
			}
		}
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_DVDS'])==1)
	{
		if (intval($data['tokens_required'])==0)
		{
			$data['tokens_required']=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
		}
		$data['tokens_required_period']=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']);
	} else
	{
		$data['tokens_required']=0;
	}

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id>? order by dvd_id asc limit 1",intval($data['dvd_id']));
			$result_previous=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id<? order by dvd_id desc limit 1",intval($data['dvd_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==1 && $data['dvd_group_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_group_id=? and dvd_id>? order by dvd_id asc limit 1",$data['dvd_group_id'],intval($data['dvd_id']));
			$result_previous=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_group_id=? and dvd_id<? order by dvd_id desc limit 1",$data['dvd_group_id'],intval($data['dvd_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==2 && $data['user_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and user_id=? and dvd_id>? order by dvd_id asc limit 1",$data['user_id'],intval($data['dvd_id']));
			$result_previous=sql_pr("SELECT $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and user_id=? and dvd_id<? order by dvd_id desc limit 1",$data['user_id'],intval($data['dvd_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$object_next['base_files_url']=$config['content_url_dvds'].'/'.$object_next['dvd_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']!='')
			{
				$pattern=str_replace("%ID%",$object_next['dvd_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
				$object_next['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("next_dvd",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$object_previous['base_files_url']=$config['content_url_dvds'].'/'.$object_previous['dvd_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']!='')
			{
				$pattern=str_replace("%ID%",$object_previous['dvd_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
				$object_previous['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("previous_dvd",$object_previous);
		}
	}

	$smarty->assign("data",$data);
	return '';
}

function dvd_viewPreProcess($block_config,$object_id)
{
	global $config;

	if (trim($_REQUEST[$block_config['var_dvd_dir']])<>'')
	{
		$stats_str=trim($_REQUEST[$block_config['var_dvd_dir']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/dvds_dir.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	} elseif (intval($_REQUEST[$block_config['var_dvd_id']])>0)
	{
		$stats_str=intval($_REQUEST[$block_config['var_dvd_id']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/dvds_id.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	}
}

function dvd_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_dvd_dir']]);
	$id=intval($_REQUEST[$block_config['var_dvd_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('dvds_info','dvd',$id,$dir);
	}
	return "$dir|$id|$current_version";
}

function dvd_viewCacheControl($block_config)
{
	return "user_nocache";
}

function dvd_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_dvd_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_dvd_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,1,2]", "is_required"=>0),
	);
}

function dvd_viewAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['dvd_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$dvd_id=intval($_REQUEST['dvd_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dvd_id=$dvd_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_dvd','block'=>'dvd_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where dvd_id=? and ip=?",$dvd_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'dvd_view')));
			} else {
				sql_pr("insert into $config[tables_prefix]rating_history set dvd_id=?, ip=?, added_date=?",$dvd_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]dvds set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where dvd_id=$dvd_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_dvds_count=ratings_dvds_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]dvds where dvd_id=$dvd_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'dvd_view')));
		}
	} elseif ($_REQUEST['action']=='flag' && intval($_REQUEST['dvd_id'])>0)
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$dvd_id=intval($_REQUEST['dvd_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dvd_id=$dvd_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_dvd','block'=>'dvd_view')));
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=3 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'dvd_view')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'dvd_view')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and dvd_id=? and ip=?",$flag['flag_id'],$dvd_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'dvd_view')));
				} else {
					sql_pr("insert into $config[tables_prefix]flags_history set dvd_id=?, flag_id=?, ip=?, added_date=?",$dvd_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_dvds set votes=votes+1 where dvd_id=? and flag_id=?",$dvd_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_dvds set votes=1, dvd_id=?, flag_id=?",$dvd_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]dvds set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where dvd_id=$dvd_id");
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, dvd_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$dvd_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, dvd_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$dvd_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select votes from $config[tables_prefix]flags_dvds where dvd_id=$dvd_id and flag_id=?) as flags from $config[tables_prefix]dvds where dvd_id=$dvd_id",$flag['flag_id']));
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
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'dvd_view')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'dvd_view')));
		}
	}
}

function dvd_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingDVDView.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
