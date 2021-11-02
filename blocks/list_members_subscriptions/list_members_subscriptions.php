<?php
function list_members_subscriptionsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	if ($_REQUEST['action']=='delete_subscriptions' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));
			$subscribed_ids=mr2array_list(sql("select subscribed_object_id from $config[tables_prefix]users_subscriptions where user_id=$user_id and subscription_id in ($delete_ids)"));
			sql("delete from $config[tables_prefix]users_subscriptions where user_id=$user_id and subscription_id in ($delete_ids)");

			if (count($subscribed_ids)>0)
			{
				$subscribed_ids=implode(",",array_map("intval",$subscribed_ids));
				sql("update $config[tables_prefix]users set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]users.user_id and subscribed_type_id=1) where user_id in ($subscribed_ids)");
				sql("update $config[tables_prefix]content_sources set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]content_sources.content_source_id and subscribed_type_id=3) where content_source_id in ($subscribed_ids)");
				sql("update $config[tables_prefix]models set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]models.model_id and subscribed_type_id=4) where model_id in ($subscribed_ids)");
				sql("update $config[tables_prefix]dvds set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]dvds.dvd_id and subscribed_type_id=5) where dvd_id in ($subscribed_ids)");
				sql("update $config[tables_prefix]categories set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]categories.category_id and subscribed_type_id=6) where category_id in ($subscribed_ids)");
				sql("update $config[tables_prefix]playlists set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]playlists.playlist_id and subscribed_type_id=13) where playlist_id in ($subscribed_ids)");
			}

			$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$user_id));

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members_subscriptions')));
		}
	}

	if (isset($block_config['var_user_id']))
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

	$where_subscriptions=" where user_id=$user_id";
	$where_purchases=" where user_id=$user_id";

	if (isset($block_config['var_subscribed_type']) && intval($_REQUEST[$block_config['var_subscribed_type']])>0)
	{
		$where_subscriptions.=" and subscribed_type_id=".intval($_REQUEST[$block_config['var_subscribed_type']]);
		if (intval($_REQUEST[$block_config['var_subscribed_type']])==1)
		{
			$where_purchases.=" and profile_id>0";
		} elseif (intval($_REQUEST[$block_config['var_subscribed_type']])==5)
		{
			$where_purchases.=" and dvd_id>0";
		} else
		{
			$where_purchases.=" and 1=0";
		}
	} elseif (isset($block_config['subscribed_type']) && intval($block_config['subscribed_type'])>0)
	{
		$where_subscriptions.=" and subscribed_type_id=".intval($block_config['subscribed_type']);
		if (intval($block_config['subscribed_type'])==1)
		{
			$where_purchases.=" and profile_id>0";
		} elseif (intval($block_config['subscribed_type'])==5)
		{
			$where_purchases.=" and dvd_id>0";
		} else
		{
			$where_purchases.=" and 1=0";
		}
	}

	$data=list_members_subscriptionsMetaData();
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
	if ($sort_by_clear=='') {$sort_by_clear="subscription_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$sort_by="$sort_by_clear $direction";

	$table_selector="*";
	$table_projector="$config[tables_prefix]users_subscriptions";
	$where=$where_subscriptions;
	if (isset($block_config['mode_purchased']))
	{
		$table_selector="subscription_id, user_id, case when profile_id>0 then profile_id when dvd_id>0 then dvd_id end as subscribed_object_id, case when profile_id>0 then 1 when dvd_id>0 then 5 end as subscribed_type_id, added_date, expiry_date, tokens, case when (select subscription_id from $config[tables_prefix]users_subscriptions where $config[tables_prefix]users_subscriptions.subscription_id=$config[tables_prefix]users_purchases.subscription_id)>0 then 0 else 1 end as is_cancelled";
		$table_projector="$config[tables_prefix]users_purchases";
		$where="$where_purchases and subscription_id>0 and expiry_date>'".date("Y-m-d H:i:s")."'";
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $table_projector $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select $table_selector from $table_projector $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("select $table_selector from $table_projector $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		if ($v['subscribed_type_id']==1)
		{
			$data[$k]['user']=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['user']['display_name'];

			if ($data[$k]['user']['avatar']!='')
			{
				$data[$k]['user']['avatar_url']="$config[content_url_avatars]/".$data[$k]['user']['avatar'];
			}
		} elseif ($v['subscribed_type_id']==3)
		{
			$data[$k]['content_source']=mr2array_single(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where content_source_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['content_source']['title'];

			$data[$k]['content_source']['base_files_url']=$config['content_url_content_sources'].'/'.$data[$k]['content_source']['content_source_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['content_source']['content_source_id'],str_replace("%DIR%",$data[$k]['content_source']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
				$data[$k]['content_source']['view_page_url']="$config[project_url]/$pattern";
			}
		} elseif ($v['subscribed_type_id']==4)
		{
			$data[$k]['model']=mr2array_single(sql_pr("select $database_selectors[models] from $config[tables_prefix]models where model_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['model']['title'];

			$data[$k]['model']['base_files_url']=$config['content_url_models'].'/'.$data[$k]['model']['model_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['model']['model_id'],str_replace("%DIR%",$data[$k]['model']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
				$data[$k]['model']['view_page_url']="$config[project_url]/$pattern";
			}
		} elseif ($v['subscribed_type_id']==5)
		{
			$data[$k]['dvd']=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where dvd_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['dvd']['title'];

			$data[$k]['dvd']['base_files_url']=$config['content_url_dvds'].'/'.$data[$k]['dvd']['dvd_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['dvd']['dvd_id'],str_replace("%DIR%",$data[$k]['dvd']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD']));
				$data[$k]['dvd']['view_page_url']="$config[project_url]/$pattern";
			}
		} elseif ($v['subscribed_type_id']==6)
		{
			$data[$k]['category']=mr2array_single(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories where category_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['category']['title'];

			$data[$k]['category']['base_files_url']=$config['content_url_categories'].'/'.$data[$k]['category']['category_id'];
		} elseif ($v['subscribed_type_id']==13)
		{
			$data[$k]['playlist']=mr2array_single(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=".$data[$k]['subscribed_object_id']));
			$data[$k]['title']=$data[$k]['playlist']['title'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']<>'')
			{
				$pattern=str_replace("%ID%",$data[$k]['playlist']['playlist_id'],str_replace("%DIR%",$data[$k]['playlist']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_PLAYLIST']));
				$data[$k]['playlist']['view_page_url']="$config[project_url]/$pattern";
			}
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_members_subscriptionsGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_subscribed_type=intval($_REQUEST[$block_config['var_subscribed_type']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);
	if (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "$from|$items_per_page|$var_user_id|$var_subscribed_type|$var_sort_by";
}

function list_members_subscriptionsCacheControl($block_config)
{
	if (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "default";
}

function list_members_subscriptionsAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_subscriptions') && isset($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_members_subscriptionsShow($block_config,null);
	}
}

function list_members_subscriptionsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[subscription_id,added_date]", "is_required"=>1, "default_value"=>"added_date desc"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"subscribed_type", "group"=>"static_filters", "type"=>"CHOICE[1,3,4,5,13]", "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_subscribed_type", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"subscribed_type"),

		// display modes
		array("name"=>"mode_purchased",           "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
	);
}

function list_members_subscriptionsLegalRequestVariables()
{
	return array('action');
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>