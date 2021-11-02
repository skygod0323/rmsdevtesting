<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once "admin/include/setup.php";

if ($_GET['id'] > 0 || $_GET['dir'] <> '')
{
	require_once "admin/include/functions_base.php";
	require_once "admin/include/functions.php";
	require_once "admin/include/database_selectors.php";

	if ($_GET['id'] > 0)
	{
		$result = sql_pr("select content_source_id, url from $config[tables_prefix]content_sources where content_source_id=?", intval($_GET['id']));
	} else
	{
		$result = sql_pr("select content_source_id, url from $config[tables_prefix]content_sources where (dir=? or $database_selectors[where_locale_dir])", trim($_GET['dir']), trim($_GET['dir']));
	}

	if (mr2rows($result) > 0)
	{
		$content_source_data = mr2array_single($result);
		if ($content_source_data['url'] == '')
		{
			header('HTTP/1.0 404 Not Found');
			if (is_file("$config[project_path]/404.html"))
			{
				echo @file_get_contents("$config[project_path]/404.html");
			} else
			{
				echo "The requested URL was not found on this server.";
			}
			die;
		}

		if ($_COOKIE['kt_tcookie'] == '1')
		{
			$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
			if (intval($stats_params['collect_traffic_stats']) == 1)
			{
				$device_type = 0;
				if (intval($stats_params['collect_traffic_stats_devices']) == 1)
				{
					$device_type = get_device_type();
				}
				file_put_contents("$config[project_path]/admin/data/stats/cs_out.dat", date("Y-m-d") . "|$content_source_data[content_source_id]|$_SERVER[GEOIP_COUNTRY_CODE]|$_COOKIE[kt_referer]|$_COOKIE[kt_qparams]|$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}

		if (is_file("$config[project_path]/admin/data/system/runtime_params.dat") && filesize("$config[project_path]/admin/data/system/runtime_params.dat") > 0)
		{
			session_start();
			$runtime_params = unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
			foreach ($runtime_params as $param)
			{
				$var = trim($param['name']);
				$val = trim($_GET[$var]);
				if (strlen($val) == 0)
				{
					$val = $_SESSION['runtime_params'][$var];
				}
				if (strlen($val) == 0)
				{
					$val = trim($param['default_value']);
				}
				if ($var <> '')
				{
					$content_source_data['url'] = str_replace("%$var%", urlencode($val), $content_source_data['url']);
				}
			}
		}
		header("Location: $content_source_data[url]");
		die;
	}
}

header('HTTP/1.0 404 Not Found');
if (is_file("$config[project_path]/404.html"))
{
	echo @file_get_contents("$config[project_path]/404.html");
} else
{
	echo "The requested URL was not found on this server.";
}
