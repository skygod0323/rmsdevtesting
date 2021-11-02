<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function kvs_parse_feed($url, $feed_config)
{
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<video>(.*?)</video>|is", $feed_contents, $temp);

	$result = [];
	if (is_array($temp[1]))
	{
		foreach ($temp[1] as $item)
		{
			$video_record = kvs_parse_item($item, $feed_config);
			$result[] = $video_record;
		}
	}

	return $result;
}

function kvs_check_feed_content($url, $feed_config)
{
	if (strpos($url, '?') === false)
	{
		$url .= '?kvs_test_feed=true';
	} else
	{
		$url .= '&kvs_test_feed=true';
	}
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<video>(.*?)</video>|is", $feed_contents, $temp);
	if (is_array($temp[1]) && count($temp[1]) > 0)
	{
		return kvs_parse_item($temp[1][0], $feed_config);
	}

	return null;
}

function kvs_format_feed($videos, $feed_config)
{
	global $config, $languages;

	header("Content-Type: text/xml; charset=utf-8");
	$result = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	$result .= "<videos>\n";

	foreach ($videos as $video)
	{
		$item_result = '';
		$item_result .= "\t<video>\n";
		$item_result .= "\t\t" . kvs_format_feed_tag('id', $video['video_id']) . "\n";
		$item_result .= "\t\t" . kvs_format_feed_tag('title', $video['title']) . "\n";
		if ($feed_config['enable_localization'] == 1 && isset($languages))
		{
			foreach ($languages as $language)
			{
				if ($video["title_$language[code]"])
				{
					$item_result .= "\t\t" . kvs_format_feed_tag("title_$language[code]", $video["title_$language[code]"]) . "\n";
				}
			}
		}
		$item_result .= "\t\t" . kvs_format_feed_tag('dir', $video['dir']) . "\n";
		if ($feed_config['enable_localization'] == 1 && isset($languages))
		{
			foreach ($languages as $language)
			{
				if ($video["dir_$language[code]"])
				{
					$item_result .= "\t\t" . kvs_format_feed_tag("dir_$language[code]", $video["dir_$language[code]"]) . "\n";
				}
			}
		}
		if ($video['description'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('description', $video['description']) . "\n";
			if ($feed_config['enable_localization'] == 1 && isset($languages))
			{
				foreach ($languages as $language)
				{
					if ($video["description_$language[code]"])
					{
						$item_result .= "\t\t" . kvs_format_feed_tag("description_$language[code]", $video["description_$language[code]"]) . "\n";
					}
				}
			}
		}
		$item_result .= "\t\t" . kvs_format_feed_tag('rating', round($video['rating'] * 10) / 10) . "\n";
		$item_result .= "\t\t" . kvs_format_feed_tag('votes', $video['votes']) . "\n";
		$item_result .= "\t\t" . kvs_format_feed_tag('popularity', $video['popularity']) . "\n";
		if ($video['release_year'] > 0)
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('release_year', $video['release_year']) . "\n";
		}
		$item_result .= "\t\t" . kvs_format_feed_tag('post_date', $video['post_date']) . "\n";
		if ($feed_config['video_content_type_id'] == 1 || $feed_config['video_content_type_id'] == 3)
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('duration', $video['duration']) . "\n";
		}
		if ($video['cs_title'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('content_source', $video['cs_title']) . "\n";
		}
		if ($video['cs_url'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('content_source_url', $video['cs_url']) . "\n";
		}
		if ($video['cs_group'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('content_source_group', $video['cs_group']) . "\n";
		}
		if ($video['user_title'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('user', $video['user_title']) . "\n";
		}
		if ($video['dvd_title'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('dvd', $video['dvd_title']) . "\n";
		}
		if ($video['dvd_group'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('dvd_group', $video['dvd_group']) . "\n";
		}
		if ($video['website_link'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('link', $video['website_link']) . "\n";
		}
		if (is_array($video['tags']) && count($video['tags']) > 0)
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('tags', implode(',', $video['tags'])) . "\n";
		}
		if (is_array($video['categories']) && count($video['categories']) > 0)
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('categories', implode(',', $video['categories'])) . "\n";
		}
		if (is_array($video['models']) && count($video['models']) > 0)
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('models', implode(',', $video['models'])) . "\n";
		}
		if ($video['embed'])
		{
			$item_result .= "\t\t" . kvs_format_feed_tag('embed', $video['embed']) . "\n";
		}
		for ($i = 1; $i <= 3; $i++)
		{
			if ($video["custom{$i}"])
			{
				$item_result .= "\t\t" . kvs_format_feed_tag("custom{$i}", $video["custom{$i}"]) . "\n";
			}
		}
		if (is_array($video['video_formats']))
		{
			$item_result .= "\t\t<formats>\n";
			foreach ($video['video_formats'] as $video_format)
			{
				$item_result .= "\t\t\t<format>\n";
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('postfix', $video_format['postfix']) . "\n";
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('duration', $video_format['duration']) . "\n";
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('width', $video_format['dimensions'][0]) . "\n";
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('height', $video_format['dimensions'][1]) . "\n";
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('filesize', $video_format['file_size']) . "\n";
				if ($feed_config['video_content_type_id'] == 4)
				{
					$time = time();
					$item_result .= "\t\t\t\t" . kvs_format_feed_tag('url', "$video_format[file_url]?ttl=$time&dsc=" . md5("$config[cv]/{$video_format['file_path']}/$time")) . "\n";
				} else
				{
					$item_result .= "\t\t\t\t" . kvs_format_feed_tag('url', $video_format['file_url']) . "\n";
				}
				$item_result .= "\t\t\t</format>\n";
			}
			$item_result .= "\t\t</formats>\n";
		} elseif (is_array($video['hotlink_format']))
		{
			$item_result .= "\t\t<files>\n";
			$item_result .= "\t\t\t<file>\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('duration', $video['hotlink_format']['duration']) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('width', $video['hotlink_format']['dimensions'][0]) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('height', $video['hotlink_format']['dimensions'][1]) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('filesize', $video['hotlink_format']['file_size']) . "\n";
			if ($feed_config['video_content_type_id'] == 4)
			{
				$time = time();
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('url', "{$video['hotlink_format']['file_url']}?ttl=$time&dsc=" . md5("$config[cv]/{$video['hotlink_format']['file_path']}/$time")) . "\n";
			} else
			{
				$item_result .= "\t\t\t\t" . kvs_format_feed_tag('url', $video['hotlink_format']['file_url']) . "\n";
			}
			$item_result .= "\t\t\t</file>\n";
			$item_result .= "\t\t</files>\n";
		} elseif ($video['file_url'])
		{
			$dimensions = explode("x", $video['file_dimensions']);
			$item_result .= "\t\t<files>\n";
			$item_result .= "\t\t\t<file>\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('duration', $video['duration']) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('width', $dimensions[0]) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('height', $dimensions[1]) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('filesize', $video['file_size']) . "\n";
			$item_result .= "\t\t\t\t" . kvs_format_feed_tag('url', $video['file_url']) . "\n";
			$item_result .= "\t\t\t</file>\n";
			$item_result .= "\t\t</files>\n";
		}
		$item_result .= "\t\t<screens main=\"$video[screen_main]\">\n";
		for ($i = 1; $i <= $video['screen_amount']; $i++)
		{
			if ($feed_config['screenshot_sources'] == 1)
			{
				$item_result .= "\t\t\t" . kvs_format_feed_tag('screen', get_video_source_url($video['video_id'], "screenshots/$i.jpg")) . "\n";
			} elseif ($video['screen_url'])
			{
				$item_result .= "\t\t\t" . kvs_format_feed_tag('screen', "$video[screen_url]/$i.jpg") . "\n";
			}
		}
		$item_result .= "\t\t</screens>\n";
		if ($video['poster_amount'] > 0)
		{
			$item_result .= "\t\t<posters main=\"$video[poster_main]\">\n";
			for ($i = 1; $i <= $video['poster_amount']; $i++)
			{
				if ($feed_config['poster_sources'] == 1)
				{
					$item_result .= "\t\t\t" . kvs_format_feed_tag('poster', get_video_source_url($video['video_id'], "posters/$i.jpg")) . "\n";
				} elseif ($video['poster_url'])
				{
					$item_result .= "\t\t\t" . kvs_format_feed_tag('poster', "$video[poster_url]/$i.jpg") . "\n";
				}
			}
			$item_result .= "\t\t</posters>\n";
		}
		$item_result .= "\t</video>\n";

		$result .= $item_result;
	}

	$result .= "</videos>\n";

	return $result;
}

function kvs_parse_item($item, $feed_config)
{
	global $languages;

	$video_record = [];

	preg_match("|<id>(.*?)</id>|is", $item, $temp);
	$video_record['external_key'] = intval($temp[1]);

	preg_match("|<title>(.*?)</title>|is", $item, $temp);
	$video_record['title'] = kvs_parse_feed_tag($temp[1]);

	if (!isset($feed_config['fields']) || in_array('dir', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<dir>(.*?)</dir>|is", $item, $temp);
		$video_record['dir'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('description', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<description>(.*?)</description>|is", $item, $temp);
		$video_record['description'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('rating', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<rating>(.*?)</rating>|is", $item, $temp);
		$video_record['rating'] = floatval($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('votes', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<votes>(.*?)</votes>|is", $item, $temp);
		$video_record['votes'] = intval($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('popularity', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<popularity>(.*?)</popularity>|is", $item, $temp);
		$video_record['popularity'] = intval($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('release_year', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<release_year>(.*?)</release_year>|is", $item, $temp);
		$video_record['release_year'] = intval($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('post_date', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<post_date>(.*?)</post_date>|is", $item, $temp);
		$video_record['post_date'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('user', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<user>(.*?)</user>|is", $item, $temp);
		$video_record['user'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('content_source', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<content_source>(.*?)</content_source>|is", $item, $temp);
		$video_record['content_source'] = kvs_parse_feed_tag($temp[1]);

		preg_match("|<content_source_url>(.*?)</content_source_url>|is", $item, $temp);
		$video_record['content_source_url'] = kvs_parse_feed_tag($temp[1]);

		if (!isset($feed_config['fields']) || in_array('content_source_group', $feed_config['fields']) || in_array('all', $feed_config['fields']))
		{
			preg_match("|<content_source_group>(.*?)</content_source_group>|is", $item, $temp);
			$video_record['content_source_group'] = kvs_parse_feed_tag($temp[1]);
		}
	}

	if (!isset($feed_config['fields']) || in_array('dvd', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<dvd>(.*?)</dvd>|is", $item, $temp);
		$video_record['dvd'] = kvs_parse_feed_tag($temp[1]);

		if (!isset($feed_config['fields']) || in_array('dvd_group', $feed_config['fields']) || in_array('all', $feed_config['fields']))
		{
			preg_match("|<dvd_group>(.*?)</dvd_group>|is", $item, $temp);
			$video_record['dvd_group'] = kvs_parse_feed_tag($temp[1]);
		}
	}

	if (!isset($feed_config['fields']) || in_array('tags', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<tags>(.*?)</tags>|is", $item, $temp);
		$video_record['tags'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('categories', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<categories>(.*?)</categories>|is", $item, $temp);
		$video_record['categories'] = kvs_parse_feed_tag($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('models', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match("|<models>(.*?)</models>|is", $item, $temp);
		$video_record['models'] = kvs_parse_feed_tag($temp[1]);
	}

	preg_match("|<duration>(.*?)</duration>|is", $item, $temp);
	$video_record['duration'] = intval($temp[1]);

	preg_match("|<link>(.*?)</link>|is", $item, $temp);
	$video_record['website_link'] = kvs_parse_feed_tag($temp[1]);

	preg_match("|<embed>(.*?)</embed>|is", $item, $temp);
	$video_record['embed_code'] = kvs_parse_feed_tag($temp[1]);

	preg_match_all("|<format>(.*?)</format>|is", $item, $temp);
	if (is_array($temp[1]) && count($temp[1]) > 0)
	{
		foreach ($temp[1] as $file)
		{
			$format_record = [];

			preg_match("|<postfix>(.*?)</postfix>|is", $file, $temp);
			$format_record['postfix'] = kvs_parse_feed_tag($temp[1]);

			preg_match("|<url>(.*?)</url>|is", $file, $temp);
			$format_record['url'] = kvs_parse_feed_tag($temp[1]);

			preg_match("|<filesize>(.*?)</filesize>|is", $file, $temp);
			$format_record['file_size'] = kvs_parse_feed_tag($temp[1]);

			preg_match("|<duration>(.*?)</duration>|is", $file, $temp);
			$format_record['duration'] = intval($temp[1]);

			$video_record['video_files'][$format_record['postfix']] = $format_record;
			if ($format_record['duration'] > $video_record['duration'])
			{
				$video_record['duration'] = $format_record['duration'];
			}
		}
	} else
	{
		preg_match_all("|<file>(.*?)</file>|is", $item, $temp);
		if (is_array($temp[1]) && count($temp[1]) > 0)
		{
			foreach ($temp[1] as $file)
			{
				$format_record = [];
				$format_record['postfix'] = 'source';

				preg_match("|<url>(.*?)</url>|is", $file, $temp);
				$format_record['url'] = kvs_parse_feed_tag($temp[1]);

				preg_match("|<duration>(.*?)</duration>|is", $file, $temp);
				$format_record['duration'] = intval($temp[1]);

				$video_record['video_files'][$format_record['postfix']] = $format_record;
				if ($format_record['duration'] > $video_record['duration'])
				{
					$video_record['duration'] = $format_record['duration'];
				}
			}
		}
	}

	preg_match_all("|<screen>(.*?)</screen>|is", $item, $temp);
	if (is_array($temp[1]))
	{
		foreach ($temp[1] as $screen)
		{
			$video_record['screenshots'][] = kvs_parse_feed_tag($screen);
		}

		preg_match("|<screens main=\"(.*?)\">|is", $item, $temp);
		$video_record['screen_main'] = intval($temp[1]);
	}

	if (!isset($feed_config['fields']) || in_array('posters', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		preg_match_all("|<poster>(.*?)</poster>|is", $item, $temp);
		if (is_array($temp[1]))
		{
			foreach ($temp[1] as $poster)
			{
				$video_record['posters'][] = kvs_parse_feed_tag($poster);
			}

			preg_match("|<posters main=\"(.*?)\">|is", $item, $temp);
			$video_record['poster_main'] = intval($temp[1]);
		}
	}

	if (!isset($feed_config['fields']) || in_array('customization', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		for ($i = 1; $i <= 3; $i++)
		{
			preg_match("|<custom{$i}>(.*?)</custom{$i}>|is", $item, $temp);
			$video_record["custom{$i}"] = kvs_parse_feed_tag($temp[1]);
		}
	}

	if (!isset($feed_config['fields']) || in_array('localization', $feed_config['fields']) || in_array('all', $feed_config['fields']))
	{
		if (isset($languages))
		{
			foreach ($languages as $language)
			{
				preg_match("|<title_$language[code]>(.*?)</title_$language[code]>|is", $item, $temp);
				$title_localized = kvs_parse_feed_tag($temp[1]);
				if ($title_localized)
				{
					$video_record["title_$language[code]"] = $title_localized;
				}

				preg_match("|<description_$language[code]>(.*?)</description_$language[code]>|is", $item, $temp);
				$description_localized = kvs_parse_feed_tag($temp[1]);
				if ($description_localized)
				{
					$video_record["description_$language[code]"] = $description_localized;
				}

				preg_match("|<dir_$language[code]>(.*?)</dir_$language[code]>|is", $item, $temp);
				$dir_localized = kvs_parse_feed_tag($temp[1]);
				if ($dir_localized)
				{
					$video_record["dir_$language[code]"] = $dir_localized;
				}
			}
		}
	}

	return $video_record;
}

function kvs_format_feed_tag($tag_name, $value)
{
	$value = str_replace(array("&", ">", "<"), array("&amp;", "&gt;", "&lt;"), $value);
	return "<$tag_name>$value</$tag_name>";
}

function kvs_parse_feed_tag($value)
{
	return trim(str_replace(array("&lt;", "&gt;", "&amp;"), array("<", ">", "&"), $value));
}
