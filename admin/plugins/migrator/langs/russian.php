<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['migrator']['title']       = "Мигратор";
$lang['plugins']['migrator']['description'] = "Мигрирует данные из другой инсталляции KVS или из других скриптов.";
$lang['plugins']['migrator']['long_desc']   = "
		Вы можете использовать этот плагин, чтобы смигрировать данные категоризации вашего другого проекта на KVS. Если
		вы хотите перевести проект с другого скрипта на KVS, обратитесь в нашу службу поддержки.
";
$lang['permissions']['plugins|migrator']    = $lang['plugins']['migrator']['title'];

$lang['plugins']['migrator']['error_php_mysqli']                                        = "PHP модуль MySQLi не установлен";
$lang['plugins']['migrator']['error_failed_to_connect']                                 = "Ошибка соединения с базой данных MySQL (%1%)";
$lang['plugins']['migrator']['divider_parameters']                                      = "Настройки миграции";
$lang['plugins']['migrator']['divider_summary']                                         = "Обзор миграции";
$lang['plugins']['migrator']['divider_recent_migrations']                               = "Последние миграции";
$lang['plugins']['migrator']['divider_recent_migrations_none']                          = "В последнее время миграций не было.";
$lang['plugins']['migrator']['field_old_script']                                        = "Старый скрипт";
$lang['plugins']['migrator']['field_old_path']                                          = "Старый путь";
$lang['plugins']['migrator']['field_old_path_hint']                                     = "абсолютный путь к директории, в которой установлен старый скрипт, он будет использоваться для прямого копирования файлов; пример: [kt|b]/path/to/domain.com[/kt|b]";
$lang['plugins']['migrator']['field_old_url']                                           = "Старый URL";
$lang['plugins']['migrator']['field_old_url_hint']                                      = "URL к старому проекту; пример: [kt|b]http://domain.com[/kt|b]";
$lang['plugins']['migrator']['field_old_mysql_url']                                     = "URL старой базы MySQL";
$lang['plugins']['migrator']['field_old_mysql_port']                                    = "Порт старой базы MySQL";
$lang['plugins']['migrator']['field_old_mysql_user']                                    = "Логин старой базы MySQL";
$lang['plugins']['migrator']['field_old_mysql_pass']                                    = "Пароль старой базы MySQL";
$lang['plugins']['migrator']['field_old_mysql_name']                                    = "Имя старой базы MySQL";
$lang['plugins']['migrator']['field_old_mysql_charset']                                 = "Кодировка старой базы MySQL";
$lang['plugins']['migrator']['field_migrate_data']                                      = "Данные для миграции";
$lang['plugins']['migrator']['field_migrate_data_tags']                                 = "Тэги";
$lang['plugins']['migrator']['field_migrate_data_categories']                           = "Категории";
$lang['plugins']['migrator']['field_migrate_data_models']                               = "Модели";
$lang['plugins']['migrator']['field_migrate_data_content_sources']                      = "Контент провайдеры";
$lang['plugins']['migrator']['field_migrate_data_dvds']                                 = "Каналы / DVD / Сериалы";
$lang['plugins']['migrator']['field_migrate_data_videos']                               = "Видео";
$lang['plugins']['migrator']['field_migrate_data_videos_screenshots']                   = "Скриншоты видео";
$lang['plugins']['migrator']['field_migrate_data_albums']                               = "Альбомы";
$lang['plugins']['migrator']['field_migrate_data_comments']                             = "Комментарии";
$lang['plugins']['migrator']['field_migrate_data_users']                                = "Пользователи";
$lang['plugins']['migrator']['field_migrate_data_favourites']                           = "Закладки";
$lang['plugins']['migrator']['field_migrate_data_friends']                              = "Друзья";
$lang['plugins']['migrator']['field_migrate_data_messages']                             = "Сообщения внутренней почты";
$lang['plugins']['migrator']['field_migrate_data_subscriptions']                        = "Подписки";
$lang['plugins']['migrator']['field_migrate_data_playlists']                            = "Плэйлисты";
$lang['plugins']['migrator']['field_override_objects']                                  = "Существующие объекты";
$lang['plugins']['migrator']['field_override_objects_yes']                              = "Заменять объекты с такими же ID на объекты из старого проекта";
$lang['plugins']['migrator']['field_override_objects_hint']                             = "[kt|b]ВНИМАНИЕ![/kt|b] Если ваша текущая база данных не пустая, то при включении этой опции некоторые объекты могут быть заменены на объекты с такими же ID из старой базы. Используйте осторожно.";
$lang['plugins']['migrator']['field_upload_hotlinked_videos']                           = "Хотлинкованные видео";
$lang['plugins']['migrator']['field_upload_hotlinked_videos_yes']                       = "Загружать к себе";
$lang['plugins']['migrator']['field_upload_hotlinked_videos_hint']                      = "включите эту опцию, если хотите чтобы KVS сохранял хотлинкованные видео к себе вместо хотлинкования по старым ссылкам";
$lang['plugins']['migrator']['field_test_mode']                                         = "Тестовый режим";
$lang['plugins']['migrator']['field_test_mode_enabled']                                 = "ограничить до";
$lang['plugins']['migrator']['field_test_mode_hint']                                    = "укажите максимальное кол-во видео / альбомов для миграции";
$lang['plugins']['migrator']['field_options']                                           = "Доп. опции";
$lang['plugins']['migrator']['field_options_name']                                      = "Название";
$lang['plugins']['migrator']['field_options_value']                                     = "Значение";
$lang['plugins']['migrator']['field_summary_duration']                                  = "Длительность";
$lang['plugins']['migrator']['field_summary_duration_value']                            = "%1% секунд";
$lang['plugins']['migrator']['field_summary_memory']                                    = "Память";
$lang['plugins']['migrator']['field_summary_memory_bytes']                              = "%1% B";
$lang['plugins']['migrator']['field_summary_memory_kilobytes']                          = "%1% Kb";
$lang['plugins']['migrator']['field_summary_memory_megabytes']                          = "%1% Mb";
$lang['plugins']['migrator']['dg_summary_col_objects']                                  = "Объекты";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['users']                  = "Пользователи";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['category_groups']        = "Группы категорий";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['categories']             = "Категории";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['tags']                   = "Тэги";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['content_source_groups']  = "Группы контент провайдеров";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['content_sources']        = "Контент провайдеры";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['models']                 = "Модели";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['model_groups']           = "Группы моделей";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['dvd_groups']             = "Группы каналов / Группы DVD / Сериалы";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['dvds']                   = "Каналы / DVD / Сезоны сериалов";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['videos']                 = "Видео";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['albums']                 = "Альбомы";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['comments']               = "Комментарии";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['fav_videos']             = "Закладки видео";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['fav_albums']             = "Закладки альбомов";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['friends']                = "Друзья";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['messages']               = "Сообщения";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['subscriptions']          = "Подписки";
$lang['plugins']['migrator']['dg_summary_col_objects_values']['playlists']              = "Плэйлисты";
$lang['plugins']['migrator']['dg_summary_col_total']                                    = "Всего";
$lang['plugins']['migrator']['dg_summary_col_inserted']                                 = "Добавлено";
$lang['plugins']['migrator']['dg_summary_col_updated']                                  = "Изменено";
$lang['plugins']['migrator']['dg_summary_col_errors']                                   = "Ошибок";
$lang['plugins']['migrator']['dg_recent_migrations_col_time']                           = "Выполнялась";
$lang['plugins']['migrator']['dg_recent_migrations_col_results']                        = "Результаты";
$lang['plugins']['migrator']['dg_recent_migrations_col_results_value']                  = "%1% добавлено, %2% обновлено, %3% ошибок";
$lang['plugins']['migrator']['dg_recent_migrations_col_results_in_process']             = "В процессе: %1%% выполнено";
$lang['plugins']['migrator']['dg_recent_migrations_col_log']                            = "Лог";
$lang['plugins']['migrator']['btn_start']                                               = "Запустить миграцию";
