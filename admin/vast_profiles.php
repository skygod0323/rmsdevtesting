<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$table_fields = array();
$table_fields[] = array('id' => 'url',       'title' => $lang['settings']['vast_profile_field_vast_url'],  'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'countries', 'title' => $lang['settings']['vast_profile_field_countries'], 'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'referers',  'title' => $lang['settings']['vast_profile_field_referers'],  'is_default' => 1, 'type' => 'list');
$table_fields[] = array('id' => 'weight',    'title' => $lang['settings']['vast_profile_field_weight'],    'is_default' => 1, 'type' => 'number');

$sort_def_field = "title";
$sort_def_direction = "desc";
$sort_array = array();
foreach ($table_fields as $k => $field)
{
	if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
	{
		if (in_array($field['id'], $_GET['grid_columns']))
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name]['grid_columns'][$field['id']]);
	} else
	{
		$table_fields[$k]['is_enabled'] = intval($field['is_default']);
	}
	if ($field['type'] == 'id')
	{
		$table_fields[$k]['is_enabled'] = 1;
	}
}
if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
{
	$_SESSION['save'][$page_name]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name]['grid_columns_order']))
{
	$temp_table_fields = array();
	foreach ($table_fields as $table_field)
	{
		if ($table_field['type'] == 'id')
		{
			$temp_table_fields[] = $table_field;
			break;
		}
	}
	foreach ($_SESSION['save'][$page_name]['grid_columns_order'] as $table_field_id)
	{
		foreach ($table_fields as $table_field)
		{
			if ($table_field['id'] == $table_field_id)
			{
				$temp_table_fields[] = $table_field;
				break;
			}
		}
	}
	foreach ($table_fields as $table_field)
	{
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_key_name = 'profile_id';
$limit_providers = 10;
$profiles = get_vast_profiles();

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
}

// =====================================================================================================================
// view log
// =====================================================================================================================

if ($_REQUEST['action'] == 'view_debug_log' && $_REQUEST['id'] != '')
{
	foreach ($profiles as $profile)
	{
		if ($_REQUEST['id'] == $profile[$table_key_name])
		{
			$log_file = "debug_vast_profile_$profile[$table_key_name].txt";
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
		}
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], ['add_new_complete', 'change_complete']))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (validate_field('empty', $_POST['title'], $lang['settings']['vast_profile_field_title']))
	{
		foreach ($profiles as $profile)
		{
			if (mb_lowercase($_POST['title']) == mb_lowercase($profile['title']) && $_POST['item_id'] != $profile[$table_key_name])
			{
				$errors[] = get_aa_error('unique_field', $lang['settings']['vast_profile_field_title']);
				break;
			}
		}
	}
	for ($i = 0; $i < $limit_providers; $i++)
	{
		if (isset($_POST["is_provider_{$i}"]))
		{
			validate_field('empty', $_POST["provider_{$i}_url"], str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_vast_url']);
			if ($_POST["provider_{$i}_weight"] != '' && $_POST["provider_{$i}_weight"] != '0')
			{
				validate_field('empty_int', $_POST["provider_{$i}_weight"], str_replace("%1%", $i + 1, $lang['settings']['vast_profile_divider_advertiser']) . " - " . $lang['settings']['vast_profile_field_weight']);
			}
		}
	}

	mkdir_recursive("$config[project_path]/admin/data/player/vast");

	$item_id = intval($_POST['item_id']);
	if ($_POST['action'] == 'add_new_complete')
	{
		$item_id = mt_rand(1, 1000000);
		for ($i = 0; $i < 99999; $i++)
		{
			if (isset($profiles[$item_id]))
			{
				$item_id = mt_rand(1, 1000000);
			}
		}
	}

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$item_id.dat";
	if ($_POST['action'] == 'add_new_complete')
	{
		if (!is_writable(dirname($profile_data_file)))
		{
			$errors[] = get_aa_error('filesystem_permission_write', dirname($profile_data_file));
		}
	} else
	{
		if (!is_writable($profile_data_file))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $profile_data_file);
		}
	}

	if (!is_array($errors))
	{
		$profile_info = [$table_key_name => $item_id, 'title' => $_POST['title'], 'is_debug_enabled' => intval($_POST['is_debug_enabled']), 'providers' => []];
		for ($i = 0; $i < $limit_providers; $i++)
		{
			$profile_info['providers'][] = [
				'is_enabled' => intval($_POST["is_provider_{$i}"]),
				'url' => trim($_POST["provider_{$i}_url"]),
				'alt_url' => trim($_POST["provider_{$i}_alt_url"]),
				'countries' => trim(@implode(',', $_POST["provider_{$i}_countries"])),
				'exclude_countries' => trim(@implode(',', $_POST["provider_{$i}_exclude_countries"])),
				'referers' => trim($_POST["provider_{$i}_referers"]),
				'exclude_referers' => trim($_POST["provider_{$i}_exclude_referers"]),
				'weight' => intval($_POST["provider_{$i}_weight"])
			];
		}
		if (intval($_POST['is_debug_enabled']) == 0)
		{
			@unlink("$config[project_path]/admin/logs/debug_vast_profile_$item_id.txt");
		}

		file_put_contents($profile_data_file, serialize($profile_info), LOCK_EX);
		if ($_POST['action'] == 'add_new_complete')
		{
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// usages
// =====================================================================================================================

$profiles_usages = [];

$player_files = get_player_data_files();
foreach ($player_files as $player_file)
{
	if (is_file($player_file['file']))
	{
		$player_data = @unserialize(file_get_contents($player_file['file']));
		foreach ($profiles as $profile)
		{
			if ($player_data['pre_roll_vast_provider'] == "vast_profile_$profile[$table_key_name]")
			{
				$profiles_usages[$profile[$table_key_name]][] = ['type' => 'pre', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
			}
			if ($player_data['post_roll_vast_provider'] == "vast_profile_$profile[$table_key_name]")
			{
				$profiles_usages[$profile[$table_key_name]][] = ['type' => 'post', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed'])];
			}
		}
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select']) > 0)
{
	if ($_REQUEST['batch_action'] == 'delete')
	{
		foreach ($_REQUEST['row_select'] as $profile_id)
		{
			if (isset($profiles[$profile_id]) && !isset($profiles_usages[$profile_id]))
			{
				unlink("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat");
				@unlink("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt");
				unset($profiles[$profile_id]);
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action']=='enable_debug')
	{
		foreach ($_REQUEST['row_select'] as $profile_id)
		{
			if (isset($profiles[$profile_id]))
			{
				$profiles[$profile_id]['is_debug_enabled'] = 1;
				file_put_contents("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat", serialize($profiles[$profile_id]), LOCK_EX);
			}
		}
		$_SESSION['messages'][]=$lang['common']['success_message_debug_enabled'];
	} elseif ($_REQUEST['batch_action']=='disable_debug')
	{
		foreach ($_REQUEST['row_select'] as $profile_id)
		{
			if (isset($profiles[$profile_id]))
			{
				unset($profiles[$profile_id]['is_debug_enabled']);
				file_put_contents("$config[project_path]/admin/data/player/vast/vast_$profile_id.dat", serialize($profiles[$profile_id]), LOCK_EX);
				@unlink("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt");
			}
		}
		$_SESSION['messages'][]=$lang['common']['success_message_debug_disabled'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);

	$_POST = $profiles[$item_id];
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$_POST['usages'] = $profiles_usages[$item_id];

	foreach ($_POST['providers'] as $provider_id => $provider)
	{
		if (strlen($provider['countries']) == 0)
		{
			$_POST['providers'][$provider_id]['countries'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['countries'] = explode(',', $provider['countries']);
		}
		if (strlen($provider['exclude_countries']) == 0)
		{
			$_POST['providers'][$provider_id]['exclude_countries'] = [];
		} else
		{
			$_POST['providers'][$provider_id]['exclude_countries'] = explode(',', $provider['exclude_countries']);
		}
	}

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$item_id.dat";
	if (!unserialize(file_get_contents($profile_data_file)))
	{
		$_POST['errors'][] = get_aa_error('player_vast_profile_format', $profile_data_file);
	}
	if (!is_writable($profile_data_file))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $profile_data_file);
	}
	if ($_POST['is_debug_enabled'] == 1)
	{
		$_POST['errors'][] = $lang['settings']['vast_profile_warning_debug_enabled'];
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data = $profiles;
if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	foreach ($data as $profile_id => $profile)
	{
		if (!mb_contains($profile['title'], $_SESSION['save'][$page_name]['se_text']))
		{
			foreach ($profile['providers'] as $provider_id => $provider)
			{
				if (!mb_contains($provider['url'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($provider['alt_url'], $_SESSION['save'][$page_name]['se_text'])
						&& !mb_contains($provider['referers'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($provider['exclude_referers'], $_SESSION['save'][$page_name]['se_text']))
				{
					unset($data[$profile_id]['providers'][$provider_id]);
				}
			}
			if (count($data[$profile_id]['providers']) == 0)
			{
				unset($data[$profile_id]);
			}
		}
	}
}

foreach ($data as $profile_id => $profile)
{
	foreach ($profile['providers'] as $provider_id => $provider)
	{
		if (intval($provider['is_enabled']) == 1)
		{
			$provider_countries = [];
			$provider['countries'] = array_map('trim', explode(',', $provider['countries']));
			$provider['exclude_countries'] = array_map('trim', explode(',', $provider['exclude_countries']));
			foreach ($provider['countries'] as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$provider_countries[] = ['title' => "+$list_countries[$country_code]"];
				}
			}
			foreach ($provider['exclude_countries'] as $country_code)
			{
				if ($country_code && isset($list_countries[$country_code]))
				{
					$provider_countries[] = ['title' => "-$list_countries[$country_code]"];
				}
			}
			if (count($provider_countries) == 0)
			{
				$provider_countries[] = ['title' => $lang['settings']['vast_profile_field_countries_all']];
			}
			$data[$profile_id]['providers'][$provider_id]['countries'] = $provider_countries;

			$provider_referers = [];
			$provider['referers'] = array_map('trim', explode("\n", $provider['referers']));
			$provider['exclude_referers'] = array_map('trim', explode("\n", $provider['exclude_referers']));
			foreach ($provider['referers'] as $referer)
			{
				if ($referer)
				{
					$provider_referers[] = ['title' => "+$referer"];
				}
			}
			foreach ($provider['exclude_referers'] as $referer)
			{
				if ($referer)
				{
					$provider_referers[] = ['title' => "-$referer"];
				}
			}
			if (count($provider_referers) == 0)
			{
				$provider_referers[] = ['title' => $lang['settings']['vast_profile_field_referers_all']];
			}
			$data[$profile_id]['providers'][$provider_id]['referers'] = $provider_referers;
		} else
		{
			unset($data[$profile_id]['providers'][$provider_id]);
		}
	}

	$data[$profile_id]['usages'] = $profiles_usages[$profile_id];

	$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$profile_id.dat";
	if (!unserialize(file_get_contents($profile_data_file)))
	{
		$data[$profile_id]['has_errors'] = 1;
	} elseif (!is_writable($profile_data_file))
	{
		$data[$profile_id]['has_warnings'] = 1;
	}
}

$total_num = count($data);

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_options.tpl');
$smarty->assign('limit_providers', $limit_providers);
$smarty->assign('list_countries', $list_countries);

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['vast_profile_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['vast_profile_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_vast_profiles_list']);
}

$smarty->display("layout.tpl");
