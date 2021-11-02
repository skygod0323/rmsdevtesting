<?php
function list_categories_groupsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (isset($block_config['show_only_with_avatar']))
	{
		$where.=" and screenshot1!='' ";
	} elseif (isset($block_config['show_only_without_avatar'])) {
		$where.=" and screenshot1='' ";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}

	$where2='';
	$having='';
	if (isset($block_config['show_only_with_categories']))
	{
		$having.=" having total_categories>0";
		$where2.=" and (select count(*) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id)>0";
	}

	$where2="$where $where2";

	$metadata=list_categories_groupsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="category_group_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$total_videos_info_str=", (select coalesce(sum(total_videos), 0) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id) as total_videos";
	$total_albums_info_str=", (select coalesce(sum(total_albums), 0) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id) as total_albums";
	$total_posts_info_str=", (select coalesce(sum(total_posts), 0) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id) as total_posts";

	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower($database_selectors[generic_selector_title])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] $where2"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[categories_groups], (select count(*) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id) as total_categories $total_videos_info_str $total_albums_info_str $total_posts_info_str from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] $where $having order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[categories_groups], (select count(*) from $config[tables_prefix]categories where category_group_id=$config[tables_prefix]categories_groups.category_group_id) as total_categories $total_videos_info_str $total_albums_info_str $total_posts_info_str from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] $where $having order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=load_balance_categories_url().'/groups/'.$data[$k]['category_group_id'];
	}

	if (isset($block_config['pull_categories']))
	{
		$categories_sort_by=trim(strtolower($block_config['pull_categories_sort_by']));
		if (strpos($categories_sort_by," asc")!==false) {$categories_direction="asc";} else {$categories_direction="desc";}
		$categories_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$categories_sort_by));
		if ($categories_sort_by_clear=='') {$categories_sort_by_clear="sort_id";}
		if ($categories_sort_by_clear=='title') {$categories_sort_by_clear="lower($database_selectors[generic_selector_title])";}

		$categories_sort_by="$categories_sort_by_clear $categories_direction";
		$categories_limit=intval($block_config['pull_categories_count']);
		if ($categories_limit==0)
		{
			$categories_limit='';
		} else {
			$categories_limit="limit $categories_limit";
		}
		foreach ($data as $ke=>$ve)
		{
			$categories=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories] and $config[tables_prefix]categories.category_group_id=? order by $categories_sort_by $categories_limit",$ve['category_group_id']));
			foreach ($categories as $k=>$v)
			{
				$categories[$k]['base_files_url']=load_balance_categories_url().'/'.$categories[$k]['category_id'];
			}
			$data[$ke]['categories']=$categories;
		}
	}

	$smarty->assign("data",$data);

	if (isset($block_config['var_from']))
	{
		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}
	return '';
}

function list_categories_groupsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_sort_by";
}

function list_categories_groupsCacheControl($block_config)
{
	return "default";
}

function list_categories_groupsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[category_group_id,sort_id,is_avatar_available,title,dir,total_categories,total_videos,total_albums]", "is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_avatar",      "group"=>"static_filters", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_without_avatar",   "group"=>"static_filters", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_with_categories",  "group"=>"static_filters", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"", "is_required"=>0),

		// pull categories
		array("name"=>"pull_categories",         "group"=>"pull_categories", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_categories_count",   "group"=>"pull_categories", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_categories_sort_by", "group"=>"pull_categories", "type"=>"SORTING[sort_id,title,today_videos,total_videos,today_albums,total_albums,avg_videos_rating,avg_videos_popularity,max_videos_ctr,avg_albums_rating,avg_albums_popularity]", "is_required"=>0, "default_value"=>"title asc"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
