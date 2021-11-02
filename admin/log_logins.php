<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$sort_array=array('session_id','user_login','login_date','last_request_date','duration','ip');
$sort_def_field="last_request_date";$sort_def_direction="desc";
$table_name="$config[tables_prefix_multi]log_logins";
$table_key_name="session_id";

if (in_array($_GET['sort_by'],$sort_array)) {$_SESSION['save'][$page_name]['sort_by']=$_GET['sort_by'];}
if ($_SESSION['save'][$page_name]['sort_by']=='') {$_SESSION['save'][$page_name]['sort_by']=$sort_def_field;$_SESSION['save'][$page_name]['sort_direction']=$sort_def_direction;} else {
if (in_array($_GET['sort_direction'],array('desc','asc'))) {$_SESSION['save'][$page_name]['sort_direction']=$_GET['sort_direction'];}
if ($_SESSION['save'][$page_name]['sort_direction']=='') {$_SESSION['save'][$page_name]['sort_direction']='desc';}}

if (isset($_GET['num_on_page'])) {$_SESSION['save'][$page_name]['num_on_page']=intval($_GET['num_on_page']);}
if ($_SESSION['save'][$page_name]['num_on_page']<1) {$_SESSION['save'][$page_name]['num_on_page']=20;}

if (isset($_GET['from'])) {$_SESSION['save'][$page_name]['from']=intval($_GET['from']);}
settype($_SESSION['save'][$page_name]['from'],"integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text']='';
	$_SESSION['save'][$page_name]['se_user_id']='';
	$_SESSION['save'][$page_name]['se_date_from']="";
	$_SESSION['save'][$page_name]['se_date_to']="";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text'])) {$_SESSION['save'][$page_name]['se_text']=$_GET['se_text'];}
	if (isset($_GET['se_user_id'])) {$_SESSION['save'][$page_name]['se_user_id']=intval($_GET['se_user_id']);}
	if (isset($_GET['se_date_from_Day']) && isset($_GET['se_date_from_Month']) && isset($_GET['se_date_from_Year']))
	{
		if (intval($_GET['se_date_from_Day'])>0 && intval($_GET['se_date_from_Month'])>0 && intval($_GET['se_date_from_Year'])>0)
		{
			$_SESSION['save'][$page_name]['se_date_from']=intval($_GET['se_date_from_Year'])."-".intval($_GET['se_date_from_Month'])."-".intval($_GET['se_date_from_Day']);
		} else {
			$_SESSION['save'][$page_name]['se_date_from']="";
		}
	}
	if (isset($_GET['se_date_to_Day']) && isset($_GET['se_date_to_Month']) && isset($_GET['se_date_to_Year']))
	{
		if (intval($_GET['se_date_to_Day'])>0 && intval($_GET['se_date_to_Month'])>0 && intval($_GET['se_date_to_Year'])>0)
		{
			$_SESSION['save'][$page_name]['se_date_to']=intval($_GET['se_date_to_Year'])."-".intval($_GET['se_date_to_Month'])."-".intval($_GET['se_date_to_Day']);
		} else {
			$_SESSION['save'][$page_name]['se_date_to']="";
		}
	}
}

$table_filtered=0;
$where='';
if ($_SESSION['save'][$page_name]['se_text']!='')
{
	$q_ip = ip2int($_SESSION['save'][$page_name]['se_text']);
	if ($q_ip > 0)
	{
		$where.=" and (ip='$q_ip') ";
	} else
	{
		$_SESSION['save'][$page_name]['se_text']='';
	}
}
if ($_SESSION['save'][$page_name]['se_user_id']>0)
{
	$where.=" and user_id=".$_SESSION['save'][$page_name]['se_user_id'];
	$table_filtered=1;
}
if ($_SESSION['save'][$page_name]['se_date_from']<>"")
{
	$where.=" and login_date>='".$_SESSION['save'][$page_name]['se_date_from']."'";
	$table_filtered=1;
}
if ($_SESSION['save'][$page_name]['se_date_to']<>"")
{
	$where.=" and login_date<='".date("Y-m-d H:i",strtotime($_SESSION['save'][$page_name]['se_date_to'])+86399)."'";
	$table_filtered=1;
}

$total_num=mr2number(sql("select count(*) from $table_name where is_failed=0 $where"));
if (($_SESSION['save'][$page_name]['from']>=$total_num || $_SESSION['save'][$page_name]['from']<0) || ($_SESSION['save'][$page_name]['from']>0 && $total_num<=$_SESSION['save'][$page_name]['num_on_page'])) {$_SESSION['save'][$page_name]['from']=0;}
$data=mr2array(sql("select *, (select login from $config[tables_prefix_multi]admin_users where user_id=$table_name.user_id) as user_login, (select is_superadmin from $config[tables_prefix_multi]admin_users where user_id=$table_name.user_id) as is_superadmin from $table_name where is_failed=0 $where order by ".$_SESSION['save'][$page_name]['sort_by']." ".$_SESSION['save'][$page_name]['sort_direction']." limit ".$_SESSION['save'][$page_name]['from'].", ".$_SESSION['save'][$page_name]['num_on_page']));
foreach ($data as $k=>$v)
{
	$data[$k]['ip']=int2ip($data[$k]['ip']);
	$data[$k]['duration_str']=durationToHumanString($data[$k]['duration']);
}

$smarty=new mysmarty();
$smarty->assign('list_users',mr2array(sql("select user_id, login from $config[tables_prefix_multi]admin_users order by login asc")));
$smarty->assign('left_menu','menu_administration.tpl');

$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('table_filtered',$table_filtered);
$smarty->assign('total_num',$total_num);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));
$smarty->assign('nav',get_navigation($total_num,$_SESSION['save'][$page_name]['num_on_page'],$_SESSION['save'][$page_name]['from'],"$page_name?",14));

$smarty->assign('page_title',$lang['settings']['submenu_option_activity_log']);

$smarty->display("layout.tpl");
?>