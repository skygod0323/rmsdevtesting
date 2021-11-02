<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_playlists messages
// =====================================================================================================================

$lang['list_playlists']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_playlists']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_playlists']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_playlists']['groups']['dynamic_filters']    = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_playlists']['groups']['display_modes']      = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_playlists']['groups']['related']            = "Playlists that contain given video";
$lang['list_playlists']['groups']['subselects']         = "Select additional data for each playlist";
$lang['list_playlists']['groups']['pull_videos']        = "Select videos for each playlist";

$lang['list_playlists']['params']['items_per_page']             = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_playlists']['params']['links_per_page']             = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_playlists']['params']['var_from']                   = $lang['website_ui']['parameter_default_var_from'];
$lang['list_playlists']['params']['var_items_per_page']         = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_playlists']['params']['sort_by']                    = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_playlists']['params']['var_sort_by']                = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_playlists']['params']['is_private']                 = "If specified, only playlists with these visibility will be displayed.";
$lang['list_playlists']['params']['show_only_with_description'] = "If specified, only playlists with non-empty description are displayed in result.";
$lang['list_playlists']['params']['show_only_with_videos']      = "If enabled, only playlists with the given amount of videos are displayed in result.";
$lang['list_playlists']['params']['var_title_section']          = "HTTP parameter, which provides title first characters to filter the list.";
$lang['list_playlists']['params']['var_category_dir']           = "HTTP parameter, which provides category directory. If specified, only playlists from category with this directory will be displayed.";
$lang['list_playlists']['params']['var_category_id']            = "HTTP parameter, which provides category ID. If specified, only playlists from category with this ID will be displayed.";
$lang['list_playlists']['params']['var_category_ids']           = "HTTP parameter, which provides comma-separated list of category IDs. If specified, only playlists from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display playlists which belong to all these categories at the same time.";
$lang['list_playlists']['params']['var_category_group_dir']     = "HTTP parameter, which provides category group directory. If specified, only playlists from category group with this directory will be displayed.";
$lang['list_playlists']['params']['var_category_group_id']      = "HTTP parameter, which provides category group ID. If specified, only playlists from category group with this ID will be displayed.";
$lang['list_playlists']['params']['var_category_group_ids']     = "HTTP parameter, which provides comma-separated list of category group IDs. If specified, only playlists from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display playlists which belong to all these category groups at the same time.";
$lang['list_playlists']['params']['var_tag_dir']                = "HTTP parameter, which provides tag directory. If specified, only playlists, which have tag with this directory will be displayed.";
$lang['list_playlists']['params']['var_tag_id']                 = "HTTP parameter, which provides tag ID. If specified, only playlists, which have tag with this ID will be displayed.";
$lang['list_playlists']['params']['var_tag_ids']                = "HTTP parameter, which provides comma-separated list of tag IDs. If specified, only playlists which have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display playlists which have all these tags at the same time.";
$lang['list_playlists']['params']['var_is_private']             = "HTTP parameter, which specifies playlists with what visibility should be displayed. The following values can be passed in the given HTTP parameter: 1 - private playlists and 0 - public playlists. Overrides is_private parameter.";
$lang['list_playlists']['params']['mode_global']                = "Enables global playlists mode.";
$lang['list_playlists']['params']['mode_related_video']         = "Shows connected playlists for the given video.";
$lang['list_playlists']['params']['var_related_video_dir']      = "HTTP parameter, which provides video directory to display its connected playlists.";
$lang['list_playlists']['params']['var_related_video_id']       = "HTTP parameter, which provides video ID to display its connected playlists.";
$lang['list_playlists']['params']['var_user_id']                = "HTTP parameter, which provides ID of a user, whose playlists should be displayed. If not enabled, block will display playlists of the current member.";
$lang['list_playlists']['params']['redirect_unknown_user_to']   = "Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own playlists (in most cases it should point to login page).";
$lang['list_playlists']['params']['show_categories_info']       = "Enables categories data loading for every playlist (reduces performance).";
$lang['list_playlists']['params']['show_tags_info']             = "Enables tags data loading for every playlist (reduces performance).";
$lang['list_playlists']['params']['show_user_info']             = "Enables user data loading for every playlist (reduces performance).";
$lang['list_playlists']['params']['show_flags_info']            = "Enables flags data loading for every playlist (reduces performance).";
$lang['list_playlists']['params']['show_comments']              = "Enables ability to display a portion of comments for every playlist. The number of comments is configured in separate block parameter. Using this parameter will decrease overall block performance.";
$lang['list_playlists']['params']['show_comments_count']        = "Can be used with [kt|b]show_comments[/kt|b] block parameter enabled. Specifies the number of comments that are selected for every playlist.";
$lang['list_playlists']['params']['pull_videos']                = "Enables ability to display a portion of videos for every playlist. The number of videos and videos sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_playlists']['params']['pull_videos_count']          = "Specifies the number of videos that are selected for every playlist.";
$lang['list_playlists']['params']['pull_videos_sort_by']        = "Specifies sorting for videos that are selected for every playlist.";

$lang['list_playlists']['values']['is_private']['0']                        = "Public only";
$lang['list_playlists']['values']['is_private']['1']                        = "Private only";
$lang['list_playlists']['values']['pull_videos_sort_by']['rating']          = "Rating";
$lang['list_playlists']['values']['pull_videos_sort_by']['video_viewed']    = "Popularity";
$lang['list_playlists']['values']['pull_videos_sort_by']['most_favourited'] = "Most favourited";
$lang['list_playlists']['values']['pull_videos_sort_by']['most_commented']  = "Most commented";
$lang['list_playlists']['values']['pull_videos_sort_by']['added2fav_date']  = "Bookmarked date";
$lang['list_playlists']['values']['pull_videos_sort_by']['rand()']          = "Random";
$lang['list_playlists']['values']['sort_by']['playlist_id']                 = "Playlist ID";
$lang['list_playlists']['values']['sort_by']['title']                       = "Title";
$lang['list_playlists']['values']['sort_by']['total_videos']                = "Total videos";
$lang['list_playlists']['values']['sort_by']['rating']                      = "Rating";
$lang['list_playlists']['values']['sort_by']['playlist_viewed']             = "Popularity";
$lang['list_playlists']['values']['sort_by']['most_commented']              = "Most commented";
$lang['list_playlists']['values']['sort_by']['subscribers_count']           = "Most subscribed";
$lang['list_playlists']['values']['sort_by']['last_content_date']           = "Last updated";
$lang['list_playlists']['values']['sort_by']['added_date']                  = "Creation date";
$lang['list_playlists']['values']['sort_by']['rand()']                      = "Random";

$lang['list_playlists']['block_short_desc'] = "Displays list of playlists with the given options";

$lang['list_playlists']['block_desc'] = "
	Block displays list of playlists with different sorting and filtering options. This block is a regular list block
	with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 3 different display modes for this block:[kt|br]
	1) Global playlists list. In order to enable this mode you should use [kt|b]mode_global[/kt|b] block
	   parameter.[kt|br]
	2) Playlists connected to the given video. In order to enable this mode you should use
	   [kt|b]mode_related_video[/kt|b] block parameter and one of [kt|b]var_related_video_id[/kt|b] or
	   [kt|b]var_related_video_dir[/kt|b] parameters.[kt|br]
	3) User's playlists list. If [kt|b]var_user_id[/kt|b] block parameter is enabled, block will display playlists of
	   the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display playlists
	   of the current user ('my playlists'), and if the current user is not logged in - user will be redirected to the
	   URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying playlists of the
	   current user ('my playlists'), block will also provide ability for removing playlists.
	[kt|br][kt|br]

	If you want to show only playlists with non-empty description, please enable
	[kt|b]show_only_with_description[/kt|b] block parameter.
	[kt|br][kt|br]

	In order to display all playlists from a particular category (having a particular tag) passed dynamically in
	request parameters, you should use one of the [kt|b]var_xxx_dir[/kt|b] or [kt|b]var_xxx_id[/kt|b] block parameters.
	[kt|br][kt|br]

	You can configure block to show only playlists that have titles starting with the given substring (or a single
	character). In this case [kt|b]var_title_section[/kt|b] block parameter should point to HTTP parameter, which
	contains title first characters combination. The combination is case-insensitive. Using this option you can create
	separate playlists list for every alphabet letter.
	[kt|br][kt|br]

	This block allows you displaying some videos for each playlist sorted by your criteria. In order to do that, you
	should enable [kt|b]pull_videos[/kt|b] block parameter and specify the number of videos and sorting using
	[kt|b]pull_videos_count[/kt|b] and [kt|b]pull_videos_sort_by[/kt|b] block parameters. Please note that using this
	feature will reduce block performance, so that you will probably want to increase caching interval for this block.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying playlists of the current user ('my playlists').
";

$lang['list_playlists']['block_examples'] = "
	[kt|b]Display all playlists of a user with ID '287'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my own playlists 10 per page with 4 top rated videos from each playlist[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	- pull_videos[kt|br]
	- pull_videos_count = 4[kt|br]
	- pull_videos_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display top 10 playlists that contain video with ID '234'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rating desc[kt|br]
	- mode_related_video = true[kt|br]
	- var_related_video_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=234
	[/kt|code]
";

?>