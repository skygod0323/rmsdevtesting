<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$lang['plugins']['rotator_clicks_matrix']['title']          = "Rotator weighting matrixes";
$lang['plugins']['rotator_clicks_matrix']['description']    = "Provides ability to see clicks matrix for every list_xxx block, where rotator is enabled.";
$lang['plugins']['rotator_clicks_matrix']['long_desc']      = "
		Use this plugin to analyze the way clicks are distributed across your pages. If the rotator is supported by a
		list block (so far only list_videos supports it), this block will collect statistics on all the clicks made on
		it. Based on this statistics, click distribution matrixes are created, used by the rotator to determine the
		weight of each individual click. This information may be useful to you when finding the most clickable areas on
		your site etc.
";
$lang['permissions']['plugins|rotator_clicks_matrix']       = $lang['plugins']['rotator_clicks_matrix']['title'];

$lang['plugins']['rotator_clicks_matrix']['field_page']                 = "Page";
$lang['plugins']['rotator_clicks_matrix']['field_page_global']          = "* Global blocks *";
$lang['plugins']['rotator_clicks_matrix']['field_items_in_row']         = "Displayed items in a row";
$lang['plugins']['rotator_clicks_matrix']['field_items_in_row_hint']    = "rotator matrix is a plain array; specify the number of items displayed on page in a row so that it can be displayed in a human-readable form";
$lang['plugins']['rotator_clicks_matrix']['divider_matrix']             = "Rotator clicks stats";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_number']       = "By page number";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_number_page']  = "Page %1%";
$lang['plugins']['rotator_clicks_matrix']['field_by_page_position']     = "By position on page";
$lang['plugins']['rotator_clicks_matrix']['btn_display']                = "Display";
$lang['plugins']['rotator_clicks_matrix']['btn_reset']                  = "Reset selected";
$lang['plugins']['rotator_clicks_matrix']['btn_reset_confirm']          = "Are you sure to reset the selected click matrices?";
