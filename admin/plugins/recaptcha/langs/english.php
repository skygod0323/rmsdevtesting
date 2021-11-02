<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['recaptcha']['title']       = "Google reCAPTCHA";
$lang['plugins']['recaptcha']['description'] = "Integrates reCAPTCHA into KVS.";
$lang['plugins']['recaptcha']['long_desc']   = "
		Go to https://www.google.com/recaptcha/admin and register your site with reCAPTCHA v2. Then Google will
		generate [kt|b]Site key[/kt|b] and [kt|b]Secret key[/kt|b] pair, that are needed for this plugin.
";
$lang['permissions']['plugins|recaptcha']    = $lang['plugins']['recaptcha']['title'];

$lang['plugins']['recaptcha']['field_enable']               = "Enable reCAPTCHA";
$lang['plugins']['recaptcha']['field_enable_enabled']       = "enabled";
$lang['plugins']['recaptcha']['field_enable_hint']          = "all KVS captcha will be replaced with reCAPTCHA";
$lang['plugins']['recaptcha']['field_site_key']             = "Site key";
$lang['plugins']['recaptcha']['field_site_key_hint']        = "copy-paste site key value from reCAPTCHA configurator";
$lang['plugins']['recaptcha']['field_secret_key']           = "Secret key";
$lang['plugins']['recaptcha']['field_secret_key_hint']      = "copy-paste secret key value from reCAPTCHA configurator";
$lang['plugins']['recaptcha']['field_alias_domain']         = "Alias domain";
$lang['plugins']['recaptcha']['field_aliases']              = "Aliases";
$lang['plugins']['recaptcha']['field_aliases_enabled']      = "configure";
$lang['plugins']['recaptcha']['field_aliases_hint']         = "use this option to specify separate keys for different domains if your project has aliases";
$lang['plugins']['recaptcha']['btn_save']                   = "Save";
$lang['plugins']['recaptcha']['error_template_not_ready']   = "Your theme version does not support reCAPTCHA integration. Please purchase KVS update service and we will adjust your theme for using reCAPTCHA.";
