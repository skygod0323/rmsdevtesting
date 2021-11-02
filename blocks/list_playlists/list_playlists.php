<?php
function list_playlistsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	if ($_REQUEST['action']=='delete_playlists' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids_str=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql("select playlist_id from $config[tables_prefix]playlists where user_id=$user_id and playlist_id in ($delete_ids_str) and is_locked=0"));
			if (count($delete_ids)>0)
			{
				$delete_ids_str=implode(",",$delete_ids);
				$delete_video_ids=implode(",",array_map("intval",mr2array_list(sql("select video_id from $config[tables_prefix]fav_videos where user_id=$user_id and playlist_id in ($delete_ids_str)"))));

				$list_ids_comments=mr2array_list(sql("select distinct user_id from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=13"));
				$list_ids_comments=implode(",",array_map("intval",$list_ids_comments));

				$list_ids_categories = array_map("intval", mr2array_list(sql("select distinct category_id from $config[tables_prefix]categories_playlists where playlist_id in ($delete_ids_str)")));
				$list_ids_tags = array_map("intval", mr2array_list(sql("select distinct tag_id from $config[tables_prefix]tags_playlists where playlist_id in ($delete_ids_str)")));

				sql("delete from $config[tables_prefix]playlists where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]fav_videos where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]categories_playlists where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]tags_playlists where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]flags_playlists where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]flags_history where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]flags_messages where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]users_events where playlist_id in ($delete_ids_str)");
				sql("delete from $config[tables_prefix]comments where object_id in ($delete_ids_str) and object_type_id=13");
				sql("delete from $config[tables_prefix]users_subscriptions where subscribed_object_id in ($delete_ids_str) and subscribed_type_id=13");

				if (strlen($list_ids_comments)>0)
				{
					sql("update $config[tables_prefix]users set
							comments_playlists_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
							comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
						where user_id in ($list_ids_comments)"
					);
				}

				if (count($list_ids_categories) > 0)
				{
					$list_ids_categories = implode(',', $list_ids_categories);
					sql_pr("update $config[tables_prefix]categories set total_playlists=(select count(*) from $config[tables_prefix]categories_playlists where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
				}
				if (count($list_ids_tags) > 0)
				{
					$list_ids_tags = implode(',', $list_ids_tags);
					sql_pr("update $config[tables_prefix]tags set total_playlists=(select count(*) from $config[tables_prefix]tags_playlists where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_tags)");
				}

				foreach ($delete_ids as $item_id)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=190, object_id=?, object_type_id=13, added_date=?",$_SESSION['user_id'],$_SESSION['username'],$item_id,date("Y-m-d H:i:s"));
				}

				if ($delete_video_ids!='')
				{
					fav_videos_changed($delete_video_ids,10);
				}

				$_SESSION['playlists']=mr2array(sql("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=$user_id order by title asc"));
				$_SESSION['playlists_amount']=count($_SESSION['playlists']);

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=delete_done");die;
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
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_playlists')));
		}
	}

	$where="";
	if ((isset($block_config['var_is_private']) && $_REQUEST[$block_config['var_is_private']]<>'') || $block_config['is_private']<>'')
	{
		if (trim($_REQUEST[$block_config['var_is_private']])<>'')
		{
			$temp=intval($_REQUEST[$block_config['var_is_private']]);
		} else {
			$temp=intval($block_config['is_private']);
		}
		$where.=" and $config[tables_prefix]playlists.is_private in ($temp)";
		$storage[$object_id]['is_private']="$temp";
		$smarty->assign("is_private","$temp");
	}

	if (isset($block_config['mode_related_video']))
	{
		$video_id = 0;
		if (isset($block_config['var_related_video_id']) && intval($_REQUEST[$block_config['var_related_video_id']])>0)
		{
			$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where video_id=?",intval($_REQUEST[$block_config['var_related_video_id']])));
		} elseif (trim($_REQUEST[$block_config['var_related_video_dir']])<>'')
		{
			$video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_related_video_dir']]),trim($_REQUEST[$block_config['var_related_video_dir']])));
		}

		$where.="and $database_selectors[where_playlists] and $config[tables_prefix]playlists.playlist_id in (select playlist_id from $config[tables_prefix]fav_videos where video_id=$video_id)";
	} elseif (isset($block_config['mode_global']))
	{
		$where.="and $database_selectors[where_playlists]";
	} elseif (isset($block_config['var_user_id']))
	{
		$user_id=intval($_REQUEST[$block_config['var_user_id']]);
		$user_info=mr2array_single(sql("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=$user_id"));
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
		$where.=" and $database_selectors[where_playlists] and $config[tables_prefix]playlists.user_id=$user_id";
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
		$where.=" and $config[tables_prefix]playlists.user_id=$user_id";

		$created_summary=mr2array(sql_pr("select is_private, count(*) as amount from $config[tables_prefix]playlists where $config[tables_prefix]playlists.user_id=$user_id group by is_private order by is_private desc"));
		$temp_summary=array();
		$temp_total=0;
		foreach ($created_summary as $summary_item)
		{
			$temp_summary[$summary_item['is_private']]=$summary_item;
			$temp_total+=$summary_item["amount"];
		}
		$smarty->assign("created_summary",$temp_summary);
		$smarty->assign("created_summary_total",$temp_total);
		$storage[$object_id]["created_summary"]=$temp_summary;
		$storage[$object_id]["created_summary_total"]=$temp_total;
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

	if (isset($block_config['show_only_with_description']))
	{
		$where.=" and $config[tables_prefix]playlists.description<>''";
	}

	if (isset($block_config['show_only_with_videos']))
	{
		$amount_limit=intval($block_config['show_only_with_videos']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and $config[tables_prefix]playlists.total_videos>=$amount_limit";
	}

	if (isset($block_config['var_title_section']) && trim($_REQUEST[$block_config['var_title_section']])<>'')
	{
		$q=sql_escape(trim($_REQUEST[$block_config['var_title_section']]));
		$where.=" and $config[tables_prefix]playlists.title like '$q%'";

		$storage[$object_id]['list_type']="title_section";
		$storage[$object_id]['title_section']=trim($_REQUEST[$block_config['var_title_section']]);
		$smarty->assign('list_type',"title_section");
		$smarty->assign('title_section',trim($_REQUEST[$block_config['var_title_section']]));
	}

	$join_tables=array();

	$dynamic_filters=array();
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'category',       'plural'=>'categories',        'title'=>'title','dir'=>'dir',     'supports_grouping'=>true,  'join_table'=>true, 'where_single'=>$database_selectors['where_categories_active_disabled'],        'where_plural'=>$database_selectors['where_categories'],        'base_files_url'=>$config['content_url_categories']);
	$dynamic_filters[]=array('is_group'=>true,  'single'=>'category_group', 'plural'=>'categories_groups', 'title'=>'title','dir'=>'dir',     'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_categories_groups_active_disabled'], 'where_plural'=>$database_selectors['where_categories_groups'], 'base_files_url'=>$config['content_url_categories'].'/groups');
	$dynamic_filters[]=array('is_group'=>false, 'single'=>'tag',            'plural'=>'tags',              'title'=>'tag',  'dir'=>'tag_dir', 'supports_grouping'=>false, 'join_table'=>true, 'where_single'=>$database_selectors['where_tags_active_disabled'],              'where_plural'=>$database_selectors['where_tags']);

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
			$df_join_table="$config[tables_prefix]{$df['plural']}_playlists";
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
								$join_tables[]="select distinct playlist_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($ids_group))";
							} else {
								$join_tables[]="select distinct playlist_id from $df_join_table where $df_id in ($ids_group)";
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
										$join_tables[]="select distinct playlist_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_ids_value_id)";
									} else {
										$join_tables[]="select distinct playlist_id from $df_join_table where $df_id=$df_ids_value_id";
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
								$join_tables[]="select distinct playlist_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id in ($df_ids_value))";
							} else {
								$join_tables[]="select distinct playlist_id from $df_join_table where $df_id in ($df_ids_value)";
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
					$data_temp['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_{$df['plural']} on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_{$df['plural']}.category_id where $database_selectors[where_categories] and $df_id=?",$data_temp[$df_id]));
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
						$join_tables[]="select distinct playlist_id from $df_join_basetable where $df_basetable_id in (select $df_basetable_id from $df_basetable where $df_id=$df_object_id)";
					} else {
						$join_tables[]="select distinct playlist_id from $df_join_table where $df_id=$df_object_id";
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

	$where="where 1=1 $where";

	$data=list_playlistsMetaData();
	foreach ($data as $res)
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
	if ($sort_by_clear=='') {$sort_by_clear="playlist_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='title')
	{
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$config[tables_prefix]playlists.$sort_by_clear $direction";
	if ($sort_by_clear=='rating')
	{
		$sort_by="$config[tables_prefix]playlists.rating/$config[tables_prefix]playlists.rating_amount desc, $config[tables_prefix]playlists.rating_amount desc";
	} elseif ($sort_by_clear=='playlist_viewed')
	{
		$sort_by="playlist_viewed desc";
	} elseif ($sort_by_clear=='most_commented')
	{
		$sort_by="$config[tables_prefix]playlists.comments_count $direction";
	}
	if ($sort_by_clear=='rand()')
	{
		$sort_by="rand() desc";
	}

	$from_clause="$config[tables_prefix]playlists inner join $config[tables_prefix]users on $config[tables_prefix]playlists.user_id=$config[tables_prefix]users.user_id";
	for ($i=1;$i<=count($join_tables);$i++)
	{
		$join_table=$join_tables[$i-1];
		$from_clause.=" inner join ($join_table) table$i on table$i.playlist_id=$config[tables_prefix]playlists.playlist_id";
	}

	$playlists_selector="$database_selectors[playlists], $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city";

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $from_clause $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $playlists_selector from $from_clause $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $playlists_selector from $from_clause $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']<>'')
		{
			$pattern=str_replace("%ID%",$data[$k]['playlist_id'],str_replace("%DIR%",$data[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
			$data[$k]['view_page_url']="$config[project_url]/$pattern";
		}
		$data[$k]['country']=$list_countries['name'][$v['country_id']];
		if ($data[$k]['avatar']!='')
		{
			$data[$k]['avatar_url']="$config[content_url_avatars]/".$data[$k]['avatar'];
		}

		if (isset($block_config['show_categories_info']))
		{
			$data[$k]['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_playlists on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_playlists.category_id where $database_selectors[where_categories] and playlist_id=".$data[$k]['playlist_id']." order by id asc"));
		}
		if (isset($block_config['show_tags_info']))
		{
			$data[$k]['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_playlists on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_playlists.tag_id where $database_selectors[where_tags] and playlist_id=".$data[$k]['playlist_id']." order by id asc"));
		}
		if (isset($block_config['show_user_info']))
		{
			$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['user_id']));
		}
		if (isset($block_config['show_flags_info']))
		{
			$flags=mr2array(sql_pr("select external_id, (select coalesce(sum(votes),0) from $config[tables_prefix]flags_playlists where $config[tables_prefix]flags_playlists.flag_id=$config[tables_prefix]flags.flag_id and $config[tables_prefix]flags_playlists.playlist_id=?) as votes from $config[tables_prefix]flags where group_id=5",$data[$k]['playlist_id']));
			$data[$k]['flags']=array();
			foreach($flags as $flag)
			{
				$data[$k]['flags'][$flag['external_id']]=$flag['votes'];
			}
		}
		if (isset($block_config['show_comments']))
		{
			$show_comments_limit='';
			if (intval($block_config['show_comments_count'])>0)
			{
				$show_comments_limit='limit '.intval($block_config['show_comments_count']);
			}
			$data[$k]['comments']=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=1 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date <= ? order by $config[tables_prefix]comments.added_date desc $show_comments_limit",$data[$k]['playlist_id'],date("Y-m-d H:i:s")));
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
		if ($videos_sort_by_clear=='rating') {
			$videos_sort_by="rating/rating_amount desc, rating_amount desc";
		} elseif ($videos_sort_by_clear=='video_viewed') {
			$videos_sort_by="video_viewed desc";
		} elseif ($videos_sort_by_clear=='most_favourited') {
			$videos_sort_by="favourites_count $videos_direction";
		} elseif ($videos_sort_by_clear=='most_commented') {
			$videos_sort_by="comments_count $videos_direction";
		} elseif ($videos_sort_by_clear=='added2fav_date') {
			$videos_sort_by="$config[tables_prefix]fav_videos.playlist_sort_id asc, $config[tables_prefix]fav_videos.added_date $videos_direction";
		}

		$limit=intval($block_config['pull_videos_count']);
		if ($limit==0)
		{
			$limit=3;
		}
		$videos_selector=str_replace("user_id","$config[tables_prefix]videos.user_id",str_replace("added_date","$config[tables_prefix]videos.added_date",$database_selectors['videos']));
		foreach ($data as $ke=>$ve)
		{
			$videos=mr2array(sql("select $videos_selector, $config[tables_prefix]fav_videos.added_date as added2fav_date from $config[tables_prefix]videos inner join $config[tables_prefix]fav_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]fav_videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$ve[user_id] and $config[tables_prefix]fav_videos.fav_type=10 and $config[tables_prefix]fav_videos.playlist_id=$ve[playlist_id] order by $videos_sort_by limit $limit"));
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

function list_playlistsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_title_section=trim($_REQUEST[$block_config['var_title_section']]);
	$var_category_group_dir=trim($_REQUEST[$block_config['var_category_group_dir']]);
	$var_category_group_id=trim($_REQUEST[$block_config['var_category_group_id']]);
	$var_category_group_ids=trim($_REQUEST[$block_config['var_category_group_ids']]);
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=trim($_REQUEST[$block_config['var_category_id']]);
	$var_category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
	$var_tag_dir=trim($_REQUEST[$block_config['var_tag_dir']]);
	$var_tag_id=trim($_REQUEST[$block_config['var_tag_id']]);
	$var_tag_ids=trim($_REQUEST[$block_config['var_tag_ids']]);
	$var_is_private=trim($_REQUEST[$block_config['var_is_private']]);
	$var_related_video_dir=trim($_REQUEST[$block_config['var_related_video_dir']]);
	$var_related_video_id=trim($_REQUEST[$block_config['var_related_video_id']]);
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	if (isset($block_config['mode_global']) || isset($block_config['mode_related_video']))
	{
		return "$from|$items_per_page|$var_title_section|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_category_dir|$var_category_id|$var_category_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_is_private|$var_related_video_dir|$var_related_video_id|$var_user_id|$var_sort_by";
	} elseif (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "$from|$items_per_page|$var_title_section|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_category_dir|$var_category_id|$var_category_ids|$var_tag_dir|$var_tag_id|$var_tag_ids|$var_is_private|$var_related_video_dir|$var_related_video_id|$var_user_id|$var_sort_by";
}

function list_playlistsCacheControl($block_config)
{
	if (isset($block_config['mode_global']) || isset($block_config['mode_related_video']))
	{
		return "default";
	}
	if (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_playlistsAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_playlists') && isset($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_playlistsShow($block_config,null);
	}
}

function list_playlistsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[playlist_id,title,total_videos,rating,playlist_viewed,most_commented,subscribers_count,last_content_date,added_date]","is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"is_private",                 "group"=>"static_filters", "type"=>"CHOICE[0,1]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_description", "group"=>"static_filters", "type"=>"",            "is_required"=>0),
		array("name"=>"show_only_with_videos",      "group"=>"static_filters", "type"=>"INT",         "is_required"=>0, "default_value"=>"1"),

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
		array("name"=>"var_is_private",         "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"is_private"),

		// display modes
		array("name"=>"mode_global",              "group"=>"display_modes", "type"=>"",       "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),

		// related
		array("name"=>"mode_related_video",    "group"=>"related", "type"=>"",       "is_required"=>0),
		array("name"=>"var_related_video_id",  "group"=>"related", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),
		array("name"=>"var_related_video_dir", "group"=>"related", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),

		// subselects
		array("name"=>"show_categories_info", "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_tags_info",       "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_user_info",       "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_flags_info",      "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments",        "group"=>"subselects", "type"=>"",    "is_required"=>0),
		array("name"=>"show_comments_count",  "group"=>"subselects", "type"=>"INT", "is_required"=>0, "default_value"=>"2"),

		// pull videos
		array("name"=>"pull_videos",         "group"=>"pull_videos", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_videos_count",   "group"=>"pull_videos", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_videos_sort_by", "group"=>"pull_videos", "type"=>"SORTING[rating,video_viewed,most_favourited,most_commented,added2fav_date]","is_required"=>0, "default_value"=>"added2fav_date desc"),
	);
}

function list_playlistsLegalRequestVariables()
{
	return array('action');
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>