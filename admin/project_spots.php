<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/setup_smarty_site.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

if (!is_file("$config[project_path]/admin/data/advertisements/advertisements.lock"))
{
	file_put_contents("$config[project_path]/admin/data/advertisements/advertisements.lock", "1", LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/advertisements/advertisements.lock","r+");
flock($lock, LOCK_EX);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$list_is_active_values = array(
	0 => $lang['website_ui']['advertisement_field_status_disabled'],
	1 => $lang['website_ui']['advertisement_field_status_active'],
);

$list_devices_values = array(
	'pc' => $lang['website_ui']['advertisement_field_devices_pc'],
	'phone' => $lang['website_ui']['advertisement_field_devices_phone'],
	'tablet' => $lang['website_ui']['advertisement_field_devices_tablet'],
);

$list_browsers_values = array(
	'chrome' => $lang['website_ui']['advertisement_field_browsers_chrome'],
	'firefox' => $lang['website_ui']['advertisement_field_browsers_firefox'],
	'safari' => $lang['website_ui']['advertisement_field_browsers_safari'],
	'msie' => $lang['website_ui']['advertisement_field_browsers_msie'],
	'opera' => $lang['website_ui']['advertisement_field_browsers_opera'],
	'yandex' => $lang['website_ui']['advertisement_field_browsers_yandex'],
	'uc' => $lang['website_ui']['advertisement_field_browsers_uc'],
	'other' => $lang['website_ui']['advertisement_field_browsers_other'],
);

$list_users_values = array(
	'guest' => $lang['website_ui']['advertisement_field_users_guest'],
	'active' => $lang['website_ui']['advertisement_field_users_active'],
	'premium' => $lang['website_ui']['advertisement_field_users_premium'],
	'webmaster' => $lang['website_ui']['advertisement_field_users_webmaster'],
);

$table_fields = array();
$table_fields[] = array('id' => 'title',              'title' => $lang['website_ui']['advertisement_field_title'],       'is_default' => 1, 'type' => 'text', 'link' => 'project_spots.php?action=change&item_id=%id%', 'link_id' => 'advertisement_id', 'link_is_editor' => 1);
$table_fields[] = array('id' => 'is_active',          'title' => $lang['website_ui']['advertisement_field_status'],      'is_default' => 1, 'type' => 'choice', 'values' => $list_is_active_values);
$table_fields[] = array('id' => 'devices',            'title' => $lang['website_ui']['advertisement_field_devices'],     'is_default' => 1, 'type' => 'multi_choice', 'values' => $list_devices_values, 'value_all' => $lang['website_ui']['advertisement_field_devices_all']);
$table_fields[] = array('id' => 'browsers',           'title' => $lang['website_ui']['advertisement_field_browsers'],    'is_default' => 1, 'type' => 'multi_choice', 'values' => $list_browsers_values, 'value_all' => $lang['website_ui']['advertisement_field_browsers_all']);
$table_fields[] = array('id' => 'users',              'title' => $lang['website_ui']['advertisement_field_users'],       'is_default' => 1, 'type' => 'multi_choice', 'values' => $list_users_values, 'value_all' => $lang['website_ui']['advertisement_field_users_all']);
$table_fields[] = array('id' => 'categories',         'title' => $lang['website_ui']['advertisement_field_categories'],  'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'exclude_categories', 'title' => $lang['website_ui']['advertisement_field_categories2'], 'is_default' => 0, 'type' => 'list', 'link' => 'categories.php?action=change&item_id=%id%', 'permission' => 'categories|view');
$table_fields[] = array('id' => 'countries',          'title' => $lang['website_ui']['advertisement_field_countries'],   'is_default' => 0, 'type' => 'list');
$table_fields[] = array('id' => 'show_date',          'title' => $lang['website_ui']['advertisement_field_show_date'],   'is_default' => 1, 'type' => 'date_range', 'min_date_label' => $lang['common']['undefined']);
$table_fields[] = array('id' => 'show_time',          'title' => $lang['website_ui']['advertisement_field_show_time'],   'is_default' => 1, 'type' => 'time_range');

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

$spots = get_site_spots();
$spots_usages = array();
$ads = array();
foreach ($spots as $spot)
{
	foreach ($spot['ads'] as $spot_ad)
	{
		$ads[$spot_ad['advertisement_id']] = $spot_ad;
	}
}

$table_key_name = "advertisement_id";

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_status'] = '';
	$_SESSION['save'][$page_name]['se_device'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status']))
	{
		$_SESSION['save'][$page_name]['se_status'] = trim($_GET['se_status']);
	}
	if (isset($_GET['se_device']))
	{
		$_SESSION['save'][$page_name]['se_device'] = trim($_GET['se_device']);
	}
}

// =====================================================================================================================
// view log
// =====================================================================================================================

if ($_REQUEST['action'] == 'view_debug_log' && $_REQUEST['id'] != '')
{
	foreach ($spots as $spot)
	{
		if ($_REQUEST['id'] == $spot['external_id'])
		{
			$log_file = "debug_ad_spot_$spot[external_id].txt";
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

if (in_array($_POST['action'], array('add_new_spot_complete', 'change_spot_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if ($_POST['action'] == 'change_spot_complete')
	{
		$_POST['external_id'] = $_POST['item_id'];
	}

	if (validate_field('empty', $_POST['title'], $lang['website_ui']['spot_field_title']))
	{
		foreach ($spots as $spot)
		{
			if (mb_lowercase($_POST['title']) == mb_lowercase($spot['title']) && $_POST['external_id'] != $spot['external_id'])
			{
				$errors[] = get_aa_error('unique_field', $lang['website_ui']['spot_field_title']);
				break;
			}
		}
	}
	if (validate_field('empty', $_POST['external_id'], $lang['website_ui']['spot_field_id']))
	{
		if (validate_field('external_id', $_POST['external_id'], $lang['website_ui']['spot_field_id']))
		{
			if ($_POST['action'] == 'add_new_spot_complete')
			{
				if (preg_match("|^[0-9]+$|is", $_POST['external_id']))
				{
					$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['spot_field_id']);
				} else
				{
					foreach ($spots as $spot)
					{
						if (mb_lowercase($_POST['external_id']) == mb_lowercase($spot['external_id']))
						{
							$errors[] = get_aa_error('unique_field', $lang['website_ui']['spot_field_id']);
							break;
						}
					}
				}
			}
		}
	}
	if (strlen($_POST['template']) != 0)
	{
		if (strpos($_POST['template'], '%ADV%') === false)
		{
			$errors[] = get_aa_error('token_required', $lang['website_ui']['spot_field_template_code'], '%ADV%');
		}
	}

	$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$_POST[external_id].dat";
	if ($_POST['action'] == 'add_new_spot_complete')
	{
		if (!is_writable(dirname($spot_data_file)))
		{
			$errors[] = get_aa_error('filesystem_permission_write', dirname($spot_data_file));
		}
	} else
	{
		if (!is_writable($spot_data_file))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $spot_data_file);
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_spot_complete')
		{
			$spot_info = array(
				'title' => $_POST['title'],
				'external_id' => $_POST['external_id'],
				'template' => $_POST['template'],
				'ads' => array()
			);
			file_put_contents($spot_data_file, serialize($spot_info), LOCK_EX);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$spot_info = $spots[$_POST['external_id']];
			$spot_info['title'] = $_POST['title'];
			$spot_info['external_id'] = $_POST['external_id'];
			$spot_info['template'] = $_POST['template'];
			file_put_contents($spot_data_file, serialize($spot_info), LOCK_EX);

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (validate_field('empty', $_POST['title'], $lang['website_ui']['advertisement_field_title']))
	{
		foreach ($ads as $ad)
		{
			if (mb_lowercase($_POST['title']) == mb_lowercase($ad['title']) && $_POST['item_id'] != $ad['advertisement_id'])
			{
				$errors[] = get_aa_error('unique_field', $lang['website_ui']['advertisement_field_title']);
				break;
			}
		}
	}

	validate_field('empty', $_POST['spot_id'], $lang['website_ui']['advertisement_field_spot']);

	validate_field('empty', $_POST['code'], $lang['website_ui']['advertisement_field_html_code']);

	if (strlen($_POST['url']) != 0)
	{
		if (strpos($_POST['code'], '%URL%') === false)
		{
			$errors[] = get_aa_error('token_required', $lang['website_ui']['advertisement_field_html_code'], '%URL%');
		}
	} else
	{
		if (strpos($_POST['code'], '%URL%') !== false)
		{
			validate_field('empty', $_POST['url'], $lang['website_ui']['advertisement_field_url']);
		}
	}

	$check_interval = 1;
	if (intval($_POST['show_from_date_Year']) > 0 && intval($_POST['show_from_date_Month']) > 0 && intval($_POST['show_from_date_Day']) > 0)
	{
		$show_from_date = intval($_POST['show_from_date_Year']) . "-" . intval($_POST['show_from_date_Month']) . "-" . intval($_POST['show_from_date_Day']);
		if ($_POST['show_from_date_time'] != '00:00' && $_POST['show_from_date_time'] != '')
		{
			$has_time_error = false;
			$show_from_date_time = array(0, 0);
			if (strpos($_POST['show_from_date_time'], ":") !== false)
			{
				$temp = explode(":", $_POST['show_from_date_time']);
				if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
				{
					$show_from_date_time[0] = $temp[0];
				} else
				{
					$has_time_error = true;
				}
				if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
				{
					$show_from_date_time[1] = $temp[1];
				} else
				{
					$has_time_error = true;
				}
			} else
			{
				$has_time_error = true;
			}
			if ($has_time_error)
			{
				$errors[] = get_aa_error('invalid_time', $lang['website_ui']['advertisement_field_show_date']);
				$check_interval = 0;
			} else
			{
				$show_from_date .= " $show_from_date_time[0]:$show_from_date_time[1]";
			}
		}
	} else
	{
		$show_from_date = "0000-00-00";
		$check_interval = 0;
	}
	if (intval($_POST['show_to_date_Year']) > 0 && intval($_POST['show_to_date_Month']) > 0 && intval($_POST['show_to_date_Day']) > 0)
	{
		$show_to_date = intval($_POST['show_to_date_Year']) . "-" . intval($_POST['show_to_date_Month']) . "-" . intval($_POST['show_to_date_Day']);
		if ($_POST['show_to_date_time'] != '00:00' && $_POST['show_to_date_time'] != '')
		{
			$has_time_error = false;
			$show_to_date_time = array(0, 0);
			if (strpos($_POST['show_to_date_time'], ":") !== false)
			{
				$temp = explode(":", $_POST['show_to_date_time']);
				if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
				{
					$show_to_date_time[0] = $temp[0];
				} else
				{
					$has_time_error = true;
				}
				if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
				{
					$show_to_date_time[1] = $temp[1];
				} else
				{
					$has_time_error = true;
				}
			} else
			{
				$has_time_error = true;
			}
			if ($has_time_error)
			{
				$errors[] = get_aa_error('invalid_time', $lang['website_ui']['advertisement_field_show_date']);
				$check_interval = 0;
			} else
			{
				$show_to_date .= " $show_to_date_time[0]:$show_to_date_time[1]";
			}
		}
	} else
	{
		$show_to_date = "0000-00-00";
		$check_interval = 0;
	}
	if ($check_interval == 1 && strtotime($show_from_date) >= strtotime($show_to_date))
	{
		$errors[] = get_aa_error('invalid_date_range', $lang['website_ui']['advertisement_field_show_date']);
	}

	$show_from_time = 0;
	$show_to_time = 0;
	if (($_POST['show_from_time'] != '00:00' && $_POST['show_from_time'] != '') || ($_POST['show_to_time'] != '00:00' && $_POST['show_to_time'] != ''))
	{
		$has_time_error = false;
		$show_from_time = array(0, 0);
		if (strpos($_POST['show_from_time'], ":") !== false)
		{
			$temp = explode(":", $_POST['show_from_time']);
			if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
			{
				$show_from_time[0] = $temp[0];
			} else
			{
				$has_time_error = true;
			}
			if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
			{
				$show_from_time[1] = $temp[1];
			} else
			{
				$has_time_error = true;
			}
		} else
		{
			$has_time_error = true;
		}
		$show_from_time = $show_from_time[0] * 3600 + $show_from_time[1] * 60;

		$show_to_time = array(0, 0);
		if (strpos($_POST['show_to_time'], ":") !== false)
		{
			$temp = explode(":", $_POST['show_to_time']);
			if (intval($temp[0]) >= 0 && intval($temp[0]) < 24)
			{
				$show_to_time[0] = $temp[0];
			} else
			{
				$has_time_error = true;
			}
			if (intval($temp[1]) >= 0 && intval($temp[1]) < 60)
			{
				$show_to_time[1] = $temp[1];
			} else
			{
				$has_time_error = true;
			}
		} else
		{
			$has_time_error = true;
		}
		$show_to_time = $show_to_time[0] * 3600 + $show_to_time[1] * 60;

		if ($has_time_error)
		{
			$errors[] = get_aa_error('invalid_time_range', $lang['website_ui']['advertisement_field_show_time']);
		}
	}

	if (@count($_POST['devices']) == 0)
	{
		$errors[] = get_aa_error('required_field', $lang['website_ui']['advertisement_field_devices']);
	}

	if (@count($_POST['browsers']) == 0)
	{
		$errors[] = get_aa_error('required_field', $lang['website_ui']['advertisement_field_browsers']);
	}

	if (@count($_POST['users']) == 0)
	{
		$errors[] = get_aa_error('required_field', $lang['website_ui']['advertisement_field_users']);
	}

	if ($_POST['action'] == 'change_complete')
	{
		$item_id = intval($_POST['item_id']);
		foreach ($spots as $spot)
		{
			if (isset($spot['ads'][$item_id]))
			{
				$old_spot = $spot;
				break;
			}
		}
		if (isset($old_spot))
		{
			$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$old_spot[external_id].dat";
			if (!is_file($spot_data_file) || !is_writable($spot_data_file))
			{
				$errors[] = get_aa_error('filesystem_permission_write', $spot_data_file);
			}
		}
	}

	$new_spot = $spots[$_POST['spot_id']];
	if (isset($new_spot))
	{
		$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$new_spot[external_id].dat";
		if (!is_file($spot_data_file) || !is_writable($spot_data_file))
		{
			$errors[] = get_aa_error('filesystem_permission_write', $spot_data_file);
		}
	}

	if (!is_array($errors) && isset($new_spot))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = mt_rand(1, 1000000);
			for ($i = 0; $i < 99999; $i++)
			{
				if (isset($ads[$item_id]))
				{
					$item_id = mt_rand(1, 1000000);
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$item_id = intval($_POST['item_id']);
			if (isset($old_spot) && $old_spot['external_id'] != $_POST['spot_id'])
			{
				unset($old_spot['ads'][$item_id]);
				file_put_contents("$config[project_path]/admin/data/advertisements/spot_$old_spot[external_id].dat", serialize($old_spot), LOCK_EX);
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}

		if (@count($_POST['devices']) == count($list_devices_values))
		{
			$_POST['devices'] = [];
		}
		if (@count($_POST['browsers']) == count($list_browsers_values))
		{
			$_POST['browsers'] = [];
		}
		if (@count($_POST['users']) == count($list_users_values))
		{
			$_POST['users'] = [];
		}

		$new_spot['ads'][$item_id] = [
			'advertisement_id' => $item_id,
			'title' => $_POST['title'],
			'is_active' => intval($_POST['is_active']),
			'show_from_date' => $show_from_date,
			'show_to_date' => $show_to_date,
			'show_from_time' => $show_from_time,
			'show_to_time' => $show_to_time,
			'devices' => $_POST['devices'],
			'browsers' => $_POST['browsers'],
			'users' => $_POST['users'],
			'countries' => implode(',', isset($_POST['countries']) ? array_map('trim', $_POST['countries']) : []),
			'category_ids' => isset($_POST['category_ids']) ? array_map('intval', $_POST['category_ids']) : [],
			'exclude_category_ids' => isset($_POST['exclude_category_ids']) ? array_map('intval', $_POST['exclude_category_ids']) : [],
			'code' => $_POST['code'],
			'url' => $_POST['url'],
			'v5.2' => 1
		];

		file_put_contents($spot_data_file, serialize($new_spot), LOCK_EX);

		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
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
	if (!is_writable("$site_templates_path"))
	{
		foreach ($_REQUEST['row_select'] as $item_id)
		{
			if (strpos($item_id, '/') !== false)
			{
				$spot_id = substr($item_id, 0, strpos($item_id, '/'));
				if (!is_writable("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/advertisements/spot_$spot_id.dat");
				}
			} else
			{
				if (!is_writable("$config[project_path]/admin/data/advertisements"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', "$config[project_path]/admin/data/advertisements");
				}
			}
		}
	}
	if (!is_array($errors))
	{
		if ($_REQUEST['batch_action'] == 'delete')
		{
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				if (strpos($item_id, '/') !== false)
				{
					$spot_id = substr($item_id, 0, strpos($item_id, '/'));
					$advertisement_id = substr($item_id, strpos($item_id, '/') + 1);
					if (isset($spots[$spot_id]))
					{
						unset($spots[$spot_id]['ads'][$advertisement_id]);
						file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat", serialize($spots[$spot_id]), LOCK_EX);
					}
				} else
				{
					$spot_id = $item_id;
					if (isset($spots[$spot_id]))
					{
						unlink("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat");
						@unlink("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt");
						unset($spots[$spot_id]);
					}
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		} elseif ($_REQUEST['batch_action'] == 'activate')
		{
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				if (strpos($item_id, '/') !== false)
				{
					$spot_id = substr($item_id, 0, strpos($item_id, '/'));
					$advertisement_id = substr($item_id, strpos($item_id, '/') + 1);
					if (isset($spots[$spot_id]))
					{
						$spots[$spot_id]['ads'][$advertisement_id]['is_active'] = 1;
						file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat", serialize($spots[$spot_id]), LOCK_EX);
					}
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_activated'];
		} elseif ($_REQUEST['batch_action'] == 'deactivate')
		{
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				if (strpos($item_id, '/') !== false)
				{
					$spot_id = substr($item_id, 0, strpos($item_id, '/'));
					$advertisement_id = substr($item_id, strpos($item_id, '/') + 1);
					if (isset($spots[$spot_id]))
					{
						$spots[$spot_id]['ads'][$advertisement_id]['is_active'] = 0;
						file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat", serialize($spots[$spot_id]), LOCK_EX);
					}
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
		} elseif ($_REQUEST['batch_action']=='enable_debug')
		{
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				$spot_id = $item_id;
				if (isset($spots[$spot_id]))
				{
					$spots[$spot_id]['is_debug_enabled'] = 1;
					file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat", serialize($spots[$spot_id]), LOCK_EX);
				}
			}
			$_SESSION['messages'][]=$lang['common']['success_message_debug_enabled'];
		} elseif ($_REQUEST['batch_action']=='disable_debug')
		{
			foreach ($_REQUEST['row_select'] as $item_id)
			{
				$spot_id = $item_id;
				if (isset($spots[$spot_id]))
				{
					unset($spots[$spot_id]['is_debug_enabled']);
					file_put_contents("$config[project_path]/admin/data/advertisements/spot_$spot_id.dat", serialize($spots[$spot_id]), LOCK_EX);
					@unlink("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt");
				}
			}
			$_SESSION['messages'][]=$lang['common']['success_message_debug_disabled'];
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

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');
$templates_data = get_site_parsed_templates();

$pages_list = get_site_pages();
$page_templates_list = array();
foreach ($pages_list as $page)
{
	$page_templates_list[] = "$page[external_id].tpl";
	$template_info = $templates_data["$page[external_id].tpl"];
	if (isset($template_info))
	{
		foreach ($template_info['spot_inserts'] as $inserted_spot)
		{
			if (isset($spots[$inserted_spot['spot_id']]))
			{
				$spots_usages[$inserted_spot['spot_id']][] = $page;
			}
		}
	}

	foreach ($template_info['block_inserts'] as $block_insert)
	{
		$block_id = $block_insert['block_id'];
		$block_name = $block_insert['block_name'];
		$block_name_mod = strtolower(str_replace(" ", "_", $block_name));

		$block_template_info = $templates_data["blocks/$page[external_id]/{$block_id}_$block_name_mod.tpl"];
		if (isset($block_template_info))
		{
			foreach ($block_template_info['spot_inserts'] as $inserted_spot)
			{
				if (isset($spots[$inserted_spot['spot_id']]))
				{
					$spots_usages[$inserted_spot['spot_id']][] = array('block_uid' => "$page[external_id]||$block_id||$block_name_mod", 'block_title' => $block_name, 'title' => $page['title']);
				}
			}
		}
	}
}

if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
{
	$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks = explode("|AND|", trim($temp[2]));
	foreach ($global_blocks as $global_block)
	{
		if ($global_block == '')
		{
			continue;
		}
		$block_id = substr($global_block, 0, strpos($global_block, "[SEP]"));
		$block_name_mod = substr($global_block, strpos($global_block, "[SEP]") + 5);
		$block_name = ucwords(str_replace('_', ' ', $block_name_mod));

		$block_template_info = $templates_data["blocks/\$global/{$block_id}_$block_name_mod.tpl"];
		if (isset($block_template_info))
		{
			foreach ($block_template_info['spot_inserts'] as $inserted_spot)
			{
				if (isset($spots[$inserted_spot['spot_id']]))
				{
					$spots_usages[$inserted_spot['spot_id']][] = array('block_uid' => "\$global||$block_id||$block_name_mod", 'block_title' => $block_name, 'title' => 'GLOBAL');
				}
			}
		}
	}
}

$list_full_templates = get_contents_from_dir("$site_templates_path", 1);
foreach ($list_full_templates as $v)
{
	if (strtolower(end(explode(".", $v))) !== 'tpl')
	{
		continue;
	}
	if (in_array($v, $page_templates_list))
	{
		continue;
	}

	$template_info = $templates_data[$v];
	if (isset($template_info))
	{
		foreach ($template_info['spot_inserts'] as $inserted_spot)
		{
			if (isset($spots[$inserted_spot['spot_id']]))
			{
				$spots_usages[$inserted_spot['spot_id']][] = array('page_component_id' => $v);
			}
		}
	}
}

$player_files = get_player_data_files();
foreach ($player_files as $player_file)
{
	if (is_file($player_file['file']))
	{
		$player_data = @unserialize(file_get_contents($player_file['file']), ['allowed_classes' => false]);
		foreach ($spots as $spot)
		{
			if ($player_data['start_html_source'] == "spot_$spot[external_id]")
			{
				$spots_usages[$spot['external_id']][] = array('is_player' => 1, 'type' => 'start', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed']));
			}
			if ($player_data['pre_roll_html_source'] == "spot_$spot[external_id]")
			{
				$spots_usages[$spot['external_id']][] = array('is_player' => 1, 'type' => 'pre', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed']));
			}
			if ($player_data['post_roll_html_source'] == "spot_$spot[external_id]")
			{
				$spots_usages[$spot['external_id']][] = array('is_player' => 1, 'type' => 'post', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed']));
			}
			if ($player_data['pause_html_source'] == "spot_$spot[external_id]")
			{
				$spots_usages[$spot['external_id']][] = array('is_player' => 1, 'type' => 'pause', 'url' => $player_file['admin_page'], 'is_embed' => intval($player_file['is_embed']));
			}
		}
	}
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change_spot' && $_GET['item_id'] != '')
{
	$_POST = $spots[$_GET['item_id']];
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
	$_POST['usages'] = $spots_usages[$_POST['external_id']];

	$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$_POST[external_id].dat";
	if (!unserialize(file_get_contents($spot_data_file), ['allowed_classes' => false]))
	{
		$_POST['errors'][] = get_aa_error('website_ui_advertising_spot_format', $spot_data_file);
	} elseif (!is_writable($spot_data_file))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $spot_data_file);
	}
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = $ads[$item_id];
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$show_time_hours = floor(intval($_POST['show_from_time']) / 3600);
	$show_time_minutes = (intval($_POST['show_from_time']) - 3600 * $show_time_hours) / 60;
	if ($show_time_hours < 10)
	{
		$show_time_hours = "0$show_time_hours";
	}
	if ($show_time_minutes < 10)
	{
		$show_time_minutes = "0$show_time_minutes";
	}
	$_POST['show_from_time'] = "$show_time_hours:$show_time_minutes";

	$show_time_hours = floor(intval($_POST['show_to_time']) / 3600);
	$show_time_minutes = (intval($_POST['show_to_time']) - 3600 * $show_time_hours) / 60;
	if ($show_time_hours < 10)
	{
		$show_time_hours = "0$show_time_hours";
	}
	if ($show_time_minutes < 10)
	{
		$show_time_minutes = "0$show_time_minutes";
	}
	$_POST['show_to_time'] = "$show_time_hours:$show_time_minutes";

	if (!isset($_POST['devices']))
	{
		$_POST['devices'] = [];
	}
	if (!isset($_POST['browsers']))
	{
		$_POST['browsers'] = [];
	}
	if (!isset($_POST['users']))
	{
		$_POST['users'] = [];
	}

	$_POST['category_ids'][] = 0;
	$_POST['category_ids'] = implode(',', $_POST['category_ids']);
	$_POST['categories'] = mr2array(sql("select * from $config[tables_prefix]categories where category_id in ($_POST[category_ids])"));

	$_POST['exclude_category_ids'][] = 0;
	$_POST['exclude_category_ids'] = implode(',', $_POST['exclude_category_ids']);
	$_POST['exclude_categories'] = mr2array(sql("select * from $config[tables_prefix]categories where category_id in ($_POST[exclude_category_ids])"));

	foreach ($spots as $spot)
	{
		if (isset($spot['ads'][$item_id]))
		{
			$_POST['spot_id'] = $spot['external_id'];
			$_POST['spot_title'] = $spot['title'];
			break;
		}
	}

	if (strlen($_POST['countries']) == 0)
	{
		$_POST['countries'] = array();
	} else
	{
		$_POST['countries'] = explode(',', $_POST['countries']);
	}

	$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$_POST[spot_id].dat";
	if (!is_writable($spot_data_file))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $spot_data_file);
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['show_from_date'] = "0000-00-00";
	$_POST['show_to_date'] = "0000-00-00";
	$_POST['show_from_time'] = "00:00";
	$_POST['show_to_time'] = "00:00";
	$_POST['is_active'] = 1;

	$_POST['devices'] = [];
	$_POST['browsers'] = [];
	$_POST['users'] = [];
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$table_filtered = 0;

$data = $spots;
if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	foreach ($data as $external_id => $spot)
	{
		if (!mb_contains($spot['title'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($spot['template'], $_SESSION['save'][$page_name]['se_text']))
		{
			foreach ($spot['ads'] as $advertisement_id => $ad)
			{
				if (!mb_contains($ad['title'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($ad['code'], $_SESSION['save'][$page_name]['se_text']))
				{
					unset($data[$external_id]['ads'][$advertisement_id]);
				}
			}
			if (count($data[$external_id]['ads']) == 0)
			{
				unset($data[$external_id]);
			}
		}
	}
}
if ($_SESSION['save'][$page_name]['se_status'] != '')
{
	$table_filtered = 1;

	$now_date = strtotime(date("Y-m-d"));
	$now_time = explode(':', date("H:i"));
	$now_time = intval($now_time[0]) * 3600 + intval($now_time[1]) * 60;

	foreach ($data as $external_id => $spot)
	{
		foreach ($spot['ads'] as $advertisement_id => $ad)
		{
			switch ($_SESSION['save'][$page_name]['se_status'])
			{
				case 'active':
					if ($ad['is_active']==0)
					{
						unset($data[$external_id]['ads'][$advertisement_id]);
					}
					break;
				case 'disabled':
					if ($ad['is_active']==1)
					{
						unset($data[$external_id]['ads'][$advertisement_id]);
					}
					break;
				case 'now':
					if ($ad['is_active']==0)
					{
						unset($data[$external_id]['ads'][$advertisement_id]);
					} elseif ($ad['show_from_date'] != '0000-00-00' && strtotime($ad['show_from_date']) > $now_date)
					{
						unset($data[$external_id]['ads'][$advertisement_id]);
					} elseif ($ad['show_to_date'] != '0000-00-00' && strtotime($ad['show_to_date']) < $now_date)
					{
						unset($data[$external_id]['ads'][$advertisement_id]);
					} elseif ($ad['show_from_time'] > 0 || $ad['show_to_time'] > 0)
					{
						if ($now_time < $ad['show_from_time'] || $now_time > $ad['show_to_time'])
						{
							$hide_ad = true;
							if ($ad['show_from_time'] > $ad['show_to_time'])
							{
								if (($now_time > $ad['show_from_time'] && $now_time < 86400) || $now_time < $ad['show_to_time'])
								{
									$hide_ad = false;
								}
							}
							if ($hide_ad)
							{
								unset($data[$external_id]['ads'][$advertisement_id]);
							}
						}
					}
					break;
			}
		}
	}
}

if ($_SESSION['save'][$page_name]['se_device'] != '')
{
	$table_filtered = 1;

	$now_date = strtotime(date("Y-m-d"));
	$now_time = explode(':', date("H:i"));
	$now_time = intval($now_time[0]) * 3600 + intval($now_time[1]) * 60;

	foreach ($data as $external_id => $spot)
	{
		foreach ($spot['ads'] as $advertisement_id => $ad)
		{
			if (@count($ad['devices']) > 0)
			{
				$ad_device_show = false;
				foreach ($ad['devices'] as $ad_device)
				{
					if ($ad_device_show)
					{
						break;
					}
					if ($ad_device == $_SESSION['save'][$page_name]['se_device'])
					{
						$ad_device_show = true;
					}
				}
				if (!$ad_device_show)
				{
					unset($data[$external_id]['ads'][$advertisement_id]);
				}
			}
		}
		if (count($data[$external_id]['ads']) == 0)
		{
			unset($data[$external_id]);
		}
	}
}

$total_num = 0;
foreach ($data as $spot)
{
	$total_num += count($spot['ads']);
}

foreach ($data as $external_id => $spot)
{
	$data[$external_id]['usages'] = $spots_usages[$spot['external_id']];
	if (is_file("$config[project_path]/admin/logs/debug_ad_spot_$external_id.txt"))
	{
		$data[$external_id]['has_debug_log'] = 1;
	}
	foreach ($spot['ads'] as $advertisement_id => $ad)
	{
		$ad['category_ids'][] = 0;
		$data[$external_id]['ads'][$advertisement_id]['categories'] = mr2array(sql("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories where category_id in (" . implode(',', $ad['category_ids']) . ")"));

		$ad['exclude_category_ids'][] = 0;
		$data[$external_id]['ads'][$advertisement_id]['exclude_categories'] = mr2array(sql("select $config[tables_prefix]categories.category_id as id, $config[tables_prefix]categories.title from $config[tables_prefix]categories where category_id in (" . implode(',', $ad['exclude_category_ids']) . ")"));

		$data[$external_id]['ads'][$advertisement_id]['show_date_from'] = $ad['show_from_date'];
		$data[$external_id]['ads'][$advertisement_id]['show_date_to'] = $ad['show_to_date'];

		$show_time_hours = floor(intval($ad['show_from_time']) / 3600);
		$show_time_minutes = (intval($ad['show_from_time']) - 3600 * $show_time_hours) / 60;
		if ($show_time_hours < 10)
		{
			$show_time_hours = "0$show_time_hours";
		}
		if ($show_time_minutes < 10)
		{
			$show_time_minutes = "0$show_time_minutes";
		}
		$data[$external_id]['ads'][$advertisement_id]['show_time_from'] = "$show_time_hours:$show_time_minutes";

		$show_time_hours = floor(intval($ad['show_to_time']) / 3600);
		$show_time_minutes = (intval($ad['show_to_time']) - 3600 * $show_time_hours) / 60;
		if ($show_time_hours < 10)
		{
			$show_time_hours = "0$show_time_hours";
		}
		if ($show_time_minutes < 10)
		{
			$show_time_minutes = "0$show_time_minutes";
		}
		$data[$external_id]['ads'][$advertisement_id]['show_time_to'] = "$show_time_hours:$show_time_minutes";

		$ad_countries = array();
		$ad['countries'] = explode(',', $ad['countries']);
		foreach ($ad['countries'] as $country_code)
		{
			if ($country_code != '' && isset($list_countries[$country_code]))
			{
				$ad_countries[] = array('title' => $list_countries[$country_code]);
			}
		}
		$data[$external_id]['ads'][$advertisement_id]['countries'] = $ad_countries;
	}

	$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$external_id.dat";
	if (!unserialize(file_get_contents($spot_data_file), ['allowed_classes' => false]))
	{
		$data[$external_id]['errors'] = 1;
	} elseif (!is_writable($spot_data_file))
	{
		$data[$external_id]['warnings'] = 1;
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('list_spots', $spots);
$smarty->assign('left_menu', 'menu_website_ui.tpl');
$smarty->assign('list_countries', $list_countries);

if (in_array($_REQUEST['action'], array('change', 'change_spot')))
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

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

if ($_REQUEST['action'] == 'change_spot')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['website_ui']['spot_edit']));
} elseif ($_REQUEST['action'] == 'add_new_spot')
{
	$smarty->assign('page_title', $lang['website_ui']['spot_add']);
} elseif ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['website_ui']['advertisement_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['website_ui']['advertisement_add']);
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_advertisements_list']);
}

$smarty->display("layout.tpl");