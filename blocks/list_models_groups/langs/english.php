<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// list_models_groups messages
// =====================================================================================================================

$lang['list_models_groups']['groups']['pagination']     = $lang['website_ui']['block_group_default_pagination'];
$lang['list_models_groups']['groups']['sorting']        = $lang['website_ui']['block_group_default_sorting'];
$lang['list_models_groups']['groups']['static_filters'] = $lang['website_ui']['block_group_default_static_filters'];
$lang['list_models_groups']['groups']['pull_models']    = "Select models for each model group";

$lang['list_models_groups']['params']['items_per_page']             = $lang['website_ui']['parameter_default_items_per_page'];
$lang['list_models_groups']['params']['links_per_page']             = $lang['website_ui']['parameter_default_links_per_page'];
$lang['list_models_groups']['params']['var_from']                   = $lang['website_ui']['parameter_default_var_from'];
$lang['list_models_groups']['params']['var_items_per_page']         = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['list_models_groups']['params']['sort_by']                    = $lang['website_ui']['parameter_default_sort_by'];
$lang['list_models_groups']['params']['var_sort_by']                = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['list_models_groups']['params']['show_only_with_screenshot1'] = "If enabled, only groups with screenshot #1 available are displayed in result.";
$lang['list_models_groups']['params']['show_only_with_screenshot2'] = "If enabled, only groups with screenshot #2 available are displayed in result.";
$lang['list_models_groups']['params']['show_only_with_models']      = "If enabled, only groups with models are displayed in result.";
$lang['list_models_groups']['params']['show_only_with_description'] = "If enabled, only groups with non-empty description are displayed in result.";
$lang['list_models_groups']['params']['pull_models']                = "Enables ability to display a portion of models for every model group. The number of models and models sorting are configured in separate block parameters. Using this parameter will decrease overall block performance.";
$lang['list_models_groups']['params']['pull_models_count']          = "Specifies the number of models that are selected for every model group (specify [kt|b]0[/kt|b] for all models in the group).";
$lang['list_models_groups']['params']['pull_models_sort_by']        = "Specifies sorting for models that are selected for every model group.";

$lang['list_models_groups']['values']['pull_models_sort_by']['sort_id']                 = "Sorting ID";
$lang['list_models_groups']['values']['pull_models_sort_by']['title']                   = "Title";
$lang['list_models_groups']['values']['pull_models_sort_by']['today_videos']            = "Videos added today";
$lang['list_models_groups']['values']['pull_models_sort_by']['total_videos']            = "Total videos";
$lang['list_models_groups']['values']['pull_models_sort_by']['today_albums']            = "Albums added today";
$lang['list_models_groups']['values']['pull_models_sort_by']['total_albums']            = "Total albums";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_videos_rating']       = "Average video rating";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_videos_popularity']   = "Average video popularity";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_albums_rating']       = "Average album rating";
$lang['list_models_groups']['values']['pull_models_sort_by']['avg_albums_popularity']   = "Average album popularity";
$lang['list_models_groups']['values']['pull_models_sort_by']['rand()']                  = "Random";
$lang['list_models_groups']['values']['sort_by']['model_group_id']                      = "Group ID";
$lang['list_models_groups']['values']['sort_by']['sort_id']                             = "Sorting ID";
$lang['list_models_groups']['values']['sort_by']['title']                               = "Title";
$lang['list_models_groups']['values']['sort_by']['dir']                                 = "Directory";
$lang['list_models_groups']['values']['sort_by']['description']                         = "Description";
$lang['list_models_groups']['values']['sort_by']['total_models']                        = "Total models";
$lang['list_models_groups']['values']['sort_by']['total_videos']                        = "Total videos";
$lang['list_models_groups']['values']['sort_by']['total_albums']                        = "Total albums";
$lang['list_models_groups']['values']['sort_by']['rand()']                              = "Random";

$lang['list_models_groups']['block_short_desc'] = "Displays list of model groups with the given options";

$lang['list_models_groups']['block_desc'] = "
	Block displays list of model groups with different sorting and filtering options. This block is a standard
	list block with pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_subselects']}
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['list_models_groups']['block_examples'] = "
	[kt|b]Display all model groups sorted alphabetically[/kt|b]
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

	[kt|b]Display model groups that have screenshot #1 and models in them[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 0[kt|br]
	- sort_by = title asc[kt|br]
	- show_only_with_screenshot1[kt|br]
	- show_only_with_models[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
";
