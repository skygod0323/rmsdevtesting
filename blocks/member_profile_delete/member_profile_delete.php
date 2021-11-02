<?php
function member_profile_deleteShow($block_config,$object_id)
{
	global $config,$smarty;

	if ($_SESSION['user_id']<1)
	{
		if ($_POST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'member_profile_delete')));
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

	if ($_POST['action']=='delete_profile')
	{
		$errors = null;
		$errors_async = null;

		if (intval($_POST['confirm_delete'])==0)
		{
			$errors['confirm_delete']=1;
			$errors_async[]=array('error_field_name'=>'confirm_delete','error_code'=>'required','block'=>'member_profile_delete');
		}

		if (isset($block_config['require_reason']) && strlen(trim($_POST['reason']))==0)
		{
			$errors['reason']=1;
			$errors_async[]=array('error_field_name'=>'reason','error_code'=>'required','block'=>'member_profile_delete');
		}

		if (!is_array($errors))
		{
			sql_pr("update $config[tables_prefix]users set is_removal_requested=1, removal_reason=? where user_id=?",trim($_POST['reason']),$_SESSION['user_id']);

			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=delete_profile_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}

		$smarty->assign('errors',$errors);
	}

	if (isset($block_config['require_reason']))
	{
		$smarty->assign('require_reason',1);
	}
	return '';
}

function member_profile_deleteGetHash($block_config)
{
	return "nocache";
}

function member_profile_deleteCacheControl($block_config)
{
	return "nocache";
}

function member_profile_deleteMetaData()
{
	return array(
		// validation
		array("name"=>"require_reason", "group"=>"validation", "type"=>"", "is_required"=>0),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
