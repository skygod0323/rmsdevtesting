<?php
function post_editShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	$user_id=intval($_SESSION['user_id']);
	$username=trim($_SESSION['username']);
	$is_trusted=intval($_SESSION['is_trusted']);

	if ($user_id<1 && !isset($block_config['allow_anonymous']))
	{
		if ($_POST['mode']=='async')
		{
			async_return_request_status(array(array('error_code'=>'not_logged_in','block'=>'post_edit')));
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
				async_return_request_status(array(array('error_code'=>'forbidden','block'=>'post_edit')));
			} else {
				header("Location: $config[project_url]");die;
			}
		}

		$item_id=intval($_REQUEST[$block_config['var_post_id']]);
		$old_data=mr2array_single(sql_pr("select * from $config[tables_prefix]posts where user_id=$user_id and post_id=?",$item_id));

		foreach ($_POST as $post_field_name=>$post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name]=trim($post_field_value);
			}
		}

		$post_type_id=0;
		$post_type=trim($block_config['post_type']);
		if ($post_type=='')
		{
			$errors['post_type']=1;
			$errors_async[]=array('error_field_name'=>'post_type','error_code'=>'required','block'=>'post_edit');
		} else
		{
			$post_type_id=mr2number(sql_pr("select post_type_id from $config[tables_prefix]posts_types where external_id=?",$post_type));
			if ($post_type_id==0)
			{
				$errors['post_type']=2;
				$errors_async[]=array('error_field_name'=>'post_type','error_code'=>'invalid','block'=>'post_edit');
			}
		}

		if ($_POST['title']=='')
		{
			$errors['title']=1;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'required','block'=>'post_edit');
		} elseif (strlen($_POST['title'])<5)
		{
			$errors['title']=2;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'minimum','error_details'=>array(5),'block'=>'post_edit');
		} elseif (!isset($block_config['duplicates_allowed']) && mr2number(sql_pr("select count(*) from $config[tables_prefix]posts where title=? and post_type_id=? and post_id<>?",$_POST['title'],$post_type_id,intval($_REQUEST[$block_config['var_post_id']])))>0)
		{
			$errors['title']=3;
			$errors_async[]=array('error_field_name'=>'title','error_code'=>'exists','block'=>'post_edit');
		}

		if ($_POST['content']=='')
		{
			$errors['content']=1;
			$errors_async[]=array('error_field_name'=>'content','error_code'=>'required','block'=>'post_edit');
		}

		if (!isset($block_config['optional_description']))
		{
			if ($_POST['description']=='')
			{
				$errors['description']=1;
				$errors_async[]=array('error_field_name'=>'description','error_code'=>'required','block'=>'post_edit');
			}
		}

		if (!isset($block_config['optional_tags']))
		{
			if ($_POST['tags']=='')
			{
				$errors['tags']=1;
				$errors_async[]=array('error_field_name'=>'tags','error_code'=>'required','block'=>'post_edit');
			}
		}

		if (!isset($block_config['optional_categories']))
		{
			if (count($_POST['category_ids'])<1 || (count($_POST['category_ids'])==1 && intval($_POST['category_ids'][0])==0))
			{
				$errors['category_ids']=1;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'required','block'=>'post_edit');
			}
		}

		$max_categories=intval($block_config['max_categories']);
		if ($max_categories==0) {$max_categories=3;}
		if (intval($errors['category_ids'])==0)
		{
			if (count($_POST['category_ids'])>$max_categories)
			{
				$errors['category_ids']=2;
				$errors_async[]=array('error_field_name'=>'category_ids','error_code'=>'maximum','error_details'=>array($max_categories),'block'=>'post_edit');
			}
		}

		$antispam_action = '';
		if ($_POST['action']=='add_new_complete')
		{
			$antispam_action = process_antispam_rules(12, $_POST['title']);
			if ((isset($block_config['use_captcha']) || strpos($antispam_action, 'captcha') !== false) && !$is_trusted)
			{
				$recaptcha_data = null;
				if (is_file("$config[project_path]/admin/data/plugins/recaptcha/enabled.dat") && is_file("$config[project_path]/admin/data/plugins/recaptcha/data.dat"))
				{
					$recaptcha_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/recaptcha/data.dat"));
				}
				if (is_array($recaptcha_data) && $recaptcha_data['site_key'])
				{
					if ($_POST['g-recaptcha-response'] == '')
					{
						$errors['code'] = 1;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'required', 'block' => 'post_edit');
					} elseif (!validate_recaptcha($_POST['g-recaptcha-response'], $recaptcha_data))
					{
						$errors['code'] = 2;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'invalid', 'block' => 'post_edit');
					}
				} else
				{
					if ($_POST['code'] == '')
					{
						$errors['code'] = 1;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'required', 'block' => 'post_edit');
					} elseif ($_POST['code'] <> $_SESSION['security_code_video_edit'] && $_POST['code'] <> $_SESSION['security_code'])
					{
						$errors['code'] = 2;
						$errors_async[] = array('error_field_name' => 'code', 'error_code' => 'invalid', 'block' => 'post_edit');
					}
				}
			}
		}

		$now_date=date("Y-m-d H:i:s");
		if (!is_array($errors))
		{
			if ($_POST['action'] == 'add_new_complete')
			{
				if (strpos($antispam_action, 'error') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam displayed error on post from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));
					async_return_request_status(array(array('error_code' => 'spam', 'block' => 'post_edit')));
				}
				if (strpos($antispam_action, 'delete') !== false)
				{
					sql_insert("insert into $config[tables_prefix]admin_system_log set event_level=2, event_message=?, event_details=?, added_date=?, added_microtime=0", "Antispam deleted post from IP $_SERVER[REMOTE_ADDR]", nvl($_POST['title']), date("Y-m-d H:i:s"));

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
			}

			$_POST['title']=process_blocked_words($_POST['title'],true);
			$_POST['description']=process_blocked_words($_POST['description'],true);
			$_POST['content']=process_blocked_words($_POST['content'],true);
			$_POST['tags']=process_blocked_words($_POST['tags'],false);

			$status_id=1;
			if ($_POST['action']=='add_new_complete')
			{
				$_POST['dir']=get_correct_dir_name($_POST['title']);
				if ($_POST['dir']<>'')
				{
					$temp_dir=$_POST['dir'];
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]posts where dir=?",$temp_dir))==0)
						{
							$_POST['dir']=$temp_dir;break;
						}
						$temp_dir=$_POST['dir'].$i;
					}
				}

				if ((isset($block_config['force_inactive']) || strpos($antispam_action, 'deactivate') !== false) && !$is_trusted)
				{
					$status_id=0;
				}

				if ($user_id<1)
				{
					$user_id=mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
					$username="Anonymous";
				}
				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				$item_id=sql_insert("insert into $config[tables_prefix]posts set post_type_id=?, user_id=?, is_review_needed=$is_review_needed, status_id=$status_id, title=?, dir=?, description=?, content=?, rating=0, rating_amount=1, ip=?, post_date=?, added_date=?",$post_type_id,$user_id,$_POST['title'],$_POST['dir'],$_POST['description'],$_POST['content'],ip2int($_SERVER['REMOTE_ADDR']),$now_date,$now_date);

				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=140, object_id=?, object_type_id=12, added_date=?",$user_id,$username,$item_id,$now_date);
			} else {
				if ($old_data['user_id']<>$user_id || $old_data['is_locked']==1)
				{
					if ($_POST['mode']=='async')
					{
						if ($old_data['is_locked'])
						{
							async_return_request_status(array(array('error_code'=>'post_locked','block'=>'post_edit')));
						} else
						{
							async_return_request_status(array(array('error_code'=>'forbidden','block'=>'post_edit')));
						}
					} else {
						header("Location: $config[project_url]");die;
					}
				}

				$status_id=intval($old_data['status_id']);
				if (isset($block_config['force_inactive_on_edit']) && !$is_trusted)
				{
					$status_id=0;
				}

				$is_review_needed=1;
				if ($is_trusted)
				{
					$is_review_needed=0;
				}

				sql_pr("update $config[tables_prefix]posts set is_review_needed=$is_review_needed, status_id=$status_id, title=?, description=?, content=? where post_id=?",$_POST['title'],$_POST['description'],$_POST['content'],$item_id);

				// track changes
				$update_details='';
				if ($_POST['title']<>$old_data['title']) {$update_details.="title, ";}
				if ($_POST['description']<>$old_data['description']) {$update_details.="description, ";}
				if ($_POST['content']<>$old_data['content']) {$update_details.="content, ";}
				if (intval($status_id)<>intval($old_data['status_id'])) {$update_details.="status_id, ";}
				for ($i=1;$i<=10;$i++)
				{
					if (isset($_POST["custom$i"]) && $_POST["custom$i"]<>$old_data["custom$i"])
					{
						$update_details.="custom$i, ";
					}
				}

				if (strlen($update_details)>0)
				{
					$update_details=substr($update_details,0,strlen($update_details)-2);
				}
				sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=170, object_id=?, object_type_id=12, action_details=?, added_date=?",$user_id,$username,$item_id,$update_details,$now_date);
				// end track changes
			}

			$update_array=array();
			for ($i=1;$i<=10;$i++)
			{
				if (isset($_POST["custom$i"]))
				{
					$update_array["custom$i"]=process_blocked_words($_POST["custom$i"],true);
				}
			}
			if (count($update_array)>0)
			{
				sql_pr("update $config[tables_prefix]posts set ?% where post_id=?",$update_array,$item_id);
			}

			if (intval($status_id) == 1)
			{
				require_once("$config[project_path]/admin/include/functions_admin.php");
				process_activated_posts(array($item_id));
			}

			$list_ids_tags=array_map("intval",mr2array_list(sql_pr("select distinct tag_id from $config[tables_prefix]tags_posts where post_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]tags_posts where post_id=?",$item_id);
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
						sql_pr("insert into $config[tables_prefix]tags_posts set tag_id=?, post_id=?",$tag_id,$item_id);
						$inserted_tags[]=mb_lowercase($tag);
						$list_ids_tags[]=$tag_id;
					}
				}
			}

			$list_ids_categories=array_map("intval",mr2array_list(sql_pr("select distinct category_id from $config[tables_prefix]categories_posts where post_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]categories_posts where post_id=?",$item_id);
			settype($_POST['category_ids'], "array");
			foreach ($_POST['category_ids'] as $category_id)
			{
				if (intval($category_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]categories_posts set category_id=?, post_id=?",$category_id,$item_id);
					$list_ids_categories[]=$category_id;
				}
			}

			$list_ids_models=array_map("intval",mr2array_list(sql_pr("select distinct model_id from $config[tables_prefix]models_posts where post_id=?",$item_id)));
			sql_pr("delete from $config[tables_prefix]models_posts where post_id=?",$item_id);
			settype($_POST['model_ids'], "array");
			foreach ($_POST['model_ids'] as $model_id)
			{
				if (intval($model_id)>0)
				{
					sql_pr("insert into $config[tables_prefix]models_posts set model_id=?, post_id=?",$model_id,$item_id);
					$list_ids_models[]=$model_id;
				}
			}

			require_once("$config[project_path]/admin/include/functions_admin.php");
			update_tags_posts_totals($list_ids_tags);
			update_categories_posts_totals($list_ids_categories);
			update_models_posts_totals($list_ids_models);

			if ($_POST['mode']=='async')
			{
				$post_data=mr2array_single(sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where user_id=$user_id and post_id=?",$item_id));
				$smarty->assign('async_submit_successful','true');
				$smarty->assign('async_object_data',$post_data);
				if ($status_id==0)
				{
					$smarty->assign('force_inactive',1);
				}
				return '';
			} else {
				if ($_POST['action']=='add_new_complete')
				{
					if ($block_config['redirect_on_new_done']<>'')
					{
						$url=process_url($block_config['redirect_on_new_done']);
						header("Location: $url?action=add_new_done");die;
					} else
					{
						header("Location: ?action=add_new_done");die;
					}
				} else {
					if ($block_config['redirect_on_change_done']<>'')
					{
						$url=process_url($block_config['redirect_on_change_done']);
						header("Location: $url?action=change_done");die;
					} else
					{
						header("Location: ?action=change_done&$block_config[var_post_id]=$item_id");die;
					}
				}
			}
		} elseif ($_POST['mode']=='async')
		{
			async_return_request_status($errors_async);
		}
	}

	if (intval($_REQUEST[$block_config['var_post_id']])>0)
	{
		$item_id=intval($_REQUEST[$block_config['var_post_id']]);
		$_POST=mr2array_single(sql_pr("select $database_selectors[posts] from $config[tables_prefix]posts where user_id=$user_id and post_id=?",$item_id));
		if (count($_POST)>0)
		{
			$_POST['block_uid']=$object_id;

			$_POST['tags']=implode(", ",mr2array_list(sql_pr("select (select $database_selectors[generic_selector_tag] from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_posts.tag_id) as tag from $config[tables_prefix]tags_posts where $config[tables_prefix]tags_posts.post_id=? order by id asc",$item_id)));
			$_POST['category_ids']=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_posts where post_id=? order by id asc",$item_id));
			$_POST['model_ids']=mr2array_list(sql_pr("select model_id from $config[tables_prefix]models_posts where post_id=? order by id asc",$item_id));

			$storage[$object_id]['post_title']=$_POST['title'];
			$storage[$object_id]['post_id']=$_POST['post_id'];
		} else {
			return "status_404";
		}
	}

	$list_categories=mr2array(sql("select $database_selectors[categories] from $config[tables_prefix]categories order by title asc"));
	$list_categories_groups=mr2array(sql("select $database_selectors[categories_groups] from $config[tables_prefix]categories_groups order by title asc"));
	$list_categories_ungrouped=array();
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
		} else
		{
			$list_categories_ungrouped[]=$category;
		}
	}

	$smarty->assign('errors',$errors);
	$smarty->assign('list_categories',$list_categories);
	$smarty->assign('list_categories_groups',$list_categories_groups);
	$smarty->assign('list_categories_ungrouped',$list_categories_ungrouped);
	$smarty->assign('list_models',mr2array(sql("select $database_selectors[models] from $config[tables_prefix]models order by title asc")));
	$smarty->assign('list_tags',mr2array(sql("select $database_selectors[tags] from $config[tables_prefix]tags order by tag asc limit 500")));

	if (intval($_POST['post_id'])>0)
	{
		if (isset($block_config['forbid_change']))
		{
			$smarty->assign('change_forbidden',1);
		} elseif ($_POST['is_locked']==1)
		{
			$smarty->assign('change_forbidden',1);
		}
	} else
	{
		$antispam_action = process_antispam_rules(12);
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

	return '';
}

function post_editGetHash($block_config)
{
	return "nocache";
}

function post_editCacheControl($block_config)
{
	return "nocache";
}

function post_editMetaData()
{
	return array(
		// new objects
		array("name"=>"post_type",       "group"=>"new_objects", "type"=>"STRING", "is_required"=>1),
		array("name"=>"allow_anonymous", "group"=>"new_objects", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive",  "group"=>"new_objects", "type"=>"",       "is_required"=>0),

		// editing mode
		array("name"=>"var_post_id",            "group"=>"edit_mode", "type"=>"STRING", "is_required"=>1, "default_value"=>"id"),
		array("name"=>"forbid_change",          "group"=>"edit_mode", "type"=>"",       "is_required"=>0),
		array("name"=>"force_inactive_on_edit", "group"=>"edit_mode", "type"=>"",       "is_required"=>0),

		// validation
		array("name"=>"duplicates_allowed",   "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"optional_description", "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"optional_tags",        "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"optional_categories",  "group"=>"validation", "type"=>"",    "is_required"=>0),
		array("name"=>"max_categories",       "group"=>"validation", "type"=>"INT", "is_required"=>0, "default_value"=>"3"),

		// functionality
		array("name"=>"use_captcha", "group"=>"functionality", "type"=>"", "is_required"=>0),

		// navigation
		array("name"=>"redirect_unknown_user_to", "group"=>"navigation", "type"=>"STRING", "is_required"=>1, "default_value"=>"/?login"),
		array("name"=>"redirect_on_new_done",     "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
		array("name"=>"redirect_on_change_done",  "group"=>"navigation", "type"=>"STRING", "is_required"=>0, "is_deprecated"=>1),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
