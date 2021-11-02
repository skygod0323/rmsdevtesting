<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// video_view messages
// =====================================================================================================================

$lang['video_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['video_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];
$lang['video_view']['groups']['limit_views']        = "Ограничение на кол-во просмотров с одного IP";

$lang['video_view']['params']['var_video_dir']                  = "Параметр URL-а, в котором передается директория видео.";
$lang['video_view']['params']['var_video_id']                   = "Параметр URL-а, в котором передается ID видео. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['video_view']['params']['show_next_and_previous_info']    = "Включает выборку информации о следующем и предыдущем видео исходя из выбранного режима.";
$lang['video_view']['params']['show_stats']                     = "Включает выборку статистики посещений видео с разбиением по дням.";
$lang['video_view']['params']['limit_unknown_user']             = "Устанавливает ограничение для незалогиненных пользователей. Указывает кол-во видео (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";
$lang['video_view']['params']['limit_member']                   = "Устанавливает ограничение для стандартных пользователей. Указывает кол-во видео (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";
$lang['video_view']['params']['limit_premium_member']           = "Устанавливает ограничение для премиум пользователей. Указывает кол-во видео (первое число), которое можно просмотреть в течении заданного кол-ва секунд (второе число, максимум 86400, т.е. сутки).";

$lang['video_view']['values']['show_next_and_previous_info']['0']   = "По дате публикации";
$lang['video_view']['values']['show_next_and_previous_info']['1']   = "По DVD / каналу / TV сезону";
$lang['video_view']['values']['show_next_and_previous_info']['2']   = "По контент провайдеру";
$lang['video_view']['values']['show_next_and_previous_info']['3']   = "По пользователю";

$lang['video_view']['block_short_desc'] = "Выводит всю информацию о каком-либо видео";

$lang['video_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному видео (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Отображение видеоплеера на основе настроек плеера панели администрирования.[kt|br]
	- Однократный рейтинг видео с одного IP.[kt|br]
	- Однократное голосование каждым флагом видео с одного IP.[kt|br]
	- Добавление видео в закладки мемберзоны или в плэйлист (только для залогиненных).[kt|br]
	- Удаление видео из закладок мемберзоны или из плэйлиста (только для залогиненных).[kt|br]
	- Покупка премиум доступа к видео за токены KVS (только для залогиненных).[kt|br]
	- Создание плэйлиста на лету (только для залогиненных).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['video_view']['groups']['limit_views']}[/kt|b]
	[kt|br][kt|br]

	Эти опции можно использовать чтобы ограничить кол-во видео, которое можно просмотреть с одного IP адреса за
	указанный интервал времени (например, за 1 час, за 4 часа, до 24 часов). Включение этих ограничений увеличит
	нагрузку на базу данных.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['video_view']['block_examples'] = "
	[kt|b]Показать видео с директорией 'my_video'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_video_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_video
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать видео с ID '198'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_video_dir = dir[kt|br]
	- var_video_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=198
	[/kt|code]
";
