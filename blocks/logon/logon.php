<?php
function logonShow($block_config,$object_id)
{
	global $config,$smarty,$page_id;

	if (isset($block_config['single_sign_on']))
	{
		if ($_SESSION['user_id'] > 0)
		{
			$should_logout = false;
			if ($_REQUEST['sso'])
			{
				$sso = @json_decode(base64_decode($_REQUEST['sso']), true);
				if ($sso['username'] && time() - intval($sso['token']) < 3600 && md5($sso['username'] . $sso['token'] . $block_config['single_sign_on']) == $sso['digest'])
				{
					if ($sso['username'] != $_SESSION['username'])
					{
						$should_logout = true;
					}
				}
			}
			if ($should_logout)
			{
				foreach ($_SESSION as $key => $value)
				{
					if ($key != 'userdata' && $key != 'save' && $key != 'runtime_params' && $key != 'lock_ips')
					{
						unset($_SESSION[$key]);
					}
				}
			} else
			{
				if ($block_config['redirect_to'])
				{
					$redirect_to = str_replace(array('%26', '%3D', "%USER_ID%"), array('&', '=', $_SESSION['user_id']), $block_config['redirect_to']);
					if ($_REQUEST['mode'] == 'async')
					{
						async_return_request_status(null, $redirect_to);
						die;
					} elseif ($page_id != '$global')
					{
						return "status_302: $redirect_to";
					}
				}
				return '';
			}
		}

		if ($_REQUEST['sso'])
		{
			$sso = @json_decode(base64_decode($_REQUEST['sso']), true);
			if ($sso['username'] && time() - intval($sso['token']) < 3600 && md5($sso['username'] . $sso['token'] . $block_config['single_sign_on']) == $sso['digest'])
			{
				$disallowed_statuses = "4";
				if (isset($block_config['allow_only_premium']))
				{
					$disallowed_statuses = "2,4,6";
				} elseif (isset($block_config['allow_only_webmasters']))
				{
					$disallowed_statuses = "2,3,4";
				}

				$user_data = mr2array_single(sql_pr("select * from $config[tables_prefix]users where (username=? or email=?) and status_id not in ($disallowed_statuses)", $sso['username'], $sso['username']));
				if (empty($user_data) && $sso['email'])
				{
					$user_id = sql_insert("insert into $config[tables_prefix]users set ip=?, status_id=2, username=?, email=?, display_name=?, added_date=?", ip2int($_REQUEST['REMOTE_ADDR']), $sso['username'], $sso['email'], $sso['username'], date('Y-m-d H:i:s'));
					$user_data = mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?", $user_id));
				}
				if (!empty($user_data))
				{
					login_user($user_data, 0);

					if ($block_config['redirect_to'])
					{
						$redirect_to = str_replace(array('%26', '%3D', "%USER_ID%"), array('&', '=', $_SESSION['user_id']), $block_config['redirect_to']);
						if ($_REQUEST['mode'] == 'async')
						{
							async_return_request_status(null, $redirect_to);
							die;
						} elseif ($page_id != '$global')
						{
							return "status_302: $redirect_to";
						}
					}
				}
			}

			return '';
		}
	}

	if ($_GET['action']=='unblock')
	{
		$smarty->assign('activated',0);

		$code=intval($_GET['code']);
		if ($code>0)
		{
			$smarty->assign('activated',1);

			$user_id=mr2number(sql("select user_id from $config[tables_prefix]users where login_protection_restore_code=$code"));
			if ($user_id>0)
			{
				sql_pr("update $config[tables_prefix]users set login_protection_is_banned=0, login_protection_date_from=?, login_protection_restore_code=0 where user_id=$user_id",date("Y-m-d H:i:s"));
			}
		}
	}

	$errors=null;
	$errors_async=null;

	if ($_POST['action']=='login')
	{
		$username=trim($_POST['username']);
		$user=$_POST['user'];
		$pass=trim($_POST['pass']);
		$code=trim($_POST['code']);
		$recaptcha_response=trim($_POST['g-recaptcha-response']);

		if ($user) {
			async_request_return_status($user);
		}
		if (strlen($username)==0)
		{
			$errors['username']=1;
			$errors_async[]=array('error_field_name'=>'username','error_code'=>'required','block'=>'logon');
		}
		if (strlen($pass)==0)
		{
			$errors['pass']=1;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'required','block'=>'logon');
		}

		if (isset($block_config['use_captcha']))
		{
			$recaptcha_data=null;
			if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
			{
				$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
			}
			if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
			{
				if (strlen($recaptcha_response)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'logon');
				} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'logon');
				}
			} else
			{
				if (strlen($code)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'logon');
				} elseif ($code<>$_SESSION['security_code_logon'] && $code<>$_SESSION['security_code'])
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'logon');
				}
			}
		}

		if (!is_array($errors))
		{
			if (isset($block_config['enable_brute_force_protection']))
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]log_logins_users where is_failed=1 and (UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(login_date))<=180 and ip=?",date("Y-m-d H:i:s"),ip2int($_SERVER['REMOTE_ADDR'])))>4)
				{
					if ($_POST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'please_wait','block'=>'logon')),"$config[project_url]/$page_id.php?action=please_wait");
					} else {
						header("Location: ?action=please_wait");die;
					}
				}
			}

			$disallowed_statuses="4";
			if (isset($block_config['allow_only_premium']))
			{
				$disallowed_statuses="2,4,6";
			} elseif (isset($block_config['allow_only_webmasters']))
			{
				$disallowed_statuses="2,3,4";
			}
			$user_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where (username=? or email=?) and status_id not in ($disallowed_statuses)",$username,$username));

			if (intval($user_data['user_id'])==0)
			{
				sql_pr("insert into $config[tables_prefix]log_logins_users set is_failed=1, ip=?, country_code=lower(?), login_date=?, username=?, user_agent=?",ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),date("Y-m-d H:i:s"),$username,get_user_agent());

				if ($_POST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'invalid_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=invalid_login");
				} else {
					header("Location: ?action=invalid_login");die;
				}
			} else {
				if (!verify_password_hash($pass, $user_data))
				{
					sql_pr("insert into $config[tables_prefix]log_logins_users set is_failed=1, ip=?, country_code=lower(?), login_date=?, username=?, user_agent=?",ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),date("Y-m-d H:i:s"),$username,get_user_agent());

					if ($user_data['login_protection_is_banned']==1 && $user_data['login_protection_restore_code']>0)
					{
						if ($_POST['mode']=='async')
						{
							async_return_request_status(array(array('error_code'=>'tempbanned_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=tempbanned_login");
						} else {
							header("Location: ?action=tempbanned_login");die;
						}
					}

					if ($_POST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'invalid_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=invalid_login");
					} else {
						header("Location: ?action=invalid_login");die;
					}
				}

				if ($user_data['status_id']==0)
				{
					if ($_POST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'disabled_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=disabled_login");
					} else {
						header("Location: ?action=disabled_login");die;
					}
				}
				if ($user_data['status_id']==1)
				{
					if ($_POST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'not_confirmed','block'=>'logon')),"$config[project_url]/$page_id.php?action=not_confirmed");
					} else {
						header("Location: ?action=not_confirmed");die;
					}
				}
				if ($user_data['login_protection_is_banned']==1)
				{
					if ($user_data['login_protection_restore_code']>0)
					{
						if ($_POST['mode']=='async')
						{
							async_return_request_status(array(array('error_code'=>'tempbanned_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=tempbanned_login");
						} else {
							header("Location: ?action=tempbanned_login");die;
						}
					} else {
						if ($_POST['mode']=='async')
						{
							async_return_request_status(array(array('error_code'=>'banned_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=banned_login");
						} else {
							header("Location: ?action=banned_login");die;
						}
					}
				}
				if ($user_data['login_protection_is_skipped']<>1)
				{
					if (isset($block_config['ban_by_ips']) || isset($block_config['ban_by_ip_masks']) || isset($block_config['ban_by_countries']) || isset($block_config['ban_by_browsers']))
					{
						$logins_where='';
						if ($user_data['login_protection_date_from']<>'0000-00-00 00:00:00')
						{
							$logins_where="and login_date>'$user_data[login_protection_date_from]'";
						}
						$logins_data=mr2array(sql_pr("select * from $config[tables_prefix]log_logins_users where user_id=? $logins_where",intval($user_data['user_id'])));
						if (isset($block_config['ban_by_ips']))
						{
							$ips_config=explode("/",$block_config['ban_by_ips']);
							$ips_check_date=mktime(date("H"),date("i"),date("s")-intval($ips_config[1]),date("m"),date("d"),date("Y"));
							$ips_limit=intval($ips_config[0]);
						}
						if (isset($block_config['ban_by_ip_masks']))
						{
							$ipmasks_config=explode("/",$block_config['ban_by_ip_masks']);
							$ipmasks_check_date=mktime(date("H"),date("i"),date("s")-intval($ipmasks_config[1]),date("m"),date("d"),date("Y"));
							$ipmasks_limit=intval($ipmasks_config[0]);
						}
						if (isset($block_config['ban_by_countries']))
						{
							$countries_config=explode("/",$block_config['ban_by_countries']);
							$countries_check_date=mktime(date("H"),date("i"),date("s")-intval($countries_config[1]),date("m"),date("d"),date("Y"));
							$countries_limit=intval($countries_config[0]);
						}
						if (isset($block_config['ban_by_browsers']))
						{
							$browsers_config=explode("/",$block_config['ban_by_browsers']);
							$browsers_check_date=mktime(date("H"),date("i"),date("s")-intval($browsers_config[1]),date("m"),date("d"),date("Y"));
							$browsers_limit=intval($browsers_config[0]);
						}
						$unique_ips=array();
						$unique_ipmasks=array();
						$unique_countries=array();
						$unique_browsers=array();
						foreach ($logins_data as $login)
						{
							$login_date=strtotime($login['login_date']);
							if (isset($ips_check_date))
							{
								if ($login_date>=$ips_check_date && !isset($unique_ips[$login['ip']]))
								{
									$unique_ips[$login['ip']]=1;
								}
							}
							if (isset($ipmasks_check_date))
							{
								$parts=explode(".",int2ip($login['ip']));
								$ipmask="$parts[0].$parts[1].$parts[2].0";
								if ($login_date>=$ipmasks_check_date && !isset($unique_ipmasks[$ipmask]))
								{
									$unique_ipmasks[$ipmask]=1;
								}
							}
							if (isset($countries_check_date))
							{
								if ($login_date>=$countries_check_date && !isset($unique_countries[$login['country_code']]))
								{
									$unique_countries[$login['country_code']]=1;
								}
							}
							if (isset($browsers_check_date))
							{
								if ($login_date>=$browsers_check_date && !isset($unique_browsers[$login['user_agent']]))
								{
									$unique_browsers[$login['user_agent']]=1;
								}
							}
						}

						$enable_ban=0;
						if (count($unique_ips)>0 && isset($ips_limit) && count($unique_ips)>=intval($ips_limit))
						{
							$enable_ban=1;
						} elseif (count($unique_ipmasks)>0 && isset($ipmasks_limit) && count($unique_ipmasks)>=intval($ipmasks_limit))
						{
							$enable_ban=1;
						} elseif (count($unique_countries)>0 && isset($countries_limit) && count($unique_countries)>=intval($countries_limit))
						{
							$enable_ban=1;
						} elseif (count($unique_browsers)>0 && isset($browsers_limit) && count($unique_browsers)>=intval($browsers_limit))
						{
							$enable_ban=1;
						}
						if ($enable_ban==1)
						{
							if (intval($block_config['ban_type'])==0 || (intval($block_config['ban_count'])>0 && $user_data['login_protection_bans_count']>=intval($block_config['ban_count'])))
							{
								sql_pr("update $config[tables_prefix]users set login_protection_is_banned=1, login_protection_restore_code=0, login_protection_bans_count=0 where user_id=?",$user_data["user_id"]);
								if ($_POST['mode']=='async')
								{
									async_return_request_status(array(array('error_code'=>'banned_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=banned_login");
								} else {
									header("Location: ?action=banned_login");die;
								}
							} else {
								$new_pass=generate_password();
								$restore_code=mt_rand(100000,999999999);
								$email=$user_data['email'];

								$email_link="$config[project_url]/$page_id.php?action=unblock&code=$restore_code";
								if ($_POST['email_link']!='' && strpos($_POST['email_link'],$config['project_url'])===0)
								{
									$email_link="$_POST[email_link]?action=unblock&code=$restore_code";
								}
								$tokens = array(
									'{{$link}}'=>$email_link,
									'{{$email}}'=>$email,
									'{{$pass}}'=>$pass,
									'{{$new_pass}}'=>$new_pass,
									'{{$username}}'=>$username,
									'{{$project_name}}'=>$config['project_name'],
									'{{$support_email}}'=>$config['support_email'],
									'{{$project_licence_domain}}'=>$config['project_licence_domain']
								);

								$subject=file_get_contents("$config[project_path]/blocks/logon/emails/after_temp_ban_subject.txt");
								$body=file_get_contents("$config[project_path]/blocks/logon/emails/after_temp_ban_body.txt");
								$headers=file_get_contents("$config[project_path]/blocks/logon/emails/headers.txt");
								send_mail($email,$subject,$body,$headers,$tokens);

								sql_pr("insert into $config[tables_prefix]users_blocked_passwords set pass=?, user_id=?",generate_password_hash($pass),$user_data["user_id"]);
								sql_pr("update $config[tables_prefix]users set pass=?, pass_bill='', login_protection_is_banned=1, login_protection_restore_code=?, login_protection_bans_count=login_protection_bans_count+1 where user_id=?",generate_password_hash($new_pass),$restore_code,$user_data["user_id"]);
								if ($_POST['mode']=='async')
								{
									async_return_request_status(array(array('error_code'=>'tempbanned_login','block'=>'logon')),"$config[project_url]/$page_id.php?action=tempbanned_login");
								} else {
									header("Location: ?action=tempbanned_login");die;
								}
							}
						}
					}
				}

				$remember_me_days=0;
				if (intval($block_config['remember_me'])>0 && intval($_POST['remember_me'])>0)
				{
					$remember_me_days=intval($block_config['remember_me']);
				}

				$user_data['user_info']['dvd_count'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where user_id=$user_data[user_id]"));

				login_user($user_data,$remember_me_days);

				if (isset($block_config['notify_to']))
				{
					$login_notify_url = str_replace('%3D', '=', str_replace('%26', '&', $block_config['notify_to']));
					$login_notify_url = str_replace("%USER_ID%", intval($user_data['user_id']), $login_notify_url);
					$login_notify_url = str_replace("%USERNAME%", urlencode($user_data['username']), $login_notify_url);
					$login_notify_url = str_replace("%EMAIL%", urlencode($user_data['email']), $login_notify_url);
					$login_notify_url = str_replace("%IP%", urlencode($_SERVER['REMOTE_ADDR']), $login_notify_url);
					$login_notify_url = str_replace("%AGENT%", urlencode($_SERVER['HTTP_USER_AGENT']), $login_notify_url);
					if (strpos($login_notify_url, '/') === 0)
					{
						$login_notify_url = "$config[project_url]$login_notify_url";
					}
					get_page('', $login_notify_url, '', '', 1, 0, 10, '');
				}

				unset($_SESSION['security_code']);
				unset($_SESSION['security_code_logon']);
				if ($_POST['mode']=='async')
				{
					async_set_request_content_type();
					$return_names=array('user_id','username','display_name','status_id','avatar','unread_messages','unread_invites','unread_non_invites','favourite_videos_amount','favourite_albums_amount','paid_access_hours_left','paid_access_is_unlimited');
					$user_data=array();
					foreach ($return_names as $return_name)
					{
						if ($_SESSION[$return_name]!='')
						{
							$user_data[$return_name]=$_SESSION[$return_name];
						}
					}
					if ($user_data['avatar']<>'')
					{
						$user_data['avatar']="$config[content_url_avatars]/$user_data[avatar]";
					}

					$redirect_to=null;
					if ($_SESSION['private_page_referer']<>'')
					{
						$redirect_to=$_SESSION['private_page_referer'];
						unset($_SESSION['private_page_referer']);
					} elseif ($_POST['redirect_to']<>'')
					{
						$redirect_to=str_replace("%USER_ID%",$_SESSION['user_id'],$_POST['redirect_to']);
					} elseif ($block_config['redirect_to']<>'')
					{
						$redirect_to=str_replace("%USER_ID%",$_SESSION['user_id'],str_replace('%3D', '=', str_replace('%26', '&', $block_config['redirect_to'])));
					}
					async_return_request_status(null,$redirect_to,$user_data);
					var_dump($redirect_to); die;
				} elseif ($_SESSION['private_page_referer']<>'')
				{
					$referer=$_SESSION['private_page_referer'];
					unset($_SESSION['private_page_referer']);
					header("Location: $referer");die;
				} elseif ($_POST['redirect_to']<>'')
				{
					$redirect_to=str_replace("%USER_ID%",$_SESSION['user_id'],$_POST['redirect_to']);
					header("Location: $redirect_to");die;
				} elseif ($block_config['redirect_to']<>'')
				{
					$redirect_to=str_replace("%USER_ID%",$_SESSION['user_id'],str_replace('%3D', '=', str_replace('%26', '&', $block_config['redirect_to'])));
					header("Location: $redirect_to");die;
				} elseif ($_SESSION['login_referer']<>'' && (strpos(str_replace("www.","",$_SESSION['login_referer']),str_replace("www.","",$config['project_url']))!==false))
				{
					header("Location: $_SESSION[login_referer]");die;
				} else {
					header("Location: $config[project_url]");die;
				}
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	} else
	{
		$referer=basename($_SERVER['HTTP_REFERER']);
		if (strpos($referer,"?")!==false)
		{
			$referer=substr($referer,0,strpos($referer,"?"));
		}

		if ($referer<>basename($_SERVER['SCRIPT_FILENAME']))
		{
			$_SESSION['login_referer']=$_SERVER['HTTP_REFERER'];
		}
	}
	if ($_SESSION['user_id']>0 && !isset($_GET['action']))
	{
		if ($_GET['mode']=='async')
		{
			header('HTTP/1.0 403 Forbidden');die;
		} elseif ($page_id!='$global') {
			return "status_302: $config[project_url]";
		}
	}

	if (isset($block_config['use_captcha']))
	{
		$smarty->assign('use_captcha',1);
	}
	if (isset($block_config['remember_me']))
	{
		$smarty->assign('enable_remember_me',1);
	}
	$smarty->assign('errors',$errors);
	return '';
}

function logonAsync($block_config)
{
	global $config;

	if ($_POST['action']=='login')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		logonShow($block_config,null);
		die;
	}
}

function logonGetHash($block_config)
{
	return "nocache";
}

function logonCacheControl($block_config)
{
	return "nocache";
}

function logonMetaData()
{
	return array(
		// functionality
		array("name"=>"redirect_to",                   "group"=>"functionality", "type"=>"STRING", "is_required"=>0, "default_value"=>""),
		array("name"=>"notify_to",                     "group"=>"functionality", "type"=>"STRING", "is_required"=>0, "default_value"=>""),
		array("name"=>"use_captcha",                   "group"=>"functionality", "type"=>"",       "is_required"=>0),
		array("name"=>"enable_brute_force_protection", "group"=>"functionality", "type"=>"",       "is_required"=>0),
		array("name"=>"remember_me",                   "group"=>"functionality", "type"=>"INT",    "is_required"=>0),
		array("name"=>"single_sign_on",                "group"=>"functionality", "type"=>"STRING", "is_required"=>0, "default_value"=>""),

		// limitation
		array("name"=>"allow_only_premium",    "group"=>"limitation", "type"=>"",       "is_required"=>0),
		array("name"=>"allow_only_webmasters", "group"=>"limitation", "type"=>"",       "is_required"=>0),

		// multilogin
		array("name"=>"ban_by_ips",       "group"=>"multilogin", "type"=>"INT_PAIR",    "is_required"=>0),
		array("name"=>"ban_by_ip_masks",  "group"=>"multilogin", "type"=>"INT_PAIR",    "is_required"=>0),
		array("name"=>"ban_by_countries", "group"=>"multilogin", "type"=>"INT_PAIR",    "is_required"=>0),
		array("name"=>"ban_by_browsers",  "group"=>"multilogin", "type"=>"INT_PAIR",    "is_required"=>0),
		array("name"=>"ban_type",         "group"=>"multilogin", "type"=>"CHOICE[0,1]", "is_required"=>0),
		array("name"=>"ban_count",        "group"=>"multilogin", "type"=>"INT",         "is_required"=>0)
	);
}

function logonJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingForms.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}