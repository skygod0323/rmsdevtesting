<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'] <> '')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_servers.php";
require_once "functions.php";

if (!is_file("$config[project_path]/admin/data/system/cron_servers.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_servers.lock", "1", LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/cron_servers.lock", "r+");
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors', 1);

log_output("INFO  Servers processor started");

$options = get_options(array('SYSTEM_CONVERSION_API_VERSION'));
log_output("INFO  Latest conversion API version: $options[SYSTEM_CONVERSION_API_VERSION]");

// check server ip
$own_ip = get_page('', "$config[project_url]/get_file.php?action=check_ip", '', '', 1, 0, 60, '');
if ($own_ip <> '' && strlen($own_ip) <= 15)
{
	sql_pr("update $config[tables_prefix]options set value=? where variable='ANTI_HOTLINK_OWN_IP'", $own_ip);
	log_output("INFO  Server IP detected: $own_ip");
}

//update storage servers stats and validate server data
$data = mr2array(sql("select * from $config[tables_prefix]admin_servers order by server_id asc"));
foreach ($data as $res)
{
	if (strpos($res['path'], '%PROJECT_PATH%') !== false)
	{
		$res['path'] = str_replace('%PROJECT_PATH%', rtrim($config['project_path'], '/'), $res['path']);
		sql_pr("update $config[tables_prefix]admin_servers set path=? where server_id=?", $res['path'], $res['server_id']);
	}

	$rnd = mt_rand(10000000, 99999999);
	mkdir("$config[temporary_path]/$rnd");
	chmod("$config[temporary_path]/$rnd", 0777);
	if (check_file('status.dat', '/', $res) > 0)
	{
		get_file('status.dat', '/', "$config[temporary_path]/$rnd", $res);
	}

	if (is_file("$config[temporary_path]/$rnd/status.dat"))
	{
		$data = explode("|", file_get_contents("$config[temporary_path]/$rnd/status.dat"));
		$load = trim($data[0]);
		$total_space = $data[1];
		$free_space = $data[2];
		@unlink("$config[temporary_path]/$rnd/status.dat");
	} elseif ($res['streaming_type_id'] == 0 || $res['streaming_type_id'] == 1)
	{
		if (intval($res['is_remote']) == 1)
		{
			$temp = explode("/", truncate_to_domain($res['urls']), 2);
			$content_path = $temp[1];
			$content_path = trim($content_path, "/");
			$data = explode("|", get_page('', $res['control_script_url'] . "?action=status&content_path=" . urlencode($content_path), '', '', 1, 0, 60, ''));
			$load = trim($data[0]);
			$total_space = $data[1];
			$free_space = $data[2];
		} else
		{
			$load = get_LA();
			$total_space = @disk_total_space($res['path']);
			$free_space = @disk_free_space($res['path']);
		}
	} else
	{
		if ($res['connection_type_id'] == 0 || $res['connection_type_id'] == 1)
		{
			$load = get_LA();
			$total_space = @disk_total_space($res['path']);
			$free_space = @disk_free_space($res['path']);
		} else
		{
			$load = 0;
			$total_space = 1000 * 1024 * 1024 * 1024;
			$free_space = 1000 * 1024 * 1024 * 1024;
		}
	}

	if ($total_space < 1 || $free_space < 1)
	{
		$load = $res['load'];
		$total_space = $res['total_space'];
		$free_space = $res['free_space'];
	}

	$remote_version = '';
	if (intval($res['streaming_type_id']) != 4)
	{
		if (intval($res['connection_type_id']) == 1 || intval($res['connection_type_id']) == 2)
		{
			$remote_version = get_page('', "$res[control_script_url]?action=version", '', '', 1, 0, 60, '');
			if ($remote_version == '')
			{
				$remote_version = '3.4.0';
			}
		}
	}

	$error_id = 0;

	if (!test_connection($res))
	{
		$error_id = 1;
	}
	if ($error_id == 0)
	{
		if ((intval($res['connection_type_id']) == 1 || intval($res['connection_type_id']) == 2) && (intval($res['streaming_type_id']) == 0 || intval($res['streaming_type_id']) == 1))
		{
			// validate control script for active remote servers
			if (get_page('', $res['control_script_url'], '', '', 1, 0, 60, '') != 'connected.')
			{
				$error_id = 2;
			} else
			{
				if (strpos($config['project_url'], 'https://') === 0 && strpos($res['control_script_url'], 'https://') === false)
				{
					$error_id = 7;
				} else
				{
					$secret_remote_key = $config['cv'];
					if ($config['cvr'])
					{
						$secret_remote_key = $config['cvr'];
					}
					$remote_path = get_page('', "$res[control_script_url]?action=path&cv=$secret_remote_key", '', '', 1, 0, 60, '');
					if (strpos($remote_path, 'Access denied') !== false)
					{
						$error_id = 3;
					} else
					{
						$remote_time = intval(get_page('', "$res[control_script_url]?action=time", '', '', 1, 0, 60, ''));
						if ($remote_time > 0)
						{
							if ($remote_time < time() + floatval($res['time_offset']) * 3600 - 300 || $remote_time > time() + floatval($res['time_offset']) * 3600 + 300)
							{
								$error_id = 4;
							}
						}
					}
				}
			}
		}
	}
	if ($error_id == 0)
	{
		if (intval($res['streaming_type_id']) == 4)
		{
			if (!is_file("$config[project_path]/admin/cdn/$res[streaming_script]"))
			{
				$error_id = 6;
			}
		}
	}
	if ($error_id == 0)
	{
		if ($res['content_type_id'] == 1)
		{
			$validation_result = validate_server_operation_videos($res);
		} elseif ($res['content_type_id'] == 2)
		{
			$validation_result = validate_server_operation_albums($res);
		}
		if (@count($validation_result) > 0)
		{
			foreach ($validation_result as $validation_item)
			{
				if (@count($validation_item['checks']) > 0)
				{
					foreach ($validation_item['checks'] as $check)
					{
						if ($check['not_accessible'] <> 1 && $check['is_error'] == 1 && $check['type'] <> 'direct_link')
						{
							$error_id = 5;
							break 2;
						}
					}
				}
			}
		}
	}

	if ($error_id > 0)
	{
		$res['error_iteration']++;
	} else
	{
		$res['error_iteration'] = 0;
	}

	sql_pr("update $config[tables_prefix]admin_servers set control_script_url_version=?, $config[tables_prefix]admin_servers.load=?, total_space=?, free_space=?, error_id=?, error_iteration=? where server_id=?",
		$remote_version, $load, $total_space, $free_space, $error_id, $res['error_iteration'], $res['server_id']
	);
	if ($error_id == 0)
	{
		log_output("INFO  Storage server \"$res[title]\" check finished: no issues");
	} else
	{
		log_output("INFO  Storage server \"$res[title]\" check finished: issue $error_id found");
	}
	@rmdir("$config[temporary_path]/$rnd");
}
update_cluster_data();

//update conversion servers stats and validate server connection
$data = mr2array(sql("select *, 1 as is_conversion_server from $config[tables_prefix]admin_conversion_servers where status_id in (1,2) order by server_id asc"));
foreach ($data as $res)
{
	if (strpos($res['path'], '%PROJECT_PATH%') !== false)
	{
		$res['path'] = str_replace('%PROJECT_PATH%', rtrim($config['project_path'], '/'), $res['path']);
		sql_pr("update $config[tables_prefix]admin_conversion_servers set path=? where server_id=?", $res['path'], $res['server_id']);
	}

	$error_id = 0;

	if (!test_connection($res))
	{
		$error_id = 1;
	} else
	{
		get_file('heartbeat.dat', '/', $config['temporary_path'], $res);
		$heartbeat = @unserialize(@file_get_contents("$config[temporary_path]/heartbeat.dat"));
		if (!is_array($heartbeat))
		{
			$error_id = 2;
		} else
		{
			$res['api_version'] = trim($heartbeat['api_version']);
			if (is_array($heartbeat['libraries']))
			{
				foreach ($heartbeat['libraries'] as $library)
				{
					if ($library['is_error'] == 1)
					{
						$error_id = 4;
						break;
					}
				}
			}

			$res['load'] = $heartbeat['la'];
			$res['total_space'] = $heartbeat['total_space'];
			$res['free_space'] = $heartbeat['free_space'];
			$res['heartbeat_date'] = date("Y-m-d H:i:s", $heartbeat['time']);

			if ($error_id == 0)
			{
				if (time() - strtotime($res['heartbeat_date']) > 900)
				{
					$error_id = 3;
				}
			}

			if ($error_id == 0)
			{
				$latest_api_version = intval(str_replace('.', '', $options['SYSTEM_CONVERSION_API_VERSION']));
				if (intval(str_replace('.', '', $res['api_version'])) < $latest_api_version)
				{
					$error_id = 5;
				}
			}

			if ($error_id == 0)
			{
				if ($heartbeat['last_activity'] > 0 &&  $heartbeat['time'] - $heartbeat['last_activity'] > 120 * 60)
				{
					$error_id = 6;
				}
			}
		}
		@unlink("$config[temporary_path]/heartbeat.dat");
	}

	if ($error_id > 0)
	{
		$res['error_iteration']++;
	} else
	{
		$res['error_iteration'] = 0;
	}

	sql_pr("update $config[tables_prefix]admin_conversion_servers set $config[tables_prefix]admin_conversion_servers.load=?, total_space=?, free_space=?, heartbeat_date=?, api_version=?, error_id=?, error_iteration=? where server_id=?",
		$res['load'], $res['total_space'], $res['free_space'], $res['heartbeat_date'], $res['api_version'], $error_id, $res['error_iteration'], $res['server_id']
	);
	if ($error_id == 0)
	{
		log_output("INFO  Conversion server \"$res[title]\" check finished: no issues");
	} else
	{
		log_output("INFO  Conversion server \"$res[title]\" check finished: issue $error_id found");
	}
}
sql("update $config[tables_prefix]admin_conversion_servers set status_id=1 where status_id=2 and error_id=0");

disconnect_all_servers();
log_output("INFO  Servers processor finished");

flock($lock, LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message == '')
	{
		echo "\n";
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
	}
}
