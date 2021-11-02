<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content_sources messages
// =====================================================================================================================

$lang['list_content_sources']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content_sources']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content_sources']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content_sources']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_content_sources']['groups']['subselects']       = "Select additional data for each content source";
$lang['list_content_sources']['groups']['pull_videos']      = "Select videos for each content source";
$lang['list_content_sources']['groups']['pull_albums']      = "Select albums for each content source";

$lang['list_content_sources']['params']['items_per_page']                   = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content_sources']['params']['links_per_page']                   = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content_sources']['params']['var_from']                         = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content_sources']['params']['var_items_per_page']               = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content_sources']['params']['sort_by']                          = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content_sources']['params']['var_sort_by']                      = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content_sources']['params']['show_only_with_screenshot1']       = "If enabled, only content sources with screenshot #1 available are displayed in result.";
$lang['list_content_sources']['params']['show_only_with_screenshot2']       = "If enabled, only content sources with screenshot #2 available are displayed in result.";
$lang['list_content_sources']['params']['show_only_with_description']       = "If enabled, only content sources with non-empty description are displayed in result.";
$lang['list_content_sources']['params']['show_only_with_videos']            = "If enabled, only content sources with the given amount of videos are displayed in result.";
$lang['list_content_sources']['params']['show_only_with_albums']            = "If enabled, only content sources with the given amount of albums are displayed in result.";
$lang['list_content_sources']['params']['show_only_with_albums_or_videos']  = "If enabled, only content sources with the given amount of videos or albums are displayed in result.";
$lang['list_content_sources']['params']['skip_categories']                  = "If specified, content sources from these categories will not be displayed (comma separated list of category IDs).";
$lang['list_content_sources']['params']['show_categories']                  = "If specified, only content sources from these categories will be displayed (comma separated list of category IDs).";
$lang['list_content_sources']['params']['skip_tags']                        = "If specified, content sources with these tags will not be displayed (comma separated list of tag IDs).";
$lang['list_content_sources']['params']['show_tags']                        = "If specified, only content sources with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_content_sources']['params']['skip_content_source_groups']       = "If specified, content sources from these content source groups will not be displayed (comma separated list of content source group IDs).";
$lang['list_content_sources']['params']['show_content_source_groups']       = "If specified, only content sources from these content source groups will be displayed (comma separated list of content source group IDs).";
$lang['list_content_sources']['params']['content_source_group_ids']         = "[kt|b]Obsolete![/kt|b] Use [kt|b]show_content_source_groups[/kt|b] instead.";
$lang['list_content_sources']['params']['var_title_section']                = "URL parameter, which provides title first characters to filter the list.";
$lang['list_content_sources']['params']['var_category_dir']                 = "URL parameter, which provides category directory. If specified, only content sources from category with this directory will be displayed.";
$lang['list_content_sources']['params']['var_category_id']                  = "URL parameter, which provides category ID. If specified, only content sources from category with this ID will be displayed.";
$lang['list_content_sources']['params']['var_category_ids']                 = "URL parameter, which provides comma-separated list of category IDs. If specified, only content sources from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display content sources that belong to all these categories at the same time.";
$lang['list_content_sources']['params']['var_category_group_dir']           = "URL parameter, which provides category group directory. If specified, only content sources from category group with this directory will be displayed.";
$lang['list_content_sources']['params']['var_category_group_id']            = "URL parameter, which provides category group ID. If specified, only content sources from category group with this ID will be displayed.";
$lang['list_content_sources']['params']['var_category_group_ids']           = "URL parameter, which provides comma-separated list of category group IDs. If specified, only content sources from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display content sources that belong to all these category groups at the same time.";
$lang['list_content_sources']['params']['var_tag_dir']                      = "URL parameter, which provides tag directory. If specified, only content sources that have tag with this directory will be displayed.";
$lang['list_content_sources']['params']['var_tag_id']                       = "URL parameter, which provides tag ID. If specified, only content sources that have tag with this ID will be displayed.";
$lang['list_content_sources']['params']['var_tag_ids']                      = "URL parameter, which provides comma-separated list of tag IDs. If specified, only content sources that have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display content sources that have all these tags at the same time.";
$lang['list_content_sources']['params']['var_content_source_group_dir']     = "URL parameter, which provides content source group directory. If specified, only content sources from group with this directory will be displayed.";
$lang['list_content_sources']['params']['var_content_source_group_id']      = "URL parameter, which provides content source group ID. If specified, only content sources from group with this ID will be displayed.";
$lang['list_content_sources']['params']['var_content_source_group_ids']     = "URL parameter, which provides comma-separated list of content source group IDs. If specified, only content sources from groups with these IDs will be displayed.";
$lang['list_content_sources']['params']['show_categories_info']             = "Enables categories data loading for every content source. Using this parameter will decrease overall block performance.";
$lang['list_content_sources']['params']['show_tags_info']                   = "Enables tags data loading for every content source. Using this parameter will decrease overall block performance.";
$lang['list_content_sources']['params']['show_group_info']                  = "Enables content source group data loading for every content source. Using this parameter will decrease overall block performance.";
$lang['list_content_sources']['params']['pull_videos']                      = "Enables ability to display a portion of videos for every content source. The number of videos and videos sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_content_sources']['params']['pull_videos_count']                = "Specifies the number of videos that are selected for every content source.";
$lang['list_content_sources']['params']['pull_videos_sort_by']              = "Specifies sorting for videos that are selected for every content source.";
$lang['list_content_sources']['params']['pull_albums']                      = "Enables ability to display a portion of albums for every content source. The number of albums and albums sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_content_sources']['params']['pull_albums_count']                = "Specifies the number of albums that are selected for every content source.";
$lang['list_content_sources']['params']['pull_albums_sort_by']              = "Specifies sorting for albums that are selected for every content source.";

$lang['list_content_sources']['values']['pull_videos_sort_by']['duration']              = "Duration";
$lang['list_content_sources']['values']['pull_videos_sort_by']['post_date']             = "Published on";
$lang['list_content_sources']['values']['pull_videos_sort_by']['last_time_view_date']   = "Last viewed";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating']                = "Overall rating";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_today']          = "Rating today";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_week']           = "Rating this week";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rating_month']          = "Rating this month";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed']          = "Overall popularity";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_today']    = "Popularity today";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_week']     = "Popularity this week";
$lang['list_content_sources']['values']['pull_videos_sort_by']['video_viewed_month']    = "Popularity this month";
$lang['list_content_sources']['values']['pull_videos_sort_by']['most_favourited']       = "Most favourited";
$lang['list_content_sources']['values']['pull_videos_sort_by']['most_commented']        = "Most commented";
$lang['list_content_sources']['values']['pull_videos_sort_by']['ctr']                   = "CTR (rotator)";
$lang['list_content_sources']['values']['pull_videos_sort_by']['rand()']                = "Random";
$lang['list_content_sources']['values']['pull_albums_sort_by']['photos_amount']         = "Images count";
$lang['list_content_sources']['values']['pull_albums_sort_by']['post_date']             = "Published on";
$lang['list_content_sources']['values']['pull_albums_sort_by']['last_time_view_date']   = "Last viewed";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating']                = "Overall rating";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_today']          = "Rating today";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_week']           = "Rating this week";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rating_month']          = "Rating this month";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed']          = "Overall popularity";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_today']    = "Popularity today";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_week']     = "Popularity this week";
$lang['list_content_sources']['values']['pull_albums_sort_by']['album_viewed_month']    = "Popularity this month";
$lang['list_content_sources']['values']['pull_albums_sort_by']['most_favourited']       = "Most favourited";
$lang['list_content_sources']['values']['pull_albums_sort_by']['most_commented']        = "Most commented";
$lang['list_content_sources']['values']['pull_albums_sort_by']['rand()']                = "Random";
$lang['list_content_sources']['values']['sort_by']['content_source_id']                 = "Content source ID";
$lang['list_content_sources']['values']['sort_by']['sort_id']                           = "Sorting ID";
$lang['list_content_sources']['values']['sort_by']['title']                             = "Title";
$lang['list_content_sources']['values']['sort_by']['rating']                            = "Rating";
$lang['list_content_sources']['values']['sort_by']['cs_viewed']                         = "Popularity";
$lang['list_content_sources']['values']['sort_by']['screenshot1']                       = "Screenshot 1";
$lang['list_content_sources']['values']['sort_by']['screenshot2']                       = "Screenshot 2";
$lang['list_content_sources']['values']['sort_by']['today_videos']                      = "Videos added today";
$lang['list_content_sources']['values']['sort_by']['total_videos']                      = "Total videos";
$lang['list_content_sources']['values']['sort_by']['today_albums']                      = "Albums added today";
$lang['list_content_sources']['values']['sort_by']['total_albums']                      = "Total albums";
$lang['list_content_sources']['values']['sort_by']['avg_videos_rating']                 = "Average video rating";
$lang['list_content_sources']['values']['sort_by']['avg_videos_popularity']             = "Average video popularity";
$lang['list_content_sources']['values']['sort_by']['avg_albums_rating']                 = "Average album rating";
$lang['list_content_sources']['values']['sort_by']['avg_albums_popularity']             = "Average album popularity";
$lang['list_content_sources']['values']['sort_by']['comments_count']                    = "Most commented";
$lang['list_content_sources']['values']['sort_by']['subscribers_count']                 = "Most subscribed";
$lang['list_content_sources']['values']['sort_by']['rank']                              = "Rank";
$lang['list_content_sources']['values']['sort_by']['last_content_date']                 = "Last content added";
$lang['list_content_sources']['values']['sort_by']['added_date']                        = "Creation date";
$lang['list_content_sources']['values']['sort_by']['rand()']                            = "Random";

$lang['list_content_sources']['block_short_desc'] = "Displays list of content sources with the given options";

$lang['list_content_sources']['block_desc'] = "
	Block displays list of content sources with different sorting and filtering options. This block is a standard list
	block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_content_sources']['block_examples'] = "
	[kt|b]Display all content sources sorted alphabetically[/kt|b]
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

	[kt|b]Display content sources with video, 10 per page and sorted by videos count[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = total_videos desc[kt|br]
	- show_only_with_videos[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all content sources that have title starting with 'a'[/kt|b]
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

	[kt|b]Display 15 content sources in category with directory 'my_category' sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = title asc[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display content sources from groups with IDs '15' and '20', 10 per page in random order[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = rand()[kt|br]
	- show_content_source_groups = 15,20[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all content sources from content source group with directory 'my_content_source_group' sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_content_source_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my_content_source_group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 content sources per page with 5 top rated videos for each content source[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
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

	[kt|b]Display all content sources with 10 most popular albums for each content source[/kt|b]
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
