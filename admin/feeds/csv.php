<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function csv_parse_feed($url, $feed_config)
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

	$rows = explode("\n", $feed_contents);
	if ($feed_config['csv_skip_first_row'] == 1)
	{
		unset($rows[0]);
	}

	$result = [];
	foreach ($rows as $row)
	{
		$row = trim($row);
		if (!$row)
		{
			continue;
		}

		$video_record = [];
		if (strlen($feed_config['separator']) == 1)
		{
			$columns = str_getcsv($row, $feed_config['separator']);
		} else
		{
			$columns = explode($feed_config['separator'], $row);
		}
		for ($i = 0; $i < count($feed_config['fields']); $i++)
		{
			$field = $feed_config['fields'][$i];
			$value = trim($columns[$i]);
			if (strpos($field, 'pass') === 0)
			{
				continue;
			}
			if ($value)
			{
				if ($field == 'video_file')
				{
					$video_record['video_files'] = ['source' => ['postfix' => 'source', 'url' => $value]];
				} elseif ($field == 'screenshot_main_source')
				{
					$video_record['screenshots'] = [$value];
				} elseif ($field == 'overview_screenshots_sources')
				{
					$video_record['screenshots'] = array_map('trim', explode(',', $value));
					if (count($video_record['screenshots']) == 1 && strpos($value, ';') > 0)
					{
						$video_record['screenshots'] = array_map('trim', explode(';', $value));
					}
				} elseif ($field == 'posters_sources')
				{
					$video_record['posters'] = array_map('trim', explode(',', $value));
					if (count($video_record['posters']) == 1 && strpos($value, ';') > 0)
					{
						$video_record['posters'] = array_map('trim', explode(';', $value));
					}
				}
			}
			$video_record[$field] = $value;
		}

		$video_record['external_key'] = $video_record[$feed_config['key_field']];
		$result[] = $video_record;
	}

	return $result;
}

function csv_check_feed_content($url, $feed_config)
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

	$rows = explode("\n", $feed_contents);
	$first_row = trim($rows[0]);
	if ($feed_config['csv_skip_first_row'] == 1)
	{
		if (count($rows) > 1)
		{
			$first_row = trim($rows[1]);
		} else
		{
			return null;
		}
	}

	if (strlen($feed_config['separator']) == 1)
	{
		$columns = array_map('trim', str_getcsv($first_row, $feed_config['separator']));
	} else
	{
		$columns = array_map('trim', explode($feed_config['separator'], $first_row));
	}

	return $columns;
}

function csv_format_feed($videos, $feed_config)
{
	global $config, $languages;

	header("Content-Type: text/plain; charset=utf-8");

	$csv_separator = $feed_config['csv_separator'];
	if ($csv_separator == '')
	{
		$csv_separator = '|';
	}
	if ($csv_separator == "\\t")
	{
		$csv_separator = "\t";
	}
	$csv_list_separator = $feed_config['csv_list_separator'];
	if ($csv_list_separator == '')
	{
		$csv_list_separator = ',';
	}
	$csv_quote = intval($feed_config['csv_quote']);

	$csv_columns = $feed_config['csv_columns'];
	if (!is_array($csv_columns))
	{
		$csv_columns = [];
		$csv_columns[] = 'id';
		$csv_columns[] = 'title';
		$csv_columns[] = 'dir';
		$csv_columns[] = 'description';
		$csv_columns[] = 'post_date';
		$csv_columns[] = 'content_source';
		$csv_columns[] = 'content_source_url';
		$csv_columns[] = 'link';
		$csv_columns[] = 'categories';
		$csv_columns[] = 'tags';
		$csv_columns[] = 'duration';
		if ($feed_config['video_content_type_id'] == 2)
		{
			$csv_columns[] = 'url';
			$csv_columns[] = 'embed';
		} elseif ($feed_config['video_content_type_id'] == 3)
		{
			$csv_columns[] = 'embed';
		} elseif ($feed_config['video_content_type_id'] == 4)
		{
			$csv_columns[] = 'url';
			$csv_columns[] = 'embed';
		}
		$csv_columns[] = 'main_screenshot';
	}
	$result = '';

	foreach ($videos as $video)
	{
		$row = '';
		$index = 0;
		foreach ($csv_columns as $field)
		{
			$index++;
			if ($index > 1)
			{
				$row .= $csv_separator;
			}
			switch ($field)
			{
				case 'id':
					$row .= csv_format_column($video['video_id'], $csv_quote);
					break;
				case 'title':
					$row .= csv_format_column($video['title'], $csv_quote);
					break;
				case 'dir':
					$row .= csv_format_column($video['dir'], $csv_quote);
					break;
				case 'description':
					$row .= csv_format_column($video['description'], $csv_quote);
					break;
				case 'rating':
					$row .= csv_format_column(round($video['rating'] * 10) / 10, $csv_quote);
					break;
				case 'rating_percent':
					$row .= csv_format_column($video['rating_percent'] . '%', $csv_quote);
					break;
				case 'votes':
					$row .= csv_format_column($video['votes'], $csv_quote);
					break;
				case 'popularity':
					$row .= csv_format_column($video['popularity'], $csv_quote);
					break;
				case 'post_date':
					$row .= csv_format_column($video['post_date'], $csv_quote);
					break;
				case 'user':
					$row .= csv_format_column($video['user_title'], $csv_quote);
					break;
				case 'content_source':
					$row .= csv_format_column($video['cs_title'], $csv_quote);
					break;
				case 'content_source_url':
					$row .= csv_format_column($video['cs_url'], $csv_quote);
					break;
				case 'content_source_group':
					$row .= csv_format_column($video['cs_group'], $csv_quote);
					break;
				case 'dvd':
					$row .= csv_format_column($video['dvd_title'], $csv_quote);
					break;
				case 'dvd_group':
					$row .= csv_format_column($video['dvd_group'], $csv_quote);
					break;
				case 'link':
					$row .= csv_format_column($video['website_link'], $csv_quote);
					break;
				case 'categories':
					$row .= csv_format_column(@implode($csv_list_separator, $video['categories']), $csv_quote);
					break;
				case 'tags':
					$row .= csv_format_column(@implode($csv_list_separator, $video['tags']), $csv_quote);
					break;
				case 'models':
					$row .= csv_format_column(@implode($csv_list_separator, $video['models']), $csv_quote);
					break;
				case 'release_year':
					if ($video['release_year'] > 0)
					{
						$row .= csv_format_column($video['release_year'], $csv_quote);
					}
					break;
				case 'duration':
					if (is_array($video['hotlink_format']) && !isset($feed_config['show_real_duration']))
					{
						$row .= csv_format_column($video['hotlink_format']['duration'], $csv_quote);
					} else
					{
						$row .= csv_format_column($video['duration'], $csv_quote);
					}
					break;
				case 'duration_hhmmss':
					if (is_array($video['hotlink_format']) && !isset($feed_config['show_real_duration']))
					{
						$row .= csv_format_column(durationToHumanString($video['hotlink_format']['duration']), $csv_quote);
					} else
					{
						$row .= csv_format_column(durationToHumanString($video['duration']), $csv_quote);
					}
					break;
				case 'quality':
					$row .= csv_format_column($video['is_hd'] == 1 ? 'HD' : 'SD', $csv_quote);
					break;
				case 'width':
					if (is_array($video['hotlink_format']))
					{
						$row .= csv_format_column($video['hotlink_format']['dimensions'][0], $csv_quote);
					} else
					{
						$dimensions = explode("x", $video['file_dimensions']);
						$row .= csv_format_column($dimensions[0], $csv_quote);
					}
					break;
				case 'height':
					if (is_array($video['hotlink_format']))
					{
						$row .= csv_format_column($video['hotlink_format']['dimensions'][1], $csv_quote);
					} else
					{
						$dimensions = explode("x", $video['file_dimensions']);
						$row .= csv_format_column($dimensions[1], $csv_quote);
					}
					break;
				case 'size':
					if (is_array($video['hotlink_format']))
					{
						$row .= csv_format_column($video['hotlink_format']['dimensions'][0] . 'x' . $video['hotlink_format']['dimensions'][1], $csv_quote);
					} else
					{
						$row .= csv_format_column($video['file_dimensions'], $csv_quote);
					}
					break;
				case 'filesize':
					if (is_array($video['hotlink_format']))
					{
						$row .= csv_format_column($video['hotlink_format']['file_size'], $csv_quote);
					}
					break;
				case 'url':
					if (is_array($video['hotlink_format']))
					{
						if ($feed_config['video_content_type_id'] == 4)
						{
							$time = time();
							$row .= csv_format_column("{$video['hotlink_format']['file_url']}?ttl=$time&dsc=" . md5("$config[cv]/{$video['hotlink_format']['file_path']}/$time"), $csv_quote);
						} else
						{
							$row .= csv_format_column($video['hotlink_format']['file_url'], $csv_quote);
						}
					} elseif ($video['file_url'])
					{
						$row .= csv_format_column($video['file_url'], $csv_quote);
					}
					break;
				case 'embed':
					$row .= csv_format_column($video['embed'], $csv_quote);
					break;
				case 'screenshots_prefix':
					if ($feed_config['screenshot_sources'] == 1)
					{
						$row .= csv_format_column(base64_encode(get_video_source_base_url()), $csv_quote);
					} elseif ($video['screen_url'])
					{
						$row .= csv_format_column(base64_encode("$video[screen_url]/"), $csv_quote);
					}
					break;
				case 'posters_prefix':
					if ($video['poster_amount'] > 0)
					{
						if ($feed_config['poster_sources'] == 1)
						{
							$row .= csv_format_column(base64_encode(get_video_source_base_url()), $csv_quote);
						} elseif ($video['poster_url'])
						{
							$row .= csv_format_column(base64_encode("$video[poster_url]/"), $csv_quote);
						}
					}
					break;
				case 'main_screenshot':
					if ($feed_config['screenshot_sources'] == 1)
					{
						$row .= csv_format_column(get_video_source_url($video['video_id'], "screenshots/$video[screen_main].jpg"), $csv_quote);
					} elseif ($video['screen_url'])
					{
						$row .= csv_format_column("$video[screen_url]/$video[screen_main].jpg", $csv_quote);
					}
					break;
				case 'main_poster':
					if ($video['poster_main'] > 0)
					{
						if ($feed_config['poster_sources'] == 1)
						{
							$row .= csv_format_column(get_video_source_url($video['video_id'], "posters/$video[poster_main].jpg"), $csv_quote);
						} elseif ($video['poster_url'])
						{
							$row .= csv_format_column("$video[poster_url]/$video[poster_main].jpg", $csv_quote);
						}
					}
					break;
				case 'main_screenshot_number':
					$row .= csv_format_column($video['screen_main'], $csv_quote);
					break;
				case 'main_poster_number':
					if ($video['poster_main'] > 0)
					{
						$row .= csv_format_column($video['poster_main'], $csv_quote);
					}
					break;
				case 'screenshots':
					$screenshots = '';
					for ($i = 1; $i <= $video['screen_amount']; $i++)
					{
						if ($feed_config['screenshot_sources'] == 1)
						{
							$screenshots .= get_video_source_url($video['video_id'],"screenshots/$i.jpg");
						} elseif ($video['screen_url'])
						{
							$screenshots .= "$video[screen_url]/$i.jpg";
						}
						if ($i < $video['screen_amount'])
						{
							$screenshots .= $csv_list_separator;
						}
					}
					$row .= csv_format_column($screenshots, $csv_quote);
					break;
				case 'posters':
					$posters = '';
					for ($i = 1; $i <= $video['poster_amount']; $i++)
					{
						if ($feed_config['poster_sources'] == 1)
						{
							$posters .= get_video_source_url($video['video_id'], "posters/$i.jpg");
						} elseif ($video['poster_url'])
						{
							$posters .= "$video[poster_url]/$i.jpg";
						}
						if ($i < $video['poster_amount'])
						{
							$posters .= $csv_list_separator;
						}
					}
					$row .= csv_format_column($posters, $csv_quote);
					break;
				case 'custom1':
					$row .= csv_format_column($video['custom1'], $csv_quote);
					break;
				case 'custom2':
					$row .= csv_format_column($video['custom2'], $csv_quote);
					break;
				case 'custom3':
					$row .= csv_format_column($video['custom3'], $csv_quote);
					break;
			}
			if (isset($languages))
			{
				foreach ($languages as $language)
				{
					if ($field == "title_$language[code]")
					{
						$row .= csv_format_column($video["title_$language[code]"], $csv_quote);
					}
					if ($field == "description_$language[code]")
					{
						$row .= csv_format_column($video["description_$language[code]"], $csv_quote);
					}
					if ($field == "dir_$language[code]")
					{
						$row .= csv_format_column($video["dir_$language[code]"], $csv_quote);
					}
				}
			}
			if (strpos($field, 'static:') === 0)
			{
				$row .= csv_format_column(str_replace('static:', '', $field), $csv_quote);
			}
		}
		if (in_array('screenshots_prefix', $csv_columns))
		{
			if ($feed_config['screenshot_sources'] == 1)
			{
				$row = str_replace(array(get_video_source_base_url(), base64_encode(get_video_source_base_url())), array('', get_video_source_base_url()), $row);
			} elseif ($video['screen_url'])
			{
				$row = str_replace(array("$video[screen_url]/", base64_encode("$video[screen_url]/")), array('', "$video[screen_url]/"), $row);
			}
		}
		if (in_array('posters_prefix', $csv_columns))
		{
			if ($feed_config['poster_sources'] == 1)
			{
				$row = str_replace(array(get_video_source_base_url(), base64_encode(get_video_source_base_url())), array('', get_video_source_base_url()), $row);
			} elseif ($video['poster_url'])
			{
				$row = str_replace(array("$video[poster_url]/", base64_encode("$video[poster_url]/")), array('', "$video[poster_url]/"), $row);
			}
		}
		$result .= "$row\n";
	}

	return $result;
}

function csv_format_column($data, $csv_quote)
{
	if ($data === '')
	{
		return $data;
	}
	$data = str_replace("\n", " ", $data);
	if ($csv_quote == 1)
	{
		return '"' . str_replace(['"', '[kt_quote]'], ['[kt_quote]', '""'], $data) . '"';
	}
	return $data;
}
