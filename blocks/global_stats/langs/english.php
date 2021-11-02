<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// global_stats messages
// =====================================================================================================================

$lang['global_stats']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['global_stats']['groups']['performance']      = $lang['website_ui']['block_group_default_performance'];

$lang['global_stats']['params']['var_category_dir']     = "URL parameter, which provides category directory. If specified, only objects from category with this directory will be counted in stats.";
$lang['global_stats']['params']['var_category_id']      = "URL parameter, which provides category ID. If specified, only objects from category with this ID will be counted in stats.";
$lang['global_stats']['params']['skip_videos']          = "Disables videos statistics.";
$lang['global_stats']['params']['skip_albums']          = "Disables albums statistics.";
$lang['global_stats']['params']['skip_members']         = "Disables community statistics.";
$lang['global_stats']['params']['skip_content_sources'] = "Disables content sources statistics.";
$lang['global_stats']['params']['skip_models']          = "Disables models statistics.";
$lang['global_stats']['params']['skip_dvds']            = "Disables DVDs / channels / TV series statistics.";
$lang['global_stats']['params']['skip_traffic']         = "Disables traffic statistics.";

$lang['global_stats']['block_short_desc'] = "Displays global website statistics summary";

$lang['global_stats']['block_desc'] = "
	Block displays summary statistics on various website aspects: the number of videos, albums, members, etc.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";
