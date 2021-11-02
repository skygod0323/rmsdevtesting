<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['rotator_reset']['title']          = "Сброс статистики ротатора";
$lang['plugins']['rotator_reset']['description']    = "Позволяет сбросить собранную статистику по ротатору.";
$lang['plugins']['rotator_reset']['long_desc']      = "
		Вы можете использовать этот плагин для сброса всех данных ротатора, кроме весовых матриц распределения кликов.
		Вам необходимо выбрать опции, которые вы хотите сбросить. Фоновая операция может занять некоторое время.
";
$lang['permissions']['plugins|rotator_reset']       = $lang['plugins']['rotator_reset']['title'];

$lang['plugins']['rotator_reset']['field_reset_videos']             = "Сбросить статистику ротатора по видео";
$lang['plugins']['rotator_reset']['field_reset_videos_hint']        = "эта опция обнулит статистику ротатора по всем видео; после этого все видео будут иметь одинаковый CTR";
$lang['plugins']['rotator_reset']['field_reset_screenshots']        = "Сбросить статистику ротатора по скриншотам видео";
$lang['plugins']['rotator_reset']['field_reset_screenshots_hint']   = "эта опция обнулит статистику ротатора по всем скриншотам всех видео; после этого все скриншоты у всех видео будут иметь одинаковый CTR";
$lang['plugins']['rotator_reset']['btn_reset']                      = "Сбросить";
