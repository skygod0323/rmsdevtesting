<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// album_view messages
// =====================================================================================================================

$lang['album_view']['groups']['object_context']     = $lang['website_ui']['block_group_default_context_object'];
$lang['album_view']['groups']['pagination']         = "Album images pagination";
$lang['album_view']['groups']['additional_data']    = $lang['website_ui']['block_group_default_additional_data'];
$lang['album_view']['groups']['limit_views']        = "Limit the number of viewed albums from a single IP";

$lang['album_view']['params']['var_album_dir']                  = "URL parameter, which provides album directory.";
$lang['album_view']['params']['var_album_id']                   = "URL parameter, which provides album ID. Will be used instead of album directory if specified and is not empty.";
$lang['album_view']['params']['var_album_image_id']             = "URL parameter, which provides album image ID. If image ID is provided in the URL, block will display this specific image instead of main album image.";
$lang['album_view']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['album_view']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['album_view']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['album_view']['params']['show_next_and_previous_info']    = "Enables block to load data of both next and previous albums based on the given criteria.";
$lang['album_view']['params']['show_stats']                     = "Enables block to load traffic stats for the displayed album (daily views).";
$lang['album_view']['params']['limit_unknown_user']             = "Enables limit for unregistered users. Configures the maximum number of albums (the first number) that can be viewed during time period in seconds (the second number, max 86400 e.g. 1 day).";
$lang['album_view']['params']['limit_member']                   = "Enables limit for standard members. Configures the maximum number of albums (the first number) that can be viewed during time period in seconds (the second number, max 86400 e.g. 1 day).";
$lang['album_view']['params']['limit_premium_member']           = "Enables limit for premium members. Configures the maximum number of albums (the first number) that can be viewed during time period in seconds (the second number, max 86400 e.g. 1 day).";

$lang['album_view']['values']['show_next_and_previous_info']['0']   = "By publishing date";
$lang['album_view']['values']['show_next_and_previous_info']['2']   = "By content source";
$lang['album_view']['values']['show_next_and_previous_info']['3']   = "By user";

$lang['album_view']['block_short_desc'] = "Displays data of a single album";

$lang['album_view']['block_desc'] = "
	Block displays data of the given album (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Rate album once from a single IP.[kt|br]
	- Flag album once from a single IP.[kt|br]
	- Add album to memberzone favourites (only for members).[kt|br]
	- Delete album from memberzone favourites (only for members).[kt|br]
	- Purchase premium access to the album using KVS tokens (only for members).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['album_view']['groups']['pagination']}[/kt|b]
	[kt|br][kt|br]

	Images from an album are typically displayed as thumbs list, therefore this block also behaves as a list block
	with pagination support. By default all album images are displayed in a single list, but you can render them as a
	paginated list using parameters from this section.
	[kt|br][kt|br]

	[kt|b]{$lang['album_view']['groups']['limit_views']}[/kt|b]
	[kt|br][kt|br]

	You can use these options to limit the number of albums that can be viewed by a single IP for any given period
	(e.g. for 1 hour, for 4 hours, up to 24 hours). Enabling this functionality will increase database load.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['album_view']['block_examples'] = "
	[kt|b]Display album with directory value 'my_photo_album'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display image with ID '198' from the album with directory value 'my_photo_album'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	- var_album_image_id = image_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_photo_album&image_id=198
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display image with ID '198' from the album with ID '11'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_album_dir = dir[kt|br]
	- var_album_id = id[kt|br]
	- var_album_image_id = image_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=11&image_id=198
	[/kt|code]
";
