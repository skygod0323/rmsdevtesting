<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once("admin/include/setup.php");

$category_dir = trim($_REQUEST['category']);
$category_id = intval($_REQUEST['category_id']);
$hash_key = md5("$category_dir|$category_id");
$cache_dir = "$config[project_path]/admin/data/engine/random_video";

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

$request_uri_params_str = '';
if (strpos($_SERVER['REQUEST_URI'], '?') !== false)
{
	$request_uri_params = explode("&", end(explode("?", $_SERVER['REQUEST_URI'], 2)));
	foreach ($request_uri_params as $param)
	{
		$param = explode("=", $param, 2);
		if (!in_array($param[0], array('category', 'category_id')))
		{
			$request_uri_params_str .= "$param[0]=" . urlencode($param[1]) . "&";
		}
	}
	$request_uri_params_str = trim($request_uri_params_str, '&');
}
if ($request_uri_params_str != '')
{
	$request_uri_params_str = "?$request_uri_params_str";
}

if (is_file("$cache_dir/$hash_key.dat") && time() - filectime("$cache_dir/$hash_key.dat") < 60)
{
	$video_list = @unserialize(@file_get_contents("$cache_dir/$hash_key.dat"));
	$video_data = $video_list[mt_rand(0, count($video_list) - 1)];
	if ($video_data['video_id'] > 0)
	{
		$pattern = str_replace("%ID%", $video_data['video_id'], str_replace("%DIR%", $video_data['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
		header("Location: $config[project_url]/$pattern{$request_uri_params_str}");
		die;
	}
}

require_once("admin/include/functions_base.php");
require_once("admin/include/functions.php");
require_once("admin/include/database_selectors.php");

$now_date = date("Y-m-d H:i:s");
$where = '';
if ($category_dir != '')
{
	$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where (dir=? or $database_selectors[where_locale_dir])", $category_dir, $category_dir));
}
if ($category_id > 0)
{
	$where = "and video_id in (select video_id from $config[tables_prefix]categories_videos where category_id=$category_id)";
}

$video_list = mr2array(sql("select video_id, $database_selectors[generic_selector_dir] as dir from $config[tables_prefix]videos where $database_selectors[where_videos] $where order by rand() limit 50"));
if (count($video_list) < 1)
{
	$video_list = mr2array(sql("select video_id, $database_selectors[generic_selector_dir] as dir from $config[tables_prefix]videos where $database_selectors[where_videos] order by rand() limit 50"));
}

if (!is_dir("$cache_dir"))
{
	mkdir("$cache_dir", 0777);
	chmod("$cache_dir", 0777);
}
$fp = fopen("$cache_dir/$hash_key.dat", "w+");
fwrite($fp, serialize($video_list));
fclose($fp);

$video_data = $video_list[mt_rand(0, count($video_list) - 1)];
if ($video_data['video_id'] > 0)
{
	$pattern = str_replace("%ID%", $video_data['video_id'], str_replace("%DIR%", $video_data['dir'], $website_ui_data['WEBSITE_LINK_PATTERN']));
	header("Location: $config[project_url]/$pattern{$request_uri_params_str}");
	die;
} else
{
	header('HTTP/1.0 404 Not Found');
	if (is_file("$config[project_path]/404.html"))
	{
		echo @file_get_contents("$config[project_path]/404.html");
	} else
	{
		echo "The requested URL was not found on this server.";
	}
}