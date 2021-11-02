<?php
function list_albumsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}
	$from=intval($_REQUEST[$block_config['var_from']]);
	if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}

	$sort_by_relevance='';

	if ($_REQUEST['action']=='delete_from_favourites' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$fav_type=intval($_REQUEST['fav_type']);
			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));

			sql_pr("delete from $config[tables_prefix]fav_albums where user_id=? and album_id in ($delete_ids) and fav_type=?",$user_id,$fav_type);

			fav_albums_changed($delete_ids);
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_albums')));
		}
	}

	if ($_REQUEST['action']=='delete_from_uploaded' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql_pr("select album_id from $config[tables_prefix]albums where user_id=? and album_id in ($delete_ids_str) and is_locked=0",$user_id));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);

				if (isset($block_config['allow_delete_uploaded_albums']))
				{
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
				} else {
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'delete_forbidden','block'=>'list_albums')));
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
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_albums')));
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
			$delete_ids=mr2array_list(sql_pr("select album_id from $config[tables_prefix]albums where user_id=? and album_id in ($delete_ids_str) and is_locked=0 and is_private=?",$user_id,$is_private_old));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);

				sql_pr("update $config[tables_prefix]albums set is_private=? where album_id in ($delete_ids_str)",$is_private_new);

				foreach ($delete_ids as $album_id)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=2, action_details='is_private', added_date=?",$user_id,$_SESSION['username'],$album_id,date("Y-m-d H:i:s"));
					if ($is_private_new==0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=9, user_id=?, album_id=?, added_date=?",$user_id,$album_id,date("Y-m-d H:i:s"));
					} else {
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=8, user_id=?, album_id=?, added_date=?",$user_id,$album_id,date("Y-m-d H:i:s"));
					}
				}
				sql_pr("update $config[tables_prefix]users set
						public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_albums_count=public_albums_count+private_albums_count+premium_albums_count
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
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_albums')));
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

	$formats_albums=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat"));

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

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]log_content_users on $config[tables_prefix]albums.album_id=$config[tables_prefix]log_content_users.album_id where $database_selectors[where_albums] and $config[tables_prefix]log_content_users.user_id=$user_id and $config[tables_prefix]log_content_users.is_old=0 $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$albums_selector=str_replace("user_id","$config[tables_prefix]albums.user_id",str_replace("added_date","$config[tables_prefix]albums.added_date",$database_selectors['albums']));
		$data=mr2array(sql("SELECT $albums_selector, $config[tables_prefix]log_content_users.added_date as visit_date from $config[tables_prefix]albums inner join $config[tables_prefix]log_content_users on $config[tables_prefix]albums.album_id=$config[tables_prefix]log_content_users.album_id where $database_selectors[where_albums] and $config[tables_prefix]log_content_users.user_id=$user_id and $config[tables_prefix]log_content_users.is_old=0 $where order by $config[tables_prefix]log_content_users.added_date desc LIMIT $from, $block_config[items_per_page]"));

		foreach ($data as $k=>$v)
		{
			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$album_id=$data[$k]['album_id'];
			$dir_path=get_dir_by_id($album_id);

			$data[$k]['dir_path']=$dir_path;

			$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
			{
				if (isset($block_config['show_image_info']))
				{
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
				} else {
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
				}
				foreach ($data[$k]['images'] as $k2=>$v2)
				{
					$formats=array();
					$image_formats=get_image_formats($album_id,$v2['image_formats']);
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							$format_item=array();
							$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
							$hash=md5($config['cv'].$file_path);

							$format_item['direct_url']="$lb_server[urls]/$file_path";
							$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

							foreach ($image_formats as $format_rec)
							{
								if ($format_rec['size']==$format['size'])
								{
									$format_item['dimensions']=$format_rec['dimensions'];
									$format_item['filesize']=$format_rec['file_size_string'];
									break;
								}
							}
							$formats[$format['size']]=$format_item;
						}
					}

					$format_item=array();
					$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
					$hash=md5($config['cv'].$file_path);
					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']=='source')
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats['source']=$format_item;

					$data[$k]['images'][$k2]['formats']=$formats;

					if ($v2['image_id']==$v['main_photo_id'])
					{
						$data[$k]['main_image']=$data[$k]['images'][$k2];
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
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
			}

			$data[$k]['preview_url']="$lb_server[urls]/preview";

			$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]users_purchases on $config[tables_prefix]albums.album_id=$config[tables_prefix]users_purchases.album_id where $database_selectors[where_albums_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$albums_selector=str_replace("user_id","$config[tables_prefix]albums.user_id",str_replace("added_date","$config[tables_prefix]albums.added_date",$database_selectors['albums']));
		$data=mr2array(sql("SELECT $albums_selector, $config[tables_prefix]users_purchases.added_date as purchase_date, $config[tables_prefix]users_purchases.expiry_date as expiry_date, $config[tables_prefix]users_purchases.tokens as tokens_spent from $config[tables_prefix]albums inner join $config[tables_prefix]users_purchases on $config[tables_prefix]albums.album_id=$config[tables_prefix]users_purchases.album_id where $database_selectors[where_albums_active_disabled_deleted] and $config[tables_prefix]users_purchases.user_id=$user_id and $config[tables_prefix]users_purchases.expiry_date>'$now_date' $where order by $config[tables_prefix]users_purchases.added_date desc LIMIT $from, $block_config[items_per_page]"));

		foreach ($data as $k=>$v)
		{
			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$album_id=$data[$k]['album_id'];
			$dir_path=get_dir_by_id($album_id);

			$data[$k]['dir_path']=$dir_path;

			$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
			{
				if (isset($block_config['show_image_info']))
				{
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
				} else {
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
				}
				foreach ($data[$k]['images'] as $k2=>$v2)
				{
					$formats=array();
					$image_formats=get_image_formats($album_id,$v2['image_formats']);
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							$format_item=array();
							$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
							$hash=md5($config['cv'].$file_path);

							$format_item['direct_url']="$lb_server[urls]/$file_path";
							$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

							foreach ($image_formats as $format_rec)
							{
								if ($format_rec['size']==$format['size'])
								{
									$format_item['dimensions']=$format_rec['dimensions'];
									$format_item['filesize']=$format_rec['file_size_string'];
									break;
								}
							}
							$formats[$format['size']]=$format_item;
						}
					}

					$format_item=array();
					$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
					$hash=md5($config['cv'].$file_path);
					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']=='source')
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats['source']=$format_item;

					$data[$k]['images'][$k2]['formats']=$formats;

					if ($v2['image_id']==$v['main_photo_id'])
					{
						$data[$k]['main_image']=$data[$k]['images'][$k2];
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
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
			}

			$data[$k]['preview_url']="$lb_server[urls]/preview";

			$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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

		$favourites_summary=mr2array(sql_pr("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $config[tables_prefix]fav_albums.user_id=? and $database_selectors[where_albums] group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc",$user_id));
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

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]fav_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]fav_albums.album_id where $database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=$user_id and $config[tables_prefix]fav_albums.fav_type=$fav_type $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$albums_selector=str_replace("user_id","$config[tables_prefix]albums.user_id",str_replace("added_date","$config[tables_prefix]albums.added_date",$database_selectors['albums']));
		$data=mr2array(sql("SELECT $albums_selector, $config[tables_prefix]fav_albums.added_date as added2fav_date from $config[tables_prefix]albums inner join $config[tables_prefix]fav_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]fav_albums.album_id where $database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=$user_id and $config[tables_prefix]fav_albums.fav_type=$fav_type $where order by $config[tables_prefix]fav_albums.added_date desc LIMIT $from, $block_config[items_per_page]"));

		foreach ($data as $k=>$v)
		{
			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$album_id=$data[$k]['album_id'];
			$dir_path=get_dir_by_id($album_id);

			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['time_passed_from_adding_to_fav']=get_time_passed($data[$k]['added2fav_date']);
			$data[$k]['dir_path']=$dir_path;

			$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
			{
				if (isset($block_config['show_image_info']))
				{
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
				} else {
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
				}
				foreach ($data[$k]['images'] as $k2=>$v2)
				{
					$formats=array();
					$image_formats=get_image_formats($album_id,$v2['image_formats']);
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							$format_item=array();
							$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
							$hash=md5($config['cv'].$file_path);

							$format_item['direct_url']="$lb_server[urls]/$file_path";
							$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

							foreach ($image_formats as $format_rec)
							{
								if ($format_rec['size']==$format['size'])
								{
									$format_item['dimensions']=$format_rec['dimensions'];
									$format_item['filesize']=$format_rec['file_size_string'];
									break;
								}
							}
							$formats[$format['size']]=$format_item;
						}
					}

					$format_item=array();
					$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
					$hash=md5($config['cv'].$file_path);
					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']=='source')
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats['source']=$format_item;

					$data[$k]['images'][$k2]['formats']=$formats;

					if ($v2['image_id']==$v['main_photo_id'])
					{
						$data[$k]['main_image']=$data[$k]['images'][$k2];
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
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
			}

			$data[$k]['preview_url']="$lb_server[urls]/preview";

			$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums_internal] and user_id=$user_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_internal] and user_id=$user_id $where order by added_date desc LIMIT $from, $block_config[items_per_page]"));

			$uploaded_summary=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]albums where $database_selectors[where_albums_internal] and user_id=$user_id group by is_private order by is_private desc"));
		} else {
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=$user_id $where"));
			if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
			$data=mr2array(sql("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=$user_id $where order by $database_selectors[generic_post_date_selector] desc LIMIT $from, $block_config[items_per_page]"));

			$uploaded_summary=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=$user_id group by is_private order by is_private desc"));
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
			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$album_id=$data[$k]['album_id'];
			$dir_path=get_dir_by_id($album_id);

			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['dir_path']=$dir_path;

			if (in_array($data[$k]['status_id'],array(0,1)))
			{
				$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$data[$k]['view_page_url']="$config[project_url]/$pattern";
			}

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
			{
				if (isset($block_config['show_image_info']))
				{
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
				} else {
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
				}
				foreach ($data[$k]['images'] as $k2=>$v2)
				{
					$formats=array();
					$image_formats=get_image_formats($album_id,$v2['image_formats']);
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							$format_item=array();
							$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
							$hash=md5($config['cv'].$file_path);

							$format_item['direct_url']="$lb_server[urls]/$file_path";
							$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

							foreach ($image_formats as $format_rec)
							{
								if ($format_rec['size']==$format['size'])
								{
									$format_item['dimensions']=$format_rec['dimensions'];
									$format_item['filesize']=$format_rec['file_size_string'];
									break;
								}
							}
							$formats[$format['size']]=$format_item;
						}
					}

					$format_item=array();
					$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
					$hash=md5($config['cv'].$file_path);
					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']=='source')
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats['source']=$format_item;

					$data[$k]['images'][$k2]['formats']=$formats;

					if ($v2['image_id']==$v['main_photo_id'])
					{
						$data[$k]['main_image']=$data[$k]['images'][$k2];
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
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
			}

			$data[$k]['preview_url']="$lb_server[urls]/preview";

			$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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
		$where_subscribed_cs="";
		$where_subscribed_models="";
		$where_subscribed_categories="";
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
			} elseif ($subscription['subscribed_type_id']==6)
			{
				$where_subscribed_categories.=",$subscription[subscribed_object_id]";
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
			$where_subscribed_models="album_id in (select album_id from $config[tables_prefix]models_albums where model_id in(-1$where_subscribed_models))";
		} else {
			$where_subscribed_models="1=0";
		}
		if ($where_subscribed_categories<>'')
		{
			$where_subscribed_categories="album_id in (select album_id from $config[tables_prefix]categories_albums where category_id in(-1$where_subscribed_categories))";
		} else {
			$where_subscribed_categories="1=0";
		}
		$where_subscribed="and ($where_subscribed_users or $where_subscribed_cs or $where_subscribed_models or $where_subscribed_categories)";

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] $where_subscribed $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$data=mr2array(sql("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] $where_subscribed $where order by $database_selectors[generic_post_date_selector] desc, album_id desc LIMIT $from, $block_config[items_per_page]"));

		foreach ($data as $k=>$v)
		{
			$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$album_id=$data[$k]['album_id'];
			$dir_path=get_dir_by_id($album_id);

			$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
			$data[$k]['dir_path']=$dir_path;

			$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";

			if (isset($block_config['show_categories_info']))
			{
				$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_tags_info']))
			{
				$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
			}
			if (isset($block_config['show_models_info']))
			{
				$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
			if (isset($block_config['show_flags_info']))
			{
				$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
				$data[$k]['flags']=array();
				foreach($flags as $flag)
				{
					$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
				}
			}
			if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
			{
				if (isset($block_config['show_image_info']))
				{
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
				} else {
					$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
				}
				foreach ($data[$k]['images'] as $k2=>$v2)
				{
					$formats=array();
					$image_formats=get_image_formats($album_id,$v2['image_formats']);
					foreach ($formats_albums as $format)
					{
						if ($format['group_id']==1)
						{
							$format_item=array();
							$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
							$hash=md5($config['cv'].$file_path);

							$format_item['direct_url']="$lb_server[urls]/$file_path";
							$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

							foreach ($image_formats as $format_rec)
							{
								if ($format_rec['size']==$format['size'])
								{
									$format_item['dimensions']=$format_rec['dimensions'];
									$format_item['filesize']=$format_rec['file_size_string'];
									break;
								}
							}
							$formats[$format['size']]=$format_item;
						}
					}

					$format_item=array();
					$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
					$hash=md5($config['cv'].$file_path);
					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']=='source')
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats['source']=$format_item;

					$data[$k]['images'][$k2]['formats']=$formats;

					if ($v2['image_id']==$v['main_photo_id'])
					{
						$data[$k]['main_image']=$data[$k]['images'][$k2];
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
				$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
			}

			$data[$k]['preview_url']="$lb_server[urls]/preview";

			$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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
		//1 - category
		//2 - tags
		//3 - cs
		//4 - model
		//5-6 - title
		//7 - user

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$result=null;
		if (isset($block_config['var_album_id']) && intval($_REQUEST[$block_config['var_album_id']])>0)
		{
			$result=sql_pr("select $database_selectors[albums] from $config[tables_prefix]albums where album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		} elseif (trim($_REQUEST[$block_config['var_album_dir']])<>'')
		{
			$result=sql_pr("select $database_selectors[albums] from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$mode_related_name='';
			$album_id=intval($data_temp["album_id"]);

			$where.=" and $config[tables_prefix]albums.album_id<>$album_id";
			if ($mode_related==1)
			{
				$mode_related_name='categories';

				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_albums where album_id=?",$album_id));
				if (count($category_ids)>0 && isset($block_config['mode_related_category_group_id']))
				{
					$category_ids=implode(',',$category_ids);
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_id in ($category_ids) and (category_group_id=? or category_group_id in (select category_group_id from $config[tables_prefix]categories_groups where external_id=?))",intval($block_config['mode_related_category_group_id']),trim($block_config['mode_related_category_group_id'])));
				}
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$join_tables[]="select distinct album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids)";
				}
			} elseif ($mode_related==2)
			{
				$mode_related_name='tags';

				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags_albums where album_id=?",$album_id));
				if (count($tag_ids)>0)
				{
					$tag_ids=implode(",",$tag_ids);
					$join_tables[]="select distinct album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)";
				}
			} elseif ($mode_related==3)
			{
				$mode_related_name='content_sources';

				$content_source_id=intval($data_temp["content_source_id"]);
				$where.=" and content_source_id=$content_source_id";
			} elseif ($mode_related==4)
			{
				$mode_related_name='models';

				$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_albums where album_id=?",$album_id));
				if (count($model_ids)>0 && isset($block_config['mode_related_model_group_id']))
				{
					$model_ids=implode(',',$model_ids);
					$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_id in ($model_ids) and (model_group_id=? or model_group_id in (select model_group_id from $config[tables_prefix]models_groups where external_id=?))",intval($block_config['mode_related_model_group_id']),trim($block_config['mode_related_model_group_id'])));
				}
				if (count($model_ids)>0)
				{
					$model_ids=implode(",",$model_ids);
					$join_tables[]="select distinct album_id from $config[tables_prefix]models_albums where model_id in ($model_ids)";
				}
			} elseif ($mode_related==5 || $mode_related==6)
			{
				$mode_related_name='title';

				$title=sql_escape($data_temp["title"]);

				$search_modifier='';
				if ($mode_related==6)
				{
					$search_modifier='WITH QUERY EXPANSION';
				}
				$where.=" and MATCH($database_selectors[locale_field_title]) AGAINST('$title' $search_modifier)";
				$sort_by_relevance="MATCH($database_selectors[locale_field_title]) AGAINST('$title' $search_modifier) desc";
			} elseif ($mode_related==7)
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
	} elseif (isset($block_config['mode_connected_video']))
	{
		$connected_video_id=0;
		if (isset($block_config['var_connected_video_id']) && intval($_REQUEST[$block_config['var_connected_video_id']])>0)
		{
			$connected_video_id=intval($_REQUEST[$block_config['var_connected_video_id']]);
		} elseif (trim($_REQUEST[$block_config['var_connected_video_dir']])<>'')
		{
			$connected_video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_connected_video_dir']]),trim($_REQUEST[$block_config['var_connected_video_dir']])));
		}
		$where.=" and connected_video_id=$connected_video_id";
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
		$where.=" and $config[tables_prefix]albums.user_id=".intval($_REQUEST[$block_config['var_user_id']]);
	}

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'content_source',      'plural'=>'content_sources',       'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_active_disabled'],        'where_plural'=>$database_selectors['where_content_sources'],         'base_files_url'=>$config['content_url_content_sources'],      'link_pattern'=>'WEBSITE_LINK_PATTERN_CS',    'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'content_source_group','plural'=>'content_sources_groups','title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_groups_active_disabled'], 'where_plural'=>$database_selectors['where_content_sources_groups']);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',               'plural'=>'models',                'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_models_active_disabled'],                 'where_plural'=>$database_selectors['where_models'],                  'base_files_url'=>$config['content_url_models'],               'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',         'plural'=>'models_groups',         'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_models_groups_active_disabled'],          'where_plural'=>$database_selectors['where_models_groups'],           'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',            'plural'=>'categories',            'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],             'where_plural'=>$database_selectors['where_categories'],              'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group',      'plural'=>'categories_groups',     'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'],      'where_plural'=>$database_selectors['where_categories_groups'],       'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',                 'plural'=>'tags',                  'title'=>'tag',  'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],                   'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_albums";
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
								$join_tables[]="select distinct album_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct album_id from $df_join_table where $df_id in ($ids_group)";
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
										$join_tables[]="select distinct album_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct album_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct album_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct album_id from $df_join_table where $df_id in ($df_ids_value)";
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
						$join_tables[]="select distinct album_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct album_id from $df_join_table where $df_id=$df_object_id";
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
			$where_temp_str='';

			$escaped_q=sql_escape($q);

			if (isset($block_config['enable_search_on_categories']))
			{
				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where $database_selectors[locale_field_title]=? or synonyms like '%$escaped_q%'",$q));
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',array_map('intval',$category_ids));
					$where_temp_str.=" or $config[tables_prefix]albums.album_id in (select album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids))";
				}
			}

			if (isset($block_config['enable_search_on_tags']))
			{
				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags where $database_selectors[locale_field_tag]=? or synonyms like '%$escaped_q%'",$q));
				if (count($tag_ids)>0)
				{
					$tag_ids=implode(',',array_map('intval',$tag_ids));
					$where_temp_str.=" or $config[tables_prefix]albums.album_id in (select album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids))";
				}
			}

			if (isset($block_config['enable_search_on_cs']))
			{
				$content_source_id=mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where $database_selectors[locale_field_title]=?",$q));
				if ($content_source_id>0)
				{
					$where_temp_str.=" or content_source_id='$content_source_id'";
				}
			}

			if (isset($block_config['enable_search_on_models']))
			{
				$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where $database_selectors[locale_field_title]=? or alias like '%$escaped_q%'",$q));
				if (count($model_ids)>0)
				{
					$model_ids=implode(',',array_map('intval',$model_ids));
					$where_temp_str.=" or $config[tables_prefix]albums.album_id in (select album_id from $config[tables_prefix]models_albums where model_id in ($model_ids))";
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
			$where.=" and $config[tables_prefix]albums.album_id not in (select album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct album_id from $config[tables_prefix]categories_albums where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]albums.album_id not in (select album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct album_id from $config[tables_prefix]tags_albums where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['skip_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$where.=" and $config[tables_prefix]albums.album_id not in (select album_id from $config[tables_prefix]models_albums where model_id in ($model_ids)) ";
		}
	}

	if ($block_config['show_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['show_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$join_tables[]="select distinct album_id from $config[tables_prefix]models_albums where model_id in ($model_ids)";
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
	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}
	if (isset($block_config['show_with_admin_flag']))
	{
		$flag_id=mr2number(sql_pr("select flag_id from $config[tables_prefix]flags where group_id=2 and external_id=?",$block_config['show_with_admin_flag']));
		if ($flag_id>0)
		{
			$where.=" and admin_flag_id=$flag_id";
		} else {
			$where.=" and 0=1";
		}
	}
	if (isset($block_config['skip_with_admin_flag']))
	{
		$flag_id=mr2number(sql_pr("select flag_id from $config[tables_prefix]flags where group_id=2 and external_id=?",$block_config['skip_with_admin_flag']));
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

	$metadata=list_albumsMetaData();
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
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating_week') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating_month') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating') {
		$sort_by="rating/rating_amount desc, rating_amount desc";
	} elseif ($sort_by_clear=='album_viewed_today' || $sort_by_clear=='viewed_today') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='album_viewed_week' || $sort_by_clear=='viewed_week') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='album_viewed_month' || $sort_by_clear=='viewed_month') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='album_viewed' || $sort_by_clear=='viewed') {
		$sort_by="album_viewed desc";
	} else {
		if ($sort_by_clear=='post_date') {$sort_by="$database_selectors[generic_post_date_selector] $direction, $config[tables_prefix]albums.album_id $direction";} else
		if ($sort_by_clear=='post_date_and_popularity') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, album_viewed desc";} else
		if ($sort_by_clear=='post_date_and_rating') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, rating/rating_amount desc, rating_amount desc";} else
		if ($sort_by_clear=='post_date_and_photos_amount') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, photos_amount desc";} else
		if ($sort_by_clear=='last_time_view_date_and_popularity') {$sort_by="date(last_time_view_date) $direction, album_viewed desc";} else
		if ($sort_by_clear=='last_time_view_date_and_rating') {$sort_by="date(last_time_view_date) $direction, rating/rating_amount desc, rating_amount desc";} else
		if ($sort_by_clear=='last_time_view_date_and_photos_amount') {$sort_by="date(last_time_view_date) $direction, photos_amount desc";} else
		if ($sort_by_clear=='most_favourited') {$sort_by="favourites_count $direction";} else
		if ($sort_by_clear=='most_commented') {$sort_by="comments_count $direction";} else
		if ($sort_by_clear=='most_purchased') {$sort_by="purchases_count $direction";}
	}

	$from_clause="$config[tables_prefix]albums";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.album_id=$config[tables_prefix]albums.album_id";
	}
	$where_clause="$database_selectors[where_albums]";
	if (isset($block_config['mode_futures']))
	{
		$where_clause="$database_selectors[where_albums_future]";
	}

	$total_count_pseudo_rand=0;
	if ($sort_by_clear=='pseudo_rand')
	{
		$limit=$block_config['items_per_page']*10;
		$album_ids=mr2array_list(sql("select SQL_CALC_FOUND_ROWS $config[tables_prefix]albums.album_id from $from_clause where $where_clause $where order by random1 limit $limit"));
		$total_count_pseudo_rand=mr2number(sql("select FOUND_ROWS()"));
		if (count($album_ids)>$block_config['items_per_page'])
		{
			$selected_ids=array();
			for ($i=1;$i<9999;$i++)
			{
				$rnd=mt_rand(0,count($album_ids)-1);
				if (!in_array($album_ids[$rnd],$selected_ids))
				{
					$selected_ids[]=intval($album_ids[$rnd]);
					if (count($selected_ids)>=$block_config['items_per_page'])
					{
						break;
					}
				}
			}
			$where_add=implode(',',$selected_ids);
		} else {
			$where_add=implode(',',$album_ids);
		}
		if (count($album_ids)>0)
		{
			$where=" and $config[tables_prefix]albums.album_id in (0,$where_add) ";
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
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$data=mr2array(sql("SELECT $database_selectors[albums] from $from_clause where $where_clause $where $sort_by LIMIT $from, $block_config[items_per_page]"));
	} else {
		$data=mr2array(sql("SELECT $database_selectors[albums] from $from_clause where $where_clause $where $sort_by LIMIT $block_config[items_per_page]"));
	}

	if ($storage[$object_id]['list_type']=="search")
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
					fwrite($fh,"$date|$q|0|1\r\n");
					fclose($fh);
				}
				if (isset($block_config['search_redirect_pattern']) && trim($block_config['search_redirect_pattern'])<>'')
				{
					$pattern=str_replace("%ID%",$data[0]['album_id'],str_replace("%DIR%",$data[0]['dir'],trim($block_config['search_redirect_pattern'])));
				} else {
					$pattern=str_replace("%ID%",$data[0]['album_id'],str_replace("%DIR%",$data[0]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
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

	foreach ($data as $k=>$v)
	{
		$lb_server=load_balance_server($data[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);

		$album_id=$data[$k]['album_id'];
		$dir_path=get_dir_by_id($album_id);

		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);
		$data[$k]['dir_path']=$dir_path;

		$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
		$data[$k]['view_page_url']="$config[project_url]/$pattern";
		if (isset($block_config['mode_futures']))
		{
			$data[$k]['view_page_url']='';
		}

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$album_id order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$album_id order by id asc"));
		}
		if (isset($block_config['show_models_info']))
		{
			$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$album_id order by id asc"));
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
		if (isset($block_config['show_flags_info']))
		{
			$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data[$k]['album_id']));
			$data[$k]['flags']=array();
			foreach($flags as $flag)
			{
				$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
			}
		}
		if (isset($block_config['show_image_info']) || isset($block_config['show_main_image_info']))
		{
			if (isset($block_config['show_image_info']))
			{
				$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id"));
			} else {
				$data[$k]['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where image_id=?",$v['main_photo_id']));
			}
			foreach ($data[$k]['images'] as $k2=>$v2)
			{
				$formats=array();
				$image_formats=get_image_formats($album_id,$v2['image_formats']);
				foreach ($formats_albums as $format)
				{
					if ($format['group_id']==1)
					{
						$format_item=array();
						$file_path="main/$format[size]/$dir_path/$album_id/$v2[image_id].jpg";
						$hash=md5($config['cv'].$file_path);

						$format_item['direct_url']="$lb_server[urls]/$file_path";
						$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";

						foreach ($image_formats as $format_rec)
						{
							if ($format_rec['size']==$format['size'])
							{
								$format_item['dimensions']=$format_rec['dimensions'];
								$format_item['filesize']=$format_rec['file_size_string'];
								break;
							}
						}
						$formats[$format['size']]=$format_item;
					}
				}

				$format_item=array();
				$file_path="sources/$dir_path/$album_id/$v2[image_id].jpg";
				$hash=md5($config['cv'].$file_path);
				$format_item['direct_url']="$lb_server[urls]/$file_path";
				$format_item['protected_url']="$config[project_url]/get_image/".$data[$k]['server_group_id']."/$hash/$file_path/";
				foreach ($image_formats as $format_rec)
				{
					if ($format_rec['size']=='source')
					{
						$format_item['dimensions']=$format_rec['dimensions'];
						$format_item['filesize']=$format_rec['file_size_string'];
						break;
					}
				}
				$formats['source']=$format_item;

				$data[$k]['images'][$k2]['formats']=$formats;

				if ($v2['image_id']==$v['main_photo_id'])
				{
					$data[$k]['main_image']=$data[$k]['images'][$k2];
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
			$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['album_id'],date("Y-m-d H:i:s")));
		}

		$data[$k]['preview_url']="$lb_server[urls]/preview";

		$data[$k]['zip_files']=get_album_zip_files($album_id,$data[$k]['zip_files'],$data[$k]['server_group_id']);
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
			fwrite($fh,"$date|$q|0|$search_results_count\r\n");
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

function list_albumsGetHash($block_config)
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
	$var_custom_flag1=trim($_REQUEST[$block_config['var_custom_flag1']]);
	$var_custom_flag2=trim($_REQUEST[$block_config['var_custom_flag2']]);
	$var_custom_flag3=trim($_REQUEST[$block_config['var_custom_flag3']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_mode_related=trim($_REQUEST[$block_config['var_mode_related']]);
	$var_album_dir=trim($_REQUEST[$block_config['var_album_dir']]);
	$var_album_id=trim($_REQUEST[$block_config['var_album_id']]);
	$var_is_private=trim($_REQUEST[$block_config['var_is_private']]);
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_connected_video_dir=trim($_REQUEST[$block_config['var_connected_video_dir']]);
	$var_connected_video_id=trim($_REQUEST[$block_config['var_connected_video_id']]);
	$var_post_date_from=trim($_REQUEST[$block_config['var_post_date_from']]);
	$var_post_date_to=trim($_REQUEST[$block_config['var_post_date_to']]);
	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_fav_type=trim($_REQUEST[$block_config['var_fav_type']]);

	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_purchased']) || isset($block_config['mode_history']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	} else {
		if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'') {
			if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
			{
				return "runtime_nocache";
			}
		}
		$result="$from|$items_per_page|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_content_source_dir|$var_content_source_id|$var_content_source_ids|$var_content_source_group_dir|$var_content_source_group_id|$var_content_source_group_ids|$var_custom_flag1|$var_custom_flag2|$var_custom_flag3|$var_search|$var_sort_by|$var_mode_related|$var_album_dir|$var_album_id|$var_is_private|$var_user_id|$var_title_section|$var_connected_video_dir|$var_connected_video_id|$var_post_date_from|$var_post_date_to|$var_fav_type";
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

function list_albumsCacheControl($block_config)
{
	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded']) || isset($block_config['mode_purchased']) || isset($block_config['mode_history']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	if (isset($block_config['show_private']) || isset($block_config['show_premium']))
	{
		return "status_specific";
	}
	return "default";
}

function list_albumsAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='add_to_favourites' && isset($_REQUEST['album_ids']))
	{
		if ($_SESSION['user_id']<1)
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_albums')));
		}
		if (!is_array($_REQUEST['album_ids']))
		{
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_albums')));
		}

		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		$user_id=intval($_SESSION['user_id']);
		$album_ids=array_map("intval",$_REQUEST['album_ids']);
		$fav_type=intval($_REQUEST['fav_type']);

		foreach ($album_ids as $album_id)
		{
			if ($album_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]fav_albums where user_id=? and album_id=? and fav_type=?",$user_id,$album_id,$fav_type))==0)
				{
					sql_pr("insert into $config[tables_prefix]fav_albums set album_id=?, user_id=?, fav_type=?, added_date=?",$album_id,$user_id,$fav_type,date("Y-m-d H:i:s"));
				}
			}
		}
		if (count($album_ids)>0)
		{
			$album_ids=implode(",",$album_ids);
			fav_albums_changed($album_ids);
		}
		async_return_request_status();
	} elseif (($_REQUEST['action']=='delete_from_favourites' || $_REQUEST['action']=='delete_from_uploaded' || $_REQUEST['action']=='delete_from_public' || $_REQUEST['action']=='delete_from_private') && is_array($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_albumsShow($block_config,null);
	}
}

function list_albumsPreProcess($block_config,$object_id)
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

function list_albumsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"12"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[album_id,title,dir,photos_amount,post_date,post_date_and_popularity,post_date_and_rating,post_date_and_photos_amount,last_time_view_date,last_time_view_date_and_popularity,last_time_view_date_and_rating,last_time_view_date_and_photos_amount,rating,rating_today,rating_week,rating_month,album_viewed,album_viewed_today,album_viewed_week,album_viewed_month,most_favourited,most_commented,most_purchased,custom1,custom2,custom3,pseudo_rand]","is_required"=>1, "default_value"=>"post_date"),
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
		array("name"=>"skip_users",                  "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_users",                  "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",            "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",              "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"is_private",                  "group"=>"static_filters", "type"=>"CHOICE[0,1,2,0|1,0|2,1|2]", "is_required"=>0, "default_value"=>"1"),

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
		array("name"=>"var_is_private",               "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"is_private"),
		array("name"=>"var_post_date_from",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_from"),
		array("name"=>"var_post_date_to",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_to"),
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
		array("name"=>"enable_search_on_custom_fields", "group"=>"search", "type"=>"",                  "is_required"=>0),

		// related
		array("name"=>"mode_related",                   "group"=>"related", "type"=>"CHOICE[1,2,3,4,5,6,7]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_album_dir",                  "group"=>"related", "type"=>"STRING",                "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_album_id",                   "group"=>"related", "type"=>"STRING",                "is_required"=>0, "default_value"=>"id"),
		array("name"=>"mode_related_category_group_id", "group"=>"related", "type"=>"STRING",                "is_required"=>0),
		array("name"=>"mode_related_model_group_id",    "group"=>"related", "type"=>"STRING",                "is_required"=>0),
		array("name"=>"var_mode_related",               "group"=>"related", "type"=>"STRING",                "is_required"=>0, "default_value"=>"mode_related"),

		// connected video
		array("name"=>"mode_connected_video",    "group"=>"connected_videos", "type"=>"",       "is_required"=>0),
		array("name"=>"var_connected_video_dir", "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_connected_video_id",  "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// display modes
		array("name"=>"mode_favourites",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_uploaded",                "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_purchased",               "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_history",                 "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_subscribed",              "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_futures",                 "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"fav_type",                     "group"=>"display_modes", "type"=>"INT",    "is_required"=>0, "default_value"=>"0"),
		array("name"=>"var_fav_type",                 "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"fav_type"),
		array("name"=>"var_user_id",                  "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",     "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_uploaded_albums", "group"=>"display_modes", "type"=>"",       "is_required"=>0),

		// subselects
		array("name"=>"show_content_source_info", "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_categories_info",     "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_tags_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_models_info",         "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_user_info",           "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_flags_info",          "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_image_info",          "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_main_image_info",     "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments",            "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments_count",      "group"=>"subselects", "type"=>"INT", "is_required"=>0, "default_value"=>"2"),

		// access
		array("name"=>"show_private", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_premium", "group"=>"access", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
	);
}

function list_albumsLegalRequestVariables()
{
	return array('action');
}

function list_albumsJavascript($block_config)
{
	global $config;

	if ((isset($block_config['mode_favourites']) || isset($block_config['mode_uploaded'])) && !isset($block_config['var_user_id']))
	{
		return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
	}
	return null;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>