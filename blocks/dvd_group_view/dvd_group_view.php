<?php
function dvd_group_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors,$website_ui_data;

	if (isset($block_config['var_dvd_group_id']) && intval($_REQUEST[$block_config['var_dvd_group_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups_active_disabled] and dvd_group_id=?",intval($_REQUEST[$block_config['var_dvd_group_id']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
		if (trim($_REQUEST[$block_config['var_dvd_group_dir']])<>'' && trim($_REQUEST[$block_config['var_dvd_group_dir']])<>$data['dir'] && $website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']!='')
		{
			$redirect_url=$config['project_url']."/".str_replace("%ID%",$data['dvd_group_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
			return "status_301:$redirect_url";
		}
	} elseif (trim($_REQUEST[$block_config['var_dvd_group_dir']])<>'')
	{
		$result=sql_pr("SELECT $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where $database_selectors[where_dvds_groups_active_disabled] and (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_dvd_group_dir']]),trim($_REQUEST[$block_config['var_dvd_group_dir']]));
		if (mr2rows($result)==0) {return 'status_404';}

		$data=mr2array_single($result);
	} else {
		return '';
	}

	$data['dvds_count']=$data['total_dvds'];

	$data['base_files_url']=$config['content_url_dvds'].'/groups/'.$data['dvd_group_id'];
	if ($website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']!='')
	{
		$data['canonical_url']="$config[project_url]/".str_replace("%ID%",$data['dvd_group_id'],str_replace("%DIR%",$data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_DVD_GROUP']));
	}

	$data['categories']=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories inner join $config[tables_prefix]categories_dvds_groups on $config[tables_prefix]categories.category_id=$config[tables_prefix]categories_dvds_groups.category_id where $database_selectors[where_categories] and dvd_group_id=$data[dvd_group_id] order by id asc"));
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

	$data['tags']=mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags inner join $config[tables_prefix]tags_dvds_groups on $config[tables_prefix]tags.tag_id=$config[tables_prefix]tags_dvds_groups.tag_id where $database_selectors[where_tags] and dvd_group_id=$data[dvd_group_id] order by id asc"));
	foreach ($data['tags'] as $v)
	{
		$data['tags_as_string'].=$v['tag'].", ";
	}
	$data['tags_as_string']=rtrim($data['tags_as_string'],", ");

	$data['models']=mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models inner join $config[tables_prefix]models_dvds_groups on $config[tables_prefix]models.model_id=$config[tables_prefix]models_dvds_groups.model_id where $database_selectors[where_models] and dvd_group_id=$data[dvd_group_id] order by id asc"));
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

	if (count($model_group_ids)>0)
	{
		$model_group_ids=implode(',',array_unique($model_group_ids));
		$model_groups=mr2array(sql("select $database_selectors[models_groups] from $config[tables_prefix]models_groups where $database_selectors[where_models_groups] and model_group_id in ($model_group_ids)"));
		foreach ($data['models'] as $k=>$v)
		{
			if ($v['model_group_id']>0)
			{
				foreach ($model_groups as $model_group)
				{
					if ($v['model_group_id']==$model_group['model_group_id'])
					{
						$model_group['base_files_url']=$config['content_url_models'].'/groups/'.$model_group['model_group_id'];
						$data['models'][$k]['model_group']=$model_group;
						break;
					}
				}
			}
		}
	}

	foreach ($data as $k=>$v)
	{
		$storage[$object_id][$k]=$v;
	}

	$smarty->assign("data",$data);
	return '';
}

function dvd_group_viewGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_dvd_group_dir']]);
	$id=intval($_REQUEST[$block_config['var_dvd_group_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	return "$dir|$id";
}

function dvd_group_viewCacheControl($block_config)
{
	return "user_nocache";
}

function dvd_group_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_dvd_group_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_dvd_group_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
