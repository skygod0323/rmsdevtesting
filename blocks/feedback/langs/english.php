<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// feedback messages
// =====================================================================================================================

$lang['feedback']['params']['require_subject']  = "Makes subject field required.";
$lang['feedback']['params']['require_email']    = "Makes email field required.";
$lang['feedback']['params']['use_custom1']      = "Makes custom 1 field required.";
$lang['feedback']['params']['use_custom2']      = "Makes custom 2 field required.";
$lang['feedback']['params']['use_custom3']      = "Makes custom 3 field required.";
$lang['feedback']['params']['use_custom4']      = "Makes custom 4 field required.";
$lang['feedback']['params']['use_custom5']      = "Makes custom 5 field required.";
$lang['feedback']['params']['use_captcha']      = "Enables captcha for this form.";

$lang['feedback']['block_short_desc'] = "Provides feedback functionality";

$lang['feedback']['block_desc'] = "
	Block provides ability to send feedback message to project admins.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]message_required[/kt|b]: when message field is empty [field = message][kt|br]
	- [kt|b]subject_required[/kt|b]: when subject field is empty, but configured as required [field = subject][kt|br]
	- [kt|b]email_required[/kt|b]: when email field is empty, but configured as required [field = email][kt|br]
	- [kt|b]custom1_required[/kt|b]: when custom1 field is empty, but configured as required [field = custom1][kt|br]
	- [kt|b]custom2_required[/kt|b]: when custom2 field is empty, but configured as required [field = custom2][kt|br]
	- [kt|b]custom3_required[/kt|b]: when custom3 field is empty, but configured as required [field = custom3][kt|br]
	- [kt|b]custom4_required[/kt|b]: when custom4 field is empty, but configured as required [field = custom4][kt|br]
	- [kt|b]custom5_required[/kt|b]: when custom5 field is empty, but configured as required [field = custom5][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";
