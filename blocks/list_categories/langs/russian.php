<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_categories messages
// =====================================================================================================================

$lang['list_categories']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_categories']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_categories']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_categories']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_categories']['groups']['related']           = "Похожие категории";
$lang['list_categories']['groups']['pull_videos']       = "Выборка видео для каждой категории";
$lang['list_categories']['groups']['pull_albums']       = "Выборка альбомов для каждой категории";

$lang['list_categories']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_categories']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_categories']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_categories']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_categories']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_categories']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_categories']['params']['show_only_with_avatar']             = "Используется для показа только тех категорий, у которых задан аватар.";
$lang['list_categories']['params']['show_only_without_avatar']          = "Используется для показа только тех категорий, у которых не задан аватар.";
$lang['list_categories']['params']['show_only_with_description']        = "Используется для показа только тех категорий, у которых задано не пустое описание.";
$lang['list_categories']['params']['show_only_with_albums']             = "Используется для показа только тех категорий, в которых есть заданное кол-во фотоальбомов.";
$lang['list_categories']['params']['show_only_with_videos']             = "Используется для показа только тех категорий, в которых есть заданное кол-во видео.";
$lang['list_categories']['params']['show_only_with_posts']              = "Используется для показа только тех категорий, в которых есть заданное кол-во записей.";
$lang['list_categories']['params']['show_only_with_albums_or_videos']   = "Используется для показа только тех категорий, в которых есть заданное кол-во видео или фотоальбомов.";
$lang['list_categories']['params']['skip_category_groups']              = "Позволяет не выводить категории из данных групп категорий (список ID групп категорий разделенных через запятую).";
$lang['list_categories']['params']['show_category_groups']              = "Позволяет выводить только категории из данных групп категорий (список ID групп категорий разделенных через запятую).";
$lang['list_categories']['params']['category_group_ids']                = "[kt|b]Устарел![/kt|b] Используйте параметр [kt|b]show_category_groups[/kt|b].";
$lang['list_categories']['params']['var_title_section']                 = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";
$lang['list_categories']['params']['var_category_group_dir']            = "Параметр URL-а, в котором передается директория группы категорий. Позволяет выводить только категории из группы с заданной директорией.";
$lang['list_categories']['params']['var_category_group_id']             = "Параметр URL-а, в котором передается ID группы категорий. Позволяет выводить только категории из группы с заданным ID.";
$lang['list_categories']['params']['var_category_group_ids']            = "Параметр URL-а, в котором передается список ID групп категорий, разделенных через запятую. Позволяет выводить только категории из групп с заданными ID.";
$lang['list_categories']['params']['mode_related']                      = "Включает режим отображения похожих категорий.";
$lang['list_categories']['params']['var_category_dir']                  = "Параметр URL-а, в котором передается директория категории для отображения похожих на нее категорий.";
$lang['list_categories']['params']['var_category_id']                   = "Параметр URL-а, в котором передается ID категории для отображения похожих на нее категорий.";
$lang['list_categories']['params']['var_category_ids']                  = "Параметр URL-а, в котором передается список ID категорий, разделенных запятыми, для отображения похожих на них категорий. По умолчанию выберет категории, которые похожи для [kt|b]ЛЮБОЙ[/kt|b] категории из списка. Если в этот список также добавить ключевое слово [kt|b]all[/kt|b], то выберет категории, которые похожи для [kt|b]КАЖДОЙ[/kt|b] категории из списка.";
$lang['list_categories']['params']['var_mode_related']                  = "Позволяет динамически переключать режим отображения похожих категорий, передавая одно из значений в URL-е: [kt|b]1[/kt|b] - по группе, [kt|b]2[/kt|b] - по видео, [kt|b]3[/kt|b] - по альбомам.";
$lang['list_categories']['params']['pull_videos']                       = "Включает возможность выборки списка видео для каждой категории. Количество и сортировка видео настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_categories']['params']['pull_videos_count']                 = "Указывает кол-во видео, которое выбирается для каждой категории.";
$lang['list_categories']['params']['pull_videos_sort_by']               = "Указывает сортировку для видео, которые выбираются для каждой категории.";
$lang['list_categories']['params']['pull_videos_duplicates']            = "Включите, если хотите разрешить выбор повторяющихся видео для разных категорий.";
$lang['list_categories']['params']['pull_albums']                       = "Включает возможность выборки списка фотоальбомов для каждой категории. Количество и сортировка фотоальбомов настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_categories']['params']['pull_albums_count']                 = "Указывает кол-во фотоальбомов, которое выбирается для каждой категории.";
$lang['list_categories']['params']['pull_albums_sort_by']               = "Указывает сортировку для фотоальбомов, которые выбираются для каждой категории.";
$lang['list_categories']['params']['pull_albums_duplicates']            = "Включите, если хотите разрешить выбор повторяющихся альбомов для разных категорий.";

$lang['list_categories']['values']['mode_related']['1']                             = "Похожие по группе";
$lang['list_categories']['values']['mode_related']['2']                             = "Похожие по видео";
$lang['list_categories']['values']['mode_related']['3']                             = "Похожие по альбомам";
$lang['list_categories']['values']['pull_videos_sort_by']['duration']               = "Длительность";
$lang['list_categories']['values']['pull_videos_sort_by']['post_date']              = "Дата публикации";
$lang['list_categories']['values']['pull_videos_sort_by']['last_time_view_date']    = "Последний просмотр";
$lang['list_categories']['values']['pull_videos_sort_by']['rating']                 = "Суммарный рейтинг";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_today']           = "Рейтинг за сегодня";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_week']            = "Рейтинг за неделю";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_month']           = "Рейтинг за месяц";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed']           = "Суммарная популярность";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_today']     = "Популярность за сегодня";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_week']      = "Популярность за неделю";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_month']     = "Популярность за месяц";
$lang['list_categories']['values']['pull_videos_sort_by']['most_favourited']        = "Добавление в закладки";
$lang['list_categories']['values']['pull_videos_sort_by']['most_commented']         = "Кол-во комментариев";
$lang['list_categories']['values']['pull_videos_sort_by']['ctr']                    = "CTR (ротатор)";
$lang['list_categories']['values']['pull_videos_sort_by']['rand()']                 = "Случайно";
$lang['list_categories']['values']['pull_albums_sort_by']['photos_amount']          = "Кол-во фотографий";
$lang['list_categories']['values']['pull_albums_sort_by']['post_date']              = "Дата публикации";
$lang['list_categories']['values']['pull_albums_sort_by']['last_time_view_date']    = "Последний просмотр";
$lang['list_categories']['values']['pull_albums_sort_by']['rating']                 = "Суммарный рейтинг";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_today']           = "Рейтинг за сегодня";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_week']            = "Рейтинг за неделю";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_month']           = "Рейтинг за месяц";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed']           = "Суммарная популярность";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_today']     = "Популярность за сегодня";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_week']      = "Популярность за неделю";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_month']     = "Популярность за месяц";
$lang['list_categories']['values']['pull_albums_sort_by']['most_favourited']        = "Добавление в закладки";
$lang['list_categories']['values']['pull_albums_sort_by']['most_commented']         = "Кол-во комментариев";
$lang['list_categories']['values']['pull_albums_sort_by']['rand()']                 = "Случайно";
$lang['list_categories']['values']['sort_by']['category_id']                        = "ID категории";
$lang['list_categories']['values']['sort_by']['sort_id']                            = "ID сортировки";
$lang['list_categories']['values']['sort_by']['is_avatar_available']                = "Наличие аватара";
$lang['list_categories']['values']['sort_by']['title']                              = "Название";
$lang['list_categories']['values']['sort_by']['dir']                                = "Директория";
$lang['list_categories']['values']['sort_by']['description']                        = "Описание";
$lang['list_categories']['values']['sort_by']['today_videos']                       = "Кол-во видео сегодня";
$lang['list_categories']['values']['sort_by']['total_videos']                       = "Кол-во видео всего";
$lang['list_categories']['values']['sort_by']['today_albums']                       = "Кол-во фотоальбомов сегодня";
$lang['list_categories']['values']['sort_by']['total_albums']                       = "Кол-во фотоальбомов всего";
$lang['list_categories']['values']['sort_by']['today_posts']                        = "Кол-во записей сегодня";
$lang['list_categories']['values']['sort_by']['total_posts']                        = "Кол-во записей всего";
$lang['list_categories']['values']['sort_by']['avg_videos_rating']                  = "Средний рейтинг видео";
$lang['list_categories']['values']['sort_by']['avg_videos_popularity']              = "Средняя популярность видео";
$lang['list_categories']['values']['sort_by']['max_videos_ctr']                     = "CTR видео";
$lang['list_categories']['values']['sort_by']['avg_albums_rating']                  = "Средний рейтинг фотоальбомов";
$lang['list_categories']['values']['sort_by']['avg_albums_popularity']              = "Средняя популярность фотоальбомов";
$lang['list_categories']['values']['sort_by']['avg_posts_rating']                   = "Средний рейтинг записей";
$lang['list_categories']['values']['sort_by']['avg_posts_popularity']               = "Средняя популярность записей";
$lang['list_categories']['values']['sort_by']['rand()']                             = "Случайно";

$lang['list_categories']['block_short_desc'] = "Выводит список категорий с заданными опциями";

$lang['list_categories']['block_desc'] = "
	Блок предназначен для отображения списка категорий с различными опциями сортировки и фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]Похожие категории[/kt|b]
	[kt|br][kt|br]

	Вы можете настроить, чтобы блок выводил список категорий, похожих на заданную по большому набору критериев
	похожести. Для включения этого режима активируйте параметр блока [kt|b]mode_related[/kt|b] и дополнительно один из
	параметров [kt|b]var_category_dir[/kt|b] или [kt|b]var_category_id[/kt|b]. Для корректной работы этого режима в
	URL-е страницы должны передаваться либо директория, либо ID категории, для которой блок будет искать похожих:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=category-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_categories']['block_examples'] = "
	[kt|b]Показать все категории по алфавиту[/kt|b]
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

	[kt|b]Показать только категории с аватаром, в которых есть видео[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_avatar[kt|br]
	- show_only_with_videos[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все категории с названием, которое начинается на букву 'a'[/kt|b]
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

	[kt|b]Показать только категории из групп с ID '15' и '20', по 10 на страницу и сортировкой по кол-ву видео в них[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = total_videos desc[kt|br]
	- var_from = from[kt|br]
	- show_category_groups = 15,20[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все категории из группы категорий с директорией 'my_category_group' и сортировкой по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_category_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my_category_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все категории с 5-ю топовыми видео в каждой категории по рейтингу[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
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

	[kt|b]Показать все категории с 10-ю топовыми фотоальбомами в каждой категории по популярности[/kt|b]
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
