<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// member_profile_edit messages
// =====================================================================================================================

$lang['member_profile_edit']['groups']['functionality'] = $lang['website_ui']['block_group_default_functionality'];
$lang['member_profile_edit']['groups']['validation']    = $lang['website_ui']['block_group_default_validation'];
$lang['member_profile_edit']['groups']['navigation']    = $lang['website_ui']['block_group_default_navigation'];

$lang['member_profile_edit']['params']['use_confirm_email']             = "When enabled, email change must be confirmed from a new email address.";
$lang['member_profile_edit']['params']['require_avatar']                = "Makes avatar field required.";
$lang['member_profile_edit']['params']['require_cover']                 = "Makes cover field required.";
$lang['member_profile_edit']['params']['require_country']               = "Makes country field required.";
$lang['member_profile_edit']['params']['require_city']                  = "Makes city field required.";
$lang['member_profile_edit']['params']['require_gender']                = "Makes gender field required.";
$lang['member_profile_edit']['params']['require_orientation']           = "Makes sexual orientation field required.";
$lang['member_profile_edit']['params']['require_relationship_status']   = "Makes relationship status field required.";
$lang['member_profile_edit']['params']['require_birth_date']            = "Makes birth date field required.";
$lang['member_profile_edit']['params']['require_custom1']               = "Makes custom 1 field required.";
$lang['member_profile_edit']['params']['require_custom2']               = "Makes custom 2 field required.";
$lang['member_profile_edit']['params']['require_custom3']               = "Makes custom 3 field required.";
$lang['member_profile_edit']['params']['require_custom4']               = "Makes custom 4 field required.";
$lang['member_profile_edit']['params']['require_custom5']               = "Makes custom 5 field required.";
$lang['member_profile_edit']['params']['require_custom6']               = "Makes custom 6 field required.";
$lang['member_profile_edit']['params']['require_custom7']               = "Makes custom 7 field required.";
$lang['member_profile_edit']['params']['require_custom8']               = "Makes custom 8 field required.";
$lang['member_profile_edit']['params']['require_custom9']               = "Makes custom 9 field required.";
$lang['member_profile_edit']['params']['require_custom10']              = "Makes custom 10 field required.";
$lang['member_profile_edit']['params']['redirect_unknown_user_to']      = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];

$lang['member_profile_edit']['block_short_desc'] = "Provides members profile editing functionality";

$lang['member_profile_edit']['block_desc'] = "
	Block provides profile editing, email change and password change functionality.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_display_modes']}[/kt|b]
	[kt|br][kt|br]

	Block supports 3 different forms:
	[kt|br][kt|br]

	1) [kt|b]Profile editing form[/kt|b]. This form will be displayed by default if no [kt|b]action[/kt|b] URL parameter
	   is passed in request.[kt|br]
	2) [kt|b]Password change form[/kt|b]. This form will be displayed if [kt|b]action=change_pass[/kt|b] URL parameter
	   is provided in request.[kt|br]
	3) [kt|b]Email change form[/kt|b]. This form will be displayed if [kt|b]action=change_email[/kt|b] URL parameter is
	   provided in request. Changing email may require confirmation from the new email address if
	   [kt|b]use_confirm_email[/kt|b] block parameter is enabled.
	[kt|br][kt|br]

	Block also supports showing several messages:
	[kt|br][kt|br]
	1) [kt|b]Email change confirmation message[/kt|b]. This message will be displayed if [kt|b]action=confirm[/kt|b] URL
	   parameter is provided in request. This confirmation URL is generated when sending email to a user after email
	   change requested (if enabled by [kt|b]use_confirm_email[/kt|b] parameter).
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	For profile editing form:[kt|br]
	[kt|code]
	- [kt|b]display_name_required[/kt|b]: when display name field is empty, but configured as required [field = display_name][kt|br]
	- [kt|b]display_name_minimum[/kt|b]: when display name field has length less than allowed (minimum length can be displayed via [kt|b]%1%[/kt|b] token) [field = display_name][kt|br]
	- [kt|b]display_name_exists[/kt|b]: when such display name has been occupied [field = display_name][kt|br]
	- [kt|b]country_id_required[/kt|b]: when country field is empty, but configured as required [field = country_id][kt|br]
	- [kt|b]city_required[/kt|b]: when city field is empty, but configured as required [field = city][kt|br]
	- [kt|b]gender_id_required[/kt|b]: when gender field is empty, but configured as required [field = gender_id][kt|br]
	- [kt|b]orientation_id_required[/kt|b]: when sexual orientation field is empty, but configured as required [field = orientation_id][kt|br]
	- [kt|b]relationship_status_id_required[/kt|b]: when relationship status field is empty, but configured as required [field = relationship_status_id][kt|br]
	- [kt|b]birth_date_required[/kt|b]: when birth date field is empty, but configured as required [field = birth_date][kt|br]
	- [kt|b]birth_date_invalid[/kt|b]: when birth date field format is invalid [field = birth_date][kt|br]
	- [kt|b]avatar_required[/kt|b]: when avatar image is not uploaded, but configured as required [field = avatar][kt|br]
	- [kt|b]avatar_invalid_format[/kt|b]: when image file uploaded into avatar field has invalid format [field = avatar][kt|br]
	- [kt|b]avatar_invalid_size[/kt|b]: when image file uploaded into avatar field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = avatar][kt|br]
	- [kt|b]cover_required[/kt|b]: when cover image is not uploaded, but configured as required [field = cover][kt|br]
	- [kt|b]cover_invalid_format[/kt|b]: when image file uploaded into cover field has invalid format [field = cover][kt|br]
	- [kt|b]cover_invalid_size[/kt|b]: when image file uploaded into cover field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = cover][kt|br]
	- [kt|b]custom1_required[/kt|b]: when custom1 field is empty, but configured as required [field = custom1][kt|br]
	- [kt|b]custom2_required[/kt|b]: when custom2 field is empty, but configured as required [field = custom2][kt|br]
	- [kt|b]custom3_required[/kt|b]: when custom3 field is empty, but configured as required [field = custom3][kt|br]
	- [kt|b]custom4_required[/kt|b]: when custom4 field is empty, but configured as required [field = custom4][kt|br]
	- [kt|b]custom5_required[/kt|b]: when custom5 field is empty, but configured as required [field = custom5][kt|br]
	- [kt|b]custom6_required[/kt|b]: when custom6 field is empty, but configured as required [field = custom6][kt|br]
	- [kt|b]custom7_required[/kt|b]: when custom7 field is empty, but configured as required [field = custom7][kt|br]
	- [kt|b]custom8_required[/kt|b]: when custom8 field is empty, but configured as required [field = custom8][kt|br]
	- [kt|b]custom9_required[/kt|b]: when custom9 field is empty, but configured as required [field = custom9][kt|br]
	- [kt|b]custom10_required[/kt|b]: when custom10 field is empty, but configured as required [field = custom10][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: when non-integer value is specified into profile subscription cost (in tokens) field [field = tokens_required][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	For password change form:[kt|br]
	[kt|code]
	- [kt|b]old_pass_required[/kt|b]: when old password field is empty [field = old_pass][kt|br]
	- [kt|b]old_pass_invalid[/kt|b]: when such old password doesn't match the actual user password [field = old_pass][kt|br]
	- [kt|b]pass_required[/kt|b]: when new password field is empty [field = pass][kt|br]
	- [kt|b]pass_minimum[/kt|b]: when new password field has length less than allowed (minimum length can be displayed via [kt|b]%1%[/kt|b] token) [field = pass][kt|br]
	- [kt|b]pass_blocked[/kt|b]: when such new password has been blocked and is not allowed to be used by this user [field = pass][kt|br]
	- [kt|b]pass2_required[/kt|b]: when password confirmation field is empty [field = pass2][kt|br]
	- [kt|b]pass2_invalid[/kt|b]: when password confirmation field doesn't match new password [field = pass2][kt|br]

	[/kt|code]
	[kt|br][kt|br]

	For email change form:[kt|br]
	[kt|code]
	- [kt|b]email_required[/kt|b]: when email field is empty [field = email][kt|br]
	- [kt|b]email_invalid[/kt|b]: when email field is not a valid email [field = email][kt|br]
	- [kt|b]email_exists[/kt|b]: when such email has been registered [field = email][kt|br]
	- [kt|b]email_not_changed[/kt|b]: when new email is the same as the old one [field = email][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_email_templates']}[/kt|b]
	[kt|br][kt|br]

	Block may send emails to confirm email change.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['member_profile_edit']['block_examples'] = "
	[kt|b]Display profile editing form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display password change form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change_pass
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display email change form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change_email
	[/kt|code]
";
