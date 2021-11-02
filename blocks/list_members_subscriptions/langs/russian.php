<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members_subscriptions messages
// =====================================================================================================================

$lang['list_members_subscriptions']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members_subscriptions']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_members_subscriptions']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_members_subscriptions']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_members_subscriptions']['groups']['display_modes']      = $lang['website_ui']['block_group_default_display_modes'];

$lang['list_members_subscriptions']['params']['items_per_page']             = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members_subscriptions']['params']['links_per_page']             = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members_subscriptions']['params']['var_from']                   = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members_subscriptions']['params']['var_items_per_page']         = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members_subscriptions']['params']['sort_by']                    = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_members_subscriptions']['params']['var_sort_by']                = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_members_subscriptions']['params']['subscribed_type']            = "Позволяет фильтровать подписки по различному типу.";
$lang['list_members_subscriptions']['params']['var_subscribed_type']        = "Позволяет фильтровать подписки по различному типу базируясь на значении в данном HTTP параметре. Набор передаваемых значений: [kt|b]1[/kt|b] - пользователи, [kt|b]3[/kt|b] - контент провайдеры, [kt|b]4[/kt|b] - модели, [kt|b]5[/kt|b] - DVD / каналы, [kt|b]13[/kt|b] - плэйлисты. Перекрывает параметр [kt|b]subscribed_type[/kt|b].";
$lang['list_members_subscriptions']['params']['mode_purchased']             = "Включает режим купленных подписок. Выводит подписки, которые были куплены пользователем за токены.";
$lang['list_members_subscriptions']['params']['var_user_id']                = "HTTP параметр, в котором передается ID пользователя, чьи подписки должны быть выведены. Если не задан, то выводятся подписки текущего пользователя.";
$lang['list_members_subscriptions']['params']['redirect_unknown_user_to']   = "Указывает путь, на который будет перенаправлен незалогиненный пользователь при попытке доступа к своим личным подпискам (в большинстве случаев это путь на страницу с формой логина).";

$lang['list_members_subscriptions']['values']['subscribed_type']['1']   = "Пользователи";
$lang['list_members_subscriptions']['values']['subscribed_type']['3']   = "Контент провайдеры";
$lang['list_members_subscriptions']['values']['subscribed_type']['4']   = "Модели";
$lang['list_members_subscriptions']['values']['subscribed_type']['5']   = "DVD / Каналы";
$lang['list_members_subscriptions']['values']['subscribed_type']['13']  = "Плэйлисты";

$lang['list_members_subscriptions']['values']['sort_by']['subscription_id'] = "ID подписки";
$lang['list_members_subscriptions']['values']['sort_by']['added_date']      = "Дата создания";
$lang['list_members_subscriptions']['values']['sort_by']['rand()']          = "Случайно";

$lang['list_members_subscriptions']['block_short_desc'] = "Выводит список подписок пользователей с заданными опциями";

$lang['list_members_subscriptions']['block_desc'] = "
	Блок предназначен для отображения списка подписок пользователей с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить пагинацию.
	[kt|br][kt|br]

	[kt|b]Опции отображения и логика[/kt|b]
	[kt|br][kt|br]

	Если включен параметр блока [kt|b]var_user_id[/kt|b], то блок отобразит список подписок пользователя, ID которого
	передается в соответствующем HTTP параметре. Если параметр блока [kt|b]var_user_id[/kt|b] не указан, то блок
	попытается вывести список подписок текущего пользователя ('мои подписки'), а если он не залогинен, то
	перенаправит его по пути, указанному в параметре блока [kt|b]redirect_unknown_user_to[/kt|b]. В данном режиме
	списка существует также возможность удаления своих подписок.
	[kt|br][kt|br]

	[kt|b]Кэширование[/kt|b]
	[kt|br][kt|br]

	Блок может быть закэширован на длительный промежуток времени. Для всех пользователей будет использоваться одна и та
	же версия кэша. Блок не кэшируется, когда отображает список своих подписок для текущего пользователя.
";

$lang['list_members_subscriptions']['block_examples'] = "
	[kt|b]Показать все подписки пользователя с ID '287'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать свои подписки по 10 на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
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