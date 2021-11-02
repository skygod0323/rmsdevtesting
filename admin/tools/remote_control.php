<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR);
$api_version = '5.2.0';

// comma separated list of whitelisted IPs
$whitelist_ips = "";

// comma separated list of whitelisted referers
$whitelist_referers = "";

// the number of seconds temp links are valid
$ttl = 3600;

######################################################################################

$config['cv'] = "a17d19a170a0df92ecc3900ae651d28e";

if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	if (strpos($_SERVER['REMOTE_ADDR'], ',') !== false)
	{
		$_SERVER['REMOTE_ADDR'] = trim(substr($_SERVER['REMOTE_ADDR'], 0, strpos($_SERVER['REMOTE_ADDR'], ',')));
	}
} elseif (isset($_SERVER['HTTP_X_REAL_IP']))
{
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

if ($_REQUEST['action'] == '' && $_REQUEST['file'] == '')
{
	echo "connected.";
	die;
} elseif ($_REQUEST['action'] == 'version')
{
	echo $api_version;
	die;
} elseif ($_REQUEST['action'] == 'ip')
{
	echo $_SERVER['REMOTE_ADDR'];
	die;
} elseif ($_REQUEST['action'] == 'path')
{
	if ($_REQUEST['cv'] != $config['cv'])
	{
		header("HTTP/1.0 403 Forbidden");
		header("KVS-Errno: 2");
		echo "Access denied (errno 2)";
		die;
	}
	echo dirname($_SERVER['SCRIPT_FILENAME']);
} elseif ($_REQUEST['action'] == 'status')
{
	$load = sys_getloadavg();
	$load = floatval($load[0]);
	if ($_REQUEST['content_path'] != '' && (is_dir(dirname($_SERVER['SCRIPT_FILENAME']) . "/$_REQUEST[content_path]") || is_link(dirname($_SERVER['SCRIPT_FILENAME']) . "/$_REQUEST[content_path]")))
	{
		$total_space = @disk_total_space(dirname($_SERVER['SCRIPT_FILENAME']) . "/$_REQUEST[content_path]");
		$free_space = @disk_free_space(dirname($_SERVER['SCRIPT_FILENAME']) . "/$_REQUEST[content_path]");
	} else
	{
		$total_space = @disk_total_space(dirname($_SERVER['SCRIPT_FILENAME']));
		$free_space = @disk_free_space(dirname($_SERVER['SCRIPT_FILENAME']));
	}
	echo "$load|$total_space|$free_space";
	die;
} elseif ($_REQUEST['action'] == 'time')
{
	echo time();
	die;
} elseif ($_REQUEST['action'] == 'check')
{
	$content_path = $_REQUEST['content_path'];
	$paths = explode('||', $_REQUEST['files']);
	foreach ($paths as $path)
	{
		if ($path <> '')
		{
			$path_rec = explode('|', $path);
			if ($content_path <> '')
			{
				if ($path_rec[1] > 0)
				{
					if (sprintf("%.0f", @filesize($content_path . "/$path_rec[0]")) <> $path_rec[1])
					{
						echo "$content_path/$path_rec[0]";
						die;
					}
				} else
				{
					if (sprintf("%.0f", @filesize($content_path . "/$path_rec[0]")) < 1)
					{
						echo "$content_path/$path_rec[0]";
						die;
					}
				}
			} else
			{
				if ($path_rec[1] > 0)
				{
					if (sprintf("%.0f", @filesize("$path_rec[0]")) <> $path_rec[1])
					{
						echo "$path_rec[0]";
						die;
					}
				} else
				{
					if (sprintf("%.0f", @filesize("$path_rec[0]")) < 1)
					{
						echo "$path_rec[0]";
						die;
					}
				}
			}
		}
	}
	echo '1';
	die;
} elseif ($_REQUEST['file'] <> '')
{
	$time = intval($_REQUEST['time']);
	$limit = intval($_REQUEST['lr']);
	$cv = trim($_REQUEST['cv2']);
	$target_file = rawurldecode($_REQUEST['file']);
	$is_download = trim($_GET['download']);

	if (strpos($target_file, 'B64') === 0)
	{
		$target_file_info = @unserialize(base64_decode(substr($target_file, 3)));

		if (!isset($target_file_info['time'], $target_file_info['cv'], $target_file_info['file']))
		{
			header("HTTP/1.0 403 Forbidden");
			header("KVS-Errno: 2");
			echo "Access denied (errno 2)";
			die;
		}

		if ($target_file_info['time'] < time() - $ttl || $target_file_info['time'] > time() + $ttl)
		{
			header("HTTP/1.0 403 Forbidden");
			header("KVS-Errno: 3");
			echo "Access denied (errno 3)";
			die;
		}

		$allowed_ips = explode(',', trim($_COOKIE["kt_remote_ips"]));
		if (md5($target_file_info['time'] . $target_file_info['limit'] . $target_file_info['file'] . $_SERVER['REMOTE_ADDR'] . $config['cv']) !== $target_file_info['cv'])
		{
			$ip_valid = false;
			foreach ($allowed_ips as $allowed_ip)
			{
				$allowed_ip = explode("||", $allowed_ip);
				if ($allowed_ip[1] === md5($allowed_ip[0] . $config['cv']))
				{
					if (md5($target_file_info['time'] . $target_file_info['limit'] . $target_file_info['file'] . $allowed_ip[0] . $config['cv']) === $target_file_info['cv'])
					{
						$ip_valid = true;
						break;
					}
				}
			}
			if (!$ip_valid && $whitelist_ips)
			{
				$whitelist_ips = array_map('trim', explode(',', trim($whitelist_ips)));
				foreach ($whitelist_ips as $whitelist_ip)
				{
					if ($whitelist_ip == $_SERVER['REMOTE_ADDR'] || md5($target_file_info['time'] . $target_file_info['limit'] . $target_file_info['file'] . $whitelist_ip . $config['cv']) === $target_file_info['cv'])
					{
						$ip_valid = true;
						break;
					}
				}
			}
			if (!$ip_valid)
			{
				header("HTTP/1.0 403 Forbidden");
				header("KVS-Errno: 4");
				header("KVS-IP: $_SERVER[REMOTE_ADDR]");
				echo "Access denied (errno 4)";
				die;
			}
		} else
		{
			$has_ip_cookie = false;
			foreach ($allowed_ips as $allowed_ip)
			{
				$allowed_ip = explode("||", $allowed_ip);
				if ($allowed_ip[0] == $_SERVER['REMOTE_ADDR'])
				{
					$has_ip_cookie = true;
				}
			}
			if (!$has_ip_cookie)
			{
				$allowed_ips[] = $_SERVER['REMOTE_ADDR'] . '||' . md5($_SERVER['REMOTE_ADDR'] . $config['cv']);
				setcookie("kt_remote_ips", implode(',', $allowed_ips), time() + $ttl, "/");
			}
		}

		$target_file = $target_file_info['file'];
		$limit = $target_file_info['limit'];
	} else
	{
		if ($time < time() - $ttl || $time > time() + $ttl)
		{
			header("HTTP/1.0 403 Forbidden");
			header("KVS-Errno: 3");
			echo "Access denied (errno 3)";
			die;
		}

		if (md5($time . $limit . $config['cv']) !== $cv)
		{
			header("HTTP/1.0 403 Forbidden");
			header("KVS-Errno: 4");
			echo "Access denied (errno 4)";
			die;
		}

		if ($_SERVER['HTTP_REFERER'] != '' && $_REQUEST['cv3'] != '')
		{
			$ref_host = parse_url(str_replace('www.', '', $_SERVER['HTTP_REFERER']), PHP_URL_HOST);
			if ($ref_host != '' && $ref_host != $_SERVER['SERVER_NAME'] && md5($ref_host . $config['cv']) !== trim($_REQUEST['cv3']))
			{
				$referer_valid = false;
				$whitelist_referers = array_map('trim', explode(',', trim($whitelist_referers)));
				foreach ($whitelist_referers as $whitelist_referer)
				{
					if ($whitelist_referer == $ref_host)
					{
						$referer_valid = true;
						break;
					}
				}

				if (!$referer_valid)
				{
					header("HTTP/1.0 403 Forbidden");
					header("KVS-Errno: 5");
					echo "Access denied (errno 5)";
					die;
				}
			}
		}

		if (md5($target_file . $config['cv']) !== trim($_REQUEST['cv4']))
		{
			header("HTTP/1.0 403 Forbidden");
			header("KVS-Errno: 6");
			echo "Access denied (errno 6)";
			die;
		}
	}

	if (floatval($_REQUEST['start']) > 0)
	{
		$start_str = "?start=" . floatval($_REQUEST['start']);
	}

	if (strpos($target_file, ".flv") !== false)
	{
		header("Content-Type: video/x-flv");
	} elseif (strpos($target_file, ".mp4") !== false)
	{
		header("Content-Type: video/mp4");
	} elseif (strpos($target_file, ".webm") !== false)
	{
		header("Content-Type: video/webm");
	} elseif (strpos($target_file, ".jpg") !== false)
	{
		header("Content-Type: image/jpeg");
	} elseif (strpos($target_file, ".gif") !== false)
	{
		header("Content-Type: image/gif");
	} elseif (strpos($target_file, ".zip") !== false)
	{
		header("Content-Type: application/zip");
	} else
	{
		header("Content-Type: application/octet-stream");
	}

	if (intval($limit) > 0)
	{
		header("X-Accel-Limit-Rate: $limit");
	}
	$short_file_name = basename($target_file);
	if ($_REQUEST['download_filename'] <> '')
	{
		$short_file_name = $_REQUEST['download_filename'];
	}
	if ($is_download == 'true')
	{
		header("Content-Disposition: attachment; filename=\"$short_file_name\"");
	} else
	{
		header("Content-Disposition: inline; filename=\"$short_file_name\"");
	}
	header("X-Accel-Redirect: $target_file{$start_str}");
}
