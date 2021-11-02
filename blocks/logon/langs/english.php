<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// logon messages
// =====================================================================================================================

$lang['logon']['groups']['functionality']   = $lang['website_ui']['block_group_default_functionality'];
$lang['logon']['groups']['limitation']      = "Login limitation";
$lang['logon']['groups']['multilogin']      = "Protection from login sharing";

$lang['logon']['params']['redirect_to']                     = "Specifies URL, which should be used to redirect users after they are logged in. You can use token [kt|b]%USER_ID%[/kt|b] if you want to redirect to the URL that requires member ID to be passed. By default will redirect to the previous page or to the index page.";
$lang['logon']['params']['notify_to']                       = "Specifies URL, which should be used to log / notify every successful login. You can use the following tokens here: [kt|b]%USER_ID%[/kt|b], [kt|b]%USERNAME%[/kt|b], [kt|b]%EMAIL%[/kt|b], [kt|b]%IP%[/kt|b] and [kt|b]%AGENT%[/kt|b].";
$lang['logon']['params']['use_captcha']                     = "Enables captcha protection for this form.";
$lang['logon']['params']['enable_brute_force_protection']   = "Enables brute force password attack protection.";
$lang['logon']['params']['remember_me']                     = "If enabled, members can choose that their session is remembered for the specified amount of days.";
$lang['logon']['params']['single_sign_on']                  = "Enables support for remote login without password (SSO) and specifies secret key that should be used in SSO signature. See block documentation for usage details.";
$lang['logon']['params']['allow_only_premium']              = "Allows only premium members to log in via this login block.";
$lang['logon']['params']['allow_only_webmasters']           = "Allows only webmaster members to log in via this login block.";
$lang['logon']['params']['ban_by_ips']                      = "Enables memberzone protection by banning accounts that have been used from the specified number of different IPs during the specified period of time (in seconds). Specify the number of unique IPs / period in seconds.";
$lang['logon']['params']['ban_by_ip_masks']                 = "Enables memberzone protection by banning accounts that have been used from the specified number of different IP masks during the specified period of time (in seconds). Specify the number of unique IP masks / period in seconds.";
$lang['logon']['params']['ban_by_countries']                = "Enables memberzone protection by banning accounts that have been used from the specified number of different countries during the specified period of time (in seconds). Specify the number of unique countries / period in seconds.";
$lang['logon']['params']['ban_by_browsers']                 = "Enables memberzone protection by banning accounts that have been used from the specified number of different browsers during the specified period of time (in seconds). Specify the number of unique browsers / period in seconds.";
$lang['logon']['params']['ban_type']                        = "Specifies ban type.";
$lang['logon']['params']['ban_count']                       = "Specifies the maximum number of temporary bans before forcing permanent ban. You should use temporary option in [kt|b]ban_type[/kt|b] parameter then.";

$lang['logon']['values']['ban_type']['0']   = "Permanent";
$lang['logon']['values']['ban_type']['1']   = "Temporary";

$lang['logon']['block_short_desc'] = "Provides members login functionality";

$lang['logon']['block_desc'] = "
	Block displays login form and provides login functionality for registered members.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]username_required[/kt|b]: when username field is empty [field = username][kt|br]
	- [kt|b]pass_required[/kt|b]: when password field is empty [field = pass][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	- [kt|b]please_wait[/kt|b]: when bruteforce protection gets triggered and user has to wait for 5 minutes before the next try[kt|br]
	- [kt|b]invalid_login[/kt|b]: when the entered username / password pair is not valid[kt|br]
	- [kt|b]not_confirmed[/kt|b]: when user's account is not confirmed via email[kt|br]
	- [kt|b]disabled_login[/kt|b]: when user's account is disabled[kt|br]
	- [kt|b]tempbanned_login[/kt|b]: when user's account is banned by temporary ban and user can unblock by clicking link sent via email[kt|br]
	- [kt|b]banned_login[/kt|b]: when user's account is permanently banned[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['functionality']}[/kt|b]
	[kt|br][kt|br]

	After logging in, members will be redirected to the URL specified in [kt|b]redirect_to[/kt|b] parameter. If this
	parameter is not set, members will be redirected to their previous page before login or to the index page.
	[kt|br][kt|br]

	Members that close their browsers will be logged off automatically, since their session will be destroyed. If you
	want to allow members keeping their login active for many days long, you should enable [kt|b]remember_me[/kt|b]
	parameter and specify the number of days there.
	[kt|br][kt|br]

	It is possible to use 3rd-party login analyzer software by configuring login block to log / notify every successful
	login to the URL configured in [kt|b]notify_to[/kt|b] parameter. You may need this if using NATS for managing your
	members:[kt|br]
	[kt|code]http://tmmwiki.com/index.php/Nats4_Member_Logging[/kt|code]
	[kt|br][kt|br]

	[kt|b]Single Sign-on (SSO)[/kt|b]
	[kt|br][kt|br]

	Logon block can be used to auto-login users from 3rd-party apps by asking them to follow Single Sign-on link to
	your site. In order to enable this functionality you should first enable [kt|b]single_sign_on[/kt|b] parameter in
	this block and set a secret key. This secret key will be used to generate SSO secure links for your users.
	[kt|br][kt|br]

	The important feature of SSO is that there is no need to add users to KVS before they can actually be logged in. 
	When users open SSO link in their browser, KVS will validate the link security and will automatically log in the 
	user that has been encrypted in this link. If there is no such user exist in KVS database, they will be 
	automatically created in a transparent way. This is very convenient if you need to allow your forum or blog users 
	to access KVS memberzone functionality - in this case they will only need to use SSO link to open KVS website and
	they will automatically get into their KVS profiles. However there are cons as well. Such users won't be able to
	access their KVS profiles using password, they will need to log in to your app first and then navigate to KVS using
	new SSO link.
	[kt|br][kt|br]

	Use the following meta code to generate SSO link from your 3rd-party app:[kt|br]

	[kt|code]
	\$username = 'admin';[kt|br]
	\$email = 'admin@site.com';[kt|br]
	\$time = time();[kt|br]
	\$secret_key = 'secretkey';[kt|br]
	\$sso_token = [[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'username' => \$username,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'email' => \$email,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'token' => \$time,[kt|br]
	[kt|sp][kt|sp][kt|sp][kt|sp]'digest' => md5(\$username . \$time . \$secret_key)[kt|br]
	];[kt|br]
	echo \"https://domain.com/test.php?sso=\" . base64_encode(json_encode(\$sso_token));[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['limitation']}[/kt|b]
	[kt|br][kt|br]

	If you have different types of users that require individual login forms, you can use parameters from this section
	to limit login form to specific types of members only. For example you may need a separate login page for
	webmasters that are allowed to upload content to your site, while other members are not allowed to upload. It is
	even possible to move webmaster-specific functionality to a subdomain, say [kt|b]webmasters.domain.com[/kt|b].
	[kt|br][kt|br]

	[kt|b]{$lang['logon']['groups']['multilogin']}[/kt|b]
	[kt|br][kt|br]

	KVS provides strong memberzone protection from members that are sharing their accounts between different people.
	Use [kt|b]ban_by_xxx[/kt|b] parameters in order to configure ban criteria (e.g. ban if more than 3 different
	countries during 3600 seconds, ban if more than 3 different browsers during 3600 seconds and etc). You can combine
	all available criteria as you like; any of them will trigger ban. There are 2 kinds of ban possible: temporary ban
	with sending email to member and asking to click the unblocking link; and permanent ban when manual admin
	intervention is required. If you use temporary ban type and additionally enter non-zero value into
	[kt|b]ban_count[/kt|b] parameter, this will enforce permanent ban after user has tried unblocking for the given
	number of times.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";
