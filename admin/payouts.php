<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions_admin.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$list_all_award_types = array(
	1 => $lang['users']['payout_field_awards_type_signup'],
	15 => $lang['users']['payout_field_awards_type_login'],
	2 => $lang['users']['payout_field_awards_type_avatar'],
	16 => $lang['users']['payout_field_awards_type_cover'],
	3 => $lang['users']['payout_field_awards_type_comment'],
	4 => $lang['users']['payout_field_awards_type_video_upload'],
	5 => $lang['users']['payout_field_awards_type_album_upload'],
	9 => $lang['users']['payout_field_awards_type_post_upload'],
	6 => $lang['users']['payout_field_awards_type_video_sale'],
	7 => $lang['users']['payout_field_awards_type_album_sale'],
	13 => $lang['users']['payout_field_awards_type_profile_sale'],
	14 => $lang['users']['payout_field_awards_type_dvd_sale'],
	8 => $lang['users']['payout_field_awards_type_referral'],
	10 => $lang['users']['payout_field_awards_type_donation'],
	11 => $lang['users']['payout_field_awards_type_video_views'],
	12 => $lang['users']['payout_field_awards_type_album_views'],
	17 => $lang['users']['payout_field_awards_type_embed_views'],
);

$list_status_values = array(
	1 => $lang['users']['payout_field_status_in_progress'],
	2 => $lang['users']['payout_field_status_closed'],
	3 => $lang['users']['payout_field_status_cancelled'],
);

$table_fields = array();
$table_fields[] = array('id' => 'payout_id',           'title' => $lang['users']['payout_field_id'],               'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'description',         'title' => $lang['users']['payout_field_description'],      'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'status_id',           'title' => $lang['users']['payout_field_status'],           'is_default' => 1, 'type' => 'choice', 'values' => $list_status_values);
$table_fields[] = array('id' => 'conversion',          'title' => $lang['users']['payout_field_conversion'],       'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'tokens',              'title' => $lang['users']['payout_field_tokens'],           'is_default' => 1, 'type' => 'number');
$table_fields[] = array('id' => 'amount',              'title' => $lang['users']['payout_field_amount'],           'is_default' => 1, 'type' => 'currency');
$table_fields[] = array('id' => 'conversion_currency', 'title' => $lang['users']['payout_field_currency'],         'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'comment',             'title' => $lang['users']['payout_field_comment'],          'is_default' => 1, 'type' => 'longtext');
$table_fields[] = array('id' => 'min_tokens_limit',    'title' => $lang['users']['payout_field_min_tokens_limit'], 'is_default' => 0, 'type' => 'number');
$table_fields[] = array('id' => 'added_date',          'title' => $lang['users']['payout_field_added_date'],       'is_default' => 1, 'type' => 'datetime');

$sort_def_field = "payout_id";
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

$table_name = "$config[tables_prefix]users_payouts";
$table_key_name = "payout_id";
$table_selector = "$table_name.*, $table_name.conversion_currency as amount_currency";
$table_projector = "$table_name";

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
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_user'] = '';
	$_SESSION['save'][$page_name]['se_date_from'] = '';
	$_SESSION['save'][$page_name]['se_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_user']))
	{
		$_SESSION['save'][$page_name]['se_user'] = trim($_GET['se_user']);
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

$where = '';
$table_filtered = 0;

switch ($_SESSION['save'][$page_name]['se_status_id'])
{
	case '1':
	case '2':
	case '3':
		$where .= " and $table_name.status_id=" . intval($_SESSION['save'][$page_name]['se_status_id']);
		$table_filtered = 1;
}

if ($_SESSION['save'][$page_name]['se_user'] <> "")
{
	$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save'][$page_name]['se_user']));
	$where .= " and exists (select user_id from $config[tables_prefix]users_payouts_users where $table_key_name=$table_name.$table_key_name and user_id=$user_id)";
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

$sort_by = $_SESSION['save'][$page_name]['sort_by'] . ' ' . $_SESSION['save'][$page_name]['sort_direction'];

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

	$award_types = @array_map('intval', $_POST['award_types']);

	$excluded_users = @array_map('intval', $_POST['excluded_users']);
	$excluded_users_str = '0';
	if (count($excluded_users) > 0)
	{
		$excluded_users_str = implode(',', $excluded_users);
	}

	$included_users = @array_map('intval', $_POST['included_users']);
	$included_users_str = '0';
	if (count($included_users) > 0)
	{
		$included_users_str = implode(',', $included_users);
	}

	if ($_POST['action'] == 'add_new_complete')
	{
		validate_field('empty_float', $_POST['conversion'], $lang['users']['payout_field_conversion']);
		validate_field('empty_int', $_POST['min_tokens_limit'], $lang['users']['payout_field_min_tokens_limit']);

		if (count($award_types) == 0)
		{
			$errors[] = get_aa_error('payout_nothing_to_pay');
		} else {
			$award_types_str = implode(',', $award_types);

			$account_field_name = '';
			switch ($_POST['gateway'])
			{
				case 'manual':
					$account_field_name = 'username';
					break;
				case 'paypal':
					$account_field_name = 'account_paypal';
					break;
			}

			$where_users = "and u.user_id not in ($excluded_users_str)";
			if (count($included_users) > 0)
			{
				$where_users = "and u.user_id in ($included_users_str)";
			}

			$awards_to_pay = mr2array(sql("
					select u.user_id, u.tokens_available, au.award_id, au.tokens_granted
					from $config[tables_prefix]users u inner join $config[tables_prefix]log_awards_users au on u.user_id=au.user_id
					where au.payout_id=0 and au.award_type in ($award_types_str) $where_users and u.$account_field_name!=''
					order by u.user_id asc, au.tokens_granted desc
			"));

			if (count($awards_to_pay) == 0)
			{
				$errors[] = get_aa_error('payout_nothing_to_pay');
			} else
			{
				$users_to_pay = array();
				foreach ($awards_to_pay as $award)
				{
					if (!isset($users_to_pay[$award['user_id']]))
					{
						$users_to_pay[$award['user_id']] = array(
							'user_id' => $award['user_id'],
							'tokens_available' => $award['tokens_available']
						);
					}
					$users_to_pay[$award['user_id']]['awards'][] = array(
						'award_id' => $award['award_id'],
						'tokens_granted' => $award['tokens_granted']
					);
				}

				$has_payments = false;
				foreach ($users_to_pay as $user_id => $payment_info)
				{
					foreach ($payment_info['awards'] as $award_info)
					{
						if ($award_info['tokens_granted'] <= $payment_info['tokens_available'])
						{
							$has_payments = true;
							break 2;
						}
					}
				}

				if (!$has_payments)
				{
					$errors[] = get_aa_error('payout_nothing_to_pay');
				}
			}
		}
	} else
	{
		validate_field('empty_int', $_POST['status_id'], $lang['users']['payout_field_status']);
	}

	if (!is_array($errors))
	{
		if ($_POST['action'] == 'add_new_complete')
		{
			$update_array = array();
			$update_array['status_id'] = 1;
			$update_array['description'] = $_POST['description'];
			$update_array['award_types'] = implode(',', array_unique($award_types));
			$update_array['conversion'] = floatval($_POST['conversion']);
			$update_array['conversion_currency'] = $_POST['conversion_currency'];
			$update_array['min_tokens_limit'] = intval($_POST['min_tokens_limit']);
			$update_array['gateway'] = $_POST['gateway'];
			$update_array['comment'] = $_POST['comment'];
			$update_array['excluded_users'] = implode(', ', mr2array_list(sql("select username from $config[tables_prefix]users where user_id in ($excluded_users_str)")));
			$update_array['included_users'] = implode(', ', mr2array_list(sql("select username from $config[tables_prefix]users where user_id in ($included_users_str)")));
			$update_array['added_date'] = date("Y-m-d H:i:s");

			$item_id = sql_insert("insert into $table_name set ?%", $update_array);

			$total_tokens_paid = 0;
			foreach ($users_to_pay as $user_id => $payment_info)
			{
				$tokens_available = $payment_info['tokens_available'];
				$tokens_to_pay = 0;
				$awards_to_pay = array();
				foreach ($payment_info['awards'] as $award_info)
				{
					if ($award_info['tokens_granted'] <= $tokens_available)
					{
						$awards_to_pay[] = $award_info['award_id'];
						$tokens_to_pay += $award_info['tokens_granted'];
						$tokens_available -= $award_info['tokens_granted'];
					}
				}

				if ($tokens_to_pay >= intval($_POST['min_tokens_limit']))
				{
					if (sql_update("update $config[tables_prefix]users set tokens_available=greatest(0, cast(tokens_available as signed)-?) where tokens_available>=? and user_id=?", $tokens_to_pay, $tokens_to_pay, $user_id) > 0)
					{
						sql_pr("insert into $config[tables_prefix]users_payouts_users set payout_id=?, user_id=?, tokens=?, amount=?, amount_currency=?, added_date=?",
							$item_id, $user_id, $tokens_to_pay, $tokens_to_pay * floatval($_POST['conversion']), $_POST['conversion_currency'], date("Y-m-d H:i:s")
						);

						foreach ($awards_to_pay as $award_id)
						{
							sql_pr("update $config[tables_prefix]log_awards_users set payout_id=? where award_id=?", $item_id, $award_id);
						}

						$total_tokens_paid += $tokens_to_pay;
					}
				}
			}

			sql_pr("update $table_name set tokens=?, amount=? where $table_key_name=?", $total_tokens_paid, $total_tokens_paid * floatval($_POST['conversion']), $item_id);

			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$old_payout_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

			$update_array = array();
			if ($old_payout_data['status_id'] == 1)
			{
				$update_array['status_id'] = intval($_POST['status_id']);
				$update_array['comment'] = $_POST['comment'];
			}
			$update_array['description'] = $_POST['description'];

			if ($update_array['status_id'] == 3)
			{
				$user_payments = mr2array(sql_pr("select * from $config[tables_prefix]users_payouts_users where $table_key_name=?", $item_id));
				foreach ($user_payments as $user_payment)
				{
					sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $user_payment['tokens'], $user_payment['user_id']);
				}
				sql_pr("update $config[tables_prefix]log_awards_users set payout_id=0 where $table_key_name=?", $item_id);
				sql_pr("delete from $config[tables_prefix]users_payouts_users where $table_key_name=?", $item_id);

				$update_array['tokens'] = 0;
				$update_array['amount'] = 0;
			} elseif (isset($_POST['delete_user']) && count($_POST['delete_user']) > 0)
			{
				$tokens_returned = 0;
				foreach ($_POST['delete_user'] as $user_id)
				{
					$user_payments = mr2array(sql_pr("select * from $config[tables_prefix]users_payouts_users where $table_key_name=? and user_id=?", $item_id, $user_id));
					foreach ($user_payments as $user_payment)
					{
						sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $user_payment['tokens'], $user_payment['user_id']);
						$tokens_returned += $user_payment['tokens'];
					}
					sql_pr("update $config[tables_prefix]log_awards_users set payout_id=0 where $table_key_name=? and user_id=?", $item_id, $user_id);
					sql_pr("delete from $config[tables_prefix]users_payouts_users where $table_key_name=? and user_id=?", $item_id, $user_id);
				}
				if ($tokens_returned > 0)
				{
					$update_array['tokens'] = max(0, $old_payout_data['tokens'] - $tokens_returned);
					$update_array['amount'] = $update_array['tokens'] * floatval($old_payout_data['conversion']);
				}
			}

			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

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

	$_POST['award_types'] = array_map('trim', explode(',', $_POST['award_types']));

	$account_field_name = '';
	switch ($_POST['gateway'])
	{
		case 'manual':
			$account_field_name = '\'\'';
			break;
		case 'paypal':
			$account_field_name = 'u.account_paypal';
			break;
	}

	$_POST['user_payments'] = mr2array(sql_pr("
			select upu.*, u.username, $account_field_name as account
			from $config[tables_prefix]users_payouts_users upu left join $config[tables_prefix]users u on upu.user_id=u.user_id
			where upu.$table_key_name=?
	", intval($_GET['item_id'])));
}

if ($_GET['action'] == 'add_new')
{
	$last_payout = mr2array_single(sql_pr("select $table_selector from $table_projector order by added_date desc limit 1"));
	if (count($last_payout) > 0)
	{
		$_POST['award_types'] = array_map('intval', explode(',', $last_payout['award_types']));
		$_POST['conversion'] = $last_payout['conversion'];
		$_POST['conversion_currency'] = $last_payout['conversion_currency'];
		$_POST['min_tokens_limit'] = $last_payout['min_tokens_limit'];
		$_POST['gateway'] = $last_payout['gateway'];

		if (intval($_POST['min_tokens_limit']) == 0)
		{
			$_POST['min_tokens_limit'] = '1';
		}

		$excluded_users = array_map('trim', explode(',', $last_payout['excluded_users']));
		if (count($excluded_users) > 0)
		{
			$excluded_users_str = '\'\'';
			foreach ($excluded_users as $excluded_user)
			{
				$excluded_users_str .= ',\'' . sql_escape($excluded_user) . '\'';
			}
			$_POST['excluded_users'] = mr2array(sql("select user_id, username from $config[tables_prefix]users where username in ($excluded_users_str)"));
		}
		$_POST['last_comment'] = $last_payout['comment'];
	} else
	{
		$_POST['award_types'] = array();
		foreach ($list_all_award_types as $award_type_id => $award_type)
		{
			$_POST['award_types'][] = $award_type_id;
		}
	}
}

if ($_GET['action'] == 'instructions')
{
	$payout = mr2array_single(sql_pr("select $table_selector from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($payout) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$account_field_name = '';
	switch ($payout['gateway'])
	{
		case 'manual':
			$account_field_name = '\'\'';
			break;
		case 'paypal':
			$account_field_name = 'u.account_paypal';
			break;
	}

	$user_payments = mr2array(sql_pr("
			select upu.*, up.comment, u.username, $account_field_name as account
			from $config[tables_prefix]users_payouts_users upu inner join $config[tables_prefix]users_payouts up on upu.$table_key_name=up.$table_key_name left join $config[tables_prefix]users u on upu.user_id=u.user_id
			where upu.$table_key_name=?
	", intval($_GET['item_id'])));

	if ($payout['gateway'] == 'paypal')
	{
		header("Content-Type: text/plain; charset=utf8");
		header("Content-Disposition: attachment; filename=\"$payout[gateway]_instructions.txt\"");
		foreach ($user_payments as $payment_info)
		{
			echo "$payment_info[account]\t$payment_info[amount]\t$payment_info[amount_currency]\t\t$payment_info[comment]\n";
		}
	} else
	{
		header("Content-Type: text/plain; charset=utf8");
		header("Content-Disposition: attachment; filename=\"$payout[gateway]_instructions.txt\"");
		echo $lang['users']['payout_field_gateway_download_empty'];
	}
	die;
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

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_users.tpl');

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_all_award_types', $list_all_award_types);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['payout_id'], $lang['users']['payout_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['users']['payout_add']);
} else
{
	$smarty->assign('page_title', $lang['users']['submenu_option_payouts_list']);
}

$smarty->display("layout.tpl");
