<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// message_details messages
// =====================================================================================================================

$lang['message_details']['groups']['context_object']    = $lang['website_ui']['block_group_default_context_object'];
$lang['message_details']['groups']['navigation']        = $lang['website_ui']['block_group_default_navigation'];

$lang['message_details']['params']['var_message_id']            = "Параметр URL-а, в котором передается ID сообщения.";
$lang['message_details']['params']['redirect_unknown_user_to']  = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['message_details']['block_short_desc'] = "Предоставляет функционал для просмотра сообщений личной почты";

$lang['message_details']['block_desc'] = "
	Блок предназначен для отображения сообщения внутренней почты и для ответа на него. В настоящее время блок устарел и
	не должен использоваться современными темами, поскольку блок [kt|b]list_messages[/kt|b] предоставляет всю ту же
	функциональность в более современном виде.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['message_details']['block_examples'] = "
	[kt|b]Показать сообщение с ID '23'[/kt|b]
	[kt|br][kt|br]

	Параметры блока:[kt|br]
	[kt|code]
	- var_message_id = message_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Ссылка на страницу:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?message_id=23
	[/kt|code]
";
