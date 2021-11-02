<?php
function random_videoShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$page_id,$formats_videos,$database_selectors,$website_ui_data,$list_countries;

	$formats_videos=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_videos.dat"));

	$where='';

	if ($block_config['is_private']<>'')
	{
		$temp_list=str_replace("|",",",$block_config['is_private']);
		$temp_list=explode(",",$temp_list);
		$temp_list=implode(",",array_map("intval",$temp_list));
		$where.=" and is_private in ($temp_list)";
	}

	$join_tables=array();

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'dvd',            'plural'=>'dvds',            'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>false, 'where_single'=>$database_selectors['where_dvds_active_disabled'],            'where_plural'=>$database_selectors['where_dvds'],            'base_files_url'=>$config['content_url_dvds'],            'link_pattern'=>'WEBSITE_LINK_PATTERN_DVD',   'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'content_source', 'plural'=>'content_sources', 'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>false, 'where_single'=>$database_selectors['where_content_sources_active_disabled'], 'where_plural'=>$database_selectors['where_content_sources'], 'base_files_url'=>$config['content_url_content_sources'], 'link_pattern'=>'WEBSITE_LINK_PATTERN_CS',    'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',          'plural'=>'models',          'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_models_active_disabled'],          'where_plural'=>$database_selectors['where_models'],          'base_files_url'=>$config['content_url_models'],          'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',      'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],      'where_plural'=>$database_selectors['where_categories'],      'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',            'title'=>'tag',   'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],            'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_videos";
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
								$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct video_id from $df_join_table where $df_id in ($ids_group)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$where.=" and $df_id in ($ids_group)";
							}
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
										$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct video_id from $df_join_table where $df_id=$df_ids_value_id";
									}
								} else {
									if ($df['is_group'])
									{
										$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$where.=" and $df_id=$df_ids_value_id";
									}
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
								$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct video_id from $df_join_table where $df_id in ($df_ids_value)";
							}
						} else {
							if ($df['is_group'])
							{
								$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$where.=" and $df_id in ($df_ids_value)";
							}
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
			} else {
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
						$join_tables[]="select distinct video_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct video_id from $df_join_table where $df_id=$df_object_id";
					}
				} else {
					if ($df['is_group'])
					{
						$where.=" and $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$where.=" and $df_id=$df_object_id";
					}
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
			$where.=" and video_id not in (select video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct video_id from $config[tables_prefix]categories_videos where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and video_id not in (select video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct video_id from $config[tables_prefix]tags_videos where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_content_sources']<>'' && !in_array('content_sources',$dynamic_filters_types) && !in_array('multi_content_sources',$dynamic_filters_types) && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$cs_ids=array_map("intval",explode(",",$block_config['skip_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where.=" and content_source_id not in ($cs_ids) ";
		}
	}

	if ($block_config['show_content_sources']<>'' && !in_array('content_sources',$dynamic_filters_types) && !in_array('multi_content_sources',$dynamic_filters_types) && !in_array('content_sources_groups',$dynamic_filters_types) && !in_array('multi_content_sources_groups',$dynamic_filters_types))
	{
		$cs_ids=array_map("intval",explode(",",$block_config['show_content_sources']));
		if (count($cs_ids)>0)
		{
			$cs_ids=implode(",",$cs_ids);
			$where.=" and content_source_id in ($cs_ids) ";
		}
	}

	if ($block_config['skip_dvds']<>'' && !in_array('dvds',$dynamic_filters_types) && !in_array('multi_dvds',$dynamic_filters_types) && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_ids=array_map("intval",explode(",",$block_config['skip_dvds']));
		if (count($dvd_ids)>0)
		{
			$dvd_ids=implode(",",$dvd_ids);
			$where.=" and dvd_id not in ($dvd_ids) ";
		}
	}

	if ($block_config['show_dvds']<>'' && !in_array('dvds',$dynamic_filters_types) && !in_array('multi_dvds',$dynamic_filters_types) && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_ids=array_map("intval",explode(",",$block_config['show_dvds']));
		if (count($dvd_ids)>0)
		{
			$dvd_ids=implode(",",$dvd_ids);
			$where.=" and dvd_id in ($dvd_ids) ";
		}
	}

	if (isset($block_config['days_passed_from']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_from'])+1,date("Y")));
		$where.=" and $database_selectors[generic_post_date_selector]<='$date_passed_from'";
	}
	if (isset($block_config['days_passed_to']))
	{
		$date_passed_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['days_passed_to'])+1,date("Y")));
		$where.=" and $database_selectors[generic_post_date_selector]>='$date_passed_from'";
	}

	$sort_by=trim(strtolower($block_config['sort_by']));
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));

	if ($sort_by_clear=='rating_today')
	{
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating_week') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating_month') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select avg(rating/rating_amount) * 100000 + rating_amount from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='rating') {
		$sort_by="rating/rating_amount desc, rating_amount desc";
	} elseif ($sort_by_clear=='video_viewed_today') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='video_viewed_week') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-6,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='video_viewed_month') {
		$date_from=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));
		$date_to=date("Y-m-d");
		$sort_by="(select sum(viewed) from $config[tables_prefix]stats_videos where video_id=$config[tables_prefix]videos.video_id and added_date>='$date_from' and added_date<='$date_to') desc";
	} elseif ($sort_by_clear=='video_viewed') {
		$sort_by="video_viewed desc";
	} elseif ($sort_by_clear=='pseudo_rand') {
		$sort_by="random1";
	} else {
		if ($sort_by_clear=='post_date') {$sort_by="$database_selectors[generic_post_date_selector] $direction, $config[tables_prefix]videos.video_id $direction";} else
		if ($sort_by_clear=='most_favourited') {$sort_by="favourites_count $direction";}  else
		if ($sort_by_clear=='most_commented') {$sort_by="comments_count $direction";}
	}

	$from_clause="$config[tables_prefix]videos";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.video_id=$config[tables_prefix]videos.video_id";
	}

	$data=mr2array(sql("SELECT $database_selectors[videos] from $from_clause where $database_selectors[where_videos] and load_type_id in (1,2,3) $where order by $sort_by LIMIT $block_config[initial_set_count]"));
	if (count($data)>0)
	{
		$data=$data[mt_rand(1,count($data))-1];
		$video_id=$data['video_id'];

		$dir_path=get_dir_by_id($data['video_id']);
		$data['dir_path']=$dir_path;

		$data['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$data['user_id']));
		if ($data['user']['avatar']!='')
		{
			$data['user']['avatar_url']=$config['content_url_avatars']."/".$data['user']['avatar'];
		}
		$data['username']=$data['user']['display_name'];
		$data['user_avatar']=$data['user']['avatar'];

		if ($data['content_source_id']>0)
		{
			$result=sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id=?",$data['content_source_id']);
			if (mr2rows($result)>0)
			{
				$data['content_source']=mr2array_single($result);
				$data['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data['content_source']['content_source_id'];
				$data['content_source_as_string']=$data['content_source']['title'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
				{
					$pattern=str_replace("%ID%",$data['content_source']['content_source_id'],str_replace("%DIR%",$data['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
					$data['content_source']['view_page_url']="$config[project_url]/$pattern";
				}
			}
			if ($data['content_source']['content_source_group_id']>0)
			{
				$result=sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] and content_source_group_id=?",$data['content_source']['content_source_group_id']);
				if (mr2rows($result)>0)
				{
					$data['content_source_group']=mr2array_single($result);
				}
			}
		}

		if ($data['dvd_id']>0)
		{
			$result=sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where $database_selectors[where_dvds] and dvd_id=?",$data['dvd_id']);
			if (mr2rows($result)>0)
			{
				$data['dvd']=mr2array_single($result);
				$data['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data['dvd']['dvd_id'];
				$data['dvd_as_string']=$data['dvd']['title'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
				{
					$pattern=str_replace("%ID%",$data['dvd']['dvd_id'],str_replace("%DIR%",$data['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
					$data['dvd']['view_page_url']="$config[project_url]/$pattern";
				}
				if ($data['dvd']['dvd_group_id']>0)
				{
					$result=sql_pr("select $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups] and dvd_group_id=?",$data['dvd']['dvd_group_id']);
					if (mr2rows($result)>0)
					{
						$data['dvd_group']=mr2array_single($result);
						$data['dvd_group']['base_files_url']=$config['content_url_dvds'].'/groups/'.$data['dvd_group']['dvd_group_id'];
						if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
						{
							$pattern=str_replace("%ID%",$data['dvd_group']['dvd_group_id'],str_replace("%DIR%",$data['dvd_group']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
							$data['dvd_group']['view_page_url']="$config[project_url]/$pattern";
						}
					}
				}
			}
		}

		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['video_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));

		$screen_url_base=load_balance_screenshots_url();

		$data['file_dimensions']=explode("x",$data['file_dimensions']);
		$data['time_passed_from_adding']=get_time_passed($data['post_date']);
		$data['duration_array']=get_duration_splitted($data['duration']);
		$data['screen_url']="$screen_url_base/$dir_path/$video_id";

		$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_videos.video_id=?) as votes from $config[tables_prefix]flags where group_id=1",$data['video_id']));
		$data['flags']=array();
		foreach($flags as $flag)
		{
			$data['flags'][$flag['external_id']]=$flag['votes'];
		}

		if ($data['admin_flag_id']>0)
		{
			$data['admin_flag']=mr2string(sql_pr("select external_id from $config[tables_prefix]flags where flag_id=?",$data['admin_flag_id']));
		}
		unset($data['admin_flag_id']);

		$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_videos on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_videos.category_id where $database_selectors[where_categories] and video_id=$data[video_id] order by id asc"));
		$category_group_ids=array();
		foreach ($data['categories'] as $k=>$v)
		{
			$data['categories_as_string'].=$v['title'].", ";
			$data['categories'][$k]['base_files_url']=$config['content_url_categories'].'/'.$v['category_id'];
			if ($v['category_group_id']>0)
			{
				$category_group_ids[]=$v['category_group_id'];
			}
		}
		$data['categories_as_string']=rtrim($data['categories_as_string'],", ");

		if (count($category_group_ids)>0)
		{
			$category_group_ids=implode(',',array_unique($category_group_ids));
			$category_groups=mr2array(sql("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] and category_group_id in ($category_group_ids)"));
			foreach ($data['categories'] as $k=>$v)
			{
				if ($v['category_group_id']>0)
				{
					foreach ($category_groups as $category_group)
					{
						if ($v['category_group_id']==$category_group['category_group_id'])
						{
							$category_group['base_files_url']=$config['content_url_categories'].'/groups/'.$category_group['category_group_id'];
							$data['categories'][$k]['category_group']=$category_group;
							break;
						}
					}
				}
			}
		}

		$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_videos on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_videos.tag_id where $database_selectors[where_tags] and video_id=$data[video_id] order by id asc"));
		foreach ($data['tags'] as $v)
		{
			$data['tags_as_string'].=$v['tag'].", ";
		}
		$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

		$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_videos on $config[tables_prefix]models.model_id=$config[tables_prefix]models_videos.model_id where $database_selectors[where_models] and video_id=$data[video_id] order by id asc"));
		$model_group_ids=array();
		foreach ($data['models'] as $k=>$v)
		{
			$data['models'][$k]['base_files_url']=$config['content_url_models'].'/'.$v['model_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				$pattern=str_replace("%ID%",$v['model_id'],str_replace("%DIR%",$v['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
				$data['models'][$k]['view_page_url']="$config[project_url]/$pattern";
			}
			$data['models_as_string'].=$v['title'].", ";
			if ($v['model_group_id']>0)
			{
				$model_group_ids[]=$v['model_group_id'];
			}
		}
		$data['models_as_string']=rtrim($data['models_as_string'],", ");

		if ($data['load_type_id']==1)
		{
			$formats=get_video_formats($video_id,$data['file_formats']);
			$allowed_postfixes_for_view=random_videoGetAllowedPostfixes();
			$named_formats=array();
			foreach ($formats as $format_rec)
			{
				if (!in_array($format_rec['postfix'],$allowed_postfixes_for_view))
				{
					continue;
				}
				foreach ($formats_videos as $format)
				{
					if ($format['postfix']==$format_rec['postfix'])
					{
						$format_rec['timeline_directory']=$format['timeline_directory'];
						$format_rec['title']=$format['title'];
						break;
					}
				}
				$format_rec['preview_url']="$data[screen_url]/preview{$format_rec['postfix']}.jpg";
				$named_formats[$format_rec['postfix']]=$format_rec;
			}
			$data['formats']=$named_formats;
		} else
		{
			$data['preview_url']="$data[screen_url]/preview.jpg";
		}

		$smarty->assign("data",$data);
	}

	return '';
}

function random_videoGetAllowedPostfixes()
{
	global $formats_videos;

	$result=array();
	foreach ($formats_videos as $format)
	{
		if ($format['access_level_id']==0)
		{
			$result[]=$format['postfix'];
		}
	}
	return $result;
}

function random_videoGetHash($block_config)
{
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_content_source_id=intval($_REQUEST[$block_config['var_content_source_id']]);
	$var_content_source_dir=trim($_REQUEST[$block_config['var_content_source_dir']]);
	$var_dvd_id=intval($_REQUEST[$block_config['var_dvd_id']]);
	$var_dvd_dir=trim($_REQUEST[$block_config['var_dvd_dir']]);

	return "$var_category_dir|$var_category_id|$var_tag_dir|$var_tag_id|$var_model_dir|$var_model_id|$var_content_source_id|$var_content_source_dir|$var_dvd_id|$var_dvd_dir";
}

function random_videoCacheControl($block_config)
{
	return "default";
}

function random_videoJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingRandomVideo.js?v={$config['project_version']}";
}

function random_videoAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['video_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'random_video')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where video_id=? and ip=?",$video_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'random_video')));
			} else {
				$now_date=date("Y-m-d");
				sql_pr("insert into $config[tables_prefix]rating_history set video_id=?, ip=?, added_date=?",$video_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]videos set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where video_id=$video_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_videos_count=ratings_videos_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
				if (intval($stats_settings['collect_videos_stats'])==1)
				{
					if (sql_update("update $config[tables_prefix]stats_videos set rating=rating+$rating, rating_amount=rating_amount+1 where video_id=$video_id and added_date='$now_date'")==0)
					{
						sql("insert into $config[tables_prefix]stats_videos set rating=$rating, rating_amount=1, video_id=$video_id, added_date='$now_date'");
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]videos where video_id=$video_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'random_video')));
		}
	} elseif ($_REQUEST['action']=='flag' && intval($_REQUEST['video_id'])>0)
	{
		if ($_REQUEST['flag_id']!='')
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'random_video')));
			}

			$flag=mr2array_single(sql_pr("select * from $config[tables_prefix]flags where group_id=1 and external_id=?",$_REQUEST['flag_id']));
			if (@count($flag)>1)
			{
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					if (intval($_SESSION['user_id'])<1)
					{
						async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'random_video')));
					}
					$tokens_available=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					if ($tokens_available<$flag['tokens_required'])
					{
						async_return_request_status(array(array('error_code'=>'flagging_not_enough_tokens','block'=>'random_video')));
					}
				}
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]flags_history where flag_id=? and video_id=? and ip=?",$flag['flag_id'],$video_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'random_video')));
				} else {
					$now_date=date("Y-m-d");
					sql_pr("insert into $config[tables_prefix]flags_history set video_id=?, flag_id=?, ip=?, added_date=?",$video_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (sql_update("update $config[tables_prefix]flags_videos set votes=votes+1 where video_id=? and flag_id=?",$video_id,$flag['flag_id'])==0)
					{
						sql_pr("insert into $config[tables_prefix]flags_videos set votes=1, video_id=?, flag_id=?",$video_id,$flag['flag_id']);
					}
					if ($flag['is_rating']==1)
					{
						$rating=$flag['rating_weight'];
						sql("update $config[tables_prefix]videos set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where video_id=$video_id");
						if (intval($_SESSION['user_id'])>0)
						{
							sql_pr("update $config[tables_prefix]users set ratings_videos_count=ratings_videos_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
						}

						$stats_settings=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
						if (intval($stats_settings['collect_videos_stats'])==1)
						{
							if (sql_update("update $config[tables_prefix]stats_videos set rating=rating+$rating, rating_amount=rating_amount+1 where video_id=$video_id and added_date='$now_date'")==0)
							{
								sql("insert into $config[tables_prefix]stats_videos set rating=$rating, rating_amount=1, video_id=$video_id, added_date='$now_date'");
							}
						}
					}
					if ($flag['is_event']!=0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=19, user_id=?, video_id=?, flag_external_id=?, added_date=?",$_SESSION['user_id'],$video_id,$flag['external_id'],date("Y-m-d H:i:s"));
					}
					if ($flag['is_tokens']==1 && $flag['tokens_required']>0 && intval($_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$flag['tokens_required'],$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",intval($_SESSION['user_id'])));
					}
					if (trim($_REQUEST['flag_message'])<>'')
					{
						sql_pr("insert into $config[tables_prefix]flags_messages set message=?, video_id=?, flag_id=?, ip=?, country_code=lower(?), user_agent=?, referer=?, added_date=?",trim($_REQUEST['flag_message']),$video_id,$flag['flag_id'],ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($_SERVER['HTTP_REFERER']),date("Y-m-d H:i:s"));
					}
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount, (select votes from $config[tables_prefix]flags_videos where video_id=$video_id and flag_id=?) as flags from $config[tables_prefix]videos where video_id=$video_id",$flag['flag_id']));
				$result_data['flags']=intval($result_data['flags']);
				if ($flag['is_rating']==1)
				{
					$result_data['rating']=floatval($result_data['rating']);
					$result_data['rating_amount']=intval($result_data['rating_amount']);
				} else
				{
					unset($result_data['rating']);
					unset($result_data['rating_amount']);
				}
				if ($flag['is_tokens']==1 && $flag['tokens_required']>0)
				{
					$result_data['tokens_spend']=intval($flag['tokens_required']);
					$result_data['tokens_available']=intval($_SESSION['tokens_available']);
				}
				async_return_request_status(null,null,$result_data);
			} else {
				async_return_request_status(array(array('error_code'=>'invalid_flag','block'=>'random_video')));
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'random_video')));
		}
	} elseif ($_REQUEST['action']=='add_to_favourites' && intval($_REQUEST['video_id'])>0)
	{
		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$user_id=intval($_SESSION['user_id']);
			$video_id=intval($_REQUEST['video_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=$video_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_video','block'=>'random_video')));
			}

			$fav_type=intval($_REQUEST['fav_type']);
			$playlist_id=intval($_REQUEST['playlist_id']);
			if ($playlist_id>0)
			{
				if (mr2number(sql("select count(*) from $config[tables_prefix]playlists where playlist_id=$playlist_id and user_id=$user_id"))==0)
				{
					async_return_request_status(array(array('error_code'=>'invalid_playlist','error_field_code'=>'error_1','block'=>'random_video')));
				}
				$fav_type=10;
			}

			if (mr2number(sql("select count(*) from $config[tables_prefix]fav_videos where user_id=$user_id and video_id=$video_id and fav_type=$fav_type and playlist_id=$playlist_id"))==0)
			{
				sql_pr("insert into $config[tables_prefix]fav_videos set video_id=$video_id, user_id=$user_id, fav_type=$fav_type, playlist_id=$playlist_id, added_date=?",date("Y-m-d H:i:s"));
				if ($playlist_id>0)
				{
					sql_pr("update $config[tables_prefix]playlists set last_content_date=? where playlist_id=$playlist_id",date("Y-m-d H:i:s"));
				}
				fav_videos_changed($video_id,$fav_type);
			}

			$result_data=array();
			$result_data['favourites_total']=intval($_SESSION['favourite_videos_amount']);
			if ($playlist_id>0)
			{
				foreach ($_SESSION['playlists'] as $playlist)
				{
					if ($playlist['playlist_id']==$playlist_id)
					{
						$result_data['favourites_playlist']=intval($playlist['total_videos']);
						break;
					}
				}
			} else
			{
				$result_data['favourites_type']=intval($_SESSION['favourite_videos_summary'][$fav_type]['amount']);
			}
			async_return_request_status(null,null,$result_data);
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'random_video')));
		}
	}
}

function random_videoMetaData()
{
	return array(
		// random selection
		array("name"=>"initial_set_count", "group"=>"random_selection", "type"=>"INT", "is_required"=>1, "default_value"=>"1"),
		array("name"=>"sort_by",           "group"=>"random_selection", "type"=>"SORTING[duration,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,pseudo_rand]","is_required"=>1, "default_value"=>"rating"),

		// static filters
		array("name"=>"skip_categories",      "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",      "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",            "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",            "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_content_sources", "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_content_sources", "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_dvds",            "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"show_dvds",            "group"=>"static_filters", "type"=>"INT_LIST",                  "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",     "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",       "group"=>"static_filters", "type"=>"INT",                       "is_required"=>0, "default_value"=>""),
		array("name"=>"is_private",           "group"=>"static_filters", "type"=>"CHOICE[0,1,2,0|1,0|2,1|2]", "is_required"=>0, "default_value"=>"1"),

		// dynamic filters
		array("name"=>"var_category_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_tag_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_model_dir",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model"),
		array("name"=>"var_model_id",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"model_id"),
		array("name"=>"var_content_source_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs"),
		array("name"=>"var_content_source_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"cs_id"),
		array("name"=>"var_dvd_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd"),
		array("name"=>"var_dvd_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dvd_id"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
