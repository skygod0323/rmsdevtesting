<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members_events messages
// =====================================================================================================================

$lang['list_members_events']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members_events']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_members_events']['groups']['display_modes']     = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_members_events']['groups']['pull_content']      = "Select content object for each event";

$lang['list_members_events']['params']['items_per_page']            = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members_events']['params']['links_per_page']            = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members_events']['params']['var_from']                  = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members_events']['params']['var_items_per_page']        = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members_events']['params']['mode_global']               = "Enables global events mode.";
$lang['list_members_events']['params']['mode_friends']              = "Enables friends events display mode.";
$lang['list_members_events']['params']['skip_users']                = "Can be used with [kt|b]mode_global[/kt|b] only. If specified, events of these users will not be displayed (comma separated list of user IDs).";
$lang['list_members_events']['params']['show_users']                = "Can be used with [kt|b]mode_global[/kt|b] only. If specified, only events of these users will be displayed (comma separated list of user IDs).";
$lang['list_members_events']['params']['var_user_id']               = "HTTP parameter, which provides ID of a user, whose events list should be displayed. If not enabled, block will display events / friends events of the current member.";
$lang['list_members_events']['params']['redirect_unknown_user_to']  = "Specifies redirect URL for the visitors that are not logged in and are attempting to access display mode available for members only.";
$lang['list_members_events']['params']['pull_content']              = "Enables ability to display information about object, related to every event (e.g. video, album and other). Using this parameter will decrease overall block performance.";
$lang['list_members_events']['params']['match_locale']              = "If this parameter is enabled, block will show only events happened with the current KVS locale.";

$lang['list_members_events']['block_short_desc'] = "Displays list of member's events with the given options";

$lang['list_members_events']['block_desc'] = "
	Block displays list of member's events with different filtering options. This block is
	a regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 3 different display modes for this block:[kt|br]
	1) Global events list. In order to enable this mode you should use [kt|b]mode_global[/kt|b] block parameter.[kt|br]
	2) User's events list. If [kt|b]var_user_id[/kt|b] block parameter is enabled, block will display events list of
	   the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display events
	   list of the current user ('my events'), and if the current user is not logged in - user will be redirected to
	   the URL specified in [kt|b]redirect_unknown_user_to[/kt|b] block parameter.[kt|br]
	3) Friends activity list. In order to use this mode, you should enable [kt|b]mode_friends[/kt|b] block parameter.
	   If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display friends activity list of the
	   user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display friends
	   activity list of the current user ('my friends events'), and if the current user is not logged in - user will be
	   redirected to the URL specified in [kt|b]redirect_unknown_user_to[/kt|b] block parameter.
	[kt|br][kt|br]

	For global events list you can exclude events of any particular users by specifying their IDs in
	[kt|b]skip_users[/kt|b] block parameter. You can also show only events of specific users by using similar
	block parameter [kt|b]show_users[/kt|b].
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying events / friends events of the current user ('my events' / 'my friends events').
";

$lang['list_members_events']['block_examples'] = "
	[kt|b]Display events of member with ID '87', 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=87
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display last 10 global events[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- mode_global[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display last 15 of my friends activity events[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";

?>