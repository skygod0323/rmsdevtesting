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

$table_fields = array();

if ($list_grouping == "user")
{
	$table_fields[] = array('id' => 'user',                    'title' => $lang['stats']['users_purchases_field_user'],                    'is_default' => 1, 'type' => 'user');
	$table_fields[] = array('id' => 'reseller_code',           'title' => $lang['stats']['users_purchases_field_reseller_code'],           'is_default' => 0, 'type' => 'text');
	$table_fields[] = array('id' => 'videos_purchased',        'title' => $lang['stats']['users_purchases_field_videos_purchased'],        'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'videos_available',        'title' => $lang['stats']['users_purchases_field_videos_available'],        'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'albums_purchased',        'title' => $lang['stats']['users_purchases_field_albums_purchased'],        'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'albums_available',        'title' => $lang['stats']['users_purchases_field_albums_available'],        'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'subscriptions_purchased', 'title' => $lang['stats']['users_purchases_field_subscriptions_purchased'], 'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'subscriptions_available', 'title' => $lang['stats']['users_purchases_field_subscriptions_available'], 'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_spent',            'title' => $lang['stats']['users_purchases_field_tokens_spent'],            'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_revenue',          'title' => $lang['stats']['users_purchases_field_tokens_revenue'],          'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
} else
{
	$table_fields[] = array('id' => 'user',           'title' => $lang['stats']['users_purchases_field_user'],           'is_default' => 1, 'type' => 'user');
	$table_fields[] = array('id' => 'reseller_code',  'title' => $lang['stats']['users_purchases_field_reseller_code'],  'is_default' => 0, 'type' => 'text');
	$table_fields[] = array('id' => 'object',         'title' => $lang['stats']['users_purchases_field_object'],         'is_default' => 1, 'type' => 'object');
	$table_fields[] = array('id' => 'tokens_spent',   'title' => $lang['stats']['users_purchases_field_tokens_spent'],   'is_default' => 1, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'tokens_revenue', 'title' => $lang['stats']['users_purchases_field_tokens_revenue'], 'is_default' => 0, 'type' => 'number', 'ifdisable_zero' => 1);
	$table_fields[] = array('id' => 'added_date',     'title' => $lang['stats']['users_purchases_field_added_date'],     'is_default' => 1, 'type' => 'datetime');
	$table_fields[] = array('id' => 'expiry_date',    'title' => $lang['stats']['users_purchases_field_expiry_date'],    'is_default' => 1, 'type' => 'datetime', 'max_date_label' => $lang['common']['undefined']);
}

if ($list_grouping == "user")
{
	$sort_def_field = "user";
	$sort_def_direction = "desc";
} else
{
	$sort_def_field = "added_date";
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

$table_name = "$config[tables_prefix]users_purchases";
$now_date = date("Y-m-d H:i:s");

if ($list_grouping == "user")
{
	$table_selector = "$table_name.user_id, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, $config[tables_prefix]users.reseller_code, sum(case when $table_name.video_id>0 then 1 else 0 end) as videos_purchased, sum(case when $table_name.video_id>0 and $table_name.expiry_date>'$now_date' then 1 else 0 end) as videos_available, sum(case when $table_name.album_id>0 then 1 else 0 end) as albums_purchased, sum(case when $table_name.album_id>0 and $table_name.expiry_date>'$now_date' then 1 else 0 end) as albums_available, sum(case when $table_name.dvd_id>0 or $table_name.profile_id>0 then 1 else 0 end) as subscriptions_purchased, sum(case when ($table_name.dvd_id>0 or $table_name.profile_id>0) and $table_name.expiry_date>'$now_date' then 1 else 0 end) as subscriptions_available, sum(tokens) as tokens_spent, sum(tokens_revenue) as tokens_revenue";
	$table_summary_selector = "sum(case when $table_name.video_id>0 then 1 else 0 end) as videos_purchased, sum(case when $table_name.video_id>0 and $table_name.expiry_date>'$now_date' then 1 else 0 end) as videos_available, sum(case when $table_name.album_id>0 then 1 else 0 end) as albums_purchased, sum(case when $table_name.album_id>0 and $table_name.expiry_date>'$now_date' then 1 else 0 end) as albums_available, sum(case when $table_name.dvd_id>0 or $table_name.profile_id>0 then 1 else 0 end) as subscriptions_purchased, sum(case when ($table_name.dvd_id>0 or $table_name.profile_id>0) and $table_name.expiry_date>'$now_date' then 1 else 0 end) as subscriptions_available, sum(tokens) as tokens_spent, sum(tokens_revenue) as tokens_revenue";
	$table_summary_field_name = "user";
	$table_projector = "$table_name left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";
	$table_group_by = "$table_name.user_id";
} else
{
	$table_selector = "$table_name.*, $table_name.tokens as tokens_spent, $table_name.user_id, $config[tables_prefix]users.username as user, $config[tables_prefix]users.status_id as user_status_id, $config[tables_prefix]users.reseller_code, case when $table_name.video_id>0 then $table_name.video_id when $table_name.album_id>0 then $table_name.album_id when $table_name.dvd_id>0 then $table_name.dvd_id when $table_name.profile_id>0 then $table_name.profile_id end as object_id, case when $table_name.video_id>0 then 1 when $table_name.album_id>0 then 2 when $table_name.dvd_id>0 then 5 when $table_name.profile_id>0 then 20 end as object_type_id, case when $table_name.video_id>0 then coalesce($config[tables_prefix]videos.title, $config[tables_prefix]videos.video_id) when $table_name.album_id>0 then coalesce($config[tables_prefix]albums.title, $config[tables_prefix]albums.album_id) when $table_name.dvd_id>0 then $config[tables_prefix]dvds.title when $table_name.profile_id>0 then u2.username end as object";
	$table_summary_selector = "sum(tokens) as tokens_spent, sum(tokens_revenue) as tokens_revenue";
	$table_summary_field_name = "user";
	$table_projector = "$table_name left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id
							left join $config[tables_prefix]videos on $config[tables_prefix]videos.video_id=$table_name.video_id
							left join $config[tables_prefix]albums on $config[tables_prefix]albums.album_id=$table_name.album_id
							left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.dvd_id
							left join $config[tables_prefix]users u2 on u2.user_id=$table_name.profile_id
	";
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
$where = '';

if ($_SESSION['save'][$page_name]['se_user'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_user']);
	$where .= " and $config[tables_prefix]users.username='$q'";
	$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_reseller_code'] != '')
{
	$q = sql_escape($_SESSION['save'][$page_name]['se_reseller_code']);
	$where .= " and $config[tables_prefix]users.reseller_code='$q'";
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

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'user')
{
	$sort_by = "$config[tables_prefix]users.username";
} elseif ($sort_by == 'reseller_code')
{
	$sort_by = "$config[tables_prefix]users.reseller_code";
} elseif ($sort_by == 'object')
{
	$sort_by = "$table_name.video_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.album_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.dvd_id " . $_SESSION['save'][$page_name]['sort_direction'] . ", $table_name.profile_id";
} elseif ($sort_by == 'added_date' || $sort_by == 'expiry_date')
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

if ($table_summary_selector != '' && $table_summary_field_name != '')
{
	$total[0] = mr2array_single(sql("select $table_summary_selector from $table_projector $where limit 1"));
	$total[0][$table_summary_field_name] = $lang['common']['total'];
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_stats.tpl');

$smarty->assign('data', $data);
$smarty->assign('total', $total);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_summary_field_name', $table_summary_field_name);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name][$list_grouping]['from'], "$page_name?", 14));

$smarty->assign('page_title', $lang['stats']['submenu_option_stats_users_purchases']);

$smarty->display("layout.tpl");
