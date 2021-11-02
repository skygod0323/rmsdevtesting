<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_categories_groups messages
// =====================================================================================================================

$lang['list_categories_groups']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['list_categories_groups']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['list_categories_groups']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_categories_groups']['groups']['pull_categories']    = "Select categories for each category group";

$lang['list_categories_groups']['params']['items_per_page']                 = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_categories_groups']['params']['links_per_page']                 = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_categories_groups']['params']['var_from']                       = $lang['website_ui']['parameter_default_var_from'];
$lang['list_categories_groups']['params']['var_items_per_page']             = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_categories_groups']['params']['sort_by']                        = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_categories_groups']['params']['var_sort_by']                    = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_categories_groups']['params']['show_only_with_avatar']          = "If enabled, only groups with avatar available are displayed in result.";
$lang['list_categories_groups']['params']['show_only_without_avatar']       = "If enabled, only groups with no avatar are displayed in result.";
$lang['list_categories_groups']['params']['show_only_with_categories']      = "If enabled, only groups with categories are displayed in result.";
$lang['list_categories_groups']['params']['show_only_with_description']     = "If enabled, only groups with non-empty description are displayed in result.";
$lang['list_categories_groups']['params']['pull_categories']                = "Enables ability to display a portion of categories for every category group. The number of categories and categories sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_categories_groups']['params']['pull_categories_count']          = "Specifies the number of categories that are selected for every category group (specify [kt|b]0[/kt|b] for all categories in the group).";
$lang['list_categories_groups']['params']['pull_categories_sort_by']        = "Specifies sorting for categories that are selected for every category group.";

$lang['list_categories_groups']['values']['pull_categories_sort_by']['sort_id']                 = "Sorting ID";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['title']                   = "Title";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['today_videos']            = "Videos added today";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['total_videos']            = "Total videos";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['today_albums']            = "Albums added today";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['total_albums']            = "Total albums";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_videos_rating']       = "Average video rating";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_videos_popularity']   = "Average video popularity";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['max_videos_ctr']          = "Video CTR";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_albums_rating']       = "Average album rating";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['avg_albums_popularity']   = "Average album popularity";
$lang['list_categories_groups']['values']['pull_categories_sort_by']['rand()']                  = "Random";
$lang['list_categories_groups']['values']['sort_by']['category_group_id']                       = "Group ID";
$lang['list_categories_groups']['values']['sort_by']['sort_id']                                 = "Sorting ID";
$lang['list_categories_groups']['values']['sort_by']['is_avatar_available']                     = "Avatar availability";
$lang['list_categories_groups']['values']['sort_by']['title']                                   = "Title";
$lang['list_categories_groups']['values']['sort_by']['dir']                                     = "Directory";
$lang['list_categories_groups']['values']['sort_by']['description']                             = "Description";
$lang['list_categories_groups']['values']['sort_by']['total_categories']                        = "Total categories";
$lang['list_categories_groups']['values']['sort_by']['total_videos']                            = "Total videos";
$lang['list_categories_groups']['values']['sort_by']['total_albums']                            = "Total albums";
$lang['list_categories_groups']['values']['sort_by']['rand()']                                  = "Random";

$lang['list_categories_groups']['block_short_desc'] = "Displays list of category groups with the given options";

$lang['list_categories_groups']['block_desc'] = "
	Block displays list of category groups with different sorting and filtering options. This block is a standard
	list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_categories_groups']['block_examples'] = "
	[kt|b]Display all category groups sorted alphabetically[/kt|b]
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

	[kt|b]Display category groups that have avatar and categories in them[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_avatar[kt|br]
	- show_only_with_categories[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
