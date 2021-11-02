<?php
function list_members_eventsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where_comments="";
	$where_user="";
	if (isset($block_config['mode_global']))
	{
		if (isset($block_config['show_users']))
		{
			$temp=array_map("intval",explode(",",$block_config['show_users']));
			$where_temp='';
			foreach ($temp as $temp_value)
			{
				if ($temp_value>0)
				{
					$where_temp.="or $config[tables_prefix]users_events.user_id=$temp_value ";
				}
			}
			if ($where_temp<>'')
			{
				$where_user.=" and (".substr($where_temp,2).")";
			}
		} elseif (isset($block_config['skip_users']))
		{
			$temp=array_map("intval",explode(",",$block_config['skip_users']));
			$where_temp='';
			foreach ($temp as $temp_value)
			{
				if ($temp_value>0)
				{
					$where_temp.="and $config[tables_prefix]users_events.user_id<>$temp_value ";
				}
			}
			if ($where_temp<>'')
			{
				$where_user.=" and (".substr($where_temp,3).") ";
			}
		}
	} elseif (isset($block_config['var_user_id']))
	{
		$user_id=intval($_REQUEST[$block_config['var_user_id']]);
		$user_info=mr2array_single(sql("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=$user_id"));
		if (count($user_info)>0)
		{
			$smarty->assign("user_id",$user_id);
			$smarty->assign("username",$user_info['username']);
			$smarty->assign("display_name",$user_info['display_name']);
			$smarty->assign("avatar",$user_info['avatar']);
			$smarty->assign("gender_id",$user_info['gender_id']);
			$smarty->assign("city",$user_info['city']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['username']=$user_info['username'];
			$storage[$object_id]['display_name']=$user_info['display_name'];
			$storage[$object_id]['avatar']=$user_info['avatar'];
			$storage[$object_id]['gender_id']=$user_info['gender_id'];
			$storage[$object_id]['city']=$user_info['city'];
			if ($user_info['country_id']>0)
			{
				$smarty->assign("country_id",$user_info['country_id']);
				$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
				$storage[$object_id]['country_id']=$user_info['country_id'];
				$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
			}

			if (isset($block_config['mode_friends']))
			{
				$temp_ids=mr2array(sql("select user_id, friend_id from $config[tables_prefix]friends where (user_id=$user_id or friend_id=$user_id) and is_approved=1"));
				$user_ids=array();
				foreach ($temp_ids as $res)
				{
					if ($res['user_id']<>$user_id)
					{
						$user_ids[]=$res['user_id'];
					}
					if ($res['friend_id']<>$user_id)
					{
						$user_ids[]=$res['friend_id'];
					}
				}
				if (count($user_ids)>0)
				{
					$user_ids=implode(",",array_unique($user_ids));
				} else {
					$user_ids=0;
				}
				$where_user=" and ($config[tables_prefix]users_events.user_id in ($user_ids) or ($config[tables_prefix]users_events.user_target_id in ($user_ids) and $config[tables_prefix]users_events.event_type_id in (10,11)))";
			} else
			{
				$where_user=" and ($config[tables_prefix]users_events.user_id=$user_id or ($config[tables_prefix]users_events.user_target_id=$user_id and $config[tables_prefix]users_events.event_type_id in (10,11)))";
			}
		} else {
			return 'status_404';
		}
	} elseif ($_SESSION['user_id']>0) {
		$user_id=intval($_SESSION['user_id']);
		$smarty->assign("display_name",$_SESSION['display_name']);
		$smarty->assign("avatar",$_SESSION['avatar']);
		$smarty->assign("user_id",$user_id);
		$storage[$object_id]['user_id']=$user_id;
		$storage[$object_id]['display_name']=$_SESSION['display_name'];
		$storage[$object_id]['avatar']=$_SESSION['avatar'];

		if (isset($block_config['mode_friends']))
		{
			$temp_ids=mr2array(sql("select user_id, friend_id from $config[tables_prefix]friends where (user_id=$user_id or friend_id=$user_id) and is_approved=1"));
			$user_ids=array();
			foreach ($temp_ids as $res)
			{
				if ($res['user_id']<>$user_id)
				{
					$user_ids[]=$res['user_id'];
				}
				if ($res['friend_id']<>$user_id)
				{
					$user_ids[]=$res['friend_id'];
				}
			}
			if (count($user_ids)>0)
			{
				$user_ids=implode(",",array_unique($user_ids));
			} else {
				$user_ids=0;
			}
			$where_user=" and ($config[tables_prefix]users_events.user_id in ($user_ids) or ($config[tables_prefix]users_events.user_target_id in ($user_ids) and $config[tables_prefix]users_events.event_type_id in (10,11)))";
		} else {
			$where_user=" and ($config[tables_prefix]users_events.user_id=$user_id or ($config[tables_prefix]users_events.user_target_id=$user_id and $config[tables_prefix]users_events.event_type_id in (10,11)))";
		}
	} else {
		if ($_GET['mode']=='async')
		{
			header('HTTP/1.0 403 Forbidden');die;
		}

		$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
		if (isset($block_config['redirect_unknown_user_to']))
		{
			$url=process_url($block_config['redirect_unknown_user_to']);
			return "status_302: $url";
		} else
		{
			return "status_302: $config[project_url]";
		}
	}

	if (isset($block_config['match_locale']))
	{
		$language_code=sql_escape($config['locale']);
		$where_comments=" and $config[tables_prefix]comments.language_code='$language_code'";
		$where_user.=" and $config[tables_prefix]users.language_code='$language_code'";
	}

	$now_date=date("Y-m-d H:i:s");
	$sql_select="
		$config[tables_prefix]users_events.*,
		$config[tables_prefix]users.display_name,
		$config[tables_prefix]users.avatar,
		$config[tables_prefix]users.gender_id,
		$config[tables_prefix]users.country_id,
		$config[tables_prefix]users.city,
		$config[tables_prefix]users.last_online_date,
		$config[tables_prefix]users.about_me,
		$database_selectors[videos_selector_title] as video_title,
		$database_selectors[videos_selector_description] as video_description,
		$database_selectors[videos_selector_dir] as video_dir,
		$database_selectors[albums_selector_title] as album_title,
		$database_selectors[albums_selector_description] as album_description,
		$database_selectors[albums_selector_dir] as album_dir,
		$database_selectors[content_sources_selector_title] as cs_title,
		$database_selectors[content_sources_selector_description] as cs_description,
		$database_selectors[content_sources_selector_dir] as cs_dir,
		$database_selectors[models_selector_title] as model_title,
		$database_selectors[models_selector_description] as model_description,
		$database_selectors[models_selector_dir] as model_dir,
		$database_selectors[dvds_selector_title] as dvd_title,
		$database_selectors[dvds_selector_description] as dvd_description,
		$database_selectors[dvds_selector_dir] as dvd_dir,
		$database_selectors[posts_selector_title] as post_title,
		$database_selectors[posts_selector_description] as post_description,
		$database_selectors[posts_selector_dir] as post_dir,
		$config[tables_prefix]posts.post_type_id as post_type_id,
		$database_selectors[playlists_selector_title] as playlist_title,
		$database_selectors[playlists_selector_description] as playlist_description,
		$database_selectors[playlists_selector_dir] as playlist_dir,
		$config[tables_prefix]playlists.user_id as playlist_user_id,
		$config[tables_prefix]comments.comment,
		$config[tables_prefix]users_blogs.entry,
		u.display_name as user_target_name,
		u.avatar as user_target_avatar,
		u.gender_id as user_target_gender_id,
		u.country_id as user_target_country_id,
		u.city as user_target_city,
		u.about_me as user_target_about_me
	";
	$sql_from="
		$config[tables_prefix]users_events inner join
		$config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]users_events.user_id left join
		$config[tables_prefix]videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]users_events.video_id left join
		$config[tables_prefix]albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]users_events.album_id left join
		$config[tables_prefix]comments on $config[tables_prefix]users_events.comment_id=$config[tables_prefix]comments.comment_id left join
		$config[tables_prefix]users_blogs on $config[tables_prefix]users_events.entry_id=$config[tables_prefix]users_blogs.entry_id left join
		$config[tables_prefix]content_sources on $config[tables_prefix]users_events.content_source_id=$config[tables_prefix]content_sources.content_source_id left join
		$config[tables_prefix]models on $config[tables_prefix]users_events.model_id=$config[tables_prefix]models.model_id left join
		$config[tables_prefix]dvds on $config[tables_prefix]users_events.dvd_id=$config[tables_prefix]dvds.dvd_id left join
		$config[tables_prefix]posts on $config[tables_prefix]users_events.post_id=$config[tables_prefix]posts.post_id left join
		$config[tables_prefix]users u on $config[tables_prefix]users_events.user_target_id=u.user_id left join
		$config[tables_prefix]playlists on $config[tables_prefix]users_events.playlist_id=$config[tables_prefix]playlists.playlist_id
	";
	$sql_where="
		$config[tables_prefix]users_events.added_date<='$now_date' and
		($config[tables_prefix]users_events.video_id<1 or ($database_selectors[where_videos])) and
		($config[tables_prefix]users_events.album_id<1 or ($database_selectors[where_albums])) and
		($config[tables_prefix]users_events.post_id<1 or ($database_selectors[where_posts])) and
		($config[tables_prefix]users_events.playlist_id<1 or ($database_selectors[where_playlists])) and
		($config[tables_prefix]users_events.content_source_id<1 or ($database_selectors[where_content_sources])) and
		($config[tables_prefix]users_events.model_id<1 or ($database_selectors[where_models])) and
		($config[tables_prefix]users_events.dvd_id<1 or ($database_selectors[where_dvds])) and
		($config[tables_prefix]users_events.comment_id<1 or ($config[tables_prefix]comments.is_approved=1 $where_comments)) and
		($config[tables_prefix]users_events.entry_id<1 or $config[tables_prefix]users_blogs.is_approved=1)
		$where_user
	";

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $sql_from where $sql_where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$limit=intval($block_config['items_per_page']);
		if ($limit==0) {$limit=$total_count;}

		$data=mr2array(sql("select $sql_select from $sql_from where $sql_where order by $config[tables_prefix]users_events.added_date desc limit $from, $limit"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else {
		$limit='';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("select $sql_select from $sql_from where $sql_where order by $config[tables_prefix]users_events.added_date desc $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$post_types=mr2array(sql("select * from $config[tables_prefix]posts_types"));

	$cluster=unserialize(file_get_contents("$config[project_path]/admin/data/system/cluster.dat"));
	$cluster_servers=array();
	$cluster_servers_weights=array();
	foreach ($cluster as $server)
	{
		if ($server['status_id']==1)
		{
			$cluster_servers[intval($server['group_id'])][]=$server;
			$cluster_servers_weights[intval($server['group_id'])]+=$server['lb_weight'];
		}
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);

		if ($data[$k]['avatar']<>'')
		{
			$data[$k]['avatar']=$config['content_url_avatars']."/".$data[$k]['avatar'];
			$data[$k]['avatar_url']=$data[$k]['avatar'];
		}
		if ($data[$k]['user_target_avatar']<>'')
		{
			$data[$k]['user_target_avatar']=$config['content_url_avatars']."/".$data[$k]['user_target_avatar'];
			$data[$k]['user_target_avatar_url']=$data[$k]['user_target_avatar'];
		}
		$data[$k]['is_online']=0;
		if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
		{
			if (time()-strtotime($data[$k]['last_online_date'])<$website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL']*60 + 30)
			{
				$data[$k]['is_online']=1;
			}
		}

		$pattern='';
		if ($data[$k]['video_id']>0)
		{
			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['video_dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			if (isset($block_config['pull_content']))
			{
				$data[$k]['video']=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where video_id=?",$data[$k]['video_id']));

				$data[$k]['video']['time_passed_from_adding']=get_time_passed($data[$k]['video']['post_date']);
				$data[$k]['video']['duration_array']=get_duration_splitted($data[$k]['video']['duration']);
				$data[$k]['video']['formats']=get_video_formats($data[$k]['video']['video_id'],$data[$k]['video']['file_formats']);
				$data[$k]['video']['dir_path']=get_dir_by_id($data[$k]['video']['video_id']);

				$screen_url_base=load_balance_screenshots_url();
				$data[$k]['video']['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video']['video_id']).'/'.$data[$k]['video']['video_id'];
			}
		} elseif ($data[$k]['album_id']>0)
		{
			$pattern=str_replace("%ID%",$data[$k]['album_id'],str_replace("%DIR%",$data[$k]['album_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			if (isset($block_config['pull_content']))
			{
				$data[$k]['album']=mr2array_single(sql_pr("select $database_selectors[albums] from $config[tables_prefix]albums where album_id=?",$data[$k]['album_id']));

				$data[$k]['album']['time_passed_from_adding']=get_time_passed($data[$k]['album']['post_date']);
				$data[$k]['album']['dir_path']=get_dir_by_id($data[$k]['album']['album_id']);

				$lb_server=load_balance_server($data[$k]['album']['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$data[$k]['album']['preview_url']="$lb_server[urls]/preview";
			}
		} elseif ($data[$k]['content_source_id']>0)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_source_id'],str_replace("%DIR%",$data[$k]['cs_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
			}
			if (isset($block_config['pull_content']))
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where content_source_id=?",$data[$k]['content_source_id']));
				$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
			}
		} elseif ($data[$k]['model_id']>0)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['model_id'],str_replace("%DIR%",$data[$k]['model_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
			}
			if (isset($block_config['pull_content']))
			{
				$data[$k]['model']=mr2array_single(sql_pr("select $database_selectors[models] from $config[tables_prefix]models where model_id=?",$data[$k]['model_id']));
				$data[$k]['model']['base_files_url']=$config['content_url_models'].'/'.$data[$k]['model']['model_id'];
			}
		} elseif ($data[$k]['dvd_id']>0)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['dvd_id'],str_replace("%DIR%",$data[$k]['dvd_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
			}
			if (isset($block_config['pull_content']))
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where dvd_id=?",$data[$k]['dvd_id']));
				$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
			}
		} elseif ($data[$k]['post_id']>0)
		{
			foreach ($post_types as $post_type)
			{
				if ($post_type['post_type_id']==$data[$k]['post_type_id'])
				{
					$pattern=str_replace("%ID%",$data[$k]['post_id'],str_replace("%DIR%",$data[$k]['post_dir'],$post_type['url_pattern']));
					break;
				}
			}
			if (isset($block_config['pull_content']))
			{
				$data[$k]['post']=mr2array_single(sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where post_id=?",$data[$k]['post_id']));
				$data[$k]['post']['base_files_url']=$config['content_url_posts'].'/'.get_dir_by_id($data[$k]['post_id']).'/'.$data[$k]['post']['post_id'];
			}
		} elseif ($data[$k]['playlist_id']>0)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['playlist_id'],str_replace("%DIR%",$data[$k]['playlist_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
			}
			if (isset($block_config['pull_content']))
			{
				$data[$k]['playlist']=mr2array_single(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=?",$data[$k]['playlist_id']));
			}
		}
		if ($pattern<>'')
		{
			$data[$k]['content_view_page_url']="$config[project_url]/$pattern";
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_members_eventsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);

	if (isset($block_config['mode_global']))
	{
		return "$from|$items_per_page";
	} elseif (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "$var_user_id|$from|$items_per_page";
}

function list_members_eventsCacheControl($block_config)
{
	if (!isset($block_config['mode_global']) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_members_eventsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// static filters
		array("name"=>"skip_users",   "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0),
		array("name"=>"show_users",   "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0),
		array("name"=>"match_locale", "group"=>"static_filters", "type"=>"",         "is_required"=>0),

		// display modes
		array("name"=>"mode_global",              "group"=>"display_modes", "type"=>"",         "is_required"=>0, "default_value"=>"1"),
		array("name"=>"mode_friends",             "group"=>"display_modes", "type"=>"",         "is_required"=>0),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING",   "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING",   "is_required"=>0, "default_value"=>"/?login"),

		// pull content
		array("name"=>"pull_content", "group"=>"pull_content", "type"=>"", "is_required"=>0),

	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>