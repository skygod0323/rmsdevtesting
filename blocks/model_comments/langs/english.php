<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// model_comments messages
// =====================================================================================================================

$lang['model_comments']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['model_comments']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['model_comments']['groups']['object_context'] = $lang['website_ui']['block_group_default_context_object'];
$lang['model_comments']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['model_comments']['groups']['i18n']           = $lang['website_ui']['block_group_default_i18n'];

$lang['model_comments']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['model_comments']['params']['links_per_page']     = $lang['website_ui']['parameter_default_links_per_page'];
$lang['model_comments']['params']['var_from']           = $lang['website_ui']['parameter_default_var_from'];
$lang['model_comments']['params']['sort_by']            = $lang['website_ui']['parameter_default_sort_by'];
$lang['model_comments']['params']['var_model_dir']      = "URL parameter, which provides model directory.";
$lang['model_comments']['params']['var_model_id']       = "URL parameter, which provides model ID. Will be used instead of model directory if specified and is not empty.";
$lang['model_comments']['params']['min_length']         = "Limits the minimum comment length allowed to be posted.";
$lang['model_comments']['params']['need_approve']       = "Configures whether new comments should be approved by administrator before they are displayed on website.";
$lang['model_comments']['params']['allow_anonymous']    = "Enables comments from anonymous users.";
$lang['model_comments']['params']['use_captcha']        = "Configures whether captcha protection should be used.";
$lang['model_comments']['params']['allow_editing']      = "Allows users edit their comments.";
$lang['model_comments']['params']['match_locale']       = "If this parameter is enabled, block will show only comments posted with the current KVS locale.";

$lang['model_comments']['values']['use_captcha']['1']       = "Enable for anonymous users only";
$lang['model_comments']['values']['use_captcha']['2']       = "Enable for all users";
$lang['model_comments']['values']['need_approve']['1']      = "Approval required for anonymous only";
$lang['model_comments']['values']['need_approve']['2']      = "Approval required for all comments";
$lang['model_comments']['values']['sort_by']['comment_id']  = "Comment ID";
$lang['model_comments']['values']['sort_by']['user_id']     = "User ID";
$lang['model_comments']['values']['sort_by']['comment']     = "Comment text";
$lang['model_comments']['values']['sort_by']['rating']      = "Rating";
$lang['model_comments']['values']['sort_by']['added_date']  = "Creation date";
$lang['model_comments']['values']['sort_by']['rand()']      = "Random";

$lang['model_comments']['block_short_desc'] = "Provides comments functionality for models";

$lang['model_comments']['block_desc'] = "
	Block displays comments for the given model (context object) and provides ability to post new comments. This block
	is a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['model_comments']['groups']['functionality']}[/kt|b]
	[kt|br][kt|br]

	Use these options to control whether anonymous users can post comments, when captcha protection will be used and
	some other.
	[kt|br][kt|br]

	If you want to manually approve all new comments you should enable [kt|b]need_approve[/kt|b] block parameter. In
	this case new comments will require administrator's approval before they will be displayed on your site. If
	[kt|b]need_approve[/kt|b] parameter is not enabled, all new comments will be visible right after being submitted.
	However they will still appear in administrator's review queue, so that administrator can moderate them if
	necessary.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_specific']}
";

$lang['model_comments']['block_examples'] = "
	[kt|b]Display all comments for the model with directory value 'my_model'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_model_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_model
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 8 comments per page for the model with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_model_dir = dir[kt|br]
	- var_model_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
