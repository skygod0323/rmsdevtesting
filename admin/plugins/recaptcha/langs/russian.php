<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['recaptcha']['title']          = "Google reCAPTCHA";
$lang['plugins']['recaptcha']['description']    = "Интегрирует reCAPTCHA в KVS.";
$lang['plugins']['recaptcha']['long_desc']      = "
		Зайдите на адрес https://www.google.com/recaptcha/admin и добавьте ваш сайт с reCAPTCHA v2. После чего Google
		создаст пару ключей [kt|b]Ключ сайта[/kt|b] и [kt|b]Секретный ключ[/kt|b], которые требуются для данного
		плагина.
";
$lang['permissions']['plugins|recaptcha']    = $lang['plugins']['recaptcha']['title'];

$lang['plugins']['recaptcha']['field_enable']               = "Включить reCAPTCHA";
$lang['plugins']['recaptcha']['field_enable_enabled']       = "включено";
$lang['plugins']['recaptcha']['field_enable_hint']          = "все каптчи KVS будут заменены на reCAPTCHA";
$lang['plugins']['recaptcha']['field_site_key']             = "Ключ сайта";
$lang['plugins']['recaptcha']['field_site_key_hint']        = "скопируйте значение ключа сайта из конфигуратора reCAPTCHA";
$lang['plugins']['recaptcha']['field_secret_key']           = "Секретный ключ";
$lang['plugins']['recaptcha']['field_secret_key_hint']      = "скопируйте значение секретного ключа из конфигуратора reCAPTCHA";
$lang['plugins']['recaptcha']['field_alias_domain']         = "Домен зеркала";
$lang['plugins']['recaptcha']['field_aliases']              = "Зеркала";
$lang['plugins']['recaptcha']['field_aliases_enabled']      = "настроить";
$lang['plugins']['recaptcha']['field_aliases_hint']         = "используйте эту опцию, если вам необходимо указать разные ключи для разных доменов, когда к вашему проекту подключены зеркала";
$lang['plugins']['recaptcha']['btn_save']                   = "Сохранить";
$lang['plugins']['recaptcha']['error_template_not_ready']   = "Ваша версия темы не поддерживает интеграцию с reCAPTCHA. Купите сервис обновления KVS и мы адаптируем вашу тему для поддержки reCAPTCHA.";
