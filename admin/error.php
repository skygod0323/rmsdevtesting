<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$smarty=new mysmarty();
$smarty->assign('config',$config);
$smarty->assign('lang',$lang);
$smarty->assign('left_menu',"no");
$smarty->assign('template',"error.tpl");

if ($_REQUEST['error']=='permission_denied')
{
	$smarty->assign('page_title',$lang['validation']['access_denied_error']);
} elseif ($_REQUEST['error']=='page_doesnt_exist')
{
	$smarty->assign('page_title',$lang['validation']['page_doesnt_exist_error']);
} else {
	$smarty->assign('page_title',$lang['validation']['unexpected_error']);
}

$smarty->display("layout.tpl");
?>