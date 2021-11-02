<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// video_edit messages
// =====================================================================================================================

$lang['video_edit']['groups']['new_objects']    = $lang['website_ui']['block_group_default_new_objects'];
$lang['video_edit']['groups']['edit_mode']      = $lang['website_ui']['block_group_default_edit_mode'];
$lang['video_edit']['groups']['validation']     = $lang['website_ui']['block_group_default_validation'];
$lang['video_edit']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['video_edit']['groups']['navigation']     = $lang['website_ui']['block_group_default_navigation'];

$lang['video_edit']['params']['allow_anonymous']            = "Разрешает незалогиненным пользователям загружать новые видео.";
$lang['video_edit']['params']['force_inactive']             = $lang['website_ui']['parameter_default_force_inactive'];
$lang['video_edit']['params']['upload_as_format']           = "Укажите постфикс формата видео, если вы хотите избежать конвертации для всех загружаемых видео; в этом случае все загружаемые видеофайлы будут сохраняться как есть без обработки под этим форматом. По умолчанию все видео загружаются как исходные файлы, что запускает для них полную фазу конвертации.";
$lang['video_edit']['params']['allow_embed']                = "Разрешает пользователям загружать embed коды.";
$lang['video_edit']['params']['allow_embed_domains']        = "Используется совместно с параметром [kt|b]allow_embed[/kt|b]. Список разрешенных доменов embed кодов, разделенный запятыми. Вы можете использовать символ [kt|b]*[/kt|b] для индикации сабдоменов, например, [kt|b]*.youtube.com[/kt|b] разрешит все сабдомены 3-го уровня для домена youtube.com; [kt|b]youtube.*[/kt|b] разрешит все youtube домены 2-го уровня.";
$lang['video_edit']['params']['var_video_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['video_edit']['params']['forbid_change']              = $lang['website_ui']['parameter_default_forbid_change'];
$lang['video_edit']['params']['forbid_change_screenshots']  = "Запрещает редактирование скриншотов у существующих видео.";
$lang['video_edit']['params']['force_inactive_on_edit']     = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['video_edit']['params']['min_duration']               = "Указывает минимально допустимую длительность загружаемых видеофайлов в секундах.";
$lang['video_edit']['params']['max_duration']               = "Указывает максимально допустимую длительность загружаемых видеофайлов в секундах.";
$lang['video_edit']['params']['max_duration_premium']       = "Указывает максимально допустимую длительность загружаемых видеофайлов в секундах для премиум пользователей. Перекрывает значение [kt|b]max_duration[/kt|b].";
$lang['video_edit']['params']['max_duration_webmaster']     = "Указывает максимально допустимую длительность загружаемых видеофайлов в секундах для пользователей-вебмастеров. Перекрывает значение [kt|b]max_duration[/kt|b].";
$lang['video_edit']['params']['optional_description']       = "Делает поле описания необязательным для заполнения.";
$lang['video_edit']['params']['optional_tags']              = "Делает поле тэгов необязательным для заполнения.";
$lang['video_edit']['params']['optional_categories']        = "Делает поле категорий необязательным для заполнения.";
$lang['video_edit']['params']['max_categories']             = "Задает максимальное кол-во выбранных одновременно категорий.";
$lang['video_edit']['params']['use_captcha']                = "Включает использование визуальной защиты от авто-сабмита при создании новых видео.";
$lang['video_edit']['params']['set_custom_flag1']           = "Устанавливает указанное значение для доп. флага 1.";
$lang['video_edit']['params']['set_custom_flag2']           = "Устанавливает указанное значение для доп. флага 2.";
$lang['video_edit']['params']['set_custom_flag3']           = "Устанавливает указанное значение для доп. флага 3.";
$lang['video_edit']['params']['redirect_unknown_user_to']   = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['video_edit']['params']['redirect_on_new_done']       = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";
$lang['video_edit']['params']['redirect_on_change_done']    = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";

$lang['video_edit']['block_short_desc'] = "Предоставляет функционал для создания и редактирования видео";

$lang['video_edit']['block_desc'] = "
	Блок отображает формы создания и редактирования для видео.
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
	- [kt|b]screenshot_invalid_format[/kt|b]: когда изображение, загруженное в поле скриншота, имеет неверный формат [поле = screenshot][kt|br]
	- [kt|b]screenshot_invalid_size[/kt|b]: когда изображение, загруженное в поле скриншота, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = screenshot][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: когда в поле стоимости видео (в токенах) указано не целочисленное число [поле = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: когда включена визуальная защита и ее решение не заполнено [поле = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: когда включена визуальная защита и ее решение не корректно [поле = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	В зависимости от того, загружается ли видеофайл с локального диска или с URL-а, или создается как embed код, блок будет проводить дополнительные проверки.
	[kt|br][kt|br]

	При загрузке файла с диска, пользователь должен загрузить корректный видеофайл:[kt|br]
	[kt|code]
	- [kt|b]content_required[/kt|b]: когда поле видеофайла не заполнено [поле = content][kt|br]
	- [kt|b]content_filesize_limit[/kt|b]: когда загруженный файл имеет размер файла больше допустимого [поле = content][kt|br]
	- [kt|b]content_invalid_format[/kt|b]: когда загруженный файл не является видеофайлом поддерживаемого формата (список поддерживаемых форматов можно вывести через токен [kt|b]%1%[/kt|b]) [поле = content][kt|br]
	- [kt|b]content_duration_minimum[/kt|b]: когда длительность загруженного видеофайла меньше допустимой (минимальную длительность можно вывести через токен [kt|b]%1%[/kt|b]) [поле = content][kt|br]
	- [kt|b]content_duration_maximum[/kt|b]: когда длительность загруженного видеофайла больше допустимой (максимальную длительность можно вывести через токен [kt|b]%1%[/kt|b]) [поле = content][kt|br]
	- [kt|b]content_duplicate[/kt|b]: когда загруженный видеофайл является дубликатом уже созданного ранее видео [поле = content][kt|br]
	- [kt|b]content_unknown_error[/kt|b]: когда во время загрузки возникла неожиданная ошибка [поле = content][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	При загрузке файла с URL-а, пользователь должен указать URL, по которому скачивается корректный видеофайл:[kt|br]
	[kt|code]
	- [kt|b]url_required[/kt|b]: когда поле URL-а не заполнено [поле = url][kt|br]
	- [kt|b]url_invalid[/kt|b]: когда по указанному URL-у не находится никакого файла [поле = url][kt|br]
	- [kt|b]url_filesize_limit[/kt|b]: когда загруженный файл имеет размер файла больше допустимого [поле = url][kt|br]
	- [kt|b]url_invalid_format[/kt|b]: когда загруженный файл не является видеофайлом поддерживаемого формата (список поддерживаемых форматов можно вывести через токен [kt|b]%1%[/kt|b]) [поле = url][kt|br]
	- [kt|b]url_duration_minimum[/kt|b]: когда длительность загруженного видеофайла меньше допустимой (минимальную длительность можно вывести через токен [kt|b]%1%[/kt|b]) [поле = url][kt|br]
	- [kt|b]url_duration_maximum[/kt|b]: когда длительность загруженного видеофайла больше допустимой (максимальную длительность можно вывести через токен [kt|b]%1%[/kt|b]) [поле = url][kt|br]
	- [kt|b]url_duplicate[/kt|b]: когда загруженный видеофайл является дубликатом уже созданного ранее видео [поле = url][kt|br]
	- [kt|b]url_unknown_error[/kt|b]: когда во время загрузки возникла неожиданная ошибка [поле = url][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	При загрузке embed кода пользователь должен указать 3 обязательных поля - код embed, длительность и скриншот:[kt|br]
	[kt|code]
	- [kt|b]embed_required[/kt|b]: когда поле кода embed не заполнено [поле = embed][kt|br]
	- [kt|b]embed_invalid[/kt|b]: когда поле кода embed не содержит корректный распознаваемый embed код [поле = embed][kt|br]
	- [kt|b]embed_domain_forbidden[/kt|b]: когда поле кода embed ссылается на неразрешенный домен [поле = embed][kt|br]
	- [kt|b]embed_duplicate[/kt|b]: когда видео с таким кодом embed уже было создано ранее [поле = embed][kt|br]
	- [kt|b]duration_required[/kt|b]: когда поле длительности не заполнено [поле = duration][kt|br]
	- [kt|b]duration_invalid[/kt|b]: когда поле длительности имеет неверный формат [поле = duration][kt|br]
	- [kt|b]screenshot_required[/kt|b]: когда не загружено файла в поле скриншота [поле = screenshot][kt|br]
	- [kt|b]screenshot_invalid_format[/kt|b]: когда изображение, загруженное в поле скриншота, имеет неверный формат [поле = screenshot][kt|br]
	- [kt|b]screenshot_invalid_size[/kt|b]: когда изображение, загруженное в поле скриншота, имеет размер меньше допустимого (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = screenshot][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['video_edit']['block_examples'] = "
	[kt|b]Показать форму добавления видео[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_video_id = id[kt|br]
	- min_duration = 10[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму редактирования видео с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_video_id = id[kt|br]
	- min_duration = 10[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
