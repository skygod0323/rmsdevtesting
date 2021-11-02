<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content_sources_groups messages
// =====================================================================================================================

$lang['list_content_sources_groups']['groups']['pagination']            = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content_sources_groups']['groups']['sorting']               = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content_sources_groups']['groups']['static_filters']        = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content_sources_groups']['groups']['pull_content_sources']  = "Выборка контент провайдеров для каждой группы контент провайдеров";

$lang['list_content_sources_groups']['params']['items_per_page']                = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content_sources_groups']['params']['links_per_page']                = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content_sources_groups']['params']['var_from']                      = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content_sources_groups']['params']['var_items_per_page']            = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content_sources_groups']['params']['sort_by']                       = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content_sources_groups']['params']['var_sort_by']                   = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content_sources_groups']['params']['show_only_with_cs']             = "Используется для показа только тех групп, в которых есть контент провайдеры.";
$lang['list_content_sources_groups']['params']['show_only_with_description']    = "Используется для показа только тех групп, у которых задано не пустое описание.";
$lang['list_content_sources_groups']['params']['pull_content_sources']          = "Включает возможность выборки списка контент провайдеров для каждой группы. Количество и сортировка списка контент провайдеров настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_content_sources_groups']['params']['pull_content_sources_count']    = "Указывает кол-во контент провайдеров, которое выбирается для каждой группы.";
$lang['list_content_sources_groups']['params']['pull_content_sources_sort_by']  = "Указывает сортировку контент провайдеров, которые выбираются для каждой группы.";

$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['sort_id']                   = "ID сортировки";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['title']                     = "Название";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['today_videos']              = "Кол-во видео сегодня";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['total_videos']              = "Кол-во видео всего";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['today_albums']              = "Кол-во фотоальбомов сегодня";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['total_albums']              = "Кол-во фотоальбомов всего";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_videos_rating']         = "Средний рейтинг видео";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_videos_popularity']     = "Средняя популярность видео";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_albums_rating']         = "Средний рейтинг фотоальбомов";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_albums_popularity']     = "Средняя популярность фотоальбомов";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['rand()']                    = "Случайно";
$lang['list_content_sources_groups']['values']['sort_by']['content_source_group_id']                        = "ID группы контент провайдеров";
$lang['list_content_sources_groups']['values']['sort_by']['sort_id']                                        = "ID сортировки";
$lang['list_content_sources_groups']['values']['sort_by']['title']                                          = "Название";
$lang['list_content_sources_groups']['values']['sort_by']['dir']                                            = "Директория";
$lang['list_content_sources_groups']['values']['sort_by']['total_content_sources']                          = "Кол-во контент провайдеров";
$lang['list_content_sources_groups']['values']['sort_by']['total_videos']                                   = "Кол-во видео";
$lang['list_content_sources_groups']['values']['sort_by']['total_albums']                                   = "Кол-во фотоальбомов";
$lang['list_content_sources_groups']['values']['sort_by']['rand()']                                         = "Случайно";

$lang['list_content_sources_groups']['block_short_desc'] = "Выводит список групп контент провайдеров с заданными опциями";

$lang['list_content_sources_groups']['block_desc'] = "
	Блок предназначен для отображения списка групп контент провайдеров с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_content_sources_groups']['block_examples'] = "
	[kt|b]Показать все группы контент провайдеров по алфавиту[/kt|b]
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

	[kt|b]Показать только группы контент провайдеров с контент провайдерами, по 10 на страницу и сортировкой по кол-ву контент провайдеров в них[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = content_sources desc[kt|br]
	- show_only_with_cs[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
