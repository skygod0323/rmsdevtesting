<?php
function album_editShow($block_config,$object_id)
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
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'album_edit')));
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

	$errors=null;
	$errors_async=null;
	if (in_array($_POST['action'],array('add_new_complete','change_complete')))
	{
		if ($_POST['action']=='change_complete' && isset($block_config['forbid_change']))
		{
			if ($_POST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'forbidden','block'=>'album_edit')));
			} else {
				header("Location: $config[project_url]");die;
			}
		}

		$allowed_formats = $block_config['allowed_formats'] ?? $config['image_allowed_ext'];
		$allowed_formats=array_map('trim',explode(',',str_replace('jpg','jpeg',$allowed_formats)));

		require_once "$config[project_path]/admin/include/functions_screenshots.php";

		foreach ($_POST as $post_field_name=>$post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name]=trim($post_field_value);
			}
		}
		settype($_POST['category_ids'],"array");
		settype($_POST['model_ids'],"array");
		settype($_POST['delete_image_ids'],"array");
		if (intval($_POST['category_id'])>0)
		{
			$_POST['category_ids'][]=intval($_POST['category_id']);
		}

		if ($_POST['title']=='')
		{
			$errors['title']=1;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'required','block'=>'album_edit');
		} elseif (strlen($_POST['title'])<5)
		{
			$errors['title']=2;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'minimum','error_details'=>array(5),'block'=>'album_edit');
		}

		if (!isset($block_config['optional_description']))
		{
			if ($_POST['description']=='')
			{
				$errors['description']=1;
				$errors_async[]=array('error_field_name'=>'description','error_code'=>'required','block'=>'album_edit');
			}
		}
		if (!isset($block_config['optional_tags']))
		{
			if ($_POST['tags']=='')
			{
				$errors['tags']=1;
				$errors_async[]=array('error_field_name'=>'tags','error_code'=>'required','block'=>'album_edit');
			}
		}
		if (!isset($block_config['optional_categories']))
		{
			if (count($_POST['category_ids'])<1 || (count($_POST['category_ids'])==1 && intval($_POST['category_ids'][0])==0))
			{
				$errors['category_id']=1;
				$errors['category_ids']=1;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'required','block'=>'album_edit');
			}
		}
		$max_categories=intval($block_config['max_categories']);
		if ($max_categories==0) {$max_categories=3;}
		if (intval($errors['category_ids'])==0)
		{
			if (count($_POST['category_ids'])>$max_categories)
			{
				$errors['category_ids']=2;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'maximum','error_details'=>array($max_categories),'block'=>'album_edit');
			}
		}

		if (isset($_POST['tokens_required']))
		{
			if ($_POST['tokens_required']!='' && $_POST['tokens_required']!='0' && intval($_POST['tokens_required'])<1)
			{
				$errors['tokens_required']=2;
				$errors_async[]=array('error_field_name'=>'tokens_required','error_code'=>'integer','block'=>'album_edit');
			}
		}

		$antispam_action = '';
		if ($_POST['action']=='add_new_complete')
		{
			$is_find_correct_image=0;
			$uploaded_images=0;

			if ($_POST['files']!='' && preg_match('/^([0-9A-Za-z]{32})$/',$_POST['files']) && is_dir("$config[temporary_path]/$_POST[files]"))
			{
				$files=get_contents_from_dir("$config[temporary_path]/$_POST[files]",1);
				foreach ($files as $file)
				{
					if ($file!='skipped.txt')
					{
						$uploaded_images++;
						$is_find_correct_image=1;
					}
				}
			} else {
				for ($i=1;$i<=100;$i++)
				{
					if (is_file($_FILES["image$i"]['tmp_name']))
					{
						$size=getimagesize($_FILES["image$i"]['tmp_name']);
						if ($size[0]>=$block_config['min_image_width'] && $size[1]>=$block_config['min_image_height'])
						{
							$is_find_correct_image=1;
						}
						$uploaded_images++;
					}
				}
			}
			if ($is_find_correct_image==0)
			{
				if (!is_file($_FILES["image1"]['tmp_name']))
				{
					$errors['image1']=1;
					$errors_async[]=array('error_field_name'=>'image1','error_code'=>'required','block'=>'album_edit');
				} else {
					$size=getimagesize($_FILES["image1"]['tmp_name']);
					if (!in_array(str_replace('image/','',$size['mime']),$allowed_formats))
					{
						$errors['image1']=2;
						$errors_async[]=array('error_field_name'=>'image1','error_code'=>'invalid_format','block'=>'album_edit');
					} else {
						if ($size[0]<$block_config['min_image_width'])
						{
							$errors['image1']=3;
							$errors_async[]=array('error_field_name'=>'image1','error_code'=>'invalid_size','error_details'=>array("$block_config[min_image_width]x$block_config[min_image_height]"),'block'=>'album_edit');
						} elseif ($size[1]<$block_config['min_image_height'])
						{
							$errors['image1']=4;
							$errors_async[]=array('error_field_name'=>'image1','error_code'=>'invalid_size','error_details'=>array("$block_config[min_image_width]x$block_config[min_image_height]"),'block'=>'album_edit');
						}
					}
				}
			} else {
				if (intval($block_config['min_image_count'])>0)
				{
					if ($uploaded_images<intval($block_config['min_image_count']))
					{
						$errors['image1']=5;
						$errors_async[]=array('error_field_name'=>'image1','error_code'=>'images_minimum','error_details'=>array(intval($block_config['min_image_count'])),'block'=>'album_edit');
					}
				}
			}

			$antispam_action = process_antispam_rules(2, $_POST['title']);
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
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'album_edit');
					} elseif (!validate_recaptcha($_POST['g-recaptcha-response'], $recaptcha_data))
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'album_edit');
					}
				} else
				{
					if ($_POST['code'] == '')
					{
						$errors['code']=1;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'album_edit');
					} elseif ($_POST['code']<>$_SESSION['security_code_album_edit'] && $_POST['code']<>$_SESSION['security_code'])
					{
						$errors['code']=2;
						$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'album_edit');
					}
				}
			}
		}

		$now_date=date("Y-m-d H:i:s");
		if (!is_array($errors))
		{
			$_POST['title']=process_blocked_words($_POST['title'],true);
			$_POST['description']=process_blocked_words($_POST['description'],true);
			$_POST['tags']=process_blocked_words($_POST['tags']);

			unset($_SESSION['security_code'], $_SESSION['security_code_album_edit']);

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
			$rejected_images=0;
			if ($_POST['action']=='add_new_complete')
			{
				if (strpos($antispam_action, 'error') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on album from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));
					async_return_request_status(array(array('error_code' => 'spam', 'block' => 'album_edit')));
				}
				if (strpos($antispam_action, 'delete') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted album from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));

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

				$_POST['dir']=get_correct_dir_name($_POST['title']);
				if ($_POST['dir']<>'')
				{
					$temp_dir=$_POST['dir'];
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where dir=?",$temp_dir))==0)
						{
							$_POST['dir']=$temp_dir;break;
						}
						$temp_dir=$_POST['dir'].$i;
					}
				}

				if (intval($options['ALBUM_INITIAL_RATING'])>0)
				{
					$rating=intval($options['ALBUM_INITIAL_RATING']);
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

				$item_id=sql_insert("insert into $config[tables_prefix]albums set user_id=?, content_source_id=?, status_id=3, is_private=?, is_review_needed=$is_review_needed, title=?, dir=?, description=?, rating=?, rating_amount=1, ip=?, added_date=?, post_date=added_date, last_time_view_date=added_date",
					$user_id,intval($_POST['content_source_id']),intval($_POST['is_private']),trim($_POST['title']),trim($_POST['dir']),trim($_POST['description']),$rating,ip2int($_SERVER['REMOTE_ADDR']),$now_date
				);

				$dir_path=get_dir_by_id($item_id);

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

				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_ALBUMS'])==1)
				{
					if (isset($_POST['tokens_required']))
					{
						$update_array['tokens_required']=intval($_POST['tokens_required']);
					}
				}

				if (count($update_array)>0)
				{
					sql_pr("update $config[tables_prefix]albums set ?% where album_id=?",$update_array,$item_id);
				}

				if (!is_dir("$config[content_path_albums_sources]/$dir_path")) {mkdir("$config[content_path_albums_sources]/$dir_path");chmod("$config[content_path_albums_sources]/$dir_path",0777);}
				if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id")) {mkdir("$config[content_path_albums_sources]/$dir_path/$item_id");chmod("$config[content_path_albums_sources]/$dir_path/$item_id",0777);}

				if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
				{
					log_album("ERROR  Failed to create album source folder: $config[content_path_albums_sources]/$dir_path/$item_id",$item_id);
				}

				$source_files=array();
				$titles=array();
				$source_index=1;
				if ($_POST['files']!='' && preg_match('/^([0-9A-Za-z]{32})$/',$_POST['files']) && is_dir("$config[temporary_path]/$_POST[files]"))
				{
					$files=get_contents_from_dir("$config[temporary_path]/$_POST[files]",1);
					sort($files);
					foreach ($files as $file)
					{
						if ($file!='skipped.txt')
						{
							if (!rename("$config[temporary_path]/$_POST[files]/$file","$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg"))
							{
								log_album("ERROR  Failed to rename file to album source folder: $config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",$item_id);
							}
							chmod("$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",0666);
							$source_files[]="image_$source_index.jpg";
							$source_index++;
						}
					}
				} else {
					for ($i=1;$i<=100;$i++)
					{
						if (is_file($_FILES["image$i"]['tmp_name']))
						{
							$size=getimagesize($_FILES["image$i"]['tmp_name']);
							if ($size[0]>=$block_config['min_image_width'] && $size[1]>=$block_config['min_image_height'])
							{
								if (!move_uploaded_file($_FILES["image$i"]['tmp_name'],"$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg"))
								{
									log_album("ERROR  Failed to rename file to album source folder: $config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",$item_id);
								}
								chmod("$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",0666);
								$source_files[]="image_$source_index.jpg";
								$titles[$source_index]=trim($_POST["image_title$i"]);
								$source_index++;
							} else {
								$rejected_images++;
							}
						}
					}
				}

				if ((isset($block_config['force_inactive']) || strpos($antispam_action, 'deactivate') !== false) && !$is_trusted)
				{
					$status_id=0;
				}

				sql_pr("insert into $config[tables_prefix]users_events set event_type_id=2, user_id=?, album_id=?, added_date=?",$user_id,$item_id,$now_date);
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=140, object_id=?, object_type_id=2, added_date=?",$user_id,$username,$item_id,$now_date);

				$background_task=array();
				$background_task['status_id']=$status_id;
				$background_task['source_files']=$source_files;
				$background_task['titles']=$titles;
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=10, album_id=?, data=?, priority=?, added_date=?",$item_id,serialize($background_task),intval($background_task_priority),$now_date);
			} else {
				$item_id=intval($_REQUEST[$block_config['var_album_id']]);
				$dir_path=get_dir_by_id($item_id);
				$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where user_id=? and album_id=?",$user_id,$item_id));
				if ($old_data['user_id']<>$user_id || $old_data['is_locked']==1)
				{
					if ($_POST['mode']=='async')
					{
						if ($old_data['is_locked'])
						{
							async_return_request_status(array(array('error_code'=>'album_locked','block'=>'album_edit')));
						} else
						{
							async_return_request_status(array(array('error_code'=>'forbidden','block'=>'album_edit')));
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

				sql_pr("update $config[tables_prefix]albums set is_review_needed=$is_review_needed, status_id=$status_id, $database_selectors[locale_field_title]=?, $database_selectors[locale_field_description]=? where album_id=?",
						$_POST['title'],$_POST['description'],$item_id
				);

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
				if (isset($_POST['is_private']))
				{
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

				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if (intval($memberzone_data['ENABLE_TOKENS_SALE_ALBUMS'])==1)
				{
					if (isset($_POST['tokens_required']))
					{
						$update_array['tokens_required']=intval($_POST['tokens_required']);
					}
				}

				if (count($update_array)>0)
				{
					sql_pr("update $config[tables_prefix]albums set ?% where album_id=?",$update_array,$item_id);
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
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=2, action_details=?, added_date=?",$user_id,$username,$item_id,$update_details,$now_date);
				// end track changes

				if ($old_data['is_private']<>intval($_POST['is_private']))
				{
					if ($old_data['relative_post_date']==0)
					{
						if ($old_data['is_private']==1)
						{
							sql_pr("insert into $config[tables_prefix]users_events set event_type_id=9, user_id=?, album_id=?, added_date=?",$user_id, $item_id,$now_date);
						} elseif (intval($_POST['is_private'])==1) {
							sql_pr("insert into $config[tables_prefix]users_events set event_type_id=8, user_id=?, album_id=?, added_date=?",$user_id, $item_id,$now_date);
						}
					}
				}

				if (!isset($block_config['forbid_change_images']))
				{
					$main_photo_id=intval($_POST['main_photo_id']);
					$delete_ids=array_map("intval",$_POST['delete_image_ids']);
					foreach ($delete_ids as $k=>$v)
					{
						if ($v==$main_photo_id)
						{
							unset($delete_ids[$k]);
						}
					}

					$user_album_image_data=array();
					$user_album_image_ids=mr2array_list(sql_pr("select image_id from $config[tables_prefix]albums_images where album_id=?",$item_id));
					$temp=mr2array(sql_pr("select image_id, title from $config[tables_prefix]albums_images where album_id=?",$item_id));
					foreach ($temp as $res)
					{
						$user_album_image_data[$res['image_id']]=$res['title'];
					}

					foreach ($user_album_image_ids as $image_id)
					{
						if (!in_array($image_id,$delete_ids) && isset($_REQUEST["comment_$image_id"]))
						{
							$comment=process_blocked_words($_REQUEST["comment_$image_id"],true);
							if ($user_album_image_data[$image_id]<>$comment)
							{
								sql_pr("update $config[tables_prefix]albums_images set title=? where image_id=?",$comment,$image_id);
							}
						}
					}

					if ((is_array($delete_ids) && count($delete_ids)>0) || $main_photo_id<>$old_data['main_photo_id'])
					{
						log_album("",$item_id);
						log_album("INFO  Changing images on site",$item_id);
					}

					if ($main_photo_id<>$old_data['main_photo_id'] && in_array($main_photo_id,$user_album_image_ids))
					{
						log_album("INFO  Changing preview image from #$old_data[main_photo_id] to #$main_photo_id",$item_id);
						sql_pr("update $config[tables_prefix]albums set main_photo_id=? where album_id=?",$main_photo_id,$item_id);
					}

					if (is_array($delete_ids) && count($delete_ids)>0)
					{
						$delete_cnt=count($delete_ids);
						$delete_ids_str=implode(",",$delete_ids);
						sql_pr("delete from $config[tables_prefix]albums_images where album_id=? and image_id in ($delete_ids_str)",$item_id);
						sql_pr("delete from $config[tables_prefix]rating_history where album_id=? and image_id in ($delete_ids_str)",$item_id);
						sql_pr("update $config[tables_prefix]comments set object_sub_id=0 where object_sub_id in ($delete_ids_str) and object_type_id=2");

						foreach ($delete_ids as $delete_id)
						{
							log_album("INFO  Removing image #$delete_id",$item_id);
						}
						sql_pr("update $config[tables_prefix]albums set photos_amount=photos_amount-? where album_id=?",$delete_cnt,$item_id);
					}

					if ((is_array($delete_ids) && count($delete_ids)>0) || $main_photo_id<>$old_data['main_photo_id'])
					{
						$background_task=array();
						$background_task['deleted_image_ids']=$delete_ids;
						if ($main_photo_id<>$old_data['main_photo_id'])
						{
							$background_task['main_image_changed']=1;
						}
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=22, album_id=?, data=?, priority=?, added_date=?",$item_id,serialize($background_task),intval($background_task_priority),$now_date);
						sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=154, object_id=?, object_type_id=2, added_date=?",$user_id,$username,$item_id,$now_date);
						log_album("INFO  Done images changes",$item_id);
					}

					if (!is_dir("$config[content_path_albums_sources]/$dir_path")) {mkdir("$config[content_path_albums_sources]/$dir_path");chmod("$config[content_path_albums_sources]/$dir_path",0777);}
					if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id")) {mkdir("$config[content_path_albums_sources]/$dir_path/$item_id");chmod("$config[content_path_albums_sources]/$dir_path/$item_id",0777);}

					if (!is_dir("$config[content_path_albums_sources]/$dir_path/$item_id"))
					{
						log_album("ERROR  Failed to create album source folder: $config[content_path_albums_sources]/$dir_path/$item_id",$item_id);
					}

					$source_files=array();
					$titles=array();
					$source_index=1;
					for ($i=1;$i<=100;$i++)
					{
						if (is_file("$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg"))
						{
							$source_index++;
						} else
						{
							break;
						}
					}
					if (is_array($_FILES['content']['tmp_name']))
					{
						for ($i=0;$i<count($_FILES['content']['tmp_name']);$i++)
						{
							if (is_file($_FILES["content"]['tmp_name'][$i]))
							{
								$size=getimagesize($_FILES["content"]['tmp_name'][$i]);
								if (in_array(str_replace('image/','',$size['mime']),$allowed_formats))
								{
									if ($size[0]>=$block_config['min_image_width'] && $size[1]>$block_config['min_image_height'])
									{
										if (!move_uploaded_file($_FILES["content"]['tmp_name'][$i],"$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg"))
										{
											log_album("ERROR  Failed to rename file to album source folder: $config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",$item_id);
										}
										chmod("$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",0666);
										$source_files[]="image_$source_index.jpg";
										$source_index++;
									} else {
										$rejected_images++;
									}
								} else {
									$rejected_images++;
								}
							}
						}
					} else {
						for ($i=1;$i<=100;$i++)
						{
							if (is_file($_FILES["image$i"]['tmp_name']))
							{
								$size=getimagesize($_FILES["image$i"]['tmp_name']);
								if ($size[0]>=$block_config['min_image_width'] && $size[1]>=$block_config['min_image_height'])
								{
									if (!move_uploaded_file($_FILES["image$i"]['tmp_name'],"$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg"))
									{
										log_album("ERROR  Failed to rename file to album source folder: $config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",$item_id);
									}
									chmod("$config[content_path_albums_sources]/$dir_path/$item_id/image_$source_index.jpg",0666);
									$source_files[]="image_$source_index.jpg";
									$titles[$source_index]=trim($_POST["image_title$i"]);
									$source_index++;
								} else {
									$rejected_images++;
								}
							}
						}
					}
					if (count($source_files)>0)
					{
						log_album("",$item_id);
						log_album("INFO  Uploading ".count($source_files)." new images",$item_id);
						$background_task=array();
						$background_task['source_files']=$source_files;
						$background_task['titles']=$titles;
						sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=14, album_id=?, data=?, priority=?, added_date=?",$item_id,serialize($background_task),intval($background_task_priority),$now_date);
					}
				}

				sql_pr("update $config[tables_prefix]users set
						public_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=0),
						private_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=1),
						premium_albums_count=(select count(*) from $config[tables_prefix]albums where status_id=1 and user_id=$config[tables_prefix]users.user_id and is_private=2),
						total_albums_count=public_albums_count+private_albums_count+premium_albums_count
					where user_id=?",$user_id
				);
			}

			sql_pr("delete from $config[tables_prefix]tags_albums where album_id=?",$item_id);
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
						sql_pr("insert into $config[tables_prefix]tags_albums set tag_id=?, album_id=?",$tag_id,$item_id);
						$inserted_tags[]=mb_lowercase($tag);
					}
				}
			}

			sql_pr("delete from $config[tables_prefix]categories_albums where album_id=?",$item_id);
			settype($_POST['category_ids'], "array");
			foreach ($_POST['category_ids'] as $category_id)
			{
				if (intval($category_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]categories_albums set category_id=?, album_id=?",$category_id,$item_id);
				}
			}

			sql_pr("delete from $config[tables_prefix]models_albums where album_id=?",$item_id);
			settype($_POST['model_ids'], "array");
			foreach ($_POST['model_ids'] as $model_id)
			{
				if (intval($model_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]models_albums set model_id=?, album_id=?",$model_id,$item_id);
				}
			}

			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				if ($status_id==0)
				{
					$smarty->assign('force_inactive',1);
				}
				if ($rejected_images>0)
				{
					$smarty->assign('rejected_images',$rejected_images);
				}

				$album_data=mr2array_single(sql_pr("select $database_selectors[albums] from $config[tables_prefix]albums where album_id=?",$item_id));
				if ($album_data['album_id']>0)
				{
					$album_url="$config[project_url]/".str_replace("%ID%",$album_data['album_id'],str_replace("%DIR%",$album_data['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));
					$smarty->assign('async_object_data',$album_data);
					$smarty->assign('async_object_url',$album_url);
				}
				return '';
			} else {
				$rejected_images_param='';
				if ($rejected_images>0)
				{
					$rejected_images_param="&rejected_images=$rejected_images";
				}

				if ($_POST['action']=='add_new_complete')
				{
					$force_inactive_param='';
					if ($status_id==0)
					{
						$force_inactive_param="&force_inactive=1";
					}
					if ($block_config['redirect_on_new_done']<>'')
					{
						$url=process_url($block_config['redirect_on_new_done']);
						header("Location: $url?action=add_new_done{$rejected_images_param}{$force_inactive_param}");die;
					} else
					{
						header("Location: ?action=add_new_done{$rejected_images_param}");die;
					}
				} else {
					$force_inactive_param='';
					if ($status_id==0)
					{
						$force_inactive_param="&force_inactive=1";
					}
					if ($block_config['redirect_on_change_done']<>'')
					{
						$url=process_url($block_config['redirect_on_change_done']);
						header("Location: $url?action=change_done{$rejected_images_param}{$force_inactive_param}");die;
					} else
					{
						header("Location: ?action=change_done{$rejected_images_param}");die;
					}
				}
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	if (intval($_REQUEST[$block_config['var_album_id']])>0)
	{
		$item_id=intval($_REQUEST[$block_config['var_album_id']]);
		$_POST=mr2array_single(sql_pr("select $database_selectors[albums] from $config[tables_prefix]albums where user_id=? and album_id=?",$user_id,$item_id));
		if (count($_POST)>0)
		{
			$_POST['block_uid']=$object_id;

			$_POST['tags']=implode(", ",mr2array_list(sql_pr("select (select $database_selectors[generic_selector_tag] from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_albums.tag_id) as tag from $config[tables_prefix]tags_albums where $config[tables_prefix]tags_albums.album_id=? order by id asc",$item_id)));
			$_POST['category_ids']=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_albums where album_id=? order by id asc",$item_id));
			$_POST['model_ids']=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_albums where album_id=? order by id asc",$item_id));
			$_POST['images']=mr2array(sql_pr("select $database_selectors[albums_images] from $config[tables_prefix]albums_images where album_id=? order by image_id",$item_id));

			$_POST['stats']=mr2array(sql_pr("select added_date, viewed, unique_viewed from $config[tables_prefix]stats_albums where album_id=? order by added_date asc",$item_id));

			$dir_path=get_dir_by_id($item_id);

			$main_image=array();
			foreach ($_POST['images'] as $image)
			{
				if ($image['image_id']==$_POST['main_photo_id'])
				{
					$main_image=$image;
					break;
				}
			}

			$cluster=unserialize(file_get_contents("$config[project_path]/admin/data/system/cluster.dat"));
			$cluster_servers=array();
			$cluster_servers_weights=array();
			foreach ($cluster as $server)
			{
				if ($server['status_id']==1)
				{
					$cluster_servers[intval($server['group_id'])][]=$server;
					$cluster_servers_weights[intval($server['group_id'])]+=$server['lb_weight'];
				}
			}

			$lb_server=load_balance_server($_POST['server_group_id'],$cluster_servers,$cluster_servers_weights);

			$formats_albums=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat"));
			$formats=array();
			$image_formats=get_image_formats($item_id,$main_image['image_formats']);
			foreach ($formats_albums as $format)
			{
				if ($format['group_id']==1)
				{
					$format_item=array();
					$file_path="main/$format[size]/$dir_path/$item_id/$_POST[main_photo_id].jpg";
					$hash=md5($config['cv'].$file_path);

					$format_item['direct_url']="$lb_server[urls]/$file_path";
					$format_item['protected_url']="$config[project_url]/get_image/$_POST[server_group_id]/$hash/$file_path/";

					foreach ($image_formats as $format_rec)
					{
						if ($format_rec['size']==$format['size'])
						{
							$format_item['dimensions']=$format_rec['dimensions'];
							$format_item['filesize']=$format_rec['file_size_string'];
							break;
						}
					}
					$formats[$format['size']]=$format_item;
				}
			}

			$format_item=array();
			$file_path="sources/$dir_path/$item_id/$_POST[main_photo_id].jpg";
			$hash=md5($config['cv'].$file_path);
			$format_item['direct_url']="$lb_server[urls]/$file_path";
			$format_item['protected_url']="$config[project_url]/get_image/$_POST[server_group_id]/$hash/$file_path/";
			foreach ($image_formats as $format_rec)
			{
				if ($format_rec['size']=='source')
				{
					$format_item['dimensions']=$format_rec['dimensions'];
					$format_item['filesize']=$format_rec['file_size_string'];
					break;
				}
			}
			$formats['source']=$format_item;

			$_POST['formats']=$formats;
			$_POST['zip_files']=get_album_zip_files($item_id,$_POST['zip_files'],$_POST['server_group_id']);

			$photos_size=0;
			foreach ($_POST['images'] as $k=>$v)
			{
				$lb_server=load_balance_server($_POST['server_group_id'],$cluster_servers,$cluster_servers_weights);

				$formats=array();
				$image_formats=get_image_formats($item_id,$v['image_formats']);
				foreach ($formats_albums as $format)
				{
					if ($format['group_id']==1)
					{
						$format_item=array();
						$file_path="main/$format[size]/$dir_path/$item_id/$v[image_id].jpg";
						$hash=md5($config['cv'].$file_path);

						$format_item['direct_url']="$lb_server[urls]/$file_path";
						$format_item['protected_url']="$config[project_url]/get_image/$_POST[server_group_id]/$hash/$file_path/";

						foreach ($image_formats as $format_rec)
						{
							if ($format_rec['size']==$format['size'])
							{
								$format_item['dimensions']=$format_rec['dimensions'];
								$format_item['filesize']=$format_rec['file_size_string'];
								break;
							}
						}
						$formats[$format['size']]=$format_item;
					}
				}
				$photos_size+=$image_formats['source']['file_size'];

				$format_item=array();
				$file_path="sources/$dir_path/$item_id/$v[image_id].jpg";
				$hash=md5($config['cv'].$file_path);
				$format_item['direct_url']="$lb_server[urls]/$file_path";
				$format_item['protected_url']="$config[project_url]/get_image/$_POST[server_group_id]/$hash/$file_path/";
				foreach ($image_formats as $format_rec)
				{
					if ($format_rec['size']=='source')
					{
						$format_item['dimensions']=$format_rec['dimensions'];
						$format_item['filesize']=$format_rec['file_size_string'];
						break;
					}
				}
				$formats['source']=$format_item;

				$_POST['images'][$k]['formats']=$formats;
			}

			$_POST['view_page_url']="$config[project_url]/".str_replace("%ID%",$_POST['album_id'],str_replace("%DIR%",$_POST['dir'],$website_ui_data['WEBSITE_LINK_PATTERN_ALBUM']));

			$storage[$object_id]['album_title']=$_POST['title'];
			$storage[$object_id]['album_id']=$_POST['album_id'];

			$_POST['photos_size']=$photos_size;
			$_POST['photos_size_string']=sizeToHumanString($photos_size,2);

			$new_images_count=0;
			$background_tasks=mr2array(sql_pr("select * from $config[tables_prefix]background_tasks where album_id=? and type_id in (10,14)",$item_id));
			foreach ($background_tasks as $task)
			{
				if (intval($task['task_id'])>0)
				{
					$task_data=@unserialize($task['data']);
					$new_images_count+=count($task_data['source_files']);
				}
			}
			if ($new_images_count>0)
			{
				$smarty->assign('has_background_task',1);
				$smarty->assign('background_task_new_images_count',$new_images_count);
				$_POST['has_background_task']=1;
				$_POST['background_task_new_images_count']=$new_images_count;
			}

		} else {
			return "status_404";
		}
	} elseif ($_REQUEST['files']!='')
	{
		$uploaded_files_key=$_REQUEST['files'];
		if (preg_match('/^([0-9A-Za-z]{32})$/',$uploaded_files_key) && is_dir("$config[temporary_path]/$uploaded_files_key"))
		{
			$files=get_contents_from_dir("$config[temporary_path]/$uploaded_files_key",1);
			sort($files);
			$total_size=0;
			$files_details=array();
			foreach ($files as $file)
			{
				if ($file!='skipped.txt')
				{
					$filesize=filesize("$config[temporary_path]/$uploaded_files_key/$file");
					$files_details[]=array(
						'index'=>intval($file),
						'name'=>$file,
						'size'=>$filesize,
						'size_string'=>sizeToHumanString($filesize,2),
					);
					$total_size+=$filesize;
				}
			}
			$uploaded_files_info=array(
				'files'=>$files_details,
				'files_size'=>$total_size,
				'files_size_string'=>sizeToHumanString($total_size,2),
				'files_count'=>count($files_details),
				'files_skipped'=>intval(@file_get_contents("$config[temporary_path]/$uploaded_files_key/skipped.txt"))
			);
			$smarty->assign('uploaded_files_info',$uploaded_files_info);
		}
		$smarty->assign('uploaded_files_key',$uploaded_files_key);
	}

	$list_categories=mr2array(sql_pr("select $database_selectors[categories] from $config[tables_prefix]categories order by title asc"));
	$list_categories_groups=mr2array(sql_pr("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups order by title asc"));
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
		}
	}

	$smarty->assign('errors',$errors);
	$smarty->assign('list_categories',$list_categories);
	$smarty->assign('list_categories_groups',$list_categories_groups);
	$smarty->assign('list_models',mr2array(sql_pr("select $database_selectors[models] from $config[tables_prefix]models order by title asc")));
	$smarty->assign('list_tags',mr2array(sql_pr("select $database_selectors[tags] from $config[tables_prefix]tags order by tag asc limit 500")));

	if ($user_content_source_group_id>0)
	{
		$smarty->assign('list_content_sources',mr2array(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources where content_source_group_id=? order by title asc",$user_content_source_group_id)));
	} else {
		$smarty->assign('list_content_sources',mr2array(sql_pr("select $database_selectors[content_sources] from $config[tables_prefix]content_sources order by title asc")));
	}

	if (intval($block_config['min_image_width'])>0)
	{
		$smarty->assign('min_image_width',intval($block_config['min_image_width']));
	}
	if (intval($block_config['min_image_height'])>0)
	{
		$smarty->assign('min_image_height',intval($block_config['min_image_height']));
	}
	if (intval($block_config['min_image_count'])>0)
	{
		$smarty->assign('min_image_count',intval($block_config['min_image_count']));
	}

	if (intval($_POST['album_id'])>0)
	{
		if (isset($block_config['forbid_change']))
		{
			$smarty->assign('change_forbidden', 1);
		} elseif ($_POST['is_locked'] == 1)
		{
			$smarty->assign('change_forbidden', 1);
		}
		if (isset($block_config['forbid_change_images']))
		{
			$smarty->assign('change_images_forbidden', 1);
		}
	} else
	{
		$antispam_action = process_antispam_rules(2);
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

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['ENABLE_TOKENS_SALE_ALBUMS'])==1)
	{
		$smarty->assign('allow_tokens',1);
		$smarty->assign('tokens_price_default',intval($memberzone_data['DEFAULT_TOKENS_STANDARD_ALBUM']));
		$smarty->assign('tokens_period_default',intval($memberzone_data['TOKENS_PURCHASE_EXPIRY']));
		$smarty->assign('tokens_commission',intval($memberzone_data['TOKENS_SALE_INTEREST']));
	}
	return '';
}

function album_editGetHash($block_config)
{
	return "nocache";
}

function album_editCacheControl($block_config)
{
	return "nocache";
}

function album_editAsync($block_config)
{
	global $config;

	if (intval($_SESSION['user_id']) < 1 && !isset($block_config['allow_anonymous']))
	{
		async_return_request_status(array(array('error_code' => 'not_logged_in', 'block' => 'album_edit')));
	}

	require_once "$config[project_path]/admin/include/functions_base.php";
	if ($_REQUEST['action'] == 'upload_files')
	{
		$errors_async = null;

		if ($_REQUEST['filename'] == '')
		{
			if (count($_POST) == 0)
			{
				$errors_async[] = array('error_field_name' => 'content', 'error_code' => 'filesize_limit', 'block' => 'album_edit');
			} else
			{
				$errors_async[] = array('error_field_name' => 'content', 'error_code' => 'required', 'block' => 'album_edit');
			}
			async_return_request_status($errors_async);
		} elseif (!preg_match('/^([0-9A-Za-z]{32})$/', $_REQUEST['filename']))
		{
			async_return_request_status(array(array('error_code' => 'invalid_params', 'block' => 'album_edit')));
		}

		$allowed_formats = $block_config['allowed_formats'] ?? $config['image_allowed_ext'];
		$allowed_formats = array_map('trim', explode(',', str_replace('jpg', 'jpeg', $allowed_formats)));

		require "$config[project_path]/admin/include/uploader.php";

		$upload_result = uploader_upload_local_files(trim($_REQUEST['filename']), $_FILES['content'], intval($_REQUEST['files']), intval($_REQUEST['index']), $allowed_formats, $block_config['min_image_width'], $block_config['min_image_height']);

		if (strpos($upload_result, 'ok_file') === 0)
		{
			async_return_request_status(null, null, ['state' => 'uploading', 'percent' => substr($upload_result, 8)]);
		} elseif (strpos($upload_result, 'skipped_file') === 0)
		{
			async_return_request_status(null, null, ['state' => 'uploading', 'percent' => substr($upload_result, 13), 'is_skipped' => true]);
		} elseif ($upload_result == 'ktfudc_notallowed_error')
		{
			async_return_request_status(array(array('error_code' => 'not_logged_in', 'block' => 'album_edit')));
		} elseif ($upload_result == 'ktfudc_unexpected_error')
		{
			async_return_request_status(array(array('error_code' => 'invalid_params', 'block' => 'album_edit')));
		}

		$total_size = 0;
		$uploaded_images = 0;

		$uploaded_files_dir = "$config[temporary_path]/$_REQUEST[filename]";
		$uploaded_files = get_contents_from_dir($uploaded_files_dir, 1);
		$skipped_list = [];
		foreach ($uploaded_files as $uploaded_file)
		{
			if ($uploaded_file == 'skipped.txt')
			{
				$skipped_list = array_map('trim', explode("\n", trim(file_get_contents("$uploaded_files_dir/$uploaded_file"))));
				continue;
			}

			$uploaded_images++;
			$total_size += filesize("$uploaded_files_dir/$uploaded_file");
		}

		if (!$uploaded_images)
		{
			$errors_async[] = array('error_field_name' => 'content', 'error_code' => 'images_empty', 'error_details' => array("$block_config[min_image_width]x$block_config[min_image_height]"), 'block' => 'album_edit');
		} elseif (intval($block_config['min_image_count']) > 0)
		{
			if ($uploaded_images < intval($block_config['min_image_count']))
			{
				$errors_async[] = array('error_field_name' => 'content', 'error_code' => 'images_minimum', 'error_details' => array(intval($block_config['min_image_count'])), 'block' => 'album_edit');
			}
		}

		if (!is_array($errors_async))
		{
			$uploaded_files_info = array(
					'filename' => $_REQUEST['filename'],
					'files_size' => $total_size,
					'files_size_string' => sizeToHumanString($total_size, 2),
					'files_count' => $uploaded_images,
					'files_skipped' => count($skipped_list)
			);
			async_return_request_status(null, null, $uploaded_files_info);
		} else
		{
			async_return_request_status($errors_async);
		}
	} elseif ($_REQUEST['action'] == 'album_preview')
	{
		if ($_REQUEST['files'] != '')
		{
			$uploaded_files_key = $_REQUEST['files'];
			if (preg_match('/^([0-9A-Za-z]{32})$/', $uploaded_files_key) && is_dir("$config[temporary_path]/$uploaded_files_key"))
			{
				$files = get_contents_from_dir("$config[temporary_path]/$uploaded_files_key", 1);
				sort($files);
				if (count($files) > 0)
				{
					$index = intval($_REQUEST['index']) - 1;
					if ($index < 0)
					{
						$index = 0;
					}
					header("Content-Type: image/jpeg");
					header("Content-Length: " . filesize("$config[temporary_path]/$uploaded_files_key/$files[$index]"));
					ob_end_clean();
					readfile("$config[temporary_path]/$uploaded_files_key/$files[$index]");
					die;
				}
			}
		}
	}
}

function album_editMetaData()
{
	return array(
		// new objects
		array("name"=>"allow_anonymous", "group"=>"new_objects", "type"=>"", "is_required"=>0),
		array("name"=>"force_inactive",  "group"=>"new_objects", "type"=>"", "is_required"=>0),

		// editing mode
		array("name"=>"var_album_id",           "group"=>"edit_mode", "type"=>"STRING", "is_required"=>1, "default_value"=>"id"),
		array("name"=>"forbid_change",          "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"forbid_change_images",   "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive_on_edit", "group"=>"edit_mode", "type"=>"",       "is_required"=>0),

		// validation
		array("name"=>"allowed_formats",      "group"=>"validation", "type"=>"STRING", "is_required"=>0),
		array("name"=>"min_image_width",      "group"=>"validation", "type"=>"INT",    "is_required"=>1, "default_value"=>"200"),
		array("name"=>"min_image_height",     "group"=>"validation", "type"=>"INT",    "is_required"=>1, "default_value"=>"200"),
		array("name"=>"min_image_count",      "group"=>"validation", "type"=>"INT",    "is_required"=>0),
		array("name"=>"optional_description", "group"=>"validation", "type"=>"",       "is_required"=>0),
		array("name"=>"optional_tags",        "group"=>"validation", "type"=>"",       "is_required"=>0),
		array("name"=>"optional_categories",  "group"=>"validation", "type"=>"",       "is_required"=>0),
		array("name"=>"max_categories",       "group"=>"validation", "type"=>"INT",    "is_required"=>0, "default_value"=>"3"),

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

function album_editJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingAlbumEdit.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
