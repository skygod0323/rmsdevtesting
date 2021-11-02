<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_messages messages
// =====================================================================================================================

$lang['list_messages']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_messages']['groups']['static_filters']  = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_messages']['groups']['dynamic_filters'] = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_messages']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['list_messages']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_messages']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_messages']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['list_messages']['params']['var_items_per_page']          = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_messages']['params']['folder']                      = "Папка сообщений для отображения.";
$lang['list_messages']['params']['var_folder']                  = "HTTP параметр, в котором передается папка сообщений для отображения. В данном параметре может быть передано одно из следующих значений: [kt|b]inbox[/kt|b] - папка входящих сообщений, [kt|b]outbox[/kt|b] - папка отправленных сообщений, [kt|b]invites[/kt|b] - папка приглашений в друзья и [kt|b]unread[/kt|b] - папка непрочитанных сообщений. Перекрывает значение [kt|b]folder[/kt|b].";
$lang['list_messages']['params']['var_user_id']                 = "HTTP параметр, в котором передается ID пользователя для отображения переписки с данным пользователем.";
$lang['list_messages']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['list_messages']['values']['folder']['inbox']     = "Входящие";
$lang['list_messages']['values']['folder']['outbox']    = "Исходящие";
$lang['list_messages']['values']['folder']['invites']   = "Приглашения в друзья";
$lang['list_messages']['values']['folder']['unread']    = "Непрочитанные";

$lang['list_messages']['block_short_desc'] = "Выводит список сообщений внутренней почты";

$lang['list_messages']['block_desc'] = "
	Блок предназначен для отображения списка сообщений внутренней почты. Позволяет вывести
	3 статические папки сообщений (входящие, отправленные и непрочтенные), а также переписку
	с выбранным пользователем. Предоставляет возможность подтверждать / отклонять запросы на
	добавление в друзья. Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Для отображения различных папок сообщений вам нужно использовать параметр блока [kt|b]var_folder[/kt|b].
	Он должен ссылаться на HTTP параметр, в котором передается один из следующих ID папки:[kt|br]
	1) [kt|b]inbox[/kt|b] - для отображения списка входящих сообщений. В данной папке отображаются
	   абсолютно все сообщения, до тех пор пока они не удалены пользователем.[kt|br]
	2) [kt|b]outbox[/kt|b] - для отображения списка отправленных сообщений. В данной папке отображаются
	   только сообщения, написанные пользователем вручную (т.е. системные не отображаются), до тех пор
	   пока они не удалены пользователем.[kt|br]
	3) [kt|b]unread[/kt|b] - для отображения списка непрочитанных сообщений. В данной папке отображаются
	   только непрочитанные сообщения всех типов на одной странице (пагинация не будет использоваться).
	[kt|br][kt|br]

	Непрочитанные сообщения становятся прочитанными сразу после отображения их в списке. Исключение
	составляют запросы на добавления в друзья, которые остаются непрочитанными до тех пор, пока
	пользователь не подтвердит или не отклонит запрос. После подтверждения / отклонения запросов
	на добавление в друзья данные сообщения удаляются.
	[kt|br][kt|br]

	Для того, чтобы отобразить переписку с каким-либо пользователем вам следует воспользоваться
	параметром блока [kt|b]var_user_id[/kt|b], который ссылается на HTTP параметр с ID пользователя.
	Если ID пользователя передано, блок отобразит переписку с данным пользователем.
	[kt|br][kt|br]

	Блок доступен только для залогиненных пользователей. Если незалогиненный пользователь пытается
	открыть страницу с этим блоком, он будет перенаправлен на путь, указанный в параметре блока
	[kt|b]redirect_unknown_user_to[/kt|b], который в большинстве случаев должен вести на страницу
	с блоком логина.
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок не поддерживает кэширование.
";

$lang['list_messages']['block_examples'] = "
	[kt|b]Показать папку входящих сообщений по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=inbox
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать папку отправленных сообщений по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=outbox
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать папку непрочитанных сообщений[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=unread
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать переписку с пользователем с ID '276'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_user_id = user_id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=276
	[/kt|code]
";

?>