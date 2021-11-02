<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members_tokens messages
// =====================================================================================================================

$lang['list_members_tokens']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members_tokens']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_members_tokens']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_members_tokens']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_members_tokens']['groups']['navigation']        = $lang['website_ui']['block_group_default_navigation'];

$lang['list_members_tokens']['params']['items_per_page']            = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members_tokens']['params']['links_per_page']            = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members_tokens']['params']['var_from']                  = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members_tokens']['params']['var_items_per_page']        = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members_tokens']['params']['sort_by']                   = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_members_tokens']['params']['var_sort_by']               = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_members_tokens']['params']['flow_group']                = "Статический фильтр по группе записей.";
$lang['list_members_tokens']['params']['var_flow_group']            = "HTTP параметр, в котором передается значение фильтра по группе записей. В данном параметре может быть передано одно из следующих значений: [kt|b]1[/kt|b] - траты токенов, [kt|b]2[/kt|b] - покупки токенов, [kt|b]3[/kt|b] - награды, [kt|b]4[/kt|b] - выплаты и [kt|b]5[/kt|b] - подарки. Перекрывает параметр [kt|b]flow_group[/kt|b].";
$lang['list_members_tokens']['params']['var_date_from']             = "HTTP параметр, в котором передается значение начала интервала для фильтра по интервалу времени. Значение должно иметь формат [kt|b]YYYY-MM-DD[/kt|b].";
$lang['list_members_tokens']['params']['var_date_to']               = "HTTP параметр, в котором передается значение конца интервала для фильтра по интервалу времени. Значение должно иметь формат [kt|b]YYYY-MM-DD[/kt|b]";
$lang['list_members_tokens']['params']['var_payout_id']             = "HTTP параметр, в котором передается значение ID выплаты для фильтра по выплате. Выводит только награды, которые были выплачены выплатой c указанным ID. Если фильтру передать значение [kt|b]0[/kt|b], то блок выведет награды, которые еще не выплачены.";
$lang['list_members_tokens']['params']['redirect_unknown_user_to']  = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['list_members_tokens']['values']['flow_group']['1'] = "Траты токенов";
$lang['list_members_tokens']['values']['flow_group']['2'] = "Покупки токенов";
$lang['list_members_tokens']['values']['flow_group']['3'] = "Награды";
$lang['list_members_tokens']['values']['flow_group']['4'] = "Выплаты";
$lang['list_members_tokens']['values']['flow_group']['5'] = "Подарки";

$lang['list_members_tokens']['values']['sort_by']['date']           = "Дата";
$lang['list_members_tokens']['values']['sort_by']['tokens']         = "Кол-во токенов";
$lang['list_members_tokens']['values']['sort_by']['flow_group']     = "Группа записей";
$lang['list_members_tokens']['values']['sort_by']['rand()']         = "Случайно";

$lang['list_members_tokens']['block_short_desc'] = "Выводит движение токенов по счету пользователя";

$lang['list_members_tokens']['block_desc'] = "
	Блок предназначен для отображения движения токенов пользователя с различными опциями сортировки и фильтрации.
	Является стандартным блоком листинга, для которого можно включить
	пагинацию.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['list_members_tokens']['block_examples'] = "
	[kt|b]Показать движение моих токенов по 10 записей на странице[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = date desc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать все мои награды[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = date desc[kt|br]
	- flow_group = 3[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Показать движение моих токенов с 1 января 2016 по 31 января 2016[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = date desc[kt|br]
	- var_date_from = date_from[kt|br]
	- var_date_to = date_to[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?date_from=2016-01-01&date_to=2016-01-31
	[/kt|code]
";
