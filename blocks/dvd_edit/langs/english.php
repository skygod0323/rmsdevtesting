<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// dvd_edit messages
// =====================================================================================================================

$lang['dvd_edit']['groups']['new_objects']      = $lang['website_ui']['block_group_default_new_objects'];
$lang['dvd_edit']['groups']['edit_mode']        = $lang['website_ui']['block_group_default_edit_mode'];
$lang['dvd_edit']['groups']['validation']       = $lang['website_ui']['block_group_default_validation'];
$lang['dvd_edit']['groups']['functionality']    = $lang['website_ui']['block_group_default_functionality'];
$lang['dvd_edit']['groups']['navigation']       = $lang['website_ui']['block_group_default_navigation'];

$lang['dvd_edit']['params']['force_inactive']           = $lang['website_ui']['parameter_default_force_inactive'];
$lang['dvd_edit']['params']['var_dvd_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['dvd_edit']['params']['forbid_change']            = $lang['website_ui']['parameter_default_forbid_change'];
$lang['dvd_edit']['params']['force_inactive_on_edit']   = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['dvd_edit']['params']['require_description']      = "Makes description field required.";
$lang['dvd_edit']['params']['require_cover1_front']     = "Makes cover 1 front image field required.";
$lang['dvd_edit']['params']['require_cover1_back']      = "Makes cover 1 back image field required.";
$lang['dvd_edit']['params']['require_cover2_front']     = "Makes cover 2 front image field required.";
$lang['dvd_edit']['params']['require_cover2_back']      = "Makes cover 2 back image field required.";
$lang['dvd_edit']['params']['require_tags']             = "Makes tags field required.";
$lang['dvd_edit']['params']['require_categories']       = "Makes categories field required.";
$lang['dvd_edit']['params']['max_categories']           = "Specifies the maximum number of selected categories at the same time.";
$lang['dvd_edit']['params']['use_captcha']              = "Enables captcha protection for new DVD form.";
$lang['dvd_edit']['params']['redirect_unknown_user_to'] = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['dvd_edit']['params']['redirect_on_new_done']     = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";
$lang['dvd_edit']['params']['redirect_on_change_done']  = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";

$lang['dvd_edit']['block_short_desc'] = "Provides creation / editing functionality for DVDs / channels / TV seasons";

$lang['dvd_edit']['block_desc'] = "
	Block displays creation / editing form for DVDs / channels / TV seasons.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: when title field is empty [field = title][kt|br]
	- [kt|b]title_exists[/kt|b]: when object with such title already exists [field = title][kt|br]
	- [kt|b]description_required[/kt|b]: when description field is empty, but configured as required [field = description][kt|br]
	- [kt|b]cover1_front_required[/kt|b]: when cover 1 front image is not uploaded, but configured as required [field = cover1_front][kt|br]
	- [kt|b]cover1_front_invalid_format[/kt|b]: when image file uploaded into cover 1 front field has invalid format [field = cover1_front][kt|br]
	- [kt|b]cover1_front_invalid_size[/kt|b]: when image file uploaded into cover 1 front field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = cover1_front][kt|br]
	- [kt|b]cover1_back_required[/kt|b]: when cover 1 back image is not uploaded, but configured as required [field = cover1_back][kt|br]
	- [kt|b]cover1_back_invalid_format[/kt|b]: when image file uploaded into cover 1 back field has invalid format [field = cover1_back][kt|br]
	- [kt|b]cover1_back_invalid_size[/kt|b]: when image file uploaded into cover 1 back field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = cover1_back][kt|br]
	- [kt|b]cover2_front_required[/kt|b]: when cover 2 front image is not uploaded, but configured as required [field = cover2_front][kt|br]
	- [kt|b]cover2_front_invalid_format[/kt|b]: when image file uploaded into cover 2 front field has invalid format [field = cover2_front][kt|br]
	- [kt|b]cover2_front_invalid_size[/kt|b]: when image file uploaded into cover 2 front field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = cover2_front][kt|br]
	- [kt|b]cover2_back_required[/kt|b]: when cover 2 back image is not uploaded, but configured as required [field = cover2_back][kt|br]
	- [kt|b]cover2_back_invalid_format[/kt|b]: when image file uploaded into cover 2 back field has invalid format [field = cover2_back][kt|br]
	- [kt|b]cover2_back_invalid_size[/kt|b]: when image file uploaded into cover 2 back field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = cover2_back][kt|br]
	- [kt|b]tags_required[/kt|b]: when tags field is empty, but configured as required [field = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: when categories field is empty, but configured as required [field = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: when more than allowed categories selected [field = category_ids][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: when non-integer value is specified into DVD subscription cost (in tokens) field [field = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['dvd_edit']['block_examples'] = "
	[kt|b]Display DVD creation form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display editing form for the DVD with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_dvd_id = id[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
