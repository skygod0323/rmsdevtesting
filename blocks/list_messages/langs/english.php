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
$lang['list_messages']['params']['folder']                      = "Folder to display.";
$lang['list_messages']['params']['var_folder']                  = "HTTP parameter, which provides message folder to display. One of the following folders can be used: [kt|b]inbox[/kt|b] - inbox folder, [kt|b]outbox[/kt|b] - sent messages folder, [kt|b]invites[/kt|b] - invites folder and [kt|b]unread[/kt|b] - unread messages folder. Overrides [kt|b]folder[/kt|b] value.";
$lang['list_messages']['params']['var_user_id']                 = "HTTP parameter, which provides user ID for displaying conversation with this user.";
$lang['list_messages']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['list_messages']['values']['folder']['inbox']     = "Inbox";
$lang['list_messages']['values']['folder']['outbox']    = "Sent messages";
$lang['list_messages']['values']['folder']['invites']   = "Friend invites";
$lang['list_messages']['values']['folder']['unread']    = "Unread messages";

$lang['list_messages']['block_short_desc'] = "Displays list of internal messages with the given options";

$lang['list_messages']['block_desc'] = "
	Block displays list of internal messages. Block provides ability to display 3 static
	folders (inbox, outbox and unread) and conversation with the given user. Allows users
	to confirm / reject friends invitations. This block is a regular list block with
	pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	In order to display different folders you should use [kt|b]var_folder[/kt|b] block parameter.
	This parameter should point to HTTP parameter that provides one of the following folder IDs:[kt|br]
	1) [kt|b]inbox[/kt|b] - for displaying incoming messages. This folder displays absolutely all
	   messages until they are removed by a user.[kt|br]
	2) [kt|b]outbox[/kt|b] - for displaying sent messages. This folder displays only messages, which
	   are manually sent be a user (e.g. system messages will not be displayed here), until they are
	   removed by a user.[kt|br]
	3) [kt|b]unread[/kt|b] - for displaying unread messages. This folder displays only unread messages
	   of all types at one page (pagination will not be used).
	[kt|br][kt|br]

	Unread messages will be marked as 'read' immediately after they are displayed in a list. Only
	friends invitations will remain unread until they are approved or rejected. After approve /
	reject action, these messages will be removed automatically.
	[kt|br][kt|br]

	In order to display conversation with any user you should use [kt|b]var_user_id[/kt|b] block
	parameter, which points to HTTP parameter with user ID. If user ID is provided, conversation
	with this user will be displayed.
	[kt|br][kt|br]

	Block is accessible for registered members only. If unregistered user tries to open a page with
	this block, he will be redirected to the URL specified in [kt|b]redirect_unknown_user_to[/kt|b]
	block parameter, which should point to login page in most cases.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	Block doesn't support caching.
";

$lang['list_messages']['block_examples'] = "
	[kt|b]Display 20 inbox messages per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=inbox
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 20 sent messages per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=outbox
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display unread messages[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_folder = folder[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?folder=unread
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display conversation with user with ID '276'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_user_id = user_id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=276
	[/kt|code]
";

?>