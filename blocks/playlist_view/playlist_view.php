<?php
function playlist_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	if (isset($block_config['var_playlist_id']) && intval($_REQUEST[$block_config['var_playlist_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists_active_disabled] and playlist_id=?",intval($_REQUEST[$block_config['var_playlist_id']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_playlist_dir']])<>'' && trim($_REQUEST[$block_config['var_playlist_dir']])<>$data['dir'] && $website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']!='')
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['playlist_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_playlist_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists_active_disabled] and dir=?",trim($_REQUEST[$block_config['var_playlist_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	if ($data['is_private']==1 && $data['user_id']!=$_SESSION['user_id'] && $_SESSION['userdata']['user_id']<1)
	{
		return 'status_404';
	}

	$data['videos_count']=$data['total_videos'];

	$data['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$data['user_id']));
	if ($data['user']['avatar']!='')
	{
		$data['user']['avatar_url']=$config['content_url_avatars']."/".$data['user']['avatar'];
	}
	$data['username']=$data['user']['display_name'];
	$data['user_avatar']=$data['user']['avatar'];

	if ($_SESSION['user_id']>0)
	{
		$data['is_subscribed']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=13",$_SESSION['user_id'],$data['playlist_id']))>0)
		{
			$data['is_subscribed']=1;
		}
	}

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_playlists where $config[tables_prefix]flags_playlists.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_playlists.playlist_id=?) as votes from $config[tables_prefix]flags where group_id=5",$data['playlist_id']));
	$data['flags']=array();
	foreach($flags as $flag)
	{
		$data['flags'][$flag['external_id']]=$flag['votes'];
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_playlists on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_playlists.category_id where $database_selectors[where_categories] and playlist_id=$data[playlist_id] order by id asc"));
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

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_playlists on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_playlists.tag_id where $database_selectors[where_tags] and playlist_id=$data[playlist_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']!='')
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['playlist_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
	}

	$videos_selector=str_replace("user_id","$config[tables_prefix]videos.user_id",str_replace("added_date","$config[tables_prefix]videos.added_date",$database_selectors['videos']));

	$metadata=playlist_viewMetaData();
	foreach ($metadata as $res)
	{
		if ($res['name']=='sort_by')
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$videos_sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($videos_sort_by=='')
	{
		$videos_sort_by=trim(strtolower($block_config['sort_by']));
	}
	if (strpos($videos_sort_by," asc")!==false) {$videos_direction="asc";} else {$videos_direction="desc";}
	$videos_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$videos_sort_by));
	if (!in_array($videos_sort_by_clear,$sorting_available))
	{
		$videos_sort_by_clear="";
	}

	if ($videos_sort_by_clear=='')
	{
		$videos_sort_by_clear="added2fav_date";
	}

	$videos_sort_by="$videos_sort_by_clear $videos_direction";
	if ($videos_sort_by_clear=='rating') {
		$videos_sort_by="rating/rating_amount desc, rating_amount desc";
	} elseif ($videos_sort_by_clear=='video_viewed') {
		$videos_sort_by="video_viewed desc";
	} elseif ($videos_sort_by_clear=='most_favourited') {
		$videos_sort_by="favourites_count $videos_direction";
	} elseif ($videos_sort_by_clear=='most_commented') {
		$videos_sort_by="comments_count $videos_direction";
	} elseif ($videos_sort_by_clear=='added2fav_date') {
		$videos_sort_by="$config[tables_prefix]fav_videos.playlist_sort_id asc, $config[tables_prefix]fav_videos.added_date $videos_direction";
	}
	if (isset($block_config['var_from']))
	{
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.playlist_id=$data[playlist_id]"));
		$from=intval($_REQUEST[$block_config['var_from']]);
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$items_per_page=intval($block_config['items_per_page']);

		$data['videos']=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.playlist_id=$data[playlist_id] order by $videos_sort_by LIMIT $from, $items_per_page"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$items_per_page;
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$items_per_page);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else
	{
		$limit='';
		$items_per_page=intval($block_config['items_per_page']);
		if ($items_per_page>0) {$limit=" limit $items_per_page";}

		$data['videos']=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.playlist_id=$data[playlist_id] order by $videos_sort_by $limit"));

		$storage[$object_id]['items_per_page']=$items_per_page;
		$smarty->assign("items_per_page",$items_per_page);
	}

	foreach ($data['videos'] as $k=>$v)
	{
		$data['videos'][$k]['duration_array']=get_duration_splitted($data['videos'][$k]['duration']);
		$data['videos'][$k]['formats']=get_video_formats($data['videos'][$k]['video_id'],$data['videos'][$k]['file_formats']);
		$data['videos'][$k]['dir_path']=get_dir_by_id($data['videos'][$k]['video_id']);

		$screen_url_base=load_balance_screenshots_url();
		$data['videos'][$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data['videos'][$k]['video_id']).'/'.$data['videos'][$k]['video_id'];

		$pattern=str_replace("%ID%",$data['videos'][$k]['video_id'],str_replace("%DIR%",$data['videos'][$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
		$data['videos'][$k]['view_page_url']="$config[project_url]/$pattern";
	}

	$storage[$object_id]['playlist_id']=$data['playlist_id'];
	$storage[$object_id]['dir']=$data['dir'];
	$storage[$object_id]['title']=$data['title'];
	$storage[$object_id]['description']=$data['description'];
	$storage[$object_id]['rating']=$data['rating'];
	$storage[$object_id]['rating_amount']=$data['rating_amount'];
	$storage[$object_id]['playlist_viewed']=$data['playlist_viewed'];
	$storage[$object_id]['added_date']=$data['added_date'];
	$storage[$object_id]['last_content_date']=$data['last_content_date'];
	$storage[$object_id]['total_videos']=$data['total_videos'];
	$storage[$object_id]['videos_count']=$data['videos_count'];
	$storage[$object_id]['tags']=$data['tags'];
	$storage[$object_id]['tags_as_string']=$data['tags_as_string'];
	$storage[$object_id]['categories']=$data['categories'];
	$storage[$object_id]['categories_as_string']=$data['categories_as_string'];
	$storage[$object_id]['comments_count']=$data['comments_count'];
	$storage[$object_id]['user_id']=$data['user_id'];
	$storage[$object_id]['username']=$data['username'];
	$storage[$object_id]['user_avatar']=$data['user_avatar'];
	$storage[$object_id]['status_id']=$data['status_id'];
	$storage[$object_id]['is_private']=$data['is_private'];
	$storage[$object_id]['subscribers_count']=$data['subscribers_count'];
	$storage[$object_id]['canonical_url']=$data['canonical_url'];

	$storage[$object_id]['sort_by']=$videos_sort_by_clear;
	$smarty->assign("sort_by",$videos_sort_by_clear);

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and playlist_id>? order by playlist_id asc limit 1",intval($data['playlist_id']));
			$result_previous=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and playlist_id<? order by playlist_id desc limit 1",intval($data['playlist_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==3 && $data['user_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and user_id=? and playlist_id>? order by playlist_id asc limit 1",$data['user_id'],intval($data['playlist_id']));
			$result_previous=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and user_id=? and playlist_id<? order by playlist_id desc limit 1",$data['user_id'],intval($data['playlist_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']!='')
			{
				$pattern=str_replace("%ID%",$object_next['playlist_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
				$object_next['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("next_playlist",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']!='')
			{
				$pattern=str_replace("%ID%",$object_previous['playlist_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
				$object_previous['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("previous_playlist",$object_previous);
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function playlist_viewPreProcess($block_config,$object_id)
{
	global $config;

	if (intval($_REQUEST[$block_config['var_playlist_id']])>0)
	{
		$stats_str=intval($_REQUEST[$block_config['var_playlist_id']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/playlists_id.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	} elseif (trim($_REQUEST[$block_config['var_playlist_dir']])<>'')
	{
		$stats_str=trim($_REQUEST[$block_config['var_playlist_dir']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/playlists_dir.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	}
}

function playlist_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_playlist_dir']]);
	$id=intval($_REQUEST[$block_config['var_playlist_id']]);
	$from=intval($_REQUEST[$block_config['var_from']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('playlists_info','playlist',$id,$dir);
	}
	return "$dir|$id|$from|$current_version";
}

function playlist_viewCacheControl($block_config)
{
	return "user_nocache";
}

function playlist_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_playlist_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_playlist_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// pagination
		array("name"=>"items_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"0"),
		array("name"=>"links_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",       "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[rating,video_viewed,most_favourited,most_commented,added2fav_date]","is_required"=>0, "default_value"=>"added2fav_date desc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,3]", "is_required"=>0),
	);
}

function playlist_viewAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['playlist_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$playlist_id=intval($_REQUEST['playlist_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'playlist_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where playlist_id=? and ip=?",$playlist_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'playlist_view')));
			} else {
				sql_pr("insert into $config[tables_prefix]rating_history set playlist_id=?, ip=?, added_date=?",$playlist_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]playlists set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where playlist_id=$playlist_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_playlists_count=ratings_playlists_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]playlists where playlist_id=$playlist_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_view')));
		}
	} elseif ($_REQUEST['action']=='flag' && intval($_REQUEST['playlist_id'])>0)
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$playlist_id=intval($_REQUEST['playlist_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'playlist_view')));
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=5 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'playlist_view')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'playlist_view')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and playlist_id=? and ip=?",$flag['flag_id'],$playlist_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'playlist_view')));
				} else {
					sql_pr("insert into $config[tables_prefix]flags_history set playlist_id=?, flag_id=?, ip=?, added_date=?",$playlist_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_playlists set votes=votes+1 where playlist_id=? and flag_id=?",$playlist_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_playlists set votes=1, playlist_id=?, flag_id=?",$playlist_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]playlists set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where playlist_id=$playlist_id");
						if (intval($_SESSION['user_id'])>0)
						{
							sql_pr("update $config[tables_prefix]users set ratings_playlists_count=ratings_playlists_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
						}
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, playlist_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$playlist_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, playlist_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$playlist_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select votes from $config[tables_prefix]flags_playlists where playlist_id=$playlist_id and flag_id=?) as flags from $config[tables_prefix]playlists where playlist_id=$playlist_id",$flag['flag_id']));
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
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'playlist_view')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_view')));
		}
	} elseif ($_REQUEST['action']=='add_to_favourites' && isset($_REQUEST['video_ids']))
	{
		if ($_SESSION['user_id']<1)
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'playlist_view')));
		}
		if (!is_array($_REQUEST['video_ids']))
		{
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_view')));
		}

		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		$user_id=intval($_SESSION['user_id']);
		$video_ids=array_map("intval",$_REQUEST['video_ids']);
		$fav_type=intval($_REQUEST['fav_type']);
		$playlist_id=intval($_REQUEST['playlist_id']);
		if ($playlist_id>0)
		{
			if (mr2number(sql("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id and user_id=$user_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'playlist_view')));
			}
			$fav_type=10;
		}

		foreach ($video_ids as $video_id)
		{
			if ($video_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]fav_videos where user_id=? and video_id=? and fav_type=? and playlist_id=?",$user_id,$video_id,$fav_type,$playlist_id))==0)
				{
					sql_pr("insert into $config[tables_prefix]fav_videos set video_id=?, user_id=?, fav_type=?, playlist_id=?, added_date=?",$video_id,$user_id,$fav_type,$playlist_id,date("Y-m-d H:i:s"));
				}
			}
		}
		if (count($video_ids)>0)
		{
			$video_ids=implode(",",$video_ids);
			fav_videos_changed($video_ids,$fav_type);
		}

		$result_data=array();
		$result_data['favourites_total']=intval($_SESSION['favourite_videos_amount']);
		foreach ($_SESSION['playlists'] as $playlist)
		{
			if ($playlist['playlist_id']==$playlist_id)
			{
				$result_data['favourites_playlist']=intval($playlist['total_videos']);
				break;
			}
		}
		async_return_request_status(null,null,$result_data);
	}
}

function playlist_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingPlaylistView.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
