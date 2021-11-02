<?php

if (isset($config) && $config['memcache_server'] != '' && class_exists('Memcache') && !isset($memcache))
{
	$memcache = new Memcache();
	if (!$memcache->connect($config['memcache_server'], $config['memcache_port']))
	{
		$memcache = null;
	}
}

$start_time = microtime(true);
$lang = array();
kvs_read_lang_file("$config[project_path]/langs/default.lang");
if ($config['locale'] != '')
{
	kvs_read_lang_file("$config[project_path]/langs/$config[locale].lang", "$config[project_path]/langs/default.lang");
}

if ($lang['php']['default_lang'] != '')
{
	setlocale(LC_ALL, $lang['php']['default_lang']);
}

function kvs_read_lang_file($filename, $base_filename = null)
{
	/** @var Memcache $memcache */
	global $config, $lang, $memcache;

	if (is_file($filename))
	{
		$cache_key = md5("kvs_theme_locale|$config[project_url]|$filename|" . filemtime($filename) . "|" . filesize($filename) . ($base_filename ? "|" . filemtime($base_filename) . "|" .filesize($base_filename) : ""));
		if ($memcache)
		{
			$cached_lang = $memcache->get($cache_key);
			if (is_array($cached_lang) && count($cached_lang) > 1)
			{
				$lang = array_merge($lang, $cached_lang);
				return;
			}
		}

		$file = fopen($filename, "r");
		while (($row = fgets($file)) !== false)
		{
			$row = trim($row);
			if ($row == '' || strpos($row, '#') === 0)
			{
				continue;
			}

			$pair = explode('=', $row, 2);
			if (count($pair) == 2)
			{
				$keys = explode('.', trim($pair[0]));
				$keys_count = count($keys);
				$lang_array = &$lang;
				$is_url = false;
				foreach ($keys as $i => $key)
				{
					if ($i == 0 && $key == 'urls')
					{
						$is_url = true;
					}
					if ($i == $keys_count - 1)
					{
						$value = trim($pair[1]);
						if (strpos($value, "\"") === 0)
						{
							$value = trim($value, "\"");
						}
						if ($is_url)
						{
							if (strpos($value, 'http://') !== 0 && strpos($value, 'https://') !== 0)
							{
								$value = "$config[project_url]$value";
							}
						} elseif (strpos($value, 'array(') === 0)
						{
							if (trim(substr($value, 6, -1)) == '')
							{
								$value = array();
							} else
							{
								$value = array_map('trim', explode(',', substr($value, 6, -1)));
							}
						}
						$lang_array[$key] = $value;
					} else
					{
						if (!isset($lang_array[$key]))
						{
							$lang_array[$key] = array();
						}
						$lang_array = &$lang_array[$key];
					}
				}
			}
		}

		if ($memcache && is_array($lang) && count($lang) > 1)
		{
			$memcache->set($cache_key, $lang);
		}
	}
}