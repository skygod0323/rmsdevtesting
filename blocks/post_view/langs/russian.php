<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// post_view messages
// =====================================================================================================================

$lang['post_view']['groups']['object_context']  = $lang['website_ui']['block_group_default_context_object'];
$lang['post_view']['groups']['additional_data'] = $lang['website_ui']['block_group_default_additional_data'];

$lang['post_view']['params']['var_post_dir']                = "Параметр URL-а, в котором передается директория записи.";
$lang['post_view']['params']['var_post_id']                 = "Параметр URL-а, в котором передается ID записи. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['post_view']['params']['show_next_and_previous_info'] = "Включает выборку информации о следующей и предыдущей записи исходя из выбранного режима.";

$lang['post_view']['values']['show_next_and_previous_info']['0']    = "По дате публикации";
$lang['post_view']['values']['show_next_and_previous_info']['3']    = "По пользователю";

$lang['post_view']['block_short_desc'] = "Выводит всю информацию о какой-либо записи";

$lang['post_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданной записи (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг записи с одного IP.[kt|br]
	- Однократное голосование каждым флагом записи с одного IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['post_view']['block_examples'] = "
	[kt|b]Показать запись с директорией 'my_post'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_post_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_post
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать запись с ID '198'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_post_dir = dir[kt|br]
	- var_post_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=198
	[/kt|code]
";
