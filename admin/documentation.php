<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$template_path="$config[project_path]/admin/docs/russian/";

if ($_GET['doc_id']=='whats_new')
{
	$template_path="$config[project_path]/admin/docs/".$_SESSION['userdata']['lang']."/";
	$template_path.="whats_new.tpl";
} elseif ($_GET['doc_id']=='quick_start')
{
	$template_path="$config[project_path]/admin/docs/".$_SESSION['userdata']['lang']."/";
	$template_path.="quick_start.tpl";
} elseif ($_GET['doc_id']=='settings')
{
	$template_path="$config[project_path]/admin/docs/".$_SESSION['userdata']['lang']."/";
	$template_path.="settings.tpl";
} elseif ($_GET['doc_id']=='website_ui')
{
	$template_path="$config[project_path]/admin/docs/".$_SESSION['userdata']['lang']."/";
	$template_path.="website_ui.tpl";
} else {
	$template_path="$config[project_path]/admin/docs/".$_SESSION['userdata']['lang']."/";
	$template_path.="doc_index.tpl";
}

$smarty=new mysmarty();
$smarty->assign('left_menu',"no");
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('template',$template_path);

$smarty->assign('page_title',$lang['common']['documentation']);

$smarty->display("layout.tpl");
