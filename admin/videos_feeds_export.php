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

$list_status_values = array(
	0 => $lang['videos']['feed_field_status_disabled'],
	1 => $lang['videos']['feed_field_status_active'],
);

$table_fields = array();
$table_fields[] = array('id' => 'feed_id',        'title' => $lang['videos']['feed_field_id'],             'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',          'title' => $lang['videos']['feed_field_title'],          'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'external_id',    'title' => $lang['videos']['feed_field_external_id'],    'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'status_id',      'title' => $lang['videos']['feed_field_status'],         'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'max_limit',      'title' => $lang['videos']['feed_field_max_limit'],      'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'cache',          'title' => $lang['videos']['feed_field_cache'],          'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'url',            'title' => $lang['videos']['feed_field_url'],            'is_default' => 1, 'type' => 'url');
$table_fields[] = array('id' => 'last_exec_date', 'title' => $lang['videos']['feed_field_last_exec_date'], 'is_default' => 0, 'type' => 'datetime', 'zero_label' => $lang['common']['undefined']);
$table_fields[] = array('id' => 'added_date',     'title' => $lang['videos']['feed_field_added_date'],     'is_default' => 0, 'type' => 'datetime');

$sort_def_field = "feed_id";
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

$table_name = "$config[tables_prefix]videos_feeds_export";
$table_key_name = "feed_id";

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
	$where .= " and (title like '%$q%' or external_id like '%$q%')";
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'url')
{
	$sort_by = "external_id";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

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

	$item_id = intval($_POST['item_id']);

	validate_field('uniq', $_POST['title'], $lang['videos']['feed_field_title'], array('field_name_in_base' => 'title'));
	if (validate_field('uniq', $_POST['external_id'], $lang['videos']['feed_field_external_id'], array('field_name_in_base' => 'external_id')))
	{
		validate_field('external_id', $_POST['external_id'], $lang['videos']['feed_field_external_id']);
	}
	validate_field('empty_int', $_POST['max_limit'], $lang['videos']['feed_field_max_limit']);
	if ($_POST['cache'] <> '0')
	{
		validate_field('empty_int', $_POST['cache'], $lang['videos']['feed_field_cache']);
	}

	if (!is_array($errors))
	{
		$options = array();
		$options['video_status_id'] = intval($_POST['video_status_id']);
		$options['video_type_id'] = intval($_POST['video_type_id']);
		$options['video_category_ids'] = isset($_POST['video_category_ids']) ? array_map('intval', $_POST['video_category_ids']) : [];
		$options['video_model_ids'] = isset($_POST['video_model_ids']) ? array_map('intval', $_POST['video_model_ids']) : [];
		$options['video_tag_ids'] = isset($_POST['video_tag_ids']) ? array_map('intval', $_POST['video_tag_ids']) : [];
		$options['video_content_source_ids'] = isset($_POST['video_content_source_ids']) ? array_map('intval', $_POST['video_content_source_ids']) : [];
		$options['video_dvd_ids'] = isset($_POST['video_dvd_ids']) ? array_map('intval', $_POST['video_dvd_ids']) : [];
		$options['video_admin_flag_id'] = intval($_POST['video_admin_flag_id']);
		$options['video_content_type_id'] = intval($_POST['video_content_type_id']);
		$options['enable_search'] = intval($_POST['enable_search']);
		$options['enable_categories'] = intval($_POST['enable_categories']);
		$options['enable_tags'] = intval($_POST['enable_tags']);
		$options['enable_models'] = intval($_POST['enable_models']);
		$options['enable_content_sources'] = intval($_POST['enable_content_sources']);
		$options['enable_dvds'] = intval($_POST['enable_dvds']);
		$options['enable_screenshot_sources'] = intval($_POST['enable_screenshot_sources']);
		$options['enable_custom_fields'] = intval($_POST['enable_custom_fields']);
		$options['enable_localization'] = intval($_POST['enable_localization']);
		$options['enable_satellites'] = intval($_POST['enable_satellites']);
		$options['enable_future_dates'] = intval($_POST['enable_future_dates']);
		$options['with_rotation_finished'] = intval($_POST['with_rotation_finished']);
		$options['with_upload_zone_site'] = intval($_POST['with_upload_zone_site']);
		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, status_id=?, external_id=?, password=?, affiliate_param_name=?, max_limit=?, cache=?, options=?, added_date=?",
				$_POST['title'], intval($_POST['status_id']), $_POST['external_id'], $_POST['password'], $_POST['affiliate_param_name'], intval($_POST['max_limit']), intval($_POST['cache']), serialize($options), date("Y-m-d H:i:s")
			);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, status_id=?, external_id=?, password=?, affiliate_param_name=?, max_limit=?, cache=?, options=? where $table_key_name=?",
				$_POST['title'], intval($_POST['status_id']), $_POST['external_id'], $_POST['password'], $_POST['affiliate_param_name'], intval($_POST['max_limit']), intval($_POST['cache']), serialize($options), $item_id
			);

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
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		sql("delete from $table_name where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'add_new')
{
	$_POST['status_id'] = 1;
	$_POST['max_limit'] = 1000;
	$_POST['cache'] = 3600;
}

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$options = unserialize($_POST['options']);
	$_POST['video_status_id'] = $options['video_status_id'];
	$_POST['video_admin_flag_id'] = $options['video_admin_flag_id'];
	$_POST['video_type_id'] = $options['video_type_id'];
	$_POST['video_content_type_id'] = $options['video_content_type_id'];
	$_POST['enable_search'] = $options['enable_search'];
	$_POST['enable_categories'] = $options['enable_categories'];
	$_POST['enable_tags'] = $options['enable_tags'];
	$_POST['enable_models'] = $options['enable_models'];
	$_POST['enable_content_sources'] = $options['enable_content_sources'];
	$_POST['enable_dvds'] = $options['enable_dvds'];
	$_POST['enable_screenshot_sources'] = $options['enable_screenshot_sources'];
	$_POST['enable_custom_fields'] = $options['enable_custom_fields'];
	$_POST['enable_localization'] = $options['enable_localization'];
	$_POST['enable_satellites'] = $options['enable_satellites'];
	$_POST['enable_future_dates'] = $options['enable_future_dates'];
	$_POST['with_rotation_finished'] = $options['with_rotation_finished'];
	$_POST['with_upload_zone_site'] = $options['with_upload_zone_site'];

	$options['video_category_ids'][] = 0;
	$options['video_category_ids'] = implode(',', array_map('intval', $options['video_category_ids']));
	$_POST['video_categories'] = mr2array(sql("select * from $config[tables_prefix]categories where category_id in ($options[video_category_ids])"));

	$options['video_model_ids'][] = 0;
	$options['video_model_ids'] = implode(',', array_map('intval', $options['video_model_ids']));
	$_POST['video_models'] = mr2array(sql("select * from $config[tables_prefix]models where model_id in ($options[video_model_ids])"));

	$options['video_tag_ids'][] = 0;
	$options['video_tag_ids'] = implode(',', array_map('intval', $options['video_tag_ids']));
	$_POST['video_tags'] = mr2array(sql("select * from $config[tables_prefix]tags where tag_id in ($options[video_tag_ids])"));

	$options['video_content_source_ids'][] = 0;
	$options['video_content_source_ids'] = implode(',', array_map('intval', $options['video_content_source_ids']));
	$_POST['video_content_sources'] = mr2array(sql("select * from $config[tables_prefix]content_sources where content_source_id in ($options[video_content_source_ids])"));

	$options['video_dvd_ids'][] = 0;
	$options['video_dvd_ids'] = implode(',', array_map('intval', $options['video_dvd_ids']));
	$_POST['video_dvds'] = mr2array(sql("select * from $config[tables_prefix]dvds where dvd_id in ($options[video_dvd_ids])"));
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select * from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	$data[$k]['url'] = "$config[project_url]/admin/feeds/$v[external_id]/";
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_videos.tpl');

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_languages', mr2array(sql("select * from $config[tables_prefix]languages order by title asc")));
$smarty->assign('list_satellites', mr2array(sql("select * from $config[tables_prefix]admin_satellites")));
$smarty->assign('list_flags_admins',mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['videos']['feed_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['videos']['feed_add']);
} else
{
	$smarty->assign('page_title', $lang['videos']['submenu_option_feeds_export']);
}

$content_scheduler_days = intval($_SESSION['userdata']['content_scheduler_days']);
if ($content_scheduler_days > 0)
{
	$where_content_scheduler_days = '';
	$sorting_content_scheduler_days = 'desc';
	if (intval($_SESSION['userdata']['content_scheduler_days_option']) == 1)
	{
		$now_date = date("Y-m-d H:i:s");
		$where_content_scheduler_days = " and post_date>'$now_date'";
		$sorting_content_scheduler_days = 'asc';
	}
	$smarty->assign('list_updates', mr2array(sql("select * from (select STR_TO_DATE(post_date, '%Y-%m-%d') as post_date, count(STR_TO_DATE(post_date, '%Y-%m-%d')) as updates from $config[tables_prefix]videos where status_id=1 and relative_post_date=0 $where_content_scheduler_days group by STR_TO_DATE(post_date, '%Y-%m-%d') order by post_date $sorting_content_scheduler_days limit $content_scheduler_days) X order by post_date desc")));
}

$smarty->display("layout.tpl");
