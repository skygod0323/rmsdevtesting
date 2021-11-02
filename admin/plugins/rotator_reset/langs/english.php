<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['rotator_reset']['title']          = "Rotator stats reset";
$lang['plugins']['rotator_reset']['description']    = "Provides ability to reset rotator stats.";
$lang['plugins']['rotator_reset']['long_desc']      = "
		Use this plugin to reset all rotator data except for weighting matrixes of click distribution. You need to
		select the types of data to be reset. This is a background operation that may take some time to complete.
";
$lang['permissions']['plugins|rotator_reset']       = $lang['plugins']['rotator_reset']['title'];

$lang['plugins']['rotator_reset']['field_reset_videos']             = "Reset videos stats";
$lang['plugins']['rotator_reset']['field_reset_videos_hint']        = "this option will reset rotator stats for all videos; after these stats are cleaned up, all videos will have the same CTR";
$lang['plugins']['rotator_reset']['field_reset_screenshots']        = "Reset videos screenshots stats";
$lang['plugins']['rotator_reset']['field_reset_screenshots_hint']   = "this option will reset rotator stats for screenshots of all videos; after these stats are cleaned up, all screenshots for all videos will have the same CTR";
$lang['plugins']['rotator_reset']['btn_reset']                      = "Reset";
