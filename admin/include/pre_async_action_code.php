<?php

/* Developed by Kernel Team.

   http://kernel-team.com



   All php code within this file will be executed before async actions inside blocks.

   It is possible to hook the needed action using the following condition:

   if ($_REQUEST['action'] == 'xxxx')

   {

       // do your custom pre-processing

   }

*/

function video_editPreProcess(&$blockConfig, $block)
{
    if ($block !== 'video_edit_video_edit') {
        return;
    }

    $blockConfig['set_custom_flag1'] = intval($_POST['custom1']) ?? 0;
}
function signupPreProcess(&$blockConfig, $block) {
	global $config;

	$idField = 'content_source_id';
	$titleField = 'title';
	$tokensAmountField = 'custom1';
	$statusField = 'status_id';
	$result = mr2array(sql_pr("select $idField, $titleField, $tokensAmountField from $config[tables_prefix]content_sources where $statusField <> 0"));
	$availableCharities = [];

	foreach ($result as $i) {
		$availableCharities[$i[$idField]] = $i[$titleField];
	}

	define('list_available_charities', $availableCharities);
}
