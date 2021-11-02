<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_view messages
// =====================================================================================================================

$lang['member_profile_view']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['member_profile_view']['groups']['additional_data']   = $lang['website_ui']['block_group_default_additional_data'];

$lang['member_profile_view']['params']['var_user_id']                   = "Параметр URL-а, в котором передается ID пользователя.";
$lang['member_profile_view']['params']['show_next_and_previous_info']   = "Включает выборку информации о следующем и предыдущем пользователях по ID.";

$lang['member_profile_view']['block_short_desc'] = "Выводит всю информацию о каком-либо пользователе";

$lang['member_profile_view']['block_desc'] = "
	Блок предназначен для отображения информации по заданному пользователю (контекстному объекту), а также предоставляет
	следующую функциональность:
	[kt|br][kt|br]

	- Возможность подписаться на пользователя (только для залогиненных).[kt|br]
	- Отправка сообщения внутренней почты пользователю (только для залогиненных).[kt|br]
	- Добавление пользователя в друзья (только для залогиненных).[kt|br]
	- Удаление пользователя из друзей (только для залогиненных).[kt|br]
	- Подтверждение или отклонение запроса пользователя на добавления в друзья (только для залогиненных).[kt|br]
	- Добавление пользователя в черный список (только для залогиненных).[kt|br]
	- Удаление пользователя из черного списка (только для залогиненных).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['member_profile_view']['block_examples'] = "
	[kt|b]Показать пользователя с ID '18'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=18
	[/kt|code]
";
