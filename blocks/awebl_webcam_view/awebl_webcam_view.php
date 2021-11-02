<?php
function awebl_webcam_viewShow($block_config, $object_id)
{
	global $config, $smarty, $storage;

	if (!is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
	{
		return '';
	}
	require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";

	if (trim($_REQUEST[$block_config['var_webcam_dir']]))
	{
		$webcam_dir = trim($_REQUEST[$block_config['var_webcam_dir']]);
		$data = awe_black_labelQueryAPI('GET', "performers/$webcam_dir");
		$data = $data['data'];
		if (!$data['id'])
		{
			return 'status_404';
		}

		if (!class_exists('Mobile_Detect'))
		{
			include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
		}
		if (class_exists('Mobile_Detect'))
		{
			$mobiledetect = new Mobile_Detect();
			if ($mobiledetect->isTablet())
			{
				$data['chatScriptUrl'] .= '&device=tablet';
			} elseif ($mobiledetect->isMobile())
			{
				$data['chatScriptUrl'] .= '&device=mobile';
			}
		}

		$awebl_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/awe_black_label/data.dat"));
		if ($awebl_data['primary_button_bg'])
		{
			$data['chatScriptUrl'] .= '&primaryButtonBg=' . urlencode(strtolower(str_replace('#', '', $awebl_data['primary_button_bg'])));
		}
		if ($awebl_data['primary_button_color'])
		{
			$data['chatScriptUrl'] .= '&primaryButtonColor=' . urlencode(strtolower(str_replace('#', '', $awebl_data['primary_button_color'])));
		}
		if ($awebl_data['terms_link_color'])
		{
			$data['chatScriptUrl'] .= '&termsLinkColor=' . urlencode(strtolower(str_replace('#', '', $awebl_data['terms_link_color'])));
		}
		if ($awebl_data['terms_toggle_color'])
		{
			$data['chatScriptUrl'] .= '&termsToggleColor=' . urlencode(strtolower(str_replace('#', '', $awebl_data['terms_toggle_color'])));
		}

		$data['appearance'] = ($data['appearance'] ? array_map('trim', explode(',', $data['appearance'])) : []);
		$data['specialLocation'] = ($data['specialLocation'] ? array_map('trim', explode(',', $data['specialLocation'])) : []);
		$data['language'] = ($data['language'] ? array_map('trim', explode(',', $data['language'])) : []);
		$data['willingness'] = ($data['willingness'] ? array_map('trim', explode(',', $data['willingness'])): []);

		if (isset($block_config['show_videos']))
		{
			$params = [];
			switch($block_config['show_videos'])
			{
				case 1:
					$params['privacy'] = 'public';
					break;
				case 2:
					$params['privacy'] = 'exclusive';
					break;
				default:
					$params['privacy'] = 'all';
			}
			$videos = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/videos", $params);
			if (is_array($videos['data']['items']))
			{
				$data['videos'] = $videos['data']['items'];
			}
		}
		if (isset($block_config['show_albums']))
		{
			$params = ['type' => 'image'];
			switch($block_config['show_albums'])
			{
				case 1:
					$params['privacy'] = 'public';
					break;
				case 2:
					$params['privacy'] = 'exclusive';
					break;
				default:
					$params['privacy'] = 'all';
			}
			$albums = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/albums", $params);
			if (is_array($albums['data']['albums']))
			{
				$data['albums'] = $albums['data']['albums'];
			}
		}
	} else
	{
		return '';
	}

	$storage[$object_id]['id'] = $data['id'];
	$storage[$object_id]['nick'] = $data['nick'];
	$storage[$object_id]['price'] = $data['price'];
	$storage[$object_id]['personAge'] = $data['personAge'];
	$storage[$object_id]['region'] = $data['region'];
	$storage[$object_id]['averageRating'] = $data['averageRating'];
	$storage[$object_id]['profilePictures'] = $data['profilePictures'];
	$storage[$object_id]['willingness'] = implode(', ', $data['willingness']);

	$smarty->assign("data", $data);

	header('X-BL-Type: KVS');
	return '';
}

function awebl_webcam_viewGetHash($block_config)
{
	return "nocache";
}

function awebl_webcam_viewCacheControl($block_config)
{
	return "nocache";
}

function awebl_webcam_viewAsync($block_config)
{
	global $config;

	if (!is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
	{
		return;
	}
	require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";

	$_REQUEST['format'] = 'json';
	if ($_REQUEST['action'] == 'init_payment')
	{
		if ($_SESSION['awebl_user_status']['purchaseUrl'])
		{
			$_SESSION['awebl_return_url'] = $_SERVER['HTTP_REFERER'];
			header("Location: {$_SESSION['awebl_user_status']['purchaseUrl']}");
		} else
		{
			header("Location: {$_SERVER['HTTP_REFERER']}");
		}
		die;
	} elseif ($_REQUEST['action'] == 'album_images' && trim($_REQUEST['id']) && trim($_REQUEST[$block_config['var_webcam_dir']]))
	{
		$webcam_dir = trim($_REQUEST[$block_config['var_webcam_dir']]);
		$id = trim($_REQUEST['id']);
		$images = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/albums/$id/items", []);

		header("Content-type: application/json");
		echo ($images['data'] && $images['data'] ? json_encode($images['data']) : []);
		die;
	} elseif ($_REQUEST['action'] == 'video_files' && trim($_REQUEST['id']) && trim($_REQUEST[$block_config['var_webcam_dir']]))
	{
		$webcam_dir = trim($_REQUEST[$block_config['var_webcam_dir']]);
		$id = trim($_REQUEST['id']);
		$files = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/videos/$id", []);

		header("Content-type: application/json");
		echo ($files['data'] && $files['data'] ? json_encode($files['data']) : []);
		die;
	} elseif ($_REQUEST['action'] == 'purchase_album' && trim($_REQUEST['id']) && trim($_REQUEST[$block_config['var_webcam_dir']]))
	{
		$webcam_dir = trim($_REQUEST[$block_config['var_webcam_dir']]);
		$id = trim($_REQUEST['id']);

		$result = awe_black_labelQueryAPI('POST', "purchases/album", ['performerNick' => $webcam_dir, 'id' => $id], true);
		if ($result['code'] == 201 || $result['code'] == 303)
		{
			$result = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/albums/$id/items", []);
			$result['data']['status'] = 'success';
			header("Content-type: application/json");
			echo ($result['data'] ? json_encode($result['data']) : []);
		} else
		{
			async_return_request_status([['error_code'=>'invalid_params', 'block'=>'awebl_webcam_view']]);
		}
		die;
	} elseif ($_REQUEST['action'] == 'purchase_video' && trim($_REQUEST['id']) && trim($_REQUEST[$block_config['var_webcam_dir']]))
	{
		$webcam_dir = trim($_REQUEST[$block_config['var_webcam_dir']]);
		$id = trim($_REQUEST['id']);

		$result = awe_black_labelQueryAPI('POST', "purchases/video", ['performerNick' => $webcam_dir, 'id' => $id], true);
		if ($result['code'] == 201 || $result['code'] == 303)
		{
			$result = awe_black_labelQueryAPI('GET', "performers/$webcam_dir/videos/$id", []);
			$result['data']['status'] = 'success';
			header("Content-type: application/json");
			echo ($result['data'] ? json_encode($result['data']) : []);
		} else
		{
			async_return_request_status([['error_code'=>'invalid_params', 'block'=>'awebl_webcam_view']]);
		}
		die;
	}
}

function awebl_webcam_viewMetaData()
{
	return array(
		// object context
			array("name" => "var_webcam_dir", "group" => "object_context", "type" => "STRING", "is_required" => 1, "default_value" => "dir"),

		// additional data
			array("name"=>"show_videos", "group"=>"additional_data", "type"=>"CHOICE[0,1,2]", "is_required"=>0),
			array("name"=>"show_albums", "group"=>"additional_data", "type"=>"CHOICE[0,1,2]", "is_required"=>0),
	);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
