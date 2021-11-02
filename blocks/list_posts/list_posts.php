<?php
function list_postsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data,$list_countries;

	if ($_REQUEST['action'] == 'delete_posts' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id'] > 0)
		{
			$user_id = intval($_SESSION['user_id']);
			$delete_ids = implode(",", array_map("intval", $_REQUEST['delete']));
			$delete_ids = mr2array_list(sql("select post_id from $config[tables_prefix]posts where user_id=$user_id and post_id in ($delete_ids) and is_locked=0"));
			if (count($delete_ids) > 0)
			{
				$delete_ids_str = implode(",", $delete_ids);

				if (isset($block_config['allow_delete_created_posts']))
				{
					$list_ids_comments = mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=12"));
					$list_ids_comments = implode(",", array_map("intval", $list_ids_comments));

					$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $config[tables_prefix]categories_posts where post_id in ($delete_ids_str)")));
					$list_ids_models = array_map("intval", mr2array_list(sql("select distinct model_id from $config[tables_prefix]models_posts where post_id in ($delete_ids_str)")));
					$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $config[tables_prefix]tags_posts where post_id in ($delete_ids_str)")));

					sql("delete from $config[tables_prefix]posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]tags_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]categories_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]models_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]rating_history where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_posts where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_history where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]flags_messages where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]users_events where post_id in ($delete_ids_str)");
					sql("delete from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=12");

					if (strlen($list_ids_comments) > 0)
					{
						sql("update $config[tables_prefix]users set
							comments_posts_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=12),
							comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_comments)"
						);
					}

					require_once("$config[project_path]/admin/include/functions_admin.php");
					update_tags_posts_totals($list_ids_tags);
					update_categories_posts_totals($list_ids_categories);
					update_models_posts_totals($list_ids_models);

					foreach ($delete_ids as $delete_id)
					{
						$dir_path = get_dir_by_id($delete_id);
						$custom_files = get_contents_from_dir("$config[content_path_posts]/$dir_path/$delete_id", 1);
						foreach ($custom_files as $custom_file)
						{
							@unlink("$config[content_path_posts]/$dir_path/$delete_id/$custom_file");
						}
						@rmdir("$config[content_path_posts]/$dir_path/$delete_id");
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=12, added_date=?", $_SESSION['user_id'], $_SESSION['username'], $delete_id, date("Y-m-d H:i:s"));
					}
				} else
				{
					if ($_REQUEST['mode'] == 'async')
					{
						async_return_request_status(array(array('error_code' => 'delete_forbidden', 'block' => 'list_posts')));
					} else
					{
						header("Location: ?action=delete_forbidden");
						die;
					}
				}
			}
			if ($_REQUEST['mode'] == 'async')
			{
				async_return_request_status();
			} else
			{
				header("Location: ?action=delete_done");
				die;
			}
		} elseif ($_REQUEST['mode'] == 'async')
		{
			async_return_request_status(array(array('error_code' => 'not_logged_in', 'block' => 'list_posts')));
		}
	}

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$post_types=array();
	$post_types_temp=mr2array(sql("select * from $config[tables_prefix]posts_types"));
	foreach ($post_types_temp as $post_type_temp)
	{
		$post_types[$post_type_temp['post_type_id']]=$post_type_temp;
	}

	$where='';
	$where_posts="$database_selectors[where_posts]";
	$sort_by_relevance='';

	if ($block_config['post_type']!='' || (isset($block_config['var_post_type']) && $_REQUEST[$block_config['var_post_type']]!=''))
	{
		$post_type_external_id=$block_config['post_type'];
		if (isset($block_config['var_post_type']) && $_REQUEST[$block_config['var_post_type']]!='')
		{
			$post_type_external_id=$_REQUEST[$block_config['var_post_type']];
		}

		$is_known_post_type=false;
		foreach ($post_types as $post_type)
		{
			if ($post_type['external_id']==$post_type_external_id)
			{
				$is_known_post_type=true;
				$where.=" and post_type_id=".intval($post_type['post_type_id']);

				$storage[$object_id]['post_type_info']=$post_type;
				$smarty->assign('post_type_info',$post_type);
			}
		}
		if (!$is_known_post_type)
		{
			return 'status_404';
		}
	}

	if (isset($block_config['var_post_date_from']) && trim($_REQUEST[$block_config['var_post_date_from']])!='')
	{
		$date_from=explode('-',trim($_REQUEST[$block_config['var_post_date_from']]));
		if (count($date_from)>=3)
		{
			$date_from=date("Y-m-d 00:00:00",mktime(0,0,0,intval($date_from[1]),intval($date_from[2]),intval($date_from[0])));
			$where.=" and $database_selectors[generic_post_date_selector] >= '$date_from'";
		}
	}
	if (isset($block_config['var_post_date_to']) && trim($_REQUEST[$block_config['var_post_date_to']])!='')
	{
		$date_to=explode('-',trim($_REQUEST[$block_config['var_post_date_to']]));
		if (count($date_to)>=3)
		{
			$date_to=date("Y-m-d 23:59:59",mktime(0,0,0,intval($date_to[1]),intval($date_to[2]),intval($date_to[0])));
			$where.=" and $database_selectors[generic_post_date_selector] < '$date_to'";
		}
	}

	$join_tables=array();

	if (isset($block_config['mode_created']))
	{
		$my_mode_created=false;
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$my_mode_created=true;

			$where_posts="$database_selectors[where_posts_active_disabled]";
		} else {
			if ($_GET['mode']=='async')
			{
				header('HTTP/1.0 403 Forbidden');die;
			}

			$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
			if (isset($block_config['redirect_unknown_user_to']))
			{
				$url=process_url($block_config['redirect_unknown_user_to']);
				return "status_302: $url";
			} else
			{
				return "status_302: $config[project_url]";
			}
		}

		$user_info=mr2array_single(sql("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=$user_id"));
		if (count($user_info)>0)
		{
			$where.=" and user_id=$user_id ";

			$smarty->assign("user_id",$user_id);
			$smarty->assign("username",$user_info['username']);
			$smarty->assign("display_name",$user_info['display_name']);
			$smarty->assign("avatar",$user_info['avatar']);
			$smarty->assign("gender_id",$user_info['gender_id']);
			$smarty->assign("city",$user_info['city']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['username']=$user_info['username'];
			$storage[$object_id]['display_name']=$user_info['display_name'];
			$storage[$object_id]['avatar']=$user_info['avatar'];
			$storage[$object_id]['gender_id']=$user_info['gender_id'];
			$storage[$object_id]['city']=$user_info['city'];
			if ($user_info['country_id']>0)
			{
				$smarty->assign("country_id",$user_info['country_id']);
				$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
				$storage[$object_id]['country_id']=$user_info['country_id'];
				$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
			}

			if ($my_mode_created)
			{
				$smarty->assign("can_manage",1);
				$storage[$object_id]['can_manage']=1;
				if (isset($block_config['allow_delete_created_posts']))
				{
					$smarty->assign("can_delete",1);
					$storage[$object_id]['can_delete']=1;
				} else
				{
					$smarty->assign("can_delete",0);
					$storage[$object_id]['can_delete']=0;
				}
			}
		} else {
			return 'status_404';
		}
	} elseif (intval($block_config['mode_related'])>0 || (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0))
	{
		//1 - tags
		//2 - categories
		//3 - models
		//4-5 - title

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$result=null;
		if (isset($block_config['var_post_id']) && intval($_REQUEST[$block_config['var_post_id']])>0)
		{
			$result=sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where post_id=?",intval($_REQUEST[$block_config['var_post_id']]));
		} elseif (trim($_REQUEST[$block_config['var_post_dir']])<>'')
		{
			$result=sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where dir=?",trim($_REQUEST[$block_config['var_post_dir']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$mode_related_name='';
			$data_temp=mr2array_single($result);
			$post_id=$data_temp["post_id"];

			$where.=" and $config[tables_prefix]posts.post_id<>$post_id";
			if ($mode_related==1)
			{
				$mode_related_name='tags';

				$tag_ids=mr2array_list(sql_pr("select tag_id from $config[tables_prefix]tags_posts where post_id=?",$post_id));
				if (count($tag_ids)>0)
				{
					$tag_ids=implode(",",$tag_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)";
				}
			} elseif ($mode_related==2)
			{
				$mode_related_name='categories';

				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_posts where post_id=?",$post_id));
				if (count($category_ids)>0 && isset($block_config['mode_related_category_group_id']))
				{
					$category_ids=implode(',',$category_ids);
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories where category_id in ($category_ids) and (category_group_id=? or category_group_id in (select category_group_id from $config[tables_prefix]categories_groups where external_id=?))",intval($block_config['mode_related_category_group_id']),trim($block_config['mode_related_category_group_id'])));
				}
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids)";
				}
			} elseif ($mode_related==3)
			{
				$mode_related_name='models';

				$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_posts where post_id=?",$post_id));
				if (count($model_ids)>0 && isset($block_config['mode_related_model_group_id']))
				{
					$model_ids=implode(',',$model_ids);
					$model_ids=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models where model_id in ($model_ids) and (model_group_id=? or model_group_id in (select model_group_id from $config[tables_prefix]models_groups where external_id=?))",intval($block_config['mode_related_model_group_id']),trim($block_config['mode_related_model_group_id'])));
				}
				if (count($model_ids)>0)
				{
					$model_ids=implode(",",$model_ids);
					$join_tables[]="select distinct post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)";
				}
			} elseif ($mode_related==4 || $mode_related==5)
			{
				$mode_related_name='title';

				$title=$data_temp["title"];
				$title=sql_escape($title);

				$search_modifier='';
				if ($mode_related==5)
				{
					$search_modifier='WITH QUERY EXPANSION';
				}
				$where.=" and MATCH(title) AGAINST('$title' $search_modifier)";
				$sort_by_relevance="MATCH(title) AGAINST('$title' $search_modifier) desc";
			}
			$storage[$object_id]['list_type']="related";
			$storage[$object_id]['related_mode']=$mode_related;
			$storage[$object_id]['related_mode_name']=$mode_related_name;
			$smarty->assign('list_type',"related");
			$smarty->assign('related_mode',$mode_related);
			$smarty->assign('related_mode_name',$mode_related_name);
		}
	} elseif (isset($block_config['mode_connected_video']))
	{
		$video_id=0;
		if (isset($block_config['var_connected_video_id']) && intval($_REQUEST[$block_config['var_connected_video_id']])>0)
		{
			$video_id=intval($_REQUEST[$block_config['var_connected_video_id']]);
		} elseif (trim($_REQUEST[$block_config['var_connected_video_dir']])<>'')
		{
			$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_connected_video_dir']]),trim($_REQUEST[$block_config['var_connected_video_dir']])));
		}
		$where.=" and connected_video_id=$video_id";
	}

	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$q=sql_escape(trim($_REQUEST[$block_config['var_title_section']]));
		$where.=" and title like '$q%'";

		$storage[$object_id]['list_type']="title_section";
		$storage[$object_id]['title_section']=trim($_REQUEST[$block_config['var_title_section']]);
		$smarty->assign('list_type',"title_section");
		$smarty->assign('title_section',trim($_REQUEST[$block_config['var_title_section']]));
	}

	for ($i=1;$i<=10;$i++)
	{
		if (isset($block_config["var_custom$i"]) && trim($_REQUEST[$block_config["var_custom$i"]])!='')
		{
			$where.=" and custom$i='".sql_escape(trim($_REQUEST[$block_config["var_custom$i"]]))."'";
		}
	}

	for ($i=1;$i<=3;$i++)
	{
		if (isset($block_config["var_custom_flag$i"]) && trim($_REQUEST[$block_config["var_custom_flag$i"]])!='')
		{
			if (strpos(trim($_REQUEST[$block_config["var_custom_flag$i"]]),',')!==false)
			{
				$where.=" and af_custom$i in (".implode(",",array_map("intval",explode(",",trim($_REQUEST[$block_config["var_custom_flag$i"]])))).")";
			} else {
				$where.=" and af_custom$i=".intval($_REQUEST[$block_config["var_custom_flag$i"]]);
			}
		}
	}

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',          'plural'=>'models',            'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true, 'where_single'=>$database_selectors['where_models_active_disabled'],            'where_plural'=>$database_selectors['where_models'],            'base_files_url'=>$config['content_url_models'],               'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',    'plural'=>'models_groups',     'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_models_groups_active_disabled'],     'where_plural'=>$database_selectors['where_models_groups'],     'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title','dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true, 'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title','dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',  'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_posts";
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
								$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct post_id from $df_join_table where $df_id in ($ids_group)";
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
										$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct post_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct post_id from $df_join_table where $df_id in ($df_ids_value)";
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
						$join_tables[]="select distinct post_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct post_id from $df_join_table where $df_id=$df_object_id";
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
			$where.=" and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct post_id from $config[tables_prefix]categories_posts where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct post_id from $config[tables_prefix]tags_posts where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['skip_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$where.=" and $config[tables_prefix]posts.post_id not in (select post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)) ";
		}
	}

	if ($block_config['show_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['show_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$join_tables[]="select distinct post_id from $config[tables_prefix]models_posts where model_id in ($model_ids)";
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
	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and description<>''";
	}

	if (is_array($config['advanced_filtering']))
	{
		foreach ($config['advanced_filtering'] as $advanced_filter)
		{
			if ($advanced_filter=='upload_zone')
			{
				$where.=' and af_upload_zone=0';
			}
		}
	}

	$data=list_postsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="post_date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='title')
	{
		$sort_by_clear="lower(title)";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";

	if ($sort_by_clear=='rating')
	{
		$sort_by="rating/rating_amount desc, rating_amount desc";
	} elseif ($sort_by_clear=='post_viewed')
	{
		$sort_by="post_viewed desc";
	} elseif ($sort_by_clear=='most_commented')
	{
		$sort_by="comments_count $direction";
	} else
	{
		if ($sort_by_clear=='post_date') {$sort_by="$database_selectors[generic_post_date_selector] $direction, $config[tables_prefix]posts.post_id $direction";} else
		if ($sort_by_clear=='post_date_and_popularity') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, post_viewed desc";} else
		if ($sort_by_clear=='post_date_and_rating') {$sort_by="date($database_selectors[generic_post_date_selector]) $direction, rating/rating_amount desc, rating_amount desc";} else
		if ($sort_by_clear=='last_time_view_date_and_popularity') {$sort_by="date(last_time_view_date) $direction, post_viewed desc";} else
		if ($sort_by_clear=='last_time_view_date_and_rating') {$sort_by="date(last_time_view_date) $direction, rating/rating_amount desc, rating_amount desc";}
	}

	$sort_by="order by $sort_by";
	if ($sort_by_relevance<>'' && trim($_REQUEST[$block_config['var_sort_by']])=='')
	{
		$sort_by="order by $sort_by_relevance";
	}

	$from_clause="$config[tables_prefix]posts";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.post_id=$config[tables_prefix]posts.post_id";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $where_posts $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("SELECT $database_selectors[posts] from $from_clause where $where_posts $where $sort_by LIMIT $from, $block_config[items_per_page]"));

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
		$limit='limit 0';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("SELECT $database_selectors[posts] from $from_clause where $where_posts $where $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['post_date']);

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where category_id in (select category_id from $config[tables_prefix]categories_posts where $database_selectors[where_categories] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags where tag_id in (select tag_id from $config[tables_prefix]tags_posts where $database_selectors[where_tags] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_models_info']))
		{
			$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models where model_id in (select model_id from $config[tables_prefix]models_posts where $database_selectors[where_models] and post_id=".$data[$k]['post_id'].")"));
		}
		if (isset($block_config['show_user_info']))
		{
			$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
		}
		if (isset($block_config['show_connected_info']) && $data[$k]['connected_video_id']>0)
		{
			$connected_video_info=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and video_id=".$data[$k]['connected_video_id']));
			if ($connected_video_info['video_id']>0)
			{
				$connected_video_info['time_passed_from_adding']=get_time_passed($connected_video_info['post_date']);
				$connected_video_info['duration_array']=get_duration_splitted($connected_video_info['duration']);
				$connected_video_info['formats']=get_video_formats($connected_video_info['video_id'],$connected_video_info['file_formats']);
				$connected_video_info['dir_path']=get_dir_by_id($connected_video_info['video_id']);

				$screen_url_base=load_balance_screenshots_url();
				$connected_video_info['screen_url']=$screen_url_base.'/'.get_dir_by_id($connected_video_info['video_id']).'/'.$connected_video_info['video_id'];

				$pattern=str_replace("%ID%",$connected_video_info['video_id'],str_replace("%DIR%",$connected_video_info['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$connected_video_info['view_page_url']="$config[project_url]/$pattern";
				$data[$k]['connected_video']=$connected_video_info;
			}
		}

		$dir_path=get_dir_by_id($data[$k]['post_id']);
		$data[$k]['base_files_url']="$config[content_url_posts]/$dir_path/".$data[$k]['post_id'];

		if (isset($post_types[$data[$k]['post_type_id']]))
		{
			$pattern=str_replace("%ID%",$data[$k]['post_id'],str_replace("%DIR%",$data[$k]['dir'],$post_types[$data[$k]['post_type_id']]['url_pattern']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}
	}

	if (count($data)>0)
	{
		$storage[$object_id]['first_object_title']=$data[0]['title'];
		$storage[$object_id]['first_object_description']=$data[0]['description'];
		$update_storage_keys=array(
			'category_info',
			'category_group_info',
			'tag_info',
			'model_info',
			'model_group_info'
		);
		foreach ($update_storage_keys as $update_storage_key)
		{
			if (isset($storage[$object_id][$update_storage_key]))
			{
				$storage[$object_id][$update_storage_key]['first_object_title']=$data[0]['title'];
				$storage[$object_id][$update_storage_key]['first_object_description']=$data[0]['description'];
			}
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_postsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_model_dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$var_model_id=trim($_REQUEST[$block_config['var_model_id']]);
	$var_model_ids=trim($_REQUEST[$block_config['var_model_ids']]);
	$var_model_group_dir=trim($_REQUEST[$block_config['var_model_group_dir']]);
	$var_model_group_id=trim($_REQUEST[$block_config['var_model_group_id']]);
	$var_model_group_ids=trim($_REQUEST[$block_config['var_model_group_ids']]);
	$var_post_date_from=trim($_REQUEST[$block_config['var_post_date_from']]);
	$var_post_date_to=trim($_REQUEST[$block_config['var_post_date_to']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_post_type=trim($_REQUEST[$block_config['var_post_type']]);
	$var_mode_related=trim($_REQUEST[$block_config['var_mode_related']]);
	$var_post_dir=trim($_REQUEST[$block_config['var_post_dir']]);
	$var_post_id=trim($_REQUEST[$block_config['var_post_id']]);
	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_connected_video_dir=trim($_REQUEST[$block_config['var_connected_video_dir']]);
	$var_connected_video_id=trim($_REQUEST[$block_config['var_connected_video_id']]);
	$var_user_id=trim($_REQUEST[$block_config['var_user_id']]);

	$var_custom='';
	for ($i=1;$i<=10;$i++)
	{
		$var_custom.=trim($_REQUEST[$block_config["var_custom$i"]])."|";
	}
	$var_custom_flag1=trim($_REQUEST[$block_config['var_custom_flag1']]);
	$var_custom_flag2=trim($_REQUEST[$block_config['var_custom_flag2']]);
	$var_custom_flag3=trim($_REQUEST[$block_config['var_custom_flag3']]);

	$result="$from|$items_per_page|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_post_date_from|$var_post_date_to|$var_sort_by|$var_custom|$var_custom_flag1|$var_custom_flag2|$var_custom_flag3|$var_post_type|$var_mode_related|$var_post_dir|$var_post_id|$var_title_section|$var_connected_video_dir|$var_connected_video_id|$var_user_id";
	return $result;
}

function list_postsCacheControl($block_config)
{
	if (isset($block_config['mode_created']) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_postsAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_posts') && is_array($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_postsShow($block_config,null);
	}
}

function list_postsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"12"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[post_id,title,dir,post_date,post_date_and_popularity,post_date_and_rating,last_time_view_date,last_time_view_date_and_popularity,last_time_view_date_and_rating,rating,post_viewed,most_commented]", "is_required"=>1, "default_value"=>"post_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"post_type",                  "group"=>"static_filters", "type"=>"STRING",   "is_required"=>0, "default_value"=>""),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"skip_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_from",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>""),
		array("name"=>"days_passed_to",             "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_post_type",          "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_type"),
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
		array("name"=>"var_post_date_from",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_from"),
		array("name"=>"var_post_date_to",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"post_date_to"),
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
		array("name"=>"var_custom_flag1",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag1"),
		array("name"=>"var_custom_flag2",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag2"),
		array("name"=>"var_custom_flag3",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"flag3"),

		// display modes
		array("name"=>"mode_created",               "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"var_user_id",                "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",   "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_created_posts", "group"=>"display_modes", "type"=>"",       "is_required"=>0),

		// related
		array("name"=>"mode_related",                   "group"=>"related", "type"=>"CHOICE[1,2,3,4,5]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_post_dir",                   "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_post_id",                    "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"id"),
		array("name"=>"mode_related_category_group_id", "group"=>"related", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"mode_related_model_group_id",    "group"=>"related", "type"=>"STRING",            "is_required"=>0),
		array("name"=>"var_mode_related",               "group"=>"related", "type"=>"STRING",            "is_required"=>0, "default_value"=>"mode_related"),

		// connected
		array("name"=>"mode_connected_video",    "group"=>"connected_videos", "type"=>"",       "is_required"=>0),
		array("name"=>"var_connected_video_dir", "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_connected_video_id",  "group"=>"connected_videos", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_models_info",     "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_user_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_connected_info",  "group"=>"subselects", "type"=>"", "is_required"=>0),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
