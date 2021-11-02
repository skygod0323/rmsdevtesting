<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['upload_folder']['title']          = "Загрузка контента по FTP";
$lang['plugins']['upload_folder']['description']    = "Позволяет создавать видео / альбомы на основе загруженных по ФТП файлов.";
$lang['plugins']['upload_folder']['long_desc']      = "
		Плагин может значительно упростить загрузку видео и фото контента на ваш сервер. Все что вам нужно сделать -
		это загрузить файлы в директории на сервере в нужной структуре и запустить плагин, настроив его на данные
		директории. Плагин проанализирует указанные директории и предоставит вам обзор найденного в них контента. При
		финальном запуске выбранный контент будет добавлен.
		[kt|br][kt|br]
		Плагин поддерживает 3 разных директории для загрузки в них стандартных видео, премиум видео и фотоальбомов.
		Желательно использовать постоянно одни и те же директории, поскольку дубликаты контента определяются
		непосредственно в привязке к директории (т.е. при загрузке в одну и ту же директорию контента, который уже был
		добавлен ранее и не удален с вашего сайта, этот контент посчитается дубликатом). В целях безопасности
		директории с загруженными файлами должны являться дочерними для корневой директории проекта.
		[kt|br][kt|br]
		В обе директории для видео вы можете загружать как просто одиночные видеофайлы в корень, так и поддиректории с
		несколькими файлами в них. Поддиректории могут содержать видеофайлы, файл TXT с описанием, а также скриншоты в
		ZIP архиве или в виде набора из JPG файлов. В качестве видеофайлов вы можете загружать не только исходники, но
		и файлы отдельных форматов. При необходимости вы можете загружать только исходник, либо только файлы форматов,
		либо комбинировать различным образом.
		[kt|br][kt|br]
		По аналогии с видео, в директорию для альбомов плагин позволяет загружать как одиночные файлы в корень, так и
		поддиректории с несколькими файлами в них. Фотографии могут быть загружены либо в ZIP архиве, либо как набор из
		JPG файлов. Описание альбома в файле TXT.
		[kt|br][kt|br]
		После завершения работы плагина загруженные файлы удаляться не будут. Вы можете удалить их самостоятельно, либо
		оставить. При повторном запуске плагина файлы (или поддиректории), которые уже были загружены будут считаться
		дубликатами.
";
$lang['permissions']['plugins|upload_folder']       = $lang['plugins']['upload_folder']['title'];

$lang['plugins']['upload_folder']['validation_error_iconv']                         = "[kt|b][%1%][/kt|b]: библиотека iconv недоступна в сборке PHP, свяжитесь с администраторами сервера";
$lang['plugins']['upload_folder']['divider_validation_results']                     = "Результаты сканирования директорий";
$lang['plugins']['upload_folder']['divider_import_results']                         = "Импортированный контент";
$lang['plugins']['upload_folder']['divider_import_results_none']                    = "Ничего не было импортировано";
$lang['plugins']['upload_folder']['field_folder_standard_videos']                   = "Директория стандартных видео";
$lang['plugins']['upload_folder']['field_folder_standard_videos_hint']              = "путь к директории, в которую вы загружаете стандартные видео";
$lang['plugins']['upload_folder']['field_folder_premium_videos']                    = "Директория премиум видео";
$lang['plugins']['upload_folder']['field_folder_premium_videos_hint']               = "путь к директории, в которую вы загружаете премиум видео";
$lang['plugins']['upload_folder']['field_folder_albums']                            = "Директория альбомов";
$lang['plugins']['upload_folder']['field_folder_albums_hint']                       = "путь к директории, в которую вы загружаете альбомы";
$lang['plugins']['upload_folder']['field_video_formats']                            = "Форматы видео";
$lang['plugins']['upload_folder']['field_video_formats_analyze']                    = "Определять форматы видео по постфиксам и загружать без обработки";
$lang['plugins']['upload_folder']['field_video_formats_ignore']                     = "Рассматривать загруженные видеофайлы как исходные файлы и загружать с обработкой";
$lang['plugins']['upload_folder']['field_video_formats_hint']                       = "хотите ли вы, чтобы загруженные видеофайлы определялись как файлы форматов (по соответствию постфиксу) и загружались без обработки, либо наоборот, чтобы загруженные видеофайлы всегда рассматривались в качестве исходных файлов и проходили обработку";
$lang['plugins']['upload_folder']['field_video_screenshots']                        = "Скриншоты видео";
$lang['plugins']['upload_folder']['field_video_screenshots_overview']               = "Рассматривать загруженные скриншоты как обзорные скриншоты";
$lang['plugins']['upload_folder']['field_video_screenshots_posters']                = "Рассматривать загруженные скриншоты как постеры";
$lang['plugins']['upload_folder']['field_video_screenshots_hint']                   = "устанавливает каким образом добавлять загруженные скриншоты";
$lang['plugins']['upload_folder']['field_filenames_encoding']                       = "Кодировка файловой системы";
$lang['plugins']['upload_folder']['field_filenames_encoding_hint']                  = "если у вас в качестве имен файлов / директорий используются символы в нестандартной кодировке, укажите код этой кодировки (cp1250, cp1251 и т.д.)[kt|br]http://ru.wikipedia.org/wiki/Набор_символов";
$lang['plugins']['upload_folder']['field_delete_files']                             = "Удалить файлы";
$lang['plugins']['upload_folder']['field_delete_files_yes']                         = "удалить файлы после импорта";
$lang['plugins']['upload_folder']['field_delete_files_hint']                        = "если включить эту опцию, исходные файлы будут удалены из данных директорий; это также повысит скорость импорта";
$lang['plugins']['upload_folder']['field_randomize']                                = "Случайный порядок";
$lang['plugins']['upload_folder']['field_randomize_yes']                            = "импортировать контент в случайном порядке";
$lang['plugins']['upload_folder']['field_randomize_hint']                           = "по умолчанию контент будет импортироваться по алфавиту; включите эту опцию, чтобы импортировать в случайном порядке";
$lang['plugins']['upload_folder']['field_content_status']                           = "Статус контента после импорта";
$lang['plugins']['upload_folder']['field_content_status_disabled']                  = "Неактивный";
$lang['plugins']['upload_folder']['field_content_status_active']                    = "Активный";
$lang['plugins']['upload_folder']['field_analyze_result']                           = "Суммарно";
$lang['plugins']['upload_folder']['field_analyze_result_found_objects']             = "%1% объектов найдено";
$lang['plugins']['upload_folder']['field_analyze_result_existing_objects']          = "%1% из них дубликаты";
$lang['plugins']['upload_folder']['field_analyze_result_errors']                    = "%1% с ошибками";
$lang['plugins']['upload_folder']['dg_contents_col_import']                         = "Вкл";
$lang['plugins']['upload_folder']['dg_contents_col_object_type']                    = "Тип объекта";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_std_video']          = "Стандарное видео";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_pre_video']          = "Премиум видео";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_album']              = "Альбом";
$lang['plugins']['upload_folder']['dg_contents_col_object_id']                      = "ID объекта";
$lang['plugins']['upload_folder']['dg_contents_col_title']                          = "Название";
$lang['plugins']['upload_folder']['dg_contents_col_file_name']                      = "Имя файла";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage']                     = "Информация";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_duplicate']           = "Дупликат (ID: %1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_error']               = "Ошибка (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_ignored']             = "Пропуск (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_file']         = "Исходный файл (%1%, %2%) - с обработкой";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_format_file']         = "Формат \"%1%\" (%2%, %3%) - без обработки";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_screenshots_zip']     = "Скриншоты в ZIP (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_screenshots']         = "Скриншоты (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_main_screenshot']     = "Главн. скриншот (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_posters_zip']         = "Постеры в ZIP (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_posters']             = "Постеры (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_images_zip']   = "Исходные файлы в ZIP (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_images']       = "Исходные файлы (%1% файлов, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_description']         = "Файл описания (%1%)";
$lang['plugins']['upload_folder']['dg_contents_errors_no_video_files']              = "Нет файлов видео";
$lang['plugins']['upload_folder']['dg_contents_errors_no_image_files']              = "Нет файлов изображений";
$lang['plugins']['upload_folder']['dg_contents_errors_unreadable_file']             = "Не хватает привилегий для чтения файла: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_video_file']          = "Некорректный видеофайл: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_zip_file']            = "Некорректный ZIP файл: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_image_file']          = "Некорректный файл изображения: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_no_delete_permissions']       = "Нет привилегий на удаление файлов";
$lang['plugins']['upload_folder']['btn_analyze']                                    = "Сканировать";
$lang['plugins']['upload_folder']['btn_import']                                     = "Импортировать выбранный контент";
$lang['plugins']['upload_folder']['btn_back']                                       = "<< Назад";
$lang['plugins']['upload_folder']['btn_close']                                      = "Закрыть";
