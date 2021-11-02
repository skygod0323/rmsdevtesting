<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/functions_base.php');
require_once('include/functions.php');

session_start();

if ($_SESSION['userdata']['user_id']>0)
{
	if ($_SESSION['saved_serialized']<>serialize($_SESSION['save']))
	{
		$_SESSION['saved_serialized']=serialize($_SESSION['save']);
		sql_pr("update $config[tables_prefix_multi]admin_users set preference=? where user_id=?",$_SESSION['saved_serialized'],$_SESSION['userdata']['user_id']);
	}
}

session_destroy();

header("Location: index.php");
