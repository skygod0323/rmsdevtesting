<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['common']['dvd']                      = "Сезон";
$lang['common']['dvds']                     = "Сезоны";
$lang['common']['dvds_empty']               = "Сезоны не выбраны.";
$lang['common']['dvds_all']                 = "Все сезоны...";
$lang['common']['object_type_dvd']          = "Сезон";
$lang['common']['object_type_dvds']         = "Сезоны";
$lang['common']['object_type_dvd_group']    = "Сериал";
$lang['common']['object_type_dvd_groups']   = "Сериалы";
$lang['common']['dg_filter_usage_dvds']     = "Используется в сезонах";
$lang['common']['dg_filter_usage_no_dvds']  = "Не используется в сезонах";

$lang['validation']['invalid_dvd']  = "[kt|b][%1%][/kt|b]: указанного сезона сериала не существует";

$lang['permissions']['videos|edit_dvd']         = "Редакт. сезона";
$lang['permissions']['dvds']                    = "Сезоны сериалов";
$lang['permissions']['dvds|view']               = "Просмотр сезонов";
$lang['permissions']['dvds|add']                = "Добавл. сезонов";
$lang['permissions']['dvds|edit_all']           = "Редакт. сезонов";
$lang['permissions']['dvds|delete']             = "Удаление сезонов";
$lang['permissions']['dvds_groups']             = "Сериалы";
$lang['permissions']['dvds_groups|view']        = "Просмотр сериалов";
$lang['permissions']['dvds_groups|add']         = "Добавл. сериалов";
$lang['permissions']['dvds_groups|edit_all']    = "Редакт. сериалов";
$lang['permissions']['dvds_groups|delete']      = "Удаление сериалов";

$lang['start']['stats_global_totals_dvds']          = "Сезоны сериалов";
$lang['start']['stats_global_totals_dvds_groups']   = "Сериалы";
$lang['start']['alerts_flagged_dvds']               = "%1% сезонов помечены флагом \"%2%\".";
$lang['start']['alerts_dvds_for_review']            = "Необходимо просмотреть %1% пользовательских сезонов, которые были добавлены или обновлены.";
$lang['start']['alerts_dvds_flags_messages']        = "Необходимо обработать %1% отзывов по сезонам.";

$lang['videos']['submenu_group_dvds']                               = "Сериалы";
$lang['videos']['submenu_option_dvds_list']                         = "Сезоны";
$lang['videos']['submenu_option_add_dvd']                           = "Добавить сезон";
$lang['videos']['submenu_option_dvd_groups_list']                   = "Сериалы";
$lang['videos']['submenu_option_add_dvd_group']                     = "Добавить сериал";
$lang['videos']['video_field_dvd']                                  = $lang['common']['dvd'];
$lang['videos']['video_field_dvd_no_group']                         = "* Не указан сериал *";
$lang['videos']['import_export_field_dvd']                          = $lang['common']['dvd'];
$lang['videos']['import_export_field_dvd_hint']                     = "[текст]: например, [kt|b]Название сезона[/kt|b]";
$lang['videos']['import_export_field_dvd_group']                    = "Сериал";
$lang['videos']['import_export_field_dvd_group_hint']               = "[text]: например, [kt|b]Название сериала[/kt|b]";
$lang['videos']['import_field_new_objects_dvds']                    = "Не создавать новые сезоны сериалов";
$lang['videos']['feed_field_video_dvds']                            = $lang['common']['dvds'];
$lang['videos']['feed_field_video_dvds_empty']                      = $lang['common']['dvds_empty'];
$lang['videos']['feed_field_video_dvds_all']                        = $lang['common']['dvds_all'];
$lang['videos']['feed_field_data_dvd']                              = $lang['common']['dvd'];
$lang['videos']['feed_field_data_dvd_group']                        = "Сериал";
$lang['videos']['feed_field_new_objects_dvds']                      = "Не создавать новые сезоны сериалов";
$lang['videos']['feed_field_videos_dvd']                            = "Сезон видео";
$lang['videos']['feed_field_videos_dvd_no_group']                   = "* Не указан сериал *";
$lang['videos']['feed_field_videos_dvd_hint']                       = "выберите сезон сериала, который вы хотите установить для всех видео, создаваемых этим фидом";
$lang['videos']['feed_field_options_enable_dvds']                   = "Разрешить сериалы в фиде";
$lang['videos']['mass_edit_videos_field_dvd']                       = $lang['common']['dvd'];
$lang['videos']['video_edit_dvd_link']                              = "Видео \"%1%\" из сезона \"%2%\"";
$lang['videos']['export_field_dvds']                                = $lang['common']['dvds'];
$lang['videos']['export_field_dvds_empty']                          = $lang['common']['dvds_empty'];
$lang['videos']['export_field_dvds_all']                            = $lang['common']['dvds_all'];
$lang['videos']['dvd_action_delete_with_videos_confirm']            = "Вы уверены, что хотите удалить сезон \"%1%\"?\\nВНИМАНИЕ: все серии, привязанные к этому сезону, будут также удалены. Пожалуйста, напишите \"yes\" чтобы подтвердить это действие.";
$lang['videos']['dvd_batch_action_delete_with_videos_confirm']      = "Вы уверены, что хотите удалить %1% сезон(ов)?\\nВНИМАНИЕ: все серии, привязанные к выбранным сезонам, будут также удалены. Пожалуйста, напишите \"yes\" чтобы подтвердить это действие.";
$lang['videos']['dvd_divider_videos']                               = "Серии";
$lang['videos']['dvd_add']                                          = "Добавление сезона";
$lang['videos']['dvd_edit']                                         = "Сезон \"%1%\"";
$lang['videos']['dvd_field_group']                                  = "Сериал";
$lang['videos']['dvd_field_group_none']                             = "* Не указан сериал *";
$lang['videos']['dvd_field_status_hint']                            = "неактивные сезоны не выводятся на списках и в других объектах, но остаются доступными по прямым ссылкам";
$lang['videos']['dvd_field_cover1_front']                           = "Обложка 1 лицевая";
$lang['videos']['dvd_field_cover1_back']                            = "Обложка 1 оборотная";
$lang['videos']['dvd_field_cover2_front']                           = "Обложка 2 лицевая";
$lang['videos']['dvd_field_cover2_front_hint2']                     = "если не загружена вручную, то будет синхронизирована автоматически с изображением \"%1%\"";
$lang['videos']['dvd_field_cover2_back']                            = "Обложка 2 оборотная";
$lang['videos']['dvd_field_cover2_back_hint2']                      = "если не загружена вручную, то будет синхронизирована автоматически с изображением \"%1%\"";
$lang['videos']['dvd_field_videos_count']                           = "Серии";
$lang['videos']['dvd_field_tokens_required_hint']                   = "стоимость подписки на этот сезон в токенах; значение [kt|b]0[/kt|b] означает использование стоимости по умолчанию, которая устанавливается в настройках мемберзоны (%1% токенов)";
$lang['videos']['dvd_group_add']                                    = "Добавление сериала";
$lang['videos']['dvd_group_edit']                                   = "Сериал \"%1%\"";
$lang['videos']['dvd_group_divider_dvds']                           = "Сезоны";
$lang['videos']['dvd_group_field_status_hint']                      = "неактивные сериалы не выводятся на списках и в других объектах, но остаются доступными по прямым ссылкам";
$lang['videos']['dvd_group_field_cover1']                           = "Обложка 1";
$lang['videos']['dvd_group_field_cover2']                           = "Обложка 2";
$lang['videos']['dvd_group_field_cover2_hint2']                     = "если не загружена вручную, то будет синхронизирована автоматически с изображением \"%1%\"";
$lang['videos']['dvd_group_field_add_dvds']                         = $lang['common']['dvds'];
$lang['videos']['dvd_group_field_add_dvds_empty']                   = $lang['common']['dvds_empty'];
$lang['videos']['dvd_group_field_add_dvds_all']                     = $lang['common']['dvds_all'];

$lang['categorization']['flag_field_group_dvds']            = "Флаги для сезонов";

$lang['users']['user_field_dvds']                       = "Сезоны";
$lang['users']['user_filter_activity_dvds']             = "Создали сезоны";
$lang['users']['user_filter_activity_no_dvds']          = "Не создавали сезонов";

$lang['settings']['system_field_dvd_cover_size']                        = "Размер обложки сезона";
$lang['settings']['system_field_dvd_cover_size_hint']                   = "сезоны поддерживают два размера обложки, размер #2 может быть выключен, либо настроен на автоматическое создание на базе размера #1";
$lang['settings']['system_field_dvd_group_cover_size']                  = "Размер обложки сериала";
$lang['settings']['system_field_dvd_group_cover_size_hint']             = "сериалы поддерживают два размера обложки, размер #2 может быть выключен, либо настроен на автоматическое создание на базе размера #1";
$lang['settings']['memberzone_awards_col_action_comment_dvd']           = "Утвержденный комментарий к сезону";
$lang['settings']['customization_divider_dvd']                          = "Сезоны";
$lang['settings']['customization_divider_dvd_group']                    = "Сериалы";
$lang['settings']['translation_edit_object_type_dvd']                   = "Перевод сезона \"%1%\"";
$lang['settings']['translation_edit_object_type_dvd_group']             = "Перевод сериала \"%1%\"";
$lang['settings']['translation_divider_dvd_info']                       = "Информация о сезоне";
$lang['settings']['translation_divider_dvd_group_info']                 = "Информация о сериале";
$lang['settings']['website_field_dvd_website_link_pattern']             = "Паттерн для страницы сезона [kt|br] на сайте";
$lang['settings']['website_field_dvd_website_link_pattern_hint']        = "этот паттерн используется для генерации ссылок на страницу просмотра сезона; может содержать токен [kt|b]%DIR%[/kt|b], который будет заменен на директорию сезона, и (или) токен [kt|b]%ID%[/kt|b], который будет заменен на ID сезона";
$lang['settings']['website_field_dvd_group_website_link_pattern']       = "Паттерн для страницы сериала [kt|br] на сайте";
$lang['settings']['website_field_dvd_group_website_link_pattern_hint']  = "этот паттерн используется для генерации ссылок на страницу просмотра сериала; может содержать токен [kt|b]%DIR%[/kt|b], который будет заменен на директорию сериала, и (или) токен [kt|b]%ID%[/kt|b], который будет заменен на ID сериала";
$lang['settings']['memberzone_field_tokens_subscribe_dvds']             = "Платные подписки на [kt|br] сезоны";
$lang['settings']['memberzone_field_tokens_subscribe_dvds_hint']        = "укажите цену в токенах по умолчанию и период в днях, цена может быть переопределена для любых сезонов в их настройках; если указать [kt|b]0[/kt|b] в качестве цены по умолчанию, то это будет значить что платная подписка будет доступна только для тех сезонов, у которых указана цена в настройках; если указать пустой период подписки, то купленные подписки будут останутся доступными навсегда";

$lang['stats']['users_awards_field_award_type_dvd_sale']        = "Продажа подписки на сезон";