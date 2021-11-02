<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

if (intval($_GET['album_id'])==0)
{
	echo "Parameters are missing";die;
}

unset($where);
if ($_SESSION['userdata']['is_access_to_own_content']==1)
{
	$admin_id=intval($_SESSION['userdata']['user_id']);
	$where.=" and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content']==1)
{
	$where.=" and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}
$data_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where status_id in (0,1) and album_id=? $where",intval($_GET['album_id'])));
if (count($data_album)==0)
{
	header("Location: error.php?error=permission_denied");die;
}

$formats_albums=mr2array(sql_pr("select * from $config[tables_prefix]formats_albums where group_id=1"));
foreach ($formats_albums as $format)
{
	if (!isset($preview_format_size))
	{
		$preview_format_size=explode('x',$format['size']);
		continue;
	}
	$format_size=explode('x',$format['size']);
	if ($format_size[0]<$preview_format_size[0])
	{
		$preview_format_size=$format_size;
	}
}
$preview_format_size="$preview_format_size[0]x$preview_format_size[1]";

$album_id=$data_album['album_id'];
$dir_path=get_dir_by_id($album_id);

$preview_data=array();
$images=mr2array(sql_pr("select image_id, image_formats from $config[tables_prefix]albums_images where album_id=? order by image_id",$album_id));
$image_bounds=array(0,0);
foreach ($images as $image)
{
	$file_path="main/$preview_format_size/$dir_path/$album_id/$image[image_id].jpg";
	$hash=md5($config['cv'].$file_path);
	$preview_data[]="$config[project_url]/get_image/$data_album[server_group_id]/$hash/$file_path/";
	$image_formats=get_image_formats($album_id,$image['image_formats']);
	if (isset($image_formats[$preview_format_size]))
	{
		if ($image_formats[$preview_format_size]['dimensions'][0]>$image_bounds[0])
		{
			$image_bounds[0]=$image_formats[$preview_format_size]['dimensions'][0];
		}
		if ($image_formats[$preview_format_size]['dimensions'][1]>$image_bounds[1])
		{
			$image_bounds[1]=$image_formats[$preview_format_size]['dimensions'][1];
		}
	}
}

$smarty=new mysmarty();
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('preview_data',$preview_data);
$smarty->assign('image_bounds',$image_bounds);
$smarty->assign('page_name',$page_name);
$smarty->display(str_replace(".php",".tpl",$page_name));
?>