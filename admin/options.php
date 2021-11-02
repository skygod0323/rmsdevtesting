<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$table_name="$config[tables_prefix_multi]admin_users";
$table_key_name="user_id";

if ($_POST['action']=='change_personal_setting_complete')
{
	$errors = null;
	$_POST['action']='change_complete';
	$_REQUEST['item_id']=$_SESSION['userdata']['user_id'];

	$_POST['login']=trim($_POST['login']);
	$_POST['short_date_format']=trim($_POST['short_date_format']);
	$_POST['full_date_format']=trim($_POST['full_date_format']);

	if ($_SESSION['userdata']['is_superadmin']==1)
	{
		validate_field('uniq',$_POST['login'],$lang['settings']['personal_field_username'],array('field_name_in_base'=>'login'));
	}
	validate_field('empty',$_POST['short_date_format'],$lang['settings']['personal_field_short_date_format']);
	validate_field('empty',$_POST['full_date_format'],$lang['settings']['personal_field_full_date_format']);
	if ($_POST['content_scheduler_days']!='0')
	{
		validate_field('empty_int',$_POST['content_scheduler_days'],$lang['settings']['personal_field_content_scheduler_days']);
	}
	if (validate_field('empty',$_POST['maximum_thumb_size'],$lang['settings']['personal_field_maximum_thumb_size']))
	{
		validate_field('size',$_POST['maximum_thumb_size'],$lang['settings']['personal_field_maximum_thumb_size']);
	}

	if ($_POST['pass']<>'' || $_POST['pass_confirm']<>'')
	{
		if (validate_field('empty',$_POST['pass'],$lang['settings']['personal_field_password']) && validate_field('empty',$_POST['pass_confirm'],$lang['settings']['personal_field_password_confirm']))
		{
			if ($_POST['pass']<>$_POST['pass_confirm'])
			{
				$errors[]=get_aa_error('password_confirmation', $lang['settings']['personal_field_password_confirm']);
			}
		}
	}
	if ($config['installation_type']==4)
	{
		if ($_POST['images_on_album_edit']<>'no' && $_POST['images_on_album_edit']<>'')
		{
			validate_field('empty_int',$_POST['images_on_album_edit_count'],$lang['settings']['personal_field_images_on_album_edit']);
		}
	}

	if (!is_array($errors))
	{
		sql_pr("update $table_name set lang=?, skin=?, short_date_format=?, full_date_format=?, content_scheduler_days=?, content_scheduler_days_option=?, is_popups_enabled=?, is_wysiwyg_enabled_videos=?, is_wysiwyg_enabled_albums=?, is_wysiwyg_enabled_posts=?, is_wysiwyg_enabled_other=?, is_ip_protection_disabled=?, is_expert_mode=?, is_hide_forum_hints=? where user_id=?",
		$_POST['lang'],$_POST['skin'],$_POST['short_date_format'],$_POST['full_date_format'],intval($_POST['content_scheduler_days']),intval($_POST['content_scheduler_days_option']),intval($_POST['is_popups_enabled']),intval($_POST['is_wysiwyg_enabled_videos']),intval($_POST['is_wysiwyg_enabled_albums']),intval($_POST['is_wysiwyg_enabled_posts']),intval($_POST['is_wysiwyg_enabled_other']),intval($_POST['is_ip_protection_disabled']),intval($_POST['is_expert_mode']),intval($_POST['is_hide_forum_hints']),$_SESSION['userdata']['user_id']);

		if ($_POST['pass']<>'')
		{
			sql_pr("update $table_name set pass=? where user_id=?",generate_password_hash(md5($_POST['pass'])),$_SESSION['userdata']['user_id']);
		}

		$_SESSION['save']['options']['default_save_button']=intval($_POST['default_save_button']);
		$_SESSION['save']['options']['maximum_thumb_size']=$_POST['maximum_thumb_size'];
		$_SESSION['save']['options']['video_edit_display_mode']=$_POST['video_edit_display_mode'];
		$_SESSION['save']['options']['video_edit_show_translations']=intval($_POST['video_edit_show_translations']);
		$_SESSION['save']['options']['video_edit_show_player']=intval($_POST['video_edit_show_player']);
		$_SESSION['save']['options']['screenshots_on_video_edit']=intval($_POST['screenshots_on_video_edit']);

		$_SESSION['save']['options']['album_edit_display_mode']=$_POST['album_edit_display_mode'];
		$_SESSION['save']['options']['album_edit_show_translations']=intval($_POST['album_edit_show_translations']);
		$_SESSION['save']['options']['images_on_album_edit']=$_POST['images_on_album_edit'];
		$_SESSION['save']['options']['images_on_album_edit_count']=intval($_POST['images_on_album_edit_count']);

		$_SESSION['userdata']['skin']=$_POST['skin'];
		$_SESSION['userdata']['lang']=$_POST['lang'];
		$_SESSION['userdata']['short_date_format']=$_POST['short_date_format'];
		$_SESSION['userdata']['full_date_format']=$_POST['full_date_format'];
		$_SESSION['userdata']['content_scheduler_days']=intval($_POST['content_scheduler_days']);
		$_SESSION['userdata']['content_scheduler_days_option']=intval($_POST['content_scheduler_days_option']);
		$_SESSION['userdata']['is_popups_enabled']=intval($_POST['is_popups_enabled']);
		$_SESSION['userdata']['is_wysiwyg_enabled_videos']=intval($_POST['is_wysiwyg_enabled_videos']);
		$_SESSION['userdata']['is_wysiwyg_enabled_albums']=intval($_POST['is_wysiwyg_enabled_albums']);
		$_SESSION['userdata']['is_wysiwyg_enabled_posts']=intval($_POST['is_wysiwyg_enabled_posts']);
		$_SESSION['userdata']['is_wysiwyg_enabled_other']=intval($_POST['is_wysiwyg_enabled_other']);
		$_SESSION['userdata']['is_ip_protection_disabled']=intval($_POST['is_ip_protection_disabled']);
		$_SESSION['userdata']['is_expert_mode']=intval($_POST['is_expert_mode']);
		$_SESSION['userdata']['is_hide_forum_hints']=intval($_POST['is_hide_forum_hints']);
		if ($_SESSION['userdata']['is_superadmin']==1)
		{
			$_SESSION['userdata']['login']=$_POST['login'];
			sql_pr("update $table_name set login=? where user_id=?",$_POST['login'],$_SESSION['userdata']['user_id']);
		}
		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if ($_POST['action']=='change_website_settings_complete')
{
	$errors = null;

	if (validate_field('empty',$_POST['WEBSITE_LINK_PATTERN'],$lang['settings']['website_field_video_website_link_pattern']))
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_video_website_link_pattern'],'%DIR%');
		}
	}

	if ($config['installation_type']==4)
	{
		if (validate_field('empty',$_POST['WEBSITE_LINK_PATTERN_ALBUM'],$lang['settings']['website_field_album_website_link_pattern']))
		{
			if (strpos($_POST['WEBSITE_LINK_PATTERN_ALBUM'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_ALBUM'],'%ID%')===false)
			{
				$errors[]=get_aa_error('token_required',$lang['settings']['website_field_album_website_link_pattern'],'%DIR%');
			}
		}
		if (validate_field('empty',$_POST['WEBSITE_LINK_PATTERN_IMAGE'],$lang['settings']['website_field_album_image_website_link_pattern']))
		{
			if (strpos($_POST['WEBSITE_LINK_PATTERN_IMAGE'],'%IMG%')===false)
			{
				$errors[]=get_aa_error('token_required',$lang['settings']['website_field_album_image_website_link_pattern'],'%IMG%');
			} elseif (strpos($_POST['WEBSITE_LINK_PATTERN_IMAGE'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_IMAGE'],'%ID%')===false)
			{
				$errors[]=get_aa_error('token_required',$lang['settings']['website_field_album_image_website_link_pattern'],'%DIR%');
			}
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_PLAYLIST']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_PLAYLIST'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_PLAYLIST'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_playlist_website_link_pattern'],'%DIR%');
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_MODEL']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_MODEL'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_MODEL'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_model_website_link_pattern'],'%DIR%');
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_CS']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_CS'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_CS'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_content_source_website_link_pattern'],'%DIR%');
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_DVD']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_DVD'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_DVD'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_dvd_website_link_pattern'],'%DIR%');
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_DVD_GROUP']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_DVD_GROUP'],'%DIR%')===false && strpos($_POST['WEBSITE_LINK_PATTERN_DVD_GROUP'],'%ID%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_dvd_group_website_link_pattern'],'%DIR%');
		}
	}
	if ($_POST['WEBSITE_LINK_PATTERN_SEARCH']<>'')
	{
		if (strpos($_POST['WEBSITE_LINK_PATTERN_SEARCH'],'%QUERY%')===false)
		{
			$errors[]=get_aa_error('token_required',$lang['settings']['website_field_search_website_link_pattern'],'%QUERY%');
		}
	}

	if ($config['installation_type']>=2 && $_POST['ENABLE_USER_ONLINE_STATUS_REFRESH']=='1')
	{
		validate_field('empty_int',$_POST['USER_ONLINE_STATUS_REFRESH_INTERVAL'],$lang['settings']['website_field_user_online_status_refresh']);
	}
	if ($config['installation_type']==4 && $_POST['ENABLE_USER_MESSAGES_REFRESH']=='1')
	{
		validate_field('empty_int',$_POST['USER_MESSAGES_REFRESH_INTERVAL'],$lang['settings']['website_field_user_new_messages_refresh']);
	}

	$regex_replacements = explode("\n", $_POST['REGEX_REPLACEMENTS']);
	foreach ($regex_replacements as $regex_replacement)
	{
		$regex_replacement = trim($regex_replacement);
		if ($regex_replacement)
		{
			$regex_replacement_last_separator = strrpos($regex_replacement, ':');
			if ($regex_replacement_last_separator)
			{
				$regex_replacement = trim(substr($regex_replacement, 0, $regex_replacement_last_separator));
				if (@preg_match($regex_replacement, "") === false)
				{
					$errors[]=get_aa_error('invalid_regex_item', $lang['settings']['website_field_regexp_replacements'], $regex_replacement);
				}
			} else
			{
				$errors[]=get_aa_error('invalid_regex_item', $lang['settings']['website_field_regexp_replacements'], $regex_replacement);
			}
		}
	}

	if (!is_writable("$config[project_path]/admin/data/system"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system"));
	}

	if (is_file("$config[project_path]/admin/data/system/runtime_params.dat") && !is_writable("$config[project_path]/admin/data/system/runtime_params.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/runtime_params.dat"));
	}

	if (is_file("$config[project_path]/admin/data/system/blocked_words.dat") && !is_writable("$config[project_path]/admin/data/system/blocked_words.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/blocked_words.dat"));
	}

	if (is_file("$config[project_path]/admin/data/system/website_ui_params.dat") && !is_writable("$config[project_path]/admin/data/system/website_ui_params.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/website_ui_params.dat"));
	}

	if (!is_array($errors))
	{
		$update_details='';
		$old_options=array();
		$old_options['DYNAMIC_PARAMS']=@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat");

		$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
		foreach ($website_ui_data as $k=>$v)
		{
			$old_options[$k]=$v;
		}

		$blocked_words=unserialize(@file_get_contents("$config[project_path]/admin/data/system/blocked_words.dat"));
		foreach ($blocked_words as $k=>$v)
		{
			$old_options[$k]=$v;
		}

		$dynamic_params=array();
		for ($i=0;$i<5;$i++)
		{
			if (strlen($_POST['DYNAMIC_PARAMS'][$i])>0)
			{
				$param=array();
				$param['name']=$_POST['DYNAMIC_PARAMS'][$i];
				$param['default_value']=$_POST['DYNAMIC_PARAMS_VALUES'][$i];
				$param['lifetime']=intval($_POST['DYNAMIC_PARAMS_LIFETIMES'][$i]);
				$dynamic_params[]=$param;
			}
		}
		file_put_contents("$config[project_path]/admin/data/system/runtime_params.dat",serialize($dynamic_params),LOCK_EX);

		if (serialize($dynamic_params) != $old_options['DYNAMIC_PARAMS'])
		{
			$update_details .= "DYNAMIC_PARAMS, ";
		}

		$params=array();
		$params['BLOCKED_WORDS']=$_POST['BLOCKED_WORDS'];
		$params['BLOCKED_WORDS_REPLACEMENT']=$_POST['BLOCKED_WORDS_REPLACEMENT'];
		$params['REGEX_REPLACEMENTS']=$_POST['REGEX_REPLACEMENTS'];
		file_put_contents("$config[project_path]/admin/data/system/blocked_words.dat",serialize($params),LOCK_EX);

		foreach ($params as $var=>$val)
		{
			if ($old_options[$var] != $val)
			{
				$update_details .= "$var, ";
			}
		}

		$params=array();
		$params['ENABLE_USER_ONLINE_STATUS_REFRESH']=$_POST['ENABLE_USER_ONLINE_STATUS_REFRESH'];
		$params['ENABLE_USER_MESSAGES_REFRESH']=$_POST['ENABLE_USER_MESSAGES_REFRESH'];
		$params['USER_ONLINE_STATUS_REFRESH_INTERVAL']=$_POST['USER_ONLINE_STATUS_REFRESH_INTERVAL'];
		$params['USER_MESSAGES_REFRESH_INTERVAL']=$_POST['USER_MESSAGES_REFRESH_INTERVAL'];
		$params['WEBSITE_LINK_PATTERN']=$_POST['WEBSITE_LINK_PATTERN'];
		$params['WEBSITE_LINK_PATTERN_ALBUM']=$_POST['WEBSITE_LINK_PATTERN_ALBUM'];
		$params['WEBSITE_LINK_PATTERN_IMAGE']=$_POST['WEBSITE_LINK_PATTERN_IMAGE'];
		$params['WEBSITE_LINK_PATTERN_PLAYLIST']=$_POST['WEBSITE_LINK_PATTERN_PLAYLIST'];
		$params['WEBSITE_LINK_PATTERN_MODEL']=trim($_POST['WEBSITE_LINK_PATTERN_MODEL']);
		$params['WEBSITE_LINK_PATTERN_CS']=trim($_POST['WEBSITE_LINK_PATTERN_CS']);
		$params['WEBSITE_LINK_PATTERN_DVD']=trim($_POST['WEBSITE_LINK_PATTERN_DVD']);
		$params['WEBSITE_LINK_PATTERN_DVD_GROUP']=trim($_POST['WEBSITE_LINK_PATTERN_DVD_GROUP']);
		$params['WEBSITE_LINK_PATTERN_SEARCH']=trim($_POST['WEBSITE_LINK_PATTERN_SEARCH']);
		$params['DISABLE_WEBSITE']=intval($_POST['DISABLE_WEBSITE']);
		$params['WEBSITE_CACHING']=intval($_POST['WEBSITE_CACHING']);
		$params['DISABLED_CONTENT_AVAILABILITY']=intval($_POST['DISABLED_CONTENT_AVAILABILITY']);
		$params['PSEUDO_VIDEO_BEHAVIOR']=intval($_POST['PSEUDO_VIDEO_BEHAVIOR']);
		file_put_contents("$config[project_path]/admin/data/system/website_ui_params.dat",serialize($params),LOCK_EX);

		foreach ($params as $var=>$val)
		{
			if ($old_options[$var] != $val)
			{
				$update_details .= "$var, ";
			}
		}

		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=221, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		return_ajax_success("$page_name?page=website_settings");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_POST['action']=='change_memberzone_settings_complete')
{
	$errors = null;

	$awards=array(
		'AWARDS_SIGNUP'=>$lang['settings']['memberzone_awards_col_action_signup'],
		'AWARDS_AVATAR'=>$lang['settings']['memberzone_awards_col_action_avatar'],
		'AWARDS_COVER'=>$lang['settings']['memberzone_awards_col_action_cover'],
		'AWARDS_LOGIN'=>$lang['settings']['memberzone_awards_col_action_login'],
		'AWARDS_COMMENT_VIDEO'=>$lang['settings']['memberzone_awards_col_action_comment_video'],
		'AWARDS_COMMENT_ALBUM'=>$lang['settings']['memberzone_awards_col_action_comment_album'],
		'AWARDS_COMMENT_CS'=>$lang['settings']['memberzone_awards_col_action_comment_content_source'],
		'AWARDS_COMMENT_MODEL'=>$lang['settings']['memberzone_awards_col_action_comment_model'],
		'AWARDS_COMMENT_DVD'=>$lang['settings']['memberzone_awards_col_action_comment_dvd'],
		'AWARDS_COMMENT_POST'=>$lang['settings']['memberzone_awards_col_action_comment_post'],
		'AWARDS_COMMENT_PLAYLIST'=>$lang['settings']['memberzone_awards_col_action_comment_playlist'],
		'AWARDS_VIDEO_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_video_upload'],
		'AWARDS_ALBUM_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_album_upload'],
		'AWARDS_POST_UPLOAD'=>$lang['settings']['memberzone_awards_col_action_post_upload'],
		'AWARDS_REFERRAL_SIGNUP'=>$lang['settings']['memberzone_awards_col_action_referral_signup'],
		'AWARDS_EARNING_UNIQUE_VIEWS'=>$lang['settings']['memberzone_awards_earning_unique_views'],
	);

	if ($_POST['AFFILIATE_PARAM_NAME']<>'')
	{
		$dynamic_params=unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
		$has_dynamic_param=false;
		foreach ($dynamic_params as $dynamic_param)
		{
			if ($_POST['AFFILIATE_PARAM_NAME']==$dynamic_param['name'])
			{
				$has_dynamic_param=true;
				break;
			}
		}
		if (!$has_dynamic_param)
		{
			$errors[]=get_aa_error('website_settings_runtime_parameter',$lang['settings']['memberzone_field_affiliate_param_name'],$_POST['AFFILIATE_PARAM_NAME']);
		}
	}

	if ($_POST['ENABLE_TOKENS_STANDARD_VIDEO']=='1' && $_POST['DEFAULT_TOKENS_STANDARD_VIDEO']<>'0')
	{
		validate_field('empty_int',$_POST['DEFAULT_TOKENS_STANDARD_VIDEO'],$lang['settings']['memberzone_field_tokens_purchase_videos']);
	}
	if ($_POST['ENABLE_TOKENS_PREMIUM_VIDEO']=='1' && $_POST['DEFAULT_TOKENS_PREMIUM_VIDEO']<>'0')
	{
		validate_field('empty_int',$_POST['DEFAULT_TOKENS_PREMIUM_VIDEO'],$lang['settings']['memberzone_field_tokens_purchase_videos']);
	}
	if ($_POST['ENABLE_TOKENS_STANDARD_ALBUM']=='1' && $_POST['DEFAULT_TOKENS_STANDARD_ALBUM']<>'0')
	{
		validate_field('empty_int',$_POST['DEFAULT_TOKENS_STANDARD_ALBUM'],$lang['settings']['memberzone_field_tokens_purchase_albums']);
	}
	if ($_POST['ENABLE_TOKENS_PREMIUM_ALBUM']=='1' && $_POST['DEFAULT_TOKENS_PREMIUM_ALBUM']<>'0')
	{
		validate_field('empty_int',$_POST['DEFAULT_TOKENS_PREMIUM_ALBUM'],$lang['settings']['memberzone_field_tokens_purchase_albums']);
	}
	if ($_POST['TOKENS_PURCHASE_EXPIRY']<>'')
	{
		validate_field('empty_int',$_POST['TOKENS_PURCHASE_EXPIRY'],$lang['settings']['memberzone_field_purchase_expiry']);
	}
	if ($_POST['ENABLE_TOKENS_INTERNAL_MESSAGES']=='1')
	{
		validate_field('empty_int',$_POST['TOKENS_INTERNAL_MESSAGES'],$lang['settings']['memberzone_field_tokens_enable_internal_messages']);
	}
	if ($_POST['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']=='1')
	{
		if ($_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']!='0' && $_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']!='')
		{
			validate_field('empty_int',$_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE'],$lang['settings']['memberzone_field_tokens_subscribe_members']);
		}
		if ($_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']<>'')
		{
			validate_field('empty_int',$_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD'],$lang['settings']['memberzone_field_tokens_subscribe_members']);
		}
	}
	if ($_POST['ENABLE_TOKENS_SUBSCRIBE_DVDS']=='1')
	{
		if ($_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']!='0' && $_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']!='')
		{
			validate_field('empty_int',$_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE'],$lang['settings']['memberzone_field_tokens_subscribe_dvds']);
		}
		if ($_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']<>'')
		{
			validate_field('empty_int',$_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD'],$lang['settings']['memberzone_field_tokens_subscribe_dvds']);
		}
	}
	if ($_POST['TOKENS_SALE_INTEREST']<>'0')
	{
		validate_field('empty_int',$_POST['TOKENS_SALE_INTEREST'],$lang['settings']['memberzone_field_tokens_sale_interest']);
	}
	if ($_POST['ENABLE_TOKENS_TRAFFIC_VIDEOS']=='1')
	{
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_VIDEOS_TOKENS'],$lang['settings']['memberzone_field_tokens_traffic_enable_videos']);
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_VIDEOS_UNIQUE'],$lang['settings']['memberzone_field_tokens_traffic_enable_videos']);
	}
	if ($_POST['ENABLE_TOKENS_TRAFFIC_ALBUMS']=='1')
	{
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_ALBUMS_TOKENS'],$lang['settings']['memberzone_field_tokens_traffic_enable_albums']);
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_ALBUMS_UNIQUE'],$lang['settings']['memberzone_field_tokens_traffic_enable_albums']);
	}
	if ($_POST['ENABLE_TOKENS_TRAFFIC_EMBEDS']=='1')
	{
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_EMBEDS_TOKENS'],$lang['settings']['memberzone_field_tokens_traffic_enable_embeds']);
		validate_field('empty_int',$_POST['TOKENS_TRAFFIC_EMBEDS_UNIQUE'],$lang['settings']['memberzone_field_tokens_traffic_enable_embeds']);
	}
	if ($_POST['ENABLE_TOKENS_DONATIONS']=='1')
	{
		validate_field('empty_int',$_POST['TOKENS_DONATION_MIN'],$lang['settings']['memberzone_field_tokens_enable_donations']);
	}
	if ($_POST['TOKENS_DONATION_INTEREST']<>'0')
	{
		validate_field('empty_int',$_POST['TOKENS_DONATION_INTEREST'],$lang['settings']['memberzone_field_tokens_donation_interest']);
	}
	if ($_POST['ACTIVITY_INDEX_FORMULA']<>'')
	{
		$formula=transform_activity_index_formula($_POST['ACTIVITY_INDEX_FORMULA']);
		$result=sql("select ($formula) as activity from $config[tables_prefix]users limit 1",false);
		if (mr2rows($result)==0)
		{
			$errors[]=get_aa_error('activity_index_formula',$lang['settings']['memberzone_field_activity_index_formula']);
		}
	}
	foreach ($awards as $award_id=>$field_name)
	{
		$has_award_error=false;
		if ($_POST["{$award_id}_CONDITION"]<>'0' && $_POST["{$award_id}_CONDITION"]<>'')
		{
			if (!validate_field('empty_int',$_POST["{$award_id}_CONDITION"],$field_name)){$has_award_error=true;}
		}
		if ($_POST[$award_id]<>'0' && $_POST[$award_id]<>'' && !$has_award_error)
		{
			validate_field('empty_int',$_POST[$award_id],$field_name);
		}
	}

	if (!is_writable("$config[project_path]/admin/data/system"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system"));
	}
	if (is_file("$config[project_path]/admin/data/system/memberzone_params.dat") && !is_writable("$config[project_path]/admin/data/system/memberzone_params.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/memberzone_params.dat"));
	}

	if (!is_array($errors))
	{
		$old_options=get_options();
		$update_details='';

		$params=array();
		$params['STATUS_AFTER_PREMIUM']=$_POST['STATUS_AFTER_PREMIUM'];
		$params['PUBLIC_VIDEOS_ACCESS']=intval($_POST['PUBLIC_VIDEOS_ACCESS']);
		$params['PRIVATE_VIDEOS_ACCESS']=intval($_POST['PRIVATE_VIDEOS_ACCESS']);
		$params['PREMIUM_VIDEOS_ACCESS']=intval($_POST['PREMIUM_VIDEOS_ACCESS']);
		$params['PUBLIC_ALBUMS_ACCESS']=intval($_POST['PUBLIC_ALBUMS_ACCESS']);
		$params['PRIVATE_ALBUMS_ACCESS']=intval($_POST['PRIVATE_ALBUMS_ACCESS']);
		$params['PREMIUM_ALBUMS_ACCESS']=intval($_POST['PREMIUM_ALBUMS_ACCESS']);
		$params['AFFILIATE_PARAM_NAME']=trim($_POST['AFFILIATE_PARAM_NAME']);
		$params['ENABLE_TOKENS_STANDARD_VIDEO']=intval($_POST['ENABLE_TOKENS_STANDARD_VIDEO']);
		$params['ENABLE_TOKENS_PREMIUM_VIDEO']=intval($_POST['ENABLE_TOKENS_PREMIUM_VIDEO']);
		$params['ENABLE_TOKENS_STANDARD_ALBUM']=intval($_POST['ENABLE_TOKENS_STANDARD_ALBUM']);
		$params['ENABLE_TOKENS_PREMIUM_ALBUM']=intval($_POST['ENABLE_TOKENS_PREMIUM_ALBUM']);
		$params['DEFAULT_TOKENS_STANDARD_VIDEO']=intval($_POST['DEFAULT_TOKENS_STANDARD_VIDEO']);
		$params['DEFAULT_TOKENS_PREMIUM_VIDEO']=intval($_POST['DEFAULT_TOKENS_PREMIUM_VIDEO']);
		$params['DEFAULT_TOKENS_STANDARD_ALBUM']=intval($_POST['DEFAULT_TOKENS_STANDARD_ALBUM']);
		$params['DEFAULT_TOKENS_PREMIUM_ALBUM']=intval($_POST['DEFAULT_TOKENS_PREMIUM_ALBUM']);
		$params['TOKENS_PURCHASE_EXPIRY']=$_POST['TOKENS_PURCHASE_EXPIRY'];
		$params['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']=intval($_POST['ENABLE_TOKENS_SUBSCRIBE_MEMBERS']);
		$params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']=intval($_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PRICE']);
		$params['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD']=$_POST['TOKENS_SUBSCRIBE_MEMBERS_DEFAULT_PERIOD'];
		$params['ENABLE_TOKENS_SUBSCRIBE_DVDS']=intval($_POST['ENABLE_TOKENS_SUBSCRIBE_DVDS']);
		$params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']=intval($_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PRICE']);
		$params['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD']=$_POST['TOKENS_SUBSCRIBE_DVDS_DEFAULT_PERIOD'];
		$params['ENABLE_TOKENS_SALE_VIDEOS']=intval($_POST['ENABLE_TOKENS_SALE_VIDEOS']);
		$params['ENABLE_TOKENS_SALE_ALBUMS']=intval($_POST['ENABLE_TOKENS_SALE_ALBUMS']);
		$params['ENABLE_TOKENS_SALE_MEMBERS']=intval($_POST['ENABLE_TOKENS_SALE_MEMBERS']);
		$params['ENABLE_TOKENS_SALE_DVDS']=intval($_POST['ENABLE_TOKENS_SALE_DVDS']);
		$params['TOKENS_SALE_INTEREST']=min(100,intval($_POST['TOKENS_SALE_INTEREST']));
		$params['TOKENS_SALE_EXCLUDES']=$_POST['TOKENS_SALE_EXCLUDES'];
		$params['ENABLE_TOKENS_DONATIONS']=intval($_POST['ENABLE_TOKENS_DONATIONS']);
		$params['TOKENS_DONATION_MIN']=intval($_POST['TOKENS_DONATION_MIN']);
		$params['TOKENS_DONATION_INTEREST']=min(100,intval($_POST['TOKENS_DONATION_INTEREST']));
		$params['ENABLE_TOKENS_INTERNAL_MESSAGES']=intval($_POST['ENABLE_TOKENS_INTERNAL_MESSAGES']);
		$params['TOKENS_INTERNAL_MESSAGES']=intval($_POST['TOKENS_INTERNAL_MESSAGES']);
		foreach ($awards as $award_id=>$field_name)
		{
			$params["{$award_id}_CONDITION"]=$_POST["{$award_id}_CONDITION"];
			$params["{$award_id}"]=$_POST["{$award_id}"];
		}
		file_put_contents("$config[project_path]/admin/data/system/memberzone_params.dat",serialize($params),LOCK_EX);

		$params['ENABLE_TOKENS_TRAFFIC_VIDEOS']=intval($_POST['ENABLE_TOKENS_TRAFFIC_VIDEOS']);
		$params['TOKENS_TRAFFIC_VIDEOS_TOKENS']=intval($_POST['TOKENS_TRAFFIC_VIDEOS_TOKENS']);
		$params['TOKENS_TRAFFIC_VIDEOS_UNIQUE']=intval($_POST['TOKENS_TRAFFIC_VIDEOS_UNIQUE']);
		$params['ENABLE_TOKENS_TRAFFIC_ALBUMS']=intval($_POST['ENABLE_TOKENS_TRAFFIC_ALBUMS']);
		$params['TOKENS_TRAFFIC_ALBUMS_TOKENS']=intval($_POST['TOKENS_TRAFFIC_ALBUMS_TOKENS']);
		$params['TOKENS_TRAFFIC_ALBUMS_UNIQUE']=intval($_POST['TOKENS_TRAFFIC_ALBUMS_UNIQUE']);
		$params['ENABLE_TOKENS_TRAFFIC_EMBEDS']=intval($_POST['ENABLE_TOKENS_TRAFFIC_EMBEDS']);
		$params['TOKENS_TRAFFIC_EMBEDS_TOKENS']=intval($_POST['TOKENS_TRAFFIC_EMBEDS_TOKENS']);
		$params['TOKENS_TRAFFIC_EMBEDS_UNIQUE']=intval($_POST['TOKENS_TRAFFIC_EMBEDS_UNIQUE']);
		$params['ACTIVITY_INDEX_FORMULA']=$_POST['ACTIVITY_INDEX_FORMULA'];
		$params['ACTIVITY_INDEX_INCLUDES']=$_POST['ACTIVITY_INDEX_INCLUDES'];
		foreach ($params as $var=>$val)
		{
			if (isset($val))
			{
				sql_pr("update $config[tables_prefix]options set value=? where variable=?",$val,$var);
				if ($old_options[$var] != $val)
				{
					$update_details .= "$var, ";
				}
			}
		}

		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=222, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		return_ajax_success("$page_name?page=memberzone_settings");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_POST['action'] == 'change_antispam_settings_complete')
{
	$errors = null;

	$params = [];
	foreach (['ANTISPAM_VIDEOS', 'ANTISPAM_ALBUMS', 'ANTISPAM_POSTS', 'ANTISPAM_PLAYLISTS', 'ANTISPAM_DVDS', 'ANTISPAM_COMMENTS', 'ANTISPAM_MESSAGES'] as $section)
	{
		foreach (['FORCE_CAPTCHA', 'FORCE_DISABLED', 'AUTODELETE', 'ERROR'] as $action)
		{
			if (isset($_POST["{$section}_{$action}_1"], $_POST["{$section}_{$action}_2"]))
			{
				$divider = '';
				switch ($section)
				{
					case 'ANTISPAM_VIDEOS':
						$divider = $lang['settings']['antispam_divider_videos'];
						break;
					case 'ANTISPAM_ALBUMS':
						$divider = $lang['settings']['antispam_divider_albums'];
						break;
					case 'ANTISPAM_POSTS':
						$divider = $lang['settings']['antispam_divider_posts'];
						break;
					case 'ANTISPAM_PLAYLISTS':
						$divider = $lang['settings']['antispam_divider_playlists'];
						break;
					case 'ANTISPAM_DVDS':
						$divider = $lang['settings']['antispam_divider_dvds'];
						break;
					case 'ANTISPAM_COMMENTS':
						$divider = $lang['settings']['antispam_divider_comments'];
						break;
					case 'ANTISPAM_MESSAGES':
						$divider = $lang['settings']['antispam_divider_messages'];
						break;
				}
				$field = '';
				switch ($action)
				{
					case 'FORCE_CAPTCHA':
						$field = $lang['settings']['antispam_field_action_force_captcha'];
						break;
					case 'FORCE_DISABLED':
						$field = $lang['settings']['antispam_field_action_deactivate'];
						break;
					case 'AUTODELETE':
						$field = $lang['settings']['antispam_field_action_autodelete'];
						break;
					case 'ERROR':
						$field = $lang['settings']['antispam_field_action_show_error'];
						break;
				}
				if ($_POST["{$section}_{$action}_1"] !='' || $_POST["{$section}_{$action}_2"] != '')
				{
					validate_field('empty_int', $_POST["{$section}_{$action}_1"], "$divider - $field");
					validate_field('empty_int', $_POST["{$section}_{$action}_2"], "$divider - $field");
				}
				$params["{$section}_{$action}"] = intval($_POST["{$section}_{$action}_1"]) . '/' . intval($_POST["{$section}_{$action}_2"]);
			}
		}
	}
	$params['ANTISPAM_BLACKLIST_WORDS'] = $_POST['ANTISPAM_BLACKLIST_WORDS'];
	$params['ANTISPAM_BLACKLIST_ACTION'] = intval($_POST['ANTISPAM_BLACKLIST_ACTION']);
	$params['ANTISPAM_COMMENTS_DUPLICATES'] = intval($_POST['ANTISPAM_COMMENTS_DUPLICATES']);
	$params['ANTISPAM_MESSAGES_DUPLICATES'] = intval($_POST['ANTISPAM_MESSAGES_DUPLICATES']);

	$params['ANTISPAM_VIDEOS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_VIDEOS_ANALYZE_HISTORY']);
	$params['ANTISPAM_ALBUMS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_ALBUMS_ANALYZE_HISTORY']);
	$params['ANTISPAM_POSTS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_POSTS_ANALYZE_HISTORY']);
	$params['ANTISPAM_PLAYLISTS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_PLAYLISTS_ANALYZE_HISTORY']);
	$params['ANTISPAM_DVDS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_DVDS_ANALYZE_HISTORY']);
	$params['ANTISPAM_COMMENTS_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_COMMENTS_ANALYZE_HISTORY']);
	$params['ANTISPAM_MESSAGES_ANALYZE_HISTORY'] = intval($_POST['ANTISPAM_MESSAGES_ANALYZE_HISTORY']);

	if (!is_array($errors))
	{
		$update_details = '';
		$old_options = get_options();

		foreach ($params as $var => $val)
		{
			if (isset($val))
			{
				sql_pr("update $config[tables_prefix]options set value=? where variable=?", $val, $var);
				if ($old_options[$var] != $val)
				{
					$update_details .= "$var, ";
				}
			}
		}

		$old_blocked_domains_str = implode(',', mr2array_list(sql_pr("select domain from $config[tables_prefix]users_blocked_domains order by sort_id asc")));
		$old_blocked_ips_str = implode(',', mr2array_list(sql_pr("select ip from $config[tables_prefix]users_blocked_ips order by sort_id asc")));

		$_POST['ANTISPAM_BLACKLIST_DOMAINS'] = str_replace(array("\n", "\r"), ',', $_POST['ANTISPAM_BLACKLIST_DOMAINS']);
		$_POST['ANTISPAM_BLACKLIST_IPS'] = str_replace(array("\n", "\r"), ',', $_POST['ANTISPAM_BLACKLIST_IPS']);

		$blocked_domains = array_map('trim', explode(",", $_POST['ANTISPAM_BLACKLIST_DOMAINS']));
		sql_pr("delete from $config[tables_prefix]users_blocked_domains");
		for ($i = 0; $i < count($blocked_domains); $i++)
		{
			$blocked_domain = $blocked_domains[$i];
			if ($blocked_domain != '')
			{
				sql_pr("insert into $config[tables_prefix]users_blocked_domains set domain=?, sort_id=?", $blocked_domain, $i);
			}
		}
		$new_blocked_domains_str = implode(',', $blocked_domains);

		$blocked_ips = array_map('trim', explode(",", $_POST['ANTISPAM_BLACKLIST_IPS']));
		sql_pr("delete from $config[tables_prefix]users_blocked_ips");
		for ($i = 0; $i < count($blocked_ips); $i++)
		{
			$blocked_ip = $blocked_ips[$i];
			if ($blocked_ip != '')
			{
				sql_pr("insert into $config[tables_prefix]users_blocked_ips set ip=?, sort_id=?", $blocked_ip, $i);
			}
		}
		$new_blocked_ips_str = implode(',', $blocked_ips);

		if ($old_blocked_domains_str != $new_blocked_domains_str)
		{
			$update_details .= "ANTISPAM_BLACKLIST_DOMAINS, ";
		}
		if ($old_blocked_ips_str != $new_blocked_ips_str)
		{
			$update_details .= "ANTISPAM_BLACKLIST_IPS, ";
		}

		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=227, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][] = $lang['settings']['common_success_message_modified'];
		return_ajax_success("$page_name?page=antispam_settings");
	} else
	{
		return_ajax_errors($errors);
	}
}

if ($_POST['action']=='change_stats_settings_complete')
{
	$errors = null;

	if ($_POST['keep_traffic_stats_period']<>'0' && $_POST['keep_traffic_stats_period']<>'')
	{
		validate_field('empty_int',$_POST['keep_traffic_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
	}
	if ($_POST['keep_player_stats_period']<>'0' && $_POST['keep_player_stats_period']<>'')
	{
		validate_field('empty_int',$_POST['keep_player_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
	}
	if ($_POST['keep_videos_stats_period']<>'0' && $_POST['keep_videos_stats_period']<>'')
	{
		validate_field('empty_int',$_POST['keep_videos_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
	}
	if ($config['installation_type']==4)
	{
		if ($_POST['keep_albums_stats_period']<>'0' && $_POST['keep_albums_stats_period']<>'')
		{
			validate_field('empty_int',$_POST['keep_albums_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
		}
	}
	if ($_POST['keep_memberzone_stats_period']<>'0' && $_POST['keep_memberzone_stats_period']<>'')
	{
		validate_field('empty_int',$_POST['keep_memberzone_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
	}
	if ($_POST['keep_search_stats_period']<>'0' && $_POST['keep_search_stats_period']<>'')
	{
		validate_field('empty_int',$_POST['keep_search_stats_period'],$lang['settings']['stats_field_keep_stats_for']);
	}
	if ($_POST['search_max_length']<>'0' && $_POST['search_max_length']<>'')
	{
		validate_field('empty_int',$_POST['search_max_length'],$lang['settings']['stats_field_search_max_length']);
	}

	if (!is_writable("$config[project_path]/admin/data/system"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system"));
	}
	if (is_file("$config[project_path]/admin/data/system/stats_params.dat") && !is_writable("$config[project_path]/admin/data/system/stats_params.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/stats_params.dat"));
	}

	if (!is_array($errors))
	{
		$params=array();
		$params['collect_traffic_stats']=intval($_POST['collect_traffic_stats']);
		$params['collect_traffic_stats_countries']=intval($_POST['collect_traffic_stats_countries']);
		$params['collect_traffic_stats_devices']=intval($_POST['collect_traffic_stats_devices']);
		$params['collect_traffic_stats_embed_domains']=intval($_POST['collect_traffic_stats_embed_domains']);
		$params['collect_player_stats']=intval($_POST['collect_player_stats']);
		$params['collect_player_stats_countries']=intval($_POST['collect_player_stats_countries']);
		$params['collect_player_stats_devices']=intval($_POST['collect_player_stats_devices']);
		$params['collect_player_stats_embed_profiles']=intval($_POST['collect_player_stats_embed_profiles']);
		$params['collect_videos_stats']=intval($_POST['collect_videos_stats']);
		$params['collect_videos_stats_unique']=intval($_POST['collect_videos_stats_unique']);
		$params['collect_videos_stats_video_plays']=intval($_POST['collect_videos_stats_video_plays']);
		$params['collect_videos_stats_video_files']=intval($_POST['collect_videos_stats_video_files']);
		$params['collect_albums_stats']=intval($_POST['collect_albums_stats']);
		$params['collect_albums_stats_unique']=intval($_POST['collect_albums_stats_unique']);
		$params['collect_albums_stats_album_images']=intval($_POST['collect_albums_stats_album_images']);
		$params['collect_memberzone_stats']=intval($_POST['collect_memberzone_stats']);
		$params['collect_memberzone_stats_video_files']=intval($_POST['collect_memberzone_stats_video_files']);
		$params['collect_memberzone_stats_album_images']=intval($_POST['collect_memberzone_stats_album_images']);
		$params['collect_search_stats']=intval($_POST['collect_search_stats']);
		$params['keep_traffic_stats_period']=intval($_POST['keep_traffic_stats_period']);
		$params['keep_player_stats_period']=intval($_POST['keep_player_stats_period']);
		$params['keep_videos_stats_period']=intval($_POST['keep_videos_stats_period']);
		$params['keep_albums_stats_period']=intval($_POST['keep_albums_stats_period']);
		$params['keep_memberzone_stats_period']=intval($_POST['keep_memberzone_stats_period']);
		$params['keep_search_stats_period']=intval($_POST['keep_search_stats_period']);
		$params['player_stats_reporting']=intval($_POST['player_stats_reporting']);
		$params['search_to_lowercase']=intval($_POST['search_to_lowercase']);
		$params['search_max_length']=intval($_POST['search_max_length']);
		$params['search_stop_symbols']=$_POST['search_stop_symbols'];

		$update_details='';
		$old_options=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));

		file_put_contents("$config[project_path]/admin/data/system/stats_params.dat",serialize($params),LOCK_EX);

		foreach ($old_options as $var=>$val)
		{
			if ($params[$var] != $val)
			{
				$update_details .= "$var, ";
			}
		}
		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=223, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		return_ajax_success("$page_name?page=stats_settings");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_POST['action']=='change_complete')
{
	$errors = null;

	if ($_POST['FILE_UPLOAD_SIZE_LIMIT']!='')
	{
		validate_field('empty_int',$_POST['FILE_UPLOAD_SIZE_LIMIT'],$lang['settings']['system_field_file_upload_size_limit']);
	}

	if ($_POST['FILE_DOWNLOAD_SPEED_LIMIT']!='')
	{
		validate_field('empty_int',$_POST['FILE_DOWNLOAD_SPEED_LIMIT'],$lang['settings']['system_field_file_download_speed_limit']);
	}

	if ($config['installation_type']>=2)
	{
		validate_field('size',$_POST['USER_AVATAR_SIZE'],$lang['settings']['system_field_user_avatar_size']);
		if ($_POST['USER_COVER_OPTION']<>0 || $_POST['USER_COVER_SIZE']<>'')
		{
			validate_field('size',$_POST['USER_COVER_SIZE'],$lang['settings']['system_field_user_avatar_size']);
		}
	}

	validate_field('size',$_POST['CATEGORY_AVATAR_SIZE'],$lang['settings']['system_field_category_screenshot_size']);
	if ($_POST['CATEGORY_AVATAR_OPTION']<>0 || $_POST['CATEGORY_AVATAR_2_SIZE']<>'')
	{
		validate_field('size',$_POST['CATEGORY_AVATAR_2_SIZE'],$lang['settings']['system_field_category_screenshot_size']);
	}

	validate_field('size',$_POST['CS_SCREENSHOT_1_SIZE'],$lang['settings']['system_field_cs_screenshot_size']);
	if ($_POST['CS_SCREENSHOT_OPTION']<>0 || $_POST['CS_SCREENSHOT_2_SIZE']<>'')
	{
		validate_field('size',$_POST['CS_SCREENSHOT_2_SIZE'],$lang['settings']['system_field_cs_screenshot_size']);
	}

	if ($config['installation_type']>=2)
	{
		validate_field('size',$_POST['MODELS_SCREENSHOT_1_SIZE'],$lang['settings']['system_field_model_screenshot_size']);
		if ($_POST['MODELS_SCREENSHOT_OPTION']<>0 || $_POST['MODELS_SCREENSHOT_2_SIZE']<>'')
		{
			validate_field('size',$_POST['MODELS_SCREENSHOT_2_SIZE'],$lang['settings']['system_field_model_screenshot_size']);
		}
	}

	if ($config['installation_type']==4)
	{
		validate_field('size',$_POST['DVD_COVER_1_SIZE'],$lang['settings']['system_field_dvd_cover_size']);
		if ($_POST['DVD_COVER_OPTION']<>0 || $_POST['DVD_COVER_2_SIZE']<>'')
		{
			validate_field('size',$_POST['DVD_COVER_2_SIZE'],$lang['settings']['system_field_dvd_cover_size']);
		}
		validate_field('size',$_POST['DVD_GROUP_COVER_1_SIZE'],$lang['settings']['system_field_dvd_group_cover_size']);
		if ($_POST['DVD_GROUP_COVER_OPTION']<>0 || $_POST['DVD_GROUP_COVER_2_SIZE']<>'')
		{
			validate_field('size',$_POST['DVD_GROUP_COVER_2_SIZE'],$lang['settings']['system_field_dvd_group_cover_size']);
		}
	}

	if (intval($_POST['TAGS_DISABLE_ALL']) == 0)
	{
		if (intval($_POST['ENABLE_TAGS_DISABLE_COMPOUND']) == 1)
		{
			validate_field('empty_int', $_POST['TAGS_DISABLE_COMPOUND'], $lang['settings']['system_field_tags_disable']);
		} else
		{
			$_POST['TAGS_DISABLE_COMPOUND'] = 0;
		}
		if (intval($_POST['ENABLE_TAGS_DISABLE_LENGTH_MIN']) == 1)
		{
			validate_field('empty_int', $_POST['TAGS_DISABLE_LENGTH_MIN'], $lang['settings']['system_field_tags_disable']);
		} else
		{
			$_POST['TAGS_DISABLE_LENGTH_MIN'] = 0;
		}
		if (intval($_POST['ENABLE_TAGS_DISABLE_LENGTH_MAX']) == 1)
		{
			validate_field('empty_int', $_POST['TAGS_DISABLE_LENGTH_MAX'], $lang['settings']['system_field_tags_disable']);
		} else
		{
			$_POST['TAGS_DISABLE_LENGTH_MAX'] = 0;
		}
		if (intval($_POST['ENABLE_TAGS_DISABLE_CHARACTERS']) == 1)
		{
			validate_field('empty', $_POST['TAGS_DISABLE_CHARACTERS'], $lang['settings']['system_field_tags_disable']);
		} else
		{
			$_POST['TAGS_DISABLE_CHARACTERS'] = '';
		}
		if (intval($_POST['TAGS_DISABLE_LIST_ENABLED']) == 1)
		{
			validate_field('empty', $_POST['TAGS_DISABLE_LIST'], $lang['settings']['system_field_tags_disable']);
		}
	}

	validate_field('empty_int',$_POST['DIRECTORIES_MAX_LENGTH'],$lang['settings']['system_field_directories_max_length']);

	if (intval($_POST['LIMIT_CONVERSION_LA_ENABLE'])==1)
	{
		validate_field('empty_float',$_POST['LIMIT_CONVERSION_LA'],$lang['settings']['system_field_conversion_limit']);
	} else {
		$_POST['LIMIT_CONVERSION_LA']='';
	}
	if (intval($_POST['LIMIT_CONVERSION_TIME_ENABLE'])==1)
	{
		if ($_POST['LIMIT_CONVERSION_TIME_FROM']=='' && $_POST['LIMIT_CONVERSION_TIME_TO']=='')
		{
			validate_field('empty',$_POST['LIMIT_CONVERSION_TIME_FROM'],$lang['settings']['system_field_conversion_limit']);
		} else {
			if ($_POST['LIMIT_CONVERSION_TIME_FROM']!='')
			{
				validate_field('time',$_POST['LIMIT_CONVERSION_TIME_FROM'],$lang['settings']['system_field_conversion_limit']);
			}
			if ($_POST['LIMIT_CONVERSION_TIME_TO']!='')
			{
				validate_field('time',$_POST['LIMIT_CONVERSION_TIME_TO'],$lang['settings']['system_field_conversion_limit']);
			}
		}
	} else {
		$_POST['LIMIT_CONVERSION_TIME_FROM']='';
		$_POST['LIMIT_CONVERSION_TIME_TO']='';
	}

	validate_field('empty_int',$_POST['MAIN_SERVER_MIN_FREE_SPACE_MB'],$lang['settings']['system_field_min_server_space_to_alert']);
	if (intval($_POST['MAIN_SERVER_MIN_FREE_SPACE_MB'])<intval($config['min_allowed_free_space_limit_mb']))
	{
		$_POST['MAIN_SERVER_MIN_FREE_SPACE_MB']=intval($config['min_allowed_free_space_limit_mb']);
	}
	validate_field('empty_int',$_POST['SERVER_GROUP_MIN_FREE_SPACE_MB'],$lang['settings']['system_field_min_server_group_space_to_alert']);
	if (intval($_POST['SERVER_GROUP_MIN_FREE_SPACE_MB'])<intval($config['min_allowed_free_space_limit_mb']))
	{
		$_POST['SERVER_GROUP_MIN_FREE_SPACE_MB']=intval($config['min_allowed_free_space_limit_mb']);
	}
	validate_field('empty_int',$_POST['LIMIT_MEMORY'],$lang['settings']['system_field_memory_limit']);
	if (intval($_POST['VIDEOS_DUPLICATE_TITLE_OPTION'])==1)
	{
		if (validate_field('empty',$_POST['VIDEOS_DUPLICATE_TITLE_POSTFIX'],$lang['settings']['system_field_videos_duplicate_title']))
		{
			if (strpos($_POST['VIDEOS_DUPLICATE_TITLE_POSTFIX'],'%NUM%')===false)
			{
				$errors[]=get_aa_error('token_required',$lang['settings']['system_field_videos_duplicate_title'],'%NUM%');
			}
		}
	}

	if (intval($_POST['SCREENSHOTS_COUNT_UNIT'])==1)
	{
		validate_field('empty_int',$_POST['SCREENSHOTS_COUNT_FIXED'],$lang['settings']['system_field_screenshots_count']);
	} else {
		validate_field('empty_int',$_POST['SCREENSHOTS_COUNT_DYNAMIC'],$lang['settings']['system_field_screenshots_count']);
	}

	if ($_POST['SCREENSHOTS_CROP_LEFT']=='')
	{
		$_POST['SCREENSHOTS_CROP_LEFT']='0';
	}
	if ($_POST['SCREENSHOTS_CROP_TOP']=='')
	{
		$_POST['SCREENSHOTS_CROP_TOP']='0';
	}
	if ($_POST['SCREENSHOTS_CROP_RIGHT']=='')
	{
		$_POST['SCREENSHOTS_CROP_RIGHT']='0';
	}
	if ($_POST['SCREENSHOTS_CROP_BOTTOM']=='')
	{
		$_POST['SCREENSHOTS_CROP_BOTTOM']='0';
	}

	$crop_ok=1;
	if ($_POST['SCREENSHOTS_CROP_LEFT']<>'0')
	{
		$crop_ok=validate_field('empty_int',$_POST['SCREENSHOTS_CROP_LEFT'],$lang['settings']['system_field_screenshots_crop']);
	}
	if ($_POST['SCREENSHOTS_CROP_TOP']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['SCREENSHOTS_CROP_TOP'],$lang['settings']['system_field_screenshots_crop']);
	}
	if ($_POST['SCREENSHOTS_CROP_RIGHT']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['SCREENSHOTS_CROP_RIGHT'],$lang['settings']['system_field_screenshots_crop']);
	}
	if ($_POST['SCREENSHOTS_CROP_BOTTOM']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['SCREENSHOTS_CROP_BOTTOM'],$lang['settings']['system_field_screenshots_crop']);
	}

	if ($_POST['SCREENSHOTS_SECONDS_OFFSET']<>'0')
	{
		validate_field('empty_int',$_POST['SCREENSHOTS_SECONDS_OFFSET'],$lang['settings']['system_field_screenshots_seconds_offset']);
	}
	if ($_POST['SCREENSHOTS_SECONDS_OFFSET_END']<>'0')
	{
		validate_field('empty_int',$_POST['SCREENSHOTS_SECONDS_OFFSET_END'],$lang['settings']['system_field_screenshots_seconds_offset_end']);
	}
	validate_field('empty_int',$_POST['SCREENSHOTS_MAIN_NUMBER'],$lang['settings']['system_field_screenshots_main_number']);

	if ($_POST['ENABLE_ANTI_HOTLINK']==1)
	{
		if ($_POST['ANTI_HOTLINK_ENABLE_IP_LIMIT']==1)
		{
			validate_field('empty_int',$_POST['ANTI_HOTLINK_N_VIDEOS'],$lang['settings']['system_field_antihotlink_limitation']);
			validate_field('empty_int',$_POST['ANTI_HOTLINK_N_HOURS'],$lang['settings']['system_field_antihotlink_limitation']);
		}
		validate_field('remote_file',$_POST['ANTI_HOTLINK_FILE'],$lang['settings']['system_field_antihotlink_custom_file'],array('is_required'=>0,'is_available'=>1));
	}

	if ($_POST['ROTATOR_VIDEOS_ENABLE']==1)
	{
		if ($_POST['ROTATOR_SCREENSHOTS_ENABLE']==1)
		{
			validate_field('empty_int',$_POST['ROTATOR_SCREENSHOTS_MIN_SHOWS'],$lang['settings']['system_field_rotator_screenshots_min_shows']);
			validate_field('empty_int',$_POST['ROTATOR_SCREENSHOTS_MIN_CLICKS'],$lang['settings']['system_field_rotator_screenshots_min_clicks']);
			if ($_POST['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION']==1)
			{
				validate_field('empty_int',$_POST['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT'],$lang['settings']['system_field_rotator_screenshots_delete']);
			}
		}

		if (validate_field('empty_int',$_POST['ROTATOR_SCHEDULE_INTERVAL'],$lang['settings']['system_field_rotator_schedule']))
		{
			if ($_POST['ROTATOR_SCHEDULE_PAUSE_FROM']!='' || $_POST['ROTATOR_SCHEDULE_PAUSE_TO']!='')
			{
				if (validate_field('time',$_POST['ROTATOR_SCHEDULE_PAUSE_FROM'],$lang['settings']['system_field_rotator_schedule']))
				{
					validate_field('time',$_POST['ROTATOR_SCHEDULE_PAUSE_TO'],$lang['settings']['system_field_rotator_schedule']);
				}
			}
		}
	} else {
		$_POST['ROTATOR_SCREENSHOTS_ENABLE']=0;
	}

	if (intval($_POST['ALBUMS_DUPLICATE_TITLE_OPTION'])==1)
	{
		if (validate_field('empty',$_POST['ALBUMS_DUPLICATE_TITLE_POSTFIX'],$lang['settings']['system_field_albums_duplicate_title']))
		{
			if (strpos($_POST['ALBUMS_DUPLICATE_TITLE_POSTFIX'],'%NUM%')===false)
			{
				$errors[]=get_aa_error('token_required',$lang['settings']['system_field_albums_duplicate_title'],'%NUM%');
			}
		}
	}
	if ($_POST['ALBUMS_CROP_LEFT']=='')
	{
		$_POST['ALBUMS_CROP_LEFT']='0';
	}
	if ($_POST['ALBUMS_CROP_TOP']=='')
	{
		$_POST['ALBUMS_CROP_TOP']='0';
	}
	if ($_POST['ALBUMS_CROP_RIGHT']=='')
	{
		$_POST['ALBUMS_CROP_RIGHT']='0';
	}
	if ($_POST['ALBUMS_CROP_BOTTOM']=='')
	{
		$_POST['ALBUMS_CROP_BOTTOM']='0';
	}

	$crop_ok=1;
	if ($_POST['ALBUMS_CROP_LEFT']<>'0')
	{
		$crop_ok=validate_field('empty_int',$_POST['ALBUMS_CROP_LEFT'],$lang['settings']['system_field_albums_crop']);
	}
	if ($_POST['ALBUMS_CROP_TOP']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['ALBUMS_CROP_TOP'],$lang['settings']['system_field_albums_crop']);
	}
	if ($_POST['ALBUMS_CROP_RIGHT']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['ALBUMS_CROP_RIGHT'],$lang['settings']['system_field_albums_crop']);
	}
	if ($_POST['ALBUMS_CROP_BOTTOM']<>'0' && $crop_ok==1)
	{
		$crop_ok=validate_field('empty_int',$_POST['ALBUMS_CROP_BOTTOM'],$lang['settings']['system_field_albums_crop']);
	}

	if ($_POST['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']<>'')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$_POST['DEFAULT_USER_IN_ADMIN_ADD_VIDEO']))==0)
		{
			$errors[]=get_aa_error('invalid_user',$lang['settings']['system_field_add_video_default_user']);
		}
	}

	if ($_POST['DEFAULT_USER_IN_ADMIN_ADD_ALBUM']<>'')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$_POST['DEFAULT_USER_IN_ADMIN_ADD_ALBUM']))==0)
		{
			$errors[]=get_aa_error('invalid_user',$lang['settings']['system_field_add_album_default_user']);
		}
	}

	if ($_POST['DEFAULT_USER_IN_ADMIN_ADD_POST']<>'')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$_POST['DEFAULT_USER_IN_ADMIN_ADD_POST']))==0)
		{
			$errors[]=get_aa_error('invalid_user',$lang['settings']['system_field_add_post_default_user']);
		}
	}

	if ($_POST['API_ENABLE']==1)
	{
		validate_field('empty',$_POST['API_PASSWORD'],$lang['settings']['system_field_api_password']);
	}

	if (!is_writable("$config[project_path]/admin/data/system"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system"));
	}
	if (is_file("$config[project_path]/admin/data/system/rotator.dat") && !is_writable("$config[project_path]/admin/data/system/rotator.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/rotator.dat"));
	}
	if (is_file("$config[project_path]/admin/data/system/api.dat") && !is_writable("$config[project_path]/admin/data/system/api.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/api.dat"));
	}
	if (is_file("$config[project_path]/admin/data/system/hotlink_info.dat") && !is_writable("$config[project_path]/admin/data/system/hotlink_info.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/hotlink_info.dat"));
	}
	if (is_file("$config[project_path]/admin/data/system/file_upload_params.dat") && !is_writable("$config[project_path]/admin/data/system/file_upload_params.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/file_upload_params.dat"));
	}

	if (!is_array($errors))
	{
		$old_options=get_options();

		$_POST['SCREENSHOTS_COUNT']=$_POST['SCREENSHOTS_COUNT_FIXED'];
		if (intval($_POST['SCREENSHOTS_COUNT_UNIT'])==2)
		{
			$_POST['SCREENSHOTS_COUNT']=$_POST['SCREENSHOTS_COUNT_DYNAMIC'];
		}

		if (intval($_POST['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT_OPTION'])==0)
		{
			$_POST['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']=0;
		}

		$update_details='';
		foreach ($old_options as $var=>$old_value)
		{
			if (isset($_POST[$var]))
			{
				if ($_POST[$var] != $old_value)
				{
					$update_details .= "$var, ";
				}
				sql_pr("update $config[tables_prefix]options set value=? where variable=?",$_POST[$var],$var);
			}
		}

		$file_upload_params=array();
		$file_upload_params['FILE_UPLOAD_DISK_OPTION']=$_POST['FILE_UPLOAD_DISK_OPTION'];
		$file_upload_params['FILE_UPLOAD_URL_OPTION']=$_POST['FILE_UPLOAD_URL_OPTION'];
		$file_upload_params['FILE_UPLOAD_SIZE_LIMIT']=intval($_POST['FILE_UPLOAD_SIZE_LIMIT']);
		$file_upload_params['FILE_DOWNLOAD_SPEED_LIMIT']=intval($_POST['FILE_DOWNLOAD_SPEED_LIMIT']);
		file_put_contents("$config[project_path]/admin/data/system/file_upload_params.dat",serialize($file_upload_params),LOCK_EX);

		$anti_hotlink_params=array();
		$anti_hotlink_params['ENABLE_ANTI_HOTLINK']=intval($_POST['ENABLE_ANTI_HOTLINK']);
		$anti_hotlink_params['ANTI_HOTLINK_ENABLE_IP_LIMIT']=intval($_POST['ANTI_HOTLINK_ENABLE_IP_LIMIT']);
		$anti_hotlink_params['ANTI_HOTLINK_TYPE']=intval($_POST['ANTI_HOTLINK_TYPE']);
		$anti_hotlink_params['ANTI_HOTLINK_ENCODE_LINKS']=intval($_POST['ANTI_HOTLINK_ENCODE_LINKS']);
		$anti_hotlink_params['ANTI_HOTLINK_FILE']=$_POST['ANTI_HOTLINK_FILE'];
		$anti_hotlink_params['ANTI_HOTLINK_WHITE_DOMAINS']=$_POST['ANTI_HOTLINK_WHITE_DOMAINS'];
		$anti_hotlink_params['ANTI_HOTLINK_WHITE_IPS']=$_POST['ANTI_HOTLINK_WHITE_IPS'];
		file_put_contents("$config[project_path]/admin/data/system/hotlink_info.dat",serialize($anti_hotlink_params),LOCK_EX);

		$rotator_params=array();
		$rotator_params['ROTATOR_VIDEOS_ENABLE']=intval($_POST['ROTATOR_VIDEOS_ENABLE']);
		$rotator_params['ROTATOR_VIDEOS_CATEGORIES_ENABLE']=intval($_POST['ROTATOR_VIDEOS_CATEGORIES_ENABLE']);
		$rotator_params['ROTATOR_VIDEOS_TAGS_ENABLE']=intval($_POST['ROTATOR_VIDEOS_TAGS_ENABLE']);
		$rotator_params['ROTATOR_SCREENSHOTS_ENABLE']=intval($_POST['ROTATOR_SCREENSHOTS_ENABLE']);
		$rotator_params['ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE']=intval($_POST['ROTATOR_SCREENSHOTS_ONLY_ONE_ENABLE']);
		$rotator_params['ROTATOR_SCREENSHOTS_MIN_SHOWS']=intval($_POST['ROTATOR_SCREENSHOTS_MIN_SHOWS']);
		$rotator_params['ROTATOR_SCREENSHOTS_MIN_CLICKS']=intval($_POST['ROTATOR_SCREENSHOTS_MIN_CLICKS']);
		$rotator_params['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']=intval($_POST['ROTATOR_SCREENSHOTS_SCREENSHOTS_LEFT']);
		file_put_contents("$config[project_path]/admin/data/system/rotator.dat",serialize($rotator_params),LOCK_EX);

		$params=array();
		$params['API_ENABLE']=intval($_POST['API_ENABLE']);
		$params['API_PASSWORD']=$_POST['API_PASSWORD'];
		file_put_contents("$config[project_path]/admin/data/system/api.dat",serialize($params),LOCK_EX);

		if (intval($_POST['ENABLE_BACKGROUND_TASKS_PAUSE'])==0)
		{
			@unlink("$config[project_path]/admin/data/system/background_tasks_pause.dat");
		}
		if ($old_options['ROTATOR_SCREENSHOTS_ENABLE']==0 && intval($_POST['ROTATOR_SCREENSHOTS_ENABLE'])==1)
		{
			sql("update $config[tables_prefix]videos set rs_dlist=0, rs_ccount=0");
		}

		if (strlen($update_details) > 0)
		{
			$update_details = substr($update_details, 0, -2);
		}
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=220, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

		$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
		return_ajax_success("$page_name?page=$_REQUEST[page]");
	} else {
		return_ajax_errors($errors);
	}
}

if ($_POST['action']=='change_customization_complete')
{
	$update_details='';

	$old_options=get_options();
	foreach ($old_options as $var=>$old_value)
	{
		if (isset($_POST[$var]))
		{
			if ($_POST[$var] != $old_value)
			{
				$update_details .= "$var, ";
			}
			sql_pr("update $config[tables_prefix]options set value=? where variable=?",$_POST[$var],$var);
		}
	}

	if (strlen($update_details) > 0)
	{
		$update_details = substr($update_details, 0, -2);
	}
	sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=224, object_type_id=30, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $update_details, date("Y-m-d H:i:s"));

	$_SESSION['messages'][]=$lang['settings']['common_success_message_modified'];
	return_ajax_success("$page_name?page=$_REQUEST[page]");
}

$data=get_options();

if ($data['SCREENSHOTS_COUNT_UNIT']==1)
{
	$data['SCREENSHOTS_COUNT_FIXED']=$data['SCREENSHOTS_COUNT'];
} else {
	$data['SCREENSHOTS_COUNT_DYNAMIC']=$data['SCREENSHOTS_COUNT'];
}

if ($data['ROTATOR_SCREENSHOTS_ENABLE']==1)
{
	$shows_interval=intval($data['ROTATOR_SCREENSHOTS_MIN_SHOWS']/5);
	$distribution_shows=mr2array_list(sql(
		"select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_dlist<=$shows_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_dlist>$shows_interval and rs_dlist<=2*$shows_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_dlist>2*$shows_interval and rs_dlist<=3*$shows_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_dlist>3*$shows_interval and rs_dlist<=4*$shows_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_dlist>4*$shows_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=1 and status_id in (0,1) ".
		"union all select count(*) from $config[tables_prefix]videos where status_id in (0,1)"
	));
	$clicks_interval=intval($data['ROTATOR_SCREENSHOTS_MIN_CLICKS']/5);
	$distribution_clicks=mr2array_list(sql(
		"select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_ccount<=$clicks_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_ccount>$clicks_interval and rs_ccount<=2*$clicks_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_ccount>2*$clicks_interval and rs_ccount<=3*$clicks_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_ccount>3*$clicks_interval and rs_ccount<=4*$clicks_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=0 and status_id in (0,1) and rs_ccount>4*$clicks_interval ".
		"union all select count(*) from $config[tables_prefix]videos where rs_completed=1 and status_id in (0,1) ".
		"union all select count(*) from $config[tables_prefix]videos where status_id in (0,1)"
	));
	$rotator_completeness=array();
	if ($distribution_shows[6]>0 && $distribution_clicks[6]>0)
	{
		for ($i=0;$i<6;$i++)
		{
			$shows_pc=$distribution_shows[$i]/$distribution_shows[6];
			$clicks_pc=$distribution_clicks[$i]/$distribution_clicks[6];
			$rotator_completeness[$i]['value']=($shows_pc+$clicks_pc)/2;
			$rotator_completeness[$i]['percent']=number_format(($shows_pc+$clicks_pc)/2*100,2);
			$rotator_completeness[$i]['amount']=($distribution_shows[$i]+$distribution_clicks[$i])/2;
			$rotator_completeness[$i]['shows']=$distribution_shows[$i];
			$rotator_completeness[$i]['clicks']=$distribution_clicks[$i];
		}
	}
}

$blocked_ips=@file("$config[project_path]/admin/data/stats/ip_blocked.dat");
$blocked_ips=array_map('trim',$blocked_ips);
$blocked_ips_str='';
for ($i=0;$i<count($blocked_ips);$i++)
{
	$blocked_ips_str.=$blocked_ips[$i];
	if ($i<count($blocked_ips)-1)
	{
		$blocked_ips_str.=', ';
	}
}
$data['BLOCKED_IPS']=$blocked_ips_str;

$dynamic_params=unserialize(@file_get_contents("$config[project_path]/admin/data/system/runtime_params.dat"));
$data['DYNAMIC_PARAMS']=array();
$data['DYNAMIC_PARAMS_VALUES']=array();
$data['DYNAMIC_PARAMS_LIFETIMES']=array();
foreach($dynamic_params as $param)
{
	$data['DYNAMIC_PARAMS'][]=$param['name'];
	$data['DYNAMIC_PARAMS_VALUES'][]=$param['default_value'];
	$data['DYNAMIC_PARAMS_LIFETIMES'][]=$param['lifetime'];
}

$website_ui_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
foreach ($website_ui_data as $k=>$v)
{
	$data[$k]=$v;
}

$blocked_words=unserialize(@file_get_contents("$config[project_path]/admin/data/system/blocked_words.dat"));
foreach ($blocked_words as $k=>$v)
{
	$data[$k]=$v;
}

$memberzone_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/memberzone_params.dat"));
foreach ($memberzone_data as $k=>$v)
{
	$data[$k]=$v;
}

$file_upload_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"));
foreach ($file_upload_data as $k=>$v)
{
	$data[$k]=$v;
}

$stats_data=unserialize(@file_get_contents("$config[project_path]/admin/data/system/stats_params.dat"));
foreach ($stats_data as $k=>$v)
{
	$data[$k]=$v;
}

$data['ANTISPAM_BLACKLIST_DOMAINS'] = implode("\n", mr2array_list(sql_pr("select domain from $config[tables_prefix]users_blocked_domains order by sort_id asc")));
$data['ANTISPAM_BLACKLIST_IPS'] = implode("\n", mr2array_list(sql_pr("select ip from $config[tables_prefix]users_blocked_ips order by sort_id asc")));

foreach (['ANTISPAM_VIDEOS', 'ANTISPAM_ALBUMS', 'ANTISPAM_POSTS', 'ANTISPAM_PLAYLISTS', 'ANTISPAM_DVDS', 'ANTISPAM_COMMENTS', 'ANTISPAM_MESSAGES'] as $section)
{
	foreach (['FORCE_CAPTCHA', 'FORCE_DISABLED', 'AUTODELETE', 'ERROR'] as $action)
	{
		$temp = explode('/', $data["{$section}_{$action}"], 2);
		$data["{$section}_{$action}_1"] = intval($temp[0]);
		$data["{$section}_{$action}_2"] = intval($temp[1]);
	}
}

$personal_data=mr2array_single(sql_pr("select * from $table_name where user_id=?",$_SESSION['userdata']['user_id']));

$list_langs_temp=str_replace(".php","",get_contents_from_dir("$config[project_path]/admin/langs/",1));unset($list_langs);
foreach ($list_langs_temp as $v)
{
	$list_langs[$v]=$v;
}
$list_skins_temp=str_replace(".css","",get_contents_from_dir("$config[project_path]/admin/styles/",1));unset($list_skins);
foreach ($list_skins_temp as $v)
{
	if ($v!='.htaccess')
	{
		$list_skins[$v]=$v;
	}
}

$options=get_options();
if ($options['CS_FIELD_1_NAME']=='') {$options['CS_FIELD_1_NAME']=$lang['settings']['custom_field_1'];}
if ($options['CS_FIELD_2_NAME']=='') {$options['CS_FIELD_2_NAME']=$lang['settings']['custom_field_2'];}
if ($options['CS_FIELD_3_NAME']=='') {$options['CS_FIELD_3_NAME']=$lang['settings']['custom_field_3'];}
if ($options['CS_FIELD_4_NAME']=='') {$options['CS_FIELD_4_NAME']=$lang['settings']['custom_field_4'];}
if ($options['CS_FIELD_5_NAME']=='') {$options['CS_FIELD_5_NAME']=$lang['settings']['custom_field_5'];}
if ($options['CS_FIELD_6_NAME']=='') {$options['CS_FIELD_6_NAME']=$lang['settings']['custom_field_6'];}
if ($options['CS_FIELD_7_NAME']=='') {$options['CS_FIELD_7_NAME']=$lang['settings']['custom_field_7'];}
if ($options['CS_FIELD_8_NAME']=='') {$options['CS_FIELD_8_NAME']=$lang['settings']['custom_field_8'];}
if ($options['CS_FIELD_9_NAME']=='') {$options['CS_FIELD_9_NAME']=$lang['settings']['custom_field_9'];}
if ($options['CS_FIELD_10_NAME']=='') {$options['CS_FIELD_10_NAME']=$lang['settings']['custom_field_10'];}

$list_server_groups_videos=mr2array(sql("select * from (select group_id, title, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=1) x where free_space>0 order by title asc"));
foreach ($list_server_groups_videos as $k=>$v)
{
	$list_server_groups_videos[$k]['free_space']=sizeToHumanString($v['free_space'],2);
}
$list_server_groups_albums=mr2array(sql("select * from (select group_id, title, (select min(free_space) from $config[tables_prefix]admin_servers where group_id=$config[tables_prefix]admin_servers_groups.group_id) as free_space from $config[tables_prefix]admin_servers_groups where content_type_id=2) x where free_space>0 order by title asc"));
foreach ($list_server_groups_albums as $k=>$v)
{
	$list_server_groups_albums[$k]['free_space']=sizeToHumanString($v['free_space'],2);
}

$smarty=new mysmarty();
$smarty->assign('list_server_groups_videos',$list_server_groups_videos);
$smarty->assign('list_server_groups_albums',$list_server_groups_albums);
$smarty->assign('personal_data',$personal_data);
$smarty->assign('list_langs',$list_langs);
$smarty->assign('list_skins',$list_skins);
$smarty->assign('list_formats_screenshots_overview',mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1")));
$smarty->assign('list_formats_screenshots_posters',mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3")));
$smarty->assign('list_formats_albums',mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=1")));
$smarty->assign('list_formats_albums_preview',mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id=1 and group_id=2")));
$smarty->assign('list_formats_videos_std',mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=0")));
$smarty->assign('list_formats_videos_premium',mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (1,2) and video_type_id=1")));
$smarty->assign('list_posts_types',mr2array(sql("select * from $config[tables_prefix]posts_types")));

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('options',$options);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if (is_array($rotator_completeness))
{
	$smarty->assign('rotator_completeness',$rotator_completeness);
}
if (is_file("$config[project_path]/admin/tinymce/tinymce.min.js") && is_file("$config[project_path]/admin/js/TinyMCEConfig.js"))
{
	$smarty->assign('tinymce_enabled','1');
}
if ($_REQUEST['page']=='general_settings')
{
	$smarty->assign('page_title',$lang['settings']['system_header']);
} elseif ($_REQUEST['page']=='website_settings')
{
	$smarty->assign('page_title',$lang['settings']['website_header']);
} elseif ($_REQUEST['page']=='memberzone_settings')
{
	$smarty->assign('page_title',$lang['settings']['memberzone_header']);
} elseif ($_REQUEST['page']=='antispam_settings')
{
	$smarty->assign('page_title',$lang['settings']['antispam_header']);
} elseif ($_REQUEST['page']=='stats_settings')
{
	$smarty->assign('page_title',$lang['settings']['stats_header']);
} elseif ($_REQUEST['page']=='customization')
{
	$smarty->assign('page_title',$lang['settings']['customization_header']);
} else {
	$smarty->assign('page_title',$lang['settings']['personal_header']);
}

$smarty->display("layout.tpl");
