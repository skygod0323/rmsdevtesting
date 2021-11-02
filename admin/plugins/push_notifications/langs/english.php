<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['push_notifications']['title']         = "$$$ Push notifications";
$lang['plugins']['push_notifications']['description']   = "Revolution in monetization of tube sites.";
$lang['plugins']['push_notifications']['long_desc']     = "
Push notifications is a brand new way of advertising. It provides alternative monetization channel with high revenue using browser's ability to send push notifications. How does it work?
[kt|br][kt|br]
When visitors open your site, their browsers will invite them to subscribe to push notifications from your site. Users who accept the invitation will receive advertising materials directly to their browsers even if they never visit your site again. In comparison to standard ads, push notifications do not affect your site content and usability, since invitation is displayed by browser, not by your site. Also Ad Maven, the advertiser integrated into KVS, is 100% compatible with Google advertising requirements so that your project will not suffer SEO restrictions.
[kt|br][kt|br]
As for monetization, push notifications will bring you passive income for quite a long time thanks to the unique technology they are implemented. Users subscribed on your site will continue receiving advertising materials even if they never come back again. You will continue receiving revenue for a long time even if your site has no traffic anymore.
[kt|br][kt|br]
In order to start working with push notifications in KVS you should first sign up with Ad Maven. You will be able to create push notifications spot only if you register your account from KVS, since this option is not publicly available. Advertiser has a list of requirements that are already integrated into KVS. [kt|b]IMPORTANT![/kt|b] Depending on whether your website works under HTTPS or HTTP you should create different tags in Ad Maven publisher panel. For HTTPS site you should create tag of [kt|b]Native Push[/kt|b] type - these are 100% native push notifications; for HTTP site you should create tag of [kt|b]Push In Page[/kt|b] type, which will emulate native push notifications with an additional dialog.
[kt|br][kt|br]
After enabling this plugin, you should see invitation to receive push notifications on every page of your site within 20 minutes or so. You can use other plugin's options to adjust invitation behavior, its frequency and even disable it for some referrers. Disabling push notifications may be required for some traffic brokers if you buy traffic for your site.
[kt|br][kt|br]
If you have already started using push notifications from Ad Maven you can still switch to this plugin for having more controls. Ad Maven guarantees the same rates for all KVS users and other publishers.
";
$lang['permissions']['plugins|push_notifications']      = $lang['plugins']['push_notifications']['title'];

$lang['plugins']['push_notifications']['field_enable']                  = "Enable push notifications";
$lang['plugins']['push_notifications']['field_enable_enabled']          = "enabled";
$lang['plugins']['push_notifications']['field_enable_hint']             = "your site will start showing push notifications within 20 minutes or so";
$lang['plugins']['push_notifications']['field_refid']                   = "Tag ID";
$lang['plugins']['push_notifications']['field_refid_sign_up']           = "Sign up with Ad Maven";
$lang['plugins']['push_notifications']['field_refid_hint_http']         = "in Ad Maven publisher panel create tag for your site with [kt|b]Push In Page[/kt|b] type; then insert ID, that is printed in tag embed code: [kt|br] <script data-cfasync=\"false\" src=\"//d2d8qsxiai9qwj.cloudfront.net/?xsqdd=[kt|b]Tag_ID_number[/kt|b]\"></script>";
$lang['plugins']['push_notifications']['field_refid_hint_https']        = "in Ad Maven publisher panel create tag for your site with [kt|b]Native Push[/kt|b] type; then insert ID, that is printed in tag embed code: [kt|br] <script data-cfasync=\"false\" src=\"//d2d8qsxiai9qwj.cloudfront.net/?xsqdd=[kt|b]Tag_ID_number[/kt|b]\"></script>";
$lang['plugins']['push_notifications']['field_repeat']                  = "Repeat";
$lang['plugins']['push_notifications']['field_repeat_always']           = "Repeat on every page";
$lang['plugins']['push_notifications']['field_repeat_interval']         = "Repeat after each ...";
$lang['plugins']['push_notifications']['field_repeat_interval_minutes'] = "minutes";
$lang['plugins']['push_notifications']['field_repeat_once']             = "Show only once";
$lang['plugins']['push_notifications']['field_repeat_hint']             = "select how often a single user should be proposed to receive push notifications";
$lang['plugins']['push_notifications']['field_first_click']             = "First visit";
$lang['plugins']['push_notifications']['field_first_click_skip']        = "do not show on the first visit";
$lang['plugins']['push_notifications']['field_first_click_hint']        = "whether user should not be proposed to allow push notifications on the first visit to your site; then any further click within your site will trigger push notifications proposal";
$lang['plugins']['push_notifications']['field_exclude_referers']        = "Exclude referrers";
$lang['plugins']['push_notifications']['field_exclude_referers_hint']   = "specify list of referrers to exclude push notifications from their traffic; each referrer on a new line using the following rules: [kt|br] - start referrer with [kt|b]http://[/kt|b] if you want to exclude traffic from specific domains (please do not specify www as it will be checked automatically), for example [kt|b]http://google.[/kt|b] will exclude traffic from [kt|b]google.com[/kt|b] and [kt|b]google.eu[/kt|b] and etc.; [kt|br] - specify referrer without [kt|b]http://[/kt|b] if you want to exclude traffic from specific URLs on your site, for example [kt|b]utm_source=adwords[/kt|b] will exclude traffic that comes to your site with [kt|b]utm_source=adwords[/kt|b] URL parameters";
$lang['plugins']['push_notifications']['field_include_referers']        = "Include referrers";
$lang['plugins']['push_notifications']['field_include_referers_hint']   = "specify list of referrers to only show push notifications to their traffic; each referrer on a new line using the following rules: [kt|br] - start referrer with [kt|b]http://[/kt|b] if you want to include traffic from specific domains (please do not specify www as it will be checked automatically), for example [kt|b]http://google.[/kt|b] will include traffic from [kt|b]google.com[/kt|b] and [kt|b]google.eu[/kt|b] and etc.; [kt|br] - specify referrer without [kt|b]http://[/kt|b] if you want to include traffic from specific URLs on your site, for example [kt|b]utm_source=adwords[/kt|b] will include traffic that comes to your site with [kt|b]utm_source=adwords[/kt|b] URL parameters";
$lang['plugins']['push_notifications']['field_exclude_members']         = "Exclude members";
$lang['plugins']['push_notifications']['field_exclude_members_none']    = "None";
$lang['plugins']['push_notifications']['field_exclude_members_all']     = "All members";
$lang['plugins']['push_notifications']['field_exclude_members_premium'] = "Premium members";
$lang['plugins']['push_notifications']['field_exclude_members_hint']    = "specify if you want to exclude members or premium members from showing push notifications";
$lang['plugins']['push_notifications']['field_js_library']              = "JS library";
$lang['plugins']['push_notifications']['field_js_library_download']     = "Download file";
$lang['plugins']['push_notifications']['field_js_library_hint']         = "download JS library file (sw.js) and copy it to the document root of your project via FTP";
$lang['plugins']['push_notifications']['error_missing_library']         = "[kt|b][%1%][/kt|b]: JS library file sw.js is not found under the document root of your project";
$lang['plugins']['push_notifications']['btn_save']                      = "Save";
