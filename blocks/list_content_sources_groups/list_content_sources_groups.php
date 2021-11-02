<?php
function list_content_sources_groupsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	$where2='';
	$having='';
	if (isset($block_config['show_only_with_cs']))
	{
		$having.=" having total_content_sources>0";
		$where2.=" and (select count(*) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id)>0";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}

	$where2="$where $where2";

	$metadata=list_content_sources_groupsMetaData();
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

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="content_source_group_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$total_videos_info_str=", (select coalesce(sum(total_videos), 0) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_videos";
	$total_albums_info_str=", (select coalesce(sum(total_albums), 0) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_albums";

	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower($database_selectors[generic_selector_title])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] $where2"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[content_sources_groups], (select count(*) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_content_sources $total_videos_info_str $total_albums_info_str from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] $where $having order by $sort_by LIMIT $from, $block_config[items_per_page]"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);
	} else {
		$limit='';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("select $database_selectors[content_sources_groups], (select count(*) from $config[tables_prefix]content_sources where content_source_group_id=$config[tables_prefix]content_sources_groups.content_source_group_id) as total_content_sources $total_videos_info_str $total_albums_info_str from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] $where $having order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['content_sources']=$v['total_content_sources'];
	}

	if (isset($block_config['pull_content_sources']))
	{
		$content_sources_sort_by=trim(strtolower($block_config['pull_content_sources_sort_by']));
		if (strpos($content_sources_sort_by," asc")!==false) {$content_sources_direction="asc";} else {$content_sources_direction="desc";}
		$content_sources_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$content_sources_sort_by));
		if ($content_sources_sort_by_clear=='') {$content_sources_sort_by_clear="sort_id";}
		if ($content_sources_sort_by_clear=='title') {$content_sources_sort_by_clear="lower($database_selectors[generic_selector_title])";}

		$content_sources_sort_by="$content_sources_sort_by_clear $content_sources_direction";
		$content_sources_limit=intval($block_config['pull_content_sources_count']);
		if ($content_sources_limit==0)
		{
			$content_sources_limit='';
		} else {
			$content_sources_limit="limit $content_sources_limit";
		}
		foreach ($data as $ke=>$ve)
		{
			$content_sources=mr2array(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and $config[tables_prefix]content_sources.content_source_group_id=? order by $content_sources_sort_by $content_sources_limit",$ve['content_source_group_id']));
			foreach ($content_sources as $k=>$v)
			{
				$content_sources[$k]['base_files_url']=$config['content_url_content_sources'].'/'.$content_sources[$k]['content_source_id'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
				{
					$pattern=str_replace("%ID%",$content_sources[$k]['content_source_id'],str_replace("%DIR%",$content_sources[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
					$content_sources[$k]['view_page_url']="$config[project_url]/$pattern";
				}
			}
			$data[$ke]['content_sources']=$content_sources;
		}
	}

	$smarty->assign("data",$data);

	if (isset($block_config['var_from']))
	{
		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}
	return '';
}

function list_content_sources_groupsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_sort_by";
}

function list_content_sources_groupsCacheControl($block_config)
{
	return "default";
}

function list_content_sources_groupsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[content_source_group_id,sort_id,title,dir,total_content_sources,total_videos,total_albums]", "is_required"=>1, "default_value"=>"title asc"),

		// static filters
		array("name"=>"show_only_with_cs",          "group"=>"static_filters", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"", "is_required"=>0),

		// pull content sources
		array("name"=>"pull_content_sources",         "group"=>"pull_content_sources", "type"=>"",         "is_required"=>0),
		array("name"=>"pull_content_sources_count",   "group"=>"pull_content_sources", "type"=>"INT",      "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_content_sources_sort_by", "group"=>"pull_content_sources", "type"=>"SORTING[sort_id,title,today_videos,total_videos,today_albums,total_albums,avg_videos_rating,avg_videos_popularity,avg_albums_rating,avg_albums_popularity]", "is_required"=>0, "default_value"=>"title asc"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
