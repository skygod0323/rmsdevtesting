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
$lang['list_members_subscriptions']['params']['subscribed_type']            = "If specified, only subscriptions of this type will be displayed.";
$lang['list_members_subscriptions']['params']['var_subscribed_type']        = "HTTP parameter, which specifies subscriptions of which type should be displayed. The following values can be passed in the given HTTP parameter: [kt|b]1[/kt|b] - users, [kt|b]3[/kt|b] - content sources, [kt|b]4[/kt|b] - models, [kt|b]5[/kt|b] - DVDs / channels, [kt|b]13[/kt|b] - playlists. Overrides [kt|b]subscribed_type[/kt|b] parameter.";
$lang['list_members_subscriptions']['params']['mode_purchased']             = "Enables purchased subscriptions mode. Shows subscriptions that were purchased by the user with tokens.";
$lang['list_members_subscriptions']['params']['var_user_id']                = "HTTP parameter, which provides ID of a user, whose subscriptions should be displayed. If not enabled, block will display subscriptions of the current member.";
$lang['list_members_subscriptions']['params']['redirect_unknown_user_to']   = "Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own subscriptions (in most cases it should point to login page).";

$lang['list_members_subscriptions']['values']['subscribed_type']['1']   = "Users";
$lang['list_members_subscriptions']['values']['subscribed_type']['3']   = "Content sources";
$lang['list_members_subscriptions']['values']['subscribed_type']['4']   = "Models";
$lang['list_members_subscriptions']['values']['subscribed_type']['5']   = "DVDs / Channels";
$lang['list_members_subscriptions']['values']['subscribed_type']['13']  = "Playlists";

$lang['list_members_subscriptions']['values']['sort_by']['subscription_id'] = "Subscription ID";
$lang['list_members_subscriptions']['values']['sort_by']['added_date']      = "Creation date";
$lang['list_members_subscriptions']['values']['sort_by']['rand()']          = "Random";

$lang['list_members_subscriptions']['block_short_desc'] = "Displays list of member's subscriptions with the given options";

$lang['list_members_subscriptions']['block_desc'] = "
	Block displays list of member's subscriptions with different sorting and filtering options. This block is a regular
	list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	If [kt|b]var_user_id[/kt|b] block parameter is enabled, block will display subscriptions of the user, whose ID is
	passed in the related HTTP parameter. Otherwise block will first try to display subscriptions of the current user
	('my subscriptions'), and if the current user is not logged in - user will be redirected to the URL specified in
	the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying subscriptions of the current user
	('my subscriptions'), block will also provide ability for removing subscriptions.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying subscriptions of the current user ('my subscriptions').
";

$lang['list_members_subscriptions']['block_examples'] = "
	[kt|b]Display all subscriptions of the user with ID '287'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my own subscriptions 10 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
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