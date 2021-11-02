<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_tags messages
// =====================================================================================================================

$lang['list_tags']['groups']['pagination']      = $lang['website_ui']['block_group_default_pagination'];
$lang['list_tags']['groups']['sorting']         = $lang['website_ui']['block_group_default_sorting'];
$lang['list_tags']['groups']['static_filters']  = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_tags']['groups']['dynamic_filters'] = $lang['website_ui']['block_group_default_dynamic_filters'];

$lang['list_tags']['params']['items_per_page']                  = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_tags']['params']['links_per_page']                  = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_tags']['params']['var_from']                        = $lang['website_ui']['parameter_default_var_from'];
$lang['list_tags']['params']['var_items_per_page']              = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_tags']['params']['sort_by']                         = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_tags']['params']['var_sort_by']                     = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_tags']['params']['show_only_with_videos']           = "If enabled, only tags with the given amount of videos are displayed in result.";
$lang['list_tags']['params']['show_only_with_albums']           = "If enabled, only tags with the given amount of albums are displayed in result.";
$lang['list_tags']['params']['show_only_with_posts']            = "If enabled, only tags with the given amount of posts are displayed in result.";
$lang['list_tags']['params']['show_only_with_albums_or_videos'] = "If enabled, only tags with the given amount of videos or albums are displayed in result.";
$lang['list_tags']['params']['show_only_with_playlists']        = "If enabled, only tags with the given amount of playlists are displayed in result.";
$lang['list_tags']['params']['show_only_with_dvds']             = "If enabled, only tags with the given amount of DVDs / channels are displayed in result.";
$lang['list_tags']['params']['show_only_with_cs']               = "If enabled, only tags with the given amount of content sources are displayed in result.";
$lang['list_tags']['params']['show_only_with_models']           = "If enabled, only tags with the given amount of models are displayed in result.";
$lang['list_tags']['params']['var_title_section']               = "URL parameter, which provides title first characters to filter the list.";

$lang['list_tags']['values']['sort_by']['tag_id']                   = "Tag ID";
$lang['list_tags']['values']['sort_by']['tag']                      = "Title";
$lang['list_tags']['values']['sort_by']['tag_dir']                  = "Directory";
$lang['list_tags']['values']['sort_by']['today_videos']             = "Videos added today";
$lang['list_tags']['values']['sort_by']['total_videos']             = "Total videos";
$lang['list_tags']['values']['sort_by']['today_albums']             = "Albums added today";
$lang['list_tags']['values']['sort_by']['total_albums']             = "Total albums";
$lang['list_tags']['values']['sort_by']['today_posts']              = "Posts added today";
$lang['list_tags']['values']['sort_by']['total_posts']              = "Total posts";
$lang['list_tags']['values']['sort_by']['total_playlists']          = "Total playlists";
$lang['list_tags']['values']['sort_by']['total_dvds']               = "Total DVDs / channels";
$lang['list_tags']['values']['sort_by']['total_cs']                 = "Total content sources";
$lang['list_tags']['values']['sort_by']['total_models']             = "Total models";
$lang['list_tags']['values']['sort_by']['avg_videos_rating']        = "Average video rating";
$lang['list_tags']['values']['sort_by']['avg_videos_popularity']    = "Average video popularity";
$lang['list_tags']['values']['sort_by']['avg_albums_rating']        = "Average album rating";
$lang['list_tags']['values']['sort_by']['avg_albums_popularity']    = "Average album popularity";
$lang['list_tags']['values']['sort_by']['avg_posts_rating']         = "Average post rating";
$lang['list_tags']['values']['sort_by']['avg_posts_popularity']     = "Average post popularity";
$lang['list_tags']['values']['sort_by']['rand()']                   = "Random";

$lang['list_tags']['block_short_desc'] = "Displays list of tags with the given options";

$lang['list_tags']['block_desc'] = "
	Block displays list of tags with different sorting and filtering options. This block is a standard list block with
	pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_tags']['block_examples'] = "
	[kt|b]Display all tags sorted alphabetically[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = tag asc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display all tags starting with 'a'[/kt|b]
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

	[kt|b]Display tags with videos, 10 per page and sorted by videos count[/kt|b]
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
";
