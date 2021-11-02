<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// message_details messages
// =====================================================================================================================

$lang['message_details']['groups']['context_object']    = $lang['website_ui']['block_group_default_context_object'];
$lang['message_details']['groups']['navigation']        = $lang['website_ui']['block_group_default_navigation'];

$lang['message_details']['params']['var_message_id']            = "URL parameter, which provides message ID.";
$lang['message_details']['params']['redirect_unknown_user_to']  = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['message_details']['block_short_desc'] = "Provides internal message display and response functionality";

$lang['message_details']['block_desc'] = "
	Block displays internal message and provides ability to reply to it. This block should not be used in modern themes
	as the same functionality is provided by [kt|b]list_messages[/kt|b] block in more natural way.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['message_details']['block_examples'] = "
	[kt|b]Show message with ID '23'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_message_id = message_id
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?message_id=23
	[/kt|code]
";
