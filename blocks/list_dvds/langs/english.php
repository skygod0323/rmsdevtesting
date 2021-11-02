<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_dvds messages
// =====================================================================================================================

$lang['list_dvds']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_dvds']['groups']['sorting']         = $lang['website_ui']['block_group_default_sorting'];
$lang['list_dvds']['groups']['static_filters']  = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_dvds']['groups']['dynamic_filters'] = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_dvds']['groups']['display_modes']   = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_dvds']['groups']['search']          = "Search DVDs by text";
$lang['list_dvds']['groups']['subselects']      = "Select additional data for each DVD";
$lang['list_dvds']['groups']['pull_videos']     = "Select videos for each DVD";

$lang['list_dvds']['params']['items_per_page']              = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_dvds']['params']['links_per_page']              = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_dvds']['params']['var_from']                    = $lang['website_ui']['parameter_default_var_from'];
$lang['list_dvds']['params']['var_items_per_page']          = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_dvds']['params']['sort_by']                     = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_dvds']['params']['var_sort_by']                 = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_dvds']['params']['show_only_with_cover1']       = "If enabled, only DVDs with cover #1 available (both front and back) are displayed in result.";
$lang['list_dvds']['params']['show_only_with_cover2']       = "If enabled, only DVDs with cover #2 available (both front and back) are displayed in result.";
$lang['list_dvds']['params']['show_only_with_description']  = "If enabled, only DVDs with non-empty description are displayed in result.";
$lang['list_dvds']['params']['show_only_with_videos']       = "If enabled, only DVDs with the given amount of videos are displayed in result.";
$lang['list_dvds']['params']['skip_categories']             = "If specified, DVDs from these categories will not be displayed (comma separated list of category IDs).";
$lang['list_dvds']['params']['show_categories']             = "If specified, only DVDs from these categories will be displayed (comma separated list of category IDs).";
$lang['list_dvds']['params']['skip_tags']                   = "If specified, DVDs with these tags will not be displayed (comma separated list of tag IDs).";
$lang['list_dvds']['params']['show_tags']                   = "If specified, only DVDs with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_dvds']['params']['skip_models']                 = "If specified, DVDs with these models will not be displayed (comma separated list of model IDs).";
$lang['list_dvds']['params']['show_models']                 = "If specified, only DVDs with these models will be displayed (comma separated list of model IDs).";
$lang['list_dvds']['params']['skip_dvd_groups']             = "If specified, DVDs from these groups will not be displayed (comma separated list of group IDs).";
$lang['list_dvds']['params']['show_dvd_groups']             = "If specified, only DVDs from these groups will be displayed (comma separated list of group IDs).";
$lang['list_dvds']['params']['var_title_section']           = "URL parameter, which provides title first characters to filter the list.";
$lang['list_dvds']['params']['var_category_dir']            = "URL parameter, which provides category directory. If specified, only DVDs from category with this directory will be displayed.";
$lang['list_dvds']['params']['var_category_id']             = "URL parameter, which provides category ID. If specified, only DVDs from category with this ID will be displayed.";
$lang['list_dvds']['params']['var_category_ids']            = "URL parameter, which provides comma-separated list of category IDs. If specified, only DVDs from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display DVDs that belong to all these categories at the same time.";
$lang['list_dvds']['params']['var_category_group_dir']      = "URL parameter, which provides category group directory. If specified, only DVDs from category group with this directory will be displayed.";
$lang['list_dvds']['params']['var_category_group_id']       = "URL parameter, which provides category group ID. If specified, only DVDs from category group with this ID will be displayed.";
$lang['list_dvds']['params']['var_category_group_ids']      = "URL parameter, which provides comma-separated list of category group IDs. If specified, only DVDs from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display DVDs that belong to all these category groups at the same time.";
$lang['list_dvds']['params']['var_tag_dir']                 = "URL parameter, which provides tag directory. If specified, only DVDs that have tag with this directory will be displayed.";
$lang['list_dvds']['params']['var_tag_id']                  = "URL parameter, which provides tag ID. If specified, only DVDs that have tag with this ID will be displayed.";
$lang['list_dvds']['params']['var_tag_ids']                 = "URL parameter, which provides comma-separated list of tag IDs. If specified, only DVDs that have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display DVDs that have all these tags at the same time.";
$lang['list_dvds']['params']['var_model_dir']               = "URL parameter, which provides model directory. If specified, only DVDs that have model with this directory will be displayed.";
$lang['list_dvds']['params']['var_model_id']                = "URL parameter, which provides model ID. If specified, only DVDs that have model with this ID will be displayed.";
$lang['list_dvds']['params']['var_model_ids']               = "URL parameter, which provides comma-separated list of model IDs. If specified, only DVDs that have models with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display DVDs that have all these models at the same time.";
$lang['list_dvds']['params']['var_model_group_dir']         = "URL parameter, which provides model group directory. If specified, only DVDs from model group with this directory will be displayed.";
$lang['list_dvds']['params']['var_model_group_id']          = "URL parameter, which provides model group ID. If specified, only DVDs from model group with this ID will be displayed.";
$lang['list_dvds']['params']['var_model_group_ids']         = "URL parameter, which provides comma-separated list of model group ID. If specified, only DVDs from model groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display DVDs that belong to all these model groups at the same time.";
$lang['list_dvds']['params']['var_dvd_group_dir']           = "URL parameter, which provides DVD group directory. If specified, only DVDs from group with this directory will be displayed.";
$lang['list_dvds']['params']['var_dvd_group_id']            = "URL parameter, which provides DVD group ID. If specified, only DVDs from group with this ID will be displayed.";
$lang['list_dvds']['params']['var_dvd_group_ids']           = "URL parameter, which provides comma-separated list of DVD group IDs. If specified, only DVDs  from groups with these IDs will be displayed.";
$lang['list_dvds']['params']['var_search']                  = "URL parameter, which provides search string. If specified, only DVDs that match this string will be displayed.";
$lang['list_dvds']['params']['search_method']               = "Specifies search method.";
$lang['list_dvds']['params']['search_scope']                = "Configures whether both title and description should be searched.";
$lang['list_dvds']['params']['search_redirect_enabled']     = "Enables redirect to DVD page if result contains only 1 DVD.";
$lang['list_dvds']['params']['search_redirect_pattern']     = "DVD page pattern to redirect user if search result contains only 1 DVD (in this case user will be immediately redirected to this DVD page). The pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b].";
$lang['list_dvds']['params']['mode_created']                = "Enables member's created DVDs display mode. You can also enable [kt|b]var_user_id[/kt|b] parameter to show created DVDs of the given user, otherwise created DVDs of the currently logged user will be displayed.";
$lang['list_dvds']['params']['mode_uploadable']             = "Enables member's uploadable DVDs display mode. You can also enable [kt|b]var_user_id[/kt|b] parameter to show uploadable DVDs of the given user, otherwise uploadable DVDs of the currently logged user will be displayed.";
$lang['list_dvds']['params']['var_user_id']                 = "URL parameter, which provides user ID for the selected display mode.";
$lang['list_dvds']['params']['redirect_unknown_user_to']    = "Specifies redirect URL for the visitors that are not logged in and are attempting to access display mode available for members only.";
$lang['list_dvds']['params']['allow_delete_created_dvds']   = "Allows members to remove their created DVDs in [kt|b]mode_created[/kt|b] display mode.";
$lang['list_dvds']['params']['show_categories_info']        = "Enables categories data loading for every DVD. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['show_tags_info']              = "Enables tags data loading for every DVD. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['show_models_info']            = "Enables models data loading for every DVD. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['show_group_info']             = "Enables DVD group data loading for every DVD. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['show_user_info']              = "Enables user data loading for every DVD. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['pull_videos']                 = "Enables ability to display a portion of videos for every DVD. The number of videos and videos sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_dvds']['params']['pull_videos_count']           = "Specifies the number of videos that are selected for every DVD.";
$lang['list_dvds']['params']['pull_videos_sort_by']         = "Specifies sorting for videos that are selected for every DVD.";

$lang['list_dvds']['values']['search_method']['1']                          = "Whole expression match";
$lang['list_dvds']['values']['search_method']['2']                          = "Any expression part match";
$lang['list_dvds']['values']['search_scope']['0']                           = "Title and description";
$lang['list_dvds']['values']['search_scope']['1']                           = "Title only";
$lang['list_dvds']['values']['allow_delete_created_dvds']['0']              = "Delete forbidden";
$lang['list_dvds']['values']['allow_delete_created_dvds']['1']              = "Delete allowed without videos";
$lang['list_dvds']['values']['allow_delete_created_dvds']['2']              = "Delete allowed together with all videos";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_id']             = "Video ID";
$lang['list_dvds']['values']['pull_videos_sort_by']['dvd_sort_id']          = "Sorting ID inside DVD";
$lang['list_dvds']['values']['pull_videos_sort_by']['title']                = "Title";
$lang['list_dvds']['values']['pull_videos_sort_by']['duration']             = "Duration";
$lang['list_dvds']['values']['pull_videos_sort_by']['post_date']            = "Published on";
$lang['list_dvds']['values']['pull_videos_sort_by']['last_time_view_date']  = "Last viewed";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating']               = "Overall rating";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_today']         = "Rating today";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_week']          = "Rating this week";
$lang['list_dvds']['values']['pull_videos_sort_by']['rating_month']         = "Rating this month";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed']         = "Overall popularity";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_today']   = "Popularity today";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_week']    = "Popularity this week";
$lang['list_dvds']['values']['pull_videos_sort_by']['video_viewed_month']   = "Popularity this month";
$lang['list_dvds']['values']['pull_videos_sort_by']['most_favourited']      = "Most favourited";
$lang['list_dvds']['values']['pull_videos_sort_by']['most_commented']       = "Most commented";
$lang['list_dvds']['values']['pull_videos_sort_by']['ctr']                  = "CTR (rotator)";
$lang['list_dvds']['values']['pull_videos_sort_by']['rand()']               = "Random";
$lang['list_dvds']['values']['sort_by']['dvd_id']                           = "DVD ID";
$lang['list_dvds']['values']['sort_by']['sort_id']                          = "Sorting ID";
$lang['list_dvds']['values']['sort_by']['title']                            = "Title";
$lang['list_dvds']['values']['sort_by']['rating']                           = "Rating";
$lang['list_dvds']['values']['sort_by']['dvd_viewed']                       = "Popularity";
$lang['list_dvds']['values']['sort_by']['today_videos']                     = "Videos added today";
$lang['list_dvds']['values']['sort_by']['total_videos']                     = "Total videos";
$lang['list_dvds']['values']['sort_by']['total_videos_duration']            = "Total videos duration";
$lang['list_dvds']['values']['sort_by']['avg_videos_rating']                = "Average video rating";
$lang['list_dvds']['values']['sort_by']['avg_videos_popularity']            = "Average video popularity";
$lang['list_dvds']['values']['sort_by']['comments_count']                   = "Most commented";
$lang['list_dvds']['values']['sort_by']['subscribers_count']                = "Most subscribed";
$lang['list_dvds']['values']['sort_by']['last_content_date']                = "Last content added";
$lang['list_dvds']['values']['sort_by']['added_date']                       = "Creation date";
$lang['list_dvds']['values']['sort_by']['rand()']                           = "Random";

$lang['list_dvds']['block_short_desc'] = "Displays list of DVDs / channels / TV seasons with the given options";

$lang['list_dvds']['block_desc'] = "
	Block displays list of DVDs / channels / TV seasons with different sorting and filtering options. This block is a
	standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	[kt|b]Display modes[/kt|b]
	[kt|br][kt|br]

	1) [kt|b]Default[/kt|b]. This is default mode to display abtract list of DVDs. No specific configuration is needed.
	[kt|br][kt|br]

	2) [kt|b]Created DVDs[/kt|b]. This mode displays DVDs that were created by a user. When displaying created DVDs for
	the user currently logged in, this mode will also provide ability to delete any created DVDs from your site, if this
	operation is permitted by [kt|b]allow_delete_created_dvds[/kt|b] parameter.
	[kt|br][kt|br]

	3) [kt|b]DVDs allowed to upload to[/kt|b]. When using DVDs as channels, users are allowed to upload videos to
	their own channels, channels of their friends and public channels. This mode can be used to display list of DVDs
	that are allowed for the current (or given) user to upload videos into them.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_list_forms']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_mode_specific']}
";

$lang['list_dvds']['block_examples'] = "
	[kt|b]Display all DVDs sorted alphabetically[/kt|b]
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

	[kt|b]Display all DVDs from group with directory 'my-group'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_dvd_group_dir = group[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?group=my-group
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display DVDs with videos, 10 per page and sorted by videos count[/kt|b]
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

	[kt|b]Display all DVDs that have title starting with 'a'[/kt|b]
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

	[kt|b]Display 15 DVDs in category with directory 'my_category' sorted alphabetically[/kt|b]
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

	[kt|b]Display 10 DVDs per page with 5 top rated videos from each DVD[/kt|b]
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

	[kt|b]Display my owned DVDs 20 per page[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- mode_created[kt|br]
	- redirect_unknown_user_to = /?login[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
