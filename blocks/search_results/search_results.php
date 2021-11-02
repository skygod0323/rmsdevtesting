<?php
function search_resultsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (intval($block_config['days'])>0)
	{
		$date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days'])+1,date("Y")));
		$where.=" and added_date>='$date'";
	}
	if (intval($block_config['query_length_min'])>0)
	{
		$where.=" and query_length>=".intval($block_config['query_length_min']);
	}
	if (intval($block_config['query_length_max'])>0)
	{
		$where.=" and query_length<=".intval($block_config['query_length_max']);
	}
	if (intval($block_config['query_amount_min'])>0)
	{
		$where.=" and amount>=".intval($block_config['query_amount_min']);
	}

	$query='';
	if (isset($block_config['var_query']) && $_REQUEST[$block_config['var_query']]<>'')
	{
		$query=str_replace('-',' ',str_replace('?','',$_REQUEST[$block_config['var_query']]));
		$query=trim(process_blocked_words($query,false));
		if ($query=='')
		{
			$where.=" and 1=0";
		}

		$storage[$object_id]['search_keyword']=$query;
		$smarty->assign('search_keyword',$query);
	} else
	{
		$category_title='';
		$tag_title='';
		if (isset($block_config['var_category_dir']) && $_REQUEST[$block_config['var_category_dir']]<>'')
		{
			$category_title=mr2string(sql_pr("select $database_selectors[generic_selector_title] as title from $config[tables_prefix]categories where dir=?",trim($_REQUEST[$block_config['var_category_dir']])));
		} elseif (isset($block_config['var_category_id']) && intval($_REQUEST[$block_config['var_category_id']])>0)
		{
			$category_title=mr2string(sql_pr("select $database_selectors[generic_selector_title] as title from $config[tables_prefix]categories where category_id=?",intval($_REQUEST[$block_config['var_category_id']])));
		} elseif (isset($block_config['var_tag_dir']) && $_REQUEST[$block_config['var_tag_dir']]<>'')
		{
			$tag_title=mr2string(sql_pr("select $database_selectors[generic_selector_tag] as tag from $config[tables_prefix]tags where tag_dir=?",trim($_REQUEST[$block_config['var_tag_dir']])));
		} elseif (isset($block_config['var_tag_id']) && intval($_REQUEST[$block_config['var_tag_id']])>0)
		{
			$tag_title=mr2string(sql_pr("select $database_selectors[generic_selector_tag] as tag from $config[tables_prefix]tags where tag_id=?",intval($_REQUEST[$block_config['var_tag_id']])));
		}
		if ($category_title!='')
		{
			$query=$category_title;
			$storage[$object_id]['search_category']=$category_title;
			$smarty->assign('search_category',$category_title);
		} elseif ($tag_title!='')
		{
			$query=$tag_title;
			$storage[$object_id]['search_tag']=$tag_title;
			$smarty->assign('search_tag',$tag_title);
		}
	}

	if (!isset($block_config['search_method']))
	{
		$block_config['search_method']=3;
	}

	$sort_by_relevance='';
	if ($query)
	{
		$query=sql_escape($query);
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
			$where.=" and MATCH(query) AGAINST ('$query' $search_modifier)";
			$sort_by_relevance="MATCH (query) AGAINST ('$query' $search_modifier) desc";
		} else if ($block_config['search_method']==2)
		{
			$where2='';
			$temp=explode(" ",$query);
			foreach ($temp as $temp_value)
			{
				$length=strlen($temp_value);
				if (function_exists('mb_detect_encoding'))
				{
					$length=mb_strlen($temp_value,mb_detect_encoding($temp_value));
				}
				if ($length>2)
				{
					$where2.=" or query like '%$temp_value%'";
				}
			}
			if ($where2)
			{
				$where2=substr($where2,4);
			} else {
				$where2.=" or query like '%$query%'";
			}
			$where.=" and ($where2)";
		} else if ($block_config['search_method']==1)
		{
			$where.=" and query like '%$query%'";
		}
		$where.=" and query!='$query'";
	}

	$query_results_limit=intval($block_config['query_results_min']);
	if ($query_results_limit>0)
	{
		$query_results_limit_column='query_results_total';
		if (intval($block_config['query_results_min_type'])==1)
		{
			$query_results_limit_column='query_results_videos';
		} elseif (intval($block_config['query_results_min_type'])==2)
		{
			$query_results_limit_column='query_results_albums';
		}
		$where.=" and $query_results_limit_column>=$query_results_limit";
	}

	$metadata=search_resultsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="amount";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$sort_by="$sort_by_clear $direction";
	if ($sort_by_relevance && isset($block_config['sort_by_relevance']))
	{
		$sort_by_clear='relevance';
		$sort_by=$sort_by_relevance;
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix_multi]stats_search where 1=1 $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		if ($sort_by_clear=='pseudo_rand')
		{
			$rand=intval(mt_rand(0,$total_count-$block_config['items_per_page']));
			$data=mr2array(sql("select * from $config[tables_prefix_multi]stats_search where 1=1 $where LIMIT $rand, $block_config[items_per_page]"));
			shuffle($data);
		} else
		{
			$data=mr2array(sql("select * from $config[tables_prefix_multi]stats_search where 1=1 $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));
		}

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

		if ($sort_by_clear=='pseudo_rand')
		{
			$total_count=mr2number(sql("select count(*) from $config[tables_prefix_multi]stats_search where 1=1 $where"));
			$rand=intval(mt_rand(0,$total_count-$block_config['items_per_page']));
			$data=mr2array(sql("select * from $config[tables_prefix_multi]stats_search where 1=1 $where LIMIT $rand, $block_config[items_per_page]"));
			shuffle($data);
		} else
		{
			$data=mr2array(sql("select * from $config[tables_prefix_multi]stats_search where 1=1 $where order by $sort_by $limit"));
		}

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	if ($website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']<>'' || (intval($block_config['size_to'])>0 && intval($block_config['size_from'])>0))
	{
		if (count($data)>0 && intval($block_config['size_to'])>0 && intval($block_config['size_from'])>0)
		{
			$groups_amount=intval($block_config['size_to'])-intval($block_config['size_from'])+1;
			if ($groups_amount<1) {$groups_amount=1;}

			$step=count($data)/$groups_amount;
			$size=intval($block_config['size_to']);
			$query_in_group_amount=$step;

			$sorted_data=kt_array_multisort($data,array(array('key'=>'query_results_total','sort'=>'desc')));
			$ranged_data=array();
			foreach ($sorted_data as $query_item)
			{
				$ranged_data[$query_item['query']]=$size;

				if (count($ranged_data)>=$query_in_group_amount)
				{
					$size--;
					$query_in_group_amount+=$step;
				}
			}
		}

		foreach ($data as $k=>$v)
		{
			if ($website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']<>'')
			{
				$query=$data[$k]['query'];
				$query=str_replace("&","%26",$query);
				$query=str_replace("?","%3F",$query);
				$query=str_replace("/","%2F",$query);
				$pattern=str_replace("%QUERY%",rawurlencode($query),$website_ui_data['WEBSITE_LINK_PATTERN_SEARCH']);
				$data[$k]['view_page_url']="$config[project_url]/$pattern";
			}
			if (isset($ranged_data))
			{
				$size=intval($ranged_data[$v['query']]);
				if ($size>0)
				{
					$data[$k]['size']=$size;
					$data[$k]['size_percent']=round(100*(1+($size-$block_config['size_from'])/$block_config['size_from']));
					if (intval($block_config['bold_from'])>0 && $size>=intval($block_config['bold_from']))
					{
						$data[$k]['is_bold']=1;
					} else {
						$data[$k]['is_bold']=0;
					}
				}
			}
		}
	}

	$smarty->assign("data",$data);

	if (isset($total_count) && isset($from))
	{
		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}
	return '';
}

function search_resultsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_query=trim($_REQUEST[$block_config['var_query']]);
	$var_category_id=intval($_REQUEST[$block_config['var_category_id']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_tag_id=intval($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_query|$var_category_id|$var_category_dir|$var_tag_id|$var_tag_dir|$var_sort_by";
}

function search_resultsCacheControl($block_config)
{
	return "default";
}

function search_resultsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[query,amount,query_results_total,query_results_videos,query_results_albums,pseudo_rand]", "is_required"=>1, "default_value"=>"amount"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"days",                   "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"query_length_min",       "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>""),
		array("name"=>"query_length_max",       "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>""),
		array("name"=>"query_results_min",      "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"0"),
		array("name"=>"query_results_min_type", "group"=>"static_filters", "type"=>"CHOICE[0,1,2]", "is_required"=>0),
		array("name"=>"query_amount_min",       "group"=>"static_filters", "type"=>"INT",           "is_required"=>0),

		// search
		array("name"=>"var_query",         "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"q"),
		array("name"=>"var_category_id",   "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_dir",  "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_tag_id",        "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_tag_dir",       "group"=>"search", "type"=>"STRING",            "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"search_method",     "group"=>"search", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"sort_by_relevance", "group"=>"search", "type"=>"",                  "is_required"=>0),

		// sizes
		array("name"=>"size_from", "group"=>"sizes", "type"=>"INT", "is_required"=>0, "default_value"=>"12"),
		array("name"=>"size_to",   "group"=>"sizes", "type"=>"INT", "is_required"=>0, "default_value"=>"19"),
		array("name"=>"bold_from", "group"=>"sizes", "type"=>"INT", "is_required"=>0, "default_value"=>"16"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
