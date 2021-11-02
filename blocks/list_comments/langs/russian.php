<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_comments messages
// =====================================================================================================================

$lang['list_comments']['groups']['pagination']          = $lang['website_ui']['block_group_default_pagination'];
$lang['list_comments']['groups']['sorting']             = $lang['website_ui']['block_group_default_sorting'];
$lang['list_comments']['groups']['static_filters']      = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_comments']['groups']['dynamic_filters']     = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_comments']['groups']['display_modes']       = $lang['website_ui']['block_group_default_display_modes'];

$lang['list_comments']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_comments']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_comments']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['list_comments']['params']['var_items_per_page']          = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_comments']['params']['sort_by']                     = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_comments']['params']['var_sort_by']                 = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_comments']['params']['comments_on']                 = "Указывает какие комментарии будут выведены.";
$lang['list_comments']['params']['mode_global']                 = "Включает режим отображения глобальных комментариев.";
$lang['list_comments']['params']['mode_content']                = "Включает режим отображения комментариев к контенту пользователя.";
$lang['list_comments']['params']['var_user_id']                 = "HTTP параметр, в котором передается ID пользователя, чей список комментариев должен быть выведен. Если не задан, то выводится список комментариев текущего пользователя.";
$lang['list_comments']['params']['redirect_unknown_user_to']    = "Указывает путь, на который будет перенаправлен незалогиненный пользователь при попытке доступа к списку своих комментариев (в большинстве случаев это путь на страницу с формой логина).";
$lang['list_comments']['params']['match_locale']                = "При включенном параметре блок выведет только комментарии, созданные на текущей локали KVS.";

$lang['list_comments']['values']['comments_on']['1']   = "Только по видео";
$lang['list_comments']['values']['comments_on']['2']   = "Только по фотоальбомам";
$lang['list_comments']['values']['comments_on']['3']   = "Только по контент пров.";
$lang['list_comments']['values']['comments_on']['4']   = "Только по моделям";
$lang['list_comments']['values']['comments_on']['5']   = "Только по DVD / каналам";

$lang['list_comments']['values']['sort_by']['comment_id']   = "ID комментария";
$lang['list_comments']['values']['sort_by']['added_date']   = "Дата добавления";
$lang['list_comments']['values']['sort_by']['rating']       = "Рейтинг";
$lang['list_comments']['values']['sort_by']['rand()']       = "Случайно";

$lang['list_comments']['block_short_desc'] = "Выводит список комментариев с заданными опциями";

$lang['list_comments']['block_desc'] = "
	Блок предназначен для отображения списка комментариев с различными опциями фильтрации. Является
	стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Существует 3 различных типа листинга комментариев:[kt|br]
	1) Список глобальных комментариев. Для получения данного списка должен быть включен параметр блока
	   [kt|b]mode_global[/kt|b].[kt|br]
	2) Список комментариев к контенту пользователя. Для получения данного списка должен быть включен параметр блока
	   [kt|b]mode_content[/kt|b]. Если указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит комментарии к
	   контенту пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести комментарии к контенту текущего пользователя
	   ('комментарии к моему контенту'), а если он не залогинен, то перенаправит его по пути, указанному в параметре
	   блока [kt|b]redirect_unknown_user_to[/kt|b].[kt|br]
	3) Список комментариев пользователя. Если указан параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит
	   комментарии пользователя, ID которого передается в соответствующем HTTP параметре. Если параметр блока
	   [kt|b]var_user_id[/kt|b] не указан, то блок попытается вывести комментарии текущего пользователя
	   ('мои комментарии'), а если он не залогинен, то перенаправит его по пути, указанному в параметре блока
	   [kt|b]redirect_unknown_user_to[/kt|b].
	[kt|br][kt|br]

	По умолчанию блок выводит комментарии по всем объектам (видео, фотоальбомы, контент провайдеры, модели, плэйлисты
	и DVD / каналы). Для отображения комментариев только по отдельным объектам, вам следует воспользоваться параметром
	блока [kt|b]comments_on[/kt|b].
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и
	та же версия кэша. Блок не кэшируется, когда отображает список комментариев текущего пользователя.
";

$lang['list_comments']['block_examples'] = "
	[kt|b]Показать комментарии пользователя с ID '87' по 20 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=87
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать 10 последних комментариев на сайте[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = added_date desc[kt|br]
	- mode_global[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать мои 15 последних комментариев[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = added_date desc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";

?>