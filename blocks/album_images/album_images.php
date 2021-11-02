<?php
function album_imagesShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	if (isset($block_config['var_album_id']) && intval($_REQUEST[$block_config['var_album_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_disabled_deleted] and album_id=?",intval($_REQUEST[$block_config['var_album_id']]));
		if (mr2rows($result)==0)
		{
			$smarty->caching=0;
			return 'nocache';
		}
	} elseif (trim($_REQUEST[$block_config['var_album_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums_active_disabled_deleted] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_album_dir']]),trim($_REQUEST[$block_config['var_album_dir']]));
		if (mr2rows($result)==0)
		{
			$smarty->caching=0;
			return 'nocache';
		}
	} else {
		return '';
	}

	$album_info=mr2array_single($result);
	$album_id=$album_info['album_id'];

	$album_info['zip_files']=get_album_zip_files($album_id,$album_info['zip_files'],$album_info['server_group_id']);

	if (isset($block_config['var_from']))
	{
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums_images where album_id=$album_id"));
		$from=intval($_REQUEST[$block_config['var_from']]);
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("SELECT $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("SELECT $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=$album_id order by image_id $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
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

	$dir_path=get_dir_by_id($album_id);
	$formats_albums=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat"));
	foreach ($data as $k=>$v)
	{
		$lb_server=load_balance_server($album_info['server_group_id'],$cluster_servers,$cluster_servers_weights);

		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);

		$pattern=str_replace("%IMG%",$v['image_id'],str_replace("%ID%",$album_id,str_replace("%DIR%",$album_info['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_IMAGE'])));
		$data[$k]['view_page_url']="$config[project_url]/$pattern";

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
				$format_item['protected_url']="$config[project_url]/get_image/$album_info[server_group_id]/$hash/$file_path/";
				if ($album_info['user_id']==$_SESSION['user_id'] || album_imagesHasPremiumAccessByTokens($album_info['album_id'],$album_info['user_id']))
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
		$format_item['protected_url']="$config[project_url]/get_image/$album_info[server_group_id]/$hash/$file_path/";
		if ($album_info['user_id']==$_SESSION['user_id'] || album_imagesHasPremiumAccessByTokens($album_info['album_id'],$album_info['user_id']))
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

		$data[$k]['formats']=$formats;
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if ($album_info['is_private']==2)
	{
		if (intval($memberzone_data['ENABLE_TOKENS_PREMIUM_ALBUM'])==1)
		{
			if (intval($album_info['tokens_required'])==0)
			{
				$album_info['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_PREMIUM_ALBUM']);
			}
		} else {
			$album_info['tokens_required']=0;
		}
	} else {
		if (intval($memberzone_data['ENABLE_TOKENS_STANDARD_ALBUM'])==1)
		{
			if (intval($album_info['tokens_required'])==0)
			{
				$album_info['tokens_required']=intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']);
			}
		} else {
			$album_info['tokens_required']=0;
		}
	}

	$album_info['is_purchased_album']=0;
	if ($_SESSION['user_id']==$album_info['user_id'] || album_imagesHasPremiumAccessByTokens($album_info['album_id'],$album_info['user_id']))
	{
		$album_info['is_purchased_album']=1;
	}

	$can_watch_album=1;
	if ($album_info['is_private']==0 && $_SESSION['user_id']<>$album_info['user_id'])
	{
		$can_watch_album=0;
		$access_option=intval($memberzone_data['PUBLIC_ALBUMS_ACCESS']);
		if (intval($album_info['access_level_id'])==1)
		{
			$access_option=0;
		} elseif (intval($album_info['access_level_id'])==2)
		{
			$access_option=1;
		} elseif (intval($album_info['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				$can_watch_album=1;
				break;
			case 1:
				if ($_SESSION['user_id']>0)
				{
					$can_watch_album=1;
				}
				break;
			case 2:
				if ($_SESSION['status_id']==3 || $album_info['is_purchased_album']==1)
				{
					$can_watch_album=1;
				}
				break;
		}
	}
	if ($album_info['is_private']==1 && $_SESSION['user_id']<>$album_info['user_id'])
	{
		$can_watch_album=0;
		$access_option=intval($memberzone_data['PRIVATE_ALBUMS_ACCESS']);
		if (intval($album_info['access_level_id'])==1)
		{
			$access_option=3;
		} elseif (intval($album_info['access_level_id'])==2)
		{
			$access_option=0;
		} elseif (intval($album_info['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				if ($_SESSION['user_id']>0)
				{
					$can_watch_album=1;
				}
				break;
			case 1:
				if ($_SESSION['user_id']>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",$_SESSION['user_id'],$album_info['user_id'],$_SESSION['user_id'],$album_info['user_id']))>0)
				{
					$can_watch_album=1;
				}
				break;
			case 2:
				if ($_SESSION['status_id']==3 || $album_info['is_purchased_album']==1)
				{
					$can_watch_album=1;
				}
				break;
			case 3:
				$can_watch_album=1;
				break;
		}
	}
	if ($album_info['is_private']==2 && $_SESSION['user_id']<>$album_info['user_id'])
	{
		$can_watch_album=0;
		$access_option=intval($memberzone_data['PREMIUM_ALBUMS_ACCESS']);
		if (intval($album_info['access_level_id'])==1)
		{
			$access_option=0;
		} elseif (intval($album_info['access_level_id'])==2)
		{
			$access_option=1;
		} elseif (intval($album_info['access_level_id'])==3)
		{
			$access_option=2;
		}
		switch ($access_option)
		{
			case 0:
				$can_watch_album=1;
				break;
			case 1:
				if ($_SESSION['user_id']>0)
				{
					$can_watch_album=1;
				}
				break;
			case 2:
				if ($_SESSION['status_id']==3 || $album_info['is_purchased_album']==1)
				{
					$can_watch_album=1;
				}
				break;
		}
	}
	$album_info['can_watch']=$can_watch_album;

	$smarty->assign("session_name",session_name());
	$smarty->assign("data",$data);
	$smarty->assign("album_info",$album_info);
	$smarty->assign("can_watch_album",$can_watch_album);

	return '';
}

function album_imagesGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_album_dir']]);
	$id=intval($_REQUEST[$block_config['var_album_id']]);
	$from=intval($_REQUEST[$block_config['var_from']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	return "$dir|$id|$from";
}

function album_imagesCacheControl($block_config)
{
	return "user_nocache";
}

function album_imagesMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",       "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),

		// object context
		array("name"=>"var_album_dir",  "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_album_id",   "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),
	);
}

function album_imagesHasPremiumAccessByTokens($album_id,$owner_id)
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
