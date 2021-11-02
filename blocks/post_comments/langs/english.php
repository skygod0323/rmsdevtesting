<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// post_comments messages
// =====================================================================================================================

$lang['post_comments']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['post_comments']['groups']['sorting']         = $lang['website_ui']['block_group_default_sorting'];
$lang['post_comments']['groups']['object_context']  = $lang['website_ui']['block_group_default_context_object'];
$lang['post_comments']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['post_comments']['groups']['i18n']            = $lang['website_ui']['block_group_default_i18n'];

$lang['post_comments']['params']['items_per_page']  = $lang['website_ui']['parameter_default_items_per_page'];
$lang['post_comments']['params']['links_per_page']  = $lang['website_ui']['parameter_default_links_per_page'];
$lang['post_comments']['params']['var_from']        = $lang['website_ui']['parameter_default_var_from'];
$lang['post_comments']['params']['sort_by']         = $lang['website_ui']['parameter_default_sort_by'];
$lang['post_comments']['params']['var_post_dir']    = "URL parameter, which provides post directory.";
$lang['post_comments']['params']['var_post_id']     = "URL parameter, which provides post ID. Will be used instead of post directory if specified and is not empty.";
$lang['post_comments']['params']['min_length']      = "Limits the minimum comment length allowed to be posted.";
$lang['post_comments']['params']['need_approve']    = "Configures whether new comments should be approved by administrator before they are displayed on website.";
$lang['post_comments']['params']['allow_anonymous'] = "Enables comments from anonymous users.";
$lang['post_comments']['params']['use_captcha']     = "Configures whether captcha protection should be used.";
$lang['post_comments']['params']['allow_editing']   = "Allows users edit their comments.";
$lang['post_comments']['params']['match_locale']    = "If this parameter is enabled, block will show only comments posted with the current KVS locale.";

$lang['post_comments']['values']['use_captcha']['1']        = "Enable for anonymous users only";
$lang['post_comments']['values']['use_captcha']['2']        = "Enable for all users";
$lang['post_comments']['values']['need_approve']['1']       = "Approval required for anonymous only";
$lang['post_comments']['values']['need_approve']['2']       = "Approval required for all comments";
$lang['post_comments']['values']['sort_by']['comment_id']   = "Comment ID";
$lang['post_comments']['values']['sort_by']['user_id']      = "User ID";
$lang['post_comments']['values']['sort_by']['comment']      = "Comment text";
$lang['post_comments']['values']['sort_by']['rating']       = "Rating";
$lang['post_comments']['values']['sort_by']['added_date']   = "Creation date";
$lang['post_comments']['values']['sort_by']['rand()']       = "Random";

$lang['post_comments']['block_short_desc'] = "Provides comments functionality for posts";

$lang['post_comments']['block_desc'] = "
	Block displays comments for the given post (context object) and provides ability to post new comments. This block
	is a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['post_comments']['groups']['functionality']}[/kt|b]
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

$lang['post_comments']['block_examples'] = "
	[kt|b]Display all comments for the post with directory value 'my_post'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_post_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_post
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 8 comments per page for the post with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_post_dir = dir[kt|br]
	- var_post_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
