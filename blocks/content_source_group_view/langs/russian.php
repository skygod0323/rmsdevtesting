<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_group_view messages
// =====================================================================================================================

$lang['content_source_group_view']['groups']['object_context']  = $lang['website_ui']['block_group_default_context_object'];

$lang['content_source_group_view']['params']['var_content_source_group_dir']    = "Параметр URL-а, в котором передается директория группы контент провайдеров.";
$lang['content_source_group_view']['params']['var_content_source_group_id']     = "Параметр URL-а, в котором передается ID группы контент провайдеров. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";

$lang['content_source_group_view']['block_short_desc'] = "Выводит всю информацию о какой-либо группе контент провайдеров";

$lang['content_source_group_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданной группе контент провайдеров (контекстному объекту).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['content_source_group_view']['block_examples'] = "
	[kt|b]Показать группу контент провайдеров с директорией 'my_content_source_group'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_content_source_group_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать группу контент провайдеров с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_content_source_group_dir = dir[kt|br]
	- var_content_source_group_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
