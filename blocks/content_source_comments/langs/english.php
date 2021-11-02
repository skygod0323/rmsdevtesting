<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// content_source_comments messages
// =====================================================================================================================

$lang['content_source_comments']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['content_source_comments']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['content_source_comments']['groups']['object_context']    = $lang['website_ui']['block_group_default_context_object'];
$lang['content_source_comments']['groups']['functionality']     = $lang['website_ui']['block_group_default_functionality'];
$lang['content_source_comments']['groups']['i18n']              = $lang['website_ui']['block_group_default_i18n'];

$lang['content_source_comments']['params']['items_per_page']            = $lang['website_ui']['parameter_default_items_per_page'];
$lang['content_source_comments']['params']['links_per_page']            = $lang['website_ui']['parameter_default_links_per_page'];
$lang['content_source_comments']['params']['var_from']                  = $lang['website_ui']['parameter_default_var_from'];
$lang['content_source_comments']['params']['sort_by']                   = $lang['website_ui']['parameter_default_sort_by'];
$lang['content_source_comments']['params']['var_content_source_dir']    = "URL parameter, which provides content source directory.";
$lang['content_source_comments']['params']['var_content_source_id']     = "URL parameter, which provides content source ID. Will be used instead of content source directory if specified and is not empty.";
$lang['content_source_comments']['params']['min_length']                = "Limits the minimum comment length allowed to be posted.";
$lang['content_source_comments']['params']['need_approve']              = "Configures whether new comments should be approved by administrator before they are displayed on website.";
$lang['content_source_comments']['params']['allow_anonymous']           = "Enables comments from anonymous users.";
$lang['content_source_comments']['params']['use_captcha']               = "Configures whether captcha protection should be used.";
$lang['content_source_comments']['params']['allow_editing']             = "Allows users edit their comments.";
$lang['content_source_comments']['params']['match_locale']              = "If this parameter is enabled, block will show only comments posted with the current KVS locale.";

$lang['content_source_comments']['values']['use_captcha']['1']      = "Enable for anonymous users only";
$lang['content_source_comments']['values']['use_captcha']['2']      = "Enable for all users";
$lang['content_source_comments']['values']['need_approve']['1']     = "Approval required for anonymous only";
$lang['content_source_comments']['values']['need_approve']['2']     = "Approval required for all comments";
$lang['content_source_comments']['values']['sort_by']['comment_id'] = "Comment ID";
$lang['content_source_comments']['values']['sort_by']['user_id']    = "User ID";
$lang['content_source_comments']['values']['sort_by']['comment']    = "Comment text";
$lang['content_source_comments']['values']['sort_by']['rating']     = "Rating";
$lang['content_source_comments']['values']['sort_by']['added_date'] = "Creation date";
$lang['content_source_comments']['values']['sort_by']['rand()']     = "Random";

$lang['content_source_comments']['block_short_desc'] = "Provides comments functionality for content sources";

$lang['content_source_comments']['block_desc'] = "
	Block displays comments for the given content source (context object) and provides ability to post new comments.
	This block is a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['content_source_comments']['groups']['functionality']}[/kt|b]
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

$lang['content_source_comments']['block_examples'] = "
	[kt|b]Display all comments for the content source with directory value 'my_content_source'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_content_source_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_content_source
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 8 comments per page for the content source with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_content_source_dir = dir[kt|br]
	- var_content_source_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
