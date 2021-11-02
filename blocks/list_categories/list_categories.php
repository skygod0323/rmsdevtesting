<?php
function list_categoriesShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

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

	if (isset($block_config['show_only_with_videos']))
	{
		$amount_limit=intval($block_config['show_only_with_videos']);
		if ($amount_limit==0)
		{
			$amount_limit=1;
		}
		$where.=" and total_videos>=$amount_limit";
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

	$dynamic_filters_types=array();
	if (isset($block_config['var_category_group_ids']) && $_REQUEST[$block_config['var_category_group_ids']]<>'')
	{
		$group_ids=$_REQUEST[$block_config['var_category_group_ids']];
		$group_ids=explode(",",trim($group_ids,"() "));
		$group_ids=array_map("intval",$group_ids);
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and category_group_id in ($group_ids)";

			$data_temp=mr2array(sql_pr("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups] and category_group_id in ($group_ids)"));
			$storage[$object_id]['list_type']="multi_groups";
			$storage[$object_id]['groups_info']=$data_temp;
			$smarty->assign('list_type',"multi_groups");
			$smarty->assign('groups_info',$data_temp);
			$dynamic_filters_types[]="multi_categories_groups";
		}
	} elseif ((isset($block_config['var_category_group_dir']) && $_REQUEST[$block_config['var_category_group_dir']]<>'') || (isset($block_config['var_category_group_id']) && $_REQUEST[$block_config['var_category_group_id']]<>''))
	{
		$result=null;
		if ($_REQUEST[$block_config['var_category_group_dir']]<>'')
		{
			$result=sql_pr("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_category_group_dir']]),trim($_REQUEST[$block_config['var_category_group_dir']]));
		} else
		{
			$result=sql_pr("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups where $database_selectors[where_categories_groups_active_disabled] and category_group_id=?",intval($_REQUEST[$block_config['var_category_group_id']]));
		}

		if (isset($result) && mr2rows($result)>0)
		{
			$data_temp=mr2array_single($result);
			$group_id=intval($data_temp["category_group_id"]);

			$where.=" and category_group_id=$group_id ";

			$storage[$object_id]['list_type']="groups";
			$storage[$object_id]['group']=$data_temp['title'];
			$storage[$object_id]['group_info']=$data_temp;
			$smarty->assign('list_type',"groups");
			$smarty->assign('group',$data_temp['title']);
			$smarty->assign('group_info',$data_temp);
			$dynamic_filters_types[]="categories_groups";
		} else
		{
			return 'status_404';
		}
	}

	if (intval($block_config['mode_related'])>0 || (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0))
	{
		//1 - group
		//2 - videos
		//3 - albums

		$mode_related=intval($block_config['mode_related']);
		if (isset($block_config['var_mode_related']) && intval($_REQUEST[$block_config['var_mode_related']])>0)
		{
			$mode_related=intval($_REQUEST[$block_config['var_mode_related']]);
		}

		$mode_related_name='';
		if ($mode_related==1)
		{
			$mode_related_name='group';
		} elseif ($mode_related==2)
		{
			$mode_related_name='videos';
		} elseif ($mode_related==3)
		{
			$mode_related_name='albums';
		}

		if (isset($block_config['var_category_ids']) && trim($_REQUEST[$block_config['var_category_ids']])!='')
		{
			$category_ids=trim($_REQUEST[$block_config['var_category_ids']]);
			if (strpos($category_ids,"|")!==false)
			{
				$ids_groups=explode("|",$category_ids);
				$category_ids=array(0);
				foreach ($ids_groups as $ids_group)
				{
					$ids_group=array_map("intval",explode(",",trim($ids_group,"() ")));
					if (count($ids_group)>0)
					{
						$category_ids=array_merge($category_ids,$ids_group);

						$where_temp='';
						foreach ($ids_group as $id_group)
						{
							$id_group=intval($id_group);
							if ($mode_related==1)
							{
								$where_temp.=" or category_group_id=(select category_group_id from $config[tables_prefix]categories where category_id=$id_group)";
							} elseif ($mode_related==2)
							{
								$where_temp.=" or category_id in (select cv2.category_id from $config[tables_prefix]categories_videos cv1 inner join $config[tables_prefix]categories_videos cv2 on cv1.category_id=$id_group and cv2.category_id!=$id_group and cv1.video_id=cv2.video_id)";
							} elseif ($mode_related==3)
							{
								$where_temp.=" or category_id in (select ca2.category_id from $config[tables_prefix]categories_albums ca1 inner join $config[tables_prefix]categories_albums ca2 on ca1.category_id=$id_group and ca2.category_id!=$id_group and ca1.album_id=ca2.album_id)";
							}
						}
						if ($where_temp!='')
						{
							$where_temp=trim(substr($where_temp,4));
							$where.=" and ($where_temp)";
						}
					}
				}
			} else
			{
				$all_met=false;
				$category_ids=explode(",",trim($category_ids,"() "));
				if (in_array('all',$category_ids))
				{
					$all_met=true;
				}
				$category_ids=array_map("intval",$category_ids);
				if (count($category_ids)>0)
				{
					$where_temp='';
					foreach ($category_ids as $id_group)
					{
						$id_group=intval($id_group);
						if ($id_group>0)
						{
							if ($mode_related==1)
							{
								if ($all_met)
								{
									$where_temp.=" and ";
								} else
								{
									$where_temp.=" or ";
								}
								$where_temp.="category_group_id=(select category_group_id from $config[tables_prefix]categories where category_id=$id_group)";
							} elseif ($mode_related==2)
							{
								if ($all_met)
								{
									$where_temp.=" and ";
								} else
								{
									$where_temp.=" or ";
								}
								$where_temp.="category_id in (select cv2.category_id from $config[tables_prefix]categories_videos cv1 inner join $config[tables_prefix]categories_videos cv2 on cv1.category_id=$id_group and cv2.category_id!=$id_group and cv1.video_id=cv2.video_id)";
							} elseif ($mode_related==3)
							{
								if ($all_met)
								{
									$where_temp.=" and ";
								} else
								{
									$where_temp.=" or ";
								}
								$where_temp.="category_id in (select ca2.category_id from $config[tables_prefix]categories_albums ca1 inner join $config[tables_prefix]categories_albums ca2 on ca1.category_id=$id_group and ca2.category_id!=$id_group and ca1.album_id=ca2.album_id)";
							}
						}
					}
					if ($where_temp!='')
					{
						$where_temp=trim(substr($where_temp,4));
						$where.=" and ($where_temp)";
					}
				}
			}

			$data_temp=null;

			$category_ids=implode(',',$category_ids);
			if ($category_ids!='')
			{
				$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where category_id in ($category_ids)");
				$data_temp=mr2array($result);
				$where.=" and category_id not in ($category_ids)";
			}

			$storage[$object_id]['list_type']="related";
			$storage[$object_id]['related_mode']=$mode_related;
			$storage[$object_id]['related_mode_name']=$mode_related_name;
			$storage[$object_id]['categories_info']=$data_temp;

			$smarty->assign('list_type',"related");
			$smarty->assign('related_mode',$mode_related);
			$smarty->assign('related_mode_name',$mode_related_name);
			$smarty->assign('categories_info',$data_temp);

		} else
		{
			$result=null;
			if (isset($block_config['var_category_id']) && intval($_REQUEST[$block_config['var_category_id']])>0)
			{
				$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where category_id=?",intval($_REQUEST[$block_config['var_category_id']]));
			} elseif (trim($_REQUEST[$block_config['var_category_dir']])!='')
			{
				$result=sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_category_dir']]),trim($_REQUEST[$block_config['var_category_dir']]));
			}

			if (isset($result) && mr2rows($result)>0)
			{
				$data_temp=mr2array_single($result);
				$category_id=$data_temp["category_id"];

				$where.=" and $config[tables_prefix]categories.category_id<>$category_id";
				if ($mode_related==1)
				{
					$group_id=intval($data_temp['category_group_id']);

					$dynamic_filters_types[]="categories_groups";
					$where.=" and category_group_id=$group_id ";
				} elseif ($mode_related==2)
				{
					$where.=" and category_id in (select cv2.category_id from $config[tables_prefix]categories_videos cv1 inner join $config[tables_prefix]categories_videos cv2 on cv1.category_id=$category_id and cv2.category_id!=$category_id and cv1.video_id=cv2.video_id)";
				} elseif ($mode_related==3)
				{
					$where.=" and category_id in (select ca2.category_id from $config[tables_prefix]categories_albums ca1 inner join $config[tables_prefix]categories_albums ca2 on ca1.category_id=$category_id and ca2.category_id!=$category_id and ca1.album_id=ca2.album_id)";
				}

				$storage[$object_id]['list_type']="related";
				$storage[$object_id]['related_mode']=$mode_related;
				$storage[$object_id]['related_mode_name']=$mode_related_name;
				$storage[$object_id]['category']=$data_temp['title'];
				$storage[$object_id]['category_info']=$data_temp;

				$smarty->assign('list_type',"related");
				$smarty->assign('related_mode',$mode_related);
				$smarty->assign('related_mode_name',$mode_related_name);
				$smarty->assign('category',$data_temp['title']);
				$smarty->assign('category_info',$data_temp);
			}
		}
	}

	if ($block_config['category_group_ids']<>'' && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['category_group_ids']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and category_group_id in ($group_ids)";
		}
	}
	if ($block_config['skip_category_groups']<>'' && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['skip_category_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and category_group_id not in ($group_ids)";
		}
	}
	if ($block_config['show_category_groups']<>'' && !in_array('categories_groups',$dynamic_filters_types) && !in_array('multi_categories_groups',$dynamic_filters_types))
	{
		$group_ids=array_map("intval",explode(",",$block_config['show_category_groups']));
		if (count($group_ids)>0)
		{
			$group_ids=implode(',',$group_ids);
			$where.=" and category_group_id in ($group_ids)";
		}
	}

	$metadata=list_categoriesMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="category_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='title')
	{
		$sort_by_clear=$sort_by_clear="lower($database_selectors[generic_selector_title])";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}
	$sort_by="$sort_by_clear $direction";
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]categories where $database_selectors[where_categories] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories] $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $database_selectors[categories] from $config[tables_prefix]categories where $database_selectors[where_categories] $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['base_files_url']=load_balance_categories_url().'/'.$data[$k]['category_id'];
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

		$rotator_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/rotator.dat"));
		foreach ($data as $ke=>$ve)
		{
			$not_clause="$config[tables_prefix]videos.video_id not in (".implode(',',$selected_video_ids).")";

			if ($videos_sort_by_clear=='ctr' && intval($rotator_params['ROTATOR_VIDEOS_CATEGORIES_ENABLE'])==1)
			{
				$videos_sort_by="(select cr_ctr from $config[tables_prefix]categories_videos where video_id=$config[tables_prefix]videos.video_id and category_id=$ve[category_id]) desc";
			}

			$videos=mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos inner join $config[tables_prefix]categories_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]categories_videos.video_id where $not_clause and $database_selectors[where_videos] and $config[tables_prefix]categories_videos.category_id=$ve[category_id] order by $videos_sort_by limit $limit"));
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
			$albums=mr2array(sql("select $database_selectors[albums] from $config[tables_prefix]albums inner join $config[tables_prefix]categories_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]categories_albums.album_id where $not_clause and $database_selectors[where_albums] and $config[tables_prefix]categories_albums.category_id=$ve[category_id] order by $albums_sort_by limit $limit"));
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

	$smarty->assign("data",$data);

	return '';
}

function list_categoriesGetHash($block_config)
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
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	return "$from|$items_per_page|$var_title_section|$var_category_group_dir|$var_category_group_id|$var_category_group_ids|$var_category_dir|$var_category_id|$var_category_ids|$var_sort_by";
}

function list_categoriesCacheControl($block_config)
{
	return "default";
}

function list_categoriesMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[category_id,sort_id,is_avatar_available,title,dir,today_videos,total_videos,today_albums,total_albums,today_posts,total_posts,avg_videos_rating,avg_videos_popularity,max_videos_ctr,avg_albums_rating,avg_albums_popularity,avg_posts_rating,avg_posts_popularity]", "is_required"=>1, "default_value"=>"title asc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_avatar",           "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_without_avatar",        "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_description",      "group"=>"static_filters", "type"=>"",         "is_required"=>0),
		array("name"=>"show_only_with_albums",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_videos",           "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_posts",            "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"show_only_with_albums_or_videos", "group"=>"static_filters", "type"=>"INT",      "is_required"=>0, "default_value"=>"1"),
		array("name"=>"skip_category_groups",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_category_groups",            "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"category_group_ids",              "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>"", "is_deprecated"=>1),

		// dynamic filters
		array("name"=>"var_title_section",      "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"section"),
		array("name"=>"var_category_group_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group"),
		array("name"=>"var_category_group_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_id"),
		array("name"=>"var_category_group_ids", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"group_ids"),

		// related
		array("name"=>"mode_related",     "group"=>"related", "type"=>"CHOICE[1,2,3]", "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_category_dir", "group"=>"related", "type"=>"STRING",        "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",  "group"=>"related", "type"=>"STRING",        "is_required"=>0, "default_value"=>"category_id"),
		array("name"=>"var_category_ids", "group"=>"related", "type"=>"STRING",        "is_required"=>0, "default_value"=>"category_ids"),
		array("name"=>"var_mode_related", "group"=>"related", "type"=>"STRING",        "is_required"=>0, "default_value"=>"mode_related"),

		// pull videos
		array("name"=>"pull_videos",            "group"=>"pull_videos", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_videos_count",      "group"=>"pull_videos", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),
		array("name"=>"pull_videos_sort_by",    "group"=>"pull_videos", "type"=>"SORTING[duration,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,video_viewed,video_viewed_today,video_viewed_week,video_viewed_month,most_favourited,most_commented,ctr]", "is_required"=>0, "default_value"=>"post_date desc"),
		array("name"=>"pull_videos_duplicates", "group"=>"pull_videos", "type"=>"",    "is_required"=>0),

		// pull albums
		array("name"=>"pull_albums",            "group"=>"pull_albums", "type"=>"",    "is_required"=>0),
		array("name"=>"pull_albums_count",      "group"=>"pull_albums", "type"=>"INT", "is_required"=>0, "default_value"=>"5"),
		array("name"=>"pull_albums_sort_by",    "group"=>"pull_albums", "type"=>"SORTING[photos_amount,post_date,last_time_view_date,rating,rating_today,rating_week,rating_month,album_viewed,album_viewed_today,album_viewed_week,album_viewed_month,most_favourited,most_commented]", "is_required"=>0, "default_value"=>"post_date desc"),
		array("name"=>"pull_albums_duplicates", "group"=>"pull_albums", "type"=>"",    "is_required"=>0),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
