<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();
for ($i = 1; $i <= 10; $i++)
{
	if ($options["CS_FIELD_{$i}_NAME"] == '')
	{
		$options["CS_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
	if ($options["CS_FILE_FIELD_{$i}_NAME"] == '')
	{
		$options["CS_FILE_FIELD_{$i}_NAME"] = $lang['settings']["custom_file_field_{$i}"];
	}
}

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? and is_system=0 order by title asc", $lang['system']['language_code']));
foreach ($list_countries as $k => $country)
{
	$list_countries[$country['country_code']] = $country['title'];
	unset($list_countries[$k]);
}

$table_name = "$config[tables_prefix]formats_videos";
$table_key_name = "format_video_id";

$errors = null;

// =====================================================================================================================
// watermark
// =====================================================================================================================

if ($_REQUEST['action'] == 'download_watermark')
{
	header('Content-Type: image/png');
	$format_id = intval($_REQUEST['id']);
	$watermark_file = "$config[project_path]/admin/data/other/watermark_video_{$format_id}.png";
	if (is_file($watermark_file))
	{
		header('Content-Length: ' . filesize($watermark_file));
		readfile($watermark_file);
	}
	die;
}

if ($_REQUEST['action'] == 'download_watermark2')
{
	header('Content-Type: image/png');
	$format_id = intval($_REQUEST['id']);
	$watermark2_file = "$config[project_path]/admin/data/other/watermark2_video_{$format_id}.png";
	if (is_file($watermark2_file))
	{
		header('Content-Length: ' . filesize($watermark2_file));
		readfile($watermark2_file);
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if ($_POST['action'] == 'change_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
	{
		if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
		{
			$errors[] = get_aa_error('format_video_changes_not_allowed');
			return_ajax_errors($errors);
		}
	}

	$item_id = intval($_POST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['settings']['format_video_field_title'], array('field_name_in_base' => 'title'));
	if (validate_field('uniq', $_POST['postfix'], $lang['settings']['format_video_field_postfix'], array('field_name_in_base' => 'postfix')))
	{
		if (!preg_match("|^[a-z0-9_\.]+$|s", $_POST['postfix']))
		{
			$errors[] = get_aa_error('format_video_postfix_symbols', $lang['settings']['format_video_field_postfix']);
		} else
		{
			if (is_numeric($_POST['postfix'][0]))
			{
				$errors[] = get_aa_error('format_video_postfix_start_with_number', $lang['settings']['format_video_field_postfix']);
			} else
			{
				$temp = explode('.', $_POST['postfix']);
				if (count($temp) != 2 || strlen($temp[1]) == 0)
				{
					$errors[] = get_aa_error('format_video_postfix_dot', $lang['settings']['format_video_field_postfix']);
				} else
				{
					$allowed_formats = explode(',', $config['video_allowed_ext']);
					$is_allowed = 0;
					foreach ($allowed_formats as $format)
					{
						if (strlen($format) > 0 && strpos($_POST['postfix'], ".$format") === strlen($_POST['postfix']) - strlen(".$format"))
						{
							$is_allowed = 1;
							break;
						}
					}
					if ($is_allowed == 0)
					{
						$errors[] = get_aa_error('format_video_postfix_not_allowed', $lang['settings']['format_video_field_postfix']);
					}
				}
			}
		}
	}

	if (intval($_POST['resize_option']) == 1)
	{
		validate_field('size', $_POST['size'], $lang['settings']['format_video_field_size']);
	}
	validate_field('empty', $_POST['ffmpeg_options'], $lang['settings']['format_video_field_ffmpeg_options']);

	validate_field('file', 'watermark_image', $lang['settings']['format_video_field_watermark_image'], array('is_image' => '1', 'allowed_ext' => 'png'));
	if (in_array(intval($_POST['watermark_position_id']), array(5, 6, 7)))
	{
		if (validate_field('empty_int', $_POST['watermark_scrolling_duration'], $lang['settings']['format_video_field_watermark_position']))
		{
			validate_field('empty', $_POST['watermark_scrolling_times'], $lang['settings']['format_video_field_watermark_position']);
		}
	}
	if ($_POST['watermark_max_width'] <> '')
	{
		validate_field('empty_int', $_POST['watermark_max_width'], $lang['settings']['format_video_field_watermark_max_width']);
	}
	if ($_POST['watermark_max_width_vertical'] <> '')
	{
		validate_field('empty_int', $_POST['watermark_max_width_vertical'], $lang['settings']['format_video_field_watermark_max_width']);
	}

	validate_field('file', 'watermark2_image', $lang['settings']['format_video_field_watermark2_image'], array('is_image' => '1', 'allowed_ext' => 'png'));
	if (in_array(intval($_POST['watermark2_position_id']), array(5, 6, 7)))
	{
		if (validate_field('empty_int', $_POST['watermark2_scrolling_duration'], $lang['settings']['format_video_field_watermark2_position']))
		{
			validate_field('empty', $_POST['watermark2_scrolling_times'], $lang['settings']['format_video_field_watermark2_position']);
		}
	}
	if ($_POST['watermark2_max_height'] <> '')
	{
		validate_field('empty_int', $_POST['watermark2_max_height'], $lang['settings']['format_video_field_watermark2_max_height']);
	}
	if ($_POST['watermark2_max_height_vertical'] <> '')
	{
		validate_field('empty_int', $_POST['watermark2_max_height_vertical'], $lang['settings']['format_video_field_watermark2_max_height']);
	}

	$limit_duration_ok = 1;
	if ($_POST['limit_total_duration'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_duration'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_min_duration_sec'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_min_duration_sec'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_max_duration_sec'] <> '' && $limit_duration_ok == 1)
	{
		$limit_duration_ok = validate_field('empty_int', $_POST['limit_total_max_duration_sec'], $lang['settings']['format_video_field_limit_duration']);
	}
	if ($_POST['limit_total_min_duration_sec'] <> '' && $_POST['limit_total_max_duration_sec'] <> '' && $limit_duration_ok == 1 && intval($_POST['limit_total_min_duration_sec']) >= intval($_POST['limit_total_max_duration_sec']))
	{
		$errors[] = get_aa_error('invalid_int_range', $lang['settings']['format_video_field_limit_duration']);
	}

	if ($_POST['limit_offset_start'] <> '')
	{
		validate_field('empty_int', $_POST['limit_offset_start'], $lang['settings']['format_video_field_offset_start']);
	}
	if ($_POST['limit_offset_end'] <> '')
	{
		validate_field('empty_int', $_POST['limit_offset_end'], $lang['settings']['format_video_field_offset_end']);
	}

	if (validate_field('empty_int', $_POST['limit_number_parts'], $lang['settings']['format_video_field_number_of_parts']))
	{
		if ($limit_duration_ok == 1 && intval($_POST['limit_number_parts']) > 1)
		{
			if (intval($_POST['limit_total_duration']) == 0)
			{
				$errors[] = get_aa_error('format_video_parts_no_duration_limit', $lang['settings']['format_video_field_limit_duration']);
			} elseif (intval($_POST['limit_total_duration_unit_id']) == 0 && intval($_POST['limit_total_duration']) / intval($_POST['limit_number_parts']) < 1)
			{
				$errors[] = get_aa_error('format_video_parts_too_small', $lang['settings']['format_video_field_limit_duration']);
			} elseif (intval($_POST['limit_number_parts_crossfade']) > 0)
			{
				$part_projected_length = floor(intval($_POST['limit_total_duration']) / intval($_POST['limit_number_parts']));
				if ($part_projected_length == 1 || intval($_POST['limit_number_parts_crossfade']) > $part_projected_length / 2)
				{
					$errors[] = get_aa_error('format_video_parts_crossfade_too_big', $lang['settings']['format_video_field_number_of_parts'], $part_projected_length);
				}
			}
		}
	}

	if (intval($_POST['limit_speed_option']) > 0)
	{
		if (intval($_POST['limit_speed_option']) == 1)
		{
			validate_field('empty_int', $_POST['limit_speed_value'], $lang['settings']['format_video_field_limit_speed_global']);
		} elseif (intval($_POST['limit_speed_option']) == 2)
		{
			validate_field('empty_float', $_POST['limit_speed_value'], $lang['settings']['format_video_field_limit_speed_global']);
		}
	}

	if (intval($_POST['is_timeline_enabled']) == 1)
	{
		if (intval($_POST['timeline_option']) == 1)
		{
			validate_field('empty_int', $_POST['timeline_amount'], $lang['settings']['format_video_field_timeline_screenshots_option']);
		} else
		{
			validate_field('empty_int', $_POST['timeline_interval'], $lang['settings']['format_video_field_timeline_screenshots_option']);
		}
		if (validate_field('empty', $_POST['timeline_directory'], $lang['settings']['format_video_field_timeline_screenshots_directory']))
		{
			if (!preg_match("|^[a-z0-9_]+$|s", $_POST['timeline_directory']))
			{
				$errors[] = get_aa_error('format_video_timeline_folder_symbols', $lang['settings']['format_video_field_timeline_screenshots_directory']);
			} else
			{
				validate_field('uniq', $_POST['timeline_directory'], $lang['settings']['format_video_field_timeline_screenshots_directory'], array('field_name_in_base' => 'timeline_directory'));
			}
		}
		$old_timeline_enabled = mr2number(sql_pr("select is_timeline_enabled from $table_name where $table_key_name=?", $item_id));
	}

	if (!is_array($errors))
	{
		if (intval($_POST['limit_speed_option']) == 0)
		{
			$_POST['limit_speed_value'] = 0;
		}
		$_POST['limit_speed_guests_option'] = $_POST['limit_speed_option'];
		$_POST['limit_speed_guests_value'] = $_POST['limit_speed_value'];
		$_POST['limit_speed_standard_option'] = $_POST['limit_speed_option'];
		$_POST['limit_speed_standard_value'] = $_POST['limit_speed_value'];
		$_POST['limit_speed_premium_option'] = $_POST['limit_speed_option'];
		$_POST['limit_speed_premium_value'] = $_POST['limit_speed_value'];
		$_POST['limit_speed_embed_option'] = $_POST['limit_speed_option'];
		$_POST['limit_speed_embed_value'] = $_POST['limit_speed_value'];

		if (!is_array($_POST['limit_speed_countries']))
		{
			$_POST['limit_speed_countries'] = array();
		}

		sql_pr("update $table_name set title=?, size=?, resize_option=?, resize_option2=?, ffmpeg_options=?, watermark_position_id=?, watermark_scrolling_direction=?, watermark_scrolling_duration=?, watermark_scrolling_times=?, watermark_max_width=?, watermark_max_width_vertical=?, customize_watermark_id=?, watermark2_position_id=?, watermark2_scrolling_direction=?, watermark2_scrolling_duration=?, watermark2_scrolling_times=?, watermark2_max_height=?, watermark2_max_height_vertical=?, customize_watermark2_id=?, is_hotlink_protection_disabled=?, is_download_enabled=?,
				limit_total_duration=?, limit_total_duration_unit_id=?, limit_total_min_duration_sec=?, limit_total_max_duration_sec=?, limit_number_parts=?, limit_number_parts_crossfade=?, limit_offset_start=?, limit_offset_start_unit_id=?, limit_offset_end=?, limit_offset_end_unit_id=?, limit_is_last_part_from_end=?, customize_duration_id=?, customize_offset_start_id=?, customize_offset_end_id=?,
				limit_speed_option=?, limit_speed_value=?, limit_speed_guests_option=?, limit_speed_guests_value=?, limit_speed_standard_option=?, limit_speed_standard_value=?, limit_speed_premium_option=?, limit_speed_premium_value=?, limit_speed_embed_option=?, limit_speed_embed_value=?, limit_speed_countries=?, is_timeline_enabled=?, timeline_option=?, timeline_amount=?, timeline_interval=?, timeline_directory=? where $table_key_name=?",
			$_POST['title'], $_POST['size'], intval($_POST['resize_option']), intval($_POST['resize_option2']), $_POST['ffmpeg_options'], intval($_POST['watermark_position_id']), intval($_POST['watermark_scrolling_direction']), intval($_POST['watermark_scrolling_duration']), trim($_POST['watermark_scrolling_times']), intval($_POST['watermark_max_width']), intval($_POST['watermark_max_width_vertical']), intval($_POST['customize_watermark_id']), intval($_POST['watermark2_position_id']), intval($_POST['watermark2_scrolling_direction']), intval($_POST['watermark2_scrolling_duration']), trim($_POST['watermark2_scrolling_times']), intval($_POST['watermark2_max_height']), intval($_POST['watermark2_max_height_vertical']), intval($_POST['customize_watermark2_id']), intval($_POST['is_hotlink_protection_disabled']), intval($_POST['is_download_enabled']),
			intval($_POST['limit_total_duration']), intval($_POST['limit_total_duration_unit_id']), intval($_POST['limit_total_min_duration_sec']), intval($_POST['limit_total_max_duration_sec']), intval($_POST['limit_number_parts']), intval($_POST['limit_number_parts_crossfade']), intval($_POST['limit_offset_start']), intval($_POST['limit_offset_start_unit_id']), intval($_POST['limit_offset_end']), intval($_POST['limit_offset_end_unit_id']), intval($_POST['limit_is_last_part_from_end']), intval($_POST['customize_duration_id']), intval($_POST['customize_offset_start_id']), intval($_POST['customize_offset_end_id']),
			intval($_POST['limit_speed_option']), floatval($_POST['limit_speed_value']), intval($_POST['limit_speed_guests_option']), floatval($_POST['limit_speed_guests_value']), intval($_POST['limit_speed_standard_option']), floatval($_POST['limit_speed_standard_value']), intval($_POST['limit_speed_premium_option']), floatval($_POST['limit_speed_premium_value']), intval($_POST['limit_speed_embed_option']), floatval($_POST['limit_speed_embed_value']), implode(',', array_map('trim', $_POST['limit_speed_countries'])), intval($_POST['is_timeline_enabled']), intval($_POST['timeline_option']), intval($_POST['timeline_amount']), intval($_POST['timeline_interval']), $_POST['timeline_directory'], $item_id
		);
		$old_postfix = mr2string(sql_pr("select postfix from $table_name where $table_key_name=?", $item_id));
		if (mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like '%||$old_postfix|%'")) == 0)
		{
			sql_pr("update $table_name set postfix=? where $table_key_name=?", $_POST['postfix'], $item_id);
		}
		if ($_POST['watermark_image_hash'] <> '')
		{
			transfer_uploaded_file('watermark_image', "$config[project_path]/admin/data/other/watermark_video_{$item_id}.png");
		} elseif ($_POST['watermark_image'] == '')
		{
			if (is_file("$config[project_path]/admin/data/other/watermark_video_{$item_id}.png"))
			{
				unlink("$config[project_path]/admin/data/other/watermark_video_{$item_id}.png");
			}
		}
		if ($_POST['watermark2_image_hash'] <> '')
		{
			transfer_uploaded_file('watermark2_image', "$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png");
		} elseif ($_POST['watermark2_image'] == '')
		{
			if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png"))
			{
				unlink("$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png");
			}
		}

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];

		if (intval($_POST['is_timeline_enabled']) == 1 && $old_timeline_enabled == 0)
		{
			$videos = mr2array(sql("select video_id from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1"));
			foreach ($videos as $video)
			{
				$background_task = array();
				$background_task['format_postfix'] = $_POST['postfix'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=8, video_id=?, data=?, added_date=?", $video['video_id'], serialize($background_task), date("Y-m-d H:i:s"));
			}
		}

		update_format_data();
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// view item
// =====================================================================================================================

$_POST = mr2array_single(sql_pr("select * from $table_name order by $table_key_name limit 1"));
$item_id = $_POST['format_video_id'];
$_POST['videos_count'] = mr2number(sql("select count(*) from $config[tables_prefix]videos where status_id in (0,1) and load_type_id=1 and file_formats like '%||$_POST[postfix]|%'"));

if ($_POST['watermark_scrolling_duration'] == '0')
{
	$_POST['watermark_scrolling_duration'] = '';
}
if ($_POST['watermark_max_width'] == '0')
{
	$_POST['watermark_max_width'] = '';
}
if ($_POST['watermark_max_width_vertical'] == '0')
{
	$_POST['watermark_max_width_vertical'] = '';
}
if ($_POST['watermark2_scrolling_duration'] == '0')
{
	$_POST['watermark2_scrolling_duration'] = '';
}
if ($_POST['watermark2_max_height'] == '0')
{
	$_POST['watermark2_max_height'] = '';
}
if ($_POST['watermark2_max_height_vertical'] == '0')
{
	$_POST['watermark2_max_height_vertical'] = '';
}
if ($_POST['limit_total_duration'] == '0')
{
	$_POST['limit_total_duration'] = '';
}
if ($_POST['limit_total_min_duration_sec'] == '0')
{
	$_POST['limit_total_min_duration_sec'] = '';
}
if ($_POST['limit_total_max_duration_sec'] == '0')
{
	$_POST['limit_total_max_duration_sec'] = '';
}
if ($_POST['limit_offset_start'] == '0')
{
	$_POST['limit_offset_start'] = '';
}
if ($_POST['limit_offset_end'] == '0')
{
	$_POST['limit_offset_end'] = '';
}
if ($_POST['limit_speed_value'] == '0')
{
	$_POST['limit_speed_value'] = '';
}
if ($_POST['timeline_interval'] == '0')
{
	$_POST['timeline_interval'] = '';
}
if ($_POST['timeline_amount'] == '0')
{
	$_POST['timeline_amount'] = '';
}

if (is_file("$config[project_path]/admin/data/other/watermark_video_{$item_id}.png"))
{
	$_POST['watermark_image'] = "watermark_video_{$item_id}.png";
	$_POST['watermark_image_url'] = "$page_name?action=download_watermark&id={$item_id}";
}
if (is_file("$config[project_path]/admin/data/other/watermark2_video_{$item_id}.png"))
{
	$_POST['watermark2_image'] = "watermark2_video_{$item_id}.png";
	$_POST['watermark2_image_url'] = "$page_name?action=download_watermark2&id={$item_id}";
}

if (strlen($_POST['limit_speed_countries']) == 0)
{
	$_POST['limit_speed_countries'] = array();
} else
{
	$_POST['limit_speed_countries'] = explode(',', $_POST['limit_speed_countries']);
}

if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
{
	if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
	{
		$_POST['errors'][] = get_aa_error('format_video_changes_not_allowed');
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_options.tpl');
$smarty->assign('allowed_formats', str_replace(',', ', ', $config['video_allowed_ext']));
$smarty->assign('list_countries', $list_countries);

$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('options', $options);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['format_video_edit']));

$smarty->display("layout.tpl");

function update_format_data()
{
	global $config;

	$data = mr2array(sql("select title,postfix,video_type_id,access_level_id,is_hotlink_protection_disabled,is_download_enabled,download_order,limit_speed_option,limit_speed_value,limit_speed_guests_option,limit_speed_guests_value,limit_speed_standard_option,limit_speed_standard_value,limit_speed_premium_option,limit_speed_premium_value,limit_speed_embed_option,limit_speed_embed_value,limit_speed_countries,timeline_directory from $config[tables_prefix]formats_videos order by format_video_id asc"));
	if (count($data) == 0)
	{
		return;
	}

	file_put_contents("$config[project_path]/admin/data/system/formats_videos.dat", serialize($data), LOCK_EX);
}
