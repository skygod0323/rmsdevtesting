<?php
function member_profile_viewShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$page_id,$list_countries,$database_selectors;

	$user_id=intval($_REQUEST[$block_config['var_user_id']]);
	if ($user_id==0 && $_SESSION['user_id']>0)
	{
		$user_id=$_SESSION['user_id'];
	}

	$data=mr2array_single(sql_pr("select *, (select $database_selectors[generic_selector_title] from $config[tables_prefix]categories where category_id=$config[tables_prefix]users.favourite_category_id) as favourite_category from $config[tables_prefix]users where status_id not in (1,4) and user_id=?",$user_id));
	if (count($data)==0)
	{
		return 'status_404';
	}

	$errors=null;
	$errors_async=null;

	if ($_REQUEST['action']=='send_message_complete')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				$tokens_required=0;
				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if ($memberzone_data['ENABLE_TOKENS_INTERNAL_MESSAGES']==1)
				{
					$tokens_required=intval($memberzone_data['TOKENS_INTERNAL_MESSAGES']);
				}

				$message=trim($_REQUEST['message']);
				if ($message=='')
				{
					$errors['message']=1;
					$errors_async[]=array('error_field_name'=>'message','error_code'=>'required','block'=>'member_profile_view');
				} elseif ($tokens_required>0 && $tokens_required>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
				{
					$errors['message']=2;
					$errors_async[]=array('error_field_name'=>'message','error_code'=>'not_enough_tokens','block'=>'member_profile_view');
				}

				if (!is_array($errors))
				{
					$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
					if (strpos($antispam_action, 'error') !== false)
					{
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
						async_return_request_status(array(array('error_code'=>'spam','block'=>'member_profile_view')));
					}

					$message_id=sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, message=?, message_md5=md5(message), ip=?, added_date=?",$user_id,$_SESSION['user_id'],strip_tags($message),ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id);

					if ($tokens_required>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$tokens_required,$_SESSION['user_id']);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
					}

					if (strpos($antispam_action, 'delete') !== false)
					{
						sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where message_id=?", $message_id);
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
					}

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status(null,null,array('message_id'=>$message_id));
					} else {
						header("Location: ?action=send_message_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status($errors_async);
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	if ($_REQUEST['action']=='add_to_friends_complete')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",$_SESSION['user_id'],$user_id,$_SESSION['user_id'],$user_id))==0)
				{
					$tokens_required=0;
					$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
					if ($memberzone_data['ENABLE_TOKENS_INTERNAL_MESSAGES']==1)
					{
						$tokens_required=intval($memberzone_data['TOKENS_INTERNAL_MESSAGES']);
					}

					if ($tokens_required>0 && $tokens_required>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id'])))
					{
						$errors['message']=2;
						$errors_async[]=array('error_field_name'=>'message','error_code'=>'not_enough_tokens','block'=>'member_profile_view');
					}

					if (!is_array($errors))
					{
						$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
						if (strpos($antispam_action, 'error') !== false)
						{
							sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
							async_return_request_status(array(array('error_code'=>'spam','block'=>'member_profile_view')));
						}

						sql_pr("insert into $config[tables_prefix]friends set user_id=?, friend_id=?, added_date=?",$_SESSION['user_id'],$user_id,date("Y-m-d H:i:s"));
						sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id);
						$message_id=sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=1, message=?, message_md5=md5(message), ip=?, added_date=?",$user_id,$_SESSION['user_id'],strip_tags(trim($_REQUEST['message'])),ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));

						if ($tokens_required>0)
						{
							sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$tokens_required,$_SESSION['user_id']);
							$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$_SESSION['user_id']));
						}

						if (strpos($antispam_action, 'delete') !== false)
						{
							sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where message_id=?", $message_id);
							sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
						}

						if ($_REQUEST['mode']=='async')
						{
							async_return_request_status(null,null,array('message_id'=>$message_id));
						} else {
							header("Location: ?action=add_to_friends_done");die;
						}
					} elseif ($_REQUEST['mode']=='async')
					{
						async_return_request_status($errors_async);
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	if ($_REQUEST['action']=='remove_from_friends')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				if (sql_delete("delete from $config[tables_prefix]friends where (user_id=? and friend_id=?) or (friend_id=? and user_id=?)",$_SESSION['user_id'],$user_id,$_SESSION['user_id'],$user_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=3, message='', added_date=?",$user_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=11, user_id=?, user_target_id=?, added_date=?",$_SESSION['user_id'],$user_id,date("Y-m-d H:i:s"));
				}

				friends_changed(array($user_id,$_SESSION['user_id']));

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=remove_from_friends_done");die;
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	if ($_REQUEST['action']=='ignore_user')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id))==0)
				{
					sql_pr("insert into $config[tables_prefix]users_ignores set user_id=?, ignored_user_id=?",$_SESSION['user_id'],$user_id);
					messages_changed();
				}

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=ignore_done");die;
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	if ($_REQUEST['action']=='remove_from_ignores')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				if (sql_delete("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id)>0)
				{
					messages_changed();
				}

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=remove_from_ignores_done");die;
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	if ($_REQUEST['action']=='confirm_add_to_friends')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id<>$_SESSION['user_id'])
			{
				if (isset($_REQUEST['confirm']) || isset($_REQUEST['confirm_x']))
				{
					if (sql_update("update $config[tables_prefix]friends set is_approved=1, approved_date=? where user_id=? and friend_id=?",date("Y-m-d H:i:s"),$user_id,$_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1 where type_id=1 and message!='' and user_id=? and user_from_id=?",$_SESSION['user_id'],$user_id);
						sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$_SESSION['user_id'],$user_id);
						sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=4, message='', added_date=?",$user_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=10, user_id=?, user_target_id=?, added_date=?",$user_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
						sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$user_id);

						messages_changed();
					}

					friends_changed(array($user_id,$_SESSION['user_id']));

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=confirm_add_to_friends_done");die;
					}
				} elseif (isset($_REQUEST['reject']) || isset($_REQUEST['reject_x']))
				{
					if (sql_pr("delete from $config[tables_prefix]friends where user_id=? and friend_id=?",$user_id,$_SESSION['user_id'])>0)
					{
						sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1 where type_id=1 and message!='' and user_id=? and user_from_id=?",$_SESSION['user_id'],$user_id);
						sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$_SESSION['user_id'],$user_id);
						sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=2, message='', added_date=?",$user_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));

						messages_changed();
					}

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=reject_add_to_friends_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'member_profile_view')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_view')));
		}
	}

	$website_ui_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
	$data['is_online']=0;
	if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
	{
		if (time()-strtotime($data['last_online_date'])<$website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL']*60+30)
		{
			$data['is_online']=1;
		}
	}

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
	$data['playlists']=mr2array(sql_pr("select $database_selectors[playlists] from $config[tables_prefix]playlists where $database_selectors[where_playlists] and user_id=?",$data['user_id']));
	$data['playlists_count']=count($data['playlists']);

	$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
	if (intval($memberzone_data['ENABLE_TOKENS_SUBSCRIBE_MEMBERS'])==1)
	{
		if (intval($data['tokens_required'])==0)
		{
			$data['tokens_required']=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
		}
		$data['tokens_required_period']=intval($memberzone_data['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']);
	} else
	{
		$data['tokens_required']=0;
	}

	$exposed_to_storage=array(
		'user_id',
		'username',
		'display_name',
		'avatar',
		'avatar_url',
		'cover',
		'cover_url',
		'is_online',
		'gender_id',
		'orientation_id',
		'relationship_status_id',
		'age',
		'country_id',
		'country',
		'city',
		'about_me',
		'comments_videos_count',
		'comments_albums_count',
		'comments_cs_count',
		'comments_models_count',
		'comments_dvds_count',
		'comments_posts_count',
		'comments_playlists_count',
		'comments_total_count',
		'logins_count',
		'friends_count',
		'public_videos_count',
		'private_videos_count',
		'premium_videos_count',
		'total_videos_count',
		'favourite_videos_count',
		'public_albums_count',
		'private_albums_count',
		'premium_albums_count',
		'total_albums_count',
		'favourite_albums_count',
		'subscribers_count',
		'playlists_count',
		'added_date',
		'last_login_date',
		'activity',
		'activity_rank',
		'tokens_available',
		'tokens_required',
		'tokens_required_period'
	);
	for ($i=1;$i<=10;$i++)
	{
		$exposed_to_storage[]="custom{$i}";
	}
	foreach ($exposed_to_storage as $storage_field)
	{
		if (isset($data[$storage_field]))
		{
			$storage[$object_id][$storage_field]=$data[$storage_field];
		}
	}

	if ($_SESSION['user_id']>0 && $_SESSION['user_id']<>$data['user_id'])
	{
		$friend_data=mr2array_single(sql_pr("select * from $config[tables_prefix]friends where is_approved=1 and ((user_id=? and friend_id=?) or (friend_id=? and user_id=?))",$_SESSION['user_id'],$data['user_id'],$_SESSION['user_id'],$data['user_id']));
		if (intval($friend_data['user_id'])==0)
		{
			$data['is_friend']=0;

			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=0 and user_id=? and friend_id=?",$data['user_id'],$_SESSION['user_id']))>0)
			{
				$data['is_friend_invitation_received']=1;
				$storage[$object_id]['is_friend_invitation_received']=$data['is_friend_invitation_received'];
			} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]friends where is_approved=0 and user_id=? and friend_id=?",$_SESSION['user_id'],$data['user_id']))>0)
			{
				$data['is_friend_invitation_sent']=1;
				$storage[$object_id]['is_friend_invitation_sent']=$data['is_friend_invitation_sent'];
			}
		} else {
			$data['is_friend']=1;
			$data['friend_since']=$friend_data['approved_date'];
		}
		$storage[$object_id]['is_friend']=$data['is_friend'];

		$data['is_subscribed']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id=? and subscribed_type_id=1",$_SESSION['user_id'],$data['user_id']))>0)
		{
			$data['is_subscribed']=1;
		}
		$storage[$object_id]['is_subscribed']=$data['is_subscribed'];

		$data['is_ignored']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$_SESSION['user_id'],$data['user_id']))>0)
		{
			$data['is_ignored']=1;
		}
		$storage[$object_id]['is_ignored']=$data['is_ignored'];

		$data['is_ignoring']=0;
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$data['user_id'],$_SESSION['user_id']))>0)
		{
			$data['is_ignoring']=1;
		}
		$storage[$object_id]['is_ignoring']=$data['is_ignoring'];
	}

	if (isset($block_config['show_next_and_previous_info']))
	{
		$result=sql_pr("SELECT * from $config[tables_prefix]users where status_id not in (1,4) and user_id>? and user_id<>? order by user_id asc limit 1",$user_id,$user_id);
		if (isset($result) && mr2rows($result)>0)
		{
			$smarty->assign("next_user",mr2array_single($result));
		}
		$result=sql_pr("SELECT * from $config[tables_prefix]users where status_id not in (1,4) and user_id<? and user_id<>? order by user_id desc limit 1",$user_id,$user_id);
		if (isset($result) && mr2rows($result)>0)
		{
			$smarty->assign("previous_user",mr2array_single($result));
		}
	}

	$smarty->assign("errors",$errors);
	$smarty->assign("data",$data);
	return '';
}

function member_profile_viewGetHash($block_config)
{
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	} else {
		return "$var_user_id";
	}
}

function member_profile_viewCacheControl($block_config)
{
	return "user_nocache";
}

function member_profile_viewAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='send_message_complete' || $_REQUEST['action']=='add_to_friends_complete' || $_REQUEST['action']=='remove_from_friends' || $_REQUEST['action']=='ignore_user' || $_REQUEST['action']=='remove_from_ignores' || $_REQUEST['action']=='confirm_add_to_friends'))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		member_profile_viewShow($block_config,null);
	}
}

function member_profile_viewMetaData()
{
	return array(
		// object context
		array("name"=>"var_user_id", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"user_id"),

		// additional data
		array("name"=>"show_next_and_previous_info", "group"=>"additional_data", "type"=>"", "is_required"=>0),
	);
}

function member_profile_viewLegalRequestVariables()
{
	return array('action');
}

function member_profile_viewJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

function member_profile_viewPreProcess($block_config,$object_id)
{
	global $config;

	$user_id=intval($_REQUEST[$block_config['var_user_id']]);
	if ($user_id>0 && intval($_SESSION['user_id'])<>$user_id)
	{
		$stats_str=$user_id;
		$fh=fopen("$config[project_path]/admin/data/stats/profiles_id.dat","a+");
		flock($fh,LOCK_EX);
		fwrite($fh,$stats_str."\r\n");
		fclose($fh);
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
