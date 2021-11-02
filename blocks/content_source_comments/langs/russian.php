<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_comments messages
// =====================================================================================================================

$lang['content_source_comments']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['content_source_comments']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['content_source_comments']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['content_source_comments']['groups']['functionality']     = $lang['website_ui']['block_group_default_functionality'];
$lang['content_source_comments']['groups']['i18n']              = $lang['website_ui']['block_group_default_i18n'];

$lang['content_source_comments']['params']['items_per_page']            = $lang['website_ui']['parameter_default_items_per_page'];
$lang['content_source_comments']['params']['links_per_page']            = $lang['website_ui']['parameter_default_links_per_page'];
$lang['content_source_comments']['params']['var_from']                  = $lang['website_ui']['parameter_default_var_from'];
$lang['content_source_comments']['params']['sort_by']                   = $lang['website_ui']['parameter_default_sort_by'];
$lang['content_source_comments']['params']['var_content_source_dir']    = "Параметр URL-а, в котором передается директория контент провайдера.";
$lang['content_source_comments']['params']['var_content_source_id']     = "Параметр URL-а, в котором передается ID контент провайдера. Если указан и содержит значение, то имеет приоритет над передаваемой директорией.";
$lang['content_source_comments']['params']['min_length']                = "Ограничивает минимально разрешенную длину комментария.";
$lang['content_source_comments']['params']['need_approve']              = "Настраивает, требуется ли администратору утверждать комментарии для того, чтобы они появились на сайте.";
$lang['content_source_comments']['params']['allow_anonymous']           = "Устанавливает, могут ли анонимные пользователи оставлять комментарии.";
$lang['content_source_comments']['params']['use_captcha']               = "Настраивает использование защиты от автоматической отправки комментариев.";
$lang['content_source_comments']['params']['allow_editing']             = "Разрешает пользователям редактировать свои комментарии.";
$lang['content_source_comments']['params']['match_locale']              = "При включенном параметре блок выведет только комментарии, созданные на текущей локали KVS.";

$lang['content_source_comments']['values']['use_captcha']['1']      = "Включить только для анонимов";
$lang['content_source_comments']['values']['use_captcha']['2']      = "Включить для всех пользователей";
$lang['content_source_comments']['values']['need_approve']['1']     = "Утверждать только комментарии от анонимов";
$lang['content_source_comments']['values']['need_approve']['2']     = "Утверждать все комментарии";
$lang['content_source_comments']['values']['sort_by']['comment_id'] = "ID комментария";
$lang['content_source_comments']['values']['sort_by']['user_id']    = "ID пользователя";
$lang['content_source_comments']['values']['sort_by']['comment']    = "Текст комментария";
$lang['content_source_comments']['values']['sort_by']['rating']     = "Рейтинг";
$lang['content_source_comments']['values']['sort_by']['added_date'] = "Дата создания";
$lang['content_source_comments']['values']['sort_by']['rand()']     = "Случайно";

$lang['content_source_comments']['block_short_desc'] = "Предоставляет функционал для комментариев по контент провайдерам";

$lang['content_source_comments']['block_desc'] = "
	Блок предназначен для отображения списка комментариев к заданному контент провайдеру (контекстному объекту), а также
	предоставляет возможность добавления новых комментариев. Является стандартным блоком листинга, для которого можно
	включить пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['content_source_comments']['groups']['functionality']}[/kt|b]
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

$lang['content_source_comments']['block_examples'] = "
	[kt|b]Показать все комментарии к контент провайдеру с директорией 'my_content_source'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_content_source_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 8 комментариев на страницу к контент провайдеру с ID '11'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_content_source_dir = dir[kt|br]
	- var_content_source_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
