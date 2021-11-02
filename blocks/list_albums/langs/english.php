<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_albums messages
// =====================================================================================================================

$lang['list_albums']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_albums']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_albums']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_albums']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_albums']['groups']['search']            = "Search albums by text";
$lang['list_albums']['groups']['related']           = "Related albums";
$lang['list_albums']['groups']['connected_videos']  = "Albums connected to a video";
$lang['list_albums']['groups']['display_modes']     = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_albums']['groups']['subselects']        = "Select additional data for each album";
$lang['list_albums']['groups']['access']            = "Limit access to albums";

$lang['list_albums']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_albums']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_albums']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_albums']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_albums']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_albums']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_albums']['params']['skip_categories']                   = "If specified, albums from these categories will never be displayed (comma separated list of category IDs).";
$lang['list_albums']['params']['show_categories']                   = "If specified, only albums from these categories will be displayed (comma separated list of category IDs).";
$lang['list_albums']['params']['skip_tags']                         = "If specified, albums with these tags will never be displayed (comma separated list of tag IDs).";
$lang['list_albums']['params']['show_tags']                         = "If specified, only albums with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_albums']['params']['skip_models']                       = "If specified, albums with these models will never be displayed (comma separated list of model IDs).";
$lang['list_albums']['params']['show_models']                       = "If specified, only albums with these models will be displayed (comma separated list of model IDs).";
$lang['list_albums']['params']['skip_content_sources']              = "If specified, albums from these content sources will never be displayed (comma separated list of content source IDs).";
$lang['list_albums']['params']['show_content_sources']              = "If specified, only albums from these content sources will be displayed (comma separated list of content source IDs).";
$lang['list_albums']['params']['skip_users']                        = "If specified, albums from these users will never be displayed (comma separated list of user IDs).";
$lang['list_albums']['params']['show_users']                        = "If specified, only albums from these users will be displayed (comma separated list of user IDs).";
$lang['list_albums']['params']['show_only_with_description']        = "If specified, only albums with non-empty description are displayed in result.";
$lang['list_albums']['params']['show_only_from_same_country']       = "Enable this option to show only albums uploaded by users from the same country as the current user.";
$lang['list_albums']['params']['show_with_admin_flag']              = "You can specify flag external ID here in order to display only albums which have this flag set as admin flag.";
$lang['list_albums']['params']['skip_with_admin_flag']              = "You can specify flag external ID here in order to skip albums which have this flag set as admin flag.";
$lang['list_albums']['params']['days_passed_from']                  = "Allows filtering by publishing date, e.g. albums added today, yesterday and etc. Specifies the upper limit in number of days passed from today.";
$lang['list_albums']['params']['days_passed_to']                    = "Allows filtering by publishing date, e.g. albums added today, yesterday and etc. Specifies the lower limit in number of days passed from today. Value should be greater than value specified in [kt|b]days_passed_from[/kt|b] block parameter.";
$lang['list_albums']['params']['is_private']                        = "If specified, only albums with these visibility will be displayed.";
$lang['list_albums']['params']['var_title_section']                 = "HTTP parameter, which provides title first characters to filter the list.";
$lang['list_albums']['params']['var_category_dir']                  = "HTTP parameter, which provides category directory. If specified, only albums from category with this directory will be displayed.";
$lang['list_albums']['params']['var_category_id']                   = "HTTP parameter, which provides category ID. If specified, only albums from category with this ID will be displayed.";
$lang['list_albums']['params']['var_category_ids']                  = "HTTP parameter, which provides comma-separated list of category ID. If specified, only albums from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display albums which belong to all these categories at the same time.";
$lang['list_albums']['params']['var_category_group_dir']            = "HTTP parameter, which provides category group directory. If specified, only albums from category group with this directory will be displayed.";
$lang['list_albums']['params']['var_category_group_id']             = "HTTP parameter, which provides category group ID. If specified, only albums from category group with this ID will be displayed.";
$lang['list_albums']['params']['var_category_group_ids']            = "HTTP parameter, which provides comma-separated list of category group ID. If specified, only albums from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display albums which belong to all these category groups at the same time.";
$lang['list_albums']['params']['var_tag_dir']                       = "HTTP parameter, which provides tag directory. If specified, only albums, which have tag with this directory will be displayed.";
$lang['list_albums']['params']['var_tag_id']                        = "HTTP parameter, which provides tag ID. If specified, only albums, which have tag with this ID will be displayed.";
$lang['list_albums']['params']['var_tag_ids']                       = "HTTP parameter, which provides comma-separated list of tag IDs. If specified, only albums which have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display albums which have all these tags at the same time.";
$lang['list_albums']['params']['var_model_dir']                     = "HTTP parameter, which provides model directory. If specified, only albums, which have model with this directory will be displayed.";
$lang['list_albums']['params']['var_model_id']                      = "HTTP parameter, which provides model ID. If specified, only albums, which have model with this ID will be displayed.";
$lang['list_albums']['params']['var_model_ids']                     = "HTTP parameter, which provides comma-separated list of model IDs. If specified, only albums which have models with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display albums which have all these models at the same time.";
$lang['list_albums']['params']['var_model_group_dir']               = "HTTP parameter, which provides model group directory. If specified, only albums from model group with this directory will be displayed.";
$lang['list_albums']['params']['var_model_group_id']                = "HTTP parameter, which provides model group ID. If specified, only albums from model group with this ID will be displayed.";
$lang['list_albums']['params']['var_model_group_ids']               = "HTTP parameter, which provides comma-separated list of model group ID. If specified, only albums from model groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display albums which belong to all these model groups at the same time.";
$lang['list_albums']['params']['var_content_source_dir']            = "HTTP parameter, which provides content source directory. If specified, only albums from content source with this directory will be displayed.";
$lang['list_albums']['params']['var_content_source_id']             = "HTTP parameter, which provides content source ID. If specified, only albums from content source with this ID will be displayed.";
$lang['list_albums']['params']['var_content_source_ids']            = "HTTP parameter, which provides comma-separated list of content source IDs. If specified, only albums from content sources with these IDs will be displayed.";
$lang['list_albums']['params']['var_content_source_group_dir']      = "HTTP parameter, which provides content source group directory. If specified, only albums from content source group with this directory will be displayed.";
$lang['list_albums']['params']['var_content_source_group_id']       = "HTTP parameter, which provides content source group ID. If specified, only albums from content source group with this ID will be displayed.";
$lang['list_albums']['params']['var_content_source_group_ids']      = "HTTP parameter, which provides comma-separated list of content source group IDs. If specified, only albums from content source groups with these IDs will be displayed.";
$lang['list_albums']['params']['var_is_private']                    = "HTTP parameter, which specifies albums with what visibility should be displayed. The following comma-separated values can be passed in the given HTTP parameter: 2 - premium albums,  1 - private albums and 0 - public albums. Overrides is_private parameter.";
$lang['list_albums']['params']['var_post_date_from']                = "HTTP parameter, which provides ability to display only albums published after the date passed in this parameter (YYYY-MM-DD).";
$lang['list_albums']['params']['var_post_date_to']                  = "HTTP parameter, which provides ability to display only albums published before the date passed in this parameter (YYYY-MM-DD).";
$lang['list_albums']['params']['var_custom_flag1']                  = "HTTP parameter, which provides ability to display only albums with the specific value of custom flag #1.";
$lang['list_albums']['params']['var_custom_flag2']                  = "HTTP parameter, which provides ability to display only albums with the specific value of custom flag #2.";
$lang['list_albums']['params']['var_custom_flag3']                  = "HTTP parameter, which provides ability to display only albums with the specific value of custom flag #3.";
$lang['list_albums']['params']['var_search']                        = "HTTP parameter, which provides search string. If specified, only albums, which match this string will be displayed.";
$lang['list_albums']['params']['search_method']                     = "Specifies search method.";
$lang['list_albums']['params']['search_scope']                      = "Configures whether both title and description should be searched.";
$lang['list_albums']['params']['search_redirect_enabled']           = "Enables redirect to album page if result contains only 1 album.";
$lang['list_albums']['params']['search_redirect_pattern']           = "Album page pattern to redirect user if search result contains only 1 album (in this case user will be immediately redirected to this album page). The pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b]. Global [kt|b]Album page URL pattern[/kt|b] will be used by default if this parameter is empty.";
$lang['list_albums']['params']['search_empty_404']                  = "Configures block to show 404 error on empty search results.";
$lang['list_albums']['params']['search_empty_redirect_to']          = "Configures block to redirect to the given URL on empty search results. You can use [kt|b]%QUERY%[/kt|b] token here, which will be replaced with query string.";
$lang['list_albums']['params']['enable_search_on_tags']             = "Enables search on tag title and if any tag with such a title is found - albums with this tag will be displayed in search result. May reduce search performance.";
$lang['list_albums']['params']['enable_search_on_categories']       = "Enables search on category title and if any category with such a title is found - albums from this category will be displayed in search result. May reduce search performance.";
$lang['list_albums']['params']['enable_search_on_models']           = "Enables search on model title and if any model with such a title is found - albums of this model will be displayed in search result. May reduce search performance.";
$lang['list_albums']['params']['enable_search_on_cs']               = "Enables search on content source title and if any content source with such a title is found - albums from this content source will be displayed in search result. May reduce search performance.";
$lang['list_albums']['params']['enable_search_on_custom_fields']    = "Enables search on album custom fields. May reduce search performance.";
$lang['list_albums']['params']['mode_related']                      = "Enables related albums display mode.";
$lang['list_albums']['params']['var_album_dir']                     = "Can be used with [kt|b]mode_related[/kt|b] only. HTTP parameter, which provides album directory to display its related albums.";
$lang['list_albums']['params']['var_album_id']                      = "Can be used with [kt|b]mode_related[/kt|b] only. HTTP parameter, which provides album ID to display its related albums.";
$lang['list_albums']['params']['mode_related_category_group_id']    = "Can be used with [kt|b]mode_related[/kt|b] by categories only. Specify category group ID / external ID to restrict only related albums from this category group.";
$lang['list_albums']['params']['mode_related_model_group_id']       = "Can be used with [kt|b]mode_related[/kt|b] by models only. Specify model group ID / external ID to restrict only related albums from this model group.";
$lang['list_albums']['params']['var_mode_related']                  = "Allows dynamically switch related albums display mode by passing one of the following values in HTTP parameter: [kt|b]1[/kt|b] - by content source, [kt|b]2[/kt|b] - by tags, [kt|b]3[/kt|b] - by categories, [kt|b]4[/kt|b] - by models, [kt|b]5[/kt|b] and [kt|b]6[/kt|b] - by title, [kt|b]7[/kt|b] - by user.";
$lang['list_albums']['params']['mode_connected_video']              = "Shows connected albums for the given video.";
$lang['list_albums']['params']['var_connected_video_dir']           = "Can be used with [kt|b]mode_connected_video[/kt|b] only. HTTP parameter, which provides video directory to display its connected albums.";
$lang['list_albums']['params']['var_connected_video_id']            = "Can be used with [kt|b]mode_connected_video[/kt|b] only. HTTP parameter, which provides video ID to display its connected albums.";
$lang['list_albums']['params']['mode_favourites']                   = "Enables member's favourite albums display mode.";
$lang['list_albums']['params']['mode_uploaded']                     = "Enables member's created albums display mode.";
$lang['list_albums']['params']['mode_purchased']                    = "Enables member's purchased albums display mode.";
$lang['list_albums']['params']['mode_history']                      = "Enables member's visits history display mode.";
$lang['list_albums']['params']['mode_subscribed']                   = "Enables member's subscribed albums display mode.";
$lang['list_albums']['params']['mode_futures']                      = "Enables future (upcoming) albums display mode.";
$lang['list_albums']['params']['fav_type']                          = "Can be used with [kt|b]mode_favourites[/kt|b] only. Type of the displayed bookmarks: 0 - the default bookmarks list, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_albums']['params']['var_fav_type']                      = "Can be used with [kt|b]mode_favourites[/kt|b] only. HTTP parameter, which provides type of the displayed bookmarks: 0 - the default bookmarks list, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_albums']['params']['var_user_id']                       = "Can be used with [kt|b]mode_favourites[/kt|b] and [kt|b]mode_uploaded[/kt|b] and [kt|b]mode_purchased[/kt|b] and [kt|b]mode_history[/kt|b] only. HTTP parameter, which provides ID of a user, whose bookmarks / created albums / purchased albums / visits history should be displayed. If not enabled, block will display bookmarks / created albums / purchased albums / visits history of the current member.";
$lang['list_albums']['params']['redirect_unknown_user_to']          = "Can be used with [kt|b]mode_favourites[/kt|b] and [kt|b]mode_uploaded[/kt|b] and [kt|b]mode_purchased[/kt|b] and [kt|b]mode_history[/kt|b] only. Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own bookmarks / created albums / purchased albums / visits history (in most cases it should point to login page).";
$lang['list_albums']['params']['allow_delete_uploaded_albums']      = "Can be used with [kt|b]mode_uploaded[/kt|b] only. Allows members to delete their own albums.";
$lang['list_albums']['params']['mode_xml']                          = "Enables XML format output [kt|b](obsolete)[/kt|b].";
$lang['list_albums']['params']['show_content_source_info']          = "Enables content source data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_categories_info']              = "Enables categories data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_tags_info']                    = "Enables tags data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_models_info']                  = "Enables models data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_user_info']                    = "Enables user data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_flags_info']                   = "Enables flags data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_image_info']                   = "Enables images data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_main_image_info']              = "Enables main image data loading for every album (reduces performance).";
$lang['list_albums']['params']['show_comments']                     = "Enables ability to display a portion of comments for every album. The number of comments is configured in separate block parameter. Using this parameter will decrease overall block performance.";
$lang['list_albums']['params']['show_comments_count']               = "Can be used with [kt|b]show_comments[/kt|b] block parameter enabled. Specifies the number of comments that are selected for every album.";
$lang['list_albums']['params']['show_private']                      = "Specifies what kinds of members can see private albums in result.";
$lang['list_albums']['params']['show_premium']                      = "Specifies what kinds of members can see premium albums in result.";

$lang['list_albums']['values']['is_private']['0']                                   = "Public only";
$lang['list_albums']['values']['is_private']['1']                                   = "Private only";
$lang['list_albums']['values']['is_private']['2']                                   = "Premium only";
$lang['list_albums']['values']['is_private']['0|1']                                 = "Public and private only";
$lang['list_albums']['values']['is_private']['0|2']                                 = "Public and premium only";
$lang['list_albums']['values']['is_private']['1|2']                                 = "Private and premium only";
$lang['list_albums']['values']['mode_related']['1']                                 = "Related by categories";
$lang['list_albums']['values']['mode_related']['2']                                 = "Related by tags";
$lang['list_albums']['values']['mode_related']['3']                                 = "Related by content source";
$lang['list_albums']['values']['mode_related']['4']                                 = "Related by models";
$lang['list_albums']['values']['mode_related']['5']                                 = "Related by title (natural mode)";
$lang['list_albums']['values']['mode_related']['6']                                 = "Related by title (with query expansion)";
$lang['list_albums']['values']['mode_related']['7']                                 = "Related by user";
$lang['list_albums']['values']['show_private']['1']                                 = "Only registered members";
$lang['list_albums']['values']['show_private']['2']                                 = "Only premium members";
$lang['list_albums']['values']['show_premium']['1']                                 = "Only registered members";
$lang['list_albums']['values']['show_premium']['2']                                 = "Only premium members";
$lang['list_albums']['values']['search_method']['1']                                = "Whole expression match";
$lang['list_albums']['values']['search_method']['2']                                = "Any expression part match";
$lang['list_albums']['values']['search_method']['3']                                = "Full-text index (natural mode)";
$lang['list_albums']['values']['search_method']['4']                                = "Full-text index (boolean mode)";
$lang['list_albums']['values']['search_method']['5']                                = "Full-text index (with query expansion)";
$lang['list_albums']['values']['search_scope']['0']                                 = "Title and description";
$lang['list_albums']['values']['search_scope']['1']                                 = "Title only";
$lang['list_albums']['values']['search_scope']['2']                                 = "Nothing";
$lang['list_albums']['values']['sort_by']['album_id']                               = "Album ID";
$lang['list_albums']['values']['sort_by']['title']                                  = "Title";
$lang['list_albums']['values']['sort_by']['dir']                                    = "Directory";
$lang['list_albums']['values']['sort_by']['photos_amount']                          = "Images count";
$lang['list_albums']['values']['sort_by']['post_date']                              = "Published on";
$lang['list_albums']['values']['sort_by']['post_date_and_popularity']               = "Published on (by popularity)";
$lang['list_albums']['values']['sort_by']['post_date_and_rating']                   = "Published on (by rating)";
$lang['list_albums']['values']['sort_by']['post_date_and_photos_amount']            = "Published on (by images count)";
$lang['list_albums']['values']['sort_by']['last_time_view_date']                    = "Last viewed";
$lang['list_albums']['values']['sort_by']['last_time_view_date_and_popularity']     = "Last viewed (by popularity)";
$lang['list_albums']['values']['sort_by']['last_time_view_date_and_rating']         = "Last viewed (by rating)";
$lang['list_albums']['values']['sort_by']['last_time_view_date_and_photos_amount']  = "Last viewed (by images count)";
$lang['list_albums']['values']['sort_by']['rating']                                 = "Overall rating";
$lang['list_albums']['values']['sort_by']['rating_today']                           = "Rating today";
$lang['list_albums']['values']['sort_by']['rating_week']                            = "Rating this week";
$lang['list_albums']['values']['sort_by']['rating_month']                           = "Rating this month";
$lang['list_albums']['values']['sort_by']['album_viewed']                           = "Overall popularity";
$lang['list_albums']['values']['sort_by']['album_viewed_today']                     = "Popularity today";
$lang['list_albums']['values']['sort_by']['album_viewed_week']                      = "Popularity this week";
$lang['list_albums']['values']['sort_by']['album_viewed_month']                     = "Popularity this month";
$lang['list_albums']['values']['sort_by']['most_favourited']                        = "Most favourited";
$lang['list_albums']['values']['sort_by']['most_commented']                         = "Most commented";
$lang['list_albums']['values']['sort_by']['most_purchased']                         = "Most purchased";
$lang['list_albums']['values']['sort_by']['custom1']                                = "Custom 1";
$lang['list_albums']['values']['sort_by']['custom2']                                = "Custom 2";
$lang['list_albums']['values']['sort_by']['custom3']                                = "Custom 3";
$lang['list_albums']['values']['sort_by']['rand()']                                 = "Random (very slow)";
$lang['list_albums']['values']['sort_by']['pseudo_rand']                            = "Pseudo random (fast)";

if (isset($options))
{
	if ($options['ALBUM_FIELD_1_NAME']!='')
	{
		$lang['list_albums']['values']['sort_by']['custom1'] = $options['ALBUM_FIELD_1_NAME'] . " (" . $lang['list_albums']['values']['sort_by']['custom1']. ")";
	}
	if ($options['ALBUM_FIELD_2_NAME']!='')
	{
		$lang['list_albums']['values']['sort_by']['custom2'] = $options['ALBUM_FIELD_2_NAME'] . " (" . $lang['list_albums']['values']['sort_by']['custom2']. ")";
	}
	if ($options['ALBUM_FIELD_3_NAME']!='')
	{
		$lang['list_albums']['values']['sort_by']['custom3'] = $options['ALBUM_FIELD_3_NAME'] . " (" . $lang['list_albums']['values']['sort_by']['custom3']. ")";
	}
}

$lang['list_albums']['block_short_desc'] = "Displays list of albums with the given options";

$lang['list_albums']['block_desc'] = "
	Block displays list of albums with different sorting and filtering options. This block is a
	regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 8 different display modes for this block:[kt|br]
	1) User's favourite albums. In order to use this mode, you should enable [kt|b]mode_favourites[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display favourite
		albums list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display favourite albums list of the current user ('my favourites'), and if the current user is not logged in -
		user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When
		displaying favourite albums of the current user ('my favourites'), block will also provide ability to remove
		albums from the favourites. Favourites may be subdivided into several lists, which you can display by using
		[kt|b]var_fav_type[/kt|b] block parameter and passing sub-list type in HTTP parameter.[kt|br]
	2) User's created albums. In order to use this mode, you should enable [kt|b]mode_uploaded[/kt|b] block parameter.
		If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display created albums list of the
		user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display created
		albums list of the current user ('my created albums'), and if the current user is not logged in - user will be
		redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying
		created albums of the current user ('my created albums'), block will also provide ability for removing
		albums from the website. By default this functionality is not allowed; in order to allow it you should enable
		[kt|b]allow_delete_uploaded_albums[/kt|b] block parameter.[kt|br]
	3) User's purchased albums (spent tokens). In order to use this mode, you should enable [kt|b]mode_purchased[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display purchased
		albums list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display purchased albums list of the current user ('my purchased albums'), and if the current user is not logged
		in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	4) User's visited albums (history). In order to use this mode, you should enable [kt|b]mode_history[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display visited
		albums list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display visited albums list of the current user ('my visited albums'), and if the current user is not logged
		in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	5) User's subscribed albums. In order to use this mode, you should enable [kt|b]mode_subscribed[/kt|b]
		block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display subscribed
		albums list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
		display subscribed albums list of the current user ('my subscribed albums'), and if the current user is not
		logged in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
		parameter.[kt|br]
	6) Related albums list. In order to use this mode, you should enable [kt|b]mode_related[/kt|b] block parameter,
		which is a choice between several different computation modes. You must also specify either
		[kt|b]var_album_dir[/kt|b] or [kt|b]var_album_id[/kt|b] block parameter, which points to HTTP parameter with
		the current album directory or ID (it is required to calculate its related albums). You can use all filters and
		sorting for this display mode. You can also enable [kt|b]var_mode_related[/kt|b] block parameter if you want
		to dynamically pass computation mode for related albums.[kt|br]
	7) Upcoming albums list. In order to use this mode, you should enable [kt|b]mode_futures[/kt|b] block parameter.[kt|br]
	8) Regular albums list. You can use all filters and sorting for this display mode.
	[kt|br][kt|br]

	If you need to exclude albums from any particular categories or having any particular tags, you
	can use [kt|b]skip_categories[/kt|b] or [kt|b]skip_tags[/kt|b] block parameters. If you want to show
	only albums from any particular categories or having any particular tags, you can use
	[kt|b]show_categories[/kt|b] or [kt|b]show_tags[/kt|b] block parameters.
	[kt|br][kt|br]

	If you want to show only albums with non-empty description, please enable [kt|b]show_only_with_description[/kt|b]
	block parameter.
	[kt|br][kt|br]

	In order to display all albums from a particular category (having a particular tag or model), you need to use
	[kt|b]var_category_dir[/kt|b] or [kt|b]var_category_id[/kt|b] ([kt|b]var_tag_dir[/kt|b] or
	[kt|b]var_tag_id[/kt|b] for tag, [kt|b]var_model_dir[/kt|b] or [kt|b]var_model_id[/kt|b] for model) block
	parameters. In order to display all albums from a particular content source, you need to use
	[kt|b]var_content_source_dir[/kt|b] or [kt|b]var_content_source_id[/kt|b] block parameter.
	[kt|br][kt|br]

	It is possible to enable this block to display only albums added during specific days interval, e.g. today,
	yesterday, last week and etc. The interval can be specified as a pair of [kt|b]days_passed_from[/kt|b] and
	[kt|b]days_passed_to[/kt|b] block parameters.
	[kt|br][kt|br]

	Search on albums can be implemented by using [kt|b]var_search[/kt|b] block parameter. It should
	point to HTTP parameter, which contains query string. One of several different search methods can be
	selected in [kt|b]search_method[/kt|b] block parameter. You can also extend search logic by enabling
	[kt|b]enable_search_on_xxx[/kt|b] block parameters, however enabling them may dramatically decrease
	search performance.
	[kt|br][kt|br]

	You can configure block to show only albums that have titles starting with the given substring
	(or a single character). In this case [kt|b]var_title_section[/kt|b] block parameter should point to
	HTTP parameter, which contains title first characters combination. The combination is case-insensitive.
	Using this option you can create separate albums list for every alphabet letter.
	[kt|br][kt|br]

	It is possible to display albums connected to a video. If you want to do that, you should enable
	[kt|b]mode_connected_video[/kt|b] and one of the [kt|b]var_connected_video_id[/kt|b] or
	[kt|b]var_connected_video_dir[/kt|b] block parameters depending on how video identifier is passed to the page
	(e.g. is it an ID or directory).
	[kt|br][kt|br]

	In order to display related content source, categories, models, tags or user information for each album you should
	enable the corresponding [kt|b]show_xxx_info[/kt|b] block parameters. This will force block to load the required
	data additionally, which will reduce performance.
	[kt|br][kt|br]

	If you need to hide private or premium albums from different kinds of users (unregistered or
	standard users), you can use [kt|b]show_private[/kt|b] or [kt|b]show_premium[/kt|b] block parameters.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block will not be
	cached when displaying favourites / created albums / purchased albums / visited albums of the current user
	('my favourites' / 'my created albums' / 'my purchased albums' / 'my visited albums'). When displaying search
	results block caching may vary depending on query string.
";

$lang['list_albums']['block_examples'] = "
	[kt|b]Display 20 albums per page sorted by publishing date[/kt|b]
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

	[kt|b]Display 10 most popular albums[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = album_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all albums added today[/kt|b]
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

	[kt|b]Display all albums added yesterday[/kt|b]
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

	[kt|b]Display 15 albums with the highest rating during last month in category with directory 'my_category'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rating_month[kt|br]
	- var_category_dir = category_dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category_dir=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 20 albums per page that have title starting with 'a'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- sort_by = title asc[kt|br]
	- var_from = from[kt|br]
	- var_title_section = section[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?section=a
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display albums, which have tag with directory 'my_tag', 20 per page and sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- sort_by = title asc[kt|br]
	- var_from = from[kt|br]
	- var_tag_dir = tag_dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?tag_dir=my_tag
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display favourite albums of a user with ID '287', 20 per page[/kt|b]
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

	[kt|b]Display my own albums 20 per page[/kt|b]
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

	[kt|b]Display 5 related albums for an album with ID '23'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 5[kt|br]
	- mode_related[kt|br]
	- var_album_id = album_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?album_id=23
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display connected albums for a video with directory 'my-video'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 9999[kt|br]
	- mode_connected_video[kt|br]
	- var_connected_video_dir = dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?dir=my-video
	[/kt|code]
";

?>