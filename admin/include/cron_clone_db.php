<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id']<1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_servers.php";
require_once "functions.php";

if (!is_file("$config[project_path]/admin/data/system/cron_clone_db.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_clone_db.lock", "1", LOCK_EX);
}

$lock=fopen("$config[project_path]/admin/data/system/cron_clone_db.lock","r+");
if (!flock($lock,LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

if ($config['is_clone_db']!="true")
{
	die('Not allowed');
}

ini_set('display_errors',1);

//get options
$options=get_options();

// update info about satellites
if (sql_update("update $config[tables_prefix]admin_satellites set project_url=? where multi_prefix=?", $config['project_url'], $config['tables_prefix_multi']) == 0)
{
	sql_pr("insert into $config[tables_prefix]admin_satellites set multi_prefix=?, project_url=?, state_id=?",$config['tables_prefix_multi'],$config['project_url'],0);
}

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
if (is_array($website_ui_data))
{
	if ($config['locale']!='')
	{
		$website_ui_data['locale']=$config['locale'];
	}
	sql_pr("update $config[tables_prefix]admin_satellites set website_ui_data=? where multi_prefix=?",serialize($website_ui_data),$config['tables_prefix_multi']);
}

// check if file upload params are changed
$old_value=@file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat");
$file_upload_params=array();
$file_upload_params['FILE_UPLOAD_DISK_OPTION']=$options['FILE_UPLOAD_DISK_OPTION'];
$file_upload_params['FILE_UPLOAD_URL_OPTION']=$options['FILE_UPLOAD_URL_OPTION'];
$file_upload_params['FILE_UPLOAD_SIZE_LIMIT']=intval($options['FILE_UPLOAD_SIZE_LIMIT']);
$file_upload_params['FILE_DOWNLOAD_SPEED_LIMIT']=intval($options['FILE_DOWNLOAD_SPEED_LIMIT']);
$new_value=serialize($file_upload_params);

if ($old_value<>$new_value)
{
	log_output("File upload settings were changed");
	file_put_contents("$config[project_path]/admin/data/system/file_upload_params.dat", $new_value, LOCK_EX);
}

// check if hotlink params are changed
$old_value=@file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat");
$anti_hotlink_params=array();
$anti_hotlink_params['ENABLE_ANTI_HOTLINK']=intval($options['ENABLE_ANTI_HOTLINK']);
$anti_hotlink_params['ANTI_HOTLINK_ENABLE_IP_LIMIT']=intval($options['ANTI_HOTLINK_ENABLE_IP_LIMIT']);
$anti_hotlink_params['ANTI_HOTLINK_TYPE']=intval($options['ANTI_HOTLINK_TYPE']);
$anti_hotlink_params['ANTI_HOTLINK_ENCODE_LINKS']=intval($options['ANTI_HOTLINK_ENCODE_LINKS']);
$anti_hotlink_params['ANTI_HOTLINK_FILE']=$options['ANTI_HOTLINK_FILE'];
$anti_hotlink_params['ANTI_HOTLINK_WHITE_DOMAINS']=$options['ANTI_HOTLINK_WHITE_DOMAINS'];
$anti_hotlink_params['ANTI_HOTLINK_WHITE_IPS']=$options['ANTI_HOTLINK_WHITE_IPS'];
$new_value=serialize($anti_hotlink_params);

if ($old_value<>$new_value)
{
	log_output("Hotlink protection settings were changed");
	file_put_contents("$config[project_path]/admin/data/system/hotlink_info.dat", $new_value, LOCK_EX);
}

// rotator is disabled
$rotator_params=array();
$rotator_params['ROTATOR_VIDEOS_ENABLE']=0;

file_put_contents("$config[project_path]/admin/data/system/rotator.dat", serialize($rotator_params), LOCK_EX);

// check if api params are changed
$api_params=array();
$api_params['API_ENABLE']=intval($options['API_ENABLE']);
$api_params['API_PASSWORD']=$options['API_PASSWORD'];

$old_value=@file_get_contents("$config[project_path]/admin/data/system/api.dat");
$new_value=serialize($api_params);

if ($old_value<>$new_value)
{
	log_output("API settings were changed");
	file_put_contents("$config[project_path]/admin/data/system/api.dat", $new_value, LOCK_EX);
}

// check if mixed params are changed
$mixed_params=array();
$mixed_params['ALBUMS_SOURCE_FILES_ACCESS_LEVEL']=intval($options['ALBUMS_SOURCE_FILES_ACCESS_LEVEL']);

$old_value=@file_get_contents("$config[project_path]/admin/data/system/mixed_options.dat");
$new_value=serialize($mixed_params);

if ($old_value<>$new_value)
{
	log_output("Mixed settings were changed");
	file_put_contents("$config[project_path]/admin/data/system/mixed_options.dat", $new_value, LOCK_EX);
}

// check if memberzone params are changed
$awards=array(
	'AWARDS_SIGNUP'=>$lang['settings']['memberzone_awards_col_action_signup'],
	'AWARDS_AVATAR'=>$lang['settings']['memberzone_awards_col_action_avatar'],
	'AWARDS_COVER'=>$lang['settings']['memberzone_awards_col_action_cover'],
	'AWARDS_LOGIN'=>$lang['settings']['memberzone_awards_col_action_login'],
	'AWARDS_COMMENT_VIDEO'=>$lang['settings']['memberzone_awards_col_action_comment_video'],
	'AWARDS_COMMENT_ALBUM'=>$lang['settings']['memberzone_awards_col_action_comment_album'],
	'AWARDS_COMMENT_CS'=>$lang['settings']['memberzone_awards_col_action_comment_content_source'],
	'AWARDS_COMMENT_MODEL'=>$lang['settings']['memberzone_awards_col_action_comment_model'],
	'AWARDS_COMMENT_DVD'=>$lang['settings']['memberzone_awards_col_action_comment_dvd'],
	'AWARDS_COMMENT_POST'=>$lang['settings']['memberzone_awards_col_action_comment_post'],
	'AWARDS_COMMENT_PLAYLIST'=>$lang['settings']['memberzone_awards_col_action_comment_playlist'],
	'AWARDS_VIDEO_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_video_upload'],
	'AWARDS_ALBUM_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_album_upload'],
	'AWARDS_POST_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_post_upload'],
	'AWARDS_REFERRAL_SIGNUP'=>$lang['settings']['memberzone_awards_col_action_referral_signup'],
);
$memberzone_params=array();
$memberzone_params['STATUS_AFTER_PREMIUM']=$options['STATUS_AFTER_PREMIUM'];
$memberzone_params['PUBLIC_VIDEOS_ACCESS']=intval($options['PUBLIC_VIDEOS_ACCESS']);
$memberzone_params['PRIVATE_VIDEOS_ACCESS']=intval($options['PRIVATE_VIDEOS_ACCESS']);
$memberzone_params['PREMIUM_VIDEOS_ACCESS']=intval($options['PREMIUM_VIDEOS_ACCESS']);
$memberzone_params['PUBLIC_ALBUMS_ACCESS']=intval($options['PUBLIC_ALBUMS_ACCESS']);
$memberzone_params['PRIVATE_ALBUMS_ACCESS']=intval($options['PRIVATE_ALBUMS_ACCESS']);
$memberzone_params['PREMIUM_ALBUMS_ACCESS']=intval($options['PREMIUM_ALBUMS_ACCESS']);
$memberzone_params['AFFILIATE_PARAM_NAME']=trim($options['AFFILIATE_PARAM_NAME']);
$memberzone_params['ENABLE_TOKENS_STANDARD_VIDEO']=intval($options['ENABLE_TOKENS_STANDARD_VIDEO']);
$memberzone_params['ENABLE_TOKENS_PREMIUM_VIDEO']=intval($options['ENABLE_TOKENS_PREMIUM_VIDEO']);
$memberzone_params['ENABLE_TOKENS_STANDARD_ALBUM']=intval($options['ENABLE_TOKENS_STANDARD_ALBUM']);
$memberzone_params['ENABLE_TOKENS_PREMIUM_ALBUM']=intval($options['ENABLE_TOKENS_PREMIUM_ALBUM']);
$memberzone_params['DEFAULT_TOKENS_STANDARD_VIDEO']=intval($options['DEFAULT_TOKENS_STANDARD_VIDEO']);
$memberzone_params['DEFAULT_TOKENS_PREMIUM_VIDEO']=intval($options['DEFAULT_TOKENS_PREMIUM_VIDEO']);
$memberzone_params['DEFAULT_TOKENS_STANDARD_ALBUM']=intval($options['DEFAULT_TOKENS_STANDARD_ALBUM']);
$memberzone_params['DEFAULT_TOKENS_PREMIUM_ALBUM']=intval($options['DEFAULT_TOKENS_PREMIUM_ALBUM']);
$memberzone_params['TOKENS_PURCHASE_EXPIRY']=$options['TOKENS_PURCHASE_EXPIRY'];
$memberzone_params['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']=intval($options['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']);
$memberzone_params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']=intval($options['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
$memberzone_params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']=$options['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD'];
$memberzone_params['ENABLE_TOKENS_SUBSCRIBE_DVDS']=intval($options['ENABLE_TOKENS_SUBSCRIBE_DVDS']);
$memberzone_params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']=intval($options['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
$memberzone_params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']=$options['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD'];
$memberzone_params['ENABLE_TOKENS_SALE_VIDEOS']=intval($options['ENABLE_TOKENS_SALE_VIDEOS']);
$memberzone_params['ENABLE_TOKENS_SALE_ALBUMS']=intval($options['ENABLE_TOKENS_SALE_ALBUMS']);
$memberzone_params['ENABLE_TOKENS_SALE_MEMBERS']=intval($options['ENABLE_TOKENS_SALE_MEMBERS']);
$memberzone_params['ENABLE_TOKENS_SALE_DVDS']=intval($options['ENABLE_TOKENS_SALE_DVDS']);
$memberzone_params['TOKENS_SALE_INTEREST']=min(100,intval($options['TOKENS_SALE_INTEREST']));
$memberzone_params['TOKENS_SALE_EXCLUDES']=$options['TOKENS_SALE_EXCLUDES'];
$memberzone_params['ENABLE_TOKENS_DONATIONS']=intval($options['ENABLE_TOKENS_DONATIONS']);
$memberzone_params['TOKENS_DONATION_MIN']=intval($options['TOKENS_DONATION_MIN']);
$memberzone_params['TOKENS_DONATION_INTEREST']=min(100,intval($options['TOKENS_DONATION_INTEREST']));
$memberzone_params['ENABLE_TOKENS_INTERNAL_MESSAGES']=intval($options['ENABLE_TOKENS_INTERNAL_MESSAGES']);
$memberzone_params['TOKENS_INTERNAL_MESSAGES']=intval($options['TOKENS_INTERNAL_MESSAGES']);
foreach ($awards as $award_id=>$field_name)
{
	$memberzone_params["{$award_id}_CONDITION"]=$options["{$award_id}_CONDITION"];
	$memberzone_params["{$award_id}"]=$options["{$award_id}"];
}

$old_value=@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat");
$new_value=serialize($memberzone_params);

if ($old_value<>$new_value)
{
	log_output("Memberzone settings were changed");
	file_put_contents("$config[project_path]/admin/data/system/memberzone_params.dat", $new_value, LOCK_EX);
}

// check if formats videos are changed
$data=mr2array(sql("select title,postfix,video_type_id,access_level_id,is_hotlink_protection_disabled,is_download_enabled,download_order,limit_speed_option,limit_speed_value,limit_speed_guests_option,limit_speed_guests_value,limit_speed_standard_option,limit_speed_standard_value,limit_speed_premium_option,limit_speed_premium_value,limit_speed_embed_option,limit_speed_embed_value,limit_speed_countries,timeline_directory from $config[tables_prefix]formats_videos order by format_video_id asc"));
if (count($data)>0)
{
	$old_value=@file_get_contents("$config[project_path]/admin/data/system/formats_videos.dat");
	$new_value=serialize($data);

	if ($old_value<>$new_value)
	{
		log_output("Formats videos settings were changed");
		file_put_contents("$config[project_path]/admin/data/system/formats_videos.dat", $new_value, LOCK_EX);
	}
}

// check if formats albums are changed
$data=mr2array(sql("select format_album_id, group_id, size, access_level_id, is_create_zip from $config[tables_prefix]formats_albums order by format_album_id asc"));
if (count($data)>0)
{
	$old_value=@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat");
	$new_value=serialize($data);

	if ($old_value<>$new_value)
	{
		log_output("Formats albums settings were changed");
		file_put_contents("$config[project_path]/admin/data/system/formats_albums.dat", $new_value, LOCK_EX);
	}
}

// update servers data
update_cluster_data();

flock($lock, LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message=='')
	{
		echo "\n";
	} else {
		echo date("[Y-m-d H:i:s] ").$message."\n";
	}
}
