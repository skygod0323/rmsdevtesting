<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['avatars_generation']['title']         = "Avatar generation";
$lang['plugins']['avatars_generation']['description']   = "Generates avatars for categories based on videos / albums in these categories.";
$lang['plugins']['avatars_generation']['long_desc']     = "
		The avatar-generating plugin creates avatars for categories based on videos or albums in these categories. For
		each category, only one video / album is selected, and the main image of this video / album (more specifically,
		its source file) will be used as the avatar for this category. In plugin settings, you can choose
		the sorting method used to choose a category`s main video / album. You also need to specify the ImageMagick
		options string that will be used to resize source files to the avatar size.
";
$lang['permissions']['plugins|avatars_generation']      = $lang['plugins']['avatars_generation']['title'];

$lang['plugins']['avatars_generation']['field_enable']                          = "Enable generation";
$lang['plugins']['avatars_generation']['field_enable_disabled']                 = "Disabled";
$lang['plugins']['avatars_generation']['field_enable_videos']                   = "From videos";
$lang['plugins']['avatars_generation']['field_enable_albums']                   = "From albums";
$lang['plugins']['avatars_generation']['field_videos_rule']                     = "Video selection rule";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_all']      = "Most popular video (all time)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_month']    = "Most popular video (this month)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_week']     = "Most popular video (this week)";
$lang['plugins']['avatars_generation']['field_videos_rule_popularity_day']      = "Most popular video (today)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_all']          = "Top rated video (all time)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_month']        = "Top rated video (this month)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_week']         = "Top rated video (this week)";
$lang['plugins']['avatars_generation']['field_videos_rule_rating_day']          = "Top rated video (today)";
$lang['plugins']['avatars_generation']['field_videos_rule_most_commented']      = "Most commented video";
$lang['plugins']['avatars_generation']['field_videos_rule_most_favourited']     = "Most favourited video";
$lang['plugins']['avatars_generation']['field_videos_rule_post_date']           = "Newest video";
$lang['plugins']['avatars_generation']['field_videos_rule_ctr']                 = "CTR (rotator)";
$lang['plugins']['avatars_generation']['field_albums_rule']                     = "Album selection rule";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_all']      = "Most popular album (all time)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_month']    = "Most popular album (this month)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_week']     = "Most popular album (this week)";
$lang['plugins']['avatars_generation']['field_albums_rule_popularity_day']      = "Most popular album (today)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_all']          = "Top rated album (all time)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_month']        = "Top rated album (this month)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_week']         = "Top rated album (this week)";
$lang['plugins']['avatars_generation']['field_albums_rule_rating_day']          = "Top rated album (today)";
$lang['plugins']['avatars_generation']['field_albums_rule_most_commented']      = "Most commented album";
$lang['plugins']['avatars_generation']['field_albums_rule_most_favourited']     = "Most favourited album";
$lang['plugins']['avatars_generation']['field_albums_rule_post_date']           = "Newest album";
$lang['plugins']['avatars_generation']['field_im_options']                      = "ImageMagick options";
$lang['plugins']['avatars_generation']['field_im_options_hint']                 = "ImageMagick options should contain [kt|b]%INPUT_FILE%[/kt|b], [kt|b]%OUTPUT_FILE%[/kt|b] and [kt|b]%SIZE%[/kt|b] tokens, which will be replaced with the real filenames and size during avatars creation";
$lang['plugins']['avatars_generation']['field_crop_options']                    = "Crop options";
$lang['plugins']['avatars_generation']['field_crop_options_left']               = "left";
$lang['plugins']['avatars_generation']['field_crop_options_top']                = "top";
$lang['plugins']['avatars_generation']['field_crop_options_right']              = "right";
$lang['plugins']['avatars_generation']['field_crop_options_bottom']             = "bottom";
$lang['plugins']['avatars_generation']['field_schedule']                        = "Schedule";
$lang['plugins']['avatars_generation']['field_schedule_interval']               = "min interval (h)";
$lang['plugins']['avatars_generation']['field_schedule_tod']                    = "time of day";
$lang['plugins']['avatars_generation']['field_schedule_tod_any']                = "any, as soon as possible";
$lang['plugins']['avatars_generation']['field_schedule_hint']                   = "specify minimum interval for this plugin execution and specific time of day if needed; please note that specific time of day is not 100% guaranteed and plugin may be started some time later on the same day, but not earlier than the specified hour";
$lang['plugins']['avatars_generation']['field_last_exec']                       = "Last executed";
$lang['plugins']['avatars_generation']['field_last_exec_none']                  = "none";
$lang['plugins']['avatars_generation']['field_last_exec_seconds']               = "seconds";
$lang['plugins']['avatars_generation']['field_next_exec']                       = "Next execution";
$lang['plugins']['avatars_generation']['field_next_exec_none']                  = "none";
$lang['plugins']['avatars_generation']['btn_save']                              = "Save";
$lang['plugins']['avatars_generation']['btn_regenerate']                        = "Save & re-generate now";
