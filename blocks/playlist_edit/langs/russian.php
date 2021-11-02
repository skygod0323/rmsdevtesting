<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// playlist_edit messages
// =====================================================================================================================

$lang['playlist_edit']['groups']['new_objects']     = $lang['website_ui']['block_group_default_new_objects'];
$lang['playlist_edit']['groups']['edit_mode']       = $lang['website_ui']['block_group_default_edit_mode'];
$lang['playlist_edit']['groups']['validation']      = $lang['website_ui']['block_group_default_validation'];
$lang['playlist_edit']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['playlist_edit']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['playlist_edit']['params']['force_inactive']              = $lang['website_ui']['parameter_default_force_inactive'];
$lang['playlist_edit']['params']['var_playlist_id']             = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['playlist_edit']['params']['force_inactive_on_edit']      = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['playlist_edit']['params']['require_description']         = "Делает поле описания обязательным для заполнения (только публичные плэйлисты).";
$lang['playlist_edit']['params']['require_tags']                = "Делает поле тэгов обязательным для заполнения (только публичные плэйлисты).";
$lang['playlist_edit']['params']['require_categories']          = "Делает поле категорий обязательным для заполнения (только публичные плэйлисты).";
$lang['playlist_edit']['params']['max_categories']              = "Задает максимальное кол-во выбранных одновременно категорий (только публичные плэйлисты).";
$lang['playlist_edit']['params']['use_captcha']                 = "Включает использование визуальной защиты от авто-сабмита при создании новых плэйлистов.";
$lang['playlist_edit']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['playlist_edit']['params']['redirect_on_new_done']        = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";
$lang['playlist_edit']['params']['redirect_on_change_done']     = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";

$lang['playlist_edit']['block_short_desc'] = "Предоставляет функционал для создания и редактирования плэйлистов";

$lang['playlist_edit']['block_desc'] = "
	Блок отображает формы создания и редактирования для плэйлистов.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: когда поле названия не заполнено [поле = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: когда длина поля названия менее 5 символов [поле = title][kt|br]
	- [kt|b]title_exists[/kt|b]: когда объект с таким названием уже существует [поле = title][kt|br]
	- [kt|b]description_required[/kt|b]: когда поле описания не заполнено [поле = description][kt|br]
	- [kt|b]tags_required[/kt|b]: когда поле тэгов не заполнено [поле = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: когда поле категорий не заполнено [поле = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: когда выбрано кол-во категорий больше допустимого [поле = category_ids][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['playlist_edit']['block_examples'] = "
	[kt|b]Показать форму добавления плэйлиста[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_playlist_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму редактирования плэйлиста с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_playlist_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
