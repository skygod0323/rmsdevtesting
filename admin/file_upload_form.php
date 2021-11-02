<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
header("Content-Type: text/html; charset=utf-8");
require_once 'include/setup.php';

session_start();
if ($_SESSION['userdata']['login']=='')
{
	header("Location: index.php");die;
}

require_once "$config[project_path]/admin/langs/english.php";
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php"))
{
	require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang'].".php";
}
if (is_file("$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php"))
{
	require_once "$config[project_path]/admin/langs/".$_SESSION['userdata']['lang']."/custom.php";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo $lang['system']['language_code'];?>">
<head>
	<title></title>
	<link rel="stylesheet" href="styles/default.css"/>
	<script type="text/javascript" src="js/admin.js?v=<?php echo substr(md5($config['project_version'] . $config['ahv']), 0, 16);?>"></script>
	<script type="text/javascript" src="js/config.php?v=<?php echo substr(md5($config['project_version'] . $config['ahv']), 0, 16);?>"></script>
</head>
<body id="iframe_content" onload="prepareAdminPanel();">
	<div id="file_upload_form">
		<form action="include/uploader.php" method="post" enctype="multipart/form-data" class="no_ajax">
			<table class="de">
				<colgroup>
					<col width="5%"/>
					<col/>
				</colgroup>
				<tr>
					<td colspan="2" class="de_control">
						<div class="de_lv_pair"><input type="radio" class="fuf_file" name="upload_type" value="1" checked="checked"/><span><?php echo $lang['uploader']['form_field_upload_type_file'];?></span></div>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required"><?php echo $lang['uploader']['form_field_file'];?> (*):</td>
					<td class="de_control">
						<input type="file" name="content" size="90" class="fixed_600"/>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="de_control">
						<div class="de_lv_pair"><input type="radio" class="fuf_url" name="upload_type" value="2"/><span><?php echo $lang['uploader']['form_field_upload_type_url'];?></span></div>
					</td>
				</tr>
				<tr>
					<td class="de_label de_required"><?php echo $lang['uploader']['form_field_url'];?> (*):</td>
					<td class="de_control">
						<input type="text" name="url" class="fixed_600" disabled="disabled"/>
					</td>
				</tr>
				<tr>
					<td class="de_action_group" colspan="2">
						<input type="hidden" name="filename"/>
						<input type="submit" value="<?php echo $lang['uploader']['form_btn_upload'];?>"/>
						<input type="button" value="<?php echo $lang['uploader']['form_btn_close'];?>"/>
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>