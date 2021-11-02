<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$errors = null;

if ($_POST['action'] == 'cleanup_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (intval($_POST["to_date_Year"]) > 0 || intval($_POST["to_date_Month"]) > 0 || intval($_POST["to_date_Day"]) > 0)
	{
		if (intval($_POST["to_date_Year"]) == 0 || intval($_POST["to_date_Month"]) == 0 || intval($_POST["to_date_Day"]) == 0)
		{
			$errors[] = get_aa_error('required_field', $lang['stats']['cleanup_field_to_date']);
		}
	} else
	{
		$errors[] = get_aa_error('required_field', $lang['stats']['cleanup_field_to_date']);
	}
	if (intval($_POST['traffic']) + intval($_POST['videos']) + intval($_POST['albums']) + intval($_POST['player']) + intval($_POST['search']) + intval($_POST['embed']) + intval($_POST['overload']) == 0)
	{
		$errors[] = get_aa_error('stats_nothing_to_delete');
	}

	if (!is_array($errors))
	{
		$to_date = intval($_POST["to_date_Year"]) . "-" . intval($_POST["to_date_Month"]) . "-" . intval($_POST["to_date_Day"]);
		$where = "where added_date<='$to_date'";

		$_SESSION['save']['stats_cleanup']['traffic'] = intval($_POST['traffic']);
		$_SESSION['save']['stats_cleanup']['embed'] = intval($_POST['embed']);
		$_SESSION['save']['stats_cleanup']['videos'] = intval($_POST['videos']);
		$_SESSION['save']['stats_cleanup']['albums'] = intval($_POST['albums']);
		$_SESSION['save']['stats_cleanup']['player'] = intval($_POST['player']);
		$_SESSION['save']['stats_cleanup']['search'] = intval($_POST['search']);
		$_SESSION['save']['stats_cleanup']['overload'] = intval($_POST['overload']);

		if (intval($_POST['traffic']) > 0)
		{
			sql("delete from $config[tables_prefix_multi]stats_in $where");
			sql("delete from $config[tables_prefix_multi]stats_cs_out $where");
			sql("delete from $config[tables_prefix_multi]stats_adv_out $where");
		}
		if (intval($_POST['embed']) > 0)
		{
			sql("delete from $config[tables_prefix_multi]stats_embed $where");
		}
		if (intval($_POST['videos']) > 0)
		{
			sql("delete from $config[tables_prefix]stats_videos $where");
		}
		if (intval($_POST['albums']) > 0)
		{
			sql("delete from $config[tables_prefix]stats_albums $where");
		}
		if (intval($_POST['player']) > 0)
		{
			sql("delete from $config[tables_prefix]stats_player $where");
		}
		if (intval($_POST['search']) > 0)
		{
			sql("delete from $config[tables_prefix_multi]stats_search $where and is_manual=0");
		}
		if (intval($_POST['overload']) > 0)
		{
			sql("delete from $config[tables_prefix_multi]stats_overload_protection $where");
		}

		$_SESSION['messages'][] = $lang['stats']['success_message_stats_cleanup'];
		return_ajax_success("$page_name");
	} else
	{
		return_ajax_errors($errors);
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_cleanup']);

$smarty->display("layout.tpl");