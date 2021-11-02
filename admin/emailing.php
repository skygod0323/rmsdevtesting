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

if ($_POST['action'] == 'start')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}
	settype($_POST['user_status_ids'], "array");

	if ($_POST['send_to'] == '1' || $_POST['send_to'] == '2')
	{
		validate_field('empty', $_POST['subject'], $lang['users']['emailing_field_subject']);
	}

	if ($_POST['send_to'] != '4')
	{
		validate_field('empty', $_POST['body'], $lang['users']['emailing_field_body']);
	}

	if ($_POST['send_to'] == '1' || $_POST['send_to'] == '2')
	{
		validate_field('empty', $_POST['headers'], $lang['users']['emailing_field_headers']);
	}
	if (($_POST['send_to'] == '2' || $_POST['send_to'] == '3') && ($_POST['delay'] <> '0'))
	{
		validate_field('empty_int', $_POST['delay'], $lang['users']['emailing_field_delay']);
	}
	if ($_POST['send_to'] == '1')
	{
		validate_field('email', $_POST['test_email'], $lang['users']['emailing_field_test_mailbox']);
	}
	if ($_POST['send_to'] == '3')
	{
		if (validate_field('empty', $_POST['user_from'], $lang['users']['emailing_field_sender']))
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $_POST['user_from'])) == 0)
			{
				$errors[] = get_aa_error('invalid_user', $lang['users']['emailing_field_sender']);
			}
		}
	}
	if (($_POST['send_to'] == '2' || $_POST['send_to'] == '3') && (count($_POST['user_status_ids']) == 0))
	{
		$errors[] = get_aa_error('required_field', $lang['users']['emailing_field_receivers']);
	}

	if (!is_array($errors))
	{
		$item_id = intval($_POST['item_id']);

		$_SESSION['save'][$page_name]['from_email'] = $_POST['from_email'];
		$_SESSION['save'][$page_name]['test_email'] = $_POST['test_email'];
		$_SESSION['save'][$page_name]['user_from'] = $_POST['user_from'];
		$_SESSION['save'][$page_name]['delay'] = $_POST['delay'];
		$_SESSION['save'][$page_name]['headers'] = $_POST['headers'];

		if ($_POST['send_to'] == 4)
		{
			$status_ids = implode(',', array_map('intval', $_POST['user_status_ids']));
			$emails = array();
			if (strlen($status_ids) > 0)
			{
				$emails = mr2array_list(sql("select email from $config[tables_prefix]users where status_id in ($status_ids)"));
			}

			$date_string = date("Y-m-d_H-i");
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename=\"emails_{$date_string}.txt\"");

			$rnd = mt_rand(10000000, 99999999);
			$export_filename = "$config[temporary_path]/emailing-$rnd.dat";

			foreach ($emails as $email)
			{
				file_put_contents($export_filename, "$email\n", FILE_APPEND);
			}
			return_ajax_success("$page_name?action=export_as_file&amp;emailing_id=$rnd");
			die;
		} else
		{
			$rnd = mt_rand(10000000, 99999999);

			$fp = fopen("$config[temporary_path]/emailing-$rnd.txt", "w");
			fwrite($fp, serialize($_POST));
			fclose($fp);

			exec("$config[php_path] $config[project_path]/admin/background_emailing.php $rnd > /dev/null &");

			if ($_POST['send_to'] == 1)
			{
				$_SESSION['messages'][] = $lang['users']['success_message_test_email_sent'];
			} else
			{
				$_SESSION['messages'][] = $lang['users']['success_message_emailing_started'];
			}
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if ($_GET['action'] == 'export_as_file')
{
	$filename = "emails_" . date("Y-m-d_H-i") . ".txt";
	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=\"$filename\"");

	$emailing_id = intval($_GET['emailing_id']);
	if ($emailing_id > 0 && is_file("$config[temporary_path]/emailing-$emailing_id.dat"))
	{
		readfile("$config[temporary_path]/emailing-$emailing_id.dat");
		unlink("$config[temporary_path]/emailing-$emailing_id.dat");
	}
	die;
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['users']['submenu_option_create_emailing']);

$smarty->display("layout.tpl");
