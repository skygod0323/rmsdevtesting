<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// custom_list_videos messages
// =====================================================================================================================

$lang['custom_list_videos']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['custom_list_videos']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['custom_list_videos']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['custom_list_videos']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['custom_list_videos']['groups']['search']            = "Текстовый поиск видео";
$lang['custom_list_videos']['groups']['related']           = "Похожие видео";
$lang['custom_list_videos']['groups']['connected_albums']  = "Видео, привязанное к альбому";
$lang['custom_list_videos']['groups']['display_modes']     = $lang['website_ui']['block_group_default_display_modes'];
$lang['custom_list_videos']['groups']['subselects']        = "Выборка дополнительных данных для каждого видео";
$lang['custom_list_videos']['groups']['access']            = "Ограничение доступа к видео";
$lang['custom_list_videos']['groups']['rotator']           = "CTR ротатор";

$lang['custom_list_videos']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['custom_list_videos']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['custom_list_videos']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['custom_list_videos']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['custom_list_videos']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['custom_list_videos']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['custom_list_videos']['params']['skip_categories']                   = "Запрещает выводить видео из данных категорий (список ID категорий разделенных через запятую).";
$lang['custom_list_videos']['params']['show_categories']                   = "Позволяет выводить только видео из данных категорий (список ID категорий разделенных через запятую).";
$lang['custom_list_videos']['params']['skip_tags']                         = "Запрещает выводить видео с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['custom_list_videos']['params']['show_tags']                         = "Позволяет выводить только видео с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['custom_list_videos']['params']['skip_models']                       = "Запрещает выводить видео с данными моделями (список ID моделей разделенных через запятую).";
$lang['custom_list_videos']['params']['show_models']                       = "Позволяет выводить только видео с данными моделями (список ID моделей разделенных через запятую).";
$lang['custom_list_videos']['params']['skip_content_sources']              = "Запрещает выводить видео от данных контент провайдеров (список ID контент провайдеров разделенных через запятую).";
$lang['custom_list_videos']['params']['show_content_sources']              = "Позволяет выводить только видео от данных контент провайдеров (список ID контент провайдеров разделенных через запятую).";
$lang['custom_list_videos']['params']['skip_dvds']                         = "Запрещает выводить видео из данных DVD / каналов (список ID DVD / каналов разделенных через запятую).";
$lang['custom_list_videos']['params']['show_dvds']                         = "Позволяет выводить только видео из данных DVD / каналов (список ID DVD / каналов разделенных через запятую).";
$lang['custom_list_videos']['params']['skip_users']                        = "Запрещает выводить видео от данных пользователей (список ID пользователей разделенных через запятую).";
$lang['custom_list_videos']['params']['show_users']                        = "Позволяет выводить только видео от данных пользователей (список ID пользователей разделенных через запятую).";
$lang['custom_list_videos']['params']['show_only_with_description']        = "Позволяет выводить только видео, у которых задано не пустое описание.";
$lang['custom_list_videos']['params']['show_only_from_same_country']       = "Включите эту опцию для отображения только тех видео, которые были загружены пользователями из такой же страны, как и текущий пользователь.";
$lang['custom_list_videos']['params']['show_with_admin_flag']              = "Вы можете указать внешний ID флага для того, чтобы показать только те видео, у которых установлен этот флаг в качестве флага админа.";
$lang['custom_list_videos']['params']['skip_with_admin_flag']              = "Вы можете указать внешний ID флага для того, чтобы не показывать те видео, у которых установлен этот флаг в качестве флага админа.";
$lang['custom_list_videos']['params']['days_passed_from']                  = "Позволяет фильтровать список видео по дате публикации, например, видео добавленное сегодня, вчера, за неделю и т.д. Указывает верхнюю границу даты публикации в кол-ве дней, прошедших с текущего дня.";
$lang['custom_list_videos']['params']['days_passed_to']                    = "Позволяет фильтровать список видео по дате публикации, например, видео добавленное сегодня, вчера, за неделю и т.д. Указывает нижнюю границу даты публикации в кол-ве дней, прошедших с текущего дня. Значение должно быть больше, чем значение в параметре блока [kt|b]days_passed_from[/kt|b].";
$lang['custom_list_videos']['params']['is_private']                        = "Позволяет выводить видео с различной доступностью.";
$lang['custom_list_videos']['params']['is_hd']                             = "Позволяет выводить HD или не-HD видео.";
$lang['custom_list_videos']['params']['format_postfix']                    = "Позволяет выводить видео, у которых есть видеофайл формата с указанным постфиксом.";
$lang['custom_list_videos']['params']['var_title_section']                 = "HTTP параметр, в котором передаются первые буквы названия для фильтрации списка.";
$lang['custom_list_videos']['params']['var_category_dir']                  = "HTTP параметр, в котором передается директория категории. Позволяет выводить только видео из категории с заданной директорией.";
$lang['custom_list_videos']['params']['var_category_id']                   = "HTTP параметр, в котором передается ID категории. Позволяет выводить только видео из категории с заданным ID.";
$lang['custom_list_videos']['params']['var_category_ids']                  = "HTTP параметр, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только видео из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те видео, которые одновременно принадлежат всем перечисленным категориям.";
$lang['custom_list_videos']['params']['var_category_group_dir']            = "HTTP параметр, в котором передается директория группы категорий. Позволяет выводить только видео из группы категорий с заданной директорией.";
$lang['custom_list_videos']['params']['var_category_group_id']             = "HTTP параметр, в котором передается ID группы категорий. Позволяет выводить только видео из группы категорий с заданным ID.";
$lang['custom_list_videos']['params']['var_category_group_ids']            = "HTTP параметр, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только видео из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те видео, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['custom_list_videos']['params']['var_tag_dir']                       = "HTTP параметр, в котором передается директория тэга. Позволяет выводить только видео, у которых есть тэг с заданной директорией.";
$lang['custom_list_videos']['params']['var_tag_id']                        = "HTTP параметр, в котором передается ID тэга. Позволяет выводить только видео, у которых есть тэг с заданным ID.";
$lang['custom_list_videos']['params']['var_tag_ids']                       = "HTTP параметр, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только видео у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те видео, у которых одновременно есть все перечисленные тэги.";
$lang['custom_list_videos']['params']['var_model_dir']                     = "HTTP параметр, в котором передается директория модели. Позволяет выводить только видео, у которых есть модель с заданной директорией.";
$lang['custom_list_videos']['params']['var_model_id']                      = "HTTP параметр, в котором передается ID модели. Позволяет выводить только видео, у которых есть модель с заданным ID.";
$lang['custom_list_videos']['params']['var_model_ids']                     = "HTTP параметр, в котором передается список ID моделей, разделенных через запятую. Позволяет выводить только видео у которых есть модели с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те видео, у которых одновременно есть все перечисленные модели.";
$lang['custom_list_videos']['params']['var_model_group_dir']               = "HTTP параметр, в котором передается директория группы моделей. Позволяет выводить только видео из группы моделей с заданной директорией.";
$lang['custom_list_videos']['params']['var_model_group_id']                = "HTTP параметр, в котором передается ID группы моделей. Позволяет выводить только видео из группы моделей с заданным ID.";
$lang['custom_list_videos']['params']['var_model_group_ids']               = "HTTP параметр, в котором передается список ID групп моделей, разделенных через запятую. Позволяет выводить только видео из групп моделей с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те видео, которые одновременно принадлежат всем перечисленным группам моделей.";
$lang['custom_list_videos']['params']['var_content_source_dir']            = "HTTP параметр, в котором передается директория контент провайдера. Позволяет выводить видео по контент провайдеру с заданной директорией.";
$lang['custom_list_videos']['params']['var_content_source_id']             = "HTTP параметр, в котором передается ID контент провайдера. Позволяет выводить видео по контент провайдеру с заданным ID.";
$lang['custom_list_videos']['params']['var_content_source_ids']            = "HTTP параметр, в котором передается список ID контент провайдеров, разделенных через запятую. Позволяет выводить только видео из контент провайдеров с заданными ID.";
$lang['custom_list_videos']['params']['var_content_source_group_dir']      = "HTTP параметр, в котором передается директория группы контент провайдеров. Позволяет выводить только видео из группы контент провайдеров с заданной директорией.";
$lang['custom_list_videos']['params']['var_content_source_group_id']       = "HTTP параметр, в котором передается ID группы контент провайдеров. Позволяет выводить только видео из группы контент провайдеров с заданным ID.";
$lang['custom_list_videos']['params']['var_content_source_group_ids']      = "HTTP параметр, в котором передается список ID групп контент провайдеров, разделенных через запятую. Позволяет выводить только видео из групп контент провайдеров с заданными ID.";
$lang['custom_list_videos']['params']['var_dvd_dir']                       = "HTTP параметр, в котором передается директория DVD / канала. Позволяет выводить видео по DVD / каналу с заданной директорией.";
$lang['custom_list_videos']['params']['var_dvd_id']                        = "HTTP параметр, в котором передается ID DVD / канала. Позволяет выводить видео по DVD / каналу с заданным ID.";
$lang['custom_list_videos']['params']['var_dvd_ids']                       = "HTTP параметр, в котором передается список ID DVD / каналов, разделенных через запятую. Позволяет выводить только видео из DVD / каналов с заданными ID.";
$lang['custom_list_videos']['params']['var_dvd_group_dir']                 = "HTTP параметр, в котором передается директория группы DVD / каналов. Позволяет выводить только видео из группы DVD / каналов с заданной директорией.";
$lang['custom_list_videos']['params']['var_dvd_group_id']                  = "HTTP параметр, в котором передается ID группы DVD / каналов. Позволяет выводить только видео из группы DVD / каналов с заданным ID.";
$lang['custom_list_videos']['params']['var_dvd_group_ids']                 = "HTTP параметр, в котором передается список ID групп DVD / каналов, разделенных через запятую. Позволяет выводить только видео из групп DVD / каналов с заданными ID.";
$lang['custom_list_videos']['params']['var_is_private']                    = "HTTP параметр, который позволяет выводить видео с различной доступностью базируясь на переданных в параметре значениях (список ID доступов разделенных через запятую, где 2 - премиум видео, 1 - личные видео и 0 - публичные видео). Перекрывает параметр [kt|b]is_private[/kt|b].";
$lang['custom_list_videos']['params']['var_is_hd']                         = "HTTP параметр, который позволяет выводить HD или не-HD видео базируясь на переданном в параметре значении (0 - не-HD видео, 1 - HD видео). Перекрывает параметр [kt|b]is_hd[/kt|b].";
$lang['custom_list_videos']['params']['var_post_date_from']                = "HTTP параметр, который позволяет выводить видео с датой публикации большей или равной чем дата, которая передается в данном параметре (YYYY-MM-DD).";
$lang['custom_list_videos']['params']['var_post_date_to']                  = "HTTP параметр, который позволяет выводить видео с датой публикации меньшей чем дата, которая передается в данном параметре (YYYY-MM-DD).";
$lang['custom_list_videos']['params']['var_duration_from']                 = "HTTP параметр, который позволяет выводить видео с длительностью большей или равной чем цифра, которая передается в данном параметре (кол-во секунд).";
$lang['custom_list_videos']['params']['var_duration_to']                   = "HTTP параметр, который позволяет выводить видео с длительностью меньшей чем цифра, которая передается в данном параметре (кол-во секунд).";
$lang['custom_list_videos']['params']['var_release_year_from']             = "HTTP параметр, который позволяет выводить видео с годом выпуска большим или равным чем год, который передается в данном параметре.";
$lang['custom_list_videos']['params']['var_release_year_to']               = "HTTP параметр, который позволяет выводить видео с годом выпуска меньшим чем год, который передается в данном параметре.";
$lang['custom_list_videos']['params']['var_custom_flag1']                  = "HTTP параметр, в котором передается значение доп. флага #1 для фильтрации видео с этим значением флага.";
$lang['custom_list_videos']['params']['var_custom_flag2']                  = "HTTP параметр, в котором передается значение доп. флага #2 для фильтрации видео с этим значением флага.";
$lang['custom_list_videos']['params']['var_custom_flag3']                  = "HTTP параметр, в котором передается значение доп. флага #3 для фильтрации видео с этим значением флага.";
$lang['custom_list_videos']['params']['var_search']                        = "HTTP параметр, в котором передается поисковая строка. Позволяет выводить только видео, которые соответствуют поисковой строке.";
$lang['custom_list_videos']['params']['search_method']                     = "Устанавливает метод поиска.";
$lang['custom_list_videos']['params']['search_scope']                      = "Указывает, по каким полям должен идти поиск.";
$lang['custom_list_videos']['params']['search_redirect_enabled']           = "Включает редирект на страницу видео, если результаты поиска содержат только 1 видео.";
$lang['custom_list_videos']['params']['search_redirect_pattern']           = "Паттерн страницы видео, на которую нужно перенаправлять пользователя, если результаты поиска содержат только 1 видео (в этом случае пользователь будет мгновенно перенаправлен на страницу этого видео). Паттерн должен содержать как минимум один из токенов: [kt|b]%ID%[/kt|b] и / или [kt|b]%DIR%[/kt|b]. Если этот параметр оставлен пустым (по умолчанию), то будет использоваться глобальный [kt|b]Паттерн для страницы видео на сайте[/kt|b].";
$lang['custom_list_videos']['params']['search_empty_404']                  = "Заставляет блок выдавать 404 ошибку при пустых результатах поиска.";
$lang['custom_list_videos']['params']['search_empty_redirect_to']          = "Заставляет блок делать редирект на указанный URL при пустых результатах поиска. В можете использовать токен [kt|b]%QUERY%[/kt|b], который заменится на поисковую строку.";
$lang['custom_list_videos']['params']['enable_search_on_tags']             = "Включает поиск по названию тэга и, если тэг с таким названием находится, то видео с этим тэгом попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['enable_search_on_categories']       = "Включает поиск по названию категории и, если категория с таким названием находится, то видео из этой категории попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['enable_search_on_models']           = "Включает поиск по названию модели и, если модель с таким названием находится, то видео по этой модели попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['enable_search_on_cs']               = "Включает поиск по названию контент провайдера и, если контент провайдер с таким названием находится, то видео этого контент провайдера попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['enable_search_on_dvds']             = "Включает поиск по названию DVD и, если DVD с таким названием находится, то видео из этого DVD попадают в результат поиска. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['enable_search_on_custom_fields']    = "Включает поиск по доп. полям видео. Может ухудшить производительность поиска.";
$lang['custom_list_videos']['params']['mode_related']                      = "Включает режим отображения похожих видео.";
$lang['custom_list_videos']['params']['var_video_dir']                     = "Используется при включенном параметре [kt|b]mode_related[/kt|b]. HTTP параметр, в котором передается директория видео для отображения похожих на него видео.";
$lang['custom_list_videos']['params']['var_video_id']                      = "Используется при включенном параметре [kt|b]mode_related[/kt|b]. HTTP параметр, в котором передается ID видео для отображения похожих на него видео.";
$lang['custom_list_videos']['params']['mode_related_category_group_id']    = "Используется при включенном параметре [kt|b]mode_related[/kt|b] по категориям. Укажите ID или внешний ID группы категорий, чтобы ограничить похожие видео только этой группой категорий.";
$lang['custom_list_videos']['params']['mode_related_model_group_id']       = "Используется при включенном параметре [kt|b]mode_related[/kt|b] по моделям. Укажите ID или внешний ID группы моделей, чтобы ограничить похожие видео только этой группой моделей.";
$lang['custom_list_videos']['params']['var_mode_related']                  = "Позволяет динамически переключать режим отображения похожих видео, передавая одно из значений в HTTP параметре: [kt|b]1[/kt|b] - по контент провайдеру, [kt|b]2[/kt|b] - по тэгам, [kt|b]3[/kt|b] - по категориям, [kt|b]4[/kt|b] - по моделям, [kt|b]5[/kt|b] - по DVD / каналу, [kt|b]6[/kt|b] и [kt|b]7[/kt|b] - по названию, [kt|b]8[/kt|b] - по пользователю.";
$lang['custom_list_videos']['params']['mode_connected_album']              = "Включает режим отображения связанного с выбранным фотоальбомом видео (всегда или 1 видео, или пустой список).";
$lang['custom_list_videos']['params']['var_connected_album_dir']           = "Используется при включенном параметре [kt|b]mode_connected_album[/kt|b]. HTTP параметр, в котором передается директория фотоальбома для отображения связанного с ним видео.";
$lang['custom_list_videos']['params']['var_connected_album_id']            = "Используется при включенном параметре [kt|b]mode_connected_album[/kt|b]. HTTP параметр, в котором передается ID фотоальбома для отображения связанного с ним видео.";
$lang['custom_list_videos']['params']['mode_favourites']                   = "Включает режим отображения закладок видео пользователя.";
$lang['custom_list_videos']['params']['mode_uploaded']                     = "Включает режим отображения загруженных пользователем видео.";
$lang['custom_list_videos']['params']['mode_dvd']                          = "Включает режим отображения видео из указанного DVD / канала, принадлежащего пользователю.";
$lang['custom_list_videos']['params']['mode_purchased']                    = "Включает режим отображения купленных пользователем видео.";
$lang['custom_list_videos']['params']['mode_history']                      = "Включает режим отображения истории просмотра пользователя.";
$lang['custom_list_videos']['params']['mode_subscribed']                   = "Включает режим отображения видео из подписок пользователя.";
$lang['custom_list_videos']['params']['mode_futures']                      = "Включает режим отображения будущих видео.";
$lang['custom_list_videos']['params']['fav_type']                          = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. Указывает тип закладок: 0 - основной список закладок, 10 - плэйлист, ID которого передается в параметре блока [kt|b]var_playlist_id[/kt|b], 1-9 - доп. списки закладок, которые вы можете использовать по своему усмотрению.";
$lang['custom_list_videos']['params']['var_fav_type']                      = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. HTTP параметр, в котором передается тип закладок: 0 - основной список закладок, 10 - плэйлист, ID которого передается в параметре блока [kt|b]var_playlist_id[/kt|b], 1-9 - доп. списки закладок, которые вы можете использовать по своему усмотрению.";
$lang['custom_list_videos']['params']['var_playlist_id']                   = "Используется при включенном параметре [kt|b]mode_favourites[/kt|b]. HTTP параметр, в котором передается ID плэйлиста.";
$lang['custom_list_videos']['params']['var_user_id']                       = "Используется при включенных параметрах [kt|b]mode_favourites[/kt|b] и [kt|b]mode_uploaded[/kt|b] и [kt|b]mode_purchased[/kt|b] и [kt|b]mode_history[/kt|b]. HTTP параметр, в котором передается ID пользователя, чьи закладки / загруженные видео / купленные видео / история просмотра должны быть выведены. Если не задан, то выводятся закладки / загруженные видео / купленные видео / история просмотра текущего пользователя.";
$lang['custom_list_videos']['params']['redirect_unknown_user_to']          = "Используется при включенных параметрах [kt|b]mode_favourites[/kt|b] и [kt|b]mode_uploaded[/kt|b] и [kt|b]mode_purchased[/kt|b] и [kt|b]mode_history[/kt|b]. Указывает путь, на который будет перенаправлен незалогиненный пользователь при попытке доступа к своим личным закладкам / загруженным видео / купленным видео / истории просмотра (в большинстве случаев это путь на страницу с формой логина).";
$lang['custom_list_videos']['params']['allow_delete_uploaded_videos']      = "Используется при включенном параметре [kt|b]mode_uploaded[/kt|b]. Разрешает пользователям удалять свои загруженные видео.";
$lang['custom_list_videos']['params']['show_content_source_info']          = "Включает выборку данных о контент провайдере для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_categories_info']              = "Включает выборку данных о категориях для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_tags_info']                    = "Включает выборку данных о тэгах для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_models_info']                  = "Включает выборку данных о моделях для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_dvd_info']                     = "Включает выборку данных о DVD / канале для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_user_info']                    = "Включает выборку данных о пользователе для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_flags_info']                   = "Включает выборку данных о флагах для каждого видео (работает медленнее).";
$lang['custom_list_videos']['params']['show_comments']                     = "Включает возможность выборки списка комментариев для каждого видео. Количество комментариев настраивается отдельным параметром блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['custom_list_videos']['params']['show_comments_count']               = "Может использоваться при включенном параметре блока [kt|b]show_comments[/kt|b]. Указывает кол-во комментариев, которое выбирается для каждого видео.";
$lang['custom_list_videos']['params']['show_private']                      = "Устанавливает для пользователей каких статусов следует показывать личные видео.";
$lang['custom_list_videos']['params']['show_premium']                      = "Устанавливает для пользователей каких статусов следует показывать премиум видео.";
$lang['custom_list_videos']['params']['disable_rotator']                   = "Выключить ротатор видео и скриншотов видео для этого блока.";
$lang['custom_list_videos']['params']['finished_rotation']                 = "Если этот параметр включен, то блок будет выводить только те видео, по которым завершена ротация скриншотов.";
$lang['custom_list_videos']['params']['under_rotation']                    = "Если этот параметр включен, то блок будет выводить только те видео, по которым не завершена ротация скриншотов.";
$lang['custom_list_videos']['params']['show_best_screenshots']             = "Этот параметр может использоваться для отображения наилучших (по CTR) скриншотов по всем видео, игнорируя степень завершенности ротации скриншотов.";
$lang['custom_list_videos']['params']['randomize_positions']               = "Включает примесь для списков, отсортированных по CTR. Укажите список позиций через запятую (первая позиция имеет цифру 1), которые должны быть заменены другими видео. Например, [kt|b]1,4,7[/kt|b] указывает что необходимо подмешать первое, четвертое и седьмое видео в списке, выбрав данные видео по другой сортировке, которая задается в параметре [kt|b]randomize_positions_sort_by[/kt|b].";
$lang['custom_list_videos']['params']['randomize_positions_sort_by']       = "Указывает сортировку для примеси, которая задается в параметре [kt|b]randomize_positions[/kt|b].";

$lang['custom_list_videos']['values']['is_private']['0']                               = "Только публичные";
$lang['custom_list_videos']['values']['is_private']['1']                               = "Только личные";
$lang['custom_list_videos']['values']['is_private']['2']                               = "Только премиум";
$lang['custom_list_videos']['values']['is_private']['0|1']                             = "Только публичные и личные";
$lang['custom_list_videos']['values']['is_private']['0|2']                             = "Только публичные и премиум";
$lang['custom_list_videos']['values']['is_private']['1|2']                             = "Только личные и премиум";
$lang['custom_list_videos']['values']['is_hd']['0']                                    = "Только не-HD";
$lang['custom_list_videos']['values']['is_hd']['1']                                    = "Только HD";
$lang['custom_list_videos']['values']['search_method']['1']                            = "Полное совпадение с запросом";
$lang['custom_list_videos']['values']['search_method']['2']                            = "Совпадение с элементами запроса";
$lang['custom_list_videos']['values']['search_method']['3']                            = "Полнотекстовый индекс (натуральный режим)";
$lang['custom_list_videos']['values']['search_method']['4']                            = "Полнотекстовый индекс (булевый режим)";
$lang['custom_list_videos']['values']['search_method']['5']                            = "Полнотекстовый индекс (с расширенным подзапросом)";
$lang['custom_list_videos']['values']['search_scope']['0']                             = "Название и описание";
$lang['custom_list_videos']['values']['search_scope']['1']                             = "Только название";
$lang['custom_list_videos']['values']['search_scope']['2']                             = "Ничего";
$lang['custom_list_videos']['values']['mode_related']['1']                             = "Похожие по контент провайдеру";
$lang['custom_list_videos']['values']['mode_related']['2']                             = "Похожие по тэгам";
$lang['custom_list_videos']['values']['mode_related']['3']                             = "Похожие по категориям";
$lang['custom_list_videos']['values']['mode_related']['4']                             = "Похожие по моделям";
$lang['custom_list_videos']['values']['mode_related']['5']                             = "Похожие по DVD / каналу";
$lang['custom_list_videos']['values']['mode_related']['6']                             = "Похожие по названию (натуральный режим)";
$lang['custom_list_videos']['values']['mode_related']['7']                             = "Похожие по названию (с расширенным подзапросом)";
$lang['custom_list_videos']['values']['mode_related']['8']                             = "Похожие по пользователю";
$lang['custom_list_videos']['values']['show_private']['1']                             = "Только для зарег. пользователей";
$lang['custom_list_videos']['values']['show_private']['2']                             = "Только для премиум пользователей";
$lang['custom_list_videos']['values']['show_premium']['1']                             = "Только для зарег. пользователей";
$lang['custom_list_videos']['values']['show_premium']['2']                             = "Только для премиум пользователей";
$lang['custom_list_videos']['values']['sort_by']['video_id']                           = "ID видео";
$lang['custom_list_videos']['values']['sort_by']['title']                              = "Название";
$lang['custom_list_videos']['values']['sort_by']['dir']                                = "Директория";
$lang['custom_list_videos']['values']['sort_by']['duration']                           = "Длительность";
$lang['custom_list_videos']['values']['sort_by']['release_year']                       = "Год выпуска";
$lang['custom_list_videos']['values']['sort_by']['post_date']                          = "Дата публикации";
$lang['custom_list_videos']['values']['sort_by']['post_date_and_popularity']           = "Дата публикации (по популярности)";
$lang['custom_list_videos']['values']['sort_by']['post_date_and_rating']               = "Дата публикации (по рейтингу)";
$lang['custom_list_videos']['values']['sort_by']['post_date_and_duration']             = "Дата публикации (по длительности)";
$lang['custom_list_videos']['values']['sort_by']['last_time_view_date']                = "Последний просмотр";
$lang['custom_list_videos']['values']['sort_by']['last_time_view_date_and_popularity'] = "Последний просмотр (по популярности)";
$lang['custom_list_videos']['values']['sort_by']['last_time_view_date_and_rating']     = "Последний просмотр (по рейтингу)";
$lang['custom_list_videos']['values']['sort_by']['last_time_view_date_and_duration']   = "Последний просмотр (по длительности)";
$lang['custom_list_videos']['values']['sort_by']['rating']                             = "Суммарный рейтинг";
$lang['custom_list_videos']['values']['sort_by']['rating_today']                       = "Рейтинг за сегодня";
$lang['custom_list_videos']['values']['sort_by']['rating_week']                        = "Рейтинг за неделю";
$lang['custom_list_videos']['values']['sort_by']['rating_month']                       = "Рейтинг за месяц";
$lang['custom_list_videos']['values']['sort_by']['video_viewed']                       = "Суммарная популярность";
$lang['custom_list_videos']['values']['sort_by']['video_viewed_today']                 = "Популярность за сегодня";
$lang['custom_list_videos']['values']['sort_by']['video_viewed_week']                  = "Популярность за неделю";
$lang['custom_list_videos']['values']['sort_by']['video_viewed_month']                 = "Популярность за месяц";
$lang['custom_list_videos']['values']['sort_by']['most_favourited']                    = "Добавление в закладки";
$lang['custom_list_videos']['values']['sort_by']['most_commented']                     = "Кол-во комментариев";
$lang['custom_list_videos']['values']['sort_by']['most_purchased']                     = "Кол-во покупок";
$lang['custom_list_videos']['values']['sort_by']['ctr']                                = "CTR (ротатор)";
$lang['custom_list_videos']['values']['sort_by']['custom1']                            = "Доп. поле 1";
$lang['custom_list_videos']['values']['sort_by']['custom2']                            = "Доп. поле 2";
$lang['custom_list_videos']['values']['sort_by']['custom3']                            = "Доп. поле 3";
$lang['custom_list_videos']['values']['sort_by']['dvd_sort_id']                        = "ID сортировки в DVD / канале";
$lang['custom_list_videos']['values']['sort_by']['pseudo_rand']                        = "Псевдослучайно (быстро)";
$lang['custom_list_videos']['values']['sort_by']['rand()']                             = "Случайно (очень медленно)";
$lang['custom_list_videos']['values']['randomize_positions_sort_by']['post_date']      = "Дата публикации";
$lang['custom_list_videos']['values']['randomize_positions_sort_by']['rating']         = "Суммарный рейтинг";
$lang['custom_list_videos']['values']['randomize_positions_sort_by']['video_viewed']   = "Суммарная популярность";
$lang['custom_list_videos']['values']['randomize_positions_sort_by']['random1']        = "Псевдослучайно (быстро)";
$lang['custom_list_videos']['values']['randomize_positions_sort_by']['rand()']         = "Случайно (очень медленно)";

if (isset($options))
{
	if ($options['VIDEO_FIELD_1_NAME']!='')
	{
		$lang['custom_list_videos']['values']['sort_by']['custom1'] = $options['VIDEO_FIELD_1_NAME'] . " (" . $lang['custom_list_videos']['values']['sort_by']['custom1']. ")";
	}
	if ($options['VIDEO_FIELD_2_NAME']!='')
	{
		$lang['custom_list_videos']['values']['sort_by']['custom2'] = $options['VIDEO_FIELD_2_NAME'] . " (" . $lang['custom_list_videos']['values']['sort_by']['custom2']. ")";
	}
	if ($options['VIDEO_FIELD_3_NAME']!='')
	{
		$lang['custom_list_videos']['values']['sort_by']['custom3'] = $options['VIDEO_FIELD_3_NAME'] . " (" . $lang['custom_list_videos']['values']['sort_by']['custom3']. ")";
	}
}

$lang['custom_list_videos']['block_short_desc'] = "Выводит список видео с заданными опциями";

$lang['custom_list_videos']['block_desc'] = "
	Блок предназначен для отображения списка видео с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Существует 9 различных типов листинга видео:[kt|br]
	1) Список видео, добавленных в личные закладки пользователя. В этом случае должен быть включен параметр блока
		[kt|b]mode_favourites[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
		отобразит список закладок пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр
		блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список закладок текущего пользователя
		('мои закладки'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
		[kt|b]redirect_unknown_user_to[/kt|b]. В данном списке видео существует также возможность удаления видео из
		своих закладок. Список закладок может быть разделен на несколько типов списков. За вывод списка определенного
		типа отвечает параметр блока [kt|b]var_fav_type[/kt|b], который ссылается на HTTP параметр, в котором вы должны
		передать тип списка закладок. Для того, чтобы вывести закладки из какого-либо плэйлиста, необходимо передать ID
		этого плэйлиста в HTTP параметре, который задается параметром блока [kt|b]var_playlist_id[/kt|b].[kt|br]
	2) Список видео, загруженных пользователем. В этом случае должен быть включен параметр блока
		[kt|b]mode_uploaded[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит
		список загруженных видео пользователем, ID которого передается в соответствующем HTTP параметре. Если параметр
		блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список загруженных видео текущего
		пользователя ('мои загруженные видео'), а если он не залогинен, то перенаправит его по пути, указанному в
		параметре блока [kt|b]redirect_unknown_user_to[/kt|b]. В данном списке видео существует также возможность
		удаления своих загруженных видео. По умолчанию эта возможность выключена; для ее включения следует включить
		параметр блока [kt|b]allow_delete_uploaded_videos[/kt|b].[kt|br]
	3) Список видео из канала, созданного пользователем. В этом случае должны быть включены параметры блока
		[kt|b]mode_dvd[/kt|b] и [kt|b]var_dvd_id[/kt|b]. Если текущий пользователь не залогинен, то блок перенаправит
		его по пути, указанному в параметре блока [kt|b]redirect_unknown_user_to[/kt|b]. В данном списке видео
		существует также возможность удаления видео из своего DVD / канала.[kt|br]
	4) Список видео, купленных пользователем. В этом случае должен быть включен параметр блока
		[kt|b]mode_purchased[/kt|b]. Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок
		отобразит список купленных видео пользователем, ID которого передается в соответствующем HTTP параметре. Если
		параметр блока [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести список купленных видео текущего
		пользователя ('мои купленные видео'), а если он не залогинен, то перенаправит его по пути, указанному в
		параметре блока [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	5) История просмотров пользователя. В этом случае должен быть включен параметр блока [kt|b]mode_history[/kt|b].
		Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит историю просмотров
		пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
		[kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести историю просмотров текущего пользователя ('моя
		история просмотров'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
		[kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	6) Видео из подписок пользователя. В этом случае должен быть включен параметр блока [kt|b]mode_subscribed[/kt|b].
		Если дополнительно указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит видео из подписок
		пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
		[kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести видео из подписок текущего пользователя ('мои
		видео из подписок'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
		[kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	7) Список похожих видео. Для отображения данного списка следует использовать параметр блока
		[kt|b]mode_related[/kt|b], который позволяет выбрать различную методику рассчета похожих видео. Также должен
		быть указан один из параметров блока [kt|b]var_video_dir[/kt|b] или [kt|b]var_video_id[/kt|b], который указывает
		в каком HTTP параметре приходит информация о текущем видео (чтобы найти похожие на него). Для данного списка
		работают все параметры фильтрации и сортировки. Вы также можете включить [kt|b]var_mode_related[/kt|b] параметр
		для того, чтобы передавать методику рассчета похожих видео динамически.[kt|br]
	8)  Список будущих видео. Для отображения данного списка следует использовать параметр блока
		[kt|b]mode_futures[/kt|b].[kt|br]
	9) Обычный список видео для просмотра. Для данного списка работают все параметры фильтрации и сортировки.
	[kt|br][kt|br]

	Если вам нужно исключить из результата видео из каких-либо категорий, тэгов, моделей и т.д. вам следует
	воспользоваться параметрами блока [kt|b]skip_categories[/kt|b], [kt|b]skip_tags[/kt|b], [kt|b]skip_models[/kt|b]
	и т.д. Для того, чтобы отобразить видео только из каких-либо категорий, тэгов, моделей и т.д., используйте
	параметры блока [kt|b]show_categories[/kt|b], [kt|b]show_tags[/kt|b], [kt|b]show_models[/kt|b] и т.д.
	[kt|br][kt|br]

	Если вы хотите показать только видео с непустым описанием, то включите параметр блока
	[kt|b]show_only_with_description[/kt|b].
	[kt|br][kt|br]

	Для того, чтобы вывести видео по какой-либо категории (тэгу или модели, по какому-либо контент провайдеру или
	DVD / каналу), следует использовать один из параметров блока [kt|b]var_xxx_dir[/kt|b] или [kt|b]var_xxx_id[/kt|b].
	[kt|br][kt|br]

	Вы можете настраивать этот блок, чтобы он выводил видео, которые появились в заданный промежуток времени,
	относительно текущей даты, например, видео за сегодня, видео за вчера, за неделю и т.д. Этот промежуток должен
	задаваться парой параметров блока [kt|b]days_passed_from[/kt|b] и [kt|b]days_passed_to[/kt|b].
	[kt|br][kt|br]

	Для реализации поиска по видео вы можете использовать параметр блока [kt|b]var_search[/kt|b].
	Он должен ссылаться на HTTP параметр, который содержит поисковую строку. Вы можете также выбрать один
	из нескольких поисковых методов в параметре блока [kt|b]search_method[/kt|b]. Результаты поиска могут быть
	расширены за счет включения параметров блока [kt|b]enable_search_on_xxx[/kt|b], однако они могут сильно
	ухудшить производительность поиска.
	[kt|br][kt|br]

	Вы можете настроить блок таким образом, чтобы он выводил только видео, название которых
	начинается с указанной подстроки (или буквы). Для этого параметр блока [kt|b]var_title_section[/kt|b]
	должен ссылаться на HTTP параметр, который содержит комбинацию первых символов. Регистр букв в
	комбинации не имеет значения. Так вы можете показывать отдельный список видео для каждой
	буквы алфавита.
	[kt|br][kt|br]

	Блок позволяет отфильтровать видео по указанным пределам длительности. Эта возможность может использоваться
	совместно с поиском или другими фильтрами. Для ее использования вам необходимо включить параметры блока
	[kt|b]var_duration_from[/kt|b] и / или [kt|b]var_duration_to[/kt|b].
	[kt|br][kt|br]

	При необходимости отображения на списке видео информации о контент провайдере, категориях, моделях, тэгах или
	пользователе для каждого видео вам нужно включить необходимые параметры блока [kt|b]show_xxx_info[/kt|b]. Это
	заставит блок делать дополнительные выборки для получения нужной информации, что будет работать медленнее.
	[kt|br][kt|br]

	Если вам необходимо спрятать личные или премиум видео от различных категорий пользователей
	(незарегистрированных или стандартных пользователей), воспользуйтесь параметрами блока
	[kt|b]show_private[/kt|b] или [kt|b]show_premium[/kt|b].
	[kt|br][kt|br]

	Блок позволяет вывести видео, связанное с каким-либо фотоальбомом. Для того, чтобы этого добиться,
	вам необходимо включить параметр блока [kt|b]mode_connected_album[/kt|b] и один из параметров
	[kt|b]var_connected_album_id[/kt|b] или [kt|b]var_connected_album_dir[/kt|b] в зависимости от того, каким образом
	идентификатор фотоальбома передается на страницу (т.е. это числовой ID или директория). Во всех случаях блок
	выведет либо 1 связанное видео, либо пустой список, если с выбранным фотоальбомов не связано ни одного видео.
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и
	та же версия кэша. Блок не кэшируется, когда отображает список загруженных видео, список купленных видео, список
	закладок или историю просмотра текущего пользователя. При выводе результатов поиска по строке поведение кэширования
	зависит от поискового запроса.
";

$lang['custom_list_videos']['block_examples'] = "
	[kt|b]Показать видео с сортировкой по дате публикации по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 самых популярных видео[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все видео, добавленные за сегодня[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- days_passed_from = 0[kt|br]
	- days_passed_to = 1[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все видео, добавленные за вчера[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- days_passed_from = 1[kt|br]
	- days_passed_to = 2[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать по 20 видео на странице с названием, которое начинается на букву 'a'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать по 20 видео на странице с длительностью более 3 минут и менее 5 минут[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	- var_duration_from = duration_from[kt|br]
	- var_duration_to = duration_to[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?duration_from=180&duration_to=300
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 видео с наибольшим рейтингом за последнюю неделю в категории с директорией 'my_category'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rating_week[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать видео, у которых есть тэг с директорией 'my_tag', по 20 на страницу и сортировкой по названию[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- var_tag_dir = tag[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?tag=my_tag
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать закладки видео пользователя с ID '287' по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_favourites[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать свои загруженные видео по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_uploaded[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 5 похожих видео для видео с ID '23'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- mode_related[kt|br]
	- var_video_id = video_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?video_id=23
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать связанное видео для фотоальбома с директорией 'my-album'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 1[kt|br]
	- mode_connected_album[kt|br]
	- var_connected_album_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my-album
	[/kt|code]
";

?>