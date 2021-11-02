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
	$data = mr2array(sql("select video_id, title from $config[tables_prefix]videos where title like '%$q%' or video_id like '%$q%' order by title asc"));
	foreach ($data as $video)
	{
		$id = $video['video_id'];
		$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $video['title']);
		echo "<value id=\"$id\">$id / $title</value>\n";
	}
	echo "</insight>";
} else
{
	echo "<insight for=\"$result_for\"></insight>";
}
