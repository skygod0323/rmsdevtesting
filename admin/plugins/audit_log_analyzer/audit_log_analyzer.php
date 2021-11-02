<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function audit_log_analyzerInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/audit_log_analyzer";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function audit_log_analyzerShow()
{
	global $config,$lang,$errors,$page_name;

	audit_log_analyzerInit();
	$plugin_path="$config[project_path]/admin/data/plugins/audit_log_analyzer";

	$errors = null;

	if ($_GET['action']=='progress')
	{
		$task_id=intval($_GET['task_id']);
		$pc=intval(@file_get_contents("$plugin_path/task-progress-$task_id.dat"));
		header("Content-Type: text/xml");

		$location='';
		if ($pc==100)
		{
			$location="<location>plugins.php?plugin_id=audit_log_analyzer&amp;action=results&amp;task_id=$task_id</location>";
			@unlink("$plugin_path/task-progress-$task_id.dat");
		}
		echo "<progress-status><percents>$pc</percents>$location</progress-status>"; die;
	} elseif ($_POST['action']=='calculate')
	{
		$period_start=date("Y-m-d 00:00:00");
		$period_end=date("Y-m-d 23:59:59");
		if (intval($_POST['period_type'])==1)
		{
			$period_start=date("Y-m-d 00:00:00");
		} elseif (intval($_POST['period_type'])==2)
		{
			$period_start=date("Y-m-d 00:00:00",time()-86400);
			$period_end=date("Y-m-d 23:59:59",time()-86400);
		} elseif (intval($_POST['period_type'])==3)
		{
			$period_start=date("Y-m-d 00:00:00",time()-7*86400);
			$period_end=date("Y-m-d 23:59:59",time()-86400);
		} elseif (intval($_POST['period_type'])==4)
		{
			$now_date=getdate();
			if ($now_date['mon']>1)
			{
				$now_date['mon']--;
			} else {
				$now_date['mon']=12;
				$now_date['year']--;
			}
			$period_start="$now_date[year]-$now_date[mon]-$now_date[mday] 00:00:00";
			$period_end=date("Y-m-d 23:59:59",time()-86400);
		} elseif (intval($_POST['period_type'])==6)
		{
			$period_start=date("Y-m-d 00:00:00",strtotime("first day of last month"));
			$period_end=date("Y-m-d 23:59:59",strtotime("last day of last month"));
		} elseif (intval($_POST['period_type'])==5)
		{
			$period_start=date("Y-m-d 00:00:00",strtotime(intval($_POST['period_custom_date_from_Year'])."-".intval($_POST['period_custom_date_from_Month'])."-".intval($_POST['period_custom_date_from_Day'])));
			$period_end=date("Y-m-d 23:59:59",strtotime(intval($_POST['period_custom_date_to_Year'])."-".intval($_POST['period_custom_date_to_Month'])."-".intval($_POST['period_custom_date_to_Day'])));

			if (intval($_POST["period_custom_date_from_Year"])<1 ||intval($_POST["period_custom_date_from_Month"])<1 ||intval($_POST["period_custom_date_from_Day"])<1 ){validate_field('empty',"",$lang['plugins']['audit_log_analyzer']['field_period_type_custom']);} else
			if (intval($_POST["period_custom_date_to_Year"])<1 ||intval($_POST["period_custom_date_to_Month"])<1 ||intval($_POST["period_custom_date_to_Day"])<1 ){validate_field('empty',"",$lang['plugins']['audit_log_analyzer']['field_period_type_custom']);} else
			if (strtotime($period_start)>strtotime($period_end))
			{
				$errors[]=get_aa_error('invalid_date_range',$lang['plugins']['audit_log_analyzer']['field_period_type_custom']);
			}
			if (is_array($errors))
			{
				return_ajax_errors($errors);
			}
		}
		$data=array();
		$data['period_type']=intval($_POST['period_type']);
		$data['period_custom_date_from']=$period_start;
		$data['period_custom_date_to']=date("Y-m-d 00:00:00",strtotime($period_end));
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

		settype($_POST['admin_ids'], 'array');
		settype($_POST['user_ids'], 'array');

		$data['period_start']=$period_start;
		$data['period_end']=$period_end;
		$data['admin_ids']=implode(",",array_map("intval",$_POST['admin_ids']));
		$data['user_ids']=implode(",",array_map("intval",$_POST['user_ids']));

		$rnd=mt_rand(10000000,99999999);
		file_put_contents("$plugin_path/task-$rnd.dat", serialize($data), LOCK_EX);

		exec("$config[php_path] $config[project_path]/admin/plugins/audit_log_analyzer/audit_log_analyzer.php $rnd > /dev/null &");
		return_ajax_success("$page_name?plugin_id=audit_log_analyzer&amp;action=progress&amp;task_id=$rnd&amp;rand=\${rand}",2);
	}

	if (intval($_GET['task_id'])>0 && is_file("$plugin_path/task-".intval($_GET['task_id']).".dat"))
	{
		$_POST=@unserialize(@file_get_contents("$plugin_path/task-".intval($_GET['task_id']).".dat"));
	} elseif (is_file("$plugin_path/data.dat")) {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	} else {
		$_POST=array();
		$_POST['period_type']=1;
		$_POST['period_custom_date_from']='0000-00-00';
		$_POST['period_custom_date_to']='0000-00-00';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	}

	$now_date=getdate();
	if ($now_date['mon']>1)
	{
		$now_date['mon']--;
	} else {
		$now_date['mon']=12;
		$now_date['year']--;
	}

	$_POST['today']=time();
	$_POST['yesterday']=time()-86400;
	$_POST['week']=time()-7*86400;
	$_POST['month']=strtotime("$now_date[year]-$now_date[mon]-$now_date[mday] $now_date[hours]:$now_date[minutes]:$now_date[seconds]");
	$_POST['month_start']=strtotime("first day of last month");
	$_POST['month_end']=strtotime("last day of last month");


	if (strlen($_POST['admin_ids'])>0)
	{
		$_POST['admins']=mr2array(sql("select user_id, login from $config[tables_prefix]admin_users where user_id in ($_POST[admin_ids])"));
	}
	if (strlen($_POST['user_ids'])>0)
	{
		$_POST['users']=mr2array(sql("select user_id, username from $config[tables_prefix]users where user_id in ($_POST[user_ids])"));
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	}
}

$task_id=intval($_SERVER['argv'][1]);

if ($task_id>0 && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('include/setup.php');
	require_once('include/functions_base.php');
	require_once('include/functions_servers.php');
	require_once('include/functions.php');

	$plugin_path="$config[project_path]/admin/data/plugins/audit_log_analyzer";

	$data=@unserialize(@file_get_contents("$plugin_path/task-$task_id.dat"));
	$data['result']=array();

	$where='';
	if (strlen($data['admin_ids'])>0)
	{
		$where_admins="(user_id in ($data[admin_ids]) and action_id in (100, 110, 130, 150, 151, 152, 180, 200))";
	}
	if (strlen($data['user_ids'])>0)
	{
		$where_users="(user_id in ($data[user_ids]) and action_id in (140, 170, 190))";
	}
	if ($where_admins<>'' && $where_users<>'')
	{
		$where="and ($where_admins or $where_users)";
	} elseif ($where_admins<>'')
	{
		$where="and $where_admins";
	} elseif ($where_users<>'')
	{
		$where="and $where_users";
	}

	if ($where<>'')
	{
		$log=mr2array(sql("select * from $config[tables_prefix]admin_audit_log where added_date>'$data[period_start]' and added_date<'$data[period_end]' $where"));
		$total_amount_of_work=count($log);
		$done_amount_of_work=0;
		$objects_modified_storage=array();
		$languages=mr2array(sql("select * from $config[tables_prefix]languages"));
		foreach ($log as $log_item)
		{
			if (in_array($log_item['action_id'],array(100, 110, 130, 150, 151, 152, 180, 200)))
			{
				$user_key="admin|$log_item[user_id]";
			} elseif (in_array($log_item['action_id'],array(140, 170, 190))) {
				$user_key="user|$log_item[user_id]";
			} else {
				$done_amount_of_work++;
				continue;
			}

			$table_name='';
			$table_key_name='';
			$title_selector="title";
			$desc_selector="description";
			if ($log_item['object_type_id']==1)
			{
				$table_name="$config[tables_prefix]videos";
				$table_key_name="video_id";
			} elseif ($log_item['object_type_id']==2)
			{
				$table_name="$config[tables_prefix]albums";
				$table_key_name="album_id";
			} elseif ($log_item['object_type_id']==3)
			{
				$table_name="$config[tables_prefix]content_sources";
				$table_key_name="content_source_id";
			} elseif ($log_item['object_type_id']==4)
			{
				$table_name="$config[tables_prefix]models";
				$table_key_name="model_id";
			} elseif ($log_item['object_type_id']==5)
			{
				$table_name="$config[tables_prefix]dvds";
				$table_key_name="dvd_id";
			} elseif ($log_item['object_type_id']==6)
			{
				$table_name="$config[tables_prefix]categories";
				$table_key_name="category_id";
			} elseif ($log_item['object_type_id']==7)
			{
				$table_name="$config[tables_prefix]categories_groups";
				$table_key_name="category_group_id";
			} elseif ($log_item['object_type_id']==8)
			{
				$table_name="$config[tables_prefix]content_sources_groups";
				$table_key_name="content_source_group_id";
			} elseif ($log_item['object_type_id']==9)
			{
				$table_name="$config[tables_prefix]tags";
				$table_key_name="tag_id";
				$title_selector="tag";
				$desc_selector="";
			} elseif ($log_item['object_type_id']==10)
			{
				$table_name="$config[tables_prefix]dvds_groups";
				$table_key_name="dvd_group_id";
			} elseif ($log_item['object_type_id']==11)
			{
				$table_name="$config[tables_prefix]posts_types";
				$table_key_name="post_type_id";
			} elseif ($log_item['object_type_id']==12)
			{
				$table_name="$config[tables_prefix]posts";
				$table_key_name="post_id";
			} elseif ($log_item['object_type_id']==13)
			{
				$table_name="$config[tables_prefix]playlists";
				$table_key_name="playlist_id";
			} elseif ($log_item['object_type_id']==14)
			{
				$table_name="$config[tables_prefix]models_groups";
				$table_key_name="model_group_id";
			}

			if (!isset($data['result'][$user_key]))
			{
				$user_info=array();
				$user_info['user_id']=$log_item['user_id'];
				$user_info['username']=$log_item['username'];
			} else {
				$user_info=$data['result'][$user_key];
			}

			if ($log_item['action_id']==100 || $log_item['action_id']==110 || $log_item['action_id']==130 || $log_item['action_id']==140)
			{
				if ($log_item['object_type_id']==1)
				{
					$user_info['videos_added']++;
				} elseif ($log_item['object_type_id']==2)
				{
					$user_info['albums_added']++;
				} elseif ($log_item['object_type_id']==12)
				{
					$user_info['posts_added']++;
				} else {
					$user_info['other_added']++;
				}
			} elseif ($log_item['action_id']==150 || $log_item['action_id']==170)
			{
				if (!isset($objects_modified_storage["$user_key|m|$log_item[object_type_id]|$log_item[object_id]"]))
				{
					if ($log_item['object_type_id']==1)
					{
						$user_info['videos_modified']++;
					} elseif ($log_item['object_type_id']==2)
					{
						$user_info['albums_modified']++;
					} elseif ($log_item['object_type_id']==12)
					{
						$user_info['posts_modified']++;
					} else {
						$user_info['other_modified']++;
					}
					$objects_modified_storage["$user_key|m|$log_item[object_type_id]|$log_item[object_id]"]=1;
				}
				if ($table_name<>'' && strpos($log_item['action_details'],'title')!==false)
				{
					if (!isset($objects_modified_storage["$user_key|mt|$log_item[object_type_id]|$log_item[object_id]"]))
					{
						$user_info['text_symbols']+=mr2number(sql_pr("select char_length(title) from $table_name where $table_key_name=?",$log_item['object_id']));
						$objects_modified_storage["$user_key|mt|$log_item[object_type_id]|$log_item[object_id]"]=1;
						usleep(1000);
					}
				}
				if ($table_name<>'' && strpos($log_item['action_details'],'description')!==false)
				{
					if (!isset($objects_modified_storage["$user_key|md|$log_item[object_type_id]|$log_item[object_id]"]))
					{
						$user_info['text_symbols']+=mr2number(sql_pr("select char_length(description) from $table_name where $table_key_name=?",$log_item['object_id']));
						$objects_modified_storage["$user_key|md|$log_item[object_type_id]|$log_item[object_id]"]=1;
						usleep(1000);
					}
				}
			} elseif ($log_item['action_id']==151)
			{
				if (!isset($objects_modified_storage["$user_key|vs|$log_item[object_type_id]|$log_item[object_id]"]))
				{
					$user_info['vs_modified']++;
					$objects_modified_storage["$user_key|vs|$log_item[object_type_id]|$log_item[object_id]"]=1;
				}
			} elseif ($log_item['action_id']==152)
			{
				if (!isset($objects_modified_storage["$user_key|ai|$log_item[object_type_id]|$log_item[object_id]"]))
				{
					$user_info['ai_modified']++;
					$objects_modified_storage["$user_key|ai|$log_item[object_type_id]|$log_item[object_id]"]=1;
				}
			} elseif ($log_item['action_id']==180 || $log_item['action_id']==190)
			{
				if ($log_item['object_type_id']==1)
				{
					$user_info['videos_deleted']++;
				} elseif ($log_item['object_type_id']==2)
				{
					$user_info['albums_deleted']++;
				} elseif ($log_item['object_type_id']==12)
				{
					$user_info['posts_deleted']++;
				} else {
					$user_info['other_deleted']++;
				}
			} elseif ($log_item['action_id']==200)
			{
				if (!isset($objects_modified_storage["$user_key|t|$log_item[object_type_id]|$log_item[object_id]"]))
				{
					if ($log_item['object_type_id']==1)
					{
						$user_info['videos_translated']++;
					} elseif ($log_item['object_type_id']==2)
					{
						$user_info['albums_translated']++;
					} else {
						$user_info['other_translated']++;
					}
					$objects_modified_storage["$user_key|t|$log_item[object_type_id]|$log_item[object_id]"]=1;
				}
				foreach ($languages as $language)
				{
					if ($table_name<>'' && strpos($log_item['action_details'],"{$title_selector}_$language[code]")!==false)
					{
						if (!isset($objects_modified_storage["$user_key|tt_$language[code]|$log_item[object_type_id]|$log_item[object_id]"]))
						{
							$user_info['translation_symbols']+=mr2number(sql_pr("select char_length({$title_selector}_$language[code]) from $table_name where $table_key_name=?",$log_item['object_id']));
							$objects_modified_storage["$user_key|tt_$language[code]|$log_item[object_type_id]|$log_item[object_id]"]=1;
							usleep(1000);
						}
					}
					if ($table_name<>'' && $desc_selector<>'' && strpos($log_item['action_details'],"{$desc_selector}_$language[code]")!==false)
					{
						if (!isset($objects_modified_storage["$user_key|td_$language[code]|$log_item[object_type_id]|$log_item[object_id]"]))
						{
							$user_info['translation_symbols']+=mr2number(sql_pr("select char_length({$desc_selector}_$language[code]) from $table_name where $table_key_name=?",$log_item['object_id']));
							$objects_modified_storage["$user_key|td_$language[code]|$log_item[object_type_id]|$log_item[object_id]"]=1;
							usleep(1000);
						}
					}
				}
			}
			$data['result'][$user_key]=$user_info;

			$done_amount_of_work+=1;
			$pc=floor(($done_amount_of_work/$total_amount_of_work)*100);
			file_put_contents("$plugin_path/task-progress-$task_id.dat", "$pc", LOCK_EX);
		}
	}

	file_put_contents("$plugin_path/task-$task_id.dat", serialize($data), LOCK_EX);
	file_put_contents("$plugin_path/task-progress-$task_id.dat", "100", LOCK_EX);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
