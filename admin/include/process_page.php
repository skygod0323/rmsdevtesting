<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
$_REQUEST=array_merge($_GET,$_POST);

$included_pages=get_included_files();
foreach ($included_pages as $page)
{
	if (strpos($page,"process_page.php")!==false)
	{
		$include_path=str_replace("include/process_page.php","",str_replace("include\process_page.php","",$page));
		break;
	}
}
require_once("$include_path/include/setup.php");

if (!is_file("$config[project_path]/admin/include/setup.php"))
{
	die("[FATAL]: project_path directory is not specified correctly in /admin/include/setup.php");
}



require_once("$config[project_path]/admin/include/functions_base.php");

function logRequest(array $req) {
	global $config;

	$json = json_encode($req);
	$file = "{$config[project_path]}/admin/data/billings/ccbill.log";
	file_put_contents($file, $json . "\n", FILE_APPEND);
}

logRequest($_REQUEST);


//$la = sys_getloadavg();



$la = floatval($la[0]);



if ($la > $config['overload_max_la_pages'])
{
	write_overload_stats(1);

	header("HTTP/1.0 503 Service Unavailable");
	if (is_file("$config[project_path]/overload.html"))
	{
		die(file_get_contents("$config[project_path]/overload.html"));
	}

	die('Sorry, the website is temporary unavailable. Please come back later!');
}



if ($_REQUEST['action'] == 'js_stats')
{
	$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
	if (intval($stats_params['collect_traffic_stats']) == 1)
	{
		write_stats(1);

		$stats_referer_host = '';
		if ($_SERVER['HTTP_REFERER'] != '')
		{
			$stats_referer_host = trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST));
			if ($stats_referer_host)
			{
				$stats_referer_host = str_replace('www.', '', $stats_referer_host);
			}
		}
		if ($stats_referer_host == '' || $stats_referer_host == str_replace('www.', '', $_SERVER['HTTP_HOST']))
		{
			$device_type = 0;
			if (intval($stats_params['collect_traffic_stats_devices']) == 1)
			{
				$device_type = get_device_type();
			}

			if (intval($_REQUEST['video_id']) > 0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/videos_id.dat", intval($_REQUEST['video_id']) . "||0||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||" . date("Y-m-d H:i:s") . "||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
			if (intval($_REQUEST['album_id']) > 0)
			{
				file_put_contents("$config[project_path]/admin/data/stats/albums_id.dat", intval($_REQUEST['album_id']) . "||0||1||$_SERVER[GEOIP_COUNTRY_CODE]||$_COOKIE[kt_referer]||$_COOKIE[kt_qparams]||" . date("Y-m-d H:i:s") . "||$_SERVER[REMOTE_ADDR]||$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}
	}

	header("Content-type: image/gif");
	die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
}



$plugin_extensions = [];
$plugin_extensions_files = get_contents_from_dir("$config[project_path]/admin/data/engine/site_plugins", 1);
foreach ($plugin_extensions_files as $plugin_extension_file)
{
	if (substr($plugin_extension_file, -4) == '.dat')
	{
		$plugin_extension = substr($plugin_extension_file, 0, -4);
		if (is_file("$config[project_path]/admin/plugins/$plugin_extension/$plugin_extension.php"))
		{
			require_once "$config[project_path]/admin/plugins/$plugin_extension/$plugin_extension.php";
			$plugin_extensions[] = $plugin_extension;
		}
	}
}

if ($_GET[session_name()])
{
	session_id($_GET[session_name()]);
}
session_start();
if (!$page_id)
{
	$page_id = preg_replace("|\..{1,4}$|is", "", end(explode("/", $_SERVER['SCRIPT_FILENAME'])));
}



if ($_REQUEST['mode']=='async')
{
	foreach ($plugin_extensions as $plugin_extension)
	{
		$plugin_function = "{$plugin_extension}PreAsyncRequest";
		if (function_exists($plugin_function))
		{
			$plugin_function();
		}
	}

	if ($_REQUEST['action']=='show_security_code' || $_REQUEST['function']=='show_security_code')
	{
		mt_srand(time());
		$t1=mt_rand(1,9);
		$t2=mt_rand(0,9);
		$t3=mt_rand(0,9);
		$t4=mt_rand(0,9);
		$t5=mt_rand(0,9);
		$text=$t1.$t2.$t3.$t4.$t5;
		$_SESSION['security_code']=$text;
		if ($_REQUEST['captcha_id']!='')
		{
			$_SESSION['security_code_'.$_REQUEST['captcha_id']]=$text;
		}

		$a1=mt_rand(-15,15);$b1=mt_rand(0,255);
		$a2=mt_rand(-15,15);$b2=mt_rand(0,255);
		$a3=mt_rand(-15,15);$b3=mt_rand(0,255);
		$a4=mt_rand(-15,15);$b4=mt_rand(0,255);
		$a5=mt_rand(-15,15);$b5=mt_rand(0,255);

		$font  = "$config[project_path]/admin/data/system/verdanaz.ttf";
		$fname = "$config[project_path]/admin/data/system/security_code.jpg";
		$im = imagecreatefromjpeg($fname);

		$white = imagecolorallocate($im, $b1, $b1, $b1);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a1, 33, 51, $black, $font, $t1);
		imagettftext($im, 33, $a1, 32, 50, $white, $font, $t1);

		$white = imagecolorallocate($im, $b2, $b2, $b2);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a2, 53, 51, $black, $font, $t2);
		imagettftext($im, 33, $a2, 52, 50, $white, $font, $t2);

		$white = imagecolorallocate($im, $b3, $b3, $b3);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a3, 73, 51, $black, $font, $t3);
		imagettftext($im, 33, $a3, 72, 50, $white, $font, $t3);

		$white = imagecolorallocate($im, $b4, $b4, $b4);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a4, 93, 51, $black, $font, $t4);
		imagettftext($im, 33, $a4, 92, 50, $white, $font, $t4);

		$white = imagecolorallocate($im, $b5, $b5, $b5);
		$black = imagecolorallocate($im, 0, 0, 0);
		imagettftext($im, 33, $a5, 113, 51, $black, $font, $t5);
		imagettftext($im, 33, $a5, 112, 50, $white, $font, $t5);

		$gif_file=mt_rand(0,9999999999);
		header("Content-Type: image/gif");
		imagegif($im,"$config[temporary_path]/$gif_file.gif");
		$gif_size=filesize("$config[temporary_path]/$gif_file.gif");
		if ($gif_size>0)
		{
			header("Content-Length: $gif_size");
			unlink("$config[temporary_path]/$gif_file.gif");
		}
		imagegif($im);
		imagedestroy($im);
		die;
	} elseif ($_REQUEST['action']=='check_security_code' || $_REQUEST['function']=='check_security_code')
	{
		async_set_request_content_type();
		if (strlen($_REQUEST['code'])>0 && $_REQUEST['code']==$_SESSION['security_code'])
		{
			if ($_REQUEST['format']=='json') {echo 'true';} else {echo "<success/>";}
		} else {
			if ($_REQUEST['format']=='json') {echo 'false';} else {echo "<error type=\"invalid_code\"/>";}
		}
		die;
	} elseif ($_REQUEST['action']=='js_online_status' || $_REQUEST['function']=='js_online_status')
	{
		if ($_SESSION['user_id']>0)
		{
			$website_ui_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
			if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
			{
				if (time()-$_SESSION['last_time_user_online_status_refreshed']>($website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL']-1)*60)
				{
					sql_pr("update $config[tables_prefix]users set last_online_date=? where user_id=?",date("Y-m-d H:i:s"),$_SESSION['user_id']);
					$_SESSION['last_time_user_online_status_refreshed']=time();
				}
			}
		}
		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	} elseif ($_REQUEST['action']=='js_user_status' || $_REQUEST['function']=='js_user_status')
	{
		async_set_request_content_type();
		if ($_SESSION['user_id']>0)
		{
			if ($_REQUEST['format']=='json')
			{
				echo json_encode(array('id'=>$_SESSION['user_id'],'status'=>$_SESSION['status_id'],'display_name'=>$_SESSION['display_name']));
			} else {
				$display_name=$_SESSION['display_name'];
				$display_name=str_replace("&","&amp;",$display_name);
				$display_name=str_replace(">","&gt;",$display_name);
				$display_name=str_replace("<","&lt;",$display_name);
				echo "<member id=\"$_SESSION[user_id]\" status=\"$_SESSION[status_id]\" display_name=\"$display_name\"/>";
			}
		} else {
			if ($_REQUEST['format']=='json')
			{
				echo json_encode(array('id'=>0));
			} else {
				echo "<guest/>";
			}
		}
		die;
	} elseif (($_REQUEST['action']=='get_block' || $_REQUEST['function']=='get_block') && isset($_REQUEST['block_id']))
	{
		include_once("$config[project_path]/admin/include/pre_initialize_page_code.php");

		$website_ui_data=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
		$stats_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
		$runtime_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
		$request_uri=clean_request_uri();

		if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
		{
			$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
		}

		if ($_SESSION['user_id']>0)
		{
			// sync user status
			$result=mr2array_single(sql_pr("select status_id, tokens_available, is_trusted from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
			$_SESSION['status_id']=intval($result['status_id']);
			$_SESSION['tokens_available']=intval($result['tokens_available']);
			$_SESSION['is_trusted']=intval($result['is_trusted']);
			$_SESSION['content_purchased']=mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?",$_SESSION['user_id'],date("Y-m-d H:i:s")));
			$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);

			if (intval($_SESSION['status_id'])==0)
			{
				require_once("$config[project_path]/logout.php");
			}
		}

		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_REQUEST['global']=='true')
		{
			if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
			{
				$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
				$page_config['blocks_list']=explode("|AND|",trim($temp[2]));

				foreach ($page_config['blocks_list'] as $block)
				{
					$block_id=substr($block,0,strpos($block,"[SEP]"));
					$block_name=substr($block,strpos($block,"[SEP]")+5);
					$block=str_replace("[SEP]","_",$block);

					if ($_REQUEST['block_id']!=$block) {continue;}
					if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {die;}
					if (!is_file("$config[project_path]/admin/data/config/\$global/$block.dat")) {die;}

					require_once("$config[project_path]/admin/include/setup_smarty_site.php");
					include_once("$config[project_path]/admin/include/list_countries.php");

					$args=array('global_id'=>$block);
					$block_content=insert_getGlobal($args);
					echo replace_runtime_params($block_content);

					foreach ($plugin_extensions as $plugin_extension)
					{
						$plugin_function = "{$plugin_extension}PostAsyncRequest";
						if (function_exists($plugin_function))
						{
							$plugin_function();
						}
					}
					die;
				}
			}
		} else {
			if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
			{
				$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
				check_page_access($temp);
				$page_config['blocks_list']=explode("|AND|",trim($temp[2]));

				foreach ($page_config['blocks_list'] as $block)
				{
					$block_id=substr($block,0,strpos($block,"[SEP]"));
					$block_name=substr($block,strpos($block,"[SEP]")+5);
					$block=str_replace("[SEP]","_",$block);

					include_once("$config[project_path]/blocks/$block_id/$block_id.php");
					$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
					$config_params=array();
					if (trim($temp[1])<>'')
					{
						$temp_params=explode("&",$temp[1]);
						foreach ($temp_params as $temp_param)
						{
							$temp_param=explode("=",$temp_param,2);
							$config_params[trim($temp_param[0])]=trim($temp_param[1]);
						}
					}
					$hash_function="{$block_id}GetHash";
					$block_hash=$hash_function($config_params);

					if (trim($temp[4])!='')
					{
						$block_dynamic_params=explode(',',$temp[4]);
						foreach ($block_dynamic_params as $block_dynamic_param)
						{
							if (trim($block_dynamic_param)!='' && trim($_REQUEST[trim($block_dynamic_param)])!='')
							{
								$block_hash='dyn:'.trim($_REQUEST[trim($block_dynamic_param)]).'|'.$block_hash;
							}
						}
					}

					$block_hash="$config[project_url]|$page_id|$block|$block_hash";
					if ($config['cache_control_user_status_in_cache']=='true')
					{
						$block_hash=intval($_SESSION['status_id'])."|$block_hash";
					}
					if ($config['project_url_scheme']=="https")
					{
						$block_hash="https|$block_hash";
					}
					if ($config['device']<>"")
					{
						$block_hash="$config[device]|$block_hash";
					}
					if ($config['locale']<>'')
					{
						$block_hash="$config[locale]|$block_hash";
					}
					if ($config['relative_post_dates']=="true")
					{
						$relative_post_date=0;
						if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
						{
							$registration_date=strtotime($_SESSION['added_date']);
							$relative_post_date=floor((time()-$registration_date)/86400)+1;
						}
						$block_hash="$relative_post_date|$block_hash";
					}
					$block_hash=md5($block_hash);

					if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block}_$block_hash.dat"))
					{
						$storage[$block]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$block}_$block_hash.dat"));
					}

					if ($_REQUEST['block_id']!=$block) {continue;}
					if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {die;}
					if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {die;}

					$storage[$block]=array();
					require_once("$config[project_path]/admin/include/setup_smarty_site.php");
					include_once("$config[project_path]/admin/include/list_countries.php");

					$args=array('block_id'=>$block_id,'block_name'=>$block_name);
					$block_content=insert_getBlock($args);
					echo replace_runtime_params($block_content);

					foreach ($plugin_extensions as $plugin_extension)
					{
						$plugin_function = "{$plugin_extension}PostAsyncRequest";
						if (function_exists($plugin_function))
						{
							$plugin_function();
						}
					}
					die;
				}
			}
		}
		die;
	} elseif ($_REQUEST['action']=='add_to_friends' || $_REQUEST['function']=='add_to_friends')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_REQUEST['user_id']);
			if ($user_id>0 && $user_id<>$_SESSION['user_id'])
			{
				require_once("$config[project_path]/admin/include/functions.php");

				$is_friend=mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where (user_id=? and friend_id=?) or (friend_id=? and user_id=?)",$_SESSION['user_id'],$user_id,$_SESSION['user_id'],$user_id));
				if ($is_friend==0)
				{
					$message=strip_tags($_REQUEST['message']);

					$tokens_required=0;
					$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
					if ($memberzone_data['ENABLE_TOKENS_INTERNAL_MESSAGES']==1)
					{
						$tokens_required=intval($memberzone_data['TOKENS_INTERNAL_MESSAGES']);
					}

					if ($tokens_required>0 && $tokens_required>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
					{
						async_return_request_status(array(array('error_field_name'=>'message','error_code'=>'not_enough_tokens')));
					}

					$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
					if (strpos($antispam_action, 'error') !== false)
					{
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
						async_return_request_status(array(array('error_code'=>'spam','block'=>'member_profile_view')));
					}

					sql_pr("insert into $config[tables_prefix]friends set user_id=?, friend_id=?, added_date=?",$_SESSION['user_id'],$user_id,date("Y-m-d H:i:s"));
					sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id);
					$message_id=sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=1, message=?, message_md5=md5(message), ip=?, added_date=?",$user_id,$_SESSION['user_id'],$message,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));

					if ($tokens_required>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens_required, 0) where user_id=?",$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
					}

					if (strpos($antispam_action, 'delete') !== false)
					{
						sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where message_id=?", $message_id);
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
					}
					async_return_request_status(null,null,array('message_id'=>$message_id));
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='donate' || $_REQUEST['function']=='donate')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
			if ($memberzone_data['ENABLE_TOKENS_DONATIONS']==1)
			{
				$user_id=intval($_REQUEST['user_id']);
				if ($user_id>0 && $user_id<>$_SESSION['user_id'])
				{
					require_once("$config[project_path]/admin/include/functions.php");

					$tokens_donated=intval($_REQUEST['tokens']);
					$tokens_required=intval($memberzone_data['TOKENS_DONATION_MIN']);

					if ($tokens_donated==0)
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'required')));
					} elseif ($tokens_donated<$tokens_required)
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'tokens_minimum','error_details'=>array($tokens_required))));
					} elseif ($tokens_donated>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
					{
						async_return_request_status(array(array('error_field_name'=>'tokens','error_code'=>'not_enough_tokens')));
					}

					$assign_tokens=$tokens_donated-ceil($tokens_donated*min(100,intval($memberzone_data['TOKENS_DONATION_INTEREST']))/100);
					$tokens_revenue=$tokens_donated-$assign_tokens;

					$donation_id=sql_insert("insert into $config[tables_prefix]log_donations_users set donator_id=?, user_id=?, tokens=?, tokens_revenue=?, comment=?, added_date=?",$_SESSION['user_id'],$user_id,$tokens_donated,$tokens_revenue,trim($_REQUEST['comment']),date("Y-m-d H:i:s"));

					if ($assign_tokens>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$assign_tokens,$user_id);
						sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=10, user_id=?, ref_id=?, donation_id=?, tokens_granted=?, added_date=?",$user_id,$_SESSION['user_id'],$donation_id,$assign_tokens,date("Y-m-d H:i:s"));
					}

					sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens_donated, 0) where user_id=?",$_SESSION['user_id']);
					$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));

					async_return_request_status();
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='subscribe' || $_REQUEST['function']=='subscribe')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$s_object_id=$s_type_id=0;
			$s_table_name=$s_table_key=$s_cache_info=$s_cache_key='';
			if (intval($_REQUEST['subscribe_user_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_user_id']); $s_type_id=1;
				$s_table_name="$config[tables_prefix]users"; $s_table_key="user_id";
				if ($s_object_id==$_SESSION['user_id'])
				{
					async_return_request_status(array(array('error_code'=>'invalid_params')));
				}
			} elseif (intval($_REQUEST['subscribe_cs_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_cs_id']); $s_type_id=3;
				$s_table_name="$config[tables_prefix]content_sources"; $s_table_key="content_source_id";
				$s_cache_info='cs_info'; $s_cache_key='cs';
			} elseif (intval($_REQUEST['subscribe_model_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_model_id']); $s_type_id=4;
				$s_table_name="$config[tables_prefix]models"; $s_table_key="model_id";
				$s_cache_info='models_info'; $s_cache_key='model';
			} elseif (intval($_REQUEST['subscribe_dvd_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_dvd_id']); $s_type_id=5;
				$s_table_name="$config[tables_prefix]dvds"; $s_table_key="dvd_id";
				$s_cache_info='dvds_info'; $s_cache_key='dvd';
			} elseif (intval($_REQUEST['subscribe_category_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_category_id']); $s_type_id=6;
				$s_table_name="$config[tables_prefix]categories"; $s_table_key="category_id";
			} elseif (intval($_REQUEST['subscribe_playlist_id'])>0)
			{
				$s_object_id=intval($_REQUEST['subscribe_playlist_id']); $s_type_id=13;
				$s_table_name="$config[tables_prefix]playlists"; $s_table_key="playlist_id";
				$s_cache_info='playlists_info'; $s_cache_key='playlist';
				$database_selectors['generic_selector_dir']='dir';
			}

			if ($s_object_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id))==0)
				{
					$s_object=mr2array_single(sql_pr("select * from $s_table_name where $s_table_key=?",$s_object_id));
					if (intval($s_object[$s_table_key])==0)
					{
						async_return_request_status(array(array('error_code'=>'invalid_params')));
					}

					$s_purchase_id=0;
					if ($s_type_id==1 || $s_type_id==5)
					{
						$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
						$s_tokens=0;
						$s_expiry_period=0;
						$s_token_sale=0;
						if ($s_type_id==1)
						{
							$s_purchase_table_key='profile_id';
							$s_award_type_id=13;
							$s_expiry_period=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']);
							$s_token_sale=intval($memberzone_data['ENABLE_TOKENS_SALE_MEMBERS']);
							if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_MEMBERS'])==1)
							{
								$s_tokens=intval($s_object['tokens_required']);
								if ($s_tokens==0)
								{
									$s_tokens=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
								}
							}
						} elseif ($s_type_id==5)
						{
							$s_purchase_table_key='dvd_id';
							$s_award_type_id=14;
							$s_expiry_period=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']);
							$s_token_sale=intval($memberzone_data['ENABLE_TOKENS_SALE_DVDS']);
							if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_DVDS'])==1)
							{
								$s_tokens=intval($s_object['tokens_required']);
								if ($s_tokens==0)
								{
									$s_tokens=intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
								}
							}
						}

						if ($s_tokens>0)
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_purchases where user_id=? and $s_purchase_table_key=? and expiry_date>?",$_SESSION['user_id'],$s_object_id,date("Y-m-d H:i:s")))==0)
							{
								if ($s_tokens>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
								{
									async_return_request_status(array(array('error_code'=>'subscription_not_enough_tokens')));
								}

								$s_added_date=date("Y-m-d H:i:s");
								$s_expiry_date="2070-01-01 00:00:00";
								if ($s_expiry_period>0)
								{
									$s_expiry_date=date("Y-m-d H:i:s",time()+$s_expiry_period*86400);
								}

								$s_assign_tokens=0;
								if ($s_token_sale==1 && $s_object['user_id']>0)
								{
									$s_assign_tokens=$s_tokens-ceil($s_tokens*min(100,intval($memberzone_data['TOKENS_SALE_INTEREST']))/100);

									$s_exclude_users=array_map('trim',explode(",",$memberzone_data['TOKENS_SALE_EXCLUDES']));
									$s_username=mr2string(sql_pr("select username from $config[tables_prefix]users where user_id=?",$s_object['user_id']));
									if ($s_username && in_array($s_username,$s_exclude_users))
									{
										$s_assign_tokens=0;
									}

									if ($s_assign_tokens>0)
									{
										sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",$s_assign_tokens,$s_object['user_id']);
										sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=?, user_id=?, $s_purchase_table_key=?, tokens_granted=?, added_date=?",$s_award_type_id,$s_object['user_id'],$s_object_id,$s_assign_tokens,date("Y-m-d H:i:s"));
									}
								}
								$s_tokens_revenue=$s_tokens-$s_assign_tokens;

								$s_purchase_id=sql_insert("insert into $config[tables_prefix]users_purchases set is_recurring=1, $s_purchase_table_key=?, user_id=?, owner_user_id=?, tokens=?, tokens_revenue=?, added_date=?, expiry_date=?",$s_object_id,$_SESSION['user_id'],$s_object['user_id'],$s_tokens,$s_tokens_revenue,$s_added_date,$s_expiry_date);

								sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$s_tokens, 0) where user_id=?",$_SESSION['user_id']);

								$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
								$_SESSION['content_purchased'][]=array($s_purchase_table_key=>$s_object_id);
								$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);
							}
						}
					}

					$s_subscription_id=sql_insert("insert into $config[tables_prefix]users_subscriptions set user_id=?, subscribed_object_id=?, subscribed_type_id=?, added_date=?",$_SESSION['user_id'],$s_object_id,$s_type_id,date("Y-m-d H:i:s"));
					if ($s_purchase_id>0)
					{
						sql_pr("update $config[tables_prefix]users_purchases set subscription_id=? where purchase_id=?",$s_subscription_id,$s_purchase_id);
					}
					sql_pr("update $s_table_name set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$s_table_name.$s_table_key and subscribed_type_id=$s_type_id) where $s_table_key=?",$s_object_id);

					if ($s_cache_info!='')
					{
						$obj_info=mr2array_single(sql_pr("select $s_table_key, $database_selectors[generic_selector_dir] as dir from $s_table_name where $s_table_key=?",$s_object_id));
						if ($obj_info[$s_table_key]>0)
						{
							inc_block_version($s_cache_info,$s_cache_key,$obj_info[$s_table_key],$obj_info['dir'],$_SESSION['user_id']);
						}
					}
					$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));
				}
				async_return_request_status();
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='unsubscribe' || $_REQUEST['function']=='unsubscribe')
	{
		configure_locale();
		if (is_file("$config[project_path]/langs/default.php"))
		{
			include_once("$config[project_path]/langs/default.php");
		}
		if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
		{
			include_once("$config[project_path]/langs/$config[locale].php");
		}
		include_once("$config[project_path]/admin/include/pre_async_action_code.php");

		if ($_SESSION['user_id']>0)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$s_object_id=$s_type_id=0;
			$s_table_name=$s_table_key=$s_cache_info=$s_cache_key='';
			if (intval($_REQUEST['unsubscribe_user_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_user_id']); $s_type_id=1;
				$s_table_name="$config[tables_prefix]users"; $s_table_key="user_id";
			} elseif (intval($_REQUEST['unsubscribe_cs_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_cs_id']); $s_type_id=3;
				$s_table_name="$config[tables_prefix]content_sources"; $s_table_key="content_source_id";
				$s_cache_info='cs_info'; $s_cache_key='cs';
			} elseif (intval($_REQUEST['unsubscribe_model_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_model_id']); $s_type_id=4;
				$s_table_name="$config[tables_prefix]models"; $s_table_key="model_id";
				$s_cache_info='models_info'; $s_cache_key='model';
			} elseif (intval($_REQUEST['unsubscribe_dvd_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_dvd_id']); $s_type_id=5;
				$s_table_name="$config[tables_prefix]dvds"; $s_table_key="dvd_id";
				$s_cache_info='dvds_info'; $s_cache_key='dvd';
			} elseif (intval($_REQUEST['unsubscribe_category_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_category_id']); $s_type_id=6;
				$s_table_name="$config[tables_prefix]categories"; $s_table_key="category_id";
			} elseif (intval($_REQUEST['unsubscribe_playlist_id'])>0)
			{
				$s_object_id=intval($_REQUEST['unsubscribe_playlist_id']); $s_type_id=13;
				$s_table_name="$config[tables_prefix]playlists"; $s_table_key="playlist_id";
				$s_cache_info='playlists_info'; $s_cache_key='playlist';
				$database_selectors['generic_selector_dir']='dir';
			}

			if ($s_object_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id))>0)
				{
					sql_pr("delete from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=?",$_SESSION['user_id'],$s_object_id,$s_type_id);
					sql_pr("update $s_table_name set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$s_table_name.$s_table_key and subscribed_type_id=$s_type_id) where $s_table_key=?",$s_object_id);

					if ($s_cache_info!='')
					{
						$obj_info=mr2array_single(sql_pr("select $s_table_key, $database_selectors[generic_selector_dir] as dir from $s_table_name where $s_table_key=?",$s_object_id));
						if ($obj_info[$s_table_key]>0)
						{
							inc_block_version($s_cache_info,$s_cache_key,$obj_info[$s_table_key],$obj_info['dir'],$_SESSION['user_id']);
						}
					}
					$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));
				}
				async_return_request_status();
			}
			async_return_request_status(array(array('error_code'=>'invalid_params')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		}
	} elseif ($_REQUEST['action']=='rotator_videos' || $_REQUEST['function']=='rotator_videos')
	{
		$pqr=trim($_REQUEST['pqr']);
		if ($_SESSION['userdata']['user_id']>0)
		{
			header("Content-type: image/gif");
			die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
		}

		if (!is_dir("$config[project_path]/admin/data/engine/rotator")) {mkdir("$config[project_path]/admin/data/engine/rotator",0777);chmod("$config[project_path]/admin/data/engine/rotator",0777);}
		if (!is_dir("$config[project_path]/admin/data/engine/rotator/videos")) {mkdir("$config[project_path]/admin/data/engine/rotator/videos",0777);chmod("$config[project_path]/admin/data/engine/rotator/videos",0777);}
		$fh=fopen("$config[project_path]/admin/data/engine/rotator/videos/clicks.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,"x:$pqr\r\n");
		fclose($fh);

		header("Content-type: image/gif");
		die(base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw=='));
	}

	configure_locale();
	if (is_file("$config[project_path]/langs/default.php"))
	{
		include_once("$config[project_path]/langs/default.php");
	}
	if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
	{
		include_once("$config[project_path]/langs/$config[locale].php");
	}
	include_once("$config[project_path]/admin/include/pre_async_action_code.php");

	if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
	{
		require_once("$config[project_path]/admin/include/database_selectors.php");
		$website_ui_data=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
		$stats_params=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));

		$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
		check_page_access($temp);
		$page_config['blocks_list']=explode("|AND|",trim($temp[2]));

		foreach ($page_config['blocks_list'] as $block)
		{
			$block_id=substr($block,0,strpos($block,"[SEP]"));
			$block=str_replace("[SEP]","_",$block);

			if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {continue;}
			if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {continue;}

			include_once("$config[project_path]/blocks/$block_id/$block_id.php");
			$async_function="{$block_id}Async";
			if (!function_exists($async_function)) {continue;}

			$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
			$config_params=array();
			if (trim($temp[1])<>'')
			{
				$temp_params=explode("&",$temp[1]);
				foreach ($temp_params as $temp_param)
				{
					$temp_param=explode("=",$temp_param,2);
					$config_params[trim($temp_param[0])]=trim($temp_param[1]);
				}
			}
			$async_function($config_params);
		}
	}
	async_return_request_status(array(array('error_code'=>'invalid_params')));
} elseif (($_REQUEST['action']=='redirect_adv' || $_REQUEST['action']=='trace') && $_REQUEST['id']>0)
{
	require_once "$config[project_path]/admin/include/functions_admin.php";

	$id=intval($_REQUEST['id']);

	$ad_info = null;
	foreach (get_site_spots() as $spot)
	{
		if (isset($spot['ads'][$id]))
		{
			$ad_info=$spot['ads'][$id];
			break;
		}
	}

	if (isset($ad_info))
	{
		if ($_COOKIE['kt_tcookie']=='1')
		{
			$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
			if (intval($stats_params['collect_traffic_stats']) == 1)
			{
				$device_type = 0;
				if (intval($stats_params['collect_traffic_stats_devices']) == 1)
				{
					$device_type = get_device_type();
				}

				file_put_contents("$config[project_path]/admin/data/stats/adv_out.dat", date("Y-m-d") . "|$id|$_SERVER[GEOIP_COUNTRY_CODE]|$_COOKIE[kt_referer]|$_COOKIE[kt_qparams]|$device_type\r\n", LOCK_EX | FILE_APPEND);
			}
		}

		$url=$ad_info['url'];

		if (is_file("$config[project_path]/admin/data/system/runtime_params.dat") && filesize("$config[project_path]/admin/data/system/runtime_params.dat")>0)
		{
			$runtime_params=unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
			foreach ($runtime_params as $param)
			{
				$var=trim($param['name']);
				$val=$_SESSION['runtime_params'][$var];
				if (strlen($val)==0)
				{
					$val=trim($param['default_value']);
				}
				if ($var<>'')
				{
					$url=str_replace("%$var%",$val,$url);
				}
			}
		}

		header("Location: $url");
	}
	die;
}

$start_time=microtime(true);
$start_memory=memory_get_peak_usage();
$performance_log_summary='';

if ($_SESSION['user_id']<1 && $_COOKIE['kt_member']<>'')
{
	require_once("$config[project_path]/admin/include/functions.php");
	require_once("$config[project_path]/admin/include/database_selectors.php");

	$result=sql_pr("select * from $config[tables_prefix]users where status_id not in (0,1,4) and remember_me_key=? and remember_me_valid_for>=?",$_COOKIE['kt_member'],date("Y-m-d H:i:s"));
	if (mr2rows($result)==0)
	{
		$domain=str_replace("www.","",$_SERVER['HTTP_HOST']);
		setcookie("kt_member",'',time()-86400,"/",".$domain");
	} else {
		$user_data=mr2array_single($result);
		login_user($user_data,0);
	}
}

if (is_file("$config[project_path]/admin/data/config/$page_id/config.dat"))
{
	$page_config=array();
	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/config.dat"));
	check_page_access($temp);
	$page_config['cache_time']=intval($temp[0]);
	$page_config['is_compressed']=intval($temp[1]);
	$page_config['blocks_list']=explode("|AND|",trim($temp[2]));
	$page_config['is_xml']=intval($temp[3]);
	$page_config['dynamic_http_params']=trim($temp[7]);
	$page_is_xml=$page_config['is_xml'];
} else {
	echo "Page is not defined within engine"; die;
}

$website_ui_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
if ($website_ui_data['DISABLE_WEBSITE'] == 1)
{
	if ($_SESSION['userdata']['user_id'] < 1)
	{
		header('HTTP/1.0 404 Not Found');
		if (is_file("$config[project_path]/website_disabled.html"))
		{
			die(file_get_contents("$config[project_path]/website_disabled.html"));
		}

		die("The requested URL was not found on this server.");
	}
}

$stats_params = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
if (intval($stats_params['collect_traffic_stats']) == 0)
{
	$config['disable_stats'] = 'true';
}

include_once("$config[project_path]/admin/include/pre_initialize_page_code.php");

configure_locale();

if ($_REQUEST['debug']=='true' && $_SESSION['userdata']['user_id']>0)
{
	require_once("$config[project_path]/admin/website_ui_debug.php");
	die;
}

foreach ($plugin_extensions as $plugin_extension)
{
	$plugin_function = "{$plugin_extension}PreSiteRequest";
	if (function_exists($plugin_function))
	{
		$plugin_function();
	}
}

if ($page_is_xml==1)
{
	header("Content-type: text/xml; charset=utf-8");
}

if ($_SESSION['user_id']>0)
{
	if ($website_ui_data['ENABLE_USER_MESSAGES_REFRESH']==1)
	{
		// sync new user messages
		if (time()-$_SESSION['last_time_get_new_message_amount']>($website_ui_data['USER_MESSAGES_REFRESH_INTERVAL']-1)*60)
		{
			require_once("$config[project_path]/admin/include/functions.php");
			messages_changed();

			$_SESSION['last_time_get_new_message_amount']=time();
		}
	}

	// sync user status
	$result=mr2array_single(sql_pr("select status_id, tokens_available, is_trusted from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
	$_SESSION['status_id']=intval($result['status_id']);
	$_SESSION['tokens_available']=intval($result['tokens_available']);
	$_SESSION['is_trusted']=intval($result['is_trusted']);
	$_SESSION['content_purchased']=mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?",$_SESSION['user_id'],date("Y-m-d H:i:s")));
	$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);

	if (intval($_SESSION['status_id'])==0)
	{
		header("Location: $config[project_url]/logout.php");
		die;
	} elseif ($_SESSION['status_id']=='3')
	{
		$transaction_data=mr2array_single(sql_pr("select (UNIX_TIMESTAMP(access_end_date) - UNIX_TIMESTAMP(?)) / 3600 as hours_left, is_unlimited_access, external_guid, external_package_id from $config[tables_prefix]bill_transactions where status_id=1 and user_id=? order by access_end_date desc limit 1",date("Y-m-d H:i:s"),$_SESSION['user_id']));
		$_SESSION['paid_access_hours_left']=intval($transaction_data['hours_left']);
		$_SESSION['paid_access_is_unlimited']=intval($transaction_data['is_unlimited_access']);
		$_SESSION['external_guid']=trim($transaction_data['external_guid']);
		$_SESSION['external_package_id']=trim($transaction_data['external_package_id']);
	} else {
		unset($_SESSION['paid_access_hours_left']);
		unset($_SESSION['paid_access_is_unlimited']);
		unset($_SESSION['external_package_id']);
		unset($_SESSION['external_guid']);
	}
}

if (is_file("$config[project_path]/admin/data/system/runtime_params.dat") && filesize("$config[project_path]/admin/data/system/runtime_params.dat")>0)
{
	$domain=str_replace("www.","",$_SERVER['HTTP_HOST']);
	$runtime_params=unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
	foreach ($runtime_params as $param)
	{
		$var=trim($param['name']);
		if (isset($_GET[$var]) || isset($_POST[$var]) || isset($_COOKIE["kt_rt_$var"]))
		{
			$val=$_GET[$var];
			if ($val=='') {$val=$_POST[$var];}
			if ($val=='') {$val=$_COOKIE["kt_rt_$var"];}
			if ($var<>'' && $val<>'')
			{
				$_SESSION['runtime_params'][$var]=$val;
				if (isset($_GET[$var]) || isset($_POST[$var]))
				{
					$val_lifetime=intval($param['lifetime']);
					if ($val_lifetime==0)
					{
						$val_lifetime=360;
					}
					setcookie("kt_rt_$var",$val,time()+$val_lifetime*86400,"/",".$domain");
				}
			}
		}
	}
}

$use_memcache=1;
if ($config['memcache_server']=='' || $_SESSION['user_id']>0 || count($_POST)>0 || $_SESSION['userdata']['user_id']>0 || $website_ui_data['WEBSITE_CACHING']>=1 || !class_exists('Memcached')) {$use_memcache=0;} else
{
	$memcache = new Memcached();
	$memcache->addServer($config['memcache_server'], $config['memcache_port']) or $use_memcache=0;
}

if ($use_memcache==1)
{
	unset($page_hash);

	if ($page_config['cache_time']>0)
	{
		$is_no_cache=0;
		foreach ($page_config['blocks_list'] as $block)
		{
			$block_id=substr($block,0,strpos($block,"[SEP]"));
			$block=str_replace("[SEP]","_",$block);

			if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {continue;}
			if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {continue;}

			include_once("$config[project_path]/blocks/$block_id/$block_id.php");
			$hash_function="{$block_id}GetHash";
			if (!function_exists($hash_function)) {continue;}

			$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
			$config_params=array();
			if (trim($temp[1])<>'')
			{
				$temp_params=explode("&",$temp[1]);
				foreach ($temp_params as $temp_param)
				{
					$temp_param=explode("=",$temp_param,2);
					$config_params[trim($temp_param[0])]=trim($temp_param[1]);
				}
			}
			$config_params_array[$block]=$config_params;

			$block_hash=$hash_function($config_params);
			if (in_array($block_hash,array('nocache','runtime_nocache'))) {$is_no_cache=1;break;}

			if (trim($temp[4])!='')
			{
				$block_dynamic_params=explode(',',$temp[4]);
				foreach ($block_dynamic_params as $block_dynamic_param)
				{
					if (trim($block_dynamic_param)!='' && trim($_REQUEST[trim($block_dynamic_param)])!='')
					{
						$block_hash='dyn:'.trim($_REQUEST[trim($block_dynamic_param)]).'|'.$block_hash;
					}
				}
			}

			$page_hash.="$block_hash|";
		}

		if ($page_config['dynamic_http_params']!='')
		{
			$page_dynamic_params=explode(',',$page_config['dynamic_http_params']);
			foreach ($page_dynamic_params as $page_dynamic_param)
			{
				if (trim($page_dynamic_param)!='' && trim($_REQUEST[trim($page_dynamic_param)])!='')
				{
					$page_hash.='dyn:'.trim($_REQUEST[trim($page_dynamic_param)]).'|';
				}
			}
		}

		if ($is_no_cache<>1)
		{
			$page_hash="$config[project_url]|$page_id|".md5($page_hash);
			if ($config['project_url_scheme']=="https")
			{
				$page_hash="https|$page_hash";
			}
			if ($config['device']<>"")
			{
				$page_hash="$config[device]|$page_hash";
			}
			if ($config['locale']<>'')
			{
				$page_hash="$config[locale]|$page_hash";
			}

			$page_content = $memcache->get($page_hash);
			if ($page_content!==false)
			{
				write_stats(0);
				if ($page_config['is_xml']<>1)
				{
					include_once("$config[project_path]/admin/include/pre_process_page_code.php");
				}
				foreach ($page_config['blocks_list'] as $block)
				{
					$block_id=substr($block,0,strpos($block,"[SEP]"));
					$block=str_replace("[SEP]","_",$block);

					$pre_process_function="{$block_id}PreProcess";
					if (function_exists($pre_process_function))
					{
						$pre_process_function($config_params_array[$block],$block);
					}
				}
				if ($page_config['is_xml']<>1)
				{
					include_once("$config[project_path]/admin/include/pre_display_page_code.php");
				}
				echo replace_runtime_params($page_content);

				foreach ($plugin_extensions as $plugin_extension)
				{
					$plugin_function = "{$plugin_extension}PostSiteRequest";
					if (function_exists($plugin_function))
					{
						$plugin_function();
					}
				}

				if ($page_config['is_xml']<>1)
				{
					include_once("$config[project_path]/admin/include/post_process_page_code.php");
				}

				log_performance(microtime(true)-$start_time,memory_get_peak_usage()-$start_memory,1,null);
				die;
			}
		}
	}
}

if ($la>$config['overload_max_la_blocks'])
{
	write_overload_stats(3);
	header("HTTP/1.0 503 Service Unavailable");
	if (is_file("$config[project_path]/overload.html"))
	{
		echo file_get_contents("$config[project_path]/overload.html");die;
	}
	echo "Sorry, the website is temporary unavailable. Please come back later!";die;
}

$js_files=array();
$js_files[]="KernelTeamVideoSharingSystem.js?v={$config['project_version']}";
if ($_SESSION['user_id']>0)
{
	$js_files[]="KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

foreach ($page_config['blocks_list'] as $block)
{
	$block_id=substr($block,0,strpos($block,"[SEP]"));
	$block=str_replace("[SEP]","_",$block);

	if (!is_file("$config[project_path]/blocks/$block_id/$block_id.php")) {continue;}
	if (!is_file("$config[project_path]/admin/data/config/$page_id/$block.dat")) {continue;}

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	$js_function="{$block_id}Javascript";
	if (!function_exists($js_function)) {continue;}

	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$block.dat"));
	$config_params=array();
	if (trim($temp[1])<>'')
	{
		$temp_params=explode("&",$temp[1]);
		foreach ($temp_params as $temp_param)
		{
			$temp_param=explode("=",$temp_param,2);
			$config_params[trim($temp_param[0])]=trim($temp_param[1]);
		}
	}

	$block_js=$js_function($config_params);
	if (strlen($block_js)>0)
	{
		$js_files[]=$block_js;
	}
}
$js_files=array_unique($js_files);

$js_includes='';
foreach($js_files as $js_file)
{
	$js_includes.="<script type=\"text/javascript\" src=\"$config[statics_url]/js/$js_file\"></script>\n    ";
}

$request_uri=clean_request_uri();

require_once("$config[project_path]/admin/include/setup_smarty_site.php");
include_once("$config[project_path]/admin/include/list_countries.php");

$smarty=new mysmarty_site();
$smarty->assign_by_ref("config",$config);
$smarty->assign_by_ref("storage",$storage);
$smarty->assign_by_ref("global_storage",$global_storage);
$smarty->assign("list_countries",$list_countries['name']);
$smarty->assign("list_countries_codes",$list_countries['code']);
$smarty->assign("js_includes",$js_includes);
$smarty->assign("request_uri",$request_uri);
$smarty->assign("page_id",$page_id);

if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
{
	$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
	if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
	{
		$recaptcha_site_key = $recaptcha_data['site_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_site_key = $recaptcha_alias['site_key'];
					break;
				}
			}
		}
		$smarty->assign("recaptcha_site_key", $recaptcha_site_key);
	}
}

$is_post=false;
if (@count($_POST)>0)
{
	$is_post=true;
}

if (!$is_post)
{
	write_stats(0);
}
if ($page_config['is_xml']<>1)
{
	include_once("$config[project_path]/admin/include/pre_process_page_code.php");
}
if (is_file("$config[project_path]/langs/default.php"))
{
	include_once("$config[project_path]/langs/default.php");
}
if ($config['locale']<>'' && is_file("$config[project_path]/langs/$config[locale].php"))
{
	include_once("$config[project_path]/langs/$config[locale].php");
}
if (is_array($lang))
{
	$smarty->assign("lang",$lang);
}

$template=$smarty->fetch("$page_id.tpl");
if (!$is_post)
{
	if ($page_config['is_xml']<>1)
	{
		include_once("$config[project_path]/admin/include/pre_display_page_code.php");
	}
}
echo replace_runtime_params($template);

foreach ($plugin_extensions as $plugin_extension)
{
	$plugin_function = "{$plugin_extension}PostSiteRequest";
	if (function_exists($plugin_function))
	{
		$plugin_function();
	}
}

if ($memcache && $use_memcache==1 && $page_hash<>'' && $page_config['cache_time']>0 && $is_no_cache<>1)
{
	if ($page_config['is_compressed']==1)
	{
		$memcache->set($page_hash, $template, $page_config['cache_time']);
	} else {
		$memcache->set($page_hash, $template, $page_config['cache_time']);
	}
}

if ($page_config['is_xml']<>1)
{
	include_once("$config[project_path]/admin/include/post_process_page_code.php");
}

log_performance(microtime(true)-$start_time,memory_get_peak_usage()-$start_memory,0,null);

function insert_getGlobal($args)
{
	global $config,$page_id,$smarty,$storage,$request_uri,$recaptcha_data,$global_storage,$regexp_check_email,$regexp_check_alpha_numeric,$la,$use_memcache,$list_countries,$lang,$database_selectors;

	if (!is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
	{
		return '';
	}
	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks_list=explode("|AND|",trim($temp[2]));
	foreach ($global_blocks_list as $block)
	{
		$block_id=substr($block,0,strpos($block,"[SEP]"));
		$block_name=substr($block,strpos($block,"[SEP]")+5);
		$block=str_replace("[SEP]","_",$block);

		if ($args['global_id']!=$block) {continue;}

		$old_page_id=$page_id;
		$old_storage=$storage;
		$page_id='$global';
		$storage=array();

		$args=array('block_id'=>$block_id,'block_name'=>$block_name);
		$block_content=insert_getBlock($args);

		$global_storage[$block]=$storage[$block];
		$smarty->assign_by_ref("global_storage",$global_storage);

		$page_id=$old_page_id;
		$storage=$old_storage;
		return $block_content;
	}
	return '';
}

function insert_getBlock($args)
{
	global $config,$page_id,$smarty,$storage,$request_uri,$recaptcha_data,$regexp_check_email,$regexp_check_alpha_numeric,$la,$use_memcache,$list_countries,$lang,$database_selectors,$website_ui_data;

	umask(0);
	$start_time_block=microtime(true);
	$start_memory_block=memory_get_peak_usage();

	$block_id=$args['block_id'];
	$block_name=$args['block_name'];
	$block_name_dir=strtolower(str_replace(" ","_",$block_name));
	$object_id="{$block_id}_$block_name_dir";

	$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$object_id.dat"));
	$cache_time=intval($temp[0]);
	if (intval($temp[2])==1 && $_SESSION['user_id']>0)
	{
		$cache_time=0;
	}
	$config_params=array();
	if (trim($temp[1])<>'')
	{
		$temp_params=explode("&",$temp[1]);
		foreach ($temp_params as $temp_param)
		{
			$temp_param=explode("=",$temp_param,2);
			$config_params[trim($temp_param[0])]=trim($temp_param[1]);
		}
	}

	include_once("$config[project_path]/blocks/$block_id/$block_id.php");
	$smarty=new mysmarty_site();
	$smarty->assign_by_ref("config",$config);
	$smarty->assign_by_ref("storage",$storage);


	$pre_process_function="{$block_id}PreProcess";
	if (function_exists($pre_process_function))
	{
		$pre_process_function($config_params,$object_id);
	}

	$hash_function="{$block_id}GetHash";
	$block_hash=$hash_function($config_params);
	if (in_array($block_hash,array('nocache','runtime_nocache'))) {$is_no_cache=1;} else {$is_no_cache=0;}
	if ($website_ui_data['WEBSITE_CACHING']==2)
	{
		$is_no_cache=1;
	}

	if (trim($temp[4])!='')
	{
		$block_dynamic_params=explode(',',$temp[4]);
		foreach ($block_dynamic_params as $block_dynamic_param)
		{
			if (trim($block_dynamic_param)!='' && trim($_REQUEST[trim($block_dynamic_param)])!='')
			{
				$block_hash='dyn:'.trim($_REQUEST[trim($block_dynamic_param)]).'|'.$block_hash;
			}
		}
	}

	$block_hash="$config[project_url]|$page_id|$object_id|$block_hash";
	if ($config['cache_control_user_status_in_cache']=='true')
	{
		$block_hash=intval($_SESSION['status_id'])."|$block_hash";
	}
	if ($config['project_url_scheme']=="https")
	{
		$block_hash="https|$block_hash";
	}
	if ($config['device']<>"")
	{
		$block_hash="$config[device]|$block_hash";
	}
	if ($config['locale']<>'')
	{
		$block_hash="$config[locale]|$block_hash";
	}
	if ($config['relative_post_dates']=="true")
	{
		$relative_post_date=0;
		if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
		{
			$registration_date=strtotime($_SESSION['added_date']);
			$relative_post_date=floor((time()-$registration_date)/86400)+1;
		}
		$block_hash="$relative_post_date|$block_hash";
	}
	$block_hash=md5($block_hash);

	if ($cache_time>0 && $_SESSION['userdata']['user_id']<1 && $is_no_cache<>1)
	{
		$smarty->caching=1;
		$smarty->cache_lifetime=$cache_time;

		if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
		{
			if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
			{
				$storage[$object_id]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
			}
			if (is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
			{
				@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
			}
			$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);

			log_performance(microtime(true)-$start_time_block,memory_get_peak_usage()-$start_memory_block,1,$object_id);
			if ($res=='')
			{
				$use_memcache=0;
			}
			return $res;
		}

		if (is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat") &&
				time()-filectime("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat")<100)
		{
			$smarty->cache_lifetime=$cache_time+100;
			if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
			{
				$block_storage_temp=array();
				if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
				{
					$block_storage_temp=@unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
				}
				if (is_array($block_storage_temp))
				{
					$storage[$object_id]=$block_storage_temp;
					$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);

					log_performance(microtime(true)-$start_time_block,memory_get_peak_usage()-$start_memory_block,1,$object_id);
					$use_memcache=0;
					return $res;
				}
			}
			$smarty->cache_lifetime=$cache_time;

			$iterations=intval($config['overload_block_wait_iterations']);
			if ($iterations==0) {$iterations=5;}
			$wait_time=intval($config['overload_block_wait_time']);
			if ($wait_time==0) {$wait_time=1;}
			if (time()-filectime("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat")<$iterations*5)
			{
				for ($i=0;$i<$iterations;$i++)
				{
					sleep($wait_time);
					if (!is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
					{
						if ($smarty->is_cached("blocks/$page_id/$object_id.tpl",$block_hash))
						{
							if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
							{
								$storage[$object_id]=unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
							}
							$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);
							log_performance(microtime(true)-$start_time_block,memory_get_peak_usage()-$start_memory_block,1,$object_id);
							$use_memcache=0;
							return $res;
						}
						break;
					}
					clearstatcache();
				}
				if ($i==$iterations)
				{
					write_overload_stats(6);
					$use_memcache=0; return "";
				}
			}
		}

		if (!is_dir("$config[project_path]/admin/data/engine/blocks_state")) {mkdir("$config[project_path]/admin/data/engine/blocks_state",0777);chmod("$config[project_path]/admin/data/engine/blocks_state",0777);}
		if (!is_dir("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]")) {mkdir("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]",0777);chmod("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]",0777);}
		$fp=fopen("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat","w");
		fwrite($fp,"1");
		fclose($fp);
	}

	require_once("$config[project_path]/admin/include/functions.php");
	require_once("$config[project_path]/admin/include/database_selectors.php");

	//overload protection
	if ($config['overload_max_mysql_processes'] > 0 && substr($block_id, 0, 5) == 'list_')
	{
		if (intval($config['overload_min_mysql_processes']) == 0)
		{
			$config['overload_min_mysql_processes'] = $config['overload_max_mysql_processes'];
		}
		if (!isset($config['mysql_processes']))
		{
			$result = sql_pr("show processlist");
			$config['mysql_processes'] = mr2rows($result);
			if ($config['mysql_processes'] > $config['overload_min_mysql_processes'])
			{
				$temp = mr2array($result);
				$config['mysql_processes'] = 0;
				foreach ($temp as $res)
				{
					if ($res['Command'] != 'Sleep')
					{
						$config['mysql_processes']++;
					}
				}
			}
		}
		if ($config['mysql_processes'] > $config['overload_min_mysql_processes'])
		{
			if ($cache_time > 0 && $_SESSION['userdata']['user_id'] < 1 && $is_no_cache <> 1)
			{
				$smarty->cache_lifetime = $cache_time * 5;
				if ($smarty->is_cached("blocks/$page_id/$object_id.tpl", $block_hash))
				{
					if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"))
					{
						$storage[$object_id] = unserialize(file_get_contents("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat"));
					}
					if (is_file("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat"))
					{
						@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
					}
					$res = $smarty->fetch("blocks/$page_id/$object_id.tpl", $block_hash);

					log_performance(microtime(true) - $start_time_block, memory_get_peak_usage() - $start_memory_block, 1, $object_id);
					$use_memcache = 0;
					return $res;
				}
				$smarty->cache_lifetime = $cache_time;
			}
		}
		if ($config['mysql_processes'] > $config['overload_max_mysql_processes'])
		{
			write_overload_stats(4);
			$use_memcache = 0;
			return "";
		}
	}

	include_once("$config[project_path]/admin/include/list_countries.php");

	$smarty->assign("list_countries",$list_countries['name']);
	$smarty->assign("list_countries_codes",$list_countries['code']);
	if (is_array($lang))
	{
		$smarty->assign("lang",$lang);
	}
	$smarty->assign("request_uri",$request_uri);

	if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
	{
		$recaptcha_site_key = $recaptcha_data['site_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_site_key = $recaptcha_alias['site_key'];
					break;
				}
			}
		}
		$smarty->assign("recaptcha_site_key", $recaptcha_site_key);
	}

	foreach ($args as $k=>$v)
	{
		if (strpos($k, 'var_')===0)
		{
			$smarty->assign($k,$v);
		}
	}

	$show_block_function="{$block_id}Show";
	$show_result=$show_block_function($config_params,$object_id);
	if ($show_result=='nocache')
	{
		$use_memcache=0;
	} elseif ($show_result=='status_404')
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
		header('HTTP/1.0 404 Not Found');
		if ($_REQUEST['mode']=='async')
		{
			die;
		} elseif (is_file("$config[project_path]/404.html"))
		{
			echo @file_get_contents("$config[project_path]/404.html");
		} else {
			echo "The requested URL was not found on this server.";
		}
		die;
	} elseif (strpos($show_result,'status_302:')===0)
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
		$redirect_url=substr($show_result,11);
		header("Location: $redirect_url");
		die;
	} elseif (strpos($show_result,'status_301:')===0)
	{
		ob_end_clean();
		@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");
		$redirect_url=substr($show_result,11);
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: $redirect_url");
		die;
	}

	if (!is_dir("$config[project_path]/admin/data/engine/storage")) {mkdir("$config[project_path]/admin/data/engine/storage",0777);chmod("$config[project_path]/admin/data/engine/storage",0777);}
	if (!is_dir("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]")) {mkdir("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]",0777);chmod("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]",0777);}
	if (is_file("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat")){@chmod("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat",0666);}
	$fp=fopen("$config[project_path]/admin/data/engine/storage/$block_hash[0]$block_hash[1]/{$object_id}_$block_hash.dat","w");
	fwrite($fp,serialize($storage[$object_id]));
	fclose($fp);

	$smarty->assign('block_uid',$object_id);
	if ($page_id=='$global')
	{
		$smarty->assign('is_global',1);
	}
	$smarty->assign('page_id',$page_id);
	$res=$smarty->fetch("blocks/$page_id/$object_id.tpl",$block_hash);
	$smarty->clear_all_assign();
	@unlink("$config[project_path]/admin/data/engine/blocks_state/$block_hash[0]$block_hash[1]/$block_hash.dat");

	log_performance(microtime(true)-$start_time_block,memory_get_peak_usage()-$start_memory_block,0,$object_id);
	if ($res=='')
	{
		$use_memcache=0;
	}
	return $res;
}

function insert_getAdv($args)
{
	global $config, $storage;

	$spot_id = trim($args['place_id']);

	$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$spot_id.dat";
	if (!is_file($spot_data_file))
	{
		return '';
	}
	$spot_info = unserialize(file_get_contents($spot_data_file));
	if (!is_array($spot_info))
	{
		return '';
	}

	$ads = array();
	foreach ($spot_info['ads'] as $ad)
	{
		if ($ad['is_active'] == 1)
		{
			$ads[] = $ad;
		}
	}

	$category_ids_in_context = array();
	foreach ($storage as $storage_info)
	{
		if (is_array($storage_info['category_info']) && intval($storage_info['category_info']['category_id']) > 0)
		{
			$category_ids_in_context[intval($storage_info['category_info']['category_id'])] = intval($storage_info['category_info']['category_id']);
		}
		if (is_array($storage_info['categories']))
		{
			foreach ($storage_info['categories'] as $category_info)
			{
				if (intval($category_info['category_id']) > 0)
				{
					$category_ids_in_context[intval($category_info['category_id'])] = intval($category_info['category_id']);
				}
			}
		}
	}
	if (count($category_ids_in_context) > 0)
	{
		$has_categorized_ads = false;
		foreach ($ads as $k => $ad)
		{
			if (@count($ad['category_ids']) > 0)
			{
				$should_delete_ad = true;
				foreach ($ad['category_ids'] as $ad_category_id)
				{
					if (isset($category_ids_in_context[$ad_category_id]))
					{
						$has_categorized_ads = true;
						$should_delete_ad = false;
						break;
					}
				}
				if ($should_delete_ad)
				{
					unset($ads[$k]);
				}
			}
		}

		if ($has_categorized_ads)
		{
			foreach ($ads as $k => $ad)
			{
				if (@count($ad['category_ids']) == 0)
				{
					unset($ads[$k]);
				}
			}
		}

		foreach ($ads as $k => $ad)
		{
			if (@count($ad['exclude_category_ids']) > 0)
			{
				$should_delete_ad = false;
				foreach ($ad['exclude_category_ids'] as $ad_category_id)
				{
					if (isset($category_ids_in_context[$ad_category_id]))
					{
						$should_delete_ad = true;
						break;
					}
				}
				if ($should_delete_ad)
				{
					unset($ads[$k]);
				}
			}
		}
	} else {
		foreach ($ads as $k => $ad)
		{
			if (@count($ad['category_ids']) > 0)
			{
				unset($ads[$k]);
			}
		}
	}

	if (count($ads) > 0)
	{
		$has_dynamic_ad = false;
		foreach ($ads as $ad)
		{
			if ($ad['show_from_date'] != '0000-00-00' || $ad['show_to_date'] != '0000-00-00')
			{
				$has_dynamic_ad = true;
				break;
			}
			if (intval($ad['show_from_time']) > 0 || intval($ad['show_to_time']) > 0)
			{
				$has_dynamic_ad = true;
				break;
			}
			if (@count($ad['devices']) > 0)
			{
				$has_dynamic_ad = true;
				break;
			}
			if (@count($ad['browsers']) > 0)
			{
				$has_dynamic_ad = true;
				break;
			}
			if (@count($ad['users']) > 0)
			{
				$has_dynamic_ad = true;
				break;
			}
			if ($ad['countries'] != '')
			{
				$has_dynamic_ad = true;
				break;
			}
			if (intval($ad['enable_refresh']) == 1)
			{
				$has_dynamic_ad = true;
				break;
			}
		}

		if ($has_dynamic_ad)
		{
			$ads_str = "%KTA:$spot_id:";
			$ads_list = '';
			foreach ($ads as $ad)
			{
				$ads_list .= "$ad[advertisement_id],";
			}
			$ads_list = trim($ads_list, ',');
			$ads_str .= "$ads_list%";
			if (intval($spot_info['is_debug_enabled']) == 1)
			{
				file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "Statically prepared $ads_list for URI: $_SERVER[REQUEST_URI]\n", FILE_APPEND | LOCK_EX);
			}
			return $ads_str;
		} else
		{
			sort($ads, SORT_NUMERIC);
			$ad = $ads[mt_rand(0, count($ads) - 1)];
			if (intval($spot_info['is_debug_enabled']) == 1)
			{
				file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "Statically displayed advertising $ad[advertisement_id] / \"$ad[title]\" for URI: $_SERVER[REQUEST_URI]\n", FILE_APPEND | LOCK_EX);
			}
			$advertising_code = $ad['code'];
			$advertising_code = str_replace("%URL%", "$config[project_url]/?action=trace&amp;id=$ad[advertisement_id]", $advertising_code);
			if ($spot_info['template'] != '')
			{
				$advertising_code = str_replace("%ADV%", $advertising_code, $spot_info['template']);
			}
			return $advertising_code;
		}
	} else
	{
		return "";
	}
}

function clean_request_uri()
{
	global $runtime_params;

	if (strpos($_SERVER['REQUEST_URI'],'?')!==false)
	{
		$request_uri=substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'?'));
		$request_uri_params=explode("&",substr($_SERVER['REQUEST_URI'],strpos($_SERVER['REQUEST_URI'],'?')+1));
		foreach ($request_uri_params as $param)
		{
			$param=explode("=",$param,2);
			$var=$param[0];
			$val=$param[1];
			if ($var=='pqr')
			{
				continue;
			}
			if (is_array($runtime_params))
			{
				foreach ($runtime_params as $param2)
				{
					if ($var==trim($param2['name']))
					{
						continue 2;
					}
				}
			}
			if ($var<>'' && $val<>'')
			{
				if (strpos($request_uri,'?')!==false)
				{
					$request_uri.="&$var=$val";
				} else {
					$request_uri.="?$var=$val";
				}
			}
		}
	} else {
		$request_uri=$_SERVER['REQUEST_URI'];
	}
	return $request_uri;
}

function configure_locale()
{
	global $config;

	$kt_lang = $_COOKIE['kt_lang'];
	if ($_REQUEST['kt_lang'])
	{
		$kt_lang = $_REQUEST['kt_lang'];
	}
	if ($kt_lang == '' && @count($config['locales']) > 0)
	{
		if (@in_array($_SESSION['user_info']['language_code'], $config['locales']))
		{
			$kt_lang = $_SESSION['user_info']['language_code'];
		} else
		{
			$user_locales = array_map('trim', explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
			foreach ($config['locales'] as $supported_locale)
			{
				if (strpos($user_locales[0], $supported_locale) === 0)
				{
					$kt_lang = $supported_locale;
					break;
				}
			}
		}
	}
	if ($kt_lang)
	{
		$domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
		if (is_array($config['locales']) && in_array($kt_lang, $config['locales']))
		{
			$config['locale'] = $kt_lang;
		}
		if ($_REQUEST['kt_lang'] && $config['locale_set_cookie'] == 'true')
		{
			setcookie('kt_lang', $kt_lang, time() + 31104000, '/', ".$domain");
		}
	}
}

function validate_recaptcha($code, $recaptcha_data)
{
	global $config;

	if (!$code)
	{
		return false;
	}
	if (is_array($recaptcha_data) && $recaptcha_data['secret_key'])
	{
		$recaptcha_secret_key = $recaptcha_data['secret_key'];
		if (is_array($recaptcha_data['aliases']))
		{
			foreach ($recaptcha_data['aliases'] as $recaptcha_alias)
			{
				if (str_replace('www.', '', $_SERVER['HTTP_HOST']) == $recaptcha_alias['domain'])
				{
					$recaptcha_secret_key = $recaptcha_alias['secret_key'];
					break;
				}
			}
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, array(
			'secret' => $recaptcha_secret_key,
			'response' => $code,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		));
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$recaptcha_response = curl_exec($ch);
		if (curl_errno($ch) > 0)
		{
			file_put_contents("$config[project_path]/admin/logs/log_curl_errors.txt", "[" . date("Y-m-d H:i:s") . "] [" . curl_errno($ch) . "] " . curl_error($ch) . "\n", FILE_APPEND | LOCK_EX);
		}
		curl_close($ch);

		if ($recaptcha_response)
		{
			$recaptcha_response = @json_decode($recaptcha_response, true);
			if (is_array($recaptcha_response) && intval($recaptcha_response['success']) == 1)
			{
				return true;
			}
		}
		return false;
	}

	return false;
}

function check_page_access($page_config)
{
	global $config;

	if (intval($page_config[4])==1)
	{
		header('HTTP/1.0 404 Not Found');
		if (is_file("$config[project_path]/404.html"))
		{
			echo @file_get_contents("$config[project_path]/404.html");
		} else {
			echo "The requested URL was not found on this server.";
		}
		die;
	}
	if ((intval($page_config[5])==1 && $_SESSION['user_id']<1) || (intval($page_config[5])==2 && $_SESSION['status_id']<>3) || (intval($page_config[5])==3 && $_SESSION['status_id']<>6) || (intval($page_config[5])==4 && $_SESSION['is_trusted']<>1))
	{
		if (trim($page_config[6])<>'')
		{
			$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
			header("Location: $page_config[6]");
		} else {
			header('HTTP/1.0 403 Forbidden');
			echo "Access denied";
		}
		die;
	}
}

function replace_runtime_params($page)
{
	global $config,$runtime_params,$page_is_xml;

	$page=trim($page);

	$hotlink_data = @unserialize(file_get_contents("$config[project_path]/admin/data/system/hotlink_info.dat"));
	if (intval($hotlink_data['ENABLE_ANTI_HOTLINK'])==1 && intval($hotlink_data['ANTI_HOTLINK_TYPE']) == 1)
	{
		$lock_ips = explode(',', trim($_COOKIE['kt_ips']));
		if (!in_array($_SERVER['REMOTE_ADDR'], $lock_ips))
		{
			$lock_ips[] = $_SERVER['REMOTE_ADDR'];
		}

		$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
		setcookie("kt_ips", trim(implode(',', $lock_ips), ', '), time() + 86400, "/", ".$domain");

		$lock_ip = $_SERVER['REMOTE_ADDR'];
		if (!is_array($_SESSION['lock_ips']) || !isset($_SESSION['lock_ips'][$_SERVER['REMOTE_ADDR']]))
		{
			$_SESSION['lock_ips'][$_SERVER['REMOTE_ADDR']] = 1;
		}
		$pos = strpos($page, '/get_file/');
		if ($pos !== false)
		{
			$result = '';
			$pos2 = 0;
			while ($pos !== false)
			{
				$pos = strpos($page, '/', $pos + 10) + 1;
				$length = strpos($page, '/', $pos + 1) - $pos;
				$token = substr($page, $pos, $length);
				if ($length == 32)
				{
					$token .= substr(md5($token . $config['cv'] . $lock_ip), 0, 10);
				}

				$result .= substr($page, $pos2, $pos - $pos2) . $token;
				$pos2 = $pos + $length;
				$pos = strpos($page, '/get_file/', $pos + 1);
			}
			$result .= substr($page, $pos2, strlen($page) - $pos2);
			$page = $result;
		}
	}

	// advanced advertising
	$pos = strpos($page, '%KTA:');
	if ($pos !== false)
	{
		$now_date = time();
		$now_time = explode(':', date("H:i"));
		$now_time = intval($now_time[0]) * 3600 + intval($now_time[1]) * 60;

		$result = '';
		$pos2 = 0;
		while ($pos !== false)
		{
			$length = strpos($page, '%', $pos + 1) + 1 - $pos;
			$token = substr($page, $pos + 5, $length - 6);
			$spot_id = substr($token, 0, strpos($token, ':'));
			$token = substr($token, strlen($spot_id) + 1);

			$ads = array();
			$ads_empty = array();
			$ads_ids = explode(',', $token);

			$spot_info = array();
			$spot_data_file = "$config[project_path]/admin/data/advertisements/spot_$spot_id.dat";
			if (is_file($spot_data_file))
			{
				$spot_info = @unserialize(file_get_contents($spot_data_file), ['allowed_classes' => false]);
			}
			if (is_array($spot_info['ads']))
			{
				foreach ($ads_ids as $advertisement_id)
				{
					if ($advertisement_id == '' || !isset($spot_info['ads'][$advertisement_id]))
					{
						continue;
					}
					$ad_info = $spot_info['ads'][$advertisement_id];

					if ($ad_info['is_active'] == 0)
					{
						continue;
					}
					if (($ad_info['show_from_date'] != '0000-00-00' && strtotime($ad_info['show_from_date']) > $now_date) || ($ad_info['show_to_date'] != '0000-00-00' && strtotime($ad_info['show_to_date']) < $now_date))
					{
						continue;
					}
					if ($ad_info['show_from_time'] > 0 || $ad_info['show_to_time'] > 0)
					{
						if ($now_time < $ad_info['show_from_time'] || $now_time > $ad_info['show_to_time'])
						{
							$skip_ad = true;
							if ($ad_info['show_from_time'] > $ad_info['show_to_time'])
							{
								if (($now_time > $ad_info['show_from_time'] && $now_time < 86400) || $now_time < $ad_info['show_to_time'])
								{
									$skip_ad = false;
								}
							}
							if ($skip_ad)
							{
								continue;
							}
						}
					}
					if (@count($ad_info['devices']) > 0)
					{
						if (!class_exists('Mobile_Detect'))
						{
							include_once "$config[project_path]/admin/include/mobiledetect/Mobile_Detect.php";
						}
						if (class_exists('Mobile_Detect'))
						{
							$mobiledetect = new Mobile_Detect();
							$ad_device_show = false;
							foreach ($ad_info['devices'] as $ad_device)
							{
								if ($ad_device_show)
								{
									break;
								}
								switch ($ad_device)
								{
									case 'pc':
										$ad_device_show = !$mobiledetect->isMobile();
										break;
									case 'tablet':
										$ad_device_show = $mobiledetect->isTablet();
										break;
									case 'phone':
										$ad_device_show = $mobiledetect->isMobile() && !$mobiledetect->isTablet();
										break;
								}
							}
							if (!$ad_device_show)
							{
								continue;
							}
						}
					}
					if (@count($ad_info['browsers']) > 0)
					{
						$current_browser = get_user_agent_code();
						if (!in_array($current_browser, $ad_info['browsers']))
						{
							continue;
						}
					}
					if (@count($ad_info['users']) > 0)
					{
						$ad_user_show = false;
						foreach ($ad_info['users'] as $ad_user)
						{
							if ($ad_user_show)
							{
								break;
							}
							switch ($ad_user)
							{
								case 'guest':
									$ad_user_show = intval($_SESSION['user_id']) < 1;
									break;
								case 'active':
									$ad_user_show = intval($_SESSION['status_id']) == 2;
									break;
								case 'premium':
									$ad_user_show = intval($_SESSION['status_id']) == 3;
									break;
								case 'webmaster':
									$ad_user_show = intval($_SESSION['status_id']) == 6;
									break;
							}
						}
						if (!$ad_user_show)
						{
							continue;
						}
					}

					$countries = explode(',', $ad_info['countries']);
					if ($advertisement_id > 0)
					{
						if (count($countries) == 0 || (count($countries) == 1 && $countries[0] == ''))
						{
							$ads_empty[] = $advertisement_id;
						} else
						{
							foreach ($countries as $country_code)
							{
								if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
								{
									$ads[] = $advertisement_id;
									break;
								}
							}
						}
					}
				}
			}

			if (count($ads) == 0)
			{
				$ads = $ads_empty;
			}

			if (count($ads) > 0)
			{
				$advertisement_id = $ads[mt_rand(0, count($ads) - 1)];
				$ad_info = $spot_info['ads'][$advertisement_id];
				if (is_array($spot_info) && isset($ad_info))
				{
					$token = $ad_info['code'];
					$token = str_replace("%URL%", "$config[project_url]/?action=trace&amp;id=$advertisement_id", $token);
					if ($spot_info['template'] != '')
					{
						$token = str_replace("%ADV%", $token, $spot_info['template']);
					}

					if (intval($spot_info['is_debug_enabled']) == 1)
					{
						$ads_str = implode(',', $ads);
						file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "Dynamically displayed advertising $ad_info[advertisement_id] / \"$ad_info[title]\" from $ads_str for URI: $_SERVER[REQUEST_URI], User: $_SESSION[username], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
					}
				} else
				{
					$token = '';
					if (intval($spot_info['is_debug_enabled']) == 1)
					{
						file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "No advertising for URI: $_SERVER[REQUEST_URI], User: $_SESSION[username], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
					}
				}
			} else
			{
				$token = '';
				if (intval($spot_info['is_debug_enabled']) == 1)
				{
					file_put_contents("$config[project_path]/admin/logs/debug_ad_spot_$spot_id.txt", date("[Y-m-d H:i:s] ") . "No advertising for URI: $_SERVER[REQUEST_URI], User: $_SESSION[username], Agent: $_SERVER[HTTP_USER_AGENT], Country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
			}

			$result .= substr($page, $pos2, $pos - $pos2) . $token;
			$pos2 = $pos + $length;
			$pos = strpos($page, '%KTA:', $pos + 1);
		}
		$result .= substr($page, $pos2, strlen($page) - $pos2);
		$page = $result;
	}

	// advanced advertising
	$pos = strpos($page, '%KTV:');
	if ($pos !== false)
	{
		$result = '';
		$pos2 = 0;
		while ($pos !== false)
		{
			$length = strpos($page, '%', $pos + 1) + 1 - $pos;
			$token = substr($page, $pos + 5, $length - 6);
			$profile_id = $token;

			$ads = [];
			$ads_sorting = [];

			$profile_data_file = "$config[project_path]/admin/data/player/vast/vast_$profile_id.dat";
			$profile_info = null;
			if (is_file($profile_data_file))
			{
				$profile_info = @unserialize(file_get_contents($profile_data_file));
			}

			$seen_ads = [];
			if (is_array($profile_info) && is_array($profile_info['providers']))
			{
				if (trim($_COOKIE["kt_vast_$profile_id"]))
				{
					$seen_ads = explode(',', trim($_COOKIE["kt_vast_$profile_id"]));
				}

				foreach ($profile_info['providers'] as $provider)
				{
					if (intval($provider['is_enabled']) == 0)
					{
						continue;
					}

					$show_ad_countries = false;
					$show_ad_referers = false;
					$skip_ad = false;

					if (!$provider['countries'])
					{
						$show_ad_countries = true;
					} else
					{
						$countries = explode(',', $provider['countries']);
						foreach ($countries as $country_code)
						{
							if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
							{
								$show_ad_countries = true;
								break;
							}
						}
					}

					if (!$provider['referers'])
					{
						$show_ad_referers = true;
					} else
					{
						$referers = array_map('trim', explode("\n", $provider['referers']));
						foreach ($referers as $referer)
						{
							if ($referer)
							{
								if (is_url($referer))
								{
									$referer_host = str_replace('www.', '', trim(parse_url($referer, PHP_URL_HOST)));
									$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
									if (strpos($current_referer_host, $referer_host) === 0)
									{
										$show_ad_referers = true;
										break;
									}
								} elseif (strpos($_SERVER['REQUEST_URI'], $referer) !== false)
								{
									$show_ad_referers = true;
									break;
								}
							}
						}
					}

					if ($provider['exclude_countries'])
					{
						$countries = explode(',', $provider['exclude_countries']);
						foreach ($countries as $country_code)
						{
							if (strtolower(trim($country_code)) == strtolower($_SERVER['GEOIP_COUNTRY_CODE']))
							{
								$skip_ad = true;
								break;
							}
						}
					}

					if ($provider['exclude_referers'])
					{
						$referers = array_map('trim', explode("\n", $provider['exclude_referers']));
						foreach ($referers as $referer)
						{
							if ($referer)
							{
								if (is_url($referer))
								{
									$referer_host = str_replace('www.', '', trim(parse_url($referer, PHP_URL_HOST)));
									$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
									if (strpos($current_referer_host, $referer_host) === 0)
									{
										$skip_ad = true;
										break;
									}
								} elseif (strpos($_SERVER['REQUEST_URI'], $referer) !== false)
								{
									$skip_ad = true;
									break;
								}
							}
						}
					}

					if ($show_ad_countries && $show_ad_referers && !$skip_ad)
					{
						$ads[] = $provider;
						$ads_sorting[] = intval($provider['weight']);
					}
				}
			}

			array_multisort($ads_sorting, SORT_NUMERIC, SORT_DESC, $ads);

			$temp_ads = $ads;
			foreach ($temp_ads as $k => $provider)
			{
				if (in_array(md5($provider['url']), $seen_ads))
				{
					unset($temp_ads[$k]);
				}
			}
			if (count($temp_ads) == 0)
			{
				$temp_ads = $ads;
				$seen_ads = [];
			}

			if (count($temp_ads) > 0)
			{
				$provider = array_pop(array_reverse($temp_ads));
				$seen_ads[] = md5($provider['url']);

				$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
				setcookie("kt_vast_$profile_id", trim(implode(',', $seen_ads), ', '), time() + 86400, "/", ".$domain");

				$token = $provider['url'];
				if (intval($profile_info['is_debug_enabled']) == 1)
				{
					$seen_ads_count = count($seen_ads) - 1;
					file_put_contents("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt", date("[Y-m-d H:i:s] ") . "Displayed VAST $token after $seen_ads_count displayed ads for country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
				if ($provider['alt_url'])
				{
					$alternate_vasts = [];
					foreach (array_map('trim', explode("\n", $provider['alt_url'])) as $vast)
					{
						if ($vast)
						{
							$alternate_vasts[] = $vast;
						}
					}
					if (count($alternate_vasts) > 0)
					{
						$token .= '|' . implode('|', $alternate_vasts);
					}
				}
			} else
			{
				$token = '';
				if (intval($profile_info['is_debug_enabled']) == 1)
				{
					file_put_contents("$config[project_path]/admin/logs/debug_vast_profile_$profile_id.txt", date("[Y-m-d H:i:s] ") . "No VAST for country: $_SERVER[GEOIP_COUNTRY_CODE]\n", FILE_APPEND | LOCK_EX);
				}
			}

			$result .= substr($page, $pos2, $pos - $pos2) . $token;
			$pos2 = $pos + $length;
			$pos = strpos($page, '%KTV:', $pos + 1);
		}
		$result .= substr($page, $pos2);
		$page = $result;
	}

	if (is_array($runtime_params))
	{
		foreach ($runtime_params as $param)
		{
			$var=trim($param['name']);
			$val=$_SESSION['runtime_params'][$var];
			if (strlen($val)==0)
			{
				$val=trim($param['default_value']);
			}
			if ($var<>'')
			{
				$val=str_replace("\"","&#34;",$val);
				$val=str_replace(">","&gt;",$val);
				$val=str_replace("<","&lt;",$val);
				$page=str_replace("%$var%",$val,$page);
			}
		}
	}

	if ($config['minify_html'] == 'true')
	{
		$page = preg_replace('/\s+/', ' ', $page);
	}

	if (is_file("$config[project_path]/admin/data/plugins/push_notifications/enabled.dat"))
	{
		$pos=strripos($page,'</body>', -1);
		if ($pos!==false)
		{
			$push_options = @unserialize(@file_get_contents("$config[project_path]/admin/data/plugins/push_notifications/data.dat"));
			if (is_array($push_options) && intval($push_options['is_enabled']) == 1)
			{
				if (strpos($config['project_url'], "https://") !== false)
				{
					$token = "<script type=\"application/javascript\" src=\"$config[project_url]/sw.js?tag_id=$push_options[refid]&amp;puid=kvs\"></script>\n";
				} else
				{
					$token = "<script data-cfasync=\"false\" src=\"//d2d8qsxiai9qwj.cloudfront.net/?xsqdd=$push_options[refid]&amp;puid=kvs\"></script>";
				}
				$show = true;
				if ($push_options['repeat'] == 'once' && intval($_COOKIE['kt_pn']) > 0)
				{
					$show = false;
				} elseif ($push_options['repeat'] == 'interval' && intval($_COOKIE['kt_pn']) > 0 && (intval($_COOKIE['kt_pn']) + intval($push_options['repeat_interval']) * 60) > time())
				{
					$show = false;
				}
				if ($push_options['exclude_members'] == 'all' && intval($_SESSION['user_id']) > 0)
				{
					$show = false;
				} elseif ($push_options['exclude_members'] == 'premium' && intval($_SESSION['status_id']) == 3)
				{
					$show = false;
				}
				if ($push_options['exclude_referers'])
				{
					$exclude_referers = array_map('trim', explode("\n", $push_options['exclude_referers']));
					foreach ($exclude_referers as $exclude_referer)
					{
						if ($exclude_referer)
						{
							if (is_url($exclude_referer))
							{
								$exclude_referer_host = str_replace('www.', '', trim(parse_url($exclude_referer, PHP_URL_HOST)));
								$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
								if (strpos($current_referer_host, $exclude_referer_host) === 0)
								{
									$show = false;
								}
							} elseif (strpos($_SERVER['REQUEST_URI'], $exclude_referer) !== false)
							{
								$show = false;
							}
						}
					}
				}
				if ($push_options['include_referers'])
				{
					$include_referers = array_map('trim', explode("\n", $push_options['include_referers']));
					foreach ($include_referers as $include_referer)
					{
						if ($include_referer)
						{
							if (is_url($include_referer))
							{
								$include_referer_host = str_replace('www.', '', trim(parse_url($include_referer, PHP_URL_HOST)));
								$current_referer_host = str_replace('www.', '', trim(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST)));
								if (strpos($current_referer_host, $include_referer_host) !== 0)
								{
									$show = false;
								}
							} elseif (strpos($_SERVER['REQUEST_URI'], $include_referer) === false)
							{
								$show = false;
							}
						}
					}
				}
				if (intval($push_options['skip_first_click']) == 1 && intval($_COOKIE['kt_pnf']) == 0)
				{
					$show = false;
					$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
					setcookie("kt_pnf", 1, time() + 86400, "/", ".$domain");
				}

				if ($show)
				{
					$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);
					setcookie("kt_pn", time(), time() + 86400, "/", ".$domain");

					$page = substr($page, 0, $pos) . $token . substr($page, $pos);
				}
			}
		}
	}

	if ($page_is_xml<>1 && $config['disable_rotator']<>'true')
	{
		// rotator
		$result='';
		$pos=strpos($page,'%KTR:');
		if ($pos===false)
		{
			return $page;
		}
		$tokens_list=array();
		$pos2=0;
		while ($pos!==false)
		{
			$token=substr($page,$pos,12);
			if (isset($tokens_list[$token]))
			{
				$index=$tokens_list[$token];
			} else {
				$max_index=intval(substr($page,$pos+5,2));
				if ($max_index==0)
				{
					$max_index=1;
				}
				$index=mt_rand(1,$max_index);
				$tokens_list[$token]=$index;
			}
			$result.=substr($page,$pos2,$pos-$pos2).$index;
			$pos2=$pos+12;
			$pos=strpos($page,'%KTR:',$pos+1);
		}
		$result.=substr($page,$pos2,strlen($page)-$pos2);
		return $result;
	}
	return $page;
}

function log_performance($exec_time,$memory,$was_cached,$block_uid)
{
	global $config,$page_id,$performance_log_summary;

	if ($config['disable_performance_stats']=='true')
	{
		return;
	}

	if ($block_uid)
	{
		$path="$config[project_path]/admin/data/analysis/performance/{$page_id}_{$block_uid}.dat";
	} else {
		$path="$config[project_path]/admin/data/analysis/performance/$page_id.dat";
	}
	$fp=fopen($path,"a+");
	flock($fp,LOCK_EX);
	$performance_log=@unserialize(file_get_contents($path));

	if (!is_array($performance_log))
	{
		$performance_log=array();
		$performance_log['cached_avg_time_s']=0;
		$performance_log['cached_requests_count']=1;
		$performance_log['uncached_avg_time_s']=0;
		$performance_log['uncached_requests_count']=1;
		$performance_log['max_memory']=0;
	}

	if ($was_cached==1)
	{
		$performance_log['cached_avg_time_s']=($performance_log['cached_avg_time_s']*$performance_log['cached_requests_count']+$exec_time)/($performance_log['cached_requests_count']+1);
		$performance_log['cached_requests_count']+=1;
	} else {
		$performance_log['uncached_avg_time_s']=($performance_log['uncached_avg_time_s']*$performance_log['uncached_requests_count']+$exec_time)/($performance_log['uncached_requests_count']+1);
		$performance_log['uncached_requests_count']+=1;
	}
	$performance_log['max_memory']=max($performance_log['max_memory'],$memory);

	ftruncate($fp,0);
	fwrite($fp,serialize($performance_log));
	fclose($fp);

	if ($_REQUEST['debug']=='true')
	{
		if ($block_uid)
		{
			$performance_log_summary.="$block_uid ".($was_cached==1?'from cache':'generated')." in {$exec_time}s, ";
		} else
		{
			if ($was_cached==1)
			{
				echo "<!--Page $page_id from cache in {$exec_time}s-->";
			} else
			{
				echo "<!--Page $page_id generated in {$exec_time}s [".trim($performance_log_summary,', ').']-->';
			}
		}
	}
}

function write_stats($stats_mode)
{
	global $config, $stats_params;

	if (intval($stats_params['collect_traffic_stats']) == 0)
	{
		return;
	}

	$domain = str_replace("www.", "", $_SERVER['HTTP_HOST']);

	$is_uniq = 1;
	if ($stats_mode == 1)
	{
		setcookie("kt_is_visited", 1, time() + 86400, "/", ".$domain");
		if ($_COOKIE['kt_is_visited'] == 1)
		{
			$is_uniq = 0;
		}
	}

	$incoming_page_params = '';
	if (strpos($_SERVER['HTTP_REFERER'], $domain) === false)
	{
		$referer = $_SERVER['HTTP_REFERER'];
		if (strlen($referer) > 255)
		{
			$referer = substr($referer, 0, 255);
		}
		$incoming_page_params = $_SERVER['QUERY_STRING'];
	} else
	{
		$referer = "";
	}

	if ($referer <> '')
	{
		setcookie("kt_referer", $referer, time() + 86400, "/", ".$domain");
	} else
	{
		$referer = $_COOKIE['kt_referer'];
	}
	if ($incoming_page_params <> '')
	{
		setcookie("kt_qparams", $incoming_page_params, time() + 86400, "/", ".$domain");
	} else
	{
		$incoming_page_params = $_COOKIE['kt_qparams'];
	}

	$device_type = 0;
	if (intval($stats_params['collect_traffic_stats_devices']) == 1)
	{
		$device_type = get_device_type();
	}

	file_put_contents("$config[project_path]/admin/data/stats/in.dat", date("Y-m-d") . "|$is_uniq|$_SERVER[GEOIP_COUNTRY_CODE]|$referer|$incoming_page_params|$stats_mode|$device_type\r\n", LOCK_EX | FILE_APPEND);
}

function write_overload_stats($stats_mode)
{
	global $config;

	file_put_contents("$config[project_path]/admin/data/stats/overload.dat", date("Y-m-d") . "|$stats_mode\r\n", LOCK_EX | FILE_APPEND);
}

function login_user($user_data,$remember_for_days)
{
	global $config,$database_selectors;

	require_once("$config[project_path]/admin/include/database_selectors.php");

	if ($user_data["avatar"]!='')
	{
		$user_data["avatar_url"]=$config['content_url_avatars']."/".$user_data['avatar'];
	}
	if ($user_data["cover"]!='')
	{
		$user_data["cover_url"]=$config['content_url_avatars']."/".$user_data['cover'];
	}

	$_SESSION['user_id']=$user_data["user_id"];
	$_SESSION['display_name']=$user_data["display_name"];
	$_SESSION['last_login_date']=$user_data["last_login_date"];
	$_SESSION['added_date']=$user_data["added_date"];
	$_SESSION['avatar']=$user_data["avatar"];
	$_SESSION['avatar_url']=$user_data["avatar_url"];
	$_SESSION['cover']=$user_data["cover"];
	$_SESSION['cover_url']=$user_data["cover_url"];
	$_SESSION['status_id']=$user_data["status_id"];
	$_SESSION['username']=$user_data["username"];
	$_SESSION['content_source_group_id']=$user_data["content_source_group_id"];
	$_SESSION['is_trusted']=$user_data["is_trusted"];
	$_SESSION['tokens_available']=$user_data["tokens_available"];
	$_SESSION['birth_date']=$user_data["birth_date"];
	$_SESSION['gender_id']=$user_data["gender_id"];
	if ($_SESSION['birth_date']!='0000-00-00')
	{
		$age=get_time_passed($_SESSION['birth_date']);
		$_SESSION['age']=$age['value'];
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	$login_award_tokens=intval($memberzone_data['AWARDS_LOGIN']);
	if ($login_award_tokens>0)
	{
		$login_award_interval=intval($memberzone_data['AWARDS_LOGIN_CONDITION']);
		if ($login_award_interval==0 || mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where user_id=? and award_type=15 and added_date>DATE_SUB(?, INTERVAL $login_award_interval HOUR)",$_SESSION['user_id'],date("Y-m-d H:i:s")))==0)
		{
			sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=15, user_id=?, tokens_granted=?, added_date=?",$_SESSION['user_id'],$login_award_tokens,date("Y-m-d H:i:s"));
			$_SESSION['tokens_available']=intval($_SESSION['tokens_available'])+$login_award_tokens;
		} else
		{
			$login_award_tokens=0;
		}
	}

	sql_pr("insert into $config[tables_prefix]log_logins_users set is_failed=0, ip=?, country_code=lower(?), login_date=?, username=?, user_id=?, user_agent=?",ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),date("Y-m-d H:i:s"),$_SESSION['username'],$_SESSION['user_id'],get_user_agent());

	$_SESSION['unread_messages'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and is_read=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
	$_SESSION['unread_invites'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and type_id=1 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
	$_SESSION['unread_non_invites'] = $_SESSION['unread_messages'] - $_SESSION['unread_invites'];
	$_SESSION['last_time_get_new_message_amount']=time();

	$_SESSION['content_purchased']=mr2array(sql_pr("select distinct video_id, album_id, profile_id, dvd_id from $config[tables_prefix]users_purchases where user_id=? and expiry_date>?",$_SESSION['user_id'],date("Y-m-d H:i:s")));
	$_SESSION['content_purchased_amount']=count($_SESSION['content_purchased']);
	$_SESSION['playlists']=mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=? order by title asc",$_SESSION['user_id']));
	$_SESSION['playlists_amount']=count($_SESSION['playlists']);

	$temp_summary=array();
	$_SESSION['favourite_videos_amount']=0;
	$_SESSION['favourite_videos_summary']=mr2array(sql_pr("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=? group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc",$_SESSION['user_id']));
	foreach ($_SESSION['favourite_videos_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_videos_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_videos_summary']=$temp_summary;

	$temp_summary=array();
	$_SESSION['favourite_albums_amount']=0;
	$_SESSION['favourite_albums_summary']=mr2array(sql_pr("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=? group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc",$_SESSION['user_id']));
	foreach ($_SESSION['favourite_albums_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_albums_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_albums_summary']=$temp_summary;

	$_SESSION['subscriptions_amount']=mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=?",$_SESSION['user_id']));

	if ($_SESSION['status_id']=='3')
	{
		sql_pr("update $config[tables_prefix]bill_transactions set status_id=1, access_start_date=?, access_end_date=(case when is_unlimited_access=1 then '2070-01-01 00:00:00' else date_add(?, interval duration_rebill day) end), duration_rebill=0, ip=?, country_code=lower(?) where status_id=4 and user_id=?", date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), intval($_SESSION['user_id']));

		$transaction_data=mr2array_single(sql_pr("select (UNIX_TIMESTAMP(access_end_date) - UNIX_TIMESTAMP(?)) / 3600 as hours_left, is_unlimited_access, external_guid, external_package_id from $config[tables_prefix]bill_transactions where status_id=1 and user_id=? order by access_end_date desc limit 1",date("Y-m-d H:i:s"),$_SESSION['user_id']));
		$_SESSION['paid_access_hours_left']=intval($transaction_data['hours_left']);
		$_SESSION['paid_access_is_unlimited']=intval($transaction_data['is_unlimited_access']);
		$_SESSION['external_guid']=trim($transaction_data['external_guid']);
		$_SESSION['external_package_id']=trim($transaction_data['external_package_id']);
	}

	unset($user_data['pass']);
	unset($user_data['pass_bill']);
	unset($user_data['temp_pass']);
	$_SESSION['user_info']=$user_data;

	if (strtotime($user_data['last_online_date'])>0 && strtotime($user_data['last_online_date'])>strtotime($user_data['last_login_date']))
	{
		$sess_duration=strtotime($user_data['last_online_date'])-strtotime($user_data['last_login_date']);
		$sess_duration_cnt=intval($user_data['avg_sess_duration_count'])+1;
		$sess_duration=floor((intval($sess_duration)+intval($user_data['avg_sess_duration'])*intval($user_data['avg_sess_duration_count']))/$sess_duration_cnt);
	} else {
		$sess_duration=$user_data['avg_sess_duration'];
		$sess_duration_cnt=$user_data['avg_sess_duration_count'];
	}

	$remember_me_inc='';
	if ($remember_for_days>0)
	{
		$rnd=mt_rand(10000000,99999999);
		$key=md5($config['installation_id'].$_SESSION['user_id'].$rnd);
		$days=intval($remember_for_days);
		$remember_me_inc=", remember_me_key='$key', remember_me_valid_for=DATE_ADD('".date("Y-m-d H:i:s")."', INTERVAL $days DAY) ";

		$domain=str_replace("www.","",$_SERVER['HTTP_HOST']);
		setcookie("kt_member",$key,time()+86400*$days,"/",".$domain");
	}

	sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+?, logins_count=logins_count+1, last_login_date=?, last_online_date=?, avg_sess_duration=?, avg_sess_duration_count=? $remember_me_inc where user_id=?",$login_award_tokens,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),intval($sess_duration),intval($sess_duration_cnt),$_SESSION['user_id']);
}

function get_block_version($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version_file=md5("{$prefix}_{$id}_{$user_id}");
	if (!is_file("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"))
	{
		$version_file=md5("{$prefix}_{$dir}_{$user_id}");
	}
	return intval(@file_get_contents("$config[project_path]/admin/data/engine/$path/$version_file[0]$version_file[1]/$version_file.dat"));
}

function inc_block_version($path,$prefix,$id,$dir,$user_id=0)
{
	global $config;

	$version=get_block_version($path,$prefix,$id,$dir,$user_id)+1;

	if (!is_dir("$config[project_path]/admin/data/engine/$path")) {mkdir("$config[project_path]/admin/data/engine/$path",0777);chmod("$config[project_path]/admin/data/engine/$path",0777);}
	if (intval($id)>0)
	{
		$version_file1=md5("{$prefix}_{$id}_{$user_id}");
		if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]",0777);chmod("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file1[0]$version_file1[1]/$version_file1.dat","$version",LOCK_EX);
	}
	if ($dir!='')
	{
		$version_file2=md5("{$prefix}_{$dir}_{$user_id}");
		if (!is_dir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]")) {mkdir("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]",0777);chmod("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]",0777);}
		file_put_contents("$config[project_path]/admin/data/engine/$path/$version_file2[0]$version_file2[1]/$version_file2.dat","$version",LOCK_EX);
	}
}

function async_set_request_content_type()
{
	if ($_REQUEST['format']=='json')
	{
		header("Content-type: application/json");
	} else {
		header("Content-type: text/xml");
	}
}

function async_return_request_status($errors = null, $redirect = null, $success_data = null)
{
	global $lang, $plugin_extensions;

	async_set_request_content_type();

	if (!isset($errors) || count($errors)==0)
	{
		foreach ($plugin_extensions as $plugin_extension)
		{
			$plugin_function = "{$plugin_extension}PostAsyncRequest";
			if (function_exists($plugin_function))
			{
				$plugin_function();
			}
		}

		if ($_REQUEST['format']=='json')
		{
			$json=array('status'=>'success');
			if ($redirect)
			{
				$json['redirect']=$redirect;
			}
			if (is_array($success_data))
			{
				$json['data']=$success_data;
			}
			echo json_encode($json);
		} else {
			if (is_array($success_data))
			{
				echo "<success>";
				foreach ($success_data as $k=>$v)
				{
					echo "<$k>$v</$k>";
				}
				echo "</success>";
			} else {
				echo '<success/>';
			}
		}
	} else {
		if ($redirect)
		{
			$xml="<failure redirect=\"$redirect\">";
		} else {
			$xml='<failure>';
		}
		$json=array('status'=>'failure');
		foreach ($errors as $error)
		{
			$json_error=array('code'=>$error['error_code']);
			$xml.="<error type=\"$error[error_code]\"";
			if ($error['error_field_name']!='')
			{
				$json_error['field']=$error['error_field_name'];
				$xml.=" field=\"$error[error_field_name]\"";
			}
			if ($error['block']!='')
			{
				$json_error['block']=$error['block'];
				$xml.=" block=\"$error[block]\"";
			}
			if (is_array($error['error_details']) && count($error['error_details'])>0)
			{
				$json_error['details']=$error['error_details'];
			}
			if ($error['error_field_code']!='')
			{
				$xml.=">$error[error_field_code]</error>";
			} else {
				$xml.="/>";
			}

			if (isset($lang))
			{
				$error_code=$error['error_code'];
				if ($error['error_field_name']!='')
				{
					$error_code=$error['error_field_name']."_".$error['error_code'];
				}
				$error_text='';
				if ($error['message']!='')
				{
					$error_text=$error['message'];
				}
				if ($error_text=='' && $error['block']!='')
				{
					$error_text=$lang['validation'][$error['block']][$error_code];
				}
				if ($error_text=='')
				{
					$error_text=$lang['validation']['common'][$error_code];
				}
				if ($error_text=='')
				{
					$error_text=$lang['validation']['common'][$error['error_code']];
				}
				if ($error_text=='')
				{
					$error_text=str_replace("%1%",$error['error_code'],$lang["validation"]["common"]["unknown_error"]);
				}
				if ($error_text!='')
				{
					if (is_array($error['error_details']) && count($error['error_details'])>0)
					{
						for ($i=1;$i<=count($error['error_details']);$i++)
						{
							$error_text=str_replace("%$i%",$error['error_details'][$i-1],$error_text);
						}
					}
					$json_error['message']=$error_text;
				}
			}

			$json['errors'][]=$json_error;
		}
		$xml.='</failure>';

		if ($_REQUEST['format']=='json')
		{
			echo json_encode($json);
		} else {
			echo $xml;
		}
	}
	die;
}


