<?php
function dvd_editShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	$user_id=intval($_SESSION['user_id']);
	$username=trim($_SESSION['username']);
	$is_trusted=intval($_SESSION['is_trusted']);

	if ($user_id<1)
	{
		if ($_POST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'dvd_edit')));
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

	$options=get_options();
	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));

	$errors=null;
	$errors_async=null;
	if (in_array($_POST['action'],array('add_new_complete','change_complete')))
	{
		if ($_POST['action']=='change_complete' && isset($block_config['forbid_change']))
		{
			if ($_POST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'forbidden','block'=>'dvd_edit')));
			} else {
				header("Location: $config[project_url]");die;
			}
		}

		$item_id=intval($_REQUEST[$block_config['var_dvd_id']]);
		$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]dvds where user_id=? and dvd_id=?",$user_id,$item_id));

		foreach ($_POST as $post_field_name=>$post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name]=trim($post_field_value);
			}
		}

		if ($_POST['title']=='')
		{
			$errors['title']=1;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'required','block'=>'dvd_edit');
		} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where title=? and dvd_id<>?",$_POST['title'],intval($_REQUEST[$block_config['var_dvd_id']])))>0)
		{
			$errors['title']=3;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'exists','block'=>'dvd_edit');
		}

		if (isset($block_config['require_description']))
		{
			if ($_POST['description']=='')
			{
				$errors['description']=1;
				$errors_async[]=array('error_field_name'=>'description','error_code'=>'required','block'=>'dvd_edit');
			}
		}

		if (isset($block_config['require_cover1_front']) && $old_data['cover1_front']=='' && $_FILES['cover1_front']['tmp_name']=='')
		{
			$errors['cover1_front']=1;
			$errors_async[]=array('error_field_name'=>'cover1_front','error_code'=>'required','block'=>'dvd_edit');
		} elseif ($_FILES['cover1_front']['tmp_name']<>'')
		{
			$file_ext=strtolower(end(explode(".",$_FILES['cover1_front']['name'])));
			if (!in_array($file_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover1_front']=2;
				$errors_async[]=array('error_field_name'=>'cover1_front','error_code'=>'invalid_format','block'=>'dvd_edit');
			} elseif ($options['DVD_COVER_1_SIZE']!='') {
				$size=getimagesize($_FILES['cover1_front']["tmp_name"]);
				$allowed_size=explode("x",$options['DVD_COVER_1_SIZE']);
				$allowed_size_option=$options['DVD_COVER_1_TYPE'];

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
					$errors['cover1_front']=3;
					$errors_async[]=array('error_field_name'=>'cover1_front','error_code'=>'invalid_size','error_details'=>array($options['DVD_COVER_1_SIZE']),'block'=>'dvd_edit');
				}
			}
		}

		if (isset($block_config['require_cover1_back']) && $old_data['cover1_back']=='' && $_FILES['cover1_back']['tmp_name']=='')
		{
			$errors['cover1_back']=1;
			$errors_async[]=array('error_field_name'=>'cover1_back','error_code'=>'required','block'=>'dvd_edit');
		} elseif ($_FILES['cover1_back']['tmp_name']<>'')
		{
			$file_ext=strtolower(end(explode(".",$_FILES['cover1_back']['name'])));
			if (!in_array($file_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover1_back']=2;
				$errors_async[]=array('error_field_name'=>'cover1_back','error_code'=>'invalid_format','block'=>'dvd_edit');
			} elseif ($options['DVD_COVER_1_SIZE']!='') {
				$size=getimagesize($_FILES['cover1_back']["tmp_name"]);
				$allowed_size=explode("x",$options['DVD_COVER_1_SIZE']);
				$allowed_size_option=$options['DVD_COVER_1_TYPE'];

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
					$errors['cover1_back']=3;
					$errors_async[]=array('error_field_name'=>'cover1_back','error_code'=>'invalid_size','error_details'=>array($options['DVD_COVER_1_SIZE']),'block'=>'dvd_edit');
				}
			}
		}

		if (isset($block_config['require_cover2_front']) && $old_data['cover2_front']=='' && $_FILES['cover2_front']['tmp_name']=='')
		{
			$errors['cover2_front']=1;
			$errors_async[]=array('error_field_name'=>'cover2_front','error_code'=>'required','block'=>'dvd_edit');
		} elseif ($_FILES['cover2_front']['tmp_name']<>'')
		{
			$file_ext=strtolower(end(explode(".",$_FILES['cover2_front']['name'])));
			if (!in_array($file_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover2_front']=2;
				$errors_async[]=array('error_field_name'=>'cover2_front','error_code'=>'invalid_format','block'=>'dvd_edit');
			} elseif ($options['DVD_COVER_2_SIZE']!='') {
				$size=getimagesize($_FILES['cover2_front']["tmp_name"]);
				$allowed_size=explode("x",$options['DVD_COVER_2_SIZE']);
				$allowed_size_option=$options['DVD_COVER_2_TYPE'];

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
					$errors['cover2_front']=3;
					$errors_async[]=array('error_field_name'=>'cover2_front','error_code'=>'invalid_size','error_details'=>array($options['DVD_COVER_2_SIZE']),'block'=>'dvd_edit');
				}
			}
		}

		if (isset($block_config['require_cover2_back']) && $old_data['cover2_back']=='' && $_FILES['cover2_back']['tmp_name']=='')
		{
			$errors['cover2_back']=1;
			$errors_async[]=array('error_field_name'=>'cover2_back','error_code'=>'required','block'=>'dvd_edit');
		} elseif ($_FILES['cover2_back']['tmp_name']<>'')
		{
			$file_ext=strtolower(end(explode(".",$_FILES['cover2_back']['name'])));
			if (!in_array($file_ext,explode(",",$config['image_allowed_ext'])))
			{
				$errors['cover2_back']=2;
				$errors_async[]=array('error_field_name'=>'cover2_back','error_code'=>'invalid_format','block'=>'dvd_edit');
			} elseif ($options['DVD_COVER_2_SIZE']!='') {
				$size=getimagesize($_FILES['cover2_back']["tmp_name"]);
				$allowed_size=explode("x",$options['DVD_COVER_2_SIZE']);
				$allowed_size_option=$options['DVD_COVER_2_TYPE'];

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
					$errors['cover2_back']=3;
					$errors_async[]=array('error_field_name'=>'cover2_back','error_code'=>'invalid_size','error_details'=>array($options['DVD_COVER_2_SIZE']),'block'=>'dvd_edit');
				}
			}
		}

		if (isset($block_config['require_tags']))
		{
			if ($_POST['tags']=='')
			{
				$errors['tags']=1;
				$errors_async[]=array('error_field_name'=>'tags','error_code'=>'required','block'=>'dvd_edit');
			}
		}

		if (isset($block_config['require_categories']))
		{
			if (count($_POST['category_ids'])<1 || (count($_POST['category_ids'])==1 && intval($_POST['category_ids'][0])==0))
			{
				$errors['category_ids']=1;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'required','block'=>'dvd_edit');
			}
		}

		$max_categories=intval($block_config['max_categories']);
		if ($max_categories==0) {$max_categories=3;}
		if (intval($errors['category_ids'])==0)
		{
			if (count($_POST['category_ids'])>$max_categories)
			{
				$errors['category_ids']=2;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'maximum','error_details'=>array($max_categories),'block'=>'dvd_edit');
			}
		}

		if (isset($_POST['tokens_required']))
		{
			if ($_POST['tokens_required']!='' && $_POST['tokens_required']!='0' && intval($_POST['tokens_required'])<1)
			{
				$errors['tokens_required']=2;
				$errors_async[]=array('error_field_name'=>'tokens_required','error_code'=>'integer','block'=>'dvd_edit');
			}
		}

		$antispam_action = '';
		if ($_POST['action']=='add_new_complete')
		{
			$antispam_action = process_antispam_rules(5, $_POST['title']);
			if ((isset($block_config['use_captcha']) || strpos($antispam_action, 'captcha') !== false) && !$is_trusted)
			{
				$recaptcha_data = null;
				if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
				{
					$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
				}
				if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
				{
					if ($_POST['g-recaptcha-response'] == '')
					{
						$errors['code'] = 1;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'required', 'block' => 'dvd_edit');
					} elseif (!validate_recaptcha($_POST['g-recaptcha-response'], $recaptcha_data))
					{
						$errors['code'] = 2;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'invalid', 'block' => 'dvd_edit');
					}
				} else
				{
					if ($_POST['code'] == '')
					{
						$errors['code'] = 1;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'required', 'block' => 'dvd_edit');
					} elseif ($_POST['code'] <> $_SESSION['security_code_video_edit'] && $_POST['code'] <> $_SESSION['security_code'])
					{
						$errors['code'] = 2;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'invalid', 'block' => 'dvd_edit');
					}
				}
			}
		}

		if (!is_array($errors))
		{
			if ($_POST['action'] == 'add_new_complete')
			{
				if (strpos($antispam_action, 'error') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on channel from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));
					async_return_request_status(array(array('error_code' => 'spam', 'block' => 'dvd_edit')));
				}
				if (strpos($antispam_action, 'delete') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted channel from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));

					if ($_POST['mode'] == 'async')
					{
						$smarty->assign('async_submit_successful', 'true');
						$smarty->assign('async_object_data', ['title' => $_POST['title'], 'dir' => get_correct_dir_name($_POST['title']), 'description' => $_POST['description'], 'status_id' => '0']);
						$smarty->assign('force_inactive', 1);
						return '';
					} else
					{
						if ($block_config['redirect_on_new_done'] <> '')
						{
							$url = process_url($block_config['redirect_on_new_done']);
							header("Location: $url?action=add_new_done");
							die;
						} else
						{
							header("Location: ?action=add_new_done");
							die;
						}
					}
				}
			}

			$_POST['title']=process_blocked_words($_POST['title'],true);
			$_POST['description']=process_blocked_words($_POST['description'],true);
			$_POST['tags']=process_blocked_words($_POST['tags'],false);

			$status_id=1;
			if ($_POST['action']=='add_new_complete')
			{
				$_POST['dir']=get_correct_dir_name($_POST['title']);
				if ($_POST['dir']<>'')
				{
					$temp_dir=$_POST['dir'];
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]dvds where dir=?",$temp_dir))==0)
						{
							$_POST['dir']=$temp_dir;break;
						}
						$temp_dir=$_POST['dir'].$i;
					}
				}

				if ((isset($block_config['force_inactive']) || strpos($antispam_action, 'deactivate') !== false) && !$is_trusted)
				{
					$status_id=0;
				}

				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				$tokens_required=0;
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_DVDS'])==1)
				{
					$tokens_required=intval($_POST['tokens_required']);
				}

				$item_id=sql_insert("insert into $config[tables_prefix]dvds set user_id=?, is_video_upload_allowed=?, is_review_needed=$is_review_needed, status_id=$status_id, title=?, dir=?, description=?, tokens_required=?, rating=0, rating_amount=1, added_date=?",
						$user_id,intval($_POST['is_video_upload_allowed']),$_POST['title'],$_POST['dir'],$_POST['description'],$tokens_required,date("Y-m-d H:i:s"));

				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=140, object_id=?, object_type_id=5, added_date=?",$user_id,$username,$item_id,date("Y-m-d H:i:s"));
			} else {
				if ($old_data['user_id']<>$user_id)
				{
					if ($_POST['mode']=='async')
					{
						async_return_request_status(array(array('error_code'=>'forbidden','block'=>'dvd_edit')));
					} else {
						header("Location: $config[project_url]");die;
					}
				}

				$status_id=intval($old_data['status_id']);
				if (isset($block_config['force_inactive_on_edit']) && !$is_trusted)
				{
					$status_id=0;
				}

				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				$is_video_upload_allowed=intval($old_data['is_video_upload_allowed']);
				if (isset($_POST['is_video_upload_allowed']))
				{
					$is_video_upload_allowed=intval($_POST['is_video_upload_allowed']);
				}

				$tokens_required=intval($old_data['tokens_required']);
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_DVDS'])==1 && isset($_POST['tokens_required']))
				{
					$tokens_required=intval($_POST['tokens_required']);
				}

				sql_pr("update $config[tables_prefix]dvds set is_review_needed=$is_review_needed, status_id=$status_id, is_video_upload_allowed=?, tokens_required=?, $database_selectors[locale_field_title]=?, $database_selectors[locale_field_description]=? where dvd_id=?",
					$is_video_upload_allowed,$tokens_required,$_POST['title'],$_POST['description'],$item_id);

				// track changes
				$update_details='';
				if ($_POST['title']<>$old_data[$database_selectors['locale_field_title']]) {$update_details.="$database_selectors[locale_field_title], ";}
				if ($_POST['description']<>$old_data[$database_selectors['locale_field_description']]) {$update_details.="$database_selectors[locale_field_description], ";}
				if (intval($is_video_upload_allowed)<>intval($old_data['is_video_upload_allowed'])) {$update_details.="is_video_upload_allowed, ";}
				if (intval($status_id)<>intval($old_data['status_id'])) {$update_details.="status_id, ";}

				if (strlen($update_details)>0)
				{
					$update_details=substr($update_details,0,strlen($update_details)-2);
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=5, action_details=?, added_date=?",$user_id,$username,$item_id,$update_details,date("Y-m-d H:i:s"));
				// end track changes
			}

			$cf1_name=$_FILES['cover1_front']['name'];
			$cf1_path=$_FILES['cover1_front']['tmp_name'];
			$cf2_name=$_FILES['cover2_front']['name'];
			$cf2_path=$_FILES['cover2_front']['tmp_name'];
			$cb1_name=$_FILES['cover1_back']['name'];
			$cb1_path=$_FILES['cover1_back']['tmp_name'];
			$cb2_name=$_FILES['cover2_back']['name'];
			$cb2_path=$_FILES['cover2_back']['tmp_name'];

			$resize1_option=$options['DVD_COVER_1_TYPE'];
			if (!in_array($resize1_option, array('need_size', 'max_size', 'max_width', 'max_height')))
			{
				$resize1_option = 'need_size';
			}
			$resize2_option=$options['DVD_COVER_2_TYPE'];
			if (!in_array($resize2_option, array('need_size', 'max_size', 'max_width', 'max_height')))
			{
				$resize2_option = 'need_size';
			}

			if ($options['DVD_COVER_OPTION'] == 1)
			{
				if ($cf1_path!='' && $cf2_path=='')
				{
					$cf2_path=$cf1_path;
					$cf2_name=$cf1_name;
				}
				if ($cb1_path!='' && $cb2_path=='')
				{
					$cb2_path=$cb1_path;
					$cb2_name=$cb1_name;
				}
			}

			if ($cf1_path!='')
			{
				if (!is_dir("$config[content_path_dvds]/$item_id"))
				{
					mkdir("$config[content_path_dvds]/$item_id",0777);
					chmod("$config[content_path_dvds]/$item_id",0777);
				}
				if ($old_data['cover1_front']!='')
				{
					@unlink("$config[content_path_dvds]/$item_id/$old_data[cover1_front]");
				}
				$cf1_ext=strtolower(end(explode(".",$cf1_name)));
				if (in_array($cf1_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cf1_name="cf1_{$item_id}.{$cf1_ext}";
					resize_image($resize1_option,$cf1_path,"$config[content_path_dvds]/$item_id/$cf1_name", $options['DVD_COVER_1_SIZE']);
					sql_pr("update $config[tables_prefix]dvds set cover1_front=? where dvd_id=?",$cf1_name,$item_id);
				}
			}
			if ($cf2_path<>'')
			{
				if (!is_dir("$config[content_path_dvds]/$item_id"))
				{
					mkdir("$config[content_path_dvds]/$item_id",0777);
					chmod("$config[content_path_dvds]/$item_id",0777);
				}
				if ($old_data['cover2_front']!='')
				{
					@unlink("$config[content_path_dvds]/$item_id/$old_data[cover2_front]");
				}
				$cf2_ext=strtolower(end(explode(".",$cf2_name)));
				if (in_array($cf2_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cf2_name="cf2_{$item_id}.{$cf2_ext}";
					resize_image($resize2_option,$cf2_path,"$config[content_path_dvds]/$item_id/$cf2_name", $options['DVD_COVER_2_SIZE']);
					sql_pr("update $config[tables_prefix]dvds set cover2_front=? where dvd_id=?",$cf2_name,$item_id);
				}
			}
			if ($cb1_path!='')
			{
				if (!is_dir("$config[content_path_dvds]/$item_id"))
				{
					mkdir("$config[content_path_dvds]/$item_id",0777);
					chmod("$config[content_path_dvds]/$item_id",0777);
				}
				if ($old_data['cover1_back']!='')
				{
					@unlink("$config[content_path_dvds]/$item_id/$old_data[cover1_back]");
				}
				$cb1_ext=strtolower(end(explode(".",$cb1_name)));
				if (in_array($cb1_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cb1_name="cb1_{$item_id}.{$cb1_ext}";
					resize_image($resize1_option,$cb1_path,"$config[content_path_dvds]/$item_id/$cb1_name", $options['DVD_COVER_1_SIZE']);
					sql_pr("update $config[tables_prefix]dvds set cover1_back=? where dvd_id=?",$cb1_name,$item_id);
				}
			}
			if ($cb2_path<>'')
			{
				if (!is_dir("$config[content_path_dvds]/$item_id"))
				{
					mkdir("$config[content_path_dvds]/$item_id",0777);
					chmod("$config[content_path_dvds]/$item_id",0777);
				}
				if ($old_data['cover2_back']!='')
				{
					@unlink("$config[content_path_dvds]/$item_id/$old_data[cover2_back]");
				}
				$cb2_ext=strtolower(end(explode(".",$cb2_name)));
				if (in_array($cb2_ext,explode(",",$config['image_allowed_ext'])))
				{
					$cb2_name="cb2_{$item_id}.{$cb2_ext}";
					resize_image($resize2_option,$cb2_path,"$config[content_path_dvds]/$item_id/$cb2_name", $options['DVD_COVER_2_SIZE']);
					sql_pr("update $config[tables_prefix]dvds set cover2_back=? where dvd_id=?",$cb2_name,$item_id);
				}
			}

			$update_array=array();
			for ($i=1;$i<=10;$i++)
			{
				if (isset($_POST["custom$i"]))
				{
					$update_array["custom$i"]=process_blocked_words($_POST["custom$i"],true);
				}
			}
			if (count($update_array)>0)
			{
				sql_pr("update $config[tables_prefix]dvds set ?% where dvd_id=?",$update_array,$item_id);
			}

			$list_ids_tags=array_map("intval",mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_dvds where dvd_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]tags_dvds where dvd_id=?",$item_id);
			if (strpos($_POST['tags'],',')!==false)
			{
				$temp=explode(",",$_POST['tags']);
			} else {
				$temp=explode(" ",$_POST['tags']);
			}
			if (is_array($temp))
			{
				$temp=array_map("trim",$temp);
				$temp=array_unique($temp);
				$inserted_tags=array();
				foreach ($temp as $tag)
				{
					$tag=trim($tag);
					if (in_array(mb_lowercase($tag),$inserted_tags)) {continue;}

					$tag_id=find_or_create_tag($tag,$options);
					if ($tag_id>0)
					{
						sql_pr("insert into $config[tables_prefix]tags_dvds set tag_id=?, dvd_id=?",$tag_id,$item_id);
						$inserted_tags[]=mb_lowercase($tag);
						$list_ids_tags[]=$tag_id;
					}
				}
			}

			$list_ids_categories=array_map("intval",mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_dvds where dvd_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]categories_dvds where dvd_id=?",$item_id);
			settype($_POST['category_ids'],'array');
			foreach ($_POST['category_ids'] as $category_id)
			{
				if (intval($category_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]categories_dvds set category_id=?, dvd_id=?",$category_id,$item_id);
					$list_ids_categories[]=$category_id;
				}
			}

			$list_ids_models=array_map("intval",mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_dvds where dvd_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]models_dvds where dvd_id=?",$item_id);
			settype($_POST['model_ids'],'array');
			foreach ($_POST['model_ids'] as $model_id)
			{
				if (intval($model_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]models_dvds set model_id=?, dvd_id=?",$model_id,$item_id);
					$list_ids_models[]=$model_id;
				}
			}

			if (count($list_ids_tags)>0)
			{
				$list_ids_tags=implode(',',$list_ids_tags);
				sql_pr("update $config[tables_prefix]tags set total_dvds=(select count(*) from $config[tables_prefix]tags_dvds where tag_id=$config[tables_prefix]tags.tag_id) where tag_id in ($list_ids_tags)");
			}

			if (count($list_ids_categories)>0)
			{
				$list_ids_categories=implode(',',$list_ids_categories);
				sql_pr("update $config[tables_prefix]categories set total_dvds=(select count(*) from $config[tables_prefix]categories_dvds where category_id=$config[tables_prefix]categories.category_id) where category_id in ($list_ids_categories)");
			}

			if (count($list_ids_models)>0)
			{
				$list_ids_models=implode(',',$list_ids_models);
				sql_pr("update $config[tables_prefix]models set total_dvds=(select count(*) from $config[tables_prefix]models_dvds where model_id=$config[tables_prefix]models.model_id) where model_id in ($list_ids_models)");
			}

			if ($_POST['mode']=='async')
			{
				$dvd_data=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where user_id=? and dvd_id=?",$user_id,$item_id));
				$smarty->assign('async_submit_successful','true');
				$smarty->assign('async_object_data',$dvd_data);
				if ($status_id==0)
				{
					$smarty->assign('force_inactive',1);
				}
				return '';
			} else {
				if ($_POST['action']=='add_new_complete')
				{
					if ($block_config['redirect_on_new_done']<>'')
					{
						$url=process_url($block_config['redirect_on_new_done']);
						header("Location: $url?action=add_new_done");die;
					} else
					{
						header("Location: ?action=add_new_done");die;
					}
				} else {
					if ($block_config['redirect_on_change_done']<>'')
					{
						$url=process_url($block_config['redirect_on_change_done']);
						header("Location: $url?action=change_done");die;
					} else
					{
						header("Location: ?action=change_done&$block_config[var_dvd_id]=$item_id");die;
					}
				}
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	if (intval($_REQUEST[$block_config['var_dvd_id']])>0)
	{
		$item_id=intval($_REQUEST[$block_config['var_dvd_id']]);
		$_POST=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where user_id=? and dvd_id=?",$user_id,$item_id));
		if (count($_POST)>0)
		{
			$_POST['block_uid']=$object_id;

			$_POST['tags']=implode(", ",mr2array_list(sql_pr("select (select $database_selectors[generic_selector_tag] from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_dvds.tag_id) as tag from $config[tables_prefix]tags_dvds where $config[tables_prefix]tags_dvds.dvd_id=? order by id asc",$item_id)));
			$_POST['category_ids']=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_dvds where dvd_id=? order by id asc",$item_id));
			$_POST['model_ids']=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_dvds where dvd_id=? order by id asc",$item_id));

			$_POST['total_videos']=mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dvd_id=?",$item_id));
			$storage[$object_id]['dvd_title']=$_POST['title'];
			$storage[$object_id]['dvd_id']=$_POST['dvd_id'];
		} else {
			return "status_404";
		}
	}

	$list_categories=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories order by title asc"));
	$list_categories_groups=mr2array(sql_pr("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups order by title asc"));
	$list_categories_ungrouped=array();
	foreach ($list_categories as $category)
	{
		if ($category['category_group_id']>0)
		{
			foreach ($list_categories_groups as $k=>$group)
			{
				if ($category['category_group_id']==$group['category_group_id'])
				{
					$list_categories_groups[$k]['categories'][]=$category;
					break;
				}
			}
		} else
		{
			$list_categories_ungrouped[]=$category;
		}
	}

	$smarty->assign('errors',$errors);
	$smarty->assign('list_categories',$list_categories);
	$smarty->assign('list_categories_groups',$list_categories_groups);
	$smarty->assign('list_categories_ungrouped',$list_categories_ungrouped);
	$smarty->assign('list_models',mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models order by title asc")));
	$smarty->assign('list_tags',mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags order by tag asc limit 500")));

	$smarty->assign('cover1_size',$options['DVD_COVER_1_SIZE']);
	$smarty->assign('cover2_size',$options['DVD_COVER_2_SIZE']);

	if (intval($_POST['dvd_id'])>0)
	{
		if (isset($block_config['forbid_change']))
		{
			$smarty->assign('change_forbidden',1);
		}
	} else
	{
		$antispam_action = process_antispam_rules(5);
		if ((isset($block_config['use_captcha']) || strpos($antispam_action, 'captcha') !== false) && !$is_trusted)
		{
			$smarty->assign('use_captcha',1);
		}
	}

	if (isset($block_config['require_description']))
	{
		$smarty->assign('require_description',1);
	}
	if (isset($block_config['require_cover1_front']))
	{
		$smarty->assign('require_cover1_front',1);
	}
	if (isset($block_config['require_cover1_back']))
	{
		$smarty->assign('require_cover1_back',1);
	}
	if (isset($block_config['require_cover2_front']))
	{
		$smarty->assign('require_cover2_front',1);
	}
	if (isset($block_config['require_cover2_back']))
	{
		$smarty->assign('require_cover2_back',1);
	}
	if (isset($block_config['require_tags']))
	{
		$smarty->assign('require_tags',1);
	}
	if (isset($block_config['require_categories']))
	{
		$smarty->assign('require_categories',1);
	}

	$max_categories=intval($block_config['max_categories']);
	if ($max_categories==0)
	{
		$max_categories=3;
	}
	$smarty->assign('max_categories',$max_categories);

	if (intval($memberzone_data['ENABLE_TOKENS_SALE_DVDS'])==1)
	{
		$smarty->assign('allow_tokens',1);
		$smarty->assign('tokens_price_default',intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']));
		$smarty->assign('tokens_period_default',intval($memberzone_data['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']));
		$smarty->assign('tokens_commission',intval($memberzone_data['TOKENS_SALE_INTEREST']));
	}

	return '';
}

function dvd_editGetHash($block_config)
{
	return "nocache";
}

function dvd_editCacheControl($block_config)
{
	return "nocache";
}

function dvd_editMetaData()
{
	return array(
		// new objects
		array("name"=>"force_inactive", "group"=>"new_objects", "type"=>"", "is_required"=>0),

		// editing mode
		array("name"=>"var_dvd_id",             "group"=>"edit_mode", "type"=>"STRING", "is_required"=>1, "default_value"=>"id"),
		array("name"=>"forbid_change",          "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive_on_edit", "group"=>"edit_mode", "type"=>"",       "is_required"=>0),

		// validation
		array("name"=>"require_description",  "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_cover1_front", "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_cover1_back",  "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_cover2_front", "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_cover2_back",  "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_tags",         "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"require_categories",   "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"max_categories",       "group"=>"validation", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),

		// functionality
		array("name"=>"use_captcha", "group"=>"functionality", "type"=>"", "is_required"=>0),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
		array("name"=>"redirect_on_new_done",     "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
		array("name"=>"redirect_on_change_done",  "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
	);
}

function dvd_editJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingDVDEdit.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
