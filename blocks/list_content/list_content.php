<?php
function list_contentShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}
	$from=intval($_REQUEST[$block_config['var_from']]);
	if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}

	if ($_REQUEST['action']=='delete_from_favourites' && (is_array($_REQUEST['delete_album_ids']) || is_array($_REQUEST['delete_video_ids'])))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$fav_type=intval($_REQUEST['fav_type']);
			$playlist_id=intval($_REQUEST['playlist_id']);
			if ($playlist_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]playlists where playlist_id=? and user_id=?",$playlist_id,$user_id))==0)
				{
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=delete_done");die;
					}
				}
				$fav_type=10;
			}
			if (is_array($_REQUEST['delete_album_ids']))
			{
				$delete_ids=implode(",",array_map("intval",$_REQUEST['delete_album_ids']));
				sql_pr("delete from $config[tables_prefix]fav_albums where user_id=? and album_id in ($delete_ids) and fav_type=?",$user_id,$fav_type);
				fav_albums_changed($delete_ids);
			}
			if (is_array($_REQUEST['delete_video_ids']))
			{
				$delete_ids=implode(",",array_map("intval",$_REQUEST['delete_video_ids']));
				sql_pr("delete from $config[tables_prefix]fav_videos where user_id=? and video_id in ($delete_ids) and fav_type=? and playlist_id=?",$user_id,$fav_type,$playlist_id);
				fav_videos_changed($delete_ids,$fav_type);
			}
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_content')));
		}
	}

	if ($_REQUEST['action']=='delete_from_uploaded' && (is_array($_REQUEST['delete_album_ids']) || is_array($_REQUEST['delete_video_ids'])))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);

			if (isset($block_config['allow_delete_uploaded_content']))
			{
				$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete_video_ids']));
				$delete_ids=mr2array_list(sql_pr("select video_id from $config[tables_prefix]videos where user_id=? and video_id in ($delete_ids_str) and is_locked=0",$user_id));
				if (count($delete_ids)>0)
				{
					$delete_ids_str=implode(",",$delete_ids);
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
				}

				$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete_album_ids']));
				$delete_ids=mr2array_list(sql_pr("select album_id from $config[tables_prefix]albums where user_id=? and album_id in ($delete_ids_str) and is_locked=0",$user_id));
				if (count($delete_ids)>0)
				{
					$delete_ids_str=implode(",",$delete_ids);
					sql_pr("update $config[tables_prefix]albums set status_id=4 where album_id in ($delete_ids_str)");

					foreach ($delete_ids as $album_id)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where album_id=? and expiry_date>?",$album_id,date("Y-m-d H:i:s")))==0)
						{
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=2, added_date=?",$_SESSION['user_id'],$_SESSION['username'],$album_id,date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=11, album_id=?, data=?, added_date=?",$album_id,serialize(array()),date("Y-m-d H:i:s"));
						} else {
							sql_pr("update $config[tables_prefix]albums set status_id=0 where album_id=?",$album_id);
						}
					}
				}

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=delete_done");die;
				}
			} else {
				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'delete_forbidden','block'=>'list_content')));
				} else {
					header("Location: ?action=delete_forbidden");die;
				}
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_content')));
		}
	}

	$where='';
	$where_videos='';
	$where_albums='';

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

	if (isset($block_config['mode_favourites']))
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
			$favourites_summary_videos=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $config[tables_prefix]fav_videos.user_id=? and $database_selectors[where_videos] group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$user_id));
			$favourites_summary_albums=mr2array(sql_pr("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $config[tables_prefix]fav_albums.user_id=? and $database_selectors[where_albums] group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc",$user_id));
		} else {
			$smarty->assign("playlists",mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and user_id=?",$user_id)));
			$favourites_summary_videos=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id left join $config[tables_prefix]playlists on $config[tables_prefix]fav_videos.playlist_id = $config[tables_prefix]playlists.playlist_id where $config[tables_prefix]fav_videos.user_id=? and $database_selectors[where_videos] and ($config[tables_prefix]fav_videos.fav_type!=10 or $database_selectors[where_playlists]) group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$user_id));
			$favourites_summary_albums=mr2array(sql_pr("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $config[tables_prefix]fav_albums.user_id=? and $database_selectors[where_albums] group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc",$user_id));
		}

		$temp_summary=array();
		$temp_total=0;
		foreach ($favourites_summary_videos as $summary_item)
		{
			if (!isset($temp_summary[$summary_item['fav_type']]))
			{
				$temp_summary[$summary_item['fav_type']]=array('fav_type'=>$summary_item['fav_type'],'amount'=>0);
			}
			$temp_summary[$summary_item['fav_type']]['amount']+=$summary_item['amount'];
			$temp_total+=$summary_item["amount"];
		}
		foreach ($favourites_summary_albums as $summary_item)
		{
			if (!isset($temp_summary[$summary_item['fav_type']]))
			{
				$temp_summary[$summary_item['fav_type']]=array('fav_type'=>$summary_item['fav_type'],'amount'=>0);
			}
			$temp_summary[$summary_item['fav_type']]['amount']+=$summary_item['amount'];
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

		$sql_query="
			from (
				select
					$config[tables_prefix]videos.video_id as object_id,
					1 as object_type,
					$config[tables_prefix]videos.status_id as status_id,
					$config[tables_prefix]videos.is_private as is_private,
					$database_selectors[videos_selector_title] as title,
					$database_selectors[videos_selector_dir] as dir,
					$database_selectors[videos_selector_description] as description,
					$config[tables_prefix]videos.duration as duration,
					$config[tables_prefix]videos.content_source_id as content_source_id,
					$config[tables_prefix]videos.user_id as user_id,
					$config[tables_prefix]videos.screen_amount as images_amount,
					$config[tables_prefix]videos.screen_main as main_image,
					$config[tables_prefix]videos.server_group_id as server_group_id,
					$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating,
					$config[tables_prefix]videos.rating as rating_summary,
					$config[tables_prefix]videos.rating_amount as rating_votes,
					$config[tables_prefix]videos.video_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]videos.last_time_view_date as last_time_view_date,
					$config[tables_prefix]videos.file_formats as files,
					$config[tables_prefix]videos.custom1 as custom1,
					$config[tables_prefix]videos.custom2 as custom2,
					$config[tables_prefix]videos.custom3 as custom3,
					$config[tables_prefix]fav_videos.added_date as added2fav_date
				from
					$config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id
				where
					$database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id and $config[tables_prefix]fav_videos.fav_type=$fav_type and $config[tables_prefix]fav_videos.playlist_id=$playlist_id $where $where_videos
				union all select
					$config[tables_prefix]albums.album_id as object_id,
					2 as object_type,
					$config[tables_prefix]albums.status_id as status_id,
					$config[tables_prefix]albums.is_private as is_private,
					$database_selectors[albums_selector_title] as title,
					$database_selectors[albums_selector_dir] as dir,
					$database_selectors[albums_selector_description] as description,
					0 as duration,
					$config[tables_prefix]albums.content_source_id as content_source_id,
					$config[tables_prefix]albums.user_id as user_id,
					$config[tables_prefix]albums.photos_amount as images_amount,
					'preview' as main_image,
					$config[tables_prefix]albums.server_group_id as server_group_id,
					$config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount as rating,
					$config[tables_prefix]albums.rating as rating_summary,
					$config[tables_prefix]albums.rating_amount as rating_votes,
					$config[tables_prefix]albums.album_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]albums.last_time_view_date as last_time_view_date,
					$config[tables_prefix]albums.zip_files as files,
					$config[tables_prefix]albums.custom1 as custom1,
					$config[tables_prefix]albums.custom2 as custom2,
					$config[tables_prefix]albums.custom3 as custom3,
					$config[tables_prefix]fav_albums.added_date as added2fav_date
				from
					$config[tables_prefix]albums inner join $config[tables_prefix]fav_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]fav_albums.album_id
				where
					$database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=$user_id and $config[tables_prefix]fav_albums.fav_type=$fav_type $where $where_albums
			) X
		";

		$data=mr2array(sql("select * $sql_query order by added2fav_date desc LIMIT $from, $block_config[items_per_page]"));
		$total_count=mr2number(sql("select count(*) $sql_query"));

		foreach ($data as $k=>$v)
		{
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['object_id']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			if ($data[$k]['object_type']=='1')
			{
				$data[$k]['formats']=get_video_formats($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$screen_base_url=load_balance_screenshots_url();
				$data[$k]['screen_base_url']=$screen_base_url.'/'.$data[$k]['dir_path'].'/'.$data[$k]['object_id'];

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['object_id']." order by id asc"));
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
			} else {
				$data[$k]['zip_files']=get_album_zip_files($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$data[$k]['image_base_url']="$lb_server[urls]/preview/";

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=".$data[$k]['object_id']." order by id asc"));
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
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=? and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['object_id'],$data[$k]['object_type'],date("Y-m-d H:i:s")));
			}
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
			$where_albums.=" and $database_selectors[where_albums_internal] ";
			$where_videos.=" and $database_selectors[where_videos_internal] ";
			$uploaded_summary_videos=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and user_id=$user_id group by is_private order by is_private desc"));
			$uploaded_summary_albums=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]albums where $database_selectors[where_albums_internal] and user_id=$user_id group by is_private order by is_private desc"));
		} else {
			$where_albums.=" and $database_selectors[where_albums] ";
			$where_videos.=" and $database_selectors[where_videos] ";
			$uploaded_summary_videos=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]videos where $database_selectors[where_videos] and user_id=$user_id group by is_private order by is_private desc"));
			$uploaded_summary_albums=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=$user_id group by is_private order by is_private desc"));
		}

		$temp_summary=array();
		$temp_total=0;
		foreach ($uploaded_summary_videos as $summary_item)
		{
			if (!isset($temp_summary[$summary_item['is_private']]))
			{
				$temp_summary[$summary_item['is_private']]=array('is_private'=>$summary_item['is_private'],'amount'=>0);
			}
			$temp_summary[$summary_item['is_private']]["amount"]+=$summary_item["amount"];
			$temp_total+=$summary_item["amount"];
		}
		foreach ($uploaded_summary_albums as $summary_item)
		{
			if (!isset($temp_summary[$summary_item['is_private']]))
			{
				$temp_summary[$summary_item['is_private']]=array('is_private'=>$summary_item['is_private'],'amount'=>0);
			}
			$temp_summary[$summary_item['is_private']]["amount"]+=$summary_item["amount"];
			$temp_total+=$summary_item["amount"];
		}
		$smarty->assign("uploaded_summary",$temp_summary);
		$smarty->assign("uploaded_summary_total",$temp_total);
		$storage[$object_id]["uploaded_summary"]=$temp_summary;
		$storage[$object_id]["uploaded_summary_total"]=$temp_total;

		$sql_query="
			from (
				select
					$config[tables_prefix]videos.video_id as object_id,
					1 as object_type,
					$config[tables_prefix]videos.status_id as status_id,
					$config[tables_prefix]videos.is_private as is_private,
					$database_selectors[videos_selector_title] as title,
					$database_selectors[videos_selector_dir] as dir,
					$database_selectors[videos_selector_description] as description,
					$config[tables_prefix]videos.duration as duration,
					$config[tables_prefix]videos.content_source_id as content_source_id,
					$config[tables_prefix]videos.user_id as user_id,
					$config[tables_prefix]videos.screen_amount as images_amount,
					$config[tables_prefix]videos.screen_main as main_image,
					$config[tables_prefix]videos.server_group_id as server_group_id,
					$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating,
					$config[tables_prefix]videos.rating as rating_summary,
					$config[tables_prefix]videos.rating_amount as rating_votes,
					$config[tables_prefix]videos.video_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]videos.added_date as added_date,
					$config[tables_prefix]videos.last_time_view_date as last_time_view_date,
					$config[tables_prefix]videos.file_formats as files,
					$config[tables_prefix]videos.custom1 as custom1,
					$config[tables_prefix]videos.custom2 as custom2,
					$config[tables_prefix]videos.custom3 as custom3
				from
					$config[tables_prefix]videos
				where
					$config[tables_prefix]videos.user_id=$user_id $where $where_videos
				union all select
					$config[tables_prefix]albums.album_id as object_id,
					2 as object_type,
					$config[tables_prefix]albums.status_id as status_id,
					$config[tables_prefix]albums.is_private as is_private,
					$database_selectors[albums_selector_title] as title,
					$database_selectors[albums_selector_dir] as dir,
					$database_selectors[albums_selector_description] as description,
					0 as duration,
					$config[tables_prefix]albums.content_source_id as content_source_id,
					$config[tables_prefix]albums.user_id as user_id,
					$config[tables_prefix]albums.photos_amount as images_amount,
					'preview' as main_image,
					$config[tables_prefix]albums.server_group_id as server_group_id,
					$config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount as rating,
					$config[tables_prefix]albums.rating as rating_summary,
					$config[tables_prefix]albums.rating_amount as rating_votes,
					$config[tables_prefix]albums.album_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]albums.added_date as added_date,
					$config[tables_prefix]albums.last_time_view_date as last_time_view_date,
					$config[tables_prefix]albums.zip_files as files,
					$config[tables_prefix]albums.custom1 as custom1,
					$config[tables_prefix]albums.custom2 as custom2,
					$config[tables_prefix]albums.custom3 as custom3
				from
					$config[tables_prefix]albums
				where
					$config[tables_prefix]albums.user_id=$user_id $where $where_albums
			) X
		";

		$data=mr2array(sql("select * $sql_query order by added_date desc LIMIT $from, $block_config[items_per_page]"));
		$total_count=mr2number(sql("select count(*) $sql_query"));

		foreach ($data as $k=>$v)
		{
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['object_id']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			if ($data[$k]['object_type']=='1')
			{
				$data[$k]['formats']=get_video_formats($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$screen_base_url=load_balance_screenshots_url();
				$data[$k]['screen_base_url']=$screen_base_url.'/'.$data[$k]['dir_path'].'/'.$data[$k]['object_id'];

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['object_id']." order by id asc"));
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
			} else {
				$data[$k]['zip_files']=get_album_zip_files($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$data[$k]['image_base_url']="$lb_server[urls]/preview/";

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=".$data[$k]['object_id']." order by id asc"));
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
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=? and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['object_id'],$data[$k]['object_type'],date("Y-m-d H:i:s")));
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
		$sql_query="
			from (
				select
					$config[tables_prefix]videos.video_id as object_id,
					1 as object_type,
					$config[tables_prefix]videos.status_id as status_id,
					$config[tables_prefix]videos.is_private as is_private,
					$database_selectors[videos_selector_title] as title,
					$database_selectors[videos_selector_dir] as dir,
					$database_selectors[videos_selector_description] as description,
					$config[tables_prefix]videos.duration as duration,
					$config[tables_prefix]videos.content_source_id as content_source_id,
					$config[tables_prefix]videos.user_id as user_id,
					$config[tables_prefix]videos.screen_amount as images_amount,
					$config[tables_prefix]videos.screen_main as main_image,
					$config[tables_prefix]videos.server_group_id as server_group_id,
					$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating,
					$config[tables_prefix]videos.rating as rating_summary,
					$config[tables_prefix]videos.rating_amount as rating_votes,
					$config[tables_prefix]videos.video_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]videos.added_date as added_date,
					$config[tables_prefix]videos.last_time_view_date as last_time_view_date,
					$config[tables_prefix]videos.file_formats as files,
					$config[tables_prefix]videos.custom1 as custom1,
					$config[tables_prefix]videos.custom2 as custom2,
					$config[tables_prefix]videos.custom3 as custom3,
					$config[tables_prefix]users_purchases.added_date as purchase_date,
					$config[tables_prefix]users_purchases.expiry_date as expiry_date,
					$config[tables_prefix]users_purchases.tokens as tokens_spent
				from
					$config[tables_prefix]videos inner join $config[tables_prefix]users_purchases on $config[tables_prefix]videos.video_id=$config[tables_prefix]users_purchases.video_id
				where
					$database_selectors[where_videos_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where
				union all select
					$config[tables_prefix]albums.album_id as object_id,
					2 as object_type,
					$config[tables_prefix]albums.status_id as status_id,
					$config[tables_prefix]albums.is_private as is_private,
					$database_selectors[albums_selector_title] as title,
					$database_selectors[albums_selector_dir] as dir,
					$database_selectors[albums_selector_description] as description,
					0 as duration,
					$config[tables_prefix]albums.content_source_id as content_source_id,
					$config[tables_prefix]albums.user_id as user_id,
					$config[tables_prefix]albums.photos_amount as images_amount,
					'preview' as main_image,
					$config[tables_prefix]albums.server_group_id as server_group_id,
					$config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount as rating,
					$config[tables_prefix]albums.rating as rating_summary,
					$config[tables_prefix]albums.rating_amount as rating_votes,
					$config[tables_prefix]albums.album_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]albums.added_date as added_date,
					$config[tables_prefix]albums.last_time_view_date as last_time_view_date,
					$config[tables_prefix]albums.zip_files as files,
					$config[tables_prefix]albums.custom1 as custom1,
					$config[tables_prefix]albums.custom2 as custom2,
					$config[tables_prefix]albums.custom3 as custom3,
					$config[tables_prefix]users_purchases.added_date as purchase_date,
					$config[tables_prefix]users_purchases.expiry_date as expiry_date,
					$config[tables_prefix]users_purchases.tokens as tokens_spent
				from
					$config[tables_prefix]albums inner join $config[tables_prefix]users_purchases on $config[tables_prefix]albums.album_id=$config[tables_prefix]users_purchases.album_id
				where
					$database_selectors[where_albums_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where
			) X
		";

		$data=mr2array(sql("select * $sql_query order by purchase_date desc LIMIT $from, $block_config[items_per_page]"));
		$total_count=mr2number(sql("select count(*) $sql_query"));

		foreach ($data as $k=>$v)
		{
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['object_id']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			if ($data[$k]['object_type']=='1')
			{
				$data[$k]['formats']=get_video_formats($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$screen_base_url=load_balance_screenshots_url();
				$data[$k]['screen_base_url']=$screen_base_url.'/'.$data[$k]['dir_path'].'/'.$data[$k]['object_id'];

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['object_id']." order by id asc"));
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
			} else {
				$data[$k]['zip_files']=get_album_zip_files($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$data[$k]['image_base_url']="$lb_server[urls]/preview/";

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=".$data[$k]['object_id']." order by id asc"));
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
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=? and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['object_id'],$data[$k]['object_type'],date("Y-m-d H:i:s")));
			}
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
			$where_subscribed_models_videos="video_id in (select video_id from $config[tables_prefix]models_videos where model_id in(-1$where_subscribed_models))";
			$where_subscribed_models_albums="album_id in (select album_id from $config[tables_prefix]models_albums where model_id in(-1$where_subscribed_models))";
		} else {
			$where_subscribed_models_videos="1=0";
			$where_subscribed_models_albums="1=0";
		}
		if ($where_subscribed_dvds<>'')
		{
			$where_subscribed_dvds="dvd_id in (-1$where_subscribed_dvds)";
		} else {
			$where_subscribed_dvds="1=0";
		}
		if ($where_subscribed_categories<>'')
		{
			$where_subscribed_categories_videos="video_id in (select video_id from $config[tables_prefix]categories_videos where category_id in(-1$where_subscribed_categories))";
			$where_subscribed_categories_albums="album_id in (select album_id from $config[tables_prefix]categories_albums where category_id in(-1$where_subscribed_categories))";
		} else {
			$where_subscribed_categories_videos="1=0";
			$where_subscribed_categories_albums="1=0";
		}
		if ($where_subscribed_playlists<>'')
		{
			$where_subscribed_playlists="video_id in (select video_id from $config[tables_prefix]fav_videos inner join $config[tables_prefix]playlists on $config[tables_prefix]fav_videos.playlist_id=$config[tables_prefix]playlists.playlist_id where $config[tables_prefix]fav_videos.playlist_id in(-1$where_subscribed_playlists) and $database_selectors[where_playlists])";
		} else {
			$where_subscribed_playlists="1=0";
		}
		$where_videos="and ($where_subscribed_users or $where_subscribed_cs or $where_subscribed_models_videos or $where_subscribed_dvds or $where_subscribed_categories_videos or $where_subscribed_playlists)";
		$where_albums="and ($where_subscribed_users or $where_subscribed_cs or $where_subscribed_models_albums or $where_subscribed_categories_albums)";

		$sql_query="
			from (
				select
					$config[tables_prefix]videos.video_id as object_id,
					1 as object_type,
					$config[tables_prefix]videos.status_id as status_id,
					$config[tables_prefix]videos.is_private as is_private,
					$database_selectors[videos_selector_title] as title,
					$database_selectors[videos_selector_dir] as dir,
					$database_selectors[videos_selector_description] as description,
					$config[tables_prefix]videos.duration as duration,
					$config[tables_prefix]videos.content_source_id as content_source_id,
					$config[tables_prefix]videos.user_id as user_id,
					$config[tables_prefix]videos.screen_amount as images_amount,
					$config[tables_prefix]videos.screen_main as main_image,
					$config[tables_prefix]videos.server_group_id as server_group_id,
					$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating,
					$config[tables_prefix]videos.rating as rating_summary,
					$config[tables_prefix]videos.rating_amount as rating_votes,
					$config[tables_prefix]videos.video_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]videos.added_date as added_date,
					$config[tables_prefix]videos.last_time_view_date as last_time_view_date,
					$config[tables_prefix]videos.file_formats as files,
					$config[tables_prefix]videos.custom1 as custom1,
					$config[tables_prefix]videos.custom2 as custom2,
					$config[tables_prefix]videos.custom3 as custom3
				from
					$config[tables_prefix]videos
				where
					$database_selectors[where_videos] $where $where_videos
				union all select
					$config[tables_prefix]albums.album_id as object_id,
					2 as object_type,
					$config[tables_prefix]albums.status_id as status_id,
					$config[tables_prefix]albums.is_private as is_private,
					$database_selectors[albums_selector_title] as title,
					$database_selectors[albums_selector_dir] as dir,
					$database_selectors[albums_selector_description] as description,
					0 as duration,
					$config[tables_prefix]albums.content_source_id as content_source_id,
					$config[tables_prefix]albums.user_id as user_id,
					$config[tables_prefix]albums.photos_amount as images_amount,
					'preview' as main_image,
					$config[tables_prefix]albums.server_group_id as server_group_id,
					$config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount as rating,
					$config[tables_prefix]albums.rating as rating_summary,
					$config[tables_prefix]albums.rating_amount as rating_votes,
					$config[tables_prefix]albums.album_viewed as object_viewed,
					$database_selectors[generic_post_date_selector] as post_date,
					$config[tables_prefix]albums.added_date as added_date,
					$config[tables_prefix]albums.last_time_view_date as last_time_view_date,
					$config[tables_prefix]albums.zip_files as files,
					$config[tables_prefix]albums.custom1 as custom1,
					$config[tables_prefix]albums.custom2 as custom2,
					$config[tables_prefix]albums.custom3 as custom3
				from
					$config[tables_prefix]albums
				where
					$database_selectors[where_albums] $where $where_albums
			) X
		";

		$data=mr2array(sql("select * $sql_query order by $database_selectors[generic_post_date_selector] desc LIMIT $from, $block_config[items_per_page]"));
		$total_count=mr2number(sql("select count(*) $sql_query"));

		foreach ($data as $k=>$v)
		{
			$data[$k]['dir_path']=get_dir_by_id($data[$k]['object_id']);
			$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
			if ($data[$k]['object_type']=='1')
			{
				$data[$k]['formats']=get_video_formats($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$screen_base_url=load_balance_screenshots_url();
				$data[$k]['screen_base_url']=$screen_base_url.'/'.$data[$k]['dir_path'].'/'.$data[$k]['object_id'];

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['object_id']." order by id asc"));
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
			} else {
				$data[$k]['zip_files']=get_album_zip_files($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

				$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";

				$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$data[$k]['image_base_url']="$lb_server[urls]/preview/";

				if (isset($block_config['show_categories_info']))
				{
					$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_tags_info']))
				{
					$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=".$data[$k]['object_id']." order by id asc"));
				}
				if (isset($block_config['show_models_info']))
				{
					$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=".$data[$k]['object_id']." order by id asc"));
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
			if (isset($block_config['show_comments']))
			{
				$show_comments_limit='';
				if (intval($block_config['show_comments_count'])>0)
				{
					$show_comments_limit='limit '.intval($block_config['show_comments_count']);
				}
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=? and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['object_id'],$data[$k]['object_type'],date("Y-m-d H:i:s")));
			}
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

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}
	if (isset($block_config['days_passed_from']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_from'])+1,date("Y")));
		$where.=" and post_date<='$date_passed_from'";
	}
	if (isset($block_config['days_passed_to']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_to'])+1,date("Y")));
		$where.=" and post_date>='$date_passed_from'";
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

	if ((isset($block_config['var_content_source_dir']) || isset($block_config['var_content_source_id'])) && ($_REQUEST[$block_config['var_content_source_dir']]<>'' || $_REQUEST[$block_config['var_content_source_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_content_source_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_content_source_dir']]),trim($_REQUEST[$block_config['var_content_source_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources_active_disabled] and content_source_id=?",intval($_REQUEST[$block_config['var_content_source_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$content_source_id=intval($data_temp["content_source_id"]);

			$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_content_sources on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_content_sources.category_id where $database_selectors[where_categories] and content_source_id=$data_temp[content_source_id]"));
			foreach ($data_temp['categories'] as $v)
			{
				$data_temp['categories_as_string'].=$v['title'].", ";
			}
			$data_temp['categories_as_string']=rtrim($data_temp['categories_as_string'],", ");
			unset($data_temp['categories']);

			$data_temp['base_files_url']=$config['content_url_content_sources'].'/'.$data_temp['content_source_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
			{
				$pattern=str_replace("%ID%",$data_temp['content_source_id'],str_replace("%DIR%",$data_temp['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
				$data_temp['view_page_url']="$config[project_url]/$pattern";
			}

			$storage[$object_id]['list_type']="content_sources";
			$storage[$object_id]['content_source']=$data_temp['title'];
			$storage[$object_id]['content_source_info']=$data_temp;
			$smarty->assign('list_type',"content_sources");
			$smarty->assign('content_source',$data_temp['title']);
			$smarty->assign('content_source_info',$data_temp);
			$where_albums.=" and content_source_id='$content_source_id' ";
			$where_videos.=" and content_source_id='$content_source_id' ";
		} else
		{
			return 'status_404';
		}
	}

	if ((isset($block_config['var_content_source_group_dir']) || isset($block_config['var_content_source_group_id'])) && ($_REQUEST[$block_config['var_content_source_group_dir']]<>'' || $_REQUEST[$block_config['var_content_source_group_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_content_source_group_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_content_source_group_dir']]),trim($_REQUEST[$block_config['var_content_source_group_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups_active_disabled] and content_source_group_id=?",intval($_REQUEST[$block_config['var_content_source_group_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$content_source_group_id=intval($data_temp["content_source_group_id"]);

			$where_albums.=" and content_source_id in (select content_source_id from $config[tables_prefix]content_sources where content_source_group_id=$content_source_group_id)";
			$where_videos.=" and content_source_id in (select content_source_id from $config[tables_prefix]content_sources where content_source_group_id=$content_source_group_id)";

			$storage[$object_id]['list_type']="content_sources_groups";
			$storage[$object_id]['content_source_group']=$data_temp['title'];
			$storage[$object_id]['content_source_group_info']=$data_temp;
			$smarty->assign('list_type',"content_sources_groups");
			$smarty->assign('content_source_group',$data_temp['title']);
			$smarty->assign('content_source_group_info',$data_temp);
		} else
		{
			return 'status_404';
		}
	}

	if ((isset($block_config['var_category_dir']) || isset($block_config['var_category_id'])) && ($_REQUEST[$block_config['var_category_dir']]<>'' || $_REQUEST[$block_config['var_category_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_category_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_category_dir']]),trim($_REQUEST[$block_config['var_category_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories_active_disabled] and category_id=?",intval($_REQUEST[$block_config['var_category_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$category_id=intval($data_temp["category_id"]);

			$where_albums.=" and album_id in (select album_id from $config[tables_prefix]categories_albums where category_id=$category_id)";
			$where_videos.=" and video_id in (select video_id from $config[tables_prefix]categories_videos where category_id=$category_id)";
			$storage[$object_id]['list_type']="categories";
			$storage[$object_id]['category']=$data_temp['title'];
			$storage[$object_id]['category_info']=$data_temp;
			$smarty->assign('list_type',"categories");
			$smarty->assign('category',$data_temp['title']);
			$smarty->assign('category_info',$data_temp);
		} else
		{
			return 'status_404';
		}
	}

	if ((isset($block_config['var_tag_dir']) || isset($block_config['var_tag_id'])) && ($_REQUEST[$block_config['var_tag_dir']]<>'' || $_REQUEST[$block_config['var_tag_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_tag_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags where $database_selectors[where_tags_active_disabled] and (tag_dir=? or $database_selectors[where_locale_tag_dir])",trim($_REQUEST[$block_config['var_tag_dir']]),trim($_REQUEST[$block_config['var_tag_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags where $database_selectors[where_tags_active_disabled] and tag_id=?",intval($_REQUEST[$block_config['var_tag_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$tag_id=intval($data_temp["tag_id"]);

			$where_albums.=" and album_id in (select album_id from $config[tables_prefix]tags_albums where tag_id=$tag_id) ";
			$where_videos.=" and video_id in (select video_id from $config[tables_prefix]tags_videos where tag_id=$tag_id) ";
			$storage[$object_id]['list_type']="tags";
			$storage[$object_id]['tag']=$data_temp['tag'];
			$storage[$object_id]['tag_info']=$data_temp;
			$smarty->assign('list_type',"tags");
			$smarty->assign('tag',$data_temp['tag']);
			$smarty->assign('tag_info',$data_temp);
		} else
		{
			return 'status_404';
		}
	}

	if ((isset($block_config['var_model_dir']) || isset($block_config['var_model_id'])) && ($_REQUEST[$block_config['var_model_dir']]<>'' || $_REQUEST[$block_config['var_model_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_model_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_model_dir']]),trim($_REQUEST[$block_config['var_model_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models_active_disabled] and model_id=?",intval($_REQUEST[$block_config['var_model_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$model_id=intval($data_temp["model_id"]);

			$data_temp['base_files_url']=$config['content_url_models'].'/'.$data_temp['model_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				$pattern=str_replace("%ID%",$data_temp['model_id'],str_replace("%DIR%",$data_temp['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
				$data_temp['view_page_url']="$config[project_url]/$pattern";
			}

			$where_albums.=" and album_id in (select album_id from $config[tables_prefix]models_albums where model_id=$model_id) ";
			$where_videos.=" and video_id in (select video_id from $config[tables_prefix]models_videos where model_id=$model_id) ";
			$storage[$object_id]['list_type']="models";
			$storage[$object_id]['model']=$data_temp['title'];
			$storage[$object_id]['model_info']=$data_temp;
			$smarty->assign('list_type',"models");
			$smarty->assign('model',$data_temp['title']);
			$smarty->assign('model_info',$data_temp);
		} else
		{
			return 'status_404';
		}
	}

	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		$q=trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]),false));
		$q=trim(str_replace('[dash]','-',str_replace('-',' ',str_replace('--','[dash]',str_replace('?','',$q)))));
		$where_temp_albums='';
		$where_temp_videos='';

		$escaped_q=sql_escape($q);

		if (isset($block_config['enable_search_on_categories']))
		{
			$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where $database_selectors[locale_field_title]=? or synonyms like '%$escaped_q%'",$q));
			if (count($category_ids)>0)
			{
				$category_ids=implode(',',array_map('intval',$category_ids));
				$where_temp_albums.=" or album_id in (select album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids))";
				$where_temp_videos.=" or video_id in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
			}
		}

		if (isset($block_config['enable_search_on_tags']))
		{
			$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags where $database_selectors[locale_field_tag]=? or synonyms like '%$escaped_q%'",$q));
			if (count($tag_ids)>0)
			{
				$tag_ids=implode(',',array_map('intval',$tag_ids));
				$where_temp_albums.=" or album_id in (select album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)) ";
				$where_temp_videos.=" or video_id in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)) ";
			}
		}

		if (isset($block_config['enable_search_on_cs']))
		{
			$content_source_id=mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where $database_selectors[locale_field_title]=?",$q));
			if ($content_source_id>0)
			{
				$where_temp_albums.=" or content_source_id='$content_source_id' ";
				$where_temp_videos.=" or content_source_id='$content_source_id' ";
			}
		}

		if (isset($block_config['enable_search_on_models']))
		{
			$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where $database_selectors[locale_field_title]=? or alias like '%$escaped_q%'",$q));
			if (count($model_ids)>0)
			{
				$model_ids=implode(',',array_map('intval',$model_ids));
				$where_temp_albums.=" or album_id in (select album_id from $config[tables_prefix]models_albums where model_id in ($model_ids)) ";
				$where_temp_videos.=" or video_id in (select video_id from $config[tables_prefix]models_videos where model_id in ($model_ids)) ";
			}
		}

		$unescaped_q=$q;
		$q=sql_escape($q);
		$search_scope=intval($block_config['search_scope']);
		if ($search_scope==2)
		{
			$where_albums.=" and (1=0 $where_temp_albums)";
			$where_videos.=" and (1=0 $where_temp_videos)";
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
					$where_albums.=" and (MATCH ($database_selectors[locale_field_title],$database_selectors[locale_field_description]) AGAINST ('$q' $search_modifier) $where_temp_albums)";
					$where_videos.=" and (MATCH ($database_selectors[locale_field_title],$database_selectors[locale_field_description]) AGAINST ('$q' $search_modifier) $where_temp_videos)";
				} else {
					$where_albums.=" and (MATCH ($database_selectors[locale_field_title]) AGAINST ('$q' $search_modifier) $where_temp_albums)";
					$where_videos.=" and (MATCH ($database_selectors[locale_field_title]) AGAINST ('$q' $search_modifier) $where_temp_videos)";
				}

				$storage[$object_id]['is_search_supports_relevance']="1";
				$smarty->assign('is_search_supports_relevance',"1");
			} else if ($block_config['search_method']==2)
			{
				$where_albums2='';
				$where_videos2='';
				$temp=explode(" ",$q);
				foreach ($temp as $temp_value)
				{
					if ($search_scope==0)
					{
						$where_albums2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or $database_selectors[locale_field_description] like '%$temp_value%'";
						$where_videos2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or $database_selectors[locale_field_description] like '%$temp_value%'";
					} else {
						$where_albums2.=" or $database_selectors[locale_field_title] like '%$temp_value%'";
						$where_videos2.=" or $database_selectors[locale_field_title] like '%$temp_value%'";
					}
				}
				if ($where_albums2<>'')
				{
					$where_albums2=substr($where_albums2,4);
				}
				if ($where_videos2<>'')
				{
					$where_videos2=substr($where_videos2,4);
				}
				$where_albums.=" and (($where_albums2) $where_temp_albums)";
				$where_videos.=" and (($where_videos2) $where_temp_videos)";
			} else
			{
				if ($search_scope==0)
				{
					$where_albums.=" and (($database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%') $where_temp_albums)";
					$where_videos.=" and (($database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%') $where_temp_videos)";
				} else {
					$where_albums.=" and (($database_selectors[locale_field_title] like '%$q%') $where_temp_albums)";
					$where_videos.=" and (($database_selectors[locale_field_title] like '%$q%') $where_temp_videos)";
				}
			}
		}

		$storage[$object_id]['list_type']="search";
		$storage[$object_id]['search_keyword']=$unescaped_q;
		$storage[$object_id]['url_prefix']="?$block_config[var_search]=$unescaped_q&";
		$smarty->assign('list_type',"search");
		$smarty->assign('search_keyword',$unescaped_q);
	}

	if ($block_config['skip_categories']<>'' && $storage[$object_id]['list_type']<>'categories' && $storage[$object_id]['list_type']<>'multi_categories' && $storage[$object_id]['list_type']<>'categories_groups' && $storage[$object_id]['list_type']<>'multi_categories_groups')
	{
		$category_ids=array_map("intval",explode(",",$block_config['skip_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where_albums.=" and album_id not in (select album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids))";
			$where_videos.=" and video_id not in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && $storage[$object_id]['list_type']<>'categories' && $storage[$object_id]['list_type']<>'multi_categories' && $storage[$object_id]['list_type']<>'categories_groups' && $storage[$object_id]['list_type']<>'multi_categories_groups')
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where_albums.=" and album_id in (select album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids))";
			$where_videos.=" and video_id in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
		}
	}

	if ($block_config['skip_tags']<>'' && $storage[$object_id]['list_type']<>'tags')
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where_albums.=" and album_id not in (select album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)) ";
			$where_videos.=" and video_id not in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && $storage[$object_id]['list_type']<>'tags')
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where_albums.=" and album_id in (select album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)) ";
			$where_videos.=" and video_id in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['skip_content_sources']<>'' && $storage[$object_id]['list_type']<>'content_sources' && $storage[$object_id]['list_type']<>'content_sources_groups')
	{
		$cs_ids=array_map("intval",explode(",",$block_config['skip_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where_albums.=" and content_source_id not in ($cs_ids) ";
			$where_videos.=" and content_source_id not in ($cs_ids) ";
		}
	}

	if ($block_config['show_content_sources']<>'' && $storage[$object_id]['list_type']<>'content_sources' && $storage[$object_id]['list_type']<>'content_sources_groups')
	{
		$cs_ids=array_map("intval",explode(",",$block_config['show_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where_albums.=" and content_source_id in ($cs_ids) ";
			$where_videos.=" and content_source_id in ($cs_ids) ";
		}
	}

	$data=list_contentMetaData();
	foreach ($data as $res)
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

	if ($sort_by_clear=='title')
	{
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if ($sort_by_clear=='object_viewed')
	{
		$sort_by="object_viewed desc";
	} elseif ($sort_by_clear=='post_date')
	{
		$sort_by="post_date $direction, object_id $direction";
	} elseif ($sort_by_clear=='most_favourited')
	{
		$sort_by="favourites_count $direction";
	} elseif ($sort_by_clear=='most_commented')
	{
		$sort_by="comments_count $direction";
	} elseif ($sort_by_clear=='most_purchased')
	{
		$sort_by="purchases_count $direction";
	}

	$where_videos_date="$database_selectors[where_videos]";
	$where_albums_date="$database_selectors[where_albums]";
	if (isset($block_config['mode_futures']))
	{
		$where_videos_date="$database_selectors[where_videos_future]";
		$where_albums_date="$database_selectors[where_albums_future]";
	}

	$sql_query="
		from (
			select
				$config[tables_prefix]videos.video_id as object_id,
				1 as object_type,
				$config[tables_prefix]videos.status_id as status_id,
				$config[tables_prefix]videos.is_private as is_private,
				$database_selectors[videos_selector_title] as title,
				$database_selectors[videos_selector_dir] as dir,
				$database_selectors[videos_selector_description] as description,
				$config[tables_prefix]videos.duration as duration,
				$config[tables_prefix]videos.content_source_id as content_source_id,
				$config[tables_prefix]videos.user_id as user_id,
				$config[tables_prefix]videos.screen_amount as images_amount,
				$config[tables_prefix]videos.screen_main as main_image,
				$config[tables_prefix]videos.server_group_id as server_group_id,
				$config[tables_prefix]videos.rating/$config[tables_prefix]videos.rating_amount as rating,
				$config[tables_prefix]videos.rating as rating_summary,
				$config[tables_prefix]videos.rating_amount as rating_votes,
				$config[tables_prefix]videos.video_viewed as object_viewed,
				$config[tables_prefix]videos.favourites_count as favourites_count,
				$config[tables_prefix]videos.comments_count as comments_count,
				$config[tables_prefix]videos.purchases_count as purchases_count,
				$database_selectors[generic_post_date_selector] as post_date,
				$config[tables_prefix]videos.last_time_view_date as last_time_view_date,
				$config[tables_prefix]videos.file_formats as files,
				$config[tables_prefix]videos.custom1 as custom1,
				$config[tables_prefix]videos.custom2 as custom2,
				$config[tables_prefix]videos.custom3 as custom3
			from
				$config[tables_prefix]videos
			where
				$where_videos_date $where $where_videos
			union all select
				$config[tables_prefix]albums.album_id as object_id,
				2 as object_type,
				$config[tables_prefix]albums.status_id as status_id,
				$config[tables_prefix]albums.is_private as is_private,
				$database_selectors[albums_selector_title] as title,
				$database_selectors[albums_selector_dir] as dir,
				$database_selectors[albums_selector_description] as description,
				0 as duration,
				$config[tables_prefix]albums.content_source_id as content_source_id,
				$config[tables_prefix]albums.user_id as user_id,
				$config[tables_prefix]albums.photos_amount as images_amount,
				'preview' as main_image,
				$config[tables_prefix]albums.server_group_id as server_group_id,
				$config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount as rating,
				$config[tables_prefix]albums.rating as rating_summary,
				$config[tables_prefix]albums.rating_amount as rating_votes,
				$config[tables_prefix]albums.album_viewed as object_viewed,
				$config[tables_prefix]albums.favourites_count as favourites_count,
				$config[tables_prefix]albums.comments_count as comments_count,
				$config[tables_prefix]albums.purchases_count as purchases_count,
				$database_selectors[generic_post_date_selector] as post_date,
				$config[tables_prefix]albums.last_time_view_date as last_time_view_date,
				$config[tables_prefix]albums.zip_files as files,
				$config[tables_prefix]albums.custom1 as custom1,
				$config[tables_prefix]albums.custom2 as custom2,
				$config[tables_prefix]albums.custom3 as custom3
			from
				$config[tables_prefix]albums
			where
				$where_albums_date $where $where_albums
		) X
	";
	if (isset($block_config['var_from']))
	{
		$total_count=mr2number(sql("select count(*) $sql_query"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$data=mr2array(sql("select * $sql_query order by $sort_by LIMIT $from, $block_config[items_per_page]"));
	} else {
		$data=mr2array(sql("select * $sql_query order by $sort_by LIMIT $block_config[items_per_page]"));
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['dir_path']=get_dir_by_id($data[$k]['object_id']);
		$data[$k]['duration_array']=get_duration_splitted($data[$k]['duration']);
		if ($data[$k]['object_type']=='1')
		{
			$data[$k]['formats']=get_video_formats($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

			$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
			if (isset($block_config['mode_futures']))
			{
				$data[$k]['view_page_url']='';
			}

			$screen_base_url=load_balance_screenshots_url();
			$data[$k]['screen_base_url']=$screen_base_url.'/'.$data[$k]['dir_path'].'/'.$data[$k]['object_id'];

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=".$data[$k]['object_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=".$data[$k]['object_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=".$data[$k]['object_id']." order by id asc"));
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
		} else {
			$data[$k]['zip_files']=get_album_zip_files($data[$k]['object_id'],$data[$k]['files'],$data[$k]['server_group_id']);

			$pattern=str_replace("%ID%",$data[$k]['object_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
			if (isset($block_config['mode_futures']))
			{
				$data[$k]['view_page_url']='';
			}

			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
			$data[$k]['image_base_url']="$lb_server[urls]/preview/";

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=".$data[$k]['object_id']." order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=".$data[$k]['object_id']." order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=".$data[$k]['object_id']." order by id asc"));
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
		if (isset($block_config['show_comments']))
		{
			$show_comments_limit='';
			if (intval($block_config['show_comments_count'])>0)
			{
				$show_comments_limit='limit '.intval($block_config['show_comments_count']);
			}
			$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=? and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['object_id'],$data[$k]['object_type'],date("Y-m-d H:i:s")));
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
			'content_source_info',
			'content_source_group_info'
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

function list_contentGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_content_source_dir=trim($_REQUEST[$block_config['var_content_source_dir']]);
	$var_content_source_id=trim($_REQUEST[$block_config['var_content_source_id']]);
	$var_content_source_group_dir=trim($_REQUEST[$block_config['var_content_source_group_dir']]);
	$var_content_source_group_id=trim($_REQUEST[$block_config['var_content_source_group_id']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_is_private=trim($_REQUEST[$block_config['var_is_private']]);
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);

	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_purchased']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	} else {
		if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
		{
			if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
			{
				return "runtime_nocache";
			}
		}
		$result="$from|$items_per_page|$var_category_dir|$var_category_id|$var_tag_dir|$var_tag_id|$var_model_dir|$var_model_id|$var_content_source_dir|$var_content_source_id|$var_content_source_group_dir|$var_content_source_group_id|$var_search|$var_sort_by|$var_is_private|$var_user_id";
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

function list_contentCacheControl($block_config)
{
	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_purchased']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	if (isset($block_config['show_private']) || isset($block_config['show_premium']))
	{
		return "status_specific";
	}
	return "default";
}

function list_contentAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_from_favourites' || $_REQUEST['action']=='delete_from_uploaded') && (is_array($_REQUEST['delete_video_ids']) || is_array($_REQUEST['delete_album_ids'])))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_contentShow($block_config,null);
	}
}

function list_contentPreProcess($block_config,$object_id)
{
	global $config;

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

function list_contentMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"12"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[object_id,title,dir,post_date,last_time_view_date,rating,object_viewed,most_favourited,most_commented,most_purchased]","is_required"=>1, "default_value"=>"post_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_description",  "group"=>"static_filters", "type"=>"",                          "is_required"=>0),
		array("name"=>"show_only_from_same_country", "group"=>"static_filters", "type"=>"",                          "is_required"=>0),
		array("name"=>"skip_categories",             "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",             "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                   "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_content_sources",        "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_content_sources",        "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",            "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",              "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"is_private",                  "group"=>"static_filters", "type"=>"CHOICE[0,1,2,0|1,0|2,1|2]", "is_required"=>0, "default_value"=>"1"),

		// dynamic filters
		array("name"=>"var_category_dir",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",              "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_tag_dir",                  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",                   "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_model_dir",                "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model"),
		array("name"=>"var_model_id",                 "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_id"),
		array("name"=>"var_content_source_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs"),
		array("name"=>"var_content_source_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_id"),
		array("name"=>"var_content_source_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_group"),
		array("name"=>"var_content_source_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_group_id"),
		array("name"=>"var_is_private",               "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"is_private"),

		// search
		array("name"=>"var_search",                   "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",                "group"=>"search", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"search_scope",                 "group"=>"search", "type"=>"CHOICE[0,1,2]",     "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_empty_404",             "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"search_empty_redirect_to",     "group"=>"search", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"enable_search_on_tags",        "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_categories",  "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_models",      "group"=>"search", "type"=>"",                  "is_required"=>0),
		array("name"=>"enable_search_on_cs",          "group"=>"search", "type"=>"",                  "is_required"=>0),

		// display modes
		array("name"=>"mode_favourites",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_uploaded",                "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_purchased",               "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_subscribed",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_futures",                 "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"fav_type",                     "group"=>"display_modes", "type"=>"INT",    "is_required"=>0, "default_value"=>"0"),
		array("name"=>"var_fav_type",                 "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"fav_type"),
		array("name"=>"var_playlist_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"playlist_id"),
		array("name"=>"var_user_id",                  "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",     "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_uploaded_content","group"=>"display_modes", "type"=>"",       "is_required"=>0),

		// subselects
		array("name"=>"show_content_source_info", "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_categories_info",     "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_tags_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_models_info",         "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_user_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments",            "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments_count",      "group"=>"subselects", "type"=>"INT", "is_required"=>0, "default_value"=>"2"),

		// access
		array("name"=>"show_private", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_premium", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
	);
}

function list_contentLegalRequestVariables()
{
	return array('action');
}

function list_contentJavascript($block_config)
{
	global $config;

	if (isset($block_config['mode_favourites']) && !isset($block_config['var_user_id']))
	{
		return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
	}
	return null;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>