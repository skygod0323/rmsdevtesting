<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['migrator']['title']       = "Migrator";
$lang['plugins']['migrator']['description'] = "Migrates data from other KVS installation or 3rd-party script.";
$lang['plugins']['migrator']['long_desc']   = "
		You can use this plugin to fully migrate categorization data from your other KVS project. If you want to
		migrate your other project to KVS from a 3rd-party script, please contact our support.
";
$lang['permissions']['plugins|migrator']    = $lang['plugins']['migrator']['title'];

$lang['plugins']['migrator']['error_php_mysqli']                                        = "MySQLi PHP module is not installed";
$lang['plugins']['migrator']['error_failed_to_connect']                                 = "Failed to connect to MySQL (%1%)";
$lang['plugins']['migrator']['divider_parameters']                                      = "Migration options";
$lang['plugins']['migrator']['divider_summary']                                         = "Migration summary";
$lang['plugins']['migrator']['divider_recent_migrations']                               = "Recent migrations";
$lang['plugins']['migrator']['divider_recent_migrations_none']                          = "There are no migrations executed recently.";
$lang['plugins']['migrator']['field_old_script']                                        = "Old script";
$lang['plugins']['migrator']['field_old_path']                                          = "Old path";
$lang['plugins']['migrator']['field_old_path_hint']                                     = "absolute path where old project is installed, will be used to copy files instead of downloading; example: [kt|b]/path/to/domain.com[/kt|b]";
$lang['plugins']['migrator']['field_old_url']                                           = "Old URL";
$lang['plugins']['migrator']['field_old_url_hint']                                      = "old project domain URL; example: [kt|b]http://domain.com[/kt|b]";
$lang['plugins']['migrator']['field_old_mysql_url']                                     = "Old MySQL URL";
$lang['plugins']['migrator']['field_old_mysql_port']                                    = "Old MySQL port";
$lang['plugins']['migrator']['field_old_mysql_user']                                    = "Old MySQL user";
$lang['plugins']['migrator']['field_old_mysql_pass']                                    = "Old MySQL password";
$lang['plugins']['migrator']['field_old_mysql_name']                                    = "Old MySQL database";
$lang['plugins']['migrator']['field_old_mysql_charset']                                 = "Old MySQL charset";
$lang['plugins']['migrator']['field_migrate_data']                                      = "Data to migrate";
$lang['plugins']['migrator']['field_migrate_data_tags']                                 = "Tags";
$lang['plugins']['migrator']['field_migrate_data_categories']                           = "Categories";
$lang['plugins']['migrator']['field_migrate_data_models']                               = "Models";
$lang['plugins']['migrator']['field_migrate_data_content_sources']                      = "Content sources";
$lang['plugins']['migrator']['field_migrate_data_dvds']                                 = "Channels / DVDs / TV series";
$lang['plugins']['migrator']['field_migrate_data_videos']                               = "Videos";
$lang['plugins']['migrator']['field_migrate_data_videos_screenshots']                   = "Screenshots for videos";
$lang['plugins']['migrator']['field_migrate_data_albums']                               = "Albums";
$lang['plugins']['migrator']['field_migrate_data_comments']                             = "Comments";
$lang['plugins']['migrator']['field_migrate_data_users']                                = "Users";
$lang['plugins']['migrator']['field_migrate_data_favourites']                           = "Favourites";
$lang['plugins']['migrator']['field_migrate_data_friends']                              = "Friends";
$lang['plugins']['migrator']['field_migrate_data_messages']                             = "Internal messages";
$lang['plugins']['migrator']['field_migrate_data_subscriptions']                        = "Subscriptions";
$lang['plugins']['migrator']['field_migrate_data_playlists']                            = "Playlists";
$lang['plugins']['migrator']['field_override_objects']                                  = "Existing objects";
$lang['plugins']['migrator']['field_override_objects_yes']                              = "Replace objects with the same IDs with migrated objects";
$lang['plugins']['migrator']['field_override_objects_hint']                             = "[kt|b]ATTENTION![/kt|b] If your current database is not empty, enabling this option may replace some objects if they have same IDs as objects in the old database. Please use with care.";
$lang['plugins']['migrator']['field_upload_hotlinked_videos']                           = "Hotlinked videos";
$lang['plugins']['migrator']['field_upload_hotlinked_videos_yes']                       = "Upload hotlinked videos";
$lang['plugins']['migrator']['field_upload_hotlinked_videos_hint']                      = "enable this option if you want to upload hotlinked videos into KVS storage instead of hotlinking them";
$lang['plugins']['migrator']['field_test_mode']                                         = "Test mode";
$lang['plugins']['migrator']['field_test_mode_enabled']                                 = "limit to";
$lang['plugins']['migrator']['field_test_mode_hint']                                    = "specify the maximum number of videos / albums to be migrated";
$lang['plugins']['migrator']['field_options']                                           = "Additional options";
$lang['plugins']['migrator']['field_options_name']                                      = "Name";
$lang['plugins']['migrator']['field_options_value']                                     = "Value";
$lang['plugins']['migrator']['field_summary_duration']                                  = "Duration";
$lang['plugins']['migrator']['field_summary_duration_value']                            = "%1% seconds";
$lang['plugins']['migrator']['field_summary_memory']                                    = "Memory";
$lang['plugins']['migrator']['field_summary_memory_bytes']                              = "%1% B";
$lang['plugins']['migrator']['field_summary_memory_kilobytes']                          = "%1% Kb";
$lang['plugins']['migrator']['field_summary_memory_megabytes']                          = "%1% Mb";
$lang['plugins']['migrator']['field_summary_log']                                       = "Log";
$lang['plugins']['migrator']['dg_summary_col_objects']                                  = "Objects";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['users']                  = "Users";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['category_groups']        = "Category groups";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['categories']             = "Categories";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['tags']                   = "Tags";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['content_source_groups']  = "Content source groups";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['content_sources']        = "Content sources";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['models']                 = "Models";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['model_groups']           = "Model groups";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['dvd_groups']             = "Channel groups / DVD groups / TV series";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['dvds']                   = "Channels / DVDs / TV seasons";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['videos']                 = "Videos";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['albums']                 = "Albums";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['comments']               = "Comments";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['fav_videos']             = "Favourite videos";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['fav_albums']             = "Favourite albums";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['friends']                = "Friends";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['messages']               = "Messages";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['subscriptions']          = "Subscriptions";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['playlists']              = "Playlists";
$lang['plugins']['migrator']['dg_summary_col_total']                                    = "Total";
$lang['plugins']['migrator']['dg_summary_col_inserted']                                 = "Inserted";
$lang['plugins']['migrator']['dg_summary_col_updated']                                  = "Updated";
$lang['plugins']['migrator']['dg_summary_col_errors']                                   = "Errors";
$lang['plugins']['migrator']['dg_recent_migrations_col_time']                           = "Executed";
$lang['plugins']['migrator']['dg_recent_migrations_col_results']                        = "Results";
$lang['plugins']['migrator']['dg_recent_migrations_col_results_value']                  = "%1% inserted, %2% updated, %3% errors";
$lang['plugins']['migrator']['dg_recent_migrations_col_results_in_process']             = "In process: %1%% done";
$lang['plugins']['migrator']['dg_recent_migrations_col_log']                            = "Log";
$lang['plugins']['migrator']['btn_start']                                               = "Start migration";
