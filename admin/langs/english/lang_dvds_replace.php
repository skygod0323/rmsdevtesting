<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['common']['dvd']                      = "DVD";
$lang['common']['dvds']                     = "DVDs";
$lang['common']['dvds_empty']               = "There are no DVDs selected.";
$lang['common']['dvds_all']                 = "All DVDs...";
$lang['common']['object_type_dvd']          = "DVD";
$lang['common']['object_type_dvds']         = "DVDs";
$lang['common']['object_type_dvd_group']    = "DVD group";
$lang['common']['object_type_dvd_groups']   = "DVD groups";
$lang['common']['dg_filter_usage_dvds']     = "Used in DVDs";
$lang['common']['dg_filter_usage_no_dvds']  = "Not used in DVDs";

$lang['validation']['invalid_dvd']  = "[kt|b][%1%][/kt|b]: the specified DVD cannot be found";

$lang['permissions']['videos|edit_dvd']         = "Edit DVD";
$lang['permissions']['dvds']                    = "DVDs";
$lang['permissions']['dvds|view']               = "View DVDs";
$lang['permissions']['dvds|add']                = "Add DVDs";
$lang['permissions']['dvds|edit_all']           = "Edit DVDs";
$lang['permissions']['dvds|delete']             = "Delete DVDs";
$lang['permissions']['dvds_groups']             = "DVD groups";
$lang['permissions']['dvds_groups|view']        = "View DVD groups";
$lang['permissions']['dvds_groups|add']         = "Add DVD groups";
$lang['permissions']['dvds_groups|edit_all']    = "Edit DVD groups";
$lang['permissions']['dvds_groups|delete']      = "Delete DVD groups";

$lang['start']['stats_global_totals_dvds']          = "DVDs";
$lang['start']['stats_global_totals_dvds_groups']   = "DVD groups";
$lang['start']['alerts_flagged_dvds']               = "There are %1% DVDs flagged with \"%2%\" flag.";
$lang['start']['alerts_dvds_for_review']            = "There are %1% DVDs added or updated, they need to be reviewed.";
$lang['start']['alerts_dvds_flags_messages']        = "There are %1% user feedbacks for DVDs, they need to be processed.";

$lang['videos']['submenu_group_dvds']                               = "DVDs";
$lang['videos']['submenu_option_dvds_list']                         = "DVDs";
$lang['videos']['submenu_option_add_dvd']                           = "Add DVD";
$lang['videos']['submenu_option_dvd_groups_list']                   = "DVD groups";
$lang['videos']['submenu_option_add_dvd_group']                     = "Add DVD group";
$lang['videos']['video_field_dvd']                                  = $lang['common']['dvd'];
$lang['videos']['video_field_dvd_no_group']                         = $lang['common']['no_group'];
$lang['videos']['import_export_field_dvd']                          = $lang['common']['dvd'];
$lang['videos']['import_export_field_dvd_hint']                     = "[text]: ex. [kt|b]DVD Title[/kt|b]";
$lang['videos']['import_export_field_dvd_group']                    = "DVD group";
$lang['videos']['import_export_field_dvd_group_hint']               = "[text]: ex. [kt|b]DVD Group Title[/kt|b]";
$lang['videos']['import_field_new_objects_dvds']                    = "Do not create new DVDs";
$lang['videos']['feed_field_video_dvds']                            = $lang['common']['dvds'];
$lang['videos']['feed_field_video_dvds_empty']                      = $lang['common']['dvds_empty'];
$lang['videos']['feed_field_video_dvds_all']                        = $lang['common']['dvds_all'];
$lang['videos']['feed_field_data_dvd']                              = $lang['common']['dvd'];
$lang['videos']['feed_field_data_dvd_group']                        = "DVD group";
$lang['videos']['feed_field_new_objects_dvds']                      = "Do not create new DVDs";
$lang['videos']['feed_field_videos_dvd']                            = "Video DVD";
$lang['videos']['feed_field_videos_dvd_hint']                       = "select DVD you want to be set for all videos created by this feed";
$lang['videos']['feed_field_options_enable_dvds']                   = "Enable DVDs in feed";
$lang['videos']['mass_edit_videos_field_dvd']                       = $lang['common']['dvd'];
$lang['videos']['video_edit_dvd_link']                              = "Video \"%1%\" from DVD \"%2%\"";
$lang['videos']['export_field_dvds']                                = $lang['common']['dvds'];
$lang['videos']['export_field_dvds_empty']                          = $lang['common']['dvds_empty'];
$lang['videos']['export_field_dvds_all']                            = $lang['common']['dvds_all'];
$lang['videos']['dvd_action_delete_with_videos_confirm']            = "Are you sure to delete DVD \"%1%\"? \\nATTENTION: all videos related to this DVD will be deleted as well. Please type \"yes\" to confirm this action.";
$lang['videos']['dvd_batch_action_delete_with_videos_confirm']      = "Are you sure to delete %1% selected DVD(s)? \\nATTENTION: all videos related to the selected DVDs will be deleted as well. Please type \"yes\" to confirm this action.";
$lang['videos']['dvd_divider_videos']                               = "Videos";
$lang['videos']['dvd_add']                                          = "Add DVD";
$lang['videos']['dvd_edit']                                         = "DVD \"%1%\"";
$lang['videos']['dvd_field_group']                                  = $lang['common']['group'];
$lang['videos']['dvd_field_group_none']                             = $lang['common']['no_group'];
$lang['videos']['dvd_field_status_hint']                            = "disabled DVDs are not displayed in listings and other objects, but remain available via direct URLs";
$lang['videos']['dvd_field_cover1_front']                           = "Cover 1 front";
$lang['videos']['dvd_field_cover1_back']                            = "Cover 1 back";
$lang['videos']['dvd_field_cover2_front']                           = "Cover 2 front";
$lang['videos']['dvd_field_cover2_front_hint2']                     = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_field_cover2_back']                            = "Cover 2 back";
$lang['videos']['dvd_field_cover2_back_hint2']                      = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_field_videos_count']                           = "Videos";
$lang['videos']['dvd_field_tokens_required_hint']                   = "custom price of subscription to this DVD in tokens; [kt|b]0[/kt|b] means default price, configured in memberzone settings (%1% tokens)";
$lang['videos']['dvd_group_add']                                    = "Add DVD group";
$lang['videos']['dvd_group_edit']                                   = "DVD group \"%1%\"";
$lang['videos']['dvd_group_divider_dvds']                           = "DVDs";
$lang['videos']['dvd_group_field_status_hint']                      = "disabled DVD groups are not displayed in listings and other objects, but remain available via direct URLs";
$lang['videos']['dvd_group_field_cover1']                           = "Cover 1";
$lang['videos']['dvd_group_field_cover2']                           = "Cover 2";
$lang['videos']['dvd_group_field_cover2_hint2']                     = "if not uploaded manually will be automatically synced with \"%1%\"";
$lang['videos']['dvd_group_field_add_dvds']                         = $lang['common']['dvds'];
$lang['videos']['dvd_group_field_add_dvds_empty']                   = $lang['common']['dvds_empty'];
$lang['videos']['dvd_group_field_add_dvds_all']                     = $lang['common']['dvds_all'];

$lang['categorization']['flag_field_group_dvds']            = "DVD flags";

$lang['users']['user_field_dvds']                       = "DVDs";
$lang['users']['user_filter_activity_dvds']             = "Have DVDs";
$lang['users']['user_filter_activity_no_dvds']          = "Have no DVDs";

$lang['settings']['system_field_dvd_cover_size']                        = "DVD cover size";
$lang['settings']['system_field_dvd_cover_size_hint']                   = "DVDs support two cover sizes, size #2 can be disabled or can be configured to be created automatically based on image uploaded into size #1";
$lang['settings']['system_field_dvd_group_cover_size']                  = "DVD group cover size";
$lang['settings']['system_field_dvd_group_cover_size_hint']             = "DVD groups support two cover sizes, size #2 can be disabled or can be configured to be created automatically based on image uploaded into size #1";
$lang['settings']['memberzone_awards_col_action_comment_dvd']           = "Approved DVD comment";
$lang['settings']['customization_divider_dvd']                          = "DVDs";
$lang['settings']['customization_divider_dvd_group']                    = "DVD groups";
$lang['settings']['translation_edit_object_type_dvd']                   = "Translation for DVD \"%1%\"";
$lang['settings']['translation_edit_object_type_dvd_group']             = "Translation for DVD group \"%1%\"";
$lang['settings']['translation_divider_dvd_info']                       = "Information about DVD";
$lang['settings']['translation_divider_dvd_group_info']                 = "Information about DVD group";
$lang['settings']['website_field_dvd_website_link_pattern']             = "DVD page URL pattern";
$lang['settings']['website_field_dvd_website_link_pattern_hint']        = "this pattern is used to generate website DVD URLs; may contain [kt|b]%DIR%[/kt|b] token, which will be replaced with the DVD directory, and (or) [kt|b]%ID%[/kt|b] token, which will be replaced with the DVD ID";
$lang['settings']['website_field_dvd_group_website_link_pattern']       = "DVD group page URL pattern";
$lang['settings']['website_field_dvd_group_website_link_pattern_hint']  = "this pattern is used to generate website DVD group URLs; may contain [kt|b]%DIR%[/kt|b] token, which will be replaced with the DVD group directory, and (or) [kt|b]%ID%[/kt|b] token, which will be replaced with the DVD group ID";
$lang['settings']['memberzone_field_tokens_subscribe_dvds']             = "Paid subscriptions to DVDs";
$lang['settings']['memberzone_field_tokens_subscribe_dvds_hint']        = "specify default price in tokens and period in days, price can be overridden for any particular DVD; specifying [kt|b]0[/kt|b] for default price means that subscription will only be enabled for DVDs that have specified price in their settings; specifying empty period means that subscription will never expire";

$lang['stats']['users_awards_field_award_type_dvd_sale']        = "DVD subscription sold";