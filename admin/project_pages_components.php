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

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');

$templates_data = get_site_parsed_templates();
$spots_data = get_site_spots();

$errors = null;

if ($_GET['action'] == 'duplicate')
{
	if (strtolower(end(explode(".", $_GET['external_id']))) === 'tpl')
	{
		$_GET['external_id'] = substr($_GET['external_id'], 0, -4);
	}
	if (strtolower(end(explode(".", $_GET['item_id']))) === 'tpl')
	{
		$_GET['item_id'] = substr($_GET['item_id'], 0, -4);
	}

	$validation_errors = validate_page_component($_GET['external_id'], '', true, false);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_component_external_id_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_component_field_id']);
				break;
			case 'page_component_external_id_invalid':
				$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['page_component_field_id']);
				break;
			case 'page_component_external_id_duplicate':
				$errors[] = get_aa_error('unique_field', $lang['website_ui']['page_component_field_id']);
				break;
			case 'fs_permissions':
				$errors[] = get_aa_error('filesystem_permission_write', $validation_error['data']);
				break;
		}
	}
	if (!is_array($errors))
	{
		$contents = '';
		if (preg_match($regexp_valid_page_component_id, $_GET['item_id']) && is_file("$site_templates_path/$_GET[item_id].tpl"))
		{
			$contents = file_get_contents("$site_templates_path/$_GET[item_id].tpl");
		}
		file_put_contents("$site_templates_path/$_GET[external_id].tpl", $contents, LOCK_EX);

		$_SESSION['messages'][] = $lang['website_ui']['success_message_component_duplicated'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	if ($_POST['action'] == 'change_complete')
	{
		$_POST['external_id'] = $_POST['item_id'];
	}
	if (strtolower(end(explode(".", $_POST['external_id']))) === 'tpl')
	{
		$_POST['external_id'] = substr($_POST['external_id'], 0, -4);
	}

	$validation_errors = validate_page_component($_POST['external_id'], $_POST['template'], $_POST['action'] == 'add_new_complete', $_POST['action'] == 'change_complete');
	foreach ($validation_errors as $validation_error)
	{
		$template_field_name = $lang['website_ui']['page_component_field_template_code'];
		if ($validation_error['include'] != '')
		{
			$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
		}
		switch ($validation_error['type'])
		{
			case 'page_component_external_id_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_component_field_id']);
				break;
			case 'page_component_external_id_invalid':
				if ($validation_error['include'] != '')
				{
					$errors[] = get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']);
				} else
				{
					$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['page_component_field_id']);
				}
				break;
			case 'page_component_external_id_duplicate':
				$errors[] = get_aa_error('unique_field', $lang['website_ui']['page_component_field_id']);
				break;
			case 'page_component_template_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_component_field_template_code']);
				break;
			case 'page_component_insert_block':
				$errors[] = get_aa_error('website_ui_invalid_block_insert_component', $template_field_name);
				break;
			case 'page_component_insert_global':
				$errors[] = get_aa_error('website_ui_invalid_block_insert_component2', $template_field_name);
				break;
			case 'global_block_uid_invalid':
				$errors[] = get_aa_error('website_ui_invalid_global_id', $template_field_name, $validation_error['data']);
				break;
			case 'page_component_unknown':
				$errors[] = get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']);
				break;
			case 'fs_permissions':
				$errors[] = get_aa_error('filesystem_permission_write', $validation_error['data']);
				break;
		}
	}

	if (!is_array($errors))
	{
		file_put_contents("$site_templates_path/$_POST[external_id].tpl", $_POST['template'], LOCK_EX);

		$file = "$site_templates_path/$_POST[external_id].tpl";
		$file_content = @file_get_contents($file);
		$hash = md5($file_content);
		$path = str_replace($config['project_path'], '', $file);
		if (sql_update("update $config[tables_prefix_multi]file_changes set hash=?, file_content=?, modified_date=?, is_modified=0 where path=?", $hash, $file_content, date("Y-m-d H:i:s"), $path) == 0)
		{
			sql_pr("insert into $config[tables_prefix_multi]file_changes set path=?, hash=?, file_content=?, modified_date=?, is_modified=0", $path, $hash, $file_content, date("Y-m-d H:i:s"));
		}

		$last_version = mr2array_single(sql_pr("select version, hash, added_date, user_id from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
		if ($hash != $last_version['hash'])
		{
			if ($last_version['user_id'] == $_SESSION['userdata']['user_id'] && $last_version['version'] > 1 && time() - strtotime($last_version['added_date']) < 300)
			{
				sql_pr("update $config[tables_prefix_multi]file_history set hash=?, file_content=?, added_date=? where path=? and version=?", $hash, $file_content, date("Y-m-d H:i:s"), $path, intval($last_version['version']));
			} else
			{
				sql_pr("insert into $config[tables_prefix_multi]file_history set path=?, version=?, hash=?, file_content=?, user_id=?, username=?, added_date=?", $path, intval($last_version['version']) + 1, $hash, $file_content, $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], date("Y-m-d H:i:s"));
			}
		}

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

if ($_GET['action'] == 'change' && $_GET['item_id'] != '')
{
	$page_component_id = $_GET['item_id'];
	if (strtolower(end(explode(".", $page_component_id))) === 'tpl')
	{
		$page_component_id = substr($page_component_id, 0, -4);
	}

	$validation_errors = validate_page_component($page_component_id);
	$includes_with_errors = array();
	foreach ($validation_errors as $validation_error)
	{
		$template_field_name = $lang['website_ui']['page_component_field_template_code'];
		if ($validation_error['include'] != '')
		{
			$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
			$includes_with_errors[$validation_error['include']] = true;
		}
		switch ($validation_error['type'])
		{
			case 'page_component_external_id_empty':
			case 'page_component_external_id_invalid':
				if ($validation_error['include'] != '')
				{
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']));
				} else
				{
					$_POST['errors'][] = bb_code_process(get_aa_error('invalid_external_id', $lang['website_ui']['page_component_field_id']));
				}
				break;
			case 'page_component_template_empty':
				$_POST['errors'][] = bb_code_process(get_aa_error('required_field', $lang['website_ui']['page_component_field_template_code']));
				break;
			case 'page_component_insert_block':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_component', $template_field_name));
				break;
			case 'page_component_insert_global':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_component2', $template_field_name));
				break;
			case 'global_block_uid_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_global_id', $template_field_name, $validation_error['data']));
				break;
			case 'page_component_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']));
				break;
			case 'advertising_spot_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $template_field_name, $validation_error['data']));
				break;
			case 'file_missing':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_missing_required_file', $validation_error['data']));
				break;
			case 'fs_permissions':
				$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', $validation_error['data']));
				break;
		}
	}
	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}

	$_POST['external_id'] = $page_component_id;

	$template_info = $templates_data["$page_component_id.tpl"];
	if (isset($template_info))
	{
		$_POST['template'] = $template_info['template_code'];

		$file = "$site_templates_path/$page_component_id.tpl";
		$file_content = $_POST['template'];
		$path = str_replace($config['project_path'], '', $file);
		$hash_check = md5($file_content);

		$last_version = mr2array_single(sql_pr("select version, hash from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
		if ($hash_check != $last_version['hash'])
		{
			sql_pr("insert into $config[tables_prefix_multi]file_history set path=?, hash=?, version=?, file_content=?, user_id=0, username='filesystem', added_date=?", $path, $hash_check, intval($last_version['version']) + 1, $file_content, date("Y-m-d H:i:s", filectime($file)));
		}

		$template_includes = get_site_includes_recursively($template_info);
		foreach ($template_includes as $included_page => $included_page_info)
		{
			$_POST['template_includes'][] = array('filename' => $included_page, 'errors' => intval($includes_with_errors[$included_page]));
		}
	}
}

if ($_REQUEST['batch_action'] <> '' && count($_REQUEST['row_select']) > 0)
{
	if ($_REQUEST['batch_action'] == 'delete')
	{
		if (!is_writable("$site_templates_path"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path"));
		}
		if (!is_array($errors))
		{
			foreach ($_REQUEST['row_select'] as $file)
			{
				if ($file == '0')
				{
					continue;
				}
				unlink("$site_templates_path/$file");
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
			return_ajax_success($page_name);
		} else
		{
			return_ajax_errors($errors);
		}
	}
}


$data = array();
$pages_list = get_site_pages();
$page_templates_list = array();
foreach ($pages_list as $page)
{
	$main_template = "$page[external_id].tpl";
	$page_templates_list[] = $main_template;

	$template_info = $templates_data[$main_template];
	if (isset($template_info))
	{
		foreach ($template_info['template_includes'] as $included_template)
		{
			if (($_SESSION['save'][$page_name]['se_text'] == '' || strpos($included_template, $_SESSION['save'][$page_name]['se_text']) !== false) && !isset($data[$included_template][$main_template]))
			{
				$data[$included_template][$main_template] = $page;
			}
		}

		$blocks_files_list = array();
		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = $block_insert['block_id'];
			$block_name = $block_insert['block_name'];
			$block_name_mod = strtolower(str_replace(" ", "_", $block_name));

			$block_template_info = $templates_data["blocks/$page[external_id]/{$block_id}_$block_name_mod.tpl"];
			if (isset($block_template_info))
			{
				foreach ($block_template_info['template_includes'] as $included_template)
				{
					$block_uid = "$page[external_id]||$block_id||$block_name_mod";
					if (($_SESSION['save'][$page_name]['se_text'] == '' || strpos($included_template, $_SESSION['save'][$page_name]['se_text']) !== false) && !isset($data[$included_template][$block_uid]))
					{
						$data[$included_template][$block_uid] = array('block_uid' => "$page[external_id]||$block_id||$block_name_mod", 'block_title' => $block_name, 'title' => $page['title']);
					}
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
			foreach ($block_template_info['template_includes'] as $included_template)
			{
				if ($_SESSION['save'][$page_name]['se_text'] == '' || strpos($included_template, $_SESSION['save'][$page_name]['se_text']) !== false)
				{
					$data[$included_template][] = array('block_uid' => "\$global||$block_id||$block_name_mod", 'block_title' => $block_name, 'title' => 'GLOBAL');
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
		foreach ($template_info['template_includes'] as $included_template)
		{
			if (($_SESSION['save'][$page_name]['se_text'] == '' || mb_contains($included_template, $_SESSION['save'][$page_name]['se_text'])) && !isset($data[$included_template][$v]))
			{
				$data[$included_template][$v] = array('page_component_id' => $v);
			}
		}
	}

	if ($_SESSION['save'][$page_name]['se_text'] <> '' && !mb_contains($v, $_SESSION['save'][$page_name]['se_text']))
	{
		continue;
	}

	if (!isset($data[$v]))
	{
		$data[$v] = array();
	}
}

foreach ($data as $k => $v)
{
	if (!is_file("$site_templates_path/$k"))
	{
		$page_component_id = $k;
		if (strtolower(end(explode(".", $page_component_id))) === 'tpl')
		{
			$page_component_id = substr($page_component_id, 0, -4);
		}
		if (!preg_match($regexp_valid_page_component_id, $page_component_id) || strpos($page_component_id, '.') !== false)
		{
			unset($data[$k]);
		} elseif (!is_writable($site_templates_path))
		{
			$_POST['invalid_templates'][] = $k;
		} else
		{
			$_POST['invalid_templates'][] = $k;
			file_put_contents("$site_templates_path/$k", '', LOCK_EX);

			$file = "$site_templates_path/$k";
			$file_content = @file_get_contents($file);
			$hash = md5($file_content);
			$path = str_replace($config['project_path'], '', $file);
			if (sql_update("update $config[tables_prefix_multi]file_changes set hash=?, file_content=?, modified_date=?, is_modified=0 where path=?", $hash, $file_content, date("Y-m-d H:i:s"), $path) == 0)
			{
				sql_pr("insert into $config[tables_prefix_multi]file_changes set path=?, hash=?, file_content=?, modified_date=?, is_modified=0", $path, $hash, $file_content, date("Y-m-d H:i:s"));
			}

			$last_version = mr2array_single(sql_pr("select version, hash, added_date, user_id from $config[tables_prefix_multi]file_history where path=? order by version desc limit 1", $path));
			sql_pr("insert into $config[tables_prefix_multi]file_history set path=?, version=?, hash=?, file_content=?, user_id=0, username='filesystem', added_date=?", $path, intval($last_version['version']) + 1, $hash, $file_content, date("Y-m-d H:i:s"));
		}
	}
}

foreach ($data as $k => $v)
{
	$validation_errors = validate_page_component($k);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_component_external_id_empty':
			case 'page_component_external_id_invalid':
			case 'page_component_insert_block':
			case 'page_component_insert_global':
			case 'global_block_uid_invalid':
			case 'page_component_unknown':
			case 'advertising_spot_unknown':
			case 'file_missing':
				$_POST['invalid_templates'][] = $k;
				break;
			case 'page_component_template_empty':
			case 'fs_permissions':
				$_POST['warning_templates'][] = $k;
				break;
		}
	}
}
ksort($data);

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('total_num', count($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['external_id'], $lang['website_ui']['page_component_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['website_ui']['page_component_add']);
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_page_components']);
}

$smarty->display("layout.tpl");
