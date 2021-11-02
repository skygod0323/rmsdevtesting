<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// top_referers messages
// =====================================================================================================================

$lang['top_referers']['groups']['top']              = "Toplist settings";
$lang['top_referers']['groups']['static_filters']   = $lang['website_ui']['block_group_default_static_filters'];
$lang['top_referers']['groups']['dynamic_filters']  = $lang['website_ui']['block_group_default_dynamic_filters'];
$lang['top_referers']['groups']['modes']            = "Modes";

$lang['top_referers']['params']['items_per_page']   = "Specifies how many referers should be displayed in the toplist.";
$lang['top_referers']['params']['sort_by']          = "Specifies toplist sorting order.";
$lang['top_referers']['params']['stats_period']     = "Indicates how many days should be considered for every referer position calculation (in days).";
$lang['top_referers']['params']['show_referers']    = "If specified, only referers with these IDs will be displayed (comma separated list of referer IDs).";
$lang['top_referers']['params']['skip_referers']    = "If specified, referers with these IDs will not be displayed (comma separated list of referer IDs).";
$lang['top_referers']['params']['show_categories']  = "If specified, only referers from these categories will be displayed (comma separated list of category IDs).";
$lang['top_referers']['params']['skip_categories']  = "If specified, referers from these categories will not be displayed (comma separated list of category IDs).";
$lang['top_referers']['params']['var_video_dir']    = "URL parameter, which provides video directory to display referers related to one of this video's categories.";
$lang['top_referers']['params']['var_video_id']     = "URL parameter, which provides video ID to display referers related to one of this video's categories. Will be used instead of video directory if specified and provides a value.";
$lang['top_referers']['params']['var_category_dir'] = "URL parameter, which provides category directory to display referers related to this category.";
$lang['top_referers']['params']['var_category_id']  = "URL parameter, which provides category ID to display referers related to this category. Will be used instead of category directory if specified and provides a value.";
$lang['top_referers']['params']['mode_videos']      = "Enables video info selection for every referer in the list. Provides ability to render a video for each referer and imitate video list.";

$lang['top_referers']['values']['mode_videos']['duration']                                  = "Higher duration";
$lang['top_referers']['values']['mode_videos']['post_date']                                 = "Most recent";
$lang['top_referers']['values']['mode_videos']['last_time_view_date']                       = "Watched right now";
$lang['top_referers']['values']['mode_videos']['rating']                                    = "Top rated";
$lang['top_referers']['values']['mode_videos']['video_viewed']                              = "Most viewed";
$lang['top_referers']['values']['mode_videos']['rand()']                                    = "Random";
$lang['top_referers']['values']['sort_by']['referer_id']                                    = "Referer ID";
$lang['top_referers']['values']['sort_by']['title']                                         = "Title";
$lang['top_referers']['values']['sort_by']['uniq_amount']                                   = "Unique requests";
$lang['top_referers']['values']['sort_by']['total_amount']                                  = "Total requests";
$lang['top_referers']['values']['sort_by']['view_video_amount']                             = "Video visits";
$lang['top_referers']['values']['sort_by']['view_embed_amount']                             = "Embed loaded";
$lang['top_referers']['values']['sort_by']['cs_out_amount+adv_out_amount']                  = "Outs";
$lang['top_referers']['values']['sort_by']['total_amount/uniq_amount']                      = "Total / unique (ratio)";
$lang['top_referers']['values']['sort_by']['view_video_amount/uniq_amount']                 = "Video viewed / unique (ratio)";
$lang['top_referers']['values']['sort_by']['(cs_out_amount+adv_out_amount)/uniq_amount']    = "Outs / unique (ratio)";
$lang['top_referers']['values']['sort_by']['view_video_amount/total_amount']                = "Video viewed / total (ratio)";
$lang['top_referers']['values']['sort_by']['(cs_out_amount+adv_out_amount)/total_amount']   = "Outs / total (ratio)";
$lang['top_referers']['values']['sort_by']['rand()']                                        = "Random";

$lang['top_referers']['block_short_desc'] = "Renders toplist of site referers with the given options";

$lang['top_referers']['block_desc'] = "
	Block displays toplist of site referers with different sorting and filtering options. In order to have any referers
	displayed in this list, you should first add them in stats section of admin panel.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	[kt|b]{$lang['top_referers']['groups']['modes']}[/kt|b]
	[kt|br][kt|br]

	This block can be rendered as video list linking to your top referers. Enable this mode to force video data
	selection for each referer in the list. If a referer has category assigned, its video will also be selected from
	the same category. No duplicate videos will be selected.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['top_referers']['block_examples'] = "
	[kt|b]Display 20 random referers[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 referers related to the category with directory 'my_category'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=my_category
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 15 referers related to the video with ID '287' and select most popular video for every referer[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rand()[kt|br]
	- stats_period = 30[kt|br]
	- var_video_id = id[kt|br]
	- mode_videos = video_viewed[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?id=287
	[/kt|code]
";
