<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$table_name="$config[tables_prefix]feeds_log";
$table_key_name="record_id";

if (isset($_GET['num_on_page'])) {$_SESSION['save'][$page_name]['num_on_page']=intval($_GET['num_on_page']);}
if ($_SESSION['save'][$page_name]['num_on_page']<1) {$_SESSION['save'][$page_name]['num_on_page']=20;}

if (isset($_GET['from'])) {$_SESSION['save'][$page_name]['from']=intval($_GET['from']);}
settype($_SESSION['save'][$page_name]['from'],"integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_feed_id']='';
	$_SESSION['save'][$page_name]['se_text']="";
	$_SESSION['save'][$page_name]['se_show_id']='';
	$_SESSION['save'][$page_name]['se_date_from']="";
	$_SESSION['save'][$page_name]['se_date_to']="";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_feed_id'])) {$_SESSION['save'][$page_name]['se_feed_id']=intval($_GET['se_feed_id']);}
	if (isset($_GET['se_text'])) {$_SESSION['save'][$page_name]['se_text']=$_GET['se_text'];}
	if (isset($_GET['se_show_id'])) {$_SESSION['save'][$page_name]['se_show_id']=$_GET['se_show_id'];}
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

if ($_GET['action']=='change' && intval($_GET['item_id'])>0)
{
	$_POST=mr2array_single(sql_pr("select * from $table_name where $table_key_name=?",intval($_GET['item_id'])));
	if (count($_POST)==0) {header("Location: $page_name");die;}
}

$table_filtered=0;
$where='';
if ($_SESSION['save'][$page_name]['se_text']!='')
{
	$q=sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where.=" and (message_text like '%$q%' or message_details like '%$q%') ";
}
if ($_SESSION['save'][$page_name]['se_date_from']<>"")
{
	$where.=" and added_date>='".$_SESSION['save'][$page_name]['se_date_from']."'";
	$table_filtered=1;
}
if ($_SESSION['save'][$page_name]['se_date_to']<>"")
{
	$where.=" and added_date<='".date("Y-m-d H:i",strtotime($_SESSION['save'][$page_name]['se_date_to'])+86399)."'";
	$table_filtered=1;
}
if ($_SESSION['save'][$page_name]['se_show_id']==1)
{
	$where.=" and message_type=2";
	$table_filtered=1;
} elseif ($_SESSION['save'][$page_name]['se_show_id']==2)
{
	$table_filtered=1;
} else {
	$where.=" and message_type!=0";
}
if ($_SESSION['save'][$page_name]['se_feed_id']>0)
{
	$where.=" and feed_id=".$_SESSION['save'][$page_name]['se_feed_id'];
	$table_filtered=1;
}


if ($where!='') {$where=" where ".substr($where,4);}

$total_num=mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from']>=$total_num || $_SESSION['save'][$page_name]['from']<0) || ($_SESSION['save'][$page_name]['from']>0 && $total_num<=$_SESSION['save'][$page_name]['num_on_page'])) {$_SESSION['save'][$page_name]['from']=0;}
$data=mr2array(sql("select *, (select title from $config[tables_prefix]videos_feeds_import where feed_id=$table_name.feed_id) as feed_title from $table_name $where order by $table_key_name desc limit ".$_SESSION['save'][$page_name]['from'].", ".$_SESSION['save'][$page_name]['num_on_page']));

$smarty=new mysmarty();
$smarty->assign('left_menu','menu_administration.tpl');
$smarty->assign('list_feeds',mr2array(sql("select * from $config[tables_prefix]videos_feeds_import order by title asc")));

$smarty->assign('data',$data);
$smarty->assign('nav',get_navigation($total_num,$_SESSION['save'][$page_name]['num_on_page'],$_SESSION['save'][$page_name]['from'],"$page_name?",14));
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('table_filtered',$table_filtered);
$smarty->assign('total_num',$total_num);
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($_REQUEST['action']=='change')
{
	$smarty->assign('page_title',$lang['settings']['feeds_log_view']);
} else {
	$smarty->assign('page_title',$lang['settings']['submenu_option_feeds_log']);
}

$smarty->display("layout.tpl");
?>