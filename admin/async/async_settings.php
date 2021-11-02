<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once '../include/setup.php';
require_once '../include/setup_smarty.php';
require_once '../include/functions_base.php';
require_once '../include/functions.php';
require_once '../include/check_access.php';

if (isset($_REQUEST['setting'], $_REQUEST['value']))
{
	$_SESSION['save']['options'][trim($_REQUEST['setting'])] = trim($_REQUEST['value']);
}

header("Content-type: image/gif");
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');