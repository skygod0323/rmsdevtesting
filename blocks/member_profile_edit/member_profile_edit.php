<?php
function member_profile_editShow($block_config,$object_id)
{
	global $config,$smarty,$regexp_check_email,$page_id,$database_selectors,$list_countries;

	if ($_GET['action'] == 'confirm' || $_GET['action'] == 'confirm_email')
	{
		$smarty->assign('activated', 0);

		$code = trim($_GET['code']);
		if ($code)
		{
			$confirm_code = mr2array_single(sql_pr("select * from $config[tables_prefix]users_confirm_codes where confirm_code=?", $code));
			if ($confirm_code['type_id'] == 2)
			{
				$smarty->assign('activated', 1);
				sql_pr("update $config[tables_prefix]users set email=temp_email, temp_email='' where user_id=? and temp_email!=''", $confirm_code['user_id']);

				$email = mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=?", $confirm_code['user_id']));
				if ($_SESSION['user_id'] == $confirm_code['user_id'])
				{
					$_SESSION['user_info']['email'] = $email;
				}

				$user_billings = mr2array_list(sql_pr("select distinct internal_provider_id from $config[tables_prefix]bill_transactions where user_id=?", $confirm_code['user_id']));
				if (count($user_billings) > 0)
				{
					require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
					foreach ($user_billings as $user_billing_id)
					{
						if (is_file("$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php"))
						{
							require_once "$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php";
							$payment_processor = KvsPaymentProcessorFactory::create_instance($user_billing_id);
							if ($payment_processor instanceof KvsPaymentProcessor)
							{
								$payment_processor->process_email_change($confirm_code['user_id'], $email);
							}
						}
					}
				}
			}
		}
	} else {
		if ($_SESSION['user_id']<1)
		{
			if ($_POST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_edit')));
			} elseif ($_GET['mode']=='async')
			{
				header('HTTP/1.0 403 Forbidden');die;
			}

			$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
			if ($block_config['redirect_unknown_user_to']<>'')
			{
				$url=process_url($block_config['redirect_unknown_user_to']);
				return "status_302: $url";
			} else
			{
				return "status_302: $config[project_url]";
			}
		}
	}

	$options=get_options(array('USER_AVATAR_SIZE','USER_AVATAR_TYPE','USER_COVER_SIZE','USER_COVER_TYPE','USER_COVER_OPTION'));
	$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	$custom_text_fields_count = 10;

	$errors=null;
	$errors_async=null;

	if ($_POST['action']=='change_pass')
	{
		$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));

		$old_pass=trim($_POST['old_pass']);
		$pass=trim($_POST['pass']);
		$pass2=trim($_POST['pass2']);

		if ($old_data['pass'])
		{
			if (strlen($old_pass)==0)
			{
				$errors['old_pass']=1;
				$errors_async[]=array('error_field_name'=>'old_pass','error_code'=>'required','block'=>'member_profile_edit');
			} elseif (!verify_password_hash($old_pass,$old_data))
			{
				$errors['old_pass']=2;
				$errors_async[]=array('error_field_name'=>'old_pass','error_code'=>'invalid','block'=>'member_profile_edit');
			}
		}

		if (strlen($pass)==0)
		{
			$errors['pass']=1;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'required','block'=>'member_profile_edit');
		} elseif (strlen($pass)<5)
		{
			$errors['pass']=2;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'minimum','error_details'=>array(5),'block'=>'member_profile_edit');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_passwords where user_id=? and pass=?",$_SESSION['user_id'],generate_password_hash($pass)))>0)
		{
			$errors['pass']=3;
			$errors_async[]=array('error_field_name'=>'pass','error_code'=>'blocked','block'=>'member_profile_edit');
		}

		if (strlen($pass2)==0)
		{
			$errors['pass2']=1;
			$errors_async[]=array('error_field_name'=>'pass2','error_code'=>'required','block'=>'member_profile_edit');
		} elseif ($pass!=$pass2)
		{
			$errors['pass2']=2;
			$errors_async[]=array('error_field_name'=>'pass2','error_code'=>'invalid','block'=>'member_profile_edit');
		}

		if (!is_array($errors))
		{
			sql_pr("update $config[tables_prefix]users set pass=?, pass_bill='' where user_id=?",generate_password_hash($pass),$_SESSION['user_id']);

			$user_billings = mr2array_list(sql_pr("select distinct internal_provider_id from $config[tables_prefix]bill_transactions where user_id=?", $_SESSION['user_id']));
			if (count($user_billings) > 0)
			{
				require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
				foreach ($user_billings as $user_billing_id)
				{
					if (is_file("$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php"))
					{
						require_once "$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php";
						$payment_processor = KvsPaymentProcessorFactory::create_instance($user_billing_id);
						if ($payment_processor instanceof KvsPaymentProcessor)
						{
							$payment_processor->process_password_change($_SESSION['user_id'], $pass);
						}
					}
				}
			}
			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=change_pass_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	} elseif ($_POST['action']=='change_email')
	{
		$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));

		$email=trim($_POST['email']);

		if (strlen($email)==0)
		{
			$errors['email']=1;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'required','block'=>'member_profile_edit');
		} elseif (!preg_match($regexp_check_email,$email))
		{
			$errors['email']=2;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'invalid','block'=>'member_profile_edit');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where email=? and user_id<>?",$email,$_SESSION['user_id']))>0)
		{
			$errors['email']=3;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'exists','block'=>'member_profile_edit');
		} elseif ($old_data['email']==$email)
		{
			$errors['email']=4;
			$errors_async[]=array('error_field_name'=>'email','error_code'=>'not_changed','block'=>'member_profile_edit');
		}

		if (!is_array($errors))
		{
			if (isset($block_config['use_confirm_email']))
			{
				$send_email=1;
				$confirm_code=generate_confirm_code();
				$username=$old_data['username'];

				sql_pr("insert into $config[tables_prefix]users_confirm_codes set confirm_code=?, user_id=?, added_date=?, type_id=2",$confirm_code,$_SESSION['user_id'],date("Y-m-d H:i:s"));
				sql_pr("update $config[tables_prefix]users set temp_email=? where user_id=?",$email,$_SESSION['user_id']);

				$email_link="$config[project_url]/$page_id.php?action=confirm&code=$confirm_code";
				if ($_POST['email_link']!='' && strpos($_POST['email_link'],$config['project_url'])===0)
				{
					$email_link="$_POST[email_link]?action=confirm_email&code=$confirm_code";
				}
				$tokens = array(
					'{{$link}}'=>$email_link,
					'{{$email}}'=>$email,
					'{{$username}}'=>$username,
					'{{$project_name}}'=>$config['project_name'],
					'{{$support_email}}'=>$config['support_email'],
					'{{$project_licence_domain}}'=>$config['project_licence_domain']
				);
				$subject=file_get_contents("$config[project_path]/blocks/member_profile_edit/emails/after_change_email_subject.txt");
				$body=file_get_contents("$config[project_path]/blocks/member_profile_edit/emails/after_change_email_body.txt");
				$headers=file_get_contents("$config[project_path]/blocks/member_profile_edit/emails/headers.txt");
				send_mail($email,$subject,$body,$headers,$tokens);
			} else
			{
				$send_email=0;
				sql_pr("update $config[tables_prefix]users set email=? where user_id=?",$email,$_SESSION['user_id']);
				$_SESSION['user_info']['email'] = $email;

				$user_billings = mr2array_list(sql_pr("select distinct internal_provider_id from $config[tables_prefix]bill_transactions where user_id=?", $_SESSION['user_id']));
				if (count($user_billings) > 0)
				{
					require_once "$config[project_path]/admin/billings/KvsPaymentProcessor.php";
					foreach ($user_billings as $user_billing_id)
					{
						if (is_file("$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php"))
						{
							require_once "$config[project_path]/admin/billings/$user_billing_id/$user_billing_id.php";
							$payment_processor = KvsPaymentProcessorFactory::create_instance($user_billing_id);
							if ($payment_processor instanceof KvsPaymentProcessor)
							{
								$payment_processor->process_email_change($_SESSION['user_id'], $email);
							}
						}
					}
				}
			}
			if ($_POST['mode']=='async')
			{
				$smarty->assign('send_email',$send_email);
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=change_email_done&send_email=$send_email");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	} elseif ($_POST['action']=='change_profile')
	{
		$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));

		$display_name=trim(process_blocked_words($_POST['display_name'],true));
		$city=trim(process_blocked_words($_POST['city'],true));
		$country_id=intval($_POST['country_id']);
		$gender_id=intval($_POST['gender_id']);
		$relationship_status_id=intval($_POST['relationship_status_id']);
		$orientation_id=intval($_POST['orientation_id']);

		if (strlen($display_name)==0)
		{
			$errors['display_name']=1;
			$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'required','block'=>'member_profile_edit');
		} elseif (strlen($display_name)<3)
		{
			$errors['display_name']=2;
			$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'minimum','error_details'=>array(3),'block'=>'member_profile_edit');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where display_name=? and user_id<>?",$display_name,$_SESSION['user_id']))>0)
		{
			$errors['display_name']=3;
			$errors_async[]=array('error_field_name'=>'display_name','error_code'=>'exists','block'=>'member_profile_edit');
		}

		if (isset($block_config['require_country']) && $country_id==0)
		{
			$errors['country_id']=1;
			$errors_async[]=array('error_field_name'=>'country_id','error_code'=>'required','block'=>'member_profile_edit');
		}

		if (isset($block_config['require_city']) && $city=='')
		{
			$errors['city']=1;
			$errors_async[]=array('error_field_name'=>'city','error_code'=>'required','block'=>'member_profile_edit');
		}

		if (isset($block_config['require_gender']) && $gender_id==0)
		{
			$errors['gender_id']=1;
			$errors_async[]=array('error_field_name'=>'gender_id','error_code'=>'required','block'=>'member_profile_edit');
		}

		if (isset($block_config['require_orientation']) && $orientation_id==0)
		{
			$errors['orientation_id']=1;
			$errors_async[]=array('error_field_name'=>'orientation_id','error_code'=>'required','block'=>'member_profile_edit');
		}

		if (isset($block_config['require_relationship_status']) && $relationship_status_id==0)
		{
			$errors['relationship_status_id']=1;
			$errors_async[]=array('error_field_name'=>'relationship_status_id','error_code'=>'required','block'=>'member_profile_edit');
		}

		$birth_date=intval($_POST['birth_date_Year'])."-".intval($_POST['birth_date_Month'])."-".intval($_POST['birth_date_Day']);
		$_POST['birth_date']=$birth_date;
		if (isset($block_config['require_birth_date']) && $birth_date=='0-0-0')
		{
			$errors['birth_date']=1;
			$errors_async[]=array('error_field_name'=>'birth_date','error_code'=>'required','block'=>'member_profile_edit');
		} elseif ($birth_date<>'0-0-0')
		{
			if (intval($_POST['birth_date_Year']) < 1 || intval($_POST['birth_date_Month']) < 1 || intval($_POST['birth_date_Day']) < 1)
			{
				$errors['birth_date'] = 1;
				$errors_async[] = array('error_field_name' => 'birth_date', 'error_code' => 'invalid', 'block' => 'member_profile_edit');
			} elseif (intval($config['min_user_age']) < 0)
			{
				$birth_date_time = strtotime($birth_date);
				$birth_date_min_time = strtotime(date('d') . '-' . date('m') . '-' . (intval(date('Y')) - abs(intval($config['min_user_age']))));
				if ($birth_date_time > $birth_date_min_time)
				{
					$errors['birth_date'] = 1;
					$errors_async[] = array('error_field_name' => 'birth_date', 'error_code' => 'min_age', 'block' => 'member_profile_edit', 'error_details' => array(abs(intval($config['min_user_age']))));
				}
			}
		}

		if (isset($block_config['require_avatar']) && (($old_data['avatar']=='' && $_FILES['avatar']['tmp_name']=='') || $_POST['avatar_delete']==1))
		{
			$errors['avatar']=1;
			$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'required','block'=>'member_profile_edit');
		} elseif ($_FILES['avatar']['tmp_name']<>'')
		{
			$avatar_ext=strtolower(end(explode(".",$_FILES['avatar']['name'])));
			if (!in_array($avatar_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['avatar']=1;
				$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'invalid_format','block'=>'member_profile_edit');
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
					$errors_async[]=array('error_field_name'=>'avatar','error_code'=>'invalid_size','error_details'=>array($options['USER_AVATAR_SIZE']),'block'=>'member_profile_edit');
				}
			}
		}

		if (isset($block_config['require_cover']) && (($old_data['cover']=='' && $_FILES['cover']['tmp_name']=='') || $_POST['cover_delete']==1))
		{
			$errors['cover']=1;
			$errors_async[]=array('error_field_name'=>'cover','error_code'=>'required','block'=>'member_profile_edit');
		} elseif ($_FILES['cover']['tmp_name']<>'')
		{
			$cover_ext=strtolower(end(explode(".",$_FILES['cover']['name'])));
			if (!in_array($cover_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover']=1;
				$errors_async[]=array('error_field_name'=>'cover','error_code'=>'invalid_format','block'=>'member_profile_edit');
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
					$errors_async[]=array('error_field_name'=>'cover','error_code'=>'invalid_size','error_details'=>array($options['USER_COVER_SIZE']),'block'=>'member_profile_edit');
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

		if (isset($_POST['tokens_required']))
		{
			if ($_POST['tokens_required']!='' && $_POST['tokens_required']!='0' && intval($_POST['tokens_required'])<1)
			{
				$errors['tokens_required']=2;
				$errors_async[]=array('error_field_name'=>'tokens_required','error_code'=>'integer','block'=>'member_profile_edit');
			}
		}

		if (!is_array($errors))
		{
			$_POST['website']=process_blocked_words($_POST['website'],true);
			$_POST['education']=process_blocked_words($_POST['education'],true);
			$_POST['occupation']=process_blocked_words($_POST['occupation'],true);
			$_POST['about_me']=process_blocked_words($_POST['about_me'],true);
			$_POST['interests']=process_blocked_words($_POST['interests'],true);
			$_POST['favourite_movies']=process_blocked_words($_POST['favourite_movies'],true);
			$_POST['favourite_music']=process_blocked_words($_POST['favourite_music'],true);
			$_POST['favourite_books']=process_blocked_words($_POST['favourite_books'],true);
			$_POST['status_message']=process_blocked_words($_POST['status_message'],true);

			$tokens_required=intval($old_data['tokens_required']);
			if (intval($memberzone_data['ENABLE_TOKENS_SALE_MEMBERS'])==1 && isset($_POST['tokens_required']))
			{
				$tokens_required=intval($_POST['tokens_required']);
			}

			sql_pr("update $config[tables_prefix]users set gender_id=?, relationship_status_id=?, orientation_id=?, display_name=?, country_id=?, city=?, birth_date=?, tokens_required=?, favourite_category_id=?, status_message=?, website=?, education=?, occupation=?, about_me=?, interests=?, favourite_movies=?, favourite_music=?, favourite_books=? where user_id=?",
				$gender_id,$relationship_status_id,$orientation_id,$display_name,$country_id,$city,$birth_date,$tokens_required,intval($_POST['favourite_category_id']),trim($_POST['status_message']),trim($_POST['website']),trim($_POST['education']),trim($_POST['occupation']),trim($_POST['about_me']),trim($_POST['interests']),trim($_POST['favourite_movies']),trim($_POST['favourite_music']),trim($_POST['favourite_books']),$_SESSION['user_id']
			);

			if ($birth_date=='0-0-0')
			{
				$birth_date='0000-00-00';
			}

			$_SESSION['display_name']=$display_name;
			$_SESSION['gender_id']=$gender_id;
			$_SESSION['birth_date']=$birth_date;
			if ($_SESSION['birth_date']!='0000-00-00')
			{
				$age=get_time_passed($_SESSION['birth_date']);
				$_SESSION['age']=$age['value'];
			} else {
				unset($_SESSION['age']);
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
				if ($old_data['avatar']!='')
				{
					@unlink("$config[content_path_avatars]/$old_data[avatar]");
				}

				$target_path=get_dir_by_id($_SESSION['user_id']);
				$avatar_ext=strtolower(end(explode(".",$avatar_name)));
				if (!in_array($avatar_ext,explode(",",$config['image_allowed_ext'])))
				{
					$avatar_ext='jpg';
				}
				$avatar_filename="$_SESSION[user_id].$avatar_ext";
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

				sql_pr("update $config[tables_prefix]users set avatar=? where user_id=?","$target_path/$avatar_filename",$_SESSION['user_id']);
				$_SESSION['avatar']="$target_path/$avatar_filename";
				$_SESSION['avatar_url']=$config['content_url_avatars']."/".$_SESSION['avatar'];

				if (intval($memberzone_data['AWARDS_AVATAR'])>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=2 and user_id=?",$_SESSION['user_id']))==0)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_AVATAR']),$_SESSION['user_id']);
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=2, user_id=?, tokens_granted=?, added_date=?",$_SESSION['user_id'],intval($memberzone_data['AWARDS_AVATAR']),date("Y-m-d H:i:s"));
					$_SESSION['tokens_available']=intval($_SESSION['tokens_available'])+intval($memberzone_data['AWARDS_AVATAR']);
				}

				sql_pr("insert into $config[tables_prefix]users_events set event_type_id=17, user_id=?, added_date=?",$_SESSION['user_id'],date("Y-m-d H:i:s"));
			} elseif ($_POST['avatar_delete']==1)
			{
				if ($old_data['avatar']!='')
				{
					@unlink("$config[content_path_avatars]/$old_data[avatar]");
					sql_pr("update $config[tables_prefix]users set avatar='' where user_id=?",$_SESSION['user_id']);
				}
				unset($_SESSION['avatar']);
				unset($_SESSION['avatar_url']);
			}

			if ($cover_path<>'')
			{
				if ($old_data['cover']!='')
				{
					@unlink("$config[content_path_avatars]/$old_data[cover]");
				}

				$target_path=get_dir_by_id($_SESSION['user_id']);
				$cover_ext=strtolower(end(explode(".",$cover_name)));
				if (!in_array($cover_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cover_ext='jpg';
				}
				$cover_filename="$_SESSION[user_id]c.$cover_ext";
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

				sql_pr("update $config[tables_prefix]users set cover=? where user_id=?","$target_path/$cover_filename",$_SESSION['user_id']);
				$_SESSION['cover']="$target_path/$cover_filename";
				$_SESSION['cover_url']=$config['content_url_avatars']."/".$_SESSION['cover'];

				if (intval($memberzone_data['AWARDS_COVER'])>0 && mr2number(sql_pr("select count(*) from $config[tables_prefix]log_awards_users where award_type=16 and user_id=?",$_SESSION['user_id']))==0)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?",intval($memberzone_data['AWARDS_COVER']),$_SESSION['user_id']);
					sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=16, user_id=?, tokens_granted=?, added_date=?",$_SESSION['user_id'],intval($memberzone_data['AWARDS_COVER']),date("Y-m-d H:i:s"));
					$_SESSION['tokens_available']=intval($_SESSION['tokens_available'])+intval($memberzone_data['AWARDS_COVER']);
				}
			} elseif ($_POST['cover_delete']==1)
			{
				if ($old_data['cover']!='')
				{
					@unlink("$config[content_path_avatars]/$old_data[cover]");
					sql_pr("update $config[tables_prefix]users set cover='' where user_id=?",$_SESSION['user_id']);
				}
				unset($_SESSION['cover']);
				unset($_SESSION['cover_url']);
			}

			$update_array=array();
			for ($i=1;$i<=$custom_text_fields_count;$i++)
			{
				if (isset($_POST["custom{$i}"]))
				{
					$update_array["custom{$i}"]=process_blocked_words(trim($_POST["custom{$i}"]),true);
				}
			}
			if (isset($_POST["account_paypal"]))
			{
				$update_array["account_paypal"]=$_POST["account_paypal"];
			}
			if (count($update_array)>0)
			{
				sql_pr("update $config[tables_prefix]users set ?% where user_id=?",$update_array,$_SESSION['user_id']);
			}

			if (trim($_POST['status_message'])<>'' && $old_data['status_message']<>trim($_POST['status_message']))
			{
				sql_pr("insert into $config[tables_prefix]users_events set event_type_id=18, user_id=?, status_message=?, added_date=?",$_SESSION['user_id'],trim($_POST['status_message']),date("Y-m-d H:i:s"));
			}

			$user_data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
			if ($user_data["avatar"]!='')
			{
				$user_data["avatar_url"]=$config['content_url_avatars']."/".$user_data['avatar'];
			}
			if ($user_data["cover"]!='')
			{
				$user_data["cover_url"]=$config['content_url_avatars']."/".$user_data['cover'];
			}
			unset($user_data['pass']);
			unset($user_data['pass_bill']);
			unset($user_data['temp_pass']);
			$_SESSION['user_info']=$user_data;

			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=change_profile_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	$smarty->assign('errors',$errors);
	$smarty->assign('list_categories',mr2array(sql("select $database_selectors[categories] from $config[tables_prefix]categories order by $database_selectors[generic_selector_title] asc")));
	$smarty->assign('avatar_size',$options['USER_AVATAR_SIZE']);
	$smarty->assign('cover_size',$options['USER_COVER_SIZE']);

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

	for ($i = 1; $i <= $custom_text_fields_count; $i++)
	{
		if (isset($block_config["require_custom{$i}"]))
		{
			$smarty->assign("require_custom{$i}",1);
		}
	}

	if ($_GET['action']=='change_email')
	{
		$smarty->assign('old_email',mr2string(sql_pr("select email from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])));
	}

	if (intval($memberzone_data['ENABLE_TOKENS_SALE_MEMBERS'])==1)
	{
		$smarty->assign('allow_tokens',1);
		$smarty->assign('tokens_price_default',intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']));
		$smarty->assign('tokens_period_default',intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']));
		$smarty->assign('tokens_commission',intval($memberzone_data['TOKENS_SALE_INTEREST']));
	}

	if ($_POST['action']<>'change_profile' && $_GET['action']<>'confirm' && $_GET['action']<>'confirm_email')
	{
		$data=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
		if ($data['birth_date']<>'0000-00-00')
		{
			$data['age']=get_time_passed($data['birth_date']);
		}
		if ($data['avatar']!='')
		{
			$data['avatar_url']=$config['content_url_avatars']."/".$data['avatar'];
		}
		if ($data['cover']!='')
		{
			$data['cover_url']=$config['content_url_avatars']."/".$data['cover'];
		}

		$data['time_passed_from_adding']=get_time_passed($data['added_date']);
		$data['time_passed_from_last_login']=get_time_passed($data['last_login_date']);
		$data['country']=$list_countries['name'][$data['country_id']];
		$smarty->assign('data', $data);

		$_POST=$data;
		if (count($_POST)>0)
		{
			$_POST['block_uid']=$object_id;
		}
	}
	return '';
}

function member_profile_editGetHash($block_config)
{
	return "nocache";
}

function member_profile_editCacheControl($block_config)
{
	return "nocache";
}

function member_profile_editMetaData()
{
	return array(
		// functionality
		array("name"=>"use_confirm_email", "group"=>"functionality", "type"=>"",       "is_required"=>0),

		// validation
		array("name"=>"require_avatar",              "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_cover",               "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_country",             "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_city",                "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_gender",              "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_orientation",         "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_relationship_status", "group"=>"validation","type"=>"", "is_required"=>0),
		array("name"=>"require_birth_date",          "group"=>"validation","type"=>"", "is_required"=>0),
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

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
	);
}

function member_profile_editJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
