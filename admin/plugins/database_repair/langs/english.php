<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['database_repair']['title']       = "Database status";
$lang['plugins']['database_repair']['description'] = "Checks database for errors and provides ability to repair it using MySQL tools.";
$lang['plugins']['database_repair']['long_desc']   = "
		Use this plugin to see database status and in situations when tables of your database contain errors that you need to fix manually.
";
$lang['permissions']['plugins|database_repair']    = $lang['plugins']['database_repair']['title'];

$lang['plugins']['database_repair']['divider_queries']          = "Active queries";
$lang['plugins']['database_repair']['divider_tables']           = "Tables";
$lang['plugins']['database_repair']['field_database_version']   = "MySQL version";
$lang['plugins']['database_repair']['dg_queries_col_id']        = "ID";
$lang['plugins']['database_repair']['dg_queries_col_command']   = "Command";
$lang['plugins']['database_repair']['dg_queries_col_time']      = "Time";
$lang['plugins']['database_repair']['dg_queries_col_state']     = "State";
$lang['plugins']['database_repair']['dg_queries_col_info']      = "Query";
$lang['plugins']['database_repair']['dg_data_col_table_name']   = "Table name";
$lang['plugins']['database_repair']['dg_data_col_engine']       = "Engine";
$lang['plugins']['database_repair']['dg_data_col_rows']         = "Rows";
$lang['plugins']['database_repair']['dg_data_col_size']         = "Size";
$lang['plugins']['database_repair']['dg_data_col_status']       = "Status";
$lang['plugins']['database_repair']['dg_data_col_message']      = "Message";
$lang['plugins']['database_repair']['btn_kill']                 = "Kill selected queries";
$lang['plugins']['database_repair']['btn_repair']               = "Repair errors";
$lang['plugins']['database_repair']['btn_check_tables']         = "Check tables";
