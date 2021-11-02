<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_group_view messages
// =====================================================================================================================

$lang['dvd_group_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];

$lang['dvd_group_view']['params']['var_dvd_group_dir']              = "Параметр URL-а, в котором передается директория группы DVD.";
$lang['dvd_group_view']['params']['var_dvd_group_id']               = "Параметр URL-а, в котором передается ID группы DVD. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";

$lang['dvd_group_view']['block_short_desc'] = "Выводит всю информацию о о какой-либо группе DVD / каналов / TV сериале";

$lang['dvd_group_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданной группе DVD / каналов / TV сериале (контекстному объекту).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['dvd_group_view']['block_examples'] = "
	[kt|b]Показать группу DVD с директорией 'my_dvd_group'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_group_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_dvd_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать группу DVD с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_group_dir = dir[kt|br]
	- var_dvd_group_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
