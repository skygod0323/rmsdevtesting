<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_view messages
// =====================================================================================================================

$lang['member_profile_view']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['member_profile_view']['groups']['additional_data']   = $lang['website_ui']['block_group_default_additional_data'];

$lang['member_profile_view']['params']['var_user_id']                   = "URL parameter, which provides member ID.";
$lang['member_profile_view']['params']['show_next_and_previous_info']   = "Enables block to load data of both next and previous members based on their ID order.";

$lang['member_profile_view']['block_short_desc'] = "Displays data of a single member";

$lang['member_profile_view']['block_desc'] = "
	Block displays data of the given member (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Subscribe to member (only for members).[kt|br]
	- Send internal message to member (only for members).[kt|br]
	- Add member to friends (only for members).[kt|br]
	- Remove member from friends (only for members).[kt|br]
	- Approve or deny friends invitation from member (only for members).[kt|br]
	- Add member to blacklist (only for members).[kt|br]
	- Remove member from blacklist (only for members).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['member_profile_view']['block_examples'] = "
	[kt|b]Display member with ID '18'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=18
	[/kt|code]
";
