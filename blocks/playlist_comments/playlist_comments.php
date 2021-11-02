<?php
function playlist_commentsShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$list_countries,$database_selectors;

	if (isset($block_config['var_playlist_id']) && intval($_REQUEST[$block_config['var_playlist_id']])>0)
	{
		$result=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where playlist_id=?",intval($_REQUEST[$block_config['var_playlist_id']]));
		if (mr2rows($result)==0) {return '';}
	} elseif (trim($_REQUEST[$block_config['var_playlist_dir']])<>'') {
		$result=sql_pr("SELECT $database_selectors[playlists] from $config[tables_prefix]playlists where dir=?",trim($_REQUEST[$block_config['var_playlist_dir']]));
		if (mr2rows($result)==0) {return '';}
	} else {
		return '';
	}

	$playlist_info=mr2array_single($result);
	$playlist_id=intval($playlist_info['playlist_id']);

	$where='';
	if (isset($block_config['match_locale']))
	{
		$language_code=sql_escape($config['locale']);
		$where.=" and $config[tables_prefix]comments.language_code='$language_code'";
	}

	$sort_by=$block_config['sort_by'];
	if (strpos($sort_by," asc")===false && strpos($sort_by," desc")===false) {$sort_by.=" desc";}

	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql_pr("select count(*) from $config[tables_prefix]comments where object_id=$playlist_id and object_type_id=13 and is_approved=1 and added_date<=? $where",date("Y-m-d H:i:s")));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.username, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=13 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date<=? $where order by $config[tables_prefix]comments.$sort_by limit $from,?",$playlist_id,date("Y-m-d H:i:s"),intval($block_config['items_per_page'])));

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

		$data=mr2array(sql_pr("select $config[tables_prefix]comments.*, $config[tables_prefix]users.status_id, $config[tables_prefix]users.avatar, $config[tables_prefix]users.username, $config[tables_prefix]users.display_name, $config[tables_prefix]users.gender_id, $config[tables_prefix]users.birth_date, $config[tables_prefix]users.country_id, $config[tables_prefix]users.city from $config[tables_prefix]comments left join $config[tables_prefix]users on $config[tables_prefix]comments.user_id=$config[tables_prefix]users.user_id where $config[tables_prefix]comments.object_id=? and $config[tables_prefix]comments.object_type_id=13 and $config[tables_prefix]comments.is_approved=1 and $config[tables_prefix]comments.added_date<=? $where order by $config[tables_prefix]comments.$sort_by $limit",$playlist_id,date("Y-m-d H:i:s")));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	foreach ($data as $k=>$v)
	{
		if ($data[$k]['avatar']<>'')
		{
			$data[$k]['avatar']=$config['content_url_avatars']."/".$data[$k]['avatar'];
			$data[$k]['avatar_url']=$data[$k]['avatar'];
		}
		if ($data[$k]['birth_date']<>'0000-00-00')
		{
			$data[$k]['age']=get_time_passed($data[$k]['birth_date']);
		} else {
			$data[$k]['age'] = '';
		}
		if ($data[$k]['status_id']==4)
		{
			if ($data[$k]['anonymous_username']!='')
			{
				$data[$k]['display_name']=$data[$k]['anonymous_username'];
			}
			if ($data[$k]['country_code']!='')
			{
				$data[$k]['country_id']=intval($list_countries['ids'][$data[$k]['country_code']]);
			}
		}
	}

	$smarty->assign("data",$data);
	$smarty->assign("playlist_info",$playlist_info);
	$smarty->assign("can_add_comment",1);

	if (isset($block_config['allow_anonymous']))
	{
		$smarty->assign("anonymous_user_id",mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1")));
	}

	if (intval($block_config['min_length'])>0)
	{
		$smarty->assign("min_length",intval($block_config['min_length']));
	}
	if (isset($block_config['allow_editing']))
	{
		$smarty->assign("allow_editing",1);
	}

	$use_captcha=1;
	if (!isset($block_config['use_captcha']))
	{
		$use_captcha=0;
	} elseif (intval($block_config['use_captcha'])==1 && intval($_SESSION['user_id'])>0)
	{
		$use_captcha=0;
	} elseif (intval($_SESSION['is_trusted'])>0)
	{
		$use_captcha=0;
	}
	if (intval(@$_SESSION['antispam_comments_captcha']) == 1)
	{
		$use_captcha = 1;
	}
	$smarty->assign("use_captcha",$use_captcha);

	return '';
}

function playlist_commentsGetHash($block_config)
{
	$dir=trim($_REQUEST[$block_config['var_playlist_dir']]);
	$id=intval($_REQUEST[$block_config['var_playlist_id']]);
	$from=intval($_REQUEST[$block_config['var_from']]);

	if ($_SESSION['user_id']>0)
	{
		return "runtime_nocache";
	}

	$current_version=0;
	if (function_exists('get_block_version'))
	{
		$current_version=get_block_version('comments_info','playlist',$id,$dir);
	}

	$use_captcha = 1;
	if (!isset($block_config['use_captcha']))
	{
		$use_captcha = 0;
	}
	if (intval(@$_SESSION['antispam_comments_captcha']) == 1)
	{
		$use_captcha = 1;
	}

	return "$dir|$id|$from|$use_captcha|$current_version";
}

function playlist_commentsCacheControl($block_config)
{
	return "user_nocache";
}

function playlist_commentsMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"0"),
		array("name"=>"links_per_page", "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",       "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),

		// sorting
		array("name"=>"sort_by", "group"=>"sorting", "type"=>"SORTING[comment_id,user_id,comment,rating,added_date]", "is_required"=>1, "default_value"=>"added_date"),

		// object context
		array("name"=>"var_playlist_dir", "group"=>"object_context", "type"=>"STRING", "is_required"=>1, "default_value"=>"dir"),
		array("name"=>"var_playlist_id",  "group"=>"object_context", "type"=>"STRING", "is_required"=>0, "default_value"=>"id"),

		// functionality
		array("name"=>"min_length",      "group"=>"functionality", "type"=>"INT",         "is_required"=>0),
		array("name"=>"need_approve",    "group"=>"functionality", "type"=>"CHOICE[1,2]", "is_required"=>0),
		array("name"=>"allow_anonymous", "group"=>"functionality", "type"=>"",            "is_required"=>0),
		array("name"=>"use_captcha",     "group"=>"functionality", "type"=>"CHOICE[1,2]", "is_required"=>0),
		array("name"=>"allow_editing",   "group"=>"functionality", "type"=>"",            "is_required"=>0),

		// i18n
		array("name"=>"match_locale", "group"=>"i18n", "type"=>"", "is_required"=>0),
	);
}

function playlist_commentsJavascript($block_config)
{
	global $config;

	return "KernelTeamVideoSharingPlaylistView.js?v={$config['project_version']}";
}

function playlist_commentsAsync($block_config)
{
	global $config,$database_selectors;

	if ($_REQUEST['action']=='add_comment' && intval($_REQUEST['playlist_id'])>0)
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");

		$playlist_id=intval($_REQUEST['playlist_id']);
		$result=sql("select * from $config[tables_prefix]playlists where playlist_id=$playlist_id");
		if (mr2rows($result)==0)
		{
			async_return_request_status(array(array('error_code'=>'invalid_playlist','block'=>'playlist_comments')));
		}
		$playlist_info=mr2array_single($result);

		$antispam_action = process_antispam_rules(15, $_REQUEST['comment']);
		if (strpos($antispam_action, 'error') !== false)
		{
			sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on comment from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['comment']), date("Y-m-d H:i:s"));
			async_return_request_status(array(array('error_code'=>'spam','block'=>'playlist_comments')));
		}
		if (strpos($antispam_action, 'delete') !== false)
		{
			sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted comment from IP $_SERVER[REMOTE_ADDR]", nvl($_REQUEST['comment']), date("Y-m-d H:i:s"));
			async_return_request_status(null,null,array('comment_id'=>0,'approved'=>false));
		}

		$comment=process_blocked_words(trim(strip_tags($_REQUEST['comment'])),true);
		$code=trim($_REQUEST['code']);
		$recaptcha_response=trim($_REQUEST['g-recaptcha-response']);

		$use_captcha=1;
		if (!isset($block_config['use_captcha']))
		{
			$use_captcha=0;
		} elseif (intval($block_config['use_captcha'])==1 && intval($_SESSION['user_id'])>0)
		{
			$use_captcha=0;
		}
		if (strpos($antispam_action, 'captcha') !== false)
		{
			$use_captcha = 1;
			$_SESSION['antispam_comments_captcha'] = 1;
		} else
		{
			$_SESSION['antispam_comments_captcha'] = 0;
		}
		if (intval($_SESSION['is_trusted'])>0)
		{
			$use_captcha=0;
		}

		$errors=null;
		if (strlen(trim($comment))==0)
		{
			$errors[]=array('error_field_name'=>'comment','error_code'=>'required','error_field_code'=>'comment_error_1','block'=>'playlist_comments');
		} elseif (intval($block_config['min_length'])>0 && strlen(trim($comment))<intval($block_config['min_length']))
		{
			$errors[]=array('error_field_name'=>'comment','error_code'=>'minimum','error_details'=>array(intval($block_config['min_length'])),'error_field_code'=>'comment_error_2','block'=>'playlist_comments');
		}
		if ($use_captcha==1)
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
					$errors[]=array('error_field_name'=>'code','error_code'=>'required','error_field_code'=>'code_error_5','block'=>'playlist_comments');
				} elseif (!validate_recaptcha($recaptcha_response, $recaptcha_data))
				{
					$errors[]=array('error_field_name'=>'code','error_code'=>'invalid','error_field_code'=>'code_error_6','block'=>'playlist_comments');
				}
			} else
			{
				if (strlen($code)==0)
				{
					$errors[]=array('error_field_name'=>'code','error_code'=>'required','error_field_code'=>'code_error_5','block'=>'playlist_comments');
				} elseif ($code<>$_SESSION['security_code_comments'] && $code<>$_SESSION['security_code'])
				{
					$errors[]=array('error_field_name'=>'code','error_code'=>'invalid','error_field_code'=>'code_error_6','block'=>'playlist_comments');
				}
			}
		}

		$user_id=0;
		$username='';
		$is_anonymous=0;
		$anonymous_username='';
		if (intval($_SESSION['user_id'])>0)
		{
			$user_id=intval($_SESSION['user_id']);
			$username=$_SESSION['username'];
		} else if (isset($block_config['allow_anonymous']))
		{
			$is_anonymous=1;
			$user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
			$anonymous_username=trim(strip_tags($_REQUEST['anonymous_username']));
			$username="Anonymous";
		}

		if (!is_array($errors))
		{
			if ($user_id>0)
			{
				$approved=1;
				$is_review_needed=1;
				if (isset($block_config['need_approve']))
				{
					$approved=0;
					if ($block_config['need_approve']=='1')
					{
						if (intval($_SESSION['user_id'])>0)
						{
							$approved=1;
						}
					}
				}
				if (strpos($antispam_action, 'deactivate') !== false)
				{
					$approved=0;
				}
				if (intval($_SESSION['is_trusted'])>0)
				{
					$approved=1;
					$is_review_needed=0;
				}
				$comment_date=date("Y-m-d H:i:s");
				if ($approved==0)
				{
					$item_id=sql_insert("insert into $config[tables_prefix]comments set object_id=$playlist_id, object_type_id=13, user_id=$user_id, anonymous_username=?, is_approved=0, is_review_needed=$is_review_needed, comment=?, comment_md5=md5(comment), ip=?, country_code=lower(?), language_code=?, added_date=?",$anonymous_username,$comment,ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($config['locale']),$comment_date);
				} else {
					$item_id=sql_insert("insert into $config[tables_prefix]comments set object_id=$playlist_id, object_type_id=13, user_id=$user_id, anonymous_username=?, is_approved=1, is_review_needed=$is_review_needed, comment=?, comment_md5=md5(comment), ip=?, country_code=lower(?), language_code=?, added_date=?",$anonymous_username,$comment,ip2int($_SERVER['REMOTE_ADDR']),nvl($_SERVER['GEOIP_COUNTRY_CODE']),nvl($config['locale']),$comment_date);

					$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
					$tokens_granted=0;
					if (strlen($comment)>=intval($memberzone_data['AWARDS_COMMENT_PLAYLIST_CONDITION']))
					{
						$tokens_granted=intval($memberzone_data['AWARDS_COMMENT_PLAYLIST']);
						if ($tokens_granted>0 && $is_anonymous==0)
						{
							sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=3, user_id=?, comment_id=?, tokens_granted=?, added_date=?",$user_id,$item_id,$tokens_granted,date("Y-m-d H:i:s"));
							$_SESSION['tokens_available']=intval($_SESSION['tokens_available'])+$tokens_granted;
						}
					}

					sql("update $config[tables_prefix]users set
							tokens_available=tokens_available+$tokens_granted,
							comments_playlists_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1 and object_type_id=13),
							comments_total_count=(select count(*) from $config[tables_prefix]comments where user_id=$config[tables_prefix]users.user_id and is_approved=1)
						where user_id=$user_id"
					);
					sql("update $config[tables_prefix]playlists set comments_count=(select count(*) from $config[tables_prefix]comments where object_id=$config[tables_prefix]playlists.playlist_id and object_type_id=13 and is_approved=1) where playlist_id=$playlist_id");

					if (function_exists('inc_block_version'))
					{
						inc_block_version('comments_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
						inc_block_version('playlists_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
					}
				}

				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=140, object_id=?, object_type_id=15, added_date=?", $user_id, $username, $item_id, date("Y-m-d H:i:s"));
				if ($is_anonymous==0)
				{
					sql_pr("insert into $config[tables_prefix]users_events set event_type_id=20, user_id=?, playlist_id=?, comment_id=?, added_date=?",$user_id,$playlist_id,$item_id,$comment_date);
				}

				unset($_SESSION['security_code']);
				unset($_SESSION['security_code_comments']);

				$comment_data=array('comment_id'=>$item_id,'approved'=>$approved==1?true:false);
				async_return_request_status(null,null,$comment_data);
			} else {
				async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_comments')));
			}
		} else {
			async_return_request_status($errors);
		}
	} elseif ($_REQUEST['action']=='vote_comment' && intval($_REQUEST['comment_id'])>0)
	{
		if (intval($_REQUEST['vote'])!=0)
		{
			require_once("$config[project_path]/admin/include/functions_base.php");
			require_once("$config[project_path]/admin/include/functions.php");
			require_once("$config[project_path]/admin/include/database_selectors.php");

			$comment_id=intval($_REQUEST['comment_id']);
			$comment_info=mr2array_single(sql("select * from $config[tables_prefix]comments where comment_id=$comment_id"));
			if (intval($comment_info['comment_id'])>0)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]comments_vote_history where comment_id=? and ip=?",$comment_id,ip2int($_SERVER['REMOTE_ADDR'])))>0)
				{
					async_return_request_status(array(array('error_code'=>'ip_already_voted','error_field_code'=>'error_1','block'=>'playlist_comments')));
				} else {
					sql_pr("insert into $config[tables_prefix]comments_vote_history set comment_id=?, ip=?, added_date=?",$comment_id,ip2int($_SERVER['REMOTE_ADDR']),date("Y-m-d H:i:s"));
					if (intval($_REQUEST['vote'])>0)
					{
						sql_pr("update $config[tables_prefix]comments set likes=likes+1, rating=cast(likes as signed)-cast(dislikes as signed) where comment_id=?",$comment_id);
					} else {
						sql_pr("update $config[tables_prefix]comments set dislikes=dislikes+1, rating=cast(likes as signed)-cast(dislikes as signed) where comment_id=?",$comment_id);
					}

					if (intval($comment_info['object_type_id'])==13)
					{
						$playlist_id=intval($comment_info['object_id']);
						$playlist_info=mr2array_single(sql("select playlist_id, dir from $config[tables_prefix]playlists where playlist_id=$playlist_id"));
						if ($playlist_info['playlist_id']>0 && function_exists('inc_block_version'))
						{
							inc_block_version('comments_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
							inc_block_version('playlists_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
						}
					}
					async_return_request_status();
				}
			}
		}
		async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_comments')));
	} elseif ($_REQUEST['action']=='edit_comment' && intval($_REQUEST['comment_id'])>0)
	{
		require_once("$config[project_path]/admin/include/functions_base.php");
		require_once("$config[project_path]/admin/include/functions.php");
		require_once("$config[project_path]/admin/include/database_selectors.php");

		if (!isset($block_config['allow_editing']))
		{
			async_return_request_status(array(array('error_code'=>'editing_not_allowed','block'=>'playlist_comments')));
		}
		if (intval($_SESSION['user_id'])>0)
		{
			$comment_id=intval($_REQUEST['comment_id']);
			$comment_info=mr2array_single(sql("select * from $config[tables_prefix]comments where comment_id=$comment_id"));
			if (intval($comment_info['comment_id'])>0)
			{
				if (intval($_SESSION['user_id'])==mr2number(sql_pr("select user_id from $config[tables_prefix]comments where comment_id=?",$comment_id)))
				{
					$is_review_needed=1;
					if (intval($_SESSION['is_trusted'])>0)
					{
						$is_review_needed=0;
					}
					$comment=process_blocked_words(trim(strip_tags($_REQUEST['comment'])),true);
					sql_pr("update $config[tables_prefix]comments set comment=?, is_review_needed=? where comment_id=?",$comment,$is_review_needed,$comment_id);

					if (intval($comment_info['object_type_id'])==13)
					{
						$playlist_id=intval($comment_info['object_id']);
						$playlist_info=mr2array_single(sql("select playlist_id, dir from $config[tables_prefix]playlists where playlist_id=$playlist_id"));
						if ($playlist_info['playlist_id']>0 && function_exists('inc_block_version'))
						{
							inc_block_version('comments_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
							inc_block_version('playlists_info','playlist',$playlist_info['playlist_id'],$playlist_info['dir']);
						}
					}
					async_return_request_status();
				} else {
					async_return_request_status(array(array('error_code'=>'editing_not_allowed','block'=>'playlist_comments')));
				}
			}
			async_return_request_status(array(array('error_code'=>'invalid_params','block'=>'playlist_comments')));
		} else {
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'playlist_comments')));
		}
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
