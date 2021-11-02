<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// album_edit messages
// =====================================================================================================================

$lang['album_edit']['groups']['new_objects']    = $lang['website_ui']['block_group_default_new_objects'];
$lang['album_edit']['groups']['edit_mode']      = $lang['website_ui']['block_group_default_edit_mode'];
$lang['album_edit']['groups']['validation']     = $lang['website_ui']['block_group_default_validation'];
$lang['album_edit']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['album_edit']['groups']['navigation']     = $lang['website_ui']['block_group_default_navigation'];

$lang['album_edit']['params']['allow_anonymous']            = "Allows anonymous users to create new albums.";
$lang['album_edit']['params']['force_inactive']             = $lang['website_ui']['parameter_default_force_inactive'];
$lang['album_edit']['params']['var_album_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['album_edit']['params']['forbid_change']              = $lang['website_ui']['parameter_default_forbid_change'];
$lang['album_edit']['params']['forbid_change_images']       = "Forbids editing album images. Images section will be displayed in read-only mode.";
$lang['album_edit']['params']['force_inactive_on_edit']     = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['album_edit']['params']['allowed_formats']            = "Comma separated list of allowed image formats. The following formats are supported: [kt|b]jpg[/kt|b], [kt|b]gif[/kt|b], [kt|b]png[/kt|b]. If not enabled, all image formats are allowed.";
$lang['album_edit']['params']['min_image_width']            = "Minimum allowed image width (in pixels).";
$lang['album_edit']['params']['min_image_height']           = "Minimum allowed image height (in pixels).";
$lang['album_edit']['params']['min_image_count']            = "Minimum allowed number of images for a new album.";
$lang['album_edit']['params']['optional_description']       = "Makes description field optional.";
$lang['album_edit']['params']['optional_tags']              = "Makes tags field optional.";
$lang['album_edit']['params']['optional_categories']        = "Makes categories field optional.";
$lang['album_edit']['params']['max_categories']             = "Specifies the maximum number of selected categories at the same time.";
$lang['album_edit']['params']['use_captcha']                = "Enables captcha protection for new album form.";
$lang['album_edit']['params']['set_custom_flag1']           = "Sets the given value into custom flag 1.";
$lang['album_edit']['params']['set_custom_flag2']           = "Sets the given value into custom flag 2.";
$lang['album_edit']['params']['set_custom_flag3']           = "Sets the given value into custom flag 3.";
$lang['album_edit']['params']['redirect_unknown_user_to']   = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['album_edit']['params']['redirect_on_new_done']       = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";
$lang['album_edit']['params']['redirect_on_change_done']    = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";

$lang['album_edit']['block_short_desc'] = "Provides creation / editing functionality for albums";

$lang['album_edit']['block_desc'] = "
	Block displays creation / editing form for albums.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: when title field is empty [field = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: when title field has length < 5 charachers [field = title][kt|br]
	- [kt|b]description_required[/kt|b]: when description field is empty [field = description][kt|br]
	- [kt|b]tags_required[/kt|b]: when tags field is empty [field = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: when categories field is empty [field = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: when more than allowed categories selected [field = category_ids][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: when non-integer value is specified into album cost (in tokens) field [field = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	- [kt|b]content_required[/kt|b]: when creating a new album and no image files are uploaded [field = content][kt|br]
	- [kt|b]content_filesize_limit[/kt|b]: when creating a new album and summary filesize of all uploaded image files is bigger than the allowed limit [field = content][kt|br]
	- [kt|b]content_images_empty[/kt|b]: when creating a new album and all uploaded image files are not images, or smaller than allowed by width / height limits [field = content][kt|br]
	- [kt|b]content_images_minimum[/kt|b]: when creating a new album and the number of accepted image files is less than the allowed limit [field = content][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['album_edit']['block_examples'] = "
	[kt|b]Display album creation form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_album_id = id[kt|br]
	- min_image_width = 800[kt|br]
	- min_image_height = 600[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display editing form for album with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_album_id = id[kt|br]
	- min_image_width = 800[kt|br]
	- min_image_height = 600[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
