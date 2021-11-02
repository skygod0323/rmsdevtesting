<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// album_view messages
// =====================================================================================================================

$lang['album_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['album_view']['groups']['pagination']         = "Пагинация фотографий альбома";
$lang['album_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];
$lang['album_view']['groups']['limit_views']        = "Ограничение на кол-во просмотров с одного IP";

$lang['album_view']['params']['var_album_dir']                  = "Параметр URL-а, в котором передается директория альбома.";
$lang['album_view']['params']['var_album_id']                   = "Параметр URL-а, в котором передается ID альбома. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['album_view']['params']['var_album_image_id']             = "Параметр URL-а, в котором передается ID фотографии из данного альбома. Если указан и содержит значение, то указанная фотография будет отображаться вместо главной фотографии альбома.";
$lang['album_view']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['album_view']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['album_view']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['album_view']['params']['show_next_and_previous_info']    = "Включает выборку информации о следующем и предыдущем альбомах исходя из выбранного режима.";
$lang['album_view']['params']['show_stats']                     = "Включает выборку статистики посещений альбома с разбиением по дням.";
$lang['album_view']['params']['limit_unknown_user']             = "Устанавливает ограничение для незалогиненных пользователей. Указывает кол-во альбомов (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";
$lang['album_view']['params']['limit_member']                   = "Устанавливает ограничение для стандартных пользователей. Указывает кол-во альбомов (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";
$lang['album_view']['params']['limit_premium_member']           = "Устанавливает ограничение для премиум пользователей. Указывает кол-во альбомов (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";

$lang['album_view']['values']['show_next_and_previous_info']['0']   = "По дате публикации";
$lang['album_view']['values']['show_next_and_previous_info']['2']   = "По контент провайдеру";
$lang['album_view']['values']['show_next_and_previous_info']['3']   = "По пользователю";

$lang['album_view']['block_short_desc'] = "Выводит всю информацию о каком-либо альбоме";

$lang['album_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному альбому (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг альбома с одного IP.[kt|br]
	- Однократное голосование каждым флагом альбомов с одного IP.[kt|br]
	- Добавление альбома в закладки мемберзоны (только для залогиненных).[kt|br]
	- Удаление альбома из закладок мемберзоны (только для залогиненных).[kt|br]
	- Покупка премиум доступа к альбому за токены KVS (только для залогиненных).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['album_view']['groups']['pagination']}[/kt|b]
	[kt|br][kt|br]

	Фотографии альбома, как правило, отображаются списком уменьшенных изображений, поэтому данный блок также ведет
	себя как стандартный блок списка с поддержкой пагинации. По умолчанию все фотографии альбома выводятся в одном
	списке, но вы также можете разбить их на страницы с помощью параметров этой секции.
	[kt|br][kt|br]

	[kt|b]{$lang['album_view']['groups']['limit_views']}[/kt|b]
	[kt|br][kt|br]

	Эти опции можно использовать чтобы ограничить кол-во альбомов, которое можно просмотреть с одного IP адреса за
	указанный интервал времени (например, за 1 час, за 4 часа, до 24 часов). Включение этих ограничений увеличит
	нагрузку на базу данных.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['album_view']['block_examples'] = "
	[kt|b]Показать альбом с директорией 'my_photo_album'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать фотографию с ID '198' из альбома с директорией 'my_photo_album'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	- var_album_image_id = image_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album&image_id=198
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать фотографию с ID '198' из альбома с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	- var_album_id = id[kt|br]
	- var_album_image_id = image_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11&image_id=198
	[/kt|code]
";
