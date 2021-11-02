<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['audit_log_analyzer']['title']         = "Audit log analysis";
$lang['plugins']['audit_log_analyzer']['description']   = "Provides ability to analyze audit log in different aspects and get summaries over it.";
$lang['plugins']['audit_log_analyzer']['long_desc']     = "
		This plugin is mainly used to calculate the amount of work your content managers, writers, and translators have
		completed for any given period of time. The calculations are based on the audit log that logs all content
		creation and editing activity.
";
$lang['permissions']['plugins|audit_log_analyzer']  = $lang['plugins']['audit_log_analyzer']['title'];

$lang['plugins']['audit_log_analyzer']['divider_parameters']                    = "Parameters";
$lang['plugins']['audit_log_analyzer']['divider_summary']                       = "Summary";
$lang['plugins']['audit_log_analyzer']['field_period']                          = "Period";
$lang['plugins']['audit_log_analyzer']['field_period_type_today']               = "Today";
$lang['plugins']['audit_log_analyzer']['field_period_type_yesterday']           = "Yesterday";
$lang['plugins']['audit_log_analyzer']['field_period_type_last_7days']          = "Last 7 days";
$lang['plugins']['audit_log_analyzer']['field_period_type_last_30days']         = "Last 30 days";
$lang['plugins']['audit_log_analyzer']['field_period_type_prev_month']          = "Previous month";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom']              = "Custom period";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom_from']         = "from";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom_to']           = "to";
$lang['plugins']['audit_log_analyzer']['field_admins']                          = "Administrators";
$lang['plugins']['audit_log_analyzer']['field_admins_empty']                    = "There are no administrators selected.";
$lang['plugins']['audit_log_analyzer']['field_admins_all']                      = "All administrators...";
$lang['plugins']['audit_log_analyzer']['field_users']                           = "Site users";
$lang['plugins']['audit_log_analyzer']['field_users_empty']                     = "There are no users selected.";
$lang['plugins']['audit_log_analyzer']['field_users_all']                       = "All users...";
$lang['plugins']['audit_log_analyzer']['field_results_period']                  = "Period";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_added']           = "Videos added";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_added']           = "Albums added";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_added']            = "Posts added";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_added']            = "Other objects added";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_modified']        = "Videos modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_modified']        = "Albums modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_modified']         = "Posts modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_modified']         = "Other objects modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_vs_modified']            = "Videos screenshots modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_ai_modified']            = "Albums images modified";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_deleted']         = "Videos deleted";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_deleted']         = "Albums deleted";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_deleted']          = "Posts deleted";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_deleted']          = "Other objects deleted";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_translated']      = "Videos translated";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_translated']      = "Albums translated";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_translated']       = "Other objects translated";
$lang['plugins']['audit_log_analyzer']['dg_results_col_text_symbols']           = "Title + desc symbols sum";
$lang['plugins']['audit_log_analyzer']['dg_results_col_translation_symbols']    = "Translation symbols sum";
$lang['plugins']['audit_log_analyzer']['btn_calculate']                         = "Calculate";
