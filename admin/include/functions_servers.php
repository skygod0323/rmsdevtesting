<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$GLOBAL_FTP_SERVERS = [];

function put_file($file_name, $path_from, $path_to, $server_data, $rename_if_possible = false)
{
	if (!isset($server_data['connection_type_id'], $path_from, $path_to) || strlen($file_name) < 3)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('put_file: ' . trim("$path_to/$file_name", '/'), $server_data);

	$source_file = rtrim($path_from, '/') . '/' . $file_name;
	if (!is_file($source_file))
	{
		debug_server('ERROR: source file doesn\'t exist', $server_data);
		return false;
	}

	$filesize = sprintf("%.0f", @filesize($source_file));

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$target_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $path_to, '/');
		$target_file = $target_folder . '/' . $file_name;

		if (!mkdir_recursive($target_folder, 0777))
		{
			if (!is_dir($target_folder))
			{
				debug_server('ERROR: failed to create target directory', $server_data);
			} else
			{
				debug_server('ERROR: target directory is not writable', $server_data);
			}
			return false;
		}

		if ($rename_if_possible && $server_data['connection_type_id'] == 0)
		{
			if (@rename($source_file, $target_file))
			{
				debug_server("file renamed ($filesize bytes)", $server_data);
				return true;
			} else
			{
				debug_server('WARN: failed to rename file', $server_data);
			}
		}
		if (@copy($source_file, $target_file))
		{
			debug_server("file copied ($filesize bytes)", $server_data);
			return true;
		} else
		{
			debug_server('ERROR: failed to copy file', $server_data);
			return false;
		}
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}
		}

		$target_folder = trim($path_to, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$target_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $target_folder, '/');
		}
		$target_file = ltrim($target_folder . '/' . $file_name, '/');

		if ($target_folder)
		{
			$paths = explode('/', $target_folder);
			foreach ($paths as $path)
			{
				if (!isset($current_path))
				{
					$current_path = $path;
				} else
				{
					$current_path .= '/' . $path;
				}

				if (!@ftp_mkdir($conn_id, $current_path))
				{
					$conn_id = ftp_check_connection($conn_id, $server_data);
					if (!isset($conn_id))
					{
						debug_server('ERROR: failed to put file', $server_data);
						return false;
					}

					@ftp_mkdir($conn_id, $current_path);
				}

				if ($server_data['is_conversion_server'] == 1 || $server_data['max_tasks'] > 0)
				{
					if (!@ftp_chmod($conn_id, 0777, $current_path))
					{
						$conn_id = ftp_check_connection($conn_id, $server_data);
						if (!isset($conn_id))
						{
							debug_server('ERROR: failed to put file', $server_data);
							return false;
						}

						@ftp_chmod($conn_id, 0777, $current_path);
					}
				}
			}
		}

		if (!@ftp_put($conn_id, $target_file, $source_file, FTP_BINARY))
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}

			if (!@ftp_put($conn_id, $target_file, $source_file, FTP_BINARY))
			{
				debug_server('ERROR: failed to put file', $server_data);
				return false;
			}
		}

		if ($server_data['is_conversion_server'] == 1 || $server_data['max_tasks'] > 0)
		{
			if (!@ftp_chmod($conn_id, 0666, $target_file))
			{
				$conn_id = ftp_check_connection($conn_id, $server_data);
				if (!isset($conn_id))
				{
					debug_server('ERROR: failed to put file', $server_data);
					return false;
				}

				@ftp_chmod($conn_id, 0666, $target_file);
			}
		}

		debug_server("file copied ($filesize bytes)", $server_data);
		return true;
	}
	return false;
}

function get_file($file_name, $path_from, $path_to, $server_data, $rename_if_possible = false)
{
	if (!isset($server_data['connection_type_id'], $path_from, $path_to) || strlen($file_name) < 3)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('get_file: ' . trim("$path_from/$file_name", '/'), $server_data);

	if (!is_dir($path_to))
	{
		debug_server('ERROR: target directory doesn\'t exist', $server_data);
		return false;
	}
	if (!is_writable($path_to))
	{
		debug_server('ERROR: target directory is not writable', $server_data);
		return false;
	}

	$target_file = rtrim($path_to, '/') . '/' . $file_name;

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$source_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $path_from, '/');
		$source_file = $source_folder . '/' . $file_name;
		if (!is_file($source_file))
		{
			debug_server('ERROR: file doesn\'t exist', $server_data);
			return false;
		}

		$filesize = sprintf("%.0f", @filesize($source_file));
		if ($rename_if_possible && $server_data['connection_type_id'] == 0)
		{
			if (@rename($source_file, $target_file))
			{
				debug_server("file renamed ($filesize bytes)", $server_data);
				return true;
			} else
			{
				debug_server('WARN: failed to rename file', $server_data);
			}
		}
		if (@copy($source_file, $target_file))
		{
			debug_server("file copied ($filesize bytes)", $server_data);
			return true;
		} else
		{
			debug_server('ERROR: failed to copy file', $server_data);
			return false;
		}
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to get file', $server_data);
				return false;
			}
		}

		$source_folder = trim($path_from, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$source_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $source_folder, '/');
		}
		$source_file = ltrim($source_folder . '/' . $file_name, '/');

		if (!@ftp_get($conn_id, $target_file, $source_file, FTP_BINARY))
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to get file', $server_data);
				return false;
			}

			if (!@ftp_get($conn_id, $target_file, $source_file, FTP_BINARY))
			{
				$filesize = @ftp_size($conn_id, $source_file);
				if ($filesize == -1)
				{
					debug_server('ERROR: file doesn\'t exit', $server_data);
				} else
				{
					debug_server('ERROR: file not copied', $server_data);
				}
				return false;
			}
		}

		$filesize = sprintf("%.0f", @filesize($target_file));
		debug_server("file copied ($filesize bytes)", $server_data);
		return true;
	}

	debug_server('ERROR: unsupported connection type', $server_data);
	return false;
}

function delete_file($file_name, $path_from, $server_data)
{
	if (!isset($server_data['connection_type_id'], $path_from) || strlen($file_name) < 3)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('delete_file: ' . trim("$path_from/$file_name", '/'), $server_data);

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$source_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $path_from, '/');
		$source_file = $source_folder . '/' . $file_name;

		$file_exists = file_exists($source_file);
		if (@unlink($source_file))
		{
			debug_server('file deleted', $server_data);
			return true;
		} elseif ($file_exists)
		{
			debug_server('ERROR: file not deleted', $server_data);
			return false;
		} else
		{
			debug_server('WARN: file doesn\'t exist', $server_data);
			return true;
		}
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to delete file', $server_data);
				return false;
			}
		}

		$source_folder = trim($path_from, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$source_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $source_folder, '/');
		}
		$source_file = ltrim($source_folder . '/' . $file_name, '/');

		if (!@ftp_delete($conn_id, $source_file))
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to delete file', $server_data);
				return false;
			}

			if (!@ftp_delete($conn_id, $source_file))
			{
				if (@ftp_size($conn_id, $source_file) == -1)
				{
					debug_server('WARN: file doesn\'t exit', $server_data);
					return true;
				} else
				{
					debug_server('ERROR: file not deleted', $server_data);
					return false;
				}
			}
		}

		debug_server('file deleted', $server_data);
		return true;
	}

	debug_server('ERROR: unsupported connection type', $server_data);
	return false;
}

function delete_dir($dir_name, $server_data)
{
	if (!isset($server_data['connection_type_id']) || !$dir_name)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('delete_dir: ' . trim($dir_name, '/'), $server_data);

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$source_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $dir_name, '/');

		if (!is_dir($source_folder))
		{
			if (is_file($source_folder))
			{
				debug_server('ERROR: directory is actually a file', $server_data);
				return false;
			}
			debug_server('WARN: directory doesn\'t exit', $server_data);
			return true;
		}

		$count = count(get_contents_from_dir($source_folder, 0));
		if (rmdir_recursive($source_folder))
		{
			debug_server("directory deleted ($count files)", $server_data);
			return true;
		} else
		{
			debug_server("ERROR: directory not deleted ($count files)", $server_data);
			return false;
		}
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to delete directory', $server_data);
				return false;
			}
		}

		$source_folder = trim($dir_name, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$source_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $source_folder, '/');
		}

		$files = @ftp_nlist($conn_id, $source_folder);
		if ($files === false)
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to delete directory', $server_data);
				return false;
			}

			$files = @ftp_nlist($conn_id, $source_folder);
			if ($files === false)
			{
				debug_server('ERROR: failed to delete directory', $server_data);
				return false;
			}
		}

		$count = count($files);
		foreach ($files as $k => $v)
		{
			$files[$k] = end(explode('/', $v));
		}

		if ($count == 0)
		{
			if (@ftp_rmdir($conn_id, $source_folder))
			{
				debug_server('directory deleted (0 files)', $server_data);
				return true;
			} else
			{
				debug_server('WARN: directory doesn\'t exist', $server_data);
				return true;
			}
		}

		foreach ($files as $file)
		{
			if ($file == '.' || $file == '..')
			{
				continue;
			}
			if (!@ftp_delete($conn_id, $source_folder . '/' . $file))
			{
				$conn_id = ftp_check_connection($conn_id, $server_data);
				if (!isset($conn_id))
				{
					debug_server('ERROR: failed to delete directory', $server_data);
					return false;
				}

				@ftp_delete($conn_id, $source_folder . '/' . $file);
			}
		}

		if (!@ftp_rmdir($conn_id, $source_folder))
		{
			$conn_id = ftp_check_connection($conn_id, $server_data);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to delete directory', $server_data);
				return false;
			}

			if (!@ftp_rmdir($conn_id, $source_folder))
			{
				debug_server("ERROR: directory not deleted ($count files)", $server_data);
				return false;
			}
		}

		debug_server("directory deleted ($count files)", $server_data);
		return true;
	}

	debug_server('ERROR: unsupported connection type', $server_data);
	return false;
}

function check_file($file_name, $path_from, $server_data)
{
	if (!isset($server_data['connection_type_id'], $path_from) || strlen($file_name) < 3)
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}
	debug_server('check_file: ' . trim("$path_from/$file_name", '/'), $server_data);

	if ($server_data['connection_type_id'] == 0 || $server_data['connection_type_id'] == 1)
	{
		// local or mount
		$source_folder = rtrim(rtrim($server_data['path'], '/') . '/' . $path_from, '/');
		$source_file = $source_folder . '/' . $file_name;

		$filesize = sprintf("%.0f", @filesize($source_file));
		if (@is_file($source_file))
		{
			debug_server("file exists ($filesize bytes)", $server_data);
		} else
		{
			debug_server('file doesn\'t exit', $server_data);
		}
		return $filesize;
	} elseif ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to check file', $server_data);
				return 0;
			}
		}

		$source_folder = trim($path_from, '/');
		if (trim($server_data['ftp_folder'], '/'))
		{
			$source_folder = trim(trim($server_data['ftp_folder'], '/') . '/' . $source_folder, '/');
		}
		$source_file = ltrim($source_folder . '/' . $file_name, '/');

		$filesize = @ftp_size($conn_id, $source_file);
		if ($filesize == -1)
		{
			if (!@ftp_pwd($conn_id))
			{
				debug_server('ERROR: connection failure', $server_data);
				$conn_id = ftp_get_connect_id($server_data, true);
				if (!isset($conn_id))
				{
					debug_server('ERROR: failed to check file', $server_data);
					return 0;
				}

				$filesize = @ftp_size($conn_id, $source_file);
				if ($filesize == -1)
				{
					debug_server('file doesn\'t exit', $server_data);
					return 0;
				}
			} else
			{
				debug_server('file doesn\'t exit', $server_data);
				return 0;
			}
		}

		debug_server("file exists ($filesize bytes)", $server_data);
		return $filesize;
	}

	debug_server('ERROR: unsupported connection type', $server_data);
	return 0;
}

function test_connection($server_data)
{
	global $config;

	if (!is_array($server_data))
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}

	if ($config['is_clone_db'] == 'true')
	{
		return true;
	}

	debug_server('test_connection', $server_data);

	if ($server_data['connection_type_id'] == 2)
	{
		// ftp
		$conn_id = ftp_get_connect_id($server_data, false);
		if (!isset($conn_id))
		{
			$conn_id = ftp_get_connect_id($server_data, true);
			if (!isset($conn_id))
			{
				debug_server('ERROR: failed to test connection', $server_data);
				return false;
			}
		}
	}

	$rnd = "storage-$server_data[server_id]";
	if ($server_data['max_tasks'])
	{
		$rnd = "conversion-$server_data[server_id]";
	}
	file_put_contents("$config[temporary_path]/test-$rnd.dat", 'test', LOCK_EX);

	$res = false;
	if (put_file("test-$rnd.dat", $config['temporary_path'], '', $server_data))
	{
		if (check_file("test-$rnd.dat", '', $server_data) == 4)
		{
			if (delete_file("test-$rnd.dat", '', $server_data))
			{
				$res = true;
			}
		}
	}

	unlink("$config[temporary_path]/test-$rnd.dat");
	return $res;
}

function test_connection_detailed($server_data)
{
	global $config, $GLOBAL_FTP_SERVERS;

	if (!is_array($server_data))
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}

	if ($config['is_clone_db'] == 'true')
	{
		return 0;
	}

	debug_server('test_connection_detailed', $server_data);

	if ($server_data['connection_type_id'] == 2)
	{
		// ftp
		if (!function_exists('ftp_connect'))
		{
			return 4;
		}

		$key = "storage_$server_data[server_id]";
		if ($server_data['max_tasks'])
		{
			// this is conversion server
			$key = "conversion_$server_data[server_id]";
		} elseif ($server_data['last_check_album_id'])
		{
			// this is albums server
			$key = "albums_$server_data[server_id]";
		}

		debug_server('connecting to server...', $server_data);

		$ftp_host = $server_data['ftp_host'];
		$ftp_port = intval($server_data['ftp_port']) > 0 ? intval($server_data['ftp_port']) : 21;
		$ftp_timeout = intval($server_data['ftp_timeout']) > 0 ? intval($server_data['ftp_timeout']) : 10;

		$conn_id = @ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
		if (!$conn_id)
		{
			debug_server("ERROR: failed to establish connection to $ftp_host:$ftp_port", $server_data);
			return 1;
		}
		if (@ftp_login($conn_id, $server_data['ftp_user'], $server_data['ftp_pass']))
		{
			debug_server('logged in', $server_data);
			if (!@ftp_pasv($conn_id, true))
			{
				debug_server('WARN: failed to turn passive mode on', $server_data);
			}
		} else
		{
			return 2;
		}

		$GLOBAL_FTP_SERVERS[$key] = $conn_id;
	}

	$rnd = "storage-$server_data[server_id]";
	if ($server_data['max_tasks'])
	{
		$rnd = "conversion-$server_data[server_id]";
	}
	file_put_contents("$config[temporary_path]/test-$rnd.dat", 'test', LOCK_EX);

	$res = 3;
	if (put_file("test-$rnd.dat", $config['temporary_path'], '', $server_data))
	{
		if (check_file("test-$rnd.dat", '', $server_data) == 4)
		{
			if (delete_file("test-$rnd.dat", '', $server_data))
			{
				$res = 0;
			}
		}
	}

	unlink("$config[temporary_path]/test-$rnd.dat");
	return $res;
}

function test_connection_status($server_data)
{
	if (!is_array($server_data))
	{
		throw new InvalidArgumentException('Invalid parameters passed');
	}

	return intval($server_data['error_id']) != 1;
}

function update_cluster_data()
{
	global $config;

	$data = mr2array(sql_pr("select * from $config[tables_prefix]admin_servers order by server_id asc"));
	if (count($data) == 0)
	{
		return;
	}

	file_put_contents("$config[project_path]/admin/data/system/cluster.dat", serialize($data), LOCK_EX);
}

function validate_server_videos($server_data, $videos)
{
	global $config;

	if (intval($server_data['streaming_type_id']) == 4)
	{
		// CDN specific validation
		$cdn_api_script = $server_data['streaming_script'];
		$cdn_api_name = str_replace('.php', '', $cdn_api_script);
		if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script";
		}

		require_once "$config[project_path]/admin/cdn/$cdn_api_script";
		$get_video_function = "{$cdn_api_name}_get_video";
		if (!function_exists($get_video_function))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script :: $get_video_function";
		}

		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$formats = get_video_formats($video_id, $video['file_formats']);
			$dir_path = get_dir_by_id($video_id);
			foreach ($formats as $format_rec)
			{
				$target_url = "$server_data[urls]/$dir_path/$video_id/$video_id{$format_rec['postfix']}";
				$target_file = substr($target_url, strpos($target_url, '/', 8));
				$video_url = $get_video_function($target_file, $target_url, null, 0, $server_data['streaming_key']);
				if (strpos($video_url, '//') === 0)
				{
					$video_url = "http:$video_url";
				}
				if (!is_binary_file_url($video_url, true, $config['project_url']))
				{
					return "$video_url";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 0)
	{
		$content_path = $server_data['path'];
		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$formats = get_video_formats($video_id, $video['file_formats']);
			$dir_path = get_dir_by_id($video_id);
			foreach ($formats as $format_rec)
			{
				if (sprintf("%.0f", filesize("$content_path/$dir_path/$video_id/$video_id{$format_rec['postfix']}")) != $format_rec['file_size'])
				{
					return "$content_path/$dir_path/$video_id/$video_id{$format_rec['postfix']}";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 1 || intval($server_data['connection_type_id']) == 2)
	{
		$temp = explode('/', truncate_to_domain($server_data['urls']), 2);
		$content_path = $temp[1];
		$content_path = trim($content_path, '/');
		$control_script_url = $server_data['control_script_url'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $control_script_url . '?action=check');
		curl_setopt($ch, CURLOPT_POST, 1);

		$post_data = 'content_path=' . $content_path . '&files=';
		foreach ($videos as $video)
		{
			$video_id = $video['video_id'];
			$formats = get_video_formats($video_id, $video['file_formats']);
			$dir_path = get_dir_by_id($video_id);
			foreach ($formats as $format_rec)
			{
				$post_data .= urlencode("$dir_path/$video_id/$video_id{$format_rec['postfix']}") . "|$format_rec[file_size]||";
			}
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_REFERER, $config['project_url']);

		$result = curl_exec($ch);
		if (curl_errno($ch) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", '[' . date('Y-m-d H:i:s') . '] [' . curl_errno($ch) . '] ' . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
		}
		curl_close($ch);

		if ($result != '1')
		{
			return $result;
		}
	}
	return 1;
}

function validate_server_images($server_data, $images)
{
	global $config;

	if (intval($server_data['streaming_type_id']) == 4)
	{
		// CDN specific validation
		$cdn_api_script = $server_data['streaming_script'];
		$cdn_api_name = str_replace('.php', '', $cdn_api_script);
		if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script";
		}

		require_once "$config[project_path]/admin/cdn/$cdn_api_script";
		$get_image_function = "{$cdn_api_name}_get_image";
		if (!function_exists($get_image_function))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script :: $get_image_function";
		}

		foreach ($images as $image)
		{
			$album_id = $image['album_id'];
			$image_id = $image['image_id'];
			$formats = get_image_formats($album_id, $image['image_formats']);
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['size'] == 'source')
				{
					$target_file = "sources/$dir_path/$album_id/$image_id.jpg";
				} else
				{
					$target_file = "main/$format_rec[size]/$dir_path/$album_id/$image_id.jpg";
				}
				$target_url = "$server_data[urls]/$target_file";
				$target_file = substr($target_url, strpos($target_url, '/', 8));
				$image_url = $get_image_function($target_file, $target_url, $server_data['streaming_key']);
				if (strpos($image_url, '//') === 0)
				{
					$image_url = "http:$image_url";
				}
				if (!is_binary_file_url($image_url, true, $config['project_url']))
				{
					return "$image_url";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 0)
	{
		$content_path = $server_data['path'];
		foreach ($images as $image)
		{
			$album_id = $image['album_id'];
			$image_id = $image['image_id'];
			$formats = get_image_formats($album_id, $image['image_formats']);
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['size'] == 'source')
				{
					$file_path = "sources/$dir_path/$album_id/$image_id.jpg";
				} else
				{
					$file_path = "main/$format_rec[size]/$dir_path/$album_id/$image_id.jpg";
				}
				if (sprintf("%.0f", filesize("$content_path/$file_path")) != $format_rec['file_size'])
				{
					return "$content_path/$file_path";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 1 || intval($server_data['connection_type_id']) == 2)
	{
		$temp = explode('/', truncate_to_domain($server_data['urls']), 2);
		$content_path = $temp[1];
		$content_path = trim($content_path, '/');
		$control_script_url = $server_data['control_script_url'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $control_script_url . '?action=check');
		curl_setopt($ch, CURLOPT_POST, 1);

		$post_data = 'content_path=' . $content_path . '&files=';
		foreach ($images as $image)
		{
			$album_id = $image['album_id'];
			$image_id = $image['image_id'];
			$formats = get_image_formats($album_id, $image['image_formats']);
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats as $format_rec)
			{
				if ($format_rec['size'] == 'source')
				{
					$file_path = "sources/$dir_path/$album_id/$image_id.jpg";
				} else
				{
					$file_path = "main/$format_rec[size]/$dir_path/$album_id/$image_id.jpg";
				}
				$post_data .= urlencode($file_path) . "|$format_rec[file_size]||";
			}
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_REFERER, $config['project_url']);

		$result = curl_exec($ch);
		if (curl_errno($ch) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", '[' . date("Y-m-d H:i:s") . '] [' . curl_errno($ch) . '] ' . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
		}
		curl_close($ch);

		if ($result != '1')
		{
			return $result;
		}
	}
	return 1;
}

function validate_server_albums($server_data, $albums, $formats_albums)
{
	global $config;

	if (intval($server_data['streaming_type_id']) == 4)
	{
		// CDN specific validation
		$cdn_api_script = $server_data['streaming_script'];
		$cdn_api_name = str_replace('.php', '', $cdn_api_script);
		if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script";
		}

		require_once "$config[project_path]/admin/cdn/$cdn_api_script";
		$get_image_function = "{$cdn_api_name}_get_image";
		if (!function_exists($get_image_function))
		{
			return "$config[project_path]/admin/cdn/$cdn_api_script :: $get_image_function";
		}

		foreach ($albums as $album)
		{
			$album_id = $album['album_id'];
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 2)
				{
					$target_file = "preview/$format[size]/$dir_path/$album_id/preview.jpg";
					$target_url = "$server_data[urls]/$target_file";
					$target_file = substr($target_url, strpos($target_url, '/', 8));
					$image_url = $get_image_function($target_file, $target_url, $server_data['streaming_key']);
					if (strpos($image_url, '//') === 0)
					{
						$image_url = "http:$image_url";
					}
					if (!is_binary_file_url($image_url, true, $config['project_url']))
					{
						return "$image_url";
					}
				}
			}

			if ($album['has_preview'] == 1)
			{
				$target_file = "sources/$dir_path/$album_id/preview.jpg";
				$target_url = "$server_data[urls]/$target_file";
				$target_file = substr($target_url, strpos($target_url, '/', 8));
				$image_url = $get_image_function($target_file, $target_url, $server_data['streaming_key']);
				if (strpos($image_url, '//') === 0)
				{
					$image_url = "http:$image_url";
				}
				if (!is_binary_file_url($image_url, true, $config['project_url']))
				{
					return "$image_url";
				}
			}

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			foreach ($zip_files as $zip_file)
			{
				if ($zip_file['size'] == 'source')
				{
					$target_file = "sources/$dir_path/$album_id/$album_id.zip";
				} else
				{
					$target_file = "main/$zip_file[size]/$dir_path/$album_id/$album_id-$zip_file[size].zip";
				}
				$target_url = "$server_data[urls]/$target_file";
				$image_url = $get_image_function($target_file, $target_url, $server_data['streaming_key']);
				if (strpos($image_url, '//') === 0)
				{
					$image_url = "http:$image_url";
				}
				if (!is_binary_file_url($image_url, true, $config['project_url']))
				{
					return "$image_url";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 0)
	{
		$content_path = $server_data['path'];
		foreach ($albums as $album)
		{
			$album_id = $album['album_id'];
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 2)
				{
					if (sprintf("%.0f", filesize("$content_path/preview/$format[size]/$dir_path/$album_id/preview.jpg")) < 1)
					{
						return "$content_path/preview/$format[size]/$dir_path/$album_id/preview.jpg";
					}
				}
			}

			if ($album['has_preview'] == 1)
			{
				if (sprintf("%.0f", filesize("$content_path/sources/$dir_path/$album_id/preview.jpg")) < 1)
				{
					return "$content_path/sources/$dir_path/$album_id/preview.jpg";
				}
			}

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			foreach ($zip_files as $zip_file)
			{
				if ($zip_file['size'] == 'source')
				{
					$file_path = "sources/$dir_path/$album_id/$album_id.zip";
				} else
				{
					$file_path = "main/$zip_file[size]/$dir_path/$album_id/$album_id-$zip_file[size].zip";
				}
				if (sprintf("%.0f", filesize("$content_path/$file_path")) != $zip_file['file_size'])
				{
					return "$content_path/$file_path";
				}
			}
		}
	} elseif (intval($server_data['connection_type_id']) == 1 || intval($server_data['connection_type_id']) == 2)
	{
		$temp = explode('/', truncate_to_domain($server_data['urls']), 2);
		$content_path = $temp[1];
		$content_path = trim($content_path, '/');
		$control_script_url = $server_data['control_script_url'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $control_script_url . '?action=check');
		curl_setopt($ch, CURLOPT_POST, 1);

		$post_data = 'content_path=' . $content_path . '&files=';
		foreach ($albums as $album)
		{
			$album_id = $album['album_id'];
			$dir_path = get_dir_by_id($album_id);
			foreach ($formats_albums as $format)
			{
				if ($format['group_id'] == 2)
				{
					$post_data .= urlencode("preview/$format[size]/$dir_path/$album_id/preview.jpg") . '|0||';
				}
			}

			if ($album['has_preview'] == 1)
			{
				$post_data .= urlencode("sources/$dir_path/$album_id/preview.jpg") . '|0||';
			}

			$zip_files = get_album_zip_files($album_id, $album['zip_files']);
			foreach ($zip_files as $zip_file)
			{
				if ($zip_file['size'] == 'source')
				{
					$file_path = "sources/$dir_path/$album_id/$album_id.zip";
				} else
				{
					$file_path = "main/$zip_file[size]/$dir_path/$album_id/$album_id-$zip_file[size].zip";
				}
				$post_data .= urlencode($file_path) . "|$zip_file[file_size]||";
			}
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_REFERER, $config['project_url']);

		$result = curl_exec($ch);
		if (curl_errno($ch) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", '[' . date("Y-m-d H:i:s") . '] [' . curl_errno($ch) . '] ' . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
		}
		curl_close($ch);

		if ($result != '1')
		{
			return $result;
		}
	}
	return 1;
}

function validate_server_operation_videos($server_data)
{
	global $config;

	$validation_result = [];
	$formats_videos = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) order by video_type_id, title"));
	foreach ($formats_videos as $format)
	{
		$format_record = [];
		$format_record['format'] = $format['title'];
		$format_record['checks'] = [];

		$videos = mr2array(sql_pr("select * from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and server_group_id=? and file_formats like ? order by random1 desc limit 10", $server_data['group_id'], "%||$format[postfix]|%"));
		if (count($videos) > 0)
		{
			$video = $videos[mt_rand(0, count($videos) - 1)];
			$dir_path = get_dir_by_id($video['video_id']);

			$check = [];
			$url = "$server_data[urls]/$dir_path/$video[video_id]/$video[video_id]{$format['postfix']}";

			unset($headers);
			$check_result = is_working_url($url, '', $headers);

			$check['type'] = 'direct_link';
			$check['url'] = $url;
			$check['details'] = "$url\n\n$headers";
			if ($check_result)
			{
				$check['is_error'] = 1;
			}
			$format_record['checks'][] = $check;

			$check = [];
			$time = time();
			$video_formats = get_video_formats($video['video_id'], $video['file_formats'], $video['server_group_id']);
			$url = $video_formats[$format['postfix']]['file_url'] . "?admin_rq_server_id=$server_data[server_id]&ttl=$time&dsc=" . md5("$config[cv]/{$video_formats[$format['postfix']]['file_path']}/$time");

			unset($headers);
			$check_result = is_binary_file_url($url, true, $config['project_url'], $headers, true);

			$check['type'] = 'protected_link';
			$check['url'] = $url;
			$check['details'] = "$url\n\n$headers";
			if (!$check_result)
			{
				$check['is_error'] = 1;
				debug_server("ERROR protected link is not working\nURL: $url\nHEADERS: $headers", $server_data);
			}
			$format_record['checks'][] = $check;
		}

		$validation_result[] = $format_record;
	}
	return $validation_result;
}

function validate_server_operation_albums($server_data)
{
	global $config;

	$source_files_access = mr2number(sql_pr("select value from $config[tables_prefix]options where variable='ALBUMS_SOURCE_FILES_ACCESS_LEVEL'"));

	$validation_result = [];
	$formats_albums = [];
	$formats_albums[] = ['title' => 'source', 'size' => 'source', 'group_id' => 0, 'access_level_id' => $source_files_access];
	$formats_albums = array_merge($formats_albums, mr2array(sql_pr("select * from $config[tables_prefix]formats_albums where status_id=1 order by group_id, title")));
	foreach ($formats_albums as $format)
	{
		$file_prefix = 'sources';
		if ($format['group_id'] == 2)
		{
			$file_prefix = "preview/$format[size]";
		} elseif ($format['group_id'] == 1)
		{
			$file_prefix = "main/$format[size]";
		}

		$format_record = [];
		$format_record['format'] = $format['title'];
		$format_record['is_sources'] = ($format['group_id'] > 0 ? 0 : 1);
		$format_record['checks'] = [];

		$images = mr2array(sql_pr("select $config[tables_prefix]albums_images.image_id, $config[tables_prefix]albums_images.album_id, $config[tables_prefix]albums.server_group_id, $config[tables_prefix]albums.zip_files from $config[tables_prefix]albums inner join $config[tables_prefix]albums_images on $config[tables_prefix]albums_images.album_id=$config[tables_prefix]albums.album_id where $config[tables_prefix]albums.status_id in (0,1) and $config[tables_prefix]albums.server_group_id=? order by random1 desc limit 10", $server_data['group_id']));
		if (count($images) > 0)
		{
			$image = $images[mt_rand(0, count($images) - 1)];
			$dir_path = get_dir_by_id($image['album_id']);

			$check = [];
			if ($format['group_id'] == 2)
			{
				$url = "$server_data[urls]/$file_prefix/$dir_path/$image[album_id]/preview.jpg";
			} else
			{
				$url = "$server_data[urls]/$file_prefix/$dir_path/$image[album_id]/$image[image_id].jpg";
			}

			if ($format['access_level_id'] == 0)
			{
				unset($headers);
				$check_result = is_binary_file_url($url, false, $config['project_url'], $headers, true);

				$check['type'] = 'direct_link2';
				$check['url'] = $url;
				$check['details'] = "$url\n\n$headers";
				if (!$check_result)
				{
					$check['is_error'] = 1;
					debug_server("ERROR direct link is not working\nURL: $url\nHEADERS: $headers", $server_data);
				}
			} else
			{
				unset($headers);
				$check_result = is_working_url($url, '', $headers);

				$check['type'] = 'direct_link';
				$check['url'] = $url;
				$check['details'] = "$url\n\n$headers";
				if ($check_result)
				{
					$check['is_error'] = 1;
				}
			}
			$format_record['checks'][] = $check;

			$check = [];
			$time = time();

			if ($format['group_id'] == 2)
			{
				$file_path = "$file_prefix/$dir_path/$image[album_id]/preview.jpg";
			} else
			{
				$file_path = "$file_prefix/$dir_path/$image[album_id]/$image[image_id].jpg";
			}
			$file_path = md5($config['cv'] . $file_path) . "/$file_path";
			$url = "$config[project_url]/get_image/$image[server_group_id]/$file_path/?admin_rq_server_id=$server_data[server_id]&ttl=$time&dsc=" . md5("$config[cv]/$file_path/$time");

			unset($headers);
			$check_result = is_binary_file_url($url, false, $config['project_url'], $headers, true);

			$check['type'] = 'protected_link';
			$check['url'] = $url;
			$check['details'] = "$url\n\n$headers";
			if (!$check_result)
			{
				$check['is_error'] = 1;
				debug_server("ERROR protected link is not working\nURL: $url\nHEADERS: $headers", $server_data);
			}
			$format_record['checks'][] = $check;

			$zip_files = get_album_zip_files($image['album_id'], $image['zip_files'], $server_data['group_id']);
			if ($format['group_id'] == 1)
			{
				if (isset($zip_files[$format['size']]))
				{
					$check = [];
					$url = "$server_data[urls]/$file_prefix/$dir_path/$image[album_id]/" . $zip_files[$format['size']]['file_name'];

					if ($format['access_level_id'] == 0)
					{
						unset($headers);
						$check_result = is_binary_file_url($url, false, $config['project_url'], $headers, true);

						$check['type'] = 'direct_link2';
						$check['url'] = $url;
						$check['details'] = "$url\n\n$headers";
						if (!$check_result)
						{
							$check['is_error'] = 1;
							debug_server("ERROR direct link is not working\nURL: $url\nHEADERS: $headers", $server_data);
						}
					} else
					{
						unset($headers);
						$check_result = is_working_url($url, '', $headers);

						$check['type'] = 'direct_link';
						$check['url'] = $url;
						$check['details'] = "$url\n\n$headers";
						if ($check_result)
						{
							$check['is_error'] = 1;
						}
					}
					$format_record['checks'][] = $check;

					$check = [];
					$time = time();
					$url = $zip_files[$format['size']]['file_url'] . "?admin_rq_server_id=$server_data[server_id]&ttl=$time&dsc=" . md5("$config[cv]/{$zip_files[$format['size']]['file_path']}/$time");

					unset($headers);
					$check_result = is_binary_file_url($url, false, $config['project_url'], $headers, true);

					$check['type'] = 'protected_link';
					$check['url'] = $url;
					$check['details'] = "$url\n\n$headers";
					if (!$check_result)
					{
						$check['is_error'] = 1;
						debug_server("ERROR protected link is not working\nURL: $url\nHEADERS: $headers", $server_data);
					}
					$format_record['checks'][] = $check;
				}
			}
		}

		$validation_result[] = $format_record;
	}
	return $validation_result;
}

function ftp_check_connection($conn_id, $server_data)
{
	if (!@ftp_pwd($conn_id))
	{
		debug_server('ERROR: connection failure', $server_data);
		$conn_id = ftp_get_connect_id($server_data, true);
	}
	return $conn_id;
}

function ftp_get_connect_id($server_data, $reconnect)
{
	global $GLOBAL_FTP_SERVERS;

	$key = "storage_$server_data[server_id]";
	if ($server_data['max_tasks'])
	{
		// this is conversion server
		$key = "conversion_$server_data[server_id]";
	} elseif ($server_data['last_check_album_id'])
	{
		// this is albums server
		$key = "albums_$server_data[server_id]";
	}

	if ($reconnect || !isset($GLOBAL_FTP_SERVERS[$key]))
	{
		if (isset($GLOBAL_FTP_SERVERS[$key]))
		{
			debug_server('closed connection on reconnect', $server_data);
			@ftp_close($GLOBAL_FTP_SERVERS[$key]);
			unset($GLOBAL_FTP_SERVERS[$key]);
		}

		if ($reconnect)
		{
			debug_server('reconnecting to server...', $server_data);
		} else
		{
			debug_server('connecting to server...', $server_data);
		}

		$ftp_host = $server_data['ftp_host'];
		$ftp_port = intval($server_data['ftp_port']) > 0 ? intval($server_data['ftp_port']) : 21;
		$ftp_timeout = intval($server_data['ftp_timeout']) > 0 ? intval($server_data['ftp_timeout']) : 10;

		$conn_id = @ftp_connect($ftp_host, $ftp_port, $ftp_timeout);
		if (!$conn_id)
		{
			debug_server("ERROR: failed to establish connection to $ftp_host:$ftp_port", $server_data);
			return null;
		}

		if (@ftp_login($conn_id, $server_data['ftp_user'], $server_data['ftp_pass']))
		{
			debug_server('logged in', $server_data);
			if (!@ftp_pasv($conn_id, true))
			{
				debug_server('WARN: failed to turn passive mode on', $server_data);
			}
			$GLOBAL_FTP_SERVERS[$key] = $conn_id;
			return $conn_id;
		} else
		{
			debug_server("ERROR: failed to log in as $server_data[ftp_user]", $server_data);
		}
		return null;
	}
	return $GLOBAL_FTP_SERVERS[$key];
}

function ftp_disconnect($server_data)
{
	global $GLOBAL_FTP_SERVERS;

	$key = "storage_$server_data[server_id]";
	if ($server_data['max_tasks'])
	{
		// this is conversion server
		$key = "conversion_$server_data[server_id]";
	} elseif ($server_data['last_check_album_id'])
	{
		// this is albums server
		$key = "albums_$server_data[server_id]";
	}
	if (isset($GLOBAL_FTP_SERVERS[$key]))
	{
		@ftp_close($GLOBAL_FTP_SERVERS[$key]);
		unset($GLOBAL_FTP_SERVERS[$key]);
		debug_server('closed connection', $server_data);
	}
}

function debug_server($message, $server_data)
{
	global $config;

	if ($server_data['is_logging_enabled'] == 1 && $server_data['server_id'] > 0)
	{
		$log_file_name = "debug_storage_server_$server_data[server_id].txt";
		if ($server_data['max_tasks'])
		{
			// this is conversion server
			$log_file_name = "debug_conversion_server_$server_data[server_id].txt";
		} elseif ($server_data['last_check_album_id'])
		{
			// this is albums server
			$log_file_name = "debug_albums_server_$server_data[server_id].txt";
		}

		$process_id = getmypid();
		file_put_contents("$config[project_path]/admin/logs/$log_file_name", date('[Y-m-d H:i:s]') . " [$process_id] $message\n", FILE_APPEND | LOCK_EX);
	}
}

function disconnect_all_servers()
{
	global $GLOBAL_FTP_SERVERS;

	foreach ($GLOBAL_FTP_SERVERS as $k => $conn_id)
	{
		@ftp_close($conn_id);
		unset($GLOBAL_FTP_SERVERS[$k]);
	}
}
