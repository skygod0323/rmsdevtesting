<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// =====================================================================================================================
// pagination messages
// =====================================================================================================================

$lang['pagination']['params']['links_per_page']         = "Specifies the maximum number of page links shown at the same time.";
$lang['pagination']['params']['related_block_ext_id']   = "Refers to the related list block. List block should be located above at the same page.";
$lang['pagination']['params']['url_prefix']             = "URL prefix, which will be used in all page links.";
$lang['pagination']['params']['se_friendly']            = "Enables SE friendly page links generation.";

$lang['pagination']['block_short_desc'] = "Provides pagination functionality for any list block";

$lang['pagination']['block_desc'] = "
	Block provides pagination links for any list block, which has pagination functionality enabled
	([kt|b]var_from[/kt|b] block parameter is specified).
	[kt|br][kt|br]

	Starting from 1.5.0 version all list blocks have pagination bundled into them. Separate pagination block
	should only be used if you want page links to redirect to another page ([kt|b]url_prefix[/kt|b] block
	parameter).
	[kt|br][kt|br]

	[kt|b]Display options and logic[/kt|b]
	[kt|br][kt|br]

	The related list block is specified using [kt|b]related_block_ext_id[/kt|b] block parameter.
	[kt|br][kt|br]

	If you want users to be redirected to list block located on the other page when they use page
	links (for example, pagination block is located on index.php, but you need users to be redirected
	to top_rated_videos.php after clicking on links), you need to use [kt|b]url_prefix[/kt|b] block
	parameter, which specifies URL prefix for all page links ([kt|b]/top_rated_videos.php?[/kt|b]
	for the above example).
	[kt|br][kt|br]

	[kt|b]Caching[/kt|b]
	[kt|br][kt|br]

	Block caching depends on related list block caching. Cache lifetime for this block should
	be the same, as for related list block.
";

?>