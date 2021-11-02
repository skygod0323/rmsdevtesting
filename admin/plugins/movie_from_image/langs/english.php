<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['movie_from_image']['title']       = "Movie from image";
$lang['plugins']['movie_from_image']['description'] = "Provides ability to create a movie from the given image.";
$lang['plugins']['movie_from_image']['long_desc']   = "
		Use this plugin to create videos with custom duration and quality from an image you upload. The plugin will
		create an MP4 video file that will show the uploaded image for the time you specify. You can use such videos in
		hotlink protection settings showing it whenever the protection was triggered.
";
$lang['permissions']['plugins|movie_from_image']    = $lang['plugins']['movie_from_image']['title'];

$lang['plugins']['movie_from_image']['field_image']         = "Source image";
$lang['plugins']['movie_from_image']['field_image_hint']    = "JPG image, which is used to create a movie";
$lang['plugins']['movie_from_image']['field_duration']      = "Movie duration";
$lang['plugins']['movie_from_image']['field_duration_hint'] = "result movie duration (in seconds)";
$lang['plugins']['movie_from_image']['field_quality']       = "Quality settings";
$lang['plugins']['movie_from_image']['field_quality_hint']  = "ffmpeg options for quality settings";
$lang['plugins']['movie_from_image']['btn_create']          = "Create";
