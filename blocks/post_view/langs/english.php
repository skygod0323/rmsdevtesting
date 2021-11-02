<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// post_view messages
// =====================================================================================================================

$lang['post_view']['groups']['object_context']  = $lang['website_ui']['block_group_default_context_object'];
$lang['post_view']['groups']['additional_data'] = $lang['website_ui']['block_group_default_additional_data'];

$lang['post_view']['params']['var_post_dir']                = "URL parameter, which provides post directory.";
$lang['post_view']['params']['var_post_id']                 = "URL parameter, which provides post ID. Will be used instead of post directory if specified and is not empty.";
$lang['post_view']['params']['show_next_and_previous_info'] = "Enables block to load data of both next and previous posts based on the given criteria.";

$lang['post_view']['values']['show_next_and_previous_info']['0']    = "By publishing date";
$lang['post_view']['values']['show_next_and_previous_info']['3']    = "By user";

$lang['post_view']['block_short_desc'] = "Displays data of a single post";

$lang['post_view']['block_desc'] = "
	Block displays data of the given post (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Rate post once from a single IP.[kt|br]
	- Flag post once from a single IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['post_view']['block_examples'] = "
	[kt|b]Display post with directory value 'my_post'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_post_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_post
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display post with ID '198'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_post_dir = dir[kt|br]
	- var_post_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=198
	[/kt|code]
";
