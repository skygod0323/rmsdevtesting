<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['users_generator']['title']        = "User accounts generator";
$lang['plugins']['users_generator']['description']  = "Generates set of random user accounts (standard or premium) or access codes using the given options.";
$lang['plugins']['users_generator']['long_desc']    = "
		This plugin will generate the desired amount of user accounts with random usernames / passwords or access codes
		that can be used to upgrade accounts to premium status or get some tokens. You can use it to sell pre-generated
		accounts / access codes in digital stores as an alternative to using payment processors.
		[kt|br][kt|br]
		Access codes allow existing users to upgrade their existing (own) accounts to premium status or get some
		tokens once per each access code. They also allow new users to specify a code during signup and get benefit of
		the specified access code. An existing user can use multiple access codes if they give tokens - in this case
		tokens from all codes will be added to the user's balance. When using access code that upgrades account to
		premium status, user won't be allowed to use another access code while the user's account is premium. User will
		be able to use another access code only after their status is changed back to normal.
";
$lang['permissions']['plugins|users_generator']  = $lang['plugins']['users_generator']['title'];

$lang['plugins']['users_generator']['divider_parameters']                       = "Parameters";
$lang['plugins']['users_generator']['divider_summary_accounts']                 = "Generated accounts";
$lang['plugins']['users_generator']['divider_summary_access_codes']             = "Generated access codes";
$lang['plugins']['users_generator']['field_generate']                           = "Generate";
$lang['plugins']['users_generator']['field_generate_access_codes']              = "Access codes";
$lang['plugins']['users_generator']['field_generate_accounts']                  = "Accounts";
$lang['plugins']['users_generator']['field_amount']                             = "Amount";
$lang['plugins']['users_generator']['field_amount_hint']                        = "the number of user accounts / access codes to be generated";
$lang['plugins']['users_generator']['field_access_type']                        = "Access type";
$lang['plugins']['users_generator']['field_access_type_premium_unlimited']      = "Unlimited premium access";
$lang['plugins']['users_generator']['field_access_type_premium_unlimited_hint'] = "creates premium accounts / access codes with unlimited access";
$lang['plugins']['users_generator']['field_access_type_premium_duration']       = "Premium access for N days";
$lang['plugins']['users_generator']['field_access_type_premium_duration_hint']  = "creates premium accounts / access codes limited to the given amount of days after the first login";
$lang['plugins']['users_generator']['field_access_type_tokens']                 = "Standard access with N tokens";
$lang['plugins']['users_generator']['field_access_type_tokens_hint']            = "creates standard accounts / access codes with the given amount of tokens, which can be spent on purchases within your site";
$lang['plugins']['users_generator']['field_username_length']                    = "Username length";
$lang['plugins']['users_generator']['field_password_length']                    = "Password length";
$lang['plugins']['users_generator']['field_access_code_length']                 = "Access code length";
$lang['plugins']['users_generator']['field_access_code_referral_award']         = "Referral award";
$lang['plugins']['users_generator']['field_access_code_referral_award_hint']    = "specify the amount of tokens that should be awarded to users that referred other users who will use access codes of this type; only makes sense if your site allows referred registrations [kt|br] e.g. specify [kt|b]100[/kt|b] to allow users receive 100 tokens by referring other users to register and pay for this access code at your site";
$lang['plugins']['users_generator']['field_users']                              = "Accounts";
$lang['plugins']['users_generator']['field_users_hint']                         = "list of users identified by username:password";
$lang['plugins']['users_generator']['field_access_codes']                       = "Access codes";
$lang['plugins']['users_generator']['field_access_codes_hint']                  = "list of access codes";
$lang['plugins']['users_generator']['btn_generate']                             = "Generate";
