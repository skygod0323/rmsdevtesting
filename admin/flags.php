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

$options = get_options();

$table_fields = array();
$table_fields[] = array('id' => 'flag_id',       'title' => $lang['categorization']['flag_field_id'],                     'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'title',         'title' => $lang['categorization']['flag_field_title'],                  'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'external_id',   'title' => $lang['categorization']['flag_field_external_id'],            'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'is_admin_flag', 'title' => $lang['categorization']['flag_field_admin_panel_admin_flag'], 'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'is_alert',      'title' => $lang['categorization']['flag_field_admin_panel_alert'],      'is_default' => 1, 'type' => 'bool', 'append' => array(1 => 'alert_min_count'));
$table_fields[] = array('id' => 'is_event',      'title' => $lang['categorization']['flag_field_voting_event'],           'is_default' => 1, 'type' => 'bool');
$table_fields[] = array('id' => 'is_rating',     'title' => $lang['categorization']['flag_field_voting_rating_weight'],   'is_default' => 1, 'type' => 'bool', 'append' => array(1 => 'rating_weight'));
$table_fields[] = array('id' => 'is_tokens',     'title' => $lang['categorization']['flag_field_voting_tokens_cost'],     'is_default' => 1, 'type' => 'bool', 'append' => array(1 => 'tokens_required'));
$table_fields[] = array('id' => 'objects',       'title' => $lang['categorization']['flag_field_objects'],                'is_default' => 1, 'type' => 'number', 'link' => 'custom', 'link_id' => 'flag_id', 'permission' => 'custom', 'ifdisable_zero' => 1);

$sort_def_field = "flag_id";
$sort_def_direction = "asc";
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
$search_fields[] = array('id' => 'flag_id',     'title' => $lang['categorization']['flag_field_id']);
$search_fields[] = array('id' => 'title',       'title' => $lang['categorization']['flag_field_title']);
$search_fields[] = array('id' => 'external_id', 'title' => $lang['categorization']['flag_field_external_id']);

$table_name = "$config[tables_prefix]flags";
$table_key_name = "flag_id";

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
$having = '';

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
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($having != '')
{
	$having = " having " . substr($having, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
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
	$group_id = intval($_POST['group_id']);
	if ($group_id == 0)
	{
		$group_id = mr2number(sql_pr("select group_id from $table_name where $table_key_name=?", $item_id));
	}

	if (validate_field('empty', $_POST['title'], $lang['categorization']['flag_field_title']))
	{
		if (mr2number(sql_pr("select count(*) from $table_name where title=? and group_id=? and $table_key_name<>?", $_POST['title'], $group_id, $item_id)) > 0)
		{
			$errors[] = get_aa_error('unique_field', $lang['categorization']['flag_field_title']);
		}
	}
	validate_field('uniq', $_POST['external_id'], $lang['categorization']['flag_field_external_id'], array('field_name_in_base' => 'external_id'));
	if (intval($_POST['is_alert']) > 0)
	{
		validate_field('empty_int', $_POST['alert_min_count'], $lang['categorization']['flag_field_admin_panel']);
	}
	if (intval($_POST['is_rating']) > 0 && $_POST['rating_weight'] != '0')
	{
		validate_field('empty_int_ext', $_POST['rating_weight'], $lang['categorization']['flag_field_voting']);
	}
	if (intval($_POST['is_tokens']) > 0)
	{
		validate_field('empty_int', $_POST['tokens_required'], $lang['categorization']['flag_field_voting']);
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, external_id=?, group_id=?, is_admin_flag=?, is_alert=?, alert_min_count=?, is_event=?, is_rating=?, rating_weight=?, is_tokens=?, tokens_required=?, added_date=?", $_POST['title'], $_POST['external_id'], intval($_POST['group_id']), intval($_POST['is_admin_flag']), intval($_POST['is_alert']), intval($_POST['alert_min_count']), intval($_POST['is_event']), intval($_POST['is_rating']), intval($_POST['rating_weight']), intval($_POST['is_tokens']), intval($_POST['tokens_required']), date("Y-m-d H:i:s"));
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			sql_pr("update $table_name set title=?, external_id=?, is_admin_flag=?, is_alert=?, alert_min_count=?, is_event=?, is_rating=?, rating_weight=?, is_tokens=?, tokens_required=? where $table_key_name=?", $_POST['title'], $_POST['external_id'], intval($_POST['is_admin_flag']), intval($_POST['is_alert']), intval($_POST['alert_min_count']), intval($_POST['is_event']), intval($_POST['is_rating']), intval($_POST['rating_weight']), intval($_POST['is_tokens']), intval($_POST['tokens_required']), $item_id);
			$_SESSION['messages'][] = $lang['common']['success_message_modified'];

			if (intval($_POST['is_admin_flag']) == 0)
			{
				sql_pr("update $config[tables_prefix]videos set admin_flag_id=0 where admin_flag_id=?", intval($_POST['is_admin_flag']));
				sql_pr("update $config[tables_prefix]albums set admin_flag_id=0 where admin_flag_id=?", intval($_POST['is_admin_flag']));
			}
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
		sql_pr("delete from $table_name where $table_key_name in ($row_select)");
		sql_pr("delete from $config[tables_prefix]flags_videos where $table_key_name in ($row_select)");
		sql_pr("delete from $config[tables_prefix]flags_albums where $table_key_name in ($row_select)");
		sql_pr("delete from $config[tables_prefix]flags_dvds where $table_key_name in ($row_select)");
		sql_pr("delete from $config[tables_prefix]flags_posts where $table_key_name in ($row_select)");
		sql_pr("delete from $config[tables_prefix]flags_playlists where $table_key_name in ($row_select)");
		sql_pr("update $config[tables_prefix]videos set admin_flag_id=0 where admin_flag_id in ($row_select)");
		sql_pr("update $config[tables_prefix]albums set admin_flag_id=0 where admin_flag_id in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$item_id = intval($_GET['item_id']);
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data_videos = mr2array(sql("select *, (select count(*) from $config[tables_prefix]flags_videos where $config[tables_prefix]flags_videos.$table_key_name=$table_name.$table_key_name) + (select count(*) from $config[tables_prefix]videos where $config[tables_prefix]videos.admin_flag_id=$table_name.$table_key_name) as objects from $table_name where group_id=1 $where $having order by $sort_by"));
$data_albums = mr2array(sql("select *, (select count(distinct album_id) from $config[tables_prefix]flags_albums where $config[tables_prefix]flags_albums.$table_key_name=$table_name.$table_key_name) + (select count(*) from $config[tables_prefix]albums where $config[tables_prefix]albums.admin_flag_id=$table_name.$table_key_name) as objects from $table_name where group_id=2 $where $having order by $sort_by"));
$data_dvds = mr2array(sql("select *, (select count(distinct dvd_id) from $config[tables_prefix]flags_dvds where $config[tables_prefix]flags_dvds.$table_key_name=$table_name.$table_key_name) as objects from $table_name where group_id=3 $where $having order by $sort_by"));
$data_posts = mr2array(sql("select *, (select count(distinct post_id) from $config[tables_prefix]flags_posts where $config[tables_prefix]flags_posts.$table_key_name=$table_name.$table_key_name) as objects from $table_name where group_id=4 $where $having order by $sort_by"));
$data_playlists = mr2array(sql("select *, (select count(distinct playlist_id) from $config[tables_prefix]flags_playlists where $config[tables_prefix]flags_playlists.$table_key_name=$table_name.$table_key_name) as objects from $table_name where group_id=5 $where $having order by $sort_by"));

$data = array();
$data[1] = $data_videos;
if ($config['installation_type'] == 4)
{
	$data[2] = $data_albums;
	$data[3] = $data_dvds;
}
if ($config['installation_type'] >= 3)
{
	$data[4] = $data_posts;
}
if ($config['installation_type'] >= 2)
{
	$data[5] = $data_playlists;
}

foreach ($data as $k => $v)
{
	foreach ($v as $k2 => $v2)
	{
		switch ($v2['group_id'])
		{
			case '1':
				$data[$k][$k2]['objects_link'] = 'videos.php?no_filter=true&se_flag_id=%id%';
				$data[$k][$k2]['objects_permission'] = 'videos|view';
				break;
			case '2':
				$data[$k][$k2]['objects_link'] = 'albums.php?no_filter=true&se_flag_id=%id%';
				$data[$k][$k2]['objects_permission'] = 'albums|view';
				break;
			case '3':
				$data[$k][$k2]['objects_link'] = 'dvds.php?no_filter=true&se_flag_id=%id%';
				$data[$k][$k2]['objects_permission'] = 'dvds|view';
				break;
			case '4':
				$data[$k][$k2]['objects_link'] = 'posts.php?no_filter=true&se_flag_id=%id%';
				$data[$k][$k2]['objects_permission'] = 'posts|view';
				break;
			case '5':
				$data[$k][$k2]['objects_link'] = 'playlists.php?no_filter=true&se_flag_id=%id%';
				$data[$k][$k2]['objects_permission'] = 'playlists|view';
				break;
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_categorization.tpl');

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('options', $options);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', count($data[1]) + @count($data[2]) + @count($data[3]) + @count($data[4]) + @count($data[5]));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['title'], $lang['categorization']['flag_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['categorization']['flag_add']);
} else
{
	$smarty->assign('page_title', $lang['categorization']['submenu_option_flags_list']);
}

$smarty->display("layout.tpl");
