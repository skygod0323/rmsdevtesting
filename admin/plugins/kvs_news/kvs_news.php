<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function kvs_newsInit()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";
	mkdir_recursive($plugin_path);
	if (!is_file("$plugin_path/data.dat"))
	{
		$data = [];
		$data['is_disabled'] = 0;
		$data['last_exec_date'] = '0000-00-00 00:00:00';
		$data['duration'] = '0';

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
	if (!is_file("$plugin_path/cron.dat"))
	{
		file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
	}
}

function kvs_newsIsEnabled()
{
	global $config;

	kvs_newsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$save_data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($save_data))
	{
		return true;
	}

	return intval($save_data['is_disabled']) != 1;
}

function kvs_newsShow()
{
	global $config, $errors, $page_name;

	kvs_newsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$errors = null;

	if ($_POST['action'] == 'change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			$save_data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
			$save_data['is_disabled'] = intval($_POST['is_disabled']);

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			if (intval($_POST['is_disabled']) == 0)
			{
				file_put_contents("$plugin_path/cron.dat", time(), LOCK_EX);
			} else
			{
				unlink("$plugin_path/cron.dat");
			}

			return_ajax_success("$page_name?plugin_id=kvs_news");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat")));
	}
}

function kvs_newsGetNews()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data) || $data['is_disabled'] == 1 || !is_array($data['news']))
	{
		return [];
	}

	return array_reverse($data['news']);
}

function kvs_newsGetLatestVersion()
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		return '';
	}

	return $data['latest_version'];
}

function kvs_newsRedirectNews($news_id)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		return;
	}

	foreach ($data['news'] as $k => $v)
	{
		if ($v['news_id'] == $news_id)
		{
			unset($data['news'][$k]);
		}
	}
	file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

	$language = 'en';
	if ($_SESSION['userdata']['lang'] == 'russian')
	{
		$language = 'ru';
	}
	header("Location: https://www.kernel-video-sharing.com/$language/news/$news_id/");
	die;
}

function kvs_newsDeleteNews($news_id)
{
	global $config;

	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		return;
	}

	if (intval($news_id) == 0)
	{
		$data['news'] = [];
	} else
	{
		foreach ($data['news'] as $k => $v)
		{
			if ($v['news_id'] == $news_id)
			{
				unset($data['news'][$k]);
			}
		}
	}
	file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
}

function kvs_newsCron()
{
	global $config;

	$start = time();

	kvs_newsInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/kvs_news";

	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"), ['allowed_classes' => false]);
	if (!is_array($data))
	{
		$data = [];
	}
	if (!is_array($data['news']))
	{
		$data['news'] = [];
	}

	$last_news_id = intval($data['last_news_id']);
	if ($last_news_id == 0)
	{
		$news = @unserialize(get_page('', "https://www.kernel-scripts.com/news/php/", '', '', 1, 0, 50, ''), ['allowed_classes' => false]);
		if ($news && is_array($news))
		{
			$news = [end($news)];
		} else
		{
			$news = [];
		}
		$data['news'] = $news;
	} else
	{
		$news = @unserialize(get_page('', "https://www.kernel-scripts.com/news/php/?from_id=$last_news_id", '', '', 1, 0, 50, ''), ['allowed_classes' => false]);
		if ($news && is_array($news))
		{
			$data['news'] = @array_merge((array)$data['news'], (array)$news);
		}
	}

	if (is_array($data['news']))
	{
		foreach ($data['news'] as $item)
		{
			if ($item['news_id'] > $last_news_id)
			{
				$last_news_id = $item['news_id'];
			}
		}
	}

	$feature_plugin_pn = 0;
	if (is_file("$config[project_path]/admin/data/plugins/push_notifications/enabled.dat"))
	{
		$feature_plugin_pn = 1;
	}

	$post_date_yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));
	$stats_result = mr2array_single(sql("select sum(uniq_amount) as uniq_amount, sum(uniq_amount + raw_amount) as total_amount, sum(view_video_amount + view_album_amount) as content_amount, sum(summary_amount) as summary_amount, sum(cs_out_amount + adv_out_amount) as out_amount, sum(view_embed_amount) as view_embed_amount from $config[tables_prefix_multi]stats_in where added_date='$post_date_yesterday'"));

	$project_url = urlencode($config['project_url']);
	$project_version = urlencode($config['project_version']);
	$version = get_page('', "https://www.kernel-scripts.com/get_version/?url=$project_url&version=$project_version&stats_unique=" . intval($stats_result['uniq_amount']) . "&stats_total=" . intval($stats_result['total_amount']) . "&stats_content=" . intval($stats_result['content_amount']) . "&stats_summary=" . intval($stats_result['summary_amount']) . "&stats_out=" . intval($stats_result['out_amount']) . "&stats_embed=" . intval($stats_result['view_embed_amount']) . "&feature_plugin_pn=$feature_plugin_pn", '', '', 1, 0, 50, '');
	if (preg_match("|^\d+\.\d+\.\d+$|is", $version) && intval(str_replace('.', '', $version)) >= 300)
	{
		$data['latest_version'] = $version;
	}

	kvs_newsLog("Last news ID: $last_news_id");
	kvs_newsLog("Latest KVS version: $version");

	$data['last_news_id'] = $last_news_id;
	$data['last_exec_date'] = $start;
	$data['duration'] = time() - $start;

	file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	file_put_contents("$plugin_path/cron.dat", $start + 4 * 60 * 60, LOCK_EX);
}

function kvs_newsLog($message)
{
	global $config;

	file_put_contents("$config[project_path]/admin/logs/plugins/kvs_news.txt", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
