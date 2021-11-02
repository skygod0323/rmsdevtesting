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
	$data = mr2array(sql("select post_id, $config[tables_prefix]posts.title, $config[tables_prefix]posts_types.title as post_type from $config[tables_prefix]posts inner join $config[tables_prefix]posts_types on $config[tables_prefix]posts.post_type_id=$config[tables_prefix]posts_types.post_type_id where $config[tables_prefix]posts.title like '%$q%' or post_id like '%$q%' or $config[tables_prefix]posts_types.title like '%$q%' order by title asc"));
	foreach ($data as $post)
	{
		$id = $post['post_id'];
		$title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $post['title']);
		$type_title = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $post['post_type']);
		if ($title != '')
		{
			echo "<value id=\"$id\">$type_title - $id / $title</value>\n";
		} else
		{
			echo "<value id=\"$id\">$type_title - $id</value>\n";
		}
	}
	echo "</insight>";
} else
{
	echo "<insight for=\"$result_for\"></insight>";
}
