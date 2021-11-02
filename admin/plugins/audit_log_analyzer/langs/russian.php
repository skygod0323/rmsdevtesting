<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['audit_log_analyzer']['title']         = "Анализ лога аудита";
$lang['plugins']['audit_log_analyzer']['description']   = "Позволяет анализировать лог аудита в различных аспектах и получать суммарные данные.";
$lang['plugins']['audit_log_analyzer']['long_desc']     = "
		Основное назначение этого плагина - это вычислить объем работы, выполненный вашими контент менеджерами /
		дескописателями / переводчиками за любые периоды времени. Вычисления базируются на логе аудита, куда попадает
		вся информация о создании и изменении контента.
";
$lang['permissions']['plugins|audit_log_analyzer']  = $lang['plugins']['audit_log_analyzer']['title'];

$lang['plugins']['audit_log_analyzer']['divider_parameters']                    = "Параметры";
$lang['plugins']['audit_log_analyzer']['divider_summary']                       = "Результаты";
$lang['plugins']['audit_log_analyzer']['field_period']                          = "Период";
$lang['plugins']['audit_log_analyzer']['field_period_type_today']               = "Сегодня";
$lang['plugins']['audit_log_analyzer']['field_period_type_yesterday']           = "Вчера";
$lang['plugins']['audit_log_analyzer']['field_period_type_last_7days']          = "Последние 7 дней";
$lang['plugins']['audit_log_analyzer']['field_period_type_last_30days']         = "Последние 30 дней";
$lang['plugins']['audit_log_analyzer']['field_period_type_prev_month']          = "Предыдущий месяц";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom']              = "Любой период";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom_from']         = "с";
$lang['plugins']['audit_log_analyzer']['field_period_type_custom_to']           = "по";
$lang['plugins']['audit_log_analyzer']['field_admins']                          = "Администраторы";
$lang['plugins']['audit_log_analyzer']['field_admins_empty']                    = "Администраторы не выбраны.";
$lang['plugins']['audit_log_analyzer']['field_admins_all']                      = "Все администраторы...";
$lang['plugins']['audit_log_analyzer']['field_users']                           = "Пользователи сайта";
$lang['plugins']['audit_log_analyzer']['field_users_empty']                     = "Пользователи не выбраны.";
$lang['plugins']['audit_log_analyzer']['field_users_all']                       = "Все пользователи...";
$lang['plugins']['audit_log_analyzer']['field_results_period']                  = "Период";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_added']           = "Добавлено видео";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_added']           = "Добавлено альбомов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_added']            = "Добавлено записей";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_added']            = "Добавлено других объектов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_modified']        = "Изменено видео";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_modified']        = "Изменено альбомов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_modified']         = "Изменено записей";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_modified']         = "Изменено других объектов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_vs_modified']            = "Изменено скриншотов у видео";
$lang['plugins']['audit_log_analyzer']['dg_results_col_ai_modified']            = "Изменено изображений у альбомов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_deleted']         = "Удалено видео";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_deleted']         = "Удалено альбомов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_posts_deleted']          = "Удалено записей";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_deleted']          = "Удалено других объектов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_videos_translated']      = "Переведено видео";
$lang['plugins']['audit_log_analyzer']['dg_results_col_albums_translated']      = "Переведено альбомов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_other_translated']       = "Переведено других объектов";
$lang['plugins']['audit_log_analyzer']['dg_results_col_text_symbols']           = "Сумма символов в названии / описании";
$lang['plugins']['audit_log_analyzer']['dg_results_col_translation_symbols']    = "Сумма символов в переводах";
$lang['plugins']['audit_log_analyzer']['btn_calculate']                         = "Вычислить";
