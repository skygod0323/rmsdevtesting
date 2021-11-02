<?php
function feedbackShow($block_config,$object_id)
{
	global $config,$smarty;

	$errors=null;
	$errors_async=null;

	if ($_POST['action']=='send')
	{
		$email=trim($_POST['email']);
		$subject=trim($_POST['subject']);
		$message=trim($_POST['message']);
		$code=trim($_POST['code']);
		$recaptcha_response=trim($_POST['g-recaptcha-response']);
		$custom1=trim($_POST['custom1']);
		$custom2=trim($_POST['custom2']);
		$custom3=trim($_POST['custom3']);
		$custom4=trim($_POST['custom4']);
		$custom5=trim($_POST['custom5']);

		if (isset($block_config['require_subject']))
		{
			if (strlen($subject)==0)
			{
				$errors['subject']=1;
				$errors_async[]=array('error_field_name'=>'subject','error_code'=>'required','block'=>'feedback');
			}
		}

		if (strlen($message)==0)
		{
			$errors['message']=1;
			$errors_async[]=array('error_field_name'=>'message','error_code'=>'required','block'=>'feedback');
		}

		if (isset($block_config['use_custom1']))
		{
			if (strlen($custom1)==0)
			{
				$errors['custom1']=1;
				$errors_async[]=array('error_field_name'=>'custom1','error_code'=>'required','block'=>'feedback');
			}
		}
		if (isset($block_config['use_custom2']))
		{
			if (strlen($custom2)==0)
			{
				$errors['custom2']=1;
				$errors_async[]=array('error_field_name'=>'custom2','error_code'=>'required','block'=>'feedback');
			}
		}
		if (isset($block_config['use_custom3']))
		{
			if (strlen($custom3)==0)
			{
				$errors['custom3']=1;
				$errors_async[]=array('error_field_name'=>'custom3','error_code'=>'required','block'=>'feedback');
			}
		}
		if (isset($block_config['use_custom4']))
		{
			if (strlen($custom4)==0)
			{
				$errors['custom4']=1;
				$errors_async[]=array('error_field_name'=>'custom4','error_code'=>'required','block'=>'feedback');
			}
		}
		if (isset($block_config['use_custom5']))
		{
			if (strlen($custom5)==0)
			{
				$errors['custom5']=1;
				$errors_async[]=array('error_field_name'=>'custom5','error_code'=>'required','block'=>'feedback');
			}
		}
		if (isset($block_config['require_email']))
		{
			if (strlen($email)==0)
			{
				$errors['email']=1;
				$errors_async[]=array('error_field_name'=>'email','error_code'=>'required','block'=>'feedback');
			}
		}

		if (isset($block_config['use_captcha']))
		{
			$recaptcha_data=null;
			if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
			{
				$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
			}
			if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
			{
				if (strlen($recaptcha_response)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'feedback');
				} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'feedback');
				}
			} else
			{
				if (strlen($code)==0)
				{
					$errors['code']=1;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'required','block'=>'feedback');
				} elseif ($code<>$_SESSION['security_code_feedback'] && $code<>$_SESSION['security_code'])
				{
					$errors['code']=2;
					$errors_async[]=array('error_field_name'=>'code','error_code'=>'invalid','block'=>'feedback');
				}
			}
		}

		if (!is_array($errors))
		{
			$http_referer=$_POST['referer'];
			if ($http_referer=='')
			{
				$http_referer=$_SERVER['HTTP_REFERER'];
			}
			sql_pr("insert into $config[tables_prefix]feedbacks set status_id=1, email=?, subject=?, message=?, ip=?, country_code=lower(?), user_agent=?, referer=?, user_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=?, added_date=?",$email,$subject,$message,ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($_SERVER['HTTP_USER_AGENT']),nvl($http_referer),intval($_SESSION['user_id']),nvl($custom1),nvl($custom2),nvl($custom3),nvl($custom4),nvl($custom5),date("Y-m-d H:i:s"));

			unset($_SESSION['security_code']);
			unset($_SESSION['security_code_feedback']);
			if ($_POST['mode']=='async')
			{
				$smarty->assign('async_submit_successful','true');
				return '';
			} else {
				header("Location: ?action=send_done");die;
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}

		$smarty->assign('errors',$errors);
	}
	if (isset($block_config['use_captcha']))
	{
		$smarty->assign('use_captcha','1');
	}
	if (isset($block_config['use_custom1']))
	{
		$smarty->assign('use_custom1','1');
	}
	if (isset($block_config['use_custom2']))
	{
		$smarty->assign('use_custom2','1');
	}
	if (isset($block_config['use_custom3']))
	{
		$smarty->assign('use_custom3','1');
	}
	if (isset($block_config['use_custom4']))
	{
		$smarty->assign('use_custom4','1');
	}
	if (isset($block_config['use_custom5']))
	{
		$smarty->assign('use_custom5','1');
	}
	if (isset($block_config['require_subject']))
	{
		$smarty->assign('require_subject','1');
	}
	if (isset($block_config['require_email']))
	{
		$smarty->assign('require_email','1');
	}
	return '';
}

function feedbackGetHash($block_config)
{
	return "nocache";
}

function feedbackCacheControl($block_config)
{
	return "nocache";
}

function feedbackMetaData()
{
	return array(
		array("name"=>"require_subject", "type"=>"", "is_required"=>0),
		array("name"=>"require_email",   "type"=>"", "is_required"=>0),
		array("name"=>"use_custom1",     "type"=>"", "is_required"=>0),
		array("name"=>"use_custom2",     "type"=>"", "is_required"=>0),
		array("name"=>"use_custom3",     "type"=>"", "is_required"=>0),
		array("name"=>"use_custom4",     "type"=>"", "is_required"=>0),
		array("name"=>"use_custom5",     "type"=>"", "is_required"=>0),
		array("name"=>"use_captcha",     "type"=>"", "is_required"=>0),
	);
}

function feedbackJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingForms.js?v={$config['project_version']}";
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
