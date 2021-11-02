<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_servers.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$table_name = "$config[tables_prefix]admin_conversion_servers";
$table_key_name = "server_id";

$errors = null;

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = $_GET['se_text'];
	}
}

if ($_REQUEST['action'] == 'view_debug_log' && intval($_REQUEST['id']) > 0)
{
	$id = intval($_REQUEST['id']);
	$log_file = "debug_conversion_server_$id.txt";
	if (is_file("$config[project_path]/admin/logs/$log_file"))
	{
		header("Content-Type: text/plain; charset=utf8");
		$log_size = sprintf("%.0f", filesize("$config[project_path]/admin/logs/$log_file"));
		if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
		{
			$fh = fopen("$config[project_path]/admin/logs/$log_file", "r");
			fseek($fh, $log_size - 1024 * 1024);
			header("Content-Length: " . (1024 * 1024 + 29));
			echo "Showing last 1MB of file...\n\n";
			echo fread($fh, 1024 * 1024 + 1);
		} else
		{
			if (isset($_REQUEST['download']))
			{
				header("Content-Disposition: attachment; filename=\"$log_file\"");
			}
			header("Content-Length: $log_size");
			readfile("$config[project_path]/admin/logs/$log_file");
		}
	}
	die;
} elseif ($_REQUEST['action'] == 'view_conversion_log' && intval($_REQUEST['id']) > 0)
{
	$server_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_REQUEST['id'])));

	$rnd = mt_rand(1000000, 9999999);
	mkdir("$config[temporary_path]/$rnd", 0777);
	chmod("$config[temporary_path]/$rnd", 0777);
	get_file('log.txt', '/', "$config[temporary_path]/$rnd", $server_data);
	if (is_file("$config[temporary_path]/$rnd/log.txt"))
	{
		header("Content-Type: text/plain; charset=utf8");
		readfile("$config[temporary_path]/$rnd/log.txt");
	}
	die;
}

$options = get_options(array('SYSTEM_CONVERSION_API_VERSION'));
$latest_api_version = $options['SYSTEM_CONVERSION_API_VERSION'];

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$_POST['path'] = rtrim($_POST['path'], "/");

	$item_id = intval($_POST['item_id']);

	if ($_POST['action'] == 'change_complete' && $_POST['ftp_pass'] == '' && intval($_POST['connection_type_id']) == 2)
	{
		$_POST['ftp_pass'] = mr2string(sql("select ftp_pass from $table_name where $table_key_name=$item_id and connection_type_id=2"));
	}

	validate_field('uniq', $_POST['title'], $lang['settings']['conversion_server_field_title'], array('field_name_in_base' => 'title'));
	validate_field('file_separator', $_POST['title'], $lang['settings']['conversion_server_field_title']);
	validate_field('empty_int', $_POST['max_tasks'], $lang['settings']['conversion_server_field_max_tasks']);

	if (intval($_POST['connection_type_id']) == 0)
	{
		$_POST['option_pull_source_files'] = 0;
	}

	$connection_data_valid = 1;
	if (intval($_POST['connection_type_id']) == 0 || intval($_POST['connection_type_id']) == 1)
	{
		if (!validate_field('path', $_POST['path'], $lang['settings']['conversion_server_field_path']))
		{
			$connection_data_valid = 0;
		} elseif (!validate_field('file_separator', $_POST['path'], $lang['settings']['conversion_server_field_path']))
		{
			$connection_data_valid = 0;
		}
		$_POST['ftp_host'] = '';
		$_POST['ftp_port'] = '';
		$_POST['ftp_user'] = '';
		$_POST['ftp_pass'] = '';
		$_POST['ftp_timeout'] = '';
	} elseif (intval($_POST['connection_type_id']) == 2)
	{
		if (!validate_field('empty', $_POST['ftp_host'], $lang['settings']['conversion_server_field_ftp_host']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_port'], $lang['settings']['conversion_server_field_ftp_port']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_user'], $lang['settings']['conversion_server_field_ftp_user']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_pass'], $lang['settings']['conversion_server_field_ftp_password']))
		{
			$connection_data_valid = 0;
		}
		if (!validate_field('empty', $_POST['ftp_timeout'], $lang['settings']['conversion_server_field_ftp_timeout']))
		{
			$connection_data_valid = 0;
		}
		$_POST['path'] = '';
	}

	if ($connection_data_valid == 1)
	{
		$other_servers = mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $table_name"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['server_id'] != $item_id)
			{
				if ($other_server['connection_type_id'] == 0 || $other_server['connection_type_id'] == 1)
				{
					if ($other_server['path'] == $_POST['path'])
					{
						$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_path'], $other_server['title']);
						$connection_data_valid = 0;
						break;
					}
				} elseif ($other_server['connection_type_id'] == 2)
				{
					if ($other_server['ftp_host'] == $_POST['ftp_host'] && $other_server['ftp_user'] == $_POST['ftp_user'] && $other_server['ftp_folder'] == $_POST['ftp_folder'])
					{
						$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_ftp_folder'], $other_server['title']);
						$connection_data_valid = 0;
						break;
					}
				}
			}
		}
		$other_servers = mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $config[tables_prefix]admin_servers"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['connection_type_id'] == 0 || $other_server['connection_type_id'] == 1)
			{
				if ($other_server['path'] == $_POST['path'])
				{
					$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_path'], $other_server['title']);
					$connection_data_valid = 0;
					break;
				}
			} elseif ($other_server['connection_type_id'] == 2)
			{
				if ($other_server['ftp_host'] == $_POST['ftp_host'] && $other_server['ftp_user'] == $_POST['ftp_user'] && $other_server['ftp_folder'] == $_POST['ftp_folder'])
				{
					$errors[] = get_aa_error('server_duplicate_connection', $lang['settings']['server_field_ftp_folder'], $other_server['title']);
					$connection_data_valid = 0;
					break;
				}
			}
		}
	}

	if ($connection_data_valid == 1)
	{
		$test_result = test_connection_detailed($_POST);
		if ($test_result == 1)
		{
			$errors[] = get_aa_error('server_invalid_connection1', $_POST['ftp_host'], $_POST['ftp_port']);
			$connection_data_valid = 0;
		} elseif ($test_result == 2)
		{
			$errors[] = get_aa_error('server_invalid_connection2');
			$connection_data_valid = 0;
		} elseif ($test_result == 3)
		{
			$errors[] = get_aa_error('server_invalid_connection3');
			$connection_data_valid = 0;
		} elseif ($test_result == 4)
		{
			$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['server_field_connection_type']);
			$connection_data_valid = 0;
		} else
		{
			get_file('heartbeat.dat', '/', $config['temporary_path'], $_POST);
			$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
			if (is_array($heartbeat))
			{
				if (intval($_POST['option_storage_servers']) == 1)
				{
					if (!$heartbeat['ftp_supported'])
					{
						$errors[] = get_aa_error('server_no_ftp_extension', $lang['settings']['conversion_server_option_optimization']);
					}
				}
				if (intval($_POST['option_pull_source_files']) == 1)
				{
					if (!$heartbeat['curl_supported'])
					{
						$errors[] = get_aa_error('server_no_curl_extension', $lang['settings']['conversion_server_option_optimization']);
					}
				}
			}

			@unlink("$config[temporary_path]/heartbeat.dat");

			if ($_POST['action'] == 'add_new_complete')
			{
				if (!put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $_POST))
				{
					sleep(5);
					put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $_POST);
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$load = trim($heartbeat['la']);
		$total_space = trim($heartbeat['total_space']);
		$free_space = trim($heartbeat['free_space']);
		if (intval($heartbeat['time']) > 0)
		{
			$time = date("Y-m-d H:i:s", $heartbeat['time']);
		} else
		{
			$time = '0000-00-00 00:00:00';
		}
		$api_version = trim($heartbeat['api_version']);

		if (is_array($heartbeat['libraries']))
		{
			foreach ($heartbeat['libraries'] as $library)
			{
				if ($library['is_error'] == 1)
				{
					$error_id = 4;
					$error_iteration = 2;
					break;
				}
			}
		}

		if (isset($_POST['config']) && $connection_data_valid == 1)
		{
			if ($_POST['config'] == '')
			{
				delete_file('config.properties', "/", $_POST);
			} else
			{
				$rnd = mt_rand(1000000, 9999999);
				mkdir("$config[temporary_path]/$rnd", 0777);
				chmod("$config[temporary_path]/$rnd", 0777);

				delete_file('config.properties', "/", $_POST);

				file_put_contents("$config[temporary_path]/$rnd/config.properties", $_POST['config'], LOCK_EX);
				put_file('config.properties', "$config[temporary_path]/$rnd", "/", $_POST);

				@unlink("$config[temporary_path]/$rnd/config.properties");
				@rmdir("$config[temporary_path]/$rnd");
			}
		}

		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, status_id=2, max_tasks=?, process_priority=?, option_storage_servers=?, option_pull_source_files=?, connection_type_id=?, path=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=?, added_date=?",
				$_POST['title'], intval($_POST['max_tasks']), intval($_POST['process_priority']), intval($_POST['option_storage_servers']), intval($_POST['option_pull_source_files']), intval($_POST['connection_type_id']), $_POST['path'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), date("Y-m-d H:i:s")
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, status_id=?, max_tasks=?, process_priority=?, option_storage_servers=?, option_pull_source_files=?, connection_type_id=?, path=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, $table_name.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=? where $table_key_name=?",
				$_POST['title'], intval($_POST['status_id']), intval($_POST['max_tasks']), intval($_POST['process_priority']), intval($_POST['option_storage_servers']), intval($_POST['option_pull_source_files']), intval($_POST['connection_type_id']), $_POST['path'], $_POST['ftp_host'], $_POST['ftp_port'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_folder'], $_POST['ftp_timeout'], $load, $total_space, $free_space, $time, $api_version, intval($error_id), intval($error_iteration), $item_id
			);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select']) > 0)
{
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		sql("delete from $table_name where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		sql("update $table_name set status_id=1 where $table_key_name in ($row_select) and status_id=0");
		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where $table_key_name in ($row_select) and status_id=1");
		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
	} elseif ($_REQUEST['batch_action'] == 'enable_debug')
	{
		sql("update $table_name set is_logging_enabled=1 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_debug_enabled'];
	} elseif ($_REQUEST['batch_action'] == 'disable_debug')
	{
		sql("update $table_name set is_logging_enabled=0 where $table_key_name in ($row_select)");
		foreach ($_REQUEST['row_select'] as $server_id)
		{
			if (intval($server_id) > 0)
			{
				@unlink("$config[project_path]/admin/logs/debug_conversion_server_$server_id.txt");
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_debug_disabled'];
	} elseif ($_REQUEST['batch_action'] == 'update_api')
	{
		if ($latest_api_version != '')
		{
			$servers = mr2array(sql("select * from $table_name where $table_key_name in ($row_select)"));
			foreach ($servers as $server)
			{
				if ($server['api_version'] != $latest_api_version)
				{
					if (is_writable("$config[temporary_path]"))
					{
						$rnd = mt_rand(1000000, 9999999);
						mkdir("$config[temporary_path]/$rnd", 0777);
						chmod("$config[temporary_path]/$rnd", 0777);

						$new_filename = '';
						if (get_file('remote_cron.php', '/', "$config[temporary_path]/$rnd", $server))
						{
							$new_filename = "remote_cron_" . date("YmdHis") . ".php";
							rename("$config[temporary_path]/$rnd/remote_cron.php", "$config[temporary_path]/$rnd/$new_filename");
						}
						if (!$new_filename || put_file($new_filename, "$config[temporary_path]/$rnd", '/', $server))
						{
							delete_file('remote_cron.php', '/', $server);
							if (put_file('remote_cron.php', "$config[project_path]/admin/tools", '/', $server))
							{
								sql_pr("update $table_name set api_version=? where $table_key_name=?", $latest_api_version, $server[$table_key_name]);
								sql_pr("update $table_name set error_id=0, error_iteration=0 where $table_key_name=? and error_id=5", $server[$table_key_name]);
							}
						}
						@unlink("$config[temporary_path]/$rnd/$new_filename");
						@rmdir("$config[temporary_path]/$rnd");
					}
				}
			}
			$_SESSION['messages'][] = $lang['settings']['success_message_api_updated'];
		}
	}
	return_ajax_success($page_name);
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['error_iteration'] > 1)
	{
		if ($_POST['error_id'] == 1)
		{
			$_POST['errors'][] = $lang['settings']['dg_conversion_servers_error_write'];
		} elseif ($_POST['error_id'] == 2)
		{
			$_POST['errors'][] = $lang['settings']['dg_conversion_servers_error_heartbeat'];
		} elseif ($_POST['error_id'] == 3)
		{
			$_POST['errors'][] = $lang['settings']['dg_conversion_servers_error_heartbeat2'];
		} elseif ($_POST['error_id'] == 4)
		{
			get_file('heartbeat.dat', '/', $config['temporary_path'], $_POST);
			$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
			if (!is_array($heartbeat))
			{
				$_POST['errors'][] = get_aa_error('conversion_server_cron_not_working');
			} else
			{
				$_POST['errors'][] = get_aa_error('conversion_server_library_path_invalid');
			}
		} elseif ($_POST['error_id'] == 5)
		{
			$_POST['errors'][] = $lang['settings']['dg_conversion_servers_error_api_version'];
		} elseif ($_POST['error_id'] == 6)
		{
			$_POST['errors'][] = $lang['settings']['dg_conversion_servers_error_locked_too_long'];
		}
	}

	$rnd = mt_rand(1000000, 9999999);
	mkdir("$config[temporary_path]/$rnd", 0777);
	chmod("$config[temporary_path]/$rnd", 0777);

	get_file('log.txt', '/', "$config[temporary_path]/$rnd", $_POST);
	get_file('config.properties', '/', "$config[temporary_path]/$rnd", $_POST);
	get_file('heartbeat.dat', '/', "$config[temporary_path]/$rnd", $_POST);

	$_POST['log'] = @file_get_contents("$config[temporary_path]/$rnd/log.txt");
	$_POST['config'] = @file_get_contents("$config[temporary_path]/$rnd/config.properties");

	$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/$rnd/heartbeat.dat"));
	if (is_array($heartbeat['libraries']))
	{
		$_POST['libraries'] = $heartbeat['libraries'];
	}

	@unlink("$config[temporary_path]/$rnd/log.txt");
	@unlink("$config[temporary_path]/$rnd/config.properties");
	@unlink("$config[temporary_path]/$rnd/heartbeat.dat");
	@rmdir("$config[temporary_path]/$rnd");
}

if ($_GET['action'] == 'add_new')
{
	$_POST['max_tasks'] = '5';
	$_POST['option_storage_servers'] = '1';
	$_POST['option_pull_source_files'] = '0';
}

$where = '';
if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (title like '%$q%') ";
}
if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$data = mr2array(sql("select *, (select count(*) from $config[tables_prefix]background_tasks where status_id in (0,1) and server_id=$table_name.server_id) as tasks_amount from $table_name $where order by title asc"));

foreach ($data as $k => $v)
{
	$data[$k]['free_space_string'] = sizeToHumanString($v['free_space'], 2);
	if ($v['total_space'] > 0)
	{
		$data[$k]['free_space_percent'] = round(($v['free_space'] / $v['total_space']) * 100, 2);
	} else
	{
		$data[$k]['free_space_percent'] = 0;
	}
	if (is_file("$config[project_path]/admin/logs/debug_conversion_server_$v[server_id].txt"))
	{
		$data[$k]['has_debug_log'] = 1;
	}
	if ($latest_api_version != '' && intval(str_replace('.', '', $data[$k]['api_version'])) < intval(str_replace('.', '', $latest_api_version)))
	{
		$data[$k]['has_old_api'] = 1;
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_options.tpl');
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('total_num', count($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['conversion_server_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['conversion_server_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_conversion_servers_list']);
}

$smarty->display("layout.tpl");
