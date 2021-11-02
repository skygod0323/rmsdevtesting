<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_group_view messages
// =====================================================================================================================

$lang['content_source_group_view']['groups']['object_context']  = $lang['website_ui']['block_group_default_context_object'];

$lang['content_source_group_view']['params']['var_content_source_group_dir']    = "URL parameter, which provides content source group directory.";
$lang['content_source_group_view']['params']['var_content_source_group_id']     = "URL parameter, which provides content source group ID. Will be used instead of content source group directory if specified and is not empty.";

$lang['content_source_group_view']['block_short_desc'] = "Displays data of a single content source group";

$lang['content_source_group_view']['block_desc'] = "
	Block displays data of the given content source group (context object).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['content_source_group_view']['block_examples'] = "
	[kt|b]Display content source group with directory value 'my_content_source_group'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_content_source_group_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display content source group with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_content_source_group_dir = dir[kt|br]
	- var_content_source_group_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
