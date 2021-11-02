<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once("admin/include/setup.php");

$video_id=intval($_GET['video_id']);
$video_dir=$_GET['video_dir'];
if ($video_id>0 || $video_dir<>'')
{
	if (strpos(str_replace("www.","",$_SERVER['HTTP_REFERER']),str_replace("www.","",$config['project_url']))===0 && trim($_GET['pqr'])<>'')
	{
		$pqr=trim($_GET['pqr']);

		if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator",0777);chmod("$config[project_path]/admin/data/engine/rotator",0777);}
		if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/rotator/videos/clicks.dat","$video_dir:$pqr\r\n",LOCK_EX|FILE_APPEND);
	}

	$website_ui_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
	$pattern=str_replace("%ID%",$video_id,str_replace("%DIR%",$video_dir,$website_ui_data['WEBSITE_LINK_PATTERN']));
	if ($config['trade_script_url']<>'')
	{
		$redirect_url=str_replace("%URL%","$config[project_url]/$pattern",$config['trade_script_url']);
	} else {
		$redirect_url="$config[project_url]/$pattern";
	}
	header("Location: $redirect_url");
}
