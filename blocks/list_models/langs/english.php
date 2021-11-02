<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_models messages
// =====================================================================================================================

$lang['list_models']['groups']['pagination']        = $lang['website_ui']['block_group_default_pagination'];
$lang['list_models']['groups']['sorting']           = $lang['website_ui']['block_group_default_sorting'];
$lang['list_models']['groups']['static_filters']    = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_models']['groups']['dynamic_filters']   = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_models']['groups']['search']            = "Search models by text";
$lang['list_models']['groups']['related']           = "Related models";
$lang['list_models']['groups']['subselects']        = "Select additional data for each model";
$lang['list_models']['groups']['pull_videos']       = "Select videos for each model";
$lang['list_models']['groups']['pull_albums']       = "Select albums for each model";

$lang['list_models']['params']['items_per_page']                    = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_models']['params']['links_per_page']                    = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_models']['params']['var_from']                          = $lang['website_ui']['parameter_default_var_from'];
$lang['list_models']['params']['var_items_per_page']                = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_models']['params']['sort_by']                           = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_models']['params']['var_sort_by']                       = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_models']['params']['show_only_with_screenshot1']        = "If enabled, only models with screenshot #1 available are displayed in result.";
$lang['list_models']['params']['show_only_with_screenshot2']        = "If enabled, only models with screenshot #2 available are displayed in result.";
$lang['list_models']['params']['show_only_with_description']        = "If enabled, only models with non-empty description are displayed in result.";
$lang['list_models']['params']['show_only_with_videos']             = "If enabled, only models with the given amount of videos are displayed in result.";
$lang['list_models']['params']['show_only_with_albums']             = "If enabled, only models with the given amount of albums are displayed in result.";
$lang['list_models']['params']['show_only_with_posts']              = "If enabled, only models with the given amount of posts are displayed in result.";
$lang['list_models']['params']['show_only_with_albums_or_videos']   = "If enabled, only models with the given amount of videos or albums are displayed in result.";
$lang['list_models']['params']['show_gender']                       = "If specified, models of the selected gender are displayed in result.";
$lang['list_models']['params']['skip_model_groups']                 = "If specified, models from these model groups will not be displayed (comma separated list of model group IDs).";
$lang['list_models']['params']['show_model_groups']                 = "If specified, only models from these model groups will be displayed (comma separated list of model group IDs).";
$lang['list_models']['params']['skip_categories']                   = "If specified, models from these categories will not be displayed (comma separated list of category IDs).";
$lang['list_models']['params']['show_categories']                   = "If specified, only models from these categories will be displayed (comma separated list of category IDs).";
$lang['list_models']['params']['skip_tags']                         = "If specified, models with these tags will not be displayed (comma separated list of tag IDs).";
$lang['list_models']['params']['show_tags']                         = "If specified, only models with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_models']['params']['var_title_section']                 = "URL parameter, which provides title first characters to filter the list (filter by alphabet letters for example).";
$lang['list_models']['params']['var_model_group_dir']               = "URL parameter, which provides model group directory. If specified, only models from group with this directory will be displayed.";
$lang['list_models']['params']['var_model_group_id']                = "URL parameter, which provides model group ID. If specified, only models from group with this ID will be displayed.";
$lang['list_models']['params']['var_model_group_ids']               = "URL parameter, which provides comma-separated list of model group IDs. If specified, only models from groups with these IDs will be displayed.";
$lang['list_models']['params']['var_category_dir']                  = "URL parameter, which provides category directory. If specified, only models from category with this directory will be displayed.";
$lang['list_models']['params']['var_category_id']                   = "URL parameter, which provides category ID. If specified, only models from category with this ID will be displayed.";
$lang['list_models']['params']['var_category_ids']                  = "URL parameter, which provides comma-separated list of category IDs. If specified, only models from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display models that belong to all these categories at the same time.";
$lang['list_models']['params']['var_category_group_dir']            = "URL parameter, which provides category group directory. If specified, only models from category group with this directory will be displayed.";
$lang['list_models']['params']['var_category_group_id']             = "URL parameter, which provides category group ID. If specified, only models from category group with this ID will be displayed.";
$lang['list_models']['params']['var_category_group_ids']            = "URL parameter, which provides comma-separated list of category group IDs. If specified, only models from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display models that belong to all these category groups at the same time.";
$lang['list_models']['params']['var_tag_dir']                       = "URL parameter, which provides tag directory. If specified, only models that have tag with this directory will be displayed.";
$lang['list_models']['params']['var_tag_id']                        = "URL parameter, which provides tag ID. If specified, only models that have tag with this ID will be displayed.";
$lang['list_models']['params']['var_tag_ids']                       = "URL parameter, which provides comma-separated list of tag IDs. If specified, only models that have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display models that have all these tags at the same time.";
$lang['list_models']['params']['var_country_id']                    = "URL parameter, which provides country ID to filter the list by country.";
$lang['list_models']['params']['var_state']                         = "URL parameter, which provides state to filter the list by state.";
$lang['list_models']['params']['var_city']                          = "URL parameter, which provides city to filter the list by city.";
$lang['list_models']['params']['var_hair_id']                       = "URL parameter, which provides hair color ID to filter the list by hair color ([kt|b]1[/kt|b] - black, [kt|b]2[/kt|b] - dark, [kt|b]3[/kt|b] - red, [kt|b]4[/kt|b] - brown, [kt|b]5[/kt|b] - blond, [kt|b]6[/kt|b] - grey, [kt|b]7[/kt|b] - bald, [kt|b]8[/kt|b] - wig).";
$lang['list_models']['params']['var_eye_color_id']                  = "URL parameter, which provides eye color ID to filter the list by eye color ([kt|b]1[/kt|b] - blue, [kt|b]2[/kt|b] - gray, [kt|b]3[/kt|b] - green, [kt|b]4[/kt|b] - amber, [kt|b]5[/kt|b] - brown, [kt|b]6[/kt|b] - hazel, [kt|b]7[/kt|b] - black).";
$lang['list_models']['params']['var_gender_id']                     = "URL parameter, which provides gender ID to filter the list by gender ([kt|b]0[/kt|b] - female, [kt|b]1[/kt|b] - male, [kt|b]2[/kt|b] - other).";
$lang['list_models']['params']['var_age_from']                      = "URL parameter, which provides ability to display only models with age greater than or equal to the number passed in this parameter (number of years).";
$lang['list_models']['params']['var_age_to']                        = "URL parameter, which provides ability to display only models with age less than the number passed in this parameter (number of years).";
$lang['list_models']['params']['var_custom1']                       = "HTTP parameter, which provides value for custom field 1 dynamic filtering.";
$lang['list_models']['params']['var_custom2']                       = "HTTP parameter, which provides value for custom field 2 dynamic filtering.";
$lang['list_models']['params']['var_custom3']                       = "HTTP parameter, which provides value for custom field 3 dynamic filtering.";
$lang['list_models']['params']['var_custom4']                       = "HTTP parameter, which provides value for custom field 4 dynamic filtering.";
$lang['list_models']['params']['var_custom5']                       = "HTTP parameter, which provides value for custom field 5 dynamic filtering.";
$lang['list_models']['params']['var_custom6']                       = "HTTP parameter, which provides value for custom field 6 dynamic filtering.";
$lang['list_models']['params']['var_custom7']                       = "HTTP parameter, which provides value for custom field 7 dynamic filtering.";
$lang['list_models']['params']['var_custom8']                       = "HTTP parameter, which provides value for custom field 8 dynamic filtering.";
$lang['list_models']['params']['var_custom9']                       = "HTTP parameter, which provides value for custom field 9 dynamic filtering.";
$lang['list_models']['params']['var_custom10']                      = "HTTP parameter, which provides value for custom field 10 dynamic filtering.";
$lang['list_models']['params']['var_search']                        = "URL parameter, which provides search string. If specified, only models that match this string will be displayed.";
$lang['list_models']['params']['search_method']                     = "Specifies search method.";
$lang['list_models']['params']['search_scope']                      = "Configures whether both title and description should be searched.";
$lang['list_models']['params']['search_redirect_enabled']           = "Enables redirect to model page if result contains only 1 model.";
$lang['list_models']['params']['search_redirect_pattern']           = "Model page pattern to redirect user if search result contains only 1 model (in this case user will be immediately redirected to this model page). The pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b].";
$lang['list_models']['params']['mode_related']                      = "Enables related models display mode.";
$lang['list_models']['params']['var_model_dir']                     = "URL parameter, which provides model directory to display its related models.";
$lang['list_models']['params']['var_model_id']                      = "URL parameter, which provides model ID to display its related models.";
$lang['list_models']['params']['var_mode_related']                  = "Allows dynamically switch related models display mode by passing one of the following values in URL parameter: [kt|b]1[/kt|b] - by tags, [kt|b]2[/kt|b] - by categories, [kt|b]3[/kt|b] - by country, [kt|b]4[/kt|b] - by city, [kt|b]5[/kt|b] - by gender, [kt|b]6[/kt|b] - by age, [kt|b]7[/kt|b] - by height, [kt|b]8[/kt|b] - by weight, [kt|b]9[/kt|b] - by hair color, [kt|b]10[/kt|b] - by videos, [kt|b]11[/kt|b] - by albums, [kt|b]12[/kt|b] - by group, [kt|b]13[/kt|b] - by state.";
$lang['list_models']['params']['mode_related_category_group_id']    = "Can be used with related by categories only. Specify category group ID / external ID to restrict only related models from this category group.";
$lang['list_models']['params']['show_categories_info']              = "Enables categories data loading for every model. Using this parameter will decrease overall block performance.";
$lang['list_models']['params']['show_tags_info']                    = "Enables tags data loading for every model. Using this parameter will decrease overall block performance.";
$lang['list_models']['params']['pull_videos']                       = "Enables ability to display a portion of videos for every model. The number of videos and videos sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_models']['params']['pull_videos_count']                 = "Specifies the number of videos that are selected for every model.";
$lang['list_models']['params']['pull_videos_sort_by']               = "Specifies sorting for videos that are selected for every model.";
$lang['list_models']['params']['pull_videos_duplicates']            = "Enable this option to allow duplicate videos to be selected for different models.";
$lang['list_models']['params']['pull_albums']                       = "Enables ability to display a portion of albums for every model. The number of albums and albums sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_models']['params']['pull_albums_count']                 = "Specifies the number of albums that are selected for every model.";
$lang['list_models']['params']['pull_albums_sort_by']               = "Specifies sorting for albums that are selected for every model.";
$lang['list_models']['params']['pull_albums_duplicates']            = "Enable this option to allow duplicate albums to be selected for different models.";

$lang['list_models']['values']['show_gender']['0']                              = "Female";
$lang['list_models']['values']['show_gender']['1']                              = "Male";
$lang['list_models']['values']['show_gender']['2']                              = "Other";
$lang['list_models']['values']['search_method']['1']                            = "Whole expression match";
$lang['list_models']['values']['search_method']['2']                            = "Any expression part match";
$lang['list_models']['values']['search_scope']['0']                             = "Title / pseudonym and description";
$lang['list_models']['values']['search_scope']['1']                             = "Title / pseudonym only";
$lang['list_models']['values']['mode_related']['1']                             = "Related by tags";
$lang['list_models']['values']['mode_related']['2']                             = "Related by categories";
$lang['list_models']['values']['mode_related']['3']                             = "Related by country";
$lang['list_models']['values']['mode_related']['4']                             = "Related by city";
$lang['list_models']['values']['mode_related']['5']                             = "Related by gender";
$lang['list_models']['values']['mode_related']['6']                             = "Related by age";
$lang['list_models']['values']['mode_related']['7']                             = "Related by height";
$lang['list_models']['values']['mode_related']['8']                             = "Related by weight";
$lang['list_models']['values']['mode_related']['9']                             = "Related by hair color";
$lang['list_models']['values']['mode_related']['10']                            = "Related by videos";
$lang['list_models']['values']['mode_related']['11']                            = "Related by albums";
$lang['list_models']['values']['mode_related']['12']                            = "Related by group";
$lang['list_models']['values']['mode_related']['13']                            = "Related by state";
$lang['list_models']['values']['pull_videos_sort_by']['duration']               = "Duration";
$lang['list_models']['values']['pull_videos_sort_by']['post_date']              = "Published on";
$lang['list_models']['values']['pull_videos_sort_by']['last_time_view_date']    = "Last viewed";
$lang['list_models']['values']['pull_videos_sort_by']['rating']                 = "Overall rating";
$lang['list_models']['values']['pull_videos_sort_by']['rating_today']           = "Rating today";
$lang['list_models']['values']['pull_videos_sort_by']['rating_week']            = "Rating this week";
$lang['list_models']['values']['pull_videos_sort_by']['rating_month']           = "Rating this month";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed']           = "Overall popularity";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_today']     = "Popularity today";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_week']      = "Popularity this week";
$lang['list_models']['values']['pull_videos_sort_by']['video_viewed_month']     = "Popularity this month";
$lang['list_models']['values']['pull_videos_sort_by']['most_favourited']        = "Most favourited";
$lang['list_models']['values']['pull_videos_sort_by']['most_commented']         = "Most commented";
$lang['list_models']['values']['pull_videos_sort_by']['ctr']                    = "CTR (rotator)";
$lang['list_models']['values']['pull_videos_sort_by']['rand()']                 = "Random";
$lang['list_models']['values']['pull_albums_sort_by']['photos_amount']          = "Images count";
$lang['list_models']['values']['pull_albums_sort_by']['post_date']              = "Published on";
$lang['list_models']['values']['pull_albums_sort_by']['last_time_view_date']    = "Last viewed";
$lang['list_models']['values']['pull_albums_sort_by']['rating']                 = "Overall rating";
$lang['list_models']['values']['pull_albums_sort_by']['rating_today']           = "Rating today";
$lang['list_models']['values']['pull_albums_sort_by']['rating_week']            = "Rating this week";
$lang['list_models']['values']['pull_albums_sort_by']['rating_month']           = "Rating this month";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed']           = "Overall popularity";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_today']     = "Popularity today";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_week']      = "Popularity this week";
$lang['list_models']['values']['pull_albums_sort_by']['album_viewed_month']     = "Popularity this month";
$lang['list_models']['values']['pull_albums_sort_by']['most_favourited']        = "Most favourited";
$lang['list_models']['values']['pull_albums_sort_by']['most_commented']         = "Most commented";
$lang['list_models']['values']['pull_albums_sort_by']['rand()']                 = "Random";
$lang['list_models']['values']['sort_by']['model_id']                           = "Model ID";
$lang['list_models']['values']['sort_by']['sort_id']                            = "Sorting ID";
$lang['list_models']['values']['sort_by']['title']                              = "Title";
$lang['list_models']['values']['sort_by']['birth_date']                         = "Birth date";
$lang['list_models']['values']['sort_by']['age']                                = "Age";
$lang['list_models']['values']['sort_by']['rating']                             = "Rating";
$lang['list_models']['values']['sort_by']['model_viewed']                       = "Popularity";
$lang['list_models']['values']['sort_by']['screenshot1']                        = "Screenshot 1";
$lang['list_models']['values']['sort_by']['screenshot2']                        = "Screenshot 2";
$lang['list_models']['values']['sort_by']['today_videos']                       = "Videos added today";
$lang['list_models']['values']['sort_by']['total_videos']                       = "Total videos";
$lang['list_models']['values']['sort_by']['today_albums']                       = "Albums added today";
$lang['list_models']['values']['sort_by']['total_albums']                       = "Total albums";
$lang['list_models']['values']['sort_by']['today_posts']                        = "Posts added today";
$lang['list_models']['values']['sort_by']['total_posts']                        = "Total posts";
$lang['list_models']['values']['sort_by']['avg_videos_rating']                  = "Average video rating";
$lang['list_models']['values']['sort_by']['avg_videos_popularity']              = "Average video popularity";
$lang['list_models']['values']['sort_by']['avg_albums_rating']                  = "Average album rating";
$lang['list_models']['values']['sort_by']['avg_albums_popularity']              = "Average album popularity";
$lang['list_models']['values']['sort_by']['avg_posts_rating']                   = "Average post rating";
$lang['list_models']['values']['sort_by']['avg_posts_popularity']               = "Average post popularity";
$lang['list_models']['values']['sort_by']['comments_count']                     = "Most commented";
$lang['list_models']['values']['sort_by']['subscribers_count']                  = "Most subscribed";
$lang['list_models']['values']['sort_by']['rank']                               = "Rank";
$lang['list_models']['values']['sort_by']['last_content_date']                  = "Last content added";
$lang['list_models']['values']['sort_by']['added_date']                         = "Creation date";
$lang['list_models']['values']['sort_by']['rand()']                             = "Random";

$lang['list_models']['block_short_desc'] = "Displays list of models with the given options";

$lang['list_models']['block_desc'] = "
	Block displays list of models with different sorting and filtering options. This block is a standard list block
	with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	[kt|b]Related models[/kt|b]
	[kt|br][kt|br]

	You can configure this block to show models that are similar to some other given model by using a wide range of
	criteria. This is so-called 'related' behavior. You should enable [kt|b]mode_related[/kt|b] parameter and
	additionally you should enable one of the [kt|b]var_model_dir[/kt|b] or [kt|b]var_model_id[/kt|b] parameters. In
	order this functionality to start working, either model ID or model directory should be passed in the URL, so that
	this block knows which model should it base from:
	[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?[kt|b]id=123[/kt|b]
	[kt|br]
	{$config['project_url']}/page.php?[kt|b]dir=model-directory[/kt|b]
	[/kt|code]
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_models']['block_examples'] = "
	[kt|b]Display all models sorted alphabetically[/kt|b]
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

	[kt|b]Display models with video, 10 per page and sorted by videos count[/kt|b]
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

	[kt|b]Display all models that have title starting with 'a'[/kt|b]
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

	[kt|b]Display 15 models in category with directory 'my_category' sorted alphabetically[/kt|b]
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

	[kt|b]Display all male models[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- var_gender_id = gender_id[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?gender_id=1
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 models per page with 5 top rated videos for each model[/kt|b]
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

	[kt|b]Display all models with 10 most popular albums for each model[/kt|b]
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
