<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_albums_images messages
// =====================================================================================================================

$lang['list_albums_images']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['list_albums_images']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['list_albums_images']['groups']['static_filters'] = $lang['website_ui']['block_group_default_static_filters'];

$lang['list_albums_images']['params']['items_per_page']     = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_albums_images']['params']['links_per_page']     = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_albums_images']['params']['var_from']           = $lang['website_ui']['parameter_default_var_from'];
$lang['list_albums_images']['params']['var_items_per_page'] = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_albums_images']['params']['sort_by']            = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_albums_images']['params']['var_sort_by']        = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_albums_images']['params']['is_private']         = "Static filtering by album type. Only photos from albums with these types will be displayed.";
$lang['list_albums_images']['params']['format']             = "Static filtering by image format. The following formats are supported: [kt|b]jpg[/kt|b], [kt|b]gif[/kt|b], [kt|b]png[/kt|b].";

$lang['list_albums_images']['values']['is_private']['0']            = "Public and private";
$lang['list_albums_images']['values']['is_private']['1']            = "Public only";
$lang['list_albums_images']['values']['is_private']['2']            = "Private only";
$lang['list_albums_images']['values']['sort_by']['image_id']        = "Photo ID";
$lang['list_albums_images']['values']['sort_by']['title']           = "Title";
$lang['list_albums_images']['values']['sort_by']['rating']          = "Rating";
$lang['list_albums_images']['values']['sort_by']['image_viewed']    = "Popularity";
$lang['list_albums_images']['values']['sort_by']['added_date']      = "Creation date";
$lang['list_albums_images']['values']['sort_by']['rand()']          = "Random";

$lang['list_albums_images']['block_short_desc'] = "Displays list of album photos with the given options";

$lang['list_albums_images']['block_desc'] = "
	Block displays list of album photos with different sorting and filtering options. This block is a standard list
	block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_dynamic_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_albums_images']['block_examples'] = "
	[kt|b]Display 20 images per page sorted by creation date[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 20[kt|br]
	- var_from = from[kt|br]
	- sort_by = added_date desc[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 top rated images[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- sort_by = rating[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 15 public GIF images in random order[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 15[kt|br]
	- sort_by = rand()[kt|br]
	- is_private = 1[kt|br]
	- format = gif[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
