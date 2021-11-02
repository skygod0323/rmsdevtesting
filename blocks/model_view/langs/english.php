<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// model_view messages
// =====================================================================================================================

$lang['model_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['model_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];

$lang['model_view']['params']['var_model_dir']                  = "URL parameter, which provides model directory.";
$lang['model_view']['params']['var_model_id']                   = "URL parameter, which provides model ID. Will be used instead of model directory if specified and is not empty.";
$lang['model_view']['params']['show_next_and_previous_info']    = "Enables block to load data of both next and previous models based on the given criteria.";

$lang['model_view']['values']['show_next_and_previous_info']['0']   = "By ID";
$lang['model_view']['values']['show_next_and_previous_info']['1']   = "By group";

$lang['model_view']['block_short_desc'] = "Displays data of a single model";

$lang['model_view']['block_desc'] = "
	Block displays data of the given model (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Rate model once from a single IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['model_view']['block_examples'] = "
	[kt|b]Display model with directory value 'my_model'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_model_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_model
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display model with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_model_dir = dir[kt|br]
	- var_model_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
