<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['upload_folder']['title']          = "FTP content uploader";
$lang['plugins']['upload_folder']['description']    = "Provides ability to create videos / albums based on uploaded by FTP file.";
$lang['plugins']['upload_folder']['long_desc']      = "
		This plugin can make uploading video and photo content to your servers much easier. All you need to do is
		upload the files to the server with the required file structure and run the plugin, modifying its settings so
		that it processes the right directories. The plugin will analyze the directories and give you an overview of
		the content found there. During the final launch the selected content will be added to the site.
		[kt|br][kt|br]
		The plugin supports 3 directories for uploading standard videos, premium videos, and photo albums. We recommend
		using the same directories each time as content duplicates are detected based on directories (i.e. when content
		that has already been added and wasn`t deleted is uploaded to the same directory, it is considered duplicate).
		For security reasons, the directories with uploaded files need to be child directories of the root directory of
		your site.
		[kt|br][kt|br]
		Both video directories let you upload single video files to directory root and multiple files into
		subdirectories as well. Subdirectories can contain video files, video description in TXT file, screenshots in
		ZIP archives or as JPG files. You can upload not just video source files but files of individual formats as
		well. When needed, you can upload only source files, or only format files, or any combination of these.
		[kt|br][kt|br]
		Similarly to videos, you can upload photo album files either file by file to directory root, or upload
		subdirectories with multiple files in them. The photos can be uploaded as ZIP archives, or as sets of JPG
		images. Album description in TXT file.
		[kt|br][kt|br]
		After the plugin completes the upload, the uploaded files will not be deleted. You can either keep them
		or delete them manually. When you run the plugin again, files or subdirectories that have already been uploaded
		will be considered duplicate.
";
$lang['permissions']['plugins|upload_folder']       = $lang['plugins']['upload_folder']['title'];

$lang['plugins']['upload_folder']['validation_error_iconv']                         = "[kt|b][%1%][/kt|b]: iconv library is not enabled in PHP distribution, contact host support";
$lang['plugins']['upload_folder']['divider_validation_results']                     = "Folders scan result";
$lang['plugins']['upload_folder']['divider_import_results']                         = "Imported content";
$lang['plugins']['upload_folder']['divider_import_results_none']                    = "No imported content";
$lang['plugins']['upload_folder']['field_folder_standard_videos']                   = "Standard videos folder";
$lang['plugins']['upload_folder']['field_folder_standard_videos_hint']              = "folder path where you upload standard videos";
$lang['plugins']['upload_folder']['field_folder_premium_videos']                    = "Premium videos folder";
$lang['plugins']['upload_folder']['field_folder_premium_videos_hint']               = "folder path where you upload premium videos";
$lang['plugins']['upload_folder']['field_folder_albums']                            = "Albums folder";
$lang['plugins']['upload_folder']['field_folder_albums_hint']                       = "folder path where you upload albums";
$lang['plugins']['upload_folder']['field_video_formats']                            = "Video formats";
$lang['plugins']['upload_folder']['field_video_formats_analyze']                    = "Detect video formats based on postfixes and upload without processing";
$lang['plugins']['upload_folder']['field_video_formats_ignore']                     = "Consider uploaded video files as source files and upload with processing";
$lang['plugins']['upload_folder']['field_video_formats_hint']                       = "whether you want the uploaded video files to be detected as format files (based on postfix correlation) and uploaded without processing, or you want that the uploaded files are always considered as source files with full processing";
$lang['plugins']['upload_folder']['field_video_screenshots']                        = "Video screenshots";
$lang['plugins']['upload_folder']['field_video_screenshots_overview']               = "Consider uploaded screenshots as overview screenshots";
$lang['plugins']['upload_folder']['field_video_screenshots_posters']                = "Consider uploaded screenshots as posters";
$lang['plugins']['upload_folder']['field_video_screenshots_hint']                   = "whether you want the uploaded screenshot files to be added as overview screenshots or as posters";
$lang['plugins']['upload_folder']['field_filenames_encoding']                       = "Filesystem encoding";
$lang['plugins']['upload_folder']['field_filenames_encoding_hint']                  = "if you have file / folder names in non standard character encoding, specify this encoding code (cp1250, cp1251 and etc.)[kt|br]http://en.wikipedia.org/wiki/Character_encoding";
$lang['plugins']['upload_folder']['field_delete_files']                             = "Delete files";
$lang['plugins']['upload_folder']['field_delete_files_yes']                         = "delete files after import";
$lang['plugins']['upload_folder']['field_delete_files_hint']                        = "if this option is enabled, source files will be removed from these folders; this will also allow faster import";
$lang['plugins']['upload_folder']['field_randomize']                                = "Random order";
$lang['plugins']['upload_folder']['field_randomize_yes']                            = "import files in random order";
$lang['plugins']['upload_folder']['field_randomize_hint']                           = "by default content will be imported in alphabetical order; enable this option if you want to import in random order";
$lang['plugins']['upload_folder']['field_content_status']                           = "Content status after import";
$lang['plugins']['upload_folder']['field_content_status_disabled']                  = "Disabled";
$lang['plugins']['upload_folder']['field_content_status_active']                    = "Active";
$lang['plugins']['upload_folder']['field_analyze_result']                           = "Summary";
$lang['plugins']['upload_folder']['field_analyze_result_found_objects']             = "%1% objects found";
$lang['plugins']['upload_folder']['field_analyze_result_existing_objects']          = "%1% of them are duplicates";
$lang['plugins']['upload_folder']['field_analyze_result_errors']                    = "%1% have errors";
$lang['plugins']['upload_folder']['dg_contents_col_import']                         = "Use";
$lang['plugins']['upload_folder']['dg_contents_col_object_type']                    = "Object type";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_std_video']          = "Standard video";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_pre_video']          = "Premium video";
$lang['plugins']['upload_folder']['dg_contents_col_object_type_album']              = "Album";
$lang['plugins']['upload_folder']['dg_contents_col_object_id']                      = "Object ID";
$lang['plugins']['upload_folder']['dg_contents_col_title']                          = "Title";
$lang['plugins']['upload_folder']['dg_contents_col_file_name']                      = "File name";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage']                     = "File info";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_duplicate']           = "Duplicate (ID: %1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_error']               = "Error (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_ignored']             = "Ignored (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_file']         = "Source file (%1%, %2%) - with processing";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_format_file']         = "Format \"%1%\" (%2%, %3%) - without processing";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_screenshots_zip']     = "Screenshots ZIP (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_screenshots']         = "Screenshots (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_main_screenshot']     = "Main screenshot (%1%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_posters_zip']         = "Posters ZIP (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_posters']             = "Posters (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_images_zip']   = "Source images ZIP (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_source_images']       = "Source images (%1% files, %2%)";
$lang['plugins']['upload_folder']['dg_contents_col_file_usage_description']         = "Description file (%1%)";
$lang['plugins']['upload_folder']['dg_contents_errors_no_video_files']              = "No video files";
$lang['plugins']['upload_folder']['dg_contents_errors_no_image_files']              = "No image files";
$lang['plugins']['upload_folder']['dg_contents_errors_unreadable_file']             = "Not enough permissions to read file: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_video_file']          = "Invalid video file: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_zip_file']            = "Invalid ZIP file: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_invalid_image_file']          = "Invalid image file: %1%";
$lang['plugins']['upload_folder']['dg_contents_errors_no_delete_permissions']       = "No permissions to delete files";
$lang['plugins']['upload_folder']['btn_analyze']                                    = "Scan folders";
$lang['plugins']['upload_folder']['btn_import']                                     = "Import selected content";
$lang['plugins']['upload_folder']['btn_back']                                       = "<< Back";
$lang['plugins']['upload_folder']['btn_close']                                      = "Close";
