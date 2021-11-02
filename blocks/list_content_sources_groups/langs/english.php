<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_content_sources_groups messages
// =====================================================================================================================

$lang['list_content_sources_groups']['groups']['pagination']            = $lang['website_ui']['block_group_default_pagination'];
$lang['list_content_sources_groups']['groups']['sorting']               = $lang['website_ui']['block_group_default_sorting'];
$lang['list_content_sources_groups']['groups']['static_filters']        = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_content_sources_groups']['groups']['pull_content_sources']  = "Select content sources for each content source group";

$lang['list_content_sources_groups']['params']['items_per_page']                = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_content_sources_groups']['params']['links_per_page']                = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_content_sources_groups']['params']['var_from']                      = $lang['website_ui']['parameter_default_var_from'];
$lang['list_content_sources_groups']['params']['var_items_per_page']            = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_content_sources_groups']['params']['sort_by']                       = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_content_sources_groups']['params']['var_sort_by']                   = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_content_sources_groups']['params']['show_only_with_cs']             = "If enabled, only groups with content sources are displayed in result.";
$lang['list_content_sources_groups']['params']['show_only_with_description']    = "If enabled, only groups with non-empty description are displayed in result.";
$lang['list_content_sources_groups']['params']['pull_content_sources']          = "Enables ability to display a portion of content sources for every group. The number of content sources and their sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_content_sources_groups']['params']['pull_content_sources_count']    = "Specifies the number of content sources that are selected for every group.";
$lang['list_content_sources_groups']['params']['pull_content_sources_sort_by']  = "Specifies sorting for content sources that are selected for every group.";

$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['sort_id']               = "Sorting ID";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['title']                 = "Title";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['today_videos']          = "Videos added today";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['total_videos']          = "Total videos";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['today_albums']          = "Albums added today";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['total_albums']          = "Total albums";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_videos_rating']     = "Average video rating";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_videos_popularity'] = "Average video popularity";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_albums_rating']     = "Average album rating";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['avg_albums_popularity'] = "Average album popularity";
$lang['list_content_sources_groups']['values']['pull_content_sources_sort_by']['rand()']                = "Random";
$lang['list_content_sources_groups']['values']['sort_by']['content_source_group_id']                    = "Content source group ID";
$lang['list_content_sources_groups']['values']['sort_by']['sort_id']                                    = "Sorting ID";
$lang['list_content_sources_groups']['values']['sort_by']['title']                                      = "Title";
$lang['list_content_sources_groups']['values']['sort_by']['dir']                                        = "Directory";
$lang['list_content_sources_groups']['values']['sort_by']['total_content_sources']                      = "Total content sources";
$lang['list_content_sources_groups']['values']['sort_by']['total_videos']                               = "Total videos";
$lang['list_content_sources_groups']['values']['sort_by']['total_albums']                               = "Total albums";
$lang['list_content_sources_groups']['values']['sort_by']['rand()']                                     = "Random";

$lang['list_content_sources_groups']['block_short_desc'] = "Displays list of content source groups with the given options";

$lang['list_content_sources_groups']['block_desc'] = "
	Block displays list of content source groups with different sorting and filtering options. This block is a standard
	list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_content_sources_groups']['block_examples'] = "
	[kt|b]Display all content source groups sorted alphabetically[/kt|b]
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

	[kt|b]Display content source groups with content sources, 10 per page and sorted by content sources count[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_from = from[kt|br]
	- sort_by = content_sources desc[kt|br]
	- show_only_with_cs[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
