<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// album_edit messages
// =====================================================================================================================

$lang['album_edit']['groups']['new_objects']    = $lang['website_ui']['block_group_default_new_objects'];
$lang['album_edit']['groups']['edit_mode']      = $lang['website_ui']['block_group_default_edit_mode'];
$lang['album_edit']['groups']['validation']     = $lang['website_ui']['block_group_default_validation'];
$lang['album_edit']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['album_edit']['groups']['navigation']     = $lang['website_ui']['block_group_default_navigation'];

$lang['album_edit']['params']['allow_anonymous']            = "Разрешает незалогиненным пользователям создавать новые альбомы.";
$lang['album_edit']['params']['force_inactive']             = $lang['website_ui']['parameter_default_force_inactive'];
$lang['album_edit']['params']['var_album_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['album_edit']['params']['forbid_change']              = $lang['website_ui']['parameter_default_forbid_change'];
$lang['album_edit']['params']['forbid_change_images']       = "Запрещает редактирование фотографий у существующих альбомов.";
$lang['album_edit']['params']['force_inactive_on_edit']     = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['album_edit']['params']['allowed_formats']            = "Список разрешенных форматов через запятую. Поддерживаются следующие форматы изображений: [kt|b]jpg[/kt|b], [kt|b]gif[/kt|b], [kt|b]png[/kt|b]. Если данный параметр не включен, то будут разрешены все форматы изображений.";
$lang['album_edit']['params']['min_image_width']            = "Указывает минимально допустимую ширину загружаемых фотографий (в пикселах).";
$lang['album_edit']['params']['min_image_height']           = "Указывает минимально допустимую высоту загружаемых фотографий (в пикселах).";
$lang['album_edit']['params']['min_image_count']            = "Указывает минимально допустимое кол-во загружаемых фотографий при создании новых альбомов.";
$lang['album_edit']['params']['optional_description']       = "Делает поле описания необязательным для заполнения.";
$lang['album_edit']['params']['optional_tags']              = "Делает поле тэгов необязательным для заполнения.";
$lang['album_edit']['params']['optional_categories']        = "Делает поле категорий необязательным для заполнения.";
$lang['album_edit']['params']['max_categories']             = "Задает максимальное кол-во выбранных одновременно категорий.";
$lang['album_edit']['params']['use_captcha']                = "Включает использование визуальной защиты от авто-сабмита при создании новых альбомов.";
$lang['album_edit']['params']['set_custom_flag1']           = "Устанавливает указанное значение для доп. флага 1.";
$lang['album_edit']['params']['set_custom_flag2']           = "Устанавливает указанное значение для доп. флага 2.";
$lang['album_edit']['params']['set_custom_flag3']           = "Устанавливает указанное значение для доп. флага 3.";
$lang['album_edit']['params']['redirect_unknown_user_to']   = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['album_edit']['params']['redirect_on_new_done']       = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";
$lang['album_edit']['params']['redirect_on_change_done']    = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";

$lang['album_edit']['block_short_desc'] = "Предоставляет функционал для создания и редактирования альбомов";

$lang['album_edit']['block_desc'] = "
	Блок отображает формы создания и редактирования для альбомов.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: когда поле названия не заполнено [поле = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: когда длина поля названия менее 5 символов [поле = title][kt|br]
	- [kt|b]description_required[/kt|b]: когда поле описания не заполнено [поле = description][kt|br]
	- [kt|b]tags_required[/kt|b]: когда поле тэгов не заполнено [поле = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: когда поле категорий не заполнено [поле = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: когда выбрано кол-во категорий больше допустимого [поле = category_ids][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: когда в поле стоимости альбома (в токенах) указано не целочисленное число [поле = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	- [kt|b]content_required[/kt|b]: когда идет создание нового альбома и не загружено ни одной фотографии [поле = content][kt|br]
	- [kt|b]content_filesize_limit[/kt|b]: когда идет создание нового альбома и суммарный размер всех загруженных файлов больше допустимого ограничения [поле = content][kt|br]
	- [kt|b]content_images_empty[/kt|b]: когда идет создание нового альбома и все загруженные файлы либо не являются изображениями поддерживаемых форматов, либо не подходят под ограничения размеров [поле = content][kt|br]
	- [kt|b]content_images_minimum[/kt|b]: когда идет создание нового альбома и кол-во принятых фотографий меньше допустимого ограничения [поле = content][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['album_edit']['block_examples'] = "
	[kt|b]Показать форму добавления альбома[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_album_id = id[kt|br]
	- min_image_width = 800[kt|br]
	- min_image_height = 600[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму редактирования альбома с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_album_id = id[kt|br]
	- min_image_width = 800[kt|br]
	- min_image_height = 600[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
