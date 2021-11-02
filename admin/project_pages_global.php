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

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');

$templates_data = get_site_parsed_templates();
$spots_data = get_site_spots();

$errors = null;

$state_file_path = "$config[project_path]/admin/data/engine/blocks_state/state_check.dat";
$block_checks = @unserialize(@file_get_contents(($state_file_path)));
if (!is_array($block_checks))
{
	$block_checks = array();
}

$blocks_list = get_contents_from_dir("$config[project_path]/blocks", 2);
sort($blocks_list);
foreach ($blocks_list as $k => $v)
{
	if (!is_file("$config[project_path]/blocks/$v/$v.php") || !is_file("$config[project_path]/blocks/$v/$v.dat"))
	{
		header("Location: project_blocks.php");
		die;
	}

	if (filemtime("$config[project_path]/blocks/$v/$v.php") != $block_checks[$v])
	{
		$block_checks[$v] = filemtime("$config[project_path]/blocks/$v/$v.php");

		unset($res);
		exec("$config[php_path] $config[project_path]/blocks/$v/$v.php test", $res);
		if (trim(implode("", $res)) != 'OK')
		{
			header("Location: project_blocks.php");
			die;
		}

		include_once("$config[project_path]/blocks/$v/$v.php");
		if (function_exists("{$v}Show") === false || function_exists("{$v}GetHash") === false || function_exists("{$v}MetaData") === false)
		{
			header("Location: project_blocks.php");
			die;
		}
	}
}
if (!is_dir(dirname($state_file_path)))
{
	mkdir(dirname($state_file_path), 0777);
	chmod(dirname($state_file_path), 0777);
}
file_put_contents($state_file_path, serialize($block_checks), LOCK_EX);

$external_id = '$global';
if ($_POST['action'] == 'change_complete')
{
	$pages = get_site_pages();

	if (!is_writable("$site_templates_path/blocks/$external_id"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks/$external_id"));
	}
	if (!is_writable("$config[project_path]/admin/data/config/$external_id"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id"));
	}
	if (!is_writable("$config[project_path]/admin/data/config/$external_id/config.dat"))
	{
		$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id/config.dat"));
	}

	if (!is_array($errors))
	{
		$blocks_name_list = array();
		for ($i = 1; $i < 100; $i++)
		{
			if (!isset($_POST["id_$i"]))
			{
				break;
			}

			$block_id = trim($_POST["id_$i"]);
			$block_name = trim($_POST["name_$i"]);
			$block_original_name = $block_name;
			$valid_ids = 1;

			if ($block_id == '' && $block_name == '')
			{
				continue;
			}
			if (intval($_POST["delete_$i"]) == 1)
			{
				if ($block_id <> '' && $block_name <> '')
				{
					$block_uid = "{$block_id}_" . strtolower(str_replace(" ", "_", $block_name));
					if (is_file("$config[project_path]/admin/data/config/$external_id/$block_uid.dat"))
					{
						$insert_directive = "|{{insert\ +name\ *=\ *['\"]getGlobal['\"]\ +global_id\ *=\ *['\"]($block_uid)['\"]|is";
						foreach ($pages as $page)
						{
							$page_contents = file_get_contents("$site_templates_path/$page[external_id].tpl");
							preg_match_all($insert_directive, $page_contents, $temp_blocks_list);
							settype($temp_blocks_list[1], "array");
							if (count($temp_blocks_list[1]) > 0)
							{
								$errors[] = get_aa_error('global_block_cannot_be_deleted', $block_name);
								break;
							}
						}
					}
				}
				continue;
			}

			if ($block_id == '')
			{
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_global_dg_blocks_col_id']);
				$valid_ids = 0;
			}
			if ($block_name == '')
			{
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_global_dg_blocks_col_name']);
				$valid_ids = 0;
			}
			if (trim($_POST["cache_$i"]) <> '0' && intval(trim($_POST["cache_$i"])) < 1)
			{
				$errors[] = get_aa_error('integer_field', $lang['website_ui']['page_global_dg_blocks_col_cache_time']);
			}
			if ($valid_ids == 0)
			{
				continue;
			}

			if (!preg_match($regexp_valid_external_id, $block_id))
			{
				$errors[] = get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['page_global_dg_blocks_col_id'], $block_id);
				$valid_ids = 0;
			}
			if (!preg_match($regexp_valid_block_name, $block_name))
			{
				$errors[] = get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['page_global_dg_blocks_col_name'], $block_name);
				$valid_ids = 0;
			}
			if ($valid_ids == 0)
			{
				continue;
			}

			$block_name = strtolower(str_replace(" ", "_", $block_name));
			if (in_array("$block_name", $blocks_name_list))
			{
				$errors[] = get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['page_global_dg_blocks_col_name'], $block_original_name);
				$valid_ids = 0;
			}
			if (!in_array($block_id, $blocks_list))
			{
				$errors[] = get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['page_global_dg_blocks_col_id'], $block_id);
				$valid_ids = 0;
			}
			if ($valid_ids == 0)
			{
				continue;
			}

			$blocks_name_list[] = "$block_name";
			if (is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat") && !is_writable("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat"));
			}
			if (is_file("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl") && !is_writable("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl"));
			}
		}
		if (is_array($errors))
		{
			$errors = array_unique($errors);
		}

		if (!is_array($errors))
		{
			$list_blocks = array();
			for ($i = 1; $i < 100; $i++)
			{
				if (!isset($_POST["id_$i"]))
				{
					break;
				}
				if (intval($_POST["delete_$i"]) == 1)
				{
					continue;
				}
				$block_id = trim($_POST["id_$i"]);
				$block_name = strtolower(str_replace(" ", "_", trim($_POST["name_$i"])));
				$block_display_name = trim($_POST["name_$i"]);

				if ($block_id == '' && $block_name == '')
				{
					continue;
				}

				$list_blocks[] = "{$block_id}[SEP]$block_name";
				if (is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat"))
				{
					$file_data = @file_get_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat");
					$temp_bl = explode("||", $file_data);
					$temp_bl[0] = intval($_POST["cache_$i"]);
					$file_data = implode("||", $temp_bl);

					file_put_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat", $file_data, LOCK_EX);
				} else
				{
					include_once("$config[project_path]/blocks/$block_id/$block_id.php");
					$metadata_function = "{$block_id}MetaData";
					if (function_exists($metadata_function))
					{
						$params = $metadata_function();
					} else
					{
						$params = array();
					}
					$list_params = "";
					foreach ($params as $param)
					{
						if ($param['is_required'] == 1)
						{
							$list_params .= "&$param[name]=$param[default_value]";
						} elseif ($param['type'] == '' && $param['default_value'] <> '')
						{
							$list_params .= "&$param[name]";
						}
					}
					$list_params = trim($list_params, "&");

					file_put_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat", intval($_POST["cache_$i"]) . "||$list_params||0||$block_id", LOCK_EX);
				}
				if (!is_file("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl"))
				{
					file_put_contents("$site_templates_path/blocks/$external_id/{$block_id}_$block_name.tpl", "<div class=\"$block_id\">\n$block_display_name\n</div>", LOCK_EX);
				}
			}
			$list_blocks = implode("|AND|", $list_blocks);
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];

			file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . $list_blocks . "||0", LOCK_EX);

			return_ajax_success($page_name);
		} else
		{
			return_ajax_errors($errors);
		}
	} else
	{
		return_ajax_errors($errors);
	}
}

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && count($_REQUEST['row_select']) > 0)
{
	$row_select = $_REQUEST['row_select'];
	if ($_REQUEST['batch_action'] == 'restore_block')
	{
		if (!is_writable("$config[project_path]/admin/data/config/$external_id/config.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id/config.dat"));
		}
		if (!is_array($errors))
		{
			$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
			$list_blocks = explode("|AND|", trim($temp[2]));

			foreach ($row_select as $temp_id)
			{
				$temp = explode('||', $temp_id);
				$block_id = trim($temp[0]);
				$block_name_mod = trim($temp[1]);
				if ($block_id == '' || $block_name_mod == '')
				{
					continue;
				}
				if (!in_array($block_id, $blocks_list) || !preg_match($regexp_valid_external_id, $block_name_mod))
				{
					continue;
				}
				if (is_file("$config[project_path]/admin/data/config/$external_id/{$block_id}_{$block_name_mod}.dat"))
				{
					$list_blocks[] = "{$block_id}[SEP]$block_name_mod";
				}
			}
			$list_blocks = implode("|AND|", $list_blocks);

			file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . $list_blocks . "||0", LOCK_EX);

			$_SESSION['messages'][] = $lang['website_ui']['success_message_block_restored'];
		}
	} elseif ($_REQUEST['batch_action'] == 'wipeout_block')
	{
		if (is_dir("$site_templates_path/blocks/$external_id") && !is_writable("$site_templates_path/blocks/$external_id"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks/$external_id"));
		}
		if (is_dir("$config[project_path]/admin/data/config/$external_id") && !is_writable("$config[project_path]/admin/data/config/$external_id"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id"));
		}
		if (!is_array($errors))
		{
			foreach ($row_select as $temp_id)
			{
				$temp = explode('||', $temp_id);
				$block_id = trim($temp[0]);
				$block_name_mod = trim($temp[1]);
				if ($block_id == '' || $block_name_mod == '')
				{
					continue;
				}
				if (!in_array($block_id, $blocks_list) || !preg_match($regexp_valid_external_id, $block_name_mod))
				{
					continue;
				}
				@unlink("$config[project_path]/admin/data/config/$external_id/{$block_id}_{$block_name_mod}.dat");
				@unlink("$site_templates_path/blocks/$external_id/{$block_id}_{$block_name_mod}.tpl");
			}
			$_SESSION['messages'][] = $lang['website_ui']['success_message_block_wiped_out'];
		}
	}
	if (is_array($errors))
	{
		return_ajax_errors($errors);
	} else
	{
		return_ajax_success($page_name);
	}
}

if (!is_dir("$site_templates_path/blocks/$external_id"))
{
	mkdir("$site_templates_path/blocks/$external_id", 0777);
	chmod("$site_templates_path/blocks/$external_id", 0777);
}
if (!is_dir("$config[project_path]/admin/data/config/$external_id"))
{
	mkdir("$config[project_path]/admin/data/config/$external_id", 0777);
	chmod("$config[project_path]/admin/data/config/$external_id", 0777);
}
if (!is_file("$config[project_path]/admin/data/config/$external_id/config.dat"))
{
	file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||||0", LOCK_EX);
}

$_POST['blocks'] = array();
$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/config.dat"));
$list_blocks = explode("|AND|", trim($temp[2]));
$i = 1;
$valid_global_blocks = array();
$template_global_blocks = '';
foreach ($list_blocks as $block)
{
	if ($block == '')
	{
		continue;
	}
	$block_id = substr($block, 0, strpos($block, "[SEP]"));
	$block_name = substr($block, strpos($block, "[SEP]") + 5);
	$block_display_name = ucwords(str_replace('_', ' ', substr($block, strpos($block, "[SEP]") + 5)));
	$block = str_replace("[SEP]", "_", $block);

	$template_global_blocks .= "{{insert name=\"getBlock\" block_id=\"$block_id\" block_name=\"$block_display_name\"}}\n";

	if (!is_file("$config[project_path]/admin/data/config/$external_id/$block.dat"))
	{
		include_once("$config[project_path]/blocks/$block_id/$block_id.php");
		$metadata_function = "{$block_id}MetaData";
		if (function_exists($metadata_function))
		{
			$params = $metadata_function();
		} else
		{
			$params = array();
		}
		$list_params = "";
		foreach ($params as $param)
		{
			if ($param['is_required'] == 1)
			{
				$list_params .= "&$param[name]=$param[default_value]";
			} elseif ($param['type'] == '' && $param['default_value'] <> '')
			{
				$list_params .= "&$param[name]";
			}
		}
		$list_params = trim($list_params, "&");

		file_put_contents("$config[project_path]/admin/data/config/$external_id/$block.dat", "86400||$list_params||0||$block_id", LOCK_EX);
	}
	if (!is_file("$site_templates_path/blocks/$external_id/$block.tpl"))
	{
		file_put_contents("$site_templates_path/blocks/$external_id/$block.tpl", "<div class=\"$block_id\">\n$block_display_name\n</div>", LOCK_EX);
	}

	$valid_global_blocks[] = $block;

	$_POST['blocks'][$i] = array();
	$_POST['blocks'][$i]['id'] = $block_id;
	$_POST['blocks'][$i]['name'] = $block_name;
	$_POST['blocks'][$i]['display_name'] = $block_display_name;
	if (is_file("$config[project_path]/admin/data/config/$external_id/$block.dat"))
	{
		$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$external_id/$block.dat"));
		$_POST['blocks'][$i]['cache'] = intval($temp[0]);
	}
	$i++;
}
$templates_data['$global.tpl'] = get_site_parsed_template($template_global_blocks);

$templates_list = get_contents_from_dir($site_templates_path, 1);
foreach ($templates_list as $template_file)
{
	$template_info = $templates_data[$template_file];
	if (isset($template_info))
	{
		foreach ($template_info['global_block_inserts'] as $global_block_insert)
		{
			$global_id = trim($global_block_insert['global_uid']);
			if ($global_id != '')
			{
				$known_global_block = false;
				foreach ($_POST['blocks'] as $k1 => $v1)
				{
					if ($global_id == "$v1[id]_$v1[name]")
					{
						$_POST['blocks'][$k1]['is_used'] = 1;
						$known_global_block = true;
					}
				}
				if (!$known_global_block)
				{
					if (is_file("$site_templates_path/blocks/$external_id/$global_id.tpl") && is_file("$config[project_path]/admin/data/config/$external_id/$global_id.dat"))
					{
						$global_block_info = file_get_contents("$config[project_path]/admin/data/config/$external_id/$global_id.dat");
						$temp_bl = explode('||', $global_block_info);
						if ($temp_bl[3] != '')
						{
							$block_name = substr($global_id, strlen($temp_bl[3]) + 1);
							$block_display_name = ucwords(str_replace("_", " ", $block_name));
							$list_blocks[] = "{$temp_bl[3]}[SEP]$block_name";
							$valid_global_blocks[] = $global_id;

							file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", "0||0||" . implode("|AND|", $list_blocks) . "||0", LOCK_EX);

							$new_block = array();
							$new_block['id'] = $temp_bl[3];
							$new_block['name'] = $block_name;
							$new_block['display_name'] = $block_display_name;
							$new_block['cache'] = intval($temp_bl[0]);
							$new_block['is_used'] = 1;
							$_POST['blocks'][] = $new_block;
						}
					}
				}
			}
		}
	}
}

if (is_file("$config[project_path]/.htaccess"))
{
	$htaccess_contents = file_get_contents("$config[project_path]/.htaccess");
	foreach ($_POST['blocks'] as $k1 => $v1)
	{
		if (strpos($htaccess_contents, "block_id=$v1[id]_$v1[name]") !== false)
		{
			$_POST['blocks'][$k1]['is_used'] = 1;
		}
	}
}

$has_block_caching_errors = array();
$validation_errors = validate_page('$global', $template_global_blocks, '', false, false, true);
foreach ($validation_errors as $validation_error)
{
	if ($validation_error['block_uid'] != '')
	{
		$has_block_error = false;
		$has_block_warning = false;
		switch ($validation_error['type'])
		{
			case 'block_id_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['page_global_dg_blocks_col_id'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'block_state_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_state', $lang['website_ui']['page_global_dg_blocks_col_id'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'block_name_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['page_global_dg_blocks_col_name'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'block_name_duplicate':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['page_global_dg_blocks_col_name'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'block_template_empty':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_empty_template', $validation_error['block_name']));
				$has_block_error = true;
				break;
			case 'block_circular_insert_block':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block', $validation_error['block_name']));
				$has_block_error = true;
				break;
			case 'block_circular_insert_global':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block2', $validation_error['block_name']));
				$has_block_error = true;
				break;
			case 'page_component_external_id_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $validation_error['block_name'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'page_component_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $validation_error['block_name'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'advertising_spot_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $validation_error['block_name'], $validation_error['data']));
				$has_block_error = true;
				break;
			case 'file_missing':
				$has_block_error = true;
				break;
			case 'block_template_smarty_session_usage':
			case 'block_template_smarty_session_status_usage':
			case 'block_template_smarty_get_usage':
			case 'block_template_smarty_request_usage':
				$has_block_caching_errors[$validation_error['block_uid']] = $validation_error['block_name'];
				if ($validation_error['include'] == '')
				{
					$has_block_error = true;
				} else
				{
					$has_block_warning = true;
				}
				break;
			case 'fs_permissions':
				$has_block_warning = true;
				break;
		}
		if ($has_block_error || $has_block_warning)
		{
			foreach ($_POST['blocks'] as $k => $v)
			{
				if ($validation_error['block_uid'] == "{$v['id']}_$v[name]")
				{
					if ($has_block_error)
					{
						$_POST['blocks'][$k]['errors'] = 1;
					}
					if ($has_block_warning)
					{
						$_POST['blocks'][$k]['warnings'] = 1;
					}
				}
			}
		}
	}
}
foreach ($has_block_caching_errors as $block_name)
{
	$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_caching_issues', $block_name));
}
foreach ($validation_errors as $validation_error)
{
	switch ($validation_error['type'])
	{
		case 'file_missing':
			$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_missing_required_file', $validation_error['data']));
			break;
		case 'dir_missing':
			$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_missing_required_dir', $validation_error['data']));
			break;
		case 'fs_permissions':
			if ($validation_error['block_uid'] == '')
			{
				$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', $validation_error['data']));
			}
			break;
	}
}

if (is_array($_POST['errors']))
{
	$_POST['errors'] = array_unique($_POST['errors']);
}

$list_global_files = get_contents_from_dir("$config[project_path]/admin/data/config/$external_id", 1);
$deleted_blocks = array();
$deleted_blocks_count = 0;
foreach ($list_global_files as $global_file)
{
	if ($global_file == "config.dat")
	{
		continue;
	}

	$is_delete_block = 1;
	foreach ($valid_global_blocks as $k)
	{
		if ($global_file == "$k.dat")
		{
			$is_delete_block = 0;
			break;
		}
	}
	if ($is_delete_block == 1)
	{
		$block_uid = str_replace(".dat", "", $global_file);
		if (is_file("$site_templates_path/blocks/$external_id/$block_uid.tpl"))
		{
			$temp = array_map('trim', explode("||", file_get_contents("$config[project_path]/admin/data/config/$external_id/$global_file")));
			if ($temp[3] != '' && in_array($temp[3], $blocks_list))
			{
				$deleted_blocks[] = array('block_id' => $temp[3], 'block_name_mod' => substr($block_uid, strlen($temp[3]) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($temp[3]) + 1))));
				$deleted_blocks_count++;
			} elseif ($temp[3] == '')
			{
				foreach ($blocks_list as $block_type_id)
				{
					if (strpos($block_uid, $block_type_id) === 0)
					{
						$deleted_blocks[] = array('block_id' => $block_type_id, 'block_name_mod' => substr($block_uid, strlen($block_type_id) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($block_type_id) + 1))));
						$deleted_blocks_count++;
					}
				}
			}
		}
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');
$smarty->assign('blocks_list', $blocks_list);
$smarty->assign('deleted_global_blocks', $deleted_blocks);
$smarty->assign('deleted_global_blocks_count', $deleted_blocks_count);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

if ($_REQUEST['action'] == 'restore_blocks')
{
	$smarty->assign('page_title', str_replace("%1%", $deleted_blocks_count, $lang['website_ui']['submenu_option_restore_global_blocks']));
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_global_blocks']);
}

$smarty->display("layout.tpl");
