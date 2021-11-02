<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// signup messages
// =====================================================================================================================

$lang['signup']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['signup']['groups']['paid_access']    = "Paid access";
$lang['signup']['groups']['validation']     = $lang['website_ui']['block_group_default_validation'];

$lang['signup']['params']['use_confirm_email']              = "Enables email address confirmation for new users with free access option. No confirmation is required for paid members and when using access codes.";
$lang['signup']['params']['remember_me']                    = "Remembers registered users for the given amount of days.";
$lang['signup']['params']['disable_captcha']                = "Disables captcha for signup process (not recommended).";
$lang['signup']['params']['enable_card_payment']            = "Enables web payment method. You must have an activated web billing in [kt|b]Memberzone[/kt|b] section.";
$lang['signup']['params']['enable_sms_payment']             = "[kt|b]Obsolete![/kt|b] SMS payment methods are not supported any more.";
$lang['signup']['params']['enable_access_codes']            = "Allows new members use access codes during registration. You should first create access codes in [kt|b]User accounts generator[/kt|b] plugin.";
$lang['signup']['params']['disable_free_access']            = "Disables free access option if any of the paid access methods is enabled.";
$lang['signup']['params']['default_access_option']          = "Specifies which access option should be selected by default.";
$lang['signup']['params']['require_display_name']           = "Makes display name field required.";
$lang['signup']['params']['require_avatar']                 = "Makes avatar field required.";
$lang['signup']['params']['require_cover']                  = "Makes cover field required.";
$lang['signup']['params']['require_country']                = "Makes country field required.";
$lang['signup']['params']['require_city']                   = "Makes city field required.";
$lang['signup']['params']['require_gender']                 = "Makes gender field required.";
$lang['signup']['params']['require_orientation']            = "Makes sexual orientation field required.";
$lang['signup']['params']['require_relationship_status']    = "Makes relationship status field required.";
$lang['signup']['params']['require_birth_date']             = "Makes birth date field required.";
$lang['signup']['params']['require_access_code']            = "Makes access code field required. In this case only users with valid access codes will be able to register.";
$lang['signup']['params']['require_custom1']                = "Makes custom 1 field required.";
$lang['signup']['params']['require_custom2']                = "Makes custom 2 field required.";
$lang['signup']['params']['require_custom3']                = "Makes custom 3 field required.";
$lang['signup']['params']['require_custom4']                = "Makes custom 4 field required.";
$lang['signup']['params']['require_custom5']                = "Makes custom 5 field required.";
$lang['signup']['params']['require_custom6']                = "Makes custom 6 field required.";
$lang['signup']['params']['require_custom7']                = "Makes custom 7 field required.";
$lang['signup']['params']['require_custom8']                = "Makes custom 8 field required.";
$lang['signup']['params']['require_custom9']                = "Makes custom 9 field required.";
$lang['signup']['params']['require_custom10']               = "Makes custom 10 field required.";

$lang['signup']['values']['default_access_option']['1'] = "Free access";
$lang['signup']['values']['default_access_option']['2'] = "Web payment";
$lang['signup']['values']['default_access_option']['3'] = "SMS payment (obsolete)";

$lang['signup']['block_short_desc'] = "Provides signup and password restore functionality";

$lang['signup']['block_desc'] = "
	Block provides signup and password restore functionality. This block is integrated with KVS payment system and may
	allow paid registrations.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_display_modes']}[/kt|b]
	[kt|br][kt|br]

	Block supports 3 different forms:
	[kt|br][kt|br]

	1) [kt|b]Signup form[/kt|b]. This form will be displayed by default if no [kt|b]action[/kt|b] URL parameter is
	   passed in request.[kt|br]
	2) [kt|b]Password reminder form[/kt|b]. This form will be displayed if [kt|b]action=restore_password[/kt|b] URL
	   parameter is provided in request. When users try to reset their password, the new password is emailed to their
	   registered email address. The new password will only become active after clicking confirmation link from the
	   sent email.[kt|br]
	3) [kt|b]Confirmation re-send form[/kt|b]. This form will be displayed if [kt|b]action=resend_confirmation[/kt|b]
	   URL parameter is provided in request. If users need another account confirmation email, they can request it
	   using this form.
	[kt|br][kt|br]

	Block also supports showing several messages:
	[kt|br][kt|br]
	1) [kt|b]Account confirmation message[/kt|b]. This message will be displayed if [kt|b]action=confirm[/kt|b] URL
	   parameter is provided in request. This confirmation URL is generated when sending email to a user after a
	   successful registration (if enabled by [kt|b]use_confirm_email[/kt|b] parameter).[kt|br]
	2) [kt|b]Password change confirmation message[/kt|b]. This message will be displayed if
	   [kt|b]action=confirm_restore_pass[/kt|b] URL parameter is provided in request. This URL is generated when
	   sending email to a user after a successful request to reset their account password.[kt|br]
	3) [kt|b]Successful payment notification[/kt|b]. This message will be displayed if [kt|b]action=payment_done[/kt|b]
	   URL parameter is provided in request. URL with such parameter should be typically configured in payment
	   processor's admin panel to redirect users after their payment is processed successfully.[kt|br]
	4) [kt|b]Failed payment notification[/kt|b]. This message will be displayed if [kt|b]action=payment_failed[/kt|b]
	   URL parameter is provided in request. URL with such parameter should be typically configured in payment
	   processor's admin panel to redirect users after their payment is declined or failed.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	For signup form:[kt|br]
	[kt|code]
	- [kt|b]username_required[/kt|b]: when username field is empty [field = username][kt|br]
	- [kt|b]username_minimum[/kt|b]: when username field has length less than allowed (minimum length can be displayed via [kt|b]%1%[/kt|b] token) [field = username][kt|br]
	- [kt|b]username_characters[/kt|b]: when username field is using non alphanumeric characters [field = username][kt|br]
	- [kt|b]username_exists[/kt|b]: when such username has been registered [field = username][kt|br]
	- [kt|b]pass_required[/kt|b]: when password field is empty [field = pass][kt|br]
	- [kt|b]pass_minimum[/kt|b]: when password field has length less than allowed (minimum length can be displayed via [kt|b]%1%[/kt|b] token) [field = pass][kt|br]
	- [kt|b]pass2_required[/kt|b]: when password confirmation field is empty [field = pass2][kt|br]
	- [kt|b]pass2_invalid[/kt|b]: when password confirmation field doesn't match original password [field = pass2][kt|br]
	- [kt|b]email_required[/kt|b]: when email field is empty [field = email][kt|br]
	- [kt|b]email_invalid[/kt|b]: when email field is not a valid email [field = email][kt|br]
	- [kt|b]email_exists[/kt|b]: when such email has been registered [field = email][kt|br]
	- [kt|b]email_blocked[/kt|b]: when email's domain is listed among blocked domains [field = email][kt|br]
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
	- [kt|b]payment_option_required[/kt|b]: when paid access is enabled, but payment option is not selected [field = payment_option][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is solved incorrectly [field = code][kt|br]
	- [kt|b]access_code_required[/kt|b]: when access code field is empty, but configured as required [field = access_code][kt|br]
	- [kt|b]access_code_invalid[/kt|b]: when user is trying to use invalid access code [field = access_code][kt|br]
	- [kt|b]card_package_id_required[/kt|b]: when user selected web payment option and didn't select access package [field = card_package_id][kt|br]
	- [kt|b]ip_blocked[/kt|b]: when user's IP is listed among the blocks IPs or IP masks[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	For password reminder form:[kt|br]
	[kt|code]
	- [kt|b]email_required[/kt|b]: when email field is empty [field = email][kt|br]
	- [kt|b]email_invalid[/kt|b]: when email field is not a valid email [field = email][kt|br]
	- [kt|b]email_doesnt_exist[/kt|b]: when there is no account with such email [field = email][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	For confirmation re-send form:[kt|br]
	[kt|code]
	- [kt|b]email_required[/kt|b]: when email field is empty [field = email][kt|br]
	- [kt|b]email_invalid[/kt|b]: when email field is not a valid email [field = email][kt|br]
	- [kt|b]email_doesnt_exist[/kt|b]: when there is no account with such email [field = email][kt|br]
	- [kt|b]email_already_confirmed[/kt|b]: when account with such email doesn't need confirmation [field = email][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Paid access[/kt|b]
	[kt|br][kt|br]

	KVS supports paid registrations via web billings (Credit Card billings, Paypal and etc). Additionally you can
	pre-generate access codes in [kt|b]User accounts generator[/kt|b] plugin and sell them using alternate channels,
	such as various digital markets, or provide free of charge to promote your paid content. You can also use this
	plugin to create the actual accounts and sell them, but this will mean that users will have to use the provided
	username / password pairs instead of signing up with their own data.
	[kt|br][kt|br]

	Parameters in paid access section are designed to control which payment methods should be enabled. Enabling web
	payment method requires you to have at least one active billing in [kt|b]Memberzone[/kt|b] section of KVS admin
	panel. Using access codes requires you to have some access codes pre-generated via
	[kt|b]User accounts generator[/kt|b] plugin and then distributed among users, so that they can use access codes
	during registration.
	[kt|br][kt|br]

	[kt|b]{$lang['website_ui']['block_group_default_email_templates']}[/kt|b]
	[kt|br][kt|br]

	Block may send emails to confirm new registrations and to reset passwords for existing accounts.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['signup']['block_examples'] = "
	[kt|b]Display signup form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- no parameters required
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display signup form with web payments and no free access option[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- enable_card_payment[kt|br]
	- disable_free_access
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display password reminder form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- no parameters required
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=restore_password
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display successful payment notification[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- no parameters required
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=payment_done
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display failed payment notification[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- no parameters required
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=payment_failed
	[/kt|code]
";
