<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['custom_post_processing']['title']          = "Custom post-processing";
$lang['plugins']['custom_post_processing']['description']    = "You can add your custom post-processing logic for videos / albums into this plugin.";
$lang['plugins']['custom_post_processing']['long_desc']      = "
		This plugin lets you add your custom post-processing logic into videos and albums, which will be executed right
		after video / album processing is finished by KVS. In order to add your logic you should put your code into
		[kt|b]/admin/plugins/custom_post_processing/custom_post_processing.php[/kt|b] file in the places identified by
		comments for videos and albums. Also you should modify [kt|b]custom_post_processingIsEnabled[/kt|b] function
		in this file to return true.
		[kt|br][kt|br]
		If you need this custom logic to be executed for existing videos or albums, use mass edit functionality from
		admin panel, which can manually execute this plugin for the selected set of videos or albums.
";
$lang['permissions']['plugins|custom_post_processing']   = $lang['plugins']['custom_post_processing']['title'];
