<?php
function top_referersShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	$where='';
	if ($block_config['show_referers']<>'')
	{
		$referer_ids=array_map('intval',explode(",",$block_config['show_referers']));
		if (count($referer_ids)>0)
		{
			$referer_ids=implode(',',$referer_ids);
			$where.=" and $config[tables_prefix_multi]stats_referers_list.referer_id not in ($referer_ids)";
		}
	}

	if ($block_config['skip_referers']<>'')
	{
		$referer_ids=array_map('intval',explode(",",$block_config['skip_referers']));
		if (count($referer_ids)>0)
		{
			$referer_ids=implode(',',$referer_ids);
			$where.=" and $config[tables_prefix_multi]stats_referers_list.referer_id in ($referer_ids)";
		}
	}

	if ($block_config['skip_categories']<>'')
	{
		$category_ids=array_map('intval',explode(",",$block_config['skip_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where.=" and $config[tables_prefix_multi]stats_referers_list.category_id not in ($category_ids)";
		}
	}

	if ($block_config['show_categories']<>'')
	{
		$category_ids=array_map('intval',explode(",",$block_config['show_categories']));
		if (count($category_ids)>0)
		{
			$category_ids=implode(',',$category_ids);
			$where.=" and $config[tables_prefix_multi]stats_referers_list.category_id in ($category_ids)";
		}
	}

	$category_id=0;
	if ((isset($block_config['var_category_dir']) && $_REQUEST[$block_config['var_category_dir']]<>'') || (isset($block_config['var_category_id']) && $_REQUEST[$block_config['var_category_id']]<>''))
	{
		if ($_REQUEST[$block_config['var_category_dir']]<>'')
		{
			$category_id=mr2number(sql_pr("select category_id from $config[tables_prefix]categories where (dir=? or $database_selectors[where_locale_dir])",trim($_REQUEST[$block_config['var_category_dir']]),trim($_REQUEST[$block_config['var_category_dir']])));
		} else {
			$category_id=intval($_REQUEST[$block_config['var_category_id']]);
		}
		if ($category_id>0)
		{
			$where.=" and category_id=$category_id";
		}
	}

	if ($category_id==0)
	{
		if ((isset($block_config['var_video_dir']) && $_REQUEST[$block_config['var_video_dir']]<>'') || (isset($block_config['var_video_id']) && $_REQUEST[$block_config['var_video_id']]<>''))
		{
			$video_id=intval($_REQUEST[$block_config['var_video_id']]);
			if ($video_id==0)
			{
				$video_id=mr2number(sql_pr("SELECT video_id from $config[tables_prefix]videos where (dir=? or $database_selectors[where_locale_dir])",$_REQUEST[$block_config['var_video_dir']],$_REQUEST[$block_config['var_video_dir']]));
			}
			if ($video_id>0)
			{
				$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_videos where video_id=?",$video_id));
				if (count($category_ids)>0)
				{
					$category_ids=implode(',',$category_ids);
					$where.=" and category_id in ($category_ids)";
				}
			}
		}
	}

	$need_date=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-intval($block_config['stats_period']),date("Y")));
	$data=mr2array(sql("select $config[tables_prefix_multi]stats_referers_list.*, uniq_amount, total_amount, view_video_amount, view_embed_amount, cs_out_amount, adv_out_amount from (
	select referer_id, sum(uniq_amount) as uniq_amount, sum(uniq_amount+raw_amount) as total_amount, sum(view_video_amount) as view_video_amount, sum(view_embed_amount) as view_embed_amount, sum(cs_out_amount) as cs_out_amount, sum(adv_out_amount) as adv_out_amount from $config[tables_prefix_multi]stats_in where added_date>='$need_date' group by referer_id
	) x right join $config[tables_prefix_multi]stats_referers_list using(referer_id)  where referer<>'<bookmarks>' and url<>'' $where order by $block_config[sort_by] LIMIT $block_config[items_per_page]"));

	$video_ids=array();
	foreach ($data as $k=>$res)
	{
		$data[$k]['base_files_url']=$config['content_url_referers'].'/'.$res['referer_id'];
		if (isset($block_config['mode_videos']))
		{
			$where_category='';
			$where_video_ids='';

			if (count($video_ids)>0)
			{
				$where_video_ids=" and video_id not in (".implode(",",$video_ids).")";
			}
			if ($res['category_id']>0)
			{
				$where_category=" and video_id in (select video_id from $config[tables_prefix]categories_videos where category_id=$res[category_id])";
			}

			$data[$k]['video']=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where is_private=0 and $database_selectors[where_videos] $where_category $where_video_ids order by $block_config[mode_videos] desc limit 1"));
			if (count($data[$k]['video'])<1)
			{
				unset($data[$k]);
			} else {
				$video_id=$data[$k]['video']['video_id'];
				$video_ids[]=$video_id;

				$data[$k]['video']['time_passed_from_adding']=get_time_passed($data[$k]['video']['post_date']);
				$data[$k]['video']['duration_array']=get_duration_splitted($data[$k]['video']['duration']);
				$data[$k]['video']['formats']=get_video_formats($data[$k]['video']['video_id'],$data[$k]['video']['file_formats']);
				$data[$k]['video']['dir_path']=get_dir_by_id($data[$k]['video']['video_id']);

				$screen_url_base=load_balance_screenshots_url();
				$data[$k]['video']['screen_url']=$screen_url_base.'/'.get_dir_by_id($video_id).'/'.$video_id;
				$data[$k]['video']['content_url']=$config['project_url']."/".str_replace("%ID%",$video_id,str_replace("%DIR%",$data[$k]['video']['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
				$data[$k]['video']['view_page_url']=$data[$k]['video']['content_url'];
			}
		}
	}

	if (isset($block_config['mode_videos']))
	{
		$storage[$object_id]['mode_videos']=1;
		$smarty->assign("mode_videos",1);
	}

	$smarty->assign("data",$data);
}

function top_referersGetHash($block_config)
{
	$var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	$var_category_id=intval($_REQUEST[$block_config['var_category_id']]);
	$var_video_dir=trim($_REQUEST[$block_config['var_video_dir']]);
	$var_video_id=intval($_REQUEST[$block_config['var_video_id']]);

	return "$var_category_dir|$var_category_id|$var_video_dir|$var_video_id";
}

function top_referersCacheControl($block_config)
{
	return "default";
}

function top_referersAsync($block_config)
{
	global $config;

	if ($_GET['action']=='redirect')
	{
		if ($_GET['content_url']=='' && $_GET['referer_url']<>'')
		{
			header("Location: $_GET[referer_url]");die;
		} elseif ($_GET['content_url']<>'' && $_GET['referer_url']=='')
		{
			header("Location: $_GET[content_url]");die;
		} elseif ($_GET['p']=='' && $_GET['content_url']<>'')
		{
			header("Location: $_GET[content_url]");die;
		} elseif ($_GET['p']=='' && $_GET['referer_url']<>'')
		{
			header("Location: $_GET[referer_url]");die;
		} elseif ($_GET['p']=='' && $_GET['content_url']=='' && $_GET['referer_url']=='')
		{
			header("Location: $config[project_url]");die;
		} else {
			mt_srand(time());
			$rnd=rand(0,100);

			if ($rnd<=intval($_GET['p']))
			{
				header("Location: $_GET[content_url]");die;
			} else {
				header("Location: $_GET[referer_url]");die;
			}
		}
	}
}

function top_referersMetaData()
{
	return array(
		// top
		array("name"=>"items_per_page", "group"=>"top", "type"=>"INT", "is_required"=>1, "default_value"=>"10"),
		array("name"=>"sort_by",        "group"=>"top", "type"=>"SORTING[referer_id,title,uniq_amount,total_amount,view_video_amount,view_embed_amount,cs_out_amount+adv_out_amount,total_amount/uniq_amount,view_video_amount/uniq_amount,(cs_out_amount+adv_out_amount)/uniq_amount,view_video_amount/total_amount,(cs_out_amount+adv_out_amount)/total_amount]", "is_required"=>1, "default_value"=>"rand()"),
		array("name"=>"stats_period",   "group"=>"top", "type"=>"INT",  "is_required"=>1, "default_value"=>"30"),

		// static filters
		array("name"=>"show_referers",   "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_referers",   "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"show_categories", "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),
		array("name"=>"skip_categories", "group"=>"static_filters", "type"=>"INT_LIST", "is_required"=>0, "default_value"=>""),

		// dynamic filters
		array("name"=>"var_video_dir",    "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"dir"),
		array("name"=>"var_video_id",     "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),
		array("name"=>"var_category_dir", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category"),
		array("name"=>"var_category_id",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"category_id"),

		// modes
		array("name"=>"mode_videos", "group"=>"modes", "type"=>"CHOICE[duration,post_date,last_time_view_date,rating,video_viewed,rand()]", "is_required"=>0, "default_value"=>"rand()"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
