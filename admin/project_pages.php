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

$errors = null;

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_show_id'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_show_id']))
	{
		$_SESSION['save'][$page_name]['se_show_id'] = $_GET['se_show_id'];
	}
}

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');

$templates_data = get_site_parsed_templates();
$spots_data = get_site_spots();

$state_file_path = "$config[project_path]/admin/data/engine/blocks_state/state_check.dat";
$block_checks = @unserialize(@file_get_contents(($state_file_path)));
if (!is_array($block_checks))
{
	$block_checks = array();
}

$blocks_list = get_contents_from_dir("$config[project_path]/blocks", 2);
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

$global_blocks_list = array();
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
		$block_name = substr($global_block, strpos($global_block, "[SEP]") + 5);
		$global_blocks_list[] = array('block_id' => $block_id, 'block_name' => $block_name);
	}
}

if ($_GET['action'] == 'reset_mem_cache' && $config['memcache_server'] <> '' && class_exists('Memcached'))
{
	$memcache = new Memcached();
	if ($memcache->addServer($config['memcache_server'], $config['memcache_port']))
	{
		$memcache->flush();
	}
	$_SESSION['messages'][] = $lang['website_ui']['success_message_mem_cache_reset'];
	header("Location: $page_name");
	die;
} elseif ($_GET['action'] == 'reset_file_cache')
{
	$clear_dir = $smarty_site->compile_dir;
	if ($clear_dir <> '' && $clear_dir <> $config['project_path'] && is_writable($clear_dir))
	{
		exec("find $clear_dir -type f -delete > /dev/null");
		exec("find $clear_dir -type d -empty ! -path $clear_dir -delete > /dev/null");
	}
	$clear_dir = $smarty_site->cache_dir;
	if ($clear_dir <> '' && $clear_dir <> $config['project_path'] && is_writable($clear_dir))
	{
		exec("find $clear_dir -type f -delete > /dev/null");
		exec("find $clear_dir -type d -empty ! -path $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/albums_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/comments_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/cs_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/dvds_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/feeds_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/models_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/playlists_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/posts_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/videos_info";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/storage";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$clear_dir = "$config[project_path]/admin/data/engine/blocks_state";
	if (is_writable($clear_dir))
	{
		exec("find $clear_dir -delete > /dev/null");
	}
	$_SESSION['messages'][] = $lang['website_ui']['success_message_file_cache_reset'];
	header("Location: $page_name");
	die;
} elseif ($_GET['action'] == 'reset_perf_stats')
{
	$clear_dir = "$config[project_path]/admin/data/analysis/performance";
	if ($clear_dir <> '' && $clear_dir <> $config['project_path'] && is_writable($clear_dir))
	{
		exec("find $clear_dir -type f -delete");
	}
	$_SESSION['messages'][] = $lang['website_ui']['success_message_performance_stats_reset'];
	header("Location: $page_name");
	die;
} elseif ($_GET['action'] == 'duplicate')
{
	$external_id = $_GET['external_id'];

	$validation_errors = validate_page($external_id, '123', '0', true, false, false);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_external_id_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_invalid':
				$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_duplicate':
				$errors[] = get_aa_error('website_ui_page_creation_page_exists', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_duplicate2':
				$errors[] = get_aa_error('website_ui_page_creation_tpl_file_exists', $lang['website_ui']['page_field_page_id']);
				break;
			case 'fs_permissions':
				$errors[] = get_aa_error('filesystem_permission_write', $validation_error['data']);
				break;
			case 'php_file_manual_copy':
				$errors[] = get_aa_error('website_ui_page_creation_php_file', $lang['website_ui']['page_field_page_id'], $external_id, $validation_error['data']);
				break;
		}
	}

	if (!is_array($errors))
	{
		$result = get_site_pages(array($_GET['item_id']));
		if (count($result) > 0)
		{
			$existing_page = $result[0];
		} else
		{
			die;
		}
		if (!is_file("$config[project_path]/$external_id.php"))
		{
			@copy("$config[project_path]/admin/tools/page_template.php", "$config[project_path]/$external_id.php");
		}
		copy("$site_templates_path/$existing_page[external_id].tpl", "$site_templates_path/$external_id.tpl");
		copy_recursive("$site_templates_path/blocks/$existing_page[external_id]", "$site_templates_path/blocks/$external_id");
		copy_recursive("$config[project_path]/admin/data/config/$existing_page[external_id]", "$config[project_path]/admin/data/config/$external_id");
		@unlink("$config[project_path]/admin/data/config/$external_id/deleted.dat");
		@unlink("$config[project_path]/admin/data/config/$external_id/deleted.tpl");

		file_put_contents("$config[project_path]/admin/data/config/$external_id/name.dat", $existing_page["title"] . " (copy)", LOCK_EX);

		$_SESSION['messages'][] = $lang['website_ui']['success_message_page_duplicated'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
{
	$item_id = $_POST['item_id'];
	if ($_POST['action'] == 'change_complete')
	{
		$external_id = $item_id;
		$_POST['external_id'] = $external_id;
	} else
	{
		$external_id = $_POST['external_id'];
	}

	if (validate_field('empty', $_POST['title'], $lang['website_ui']['page_field_display_name']))
	{
		$existing_pages = get_site_pages();
		foreach ($existing_pages as $existing_page)
		{
			if ($existing_page['external_id'] != $external_id && $existing_page['title'] == $_POST['title'])
			{
				$errors[] = get_aa_error('unique_field', $lang['website_ui']['page_field_display_name']);
				break;
			}
		}
	}

	$validation_errors = validate_page($external_id, $_POST['template'], $_POST["cache_time"], $_POST['action'] == 'add_new_complete', $_POST['action'] == 'change_complete', false);
	foreach ($validation_errors as $validation_error)
	{
		if ($validation_error['block_uid'] != '' || $validation_error['global_uid'] != '')
		{
			continue;
		}
		$template_field_name = $lang['website_ui']['page_field_template_code'];
		if ($validation_error['include'] != '')
		{
			$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
			$includes_with_errors[$validation_error['include']] = true;
		}
		switch ($validation_error['type'])
		{
			case 'page_external_id_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_invalid':
				$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_duplicate':
				$errors[] = get_aa_error('website_ui_page_creation_page_exists', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_external_id_duplicate2':
				$errors[] = get_aa_error('website_ui_page_creation_tpl_file_exists', $lang['website_ui']['page_field_page_id']);
				break;
			case 'page_cache_time_invalid':
				$errors[] = get_aa_error('integer_field', $lang['website_ui']['page_field_cache_time']);
				break;
			case 'page_template_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['page_field_template_code']);
				break;
			case 'page_component_external_id_invalid':
				$errors[] = get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']);
				break;
			case 'page_component_unknown':
				$errors[] = get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']);
				break;
			case 'page_component_insert_block':
				$errors[] = get_aa_error('website_ui_invalid_block_insert_component', $template_field_name);
				break;
			case 'global_block_uid_invalid':
				$errors[] = get_aa_error('website_ui_invalid_global_id', $template_field_name, $validation_error['data']);
				break;
			case 'php_file_manual_copy':
				$errors[] = get_aa_error('website_ui_page_creation_php_file', $lang['website_ui']['page_field_page_id'], $external_id, $validation_error['data']);
				break;
		}
	}

	$template_info = get_site_parsed_template($_POST['template']);
	foreach ($template_info['block_inserts'] as $block_insert)
	{
		$block_id = trim($block_insert['block_id']);
		$block_name = trim($block_insert['block_name']);
		$block_name_mod = strtolower(str_replace(" ", "_", $block_name));
		$block_original_name = $block_name;

		$valid_ids = true;
		foreach ($validation_errors as $validation_error)
		{
			if ($validation_error['block_uid'] == "{$block_id}_$block_name_mod")
			{
				switch ($validation_error['type'])
				{
					case 'block_id_invalid':
						$errors[] = get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['page_field_template_code'], $block_id);
						$valid_ids = false;
						break;
					case 'block_state_invalid':
						$errors[] = get_aa_error('website_ui_invalid_block_state', $lang['website_ui']['page_field_template_code'], $block_id);
						$valid_ids = false;
						break;
					case 'block_name_invalid':
						$errors[] = get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['page_field_template_code'], $block_name);
						$valid_ids = false;
						break;
					case 'block_name_duplicate':
						$errors[] = get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['page_field_template_code'], $block_name);
						$valid_ids = false;
						break;
				}
			}
		}

		if ($valid_ids)
		{
			$block_name = strtolower(str_replace(" ", "_", $block_name));
			if ($_POST['action'] == 'change_complete')
			{
				include_once("$config[project_path]/blocks/$block_id/$block_id.php");

				$meta_function = "{$block_id}MetaData";
				if (function_exists($meta_function))
				{
					$need_params = $meta_function();
					$result_params = "";
					foreach ($need_params as $param)
					{
						if ($_POST["is_{$block_name}_$param[name]"] <> 1)
						{
							continue;
						}
						if ($param['type'] == '')
						{
							$result_params .= "&$param[name]";
							continue;
						}

						if (($param['type'] == 'INT_PAIR' && (trim($_POST["{$block_name}_$param[name]1"]) == '' || trim($_POST["{$block_name}_$param[name]2"]) == '')) ||
							($param['type'] <> 'INT_PAIR' && strpos($param['type'], 'SORTING') !== 0 && $param['type'] <> '' && trim($_POST["{$block_name}_$param[name]"]) == '')
						)
						{
							$errors[] = get_aa_error('website_ui_block_param_value_missing', $lang['website_ui']['page_divider_content'], $param['name'], $block_original_name);
							continue;
						}

						switch ($param['type'])
						{
							case 'INT':
								if (trim(intval(trim($_POST["{$block_name}_$param[name]"]))) <> trim($_POST["{$block_name}_$param[name]"]))
								{
									$errors[] = get_aa_error('website_ui_block_param_integer', $lang['website_ui']['page_divider_content'], $param['name'], $block_original_name);
								}
								break;
							case 'INT_LIST':
								$temp2 = explode(",", trim($_POST["{$block_name}_$param[name]"]));
								$temp_value = array();
								foreach ($temp2 as $tmp_param)
								{
									if (trim(intval(trim($tmp_param))) <> trim($tmp_param))
									{
										$errors[] = get_aa_error('website_ui_block_param_integer_list', $lang['website_ui']['page_divider_content'], $param['name'], $block_original_name);
										break;
									}
									$temp_value[] = intval($tmp_param);
								}
								$_POST["{$block_name}_$param[name]"] = implode(",", $temp_value);
								break;
							case 'INT_PAIR':
								if ((trim(intval(trim($_POST["{$block_name}_$param[name]1"]))) <> trim($_POST["{$block_name}_$param[name]1"])) ||
									(trim(intval(trim($_POST["{$block_name}_$param[name]2"]))) <> trim($_POST["{$block_name}_$param[name]2"]))
								)
								{
									$errors[] = get_aa_error('website_ui_block_param_integer_pair', $lang['website_ui']['page_divider_content'], $param['name'], $block_original_name);
								}
								break;
						}

						if ($param['is_required'] == 1 || $_POST["is_{$block_name}_$param[name]"] == 1)
						{
							if (strpos($param['type'], 'SORTING') === 0)
							{
								$param['type'] = "SORTING";
							}

							switch ($param['type'])
							{
								case 'INT_PAIR':
									$result_params .= "&$param[name]=" . trim($_POST["{$block_name}_$param[name]1"]) . "/" . trim($_POST["{$block_name}_$param[name]2"]);
									break;
								case 'SORTING':
									$result_params .= "&$param[name]=" . trim($_POST["{$block_name}_$param[name]1"]) . " " . trim($_POST["{$block_name}_$param[name]2"]);
									break;
								case 'STRING':
									$result_params .= "&$param[name]=" . str_replace('=', '%3D', str_replace('&', '%26', trim($_POST["{$block_name}_$param[name]"])));
									break;
								default:
									$result_params .= "&$param[name]=" . trim($_POST["{$block_name}_$param[name]"]);
									break;
							}
							$result_params = rtrim($result_params, "=");
						}
					}
				}

				$result_params = trim(ltrim($result_params, "&"));
				$_POST["parameters_$block_name"] = $result_params;
			}
		}
	}

	foreach ($validation_errors as $validation_error)
	{
		if ($validation_error['global_uid'] != '')
		{
			if ($validation_error['type'] == 'global_block_uid_invalid')
			{
				$errors[] = get_aa_error('website_ui_invalid_global_id', $lang['website_ui']['page_field_template_code'], $validation_error['data']);
				$valid_ids = false;
			}
		} else
		{
			if ($validation_error['type'] == 'fs_permissions')
			{
				$errors[] = get_aa_error('filesystem_permission_write', $validation_error['data']);
			}
		}
	}

	if (is_array($errors))
	{
		$errors = array_unique($errors);
	}

	if (!is_array($errors))
	{
		$is_page_without_caching = 0;
		$list_blocks = array();
		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = trim($block_insert['block_id']);
			$block_name = trim($block_insert['block_name']);
			if (preg_match($regexp_valid_external_id, $block_id) && preg_match($regexp_valid_block_name, $block_name))
			{
				$block_name = strtolower(str_replace(" ", "_", $block_name));
				if (isset($_POST["cache_time_$block_name"], $_POST["parameters_$block_name"]))
				{
					$temp_bl = explode("||", file_get_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat"));
					file_put_contents("$config[project_path]/admin/data/config/$external_id/{$block_id}_$block_name.dat", intval($_POST["cache_time_$block_name"]) . "||" . $_POST["parameters_$block_name"] . "||" . intval($temp_bl[2]) . "||$block_id" . "||$temp_bl[4]", LOCK_EX);
				}
				$list_blocks[] = "{$block_id}[SEP]$block_name";

				$parameters_temp = explode("&", trim($_POST["parameters_$block_name"]));
				$parameters = array();
				foreach ($parameters_temp as $parameter_temp)
				{
					$temp_bl = explode("=", $parameter_temp);
					$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
				}

				include_once("$config[project_path]/blocks/$block_id/$block_id.php");
				$page_id = $external_id;
				$hash_function = "{$block_id}CacheControl";
				if (function_exists($hash_function))
				{
					$block_hash = $hash_function($parameters);
					if ($block_hash == 'nocache')
					{
						$is_page_without_caching = 1;
					}
				}
			}
		}

		if ($is_page_without_caching == 1)
		{
			$_POST["cache_time"] = 0;
			$_POST["is_compressed"] = 0;
		}

		$list_blocks = implode("|AND|", $list_blocks);
		file_put_contents("$site_templates_path/$external_id.tpl", trim($_POST["template"]), LOCK_EX);

		$file = "$site_templates_path/$external_id.tpl";
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

		if (!is_dir("$config[project_path]/admin/data/config/$external_id"))
		{
			mkdir("$config[project_path]/admin/data/config/$external_id", 0777);
			chmod("$config[project_path]/admin/data/config/$external_id", 0777);
		}
		if (!is_file("$config[project_path]/$external_id.php"))
		{
			@copy("$config[project_path]/admin/tools/page_template.php", "$config[project_path]/$external_id.php");
		}
		file_put_contents("$config[project_path]/admin/data/config/$external_id/config.dat", intval($_POST["cache_time"]) . "||" . intval($_POST["is_compressed"]) . "||" . $list_blocks . "||" . intval($_POST["is_xml"]) . "||" . intval($_POST["is_disabled"]) . "||" . intval($_POST["access_type_id"]) . "||" . trim($_POST["access_type_redirect_url"]) . "||" . trim($_POST["dynamic_http_params"]), LOCK_EX);
		file_put_contents("$config[project_path]/admin/data/config/$external_id/name.dat", $_POST["title"], LOCK_EX);

		if ($_POST['action'] == 'add_new_complete')
		{
			@unlink("$config[project_path]/admin/data/config/$external_id/deleted.dat");
			@unlink("$config[project_path]/admin/data/config/$external_id/deleted.tpl");
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			if (isset($_POST['update_content']))
			{
				$_SESSION['messages'][] = $lang['website_ui']['success_message_content_updated'];
			} else
			{
				$_SESSION['messages'][] = $lang['common']['success_message_modified'];
			}
		}
		if (isset($_POST['update_content']))
		{
			return_ajax_success("$page_name?action=change&amp;item_id=$item_id");
		} else
		{
			return_ajax_success($page_name);
		}
	} else
	{
		return_ajax_errors($errors);
	}
}

if ($_POST['action'] == 'change_block_complete')
{
	$temp = explode("||", $_POST['item_id']);
	$page_id = trim($temp[0]);
	$block_id = trim($temp[1]);
	$block_name = trim($temp[2]);
	$block_original_name = $_POST['item_name'];

	$validation_errors = validate_block($page_id, $block_id, $block_original_name, array(), $_POST['template'], $_POST['cache_time'], false, true, false, false);

	$valid_ids = true;
	foreach ($validation_errors as $validation_error)
	{
		$template_field_name = $lang['website_ui']['block_field_template_code'];
		if ($validation_error['include'] != '')
		{
			$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
			$includes_with_errors[$validation_error['include']] = true;
		}
		switch ($validation_error['type'])
		{
			case 'block_id_invalid':
			case 'block_name_invalid':
			case 'page_external_id_empty':
			case 'page_external_id_invalid':
				$errors[] = get_aa_error('invalid_external_id', $lang['website_ui']['block_field_uid']);
				$valid_ids = false;
				break;
			case 'block_state_invalid':
				$errors[] = get_aa_error('website_ui_invalid_block_state', $lang['website_ui']['block_field_uid'], $block_id);
				$valid_ids = false;
				break;
			case 'block_cache_time_invalid':
				$errors[] = get_aa_error('integer_field', $lang['website_ui']['block_field_cache_time']);
				break;
			case 'block_template_empty':
				$errors[] = get_aa_error('required_field', $lang['website_ui']['block_field_template_code']);
				break;
			case 'block_circular_insert_block':
				$errors[] = get_aa_error('website_ui_invalid_block_insert_block', $template_field_name);
				break;
			case 'block_circular_insert_global':
				$errors[] = get_aa_error('website_ui_invalid_block_insert_block2', $template_field_name);
				break;
			case 'page_component_external_id_invalid':
				$errors[] = get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']);
				break;
			case 'page_component_unknown':
				$errors[] = get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']);
				break;
			case 'fs_permissions':
				$errors[] = get_aa_error('filesystem_permission_write', $validation_error['data']);
				break;
		}
	}

	if (!$valid_ids)
	{
		return_ajax_errors($errors);
	}

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	$meta_function = "{$block_id}MetaData";
	if (function_exists($meta_function))
	{
		$need_params = $meta_function();

		$result_params = "";
		foreach ($need_params as $param)
		{
			if ($param['is_required'] <> 1 && $_POST["is_$param[name]"] <> 1)
			{
				continue;
			}

			if (($param['type'] == 'INT_PAIR' && (trim($_POST["$param[name]1"]) == '' || trim($_POST["$param[name]2"]) == '')) ||
				($param['type'] <> 'INT_PAIR' && strpos($param['type'], 'SORTING') !== 0 && $param['type'] <> '' && trim($_POST["$param[name]"]) == '')
			)
			{
				$errors[] = get_aa_error('website_ui_block_param_value_missing', $lang['website_ui']['block_divider_params'], $param['name'], $block_original_name);
				continue;
			}

			switch ($param['type'])
			{
				case 'INT':
					if (trim(intval(trim($_POST["$param[name]"]))) <> trim($_POST["$param[name]"]))
					{
						$errors[] = get_aa_error('website_ui_block_param_integer', $lang['website_ui']['block_divider_params'], $param['name'], $block_original_name);
					}
					break;
				case 'INT_LIST':
					$temp2 = explode(",", trim($_POST["$param[name]"]));
					$temp_value = array();
					foreach ($temp2 as $tmp_param)
					{
						if (trim(intval(trim($tmp_param))) <> trim($tmp_param))
						{
							$errors[] = get_aa_error('website_ui_block_param_integer_list', $lang['website_ui']['block_divider_params'], $param['name'], $block_original_name);
							break;
						}
						$temp_value[] = intval($tmp_param);
					}
					$_POST["$param[name]"] = implode(",", $temp_value);
					break;
				case 'INT_PAIR':
					if ((trim(intval(trim($_POST["$param[name]1"]))) <> trim($_POST["$param[name]1"])) ||
						(trim(intval(trim($_POST["$param[name]2"]))) <> trim($_POST["$param[name]2"]))
					)
					{
						$errors[] = get_aa_error('website_ui_block_param_integer_pair', $lang['website_ui']['block_divider_params'], $param['name'], $block_original_name);
					}
					break;
			}

			if ($param['is_required'] == 1 || $_POST["is_$param[name]"] == 1)
			{
				if (strpos($param['type'], 'SORTING') === 0)
				{
					$param['type'] = "SORTING";
				}

				switch ($param['type'])
				{
					case 'INT_PAIR':
						$result_params .= "&$param[name]=" . trim($_POST["$param[name]1"]) . "/" . trim($_POST["$param[name]2"]);
						break;
					case 'SORTING':
						$result_params .= "&$param[name]=" . trim($_POST["$param[name]1"]) . " " . trim($_POST["$param[name]2"]);
						break;
					case 'STRING':
						$result_params .= "&$param[name]=" . str_replace('=', '%3D', str_replace('&', '%26', trim($_POST["$param[name]"])));
						break;
					default:
						$result_params .= "&$param[name]=" . trim($_POST["$param[name]"]);
						break;
				}
				$result_params = rtrim($result_params, "=");
			}
		}
	}
	$result_params = trim(ltrim($result_params, "&"));

	if (is_array($errors))
	{
		$errors = array_unique($errors);
	}

	if (!is_array($errors))
	{
		if (trim($_POST["template"]) != trim(@file_get_contents("$site_templates_path/blocks/$page_id/{$block_id}_$block_name.tpl")))
		{
			file_put_contents("$site_templates_path/blocks/$page_id/{$block_id}_$block_name.tpl", trim($_POST["template"]), LOCK_EX);
		}

		file_put_contents("$config[project_path]/admin/data/config/$page_id/{$block_id}_$block_name.dat", intval($_POST["cache_time"]) . "||" . $result_params . "||" . intval($_POST["is_not_cached_for_members"]) . "||$block_id" . "||$_POST[dynamic_http_params]", LOCK_EX);

		$file = "$site_templates_path/blocks/$page_id/{$block_id}_$block_name.tpl";
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

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		if ($page_id == '$global')
		{
			return_ajax_success('project_pages_global.php');
		} else
		{
			return_ajax_success($page_name);
		}
	} else
	{
		return_ajax_errors($errors);
	}
}

if (isset($_POST['save_caching']))
{
	$data = get_site_pages();
	foreach ($data as $v)
	{
		if (!preg_match($regexp_valid_external_id, $v['external_id']))
		{
			continue;
		}
		$is_page_without_caching = 0;
		$template_info = $templates_data["$v[external_id].tpl"];
		if (isset($template_info))
		{
			foreach ($template_info['block_inserts'] as $block_insert)
			{
				$block_id = trim($block_insert['block_id']);
				$block_name = trim($block_insert['block_name']);

				if (preg_match($regexp_valid_external_id, $block_id) && preg_match($regexp_valid_block_name, $block_name))
				{
					$block_name = strtolower(str_replace(" ", "_", $block_name));
					if (isset($_POST["cache_time_$v[external_id]_$block_name"]))
					{
						if (!is_writable("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat"))
						{
							$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat"));
						}
						if (!is_array($errors))
						{
							$file_data = @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat");
							$temp_bl = explode("||", $file_data);
							$cache_time = intval($temp_bl[0]);

							$parameters_temp = explode("&", trim($temp_bl[1]));
							$parameters = array();
							foreach ($parameters_temp as $parameter_temp)
							{
								$temp_bl = explode("=", $parameter_temp);
								$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
							}

							include_once("$config[project_path]/blocks/$block_id/$block_id.php");
							$page_id = $v['external_id'];
							$hash_function = "{$block_id}CacheControl";
							if (function_exists($hash_function))
							{
								$block_hash = $hash_function($parameters);
								if ($block_hash == 'nocache')
								{
									$is_page_without_caching = 1;
									$_POST["cache_time_$v[external_id]_$block_name"] = 0;
								}
							}

							$temp_bl = explode("||", $file_data);
							$temp_bl[0] = intval($_POST["cache_time_$v[external_id]_$block_name"]);
							$file_data = implode("||", $temp_bl);

							file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat", $file_data, LOCK_EX);
						}
					}
				}
			}
		}
		if (isset($_POST["cache_time_$v[external_id]"]))
		{
			if (!is_writable("$config[project_path]/admin/data/config/$v[external_id]/config.dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$v[external_id]/config.dat"));
			}
			if (!is_array($errors))
			{
				$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat"));
				if ($is_page_without_caching == 1)
				{
					$_POST["cache_time_$v[external_id]"] = 0;
					$_POST["is_compressed_$v[external_id]"] = 0;
				}
				$temp[0] = intval($_POST["cache_time_$v[external_id]"]);
				$temp[1] = intval($_POST["is_compressed_$v[external_id]"]);

				file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat", implode("||", $temp), LOCK_EX);
			}
		}
	}
	if (is_array($errors))
	{
		return_ajax_errors($errors);
	} else
	{
		$_SESSION['messages'][] = $lang['website_ui']['success_message_cache_updated'];
		return_ajax_success($page_name);
	}
}

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['save_caching']) && count($_REQUEST['row_select']) > 0)
{
	$row_select = $_REQUEST['row_select'];
	if ($_REQUEST['batch_action'] == 'delete')
	{
		$row_select = get_site_pages($row_select);
		foreach ($row_select as $res)
		{
			if (!is_writable("$config[project_path]/admin/data/config/$res[external_id]"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$res[external_id]"));
			}
		}
		if (!is_array($errors))
		{
			foreach ($row_select as $res)
			{
				@rename("$site_templates_path/$res[external_id].tpl", "$config[project_path]/admin/data/config/$res[external_id]/deleted.tpl");
				@unlink("$config[project_path]/$res[external_id].php");

				file_put_contents("$config[project_path]/admin/data/config/$res[external_id]/deleted.dat", '1', LOCK_EX);
			}
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		}
	} elseif ($_REQUEST['batch_action'] == 'restore_page')
	{
		if (!is_writable("$site_templates_path"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path"));
		}
		foreach ($row_select as $external_id)
		{
			if (!preg_match($regexp_valid_external_id, $external_id))
			{
				continue;
			}
			if (is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat") && is_file("$config[project_path]/admin/data/config/$external_id/deleted.tpl"))
			{
				if (is_file("$site_templates_path/$external_id.tpl"))
				{
					$errors[] = get_aa_error('website_ui_page_creation_tpl_file_exists', $external_id);
				} elseif (!is_file("$config[project_path]/$external_id.php") && !is_writable("$config[project_path]"))
				{
					$errors[] = get_aa_error('website_ui_page_creation_php_file', $external_id, $external_id, str_replace("//", "/", "$config[project_path]/$external_id.php"));
				}
			}
		}
		if (!is_array($errors))
		{
			foreach ($row_select as $external_id)
			{
				if (!preg_match($regexp_valid_external_id, $external_id))
				{
					continue;
				}
				if (is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat") && is_file("$config[project_path]/admin/data/config/$external_id/deleted.tpl"))
				{
					if (!is_file("$config[project_path]/$external_id.php"))
					{
						@copy("$config[project_path]/admin/tools/page_template.php", "$config[project_path]/$external_id.php");
					}
					if (!is_file("$site_templates_path/$external_id.tpl"))
					{
						@rename("$config[project_path]/admin/data/config/$external_id/deleted.tpl", "$site_templates_path/$external_id.tpl");
					}
					@unlink("$config[project_path]/admin/data/config/$external_id/deleted.dat");
				}
			}
			$_SESSION['messages'][] = $lang['website_ui']['success_message_page_restored'];
		}
	} elseif ($_REQUEST['batch_action'] == 'wipeout_page')
	{
		if (!is_writable("$site_templates_path/blocks"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks"));
		}
		if (!is_writable("$config[project_path]/admin/data/config"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config"));
		}
		foreach ($row_select as $external_id)
		{
			if (!preg_match($regexp_valid_external_id, $external_id))
			{
				continue;
			}
			if (is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat"))
			{
				if (is_dir("$site_templates_path/blocks/$external_id") && !is_writable("$site_templates_path/blocks/$external_id"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks/$external_id"));
				}
				if (is_dir("$config[project_path]/admin/data/config/$external_id") && !is_writable("$config[project_path]/admin/data/config/$external_id"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id"));
				}
			}
		}
		if (!is_array($errors))
		{
			foreach ($row_select as $external_id)
			{
				if (!preg_match($regexp_valid_external_id, $external_id))
				{
					continue;
				}
				if (is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat"))
				{
					$temp = get_contents_from_dir("$config[project_path]/admin/data/config/$external_id", 1);
					foreach ($temp as $file)
					{
						@unlink("$config[project_path]/admin/data/config/$external_id/$file");
					}
					@rmdir("$config[project_path]/admin/data/config/$external_id");
					$temp = get_contents_from_dir("$site_templates_path/blocks/$external_id", 1);
					foreach ($temp as $file)
					{
						@unlink("$site_templates_path/blocks/$external_id/$file");
					}
					@rmdir("$site_templates_path/blocks/$external_id");
				}
			}
			$_SESSION['messages'][] = $lang['website_ui']['success_message_page_wiped_out'];
		}
	} elseif ($_REQUEST['batch_action'] == 'wipeout_block')
	{
		foreach ($row_select as $temp_id)
		{
			$temp = explode('||', $temp_id);
			$external_id = trim($temp[0]);
			$block_uid = trim($temp[1]);
			if ($external_id == '' || $block_uid == '')
			{
				continue;
			}
			if (!preg_match($regexp_valid_external_id, $external_id) || !preg_match($regexp_valid_external_id, $block_uid))
			{
				continue;
			}
			if (!is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat"))
			{
				if (is_dir("$site_templates_path/blocks/$external_id") && !is_writable("$site_templates_path/blocks/$external_id"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$site_templates_path/blocks/$external_id"));
				}
				if (is_dir("$config[project_path]/admin/data/config/$external_id") && !is_writable("$config[project_path]/admin/data/config/$external_id"))
				{
					$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$config[project_path]/admin/data/config/$external_id"));
				}
			}
		}
		if (!is_array($errors))
		{
			foreach ($row_select as $temp_id)
			{
				$temp = explode('||', $temp_id);
				$external_id = trim($temp[0]);
				$block_uid = trim($temp[1]);
				if ($external_id == '' || $block_uid == '')
				{
					continue;
				}
				if (!preg_match($regexp_valid_external_id, $external_id) || !preg_match($regexp_valid_external_id, $block_uid))
				{
					continue;
				}
				if (!is_file("$config[project_path]/admin/data/config/$external_id/deleted.dat"))
				{
					@unlink("$config[project_path]/admin/data/config/$external_id/$block_uid.dat");
					@unlink("$site_templates_path/blocks/$external_id/$block_uid.tpl");
				}
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

//make all need files, mark as for delete old files.
$good_page_list = array('$global');
$good_block_list = array();
$data = get_site_pages();
foreach ($data as $v)
{
	if (!preg_match($regexp_valid_external_id, $v['external_id']))
	{
		continue;
	}
	$good_page_list[] = $v['external_id'];
	if (!is_file("$site_templates_path/$v[external_id].tpl"))
	{
		continue;
	}

	if (!is_dir("$config[project_path]/admin/data/config/$v[external_id]"))
	{
		mkdir("$config[project_path]/admin/data/config/$v[external_id]", 0777);
		chmod("$config[project_path]/admin/data/config/$v[external_id]", 0777);
	}
	if (!is_dir("$site_templates_path/blocks/$v[external_id]"))
	{
		mkdir("$site_templates_path/blocks/$v[external_id]", 0777);
		chmod("$site_templates_path/blocks/$v[external_id]", 0777);
	}
	if (!is_file("$config[project_path]/admin/data/config/$v[external_id]/config.dat"))
	{
		file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat", "0||0||||0", LOCK_EX);
	}
	if (!is_file("$config[project_path]/admin/data/config/$v[external_id]/name.dat"))
	{
		file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/name.dat", $v['title'], LOCK_EX);
	}
	if (is_file("$config[project_path]/admin/data/config/$v[external_id]/deleted.dat"))
	{
		unlink("$config[project_path]/admin/data/config/$v[external_id]/deleted.dat");
	}
	if (is_file("$config[project_path]/admin/data/config/$v[external_id]/deleted.tpl"))
	{
		unlink("$config[project_path]/admin/data/config/$v[external_id]/deleted.tpl");
	}

	$page_config = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat"));
	if (trim($page_config[2]) == '')
	{
		$page_config_blocks_list = array();
	} else
	{
		$page_config_blocks_list = explode("|AND|", trim($page_config[2]));
	}
	$template_info = $templates_data["$v[external_id].tpl"];
	if (isset($template_info))
	{
		$blocks_name_list = array();
		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = trim($block_insert['block_id']);
			$block_name = trim($block_insert['block_name']);
			$block_display_name = $block_name;

			//check for error
			if (!preg_match($regexp_valid_external_id, $block_id) || !preg_match($regexp_valid_block_name, $block_name))
			{
				continue;
			}
			if (!in_array($block_id, $blocks_list))
			{
				continue;
			}
			if (in_array(strtolower(str_replace(" ", "_", $block_name)), $blocks_name_list))
			{
				continue;
			}
			$blocks_name_list[] = strtolower(str_replace(" ", "_", $block_name));

			//make all need files
			$block_name = strtolower(str_replace(" ", "_", $block_name));
			$good_block_list[] = "$v[external_id]||$block_id||$block_name";

			if (!is_file("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat"))
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

				file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name.dat", "0||$list_params||0||$block_id||", LOCK_EX);

				if (count($page_config) > 0 && !in_array("{$block_id}[SEP]{$block_name}", $page_config_blocks_list))
				{
					$page_config_blocks_list[] = "{$block_id}[SEP]{$block_name}";
					$page_config[2] = implode("|AND|", $page_config_blocks_list);

					file_put_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat", implode("||", $page_config), LOCK_EX);
				}
			}
			if (!is_file("$site_templates_path/blocks/$v[external_id]/{$block_id}_$block_name.tpl"))
			{
				file_put_contents("$site_templates_path/blocks/$v[external_id]/{$block_id}_$block_name.tpl", "<div class=\"$block_id\">\n$block_display_name\n</div>", LOCK_EX);

				$file = "$site_templates_path/blocks/$v[external_id]/{$block_id}_$block_name.tpl";
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
}

foreach ($global_blocks_list as $global_block)
{
	$good_block_list[] = "\$global||$global_block[block_id]||$global_block[block_name]";
}

$list_config_folders = get_contents_from_dir("$config[project_path]/admin/data/config/", 2);
$deleted_pages = array();
$deleted_blocks = array();
$deleted_blocks_count = 0;
foreach ($list_config_folders as $res)
{
	if (is_file("$config[project_path]/admin/data/config/$res/deleted.dat") && is_file("$config[project_path]/admin/data/config/$res/deleted.tpl"))
	{
		$deleted_page_name = @file_get_contents("$config[project_path]/admin/data/config/$res/name.dat");
		if ($deleted_page_name == '')
		{
			$deleted_page_name = $res;
		}
		$deleted_pages[] = array('external_id' => $res, 'title' => $deleted_page_name);
	} elseif (!is_file("$config[project_path]/admin/data/config/$res/deleted.dat"))
	{
		$pages = get_site_pages(array($res));
		if (count($pages) > 0)
		{
			$page_info = $pages[0];
			$files = get_contents_from_dir("$config[project_path]/admin/data/config/$page_info[external_id]", 1);
			$page_deleted_blocks = array();
			foreach ($files as $file)
			{
				if ($file == "config.dat")
				{
					continue;
				}
				if ($file == "name.dat")
				{
					continue;
				}
				if ($file == "deleted.dat")
				{
					continue;
				}
				if ($file == "deleted.tpl")
				{
					continue;
				}

				$is_delete_block = 1;
				foreach ($good_block_list as $k => $v)
				{
					$temp = explode("||", $v);
					if ($temp[0] == $page_info['external_id'])
					{
						if ("$temp[1]_$temp[2].dat" == $file)
						{
							$is_delete_block = 0;
						}
					}
				}
				if ($is_delete_block == 1)
				{
					$block_uid = str_replace(".dat", "", $file);
					if (is_file("$site_templates_path/blocks/$page_info[external_id]/$block_uid.tpl"))
					{
						$temp = explode("||", file_get_contents("$config[project_path]/admin/data/config/$page_info[external_id]/$file"));
						if ($temp[3] != '' && in_array($temp[3], $blocks_list))
						{
							$page_deleted_blocks[] = array('block_id' => $temp[3], 'block_name_mod' => substr($block_uid, strlen($temp[3]) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($temp[3]) + 1))));
							$deleted_blocks_count++;
						} else
						{
							foreach ($blocks_list as $block_type_id)
							{
								if (strpos($block_uid, $block_type_id) === 0)
								{
									$page_deleted_blocks[] = array('block_id' => $block_type_id, 'block_name_mod' => substr($block_uid, strlen($block_type_id) + 1), 'block_name' => ucwords(str_replace("_", " ", substr($block_uid, strlen($block_type_id) + 1))));
									$deleted_blocks_count++;
								}
							}
						}
					}
				}
			}
			if (count($page_deleted_blocks) > 0)
			{
				$deleted_blocks[] = array('external_id' => $page_info['external_id'], 'title' => $page_info['title'], 'blocks' => $page_deleted_blocks);
			}
		}
	}
}

if ($_GET['action'] == 'change' && $_GET['item_id'] != '')
{
	$result = get_site_pages(array($_GET['item_id']));
	if (count($result) > 0)
	{
		$_POST = $result[0];
	} else
	{
		header("Location: $page_name");
		die;
	}

	$validation_errors = validate_page($_POST['external_id'], '', '', false, false, true);

	$valid_id = true;
	$includes_with_errors = array();
	foreach ($validation_errors as $validation_error)
	{
		if ($validation_error['block_uid'] != '' || $validation_error['global_uid'] != '')
		{
			continue;
		}
		$template_field_name = $lang['website_ui']['page_component_field_template_code'];
		if ($validation_error['include'] != '')
		{
			$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
			$includes_with_errors[$validation_error['include']] = true;
		}
		switch ($validation_error['type'])
		{
			case 'page_external_id_empty':
			case 'page_external_id_invalid':
				$valid_id = false;
				break;
			case 'page_state_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_page_state_invalid', $lang['website_ui']['page_field_template_code']));
				break;
			case 'page_template_empty':
				$_POST['errors'][] = bb_code_process(get_aa_error('required_field', $lang['website_ui']['page_field_template_code']));
				break;
			case 'page_template_smarty_get_usage':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_page_smarty_get_usage', $template_field_name, $validation_error['data']));
				break;
			case 'page_template_smarty_request_usage':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_page_smarty_request_usage', $template_field_name, $validation_error['data']));
				break;
			case 'page_component_external_id_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']));
				break;
			case 'page_component_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']));
				break;
			case 'advertising_spot_unknown':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $template_field_name, $validation_error['data']));
				break;
			case 'page_component_insert_block':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_component', $template_field_name));
				break;
			case 'global_block_uid_invalid':
				$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_global_id', $template_field_name, $validation_error['data']));
				$has_block_error = true;
				break;
		}
	}

	if (!$valid_id)
	{
		header("Location: $page_name");
		die;
	}

	$_POST['blocks'] = array();

	$template_info = $templates_data["$_POST[external_id].tpl"];
	if (isset($template_info))
	{
		$_POST['template'] = $template_info['template_code'];

		$file = "$site_templates_path/$_POST[external_id].tpl";
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

		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = $block_insert['block_id'];
			$block_name = $block_insert['block_name'];
			$block_name_mod = strtolower(str_replace(" ", "_", $block_name));

			$valid_ids = true;
			$has_block_error = false;
			$has_block_critical_error = false;
			$has_block_caching_error = false;
			foreach ($validation_errors as $validation_error)
			{
				if ($validation_error['block_uid'] == "{$block_id}_$block_name_mod")
				{
					switch ($validation_error['type'])
					{
						case 'block_id_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_id', $lang['website_ui']['page_field_template_code'], $block_id));
							$valid_ids = false;
							break;
						case 'block_state_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_state', $lang['website_ui']['page_field_template_code'], $block_id));
							$valid_ids = false;
							break;
						case 'block_name_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_name', $lang['website_ui']['page_field_template_code'], $block_name));
							$valid_ids = false;
							break;
						case 'block_name_duplicate':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_blocks_unique_names', $lang['website_ui']['page_field_template_code'], $block_name));
							$valid_ids = false;
							break;
						case 'block_template_empty':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_empty_template', $block_name));
							$has_block_error = true;
							break;
						case 'block_circular_insert_block':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block', $block_name));
							$has_block_error = true;
							break;
						case 'block_circular_insert_global':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block2', $block_name));
							$has_block_error = true;
							break;
						case 'page_component_external_id_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'page_component_unknown':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'advertising_spot_unknown':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'file_missing':
							$has_block_error = true;
							$has_block_critical_error = true;
							break;
						case 'var_from_duplicate':
							if ($config['is_pagination_3.0'] == 'true')
							{
								$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_var_from_equal_names', $block_name));
								$has_block_error = true;
							}
							break;
						case 'block_template_smarty_session_usage':
						case 'block_template_smarty_session_status_usage':
						case 'block_template_smarty_get_usage':
						case 'block_template_smarty_request_usage':
							$has_block_error = true;
							$has_block_caching_error = true;
							break;
					}
				}
			}

			if ($valid_ids)
			{
				$block_info = array();
				if ($has_block_caching_error)
				{
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_caching_issues', $block_name));
				}

				if (is_file("$config[project_path]/blocks/$block_id/langs/english.php"))
				{
					include_once("$config[project_path]/blocks/$block_id/langs/english.php");
				}
				if (($_SESSION['userdata']['lang'] != 'english') && (is_file("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php")))
				{
					include_once("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php");
				}

				$file_data = @file_get_contents("$config[project_path]/admin/data/config/$_POST[external_id]/{$block_id}_$block_name_mod.dat");
				$temp_bl = explode("||", $file_data);
				$block_info['cache_time'] = intval($temp_bl[0]);
				$block_info['is_not_cached_for_members'] = intval($temp_bl[2]);
				$parameters_temp = explode("&", trim($temp_bl[1]));
				$parameters = array();
				foreach ($parameters_temp as $parameter_temp)
				{
					$temp_bl = explode("=", $parameter_temp);
					$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
				}

				include_once("$config[project_path]/blocks/$block_id/$block_id.php");
				$page_id = $_POST['external_id'];
				$hash_function = "{$block_id}CacheControl";
				if (function_exists($hash_function))
				{
					$block_hash = $hash_function($parameters);
					if ($block_hash == 'nocache')
					{
						$block_info['no_cache'] = 1;
					}
				}

				$metadata_function = "{$block_id}MetaData";
				if (function_exists($metadata_function))
				{
					$params = $metadata_function();
				} else
				{
					$params = array();
				}

				foreach ($params as $k => $param)
				{
					$param_type = $param['type'];
					if (strpos($param['type'], "SORTING") === 0)
					{
						$param_type = "SORTING";
					}
					if (strpos($param['type'], "CHOICE") === 0)
					{
						$param_type = "CHOICE";
					}

					if ($param_type == 'INT')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int'];
					} elseif ($param_type == 'INT_LIST')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int_list'];
					} elseif ($param_type == 'INT_PAIR')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int_pair'];
					} elseif ($param_type == 'STRING')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_string'];
					} elseif ($param_type == '')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_boolean'];
					} elseif ($param_type == 'LIST_BLOCK')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_list_block'];
					} elseif ($param_type == 'SORTING')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_sorting'];
					} elseif ($param_type == 'CHOICE')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_choice'];
					}

					$params[$k]['desc'] = $lang[$block_id]['params'][$param['name']];

					if (array_key_exists($param['name'], $parameters))
					{
						$params[$k]['is_enabled'] = 1;
						$params[$k]['value'] = $parameters[$param['name']];
						if ($param_type == 'STRING')
						{
							$params[$k]['value'] = str_replace(array('%26', '%3D'), array('&', '='), $params[$k]['value']);
						}
					} else
					{
						unset($params[$k]);
						continue;
					}

					if ($param_type == 'SORTING')
					{
						if (strpos($params[$k]['value'], "asc") !== false)
						{
							$params[$k]['value_modifier'] = "asc";
						} else
						{
							$params[$k]['value_modifier'] = "desc";
						}
						$params[$k]['value'] = trim(str_replace(" desc", "", str_replace("asc", "", $params[$k]['value'])));
					}

					if (in_array($param_type, array('SORTING', 'CHOICE')))
					{
						$param_type_values = explode(",", trim(rtrim(str_replace("SORTING[", "", str_replace("CHOICE[", "", $param['type'])), "]")));
						foreach ($param_type_values as $temp_value)
						{
							$params[$k]['values'][$temp_value] = $lang[$block_id]['values'][$param['name']][$temp_value];
						}
						if ($param_type == 'SORTING')
						{
							$params[$k]['values']['rand()'] = $lang[$block_id]['values'][$param['name']]['rand()'];
						}
					} elseif ($param_type == 'LIST_BLOCK')
					{
						foreach ($template_info['block_inserts'] as $list_block_insert)
						{
							if ($block_name == $list_block_insert['block_name'])
							{
								break;
							}
							$metadata_function_temp = "{$list_block_insert['block_id']}MetaData";
							if (function_exists($metadata_function_temp))
							{
								$params_temp = $metadata_function_temp();
							} else
							{
								$params_temp = array();
							}
							$is_find_from = 0;
							foreach ($params_temp as $param_temp)
							{
								if ($param_temp['name'] == 'var_from')
								{
									$is_find_from = 1;
									break;
								}
							}
							if ($is_find_from == 1)
							{
								$params[$k]['values']["{$list_block_insert['block_id']}|{$list_block_insert['block_name']}"] = $list_block_insert['block_name'];
							}
						}
					} elseif ($param_type == 'INT_PAIR')
					{
						$temp_bl = explode("/", $params[$k]['value']);
						settype($params[$k]['value'], "array");
						$params[$k]['value'][0] = trim($temp_bl[0]);
						$params[$k]['value'][1] = trim($temp_bl[1]);
					}

					$params[$k]['type'] = $param_type;
				}

				$block_info['params'] = $params;
				$block_info['block_id'] = $block_id;
				$block_info['block_name'] = $block_name;
				$block_info['block_name_dir'] = $block_name_mod;
				$block_info['errors'] = ($has_block_error ? 1 : 0);
				$block_info['critical_errors'] = ($has_block_critical_error ? 1 : 0);
				$_POST['blocks'][] = $block_info;
			}
		}

		$global_blocks_on_page = array();
		foreach ($template_info['global_block_inserts'] as $global_block_insert)
		{
			$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
		}

		$included_pages = get_site_includes_recursively($template_info);
		foreach ($included_pages as $included_page_info)
		{
			foreach ($included_page_info['global_block_inserts'] as $global_block_insert)
			{
				$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
			}
		}

		foreach ($global_blocks_on_page as $global_block_insert)
		{
			$global_id = $global_block_insert['global_uid'];

			$valid_id = false;
			foreach ($global_blocks_list as $global_block)
			{
				if ($global_id == "$global_block[block_id]_$global_block[block_name]")
				{
					$block_id = $global_block['block_id'];
					$block_name = ucwords(str_replace('_', ' ', $global_block['block_name']));
					$block_name_mod = $global_block['block_name'];
					$valid_id = true;
					break;
				}
			}

			$has_block_error = false;
			$has_block_critical_error = false;
			$has_block_caching_error = false;
			foreach ($validation_errors as $validation_error)
			{
				if ($validation_error['global_uid'] == $global_id)
				{
					switch ($validation_error['type'])
					{
						case 'global_block_uid_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_global_id', $lang['website_ui']['page_field_template_code'], $global_id));
							$has_block_error = true;
							break;
						case 'block_template_empty':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_empty_template', $block_name));
							$has_block_error = true;
							break;
						case 'block_circular_insert_block':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block', $block_name));
							$has_block_error = true;
							break;
						case 'block_circular_insert_global':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block2', $block_name));
							$has_block_error = true;
							break;
						case 'page_component_external_id_invalid':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'page_component_unknown':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'advertising_spot_unknown':
							$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $block_name, $validation_error['data']));
							$has_block_error = true;
							break;
						case 'file_missing':
							$has_block_error = true;
							$has_block_critical_error = true;
							break;
						case 'block_template_smarty_session_usage':
						case 'block_template_smarty_session_status_usage':
						case 'block_template_smarty_get_usage':
						case 'block_template_smarty_request_usage':
							$has_block_error = true;
							$has_block_caching_error = true;
							break;
					}
				}
			}

			if ($valid_id)
			{
				$block_info = array();
				if ($has_block_caching_error)
				{
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_caching_issues', $block_name));
				}
				if (is_file("$config[project_path]/blocks/$block_id/langs/english.php"))
				{
					include_once("$config[project_path]/blocks/$block_id/langs/english.php");
				}
				if (($_SESSION['userdata']['lang'] != 'english') && (is_file("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php")))
				{
					include_once("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php");
				}

				$file_data = @file_get_contents("$config[project_path]/admin/data/config/\$global/$global_id.dat");
				$temp_bl = explode("||", $file_data);
				$block_info['cache_time'] = intval($temp_bl[0]);
				$block_info['is_not_cached_for_members'] = intval($temp_bl[2]);

				$parameters_temp = explode("&", trim($temp_bl[1]));
				$parameters = array();
				foreach ($parameters_temp as $parameter_temp)
				{
					$temp_bl = explode("=", $parameter_temp);
					$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
				}

				include_once("$config[project_path]/blocks/$block_id/$block_id.php");
				$page_id = $_POST['external_id'];

				$metadata_function = "{$block_id}MetaData";
				if (function_exists($metadata_function))
				{
					$params = $metadata_function();
				} else
				{
					$params = array();
				}

				foreach ($params as $k => $param)
				{
					$param_type = $param['type'];
					if (strpos($param['type'], "SORTING") === 0)
					{
						$param_type = "SORTING";
					}
					if (strpos($param['type'], "CHOICE") === 0)
					{
						$param_type = "CHOICE";
					}

					if ($param_type == 'INT')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int'];
					} elseif ($param_type == 'INT_LIST')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int_list'];
					} elseif ($param_type == 'INT_PAIR')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_int_pair'];
					} elseif ($param_type == 'STRING')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_string'];
					} elseif ($param_type == '')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_boolean'];
					} elseif ($param_type == 'LIST_BLOCK')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_list_block'];
					} elseif ($param_type == 'SORTING')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_sorting'];
					} elseif ($param_type == 'CHOICE')
					{
						$params[$k]['type_name'] = $lang['website_ui']['common_type_choice'];
					}

					$params[$k]['desc'] = $lang[$block_id]['params'][$param['name']];

					if (array_key_exists($param['name'], $parameters))
					{
						$params[$k]['is_enabled'] = 1;
						$params[$k]['value'] = $parameters[$param['name']];
					} else
					{
						unset($params[$k]);
						continue;
					}

					if ($param_type == 'SORTING')
					{
						if (strpos($params[$k]['value'], "asc") !== false)
						{
							$params[$k]['value_modifier'] = "asc";
						} else
						{
							$params[$k]['value_modifier'] = "desc";
						}
						$params[$k]['value'] = trim(str_replace(" desc", "", str_replace("asc", "", $params[$k]['value'])));
					}

					if (in_array($param_type, array('SORTING', 'CHOICE')))
					{
						$param_type_values = explode(",", trim(rtrim(str_replace("SORTING[", "", str_replace("CHOICE[", "", $param['type'])), "]")));
						foreach ($param_type_values as $temp_value)
						{
							$params[$k]['values'][$temp_value] = $lang[$block_id]['values'][$param['name']][$temp_value];
						}
						if ($param_type == 'SORTING')
						{
							$params[$k]['values']['rand()'] = $lang[$block_id]['values'][$param['name']]['rand()'];
						}
					} elseif ($param_type == 'LIST_BLOCK')
					{
						foreach ($template_info['block_inserts'] as $list_block_insert)
						{
							if ($block_name == $list_block_insert['block_name'])
							{
								break;
							}
							$metadata_function_temp = "{$list_block_insert['block_id']}MetaData";
							if (function_exists($metadata_function_temp))
							{
								$params_temp = $metadata_function_temp();
							} else
							{
								$params_temp = array();
							}
							$is_find_from = 0;
							foreach ($params_temp as $param_temp)
							{
								if ($param_temp['name'] == 'var_from')
								{
									$is_find_from = 1;
									break;
								}
							}
							if ($is_find_from == 1)
							{
								$params[$k]['values']["{$list_block_insert['block_id']}|{$list_block_insert['block_name']}"] = $list_block_insert['block_name'];
							}
						}
					} elseif ($param_type == 'INT_PAIR')
					{
						$temp_bl = explode("/", $params[$k]['value']);
						settype($params[$k]['value'], "array");
						$params[$k]['value'][0] = trim($temp_bl[0]);
						$params[$k]['value'][1] = trim($temp_bl[1]);
					}

					$params[$k]['type'] = $param_type;
				}

				$block_info['params'] = $params;
				$block_info['is_global'] = 1;
				$block_info['block_id'] = $block_id;
				$block_info['block_name'] = ucwords(str_replace('_', ' ', $block_name));
				$block_info['block_name_dir'] = $block_name_mod;
				$block_info['errors'] = ($has_block_error ? 1 : 0);
				$block_info['critical_errors'] = ($has_block_critical_error ? 1 : 0);
				$_POST['blocks'][] = $block_info;
			}
		}
	}

	$file_data = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$_POST[external_id]/config.dat"));
	$_POST['cache_time'] = intval($file_data[0]);
	$_POST['is_compressed'] = intval($file_data[1]);
	$_POST['is_xml'] = intval($file_data[3]);
	$_POST['is_disabled'] = intval($file_data[4]);
	$_POST['access_type_id'] = intval($file_data[5]);
	$_POST['access_type_redirect_url'] = trim($file_data[6]);
	$_POST['dynamic_http_params'] = trim($file_data[7]);

	$htaccess_rows = explode("\n", @file_get_contents("$config[project_path]/.htaccess"));
	foreach ($htaccess_rows as $row)
	{
		$row = trim($row);
		if (strpos($row, 'RewriteRule') === 0 && (strpos($row, "/$_POST[external_id].php") !== false || strpos($row, " $_POST[external_id].php") !== false))
		{
			$_POST['htaccess_rules'] .= "$row\n";
		}
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
				if ($validation_error['global_uid'] == '')
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
}

if ($_GET['action'] == 'change_block' && $_GET['item_id'] <> '')
{
	$temp = explode("||", $_GET['item_id']);
	$page_id = trim($temp[0]);
	$block_id = trim($temp[1]);
	$block_name = trim($temp[2]);
	$block_original_name = trim($_GET['item_name']);

	if ($page_id != '$global')
	{
		$validation_errors = validate_page($page_id, '', '', false, false, true);
	} else
	{
		$validation_errors = validate_block($page_id, $block_id, $block_original_name, array(), '', '', false, false, false, true);
		foreach ($validation_errors as $k => $v)
		{
			$validation_errors[$k]['block_uid'] = "{$block_id}_$block_name";
			$validation_errors[$k]['block_name'] = $block_original_name;
		}
	}

	$valid_ids = true;
	$includes_with_errors = array();
	foreach ($validation_errors as $validation_error)
	{
		if ($validation_error['block_uid'] == "{$block_id}_$block_name")
		{
			$template_field_name = $lang['website_ui']['block_field_template_code'];
			if ($validation_error['include'] != '')
			{
				$template_field_name .= ' -> ' . str_replace("%1%", $validation_error['include'], $lang['website_ui']['page_component_edit']);
				$includes_with_errors[$validation_error['include']] = true;
			}
			switch ($validation_error['type'])
			{
				case 'page_external_id_empty':
				case 'page_external_id_invalid':
				case 'block_id_invalid':
				case 'block_name_invalid':
				case 'block_state_invalid':
					$valid_ids = false;
					break;
				case 'block_template_empty':
					$_POST['errors'][] = bb_code_process(get_aa_error('required_field', $lang['website_ui']['block_field_template_code']));
					break;
				case 'block_circular_insert_block':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block', $template_field_name));
					break;
				case 'block_circular_insert_global':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_block_insert_block2', $template_field_name));
					break;
				case 'block_template_smarty_session_usage':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_smarty_session_usage', $template_field_name));
					break;
				case 'block_template_smarty_session_status_usage':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_smarty_session_status_usage', $template_field_name));
					break;
				case 'block_template_smarty_get_usage':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_smarty_get_usage', $template_field_name, $validation_error['data']));
					break;
				case 'block_template_smarty_request_usage':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_block_smarty_request_usage', $template_field_name, $validation_error['data']));
					break;
				case 'page_component_external_id_invalid':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component_id', $template_field_name, $validation_error['data']));
					break;
				case 'page_component_unknown':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_page_component', $template_field_name, $validation_error['data']));
					break;
				case 'advertising_spot_unknown':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_invalid_advertising_spot', $template_field_name, $validation_error['data']));
					break;
				case 'fs_permissions':
					$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', $validation_error['data']));
					break;
				case 'file_missing':
					$_POST['errors'][] = bb_code_process(get_aa_error('website_ui_missing_required_file', $validation_error['data']));
					break;
			}
		}
	}
	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}

	if (!$valid_ids)
	{
		header("Location: $page_name");
		die;
	}

	$template_info = $templates_data["blocks/$page_id/{$block_id}_$block_name.tpl"];
	if (isset($template_info))
	{
		$_POST['template'] = $template_info['template_code'];

		$file = "$site_templates_path/blocks/$page_id/{$block_id}_$block_name.tpl";
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

	if (is_file("$config[project_path]/blocks/$block_id/langs/english.php"))
	{
		include_once("$config[project_path]/blocks/$block_id/langs/english.php");
	}
	if (($_SESSION['userdata']['lang'] != 'english') && (is_file("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php")))
	{
		include_once("$config[project_path]/blocks/$block_id/langs/" . $_SESSION['userdata']['lang'] . ".php");
	}

	$file_data = @file_get_contents("$config[project_path]/admin/data/config/$page_id/{$block_id}_$block_name.dat");
	$temp = explode("||", $file_data);
	$_POST['cache_time'] = intval($temp[0]);
	$parameters_temp = explode("&", trim($temp[1]));
	$_POST['is_not_cached_for_members'] = intval($temp[2]);
	$_POST['dynamic_http_params'] = trim($temp[4]);
	$parameters = array();
	foreach ($parameters_temp as $parameter_temp)
	{
		$temp = explode("=", $parameter_temp);
		$parameters[trim($temp[0])] = trim($temp[1]);
	}

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	$hash_function = "{$block_id}CacheControl";
	if (function_exists($hash_function))
	{
		$block_hash = $hash_function($parameters);
		if ($block_hash == 'nocache')
		{
			$_POST['no_cache'] = 1;
		}
	}

	$metadata_function = "{$block_id}MetaData";
	if (function_exists($metadata_function))
	{
		$params = $metadata_function();
	} else
	{
		$params = array();
	}

	foreach ($params as $k => $param)
	{
		$param_type = $param['type'];
		if (strpos($param['type'], "SORTING") === 0)
		{
			$param_type = "SORTING";
		}
		if (strpos($param['type'], "CHOICE") === 0)
		{
			$param_type = "CHOICE";
		}

		if ($param_type == 'INT')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_int'];
		} elseif ($param_type == 'INT_LIST')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_int_list'];
		} elseif ($param_type == 'INT_PAIR')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_int_pair'];
		} elseif ($param_type == 'STRING')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_string'];
		} elseif ($param_type == '')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_boolean'];
		} elseif ($param_type == 'LIST_BLOCK')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_list_block'];
		} elseif ($param_type == 'SORTING')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_sorting'];
		} elseif ($param_type == 'CHOICE')
		{
			$params[$k]['type_name'] = $lang['website_ui']['common_type_choice'];
		}

		$params[$k]['desc'] = $lang[$block_id]['params'][$param['name']];
		if ($param['group'] != '')
		{
			$params[$k]['group_desc'] = $lang[$block_id]['groups'][$param['group']];
		}

		if (array_key_exists($param['name'], $parameters))
		{
			$params[$k]['is_enabled'] = 1;
			$params[$k]['value'] = $parameters[$param['name']];
			if ($param_type == 'STRING')
			{
				$params[$k]['value'] = str_replace(array('%26', '%3D'), array('&', '='), $params[$k]['value']);
			}
		} else
		{
			$params[$k]['is_enabled'] = 0;
			$params[$k]['value'] = $params[$k]['default_value'];
		}

		if ($param_type == 'SORTING')
		{
			if (strpos($params[$k]['value'], "asc") !== false)
			{
				$params[$k]['value_modifier'] = "asc";
			} else
			{
				$params[$k]['value_modifier'] = "desc";
			}
			$params[$k]['value'] = trim(str_replace(" desc", "", str_replace("asc", "", $params[$k]['value'])));
		}

		if (in_array($param_type, array('SORTING', 'CHOICE')))
		{
			$param_type_values = explode(",", trim(rtrim(str_replace("SORTING[", "", str_replace("CHOICE[", "", $param['type'])), "]")));
			foreach ($param_type_values as $temp_value)
			{
				$params[$k]['values'][$temp_value] = $lang[$block_id]['values'][$param['name']][$temp_value];
			}
			if ($param_type == 'SORTING')
			{
				$params[$k]['values']['rand()'] = $lang[$block_id]['values'][$param['name']]['rand()'];
			}
		} elseif ($param_type == 'LIST_BLOCK')
		{
			$page_template_info = $templates_data["$page_id.tpl"];
			if (isset($page_template_info))
			{
				foreach ($page_template_info['block_inserts'] as $block_insert)
				{
					if ($block_name == strtolower(str_replace(" ", "_", $block_insert['block_name'])))
					{
						break;
					}
					$metadata_function_temp = "{$block_insert['block_id']}MetaData";
					if (function_exists($metadata_function_temp))
					{
						$params_temp = $metadata_function_temp();
					} else
					{
						$params_temp = array();
					}
					$is_find_from = 0;
					foreach ($params_temp as $param_temp)
					{
						if ($param_temp['name'] == 'var_from')
						{
							$is_find_from = 1;
							break;
						}
					}
					if ($is_find_from == 1)
					{
						$params[$k]['values']["{$block_insert['block_id']}|{$block_insert['block_name']}"] = $block_insert['block_name'];
					}
				}
			}
		} elseif ($param_type == 'INT_PAIR')
		{
			$temp = explode("/", $params[$k]['value']);
			settype($params[$k]['value'], "array");
			$params[$k]['value'][0] = trim($temp[0]);
			$params[$k]['value'][1] = trim($temp[1]);
		}

		$params[$k]['type'] = $param_type;
	}
	$_POST['params'] = $params;

	$_POST['description'] = $lang[$block_id]['block_desc'] . "[kt|br][kt|br]" . $lang[$block_id]['block_examples'];
	if ($page_id <> '$global')
	{
		$_POST['description'] = str_replace('page.php', "$page_id.php", $_POST['description']);
	}
	$_POST['default_template'] = @file_get_contents("$config[project_path]/blocks/$block_id/$block_id.tpl");

	if ($_GET['item_name'] <> '')
	{
		$_POST['block_name'] = $_GET['item_name'];
	} else
	{
		$_POST['block_name'] = $block_name;
	}
	$_POST['block_id'] = $block_id;
	$_POST['block_uid'] = "{$block_id}_$block_name";

	if ($page_id <> '$global')
	{
		$result = get_site_pages(array($page_id));
		if (count($result) > 0)
		{
			$_POST['page_info'] = $result[0];
		}
	} else
	{
		$_POST['page_info']['is_global'] = 'true';
	}
}

$data = get_site_pages();

$total_requests = 0;
$page_sort_ids = array();
$page_sort_requests = array();

foreach ($data as $k => $v)
{
	$validation_errors = validate_page($v['external_id'], '', '', false, false, true);
	foreach ($validation_errors as $validation_error)
	{
		switch ($validation_error['type'])
		{
			case 'page_state_invalid':
			case 'page_template_empty':
			case 'page_template_smarty_get_usage':
			case 'page_template_smarty_request_usage':
			case 'page_component_external_id_invalid':
			case 'page_component_insert_block':
			case 'page_component_unknown':
			case 'advertising_spot_unknown':
			case 'block_id_invalid':
			case 'block_state_invalid':
			case 'block_name_invalid':
			case 'block_name_duplicate':
			case 'block_circular_insert_block':
			case 'block_circular_insert_global':
			case 'global_block_uid_invalid':
			case 'file_missing':
			case 'dir_missing':
				$data[$k]['errors'] = 1;
				break;
			case 'var_from_duplicate':
				if ($config['is_pagination_3.0'] == 'true')
				{
					$data[$k]['errors'] = 1;
				}
				break;
			case 'fs_permissions':
				if ($validation_error['global_uid'] == '' && $validation_error['block_uid'] == '')
				{
					$data[$k]['warnings'] = 1;
				}
				break;
		}
	}

	$page_performance_data = @unserialize(@file_get_contents("$config[project_path]/admin/data/analysis/performance/{$v['external_id']}.dat"));
	if (is_array($page_performance_data))
	{
		$page_requests = intval($page_performance_data['cached_requests_count']) + intval($page_performance_data['uncached_requests_count']);
		if ($page_requests > 1000)
		{
			$data[$k]['total_requests'] = floor($page_requests / 1000);
			$data[$k]['total_requests_needs_k'] = 1;
		} else
		{
			$data[$k]['total_requests'] = $page_requests;
			if ($data[$k]['total_requests'] > 2)
			{
				$data[$k]['total_requests'] -= 2;
			}
		}
		$data[$k]['cached_avg_time_s'] = $page_performance_data['cached_avg_time_s'];
		$data[$k]['uncached_avg_time_s'] = $page_performance_data['uncached_avg_time_s'];
		$data[$k]['cache_pc'] = max(0, 100 * floatval($page_performance_data['cached_requests_count'] - 1) / floatval($page_performance_data['cached_requests_count'] + $page_performance_data['uncached_requests_count'] - 2));
		$data[$k]['max_memory'] = $page_performance_data['max_memory'];

		$total_requests += $page_requests;
		$page_sort_ids[] = $v['external_id'];
		$page_sort_requests[] = $page_requests;
	}

	$template_info = $templates_data["$v[external_id].tpl"];
	if (isset($template_info))
	{
		foreach ($template_info['block_inserts'] as $block_insert)
		{
			$block_id = $block_insert['block_id'];
			$block_name = $block_insert['block_name'];
			$block_name_mod = strtolower(str_replace(" ", "_", $block_name));

			$valid_ids = true;
			$has_block_error = false;
			$has_block_warning = false;
			foreach ($validation_errors as $validation_error)
			{
				if ($validation_error['block_uid'] == "{$block_id}_$block_name_mod")
				{
					switch ($validation_error['type'])
					{
						case 'block_id_invalid':
						case 'block_state_invalid':
						case 'block_name_invalid':
						case 'block_name_duplicate':
							$has_block_error = true;
							$valid_ids = false;
							break;
						case 'block_circular_insert_block':
						case 'block_circular_insert_global':
						case 'page_component_external_id_invalid':
						case 'page_component_unknown':
						case 'advertising_spot_unknown':
						case 'file_missing':
							$has_block_error = true;
							break;
						case 'block_template_smarty_session_usage':
						case 'block_template_smarty_session_status_usage':
						case 'block_template_smarty_get_usage':
						case 'block_template_smarty_request_usage':
							if ($validation_error['include'] == '')
							{
								$has_block_error = true;
							} else
							{
								$has_block_warning = true;
							}
							break;
						case 'block_template_empty':
						case 'fs_permissions':
							$has_block_warning = true;
							break;
					}
				}
			}

			if ($valid_ids)
			{
				$temp_arr = array();
				$temp_arr['block_id'] = $block_id;
				$temp_arr['block_name'] = $block_name;
				$temp_arr['block_name_dir'] = $block_name_mod;
				if ($has_block_warning == 1)
				{
					$temp_arr['warnings'] = 1;
				}
				if ($has_block_error == 1)
				{
					$temp_arr['errors'] = 1;
				}

				$file_data = @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/{$block_id}_$block_name_mod.dat");
				$temp_bl = explode("||", $file_data);
				$temp_arr['cache_time'] = intval($temp_bl[0]);

				$parameters_temp = explode("&", trim($temp_bl[1]));
				$parameters = array();
				foreach ($parameters_temp as $parameter_temp)
				{
					$temp_bl = explode("=", $parameter_temp);
					$parameters[trim($temp_bl[0])] = trim($temp_bl[1]);
				}

				include_once("$config[project_path]/blocks/$block_id/$block_id.php");
				$page_id = $v['external_id'];
				$hash_function = "{$block_id}CacheControl";
				if (function_exists($hash_function))
				{
					$block_hash = $hash_function($parameters);
					if ($block_hash == 'nocache')
					{
						$temp_arr['no_cache'] = 1;
					}
				}

				$performance_data = @unserialize(@file_get_contents("$config[project_path]/admin/data/analysis/performance/{$v['external_id']}_{$block_id}_$block_name_mod.dat"));
				if (is_array($performance_data))
				{
					$temp_arr['cached_avg_time_s'] = $performance_data['cached_avg_time_s'];
					$temp_arr['uncached_avg_time_s'] = $performance_data['uncached_avg_time_s'];
					$temp_arr['cache_pc'] = max(0, 100 * floatval($page_performance_data['cached_requests_count'] + $performance_data['cached_requests_count'] - 2) / floatval($page_performance_data['cached_requests_count'] + $performance_data['cached_requests_count'] + $performance_data['uncached_requests_count'] - 3));
					$temp_arr['max_memory'] = $performance_data['max_memory'];
					if ($performance_data['cached_avg_time_s'] >= 0.8 || $performance_data['uncached_avg_time_s'] >= 0.8)
					{
						$temp_arr['is_slow'] = 1;
						$data[$k]['is_slow'] = 1;
					}
				}

				$data[$k]['blocks'][] = $temp_arr;
			}
		}

		$global_blocks_on_page = array();
		foreach ($template_info['global_block_inserts'] as $global_block_insert)
		{
			$global_block_insert['is_from_include'] = 1;
			$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
		}

		$included_pages = get_site_includes_recursively($template_info);
		foreach ($included_pages as $included_page_info)
		{
			foreach ($included_page_info['global_block_inserts'] as $global_block_insert)
			{
				$global_block_insert['is_from_include'] = 1;
				$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
			}
		}

		foreach ($global_blocks_on_page as $global_block_insert)
		{
			$global_id = $global_block_insert['global_uid'];

			$valid_id = false;
			foreach ($global_blocks_list as $global_block)
			{
				if ($global_id == "$global_block[block_id]_$global_block[block_name]")
				{
					$block_id = $global_block['block_id'];
					$block_name = ucwords(str_replace('_', ' ', $global_block['block_name']));
					$block_name_mod = $global_block['block_name'];
					$valid_id = true;
					break;
				}
			}

			$has_block_error = false;
			$has_block_warning = false;
			foreach ($validation_errors as $validation_error)
			{
				if ($validation_error['global_uid'] == $global_id)
				{
					switch ($validation_error['type'])
					{
						case 'global_block_uid_invalid':
						case 'block_circular_insert_block':
						case 'block_circular_insert_global':
						case 'page_component_external_id_invalid':
						case 'page_component_unknown':
						case 'advertising_spot_unknown':
						case 'file_missing':
							$has_block_error = true;
							break;
						case 'block_template_smarty_session_usage':
						case 'block_template_smarty_session_status_usage':
						case 'block_template_smarty_get_usage':
						case 'block_template_smarty_request_usage':
							if ($validation_error['include'] == '')
							{
								$has_block_error = true;
							} else
							{
								$has_block_warning = true;
							}
							break;
						case 'var_from_duplicate':
							if ($config['is_pagination_3.0'] == 'true')
							{
								$has_block_error = true;
							}
							break;
						case 'block_template_empty':
						case 'fs_permissions':
							$has_block_warning = true;
							break;
					}
				}
			}

			if ($valid_id)
			{
				$temp_arr = array();
				$temp_arr['is_global'] = 1;
				$temp_arr['block_id'] = "$global_block[block_id]";
				$temp_arr['block_name'] = ucwords(str_replace('_', ' ', "$global_block[block_name]"));
				$temp_arr['block_name_dir'] = "$global_block[block_name]";
				if ($has_block_warning == 1)
				{
					$temp_arr['warnings'] = 1;
				}
				if ($has_block_error == 1)
				{
					$temp_arr['errors'] = 1;
				}

				$file_data = @file_get_contents("$config[project_path]/admin/data/config/\$global/$global_id.dat");
				$temp_bl = explode("||", $file_data);
				$temp_arr['cache_time'] = intval($temp_bl[0]);

				$performance_data = @unserialize(@file_get_contents("$config[project_path]/admin/data/analysis/performance/\$global_$global_id.dat"));
				if (is_array($performance_data))
				{
					$temp_arr['cached_avg_time_s'] = $performance_data['cached_avg_time_s'];
					$temp_arr['uncached_avg_time_s'] = $performance_data['uncached_avg_time_s'];
					if ($page_performance_data['cached_requests_count'] + $performance_data['cached_requests_count'] + $performance_data['uncached_requests_count'] - 3 == 0)
					{
						$temp_arr['cache_pc'] = 0;
					} else
					{
						$temp_arr['cache_pc'] = max(0, 100 * floatval($page_performance_data['cached_requests_count'] + $performance_data['cached_requests_count'] - 2) / floatval($page_performance_data['cached_requests_count'] + $performance_data['cached_requests_count'] + $performance_data['uncached_requests_count'] - 3));
					}
					$temp_arr['max_memory'] = $performance_data['max_memory'];
					if ($performance_data['cached_avg_time_s'] >= 0.8 || $performance_data['uncached_avg_time_s'] >= 0.8)
					{
						$temp_arr['is_slow'] = 1;
						$data[$k]['is_slow'] = 1;
					}
				}
				if ($global_block_insert['is_from_include'] == 1)
				{
					$temp_arr['is_from_include'] = 1;
				}
				$data[$k]['blocks'][] = $temp_arr;
			}
		}
	}

	$file_data = explode("||", @file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat"));
	$data[$k]['cache_time'] = intval($file_data[0]);
	$data[$k]['is_compressed'] = intval($file_data[1]);
	$data[$k]['is_xml'] = intval($file_data[3]);
	$data[$k]['is_disabled'] = intval($file_data[4]);
	$data[$k]['access_type_id'] = intval($file_data[5]);

	if ($_SESSION['save'][$page_name]['se_text'] != '')
	{
		if (!mb_contains($data[$k]['title'], $_SESSION['save'][$page_name]['se_text']) && !mb_contains($data[$k]['external_id'], $_SESSION['save'][$page_name]['se_text']))
		{
			$has_matching_block = false;
			if (isset($data[$k]['blocks']))
			{
				foreach ($data[$k]['blocks'] as $k2 => $block_on_page)
				{
					if (mb_contains($block_on_page['block_name'], $_SESSION['save'][$page_name]['se_text']) || mb_contains($block_on_page['block_id'], $_SESSION['save'][$page_name]['se_text']))
					{
						$has_matching_block = true;
					} else
					{
						unset($data[$k]['blocks'][$k2]);
					}
				}
			}
			if (!$has_matching_block)
			{
				unset($data[$k]);
			}
		}
	}
}

array_multisort($page_sort_requests, SORT_NUMERIC, SORT_DESC, $page_sort_ids);
$page_sort_limit = 0;
foreach ($page_sort_ids as $k => $v)
{
	$page_sort_limit += $page_sort_requests[$k];
	if ($page_sort_limit > 0.9 * $total_requests)
	{
		unset($page_sort_ids[$k]);
	}
}

foreach ($data as $k => $v)
{
	switch ($_SESSION['save'][$page_name]['se_show_id'])
	{
		case 'active':
			$table_filtered = 1;
			if ($data[$k]['is_disabled'] == 1)
			{
				unset($data[$k]);
			}
			break;
		case 'disabled':
			$table_filtered = 1;
			if ($data[$k]['is_disabled'] == 0)
			{
				unset($data[$k]);
			}
			break;
		case 'slow':
			$table_filtered = 1;
			if ($data[$k]['is_slow'] == 0)
			{
				unset($data[$k]);
			}
			break;
		case 'popular':
			$table_filtered = 1;
			if (!in_array($data[$k]['external_id'], $page_sort_ids))
			{
				unset($data[$k]);
			}
			break;
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');

if (in_array($_REQUEST['action'], array('change', 'change_block')))
{
	$smarty->assign('supports_popups', 1);
}
$smarty->assign('deleted_pages', $deleted_pages);
$smarty->assign('deleted_blocks', $deleted_blocks);
$smarty->assign('deleted_blocks_count', $deleted_blocks_count);
$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_filtered', $table_filtered);
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
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['website_ui']['page_edit']));
} elseif ($_REQUEST['action'] == 'change_block')
{
	if ($_POST['page_info']['is_global'] == 'true')
	{
		$smarty->assign('page_title', str_replace("%1%", $_POST['block_name'], $lang['website_ui']['block_global_edit']));
	} else
	{
		$smarty->assign('page_title', str_replace("%2%", $_POST['page_info']['title'], str_replace("%1%", $_POST['block_name'], $lang['website_ui']['block_edit'])));
	}
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['website_ui']['page_add']);
} elseif ($_REQUEST['action'] == 'restore_pages')
{
	$smarty->assign('page_title', str_replace("%1%", count($deleted_pages), $lang['website_ui']['submenu_option_restore_pages']));
} elseif ($_REQUEST['action'] == 'restore_blocks')
{
	$smarty->assign('page_title', str_replace("%1%", $deleted_blocks_count, $lang['website_ui']['submenu_option_restore_blocks']));
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_pages_list']);
}

$smarty->display("layout.tpl");
