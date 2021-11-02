<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['grabbers']['title']       = "Грабберы";
$lang['plugins']['grabbers']['description'] = "Позволяет получать контент со сторонних сайтов.";
$lang['plugins']['grabbers']['long_desc']   = "
		Вы можете использовать этот плагин для импорта контента со сторонних сайтов, для которых KVS предоставляет
		грабберы. Сначала вам необходимо установить желаемые грабберы из репозитория KVS и настроить их, после чего вы
		сможете импортировать контент с этих сайтов через стандартный импорт KVS (используя поле URL страницы видео),
		либо через упрощенный модуль импорта в этом плагине. Большинство грабберов поддерживают не только импорт с
		отдельных страниц контента, но и массовый импорт контента со страниц списков. Для каждого граббера можно
		включить функцию автопилота, чтобы он мониторил указанные списки и автоматически импортировал новый контент с
		них.
		[kt|br][kt|br]
		Просьба обратить внимание, что сайт-источник может забанить IP вашего сервера, если на нем используется
		какая-либо защита по IP. Для каждого граббера можно настроить таймаут (по умолчанию 5 секунд), чтобы ваш сервер
		не делал запросы слишком часто.
		[kt|br][kt|br]
		Если вы хотите предложить новые сайты для добавления в репозиторий грабберов, обратитесь в нашу службу
		поддержки.
";
$lang['permissions']['plugins|grabbers']    = $lang['plugins']['grabbers']['title'];

$lang['plugins']['grabbers']['upload']                                  = "Загрузка контента";
$lang['plugins']['grabbers']['divider_grabber_settings']                = "Настройки граббера %1%";
$lang['plugins']['grabbers']['divider_grabber_settings_default']        = "[kt|b]Внимание![/kt|b] Этот граббер может обрабатывать любые страницы, но его функциональность ограничена. Он может использовать содержимое тэгов HTML title / description / keywords и находить исходные видеофайлы / фотографии в коде страницы. Вам всегда следует использовать грабберы под конкретные сайты, поскольку они умеют собирать больше данных и имеют больше настроек. Если для сайта, с которого вы хотите получить видео нет граббера, попробуйте обратиться в службу поддержки, возможно, мы сможем добавить граббер под этот сайт.";
$lang['plugins']['grabbers']['divider_grabber_log']                     = "Лог граббера";
$lang['plugins']['grabbers']['divider_filters']                         = "Фильтры";
$lang['plugins']['grabbers']['divider_autopilot']                       = "Настройки автопилота";
$lang['plugins']['grabbers']['divider_upload']                          = "Загрузка контента";
$lang['plugins']['grabbers']['divider_upload_options']                  = "Опции создания контента";
$lang['plugins']['grabbers']['divider_upload_confirm']                  = "Подтверждение загрузки контента";
$lang['plugins']['grabbers']['divider_install']                         = "Установка грабберов";
$lang['plugins']['grabbers']['divider_grabbers']                        = "Активные грабберы";
$lang['plugins']['grabbers']['divider_grabbers_videos']                 = "Видео";
$lang['plugins']['grabbers']['divider_grabbers_albums']                 = "Альбомы";
$lang['plugins']['grabbers']['divider_grabbers_models']                 = "Модели";
$lang['plugins']['grabbers']['divider_grabbers_none']                   = "Выберите, какие грабберы вы хотите установить и настройте их.";
$lang['plugins']['grabbers']['field_upload']                            = "Загрузить контент через грабберы вручную";
$lang['plugins']['grabbers']['field_upload_hint']                       = "эта функциональность позволит вам создать начальную базу контента, после чего вы можете настроить каждый граббер на автопилот, чтобы постоянно подтягивался новый контент";
$lang['plugins']['grabbers']['field_upload_type']                       = "Тип загрузки";
$lang['plugins']['grabbers']['field_upload_type_videos']                = "Видео";
$lang['plugins']['grabbers']['field_upload_type_albums']                = "Альбомы";
$lang['plugins']['grabbers']['field_upload_type_models']                = "Модели";
$lang['plugins']['grabbers']['field_upload_list']                       = "Список URL-ов";
$lang['plugins']['grabbers']['field_upload_list_hint']                  = "Укажите список URL-ов контента или URL-ов списков контента для граббинга. Каждый URL должен быть указан на отдельной строке в таком формате: просто [kt|b]URL[/kt|b] или [kt|b]URL|число[/kt|b], где [kt|b]число[/kt|b] - это кол-во контента, которое необходимо сграббить с указанного списка, прокручивая список на следующие страницы, если это возможно. Если число не указано, либо если указан [kt|b]0[/kt|b], то граббер будет добавлять только контент, который отображается непосредственно на данной странице списка без учета пагинации. [kt|br] [kt|b]http://domain.com/top.html|1000[/kt|b] - указывает грабберу добавить 1000 видео / альбомов со списка http://domain.com/top.html, при необходимости используя пагинацию; [kt|br] [kt|b]http://domain.com/top.html[/kt|b] - указывает грабберу добавить N видео / альбомов, которые отображаются непосредственно на странице http://domain.com/top.html, не используя при этом пагинацию; [kt|br] [kt|b]http://domain.com/video-page.html[/kt|b] - указывает грабберу добавить только 1 видео со страницы http://domain.com/video-page.html (если предположить, что данная страница является индивидуальной страницей этого видео).";
$lang['plugins']['grabbers']['field_upload_list_hint_autopilot']        = "Укажите список URL-ов списков контента для граббинга. Каждый URL должен быть указан на отдельной строке в таком формате: просто [kt|b]URL[/kt|b] или [kt|b]URL|число[/kt|b], где [kt|b]число[/kt|b] - это кол-во контента, которое необходимо сграббить с указанного списка, прокручивая список на следующие страницы, если это возможно. Если число не указано, либо если указан [kt|b]0[/kt|b], то граббер будет добавлять только контент, который отображается непосредственно на данной странице списка без учета пагинации. [kt|br] [kt|b]http://domain.com/top.html|1000[/kt|b] - указывает грабберу добавить 1000 видео / альбомов со списка http://domain.com/top.html, при необходимости используя пагинацию; [kt|br] [kt|b]http://domain.com/top.html[/kt|b] - указывает грабберу добавить N видео / альбомов, которые отображаются непосредственно на странице http://domain.com/top.html, не используя при этом пагинацию.";
$lang['plugins']['grabbers']['field_kvs_repository']                    = "Репозиторий KVS";
$lang['plugins']['grabbers']['field_kvs_repository_empty']              = "Грабберы не выбраны.";
$lang['plugins']['grabbers']['field_kvs_repository_all']                = "Все грабберы...";
$lang['plugins']['grabbers']['field_kvs_repository_hint']               = "установить грабберы из репозитория KVS";
$lang['plugins']['grabbers']['field_custom_grabber']                    = "Свой граббер";
$lang['plugins']['grabbers']['field_custom_grabber_hint']               = "загрузить PHP файл своего граббера";
$lang['plugins']['grabbers']['field_delete']                            = "Удалить";
$lang['plugins']['grabbers']['field_name']                              = "Граббер";
$lang['plugins']['grabbers']['field_name_missing_grabber']              = "Не найдено граббера";
$lang['plugins']['grabbers']['field_name_error_grabber']                = "Ошибка граббера";
$lang['plugins']['grabbers']['field_name_duplicates']                   = "Дубликаты";
$lang['plugins']['grabbers']['field_version']                           = "Версия";
$lang['plugins']['grabbers']['field_ydl_binary']                        = "Путь к youtube-dl";
$lang['plugins']['grabbers']['field_ydl_binary_hint']                   = "Библиотека youtube-dl используется для скачивания видео во многих грабберах, информация о ее установке находится по адресу https://github.com/rg3/youtube-dl [kt|br] Для повышения скорости скачивания вы можете установить библиотеку Aria2 по адресу https://aria2.github.io и добавить опцию после пути к youtube-dl: [kt|br] [kt|b]/usr/local/bin/youtube-dl --external-downloader /usr/bin/aria2c[kt|b]";
$lang['plugins']['grabbers']['field_mode']                              = "Режим";
$lang['plugins']['grabbers']['field_mode_none']                         = "Не выбран";
$lang['plugins']['grabbers']['field_mode_download']                     = "Скачать";
$lang['plugins']['grabbers']['field_mode_embed']                        = "Embed";
$lang['plugins']['grabbers']['field_mode_pseudo']                       = "Псевдо";
$lang['plugins']['grabbers']['field_mode_skip']                         = "Пропустить";
$lang['plugins']['grabbers']['field_mode_hint']                         = "- при использовании опции [kt|b]%1%[/kt|b] граббер будет скачивать файлы и сохранять на ваш(и) сервер(а); [kt|br] - при использовании опции [kt|b]%2%[/kt|b] граббер будет ставить embed коды на сайт-источник через их плеер; [kt|br] - при использовании опции [kt|b]%3%[/kt|b] граббер будет ставить ссылки на сайт-источник, так что пользователи, которые захотят посмотреть контент будут перенаправлены на сайт-источник. [kt|br]";
$lang['plugins']['grabbers']['field_url_postfix']                       = "Постфикс URL-ов";
$lang['plugins']['grabbers']['field_url_postfix_hint']                  = "если сайт-источник поддерживает партнерскую программу, то вы можете указать название / значение партнерского параметра, в таком случае граббер будет подставлять его во все embed коды и псевдо видео, например: [kt|b]ref=kernel[/kt|b]";
$lang['plugins']['grabbers']['field_data']                              = "Данные";
$lang['plugins']['grabbers']['field_data_none']                         = "Только контент";
$lang['plugins']['grabbers']['field_data_title']                        = "Название";
$lang['plugins']['grabbers']['field_data_description']                  = "Описание";
$lang['plugins']['grabbers']['field_data_tags']                         = "Тэги";
$lang['plugins']['grabbers']['field_data_categories']                   = "Категории";
$lang['plugins']['grabbers']['field_data_models']                       = "Модели";
$lang['plugins']['grabbers']['field_data_content_source']               = "Контент провайдер";
$lang['plugins']['grabbers']['field_data_channel']                      = "Канал";
$lang['plugins']['grabbers']['field_data_screenshot']                   = "Скриншот";
$lang['plugins']['grabbers']['field_data_rating']                       = "Рейтинг";
$lang['plugins']['grabbers']['field_data_views']                        = "Просмотры";
$lang['plugins']['grabbers']['field_data_date']                         = "Дата добавления";
$lang['plugins']['grabbers']['field_data_custom']                       = "Доп. поля";
$lang['plugins']['grabbers']['field_data_age']                          = "Возраст";
$lang['plugins']['grabbers']['field_data_birth_date']                   = "День рождения";
$lang['plugins']['grabbers']['field_data_gender']                       = "Пол";
$lang['plugins']['grabbers']['field_data_pseudonyms']                   = "Псевдонимы";
$lang['plugins']['grabbers']['field_data_height']                       = "Рост";
$lang['plugins']['grabbers']['field_data_weight']                       = "Вес";
$lang['plugins']['grabbers']['field_data_measurements']                 = "Измерения";
$lang['plugins']['grabbers']['field_data_country']                      = "Страна";
$lang['plugins']['grabbers']['field_data_city']                         = "Город";
$lang['plugins']['grabbers']['field_data_state']                        = "Штат";
$lang['plugins']['grabbers']['field_data_eye_color']                    = "Цвет глаз";
$lang['plugins']['grabbers']['field_data_hair_color']                   = "Цвет волос";
$lang['plugins']['grabbers']['field_data_hint']                         = "выберите, какие данные должны заимствоваться граббером";
$lang['plugins']['grabbers']['field_import_categories_as_tags']         = "Категории как тэги";
$lang['plugins']['grabbers']['field_import_categories_as_tags_enabled'] = "импортировать категории из этого граббера как тэги";
$lang['plugins']['grabbers']['field_import_categories_as_tags_hint']    = "По умолчанию, если вы включите граббинг категорий, они будут добавлены в том же виде, в каком они используются на исходном сайте. Это может привести к забиванию вашей базы сотнями категорий с похожими названиями и ваша структура категоризации будет плохой. Мы рекомендуем использовать как можно меньше категорий, но в то же время большой набор тэгов. Если включить эту опцию, то категории из этого граббера будут добавляться к контенту как тэги. Вы также можете включить плагин [kt|b]Автоподбор категорий[/kt|b], чтобы он подбирал категории на основе тэгов (для его правильной работы вам нужно будет задать вручную список категорий и их синонимов).";
$lang['plugins']['grabbers']['field_content_source']                    = "Контент провайдер";
$lang['plugins']['grabbers']['field_content_source_no_group']           = "* Нет группы *";
$lang['plugins']['grabbers']['field_content_source_hint']               = "выберите контент провайдера, чтобы назначить его всему контенту от этого граббера";
$lang['plugins']['grabbers']['field_quality']                           = "Качество";
$lang['plugins']['grabbers']['field_quality_multiple']                  = "Мультиформат";
$lang['plugins']['grabbers']['field_quality_none']                      = "Наилучшее";
$lang['plugins']['grabbers']['field_quality_hint']                      = "файлы какого качества граббер должен скачивать";
$lang['plugins']['grabbers']['field_quality_missing']                   = "если файл отсутствует";
$lang['plugins']['grabbers']['field_quality_missing_error']             = "Пропускать такой контент";
$lang['plugins']['grabbers']['field_quality_missing_lower']             = "Использовать худшее качество";
$lang['plugins']['grabbers']['field_quality_missing_higher']            = "Использовать лучшее качество";
$lang['plugins']['grabbers']['field_download_format']                   = "загружать как";
$lang['plugins']['grabbers']['field_download_format_source']            = "Исходный файл (с обработкой)";
$lang['plugins']['grabbers']['field_download_format_skip']              = "Пропустить";
$lang['plugins']['grabbers']['field_download_format_format']            = "Формат \"%1%\" (без обработки)";
$lang['plugins']['grabbers']['field_filters']                           = "Фильтры";
$lang['plugins']['grabbers']['field_quantity_filter_videos']            = "Фильтр длительности";
$lang['plugins']['grabbers']['field_quantity_filter_albums']            = "Фильтр кол-ва фото";
$lang['plugins']['grabbers']['field_quantity_filter_from']              = "от";
$lang['plugins']['grabbers']['field_quantity_filter_to']                = "до";
$lang['plugins']['grabbers']['field_quantity_filter_videos_hint']       = "секунды; контент, который не подходит под этот фильтр не будет добавлен";
$lang['plugins']['grabbers']['field_quantity_filter_albums_hint']       = "кол-во фото; контент, который не подходит под этот фильтр не будет добавлен";
$lang['plugins']['grabbers']['field_rating_filter']                     = "Фильтр рейтинга";
$lang['plugins']['grabbers']['field_rating_filter_from']                = "от";
$lang['plugins']['grabbers']['field_rating_filter_to']                  = "до";
$lang['plugins']['grabbers']['field_rating_filter_hint']                = "проценты (0-100); контент, который не подходит под этот фильтр не будет добавлен";
$lang['plugins']['grabbers']['field_views_filter']                      = "Фильтр просмотров";
$lang['plugins']['grabbers']['field_views_filter_from']                 = "от";
$lang['plugins']['grabbers']['field_views_filter_to']                   = "до";
$lang['plugins']['grabbers']['field_views_filter_hint']                 = "контент, который не подходит под этот фильтр не будет добавлен";
$lang['plugins']['grabbers']['field_date_filter']                       = "Фильтр новизны";
$lang['plugins']['grabbers']['field_date_filter_from']                  = "от";
$lang['plugins']['grabbers']['field_date_filter_to']                    = "до";
$lang['plugins']['grabbers']['field_date_filter_hint']                  = "дни; контент, который не подходит под этот фильтр не будет добавлен";
$lang['plugins']['grabbers']['field_terminology_filter']                = "Фильтр терминологии";
$lang['plugins']['grabbers']['field_terminology_filter_hint']           = "укажите список слов через запятую, которые вы не хотите импортировать; контент, который содержит любое из этих слов в названии не будет добавлен";
$lang['plugins']['grabbers']['field_quality_from_filter']               = "Фильтр качества";
$lang['plugins']['grabbers']['field_quality_from_filter_hint']          = "выберите минимальное качество видео, которое вы хотите разрешить к импорту";
$lang['plugins']['grabbers']['field_replacements']                      = "Замена текстовок";
$lang['plugins']['grabbers']['field_replacements_hint']                 = "в некоторых случаях грабберы могут парсить названия или описания, в которых присутствует название сайта или другой статический текст, который вы бы хотели убрать, вы можете настроить замену этого текста на пустую строку или на свой текст; [kt|br] укажите в таком формате построчно: [kt|b]оригинальный текст: замена[/kt|b]";
$lang['plugins']['grabbers']['field_timeout']                           = "Таймаут";
$lang['plugins']['grabbers']['field_timeout_hint']                      = "рекомендуется устанавливать приличный таймаут (5-10 секунд), чтобы предотвратить блокировку IP вашего сервера (хоть это и не гарантия); таймаут несколько снизит скорость добавления контента";
$lang['plugins']['grabbers']['field_proxies']                           = "Прокси";
$lang['plugins']['grabbers']['field_proxies_hint']                      = "укажите список прокси серверов для этого граббера, из которого будет выбираться случайный сервер; каждый прокси сервер должен быть указан на отдельной строке в таком формате: [kt|b]схема://пользователь:пароль@сервер:порт[/kt|b], где схема и пользователь:пароль могут быть не указаны; [kt|br] например, [kt|b]http://user:password@123.124.125.126:3128[/kt|b] или просто [kt|b]123.124.125.126:3128[/kt|b] если прокси без авторизации";
$lang['plugins']['grabbers']['field_account']                           = "Вход в мемберзону";
$lang['plugins']['grabbers']['field_account_hint']                      = "если для получения доступа к контенту требуется вход в мемберзону, укажите данные входа в виде строки [kt|b]username:password[/kt|b]";
$lang['plugins']['grabbers']['field_autodelete']                        = "Автоудаление";
$lang['plugins']['grabbers']['field_autodelete_enabled']                = "включить автоматическое удаление контента";
$lang['plugins']['grabbers']['field_autodelete_hint']                   = "граббер будет проверять удаленный контент на сайте-источнике и автоматически удалять его на вашем сайте";
$lang['plugins']['grabbers']['field_autopilot']                         = "Автопилот";
$lang['plugins']['grabbers']['field_autopilot_enabled']                 = "включить";
$lang['plugins']['grabbers']['field_autopilot_hint']                    = "функция автопилота позволяет периодически делать запросы на сайт-источник в поиске нового контента по указанным URL-ам";
$lang['plugins']['grabbers']['field_autopilot_interval']                = "Интервал (часы)";
$lang['plugins']['grabbers']['field_autopilot_interval_hint']           = "через какой интервал времени граббер должен делать повторные запросы на добавление нового контента";
$lang['plugins']['grabbers']['field_threads']                           = "Кол-во потоков на граббер";
$lang['plugins']['grabbers']['field_threads_hint']                      = "установите несколько потоков на граббер, если грабберы скачивают файлы на ваш сервер, что может занимать много времени; необходимо также учитывать что многопоточные запросы могут привести к блокированию IP вашего сервера сайтом-источником";
$lang['plugins']['grabbers']['field_limit_title']                       = "Длина названия";
$lang['plugins']['grabbers']['field_limit_title_words']                 = "слов";
$lang['plugins']['grabbers']['field_limit_title_characters']            = "символов";
$lang['plugins']['grabbers']['field_limit_title_hint']                  = "позволяет обрезать названия импортируемого контента по кол-ву слов или символов";
$lang['plugins']['grabbers']['field_limit_description']                 = "Длина описания";
$lang['plugins']['grabbers']['field_limit_description_words']           = "слов";
$lang['plugins']['grabbers']['field_limit_description_characters']      = "символов";
$lang['plugins']['grabbers']['field_limit_description_hint']            = "позволяет обрезать описания импортируемого контента по кол-ву слов или символов";
$lang['plugins']['grabbers']['field_status_after_import']               = "Статус после импорта";
$lang['plugins']['grabbers']['field_status_after_import_active']        = "Активный";
$lang['plugins']['grabbers']['field_status_after_import_disabled']      = "Неактивный";
$lang['plugins']['grabbers']['field_options_categorization']            = "Новые объекты категоризации";
$lang['plugins']['grabbers']['field_options_categorization_categories'] = "Не создавать новые категории";
$lang['plugins']['grabbers']['field_options_categorization_models']     = "Не создавать новые модели";
$lang['plugins']['grabbers']['field_options_categorization_cs']         = "Не создавать новые контент провайдеры";
$lang['plugins']['grabbers']['field_options_categorization_channels']   = "Не создавать новые каналы";
$lang['plugins']['grabbers']['field_options_categorization_hint']       = "по умолчанию объекты категоризации, которые распознаются грабберами, будут создаваться если у вас их нет; используйте эти опции, если вы не хотите чтобы грабберы создавали новую категоризацию";
$lang['plugins']['grabbers']['field_options_other']                     = "Другие опции";
$lang['plugins']['grabbers']['field_options_other_duplicates']          = "Не добавлять контент с существующими названиями";
$lang['plugins']['grabbers']['field_options_other_duplicates_hint']     = "запрещает импортировать контент с названиями, которые уже есть в вашей базе; это может помочь справиться с дубликатами контента на разных сайтах";
$lang['plugins']['grabbers']['field_options_other_need_review']         = "Пометить весь контент флагом \"Требует проверки\"";
$lang['plugins']['grabbers']['field_options_other_need_review_hint']    = "позволяет легко отфильтровать весь добавленный контент в панели администрирования";
$lang['plugins']['grabbers']['field_options_other_randomize_time']      = "Случайное время публикации";
$lang['plugins']['grabbers']['field_options_other_randomize_time_hint'] = "если включить, то время публикации всех импортируемых видео будет выставлено случайно в интервале от 00:00 до 23:59; в противном случае все импортируемые видео будут иметь текущее серверное время";
$lang['plugins']['grabbers']['field_videos_amount']                     = "Видео";
$lang['plugins']['grabbers']['field_albums_amount']                     = "Альбомы";
$lang['plugins']['grabbers']['field_models_amount']                     = "Модели";
$lang['plugins']['grabbers']['field_total']                             = "Всего";
$lang['plugins']['grabbers']['field_last_exec']                         = "Последний запуск";
$lang['plugins']['grabbers']['field_last_exec_none']                    = "нет";
$lang['plugins']['grabbers']['field_last_exec_info']                    = "(%1% секунд, %2% добавлено, %3% дубликатов)";
$lang['plugins']['grabbers']['error_invalid_grabber_file']              = "[kt|b]%1%[/kt|b]: загруженный файл не является корректным PHP файлом или не реализует API граббера корректно";
$lang['plugins']['grabbers']['error_same_formats_multiple_quality']     = "[kt|b]%1%[/kt|b]: вы должны указать разные форматы видео для файлов разного качества";
$lang['plugins']['grabbers']['error_autopilot_url_not_supported']       = "[kt|b]%1%[/kt|b]: один из указанных URL-ов не поддерживается этим граббером (%2%)";
$lang['plugins']['grabbers']['error_ydl_path_invalid']                  = "[kt|b]%1%[/kt|b]: некорректный путь к библиотеке";
$lang['plugins']['grabbers']['error_no_dom_module_installed']           = "Модуль PHP DOM не установлен.";
$lang['plugins']['grabbers']['error_no_grabbers_installed']             = "Не установлено ни одного граббера. Установите грабберы, чтобы использовать их в загрузке контента.";
$lang['plugins']['grabbers']['error_grabber_broken']                    = "Этот граббер помечен как нерабочий и в настоящее время не может использоваться. Как только он будет исправлен, он включится автоматически.";
$lang['plugins']['grabbers']['error_grabber_noydl']                     = "Библиотека youtube-dl не найдена (https://github.com/rg3/youtube-dl)";
$lang['plugins']['grabbers']['btn_save']                                = "Сохранить";
$lang['plugins']['grabbers']['btn_upload']                              = "Загрузить";
$lang['plugins']['grabbers']['btn_back']                                = "Назад";
$lang['plugins']['grabbers']['btn_confirm']                             = "Подтвердить";
