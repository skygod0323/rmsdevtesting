<?php
function list_content_sourcesShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$unescaped_q=trim($_REQUEST[$block_config['var_title_section']]);
		$q=sql_escape($unescaped_q);
		$where.=" and $database_selectors[locale_field_title] like '$q%'";
		$smarty->assign('list_type',"section");
		$smarty->assign('section',$unescaped_q);
		$storage[$object_id]['list_type']="section";
		$storage[$object_id]['section']=$unescaped_q;
	}

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

	if (isset($block_config['show_only_with_albums_or_videos']))
	{
		$amount_limit=intval($block_config['show_only_with_albums_or_videos']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and (total_albums>=$amount_limit or total_videos>=$amount_limit)";
	}

	if (isset($block_config['show_only_with_screenshot1']))
	{
		$where.=" and screenshot1!=''";
	}

	if (isset($block_config['show_only_with_screenshot2']))
	{
		$where.=" and screenshot2!=''";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}

	$join_tables=array();

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'content_source_group', 'plural'=>'content_sources_groups', 'title'=>'title','dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_groups_active_disabled'], 'where_plural'=>$database_selectors['where_content_sources_groups']);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',             'plural'=>'categories',             'title'=>'title','dir'=>'dir',     'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],             'where_plural'=>$database_selectors['where_categories'],             'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group',       'plural'=>'categories_groups',      'title'=>'title','dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'],      'where_plural'=>$database_selectors['where_categories_groups'],      'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',                  'plural'=>'tags',                   'title'=>'tag',  'dir'=>'tag_dir', 'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],                   'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_content_sources";
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
								$join_tables[]="select distinct content_source_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct content_source_id from $df_join_table where $df_id in ($ids_group)";
							}
						} else {
							$where.=" and $df_id in ($ids_group)";
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

				if ($df_id=='content_source_group_id')
				{
					$storage[$object_id]["list_type"]="multi_groups";
					$storage[$object_id]["groups_info"]=$df_objects;
					$smarty->assign("list_type","multi_groups");
					$smarty->assign("groups_info",$df_objects);
				} else {
					$storage[$object_id]["list_type"]="multi_{$df['plural']}";
					$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
					$smarty->assign("list_type","multi_{$df['plural']}");
					$smarty->assign("{$df['plural']}_info",$df_objects);
				}
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
										$join_tables[]="select distinct content_source_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct content_source_id from $df_join_table where $df_id=$df_ids_value_id";
									}
								} else {
									$where.=" and $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct content_source_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct content_source_id from $df_join_table where $df_id in ($df_ids_value)";
							}
						} else {
							$where.=" and $df_id in ($df_ids_value)";
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

					if ($df_id=='content_source_group_id')
					{
						$storage[$object_id]["list_type"]="multi_groups";
						$storage[$object_id]["groups_info"]=$df_objects;
						$smarty->assign("list_type","multi_groups");
						$smarty->assign("groups_info",$df_objects);
					} else {
						$storage[$object_id]["list_type"]="multi_{$df['plural']}";
						$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
						$smarty->assign("list_type","multi_{$df['plural']}");
						$smarty->assign("{$df['plural']}_info",$df_objects);
					}
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

			$result=null;
			if ($_REQUEST[$block_config[$df_var_dir]]!='')
			{
				$result=sql_pr("select $df_selector from $df_table where $df_where_single and ({$df['dir']}=? or $df_selector_locale_dir)",trim($_REQUEST[$block_config[$df_var_dir]]),trim($_REQUEST[$block_config[$df_var_dir]]));
			} else
			{
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

				if ($df_id=='content_source_group_id')
				{
					$storage[$object_id]["list_type"]="groups";
					$storage[$object_id]["group"]=$data_temp[$df['title']];
					$storage[$object_id]["group_info"]=$data_temp;
					$smarty->assign("list_type","groups");
					$smarty->assign("group",$data_temp[$df['title']]);
					$smarty->assign("group_info",$data_temp);
				} else {
					$storage[$object_id]["list_type"]="{$df['plural']}";
					$storage[$object_id]["{$df['single']}"]=$data_temp[$df['title']];
					$storage[$object_id]["{$df['single']}_info"]=$data_temp;
					$smarty->assign("list_type","{$df['plural']}");
					$smarty->assign("{$df['single']}",$data_temp[$df['title']]);
					$smarty->assign("{$df['single']}_info",$data_temp);
				}
				$dynamic_filters_types[]="{$df['plural']}";

				if ($df_join_table)
				{
					if ($df['is_group'])
					{
						$join_tables[]="select distinct content_source_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct content_source_id from $df_join_table where $df_id=$df_object_id";
					}
				} else {
					$where.=" and $df_id=$df_object_id";
				}
			} else
			{
				return 'status_404';
			}
		}
	}

	if ($block_config['skip_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['skip_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where.=" and $config[tables_prefix]content_sources.content_source_id not in (select content_source_id from $config[tables_prefix]categories_content_sources where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct content_source_id from $config[tables_prefix]categories_content_sources where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]content_sources.content_source_id not in (select content_source_id from $config[tables_prefix]tags_content_sources where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct content_source_id from $config[tables_prefix]tags_content_sources where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['content_source_group_ids']<>'' && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['content_source_group_ids']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(",",$group_ids);
			$where.=" and content_source_group_id in ($group_ids)";
		}
	}
	if ($block_config['skip_content_source_groups']<>'' && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['skip_content_source_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(",",$group_ids);
			$where.=" and content_source_group_id not in ($group_ids)";
		}
	}
	if ($block_config['show_content_source_groups']<>'' && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['show_content_source_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(",",$group_ids);
			$where.=" and content_source_group_id in ($group_ids)";
		}
	}

	$metadata=list_content_sourcesMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="content_source_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='content_source_id')
	{
		$sort_by_clear="$config[tables_prefix]content_sources.content_source_id";
	}
	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower($database_selectors[generic_selector_title])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if ($sort_by_clear=='rating')
	{
		$sort_by='rating/rating_amount desc, rating_amount desc';
	} elseif ($sort_by_clear=='rank')
	{
		$sort_by='`rank` asc';
	}

	$from_clause="$config[tables_prefix]content_sources";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.content_source_id=$config[tables_prefix]content_sources.content_source_id";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $database_selectors[where_content_sources] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[content_sources] from $from_clause where $database_selectors[where_content_sources] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[content_sources] from $from_clause where $database_selectors[where_content_sources] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source_id'];
		if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
		{
			$pattern=str_replace("%ID%",$data[$k]['content_source_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_content_sources on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_content_sources.category_id where $database_selectors[where_categories] and content_source_id=".$data[$k]['content_source_id']." order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_content_sources on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_content_sources.tag_id where $database_selectors[where_tags] and content_source_id=".$data[$k]['content_source_id']." order by id asc"));
		}
		if (isset($block_config['show_group_info']))
		{
			if (intval($data[$k]['content_source_group_id'])>0)
			{
				$data[$k]['content_source_group']=mr2array_single(sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] and content_source_group_id=?",intval($data[$k]['content_source_group_id'])));
			}
		}
	}

	if (isset($block_config['pull_videos']))
	{
		$videos_sort_by=trim(strtolower($block_config['pull_videos_sort_by']));
		if (strpos($videos_sort_by," asc")!==false) {$videos_direction="asc";} else {$videos_direction="desc";}
		$videos_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$videos_sort_by));

		if ($videos_sort_by_clear=='')
		{
			$videos_sort_by_clear="rating";
		}

		$videos_sort_by="$videos_sort_by_clear $videos_direction";
		if ($videos_sort_by_clear=='rating_today')
		{
			$date_from=date("Y-m-d");
			$videos_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date='$date_from') desc";
		} elseif ($videos_sort_by_clear=='rating_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$videos_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($videos_sort_by_clear=='rating_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$videos_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($videos_sort_by_clear=='rating') {
			$videos_sort_by="rating/rating_amount desc, rating_amount desc";
		} elseif ($videos_sort_by_clear=='video_viewed_today') {
			$date_from=date("Y-m-d");
			$videos_sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date='$date_from') desc";
		} elseif ($videos_sort_by_clear=='video_viewed_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$videos_sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($videos_sort_by_clear=='video_viewed_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$videos_sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($videos_sort_by_clear=='video_viewed') {
			$videos_sort_by="video_viewed desc";
		} elseif ($videos_sort_by_clear=='ctr') {
			$videos_sort_by="r_ctr desc";
		} elseif ($videos_sort_by_clear=='post_date') {
			$videos_sort_by="$database_selectors[generic_post_date_selector] $videos_direction, video_id $videos_direction";
		} elseif ($videos_sort_by_clear=='most_favourited') {
			$videos_sort_by="favourites_count $videos_direction";
		} elseif ($videos_sort_by_clear=='most_commented') {
			$videos_sort_by="comments_count $videos_direction";
		}

		$limit=intval($block_config['pull_videos_count']);
		if ($limit==0)
		{
			$limit=3;
		}
		foreach ($data as $ke=>$ve)
		{
			$videos=mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and content_source_id=$ve[content_source_id] order by $videos_sort_by limit $limit"));
			foreach ($videos as $k=>$v)
			{
				$videos[$k]['time_passed_from_adding']=get_time_passed($videos[$k]['post_date']);
				$videos[$k]['duration_array']=get_duration_splitted($videos[$k]['duration']);
				$videos[$k]['formats']=get_video_formats($videos[$k]['video_id'],$videos[$k]['file_formats']);
				$videos[$k]['dir_path']=get_dir_by_id($videos[$k]['video_id']);

				$screen_url_base=load_balance_screenshots_url();
				$videos[$k]['screen_url']=$screen_url_base.'/'.get_dir_by_id($videos[$k]['video_id']).'/'.$videos[$k]['video_id'];

				$pattern=str_replace("%ID%",$videos[$k]['video_id'],str_replace("%DIR%",$videos[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$videos[$k]['view_page_url']="$config[project_url]/$pattern";
			}
			$data[$ke]['videos']=$videos;
		}
	}

	if (isset($block_config['pull_albums']))
	{
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

		$albums_sort_by=trim(strtolower($block_config['pull_albums_sort_by']));
		if (strpos($albums_sort_by," asc")!==false) {$albums_direction="asc";} else {$albums_direction="desc";}
		$albums_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$albums_sort_by));

		if ($albums_sort_by_clear=='')
		{
			$albums_sort_by_clear="rating";
		}

		$albums_sort_by="$albums_sort_by_clear $albums_direction";
		if ($albums_sort_by_clear=='rating_today')
		{
			$date_from=date("Y-m-d");
			$albums_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date='$date_from') desc";
		} elseif ($albums_sort_by_clear=='rating_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$albums_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($albums_sort_by_clear=='rating_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$albums_sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($albums_sort_by_clear=='rating') {
			$albums_sort_by="rating/rating_amount desc, rating_amount desc";
		} elseif ($albums_sort_by_clear=='album_viewed_today') {
			$date_from=date("Y-m-d");
			$albums_sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date='$date_from') desc";
		} elseif ($albums_sort_by_clear=='album_viewed_week') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
			$date_to=date("Y-m-d");
			$albums_sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($albums_sort_by_clear=='album_viewed_month') {
			$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
			$date_to=date("Y-m-d");
			$albums_sort_by="(select sum(viewed) from $config[tables_prefix]stats_albums where album_id=$config[tables_prefix]albums.album_id and added_date>='$date_from' and added_date<='$date_to') desc";
		} elseif ($albums_sort_by_clear=='album_viewed') {
			$albums_sort_by="album_viewed desc";
		} elseif ($albums_sort_by_clear=='post_date') {
			$albums_sort_by="$database_selectors[generic_post_date_selector] $albums_direction, album_id $albums_direction";
		} elseif ($albums_sort_by_clear=='most_favourited') {
			$albums_sort_by="favourites_count $albums_direction";
		} elseif ($albums_sort_by_clear=='most_commented') {
			$albums_sort_by="comments_count $albums_direction";
		}

		$limit=intval($block_config['pull_albums_count']);
		if ($limit==0)
		{
			$limit=5;
		}
		foreach ($data as $ke=>$ve)
		{
			$albums=mr2array(sql("select $database_selectors[albums] from $config[tables_prefix]albums where $database_selectors[where_albums] and content_source_id=$ve[content_source_id] order by $albums_sort_by limit $limit"));
			foreach ($albums as $k=>$v)
			{
				$lb_server=load_balance_server($albums[$k]['server_group_id'],$cluster_servers,$cluster_servers_weights);
				$album_id=$albums[$k]['album_id'];
				$dir_path=get_dir_by_id($album_id);

				$albums[$k]['time_passed_from_adding']=get_time_passed($albums[$k]['post_date']);
				$albums[$k]['dir_path']=$dir_path;

				$pattern=str_replace("%ID%",$album_id,str_replace("%DIR%",$albums[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
				$albums[$k]['view_page_url']="$config[project_url]/$pattern";
				$albums[$k]['preview_url']="$lb_server[urls]/preview";
			}
			$data[$ke]['albums']=$albums;
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_content_sourcesGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_content_source_group_dir=trim($_REQUEST[$block_config['var_content_source_group_dir']]);
	$var_content_source_group_id=trim($_REQUEST[$block_config['var_content_source_group_id']]);
	$var_content_source_group_ids=trim($_REQUEST[$block_config['var_content_source_group_ids']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_title_section|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_content_source_group_dir|$var_content_source_group_id|$var_content_source_group_ids|$var_sort_by";
}

function list_content_sourcesCacheControl($block_config)
{
	return "default";
}

function list_content_sourcesMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[content_source_id,sort_id,title,rating,cs_viewed,screenshot1,screenshot2,today_videos,total_videos,today_albums,total_albums,avg_videos_rating,avg_videos_popularity,avg_albums_rating,avg_albums_popularity,comments_count,subscribers_count,rank,last_content_date,added_date]", "is_required"=>1, "default_value"=>"title asc"),

		// static filters
		array("name"=>"show_only_with_screenshot1",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_screenshot2",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_description",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_albums",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_videos",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_albums_or_videos", "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"skip_categories",                 "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",                 "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                       "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                       "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_content_source_groups",      "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_content_source_groups",      "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"content_source_group_ids",        "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>"", "is_deprecated"=>1),

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
		array("name"=>"var_content_source_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group"),
		array("name"=>"var_content_source_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_id"),
		array("name"=>"var_content_source_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_ids"),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_group_info",      "group"=>"subselects", "type"=>"", "is_required"=>0),

		// pull videos
		array("name"=>"pull_videos",         "group"=>"pull_videos", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_videos_count",   "group"=>"pull_videos", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_videos_sort_by", "group"=>"pull_videos", "type"=>"SORTING[duration,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,ctr]", "is_required"=>0, "default_value"=>"post_date desc"),

		// pull albums
		array("name"=>"pull_albums",         "group"=>"pull_albums", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_albums_count",   "group"=>"pull_albums", "type"=>"INT", "is_required"=>0, "default_value"=>"5"),
		array("name"=>"pull_albums_sort_by", "group"=>"pull_albums", "type"=>"SORTING[photos_amount,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,album_viewed,album_viewed_today,album_viewed_week,album_viewed_month,most_favourited,most_commented]", "is_required"=>0, "default_value"=>"post_date desc"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
