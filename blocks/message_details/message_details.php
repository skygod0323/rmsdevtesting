<?php
function message_detailsShow($block_config,$object_id)
{
	global $config,$smarty,$storage;

	$user_id=intval($_SESSION['user_id']);

	if ($user_id<1)
	{
		if ($_REQUEST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in')));
		} elseif ($_GET['mode']=='async')
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

	$errors=null;
	$errors_async=null;

	if ($_REQUEST['action']=='send' || $_REQUEST['action']=='send_message')
	{
		$message=trim($_REQUEST['message']);
		$reply_to_user_id=intval($_REQUEST['reply_to_user_id']);

		$tokens_required=0;
		$memberzone_data=@unserialize(file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
		if ($memberzone_data['ENABLE_TOKENS_INTERNAL_MESSAGES']==1)
		{
			$tokens_required=intval($memberzone_data['TOKENS_INTERNAL_MESSAGES']);
		}

		if ($message=='')
		{
			$errors['message']=1;
			$errors_async[]=array('error_field_name'=>'message','error_code'=>'required','block'=>'message_details');
		} elseif ($tokens_required>0 && $tokens_required>mr2number(sql_pr("select tokens_available from $config[tables_prefix]users where user_id=?",$user_id)))
		{
			$errors['message']=2;
			$errors_async[]=array('error_field_name'=>'message','error_code'=>'not_enough_tokens','block'=>'message_details');
		}

		if (!is_array($errors))
		{
			if ($reply_to_user_id>0 && $user_id<>$reply_to_user_id)
			{
				$antispam_action = process_antispam_rules(21, $_REQUEST['message']);
				if (strpos($antispam_action, 'error') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on internal message from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['message']), date("Y-m-d H:i:s"));
					async_return_request_status(array(array('error_code'=>'spam','block'=>'list_messages')));
				}

				$message=strip_tags($message);
				$message_id=sql_insert("insert into $config[tables_prefix]messages set user_id=?, user_from_id=?, message=?, message_md5=md5(message), ip=?, added_date=?",$reply_to_user_id,$user_id,$message,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
				sql_pr("delete from $config[tables_prefix]users_ignores where user_id=? and ignored_user_id=?",$user_id,$reply_to_user_id);

				if ($tokens_required>0)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=GREATEST(tokens_available-?, 0) where user_id=?",$tokens_required,$user_id);
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
					async_return_request_status(array(),$_SESSION['message_details_referer'],$message_data);
				} else {
					header("Location: $_SESSION[message_details_referer]");die;
				}
			} elseif ($_REQUEST['mode']=='async')
			{
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'list_messages')));
			}

		} elseif ($_REQUEST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	$message_id=intval($_REQUEST[$block_config['var_message_id']]);

	$data=mr2array_single(sql_pr("select $config[tables_prefix]messages.*, $config[tables_prefix]users.display_name as user_from_name, $config[tables_prefix]users.avatar as user_from_avatar
							from $config[tables_prefix]messages left join $config[tables_prefix]users on $config[tables_prefix]messages.user_from_id=$config[tables_prefix]users.user_id
							where (($config[tables_prefix]messages.user_id=? and $config[tables_prefix]messages.is_hidden_from_user_id=0) or ($config[tables_prefix]messages.user_from_id=? and $config[tables_prefix]messages.is_hidden_from_user_from_id=0)) and message_id=?", $_SESSION['user_id'], $_SESSION['user_id'], $message_id
	));
	if (count($data)>0)
	{
		$data['time_passed_from_adding']=get_time_passed($data['added_date']);
		if ($data['user_from_avatar'])
		{
			$data['user_from_avatar_url']=$config['content_url_avatars']."/".$data['user_from_avatar'];
		}
		$storage[$object_id]['message_user_id']=$data['user_from_id'];
		$storage[$object_id]['message_display_name']=$data['user_from_name'];
		$storage[$object_id]['message_avatar']=$data['user_from_avatar'];
		$storage[$object_id]['message_avatar_url']=$data['user_from_avatar_url'];
	} else
	{
		return 'status_404';
	}

	$_SESSION['message_details_referer']=$_SERVER['HTTP_REFERER'];

	$smarty->assign("errors",$errors);
	$smarty->assign("data",$data);
	return '';
}

function message_detailsGetHash($block_config)
{
	return "nocache";
}

function message_detailsCacheControl($block_config)
{
	return "nocache";
}

function message_detailsMetaData()
{
	return array(
		// object context
		array("name"=>"var_message_id", "group"=>"context_object", "type"=>"STRING", "is_required"=>1, "default_value"=>"message_id"),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login")
	);
}

function message_detailsAsync($block_config)
{
	global $config;

	if ($_REQUEST['action']=='send_message')
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		message_detailsShow($block_config,null);
	}
}

function message_detailsJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingMembers.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
