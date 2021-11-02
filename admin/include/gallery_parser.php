<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
function parse_gallery($page_url)
{
	global $config;
	$urls = array();

	$code = str_replace(array("\n", "\r"), " ", get_page('', $page_url, '', '', 0, 1, 50, '', array('dont_follow' => true)));
	preg_match("|.+Location:(.+?)\ |is", $code, $temp);
	if (trim($temp[1]) <> '')
	{
		$page_url = trim($temp[1]);
	}

	$code = get_page('', $page_url, '', '', 1, 0, 50, '');

	preg_match_all("{<base[^>]*?href\ *?=[\ '\"]*?http:([^>]*?)[\ '\">].*?>}is", $code, $temp);
	$base_href = "http:" . @$temp[1][0];

	$video_allowed_ext = explode(",", $config['video_allowed_ext']);
	foreach ($video_allowed_ext as $ext)
	{
		$ext = trim($ext);
		if ($ext == '')
		{
			continue;
		}
		preg_match_all("{href\ *=[\ '\"]*([^>]*?\.($ext)([\?][^\ '\"]*)?)}is", $code, $temp);

		if (count($temp[1]) == 0)
		{
			preg_match_all("{src\ *=[\ '\"]*([^>]*?\.($ext)([\?][^\ '\"]*)?)}is", $code, $temp);
		}
		if (count($temp[1]) == 0)
		{
			preg_match_all("{=([^>=&'\"]*?\.($ext))}is", $code, $temp);
		}
		if (count($temp[1]) == 0)
		{
			preg_match_all("{:\ *['\"]([^>=&'\"]*?\.($ext))['\"]}is", $code, $temp);
		}
		if (count($temp[1]) > 0)
		{
			break;
		}
	}

	foreach ($temp[1] as $video_url)
	{
		$urls[] = normalize_url($page_url, $video_url, $base_href);
	}

	return array_unique($urls);
}

function parse_image_gallery($page_url)
{
	$urls = array();

	$code = str_replace(array("\n", "\r"), " ", get_page('', $page_url, '', '', 0, 1, 50, '', array('dont_follow' => true)));
	preg_match("|.+Location:(.+?)\ |is", $code, $temp);
	if (trim($temp[1]) <> '')
	{
		$page_url = trim($temp[1]);
	}

	$code = get_page('', $page_url, '', '', 1, 0, 50, '');

	preg_match_all("{<base[^>]*?href\ *?=[\ '\"]*?http:([^>]*?)[\ '\">].*?>}is", $code, $temp);
	$base_href = "http:" . @$temp[1][0];

	preg_match_all("|href\ *=\ *['\"\ ]*([^\"'<>]+?\.jpg[^\"'<>\ ]*)['\"\ ]*|is", $code, $temp);

	foreach ($temp[1] as $image_url)
	{
		$urls[] = normalize_url($page_url, $image_url, $base_href);
	}
	if (count($temp[1]) == 0)
	{
		unset($temp);
		preg_match_all("|href\ *=\ *['\"\ ]*([^\"'<>]+?\.jpeg[^\"'<>\ ]*)['\"\ ]*|is", $code, $temp);

		foreach ($temp[1] as $image_url)
		{
			$urls[] = normalize_url($page_url, $image_url, $base_href);
		}
	}

	return array_unique($urls);
}

function normalize_url($url, $file_name, $base_href)
{
	$url = trim($url);
	if (strpos($file_name, '\/'))
	{
		$file_name = str_replace('\/', '/', $file_name);
	}
	if (strpos($file_name, '&amp;'))
	{
		$file_name = str_replace('&amp;', '&', $file_name);
	}
	$file_name = trim($file_name);
	$base_href = trim($base_href);

	$url_array = explode("?", $url);
	$url = $url_array[0];

	if (strpos($file_name, "http://") !== false || strpos($file_name, "https://") !== false)
	{
		$thumb_url = $file_name;
	} else
	{
		if ($url{strlen($url) - 1} == "/")
		{
			$url_dir = $url;
		} else
		{
			$temp = explode("/", $url);
			unset($temp[count($temp) - 1]);
			$url_dir = implode("/", $temp);
		}

		$url_dir = trim($url_dir);
		if ($url_dir{strlen($url_dir) - 1} != '/')
		{
			$url_dir .= "/";
		}

		if (strlen($base_href) > 10)
		{
			$url_dir = $base_href;
			if ($url_dir{strlen($url_dir) - 1} != '/')
			{
				$url_dir .= "/";
			}
		}

		if (strpos($file_name, "/") === 0)
		{
			$page_url_scheme = parse_url($url_dir, PHP_URL_SCHEME);
			$page_url_host = parse_url($url_dir, PHP_URL_HOST);
			$thumb_url = "$page_url_scheme://$page_url_host" . $file_name;
		} else
		{
			$ss_count = substr_count($file_name, "../");
			if ($ss_count > 0)
			{
				$url_array = explode("/", $url_dir);
				$url_dir = '';
				for ($it = 0; $it < (count($url_array) - $ss_count - 1); $it++)
				{
					$url_dir .= $url_array[$it] . "/";
				}
				$file_name = str_replace("../", "", $file_name);
			}
			$thumb_url = $url_dir . $file_name;
		}
	}

	return trim(str_replace("\n", "", $thumb_url));
}
