<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/pclzip.lib.php';
require_once 'include/check_access.php';

$plugins_list = get_contents_from_dir("$config[project_path]/admin/plugins", 2);
sort($plugins_list);

$plugins = array();
foreach ($plugins_list as $k => $v)
{
	if (!is_file("$config[project_path]/admin/plugins/$v/$v.php") || !is_file("$config[project_path]/admin/plugins/$v/$v.tpl") || !is_file("$config[project_path]/admin/plugins/$v/$v.dat"))
	{
		continue;
	}
	if ($_SESSION['userdata']['is_superadmin'] == 0 && !in_array("plugins|$v", $_SESSION['permissions']))
	{
		continue;
	}

	$temp = array();
	$temp['id'] = $v;

	$validity_check_hash = trim(@file_get_contents("$config[project_path]/admin/data/plugins/$v/hash.dat"));
	$validity_actual_hash = md5_file("$config[project_path]/admin/plugins/$v/$v.php");
	if ($validity_check_hash != $validity_actual_hash)
	{
		unset($res);
		exec("$config[php_path] $config[project_path]/admin/plugins/$v/$v.php test", $res);
		if (trim(@implode("", $res)) != 'OK')
		{
			$temp['is_invalid'] = 1;
		} else
		{
			if (!is_dir("$config[project_path]/admin/data/plugins"))
			{
				mkdir("$config[project_path]/admin/data/plugins");
				chmod("$config[project_path]/admin/data/plugins", 0777);
			}
			if (!is_dir("$config[project_path]/admin/data/plugins/$v"))
			{
				mkdir("$config[project_path]/admin/data/plugins/$v");
				chmod("$config[project_path]/admin/data/plugins/$v", 0777);
			}
			file_put_contents("$config[project_path]/admin/data/plugins/$v/hash.dat", $validity_actual_hash);
		}
	}

	if (!isset($temp['is_invalid']))
	{
		require_once "$config[project_path]/admin/plugins/$v/$v.php";
		if (!function_exists("{$v}Show"))
		{
			$temp['is_invalid'] = 1;
		}
		if (function_exists("{$v}IsEnabled"))
		{
			$func = "{$v}IsEnabled";
			$is_enabled = $func();
			if ($is_enabled)
			{
				$temp['is_enabled'] = 1;
			} else
			{
				$temp['is_enabled'] = 0;
			}
		} else
		{
			$temp['is_enabled'] = 0;
		}
	}

	$file_data = file_get_contents("$config[project_path]/admin/plugins/$v/$v.dat");
	preg_match("|<plugin_name>(.*?)</plugin_name>|is", $file_data, $temp_find);
	$temp['name'] = trim($temp_find[1]);
	preg_match("|<author>(.*?)</author>|is", $file_data, $temp_find);
	$temp['author'] = trim($temp_find[1]);
	preg_match("|<version>(.*?)</version>|is", $file_data, $temp_find);
	$temp['version'] = trim($temp_find[1]);
	preg_match("|<kvs_version>(.*?)</kvs_version>|is", $file_data, $temp_find);
	$temp['kvs_version'] = trim($temp_find[1]);
	preg_match("|<plugin_types>(.*?)</plugin_types>|is", $file_data, $temp_find);
	$temp['plugin_types'] = explode(',', trim($temp_find[1]));

	$req_kvs_version = $temp['kvs_version'];
	$req_kvs_version = intval(str_replace('.', '', $req_kvs_version));

	$kvs_version = $config['project_version'];
	$kvs_version = intval(str_replace('.', '', $kvs_version));

	if ($req_kvs_version > $kvs_version)
	{
		$temp['is_invalid'] = 1;
		$temp['is_invalid_version'] = 1;
	}

	if (is_file("$config[project_path]/admin/plugins/$v/langs/english.php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/english.php";
	}
	if ($_SESSION['userdata']['lang'] != 'english' && is_file("$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php"))
	{
		require_once "$config[project_path]/admin/plugins/$v/langs/" . $_SESSION['userdata']['lang'] . ".php";
	}
	$temp['title'] = $lang['plugins'][$v]['title'];
	$temp['description'] = $lang['plugins'][$v]['description'];
	$plugins[] = $temp;
}
$plugins = kt_array_multisort($plugins, array(array('key' => 'title', 'sort' => 'asc')));

$plugin_id = $_REQUEST['plugin_id'];
if (isset($plugin_id))
{
	foreach ($plugins as $v)
	{
		if ($plugin_id == $v['id'])
		{
			$plugin = $v;
		}
	}
	if (!isset($plugin))
	{
		header("Location: $page_name");
		die;
	}

	if ($plugin['is_invalid_version'] == 1)
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('plugin_kvs_version_is_not_supported', $plugin['kvs_version'], $config['project_version']));
	} else
	{
		require_once "$config[project_path]/admin/plugins/$plugin_id/$plugin_id.php";
		$show_plugin_function = "{$plugin_id}Show";
		$show_plugin_function();
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_plugins.tpl');
$smarty->assign('plugins', $plugins);

$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('total_num', count($plugins));
$smarty->assign('list_messages', $list_messages);

if (isset($plugin) && $plugin['is_invalid_version'] <> 1)
{
	$smarty->assign('template', "../plugins/$plugin_id/$plugin_id.tpl");
} else
{
	$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
}

if (isset($plugin_id))
{
	$smarty->assign('page_title', $lang['plugins'][$plugin_id]['title']);
} else
{
	$smarty->assign('page_title', $lang['plugins']['submenu_plugins_home']);
}

$smarty->display("layout.tpl");
