<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['categories_autogeneration']['title']          = "Category auto-selection";
$lang['plugins']['categories_autogeneration']['description']    = "Can be used to select categories for videos and albums based on the title, description and tags fields.";
$lang['plugins']['categories_autogeneration']['long_desc']      = "
		Use this to add categories to videos and photo albums automatically based on the title, description and tags
		fields. For this plugin to start working, you need to create a database of categories, as the plugin does not
		create new categories choosing from existing ones instead. Category search is done not just by category names
		but by synonyms as well.
";
$lang['permissions']['plugins|categories_autogeneration']   = $lang['plugins']['categories_autogeneration']['title'];

$lang['plugins']['categories_autogeneration']['field_enable_for_videos']            = "Videos";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_disabled']   = "Disabled";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_always']     = "Process every video";
$lang['plugins']['categories_autogeneration']['field_enable_for_videos_empty']      = "Process only videos with empty categories";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums']            = "Albums";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_disabled']   = "Disabled";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_always']     = "Process every album";
$lang['plugins']['categories_autogeneration']['field_enable_for_albums_empty']      = "Process only albums with empty categories";
$lang['plugins']['categories_autogeneration']['field_lenient']                      = "Lenient match";
$lang['plugins']['categories_autogeneration']['field_lenient_off']                  = "Disabled";
$lang['plugins']['categories_autogeneration']['field_lenient_all']                  = "Enabled for all compound categories and synonyms";
$lang['plugins']['categories_autogeneration']['field_lenient_specific']             = "Enabled for specific categories and synonyms";
$lang['plugins']['categories_autogeneration']['field_lenient_hint1']                = "if enabled, compound categories consisting of several words will not require exact match, they will be selected when all their words are matched together; for example [kt|b]Funny Cat[/kt|b] category will be selected for a content having title [kt|b]Funny video with cat and dog[/kt|b] [kt|br] [kt|b]WARNING![/kt|b] Use wisely. Using this option can select categories incorrectly since lenient match can't be 100% accurate.";
$lang['plugins']['categories_autogeneration']['field_lenient_hint2']                = "specify comma-separated list of categories or their synonyms that should be applicable for lenient match; all other compound categories will match as usual then";
$lang['plugins']['categories_autogeneration']['btn_save']                           = "Save";
