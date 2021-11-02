<?php

function awe_black_labelInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins", 0777);
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path, 0777);
		chmod($plugin_path, 0777);
	}
	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
}

function awe_black_labelIsEnabled()
{
	global $config;

	awe_black_labelInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (is_array($data) && $data['white_label_url'] && $data['app_secret'])
	{
		return true;
	}

	return false;
}

function awe_black_labelShow()
{
	global $config, $lang, $errors, $page_name;

	awe_black_labelInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

	$errors = null;

	if ($_GET['action'] == 'get_log')
	{
		$log_file = "$config[project_path]/admin/logs/plugins/awe_black_label.txt";
		header("Content-Type: text/plain; charset=utf8");
		if (is_file($log_file))
		{
			$log_size = sprintf("%.0f", filesize($log_file));
			if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
			{
				$fh = fopen($log_file, "r");
				fseek($fh, $log_size - 1024 * 1024);
				header("Content-Length: " . (1024 * 1024 + 29));
				echo "Showing last 1MB of file...\n\n";
				echo fread($fh, 1024 * 1024 + 1);
			} else
			{
				if (isset($_REQUEST['download']))
				{
					$log_file_name = basename($log_file);
					header("Content-Disposition: attachment; filename=\"$log_file_name\"");
				}
				header("Content-Length: $log_size");
				readfile($log_file);
			}
		}
		die;
	}

	if ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}
		$_POST['white_label_url'] = rtrim($_POST['white_label_url'], '/');

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
		}

		if ($_POST['white_label_url'])
		{
			$url_ok = validate_field('url', $_POST['white_label_url'], $lang['plugins']['awe_black_label']['field_white_label_url']);
			$key_ok = validate_field('empty', $_POST['app_secret'], $lang['plugins']['awe_black_label']['field_app_secret']);
			if ($url_ok + $key_ok == 2)
			{
				$languages = awe_black_labelQueryAPI('GET', 'languages', [], false, $_POST);
				if (@count($languages['data']['languages']) == 0)
				{
					$errors[] = str_replace("%1%", $lang['plugins']['awe_black_label']['field_white_label_url'], $lang['plugins']['awe_black_label']['validation_invalid_url']);
				} else
				{
					$_POST['languages'] = $languages['data']['languages'];
					if (!isset($_POST['language_code']))
					{
						$_POST['language_code'] = 'auto';
					}
				}
			}

			validate_field('empty_int', $_POST['member_status_refresh_interval'], $lang['plugins']['awe_black_label']['field_members_status_update']);
		}

		if (!is_array($errors))
		{
			file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
			if (intval($_POST['is_debug_enabled']) == 0)
			{
				@unlink("$config[project_path]/admin/logs/plugins/awe_black_label.txt");
			}

			if (!is_dir("$config[project_path]/admin/data/engine"))
			{
				mkdir("$config[project_path]/admin/data/engine");
				chmod("$config[project_path]/admin/data/engine", 0777);
			}
			if (!is_dir("$config[project_path]/admin/data/engine/site_plugins"))
			{
				mkdir("$config[project_path]/admin/data/engine/site_plugins");
				chmod("$config[project_path]/admin/data/engine/site_plugins", 0777);
			}
			file_put_contents("$config[project_path]/admin/data/engine/site_plugins/awe_black_label.dat", 1);

			$project_url=urlencode($config['project_url']);
			get_page('',"https://www.kernel-scripts.com/get_version/?url=$project_url&feature_plugin_awebl=1",'','',1,0,5,'');

			return_ajax_success("$page_name?plugin_id=awe_black_label");
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
}

function awe_black_labelPreSiteRequest()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data) || !$data['white_label_url'] || !$data['app_secret'])
	{
		return;
	}

	if (intval(@$_SESSION['user_id']) == 0)
	{
		return;
	}

	if (time() - intval(@$_SESSION['awebl_last_status_refresh']) > intval($data['member_status_refresh_interval']) * 60 || intval($_REQUEST['status_refresh']) == 1)
	{
		$data = awe_black_labelQueryAPI('POST', "users", ['partnerUserId' => intval($_SESSION['user_id']), 'displayName' => trim($_SESSION['display_name']), 'email' => trim($_SESSION['user_info']['email'])], true);
		if (is_array($data['data']) && isset($data['data']['userType']))
		{
			if (!class_exists('Mobile_Detect'))
			{
				include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
			}
			if (class_exists('Mobile_Detect'))
			{
				$mobiledetect = new Mobile_Detect();
				if ($mobiledetect->isTablet())
				{
					$data['data']['purchaseUrl'] .= '&device=tablet';
				} elseif ($mobiledetect->isMobile())
				{
					$data['data']['purchaseUrl'] .= '&device=mobile';
				}
			}

			$_SESSION['awebl_user_status'] = $data['data'];
		}
		$_SESSION['awebl_last_status_refresh'] = time();
	}

	if (intval($_REQUEST['status_refresh']) == 1 && isset($_SESSION['awebl_return_url']))
	{
		header("Location: {$_SESSION['awebl_return_url']}");
		unset($_SESSION['awebl_return_url']);
		die;
	}
}

function awe_black_labelPostSiteRequest()
{
	global $config;

	if ($_GET['action'] == 'confirm_email')
	{
		$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (!is_array($data) || !$data['white_label_url'] || !$data['app_secret'])
		{
			return;
		}

		$code = trim($_GET['code']);
		if ($code)
		{
			$confirm_code = mr2array_single(sql_pr("select * from $config[tables_prefix]users_confirm_codes where confirm_code=?", $code));
			if ($confirm_code['type_id'] == 2)
			{
				$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=?", $confirm_code['user_id']));
				awe_black_labelQueryAPI('PATCH', "users/" . intval($confirm_code['user_id']), ['partnerUserId' => intval($confirm_code['user_id']), 'email' => $email], true);
			}
		}
	}
}

function awe_black_labelPostAsyncRequest()
{
	global $config;

	if (($_POST['action'] == 'change_email' && trim($_POST['email']) == $_SESSION['user_info']['email']))
	{
		$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

		$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
		if (!is_array($data) || !$data['white_label_url'] || !$data['app_secret'])
		{
			return;
		}

		if (intval(@$_SESSION['user_id']) == 0)
		{
			return;
		}

		awe_black_labelQueryAPI('PATCH', "users/" . intval($_SESSION['user_id']), ['partnerUserId' => intval($_SESSION['user_id']), 'email' => trim($_SESSION['user_info']['email'])], true);
	}
}

function awe_black_labelQueryAPI($method, $endpoint, $params = [], $params_encode_json = false, $test_mode = null)
{
	global $config;

	awe_black_labelInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/awe_black_label";

	$is_system = false;
	$data = @unserialize(file_get_contents("$plugin_path/data.dat"));
	if (is_array($test_mode))
	{
		$data = $test_mode;
		$is_system = true;
	}
	if (!is_array($data) || !$data['white_label_url'] || !$data['app_secret'])
	{
		return [];
	}

	if (!in_array(strtoupper($method), array('GET', 'POST', 'PUT', 'DELETE', 'PATCH')))
	{
		return [];
	}

	$prefix = '';
	if (!$is_system && @$_SESSION['awebl_prefix'])
	{
		$prefix = trim($_SESSION['awebl_prefix']);
	}
	if (!$prefix)
	{
		if ($data['language_code'] == 'auto')
		{
			$available_languages = ['en', 'es', 'de', 'fr', 'it', 'pt', 'nl', 'sv', 'no', 'da', 'fi', 'ja', 'ru', 'hu', 'cs', 'sk', 'ro', 'pl', 'zh'];
			$user_languages = array_map('trim', explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
			foreach ($user_languages as $user_language)
			{
				foreach ($available_languages as $available_language)
				{
					if (strpos($user_language, $available_language) === 0)
					{
						$prefix = $available_language;
						break 2;
					}
				}
			}
		} else
		{
			$prefix = $data['language_code'];
		}
	}
	if (!$prefix)
	{
		$prefix = 'en';
	}
	$session_id = '';
	if (!$is_system && @$_SESSION['awebl_session_id'])
	{
		$session_id = trim($_SESSION['awebl_session_id']);
	}

	$is_debug_enabled = false;
	if (intval($data['is_debug_enabled']) == 1)
	{
		if (!$data['debug_ips'])
		{
			$is_debug_enabled = true;
		} else
		{
			$debug_ips = array_map('trim', explode(',', $data['debug_ips']));
			foreach ($debug_ips as $debug_ip)
			{
				if (ip2int($_SERVER['REMOTE_ADDR']) == ip2int($debug_ip))
				{
					$is_debug_enabled = true;
					break;
				}
			}
		}
	}

	$api_params = '';
	if (count($params) > 0)
	{
		if ($params_encode_json)
		{
			$api_params = json_encode($params);
		} else
		{
			$api_params = http_build_query($params);
		}
	}

	$api_url = "$data[white_label_url]/$prefix/api/v1/$endpoint";
	if (strtoupper($method) == 'GET' && $api_params)
	{
		$api_url .= "?$api_params";
	}

	$headers = array(
			"Accept: application/json",
			"X-Application-Secret: $data[app_secret]",
			"X-Client-Ip: $_SERVER[REMOTE_ADDR]",
			"X-User-Agent: $_SERVER[HTTP_USER_AGENT]"
	);

	if ($endpoint != "users")
	{
		$headers[] = "X-Session-Id: $session_id";
	}

	$curl = curl_init();
	curl_setopt_array($curl, array(
					CURLOPT_URL => $api_url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_FOLLOWLOCATION => 0,
					CURLOPT_TIMEOUT => 20,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => strtoupper($method),
					CURLOPT_HTTPHEADER => $headers,
			)
	);
	if (!$is_system)
	{
		curl_setopt($curl, CURLOPT_HEADERFUNCTION, static function ($curl, $header) {
			$header_parsed = array_map('trim', explode(':', $header, 2));
			switch (strtolower($header_parsed[0]))
			{
				case 'x-session-id':
					$_SESSION['awebl_session_id'] = $header_parsed[1];
					break;
				case 'x-api-prefix':
					$_SESSION['awebl_prefix'] = $header_parsed[1];
					break;
			}
			return strlen($header);
		});
	}
	if (strtoupper($method) != 'GET' && $api_params)
	{
		curl_setopt($curl, CURLOPT_POSTFIELDS, $api_params);
		if ($is_debug_enabled)
		{
			file_put_contents("$config[project_path]/admin/logs/plugins/awe_black_label.txt", "[" . date("Y-m-d H:i:s") . "] POST data -------------- \n\n$api_params\n\n", FILE_APPEND | LOCK_EX);
		}
	}

	$verbose = null;
	if ($is_debug_enabled)
	{
		$verbose = fopen('php://temp', 'w+');
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt($curl, CURLOPT_STDERR, $verbose);
	}

	$result = [];

	$response = curl_exec($curl);
	if ($response)
	{
		$response = @json_decode($response, true);
		if ($response && $response['data'])
		{
			$result['data'] = $response['data'];
		}
	}
	if (curl_errno($curl))
	{
		file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] [" . curl_errno($curl) . "] " . curl_error($curl) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($verbose)
	{
		rewind($verbose);
		file_put_contents("$config[project_path]/admin/logs/plugins/awe_black_label.txt", "[" . date("Y-m-d H:i:s") . "] CURL request -------------- \n\n" . stream_get_contents($verbose) . "\n", FILE_APPEND | LOCK_EX);
	}

	if ($response && $is_debug_enabled)
	{
		file_put_contents("$config[project_path]/admin/logs/plugins/awe_black_label.txt", "[" . date("Y-m-d H:i:s") . "] RESPONSE data -------------- \n\n" . json_encode($response['data']) . "\n\n", FILE_APPEND | LOCK_EX);
	}

	$result['code'] = curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
	curl_close($curl);

	return $result;
}

function awe_black_labelListBlocks()
{
	return ['awebl_list_categories', 'awebl_list_webcams', 'awebl_webcam_view'];
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
