<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['models_autogeneration']['title']          = "Автоподбор моделей";
$lang['plugins']['models_autogeneration']['description']    = "Используется для подбора моделей у видео и альбомов на основе названия, описания и тэгов.";
$lang['plugins']['models_autogeneration']['long_desc']      = "
		Используется для подбора моделей у видео и альбомов на основе полей названия, описания и тэгов. Для того, чтобы
		этот плагин начал работу, вам необходимо создать базу моделей, т.к. плагин не создает новые модели - он лишь
		выбирает из существующих моделей. Поиск моделей осуществляется не только по названиям, но и по всем псевдонимам
		моделей.
";
$lang['permissions']['plugins|models_autogeneration']   = $lang['plugins']['models_autogeneration']['title'];

$lang['plugins']['models_autogeneration']['field_enable_for_videos']            = "Видео";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_disabled']   = "Не обрабатывать";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_always']     = "Обрабатывать каждое видео";
$lang['plugins']['models_autogeneration']['field_enable_for_videos_empty']      = "Обрабатывать только видео, у которых нет явно указанных моделей";
$lang['plugins']['models_autogeneration']['field_enable_for_albums']            = "Альбомы";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_disabled']   = "Не обрабатывать";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_always']     = "Обрабатывать каждый альбом";
$lang['plugins']['models_autogeneration']['field_enable_for_albums_empty']      = "Обрабатывать только альбомы, у которых нет явно указанных моделей";
$lang['plugins']['models_autogeneration']['btn_save']                           = "Сохранить";
