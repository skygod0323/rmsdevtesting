<?php

function kvs_post_install()
{
	global $config;

	$database_version = mr2string(sql("select value from $config[tables_prefix]options where variable='SYSTEM_VERSION'"));
	if ($database_version == '')
	{
		return;
	}

	$servers_storage = mr2array(sql("select * from $config[tables_prefix]admin_servers"));
	$servers_conversion = mr2array(sql("select * from $config[tables_prefix]admin_conversion_servers"));
	if ($config['is_clone_db'] <> 'true' && is_dir($config['project_path']) && count($servers_storage) > 0 && count($servers_conversion) > 0)
	{
		foreach ($servers_storage as $server)
		{
			if (strpos($server['path'], '%PROJECT_PATH%') !== false)
			{
				$server['path'] = str_replace('%PROJECT_PATH%', rtrim($config['project_path'], '/'), $server['path']);
				sql_pr("update $config[tables_prefix]admin_servers set path=? where server_id=?", $server['path'], $server['server_id']);
			}
		}
		foreach ($servers_conversion as $server)
		{
			if (strpos($server['path'], '%PROJECT_PATH%') !== false)
			{
				$server['path'] = str_replace('%PROJECT_PATH%', rtrim($config['project_path'], '/'), $server['path']);
				sql_pr("update $config[tables_prefix]admin_conversion_servers set path=? where server_id=?", $server['path'], $server['server_id']);
			}
		}
	}

	file_put_contents("$config[project_path]/admin/data/system/initial_version.dat", $config['project_version']);

	if (mr2number(sql("select count(*) from $config[tables_prefix]list_countries")) == 0)
	{
		$countries = file_get_contents("$config[project_path]/admin/data/system/countries.csv");
		$countries = explode("\n", $countries);
		foreach ($countries as $country)
		{
			$country = trim($country);
			if ($country == '')
			{
				continue;
			}
			$country = explode(';', $country);
			foreach($country as $k=>$v)
			{
				$country[$k] = trim($v, '"');
			}
			sql_pr("insert into $config[tables_prefix]list_countries set country_id=?, country_code=?, language_code=?, title=?, is_system=?, continent_code=?, added_date=?", intval($country[0]), trim($country[1]), trim($country[2]), trim($country[3]), intval($country[4]), trim($country[5]), date("Y-m-d H:i:s"));
		}
	}
}