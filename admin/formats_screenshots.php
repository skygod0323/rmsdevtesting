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

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_status_values = array(
	0 => $lang['settings']['format_screenshot_field_status_creating'],
	1 => $lang['settings']['format_screenshot_field_status_required'],
	2 => $lang['settings']['format_screenshot_field_status_error'],
	3 => $lang['settings']['format_screenshot_field_status_deleting'],
	4 => $lang['settings']['format_screenshot_field_status_error'],
);

$list_image_type_values = array(
	0 => $lang['settings']['format_screenshot_field_image_type_jpg'],
	1 => $lang['settings']['format_screenshot_field_image_type_webp'],
);

$list_interlace_values = array(
	0 => $lang['settings']['format_screenshot_field_interlace_none'],
	1 => $lang['settings']['format_screenshot_field_interlace_line'],
	2 => $lang['settings']['format_screenshot_field_interlace_plane'],
);

$list_watermark_positions = array(
	'' => $lang['common']['undefined'],
	0 => $lang['settings']['format_screenshot_field_watermark_position_random'],
	1 => $lang['settings']['format_screenshot_field_watermark_position_top_left'],
	2 => $lang['settings']['format_screenshot_field_watermark_position_top_right'],
	3 => $lang['settings']['format_screenshot_field_watermark_position_bottom_right'],
	4 => $lang['settings']['format_screenshot_field_watermark_position_bottom_left'],
);

$table_fields = array();
$table_fields[] = array('id' => 'format_screenshot_id',  'title' => $lang['settings']['format_screenshot_field_id'],                 'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',                 'title' => $lang['settings']['format_screenshot_field_title'],              'is_default' => 1, 'type' => 'text', 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'status_id',             'title' => $lang['settings']['format_screenshot_field_status'],             'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values, 'append' => array(0 => 'pc_complete', 3 => 'pc_complete'), 'is_nowrap' => 1, 'ifhighlight' => 'is_error');
$table_fields[] = array('id' => 'size',                  'title' => $lang['settings']['format_screenshot_field_size'],               'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'image_type',            'title' => $lang['settings']['format_screenshot_field_image_type'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_image_type_values);
$table_fields[] = array('id' => 'is_create_zip',         'title' => $lang['settings']['format_screenshot_field_create_zip'],         'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'interlace_id',          'title' => $lang['settings']['format_screenshot_field_interlace'],          'is_default' => 0, 'type' => 'choice', 'values' => $list_interlace_values);
$table_fields[] = array('id' => 'comment',               'title' => $lang['settings']['format_screenshot_field_comment'],            'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'watermark_image',       'title' => $lang['settings']['format_screenshot_field_watermark_image'],    'is_default' => 0, 'type' => 'image');
$table_fields[] = array('id' => 'watermark_position_id', 'title' => $lang['settings']['format_screenshot_field_watermark_position'], 'is_default' => 0, 'type' => 'choice', 'values' => $list_watermark_positions);

$sort_def_field = "format_screenshot_id";
$sort_def_direction = "desc";
$sort_array = array();
$sidebar_fields = array();
foreach ($table_fields as $k => $field)
{
	if ($field['type'] != 'longtext' && $field['type'] != 'list' && $field['type'] != 'rename' && $field['type'] != 'thumb')
	{
		$sort_array[] = $field['id'];
		$table_fields[$k]['is_sortable'] = 1;
	}
	if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
	{
		if (in_array($field['id'], $_GET['grid_columns']))
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name]['grid_columns'][$field['id']]);
	} else
	{
		$table_fields[$k]['is_enabled'] = intval($field['is_default']);
	}
	if ($field['type'] == 'id')
	{
		$table_fields[$k]['is_enabled'] = 1;
	}
}
if (isset($_GET['grid_columns']) && is_array($_GET['grid_columns']) && !isset($_GET['reset_filter']))
{
	$_SESSION['save'][$page_name]['grid_columns_order'] = $_GET['grid_columns'];
}
if (is_array($_SESSION['save'][$page_name]['grid_columns_order']))
{
	$temp_table_fields = array();
	foreach ($table_fields as $table_field)
	{
		if ($table_field['type'] == 'id')
		{
			$temp_table_fields[] = $table_field;
			break;
		}
	}
	foreach ($_SESSION['save'][$page_name]['grid_columns_order'] as $table_field_id)
	{
		foreach ($table_fields as $table_field)
		{
			if ($table_field['id'] == $table_field_id)
			{
				$temp_table_fields[] = $table_field;
				break;
			}
		}
	}
	foreach ($table_fields as $table_field)
	{
		if (!in_array($table_field['id'], $_SESSION['save'][$page_name]['grid_columns_order']) && $table_field['type'] != 'id')
		{
			$temp_table_fields[] = $table_field;
		}
	}
	$table_fields = $temp_table_fields;
}

$table_name = "$config[tables_prefix]formats_screenshots";
$table_key_name = "format_screenshot_id";

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name]['sort_by'] == '')
{
	$_SESSION['save'][$page_name]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name]['sort_direction'] = 'desc';
	}
}

if (isset($_GET['num_on_page']))
{
	$_SESSION['save'][$page_name]['num_on_page'] = intval($_GET['num_on_page']);
}
if ($_SESSION['save'][$page_name]['num_on_page'] < 1)
{
	$_SESSION['save'][$page_name]['num_on_page'] = 20;
}

if (isset($_GET['from']))
{
	$_SESSION['save'][$page_name]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name]['from'], "integer");

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

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and (title like '%$q%' or size like '%$q%' or im_options like '%$q%' or im_options_manual like '%$q%' or comment like '%$q%') ";
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'watermark_image')
{
	$sort_by = $table_key_name;
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// watermark
// =====================================================================================================================

if ($_REQUEST['action'] == 'download_watermark')
{
	header('Content-Type: image/png');
	$format_id = intval($_REQUEST['id']);
	$watermark_file = "$config[project_path]/admin/data/other/watermark_screen_{$format_id}.png";
	if (is_file($watermark_file))
	{
		header('Content-Length: ' . filesize($watermark_file));
		readfile($watermark_file);
	}
	die;
}

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if (in_array($_POST['action'], array('add_new_complete', 'change_complete')))
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
			$errors[] = get_aa_error('format_screenshot_changes_not_allowed');
			return_ajax_errors($errors);
		}
	}

	$item_id = intval($_POST['item_id']);

	validate_field('empty', $_POST['title'], $lang['settings']['format_screenshot_field_title']);

	$need_resize = true;
	if ($_POST['size'] <> 'source')
	{
		if (validate_field('size', $_POST['size'], $lang['settings']['format_screenshot_field_size']))
		{
			if (mr2number(sql_pr("select count(*) from $table_name where size=? and group_id=? and $table_key_name<>?", $_POST['size'], intval($_POST['group_id']), $item_id)) > 0)
			{
				$errors[] = get_aa_error('unique_field', $lang['settings']['format_screenshot_field_size']);
			}
		}
	} else
	{
		$need_resize = false;
		if (mr2number(sql_pr("select count(*) from $table_name where size=? and group_id=? and $table_key_name<>?", $_POST['size'], intval($_POST['group_id']), $item_id)) > 0)
		{
			$errors[] = get_aa_error('unique_field', $lang['settings']['format_screenshot_field_size']);
		}
	}

	if (intval($_POST['image_type']) == 1)
	{
		$rnd = mt_rand(10000000, 99999999);
		$source_image = "$config[project_path]/admin/plugins/audit/data/test_watermark.png";
		$target_image = "$config[temporary_path]/test_webp-$rnd.webp";
		exec("$config[image_magick_path] $source_image webp:$target_image");
		if (!is_file($target_image) || filesize($target_image) == 0)
		{
			$errors[] = get_aa_error('image_format_webp', $lang['settings']['format_screenshot_field_image_type'], $config['image_magick_path']);
		}
		@unlink($target_image);
	}

	if ($_POST['group_id'] == 1 || $_POST['group_id'] == 2)
	{
		if (validate_field('empty', $_POST['im_options'], $lang['settings']['format_screenshot_field_im_options']))
		{
			if (strpos($_POST['im_options'], '%INPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options'], '%INPUT_FILE%');
			} elseif (strpos($_POST['im_options'], '%OUTPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options'], '%OUTPUT_FILE%');
			} elseif ($need_resize && strpos($_POST['im_options'], '%SIZE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options'], '%SIZE%');
			}
		}
	}

	if ($_POST['group_id'] == 1 || $_POST['group_id'] == 3)
	{
		if (validate_field('empty', $_POST['im_options_manual'], $lang['settings']['format_screenshot_field_im_options_manual']))
		{
			if (strpos($_POST['im_options_manual'], '%INPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options_manual'], '%INPUT_FILE%');
			} elseif (strpos($_POST['im_options_manual'], '%OUTPUT_FILE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options_manual'], '%OUTPUT_FILE%');
			} elseif ($need_resize && strpos($_POST['im_options_manual'], '%SIZE%') === false)
			{
				$errors[] = get_aa_error('token_required', $lang['settings']['format_screenshot_field_im_options_manual'], '%SIZE%');
			}
		}
	}

	validate_field('file', 'watermark_image', $lang['settings']['format_screenshot_field_watermark_image'], array('is_image' => '1', 'allowed_ext' => 'png'));

	if (!is_array($errors))
	{
		if ($_POST['group_id'] == 2)
		{
			$_POST['im_options_manual'] = $_POST['im_options'];
		} elseif ($_POST['group_id'] == 3)
		{
			$_POST['im_options'] = $_POST['im_options_manual'];
		}
		if ($_POST['action'] == 'add_new_complete')
		{
			$item_id = sql_insert("insert into $table_name set title=?, group_id=?, status_id=0, size=?, image_type=?, im_options=?, im_options_manual=?, interlace_id=?, comment=?, aspect_ratio_id=?, aspect_ratio_gravity=?, vertical_aspect_ratio_id=?, vertical_aspect_ratio_gravity=?, is_create_zip=?, watermark_position_id=?, added_date=?",
				$_POST['title'], intval($_POST['group_id']), $_POST['size'], intval($_POST['image_type']), $_POST['im_options'], $_POST['im_options_manual'], intval($_POST['interlace_id']), $_POST['comment'], intval($_POST['aspect_ratio_id']), trim($_POST['aspect_ratio_gravity']), intval($_POST['vertical_aspect_ratio_id']), trim($_POST['vertical_aspect_ratio_gravity']), intval($_POST['is_create_zip']), intval($_POST['watermark_position_id']), date("Y-m-d H:i:s")
			);
			transfer_uploaded_file('watermark_image', "$config[project_path]/admin/data/other/watermark_screen_{$item_id}.png");

			$background_task = array();
			$background_task['format_id'] = $item_id;
			$background_task['format_size'] = $_POST['size'];
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=7, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_is_create_zip = mr2number(sql_pr("select is_create_zip from $table_name where $table_key_name=?", $item_id));

			sql_pr("update $table_name set title=?, im_options=?, im_options_manual=?, interlace_id=?, comment=?, aspect_ratio_id=?, aspect_ratio_gravity=?, vertical_aspect_ratio_id=?, vertical_aspect_ratio_gravity=?, is_create_zip=?, watermark_position_id=? where $table_key_name=?",
				$_POST['title'], $_POST['im_options'], $_POST['im_options_manual'], intval($_POST['interlace_id']), $_POST['comment'], intval($_POST['aspect_ratio_id']), trim($_POST['aspect_ratio_gravity']), intval($_POST['vertical_aspect_ratio_id']), trim($_POST['vertical_aspect_ratio_gravity']), intval($_POST['is_create_zip']), intval($_POST['watermark_position_id']), $item_id
			);
			if ($_POST['watermark_image_hash'] <> '')
			{
				transfer_uploaded_file('watermark_image', "$config[project_path]/admin/data/other/watermark_screen_{$item_id}.png");
			} elseif ($_POST['watermark_image'] == '')
			{
				if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$item_id}.png"))
				{
					unlink("$config[project_path]/admin/data/other/watermark_screen_{$item_id}.png");
				}
			}

			if ($old_is_create_zip == 0 && intval($_POST['is_create_zip']) == 1)
			{
				$background_task = array();
				$background_task['format_id'] = $item_id;
				$background_task['format_size'] = $_POST['size'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=16, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));
			} elseif ($old_is_create_zip == 1 && intval($_POST['is_create_zip']) == 0)
			{
				$background_task = array();
				$background_task['format_id'] = $item_id;
				$background_task['format_size'] = $_POST['size'];
				sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=17, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));
			}

			$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && !isset($_REQUEST['reorder']) && count($_REQUEST['row_select']) > 0)
{
	$format_id = intval($_REQUEST['row_select'][0]);
	if ($_REQUEST['batch_action'] == 'delete')
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
		{
			if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
			{
				$errors[] = get_aa_error('format_screenshot_changes_not_allowed');
				return_ajax_errors($errors);
			}
		}

		$format_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=$format_id"));
		if (is_array($format_data))
		{
			if (mr2number(sql_pr("select count(*) from $table_name where status_id=1 and group_id=1 and $table_key_name<>$format_id")) == 0)
			{
				$errors[] = get_aa_error('format_screenshot_delete_not_allowed');
				return_ajax_errors($errors);
			}
			if ($format_data['group_id'] == 2)
			{
				$used_in_player = false;
				$player_data_files = get_player_data_files();
				foreach ($player_data_files as $player_data_file)
				{
					if (check_format_usage_in_player($format_data['size'], $player_data_file['file']))
					{
						$used_in_player = true;
					}
				}
				if ($used_in_player)
				{
					$errors[] = get_aa_error('format_screenshot_delete_not_allowed2');
					return_ajax_errors($errors);
				}
			}

			sql_pr("update $table_name set status_id=3 where $table_key_name=$format_id");
			@unlink("$config[project_path]/admin/data/other/watermark_screen_{$format_id}.png");

			$background_task = array();
			$background_task['format_id'] = $format_id;
			$background_task['format_size'] = $format_data['size'];
			sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=9, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_removed'];
		}
	} elseif ($_REQUEST['batch_action'] == 'recreate')
	{
		$format_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=$format_id"));

		$background_task = array();
		$background_task['format_id'] = $format_id;
		$background_task['format_size'] = $format_data['size'];
		$background_task['recreate'] = '1';
		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=7, data=?, added_date=?", serialize($background_task), date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['settings']['format_screenshot_success_message_recreated'];
	} elseif ($_REQUEST['batch_action'] == 'restart')
	{
		$format_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $format_id));

		$format_status_id = 0;
		$task_type_id = 7;
		if ($format_data['status_id'] == 3 || $format_data['status_id'] == 4)
		{
			$format_status_id = 3;
			$task_type_id = 9;
		}

		sql_pr("update $table_name set status_id=$format_status_id where $table_key_name=$format_id");

		$background_task = array();
		$background_task['format_id'] = $format_id;
		$background_task['format_size'] = $format_data['size'];
		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=?, data=?, added_date=?", $task_type_id, serialize($background_task), date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['settings']['format_screenshot_success_message_restarted'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=? and status_id=1", $item_id));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$item_id}.png"))
	{
		$_POST['watermark_image'] = "watermark_screen_{$item_id}.png";
		$_POST['watermark_image_url'] = "$page_name?action=download_watermark&id={$item_id}";
	}
}

if ($_GET['action'] == 'add_new')
{
	$_POST['im_options'] = '-enhance -strip -unsharp 1.0x1.0+0.5 -unsharp 1.0x1.0+0.5 -modulate 110,102,100 -unsharp 1.0x1.0+0.5 -contrast -gamma 1.2 -resize %SIZE% %INPUT_FILE% -filter Lanczos -filter Blackman -quality 80 %OUTPUT_FILE%';
	$_POST['im_options_manual'] = '-strip -resize %SIZE% %INPUT_FILE% -quality 80 %OUTPUT_FILE%';
	$_POST['aspect_ratio_id'] = 2;
	$_POST['vertical_aspect_ratio_id'] = 1;
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where status_id<>2 and type_id not in (50,51,52,53)")) > 0)
{
	if (!is_file("$config[project_path]/admin/data/system/background_tasks_pause.dat"))
	{
		$_POST['errors'][] = get_aa_error('format_screenshot_changes_not_allowed');
	}
}

$formats_creating_tasks = mr2array(sql("select * from $config[tables_prefix]background_tasks where type_id=7"));
$formats_deleting_tasks = mr2array(sql("select * from $config[tables_prefix]background_tasks where type_id=9"));
$data_overview = mr2array(sql("select * from $table_name where group_id=1 $where order by $sort_by"));
$data_timeline = mr2array(sql("select * from $table_name where group_id=2 $where order by $sort_by"));
$data_posters = mr2array(sql("select * from $table_name where group_id=3 $where order by $sort_by"));

$data = array();
$data[1] = $data_overview;
$data[2] = $data_timeline;
$data[3] = $data_posters;

foreach ($data as $index => $data_item)
{
	foreach ($data_item as $k => $v)
	{
		if (is_file("$config[project_path]/admin/data/other/watermark_screen_{$v['format_screenshot_id']}.png"))
		{
			$data[$index][$k]['watermark_image'] = "{$v['format_screenshot_id']}.png";
			$data[$index][$k]['watermark_image_url'] = "$page_name?action=download_watermark&id=$v[format_screenshot_id]";
		} else
		{
			$data[$index][$k]['watermark_position_id'] = "";
		}

		if ($v['size'] == 'source')
		{
			$data[$index][$k]['size'] = $lang['settings']['format_screenshot_field_size_source'] . " " . $lang['settings']['format_screenshot_field_size_dynamic'];
		} elseif ($v['aspect_ratio_id'] == 3 || $v['vertical_aspect_ratio_id'] == 3)
		{
			$data[$index][$k]['size'] = $v['size'] . " " . $lang['settings']['format_screenshot_field_size_dynamic'];
		} else
		{
			$data[$index][$k]['size'] = $v['size'] . " " . $lang['settings']['format_screenshot_field_size_fixed'];
		}

		if ($v['status_id'] == 0)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;

			$has_task = false;
			foreach ($formats_creating_tasks as $task)
			{
				$task_data = @unserialize($task['data']);
				if ($task_data['format_id'] == $v['format_screenshot_id'])
				{
					$data[$index][$k]['pc_complete'] = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat")) . '%';
					$has_task = true;
					break;
				}
			}
			if (!$has_task)
			{
				$data[$index][$k]['status_id'] = 2;
				$data[$index][$k]['is_error'] = 1;
			}
		} elseif ($v['status_id'] == 2)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;
			$data[$index][$k]['is_error'] = 1;
		} elseif ($v['status_id'] == 3)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;

			$has_task = false;
			foreach ($formats_deleting_tasks as $task)
			{
				$task_data = @unserialize($task['data']);
				if ($task_data['format_id'] == $v['format_screenshot_id'])
				{
					$data[$index][$k]['pc_complete'] = intval(@file_get_contents("$config[project_path]/admin/data/engine/tasks/$task[task_id].dat")) . '%';
					$has_task = true;
					break;
				}
			}
			if (!$has_task)
			{
				$data[$index][$k]['status_id'] = 4;
				$data[$index][$k]['is_error'] = 1;
			}
		} elseif ($v['status_id'] == 4)
		{
			$data[$index][$k]['is_editing_forbidden'] = 1;
			$data[$index][$k]['is_error'] = 1;
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_options.tpl');

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', count($data_overview) + count($data_timeline));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['settings']['format_screenshot_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['settings']['format_screenshot_add']);
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_formats_screenshots_list']);
}

$smarty->display("layout.tpl");

function check_format_usage_in_player($size, $data_file)
{
	if (is_file($data_file))
	{
		$player_data = @unserialize(file_get_contents($data_file), ['allowed_classes' => false]);
		if (is_array($player_data))
		{
			if ($player_data['timeline_screenshots_size'] == $size || $player_data['timeline_screenshots_webp_size'] == $size)
			{
				return true;
			}
		}
	}

	return false;
}
