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
$table_name="$config[tables_prefix]card_bill_providers";
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

	$internal_id=mr2string(sql("select internal_id from $config[tables_prefix]card_bill_providers where provider_id=$item_id"));

	$cf_pkg_setprice=mr2number(sql("select cf_pkg_setprice from $config[tables_prefix]card_bill_providers where provider_id=$item_id"));
	if ($cf_pkg_setprice==1 && $status_id==1 && $internal_id!='tokens')
	{
		validate_field('empty',$_POST['signature'],$lang['users']['card_bill_config_field_signature']);
	}

	if (($internal_id=='ccbill' || $internal_id=='ccbilldyn') && $status_id==1)
	{
		validate_field('empty',$_POST['account_id'],$lang['users']['card_bill_config_field_datalink_account']);
		validate_field('empty',$_POST['sub_account_id'],$lang['users']['card_bill_config_field_datalink_subaccount']);
		validate_field('empty',$_POST['datalink_username'],$lang['users']['card_bill_config_field_datalink_username']);
		validate_field('empty',$_POST['datalink_password'],$lang['users']['card_bill_config_field_datalink_password']);
	}
	if ($internal_id=='nats' || $internal_id=='natsum')
	{
		if ($_POST['datalink_url']!='' || $_POST['datalink_username']!='' || $_POST['datalink_password']!='')
		{
			validate_field('url',$_POST['datalink_url'],$lang['users']['card_bill_config_field_datalink_url']);
			validate_field('empty',$_POST['datalink_username'],$lang['users']['card_bill_config_field_datalink_username']);
			validate_field('empty',$_POST['datalink_password'],$lang['users']['card_bill_config_field_datalink_password']);
		}
	}

	$data=mr2array_list(sql("select package_id from $config[tables_prefix]card_bill_packages where provider_id=$item_id"));

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
			$errors[]=get_aa_error('bill_config_package_field_required',$lang['users']['card_bill_config_divider_packages']." - ".$lang['users']['card_bill_package_field_title']);
			$has_title_error=1;
		}
		if (trim(intval(trim($_POST["order_$package_id"])))<>trim($_POST["order_$package_id"]) && $has_order_error==0)
		{
			$errors[]=get_aa_error('bill_config_package_field_integer',$lang['users']['card_bill_config_divider_packages']." - ".$lang['users']['card_bill_package_field_order']);
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
					$errors[]=get_aa_error('bill_config_package_field_integer',$lang['users']['card_bill_config_divider_packages']." - ".$lang['users']['card_bill_package_field_order']);
					$has_order_error=1;
				}
				if (intval($_POST["is_active_new$i"])==1)
				{
					$active_packages++;
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
		$is_default=0;
		if ($status_id==1)
		{
			$is_default=intval($_POST['is_default']);
			if ($is_default==0 && mr2number(sql_pr("select count(*) from $table_name where status_id=1 and is_default=1 and $table_key_name<>?",$item_id))==0)
			{
				$is_default=1;
			}
		}

		$options='';
		if (is_array($_POST['options']))
		{
			$options=serialize($_POST['options']);
		}

		sql_pr("update $table_name set status_id=?, is_default=?, postback_reseller_param=?, postback_repost_url=?, postback_ip_protection=?, postback_username=?, postback_password=?, account_id=?, sub_account_id=?, datalink_url=?, datalink_username=?, datalink_password=?, datalink_use_ip=?, signature=?, options=? where $table_key_name=?",
				$status_id,$is_default,nvl($_POST['postback_reseller_param']),nvl($_POST['postback_repost_url']),nvl($_POST['postback_ip_protection']),nvl($_POST['postback_username']),nvl($_POST['postback_password']),nvl($_POST['account_id']),nvl($_POST['sub_account_id']),nvl($_POST['datalink_url']),nvl($_POST['datalink_username']),nvl($_POST['datalink_password']),nvl($_POST['datalink_use_ip']),nvl($_POST['signature']),$options,$item_id);
		if ($is_default==1)
		{
			sql_pr("update $table_name set is_default=0 where $table_key_name<>?",$item_id);
		}

		$data=mr2array_list(sql("select package_id from $config[tables_prefix]card_bill_packages where provider_id=$item_id"));
		foreach ($data as $package_id)
		{
			if (intval($_POST["delete_$package_id"])==1)
			{
				sql_pr("delete from $config[tables_prefix]card_bill_packages where package_id=?",$package_id);
			} else
			{
				$is_default=($_POST['default_package_id']==$package_id ? 1 : 0);
				sql_pr("update $config[tables_prefix]card_bill_packages set title=?, status_id=?, scope_id=?, is_default=?, sort_id=? where package_id=$package_id",$_POST["title_$package_id"],intval($_POST["is_active_$package_id"]),intval($_POST["scope_$package_id"]),$is_default,intval($_POST["order_$package_id"]));
			}
		}

		for ($i=1;$i<9999;$i++)
		{
			if ($_POST["added_new$i"]<>'')
			{
				if ($_POST["title_new$i"]<>'' && intval($_POST["delete_new$i"])<>1)
				{
					$is_default=($_POST['default_package_id']=="new$i" ? 1 : 0);
					$new_package_id=sql_insert("insert into $config[tables_prefix]card_bill_packages set title=?, provider_id=?, sort_id=?, status_id=?, scope_id=?, is_default=?",$_POST["title_new$i"],$item_id,intval($_POST["order_new$i"]),intval($_POST["is_active_new$i"]),intval($_POST["scope_new$i"]),$is_default);

					if ($cf_pkg_setprice==1)
					{
						sql_pr("update $config[tables_prefix]card_bill_packages set external_id=? where package_id=? and external_id=''",md5($new_package_id),$new_package_id);
					}
				}
			} else {
				break;
			}
		}
		check_default_billing_package('card');

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

	$cf_pkg_setprice=mr2number(sql("select cf_pkg_setprice from $config[tables_prefix]card_bill_providers inner join $config[tables_prefix]card_bill_packages on $config[tables_prefix]card_bill_providers.provider_id=$config[tables_prefix]card_bill_packages.provider_id where package_id=$item_id"));

	validate_field('empty',$_POST['title'],$lang['users']['card_bill_package_field_title']);
	validate_field('empty',$_POST['external_id'],$lang['users']['card_bill_package_field_external_id']);
	if (intval($_POST['access_type'])==1)
	{
		$_POST['duration_initial']=0;
		$_POST['duration_rebill']=0;
		$_POST['tokens']=0;
	} elseif (intval($_POST['access_type'])==2)
	{
		validate_field('empty_int',$_POST['duration_initial'],$lang['users']['card_bill_package_field_access_type']);
		if ($_POST['duration_rebill']<>'')
		{
			validate_field('empty_int',$_POST['duration_rebill'],$lang['users']['card_bill_package_field_access_type']);
		}
		$_POST['tokens']=0;
	} elseif (intval($_POST['access_type'])==3)
	{
		validate_field('empty_int',$_POST['tokens'],$lang['users']['card_bill_package_field_access_type']);
		$_POST['duration_initial']=0;
		$_POST['duration_rebill']=0;
	}

	if ($cf_pkg_setprice==1)
	{
		if (validate_field('empty',$_POST['price_initial'],$lang['users']['card_bill_package_field_price']))
		{
			if (!preg_match("|^[0-9\.]+$|is",$_POST['price_initial']))
			{
				$errors[]=get_aa_error('bill_package_price',$lang['users']['card_bill_package_field_price']);
			} elseif ($_POST['duration_rebill']<>'')
			{
				if (validate_field('empty',$_POST['price_rebill'],$lang['users']['card_bill_package_field_price']))
				{
					if (!preg_match("|^[0-9\.]+$|is",$_POST['price_rebill']))
					{
						$errors[]=get_aa_error('bill_package_price',$lang['users']['card_bill_package_field_price']);
					}
				}
			}
		}
	}

	validate_field('url',$_POST['payment_page_url'],$lang['users']['card_bill_package_field_payment_page_url']);
	if ($_POST['oneclick_page_url']!='')
	{
		validate_field('url',$_POST['oneclick_page_url'],$lang['users']['card_bill_package_field_oneclick_page_url']);
	}

	if (!is_array($errors))
	{
		$status_id=intval($_POST["status_id"]);
		if ($status_id<>1) {$status_id=0;}

		sql_pr("update $config[tables_prefix]card_bill_packages set status_id=?, scope_id=?, duration_initial=?, duration_rebill=?, tokens=?, title=?, price_initial=?, price_initial_currency=?, price_rebill=?, price_rebill_currency=?, payment_page_url=?, oneclick_page_url=?, include_countries=?, exclude_countries=?, external_id=? where package_id=?",
		$status_id,intval($_POST["scope_id"]),intval($_POST["duration_initial"]),intval($_POST["duration_rebill"]),intval($_POST["tokens"]),$_POST["title"],nvl($_POST["price_initial"]),nvl($_POST["price_initial_currency"]),nvl($_POST["price_rebill"]),nvl($_POST["price_rebill_currency"]),$_POST["payment_page_url"],nvl($_POST["oneclick_page_url"]),nvl($_POST["include_countries"]),nvl($_POST["exclude_countries"]),$_POST['external_id'],$item_id);

		check_default_billing_package('card');
		$_SESSION['messages'][]=$lang['common']['success_message_modified'];
		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if (@array_search("0",$_REQUEST['row_select'])!==false) {unset($_REQUEST['row_select'][@array_search("0",$_REQUEST['row_select'])]);}
if ($_REQUEST['batch_action']<>'' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select'])>0)
{
	$row_select=implode(",",array_map("intval",$_REQUEST['row_select']));
	if ($_REQUEST['batch_action']=='delete')
	{
		$packages=mr2array_list(sql("select package_id from $config[tables_prefix]card_bill_packages where package_id in ($row_select)"));
		if (count($packages)>0)
		{
			$packages_str=implode(",",$packages);
			$providers=mr2array_list(sql("select distinct $config[tables_prefix]card_bill_providers.provider_id
								  from $config[tables_prefix]card_bill_packages inner join $config[tables_prefix]card_bill_providers on
								  $config[tables_prefix]card_bill_packages.provider_id = $config[tables_prefix]card_bill_providers.provider_id
								  where package_id in ($packages_str) and $config[tables_prefix]card_bill_providers.status_id=1"));
			foreach($providers as $provider_id)
			{
				if (is_array($errors))
				{
					break;
				}
				if (mr2number(sql("select count(*) from $config[tables_prefix]card_bill_packages inner join $config[tables_prefix]card_bill_providers on
									  $config[tables_prefix]card_bill_packages.provider_id = $config[tables_prefix]card_bill_providers.provider_id where
									  $config[tables_prefix]card_bill_providers.provider_id=$provider_id and $config[tables_prefix]card_bill_packages.status_id=1 and
									  $config[tables_prefix]card_bill_packages.package_id not in ($packages_str)"))==0)
				{
					$errors[]=get_aa_error('bill_config_package_removal');
				}
			}

			if (!is_array($errors))
			{
				sql("delete from $config[tables_prefix]card_bill_packages where package_id in ($packages_str)");
			} else {
				return_ajax_errors($errors);
			}
		}
		check_default_billing_package('card');

		$_SESSION['messages'][]=$lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

if ($_GET['action']=='change' && intval($_GET['item_id'])>0)
{
	$item_id=intval($_GET['item_id']);
	$_POST=mr2array_single(sql("select * from $table_name where $table_key_name=$item_id"));
	if (count($_POST)==0) {header("Location: $page_name");die;}

	$_POST['packages']=mr2array(sql("select * from $config[tables_prefix]card_bill_packages where provider_id=$item_id order by sort_id asc"));
	if ($_POST['options']!='')
	{
		$_POST['options']=unserialize($_POST['options']);
	}

	$provider_internal_id = $_POST["internal_id"];
	if ($provider_internal_id && is_file("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php"))
	{
		require_once("$config[project_path]/admin/billings/KvsPaymentProcessor.php");
		require_once("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php");
		$payment_processor = KvsPaymentProcessorFactory::create_instance($provider_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			$_POST["example_payment_url"] = $payment_processor->get_example_payment_url();
			$_POST["example_oneclick_url"] = $payment_processor->get_example_oneclick_url();
		}
	}
}

if ($_GET['action']=='change_package' && intval($_GET['item_id'])>0)
{
	$item_id=intval($_GET['item_id']);
	$_POST=mr2array_single(sql("select * from $config[tables_prefix]card_bill_packages where package_id=$item_id"));
	if (count($_POST)==0) {header("Location: $page_name");die;}

	$_POST['provider']=mr2array_single(sql_pr("select * from $config[tables_prefix]card_bill_providers where provider_id=?",$_POST['provider_id']));

	$provider_internal_id = $_POST["provider"]["internal_id"];
	if ($provider_internal_id && is_file("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php"))
	{
		require_once("$config[project_path]/admin/billings/KvsPaymentProcessor.php");
		require_once("$config[project_path]/admin/billings/$provider_internal_id/$provider_internal_id.php");
		$payment_processor = KvsPaymentProcessorFactory::create_instance($provider_internal_id);
		if ($payment_processor instanceof KvsPaymentProcessor)
		{
			$_POST['provider']["example_payment_url"] = $payment_processor->get_example_payment_url();
			$_POST['provider']["example_oneclick_url"] = $payment_processor->get_example_oneclick_url();
		}
	}
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

$data = mr2array(sql("select * from $table_name order by status_id desc, is_default desc, title asc"));
foreach ($data as $k => $v)
{
	$internal_id = $data[$k]['internal_id'];
	$data[$k]['packages'] = mr2array(sql("select * from $config[tables_prefix]card_bill_packages where provider_id=$v[provider_id] order by sort_id asc"));
	if ($_SESSION['save'][$page_name]['se_text'] != '')
	{
		foreach ($data[$k]['packages'] as $k2 => $v2)
		{
			if (strpos($v2['title'], $_SESSION['save'][$page_name]['se_text']) === false &&
				strpos($v2['external_id'], $_SESSION['save'][$page_name]['se_text']) === false &&
				strpos($v2['payment_page_url'], $_SESSION['save'][$page_name]['se_text']) === false)
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
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('total_num',count($data));
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($_REQUEST['action']=='change')
{
	$smarty->assign('page_title',str_replace("%1%",$_POST['title'],$lang['users']['card_bill_config_edit']));
} elseif ($_REQUEST['action']=='change_package')
{
	$smarty->assign('page_title',str_replace("%2%",$_POST['provider']['title'],str_replace("%1%",$_POST['title'],$lang['users']['card_bill_package_edit'])));
} else {
	$smarty->assign('page_title',$lang['users']['submenu_option_card_billing']);
}

$smarty->display("layout.tpl");
?>