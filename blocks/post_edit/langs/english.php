<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// post_edit messages
// =====================================================================================================================

$lang['post_edit']['groups']['new_objects']     = $lang['website_ui']['block_group_default_new_objects'];
$lang['post_edit']['groups']['edit_mode']       = $lang['website_ui']['block_group_default_edit_mode'];
$lang['post_edit']['groups']['validation']      = $lang['website_ui']['block_group_default_validation'];
$lang['post_edit']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['post_edit']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['post_edit']['params']['post_type']                   = "All new posts will be created of that type. Specify post type external ID.";
$lang['post_edit']['params']['allow_anonymous']             = "Allows anonymous users to create new posts.";
$lang['post_edit']['params']['force_inactive']              = $lang['website_ui']['parameter_default_force_inactive'];
$lang['post_edit']['params']['var_post_id']                 = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['post_edit']['params']['forbid_change']               = $lang['website_ui']['parameter_default_forbid_change'];
$lang['post_edit']['params']['force_inactive_on_edit']      = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['post_edit']['params']['duplicates_allowed']          = "Whether posts with duplicate titles are allowed.";
$lang['post_edit']['params']['optional_description']        = "Makes description field optional.";
$lang['post_edit']['params']['optional_tags']               = "Makes tags field optional.";
$lang['post_edit']['params']['optional_categories']         = "Makes categories field optional.";
$lang['post_edit']['params']['max_categories']              = "Specifies the maximum number of selected categories at the same time.";
$lang['post_edit']['params']['use_captcha']                 = "Enables captcha protection for new post form.";
$lang['post_edit']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['post_edit']['params']['redirect_on_new_done']        = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";
$lang['post_edit']['params']['redirect_on_change_done']     = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";

$lang['post_edit']['block_short_desc'] = "Provides creation / editing functionality for posts";

$lang['post_edit']['block_desc'] = "
	Block displays creation / editing form for posts of the specified type. You should use [kt|b]post_type[/kt|b]
	parameter in order to specify post type for all new posts. Please use multiple [kt|b]post_edit[/kt|b] blocks if
	you want to allow creating posts of different types.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]post_type_required[/kt|b]: when [kt|b]post_type[/kt|b] parameter in block settings is empty[kt|br]
	- [kt|b]post_type_invalid[/kt|b]: when [kt|b]post_type[/kt|b] parameter in block settings is not a valid post type[kt|br]
	- [kt|b]title_required[/kt|b]: when title field is empty [field = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: when title field has length < 5 charachers [field = title][kt|br]
	- [kt|b]title_exists[/kt|b]: when object with such title already exists [field = title][kt|br]
	- [kt|b]description_required[/kt|b]: when description field is empty [field = description][kt|br]
	- [kt|b]content_required[/kt|b]: when content field is empty [field = content][kt|br]
	- [kt|b]tags_required[/kt|b]: when tags field is empty [field = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: when categories field is empty [field = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: when more than allowed categories selected [field = category_ids][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['post_edit']['block_examples'] = "
	[kt|b]Display post creation form for the 'news' post type[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- post_type = news[kt|br]
	- var_post_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display editing form for the post with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- post_type = news[kt|br]
	- var_post_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
