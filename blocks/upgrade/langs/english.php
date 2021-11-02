<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// upgrade messages
// =====================================================================================================================

$lang['upgrade']['groups']['paid_access']    = "Paid access";

$lang['upgrade']['params']['enable_card_payment']   = "Enables web payment method. You must have an activated web billing in [kt|b]Memberzone[/kt|b] section.";
$lang['upgrade']['params']['enable_sms_payment']    = "[kt|b]Obsolete![/kt|b] SMS payment methods are not supported any more.";
$lang['upgrade']['params']['enable_access_codes']   = "Allows members use access codes to upgrade their profiles. You should first create access codes in [kt|b]User accounts generator[/kt|b] plugin.";
$lang['upgrade']['params']['default_access_option'] = "Specifies which access option should be selected by default.";

$lang['upgrade']['values']['default_access_option']['2']    = "Web payment";
$lang['upgrade']['values']['default_access_option']['3']    = "SMS payment (obsolete)";

$lang['upgrade']['block_short_desc'] = "Provides membership type upgrade and tokens purchase functionality";

$lang['upgrade']['block_desc'] = "
	Block provides membership type upgrade and tokens purchase functionality for registered members. This block is
	integrated with KVS payment system and may allow paid upgrades.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]payment_option_required[/kt|b]: when paid access is enabled, but payment option is not selected [field = payment_option][kt|br]
	- [kt|b]access_code_invalid[/kt|b]: when user is trying to use invalid access code [field = access_code][kt|br]
	- [kt|b]card_package_id_required[/kt|b]: when user selected web payment option and didn't select access package [field = card_package_id][kt|br]
	- [kt|b]card_package_id_not_enough_tokens[/kt|b]: when user is trying to purchase access package of [kt|b]Internal Tokens[/kt|b] billing and doesn't have enough tokens [field = card_package_id][kt|br]
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
	and upgrade their profiles.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['upgrade']['block_examples'] = "
	[kt|b]Display membership type upgrade form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- enable_card_payment[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
