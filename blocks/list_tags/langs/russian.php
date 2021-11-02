<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_tags messages
// =====================================================================================================================

$lang['list_tags']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_tags']['groups']['sorting']         = $lang['website_ui']['block_group_default_sorting'];
$lang['list_tags']['groups']['static_filters']  = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_tags']['groups']['dynamic_filters'] = $lang['website_ui']['block_group_default_dynamic_filters'];

$lang['list_tags']['params']['items_per_page']                  = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_tags']['params']['links_per_page']                  = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_tags']['params']['var_from']                        = $lang['website_ui']['parameter_default_var_from'];
$lang['list_tags']['params']['var_items_per_page']              = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_tags']['params']['sort_by']                         = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_tags']['params']['var_sort_by']                     = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_tags']['params']['show_only_with_videos']           = "Используется для показа только тех тэгов, которые используются в заданном кол-ве видео.";
$lang['list_tags']['params']['show_only_with_albums']           = "Используется для показа только тех тэгов, которые используются в заданном кол-ве фотоальбомов.";
$lang['list_tags']['params']['show_only_with_posts']            = "Используется для показа только тех тэгов, которые используются в заданном кол-ве записей.";
$lang['list_tags']['params']['show_only_with_albums_or_videos'] = "Используется для показа только тех тэгов, которые используются в заданном кол-ве видео или фотоальбомов.";
$lang['list_tags']['params']['show_only_with_playlists']        = "Используется для показа только тех тэгов, которые используются в заданном кол-ве плэйлистов.";
$lang['list_tags']['params']['show_only_with_dvds']             = "Используется для показа только тех тэгов, которые используются в заданном кол-ве DVD / каналов.";
$lang['list_tags']['params']['show_only_with_cs']               = "Используется для показа только тех тэгов, которые используются в заданном кол-ве контент провайдеров.";
$lang['list_tags']['params']['show_only_with_models']           = "Используется для показа только тех тэгов, которые используются в заданном кол-ве моделей.";
$lang['list_tags']['params']['var_title_section']               = "Параметр URL-а, в котором передаются первые буквы названия для фильтрации списка.";

$lang['list_tags']['values']['sort_by']['tag_id']                   = "ID тэга";
$lang['list_tags']['values']['sort_by']['tag']                      = "Название";
$lang['list_tags']['values']['sort_by']['tag_dir']                  = "Директория";
$lang['list_tags']['values']['sort_by']['today_videos']             = "Кол-во видео сегодня";
$lang['list_tags']['values']['sort_by']['total_videos']             = "Кол-во видео всего";
$lang['list_tags']['values']['sort_by']['today_albums']             = "Кол-во фотоальбомов сегодня";
$lang['list_tags']['values']['sort_by']['total_albums']             = "Кол-во фотоальбомов всего";
$lang['list_tags']['values']['sort_by']['today_posts']              = "Кол-во записей сегодня";
$lang['list_tags']['values']['sort_by']['total_posts']              = "Кол-во записей всего";
$lang['list_tags']['values']['sort_by']['total_playlists']          = "Кол-во плэйлистов";
$lang['list_tags']['values']['sort_by']['total_dvds']               = "Кол-во DVD / каналов";
$lang['list_tags']['values']['sort_by']['total_cs']                 = "Кол-во контент провайдеров";
$lang['list_tags']['values']['sort_by']['total_models']             = "Кол-во моделей";
$lang['list_tags']['values']['sort_by']['avg_videos_rating']        = "Средний рейтинг видео";
$lang['list_tags']['values']['sort_by']['avg_videos_popularity']    = "Средняя популярность видео";
$lang['list_tags']['values']['sort_by']['avg_albums_rating']        = "Средний рейтинг фотоальбомов";
$lang['list_tags']['values']['sort_by']['avg_albums_popularity']    = "Средняя популярность фотоальбомов";
$lang['list_tags']['values']['sort_by']['avg_posts_rating']         = "Средний рейтинг записей";
$lang['list_tags']['values']['sort_by']['avg_posts_popularity']     = "Средняя популярность записей";
$lang['list_tags']['values']['sort_by']['rand()']                   = "Случайно";

$lang['list_tags']['block_short_desc'] = "Выводит список тэгов с заданными опциями";

$lang['list_tags']['block_desc'] = "
	Блок предназначен для отображения списка тэгов с различными опциями сортировки и фильтрации. Является стандартным
	блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_tags']['block_examples'] = "
	[kt|b]Показать все тэги по алфавиту[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = tag asc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все тэги, которые начинаются на букву 'a'[/kt|b]
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

	[kt|b]Показать тэги, в которых есть видео, по 10 на страницу и сортировкой по кол-ву видео[/kt|b]
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
";
