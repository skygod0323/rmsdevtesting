<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_models_groups messages
// =====================================================================================================================

$lang['list_models_groups']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['list_models_groups']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['list_models_groups']['groups']['static_filters'] = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_models_groups']['groups']['pull_models']    = "Выборка моделей для каждой группы моделей";

$lang['list_models_groups']['params']['items_per_page']             = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_models_groups']['params']['links_per_page']             = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_models_groups']['params']['var_from']                   = $lang['website_ui']['parameter_default_var_from'];
$lang['list_models_groups']['params']['var_items_per_page']         = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_models_groups']['params']['sort_by']                    = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_models_groups']['params']['var_sort_by']                = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_models_groups']['params']['show_only_with_screenshot1'] = "Используется для показа только тех групп, у которых задан скриншот #1.";
$lang['list_models_groups']['params']['show_only_with_screenshot2'] = "Используется для показа только тех групп, у которых задан скриншот #2.";
$lang['list_models_groups']['params']['show_only_with_models']      = "Используется для показа только тех групп, в которых есть модели.";
$lang['list_models_groups']['params']['show_only_with_description'] = "Используется для показа только тех групп, у которых задано не пустое описание.";
$lang['list_models_groups']['params']['pull_models']                = "Включает возможность выборки списка моделей для каждой группы моделей. Количество и сортировка моделей настраивается отдельными параметрами блока. Включение этой опции заметно ухудшает производительность блока.";
$lang['list_models_groups']['params']['pull_models_count']          = "Указывает кол-во моделей, которое выбирается для каждой группы (укажите [kt|b]0[/kt|b], чтобы вывести все модели из группы).";
$lang['list_models_groups']['params']['pull_models_sort_by']        = "Указывает сортировку для моделей, которые выбираются для каждой группы.";

$lang['list_models_groups']['values']['pull_models_sort_by']['sort_id']                 = "ID сортировки";
$lang['list_models_groups']['values']['pull_models_sort_by']['title']                   = "Название";
$lang['list_models_groups']['values']['pull_models_sort_by']['today_videos']            = "Кол-во видео сегодня";
$lang['list_models_groups']['values']['pull_models_sort_by']['total_videos']            = "Кол-во видео всего";
$lang['list_models_groups']['values']['pull_models_sort_by']['today_albums']            = "Кол-во фотоальбомов сегодня";
$lang['list_models_groups']['values']['pull_models_sort_by']['total_albums']            = "Кол-во фотоальбомов всего";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_videos_rating']       = "Средний рейтинг видео";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_videos_popularity']   = "Средняя популярность видео";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_albums_rating']       = "Средний рейтинг фотоальбомов";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_albums_popularity']   = "Средняя популярность фотоальбомов";
$lang['list_models_groups']['values']['pull_models_sort_by']['rand()']                  = "Случайно";
$lang['list_models_groups']['values']['sort_by']['model_group_id']                      = "ID группы";
$lang['list_models_groups']['values']['sort_by']['sort_id']                             = "ID сортировки";
$lang['list_models_groups']['values']['sort_by']['title']                               = "Название";
$lang['list_models_groups']['values']['sort_by']['dir']                                 = "Директория";
$lang['list_models_groups']['values']['sort_by']['description']                         = "Описание";
$lang['list_models_groups']['values']['sort_by']['total_models']                        = "Кол-во моделей";
$lang['list_models_groups']['values']['sort_by']['total_videos']                        = "Кол-во видео";
$lang['list_models_groups']['values']['sort_by']['total_albums']                        = "Кол-во фотоальбомов";
$lang['list_models_groups']['values']['sort_by']['rand()']                              = "Случайно";

$lang['list_models_groups']['block_short_desc'] = "Выводит список групп моделей с заданными опциями";

$lang['list_models_groups']['block_desc'] = "
	Блок предназначен для отображения списка групп моделей с различными опциями сортировки и фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_models_groups']['block_examples'] = "
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

	[kt|b]Показать только группы со скриншотом #1, в которых есть модели[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_screenshot1[kt|br]
	- show_only_with_models[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
