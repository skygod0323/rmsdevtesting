<?php
function custom_list_videosShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$page_id,$website_ui_data,$page_is_xml,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}
	$from=intval($_REQUEST[$block_config['var_from']]);
	if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}

	$external_search_enabled=0;
	$external_search_enabled_condition=0;
	$external_search_display=0;
	$external_search_text='';
	$external_search_from=0;
	$internal_query_enabled=1;
	$sort_by_relevance='';

	$smarty->assign("list_countries",$list_countries);

	if ($_REQUEST['action']=='delete_from_favourites' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$fav_type=intval($_REQUEST['fav_type']);
			$playlist_id=intval($_REQUEST['playlist_id']);
			$move_to_playlist_id=0;
			if ($playlist_id>0)
			{
				$result=sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=? and user_id=?",$playlist_id,$user_id);
				if (mr2rows($result)==0)
				{
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'forbidden','block'=>'list_videos')));
					} else {
						header("Location: ?action=delete_forbidden");die;
					}
				} else {
					$data_temp=mr2array_single($result);
					if ($data_temp['is_locked']==1)
					{
						if ($_REQUEST['mode']=='async')
						{
							async_return_request_status(array(array('error_code'=>'playlist_locked','error_details'=>array($data_temp['title']), 'block'=>'list_videos')));
						} else {
							header("Location: ?action=delete_forbidden");die;
						}
					}
					$fav_type=10;
				}
			}

			if (intval($_REQUEST['move_to_playlist_id'])>0)
			{
				$result=sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=? and user_id=?",intval($_REQUEST['move_to_playlist_id']),$user_id);
				if (mr2rows($result)==0)
				{
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'forbidden','block'=>'list_videos')));
					} else {
						header("Location: ?action=delete_forbidden");die;
					}
				} else {
					$data_temp=mr2array_single($result);
					if ($data_temp['is_locked']==1)
					{
						if ($_REQUEST['mode']=='async')
						{
							async_return_request_status(array(array('error_code'=>'playlist_locked','error_details'=>array($data_temp['title']), 'block'=>'list_videos')));
						} else {
							header("Location: ?action=delete_forbidden");die;
						}
					}
					$move_to_playlist_id=intval($_REQUEST['move_to_playlist_id']);
				}
			}

			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));
			sql_pr("delete from $config[tables_prefix]fav_videos where user_id=? and video_id in ($delete_ids) and fav_type=? and playlist_id=?",$user_id,$fav_type,$playlist_id);

			if ($move_to_playlist_id>0)
			{
				foreach ($_REQUEST['delete'] as $video_id)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]fav_videos where user_id=? and video_id=? and fav_type=10 and playlist_id=?",$user_id,intval($video_id),$move_to_playlist_id))==0)
					{
						sql_pr("insert into $config[tables_prefix]fav_videos set user_id=?, video_id=?, fav_type=10, playlist_id=?, added_date=?",$user_id,intval($video_id),$move_to_playlist_id,date("Y-m-d H:i:s"));
					}
				}
				sql_pr("update $config[tables_prefix]playlists set last_content_date=?, total_videos=(select count(*) from $config[tables_prefix]fav_videos where $config[tables_prefix]playlists.playlist_id=$config[tables_prefix]fav_videos.playlist_id) where playlist_id=?",date("Y-m-d H:i:s"),$move_to_playlist_id);
			}

			fav_videos_changed($delete_ids,$fav_type);
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
	}

	if ($_REQUEST['action']=='delete_from_uploaded' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql_pr("select video_id from $config[tables_prefix]videos where user_id=? and video_id in ($delete_ids_str) and is_locked=0",$user_id));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);

				if (isset($block_config['allow_delete_uploaded_videos']))
				{
					sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id in ($delete_ids_str)");

					foreach ($delete_ids as $video_id)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where video_id=? and expiry_date>?",$video_id,date("Y-m-d H:i:s")))==0)
						{
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=1, added_date=?",$_SESSION['user_id'],$_SESSION['username'],$video_id,date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?",$video_id,serialize(array()),date("Y-m-d H:i:s"));
						} else {
							sql_pr("update $config[tables_prefix]videos set status_id=0 where video_id=?",$video_id);
						}
					}
				} else {
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'delete_forbidden','block'=>'list_videos')));
					} else {
						header("Location: ?action=delete_forbidden");die;
					}
				}
			}
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
	}

	if (($_REQUEST['action']=='delete_from_public' || $_REQUEST['action']=='delete_from_private') && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$is_private_old=1;
			$is_private_new=0;
			if ($_REQUEST['action']=='delete_from_public')
			{
				$is_private_old=0;
				$is_private_new=1;
			}

			$user_id=intval($_SESSION['user_id']);
			$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql_pr("select video_id from $config[tables_prefix]videos where user_id=? and video_id in ($delete_ids_str) and is_locked=0 and is_private=?",$user_id,$is_private_old));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);

				sql_pr("update $config[tables_prefix]videos set is_private=? where video_id in ($delete_ids_str)",$is_private_new);

				foreach ($delete_ids as $video_id)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=1, action_details='is_private', added_date=?",$user_id,$_SESSION['username'],$video_id,date("Y-m-d H:i:s"));
					if ($is_private_new==0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=7, user_id=?, video_id=?, added_date=?",$user_id,$video_id,date("Y-m-d H:i:s"));
					} else {
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=6, user_id=?, video_id=?, added_date=?",$user_id,$video_id,date("Y-m-d H:i:s"));
					}
				}
				sql_pr("update $config[tables_prefix]users set
						public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_videos_count=public_videos_count+private_videos_count+premium_videos_count
					where user_id=?",$user_id
				);
			}
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				if ($is_private_new==0)
				{
					header("Location: ?action=delete_from_private_done");die;
				} else {
					header("Location: ?action=delete_from_public_done");die;
				}
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
	}

	if ($_REQUEST['action']=='delete_from_dvd' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$dvd_id=intval($_REQUEST['dvd_id']);

			$result=sql_pr("select * from $config[tables_prefix]dvds where dvd_id=? and user_id=?",$dvd_id,$user_id);
			if (mr2rows($result)==0)
			{
				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'forbidden','block'=>'list_videos')));
				} else {
					header("Location: ?action=delete_forbidden");die;
				}
			} else {
				$data_temp=mr2array_single($result);
				if ($data_temp['is_locked']==1)
				{
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'dvd_locked','error_details'=>array($data_temp['title']), 'block'=>'list_videos')));
					} else {
						header("Location: ?action=delete_forbidden");die;
					}
				}
			}

			$delete_ids=array_map("intval",$_REQUEST['delete']);
			if (count($delete_ids)>0)
			{
				foreach ($delete_ids as $video_id)
				{
					if (sql_update("update $config[tables_prefix]videos set dvd_id=0 where video_id=? and dvd_id=?",$video_id,$dvd_id))
					{
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=1, action_details='dvd_id', added_date=?",$_SESSION['user_id'],$_SESSION['username'],$video_id,date("Y-m-d H:i:s"));
					}
				}
				sql_pr("update $config[tables_prefix]dvds set total_videos=(select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] and dvd_id=$config[tables_prefix]dvds.dvd_id), total_videos_duration=(select sum(duration) from $config[tables_prefix]videos where $database_selectors[where_videos] and dvd_id=$config[tables_prefix]dvds.dvd_id) where dvd_id=?",$dvd_id);
			}

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
	}

	$where='';

	if ((isset($block_config['var_is_private']) && $_REQUEST[$block_config['var_is_private']]<>'') || $block_config['is_private']<>'')
	{
		if (trim($_REQUEST[$block_config['var_is_private']])<>'')
		{
			$temp_list=trim($_REQUEST[$block_config['var_is_private']]);
		} else {
			$temp_list=$block_config['is_private'];
		}
		$temp_list=str_replace("|",",",$temp_list);
		$temp_list=explode(",",$temp_list);
		$temp_list=implode(",",array_map("intval",$temp_list));
		$where.=" and is_private in ($temp_list)";
		$storage[$object_id]['is_private']=$temp_list;
		$smarty->assign("is_private",$temp_list);
	}

	if ((isset($block_config['var_is_hd']) && $_REQUEST[$block_config['var_is_hd']]<>'') || $block_config['is_hd']<>'')
	{
		if (trim($_REQUEST[$block_config['var_is_hd']])<>'')
		{
			$temp_list=intval($_REQUEST[$block_config['var_is_hd']]);
		} else {
			$temp_list=intval($block_config['is_hd']);
		}
		$where.=" and is_hd=$temp_list";
		$storage[$object_id]['is_hd']=$temp_list;
		$smarty->assign("is_hd",$temp_list);
	}

	if (isset($block_config['var_duration_from']) && intval($_REQUEST[$block_config['var_duration_from']])>0)
	{
		$where.=" and duration >= ".intval($_REQUEST[$block_config['var_duration_from']]);
	}
	if (isset($block_config['var_duration_to']) && intval($_REQUEST[$block_config['var_duration_to']])>0)
	{
		$where.=" and duration < ".intval($_REQUEST[$block_config['var_duration_to']]);
	}

	if (isset($block_config['var_post_date_from']) && trim($_REQUEST[$block_config['var_post_date_from']])!='')
	{
		$date_from=explode('-',trim($_REQUEST[$block_config['var_post_date_from']]));
		if (count($date_from)>=3)
		{
			$date_from=date("Y-m-d 00:00:00",mktime(0,0,0,intval($date_from[1]),intval($date_from[2]),intval($date_from[0])));
			$where.=" and $database_selectors[generic_post_date_selector] >= '$date_from'";
		}
	}
	if (isset($block_config['var_post_date_to']) && trim($_REQUEST[$block_config['var_post_date_to']])!='')
	{
		$date_to=explode('-',trim($_REQUEST[$block_config['var_post_date_to']]));
		if (count($date_to)>=3)
		{
			$date_to=date("Y-m-d 23:59:59",mktime(0,0,0,intval($date_to[1]),intval($date_to[2]),intval($date_to[0])));
			$where.=" and $database_selectors[generic_post_date_selector] < '$date_to'";
		}
	}

	if (isset($block_config['var_release_year_from']) && intval($_REQUEST[$block_config['var_release_year_from']])>0)
	{
		$where.=" and release_year >= ".intval($_REQUEST[$block_config['var_release_year_from']]);
	}
	if (isset($block_config['var_release_year_to']) && intval($_REQUEST[$block_config['var_release_year_to']])>0)
	{
		$where.=" and release_year <= ".intval($_REQUEST[$block_config['var_release_year_to']]);
	}

	if ($block_config['show_private']==1 && $_SESSION['user_id']<1)
	{
		$where.=" and is_private<>1 ";
	} elseif ($block_config['show_private']==2 && $_SESSION['status_id']<>3)
	{
		$where.=" and is_private<>1 ";
	}

	if ($block_config['show_premium']==1 && $_SESSION['user_id']<1)
	{
		$where.=" and is_private<>2 ";
	} elseif ($block_config['show_premium']==2 && $_SESSION['status_id']<>3)
	{
		$where.=" and is_private<>2 ";
	}

	if ($block_config['format_postfix']<>'')
	{
		$postfix=sql_escape($block_config['format_postfix']);
		$where.=" and file_formats like concat('%||$postfix|%')";
	}

	if (isset($block_config['mode_history']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
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

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos inner join $config[tables_prefix]log_content_users on $config[tables_prefix]videos.video_id=$config[tables_prefix]log_content_users.video_id where $database_selectors[where_videos] and $config[tables_prefix]log_content_users.user_id=$user_id and $config[tables_prefix]log_content_users.is_old=0 $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$videos_selector=str_replace("user_id","$config[tables_prefix]videos.user_id",str_replace("added_date","$config[tables_prefix]videos.added_date",$database_selectors['videos']));
		$data=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]log_content_users.added_date as visit_date from $config[tables_prefix]videos inner join $config[tables_prefix]log_content_users on $config[tables_prefix]videos.video_id=$config[tables_prefix]log_content_users.video_id where $database_selectors[where_videos] and $config[tables_prefix]log_content_users.user_id=$user_id and $config[tables_prefix]log_content_users.is_old=0 $where order by $config[tables_prefix]log_content_users.added_date desc LIMIT $from, $block_config[items_per_page]"));
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));

 
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		$storage[$object_id]['mode_history']=1;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_history",1);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	} elseif (isset($block_config['mode_purchased']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
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

		$now_date=date("Y-m-d H:i:s");
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos inner join $config[tables_prefix]users_purchases on $config[tables_prefix]videos.video_id=$config[tables_prefix]users_purchases.video_id where $database_selectors[where_videos_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$videos_selector=str_replace("dvd_id","$config[tables_prefix]videos.dvd_id",str_replace("user_id","$config[tables_prefix]videos.user_id",str_replace("added_date","$config[tables_prefix]videos.added_date",$database_selectors['videos'])));
		$data=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]users_purchases.added_date as purchase_date, $config[tables_prefix]users_purchases.expiry_date as expiry_date, $config[tables_prefix]users_purchases.tokens as tokens_spent from $config[tables_prefix]videos inner join $config[tables_prefix]users_purchases on $config[tables_prefix]videos.video_id=$config[tables_prefix]users_purchases.video_id where $database_selectors[where_videos_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where order by $config[tables_prefix]users_purchases.added_date desc LIMIT $from, $block_config[items_per_page]"));
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		$storage[$object_id]['mode_purchased']=1;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_purchased",1);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	} elseif (isset($block_config['mode_favourites']))
	{
		$my_mode_favourites=0;
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
			$my_mode_favourites=1;
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

		if ($my_mode_favourites==1)
		{
			$smarty->assign("playlists",mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=?",$user_id)));
			$favourites_summary=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $config[tables_prefix]fav_videos.user_id=? and $database_selectors[where_videos] group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$user_id));
		} else {
			$smarty->assign("playlists",mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and user_id=?",$user_id)));
			$favourites_summary=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id left join $config[tables_prefix]playlists on $config[tables_prefix]fav_videos.playlist_id = $config[tables_prefix]playlists.playlist_id where $config[tables_prefix]fav_videos.user_id=? and $database_selectors[where_videos] and ($config[tables_prefix]fav_videos.fav_type!=10 or $database_selectors[where_playlists]) group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$user_id));
		}

		$temp_summary=array();
		$temp_total=0;
		foreach ($favourites_summary as $summary_item)
		{
			$temp_summary[$summary_item['fav_type']]=$summary_item;
			$temp_total+=$summary_item["amount"];
		}
		$smarty->assign("favourites_summary",$temp_summary);
		$smarty->assign("favourites_summary_total",$temp_total);
		$storage[$object_id]["favourites_summary"]=$temp_summary;
		$storage[$object_id]["favourites_summary_total"]=$temp_total;

		$fav_type=intval($block_config['fav_type']);
		if (isset($block_config['var_fav_type']))
		{
			$fav_type=intval($_REQUEST[$block_config['var_fav_type']]);
		}
		$playlist_id=0;
		if (isset($block_config['var_playlist_id']))
		{
			$playlist_id=intval($_REQUEST[$block_config['var_playlist_id']]);
			if ($playlist_id>0)
			{
				$fav_type=10;
				if ($my_mode_favourites==1)
				{
					$result=sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=? and user_id=?",$playlist_id,$user_id);
				} else {
					$result=sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and playlist_id=? and user_id=?",$playlist_id,$user_id);
				}
				if (mr2rows($result)>0)
				{
					$data_temp=mr2array_single($result);
					if (count($data_temp)>=1)
					{
						if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']!='')
						{
							$data_temp['view_page_url']="$config[project_url]/".str_replace("%ID%",$data_temp['playlist_id'],str_replace("%DIR%",$data_temp['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
						}

						$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_playlists on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_playlists.category_id where $database_selectors[where_categories] and playlist_id=?",$data_temp["playlist_id"]));
						foreach ($data_temp['categories'] as $v)
						{
							$data_temp['categories_as_string'].=$v['title'].", ";
						}
						$data_temp['categories_as_string']=rtrim($data_temp['categories_as_string'],", ");

						$storage[$object_id]['playlist_id']=$playlist_id;
						$storage[$object_id]['playlist']=$data_temp['title'];
						$storage[$object_id]['playlist_info']=$data_temp;
						$smarty->assign("playlist_id",$playlist_id);
						$smarty->assign("playlist",$data_temp['title']);
						$smarty->assign("playlist_info",$data_temp);
					}
				} else {
					return 'status_404';
				}
			}
		}

		if ($_REQUEST['countryId'])
		{
			$user_country=mr2array_list(sql("select user_id from $config[tables_prefix]users where country_id=$_REQUEST[countryId]"));
			$user_country_ids_str=implode(",",$user_country);

			$where.=" and $config[tables_prefix]videos.user_id in ($user_country_ids_str)";

			$smarty->assign("countryId",$_REQUEST['countryId']);

		}


		//// favoriate video sort
		$sort_by=trim(strtolower($_REQUEST['by']));

		if ($sort_by) {
			$metadata=custom_list_videosMetaData();
			foreach ($metadata as $res)
			{
				if (strpos($res['type'],"SORTING")!==false)
				{
					preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
					$sorting_available=explode(",",$temp[1]);
					break;
				}
			}
			$sorting_available[]="rand()";
	
			if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
			if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
			$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	
			if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
			//if ($sort_by_clear=='') {$sort_by_clear="rating_today";}
	
			$storage[$object_id]['sort_by']=$sort_by_clear;
			$smarty->assign("sort_by",$sort_by_clear);
	
			$rotator_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));
	
			
			if ($internal_query_enabled==1)
			{
				if ($sort_by_clear=='rating_today')
				{
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='rating_week') {
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='rating_month') {
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='rating') {
					$sort_by="rating/rating_amount desc, rating_amount desc";
				} else if ($sort_by_clear == 'your_rating') {
					$sort_by="(select rating from $config[tables_prefix]user_rating_history where object_id=$config[tables_prefix]videos.video_id and user_id=$_SESSION[user_id] ORDER BY added_date desc LIMIT 1) desc";
					// var_dump($sort_by); die;
				} elseif ($sort_by_clear=='video_viewed_today' || $sort_by_clear=='viewed_today') {
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='video_viewed_week' || $sort_by_clear=='viewed_week') {
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='video_viewed_month' || $sort_by_clear=='viewed_month') {
					$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
					$date_to=date("Y-m-d");
					$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
				} elseif ($sort_by_clear=='video_viewed' || $sort_by_clear=='viewed') {
					$sort_by="video_viewed desc";
				} 
			}
			///
		}

		

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id and $config[tables_prefix]fav_videos.fav_type=$fav_type and $config[tables_prefix]fav_videos.playlist_id=$playlist_id $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$videos_selector=str_replace("user_id","$config[tables_prefix]videos.user_id",str_replace("added_date","$config[tables_prefix]videos.added_date",$database_selectors['videos']));
		if ($sort_by) {
			$sql = "SELECT $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id and $config[tables_prefix]fav_videos.fav_type=$fav_type and $config[tables_prefix]fav_videos.playlist_id=$playlist_id $where order by ".$sort_by." LIMIT $from, $block_config[items_per_page]";
			// var_dump($sql); die;
			$data=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id and $config[tables_prefix]fav_videos.fav_type=$fav_type and $config[tables_prefix]fav_videos.playlist_id=$playlist_id $where order by ".$sort_by." LIMIT $from, $block_config[items_per_page]"));
		} else {
			$data=mr2array(sql("SELECT $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id and $config[tables_prefix]fav_videos.fav_type=$fav_type and $config[tables_prefix]fav_videos.playlist_id=$playlist_id $where order by $config[tables_prefix]fav_videos.playlist_sort_id asc, $config[tables_prefix]fav_videos.added_date desc LIMIT $from, $block_config[items_per_page]"));
		}
		


		$data_country=mr2array_list(sql("select country_id from $config[tables_prefix]users where user_id in (select DISTINCT user_id from $config[tables_prefix]videos where video_id in (select video_id from $config[tables_prefix]fav_videos where fav_type=$fav_type and user_id=$user_id))"));

		$country_ids_str=implode(",",$data_country);

		$smarty->assign("data_country",$data_country);


		foreach ($data as $k=>$v)
		{ 
			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['time_passed_from_adding_to_fav']=get_time_passed($data[$k]['added2fav_date']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			$result_rating=sql_pr("select rating from $config[tables_prefix]user_rating_history where user_id=$_SESSION[user_id] and type=1 and object_id=? order by added_date desc limit 1",$data[$k]['video_id']);
			if (mr2rows($result_rating)==0) {
				$data[$k]['user_rating']="N/A";
			} else {
				$data[$k]['user_rating']=mr2number($result_rating); 
			}

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
				$data[$k]['user']['country_code']=mr2array_single(sql_pr("select * from $config[tables_prefix]list_countries where country_id=".$data[$k]['user']['country_id']))['country_code'];
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		$storage[$object_id]['mode_favourites']=1;
		$storage[$object_id]['fav_type']=$fav_type;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_favourites",1);
		$smarty->assign("fav_type",$fav_type);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	} elseif (isset($block_config['mode_uploaded']))
	{
		$my_mode_uploaded=0;
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
			$my_mode_uploaded=1;
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

		if ($my_mode_uploaded==1)
		{
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and user_id=$user_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and user_id=$user_id $where order by added_date desc LIMIT $from, $block_config[items_per_page]"));

			$uploaded_summary=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and user_id=$user_id group by is_private order by is_private desc"));
		} else {
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=$user_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=$user_id $where order by $database_selectors[generic_post_date_selector] desc, video_id desc LIMIT $from, $block_config[items_per_page]"));

			$uploaded_summary=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=$user_id group by is_private order by is_private desc"));
		}

		$temp_summary=array();
		$temp_total=0;
		foreach ($uploaded_summary as $summary_item)
		{
			$temp_summary[$summary_item['is_private']]=$summary_item;
			$temp_total+=$summary_item["amount"];
		}
		$smarty->assign("uploaded_summary",$temp_summary);
		$smarty->assign("uploaded_summary_total",$temp_total);
		$storage[$object_id]["uploaded_summary"]=$temp_summary;
		$storage[$object_id]["uploaded_summary_total"]=$temp_total;
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			if (in_array($data[$k]['status_id'],array(0,1)))
			{
				$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";
			}
		}

		$storage[$object_id]['mode_uploaded']=1;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_uploaded",1);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	} elseif (isset($block_config['mode_dvd']))
	{
		$my_mode_dvd=0;
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
			$my_mode_dvd=1;
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

		$dvd_id=intval($_REQUEST[$block_config['var_dvd_id']]);

		$result=sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where dvd_id=? and user_id=?",$dvd_id,$user_id);
		if (mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			if (count($data_temp)>=1)
			{
				if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']!='')
				{
					$data_temp['view_page_url']="$config[project_url]/".str_replace("%ID%",$data_temp['dvd_id'],str_replace("%DIR%",$data_temp['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
				}

				$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_dvds on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_dvds.category_id where $database_selectors[where_categories] and dvd_id=?",$data_temp["dvd_id"]));
				foreach ($data_temp['categories'] as $v)
				{
					$data_temp['categories_as_string'].=$v['title'].", ";
				}
				$data_temp['categories_as_string']=rtrim($data_temp['categories_as_string'],", ");

				$storage[$object_id]['dvd_id']=$dvd_id;
				$storage[$object_id]['dvd']=$data_temp['title'];
				$storage[$object_id]['dvd_info']=$data_temp;
				$smarty->assign("dvd_id",$dvd_id);
				$smarty->assign("dvd",$data_temp['title']);
				$smarty->assign("dvd_info",$data_temp);
			}
		} else {
			return 'status_404';
		}

		if ($my_mode_dvd==1)
		{
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and $config[tables_prefix]videos.dvd_id=$dvd_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and $config[tables_prefix]videos.dvd_id=$dvd_id $where order by $config[tables_prefix]videos.dvd_sort_id asc, $config[tables_prefix]videos.post_date desc LIMIT $from, $block_config[items_per_page]"));
		} else {
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] and $config[tables_prefix]videos.dvd_id=$dvd_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and $config[tables_prefix]videos.dvd_id=$dvd_id $where order by $config[tables_prefix]videos.dvd_sort_id asc, $config[tables_prefix]videos.post_date desc LIMIT $from, $block_config[items_per_page]"));
		}
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			if (in_array($data[$k]['status_id'],array(0,1)))
			{
				$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";
			}
		}

		$storage[$object_id]['mode_dvd']=1;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_dvd",1);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	} elseif (isset($block_config['mode_subscribed']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
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
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
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

		$where_subscribed_users="";
		$where_subscribed_dvds="";
		$where_subscribed_cs="";
		$where_subscribed_models="";
		$where_subscribed_categories="";
		$where_subscribed_playlists="";
		$subscriptions=mr2array(sql_pr("select subscribed_object_id, subscribed_type_id from $config[tables_prefix]users_subscriptions where user_id=?",$user_id));
		foreach ($subscriptions as $subscription)
		{
			if ($subscription['subscribed_type_id']==1)
			{
				$where_subscribed_users.=",$subscription[subscribed_object_id]";
			} elseif ($subscription['subscribed_type_id']==3)
			{
				$where_subscribed_cs.=",$subscription[subscribed_object_id]";
			} elseif ($subscription['subscribed_type_id']==4)
			{
				$where_subscribed_models.=",$subscription[subscribed_object_id]";
			} elseif ($subscription['subscribed_type_id']==5)
			{
				$where_subscribed_dvds.=",$subscription[subscribed_object_id]";
			} elseif ($subscription['subscribed_type_id']==6)
			{
				$where_subscribed_categories.=",$subscription[subscribed_object_id]";
			} elseif ($subscription['subscribed_type_id']==13)
			{
				$where_subscribed_playlists.=",$subscription[subscribed_object_id]";
			}
		}

		if ($where_subscribed_users<>'')
		{
			$where_subscribed_users="user_id in (-1$where_subscribed_users)";
		} else {
			$where_subscribed_users="1=0";
		}
		if ($where_subscribed_cs<>'')
		{
			$where_subscribed_cs="content_source_id in (-1$where_subscribed_cs)";
		} else {
			$where_subscribed_cs="1=0";
		}
		if ($where_subscribed_models<>'')
		{
			$where_subscribed_models="video_id in (select video_id from $config[tables_prefix]models_videos where model_id in(-1$where_subscribed_models))";
		} else {
			$where_subscribed_models="1=0";
		}
		if ($where_subscribed_dvds<>'')
		{
			$where_subscribed_dvds="dvd_id in (-1$where_subscribed_dvds)";
		} else {
			$where_subscribed_dvds="1=0";
		}
		if ($where_subscribed_categories<>'')
		{
			$where_subscribed_categories="video_id in (select video_id from $config[tables_prefix]categories_videos where category_id in(-1$where_subscribed_categories))";
		} else {
			$where_subscribed_categories="1=0";
		}
		if ($where_subscribed_playlists<>'')
		{
			$where_subscribed_playlists="video_id in (select video_id from $config[tables_prefix]fav_videos inner join $config[tables_prefix]playlists on $config[tables_prefix]fav_videos.playlist_id=$config[tables_prefix]playlists.playlist_id where $config[tables_prefix]fav_videos.playlist_id in(-1$where_subscribed_playlists) and $database_selectors[where_playlists])";
		} else {
			$where_subscribed_playlists="1=0";
		}
		$where_subscribed="and ($where_subscribed_users or $where_subscribed_cs or $where_subscribed_models or $where_subscribed_dvds or $where_subscribed_categories or $where_subscribed_playlists)";

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_subscribed $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$data=mr2array(sql("SELECT $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] $where_subscribed $where order by $database_selectors[generic_post_date_selector] desc, video_id desc LIMIT $from, $block_config[items_per_page]"));
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		$storage[$object_id]['mode_subscribed']=1;
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];

		$smarty->assign("mode_subscribed",1);
		$smarty->assign("total_count",$total_count);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("showing_from",$from);
		$smarty->assign("data",$data);

		if (isset($block_config['var_from']))
		{
			$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
		}
		return 1;
	}

	$join_tables=array();
	if (intval($block_config['mode_related'])>0 || (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0))
	{
		//1 - cs
		//2 - tags
		//3 - category
		//4 - model
		//5 - dvd / channel
		//6-7 - title
		//8 - user

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$result=null;
		if (isset($block_config['var_video_id']) && intval($_REQUEST[$block_config['var_video_id']])>0)
		{
			$result=sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where video_id=?",intval($_REQUEST[$block_config['var_video_id']]));
		} elseif (trim($_REQUEST[$block_config['var_video_dir']])<>'')
		{
			$result=sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_video_dir']]),trim($_REQUEST[$block_config['var_video_dir']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$mode_related_name='';
			$video_id=intval($data_temp["video_id"]);

			$where.=" and $config[tables_prefix]videos.video_id<>$video_id";
			if ($mode_related==1)
			{
				$mode_related_name='content_sources';

				$content_source_id=intval($data_temp["content_source_id"]);
				$where.=" and content_source_id=$content_source_id";
			} elseif ($mode_related==2)
			{
				$mode_related_name='tags';

				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags_videos where video_id=?",$video_id));
				if (count($tag_ids)>0)
				{
					$tag_ids=implode(",",$tag_ids);
					$join_tables[]="select distinct video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)";
				}
			} elseif ($mode_related==3)
			{
				$mode_related_name='categories';

				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_videos where video_id=?",$video_id));
				if (count($category_ids)>0 && isset($block_config['mode_related_category_group_id']))
				{
					$category_ids=implode(',',$category_ids);
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_id in ($category_ids) and (category_group_id=? or category_group_id in (select category_group_id from $config[tables_prefix]categories_groups where external_id=?))",intval($block_config['mode_related_category_group_id']),trim($block_config['mode_related_category_group_id'])));
				}
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$join_tables[]="select distinct video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids)";
				}
			} elseif ($mode_related==4)
			{
				$mode_related_name='models';

				$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_videos where video_id=?",$video_id));
				if (count($model_ids)>0 && isset($block_config['mode_related_model_group_id']))
				{
					$model_ids=implode(',',$model_ids);
					$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_id in ($model_ids) and (model_group_id=? or model_group_id in (select model_group_id from $config[tables_prefix]models_groups where external_id=?))",intval($block_config['mode_related_model_group_id']),trim($block_config['mode_related_model_group_id'])));
				}
				if (count($model_ids)>0)
				{
					$model_ids=implode(",",$model_ids);
					$join_tables[]="select distinct video_id from $config[tables_prefix]models_videos where model_id in ($model_ids)";
				}
			} elseif ($mode_related==5)
			{
				$mode_related_name='dvd';

				$dvd_id=intval($data_temp["dvd_id"]);
				$where.=" and dvd_id=$dvd_id";
			} elseif ($mode_related==6 || $mode_related==7)
			{
				$mode_related_name='title';

				$title=sql_escape($data_temp["title"]);

				$search_modifier='';
				if ($mode_related==7)
				{
					$search_modifier='WITH QUERY EXPANSION';
				}
				$where.=" and MATCH($database_selectors[locale_field_title]) AGAINST('$title' $search_modifier)";
				$sort_by_relevance="MATCH($database_selectors[locale_field_title]) AGAINST('$title' $search_modifier) desc";
			} elseif ($mode_related==8)
			{
				$mode_related_name='user';

				$user_id=intval($data_temp["user_id"]);
				$where.=" and user_id=$user_id";
			}
			$storage[$object_id]['list_type']="related";
			$storage[$object_id]['related_mode']=$mode_related;
			$storage[$object_id]['related_mode_name']=$mode_related_name;
			$smarty->assign('list_type',"related");
			$smarty->assign('related_mode',$mode_related);
			$smarty->assign('related_mode_name',$mode_related_name);
		}
	} elseif (isset($block_config['mode_connected_album']))
	{
		$connected_video_id=0;
		if (isset($block_config['var_connected_album_id']) && intval($_REQUEST[$block_config['var_connected_album_id']])>0)
		{
			$connected_video_id=mr2number(sql_pr("select connected_video_id from $config[tables_prefix]albums where album_id=?",intval($_REQUEST[$block_config['var_connected_album_id']])));
		} elseif (trim($_REQUEST[$block_config['var_connected_album_dir']])<>'')
		{
			$connected_video_id=mr2number(sql_pr("select connected_video_id from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_connected_album_dir']]),trim($_REQUEST[$block_config['var_connected_album_dir']])));
		}
		$where.=" and $config[tables_prefix]videos.video_id=$connected_video_id";
	}

	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$q=sql_escape(trim($_REQUEST[$block_config['var_title_section']]));
		$where.=" and $database_selectors[locale_field_title] like '$q%'";

		$storage[$object_id]['list_type']="title_section";
		$storage[$object_id]['title_section']=trim($_REQUEST[$block_config['var_title_section']]);
		$smarty->assign('list_type',"title_section");
		$smarty->assign('title_section',trim($_REQUEST[$block_config['var_title_section']]));
	}

	if (isset($block_config['var_user_id']) && trim($_REQUEST[$block_config['var_user_id']])<>'')
	{
		$where.=" and $config[tables_prefix]videos.user_id=".intval($_REQUEST[$block_config['var_user_id']]);
	}

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'dvd',                  'plural'=>'dvds',                   'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>false, 'where_single'=>$database_selectors['where_dvds_active_disabled'],                   'where_plural'=>$database_selectors['where_dvds'],                    'base_files_url'=>$config['content_url_dvds'],                'link_pattern'=>'WEBSITE_LINK_PATTERN_DVD',       'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'dvd_group',            'plural'=>'dvds_groups',            'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_dvds_groups_active_disabled'],            'where_plural'=>$database_selectors['where_dvds_groups'],             'base_files_url'=>$config['content_url_dvds'].'/groups',      'link_pattern'=>'WEBSITE_LINK_PATTERN_DVD_GROUP', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'content_source',       'plural'=>'content_sources',        'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_active_disabled'],        'where_plural'=>$database_selectors['where_content_sources'],         'base_files_url'=>$config['content_url_content_sources'],     'link_pattern'=>'WEBSITE_LINK_PATTERN_CS',        'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'content_source_group', 'plural'=>'content_sources_groups', 'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_groups_active_disabled'], 'where_plural'=>$database_selectors['where_content_sources_groups']);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',                'plural'=>'models',                 'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_models_active_disabled'],                 'where_plural'=>$database_selectors['where_models'],                  'base_files_url'=>$config['content_url_models'],              'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL',     'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',          'plural'=>'models_groups',          'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_models_groups_active_disabled'],          'where_plural'=>$database_selectors['where_models_groups'],           'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',             'plural'=>'categories',             'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],             'where_plural'=>$database_selectors['where_categories'],              'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group',       'plural'=>'categories_groups',      'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'],      'where_plural'=>$database_selectors['where_categories_groups'],       'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',                  'plural'=>'tags',                   'title'=>'tag',   'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],                   'where_plural'=>$database_selectors['where_tags']);

	$dynamic_filters_types=array();
	foreach ($dynamic_filters as $df)
	{
		$df_id="{$df['single']}_id";
		$df_selector=$database_selectors[$df['plural']];
		$df_selector_locale_dir=$database_selectors["where_locale_{$df['dir']}"];
		$df_table="$config[tables_prefix]{$df['plural']}";
		$df_join_table="";
		if ($df['join_table'])
		{
			$df_join_table="$config[tables_prefix]{$df['plural']}_videos";
		}

		$df_basetable="";
		$df_basetable_id="";
		$df_join_basetable="";
		if ($df['is_group'])
		{
			$df_basetable=str_replace("_groups","",$df_table);
			$df_basetable_id=str_replace("_group","",$df_id);
			$df_join_basetable=str_replace("_groups","",$df_join_table);
		}

		$df_var_id="var_{$df['single']}_id";
		$df_var_ids="var_{$df['single']}_ids";
		$df_var_dir="var_{$df['single']}_dir";
		if (isset($block_config[$df_var_ids]) && $_REQUEST[$block_config[$df_var_ids]]<>'')
		{
			$df_ids_value=$_REQUEST[$block_config[$df_var_ids]];
			$df_where_plural=$df['where_plural'];
			if (!$df_where_plural)
			{
				$df_where_plural='1=1';
			}
			if (strpos($df_ids_value,"|")!==false)
			{
				$ids_groups=explode("|",$df_ids_value);
				$df_ids_value=array(0);
				foreach ($ids_groups as $ids_group)
				{
					$ids_group=array_map("intval",explode(",",trim($ids_group,"() ")));
					if (count($ids_group)>0)
					{
						$df_ids_value=array_merge($df_ids_value,$ids_group);
						$ids_group=implode(',',$ids_group);
						if ($df_join_table!='')
						{
							if ($df['is_group'])
							{
								$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct video_id from $df_join_table where $df_id in ($ids_group)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$where.=" and $df_id in ($ids_group)";
							}
						}
					}
				}
				$df_ids_value=implode(',',$df_ids_value);

				$df_objects=mr2array(sql_pr("select $df_selector from $df_table where $df_where_plural and $df_id in ($df_ids_value)"));
				if ($df['base_files_url']!='' || $df['link_pattern']!='')
				{
					foreach ($df_objects as $k=>$v)
					{
						if ($df['base_files_url']!='')
						{
							$df_objects[$k]['base_files_url']=$df['base_files_url'].'/'.$v[$df_id];
						}
						if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
						{
							$pattern=str_replace("%ID%",$v[$df_id],str_replace("%DIR%",$v[$df['dir']],$website_ui_data[$df['link_pattern']]));
							$df_objects[$k]['view_page_url']="$config[project_url]/$pattern";
						}
					}
				}

				$storage[$object_id]["list_type"]="multi_{$df['plural']}";
				$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
				$smarty->assign("list_type","multi_{$df['plural']}");
				$smarty->assign("{$df['plural']}_info",$df_objects);
				$dynamic_filters_types[]="multi_{$df['plural']}";
			} else {
				$df_all_met=false;
				$df_ids_value=explode(",",trim($df_ids_value,"() "));
				if (in_array('all',$df_ids_value))
				{
					$df_all_met=true;
				}
				$df_ids_value=array_map("intval",$df_ids_value);
				if (count($df_ids_value)>0)
				{
					if ($df_all_met)
					{
						foreach ($df_ids_value as $df_ids_value_id)
						{
							if ($df_ids_value_id>0)
							{
								if ($df_join_table)
								{
									if ($df['is_group'])
									{
										$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct video_id from $df_join_table where $df_id=$df_ids_value_id";
									}
								} else {
									if ($df['is_group'])
									{
										$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$where.=" and $df_id=$df_ids_value_id";
									}
								}
							}
						}
						$df_ids_value=implode(',',$df_ids_value);
					} else {
						$df_ids_value=implode(',',$df_ids_value);
						if ($df_join_table)
						{
							if ($df['is_group'])
							{
								$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct video_id from $df_join_table where $df_id in ($df_ids_value)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$where.=" and $df_id in ($df_ids_value)";
							}
						}
					}

					$df_objects=mr2array(sql_pr("select $df_selector from $df_table where $df_where_plural and $df_id in ($df_ids_value)"));
					if ($df['base_files_url']!='' || $df['link_pattern']!='')
					{
						foreach ($df_objects as $k=>$v)
						{
							if ($df['base_files_url']!='')
							{
								$df_objects[$k]['base_files_url']=$df['base_files_url'].'/'.$v[$df_id];
							}
							if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
							{
								$pattern=str_replace("%ID%",$v[$df_id],str_replace("%DIR%",$v[$df['dir']],$website_ui_data[$df['link_pattern']]));
								$df_objects[$k]['view_page_url']="$config[project_url]/$pattern";
							}
						}
					}

					$storage[$object_id]["list_type"]="multi_{$df['plural']}";
					$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
					$smarty->assign("list_type","multi_{$df['plural']}");
					$smarty->assign("{$df['plural']}_info",$df_objects);
					$dynamic_filters_types[]="multi_{$df['plural']}";
				}
			}
		} elseif ((isset($block_config[$df_var_dir]) && $_REQUEST[$block_config[$df_var_dir]]!='') || (isset($block_config[$df_var_id]) && $_REQUEST[$block_config[$df_var_id]]!=''))
		{
			$df_where_single=$df['where_single'];
			if (!$df_where_single)
			{
				$df_where_single='1=1';
			}

			if ($_REQUEST[$block_config[$df_var_dir]]!='')
			{
				$result=sql_pr("select $df_selector from $df_table where $df_where_single and ({$df['dir']}=? or $df_selector_locale_dir)",trim($_REQUEST[$block_config[$df_var_dir]]),trim($_REQUEST[$block_config[$df_var_dir]]));
			} else {
				$result=sql_pr("select $df_selector from $df_table where $df_where_single and $df_id=?",intval($_REQUEST[$block_config[$df_var_id]]));
			}

			if (isset($result) && mr2rows($result)>0)
			{
				$data_temp=mr2array_single($result);
				$df_object_id=$data_temp[$df_id];

				if ($df['base_files_url']!='')
				{
					$data_temp['base_files_url']=$df['base_files_url'].'/'.$data_temp[$df_id];
				}
				if ($df['link_pattern']!='' && $website_ui_data[$df['link_pattern']]!='')
				{
					$pattern=str_replace("%ID%",$data_temp[$df_id],str_replace("%DIR%",$data_temp[$df['dir']],$website_ui_data[$df['link_pattern']]));
					$data_temp['view_page_url']="$config[project_url]/$pattern";
				}
				if ($df['supports_grouping'] && $data_temp["{$df['single']}_group_id"]>0)
				{
					$data_temp["{$df['single']}_group"]=mr2array_single(sql_pr("select {$database_selectors["$df[plural]_groups"]} from $config[tables_prefix]$df[plural]_groups where {$database_selectors["where_$df[plural]_groups"]} and {$df['single']}_group_id=?",$data_temp["{$df['single']}_group_id"]));
				}
				if ($df['sub_categories'])
				{
					$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_{$df['plural']} on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_{$df['plural']}.category_id where $database_selectors[where_categories] and $df_id=? order by id asc",$data_temp[$df_id]));
					foreach ($data_temp['categories'] as $v)
					{
						$data_temp['categories_as_string'].=$v['title'].", ";
					}
					$data_temp['categories_as_string']=rtrim($data_temp['categories_as_string'],", ");
				}
				if ($df['sub_tags'])
				{
					$data_temp['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_{$df['plural']} on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_{$df['plural']}.tag_id where $database_selectors[where_tags] and $df_id=? order by id asc",$data_temp[$df_id]));
					foreach ($data_temp['tags'] as $v)
					{
						$data_temp['tags_as_string'].=$v['tag'].", ";
					}
					$data_temp['tags_as_string']=rtrim($data_temp['tags_as_string'],", ");
				}
				if ($data_temp['country_id']>0)
				{
					$data_temp['country']=$list_countries['name'][$data_temp['country_id']];
				}

				$storage[$object_id]["list_type"]="{$df['plural']}";
				$storage[$object_id]["{$df['single']}"]=$data_temp[$df['title']];
				$storage[$object_id]["{$df['single']}_info"]=$data_temp;
				$smarty->assign("list_type","{$df['plural']}");
				$smarty->assign("{$df['single']}",$data_temp[$df['title']]);
				$smarty->assign("{$df['single']}_info",$data_temp);
				$dynamic_filters_types[]="{$df['plural']}";

				if ($df_join_table)
				{
					if ($df['is_group'])
					{
						$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct video_id from $df_join_table where $df_id=$df_object_id";
					}
				} else {
					if ($df['is_group'])
					{
						$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$where.=" and $df_id=$df_object_id";
					}
				}
			} else
			{
				return 'status_404';
			}
		}
	}

	for ($i=1;$i<=3;$i++)
	{
		if (isset($block_config["var_custom_flag$i"]) && trim($_REQUEST[$block_config["var_custom_flag$i"]])!='')
		{
			if (strpos(trim($_REQUEST[$block_config["var_custom_flag$i"]]),',')!==false)
			{
				$where.=" and af_custom$i in (".implode(",",array_map("intval",explode(",",trim($_REQUEST[$block_config["var_custom_flag$i"]])))).")";
			} else {
				$where.=" and af_custom$i=".intval($_REQUEST[$block_config["var_custom_flag$i"]]);
			}
		}
	}



	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		$q=trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]),false));
		$q=trim(str_replace('[dash]','-',str_replace('-',' ',str_replace('--','[dash]',str_replace('?','',$q)))));

		$unescaped_q=$q;
		if ($q=='')
		{
			$where.=" and 1=0";
		} else
		{
			if (is_file("$config[project_path]/admin/plugins/external_search/external_search.php"))
			{
				require_once("$config[project_path]/admin/plugins/external_search/external_search.php");
				if (function_exists('external_searchGetOptions'))
				{
					$external_search_options=external_searchGetOptions();
					if (is_array($external_search_options))
					{
						$external_search_enabled=intval($external_search_options['enable_external_search']);
						$external_search_enabled_condition=intval($external_search_options['enable_external_search_condition']);
						$external_search_display=intval($external_search_options['display_results']);
						$external_search_from=intval($from);
						$external_search_text=trim($q);
						if ($external_search_enabled==1 && $external_search_display==0)
						{
							$internal_query_enabled=0;
						}
					}
				}
			}

			$where_temp_str='';
			if ($internal_query_enabled==1)
			{
				$escaped_q=sql_escape($q);
				if (isset($block_config['enable_search_on_categories']))
				{
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where $database_selectors[locale_field_title]=? or synonyms like '%$escaped_q%'",$q));
					if (count($category_ids)>0)
					{
						$category_ids=implode(',',array_map('intval',$category_ids));
						$where_temp_str.=" or $config[tables_prefix]videos.video_id in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
					}
				}

				if (isset($block_config['enable_search_on_tags']))
				{
					$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags where $database_selectors[locale_field_tag]=? or synonyms like '%$escaped_q%'",$q));
					if (count($tag_ids)>0)
					{
						$tag_ids=implode(',',array_map('intval',$tag_ids));
						$where_temp_str.=" or $config[tables_prefix]videos.video_id in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids))";
					}
				}

				if (isset($block_config['enable_search_on_cs']))
				{
					$content_source_id=mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where $database_selectors[locale_field_title]=?",$q));
					if ($content_source_id>0)
					{
						$where_temp_str.=" or content_source_id = '$content_source_id' ";
					}
				}

				if (isset($block_config['enable_search_on_models']))
				{
					$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where $database_selectors[locale_field_title]=? or alias like '%$escaped_q%'",$q));
					if (count($model_ids)>0)
					{
						$model_ids=implode(',',array_map('intval',$model_ids));
						$where_temp_str.=" or $config[tables_prefix]videos.video_id in (select video_id from $config[tables_prefix]models_videos where model_id in ($model_ids))";
					}
				}

				if (isset($block_config['enable_search_on_dvds']))
				{
					$dvd_id=mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where $database_selectors[locale_field_title]=?",$q));
					if ($dvd_id>0)
					{
						$where_temp_str.=" or dvd_id = '$dvd_id' ";
					}
				}
			}

			$q=sql_escape($q);
			$search_scope=intval($block_config['search_scope']);
			if ($search_scope==2)
			{
				$where2='1=0';
				if (isset($block_config['enable_search_on_custom_fields']))
				{
					$where2.= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%'";
				}
				$where.=" and (($where2) $where_temp_str)";
			} else
			{
				if ($block_config['search_method']==3 || $block_config['search_method']==4 || $block_config['search_method']==5)
				{
					$search_modifier='';
					if ($block_config['search_method']==4)
					{
						$search_modifier='IN BOOLEAN MODE';
					} elseif ($block_config['search_method']==5)
					{
						$search_modifier='WITH QUERY EXPANSION';
					}
					if ($search_scope==0)
					{
						$where2="MATCH ($database_selectors[locale_field_title],$database_selectors[locale_field_description]) AGAINST ('$q' $search_modifier)";
						$sort_by_relevance="MATCH ($database_selectors[locale_field_title],$database_selectors[locale_field_description]) AGAINST ('$q' $search_modifier) desc";
					} else {
						$where2="MATCH ($database_selectors[locale_field_title]) AGAINST ('$q' $search_modifier)";
						$sort_by_relevance="MATCH ($database_selectors[locale_field_title]) AGAINST ('$q' $search_modifier) desc";
					}
					if (isset($block_config['enable_search_on_custom_fields']))
					{
						$where2.= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%'";
					}
					$where.=" and (($where2) $where_temp_str)";

					$storage[$object_id]['is_search_supports_relevance']="1";
					$smarty->assign('is_search_supports_relevance',"1");
				} else if ($block_config['search_method']==2)
				{
					$where2='';
					$temp=explode(" ",$q);
					foreach ($temp as $temp_value)
					{
						$length=strlen($temp_value);
						if (function_exists('mb_detect_encoding'))
						{
							$length=mb_strlen($temp_value,mb_detect_encoding($temp_value));
						}
						if ($length>2)
						{
							if ($search_scope==0)
							{
								$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or $database_selectors[locale_field_description] like '%$temp_value%'";
							} else {
								$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%'";
							}
							if (isset($block_config['enable_search_on_custom_fields']))
							{
								$where2.= " or custom1 like '%$temp_value%' or custom2 like '%$temp_value%' or custom3 like '%$temp_value%'";
							}
						}
					}
					if ($where2<>'')
					{
						$where2=substr($where2,4);
					} else {
						if ($search_scope==0)
						{
							$where2.="$database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%'";
						} else {
							$where2.="$database_selectors[locale_field_title] like '%$q%'";
						}
						if (isset($block_config['enable_search_on_custom_fields']))
						{
							$where2.= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%'";
						}
					}
					$where.=" and (($where2) $where_temp_str)";
				} else
				{
					$where2='';
					if (isset($block_config['enable_search_on_custom_fields']))
					{
						$where2.= " or custom1 like '%$q%' or custom2 like '%$q%' or custom3 like '%$q%'";
					}
					if ($search_scope==0)
					{
						$where.=" and (($database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%' $where2) $where_temp_str)";
					} else {
						$where.=" and (($database_selectors[locale_field_title] like '%$q%' $where2) $where_temp_str)";
					}
				}
			}
		}

		$storage[$object_id]['list_type']="search";
		$storage[$object_id]['search_keyword']=$unescaped_q;
		$storage[$object_id]['url_prefix']="?$block_config[var_search]=$unescaped_q&";
		$smarty->assign('list_type',"search");
		$smarty->assign('search_keyword',$unescaped_q);
	}

	if ($block_config['skip_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['skip_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where.=" and $config[tables_prefix]videos.video_id not in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]videos.video_id not in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['skip_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$where.=" and $config[tables_prefix]videos.video_id not in (select video_id from $config[tables_prefix]models_videos where model_id in ($model_ids)) ";
		}
	}

	if ($block_config['show_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['show_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$join_tables[]="select distinct video_id from $config[tables_prefix]models_videos where model_id in ($model_ids)";
		}
	}

	if ($block_config['skip_content_sources']<>'' && !in_array('content_sources',$dynamic_filters_types) && !in_array('multi_content_sources',$dynamic_filters_types) && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$cs_ids=array_map("intval",explode(",",$block_config['skip_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where.=" and content_source_id not in ($cs_ids) ";
		}
	}

	if ($block_config['show_content_sources']<>'' && !in_array('content_sources',$dynamic_filters_types) && !in_array('multi_content_sources',$dynamic_filters_types) && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$cs_ids=array_map("intval",explode(",",$block_config['show_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where.=" and content_source_id in ($cs_ids) ";
		}
	}

	if ($block_config['skip_dvds']<>'' && !in_array('dvds',$dynamic_filters_types) && !in_array('multi_dvds',$dynamic_filters_types) && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_ids=array_map("intval",explode(",",$block_config['skip_dvds']));
		if (count($dvd_ids)>0)
		{
			$dvd_ids=implode(",",$dvd_ids);
			$where.=" and dvd_id not in ($dvd_ids) ";
		}
	}

	if ($block_config['show_dvds']<>'' && !in_array('dvds',$dynamic_filters_types) && !in_array('multi_dvds',$dynamic_filters_types) && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_ids=array_map("intval",explode(",",$block_config['show_dvds']));
		if (count($dvd_ids)>0)
		{
			$dvd_ids=implode(",",$dvd_ids);
			$where.=" and dvd_id in ($dvd_ids) ";
		}
	}

	if ($block_config['skip_users']<>'')
	{
		$user_ids=array_map("intval",explode(",",$block_config['skip_users']));
		if (count($user_ids)>0)
		{
			$user_ids=implode(",",$user_ids);
			$where.=" and user_id not in ($user_ids) ";
		}
	}

	if ($block_config['show_users']<>'')
	{
		$user_ids=array_map("intval",explode(",",$block_config['show_users']));
		if (count($user_ids)>0)
		{
			$user_ids=implode(",",$user_ids);
			$where.=" and user_id in ($user_ids) ";
		}
	}

	if (isset($block_config['days_passed_from']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_from'])+1,date("Y")));
		$where.=" and $database_selectors[generic_post_date_selector]<='$date_passed_from'";
	}
	if (isset($block_config['days_passed_to']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_to'])+1,date("Y")));
		$where.=" and $database_selectors[generic_post_date_selector]>='$date_passed_from'";
	}
	if (isset($block_config['under_rotation']))
	{
		$where.=" and rs_completed=0";
	} elseif (isset($block_config['finished_rotation']))
	{
		$where.=" and rs_completed=1";
	}
	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}
	if (isset($block_config['show_with_admin_flag']))
	{
		$flag_id=mr2number(sql_pr("select flag_id from $config[tables_prefix]flags where group_id=1 and external_id=?",$block_config['show_with_admin_flag']));
		if ($flag_id>0)
		{
			$where.=" and admin_flag_id=$flag_id";
		} else {
			$where.=" and 0=1";
		}
	}
	if (isset($block_config['skip_with_admin_flag']))
	{
		$flag_id=mr2number(sql_pr("select flag_id from $config[tables_prefix]flags where group_id=1 and external_id=?",$block_config['skip_with_admin_flag']));
		if ($flag_id>0)
		{
			$where.=" and admin_flag_id!=$flag_id";
		}
	}
	if (isset($block_config['show_only_from_same_country']))
	{
		$country_code=$_SERVER['GEOIP_COUNTRY_CODE'];
		if ($country_code<>'')
		{
			$country_id=intval(array_search(strtolower($country_code),$list_countries['code']));
			if ($country_id>0)
			{
				$where.=" and user_id in (select user_id from $config[tables_prefix]users where country_id=$country_id) ";
				$smarty->assign("current_country_id",$country_id);
				$smarty->assign("current_country_name",$list_countries['name'][$country_id]);
				$storage[$object_id]['current_country_id']=$country_id;
				$storage[$object_id]['current_country_name']=$list_countries['name'][$country_id];
			}
		}
	}

	if (is_array($config['advanced_filtering']))
	{
		foreach ($config['advanced_filtering'] as $advanced_filter)
		{
			if ($advanced_filter=='upload_zone')
			{
				$where.=' and af_upload_zone=0';
			}
		}
	}



	$external_search_result=array();
	$external_search_result_count=0;
	$external_search_index_from=0;
	if ($external_search_enabled>0)
	{
		if (function_exists('external_searchDoSearch'))
		{
			$external_search_result_temp=external_searchDoSearch($external_search_text,($internal_query_enabled==0 ? $external_search_from : 0),($internal_query_enabled==0 ? $block_config['items_per_page'] : 0));
			if (@count($external_search_result_temp)>0)
			{
				$external_search_result=$external_search_result_temp['data'];
				$external_search_result_count=$external_search_result_temp['total_count'];
				$external_search_index_from=$external_search_result_temp['from'];
				if ($internal_query_enabled==0)
				{
					if ($config['is_pagination_3.0']=="true")
					{
						if (($external_search_index_from>0 && ($external_search_index_from>=$external_search_result_count || $external_search_result_count==0)) || $external_search_index_from<0)
						{
							return 'status_404';
						}
					}
				}
				foreach ($external_search_result as $k=>$v)
				{
					$external_search_result[$k]['time_passed_from_adding']=get_time_passed($external_search_result[$k]['post_date']);
					$external_search_result[$k]['duration_array']=get_duration_splitted($external_search_result[$k]['duration']);
					$external_search_result[$k]['video_comments']=$external_search_result[$k]['comments_count'];

					if ($external_search_result[$k]['video_id']>0)
					{
						if (isset($block_config['show_categories_info']))
						{
							$external_search_result[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$external_search_result[$k]['video_id']." order by id asc"));
						}
						if (isset($block_config['show_tags_info']))
						{
							$external_search_result[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$external_search_result[$k]['video_id']." order by id asc"));
						}
						if (isset($block_config['show_models_info']))
						{
							$external_search_result[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$external_search_result[$k]['video_id']." order by id asc"));
							foreach ($external_search_result[$k]['models'] as $k2=>$v2)
							{
								$external_search_result[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
								if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
								{
									$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
									$external_search_result[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
								}
							}
						}
						if (isset($block_config['show_user_info']) && $external_search_result[$k]['user_id']>0)
						{
							$external_search_result[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$external_search_result[$k]['user_id']));
						}
						if (isset($block_config['show_content_source_info']) && $external_search_result[$k]['content_source_id']>0)
						{
							$external_search_result[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$external_search_result[$k]['content_source_id']));
							if ($external_search_result[$k]['content_source']['content_source_id']>0)
							{
								$external_search_result[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$external_search_result[$k]['content_source']['content_source_id'];
								if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
								{
									$pattern=str_replace("%ID%",$external_search_result[$k]['content_source']['content_source_id'],str_replace("%DIR%",$external_search_result[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
									$external_search_result[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
								}
							}
						}
						if (isset($block_config['show_dvd_info']) && $external_search_result[$k]['dvd_id']>0)
						{
							$external_search_result[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$external_search_result[$k]['dvd_id']));
							if ($external_search_result[$k]['dvd']['dvd_id']>0)
							{
								$external_search_result[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$external_search_result[$k]['dvd']['dvd_id'];
								if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
								{
									$pattern=str_replace("%ID%",$external_search_result[$k]['dvd']['dvd_id'],str_replace("%DIR%",$external_search_result[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
									$external_search_result[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
								}
							}
						}
						if (isset($block_config['show_flags_info']))
						{
							$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$external_search_result[$k]['video_id']));
							$external_search_result[$k]['flags']=array();
							foreach($flags as $flag)
							{
								$external_search_result[$k]['flags'][$flag['external_id']]=$flag['votes'];
							}
						}
						if (isset($block_config['show_comments']))
						{
							$show_comments_limit='';
							if (intval($block_config['show_comments_count'])>0)
							{
								$show_comments_limit='limit '.intval($block_config['show_comments_count']);
							}
							$external_search_result[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$external_search_result[$k]['video_id'],date("Y-m-d H:i:s")));
						}
					}
				}
			}
		}
	}


	if ($_REQUEST['countryId'])
	{
		$user_country=mr2array_list(sql("select user_id from $config[tables_prefix]users where country_id=$_REQUEST[countryId]"));
		$user_country_ids_str=implode(",",$user_country);
		$smarty->assign("countryId",$_REQUEST['countryId']);
	}

	if ($_REQUEST['userId']) {
		$where_user = ' and user_id='.$_REQUEST['userId'];
	}

	$data_country=mr2array_list(sql("select country_id from $config[tables_prefix]users where user_id in (select DISTINCT user_id from $config[tables_prefix]videos)"));
	$country_ids_str=implode(",",$data_country);
	$smarty->assign("data_country",$data_country);

	$metadata=custom_list_videosMetaData();
	foreach ($metadata as $res)
	{
		if (strpos($res['type'],"SORTING")!==false)
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));

	

	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));

	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="post_date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$rotator_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));


	
	if ($internal_query_enabled==1)
	{
		if ($sort_by_clear=='title')
		{
			$sort_by_clear="lower($database_selectors[generic_selector_title])";
			if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
		}
		$sort_by="$sort_by_clear $direction";

		if ($sort_by_clear=='rating_today')
		{
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='rating_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='rating_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='rating') {
			$sort_by="rating/rating_amount desc, rating_amount desc";
		} else if ($sort_by_clear == 'your_rating') {
			$sort_by="(select rating from $config[tables_prefix]user_rating_history where object_id=$config[tables_prefix]videos.video_id and user_id=$_SESSION[user_id] ORDER BY added_date desc LIMIT 1) desc";
			// var_dump($sort_by); die;
		} elseif ($sort_by_clear=='video_viewed_today' || $sort_by_clear=='viewed_today') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='video_viewed_week' || $sort_by_clear=='viewed_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='video_viewed_month' || $sort_by_clear=='viewed_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($sort_by_clear=='video_viewed' || $sort_by_clear=='viewed') {
			$sort_by="video_viewed desc";
		} elseif ($sort_by_clear=='ctr') {
			$sort_by="r_ctr desc";
			if (intval($rotator_params['ROTATOR_VIDEOS_CATEGORIES_ENABLE'])==1 && $storage[$object_id]['list_type']=='categories' && is_array($storage[$object_id]['category_info']))
			{
				$ctr_category_id=intval($storage[$object_id]['category_info']['category_id']);
				if ($ctr_category_id>0)
				{
					$sort_by="(select cr_ctr from $config[tables_prefix]categories_videos where video_id=$config[tables_prefix]videos.video_id and category_id=$ctr_category_id) desc";
				}
			} elseif (intval($rotator_params['ROTATOR_VIDEOS_TAGS_ENABLE'])==1 && $storage[$object_id]['list_type']=='tags' && is_array($storage[$object_id]['tag_info']))
			{
				$ctr_tag_id=intval($storage[$object_id]['tag_info']['tag_id']);
				if ($ctr_tag_id>0)
				{
					$sort_by="(select cr_ctr from $config[tables_prefix]tags_videos where video_id=$config[tables_prefix]videos.video_id and tag_id=$ctr_tag_id) desc";
				}
			}
		} else {
			if ($sort_by_clear=='post_date') {$sort_by="$database_selectors[generic_post_date_selector] $direction, $config[tables_prefix]videos.video_id $direction";} else
			if ($sort_by_clear=='post_date_and_popularity') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, video_viewed desc";} else
			if ($sort_by_clear=='post_date_and_rating') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, rating/rating_amount desc, rating_amount desc";} else
			if ($sort_by_clear=='post_date_and_duration') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, duration desc";} else
			if ($sort_by_clear=='last_time_view_date_and_popularity') {$sort_by="date(last_time_view_date) $direction, video_viewed desc";} else
			if ($sort_by_clear=='last_time_view_date_and_rating') {$sort_by="date(last_time_view_date) $direction, rating/rating_amount desc, rating_amount desc";} else
			if ($sort_by_clear=='last_time_view_date_and_duration') {$sort_by="date(last_time_view_date) $direction, duration desc";} else
			if ($sort_by_clear=='most_favourited') {$sort_by="favourites_count $direction";} else
			if ($sort_by_clear=='most_commented') {$sort_by="comments_count $direction";} else
			if ($sort_by_clear=='most_purchased') {$sort_by="purchases_count $direction";}
		}
		

		$from_clause="$config[tables_prefix]videos";
		for ($i=1;$i<=count($join_tables);$i++)
		{
			$join_table=$join_tables[$i-1];
			$from_clause.=" inner join ($join_table) table$i on table$i.video_id=$config[tables_prefix]videos.video_id";
		}
		$where_clause="$database_selectors[where_videos]";


		if (isset($block_config['mode_futures']))
		{
			$where_clause="$database_selectors[where_videos_future]";
		}

		$total_count_pseudo_rand=0;
		if ($sort_by_clear=='pseudo_rand')
		{
			$limit=$block_config['items_per_page']*10;
			$video_ids=mr2array_list(sql("select SQL_CALC_FOUND_ROWS $config[tables_prefix]videos.video_id from $from_clause where $where_clause $where order by random1 limit $limit"));
			$total_count_pseudo_rand=mr2number(sql("select FOUND_ROWS()"));
			if (count($video_ids)>$block_config['items_per_page'])
			{
				$selected_ids=array();
				for ($i=1;$i<9999;$i++)
				{
					$rnd=mt_rand(0,count($video_ids)-1);
					if (!in_array($video_ids[$rnd],$selected_ids))
					{
						$selected_ids[]=intval($video_ids[$rnd]);
						if (count($selected_ids)>=$block_config['items_per_page'])
						{
							break;
						}
					}
				}
				$where_add=implode(',',$selected_ids);
			} else {
				$where_add=implode(',',$video_ids);
			}
			if (count($video_ids)>0)
			{
				$where=" and $config[tables_prefix]videos.video_id in (0,$where_add) ";
			}
			$from=0;
			$sort_by="order by rand()";
		} else {
			$sort_by="order by $sort_by";
		}
		if ($sort_by_relevance<>'' && trim($_REQUEST[$block_config['var_sort_by']])=='')
		{
			$sort_by="order by $sort_by_relevance";
			$storage[$object_id]['sort_by']='relevance';
			$smarty->assign("sort_by",'relevance');
		}





		if (isset($block_config['var_from']))
		{

			$total_count=mr2number(sql("select count(*) from $from_clause where $where_clause $where"));
			if ($sort_by_clear=='pseudo_rand')
			{
				$total_count=$total_count_pseudo_rand;
			}
			if ($external_search_enabled>0 && $external_search_result_count>0)
			{
				if ($external_search_enabled==1 || ($external_search_enabled==2 && $total_count<$external_search_enabled_condition))
				{
					$total_count+=$external_search_result_count;
					if ($external_search_display==1)
					{
						$from-=$external_search_result_count;
						if ($from<0) {$from=0;}
					}
				}
			}
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

			$data=mr2array(sql("SELECT $database_selectors[videos] from $from_clause where $where_clause $where $where_user $sort_by LIMIT $from, $block_config[items_per_page]"));

			if ($external_search_enabled>0 && $external_search_result_count>0)
			{
				if ($external_search_enabled==1 || ($external_search_enabled==2 && $total_count<$external_search_enabled_condition))
				{
					if ($external_search_display==1)
					{
						$from=$external_search_from;
					}
				}
			}
		} else {
			$data=mr2array(sql("SELECT $database_selectors[videos] from $from_clause where $where_clause $where $sort_by LIMIT $block_config[items_per_page]"));
		}

		if ($storage[$object_id]['list_type']=="search" && $external_search_result_count==0)
		{
			$check_count=count($data);
			if (isset($total_count))
			{
				$check_count=$total_count;
			}
			if ($check_count==1)
			{
				if (isset($block_config['search_redirect_enabled']))
				{
					if ($storage[$object_id]['search_keyword']<>'' && $from==0 && (strpos(str_replace("www.","",$_SERVER['HTTP_REFERER']),str_replace("www.","",$config['project_url']))===0))
					{
						$q=$storage[$object_id]['search_keyword'];
						$date=date("Y-m-d");
						$fh=fopen("$config[project_path]/admin/data/stats/search.dat","a+");
						flock($fh,LOCK_EX);
						fwrite($fh,"$date|$q|1|0\r\n");
						fclose($fh);
					}
					if (isset($block_config['search_redirect_pattern']) && trim($block_config['search_redirect_pattern'])<>'')
					{
						$pattern=str_replace("%ID%",$data[0]['video_id'],str_replace("%DIR%",$data[0]['dir'],trim($block_config['search_redirect_pattern'])));
					} else {
						$pattern=str_replace("%ID%",$data[0]['video_id'],str_replace("%DIR%",$data[0]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
					}
					if (is_url($pattern))
					{
						return "status_302:$pattern";
					} else {
						return "status_302:$config[project_url]/$pattern";
					}
				}
			}
		}

		if (isset($block_config['randomize_positions']) && $sort_by_clear == 'ctr')
		{
			$positions = array_map('intval', array_map('trim', explode(',', $block_config['randomize_positions'])));
			$exclude_video_ids = '0';
			foreach ($data as $v)
			{
				$exclude_video_ids .= ',' . intval($v['video_id']);
			}

			$randomize_sort_by = strtolower($block_config['randomize_positions_sort_by']);
			$randomize_direction = strpos($randomize_sort_by, ' asc') !== false ? 'asc' : 'desc';
			$randomize_sort_by_clear = trim(str_replace(array('asc', 'desc'), '', $randomize_sort_by));
			if (!$randomize_sort_by_clear)
			{
				$randomize_sort_by_clear = 'random1';
			}
			if ($randomize_sort_by_clear == 'rating')
			{
				$randomize_sort_by_clear = "$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount $randomize_direction, $config[tables_prefix]videos.rating_amount";
			} elseif ($randomize_sort_by_clear != 'rand()')
			{
				$randomize_sort_by_clear = "$config[tables_prefix]videos.$randomize_sort_by_clear";
			}
			$randomize_sort_by = "$randomize_sort_by_clear $randomize_direction";

			$replace_data = mr2array(sql("SELECT $database_selectors[videos] from $from_clause where $where_clause $where and $config[tables_prefix]videos.video_id not in ($exclude_video_ids) order by $randomize_sort_by LIMIT " . count($positions) * 5));
			if ($randomize_sort_by_clear == "$config[tables_prefix]videos.random1")
			{
				shuffle($replace_data);
			}
			foreach ($positions as $position)
			{
				if (isset($data[$position - 1]) && count($replace_data) > 0)
				{
					array_splice($data, $position - 1, 0, [array_shift($replace_data)]);
					array_pop($data);
				}
			}
		}

		$matrix_key=md5("$page_id|$object_id");
		$place=1;
		$video_ids_list="";

		$list_rotator_context='';
		if ($storage[$object_id]['list_type']=='categories' && is_array($storage[$object_id]['category_info']))
		{
			$list_rotator_context='cat'.$storage[$object_id]['category_info']['category_id'];
		} elseif ($storage[$object_id]['list_type']=='tags' && is_array($storage[$object_id]['tag_info']))
		{
			$list_rotator_context='tag'.$storage[$object_id]['tag_info']['tag_id'];
		}
		
		foreach ($data as $k=>$v)
		{
			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			$data[$k]['formats']=get_video_formats($data[$k]['video_id'],$data[$k]['file_formats']);
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['video_id']);

			$screen_url_base=load_balance_screenshots_url();
			$data[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($data[$k]['video_id']).'/'.$data[$k]['video_id'];


			$result_rating=sql_pr("select rating from $config[tables_prefix]user_rating_history where user_id=$_SESSION[user_id] and type=1 and object_id=? ORDER BY added_date DESC LIMIT 1",$data[$k]['video_id']);
			if (mr2rows($result_rating)==0) {
				$data[$k]['user_rating']="N/A";
			} else {
				$data[$k]['user_rating']=mr2number($result_rating); 
			}

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['video_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['video_id']." order by id asc"));
				foreach ($data[$k]['models'] as $k2=>$v2)
				{
					$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
					{
						$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
						$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_user_info']))
			{
				$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
				$data[$k]['user']['country_code']=mr2array_single(sql_pr("select * from $config[tables_prefix]list_countries where country_id=".$data[$k]['user']['country_id']))['country_code'];
			}
			if (isset($block_config['show_content_source_info']) && $data[$k]['content_source_id']>0)
			{
				$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=".$data[$k]['content_source_id']));
				if ($data[$k]['content_source']['content_source_id']>0)
				{
					$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
						$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_dvd_info']) && $data[$k]['dvd_id']>0)
			{
				$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=".$data[$k]['dvd_id']));
				if ($data[$k]['dvd']['dvd_id']>0)
				{
					$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
					if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
					{
						$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
						$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
					}
				}
			}
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data[$k]['video_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['video_id'],date("Y-m-d H:i:s")));
			}
			$data[$k]['video_comments']=$data[$k]['comments_count'];

			$pattern=str_replace("%ID%",$data[$k]['video_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
			if (isset($block_config['mode_futures']))
			{
				$data[$k]['view_page_url']='';
			}

			$screen_amount=$data[$k]['screen_amount'];
			$screen_main=$data[$k]['screen_main'];
			if ($rotator_params['ROTATOR_VIDEOS_ENABLE']==1)
			{
				if ($page_is_xml<>1 && $config['disable_rotator']<>'true' && !isset($block_config['disable_rotator']))
				{
					// rotator
					if ($data[$k]['rs_completed']==0 && $data[$k]['screen_amount']>1 && $rotator_params['ROTATOR_SCREENSHOTS_ENABLE']==1)
					{
						$screen_cnt=$data[$k]['screen_amount'];
						if (strlen("$screen_cnt")==1){$screen_cnt="0$screen_cnt";} else
						if (strlen("$screen_cnt")>2){$screen_cnt="99";}

						$place_str=trim($place);
						if (strlen("$place_str")==1){$place_str="00$place_str";} else
						if (strlen("$place_str")==2){$place_str="0$place_str";} else
						if (strlen("$place_str")>3){$place_str="999";}

						$token="%KTR:$screen_cnt:$place_str%";
						$data[$k]['screen_main']=$token;

						if (intval($rotator_params['ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE'])==1)
						{
							$data[$k]['screen_amount']=1;
						}
					} else {
						$token="0";
					}
					if (isset($block_config['show_best_screenshots']))
					{
						$token="0";
					}

					$page_index=floor($from/$block_config['items_per_page'])+1;
					if ($config['rotator_no_params']<>'true')
					{
						$data[$k]['view_page_url']=$data[$k]['view_page_url']."?pqr=$place:$matrix_key:$token:".$data[$k]['video_id'].":$page_index:$list_rotator_context";
					}
					$data[$k]['rotator_params']="pqr=$place:$matrix_key:$token:".$data[$k]['video_id'].":$page_index:$list_rotator_context";
					$video_ids_list.=$data[$k]['video_id'].',';
				}
			}
			if (isset($block_config['show_best_screenshots']) && $data[$k]['rs_completed']==0)
			{
				if ($data[$k]['screen_main_temp']>0 && $data[$k]['screen_main_temp']<=$screen_amount)
				{
					$data[$k]['screen_main']=$data[$k]['screen_main_temp'];
				} else {
					$data[$k]['screen_main']=$screen_main;
				}
			}
			$place++;
		}
		if ($video_ids_list && $rotator_params['ROTATOR_VIDEOS_ENABLE']==1)
		{
			if ($page_is_xml<>1 && $config['disable_rotator']<>'true' && !isset($block_config['disable_rotator']) && $_SESSION['userdata']['user_id']<1)
			{
				// rotator
				$cnt_file_key=md5("$page_id|$object_id|".custom_list_videosGetHash($block_config));
				$cnt_file_dir="$cnt_file_key[0]$cnt_file_key[1]$cnt_file_key[2]";
				if (!is_file("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir/$cnt_file_key.dat"))
				{
					if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator",0777);chmod("$config[project_path]/admin/data/engine/rotator",0777);}
					if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}
					if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos/list")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos/list",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos/list",0777);}
					if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir",0777);}
				}

				if (isset($block_config['show_best_screenshots']) || $rotator_params['ROTATOR_SCREENSHOTS_ENABLE']==0)
				{
					$video_ids_list="$video_ids_list|$list_rotator_context|0";
				} else
				{
					$video_ids_list="$video_ids_list|$list_rotator_context|1";
				}
				file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir/$cnt_file_key.dat",$video_ids_list,LOCK_EX);
				file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/views.dat", "$video_ids_list\r\n", FILE_APPEND | LOCK_EX);
				chmod("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir/$cnt_file_key.dat",0666);
			}
		}
	} elseif (trim($_REQUEST[$block_config['var_sort_by']])=='')
	{
		$storage[$object_id]['sort_by']='relevance';
		$smarty->assign("sort_by",'relevance');
	}

	if ($external_search_enabled>0 && $external_search_result_count>0)
	{
		if ($external_search_enabled==1 || ($external_search_enabled==2 && $total_count<$external_search_enabled_condition))
		{
			switch ($external_search_display)
			{
				case 0:
					$data=array();
					for ($i=0;$i<$block_config['items_per_page'];$i++)
					{
						if ($i+$from<$external_search_result_count)
						{
							$data[]=$external_search_result[$i+$from-$external_search_index_from];
						}
					}
					$total_count=$external_search_result_count;
					break;
				case 1:
					$temp_data=array_merge($external_search_result,$data);
					$index_start=$from;
					if ($from>=$external_search_result_count)
					{
						$index_start=$external_search_result_count;
					}
					$data=array();
					for ($i=0;$i<$block_config['items_per_page'];$i++)
					{
						if ($i+$index_start<count($temp_data))
						{
							$data[]=$temp_data[$i+$index_start];
						}
					}
					break;
				case 2:
					$temp_data=array_merge($data,$external_search_result);
					$index_start=0;
					if (count($data)==0)
					{
						$index_start=$from-($total_count-$external_search_result_count);
					}
					$data=array();
					for ($i=0;$i<$block_config['items_per_page'];$i++)
					{
						if ($i+$index_start<count($temp_data))
						{
							$data[]=$temp_data[$i+$index_start];
						}
					}
					break;
			}
		}
	}

	if ($storage[$object_id]['list_type']=="search")
	{
		$search_results_count=intval($total_count);
		if ($search_results_count==0)
		{
			$search_results_count=count($data);
		}
		if ($storage[$object_id]['search_keyword']<>'' && $from==0 && $search_results_count>0 && (strpos(str_replace("www.","",$_SERVER['HTTP_REFERER']),str_replace("www.","",$config['project_url']))===0))
		{
			$q=$storage[$object_id]['search_keyword'];
			$date=date("Y-m-d");
			$fh=fopen("$config[project_path]/admin/data/stats/search.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,"$date|$q|$search_results_count|0\r\n");
			fclose($fh);
		}

		if ($search_results_count==0)
		{
			if (isset($block_config['search_empty_404']))
			{
				header('HTTP/1.0 404 Not Found');
				return '';
			}
			if (isset($block_config['search_empty_redirect_to']))
			{
				$pattern=urldecode(str_replace("%QUERY%",$storage[$object_id]['search_keyword'],trim($block_config['search_empty_redirect_to'])));
				if (is_url($pattern))
				{
					return "status_302:$pattern";
				} else {
					return "status_302:$config[project_url]/$pattern";
				}
			}
		}
	}

	$storage[$object_id]['total_count']=$total_count;
	$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
	$storage[$object_id]['var_from']=$block_config['var_from'];
	if (count($data)>0)
	{
		$storage[$object_id]['first_object_title']=$data[0]['title'];
		$storage[$object_id]['first_object_description']=$data[0]['description'];
		$update_storage_keys=array(
			'category_info',
			'category_group_info',
			'tag_info',
			'model_info',
			'model_group_info',
			'content_source_info',
			'content_source_group_info',
			'dvd_info',
			'dvd_group_info'
		);
		foreach ($update_storage_keys as $update_storage_key)
		{
			if (isset($storage[$object_id][$update_storage_key]))
			{
				$storage[$object_id][$update_storage_key]['first_object_title']=$data[0]['title'];
				$storage[$object_id][$update_storage_key]['first_object_description']=$data[0]['description'];
			}
		}
	}

	$smarty->assign("total_count",$total_count);
	$smarty->assign("items_per_page",$block_config['items_per_page']);
	$smarty->assign("showing_from",$from);
	$smarty->assign("data",$data);

	if (isset($block_config['var_from']))
	{
		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}
	return '';
}

function custom_list_videosGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_model_ids=trim($_REQUEST[$block_config['var_model_ids']]);
	$var_model_group_dir=trim($_REQUEST[$block_config['var_model_group_dir']]);
	$var_model_group_id=trim($_REQUEST[$block_config['var_model_group_id']]);
	$var_model_group_ids=trim($_REQUEST[$block_config['var_model_group_ids']]);
	$var_content_source_dir=trim($_REQUEST[$block_config['var_content_source_dir']]);
	$var_content_source_id=trim($_REQUEST[$block_config['var_content_source_id']]);
	$var_content_source_ids=trim($_REQUEST[$block_config['var_content_source_ids']]);
	$var_content_source_group_dir=trim($_REQUEST[$block_config['var_content_source_group_dir']]);
	$var_content_source_group_id=trim($_REQUEST[$block_config['var_content_source_group_id']]);
	$var_content_source_group_ids=trim($_REQUEST[$block_config['var_content_source_group_ids']]);
	$var_dvd_dir=trim($_REQUEST[$block_config['var_dvd_dir']]);
	$var_dvd_id=trim($_REQUEST[$block_config['var_dvd_id']]);
	$var_dvd_ids=trim($_REQUEST[$block_config['var_dvd_ids']]);
	$var_dvd_group_dir=trim($_REQUEST[$block_config['var_dvd_group_dir']]);
	$var_dvd_group_id=trim($_REQUEST[$block_config['var_dvd_group_id']]);
	$var_dvd_group_ids=trim($_REQUEST[$block_config['var_dvd_group_ids']]);
	$var_custom_flag1=trim($_REQUEST[$block_config['var_custom_flag1']]);
	$var_custom_flag2=trim($_REQUEST[$block_config['var_custom_flag2']]);
	$var_custom_flag3=trim($_REQUEST[$block_config['var_custom_flag3']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_mode_related=trim($_REQUEST[$block_config['var_mode_related']]);
	$var_video_dir=trim($_REQUEST[$block_config['var_video_dir']]);
	$var_video_id=trim($_REQUEST[$block_config['var_video_id']]);
	$var_is_private=trim($_REQUEST[$block_config['var_is_private']]);
	$var_is_hd=trim($_REQUEST[$block_config['var_is_hd']]);
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_connected_album_dir=trim($_REQUEST[$block_config['var_connected_album_dir']]);
	$var_connected_album_id=trim($_REQUEST[$block_config['var_connected_album_id']]);
	$var_post_date_from=trim($_REQUEST[$block_config['var_post_date_from']]);
	$var_post_date_to=trim($_REQUEST[$block_config['var_post_date_to']]);
	$var_duration_from=intval($_REQUEST[$block_config['var_duration_from']]);
	$var_duration_to=intval($_REQUEST[$block_config['var_duration_to']]);
	$var_release_year_from=intval($_REQUEST[$block_config['var_release_year_from']]);
	$var_release_year_to=intval($_REQUEST[$block_config['var_release_year_to']]);
	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_fav_type=trim($_REQUEST[$block_config['var_fav_type']]);
	$var_playlist_id=intval($_REQUEST[$block_config['var_playlist_id']]);

	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_dvd']) || isset($block_config['mode_purchased']) || isset($block_config['mode_history']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	} else {
		if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'') {
			if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
			{
				return "runtime_nocache";
			}
		}
		$result="$from|$items_per_page|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_content_source_dir|$var_content_source_id|$var_content_source_ids|$var_content_source_group_dir|$var_content_source_group_id|$var_content_source_group_ids|$var_dvd_dir|$var_dvd_id|$var_dvd_ids|$var_dvd_group_dir|$var_dvd_group_id|$var_dvd_group_ids|$var_custom_flag1|$var_custom_flag2|$var_custom_flag3|$var_search|$var_sort_by|$var_mode_related|$var_video_dir|$var_video_id|$var_is_private|$var_is_hd|$var_user_id|$var_connected_album_dir|$var_connected_album_id|$var_post_date_from|$var_post_date_to|$var_duration_from|$var_duration_to|$var_release_year_from|$var_release_year_to|$var_title_section|$var_fav_type|$var_playlist_id";
		if (isset($block_config['show_private']) || isset($block_config['show_premium']))
		{
			$result="$result|".intval($_SESSION['status_id']);
		}
		if (isset($block_config['show_only_from_same_country']))
		{
			$result="$result|$_SERVER[GEOIP_COUNTRY_CODE]";
		}
		return $result;
	}
}

function custom_list_videosCacheControl($block_config)
{
	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_dvd']) || isset($block_config['mode_purchased']) || isset($block_config['mode_history']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	if (isset($block_config['show_private']) || isset($block_config['show_premium']))
	{
		return "status_specific";
	}
	return "default";
}

function custom_list_videosAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='add_to_favourites' && isset($_REQUEST['video_ids']))
	{
		if ($_SESSION['user_id']<1)
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
		if (!is_array($_REQUEST['video_ids']))
		{
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_videos')));
		}

		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		$user_id=intval($_SESSION['user_id']);
		$video_ids=array_map("intval",$_REQUEST['video_ids']);
		$fav_type=intval($_REQUEST['fav_type']);
		$playlist_id=intval($_REQUEST['playlist_id']);
		if ($playlist_id>0)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where playlist_id=? and user_id=?",$playlist_id,$user_id))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'list_videos')));
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
		async_return_request_status();
	} elseif ($_REQUEST['action']=='move_to_dvd' && isset($_REQUEST['video_ids']) && isset($_REQUEST['dvd_id']))
	{
		if ($_SESSION['user_id']<1)
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_videos')));
		}
		if (!is_array($_REQUEST['video_ids']) || !intval($_REQUEST['dvd_id']))
		{
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_videos')));
		}

		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		$is_dvd_upload_allowed=false;
		$dvd_info=mr2array_single(sql_pr("select dvd_id, is_video_upload_allowed, user_id from $config[tables_prefix]dvds where dvd_id=?",intval($_REQUEST['dvd_id'])));
		if (count($dvd_info)>0)
		{
			if ($dvd_info['is_video_upload_allowed']==0 || ($dvd_info['is_video_upload_allowed']>0 && $dvd_info['user_id']==$_SESSION['user_id']))
			{
				$is_dvd_upload_allowed=true;
			} elseif ($dvd_info['is_video_upload_allowed']==1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",intval($_SESSION['user_id']),$dvd_info['user_id'],intval($_SESSION['user_id']),$dvd_info['user_id']))>0)
			{
				$is_dvd_upload_allowed=true;
			}
		}
		if (!$is_dvd_upload_allowed)
		{
			async_return_request_status(array(array('error_code'=>'forbidden','block'=>'list_videos')));
		}

		$video_ids=array_map("intval",$_REQUEST['video_ids']);
		if (count($video_ids)>0)
		{
			foreach ($video_ids as $video_id)
			{
				if (sql_update("update $config[tables_prefix]videos set dvd_id=? where video_id=? and user_id=?",intval($_REQUEST['dvd_id']),$video_id,$_SESSION['user_id']))
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=1, action_details='dvd_id', added_date=?",$_SESSION['user_id'],$_SESSION['username'],$video_id,date("Y-m-d H:i:s"));
				}
			}
		}

		async_return_request_status();
	} elseif (($_REQUEST['action']=='delete_from_favourites' || $_REQUEST['action']=='delete_from_uploaded' || $_REQUEST['action']=='delete_from_public' || $_REQUEST['action']=='delete_from_private' || $_REQUEST['action']=='delete_from_dvd') && is_array($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		custom_list_videosShow($block_config,null);
	}
}

function custom_list_videosPreProcess($block_config,$object_id)
{
	global $config,$page_id;

	if ($config['disable_rotator']<>'true' && !isset($block_config['disable_rotator']) && $_SESSION['userdata']['user_id']<1)
	{
		// rotator
		$cnt_file_key=md5("$page_id|$object_id|".custom_list_videosGetHash($block_config));
		$cnt_file_dir="$cnt_file_key[0]$cnt_file_key[1]$cnt_file_key[2]";
		$list_data=@file_get_contents("$config[project_path]/admin/data/engine/rotator/videos/list/$cnt_file_dir/$cnt_file_key.dat");
		if ($list_data)
		{
			if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator");chmod("$config[project_path]/admin/data/engine/rotator",0777);}
			if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos");chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}

			file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/views.dat", "$list_data\r\n", FILE_APPEND | LOCK_EX);
		}
	}
	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		require_once("$config[project_path]/admin/include/functions.php");
		$q=trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]),false));
		$q=trim(str_replace('[dash]','-',str_replace('-',' ',str_replace('--','[dash]',str_replace('?','',$q)))));
		$from=intval($_REQUEST[$block_config['var_from']]);
		if ($q<>'' && $from==0 && (strpos(str_replace("www.","",$_SERVER['HTTP_REFERER']),str_replace("www.","",$config['project_url']))===0))
		{
			// track stats only from own pages
			$date=date("Y-m-d");
			$fh=fopen("$config[project_path]/admin/data/stats/search.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,"$date|$q\r\n");
			fclose($fh);
		}
	}
}

function custom_list_videosMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"12"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[video_id,title,dir,duration,release_year,post_date,post_date_and_popularity,post_date_and_rating,post_date_and_duration,last_time_view_date,last_time_view_date_and_popularity,last_time_view_date_and_rating,last_time_view_date_and_duration,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,most_purchased,ctr,custom1,custom2,custom3,dvd_sort_id,pseudo_rand,your_rating]", "is_required"=>1, "default_value"=>"post_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_description",  "group"=>"static_filters", "type"=>"",                          "is_required"=>0),
		array("name"=>"show_only_from_same_country", "group"=>"static_filters", "type"=>"",                          "is_required"=>0),
		array("name"=>"show_with_admin_flag",        "group"=>"static_filters", "type"=>"STRING",                    "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_with_admin_flag",        "group"=>"static_filters", "type"=>"STRING",                    "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_categories",             "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",             "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_models",                 "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_models",                 "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_content_sources",        "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_content_sources",        "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_dvds",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_dvds",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_users",                  "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_users",                  "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",            "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",              "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"is_private",                  "group"=>"static_filters", "type"=>"CHOICE[0,1,2,0|1,0|2,1|2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"is_hd",                       "group"=>"static_filters", "type"=>"CHOICE[0,1]",               "is_required"=>0, "default_value"=>"1"),
		array("name"=>"format_postfix",              "group"=>"static_filters", "type"=>"STRING",                    "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_title_section",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
		array("name"=>"var_category_dir",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",              "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_ids",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_ids"),
		array("name"=>"var_category_group_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group"),
		array("name"=>"var_category_group_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_id"),
		array("name"=>"var_category_group_ids",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_ids"),
		array("name"=>"var_tag_dir",                  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",                   "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_tag_ids",                  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_ids"),
		array("name"=>"var_model_dir",                "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model"),
		array("name"=>"var_model_id",                 "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_id"),
		array("name"=>"var_model_ids",                "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_ids"),
		array("name"=>"var_model_group_dir",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group"),
		array("name"=>"var_model_group_id",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_id"),
		array("name"=>"var_model_group_ids",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_ids"),
		array("name"=>"var_content_source_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs"),
		array("name"=>"var_content_source_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_id"),
		array("name"=>"var_content_source_ids",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_ids"),
		array("name"=>"var_content_source_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_group"),
		array("name"=>"var_content_source_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_group_id"),
		array("name"=>"var_content_source_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_group_ids"),
		array("name"=>"var_dvd_dir",                  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd"),
		array("name"=>"var_dvd_id",                   "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_id"),
		array("name"=>"var_dvd_ids",                  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_ids"),
		array("name"=>"var_dvd_group_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_group"),
		array("name"=>"var_dvd_group_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_group_id"),
		array("name"=>"var_dvd_group_ids",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_group_ids"),
		array("name"=>"var_is_private",               "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"is_private"),
		array("name"=>"var_is_hd",                    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"is_hd"),
		array("name"=>"var_post_date_from",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_from"),
		array("name"=>"var_post_date_to",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_to"),
		array("name"=>"var_duration_from",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"duration_from"),
		array("name"=>"var_duration_to",              "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"duration_to"),
		array("name"=>"var_release_year_from",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"release_year_from"),
		array("name"=>"var_release_year_to",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"release_year_to"),
		array("name"=>"var_custom_flag1",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag1"),
		array("name"=>"var_custom_flag2",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag2"),
		array("name"=>"var_custom_flag3",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag3"),

		// search
		array("name"=>"var_search",                     "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",                  "group"=>"search", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"search_scope",                   "group"=>"search", "type"=>"CHOICE[0,1,2]",     "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_redirect_enabled",        "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_redirect_pattern",        "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>""),
		array("name"=>"search_empty_404",               "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_empty_redirect_to",       "group"=>"search", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"enable_search_on_tags",          "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_categories",    "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_models",        "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_cs",            "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_dvds",          "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_custom_fields", "group"=>"search", "type"=>"",                  "is_required"=>0),

		// related
		array("name"=>"mode_related",                   "group"=>"related", "type"=>"CHOICE[1,2,3,4,5,6,7,8]", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"var_video_dir",                  "group"=>"related", "type"=>"STRING",                  "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_video_id",                   "group"=>"related", "type"=>"STRING",                  "is_required"=>0, "default_value"=>"id"),
		array("name"=>"mode_related_category_group_id", "group"=>"related", "type"=>"STRING",                  "is_required"=>0),
		array("name"=>"mode_related_model_group_id",    "group"=>"related", "type"=>"STRING",                  "is_required"=>0),
		array("name"=>"var_mode_related",               "group"=>"related", "type"=>"STRING",                  "is_required"=>0, "default_value"=>"mode_related"),

		// connected albums
		array("name"=>"mode_connected_album",    "group"=>"connected_albums", "type"=>"",       "is_required"=>0),
		array("name"=>"var_connected_album_dir", "group"=>"connected_albums", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_connected_album_id",  "group"=>"connected_albums", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// display modes
		array("name"=>"mode_favourites",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_uploaded",                "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_dvd",                     "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_purchased",               "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_history",                 "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_subscribed",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_futures",                 "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"fav_type",                     "group"=>"display_modes", "type"=>"INT",    "is_required"=>0, "default_value"=>"0"),
		array("name"=>"var_fav_type",                 "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"fav_type"),
		array("name"=>"var_playlist_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"playlist_id"),
		array("name"=>"var_user_id",                  "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",     "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_uploaded_videos", "group"=>"display_modes", "type"=>"",       "is_required"=>0),

		// subselects
		array("name"=>"show_content_source_info", "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_categories_info",     "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_tags_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_models_info",         "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_dvd_info",            "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_user_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_flags_info",          "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments",            "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments_count",      "group"=>"subselects", "type"=>"INT", "is_required"=>0, "default_value"=>"2"),

		// access
		array("name"=>"show_private", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_premium", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),

		// rotator
		array("name"=>"disable_rotator",             "group"=>"rotator", "type"=>"",         "is_required"=>0),
		array("name"=>"finished_rotation",           "group"=>"rotator", "type"=>"",         "is_required"=>0),
		array("name"=>"under_rotation",              "group"=>"rotator", "type"=>"",         "is_required"=>0),
		array("name"=>"show_best_screenshots",       "group"=>"rotator", "type"=>"",         "is_required"=>0),
		array("name"=>"randomize_positions",         "group"=>"rotator", "type"=>"INT_LIST", "is_required"=>0),
		array("name"=>"randomize_positions_sort_by", "group"=>"rotator", "type"=>"SORTING[post_date,rating,video_viewed,random1]", "is_required"=>0, "default_value"=>"pseudo_rand desc"),
	);
}

function custom_list_videosLegalRequestVariables()
{
	return array('action');
}

function custom_list_videosJavascript($block_config)
{
	global $config;

	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_dvd'])) && !isset($block_config['var_user_id']))
	{
		return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
	}
	return null;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>