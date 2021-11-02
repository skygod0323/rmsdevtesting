<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['content_stats']['title']          = "Статистика контента";
$lang['plugins']['content_stats']['description']    = "Показывает суммарную статистику по объему контента.";
$lang['plugins']['content_stats']['long_desc']      = "
		Этот плагин отображает суммарную статистику по всем видео и альбомам с группировкой по форматам. Таким образом,
		вы сможете увидеть, сколько дискового пространства освободится в случае удаления того или иного формата.
";
$lang['permissions']['plugins|content_stats']  = $lang['plugins']['content_stats']['title'];

$lang['plugins']['content_stats']['divider_result']                     = "Статистика контента - %1%";
$lang['plugins']['content_stats']['divider_result_log_file']            = "Лог файл";
$lang['plugins']['content_stats']['divider_recent_calculations']        = "Недавние калькуляции";
$lang['plugins']['content_stats']['divider_recent_calculations_none']   = "В последнее время калькуляций не было.";

$lang['plugins']['content_stats']['dg_results_col_type']                            = "Тип";
$lang['plugins']['content_stats']['dg_results_col_type_group_videos']               = "%1% видео";
$lang['plugins']['content_stats']['dg_results_col_type_group_albums']               = "%1% альбомы";
$lang['plugins']['content_stats']['dg_results_col_type_video_sources']              = "Исходные файлы видео";
$lang['plugins']['content_stats']['dg_results_col_type_video_formats']              = "Формат видео \"%1%\"";
$lang['plugins']['content_stats']['dg_results_col_type_video_timelines']            = "Формат видео \"%1%\" (таймлайновые скриншоты)";
$lang['plugins']['content_stats']['dg_results_col_type_video_logs']                 = "Логи обработки видео";
$lang['plugins']['content_stats']['dg_results_col_type_screenshots_sources']        = "Исходные файлы скриншотов";
$lang['plugins']['content_stats']['dg_results_col_type_screenshots_formats']        = "Формат скриншотов \"%1%\"";
$lang['plugins']['content_stats']['dg_results_col_type_screenshots_zip']            = "Формат скриншотов \"%1%\" (ZIP архивы)";
$lang['plugins']['content_stats']['dg_results_col_type_album_images_sources']       = "Исходные файлы альбомов";
$lang['plugins']['content_stats']['dg_results_col_type_album_images_formats']       = "Формат альбомов \"%1%\"";
$lang['plugins']['content_stats']['dg_results_col_type_album_images_zip']           = "Формат альбомов \"%1%\" (ZIP архивы)";
$lang['plugins']['content_stats']['dg_results_col_type_album_images_sources_zip']   = "Исходные файлы альбомов (ZIP архивы)";
$lang['plugins']['content_stats']['dg_results_col_type_album_logs']                 = "Логи обработки альбомов";
$lang['plugins']['content_stats']['dg_results_col_type_total']                      = "Всего";
$lang['plugins']['content_stats']['dg_results_col_storage']                         = "Место хранения";
$lang['plugins']['content_stats']['dg_results_col_storage_local']                   = "Главный сервер";
$lang['plugins']['content_stats']['dg_results_col_storage_content']                 = "Сервера хранения";
$lang['plugins']['content_stats']['dg_results_col_files']                           = "Файлов";
$lang['plugins']['content_stats']['dg_results_col_size']                            = "Размер";

$lang['plugins']['content_stats']['dg_recent_calculations_col_time']                = "Выполнялась";
$lang['plugins']['content_stats']['dg_recent_calculations_col_results']             = "Результаты";
$lang['plugins']['content_stats']['dg_recent_calculations_col_results_value']       = "Главный сервер: %1%, Сервера хранения: %2%";
$lang['plugins']['content_stats']['dg_recent_calculations_col_results_in_process']  = "В процессе: %1%% выполнено";
$lang['plugins']['content_stats']['dg_recent_calculations_col_log']                 = "Лог";

$lang['plugins']['content_stats']['btn_calculate']  = "Новая калькуляция";
