<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['backup']['title']         = "Резервное копирование";
$lang['plugins']['backup']['description']   = "Позволяет осуществить резервное копирование всех аспектов вашего проекта.";
$lang['plugins']['backup']['long_desc']     = "
		Плагин позволяет создавать резервные копии базы данных, системных файлов KVS, темы сайта, а также некоторых 
		других данных. Рекомендуется включать как минимум недельное резервное копирование. [kt|b]ВНИМАНИЕ![/kt|b] Этот
		плагин не является заменой серверному резервному копированию, он предназначен для создание резервных копий 
		некоторых аспектов движка и сайта для простого способа восстановить их, если вы что-то поломали. Инструкцию по
		восстановлению можно найти внутри архива резервной копии.
";
$lang['permissions']['plugins|backup']  = $lang['plugins']['backup']['title'];

$lang['plugins']['backup']['error_mysqldump_command']   = "Команда mysqldump недоступна: %1%, вы можете переопределить ее в файле [kt|b]/admin/include/setup.php[/kt|b]";
$lang['plugins']['backup']['error_folder_permissions']  = "Не хватает прав для создания файлов резервных копий в указанной директории";
$lang['plugins']['backup']['error_mysql_backup_failed'] = "Возникла ошибка при создании резервной копии базы данных";
$lang['plugins']['backup']['warning_automatic_backup']  = "Не включено создание автоматических резервных копий";

$lang['plugins']['backup']['divider_parameters']                            = "Параметры";
$lang['plugins']['backup']['divider_parameters_hint']                       = "[kt|b]ВНИМАНИЕ![/kt|b] Резервное копирование файлов видео и фото контента не поддерживается. Проконсультируйтесь с вашим хостинг провайдером касательно резервного копирования всей системы на отдельных дисках или серверах. Плагин резервного копирования KVS не является заменой полноценному серверному бэкапу и используется для удобства решения мелких проблем.";
$lang['plugins']['backup']['divider_manual_backup']                         = "Создание резервной копии вручную";
$lang['plugins']['backup']['divider_backups']                               = "Резервные копии";
$lang['plugins']['backup']['divider_backups_none']                          = "В настоящее время у вас нет резервных копий.";
$lang['plugins']['backup']['field_backup_folder']                           = "Директория хранения [kt|br] резервных копий";
$lang['plugins']['backup']['field_backup_folder_hint']                      = "Мы настоятельно рекомендуем указать внешнюю директорию для защиты от случайного удаления резервных копий. [kt|br] При использовании внешней директории она должна быть включена в список разрешенных путей в опции PHP.ini [kt|b]open_basedir[/kt|b] (если у вас используется это ограничение).";
$lang['plugins']['backup']['field_backup_folder_hint2']                     = "Ваше текущее значение опции [kt|b]open_basedir[/kt|b] установлено: [kt|b]%1%[/kt|b]";
$lang['plugins']['backup']['field_backup_auto']                             = "Автоматическое резервное [kt|br] копирование";
$lang['plugins']['backup']['field_backup_auto_daily']                       = "создавать ежедневные копии и хранить их за 7 последних дней";
$lang['plugins']['backup']['field_backup_auto_weekly']                      = "создавать еженедельные копии и хранить их за последний месяц";
$lang['plugins']['backup']['field_backup_auto_monthly']                     = "создавать ежемесячные копии и хранить их за последний год";
$lang['plugins']['backup']['field_backup_auto_skip_content_auxiliary']      = "не копировать файлы записей, объектов категоризации и пользователей";
$lang['plugins']['backup']['field_schedule']                                = "Расписание";
$lang['plugins']['backup']['field_schedule_interval']                       = "минимальный интервал (ч)";
$lang['plugins']['backup']['field_schedule_tod']                            = "время дня";
$lang['plugins']['backup']['field_schedule_tod_any']                        = "как получится";
$lang['plugins']['backup']['field_schedule_hint']                           = "укажите минимальный интервал между повторными запусками этого плагина, а также время дня если требуется; время дня не может быть гарантировано на 100%, в зависимости от обстановки плагин может запуститься позднее в этот же день, но не ранее указанного часа";
$lang['plugins']['backup']['field_last_exec']                               = "Последний запуск";
$lang['plugins']['backup']['field_last_exec_none']                          = "нет";
$lang['plugins']['backup']['field_last_exec_seconds']                       = "секунд";
$lang['plugins']['backup']['field_next_exec']                               = "Следующий запуск";
$lang['plugins']['backup']['field_next_exec_none']                          = "нет";
$lang['plugins']['backup']['field_backup_options']                          = "Содержимое копии";
$lang['plugins']['backup']['field_backup_options_mysql']                    = "сделать резервную копию базы данных";
$lang['plugins']['backup']['field_backup_options_mysql_hint']               = "эта опция полностью сохранит всю базу данных KVS в одном файле";
$lang['plugins']['backup']['field_backup_options_website']                  = "сделать резервную копию сайта";
$lang['plugins']['backup']['field_backup_options_website_hint']             = "эта опция скопирует настройки сайта и плеера, шаблоны, файлы конфигурации, картинки, стили, яваскрипты и все файлы в корневой директории проекта";
$lang['plugins']['backup']['field_backup_options_player']                   = "сделать резервную копию настроек плеера";
$lang['plugins']['backup']['field_backup_options_player_hint']              = "эта опция скопирует настройки плеера и настройки embed плеера";
$lang['plugins']['backup']['field_backup_options_kvs']                      = "сделать резервную копию системных файлов";
$lang['plugins']['backup']['field_backup_options_kvs_hint']                 = "эта опция скопирует все системные файлы KVS";
$lang['plugins']['backup']['field_backup_options_content_auxiliary']        = "сделать резервную копию файлов записей, категоризации и пользователей";
$lang['plugins']['backup']['field_backup_options_content_auxiliary_hint']   = "эта опция скопирует все файлы записей, объектов категоризации и пользователей";
$lang['plugins']['backup']['field_backup_options_content_main']             = "сделать резервную копию файлов видео и альбомов";
$lang['plugins']['backup']['field_backup_options_content_main_hint']        = "[kt|b]НЕ ПОДДЕРЖИВАЕТСЯ:[/kt|b] видео и альбомы могут занимать терабайты дискового пространства, поэтому их копирование не поддерживается";
$lang['plugins']['backup']['dg_backups_col_filename']                       = "Имя файла";
$lang['plugins']['backup']['dg_backups_col_filedate']                       = "Дата создания";
$lang['plugins']['backup']['dg_backups_col_filesize']                       = "Размер";
$lang['plugins']['backup']['dg_backups_col_backup_type']                    = "Содержит";
$lang['plugins']['backup']['dg_backups_col_backup_type_mysql']              = "дамп базы данных";
$lang['plugins']['backup']['dg_backups_col_backup_type_website']            = "копия сайта";
$lang['plugins']['backup']['dg_backups_col_backup_type_player']             = "настройки плеера";
$lang['plugins']['backup']['dg_backups_col_backup_type_kvs']                = "системные файлы KVS";
$lang['plugins']['backup']['dg_backups_col_backup_type_content_auxiliary']  = "файлы контента";
$lang['plugins']['backup']['btn_save']                                      = "Сохранить";
$lang['plugins']['backup']['btn_backup']                                    = "Создать резервную копию";
