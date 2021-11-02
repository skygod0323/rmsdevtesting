<?php
function video_editShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	$user_id=intval($_SESSION['user_id']);
	$username=trim($_SESSION['username']);
	$is_trusted=intval($_SESSION['is_trusted']);
	$user_status_id=intval($_SESSION['status_id']);
	$user_content_source_group_id=intval($_SESSION['content_source_group_id']);

	if ($user_id<1 && !isset($block_config['allow_anonymous']))
	{
		if ($_POST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_edit')));
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

	$min_image_size=array(0=>0,1=>0);
	$sizes=mr2array_list(sql_pr("select size from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
	foreach($sizes as $size)
	{
		$temp_size=explode("x",$size);
		if (intval($temp_size[0])>$min_image_size[0]) {$min_image_size[0]=intval($temp_size[0]);}
		if (intval($temp_size[1])>$min_image_size[1]) {$min_image_size[1]=intval($temp_size[1]);}
	}

	$options=get_options();

	$errors=null;
	$errors_async=null;
	if (in_array($_POST['action'],array('add_new_complete','change_complete')))
	{
		if ($_POST['action']=='change_complete' && isset($block_config['forbid_change']))
		{
			if ($_POST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'forbidden','block'=>'video_edit')));
			} else {
				header("Location: $config[project_url]");die;
			}
		}

		require_once "$config[project_path]/admin/include/functions_screenshots.php";

		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}
		settype($_POST['category_ids'],"array");
		settype($_POST['model_ids'],"array");

		if ($_POST['title']=='')
		{
			$errors['title']=1;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'required','block'=>'video_edit');
		} elseif (strlen($_POST['title'])<5)
		{
			$errors['title']=2;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'minimum','error_details'=>array(5),'block'=>'video_edit');
		}

		if (!isset($block_config['optional_description']))
		{
			if ($_POST['description']=='')
			{
				$errors['description']=1;
				$errors_async[]=array('error_field_name'=>'description','error_code'=>'required','block'=>'video_edit');
			}
		}
		if (!isset($block_config['optional_tags']))
		{
			if ($_POST['tags']=='')
			{
				$errors['tags']=1;
				$errors_async[]=array('error_field_name'=>'tags','error_code'=>'required','block'=>'video_edit');
			}
		}
		if (!isset($block_config['optional_categories']))
		{
			if (count($_POST['category_ids'])<1 || (count($_POST['category_ids'])==1 && intval($_POST['category_ids'][0])==0))
			{
				$errors['category_ids']=1;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'required','block'=>'video_edit');
			}
		}
		$max_categories=intval($block_config['max_categories']);
		if ($max_categories==0) {$max_categories=3;}
		if (intval($errors['category_ids'])==0)
		{
			if (count($_POST['category_ids'])>$max_categories)
			{
				$errors['category_ids']=2;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'maximum','error_details'=>array($max_categories),'block'=>'video_edit');
			}
		}

		if (intval($_POST['dvd_id'])>0)
		{
			$dvd_info=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where dvd_id=?",intval($_POST['dvd_id'])));
			if (!video_editIsDvdAllowedForUpload($dvd_info))
			{
				$errors['dvd_id']=2;
				$errors_async[]=array('error_field_name'=>'dvd_id','error_code'=>'forbidden','block'=>'video_edit');
			}
		}

		if ($_FILES['screenshot']['tmp_name']<>'')
		{
			$ext=strtolower(end(explode(".",$_FILES['screenshot']['name'])));
			if (strpos($ext,"?")!==false) {$ext=substr($ext,0,strpos($ext,"?"));}
			if ($ext!='jpg' && $ext!='jpeg')
			{
				$errors['screenshot']=1;
				$errors_async[]=array('error_field_name'=>'screenshot','error_code'=>'invalid_format','block'=>'video_edit');
			} else {
				$size=getimagesize($_FILES['screenshot']['tmp_name']);
				if ($size[0]<$min_image_size[0] || $size[1]<$min_image_size[1])
				{
					$errors['screenshot']=2;
					$errors_async[]=array('error_field_name'=>'screenshot','error_code'=>'invalid_size','error_details'=>array("$min_image_size[0]x$min_image_size[1]"),'block'=>'video_edit');
				}
			}
		}

		if (isset($_POST['tokens_required']))
		{
			if ($_POST['tokens_required']!='' && $_POST['tokens_required']!='0' && intval($_POST['tokens_required'])<1)
			{
				$errors['tokens_required']=2;
				$errors_async[]=array('error_field_name'=>'tokens_required','error_code'=>'integer','block'=>'video_edit');
			}
		}

		$duration=0;
		$antispam_action = '';
		if ($_POST['action']=='add_new_complete')
		{
			if ($_POST['file_hash']=='')
			{
				$errors['file']=1;
				$errors_async[]=array('error_field_name'=>'file','error_code'=>'required','block'=>'video_edit');
			} else {
				if (is_file("$config[temporary_path]/$_POST[file_hash].embed"))
				{
					$embed_info = @unserialize(file_get_contents("$config[temporary_path]/$_POST[file_hash].embed"));
					$duration = intval($embed_info['duration']);
				} else
				{
					$ext=strtolower(end(explode(".",$_POST['file'])));
					if (!in_array($ext,explode(",",$config['video_allowed_ext'])))
					{
						$errors['file']=2;
						$errors_async[]=array('error_field_name'=>'file','error_code'=>'invalid_format','error_details'=>array(str_replace(',',', ', $config['video_allowed_ext'])),'block'=>'video_edit');
					} else {
						$duration=get_video_duration("$config[temporary_path]/$_POST[file_hash].tmp");

						if (intval($block_config['min_duration'])>0 && $duration<intval($block_config['min_duration']))
						{
							$errors['file']=3;
							$errors_async[]=array('error_field_name'=>'file','error_code'=>'duration_minimum','error_details'=>array(intval($block_config['min_duration'])),'block'=>'video_edit');
						} else {
							$max_duration_limit=intval($block_config['max_duration']);
							if ($user_status_id==3 && intval($block_config['max_duration_premium'])>0)
							{
								$max_duration_limit=intval($block_config['max_duration_premium']);
							} elseif ($user_status_id==6 && intval($block_config['max_duration_webmaster'])>0)
							{
								$max_duration_limit=intval($block_config['max_duration_webmaster']);
							}
							if ($max_duration_limit>0 && $duration>$max_duration_limit)
							{
								$errors['file']=4;
								$errors_async[]=array('error_field_name'=>'file','error_code'=>'duration_maximum','error_details'=>array($max_duration_limit),'block'=>'video_edit');
							} else {
								if ($options['VIDEOS_DUPLICATE_FILE_OPTION']>0)
								{
									$filekey=md5_file("$config[temporary_path]/$_POST[file_hash].tmp");

									$duplicate_video_id=mr2number(sql_pr("select video_id from $config[tables_prefix]videos where file_key=? limit 1",$filekey));
									if ($duplicate_video_id>0)
									{
										$errors['file']=5;
										$errors_async[]=array('error_field_name'=>'file','error_code'=>'duplicate','block'=>'video_edit');
									} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION']==2)
									{
										$duplicate_video_id=mr2number(sql_pr("select object_id from $config[tables_prefix]deleted_content where file_key=? limit 1",$filekey));
										if ($duplicate_video_id>0)
										{
											$errors['file']=5;
											$errors_async[]=array('error_field_name'=>'file','error_code'=>'duplicate','block'=>'video_edit');
										}
									}
								}
							}
						}
					}
				}
			}

			$antispam_action = process_antispam_rules(1, $_POST['title']);
			if ((isset($block_config['use_captcha']) || strpos($antispam_action, 'captcha') !== false) && !$is_trusted)
			{
				$recaptcha_data=null;
				if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
				{
					$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
				}
				if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
				{
					if ($_POST['g-recaptcha-response'] == '')
					{
						$errors['code']=1;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'video_edit');
					} elseif (!validate_recaptcha($_POST['g-recaptcha-response'], $recaptcha_data))
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'video_edit');
					}
				} else
				{
					if ($_POST['code'] == '')
					{
						$errors['code']=1;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'video_edit');
					} elseif ($_POST['code']<>$_SESSION['security_code_video_edit'] && $_POST['code']<>$_SESSION['security_code'])
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'video_edit');
					}
				}
			}
		}

		$now_date=date("Y-m-d H:i:s");
		if (!is_array($errors))
		{
			if (strpos($antispam_action, 'error') !== false)
			{
				sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on video from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));
				async_return_request_status(array(array('error_code' => 'spam', 'block' => 'video_edit')));
			}
			if (strpos($antispam_action, 'delete') !== false)
			{
				sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted video from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));

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

			$_POST['title']=process_blocked_words($_POST['title'],true);
			$_POST['description']=process_blocked_words($_POST['description'],true);
			$_POST['tags']=process_blocked_words($_POST['tags']);

			unset($_SESSION['security_code'], $_SESSION['security_code_video_edit']);

			$background_task_priority = intval($options['USER_TASKS_PRIORITY_STANDARD']);
			if ($user_status_id == 3)
			{
				$background_task_priority = intval($options['USER_TASKS_PRIORITY_PREMIUM']);
			} elseif ($user_status_id == 6)
			{
				$background_task_priority = intval($options['USER_TASKS_PRIORITY_WEBMASTER']);
			} elseif ($is_trusted)
			{
				$background_task_priority = intval($options['USER_TASKS_PRIORITY_TRUSTED']);
			}

			$status_id=1;
			if ($_POST['action']=='add_new_complete')
			{
				$_POST['dir']=get_correct_dir_name($_POST['title']);
				if ($_POST['dir']<>'')
				{
					$temp_dir=$_POST['dir'];
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=?",$temp_dir))==0)
						{
							$_POST['dir']=$temp_dir;break;
						}
						$temp_dir=$_POST['dir'].$i;
					}
				}

				if (intval($options['VIDEO_INITIAL_RATING'])>0)
				{
					$rating=intval($options['VIDEO_INITIAL_RATING']);
				} else {
					$rating=0;
				}

				if (!in_array(intval($_POST['is_private']),array(0,1,2)) || $user_id<1)
				{
					$_POST['is_private']=0;
				}

				if ($user_id<1)
				{
					$user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
					$username="Anonymous";
				}
				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				$dvd_id=0;
				if (intval($_POST['channel_id'])>0)
				{
					$dvd_id=intval($_POST['channel_id']);
				}
				if (intval($_POST['dvd_id'])>0)
				{
					$dvd_id=intval($_POST['dvd_id']);
				}

				if ($dvd_id == 0) {
					//// create new channel for user
					$dvds = mr2array_single(sql_pr("select dvd_id from $config[tables_prefix]dvds where user_id=? limit 1", $user_id));
					if ($dvds && $dvds['dvd_id']) {
						$dvd_id = $dvds['dvd_id'];
					} else {
						$dvd_id=sql_insert("insert into $config[tables_prefix]dvds set user_id=?, is_video_upload_allowed=?, is_review_needed=1, status_id=1, title=?, dir=?, description=?, tokens_required=0, rating=0, rating_amount=1, added_date=?",
						$user_id,1,"new channel","new-channel-".$user_id,"",date("Y-m-d H:i:s"));
					}
					
				}


				$item_id=sql_insert("insert into $config[tables_prefix]videos set is_review_needed=$is_review_needed, user_id=?, content_source_id=?, dvd_id=?, is_private=?, title=?, dir=?, description=?, status_id=3, load_type_id=1, duration=?, rating=?, rating_amount=1, ip=?, screen_main=1, added_date=?, post_date=added_date, last_time_view_date=added_date",
						$user_id,intval($_POST['content_source_id']),$dvd_id,intval($_POST['is_private']),trim($_POST['title']),trim($_POST['dir']),trim($_POST['description']),$duration,$rating,ip2int($_SERVER['REMOTE_ADDR']),$now_date);

				$update_array=array();
				if (isset($_POST['custom1']))
				{
					$update_array['custom1']=process_blocked_words($_POST['custom1'],true);
				}
				if (isset($_POST['custom2']))
				{
					$update_array['custom2']=process_blocked_words($_POST['custom2'],true);
				}
				if (isset($_POST['custom3']))
				{
					$update_array['custom3']=process_blocked_words($_POST['custom3'],true);
				}
				if (isset($_POST['release_year']))
				{
					$update_array['release_year']=intval($_POST['release_year']);
				}
				if (isset($_POST['access_level_id']) && in_array(intval($_POST['access_level_id']),array(1,2,3)))
				{
					$update_array['access_level_id']=intval($_POST['access_level_id']);
				}
				if (intval($block_config['set_custom_flag1'])>0)
				{
					$update_array['af_custom1']=intval($block_config['set_custom_flag1']);
				}
				if (intval($block_config['set_custom_flag2'])>0)
				{
					$update_array['af_custom2']=intval($block_config['set_custom_flag2']);
				}
				if (intval($block_config['set_custom_flag3'])>0)
				{
					$update_array['af_custom3']=intval($block_config['set_custom_flag3']);
				}
				if (isset($embed_info))
				{
					$update_array['embed']=$embed_info['embed'];
					$update_array['load_type_id']=3;
				}

				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_VIDEOS'])==1)
				{
					if (isset($_POST['tokens_required']))
					{
						$update_array['tokens_required']=intval($_POST['tokens_required']);
					}
				}

				if (count($update_array)>0)
				{
					sql_pr("update $config[tables_prefix]videos set ?% where video_id=?",$update_array,$item_id);
				}

				$dir_path = get_dir_by_id($item_id);

				if (!mkdir_recursive("$config[content_path_videos_sources]/$dir_path/$item_id"))
				{
					log_video("ERROR  Failed to create directory: $config[content_path_videos_sources]/$dir_path/$item_id", $item_id);
				}

				$screen_path = "$config[content_path_videos_sources]/$dir_path/$item_id/temp/screenshots/screenshot1.jpg";
				if ($_FILES['screenshot']['tmp_name'])
				{
					if (!mkdir_recursive(dirname($screen_path)))
					{
						log_video("ERROR  Failed to create directory: " . dirname($screen_path), $item_id);
					}
					if (!move_uploaded_file($_FILES['screenshot']['tmp_name'], $screen_path))
					{
						log_video("ERROR  Failed to move file to directory: $screen_path", $item_id);
					}
				} elseif (is_file("$config[temporary_path]/$_POST[file_hash].jpg"))
				{
					if (!mkdir_recursive(dirname($screen_path)))
					{
						log_video("ERROR  Failed to create directory: " . dirname($screen_path), $item_id);
					}
					if (!rename("$config[temporary_path]/$_POST[file_hash].jpg", $screen_path))
					{
						log_video("ERROR  Failed to move file to directory: $screen_path", $item_id);
					}
				}

				$source_file_name="$item_id.tmp";
				if (isset($block_config['upload_as_format']))
				{
					if (intval($_POST['is_private'])==2)
					{
						$formats_videos_postfixes=mr2array_list(sql_pr("select postfix from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=1"));
					} else {
						$formats_videos_postfixes=mr2array_list(sql_pr("select postfix from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=0"));
					}
					foreach ($formats_videos_postfixes as $postfix)
					{
						if (trim($block_config['upload_as_format'])==$postfix)
						{
							$source_file_name="$item_id{$postfix}";
						}
					}
				}

				if (!transfer_uploaded_file('file',"$config[content_path_videos_sources]/$dir_path/$item_id/$source_file_name"))
				{
					if (!isset($embed_info))
					{
						log_video("ERROR  Failed to move file to directory: $config[content_path_videos_sources]/$dir_path/$item_id/$source_file_name",$item_id);
					}
				}
				@unlink("$config[temporary_path]/$_POST[file_hash].embed");
				@unlink("$config[temporary_path]/$_POST[file_hash].jpg");

				if ((isset($block_config['force_inactive']) || strpos($antispam_action, 'deactivate') !== false) && !$is_trusted)
				{
					$status_id=0;
				}

				sql_pr("insert into $config[tables_prefix]users_events set event_type_id=1, user_id=?, video_id=?, added_date=?",$user_id,$item_id,$now_date);
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=140, object_id=?, object_type_id=1, added_date=?",$user_id,$username,$item_id,$now_date);

				$background_task=array();
				$background_task['status_id']=$status_id;
				$background_task['source']=$source_file_name;
				$background_task['duration']=$duration;
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=1, video_id=?, data=?, priority=?, added_date=?",$item_id,serialize($background_task),intval($background_task_priority),$now_date);
			} else {
				$item_id=intval($_REQUEST[$block_config['var_video_id']]);
				$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]videos where user_id=? and status_id in (0,1,2) and video_id=?",$user_id,$item_id));
				if ($old_data['user_id']<>$user_id || $old_data['is_locked']==1)
				{
					if ($_POST['mode']=='async')
					{
						if ($old_data['is_locked'])
						{
							async_return_request_status(array(array('error_code'=>'video_locked','block'=>'video_edit')));
						} else
						{
							async_return_request_status(array(array('error_code'=>'forbidden','block'=>'video_edit')));
						}
					} else {
						header("Location: $config[project_url]");die;
					}
				}

				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				$status_id=intval($old_data['status_id']);
				if (isset($block_config['force_inactive_on_edit']) && !$is_trusted)
				{
					$status_id=0;
				}

				sql_pr("update $config[tables_prefix]videos set is_review_needed=$is_review_needed, status_id=$status_id, $database_selectors[locale_field_title]=?, $database_selectors[locale_field_description]=? where video_id=?",
						$_POST['title'],$_POST['description'],$item_id);

				$update_array=array();
				if (isset($_POST['custom1']))
				{
					$update_array['custom1']=process_blocked_words($_POST['custom1'],true);
				}
				if (isset($_POST['custom2']))
				{
					$update_array['custom2']=process_blocked_words($_POST['custom2'],true);
				}
				if (isset($_POST['custom3']))
				{
					$update_array['custom3']=process_blocked_words($_POST['custom3'],true);
				}
				if (isset($_POST['release_year']))
				{
					$update_array['release_year']=intval($_POST['release_year']);
				}
				if (isset($_POST['is_private']))
				{
					if ($old_data['is_private']==2)
					{
						$_POST['is_private']=$old_data['is_private'];
					}
					if (in_array($old_data['is_private'],array(0,1)) && $_POST['is_private']==2)
					{
						$_POST['is_private']=$old_data['is_private'];
					}
					if (!in_array(intval($_POST['is_private']),array(0,1,2)))
					{
						$_POST['is_private']=$old_data['is_private'];
					}
					$update_array['is_private']=$_POST['is_private'];
				}
				if (isset($_POST['access_level_id']) && in_array(intval($_POST['access_level_id']),array(1,2,3)))
				{
					$update_array['access_level_id']=intval($_POST['access_level_id']);
				}
				if (isset($_POST['content_source_id']))
				{
					$update_array['content_source_id']=intval($_POST['content_source_id']);
				}
				if (isset($_POST['dvd_id']))
				{
					$update_array['dvd_id']=intval($_POST['dvd_id']);
				}

				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_VIDEOS'])==1)
				{
					if (isset($_POST['tokens_required']))
					{
						$update_array['tokens_required']=intval($_POST['tokens_required']);
					}
				}

				if (count($update_array)>0)
				{
					sql_pr("update $config[tables_prefix]videos set ?% where video_id=?",$update_array,$item_id);
				}

				// track changes
				$update_details='';
				if ($_POST['title']<>$old_data[$database_selectors['locale_field_title']]) {$update_details.="$database_selectors[locale_field_title], ";}
				if ($_POST['description']<>$old_data[$database_selectors['locale_field_description']]) {$update_details.="$database_selectors[locale_field_description], ";}
				if (intval($_POST['content_source_id'])<>intval($old_data['content_source_id'])) {$update_details.="content_source_id, ";}
				if (intval($_POST['is_private'])<>intval($old_data['is_private'])) {$update_details.="is_private, ";}
				if (isset($_POST['custom1']) && $_POST['custom1']<>$old_data['custom1']) {$update_details.="custom1, ";}
				if (isset($_POST['custom2']) && $_POST['custom2']<>$old_data['custom2']) {$update_details.="custom2, ";}
				if (isset($_POST['custom3']) && $_POST['custom3']<>$old_data['custom3']) {$update_details.="custom3, ";}
				if (intval($status_id)<>intval($old_data['status_id'])) {$update_details.="status_id, ";}

				if ($update_details !== '')
				{
					$update_details=substr($update_details,0, -2);
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=1, action_details=?, added_date=?",$user_id,$username,$item_id,$update_details,$now_date);
				// end track changes

				if ($update_array['dvd_id']>0)
				{
					require_once "$config[project_path]/admin/include/functions_admin.php";
					update_dvds_videos_totals(array($update_array['dvd_id']));
				}

				if (!isset($block_config['forbid_change_screenshots']))
				{
					$dir_path = get_dir_by_id($item_id);
					$screen_main = $old_data['screen_main'];
					if ((intval($_POST['main_screenshot']) > 0 && intval($_POST['main_screenshot']) <= $old_data['screen_amount'] && intval($_POST['main_screenshot']) != $screen_main) || $_FILES['screenshot']['tmp_name'])
					{
						log_video('', $item_id);
						log_video("INFO  Changing overview screenshots on site", $item_id);
					}
					if (intval($_POST['main_screenshot']) > 0 && intval($_POST['main_screenshot']) <= $old_data['screen_amount'] && intval($_POST['main_screenshot']) != $screen_main)
					{
						$screen_main = intval($_POST['main_screenshot']);
						log_video("INFO  Changing main screenshot from #{$old_data['screen_main']} to #$screen_main", $item_id);
						sql_pr("update $config[tables_prefix]videos set screen_main=? where video_id=?", $screen_main, $item_id);
					}

					$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$item_id/screenshots";
					if ($_FILES['screenshot']['tmp_name'])
					{
						$screen_source_path = "$screen_source_dir/$screen_main.jpg";

						$rnd = mt_rand(10000000, 99999999);
						mkdir_recursive("$config[temporary_path]/$rnd");
						$screen_restore_files = [];

						try
						{
							$content_source_id = $update_array['content_source_id'] ?? $old_data['content_source_id'];
							$custom_crop_options = '';
							if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0 && $content_source_id > 0)
							{
								$content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $content_source_id));
								$custom_crop_options = $content_source["custom{$options['SCREENSHOTS_CROP_CUSTOMIZE']}"];
							}

							log_video("INFO  Replacing main screenshot by manual upload", $item_id);

							if (!mkdir_recursive($screen_source_dir))
							{
								throw new RuntimeException("Failed to create directory: $screen_source_dir");
							}
							if (@copy($screen_source_path, "$config[temporary_path]/$rnd/source.jpg"))
							{
								$screen_restore_files[] = ['to' => $screen_source_path, 'from' => "$config[temporary_path]/$rnd/source.jpg"];
							}

							if (!move_uploaded_file($_FILES['screenshot']['tmp_name'], $screen_source_path))
							{
								throw new RuntimeException("Failed to move file to directory: $screen_source_path");
							}
							@chmod($screen_source_path, 0666);

							$exec_res = process_screen_source($screen_source_path, $options, true, $custom_crop_options);
							if ($exec_res)
							{
								throw new RuntimeException("IM operation failed: $exec_res");
							}

							$list_formats_overview = mr2array(sql_pr("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
							foreach ($list_formats_overview as $format)
							{
								$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$item_id/$format[size]";
								if (!mkdir_recursive($screen_target_dir))
								{
									throw new RuntimeException("Failed to create directory: $screen_target_dir");
								}

								$screen_target_path = "$screen_target_dir/$screen_main.jpg";
								if (@copy($screen_target_path, "$config[temporary_path]/$rnd/$format[size].jpg"))
								{
									$screen_restore_files[] = ['to' => $screen_target_path, 'from' => "$config[temporary_path]/$rnd/$format[size].jpg"];
								}

								$exec_res = make_screen_from_source($screen_source_path, $screen_target_path, $format, $options, true);
								if ($exec_res)
								{
									throw new RuntimeException("IM operation failed: $exec_res");
								}

								if ($format['is_create_zip'] == 1)
								{
									require_once "$config[project_path]/admin/include/pclzip.lib.php";

									$zip_target_path = "$screen_target_dir/$item_id-$format[size].zip";
									if (@copy($zip_target_path, "$config[temporary_path]/$rnd/$item_id-$format[size].zip"))
									{
										$screen_restore_files[] = ['to' => $zip_target_path, 'from' => "$config[temporary_path]/$rnd/$item_id-$format[size].zip"];
									}

									$zip_files_to_add = [];
									for ($i = 1; $i <= $old_data['screen_amount']; $i++)
									{
										$zip_files_to_add[] = "$screen_target_dir/$i.jpg";
									}
									$zip = new PclZip($zip_target_path);
									$zip->create($zip_files_to_add, $p_add_dir = '', $p_remove_dir = $screen_target_dir);
								}
							}

							$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];
							$screenshots_data[$screen_main] = ['type' => 'uploaded', 'filesize' => filesize($screen_source_path)];
							file_put_contents("$screen_source_dir/info.dat", @serialize($screenshots_data));

							if (is_file("$screen_source_dir/rotator.dat"))
							{
								$rotator_data = @unserialize(file_get_contents("$screen_source_dir/rotator.dat"));
								if (isset($rotator_data[$screen_main]))
								{
									unset($rotator_data[$screen_main]);
									file_put_contents("$screen_source_dir/rotator.dat", @serialize($rotator_data));
								}
							}
						} catch (Exception $e)
						{
							log_video('ERROR ' . $e->getMessage(), $item_id);
							log_video('ERROR Error during screenshots creation, stopping further processing', $item_id);

							// restore all original screenshot files
							foreach ($screen_restore_files as $screen_restore_file)
							{
								if (is_file($screen_restore_file['from']))
								{
									@copy($screen_restore_file['from'], $screen_restore_file['to']);
								}
							}
						}
						rmdir_recursive("$config[temporary_path]/$rnd");
					}

					if ($_FILES['screenshot']['tmp_name'] || $screen_main != $old_data['screen_main'])
					{
						$video_formats = get_video_formats($item_id, $old_data['file_formats']);
						copy("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/$screen_main.jpg", "$config[content_path_videos_screenshots]/$dir_path/$item_id/preview.jpg");
						foreach ($video_formats as $format)
						{
							resize_image('need_size_no_composite', "$config[content_path_videos_screenshots]/$dir_path/$item_id/preview.jpg", "$config[content_path_videos_screenshots]/$dir_path/$item_id/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
						}

						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=153, object_id=?, object_type_id=1, action_details=?, added_date=?", $user_id, $username, $item_id, 'screen_main', $now_date);
						log_video("INFO  Done screenshots changes", $item_id);
					}
				}

				if ($old_data['is_private']<>intval($_POST['is_private']))
				{
					if ($old_data['relative_post_date']==0)
					{
						if ($old_data['is_private']==1)
						{
							sql_pr("insert into $config[tables_prefix]users_events set event_type_id=7, user_id=?, video_id=?, added_date=?",$user_id,$item_id,$now_date);
						} elseif (intval($_POST['is_private'])==1) {
							sql_pr("insert into $config[tables_prefix]users_events set event_type_id=6, user_id=?, video_id=?, added_date=?",$user_id,$item_id,$now_date);
						}
					}
				}

				sql_pr("update $config[tables_prefix]users set
						public_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_videos_count=(select count(*) from $config[tables_prefix]videos where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_videos_count=public_videos_count+private_videos_count+premium_videos_count
					where user_id=?",$user_id
				);
			}

			sql_pr("delete from $config[tables_prefix]tags_videos where video_id=?",$item_id);
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
						sql_pr("insert into $config[tables_prefix]tags_videos set tag_id=?, video_id=?",$tag_id,$item_id);
						$inserted_tags[]=mb_lowercase($tag);
					}
				}
			}

			sql_pr("delete from $config[tables_prefix]categories_videos where video_id=?",$item_id);
			foreach ($_POST['category_ids'] as $category_id)
			{
				if (intval($category_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?",$category_id,$item_id);
				}
			}

			sql_pr("delete from $config[tables_prefix]models_videos where video_id=?",$item_id);
			foreach ($_POST['model_ids'] as $model_id)
			{
				if (intval($model_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]models_videos set model_id=?, video_id=?",$model_id,$item_id);
				}
			}

			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				if ($status_id==0)
				{
					$smarty->assign('force_inactive',1);
				}

				$video_data=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where video_id=?",$item_id));
				if ($video_data['video_id']>0)
				{
					$video_url="$config[project_url]/".str_replace("%ID%",$video_data['video_id'],str_replace("%DIR%",$video_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
					$smarty->assign('async_object_data',$video_data);
					$smarty->assign('async_object_url',$video_url);
				}
				return '';
			} else {
				if ($_POST['action']=='add_new_complete')
				{
					if ($block_config['redirect_on_new_done']<>'')
					{
						$force_inactive_param='';
						if ($status_id==0)
						{
							$force_inactive_param="&force_inactive=1";
						}
						$url=process_url($block_config['redirect_on_new_done']);
						header("Location: $url?action=upload_done{$force_inactive_param}");die;
					} else
					{
						header("Location: ?action=upload_done");die;
					}
				} else
				{
					if ($block_config['redirect_on_change_done']<>'')
					{
						$force_inactive_param='';
						if ($status_id==0)
						{
							$force_inactive_param="&force_inactive=1";
						}
						$url=process_url($block_config['redirect_on_change_done']);
						header("Location: $url?action=change_done{$force_inactive_param}");die;
					} else
					{
						header("Location: ?action=change_done");die;
					}
				}
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	if (intval($_REQUEST[$block_config['var_video_id']])>0)
	{
		$item_id=intval($_REQUEST[$block_config['var_video_id']]);
		$_POST=mr2array_single(sql_pr("select $database_selectors[videos] from $config[tables_prefix]videos where user_id=? and status_id in (0,1,2) and video_id=?",$user_id,$item_id));
		if (count($_POST)>0)
		{
			$_POST['block_uid']=$object_id;

			$_POST['time_passed_from_adding']=get_time_passed($_POST['post_date']);
			$_POST['duration_array']=get_duration_splitted($_POST['duration']);
			$_POST['file_size_human_string']=sizeToHumanString($_POST['file_size'],2);
			$_POST['tags']=implode(", ",mr2array_list(sql_pr("select (select $database_selectors[generic_selector_tag] from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) as tag from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc",$item_id)));
			$_POST['category_ids']=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_videos where video_id=? order by id asc",$item_id));
			$_POST['model_ids']=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_videos where video_id=? order by id asc",$item_id));

			$_POST['stats']=mr2array(sql_pr("select added_date, viewed, unique_viewed from $config[tables_prefix]stats_videos where video_id=? order by added_date asc",$item_id));

			$video_id=$_POST['video_id'];
			$dir_path=get_dir_by_id($video_id);
			$formats=get_video_formats($video_id,$_POST['file_formats']);
			$allowed_postfixes=video_editGetAllowedPostfixesForCurrentUser();

			$max_size=0;
			foreach ($formats as $format_rec)
			{
				if (!in_array($format_rec['postfix'],$allowed_postfixes))
				{
					continue;
				}
				if (!in_array(end(explode(".",$format_rec['postfix'])),explode(",",$config['player_allowed_ext'])))
				{
					continue;
				}
				if ($max_size==0 || $format_rec['file_size']>$max_size)
				{
					$max_size=$format_rec['file_size'];
					$display_format=$format_rec;
				}
			}

			$_POST['preview_url']="$config[content_url_videos_screenshots]/$dir_path/$video_id/preview.jpg?rnd=".time();
			if (isset($display_format))
			{
				$hash=md5($config['cv']."$dir_path/$video_id/$video_id{$display_format['postfix']}");
				$_POST['file_path']="$hash/$dir_path/$video_id/$video_id{$display_format['postfix']}";
				$_POST['file_dimensions']=$display_format['dimensions'];
				$_POST['file_duration']=$display_format['duration'];
				$_POST['file_duration_string']=$display_format['duration_string'];
				$_POST['file_size']=$display_format['file_size'];
				$_POST['file_size_string']=$display_format['file_size_string'];
			} else {
				unset($_POST['file_path']);
				$_POST['file_dimensions']=explode('x',$_POST['file_dimensions']);
				$_POST['file_duration']=$_POST['duration'];
				$_POST['file_duration_string']=durationToHumanString($_POST['duration']);
				$_POST['file_size_string']=sizeToHumanString($_POST['file_size'],2);
			}
			$_POST['screen_url']="$config[content_url_videos_screenshots]/$dir_path/$video_id";

			$_POST['screenshot_sources'] = [];
			for ($i = 1; $i <= $_POST['screen_amount']; $i++)
			{
				$_POST['screenshot_sources'][] = get_video_source_url($_POST['video_id'], "screenshots/$i.jpg");
			}

			$_POST['poster_sources'] = [];
			for ($i = 1; $i <= $_POST['poster_amount']; $i++)
			{
				$_POST['poster_sources'][] = get_video_source_url($_POST['video_id'], "posters/$i.jpg");
			}

			$_POST['view_page_url']="$config[project_url]/".str_replace("%ID%",$_POST['video_id'],str_replace("%DIR%",$_POST['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));

			$storage[$object_id]['video_title']=$_POST['title'];
			$storage[$object_id]['video_id']=$_POST['video_id'];
		} else {
			return "status_404";
		}
	} elseif ($_REQUEST['file']!='')
	{
		$uploaded_file_key = $_REQUEST['file'];
		if (preg_match('/^([0-9A-Za-z]{32})$/', $uploaded_file_key))
		{
			$uploaded_file_info=array();
			if (is_file("$config[temporary_path]/$uploaded_file_key.tmp"))
			{
				$uploaded_file_info = array(
					'size' => sprintf("%.0f", filesize("$config[temporary_path]/$uploaded_file_key.tmp")),
					'size_string' => sizeToHumanString(sprintf("%.0f", filesize("$config[temporary_path]/$uploaded_file_key.tmp")), 2),
					'duration' => get_video_duration("$config[temporary_path]/$uploaded_file_key.tmp"),
					'duration_string' => durationToHumanString(get_video_duration("$config[temporary_path]/$uploaded_file_key.tmp")),
					'dimensions' => get_video_dimensions("$config[temporary_path]/$uploaded_file_key.tmp")
				);
			} elseif (is_file("$config[temporary_path]/$uploaded_file_key.embed"))
			{
				$embed_info = @unserialize(file_get_contents("$config[temporary_path]/$uploaded_file_key.embed"));
				$embed_image_size = getimagesize("$config[temporary_path]/$uploaded_file_key.jpg");

				preg_match("|width\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $embed_info['embed'], $temp);
				$embed_width = trim($temp[1]);

				preg_match("|height\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $embed_info['embed'], $temp);
				$embed_height = trim($temp[1]);

				if (intval($embed_width) == 0 || intval($embed_height) == 0)
				{
					$embed_width = $embed_image_size[0];
					$embed_height = $embed_image_size[1];
				}

				if (is_array($embed_info))
				{
					$uploaded_file_info = array(
						'embed' => $embed_info['embed'],
						'size' => 0,
						'size_string' => sizeToHumanString(0, 2),
						'duration' => $embed_info['duration'],
						'duration_string' => durationToHumanString($embed_info['duration']),
						'dimensions' => array($embed_width, $embed_height)
					);
				}
			}
			$smarty->assign('uploaded_file_info', $uploaded_file_info);
		}
		$smarty->assign('uploaded_file_key', $uploaded_file_key);
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

	$list_dvds=mr2array(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where is_video_upload_allowed=0 or user_id=? or (is_video_upload_allowed=1 and (user_id in (select user_id from $config[tables_prefix]friends where is_approved=1 and friend_id=?) or user_id in (select friend_id from $config[tables_prefix]friends where is_approved=1 and user_id=?))) order by title asc",$user_id,$user_id,$user_id));
	$list_dvds_groups=mr2array(sql_pr("select $database_selectors[dvds_groups] from $config[tables_prefix]dvds_groups where dvd_group_id in (select dvd_group_id from $config[tables_prefix]dvds where is_video_upload_allowed=0 or user_id=? or (is_video_upload_allowed=1 and (user_id in (select user_id from $config[tables_prefix]friends where is_approved=1 and friend_id=?) or user_id in (select friend_id from $config[tables_prefix]friends where is_approved=1 and user_id=?)))) order by title asc",$user_id,$user_id,$user_id));
	$list_dvds_ungrouped=array();
	foreach ($list_dvds as $dvd)
	{
		if ($dvd['dvd_group_id']>0)
		{
			foreach ($list_dvds_groups as $k=>$group)
			{
				if ($dvd['dvd_group_id']==$group['dvd_group_id'])
				{
					$list_dvds_groups[$k]['dvds'][]=$dvd;
					break;
				}
			}
		} else
		{
			$list_dvds_ungrouped[]=$dvd;
		}
	}

	if (intval($_REQUEST['dvd_id'])>0 || intval($_POST['dvd_id'])>0 || intval($_REQUEST['channel_id'])>0)
	{
		$dvd_id=intval($_REQUEST['dvd_id']);
		if ($dvd_id==0)
		{
			$dvd_id=intval($_POST['dvd_id']);
		}
		if ($dvd_id==0)
		{
			$dvd_id=intval($_REQUEST['channel_id']);
		}

		$dvd_info=mr2array_single(sql_pr("select $database_selectors[dvds] from $config[tables_prefix]dvds where dvd_id=?",$dvd_id));
		$smarty->assign('dvd',$dvd_info);
		if (video_editIsDvdAllowedForUpload($dvd_info))
		{
			$smarty->assign('dvd_forbidden',0);
			$smarty->assign('channel',$dvd_info);
		} else {
			$smarty->assign('dvd_forbidden',1);
			$smarty->assign('channel_forbidden',1);
		}
	}

	$smarty->assign('errors',$errors);
	$smarty->assign('list_categories',$list_categories);
	$smarty->assign('list_categories_groups',$list_categories_groups);
	$smarty->assign('list_categories_ungrouped',$list_categories_ungrouped);
	$smarty->assign('list_dvds',$list_dvds);
	$smarty->assign('list_dvds_groups',$list_dvds_groups);
	$smarty->assign('list_dvds_ungrouped',$list_dvds_ungrouped);
	$smarty->assign('list_models',mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models order by title asc")));
	$smarty->assign('list_tags',mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags order by tag asc limit 500")));
	$smarty->assign('screenshot_size',"$min_image_size[0]x$min_image_size[1]");

	if ($user_content_source_group_id>0)
	{
		$smarty->assign('list_content_sources',mr2array(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where content_source_group_id=? order by title asc",$user_content_source_group_id)));
	} else {
		$smarty->assign('list_content_sources',mr2array(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources order by title asc")));
	}

	if (intval($block_config['min_duration'])>0)
	{
		$smarty->assign('min_duration',intval($block_config['min_duration']));
	}
	$max_duration_limit=intval($block_config['max_duration']);
	if ($user_status_id==3 && intval($block_config['max_duration_premium'])>0)
	{
		$max_duration_limit=intval($block_config['max_duration_premium']);
	} elseif ($user_status_id==6 && intval($block_config['max_duration_webmaster'])>0)
	{
		$max_duration_limit=intval($block_config['max_duration_webmaster']);
	}
	if ($max_duration_limit>0)
	{
		$smarty->assign('max_duration',$max_duration_limit);
	}

	if (intval($_POST['video_id'])>0)
	{
		if (isset($block_config['forbid_change']))
		{
			$smarty->assign('change_forbidden', 1);
		} elseif ($_POST['is_locked'] == 1)
		{
			$smarty->assign('change_forbidden', 1);
		}
		if (isset($block_config['forbid_change_screenshots']))
		{
			$smarty->assign('change_screenshots_forbidden', 1);
		}
	} else
	{
		$antispam_action = process_antispam_rules(1);
		if ((isset($block_config['use_captcha']) || strpos($antispam_action, 'captcha') !== false) && !$is_trusted)
		{
			$smarty->assign('use_captcha',1);
		}
	}

	if (isset($block_config['optional_description']))
	{
		$smarty->assign('optional_description',1);
	}
	if (isset($block_config['optional_tags']))
	{
		$smarty->assign('optional_tags',1);
	}
	if (isset($block_config['optional_categories']))
	{
		$smarty->assign('optional_categories',1);
	}

	$max_categories=intval($block_config['max_categories']);
	if ($max_categories==0)
	{
		$max_categories=3;
	}
	$smarty->assign('max_categories',$max_categories);

	if (isset($block_config['allow_embed']))
	{
		$smarty->assign('allow_embed',1);
	}

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['ENABLE_TOKENS_SALE_VIDEOS'])==1)
	{
		$smarty->assign('allow_tokens',1);
		$smarty->assign('tokens_price_default',intval($memberzone_data['DEFAULT_TOKENS_STANDARD_VIDEO']));
		$smarty->assign('tokens_period_default',intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']));
		$smarty->assign('tokens_commission',intval($memberzone_data['TOKENS_SALE_INTEREST']));
	}

	$file_upload_data=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"));
	if ($file_upload_data['FILE_UPLOAD_URL_OPTION']=='public' || ($file_upload_data['FILE_UPLOAD_URL_OPTION']=='members' && $user_id>0))
	{
		$smarty->assign('allow_url',1);
	}

	return '';
}

function video_editGetHash($block_config)
{
	return "nocache";
}

function video_editCacheControl($block_config)
{
	return "nocache";
}

function video_editAsync($block_config)
{
	global $config,$page_id,$block,$lang;

	if ($_REQUEST['action']=='upload_popup' || $_REQUEST['action']=='upload_popup_url')
	{
		require_once "$config[project_path]/admin/include/setup_smarty_site.php";

		$smarty=new mysmarty_site();

		$smarty->assign('config',$config);
		if (is_array($lang))
		{
			$smarty->assign("lang",$lang);
		}
		$smarty->display("blocks/$page_id/$block.tpl");
		die;
	} elseif ($_REQUEST['action']=='upload_file')
	{
		$errors_async = null;

		require_once "$config[project_path]/admin/include/functions_base.php";
		require_once "$config[project_path]/admin/include/functions.php";

		$upload_field_name = 'content';
		if ($_REQUEST['upload_option'] == 'url')
		{
			$upload_field_name = 'url';
		} elseif ($_REQUEST['upload_option'] == 'embed' && isset($block_config['allow_embed']))
		{
			$upload_field_name = 'embed';
		}

		if ($_REQUEST['filename'] == '')
		{
			if (count($_POST) == 0)
			{
				$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'filesize_limit', 'block' => 'video_edit');
			} else
			{
				$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'required', 'block' => 'video_edit');
			}
			async_return_request_status($errors_async);
		} elseif (!preg_match('/^([0-9A-Za-z]{32})$/', $_REQUEST['filename']))
		{
			async_return_request_status(array(array('error_code' => 'invalid_params', 'block' => 'video_edit')));
		}

		if ($upload_field_name == 'content' || $upload_field_name == 'url')
		{
			require "$config[project_path]/admin/include/uploader.php";

			if ($upload_field_name == 'url')
			{
				$upload_result = uploader_upload_remote_file(trim($_REQUEST['filename']), trim($_REQUEST['url']), !isset($_REQUEST['upload_v2']));
			} else
			{
				$upload_result = uploader_upload_local_file(trim($_REQUEST['filename']), $_FILES['content'], trim($_REQUEST['size']), intval($_REQUEST['chunks']), intval($_REQUEST['index']));
			}

			if (strpos($upload_result, 'ok_chunk') === 0)
			{
				async_return_request_status(null, null, ['state' => 'uploading', 'percent' => substr($upload_result, 9)]);
			} elseif ($upload_result == 'ktfudc_notallowed_error')
			{
				async_return_request_status(array(array('error_code' => 'not_logged_in', 'block' => 'video_edit')));
			} elseif ($upload_result == 'ktfudc_filesize_error')
			{
				async_return_request_status(array(array('error_field_name' => $upload_field_name, 'error_code' => 'filesize_limit', 'block' => 'video_edit')));
			} elseif ($upload_result == 'ktfudc_url_error')
			{
				async_return_request_status(array(array('error_field_name' => $upload_field_name, 'error_code' => 'invalid', 'block' => 'video_edit')));
			} elseif ($upload_result == 'ktfudc_unexpected_error')
			{
				async_return_request_status(array(array('error_code' => 'invalid_params', 'block' => 'video_edit')));
			}
		}

		$embed_duration = 0;
		if ($upload_field_name == 'embed')
		{
			if ($_REQUEST["embed"] == '')
			{
				$errors_async[] = array('error_field_name' => 'embed', 'error_code' => 'required', 'block' => 'video_edit');
			} else
			{
				$embed_url = "";
				if (preg_match("|src\ *=\ *['\"]?([^'\"\ ]+)['\"]?|is", $_REQUEST['embed'], $temp))
				{
					$embed_url = trim($temp[1]);
				}
				if ($embed_url == '' || !is_url($embed_url))
				{
					$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'invalid', 'block' => 'video_edit');
				} else
				{
					if (isset($block_config['allow_embed_domains']))
					{
						$embed_domain = parse_url(str_replace("www.", "", $embed_url), PHP_URL_HOST);
						if (!$embed_domain)
						{
							$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'invalid', 'block' => 'video_edit');
						} else
						{
							$embed_allowed_domains = explode(',', $block_config['allow_embed_domains']);
							$embed_is_allowed = false;
							foreach ($embed_allowed_domains as $embed_allowed_domain)
							{
								$embed_allowed_domain = trim($embed_allowed_domain);
								if (strpos($embed_allowed_domain, '*') !== false)
								{
									$regexp = str_replace(array(".", "*"), array("\\.", "[^.]*"), $embed_allowed_domain);
									if (preg_match("|^$regexp$|is", $embed_domain))
									{
										$embed_is_allowed = true;
										break;
									}
								} elseif ($embed_domain == $embed_allowed_domain)
								{
									$embed_is_allowed = true;
									break;
								}
							}
							if (!$embed_is_allowed)
							{
								$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'domain_forbidden', 'error_details' => array($embed_domain), 'block' => 'video_edit');
							}
						}
					}

					$similar_embed_codes = mr2array_list(sql_pr("select embed from $config[tables_prefix]videos where embed like ?", "%$embed_url%"));
					foreach ($similar_embed_codes as $similar_embed_code)
					{
						if (preg_match("|src\ *=\ *['\"]?([^'\"\ ]+)['\"]?|is", $similar_embed_code, $temp))
						{
							if ($embed_url == trim($temp[1]))
							{
								$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'duplicate', 'block' => 'video_edit');
								break;
							}
						}
					}
				}
			}

			if ($_REQUEST["duration"] == '')
			{
				$errors_async[] = array('error_field_name' => 'duration', 'error_code' => 'required', 'block' => 'video_edit');
			} else
			{
				if (strpos($_POST['duration'], ":") !== false)
				{
					$temp = explode(":", $_REQUEST['duration']);
					if (count($temp) == 3)
					{
						$embed_duration = intval($temp[0]) * 3600 + intval($temp[1]) * 60 + intval($temp[2]);
					} else
					{
						$embed_duration = intval($temp[0]) * 60 + intval($temp[1]);
					}
				} else
				{
					$embed_duration = intval($_REQUEST['duration']);
				}
				if ($embed_duration == 0)
				{
					$errors_async[] = array('error_field_name' => 'duration', 'error_code' => 'invalid', 'block' => 'video_edit');
				}
			}
			if ($_FILES['screenshot']['tmp_name'] == '')
			{
				$errors_async[] = array('error_field_name' => 'screenshot', 'error_code' => 'required', 'block' => 'video_edit');
			} else
			{
				$ext = strtolower(end(explode(".", $_FILES['screenshot']['name'])));
				if (strpos($ext, "?") !== false)
				{
					$ext = substr($ext, 0, strpos($ext, "?"));
				}
				if ($ext != 'jpg' && $ext != 'jpeg')
				{
					$errors_async[] = array('error_field_name' => 'screenshot', 'error_code' => 'invalid_format', 'block' => 'video_edit');
				} else
				{
					$min_image_size = array(0, 0);
					$sizes = mr2array_list(sql_pr("select size from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
					foreach ($sizes as $size)
					{
						$temp_size = explode("x", $size);
						if (intval($temp_size[0]) > $min_image_size[0])
						{
							$min_image_size[0] = intval($temp_size[0]);
						}
						if (intval($temp_size[1]) > $min_image_size[1])
						{
							$min_image_size[1] = intval($temp_size[1]);
						}
					}

					$size = getimagesize($_FILES['screenshot']['tmp_name']);
					if ($size[0] < $min_image_size[0] || $size[1] < $min_image_size[1])
					{
						$errors_async[] = array('error_field_name' => 'screenshot', 'error_code' => 'invalid_size', 'error_details' => array("$min_image_size[0]x$min_image_size[1]"), 'block' => 'video_edit');
					}
				}
			}
		} else
		{
			$status_file = @file_get_contents("$config[temporary_path]/$_REQUEST[filename].status");
			if (strpos($status_file, "ktfudc_filesize_error") !== false)
			{
				$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'filesize_limit', 'block' => 'video_edit');
			} elseif (strpos($status_file, "ktfudc_url_error") !== false)
			{
				$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'invalid', 'block' => 'video_edit');
			} elseif (strpos($status_file, "ktfudc_unexpected_error") !== false)
			{
				$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'unknown_error', 'block' => 'video_edit');
			} else
			{
				$duration = get_video_duration("$config[temporary_path]/$_REQUEST[filename].tmp");
				if ($duration < 1)
				{

					$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'invalid_format', 'error_details' => array(str_replace(',', ', ', $config['video_allowed_ext'])), 'block' => 'video_edit');
				} else
				{
					if (intval($block_config['min_duration']) > 0 && $duration < intval($block_config['min_duration']))
					{
						$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'duration_minimum', 'error_details' => array(intval($block_config['min_duration'])), 'block' => 'video_edit');
					} else
					{
						$max_duration_limit = intval($block_config['max_duration']);
						if ($_SESSION['status_id'] == 3 && intval($block_config['max_duration_premium']) > 0)
						{
							$max_duration_limit = intval($block_config['max_duration_premium']);
						} elseif ($_SESSION['status_id'] == 6 && intval($block_config['max_duration_webmaster']) > 0)
						{
							$max_duration_limit = intval($block_config['max_duration_webmaster']);
						}
						if ($max_duration_limit > 0 && $duration > $max_duration_limit)
						{
							$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'duration_maximum', 'error_details' => array($max_duration_limit), 'block' => 'video_edit');
						} else
						{
							$options = get_options(array('VIDEOS_DUPLICATE_FILE_OPTION'));
							if ($options['VIDEOS_DUPLICATE_FILE_OPTION'] > 0)
							{
								$filekey = md5_file("$config[temporary_path]/$_REQUEST[filename].tmp");

								$duplicate_video_id = mr2number(sql_pr("select video_id from $config[tables_prefix]videos where file_key=? limit 1", $filekey));
								if ($duplicate_video_id > 0)
								{
									$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'duplicate', 'block' => 'video_edit');
								} elseif ($options['VIDEOS_DUPLICATE_FILE_OPTION'] == 2)
								{
									$duplicate_video_id = mr2number(sql_pr("select object_id from $config[tables_prefix]deleted_content where file_key=? limit 1", $filekey));
									if ($duplicate_video_id > 0)
									{
										$errors_async[] = array('error_field_name' => $upload_field_name, 'error_code' => 'duplicate', 'block' => 'video_edit');
									}
								}
							}
						}
					}
				}
			}
		}
		if (!is_array($errors_async))
		{
			if ($upload_field_name == 'embed')
			{
				$embed_array = array('embed' => $_REQUEST["embed"], 'duration' => $embed_duration);
				file_put_contents("$config[temporary_path]/$_REQUEST[filename].embed", serialize($embed_array));
				move_uploaded_file($_FILES['screenshot']['tmp_name'], "$config[temporary_path]/$_REQUEST[filename].jpg");
			}

			$uploaded_file_info = array('filename' => $_REQUEST['filename']);

			if (is_file("$config[temporary_path]/$_REQUEST[filename].tmp"))
			{
				$uploaded_file_info = array(
						'filename' => $_REQUEST['filename'],
						'size' => sprintf("%.0f", filesize("$config[temporary_path]/$_REQUEST[filename].tmp")),
						'size_string' => sizeToHumanString(sprintf("%.0f", filesize("$config[temporary_path]/$_REQUEST[filename].tmp")), 2),
						'duration' => get_video_duration("$config[temporary_path]/$_REQUEST[filename].tmp"),
						'duration_string' => durationToHumanString(get_video_duration("$config[temporary_path]/$_REQUEST[filename].tmp")),
						'dimensions' => get_video_dimensions("$config[temporary_path]/$_REQUEST[filename].tmp")
				);
			} elseif (is_file("$config[temporary_path]/$_REQUEST[filename].embed"))
			{
				$embed_info = @unserialize(file_get_contents("$config[temporary_path]/$_REQUEST[filename].embed"));
				$embed_image_size = getimagesize("$config[temporary_path]/$_REQUEST[filename].jpg");

				preg_match("|width\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $embed_info['embed'], $temp);
				$embed_width = trim($temp[1]);

				preg_match("|height\ *=\ *['\"]?\ *([0-9]+)\ *['\"]?|is", $embed_info['embed'], $temp);
				$embed_height = trim($temp[1]);

				if (intval($embed_width) == 0 || intval($embed_height) == 0)
				{
					[$embed_width, $embed_height] = $embed_image_size;
				}

				if (is_array($embed_info))
				{
					$uploaded_file_info = array(
							'filename' => $_REQUEST['filename'],
							'embed' => $embed_info['embed'],
							'size' => 0,
							'size_string' => sizeToHumanString(0, 2),
							'duration' => $embed_info['duration'],
							'duration_string' => durationToHumanString($embed_info['duration']),
							'dimensions' => array($embed_width, $embed_height)
					);
				}
			}

			async_return_request_status(null, null, $uploaded_file_info);
		} else
		{
			async_return_request_status($errors_async);
		}
	} elseif ($_REQUEST['action']=='video_preview')
	{
		if ($_SESSION['user_id']<1 && !isset($block_config['allow_anonymous']))
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'video_edit')));
		}

		if ($_REQUEST['file']!='')
		{
			$uploaded_file_key=$_REQUEST['file'];
			if (preg_match('/^([0-9A-Za-z]{32})$/',$uploaded_file_key))
			{
				if (is_file("$config[temporary_path]/$uploaded_file_key.tmp"))
				{
					require_once "$config[project_path]/admin/include/functions_base.php";
					require_once "$config[project_path]/admin/include/functions.php";

					$first_screen_offset=mr2number(sql_pr("select value from $config[tables_prefix]options where variable='SCREENSHOTS_SECONDS_OFFSET'"));
					if ($first_screen_offset<=0) {
						$first_screen_offset=1;
					}

					if (!is_video_secure("$config[temporary_path]/$uploaded_file_key.tmp"))
					{
						die;
					}

					$video_duration = get_video_duration("$config[temporary_path]/$uploaded_file_key.tmp");
					if ($first_screen_offset > $video_duration) {
						$first_screen_offset = 1;
					}

					exec("$config[ffmpeg_path] -ss $first_screen_offset -i $config[temporary_path]/$uploaded_file_key.tmp -vframes 1 -y -f mjpeg -vf \"scale=trunc(iw*sar/2)*2:ih\" $config[temporary_path]/{$uploaded_file_key}_preview.jpg");
					if (is_file("$config[temporary_path]/{$uploaded_file_key}_preview.jpg") && filesize("$config[temporary_path]/{$uploaded_file_key}_preview.jpg")>0)
					{
						header("Content-Type: image/jpeg");
						header("Content-Length: ".filesize("$config[temporary_path]/{$uploaded_file_key}_preview.jpg"));
						ob_end_clean();
						readfile("$config[temporary_path]/{$uploaded_file_key}_preview.jpg");
					}
				} else {
					header("Content-Type: image/jpeg");
					header("Content-Length: ".filesize("$config[temporary_path]/$uploaded_file_key.jpg"));
					ob_end_clean();
					readfile("$config[temporary_path]/$uploaded_file_key.jpg");
				}
				die;
			}
		}
	}
}

function video_editMetaData()
{
	return array(
		// new objects
		array("name"=>"allow_anonymous",     "group"=>"new_objects", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive",      "group"=>"new_objects", "type"=>"",       "is_required"=>0),
		array("name"=>"upload_as_format",    "group"=>"new_objects", "type"=>"STRING", "is_required"=>0),
		array("name"=>"allow_embed",         "group"=>"new_objects", "type"=>"",       "is_required"=>0),
		array("name"=>"allow_embed_domains", "group"=>"new_objects", "type"=>"STRING", "is_required"=>0),

		// editing mode
		array("name"=>"var_video_id",              "group"=>"edit_mode", "type"=>"STRING", "is_required"=>1, "default_value"=>"id"),
		array("name"=>"forbid_change",             "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"forbid_change_screenshots", "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive_on_edit",    "group"=>"edit_mode", "type"=>"",       "is_required"=>0),

		// validation
		array("name"=>"min_duration",           "group"=>"validation", "type"=>"INT", "is_required"=>1, "default_value"=>"10"),
		array("name"=>"max_duration",           "group"=>"validation", "type"=>"INT", "is_required"=>0),
		array("name"=>"max_duration_premium",   "group"=>"validation", "type"=>"INT", "is_required"=>0),
		array("name"=>"max_duration_webmaster", "group"=>"validation", "type"=>"INT", "is_required"=>0),
		array("name"=>"optional_description",   "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"optional_tags",          "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"optional_categories",    "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"max_categories",         "group"=>"validation", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),

		// functionality
		array("name"=>"use_captcha",      "group"=>"functionality", "type"=>"",    "is_required"=>0),
		array("name"=>"set_custom_flag1", "group"=>"functionality", "type"=>"INT", "is_required"=>0),
		array("name"=>"set_custom_flag2", "group"=>"functionality", "type"=>"INT", "is_required"=>0),
		array("name"=>"set_custom_flag3", "group"=>"functionality", "type"=>"INT", "is_required"=>0),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
		array("name"=>"redirect_on_new_done",     "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
		array("name"=>"redirect_on_change_done",  "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
	);
}

function video_editJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingVideoEdit.js?v={$config['project_version']}";
}

function video_editGetAllowedPostfixesForCurrentUser()
{
	global $config;

	$formats_videos=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_videos.dat"));

	$result=array();
	foreach ($formats_videos as $format)
	{
		if ($format['access_level_id']==0)
		{
			$result[]=$format['postfix'];
		} elseif ($format['access_level_id']==1 && $_SESSION['user_id']>0)
		{
			$result[]=$format['postfix'];
		} elseif ($format['access_level_id']==2 && $_SESSION['status_id']==3)
		{
			$result[]=$format['postfix'];
		}
	}
	return $result;
}

function video_editIsDvdAllowedForUpload($dvd_info)
{
	global $config;

	if (count($dvd_info)>0)
	{
		if ($dvd_info['is_video_upload_allowed']==0 || ($dvd_info['is_video_upload_allowed']>0 && $dvd_info['user_id']==$_SESSION['user_id']))
		{
			return true;
		} elseif ($dvd_info['is_video_upload_allowed']==1 && mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",intval($_SESSION['user_id']),$dvd_info['user_id'],intval($_SESSION['user_id']),$dvd_info['user_id']))>0)
		{
			return true;
		}
	}
	return false;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
