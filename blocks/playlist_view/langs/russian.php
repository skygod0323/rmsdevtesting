<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// playlist_view messages
// =====================================================================================================================

$lang['playlist_view']['groups']['object_context']      = $lang['website_ui']['block_group_default_context_object'];
$lang['playlist_view']['groups']['pagination']          = "Пагинация видео плэйлиста";
$lang['playlist_view']['groups']['sorting']             = "Сортировка видео плэйлиста";
$lang['playlist_view']['groups']['additional_data']     = $lang['website_ui']['block_group_default_additional_data'];

$lang['playlist_view']['params']['var_playlist_dir']            = "Параметр URL-а, в котором передается директория плэйлиста.";
$lang['playlist_view']['params']['var_playlist_id']             = "Параметр URL-а, в котором передается ID плэйлиста. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['playlist_view']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['playlist_view']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['playlist_view']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['playlist_view']['params']['sort_by']                     = $lang['website_ui']['parameter_default_sort_by'];
$lang['playlist_view']['params']['var_sort_by']                 = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['playlist_view']['params']['show_next_and_previous_info'] = "Включает выборку информации о следующем и предыдущем плэйлистах исходя из выбранного режима.";

$lang['playlist_view']['values']['sort_by']['rating']           = "Рейтинг";
$lang['playlist_view']['values']['sort_by']['video_viewed']     = "Популярность";
$lang['playlist_view']['values']['sort_by']['most_favourited']  = "Добавление в закладки";
$lang['playlist_view']['values']['sort_by']['most_commented']   = "Кол-во комментариев";
$lang['playlist_view']['values']['sort_by']['added2fav_date']   = "Дата добавления в закладки";
$lang['playlist_view']['values']['sort_by']['rand()']           = "Случайно";

$lang['playlist_view']['values']['show_next_and_previous_info']['0']   = "По ID";
$lang['playlist_view']['values']['show_next_and_previous_info']['3']   = "По пользователю";

$lang['playlist_view']['block_short_desc'] = "Выводит всю информацию о плэйлисте";

$lang['playlist_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному плэйлисту (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг плэйлиста с одного IP.[kt|br]
	- Однократное голосование каждым флагом плэйлистов с одного IP.[kt|br]
	- Добавление новых видео в плэйлист (только для владельца плэйлиста).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['playlist_view']['groups']['pagination']} / {$lang['playlist_view']['groups']['sorting']}[/kt|b]
	[kt|br][kt|br]

	Этот блок может выводить видео из плэйлиста, поэтому он также ведет себя как стандартный блок списка с поддержкой
	пагинации и сортировки. По умолчанию все видео плэйлиста выводятся в одном списке, но вы также можете разбить их на
	страницы с помощью параметров этих секций.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['playlist_view']['block_examples'] = "
	[kt|b]Показать плэйлист с директорией 'my_playlist'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_playlist
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать плэйлист с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	- var_playlist_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать плэйлист с ID '46' и вывести видео плэйлиста по 10 на страницу с сортировкой по их рейтингу[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	- var_playlist_id = id[kt|br]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = rating[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
