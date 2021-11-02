<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_view messages
// =====================================================================================================================

$lang['dvd_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['dvd_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];

$lang['dvd_view']['params']['var_dvd_dir']                  = "Параметр URL-а, в котором передается директория DVD.";
$lang['dvd_view']['params']['var_dvd_id']                   = "Параметр URL-а, в котором передается ID DVD. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['dvd_view']['params']['show_next_and_previous_info']  = "Включает выборку информации о следующем и предыдущем DVD исходя из выбранного режима.";

$lang['dvd_view']['values']['show_next_and_previous_info']['0']   = "По ID";
$lang['dvd_view']['values']['show_next_and_previous_info']['1']   = "По группе";
$lang['dvd_view']['values']['show_next_and_previous_info']['2']   = "По владельцу";

$lang['dvd_view']['block_short_desc'] = "Выводит всю информацию о каком-либо DVD / канале / TV сезоне";

$lang['dvd_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному DVD / каналу / TV сезону (контекстному объекту), а также
	предоставляет следующую функциональность:
	[kt|br][kt|br]

	- Однократный рейтинг DVD с одного IP.[kt|br]
	- Однократное голосование каждым флагом DVD с одного IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['dvd_view']['block_examples'] = "
	[kt|b]Показать DVD с директорией 'my_dvd'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_dvd
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать DVD с ID '46'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_dir = dir[kt|br]
	- var_dvd_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
