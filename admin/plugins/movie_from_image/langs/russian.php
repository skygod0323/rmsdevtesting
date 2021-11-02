<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['movie_from_image']['title']       = "Видео по картинке";
$lang['plugins']['movie_from_image']['description'] = "Позволяет создать видео на основе загруженной картинки.";
$lang['plugins']['movie_from_image']['long_desc']   = "
		Используйте этот плагин для создания видео указанной длительности и с указанным качеством по загруженной
		картинке. Плагин создаст MP4 видеофайл, который в течение указанного времени будет показывать загруженную
		картинку.
";
$lang['permissions']['plugins|movie_from_image']    = $lang['plugins']['movie_from_image']['title'];

$lang['plugins']['movie_from_image']['field_image']         = "Изображение";
$lang['plugins']['movie_from_image']['field_image_hint']    = "изображение в формате JPG, на основе которого будет создаваться видео";
$lang['plugins']['movie_from_image']['field_duration']      = "Длительность видео";
$lang['plugins']['movie_from_image']['field_duration_hint'] = "длительность видео (в секундах)";
$lang['plugins']['movie_from_image']['field_quality']       = "Настройки качества";
$lang['plugins']['movie_from_image']['field_quality_hint']  = "опции ffmpeg для настройки качества";
$lang['plugins']['movie_from_image']['btn_create']          = "Создать";
