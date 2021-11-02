<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['common']['dvd']                      = "Season";
$lang['common']['dvds']                     = "Seasons";
$lang['common']['dvds_empty']               = "There are no seasons selected.";
$lang['common']['dvds_all']                 = "All seasons...";
$lang['common']['object_type_dvd']          = "Season";
$lang['common']['object_type_dvds']         = "Seasons";
$lang['common']['object_type_dvd_group']    = "TV series";
$lang['common']['object_type_dvd_groups']   = "TV series";
$lang['common']['dg_filter_usage_dvds']     = "Used in seasons";
$lang['common']['dg_filter_usage_no_dvds']  = "Not used in seasons";

$lang['validation']['invalid_dvd']  = "[kt|b][%1%][/kt|b]: the specified TV series season cannot be found";

$lang['permissions']['videos|edit_dvd']         = "Edit season";
$lang['permissions']['dvds']                    = "Seasons";
$lang['permissions']['dvds|view']               = "View seasons";
$lang['permissions']['dvds|add']                = "Add seasons";
$lang['permissions']['dvds|edit_all']           = "Edit seasons";
$lang['permissions']['dvds|delete']             = "Delete seasons";
$lang['permissions']['dvds_groups']             = "TV series";
$lang['permissions']['dvds_groups|view']        = "View TV series";
$lang['permissions']['dvds_groups|add']         = "Add TV series";
$lang['permissions']['dvds_groups|edit_all']    = "Edit TV series";
$lang['permissions']['dvds_groups|delete']      = "Delete TV series";

$lang['start']['stats_global_totals_dvds']          = "TV series seasons";
$lang['start']['stats_global_totals_dvds_groups']   = "TV series";
$lang['start']['alerts_flagged_dvds']               = "There are %1% seasons flagged with \"%2%\" flag.";
$lang['start']['alerts_dvds_for_review']            = "There are %1% seasons added or updated, they need to be reviewed.";
$lang['start']['alerts_dvds_flags_messages']        = "There are %1% user feedbacks for seasons, they need to be processed.";

$lang['videos']['submenu_group_dvds']                               = "TV series";
$lang['videos']['submenu_option_dvds_list']                         = "Seasons";
$lang['videos']['submenu_option_add_dvd']                           = "Add season";
$lang['videos']['submenu_option_dvd_groups_list']                   = "TV series";
$lang['videos']['submenu_option_add_dvd_group']                     = "Add TV series";
$lang['videos']['video_field_dvd']                                  = $lang['common']['dvd'];
$lang['videos']['video_field_dvd_no_group']                         = "* No TV series specified *";
$lang['videos']['import_export_field_dvd']                          = $lang['common']['dvd'];
$lang['videos']['import_export_field_dvd_hint']                     = "[text]: ex. [kt|b]TV Season title[/kt|b]";
$lang['videos']['import_export_field_dvd_group']                    = "TV series";
$lang['videos']['import_export_field_dvd_group_hint']               = "[text]: ex. [kt|b]TV Series Title[/kt|b]";
$lang['videos']['import_field_new_objects_dvds']                    = "Do not create new seasons";
$lang['videos']['feed_field_video_dvds']                            = $lang['common']['dvds'];
$lang['videos']['feed_field_video_dvds_empty']                      = $lang['common']['dvds_empty'];
$lang['videos']['feed_field_video_dvds_all']                        = $lang['common']['dvds_all'];
$lang['videos']['feed_field_data_dvd']                              = $lang['common']['dvd'];
$lang['videos']['feed_field_data_dvd_group']                        = "TV series";
$lang['videos']['feed_field_new_objects_dvds']                      = "Do not create new seasons";
$lang['videos']['feed_field_videos_dvd']                            = "Video TV series season";
$lang['videos']['feed_field_videos_dvd_no_group']                   = "* No TV series specified *";
$lang['videos']['feed_field_videos_dvd_hint']                       = "select TV series season you want to be set for all videos created by this feed";
$lang['videos']['feed_field_options_enable_dvds']                   = "Enable TV series in feed";
$lang['videos']['mass_edit_videos_field_dvd']                       = $lang['common']['dvd'];
$lang['videos']['video_edit_dvd_link']                              = "Video \"%1%\" from season \"%2%\"";
$lang['videos']['export_field_dvds']                                = $lang['common']['dvds'];
$lang['videos']['export_field_dvds_empty']                          = $lang['common']['dvds_empty'];
$lang['videos']['export_field_dvds_all']                            = $lang['common']['dvds_all'];
$lang['videos']['export_field_dvds']                                = "Seasons";
$lang['videos']['export_field_dvds_empty']                          = "There are no seasons selected.";
$lang['videos']['export_field_dvds_all']                            = "All seasons ...";
$lang['videos']['dvd_action_delete_with_videos_confirm']            = "Are you sure to delete season \"%1%\"? \\nATTENTION: all videos related to this season will be deleted as well. Please type \"yes\" to confirm this action.";
$lang['videos']['dvd_batch_action_delete_with_videos_confirm']      = "Are you sure to delete %1% selected season(s)? \\nATTENTION: all videos related to the selected seasons will be deleted as well. Please type \"yes\" to confirm this action.";
$lang['videos']['dvd_divider_videos']                               = "Episodes";
$lang['videos']['dvd_add']                                          = "Add season";
$lang['videos']['dvd_edit']                                         = "Season \"%1%\"";
$lang['videos']['dvd_field_group']                                  = "TV series";
$lang['videos']['dvd_field_group_none']                             = "* No TV series specified *";
$lang['videos']['dvd_field_status_hint']                            = "disabled seasons are not displayed in listings and other objects, but remain available via direct URLs";
$lang['videos']['dvd_field_cover1_front']                           = "Cover 1 front";
$lang['videos']['dvd_field_cover1_back']                            = "Cover 1 back";
$lang['videos']['dvd_field_cover2_front']                           = "Cover 2 front";
$lang['videos']['dvd_field_cover2_front_hint2']                     = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_field_cover2_back']                            = "Cover 2 back";
$lang['videos']['dvd_field_cover2_back_hint2']                      = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_field_videos_count']                           = "Episodes";
$lang['videos']['dvd_field_tokens_required_hint']                   = "custom price of subscription to this season in tokens; [kt|b]0[/kt|b] means default price, configured in memberzone settings (%1% tokens)";
$lang['videos']['dvd_group_add']                                    = "Add TV series";
$lang['videos']['dvd_group_edit']                                   = "TV series \"%1%\"";
$lang['videos']['dvd_group_divider_dvds']                           = "Seasons";
$lang['videos']['dvd_group_field_status_hint']                      = "disabled TV series are not displayed in listings and other objects, but remain available via direct URLs";
$lang['videos']['dvd_group_field_cover1']                           = "Cover 1";
$lang['videos']['dvd_group_field_cover2']                           = "Cover 2";
$lang['videos']['dvd_group_field_cover2_hint2']                     = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_group_field_add_dvds']                         = $lang['common']['dvds'];
$lang['videos']['dvd_group_field_add_dvds_empty']                   = $lang['common']['dvds_empty'];
$lang['videos']['dvd_group_field_add_dvds_all']                     = $lang['common']['dvds_all'];

$lang['categorization']['flag_field_group_dvds']            = "Season flags";

$lang['users']['user_field_dvds']                       = "Seasons";
$lang['users']['user_filter_activity_dvds']             = "Have seasons";
$lang['users']['user_filter_activity_no_dvds']          = "Have no seasons";

$lang['settings']['system_field_dvd_cover_size']                        = "Season cover size";
$lang['settings']['system_field_dvd_cover_size_hint']                   = "seasons support two cover sizes, size #2 can be disabled or can be configured to be created automatically based on image uploaded into size #1";
$lang['settings']['system_field_dvd_group_cover_size']                  = "TV series cover size";
$lang['settings']['system_field_dvd_group_cover_size_hint']             = "TV series support two cover sizes, size #2 can be disabled or can be configured to be created automatically based on image uploaded into size #1";
$lang['settings']['memberzone_awards_col_action_comment_dvd']           = "Approved season comment";
$lang['settings']['customization_divider_dvd']                          = "Seasons";
$lang['settings']['customization_divider_dvd_group']                    = "TV series";
$lang['settings']['translation_edit_object_type_dvd']                   = "Translation for season \"%1%\"";
$lang['settings']['translation_edit_object_type_dvd_group']             = "Translation for TV series \"%1%\"";
$lang['settings']['translation_divider_dvd_info']                       = "Information about season";
$lang['settings']['translation_divider_dvd_group_info']                 = "Information about TV series";
$lang['settings']['website_field_dvd_website_link_pattern']             = "Season page URL pattern";
$lang['settings']['website_field_dvd_website_link_pattern_hint']        = "this pattern is used to generate website season URLs; may contain [kt|b]%DIR%[/kt|b] token, which will be replaced with the season directory, and (or) [kt|b]%ID%[/kt|b] token, which will be replaced with the season ID";
$lang['settings']['website_field_dvd_group_website_link_pattern']       = "TV series page URL pattern";
$lang['settings']['website_field_dvd_group_website_link_pattern_hint']  = "this pattern is used to generate website TV series URLs; may contain [kt|b]%DIR%[/kt|b] token, which will be replaced with the TV series directory, and (or) [kt|b]%ID%[/kt|b] token, which will be replaced with the TV series ID";
$lang['settings']['memberzone_field_tokens_subscribe_dvds']             = "Paid subscriptions to seasons";
$lang['settings']['memberzone_field_tokens_subscribe_dvds_hint']        = "specify default price in tokens and period in days, price can be overridden for any particular season; specifying [kt|b]0[/kt|b] for default price means that subscription will only be enabled for seasons that have specified price in their settings; specifying empty period means that subscription will never expire";

$lang['stats']['users_awards_field_award_type_dvd_sale']        = "Season subscription sold";