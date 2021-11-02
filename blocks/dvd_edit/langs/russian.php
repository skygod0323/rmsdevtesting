<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_edit messages
// =====================================================================================================================

$lang['dvd_edit']['groups']['new_objects']      = $lang['website_ui']['block_group_default_new_objects'];
$lang['dvd_edit']['groups']['edit_mode']        = $lang['website_ui']['block_group_default_edit_mode'];
$lang['dvd_edit']['groups']['validation']       = $lang['website_ui']['block_group_default_validation'];
$lang['dvd_edit']['groups']['functionality']    = $lang['website_ui']['block_group_default_functionality'];
$lang['dvd_edit']['groups']['navigation']       = $lang['website_ui']['block_group_default_navigation'];

$lang['dvd_edit']['params']['force_inactive']           = $lang['website_ui']['parameter_default_force_inactive'];
$lang['dvd_edit']['params']['var_dvd_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['dvd_edit']['params']['forbid_change']            = $lang['website_ui']['parameter_default_forbid_change'];
$lang['dvd_edit']['params']['force_inactive_on_edit']   = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['dvd_edit']['params']['require_description']      = "Делает поле описания обязательным для заполнения.";
$lang['dvd_edit']['params']['require_cover1_front']     = "Делает поле лицевого скриншота 1 обязательным для загрузки.";
$lang['dvd_edit']['params']['require_cover1_back']      = "Делает поле оборотного скриншота 1 обязательным для загрузки.";
$lang['dvd_edit']['params']['require_cover2_front']     = "Делает поле лицевого скриншота 2 обязательным для загрузки.";
$lang['dvd_edit']['params']['require_cover2_back']      = "Делает поле оборотного скриншота 2 обязательным для загрузки.";
$lang['dvd_edit']['params']['require_tags']             = "Делает поле тэгов обязательным для заполнения.";
$lang['dvd_edit']['params']['require_categories']       = "Делает поле категорий обязательным для заполнения.";
$lang['dvd_edit']['params']['max_categories']           = "Задает максимальное кол-во выбранных одновременно категорий.";
$lang['dvd_edit']['params']['use_captcha']              = "Включает использование визуальной защиты от авто-сабмита при создании новых DVD.";
$lang['dvd_edit']['params']['redirect_unknown_user_to'] = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['dvd_edit']['params']['redirect_on_new_done']     = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";
$lang['dvd_edit']['params']['redirect_on_change_done']  = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";

$lang['dvd_edit']['block_short_desc'] = "Предоставляет функционал для создания и редактирования DVD / каналов / ТВ сезонов";

$lang['dvd_edit']['block_desc'] = "
	Блок отображает формы создания и редактирования для DVD / каналов / ТВ сезонов.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: когда поле названия не заполнено [поле = title][kt|br]
	- [kt|b]title_exists[/kt|b]: когда объект с таким названием уже существует [поле = title][kt|br]
	- [kt|b]description_required[/kt|b]: когда поле описания не заполнено, но настроено как обязательное [поле = description][kt|br]
	- [kt|b]cover1_front_required[/kt|b]: когда не загружено файла в поле лицевого скриншота 1, но настроено как обязательное [поле = cover1_front][kt|br]
	- [kt|b]cover1_front_invalid_format[/kt|b]: когда изображение, загруженное в поле лицевого скриншота 1, имеет неверный формат [поле = cover1_front][kt|br]
	- [kt|b]cover1_front_invalid_size[/kt|b]: когда изображение, загруженное в поле лицевого скриншота 1, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = cover1_front][kt|br]
	- [kt|b]cover1_back_required[/kt|b]: когда не загружено файла в поле оборотного скриншота 1, но настроено как обязательное [поле = cover1_back][kt|br]
	- [kt|b]cover1_back_invalid_format[/kt|b]: когда изображение, загруженное в поле оборотного скриншота 1, имеет неверный формат [поле = cover1_back][kt|br]
	- [kt|b]cover1_back_invalid_size[/kt|b]: когда изображение, загруженное в поле оборотного скриншота 1, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = cover1_back][kt|br]
	- [kt|b]cover2_front_required[/kt|b]: когда не загружено файла в поле лицевого скриншота 2, но настроено как обязательное [поле = cover2_front][kt|br]
	- [kt|b]cover2_front_invalid_format[/kt|b]: когда изображение, загруженное в поле лицевого скриншота 2, имеет неверный формат [поле = cover2_front][kt|br]
	- [kt|b]cover2_front_invalid_size[/kt|b]: когда изображение, загруженное в поле лицевого скриншота 2, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = cover2_front][kt|br]
	- [kt|b]cover2_back_required[/kt|b]: когда не загружено файла в поле оборотного скриншота 2, но настроено как обязательное [поле = cover2_back][kt|br]
	- [kt|b]cover2_back_invalid_format[/kt|b]: когда изображение, загруженное в поле оборотного скриншота 2, имеет неверный формат [поле = cover2_back][kt|br]
	- [kt|b]cover2_back_invalid_size[/kt|b]: когда изображение, загруженное в поле оборотного скриншота 2, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = cover2_back][kt|br]
	- [kt|b]tags_required[/kt|b]: когда поле тэгов не заполнено, но настроено как обязательное [поле = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: когда поле категорий не заполнено, но настроено как обязательное [поле = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: когда выбрано кол-во категорий больше допустимого [поле = category_ids][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: когда в поле стоимости подписки на DVD (в токенах) указано не целочисленное число [поле = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['dvd_edit']['block_examples'] = "
	[kt|b]Показать форму добавления DVD[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму редактирования DVD с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_dvd_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
