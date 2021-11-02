<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function custom_post_processingIsEnabled()
{
	// return true to enable this plugin
	return false;
}

function custom_post_processingInit()
{
	// this is for admin panel, nothing is required here
}

function custom_post_processingShow()
{
	// this is for admin panel, nothing is required here
}

if ($_SERVER['argv'][1] == 'exec' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once('setup.php');
	require_once('functions_base.php');

	$object_type = $_SERVER['argv'][2];
	$object_id = intval($_SERVER['argv'][3]);

	if ($object_type == 'video')
	{
		$video_id = $object_id;
		$video_data = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=$video_id"));

		// here you can use $video_data to access data of the post-processed video
		// by default it will print video data into the log:
		print_r($video_data);
	} elseif ($object_type == 'album')
	{
		$album_id = $object_id;
		$album_data = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));

		// here you can use $album_data to access data of the post-processed album
		// by default it will print album data into the log:
		print_r($album_data);
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
