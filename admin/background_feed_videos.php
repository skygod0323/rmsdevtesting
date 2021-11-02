<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT'])
{
	header('HTTP/1.0 403 Forbidden');
	die('Access denied');
}

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/pclzip.lib.php';

$feed_id = intval($_SERVER['argv'][1]);
if ($feed_id == 0)
{
	die('Feed ID is missing');
}

if (!is_file("$config[project_path]/admin/data/system/feeds_videos_$feed_id.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/feeds_videos_$feed_id.lock", '1', LOCK_EX);
}

$lock = fopen("$config[project_path]/admin/data/system/feeds_videos_$feed_id.lock", 'r+');
if (!flock($lock, LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

sql_pr('set wait_timeout=86400');

$options = get_options();

$memory_limit = intval($options['LIMIT_MEMORY']);
if ($memory_limit == 0)
{
	$memory_limit = 512;
}
ini_set('memory_limit', "{$memory_limit}M");

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));

$feed = mr2array_single(sql_pr("select * from $config[tables_prefix]videos_feeds_import where feed_id=?", $feed_id));
if ($feed)
{
	exec_feed();
}
flock($lock, LOCK_UN);
fclose($lock);

function exec_feed()
{
	global $config, $options, $languages, $regexp_check_email, $feed;

	$start_time = time();
	log_feed(0, 1, "Starting feed \"$feed[title]\"");

	if (!in_array($feed['feed_type_id'], ['csv', 'kvs', 'rss']))
	{
		log_feed(0, 2, "Feed parser type is not supported: $feed[feed_type_id]");
		return;
	}

	$global_lock_file = "$config[project_path]/admin/data/system/background_import.lock";
	if (!is_file($global_lock_file))
	{
		file_put_contents($global_lock_file, '1', LOCK_EX);
	}
	$global_lock = fopen($global_lock_file, 'r');

	require_once("$config[project_path]/admin/feeds/$feed[feed_type_id].php");

	if (intval($feed['exec_interval_hours']) + intval($feed['exec_interval_minutes']) == 0)
	{
		sql_pr("update $config[tables_prefix]videos_feeds_import set status_id=0, last_exec_date=? where feed_id=?", date('Y-m-d H:i:s'), $feed['feed_id']);
	} else
	{
		sql_pr("update $config[tables_prefix]videos_feeds_import set last_exec_date=? where feed_id=?", date('Y-m-d H:i:s'), $feed['feed_id']);
	}

	$is_skip_new_categories = 0;
	$is_skip_new_models = 0;
	$is_skip_new_content_sources = 0;
	$is_skip_new_dvds = 0;
	$feed_options = @unserialize($feed['options']);
	if (is_array($feed_options))
	{
		$is_skip_new_categories = intval($feed_options['is_skip_new_categories']);
		$is_skip_new_models = intval($feed_options['is_skip_new_models']);
		$is_skip_new_content_sources = intval($feed_options['is_skip_new_content_sources']);
		$is_skip_new_dvds = intval($feed_options['is_skip_new_dvds']);
	}

	$categories_all = [];
	$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
	foreach ($temp as $category)
	{
		$categories_all[mb_lowercase($category['title'])] = $category['category_id'];
		$temp_syn = explode(",", $category['synonyms']);
		if (is_array($temp_syn))
		{
			foreach ($temp_syn as $syn)
			{
				$syn = trim($syn);
				if ($syn)
				{
					$categories_all[mb_lowercase($syn)] = $category['category_id'];
				}
			}
		}
	}

	$models_all = [];
	$temp = mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
	foreach ($temp as $model)
	{
		$models_all[mb_lowercase($model['title'])] = $model['model_id'];
		$temp_syn = explode(",", $model['alias']);
		if (is_array($temp_syn))
		{
			foreach ($temp_syn as $syn)
			{
				$syn = trim($syn);
				if ($syn)
				{
					$models_all[mb_lowercase($syn)] = $model['model_id'];
				}
			}
		}
	}

	$formats_videos = mr2array(sql_pr("select format_video_id, postfix from $config[tables_prefix]formats_videos where status_id in (1,2)"));

	$configuration = @unserialize($feed['data_configuration']);
	$configuration['feed_charset'] = $feed['feed_charset'];

	$list_separator = ',';
	if ($configuration['separator_list_items'])
	{
		$list_separator = $configuration['separator_list_items'];
	}

	$parse_func = "$feed[feed_type_id]_parse_feed";
	$feed_result = $parse_func($feed['url'], $configuration);
	if ($feed['direction_id'] == 1)
	{
		$feed_result = array_reverse($feed_result);
	}
	log_feed(0, 1, "Feed parsed: " . count($feed_result) . " items");

	$videos_added = 0;
	$videos_skipped = 0;
	$videos_errored = 0;
	$videos_total = count($feed_result);

	$item = 0;
	foreach ($feed_result as $video_rec)
	{
		if (min(@disk_free_space($config['project_path']), @disk_free_space($config['content_path_videos_sources'])) < $options['MAIN_SERVER_MIN_FREE_SPACE_MB'] * 1024 * 1024)
		{
			log_feed(0, 2, "Server free space is lower than $options[MAIN_SERVER_MIN_FREE_SPACE_MB]M, stopping feed");
			break;
		}

		$item++;
		if (intval($feed['max_videos_per_exec']) > 0 && $videos_added >= $feed['max_videos_per_exec'])
		{
			break;
		}

		$limit_duration_from = intval($feed['limit_duration_from']);
		$limit_duration_to = intval($feed['limit_duration_to']);
		$limit_rating_from = intval($feed['limit_rating_from']);
		$limit_rating_to = intval($feed['limit_rating_to']);
		$limit_views_from = intval($feed['limit_views_from']);
		$limit_views_to = intval($feed['limit_views_to']);
		$limit_terminology = $feed['limit_terminology'];
		$limit_date_from = 0;
		$limit_date_to = 0;

		$videos_adding_mode_id = $feed['videos_adding_mode_id'];
		$screenshots_mode_id = $feed['screenshots_mode_id'];
		$format_video_id = $feed['format_video_id'];
		$grabber = null;

		if ($videos_adding_mode_id == 6)
		{
			if (!$video_rec['website_link'])
			{
				$videos_errored++;
				log_feed($item, 2, 'No website link');
				continue;
			}

			$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where gallery_url=? limit 1", $video_rec['website_link']));
			if ($duplicate_video_id > 0)
			{
				log_feed($item, 0, "Skipped (duplicate gallery in video $duplicate_video_id)");
				$videos_skipped++;
				continue;
			}

			if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
			{
				require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
				$grabber_gunction = "grabbersFindGrabber";
				if (function_exists($grabber_gunction))
				{
					$grabber = $grabber_gunction($video_rec['website_link'], 'videos');
				}
			}

			if ($grabber instanceof KvsGrabberVideo)
			{
				log_feed($item, 0, 'Using grabber ' . $grabber->get_grabber_id(), $video_rec['website_link']);

				if (!$grabber->is_content_url($video_rec['website_link']))
				{
					$videos_errored++;
					log_feed($item, 2, 'URL is not supported by grabber', $video_rec['website_link']);
					continue;
				}

				$grabber_settings = $grabber->get_settings();
				$grabber_video_info = $grabber->grab_video_data($video_rec['website_link'], $config['temporary_path']);
				$grabber_video_files = $grabber_video_info->get_video_files();

				if ($grabber_video_info->get_error_code() > 0)
				{
					$videos_errored++;
					$grabber_error_message = '';
					switch ($grabber_video_info->get_error_code())
					{
						case KvsGrabberVideoInfo::ERROR_CODE_PAGE_UNAVAILABLE:
							$grabber_error_message = 'Video page is not available';
							break;
						case KvsGrabberVideoInfo::ERROR_CODE_PAGE_ERROR:
						case KvsGrabberVideoInfo::ERROR_CODE_UNEXPECTED_ERROR:
							$grabber_error_message = 'Video page gives error: ' . $grabber_video_info->get_error_message();
							break;
						case KvsGrabberVideoInfo::ERROR_CODE_PARSING_ERROR:
							$grabber_error_message = 'Grabber was not able to parse video page';
							break;
					}
					log_feed($item, 2, 'Grabber returned error', $grabber_error_message);
					continue;
				}

				if ($grabber_settings->get_filter_quality_from())
				{
					$max_quality = '';
					foreach ($grabber_video_files as $quality => $video_file)
					{
						if (intval($quality) >= intval($max_quality))
						{
							$max_quality = $quality;
						}
					}
					if (intval($max_quality) < intval($grabber_settings->get_filter_quality_from()))
					{
						log_feed($item, 0, 'Skipped (quality filter)');
						$videos_skipped++;
						continue;
					}
				}

				$limit_duration_from = $grabber_settings->get_filter_quantity_from();
				$limit_duration_to = $grabber_settings->get_filter_quantity_to();

				$limit_rating_from = $grabber_settings->get_filter_rating_from();
				$limit_rating_to = $grabber_settings->get_filter_rating_to();

				$limit_views_from = $grabber_settings->get_filter_views_from();
				$limit_views_to = $grabber_settings->get_filter_views_to();

				$limit_date_from = $grabber_settings->get_filter_date_from();
				$limit_date_to = $grabber_settings->get_filter_date_to();

				$limit_terminology = $grabber_settings->get_filter_terminology();

				switch ($grabber_settings->get_mode())
				{
					case KvsGrabberSettings::GRAB_MODE_EMBED:
						$videos_adding_mode_id = 1;
						$screenshots_mode_id = 1;
						$video_rec['embed_code'] = $grabber_video_info->get_embed();
						break;
					case KvsGrabberSettings::GRAB_MODE_PSEUDO:
						$screenshots_mode_id = 1;
						$videos_adding_mode_id = 2;
						break;
					case KvsGrabberSettings::GRAB_MODE_DOWNLOAD:
						$videos_adding_mode_id = 4;

						$video_rec['video_files'] = [];
						if ($grabber_settings->get_quality() != '*')
						{
							$format_video_id = 0;
							if ($grabber_settings->get_download_format())
							{
								foreach ($formats_videos as $format)
								{
									if ($format['postfix'] == $grabber_settings->get_download_format())
									{
										$format_video_id = $format['format_video_id'];
										break;
									}
								}
							}

							$download_url = '';
							$download_quality = $grabber_settings->get_quality();
							if ($download_quality == '')
							{
								$max_quality = '';
								$max_quality_url = '';
								foreach ($grabber_video_files as $grabber_video_file_quality => $video_file)
								{
									if (intval($grabber_video_file_quality) >= intval($max_quality))
									{
										$max_quality = $grabber_video_file_quality;
										$max_quality_url = $video_file;
									}
								}
								if ($max_quality_url)
								{
									$download_url = $max_quality_url;
									$download_quality = $max_quality;
								}
							} elseif (isset($grabber_video_files[$download_quality]))
							{
								$download_url = $grabber_video_files[$download_quality];
							} elseif ($grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_LOWER)
							{
								ksort($grabber_video_files, SORT_NUMERIC);
								$grabber_video_files = array_reverse($grabber_video_files, true);
								foreach ($grabber_video_files as $grabber_video_file_quality => $video_file)
								{
									if (intval($grabber_video_file_quality) < intval($download_quality))
									{
										$download_url = $video_file;
										$download_quality = $grabber_video_file_quality;
										break;
									}
								}
							} elseif ($grabber_settings->get_quality_missing() == KvsGrabberSettings::QUALITY_MISSING_HIGHER)
							{
								ksort($grabber_video_files, SORT_NUMERIC);
								foreach ($grabber_video_files as $grabber_video_file_quality => $video_file)
								{
									if (intval($grabber_video_file_quality) > intval($download_quality))
									{
										$download_url = $video_file;
										$download_quality = $grabber_video_file_quality;
										break;
									}
								}
							}

							if ($download_url)
							{
								$video_rec['video_files']['source'] = ['postfix' => 'source', 'url' => $download_url, 'quality' => $download_quality];
							} else
							{
								$videos_errored++;
								log_feed($item, 2, 'No download file detected');
								continue 2;
							}
						} else
						{
							if ($feed['videos_is_private'] == 2)
							{
								$videos_errored++;
								log_feed($item, 2, 'Premium videos are not allowed for this grabber download mode');
								continue 2;
							}

							$format_video_id = 9999999;
							foreach ($grabber_settings->get_download_formats_mapping() as $grabber_quality_key => $grabber_quality_format)
							{
								if ($grabber_video_files[$grabber_quality_key])
								{
									foreach ($formats_videos as $format_video)
									{
										if ($grabber_quality_format == $format_video['postfix'])
										{
											$video_rec['video_files'][$format_video['postfix']] = ['postfix' => $format_video['postfix'], 'url' => $grabber_video_files[$grabber_quality_key], 'quality' => $grabber_quality_key];
										}
									}
								}
							}
						}
						break;
				}

				$video_rec['external_key'] = $grabber_video_info->get_canonical();
				if (!$video_rec['duration'])
				{
					$video_rec['duration'] = $grabber_video_info->get_duration();
				}
				foreach ($grabber_settings->get_data() as $grabber_settings_data_item)
				{
					switch ($grabber_settings_data_item)
					{
						case KvsGrabberSettings::DATA_FIELD_TITLE:
							if (!$video_rec['title'])
							{
								$video_rec['title'] = $grabber_video_info->get_title();
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_DESCRIPTION:
							if (!$video_rec['description'])
							{
								$video_rec['description'] = $grabber_video_info->get_description();
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_DATE:
							if (!$video_rec['post_date'] && $grabber_video_info->get_date())
							{
								$video_rec['post_date'] = date('Y-m-d', $grabber_video_info->get_date());
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_RATING:
							if (!$video_rec['rating'] && !$video_rec['rating_percent'])
							{
								$video_rec['rating_percent'] = $grabber_video_info->get_rating();
								$video_rec['votes'] = max(1, intval($grabber_video_info->get_votes()));
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_VIEWS:
							if (!$video_rec['popularity'])
							{
								$video_rec['popularity'] = intval($grabber_video_info->get_views());
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CUSTOM:
							for ($i = 1; $i <= 3; $i++)
							{
								if (!$video_rec["custom$i"] && $grabber_video_info->get_custom_field($i))
								{
									$video_rec["custom$i"] = $grabber_video_info->get_custom_field($i);
								}
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_SCREENSHOT:
							if (@count($video_rec['screenshots']) == 0)
							{
								$video_rec['screenshots'] = [$grabber_video_info->get_screenshot()];
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CATEGORIES:
							if (!$video_rec['categories'])
							{
								$video_rec['categories'] = implode($list_separator, $grabber_video_info->get_categories());
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_MODELS:
							if (!$video_rec['models'])
							{
								$video_rec['models'] = implode($list_separator, $grabber_video_info->get_models());
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_TAGS:
							if (!$video_rec['tags'])
							{
								$video_rec['tags'] = implode($list_separator, $grabber_video_info->get_tags());
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CONTENT_SOURCE:
							if (!$video_rec['content_source'])
							{
								$video_rec['content_source'] = $grabber_video_info->get_content_source();
								$video_rec['content_source_url'] = '';
								$video_rec['content_source_group'] = '';
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CHANNEL:
							if (!$video_rec['dvd'])
							{
								$video_rec['dvd'] = $grabber_video_info->get_channel();
								$video_rec['dvd_group'] = '';
							}
							break;
					}
				}
			} else
			{
				$videos_errored++;
				log_feed($item, 2, 'No grabber available', $video_rec['website_link']);
				continue;
			}
		}

		if ($limit_duration_from > 0 || $limit_duration_to > 0)
		{
			$video_duration = parse_duration($video_rec['duration']);
			if ($video_duration)
			{
				if (($limit_duration_from > 0 && $video_duration < $limit_duration_from) || ($limit_duration_to > 0 && $video_duration > $limit_duration_to))
				{
					log_feed($item, 0, 'Skipped (duration filter)');
					$videos_skipped++;
					continue;
				}
			}
		}

		if ($limit_rating_from > 0 || $limit_rating_to > 0)
		{
			$video_rating = 0;
			if (isset($video_rec['rating_percent']))
			{
				$video_rating = intval($video_rec['rating_percent']);
			} elseif (isset($video_rec['rating']))
			{
				$video_rating = floatval($video_rec['rating']) / 5 * 100;
			}
			if (($limit_rating_from > 0 && $video_rating < $limit_rating_from) || ($limit_rating_to > 0 && $video_rating > $limit_rating_to))
			{
				log_feed($item, 0, 'Skipped (rating filter)');
				$videos_skipped++;
				continue;
			}
		}

		if ($limit_views_from > 0 || $limit_views_to > 0)
		{
			if ($video_rec['popularity'])
			{
				if (($limit_views_from > 0 && intval($video_rec['popularity']) < $limit_views_from) || ($limit_views_to > 0 && intval($video_rec['popularity']) > $limit_views_to))
				{
					log_feed($item, 0, 'Skipped (popularity filter)');
					$videos_skipped++;
					continue;
				}
			}
		}

		if ($limit_date_from > 0 || $limit_date_to > 0)
		{
			if ($video_rec['post_date'])
			{
				$video_days_posted = floor((time() - strtotime($video_rec['post_date'])) / 86400);
				if (($limit_date_from > 0 && $video_days_posted < $limit_date_from) || ($limit_date_to > 0 && $video_days_posted > $limit_date_to))
				{
					log_feed($item, 0, 'Skipped (date filter)');
					$videos_skipped++;
					continue;
				}
			}
		}

		if ($limit_terminology)
		{
			if ($video_rec['title'])
			{
				$limit_terminology_value = array_map('trim', explode(',', mb_lowercase($limit_terminology)));
				$limit_terminology_title = mb_lowercase($video_rec['title']);

				unset($limit_terminology_words_in_title);
				preg_match_all('/([\p{N}\p{L}-_#@]+)/u', $limit_terminology_title, $limit_terminology_words_in_title);

				foreach ($limit_terminology_words_in_title[0] as $word)
				{
					if (in_array($word, $limit_terminology_value))
					{
						log_feed($item, 0, 'Skipped (terminology filter)');
						$videos_skipped++;
						continue 2;
					}
				}

				foreach ($limit_terminology_value as $word)
				{
					if (strpos($word, ' ') && strpos($limit_terminology_title, $word))
					{
						log_feed($item, 0, 'Skipped (terminology filter)');
						$videos_skipped++;
						continue 2;
					}
				}
			}
		}

		if (!$video_rec['external_key'])
		{
			$videos_errored++;
			log_feed($item, 2, 'No external key');
			continue;
		}

		$video_rec['external_key'] = md5($feed['key_prefix'] . $video_rec['external_key']);

		if (intval($feed['is_skip_deleted_videos']) == 1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_feeds_import_history where video_key=?", $video_rec['external_key'])) > 0)
		{
			log_feed($item, 0, 'Skipped (exists in history)');
			$videos_skipped++;
			continue;
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", $video_rec['external_key'])) > 0)
		{
			log_feed($item, 0, 'Skipped (exists in database)');
			$videos_skipped++;
			continue;
		}

		if ($video_rec['title'] && intval($feed['is_skip_duplicate_titles']) == 1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where title=?", $video_rec['title'])) > 0)
		{
			log_feed($item, 0, 'Skipped (duplicate title)');
			$videos_skipped++;
			continue;
		}

		if (intval($video_rec['id']) > 0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=?", intval($video_rec['id']))) > 0)
		{
			log_feed($item, 0, 'Skipped (duplicate ID)');
			$videos_skipped++;
			continue;
		}

		if (!$video_rec['title'] && intval($feed['videos_status_id']) == 1)
		{
			$videos_errored++;
			log_feed($item, 2, 'No title');
			continue;
		}

		if (in_array($videos_adding_mode_id, [1, 2, 3]))
		{
			if (parse_duration($video_rec['duration']) == 0)
			{
				if (@count($video_rec['video_files']) == 0)
				{
					$videos_errored++;
					log_feed($item, 2, 'No duration');
					continue;
				}
				foreach ($video_rec['video_files'] as $video_file)
				{
					if (!is_url($video_file['url']))
					{
						$videos_errored++;
						log_feed($item, 2, 'Video file URL is not valid', $video_file['url']);
						continue 2;
					}
				}
			}
		}

		if ($screenshots_mode_id != 2)
		{
			if (@count($video_rec['screenshots']) == 0)
			{
				$videos_errored++;
				log_feed($item, 2, 'No screenshots');
				continue;
			}

			$url_errors_count = 0;
			$availability_errors_count = 0;
			$total_urls_count = 0;

			foreach ($video_rec['screenshots'] as $screenshot)
			{
				$screenshot = trim($screenshot);
				if (!$screenshot)
				{
					continue;
				}

				$total_urls_count++;
				if (!is_url($screenshot))
				{
					log_feed($item, 0, 'Screenshot URL is not valid', $screenshot);
					$url_errors_count++;
				} elseif (!is_binary_file_url($screenshot))
				{
					log_feed($item, 0, 'Screenshot URL is not available', $screenshot);
					$availability_errors_count++;
				}
			}

			if ($availability_errors_count + $url_errors_count >= $total_urls_count)
			{
				$videos_errored++;
				log_feed($item, 2, 'Screenshot URLs are invalid or not available');
				continue;
			}
		}

		if ($videos_adding_mode_id == 1 && !$video_rec['embed_code'])
		{
			$videos_errored++;
			log_feed($item, 2, 'No embed code');
			continue;
		}

		if ($videos_adding_mode_id == 2)
		{
			if (!$video_rec['website_link'])
			{
				$videos_errored++;
				log_feed($item, 2, 'No website link');
				continue;
			} elseif (!is_url($video_rec['website_link']))
			{
				$videos_errored++;
				log_feed($item, 2, 'Website link URL is not valid', $video_rec['website_link']);
				continue;
			} elseif (!is_working_url($video_rec['website_link']))
			{
				$videos_errored++;
				log_feed($item, 2, 'Website link URL is not available', $video_rec['website_link']);
				continue;
			}
		}

		if ($videos_adding_mode_id == 3)
		{
			if (!isset($video_rec['video_files']['source']['url']))
			{
				$videos_errored++;
				log_feed($item, 2, 'No hotlink video file');
				continue;
			}
		}

		if ($videos_adding_mode_id == 3 || $videos_adding_mode_id == 4 || $screenshots_mode_id == 2 || $screenshots_mode_id == 3)
		{
			if (@count($video_rec['video_files']) == 0)
			{
				$videos_errored++;
				log_feed($item, 2, 'No video files');
				continue;
			}
			foreach ($video_rec['video_files'] as $video_file)
			{
				if (!is_url($video_file['url']))
				{
					$videos_errored++;
					log_feed($item, 2, 'Video file URL is not valid', $video_file['url']);
					continue 2;
				}
			}
		}

		if ($feed['post_date_mode_id'] == 2 && !$video_rec['post_date'])
		{
			$videos_errored++;
			log_feed($item, 2, 'No publishing date');
			continue;
		}

		$insert_data = [];
		$insert_data['dir'] = trim($video_rec['dir']);
		$insert_data['description'] = trim($video_rec['description']);
		$insert_data['user_id'] = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $options['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']));
		if (isset($video_rec['rating_percent']))
		{
			$insert_data['rating_amount'] = (intval($video_rec['votes']) > 0 ? intval($video_rec['votes']) : 1);
			$insert_data['rating'] = intval($video_rec['rating_percent']) / 100 * 5 * $insert_data['rating_amount'];
		} elseif (isset($video_rec['rating']))
		{
			$insert_data['rating_amount'] = (intval($video_rec['votes']) > 0 ? intval($video_rec['votes']) : 1);
			$insert_data['rating'] = floatval($video_rec['rating']) * $insert_data['rating_amount'];
		} else
		{
			$insert_data['rating_amount'] = 1;
			$insert_data['rating'] = intval($options['VIDEO_INITIAL_RATING']);
		}
		$insert_data['video_viewed'] = intval($video_rec['popularity']);
		$insert_data['release_year'] = intval($video_rec['release_year']);
		$insert_data['status_id'] = 3;
		$insert_data['custom1'] = trim($video_rec['custom1']);
		$insert_data['custom2'] = trim($video_rec['custom2']);
		$insert_data['custom3'] = trim($video_rec['custom3']);
		$insert_data['external_key'] = $video_rec['external_key'];
		$insert_data['feed_id'] = $feed['feed_id'];
		$insert_data['is_review_needed'] = intval($feed['videos_is_review_needed']);
		$insert_data['is_private'] = intval($feed['videos_is_private']);

		$videos_per_dates = [];
		if ($feed['post_date_mode_id'] == 3 || $feed['post_date_mode_id'] == 4)
		{
			if ($feed['post_date_mode_id'] == 3)
			{
				$min_date = date('Y-m-d', time() + 86400);
				$max_date = date('Y-m-d', time() + intval($feed['end_date_offset'] + 1) * 86400);
			} else
			{
				$min_date = date('Y-m-d', strtotime($feed['start_date_interval']));
				$max_date = date('Y-m-d', strtotime($feed['end_date_interval']) + 86400);
			}
			$res = mr2array(sql_pr("select date_format(post_date,'%Y-%m-%d') as period, count(*) as cnt from $config[tables_prefix]videos where post_date>='$min_date' and post_date<'$max_date' group by date_format(post_date,'%Y-%m-%d')"));
			foreach ($res as $res_item)
			{
				$videos_per_dates[$res_item['period']] = intval($res_item['cnt']);
			}
		}

		$base_post_date = time();
		if ($feed['post_date_mode_id'] == 2)
		{
			$base_post_date = strtotime($video_rec['post_date']);
		} elseif ($feed['post_date_mode_id'] == 3)
		{
			$is_distributed = 0;
			for ($it = 1; $it <= intval($feed['end_date_offset']); $it++)
			{
				$date = date('Y-m-d', time() + 86400 * $it);
				if (intval($videos_per_dates[$date]) < $feed['max_videos_per_day'])
				{
					$base_post_date = strtotime($date);
					$is_distributed = 1;
					break;
				}
			}
			if ($is_distributed == 0)
			{
				log_feed($item, 0, 'Skipped (no publishing date slot)');
				$videos_skipped++;
				continue;
			}
		} elseif ($feed['post_date_mode_id'] == 4)
		{
			$min_date = strtotime($feed['start_date_interval']);
			$max_date = strtotime($feed['end_date_interval']);
			$days = ceil(($max_date - $min_date) / 86400);
			$is_distributed = 0;
			for ($it = 0; $it < $days * 10; $it++)
			{
				$date = date('Y-m-d', $min_date + 86400 * mt_rand(0, $days));
				if (intval($feed['max_videos_per_day']) > 0)
				{
					if (intval($videos_per_dates[$date]) >= $feed['max_videos_per_day'])
					{
						continue;
					}
				}
				$base_post_date = strtotime($date);
				$is_distributed = 1;
				break;
			}
			if ($is_distributed == 0)
			{
				log_feed($item, 0, 'Skipped (no publishing date slot)');
				$videos_skipped++;
				continue;
			}
		}

		if ($options['USE_POST_DATE_RANDOMIZATION'] == '0')
		{
			$insert_data['post_date'] = date('Y-m-d 00:00:00', $base_post_date);
		} elseif ($options['USE_POST_DATE_RANDOMIZATION'] == '1')
		{
			$insert_data['post_date'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d 00:00:00', $base_post_date)) + mt_rand(0, 86399));
		} elseif ($options['USE_POST_DATE_RANDOMIZATION'] == '2')
		{
			$insert_data['post_date'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d', $base_post_date) . " " . date('H:i:s')));
		}
		$insert_data['last_time_view_date'] = date('Y-m-d H:i:s');

		$max_filesize = 0;
		$max_filesize_file = null;
		if (@count($video_rec['video_files']) > 0)
		{
			foreach ($video_rec['video_files'] as $video_file)
			{
				if ($video_file['file_size'] > $max_filesize)
				{
					$max_filesize = $video_file['file_size'];
					$max_filesize_file = $video_file;
				}
			}
		}

		$insert_data['duration'] = parse_duration($video_rec['duration']);
		if ($videos_adding_mode_id == 1)
		{
			$insert_data['load_type_id'] = 3;
			$insert_data['embed'] = process_embed_code(trim($video_rec['embed_code']));
			if (isset($video_rec['video_files']['source']['url']))
			{
				$insert_data['file_url'] = trim($video_rec['video_files']['source']['url']);
			} elseif ($max_filesize_file)
			{
				$insert_data['file_url'] = $max_filesize_file['url'];
			}
		} elseif ($videos_adding_mode_id == 2)
		{
			$insert_data['load_type_id'] = 5;
			$insert_data['pseudo_url'] = trim($video_rec['website_link']);
			if (isset($video_rec['video_files']['source']['url']))
			{
				$insert_data['file_url'] = trim($video_rec['video_files']['source']['url']);
			} elseif ($max_filesize_file)
			{
				$insert_data['file_url'] = $max_filesize_file['url'];
			}
		} elseif ($videos_adding_mode_id == 3)
		{
			$insert_data['load_type_id'] = 2;
			$insert_data['file_url'] = trim($video_rec['video_files']['source']['url']);
		} elseif ($videos_adding_mode_id == 4)
		{
			$insert_data['load_type_id'] = 1;
		}

		if ($video_rec['website_link'])
		{
			$insert_data['gallery_url'] = $video_rec['website_link'];
		}

		$download_temp_dir = "$config[temporary_path]/feed_$feed[feed_id]" . mt_rand(10000000, 99999999);
		for ($i = 0; $i < 100; $i++)
		{
			if (is_dir($download_temp_dir))
			{
				$download_temp_dir = "$config[temporary_path]/feed_$feed[feed_id]" . mt_rand(10000000, 99999999);
			} else {
				break;
			}
		}
		if (!mkdir_recursive($download_temp_dir))
		{
			$videos_errored++;
			log_feed($item, 2, 'Failed to create temp directory');
			continue;
		}

		$screen_dir = "$download_temp_dir/screenshots";
		if ($screenshots_mode_id == 1)
		{
			if (!mkdir_recursive($screen_dir))
			{
				$videos_errored++;
				log_feed($item, 2, 'Failed to create temp directory');
				continue;
			}

			$counter = 1;
			foreach ($video_rec['screenshots'] as $screen_url)
			{
				$screen_url = trim($screen_url);
				if (!$screen_url)
				{
					continue;
				}

				$screen_path = "$screen_dir/screenshot{$counter}.jpg";
				save_file_from_url($screen_url, $screen_path, '', 20);

				$img_size = getimagesize($screen_path);
				if ($img_size[0] > 0 && $img_size[1] > 0)
				{
					$counter++;
				} else
				{
					@unlink($screen_path);
				}
			}
		} elseif ($screenshots_mode_id == 3)
		{
			if (!mkdir_recursive($screen_dir))
			{
				$videos_errored++;
				log_feed($item, 2, 'Failed to create temp directory');
				continue;
			}

			$screenshot_index = 0;
			if ($video_rec['screen_main'] > 1 && $video_rec['screen_main'] <= count($video_rec['screenshots']))
			{
				$screenshot_index = intval($video_rec['screen_main']) - 1;
			}
			save_file_from_url(trim($video_rec['screenshots'][$screenshot_index]), "$screen_dir/screenshot1.jpg", '', 20);
		}

		$poster_dir = "$download_temp_dir/posters";
		if (@count($video_rec['posters']) > 0)
		{
			if (!mkdir_recursive($poster_dir))
			{
				$videos_errored++;
				log_feed($item, 2, 'Failed to create temp directory');
				continue;
			}

			$counter = 1;
			foreach ($video_rec['posters'] as $poster_url)
			{
				$poster_url = trim($poster_url);
				if (!$poster_url)
				{
					continue;
				}

				$poster_path = "$poster_dir/poster{$counter}.jpg";
				save_file_from_url($poster_url, $poster_path, '', 20);

				$img_size = getimagesize($poster_path);
				if ($img_size[0] > 0 && $img_size[1] > 0)
				{
					$counter++;
				} else
				{
					@unlink($poster_path);
				}
			}
		}

		if ($insert_data['load_type_id'] == 1)
		{
			if ($format_video_id == 9999999)
			{
				$video_postfixes = [];
				foreach ($formats_videos as $format)
				{
					if (in_array($feed['videos_is_private'], [0, 1]) && $format['video_type_id'] == 0)
					{
						$video_postfixes[] = $format['postfix'];
					} elseif ($feed['videos_is_private'] == 2 && $format['video_type_id'] == 1)
					{
						$video_postfixes[] = $format['postfix'];
					}
				}

				$has_downloaded_files = false;
				if ($insert_data['gallery_url'] && $grabber instanceof KvsGrabberVideoYDL)
				{
					$download_qualities = [];
					foreach ($video_rec['video_files'] as $video_file)
					{
						if (in_array($video_file['postfix'], $video_postfixes) && $video_file['quality'])
						{
							$download_qualities[] = $video_file['quality'];
						}
					}
					if (count($download_qualities) > 0)
					{
						log_feed($item, 0, 'Started downloading multiple remote files', implode(", ", $download_qualities));
						$grabber->download_files($insert_data['gallery_url'], $download_qualities, $download_temp_dir);
						foreach ($video_rec['video_files'] as $video_file)
						{
							if (in_array($video_file['postfix'], $video_postfixes) && $video_file['quality'])
							{
								$download_path = "$download_temp_dir/file{$video_file['postfix']}";
								@rename("$download_temp_dir/" . intval($video_file['quality']) . ".tmp", $download_path);
								if (!is_file($download_path) && $video_file['quality'] == '?')
								{
									@rename("$download_temp_dir/file.tmp", $download_path);
								}

								$filesize = @sprintf("%.0f", filesize($download_path));
								$duration = get_video_duration($download_path);
								if ($duration == 0)
								{
									log_feed($item, 2, "Failed to download remote file ($filesize bytes)", $video_file['url']);
									$has_downloaded_files = false;
									break;
								} else
								{
									$has_downloaded_files = true;
									log_feed($item, 0, "Finished downloading remote file ($filesize bytes)", $video_file['url']);
								}
							}
						}
					}
				} else
				{
					foreach ($video_rec['video_files'] as $video_file)
					{
						if (in_array($video_file['postfix'], $video_postfixes))
						{
							$download_path = "$download_temp_dir/file{$video_file['postfix']}";
							$download_referer = '';
							if ($insert_data['gallery_url'])
							{
								$download_referer = $insert_data['gallery_url'];
							}
							log_feed($item, 0, 'Started downloading remote file', $video_file['url']);
							save_file_from_url($video_file['url'], $download_path, $download_referer);

							$filesize = @sprintf("%.0f", filesize($download_path));
							$duration = get_video_duration($download_path);
							if ($duration == 0)
							{
								log_feed($item, 2, "Failed to download remote file ($filesize bytes)", $video_file['url']);
								$has_downloaded_files = false;
								break;
							} else
							{
								$has_downloaded_files = true;
								log_feed($item, 0, "Finished downloading remote file ($filesize bytes)", $video_file['url']);
							}
						}
					}
				}
				if (!$has_downloaded_files)
				{
					log_feed($item, 2, 'Video doesn\'t have matching video formats');
					$videos_errored++;
					rmdir_recursive($screen_dir);
					rmdir_recursive($poster_dir);
					rmdir_recursive($download_temp_dir);
					continue;
				}
			} else
			{
				$video_postfix = '.tmp';
				if ($format_video_id > 0)
				{
					foreach ($formats_videos as $format)
					{
						if ($format_video_id == $format['format_video_id'])
						{
							$video_postfix = $format['postfix'];
							break;
						}
					}
				}

				$video_file = '';
				if (isset($video_rec['video_files']['source']))
				{
					$video_file = $video_rec['video_files']['source'];
				} elseif ($max_filesize_file)
				{
					$video_file = $max_filesize_file;
				}

				if ($video_file)
				{
					$download_path = "$download_temp_dir/file{$video_postfix}";
					log_feed($item, 0, 'Started downloading remote file', $video_file['url']);
					if ($video_file['quality'] && $insert_data['gallery_url'] && $grabber instanceof KvsGrabberVideoYDL)
					{
						$grabber->download_files($insert_data['gallery_url'], $video_file['quality'], $download_temp_dir);
						if ($format_video_id > 0)
						{
							@rename("$download_temp_dir/file.tmp", $download_path);
						}
					} else
					{
						$download_referer = '';
						if ($insert_data['gallery_url'])
						{
							$download_referer = $insert_data['gallery_url'];
						}
						save_file_from_url($video_file['url'], $download_path, $download_referer);
					}
					$filesize = @sprintf("%.0f", filesize($download_path));
					$duration = get_video_duration($download_path);
					if ($duration == 0)
					{
						log_feed($item, 2, "Failed to download remote file ($filesize bytes)", $video_file['url']);
						$videos_errored++;
						rmdir_recursive($screen_dir);
						rmdir_recursive($poster_dir);
						rmdir_recursive($download_temp_dir);
						continue;
					} else
					{
						log_feed($item, 0, "Finished downloading remote file ($filesize bytes)", $video_file['url']);
					}
				} else
				{
					log_feed($item, 2, "No video file to download");
					$videos_errored++;
					rmdir_recursive($screen_dir);
					rmdir_recursive($poster_dir);
					rmdir_recursive($download_temp_dir);
					continue;
				}
			}
		}

		flock($global_lock, LOCK_EX);

		$category_ids = [];
		if ($video_rec['categories'])
		{
			$value_temp = explode($list_separator, $video_rec['categories']);
			$inserted_categories = [];
			foreach ($value_temp as $cat_title)
			{
				$cat_title = trim($cat_title);
				if (!$cat_title || in_array(mb_lowercase($cat_title), $inserted_categories))
				{
					continue;
				}

				if ($categories_all[mb_lowercase($cat_title)] > 0)
				{
					$cat_id = $categories_all[mb_lowercase($cat_title)];
				} else
				{
					$cat_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $cat_title));
					if ($cat_id == 0 && !$is_skip_new_categories)
					{
						$cat_dir = get_correct_dir_name($cat_title);
						$temp_dir = $cat_dir;
						for ($it = 2; $it < 99999; $it++)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
							{
								$cat_dir = $temp_dir;
								break;
							}
							$temp_dir = $cat_dir . $it;
						}
						$cat_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $cat_title, $cat_dir, date('Y-m-d H:i:s'));
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=6, added_date=?", $feed['feed_id'], $feed['title'], $cat_id, date('Y-m-d H:i:s'));
					}
					if ($cat_id > 0)
					{
						$categories_all[mb_lowercase($cat_title)] = $cat_id;
					}
				}
				if ($cat_id > 0)
				{
					$inserted_categories[] = mb_lowercase($cat_title);
					$category_ids[] = $cat_id;
				}
			}
		}

		$model_ids = [];
		if ($video_rec['models'])
		{
			$value_temp = explode($list_separator, $video_rec['models']);
			$inserted_models = [];
			foreach ($value_temp as $model_title)
			{
				$model_title = trim($model_title);
				if (!$model_title || in_array(mb_lowercase($model_title), $inserted_models))
				{
					continue;
				}

				if ($models_all[mb_lowercase($model_title)] > 0)
				{
					$model_id = $models_all[mb_lowercase($model_title)];
				} else
				{
					$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
					if ($model_id == 0 && !$is_skip_new_models)
					{
						$model_dir = get_correct_dir_name($model_title);
						$temp_dir = $model_dir;
						for ($it = 2; $it < 99999; $it++)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
							{
								$model_dir = $temp_dir;
								break;
							}
							$temp_dir = $model_dir . $it;
						}
						$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date('Y-m-d H:i:s'));
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=4, added_date=?", $feed['feed_id'], $feed['title'], $model_id, date('Y-m-d H:i:s'));
					}
					if ($model_id > 0)
					{
						$models_all[mb_lowercase($model_title)] = $model_id;
					}
				}
				if ($model_id > 0)
				{
					$inserted_models[] = mb_lowercase($model_title);
					$model_ids[] = $model_id;
				}
			}
		}

		$tag_ids = [];
		if ($video_rec['tags'])
		{
			$value_temp = explode($list_separator, $video_rec['tags']);
			$inserted_tags = [];
			foreach ($value_temp as $tag_title)
			{
				$tag_title = trim($tag_title);
				if (!$tag_title || in_array(mb_lowercase($tag_title), $inserted_tags))
				{
					continue;
				}

				$tag_id = find_or_create_tag($tag_title, $options);
				if ($tag_id > 0)
				{
					$inserted_tags[] = mb_lowercase($tag_title);
					$tag_ids[] = $tag_id;
				}
			}
		}

		if ($video_rec['user'])
		{
			$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $video_rec['user']));
			if ($user_id == 0)
			{
				$email = $video_rec['user'];
				if (!preg_match($regexp_check_email, $email))
				{
					$email = generate_email($video_rec['user']);
				}
				$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?", $video_rec['user'], $video_rec['user'], $email, date("Y-m-d H:i:s"));
			}
			$insert_data['user_id'] = $user_id;
		}

		if ($feed['videos_content_source_id'] > 0)
		{
			$insert_data['content_source_id'] = $feed['videos_content_source_id'];
		} elseif ($video_rec['content_source'])
		{
			$insert_data['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $video_rec['content_source']));
			if ($insert_data['content_source_id'] == 0 && !$is_skip_new_content_sources)
			{
				$cs_dir = get_correct_dir_name($video_rec['content_source']);
				$temp_dir = $cs_dir;
				for ($it = 2; $it < 999999; $it++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
					{
						$cs_dir = $temp_dir;
						break;
					}
					$temp_dir = $cs_dir . $it;
				}
				$insert_data['content_source_id'] = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, url=?, rating_amount=1, added_date=?", $video_rec['content_source'], $cs_dir, trim($video_rec['content_source_url']), date('Y-m-d H:i:s'));
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=3, added_date=?", $feed['feed_id'], $feed['title'], $insert_data['content_source_id'], date('Y-m-d H:i:s'));

				if ($video_rec['content_source_group'])
				{
					$content_source_group_id = mr2number(sql_pr("select content_source_group_id from $config[tables_prefix]content_sources_groups where title=?", $video_rec['content_source_group']));
					if ($content_source_group_id == 0)
					{
						$group_dir = get_correct_dir_name($video_rec['content_source_group']);
						$temp_dir = $group_dir;
						for ($it = 2; $it < 999999; $it++)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where dir=?", $temp_dir)) == 0)
							{
								$group_dir = $temp_dir;
								break;
							}
							$temp_dir = $group_dir . $it;
						}
						$content_source_group_id = sql_insert("insert into $config[tables_prefix]content_sources_groups set title=?, dir=?, added_date=?", $video_rec['content_source_group'], $group_dir, date('Y-m-d H:i:s'));
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=8, added_date=?", $feed['feed_id'], $feed['title'], $content_source_group_id, date('Y-m-d H:i:s'));
					}
					if ($content_source_group_id > 0)
					{
						sql_update("update $config[tables_prefix]content_sources set content_source_group_id=? where content_source_id=?", $content_source_group_id, $insert_data['content_source_id']);
					}
				}
			}
		}

		if ($feed['videos_dvd_id'] > 0)
		{
			$insert_data['dvd_id'] = $feed['videos_dvd_id'];
		} elseif ($video_rec['dvd'])
		{
			$insert_data['dvd_id'] = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $video_rec['dvd']));
			if ($insert_data['dvd_id'] == 0 && !$is_skip_new_dvds)
			{
				$dvd_dir = get_correct_dir_name($video_rec['dvd']);
				$temp_dir = $dvd_dir;
				for ($it = 2; $it < 999999; $it++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?", $temp_dir)) == 0)
					{
						$dvd_dir = $temp_dir;
						break;
					}
					$temp_dir = $dvd_dir . $it;
				}
				$insert_data['dvd_id'] = sql_insert("insert into $config[tables_prefix]dvds set title=?, dir=?, rating_amount=1, added_date=?", $video_rec['dvd'], $dvd_dir, date('Y-m-d H:i:s'));
				sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=5, added_date=?", $feed['feed_id'], $feed['title'], $insert_data['dvd_id'], date('Y-m-d H:i:s'));

				if ($video_rec['dvd_group'])
				{
					$dvd_group_id = mr2number(sql_pr("select dvd_group_id from $config[tables_prefix]dvds_groups where title=?", $video_rec['dvd_group']));
					if ($dvd_group_id == 0)
					{
						$group_dir = get_correct_dir_name($video_rec['dvd_group']);
						$temp_dir = $group_dir;
						for ($it = 2; $it < 999999; $it++)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds_groups where dir=?", $temp_dir)) == 0)
							{
								$group_dir = $temp_dir;
								break;
							}
							$temp_dir = $group_dir . $it;
						}
						$dvd_group_id = sql_insert("insert into $config[tables_prefix]dvds_groups set title=?, dir=?, added_date=?", $video_rec['dvd_group'], $group_dir, date('Y-m-d H:i:s'));
						sql_insert("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=10, added_date=?", $feed['feed_id'], $feed['title'], $dvd_group_id, date('Y-m-d H:i:s'));
					}
					if ($dvd_group_id > 0)
					{
						sql_update("update $config[tables_prefix]dvds set dvd_group_id=? where dvd_id=?", $dvd_group_id, $insert_data['dvd_id']);
					}
				}
			}
		}

		if ($feed['title_limit'] > 0)
		{
			$video_rec['title'] = truncate_text($video_rec['title'], $feed['title_limit'], $feed['title_limit_type_id']);
		}
		foreach ($languages as $language)
		{
			if ($video_rec["title_$language[code]"])
			{
				if ($feed['title_limit'] > 0)
				{
					$video_rec["title_$language[code]"] = truncate_text($video_rec["title_$language[code]"], $feed['title_limit'], $feed['title_limit_type_id']);
				}
				$insert_data["title_$language[code]"] = $video_rec["title_$language[code]"];
			}
			if ($video_rec["description_$language[code]"])
			{
				$insert_data["description_$language[code]"] = $video_rec["description_$language[code]"];
			}
			if ($video_rec["dir_$language[code]"])
			{
				$insert_data["dir_$language[code]"] = $video_rec["dir_$language[code]"];
			}
		}

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where external_key=?", $video_rec['external_key'])) > 0)
		{
			flock($global_lock, LOCK_UN);
			log_feed($item, 0, 'Skipped (exists in database)');
			$videos_skipped++;
			continue;
		}

		if ($video_rec['title'] && intval($feed['is_skip_duplicate_titles']) == 1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where title=?", $video_rec['title'])) > 0)
		{
			flock($global_lock, LOCK_UN);
			log_feed($item, 0, 'Skipped (duplicate title)');
			$videos_skipped++;
			continue;
		}

		$insert_data['title'] = $video_rec['title'];
		if ($insert_data['title'] && !$insert_data['dir'])
		{
			$dir = get_correct_dir_name($insert_data['title']);
			$temp_dir = $dir;
			for ($it = 2; $it < 99999; $it++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=?", $temp_dir)) == 0)
				{
					$dir = $temp_dir;
					break;
				}
				$temp_dir = $dir . $it;
			}
			$insert_data['dir'] = $dir;
		}
		foreach ($languages as $language)
		{
			if ($insert_data["title_$language[code]"] && !$insert_data["dir_$language[code]"])
			{
				$dir = get_correct_dir_name($insert_data["title_$language[code]"], $language);
				$temp_dir = $dir;
				for ($it = 2; $it < 99999; $it++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir_$language[code]=?", $temp_dir)) == 0)
					{
						$dir = $temp_dir;
						break;
					}
					$temp_dir = $dir . $it;
				}
				$insert_data["dir_$language[code]"] = $dir;
			}
		}

		if (intval($video_rec['id']) > 0)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where video_id=?", intval($video_rec['id']))) > 0)
			{
				flock($global_lock, LOCK_UN);
				$videos_errored++;
				log_feed($item, 2, "Video with ID $video_rec[id] already exists");
				continue;
			} else
			{
				$insert_data['video_id'] = intval($video_rec['id']);
			}
		}

		flock($global_lock, LOCK_UN);

		$insert_data['added_date'] = date('Y-m-d H:i:s');
		$item_id = sql_insert("insert into $config[tables_prefix]videos set ?%", $insert_data);

		if ($item_id == 0)
		{
			$videos_errored++;
			log_feed($item, 2, 'Failed to insert new video');
			continue;
		}

		$tag_ids = array_unique($tag_ids);
		foreach ($tag_ids as $tag_id)
		{
			sql_insert("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?", $tag_id, $item_id);
		}
		$category_ids = array_unique($category_ids);
		foreach ($category_ids as $category_id)
		{
			sql_insert("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?", $category_id, $item_id);
		}
		$model_ids = array_unique($model_ids);
		foreach ($model_ids as $model_id)
		{
			sql_insert("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?", $model_id, $item_id);
		}

		$dir_path = get_dir_by_id($item_id);

		if (is_dir($screen_dir))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
			}
			if (!rename($screen_dir, "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots"))
			{
				log_video("ERROR  Failed to move dir to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots", $item_id);
			}
		}
		if (is_dir($poster_dir))
		{
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id/temp"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp", $item_id);
			}
			if (!rename($poster_dir, "$config[content_path_videos_sources]/$dir_path/$item_id/temp/posters"))
			{
				log_video("ERROR  Failed to move dir to directory: $config[content_path_videos_sources]/$dir_path/$item_id/temp/posters", $item_id);
			}
		}

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos_feeds_import_history where video_key=?", $video_rec['external_key'])) == 0)
		{
			sql_pr("insert into $config[tables_prefix]videos_feeds_import_history set video_key=?, feed_id=?, added_date=?", $video_rec['external_key'], $feed['feed_id'], date('Y-m-d H:i:s'));
		}

		$background_task = [];
		$background_task['status_id'] = intval($feed['videos_status_id']);
		$background_task['duration'] = $insert_data['duration'];
		if ($insert_data['file_url'])
		{
			$background_task['video_url'] = $insert_data['file_url'];
		}
		if ($video_rec['screen_main'] > 1)
		{
			$background_task['screen_main'] = intval($video_rec['screen_main']);
		}
		if ($video_rec['poster_main'] > 1)
		{
			$background_task['poster_main'] = intval($video_rec['poster_main']);
		}

		if ($insert_data['load_type_id'] == 1)
		{
			$background_task['source'] = "$item_id.tmp";
			if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
			{
				log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
			} else
			{
				if (is_file("$download_temp_dir/file.tmp"))
				{
					if (!rename("$download_temp_dir/file.tmp", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp") == 0)
					{
						log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id.tmp", $item_id);
					}
				} else
				{
					$temporary_size = 0;
					foreach ($formats_videos as $format)
					{
						if (is_file("$download_temp_dir/file{$format['postfix']}"))
						{
							if (!rename("$download_temp_dir/file{$format['postfix']}", "$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}") || filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}") == 0)
							{
								log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}", $item_id);
							}
							if (sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}")) > $temporary_size)
							{
								$background_task['source'] = "$item_id{$format['postfix']}";
								$temporary_size = sprintf("%.0f", filesize("$config[content_path_videos_sources]/$dir_path/$item_id/$item_id{$format['postfix']}"));
							}
						}
					}
				}
			}
		}

		rmdir_recursive($screen_dir);
		rmdir_recursive($poster_dir);
		rmdir_recursive($download_temp_dir);

		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=1, video_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
		sql_pr("insert into $config[tables_prefix]users_events set event_type_id=1, user_id=?, video_id=?, added_date=?", $insert_data['user_id'], $item_id, $insert_data['post_date']);
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=120, object_id=?, object_type_id=1, added_date=?", $feed['feed_id'], $feed['title'], $item_id, date("Y-m-d H:i:s"));

		if ($insert_data['gallery_url'] && $grabber instanceof KvsGrabber)
		{
			$grabber->post_process_inserted_object($item_id, $insert_data['gallery_url']);
		}

		$videos_added++;
		usleep(50000);
	}

	$exec_time = time() - $start_time;

	log_feed(0, 1, "Finished feed \"$feed[title]\" in $exec_time seconds with $videos_added videos added, $videos_skipped videos skipped and $videos_errored errors from $videos_total videos available");

	sql_pr("update $config[tables_prefix]videos_feeds_import set last_exec_duration=?, last_exec_videos_added=?, last_exec_videos_skipped=?, last_exec_videos_errored=? where feed_id=?", $exec_time, $videos_added, $videos_skipped, $videos_errored, $feed['feed_id']);
}

function parse_duration($str)
{
	if (!trim($str))
	{
		return 0;
	}
	$regex1 = "|^([0-9]+)h([0-9]+)m([0-9]+)s$|is";
	$regex2 = "|^([0-9]+)m([0-9]+)s$|is";
	if (preg_match($regex1, $str, $temp))
	{
		return intval($temp[1]) * 3600 + intval($temp[2]) * 60 + intval($temp[3]);
	} elseif (preg_match($regex2, $str, $temp))
	{
		return intval($temp[1]) * 60 + intval($temp[2]);
	} elseif (strpos($str, ":") !== false)
	{
		$temp = explode(":", $str);
		if (count($temp) == 3)
		{
			return intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
		} else
		{
			return intval($temp[0]) * 60 + intval($temp[1]);
		}
	} else
	{
		return intval($str);
	}
}

function log_feed($item_id, $message_type, $message_text, $message_details = '')
{
	global $config, $feed;

	if ($message_type == 0 && $feed['is_debug_enabled'] == 0)
	{
		return;
	}
	if ($item_id > 0)
	{
		$message_text = "[Item $item_id]: $message_text";
	}

	sql_pr("insert into $config[tables_prefix]feeds_log set feed_id=?, message_type=?, message_text=?, message_details=?, added_date=?", $feed['feed_id'], $message_type, $message_text, $message_details, date("Y-m-d H:i:s"));
}

function log_video($message, $video_id, $no_date = 0)
{
	global $config;

	if (intval($video_id) > 0)
	{
		if ($message)
		{
			if (intval($no_date) == 0)
			{
				$message = date('[Y-m-d H:i:s] ') . $message;
			}
		}
		file_put_contents("$config[project_path]/admin/logs/videos/$video_id.txt", "$message\n", FILE_APPEND | LOCK_EX);
	}
}
