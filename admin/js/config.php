<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
header("Content-Type: application/javascript");

require_once '../include/setup.php';

session_start();
require_once "$config[project_path]/admin/langs/english.php";
if ($_SESSION['userdata']['lang']<>'' && $_SESSION['userdata']['lang']<>'english' && is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php";
}
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php"))
{
	require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php";
}

$file_upload_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"));

?>
var KTLanguagePack = new Object();

// upload messages
KTLanguagePack['ktfuc_ie_not_supported'] = '<?php echo $lang['javascript']['ktfuc_ie_not_supported'];?>';
KTLanguagePack['ktfuf_file_error'] = '<?php echo $lang['javascript']['ktfuf_file_error'];?>';
KTLanguagePack['ktfuf_url_error'] = '<?php echo $lang['javascript']['ktfuf_url_error'];?>';
KTLanguagePack['ktfuf_ext_error'] = '<?php echo str_replace('%1%', '${formats}', $lang['javascript']['ktfuf_ext_error']);?>';
KTLanguagePack['ktfuf_n_files'] = '<?php echo str_replace('%1%', '${number}', $lang['javascript']['ktfuf_n_files']);?>';
KTLanguagePack['ktfudc_preparing'] = '<?php echo $lang['javascript']['ktfudc_preparing'];?>';
KTLanguagePack['ktfudc_uploading'] = '<?php echo str_replace(array('%1%', '%2%', '%3%', '%4%'), array('${timeLeft}', '${loaded}', '${total}', '${speed}'), $lang['javascript']['ktfudc_uploading']);?>';
KTLanguagePack['ktfudc_finished'] = '<?php echo $lang['javascript']['ktfudc_finished'];?>';
KTLanguagePack['ktfudc_filesize_error'] = '<?php echo $lang['javascript']['ktfudc_filesize_error'];?>';
KTLanguagePack['ktfudc_unexpected_error'] = '<?php echo $lang['javascript']['ktfudc_unexpected_error'];?>';

// ajax messages
KTLanguagePack['kta_xmlr_error'] = '<?php echo $lang['javascript']['kta_xmlr_error'];?>';
KTLanguagePack['kta_browser_error'] = '<?php echo str_replace('%1%', '${error}', $lang['javascript']['kta_browser_error']);?>';
KTLanguagePack['kta_server_error'] = '<?php echo str_replace('%1%', '${error}', $lang['javascript']['kta_server_error']);?>';
KTLanguagePack['kta_unexpected_error'] = '<?php echo $lang['javascript']['kta_unexpected_error'];?>';
KTLanguagePack['kta_unexpected_response_error'] = '<?php echo $lang['javascript']['kta_unexpected_response_error'];?>';

// size messages
KTLanguagePack['bytes'] = '<?php echo $lang['javascript']['bytes'];?>';
KTLanguagePack['kilo_bytes'] = '<?php echo $lang['javascript']['kilo_bytes'];?>';
KTLanguagePack['mega_bytes'] = '<?php echo $lang['javascript']['mega_bytes'];?>';
KTLanguagePack['giga_bytes'] = '<?php echo $lang['javascript']['giga_bytes'];?>';

// time messages
KTLanguagePack['seconds'] = '<?php echo $lang['javascript']['seconds'];?>';
KTLanguagePack['minutes'] = '<?php echo $lang['javascript']['minutes'];?>';
KTLanguagePack['hours'] = '<?php echo $lang['javascript']['hours'];?>';

// image list layer messages
KTLanguagePack['image_list_text'] = '<?php echo str_replace(array('%1%', '%2%'), array('${item}', '${total}'), $lang['javascript']['image_list_text']);?>';
KTLanguagePack['image_list_text_loading'] = '<?php echo str_replace(array('%1%', '%2%'), array('${item}', '${total}'), $lang['javascript']['image_list_text_loading']);?>';
KTLanguagePack['image_list_text_error'] = '<?php echo $lang['javascript']['image_list_text_error'];?>';
KTLanguagePack['image_list_btn_prev'] = '<?php echo $lang['javascript']['image_list_btn_prev'];?>';
KTLanguagePack['image_list_btn_next'] = '<?php echo $lang['javascript']['image_list_btn_next'];?>';
KTLanguagePack['image_list_main'] = '<?php echo $lang['javascript']['image_list_main'];?>';
KTLanguagePack['image_list_delete'] = '<?php echo $lang['javascript']['image_list_delete'];?>';

// other messages
KTLanguagePack['post_wait_popup_text'] = '<?php echo $lang['javascript']['post_wait_popup_text'];?>';
KTLanguagePack['post_progress_popup_text'] = '<?php echo $lang['javascript']['post_progress_popup_text'];?>';
KTLanguagePack['no_items_found'] = '<?php echo $lang['javascript']['no_items_found'];?>';
KTLanguagePack['new_item'] = '<?php echo str_replace('%1%', '${object}', $lang['javascript']['new_item']);?>';
KTLanguagePack['new_item_marker'] = '<?php echo $lang['javascript']['new_item_marker'];?>';
KTLanguagePack['insight_hint'] = '<?php echo $lang['javascript']['insight_hint'];?>';
KTLanguagePack['preset_delete_confirm'] = '<?php echo $lang['javascript']['preset_delete_confirm'];?>';
KTLanguagePack['profile_delete_confirm'] = '<?php echo $lang['javascript']['profile_delete_confirm'];?>';
KTLanguagePack['change_translit_rules_confirm'] = '<?php echo $lang['javascript']['change_translit_rules_confirm'];?>';
KTLanguagePack['disable_website_confirm'] = '<?php echo $lang['javascript']['disable_website_confirm'];?>';
KTLanguagePack['disable_website_caching_confirm'] = '<?php echo $lang['javascript']['disable_website_caching_confirm'];?>';
KTLanguagePack['feed_duplicate_prefix_change_confirm'] = '<?php echo str_replace('%1%',$lang['videos']['feed_field_key_prefix'],$lang['javascript']['feed_duplicate_prefix_change_confirm']);?>';

var KTConfig = new Object();

KTConfig['ajax_default_timeout_ms'] = 20000;
KTConfig['wait_popup_timeout_ms'] = 2000;
KTConfig['form_progress_status_timeout_ms'] = 500;

<?php if (strpos($config['project_url'], 'https://') !== false) : ?>
KTConfig['is_https'] = 'true';
<?php endif; ?>

<?php if($_SESSION['userdata']['login']!='') : ?>

KTConfig['file_upload_chunk_size'] = 9 * 1024 * 1024;
KTConfig['file_upload_max_size'] = <?php echo intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']);?> * 1024 * 1024;

KTConfig['file_upload_form_url'] = 'file_upload_form.php';
KTConfig['file_upload_status_url'] = 'include/get_upload_status.php?file=${hash}&rand=${rand}&format=json';
KTConfig['file_upload_status_timeout_ms'] = 500;

<?php endif; ?>

KTConfig['data_editor_use_popups'] = <?php echo $_SESSION['userdata']['is_popups_enabled']=='1' ? "true" : "false";?>;

<?php
	if ($_REQUEST['is_popup']==true)
	{
		echo "KTConfig['is_running_in_popup'] = true;";
	}
?>