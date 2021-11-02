<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');
require_once('include/database_selectors.php');

$list_post_types = mr2array(sql("select * from $config[tables_prefix]posts_types order by title asc"));
$locked_post_type = null;
if ($_REQUEST['post_type_external_id'] != '')
{
	foreach ($list_post_types as $post_type)
	{
		if ($post_type['external_id'] == $_REQUEST['post_type_external_id'])
		{
			$locked_post_type = $post_type;
		}
	}
}

if (!isset($locked_post_type))
{
	header("Location: posts.php");
	die;
}

$page_name="posts_for_$locked_post_type[external_id].php";
require_once('posts.php');
