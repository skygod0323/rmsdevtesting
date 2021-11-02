<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function push_notificationsInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins");
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/push_notifications";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path);
		chmod($plugin_path, 0777);
	}
	if (!is_file("$plugin_path/data.dat"))
	{
		$data = array();
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
}

function push_notificationsIsEnabled()
{
	global $config;

	push_notificationsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/push_notifications";

	return is_file("$plugin_path/enabled.dat");
}

function push_notificationsShow()
{
	global $config, $errors, $page_name, $lang;

	push_notificationsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/push_notifications";

	$errors = null;

	if ($_GET['action'] == 'library')
	{
		header("Content-Type: application/javascript");
		header("Content-Disposition: attachment; filename=\"sw.js\"");
		header("Content-Length: " . filesize("$config[project_path]/admin/plugins/push_notifications/data/sw.js"));
		readfile("$config[project_path]/admin/plugins/push_notifications/data/sw.js");
		die;
	}

	$is_htps = strpos($config['project_url'], "https://") !== false;

	if ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['is_enabled']) == 1)
		{
			validate_field('empty', $_POST['refid'], $lang['plugins']['push_notifications']['field_refid']);
			if ($_POST['repeat'] == 'interval')
			{
				validate_field('empty_int', $_POST['repeat_interval'], $lang['plugins']['push_notifications']['field_repeat']);
			}

			if ($is_htps)
			{
				if (!is_file("$config[project_path]/sw.js"))
				{
					if (!is_writable("$config[project_path]"))
					{
						$errors[] = str_replace('%1%', $lang['plugins']['push_notifications']['field_js_library'], $lang['plugins']['push_notifications']['error_missing_library']);
					}
				}
			}
		}

		if (!is_array($errors))
		{
			file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
			if (intval($_POST['is_enabled']) == 1)
			{
				if ($is_htps && !is_file("$config[project_path]/sw.js"))
				{
					copy("$config[project_path]/admin/plugins/push_notifications/data/sw.js", "$config[project_path]/sw.js");
				}
				file_put_contents("$plugin_path/enabled.dat", '1', LOCK_EX);
				$feature_plugin_pn = 1;
			} else
			{
				@unlink("$plugin_path/enabled.dat");
				$feature_plugin_pn = 0;
			}

			$project_url=urlencode($config['project_url']);
			get_page('',"https://www.kernel-scripts.com/get_version/?url=$project_url&feature_plugin_pn=$feature_plugin_pn",'','',1,0,5,'');

			return_ajax_success("$page_name?plugin_id=push_notifications");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	if ($is_htps)
	{
		if (!is_file("$config[project_path]/sw.js"))
		{
			if (!is_writable("$config[project_path]"))
			{
				$_POST['is_library_missing'] = 1;
			}
			if (intval($_POST['is_enabled']) == 1)
			{
				$_POST['errors'][] = str_replace('%1%', $lang['plugins']['push_notifications']['field_js_library'], $lang['plugins']['push_notifications']['error_missing_library']);
			}
		}
		$_POST['is_https'] = 1;
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat")));
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
