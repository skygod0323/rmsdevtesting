<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content messages
// =====================================================================================================================

$lang['list_content']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_content']['groups']['search']           = "Search content by text";
$lang['list_content']['groups']['display_modes']    = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_content']['groups']['subselects']       = "Select additional data for each content";
$lang['list_content']['groups']['access']           = "Limit access to content";

$lang['list_content']['params']['items_per_page']               = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content']['params']['links_per_page']               = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content']['params']['var_from']                     = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content']['params']['var_items_per_page']           = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content']['params']['sort_by']                      = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content']['params']['var_sort_by']                  = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content']['params']['skip_categories']              = "If specified, content from these categories will never be displayed (comma separated list of category IDs).";
$lang['list_content']['params']['show_categories']              = "If specified, only content from these categories will be displayed (comma separated list of category IDs).";
$lang['list_content']['params']['skip_tags']                    = "If specified, content with these tags will never be displayed (comma separated list of tag IDs).";
$lang['list_content']['params']['show_tags']                    = "If specified, only content with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_content']['params']['skip_content_sources']         = "If specified, content from these content sources will never be displayed (comma separated list of content source IDs).";
$lang['list_content']['params']['show_content_sources']         = "If specified, only content from these content sources will be displayed (comma separated list of content source IDs).";
$lang['list_content']['params']['show_only_with_description']   = "If specified, only content with non-empty description is displayed in result.";
$lang['list_content']['params']['show_only_from_same_country']  = "Enable this option to show only content uploaded by users from the same country as the current user.";
$lang['list_content']['params']['days_passed_from']             = "Allows filtering by publishing date, e.g. content added today, yesterday and etc. Specifies the upper limit in number of days passed from today.";
$lang['list_content']['params']['days_passed_to']               = "Allows filtering by publishing date, e.g. content added today, yesterday and etc. Specifies the lower limit in number of days passed from today. Value should be greater than value specified in [kt|b]days_passed_from[/kt|b] block parameter.";
$lang['list_content']['params']['is_private']                   = "Specifies whether only public or private content should be displayed.";
$lang['list_content']['params']['var_category_dir']             = "HTTP parameter, which provides category directory. If specified, only content from category with this directory will be displayed.";
$lang['list_content']['params']['var_category_id']              = "HTTP parameter, which provides category ID. If specified, only content from category with this ID will be displayed.";
$lang['list_content']['params']['var_tag_dir']                  = "HTTP parameter, which provides tag directory. If specified, only content, which have tag with this directory will be displayed.";
$lang['list_content']['params']['var_tag_id']                   = "HTTP parameter, which provides tag ID. If specified, only content, which have tag with this ID will be displayed.";
$lang['list_content']['params']['var_model_dir']                = "HTTP parameter, which provides model directory. If specified, only content, which have model with this directory will be displayed.";
$lang['list_content']['params']['var_model_id']                 = "HTTP parameter, which provides model ID. If specified, only content, which have model with this ID will be displayed.";
$lang['list_content']['params']['var_content_source_dir']       = "HTTP parameter, which provides content source directory. If specified, only content from content source with this directory will be displayed.";
$lang['list_content']['params']['var_content_source_id']        = "HTTP parameter, which provides content source ID. If specified, only content from content source with this ID will be displayed.";
$lang['list_content']['params']['var_content_source_group_dir'] = "HTTP parameter, which provides content source group directory. If specified, only content from content source group with this directory will be displayed.";
$lang['list_content']['params']['var_content_source_group_id']  = "HTTP parameter, which provides content source group ID. If specified, only content from content source group with this ID will be displayed.";
$lang['list_content']['params']['var_is_private']               = "HTTP parameter, which specifies content with what visibility should be displayed. The following comma-separated values can be passed in the given HTTP parameter: 2 - premium content, 1 - private content and 0 - public content. Overrides [kt|b]is_private[/kt|b] parameter.";
$lang['list_content']['params']['var_search']                   = "HTTP parameter, which provides search string. If specified, only content, which match this string will be displayed.";
$lang['list_content']['params']['search_method']                = "Specifies search method.";
$lang['list_content']['params']['search_scope']                 = "Configures whether both title and description should be searched.";
$lang['list_content']['params']['search_empty_404']             = "Configures block to show 404 error on empty search results.";
$lang['list_content']['params']['search_empty_redirect_to']     = "Configures block to redirect to the given URL on empty search results. You can use [kt|b]%QUERY%[/kt|b] token here, which will be replaced with query string.";
$lang['list_content']['params']['enable_search_on_tags']        = "Enables search on tag title and if any tag with such a title is found - content with this tag will be displayed in search result. May reduce search performance.";
$lang['list_content']['params']['enable_search_on_categories']  = "Enables search on category title and if any category with such a title is found - content from this category will be displayed in search result. May reduce search performance.";
$lang['list_content']['params']['enable_search_on_models']      = "Enables search on model title and if any model with such a title is found - content of this model will be displayed in search result. May reduce search performance.";
$lang['list_content']['params']['enable_search_on_cs']          = "Enables search on content source title and if any content source with such a title is found - content from this content source will be displayed in search result. May reduce search performance.";
$lang['list_content']['params']['mode_favourites']              = "Enables member's favourite content display mode.";
$lang['list_content']['params']['mode_uploaded']                = "Enables member's uploaded content display mode.";
$lang['list_content']['params']['mode_purchased']               = "Enables member's purchased content display mode.";
$lang['list_content']['params']['mode_subscribed']              = "Enables member's subscribed content display mode.";
$lang['list_content']['params']['mode_futures']                 = "Enables future (upcoming) content display mode.";
$lang['list_content']['params']['fav_type']                     = "Can be used with [kt|b]mode_favourites[/kt|b] only. Type of the displayed bookmarks: 0 - the default bookmarks list, 10 - playlist, which ID is passed in [kt|b]var_playlist_id[/kt|b] block parameter, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_content']['params']['var_fav_type']                 = "Can be used with [kt|b]mode_favourites[/kt|b] only. HTTP parameter, which provides type of the displayed bookmarks: 0 - the default bookmarks list, 10 - playlist, which ID is passed in [kt|b]var_playlist_id[/kt|b] block parameter, 1-9 - custom bookmarks lists, which you can use on your own.";
$lang['list_content']['params']['var_playlist_id']              = "Can be used with [kt|b]mode_favourites[/kt|b] only. HTTP parameter, which provides ID of the displayed playlist.";
$lang['list_content']['params']['var_user_id']                  = "Can be used with [kt|b]mode_favourites[/kt|b], [kt|b]mode_uploaded[/kt|b], [kt|b]mode_purchased[/kt|b] and [kt|b]mode_subscribed[/kt|b] only. HTTP parameter, which provides ID of a user, whose bookmarks / uploaded content should be displayed. If not enabled, block will display bookmarks / uploaded content of the current member.";
$lang['list_content']['params']['redirect_unknown_user_to']     = "Can be used with [kt|b]mode_favourites[/kt|b], [kt|b]mode_uploaded[/kt|b], [kt|b]mode_purchased[/kt|b] and [kt|b]mode_subscribed[/kt|b] only. Specifies URL, which will be used to redirect unregistered users to, when they are trying to access their own bookmarks / uploaded content (in most cases it should point to login page).";
$lang['list_content']['params']['allow_delete_uploaded_content']= "Can be used with [kt|b]mode_uploaded[/kt|b] only. Allows members to delete their own content.";
$lang['list_content']['params']['show_content_source_info']     = "Enables content source data loading for every content item (reduces performance).";
$lang['list_content']['params']['show_categories_info']         = "Enables categories data loading for every content item (reduces performance).";
$lang['list_content']['params']['show_tags_info']               = "Enables tags data loading for every content item (reduces performance).";
$lang['list_content']['params']['show_models_info']             = "Enables models data loading for every content item (reduces performance).";
$lang['list_content']['params']['show_user_info']               = "Enables user data loading for every content item (reduces performance).";
$lang['list_content']['params']['show_comments']                = "Enables ability to display a portion of comments for every content item. The number of comments is configured in separate block parameter. Using this parameter will decrease overall block performance.";
$lang['list_content']['params']['show_comments_count']          = "Can be used with [kt|b]show_comments[/kt|b] block parameter enabled. Specifies the number of comments that are selected for every content item.";
$lang['list_content']['params']['show_private']                 = "Specifies what kinds of members can see private content in result.";
$lang['list_content']['params']['show_premium']                 = "Specifies what kinds of members can see premium content in result.";

$lang['list_content']['values']['is_private']['0']                  = "Public only";
$lang['list_content']['values']['is_private']['1']                  = "Private only";
$lang['list_content']['values']['is_private']['2']                  = "Premium only";
$lang['list_content']['values']['is_private']['0|1']                = "Public and private only";
$lang['list_content']['values']['is_private']['0|2']                = "Public and premium only";
$lang['list_content']['values']['is_private']['1|2']                = "Private and premium only";
$lang['list_content']['values']['search_method']['1']               = "Whole expression match";
$lang['list_content']['values']['search_method']['2']               = "Any expression part match";
$lang['list_content']['values']['search_method']['3']               = "Full-text index (natural)";
$lang['list_content']['values']['search_method']['4']               = "Full-text index (boolean mode)";
$lang['list_content']['values']['search_method']['5']               = "Full-text index (with query expansion)";
$lang['list_content']['values']['search_scope']['0']                = "Title and description";
$lang['list_content']['values']['search_scope']['1']                = "Title only";
$lang['list_content']['values']['search_scope']['2']                = "Nothing";
$lang['list_content']['values']['show_private']['1']                = "Only registered members";
$lang['list_content']['values']['show_private']['2']                = "Only premium members";
$lang['list_content']['values']['show_premium']['1']                = "Only registered members";
$lang['list_content']['values']['show_premium']['2']                = "Only premium members";
$lang['list_content']['values']['sort_by']['object_id']             = "Object ID";
$lang['list_content']['values']['sort_by']['title']                 = "Title";
$lang['list_content']['values']['sort_by']['dir']                   = "Directory";
$lang['list_content']['values']['sort_by']['post_date']             = "Published on";
$lang['list_content']['values']['sort_by']['last_time_view_date']   = "Last viewed";
$lang['list_content']['values']['sort_by']['rating']                = "Rating";
$lang['list_content']['values']['sort_by']['object_viewed']         = "Popularity";
$lang['list_content']['values']['sort_by']['most_favourited']       = "Most favourited";
$lang['list_content']['values']['sort_by']['most_commented']        = "Most commented";
$lang['list_content']['values']['sort_by']['most_purchased']        = "Most purchased";
$lang['list_content']['values']['sort_by']['rand()']                = "Random";

$lang['list_content']['block_short_desc'] = "Displays mixed list of videos and albums with the given options";

$lang['list_content']['block_desc'] = "
	Block displays mixed list of videos and albums with different sorting and filtering options. This block is a
	regular list block with pagination support.
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	There are 6 different display modes for this block:[kt|br]
	1) User's favourites. In order to use this mode, you should enable [kt|b]mode_favourites[/kt|b] block parameter.
	   If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display favourites list of the user,
	   whose ID is passed in the related HTTP parameter. Otherwise block will first try to display favourites of the
	   current user ('my favourites'), and if the current user is not logged in - user will be redirected to the URL
	   specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying favourites of the
	   current user ('my favourites'), block will also provide ability for removing content from the favourites.
	   Favourites may be subdivided into several lists, which you can display by using [kt|b]var_fav_type[/kt|b] block
	   parameter and passing sub-list type in HTTP parameter. In order to display favourites from a particular
	   playlist, you should pass playlist ID in HTTP parameter, configured by [kt|b]var_playlist_id[/kt|b] block
	   parameter.[kt|br]
	2) User's uploaded content. In order to use this mode, you should enable [kt|b]mode_uploaded[/kt|b] block parameter.
	   If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display uploaded content list of the
	   user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to display uploaded
	   content of the current user ('my uploads'), and if the current user is not logged in - user will be redirected
	   to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block parameter. When displaying uploaded
	   content of the current user ('my uploads'), block will also provide ability for removing content from the
	   site.[kt|br]
	3) User's purchased content (spent tokens). In order to use this mode, you should enable [kt|b]mode_purchased[/kt|b]
	   block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display purchased
	   content list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
	   display purchased content list of the current user ('my purchased content'), and if the current user is not
	   logged in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
	   parameter.[kt|br]
	4) User's subscribed content. In order to use this mode, you should enable [kt|b]mode_subscribed[/kt|b]
	   block parameter. If [kt|b]var_user_id[/kt|b] block parameter is also enabled, block will display subscribed
	   content list of the user, whose ID is passed in the related HTTP parameter. Otherwise block will first try to
	   display subscribed content list of the current user ('my subscribed content'), and if the current user is not
	   logged in - user will be redirected to the URL specified in the [kt|b]redirect_unknown_user_to[/kt|b] block
	   parameter.[kt|br]
	5) Upcoming content list. In order to use this mode, you should enable [kt|b]mode_futures[/kt|b] block parameter.[kt|br]
	6) Regular content list. You can use all filters and sorting for this display mode.
	[kt|br][kt|br]

	If you need to exclude content from any particular categories or having any particular tags, you can use
	[kt|b]skip_categories[/kt|b] or [kt|b]skip_tags[/kt|b] block parameters. If you want to show only content from any
	particular categories or having any particular tags, you can use [kt|b]show_categories[/kt|b] or
	[kt|b]show_tags[/kt|b] block parameters.
	[kt|br][kt|br]

	If you want to show only content with non-empty description, please enable [kt|b]show_only_with_description[/kt|b]
	block parameter.
	[kt|br][kt|br]

	In order to display all content from a particular category (having a particular tag, or model, from a particular
	content source) passed dynamically in request parameters, you should use one of the [kt|b]var_xxx_dir[/kt|b] or
	[kt|b]var_xxx_id[/kt|b] block parameters.
	[kt|br][kt|br]

	It is possible to enable this block to display only content added during specific days interval, e.g. today,
	yesterday, last week and etc. The interval can be specified as a pair of [kt|b]days_passed_from[/kt|b] and
	[kt|b]days_passed_to[/kt|b] block parameters.
	[kt|br][kt|br]

	Search on content can be implemented by using [kt|b]var_search[/kt|b] block parameter. It should point to HTTP
	parameter, which contains query string. One of several different search methods can be selected in
	[kt|b]search_method[/kt|b] block parameter. You can also extend search logic by enabling
	[kt|b]enable_search_on_xxx[/kt|b] block parameters, however enabling them may dramatically decrease search
	performance.
	[kt|br][kt|br]

	Category, tag, model, content source and query string filtering is mutually exclusive, e.g. it is not
	possible to use any two of them at the same time.
	[kt|br][kt|br]

	If you need to hide private or premium content from different kinds of users (unregistered or standard users), you
	can use [kt|b]show_private[/kt|b] or [kt|b]show_premium[/kt|b] block parameters.
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	This block can be cached for a long time. The same cache version will be used for all users. Block
	will not be cached when displaying favourites of the current user ('my favourites'). When displaying search results
	block caching may vary depending on query string.
";

$lang['list_content']['block_examples'] = "
";

?>