<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_view messages
// =====================================================================================================================

$lang['dvd_view']['groups']['object_context']   = $lang['website_ui']['block_group_default_context_object'];
$lang['dvd_view']['groups']['additional_data']  = $lang['website_ui']['block_group_default_additional_data'];

$lang['dvd_view']['params']['var_dvd_dir']                  = "URL parameter, which provides DVD directory.";
$lang['dvd_view']['params']['var_dvd_id']                   = "URL parameter, which provides DVD ID. Will be used instead of DVD directory if specified and is not empty.";
$lang['dvd_view']['params']['show_next_and_previous_info']  = "Enables block to load data of both next and previous DVDs based on the given criteria.";

$lang['dvd_view']['values']['show_next_and_previous_info']['0']   = "By ID";
$lang['dvd_view']['values']['show_next_and_previous_info']['1']   = "By group";
$lang['dvd_view']['values']['show_next_and_previous_info']['2']   = "By owner";

$lang['dvd_view']['block_short_desc'] = "Displays data of a single DVD / channel / TV season";

$lang['dvd_view']['block_desc'] = "
	Block displays data of the given DVD / channel / TV season (context object) and provides the following
	functionality:
	[kt|br][kt|br]

	- Rate DVD once from a single IP.[kt|br]
	- Flag DVD once from a single IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['dvd_view']['block_examples'] = "
	[kt|b]Display DVD with directory value 'my_dvd'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_dvd
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display DVD with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_dir = dir[kt|br]
	- var_dvd_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
