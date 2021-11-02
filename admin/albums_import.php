<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');
require_once('include/pclzip.lib.php');

$table_name="$config[tables_prefix]albums";
$table_key_name="album_id";

$errors = null;

if ($_REQUEST['action']=='progress' || $_REQUEST['action']=='progress2')
{
	$import_id=intval($_REQUEST['import_id']);
	$pc=intval(@file_get_contents("$config[temporary_path]/import-progress-$import_id.dat"));
	header("Content-Type: text/xml");

	if ($pc==100)
	{
		if ($_REQUEST['action']=='progress')
		{
			$location="<location>$page_name?action=import_start&amp;import_id=$import_id</location>";
		} else {
			$location="<location>albums.php</location>";
		}
		@unlink("$config[temporary_path]/import-progress-$import_id.dat");
	}
	echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
}

$options=get_options();
if ($options['ALBUM_FIELD_1_NAME']=='') {$options['ALBUM_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['ALBUM_FIELD_2_NAME']=='') {$options['ALBUM_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['ALBUM_FIELD_3_NAME']=='') {$options['ALBUM_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}

if ($options['ALBUMS_IMPORT_PRESETS']<>'')
{
	$ALBUMS_IMPORT_PRESETS=@unserialize($options['ALBUMS_IMPORT_PRESETS']);
}
if ($_POST['preset_id']<>'' && $_POST['preset_name']=='' && $_POST['action']=='start_import')
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
	$post_date_randomization_from=intval($_POST["post_date_randomization_from_Year"])."-".intval($_POST["post_date_randomization_from_Month"])."-".intval($_POST["post_date_randomization_from_Day"]);
	$post_date_randomization_to=intval($_POST["post_date_randomization_to_Year"])."-".intval($_POST["post_date_randomization_to_Month"])."-".intval($_POST["post_date_randomization_to_Day"]);
	$_POST['post_date_randomization_from']=$post_date_randomization_from;
	$_POST['post_date_randomization_to']=$post_date_randomization_to;

	$temp_data=$_POST;
	unset($temp_data['action']);
	unset($temp_data['data']);
	unset($temp_data['file']);
	unset($temp_data['file_hash']);
	$ALBUMS_IMPORT_PRESETS[$name]=$temp_data;

	if ($temp_data['is_default_preset']==1)
	{
		foreach ($ALBUMS_IMPORT_PRESETS as $k=>$preset)
		{
			if ($k<>$name && $preset['is_default_preset']==1)
			{
				$ALBUMS_IMPORT_PRESETS[$k]['is_default_preset']=0;
			}
		}
	}

	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_IMPORT_PRESETS'",serialize($ALBUMS_IMPORT_PRESETS));
}
if ($_GET['action']!='back_import' && !isset($_GET['preset_id']) && count($_POST)==0 && is_array($ALBUMS_IMPORT_PRESETS))
{
	foreach ($ALBUMS_IMPORT_PRESETS as $k=>$preset)
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
	unset($ALBUMS_IMPORT_PRESETS[$_POST['preset_id']]);
	sql_pr("update $config[tables_prefix]options set value=? where variable='ALBUMS_IMPORT_PRESETS'",serialize($ALBUMS_IMPORT_PRESETS));

	$_SESSION['messages'][]=$lang['albums']['success_message_import_export_preset_removed'];
	return_ajax_success("$page_name");
} elseif (isset($_GET['preset_id']))
{
	$_POST=$ALBUMS_IMPORT_PRESETS[$_GET['preset_id']];
}

if ($_POST['action']=='start_import')
{
	$is_post_date_randomization=intval($_POST['is_post_date_randomization']);
	$is_post_date_randomization_days=intval($_POST['is_post_date_randomization_days']);
	$is_post_time_randomization=intval($_POST['is_post_time_randomization']);
	$is_make_directories=intval($_POST['is_make_directories']);
	$separator=$_POST['separator'];
	$line_separator=$_POST['line_separator'];

	$separator=str_replace("\\r","\r",$separator);
	$separator=str_replace("\\n","\n",$separator);
	$separator=str_replace("\\t","\t",$separator);
	$_POST['separator_modified']=$separator;

	$line_separator=str_replace("\\r","\r",$line_separator);
	$line_separator=str_replace("\\n","\n",$line_separator);
	$line_separator=str_replace("\\t","\t",$line_separator);
	$_POST['line_separator_modified']=$line_separator;

	for ($i=1;$i<=999;$i++)
	{
		if (!isset($_POST["field$i"]))
		{
			$fields_amount=$i-1;break;
		}
	}
	$_POST['fields_amount']=$fields_amount;
	$fields_selected_amount=0;
	for ($i=1;$i<=$fields_amount;$i++)
	{
		if ($_POST["field$i"]<>'')
		{
			$fields_selected_amount++;
		}
	}

	if ($_POST["file_hash"]=='' && $_POST["data"]=='')
	{
		validate_field('empty',"",$lang['albums']['import_field_data_text']);
	}

	validate_field('empty',$_POST['separator'],$lang['albums']['import_export_field_separator_fields']);
	validate_field('empty',$_POST['line_separator'],$lang['albums']['import_export_field_separator_lines']);

	$import_fiels_list=array();
	$is_error=1;
	for ($i=1;$i<=$fields_amount;$i++)
	{
		if ($_POST["field$i"]<>'')
		{
			$is_error=0;
			if (trim($_POST["field$i"])<>'skip')
			{
				$import_fiels_list[]=trim($_POST["field$i"]);
			}
		}
	}
	$is_field_error=0;
	if ($is_error) {$errors[]=get_aa_error('import_fields_required',$lang['albums']['import_divider_fields']);$is_field_error=1;}

	if ($is_field_error==0 && count($import_fiels_list)<>count(array_unique($import_fiels_list)))
	{
		$errors[]=get_aa_error('import_fields_duplication',$lang['albums']['import_divider_fields']);
		$is_field_error=1;
	}
	if (!is_array($errors))
	{
		$is_fields_selected=1;
	}

	if (!in_array("user",$import_fiels_list))
	{
		$has_users=0;
		$temp=explode(",",$_POST['users']);
		if (is_array($temp))
		{
			$temp=array_map("trim",$temp);
			$temp=array_unique($temp);
			foreach ($temp as $user)
			{
				if (strlen($user)>0)
				{
					$has_users=1;
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=? and status_id not in (1)",$user))==0)
					{
						$errors[]=get_aa_error('import_users',$lang['albums']['import_field_users'],$user);
					}
				}
			}
		}
		if ($has_users==0)
		{
			$errors[]=get_aa_error('import_users_required',$lang['albums']['import_field_users']);
		}
	}

	if ($is_make_directories<>1 && !in_array("directory",$import_fiels_list))
	{
		$errors[]=get_aa_error('import_directory_from_title',$lang['albums']['import_field_options']);
	}

	if ($is_post_time_randomization==1)
	{
		$post_time_from=array(0,0);
		if (strpos($_POST['post_time_randomization_from'],":")!==false)
		{
			$temp=explode(":",$_POST['post_time_randomization_from']);
			if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_from[0]=$temp[0];}
			if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_from[1]=$temp[1];}
		}
		$post_time_from=$post_time_from[0]*3600+$post_time_from[1]*60;

		$post_time_to=array(0,0);
		if (strpos($_POST['post_time_randomization_to'],":")!==false)
		{
			$temp=explode(":",$_POST['post_time_randomization_to']);
			if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_to[0]=$temp[0];}
			if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_to[1]=$temp[1];}
		}
		$post_time_to=$post_time_to[0]*3600+$post_time_to[1]*60;

		if ($post_time_from>$post_time_to)
		{
			$errors[]=get_aa_error('invalid_time_range',$lang['albums']['import_field_post_date']);
		}
	}

	if ($is_post_date_randomization==1)
	{
		if (intval($_POST['post_date_randomization_option'])==0)
		{
			$post_date_randomization_from=intval($_POST["post_date_randomization_from_Year"])."-".intval($_POST["post_date_randomization_from_Month"])."-".intval($_POST["post_date_randomization_from_Day"]);
			$post_date_randomization_to=intval($_POST["post_date_randomization_to_Year"])."-".intval($_POST["post_date_randomization_to_Month"])."-".intval($_POST["post_date_randomization_to_Day"]);
			$_POST['post_date_randomization_from']=$post_date_randomization_from;
			$_POST['post_date_randomization_to']=$post_date_randomization_to;

			if (intval($_POST["post_date_randomization_from_Year"])<1 ||intval($_POST["post_date_randomization_from_Month"])<1 ||intval($_POST["post_date_randomization_from_Day"])<1 ){validate_field('empty',"",$lang['albums']['import_field_post_date']);} else
			if (intval($_POST["post_date_randomization_to_Year"])<1 ||intval($_POST["post_date_randomization_to_Month"])<1 ||intval($_POST["post_date_randomization_to_Day"])<1 ){validate_field('empty',"",$lang['albums']['import_field_post_date']);} else
			if (strtotime($post_date_randomization_from)>strtotime($post_date_randomization_to))
			{
				$errors[]=get_aa_error('invalid_date_range',$lang['albums']['import_field_post_date']);
			}
		} else {
			if (validate_field('empty_int_ext',$_POST['relative_post_date_randomization_from'],$lang['albums']['import_field_post_date']))
			{
				if (validate_field('empty_int_ext',$_POST['relative_post_date_randomization_to'],$lang['albums']['import_field_post_date']))
				{
					$post_date_randomization_from=intval($_POST['relative_post_date_randomization_from']);
					$post_date_randomization_to=intval($_POST['relative_post_date_randomization_to']);
					if ($post_date_randomization_to<$post_date_randomization_from)
					{
						$errors[]=get_aa_error('invalid_int_range',$lang['albums']['import_field_post_date']);
					}
				}
			}
		}
	} elseif ($is_post_date_randomization_days==1)
	{
		validate_field('empty_int',$_POST['post_date_randomization_days'],$lang['albums']['import_field_post_date']);
	}

	if ($_POST["file_hash"]<>'' || $_POST["data"]<>'')
	{
		if ($_POST['data']<>'')
		{
			$import_data=$_POST['data'];
		} else {
			if (preg_match('/^([0-9A-Za-z]{32})$/',$_POST['file_hash'])) {
				$import_data=file_get_contents("$config[temporary_path]/$_POST[file_hash].tmp");
			}
		}

		if ($_POST['separator']=='\n' || $_POST['separator']=='\r\n')
		{
			if ($_POST['separator']=='\n' && count(explode("\r\n",$import_data))>count(explode("\n",$import_data)))
			{
				$separator="\r\n";
			} elseif ($_POST['separator']=='\r\n' && count(explode("\n",$import_data))>count(explode("\r\n",$import_data)))
			{
				$separator="\n";
			}
			$separator=str_replace("\\r","\r",$separator);
			$separator=str_replace("\\n","\n",$separator);
			$_POST['separator_modified']=$separator;
		}

		if ($_POST['line_separator']=='\n' || $_POST['line_separator']=='\r\n')
		{
			if ($_POST['line_separator']=='\n' && count(explode("\r\n",$import_data))>count(explode("\n",$import_data)))
			{
				$line_separator="\r\n";
			} elseif ($_POST['line_separator']=='\r\n' && count(explode("\n",$import_data))>count(explode("\r\n",$import_data)))
			{
				$line_separator="\n";
			}
			$line_separator=str_replace("\\r","\r",$line_separator);
			$line_separator=str_replace("\\n","\n",$line_separator);
			$_POST['line_separator_modified']=$line_separator;
		}

		if ($is_fields_selected>0)
		{
			$temp=explode($line_separator,$import_data);
			$lines_with_error=null;
			$line=0;
			foreach ($temp as $res)
			{
				$line++;
				if (trim($res)=='') {continue;}
				if (function_exists('str_getcsv') && strlen($separator)==1)
				{
					$temp_line=str_getcsv($res,$separator);
				} else {
					$temp_line=explode($separator,$res);
				}
				if (count($temp_line)<>$fields_selected_amount)
				{
				   $lines_with_error[]=$line;
				}
			}
			if (is_array($lines_with_error))
			{
				$errors[]=get_aa_error('import_fields_in_line',implode(", ",$lines_with_error));
			}
		}
	}

	if (!is_array($errors))
	{
		$rnd=mt_rand(10000000,99999999);
		for ($i=0;$i<999;$i++)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports where import_id=?",$rnd))>0)
			{
				$rnd=mt_rand(10000000,99999999);
			} else
			{
				break;
			}
		}

		$_POST['import_data']=$import_data;

		file_put_contents("$config[temporary_path]/import-$rnd.dat",serialize($_POST),LOCK_EX);

		$lang=$_SESSION['userdata']['lang'];
		$admin_id=$_SESSION['userdata']['user_id'];
		exec("$config[php_path] $config[project_path]/admin/background_import_albums.php $rnd validation $lang $admin_id > /dev/null &");
		return_ajax_success("$page_name?action=progress&amp;import_id=$rnd&amp;rand=\${rand}",2);
	} else {
		return_ajax_errors($errors);
	}
}

if ($_GET['action']=='import_start' && intval($_GET['import_id'])>0)
{
	$import_id=intval($_GET['import_id']);
	if (is_file("$config[temporary_path]/import-$import_id.dat"))
	{
		$data=@unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));

		$import_data=$data['import_data'];
		$import_result=$data['import_result'];
		$import_stats['errors']=0;
		$import_stats['warnings']=0;
		$import_stats['info']=0;
		$import_stats['items']=0;
		$import_stats['empty_lines']=0;
		$import_stats['ok_lines']=intval($data['lines_to_import']);
		foreach ($import_result as $res)
		{
			if ($res['skipped']==1)
			{
				$import_stats['empty_lines']++;
			}
			if (is_array($res['errors']))
			{
				$import_stats['errors']+=count($res['errors']);
			}
			if (is_array($res['warnings']))
			{
				$import_stats['warnings']+=count($res['warnings']);
			}
			if (is_array($res['info']))
			{
				$import_stats['info']+=count($res['info']);
			}
		}

		$temp=explode($data['line_separator_modified'],$import_data);
		foreach ($temp as $res)
		{
			if (trim($res)<>'')
			{
				$import_stats['items']++;
			}
		}
	}
}

if (isset($_POST['save_default']) && intval($_POST['import_id']) > 0)
{
	$import_id = intval($_POST['import_id']);
	$admin_id = intval($_SESSION['userdata']['user_id']);

	$import_task = unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));
	$import_data = $import_task['import_data'];

	$lines_with_errors = $import_task['lines_with_errors'];

	unset($import_task['data']);
	unset($import_task['import_data']);
	unset($import_task['import_result']);
	unset($import_task['lines_with_errors']);

	$task_id = sql_insert("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=51, added_date=?", date("Y-m-d H:i:s"));

	sql_pr("insert into $config[tables_prefix]background_imports set import_id=?, task_id=?, admin_id=?, status_id=0, type_id=2, threads=?, options=?, added_date=?",
		$import_id, $task_id, $admin_id, intval($import_task['threads']), serialize($import_task), date("Y-m-d H:i:s")
	);

	$line_separator = $import_task['line_separator_modified'];
	if ($import_task['line_separator'] == '\r\n')
	{
		$line_separator = "\n";
	}

	$lines_counter = 0;
	$thread_id = 0;
	$lines = explode($line_separator, $import_data);
	foreach ($lines as $line)
	{
		$lines_counter++;

		if (trim($line) == '' || in_array($lines_counter, $lines_with_errors))
		{
			continue;
		}

		$thread_id++;
		if ($thread_id > intval($import_task['threads']))
		{
			$thread_id = 1;
		}
		sql_pr("insert into $config[tables_prefix]background_imports_data set import_id=?, line_id=?, status_id=0, thread_id=?, data=?",
			$import_id, $lines_counter, $thread_id, $line
		);
	}
	unlink("$config[temporary_path]/import-$import_id.dat");

	return_ajax_success("albums.php");
}

if (isset($_POST['back_import']) && intval($_POST['import_id'])>0)
{
	$import_id=intval($_POST['import_id']);
	return_ajax_success("$page_name?action=back_import&amp;import_id=$import_id");
}

if ($_GET['action']=='back_import')
{
	$import_id=intval($_GET['import_id']);
	if (is_file("$config[temporary_path]/import-$import_id.dat"))
	{
		$_POST=@unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"));
	}
}

if ($_POST['users']=='')
{
	$_POST['users']=$options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM'];
}

if ($_POST['post_date_randomization_days']=='')
{
	$_POST['post_date_randomization_days']=1;
}

if ($_POST['post_time_randomization_from']=='')
{
	$_POST['post_time_randomization_from']='00:00';
}
if ($_POST['post_time_randomization_to']=='')
{
	$_POST['post_time_randomization_to']='23:59';
}

if ($_POST['status_after_import_id']=='')
{
	if ($options['DEFAULT_STATUS_IN_ADMIN_ADD_ALBUM']==0)
	{
		$_POST['status_after_import_id']=1;
	}
}

$list_content_sources=array();
$temp=mr2array(sql("select content_source_id, $config[tables_prefix]content_sources_groups.content_source_group_id, $config[tables_prefix]content_sources.title, $config[tables_prefix]content_sources_groups.title as content_source_group_title from $config[tables_prefix]content_sources left join $config[tables_prefix]content_sources_groups on $config[tables_prefix]content_sources_groups.content_source_group_id=$config[tables_prefix]content_sources.content_source_group_id order by $config[tables_prefix]content_sources_groups.title asc,$config[tables_prefix]content_sources.title asc"));
foreach ($temp as $res)
{
	$list_content_sources[$res['content_source_group_id']][]=$res;
}

$smarty=new mysmarty();
$smarty->assign('left_menu','menu_albums.tpl');
$smarty->assign('options',$options);
$smarty->assign('import_result',$import_result);
$smarty->assign('import_stats',$import_stats);
$smarty->assign('import_id',$import_id);
$smarty->assign('list_content_sources',$list_content_sources);
$smarty->assign('list_presets',$ALBUMS_IMPORT_PRESETS);
$smarty->assign('list_languages',mr2array(sql("select * from $config[tables_prefix]languages order by title asc")));
$smarty->assign('list_categories_groups',mr2array(sql("select * from $config[tables_prefix]categories_groups order by title asc")));

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($import_id>0 && is_array($import_result))
{
	$smarty->assign('page_title',$lang['albums']['import_header_preview']);
} else {
	$smarty->assign('page_title',$lang['albums']['import_header_import']);
}

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
	$smarty->assign('list_updates',mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]albums where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
