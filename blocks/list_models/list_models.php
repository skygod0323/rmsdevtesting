<?php
function list_modelsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

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

	if (isset($block_config['var_country_id']) && $_REQUEST[$block_config['var_country_id']]<>'')
	{
		$country_id=intval($_REQUEST[$block_config['var_country_id']]);
		$where.=" and country_id=$country_id";

		$smarty->assign('cities',mr2array(sql_pr("select city, count(*) as total_models from $config[tables_prefix]models where country_id=? and city!='' group by city",$country_id)));
		$smarty->assign('states',mr2array(sql_pr("select state, count(*) as total_models from $config[tables_prefix]models where country_id=? and state!='' group by state",$country_id)));

		$smarty->assign('country_id',$country_id);
		$smarty->assign('country',$list_countries['name'][$country_id]);
		$storage[$object_id]['country_id']=$country_id;
		$storage[$object_id]['country']=$list_countries['name'][$country_id];
	}

	if (isset($block_config['var_state']) && $_REQUEST[$block_config['var_state']]<>'')
	{
		$state=sql_escape(trim($_REQUEST[$block_config['var_state']]));
		$where.=" and state='$state'";

		$smarty->assign('cities',mr2array(sql_pr("select city, count(*) as total_models from $config[tables_prefix]models where state=? and city!='' group by city",trim($_REQUEST[$block_config['var_state']]))));

		$smarty->assign('state',trim($_REQUEST[$block_config['var_state']]));
		$storage[$object_id]['state']=trim($_REQUEST[$block_config['var_state']]);
	}

	if (isset($block_config['var_city']) && $_REQUEST[$block_config['var_city']]<>'')
	{
		$city=sql_escape(trim($_REQUEST[$block_config['var_city']]));
		$where.=" and city like '%$city%'";

		$smarty->assign('city',trim($_REQUEST[$block_config['var_city']]));
		$storage[$object_id]['city']=trim($_REQUEST[$block_config['var_city']]);
	}
	if (isset($block_config['var_hair_id']) && $_REQUEST[$block_config['var_hair_id']]<>'')
	{
		$where.=" and hair_id=".intval($_REQUEST[$block_config['var_hair_id']]);
		$smarty->assign('hair_id',intval($_REQUEST[$block_config['var_hair_id']]));
		$storage[$object_id]['hair_id'] = intval($_REQUEST[$block_config['var_hair_id']]);
	}
	if (isset($block_config['var_eye_color_id']) && $_REQUEST[$block_config['var_eye_color_id']]<>'')
	{
		$where.=" and eye_color_id=".intval($_REQUEST[$block_config['var_eye_color_id']]);
		$smarty->assign('eye_color_id',intval($_REQUEST[$block_config['var_eye_color_id']]));
		$storage[$object_id]['eye_color_id'] = intval($_REQUEST[$block_config['var_eye_color_id']]);
	}

	if (isset($block_config['var_gender_id']) && $_REQUEST[$block_config['var_gender_id']]<>'')
	{
		$where.=" and gender_id=".intval($_REQUEST[$block_config['var_gender_id']]);
		$smarty->assign('gender_id',intval($_REQUEST[$block_config['var_gender_id']]));
		$storage[$object_id]['gender_id'] = intval($_REQUEST[$block_config['var_gender_id']]);
	} elseif (isset($block_config['show_gender']))
	{
		$where.=" and gender_id=".intval($block_config['show_gender']);
		$smarty->assign('gender_id',intval($block_config['show_gender']));
		$storage[$object_id]['gender_id'] = intval($block_config['show_gender']);
	}

	if (isset($block_config['var_age_from']) && intval($_REQUEST[$block_config['var_age_from']])>0)
	{
		$where.=" and age >= ".intval($_REQUEST[$block_config['var_age_from']]);
	}
	if (isset($block_config['var_age_to']) && intval($_REQUEST[$block_config['var_age_to']])>0)
	{
		$where.=" and age < ".intval($_REQUEST[$block_config['var_age_to']]);
	}

	for ($i = 1; $i <= 10; $i++)
	{
		if (isset($block_config["var_custom$i"]) && trim($_REQUEST[$block_config["var_custom$i"]]))
		{
			$where .= " and custom$i='" . sql_escape(trim($_REQUEST[$block_config["var_custom$i"]])) . "'";
		}
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
							$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or $database_selectors[locale_field_description] like '%$temp_value%' or alias like '%$temp_value%'";
						} else {
							$where2.=" or $database_selectors[locale_field_title] like '%$temp_value%' or alias like '%$temp_value%'";
						}
					}
				}
				if ($where2<>'')
				{
					$where2=substr($where2,4);
				} else {
					if ($search_scope==0)
					{
						$where2.="$database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%' or alias like '%$q%'";
					} else {
						$where2.="$database_selectors[locale_field_title] like '%$q%' or alias like '%$q%'";
					}
				}
				$where.=" and ($where2)";
			} else
			{
				if ($search_scope==0)
				{
					$where.=" and ($database_selectors[locale_field_title] like '%$q%' or $database_selectors[locale_field_description] like '%$q%' or alias like '%$q%')";
				} else {
					$where.=" and ($database_selectors[locale_field_title] like '%$q%' or alias like '%$q%')";
				}
			}
		}

		$storage[$object_id]['list_type']="search";
		$storage[$object_id]['search_keyword']=$unescaped_q;
		$storage[$object_id]['url_prefix']="?$block_config[var_search]=$unescaped_q&";
		$smarty->assign('list_type',"search");
		$smarty->assign('search_keyword',$unescaped_q);
	}

	if (isset($block_config['show_only_with_screenshot1']))
	{
		$where.=" and screenshot1 <> ''";
	}
	if (isset($block_config['show_only_with_screenshot2']))
	{
		$where.=" and screenshot2 <> ''";
	}

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $database_selectors[locale_field_description]<>''";
	}

	if ($_SESSION['status_id']==3)
	{
		// nothing
	} elseif ($_SESSION['user_id']>0)
	{
		$where.=" and access_level_id in (0,1)";
	} else {
		$where.=" and access_level_id=0";
	}

	$join_tables=array();

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model_group',    'plural'=>'models_groups',     'title'=>'title','dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_models_groups_active_disabled'],     'where_plural'=>$database_selectors['where_models_groups'],     'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title','dir'=>'dir',     'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title','dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',  'dir'=>'tag_dir', 'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_models";
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
								$join_tables[]="select distinct model_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct model_id from $df_join_table where $df_id in ($ids_group)";
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

				if ($df_id=='model_group_id')
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
										$join_tables[]="select distinct model_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct model_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct model_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct model_id from $df_join_table where $df_id in ($df_ids_value)";
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

					if ($df_id=='model_group_id')
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

				if ($df_id=='model_group_id')
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
						$join_tables[]="select distinct model_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct model_id from $df_join_table where $df_id=$df_object_id";
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

	if (intval($block_config['mode_related'])>0 || (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0))
	{
		//1 - tags
		//2 - categories
		//3 - country
		//4 - city
		//5 - gender
		//6 - age
		//7 - height
		//8 - weight
		//9 - hair
		//10 - videos
		//11 - albums
		//12 - group
		//13 - state

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$result=null;
		if (isset($block_config['var_model_id']) && intval($_REQUEST[$block_config['var_model_id']])>0)
		{
			$result=sql_pr("select $database_selectors[models] from $config[tables_prefix]models where model_id=?",intval($_REQUEST[$block_config['var_model_id']]));
		} elseif (trim($_REQUEST[$block_config['var_model_dir']])<>'')
		{
			$result=sql_pr("select $database_selectors[models] from $config[tables_prefix]models where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_model_dir']]),trim($_REQUEST[$block_config['var_model_dir']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$mode_related_name='';
			$data_temp=mr2array_single($result);
			$model_id=$data_temp["model_id"];

			$where.=" and $config[tables_prefix]models.model_id<>$model_id";
			if ($mode_related==1)
			{
				$mode_related_name='tags';

				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags_models where model_id=?",$model_id));
				if (count($tag_ids)>0)
				{
					$tag_ids=implode(",",$tag_ids);
					$join_tables[]="select distinct model_id from $config[tables_prefix]tags_models where tag_id in ($tag_ids)";
				}
			} elseif ($mode_related==2)
			{
				$mode_related_name='categories';

				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_models where model_id=?",$model_id));
				if (count($category_ids)>0 && intval($block_config['mode_related_category_group_id'])>0)
				{
					$category_ids=implode(',',$category_ids);
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_id in ($category_ids) and (category_group_id=? or category_group_id in (select category_group_id from $config[tables_prefix]categories_groups where external_id=?))",intval($block_config['mode_related_category_group_id']),trim($block_config['mode_related_category_group_id'])));
				}
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$join_tables[]="select distinct model_id from $config[tables_prefix]categories_models where category_id in ($category_ids)";
				}
			} elseif ($mode_related==3)
			{
				$mode_related_name='country';

				$country_id=intval($data_temp["country_id"]);
				if ($country_id>0)
				{
					$where.=" and country_id=$country_id";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==4)
			{
				$mode_related_name='city';

				$city=sql_escape(trim($data_temp["city"]));
				if ($city!='')
				{
					$where.=" and city='$city'";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==5)
			{
				$mode_related_name='gender';

				$gender_id=intval($data_temp["gender_id"]);
				$where.=" and gender_id=$gender_id";
			} elseif ($mode_related==6)
			{
				$mode_related_name='age';

				$age=intval($data_temp["age"]);
				if ($age>0)
				{
					$age_min=max(0,$age-3);
					$age_max=$age+3;
					$where.=" and age between $age_min and $age_max";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==7)
			{
				$mode_related_name='height';

				$height=intval($data_temp["height"]);
				if ($height>0)
				{
					$height_min=max(0,$height-5);
					$height_max=$height+5;
					$where.=" and cast(height as signed) between $height_min and $height_max";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==8)
			{
				$mode_related_name='weight';

				$weight=intval($data_temp["weight"]);
				if ($weight>0)
				{
					$weight_min=max(0,$weight-5);
					$weight_max=$weight+5;
					$where.=" and cast(weight as signed) between $weight_min and $weight_max";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==9)
			{
				$mode_related_name='hair';

				$hair_id=intval($data_temp["hair_id"]);
				if ($hair_id>0) {
					$where.=" and hair_id=$hair_id";
				} else {
					$where.=" and 0=1";
				}
			} elseif ($mode_related==10)
			{
				$mode_related_name='videos';

				$where.=" and model_id in (select mv2.model_id from $config[tables_prefix]models_videos mv1 inner join $config[tables_prefix]models_videos mv2 on mv1.model_id=$model_id and mv2.model_id!=$model_id and mv1.video_id=mv2.video_id)";
			} elseif ($mode_related==11)
			{
				$mode_related_name='albums';

				$where.=" and model_id in (select ma2.model_id from $config[tables_prefix]models_albums ma1 inner join $config[tables_prefix]models_albums ma2 on ma1.model_id=$model_id and ma2.model_id!=$model_id and ma1.album_id=ma2.album_id)";
			} elseif ($mode_related==12)
			{
				$mode_related_name='group';

				$model_group_id=intval($data_temp["model_group_id"]);
				$where.=" and model_group_id=$model_group_id";
			} elseif ($mode_related==13)
			{
				$mode_related_name='state';

				$state=sql_escape(trim($data_temp["state"]));
				if ($state!='')
				{
					$where.=" and state='$state'";
				} else {
					$where.=" and 0=1";
				}
			}

			$storage[$object_id]['list_type']="related";
			$storage[$object_id]['related_mode']=$mode_related;
			$storage[$object_id]['related_mode_name']=$mode_related_name;
			$storage[$object_id]['model']=$data_temp['title'];
			$storage[$object_id]['model_info']=$data_temp;

			$smarty->assign('list_type',"related");
			$smarty->assign('related_mode',$mode_related);
			$smarty->assign('related_mode_name',$mode_related_name);
			$smarty->assign('model',$data_temp['title']);
			$smarty->assign('model_info',$data_temp);
		}
	}

	if ($block_config['skip_model_groups']<>'' && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['skip_model_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and model_group_id not in ($group_ids)";
		}
	}

	if ($block_config['show_model_groups']<>'' && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['show_model_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and model_group_id in ($group_ids)";
		}
	}

	if ($block_config['skip_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['skip_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where.=" and $config[tables_prefix]models.model_id not in (select model_id from $config[tables_prefix]categories_models where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct model_id from $config[tables_prefix]categories_models where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]models.model_id not in (select model_id from $config[tables_prefix]tags_models where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct model_id from $config[tables_prefix]tags_models where tag_id in ($tag_ids)";
		}
	}

	$metadata=list_modelsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="model_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='model_id')
	{
		$sort_by_clear="$config[tables_prefix]models.model_id";
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

	$from_clause="$config[tables_prefix]models";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.model_id=$config[tables_prefix]models.model_id";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $database_selectors[where_models] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[models] from $from_clause where $database_selectors[where_models] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[models] from $from_clause where $database_selectors[where_models] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=$config['content_url_models'].'/'.$data[$k]['model_id'];
		if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
		{
			$pattern=str_replace("%ID%",$data[$k]['model_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_models on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_models.category_id where $database_selectors[where_categories] and model_id=".$data[$k]['model_id']." order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_models on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_models.tag_id where $database_selectors[where_tags] and model_id=".$data[$k]['model_id']." order by id asc"));
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
			if (trim($block_config['search_redirect_pattern'])<>'' || $website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				if (trim($block_config['search_redirect_pattern'])<>'')
				{
					$pattern=str_replace("%ID%",$data[0]['model_id'],str_replace("%DIR%",$data[0]['dir'],trim($block_config['search_redirect_pattern'])));
				} else {
					$pattern=str_replace("%ID%",$data[0]['model_id'],str_replace("%DIR%",$data[0]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
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
			$videos_sort_by="$database_selectors[generic_post_date_selector] $videos_direction, $config[tables_prefix]videos.video_id $videos_direction";
		} elseif ($videos_sort_by_clear=='most_favourited') {
			$videos_sort_by="favourites_count $videos_direction";
		} elseif ($videos_sort_by_clear=='most_commented') {
			$videos_sort_by="comments_count $videos_direction";
		}

		$selected_video_ids=array(0);

		$limit=intval($block_config['pull_videos_count']);
		if ($limit==0)
		{
			$limit=3;
		}

		foreach ($data as $ke=>$ve)
		{
			$not_clause="$config[tables_prefix]videos.video_id not in (".implode(',',$selected_video_ids).")";
			$videos=mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos inner join $config[tables_prefix]models_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]models_videos.video_id where $not_clause and $database_selectors[where_videos] and $config[tables_prefix]models_videos.model_id=$ve[model_id] order by $videos_sort_by limit $limit"));
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

				if (!isset($block_config['pull_videos_duplicates']))
				{
					$selected_video_ids[]=$v['video_id'];
				}
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
			$albums_sort_by="$database_selectors[generic_post_date_selector] $albums_direction, $config[tables_prefix]albums.album_id $albums_direction";
		} elseif ($albums_sort_by_clear=='most_favourited') {
			$albums_sort_by="favourites_count $albums_direction";
		} elseif ($albums_sort_by_clear=='most_commented') {
			$albums_sort_by="comments_count $albums_direction";
		}

		$selected_album_ids=array(0);

		$limit=intval($block_config['pull_albums_count']);
		if ($limit==0)
		{
			$limit=5;
		}
		foreach ($data as $ke=>$ve)
		{
			$not_clause="$config[tables_prefix]albums.album_id not in (".implode(',',$selected_album_ids).")";
			$albums=mr2array(sql("select $database_selectors[albums] from $config[tables_prefix]albums inner join $config[tables_prefix]models_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]models_albums.album_id where $not_clause and $database_selectors[where_albums] and $config[tables_prefix]models_albums.model_id=$ve[model_id] order by $albums_sort_by limit $limit"));
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

				if (!isset($block_config['pull_albums_duplicates']))
				{
					$selected_album_ids[]=$v['album_id'];
				}
			}
			$data[$ke]['albums']=$albums;
		}
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['country']=$list_countries['name'][$data[$k]['country_id']];
	}

	$smarty->assign("data",$data);

	return '';
}

function list_modelsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_model_group_dir=trim($_REQUEST[$block_config['var_model_group_dir']]);
	$var_model_group_id=trim($_REQUEST[$block_config['var_model_group_id']]);
	$var_model_group_ids=trim($_REQUEST[$block_config['var_model_group_ids']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_country_id=intval($_REQUEST[$block_config['var_country_id']]);
	$var_state=trim($_REQUEST[$block_config['var_state']]);
	$var_city=trim($_REQUEST[$block_config['var_city']]);
	$var_hair_id=intval($_REQUEST[$block_config['var_hair_id']]);
	$var_eye_color_id=intval($_REQUEST[$block_config['var_eye_color_id']]);
	$var_gender_id=intval($_REQUEST[$block_config['var_gender_id']]);
	$var_age_from=intval($_REQUEST[$block_config['var_age_from']]);
	$var_age_to=intval($_REQUEST[$block_config['var_age_to']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_mode_related=trim($_REQUEST[$block_config['var_mode_related']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);

	$var_custom = '';
	for ($i = 1; $i <= 10; $i++)
	{
		$var_custom .= trim($_REQUEST[$block_config["var_custom$i"]]) . '|';
	}

	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
		{
			return "runtime_nocache";
		}
	}
	return "$from|$items_per_page|$var_title_section|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_gender_id|$var_country_id|$var_state|$var_city|$var_hair_id|$var_eye_color_id|$var_age_from|$var_age_to|$var_search|$var_mode_related|$var_model_dir|$var_model_id|$var_sort_by|$var_custom|$_SESSION[status_id]";
}

function list_modelsCacheControl($block_config)
{
	return "status_specific";
}

function list_modelsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[model_id,sort_id,title,birth_date,age,rating,model_viewed,screenshot1,screenshot2,today_videos,total_videos,today_albums,total_albums,today_posts,total_posts,avg_videos_rating,avg_videos_popularity,avg_albums_rating,avg_albums_popularity,avg_posts_rating,avg_posts_popularity,comments_count,subscribers_count,rank,last_content_date,added_date]","is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_screenshot1",      "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_with_screenshot2",      "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_with_description",      "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_with_albums",           "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_videos",           "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_posts",            "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_albums_or_videos", "group"=>"static_filters", "type"=>"INT",           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_gender",                     "group"=>"static_filters", "type"=>"CHOICE[0,1,2]", "is_required"=>0),
		array("name"=>"skip_model_groups",               "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),
		array("name"=>"show_model_groups",               "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_categories",                 "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",                 "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                       "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                       "group"=>"static_filters", "type"=>"INT_LIST",      "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_title_section",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
		array("name"=>"var_model_group_dir",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group"),
		array("name"=>"var_model_group_id",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_id"),
		array("name"=>"var_model_group_ids",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_ids"),
		array("name"=>"var_category_dir",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",        "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_ids",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_ids"),
		array("name"=>"var_category_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group"),
		array("name"=>"var_category_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_id"),
		array("name"=>"var_category_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_group_ids"),
		array("name"=>"var_tag_dir",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag"),
		array("name"=>"var_tag_id",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_id"),
		array("name"=>"var_tag_ids",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"tag_ids"),
		array("name"=>"var_gender_id",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"gender_id"),
		array("name"=>"var_country_id",         "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"country_id"),
		array("name"=>"var_state",              "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"state"),
		array("name"=>"var_city",               "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"city"),
		array("name"=>"var_hair_id",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"hair_id"),
		array("name"=>"var_eye_color_id",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"eye_color_id"),
		array("name"=>"var_age_from",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"age_from"),
		array("name"=>"var_age_to",             "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"age_to"),
		array("name"=>"var_custom1",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom1"),
		array("name"=>"var_custom2",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom2"),
		array("name"=>"var_custom3",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom3"),
		array("name"=>"var_custom4",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom4"),
		array("name"=>"var_custom5",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom5"),
		array("name"=>"var_custom6",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom6"),
		array("name"=>"var_custom7",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom7"),
		array("name"=>"var_custom8",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom8"),
		array("name"=>"var_custom9",            "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom9"),
		array("name"=>"var_custom10",           "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"custom10"),

		// search
		array("name"=>"var_search",              "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",           "group"=>"search", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"search_scope",            "group"=>"search", "type"=>"CHOICE[0,1]", "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_redirect_enabled", "group"=>"search", "type"=>"",            "is_required"=>0),
		array("name"=>"search_redirect_pattern", "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>""),

		// related
		array("name"=>"mode_related",                   "group"=>"related", "type"=>"CHOICE[1,2,3,13,4,5,6,7,8,9,10,11,12]", "is_required"=>0, "default_value"=>"6"),
		array("name"=>"var_model_dir",                  "group"=>"related", "type"=>"STRING",                                "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_model_id",                   "group"=>"related", "type"=>"STRING",                                "is_required"=>0, "default_value"=>"id"),
		array("name"=>"var_mode_related",               "group"=>"related", "type"=>"STRING",                                "is_required"=>0, "default_value"=>"mode_related"),
		array("name"=>"mode_related_category_group_id", "group"=>"related", "type"=>"INT",                                   "is_required"=>0),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),

		// pull videos
		array("name"=>"pull_videos",            "group"=>"pull_videos", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_videos_count",      "group"=>"pull_videos", "type"=>"INT", "is_required"=>0, "default_value"=>"4"),
		array("name"=>"pull_videos_sort_by",    "group"=>"pull_videos", "type"=>"SORTING[duration,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,ctr]","is_required"=>0, "default_value"=>"post_date desc"),
		array("name"=>"pull_videos_duplicates", "group"=>"pull_videos", "type"=>"",    "is_required"=>0),

		// pull albums
		array("name"=>"pull_albums",            "group"=>"pull_albums", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_albums_count",      "group"=>"pull_albums", "type"=>"INT", "is_required"=>0, "default_value"=>"4"),
		array("name"=>"pull_albums_sort_by",    "group"=>"pull_albums", "type"=>"SORTING[photos_amount,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,album_viewed,album_viewed_today,album_viewed_week,album_viewed_month,most_favourited,most_commented]","is_required"=>0, "default_value"=>"post_date desc"),
		array("name"=>"pull_albums_duplicates", "group"=>"pull_albums", "type"=>"",    "is_required"=>0),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
