<?php
function album_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	if (isset($block_config['var_album_id']) && intval($_REQUEST[$block_config['var_album_id']])>0)
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_admin] and album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_deleted] and album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==2)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_all] and album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_disabled_deleted] and album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_album_dir']])<>'' && trim($_REQUEST[$block_config['var_album_dir']])<>$data['dir'])
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['album_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_album_dir']])<>'')
	{
		if ($_SESSION['userdata']['user_id']>0)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_admin] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==1)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_deleted] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		} elseif (intval($website_ui_data['DISABLED_CONTENT_AVAILABILITY'])==2)
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_all] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		} else
		{
			$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_disabled_deleted] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		}
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	if (isset($block_config['var_album_image_id']) && isset($_REQUEST[$block_config['var_album_image_id']]))
	{
		$album_image_id=intval($_REQUEST[$block_config['var_album_image_id']]);
		if ($album_image_id>0)
		{
			$data['image_info']=mr2array_single(sql("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id='$data[album_id]' and image_id='$album_image_id'"));
			if ($data['image_info']['image_id']<1)
			{
				return 'status_404';
			}
		} else {
			return 'status_404';
		}
	} else {
		$data['image_info']=mr2array_single(sql("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id='$data[album_id]' and image_id='$data[main_photo_id]'"));
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
			if (album_viewHasPremiumAccessByTokens(0,$data['user_id']))
			{
				$data['user']['is_purchased']=1;
			}
		}
		$data['is_favourited']=0;
		$data['favourite_types']=array();
		$fav_types=mr2array_list(sql_pr("select distinct fav_type from $config[tables_prefix]fav_albums where user_id=? and album_id=?",$_SESSION['user_id'],$data['album_id']));
		if (count($fav_types)>0)
		{
			$data['is_favourited']=1;
			$data['favourite_types']=$fav_types;
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

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.album_id=?) as votes from $config[tables_prefix]flags where group_id=2",$data['album_id']));
	$data['flags']=array();
	foreach($flags as $flag)
	{
		$data['flags'][$flag['external_id']]=$flag['votes'];
	}

	$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_albums.image_id=?) as votes from $config[tables_prefix]flags where group_id=2",intval($data['image_info']['image_id'])));
	$data['image_info']['flags']=array();
	foreach($flags as $flag)
	{
		$data['image_info']['flags'][$flag['external_id']]=$flag['votes'];
	}

	if ($data['admin_flag_id']>0)
	{
		$data['admin_flag']=mr2string(sql_pr("select external_id from $config[tables_prefix]flags where flag_id=?",$data['admin_flag_id']));
	}
	unset($data['admin_flag_id']);

	$album_log=view_albumGetAlbumLog($block_config);
	if (is_array($album_log))
	{
		if (count($album_log['album_log'])>=$album_log['albums_amount'])
		{
			$storage[$object_id]['is_limit_over']=1;
			$smarty->assign("is_limit_over",1);
		}
		$storage[$object_id]['album_log_albums_amount']=count($album_log['album_log']);
		$smarty->assign("album_log_albums_amount",count($album_log['album_log']));
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

	$image_id=$data['image_info']['image_id'];
	$album_id=$data['album_id'];
	$dir_path=get_dir_by_id($album_id);
	$data['dir_path']=$dir_path;

	$lb_server=load_balance_server($data['server_group_id'],$cluster_servers,$cluster_servers_weights);

	$formats_albums=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat"));
	$formats=array();
	$image_formats=get_image_formats($album_id,$data['image_info']['image_formats']);
	foreach ($formats_albums as $format)
	{
		if ($format['group_id']==1)
		{
			$format_item=array();
			$file_path="main/$format[size]/$dir_path/$album_id/$image_id.jpg";
			$hash=md5($config['cv'].$file_path);

			$format_item['direct_url']="$lb_server[urls]/$file_path";
			$format_item['protected_url']="$config[project_url]/get_image/$data[server_group_id]/$hash/$file_path/";
			if ($data['user_id']==$_SESSION['user_id'] || $data['user']['is_purchased']==1)
			{
				$format_item['protected_url'].='?ov='.md5($config['cv'].$_SESSION['user_id']);
			}

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
		} else
		{
			$file_path="preview/$format[size]/$dir_path/$album_id/preview.jpg";
			$data['preview_formats'][$format['size']]="$lb_server[urls]/$file_path";
		}
	}

	$format_item=array();
	$file_path="sources/$dir_path/$album_id/$image_id.jpg";
	$hash=md5($config['cv'].$file_path);
	$format_item['direct_url']="$lb_server[urls]/$file_path";
	$format_item['protected_url']="$config[project_url]/get_image/$data[server_group_id]/$hash/$file_path/";
	if ($data['user_id']==$_SESSION['user_id'] || $data['user']['is_purchased']==1)
	{
		$format_item['protected_url'].='?ov='.md5($config['cv'].$_SESSION['user_id']);
	}
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

	$data['image_info']['formats']=$formats;
	$data['image_info']['time_passed_from_adding']=get_time_passed($data['image_info']['added_date']);

	$data['zip_files']=get_album_zip_files($album_id,$data['zip_files'],$data['server_group_id']);
	foreach ($data['zip_files'] as $k=>$v)
	{
		$data['zip_files'][$k]['file_url'].="?download=true&download_filename=$data[dir].zip";
		if ($data['user_id']==$_SESSION['user_id'] || $data['user']['is_purchased']==1)
		{
			$data['zip_files'][$k]['file_url'].='&ov='.md5($config['cv'].$_SESSION['user_id']);
		}
	}

	if (isset($block_config['var_from']))
	{
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums_images where album_id=$album_id"));
		$from=intval($_REQUEST[$block_config['var_from']]);
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}
		$items_per_page=intval($block_config['items_per_page']);

		$data['images']=mr2array(sql("SELECT $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id LIMIT $from, $items_per_page"));

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

		$data['images']=mr2array(sql("SELECT $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id $limit"));

		$storage[$object_id]['items_per_page']=$items_per_page;
		$smarty->assign("items_per_page",$items_per_page);
	}
	foreach ($data['images'] as $k=>$v)
	{
		$lb_server=load_balance_server($data['server_group_id'],$cluster_servers,$cluster_servers_weights);

		$formats=array();
		$image_formats=get_image_formats($album_id,$v['image_formats']);
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1)
			{
				$format_item=array();
				$file_path="main/$format[size]/$dir_path/$album_id/$v[image_id].jpg";
				$hash=md5($config['cv'].$file_path);

				$format_item['direct_url']="$lb_server[urls]/$file_path";
				$format_item['protected_url']="$config[project_url]/get_image/$data[server_group_id]/$hash/$file_path/";
				if ($data['user_id']==$_SESSION['user_id'] || $data['user']['is_purchased']==1)
				{
					$format_item['protected_url'].='?ov='.md5($config['cv'].$_SESSION['user_id']);
				}

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
		$file_path="sources/$dir_path/$album_id/$v[image_id].jpg";
		$hash=md5($config['cv'].$file_path);
		$format_item['direct_url']="$lb_server[urls]/$file_path";
		$format_item['protected_url']="$config[project_url]/get_image/$data[server_group_id]/$hash/$file_path/";
		if ($data['user_id']==$_SESSION['user_id'] || $data['user']['is_purchased']==1)
		{
			$format_item['protected_url'].='?ov='.md5($config['cv'].$_SESSION['user_id']);
		}
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

		$data['images'][$k]['formats']=$formats;
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_albums on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_albums.category_id where $database_selectors[where_categories] and album_id=$data[album_id] order by id asc"));
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

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_albums on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_albums.tag_id where $database_selectors[where_tags] and album_id=$data[album_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_albums on $config[tables_prefix]models.model_id=$config[tables_prefix]models_albums.model_id where $database_selectors[where_models] and album_id=$data[album_id] order by id asc"));
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
			$result_next=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and $database_selectors[generic_post_date_selector]<? and album_id<>? order by $database_selectors[generic_post_date_selector] desc, album_id desc limit 1",$data['post_date'],intval($data['album_id']));
			$result_previous=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and $database_selectors[generic_post_date_selector]>? and album_id<>? order by $database_selectors[generic_post_date_selector] asc, album_id asc limit 1",$data['post_date'],intval($data['album_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==2 && $data['content_source_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and content_source_id=? and $database_selectors[generic_post_date_selector]<? and album_id<>? order by $database_selectors[generic_post_date_selector] desc, album_id desc limit 1",$data['content_source_id'],$data['post_date'],intval($data['album_id']));
			$result_previous=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and content_source_id=? and $database_selectors[generic_post_date_selector]>? and album_id<>? order by $database_selectors[generic_post_date_selector] asc, album_id asc limit 1",$data['content_source_id'],$data['post_date'],intval($data['album_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==3)
		{
			$result_next=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=? and $database_selectors[generic_post_date_selector]<? and album_id<>? order by $database_selectors[generic_post_date_selector] desc, album_id desc limit 1",$data['user_id'],$data['post_date'],intval($data['album_id']));
			$result_previous=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and user_id=? and $database_selectors[generic_post_date_selector]>? and album_id<>? order by $database_selectors[generic_post_date_selector] asc, album_id asc limit 1",$data['user_id'],$data['post_date'],intval($data['album_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$pattern=str_replace("%ID%",$object_next['album_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$object_next['view_page_url']="$config[project_url]/$pattern";
			$object_next['preview_url']="$lb_server[urls]/preview";

			$smarty->assign("next_album",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$pattern=str_replace("%ID%",$object_previous['album_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
			$object_previous['view_page_url']="$config[project_url]/$pattern";
			$object_previous['preview_url']="$lb_server[urls]/preview";

			$smarty->assign("previous_album",$object_previous);
		}
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if ($data['is_private']==2)
	{
		if (intval($memberzone_data['ENABLE_TOKENS_PREMIUM_ALBUM'])==1)
		{
			if (intval($data['tokens_required'])==0)
			{
				$data['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_PREMIUM_ALBUM']);
			}
			$data['tokens_required_period']=intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']);
		} else {
			$data['tokens_required']=0;
		}
	} else {
		if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM'])==1)
		{
			if (intval($data['tokens_required'])==0)
			{
				$data['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']);
			}
			$data['tokens_required_period']=intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']);
		} else {
			$data['tokens_required']=0;
		}
	}

	$data['is_purchased_album']=0;
	if ($_SESSION['user_id']==$data['user_id'] || album_viewHasPremiumAccessByTokens($data['album_id'],$data['user_id']))
	{
		$data['is_purchased_album']=1;

		$storage[$object_id]['is_limit_over']=0;
		$smarty->assign("is_limit_over",0);
	}

	$data['can_watch']=1;
	if ($data['is_private']==0 && $_SESSION['user_id']<>$data['user_id'])
	{
		$data['can_watch']=0;
		$access_option=intval($memberzone_data['PUBLIC_ALBUMS_ACCESS']);
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
				if ($_SESSION['status_id']==3 || $data['is_purchased_album']==1)
				{
					$data['can_watch']=1;
				}
				break;
		}
	}
	if ($data['is_private']==1 && $_SESSION['user_id']<>$data['user_id'])
	{
		$data['can_watch']=0;
		$access_option=intval($memberzone_data['PRIVATE_ALBUMS_ACCESS']);
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
				if ($_SESSION['status_id']==3 || $data['is_purchased_album']==1)
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
		$access_option=intval($memberzone_data['PREMIUM_ALBUMS_ACCESS']);
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
				if ($_SESSION['status_id']==3 || $data['is_purchased_album']==1)
				{
					$data['can_watch']=1;
				}
				break;
		}
	}

	$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['album_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));

	$storage[$object_id]['album_id']=$data['album_id'];
	$storage[$object_id]['dir_path']=$data['dir_path'];
	$storage[$object_id]['image_id']=$data['image_info']['image_id'];
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
	$storage[$object_id]['rating']=$data['rating'];
	$storage[$object_id]['rating_amount']=$data['rating_amount'];
	$storage[$object_id]['album_viewed']=$data['album_viewed'];
	$storage[$object_id]['post_date']=$data['post_date'];
	$storage[$object_id]['photos_amount']=$data['photos_amount'];
	$storage[$object_id]['tags']=$data['tags'];
	$storage[$object_id]['tags_as_string']=$data['tags_as_string'];
	$storage[$object_id]['category']=$data['category'];
	$storage[$object_id]['categories']=$data['categories'];
	$storage[$object_id]['categories_as_string']=$data['categories_as_string'];
	$storage[$object_id]['models']=$data['models'];
	$storage[$object_id]['models_as_string']=$data['models_as_string'];
	$storage[$object_id]['comments_count']=$data['comments_count'];
	$storage[$object_id]['user_id']=$data['user_id'];
	$storage[$object_id]['username']=$data['username'];
	$storage[$object_id]['user_avatar']=$data['user_avatar'];
	$storage[$object_id]['status_id']=$data['status_id'];
	$storage[$object_id]['is_private']=$data['is_private'];
	$storage[$object_id]['content_source']=$data['content_source'];
	$storage[$object_id]['content_source_as_string']=$data['content_source_as_string'];
	$storage[$object_id]['content_source_group']=$data['content_source_group'];
	$storage[$object_id]['custom1']=$data['custom1'];
	$storage[$object_id]['custom2']=$data['custom2'];
	$storage[$object_id]['custom3']=$data['custom3'];
	$storage[$object_id]['canonical_url']=$data['canonical_url'];
	$storage[$object_id]['admin_flag']=$data['admin_flag'];
	$storage[$object_id]['preview_formats']=$data['preview_formats'];
	foreach ($data['image_info']['formats'] as $size=>$format_rec)
	{
		$storage[$object_id]['preview_formats_image'][$size]=$format_rec['direct_url'];
	}

	$smarty->assign("session_name",session_name());
	$smarty->assign("data",$data);

	if (isset($block_config['show_stats']))
	{
		$smarty->assign("stats",mr2array(sql_pr("select added_date, viewed, unique_viewed from $config[tables_prefix]stats_albums where album_id=? order by added_date asc",$data['album_id'])));
	}

	if ($data['status_id']==2 || $data['status_id']==3)
	{
		$smarty->caching=0;
		return 'nocache';
	}

	return '';
}

function album_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_album_dir']]);
	$id=intval($_REQUEST[$block_config['var_album_id']]);
	$image_id=intval($_REQUEST[$block_config['var_album_image_id']]);
	$from=intval($_REQUEST[$block_config['var_from']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$is_limit_over=0;
	$album_log=view_albumGetAlbumLog($block_config);
	if (is_array($album_log))
	{
		if (count($album_log['album_log'])>=$album_log['albums_amount'])
		{
			$is_limit_over=1;
		}
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('albums_info','album',$id,$dir);
	}

	return "$dir|$id|$image_id|$from|$is_limit_over|$current_version";
}

function album_viewCacheControl($block_config)
{
	return "user_nocache";
}

function album_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_album_dir",      "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_album_id",       "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),
		array("name"=>"var_album_image_id", "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"image_id"),

		// pagination
		array("name"=>"items_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"0"),
		array("name"=>"links_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",       "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,2,3]", "is_required"=>0),
		array("name"=>"show_stats",                  "group"=>"additional_data", "type"=>"",              "is_required"=>0),

		// limit views
		array("name"=>"limit_unknown_user",   "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
		array("name"=>"limit_member",         "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
		array("name"=>"limit_premium_member", "group"=>"limit_views", "type"=>"INT_PAIR", "is_required"=>0, "default_value"=>""),
	);
}

function album_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingAlbumView.js?v={$config['project_version']}";
}

function album_viewPreProcess($block_config,$object_id)
{
	global $config, $database_selectors;

	$album_id=0;
	$album_dir='';
	if (intval($_REQUEST[$block_config['var_album_id']])>0)
	{
		if ($_REQUEST['no_stats']!='true')
		{
			file_put_contents("$config[project_path]/admin/data/stats/albums_id.dat", intval($_REQUEST[$block_config['var_album_id']])."||".intval($_SESSION['user_id'])."||0||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||0\r\n", LOCK_EX | FILE_APPEND);
		}
		$album_id=intval($_REQUEST[$block_config['var_album_id']]);
	} elseif (trim($_REQUEST[$block_config['var_album_dir']])<>'')
	{
		if ($_REQUEST['no_stats']!='true')
		{
			file_put_contents("$config[project_path]/admin/data/stats/albums_dir.dat", trim($_REQUEST[$block_config['var_album_dir']])."||".intval($_SESSION['user_id'])."||0||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||0\r\n", LOCK_EX | FILE_APPEND);
		}
		$album_dir=trim($_REQUEST[$block_config['var_album_dir']]);
	}

	$album_log = view_albumGetAlbumLog($block_config);
	if (is_array($album_log))
	{
		if (count($album_log['album_log']) < $album_log['albums_amount'])
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$now_date = date("Y-m-d H:i:s");
			if ($album_id > 0)
			{
				sql_pr("insert into $config[tables_prefix]albums_visits set ip=?, album_id=?, flag=1, added_date='$now_date'", ip2int($_SERVER['REMOTE_ADDR']), $album_id);
			} elseif ($album_dir != '')
			{
				sql_pr("insert into $config[tables_prefix]albums_visits set ip=?, album_id=(select album_id from $config[tables_prefix]albums where (dir=? or $database_selectors[where_locale_dir])), flag=1, added_date='$now_date'", ip2int($_SERVER['REMOTE_ADDR']), $album_dir, $album_dir);
			}
		}
	}

	if ($_REQUEST['no_stats']!='true')
	{
		$image_id=intval($_REQUEST[$block_config['var_album_image_id']]);
		$album_id=intval($_REQUEST[$block_config['var_album_id']]);
		if ($image_id>0)
		{
			$fh=fopen("$config[project_path]/admin/data/stats/images_id.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,"i||$image_id\r\n");
			fclose($fh);
		} elseif ($album_id>0) {
			$fh=fopen("$config[project_path]/admin/data/stats/images_id.dat","a+");
			flock($fh,LOCK_EX);
			fwrite($fh,"a||$album_id\r\n");
			fclose($fh);
		}
	}
}

function album_viewAsync($block_config)
{
	global $config,$stats_params;

	if ($_REQUEST['action']=='rate' && (intval($_REQUEST['album_image_id'])>0 || intval($_REQUEST['album_id'])>0))
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			if (intval($_REQUEST['album_image_id'])>0)
			{
				$album_image_id=intval($_REQUEST['album_image_id']);
				$album_id=mr2number(sql("select album_id from $config[tables_prefix]albums_images where image_id=$album_image_id"));
				if ($album_id==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
				}
			} else {
				$album_id=intval($_REQUEST['album_id']);
				$album_image_id=mr2number(sql("select main_photo_id from $config[tables_prefix]albums where album_id=$album_id"));
				if ($album_image_id==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
				}
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where image_id=? and ip=?",$album_image_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'album_view')));
			} else {
				$now_date=date("Y-m-d");
				sql_pr("insert into $config[tables_prefix]rating_history set album_id=?, image_id=?, ip=?, added_date=?",$album_id,$album_image_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]albums_images set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where image_id=$album_image_id");
				sql("update $config[tables_prefix]albums set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where album_id=$album_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_albums_count=ratings_albums_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
				if (intval($stats_settings['collect_albums_stats'])==1)
				{
					if (sql_update("update $config[tables_prefix]stats_albums set rating=rating+$rating, rating_amount=rating_amount+1 where album_id=$album_id and added_date='$now_date'")==0)
					{
						sql("insert into $config[tables_prefix]stats_albums set rating=$rating, rating_amount=1, album_id=$album_id, added_date='$now_date'");
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]albums where album_id=$album_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'album_view')));
		}
	} elseif ($_REQUEST['action']=='flag' && (intval($_REQUEST['album_image_id'])>0 || intval($_REQUEST['album_id'])>0))
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			if (intval($_REQUEST['album_image_id'])>0)
			{
				$album_image_id=intval($_REQUEST['album_image_id']);
				$album_id=mr2number(sql("select album_id from $config[tables_prefix]albums_images where image_id=$album_image_id"));
				if ($album_id==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
				}
			} else {
				$album_id=intval($_REQUEST['album_id']);
				$album_image_id=mr2number(sql("select main_photo_id from $config[tables_prefix]albums where album_id=$album_id"));
				if ($album_image_id==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
				}
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=2 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'album_view')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'album_view')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and album_id=? and image_id=? and ip=?",$flag['flag_id'],$album_id,$album_image_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'album_view')));
				} else {
					$now_date=date("Y-m-d");
					sql_pr("insert into $config[tables_prefix]flags_history set album_id=?, image_id=?, flag_id=?, ip=?, added_date=?",$album_id,$album_image_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_albums set votes=votes+1 where album_id=? and image_id=? and flag_id=?",$album_id,$album_image_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_albums set votes=1, album_id=?, image_id=?, flag_id=?",$album_id,$album_image_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]albums_images set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where image_id=$album_image_id");
						sql("update $config[tables_prefix]albums set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where album_id=$album_id");
						if (intval($_SESSION['user_id'])>0)
						{
							sql_pr("update $config[tables_prefix]users set ratings_albums_count=ratings_albums_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
						}

						$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
						if (intval($stats_settings['collect_albums_stats'])==1)
						{
							if (sql_update("update $config[tables_prefix]stats_albums set rating=rating+$rating, rating_amount=rating_amount+1 where album_id=$album_id and added_date='$now_date'")==0)
							{
								sql("insert into $config[tables_prefix]stats_albums set rating=$rating, rating_amount=1, album_id=$album_id, added_date='$now_date'");
							}
						}
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, album_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$album_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, album_id=?, image_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$album_id,$album_image_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_albums where album_id=$album_id and flag_id=?) as flags from $config[tables_prefix]albums where album_id=$album_id",$flag['flag_id']));
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
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'album_view')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'album_view')));
		}
	} elseif ($_REQUEST['action']=='add_to_favourites' && intval($_REQUEST['album_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$album_id=intval($_REQUEST['album_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where album_id=$album_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
			}

			$fav_type=intval($_REQUEST['fav_type']);

			if (mr2number(sql("select count(*) from $config[tables_prefix]fav_albums where user_id=$user_id and album_id=$album_id and fav_type=$fav_type"))==0)
			{
				sql_pr("insert into $config[tables_prefix]fav_albums set album_id=$album_id, user_id=$user_id, fav_type=$fav_type, added_date=?",date("Y-m-d H:i:s"));
				fav_albums_changed($album_id);
			}

			$result_data=array();
			$result_data['favourites_total']=intval($_SESSION['favourite_albums_amount']);
			$result_data['favourites_type']=intval($_SESSION['favourite_albums_summary'][$fav_type]['amount']);
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'album_view')));
		}
	} elseif ($_REQUEST['action']=='delete_from_favourites' && intval($_REQUEST['album_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$album_id=intval($_REQUEST['album_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where album_id=$album_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_album','block'=>'album_view')));
			}

			$fav_type=intval($_REQUEST['fav_type']);

			sql("delete from $config[tables_prefix]fav_albums where album_id=$album_id and user_id=$user_id and fav_type=$fav_type");
			fav_albums_changed($album_id);

			$result_data=array();
			$result_data['favourites_total']=intval($_SESSION['favourite_albums_amount']);
			$result_data['favourites_type']=intval($_SESSION['favourite_albums_summary'][$fav_type]['amount']);
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'album_view')));
		}
	} elseif ($_REQUEST['action']=='purchase_album' && intval($_REQUEST['album_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$album_id=intval($_REQUEST['album_id']);

			$album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=?",$album_id));
			if (intval($album['album_id'])==0 || intval($album['user_id'])==$user_id)
			{
				async_return_request_status(array(array('error_code'=>'invalid_album','error_field_code'=>'error_1','block'=>'album_view')));
			}
			$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if ($album['is_private']==2)
			{
				if (intval($memberzone_data['ENABLE_TOKENS_PREMIUM_ALBUM'])==1)
				{
					if (intval($album['tokens_required'])==0)
					{
						$album['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_PREMIUM_ALBUM']);
					}
				} else {
					$album['tokens_required']=0;
				}
			} else {
				if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM'])==1)
				{
					if (intval($album['tokens_required'])==0)
					{
						$album['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']);
					}
				} else {
					$album['tokens_required']=0;
				}
			}
			$tokens=intval($album['tokens_required']);
			if ($tokens==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_album','error_field_code'=>'error_1','block'=>'album_view')));
			}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where user_id=$user_id and album_id=$album_id and expiry_date>?",date("Y-m-d H:i:s")))==0)
			{
				$tokens_available=mr2number(sql("select tokens_available from $config[tables_prefix]users where user_id=$user_id"));
				if ($tokens_available<$tokens)
				{
					async_return_request_status(array(array('error_code'=>'not_enough_tokens','error_field_code'=>'error_2','block'=>'album_view')));
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
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_ALBUMS'])==1)
				{
					$assign_tokens=$tokens-ceil($tokens*min(100,intval($memberzone_data['TOKENS_SALE_INTEREST']))/100);

					$exclude_users=array_map('trim',explode(",",$memberzone_data['TOKENS_SALE_EXCLUDES']));
					$username=mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?",$album['user_id']));
					if ($username && in_array($username,$exclude_users))
					{
						$assign_tokens=0;
					}

					if ($assign_tokens>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$assign_tokens,$album['user_id']);
						sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=7, user_id=?, album_id=?, tokens_granted=?, added_date=?",$album['user_id'],$album['album_id'],$assign_tokens,date("Y-m-d H:i:s"));
					} else {
						$assign_tokens=0;
					}
				}
				$tokens_revenue=$tokens-$assign_tokens;

				sql_pr("insert into $config[tables_prefix]users_purchases set album_id=$album_id, user_id=$user_id, owner_user_id=$album[user_id], tokens=?, tokens_revenue=?, added_date=?, expiry_date=?",$tokens,$tokens_revenue,$added_date,$expiry_date);
				sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens, 0) where user_id=$user_id");
				sql_pr("update $config[tables_prefix]albums set purchases_count=(select count(*) from $config[tables_prefix]users_purchases where $config[tables_prefix]users_purchases.album_id=$config[tables_prefix]albums.album_id) where album_id=$album_id");

				$_SESSION['tokens_available']=mr2number(sql("select tokens_available from $config[tables_prefix]users where user_id=$user_id"));
				$_SESSION['content_purchased'][]=array('album_id'=>$album_id);
				$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);
			}

			$result_data=array();
			$result_data['tokens_spend']=intval($tokens);
			$result_data['tokens_available']=intval($_SESSION['tokens_available']);
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'album_view')));
		}
	} elseif ($_REQUEST['action']=='js_stats_view_album')
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

			if (intval($_REQUEST[$block_config['var_album_id']])>0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/albums_id.dat", intval($_REQUEST[$block_config['var_album_id']])."||".intval($_SESSION['user_id'])."||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			} elseif (trim($_REQUEST[$block_config['var_album_dir']])<>'')
			{
				file_put_contents("$config[project_path]/admin/data/stats/albums_dir.dat", trim($_REQUEST[$block_config['var_album_dir']])."||".intval($_SESSION['user_id'])."||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||".date("Y-m-d H:i:s")."||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}
		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	}
}

function view_albumGetAlbumLog($block_config)
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

		$albums_amount = intval($temp[0]);
		$time = intval($temp[1]);

		if ($albums_amount > 0 && $time > 0)
		{
			if (isset($config['context']['album_view_log']) && is_array($config['context']['album_view_log']))
			{
				$album_log = $config['context']['album_view_log'];
			} else
			{
				require_once("$config[project_path]/admin/include/functions_base.php");
				require_once("$config[project_path]/admin/include/functions.php");

				$album_log = mr2array_list(sql_pr("select album_id from $config[tables_prefix]albums_visits where ip=? and flag=1 and added_date>?", ip2int($_SERVER['REMOTE_ADDR']), date("Y-m-d H:i:s", time() - $time)));
				$config['context']['album_view_log'] = $album_log;
			}
			return array("album_log" => $album_log, "albums_amount" => $albums_amount);
		}
	}

	return false;
}

function album_viewHasPremiumAccessByTokens($album_id,$owner_id)
{
	if ($_SESSION['status_id']==2)
	{
		foreach ($_SESSION['content_purchased'] as $purchase)
		{
			if ($album_id>0 && $purchase['album_id']==$album_id)
			{
				return true;
			}
			if ($owner_id>0 && $purchase['profile_id']==$owner_id)
			{
				return true;
			}
		}
	}
	return false;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
