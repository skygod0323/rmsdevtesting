<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// model_comments messages
// =====================================================================================================================

$lang['model_comments']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['model_comments']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['model_comments']['groups']['object_context'] = $lang['website_ui']['block_group_default_context_object'];
$lang['model_comments']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['model_comments']['groups']['i18n']           = $lang['website_ui']['block_group_default_i18n'];

$lang['model_comments']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['model_comments']['params']['links_per_page']     = $lang['website_ui']['parameter_default_links_per_page'];
$lang['model_comments']['params']['var_from']           = $lang['website_ui']['parameter_default_var_from'];
$lang['model_comments']['params']['sort_by']            = $lang['website_ui']['parameter_default_sort_by'];
$lang['model_comments']['params']['var_model_dir']      = "Параметр URL-а, в котором передается директория модели.";
$lang['model_comments']['params']['var_model_id']       = "Параметр URL-а, в котором передается ID модели. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['model_comments']['params']['min_length']         = "Ограничивает минимально разрешенную длину комментария.";
$lang['model_comments']['params']['need_approve']       = "Настраивает, требуется ли администратору утверждать комментарии для того, чтобы они появились на сайте.";
$lang['model_comments']['params']['allow_anonymous']    = "Устанавливает, могут ли анонимные пользователи оставлять комментарии.";
$lang['model_comments']['params']['use_captcha']        = "Настраивает использование защиты от автоматической отправки комментариев.";
$lang['model_comments']['params']['allow_editing']      = "Разрешает пользователям редактировать свои комментарии.";
$lang['model_comments']['params']['match_locale']       = "При включенном параметре блок выведет только комментарии, созданные на текущей локали KVS.";

$lang['model_comments']['values']['use_captcha']['1']       = "Включить только для анонимов";
$lang['model_comments']['values']['use_captcha']['2']       = "Включить для всех пользователей";
$lang['model_comments']['values']['need_approve']['1']      = "Утверждать только комментарии от анонимов";
$lang['model_comments']['values']['need_approve']['2']      = "Утверждать все комментарии";
$lang['model_comments']['values']['sort_by']['comment_id']  = "ID комментария";
$lang['model_comments']['values']['sort_by']['user_id']     = "ID пользователя";
$lang['model_comments']['values']['sort_by']['comment']     = "Текст комментария";
$lang['model_comments']['values']['sort_by']['rating']      = "Рейтинг";
$lang['model_comments']['values']['sort_by']['added_date']  = "Дата создания";
$lang['model_comments']['values']['sort_by']['rand()']      = "Случайно";

$lang['model_comments']['block_short_desc'] = "Предоставляет функционал для комментариев по моделям";

$lang['model_comments']['block_desc'] = "
	Блок предназначен для отображения списка комментариев к заданной модели (контекстному объекту), а также
	предоставляет возможность добавления новых комментариев. Является стандартным блоком листинга, для которого можно
	включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['model_comments']['groups']['functionality']}[/kt|b]
	[kt|br][kt|br]

	Настройки функциональности позволяют настроить, какие пользователи могут добавлять комментарии, в каких
	случаях показывать визуальную защиту, и другое.
	[kt|br][kt|br]

	Если вы хотите вручную проверять каждый новый комментарий прежде чем он появится на сайте, вам следует
	включить параметр блока [kt|b]need_approve[/kt|b]. В этом случае все новые комментарии будут требовать утверждения
	администратором, прежде чем они появятся на сайте. Если параметр блока [kt|b]need_approve[/kt|b] выключен, то
	комментарии будут появляться на сайте сразу, но они все равно будут попадать в список на проверку, чтобы
	администратор мог видеть все новые комментарии и, при необходимости, администрировать их.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_specific']}
";

$lang['model_comments']['block_examples'] = "
	[kt|b]Показать все комментарии к модели с директорией 'my_model'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_model_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_model
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 8 комментариев на страницу к модели с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_model_dir = dir[kt|br]
	- var_model_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
