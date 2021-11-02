<?php

function signupShow($block_config,$object_id)
{
	global $config,$smarty,$regexp_check_alpha_numeric,$regexp_check_email,$page_id;

	$errors=null;
	$errors_async=null;

	$options=get_options(array('USER_AVATAR_SIZE','USER_AVATAR_TYPE','USER_COVER_SIZE','USER_COVER_TYPE','USER_COVER_OPTION'));
	$custom_text_fields_count = 10;

	if ($_GET['action'] == 'confirm_restore_pass')
	{
		$smarty->assign('activated', 0);

		$code = trim($_GET['code']);
		if ($code)
		{
			$confirm_code = mr2array_single(sql_pr("select * from $config[tables_prefix]users_confirm_codes where confirm_code=?", $code));
			if ($confirm_code['type_id'] == 3)
			{
				$smarty->assign('activated', 1);

				sql_pr("update $config[tables_prefix]users set pass=temp_pass, pass_bill='', temp_pass='' where user_id=? and temp_pass!=''", $confirm_code['user_id']);
				sql_pr("update $config[tables_prefix]users set status_id=2 where status_id=1 and user_id=?", $confirm_code['user_id']);
			}
		}

		return '';
	} elseif ($_GET['action'] == 'confirm')
	{
		$smarty->assign('activated', 0);

		$code = trim($_GET['code']);
		if ($code)
		{
			$confirm_code = mr2array_single(sql_pr("select * from $config[tables_prefix]users_confirm_codes where confirm_code=?", $code));
			if ($confirm_code['type_id'] == 1)
			{
				$smarty->assign('activated', 1);
				sql_pr("update $config[tables_prefix]users set status_id=2 where status_id=1 and user_id=?", $confirm_code['user_id']);
			}
		}

		return '';
	} elseif ($_POST['action']=='restore_password')
	{
		$email=trim($_POST['email']);
		$code=trim($_POST['code']);
		$recaptcha_response=trim($_POST['g-recaptcha-response']);

		if (strlen($email)==0)
		{
			$errors['email']=1;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'required','block'=>'signup');
		} elseif (!preg_match($regexp_check_email,$email))
		{
			$errors['email']=2;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'invalid','block'=>'signup');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=?",$email))==0)
		{
			$errors['email']=3;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'doesnt_exist','block'=>'signup');
		}

		if (!isset($block_config['disable_captcha']))
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
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
				} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
				}
			} else
			{
				if (strlen($code)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
				} elseif ($code<>$_SESSION['security_code_signup'] && $code<>$_SESSION['security_code'])
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
				}
			}
		}

		if (!is_array($errors))
		{
			$data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where email=? order by user_id desc limit 1",$email));
			$username=$data['username'];
			$pass=strtolower(generate_password());

			$confirm_code=generate_confirm_code();

			sql_pr("insert into $config[tables_prefix]users_confirm_codes set confirm_code=?, user_id=?, added_date=?, type_id=3",$confirm_code,$data['user_id'],date("Y-m-d H:i:s"));
			sql_pr("update $config[tables_prefix]users set temp_pass=? where user_id=?",generate_password_hash($pass),$data['user_id']);

			$email_link="$config[project_url]/$page_id.php?action=confirm_restore_pass&code=$confirm_code";
			if ($_POST['email_link']!='' && strpos($_POST['email_link'],$config['project_url'])===0)
			{
				$email_link="$_POST[email_link]?action=confirm_restore_pass&code=$confirm_code";
			}
			$tokens = array(
				'{{$link}}'=>$email_link,
				'{{$email}}'=>$email,
				'{{$pass}}'=>$pass,
				'{{$username}}'=>$username,
				'{{$project_name}}'=>$config['project_name'],
				'{{$support_email}}'=>$config['support_email'],
				'{{$project_licence_domain}}'=>$config['project_licence_domain']
			);
			$subject=file_get_contents("$config[project_path]/blocks/signup/emails/after_restore_password_subject.txt");
			$body=file_get_contents("$config[project_path]/blocks/signup/emails/after_restore_password_body.txt");
			$headers=file_get_contents("$config[project_path]/blocks/signup/emails/headers.txt");
			send_mail($email,$subject,$body,$headers,$tokens);

			unset($_SESSION['security_code']);
			unset($_SESSION['security_code_signup']);
			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=restore_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}

		$smarty->assign('errors',$errors);
		return '';
	} elseif ($_POST['action']=='resend_confirmation')
	{
		$email=trim($_POST['email']);
		$code=trim($_POST['code']);
		$recaptcha_response=trim($_POST['g-recaptcha-response']);

		if (strlen($email)==0)
		{
			$errors['email']=1;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'required','block'=>'signup');
		} elseif (!preg_match($regexp_check_email,$email))
		{
			$errors['email']=2;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'invalid','block'=>'signup');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=?",$email))==0)
		{
			$errors['email']=3;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'doesnt_exist','block'=>'signup');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=? and status_id=1",$email))==0)
		{
			$errors['email']=4;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'already_confirmed','block'=>'signup');
		}

		if (!isset($block_config['disable_captcha']))
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
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
				} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
				}
			} else
			{
				if (strlen($code)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
				} elseif ($code<>$_SESSION['security_code_signup'] && $code<>$_SESSION['security_code'])
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
				}
			}
		}

		if (!is_array($errors))
		{
			$data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where email=? order by user_id desc limit 1",$email));
			$username=$data['username'];
			$confirm_code=generate_confirm_code();

			sql_pr("insert into $config[tables_prefix]users_confirm_codes set confirm_code=?, user_id=?, added_date=?, type_id=1",$confirm_code,$data['user_id'],date("Y-m-d H:i:s"));

			$email_link="$config[project_url]/$page_id.php?action=confirm&code=$confirm_code";
			if ($_POST['email_link']!='' && strpos($_POST['email_link'],$config['project_url'])===0)
			{
				$email_link="$_POST[email_link]?action=confirm&code=$confirm_code";
			}
			$tokens = array(
				'{{$link}}'=>$email_link,
				'{{$email}}'=>$email,
				'{{$username}}'=>$username,
				'{{$project_name}}'=>$config['project_name'],
				'{{$support_email}}'=>$config['support_email'],
				'{{$project_licence_domain}}'=>$config['project_licence_domain']
			);
			$subject=file_get_contents("$config[project_path]/blocks/signup/emails/after_resend_confirmation_subject.txt");
			$body=file_get_contents("$config[project_path]/blocks/signup/emails/after_resend_confirmation_body.txt");
			$headers=file_get_contents("$config[project_path]/blocks/signup/emails/headers.txt");
			send_mail($email,$subject,$body,$headers,$tokens);

			unset($_SESSION['security_code']);
			unset($_SESSION['security_code_signup']);
			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=resend_confirmation_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}

		$smarty->assign('errors',$errors);
		return '';
	} elseif ($_POST['action']=='signup')
	{
		$username=trim($_POST['username']);
		$display_name=trim(process_blocked_words($_POST['display_name'],true));
		$pass=trim($_POST['pass']);
		$pass2=trim($_POST['pass2']);
		$email=trim($_POST['email']);
		$code=trim($_POST['code']);
		$recaptcha_response=trim($_POST['g-recaptcha-response']);
		$city=trim(process_blocked_words($_POST['city'],true));
		$country_id=intval($_POST['country_id']);
		$gender_id=intval($_POST['gender_id']);
		$relationship_status_id=intval($_POST['relationship_status_id']);
		$orientation_id=intval($_POST['orientation_id']);

		$card_package_id=intval($_POST['card_package_id']);
		$sms_package_id=intval($_POST['sms_package_id']);
		$sms_passcode=trim($_POST['sms_passcode']);
		$access_code=trim($_POST['access_code']);

		if (strlen($username)==0)
		{
			$errors['username']=1;
			$errors_async[]=array('error_field_name'=>'username','error_code'=>'required','block'=>'signup');
		} elseif (strlen($username)<3)
		{
			$errors['username']=2;
			$errors_async[]=array('error_field_name'=>'username','error_code'=>'minimum','error_details'=>array(3),'block'=>'signup');
		} elseif (!preg_match($regexp_check_alpha_numeric,$username))
		{
			$errors['username']=3;
			$errors_async[]=array('error_field_name'=>'username','error_code'=>'characters','block'=>'signup');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$username))>0)
		{
			$errors['username']=4;
			$errors_async[]=array('error_field_name'=>'username','error_code'=>'exists','block'=>'signup');
		}

		if (strlen($pass)==0)
		{
			$errors['pass']=1;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'required','block'=>'signup');
		} elseif (strlen($pass)<5)
		{
			$errors['pass']=2;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'minimum','error_details'=>array(5),'block'=>'signup');
		}

		if (strlen($pass2)==0)
		{
			$errors['pass2']=1;
			$errors_async[]=array('error_field_name'=>'pass2','error_code'=>'required','block'=>'signup');
		} elseif ($pass!=$pass2)
		{
			$errors['pass2']=2;
			$errors_async[]=array('error_field_name'=>'pass2','error_code'=>'invalid','block'=>'signup');
		}

		if (strlen($email)==0)
		{
			$errors['email']=1;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'required','block'=>'signup');
		} elseif (!preg_match($regexp_check_email,$email))
		{
			$errors['email']=2;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'invalid','block'=>'signup');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=?",$email))>0)
		{
			$errors['email']=3;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'exists','block'=>'signup');
		}

		if (isset($block_config['require_display_name']) && $display_name=='')
		{
			$errors['display_name']=1;
			$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'required','block'=>'signup');
		} elseif ($display_name!='')
		{
			if (strlen($display_name)<3)
			{
				$errors['display_name']=2;
				$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'minimum','error_details'=>array(3),'block'=>'signup');
			} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where display_name=?",$display_name))>0)
			{
				$errors['display_name']=3;
				$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'exists','block'=>'signup');
			}
		}

		if (isset($block_config['require_country']) && $country_id==0)
		{
			$errors['country_id']=1;
			$errors_async[]=array('error_field_name'=>'country_id','error_code'=>'required','block'=>'signup');
		}

		if (isset($block_config['require_city']) && $city=='')
		{
			$errors['city']=1;
			$errors_async[]=array('error_field_name'=>'city','error_code'=>'required','block'=>'signup');
		}

		if (isset($block_config['require_gender']) && $gender_id==0)
		{
			$errors['gender_id']=1;
			$errors_async[]=array('error_field_name'=>'gender_id','error_code'=>'required','block'=>'signup');
		}

		if (isset($block_config['require_orientation']) && $orientation_id==0)
		{
			$errors['orientation_id']=1;
			$errors_async[]=array('error_field_name'=>'orientation_id','error_code'=>'required','block'=>'signup');
		}

		if (isset($block_config['require_relationship_status']) && $relationship_status_id==0)
		{
			$errors['relationship_status_id']=1;
			$errors_async[]=array('error_field_name'=>'relationship_status_id','error_code'=>'required','block'=>'signup');
		}

		$birth_date=intval($_POST['birth_date_Year'])."-".intval($_POST['birth_date_Month'])."-".intval($_POST['birth_date_Day']);
		$_POST['birth_date']=$birth_date;
		if (isset($block_config['require_birth_date']) && $birth_date=='0-0-0')
		{
			$errors['birth_date']=1;
			$errors_async[]=array('error_field_name'=>'birth_date','error_code'=>'required','block'=>'signup');
		} elseif ($birth_date<>'0-0-0')
		{
			if (intval($_POST['birth_date_Year']) < 1 || intval($_POST['birth_date_Month']) < 1 || intval($_POST['birth_date_Day']) < 1)
			{
				$errors['birth_date'] = 1;
				$errors_async[] = array('error_field_name' => 'birth_date', 'error_code' => 'invalid', 'block' => 'signup');
			} elseif (intval($config['min_user_age']) < 0)
			{
				$birth_date_time = strtotime($birth_date);
				$birth_date_min_time = strtotime(date('d') . '-' . date('m') . '-' . (intval(date('Y')) - abs(intval($config['min_user_age']))));
				if ($birth_date_time > $birth_date_min_time)
				{
					$errors['birth_date'] = 1;
					$errors_async[] = array('error_field_name' => 'birth_date', 'error_code' => 'min_age', 'block' => 'signup', 'error_details' => array(abs(intval($config['min_user_age']))));
				}
			}
		}

		if (isset($block_config['require_avatar']) && $_FILES['avatar']['tmp_name']=='')
		{
			$errors['avatar']=1;
			$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'required','block'=>'signup');
		} elseif ($_FILES['avatar']['tmp_name']<>'')
		{
			$avatar_ext=strtolower(end(explode(".",$_FILES['avatar']['name'])));
			if (!in_array($avatar_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['avatar']=1;
				$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'invalid_format','block'=>'signup');
			} else {
				$size=getimagesize($_FILES['avatar']["tmp_name"]);
				$allowed_size=explode("x",$options['USER_AVATAR_SIZE']);
				$allowed_size_option=$options['USER_AVATAR_TYPE'];

				$valid_size = true;
				switch ($allowed_size_option)
				{
					case 'need_size':
						if ($size[0] < $allowed_size[0] || $size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
					case 'max_size':
						if ($size[0] < $allowed_size[0] && $size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
					case 'max_width':
						if ($size[0] < $allowed_size[0])
						{
							$valid_size = false;
						}
						break;
					case 'max_height':
						if ($size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
				}

				if (!$valid_size)
				{
					$errors['avatar']=2;
					$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'invalid_size','error_details'=>array($options['USER_AVATAR_SIZE']),'block'=>'signup');
				}
			}
		}

		if (isset($block_config['require_cover']) && $_FILES['cover']['tmp_name']=='')
		{
			$errors['cover']=1;
			$errors_async[]=array('error_field_name'=>'cover','error_code'=>'required','block'=>'signup');
		} elseif ($_FILES['cover']['tmp_name']<>'')
		{
			$cover_ext=strtolower(end(explode(".",$_FILES['cover']['name'])));
			if (!in_array($cover_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover']=1;
				$errors_async[]=array('error_field_name'=>'cover','error_code'=>'invalid_format','block'=>'signup');
			} else {
				$size=getimagesize($_FILES['cover']["tmp_name"]);
				$allowed_size=explode("x",$options['USER_COVER_SIZE']);
				$allowed_size_option=$options['USER_COVER_TYPE'];

				$valid_size = true;
				switch ($allowed_size_option)
				{
					case 'need_size':
						if ($size[0] < $allowed_size[0] || $size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
					case 'max_size':
						if ($size[0] < $allowed_size[0] && $size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
					case 'max_width':
						if ($size[0] < $allowed_size[0])
						{
							$valid_size = false;
						}
						break;
					case 'max_height':
						if ($size[1] < $allowed_size[1])
						{
							$valid_size = false;
						}
						break;
				}

				if (!$valid_size)
				{
					$errors['cover']=2;
					$errors_async[]=array('error_field_name'=>'cover','error_code'=>'invalid_size','error_details'=>array($options['USER_COVER_SIZE']),'block'=>'signup');
				}
			}
		}

		for ($i = 1; $i <= $custom_text_fields_count; $i++)
		{
			if (isset($block_config["require_custom{$i}"]) && $_POST["custom{$i}"]=='')
			{
				$errors["custom{$i}"]=1;
				$errors_async[]=array('error_field_name'=>"custom{$i}",'error_code'=>'required','block'=>'signup');
			}
		}

		$payment_option=1;
		if (isset($block_config['enable_sms_payment']) || isset($block_config['enable_card_payment']))
		{
			$payment_option=intval($_POST['payment_option']);
			if ($payment_option==0)
			{
				$errors['payment_option']=1;
				$errors_async[]=array('error_field_name'=>'payment_option','error_code'=>'required','block'=>'signup');
			}
		}
		if (isset($block_config['disable_free_access']) && $payment_option==1)
		{
			$errors['payment_option']=1;
			$errors_async[]=array('error_field_name'=>'payment_option','error_code'=>'required','block'=>'signup');
		}

		if ($payment_option==1)
		{
			if (!isset($block_config['disable_captcha']))
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
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
					} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
					}
				} else
				{
					if (strlen($code)==0)
					{
						$errors['code']=1;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'signup');
					} elseif ($code<>$_SESSION['security_code_signup'] && $code<>$_SESSION['security_code'])
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'signup');
					}
				}
			}

			if ($access_code!='')
			{
				if (isset($block_config['enable_access_codes']))
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_transactions where status_id=4 and access_code=?",$access_code))==0)
					{
						$errors['access_code']=2;
						$errors_async[]=array('error_field_name'=>'access_code','error_code'=>'invalid','block'=>'signup');
					}
				} else
				{
					$errors['access_code']=2;
					$errors_async[]=array('error_field_name'=>'access_code','error_code'=>'invalid','block'=>'signup');
				}
			} elseif (isset($block_config['require_access_code']))
			{
				$errors['access_code']=1;
				$errors_async[]=array('error_field_name'=>'access_code','error_code'=>'required','block'=>'signup');
			}
		} elseif ($payment_option==2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]card_bill_packages where package_id=?",$card_package_id))==0)
			{
				$errors['card_package_id']=1;
				$errors_async[]=array('error_field_name'=>'card_package_id','error_code'=>'required','block'=>'signup');
			}
		} elseif ($payment_option==3)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]sms_bill_packages where package_id=?",$sms_package_id))==0)
			{
				$errors['sms_package_id']=1;
				$errors_async[]=array('error_field_name'=>'sms_package_id','error_code'=>'required','block'=>'signup');
			}

			if (strlen($sms_passcode)==0)
			{
				$errors['sms_passcode']=1;
				$errors_async[]=array('error_field_name'=>'sms_passcode','error_code'=>'required','block'=>'signup');
			} elseif (mr2number(sql_pr("select transaction_id from $config[tables_prefix]bill_transactions where access_code=? and user_id=0 and type_id in (1,10)",$sms_passcode))==0)
			{
				$errors['sms_passcode']=2;
				$errors_async[]=array('error_field_name'=>'sms_passcode','error_code'=>'invalid','block'=>'signup');
			}
		}

		if (!is_array($errors))
		{
			$memberzone_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));

			$status_id = 2;
			$tokens_purchased = 0;
			$tokens_awarded = intval($memberzone_data['AWARDS_SIGNUP']);
			$sms_transaction_id = 0;
			$access_code_transaction_id = 0;
			$referral_award = 0;

			if ($payment_option == 1)
			{
				if (intval($memberzone_data['AWARDS_REFERRAL_SIGNUP']) > 0)
				{
					$referral_award = intval($memberzone_data['AWARDS_REFERRAL_SIGNUP']);
					if (intval($memberzone_data['AWARDS_REFERRAL_SIGNUP_CONDITION']) > 0)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where added_date>? and ip=?", date("Y-m-d H:i:s", time() - 3600 * intval($memberzone_data['AWARDS_REFERRAL_SIGNUP_CONDITION'])), ip2int($_SERVER['REMOTE_ADDR']))) > 0)
						{
							$referral_award = 0;
						}
					}
				}
				if (isset($block_config['enable_access_codes']) && $access_code != '')
				{
					$access_code_transaction = mr2array_single(sql_pr("select * from $config[tables_prefix]bill_transactions where status_id=4 and access_code=?", $access_code));
					if ($access_code_transaction['transaction_id'] > 0)
					{
						$access_code_transaction_id = $access_code_transaction['transaction_id'];
						if ($access_code_transaction['tokens_granted'] > 0)
						{
							$status_id = 2;
							$tokens_purchased = $access_code_transaction['tokens_granted'];
						} else
						{
							$status_id = 3;
						}
						if ($access_code_transaction['access_code_referral_award'] > 0)
						{
							$referral_award = intval($access_code_transaction['access_code_referral_award']);
						}
					}
				} elseif (isset($block_config['use_confirm_email']))
				{
					$status_id = 1;
				}
			} elseif ($payment_option == 2)
			{
				if ($email == "$username@$config[project_licence_domain]")
				{
					$email = '';
				}
				$bill_internal_id = mr2string(sql("select internal_id from $config[tables_prefix]card_bill_providers where provider_id=(select provider_id from $config[tables_prefix]card_bill_packages where package_id=$card_package_id)"));
				$package_data = mr2array_single(sql("select * from $config[tables_prefix]card_bill_packages where package_id=$card_package_id"));

				$back_link = "$config[project_url]$_SERVER[SCRIPT_NAME]";
				if ($_POST['back_link'] != '' && strpos($_POST['back_link'], $config['project_url']) === 0)
				{
					$back_link = $_POST['back_link'];
				}

				require_once("$config[project_path]/admin/billings/KvsPaymentProcessor.php");
				require_once("$config[project_path]/admin/billings/$bill_internal_id/$bill_internal_id.php");
				$payment_processor = KvsPaymentProcessorFactory::create_instance($bill_internal_id);
				if ($payment_processor instanceof KvsPaymentProcessor)
				{
					$url = $payment_processor->get_payment_page_url($package_data, $back_link, array('username' => $username, 'pass' => $pass, 'email' => $email));
				} else
				{
					$redirect_func = "{$bill_internal_id}_get_redirect_url";
					$url = $redirect_func($package_data, $back_link, array('username' => $username, 'pass' => $pass, 'email' => $email));
				}
				$url = signupReplaceRuntimeParams($url);

				if (sql_update("update $config[tables_prefix]bill_outs set outs_amount=outs_amount+1 where added_date=?", date('Y-m-d')) == 0)
				{
					sql_pr("insert into $config[tables_prefix]bill_outs set outs_amount=1, added_date=?", date('Y-m-d'));
				}
				if ($_POST['mode'] == 'async')
				{
					async_return_request_status(null, $url);
				} else
				{
					header("Location: $url");
					die;
				}
			} elseif ($payment_option == 3)
			{
				$sms_transaction = mr2array_single(sql_pr("select * from $config[tables_prefix]bill_transactions where access_code=? and type_id in (1,10)", $sms_passcode));
				if ($sms_transaction['transaction_id'] > 0)
				{
					$sms_transaction_id = $sms_transaction['transaction_id'];
					if ($sms_transaction['tokens_granted'] > 0)
					{
						$status_id = 2;
						$tokens_purchased = $sms_transaction['tokens_granted'];
					} else
					{
						$status_id = 3;
					}
				}
			}

			if ($country_id == 0)
			{
				$country_id = mr2number(sql_pr("select country_id from $config[tables_prefix]list_countries where country_code=? and is_system=0 limit 1", nvl($_SERVER['GEOIP_COUNTRY_CODE'])));
			}

			if ($display_name == '')
			{
				$display_name = $username;
			}

			$reseller_code = '';
			if ($memberzone_data['AFFILIATE_PARAM_NAME'] != '')
			{
				$reseller_code = trim($_SESSION['runtime_params'][$memberzone_data['AFFILIATE_PARAM_NAME']]);
			}

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=? or ip=? or ip=?", nvl($_SERVER['REMOTE_ADDR']), nvl(int2ip(ip2int($_SERVER['REMOTE_ADDR']))), ip2mask(int2ip(ip2int($_SERVER['REMOTE_ADDR']))))) > 0)
			{
				sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted user from IP $_SERVER[REMOTE_ADDR]", nvl($display_name), date("Y-m-d H:i:s"));
				if ($_POST['mode']=='async')
				{
					$smarty->assign('async_submit_successful','true');
					return '';
				} else {
					header("Location: ?action=signup_done&send_email=1");die;
				}
			}
			unset($temp);
			if (preg_match("/([^@.]+\.[^@.]+)$/is",$email,$temp))
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_domains where domain=?", $temp[1])) > 0)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted user from IP $_SERVER[REMOTE_ADDR]", nvl($email), date("Y-m-d H:i:s"));
					if ($_POST['mode']=='async')
					{
						$smarty->assign('async_submit_successful','true');
						return '';
					} else {
						header("Location: ?action=signup_done&send_email=1");die;
					}
				}
			}

			$user_id = sql_insert("insert into $config[tables_prefix]users set ip=?, country_id=?, city=?, status_id=?, username=?, pass=?, email=?, display_name=?, gender_id=?, relationship_status_id=?, orientation_id=?, birth_date=?, tokens_available=?, language_code=?, reseller_code=?, added_date=?",
				ip2int($_SERVER['REMOTE_ADDR']), $country_id, $city, $status_id, $username, generate_password_hash($pass), $email, $display_name, $gender_id, $relationship_status_id, $orientation_id, $birth_date, $tokens_awarded+$tokens_purchased, nvl($config['locale']), $reseller_code, date("Y-m-d H:i:s")
			);

			$update_array = array();
			$custom_text_fields_count = 10;
			for ($i = 1; $i <= $custom_text_fields_count; $i++)
			{
				if (isset($_POST["custom{$i}"]))
				{
					$update_array["custom{$i}"] = process_blocked_words(trim($_POST["custom{$i}"]), true);
				}
			}
			if (isset($_POST["account_paypal"]))
			{
				$update_array["account_paypal"] = $_POST["account_paypal"];
			}
			if (count($update_array) > 0)
			{
				sql_pr("update $config[tables_prefix]users set ?% where user_id=?", $update_array, $user_id);
			}

			if ($tokens_awarded > 0)
			{
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=1, user_id=?, tokens_granted=?, added_date=?", $user_id, $tokens_awarded, date("Y-m-d H:i:s"));
			}
			if ($sms_transaction_id > 0)
			{
				$transaction_status_id = 1;
				if ($tokens_purchased > 0)
				{
					$transaction_status_id = 2;
				}
				sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=$transaction_status_id, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), $sms_transaction_id);
			} elseif ($access_code_transaction_id > 0)
			{
				if ($tokens_purchased > 0)
				{
					sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=2, access_start_date=?, access_end_date=?, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $access_code_transaction_id);
				} else
				{
					sql_pr("update $config[tables_prefix]bill_transactions set user_id=?, ip=?, country_code=lower(?), status_id=1, access_start_date=?, access_end_date=(case when is_unlimited_access=1 then '2070-01-01 00:00:00' else date_add(?, interval duration_rebill day) end), duration_rebill=0, access_code='' where transaction_id=?", $user_id, ip2int($_SERVER['REMOTE_ADDR']), nvl($_SERVER['GEOIP_COUNTRY_CODE']), date("Y-m-d H:i:s"), date("Y-m-d H:i:s"), $access_code_transaction_id);
				}
			}

			if ($reseller_code && $referral_award > 0)
			{
				$referring_user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where user_id=?", intval($reseller_code)));
				if ($referring_user_id > 0)
				{
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=8, user_id=?, ref_id=?, tokens_granted=?, added_date=?", $referring_user_id, $user_id, $referral_award, date("Y-m-d H:i:s"));
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $referral_award, $referring_user_id);
				}
			}

			$avatar_name=$_FILES['avatar']['name'];
			$avatar_path=$_FILES['avatar']['tmp_name'];
			$cover_name=$_FILES['cover']['name'];
			$cover_path=$_FILES['cover']['tmp_name'];

			if ($options['USER_COVER_OPTION']==1)
			{
				if ($avatar_path!='' && $cover_path=='')
				{
					$cover_path=$avatar_path;
					$cover_name=$avatar_name;
				}
			}

			if ($avatar_path<>'')
			{
				$target_path=get_dir_by_id($user_id);
				$avatar_ext=strtolower(end(explode(".",$avatar_name)));
				if (!in_array($avatar_ext,explode(",",$config['image_allowed_ext'])))
				{
					$avatar_ext='jpg';
				}
				$avatar_filename="$user_id.$avatar_ext";
				if (!is_dir("$config[content_path_avatars]/$target_path"))
				{
					mkdir("$config[content_path_avatars]/$target_path",0777);
					chmod("$config[content_path_avatars]/$target_path",0777);
				}

				$resize_option=$options['USER_AVATAR_TYPE'];
				if (!in_array($resize_option, array('need_size', 'max_size', 'max_width', 'max_height')))
				{
					$resize_option = 'need_size';
				}
				resize_image($resize_option,$avatar_path,"$config[content_path_avatars]/$target_path/$avatar_filename",$options['USER_AVATAR_SIZE']);

				sql_pr("update $config[tables_prefix]users set avatar=? where user_id=?","$target_path/$avatar_filename",$user_id);

				if (intval($memberzone_data['AWARDS_AVATAR'])>0)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_AVATAR']),$user_id);
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=2, user_id=?, tokens_granted=?, added_date=?",$user_id,intval($memberzone_data['AWARDS_AVATAR']),date("Y-m-d H:i:s"));
				}
			}

			if ($cover_path<>'')
			{
				$target_path=get_dir_by_id($user_id);
				$cover_ext=strtolower(end(explode(".",$cover_name)));
				if (!in_array($cover_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cover_ext='jpg';
				}
				$cover_filename="{$user_id}c.$cover_ext";
				if (!is_dir("$config[content_path_avatars]/$target_path"))
				{
					mkdir("$config[content_path_avatars]/$target_path",0777);
					chmod("$config[content_path_avatars]/$target_path",0777);
				}

				$resize_option=$options['USER_COVER_TYPE'];
				if (!in_array($resize_option, array('need_size', 'max_size', 'max_width', 'max_height')))
				{
					$resize_option = 'need_size';
				}
				resize_image($resize_option,$cover_path,"$config[content_path_avatars]/$target_path/$cover_filename",$options['USER_COVER_SIZE']);

				sql_pr("update $config[tables_prefix]users set cover=? where user_id=?","$target_path/$cover_filename",$user_id);

				if (intval($memberzone_data['AWARDS_COVER'])>0)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_COVER']),$user_id);
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=16, user_id=?, tokens_granted=?, added_date=?",$user_id,intval($memberzone_data['AWARDS_COVER']),date("Y-m-d H:i:s"));
				}
			}

			$send_email=0; // backward compatibility
			if ($status_id>=2)
			{
				$user_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$user_id));
				$remember_me_days=0;
				if (intval($block_config['remember_me'])>0)
				{
					$remember_me_days=intval($block_config['remember_me']);
				}
				login_user($user_data,$remember_me_days);
			} elseif ($status_id == 1)
			{
				$send_email = 1; // backward compatibility
				$confirm_code = generate_confirm_code();

				sql_pr("insert into $config[tables_prefix]users_confirm_codes set confirm_code=?, user_id=?, added_date=?, type_id=1", $confirm_code, $user_id, date("Y-m-d H:i:s"));

				$email_link = "$config[project_url]/$page_id.php?action=confirm&code=$confirm_code";
				if ($_POST['email_link'] != '' && strpos($_POST['email_link'], $config['project_url']) === 0)
				{
					$email_link = "$_POST[email_link]?action=confirm&code=$confirm_code";
				}
				$tokens = array(
					'{{$link}}' => $email_link,
					'{{$email}}' => $email,
					'{{$pass}}' => $pass,
					'{{$username}}' => $username,
					'{{$project_name}}' => $config['project_name'],
					'{{$support_email}}' => $config['support_email'],
					'{{$project_licence_domain}}' => $config['project_licence_domain']
				);
				$subject = file_get_contents("$config[project_path]/blocks/signup/emails/after_signup_subject.txt");
				$body = file_get_contents("$config[project_path]/blocks/signup/emails/after_signup_body.txt");
				$headers = file_get_contents("$config[project_path]/blocks/signup/emails/headers.txt");
				send_mail($email, $subject, $body, $headers, $tokens);
			}

			unset($_SESSION['security_code']);
			unset($_SESSION['security_code_signup']);
			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=signup_done&send_email=$send_email");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
		$smarty->assign('errors',$errors);
	}

	if (isset($block_config['enable_card_payment']))
	{
		$service_id=intval($_REQUEST['service_id']);
		$service_provider=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where status_id=1 and provider_id=?",$service_id));
		if (intval($service_provider['provider_id'])==0)
		{
			$service_provider=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where status_id=1 and is_default=1 and internal_id!='tokens'"));
			$service_id=intval($service_provider['provider_id']);
		}
		$card_packages=mr2array(sql("select * from $config[tables_prefix]card_bill_packages where status_id=1 and scope_id in (0,1) and provider_id=$service_id order by sort_id asc"));
		if (count($card_packages)>0)
		{
			foreach ($card_packages as $k=>$v)
			{
				$card_packages[$k]['payment_page_url']=signupReplaceRuntimeParams($card_packages[$k]['payment_page_url']);
				if ($v['include_countries']!='')
				{
					$include_countries=array_map('strtolower',array_map('trim',explode(',',$v['include_countries'])));
					if (!in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$include_countries))
					{
						unset($card_packages[$k]);
					}
				}
				if ($v['exclude_countries']!='')
				{
					$exclude_countries=array_map('strtolower',array_map('trim',explode(',',$v['exclude_countries'])));
					if (in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$exclude_countries))
					{
						unset($card_packages[$k]);
					}
				}
			}
			$smarty->assign('card_packages',$card_packages);
		}
		if (intval($service_provider['provider_id'])>0)
		{
			$smarty->assign('service_id',$service_id);
			$smarty->assign('service_provider',$service_provider);
		}

		$card_providers=mr2array(sql("select provider_id, title from $config[tables_prefix]card_bill_providers where status_id=1 and internal_id!='tokens' order by is_default desc"));
		if (count($card_providers))
		{
			foreach ($card_providers as $k=>$card_provider)
			{
				$card_providers[$k]['packages']=mr2array(sql_pr("select * from $config[tables_prefix]card_bill_packages where status_id=1 and scope_id in (0,1) and provider_id=? order by sort_id asc",$card_provider['provider_id']));
				foreach ($card_providers[$k]['packages'] as $k2=>$package)
				{
					$card_providers[$k]['packages'][$k2]['payment_page_url']=signupReplaceRuntimeParams($package['payment_page_url']);
					if ($package['include_countries']!='')
					{
						$include_countries=array_map('strtolower',array_map('trim',explode(',',$package['include_countries'])));
						if (!in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$include_countries))
						{
							unset($card_providers[$k]['packages'][$k2]);
						}
					}
					if ($package['exclude_countries']!='')
					{
						$exclude_countries=array_map('strtolower',array_map('trim',explode(',',$package['exclude_countries'])));
						if (in_array(strtolower($_SERVER['GEOIP_COUNTRY_CODE']),$exclude_countries))
						{
							unset($card_providers[$k]['packages'][$k2]);
						}
					}
				}
			}
			$smarty->assign('card_providers',$card_providers);
		}

		$generated_username=strtolower(generate_password());
		for ($i=0;$i<100;$i++)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$generated_username))==0)
			{
				break;
			}
			$generated_username=strtolower(generate_password());
		}
		$generated_password=strtolower(generate_password());
		$generated_email="$generated_username@$config[project_licence_domain]";
		$smarty->assign('generated_username',$generated_username);
		$smarty->assign('generated_password',$generated_password);
		$smarty->assign('generated_email',$generated_email);
	}
	if (isset($block_config['enable_sms_payment']))
	{
		$sms_packages=mr2array(sql("select * from $config[tables_prefix]sms_bill_packages where status_id=1 and provider_id=(select provider_id from $config[tables_prefix]sms_bill_providers where status_id=1) order by sort_id asc"));
		if (count($sms_packages)>0)
		{
			$provider_internal_id=mr2string(sql("select internal_id from $config[tables_prefix]sms_bill_providers where status_id=1"));
			require_once("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php");
			$message_text_func="{$provider_internal_id}_get_sms_message_text";
			foreach ($sms_packages as $k=>$v)
			{
				$sms_packages[$k]['countries']=mr2array(sql("select * from $config[tables_prefix]sms_bill_countries where package_id=$v[package_id] order by sort_id asc"));
				foreach ($sms_packages[$k]['countries'] as $k1=>$v1)
				{
					$sms_packages[$k]['countries'][$k1]['operators']=mr2array(sql("select * from $config[tables_prefix]sms_bill_operators where country_id=$v1[country_id] order by sort_id asc"));
					foreach ($sms_packages[$k]['countries'][$k1]['operators'] as $k2=>$v2)
					{
						$sms_packages[$k]['countries'][$k1]['operators'][$k2]['sms_message_text']=$message_text_func($v2['prefix'],$v['external_id']);
					}
				}
			}
			$smarty->assign('sms_packages',$sms_packages);
			$smarty->assign('geo_code',strtolower($_SERVER['GEOIP_COUNTRY_CODE']));
		}
	}
	if (isset($block_config['enable_access_codes']))
	{
		$smarty->assign('access_codes',mr2array_list(sql_pr("select access_code from $config[tables_prefix]bill_transactions where status_id=4 and access_code!=''")));
	}

	if ($_SESSION['user_id']>0 && !isset($_GET['action']))
	{
		if ($_GET['mode']=='async')
		{
			header('HTTP/1.0 403 Forbidden');die;
		} else {
			return "status_302: $config[project_url]";
		}
	}
	if (isset($block_config['disable_free_access']))
	{
		$smarty->assign('disable_free_access',1);
	}
	if (isset($block_config['default_access_option']))
	{
		if ($_POST['action']=='')
		{
			$_POST['payment_option']=$block_config['default_access_option'];
		}
	}

	if (isset($block_config['disable_captcha']))
	{
		$smarty->assign('disable_captcha',1);
	}

	if (isset($block_config['require_display_name']))
	{
		$smarty->assign('require_display_name',1);
	}
	if (isset($block_config['require_avatar']))
	{
		$smarty->assign('require_avatar',1);
	}
	if (isset($block_config['require_cover']))
	{
		$smarty->assign('require_cover',1);
	}
	if (isset($block_config['require_country']))
	{
		$smarty->assign('require_country',1);
	}
	if (isset($block_config['require_city']))
	{
		$smarty->assign('require_city',1);
	}
	if (isset($block_config['require_gender']))
	{
		$smarty->assign('require_gender',1);
	}
	if (isset($block_config['require_orientation']))
	{
		$smarty->assign('require_orientation',1);
	}
	if (isset($block_config['require_relationship_status']))
	{
		$smarty->assign('require_relationship_status',1);
	}
	if (isset($block_config['require_birth_date']))
	{
		$smarty->assign('require_birth_date',1);
	}
	if (isset($block_config['require_access_code']))
	{
		$smarty->assign('require_access_code',1);
	}

	for ($i = 1; $i <= $custom_text_fields_count; $i++)
	{
		if (isset($block_config["require_custom{$i}"]))
		{
			$smarty->assign("require_custom{$i}",1);
		}
	}

	$smarty->assign('avatar_size',$options['USER_AVATAR_SIZE']);
	$smarty->assign('cover_size',$options['USER_COVER_SIZE']);

	if ($_POST['action']=='')
	{
		$_POST['country_id'] = mr2number(sql_pr("select country_id from $config[tables_prefix]list_countries where country_code=? and is_system=0 limit 1", nvl($_SERVER['GEOIP_COUNTRY_CODE'])));
	}
	return '';
}

function signupAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='check_user')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		async_set_request_content_type();
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",trim($_REQUEST['username'])))>0)
		{
			if ($_REQUEST['format']=='json') {echo 'false';} else {echo "<failure/>";}
		} else {
			if ($_REQUEST['format']=='json') {echo 'true';} else {echo "<success/>";}
		}
		die;
	} elseif ($_REQUEST['action']=='check_email')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		async_set_request_content_type();
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=?",trim($_REQUEST['email'])))>0)
		{
			if ($_REQUEST['format']=='json') {echo 'false';} else {echo "<failure/>";}
		} else {
			if ($_REQUEST['format']=='json') {echo 'true';} else {echo "<success/>";}
		}
		die;
	}
}

function signupGetHash($block_config)
{
	return "nocache";
}

function signupCacheControl($block_config)
{
	return "nocache";
}

function signupMetaData()
{
	return array(
		// functionality
		array("name"=>"use_confirm_email", "group"=>"functionality", "type"=>"",    "is_required"=>0),
		array("name"=>"remember_me",       "group"=>"functionality", "type"=>"INT", "is_required"=>0),
		array("name"=>"disable_captcha",   "group"=>"functionality", "type"=>"",    "is_required"=>0),

		// paid access
		array("name"=>"enable_card_payment",   "group"=>"paid_access", "type"=>"",              "is_required"=>0),
		array("name"=>"enable_sms_payment",    "group"=>"paid_access", "type"=>"",              "is_required"=>0, "is_deprecated"=>1),
		array("name"=>"enable_access_codes",   "group"=>"paid_access", "type"=>"",              "is_required"=>0),
		array("name"=>"disable_free_access",   "group"=>"paid_access", "type"=>"",              "is_required"=>0),
		array("name"=>"default_access_option", "group"=>"paid_access", "type"=>"CHOICE[1,2,3]", "is_required"=>0),

		// validation
		array("name"=>"require_display_name",        "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_avatar",              "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_cover",               "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_country",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_city",                "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_gender",              "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_orientation",         "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_relationship_status", "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_birth_date",          "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom1",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom2",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom3",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom4",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom5",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom6",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom7",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom8",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom9",             "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_custom10",            "group"=>"validation", "type"=>"", "is_required"=>0),
		array("name"=>"require_access_code",         "group"=>"validation", "type"=>"", "is_required"=>0),
	);
}

function signupJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingForms.js?v={$config['project_version']}";
}

function signupReplaceRuntimeParams($url)
{
	global $runtime_params;

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
				$url=str_replace("%$var%",$val,$url);
			}
		}
	}
	return $url;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
