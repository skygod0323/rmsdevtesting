<?php
function list_members_blogShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$list_countries;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$user_id=0;
	$where_user="";
	if (isset($block_config['mode_global']))
	{
		$where_user="";
	} elseif (isset($block_config['var_user_id']))
	{
		$user_id=intval($_REQUEST[$block_config['var_user_id']]);
		$where_user="and $config[tables_prefix]users_blogs.user_id=$user_id";
	} elseif ($_SESSION['user_id']>0)
	{
		$user_id=intval($_SESSION['user_id']);
		$where_user="and $config[tables_prefix]users_blogs.user_id=$user_id";
	}

	$errors=null;
	$errors_async=null;

	if ($_REQUEST['action']=='add' || $_REQUEST['action']=='add_entry')
	{
		if ($_SESSION['user_id']>0)
		{
			if ($user_id>0)
			{
				$entry=trim($_REQUEST['entry']);

				if ($entry=='')
				{
					$errors['entry']=1;
					$errors_async[]=array('error_field_name'=>'entry','error_code'=>'required','block'=>'list_members_blog');
				}

				if (!is_array($errors))
				{
					$entry=process_blocked_words(strip_tags($entry),true);

					if (isset($block_config['need_approve']) && intval($_SESSION['is_trusted'])==0)
					{
						$approved=0;
					} else {
						$approved=1;
					}
					$item_id=sql_insert("insert into $config[tables_prefix]users_blogs set user_id=?, is_approved=?, user_from_id=?, entry=?, added_date=?",$user_id,$approved,intval($_SESSION['user_id']),$entry,date("Y-m-d H:i:s"));
					if ($user_id==$_SESSION['user_id'])
					{
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=12, user_id=?, user_target_id=?, entry_id=?, added_date=?",intval($_SESSION['user_id']),$user_id,$item_id,date("Y-m-d H:i:s"));
					} else {
						sql_pr("insert into $config[tables_prefix]users_events set event_type_id=13, user_id=?, user_target_id=?, entry_id=?, added_date=?",intval($_SESSION['user_id']),$user_id,$item_id,date("Y-m-d H:i:s"));
					}
					if ($_REQUEST['mode']=='async')
					{
						$entry_data=array('entry_id'=>$item_id,'approved'=>$approved==1?true:false);
						async_return_request_status(null,null,$entry_data);
					} else {
						header("Location: ?action=send_done");die;
					}
				} elseif ($_REQUEST['mode']=='async')
				{
					async_return_request_status($errors_async);
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_members_blog')));
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members_blog')));
		}
	}

	if (($_REQUEST['action']=='delete' || $_REQUEST['action']=='delete_entry') && is_array($_REQUEST['delete']))
	{
		if ($_SESSION['user_id']>0)
		{
			$delete_ids=implode(",",array_map("intval",$_REQUEST['delete']));
			$delete_ids=mr2array_list(sql_pr("select entry_id from $config[tables_prefix]users_blogs where entry_id in ($delete_ids) and (user_id=? or user_from_id=?)",intval($_SESSION['user_id']),intval($_SESSION['user_id'])));
			if (count($delete_ids)>0)
			{
				$delete_ids=implode(",",$delete_ids);
				sql_pr("delete from $config[tables_prefix]users_blogs where entry_id in ($delete_ids)");
				sql_pr("delete from $config[tables_prefix]users_events where entry_id in ($delete_ids)");
			}
			if ($_REQUEST['mode']=='async')
			{
				async_return_request_status();
			} else {
				header("Location: ?action=delete_done");die;
			}
		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members_blog')));
		}
	}

	if (!isset($block_config['mode_global']))
	{
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
		} else
		{
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
				if ($_REQUEST['mode']=='async')
				{
					header('HTTP/1.0 404 Not found');die;
				}
				return 'status_404';
			}
		}
	}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]users_blogs where is_approved=1 $where_user"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("SELECT $config[tables_prefix]users_blogs.*,
								$config[tables_prefix]users.display_name as user_from_name,
								$config[tables_prefix]users.avatar as user_from_avatar,
								$config[tables_prefix]users.gender_id as user_from_gender_id,
								$config[tables_prefix]users.birth_date as user_from_birth_date,
								$config[tables_prefix]users.country_id as user_from_country_id,
								$config[tables_prefix]users.city as user_from_city
							from $config[tables_prefix]users_blogs left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]users_blogs.user_from_id
							where is_approved=1 $where_user order by added_date desc LIMIT $from, $block_config[items_per_page]"));

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

		$data=mr2array(sql("SELECT
								$config[tables_prefix]users_blogs.*,
								$config[tables_prefix]users.display_name as user_from_name,
								$config[tables_prefix]users.avatar as user_from_avatar,
								$config[tables_prefix]users.gender_id as user_from_gender_id,
								$config[tables_prefix]users.birth_date as user_from_birth_date,
								$config[tables_prefix]users.country_id as user_from_country_id,
								$config[tables_prefix]users.city as user_from_city
							from $config[tables_prefix]users_blogs left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$config[tables_prefix]users_blogs.user_from_id
							where is_approved=1 $where_user order by added_date desc $limit"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		$data[$k]['time_passed_from_adding']=get_time_passed($data[$k]['added_date']);
		if ($data[$k]['user_from_birth_date']<>'0000-00-00')
		{
			$data[$k]['user_from_age']=get_time_passed($data[$k]['user_from_birth_date']);
		} else {
			$data[$k]['user_from_age'] = '';
		}
		if ($data[$k]['user_from_avatar']<>'')
		{
			$data[$k]['user_from_avatar_url']=$config['content_url_avatars']."/".$data[$k]['user_from_avatar'];
		}
	}

	$smarty->assign("data",$data);
	$smarty->assign("errors",$errors);

	if (isset($block_config['allow_editing']))
	{
		$smarty->assign("allow_editing",1);
	}

	return '';
}

function list_members_blogGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_user_id=intval($_REQUEST[$block_config['var_user_id']]);

	if (isset($block_config['mode_global']))
	{
		return "$from|$items_per_page";
	} elseif (!isset($block_config['var_user_id']))
	{
		return "nocache";
	} elseif ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}
	return "$var_user_id|$from|$items_per_page";
}

function list_members_blogCacheControl($block_config)
{
	if (isset($block_config['mode_global']))
	{
		return "default";
	}
	if (!isset($block_config['var_user_id']))
	{
		return "nocache";
	}
	return "user_nocache";
}

function list_members_blogAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='add_entry')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_members_blogShow($block_config,null);
	}

	if ($_REQUEST['action']=='delete_entry' && isset($_REQUEST['delete']))
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		list_members_blogShow($block_config,null);
	}

	if ($_REQUEST['action']=='edit_entry' && intval($_REQUEST['entry_id'])>0)
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		if (!isset($block_config['allow_editing']))
		{
			async_return_request_status(array(array('error_code'=>'editing_not_allowed','block'=>'list_members_blog')));
		}
		if (intval($_SESSION['user_id'])>0)
		{
			$entry_id=intval($_REQUEST['entry_id']);
			$entry_info=mr2array_single(sql_pr("select * from $config[tables_prefix]users_blogs where entry_id=?",$entry_id));
			if (intval($entry_info['entry_id'])>0)
			{
				if (intval($_SESSION['user_id'])==$entry_info['user_from_id'])
				{
					$entry=process_blocked_words(trim(strip_tags($_REQUEST['entry'])),true);
					sql_pr("update $config[tables_prefix]users_blogs set entry=? where entry_id=?",$entry,$entry_id);

					async_return_request_status();
				} else {
					async_return_request_status(array(array('error_code'=>'editing_not_allowed','block'=>'list_members_blog')));
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_members_blog')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'list_members_blog')));
		}
	}
}

function list_members_blogMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// display modes
		array("name"=>"mode_global",              "group"=>"display_modes", "type"=>"",       "is_required"=>0, "default_value"=>"1"),
		array("name"=>"var_user_id",              "group"=>"display_modes", "type"=>"STRING", "is_required"=>0, "default_value"=>"user_id"),
		array("name"=>"redirect_unknown_user_to", "group"=>"display_modes", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),

		// functionality
		array("name"=>"need_approve",  "group"=>"functionality", "type"=>"", "is_required"=>0),
		array("name"=>"allow_editing", "group"=>"functionality", "type"=>"", "is_required"=>0),
	);
}

function list_members_blogLegalRequestVariables()
{
	return array('action');
}

function list_members_blogJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>