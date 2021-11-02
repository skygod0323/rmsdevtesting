<?php

/* Developed by Kernel Team.

   http://kernel-team.com

*/



/* DO NOT EDIT */



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



require_once("setup.php");
require_once("functions_base.php");
require_once("functions_servers.php");
require_once("functions.php");

if (!is_file("$config[project_path]/admin/data/system/cron_custom.lock"))

{

	file_put_contents("$config[project_path]/admin/data/system/cron_custom.lock", "1", LOCK_EX);

}



$lock=fopen("$config[project_path]/admin/data/system/cron_custom.lock","r+");

if (!flock($lock,LOCK_EX | LOCK_NB))

{

	die('Already locked');

}



ini_set('display_errors',1);



/* EDIT FROM HERE */


// =====================================================================================================================
// custom views award
// =====================================================================================================================
$file = "$config[project_path]/admin/data/stats/views.dat";
$oldViews = [];
$newViews = [];

if (file_exists($file)) {
	$data = explode("\r\n", @file_get_contents($file));

	foreach ($data as $res)
	{
		if ($res == '') {
			continue;
		}

		list($userId, $oldViewsCount) = explode("|", $res, 2);
		$oldViews[$userId] = $oldViewsCount;
	}
}

$memberzone_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
$viewsCondition = intval($memberzone_data['AWARDS_EARNING_UNIQUE_VIEWS_CONDITION']);
$viewsAward = intval($memberzone_data['AWARDS_EARNING_UNIQUE_VIEWS']);

$result = mr2array(sql_pr("
	select
	$config[tables_prefix]users.user_id as user_id,
	$config[tables_prefix]users.custom1 as charity_id,
	SUM($config[tables_prefix]videos.video_viewed_unique) as views_count
	from $config[tables_prefix]users
	left join $config[tables_prefix]videos
	on $config[tables_prefix]videos.user_id = $config[tables_prefix]users.user_id
	group by $config[tables_prefix]users.user_id
"));

foreach ($result as $i) {
	$id = intval($i['user_id']);
	$charity = intval($i['charity_id']);
	$viewsCount = intval($i['views_count']);
	$previousCount = 0;

	if (isset($oldViews[$id])) {
		$previousCount = $oldViews[$id];
	}

	$diff = $viewsCount - $previousCount;
	$multiplier = $diff > 0
		? intdiv($diff, $viewsCondition)
		: 0;

	if ($multiplier > 0) {
		$award = $multiplier * $viewsAward;
		userPayouts($id, $award);
		charityPayouts($charity, $award);
		$newViews[] = "$id|$viewsCount";
	} else {
		$newViews[] = "$id|$previousCount";
	}
}

$newViews = implode("\r\n", $newViews);
file_put_contents($file, $newViews);

function userPayouts($id = 0, $amount) {
	if ($id == 0) {
		return;
	}

	global $config;

	sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $amount, intval($id));
}

function charityPayouts($id = 0, $amount) {
	if ($id == 0) {
		return;
	}

	global $config;

	sql_pr("update $config[tables_prefix]content_sources set custom1=custom1+? where content_source_id=?", $amount, intval($id));
}

/* DO NOT EDIT */

flock($lock, LOCK_UN);

fclose($lock);

