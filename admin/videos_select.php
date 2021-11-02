<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_admin.php');
require_once('include/functions_base.php');
require_once('include/functions_screenshots.php');
require_once('include/functions.php');
require_once('include/check_access.php');
require_once("include/pclzip.lib.php");
require_once("include/database_selectors.php");

$errors = null;

// =====================================================================================================================
// select action
// =====================================================================================================================

if ($_POST['action'] == 'select_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	validate_field('empty', $_POST['selector'], $_POST['select_by'] == 'urls' ? $lang['videos']['select_field_list_urls'] : $lang['videos']['select_field_list_ids']);
	validate_field('empty', $_POST['operation'], $lang['videos']['select_field_operation']);

	$video_ids = array();
	$selector = explode("\n", $_POST['selector']);
	if ($_POST['select_by'] == 'ids')
	{
		foreach ($selector as $video_id)
		{
			$video_id = intval(trim($video_id));
			if ($video_id > 0)
			{
				$video_ids[] = $video_id;
			}
		}
	} elseif ($_POST['select_by'] == 'urls')
	{
		$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));

		foreach ($selector as $video_url)
		{
			$video_url = trim($video_url);
			if (is_url($video_url))
			{
				$search_id = 0;
				$search_dir = '';
				unset($temp);

				$pattern_check = str_replace('%ID%', '([0-9]+)', str_replace('%DIR%', '(.*)', str_replace('?', '\?', $website_ui_data['WEBSITE_LINK_PATTERN'])));
				if (preg_match("|$pattern_check|is", $video_url, $temp))
				{
					if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') !== false)
					{
						if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') === false)
						{
							$search_id = intval($temp[1]);
						} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') > strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%'))
						{
							$search_id = intval($temp[2]);
						} else {
							$search_id = intval($temp[1]);
						}
					} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') !== false)
					{
						$search_dir = trim($temp[1]);
					}
				}
				if ($search_id == 0)
				{
					$search_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where dir=?", $search_dir));
				}

				if ($search_id > 0)
				{
					$video_ids[] = $search_id;
				} else
				{
					$errors[] = get_aa_error('invalid_video_page_url', $video_url);
				}
			} elseif ($video_url)
			{
				$errors[] = get_aa_error('invalid_video_page_url', $video_url);
			}
		}
	}

	if (count($video_ids) == 0)
	{
		validate_field('empty', '', $_POST['select_by'] == 'urls' ? $lang['videos']['select_field_list_urls'] : $lang['videos']['select_field_list_ids']);
	}

	if ($_POST['operation'] == 'delete')
	{
		validate_field('empty', $_POST['confirm'], $lang['videos']['select_field_operation_confirm']);
	}

	if (!is_array($errors))
	{
		switch ($_POST['operation'])
		{
			case 'list':
				return_ajax_success("videos.php?no_filter=true&amp;se_ids=" . implode(',', $video_ids), 1);
				break;
			case 'mass_edit':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/mass-edit-$rnd.dat", serialize(array('all' => 0, 'ids' => $video_ids)));
				return_ajax_success("videos_mass_edit.php?edit_id=$rnd", 1);
				break;
			case 'mark_deleted':
				$rnd = mt_rand(10000000, 99999999);
				file_put_contents("$config[temporary_path]/delete-videos-$rnd.dat", serialize(array('ids' => $video_ids)));
				return_ajax_success("videos.php?action=mark_deleted&amp;delete_id=$rnd", 1);
				break;
			case 'delete':
				foreach ($video_ids as $video_id)
				{
					sql_pr("update $config[tables_prefix]videos set status_id=4 where video_id=?", $video_id);
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $video_id, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $video_id, serialize(array()), date("Y-m-d H:i:s"));
				}
				$_SESSION['messages'][] = $lang['common']['success_message_removed'];
				return_ajax_success("videos.php", 1);
				break;
		}
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// view
// =====================================================================================================================

$mass_select_id = intval($_REQUEST['select_id']);
if ($mass_select_id > 0)
{
	if (!is_file("$config[temporary_path]/mass-select-$mass_select_id.dat"))
	{
		header("Location: videos.php");
		die;
	}
	$_POST = @unserialize(file_get_contents("$config[temporary_path]/mass-select-$mass_select_id.dat"));
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_videos.tpl');
$smarty->assign('options', $options);

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['videos']['submenu_option_select_videos']);

$content_scheduler_days = intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days = '';
	$sorting_content_scheduler_days = 'desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option']) == 1)
	{
		$now_date=date("Y-m-d H:i:s");
		$where_content_scheduler_days=" and post_date>'$now_date'";
		$sorting_content_scheduler_days = 'asc';
	}
	$smarty->assign('list_updates', mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
