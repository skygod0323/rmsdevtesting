<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
include_once 'version.php';
umask(0);
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
//ini_set("session.cookie_domain",".".str_replace('www.','',$_SERVER['HTTP_HOST']));

if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$_SERVER['ORIG_REMOTE_ADDR']=$_SERVER['REMOTE_ADDR'];
	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_X_FORWARDED_FOR'];
	if (strpos($_SERVER['REMOTE_ADDR'],',')!==false)
	{
		$_SERVER['REMOTE_ADDR']=trim(substr($_SERVER['REMOTE_ADDR'],0,strpos($_SERVER['REMOTE_ADDR'],',')));
	}
} elseif (isset($_SERVER['HTTP_X_REAL_IP']))
{
	$_SERVER['ORIG_REMOTE_ADDR']=$_SERVER['REMOTE_ADDR'];
	$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_X_REAL_IP'];
}
if (isset($_SERVER['HTTP_CF_IPCOUNTRY']))
{
	$_SERVER['GEOIP_COUNTRY_CODE']=$_SERVER['HTTP_CF_IPCOUNTRY'];
}

// =============================================================================
// Installation options
// =============================================================================

if (!isset($config))
{
	$config = [];
}

$config['project_name']="rmsdevtesting.com";
$config['project_path']="F:/xampp7_4/htdocs";
$config['project_url']="https://www.localhost";
$config['project_title']="CMS";

$config['php_path']="F:/xampp7_4/php";
$config['ffmpeg_path']="/usr/bin/ffmpeg";
$config['image_magick_path']="/usr/bin/convert";
$config['mysqldump_path']="/usr/bin/mysqldump";

$config['server_type']="nginx";

$config['memcache_server']="127.0.0.1";
$config['memcache_port']="11211";

$config['imagemagick_default_jpeg_quality']="80";
$config['min_user_age']="-18";

$config['installation_id']="b21b0ed8f89dde60016e960fb6d12098";
$config['installation_type']="4";
$config['project_licence_domain']="rmsdevtesting.com";
$config['satellite_for']="";
$config['billing_scripts_name']="a68a2d967c5bfb13e9094bf5dad58387";
$config['player_license_code']="$758173625013474";
$config['player_lrc']="101407776";
$config['cv']="a17d19a170a0df92ecc3900ae651d28e";
$config['ahv']="97035817039595148473817017057056";

// =============================================================================
// Overload protection
// =============================================================================

$config['overload_max_la_blocks']=30;
$config['overload_max_la_pages']=50;
$config['overload_max_la_cron']=30;
$config['overload_min_mysql_processes']=20;
$config['overload_max_mysql_processes']=40;
$config['overload_block_wait_iterations']=5;
$config['overload_block_wait_time']=1;

// =============================================================================
// Mirrors configuration
// =============================================================================

// =============================================================================
// Advanced options, should not be changed unless recommended by support
// =============================================================================

$config['uploader_url']="include/uploader_nginx.php";

$config['support_email']="$config[project_name] support <support@$config[project_licence_domain]>";
$config['default_email_headers']="From: ".$config['support_email']."\n"."Reply-To: ".$config['support_email']."\nContent-type: text/plain; charset=UTF-8";

$config['video_allowed_ext']="mp4,flv,3gp,mov,asf,mpg,avi,mpeg,wmv,rm,dat,mkv,vob,m2v,f4v,m4v,m2t,mts,webm,ts";
$config['image_allowed_ext']="jpg,jpeg,png,gif";
$config['other_allowed_ext']="zip,swf";
$config['jpeg_image_or_group_allowed_ext']="jpg,zip";
$config['player_allowed_ext']="flv,mp4";
$config['min_allowed_free_space_limit_mb']="512";
$config['admin_session_duration_minutes']="0";

$config['content_path_videos_sources']="$config[project_path]/contents/videos_sources";
$config['content_path_videos_screenshots']="$config[project_path]/contents/videos_screenshots";
$config['content_path_albums_sources']="$config[project_path]/contents/albums_sources";
$config['content_path_categories']="$config[project_path]/contents/categories";
$config['content_path_content_sources']="$config[project_path]/contents/content_sources";
$config['content_path_models']="$config[project_path]/contents/models";
$config['content_path_dvds']="$config[project_path]/contents/dvds";
$config['content_path_posts']="$config[project_path]/contents/posts";
$config['content_path_avatars']="$config[project_path]/contents/avatars";
$config['content_path_referers']="$config[project_path]/contents/referers";
$config['content_path_other']="$config[project_path]/contents/other";
$config['temporary_path']="$config[project_path]/tmp";

$config['content_url_videos_sources']="$config[project_url]/contents/videos_sources";
$config['content_url_videos_screenshots']="$config[project_url]/contents/videos_screenshots";
$config['content_url_albums_sources']="$config[project_url]/contents/albums_sources";
$config['content_url_categories']="$config[project_url]/contents/categories";
$config['content_url_content_sources']="$config[project_url]/contents/content_sources";
$config['content_url_models']="$config[project_url]/contents/models";
$config['content_url_dvds']="$config[project_url]/contents/dvds";
$config['content_url_posts']="$config[project_url]/contents/posts";
$config['content_url_avatars']="$config[project_url]/contents/avatars";
$config['content_url_referers']="$config[project_url]/contents/referers";
$config['content_url_other']="$config[project_url]/contents/other";

$config['alt_urls_videos_screenshots']=array();
$config['alt_urls_categories']=array();

/* allows moving statics to CDN */
$config['statics_url']="$config[project_url]";

/* enables support for content relative publishing dates */
$config['relative_post_dates']="false";

/* enables content publishing date offset in minutes (can be used if screenshots are synced to remote server) */
$config['post_dates_offset']="0";

/* separate screenshots URL for admin panel to skip CDN */
$config['content_url_videos_screenshots_admin_panel']="$config[project_url]/contents/videos_screenshots";

/* in which mode dvds feature acts: "dvds" or "channels" or "series" */
$config['dvds_mode']="channels";

/* the maximum number of objects which are allowed to be deleted when reviewing content (delete other from the same user) */
$config['max_delete_on_review']="30";

/* download limit for curl downloads */
$config['curl_limit_rate_kbit_s']="0";

/* not recommended for novice users. multiline descriptions will stop working in site area, any inline JS or CSS code that is not terminated correctly will stop working */
$config['minify_html']="false";

/* for internal use only */
$config['safe_mode']="false";

/* if set to "true", rotator will not add ?pqr= params to page URLs and custom JS code will be required;
   this is the needed behavior in all modern themes */
$config['rotator_no_params']="true";

/* specify trade script URL with all options, must contain %URL% token, which will be replaced with gallery page */
$config['trade_script_url']="";

/* completely disables rotator feature, improves performance */
$config['disable_rotator']='false';

/* disables performance statistics, improves performance */
$config['disable_performance_stats']='false';

/* backward compatibility */
$config['is_pagination_2.0']="true";
$config['is_pagination_3.0']="true";
$config['is_pagination_4.0']="true";
$config['is_pagination_4.0_whitelist']=array();
$config['is_pagination_5.0']="true";

/* advanced filtering */
$config['advanced_filtering']=array();

/* for dev debugging */
$config['enable_debug']="false";

/* for dev debugging of get_file.php */
$config['enable_debug_get_file']="true";

// =============================================================================
// Mobile subdomain
// =============================================================================

if ($_SERVER['HTTP_HOST']=='m.'.$config['project_licence_domain'])
{
	$config['project_url']="http://m.".$config['project_licence_domain'];
}

// =============================================================================
// Database
// =============================================================================

$config['tables_prefix']="ktvs_";
$config['tables_prefix_multi']="ktvs_";

if ($config['tables_prefix']==$config['tables_prefix_multi'])
{
	$config['is_clone_db']="false";
} else {
	$config['is_clone_db']="true";
}

ini_set("error_log", "$config[project_path]/admin/logs/log_php_errors.txt");