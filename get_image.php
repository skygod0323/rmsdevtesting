<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'admin/include/setup.php';

$sg_id=intval($_GET['sg_id']);
$hash=$_GET['hash'];
$file=trim(rtrim($_GET['file'],"/"));
$admin_rq_server_id=intval($_GET['admin_rq_server_id']);
$download_filename=trim($_GET['download_filename']);

// check link hash validity
$hash_check=md5($config['cv'].$file);
if ($hash_check<>$hash)
{
	header("HTTP/1.0 404 Not found");die;
}

$disable_security_check=false;
if (isset($_REQUEST['dsc']))
{
	$disable_security_check_ttl=intval($_REQUEST['ttl']);
	if (md5("$config[cv]/$hash/$file/$disable_security_check_ttl")==$_REQUEST['dsc'])
	{
		if (abs($disable_security_check_ttl-time())<86400)
		{
			$disable_security_check=true;
		}
	}
}

if (strpos($file,'sources')===0)
{
	preg_match("@^sources/\d+/(\d+).+@is",$file,$temp);
	$album_id=$temp[1];
	$size='sources';

	// request for source file, check if it is allowed for the current user
	$options=@unserialize(@file_get_contents("admin/data/system/mixed_options.dat"));
	$access_level=intval($options['ALBUMS_SOURCE_FILES_ACCESS_LEVEL']);
	if ($access_level>0 && !$disable_security_check)
	{
		session_start();
		if ($_SESSION['userdata']['user_id']<1)
		{
			if ($access_level==3)
			{
				if (is_file("$config[content_path_other]/access_level_album_source.jpg"))
				{
					header("Location: $config[content_url_other]/access_level_album_source.jpg");die;
				}
				header("HTTP/1.0 403 Forbidden");
				echo "Access denied / Image source files are not available publicly";die;
			} elseif ($access_level==2 && $_SESSION['status_id']!=3)
			{
				$has_premium_access_as_owner=false;
				if (isset($_REQUEST['ov']) && md5($config['cv'].$_SESSION['user_id'])==$_REQUEST['ov'])
				{
					$has_premium_access_as_owner=true;
				}

				$has_premium_access_by_tokens=false;
				if ($_SESSION['status_id']==2)
				{
					foreach ($_SESSION['content_purchased'] as $purchase)
					{
						if ($purchase['album_id']==$album_id)
						{
							$has_premium_access_by_tokens=true;
							break;
						}
					}
				}
				if (!$has_premium_access_by_tokens && !$has_premium_access_as_owner)
				{
					if (is_file("$config[content_path_other]/access_level_album_source.jpg"))
					{
						header("Location: $config[content_url_other]/access_level_album_source.jpg");die;
					}
					header("HTTP/1.0 403 Forbidden");
					echo "Access denied / Only Premium members can access image source files";die;
				}
			} elseif ($access_level==1 && $_SESSION['user_id']<1)
			{
				if (is_file("$config[content_path_other]/access_level_album_source.jpg"))
				{
					header("Location: $config[content_url_other]/access_level_album_source.jpg");die;
				}
				header("HTTP/1.0 403 Forbidden");
				echo "Access denied / Only active members can access image source files";die;
			}
		}
	}
} else {
	$formats_albums=@unserialize(@file_get_contents("admin/data/system/formats_albums.dat"));
	preg_match("@^(main|preview)/(\d+)x(\d+)/\d+/(\d+).+@is",$file,$temp);
	$group_id=1;
	if ($temp[1]=='preview')
	{
		$group_id=2;
	}
	$size="$temp[2]x$temp[3]";
	$album_id=$temp[4];
	foreach ($formats_albums as $format)
	{
		if ($size==$format['size'] && $group_id==$format['group_id'])
		{
			$current_format=$format;
			break;
		}
	}

	// check if user has access level for watching this album format
	$format_id=$current_format['format_album_id'];
	if ($current_format['access_level_id']>0 && !$disable_security_check)
	{
		session_start();
		if ($_SESSION['userdata']['user_id']<1)
		{
			if ($current_format['access_level_id']==1 && $_SESSION['user_id']<1)
			{
				if (is_file("$config[content_path_other]/access_level_album_{$format_id}.jpg"))
				{
					header("Location: $config[content_url_other]/access_level_album_{$format_id}.jpg");die;
				}
				header("HTTP/1.0 403 Forbidden");
				echo "Access denied / Only active members can access this image file";die;
			} elseif ($current_format['access_level_id']==2 && $_SESSION['status_id']!=3)
			{
				$has_premium_access_as_owner=false;
				if (isset($_REQUEST['ov']) && md5($config['cv'].$_SESSION['user_id'])==$_REQUEST['ov'])
				{
					$has_premium_access_as_owner=true;
				}

				$has_premium_access_by_tokens=false;
				if ($_SESSION['status_id']==2)
				{
					foreach ($_SESSION['content_purchased'] as $purchase)
					{
						if ($purchase['album_id']==$album_id)
						{
							$has_premium_access_by_tokens=true;
							break;
						}
					}
				}
				if (!$has_premium_access_by_tokens && !$has_premium_access_as_owner)
				{
					if (is_file("$config[content_path_other]/access_level_album_{$format_id}.jpg"))
					{
						header("Location: $config[content_url_other]/access_level_album_{$format_id}.jpg");die;
					}
					header("HTTP/1.0 403 Forbidden");
					echo "Access denied / Only Premium members can access this image file";die;
				}
			}
		}
	}
}

if ($_SERVER['REQUEST_METHOD']!='HEAD')
{
	$stats_settings=@unserialize(@file_get_contents("admin/data/system/stats_params.dat"));
	if (intval($stats_settings['collect_albums_stats_album_images'])==1 || intval($stats_settings['collect_memberzone_stats_album_images'])==1)
	{
		$user_id=0;
		if (intval($stats_settings['collect_memberzone_stats_album_images'])==1)
		{
			if (!session_id()) {session_start();}
			$user_id=intval($_SESSION['user_id']);
		}

		$fh=fopen("$config[project_path]/admin/data/stats/album_files.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,"$album_id||".date("Y-m-d H:i:s")."||$size||$user_id||\r\n");
		fclose($fh);
	}
}

$data=unserialize(file_get_contents("admin/data/system/cluster.dat"));
$countries=array();

// filter out servers from other groups and countries
$data_user_country=array();
$sum_weight_country=0;
$data_other=array();
$sum_weight_other=0;
$data_default=array();

foreach ($data as $server)
{
	if ($admin_rq_server_id==$server['server_id'])
	{
		$server_admin_rq=$server;
	} elseif ($server['status_id']==1 && intval($server['group_id'])==$sg_id)
	{
		$countries=explode(',',$server['lb_countries']);
		if (strlen($server['lb_countries'])>0 && @count($countries)>0)
		{
			foreach ($countries as $country)
			{
				if (strtolower(trim($country))==strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
				{
					$data_user_country[]=$server;
					$sum_weight_country+=$server['lb_weight'];
				}
			}
		} else {
			$data_other[]=$server;
			$sum_weight_other+=$server['lb_weight'];
		}
		$data_default[]=$server;
	}
}

unset($target_file, $target_server, $is_remote, $control_script, $control_script_lock_ip);

// determine which server should be used for returning image
if (isset($server_admin_rq))
{
	$target_file="$server_admin_rq[urls]/$file";
	$target_server=$server_admin_rq['urls'];
	$is_remote=intval($server_admin_rq['is_remote']);
	$control_script=$server_admin_rq['control_script_url'];
	$control_script_lock_ip=intval($server_admin_rq['control_script_url_lock_ip']);
	$time_offset=$server_admin_rq['time_offset'];
	$streaming_type_id=$server_admin_rq['streaming_type_id'];
	$streaming_api_script=$server_admin_rq['streaming_script'];
	$streaming_api_name=str_replace(".php","",$streaming_api_script);
	$streaming_key=$server_admin_rq['streaming_key'];
	$is_replace_domain_on_satellite=intval($server_admin_rq['is_replace_domain_on_satellite']);
} else
{
	if (@count($data_user_country)>0)
	{
		$data_lb=$data_user_country;
		$sum_weight=$sum_weight_country;
	} else {
		$data_lb=$data_other;
		$sum_weight=$sum_weight_other;
	}

	if (@count($data_lb)==1)
	{
		$result_server=$data_lb[0];
	} else {
		$time=time();
		$time -= ($time % 300);
		srand($time+$album_id);
		$lb_value=rand(1,$sum_weight);
		$cur_value=0;
		foreach ($data_lb as $server)
		{
			if ($lb_value<=$cur_value+$server['lb_weight'])
			{
				$result_server=$server;
				break;
			}
			$cur_value+=$server['lb_weight'];
		}
		if (!isset($result_server))
		{
			$result_server=$data_default[0];
		}

		if (in_array($result_server['error_id'],[2,3,4,5,6]))
		{
			foreach ($data_lb as $server)
			{
				if (!in_array($server['error_id'],[2,3,4,5,6]))
				{
					$result_server=$server;
					break;
				}
			}
		}
	}

	$target_file="$result_server[urls]/$file";
	$target_server=$result_server['urls'];
	$is_remote=intval($result_server['is_remote']);
	$control_script=$result_server['control_script_url'];
	$control_script_lock_ip=intval($result_server['control_script_url_lock_ip']);
	$time_offset=$result_server['time_offset'];
	$streaming_type_id=$result_server['streaming_type_id'];
	$streaming_api_script=$result_server['streaming_script'];
	$streaming_api_name=str_replace(".php","",$streaming_api_script);
	$streaming_key=$result_server['streaming_key'];
	$is_replace_domain_on_satellite=intval($result_server['is_replace_domain_on_satellite']);
}

if ($is_replace_domain_on_satellite==1)
{
	if ($config['is_clone_db']=="true" && $config['satellite_for']!='')
	{
		$target_file=str_replace($config['satellite_for'],$config['project_licence_domain'],$target_file);
		$control_script=str_replace($config['satellite_for'],$config['project_licence_domain'],$control_script);
		if (strpos($target_file,'https://')!==false && strpos($config['project_url'],'https://')===false)
		{
			$target_file=str_replace('https://','http://',$target_file);
		}
		if (strpos($control_script,'https://')!==false && strpos($config['project_url'],'https://')===false)
		{
			$control_script=str_replace('https://','http://',$control_script);
		}
	}
	if ($config['mirror_for']!='')
	{
		$target_file=str_replace($config['mirror_for'],$config['project_licence_domain'],$target_file);
		$control_script=str_replace($config['mirror_for'],$config['project_licence_domain'],$control_script);
		if (strpos($target_file,'https://')!==false && strpos($config['project_url'],'https://')===false)
		{
			$target_file=str_replace('https://','http://',$target_file);
		}
		if (strpos($control_script,'https://')!==false && strpos($config['project_url'],'https://')===false)
		{
			$control_script=str_replace('https://','http://',$control_script);
		}
	}
}

if ($streaming_type_id==0)
{
	if ($is_remote==1)
	{
		$temp=explode("/",substr($target_server,8),2);
		$target_server=$temp[1];
		$target_server=trim($target_server,"/");
		if ($target_server<>'')
		{
			$file="/$target_server/$file";
		} else {
			$file="/$file";
		}
		$time=time();
		if (floatval($time_offset)<>0)
		{
			$time+=floatval($time_offset)*3600;
		}

		$download_str_append='';
		if (strpos($file,'.zip')!==false)
		{
			if ($download_filename=='')
			{
				$download_filename=basename($file);
			}
			$download_str_append="&download=true&download_filename=$download_filename";
		}

		$ref_host=str_replace("www.","",$_SERVER['HTTP_HOST']);

		$secret_remote_key = $config['cv'];
		if ($config['cvr'])
		{
			$secret_remote_key = $config['cvr'];
		}

		header("Cache-Control: nocache");
		if ($control_script_lock_ip==1)
		{
			$file_info=array(
				'time'=>$time,
				'limit'=>0,
				'file'=>$file,
				'cv'=>md5($time."0".$file.$_SERVER['REMOTE_ADDR'].$secret_remote_key),
			);
			header("Location: $control_script?file=B64".rawurlencode(base64_encode(serialize($file_info))).$download_str_append);
		} else
		{
			header("Location: $control_script?time=".$time."&cv2=".md5($time."0".$secret_remote_key)."&file=".rawurlencode($file)."&cv3=".md5($ref_host.$secret_remote_key)."&cv4=".md5($file.$secret_remote_key).$download_str_append);
		}
	} else {
		$target_file=substr($target_file,strpos($target_file,'/',8));

		if (strpos($target_file,'.zip')!==false)
		{
			$short_file_name=basename($target_file);
			if ($download_filename<>'')
			{
				$short_file_name=$download_filename;
			}
			header("Content-Type: application/zip");
			header("Content-Disposition: attachment; filename=\"$short_file_name\"");
		} else {
			header("Content-Type: image/jpeg");
		}
		header("X-Accel-Redirect: $target_file");
	}
} elseif ($streaming_type_id==4)
{
	if (is_file("$config[project_path]/admin/cdn/$streaming_api_script"))
	{
		require_once "$config[project_path]/admin/cdn/$streaming_api_script";
		$get_image_function="{$streaming_api_name}_get_image";
		if (function_exists($get_image_function))
		{
			$target_url=$target_file;
			$target_file=substr($target_file,strpos($target_file,'/',8));
			$image_url=$get_image_function($target_file,$target_url,$streaming_key);
			if ($image_url)
			{
				header("Location: $image_url");die;
			} else {
				header("Location: $target_url");die;
			}
		}
	}
	header("Location: $target_file");
} else {
	header("Location: $target_file?rnd=".time());
}
