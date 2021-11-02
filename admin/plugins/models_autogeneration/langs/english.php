<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['models_autogeneration']['title']          = "Model auto-selection";
$lang['plugins']['models_autogeneration']['description']    = "Can be used to select models for videos and albums based on the title, description and tags fields.";
$lang['plugins']['models_autogeneration']['long_desc']      = "
		Use this to add models to videos and photo albums automatically based on the title, description and tags fields.
		For this plugin to start working, you need to create a database of models, as the plugin does not create new
		models choosing from existing ones instead. Model search is done not just by model names but by all their
		pseudonyms as well.
";
$lang['permissions']['plugins|models_autogeneration']   = $lang['plugins']['models_autogeneration']['title'];

$lang['plugins']['models_autogeneration']['field_enable_for_videos']            = "Videos";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_disabled']   = "Disabled";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_always']     = "Process every video";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_empty']      = "Process only videos with empty models";
$lang['plugins']['models_autogeneration']['field_enable_for_albums']            = "Albums";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_disabled']   = "Disabled";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_always']     = "Process every album";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_empty']      = "Process only albums with empty models";
$lang['plugins']['models_autogeneration']['btn_save']                           = "Save";
