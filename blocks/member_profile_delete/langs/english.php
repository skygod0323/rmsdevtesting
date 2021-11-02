<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_delete messages
// =====================================================================================================================

$lang['member_profile_delete']['groups']['validation']  = $lang['website_ui']['block_group_default_validation'];
$lang['member_profile_delete']['groups']['navigation']  = $lang['website_ui']['block_group_default_navigation'];

$lang['member_profile_delete']['params']['require_reason']           = "Makes reason field required.";
$lang['member_profile_delete']['params']['redirect_unknown_user_to'] = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['member_profile_delete']['block_short_desc'] = "Provides members profile delete functionality";

$lang['member_profile_delete']['block_desc'] = "
	Block allows members to request that their profiles are deleted.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]reason_required[/kt|b]: when reason field is empty, but configured as required [field = reason][kt|br]
	- [kt|b]confirm_delete_required[/kt|b]: when confirmation checkbox is not checked [field = confirm_delete][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['member_profile_delete']['block_examples'] = "
	[kt|b]Display profile delete form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
