<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content_sources messages
// =====================================================================================================================

$lang['list_content_sources']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content_sources']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content_sources']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content_sources']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_content_sources']['groups']['subselects']       = "Выборка дополнительных данных для каждого контент провайдера";
$lang['list_content_sources']['groups']['pull_videos']      = "Выборка видео для каждого контент провайдера";
$lang['list_content_sources']['groups']['pull_albums']      = "Выборка альбомов для каждого контент провайдера";

$lang['list_content_sources']['params']['items_per_page']                   = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content_sources']['params']['links_per_page']                   = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content_sources']['params']['var_from']                         = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content_sources']['params']['var_items_per_page']               = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content_sources']['params']['sort_by']                          = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content_sources']['params']['var_sort_by']                      = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content_sources']['params']['show_only_with_screenshot1']       = "Используется для показа только тех провайдеров, у которых задан скриншот #1.";
$lang['list_content_sources']['params']['show_only_with_screenshot2']       = "Используется для показа только тех провайдеров, у которых задан скриншот #2.";
$lang['list_content_sources']['params']['show_only_with_videos']            = "Используется для показа только тех провайдеров, у которых есть заданное кол-во видео.";
$lang['list_content_sources']['params']['show_only_with_albums']            = "Используется для показа только тех провайдеров, у которых есть заданное кол-во фотоальбомов.";
$lang['list_content_sources']['params']['show_only_with_albums_or_videos']  = "Используется для показа только тех провайдеров, у которых есть заданное кол-во видео или фотоальбомов.";
$lang['list_content_sources']['params']['show_only_with_description']       = "Используется для показа только тех провайдеров, у которых задано не пустое описание.";
$lang['list_content_sources']['params']['skip_categories']                  = "Позволяет не выводить провайдеров из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_content_sources']['params']['show_categories']                  = "Позволяет выводить только провайдеров из данных категорий (список ID категорий разделенных через запятую).";
$lang['list_content_sources']['params']['skip_tags']                        = "Позволяет не выводить провайдеров с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_content_sources']['params']['show_tags']                        = "Позволяет выводить только провайдеров с данными тэгами (список ID тэгов разделенных через запятую).";
$lang['list_content_sources']['params']['skip_content_source_groups']       = "Позволяет не выводить провайдеров из данных групп провайдеров (список ID групп провайдеров разделенных через запятую).";
$lang['list_content_sources']['params']['show_content_source_groups']       = "Позволяет выводить только провайдеров из данных групп провайдеров (список ID групп провайдеров разделенных через запятую).";
$lang['list_content_sources']['params']['content_source_group_ids']         = "[kt|b]Устарел![/kt|b] Используйте параметр [kt|b]show_content_source_groups[/kt|b].";
$lang['list_content_sources']['params']['var_title_section']                = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_content_sources']['params']['var_category_dir']                 = "Параметр URL-а, в котором передается директория категории. Позволяет выводить только провайдеров из категории с заданной директорией.";
$lang['list_content_sources']['params']['var_category_id']                  = "Параметр URL-а, в котором передается ID категории. Позволяет выводить только провайдеров из категории с заданным ID.";
$lang['list_content_sources']['params']['var_category_ids']                 = "Параметр URL-а, в котором передается список ID категорий, разделенных через запятую. Позволяет выводить только провайдеров из категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те провайдеры, которые одновременно принадлежат всем перечисленным категориям.";
$lang['list_content_sources']['params']['var_category_group_dir']           = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только провайдеров из группы категорий с заданной директорией.";
$lang['list_content_sources']['params']['var_category_group_id']            = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только провайдеров из группы категорий с заданным ID.";
$lang['list_content_sources']['params']['var_category_group_ids']           = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только провайдеров из групп категорий с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те провайдеры, которые одновременно принадлежат всем перечисленным группам категорий.";
$lang['list_content_sources']['params']['var_tag_dir']                      = "Параметр URL-а, в котором передается директория тэга. Позволяет выводить только провайдеров, у которых есть тэг с заданной директорией.";
$lang['list_content_sources']['params']['var_tag_id']                       = "Параметр URL-а, в котором передается ID тэга. Позволяет выводить только провайдеров, у которых есть тэг с заданным ID.";
$lang['list_content_sources']['params']['var_tag_ids']                      = "Параметр URL-а, в котором передается список ID тэгов, разделенных через запятую. Позволяет выводить только провайдеров, у которых есть тэги с заданными ID. Если одним из элементов списка является ключевое слово [kt|b]all[/kt|b], то блок выведет только те провайдеры, у которых одновременно есть все перечисленные тэги.";
$lang['list_content_sources']['params']['var_content_source_group_dir']     = "Параметр URL-а, в котором передается директория группы контент провайдеров. Позволяет выводить только провайдеров из группы с заданной директорией.";
$lang['list_content_sources']['params']['var_content_source_group_id']      = "Параметр URL-а, в котором передается ID группы контент провайдеров. Позволяет выводить только провайдеров из группы с заданным ID.";
$lang['list_content_sources']['params']['var_content_source_group_ids']     = "Параметр URL-а, в котором передается список ID групп контент провайдеров, разделенных через запятую. Позволяет выводить только провайдеров из групп с заданными ID.";
$lang['list_content_sources']['params']['show_categories_info']             = "Включает выборку данных о категориях для каждого контент провайдера. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources']['params']['show_tags_info']                   = "Включает выборку данных о тэгах для каждого контент провайдера. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources']['params']['show_group_info']                  = "Включает выборку данных о группе для каждого контент провайдера. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources']['params']['pull_videos']                      = "Включает возможность выборки списка видео для каждого контент провайдера. Количество и сортировка видео настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources']['params']['pull_videos_count']                = "Указывает кол-во видео, которое выбирается для каждого контент провайдера.";
$lang['list_content_sources']['params']['pull_videos_sort_by']              = "Указывает сортировку для видео, которые выбираются для каждого контент провайдера.";
$lang['list_content_sources']['params']['pull_albums']                      = "Включает возможность выборки списка фотоальбомов для каждого контент провайдера. Количество и сортировка фотоальбомов настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources']['params']['pull_albums_count']                = "Указывает кол-во фотоальбомов, которое выбирается для каждого контент провайдера.";
$lang['list_content_sources']['params']['pull_albums_sort_by']              = "Указывает сортировку для фотоальбомов, которые выбираются для каждого контент провайдера.";

$lang['list_content_sources']['values']['pull_videos_sort_by']['duration']              = "Длительность";
$lang['list_content_sources']['values']['pull_videos_sort_by']['post_date']             = "Дата публикации";
$lang['list_content_sources']['values']['pull_videos_sort_by']['last_time_view_date']   = "Последний просмотр";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating']                = "Суммарный рейтинг";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_today']          = "Рейтинг за сегодня";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_week']           = "Рейтинг за неделю";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_month']          = "Рейтинг за месяц";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed']          = "Суммарная популярность";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_today']    = "Популярность за сегодня";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_week']     = "Популярность за неделю";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_month']    = "Популярность за месяц";
$lang['list_content_sources']['values']['pull_videos_sort_by']['most_favourited']       = "Добавление в закладки";
$lang['list_content_sources']['values']['pull_videos_sort_by']['most_commented']        = "Кол-во комментариев";
$lang['list_content_sources']['values']['pull_videos_sort_by']['ctr']                   = "CTR (ротатор)";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rand()']                = "Случайно";
$lang['list_content_sources']['values']['pull_albums_sort_by']['photos_amount']         = "Кол-во фотографий";
$lang['list_content_sources']['values']['pull_albums_sort_by']['post_date']             = "Дата публикации";
$lang['list_content_sources']['values']['pull_albums_sort_by']['last_time_view_date']   = "Последний просмотр";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating']                = "Суммарный рейтинг";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_today']          = "Рейтинг за сегодня";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_week']           = "Рейтинг за неделю";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_month']          = "Рейтинг за месяц";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed']          = "Суммарная популярность";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_today']    = "Популярность за сегодня";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_week']     = "Популярность за неделю";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_month']    = "Популярность за месяц";
$lang['list_content_sources']['values']['pull_albums_sort_by']['most_favourited']       = "Добавление в закладки";
$lang['list_content_sources']['values']['pull_albums_sort_by']['most_commented']        = "Кол-во комментариев";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rand()']                = "Случайно";
$lang['list_content_sources']['values']['sort_by']['content_source_id']                 = "ID контент провайдера";
$lang['list_content_sources']['values']['sort_by']['sort_id']                           = "ID сортировки";
$lang['list_content_sources']['values']['sort_by']['title']                             = "Название";
$lang['list_content_sources']['values']['sort_by']['rating']                            = "Рейтинг";
$lang['list_content_sources']['values']['sort_by']['cs_viewed']                         = "Популярность";
$lang['list_content_sources']['values']['sort_by']['screenshot1']                       = "Скриншот 1";
$lang['list_content_sources']['values']['sort_by']['screenshot2']                       = "Скриншот 2";
$lang['list_content_sources']['values']['sort_by']['today_videos']                      = "Кол-во видео сегодня";
$lang['list_content_sources']['values']['sort_by']['total_videos']                      = "Кол-во видео";
$lang['list_content_sources']['values']['sort_by']['today_albums']                      = "Кол-во фотоальбомов сегодня";
$lang['list_content_sources']['values']['sort_by']['total_albums']                      = "Кол-во фотоальбомов всего";
$lang['list_content_sources']['values']['sort_by']['avg_videos_rating']                 = "Средний рейтинг видео";
$lang['list_content_sources']['values']['sort_by']['avg_videos_popularity']             = "Средняя популярность видео";
$lang['list_content_sources']['values']['sort_by']['avg_albums_rating']                 = "Средний рейтинг фотоальбомов";
$lang['list_content_sources']['values']['sort_by']['avg_albums_popularity']             = "Средняя популярность фотоальбомов";
$lang['list_content_sources']['values']['sort_by']['comments_count']                    = "Кол-во комментариев";
$lang['list_content_sources']['values']['sort_by']['subscribers_count']                 = "Кол-во подписок";
$lang['list_content_sources']['values']['sort_by']['rank']                              = "Ранг";
$lang['list_content_sources']['values']['sort_by']['last_content_date']                 = "Дата последнего добавления контента";
$lang['list_content_sources']['values']['sort_by']['added_date']                        = "Дата создания";
$lang['list_content_sources']['values']['sort_by']['rand()']                            = "Случайно";

$lang['list_content_sources']['block_short_desc'] = "Выводит список контент провайдеров с заданными опциями";

$lang['list_content_sources']['block_desc'] = "
	Блок предназначен для отображения списка контент провайдеров с различными опциями сортировки и фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_content_sources']['block_examples'] = "
	[kt|b]Показать всех контент провайдеров по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать только контент провайдеров с видео, по 10 на страницу и сортировкой по кол-ву видео для них[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = total_videos desc[kt|br]
	- show_only_with_videos[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать всех контент провайдеров с названием, которое начинается на букву 'a'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 15 контент провайдеров в категории с директорией 'my_category' и сортировкой по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = title asc[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать только контент провайдеров из групп с ID '15' и '20', по 10 на страницу в случайном порядке[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = rand()[kt|br]
	- show_content_source_groups = 15,20[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать всех контент провайдеров из группы контент провайдеров с директорией 'my_content_source_group' и сортировкой по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_content_source_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my_content_source_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать по 10 контент провайдеров на страницу с 5-ю топовыми видео для каждого контент провайдера по рейтингу[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- pull_videos[kt|br]
	- pull_videos_count = 5[kt|br]
	- pull_videos_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать всех контент провайдеров с 10-ю топовыми фотоальбомами в каждом контент провайдере по популярности[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- pull_albums[kt|br]
	- pull_albums_count = 10[kt|br]
	- pull_albums_sort_by = album_viewed desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
