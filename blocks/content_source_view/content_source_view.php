<?php
function content_source_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	if (isset($block_config['var_content_source_id']) && intval($_REQUEST[$block_config['var_content_source_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources_active_disabled] and content_source_id=?",intval($_REQUEST[$block_config['var_content_source_id']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_content_source_dir']])<>'' && trim($_REQUEST[$block_config['var_content_source_dir']])<>$data['dir'] && $website_ui_data['WEBSITE_LINK_PATTERN_CS']!='')
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['content_source_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_content_source_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_content_source_dir']]),trim($_REQUEST[$block_config['var_content_source_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	$data['videos_count']=$data['total_videos'];
	$data['albums_count']=$data['total_albums'];
	$data['photos_count']=$data['total_photos'];

	if ($_SESSION['user_id']>0)
	{
		$data['is_subscribed']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=3",$_SESSION['user_id'],$data['content_source_id']))>0)
		{
			$data['is_subscribed']=1;
		}
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_content_sources on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_content_sources.category_id where $database_selectors[where_categories] and content_source_id=$data[content_source_id] order by id asc"));
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
	if (count($data['categories'])>0)
	{
		$data['category']=$data['categories'][0];
	}

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_content_sources on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_content_sources.tag_id where $database_selectors[where_tags] and content_source_id=$data[content_source_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	if ($data['content_source_group_id']>0)
	{
		$result=sql_pr("select $database_selectors[content_sources_groups] from $config[tables_prefix]content_sources_groups where $database_selectors[where_content_sources_groups] and content_source_group_id=?",$data['content_source_group_id']);
		if (mr2rows($result)>0)
		{
			$data['content_source_group']=mr2array_single($result);
			$data['group_as_string']=$data['content_source_group']['title'];
		}
	}

	$data['base_files_url']=$config['content_url_content_sources'].'/'.$data['content_source_id'];
	if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']!='')
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['content_source_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
	}

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id>? order by content_source_id asc limit 1",intval($data['content_source_id']));
			$result_previous=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_id<? order by content_source_id desc limit 1",intval($data['content_source_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==1 && $data['content_source_group_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_group_id=? and content_source_id>? order by content_source_id asc limit 1",$data['content_source_group_id'],intval($data['content_source_id']));
			$result_previous=sql_pr("SELECT $database_selectors[content_sources] from $config[tables_prefix]content_sources where $database_selectors[where_content_sources] and content_source_group_id=? and content_source_id<? order by content_source_id desc limit 1",$data['content_source_group_id'],intval($data['content_source_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$object_next['base_files_url']=$config['content_url_content_sources'].'/'.$object_next['content_source_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']!='')
			{
				$pattern=str_replace("%ID%",$object_next['content_source_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
				$object_next['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("next_content_source",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$object_previous['base_files_url']=$config['content_url_content_sources'].'/'.$object_previous['content_source_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_CS']!='')
			{
				$pattern=str_replace("%ID%",$object_previous['content_source_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_CS']));
				$object_previous['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("previous_content_source",$object_previous);
		}
	}

	$smarty->assign("data",$data);
	return '';
}

function content_source_viewPreProcess($block_config,$object_id)
{
	global $config;

	if (intval($_REQUEST[$block_config['var_content_source_id']])>0)
	{
		$stats_str=intval($_REQUEST[$block_config['var_content_source_id']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/cs_id.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	} elseif (trim($_REQUEST[$block_config['var_content_source_dir']])<>'')
	{
		$stats_str=trim($_REQUEST[$block_config['var_content_source_dir']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/cs_dir.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	}
}

function content_source_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_content_source_dir']]);
	$id=intval($_REQUEST[$block_config['var_content_source_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('cs_info','cs',$id,$dir);
	}

	return "$dir|$id|$current_version";
}

function content_source_viewCacheControl($block_config)
{
	return "user_nocache";
}

function content_source_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_content_source_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_content_source_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,1]", "is_required"=>0)
	);
}

function content_source_viewAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['cs_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$content_source_id=intval($_REQUEST['cs_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where content_source_id=$content_source_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_content_source','block'=>'content_source_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where content_source_id=? and ip=?",$content_source_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'content_source_view')));
			} else {
				sql_pr("insert into $config[tables_prefix]rating_history set content_source_id=?, ip=?, added_date=?",$content_source_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]content_sources set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where content_source_id=$content_source_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_cs_count=ratings_cs_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]content_sources where content_source_id=$content_source_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'content_source_view')));
		}
	}
}

function content_source_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingCSView.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
