<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_dvds_groups messages
// =====================================================================================================================

$lang['list_dvds_groups']['groups']['pagination']       = $lang['website_ui']['block_group_default_pagination'];
$lang['list_dvds_groups']['groups']['sorting']          = $lang['website_ui']['block_group_default_sorting'];
$lang['list_dvds_groups']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_dvds_groups']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['list_dvds_groups']['groups']['display_modes']    = $lang['website_ui']['block_group_default_display_modes'];
$lang['list_dvds_groups']['groups']['search']           = "Search DVD groups by text";
$lang['list_dvds_groups']['groups']['subselects']       = "Select additional data for each DVD group";
$lang['list_dvds_groups']['groups']['pull_dvds']        = "Select DVDs for each DVD group";

$lang['list_dvds_groups']['params']['items_per_page']               = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_dvds_groups']['params']['links_per_page']               = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_dvds_groups']['params']['var_from']                     = $lang['website_ui']['parameter_default_var_from'];
$lang['list_dvds_groups']['params']['var_items_per_page']           = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_dvds_groups']['params']['sort_by']                      = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_dvds_groups']['params']['var_sort_by']                  = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_dvds_groups']['params']['show_only_with_dvds']          = "If enabled, only groups with DVDs are displayed in result.";
$lang['list_dvds_groups']['params']['show_only_with_screenshot1']   = "If enabled, only groups with screenshot #1 available are displayed in result.";
$lang['list_dvds_groups']['params']['show_only_with_screenshot2']   = "If enabled, only groups with screenshot #2 available are displayed in result.";
$lang['list_dvds_groups']['params']['show_only_with_description']   = "If enabled, only groups with non-empty description are displayed in result.";
$lang['list_dvds_groups']['params']['skip_categories']              = "If specified, groups from these categories will not be displayed (comma separated list of category IDs).";
$lang['list_dvds_groups']['params']['show_categories']              = "If specified, only groups from these categories will be displayed (comma separated list of category IDs).";
$lang['list_dvds_groups']['params']['skip_tags']                    = "If specified, groups with these tags will not be displayed (comma separated list of tag IDs).";
$lang['list_dvds_groups']['params']['show_tags']                    = "If specified, only groups with these tags will be displayed (comma separated list of tag IDs).";
$lang['list_dvds_groups']['params']['skip_models']                  = "If specified, groups with these models will not be displayed (comma separated list of model IDs).";
$lang['list_dvds_groups']['params']['show_models']                  = "If specified, only groups with these models will be displayed (comma separated list of model IDs).";
$lang['list_dvds_groups']['params']['var_title_section']            = "URL parameter, which provides title first characters to filter the list.";
$lang['list_dvds_groups']['params']['var_category_dir']             = "URL parameter, which provides category directory. If specified, only groups from category with this directory will be displayed.";
$lang['list_dvds_groups']['params']['var_category_id']              = "URL parameter, which provides category ID. If specified, only groups from category with this ID will be displayed.";
$lang['list_dvds_groups']['params']['var_category_ids']             = "URL parameter, which provides comma-separated list of category IDs. If specified, only groups from categories with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display groups that belong to all these categories at the same time.";
$lang['list_dvds_groups']['params']['var_category_group_dir']       = "URL parameter, which provides category group directory. If specified, only groups from category group with this directory will be displayed.";
$lang['list_dvds_groups']['params']['var_category_group_id']        = "URL parameter, which provides category group ID. If specified, only groups from category group with this ID will be displayed.";
$lang['list_dvds_groups']['params']['var_category_group_ids']       = "URL parameter, which provides comma-separated list of category group IDs. If specified, only groups from category groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display groups that belong to all these category groups at the same time.";
$lang['list_dvds_groups']['params']['var_tag_dir']                  = "URL parameter, which provides tag directory. If specified, only groups that have tag with this directory will be displayed.";
$lang['list_dvds_groups']['params']['var_tag_id']                   = "URL parameter, which provides tag ID. If specified, only groups that have tag with this ID will be displayed.";
$lang['list_dvds_groups']['params']['var_tag_ids']                  = "URL parameter, which provides comma-separated list of tag IDs. If specified, only groups that have tags with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display groups that have all these tags at the same time.";
$lang['list_dvds_groups']['params']['var_model_dir']                = "URL parameter, which provides model directory. If specified, only groups that have model with this directory will be displayed.";
$lang['list_dvds_groups']['params']['var_model_id']                 = "URL parameter, which provides model ID. If specified, only groups that have model with this ID will be displayed.";
$lang['list_dvds_groups']['params']['var_model_ids']                = "URL parameter, which provides comma-separated list of model IDs. If specified, only groups that have models with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display groups that have all these models at the same time.";
$lang['list_dvds_groups']['params']['var_model_group_dir']          = "URL parameter, which provides model group directory. If specified, only groups from model group with this directory will be displayed.";
$lang['list_dvds_groups']['params']['var_model_group_id']           = "URL parameter, which provides model group ID. If specified, only groups from model group with this ID will be displayed.";
$lang['list_dvds_groups']['params']['var_model_group_ids']          = "URL parameter, which provides comma-separated list of model group ID. If specified, only groups from model groups with these IDs will be displayed. If this list contains predefined keyword [kt|b]all[/kt|b], then block will display groups that belong to all these model groups at the same time.";
$lang['list_dvds_groups']['params']['var_search']                   = "URL parameter, which provides search string. If specified, only groups that match this string will be displayed.";
$lang['list_dvds_groups']['params']['search_method']                = "Specifies search method.";
$lang['list_dvds_groups']['params']['search_scope']                 = "Configures whether both title and description should be searched.";
$lang['list_dvds_groups']['params']['search_redirect_enabled']      = "Enables redirect to group page if result contains only 1 group.";
$lang['list_dvds_groups']['params']['search_redirect_pattern']      = "Group page pattern to redirect user if search result contains only 1 group (in this case user will be immediately redirected to this group page). The pattern should contain at least one of these tokens: [kt|b]%ID%[/kt|b] and / or [kt|b]%DIR%[/kt|b].";
$lang['list_dvds_groups']['params']['show_categories_info']         = "Enables categories data loading for every group. Using this parameter will decrease overall block performance.";
$lang['list_dvds_groups']['params']['show_tags_info']               = "Enables tags data loading for every group. Using this parameter will decrease overall block performance.";
$lang['list_dvds_groups']['params']['show_models_info']             = "Enables models data loading for every group. Using this parameter will decrease overall block performance.";
$lang['list_dvds_groups']['params']['pull_dvds']                    = "Enables ability to display a portion of DVDs for every group. The number of DVDs and their sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_dvds_groups']['params']['pull_dvds_count']              = "Specifies the number of DVDs that are selected for every group.";
$lang['list_dvds_groups']['params']['pull_dvds_sort_by']            = "Specifies sorting for DVDs that are selected for every group.";

$lang['list_dvds_groups']['values']['search_method']['1']                           = "Whole expression match";
$lang['list_dvds_groups']['values']['search_method']['2']                           = "Any expression part match";
$lang['list_dvds_groups']['values']['search_scope']['0']                            = "Title and description";
$lang['list_dvds_groups']['values']['search_scope']['1']                            = "Title only";
$lang['list_dvds_groups']['values']['sort_by']['dvd_group_id']                      = "Group ID";
$lang['list_dvds_groups']['values']['sort_by']['sort_id']                           = "Sorting ID";
$lang['list_dvds_groups']['values']['sort_by']['title']                             = "Title";
$lang['list_dvds_groups']['values']['sort_by']['total_dvds']                        = "DVDs count";
$lang['list_dvds_groups']['values']['sort_by']['rand()']                            = "Random";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['dvd_id']                  = "DVD ID";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['sort_id']                 = "Sorting ID";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['title']                   = "Title";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['rating']                  = "Rating";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['dvd_viewed']              = "Popularity";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['today_videos']            = "Videos added today";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['total_videos']            = "Total videos";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['total_videos_duration']   = "Total videos duration";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['avg_videos_rating']       = "Average video rating";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['avg_videos_popularity']   = "Average video popularity";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['comments_count']          = "Most commented";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['subscribers_count']       = "Most subscribed";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['last_content_date']       = "Last content added";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['added_date']              = "Creation date";
$lang['list_dvds_groups']['values']['pull_dvds_sort_by']['rand()']                  = "Random";

$lang['list_dvds_groups']['block_short_desc'] = "Displays list of DVD / channel groups / TV series with the given options";

$lang['list_dvds_groups']['block_desc'] = "
	Block displays list of DVD / channel groups / TV series with different sorting and filtering options. This block is
	a standard list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_dvds']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_text_search']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_dvds_groups']['block_examples'] = "
	[kt|b]Display all DVD groups sorted alphabetically[/kt|b]
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

	[kt|b]Display DVD groups with DVDs, 10 per page and sorted by DVDs count[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = total_dvds desc[kt|br]
	- show_only_with_dvds[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all DVD groups that have title starting with 'a'[/kt|b]
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

	[kt|b]Display 15 DVD groups in category with directory 'my_category' sorted alphabetically[/kt|b]
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

	[kt|b]Display 10 DVD groups per page with 5 top rated DVDs from each group[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = title asc[kt|br]
	- pull_dvds[kt|br]
	- pull_dvds_count = 5[kt|br]
	- pull_dvds_sort_by = rating desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
