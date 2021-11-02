<?php
function list_commentsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where="";
	$join="";
	if (isset($block_config['mode_global']))
	{
		$where="";
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
		} else {
			return 'status_404';
		}
		if (isset($block_config['mode_content']))
		{
			$join="inner join (
				select video_id as object_id, 1 as object_type_id from $config[tables_prefix]videos where user_id=$user_id
				union all select album_id as object_id, 2 as object_type_id from $config[tables_prefix]albums where user_id=$user_id
				union all select dvd_id as object_id, 5 as object_type_id from $config[tables_prefix]dvds where user_id=$user_id
				union all select post_id as object_id, 12 as object_type_id from $config[tables_prefix]posts where user_id=$user_id
				union all select playlist_id as object_id, 13 as object_type_id from $config[tables_prefix]playlists where user_id=$user_id
			) c on $config[tables_prefix]comments.object_id=c.object_id and $config[tables_prefix]comments.object_type_id=c.object_type_id ";
		} else
		{
			$where=" and $config[tables_prefix]comments.user_id=$user_id ";
		}
	} elseif ($_SESSION['user_id']>0) {
		$user_id=intval($_SESSION['user_id']);
		$smarty->assign("display_name",$_SESSION['display_name']);
		$smarty->assign("avatar",$_SESSION['avatar']);
		$smarty->assign("user_id",$user_id);
		$storage[$object_id]['user_id']=$user_id;
		$storage[$object_id]['display_name']=$_SESSION['display_name'];
		$storage[$object_id]['avatar']=$_SESSION['avatar'];

		if (isset($block_config['mode_content']))
		{
			$join="inner join (
				select video_id as object_id, 1 as object_type_id from $config[tables_prefix]videos where user_id=$user_id
				union all select album_id as object_id, 2 as object_type_id from $config[tables_prefix]albums where user_id=$user_id
				union all select dvd_id as object_id, 5 as object_type_id from $config[tables_prefix]dvds where user_id=$user_id
				union all select post_id as object_id, 12 as object_type_id from $config[tables_prefix]posts where user_id=$user_id
				union all select playlist_id as object_id, 13 as object_type_id from $config[tables_prefix]playlists where user_id=$user_id
			) c on $config[tables_prefix]comments.object_id=c.object_id and $config[tables_prefix]comments.object_type_id=c.object_type_id ";
		} else
		{
			$where=" and $config[tables_prefix]comments.user_id=$_SESSION[user_id] ";
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

	if (intval($block_config['comments_on'])>0)
	{
		$object_type_id=intval($block_config['comments_on']);
		$where.=" and $config[tables_prefix]comments.object_type_id=$object_type_id ";
	}

	if (isset($block_config['match_locale']))
	{
		$language_code=sql_escape($config['locale']);
		$where.=" and $config[tables_prefix]comments.language_code='$language_code'";
	}

	$now_date=date("Y-m-d H:i:s");
	$sql_select="
		$config[tables_prefix]comments.object_type_id as content_type,
		$config[tables_prefix]comments.object_id as content_id,
		case $config[tables_prefix]comments.object_type_id when 1 then $database_selectors[videos_selector_dir] when 2 then $database_selectors[albums_selector_dir] when 3 then $database_selectors[content_sources_selector_dir] when 4 then $database_selectors[models_selector_dir] when 5 then $database_selectors[dvds_selector_dir] when 12 then $database_selectors[posts_selector_dir] when 13 then $database_selectors[playlists_selector_dir] end as content_dir,
		case $config[tables_prefix]comments.object_type_id when 1 then $database_selectors[videos_selector_title] when 2 then $database_selectors[albums_selector_title] when 3 then $database_selectors[content_sources_selector_title] when 4 then $database_selectors[models_selector_title] when 5 then $database_selectors[dvds_selector_title] when 12 then $database_selectors[posts_selector_title] when 13 then $database_selectors[playlists_selector_title] end as content_title,
		case $config[tables_prefix]comments.object_type_id when 1 then $database_selectors[videos_selector_description] when 2 then $database_selectors[albums_selector_description] when 3 then $database_selectors[content_sources_selector_description] when 4 then $database_selectors[models_selector_description] when 5 then $database_selectors[dvds_selector_description] when 12 then $database_selectors[posts_selector_description] when 13 then $database_selectors[playlists_selector_description] end as content_desc,
		case $config[tables_prefix]comments.object_type_id when 12 then $config[tables_prefix]posts.post_type_id else 0 end as post_type_id,
		case $config[tables_prefix]comments.object_type_id when 13 then $config[tables_prefix]playlists.user_id else 0 end as playlist_user_id,
		$config[tables_prefix]comments.user_id,
		$config[tables_prefix]comments.comment,
		$config[tables_prefix]comments.added_date,
		$config[tables_prefix]comments.anonymous_username,
		$config[tables_prefix]users.status_id,
		$config[tables_prefix]users.avatar,
		$config[tables_prefix]users.username,
		$config[tables_prefix]users.display_name,
		$config[tables_prefix]users.gender_id,
		$config[tables_prefix]users.country_id,
		$config[tables_prefix]users.city
	";
	$sql_from="
		$config[tables_prefix]comments inner join
		$config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id left join
		$config[tables_prefix]videos on $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.object_id=$config[tables_prefix]videos.video_id left join
		$config[tables_prefix]albums on $config[tables_prefix]comments.object_type_id=2 and $config[tables_prefix]comments.object_id=$config[tables_prefix]albums.album_id left join
		$config[tables_prefix]content_sources on $config[tables_prefix]comments.object_type_id=3 and $config[tables_prefix]comments.object_id=$config[tables_prefix]content_sources.content_source_id left join
		$config[tables_prefix]models on $config[tables_prefix]comments.object_type_id=4 and $config[tables_prefix]comments.object_id=$config[tables_prefix]models.model_id left join
		$config[tables_prefix]dvds on $config[tables_prefix]comments.object_type_id=5 and $config[tables_prefix]comments.object_id=$config[tables_prefix]dvds.dvd_id left join
		$config[tables_prefix]posts on $config[tables_prefix]comments.object_type_id=12 and $config[tables_prefix]comments.object_id=$config[tables_prefix]posts.post_id left join
		$config[tables_prefix]playlists on $config[tables_prefix]comments.object_type_id=13 and $config[tables_prefix]comments.object_id=$config[tables_prefix]playlists.playlist_id
		$join
	";
	$sql_where="
		$config[tables_prefix]comments.added_date<='$now_date' and
		($config[tables_prefix]comments.object_type_id<>1 or ($database_selectors[where_videos])) and
		($config[tables_prefix]comments.object_type_id<>2 or ($database_selectors[where_albums])) and
		($config[tables_prefix]comments.object_type_id<>3 or ($database_selectors[where_content_sources])) and
		($config[tables_prefix]comments.object_type_id<>4 or ($database_selectors[where_models])) and
		($config[tables_prefix]comments.object_type_id<>5 or ($database_selectors[where_dvds])) and
		($config[tables_prefix]comments.object_type_id<>12 or ($database_selectors[where_posts])) and
		($config[tables_prefix]comments.object_type_id<>13 or ($database_selectors[where_playlists])) and
		$config[tables_prefix]comments.is_approved=1 $where
	";

	$data=list_commentsMetaData();
	foreach ($data as $res)
	{
		if ($res['name']=='sort_by')
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
	if ($sort_by_clear=='') {$sort_by_clear="added_date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='rating')
	{
		$sort_by_clear="($config[tables_prefix]comments.likes - $config[tables_prefix]comments.dislikes)";
	} elseif ($sort_by_clear!='rand()')
	{
		$sort_by_clear="$config[tables_prefix]comments.$sort_by_clear";
	}
	$sort_by="$sort_by_clear $direction";

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $sql_from where $sql_where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$limit=intval($block_config['items_per_page']);
		if ($limit==0) {$limit=$total_count;}

		$data=mr2array(sql("select $sql_select from $sql_from where $sql_where order by $sort_by limit $from, $limit"));

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

		$data=mr2array(sql("select $sql_select from $sql_from where $sql_where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$post_types=mr2array(sql("select * from $config[tables_prefix]posts_types"));

	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);
		if ($data[$k]['avatar']<>'')
		{
			$data[$k]['avatar']=$config['content_url_avatars']."/".$data[$k]['avatar'];
			$data[$k]['avatar_url']=$data[$k]['avatar'];
		}
		if ($data[$k]['country_id']>0)
		{
			$data[$k]['country']=$list_countries['name'][$data[$k]['country_id']];
		}
		if ($data[$k]['status_id']==4)
		{
			$data[$k]['is_anonymous']=1;
			if ($data[$k]['anonymous_username']!='')
			{
				$data[$k]['display_name']=$data[$k]['anonymous_username'];
			}
		}

		$pattern='';
		if ($data[$k]['content_type']==1)
		{
			$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
		} elseif ($data[$k]['content_type']==2)
		{
			$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
		} elseif ($data[$k]['content_type']==3)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
			}
		} elseif ($data[$k]['content_type']==4)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
			}
		} elseif ($data[$k]['content_type']==5)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
			}
		} elseif ($data[$k]['content_type']==12)
		{
			foreach ($post_types as $post_type)
			{
				if ($post_type['post_type_id']==$data[$k]['post_type_id'])
				{
					$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$post_type['url_pattern']));
					break;
				}
			}
		} elseif ($data[$k]['content_type']==13)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_id'],str_replace("%DIR%",$data[$k]['content_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
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

function list_commentsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);

	if (isset($block_config['mode_global']))
	{
		return "$from|$items_per_page|$var_sort_by";
	} elseif (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "$var_user_id|$from|$items_per_page|$var_sort_by";
}

function list_commentsCacheControl($block_config)
{
	if (isset($block_config['mode_global']))
	{
		return "default";
	} elseif (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_commentsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[comment_id,added_date,rating]", "is_required"=>1, "default_value"=>"added_date desc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"comments_on",  "group"=>"static_filters", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"match_locale", "group"=>"static_filters", "type"=>"",                  "is_required"=>0),

		// display modes
		array("name"=>"mode_global",              "group"=>"display_modes", "type"=>"",       "is_required"=>0, "default_value"=>"1"),
		array("name"=>"mode_content",             "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>