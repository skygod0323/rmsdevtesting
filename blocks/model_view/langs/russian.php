<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// model_view messages
// =====================================================================================================================

$lang['model_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['model_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];

$lang['model_view']['params']['var_model_dir']                  = "Параметр URL-а, в котором передается директория модели.";
$lang['model_view']['params']['var_model_id']                   = "Параметр URL-а, в котором передается ID модели. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['model_view']['params']['show_next_and_previous_info']    = "Включает выборку информации о следующей и предыдущей модели исходя из выбранного режима.";

$lang['model_view']['values']['show_next_and_previous_info']['0']   = "По ID";
$lang['model_view']['values']['show_next_and_previous_info']['1']   = "По группе";

$lang['model_view']['block_short_desc'] = "Выводит всю информацию о какой-либо модели";

$lang['model_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданной модели (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг модели с одного IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['model_view']['block_examples'] = "
	[kt|b]Показать модель с директорией 'my_model'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_model_dir = dir[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_model
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать модель с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_model_dir = dir[kt|br]
	- var_model_id = id[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
