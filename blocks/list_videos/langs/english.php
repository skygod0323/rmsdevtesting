<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_videos messages
// =====================================================================================================================

$lang['list_videos']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_videos']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_videos']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_videos']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_videos']['groups']['search']            = "Search videos by text";
$lang['list_videos']['groups']['related']           = "Related videos";
$lang['list_videos']['groups']['connected_albums']  = "Video connected to an album";
$lang['list_videos']['groups']['display_modes']     = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_videos']['groups']['subselects']        = "Select additional data for each video";
$lang['list_videos']['groups']['access']            = "Limit access to videos";
$lang['list_videos']['groups']['rotator']           = "CTR rotator";

$lang['list_videos']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_videos']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_videos']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_videos']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_videos']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_videos']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_videos']['params']['skip_categories']                   = "If specified, videos from these categories will never be displayed (comma separated list of category IDs).";
$lang['list_videos']['params']['show_categories']                   = "If specified, only videos from these categories will be displayed (comma separated list of category IDs).";
$lang['list_videos']['params']['skip_tags']                         = "If specified, videos with these tags will never be displayed (comma separated list of tag IDs).";
$lang['list_videos']['params']['show_tags']                         = "If specified, only videos with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_videos']['params']['skip_models']                       = "If specified, videos with these models will never be displayed (comma separated list of model IDs).";
$lang['list_videos']['params']['show_models']                       = "If specified, only videos with these models will be displayed (comma separated list of model IDs).";
$lang['list_videos']['params']['skip_content_sources']              = "If specified, videos from these content sources will never be displayed (comma separated list of content source IDs).";
$lang['list_videos']['params']['show_content_sources']              = "If specified, only videos from these content sources will be displayed (comma separated list of content source IDs).";
$lang['list_videos']['params']['skip_dvds']                         = "If specified, videos from these DVDs / channels will never be displayed (comma separated list of DVD / channel IDs).";
$lang['list_videos']['params']['show_dvds']                         = "If specified, only videos from these DVDs / channels will be displayed (comma separated list of DVD / channel IDs).";
$lang['list_videos']['params']['skip_users']                        = "If specified, videos from these users will never be displayed (comma separated list of user IDs).";
$lang['list_videos']['params']['show_users']                        = "If specified, only videos from these users will be displayed (comma separated list of user IDs).";
$lang['list_videos']['params']['show_only_with_description']        = "If specified, only videos with non-empty description are displayed in result.";
$lang['list_videos']['params']['show_only_from_same_country']       = "Enable this option to show only videos uploaded by users from the same country as the current user.";
$lang['list_videos']['params']['show_with_admin_flag']              = "You can specify flag external ID here in order to display only videos which have this flag set as admin flag.";
$lang['list_videos']['params']['skip_with_admin_flag']              = "You can specify flag external ID here in order to skip videos which have this flag set as admin flag.";
$lang['list_videos']['params']['days_passed_from']                  = "Allows filtering by publishing date, e.g. videos added today, yesterday and etc. Specifies the upper limit in number of days passed from today.";
$lang['list_videos']['params']['days_passed_to']                    = "Allows filtering by publishing date, e.g. videos added today, yesterday and etc. Specifies the lower limit in number of days passed from today. Value should be greater than value specified in [kt|b]days_passed_from[/kt|b] block parameter.";
$lang['list_videos']['params']['is_private']                        = "If specified, only videos with these visibility will be displayed.";
$lang['list_videos']['params']['is_hd']                             = "If specified, only HD or non-HD videos will be displayed.";
$lang['list_videos']['params']['format_postfix']                    = "If specified, only videos which have a video file with this postfix will be displayed.";
$lang['list_videos']['params']['var_title_section']                 = "HTTP parameter, which provides title first characters to filter the list.";
$lang['list_videos']['params']['var_category_dir']                  = "HTTP parameter, which provides category directory. If specified, only videos from category with this directory will be displayed.";
$lang['list_videos']['params']['var_category_id']                   = "HTTP parameter, which provides category ID. If specified, only videos from category with this ID will be displayed.";
$lang['list_videos']['params']['var_category_ids']                  = "HTTP parameter, which provides comma-separated list of category IDs. If specified, only videos from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display videos which belong to all these categories at the same time.";
$lang['list_videos']['params']['var_category_group_dir']            = "HTTP parameter, which provides category group directory. If specified, only videos from category group with this directory will be displayed.";
$lang['list_videos']['params']['var_category_group_id']             = "HTTP parameter, which provides category group ID. If specified, only videos from category group with this ID will be displayed.";
$lang['list_videos']['params']['var_category_group_ids']            = "HTTP parameter, which provides comma-separated list of category group IDs. If specified, only videos from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display videos which belong to all these category groups at the same time.";
$lang['list_videos']['params']['var_tag_dir']                       = "HTTP parameter, which provides tag directory. If specified, only videos, which have tag with this directory will be displayed.";
$lang['list_videos']['params']['var_tag_id']                        = "HTTP parameter, which provides tag ID. If specified, only videos, which have tag with this ID will be displayed.";
$lang['list_videos']['params']['var_tag_ids']                       = "HTTP parameter, which provides comma-separated list of tag IDs. If specified, only videos which have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display videos which have all these tags at the same time.";
$lang['list_videos']['params']['var_model_dir']                     = "HTTP parameter, which provides model directory. If specified, only videos, which have model with this directory will be displayed.";
$lang['list_videos']['params']['var_model_id']                      = "HTTP parameter, which provides model ID. If specified, only videos, which have model with this ID will be displayed.";
$lang['list_videos']['params']['var_model_ids']                     = "HTTP parameter, which provides comma-separated list of model IDs. If specified, only videos which have models with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display videos which have all these models at the same time.";
$lang['list_videos']['params']['var_model_group_dir']               = "HTTP parameter, which provides model group directory. If specified, only videos from model group with this directory will be displayed.";
$lang['list_videos']['params']['var_model_group_id']                = "HTTP parameter, which provides model group ID. If specified, only videos from model group with this ID will be displayed.";
$lang['list_videos']['params']['var_model_group_ids']               = "HTTP parameter, which provides comma-separated list of model group ID. If specified, only videos from model groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display videos which belong to all these model groups at the same time.";
$lang['list_videos']['params']['var_content_source_dir']            = "HTTP parameter, which provides content source directory. If specified, only videos from content source with this directory will be displayed.";
$lang['list_videos']['params']['var_content_source_id']             = "HTTP parameter, which provides content source ID. If specified, only videos from content source with this ID will be displayed.";
$lang['list_videos']['params']['var_content_source_ids']            = "HTTP parameter, which provides comma-separated list of content source IDs. If specified, only videos from content sources with these IDs will be displayed.";
$lang['list_videos']['params']['var_content_source_group_dir']      = "HTTP parameter, which provides content source group directory. If specified, only videos from content source group with this directory will be displayed.";
$lang['list_videos']['params']['var_content_source_group_id']       = "HTTP parameter, which provides content source group ID. If specified, only videos from content source group with this ID will be displayed.";
$lang['list_videos']['params']['var_content_source_group_ids']      = "HTTP parameter, which provides comma-separated list of content source group IDs. If specified, only videos from content source groups with these IDs will be displayed.";
$lang['list_videos']['params']['var_dvd_dir']                       = "HTTP parameter, which provides DVD / channel directory. If specified, only videos from DVD / channel with this directory will be displayed.";
$lang['list_videos']['params']['var_dvd_id']                        = "HTTP parameter, which provides DVD / channel ID. If specified, only videos from DVD / channel with this ID will be displayed.";
$lang['list_videos']['params']['var_dvd_ids']                       = "HTTP parameter, which provides comma-separated list of DVD / channel IDs. If specified, only videos from DVDs / channels with these IDs will be displayed.";
$lang['list_videos']['params']['var_dvd_group_dir']                 = "HTTP parameter, which provides DVD / channel group directory. If specified, only videos from DVD / channel group with this directory will be displayed.";
$lang['list_videos']['params']['var_dvd_group_id']                  = "HTTP parameter, which provides DVD / channel group ID. If specified, only videos from DVD / channel group with this ID will be displayed.";
$lang['list_videos']['params']['var_dvd_group_ids']                 = "HTTP parameter, which provides comma-separated list of DVD / channel group IDs. If specified, only videos from DVD / channel groups with these IDs will be displayed.";
$lang['list_videos']['params']['var_is_private']                    = "HTTP parameter, which specifies videos with what visibility should be displayed. The following comma-separated values can be passed in the given HTTP parameter: 2 - premium videos, 1 - private videos and 0 - public videos. Overrides [kt|b]is_private[/kt|b] parameter.";
$lang['list_videos']['params']['var_is_hd']                         = "HTTP parameter, which specifies whether HD or non-HD videos should be displayed. The following values can be passed in the given HTTP parameter: 0 - non-HD videos, 1 - HD videos. Overrides [kt|b]is_hd[/kt|b] parameter.";
$lang['list_videos']['params']['var_post_date_from']                = "HTTP parameter, which provides ability to display only videos published after the date passed in this parameter (YYYY-MM-DD).";
$lang['list_videos']['params']['var_post_date_to']                  = "HTTP parameter, which provides ability to display only videos published before the date passed in this parameter (YYYY-MM-DD).";
$lang['list_videos']['params']['var_duration_from']                 = "HTTP parameter, which provides ability to display only videos with duration greater than or equal to the number passed in this parameter (number of seconds).";
$lang['list_videos']['params']['var_duration_to']                   = "HTTP parameter, which provides ability to display only videos with duration less than the number passed in this parameter (number of seconds).";
$lang['list_videos']['params']['var_release_year_from']             = "HTTP parameter, which provides ability to display only videos with release year greater than or equal to the year passed in this parameter.";
$lang['list_videos']['params']['var_release_year_to']               = "HTTP parameter, which provides ability to display only videos with release year less than the year passed in this parameter.";
$lang['list_videos']['params']['var_custom_flag1']                  = "HTTP parameter, which provides ability to display only videos with the specific value of custom flag #1.";
$lang['list_videos']['params']['var_custom_flag2']                  = "HTTP parameter, which provides ability to display only videos with the specific value of custom flag #2.";
$lang['list_videos']['params']['var_custom_flag3']                  = "HTTP parameter, which provides ability to display only videos with the specific value of custom flag #3.";
$lang['list_videos']['params']['var_search']                        = "HTTP parameter, which provides search string. If specified, only videos, which match this string will be displayed.";
$lang['list_videos']['params']['search_method']                     = "Specifies search method.";
$lang['list_videos']['params']['search_scope']                      = "Configures whether both title and description should be searched.";
$lang['list_videos']['params']['search_redirect_enabled']           = "Enables redirect to video page if result contains only 1 video.";
$lang['list_videos']['params']['search_redirect_pattern']           = "Video page pattern to redirect user if search result contains only 1 video (in this case user will be immediately redirected to this video page). The pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b]. Global [kt|b]Video page URL pattern[/kt|b] will be used by default if this parameter is empty.";
$lang['list_videos']['params']['search_empty_404']                  = "Configures block to show 404 error on empty search results.";
$lang['list_videos']['params']['search_empty_redirect_to']          = "Configures block to redirect to the given URL on empty search results. You can use [kt|b]%QUERY%[/kt|b] token here, which will be replaced with query string.";
$lang['list_videos']['params']['enable_search_on_tags']             = "Enables search on tag title and if any tag with such a title is found - videos with this tag will be displayed in search result. May reduce search performance.";
$lang['list_videos']['params']['enable_search_on_categories']       = "Enables search on category title and if any category with such a title is found - videos from this category will be displayed in search result. May reduce search performance.";
$lang['list_videos']['params']['enable_search_on_models']           = "Enables search on model title and if any model with such a title is found - videos of this model will be displayed in search result. May reduce search performance.";
$lang['list_videos']['params']['enable_search_on_cs']               = "Enables search on content source title and if any content source with such a title is found - videos from this content source will be displayed in search result. May reduce search performance.";
$lang['list_videos']['params']['enable_search_on_dvds']             = "Enables search on dvd title and if any dvd with such a title is found - videos from this dvd will be displayed in search result. May reduce search performance.";
$lang['list_videos']['params']['enable_search_on_custom_fields']    = "Enables search on video custom fields. May reduce search performance.";
$lang['list_videos']['params']['mode_related']                      = "Enables related videos display mode.";
$lang['list_videos']['params']['var_video_dir']                     = "Can be used with [kt|b]mode_related[/kt|b] only. HTTP parameter, which provides video directory to display its related videos.";
$lang['list_videos']['params']['var_video_id']                      = "Can be used with [kt|b]mode_related[/kt|b] only. HTTP parameter, which provides video ID to display its related videos.";
$lang['list_videos']['params']['mode_related_category_group_id']    = "Can be used with [kt|b]mode_related[/kt|b] by categories only. Specify category group ID / external ID to restrict only related videos from this category group.";
$lang['list_videos']['params']['mode_related_model_group_id']       = "Can be used with [kt|b]mode_related[/kt|b] by models only. Specify model group ID / external ID to restrict only related videos from this model group.";
$lang['list_videos']['params']['var_mode_related']                  = "Allows dynamically switch related videos display mode by passing one of the following values in HTTP parameter: [kt|b]1[/kt|b] - by content source, [kt|b]2[/kt|b] - by tags, [kt|b]3[/kt|b] - by categories, [kt|b]4[/kt|b] - by models, [kt|b]5[/kt|b] - by DVD / channel, [kt|b]6[/kt|b] and [kt|b]7[/kt|b] - by title, [kt|b]8[/kt|b] - by user.";
$lang['list_videos']['params']['mode_connected_album']              = "Shows related video (always 1 video, or empty list) for the given album.";
$lang['list_videos']['params']['var_connected_album_dir']           = "Can be used with [kt|b]mode_connected_album[/kt|b] only. HTTP parameter, which provides album directory to display its connected video.";
$lang['list_videos']['params']['var_connected_album_id']            = "Can be used with [kt|b]mode_connected_album[/kt|b] only. HTTP parameter, which provides album ID to display its connected video.";
$lang['list_videos']['params']['mode_favourites']                   = "Enables member's favourite videos display mode.";
$lang['list_videos']['params']['mode_uploaded']                     = "Enables member's uploaded videos display mode.";
$lang['list_videos']['params']['mode_dvd']                          = "Enables member's DVD / channel videos display mode.";
$lang['list_videos']['params']['mode_purchased']                    = "Enables member's purchased videos display mode.";
$lang['list_videos']['params']['mode_history']                      = "Enables member's visits history display mode.";
$lang['list_videos']['params']['mode_subscribed']                   = "Enables member's subscribed videos display mode.";
$lang['list_videos']['params']['mode_futures']                      = "Enables future (upcoming) videos display mode.";
$lang['list_videos']['params']['fav_type']                          = "Can be used with [kt|b]mode_favourites[/kt|b] only. Type of the displayed bookmarks: 0 - the default bookmarks list, 10 - playlist, which ID is passed in [kt|b]var_playlist_id[/kt|b] block parameter, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_videos']['params']['var_fav_type']                      = "Can be used with [kt|b]mode_favourites[/kt|b] only. HTTP parameter, which provides type of the displayed bookmarks: 0 - the default bookmarks list, 10 - playlist, which ID is passed in [kt|b]var_playlist_id[/kt|b] block parameter, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_videos']['params']['var_playlist_id']                   = "Can be used with [kt|b]mode_favourites[/kt|b] only. HTTP parameter, which provides ID of the displayed playlist.";
$lang['list_videos']['params']['var_user_id']                       = "Can be used with [kt|b]mode_favourites[/kt|b] and [kt|b]mode_uploaded[/kt|b] and [kt|b]mode_purchased[/kt|b] and [kt|b]mode_history[/kt|b] only. HTTP parameter, which provides ID of a user, whose bookmarks / uploaded videos / purchased videos / visits history should be displayed. If not enabled, block will display bookmarks / uploaded videos / purchased videos / visits history of the current member.";
$lang['list_videos']['params']['redirect_unknown_user_to']          = "Can be used with [kt|b]mode_favourites[/kt|b] and [kt|b]mode_uploaded[/kt|b] and [kt|b]mode_purchased[/kt|b] and [kt|b]mode_history[/kt|b] only. Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own bookmarks / uploaded videos / purchased videos / visits history (in most cases it should point to login page).";
$lang['list_videos']['params']['allow_delete_uploaded_videos']      = "Can be used with [kt|b]mode_uploaded[/kt|b] only. Allows members to delete their own videos.";
$lang['list_videos']['params']['show_content_source_info']          = "Enables content source data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_categories_info']              = "Enables categories data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_tags_info']                    = "Enables tags data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_models_info']                  = "Enables models data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_dvd_info']                     = "Enables DVD / channel data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_user_info']                    = "Enables user data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_flags_info']                   = "Enables flags data loading for every video (reduces performance).";
$lang['list_videos']['params']['show_comments']                     = "Enables ability to display a portion of comments for every video. The number of comments is configured in separate block parameter. Using this parameter will decrease overall block performance.";
$lang['list_videos']['params']['show_comments_count']               = "Can be used with [kt|b]show_comments[/kt|b] block parameter enabled. Specifies the number of comments that are selected for every video.";
$lang['list_videos']['params']['show_private']                      = "Specifies what kinds of members can see private videos in result.";
$lang['list_videos']['params']['show_premium']                      = "Specifies what kinds of members can see premium videos in result.";
$lang['list_videos']['params']['disable_rotator']                   = "Disable videos and videos screenshots rotator for this block.";
$lang['list_videos']['params']['finished_rotation']                 = "If this parameter is enabled, only videos that have finished their screenshots rotation will be displayed by this block.";
$lang['list_videos']['params']['under_rotation']                    = "If this parameter is enabled, only videos that are still under rotation (screenshots have not yet been rotated) will be displayed by this block.";
$lang['list_videos']['params']['show_best_screenshots']             = "This parameter can be used to force displaying the best screenshots (by CTR) for all videos ignoring the fact whether screenshots rotation is finished for a video or not.";
$lang['list_videos']['params']['randomize_positions']               = "Allows mixing best CTR videos with other videos when sorting list by CTR. Specify comma-separated list of position indexes (first video has index 1) that should be replaced by other videos. For example, [kt|b]1,4,7[/kt|b] configures that first, fourth and seventh video by CTR should be replaced by some other videos based on sorting criteria in [kt|b]randomize_positions_sort_by[/kt|b] parameter.";
$lang['list_videos']['params']['randomize_positions_sort_by']       = "Specifies sorting for videos selected by [kt|b]randomize_positions[/kt|b] parameter.";

$lang['list_videos']['values']['is_private']['0']                               = "Public only";
$lang['list_videos']['values']['is_private']['1']                               = "Private only";
$lang['list_videos']['values']['is_private']['2']                               = "Premium only";
$lang['list_videos']['values']['is_private']['0|1']                             = "Public and private only";
$lang['list_videos']['values']['is_private']['0|2']                             = "Public and premium only";
$lang['list_videos']['values']['is_private']['1|2']                             = "Private and premium only";
$lang['list_videos']['values']['is_hd']['0']                                    = "Non-HD only";
$lang['list_videos']['values']['is_hd']['1']                                    = "HD only";
$lang['list_videos']['values']['search_method']['1']                            = "Whole expression match";
$lang['list_videos']['values']['search_method']['2']                            = "Any expression part match";
$lang['list_videos']['values']['search_method']['3']                            = "Full-text index (natural mode)";
$lang['list_videos']['values']['search_method']['4']                            = "Full-text index (boolean mode)";
$lang['list_videos']['values']['search_method']['5']                            = "Full-text index (with query expansion)";
$lang['list_videos']['values']['search_scope']['0']                             = "Title and description";
$lang['list_videos']['values']['search_scope']['1']                             = "Title only";
$lang['list_videos']['values']['search_scope']['2']                             = "Nothing";
$lang['list_videos']['values']['mode_related']['1']                             = "Related by content source";
$lang['list_videos']['values']['mode_related']['2']                             = "Related by tags";
$lang['list_videos']['values']['mode_related']['3']                             = "Related by categories";
$lang['list_videos']['values']['mode_related']['4']                             = "Related by models";
$lang['list_videos']['values']['mode_related']['5']                             = "Related by DVD / channel";
$lang['list_videos']['values']['mode_related']['6']                             = "Related by title (natural)";
$lang['list_videos']['values']['mode_related']['7']                             = "Related by title (with query expansion)";
$lang['list_videos']['values']['mode_related']['8']                             = "Related by user";
$lang['list_videos']['values']['show_private']['1']                             = "Only registered members";
$lang['list_videos']['values']['show_private']['2']                             = "Only premium members";
$lang['list_videos']['values']['show_premium']['1']                             = "Only registered members";
$lang['list_videos']['values']['show_premium']['2']                             = "Only premium members";
$lang['list_videos']['values']['sort_by']['video_id']                           = "Video ID";
$lang['list_videos']['values']['sort_by']['title']                              = "Title";
$lang['list_videos']['values']['sort_by']['dir']                                = "Directory";
$lang['list_videos']['values']['sort_by']['duration']                           = "Duration";
$lang['list_videos']['values']['sort_by']['release_year']                       = "Release year";
$lang['list_videos']['values']['sort_by']['post_date']                          = "Published on";
$lang['list_videos']['values']['sort_by']['post_date_and_popularity']           = "Published on (by popularity)";
$lang['list_videos']['values']['sort_by']['post_date_and_rating']               = "Published on (by rating)";
$lang['list_videos']['values']['sort_by']['post_date_and_duration']             = "Published on (by duration)";
$lang['list_videos']['values']['sort_by']['last_time_view_date']                = "Last viewed";
$lang['list_videos']['values']['sort_by']['last_time_view_date_and_popularity'] = "Last viewed (by popularity)";
$lang['list_videos']['values']['sort_by']['last_time_view_date_and_rating']     = "Last viewed (by rating)";
$lang['list_videos']['values']['sort_by']['last_time_view_date_and_duration']   = "Last viewed (by duration)";
$lang['list_videos']['values']['sort_by']['rating']                             = "Overall rating";
$lang['list_videos']['values']['sort_by']['rating_today']                       = "Rating today";
$lang['list_videos']['values']['sort_by']['rating_week']                        = "Rating this week";
$lang['list_videos']['values']['sort_by']['rating_month']                       = "Rating this month";
$lang['list_videos']['values']['sort_by']['video_viewed']                       = "Overall popularity";
$lang['list_videos']['values']['sort_by']['video_viewed_today']                 = "Popularity today";
$lang['list_videos']['values']['sort_by']['video_viewed_week']                  = "Popularity this week";
$lang['list_videos']['values']['sort_by']['video_viewed_month']                 = "Popularity this month";
$lang['list_videos']['values']['sort_by']['most_favourited']                    = "Most favourited";
$lang['list_videos']['values']['sort_by']['most_commented']                     = "Most commented";
$lang['list_videos']['values']['sort_by']['most_purchased']                     = "Most purchased";
$lang['list_videos']['values']['sort_by']['ctr']                                = "CTR (rotator)";
$lang['list_videos']['values']['sort_by']['custom1']                            = "Custom 1";
$lang['list_videos']['values']['sort_by']['custom2']                            = "Custom 2";
$lang['list_videos']['values']['sort_by']['custom3']                            = "Custom 3";
$lang['list_videos']['values']['sort_by']['dvd_sort_id']                        = "Sorting ID inside DVD / channel";
$lang['list_videos']['values']['sort_by']['pseudo_rand']                        = "Pseudo random (fast)";
$lang['list_videos']['values']['sort_by']['rand()']                             = "Random (very slow)";
$lang['list_videos']['values']['randomize_positions_sort_by']['post_date']      = "Published on";
$lang['list_videos']['values']['randomize_positions_sort_by']['rating']         = "Overall rating";
$lang['list_videos']['values']['randomize_positions_sort_by']['video_viewed']   = "Overall popularity";
$lang['list_videos']['values']['randomize_positions_sort_by']['random1']        = "Pseudo random (fast)";
$lang['list_videos']['values']['randomize_positions_sort_by']['rand()']         = "Random (very slow)";

if (isset($options))
{
	if ($options['VIDEO_FIELD_1_NAME']!='')
	{
		$lang['list_videos']['values']['sort_by']['custom1'] = $options['VIDEO_FIELD_1_NAME'] . " (" . $lang['list_videos']['values']['sort_by']['custom1']. ")";
	}
	if ($options['VIDEO_FIELD_2_NAME']!='')
	{
		$lang['list_videos']['values']['sort_by']['custom2'] = $options['VIDEO_FIELD_2_NAME'] . " (" . $lang['list_videos']['values']['sort_by']['custom2']. ")";
	}
	if ($options['VIDEO_FIELD_3_NAME']!='')
	{
		$lang['list_videos']['values']['sort_by']['custom3'] = $options['VIDEO_FIELD_3_NAME'] . " (" . $lang['list_videos']['values']['sort_by']['custom3']. ")";
	}
}

$lang['list_videos']['block_short_desc'] = "Displays list of videos with the given options";

$lang['list_videos']['block_desc'] = "
	Block displays list of videos with different sorting and filtering options. This block is a
	regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 9 different display modes for this block:[kt|br]
	1) User's favourite videos. In order to use this mode, you should enable [kt|b]mode_favourites[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display favourite
		videos list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display favourite videos list of the current user ('my favourites'), and if the current user is not logged in -
		user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When
		displaying favourite videos of the current user ('my favourites'), block will also provide ability to remove
		videos from the favourites. Favourites may be subdivided into several lists, which you can display by using
		[kt|b]var_fav_type[/kt|b] block parameter and passing sub-list type in HTTP parameter. In order to display
		favourites from a particular playlist, you should pass playlist ID in HTTP parameter, configured by
		[kt|b]var_playlist_id[/kt|b] block parameter.[kt|br]
	2) User's uploaded videos. In order to use this mode, you should enable [kt|b]mode_uploaded[/kt|b] block parameter.
		If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display uploaded videos list of the
		user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display uploaded
		videos list of the current user ('my uploaded videos'), and if the current user is not logged in - user will be
		redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying
		uploaded videos of the current user ('my uploaded videos'), block will also provide ability for removing
		videos from the website. By default this functionality is not allowed; in order to allow it you should enable
		[kt|b]allow_delete_uploaded_videos[/kt|b] block parameter.[kt|br]
	3) Videos from user's DVD / channel. In order to use this mode, you should enable [kt|b]mode_dvd[/kt|b] and
		[kt|b]var_dvd_id[/kt|b] block parameters. If the current user is not logged in - user will be redirected to the
		URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. Block will also provide ability for
		removing videos from the given DVD / channel.[kt|br]
	4) User's purchased videos (spent tokens). In order to use this mode, you should enable [kt|b]mode_purchased[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display purchased
		videos list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display purchased videos list of the current user ('my purchased videos'), and if the current user is not logged
		in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	5) User's visited videos (history). In order to use this mode, you should enable [kt|b]mode_history[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display visited
		videos list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display visited videos list of the current user ('my visited videos'), and if the current user is not logged
		in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	6) User's subscribed videos. In order to use this mode, you should enable [kt|b]mode_subscribed[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display subscribed
		videos list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display subscribed videos list of the current user ('my subscribed videos'), and if the current user is not
		logged in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	7) Related videos list. In order to use this mode, you should enable [kt|b]mode_related[/kt|b] block parameter,
		which is a choice between several different computation modes. You must also specify either
		[kt|b]var_video_dir[/kt|b] or [kt|b]var_video_id[/kt|b] block parameter, which points to HTTP parameter with
		the current video directory or ID (it is required to calculate its related videos). You can use all filters and
		sorting for this display mode. You can also enable [kt|b]var_mode_related[/kt|b] block parameter if you want
		to dynamically pass computation mode for related videos.[kt|br]
	8) Upcoming videos list. In order to use this mode, you should enable [kt|b]mode_futures[/kt|b] block parameter.[kt|br]
	9) Regular videos list. You can use all filters and sorting for this display mode.
	[kt|br][kt|br]

	If you need to exclude videos from any particular categories, tags, models or etc., you can use
	[kt|b]skip_categories[/kt|b], [kt|b]skip_tags[/kt|b], [kt|b]skip_models[/kt|b] or etc. block parameters. If you
	want to show only videos from any particular categories, tags, models or etc. you can use
	[kt|b]show_categories[/kt|b], [kt|b]show_tags[/kt|b], [kt|b]show_models[/kt|b] or etc. block parameters.
	[kt|br][kt|br]

	If you want to show only videos with non-empty description, please enable [kt|b]show_only_with_description[/kt|b]
	block parameter.
	[kt|br][kt|br]

	In order to display all videos from a particular category (having a particular tag, or model, from a particular
	content source or DVD / channel) passed dynamically in request parameters, you should use one of the
	[kt|b]var_xxx_dir[/kt|b] or [kt|b]var_xxx_id[/kt|b] block parameters.
	[kt|br][kt|br]

	It is possible to enable this block to display only videos added during specific days interval, e.g. today,
	yesterday, last week and etc. The interval can be specified as a pair of [kt|b]days_passed_from[/kt|b] and
	[kt|b]days_passed_to[/kt|b] block parameters.
	[kt|br][kt|br]

	Search on videos can be implemented by using [kt|b]var_search[/kt|b] block parameter. It should
	point to HTTP parameter, which contains query string. One of several different search methods can be
	selected in [kt|b]search_method[/kt|b] block parameter. You can also extend search logic by enabling
	[kt|b]enable_search_on_xxx[/kt|b] block parameters, however enabling them may dramatically decrease
	search performance.
	[kt|br][kt|br]

	You can configure block to show only videos that have titles starting with the given substring
	(or a single character). In this case [kt|b]var_title_section[/kt|b] block parameter should point to
	HTTP parameter, which contains title first characters combination. The combination is case-insensitive.
	Using this option you can create separate videos list for every alphabet letter.
	[kt|br][kt|br]

	It is possible to filter out videos with the specific duration limits. This feature can be used with search or
	other filters. In order to use it you should enable [kt|b]var_duration_from[/kt|b] and / or [kt|b]var_duration_to[/kt|b]
	block parameters.
	[kt|br][kt|br]

	In order to display related content source, categories, models, tags or user information for each video you should
	enable the corresponding [kt|b]show_xxx_info[/kt|b] block parameters. This will force block to load the required
	data additionally, which will reduce performance.
	[kt|br][kt|br]

	If you need to hide private or premium videos from different kinds of users (unregistered or
	standard users), you can use [kt|b]show_private[/kt|b] or [kt|b]show_premium[/kt|b] block parameters.
	[kt|br][kt|br]

	It is possible to display a video connected to an album. If you want to do that, you should enable
	[kt|b]mode_connected_album[/kt|b] and one of the [kt|b]var_connected_album_id[/kt|b] or
	[kt|b]var_connected_album_dir[/kt|b] block parameters depending on how album identifier is passed to the page
	(e.g. is it an ID or directory). In all cases this block will display either 1 related video, or empty list if
	the given album has no related video.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying favourites / uploaded videos / purchased videos / visited videos of the current user
	('my favourites' / 'my uploaded videos' / 'my purchased videos' / 'my visited videos'). When displaying search
	results block caching may vary depending on query string.
";

$lang['list_videos']['block_examples'] = "
	[kt|b]Display 20 videos per page sorted by published date[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 most popular videos[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all videos added today[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- days_passed_from = 0[kt|br]
	- days_passed_to = 1[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all videos added yesterday[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 999999[kt|br]
	- sort_by = post_date desc[kt|br]
	- days_passed_from = 1[kt|br]
	- days_passed_to = 2[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 20 videos per page that have title starting with 'a'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 20 videos per page with duration greater than 3 and less than 5 minutes[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = post_date desc[kt|br]
	- var_duration_from = duration_from[kt|br]
	- var_duration_to = duration_to[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?duration_from=180&duration_to=300
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 15 videos with the highest rating during last week in category with directory 'my_category'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rating_week[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display videos, which have tag with directory 'my_tag', 20 per page and sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- var_tag_dir = tag[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?tag=my_tag
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display favourite videos of a user with ID '287', 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_favourites[kt|br]
	- var_user_id = user_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?user_id=287
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display my own uploaded videos 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_uploaded[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 5 related videos for a video with ID '23'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- mode_related[kt|br]
	- var_video_id = video_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?video_id=23
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display connected video for an album with directory 'my-album'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 1[kt|br]
	- mode_connected_album[kt|br]
	- var_connected_album_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my-album
	[/kt|code]
";

?>