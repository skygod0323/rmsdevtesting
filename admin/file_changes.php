<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$table_name = "$config[tables_prefix_multi]file_changes";

$total_num = mr2number(sql("select count(*) from $table_name where is_modified=1 $where"));
$data = mr2array(sql("select * from $table_name where is_modified=1 $where order by modified_date desc"));

if ($_GET['action'] == 'get_old_content' && $_GET['hash'] <> '')
{
	$old_content = '';
	foreach ($data as $record)
	{
		if ($record['hash'] == $_GET['hash'])
		{
			$old_content = $record['file_content'];
			break;
		}
	}
	header("Content-Type: text/plain; charset=utf8");
	echo $old_content;
	die;
}
if ($_GET['action'] == 'get_new_content' && $_GET['hash'] <> '')
{
	$new_content = '';
	foreach ($data as $record)
	{
		if ($record['hash'] == $_GET['hash'])
		{
			if (is_file("$config[project_path]/$record[path]"))
			{
				$new_content = @file_get_contents("$config[project_path]/$record[path]");
			}
			break;
		}
	}
	header("Content-Type: text/plain; charset=utf8");
	echo $new_content;
	die;
}

if ($_POST['action'] == 'approve')
{
	foreach ($data as $item)
	{
		$content = @file_get_contents("$config[project_path]/$item[path]");
		$new_hash = md5($content);
		sql_pr("update $config[tables_prefix_multi]file_changes set hash=?, file_content=?, is_modified=0 where path=?", $new_hash, $content, $item['path']);
	}
	return_ajax_success($page_name);
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['file_changes_header']);

$smarty->display("layout.tpl");
