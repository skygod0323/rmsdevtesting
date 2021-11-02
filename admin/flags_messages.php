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

$table_fields = array();
$table_fields[] = array('id' => 'flag_message_id', 'title' => $lang['users']['flag_message_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'flag',            'title' => $lang['users']['flag_message_field_flag'],       'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'object',          'title' => $lang['users']['flag_message_field_object'],     'is_default' => 1, 'type' => 'object');
$table_fields[] = array('id' => 'message',         'title' => $lang['users']['flag_message_field_message'],    'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'ip',              'title' => $lang['users']['flag_message_field_ip'],         'is_default' => 0, 'type' => 'ip');
$table_fields[] = array('id' => 'country',         'title' => $lang['users']['flag_message_field_country'],    'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'user_agent',      'title' => $lang['users']['flag_message_field_user_agent'], 'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'referer',         'title' => $lang['users']['flag_message_field_referer'],    'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'added_date',      'title' => $lang['users']['flag_message_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "flag_message_id";
$sort_def_direction = "desc";
$sort_array = array();
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

$search_fields = array();
$search_fields[] = array('id' => 'flag_message_id', 'title' => $lang['users']['flag_message_field_id']);
$search_fields[] = array('id' => 'message',         'title' => $lang['users']['flag_message_field_message']);
$search_fields[] = array('id' => 'ip',              'title' => $lang['users']['flag_message_field_ip']);

$table_name = "$config[tables_prefix]flags_messages";
$table_key_name = "flag_message_id";
$table_selector = "$table_name.*, $config[tables_prefix]flags.title as flag, case when $table_name.video_id>0 then $table_name.video_id when $table_name.album_id>0 then $table_name.album_id when $table_name.dvd_id>0 then $table_name.dvd_id when $table_name.post_id>0 then $table_name.post_id when $table_name.playlist_id>0 then $table_name.playlist_id end as object_id, case when $table_name.video_id>0 then 1 when $table_name.album_id>0 then 2 when $table_name.dvd_id>0 then 5 when $table_name.post_id>0 then 12 when $table_name.playlist_id>0 then 13 end as object_type_id, case when $table_name.video_id>0 then coalesce($config[tables_prefix]videos.title, $config[tables_prefix]videos.video_id) when $table_name.album_id>0 then coalesce($config[tables_prefix]albums.title, $config[tables_prefix]albums.album_id) when $table_name.dvd_id>0 then $config[tables_prefix]dvds.title when $table_name.post_id>0 then $config[tables_prefix]posts.title when $table_name.playlist_id>0 then $config[tables_prefix]playlists.title end as object";
$table_projector = "$table_name
						left join $config[tables_prefix]flags on $config[tables_prefix]flags.flag_id=$table_name.flag_id
						left join $config[tables_prefix]videos on $config[tables_prefix]videos.video_id=$table_name.video_id
						left join $config[tables_prefix]albums on $config[tables_prefix]albums.album_id=$table_name.album_id
						left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.dvd_id
						left join $config[tables_prefix]posts on $config[tables_prefix]posts.post_id=$table_name.post_id
						left join $config[tables_prefix]playlists on $config[tables_prefix]playlists.playlist_id=$table_name.playlist_id
";

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
	$_SESSION['save'][$page_name]['se_flag_id'] = '';
	$_SESSION['save'][$page_name]['se_object_type_id'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_flag_id']))
	{
		$_SESSION['save'][$page_name]['se_flag_id'] = intval($_GET['se_flag_id']);
	}
	if (isset($_GET['se_object_type_id']))
	{
		$_SESSION['save'][$page_name]['se_object_type_id'] = intval($_GET['se_object_type_id']);
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where_search = '1=0';
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
		if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
		{
			if ($search_field['id'] == $table_key_name)
			{
				if (preg_match("/^([\ ]*[0-9]+[\ ]*,[\ ]*)+[0-9]+[\ ]*$/is", $q))
				{
					$search_ids_array = array_map('intval', array_map('trim', explode(',', $q)));
					$where_search .= " or $table_name.$search_field[id] in (" . implode(',', $search_ids_array) . ")";
				} else
				{
					$where_search .= " or $table_name.$search_field[id]='$q'";
				}
			} elseif ($search_field['id'] == 'ip')
			{
				$q_ip = ip2int($q);
				if ($q_ip > 0)
				{
					$where_search .= " or $table_name.ip='$q_ip'";
				}
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_flag_id'] > 0)
{
	$where .= " and $table_name.flag_id=" . intval($_SESSION['save'][$page_name]['se_flag_id']);
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_object_type_id'])
{
	case 1:
		$where .= " and $table_name.video_id>0";
		$table_filtered = 1;
		break;
	case 2:
		$where .= " and $table_name.album_id>0";
		$table_filtered = 1;
		break;
	case 5:
		$where .= " and $table_name.dvd_id>0";
		$table_filtered = 1;
		break;
	case 12:
		$where .= " and $table_name.post_id>0";
		$table_filtered = 1;
		break;
	case 13:
		$where .= " and $table_name.playlist_id>0";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'object')
{
	$sort_by = 'video_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', album_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', dvd_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', post_id ' . $_SESSION['save'][$page_name]['sort_direction'] . ', playlist_id';
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// table actions
// =====================================================================================================================

if (@array_search("0", $_REQUEST['row_select']) !== false)
{
	unset($_REQUEST['row_select'][@array_search("0", $_REQUEST['row_select'])]);
}
if ($_REQUEST['batch_action'] <> '' && count($_REQUEST['row_select']) > 0)
{
	$row_select_str = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		sql("delete from $table_name where $table_key_name in ($row_select_str)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$_POST['ip'] = int2ip($_POST['ip']);
	if ($_POST['object'] == '')
	{
		$_POST['object'] = $_POST['object_id'];
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_projector $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

// =====================================================================================================================
// display
// =====================================================================================================================

$list_flags = mr2array(sql("select * from $config[tables_prefix]flags order by group_id asc, title asc"));
$list_flags_grouped = array();
foreach ($list_flags as $flag)
{
	$list_flags_grouped[$flag['group_id']][] = $flag;
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');
$smarty->assign('list_flags_grouped', $list_flags_grouped);
$smarty->assign('options', $options);

if (in_array($_REQUEST['action'], array('change')))
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
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['flag_message_id'], $lang['users']['flag_message_edit']));
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_flags_messages']);
}

$smarty->display("layout.tpl");
