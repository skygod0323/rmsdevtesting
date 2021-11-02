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
$lang['list_members_tokens']['params']['flow_group']                = "Static filtering by tokens flow group.";
$lang['list_members_tokens']['params']['var_flow_group']            = "URL parameter, which provides value for tokens flow group filter. One of the following values can be used: [kt|b]1[/kt|b] - spent tokens, [kt|b]2[/kt|b] - purchased tokens, [kt|b]3[/kt|b] - awards, [kt|b]4[/kt|b] - payouts and [kt|b]5[/kt|b] - donations. Overrides [kt|b]flow_group[/kt|b] parameter.";
$lang['list_members_tokens']['params']['var_date_from']             = "URL parameter, which provides start value for date interval filter. Value should have [kt|b]YYYY-MM-DD[/kt|b] format.";
$lang['list_members_tokens']['params']['var_date_to']               = "URL parameter, which provides end value for date interval filter. Value should have [kt|b]YYYY-MM-DD[/kt|b] format.";
$lang['list_members_tokens']['params']['var_payout_id']             = "URL parameter, which provides value for payout filter. Filters only awards that were paid out by the payout with the given ID. If [kt|b]0[/kt|b] is passed as a payout ID, will show awards that were not paid yet and will be paid in future.";
$lang['list_members_tokens']['params']['redirect_unknown_user_to']  = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['list_members_tokens']['values']['flow_group']['1'] = "Spent tokens";
$lang['list_members_tokens']['values']['flow_group']['2'] = "Purchased tokens";
$lang['list_members_tokens']['values']['flow_group']['3'] = "Awards";
$lang['list_members_tokens']['values']['flow_group']['4'] = "Payouts";
$lang['list_members_tokens']['values']['flow_group']['5'] = "Donations";

$lang['list_members_tokens']['values']['sort_by']['date']           = "Date";
$lang['list_members_tokens']['values']['sort_by']['tokens']         = "Tokens amount";
$lang['list_members_tokens']['values']['sort_by']['flow_group']     = "Flow group";
$lang['list_members_tokens']['values']['sort_by']['rand()']         = "Random";

$lang['list_members_tokens']['block_short_desc'] = "Displays list of member's token operations with the given options";

$lang['list_members_tokens']['block_desc'] = "
	Block displays list of all token transactions of the given member with different sorting and filtering options.
	This block is a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['list_members_tokens']['block_examples'] = "
	[kt|b]Display my token transactions 10 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = date desc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my all awards[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = date desc[kt|br]
	- flow_group = 3[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my token transactions from January, 1 2016 to January, 31 2016[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = date desc[kt|br]
	- var_date_from = date_from[kt|br]
	- var_date_to = date_to[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?date_from=2016-01-01&date_to=2016-01-31
	[/kt|code]
";
