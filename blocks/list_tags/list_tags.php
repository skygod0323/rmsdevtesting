<?php
function list_tagsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (isset($block_config['show_only_with_videos']))
	{
		$amount_limit=intval($block_config['show_only_with_videos']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_videos>=$amount_limit";
	}

	if (isset($block_config['show_only_with_albums']))
	{
		$amount_limit=intval($block_config['show_only_with_albums']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_albums>=$amount_limit";
	}

	if (isset($block_config['show_only_with_posts']))
	{
		$amount_limit=intval($block_config['show_only_with_posts']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_posts>=$amount_limit";
	}

	if (isset($block_config['show_only_with_albums_or_videos']))
	{
		$amount_limit=intval($block_config['show_only_with_albums_or_videos']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and (total_albums>=$amount_limit or total_videos>=$amount_limit)";
	}

	if (isset($block_config['show_only_with_playlists']))
	{
		$amount_limit=intval($block_config['show_only_with_playlists']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_playlists>=$amount_limit";
	}

	if (isset($block_config['show_only_with_dvds']))
	{
		$amount_limit=intval($block_config['show_only_with_dvds']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_dvds>=$amount_limit";
	}

	if (isset($block_config['show_only_with_cs']))
	{
		$amount_limit=intval($block_config['show_only_with_cs']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_cs>=$amount_limit";
	}

	if (isset($block_config['show_only_with_models']))
	{
		$amount_limit=intval($block_config['show_only_with_models']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_models>=$amount_limit";
	}

	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$unescaped_q=trim($_REQUEST[$block_config['var_title_section']]);
		$q=sql_escape($unescaped_q);
		$where.=" and $database_selectors[locale_field_tag] like '$q%'";
		$smarty->assign('list_type',"section");
		$smarty->assign('section',$unescaped_q);
		$storage[$object_id]['list_type']="section";
		$storage[$object_id]['section']=$unescaped_q;
	}

	$data=list_tagsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="tag";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='tag')
	{
		$sort_by_clear="lower($database_selectors[generic_selector_tag])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]tags where $database_selectors[where_tags] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[tags] from $config[tables_prefix]tags where $database_selectors[where_tags] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[tags] from $config[tables_prefix]tags where $database_selectors[where_tags] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$smarty->assign("data",$data);

	if (isset($block_config['var_from']))
	{
		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}
	return '';
}

function list_tagsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_title_section|$var_sort_by";
}

function list_tagsCacheControl($block_config)
{
	return "default";
}

function list_tagsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[tag_id,tag,tag_dir,today_videos,total_videos,today_albums,total_albums,today_posts,total_posts,total_playlists,total_dvds,total_cs,total_models,avg_videos_rating,avg_videos_popularity,avg_albums_rating,avg_albums_popularity,avg_posts_rating,avg_posts_popularity]", "is_required"=>1, "default_value"=>"tag asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_videos",           "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_albums",           "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_albums_or_videos", "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_posts",            "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_playlists",        "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_dvds",             "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_cs",               "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_models",           "group"=>"static_filters", "type"=>"INT",    "is_required"=>0, "default_value"=>"1"),

		// dynamic filters
		array("name"=>"var_title_section", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
