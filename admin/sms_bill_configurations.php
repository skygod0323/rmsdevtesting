<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_admin.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

$sort_array=array();
$sort_def_field="";$sort_def_direction="desc";
$table_name="$config[tables_prefix]sms_bill_providers";
$table_key_name="provider_id";

$errors = null;

if (in_array($_POST['action'],array('change_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id=intval($_POST['item_id']);
	$status_id=intval($_POST['status_id']);
	if ($status_id<>1) {$status_id=0;}

	if ($status_id==1)
	{
		validate_field('empty',$_POST['secret_key'],$lang['users']['sms_bill_config_field_secret_key']);
	}

	$data=mr2array_list(sql("select package_id from $config[tables_prefix]sms_bill_packages where provider_id=$item_id"));

	$has_title_error=0;
	$has_order_error=0;
	$active_packages=0;
	foreach ($data as $package_id)
	{
		if (intval($_POST["delete_$package_id"])==1)
		{
			continue;
		}
		if ($_POST["title_$package_id"]=='' && $has_title_error==0)
		{
			$errors[]=get_aa_error('bill_config_package_field_required',$lang['users']['sms_bill_config_packages_col_title']);
			$has_title_error=1;
		}
		if (trim(intval(trim($_POST["order_$package_id"])))<>trim($_POST["order_$package_id"]) && $has_order_error==0)
		{
			$errors[]=get_aa_error('bill_config_package_field_integer',$lang['users']['sms_bill_config_packages_col_order']);
			$has_order_error=1;
		}
		if (intval($_POST["is_active_$package_id"])==1)
		{
			$active_packages++;
		}
	}
	for ($i=1;$i<9999;$i++)
	{
		if ($_POST["added_new$i"]<>'')
		{
			if ($_POST["title_new$i"]<>'' && intval($_POST["delete_new$i"])<>1)
			{
				if (trim(intval(trim($_POST["order_new$i"])))<>trim($_POST["order_new$i"]) && $has_order_error==0)
				{
					$errors[]=get_aa_error('bill_config_package_field_integer',$lang['users']['sms_bill_config_packages_col_order']);
					$has_order_error=1;
				}
			}
		} else {
			break;
		}
	}

	if (!is_array($errors))
	{
		if ($status_id==1 && $active_packages==0)
		{
			$errors[]=get_aa_error('bill_config_activation');
		}
	}

	if (!is_array($errors))
	{
		sql_pr("update $table_name set status_id=?, secret_key=? where $table_key_name=?",$status_id,$_POST['secret_key'],$item_id);
		if ($status_id==1)
		{
			sql_pr("update $table_name set status_id=0 where $table_key_name<>?",$item_id);
		}

		$data=mr2array_list(sql("select package_id from $config[tables_prefix]sms_bill_packages where provider_id=$item_id"));
		foreach ($data as $package_id)
		{
			if (intval($_POST["delete_$package_id"])==1)
			{
				sql_pr("delete from $config[tables_prefix]sms_bill_packages where package_id=?",$package_id);
			} else
			{
				$is_default=($_POST['default_package_id']==$package_id ? 1 : 0);
				sql_pr("update $config[tables_prefix]sms_bill_packages set title=?, status_id=?, is_default=?, sort_id=? where package_id=$package_id",
				$_POST["title_$package_id"],intval($_POST["is_active_$package_id"]),$is_default,intval($_POST["order_$package_id"]));
			}
		}

		for ($i=1;$i<9999;$i++)
		{
			if ($_POST["added_new$i"]<>'')
			{
				if ($_POST["title_new$i"]<>'' && intval($_POST["delete_new$i"])<>1)
				{
					sql_pr("insert into $config[tables_prefix]sms_bill_packages set title=?, provider_id=?, sort_id=?, status_id=0",
					$_POST["title_new$i"],$item_id,intval($_POST["order_new$i"]));
				}
			} else {
				break;
			}
		}
		check_default_billing_package('sms');

		$_SESSION['messages'][]=$lang['common']['success_message_modified'];
		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'],array('change_package_complete')))
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id=intval($_POST['item_id']);

	validate_field('empty',$_POST['title'],$lang['users']['sms_bill_package_field_title']);
	if (intval($_POST['access_type'])==1)
	{
		$_POST['duration']=0;
		$_POST['tokens']=0;
	} elseif (intval($_POST['access_type'])==2)
	{
		validate_field('empty_int',$_POST['duration'],$lang['users']['sms_bill_package_field_access_type']);
		$_POST['tokens']=0;
	} elseif (intval($_POST['access_type'])==3)
	{
		validate_field('empty_int',$_POST['tokens'],$lang['users']['sms_bill_package_field_access_type']);
		$_POST['duration']=0;
	}

	$countries=array();

	$countries_ids=mr2array_list(sql("select country_id from $config[tables_prefix]sms_bill_countries where package_id=$item_id"));
	$countries_ids_string=trim(implode(",",$countries_ids).",0",",");
	$operators_ids=mr2array_list(sql("select operator_id from $config[tables_prefix]sms_bill_operators where country_id in ($countries_ids_string)"));

	foreach ($countries_ids as $country_id)
	{
		$countries[$country_id]=array();
		$countries[$country_id]['id']=$country_id;
		$countries[$country_id]['title']=$_POST["country_title_$country_id"];
		$countries[$country_id]['code']=$_POST["country_code_$country_id"];
		$countries[$country_id]['order']=$_POST["country_order_$country_id"];
		$countries[$country_id]['is_active']=$_POST["country_is_active_$country_id"];
		$countries[$country_id]['delete']=$_POST["country_delete_$country_id"];
		$countries[$country_id]['operators']=array();
		$countries[$country_id]['active_operators']=0;
	}
	foreach ($operators_ids as $operator_id)
	{
		$operator=array();
		$operator['id']=$operator_id;
		$operator['title']=$_POST["title_$operator_id"];
		$operator['phone']=$_POST["phone_$operator_id"];
		$operator['prefix']=$_POST["prefix_$operator_id"];
		$operator['cost']=$_POST["cost_$operator_id"];
		$operator['order']=$_POST["order_$operator_id"];
		$operator['is_active']=$_POST["is_active_$operator_id"];
		$operator['delete']=$_POST["delete_$operator_id"];
		$countries[$_POST["ref_country_$operator_id"]]['operators']["$operator_id"]=$operator;
	}
	for ($i=1;$i<9999;$i++)
	{
		if ($_POST["added_country_new$i"]<>'')
		{
			$countries["new$i"]=array();
			$countries["new$i"]['title']=$_POST["country_title_new$i"];
			$countries["new$i"]['code']=$_POST["country_code_new$i"];
			$countries["new$i"]['order']=$_POST["country_order_new$i"];
			$countries["new$i"]['is_active']=$_POST["country_is_active_new$i"];
			$countries["new$i"]['delete']=$_POST["country_delete_new$i"];
			$countries["new$i"]['operators']=array();
			$countries["new$i"]['active_operators']=0;
		} else {
			break;
		}
	}
	for ($i=1;$i<9999;$i++)
	{
		if ($_POST["ref_country_new$i"]<>'')
		{
			$operator=array();
			$operator['title']=$_POST["title_new$i"];
			$operator['phone']=$_POST["phone_new$i"];
			$operator['prefix']=$_POST["prefix_new$i"];
			$operator['cost']=$_POST["cost_new$i"];
			$operator['order']=$_POST["order_new$i"];
			$operator['is_active']=$_POST["is_active_new$i"];
			$operator['delete']=$_POST["delete_new$i"];
			$countries[$_POST["ref_country_new$i"]]['operators']["new$i"]=$operator;
		} else {
			break;
		}
	}

	$has_title_error=0;
	$has_country_code_error=0;
	$has_phone_error=0;
	$has_prefix_error=0;
	$has_cost_error=0;
	$has_order_error=0;

	foreach ($countries as $country)
	{
		if (intval($country['delete'])==1)
		{
			continue;
		}
		if ($country['title']=='' && isset($country['id']) && $has_title_error==0)
		{
			$errors[]=get_aa_error('bill_package_sub_field_required',$lang['users']['sms_bill_package_countries_col_title']);
			$has_title_error=1;
		} else if ($country['title']=='')
		{
			continue;
		}
		if ($country['code']=='' && $has_country_code_error==0)
		{
			$errors[]=get_aa_error('bill_package_sub_field_required','Country code');
			$has_country_code_error=1;
		}
		if (trim(intval(trim($country['order'])))<>trim($country['order']) && $has_order_error==0)
		{
			$errors[]=get_aa_error('bill_package_sub_field_integer',$lang['users']['sms_bill_package_countries_col_order']);
			$has_order_error=1;
		}

		foreach ($country['operators'] as $operator)
		{
			if (intval($operator['delete'])==1 || $operator['title']=='' || intval($country['delete'])==1 || $country['title']=='')
			{
				continue;
			}
			if ($operator['title']=='' && isset($operator['id']) && $has_title_error==0)
			{
				$errors[]=get_aa_error('bill_package_sub_field_required',$lang['users']['sms_bill_package_countries_col_title']);
				$has_title_error=1;
			} else if ($operator['title']=='')
			{
				continue;
			}
			if ($operator['phone']=='' && $has_phone_error==0)
			{
				$errors[]=get_aa_error('bill_package_sub_field_required',$lang['users']['sms_bill_package_countries_col_number']);
				$has_phone_error=1;
			}
			if ($operator['cost']=='' && $has_cost_error==0)
			{
				$errors[]=get_aa_error('bill_package_sub_field_required',$lang['users']['sms_bill_package_countries_col_subscriber_cost']);
				$has_cost_error=1;
			}
			if (trim(intval(trim($operator['order'])))<>trim($operator['order']) && $has_order_error==0)
			{
				$errors[]=get_aa_error('bill_package_sub_field_integer',$lang['users']['sms_bill_package_countries_col_order']);
				$has_order_error=1;
			}
		}
	}

	$line_error=$has_title_error+$has_country_code_error+$has_phone_error+$has_prefix_error+$has_cost_error+$has_order_error;

	if ($line_error==0)
	{
		$status_id=intval($_POST["status_id"]);
		if ($status_id<>1) {$status_id=0;}

		$active_countries=0;
		foreach ($countries as $country)
		{
			if (intval($country['delete'])==1 || $country['title']=='') {continue;}
			$active_operators=0;
			foreach ($country['operators'] as $operator)
			{
				if (intval($operator['delete'])==1 || $operator['title']=='') {continue;}
				if (intval($operator['is_active'])==1) {$active_operators++;}
			}
			if (intval($country['delete'])==1) {continue;}
			if (intval($country['is_active'])==1)
			{
				if ($active_operators==0)
				{
					$errors[]=get_aa_error('bill_package_sub_activation',$country['title']);
				} else {
					$active_countries++;
				}
			}
		}
		if ($status_id==1)
		{
			if ($active_countries==0)
			{
				$errors[]=get_aa_error('bill_package_activation');
			}
		}
	}

	if (!is_array($errors))
	{
		$status_id=intval($_POST["status_id"]);
		if ($status_id<>1) {$status_id=0;}

		sql_pr("update $config[tables_prefix]sms_bill_packages set status_id=?, title=?, external_id=?, duration=?, tokens=? where package_id=?",$status_id,$_POST["title"],$_POST["external_id"],intval($_POST["duration"]),intval($_POST["tokens"]),$item_id);

		foreach ($countries as $country)
		{
			$country_id=$country['id'];
			if (isset($country_id))
			{
				if (intval($country['delete'])==1)
				{
					sql_pr("delete from $config[tables_prefix]sms_bill_operators where country_id=?",$country_id);
					sql_pr("delete from $config[tables_prefix]sms_bill_countries where country_id=?",$country_id);
					continue;
				}
				sql_pr("update $config[tables_prefix]sms_bill_countries set status_id=?, sort_id=?, country_code=?, title=? where country_id=?",intval($country['is_active']),intval($country['order']),$country['code'],$country['title'],$country_id);
			} else {
				if (intval($country['delete'])==1 || $country['title']=='') {continue;}
				$country_id=sql_insert("insert into $config[tables_prefix]sms_bill_countries set package_id=?, status_id=?, sort_id=?, country_code=?, title=?",$item_id,intval($country['is_active']),intval($country['order']),$country['code'],$country['title']);
			}

			foreach ($country['operators'] as $operator)
			{
				$operator_id=$operator['id'];
				if (isset($operator_id))
				{
					if (intval($operator['delete'])==1)
					{
						sql_pr("delete from $config[tables_prefix]sms_bill_operators where operator_id=?",$operator_id);
						continue;
					}
					sql_pr("update $config[tables_prefix]sms_bill_operators set title=?, status_id=?, sort_id=?, phone=?, prefix=?, cost=? where operator_id=?",$operator['title'],intval($operator['is_active']),intval($operator['order']),$operator['phone'],nvl($operator['prefix']),$operator['cost'],$operator_id);
				} else {
					if (intval($operator['delete'])==1 || $operator['title']=='') {continue;}
					$operator_id=sql_insert("insert into $config[tables_prefix]sms_bill_operators set title=?, country_id=?, status_id=?, sort_id=?, phone=?, prefix=?, cost=?",$operator['title'],$country_id,intval($operator['is_active']),intval($operator['order']),$operator['phone'],nvl($operator['prefix']),$operator['cost']);
				}
			}
		}
		check_default_billing_package('sms');

		$_SESSION['messages'][]=$lang['common']['success_message_modified'];
		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if (@array_search("0",$_REQUEST['row_select'])!==false) {unset($_REQUEST['row_select'][@array_search("0",$_REQUEST['row_select'])]);}
if ($_REQUEST['batch_action']<>'' && count($_REQUEST['row_select'])>0)
{
	$row_select=implode(",",array_map("intval",$_REQUEST['row_select']));
	if ($_REQUEST['batch_action']=='delete')
	{
		$packages=mr2array_list(sql("select package_id from $config[tables_prefix]sms_bill_packages where package_id in ($row_select)"));
		if (count($packages)>0)
		{
			$packages_str=implode(",",$packages);

			if (mr2number(sql("select count(*) from $config[tables_prefix]sms_bill_packages inner join $config[tables_prefix]sms_bill_providers on
								  $config[tables_prefix]sms_bill_packages.provider_id = $config[tables_prefix]sms_bill_providers.provider_id where
								  $config[tables_prefix]sms_bill_providers.status_id=1 and $config[tables_prefix]sms_bill_packages.package_id in ($packages_str)"))>0)
			{
				if (mr2number(sql("select count(*) from $config[tables_prefix]sms_bill_packages inner join $config[tables_prefix]sms_bill_providers on
									  $config[tables_prefix]sms_bill_packages.provider_id = $config[tables_prefix]sms_bill_providers.provider_id where
									  $config[tables_prefix]sms_bill_providers.status_id=1 and $config[tables_prefix]sms_bill_packages.status_id=1 and
									  $config[tables_prefix]sms_bill_packages.package_id not in ($packages_str)"))==0)
				{
					$errors[]=get_aa_error('bill_config_package_removal');
				}
			}

			if (!is_array($errors))
			{
				$countries=mr2array_list(sql("select country_id from $config[tables_prefix]sms_bill_countries where package_id in ($packages_str)"));
				if (count($countries)>0)
				{
					$countries_str=implode(",",$countries);
					sql("delete from $config[tables_prefix]sms_bill_operators where country_id in ($countries_str)");
					sql("delete from $config[tables_prefix]sms_bill_countries where country_id in ($countries_str)");
				}
				sql("delete from $config[tables_prefix]sms_bill_packages where package_id in ($packages_str)");
			} else {
				return_ajax_errors($errors);
			}
		}
		check_default_billing_package('sms');

		$_SESSION['messages'][]=$lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

if ($_GET['action']=='change' && intval($_GET['item_id'])>0)
{
	$item_id=intval($_GET['item_id']);
	$_POST=mr2array_single(sql("select * from $table_name where provider_id=$item_id"));
	if (count($_POST)==0) {header("Location: $page_name");die;}
	$_POST['packages']=mr2array(sql("select *, (select count(*) from $config[tables_prefix]sms_bill_countries where package_id=$config[tables_prefix]sms_bill_packages.package_id and status_id=1) as countries_amount from $config[tables_prefix]sms_bill_packages where provider_id=$_POST[provider_id] order by sort_id asc"));
}

if ($_GET['action']=='change_package' && intval($_GET['item_id'])>0)
{
	$item_id=intval($_GET['item_id']);
	$_POST=mr2array_single(sql("select * from $config[tables_prefix]sms_bill_packages where package_id=$item_id"));
	$_POST['provider']=mr2array_single(sql_pr("select * from $config[tables_prefix]sms_bill_providers where provider_id=?",$_POST['provider_id']));
	$_POST['countries']=mr2array(sql("select * from $config[tables_prefix]sms_bill_countries where package_id=$item_id order by sort_id asc"));
	foreach ($_POST['countries'] as $k=>$v)
	{
		$_POST['countries'][$k]['operators']=mr2array(sql("select * from $config[tables_prefix]sms_bill_operators where country_id=$v[country_id] order by sort_id asc"));
	}
	if (count($_POST)==0) {header("Location: $page_name");die;}
}

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
}

$data = mr2array(sql("select * from $table_name order by status_id desc, $table_key_name asc"));
foreach ($data as $k => $v)
{
	$internal_id = $data[$k]['internal_id'];
	$data[$k]['packages'] = mr2array(sql("select *, (select count(*) from $config[tables_prefix]sms_bill_countries where package_id=$config[tables_prefix]sms_bill_packages.package_id and status_id=1) as countries_amount from $config[tables_prefix]sms_bill_packages where provider_id=$v[provider_id] order by sort_id asc"));
	if ($_SESSION['save'][$page_name]['se_text'] != '')
	{
		foreach ($data[$k]['packages'] as $k2 => $v2)
		{
			if (strpos($v2['title'], $_SESSION['save'][$page_name]['se_text']) === false &&
				strpos($v2['external_id'], $_SESSION['save'][$page_name]['se_text']) === false)
			{
				unset($data[$k]['packages'][$k2]);
			}
		}
	}
}


$smarty=new mysmarty();
$smarty->assign('left_menu','menu_users.tpl');

if (in_array($_REQUEST['action'],array('change','change_package'))) {$smarty->assign('supports_popups',1);}

$smarty->assign('data',$data);
$smarty->assign('list_countries',mr2array(sql_pr("select title, country_code from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc",$lang['system']['language_code'])));
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('total_num',count($data));
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($_REQUEST['action']=='change')
{
	$smarty->assign('page_title',str_replace("%1%",$_POST['title'],$lang['users']['sms_bill_config_edit']));
} elseif ($_REQUEST['action']=='change_package')
{
	$smarty->assign('page_title',str_replace("%2%",$_POST['processor_title'],str_replace("%1%",$_POST['title'],$lang['users']['sms_bill_package_edit'])));
} else {
	$smarty->assign('page_title',$lang['users']['submenu_option_sms_billing']);
}

$smarty->display("layout.tpl");
?>