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

if (isset($_GET['se_group_by']))
{
	$_SESSION['save'][$page_name]['se_group_by'] = $_GET['se_group_by'];
}
if ($_SESSION['save'][$page_name]['se_group_by'] <> 'user')
{
	$_SESSION['save'][$page_name]['se_group_by'] = "log";
}
$list_grouping = $_SESSION['save'][$page_name]['se_group_by'];

$list_status_values = array(
	0 => $lang['stats']['users_logins_field_status_disabled'],
	1 => $lang['stats']['users_logins_field_status_not_confirmed'],
	2 => $lang['stats']['users_logins_field_status_active'],
	3 => $lang['stats']['users_logins_field_status_premium'],
	4 => $lang['stats']['users_logins_field_status_anonymous'],
	6 => $lang['stats']['users_logins_field_status_webmaster'],
);

$list_countries = mr2array(sql_pr("select * from $config[tables_prefix]list_countries where language_code=? order by title asc", $lang['system']['language_code']));

$list_country_values = array();
$list_country_values[0] = ' ';
foreach ($list_countries as $country)
{
	$list_country_values[$country['country_code']] = $country['title'];
}

$table_fields = array();

if ($list_grouping == "user")
{
	$table_fields[] = array('id' => 'user',             'title' => $lang['stats']['users_logins_field_user'],               'is_default' => 1, 'type' => 'user');
	$table_fields[] = array('id' => 'status_id',        'title' => $lang['stats']['users_logins_field_status'],             'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
	$table_fields[] = array('id' => 'reseller_code',    'title' => $lang['stats']['users_logins_field_reseller_code'],      'is_default' => 0, 'type' => 'text');
	$table_fields[] = array('id' => 'unique_logins',    'title' => $lang['stats']['users_logins_field_unique_logins'],      'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_ips',       'title' => $lang['stats']['users_logins_field_unique_ips'],         'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_countries', 'title' => $lang['stats']['users_logins_field_unique_countries'],   'is_default' => 1, 'type' => 'number');
	$table_fields[] = array('id' => 'unique_agents',    'title' => $lang['stats']['users_logins_field_unique_agents'],      'is_default' => 1, 'type' => 'number');
} else
{
	$table_fields[] = array('id' => 'user',          'title' => $lang['stats']['users_logins_field_user'],          'is_default' => 1, 'type' => 'user');
	$table_fields[] = array('id' => 'status_id',     'title' => $lang['stats']['users_logins_field_status'],        'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
	$table_fields[] = array('id' => 'reseller_code', 'title' => $lang['stats']['users_logins_field_reseller_code'], 'is_default' => 0, 'type' => 'text');
	$table_fields[] = array('id' => 'login_date',    'title' => $lang['stats']['users_logins_field_login_date'],    'is_default' => 1, 'type' => 'datetime');
	if ($config['safe_mode'] == 'false')
	{
		$table_fields[] = array('id' => 'ip',        'title' => $lang['stats']['users_logins_field_ip'],            'is_default' => 1, 'type' => 'ip');
	}
	$table_fields[] = array('id' => 'country_code',  'title' => $lang['stats']['users_logins_field_country'],       'is_default' => 1, 'type' => 'choice', 'values' => $list_country_values);
	$table_fields[] = array('id' => 'user_agent',    'title' => $lang['stats']['users_logins_field_user_agent'],    'is_default' => 1, 'type' => 'text');
}

if ($list_grouping == "user")
{
	$sort_def_field = "user";
	$sort_def_direction = "desc";
} else
{
	$sort_def_field = "login_date";
	$sort_def_direction = "desc";
}

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
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 1;
		} else
		{
			$_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']] = 0;
		}
	}
	if (is_array($_SESSION['save'][$page_name][$list_grouping]['grid_columns']))
	{
		$table_fields[$k]['is_enabled'] = intval($_SESSION['save'][$page_name][$list_grouping]['grid_columns'][$field['id']]);
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

$table_name = "$config[tables_prefix]log_logins_users";

if ($list_grouping == "user")
{
	$table_selector = "u1.user_id, $table_name.username as user, u1.status_id as user_status_id, u1.status_id, u1.reseller_code, count($table_name.ip) as unique_logins, count(distinct $table_name.ip) as unique_ips, count(distinct $table_name.country_code) as unique_countries, count(distinct $table_name.user_agent) as unique_agents";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
	$table_group_by = "$table_name.user_id";
} else
{
	$table_selector = "$table_name.*, u1.user_id as user_id, $table_name.username as user, u1.status_id as user_status_id, u1.status_id, u1.reseller_code";
	$table_projector = "$table_name left join $config[tables_prefix]users u1 on u1.user_id=$table_name.user_id";
	$table_group_by = "";
}

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

if (in_array($_GET['sort_by'], $sort_array))
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $_GET['sort_by'];
}
if ($_SESSION['save'][$page_name][$list_grouping]['sort_by'] == '')
{
	$_SESSION['save'][$page_name][$list_grouping]['sort_by'] = $sort_def_field;
	$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $sort_def_direction;
} else
{
	if (in_array($_GET['sort_direction'], array('desc', 'asc')))
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = $_GET['sort_direction'];
	}
	if ($_SESSION['save'][$page_name][$list_grouping]['sort_direction'] == '')
	{
		$_SESSION['save'][$page_name][$list_grouping]['sort_direction'] = 'desc';
	}
}
$_SESSION['save'][$page_name]['sort_by'] = $_SESSION['save'][$page_name][$list_grouping]['sort_by'];
$_SESSION['save'][$page_name]['sort_direction'] = $_SESSION['save'][$page_name][$list_grouping]['sort_direction'];

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
	$_SESSION['save'][$page_name][$list_grouping]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name][$list_grouping]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_reseller_code'] = '';
	$_SESSION['save'][$page_name]['se_period_id'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_reseller_code']))
	{
		$_SESSION['save'][$page_name]['se_reseller_code'] = trim($_GET['se_reseller_code']);
	}
	if (isset($_GET['se_period_id']))
	{
		$_SESSION['save'][$page_name]['se_period_id'] = intval($_GET['se_period_id']);
		switch ($_SESSION['save'][$page_name]['se_period_id'])
		{
			case 0:
				$_SESSION['save'][$page_name]['se_date_from'] = '';
				$_SESSION['save'][$page_name]['se_date_to'] = '';
				break;
			case 1:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 6);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 2:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-d', time() - 86400 * 30);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 3:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1');
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d');
				break;
			case 4:
				$_SESSION['save'][$page_name]['se_date_from'] = date('Y-m-1', strtotime(date('Y-m-1 00:00:00')) - 86400);
				$_SESSION['save'][$page_name]['se_date_to'] = date('Y-m-d', strtotime(date('Y-m-1 00:00:00')) - 86400);
				break;
			case 5:
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
				break;
		}
	}
}

$table_filtered = 0;
$where = "and $table_name.is_failed=0";

if ($_SESSION['save'][$page_name]['se_user'] <> "")
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
	$where .= " and $table_name.username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_status_id'] != '')
{
	$where .= " and u1.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_reseller_code'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_reseller_code']);
	$where .= " and u1.reseller_code='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_from'] <> "")
{
	$where .= " and $table_name.login_date>='" . $_SESSION['save'][$page_name]['se_date_from'] . "'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_date_to'] <> "")
{
	$where .= " and $table_name.login_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_date_to']) + 86399) . "'";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$table_name.username";
} elseif ($sort_by == 'status_id')
{
	$sort_by = "u1.status_id";
} elseif ($sort_by == 'reseller_code')
{
	$sort_by = "u1.reseller_code";
} elseif ($sort_by == 'unique_logins' || $sort_by == 'unique_ips' || $sort_by == 'unique_countries' || $sort_by == 'unique_agents')
{
	$sort_by = "$sort_by";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($table_group_by != '')
{
	$total_num = mr2number(sql("select count(distinct $table_group_by) from $table_projector $where"));
} else
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
}

if (($_SESSION['save'][$page_name][$list_grouping]['from'] >= $total_num || $_SESSION['save'][$page_name][$list_grouping]['from'] < 0) || ($_SESSION['save'][$page_name][$list_grouping]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name][$list_grouping]['from'] = 0;
}
if ($table_group_by != '')
{
	$data = mr2array(sql("select $table_selector from $table_projector $where group by $table_group_by order by $sort_by limit " . $_SESSION['save'][$page_name][$list_grouping]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
} else
{
	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name][$list_grouping]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name][$list_grouping]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users_logins']);

$smarty->display("layout.tpl");
