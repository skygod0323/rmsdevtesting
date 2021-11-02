<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// video_edit messages
// =====================================================================================================================

$lang['video_edit']['groups']['new_objects']    = $lang['website_ui']['block_group_default_new_objects'];
$lang['video_edit']['groups']['edit_mode']      = $lang['website_ui']['block_group_default_edit_mode'];
$lang['video_edit']['groups']['validation']     = $lang['website_ui']['block_group_default_validation'];
$lang['video_edit']['groups']['functionality']  = $lang['website_ui']['block_group_default_functionality'];
$lang['video_edit']['groups']['navigation']     = $lang['website_ui']['block_group_default_navigation'];

$lang['video_edit']['params']['allow_anonymous']            = "Enables anonymous users to post videos.";
$lang['video_edit']['params']['force_inactive']             = $lang['website_ui']['parameter_default_force_inactive'];
$lang['video_edit']['params']['upload_as_format']           = "Specify video format postfix if you want to avoid conversion for all uploaded videos; in this case the uploaded files will be saved untouched without any conversion under this format. By default all videos will be uploaded as source files, which will trigger full conversion cycle for them.";
$lang['video_edit']['params']['allow_embed']                = "Enables ability for users to upload embed codes.";
$lang['video_edit']['params']['allow_embed_domains']        = "Should be used with [kt|b]allow_embed[/kt|b] parameter. Comma-separated list of domains which are allowed in embed codes. You can use [kt|b]*[/kt|b] symbol to include subdomains, e.g. [kt|b]*.youtube.com[/kt|b] will allow all 3rd level subdomains of youtube.com domain; [kt|b]youtube.*[/kt|b] will allow all 2nd level youtube domains.";
$lang['video_edit']['params']['var_video_id']               = $lang['website_ui']['parameter_default_var_context_object_id'];
$lang['video_edit']['params']['forbid_change']              = $lang['website_ui']['parameter_default_forbid_change'];
$lang['video_edit']['params']['forbid_change_screenshots']  = "Forbids editing video screenshots. Screenshots section will be displayed in read-only mode.";
$lang['video_edit']['params']['force_inactive_on_edit']     = $lang['website_ui']['parameter_default_force_inactive_on_edit'];
$lang['video_edit']['params']['min_duration']               = "Minimum allowed video duration in seconds.";
$lang['video_edit']['params']['max_duration']               = "Maximum allowed video duration in seconds.";
$lang['video_edit']['params']['max_duration_premium']       = "Maximum allowed video duration in seconds for premium users. Overrides [kt|b]max_duration[/kt|b] value.";
$lang['video_edit']['params']['max_duration_webmaster']     = "Maximum allowed video duration in seconds for webmaster users. Overrides [kt|b]max_duration[/kt|b] value.";
$lang['video_edit']['params']['optional_description']       = "Makes description field optional.";
$lang['video_edit']['params']['optional_tags']              = "Makes tags field optional.";
$lang['video_edit']['params']['optional_categories']        = "Makes categories field optional.";
$lang['video_edit']['params']['max_categories']             = "Specifies the maximum number of selected categories at the same time.";
$lang['video_edit']['params']['use_captcha']                = "Enables captcha protection for new video form.";
$lang['video_edit']['params']['set_custom_flag1']           = "Sets the given value into custom flag #1.";
$lang['video_edit']['params']['set_custom_flag2']           = "Sets the given value into custom flag #2.";
$lang['video_edit']['params']['set_custom_flag3']           = "Sets the given value into custom flag #3.";
$lang['video_edit']['params']['redirect_unknown_user_to']   = $lang['website_ui']['parameter_default_redirect_unknown_user_to'];
$lang['video_edit']['params']['redirect_on_new_done']       = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";
$lang['video_edit']['params']['redirect_on_change_done']    = "[kt|b]Obsolete![/kt|b] This parameter is not used with modern AJAX form posting.";

$lang['video_edit']['block_short_desc'] = "Provides creation / editing functionality for videos";

$lang['video_edit']['block_desc'] = "
	Block displays creation / editing form for videos.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_error_codes']}
	[kt|br][kt|br]

	[kt|code]
	- [kt|b]title_required[/kt|b]: when title field is empty [field = title][kt|br]
	- [kt|b]title_minimum[/kt|b]: when title field has length < 5 charachers [field = title][kt|br]
	- [kt|b]description_required[/kt|b]: when description field is empty [field = description][kt|br]
	- [kt|b]tags_required[/kt|b]: when tags field is empty [field = tags][kt|br]
	- [kt|b]category_ids_required[/kt|b]: when categories field is empty [field = category_ids][kt|br]
	- [kt|b]category_ids_maximum[/kt|b]: when more than allowed categories selected [field = category_ids][kt|br]
	- [kt|b]screenshot_invalid_format[/kt|b]: when image file uploaded into screenshot field has invalid format [field = screenshot][kt|br]
	- [kt|b]screenshot_invalid_size[/kt|b]: when image file uploaded into screenshot field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = screenshot][kt|br]
	- [kt|b]tokens_required_integer[/kt|b]: when non-integer value is specified into video cost (in tokens) field [field = tokens_required][kt|br]
	- [kt|b]code_required[/kt|b]: when captcha is enabled and not solved [field = code][kt|br]
	- [kt|b]code_invalid[/kt|b]: when captcha is enabled and solved incorrectly [field = code][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Depending on whether a video file is uploaded from local disk or from URL, or video is added as embed code, different additional errors may happen.
	[kt|br][kt|br]

	For local file upload user must upload a valid video file from the local disk:[kt|br]
	[kt|code]
	- [kt|b]content_required[/kt|b]: when no video file is uploaded [field = content][kt|br]
	- [kt|b]content_filesize_limit[/kt|b]: when the uploaded video file has filesize more than allowed [field = content][kt|br]
	- [kt|b]content_invalid_format[/kt|b]: when the uploaded video file is not of supported format (list of supported formats can be displayed via [kt|b]%1%[/kt|b] token) [field = content][kt|br]
	- [kt|b]content_duration_minimum[/kt|b]: when the uploaded video file duration is smaller than allowed (the minimum supported duration can be displayed via [kt|b]%1%[/kt|b] token) [field = content][kt|br]
	- [kt|b]content_duration_maximum[/kt|b]: when the uploaded video file duration is bigger than allowed (the maximum supported duration can be displayed via [kt|b]%1%[/kt|b] token) [field = content][kt|br]
	- [kt|b]content_duplicate[/kt|b]: when the uploaded video file is a duplicate of already uploaded video [field = content][kt|br]
	- [kt|b]content_unknown_error[/kt|b]: when unidentified error happended during file upload [field = content][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	For URL upload user must specify a valid video file URL:[kt|br]
	[kt|code]
	- [kt|b]url_required[/kt|b]: when no video URL is specified [field = url][kt|br]
	- [kt|b]url_invalid[/kt|b]: when the specified video URL is invalid [field = url][kt|br]
	- [kt|b]url_filesize_limit[/kt|b]: when the uploaded video file has filesize more than allowed [field = url][kt|br]
	- [kt|b]url_invalid_format[/kt|b]: when the uploaded video file is not of supported format (list of supported formats can be displayed via [kt|b]%1%[/kt|b] token) [field = url][kt|br]
	- [kt|b]url_duration_minimum[/kt|b]: when the uploaded video file duration is smaller than allowed (the minimum supported duration can be displayed via [kt|b]%1%[/kt|b] token) [field = url][kt|br]
	- [kt|b]url_duration_maximum[/kt|b]: when the uploaded video file duration is bigger than allowed (the maximum supported duration can be displayed via [kt|b]%1%[/kt|b] token) [field = url][kt|br]
	- [kt|b]url_duplicate[/kt|b]: when the uploaded video file is a duplicate of already uploaded video [field = url][kt|br]
	- [kt|b]url_unknown_error[/kt|b]: when unidentified error happended during URL upload [field = url][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	For embed code user must specify 3 required fields - embed code, duration and screenhost:[kt|br]
	[kt|code]
	- [kt|b]embed_required[/kt|b]: when embed code field is empty [field = embed][kt|br]
	- [kt|b]embed_invalid[/kt|b]: when embed code is invalid [field = embed][kt|br]
	- [kt|b]embed_domain_forbidden[/kt|b]: when embed code refers to domain that is not allowed [field = embed][kt|br]
	- [kt|b]embed_duplicate[/kt|b]: when video with the same embed code already exists [field = embed][kt|br]
	- [kt|b]duration_required[/kt|b]: when duration field is empty [field = duration][kt|br]
	- [kt|b]duration_invalid[/kt|b]: when duration is specified in invalid format [field = duration][kt|br]
	- [kt|b]screenshot_required[/kt|b]: when screenshot field is empty [field = screenshot][kt|br]
	- [kt|b]screenshot_invalid_format[/kt|b]: when image file uploaded into screenshot field has invalid format [field = screenshot][kt|br]
	- [kt|b]screenshot_invalid_size[/kt|b]: when image file uploaded into screenshot field has size less than allowed (minimum size can be displayed via [kt|b]%1%[/kt|b] token) [field = screenshot][kt|br]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_editing_mode']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_no']}
";

$lang['video_edit']['block_examples'] = "
	[kt|b]Display video creation form[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_video_id = id[kt|br]
	- min_duration = 10[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display editing form for video with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_video_id = id[kt|br]
	- min_duration = 10[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?action=change&id=11
	[/kt|code]
";
