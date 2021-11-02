<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_edit messages
// =====================================================================================================================

$lang['member_profile_edit']['groups']['functionality'] = $lang['website_ui']['block_group_default_functionality'];
$lang['member_profile_edit']['groups']['validation']    = $lang['website_ui']['block_group_default_validation'];
$lang['member_profile_edit']['groups']['navigation']    = $lang['website_ui']['block_group_default_navigation'];

$lang['member_profile_edit']['params']['use_confirm_email']             = "При включенном параметре изменение адреса email должно быть подтверждено с нового адреса.";
$lang['member_profile_edit']['params']['require_avatar']                = "Делает поле аватара обязательным для загрузки.";
$lang['member_profile_edit']['params']['require_cover']                 = "Делает поле обложки обязательным для загрузки.";
$lang['member_profile_edit']['params']['require_country']               = "Делает поле страны обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_city']                  = "Делает поле города обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_gender']                = "Делает поле пола обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_orientation']           = "Делает поле ориентации обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_relationship_status']   = "Делает поле семейного положения обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_birth_date']            = "Делает поле дня рождения обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom1']               = "Делает доп. поле 1 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom2']               = "Делает доп. поле 2 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom3']               = "Делает доп. поле 3 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom4']               = "Делает доп. поле 4 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom5']               = "Делает доп. поле 5 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom6']               = "Делает доп. поле 6 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom7']               = "Делает доп. поле 7 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom8']               = "Делает доп. поле 8 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom9']               = "Делает доп. поле 9 обязательным для заполнения.";
$lang['member_profile_edit']['params']['require_custom10']              = "Делает доп. поле 10 обязательным для заполнения.";
$lang['member_profile_edit']['params']['redirect_unknown_user_to']      = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['member_profile_edit']['block_short_desc'] = "Предоставляет функционал для редактирования личного профиля пользователей";

$lang['member_profile_edit']['block_desc'] = "
	Блок предоставляет возможность изменения своего профиля, пароля и email-а.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_display_modes']}[/kt|b]
	[kt|br][kt|br]

	Блок поддерживает 3 различные формы:
	[kt|br][kt|br]

	1) [kt|b]Форма изменения данных профиля[/kt|b]. Эта форма отображается по умолчанию, если ничего не передавать в
	   параметре URL-а [kt|b]action[/kt|b].[kt|br]
	2) [kt|b]Форма изменения пароля[/kt|b]. Эта форма будет отображаться, если на страницу с блоком передать параметр
	   [kt|b]action=change_pass[/kt|b] в URL-е.[kt|br]
	3) [kt|b]Форма изменения email-а[/kt|b]. Эта форма будет отображаться, если на страницу с блоком передать
	   параметр [kt|b]action=change_email[/kt|b] в URL-е. Изменение email-а может потребовать подтверждения с нового
	   email адреса, если в настройках блока включен параметр [kt|b]use_confirm_email[/kt|b].
	[kt|br][kt|br]

	Блок также поддерживает отображение нескольких информационных сообщений в ответ на действия пользователей:
	[kt|br][kt|br]
	1) [kt|b]Сообщение о смене email-а[/kt|b]. Это сообщение будет отображаться, если на страницу с блоком передать
	   параметр [kt|b]action=confirm[/kt|b] в URL-е. Такой URL создается при отправке пользователю email сообщения
	   о необходимости подтверждения смены email-а (если в настройках блока включен параметр
	   [kt|b]use_confirm_email[/kt|b]).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	Для формы изменения данных профиля:[kt|br]
	[kt|code]
	- [kt|b]display_name_required[/kt|b]: когда поле никнейма не заполнено, но настроено как обязательное [поле = display_name][kt|br]
	- [kt|b]display_name_minimum[/kt|b]: когда поле никнейма имеет длину меньше допустимой (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = display_name][kt|br]
	- [kt|b]display_name_exists[/kt|b]: когда пользователь с таким никнеймом уже зарегистрирован [поле = display_name][kt|br]
	- [kt|b]country_id_required[/kt|b]: когда поле страны не заполнено, но настроено как обязательное [поле = country_id][kt|br]
	- [kt|b]city_required[/kt|b]: когда поле города не заполнено, но настроено как обязательное [поле = city][kt|br]
	- [kt|b]gender_id_required[/kt|b]: когда поле пола не заполнено, но настроено как обязательное [поле = gender_id][kt|br]
	- [kt|b]orientation_id_required[/kt|b]: когда поле ориентации не заполнено, но настроено как обязательное [поле = orientation_id][kt|br]
	- [kt|b]relationship_status_id_required[/kt|b]: когда поле семейного положения не заполнено, но настроено как обязательное [поле = relationship_status_id][kt|br]
	- [kt|b]birth_date_required[/kt|b]: когда поле даты рождения не заполнено, но настроено как обязательное [поле = birth_date][kt|br]
	- [kt|b]birth_date_invalid[/kt|b]: когда поле даты рождения имеет неверный формат [поле = birth_date][kt|br]
	- [kt|b]avatar_required[/kt|b]: когда не загружено файла в поле аватара, но настроено как обязательное [поле = avatar][kt|br]
	- [kt|b]avatar_invalid_format[/kt|b]: когда изображение, загруженное в поле аватара, имеет неверный формат [поле = avatar][kt|br]
	- [kt|b]avatar_invalid_size[/kt|b]: когда изображение, загруженное в поле аватара, имеет размер меньше допустимого (минимальный размер можно вывести через токен [kt|b]%1%[/kt|b]) [поле = avatar][kt|br]
	- [kt|b]cover_required[/kt|b]: когда не загружено файла в поле обложки, но настроено как обязательное [поле = cover][kt|br]
	- [kt|b]cover_invalid_format[/kt|b]: когда изображение, загруженное в поле обложки, имеет неверный формат [поле = cover][kt|br]
	- [kt|b]cover_invalid_size[/kt|b]: когда изображение, загруженное в поле обложки, имеет размер меньше допустимого (минимальный размер можно вывести через токен [kt|b]%1%[/kt|b]) [поле = cover][kt|br]
	- [kt|b]custom1_required[/kt|b]: когда дополнительное поле 1 не заполнено, но настроено как обязательное [поле = custom1][kt|br]
	- [kt|b]custom2_required[/kt|b]: когда дополнительное поле 2 не заполнено, но настроено как обязательное [поле = custom2][kt|br]
	- [kt|b]custom3_required[/kt|b]: когда дополнительное поле 3 не заполнено, но настроено как обязательное [поле = custom3][kt|br]
	- [kt|b]custom4_required[/kt|b]: когда дополнительное поле 4 не заполнено, но настроено как обязательное [поле = custom4][kt|br]
	- [kt|b]custom5_required[/kt|b]: когда дополнительное поле 5 не заполнено, но настроено как обязательное [поле = custom5][kt|br]
	- [kt|b]custom6_required[/kt|b]: когда дополнительное поле 6 не заполнено, но настроено как обязательное [поле = custom6][kt|br]
	- [kt|b]custom7_required[/kt|b]: когда дополнительное поле 7 не заполнено, но настроено как обязательное [поле = custom7][kt|br]
	- [kt|b]custom8_required[/kt|b]: когда дополнительное поле 8 не заполнено, но настроено как обязательное [поле = custom8][kt|br]
	- [kt|b]custom9_required[/kt|b]: когда дополнительное поле 9 не заполнено, но настроено как обязательное [поле = custom9][kt|br]
	- [kt|b]custom10_required[/kt|b]: когда дополнительное поле 10 не заполнено, но настроено как обязательное [поле = custom10][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: когда в поле стоимости подписки на профиль (в токенах) указано не целочисленное число [поле = tokens_required][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Для формы изменения пароля:[kt|br]
	[kt|code]
	- [kt|b]old_pass_required[/kt|b]: когда поле текущего пароля не заполнено [поле = old_pass][kt|br]
	- [kt|b]old_pass_invalid[/kt|b]: когда введенный текущий пароль не подходит [поле = old_pass][kt|br]
	- [kt|b]pass_required[/kt|b]: когда поле нового пароля не заполнено [поле = pass][kt|br]
	- [kt|b]pass_minimum[/kt|b]: когда поле нового пароля имеет длину меньше допустимой (минимальную длину можно вывести через токен [kt|b]%1%[/kt|b]) [поле = pass][kt|br]
	- [kt|b]pass_blocked[/kt|b]: когда введенный новый пароль заблокирован и не может использоваться этим пользователем [поле = pass][kt|br]
	- [kt|b]pass2_required[/kt|b]: когда поле подтверждения пароля не заполнено [поле = pass2][kt|br]
	- [kt|b]pass2_invalid[/kt|b]: когда поле подтверждения пароля не совпадает с новым паролем [поле = pass2][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Для формы изменения email-а:[kt|br]
	[kt|code]
	- [kt|b]email_required[/kt|b]: когда поле email-а не заполнено [поле = email][kt|br]
	- [kt|b]email_invalid[/kt|b]: когда введенный email имеет неверный формат [поле = email][kt|br]
	- [kt|b]email_exists[/kt|b]: когда пользователь с таким email-ом уже зарегистрирован [поле = email][kt|br]
	- [kt|b]email_not_changed[/kt|b]: когда новый email является таким же, как и был [поле = email][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_email_templates']}[/kt|b]
	[kt|br][kt|br]

	Блок может отправлять email сообщения для подтверждения смены email адресов.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['member_profile_edit']['block_examples'] = "
	[kt|b]Показать форму изменения данных профиля[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму изменения пароля[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change_pass
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму изменения адреса email[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change_email
	[/kt|code]
";
