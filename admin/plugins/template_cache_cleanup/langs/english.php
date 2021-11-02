<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['template_cache_cleanup']['title']         = "Template Cache Cleanup";
$lang['plugins']['template_cache_cleanup']['description']   = "Removes old template cache entries (last used > 5 days ago) on schedule or manually.";
$lang['plugins']['template_cache_cleanup']['long_desc']     = "
		The template cache clearing plugin clears file cache either manually or using a schedule. This plugin can also
		be used to get information about your file cache size and the number of files in it. File cash is used in
		various parts of KVS, but it cannot clear itself. As your site keeps working, the file cache will keep growing.
		We recommend clearing it manually from time to time. If your site`s member area is used by a large number of
		users, we recommend scheduling cache-clearing tasks. Please pay attention that when you launch the clearing
		manually, it will actually be launched within 5 minutes after you submit the form.
";
$lang['permissions']['plugins|template_cache_cleanup']      = $lang['plugins']['template_cache_cleanup']['title'];

$lang['plugins']['template_cache_cleanup']['field_cache_folder']            = "Template cache folder";
$lang['plugins']['template_cache_cleanup']['field_cache_size']              = "Template cache size";
$lang['plugins']['template_cache_cleanup']['field_storage_folder']          = "\$storage cache folder";
$lang['plugins']['template_cache_cleanup']['field_storage_size']            = "\$storage cache size";
$lang['plugins']['template_cache_cleanup']['field_size_check']              = "N/A";
$lang['plugins']['template_cache_cleanup']['field_size_megabytes']          = "Mb";
$lang['plugins']['template_cache_cleanup']['field_size_files']              = "file(s)";
$lang['plugins']['template_cache_cleanup']['field_enable']                  = "Enable schedule";
$lang['plugins']['template_cache_cleanup']['field_enable_enabled']          = "enabled";
$lang['plugins']['template_cache_cleanup']['field_schedule']                = "Schedule";
$lang['plugins']['template_cache_cleanup']['field_schedule_interval']       = "min interval (h)";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod']            = "time of day";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod_any']        = "any, as soon as possible";
$lang['plugins']['template_cache_cleanup']['field_schedule_hint']           = "specify minimum interval for this plugin execution and specific time of day if needed; please note that specific time of day is not 100% guaranteed and plugin may be started some time later on the same day, but not earlier than the specified hour";
$lang['plugins']['template_cache_cleanup']['field_last_exec']               = "Last executed";
$lang['plugins']['template_cache_cleanup']['field_last_exec_none']          = "none";
$lang['plugins']['template_cache_cleanup']['field_last_exec_seconds']       = "seconds";
$lang['plugins']['template_cache_cleanup']['field_last_exec_files']         = "files removed";
$lang['plugins']['template_cache_cleanup']['field_next_exec']               = "Next execution";
$lang['plugins']['template_cache_cleanup']['field_next_exec_none']          = "none";
$lang['plugins']['template_cache_cleanup']['btn_save']                      = "Save";
$lang['plugins']['template_cache_cleanup']['btn_calculate_stats']           = "Check cache size";
$lang['plugins']['template_cache_cleanup']['btn_start_now']                 = "Clean-up now";
