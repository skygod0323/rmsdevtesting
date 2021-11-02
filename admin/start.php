<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

if ($_GET['action']=='redirect_news' && intval($_GET['news_id'])>0)
{
	if (is_file("$config[project_path]/admin/plugins/kvs_news/kvs_news.php"))
	{
		require_once "$config[project_path]/admin/plugins/kvs_news/kvs_news.php";
		if (function_exists('kvs_newsRedirectNews'))
		{
			kvs_newsRedirectNews(intval($_GET['news_id']));die;
		}
	}
} elseif ($_GET['action']=='delete_news' && isset($_GET['news_id']))
{
	if (is_file("$config[project_path]/admin/plugins/kvs_news/kvs_news.php"))
	{
		require_once "$config[project_path]/admin/plugins/kvs_news/kvs_news.php";
		if (function_exists('kvs_newsDeleteNews'))
		{
			kvs_newsDeleteNews(intval($_GET['news_id']));
			header("Location: start.php");die;
		}
	}
}

if ($_POST['action']=='disable_kvs_support')
{
	if (in_array('system|administration',$_SESSION['permissions']))
	{
		sql("update $config[tables_prefix]options set value='0' where variable='ENABLE_KVS_SUPPORT_ACCESS'");
	}
	return_ajax_success('start.php');
} elseif ($_POST['action']=='enable_kvs_support')
{
	if (in_array('system|administration',$_SESSION['permissions']))
	{
		sql("update $config[tables_prefix]options set value='1' where variable='ENABLE_KVS_SUPPORT_ACCESS'");
	}
	return_ajax_success('start.php');
}

if (!is_file("$config[project_path]/admin/data/system/initial_version.dat"))
{
	require_once "$config[project_path]/admin/tools/post_install.php";
	kvs_post_install();
}

$options=get_options();

require_once "$config[project_path]/admin/include/database_selectors.php";
$where_videos = '1=1';
$where_albums = '1=1';
$where_posts = '1=1';
$where_categories = '1=1';
$where_tags = '1=1';
$where_models = '1=1';
$where_content_sources = '1=1';
$where_dvds = '1=1';
$where_dvds_groups = '1=1';
if ($config['is_clone_db'] == 'true')
{
	$where_videos = $database_selectors['where_videos_all'];
	$where_albums = $database_selectors['where_albums_all'];
	$where_posts = $database_selectors['where_posts_all'];
	$where_categories = $database_selectors['where_categories_active_disabled'];
	$where_tags = $database_selectors['where_tags_active_disabled'];
	$where_models = $database_selectors['where_models_active_disabled'];
	$where_content_sources = $database_selectors['where_content_sources_active_disabled'];
	$where_dvds = $database_selectors['where_dvds_active_disabled'];
	$where_dvds_groups = $database_selectors['where_dvds_groups_active_disabled'];
}

$website_ui_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"), ['allowed_classes' => false]);

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
	$stats['daily_updates_videos']=mr2array(sql("select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where $where_videos and status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days"));
	$stats['daily_updates_albums']=mr2array(sql("select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]albums where $where_albums and status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days"));
	$stats['daily_updates_posts']=mr2array(sql("select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]posts where $where_posts and status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days"));
	$stats['daily_updates']=array();
	foreach ($stats['daily_updates_videos'] as $update)
	{
		$date=strtotime($update['post_date']);
		$stats['daily_updates'][$date]=array('videos'=>$update['updates']);
	}
	foreach ($stats['daily_updates_albums'] as $update)
	{
		$date=strtotime($update['post_date']);
		if (isset($stats['daily_updates'][$date]))
		{
			$stats['daily_updates'][$date]['albums']=$update['updates'];
		} else {
			$stats['daily_updates'][$date]=array('albums'=>$update['updates']);
		}
	}
	foreach ($stats['daily_updates_posts'] as $update)
	{
		$date=strtotime($update['post_date']);
		if (isset($stats['daily_updates'][$date]))
		{
			$stats['daily_updates'][$date]['posts']=$update['updates'];
		} else {
			$stats['daily_updates'][$date]=array('posts'=>$update['updates']);
		}
	}
	ksort($stats['daily_updates']);
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==0)
	{
		$stats['daily_updates']=array_reverse($stats['daily_updates'],true);
	}
	if (count($stats['daily_updates'])>$content_scheduler_days)
	{
		$stats['daily_updates']=array_slice($stats['daily_updates'],0,$content_scheduler_days,true);
	}
	if (intval($_SESSION['userdata']['content_scheduler_days_option'])==1)
	{
		$stats['daily_updates']=array_reverse($stats['daily_updates'],true);
	}
}

$now_date=date("Y-m-d H:i:s");
$stats['total_premium_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and is_private=2"));
$stats['total_active_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and status_id=1"));
$stats['total_disabled_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and status_id=0"));
$stats['total_deleted_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and status_id=5"));
$stats['total_error_videos']=mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and status_id=2"));
$stats['total_premium_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and is_private=2"));
$stats['total_active_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and status_id=1"));
$stats['total_disabled_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and status_id=0"));
$stats['total_deleted_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and status_id=5"));
$stats['total_error_albums']=mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and status_id=2"));
$stats['total_active_posts']=mr2number(sql("select count(*) from $config[tables_prefix]posts where $where_posts and status_id=1"));
$stats['total_disabled_posts']=mr2number(sql("select count(*) from $config[tables_prefix]posts where $where_posts and status_id=0"));
$stats['total_comments']=mr2number(sql("select count(*) from $config[tables_prefix]comments"));
$stats['total_content_sources']=mr2number(sql("select count(*) from $config[tables_prefix]content_sources where $where_content_sources"));
$stats['total_categories']=mr2number(sql("select count(*) from $config[tables_prefix]categories where $where_categories"));
$stats['total_models']=mr2number(sql("select count(*) from $config[tables_prefix]models where $where_models"));
$stats['total_dvds']=mr2number(sql("select count(*) from $config[tables_prefix]dvds where $where_dvds"));
$stats['total_dvds_groups']=mr2number(sql("select count(*) from $config[tables_prefix]dvds_groups where $where_dvds_groups"));
$stats['total_tags']=mr2number(sql("select count(*) from $config[tables_prefix]tags where $where_tags"));
$stats['total_users']=mr2number(sql("select count(*) from $config[tables_prefix]users"));
$stats['total_disabled_users']=mr2number(sql("select count(*) from $config[tables_prefix]users where status_id=0"));
$stats['total_nonconfirmed_users']=mr2number(sql("select count(*) from $config[tables_prefix]users where status_id=1"));
$stats['total_premium_users']=mr2number(sql("select count(*) from $config[tables_prefix]users where status_id=3"));
$stats['total_active_users_week']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date>date_sub('$now_date', interval 604800 second)"));
$stats['total_active_users_month']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date>date_sub('$now_date', interval 2592000 second)"));
$stats['total_active_users_year']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date>date_sub('$now_date', interval 31536000 second)"));
$stats['total_not_active_users_week']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date<=date_sub('$now_date', interval 604800 second)"));
$stats['total_not_active_users_month']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date<=date_sub('$now_date', interval 2592000 second)"));
$stats['total_not_active_users_year']=mr2number(sql("select count(*) from $config[tables_prefix]users where last_login_date<=date_sub('$now_date', interval 31536000 second)"));
$stats['total_bookmarks_videos']=mr2number(sql("select count(*) from $config[tables_prefix]fav_videos"));
$stats['total_bookmarks_albums']=mr2number(sql("select count(*) from $config[tables_prefix]fav_albums"));
$stats['total_playlists']=mr2number(sql("select count(*) from $config[tables_prefix]playlists"));
$stats['total_friends']=mr2number(sql("select count(*) from $config[tables_prefix]friends"));
$stats['total_users_blogs']=mr2number(sql("select count(*) from $config[tables_prefix]users_blogs"));
$stats['total_temporary_banned_users']=mr2number(sql("select count(*) from $config[tables_prefix]users where login_protection_is_banned=1 and login_protection_restore_code<>0"));
$stats['total_forever_banned_users']=mr2number(sql("select count(*) from $config[tables_prefix]users where login_protection_is_banned=1 and login_protection_restore_code=0"));
$stats['total_messages']=mr2number(sql("select count(*) from $config[tables_prefix]messages"));
if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
{
	$stats['user_sess_avg_duration']=mr2float(sql("select avg(avg_sess_duration) from $config[tables_prefix]users where avg_sess_duration>0"));
	$stats['user_sess_avg_duration']=durationToHumanString(floor($stats['user_sess_avg_duration']));
}

$post_date_now=date("Y-m-d");
$post_date_yesterday=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
$post_date_week=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-7,date("Y")));
$post_date_month=date("Y-m-d",mktime(0,0,0,date("m"),date("d")-30,date("Y")));

$stats_result=mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as embed_amount from $config[tables_prefix_multi]stats_in where added_date='$post_date_now' limit 1"));
$stats['in_today_uniq']=intval($stats_result['uniq_amount']);
$stats['in_today_content']=intval($stats_result['content_amount']);
$stats['in_today_total']=intval($stats_result['total_amount']);
$stats['out_today']=intval($stats_result['out_amount']);
$stats['embed_today']=intval($stats_result['embed_amount']);

$stats_result=mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as embed_amount from $config[tables_prefix_multi]stats_in where added_date='$post_date_yesterday' limit 1"));
$stats['in_yesterday_uniq']=intval($stats_result['uniq_amount']);
$stats['in_yesterday_content']=intval($stats_result['content_amount']);
$stats['in_yesterday_total']=intval($stats_result['total_amount']);
$stats['out_yesterday']=intval($stats_result['out_amount']);
$stats['embed_yesterday']=intval($stats_result['embed_amount']);

$stats_result=mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as embed_amount from $config[tables_prefix_multi]stats_in where added_date<='$post_date_now' and added_date>='$post_date_week' limit 1"));
$stats['in_week_uniq']=intval($stats_result['uniq_amount']);
$stats['in_week_content']=intval($stats_result['content_amount']);
$stats['in_week_total']=intval($stats_result['total_amount']);
$stats['out_week']=intval($stats_result['out_amount']);
$stats['embed_week']=intval($stats_result['embed_amount']);

$stats_result=mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as embed_amount from $config[tables_prefix_multi]stats_in where added_date<='$post_date_now' and added_date>='$post_date_month' limit 1"));
$stats['in_month_uniq']=intval($stats_result['uniq_amount']);
$stats['in_month_content']=intval($stats_result['content_amount']);
$stats['in_month_total']=intval($stats_result['total_amount']);
$stats['out_month']=intval($stats_result['out_amount']);
$stats['embed_month']=intval($stats_result['embed_amount']);

$stats['in_today_errors']=mr2number(sql("select sum(amount_max_la_pages + amount_max_sleep_processes + amount_max_la_blocks + amount_max_mysql_processes + amount_max_timeout_blocks) from $config[tables_prefix_multi]stats_overload_protection where added_date='$post_date_now'"));
$stats['in_yesterday_errors']=mr2number(sql("select sum(amount_max_la_pages + amount_max_sleep_processes + amount_max_la_blocks + amount_max_mysql_processes + amount_max_timeout_blocks) from $config[tables_prefix_multi]stats_overload_protection where added_date='$post_date_yesterday'"));
$stats['in_week_errors']=mr2number(sql("select sum(amount_max_la_pages + amount_max_sleep_processes + amount_max_la_blocks + amount_max_mysql_processes + amount_max_timeout_blocks) from $config[tables_prefix_multi]stats_overload_protection where added_date<='$post_date_now' and added_date>='$post_date_week'"));
$stats['in_month_errors']=mr2number(sql("select sum(amount_max_la_pages + amount_max_sleep_processes + amount_max_la_blocks + amount_max_mysql_processes + amount_max_timeout_blocks) from $config[tables_prefix_multi]stats_overload_protection where added_date<='$post_date_now' and added_date>='$post_date_month'"));

$referer_id=mr2number(sql("select referer_id from $config[tables_prefix_multi]stats_referers_list where referer='<bookmarks>'"));
$stats['bookmarks_today']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where referer_id='$referer_id' and added_date='$post_date_now'"));
$stats['bookmarks_yesterday']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where referer_id='$referer_id' and added_date='$post_date_yesterday'"));
$stats['bookmarks_week']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where referer_id='$referer_id' and added_date<='$post_date_now' and added_date>='$post_date_week'"));
$stats['bookmarks_month']=mr2number(sql("select sum(uniq_amount) from $config[tables_prefix_multi]stats_in where referer_id='$referer_id' and added_date<='$post_date_now' and added_date>='$post_date_month'"));
if ($stats['bookmarks_today']>0) {$stats['bookmarks_today_pc']=round($stats['bookmarks_today']/$stats['in_today_uniq']*100,2);} else {$stats['bookmarks_today_pc']=0;}
if ($stats['bookmarks_yesterday']>0) {$stats['bookmarks_yesterday_pc']=round($stats['bookmarks_yesterday']/$stats['in_yesterday_uniq']*100,2);} else {$stats['bookmarks_yesterday_pc']=0;}
if ($stats['bookmarks_week']>0) {$stats['bookmarks_week_pc']=round($stats['bookmarks_week']/$stats['in_week_uniq']*100,2);} else {$stats['bookmarks_week_pc']=0;}
if ($stats['bookmarks_month']>0) {$stats['bookmarks_month_pc']=round($stats['bookmarks_month']/$stats['in_month_uniq']*100,2);} else {$stats['bookmarks_month_pc']=0;}
if ($stats['bookmarks_today']>1000) {$stats['bookmarks_today']=floor($stats['bookmarks_today']/1000);$stats['bookmarks_need_k']=1;}
if ($stats['bookmarks_yesterday']>1000) {$stats['bookmarks_yesterday']=floor($stats['bookmarks_yesterday']/1000);$stats['bookmarks_yesterday_need_k']=1;}
if ($stats['bookmarks_week']>1000) {$stats['bookmarks_week']=floor($stats['bookmarks_week']/1000);$stats['bookmarks_week_need_k']=1;}
if ($stats['bookmarks_month']>1000) {$stats['bookmarks_month']=floor($stats['bookmarks_month']/1000);$stats['bookmarks_month_need_k']=1;}

if ($stats['in_today_uniq']>0) {$stats['out_today_pc']=round($stats['out_today']/$stats['in_today_uniq']*100,2);} else {$stats['out_today_pc']=0;}
if ($stats['in_yesterday_uniq']>0) {$stats['out_yesterday_pc']=round($stats['out_yesterday']/$stats['in_yesterday_uniq']*100,2);} else {$stats['out_yesterday_pc']=0;}
if ($stats['in_week_uniq']>0) {$stats['out_week_pc']=round($stats['out_week']/$stats['in_week_uniq']*100,2);} else {$stats['out_week_pc']=0;}
if ($stats['in_month_uniq']>0) {$stats['out_month_pc']=round($stats['out_month']/$stats['in_month_uniq']*100,2);} else {$stats['out_month_pc']=0;}

if ($stats['in_today_uniq']>0) {$stats['embed_today_pc']=round($stats['embed_today']/$stats['in_today_uniq']*100,2);} else {$stats['embed_today_pc']=0;}
if ($stats['in_yesterday_uniq']>0) {$stats['embed_yesterday_pc']=round($stats['embed_yesterday']/$stats['in_yesterday_uniq']*100,2);} else {$stats['embed_yesterday_pc']=0;}
if ($stats['in_week_uniq']>0) {$stats['embed_week_pc']=round($stats['embed_week']/$stats['in_week_uniq']*100,2);} else {$stats['embed_week_pc']=0;}
if ($stats['in_month_uniq']>0) {$stats['embed_month_pc']=round($stats['embed_month']/$stats['in_month_uniq']*100,2);} else {$stats['embed_month_pc']=0;}

if ($stats['in_today_uniq']>1000) {$stats['in_today_uniq']=floor($stats['in_today_uniq']/1000);$stats['in_today_uniq_need_k']=1;}
if ($stats['in_yesterday_uniq']>1000) {$stats['in_yesterday_uniq']=floor($stats['in_yesterday_uniq']/1000);$stats['in_yesterday_uniq_need_k']=1;}
if ($stats['in_week_uniq']>1000) {$stats['in_week_uniq']=floor($stats['in_week_uniq']/1000);$stats['in_week_uniq_need_k']=1;}
if ($stats['in_month_uniq']>1000) {$stats['in_month_uniq']=floor($stats['in_month_uniq']/1000);$stats['in_month_uniq_need_k']=1;}
if ($stats['in_today_total']>1000) {$stats['in_today_total']=floor($stats['in_today_total']/1000);$stats['in_today_total_need_k']=1;}
if ($stats['in_yesterday_total']>1000) {$stats['in_yesterday_total']=floor($stats['in_yesterday_total']/1000);$stats['in_yesterday_total_need_k']=1;}
if ($stats['in_week_total']>1000) {$stats['in_week_total']=floor($stats['in_week_total']/1000);$stats['in_week_total_need_k']=1;}
if ($stats['in_month_total']>1000) {$stats['in_month_total']=floor($stats['in_month_total']/1000);$stats['in_month_total_need_k']=1;}
if ($stats['in_today_content']>1000) {$stats['in_today_content']=floor($stats['in_today_content']/1000);$stats['in_today_content_need_k']=1;}
if ($stats['in_yesterday_content']>1000) {$stats['in_yesterday_content']=floor($stats['in_yesterday_content']/1000);$stats['in_yesterday_content_need_k']=1;}
if ($stats['in_week_content']>1000) {$stats['in_week_content']=floor($stats['in_week_content']/1000);$stats['in_week_content_need_k']=1;}
if ($stats['in_month_content']>1000) {$stats['in_month_content']=floor($stats['in_month_content']/1000);$stats['in_month_content_need_k']=1;}
if ($stats['out_today']>1000) {$stats['out_today']=floor($stats['out_today']/1000);$stats['out_today_need_k']=1;}
if ($stats['out_yesterday']>1000) {$stats['out_yesterday']=floor($stats['out_yesterday']/1000);$stats['out_yesterday_need_k']=1;}
if ($stats['out_week']>1000) {$stats['out_week']=floor($stats['out_week']/1000);$stats['out_week_need_k']=1;}
if ($stats['out_month']>1000) {$stats['out_month']=floor($stats['out_month']/1000);$stats['out_month_need_k']=1;}
if ($stats['embed_today']>1000) {$stats['embed_today']=floor($stats['embed_today']/1000);$stats['embed_today_need_k']=1;}
if ($stats['embed_yesterday']>1000) {$stats['embed_yesterday']=floor($stats['embed_yesterday']/1000);$stats['embed_yesterday_need_k']=1;}
if ($stats['embed_week']>1000) {$stats['embed_week']=floor($stats['embed_week']/1000);$stats['embed_week_need_k']=1;}
if ($stats['embed_month']>1000) {$stats['embed_month']=floor($stats['embed_month']/1000);$stats['embed_month_need_k']=1;}
if ($stats['in_today_errors']>1000) {$stats['in_today_errors']=floor($stats['in_today_errors']/1000);$stats['in_today_errors_need_k']=1;}
if ($stats['in_yesterday_errors']>1000) {$stats['in_yesterday_errors']=floor($stats['in_yesterday_errors']/1000);$stats['in_yesterday_errors_need_k']=1;}
if ($stats['in_week_errors']>1000) {$stats['in_week_errors']=floor($stats['in_week_errors']/1000);$stats['in_week_errors_need_k']=1;}
if ($stats['in_month_errors']>1000) {$stats['in_month_errors']=floor($stats['in_month_errors']/1000);$stats['in_month_errors_need_k']=1;}

if ($config['is_clone_db'] != 'true')
{
	$stats['storage_servers'] = mr2array(sql("select title, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space, (select min(total_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as total_space, (select avg(`load`) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as la from $config[tables_prefix]admin_servers_groups order by group_id asc"));
	foreach ($stats['storage_servers'] as $k => $v)
	{
		$stats['storage_servers'][$k]['free_space'] = sizeToHumanString($v['free_space'], 1);
		if ($v['total_space'] > 0)
		{
			$stats['storage_servers'][$k]['total_space_pc'] = 100 * ($v['free_space'] / $v['total_space']);
		} else
		{
			$stats['storage_servers'][$k]['total_space_pc'] = 0;
		}
	}

	$stats['total_videos_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]videos where $where_videos and is_review_needed=1"));
	$stats['total_albums_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]albums where $where_albums and is_review_needed=1"));
	$stats['total_posts_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]posts where $where_posts and is_review_needed=1"));
	$stats['total_dvds_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]dvds where $where_dvds and is_review_needed=1"));
	$stats['total_playlists_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]playlists where is_review_needed=1"));
	$stats['total_comments_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]comments where is_review_needed=1"));
	$stats['total_users_blogs_for_review'] = mr2number(sql("select count(*) from $config[tables_prefix]users_blogs where is_approved=0"));

	$stats['total_new_feedbacks'] = mr2number(sql("select count(*) from $config[tables_prefix]feedbacks where status_id=1"));

	$stats['profile_removal_requests'] = mr2number(sql("select count(*) from $config[tables_prefix]users where is_removal_requested=1"));

	$data = mr2array(sql("select * from $config[tables_prefix]flags where is_alert=1 order by group_id asc, flag_id asc"));
	$flagged_alerts = [];
	foreach ($data as $flag)
	{
		if ($flag['group_id'] == 1)
		{
			$flag_count = mr2number(sql("select count(*) from $config[tables_prefix]flags_videos where flag_id=$flag[flag_id] and votes>=$flag[alert_min_count]"));
		} elseif ($flag['group_id'] == 2)
		{
			$flag_count = mr2rows(sql("select album_id from $config[tables_prefix]flags_albums where flag_id=$flag[flag_id] group by album_id having sum(votes)>=$flag[alert_min_count]"));
		} elseif ($flag['group_id'] == 3)
		{
			$flag_count = mr2number(sql("select count(*) from $config[tables_prefix]flags_dvds where flag_id=$flag[flag_id] and votes>=$flag[alert_min_count]"));
		} elseif ($flag['group_id'] == 4)
		{
			$flag_count = mr2number(sql("select count(*) from $config[tables_prefix]flags_posts where flag_id=$flag[flag_id] and votes>=$flag[alert_min_count]"));
		} elseif ($flag['group_id'] == 5)
		{
			$flag_count = mr2number(sql("select count(*) from $config[tables_prefix]flags_playlists where flag_id=$flag[flag_id] and votes>=$flag[alert_min_count]"));
		}
		if ($flag_count > 0)
		{
			$alert_data = [];
			$alert_data['flag_id'] = $flag['flag_id'];
			$alert_data['flag_group_id'] = $flag['group_id'];
			$alert_data['flag_title'] = $flag['title'];
			$alert_data['alert_min_count'] = $flag['alert_min_count'];
			$alert_data['count'] = $flag_count;
			$flagged_alerts[] = $alert_data;
		}
	}
	$stats['flags_messages'] = mr2number(sql("select count(*) from $config[tables_prefix]flags_messages"));
}

if ($stats['total_videos_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['total_albums_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['total_dvds_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['total_playlists_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['total_comments_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['total_users_blogs_for_review'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_review_alerts'] = 1;
}
if ($stats['profile_removal_requests'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_memberzone_alerts'] = 1;
}
if ($stats['flags_messages'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_feedback_alerts'] = 1;
}
if ($stats['total_new_feedbacks'] > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_feedback_alerts'] = 1;
}
if (count($flagged_alerts) > 0)
{
	$stats['has_alerts'] = 1;
	$stats['has_flagged_alerts'] = 1;
}

$errors=array();

$disabled_functions = array_map('trim', explode(',', ini_get('disable_functions')));
if (in_array('exec', $disabled_functions))
{
	$error = array();
	$error['message'] = $lang['start']['errors_exec_function'];
	$errors[] = $error;
}

$stats_cron=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/cron.dat"));
if ($stats_cron['cron_error']==1)
{
	$error=array();
	$error['message']=$lang['start']['errors_cron_folder'];
	$errors[]=$error;
} elseif ($stats_cron['cron_error']==2)
{
	$error=array();
	$error['message']=$lang['start']['errors_cron_duplicate'];
	$errors[]=$error;
} elseif (time()-$stats_cron['cron_last_time']>900)
{
	$error=array();
	$error['message']=str_replace("%1%","15",$lang['start']['errors_cron_execution']);
	$errors[]=$error;
}

if (@disk_free_space($config['project_path'])<$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
{
	$error=array();
	$error['message']=str_replace("%1%",$options['MAIN_SERVER_MIN_FREE_SPACE_MB'],$lang['start']['errors_main_server_disk_space']);
	$errors[]=$error;
}

if (mr2number(sql("select server_id from $config[tables_prefix]admin_servers where error_id>0 and error_iteration>1 limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_storage_server_validation'];
	if (in_array('system|servers',$_SESSION['permissions']))
	{
		$error['href']="servers.php?no_filter=true";
	}
	$errors[]=$error;
}
if (mr2number(sql("select server_id from $config[tables_prefix]admin_conversion_servers where status_id=1 and error_id>0 and error_iteration>1 limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_conversion_server_validation'];
	if (in_array('system|servers',$_SESSION['permissions']))
	{
		if ($config['installation_type']>=3)
		{
			$error['href']="servers_conversion.php?no_filter=true";
		} else {
			$error['href']="servers_conversion_basic.php";
		}
	}
	$errors[]=$error;
}
if (mr2number(sql("select task_id from $config[tables_prefix]background_tasks where status_id=2 limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_failed_background_tasks'];
	if (in_array('system|background_tasks',$_SESSION['permissions']))
	{
		$error['href']="background_tasks.php?no_filter=true&se_status_id=2";
	}
	$errors[]=$error;
}
if (mr2number(sql("select record_id from $config[tables_prefix]bill_log where is_alert=1 limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_bill_log'];
	if (in_array('system|administration',$_SESSION['permissions']))
	{
		$error['href']="log_bill.php?no_filter=true&se_show_id=2&reset_errors=1";
	}
	$errors[]=$error;
}
if (is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
{
	$error=array();
	$error['message']=$lang['start']['errors_background_tasks_paused'];
	if (in_array('system|system_settings',$_SESSION['permissions']))
	{
		$error['href']="options.php?page=general_settings";
	}
	$errors[]=$error;
}
if (is_file("$config[project_path]/admin/data/system/update_progress.dat"))
{
	$error=array();
	$error['message']=$lang['start']['errors_update_in_progress'];
	if (in_array('plugins|kvs_update',$_SESSION['permissions']))
	{
		$error['href']="plugins.php?plugin_id=kvs_update&step=pre";
	}
	$errors[]=$error;
}

if ($website_ui_data['DISABLE_WEBSITE']==1)
{
	$error=array();
	$error['message']=$lang['start']['errors_website_disabled'];
	if (in_array('system|system_settings',$_SESSION['permissions']))
	{
		$error['href']="options.php?page=website_settings";
	}
	$errors[]=$error;
}
if (mr2number(sql("select is_modified from $config[tables_prefix_multi]file_changes where is_modified=1 limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_website_files_changed'];
	if (in_array('system|administration',$_SESSION['permissions']))
	{
		$error['href']="file_changes.php";
	}
	$errors[]=$error;
}
if (mr2number(sql("select user_id from $config[tables_prefix_multi]admin_users where (pass=md5('123') || pass=md5(concat('pass:',md5('123')))) limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_default_password_is_not_changed'];
	$error['href']="options.php";
	$errors[]=$error;
}
if (mr2number(sql("select content_source_id from $config[tables_prefix]content_sources where url='' limit 1"))>0)
{
	$error=array();
	$error['message']=$lang['start']['errors_content_sources_have_empty_url'];
	if (in_array('content_sources|view',$_SESSION['permissions']))
	{
		$error['href']="content_sources.php?no_filter=true&se_field=empty%2Furl";
	}
	$errors[]=$error;
}

$admin_panel_alerts = [];

if (mr2number(sql("select server_id from $config[tables_prefix]admin_servers where is_logging_enabled=1 limit 1")) > 0)
{
	$admin_panel_alerts[] = ['message' => $lang['start']['alerts_storage_servers_debug_mode'], 'href' => in_array('system|servers',$_SESSION['permissions']) ? 'servers.php?no_filter=true' : ''];
}
if (mr2number(sql("select server_id from $config[tables_prefix]admin_conversion_servers where is_logging_enabled=1 limit 1")) > 0)
{
	$admin_panel_alerts[] = ['message' => $lang['start']['alerts_conversion_servers_debug_mode'], 'href' => in_array('system|servers',$_SESSION['permissions']) ? 'servers_conversion.php?no_filter=true' : ''];
}
if (mr2number(sql("select feed_id from $config[tables_prefix]videos_feeds_import where is_debug_enabled=1 limit 1")) > 0)
{
	$admin_panel_alerts[] = ['message' => $lang['start']['alerts_video_feeds_debug_mode'], 'href' => in_array('videos|feeds_import',$_SESSION['permissions']) ? 'videos_feeds_import.php?no_filter=true' : ''];
}

$player_data_files = get_player_data_files();
foreach ($player_data_files as $player_data_file)
{
	if (is_file($player_data_file['file']))
	{
		$player_data = @unserialize(file_get_contents($player_data_file['file']), ['allowed_classes' => false]);
		if (intval($player_data['error_logging']) == 1)
		{
			$admin_panel_alerts[] = ['message' => $lang['start']['alerts_player_settings_error_logging'], 'href' => in_array('system|player_settings',$_SESSION['permissions']) ? $player_data_file['admin_page'] : ''];
		}
	}
}

$vast_key_data = @unserialize(file_get_contents("$config[project_path]/admin/data/player/vast/key.dat"), ['allowed_classes' => false]) ?: [];
if ($vast_key_data['primary_vast_key'])
{
	$has_vast_key_error = false;
	$vast_key_valid = intval(substr($vast_key_data['primary_vast_key'], 0, 10));
	if ($vast_key_valid > 0)
	{
		$vast_key_valid = intval(($vast_key_valid - time()) / 86400);
		if ($vast_key_valid > 0)
		{
			if ($vast_key_valid <= 3)
			{
				$error = ['message' => $lang['start']['errors_vast_subscription_expiring']];
				if (in_array('system|player_settings', $_SESSION['permissions']))
				{
					$error['href'] = 'player.php';
				}
				$errors[] = $error;
			}
		} else
		{
			$has_vast_key_error = true;
		}
	} else
	{
		$has_vast_key_error = true;
	}
	if ($has_vast_key_error)
	{
		$error = ['message' => $lang['start']['errors_vast_subscription_expired']];
		if (in_array('system|player_settings', $_SESSION['permissions']))
		{
			$error['href'] = 'player.php';
		}
		$errors[] = $error;
	}
}

$vast_profiles = get_vast_profiles();
foreach ($vast_profiles as $vast_profile)
{
	if ($vast_profile['is_debug_enabled'] == 1)
	{
		$admin_panel_alerts[] = ['message' => $lang['start']['alerts_vast_profiles_debug_mode'], 'href' => in_array('system|vast_profiles',$_SESSION['permissions']) ? 'vast_profiles.php' : ''];
		break;
	}
}

$site_spots = get_site_spots();
foreach ($site_spots as $site_spot)
{
	if ($site_spot['is_debug_enabled'] == 1)
	{
		$admin_panel_alerts[] = ['message' => $lang['start']['alerts_advertising_spots_debug_mode'], 'href' => in_array('advertising|view',$_SESSION['permissions']) ? 'project_spots.php' : ''];
		break;
	}
}

$news=array();
$new_version='';
if (is_file("$config[project_path]/admin/plugins/kvs_news/kvs_news.php"))
{
	require_once "$config[project_path]/admin/plugins/kvs_news/kvs_news.php";
	if (function_exists('kvs_newsGetNews'))
	{
		$news=kvs_newsGetNews();
		foreach ($news as $k=>$news_item)
		{
			if (time()-strtotime($news_item['post_date'])<7*86400)
			{
				$news[$k]['is_new']=1;
			}
		}
	}
	if (function_exists('kvs_newsGetLatestVersion'))
	{
		$temp_version=kvs_newsGetLatestVersion();
		if (intval(str_replace('.','',$temp_version))>intval(str_replace('.','',$config['project_version'])))
		{
			$new_version=$temp_version;
		}
	}
}

$smarty=new mysmarty();
$smarty->assign('left_menu',"no");
$smarty->assign('options',$options);
$smarty->assign('stats',$stats);
$smarty->assign('flagged_alerts',$flagged_alerts);
$smarty->assign('admin_panel_alerts',$admin_panel_alerts);
$smarty->assign('errors',$errors);
$smarty->assign('news',$news);
$smarty->assign('new_version',$new_version);

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));
$smarty->assign('news_text_key','short_text_'.$_SESSION['userdata']['lang']);

$smarty->assign('page_title',$lang['main_menu']['home']);

$smarty->display("layout.tpl");
