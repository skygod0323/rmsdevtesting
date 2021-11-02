<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// post_edit messages
// =====================================================================================================================

$lang['post_edit']['groups']['new_objects']     = $lang['website_ui']['block_group_default_new_objects'];
$lang['post_edit']['groups']['edit_mode']       = $lang['website_ui']['block_group_default_edit_mode'];
$lang['post_edit']['groups']['validation']      = $lang['website_ui']['block_group_default_validation'];
$lang['post_edit']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['post_edit']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['post_edit']['params']['post_type']                   = "Все новые записи будут создаваться с этим типом. Укажите внешний ID типа записей.";
$lang['post_edit']['params']['allow_anonymous']             = "Разрешает незалогиненным пользователям создавать новые записи.";
$lang['post_edit']['params']['force_inactive']              = $lang['website_ui']['parameter_default_force_inactive'];
$lang['post_edit']['params']['var_post_id']                 = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['post_edit']['params']['forbid_change']               = $lang['website_ui']['parameter_default_forbid_change'];
$lang['post_edit']['params']['force_inactive_on_edit']      = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['post_edit']['params']['duplicates_allowed']          = "Допускаются ли записи с одинаковыми названиями.";
$lang['post_edit']['params']['optional_description']        = "Делает поле описания необязательным для заполнения.";
$lang['post_edit']['params']['optional_tags']               = "Делает поле тэгов необязательным для заполнения.";
$lang['post_edit']['params']['optional_categories']         = "Делает поле категорий необязательным для заполнения.";
$lang['post_edit']['params']['max_categories']              = "Задает максимальное кол-во выбранных одновременно категорий.";
$lang['post_edit']['params']['use_captcha']                 = "Включает использование визуальной защиты от авто-сабмита при создании новых записей.";
$lang['post_edit']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['post_edit']['params']['redirect_on_new_done']        = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";
$lang['post_edit']['params']['redirect_on_change_done']     = "[kt|b]Устарел![/kt|b] Этот параметр больше не используется при современной отправке форм через AJAX.";

$lang['post_edit']['block_short_desc'] = "Предоставляет функционал для создания и редактирования записей";

$lang['post_edit']['block_desc'] = "
	Блок отображает формы создания и редактирования для записей определенного типа. Вам следует использовать параметр
	[kt|b]post_type[/kt|b], чтобы указать тип записей для всех новых записей, которые будут создаваться в этом блоке.
	При необходимости разрешить пользователям создавать записи нескольких типов, вам следует использщовать разные блоки
	[kt|b]post_edit[/kt|b].
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]post_type_required[/kt|b]: когда параметр [kt|b]post_type[/kt|b] в настройках блока не заполнен[kt|br]
	- [kt|b]post_type_invalid[/kt|b]: когда параметр [kt|b]post_type[/kt|b] в настройках блока ссылается на несуществующий тип записей[kt|br]
	- [kt|b]title_required[/kt|b]: когда поле названия не заполнено [поле = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: когда длина поля названия менее 5 символов [поле = title][kt|br]
	- [kt|b]title_exists[/kt|b]: когда объект с таким названием уже существует [поле = title][kt|br]
	- [kt|b]description_required[/kt|b]: когда поле описания не заполнено [поле = description][kt|br]
	- [kt|b]content_required[/kt|b]: когда поле контента не заполнено [поле = content][kt|br]
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

$lang['post_edit']['block_examples'] = "
	[kt|b]Показать форму добавления записи с типом 'news'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- post_type = news[kt|br]
	- var_post_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать форму редактирования записи с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- post_type = news[kt|br]
	- var_post_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
