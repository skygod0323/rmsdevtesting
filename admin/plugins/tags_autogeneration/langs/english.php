<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['tags_autogeneration']['title']        = "Tag auto-selection";
$lang['plugins']['tags_autogeneration']['description']  = "Can be used to select tags for videos and albums based on title and description fields.";
$lang['plugins']['tags_autogeneration']['long_desc']    = "
		Use this to add tags to videos and photo albums automatically based on the title and description fields. For
		this plugin to start working, you need to create a database of tags, as the plugin does not create new tags
		choosing from existing ones instead. Model search is done not just by tag names but by synonyms as well.
";
$lang['permissions']['plugins|tags_autogeneration']     = $lang['plugins']['tags_autogeneration']['title'];

$lang['plugins']['tags_autogeneration']['field_enable_for_videos']          = "Videos";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_disabled'] = "Disabled";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_always']   = "Process every video";
$lang['plugins']['tags_autogeneration']['field_enable_for_videos_empty']    = "Process only videos with empty tags";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums']          = "Albums";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_disabled'] = "Disabled";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_always']   = "Process every album";
$lang['plugins']['tags_autogeneration']['field_enable_for_albums_empty']    = "Process only albums with empty tags";
$lang['plugins']['tags_autogeneration']['field_lenient']                    = "Lenient match";
$lang['plugins']['tags_autogeneration']['field_lenient_off']                = "Disabled";
$lang['plugins']['tags_autogeneration']['field_lenient_all']                = "Enabled for all compound tags and synonyms";
$lang['plugins']['tags_autogeneration']['field_lenient_specific']           = "Enabled for specific tags and synonyms";
$lang['plugins']['tags_autogeneration']['field_lenient_hint1']              = "if enabled, compound tags consisting of several words will not require exact match, they will be selected when all their words are matched together; for example [kt|b]funny cat[/kt|b] tag will be selected for a content having title [kt|b]Funny video with cat and dog[/kt|b] [kt|br] [kt|b]WARNING![/kt|b] Use wisely. Using this option can select tags incorrectly since lenient match can't be 100% accurate.";
$lang['plugins']['tags_autogeneration']['field_lenient_hint2']              = "specify comma-separated list of tags or their synonyms that should be applicable for lenient match; all other compound tags will match as usual then";
$lang['plugins']['tags_autogeneration']['btn_save']                         = "Save";
