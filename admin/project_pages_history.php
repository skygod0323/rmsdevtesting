<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/setup_smarty_site.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$table_fields = array();
$table_fields[] = array('id' => 'change_id',  'title' => $lang['website_ui']['page_history_field_change_id'],  'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'object',     'title' => $lang['website_ui']['page_history_field_object'],     'is_default' => 1, 'type' => 'longtext', 'link' => 'custom', 'link_id' => 'object_id', 'link_is_editor' => 1, 'permission' => 'website_ui|view');
$table_fields[] = array('id' => 'username',   'title' => $lang['website_ui']['page_history_field_author'],     'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'version',    'title' => $lang['website_ui']['page_history_field_version'],    'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'added_date', 'title' => $lang['website_ui']['page_history_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "added_date";
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
$search_fields[] = array('id' => 'filenames', 'title' => $lang['website_ui']['page_history_filter_search_in_names']);
$search_fields[] = array('id' => 'contents',  'title' => $lang['website_ui']['page_history_filter_search_in_content']);

$table_key_name = "change_id";
$table_name = "$config[tables_prefix_multi]file_history";
$table_selector = "change_id, path, version, username, added_date";

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
	$_SESSION['save'][$page_name]['se_username'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_username']))
	{
		$_SESSION['save'][$page_name]['se_username'] = trim($_GET['se_username']);
	}
	if (isset($_GET['se_date_from_Day']) && isset($_GET['se_date_from_Month']) && isset($_GET['se_date_from_Year']))
	{
		if (intval($_GET['se_date_from_Day']) > 0 && intval($_GET['se_date_from_Month']) > 0 && intval($_GET['se_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_date_from'] = intval($_GET['se_date_from_Year']) . "-" . intval($_GET['se_date_from_Month']) . "-" . intval($_GET['se_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_date_from'] = "";
		}
	}
	if (isset($_GET['se_date_to_Day']) && isset($_GET['se_date_to_Month']) && isset($_GET['se_date_to_Year']))
	{
		if (intval($_GET['se_date_to_Day']) > 0 && intval($_GET['se_date_to_Month']) > 0 && intval($_GET['se_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_date_to'] = intval($_GET['se_date_to_Year']) . "-" . intval($_GET['se_date_to_Month']) . "-" . intval($_GET['se_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_date_to'] = "";
		}
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
			if ($search_field['id'] == 'filenames')
			{
				$where_search .= " or $table_name.path like '%$q%'";
			} elseif ($search_field['id'] == 'contents')
			{
				$where_search .= " or $table_name.file_content like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_username'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_username']);
	$where .= " and username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and added_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
	$prev_version = mr2array_single(sql_pr("select * from $table_name where path=? and version<? order by version desc limit 1", $_POST['path'], $_POST['version']));
	$_POST['prev_version'] = $prev_version;
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name where version>0 $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}

$data = mr2array(sql("select $table_selector from $table_name where version>0 $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

$pages = get_site_pages();
$page_ids = array();
foreach ($pages as $page)
{
	$page_ids[$page['external_id']] = $page;
}

$templates_data = get_site_parsed_templates();

$smarty_site = new mysmarty_site();
$site_templates_path = rtrim($smarty_site->template_dir, '/');

$templates_map = array();

$templates = get_contents_from_dir($site_templates_path, 1);
foreach ($templates as $template)
{
	if (strtolower(end(explode(".", $template))) !== 'tpl')
	{
		continue;
	}

	$temp = explode(".", $template);
	$page_external_id = $temp[0];
	if (isset($page_ids[$page_external_id]))
	{
		$page = $page_ids[$page_external_id];
		$templates_map["$site_templates_path/$page_external_id.tpl"] = array('type' => 'page', 'page_id' => $page_external_id, 'page_name' => $page['title']);
		if (isset($templates_data["$page_external_id.tpl"]))
		{
			foreach ($templates_data["$page_external_id.tpl"]['block_inserts'] as $block_insert)
			{
				$block_id = trim($block_insert['block_id']);
				$block_name = trim($block_insert['block_name']);
				if (!preg_match($regexp_valid_external_id, $block_id) || !preg_match($regexp_valid_block_name, $block_name))
				{
					continue;
				}
				$block_internal_name = strtolower(str_replace(" ", "_", $block_name));
				$templates_map["$site_templates_path/blocks/$page_external_id/{$block_id}_{$block_internal_name}.tpl"] = array('type' => 'block', 'page_id' => $page_external_id, 'page_name' => $page['title'], 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name);
			}
		}
	} else
	{
		$templates_map["$site_templates_path/$page_external_id.tpl"] = array('type' => 'component', 'component_name' => "$page_external_id.tpl");
	}
}

if (is_file("$config[project_path]/admin/data/config/\$global/config.dat"))
{
	$temp = explode("||", @file_get_contents("$config[project_path]/admin/data/config/\$global/config.dat"));
	$global_blocks = explode("|AND|", trim($temp[2]));
	foreach ($global_blocks as $global_block)
	{
		if ($global_block == '')
		{
			continue;
		}
		$block_id = substr($global_block, 0, strpos($global_block, "[SEP]"));
		$block_internal_name = substr($global_block, strpos($global_block, "[SEP]") + 5);
		$block_name = ucwords(str_replace('_', ' ', $block_internal_name));

		$templates_map["$site_templates_path/blocks/\$global/{$block_id}_$block_internal_name.tpl"] = array('type' => 'global', 'block_id' => $block_id, 'block_name' => $block_name, 'block_internal_name' => $block_internal_name);
	}
}

foreach ($data as $k => $v)
{
	$object = $templates_map["$config[project_path]/" . trim($v['path'], '/')];
	if (isset($object))
	{
		switch ($object['type'])
		{
			case 'page':
				$data[$k]['object'] = str_replace('%1%', $object['page_name'], $lang['website_ui']['page_history_field_object_page']);
				$data[$k]['object_id'] = $object['page_id'];
				$data[$k]['object_link'] = "project_pages.php?action=change&item_id=%id%";
				break;
			case 'block':
				$data[$k]['object'] = str_replace('%1%', $object['block_name'], str_replace('%2%', $object['page_name'], $lang['website_ui']['page_history_field_object_block']));
				$data[$k]['object_id'] = "$object[page_id]||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			case 'component':
				$data[$k]['object'] = str_replace('%1%', $object['component_name'], $lang['website_ui']['page_history_field_object_component']);
				$data[$k]['object_id'] = $object['component_name'];
				$data[$k]['object_link'] = "project_pages_components.php?action=change&item_id=%id%";
				break;
			case 'global':
				$data[$k]['object'] = str_replace('%1%', $object['block_name'], $lang['website_ui']['page_history_field_object_global_block']);
				$data[$k]['object_id'] = "\$global||$object[block_id]||$object[block_internal_name]";
				$data[$k]['object_link'] = "project_pages.php?action=change_block&item_id=%id%&item_name=$object[block_name]";
				break;
			default:
				$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']);
				if (strpos($v['path'], 'admin/include/') === false)
				{
					if (is_file("$config[project_path]$v[path]"))
					{
						$data[$k]['object_id'] = 'test';
						$data[$k]['object_link'] = "$config[project_url]$v[path]";
					} else
					{
						$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file_deleted']);
					}
				}
				break;
		}
	} else
	{
		$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file']);
		if (strpos($v['path'], 'admin/include/') === false)
		{
			if (is_file("$config[project_path]$v[path]"))
			{
				$data[$k]['object_id'] = 'test';
				$data[$k]['object_link'] = "$config[project_url]$v[path]";
			} else
			{
				$data[$k]['object'] = str_replace('%1%', $v['path'], $lang['website_ui']['page_history_field_object_file_deleted']);
			}
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');
$smarty->assign('list_usernames', mr2array_list(sql("select distinct username from $table_name")));

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

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

$smarty->assign('page_title', $lang['website_ui']['submenu_option_theme_history']);

$smarty->display("layout.tpl");