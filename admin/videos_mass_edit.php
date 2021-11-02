<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$table_name="$config[tables_prefix]videos";

$errors = null;

$mass_edit_id=intval($_REQUEST['edit_id']);

if ($_REQUEST['action']=='progress')
{
	$pc=intval(@file_get_contents("$config[temporary_path]/mass-edit-progress-$mass_edit_id.dat"));
	header("Content-Type: text/xml");

	if ($pc==100)
	{
		$location="<location>videos.php</location>";
		@unlink("$config[temporary_path]/mass-edit-progress-$mass_edit_id.dat");
		$_SESSION['messages'][]=$lang['videos']['success_message_objects_updated'];
	}
	echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
}

if ($mass_edit_id < 1 || !is_file("$config[temporary_path]/mass-edit-$mass_edit_id.dat")) {header("Location: videos.php");die;}
$data=@unserialize(file_get_contents("$config[temporary_path]/mass-edit-$mass_edit_id.dat"));
if (!is_array($data)) {header("Location: videos.php");die;}

if (intval($data['all'])==0)
{
	$ids_str=implode(",",$data['ids']);
	if ($ids_str=='')
	{
		$ids_str='0';
	}
	$all_str='';
} else {
	$ids_str='0';
	$all_str=' or 1=1 ';
}

if ($_POST['action']=='change_complete')
{
	$admin_user_ids=$_POST['admin_user_ids'];
	$category_ids_add=$_POST['category_ids_add'];
	$category_ids_delete=$_POST['category_ids_delete'];
	$model_ids_add=$_POST['model_ids_add'];
	$model_ids_delete=$_POST['model_ids_delete'];
	$video_format_create_ids=$_POST['video_format_create_ids'];
	$video_format_delete_ids=$_POST['video_format_delete_ids'];
	$screenshot_format_recreate_ids=$_POST['screenshot_format_recreate_ids'];
	if ($_POST['tags_add']<>'')
	{
		$tags_add=explode(",",$_POST['tags_add']);
	}
	if ($_POST['tags_delete']<>'')
	{
		$tags_delete=explode(",",$_POST['tags_delete']);
	}
	$post_process_plugins=$_POST['post_process_plugins'];

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if ($_POST['tokens_required']<>'' && $_POST['tokens_required']<>'0')
	{
		validate_field('empty_int',$_POST['tokens_required'],$lang['videos']['mass_edit_videos_field_tokens_cost']);
	}
	if ($_POST['release_year']<>'')
	{
		validate_field('empty_int',$_POST['release_year'],$lang['videos']['mass_edit_videos_field_release_year']);
	}
	if ($_POST['dvd']!='')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where title=?",$_POST['dvd']))==0)
		{
			$errors[]=get_aa_error('invalid_dvd',$lang['videos']['mass_edit_videos_field_dvd']);
		}
	}

	if (intval($_POST['post_date_option'])==0)
	{
		$change_from=0;
		$change_to=0;
		if (intval($_POST["post_date_from_Year"])>0 && intval($_POST["post_date_from_Month"])>0 && intval($_POST["post_date_from_Day"])>0) {$change_from=1;}
		if (intval($_POST["post_date_to_Year"])>0 && intval($_POST["post_date_to_Month"])>0 && intval($_POST["post_date_to_Day"])>0) {$change_to=1;}
		if ($change_from+$change_to==1)
		{
			$errors[]=get_aa_error('invalid_date_range',$lang['videos']['mass_edit_videos_field_post_date']);
		} elseif ($change_from+$change_to==2)
		{
			$post_date_from=strtotime(intval($_POST["post_date_from_Year"])."-".intval($_POST["post_date_from_Month"])."-".intval($_POST["post_date_from_Day"]));
			$post_date_to=strtotime(intval($_POST["post_date_to_Year"])."-".intval($_POST["post_date_to_Month"])."-".intval($_POST["post_date_to_Day"]));
			if ($post_date_from>$post_date_to)
			{
				$errors[]=get_aa_error('invalid_date_range',$lang['videos']['mass_edit_videos_field_post_date']);
			}
		}
	} else {
		$has_relative_post_date_error=0;
		$change_from=intval($_POST['relative_post_date_from']);
		$change_to=intval($_POST['relative_post_date_to']);
		if ($_POST['relative_post_date_from']<>'' && $_POST['relative_post_date_from']<>'0' && $has_relative_post_date_error==0)
		{
			if (!validate_field('empty_int_ext',$_POST['relative_post_date_from'],$lang['videos']['mass_edit_videos_field_post_date']))
			{
				$has_relative_post_date_error=1;
			}
		}
		if ($_POST['relative_post_date_to']<>'' && $_POST['relative_post_date_to']<>'0' && $has_relative_post_date_error==0)
		{
			if (!validate_field('empty_int_ext',$_POST['relative_post_date_to'],$lang['videos']['mass_edit_videos_field_post_date']))
			{
				$has_relative_post_date_error=1;
			}
		}
		if ($has_relative_post_date_error==0)
		{
			if ($change_to<$change_from)
			{
				$errors[]=get_aa_error('invalid_int_range',$lang['videos']['mass_edit_videos_field_post_date']);
				$has_relative_post_date_error=1;
			}
		}
	}
	if (intval($_POST['post_time_change'])==1)
	{
		$post_time_from=array(0,0);
		if (strpos($_POST['post_time_from'],":")!==false)
		{
			$temp=explode(":",$_POST['post_time_from']);
			if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_from[0]=$temp[0];}
			if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_from[1]=$temp[1];}
		}
		$post_time_from=$post_time_from[0]*3600+$post_time_from[1]*60;

		$post_time_to=array(0,0);
		if (strpos($_POST['post_time_to'],":")!==false)
		{
			$temp=explode(":",$_POST['post_time_to']);
			if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_to[0]=$temp[0];}
			if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_to[1]=$temp[1];}
		}
		$post_time_to=$post_time_to[0]*3600+$post_time_to[1]*60;

		if ($post_time_from>$post_time_to)
		{
			$errors[]=get_aa_error('invalid_time_range',$lang['videos']['mass_edit_videos_field_post_time']);
		}
	}
	if ($_POST['rating_min']<>'' || $_POST['rating_max']<>'')
	{
		$has_rating_error=0;
		$rating_min=floatval($_POST['rating_min']);
		$rating_max=floatval($_POST['rating_max']);
		$votes_min=intval($_POST['rating_amount_min']);
		$votes_max=intval($_POST['rating_amount_max']);
		if ($_POST['rating_min']<>'' && $_POST['rating_min']<>'0' && $has_rating_error==0)
		{
			if (!validate_field('empty_float',$_POST['rating_min'],$lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error=1;
			} else {
				if ($rating_min<0 || $rating_min>10)
				{
					$errors[]=get_aa_error('invalid_rating',$lang['videos']['mass_edit_videos_field_rating']);
					$has_rating_error=1;
				}
			}
		}
		if ($_POST['rating_max']<>'' && $_POST['rating_max']<>'0' && $has_rating_error==0)
		{
			if (!validate_field('empty_float',$_POST['rating_max'],$lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error=1;
			} else {
				if ($rating_max<0 || $rating_max>10)
				{
					$errors[]=get_aa_error('invalid_rating',$lang['videos']['mass_edit_videos_field_rating']);
					$has_rating_error=1;
				}
			}
		}
		if ($has_rating_error==0)
		{
			if ($rating_max<$rating_min)
			{
				$errors[]=get_aa_error('invalid_int_range',$lang['videos']['mass_edit_videos_field_rating']);
				$has_rating_error=1;
			}
		}
		if ($has_rating_error==0)
		{
			if (!validate_field('empty_int',$_POST['rating_amount_min'],$lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error=1;
			}
		}
		if ($has_rating_error==0)
		{
			if (!validate_field('empty_int',$_POST['rating_amount_max'],$lang['videos']['mass_edit_videos_field_rating']))
			{
				$has_rating_error=1;
			}
		}
		if ($has_rating_error==0)
		{
			if ($votes_max<$votes_min)
			{
				$errors[]=get_aa_error('invalid_int_range',$lang['videos']['mass_edit_videos_field_rating']);
				$has_rating_error=1;
			}
		}
	}
	if ($_POST['visits_min']<>'' || $_POST['visits_max']<>'')
	{
		$has_visits_error=0;
		$visits_min=intval($_POST['visits_min']);
		$visits_max=intval($_POST['visits_max']);
		if ($_POST['visits_min']<>'' && $_POST['visits_min']<>'0' && $has_visits_error==0)
		{
			if (!validate_field('empty_int',$_POST['visits_min'],$lang['videos']['mass_edit_videos_field_visits']))
			{
				$has_visits_error=1;
			}
		}
		if ($_POST['visits_max']<>'' && $_POST['visits_max']<>'0' && $has_visits_error==0)
		{
			if (!validate_field('empty_int',$_POST['visits_max'],$lang['videos']['mass_edit_videos_field_visits']))
			{
				$has_visits_error=1;
			}
		}
		if ($has_visits_error==0)
		{
			if ($visits_max<$visits_min)
			{
				$errors[]=get_aa_error('invalid_int_range',$lang['videos']['mass_edit_videos_field_visits']);
				$has_visits_error=1;
			}
		}
	}
	if ($_POST['video_format_upload_id']!='')
	{
		if (validate_field('file','video_format_upload_file',$lang['videos']['mass_edit_videos_field_format_video_upload'],array('is_required'=>1)))
		{
			if (get_video_duration("$config[temporary_path]/$_POST[video_format_upload_file_hash].tmp")<1)
			{
				$errors[]=get_aa_error('invalid_video_file',$lang['videos']['mass_edit_videos_field_format_video_upload']);
			}
		}
	}
	if ($_POST['new_storage_group_id']<>'')
	{
		$background_tasks=mr2array(sql("select video_id from $config[tables_prefix]background_tasks where type_id=15"));
		if (intval($data['all'])==0)
		{
			foreach ($background_tasks as $task)
			{
				$video_id=intval($task['video_id']);
				if (in_array($video_id,$data['ids']))
				{
					$errors[]=get_aa_error('videos_mass_edit_migration');
					break;
				}
			}
		} else {
			if (count($background_tasks)>0)
			{
				$errors[]=get_aa_error('videos_mass_edit_migration');
			}
		}
	}

	if (!is_array($errors))
	{
		$needs_editing=0;
		if (in_array('system|administration',$_SESSION['permissions']))
		{
			if (@count($admin_user_ids)>0)
			{
				$data['admin_user_ids']=$admin_user_ids;
				$needs_editing=1;
			}
		}
		if (intval($_POST['regenerate_directories'])==1)
		{
			$lang_codes=mr2array_list(sql("select code from $config[tables_prefix]languages"));
			if ($_POST['regenerate_directories_language']=='' || in_array($_POST['regenerate_directories_language'],$lang_codes))
			{
				$data['regenerate_directories']=1;
				$data['regenerate_directories_language']=$_POST['regenerate_directories_language'];
				$needs_editing=1;
			}
		}
		if ($_POST['status_id']<>'')
		{
			$data['status_id']=$_POST['status_id'];
			$needs_editing=1;
		}
		if ($_POST['is_private']<>'')
		{
			$data['is_private']=$_POST['is_private'];
			$needs_editing=1;
		}
		if ($_POST['access_level_id']<>'')
		{
			$data['access_level_id']=$_POST['access_level_id'];
			$needs_editing=1;
		}
		if ($_POST['tokens_required']<>'')
		{
			$data['tokens_required']=$_POST['tokens_required'];
			$needs_editing=1;
		}
		if ($_POST['release_year']<>'')
		{
			$data['release_year']=$_POST['release_year'];
			$needs_editing=1;
		}
		if ($_POST['users']<>'')
		{
			$data['users']=$_POST['users'];
			$needs_editing=1;
		}
		if ($_POST['content_source_id']<>'')
		{
			$data['content_source_id']=$_POST['content_source_id'];
			$needs_editing=1;
		}
		if ($_POST['dvd']<>'')
		{
			$dvd_id=mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?",$_POST['dvd']));
			if ($dvd_id>0)
			{
				$data['dvd_id']=$dvd_id;
				$needs_editing=1;
			}
		}
		if ($_POST['admin_flag_id']<>'')
		{
			$data['admin_flag_id']=$_POST['admin_flag_id'];
			$needs_editing=1;
		}
		if ($_POST['is_locked']<>'')
		{
			$data['is_locked']=$_POST['is_locked'];
			$needs_editing=1;
		}
		if ($_POST['is_review_needed']<>'')
		{
			$data['is_review_needed']=$_POST['is_review_needed'];
			$needs_editing=1;
		}
		if (intval($_POST['post_date_option'])==0)
		{
			if ($change_from+$change_to==2)
			{
				$data['post_date_from']=intval($_POST["post_date_from_Year"])."-".intval($_POST["post_date_from_Month"])."-".intval($_POST["post_date_from_Day"]);
				$data['post_date_to']=intval($_POST["post_date_to_Year"])."-".intval($_POST["post_date_to_Month"])."-".intval($_POST["post_date_to_Day"]);
				$data['change_post_date_fixed']=1;
				$needs_editing=1;
			}
		} else {
			if ($change_from<>0 || $change_to<>0)
			{
				$data['relative_post_date_from']=$change_from;
				$data['relative_post_date_to']=$change_to;
				$data['change_post_date_relative']=1;
				$needs_editing=1;
			}
		}
		if (intval($_POST['post_time_change'])==1)
		{
			$data['post_time_change']=1;
			$data['post_time_from']=$post_time_from;
			$data['post_time_to']=$post_time_to;
			$needs_editing=1;
		}
		if (isset($rating_min, $rating_max))
		{
			$data['rating_min']=$rating_min;
			$data['rating_max']=$rating_max;
			$data['rating_amount_min']=$votes_min;
			$data['rating_amount_max']=$votes_max;
			$data['change_rating']=1;
			$needs_editing=1;
		}
		if (isset($visits_min, $visits_max))
		{
			$data['visits_min']=$visits_min;
			$data['visits_max']=$visits_max;
			$data['change_visits']=1;
			$needs_editing=1;
		}

		if (@count($category_ids_add)>0)
		{
			$data['category_ids_add']=$category_ids_add;
			$needs_editing=1;
		}
		if (@count($category_ids_delete)>0)
		{
			$data['category_ids_delete']=$category_ids_delete;
			$needs_editing=1;
		}
		if (@count($model_ids_add)>0)
		{
			$data['model_ids_add']=$model_ids_add;
			$needs_editing=1;
		}
		if (@count($model_ids_delete)>0)
		{
			$data['model_ids_delete']=$model_ids_delete;
			$needs_editing=1;
		}
		if (@count($tags_add)>0)
		{
			$data['tags_add']=$tags_add;
			$needs_editing=1;
		}
		if (@count($tags_delete)>0)
		{
			$data['tags_delete']=$tags_delete;
			$needs_editing=1;
		}
		if ($_POST['flag_id']<>'')
		{
			$data['flag_id']=$_POST['flag_id'];
			$needs_editing=1;
		}
		if (intval($_POST['delete_source_files'])==1)
		{
			$data['delete_source_files']=1;
			$needs_editing=1;
		}
		if (@count($video_format_create_ids)>0)
		{
			$data['video_format_create_ids']=$video_format_create_ids;
			$data['video_format_create_disable_wm']=intval($_POST['video_format_create_disable_wm']);
			$needs_editing=1;
		}
		if ($_POST['video_format_upload_id']<>'')
		{
			$data['video_format_upload_id']=$_POST['video_format_upload_id'];
			$data['video_format_upload_file']="$config[temporary_path]/$_POST[video_format_upload_file_hash].tmp";
			$needs_editing=1;
		}
		if (@count($video_format_delete_ids)>0)
		{
			$data['video_format_delete_ids']=$video_format_delete_ids;
			$needs_editing=1;
		}
		if ($_POST['video_format_duration_id']<>'')
		{
			$data['video_format_duration_id']=$_POST['video_format_duration_id'];
			$needs_editing=1;
		}
		if ($_POST['new_storage_group_id']<>'')
		{
			$data['new_storage_group_id']=$_POST['new_storage_group_id'];
			$needs_editing=1;
		}
		if (intval($_POST['recreate_overview_screenshots'])==1)
		{
			$data['recreate_overview_screenshots']=1;
			$needs_editing=1;
		}
		if (intval($_POST['delete_overview_screenshots'])==1)
		{
			$data['delete_overview_screenshots']=1;
			$needs_editing=1;
		}
		if (@count($screenshot_format_recreate_ids)>0)
		{
			$data['screenshot_format_recreate_ids']=$screenshot_format_recreate_ids;
			$needs_editing=1;
		}
		if (intval($_POST['rotator_reset_main_stats'])==1)
		{
			$data['rotator_reset_main_stats']=1;
			$needs_editing=1;
		}
		if (intval($_POST['rotator_reset_screenshots_stats'])==1)
		{
			$data['rotator_reset_screenshots_stats']=1;
			$needs_editing=1;
		}
		if (@count($post_process_plugins)>0)
		{
			$data['post_process_plugins']=$post_process_plugins;
			$needs_editing=1;
		}

		if ($needs_editing==1)
		{
			file_put_contents("$config[temporary_path]/mass-edit-$mass_edit_id.dat",serialize($data),LOCK_EX);

			$admin_id=$_SESSION['userdata']['user_id'];
			$is_access_to_own_content=intval($_SESSION['userdata']['is_access_to_own_content']);
			$is_access_to_disabled_content=intval($_SESSION['userdata']['is_access_to_disabled_content']);
			$is_access_to_content_flagged_with='0';
			if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
			{
				$is_access_to_content_flagged_with = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
			}

			$task_id=sql_insert("insert into $config[tables_prefix]background_tasks set status_id=1, type_id=52, added_date=?, start_date=?",date("Y-m-d H:i:s"),date("Y-m-d H:i:s"));

			exec("$config[php_path] $config[project_path]/admin/background_mass_edit.php $mass_edit_id $admin_id $is_access_to_own_content $is_access_to_disabled_content $is_access_to_content_flagged_with $task_id > $config[project_path]/admin/logs/tasks/$task_id.txt 2>&1 &");
			return_ajax_success("$page_name?action=progress&amp;edit_id=$mass_edit_id&amp;rand=\${rand}",2);
		} else {
			@unlink("$config[temporary_path]/mass-edit-$mass_edit_id.dat");
			$_SESSION['messages'][]=$lang['videos']['success_message_objects_updated'];
			return_ajax_success("videos.php");
		}
	} else {
		return_ajax_errors($errors);
	}
}

$list_content_sources=array();
$temp=mr2array(sql("select content_source_id, $config[tables_prefix]content_sources_groups.content_source_group_id, $config[tables_prefix]content_sources.title, $config[tables_prefix]content_sources_groups.title as content_source_group_title from $config[tables_prefix]content_sources left join $config[tables_prefix]content_sources_groups on $config[tables_prefix]content_sources_groups.content_source_group_id=$config[tables_prefix]content_sources.content_source_group_id order by $config[tables_prefix]content_sources_groups.title asc,$config[tables_prefix]content_sources.title asc"));
foreach ($temp as $res)
{
	$list_content_sources[$res['content_source_group_id']][]=$res;
}

$list_formats_videos_create=array();
$temp=mr2array(sql("select format_video_id, postfix, title, video_type_id from $config[tables_prefix]formats_videos where status_id in (1,2) order by title asc"));
foreach ($temp as $res)
{
	if (is_file("$config[project_path]/admin/data/other/watermark_video_{$res['format_video_id']}.png") || is_file("$config[project_path]/admin/data/other/watermark2_video_{$res['format_video_id']}.png"))
	{
		$res['has_watermark']=1;
	}
	$list_formats_videos_create[$res['video_type_id']][]=$res;
}

$list_formats_videos_delete=array();
$temp=mr2array(sql("select postfix, title, video_type_id from $config[tables_prefix]formats_videos where status_id in (0,2) order by title asc"));
foreach ($temp as $res)
{
	$list_formats_videos_delete[$res['video_type_id']][]=$res;
}

$list_formats_videos_duration=array();
$temp=mr2array(sql("select format_video_id, postfix, title, video_type_id from $config[tables_prefix]formats_videos where status_id in (1) order by title asc"));
foreach ($temp as $res)
{
	$list_formats_videos_duration[$res['video_type_id']][]=$res;
}

$list_server_groups=mr2array(sql("select * from (select group_id, title, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=1) x where free_space>0 order by title asc"));
foreach ($list_server_groups as $k=>$v)
{
	$list_server_groups[$k]['free_space']=sizeToHumanString($v['free_space'],2);
	$list_server_groups[$k]['total_space']=sizeToHumanString($v['total_space'],2);
}

$list_formats_screenshots_overview = [];
$list_formats_screenshots_timeline = [];
$list_formats_screenshots_posters = [];
$temp = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 order by title asc"));
foreach ($temp as $res)
{
	if ($res['group_id'] == 1)
	{
		$list_formats_screenshots_overview[] = $res;
	} elseif ($res['group_id'] == 2)
	{
		$list_formats_screenshots_timeline[] = $res;
	} elseif ($res['group_id'] == 3)
	{
		$list_formats_screenshots_posters[] = $res;
	}
}

$plugins_list=get_contents_from_dir("$config[project_path]/admin/plugins",2);
sort($plugins_list);
$list_post_process_plugins=array();
foreach ($plugins_list as $k=>$v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
	{
		continue;
	}
	$file_data=file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
	preg_match("|<plugin_types>(.*?)</plugin_types>|is",$file_data,$temp_find);
	$plugin_types=explode(',',trim($temp_find[1]));
	$is_process_plugin=0;
	foreach ($plugin_types as $type)
	{
		if ($type=='process_object')
		{
			$is_process_plugin=1;
		}
	}

	if ($is_process_plugin==1)
	{
		require_once("$config[project_path]/admin/plugins/$v/$v.php");
		$process_plugin_function="{$v}IsEnabled";
		if (function_exists($process_plugin_function))
		{
			if ($process_plugin_function())
			{
				if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
				{
					require_once("$config[project_path]/admin/plugins/$v/langs/english.php");
				}
				if (($_SESSION['userdata']['lang']!='english') && (is_file("$config[project_path]/admin/plugins/$v/langs/".$_SESSION['userdata']['lang'].".php")))
				{
					require_once("$config[project_path]/admin/plugins/$v/langs/".$_SESSION['userdata']['lang'].".php");
				}
				$list_post_process_plugins[]=array('plugin_id'=>$v,'title'=>$lang['plugins'][$v]['title']);
			}
		}
	}
}

$smarty=new mysmarty();
$smarty->assign('left_menu','menu_videos.tpl');
$smarty->assign('list_content_sources',$list_content_sources);
$smarty->assign('list_formats_videos_create',$list_formats_videos_create);
$smarty->assign('list_formats_videos_delete',$list_formats_videos_delete);
$smarty->assign('list_formats_videos_duration',$list_formats_videos_duration);
$smarty->assign('list_formats_screenshots_overview',$list_formats_screenshots_overview);
$smarty->assign('list_formats_screenshots_timeline',$list_formats_screenshots_timeline);
$smarty->assign('list_formats_screenshots_posters',$list_formats_screenshots_posters);
$smarty->assign('list_flags_videos',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 order by title asc")));
$smarty->assign('list_flags_admins',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('list_server_groups',$list_server_groups);
$smarty->assign('list_post_process_plugins',$list_post_process_plugins);
$smarty->assign('list_languages',mr2array(sql("select * from $config[tables_prefix]languages")));

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'],'%ID%')===false)
{
	$smarty->assign('disallow_directory_change',1);
}

unset($where);
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

$videos_count=mr2number(sql("select count(*) from $table_name where status_id in (0,1) and (video_id in ($ids_str) $all_str) $where"));
$all_videos_count=mr2number(sql("select count(*) from $table_name where status_id in (0,1) $where"));
$smarty->assign('videos_count',$videos_count);
if ($videos_count==$all_videos_count)
{
	$smarty->assign('videos_count_all',1);
}

$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

$smarty->assign('page_title',$lang['videos']['mass_edit_videos_header']);

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
