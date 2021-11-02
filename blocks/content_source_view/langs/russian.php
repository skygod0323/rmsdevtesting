<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_view messages
// =====================================================================================================================

$lang['content_source_view']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['content_source_view']['groups']['additional_data']   = $lang['website_ui']['block_group_default_additional_data'];

$lang['content_source_view']['params']['var_content_source_dir']        = "Параметр URL-а, в котором передается директория контент провайдера.";
$lang['content_source_view']['params']['var_content_source_id']         = "Параметр URL-а, в котором передается ID контент провайдера. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['content_source_view']['params']['show_next_and_previous_info']   = "Включает выборку информации о следующем и предыдущем контент провайдерах исходя из выбранного режима.";

$lang['content_source_view']['values']['show_next_and_previous_info']['0']   = "По ID";
$lang['content_source_view']['values']['show_next_and_previous_info']['1']   = "По группе";

$lang['content_source_view']['block_short_desc'] = "Выводит всю информацию о каком-либо контент провайдере";

$lang['content_source_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному контент провайдеру (контекстному объекту), а также
	предоставляет следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг контент провайдера с одного IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['content_source_view']['block_examples'] = "
	[kt|b]Показать контент провайдер с директорией 'my_content_source'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_content_source_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать контент провайдер с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_content_source_dir = dir[kt|br]
	- var_content_source_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
