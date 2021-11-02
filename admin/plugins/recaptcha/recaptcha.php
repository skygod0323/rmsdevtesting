<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function recaptchaInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins");
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";
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

function recaptchaIsEnabled()
{
	global $config;

	recaptchaInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";

	return is_file("$plugin_path/enabled.dat");
}

function recaptchaShow()
{
	global $config, $errors, $page_name, $lang;

	recaptchaInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/recaptcha";

	$errors = null;

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
			validate_field('empty', $_POST['site_key'], $lang['plugins']['recaptcha']['field_site_key']);
			validate_field('empty', $_POST['secret_key'], $lang['plugins']['recaptcha']['field_secret_key']);
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
			if (intval($_POST['is_enabled']) == 1)
			{
				file_put_contents("$plugin_path/enabled.dat", '1', LOCK_EX);
			} else
			{
				@unlink("$plugin_path/enabled.dat");
			}
			return_ajax_success("$page_name?plugin_id=recaptcha");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat")));
	}

	$is_recaptcha_ready = 0;
	require_once 'include/setup_smarty_site.php';
	$smarty_site = new mysmarty_site();
	$template_files = get_contents_from_dir($smarty_site->template_dir, 1);
	foreach ($template_files as $template_file)
	{
		$template_contents = file_get_contents("{$smarty_site->template_dir}/$template_file");
		if (strpos($template_contents, 'https://www.google.com/recaptcha/api.js') !== false)
		{
			$is_recaptcha_ready = 1;
			break;
		}
	}

	if ($is_recaptcha_ready == 0)
	{
		$_POST['errors'][] = bb_code_process($lang['plugins']['recaptcha']['error_template_not_ready']);
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
