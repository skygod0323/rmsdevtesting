<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_posts messages
// =====================================================================================================================

$lang['list_posts']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_posts']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_posts']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_posts']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_posts']['groups']['display_modes']      = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_posts']['groups']['related']            = "Похожие записи";
$lang['list_posts']['groups']['connected_videos']   = "Связь записи-видео";
$lang['list_posts']['groups']['subselects']         = "Выборка дополнительных данных для каждой записи";

$lang['list_posts']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_posts']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_posts']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['list_posts']['params']['var_items_per_page']             = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_posts']['params']['sort_by']                        = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_posts']['params']['var_sort_by']                    = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_posts']['params']['post_type']                      = "Укажите внешний ID типа записей, чтобы вывести только записи этого типа.";
$lang['list_posts']['params']['show_only_with_description']     = "Позволяет выводить только записи, у которых задано не пустое описание.";
$lang['list_posts']['params']['skip_categories']                = "Запрещает выводить записи из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_posts']['params']['show_categories']                = "Позволяет выводить только записи из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_posts']['params']['skip_tags']                      = "Запрещает выводить записи с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_posts']['params']['show_tags']                      = "Позволяет выводить только записи с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_posts']['params']['skip_models']                    = "Запрещает выводить записи с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_posts']['params']['show_models']                    = "Позволяет выводить только записи с данными моделями (список ID моделей разделенных через запятую).";
$lang['list_posts']['params']['days_passed_from']               = "Позволяет фильтровать список записей по дате публикации, например, записи добавленные сегодня, вчера, за неделю и т.д. Указывает верхнюю границу даты публикации в кол-ве дней, прошедших с текущего дня.";
$lang['list_posts']['params']['days_passed_to']                 = "Позволяет фильтровать список записей по дате публикации, например, записи добавленные сегодня, вчера, за неделю и т.д. Указывает нижнюю границу даты публикации в кол-ве дней, прошедших с текущего дня. Значение должно быть больше, чем значение в параметре блока [kt|b]days_passed_from[/kt|b].";
$lang['list_posts']['params']['var_post_type']                  = "Параметр URL-а, в котором передается внешний ID типа записей. Позволяет выводить только записи с заданным типом.";
$lang['list_posts']['params']['var_title_section']              = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_posts']['params']['var_category_dir']               = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только записи из категории с заданной директорией.";
$lang['list_posts']['params']['var_category_id']                = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только записи из категории с заданным ID.";
$lang['list_posts']['params']['var_category_ids']               = "Параметр URL-а, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только записи из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те записи, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_posts']['params']['var_category_group_dir']         = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только записи из группы категорий с заданной директорией.";
$lang['list_posts']['params']['var_category_group_id']          = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только записи из группы категорий с заданным ID.";
$lang['list_posts']['params']['var_category_group_ids']         = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только записи из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те записи, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_posts']['params']['var_tag_dir']                    = "Параметр URL-а, в котором передается директория тэга. Позволяет выводить только записи, у которых есть тэг с заданной директорией.";
$lang['list_posts']['params']['var_tag_id']                     = "Параметр URL-а, в котором передается ID тэга. Позволяет выводить только записи, у которых есть тэг с заданным ID.";
$lang['list_posts']['params']['var_tag_ids']                    = "Параметр URL-а, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только записи, у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те записи, которые одновременно принадлежат всем перечисленным тэгам.";
$lang['list_posts']['params']['var_model_dir']                  = "Параметр URL-а, в котором передается директория модели. Позволяет выводить только записи, у которых есть модель с заданной директорией.";
$lang['list_posts']['params']['var_model_id']                   = "Параметр URL-а, в котором передается ID модели. Позволяет выводить только записи, у которых есть модель с заданным ID.";
$lang['list_posts']['params']['var_model_ids']                  = "Параметр URL-а, в котором передается список ID моделей, разделенных через запятую. Позволяет выводить только записи из моделей с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те записи, которые одновременно принадлежат всем перечисленным моделям.";
$lang['list_posts']['params']['var_model_group_dir']            = "Параметр URL-а, в котором передается директория группы моделей. Позволяет выводить только записи из группы моделей с заданной директорией.";
$lang['list_posts']['params']['var_model_group_id']             = "Параметр URL-а, в котором передается ID группы моделей. Позволяет выводить только записи из группы моделей с заданным ID.";
$lang['list_posts']['params']['var_model_group_ids']            = "Параметр URL-а, в котором передается список ID групп моделей, разделенных через запятую. Позволяет выводить только записи из групп моделей с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те записи, которые одновременно принадлежат всем перечисленным группам моделей.";
$lang['list_posts']['params']['var_post_date_from']             = "Параметр URL-а, в котором передается дата начала интервала публикации (YYYY-MM-DD). Позволяет выводить только записи, опубликованные в данном интервале.";
$lang['list_posts']['params']['var_post_date_to']               = "Параметр URL-а, в котором передается дата конца интервала публикации (YYYY-MM-DD). Позволяет выводить только записи, опубликованные в данном интервале.";
$lang['list_posts']['params']['var_custom1']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 1.";
$lang['list_posts']['params']['var_custom2']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 2.";
$lang['list_posts']['params']['var_custom3']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 3.";
$lang['list_posts']['params']['var_custom4']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 4.";
$lang['list_posts']['params']['var_custom5']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 5.";
$lang['list_posts']['params']['var_custom6']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 6.";
$lang['list_posts']['params']['var_custom7']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 7.";
$lang['list_posts']['params']['var_custom8']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 8.";
$lang['list_posts']['params']['var_custom9']                    = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 9.";
$lang['list_posts']['params']['var_custom10']                   = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. полю 10.";
$lang['list_posts']['params']['var_custom_flag1']               = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. флагу 1.";
$lang['list_posts']['params']['var_custom_flag2']               = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. флагу 2.";
$lang['list_posts']['params']['var_custom_flag3']               = "Параметр URL-а, в котором передается значение для динамического фильтра по доп. флагу 3.";
$lang['list_posts']['params']['mode_created']                   = "Включает режим отображения записей, созданных пользователем. Вы можете также включить параметр [kt|b]var_user_id[/kt|b], чтобы указать пользователя, для которого выводить список; в противном случае список созданных записей будет выводиться для текущего залогиненного пользователя.";
$lang['list_posts']['params']['var_user_id']                    = "Параметр URL-а, в котором передается ID пользователя для выбранного режима отображения.";
$lang['list_posts']['params']['redirect_unknown_user_to']       = "Указывает URL, на который будет перенаправлен незалогиненный пользователь при попытке доступа к режиму отображения, доступному только для залогиненных пользователей.";
$lang['list_posts']['params']['allow_delete_created_posts']     = "Разрешает пользователям удалять свои созданные записи в режиме отображения [kt|b]mode_created[/kt|b].";
$lang['list_posts']['params']['mode_related']                   = "Включает режим отображения похожих записей.";
$lang['list_posts']['params']['var_post_dir']                   = "Параметр URL-а, в котором передается директория записи для отображения похожих на нее записей.";
$lang['list_posts']['params']['var_post_id']                    = "Параметр URL-а, в котором передается ID записи для отображения похожих на нее записей.";
$lang['list_posts']['params']['mode_related_category_group_id'] = "Используется при режиме похожих записей по категориям. Укажите ID или внешний ID группы категорий, чтобы ограничить похожие записи только этой группой категорий.";
$lang['list_posts']['params']['mode_related_model_group_id']    = "Используется при режиме похожих записей по моделям. Укажите ID или внешний ID группы моделей, чтобы ограничить похожие записи только этой группой моделей.";
$lang['list_posts']['params']['var_mode_related']               = "Позволяет динамически переключать режим отображения похожих записей, передавая одно из значений в параметре URL-а: [kt|b]1[/kt|b] - по тэгам, [kt|b]2[/kt|b] - по категориям, [kt|b]3[/kt|b] - по моделям, [kt|b]4[/kt|b] и [kt|b]5[/kt|b] - по названию.";
$lang['list_posts']['params']['mode_connected_video']           = "Включает режим отображения записей связанных с выбранным видео.";
$lang['list_posts']['params']['var_connected_video_dir']        = "Параметр URL-а, в котором передается директория видео для отображения связанных с ним записей.";
$lang['list_posts']['params']['var_connected_video_id']         = "Параметр URL-а, в котором передается ID видео для отображения связанных с ним записей.";
$lang['list_posts']['params']['show_categories_info']           = "Включает выборку данных о категориях для каждой записи. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_posts']['params']['show_tags_info']                 = "Включает выборку данных о тэгах для каждой записи. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_posts']['params']['show_models_info']               = "Включает выборку данных о моделях для каждой записи. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_posts']['params']['show_user_info']                 = "Включает выборку данных о пользователе для каждой записи. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_posts']['params']['show_connected_info']            = "Включает выборку данных о связанном видео для каждой записи. Включение этой опции заметно ухудшает производительность блока.";

$lang['list_posts']['values']['mode_related']['1']                              = "Похожие по тэгам";
$lang['list_posts']['values']['mode_related']['2']                              = "Похожие по категориям";
$lang['list_posts']['values']['mode_related']['3']                              = "Похожие по моделям";
$lang['list_posts']['values']['mode_related']['4']                              = "Похожие по названию (натуральный режим)";
$lang['list_posts']['values']['mode_related']['5']                              = "Похожие по названию (с расширенным подзапросом)";
$lang['list_posts']['values']['sort_by']['post_id']                             = "ID записи";
$lang['list_posts']['values']['sort_by']['title']                               = "Название";
$lang['list_posts']['values']['sort_by']['dir']                                 = "Директория";
$lang['list_posts']['values']['sort_by']['post_date']                           = "Дата публикации";
$lang['list_posts']['values']['sort_by']['post_date_and_popularity']            = "Дата публикации (по популярности)";
$lang['list_posts']['values']['sort_by']['post_date_and_rating']                = "Дата публикации (по рейтингу)";
$lang['list_posts']['values']['sort_by']['last_time_view_date']                 = "Последний просмотр";
$lang['list_posts']['values']['sort_by']['last_time_view_date_and_popularity']  = "Последний просмотр (по популярности)";
$lang['list_posts']['values']['sort_by']['last_time_view_date_and_rating']      = "Последний просмотр (по рейтингу)";
$lang['list_posts']['values']['sort_by']['rating']                              = "Рейтинг";
$lang['list_posts']['values']['sort_by']['post_viewed']                         = "Популярность";
$lang['list_posts']['values']['sort_by']['most_commented']                      = "Кол-во комментариев";
$lang['list_posts']['values']['sort_by']['rand()']                              = "Случайно (очень медленно)";

$lang['list_posts']['block_short_desc'] = "Выводит список записей с заданными опциями";

$lang['list_posts']['block_desc'] = "
	Блок предназначен для отображения списка записей с различными опциями сортировки и фильтрации. Является стандартным
	блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]Режимы отображения[/kt|b]
	[kt|br][kt|br]

	1) [kt|b]По умолчанию[/kt|b]. Этот режим позволяет вывести просто список записей. Он работает по умолчанию.
	[kt|br][kt|br]

	2) [kt|b]Созданные записи[/kt|b]. Этот режим позволяет вывести записи, которые были созданы пользователем. Если
	отображаются созданные записи текущего залогиненного пользователя, данный режим также позволяет удалить любые
	созданные записи, если эта операция разрешена параметром [kt|b]allow_delete_created_posts[/kt|b].
	[kt|br][kt|br]

	3) [kt|b]Похожие записи[/kt|b]. Вы можете настроить, чтобы блок выводил список записей, похожих на заданную по
	большому набору критериев похожести. Для включения этого режима активируйте параметр блока [kt|b]mode_related[/kt|b]
	и дополнительно один из параметров [kt|b]var_post_dir[/kt|b] или [kt|b]var_post_id[/kt|b]. Для корректной работы
	этого режима в URL-е страницы должны передаваться либо директория, либо ID записи, для которой блок будет искать
	похожие:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=post-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	4) [kt|b]Записи, связанные с заданным видео[/kt|b]. Для включения этого режима активируйте параметр блока
	[kt|b]mode_connected_video[/kt|b] и дополнительно один из параметров
	[kt|b]var_connected_video_dir[/kt|b] или [kt|b]var_connected_video_id[/kt|b]. Для корректной работы этого режима в
	URL-е страницы должны передаваться либо директория, либо ID видео, для которого блок будет выводить связанные:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=video-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_list_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_mode_specific']}
";

$lang['list_posts']['block_examples'] = "
	[kt|b]Показать 20 новостей на странице с сортировкой по дате публикации[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 самых популярных новостей[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = post_viewed[kt|br]
	- post_type = news[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все новости за сегодня[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	- days_passed_from = 0[kt|br]
	- days_passed_to = 1[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все вчерашние новости[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- post_type = news[kt|br]
	- days_passed_from = 1[kt|br]
	- days_passed_to = 2[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 топовых новостей в категории с директорией 'my_category'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rating[kt|br]
	- post_type = news[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать новости, у которых есть тэг с директорией 'my_tag', по 20 на страницу с сортировкой по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- post_type = news[kt|br]
	- var_tag_dir = tag[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?tag=my_tag
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 5 похожих новостей к записи с ID '23'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_related[kt|br]
	- var_post_id = post_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?post_id=23
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все новости, привязанные к видео с директорией 'my-video'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_connected_video[kt|br]
	- var_connected_video_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my-video
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все новости, созданные мной (залогиненным пользователем)[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_created[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	- allow_delete_created_posts[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все новости, созданные пользователем с ID 12[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- sort_by = post_date[kt|br]
	- post_type = news[kt|br]
	- mode_created[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=12
	[/kt|code]
";
