<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_categories_groups messages
// =====================================================================================================================

$lang['list_categories_groups']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_categories_groups']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_categories_groups']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_categories_groups']['groups']['pull_categories']    = "Выборка категорий для каждой группы категорий";

$lang['list_categories_groups']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_categories_groups']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_categories_groups']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['list_categories_groups']['params']['var_items_per_page']             = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_categories_groups']['params']['sort_by']                        = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_categories_groups']['params']['var_sort_by']                    = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_categories_groups']['params']['show_only_with_avatar']          = "Используется для показа только тех групп, у которых задан аватар.";
$lang['list_categories_groups']['params']['show_only_without_avatar']       = "Используется для показа только тех групп, у которых не задан аватар.";
$lang['list_categories_groups']['params']['show_only_with_categories']      = "Используется для показа только тех групп, в которых есть категории.";
$lang['list_categories_groups']['params']['show_only_with_description']     = "Используется для показа только тех групп, у которых задано не пустое описание.";
$lang['list_categories_groups']['params']['pull_categories']                = "Включает возможность выборки списка категорий для каждой группы категорий. Количество и сортировка категорий настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_categories_groups']['params']['pull_categories_count']          = "Указывает кол-во категорий, которое выбирается для каждой группы (укажите [kt|b]0[/kt|b], чтобы вывести все категории из группы).";
$lang['list_categories_groups']['params']['pull_categories_sort_by']        = "Указывает сортировку для категорий, которые выбираются для каждой группы.";

$lang['list_categories_groups']['values']['pull_categories_sort_by']['sort_id']                 = "ID сортировки";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['title']                   = "Название";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['today_videos']            = "Кол-во видео сегодня";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['total_videos']            = "Кол-во видео всего";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['today_albums']            = "Кол-во фотоальбомов сегодня";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['total_albums']            = "Кол-во фотоальбомов всего";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_videos_rating']       = "Средний рейтинг видео";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_videos_popularity']   = "Средняя популярность видео";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['max_videos_ctr']          = "CTR видео";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_albums_rating']       = "Средний рейтинг фотоальбомов";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_albums_popularity']   = "Средняя популярность фотоальбомов";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['rand()']                  = "Случайно";
$lang['list_categories_groups']['values']['sort_by']['category_group_id']                       = "ID группы";
$lang['list_categories_groups']['values']['sort_by']['sort_id']                                 = "ID сортировки";
$lang['list_categories_groups']['values']['sort_by']['is_avatar_available']                     = "Наличие аватара";
$lang['list_categories_groups']['values']['sort_by']['title']                                   = "Название";
$lang['list_categories_groups']['values']['sort_by']['dir']                                     = "Директория";
$lang['list_categories_groups']['values']['sort_by']['description']                             = "Описание";
$lang['list_categories_groups']['values']['sort_by']['total_categories']                        = "Кол-во категорий";
$lang['list_categories_groups']['values']['sort_by']['total_videos']                            = "Кол-во видео";
$lang['list_categories_groups']['values']['sort_by']['total_albums']                            = "Кол-во фотоальбомов";
$lang['list_categories_groups']['values']['sort_by']['rand()']                                  = "Случайно";

$lang['list_categories_groups']['block_short_desc'] = "Выводит список групп категорий с заданными опциями";

$lang['list_categories_groups']['block_desc'] = "
	Блок предназначен для отображения списка групп категорий с различными опциями сортировки и фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_categories_groups']['block_examples'] = "
	[kt|b]Показать все группы по алфавиту[/kt|b]
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

	[kt|b]Показать только группы с аватаром, в которых есть категории[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_avatar[kt|br]
	- show_only_with_categories[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
