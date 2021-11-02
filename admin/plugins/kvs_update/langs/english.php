<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['kvs_update']['title']         = "KVS update";
$lang['plugins']['kvs_update']['description']   = "Provides step by step update assistant for updating KVS to a new version.";
$lang['plugins']['kvs_update']['long_desc']     = "
		This plugin lets you partially automate the update process. You need to upload the archived update that you
		received and specify the MD5 hash of the archive which is shown on the KVS website in the protected customer
		area. The plugin will check whether this archive is suitable for updating your script and it will then give you
		step-by-step instructions. It will also check the completion of each step. If at some point the plugin displays
		an error notification saying the step was not completed, you will need to follow the instructions again.
";
$lang['permissions']['plugins|kvs_update']      = $lang['plugins']['kvs_update']['title'];

$lang['plugins']['kvs_update']['field_step']                            = "Step";
$lang['plugins']['kvs_update']['field_step_value']                      = "%1% of %2%";
$lang['plugins']['kvs_update']['field_description']                     = "Instructions";
$lang['plugins']['kvs_update']['field_description_db']                  = "The database was automatically updated. You can see update log below. You should not have any errors in this log if you are applying this update for the first time.";
$lang['plugins']['kvs_update']['field_update_version']                  = "Update version";
$lang['plugins']['kvs_update']['field_update_info']                     = "Additional info";
$lang['plugins']['kvs_update']['field_custom_changes']                  = "Custom changes";
$lang['plugins']['kvs_update']['field_custom_changes_notice']           = "[kt|b]ATTENTION![/kt|b] It was detected that your project contains custom changes in some files which will be affected by update. If you continue, these changes will be lost without ability to restore them.";
$lang['plugins']['kvs_update']['field_custom_changes_confirm']          = "continue with the update";
$lang['plugins']['kvs_update']['field_mysql_update_summary']            = "Summary";
$lang['plugins']['kvs_update']['field_mysql_update_summary_value']      = "%1% successful updates, %2% errors";
$lang['plugins']['kvs_update']['field_mysql_update_log']                = "DB update log";
$lang['plugins']['kvs_update']['field_get_update']                      = "Get update from";
$lang['plugins']['kvs_update']['field_get_update_hint']                 = "log in with your KVS account and download update ZIP from your license info page";
$lang['plugins']['kvs_update']['field_update_archive']                  = "Update archive";
$lang['plugins']['kvs_update']['field_update_archive_hint']             = "upload update archive you downloaded from KVS customer zone (you can upload using direct link from KVS customer zone)";
$lang['plugins']['kvs_update']['field_validation_hash']                 = "MD5 hash";
$lang['plugins']['kvs_update']['field_validation_hash_hint']            = "copy-paste update archive MD5 hash in order to validate its integrity (you should be able to find this hash in KVS customer zone)";
$lang['plugins']['kvs_update']['field_backup']                          = "Backup";
$lang['plugins']['kvs_update']['field_backup_hint']                     = "backup your project with \"Backup\" plugin available in admin panel";
$lang['plugins']['kvs_update']['field_backup_text']                     = "I did backup";
$lang['plugins']['kvs_update']['field_update_logs']                     = "Previous update logs";
$lang['plugins']['kvs_update']['btn_validate_and_next']                 = "Validate & next";
$lang['plugins']['kvs_update']['btn_continue']                          = "Continue";
$lang['plugins']['kvs_update']['btn_start']                             = "Start";
$lang['plugins']['kvs_update']['btn_finish']                            = "Finish";
$lang['plugins']['kvs_update']['btn_cancel']                            = "Cancel";
$lang['plugins']['kvs_update']['error_unsupported_update_file_format']  = "The uploaded update file is not supported by this plugin";
$lang['plugins']['kvs_update']['error_unsupported_update_version']      = "This update file is intended to be used for [kt|b]%1%[/kt|b] versions, but your project version is [kt|b]%2%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_domain']       = "This update file is intended to be used for [kt|b]%1%[/kt|b] domain, but your project domain is [kt|b]%2%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_multi_db']     = "This update file is intended to be used for [kt|b]%1%[/kt|b] database prefix, but your project database prefix is [kt|b]%2%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_package']      = "This update file is intended to be used for [kt|b]%1%[/kt|b] package, but your project package is [kt|b]%2%[/kt|b]";
$lang['plugins']['kvs_update']['error_unsupported_update_package_1']    = "Basic";
$lang['plugins']['kvs_update']['error_unsupported_update_package_2']    = "Advanced";
$lang['plugins']['kvs_update']['error_unsupported_update_package_3']    = "Premium";
$lang['plugins']['kvs_update']['error_unsupported_update_package_4']    = "Ultimate";
$lang['plugins']['kvs_update']['error_unsupported_source_code1']        = "This update file is intended to be used for project with no source code, but your project has source code";
$lang['plugins']['kvs_update']['error_unsupported_source_code2']        = "This update file is intended to be used for project with source code, but your project has no source code";
$lang['plugins']['kvs_update']['error_invalid_validation_hash']         = "MD5 hash is not valid, please check if you copied it correctly";
$lang['plugins']['kvs_update']['error_no_language_file_available']      = "Update language file is not available, please contact support";
$lang['plugins']['kvs_update']['error_no_stamp']                        = "Project stamp is not available";
$lang['plugins']['kvs_update']['error_backup_is_not_done']              = "[kt|b][%1%][/kt|b]: you must confirm that you've done backup";
$lang['plugins']['kvs_update']['error_not_confirmed']                   = "[kt|b][%1%][/kt|b]: you must confirm that you want to continue";
$lang['plugins']['kvs_update']['error_step_validation_failed']          = "Step validation failed. Please make sure that you exactly followed step instructions.";
$lang['plugins']['kvs_update']['error_step_doesnt_exist']               = "Step doesn't exist";
