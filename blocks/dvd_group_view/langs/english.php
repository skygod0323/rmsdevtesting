<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_group_view messages
// =====================================================================================================================

$lang['dvd_group_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];

$lang['dvd_group_view']['params']['var_dvd_group_dir']              = "URL parameter, which provides DVD group directory.";
$lang['dvd_group_view']['params']['var_dvd_group_id']               = "URL parameter, which provides DVD group ID. Will be used instead of DVD group directory if specified and is not empty.";

$lang['dvd_group_view']['block_short_desc'] = "Displays data of a single DVD / channel group / TV serie";

$lang['dvd_group_view']['block_desc'] = "
	Block displays data of the given DVD / channel group / TV serie (context object).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['dvd_group_view']['block_examples'] = "
	[kt|b]Display DVD group with directory value 'my_dvd_group'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_group_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_dvd_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display DVD group with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_group_dir = dir[kt|br]
	- var_dvd_group_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
