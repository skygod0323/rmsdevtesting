<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
$_REQUEST=array_merge($_GET,$_POST);

if (strpos($_SERVER['HTTP_HOST'], 'www.') === 0 && strpos($_SERVER['REQUEST_URI'],'/admin/') === 0)
{
	$domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
	header("Location: //{$domain}$_SERVER[REQUEST_URI]");
	die;
}

if (intval($config['admin_session_duration_minutes'])>0 && session_status() != PHP_SESSION_ACTIVE)
{
	ini_set("session.gc_maxlifetime",intval($config['admin_session_duration_minutes'])*60);
}

session_start();

if ($_SESSION['userdata']['login']=='' || ($_SESSION['userdata']['is_ip_protection_disabled']<>1 && $_SESSION['userdata']['ip']!=$_SERVER['REMOTE_ADDR']))
{
	$_SESSION['referer']=str_replace("&","&amp;",$_SERVER['REQUEST_URI']);
	header("Location: index.php");die;
}

ini_set('max_execution_time','9999');

$memory_limit = trim(ini_get('memory_limit'));
if ($memory_limit && $memory_limit != '-1')
{
	$last = strtolower(substr($memory_limit, -1));
	$memory_limit = intval($memory_limit);
	switch ($last)
	{
		/** @noinspection PhpMissingBreakStatementInspection */
		case 'g':
			$memory_limit *= 1024;
		/** @noinspection PhpMissingBreakStatementInspection */
		case 'm':
			$memory_limit *= 1024;
		case 'k':
			$memory_limit *= 1024;
	}
	if ($memory_limit < 510 * 1000 * 1000)
	{
		ini_set('memory_limit', '512M');
	}
}

$_SESSION['admin_page_generation_time_start'] = microtime(true);
$_SESSION['admin_page_generation_memory_start'] = memory_get_peak_usage();

if ($_SESSION['userdata']['login_gate']<>$config['project_url'])
{
	$config['sql_safe_mode'] = 1;
	$result=sql_pr("select * from $config[tables_prefix_multi]admin_users where login=? and md5(pass)=?",$_SESSION['userdata']['login'],nvl($_SESSION['userdata']['pass']));
	unset($config['sql_safe_mode']);
	if (mr2rows($result)>0)
	{
		$old_session_id=$_SESSION['userdata']['session_id'];

		$admin_data=mr2array_single($result);
		$_SESSION['userdata']=$admin_data;
		$_SESSION['userdata']['ip']=$_SERVER['REMOTE_ADDR'];
		$_SESSION['userdata']['session_id']=$old_session_id;
		$_SESSION['userdata']['last_login']=@mr2array_single(sql_pr("select login_date, ip, duration from $config[tables_prefix_multi]log_logins where user_id=? order by login_date desc limit 1",$_SESSION['userdata']['user_id']));
		$_SESSION['userdata']['pass']=md5($_SESSION['userdata']['pass']);
		$_SESSION['userdata']['login_gate']=$config['project_url'];
		if ($_SESSION['userdata']['last_login']['ip']<>'') {$_SESSION['userdata']['last_login']['ip']=int2ip($_SESSION['userdata']['last_login']['ip']);}

		$_SESSION['save']=unserialize($_SESSION['userdata']['preference']);
		unset($_SESSION['userdata']['preference']);

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix_multi]log_logins where session_id=? and (UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_request_date))<86400",$_SESSION['userdata']['session_id'],date("Y-m-d H:i:s")))==0)
		{
			sql_pr("insert into $config[tables_prefix_multi]log_logins set session_id=?, user_id=?, login_date=?, last_request_date=?, duration=0, ip=?",$_SESSION['userdata']['session_id'],$_SESSION['userdata']['user_id'],date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),ip2int($_SERVER['REMOTE_ADDR']));
		}
		sql_pr("update $config[tables_prefix_multi]admin_users set last_ip=? where user_id=?",ip2int($_SERVER['REMOTE_ADDR']),$_SESSION['userdata']['user_id']);
	} else {
		$_SESSION['referer']=str_replace("&","&amp;",$_SERVER['REQUEST_URI']);
		header("Location: index.php?force_relogin=true");die;
	}
}

if (intval($config['admin_session_duration_minutes'])>0)
{
	if (intval($_SESSION['last_request_date'])>0)
	{
		if (time()-intval($_SESSION['last_request_date'])>intval($config['admin_session_duration_minutes'])*60)
		{
			session_destroy();
			session_start();
			$_SESSION['referer']=str_replace("&","&amp;",$_SERVER['REQUEST_URI']);
			header("Location: index.php");
			die;
		}
	}
	$_SESSION['last_request_date']=time();
}

$last_version_check=@file_get_contents("$config[project_path]/admin/data/engine/checks/last_version.dat");
if ($last_version_check<>$config['project_version'])
{
	$compiled_templates=get_contents_from_dir("$config[project_path]/admin/smarty/template-c",1);
	foreach ($compiled_templates as $compiled_template)
	{
		@unlink("$config[project_path]/admin/smarty/template-c/$compiled_template");
	}
	if (!is_dir("$config[project_path]/admin/data/engine/checks")){mkdir("$config[project_path]/admin/data/engine/checks",0777);chmod("$config[project_path]/admin/data/engine/checks",0777);}
	file_put_contents("$config[project_path]/admin/data/engine/checks/last_version.dat",$config['project_version'],LOCK_EX);
}

setcookie('kt_redirect_to','',time(),'/');

$duration=mr2number(sql_pr("select UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(last_request_date) from $config[tables_prefix_multi]log_logins where session_id=?",date("Y-m-d H:i:s"),$_SESSION['userdata']['session_id']));
if ($duration>600) {$duration=600;}
sql_pr("update $config[tables_prefix_multi]log_logins set last_request_date=?, duration=duration+? where session_id=?",date("Y-m-d H:i:s"),$duration,$_SESSION['userdata']['session_id']);

if (is_array($_SESSION['save']) && count($_SESSION['save'])>0)
{
	if ($_SESSION['saved_serialized']<>serialize($_SESSION['save']))
	{
		$_SESSION['saved_serialized']=serialize($_SESSION['save']);
		sql_pr("update $config[tables_prefix_multi]admin_users set preference=? where user_id=?",$_SESSION['saved_serialized'],$_SESSION['userdata']['user_id']);
	}
}

if (!is_file($config['project_path']."/admin/styles/".$_SESSION['userdata']['skin'].".css"))
{
	$_SESSION['userdata']['skin']="default";
}
if (!is_file($config['project_path']."/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	$_SESSION['userdata']['lang']="english";
}
if (!is_file("$config[project_path]/admin/langs/english.php"))  {echo "Project has run into inconsistent state, as one of the project resources is missing (language pack)";die;}
if (!is_file("$config[project_path]/admin/styles/default.css")) {echo "Project has run into inconsistent state, as one of the project resources is missing (styles pack)";die;}

unset($lang);
require_once("$config[project_path]/admin/langs/english.php");
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php");
}
if ($config['dvds_mode']=='dvds')
{
	require_once("$config[project_path]/admin/langs/english/lang_dvds_replace.php");
	if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_dvds_replace.php"))
	{
		require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_dvds_replace.php");
	}
} elseif ($config['dvds_mode']=='series')
{
	require_once("$config[project_path]/admin/langs/english/lang_series_replace.php");
	if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_series_replace.php"))
	{
		require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/lang_series_replace.php");
	}
}
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php"))
{
	require_once("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php");
}

if (isset($lang['system']['set_locale']))
{
	setlocale(LC_TIME,$lang['system']['set_locale']);
}

$admin_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users where user_id=?", $_SESSION['userdata']['user_id']));
if (intval($admin_data['user_id']) != intval($_SESSION['userdata']['user_id']))
{
	header("Location: error.php?error=permission_denied");
	die;
} else
{
	$_SESSION['userdata']['group_id'] = $admin_data['group_id'];
	$_SESSION['userdata']['is_debug_enabled'] = $admin_data['is_debug_enabled'];
	$_SESSION['userdata']['is_access_to_own_content'] = $admin_data['is_access_to_own_content'];
	$_SESSION['userdata']['is_access_to_disabled_content'] = $admin_data['is_access_to_disabled_content'];
	$_SESSION['userdata']['is_access_to_content_flagged_with'] = $admin_data['is_access_to_content_flagged_with'];
}

if ($_SESSION['userdata']['is_debug_enabled'] == 1)
{
	require_once("$config[project_path]/admin/include/functions_base.php");
	if ($_SERVER['REQUEST_METHOD'] == 'GET')
	{
		debug_admin("GET $_SERVER[REQUEST_URI]", $_SESSION['userdata']['user_id']);
	} elseif ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$post_data = '';
		foreach ($_POST as $k => $v)
		{
			$post_data .= "$k = $v\n";
		}
		$post_data = trim($post_data);
		debug_admin("POST $_SERVER[REQUEST_URI]\n$post_data", $_SESSION['userdata']['user_id']);
	}
}

$_SESSION['server_time']=time();
$_SESSION['server_la']=get_LA();
$_SESSION['server_processes']=mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where status_id<>2"));
$_SESSION['server_processes_error']=mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where status_id=2"));
$_SESSION['server_free_space']=sizeToHumanString(@disk_free_space($config['project_path']),1);
$_SESSION['server_free_space_pc']=@disk_free_space($config['project_path'])/@disk_total_space($config['project_path'])*100;
if (is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	$_SESSION['server_processes_paused']=1;
} else {
	$_SESSION['server_processes_paused']=0;
}
if (strpos($config['project_url'], $_SERVER['HTTP_HOST']) === false)
{
	$_SESSION['admin_panel_project_url'] = "$config[project_url]?" . session_name() . '=' . session_id();
}

$page_name=end(explode("/",$_SERVER['SCRIPT_FILENAME']));

$list_messages = null;
if (is_array($_SESSION['messages']))
{
	$list_messages = $_SESSION['messages'];
	unset($_SESSION['messages']);
}

$config['image_allowed_ext'].=",".strtoupper($config['image_allowed_ext']);
$config['other_allowed_ext'].=",".strtoupper($config['other_allowed_ext']);
$config['player_allowed_ext'].=",".strtoupper($config['player_allowed_ext']);

if ($_SESSION['userdata']['is_superadmin']==0)
{
	if ($_SESSION['userdata']['group_id'] > 0)
	{
		$group_data = mr2array_single(sql_pr("select * from $config[tables_prefix_multi]admin_users_groups where group_id=?", $_SESSION['userdata']['group_id']));
		if (intval($group_data['group_id']) == 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
		$_SESSION['userdata']['is_access_to_own_content'] = $group_data['is_access_to_own_content'];
		$_SESSION['userdata']['is_access_to_disabled_content'] = $group_data['is_access_to_disabled_content'];
		$_SESSION['userdata']['is_access_to_content_flagged_with'] = $group_data['is_access_to_content_flagged_with'];

		$_SESSION['permissions'] = mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?)", $_SESSION['userdata']['group_id']));
	} else
	{
		$_SESSION['permissions'] = [];
	}
	$_SESSION['permissions']=array_merge($_SESSION['permissions'],mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_permissions where user_id=?)",$_SESSION['userdata']['user_id'])));
	$_SESSION['permissions']=array_unique($_SESSION['permissions']);

	//check permissions
	settype($_SESSION['permissions'],"array");

	if ($page_name=='admin_users.php' && $_SESSION['userdata']['is_superadmin']==0) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='admin_users_groups.php' && $_SESSION['userdata']['is_superadmin']==0) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='options.php')
	{
		if ($_REQUEST['page']=='general_settings' && !in_array('system|system_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_complete' && !in_array('system|system_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['page']=='website_settings' && !in_array('system|website_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_website_settings_complete' && !in_array('system|website_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['page']=='antispam_settings' && !in_array('system|antispam_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_antispam_settings_complete' && !in_array('system|antispam_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['page']=='memberzone_settings' && !in_array('system|memberzone_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_memberzone_settings_complete' && !in_array('system|memberzone_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['page']=='stats_settings' && !in_array('system|stats_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_stats_settings_complete' && !in_array('system|stats_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['page']=='customization' && !in_array('system|customization',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
		if ($_REQUEST['action']=='change_customization_complete' && !in_array('system|customization',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	}
	if ($page_name=='log_logins.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='log_audit.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='log_bill.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='log_feeds.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='log_imports.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='installation.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='file_changes.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='background_tasks.php' && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='log_background_tasks.php' && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='translations.php' && !in_array('localization|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='translations_summary.php' && !in_array('localization|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='formats_videos_basic.php' && !in_array('system|formats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='formats_videos.php' && !in_array('system|formats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='formats_screenshots.php' && !in_array('system|formats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='formats_albums.php' && !in_array('system|formats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='servers.php' && !in_array('system|servers',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='servers_test.php' && !in_array('system|servers',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='servers_conversion.php' && !in_array('system|servers',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='servers_conversion_basic.php' && !in_array('system|servers',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='player.php' && !in_array('system|player_settings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='vast_profiles.php' && !in_array('system|vast_profiles',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='languages.php' && !in_array('system|localization',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='videos.php' && !in_array('videos|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('videos|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && in_array($_REQUEST['action'],array("mark_deleted","mark_deleted_complete","change_deleted","change_deleted_complete")) && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="delete" && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="soft_delete" && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="activate" && !(in_array('videos|edit_status',$_SESSION['permissions']) || in_array('videos|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="deactivate" && !(in_array('videos|edit_status',$_SESSION['permissions']) || in_array('videos|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('videos|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="delete_and_activate" && !in_array('videos|edit_all',$_SESSION['permissions']) && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="activate_and_delete" && !in_array('videos|edit_all',$_SESSION['permissions']) && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos.php' && $_REQUEST['batch_action']=="inc_priority" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='videos_select.php' && !in_array('videos|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_select.php' && in_array($_REQUEST['operation'],array("mark_deleted","delete")) && !in_array('videos|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_select.php' && in_array($_REQUEST['operation'],array("mass_edit")) && !in_array('videos|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='videos_mass_edit.php' && !in_array('videos|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_feeds_import.php' && !in_array('videos|feeds_import',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_feeds_export.php' && !in_array('videos|feeds_export',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='videos_screenshots.php' && !in_array('videos|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_screenshots.php' && in_array($_REQUEST['action'],array("upload_screenshots","change_screenshots")) && !in_array('videos|manage_screenshots',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_screenshots_grabbing.php' && !in_array('videos|manage_screenshots',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='videos_export.php' && !in_array('videos|export',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='videos_import.php' && !in_array('videos|import',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='albums.php' && !in_array('albums|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['action']=="change_complete" && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('albums|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("upload_images","process_images")) && !in_array('albums|manage_images',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && in_array($_REQUEST['action'],array("mark_deleted","mark_deleted_complete","change_deleted","change_deleted_complete")) && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="delete" && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="soft_delete" && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="activate" && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="delete_and_activate" && !in_array('albums|edit_all',$_SESSION['permissions']) && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="activate_and_delete" && !in_array('albums|edit_all',$_SESSION['permissions']) && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="restart" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums.php' && $_REQUEST['batch_action']=="inc_priority" && !in_array('system|background_tasks',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='albums_select.php' && !in_array('albums|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums_select.php' && in_array($_REQUEST['operation'],array("mark_deleted","delete")) && !in_array('albums|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums_select.php' && in_array($_REQUEST['operation'],array("mass_edit")) && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='albums_mass_edit.php' && !in_array('albums|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums_export.php' && !in_array('albums|export',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='albums_import.php' && !in_array('albums|import',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='posts_types.php' && !in_array('posts_types|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_types.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts_types|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_types.php' && $_REQUEST['action']=="change_complete" && !in_array('posts_types|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_types.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts_types|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_types.php' && $_REQUEST['batch_action']=="delete_with_content" && !(in_array('posts_types|delete',$_SESSION['permissions']) && in_array('posts|delete',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='posts.php' && !in_array('posts|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="activate" && !(in_array('posts|edit_status',$_SESSION['permissions']) || in_array('posts|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts.php' && $_REQUEST['batch_action']=="deactivate" && !(in_array('posts|edit_status',$_SESSION['permissions']) || in_array('posts|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='posts_for_types.php' && !in_array('posts|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_for_types.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('posts|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="delete" && !in_array('posts|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="activate" && !(in_array('posts|edit_status',$_SESSION['permissions']) || in_array('posts|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='posts_for_types.php' && $_REQUEST['batch_action']=="deactivate" && !(in_array('posts|edit_status',$_SESSION['permissions']) || in_array('posts|edit_all',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='categories.php' && !in_array('categories|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('categories|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories.php' && $_REQUEST['action']=="change_complete" && !in_array('categories|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories.php' && $_REQUEST['batch_action']=="delete" && !in_array('categories|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='categories_groups.php' && !in_array('category_groups|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('category_groups|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('category_groups|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='categories_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('category_groups|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='models.php' && !in_array('models|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('models|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models.php' && $_REQUEST['action']=="change_complete" && !in_array('models|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models.php' && $_REQUEST['batch_action']=="delete" && !in_array('models|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='models_groups.php' && !in_array('models_groups|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('models_groups|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('models_groups|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='models_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('models_groups|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='tags.php' && !in_array('tags|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='tags.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('tags|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='tags.php' && $_REQUEST['action']=="change_complete" && !in_array('tags|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='tags.php' && $_REQUEST['batch_action']=="delete" && !in_array('tags|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='tags.php' && isset($_REQUEST['save_rename']) && !in_array('tags|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='flags.php' && !in_array('flags|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='flags.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('flags|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='flags.php' && $_REQUEST['action']=="change_complete" && !in_array('flags|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='flags.php' && $_REQUEST['batch_action']=="delete" && !in_array('flags|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='dvds.php' && !in_array('dvds|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('dvds|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds.php' && $_REQUEST['action']=="change_complete" && !in_array('dvds|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds.php' && in_array($_REQUEST['batch_action'],array("delete","delete_with_videos")) && !in_array('dvds|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds.php' && $_REQUEST['batch_action']=="mark_reviewed" && !in_array('dvds|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='dvds_groups.php' && !in_array('dvds_groups|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('dvds_groups|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('dvds_groups|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='dvds_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('dvds_groups|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='content_sources.php' && !in_array('content_sources|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('content_sources|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources.php' && $_REQUEST['action']=="change_complete" && !in_array('content_sources|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources.php' && $_REQUEST['batch_action']=="delete" && !in_array('content_sources|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='content_sources_groups.php' && !in_array('content_sources_groups|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources_groups.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('content_sources_groups|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources_groups.php' && $_REQUEST['action']=="change_complete" && !in_array('content_sources_groups|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='content_sources_groups.php' && $_REQUEST['batch_action']=="delete" && !in_array('content_sources_groups|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='users.php' && !in_array('users|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('users|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && $_REQUEST['action']=="change_complete" && !in_array('users|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && in_array($_REQUEST['batch_action'],array("delete","delete_with_content")) && !in_array('users|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="unban" && !in_array('users|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="confirm" && !in_array('users|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="activate" && !in_array('users|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('users|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='emailing.php' && !in_array('users|emailings',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='feedbacks.php' && !in_array('feedbacks|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['action']=="change_complete" && !in_array('feedbacks|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['batch_action']=="close" && !in_array('feedbacks|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='feedbacks.php' && $_REQUEST['batch_action']=="delete" && !in_array('feedbacks|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='flags_messages.php' && !in_array('feedbacks|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='flags_messages.php' && $_REQUEST['batch_action']=="delete" && !in_array('feedbacks|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='sms_bill_configurations.php' && !in_array('billing|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='sms_bill_configurations.php' && in_array($_REQUEST['action'],array("change_complete","change_package_complete")) && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='sms_bill_configurations.php' && $_REQUEST['batch_action']=="delete" && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='card_bill_configurations.php' && !in_array('billing|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='card_bill_configurations.php' && in_array($_REQUEST['action'],array("change_complete","change_package_complete")) && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='card_bill_configurations.php' && $_REQUEST['batch_action']=="delete" && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='bill_transactions.php' && !in_array('billing|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='bill_transactions.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='bill_transactions.php' && $_REQUEST['action']=="change_complete" && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='bill_transactions.php' && $_REQUEST['batch_action']=="cancel" && !in_array('billing|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='payouts.php' && !in_array('payouts|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='payouts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('payouts|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='payouts.php' && $_REQUEST['action']=="change_complete" && !in_array('payouts|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='users_blogs.php' && !in_array('users|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['action']=="change_complete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="approve" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="delete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="approve_and_delete" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='users_blogs.php' && $_REQUEST['batch_action']=="delete_and_approve" && !in_array('users|manage_blogs',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='comments.php' && !in_array('users|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='comments.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete","change_complete")) && !in_array('users|manage_comments',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="approve" && !in_array('users|manage_comments',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="delete" && !in_array('users|manage_comments',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="approve_and_delete" && !in_array('users|manage_comments',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='comments.php' && $_REQUEST['batch_action']=="delete_and_approve" && !in_array('users|manage_comments',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='messages.php' && !in_array('messages|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='messages.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('messages|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='messages.php' && $_REQUEST['action']=="change_complete" && !in_array('messages|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='messages.php' && $_REQUEST['batch_action']=="delete" && !in_array('messages|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='playlists.php' && !in_array('playlists|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='playlists.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('playlists|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='playlists.php' && $_REQUEST['action']=="change_complete" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="delete" && !in_array('playlists|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="activate" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='playlists.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('playlists|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='stats_country.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_out.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_player.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_in.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_referer.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_embed.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_videos.php' && !in_array('stats|view_content_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_albums.php' && !in_array('stats|view_content_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_transactions.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_initial_transactions.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_logins.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_content.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_purchases.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_sellings.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_users_awards.php' && !in_array('stats|view_user_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_referers_list.php' && !in_array('stats|manage_referers',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_cleanup.php' && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='stats_search.php' && !in_array('stats|view_traffic_stats',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_search.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='stats_search.php' && $_REQUEST['batch_action']=="delete" && !in_array('stats|manage_search_queries',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_theme.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_theme.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages_history.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("change_complete","change_block_complete")) && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && $_REQUEST['action']=="duplicate" && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && isset($_REQUEST['save_caching']) && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("restore_pages")) && !(in_array('website_ui|add',$_SESSION['permissions']) || in_array('website_ui|delete',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("restore_blocks")) && !(in_array('website_ui|edit_all',$_SESSION['permissions']) || in_array('website_ui|delete',$_SESSION['permissions']))) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && in_array($_REQUEST['action'],array("reset_mem_cache","reset_file_cache","reset_perf_stats")) && !in_array('system|administration',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="wipeout_page" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="wipeout_block" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages.php' && $_REQUEST['batch_action']=="restore_page" && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages_lang_files.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_files.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_files.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_files.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages_lang_texts.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_texts.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_texts.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_lang_texts.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages_components.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_components.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete")) && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_components.php' && $_REQUEST['action']=="change_complete" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_components.php' && $_REQUEST['action']=="duplicate" && !in_array('website_ui|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_components.php' && $_REQUEST['batch_action']=="delete" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_pages_global.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_global.php' && in_array($_REQUEST['action'],array("change_complete")) && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_global.php' && $_REQUEST['batch_action']=="restore_block" && !in_array('website_ui|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_pages_global.php' && $_REQUEST['batch_action']=="wipeout_block" && !in_array('website_ui|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_spots.php' && !in_array('advertising|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_spots.php' && in_array($_REQUEST['action'],array("add_new","add_new_complete","add_new_spot","add_new_spot_complete")) && !in_array('advertising|add',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_spots.php' && in_array($_REQUEST['action'],array("change_complete","change_spot_complete")) && !in_array('advertising|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="delete" && !in_array('advertising|delete',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="activate" && !in_array('advertising|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='project_spots.php' && $_REQUEST['batch_action']=="deactivate" && !in_array('advertising|edit_all',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}

	if ($page_name=='project_blocks.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
	if ($page_name=='templates_search.php' && !in_array('website_ui|view',$_SESSION['permissions'])) {header("Location: error.php?error=permission_denied");die;}
} else {
	$_SESSION['permissions']=mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions"));
}