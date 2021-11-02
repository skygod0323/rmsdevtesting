<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if (isset($_SERVER['HTTP_ORIGIN']))
{
	header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	header("Access-Control-Allow-Credentials: true");

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
	{
		die;
	}
}

$async_upload = false;
if (!isset($config))
{
	require_once 'setup.php';
	$async_upload = true;
}
require_once 'functions_base.php';

$url = trim($_REQUEST['url']);
$hash = trim($_REQUEST['filename']);

if ($async_upload)
{
	if ($url)
	{
		$result = uploader_upload_remote_file($hash, $url);
	} else
	{
		if (intval($_REQUEST['files']) > 0)
		{
			$result = uploader_upload_local_files($hash, $_FILES['content'], intval($_REQUEST['files']), intval($_REQUEST['index']));
		} else
		{
			$result = uploader_upload_local_file($hash, $_FILES['content'], trim($_REQUEST['size']), intval($_REQUEST['chunks']), intval($_REQUEST['index']));
		}
	}

	if ($result == 'ok' || strpos($result, 'ok_chunk') === 0 || strpos($result, 'ok_file') === 0)
	{
		header('Content-Type: application/json; charset=utf8');
		echo json_encode(['status' => 'success']);
		die;
	}

	if ($result == 'ktfudc_notallowed_error')
	{
		header('HTTP/1.0 403 Forbidden');
		die;
	}

	if (count($_POST) == 0)
	{
		echo json_encode(['status' => 'failure', 'error' => 'ktfudc_filesize_error']);
		die;
	}

	echo json_encode(['status' => 'failure', 'error' => $result]);
	die;
}

function uploader_upload_local_file(string $hash, ?array $fileinfo, string $filesize = '', int $chunks = 0, int $chunk_index = 0): string
{
	global $config;

	$file_upload_data = unserialize(file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"), ['allowed_classes' => false]);
	if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] != 'public')
	{
		session_start();
		if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'admins' && intval($_SESSION['userdata']['user_id']) < 1)
		{
			return 'ktfudc_unexpected_error';
		}
		if (intval($_SESSION['userdata']['user_id']) < 1)
		{
			if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'members' && intval($_SESSION['user_id']) < 1)
			{
				return 'ktfudc_notallowed_error';
			}
		}
	}

	if (preg_match('/^([0-9A-Za-z]{32})$/', $hash))
	{
		if (is_file("$config[temporary_path]/$hash.tmp"))
		{
			return 'ok';
		}

		if ($chunks > 0)
		{
			if (intval($fileinfo['error']) > 0)
			{
				if (intval($fileinfo['error']) == 1)
				{
					return 'ktfudc_filesize_error';
				} else
				{
					return 'ktfudc_unexpected_error';
				}
			}
			if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $filesize > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
			{
				return 'ktfudc_filesize_error';
			}

			$temp_dir = "$config[temporary_path]/$hash";

			if ($chunk_index > 0)
			{
				$chunk_file = "$temp_dir/$chunk_index.tmp";
				if (!is_dir($temp_dir))
				{
					mkdir($temp_dir);
					chmod($temp_dir, 0777);
				}
				if (is_file($chunk_file))
				{
					unlink($chunk_file);
				}

				if (isset($fileinfo['tmp_name']))
				{
					move_uploaded_file($fileinfo['tmp_name'], $chunk_file);
					if (is_file($chunk_file))
					{
						$loaded_percent = floor($chunk_index / $chunks * 100);
						return "ok_chunk_{$loaded_percent}%";
					}
				}
			} elseif ($filesize > 0)
			{
				set_time_limit(0);

				$target_file = "$config[temporary_path]/$hash.tmp";
				@unlink($target_file);
				for ($i = 1; $i <= $chunks; $i++)
				{
					$chunk_file = "$temp_dir/$i.tmp";
					if (is_file($chunk_file))
					{
						$fp = fopen($chunk_file, 'rb');
						$buff = fread($fp, filesize($chunk_file));
						fclose($fp);

						$fp = fopen($target_file, 'ab');
						fwrite($fp, $buff);
						fclose($fp);

						unlink($chunk_file);
					} else
					{
						break;
					}
				}

				@rmdir($temp_dir);
				if ($filesize == sprintf("%.0f", filesize($target_file)))
				{
					return 'ok';
				}
			}
		} else
		{
			if (intval($fileinfo['error']) > 0)
			{
				if (intval($fileinfo['error']) == 1)
				{
					return 'ktfudc_filesize_error';
				} else
				{
					return 'ktfudc_unexpected_error';
				}
			}

			if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $fileinfo['size'] > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
			{
				return 'ktfudc_filesize_error';
			}

			if (isset($fileinfo['tmp_name']))
			{
				move_uploaded_file($fileinfo['tmp_name'], "$config[temporary_path]/$hash.tmp");
				if (is_file("$config[temporary_path]/$hash.tmp"))
				{
					chmod("$config[temporary_path]/$hash.tmp", 0666);
					return 'ok';
				}
			}
		}
	}
	return 'ktfudc_unexpected_error';
}

function uploader_upload_local_files(string $hash, ?array $fileinfo, int $total_files = 0, int $file_index = 0, ?array $allowed_image_formats = null, int $allowed_min_image_width = 0, int $allowed_min_image_height = 0): string
{
	global $config;

	$file_upload_data = unserialize(file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"), ['allowed_classes' => false]);
	if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] != 'public')
	{
		session_start();
		if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'admins' && intval($_SESSION['userdata']['user_id']) < 1)
		{
			return 'ktfudc_unexpected_error';
		}
		if (intval($_SESSION['userdata']['user_id']) < 1)
		{
			if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'members' && intval($_SESSION['user_id']) < 1)
			{
				return 'ktfudc_notallowed_error';
			}
		}
	}

	if (preg_match('/^([0-9A-Za-z]{32})$/', $hash))
	{
		$temp_dir = "$config[temporary_path]/$hash";
		$starting_index = 1;
		if (!is_dir($temp_dir))
		{
			mkdir($temp_dir);
			chmod($temp_dir,0777);
		} else
		{
			$existing_files = get_contents_from_dir($temp_dir,1);
			foreach ($existing_files as $existing_file)
			{
				if (substr($existing_file, -4) == '.tmp')
				{
					$starting_index++;
				}
			}
		}

		if ($total_files > 0)
		{
			if ($file_index > 0)
			{
				$target_file = "$temp_dir/" . str_pad($starting_index,5,'0',STR_PAD_LEFT) . '.tmp';
				if (!is_dir($temp_dir))
				{
					mkdir($temp_dir);
					chmod($temp_dir, 0777);
				}
				if (is_file($target_file))
				{
					unlink($target_file);
				}

				if (isset($fileinfo['tmp_name']))
				{
					if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $fileinfo['size'] > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
					{
						$is_file_accepted = false;
					} else
					{
						$is_file_accepted = true;
						if (is_array($allowed_image_formats))
						{
							$is_file_accepted = false;
							$size = getimagesize($fileinfo['tmp_name']);
							if (in_array(str_replace('image/', '', $size['mime']), $allowed_image_formats))
							{
								if ($size[0] >= $allowed_min_image_width && $size[1] > $allowed_min_image_height)
								{
									$is_file_accepted = true;
								}
							}
						}

					}

					$loaded_percent = floor($file_index / $total_files * 100);
					if ($is_file_accepted)
					{
						move_uploaded_file($fileinfo['tmp_name'], $target_file);
						if (is_file($target_file))
						{
							return "ok_file_{$loaded_percent}%";
						}
					} else
					{
						file_put_contents("$temp_dir/skipped.txt", "$fileinfo[name]\n", LOCK_EX | FILE_APPEND);
						return "skipped_file_{$loaded_percent}%";
					}
				}
			} else
			{
				return 'ok';
			}
		} else
		{
			if (is_array($fileinfo['tmp_name']))
			{
				$file_index = $starting_index;
				$skipped_list = [];
				foreach ($fileinfo['tmp_name'] as $file_info_key => $tmp_name)
				{
					if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $fileinfo['size'][$file_info_key] > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
					{
						$skipped_list[] = $fileinfo['name'][$file_info_key];
					} else
					{
						$is_file_accepted = true;
						if (is_array($allowed_image_formats))
						{
							$is_file_accepted = false;
							$size = getimagesize($tmp_name);
							if (in_array(str_replace('image/', '', $size['mime']), $allowed_image_formats))
							{
								if ($size[0] >= $allowed_min_image_width && $size[1] > $allowed_min_image_height)
								{
									$is_file_accepted = true;
								}
							}
						}

						$target_file = "$temp_dir/" . str_pad($file_index,5,'0',STR_PAD_LEFT) . '.tmp';
						if ($is_file_accepted && move_uploaded_file($tmp_name, $target_file))
						{
							$file_index++;
						} else
						{
							$skipped_list[] = $fileinfo['name'][$file_info_key];
						}
					}
				}
				if (count($skipped_list) > 0)
				{
					file_put_contents("$temp_dir/skipped.txt", implode("\n", $skipped_list) . "\n", LOCK_EX | FILE_APPEND);
				}
				return 'ok';
			}
		}
	}
	return 'ktfudc_unexpected_error';
}

function uploader_upload_remote_file(string $hash, string $url, bool $is_wait = false): string
{
	global $config;

	$file_upload_data = unserialize(file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"), ['allowed_classes' => false]);
	if ($file_upload_data['FILE_UPLOAD_URL_OPTION'] != 'public')
	{
		session_start();
		if ($file_upload_data['FILE_UPLOAD_URL_OPTION'] == 'admins' && intval($_SESSION['userdata']['user_id']) < 1)
		{
			return 'ktfudc_unexpected_error';
		}
		if (intval($_SESSION['userdata']['user_id']) < 1)
		{
			if ($file_upload_data['FILE_UPLOAD_URL_OPTION'] == 'members' && intval($_SESSION['user_id']) < 1)
			{
				return 'ktfudc_notallowed_error';
			}
		}
	}

	if (preg_match('/^([0-9A-Za-z]{32})$/', $hash) && $url)
	{
		if (@is_file("$config[temporary_path]/{$hash}_wget_log.txt"))
		{
			$wget_log = file_get_contents("$config[temporary_path]/{$hash}_wget_log.txt");
			if (strpos($wget_log, 'ERROR 4') !== false || strpos($wget_log, 'ERROR 5') !== false || strpos($wget_log, 'unable to resolve host address') !== false)
			{
				return 'ktfudc_url_error';
			}

			unset($temp);
			$length = 0;
			preg_match_all('/Length: ([0-9]+)/', $wget_log, $temp);
			settype($temp[1], 'array');
			if (count($temp[1]) > 0)
			{
				foreach ($temp[1] as $v)
				{
					if (intval($v) > $length)
					{
						$length = intval($v);
					}
				}
			}
			if ($length > 0)
			{
				$loaded = 0;
				$wget_log = strrev($wget_log);
				unset($temp);
				preg_match('/%([0-9]+)/', $wget_log, $temp);
				if (intval(strrev($temp[1])) == 100)
				{
					if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $length > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
					{
						return 'ktfudc_filesize_error';
					}
					return 'ok';
				} else
				{
					unset($temp);
					preg_match('/K([0-9]+)/', $wget_log, $temp);
					if (intval(strrev($temp[1])) > 0)
					{
						$loaded = intval(strrev($temp[1])) * 1024;
					}
					$loaded_percent = number_format($loaded / $length * 100, 2, '.', '');
					if ($loaded_percent == '0.00' && strpos($wget_log, '.. K0') !== false)
					{
						$loaded_percent = '0.01';
					}
					return "ok_chunk_{$loaded_percent}%";
				}
			}

			return 'ok_chunk';
		}

		$wget_path = $config['wget_path'];
		if ($wget_path == '' || $wget_path == 'disabled')
		{
			$wget_path = 'wget';
		}

		$url = str_replace(" ", "%20", $url);

		$headers = get_page("", $url, "", "", 0, 1, 20, "");
		unset($temp);
		preg_match('/Content-Length: ([0-9]+)/', $headers, $temp);
		$length = sprintf("%.0f", $temp[1]);

		if ($length > 1)
		{
			if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $length > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
			{
				file_put_contents("$config[temporary_path]/$hash.status", json_encode(['status' => 'failure', 'error' => 'ktfudc_filesize_error']), LOCK_EX);
				return 'ktfudc_filesize_error';
			}
		}

		$limit_rate_options = '';
		if (intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT']) > 0)
		{
			$limit_rate_options = '--limit-rate ' . intval($file_upload_data['FILE_DOWNLOAD_SPEED_LIMIT'] / 8) . 'k';
		}

		unset($temp);
		file_put_contents("$config[temporary_path]/{$hash}_wget_log.txt", "$url\n", LOCK_EX);
		exec("$wget_path $limit_rate_options --timeout 60 --no-use-server-timestamps --no-check-certificate --server-response -O $config[temporary_path]/$hash.tmp -a $config[temporary_path]/{$hash}_wget_log.txt -b " . escapeshellarg($url), $temp);

		$temp = implode("\n", $temp);
		if ($is_wait)
		{
			if (strpos($temp, "in background") !== false)
			{
				session_write_close();
				for ($i = 0; $i < 1000; $i++)
				{
					if (@is_file("$config[temporary_path]/{$hash}_wget_log.txt"))
					{
						$wget_log = file_get_contents("$config[temporary_path]/{$hash}_wget_log.txt");
						if (strpos($wget_log, 'ERROR 4') !== false || strpos($wget_log, 'ERROR 5') !== false || strpos($wget_log, 'unable to resolve host address') !== false)
						{
							return 'ktfudc_url_error';
						}

						if (strpos($wget_log, ".tmp' saved "))
						{
							return 'ok';
						}
						$wget_log = strrev($wget_log);
						unset($temp);
						preg_match('/%([0-9]+)/', $wget_log, $temp);
						if (intval(strrev($temp[1])) == 100)
						{
							return 'ok';
						}
					} elseif ($i > 100)
					{
						return 'ktfudc_unexpected_error';
					}
					sleep(1);
				}
			} else
			{
				if (is_file("$config[temporary_path]/$hash.tmp"))
				{
					return 'ok';
				}
			}
		} else
		{
			return 'ok_chunk';
		}
	}
	return 'ktfudc_unexpected_error';
}
