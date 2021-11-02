<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_categories messages
// =====================================================================================================================

$lang['list_categories']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_categories']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_categories']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_categories']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_categories']['groups']['related']           = "Related categories";
$lang['list_categories']['groups']['pull_videos']       = "Select videos for each category";
$lang['list_categories']['groups']['pull_albums']       = "Select albums for each category";

$lang['list_categories']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_categories']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_categories']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_categories']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_categories']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_categories']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_categories']['params']['show_only_with_avatar']             = "If enabled, only categories with avatar available are displayed in result.";
$lang['list_categories']['params']['show_only_without_avatar']          = "If enabled, only categories with no avatar are displayed in result.";
$lang['list_categories']['params']['show_only_with_description']        = "If enabled, only categories with non-empty description are displayed in result.";
$lang['list_categories']['params']['show_only_with_albums']             = "If enabled, only categories with the given amount of albums are displayed in result.";
$lang['list_categories']['params']['show_only_with_videos']             = "If enabled, only categories with the given amount of videos are displayed in result.";
$lang['list_categories']['params']['show_only_with_posts']              = "If enabled, only categories with the given amount of posts are displayed in result.";
$lang['list_categories']['params']['show_only_with_albums_or_videos']   = "If enabled, only categories with the given amount of videos or albums are displayed in result.";
$lang['list_categories']['params']['skip_category_groups']              = "If specified, categories from these category groups will not be displayed (comma separated list of category group IDs).";
$lang['list_categories']['params']['show_category_groups']              = "If specified, only categories from these category groups will be displayed (comma separated list of category group IDs).";
$lang['list_categories']['params']['category_group_ids']                = "[kt|b]Obsolete![/kt|b] Use [kt|b]show_category_groups[/kt|b] instead.";
$lang['list_categories']['params']['var_title_section']                 = "URL parameter, which provides title first characters to filter the list.";
$lang['list_categories']['params']['var_category_group_dir']            = "URL parameter, which provides category group directory. If specified, only categories from group with this directory will be displayed.";
$lang['list_categories']['params']['var_category_group_id']             = "URL parameter, which provides category group ID. If specified, only categories from group with this ID will be displayed.";
$lang['list_categories']['params']['var_category_group_ids']            = "URL parameter, which provides comma-separated list of category group IDs. If specified, only categories from groups with these IDs will be displayed.";
$lang['list_categories']['params']['mode_related']                      = "Enables related categories display mode.";
$lang['list_categories']['params']['var_category_dir']                  = "URL parameter, which provides category directory to display its related categories.";
$lang['list_categories']['params']['var_category_id']                   = "URL parameter, which provides category ID to display its related categories.";
$lang['list_categories']['params']['var_category_ids']                  = "URL parameter, which provides comma-separated list of category IDs to display their related categories. By default will select categories that are related to [kt|b]ANY[/kt|b] category specified in this list. If this list also contains [kt|b]all[/kt|b] keyword, will select categories that are related to [kt|b]ALL[/kt|b] categories in this list.";
$lang['list_categories']['params']['var_mode_related']                  = "Allows dynamically switch related categories display mode by passing one of the following values in URL parameter: [kt|b]1[/kt|b] - by group, [kt|b]2[/kt|b] - by videos, [kt|b]3[/kt|b] - by albums.";
$lang['list_categories']['params']['pull_videos']                       = "Enables ability to display a portion of videos for every category. The number of videos and videos sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_categories']['params']['pull_videos_count']                 = "Specifies the number of videos that are selected for every category.";
$lang['list_categories']['params']['pull_videos_sort_by']               = "Specifies sorting for videos that are selected for every category.";
$lang['list_categories']['params']['pull_videos_duplicates']            = "Enable this option to allow duplicate videos to be selected for different categories.";
$lang['list_categories']['params']['pull_albums']                       = "Enables ability to display a portion of albums for every category. The number of albums and albums sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_categories']['params']['pull_albums_count']                 = "Specifies the number of albums that are selected for every category.";
$lang['list_categories']['params']['pull_albums_sort_by']               = "Specifies sorting for albums that are selected for every category.";
$lang['list_categories']['params']['pull_albums_duplicates']            = "Enable this option to allow duplicate albums to be selected for different categories.";

$lang['list_categories']['values']['mode_related']['1']                             = "Related by group";
$lang['list_categories']['values']['mode_related']['2']                             = "Related by videos";
$lang['list_categories']['values']['mode_related']['3']                             = "Related by albums";
$lang['list_categories']['values']['pull_videos_sort_by']['duration']               = "Duration";
$lang['list_categories']['values']['pull_videos_sort_by']['post_date']              = "Published on";
$lang['list_categories']['values']['pull_videos_sort_by']['last_time_view_date']    = "Last viewed";
$lang['list_categories']['values']['pull_videos_sort_by']['rating']                 = "Overall rating";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_today']           = "Rating today";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_week']            = "Rating this week";
$lang['list_categories']['values']['pull_videos_sort_by']['rating_month']           = "Rating this month";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed']           = "Overall popularity";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_today']     = "Popularity today";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_week']      = "Popularity this week";
$lang['list_categories']['values']['pull_videos_sort_by']['video_viewed_month']     = "Popularity this month";
$lang['list_categories']['values']['pull_videos_sort_by']['most_favourited']        = "Most favourited";
$lang['list_categories']['values']['pull_videos_sort_by']['most_commented']         = "Most commented";
$lang['list_categories']['values']['pull_videos_sort_by']['ctr']                    = "CTR (rotator)";
$lang['list_categories']['values']['pull_videos_sort_by']['rand()']                 = "Random";
$lang['list_categories']['values']['pull_albums_sort_by']['photos_amount']          = "Images count";
$lang['list_categories']['values']['pull_albums_sort_by']['post_date']              = "Published on";
$lang['list_categories']['values']['pull_albums_sort_by']['last_time_view_date']    = "Last viewed";
$lang['list_categories']['values']['pull_albums_sort_by']['rating']                 = "Overall rating";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_today']           = "Rating today";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_week']            = "Rating this week";
$lang['list_categories']['values']['pull_albums_sort_by']['rating_month']           = "Rating this month";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed']           = "Overall popularity";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_today']     = "Popularity today";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_week']      = "Popularity this week";
$lang['list_categories']['values']['pull_albums_sort_by']['album_viewed_month']     = "Popularity this month";
$lang['list_categories']['values']['pull_albums_sort_by']['most_favourited']        = "Most favourited";
$lang['list_categories']['values']['pull_albums_sort_by']['most_commented']         = "Most commented";
$lang['list_categories']['values']['pull_albums_sort_by']['rand()']                 = "Random";
$lang['list_categories']['values']['sort_by']['category_id']                        = "Category ID";
$lang['list_categories']['values']['sort_by']['sort_id']                            = "Sorting ID";
$lang['list_categories']['values']['sort_by']['is_avatar_available']                = "Avatar availability";
$lang['list_categories']['values']['sort_by']['title']                              = "Title";
$lang['list_categories']['values']['sort_by']['dir']                                = "Directory";
$lang['list_categories']['values']['sort_by']['description']                        = "Description";
$lang['list_categories']['values']['sort_by']['today_videos']                       = "Videos added today";
$lang['list_categories']['values']['sort_by']['total_videos']                       = "Total videos";
$lang['list_categories']['values']['sort_by']['today_albums']                       = "Albums added today";
$lang['list_categories']['values']['sort_by']['total_albums']                       = "Total albums";
$lang['list_categories']['values']['sort_by']['today_posts']                        = "Posts added today";
$lang['list_categories']['values']['sort_by']['total_posts']                        = "Total posts";
$lang['list_categories']['values']['sort_by']['avg_videos_rating']                  = "Average video rating";
$lang['list_categories']['values']['sort_by']['avg_videos_popularity']              = "Average video popularity";
$lang['list_categories']['values']['sort_by']['max_videos_ctr']                     = "Video CTR";
$lang['list_categories']['values']['sort_by']['avg_albums_rating']                  = "Average album rating";
$lang['list_categories']['values']['sort_by']['avg_albums_popularity']              = "Average album popularity";
$lang['list_categories']['values']['sort_by']['avg_posts_rating']                   = "Average post rating";
$lang['list_categories']['values']['sort_by']['avg_posts_popularity']               = "Average post popularity";
$lang['list_categories']['values']['sort_by']['rand()']                             = "Random";

$lang['list_categories']['block_short_desc'] = "Displays list of categories with the given options";

$lang['list_categories']['block_desc'] = "
	Block displays list of categories with different sorting and filtering options. This block is a standard list block
	with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]Related categories[/kt|b]
	[kt|br][kt|br]

	You can configure this block to show categories that are similar to some other given category by using a wide range
	of criteria. This is so-called 'related' behavior. You should enable [kt|b]mode_related[/kt|b] parameter and
	additionally you should enable one of the [kt|b]var_category_dir[/kt|b] or [kt|b]var_category_id[/kt|b] parameters.
	In order this functionality to start working, either category ID or category directory should be passed in the URL,
	so that this block knows which category should it base from:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=category-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_categories']['block_examples'] = "
	[kt|b]Display all categories sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display categories that have avatar and videos in them[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_avatar[kt|br]
	- show_only_with_videos[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all categories that have title starting with 'a'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display categories from category groups with IDs '15' and '20', 10 per page and sorted by videos count[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = total_videos desc[kt|br]
	- var_from = from[kt|br]
	- show_category_groups = 15,20[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all categories from category group with directory 'my_category_group' sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_category_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my_category_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all categories with 5 top rated videos for each category[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- pull_videos[kt|br]
	- pull_videos_count = 5[kt|br]
	- pull_videos_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all categories with 10 most popular albums for each category[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- pull_albums[kt|br]
	- pull_albums_count = 10[kt|br]
	- pull_albums_sort_by = album_viewed desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
