<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// playlist_edit messages
// =====================================================================================================================

$lang['playlist_edit']['groups']['new_objects']     = $lang['website_ui']['block_group_default_new_objects'];
$lang['playlist_edit']['groups']['edit_mode']       = $lang['website_ui']['block_group_default_edit_mode'];
$lang['playlist_edit']['groups']['validation']      = $lang['website_ui']['block_group_default_validation'];
$lang['playlist_edit']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['playlist_edit']['groups']['navigation']      = $lang['website_ui']['block_group_default_navigation'];

$lang['playlist_edit']['params']['force_inactive']              = $lang['website_ui']['parameter_default_force_inactive'];
$lang['playlist_edit']['params']['var_playlist_id']             = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['playlist_edit']['params']['force_inactive_on_edit']      = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['playlist_edit']['params']['require_description']         = "Makes description field required (only affects public playlists).";
$lang['playlist_edit']['params']['require_tags']                = "Makes tags field required (only affects public playlists).";
$lang['playlist_edit']['params']['require_categories']          = "Makes categories field required (only affects public playlists).";
$lang['playlist_edit']['params']['max_categories']              = "Specifies the maximum number of selected categories at the same time.";
$lang['playlist_edit']['params']['use_captcha']                 = "Enables captcha protection for new playlist form.";
$lang['playlist_edit']['params']['redirect_unknown_user_to']    = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['playlist_edit']['params']['redirect_on_new_done']        = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";
$lang['playlist_edit']['params']['redirect_on_change_done']     = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";

$lang['playlist_edit']['block_short_desc'] = "Provides creation / editing functionality for playlists";

$lang['playlist_edit']['block_desc'] = "
	Block displays creation / editing form for playlists.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: when title field is empty [field = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: when title field has length < 5 charachers [field = title][kt|br]
	- [kt|b]title_exists[/kt|b]: when object with such title already exists [field = title][kt|br]
	- [kt|b]description_required[/kt|b]: when description field is empty [field = description][kt|br]
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

$lang['playlist_edit']['block_examples'] = "
	[kt|b]Display playlist creation form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_playlist_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display editing form for the playlist with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_playlist_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
