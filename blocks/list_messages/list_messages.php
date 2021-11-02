<?php
function list_messagesShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$folder=strtolower(trim($_REQUEST[$block_config['var_folder']]));
	if ($folder=='')
	{
		$folder=trim($block_config['folder']);
	}
	$user_from_id=intval($_REQUEST[$block_config['var_user_id']]);
	if ($user_from_id<1 && !in_array($folder,array('inbox','invites','outbox','unread'))) {$folder='inbox';}

	$user_id=intval($_SESSION['user_id']);

	$errors=null;
	$errors_async=null;

	if ($_REQUEST['action']=='send')
	{
		if ($user_id>0)
		{
			$reply_to_user_id=intval($_REQUEST['reply_to_user_id']);
			$message_id=intval($_REQUEST['message_id']);
			$message=trim(strip_tags($_REQUEST['message']));

			if ($message_id>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_from_id=? and message_id=? and type_id=0",$user_id,$message_id))>0)
				{
					sql_pr("update $config[tables_prefix]messages set message=?, message_md5=md5(message) where message_id=?",$message,$message_id);

					if ($_REQUEST['mode']=='async')
					{
						$message_data=array('message_id'=>$message_id);
						async_return_request_status(null,null,$message_data);
					} else {
						header("Location: ?action=send_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'forbidden','block'=>'list_messages')));
				}
			} elseif ($reply_to_user_id>0 && $user_id<>$reply_to_user_id)
			{
				$tokens_required=0;
				$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
				if ($memberzone_data['ENABLE_TOKENS_INTERNAL_MESSAGES']==1)
				{
					$tokens_required=intval($memberzone_data['TOKENS_INTERNAL_MESSAGES']);
				}

				if ($message=='')
				{
					$errors['message']=1;
					$errors_async[]=array('error_field_name'=>'message','error_code'=>'required','block'=>'list_messages');
				} elseif ($tokens_required>0 && $tokens_required>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$user_id)))
				{
					$errors['message']=2;
					$errors_async[]=array('error_field_name'=>'message','error_code'=>'not_enough_tokens','block'=>'list_messages');
				}

				if (!is_array($errors))
				{
					$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
					if (strpos($antispam_action, 'error') !== false)
					{
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
						async_return_request_status(array(array('error_code'=>'spam','block'=>'list_messages')));
					}

					$message_id=sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, message=?, message_md5=md5(message), ip=?, added_date=?",$reply_to_user_id,$user_id,$message,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$user_id,$reply_to_user_id);

					if ($tokens_required>0)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-$tokens_required, 0) where user_id=?",$user_id);
						$_SESSION['tokens_available']=mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$user_id));
					}

					if (strpos($antispam_action, 'delete') !== false)
					{
						sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where message_id=?", $message_id);
						sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
					}

					if ($_REQUEST['mode']=='async')
					{
						$message_data=array('message_id'=>$message_id);
						async_return_request_status(null,null,$message_data);
					} else {
						header("Location: ?action=send_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status($errors_async);
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='confirm_add_to_friends' || $_REQUEST['action']=='reject_add_to_friends')
	{
		if ($user_id>0)
		{
			$user_from_id=intval($_REQUEST['message_from_user_id']);
			if ($user_from_id>0 && $user_id<>$user_from_id)
			{
				if ($_REQUEST['action']=='reject_add_to_friends' || isset($_REQUEST['reject']) || isset($_REQUEST['reject_x']))
				{
					if (sql_delete("delete from $config[tables_prefix]friends where user_id=? and friend_id=?",$user_from_id,$user_id)>0)
					{
						sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1, read_date=? where type_id=1 and message!='' and user_id=? and user_from_id=?",date("Y-m-d H:i:s"),$user_id,$user_from_id);
						sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$user_id,$user_from_id);
						sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=2, message='', added_date=?", $user_from_id,$user_id,date("Y-m-d H:i:s"));

						messages_changed();
					}

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=reject_add_to_friends_done");die;
					}
				} elseif ($_REQUEST['action']=='confirm_add_to_friends' || isset($_REQUEST['confirm']) || isset($_REQUEST['confirm_x']))
				{
					if (sql_update("update $config[tables_prefix]friends set is_approved=1, approved_date=? where user_id=? and friend_id=?",date("Y-m-d H:i:s"),$user_from_id,$user_id)>0)
					{
						sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1, read_date=? where type_id=1 and message!='' and user_id=? and user_from_id=?",date("Y-m-d H:i:s"),$user_id,$user_from_id);
						sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$user_id,$user_from_id);
						sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=4, message='', added_date=?", $user_from_id,$user_id,date("Y-m-d H:i:s"));
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=10, user_id=?, user_target_id=?, added_date=?",$user_from_id,$user_id,date("Y-m-d H:i:s"));

						messages_changed();
					}

					friends_changed(array($user_id,$user_from_id));

					if ($_REQUEST['mode']=='async')
					{
						async_return_request_status();
					} else {
						header("Location: ?action=confirm_add_to_friends_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='confirm_all_invites')
	{
		if ($user_id>0)
		{
			$confirm_ids=mr2array_list(sql_pr("select distinct user_from_id from $config[tables_prefix]messages where user_id=? and type_id=1 and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)",$user_id,$user_id));
			foreach ($confirm_ids as $friend_id)
			{
				if (sql_update("update $config[tables_prefix]friends set is_approved=1, approved_date=? where user_id=? and friend_id=?",date("Y-m-d H:i:s"),$friend_id,$user_id)>0)
				{
					sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1, read_date=? where type_id=1 and message!='' and user_id=? and user_from_id=?",date("Y-m-d H:i:s"),$user_id,$friend_id);
					sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$user_id,$friend_id);
					sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=4, message='', added_date=?", $friend_id,$user_id,date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=10, user_id=?, user_target_id=?, added_date=?",$friend_id,$user_id,date("Y-m-d H:i:s"));

					messages_changed();
				}
			}

			$confirm_ids[]=$user_id;
			friends_changed($confirm_ids);

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=confirm_all_invites_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='reject_all_invites')
	{
		if ($user_id>0)
		{
			$reject_ids=mr2array_list(sql_pr("select distinct user_from_id from $config[tables_prefix]messages where user_id=? and type_id=1 and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)",$user_id,$user_id));
			foreach ($reject_ids as $friend_id)
			{
				if (sql_delete("delete from $config[tables_prefix]friends where user_id=? and friend_id=?",$friend_id,$user_id)>0)
				{
					sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1, read_date=? where type_id=1 and message!='' and user_id=? and user_from_id=?",date("Y-m-d H:i:s"),$user_id,$friend_id);
					sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",$user_id,$friend_id);
					sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=2, message='', added_date=?",$friend_id,$user_id,date("Y-m-d H:i:s"));

					messages_changed();
				}
			}

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=reject_all_invites_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='delete' && is_array($_REQUEST['delete']))
	{
		if ($user_id>0)
		{
			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));
			sql_pr("delete from $config[tables_prefix]messages where type_id!=1 and message_id in ($delete_ids) and ((user_id=? and is_hidden_from_user_from_id=1) or (user_from_id=? and is_hidden_from_user_id=1))",$user_id,$user_id);
			sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where type_id!=1 and message_id in ($delete_ids) and user_id=?",$user_id);
			sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_from_id=1 where type_id!=1 and message_id in ($delete_ids) and user_from_id=?",$user_id);

			messages_changed();

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='delete_conversation')
	{
		if ($user_id>0)
		{
			$conversation_user_id=intval($_REQUEST['conversation_user_id']);
			if ($conversation_user_id>0 && $user_id<>$conversation_user_id)
			{
				sql_pr("delete from $config[tables_prefix]messages where type_id!=1 and ((user_id=? and user_from_id=? and is_hidden_from_user_from_id=1) or (user_from_id=? and user_id=? and is_hidden_from_user_id=1))",$user_id,$conversation_user_id,$user_id,$conversation_user_id);
				sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where type_id!=1 and user_id=? and user_from_id=?",$user_id,$conversation_user_id);
				sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_from_id=1 where type_id!=1 and user_from_id=? and user_id=?",$user_id,$conversation_user_id);

				messages_changed();

				if ($_REQUEST['mode']=='async')
				{
					async_return_request_status();
				} else {
					header("Location: ?action=delete_done");die;
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($_REQUEST['action']=='ignore_conversation')
	{
		if ($user_id>0)
		{
			$conversation_user_id=intval($_REQUEST['conversation_user_id']);
			if ($conversation_user_id>0 && $user_id<>$conversation_user_id)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$user_id,$conversation_user_id))==0)
				{
					sql_pr("insert into $config[tables_prefix]users_ignores set user_id=?, ignored_user_id=?",$user_id,$conversation_user_id);

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
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_messages')));
		}
	}

	if ($user_id==0)
	{
		if ($_REQUEST['mode']=='async')
		{
			header('HTTP/1.0 403 Forbidden');die;
		}

		$_SESSION['private_page_referer']=$_SERVER['REQUEST_URI'];
		if (isset($block_config['redirect_unknown_user_to']))
		{
			$url=process_url($block_config['redirect_unknown_user_to']);
			return "status_302: $url";
		} else
		{
			return "status_302: $config[project_url]";
		}
	}

	$data=array();
	$from_users_count=0;

	$total_count=0;
	$from=intval($_REQUEST[$block_config['var_from']]);
	if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}

	if ($user_from_id>0)
	{
		$folder='conversation';
		$where="(($config[tables_prefix]messages.user_id=$user_id and user_from_id=$user_from_id and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id)) or ($config[tables_prefix]messages.user_id=$user_from_id and user_from_id=$user_id and is_hidden_from_user_from_id=0 and $config[tables_prefix]messages.user_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id)))";

		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]messages where $where"));
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$limit=intval($block_config['items_per_page']);
		if ($limit==0) {$limit=$total_count;}

		$data=mr2array(sql("select
								$config[tables_prefix]messages.*,
								$config[tables_prefix]users.display_name as user_from_name,
								$config[tables_prefix]users.avatar as user_from_avatar,
								$config[tables_prefix]users.gender_id as user_from_gender_id,
								$config[tables_prefix]users.country_id as user_from_country_id,
								$config[tables_prefix]users.city as user_from_city
							from $config[tables_prefix]messages left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]messages.user_from_id
							where $where order by added_date desc LIMIT $from, $limit"));

		$user_info=mr2array_single(sql_pr("select * from $config[tables_prefix]users where user_id=?",$user_from_id));
		if (count($user_info)>0)
		{
			$smarty->assign("conversation_user_id",$user_from_id);
			$smarty->assign("conversation_display_name",$user_info['display_name']);
			$smarty->assign("conversation_avatar",$user_info['avatar']);
			$smarty->assign("conversation_username",$user_info['username']);
			if ($user_info['avatar']!='')
			{
				$smarty->assign("conversation_avatar_url","$config[content_url_avatars]/".$user_info['avatar']);
			}
			$smarty->assign("conversation_gender_id",$user_info['gender_id']);
			$smarty->assign("conversation_city",$user_info['city']);
			$storage[$object_id]['conversation_user_id']=$user_from_id;
			$storage[$object_id]['conversation_display_name']=$user_info['display_name'];
			if ($user_info['avatar']!='')
			{
				$storage[$object_id]['conversation_avatar']=$user_info['avatar'];
			}
			$storage[$object_id]['conversation_avatar_url']="$config[content_url_avatars]/".$user_info['avatar'];
			$storage[$object_id]['conversation_gender_id']=$user_info['gender_id'];
			$storage[$object_id]['conversation_city']=$user_info['city'];
			if ($user_info['country_id']>0)
			{
				$smarty->assign("conversation_country_id",$user_info['country_id']);
				$smarty->assign("conversation_country",$list_countries['name'][$user_info['country_id']]);
				$storage[$object_id]['conversation_country_id']=$user_info['country_id'];
				$storage[$object_id]['conversation_country']=$list_countries['name'][$user_info['country_id']];
			}
			$from_users_count=1;
		} else {
			return 'status_404';
		}
	} else {
		switch ($folder)
		{
			case 'inbox':
			case 'invites':
				$where="$config[tables_prefix]messages.user_id=$user_id and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id)";
				if ($folder=='invites')
				{
					$where.=" and type_id=1";
				}

				$total_count=mr2number(sql("select count(*) from $config[tables_prefix]messages where $where"));
				if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

				$limit=intval($block_config['items_per_page']);
				if ($limit==0) {$limit=$total_count;}

				$data=mr2array(sql("select
										$config[tables_prefix]messages.*,
										$config[tables_prefix]users.display_name as user_from_name,
										$config[tables_prefix]users.avatar as user_from_avatar,
										$config[tables_prefix]users.gender_id as user_from_gender_id,
										$config[tables_prefix]users.country_id as user_from_country_id,
										$config[tables_prefix]users.city as user_from_city
									from $config[tables_prefix]messages left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]messages.user_from_id
									where $where order by added_date desc LIMIT $from, $limit"));
				$from_users_count=mr2number(sql("select count(distinct user_from_id) from $config[tables_prefix]messages where $where"));
			break;
			case 'outbox':
				$where="$config[tables_prefix]messages.user_from_id=$user_id and is_hidden_from_user_from_id=0 and type_id in (0) and $config[tables_prefix]messages.user_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id)";

				$total_count=mr2number(sql("select count(*) from $config[tables_prefix]messages where $where"));
				if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

				$limit=intval($block_config['items_per_page']);
				if ($limit==0) {$limit=$total_count;}

				$data=mr2array(sql("select
										$config[tables_prefix]messages.*,
										$config[tables_prefix]users.display_name as user_to_name,
										$config[tables_prefix]users.avatar as user_to_avatar,
										$config[tables_prefix]users.gender_id as user_to_gender_id,
										$config[tables_prefix]users.country_id as user_to_country_id,
										$config[tables_prefix]users.city as user_to_city
									from $config[tables_prefix]messages left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]messages.user_id
									where $where order by added_date desc LIMIT $from, $limit"));
				$from_users_count=mr2number(sql("select count(distinct user_id) from $config[tables_prefix]messages where $where"));
			break;
			case 'unread':
				$where="$config[tables_prefix]messages.user_id=$user_id and is_hidden_from_user_id=0 and is_read=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id)";
				$total_count=mr2number(sql("select count(*) from $config[tables_prefix]messages where $where"));
				$from=0;

				$data=mr2array(sql("select
										$config[tables_prefix]messages.*,
										ceil(sin(abs(cast($config[tables_prefix]messages.type_id as signed) - 1))) as sort_order,
										$config[tables_prefix]users.display_name as user_from_name,
										$config[tables_prefix]users.avatar as user_from_avatar,
										$config[tables_prefix]users.gender_id as user_from_gender_id,
										$config[tables_prefix]users.country_id as user_from_country_id,
										$config[tables_prefix]users.city as user_from_city
									from $config[tables_prefix]messages left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]messages.user_from_id
									where $where order by sort_order desc, added_date desc"));
				$from_users_count=mr2number(sql("select count(distinct user_from_id) from $config[tables_prefix]messages where $where"));
			break;
		}
	}

	$update_ids=array();
	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);
		if ($data[$k]['is_read']==0)
		{
			$update_ids[]=intval($data[$k]['message_id']);
		}
		if ($data[$k]['user_from_avatar']!='')
		{
			$data[$k]['user_from_avatar_url']="$config[content_url_avatars]/".$data[$k]['user_from_avatar'];
		}
		if ($data[$k]['user_to_avatar']!='')
		{
			$data[$k]['user_to_avatar_url']="$config[content_url_avatars]/".$data[$k]['user_to_avatar'];
		}
		if ($data[$k]['user_from_country_id']>0)
		{
			$data[$k]['user_from_country']=$list_countries['name'][$data[$k]['user_from_country_id']];
		}
		if ($data[$k]['user_to_country_id']>0)
		{
			$data[$k]['user_to_country']=$list_countries['name'][$data[$k]['user_to_country_id']];
		}
	}
	if (count($update_ids)>0)
	{
		$update_ids=implode(",",$update_ids);
		sql_pr("update $config[tables_prefix]messages set is_read=1, read_date=? where message_id in ($update_ids) and type_id!=1 and user_id=?",date("Y-m-d H:i:s"),$user_id);
		messages_changed();
	}

	if (isset($block_config['var_from']))
	{
		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,($folder=='unread'?$total_count:$block_config['items_per_page']),$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	}

	if ($folder=='unread')
	{
		$storage[$object_id]['items_per_page']=$total_count;
		$smarty->assign("items_per_page",$total_count);
	} else
	{
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$my_messages_inbox=mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where $config[tables_prefix]messages.user_id=? and is_hidden_from_user_id=0",$user_id));
	$my_messages_sent=mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where $config[tables_prefix]messages.user_from_id=? and is_hidden_from_user_from_id=0 and type_id in (0)",$user_id));

	$smarty->assign("my_messages_inbox",$my_messages_inbox);
	$smarty->assign("my_messages_sent",$my_messages_sent);
	$storage[$object_id]['my_messages_inbox']=$my_messages_inbox;
	$storage[$object_id]['my_messages_sent']=$my_messages_sent;

	$smarty->assign("folder",$folder);
	$smarty->assign("users_count",$from_users_count);
	$smarty->assign("data",$data);

	return '';
}

function list_messagesGetHash($block_config)
{
	return "nocache";
}

function list_messagesCacheControl($block_config)
{
	return "nocache";
}

function list_messagesAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='send' || $_REQUEST['action']=='confirm_add_to_friends' || $_REQUEST['action']=='reject_add_to_friends' || $_REQUEST['action']=='delete_conversation' || $_REQUEST['action']=='ignore_conversation' || $_REQUEST['action']=='confirm_all_invites' || $_REQUEST['action']=='reject_all_invites')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_messagesShow($block_config,null);
	}
	if ($_REQUEST['action']=='delete' && isset($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_messagesShow($block_config,null);
	}
}

function list_messagesMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// static filters
		array("name"=>"folder", "group"=>"static_filters", "type"=>"CHOICE[inbox,invites,outbox,unread]", "is_required"=>0),

		// dynamic filters
		array("name"=>"var_folder",  "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>0, "default_value"=>"folder"),
		array("name"=>"var_user_id", "group"=>"dynamic_filters", "type"=>"STRING", "is_required"=>1, "default_value"=>"user_id"),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login")
	);
}

function list_messagesJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>