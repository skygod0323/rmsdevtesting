<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// playlist_view messages
// =====================================================================================================================

$lang['playlist_view']['groups']['object_context']      = $lang['website_ui']['block_group_default_context_object'];
$lang['playlist_view']['groups']['pagination']          = "Playlist videos pagination";
$lang['playlist_view']['groups']['sorting']             = "Playlist videos sorting";
$lang['playlist_view']['groups']['additional_data']     = $lang['website_ui']['block_group_default_additional_data'];

$lang['playlist_view']['params']['var_playlist_dir']            = "URL parameter, which provides playlist directory.";
$lang['playlist_view']['params']['var_playlist_id']             = "URL parameter, which provides playlist ID. Will be used instead of playlist directory if specified and is not empty.";
$lang['playlist_view']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['playlist_view']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['playlist_view']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['playlist_view']['params']['sort_by']                     = $lang['website_ui']['parameter_default_sort_by'];
$lang['playlist_view']['params']['var_sort_by']                 = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['playlist_view']['params']['show_next_and_previous_info'] = "Enables block to load data of both next and previous playlists based on the given criteria.";

$lang['playlist_view']['values']['sort_by']['rating']           = "Rating";
$lang['playlist_view']['values']['sort_by']['video_viewed']     = "Popularity";
$lang['playlist_view']['values']['sort_by']['most_favourited']  = "Most favourited";
$lang['playlist_view']['values']['sort_by']['most_commented']   = "Most commented";
$lang['playlist_view']['values']['sort_by']['added2fav_date']   = "Bookmarked date";
$lang['playlist_view']['values']['sort_by']['rand()']           = "Random";

$lang['playlist_view']['values']['show_next_and_previous_info']['0']   = "By ID";
$lang['playlist_view']['values']['show_next_and_previous_info']['3']   = "By user";

$lang['playlist_view']['block_short_desc'] = "Displays data of a single playlist";

$lang['playlist_view']['block_desc'] = "
	Block displays data of the given playlist (context object) and provides the following functionality:
	[kt|br][kt|br]

	- Rate playlist once from a single IP.[kt|br]
	- Flag playlist once from a single IP.[kt|br]
	- Add more videos to a playlist (only for playlist owner).[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_context_object']}
	[kt|br][kt|br]

	[kt|b]{$lang['playlist_view']['groups']['pagination']} / {$lang['playlist_view']['groups']['sorting']}[/kt|b]
	[kt|br][kt|br]

	This block can display videos added to a playlist, therefore it also behaves as a list block with pagination and
	sorting support. By default all playlist videos are displayed in a single list, but you can render them as a
	paginated list using parameters from these sections.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_user_no']}
";

$lang['playlist_view']['block_examples'] = "
	[kt|b]Display playlist with directory value 'my_playlist'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my_playlist
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display playlist with ID '46'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	- var_playlist_id = id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display playlist with ID '46' and display playlist videos 10 per page sorted by their rating[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- var_playlist_dir = dir[kt|br]
	- var_playlist_id = id[kt|br]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = rating[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=46
	[/kt|code]
";
