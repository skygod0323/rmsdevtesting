<?php
function list_dvdsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	if ($_REQUEST['action']=='delete_dvds' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql_pr("select dvd_id from $config[tables_prefix]dvds where user_id=? and dvd_id in ($delete_ids_str)",$user_id));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);

				if (intval($block_config['allow_delete_created_dvds'])>0)
				{
					if (intval($block_config['allow_delete_created_dvds'])==1)
					{
						sql_pr("update $config[tables_prefix]videos set dvd_id=0 where dvd_id in ($delete_ids_str)");
					} elseif (intval($block_config['allow_delete_created_dvds'])==2)
					{
						$video_ids=mr2array_list(sql_pr("select video_id from $config[tables_prefix]videos where dvd_id in ($delete_ids_str) and user_id=?",$user_id));
						foreach($video_ids as $video_id)
						{
							sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?",$video_id);
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=1, added_date=?",$_SESSION['user_id'],$_SESSION['username'],$video_id,date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?",$video_id,serialize(array()),date("Y-m-d H:i:s"));
						}
						sql_pr("update $config[tables_prefix]videos set dvd_id=0 where dvd_id in ($delete_ids_str)");
					}

					$list_ids_comments=mr2array_list(sql_pr("select distinct user_id from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=5"));
					$list_ids_comments=implode(",",array_map("intval",$list_ids_comments));

					$list_ids_groups=mr2array_list(sql_pr("select distinct dvd_group_id from $config[tables_prefix]dvds where dvd_id in ($delete_ids_str)"));
					$list_ids_groups=implode(",",array_map("intval",$list_ids_groups));

					$list_ids_categories=array_map("intval",mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_dvds where dvd_id in ($delete_ids_str)")));
					$list_ids_models=array_map("intval",mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_dvds where dvd_id in ($delete_ids_str)")));
					$list_ids_tags=array_map("intval",mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_dvds where dvd_id in ($delete_ids_str)")));

					$data=mr2array(sql_pr("select * from $config[tables_prefix]dvds where dvd_id in ($delete_ids_str)"));
					foreach ($data as $v)
					{
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[cover1_front]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[cover1_front]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[cover1_back]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[cover1_back]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[cover2_front]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[cover2_front]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[cover2_back]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[cover2_back]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file1]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file1]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file2]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file2]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file3]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file3]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file4]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file4]");}
						if (is_file("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file5]")) {@unlink("$config[content_path_dvds]/$v[dvd_id]/$v[custom_file5]");}
						if (is_dir("$config[content_path_dvds]/$v[dvd_id]")) {@rmdir("$config[content_path_dvds]/$v[dvd_id]");}
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=5, added_date=?",$_SESSION['user_id'],$_SESSION['username'],$v['dvd_id'],date("Y-m-d H:i:s"));
					}

					sql_pr("delete from $config[tables_prefix]dvds where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]categories_dvds where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]tags_dvds where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]models_dvds where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]users_events where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=5");
					sql_pr("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($delete_ids_str) and subscribed_type_id=5");
					sql_pr("delete from $config[tables_prefix]flags_dvds where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]flags_history where dvd_id in ($delete_ids_str)");
					sql_pr("delete from $config[tables_prefix]flags_messages where dvd_id in ($delete_ids_str)");

					if (strlen($list_ids_comments)>0)
					{
						sql_pr("update $config[tables_prefix]users set
								comments_dvds_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=5),
								comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
							where user_id in ($list_ids_comments)"
						);
					}
					if (strlen($list_ids_groups)>0)
					{
						sql_pr("update $config[tables_prefix]dvds_groups set total_dvds=(select count(*) from $config[tables_prefix]dvds where $config[tables_prefix]dvds.dvd_group_id=$config[tables_prefix]dvds_groups.dvd_group_id) where dvd_group_id in ($list_ids_groups)");
					}
					if (count($list_ids_categories)>0)
					{
						$list_ids_categories=implode(',',$list_ids_categories);
						sql_pr("update $config[tables_prefix]categories set total_dvds=(select count(*) from $config[tables_prefix]categories_dvds where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
					}
					if (count($list_ids_models)>0)
					{
						$list_ids_models=implode(',',$list_ids_models);
						sql_pr("update $config[tables_prefix]models set total_dvds=(select count(*) from $config[tables_prefix]models_dvds where model_id=$config[tables_prefix]models.model_id) where model_id in ($list_ids_models)");
					}
					if (count($list_ids_tags)>0)
					{
						$list_ids_tags=implode(',',$list_ids_tags);
						sql_pr("update $config[tables_prefix]tags set total_dvds=(select count(*) from $config[tables_prefix]tags_dvds where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_tags)");
					}

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=delete_done");die;
					}
				} else {
					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'delete_forbidden','block'=>'list_dvds')));
					} else {
						header("Location: ?action=delete_forbidden");die;
					}
				}
			} else {
				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=delete_done");die;
				}
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_dvds')));
		}
	}

	$where='';
	$my_mode_created=false;
	if (isset($block_config['mode_created']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
			if (count($user_info)>0)
			{
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
			} else {
				return 'status_404';
			}
			$where=" and user_id=$user_id ";
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
			$where=" and user_id=$_SESSION[user_id] ";
			$my_mode_created=true;
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
	} elseif (isset($block_config['mode_uploadable']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
			if (count($user_info)>0)
			{
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
			} else {
				return 'status_404';
			}
			$where=" and (is_video_upload_allowed=0 or user_id=$user_id or (is_video_upload_allowed=1 and (user_id in (select user_id from $config[tables_prefix]friends where is_approved=1 and friend_id=$user_id) or user_id in (select friend_id from $config[tables_prefix]friends where is_approved=1 and user_id=$user_id)))) ";
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("user_id",$user_id);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$where=" and (is_video_upload_allowed=0 or user_id=$user_id or (is_video_upload_allowed=1 and (user_id in (select user_id from $config[tables_prefix]friends where is_approved=1 and friend_id=$user_id) or user_id in (select friend_id from $config[tables_prefix]friends where is_approved=1 and user_id=$user_id)))) ";
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

	if (isset($block_config['show_only_with_cover1']))
	{
		$where.=" and cover1_front <> '' and cover1_back <> ''";
	}
	if (isset($block_config['show_only_with_cover2']))
	{
		$where.=" and cover2_front <> '' and cover2_back <> ''";
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
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'dvd_group',      'plural'=>'dvds_groups',       'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>false, 'where_single'=>$database_selectors['where_dvds_groups_active_disabled'],       'where_plural'=>$database_selectors['where_dvds_groups'],       'base_files_url'=>$config['content_url_dvds'].'/groups',       'link_pattern'=>'WEBSITE_LINK_PATTERN_DVD_GROUP', 'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'model',          'plural'=>'models',            'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_models_active_disabled'],            'where_plural'=>$database_selectors['where_models'],            'base_files_url'=>$config['content_url_models'],               'link_pattern'=>'WEBSITE_LINK_PATTERN_MODEL',     'sub_categories'=>true, 'sub_tags'=>true);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'model_group',    'plural'=>'models_groups',     'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_models_groups_active_disabled'],     'where_plural'=>$database_selectors['where_models_groups'],     'base_files_url'=>$config['content_url_models'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>true,  'join_table'=>true,  'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title', 'dir'=>'dir',    'supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',   'dir'=>'tag_dir','supports_grouping'=>false, 'join_table'=>true,  'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_dvds";
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
								$join_tables[]="select distinct dvd_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct dvd_id from $df_join_table where $df_id in ($ids_group)";
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
										$join_tables[]="select distinct dvd_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct dvd_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct dvd_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct dvd_id from $df_join_table where $df_id in ($df_ids_value)";
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
						$join_tables[]="select distinct dvd_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct dvd_id from $df_join_table where $df_id=$df_object_id";
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
			$where.=" and $config[tables_prefix]dvds.dvd_id not in (select dvd_id from $config[tables_prefix]categories_dvds where category_id in ($category_ids))";
		}
	}

	if ($block_config['show_categories']<>'' && !in_array('categories',$dynamic_filters_types) && !in_array('multi_categories',$dynamic_filters_types) && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$category_ids=array_map("intval",explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$join_tables[]="select distinct dvd_id from $config[tables_prefix]categories_dvds where category_id in ($category_ids)";
		}
	}

	if ($block_config['skip_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['skip_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$where.=" and $config[tables_prefix]dvds.dvd_id not in (select dvd_id from $config[tables_prefix]tags_dvds where tag_id in ($tag_ids)) ";
		}
	}

	if ($block_config['show_tags']<>'' && !in_array('tags',$dynamic_filters_types) && !in_array('multi_tags',$dynamic_filters_types))
	{
		$tag_ids=array_map("intval",explode(",",$block_config['show_tags']));
		if (count($tag_ids)>0)
		{
			$tag_ids=implode(",",$tag_ids);
			$join_tables[]="select distinct dvd_id from $config[tables_prefix]tags_dvds where tag_id in ($tag_ids)";
		}
	}

	if ($block_config['skip_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['skip_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$where.=" and $config[tables_prefix]dvds.dvd_id not in (select dvd_id from $config[tables_prefix]models_dvds where model_id in ($model_ids)) ";
		}
	}

	if ($block_config['show_models']<>'' && !in_array('models',$dynamic_filters_types) && !in_array('multi_models',$dynamic_filters_types) && !in_array('models_groups',$dynamic_filters_types) && !in_array('multi_models_groups',$dynamic_filters_types))
	{
		$model_ids=array_map("intval",explode(",",$block_config['show_models']));
		if (count($model_ids)>0)
		{
			$model_ids=implode(",",$model_ids);
			$join_tables[]="select distinct dvd_id from $config[tables_prefix]models_dvds where model_id in ($model_ids)";
		}
	}

	if ($block_config['skip_dvd_groups']<>'' && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_group_ids=array_map("intval",explode(",",$block_config['skip_dvd_groups']));
		if (count($dvd_group_ids)>0)
		{
			$dvd_group_ids=implode(",",$dvd_group_ids);
			$where.=" and $config[tables_prefix]dvds.dvd_group_id not in ($dvd_group_ids) ";
		}
	}

	if ($block_config['show_dvd_groups']<>'' && !in_array('dvds_groups',$dynamic_filters_types) && !in_array('multi_dvds_groups',$dynamic_filters_types))
	{
		$dvd_group_ids=array_map("intval",explode(",",$block_config['show_dvd_groups']));
		if (count($dvd_group_ids)>0)
		{
			$dvd_group_ids=implode(",",$dvd_group_ids);
			$where.=" and $config[tables_prefix]dvds.dvd_group_id in ($dvd_group_ids) ";
		}
	}

	$metadata=list_dvdsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="dvd_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='dvd_id')
	{
		$sort_by_clear="$config[tables_prefix]dvds.dvd_id";
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
	}

	$from_clause="$config[tables_prefix]dvds";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.dvd_id=$config[tables_prefix]dvds.dvd_id";
	}

	$dvds_selector="$database_selectors[dvds]";
	if ($my_mode_created)
	{
		$dvds_selector.=", (select count(*) from $config[tables_prefix]videos where $database_selectors[where_videos_internal] and $config[tables_prefix]videos.dvd_id=$config[tables_prefix]dvds.dvd_id) as total_videos";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause where $database_selectors[where_dvds] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $dvds_selector from $from_clause where $database_selectors[where_dvds] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $dvds_selector from $from_clause where $database_selectors[where_dvds] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd_id'];
		if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
		{
			$pattern=str_replace("%ID%",$data[$k]['dvd_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}
		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_dvds on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_dvds.category_id where $database_selectors[where_categories] and dvd_id=".$data[$k]['dvd_id']." order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_dvds on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_dvds.tag_id where $database_selectors[where_tags] and dvd_id=".$data[$k]['dvd_id']." order by id asc"));
		}
		if (isset($block_config['show_models_info']))
		{
			$data[$k]['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_dvds on $config[tables_prefix]models.model_id=$config[tables_prefix]models_dvds.model_id where $database_selectors[where_models] and dvd_id=".$data[$k]['dvd_id']." order by id asc"));
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
		if (isset($block_config['show_user_info']) && $data[$k]['user_id']>0)
		{
			$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
		}
		if (isset($block_config['show_group_info']) && $data[$k]['dvd_group_id']>0)
		{
			$data[$k]['group']=mr2array_single(sql_pr("select $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups] and dvd_group_id=".$data[$k]['dvd_group_id']));
			if ($data[$k]['group']['dvd_group_id']>0)
			{
				$data[$k]['group']['base_files_url']=$config['content_url_dvds'].'/groups/'.$data[$k]['group']['dvd_group_id'];
				if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
				{
					$pattern=str_replace("%ID%",$data[$k]['group']['dvd_group_id'],str_replace("%DIR%",$data[$k]['group']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
					$data[$k]['group']['view_page_url']="$config[project_url]/$pattern";
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
			if (trim($block_config['search_redirect_pattern'])<>'' || $website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
			{
				if (trim($block_config['search_redirect_pattern'])<>'')
				{
					$pattern=str_replace("%ID%",$data[0]['dvd_id'],str_replace("%DIR%",$data[0]['dir'],trim($block_config['search_redirect_pattern'])));
				} else {
					$pattern=str_replace("%ID%",$data[0]['dvd_id'],str_replace("%DIR%",$data[0]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
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
		if ($videos_sort_by_clear=='title')
		{
			$videos_sort_by_clear="lower($database_selectors[generic_selector_title])";
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
			$videos=mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos where $database_selectors[where_videos] and dvd_id=$ve[dvd_id] order by $videos_sort_by limit $limit"));
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

	$smarty->assign("data",$data);

	return '';
}

function list_dvdsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

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
	$var_dvd_group_dir=trim($_REQUEST[$block_config['var_dvd_group_dir']]);
	$var_dvd_group_id=trim($_REQUEST[$block_config['var_dvd_group_id']]);
	$var_dvd_group_ids=trim($_REQUEST[$block_config['var_dvd_group_ids']]);
	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	$var_user_id=trim($_REQUEST[$block_config['var_user_id']]);

	if ((isset($block_config['mode_created']) || isset($block_config['mode_uploadable'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		if (strpos($_REQUEST[$block_config['var_search']],' ')!==false)
		{
			return "runtime_nocache";
		}
	}
	return "$from|$items_per_page|$var_category_dir|$var_category_id|$var_category_ids|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_model_dir|$var_model_id|$var_model_ids|$var_model_group_dir|$var_model_group_id|$var_model_group_ids|$var_dvd_group_dir|$var_dvd_group_id|$var_dvd_group_ids|$var_title_section|$var_search|$var_user_id|$var_sort_by";
}

function list_dvdsCacheControl($block_config)
{
	if ((isset($block_config['mode_created']) || isset($block_config['mode_uploadable'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_dvdsAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_dvds') && isset($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_dvdsShow($block_config,null);
	}
}

function list_dvdsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[dvd_id,sort_id,title,rating,dvd_viewed,today_videos,total_videos,total_videos_duration,avg_videos_rating,avg_videos_popularity,comments_count,subscribers_count,last_content_date,added_date]", "is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_cover1",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_cover2",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_videos",      "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"skip_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_tags",                  "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_models",                "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_dvd_groups",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_dvd_groups",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),

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
		array("name"=>"var_dvd_group_dir",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group"),
		array("name"=>"var_dvd_group_id",       "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_id"),
		array("name"=>"var_dvd_group_ids",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_ids"),

		// search
		array("name"=>"var_search",              "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method",           "group"=>"search", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"search_scope",            "group"=>"search", "type"=>"CHOICE[0,1]", "is_required"=>0, "default_value"=>"0"),
		array("name"=>"search_redirect_enabled", "group"=>"search", "type"=>"",            "is_required"=>0),
		array("name"=>"search_redirect_pattern", "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>""),

		// display modes
		array("name"=>"mode_created",              "group"=>"display_modes", "type"=>"",              "is_required"=>0),
		array("name"=>"mode_uploadable",           "group"=>"display_modes", "type"=>"",              "is_required"=>0),
		array("name"=>"var_user_id",               "group"=>"display_modes", "type"=>"STRING",        "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to",  "group"=>"display_modes", "type"=>"STRING",        "is_required"=>0, "default_value"=>"/?login"),
		array("name"=>"allow_delete_created_dvds", "group"=>"display_modes", "type"=>"CHOICE[0,1,2]", "is_required"=>0),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_models_info",     "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_group_info",      "group"=>"subselects", "type"=>"", "is_required"=>0),
		array("name"=>"show_user_info",       "group"=>"subselects", "type"=>"", "is_required"=>0),

		// pull videos
		array("name"=>"pull_videos",         "group"=>"pull_videos", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_videos_count",   "group"=>"pull_videos", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_videos_sort_by", "group"=>"pull_videos", "type"=>"SORTING[video_id,dvd_sort_id,title,duration,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,ctr]", "is_required"=>0, "default_value"=>"post_date desc"),
	);
}

function list_dvdsLegalRequestVariables()
{
	return array('action');
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>