<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_action_values = array(
	100 => $lang['settings']['audit_log_field_action_added_object_manually'],
	110 => $lang['settings']['audit_log_field_action_added_object_import'],
	120 => $lang['settings']['audit_log_field_action_added_object_feed'],
	130 => $lang['settings']['audit_log_field_action_added_object_plugin'],
	140 => $lang['settings']['audit_log_field_action_added_object_manually'],
	150 => $lang['settings']['audit_log_field_action_modified_object'],
	151 => $lang['settings']['audit_log_field_action_modified_video_screenshots'],
	152 => $lang['settings']['audit_log_field_action_modified_album_images'],
	153 => $lang['settings']['audit_log_field_action_modified_video_screenshots'],
	154 => $lang['settings']['audit_log_field_action_modified_album_images'],
	160 => $lang['settings']['audit_log_field_action_modified_object_massedit'],
	170 => $lang['settings']['audit_log_field_action_modified_object'],
	175 => $lang['settings']['audit_log_field_action_modified_object'],
	180 => $lang['settings']['audit_log_field_action_deleted_object'],
	190 => $lang['settings']['audit_log_field_action_deleted_object'],
	195 => $lang['settings']['audit_log_field_action_deleted_object'],
	200 => $lang['settings']['audit_log_field_action_translated_object'],
	220 => $lang['settings']['audit_log_field_action_modified_content_settings'],
	221 => $lang['settings']['audit_log_field_action_modified_website_settings'],
	222 => $lang['settings']['audit_log_field_action_modified_memberzone_settings'],
	223 => $lang['settings']['audit_log_field_action_modified_stats_settings'],
	224 => $lang['settings']['audit_log_field_action_modified_customization_settings'],
	225 => $lang['settings']['audit_log_field_action_modified_player_settings'],
	226 => $lang['settings']['audit_log_field_action_modified_embed_settings'],
	227 => $lang['settings']['audit_log_field_action_modified_antispam_settings'],
);

$table_fields = array();
$table_fields[] = array('id' => 'user_id',        'title' => $lang['settings']['audit_log_field_author'],   'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'action_id',      'title' => $lang['settings']['audit_log_field_action'],   'is_default' => 1, 'type' => 'choice', 'values' => $list_action_values);
$table_fields[] = array('id' => 'object',         'title' => $lang['settings']['audit_log_field_object'],   'is_default' => 1, 'type' => 'object');
$table_fields[] = array('id' => 'action_details', 'title' => $lang['settings']['audit_log_field_details'],  'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'added_date',     'title' => $lang['settings']['audit_log_field_datetime'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "added_date";
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

$table_name = "$config[tables_prefix]admin_audit_log";
$table_key_name = "record_id";

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
	$_SESSION['save'][$page_name]['se_object_type_id'] = '';
	$_SESSION['save'][$page_name]['se_object_id'] = '';
	$_SESSION['save'][$page_name]['se_action_type_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = "";
	$_SESSION['save'][$page_name]['se_date_to'] = "";
	$_SESSION['save'][$page_name]['se_admin_id'] = "";
	$_SESSION['save'][$page_name]['se_user'] = "";
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_object_type_id']))
	{
		$_SESSION['save'][$page_name]['se_object_type_id'] = intval($_GET['se_object_type_id']);
	}
	if (isset($_GET['se_object_id']))
	{
		$_SESSION['save'][$page_name]['se_object_id'] = intval($_GET['se_object_id']);
	}
	if (isset($_GET['se_action_type_id']))
	{
		$_SESSION['save'][$page_name]['se_action_type_id'] = intval($_GET['se_action_type_id']);
	}
	if (isset($_GET['se_date_from_Day'], $_GET['se_date_from_Month'], $_GET['se_date_from_Year']))
	{
		if (intval($_GET['se_date_from_Day']) > 0 && intval($_GET['se_date_from_Month']) > 0 && intval($_GET['se_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_date_from'] = intval($_GET['se_date_from_Year']) . "-" . intval($_GET['se_date_from_Month']) . "-" . intval($_GET['se_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_date_from'] = "";
		}
	}
	if (isset($_GET['se_date_to_Day'], $_GET['se_date_to_Month'], $_GET['se_date_to_Year']))
	{
		if (intval($_GET['se_date_to_Day']) > 0 && intval($_GET['se_date_to_Month']) > 0 && intval($_GET['se_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_date_to'] = intval($_GET['se_date_to_Year']) . "-" . intval($_GET['se_date_to_Month']) . "-" . intval($_GET['se_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_date_to'] = "";
		}
	}
	if (isset($_GET['se_admin_id']))
	{
		$_SESSION['save'][$page_name]['se_admin_id'] = intval($_GET['se_admin_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and $table_name.action_details like '%$q%'";
}

if ($_SESSION['save'][$page_name]['se_object_type_id'] > 0)
{
	$where .= " and $table_name.object_type_id=" . intval($_SESSION['save'][$page_name]['se_object_type_id']);
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_object_id'] > 0)
{
	$where .= " and $table_name.object_id=" . intval($_SESSION['save'][$page_name]['se_object_id']);
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_admin_id'] > 0)
{
	$where .= " and $table_name.action_id not in (120, 140, 153, 154, 170, 175, 190, 195) and $table_name.user_id=" . intval($_SESSION['save'][$page_name]['se_admin_id']);
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_user'] <> "")
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
	$where .= " and $table_name.action_id in (140, 153, 154, 170, 190) and $table_name.username='$q'";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_action_type_id'])
{
	case 1:
		$where .= " and $table_name.action_id in (100, 140)";
		$table_filtered = 1;
		break;
	case 2:
		$where .= " and $table_name.action_id in (110)";
		$table_filtered = 1;
		break;
	case 3:
		$where .= " and $table_name.action_id in (120)";
		$table_filtered = 1;
		break;
	case 4:
		$where .= " and $table_name.action_id in (130)";
		$table_filtered = 1;
		break;
	case 5:
		$where .= " and $table_name.action_id in (150, 170, 175)";
		$table_filtered = 1;
		break;
	case 6:
		$where .= " and $table_name.action_id in (151, 153)";
		$table_filtered = 1;
		break;
	case 7:
		$where .= " and $table_name.action_id in (152, 154)";
		$table_filtered = 1;
		break;
	case 8:
		$where .= " and $table_name.action_id in (160)";
		$table_filtered = 1;
		break;
	case 9:
		$where .= " and $table_name.action_id in (180, 190, 195)";
		$table_filtered = 1;
		break;
	case 10:
		$where .= " and $table_name.action_id in (200)";
		$table_filtered = 1;
		break;
	default:
		if (intval($_SESSION['save'][$page_name]['se_action_type_id']) > 0)
		{
			$where .= " and $table_name.action_id=" . intval($_SESSION['save'][$page_name]['se_action_type_id']);
			$table_filtered = 1;
		}
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'object')
{
	$sort_by = "$table_name.object_type_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.object_id";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}

$data = mr2array(sql("select $table_name.*, $table_name.object_id as object, $config[tables_prefix]admin_users.login as actual_admin_login from $table_name left join $config[tables_prefix]admin_users on $table_name.user_id=$config[tables_prefix]admin_users.user_id $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
foreach ($data as $k => $v)
{
	if ($v['action_id'] == 120 || $v['action_id'] == 175 || $v['action_id'] == 195)
	{
		$data[$k]['user_id'] = str_replace("%1%", $v['username'], $lang['settings']['audit_log_field_author_feed']);
	} elseif ($v['action_id'] == 140 || $v['action_id'] == 170 || $v['action_id'] == 190)
	{
		$data[$k]['user_id'] = str_replace("%1%", $v['username'], $lang['settings']['audit_log_field_author_website']);
	} else
	{
		if ($v['actual_admin_login'])
		{
			$data[$k]['user_id'] = str_replace("%1%", $v['actual_admin_login'], $lang['settings']['audit_log_field_author_admin']);
		} else
		{
			$data[$k]['user_id'] = str_replace("%1%", $v['username'], $lang['settings']['audit_log_field_author_admin']);
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');
$smarty->assign('list_admins', mr2array(sql("select user_id, login from $config[tables_prefix]admin_users order by login asc")));

$smarty->assign('data', $data);
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['submenu_option_audit_log']);

$smarty->display("layout.tpl");
