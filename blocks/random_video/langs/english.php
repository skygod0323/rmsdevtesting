<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// random_video messages
// =====================================================================================================================

$lang['random_video']['groups']['random_selection'] = "Initial set selection";
$lang['random_video']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['random_video']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];

$lang['random_video']['params']['initial_set_count']        = "Specifies the size of initial set. A video will be randomly selected from this set.";
$lang['random_video']['params']['sort_by']                  = "Specifies initial set sorting.";
$lang['random_video']['params']['skip_categories']          = "If specified, videos from these categories will not be selected into initial set (comma separated list of category IDs).";
$lang['random_video']['params']['show_categories']          = "If specified, only videos from these categories will be selected into initial set (comma separated list of category IDs).";
$lang['random_video']['params']['skip_tags']                = "If specified, videos with these tags will not be selected into initial set (comma separated list of tag IDs).";
$lang['random_video']['params']['show_tags']                = "If specified, only videos with these tags will be selected into initial set (comma separated list of tag IDs).";
$lang['random_video']['params']['skip_content_sources']     = "If specified, videos from these content sources will not be selected into initial set (comma separated list of content source IDs).";
$lang['random_video']['params']['show_content_sources']     = "If specified, only videos from these content sources will be selected into initial set (comma separated list of content source IDs).";
$lang['random_video']['params']['skip_dvds']                = "If specified, videos from these DVDs will not be selected into initial set (comma separated list of DVD IDs).";
$lang['random_video']['params']['show_dvds']                = "If specified, only videos from these DVDs will be selected into initial set (comma separated list of DVD IDs).";
$lang['random_video']['params']['days_passed_from']         = "Allows filtering by publishing date, e.g. videos added today, yesterday and etc. Specifies the upper limit in number of days passed from today.";
$lang['random_video']['params']['days_passed_to']           = "Allows filtering by publishing date, e.g. videos added today, yesterday and etc. Specifies the lower limit in number of days passed from today. Should be greater than value specified in [kt|b]days_passed_from[/kt|b] parameter.";
$lang['random_video']['params']['is_private']               = "If specified, only videos with these visibility will be selected into initial set.";
$lang['random_video']['params']['var_category_dir']         = "URL parameter, which provides category directory. If specified, only videos from category with this directory will be selected into initial set.";
$lang['random_video']['params']['var_category_id']          = "URL parameter, which provides category ID. If specified, only videos from category with this ID will be selected into initial set.";
$lang['random_video']['params']['var_tag_dir']              = "URL parameter, which provides tag directory. If specified, only videos that have tag with this directory will be selected into initial set.";
$lang['random_video']['params']['var_tag_id']               = "URL parameter, which provides tag ID. If specified, only videos that have tag with this ID will be selected into initial set.";
$lang['random_video']['params']['var_model_dir']            = "URL parameter, which provides model directory. If specified, only videos that have model with this directory will be selected into initial set.";
$lang['random_video']['params']['var_model_id']             = "URL parameter, which provides model ID. If specified, only videos that have model with this ID will be selected into initial set.";
$lang['random_video']['params']['var_content_source_dir']   = "URL parameter, which provides content source directory. If specified, only videos from content source with this directory will be selected into initial set.";
$lang['random_video']['params']['var_content_source_id']    = "URL parameter, which provides content source ID. If specified, only videos from content source with this ID will be selected into initial set.";
$lang['random_video']['params']['var_dvd_dir']              = "URL parameter, which provides DVD directory. If specified, only videos from DVD with this directory will be selected into initial set.";
$lang['random_video']['params']['var_dvd_id']               = "URL parameter, which provides DVD ID. If specified, only videos from DVD with this ID will be selected into initial set.";

$lang['random_video']['values']['is_private']['0']                  = "Public only";
$lang['random_video']['values']['is_private']['1']                  = "Private only";
$lang['random_video']['values']['is_private']['2']                  = "Premium only";
$lang['random_video']['values']['is_private']['0|1']                = "Public and private only";
$lang['random_video']['values']['is_private']['0|2']                = "Public and premium only";
$lang['random_video']['values']['is_private']['1|2']                = "Private and premium only";
$lang['random_video']['values']['sort_by']['duration']              = "Duration";
$lang['random_video']['values']['sort_by']['post_date']             = "Published on";
$lang['random_video']['values']['sort_by']['last_time_view_date']   = "Last viewed";
$lang['random_video']['values']['sort_by']['rating']                = "Overall rating";
$lang['random_video']['values']['sort_by']['rating_today']          = "Rating today";
$lang['random_video']['values']['sort_by']['rating_week']           = "Rating this week";
$lang['random_video']['values']['sort_by']['rating_month']          = "Rating this month";
$lang['random_video']['values']['sort_by']['video_viewed']          = "Overall popularity";
$lang['random_video']['values']['sort_by']['video_viewed_today']    = "Popularity today";
$lang['random_video']['values']['sort_by']['video_viewed_week']     = "Popularity this week";
$lang['random_video']['values']['sort_by']['video_viewed_month']    = "Popularity this month";
$lang['random_video']['values']['sort_by']['most_favourited']       = "Most favourited";
$lang['random_video']['values']['sort_by']['most_commented']        = "Most commented";
$lang['random_video']['values']['sort_by']['pseudo_rand']           = "Pseudo random (faster performance)";
$lang['random_video']['values']['sort_by']['rand()']                = "Random";

$lang['random_video']['block_short_desc'] = "Displays random video data";

$lang['random_video']['block_desc'] = "
	Block displays data of a random video. It works by selecting a set of videos based on the given filtering and
	sorting and then selects random video from that set.
	[kt|br][kt|br]

	Block also provides the following functionality:
	[kt|br][kt|br]

	- Rate video once from a single IP.[kt|br]
	- Flag video once from a single IP.[kt|br]
	[kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['random_video']['block_examples'] = "
	[kt|b]Display random video from the 10 most popular videos[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- initial_set_count = 10[kt|br]
	- sort_by = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display random video from all videos added today[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- initial_set_count = 999999[kt|br]
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

	[kt|b]Display random video from the 15 videos with the highest rating during last week in category with directory 'my_category'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- initial_set_count = 15[kt|br]
	- sort_by = rating_week[kt|br]
	- var_category_dir = category_dir[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category_dir=my_category
	[/kt|code]
";
