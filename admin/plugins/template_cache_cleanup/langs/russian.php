<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['template_cache_cleanup']['title']         = "Очистка кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['description']   = "Удаляет старые экземпляры кэша шаблонов (посл. использование > 5 дней назад) по расписанию или ручным запуском.";
$lang['plugins']['template_cache_cleanup']['long_desc']     = "
		Плагин очистки кэша шаблонов подчищает файловый кэш вручную или по расписанию. Этот плагин может также
		использоваться для получения информации о размере файлового кэша и количества файлов в нем. Файловый кэш
		используется в KVS повсеместно, но он не может самоочищаться. Со временем работы сайта файловый кэш будет
		разрастаться и разрастаться. Мы рекомендуем изредка чистить его в ручном режиме. Если же мемберзона вашего
		сайта используется большим количеством пользователей, то мы рекомендуем настроить очистку кэша по расписанию.
		Обратите внимание, что при ручном запуске реальный запуск произойдет в течение 5 минут после отправки формы.
";
$lang['permissions']['plugins|template_cache_cleanup']      = $lang['plugins']['template_cache_cleanup']['title'];

$lang['plugins']['template_cache_cleanup']['field_cache_folder']            = "Расположение кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['field_cache_size']              = "Размер кэша шаблонов";
$lang['plugins']['template_cache_cleanup']['field_storage_folder']          = "Расположение кэша \$storage";
$lang['plugins']['template_cache_cleanup']['field_storage_size']            = "Размер кэша \$storage";
$lang['plugins']['template_cache_cleanup']['field_size_check']              = "N/A";
$lang['plugins']['template_cache_cleanup']['field_size_megabytes']          = "Мб";
$lang['plugins']['template_cache_cleanup']['field_size_files']              = "файл(ов)";
$lang['plugins']['template_cache_cleanup']['field_enable']                  = "Запуск по расписанию";
$lang['plugins']['template_cache_cleanup']['field_enable_enabled']          = "включен";
$lang['plugins']['template_cache_cleanup']['field_schedule']                = "Расписание";
$lang['plugins']['template_cache_cleanup']['field_schedule_interval']       = "минимальный интервал (ч)";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod']            = "время дня";
$lang['plugins']['template_cache_cleanup']['field_schedule_tod_any']        = "как получится";
$lang['plugins']['template_cache_cleanup']['field_schedule_hint']           = "укажите минимальный интервал между повторными запусками этого плагина, а также время дня если требуется; время дня не может быть гарантировано на 100%, в зависимости от обстановки плагин может запуститься позднее в этот же день, но не ранее указанного часа";
$lang['plugins']['template_cache_cleanup']['field_last_exec']               = "Последний запуск";
$lang['plugins']['template_cache_cleanup']['field_last_exec_none']          = "нет";
$lang['plugins']['template_cache_cleanup']['field_last_exec_seconds']       = "секунд";
$lang['plugins']['template_cache_cleanup']['field_last_exec_files']         = "файлов удалено";
$lang['plugins']['template_cache_cleanup']['field_next_exec']               = "Следующий запуск";
$lang['plugins']['template_cache_cleanup']['field_next_exec_none']          = "нет";
$lang['plugins']['template_cache_cleanup']['btn_save']                      = "Сохранить";
$lang['plugins']['template_cache_cleanup']['btn_calculate_stats']           = "Вычислить размер кэша";
$lang['plugins']['template_cache_cleanup']['btn_start_now']                 = "Запустить очистку";
