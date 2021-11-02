<?php
function content_source_group_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	$total_cs_info_str="(select count(*) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_content_sources";
	$total_videos_info_str="(select coalesce(sum(total_videos), 0) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_videos";
	$total_albums_info_str="(select coalesce(sum(total_albums), 0) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_albums";

	if (isset($block_config['var_content_source_group_id']) && intval($_REQUEST[$block_config['var_content_source_group_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[content_sources_groups], $total_cs_info_str, $total_videos_info_str, $total_albums_info_str from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups_active_disabled] and content_source_group_id=?",intval($_REQUEST[$block_config['var_content_source_group_id']]));
		if (mr2rows($result)==0) {return 'status_404';}
	} elseif (trim($_REQUEST[$block_config['var_content_source_group_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[content_sources_groups], $total_cs_info_str, $total_videos_info_str, $total_albums_info_str from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_content_source_group_dir']]),trim($_REQUEST[$block_config['var_content_source_group_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}
	} else {
		return '';
	}

	$data=mr2array_single($result);
	$data['content_sources_count']=$data['total_content_sources'];

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	$smarty->assign("data",$data);
	return '';
}

function content_source_group_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_content_source_group_dir']]);
	$id=intval($_REQUEST[$block_config['var_content_source_group_id']]);

	return "$dir|$id";
}

function content_source_group_viewCacheControl($block_config)
{
	return "default";
}

function content_source_group_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_content_source_group_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_content_source_group_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id")
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
