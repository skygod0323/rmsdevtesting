<?php
function model_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$list_countries,$database_selectors,$website_ui_data;

	if (isset($block_config['var_model_id']) && intval($_REQUEST[$block_config['var_model_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models_active_disabled] and model_id=?",intval($_REQUEST[$block_config['var_model_id']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_model_dir']])<>'' && trim($_REQUEST[$block_config['var_model_dir']])<>$data['dir'] && $website_ui_data['WEBSITE_LINK_PATTERN_MODEL']!='')
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['model_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_model_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_model_dir']]),trim($_REQUEST[$block_config['var_model_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	if ($data['access_level_id']==1 && $_SESSION['user_id']<1)
	{
		return 'status_404';
	} elseif ($data['access_level_id']==2 && $_SESSION['status_id']!=3)
	{
		return 'status_404';
	}

	$data['videos_count']=$data['total_videos'];
	$data['albums_count']=$data['total_albums'];
	$data['photos_count']=$data['total_photos'];
	$data['country']=$list_countries['name'][$data['country_id']];
	if ($data['age']==0) {
		$data['age']='';
	}

	if ($_SESSION['user_id']>0)
	{
		$data['is_subscribed']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=4",$_SESSION['user_id'],$data['model_id']))>0)
		{
			$data['is_subscribed']=1;
		}
	}

	$data['base_files_url']=$config['content_url_models'].'/'.$data['model_id'];
	if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']!='')
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['model_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_models on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_models.category_id where $database_selectors[where_categories] and model_id=$data[model_id] order by id asc"));
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

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_models on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_models.tag_id where $database_selectors[where_tags] and model_id=$data[model_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	if ($data['model_group_id']>0)
	{
		$result=sql_pr("select $database_selectors[models_groups] from $config[tables_prefix]models_groups where $database_selectors[where_models_groups] and model_group_id=?",$data['model_group_id']);
		if (mr2rows($result)>0)
		{
			$data['model_group']=mr2array_single($result);
			$data['model_group']['base_files_url']=$config['content_url_models'].'/groups/'.$data['model_group']['model_group_id'];
			$data['group_as_string']=$data['model_group']['title'];
		}
	}

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	if (isset($block_config['show_next_and_previous_info']))
	{
		if (intval($block_config['show_next_and_previous_info'])==0)
		{
			$result_next=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models] and model_id>? order by model_id asc limit 1",intval($data['model_id']));
			$result_previous=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models] and model_id<? order by model_id desc limit 1",intval($data['model_id']));
		} elseif (intval($block_config['show_next_and_previous_info'])==1 && $data['model_group_id']>0)
		{
			$result_next=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models] and model_group_id=? and model_id>? order by model_id asc limit 1",$data['model_group_id'],intval($data['model_id']));
			$result_previous=sql_pr("SELECT $database_selectors[models] from $config[tables_prefix]models where $database_selectors[where_models] and model_group_id=? and model_id<? order by model_id desc limit 1",$data['model_group_id'],intval($data['model_id']));
		}

		if (isset($result_next) && mr2rows($result_next)>0)
		{
			$object_next=mr2array_single($result_next);
			$object_next['base_files_url']=$config['content_url_models'].'/'.$object_next['model_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']!='')
			{
				$pattern=str_replace("%ID%",$object_next['model_id'],str_replace("%DIR%",$object_next['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
				$object_next['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("next_model",$object_next);
		}
		if (isset($result_previous) && mr2rows($result_previous)>0)
		{
			$object_previous=mr2array_single($result_previous);
			$object_previous['base_files_url']=$config['content_url_models'].'/'.$object_previous['model_id'];
			if ($website_ui_data['WEBSITE_LINK_PATTERN_MODEL']!='')
			{
				$pattern=str_replace("%ID%",$object_previous['model_id'],str_replace("%DIR%",$object_previous['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_MODEL']));
				$object_previous['view_page_url']="$config[project_url]/$pattern";
			}

			$smarty->assign("previous_model",$object_previous);
		}
	}

	$smarty->assign("data",$data);
	return '';
}

function model_viewPreProcess($block_config,$object_id)
{
	global $config;

	if (intval($_REQUEST[$block_config['var_model_id']])>0)
	{
		$stats_str=intval($_REQUEST[$block_config['var_model_id']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/models_id.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	} elseif (trim($_REQUEST[$block_config['var_model_dir']])<>'')
	{
		$stats_str=trim($_REQUEST[$block_config['var_model_dir']])."||".date("Y-m-d");
		$fh=fopen("$config[project_path]/admin/data/stats/models_dir.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	}
}

function model_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_model_dir']]);
	$id=intval($_REQUEST[$block_config['var_model_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('models_info','model',$id,$dir);
	}

	return "$dir|$id|$current_version";
}

function model_viewCacheControl($block_config)
{
	return "user_nocache";
}

function model_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_model_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_model_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"CHOICE[0,1]", "is_required"=>0)
	);
}

function model_viewAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='rate' && intval($_REQUEST['model_id'])>0)
	{
		if (isset($_REQUEST['vote']))
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");

			$model_id=intval($_REQUEST['model_id']);
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where model_id=$model_id"))==0)
			{
				async_return_request_status(array(array('error_code'=>'invalid_model','block'=>'model_view')));
			}

			$rating=intval($_REQUEST['vote']);
			if ($rating>10){$rating=10;}
			if ($rating<0){$rating=0;}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]rating_history where model_id=? and ip=?",$model_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
			{
				async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'model_view')));
			} else {
				sql_pr("insert into $config[tables_prefix]rating_history set model_id=?, ip=?, added_date=?",$model_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql("update $config[tables_prefix]models set rating_amount=(case when rating=0 then 1 else rating_amount+1 end), rating=rating+$rating where model_id=$model_id");
				if (intval($_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]users set ratings_models_count=ratings_models_count+1, ratings_total_count=ratings_total_count+1 where user_id=?",intval($_SESSION['user_id']));
				}

				$result_data=mr2array_single(sql_pr("select rating/rating_amount as rating, rating_amount from $config[tables_prefix]models where model_id=$model_id"));
				$result_data['rating']=floatval($result_data['rating']);
				$result_data['rating_amount']=intval($result_data['rating_amount']);
				async_return_request_status(null,null,$result_data);
			}
		} else {
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'model_view')));
		}
	}
}

function model_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingModelView.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
