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
$lang['list_comments']['params']['comments_on']                 = "Specifies which comments should be displayed.";
$lang['list_comments']['params']['mode_global']                 = "Enables global comments mode.";
$lang['list_comments']['params']['mode_content']                = "Enables user's content comments mode.";
$lang['list_comments']['params']['var_user_id']                 = "HTTP parameter, which provides ID of a user, whose posted comments list should be displayed. If not enabled, block will display comments list of the current member.";
$lang['list_comments']['params']['redirect_unknown_user_to']    = "Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own posted comments list (in most cases it should point to login page).";
$lang['list_comments']['params']['match_locale']                = "If this parameter is enabled, block will show only comments posted with the current KVS locale.";

$lang['list_comments']['values']['comments_on']['1']   = "Only videos";
$lang['list_comments']['values']['comments_on']['2']   = "Only albums";
$lang['list_comments']['values']['comments_on']['3']   = "Only content sources";
$lang['list_comments']['values']['comments_on']['4']   = "Only models";
$lang['list_comments']['values']['comments_on']['5']   = "Only DVDs / channels";

$lang['list_comments']['values']['sort_by']['comment_id']   = "Comment ID";
$lang['list_comments']['values']['sort_by']['added_date']   = "Date added";
$lang['list_comments']['values']['sort_by']['rating']       = "Rating";
$lang['list_comments']['values']['sort_by']['rand()']       = "Random";

$lang['list_comments']['block_short_desc'] = "Displays list of comments with the given options";

$lang['list_comments']['block_desc'] = "
	Block displays list of comments with different filtering options. This block is a regular list block
	with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 3 different display modes for this block:[kt|br]
	1) Global comments list. To enable this mode you should use [kt|b]mode_global[/kt|b] block parameter.[kt|br]
	2) User's content comments list. To enable this mode you should use [kt|b]mode_content[/kt|b] block parameter. If
	[kt|b]var_user_id[/kt|b] block parameter is enabled, block will display comments list for all content created by
	the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display comments list
	for all content created by the current user ('my content comments'), and if the current user is not logged in -
	user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.
	[kt|br]
	3) User's comments list. If [kt|b]var_user_id[/kt|b] block parameter is enabled, block will display comments list
	of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display comments
	list of the current user ('my comments'), and if the current user is not logged in - user will be redirected to the
	URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter.
	[kt|br][kt|br]

	Comments on all objects are rendered by default (videos, albums, content sources, models, DVDs / channels and
	playlists). In order to show only comments on specific objects use [kt|b]comments_on[/kt|b] block parameter.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying comments of the current user ('my comments').
";

$lang['list_comments']['block_examples'] = "
	[kt|b]Display comments of member with ID '87', 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=87
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display last 10 global comments[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = added_date desc[kt|br]
	- mode_global[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my last 15 comments[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = added_date desc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";

?>