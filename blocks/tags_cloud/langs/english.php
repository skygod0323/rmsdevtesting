<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// tags_cloud messages
// =====================================================================================================================

$lang['tags_cloud']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['tags_cloud']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['tags_cloud']['groups']['sizes']          = "Font sizes";
$lang['tags_cloud']['groups']['display_modes']  = $lang['website_ui']['block_group_default_display_modes'];
$lang['tags_cloud']['groups']['categories']     = "Categories cloud";

$lang['tags_cloud']['params']['items_per_page']             = "Limits the displayed number of tags. Set to [kt|b]0[/kt|b] to render all available tags.";
$lang['tags_cloud']['params']['sort_by']                    = "Tags sorting within the cloud.";
$lang['tags_cloud']['params']['size_from']                  = "Font size in pixels for the least used (smallest) tag.";
$lang['tags_cloud']['params']['size_to']                    = "Font size in pixels for the most used (biggest) tag.";
$lang['tags_cloud']['params']['bold_from']                  = "Tags with font size bigger than this will be displayed in bold text.";
$lang['tags_cloud']['params']['mode_albums']                = "If enabled, album tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_posts']                 = "If enabled, post tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_playlists']             = "If enabled, playlist tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_dvds']                  = "If enabled, DVD tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_cs']                    = "If enabled, content source tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_models']                = "If enabled, model tags are displayed instead of video tags.";
$lang['tags_cloud']['params']['mode_categories']            = "If enabled, categories are displayed instead of tags.";
$lang['tags_cloud']['params']['show_only_with_avatar']      = "If enabled, only categories with avatar available are displayed in result.";
$lang['tags_cloud']['params']['show_only_without_avatar']   = "If enabled, only categories with no avatar are displayed in result.";
$lang['tags_cloud']['params']['show_only_with_items']       = "If enabled, only categories with content are displayed in result.";

$lang['tags_cloud']['values']['sort_by']['title']   = "Title";
$lang['tags_cloud']['values']['sort_by']['amount']  = "Objects count";
$lang['tags_cloud']['values']['sort_by']['rand()']  = "Random";

$lang['tags_cloud']['block_short_desc'] = "Displays tag cloud from tags or categories used on your site";

$lang['tags_cloud']['block_desc'] = "
	Block displays tag cloud based on tags or categories used on your site.
	[kt|br][kt|br]

	[kt|b]Font sizes[/kt|b]
	[kt|br][kt|br]

	Tag cloud is rendered as a list of tags with all tags having different sizes based on their usage frequency, so
	that the most popular tags are of bigger size. Parameters under this section allow you to control the absolute font
	size values to be rendered.
	[kt|br][kt|br]

	[kt|b]Display modes[/kt|b]
	[kt|br][kt|br]

	By default tags usage is calculated from videos, but this can be changed by switching into specific display modes.
	[kt|br][kt|br]

	[kt|b]Categories cloud[/kt|b]
	[kt|br][kt|br]

	You can also render categories as a cloud in this block.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['tags_cloud']['block_examples'] = "
	[kt|b]Display full tag cloud in random order[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = rand()[kt|br]
	- size_from = 12[kt|br]
	- size_to = 19[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
