<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_view messages
// =====================================================================================================================

$lang['content_source_view']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['content_source_view']['groups']['additional_data']   = $lang['website_ui']['block_group_default_additional_data'];

$lang['content_source_view']['params']['var_content_source_dir']        = "URL parameter, which provides content source directory.";
$lang['content_source_view']['params']['var_content_source_id']         = "URL parameter, which provides content source ID. Will be used instead of content source directory if specified and is not empty.";
$lang['content_source_view']['params']['show_next_and_previous_info']   = "Enables block to load data of both next and previous content sources based on the given criteria.";

$lang['content_source_view']['values']['show_next_and_previous_info']['0']   = "By ID";
$lang['content_source_view']['values']['show_next_and_previous_info']['1']   = "By group";

$lang['content_source_view']['block_short_desc'] = "Displays data of a single content source";

$lang['content_source_view']['block_desc'] = "
	Block displays data of the given content source (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Rate content source once from a single IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['content_source_view']['block_examples'] = "
	[kt|b]Display content source with directory value 'my_content_source'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_content_source_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display content source with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_content_source_dir = dir[kt|br]
	- var_content_source_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
