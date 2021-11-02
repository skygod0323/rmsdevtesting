<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_members_blog messages
// =====================================================================================================================

$lang['list_members_blog']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_members_blog']['groups']['display_modes']   = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_members_blog']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];

$lang['list_members_blog']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_members_blog']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_members_blog']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['list_members_blog']['params']['var_items_per_page']          = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_members_blog']['params']['mode_global']                 = "Enables global entries mode.";
$lang['list_members_blog']['params']['var_user_id']                 = "HTTP parameter, which provides ID of a user, whose blog entries should be displayed. If not enabled, block will display blog entries of the current member.";
$lang['list_members_blog']['params']['redirect_unknown_user_to']    = "Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own blog entries list (in most cases it should point to login page).";
$lang['list_members_blog']['params']['need_approve']                = "With this parameter enabled, new blog entries won't be displayed on website until they are approved by administrator.";
$lang['list_members_blog']['params']['allow_editing']               = "Allows users edit their entries.";

$lang['list_members_blog']['block_short_desc'] = "Displays list of member's blog posts with the given options";

$lang['list_members_blog']['block_desc'] = "
	Block displays list of member's blog entries (wall) with different filtering options. This block is
	a regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 2 different display modes for this block:[kt|br]
	1) Global blog entries list. In order to enable this mode you should use [kt|b]mode_global[/kt|b] block
	   parameter.[kt|br]
	2) User's blog entries list. If [kt|b]var_user_id[/kt|b] block parameter is enabled, block will display blog
	   entries list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
	   display blog entries list of the current user ('my blog entries'), and if the current user is not logged in -
	   user will be redirected to the URL specified in [kt|b]redirect_unknown_user_to[/kt|b] block parameter.
	[kt|br][kt|br]

	If you want to manually approve all new blog entries you need to enable [kt|b]need_approve[/kt|b]
	block parameter. In this case all new entries will require administrator approval before they
	are displayed on website.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block
	will not be cached when displaying blog entries list of the current user ('my blog entries').
";

$lang['list_members_blog']['block_examples'] = "
	[kt|b]Display blog entries of member with ID '169', 20 per page[/kt|b]
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
	{$config['project_url']}/page.php?user_id=169
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display last 10 blog entries of my own blog[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display last 10 global blog entries[/kt|b]
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
";

?>