<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once '../include/setup.php';
require_once '../include/setup_smarty.php';
require_once '../include/functions_base.php';
require_once '../include/functions.php';
require_once '../include/check_access.php';

header("Content-Type: text/xml; charset=utf-8");

$result_for = $_REQUEST['for'];
$result_for = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $result_for);
if (strlen($_REQUEST['for']) > 1)
{
	echo "<insight for=\"$result_for\">\n";
	$q = sql_escape($_REQUEST['for']);
	$data = mr2array_list(sql("select title from $config[tables_prefix_multi]stats_referers_list where referer like '%$q%' or title like '%$q%' order by referer asc"));
	foreach ($data as $referer)
	{
		$referer = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $referer);
		echo "<value>$referer</value>\n";
	}
	echo "</insight>";
} else
{
	echo "<insight for=\"$result_for\"></insight>";
}
