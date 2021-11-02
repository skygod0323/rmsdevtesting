<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
if (!isset($config))
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}
$old_request = $_REQUEST;
require_once 'include/setup_smarty.php';
require_once 'include/setup_smarty_site.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/database_selectors.php';
require_once 'include/list_countries.php';
$_REQUEST = $old_request;

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

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"), ['allowed_classes' => false]);

$result = get_site_pages(array($page_id));
if (count($result) > 0)
{
	$page_info = $result[0];
} else
{
	die;
}

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = str_replace(array('debug=true', '&&', '?&'), array('', '&', '?'), $request_uri);
$request_uri = trim($request_uri, '?&');

$status_code = '200';

$http_params = array();
foreach ($_REQUEST as $k => $v)
{
	if ($k == 'debug')
	{
		continue;
	}
	$param = array('name' => $k, 'value' => $v);
	$http_params[] = $param;
}

$htaccess_rules = array();
$htaccess_rows = explode("\n", @file_get_contents("$config[project_path]/.htaccess"));
foreach ($htaccess_rows as $row)
{
	$row = trim($row);
	if (strpos($row, 'RewriteRule') === 0 && (strpos($row, "/$page_id.php") !== false || strpos($row, " $page_id.php") !== false))
	{
		$row_pattern = explode(' ', $row);
		$test_request_uri = $request_uri;
		if (strpos($test_request_uri, '/') === 0)
		{
			$test_request_uri = substr($test_request_uri, 1);
		}

		$row_is_current = 0;
		if ($row_pattern[1] != '' && preg_match("($row_pattern[1])u", $test_request_uri))
		{
			$row_is_current = 1;
		}
		$htaccess_rules[] = array('rule' => $row, 'is_current' => $row_is_current);
	}
}

$session_values = array();
if (isset($_SESSION['user_id']))
{
	$session_values['user_id'] = $_SESSION['user_id'];

	if (isset($_SESSION['display_name']))
	{
		$session_values['display_name'] = $_SESSION['display_name'];
	}
	if (isset($_SESSION['last_login_date']))
	{
		$session_values['last_login_date'] = $_SESSION['last_login_date'];
	}
	if (isset($_SESSION['added_date']))
	{
		$session_values['added_date'] = $_SESSION['added_date'];
	}
	if (isset($_SESSION['avatar']))
	{
		$session_values['avatar'] = $_SESSION['avatar'];
	}
	if (isset($_SESSION['avatar_url']))
	{
		$session_values['avatar_url'] = $_SESSION['avatar_url'];
	}
	if (isset($_SESSION['cover']))
	{
		$session_values['cover'] = $_SESSION['cover'];
	}
	if (isset($_SESSION['cover_url']))
	{
		$session_values['cover_url'] = $_SESSION['cover_url'];
	}
	if (isset($_SESSION['status_id']))
	{
		$session_values['status_id'] = $_SESSION['status_id'];
	}
	if (isset($_SESSION['username']))
	{
		$session_values['username'] = $_SESSION['username'];
	}
	if (isset($_SESSION['birth_date']))
	{
		$session_values['birth_date'] = $_SESSION['birth_date'];
	}
	if (isset($_SESSION['age']))
	{
		$session_values['age'] = $_SESSION['age'];
	}
	if (isset($_SESSION['gender_id']))
	{
		$session_values['gender_id'] = $_SESSION['gender_id'];
	}
	if (isset($_SESSION['content_source_group_id']))
	{
		$session_values['content_source_group_id'] = $_SESSION['content_source_group_id'];
	}
	if (isset($_SESSION['is_trusted']))
	{
		$session_values['is_trusted'] = $_SESSION['is_trusted'];
	}
	if (isset($_SESSION['tokens_available']))
	{
		$session_values['tokens_available'] = $_SESSION['tokens_available'];
	}
	if (isset($_SESSION['unread_messages']))
	{
		$session_values['unread_messages'] = $_SESSION['unread_messages'];
	}
	if (isset($_SESSION['unread_invites']))
	{
		$session_values['unread_invites'] = $_SESSION['unread_invites'];
	}
	if (isset($_SESSION['unread_non_invites']))
	{
		$session_values['unread_non_invites'] = $_SESSION['unread_non_invites'];
	}
	if (isset($_SESSION['paid_access_hours_left']))
	{
		$session_values['paid_access_hours_left'] = $_SESSION['paid_access_hours_left'];
	}
	if (isset($_SESSION['paid_access_is_unlimited']))
	{
		$session_values['paid_access_is_unlimited'] = $_SESSION['paid_access_is_unlimited'];
	}
	if (isset($_SESSION['external_guid']))
	{
		$session_values['external_guid'] = $_SESSION['external_guid'];
	}
	if (isset($_SESSION['external_package_id']))
	{
		$session_values['external_package_id'] = $_SESSION['external_package_id'];
	}
	if (isset($_SESSION['playlists']) && count($_SESSION['playlists']) > 0)
	{
		$session_values['playlists'] = $_SESSION['playlists'];
	}
	if (isset($_SESSION['playlists_amount']) > 0)
	{
		$session_values['playlists_amount'] = $_SESSION['playlists_amount'];
	}
	if (isset($_SESSION['content_purchased']) && count($_SESSION['content_purchased']) > 0)
	{
		$session_values['content_purchased'] = $_SESSION['content_purchased'];
	}
	if (isset($_SESSION['content_purchased_amount']) > 0)
	{
		$session_values['content_purchased_amount'] = $_SESSION['content_purchased_amount'];
	}
	if (isset($_SESSION['favourite_videos_summary']) && count($_SESSION['favourite_videos_summary']) > 0)
	{
		$session_values['favourite_videos_summary'] = $_SESSION['favourite_videos_summary'];
	}
	if (isset($_SESSION['favourite_videos_amount']) > 0)
	{
		$session_values['favourite_videos_amount'] = $_SESSION['favourite_videos_amount'];
	}
	if (isset($_SESSION['favourite_albums_summary']) && count($_SESSION['favourite_albums_summary']) > 0)
	{
		$session_values['favourite_albums_summary'] = $_SESSION['favourite_albums_summary'];
	}
	if (isset($_SESSION['favourite_albums_amount']) > 0)
	{
		$session_values['favourite_albums_amount'] = $_SESSION['favourite_albums_amount'];
	}
	if (isset($_SESSION['subscriptions_amount']) > 0)
	{
		$session_values['subscriptions_amount'] = $_SESSION['subscriptions_amount'];
	}
	if (isset($_SESSION['user_info']))
	{
		$session_values['user_info'] = $_SESSION['user_info'];
	}
}

$variable_flat = array();
variable_flat_recursive("session", $session_values, '$smarty.session', 0);
$session_values = $variable_flat;


if (is_file("$config[project_path]/langs/default.php"))
{
	$admin_lang = $lang;
	unset($lang);
	include_once "$config[project_path]/langs/default.php";

	$temp_lang['$lang'] = $lang;
	$variable_flat = array();
	variable_flat_recursive("localization", $temp_lang, '', 0);
	$localization = $variable_flat;

	$lang = $admin_lang;
}

$runtime_params = array();
$temp = unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
foreach ($temp as $param)
{
	$var = trim($param['name']);
	if (isset($_SESSION['runtime_params'][$var]))
	{
		$runtime_params[$var] = $_SESSION['runtime_params'][$var];
	}
}

$smarty_site = new mysmarty_site();
$site_templates_path = $smarty_site->template_dir;

$templates_data = get_site_parsed_templates();
$template_info = $templates_data["$page_id.tpl"];

if (isset($template_info))
{
	$page_includes = get_site_includes_recursively($template_info);

	$blocks = array();
	$storage = array();
	foreach ($template_info['block_inserts'] as $block_insert)
	{
		$block_id = trim($block_insert['block_id']);
		$block_name = trim($block_insert['block_name']);
		$block_name_mod = strtolower(str_replace(" ", "_", $block_name));
		$block_uid = "{$block_id}_{$block_name_mod}";

		if (!is_file("$config[project_path]/admin/data/config/$page_id/$block_uid.dat"))
		{
			continue;
		}
		$temp = explode("||", file_get_contents("$config[project_path]/admin/data/config/$page_id/$block_uid.dat"));
		$config_params = array();
		if (trim($temp[1]) <> '')
		{
			$temp_params = explode("&", $temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param = explode("=", $temp_param, 2);
				$config_params[trim($temp_param[0])] = trim($temp_param[1]);
			}
		}

		include_once "$config[project_path]/blocks/$block_id/$block_id.php";
		$smarty = new mysmarty_site();
		$smarty->assign_by_ref("config", $config);
		$smarty->assign_by_ref("storage", $storage);

		$start_memory = memory_get_peak_usage();
		$start_time = microtime(true);

		$show_block_function = "{$block_id}Show";
		$show_result = $show_block_function($config_params, $block_uid);

		$memory_usage = memory_get_peak_usage() - $start_memory;
		$time_usage = microtime(true) - $start_time;

		$block_storage = array($block_uid => $storage[$block_uid]);

		$variable_flat = array();
		variable_flat_recursive("{$block_uid}_storage", $block_storage, '', 0);
		$block_storage = $variable_flat;

		$variable_flat = array();
		variable_flat_recursive("{$block_uid}_template", $smarty->get_template_vars(), '', 0);
		$block_template_vars = $variable_flat;

		if (count($_POST) > 0 && $_POST['block_uid'] == $block_uid)
		{
			$variable_flat = array();
			variable_flat_recursive("{$block_uid}_post", $_POST, 'smarty.post', 0);
			$block_template_vars = array_merge($block_template_vars, $variable_flat);
		}

		$block_template_info = $templates_data["blocks/$page_id/$block_uid.tpl"];
		if (isset($block_template_info))
		{
			$block_includes = get_site_includes_recursively($block_template_info);
		}

		foreach ($config_params as $k2 => $v2)
		{
			if (strpos($k2, 'var_') === 0 && isset($_REQUEST[$v2]))
			{
				$config_params[$k2] = "$v2 ($_REQUEST[$v2])";
			}
		}

		$block = array();
		$block['block_id'] = $block_id;
		$block['block_name'] = $block_name;
		$block['block_name_mod'] = $block_name_mod;
		$block['block_uid'] = $block_uid;
		$block['params'] = $config_params;
		$block['storage'] = $storage[$block_uid];
		$block['block_includes'] = $block_includes;
		$block['storage'] = $block_storage;
		$block['template_vars'] = $block_template_vars;
		if ($show_result == 'status_404')
		{
			$block['status_code'] = '404';
		} elseif (strpos($show_result, 'status_302:') === 0)
		{
			$block['status_code'] = '302 (' . substr($show_result, 11) . ')';
		} elseif (strpos($show_result, 'status_301:') === 0)
		{
			$block['status_code'] = '301 (' . substr($show_result, 11) . ')';
		}
		if ($block['status_code'] != '' && $status_code == '200')
		{
			$status_code = $block['status_code'];
		}
		$block['memory_usage'] = $memory_usage;
		$block['time_usage'] = $time_usage;
		$blocks[] = $block;
	}

	$global_blocks_on_page = array();
	foreach ($template_info['global_block_inserts'] as $global_block_insert)
	{
		$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
	}

	foreach ($page_includes as $included_page_info)
	{
		foreach ($included_page_info['global_block_inserts'] as $global_block_insert)
		{
			$global_blocks_on_page[$global_block_insert['global_uid']] = $global_block_insert;
		}
	}

	$old_page_id = $page_id;
	$page_id = '$global';
	$storage = array();
	foreach ($global_blocks_on_page as $global_block_insert)
	{
		$global_id = trim($global_block_insert['global_uid']);
		$is_valid_global_block = false;
		foreach ($global_blocks_list as $global_block)
		{
			if ($global_id == "$global_block[block_id]_$global_block[block_name]")
			{
				$is_valid_global_block = true;
				break;
			}
		}
		if (!$is_valid_global_block)
		{
			continue;
		}
		$block_id = "$global_block[block_id]";
		$block_name = ucwords(str_replace('_', ' ', "$global_block[block_name]"));
		$block_uid = $global_id;

		if (!is_file("$config[project_path]/admin/data/config/\$global/$block_uid.dat"))
		{
			continue;
		}
		$temp = explode("||", file_get_contents("$config[project_path]/admin/data/config/\$global/$block_uid.dat"));
		$config_params = array();
		if (trim($temp[1]) <> '')
		{
			$temp_params = explode("&", $temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param = explode("=", $temp_param, 2);
				$config_params[trim($temp_param[0])] = trim($temp_param[1]);
			}
		}

		if (in_array($block_id, array('logon', 'signup')) && intval($_SESSION['user_id']) > 0)
		{
			continue;
		}
		if (in_array($block_id, array('upgrade', 'member_profile_edit', 'member_profile_delete')) && intval($_SESSION['user_id']) < 1)
		{
			continue;
		}

		include_once "$config[project_path]/blocks/$block_id/$block_id.php";
		$smarty = new mysmarty_site();
		$smarty->assign_by_ref("config", $config);
		$smarty->assign_by_ref("storage", $storage);

		$start_memory = memory_get_peak_usage();
		$start_time = microtime(true);

		$show_block_function = "{$block_id}Show";
		$show_result = $show_block_function($config_params, $block_uid);

		$memory_usage = memory_get_peak_usage() - $start_memory;
		$time_usage = microtime(true) - $start_time;

		$block_storage = array($block_uid => $storage[$block_uid]);

		$variable_flat = array();
		variable_flat_recursive("{$block_uid}_storage_g", $block_storage, '', 0);
		$block_storage = $variable_flat;

		$variable_flat = array();
		variable_flat_recursive("{$block_uid}_template_g", $smarty->get_template_vars(), '', 0);
		$block_template_vars = $variable_flat;

		if (count($_POST) > 0 && $_POST['block_uid'] == $block_uid)
		{
			$variable_flat = array();
			variable_flat_recursive("{$block_uid}_post_g", $_POST, 'smarty.post', 0);
			$block_template_vars = array_merge($block_template_vars, $variable_flat);
		}

		$block_template_info = $templates_data["blocks/$page_id/$block_uid.tpl"];
		if (isset($block_template_info))
		{
			$block_includes = get_site_includes_recursively($block_template_info);
		}

		foreach ($config_params as $k2 => $v2)
		{
			if (strpos($k2, 'var_') === 0 && isset($_REQUEST[$v2]))
			{
				$config_params[$k2] = "$v2 ($_REQUEST[$v2])";
			}
		}

		$block = array();
		$block['is_global'] = 1;
		$block['block_id'] = $block_id;
		$block['block_name'] = $block_name;
		$block['block_name_mod'] = substr($block_uid, strlen($block_id) + 1);
		$block['block_uid'] = $block_uid;
		$block['params'] = $config_params;
		$block['block_includes'] = $block_includes;
		$block['storage'] = $block_storage;
		$block['template_vars'] = $block_template_vars;
		if ($show_result == 'status_404')
		{
			$block['status_code'] = '404';
		} elseif (strpos($show_result, 'status_302:') === 0)
		{
			$block['status_code'] = '302 (' . substr($show_result, 11) . ')';
		} elseif (strpos($show_result, 'status_301:') === 0)
		{
			$block['status_code'] = '301 (' . substr($show_result, 11) . ')';
		}
		if ($block['status_code'] != '' && $status_code == '200')
		{
			$status_code = $block['status_code'];
		}
		$block['memory_usage'] = $memory_usage;
		$block['time_usage'] = $time_usage;
		$blocks[] = $block;
	}
	$page_id = $old_page_id;
}

$page_configuration = explode("||", file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));

$smarty = new mysmarty();
$smarty->assign_by_ref("config", $config);
$smarty->assign("page_status", $status_code);
$smarty->assign("page_id", $page_info['page_id']);
$smarty->assign("page_name", $page_info['title']);
$smarty->assign("page_external_id", $page_id);
$smarty->assign("page_is_xml", $page_configuration[3]);
$smarty->assign("page_request_uri", $request_uri);
$smarty->assign("page_http_params", $http_params);
$smarty->assign("session_values", $session_values);
$smarty->assign("runtime_params", $runtime_params);
$smarty->assign("htaccess_rules", $htaccess_rules);
$smarty->assign("localization", $localization);
$smarty->assign("blocks", $blocks);
$smarty->assign("lang", $lang);
$smarty->assign("admin_url", $config['admin_url'] ?? str_replace('www.', '', $config['project_url']));
$smarty->assign("page_includes", $page_includes);
$smarty->display("website_ui_debug.tpl");

function variable_flat_recursive($block_uid, $item, $parent_key, $level)
{
	global $variable_flat;

	if (is_array($item))
	{
		ksort($item);
		$index = 0;
		foreach ($item as $k => $v)
		{
			$new_id = "$block_uid-$index";
			$key = $k;
			if ($parent_key <> '')
			{
				$key = "$parent_key.$k";
			}
			if ($key == 'config' || $key == 'storage')
			{
				continue;
			}
			if ($index > 100)
			{
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => '...');
				break;
			}
			if (is_array($v))
			{
				$is_expandable = 1;
				if (count($v) == 0)
				{
					$value_replace = "Array (0)";
					$is_expandable = 0;
				} elseif (isset($v[0]))
				{
					$value_replace = "Array (" . count($v) . ")";
				} elseif (isset($v['title']))
				{
					$value_replace = "$v[title] (Object)";
				} else
				{
					$value_replace = "Object";
				}
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => $value_replace, 'is_expandable' => $is_expandable);
				variable_flat_recursive($new_id, $v, "$key", $level + 1);
				$index++;
			} else
			{
				$variable_flat[] = array('row_id' => $new_id, 'level' => $level, 'key' => "$key", 'value' => $v);
				variable_flat_recursive($new_id, $v, "$key", $level + 1);
				$index++;
			}

		}
	}
}
