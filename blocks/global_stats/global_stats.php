<?php
function global_statsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	$now_date=date("Y-m-d H:i:s");
	$now_date_short=date("Y-m-d");

	$where_videos='';
	$where_albums='';
	$where_content_sources='';
	$where_models='';
	$where_dvds='';
	$where_dvds_groups='';
	if ((isset($block_config['var_category_dir']) || isset($block_config['var_category_id'])) && ($_REQUEST[$block_config['var_category_dir']]<>'' || $_REQUEST[$block_config['var_category_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_category_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_category_dir']]),trim($_REQUEST[$block_config['var_category_dir']]));
		} else {
			$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories_active_disabled] and category_id=?",intval($_REQUEST[$block_config['var_category_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$category_id=intval($data_temp["category_id"]);

			$where_videos.=" and $config[tables_prefix]videos.video_id in (select video_id from $config[tables_prefix]categories_videos where category_id=$category_id)";
			$where_albums.=" and $config[tables_prefix]albums.album_id in (select album_id from $config[tables_prefix]categories_albums where category_id=$category_id)";
			$where_content_sources.=" and $config[tables_prefix]content_sources.content_source_id in (select content_source_id from $config[tables_prefix]categories_content_sources where category_id=$category_id)";
			$where_models.=" and $config[tables_prefix]models.model_id in (select model_id from $config[tables_prefix]categories_models where category_id=$category_id)";
			$where_dvds.=" and $config[tables_prefix]dvds.dvd_id in (select dvd_id from $config[tables_prefix]categories_dvds where category_id=$category_id)";
			$where_dvds_groups.=" and $config[tables_prefix]dvds_groups.dvd_group_id in (select dvd_group_id from $config[tables_prefix]categories_dvds_groups where category_id=$category_id)";
			$storage[$object_id]['category']=$data_temp['title'];
			$storage[$object_id]['category_info']=$data_temp;
			$smarty->assign('category',$data_temp['title']);
			$smarty->assign('category_info',$data_temp);
		}
	}

	$stats=array();
	$stats['comments_total']=mr2number(sql("select count(*) from $config[tables_prefix]comments where is_approved=1"));

	if (!isset($block_config['skip_videos']))
	{
		$stats['videos_total']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos"));
		$stats['videos_today']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and STR_TO_DATE($database_selectors[generic_post_date_selector], '%Y-%m-%d')='$now_date_short'"));
		$stats['private_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=1"));
		$stats['premium_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=2"));
		$stats['comments_videos']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=1 and is_approved=1"));
		$stats['videos_total_uploaded_size']=sizeToHumanString(mr2number(sql("select sum(file_size) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos")),2);
		$stats['private_videos_uploaded_size']=sizeToHumanString(mr2number(sql("select sum(file_size) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=1")),2);
		$stats['premium_videos_uploaded_size']=sizeToHumanString(mr2number(sql("select sum(file_size) from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=2")),2);
		$stats['videos_total_duration']=mr2float(sql("select sum(duration) / 3600 from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos"));
		$stats['private_videos_duration']=mr2float(sql("select sum(duration) / 3600 from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=1"));
		$stats['premium_videos_duration']=mr2float(sql("select sum(duration) / 3600 from $config[tables_prefix]videos where $database_selectors[where_videos] $where_videos and is_private=2"));
		if (!isset($block_config['skip_members']))
		{
			$stats['videos_bookmarks']=mr2number(sql("select count(*) from $config[tables_prefix]fav_videos"));
			$stats['playlists']=mr2number(sql("select count(*) from $config[tables_prefix]playlists where $database_selectors[where_playlists]"));
			$stats['comments_playlists']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=13 and is_approved=1"));
		}
	}
	if (!isset($block_config['skip_members']))
	{
		$online_interval=intval($website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL'])*60+30;

		$stats['members_total']=mr2number(sql("select count(*) from $config[tables_prefix]users where status_id not in (1,4)"));
		$stats['members_today']=mr2number(sql("select count(*) from $config[tables_prefix]users where status_id not in (1,4) and STR_TO_DATE(added_date, '%Y-%m-%d')='$now_date_short'"));
		$stats['members_online']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_online_date>date_sub('$now_date', interval $online_interval second)"));
		$stats['friends_total']=mr2number(sql("select count(*) from $config[tables_prefix]friends"));
	}
	if (!isset($block_config['skip_albums']))
	{
		$stats['albums_total']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] $where_albums"));
		$stats['albums_today']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] $where_albums and STR_TO_DATE($database_selectors[generic_post_date_selector], '%Y-%m-%d')='$now_date_short'"));
		$stats['private_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] $where_albums and is_private=1"));
		$stats['premium_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $database_selectors[where_albums] $where_albums and is_private=2"));
		$stats['albums_images_total']=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]albums_images on $config[tables_prefix]albums.album_id=$config[tables_prefix]albums_images.album_id where $database_selectors[where_albums] $where_albums"));
		$stats['albums_images_private']=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]albums_images on $config[tables_prefix]albums.album_id=$config[tables_prefix]albums_images.album_id where $database_selectors[where_albums] $where_albums and is_private=1"));
		$stats['albums_images_premium']=mr2number(sql("select count(*) from $config[tables_prefix]albums inner join $config[tables_prefix]albums_images on $config[tables_prefix]albums.album_id=$config[tables_prefix]albums_images.album_id where $database_selectors[where_albums] $where_albums and is_private=2"));
		$stats['comments_albums']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=2 and is_approved=1"));
		if (!isset($block_config['skip_members']))
		{
			$stats['albums_bookmarks']=mr2number(sql("select count(*) from $config[tables_prefix]fav_albums"));
		}
	}
	if (!isset($block_config['skip_content_sources']))
	{
		$stats['content_sources']=mr2number(sql("select count(*) from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] $where_content_sources"));
		$stats['content_sources_groups']=mr2number(sql("select count(*) from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups]"));
		$stats['comments_cs']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=3 and is_approved=1"));
	}
	if (!isset($block_config['skip_models']))
	{
		$stats['models']=mr2number(sql("select count(*) from $config[tables_prefix]models where $database_selectors[where_models] $where_models"));
		$stats['models_groups']=mr2number(sql("select count(*) from $config[tables_prefix]models_groups where $database_selectors[where_models_groups]"));
		$stats['comments_models']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=4 and is_approved=1"));
	}
	if (!isset($block_config['skip_dvds']))
	{
		$stats['dvds']=mr2number(sql("select count(*) from $config[tables_prefix]dvds where $database_selectors[where_dvds] $where_dvds"));
		$stats['dvds_groups']=mr2number(sql("select count(*) from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups] $where_dvds_groups"));
		$stats['comments_dvds']=mr2number(sql("select count(*) from $config[tables_prefix]comments where object_type_id=5 and is_approved=1 "));
	}

	if (!isset($block_config['skip_traffic']))
	{
		$traffic_date_now=date("Y-m-d");
		$traffic_date_yesterday=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$traffic_date_week=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
		$traffic_date_month=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));

		$stats['traffic_yesterday']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where added_date='$traffic_date_yesterday'"));
		$stats['traffic_week']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where added_date<='$traffic_date_now' and added_date>='$traffic_date_week'"));
		$stats['traffic_month']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where added_date<='$traffic_date_now' and added_date>='$traffic_date_month'"));
	}

	$smarty->assign('stats',$stats);
	foreach ($stats as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}
	return '';
}

function global_statsGetHash($block_config)
{
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=intval($_REQUEST[$block_config['var_category_id']]);

	return "$var_category_dir|$var_category_id";
}

function global_statsCacheControl($block_config)
{
	return "default";
}

function global_statsMetaData()
{
	return array(
		// dynamic filters
		array("name"=>"var_category_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),

		// performance
		array("name"=>"skip_videos",          "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_albums",          "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_members",         "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_content_sources", "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_models",          "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_dvds",            "group"=>"performance", "type"=>"", "is_required"=>0),
		array("name"=>"skip_traffic",         "group"=>"performance", "type"=>"", "is_required"=>0)
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
