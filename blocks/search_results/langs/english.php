<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// search_results messages
// =====================================================================================================================

$lang['search_results']['groups']['pagination']         = $lang['website_ui']['block_group_default_pagination'];
$lang['search_results']['groups']['sorting']            = $lang['website_ui']['block_group_default_sorting'];
$lang['search_results']['groups']['static_filters']     = $lang['website_ui']['block_group_default_static_filters'];
$lang['search_results']['groups']['search']             = "Similar queries";
$lang['search_results']['groups']['sizes']              = "Tag cloud mode";

$lang['search_results']['params']['items_per_page']         = $lang['website_ui']['parameter_default_items_per_page'];
$lang['search_results']['params']['links_per_page']         = $lang['website_ui']['parameter_default_links_per_page'];
$lang['search_results']['params']['var_from']               = $lang['website_ui']['parameter_default_var_from'];
$lang['search_results']['params']['var_items_per_page']     = $lang['website_ui']['parameter_default_var_items_per_page'];
$lang['search_results']['params']['sort_by']                = $lang['website_ui']['parameter_default_sort_by'];
$lang['search_results']['params']['var_sort_by']            = $lang['website_ui']['parameter_default_var_sort_by'];
$lang['search_results']['params']['days']                   = "Can be used to select only search queries submitted during the last N days (inclusive).";
$lang['search_results']['params']['query_length_min']       = "Limits the minimum length of displayed search queries (inclusive).";
$lang['search_results']['params']['query_length_max']       = "Limits the maximum length of displayed search queries (inclusive).";
$lang['search_results']['params']['query_results_min']      = "Shows only queries that have found at least this number of results when last submitted (inclusive).";
$lang['search_results']['params']['query_results_min_type'] = "Configures whether [kt|b]query_results_min[/kt|b] parameter is based on videos or albums, or both.";
$lang['search_results']['params']['query_amount_min']       = "Shows only queries that have been submitted at least this number of times (inclusive).";
$lang['search_results']['params']['var_query']              = "URL parameter, which provides query to select similar search queries (related queries for the given query).";
$lang['search_results']['params']['var_category_id']        = "URL parameter, which provides category ID to select similar search queries (related queries for the given category).";
$lang['search_results']['params']['var_category_dir']       = "URL parameter, which provides category directory to select similar search queries (related queries for the given category).";
$lang['search_results']['params']['var_tag_id']             = "URL parameter, which provides tag ID to select similar search queries (related queries for the given tag).";
$lang['search_results']['params']['var_tag_dir']            = "URL parameter, which provides tag directory to select similar search queries (related queries for the given tag).";
$lang['search_results']['params']['search_method']          = "Specifies method to find similar queries.";
$lang['search_results']['params']['sort_by_relevance']      = "Sorts queries by their similarity relevance displaying most similar queries first. Works only if full text index is used in [kt|b]search_method[/kt|b] parameter.";
$lang['search_results']['params']['size_from']              = "Font size in pixels for the query with the least found results (smallest).";
$lang['search_results']['params']['size_to']                = "Font size in pixels for the query with the most found results (biggest).";
$lang['search_results']['params']['bold_from']              = "Queries with font size bigger than this will be displayed in bold text.";

$lang['search_results']['values']['query_results_min_type']['0']        = "Videos and albums";
$lang['search_results']['values']['query_results_min_type']['1']        = "Only videos";
$lang['search_results']['values']['query_results_min_type']['2']        = "Only albums";
$lang['search_results']['values']['search_method']['1']                 = "Whole query match";
$lang['search_results']['values']['search_method']['2']                 = "Any query part match";
$lang['search_results']['values']['search_method']['3']                 = "Full-text index (natural mode)";
$lang['search_results']['values']['search_method']['4']                 = "Full-text index (boolean mode)";
$lang['search_results']['values']['search_method']['5']                 = "Full-text index (with query expansion)";
$lang['search_results']['values']['sort_by']['query']                   = "Query text";
$lang['search_results']['values']['sort_by']['amount']                  = "Queries count";
$lang['search_results']['values']['sort_by']['query_results_total']     = "Found objects count";
$lang['search_results']['values']['sort_by']['query_results_videos']    = "Found videos count";
$lang['search_results']['values']['sort_by']['query_results_albums']    = "Found albums count";
$lang['search_results']['values']['sort_by']['pseudo_rand']             = "Pseudo random (fast)";
$lang['search_results']['values']['sort_by']['rand()']                  = "Random (slow)";

$lang['search_results']['block_short_desc'] = "Displays search queries submitted by users on your site";

$lang['search_results']['block_desc'] = "
	Block displays list of search queries submitted by users on your site. This block is a standard list block with
	pagination support.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_static_filters']}
	[kt|br][kt|br]

	[kt|b]Similar queries[/kt|b]
	[kt|br][kt|br]

	This block provides extremely powerful SEO features to display similar search queries to any text provided in the
	URL or to any category / tag in the URL.
	[kt|br][kt|br]

	[kt|b]Tag cloud mode[/kt|b]
	[kt|br][kt|br]

	Tag cloud is rendered as a list of queries with all queries having different sizes based on the number of results
	they produced, so that queries with more results are of bigger size. Parameters under this section allow you to
	control the absolute font size values to be rendered.
	[kt|br][kt|br]

	{$lang['website_ui']['block_desc_default_caching_default']}
";

$lang['search_results']['block_examples'] = "
	[kt|b]Display top 10 search queries[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 queries similar to query 'cars'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_search = q[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?q=cars
	[/kt|code]
	[kt|br][kt|br]

	[kt|b]Display 10 queries similar to category with directory 'auto'[/kt|b]
	[kt|br][kt|br]

	Block parameters:[kt|br]
	[kt|code]
	- items_per_page = 10[kt|br]
	- var_category_dir = category[kt|br]
	[/kt|code]
	[kt|br][kt|br]

	Page link:[kt|br]
	[kt|code]
	{$config['project_url']}/page.php?category=auto
	[/kt|code]
";
