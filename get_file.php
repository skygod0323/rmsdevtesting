<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'admin/include/setup.php';

if ($_GET['action']=='check_ip')
{
	header("KVS-IP: $_SERVER[REMOTE_ADDR]");
	echo $_SERVER['REMOTE_ADDR'];
	die;
}

$sg_id=intval($_GET['sg_id']);
$hash=$_GET['hash'];
$hash2='';
if (strlen($hash)>32)
{
	$hash2=substr($hash,32);
	$hash=substr($hash,0,32);
}
$file=trim(rtrim($_GET['file'],"/"));
$admin_rq_server_id=intval($_GET['admin_rq_server_id']);
$is_download=trim($_GET['download']);
$download_filename=trim($_GET['download_filename']);

// check link hash validity
$hash_check=md5($config['cv'].$file);
if ($hash_check<>$hash)
{
	debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI] Invalid link hash $hash for file $file");
	header("HTTP/1.0 404 Not found");die;
}

if ($sg_id==0)
{
	// request for source file on main server
	$target_file="$config[content_url_videos_sources]/$file";
	if ($config['server_type']=='nginx')
	{
		$target_file=substr($target_file,strpos($target_file,'/',8));
		$short_file_name=end(explode("/",$file));
		if ($download_filename<>'')
		{
			$short_file_name=$download_filename;
		}
		$file_ext=strtolower(end(explode(".",$file)));
		if ($file_ext=='jpg')
		{
			header("Content-type: image/jpeg");
			header("Content-Disposition: inline; filename=\"$short_file_name\"");
		} else {
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"$short_file_name\"");
		}
		header("X-Accel-Redirect: $target_file");
	} else {
		header("Location: $target_file");
	}
	die;
}

// check format validity
$formats_videos=@unserialize(@file_get_contents("admin/data/system/formats_videos.dat"));
preg_match("|\d+/(\d+)/\d+(.+)|is",$file,$temp);
$video_id=$temp[1];
$postfix=$temp[2];

foreach($formats_videos as $format)
{
	if ($postfix==$format['postfix'])
	{
		$current_format=$format;
		break;
	}
}
if (!isset($current_format))
{
	debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  No video format is available for file $file");
	header("HTTP/1.0 404 Not found");
	echo "File format is not found";
	die;
}

$hotlink_info=@unserialize(@file_get_contents("admin/data/system/hotlink_info.dat"));

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

if (stripos($_SERVER['HTTP_USER_AGENT'], 'google') !== false)
{
	$domain = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	if (substr($domain, -strlen('googlebot.com')) == 'googlebot.com' || substr($domain, -strlen('google.com')) == 'google.com')
	{
		debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Disabled protection for google bot: $_SERVER[HTTP_USER_AGENT], $domain");
		$disable_security_check = true;
	} else {
		debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Failed to disable protection for google bot: $_SERVER[HTTP_USER_AGENT], $domain");
	}
}

$white_ips=$hotlink_info['ANTI_HOTLINK_WHITE_IPS'];
if ($white_ips!='')
{
	$white_ips=array_map('trim',explode(',',$white_ips));
	if (in_array($_SERVER['REMOTE_ADDR'],$white_ips))
	{
		debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Disabled protection for whitelisted IP");
		$disable_security_check = true;
	}
}

// if antihotlink is enabled, check protection data
if ($hotlink_info['ENABLE_ANTI_HOTLINK']==1 && !$disable_security_check)
{
	if ($current_format['is_hotlink_protection_disabled']==0)
	{
		if (intval($hotlink_info['ANTI_HOTLINK_TYPE'])==1)
		{
			$allowed=0;
			$hash_check2=substr(md5($hash.$config['cv'].$_SERVER['REMOTE_ADDR']),0,10);
			if ($hash_check2==$hash2)
			{
				$allowed=1;
			}

			if ($allowed==0)
			{
				$hash_encoded=$hash;
				if ($hotlink_info['ANTI_HOTLINK_ENCODE_LINKS']==1)
				{
					for ($i=0;$i<strlen($hash_encoded);$i++)
					{
						$new_pos=$i;
						for ($j=$i;$j<strlen($config['ahv']);$j++)
						{
							$val=intval($config['ahv'][$j]);
							$new_pos+=$val;
						}
						while ($new_pos>=strlen($hash_encoded))
						{
							$new_pos-=strlen($hash_encoded);
						}
						$t=$hash_encoded[$i];
						$hash_encoded[$i]=$hash_encoded[$new_pos];
						$hash_encoded[$new_pos]=$t;
					}
					$hash_check2=substr(md5($hash_encoded.$config['cv'].$_SERVER['REMOTE_ADDR']),0,10);
					if ($hash_check2==$hash2)
					{
						$allowed=1;
					}
				}
				if ($allowed==0)
				{
					$check_ips=array();
					if (trim($_COOKIE['kt_ips'])!='')
					{
						$check_ips=explode(',', trim($_COOKIE['kt_ips']));
					} elseif ($_SERVER['HTTP_COOKIES']!='')
					{
						$cookies=explode(';',$_SERVER['HTTP_COOKIES']);
						foreach ($cookies as $cookie)
						{
							if (strpos(trim($cookie),'kt_ips=')===0)
							{
								$cookie=explode('=',$cookie,2);
								$check_ips=explode(',',trim(urldecode($cookie[1])));
								break;
							}
						}
					}
					if (is_array($check_ips))
					{
						foreach ($check_ips as $check_ip)
						{
							$check_ip=trim($check_ip);
							$hash_check2=substr(md5($hash.$config['cv'].$check_ip),0,10);
							if ($hash_check2==$hash2)
							{
								$allowed=1;
								break;
							}
							$hash_check2=substr(md5($hash_encoded.$config['cv'].$check_ip),0,10);
							if ($hash_check2==$hash2)
							{
								$allowed=1;
							}
						}
					}
				}
				if ($allowed==0)
				{
					session_start();
					$check_ips=$_SESSION['lock_ips'];
					if (is_array($check_ips))
					{
						foreach ($check_ips as $check_ip=>$v)
						{
							$hash_check2=substr(md5($hash.$config['cv'].$check_ip),0,10);
							if ($hash_check2==$hash2)
							{
								$allowed=1;
								break;
							}
							$hash_check2=substr(md5($hash_encoded.$config['cv'].$check_ip),0,10);
							if ($hash_check2==$hash2)
							{
								$allowed=1;
							}
						}
					}
				}
			}

			if ($allowed==0)
			{
				debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Access denied due to invalid IP: $_SERVER[REMOTE_ADDR]");
				if (trim($hotlink_info['ANTI_HOTLINK_FILE'])<>'' && $admin_rq_server_id==0)
				{
					header("Location: ".trim($hotlink_info['ANTI_HOTLINK_FILE']));
				} else {
					header("HTTP/1.0 403 Forbidden");
					echo "Access denied (errno 3)";
				}
				die;
			}
		} else {
			if ($_SERVER['HTTP_REFERER']<>'')
			{
				$referer=str_replace("www.","",$_SERVER['HTTP_REFERER']);
				if (strpos($referer,str_replace("www.","",$config['project_url']))!==0)
				{
					$ref_host=parse_url($referer,PHP_URL_HOST);
					$host=parse_url(str_replace("www.","",$config['project_url']),PHP_URL_HOST);
					if (strpos($ref_host,".$host")===false)
					{
						$allowed=0;
						if ($hotlink_info['ANTI_HOTLINK_WHITE_DOMAINS']!='')
						{
							$white_domains=explode(',',$hotlink_info['ANTI_HOTLINK_WHITE_DOMAINS']);
							foreach ($white_domains as $white_domain)
							{
								$host=trim(str_replace("http://","",str_replace("https://","",str_replace("www.","",$white_domain))));
								if (strpos($ref_host,$host)!==false)
								{
									$allowed=1;
									break;
								}
							}
						}
						if ($ref_host===$host)
						{
							$allowed=1;
						}

						if ($allowed==0)
						{
							debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Access denied due to invalid referer: $_SERVER[HTTP_REFERER]");
							if (trim($hotlink_info['ANTI_HOTLINK_FILE'])<>'' && $admin_rq_server_id==0)
							{
								header("Location: ".trim($hotlink_info['ANTI_HOTLINK_FILE']));
							} else {
								header("HTTP/1.0 403 Forbidden");
								echo "Access denied (errno 1)";
							}
							die;
						}
					}
				}
			}
		}
	}

	$range_start=0;
	if ($_SERVER['HTTP_RANGE']!='')
	{
		unset($ranges);
		if (preg_match('/^bytes=((\d*-\d*,? ?)+)$/',$_SERVER['HTTP_RANGE'],$ranges))
		{
			$ranges=explode('-',$ranges[1]);
			$range_start=intval($ranges[0]);
		}
	}

	if ($range_start==0 && intval($hotlink_info['ANTI_HOTLINK_ENABLE_IP_LIMIT'])==1)
	{
		// log ip data for ip protection
		file_put_contents("$config[project_path]/admin/data/stats/ip_data.dat", "$_SERVER[REMOTE_ADDR]|".time()."\r\n", FILE_APPEND | LOCK_EX);

		$bad_ips=@file("$config[project_path]/admin/data/stats/ip_blocked.dat");
		$bad_ips=array_map('trim',$bad_ips);

		if (in_array($_SERVER['REMOTE_ADDR'],$bad_ips))
		{
			debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Access denied due to blocked IP: $_SERVER[REMOTE_ADDR]");
			if (trim($hotlink_info['ANTI_HOTLINK_FILE'])<>'' && $admin_rq_server_id==0)
			{
				header("Location: ".trim($hotlink_info['ANTI_HOTLINK_FILE']));
			} else {
				header("HTTP/1.0 403 Forbidden");
				echo "Access denied (errno 4)";
			}
			die;
		}
	}
}

if ($current_format['access_level_id']>0 && !$disable_security_check)
{
	session_start();
	if ($_SESSION['userdata']['user_id']<1)
	{
		// check if user has access level for watching this video format
		if ($current_format['access_level_id']==1 && $_SESSION['user_id']<1)
		{
			debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Access denied due to member-protected file access");
			header("HTTP/1.0 403 Forbidden");
			echo "Access denied / Only active members can access this video file";die;
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
					if ($purchase['video_id']==$video_id)
					{
						$has_premium_access_by_tokens=true;
						break;
					}
				}
			}
			if (!$has_premium_access_by_tokens && !$has_premium_access_as_owner)
			{
				debug_get_file("$_SERVER[REMOTE_ADDR]  $_SERVER[REQUEST_URI]  Access denied due to premium-protected file access");
				header("HTTP/1.0 403 Forbidden");
				echo "Access denied / Only Premium members can access this video file";die;
			}
		}
	}
}

if ($_SERVER['REQUEST_METHOD']!='HEAD')
{
	$stats_settings=@unserialize(@file_get_contents("admin/data/system/stats_params.dat"));
	if (intval($stats_settings['collect_videos_stats_video_files'])==1 || intval($stats_settings['collect_memberzone_stats_video_files'])==1)
	{
		$user_id=0;
		if (intval($stats_settings['collect_memberzone_stats_video_files'])==1)
		{
			if (!session_id()) {session_start();}
			$user_id=intval($_SESSION['user_id']);
		}

		$fh=fopen("$config[project_path]/admin/data/stats/video_files.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,"$video_id||".date("Y-m-d H:i:s")."||$postfix||$user_id||".intval($_REQUEST['start_sec'])."\r\n");
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

// determine which server should be used for returning video
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
	$streaming_param=$server_admin_rq['streaming_param'];
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
		$time-=($time%300);
		srand($time+$video_id);
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
	$streaming_param=$result_server['streaming_param'];
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

if (floatval($_REQUEST['start'])>0)
{
	$start_param_name='start';
	if ($streaming_param<>'')
	{
		$start_param_name=$streaming_param;
	}
	$start_str="?$start_param_name=".floatval($_REQUEST['start']);
	$start_str_append="&$start_param_name=".floatval($_REQUEST['start']);
	$start_str_empty="$start_param_name=".floatval($_REQUEST['start']);
}

$limit=0;
if (intval($current_format['limit_speed_option'])+intval($current_format['limit_speed_guests_option'])+intval($current_format['limit_speed_standard_option'])+intval($current_format['limit_speed_premium_option'])+intval($current_format['limit_speed_embed_option'])>0)
{
	$limit_option=intval($current_format['limit_speed_option']);
	$limit_value=floatval($current_format['limit_speed_value']);
	if (intval($current_format['limit_speed_option'])!=intval($current_format['limit_speed_guests_option']) || number_format($current_format['limit_speed_value'],1)!=number_format($current_format['limit_speed_guests_value'],1))
	{
		if (!session_id()) {session_start();}
		if (intval($_SESSION['user_id'])==0)
		{
			$limit_option=intval($current_format['limit_speed_guests_option']);
			$limit_value=floatval($current_format['limit_speed_guests_value']);
		}
	}
	if (intval($current_format['limit_speed_option'])!=intval($current_format['limit_speed_standard_option']) || number_format($current_format['limit_speed_value'],1)!=number_format($current_format['limit_speed_standard_value'],1))
	{
		if (!session_id()) {session_start();}
		if (intval($_SESSION['user_id'])>0 && intval($_SESSION['status_id'])!=3)
		{
			$limit_option=intval($current_format['limit_speed_standard_option']);
			$limit_value=floatval($current_format['limit_speed_standard_value']);
		}
	}
	if (intval($current_format['limit_speed_option'])!=intval($current_format['limit_speed_premium_option']) || number_format($current_format['limit_speed_value'],1)!=number_format($current_format['limit_speed_premium_value'],1))
	{
		if (!session_id()) {session_start();}
		if (intval($_SESSION['user_id'])>0 && intval($_SESSION['status_id'])==3)
		{
			$limit_option=intval($current_format['limit_speed_premium_option']);
			$limit_value=floatval($current_format['limit_speed_premium_value']);
		}
	}
	if (intval($current_format['limit_speed_option'])!=intval($current_format['limit_speed_embed_option']) || number_format($current_format['limit_speed_value'],1)!=number_format($current_format['limit_speed_embed_value'],1))
	{
		if ($_REQUEST['embed']=='true')
		{
			$limit_option=intval($current_format['limit_speed_embed_option']);
			$limit_value=floatval($current_format['limit_speed_embed_value']);
		}
	}
	if ($limit_option==1)
	{
		$limit=intval($limit_value);
	} elseif ($limit_option==2 && intval($_REQUEST['br'])>0)
	{
		$limit=intval($limit_value*intval($_REQUEST['br']));
	}
}

if ($current_format['limit_speed_countries']!='')
{
	$countries=explode(',',$current_format['limit_speed_countries']);
	if (count($countries)>0)
	{
		$is_country_in_limit=false;
		foreach ($countries as $country)
		{
			if (strtolower(trim($country))==strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
			{
				$is_country_in_limit=true;
				break;
			}
		}
		if (!$is_country_in_limit)
		{
			$limit=0;
		}
	}
}

if ($disable_security_check)
{
	$limit=0;
}

if ($streaming_type_id==0)
{
	$limit=intval($limit/8*1000);
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
		if ($is_download=='true')
		{
			$download_str_append.='&download=true';
		}
		if ($download_filename<>'')
		{
			$download_str_append.="&download_filename=$download_filename";
		}
		$ref_host=str_replace("www.","",$_SERVER['HTTP_HOST']);

		$secret_remote_key = $config['cv'];
		if ($config['cvr'])
		{
			$secret_remote_key = $config['cvr'];
		}
		if ($control_script_lock_ip==1)
		{
			$file_info=array(
				'time'=>$time,
				'limit'=>$limit,
				'file'=>$file,
				'cv'=>md5($time.$limit.$file.$_SERVER['REMOTE_ADDR'].$secret_remote_key),
			);
			header("Location: $control_script?file=B64".rawurlencode(base64_encode(serialize($file_info))).$start_str_append.$download_str_append);
		} else
		{
			header("Location: $control_script?time=".$time."&cv=".md5($time.$secret_remote_key)."&lr=".$limit."&cv2=".md5($time.$limit.$secret_remote_key)."&file=".rawurlencode($file).$start_str_append.$download_str_append."&cv3=".md5($ref_host.$secret_remote_key)."&cv4=".md5($file.$secret_remote_key));
		}
	} else {
		$target_file=substr($target_file,strpos($target_file,'/',8));
		$short_file_name=basename($target_file);
		if ($download_filename<>'')
		{
			$short_file_name=$download_filename;
		}
		if (strpos($postfix,".flv")!==false)
		{
			header("Content-Type: video/x-flv");
		} elseif (strpos($postfix,".mp4")!==false)
		{
			header("Content-Type: video/mp4");
		} elseif (strpos($postfix,".webm")!==false)
		{
			header("Content-Type: video/webm");
		} elseif (strpos($postfix,".gif")!==false)
		{
			header("Content-Type: image/gif");
		} else {
			header("Content-Type: application/octet-stream");
		}
		if (intval($limit)>0)
		{
			header("X-Accel-Limit-Rate: $limit");
		}
		if ($is_download=='true')
		{
			header("Content-Disposition: attachment; filename=\"$short_file_name\"");
		} else {
			header("Content-Disposition: inline; filename=\"$short_file_name\"");
		}
		header("X-Accel-Redirect: $target_file{$start_str}");
	}
} elseif ($streaming_type_id==4)
{
	if (is_file("$config[project_path]/admin/cdn/$streaming_api_script"))
	{
		require_once "$config[project_path]/admin/cdn/$streaming_api_script";
		$get_video_function="{$streaming_api_name}_get_video";
		if (function_exists($get_video_function))
		{
			$target_url=$target_file;
			$target_file=substr($target_file,strpos($target_file,'/',8));
			$video_url=$get_video_function($target_file,$target_url,$start_str_empty,$limit,$streaming_key);
			if ($video_url)
			{
				header("Location: $video_url");die;
			} else {
				header("Location: $target_url{$start_str}");die;
			}
		}
	}
	header("Location: $target_file{$start_str}");
} else {
	header("Location: $target_file{$start_str}");
}

function debug_get_file($message)
{
	global $config;

	if ($config['enable_debug_get_file']=='true')
	{
		$fp=fopen("$config[project_path]/admin/logs/get_file.txt","a+");
		flock($fp,LOCK_EX);
		if ($message=='')
		{
			fwrite($fp,"\n");
		} else {
			fwrite($fp,date("[Y-m-d H:i:s] ").$message."\n");
		}
		fclose($fp);
	}
}
