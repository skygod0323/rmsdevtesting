<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

if (intval($_GET['video_id']) == 0)
{
	echo "Parameters are missing";
	die;
}

$where = '';
if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
{
	$admin_id = intval($_SESSION['userdata']['user_id']);
	$where .= " and admin_user_id=$admin_id ";
}
if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
{
	$where .= " and status_id=0 ";
}
if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
{
	$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
	$where .= " and admin_flag_id>0 and admin_flag_id in ($flags_access_limit)";
}
$data_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where status_id in (0,1) and video_id=? $where", intval($_GET['video_id'])));
if (count($data_video) == 0)
{
	header("Location: error.php?error=permission_denied");
	die;
}

$formats_videos = [];
$formats_videos_temp = mr2array(sql_pr("select * from $config[tables_prefix]formats_videos where status_id in (1,2) order by title asc"));
foreach ($formats_videos_temp as $format)
{
	$formats_videos[$format['postfix']] = $format;
}

$video_id = $data_video['video_id'];
$dir_path = get_dir_by_id($video_id);

$preview_data = [];
$preview_data['load_type_id'] = $data_video['load_type_id'];
$preview_data['embed'] = $data_video['embed'];

$preview_data['flashvars'] = [
		'disable_preview_resize' => "true",
		'skin' => "$config[project_url]/player/skin/youtube.css",
		'hide_controlbar' => "1",
		'hide_style' => "fade",
		'autoplay' => "true",
		'license_code' => "$config[player_license_code]"
];

if (isset($config['content_url_videos_screenshots_admin_panel']))
{
	$preview_data['flashvars']['preview_url'] = "$config[content_url_videos_screenshots_admin_panel]/$dir_path/$video_id/preview.jpg";
} else
{
	$preview_data['flashvars']['preview_url'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/preview.jpg";
}

if ($data_video['load_type_id'] == 1)
{
	$i = 0;
	$timeline_amount = 0;
	$timeline_interval = 0;
	$timeline_cuepoints = false;
	$timeline_directory = '';
	$video_formats = get_video_formats($video_id, $data_video['file_formats']);
	foreach ($video_formats as $format)
	{
		if (in_array(end(explode(".", $format['postfix'])), explode(",", $config['player_allowed_ext'])) && (trim($_GET['postfix']) == '' || trim($_GET['postfix']) == $format['postfix']))
		{
			$format_path = md5($config['cv'] . "$dir_path/$video_id/$video_id{$format['postfix']}") . "/$dir_path/$video_id/$video_id{$format['postfix']}";
			$format_time = time();
			$format_url = "$config[project_url]/get_file/$data_video[server_group_id]/$format_path/?ttl=$format_time&dsc=" . md5("$config[cv]/$format_path/$format_time");
			$format_title = $formats_videos[$format['postfix']]['title'];

			if (intval($format['timeline_screen_amount']) > $timeline_amount && $formats_videos[$format['postfix']]['timeline_directory'])
			{
				$timeline_amount = intval($format['timeline_screen_amount']);
				$timeline_interval = intval($format['timeline_screen_interval']);
				$timeline_directory = $formats_videos[$format['postfix']]['timeline_directory'];
				if (intval($format['timeline_cuepoints']) > 0)
				{
					$timeline_cuepoints = true;
				}
			}

			if ($i == 0)
			{
				$preview_data['flashvars']['video_url'] = $format_url;
				$preview_data['flashvars']['video_url_text'] = $format_title;
			} elseif ($i == 1)
			{
				$preview_data['flashvars']['video_alt_url'] = $format_url;
				$preview_data['flashvars']['video_alt_url_text'] = $format_title;
			} else
			{
				$preview_data['flashvars']["video_alt_url{$i}"] = $format_url;
				$preview_data['flashvars']["video_alt_url{$i}_text"] = $format_title;
			}
			$i++;
		}
	}

	if ($timeline_amount > 0)
	{
		$sizes = mr2array_list(sql("select size from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));
		if (count($sizes) > 0)
		{
			$preview_data['flashvars']['timeline_screens_url'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_directory/$sizes[0]/{time}.jpg";
			$preview_data['flashvars']['timeline_screens_interval'] = $timeline_interval;
			if ($timeline_cuepoints)
			{
				$preview_data['flashvars']['cuepoints'] = "$config[content_url_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_directory/cuepoints.json";
			}
		}
	}
} elseif ($data_video['load_type_id'] == 2)
{
	$preview_data['flashvars']['video_url'] = $data_video['file_url'];
}

$smarty = new mysmarty();
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('preview_data', $preview_data);
$smarty->assign('page_name', $page_name);
$smarty->display(str_replace(".php", ".tpl", $page_name));
