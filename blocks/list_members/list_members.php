<?php
function list_membersShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$list_countries,$website_ui_data,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	if ($_REQUEST['action']=='delete_from_friends' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$delete_ids=array_map("intval",$_REQUEST['delete']);
			foreach ($delete_ids as $friend_id)
			{
				if (sql_delete("delete from $config[tables_prefix]friends where (user_id=? and friend_id=?) or (friend_id=? and user_id=?)",$_SESSION['user_id'],$friend_id,$_SESSION['user_id'],$friend_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=3, message='', added_date=?",$friend_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=11, user_id=?, user_target_id=?, added_date=?",$_SESSION['user_id'],$friend_id,date("Y-m-d H:i:s"));
				}
			}

			$delete_ids[]=$_SESSION['user_id'];
			friends_changed($delete_ids);

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members')));
		}
	}
	if ($_REQUEST['action']=='delete_from_subscriptions' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));
			sql_pr("delete from $config[tables_prefix]users_subscriptions where user_id=? and subscribed_object_id in ($delete_ids) and subscribed_type_id=1",$user_id);
			sql_pr("update $config[tables_prefix]users set subscribers_count=(select count(*) from $config[tables_prefix]users_subscriptions where subscribed_object_id=$config[tables_prefix]users.user_id and subscribed_type_id=1) where user_id in ($delete_ids)");

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_from_subscriptions_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members')));
		}
	}
	if ($_REQUEST['action']=='delete_from_conversations' && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$delete_ids=array_map("intval",$_REQUEST['delete']);
			foreach ($delete_ids as $conversation_id)
			{
				sql_pr("delete from $config[tables_prefix]messages where type_id!=1 and ((user_id=? and user_from_id=? and is_hidden_from_user_from_id=1) or (user_from_id=? and user_id=? and is_hidden_from_user_id=1))",$user_id,$conversation_id,$user_id,$conversation_id);
				sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_id=1 where type_id!=1 and user_id=? and user_from_id=?",$user_id,$conversation_id);
				sql_pr("update $config[tables_prefix]messages set is_hidden_from_user_from_id=1 where type_id!=1 and user_from_id=? and user_id=?",$user_id,$conversation_id);
			}

			messages_changed();

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members')));
		}
	}
	if ($_REQUEST['action']=='confirm_all_invites')
	{
		if ($_SESSION['user_id']>0)
		{
			$confirm_ids=mr2array_list(sql_pr("select distinct user_from_id from $config[tables_prefix]messages where user_id=? and type_id=1 and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)",$_SESSION['user_id'],$_SESSION['user_id']));
			foreach ($confirm_ids as $friend_id)
			{
				if (sql_update("update $config[tables_prefix]friends set is_approved=1, approved_date=? where user_id=? and friend_id=?",date("Y-m-d H:i:s"),$friend_id,$_SESSION['user_id'])>0)
				{
					sql_pr("update $config[tables_prefix]messages set type_id=0, is_read=1, read_date=? where type_id=1 and message!='' and user_id=? and user_from_id=?",date("Y-m-d H:i:s"),$_SESSION['user_id'],$friend_id);
					sql_pr("delete from $config[tables_prefix]messages where type_id=1 and user_id=? and user_from_id=?",intval($_SESSION['user_id']),$friend_id);
					sql_pr("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, type_id=4, message='', added_date=?", $friend_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=10, user_id=?, user_target_id=?, added_date=?",$friend_id,$_SESSION['user_id'],date("Y-m-d H:i:s"));
				}
			}

			$confirm_ids[]=$_SESSION['user_id'];
			friends_changed($confirm_ids);
			messages_changed();

			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=confirm_all_invites_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members')));
		}
	}

	$where='';
	$join_table='';

	if (isset($block_config['mode_friends']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
			if (count($user_info)>0)
			{
				$smarty->assign("user_id",$user_id);
				$smarty->assign("username",$user_info['username']);
				$smarty->assign("display_name",$user_info['display_name']);
				$smarty->assign("avatar",$user_info['avatar']);
				$smarty->assign("gender_id",$user_info['gender_id']);
				$smarty->assign("city",$user_info['city']);
				$storage[$object_id]['user_id']=$user_id;
				$storage[$object_id]['username']=$user_info['username'];
				$storage[$object_id]['display_name']=$user_info['display_name'];
				$storage[$object_id]['avatar']=$user_info['avatar'];
				$storage[$object_id]['gender_id']=$user_info['gender_id'];
				$storage[$object_id]['city']=$user_info['city'];
				if ($user_info['country_id']>0)
				{
					$smarty->assign("country_id",$user_info['country_id']);
					$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
					$storage[$object_id]['country_id']=$user_info['country_id'];
					$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
				}
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
		} else {
			if ($_GET['mode']=='async')
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

		settype($_SESSION['user_id'],"integer");
		$join_table="inner join (select user_id, min(approved_date) as added_to_friends_date from (select friend_id as user_id, approved_date from $config[tables_prefix]friends where user_id=$user_id and is_approved=1 union all select user_id, approved_date from $config[tables_prefix]friends where friend_id=$user_id and is_approved=1) Y GROUP BY user_id) X on $config[tables_prefix]users.user_id=X.user_id";
		$storage[$object_id]['mode_friends']=1;
		$smarty->assign("mode_friends",1);
	} elseif (isset($block_config['mode_subscribed']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
			if (count($user_info)>0)
			{
				$smarty->assign("user_id",$user_id);
				$smarty->assign("username",$user_info['username']);
				$smarty->assign("display_name",$user_info['display_name']);
				$smarty->assign("avatar",$user_info['avatar']);
				$smarty->assign("gender_id",$user_info['gender_id']);
				$smarty->assign("city",$user_info['city']);
				$storage[$object_id]['user_id']=$user_id;
				$storage[$object_id]['username']=$user_info['username'];
				$storage[$object_id]['display_name']=$user_info['display_name'];
				$storage[$object_id]['avatar']=$user_info['avatar'];
				$storage[$object_id]['gender_id']=$user_info['gender_id'];
				$storage[$object_id]['city']=$user_info['city'];
				if ($user_info['country_id']>0)
				{
					$smarty->assign("country_id",$user_info['country_id']);
					$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
					$storage[$object_id]['country_id']=$user_info['country_id'];
					$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
				}
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$smarty->assign("can_manage",1);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
			$storage[$object_id]['can_manage']=1;
		} else {
			if ($_GET['mode']=='async')
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

		$join_table="inner join (select subscribed_object_id as user_id, added_date as subscribed_date from $config[tables_prefix]users_subscriptions where user_id=$user_id and subscribed_type_id=1) X on $config[tables_prefix]users.user_id=X.user_id";
		$storage[$object_id]['mode_subscribed']=1;
		$smarty->assign("mode_subscribed",1);
	} elseif (isset($block_config['mode_subscribers']))
	{
		if (isset($block_config['var_user_id']))
		{
			$user_id=intval($_REQUEST[$block_config['var_user_id']]);
			$user_info=mr2array_single(sql_pr("select username, display_name, avatar, gender_id, country_id, city from $config[tables_prefix]users where user_id=?",$user_id));
			if (count($user_info)>0)
			{
				$smarty->assign("user_id",$user_id);
				$smarty->assign("username",$user_info['username']);
				$smarty->assign("display_name",$user_info['display_name']);
				$smarty->assign("avatar",$user_info['avatar']);
				$smarty->assign("gender_id",$user_info['gender_id']);
				$smarty->assign("city",$user_info['city']);
				$storage[$object_id]['user_id']=$user_id;
				$storage[$object_id]['username']=$user_info['username'];
				$storage[$object_id]['display_name']=$user_info['display_name'];
				$storage[$object_id]['avatar']=$user_info['avatar'];
				$storage[$object_id]['gender_id']=$user_info['gender_id'];
				$storage[$object_id]['city']=$user_info['city'];
				if ($user_info['country_id']>0)
				{
					$smarty->assign("country_id",$user_info['country_id']);
					$smarty->assign("country",$list_countries['name'][$user_info['country_id']]);
					$storage[$object_id]['country_id']=$user_info['country_id'];
					$storage[$object_id]['country']=$list_countries['name'][$user_info['country_id']];
				}
			} else {
				return 'status_404';
			}
		} elseif ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
		} else {
			if ($_GET['mode']=='async')
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

		$join_table="inner join (select user_id, added_date as subscribed_date from $config[tables_prefix]users_subscriptions where subscribed_object_id=$user_id and subscribed_type_id=1) X on $config[tables_prefix]users.user_id=X.user_id";
		$storage[$object_id]['mode_subscribers']=1;
		$smarty->assign("mode_subscribers",1);
	} elseif (isset($block_config['mode_conversations']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
		} else {
			if ($_GET['mode']=='async')
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

		$join_table="inner join (select user_id, max(added_date) as conversation_last_date, (select message from $config[tables_prefix]messages where $config[tables_prefix]messages.user_from_id=Y.user_id and $config[tables_prefix]messages.user_id=$user_id order by $config[tables_prefix]messages.added_date desc limit 1) as last_message, sum(messages) as conversation_total_messages, sum(messages_read) as conversation_read_messages, sum(messages) - sum(messages_read) as conversation_unread_messages from (select user_from_id as user_id, max(added_date) as added_date, count(*) as messages, sum(is_read) as messages_read from $config[tables_prefix]messages where user_id=$user_id and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id) group by user_from_id union all select user_id, max(added_date) as added_date, count(*) as messages, count(*) as messages_read from $config[tables_prefix]messages where user_from_id=$user_id and is_hidden_from_user_from_id=0 and user_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id) group by user_id) Y group by user_id) X on $config[tables_prefix]users.user_id=X.user_id";
		$storage[$object_id]['mode_conversations']=1;
		$smarty->assign("mode_conversations",1);

		messages_changed();
	} elseif (isset($block_config['mode_invites']))
	{
		if ($_SESSION['user_id']>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$smarty->assign("user_id",$user_id);
			$smarty->assign("display_name",$_SESSION['display_name']);
			$smarty->assign("avatar",$_SESSION['avatar']);
			$storage[$object_id]['user_id']=$user_id;
			$storage[$object_id]['display_name']=$_SESSION['display_name'];
			$storage[$object_id]['avatar']=$_SESSION['avatar'];
		} else {
			if ($_GET['mode']=='async')
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

		$join_table="inner join (select user_from_id as user_id, max(added_date) as invite_date from $config[tables_prefix]messages where user_id=$user_id and type_id=1 and is_read=0 and is_hidden_from_user_id=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=$user_id) group by user_from_id) X on $config[tables_prefix]users.user_id=X.user_id";
		$storage[$object_id]['mode_invites']=1;
		$smarty->assign("mode_invites",1);

		messages_changed();
	}

	if (isset($block_config['match_locale']))
	{
		$language_code=sql_escape($config['locale']);
		$where.=" and language_code='$language_code'";
	}

	if (isset($block_config['var_country_id']) && !isset($block_config['show_only_current_countries']) && $_REQUEST[$block_config['var_country_id']]<>'')
	{
		$where.=" and country_id=".intval($_REQUEST[$block_config['var_country_id']]);

		$smarty->assign('cities',mr2array_list(sql_pr("select distinct city from $config[tables_prefix]users where country_id=? and city!=''",intval($_REQUEST[$block_config['var_country_id']]))));
	}
	if (isset($block_config['var_city']) && $_REQUEST[$block_config['var_city']]<>'')
	{
		$city=sql_escape(trim($_REQUEST[$block_config['var_city']]));
		$where.=" and city like '%$city%'";
	}
	if (isset($block_config['var_gender_id']) && $_REQUEST[$block_config['var_gender_id']]<>'')
	{
		$where.=" and gender_id=".intval($_REQUEST[$block_config['var_gender_id']]);
	}
	if (isset($block_config['var_relationship_status_id']) && $_REQUEST[$block_config['var_relationship_status_id']]<>'')
	{
		$where.=" and relationship_status_id=".intval($_REQUEST[$block_config['var_relationship_status_id']]);
	}
	if (isset($block_config['var_orientation_id']) && $_REQUEST[$block_config['var_orientation_id']]<>'')
	{
		$where.=" and orientation_id=".intval($_REQUEST[$block_config['var_orientation_id']]);
	}
	if (isset($block_config['var_show_only_with_avatar']) && $_REQUEST[$block_config['var_show_only_with_avatar']]==1)
	{
		$where.=" and avatar<>''";
	}
	if (isset($block_config['show_only_online']) || (isset($block_config['var_show_only_online']) && $_REQUEST[$block_config['var_show_only_online']]==1))
	{
		$online_interval=intval($website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL'])*60+30;
		$last_online_date=date("Y-m-d H:i:s",time()-$online_interval);
		$where.=" and last_online_date>'$last_online_date'";
	}
	if (isset($block_config['var_show_only_current_countries']) && intval($_REQUEST[$block_config['var_show_only_current_countries']])==1)
	{
		$country_code=$_SERVER['GEOIP_COUNTRY_CODE'];
		if ($country_code<>'')
		{
			$country_id=intval(array_search(strtolower($country_code),$list_countries['code']));
			if ($country_id>0)
			{
				$where.=" and country_id=$country_id";
				$smarty->assign("current_country_id",$country_id);
				$smarty->assign("current_country_name",$list_countries['name'][$country_id]);
				$storage[$object_id]['current_country_id']=$country_id;
				$storage[$object_id]['current_country_name']=$list_countries['name'][$country_id];
			}
		}
	}
	if (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'')
	{
		$q=trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]),false));
		$q=trim(str_replace('[dash]','-',str_replace('-',' ',str_replace('--','[dash]',str_replace('?','',$q)))));

		if ($q=='')
		{
			$where.=" and 1=0";
			$unescaped_q='';
		} else
		{
			$unescaped_q=$q;
			$q=sql_escape($q);

			if ($block_config['search_method']==2)
			{
				$temp=explode(" ",$q);
				$where2='';
				foreach ($temp as $temp_value)
				{
					$country_ids=mr2array_list(sql_pr("select country_id from $config[tables_prefix]list_countries where title like ?", "%$temp_value%"));
					$where2.=" or display_name like '%$temp_value%' or city like '%$temp_value%' or about_me like '%$temp_value%'";
					if (count($country_ids)>0)
					{
						$country_ids=implode(',',array_map('intval',$country_ids));
						$where2.=" or country_id in ($country_ids)";
					}
				}
				if ($where2<>'')
				{
					$where2=substr($where2,4);
				}
				$where.=" and ($where2)";
			} else
			{
				$country_ids=mr2array_list(sql_pr("select country_id from $config[tables_prefix]list_countries where title like ?", "%$q%"));
				$where_country_ids='';
				if (count($country_ids)>0)
				{
					$country_ids=implode(',',array_map('intval',$country_ids));
					$where_country_ids.=" or country_id in ($country_ids)";
				}
				$where.=" and (display_name like '%$q%' or city like '%$q%' or about_me like '%$q%' $where_country_ids)";
			}
		}

		$storage[$object_id]['search_keyword']=$unescaped_q;
		$smarty->assign('search_keyword',$unescaped_q);
	}
	if (isset($block_config['show_only_with_avatar']))
	{
		$where.=" and avatar<>''";
	}
	if (isset($block_config['show_only_trusted']))
	{
		$where.=" and is_trusted=1";
	}
	if (isset($block_config['show_only_current_countries']))
	{
		$country_code=$_SERVER['GEOIP_COUNTRY_CODE'];
		if ($country_code<>'')
		{
			$country_id=intval(array_search(strtolower($country_code),$list_countries['code']));
			if ($country_id>0)
			{
				$where.=" and country_id=$country_id";
				$smarty->assign("current_country_id",$country_id);
				$smarty->assign("current_country_name",$list_countries['name'][$country_id]);
				$storage[$object_id]['current_country_id']=$country_id;
				$storage[$object_id]['current_country_name']=$list_countries['name'][$country_id];
			}
		}
	}
	if (isset($block_config['show_gender']))
	{
		if (intval($block_config['show_gender'])>0)
		{
			$where.=" and gender_id=".intval($block_config['show_gender']);
		}
	}
	if (isset($block_config['show_status']))
	{
		$where.=" and status_id=".intval($block_config['show_status']);
	}
	if (isset($block_config['var_age_from']) && intval($_REQUEST[$block_config['var_age_from']])>0)
	{
		$age_from=intval($_REQUEST[$block_config['var_age_from']]);
		$where.=" and birth_date<='".date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d"),date("Y")-$age_from))."'";
	}
	if (isset($block_config['var_age_to']) && intval($_REQUEST[$block_config['var_age_to']])>0)
	{
		$age_to=intval($_REQUEST[$block_config['var_age_to']]);
		$where.=" and birth_date>='".date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("d"),date("Y")-$age_to-1))."'";
	}

	for ($i=1;$i<=10;$i++)
	{
		if (isset($block_config["var_custom$i"]) && trim($_REQUEST[$block_config["var_custom$i"]])!='')
		{
			$where.=" and custom$i='".sql_escape(trim($_REQUEST[$block_config["var_custom$i"]]))."'";
		}
	}

	$data=list_membersMetaData();
	foreach ($data as $res)
	{
		if (strpos($res['type'],"SORTING")!==false)
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="added_date";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	if ($sort_by_clear=='display_name')
	{
		$sort_by_clear=$sort_by_clear="lower(display_name)";
		if (strpos($sort_by," desc")!==false) {$direction="desc";} else {$direction="asc";}
	}

	$sort_by="$sort_by_clear $direction";
	if ($sort_by_clear=='user_id')
	{
		$sort_by="$config[tables_prefix]users.user_id $direction";
	}

	if (isset($block_config['mode_friends']) && $sort_by_clear=='added_date') {$sort_by="added_to_friends_date $direction";}
	if (isset($block_config['mode_conversations']) && $sort_by_clear=='added_date') {$sort_by="conversation_last_date $direction";}
	if (isset($block_config['mode_invites']) && $sort_by_clear=='added_date') {$sort_by="invite_date $direction";}
	if (isset($block_config['mode_subscribed']) && $sort_by_clear=='added_date') {$sort_by="subscribed_date $direction";}
	if (isset($block_config['mode_subscribers']) && $sort_by_clear=='added_date') {$sort_by="subscribed_date $direction";}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]users $join_table where status_id not in (0,1,4) $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("select *, (select $database_selectors[generic_selector_title] from $config[tables_prefix]categories where category_id=$config[tables_prefix]users.favourite_category_id) as favourite_category from $config[tables_prefix]users $join_table where status_id not in (0,1,4) $where order by $sort_by LIMIT $from, $block_config[items_per_page]"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else {
		$limit='';
		if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

		$data=mr2array(sql("select *, (select $database_selectors[generic_selector_title] from $config[tables_prefix]categories where category_id=$config[tables_prefix]users.favourite_category_id) as favourite_category from $config[tables_prefix]users $join_table where status_id not in (0,1,4) $where order by $sort_by $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['country']=$list_countries['name'][$v['country_id']];
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);
		if ($data[$k]['birth_date']<>'0000-00-00')
		{
			$data[$k]['age']=get_time_passed($data[$k]['birth_date']);
		} else {
			$data[$k]['age'] = '';
		}
		if (isset($block_config['mode_friends']))
		{
			$data[$k]['time_passed_from_adding_to_friends']=get_time_passed($data[$k]['added_to_friends_date']);
		}
		$data[$k]['is_online']=0;
		if ($website_ui_data['ENABLE_USER_ONLINE_STATUS_REFRESH']==1)
		{
			if (time()-strtotime($data[$k]['last_online_date'])<$website_ui_data['USER_ONLINE_STATUS_REFRESH_INTERVAL']*60 + 30)
			{
				$data[$k]['is_online']=1;
			}
		}
		if ($data[$k]['avatar']!='')
		{
			$data[$k]['avatar_url']="$config[content_url_avatars]/".$data[$k]['avatar'];
		}
	}

	$smarty->assign("data",$data);

	return '';
}

function list_membersAsync($block_config)
{
	global $config;

	if (($_REQUEST['action']=='delete_from_friends' || $_REQUEST['action']=='delete_from_subscriptions' || $_REQUEST['action']=='delete_from_conversations') && is_array($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_membersShow($block_config,null);
	} elseif ($_REQUEST['action']=='confirm_all_invites')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_membersShow($block_config,null);
	}
}

function list_membersGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_country_id=intval($_REQUEST[$block_config['var_country_id']]);
	$var_city=trim($_REQUEST[$block_config['var_city']]);
	$var_gender_id=intval($_REQUEST[$block_config['var_gender_id']]);
	$var_relationship_status_id=intval($_REQUEST[$block_config['var_relationship_status_id']]);
	$var_orientation_id=intval($_REQUEST[$block_config['var_orientation_id']]);
	$var_age_from=intval($_REQUEST[$block_config['var_age_from']]);
	$var_age_to=intval($_REQUEST[$block_config['var_age_to']]);
	$var_show_only_with_avatar=trim($_REQUEST[$block_config['var_show_only_with_avatar']]);
	$var_show_only_online=trim($_REQUEST[$block_config['var_show_only_online']]);
	$var_show_only_current_countries=trim($_REQUEST[$block_config['var_show_only_current_countries']]);
	$var_search=trim($_REQUEST[$block_config['var_search']]);
	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);
	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);

	$var_custom='';
	for ($i=1;$i<=10;$i++)
	{
		$var_custom.=trim($_REQUEST[$block_config["var_custom$i"]])."|";
	}

	if ((isset($block_config['mode_friends']) || isset($block_config['mode_subscribers']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	} elseif (isset($block_config['mode_conversations']) || isset($block_config['mode_invites']))
	{
		return "nocache";
	} elseif (isset($block_config['var_search']) && $_REQUEST[$block_config['var_search']]<>'') {
		return "runtime_nocache";
	} else {
		$result="$from|$items_per_page|$var_country_id|$var_city|$var_user_id|$var_gender_id|$var_relationship_status_id|$var_orientation_id|$var_age_from|$var_age_to|$var_show_only_with_avatar|$var_show_only_online|$var_show_only_current_countries|$var_search|$var_sort_by|$var_custom";
		if (isset($block_config['show_only_current_countries']) || intval($var_show_only_current_countries)==1)
		{
			$result="$result|$_SERVER[GEOIP_COUNTRY_CODE]";
		}
		return $result;
	}
}

function list_membersCacheControl($block_config)
{
	if ((isset($block_config['mode_friends']) || isset($block_config['mode_subscribers']) || isset($block_config['mode_subscribed'])) && !isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	if (isset($block_config['mode_conversations']) || isset($block_config['mode_invites']))
	{
		return "nocache";
	}
	return "default";
}

function list_membersMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[user_id,display_name,birth_date,video_viewed,album_viewed,profile_viewed,video_watched,album_watched,comments_videos_count,comments_albums_count,comments_cs_count,comments_models_count,comments_dvds_count,comments_posts_count,comments_playlists_count,comments_total_count,logins_count,public_videos_count,private_videos_count,premium_videos_count,total_videos_count,favourite_videos_count,public_albums_count,private_albums_count,premium_albums_count,total_albums_count,favourite_albums_count,added_date,last_login_date,last_online_date,activity,tokens_available]", "is_required"=>1, "default_value"=>"added_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"show_only_with_avatar",       "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_online",            "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_trusted",           "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_only_current_countries", "group"=>"static_filters", "type"=>"",              "is_required"=>0),
		array("name"=>"show_gender",                 "group"=>"static_filters", "type"=>"CHOICE[1,2]",   "is_required"=>0),
		array("name"=>"show_status",                 "group"=>"static_filters", "type"=>"CHOICE[2,3,6]", "is_required"=>0),
		array("name"=>"match_locale",                "group"=>"static_filters", "type"=>"",              "is_required"=>0),

		// dynamic filters
		array("name"=>"var_country_id",                  "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"country_id"),
		array("name"=>"var_city",                        "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"city"),
		array("name"=>"var_gender_id",                   "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"gender_id"),
		array("name"=>"var_relationship_status_id",      "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"relationship_status_id"),
		array("name"=>"var_orientation_id",              "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"orientation_id"),
		array("name"=>"var_age_from",                    "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"age_from"),
		array("name"=>"var_age_to",                      "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"age_to"),
		array("name"=>"var_show_only_with_avatar",       "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"show_only_with_avatar"),
		array("name"=>"var_show_only_online",            "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"show_only_online"),
		array("name"=>"var_show_only_current_countries", "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"show_only_current_countries"),
		array("name"=>"var_custom1",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom1"),
		array("name"=>"var_custom2",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom2"),
		array("name"=>"var_custom3",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom3"),
		array("name"=>"var_custom4",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom4"),
		array("name"=>"var_custom5",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom5"),
		array("name"=>"var_custom6",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom6"),
		array("name"=>"var_custom7",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom7"),
		array("name"=>"var_custom8",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom8"),
		array("name"=>"var_custom9",                     "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom9"),
		array("name"=>"var_custom10",                    "group"=>"dynamic_filters", "type"=>"STRING",  "is_required"=>0, "default_value"=>"custom10"),

		// search
		array("name"=>"var_search",    "group"=>"search", "type"=>"STRING",      "is_required"=>0, "default_value"=>"q"),
		array("name"=>"search_method", "group"=>"search", "type"=>"CHOICE[1,2]", "is_required"=>0, "default_value"=>"1"),

		// display modes
		array("name"=>"mode_conversations",       "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_invites",             "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_friends",             "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_subscribers",         "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"mode_subscribed",          "group"=>"display_modes", "type"=>"",       "is_required"=>0),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"/?login"),
	);
}

function list_membersLegalRequestVariables()
{
	return array('action');
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>