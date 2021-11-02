<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

$table_name="$config[tables_prefix]videos";
$table_key_name="video_id";

$errors = null;

$options=get_options();

if ($options['VIDEO_FIELD_1_NAME']=='') {$options['VIDEO_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['VIDEO_FIELD_2_NAME']=='') {$options['VIDEO_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['VIDEO_FIELD_3_NAME']=='') {$options['VIDEO_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
$languages=mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));

$VIDEOS_EXPORT_PRESETS=array();
if ($options['VIDEOS_EXPORT_PRESETS']<>'')
{
	$VIDEOS_EXPORT_PRESETS=@unserialize($options['VIDEOS_EXPORT_PRESETS']);
}
if ($_POST['preset_id']<>'' && $_POST['preset_name']=='' && $_POST['action']=='start_export')
{
	$_POST['preset_name']=$_POST['preset_id'];
}
if ($_POST['preset_name']<>'')
{
	$name=$_POST['preset_name'];
	for ($i=1;$i<=999;$i++)
	{
		if (!isset($_POST["field$i"]))
		{
			$fields_amount=$i-1;break;
		}
	}
	$_POST['fields_amount']=$fields_amount;
	$post_date_from=intval($_POST["post_date_from_Year"])."-".intval($_POST["post_date_from_Month"])."-".intval($_POST["post_date_from_Day"]);
	$post_date_to=intval($_POST["post_date_to_Year"])."-".intval($_POST["post_date_to_Month"])."-".intval($_POST["post_date_to_Day"]);
	$_POST['post_date_from']=$post_date_from;
	$_POST['post_date_to']=$post_date_to;

	$added_date_from=intval($_POST["added_date_from_Year"])."-".intval($_POST["added_date_from_Month"])."-".intval($_POST["added_date_from_Day"]);
	$added_date_to=intval($_POST["added_date_to_Year"])."-".intval($_POST["added_date_to_Month"])."-".intval($_POST["added_date_to_Day"]);
	$_POST['added_date_from']=$added_date_from;
	$_POST['added_date_to']=$added_date_to;

	$temp_data=$_POST;
	unset($temp_data['action']);
	unset($temp_data['data']);
	unset($temp_data['file']);
	unset($temp_data['file_hash']);
	$VIDEOS_EXPORT_PRESETS[$name]=$temp_data;

	if ($temp_data['is_default_preset']==1)
	{
		foreach ($VIDEOS_EXPORT_PRESETS as $k=>$preset)
		{
			if ($k<>$name && $preset['is_default_preset']==1)
			{
				$VIDEOS_EXPORT_PRESETS[$k]['is_default_preset']=0;
			}
		}
	}

	sql_pr("update $config[tables_prefix]options set value=? where variable='VIDEOS_EXPORT_PRESETS'",serialize($VIDEOS_EXPORT_PRESETS));
}
if (!isset($_GET['preset_id']) && count($_POST)==0)
{
	foreach ($VIDEOS_EXPORT_PRESETS as $k=>$preset)
	{
		if ($preset['is_default_preset']==1)
		{
			$_GET['preset_id']=$k;
			break;
		}
	}
}
if (isset($_POST['delete_preset']) && isset($_POST['preset_id']))
{
	unset($VIDEOS_EXPORT_PRESETS[$_POST['preset_id']]);
	sql_pr("update $config[tables_prefix]options set value=? where variable='VIDEOS_EXPORT_PRESETS'",serialize($VIDEOS_EXPORT_PRESETS));

	$_SESSION['messages'][]=$lang['videos']['success_message_import_export_preset_removed'];
	return_ajax_success("$page_name");
} elseif (isset($_GET['preset_id']))
{
	$_POST=$VIDEOS_EXPORT_PRESETS[$_GET['preset_id']];
	settype($_POST['se_admin_ids'],"array");
	settype($_POST['se_user_ids'],"array");
	settype($_POST['se_category_ids'],"array");
	settype($_POST['se_model_ids'],"array");
	settype($_POST['se_cs_ids'],"array");
	settype($_POST['se_dvd_ids'],"array");

	if (isset($_POST['se_admin_ids']) && count($_POST['se_admin_ids'])>0)
	{
		$se_admin_ids=implode(",",array_map("intval",$_POST['se_admin_ids']));
		$_POST['admins']=mr2array(sql_pr("select user_id, login from $config[tables_prefix]admin_users where user_id in ($se_admin_ids) order by login asc"));
	}
	if (isset($_POST['se_user_ids']) && count($_POST['se_user_ids'])>0)
	{
		$se_user_ids=implode(",",array_map("intval",$_POST['se_user_ids']));
		$_POST['users']=mr2array(sql_pr("select user_id, username from $config[tables_prefix]users where user_id in ($se_user_ids) order by username asc"));
	}
	if (isset($_POST['se_category_ids']) && count($_POST['se_category_ids'])>0)
	{
		$se_category_ids=implode(",",array_map("intval",$_POST['se_category_ids']));
		$_POST['categories']=mr2array(sql_pr("select category_id, title from $config[tables_prefix]categories where category_id in ($se_category_ids) order by title asc"));
	}
	if (isset($_POST['se_model_ids']) && count($_POST['se_model_ids'])>0)
	{
		$se_model_ids=implode(",",array_map("intval",$_POST['se_model_ids']));
		$_POST['models']=mr2array(sql_pr("select model_id, title from $config[tables_prefix]models where model_id in ($se_model_ids) order by title asc"));
	}
	if (isset($_POST['se_cs_ids']) && count($_POST['se_cs_ids'])>0)
	{
		$se_cs_ids=implode(",",array_map("intval",$_POST['se_cs_ids']));
		$_POST['content_sources']=mr2array(sql_pr("select content_source_id, title from $config[tables_prefix]content_sources where content_source_id in ($se_cs_ids) order by title asc"));
	}
	if (isset($_POST['se_dvd_ids']) && count($_POST['se_dvd_ids'])>0)
	{
		$se_dvd_ids=implode(",",array_map("intval",$_POST['se_dvd_ids']));
		$_POST['dvds']=mr2array(sql_pr("select dvd_id, title from $config[tables_prefix]dvds where dvd_id in ($se_dvd_ids) order by title asc"));
	}
}

$list_formats_videos=mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) and access_level_id=0 order by title"));
$list_formats_screenshots_overview=mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1 order by title"));
$list_formats_screenshots_posters=mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3 order by title"));

$list_satellites=mr2array(sql("select * from $config[tables_prefix]admin_satellites order by multi_prefix"));
foreach ($list_satellites as $k=>$satellite)
{
	$list_satellites[$k]['website_ui_data']=@unserialize($satellite['website_ui_data']);
}

if ($_POST['action']=='start_export')
{
	for ($i=1;$i<=999;$i++)
	{
		if (!isset($_POST["field$i"]))
		{
			$fields_amount=$i-1;break;
		}
	}
	$_POST['fields_amount']=$fields_amount;

	$is_error=1;
	for ($i=1;$i<=$fields_amount;$i++)
	{
		if ($_POST["field$i"]<>'')
		{
			$is_error=0;break;
		}
	}
	if ($is_error) {$errors[]=get_aa_error('export_fields_required', $lang['videos']['export_divider_fields']);}

	validate_field('empty',$_POST['separator'],$lang['videos']['import_export_field_separator_fields']);
	validate_field('empty',$_POST['line_separator'],$lang['videos']['import_export_field_separator_lines']);

	if ($_POST['embed_url_pattern'])
	{
		if (strpos($_POST['embed_url_pattern'], '%ID%') === false)
		{
			$errors[] = get_aa_error('token_required', $lang['videos']['export_field_embed_code_url_pattern'], '%ID%');
		}
	}

	if ($_POST['is_post_date_range_enabled']==1)
	{
		$post_date_from=intval($_POST["post_date_from_Year"])."-".intval($_POST["post_date_from_Month"])."-".intval($_POST["post_date_from_Day"]);
		$post_date_to=intval($_POST["post_date_to_Year"])."-".intval($_POST["post_date_to_Month"])."-".intval($_POST["post_date_to_Day"]);
		$_POST['post_date_from']=$post_date_from;
		$_POST['post_date_to']=$post_date_to;

		if (intval($_POST["post_date_from_Year"])<1 || intval($_POST["post_date_from_Month"])<1 || intval($_POST["post_date_from_Day"])<1 ){validate_field('empty',"",$lang['videos']['export_field_post_date_range']);} else
		if (intval($_POST["post_date_to_Year"])<1 || intval($_POST["post_date_to_Month"])<1 || intval($_POST["post_date_to_Day"])<1 ){validate_field('empty',"",$lang['videos']['export_field_post_date_range']);} else
		if (strtotime($post_date_from)>strtotime($post_date_to))
		{
			$errors[]=get_aa_error('invalid_date_range',$lang['videos']['export_field_post_date_range']);
		}
	}
	if ($_POST['is_id_range_enabled']==1)
	{
		if ($_POST['id_range_from']<>'' && intval($_POST['id_range_from'])<1) {$errors[]=get_aa_error('integer_field',$lang['videos']['export_field_id_range']);} else
		if ($_POST['id_range_to']<>'' && intval($_POST['id_range_to'])<1) {$errors[]=get_aa_error('integer_field',$lang['videos']['export_field_id_range']);} else
		if ($_POST['id_range_from']=='' && $_POST['id_range_to']=='') {$errors[]=get_aa_error('required_field',$lang['videos']['export_field_id_range']);} else
		if ($_POST['id_range_from']<>'' && $_POST['id_range_to']<>'' && $_POST["id_range_from"]>$_POST["id_range_to"])
		{
			$errors[]=get_aa_error('invalid_id_range',$lang['videos']['export_field_id_range']);
		}
	}
	if ($_POST['is_added_date_range_enabled']==1)
	{
		$added_date_from=intval($_POST["added_date_from_Year"])."-".intval($_POST["added_date_from_Month"])."-".intval($_POST["added_date_from_Day"]);
		$added_date_to=intval($_POST["added_date_to_Year"])."-".intval($_POST["added_date_to_Month"])."-".intval($_POST["added_date_to_Day"]);
		$_POST['added_date_from']=$added_date_from;
		$_POST['added_date_to']=$added_date_to;

		if (intval($_POST["added_date_from_Year"])<1 || intval($_POST["added_date_from_Month"])<1 || intval($_POST["added_date_from_Day"])<1 ){validate_field('empty',"",$lang['videos']['export_field_added_date_range']);} else
		if (intval($_POST["added_date_to_Year"])<1 || intval($_POST["added_date_to_Month"])<1 || intval($_POST["added_date_to_Day"])<1 ){validate_field('empty',"",$lang['videos']['export_field_added_date_range']);} else
		if (strtotime($added_date_from)>strtotime($added_date_to))
		{
			$errors[]=get_aa_error('invalid_date_range',$lang['videos']['export_field_added_date_range']);
		}
	}
	if ($_POST['limit']<>'')
	{
		validate_field('empty_int',$_POST['limit'],$lang['videos']['export_field_limit']);
	}

	$post_date_selector='post_date';
	if ($config['relative_post_dates']=="true")
	{
		$now_date=date("Y-m-d H:i:s");
		$post_date_selector="(case when relative_post_date!=0 then date_add('$now_date', interval relative_post_date-1 day) else post_date end)";
	}
	$title_selector='title';
	$desc_selector='description';
	$tag_selector='tag';
	$locale_field_title='title';
	$locale_field_desc='description';
	if ($_POST['language']<>'')
	{
		foreach ($languages as $language)
		{
			if ($_POST['language']==$language['code'])
			{
				$title_selector="(case when title_$language[code]<>'' then title_$language[code] else title end)";
				$desc_selector="(case when description_$language[code]<>'' then description_$language[code] else description end)";
				$tag_selector="(case when tag_$language[code]<>'' then tag_$language[code] else tag end)";
				$locale_field_title="title_$language[code]";
				$locale_field_desc="description_$language[code]";
				break;
			}
		}
	}

	if (!is_array($errors))
	{
		settype($_POST['se_admin_ids'],"array");
		settype($_POST['se_user_ids'],"array");
		settype($_POST['se_category_ids'],"array");
		settype($_POST['se_model_ids'],"array");
		settype($_POST['se_cs_ids'],"array");
		settype($_POST['se_dvd_ids'],"array");

		$separator = str_replace(array("\\r", "\\n", "\\t"), array("\r", "\n", "\t"), $_POST['separator']);
		$line_separator= str_replace(array("\\r", "\\n", "\\t"), array("\r", "\n", "\t"), $_POST['line_separator']);

		switch ($_POST['order_by'])
		{
			case 'post_date':$order_by="post_date";break;
			case 'video_id':$order_by="video_id";break;
			case 'title':$order_by="$title_selector";break;
			case 'description':$order_by="$desc_selector";break;
			case 'content_source':$order_by="content_source_title";break;
			case 'dvd':$order_by="dvd_title";break;
			case 'duration':$order_by="duration";break;
			case 'rating':$order_by="rating";break;
			case 'video_viewed':$order_by="video_viewed";break;
			case 'user':$order_by="user_title";break;
			case 'custom_1':$order_by="custom1";break;
			case 'custom_2':$order_by="custom2";break;
			case 'custom_3':$order_by="custom3";break;
			case 'ctr':$order_by="r_ctr";break;
			case 'rand':$order_by="rand()";break;
			default:$order_by="post_date";break;
		}
		if ($order_by<>"rand()")
		{
			$order_direction=$_POST['order_direction'];
			if ($order_direction<>'asc') {$order_direction="desc";}

			if ($order_by=='post_date')
			{
				$order_by="$post_date_selector $order_direction, video_id $order_direction";
			} elseif ($order_by=='rating')
			{
				$order_by="$table_name.rating/$table_name.rating_amount $order_direction, $table_name.rating_amount $order_direction";
			} else {
				$order_by.=" $order_direction";
			}
		}
		if (intval($_POST['limit'])>0)
		{
			$limit="limit ".intval($_POST['limit']);
		}

		for ($i=1;$i<=$fields_amount;$i++)
		{
			if ($_POST["field$i"]=='categories')
			{
				$is_categories_selected=1;
			}
			if ($_POST["field$i"]=='models')
			{
				$is_models_selected=1;
			}
			if ($_POST["field$i"]=='tags')
			{
				$is_tags_selected=1;
			}
		}

		$where='';
		if ($_POST['se_text']!='')
		{
			$q=sql_escape($_POST['se_text']);
			$where.=" and ($locale_field_title like '%$q%' or $locale_field_desc like '%$q%') ";
		}

		if (isset($_POST['se_admin_ids']) && count($_POST['se_admin_ids'])>0)
		{
			$se_admin_ids=implode(",",array_map("intval",$_POST['se_admin_ids']));
			$where.=" and admin_user_id in ($se_admin_ids)";
		}
		if (isset($_POST['se_user_ids']) && count($_POST['se_user_ids'])>0)
		{
			$se_user_ids=implode(",",array_map("intval",$_POST['se_user_ids']));
			$where.=" and user_id in ($se_user_ids)";
		}
		if (isset($_POST['se_category_ids']) && count($_POST['se_category_ids'])>0)
		{
			$se_category_ids=implode(",",array_map("intval",$_POST['se_category_ids']));
			$where.=" and exists (select category_id from $config[tables_prefix]categories_videos where video_id=$table_name.video_id and category_id in ($se_category_ids))";
		}
		if (isset($_POST['se_model_ids']) && count($_POST['se_model_ids'])>0)
		{
			$se_model_ids=implode(",",array_map("intval",$_POST['se_model_ids']));
			$where.=" and exists (select model_id from $config[tables_prefix]models_videos where video_id=$table_name.video_id and model_id in ($se_model_ids))";
		}
		if ($_POST['se_tags']!='')
		{
			$tag_ids=array('0');
			$temp=explode(",",$_POST['se_tags']);

			foreach ($temp as $temp_tag)
			{
				$temp_tag=trim($temp_tag);
				if ($temp_tag=='') {continue;}

				$tag_id=mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?",$temp_tag));
				if ($tag_id>0)
				{
					$tag_ids[]=$tag_id;
				}
			}
			$tag_ids=implode(",",$tag_ids);
			$where.=" and exists (select tag_id from $config[tables_prefix]tags_videos where video_id=$table_name.video_id and tag_id in ($tag_ids))";
		}
		if (isset($_POST['se_cs_ids']) && count($_POST['se_cs_ids'])>0)
		{
			$se_cs_ids=implode(",",array_map("intval",$_POST['se_cs_ids']));
			$where.=" and content_source_id in ($se_cs_ids)";
		}
		if (isset($_POST['se_dvd_ids']) && count($_POST['se_dvd_ids'])>0)
		{
			$se_dvd_ids=implode(",",array_map("intval",$_POST['se_dvd_ids']));
			$where.=" and dvd_id in ($se_dvd_ids)";
		}

		if ($_POST['se_status_id']=='0') {$where.=" and status_id=0";} else
		if ($_POST['se_status_id']=='1') {$where.=" and status_id=1";} else
		if ($_POST['se_status_id']=='2') {$where.=" and status_id=2";} else
		if ($_POST['se_status_id']=='3') {$where.=" and status_id=3";}
		if ($_POST['se_review_flag']=='1') {$where.=" and is_review_needed=1";} else
		if ($_POST['se_review_flag']=='2') {$where.=" and is_review_needed=0";}
		if ($_POST['se_is_private']=='0') {$where.=" and is_private=0";} else
		if ($_POST['se_is_private']=='1') {$where.=" and is_private=1";} else
		if ($_POST['se_is_private']=='2') {$where.=" and is_private=2";}
		if ($_POST['se_load_type_id']=='0') {$where.=" and load_type_id=0";} else
		if ($_POST['se_load_type_id']=='1') {$where.=" and load_type_id=1";} else
		if ($_POST['se_load_type_id']=='2') {$where.=" and load_type_id=2";} else
		if ($_POST['se_load_type_id']=='3') {$where.=" and load_type_id=3";} else
		if ($_POST['se_load_type_id']=='5') {$where.=" and load_type_id=5";}
		if (intval($_POST['se_admin_flag_id'])>0) {$where.=" and admin_flag_id=".intval($_POST['se_admin_flag_id']);}
		if ($_POST['is_post_date_range_enabled']==1)
		{
			$post_date_from=intval($_POST["post_date_from_Year"])."-".intval($_POST["post_date_from_Month"])."-".intval($_POST["post_date_from_Day"])." 00:00:00";
			$post_date_to=intval($_POST["post_date_to_Year"])."-".intval($_POST["post_date_to_Month"])."-".intval($_POST["post_date_to_Day"])." 23:59:59";
			$where.=" and $post_date_selector>='$post_date_from' and $post_date_selector<='$post_date_to' ";
		}
		if ($_POST['is_id_range_enabled']==1)
		{
			$id_from=intval($_POST["id_range_from"]);
			$id_to=intval($_POST["id_range_to"]);
			if ($id_from>0)
			{
				$where.=" and video_id>='$id_from' ";
			}
			if ($id_to>0)
			{
				$where.=" and video_id<='$id_to' ";
			}
		}
		if ($_POST['is_added_date_range_enabled']==1)
		{
			$added_date_from=intval($_POST["added_date_from_Year"])."-".intval($_POST["added_date_from_Month"])."-".intval($_POST["added_date_from_Day"])." 00:00:00";
			$added_date_to=intval($_POST["added_date_to_Year"])."-".intval($_POST["added_date_to_Month"])."-".intval($_POST["added_date_to_Day"])." 23:59:59";
			$where.=" and added_date>='$added_date_from' and added_date<='$added_date_to' ";
		}
		if ($_POST['se_status_id']=='') {$where.=" and status_id in(0,1) ";}
		if ($_POST['is_post_time_considered']==1) {$now_date=date("Y-m-d H:i:s");$where.=" and $post_date_selector<='$now_date' ";}

		if ($_SESSION['userdata']['is_access_to_own_content']==1)
		{
			$admin_id=intval($_SESSION['userdata']['user_id']);
			$where.=" and admin_user_id=$admin_id ";
		}
		if ($_SESSION['userdata']['is_access_to_disabled_content']==1)
		{
			$where.=" and status_id=0 ";
		}
		if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
		{
			$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
		}
		if ($where!='') {$where=" where ".substr($where,4);}

		$export_id=mt_rand(10000000,99999999);
		$export_filename="$config[temporary_path]/export-$export_id.dat";

		$data=sql("select *, $title_selector as title, $desc_selector as description, $post_date_selector as post_date,
			(select $title_selector from $config[tables_prefix]content_sources where content_source_id=$table_name.content_source_id) as content_source_title,
			(select url from $config[tables_prefix]content_sources where content_source_id=$table_name.content_source_id) as content_source_url,
			(select $title_selector from $config[tables_prefix]dvds where dvd_id=$table_name.dvd_id) as dvd_title,
			(select username from $config[tables_prefix]users where user_id=$table_name.user_id) as user_title,
			(select title from $config[tables_prefix]flags where flag_id=$table_name.admin_flag_id) as flag_title,
			coalesce(format(rating/rating_amount,1),0) as rating,
			coalesce(ceil(rating/rating_amount*20),0) as rating_percent
		from $table_name $where order by $order_by $limit");

		if (intval($_POST['is_header_row'])==1)
		{
			$export_result_str='';
			for ($i=1;$i<=$fields_amount;$i++)
			{
				switch ($_POST["field$i"])
				{
					case 'video_id':$export_result_str.="{$separator}{$lang['videos']['import_export_field_id']}";break;
					case 'title':$export_result_str.="{$separator}{$lang['videos']['import_export_field_title']}";break;
					case 'directory':$export_result_str.="{$separator}{$lang['videos']['import_export_field_directory']}";break;
					case 'description':$export_result_str.="{$separator}{$lang['videos']['import_export_field_description']}";break;
					case 'categories':$export_result_str.="{$separator}{$lang['videos']['import_export_field_categories']}";break;
					case 'models':$export_result_str.="{$separator}{$lang['videos']['import_export_field_models']}";break;
					case 'tags':$export_result_str.="{$separator}{$lang['videos']['import_export_field_tags']}";break;
					case 'content_source':$export_result_str.="{$separator}{$lang['videos']['import_export_field_content_source']}";break;
					case 'content_source/url':$export_result_str.="{$separator}{$lang['videos']['import_export_field_content_source_url']}";break;
					case 'dvd':$export_result_str.="{$separator}{$lang['videos']['import_export_field_dvd']}";break;
					case 'website_link':$export_result_str.="{$separator}{$lang['videos']['import_export_field_website_link']}";break;
					case 'source_file':$export_result_str.="{$separator}{$lang['videos']['import_export_field_source_file_download_link']}";break;
					case 'video_url':$export_result_str.="{$separator}{$lang['videos']['import_export_field_video_url']}";break;
					case 'embed_code':$export_result_str.="{$separator}{$lang['videos']['import_export_field_embed_code']}";break;
					case 'gallery_url':$export_result_str.="{$separator}{$lang['videos']['import_export_field_gallery_url']}";break;
					case 'pseudo_url':$export_result_str.="{$separator}{$lang['videos']['import_export_field_pseudo_url']}";break;
					case 'duration':$export_result_str.="{$separator}{$lang['videos']['import_export_field_duration']}";break;
					case 'post_date':$export_result_str.="{$separator}{$lang['videos']['import_export_field_post_date']}";break;
					case 'added_date':$export_result_str.="{$separator}{$lang['videos']['import_export_field_added_date']}";break;
					case 'rating':$export_result_str.="{$separator}{$lang['videos']['import_export_field_rating']}";break;
					case 'rating_percent':$export_result_str.="{$separator}{$lang['videos']['import_export_field_rating_percent']}";break;
					case 'rating_amount':$export_result_str.="{$separator}{$lang['videos']['import_export_field_rating_amount']}";break;
					case 'video_viewed':$export_result_str.="{$separator}{$lang['videos']['import_export_field_visits']}";break;
					case 'user':$export_result_str.="{$separator}{$lang['videos']['import_export_field_user']}";break;
					case 'status':$export_result_str.="{$separator}{$lang['videos']['import_export_field_status']}";break;
					case 'type':$export_result_str.="{$separator}{$lang['videos']['import_export_field_type']}";break;
					case 'tokens':$export_result_str.="{$separator}{$lang['videos']['import_export_field_tokens_cost']}";break;
					case 'release_year':$export_result_str.="{$separator}{$lang['videos']['import_export_field_release_year']}";break;
					case 'admin_flag':$export_result_str.="{$separator}{$lang['videos']['import_export_field_admin_flag']}";break;
					case 'custom_1':$export_result_str.="{$separator}{$options['VIDEO_FIELD_1_NAME']}";break;
					case 'custom_2':$export_result_str.="{$separator}{$options['VIDEO_FIELD_2_NAME']}";break;
					case 'custom_3':$export_result_str.="{$separator}{$options['VIDEO_FIELD_3_NAME']}";break;
					case 'screenshot_main_number':$export_result_str.="{$separator}{$lang['videos']['import_export_field_screenshot_main_number']}";break;
					case 'screenshot_main_source':$export_result_str.="{$separator}{$lang['videos']['import_export_field_screenshot_main_source']}";break;
					case 'overview_screenshots_sources':$export_result_str.="{$separator}{$lang['videos']['import_export_field_screenshots_overview_sources']}";break;
					case 'poster_main_number':$export_result_str.="{$separator}{$lang['videos']['import_export_field_poster_main_format']}";break;
					case 'poster_main_source':$export_result_str.="{$separator}{$lang['videos']['import_export_field_poster_main_source']}";break;
					case 'posters_sources':$export_result_str.="{$separator}{$lang['videos']['import_export_field_posters_sources']}";break;
				}
				foreach ($list_satellites as $satellite)
				{
					if ($_POST["field$i"]=="website_link/{$satellite['multi_prefix']}")
					{
						$export_result_str.="{$separator}{$lang['videos']['import_export_field_website_link']} ($satellite[project_url])";
						break;
					}
				}
				foreach ($languages as $language)
				{
					if ($_POST["field$i"]=="title_{$language['code']}")
					{
						$export_result_str.="{$separator}{$lang['videos']['import_export_field_title']} ($language[title])";
						break;
					} elseif ($_POST["field$i"]=="description_{$language['code']}")
					{
						$export_result_str.="{$separator}{$lang['videos']['import_export_field_description']} ($language[title])";
						break;
					}
				}
				foreach ($list_formats_videos as $format_video)
				{
					if ($_POST["field$i"]=="format_video_{$format_video['format_video_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_video['title'],$lang['videos']['import_export_field_video_file_temp_link']);
						break;
					} elseif ($_POST["field$i"]=="hotlink_video_{$format_video['format_video_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_video['title'],$lang['videos']['import_export_field_video_file_hotlink']);
						break;
					} elseif ($_POST["field$i"]=="dimensions_video_{$format_video['format_video_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_video['title'],$lang['videos']['import_export_field_video_file_dimensions']);
						break;
					} elseif ($_POST["field$i"]=="duration_video_{$format_video['format_video_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_video['title'],$lang['videos']['import_export_field_video_file_duration']);
						break;
					} elseif ($_POST["field$i"]=="filesize_video_{$format_video['format_video_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_video['title'],$lang['videos']['import_export_field_video_file_filesize']);
						break;
					}
				}
				foreach ($list_formats_screenshots_overview as $format_screenshot)
				{
					if ($_POST["field$i"]=="screenshot_main_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_screenshot['title'],$lang['videos']['import_export_field_screenshot_main_format']);
						break;
					}
					if ($_POST["field$i"]=="overview_screenshots_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_screenshot['title'],$lang['videos']['import_export_field_screenshots_overview_format']);
						break;
					}
					if ($_POST["field$i"]=="overview_screenshots_zip_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="{$separator}".str_replace("%1%",$format_screenshot['title'],$lang['videos']['import_export_field_screenshots_overview_format_zip']);
						break;
					}
				}
				foreach ($list_formats_screenshots_posters as $format_screenshot)
				{
					if ($_POST["field$i"] == "poster_main_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "{$separator}" . str_replace("%1%", $format_screenshot['title'], $lang['videos']['import_export_field_poster_main_format']);
						break;
					}
					if ($_POST["field$i"] == "posters_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "{$separator}" . str_replace("%1%", $format_screenshot['title'], $lang['videos']['import_export_field_posters_format']);
						break;
					}
					if ($_POST["field$i"] == "posters_zip_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "{$separator}" . str_replace("%1%", $format_screenshot['title'], $lang['videos']['import_export_field_posters_format_zip']);
						break;
					}
				}
			}
			$export_result_str='#'.substr($export_result_str,strlen($separator));
			file_put_contents($export_filename,"{$export_result_str}$line_separator",FILE_APPEND);
		}
		while ($res = mr2array_single($data))
		{
			$video_id=$res['video_id'];
			$dir_path=get_dir_by_id($video_id);
			$export_result_str="";
			if ($is_categories_selected==1)
			{
				$category_titles=mr2array_list(sql_pr("select (select $title_selector from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) as title from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=$video_id order by id asc"));
				foreach ($category_titles as $k=>$v)
				{
					if (strpos($v, ',')!==false)
					{
						$category_titles[$k]=str_replace(",","\\,",$v);
					}
				}
				$res['categories']=implode(", ",$category_titles);
			}
			if ($is_models_selected==1)
			{
				$model_titles=mr2array_list(sql_pr("select (select $title_selector from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) as title from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=$video_id order by id asc"));
				foreach ($model_titles as $k=>$v)
				{
					if (strpos($v, ',')!==false)
					{
						$model_titles[$k]=str_replace(",","\\,",$v);
					}
				}
				$res['models']=implode(", ",$model_titles);
			}
			if ($is_tags_selected==1)
			{
				$res['tags']=implode(", ",mr2array_list(sql_pr("select (select $tag_selector from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) as tag from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=$video_id order by id asc")));
			}
			$video_formats=get_video_formats($video_id,$res['file_formats'],$res['server_group_id']);
			$res['file_dimensions']=explode("x",$res['file_dimensions']);

			for ($i=1;$i<=$fields_amount;$i++)
			{
				if ($res['is_private']==1)
				{
					$res['type_title']="Private";
				} elseif ($res['is_private']==2)
				{
					$res['type_title']="Premium";
				} else {
					$res['type_title']="Public";
				}

				if ($res['status_id']=='1')
				{
					$res['status_title']="Active";
				} elseif ($res['status_id']=='0')
				{
					$res['status_title']="Disabled";
				} else {
					$res['status_title']="Error";
				}

				switch ($_POST["field$i"])
				{
					case 'video_id':$export_result_str.="{$separator}$video_id";break;
					case 'title':$export_result_str.="{$separator}$res[title]";break;
					case 'directory':$export_result_str.="{$separator}$res[dir]";break;
					case 'description':$export_result_str.="{$separator}".str_replace("\n"," ","$res[description]");break;
					case 'categories':$export_result_str.="{$separator}$res[categories]";break;
					case 'models':$export_result_str.="{$separator}$res[models]";break;
					case 'tags':$export_result_str.="{$separator}$res[tags]";break;
					case 'content_source':$export_result_str.="{$separator}$res[content_source_title]";break;
					case 'content_source/url':$export_result_str.="{$separator}$res[content_source_url]";break;
					case 'dvd':$export_result_str.="{$separator}$res[dvd_title]";break;
					case 'website_link':
						if ($res['dir']<>'')
						{
							$export_result_str.="{$separator}$config[project_url]/".str_replace("%ID%",$video_id,str_replace("%DIR%",$res['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
						} else {
							$export_result_str.="$separator";
						}
						break;
					case 'video_url':
						if ($res['load_type_id']==2 || $res['load_type_id']==3)
						{
							$export_result_str.="{$separator}$res[file_url]";
						} else {
							$export_result_str.="$separator";
						}
						break;
					case 'source_file':
						if (is_file("$config[content_path_videos_sources]/$dir_path/$video_id/$video_id.tmp"))
						{
							$export_result_str.="{$separator}" . get_video_source_url($video_id, "$video_id.tmp");
						} else {
							$export_result_str.="$separator";
						}
						break;
					case 'embed_code':
						if ($res['load_type_id'] == 3)
						{
							$export_result_str .= "{$separator}$res[embed]";
						} elseif ($res['load_type_id'] == 5)
						{
							$export_result_str .= "$separator";
						} else
						{
							$player_data_embed = @unserialize(file_get_contents("$config[project_path]/admin/data/player/embed/config.dat"));
							[$video_width, $video_height] = $res['file_dimensions'];
							if ($res['load_type_id'] == 1)
							{
								$slots = [];
								if ($res['is_private'] == 0 || $res['is_private'] == 1)
								{
									$slots = $player_data_embed['slots'][0];
								} elseif ($res['is_private'] == 2)
								{
									$slots = $player_data_embed['slots'][1];
								}
								if (count($slots) > 0)
								{
									foreach ($slots as $slot)
									{
										foreach ($video_formats as $format_rec)
										{
											if ($slot['type'] == 'redirect' || $slot['type'] != $format_rec['postfix'])
											{
												continue;
											}
											[$video_width, $video_height] = $format_rec['dimensions'];
											break 2;
										}
									}
								}
							}

							$embed_code = '';
							if (intval($_POST['embed_width']) > 0 && intval($_POST['embed_height']) > 0)
							{
								$video_width = intval($_POST['embed_width']);
								$video_height = intval($_POST['embed_height']);
							} elseif (intval($_POST['embed_width']) > 0)
							{
								$video_height = ceil($video_height / $video_width * intval($_POST['embed_width']));
								$video_width = intval($_POST['embed_width']);
							} elseif (intval($_POST['embed_height']) > 0)
							{
								$video_width = ceil($video_width / $video_height * intval($_POST['embed_height']));
								$video_height = intval($_POST['embed_height']);
							} elseif (intval($player_data_embed['embed_size_option']) == 1)
							{
								$ratio = $video_height / $video_width;
								$video_width = intval($player_data_embed['width']);
								if (intval($player_data_embed['height_option']) == 1)
								{
									$video_height = intval($player_data_embed['height']);
								} else
								{
									$video_height = ceil($ratio * $video_width);
								}
							}

							$embed_options = [];
							if (in_array($_POST['embed_skin'], ['black', 'white']))
							{
								$embed_options[] = "skin=$_POST[embed_skin]";
							}
							if (in_array($_POST['embed_autoplay'], ['true', 'false']))
							{
								$embed_options[] = "autoplay=$_POST[embed_autoplay]";
							}

							$embed_url = str_replace('%ID%', $video_id, $_POST['embed_url_pattern'] ?: "$config[project_url]/embed/%ID%");
							if (count($embed_options) > 0)
							{
								$embed_url .= (strpos($embed_url, '?') === false ? '?' : '&amp;') . implode('&amp;', $embed_options);
							}

							$embed_code = "<iframe width=\"$video_width\" height=\"$video_height\" src=\"$embed_url\" frameborder=\"0\" allowfullscreen></iframe>";
							$export_result_str .= "$separator{$embed_code}";
						}
						break;
					case 'gallery_url':
						$export_result_str.="{$separator}$res[gallery_url]";
						break;
					case 'pseudo_url':
						if ($res['load_type_id']==5)
						{
							$export_result_str.="{$separator}$res[pseudo_url]";
						} else {
							$export_result_str.="$separator";
						}
						break;
					case 'duration':
						if ($_POST['duration_format']=='human')
						{
							$export_result_str.="{$separator}".durationToHumanString($res['duration']);
						} else
						{
							$export_result_str.="{$separator}$res[duration]";
						}
						break;
					case 'post_date':$export_result_str.="{$separator}$res[post_date]";break;
					case 'added_date':$export_result_str.="{$separator}$res[added_date]";break;
					case 'rating':$export_result_str.="{$separator}$res[rating]";break;
					case 'rating_percent':$export_result_str.="{$separator}$res[rating_percent]%";break;
					case 'rating_amount':$export_result_str.="{$separator}$res[rating_amount]";break;
					case 'video_viewed':$export_result_str.="{$separator}$res[video_viewed]";break;
					case 'user':$export_result_str.="{$separator}$res[user_title]";break;
					case 'status':$export_result_str.="{$separator}$res[status_title]";break;
					case 'type':$export_result_str.="{$separator}$res[type_title]";break;
					case 'tokens':$export_result_str.="{$separator}$res[tokens_required]";break;
					case 'release_year':
						if ($res['release_year']>0)
						{
							$export_result_str.="{$separator}$res[release_year]";
						} else {
							$export_result_str.="{$separator}";
						}
						break;
					case 'admin_flag':$export_result_str.="{$separator}$res[flag_title]";break;
					case 'custom_1':$export_result_str.="{$separator}".str_replace("\n"," ","$res[custom1]");break;
					case 'custom_2':$export_result_str.="{$separator}".str_replace("\n"," ","$res[custom2]");break;
					case 'custom_3':$export_result_str.="{$separator}".str_replace("\n"," ","$res[custom3]");break;
					case 'screenshot_main_number':
						$export_result_str .= "{$separator}$res[screen_main]";
						break;
					case 'screenshot_main_source':
						$export_result_str .= "{$separator}" . get_video_source_url($video_id, "screenshots/$res[screen_main].jpg");
						break;
					case 'overview_screenshots_sources':
						$export_result_str .= "$separator";
						for ($is = 1; $is <= $res['screen_amount']; $is++)
						{
							$export_result_str .= get_video_source_url($video_id, "screenshots/$is.jpg");
							if ($is < $res['screen_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
					case 'poster_main_number':
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$res[poster_main]";
						}
						break;
					case 'poster_main_source':
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= get_video_source_url($video_id, "posters/$res[poster_main].jpg");
						}
						break;
					case 'posters_sources':
						$export_result_str .= "$separator";
						for ($is = 1; $is <= $res['poster_amount']; $is++)
						{
							$export_result_str .= get_video_source_url($video_id, "posters/$is.jpg");
							if ($is < $res['poster_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
					case 'poster_main_number':
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$res[poster_main]";
						}
						break;
					case 'poster_main_source':
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= get_video_source_url($video_id, "posters/$res[poster_main].jpg");
						}
						break;
					case 'posters_sources':
						$export_result_str .= "$separator";
						for ($is = 1; $is <= $res['poster_amount']; $is++)
						{
							$export_result_str .= get_video_source_url($video_id, "posters/$is.jpg");
							if ($is < $res['poster_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
				}
				foreach ($list_satellites as $satellite)
				{
					if ($_POST["field$i"]=="website_link/{$satellite['multi_prefix']}")
					{
						if ($res['dir']!='' && $satellite['website_ui_data']['WEBSITE_LINK_PATTERN']!='')
						{
							$satellite_dir=$res['dir'];
							if ($satellite['website_ui_data']['locale']!='' && $res['dir_'.$satellite['website_ui_data']['locale']]!='')
							{
								$satellite_dir=$res['dir_'.$satellite['website_ui_data']['locale']];
							}
							$export_result_str.="{$separator}$satellite[project_url]/".str_replace("%ID%",$video_id,str_replace("%DIR%",$satellite_dir,$satellite['website_ui_data']['WEBSITE_LINK_PATTERN']));
						} else {
							$export_result_str.="$separator";
						}
						break;
					}
				}
				foreach ($languages as $language)
				{
					if ($_POST["field$i"]=="title_{$language['code']}")
					{
						$export_result_str.="{$separator}{$res["title_$language[code]"]}";
						break;
					} elseif ($_POST["field$i"]=="description_{$language['code']}")
					{
						$export_result_str.="{$separator}".str_replace("\n"," ","{$res["description_$language[code]"]}");
						break;
					} elseif ($_POST["field$i"]=="directory_{$language['code']}")
					{
						$export_result_str.="{$separator}{$res["dir_$language[code]"]}";
						break;
					}
				}
				foreach ($list_formats_videos as $format_video)
				{
					if ($_POST["field$i"]=="format_video_{$format_video['format_video_id']}")
					{
						$has_format=0;
						foreach ($video_formats as $format_rec)
						{
							if ($format_rec['postfix']==$format_video['postfix'])
							{
								$file_path="$dir_path/$video_id/$video_id{$format_video['postfix']}";
								$hash=md5($config['cv'].$file_path);
								$time=time();
								$export_result_str.="{$separator}$format_rec[file_url]?ttl=$time&dsc=".md5("$config[cv]/$hash/$file_path/$time");
								$has_format=1;
								break;
							}
						}
						if ($has_format==0)
						{
							$export_result_str.="$separator";
						}
					} elseif ($_POST["field$i"]=="hotlink_video_{$format_video['format_video_id']}")
					{
						$has_format=0;
						foreach ($video_formats as $format_rec)
						{
							if ($format_rec['postfix']==$format_video['postfix'])
							{
								$export_result_str.="{$separator}$format_rec[file_url]";
								$has_format=1;
								break;
							}
						}
						if ($has_format==0)
						{
							$export_result_str.="$separator";
						}
					} elseif ($_POST["field$i"]=="dimensions_video_{$format_video['format_video_id']}")
					{
						$has_format=0;
						foreach ($video_formats as $format_rec)
						{
							if ($format_rec['postfix']==$format_video['postfix'])
							{
								$export_result_str.="{$separator}{$format_rec['dimensions'][0]}x{$format_rec['dimensions'][1]}";
								$has_format=1;
								break;
							}
						}
						if ($has_format==0)
						{
							$export_result_str.="$separator";
						}
					} elseif ($_POST["field$i"]=="duration_video_{$format_video['format_video_id']}")
					{
						$has_format=0;
						foreach ($video_formats as $format_rec)
						{
							if ($format_rec['postfix']==$format_video['postfix'])
							{
								if ($_POST['duration_format']=='human')
								{
									$export_result_str.="{$separator}".durationToHumanString($format_rec['duration']);
								} else
								{
									$export_result_str.="{$separator}$format_rec[duration]";
								}
								$has_format=1;
								break;
							}
						}
						if ($has_format==0)
						{
							$export_result_str.="$separator";
						}
					} elseif ($_POST["field$i"]=="filesize_video_{$format_video['format_video_id']}")
					{
						$has_format=0;
						foreach ($video_formats as $format_rec)
						{
							if ($format_rec['postfix']==$format_video['postfix'])
							{
								$export_result_str.="{$separator}$format_rec[file_size]";
								$has_format=1;
								break;
							}
						}
						if ($has_format==0)
						{
							$export_result_str.="$separator";
						}
					}
				}
				foreach ($list_formats_screenshots_overview as $format_screenshot)
				{
					if ($_POST["field$i"]=="screenshot_main_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="{$separator}$config[content_url_videos_screenshots]/$dir_path/$video_id/$format_screenshot[size]/$res[screen_main].jpg";
						break;
					}
					if ($_POST["field$i"]=="overview_screenshots_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="$separator";
						for ($is=1;$is<=$res['screen_amount'];$is++)
						{
							$export_result_str.="$config[content_url_videos_screenshots]/$dir_path/$video_id/$format_screenshot[size]/$is.jpg";
							if ($is<$res['screen_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
					}
					if ($_POST["field$i"]=="overview_screenshots_zip_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str.="{$separator}$config[content_url_videos_screenshots]/$dir_path/$video_id/$format_screenshot[size]/$video_id-$format_screenshot[size].zip";
						break;
					}
				}
				foreach ($list_formats_screenshots_posters as $format_screenshot)
				{
					if ($_POST["field$i"] == "poster_main_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$res[poster_main].jpg";
						}
						break;
					}
					if ($_POST["field$i"] == "posters_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						for ($is = 1; $is <= $res['poster_amount']; $is++)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$is.jpg";
							if ($is < $res['poster_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
					}
					if ($_POST["field$i"] == "posters_zip_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$video_id-$format_screenshot[size].zip";
						}
						break;
					}
				}
				foreach ($list_formats_screenshots_posters as $format_screenshot)
				{
					if ($_POST["field$i"] == "poster_main_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$res[poster_main].jpg";
						}
						break;
					}
					if ($_POST["field$i"] == "posters_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						for ($is = 1; $is <= $res['poster_amount']; $is++)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$is.jpg";
							if ($is < $res['poster_amount'])
							{
								$export_result_str .= ",";
							}
						}
						break;
					}
					if ($_POST["field$i"] == "posters_zip_{$format_screenshot['format_screenshot_id']}")
					{
						$export_result_str .= "$separator";
						if ($res['poster_amount'] > 0)
						{
							$export_result_str .= "$config[content_url_videos_screenshots]/$dir_path/$video_id/posters/$format_screenshot[size]/$video_id-$format_screenshot[size].zip";
						}
						break;
					}
				}
			}

			$export_result_str=substr($export_result_str,strlen($separator));
			file_put_contents($export_filename,"{$export_result_str}$line_separator",FILE_APPEND);
		}

		return_ajax_success("$page_name?action=export_as_file&amp;export_id=$export_id");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_REQUEST['action'] == 'export_as_file')
{
	$export_id = intval($_REQUEST['export_id']);
	if ($export_id > 0)
	{
		$export_date = date("Y-m-d_H-i");
		$export_file = "$config[temporary_path]/export-$export_id.dat";
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=\"export_data_$export_date.txt\"");
		if (is_file($export_file) && filesize($export_file) > 0)
		{
			header("Content-Length: " . filesize($export_file));
			readfile($export_file);
		} else
		{
			header("Content-Length: " . strlen($lang['videos']['export_result_no_data']));
			echo $lang['videos']['export_result_no_data'];
		}
	}
	die;
}

$smarty=new mysmarty();
$smarty->assign('left_menu','menu_videos.tpl');
$smarty->assign('options',$options);
$smarty->assign('list_formats_videos',$list_formats_videos);
$smarty->assign('list_formats_screenshots_overview',$list_formats_screenshots_overview);
$smarty->assign('list_formats_screenshots_posters',$list_formats_screenshots_posters);
$smarty->assign('list_satellites',$list_satellites);
$smarty->assign('list_flags_admins',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('list_presets',$VIDEOS_EXPORT_PRESETS);
$smarty->assign('list_languages',$languages);

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('video_page_name','videos.php');
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

$smarty->assign('page_title',$lang['videos']['export_header_export']);

$content_scheduler_days=intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days='';
	$sorting_content_scheduler_days='desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$now_date=date("Y-m-d H:i:s");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days='asc';
	}
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
