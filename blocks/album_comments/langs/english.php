<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// album_comments messages
// =====================================================================================================================

$lang['album_comments']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['album_comments']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['album_comments']['groups']['object_context'] = $lang['website_ui']['block_group_default_context_object'];
$lang['album_comments']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['album_comments']['groups']['i18n']           = $lang['website_ui']['block_group_default_i18n'];

$lang['album_comments']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['album_comments']['params']['links_per_page']     = $lang['website_ui']['parameter_default_links_per_page'];
$lang['album_comments']['params']['var_from']           = $lang['website_ui']['parameter_default_var_from'];
$lang['album_comments']['params']['sort_by']            = $lang['website_ui']['parameter_default_sort_by'];
$lang['album_comments']['params']['var_album_dir']      = "URL parameter, which provides album directory.";
$lang['album_comments']['params']['var_album_id']       = "URL parameter, which provides album ID. Will be used instead of album directory if specified and is not empty.";
$lang['album_comments']['params']['var_album_image_id'] = "URL parameter, which provides album image ID. If provided, block will display comments for the specific album image. Otherwise all album comments will be displayed.";
$lang['album_comments']['params']['min_length']         = "Limits the minimum comment length allowed to be posted.";
$lang['album_comments']['params']['need_approve']       = "Configures whether new comments should be approved by administrator before they are displayed on website.";
$lang['album_comments']['params']['allow_anonymous']    = "Enables comments from anonymous users.";
$lang['album_comments']['params']['use_captcha']        = "Configures whether captcha protection should be used.";
$lang['album_comments']['params']['allow_editing']      = "Allows users edit their comments.";
$lang['album_comments']['params']['match_locale']       = "If this parameter is enabled, block will show only comments posted with the current KVS locale.";

$lang['album_comments']['values']['use_captcha']['1']       = "Enable for anonymous users only";
$lang['album_comments']['values']['use_captcha']['2']       = "Enable for all users";
$lang['album_comments']['values']['need_approve']['1']      = "Approval required for anonymous only";
$lang['album_comments']['values']['need_approve']['2']      = "Approval required for all comments";
$lang['album_comments']['values']['sort_by']['comment_id']  = "Comment ID";
$lang['album_comments']['values']['sort_by']['user_id']     = "User ID";
$lang['album_comments']['values']['sort_by']['comment']     = "Comment text";
$lang['album_comments']['values']['sort_by']['rating']      = "Rating";
$lang['album_comments']['values']['sort_by']['added_date']  = "Creation date";
$lang['album_comments']['values']['sort_by']['rand()']      = "Random";

$lang['album_comments']['block_short_desc'] = "Provides comments functionality for photo albums";

$lang['album_comments']['block_desc'] = "
	Block displays comments for the given album or album image (context object) and provides ability to post new
	comments. This block is a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['album_comments']['groups']['functionality']}[/kt|b]
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

$lang['album_comments']['block_examples'] = "
	[kt|b]Display all comments for the album with directory value 'my_photo_album'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_album_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 5 comments for the album image with ID '176' from album with directory value 'my_photo_album' without any pagination[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_album_dir = dir[kt|br]
	- var_album_image_id = image_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album&image_id=176
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 8 comments per page for the album with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 8[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	- var_album_dir = dir[kt|br]
	- var_album_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11
	[/kt|code]
";
