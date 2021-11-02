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

$list_status_values = array(
	0 => $lang['settings']['import_field_status_scheduled'],
	1 => $lang['settings']['import_field_status_in_process'],
	2 => $lang['settings']['import_field_status_completed'],
	3 => $lang['settings']['import_field_status_cancelled'],
);

$list_type_values = array(
	1 => $lang['settings']['import_field_type_videos'],
	2 => $lang['settings']['import_field_type_albums'],
);

$table_fields[] = array('id' => 'import_id',  'title' => $lang['settings']['import_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'status_id',  'title' => $lang['settings']['import_field_status'],     'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'type_id',    'title' => $lang['settings']['import_field_type'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_type_values);
$table_fields[] = array('id' => 'threads',    'title' => $lang['settings']['import_field_threads'],    'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'objects',    'title' => $lang['settings']['import_field_objects'],    'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'errors',     'title' => $lang['settings']['import_field_errors'],     'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date', 'title' => $lang['settings']['import_field_added_date'], 'is_default' => 1, 'type' => 'datetime');

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

$table_name = "$config[tables_prefix]background_imports";
$table_key_name = "import_id";

$table_selector = "*, (select count(*) from $config[tables_prefix]background_imports_data where $table_key_name=$table_name.$table_key_name) as objects, (select count(*) from $config[tables_prefix]background_imports_data where $table_key_name=$table_name.$table_key_name and status_id=1 and object_id=0) as errors";

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
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_type_id'] = '';
	$_SESSION['save'][$page_name]['se_text'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_type_id']))
	{
		$_SESSION['save'][$page_name]['se_type_id'] = intval($_GET['se_type_id']);
	}
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = $_GET['se_text'];
	}
}

$table_filtered = 0;
$where = '';

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save'][$page_name]['se_text'])));
	$where .= " and $table_key_name in (select $table_key_name from $config[tables_prefix]background_imports_data where data like '%$q%')";
}

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and status_id=1";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '2')
{
	$where .= " and status_id=2";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '3')
{
	$where .= " and status_id=3";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_type_id'] > 0)
{
	$where .= " and type_id=" . intval($_SESSION['save'][$page_name]['se_type_id']);
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// table actions
// =====================================================================================================================

if ($_REQUEST['action'] == 'import_log' && intval($_REQUEST['item_id']) > 0)
{
	header("Content-Type: text/plain; charset=utf8");

	$import_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_REQUEST['item_id'])));
	if (count($import_data) < 1)
	{
		die;
	}

	$task_id = intval($import_data['task_id']);

	if (isset($_REQUEST['download']))
	{
		header("Content-Disposition: attachment; filename=\"$task_id.txt\"");
	}
	if (is_file("$config[project_path]/admin/logs/tasks/$task_id.txt"))
	{
		readfile("$config[project_path]/admin/logs/tasks/$task_id.txt");
		echo "\n";
	}
	if (is_file("$config[project_path]/admin/logs/tasks/{$task_id}_1.txt"))
	{
		for ($i = 1; $i < 1000; $i++)
		{
			if (is_file("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt"))
			{
				$log_size = sprintf("%.0f", filesize("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt"));
				if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
				{
					$fh = fopen("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt", "r");
					fseek($fh, $log_size - 1024 * 1024);
					echo "Showing last 1MB of file...\n\n";
					echo fread($fh, 1024 * 1024 + 1);
				} else
				{
					readfile("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt");
				}
				echo "\n";
			} else
			{
				break;
			}
		}
	}
	die;
}

if ($_REQUEST['action'] == 'new_import' && intval($_REQUEST['item_id']) > 0)
{
	$import_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", intval($_REQUEST['item_id'])));
	if (count($import_data) < 1)
	{
		die;
	}

	$rnd = mt_rand(10000000, 99999999);
	for ($i = 0; $i < 999; $i++)
	{
		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_imports where import_id=?", $rnd)) > 0)
		{
			$rnd = mt_rand(10000000, 99999999);
		} else
		{
			break;
		}
	}

	file_put_contents("$config[temporary_path]/import-$rnd.dat", $import_data['options']);
	if ($import_data['type_id'] == 1)
	{
		return_ajax_success("videos_import.php?action=back_import&amp;import_id=$rnd");
	} elseif ($import_data['type_id'] == 2)
	{
		return_ajax_success("albums_import.php?action=back_import&amp;import_id=$rnd");
	}
	die;
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector from $table_name where $table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$import_options = unserialize($_POST['options']);
	$line_separator = "\n";
	if (is_array($import_options) && $import_options['line_separator'])
	{
		$line_separator = $import_options['line_separator'];
		$line_separator = str_replace("\\r", "\r", $line_separator);
		$line_separator = str_replace("\\n", "\n", $line_separator);
		$line_separator = str_replace("\\t", "\t", $line_separator);
	}

	$_POST['data'] = implode($line_separator, mr2array_list(sql_pr("select data from $config[tables_prefix]background_imports_data where $table_key_name=?", $_POST[$table_key_name])));

	$_POST['log'] = '';
	$task_id = intval($_POST['task_id']);
	if (is_file("$config[project_path]/admin/logs/tasks/$task_id.txt"))
	{
		$_POST['log'] .= file_get_contents("$config[project_path]/admin/logs/tasks/$task_id.txt");
		$_POST['log'] .= "\n";
	}
	if (is_file("$config[project_path]/admin/logs/tasks/{$task_id}_1.txt"))
	{
		for ($i = 1; $i < 1000; $i++)
		{
			if (is_file("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt"))
			{
				$log_size = sprintf("%.0f", filesize("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt"));
				if ($log_size > 1024 * 1024)
				{
					$fh = fopen("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt", "r");
					fseek($fh, $log_size - 1024 * 1024);
					$_POST['log'] .= "Showing last 1MB of file...\n\n";
					$_POST['log'] .= fread($fh, 1024 * 1024 + 1);
				} else
				{
					$_POST['log'] .= file_get_contents("$config[project_path]/admin/logs/tasks/{$task_id}_$i.txt");
					$_POST['log'] .= "\n";
				}
			} else
			{
				break;
			}
		}
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$total_num = mr2number(sql("select count(*) from $table_name $where"));
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}
$data = mr2array(sql("select $table_selector from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));

foreach ($data as $k => $v)
{
	if (is_file("$config[project_path]/admin/logs/tasks/$v[task_id].txt") || is_file("$config[project_path]/admin/logs/tasks/$v[task_id]_1.txt"))
	{
		$data[$k]['has_import_log'] = 1;
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST[$table_key_name], $lang['settings']['import_view']));
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_imports_log']);
}

$smarty->display("layout.tpl");
