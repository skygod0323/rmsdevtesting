<?php
function list_dvds_groupsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (isset($block_config['show_only_with_dvds']))
	{
		$where.=" and total_dvds>0";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}

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

	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		$q=trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]),false));
		$q=trim(str_replace('[dash]','-',str_replace('-',' ',str_replace('--','[dash]',str_replace('?','',$q)))));

		$unescaped_q=$q;
		if ($q=='')
		{
			$where.=" and 1=0";
		} else
		{
			$q=sql_escape($q);
			$search_scope=intval($block_config['search_scope']);
			if ($block_config['search_method']==2)
			{
				$where2='';
				$temp=explode(" ",$q);
				foreach ($temp as $temp_value)
				{
					$length=strlen($temp_value);
					if (function_exists('mb_detect_encoding'))
					{
						$length=mb_strlen($temp_value,mb_detect_encoding($temp_value));
					}
					if ($length>2)
					{
						if ($search_scope==0)
						{
							$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or $database_selectors[locale_field_description] like '%$temp_value%'";
						} else {
							$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%'";
						}
					}
				}
				if ($where2<>'')
				{
					$where2=substr($where2,4);
				} else {
					if ($search_scope==0)
					{
						$where2.="$database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%'";
					} else {
						$where2.="$database_selectors[locale_field_title] like '%$q%'";
					}
				}
				$where.=" and ($where2)";
			} else
			{
				if ($search_scope==0)
				{
					$where.=" and ($database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%')";
				} else {
					$where.=" and ($database_selectors[locale_field_title] like '%$q%')";
				}
			}
		}

		$storage[$object_id]['list_type']="search";
		$storage[$object_id]['search_keyword']=$unescaped_q;
		$storage[$object_id]['url_prefix']="?$block_config[var_search]=$unescaped_q&";
		$smarty->assign('list_type',"search");
		$smarty->assign('search_keyword',$unescaped_q);
	}

	$join_tables=array();

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',          'plural'=>'models',            'title'=>'title', 'dir'=>'dir',     'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_models_active_disabled'],            'where_plural'=>$database_selectors['where_models'],            'base_files_url'=>$config['content_url_models'],             'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',    'plural'=>'models_groups',     'title'=>'title', 'dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_models_groups_active_disabled'],     'where_plural'=>$database_selectors['where_models_groups'],     'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title', 'dir'=>'dir',     'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title', 'dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',   'dir'=>'tag_dir', 'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_dvds_groups";
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
								$join_tables[]="select distinct dvd_group_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct dvd_group_id from $df_join_table where $df_id in ($ids_group)";
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

				$storage[$object_id]["list_type"]="multi_{$df['plural']}";
				$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
				$smarty->assign("list_type","multi_{$df['plural']}");
				$smarty->assign("{$df['plural']}_info",$df_objects);
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
										$join_tables[]="select distinct dvd_group_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct dvd_group_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct dvd_group_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct dvd_group_id from $df_join_table where $df_id in ($df_ids_value)";
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

					$storage[$object_id]["list_type"]="multi_{$df['plural']}";
					$storage[$object_id]["{$df['plural']}_info"]=$df_objects;
					$smarty->assign("list_type","multi_{$df['plural']}");
					$smarty->assign("{$df['plural']}_info",$df_objects);
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

				$storage[$object_id]["list_type"]="{$df['plural']}";
				$storage[$object_id]["{$df['single']}"]=$data_temp[$df['title']];
				$storage[$object_id]["{$df['single']}_info"]=$data_temp;
				$smarty->assign("list_type","{$df['plural']}");
				$smarty->assign("{$df['single']}",$data_temp[$df['title']]);
				$smarty->assign("{$df['single']}_info",$data_temp);
				$dynamic_filters_types[]="{$df['plural']}";

				if ($df_join_table)
				{
					if ($df['is_group'])
					{
						$join_tables[]="select distinct dvd_group_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct dvd_group_id from $df_join_table where $df_id=$df_object_id";
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
			$where.=" and $config[tables_prefix]dvds_groups.dvd_group_id not in (select dvd_group_id from $config[tables_prefix]categories_dvds_groups where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct dvd_group_id from $config[tables_prefix]categories_dvds_groups where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]dvds_groups.dvd_group_id not in (select dvd_group_id from $config[tables_prefix]tags_dvds_groups where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct dvd_group_id from $config[tables_prefix]tags_dvds_groups where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['skip_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$where.=" and $config[tables_prefix]dvds_groups.dvd_group_id not in (select dvd_group_id from $config[tables_prefix]models_dvds_groups where model_id in ($model_ids)) ";
		}
	}

	if ($block_config['show_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['show_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$join_tables[]="select distinct dvd_group_id from $config[tables_prefix]models_dvds_groups where model_id in ($model_ids)";
		}
	}

	$metadata=list_dvds_groupsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="dvd_group_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='dvd_group_id')
	{
		$sort_by_clear="$config[tables_prefix]dvds_groups.dvd_group_id";
	}
	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower($database_selectors[generic_selector_title])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";

	$from_clause="$config[tables_prefix]dvds_groups";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.dvd_group_id=$config[tables_prefix]dvds_groups.dvd_group_id";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $database_selectors[where_dvds_groups] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[dvds_groups] from $from_clause where $database_selectors[where_dvds_groups] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[dvds_groups] from $from_clause where $database_selectors[where_dvds_groups] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=$config['content_url_dvds'].'/groups/'.$data[$k]['dvd_group_id'];
		if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
		{
			$pattern=str_replace("%ID%",$data[$k]['dvd_group_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_dvds_groups on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_dvds_groups.category_id where $database_selectors[where_categories] and dvd_group_id=".$data[$k]['dvd_group_id']." order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_dvds_groups on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_dvds_groups.tag_id where $database_selectors[where_tags] and dvd_group_id=".$data[$k]['dvd_group_id']." order by id asc"));
		}
		if (isset($block_config['show_models_info']))
		{
			$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_dvds_groups on $config[tables_prefix]models.model_id=$config[tables_prefix]models_dvds_groups.model_id where $database_selectors[where_models] and dvd_group_id=".$data[$k]['dvd_group_id']." order by id asc"));
			foreach ($data[$k]['models'] as $k2=>$v2)
			{
				$data[$k]['models'][$k2]['base_files_url']=$config['content_url_models'].'/'.$v2['model_id'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
				{
					$pattern=str_replace("%ID%",$v2['model_id'],str_replace("%DIR%",$v2['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
					$data[$k]['models'][$k2]['view_page_url']="$config[project_url]/$pattern";
				}
			}
		}
	}

	if ($storage[$object_id]['list_type']=="search" && isset($block_config['search_redirect_enabled']))
	{
		$check_count=count($data);
		if (isset($block_config['var_from']) && isset($total_count))
		{
			$check_count=$total_count;
		}
		if ($check_count==1)
		{
			if (trim($block_config['search_redirect_pattern'])<>'' || $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
			{
				if (trim($block_config['search_redirect_pattern'])<>'')
				{
					$pattern=str_replace("%ID%",$data[0]['dvd_group_id'],str_replace("%DIR%",$data[0]['dir'],trim($block_config['search_redirect_pattern'])));
				} else {
					$pattern=str_replace("%ID%",$data[0]['dvd_group_id'],str_replace("%DIR%",$data[0]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
				}
				if (is_url($pattern))
				{
					return "status_302:$pattern";
				} else {
					return "status_302:$config[project_url]/$pattern";
				}
			}
		}
	}

	if (isset($block_config['pull_dvds']))
	{
		$dvds_sort_by=trim(strtolower($block_config['pull_dvds_sort_by']));
		if (strpos($dvds_sort_by," asc")!==false) {$dvds_direction="asc";} else {$dvds_direction="desc";}
		$dvds_sort_by_clear=str_replace(" desc","",str_replace(" asc","",$dvds_sort_by));

		if ($dvds_sort_by_clear=='title')
		{
			$dvds_sort_by_clear="lower($database_selectors[generic_selector_title])";
			if (strpos($dvds_sort_by," desc")!==false) {$dvds_direction="desc";} else {$dvds_direction="asc";}
		}

		if ($dvds_sort_by_clear=='')
		{
			$dvds_sort_by_clear="dvd_id";
			$dvds_direction="asc";
		}

		$dvds_sort_by="$dvds_sort_by_clear $dvds_direction";
		if ($dvds_sort_by_clear=='rating') {
			$dvds_sort_by="rating/rating_amount desc, rating_amount desc";
		} elseif ($dvds_sort_by_clear=='dvd_viewed') {
			$dvds_sort_by="dvd_viewed desc";
		}

		$limit=intval($block_config['pull_dvds_count']);
		if ($limit==0)
		{
			$limit="";
		} else {
			$limit="limit $limit";
		}
		foreach ($data as $ke=>$ve)
		{
			$dvds=mr2array(sql("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_group_id=$ve[dvd_group_id] order by $dvds_sort_by $limit"));
			foreach ($dvds as $k=>$v)
			{
				$dvds[$k]['base_files_url']=$config['content_url_dvds'].'/'.$dvds[$k]['dvd_id'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
				{
					$pattern=str_replace("%ID%",$dvds[$k]['dvd_id'],str_replace("%DIR%",$dvds[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
					$dvds[$k]['view_page_url']="$config[project_url]/$pattern";
				}
			}
			$data[$ke]['dvds']=$dvds;
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_dvds_groupsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
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
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_model_ids=trim($_REQUEST[$block_config['var_model_ids']]);
	$var_model_group_dir=trim($_REQUEST[$block_config['var_model_group_dir']]);
	$var_model_group_id=trim($_REQUEST[$block_config['var_model_group_id']]);
	$var_model_group_ids=trim($_REQUEST[$block_config['var_model_group_ids']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);

	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
		{
			return "runtime_nocache";
		}
	}

	return "$from|$items_per_page|$var_title_section|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_search|$var_sort_by";
}

function list_dvds_groupsCacheControl($block_config)
{
	return "default";
}

function list_dvds_groupsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[dvd_group_id,sort_id,title,total_dvds]", "is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_dvds",        "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"skip_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_title_section",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
		array("name"=>"var_category_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_ids",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_ids"),
		array("name"=>"var_category_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group"),
		array("name"=>"var_category_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_id"),
		array("name"=>"var_category_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_ids"),
		array("name"=>"var_tag_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_tag_ids",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_ids"),
		array("name"=>"var_model_dir",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model"),
		array("name"=>"var_model_id",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_id"),
		array("name"=>"var_model_ids",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_ids"),
		array("name"=>"var_model_group_dir",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group"),
		array("name"=>"var_model_group_id",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_id"),
		array("name"=>"var_model_group_ids",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_group_ids"),

		// search
		array("name"=>"var_search",              "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",           "group"=>"search", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"search_scope",            "group"=>"search", "type"=>"CHOICE[0,1]", "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_redirect_enabled", "group"=>"search", "type"=>"",            "is_required"=>0),
		array("name"=>"search_redirect_pattern", "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>""),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_models_info",     "group"=>"subselects", "type"=>"", "is_required"=>0),

		// pull dvds
		array("name"=>"pull_dvds",         "group"=>"pull_dvds", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_dvds_count",   "group"=>"pull_dvds", "type"=>"INT", "is_required"=>0, "default_value"=>"0"),
		array("name"=>"pull_dvds_sort_by", "group"=>"pull_dvds", "type"=>"SORTING[dvd_id,sort_id,title,rating,dvd_viewed,today_videos,total_videos,total_videos_duration,avg_videos_rating,avg_videos_popularity,comments_count,subscribers_count,last_content_date,added_date]","is_required"=>0, "default_value"=>"dvd_id asc"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
