<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function users_generatorInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins", 0777);
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/users_generator";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path, 0777);
		chmod($plugin_path, 0777);
	}
}

function users_generatorShow()
{
	global $config, $lang, $errors, $page_name;

	users_generatorInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/users_generator";

	$errors = null;

	if ($_POST['action'] == 'generate')
	{
		validate_field('empty_int', $_POST['amount'], $lang['plugins']['users_generator']['field_amount']);
		validate_field('empty_int', $_POST['access_type'], $lang['plugins']['users_generator']['field_access_type']);
		if (intval($_POST['access_type']) == 2)
		{
			validate_field('empty_int', $_POST['duration'], $lang['plugins']['users_generator']['field_access_type']);
		} elseif (intval($_POST['access_type']) == 3)
		{
			validate_field('empty_int', $_POST['tokens'], $lang['plugins']['users_generator']['field_access_type']);
		}

		if ($_POST['generate'] == 'access_codes')
		{
			if (validate_field('empty_int', $_POST['access_code_length'], $lang['plugins']['users_generator']['field_access_code_length']))
			{
				if (intval($_POST['access_code_length']) < 6)
				{
					$errors[] = get_aa_error('integer_min', $lang['plugins']['users_generator']['field_access_code_length'], '6');
				} elseif (intval($_POST['access_code_length']) > 50)
				{
					$errors[] = get_aa_error('integer_max', $lang['plugins']['users_generator']['field_access_code_length'], '50');
				}
			}
			if ($_POST['access_code_referral_award'])
			{
				validate_field('empty_int', $_POST['access_code_referral_award'], $lang['plugins']['users_generator']['field_access_code_referral_award']);
			}
		} else
		{
			if (validate_field('empty_int', $_POST['username_length'], $lang['plugins']['users_generator']['field_username_length']))
			{
				if (intval($_POST['username_length']) < 6)
				{
					$errors[] = get_aa_error('integer_min', $lang['plugins']['users_generator']['field_username_length'], '6');
				} elseif (intval($_POST['username_length']) > 50)
				{
					$errors[] = get_aa_error('integer_max', $lang['plugins']['users_generator']['field_username_length'], '50');
				}
			}
			if (validate_field('empty_int', $_POST['password_length'], $lang['plugins']['users_generator']['field_password_length']))
			{
				if (intval($_POST['password_length']) < 6)
				{
					$errors[] = get_aa_error('integer_min', $lang['plugins']['users_generator']['field_password_length'], '6');
				} elseif (intval($_POST['password_length']) > 50)
				{
					$errors[] = get_aa_error('integer_max', $lang['plugins']['users_generator']['field_password_length'], '50');
				}
			}
		}

		if (!is_array($errors))
		{
			$data = array();
			$data['results'] = '';
			$data['generate'] = $_POST['generate'];
			$data['amount'] = $_POST['amount'];
			$data['access_type'] = $_POST['access_type'];
			$data['duration'] = $_POST['duration'];
			$data['tokens'] = $_POST['tokens'];
			$data['username_length'] = $_POST['username_length'];
			$data['password_length'] = $_POST['password_length'];
			$data['access_code_length'] = $_POST['access_code_length'];
			$data['access_code_referral_award'] = $_POST['access_code_referral_award'];

			$user_status_id = 3;
			$user_tokens_assigned = 0;
			if (intval($_POST['access_type']) == 3)
			{
				$user_status_id = 2;
				$user_tokens_assigned = intval($_POST['tokens']);
			}

			for ($i = 0; $i < intval($_POST['amount']); $i++)
			{
				if ($_POST['generate'] == 'access_codes')
				{
					$access_code = strtolower(generate_password(intval($_POST['access_code_length'])));
					$access_code_valid = false;
					for ($j = 0; $j < 100; $j++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]bill_transactions where access_code=?", $access_code)) == 0)
						{
							$access_code_valid = true;
							break;
						}
						$access_code = strtolower(generate_password(intval($_POST['access_code_length'])));
					}
					if (!$access_code_valid)
					{
						continue;
					}

					$transaction_type_id = 1;
					$transaction_duration = 0;
					$transaction_tokens = 0;

					$transaction_start_date = "0000-00-00 00:00:00";
					$transaction_end_date = "2070-01-01 00:00:00";
					$transaction_is_unlimited = 1;

					if (intval($_POST['access_type']) == 2)
					{
						$transaction_duration = intval($_POST['duration']);
						$transaction_end_date = "0000-00-00 00:00:00";
						$transaction_is_unlimited = 0;
					} elseif (intval($_POST['access_type']) == 3)
					{
						$transaction_type_id = 10;
						$transaction_tokens = intval($_POST['tokens']);
						$transaction_end_date = "0000-00-00 00:00:00";
						$transaction_is_unlimited = 0;
					}

					sql_pr("insert into $config[tables_prefix]bill_transactions set bill_type_id=1, status_id=4, type_id=?, duration_rebill=?, access_code=?, access_code_referral_award=?, transaction_log=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, tokens_granted=?",
						$transaction_type_id, $transaction_duration, $access_code, intval($_POST['access_code_referral_award']), "$access_code", $transaction_start_date, $transaction_end_date, $transaction_is_unlimited, $transaction_tokens
					);

					$data['results'] .= "$access_code\n";
				} else
				{
					$user_name = strtolower(generate_password(intval($_POST['username_length'])));
					$user_name_valid = false;
					for ($j = 0; $j < 100; $j++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?", $user_name)) == 0)
						{
							$user_name_valid = true;
							break;
						}
						$user_name = strtolower(generate_password(intval($_POST['username_length'])));
					}
					if (!$user_name_valid)
					{
						continue;
					}
					$user_password = strtolower(generate_password(intval($_POST['password_length'])));

					$user_id = sql_insert("insert into $config[tables_prefix]users set username=?, pass=?, display_name=?, status_id=?, tokens_available=?, added_date=?",
						$user_name, generate_password_hash($user_password), $user_name, $user_status_id, $user_tokens_assigned, date("Y-m-d H:i:s")
					);
					if ($user_id > 0)
					{
						$transaction_status_id = 4;
						$transaction_type_id = 1;
						$transaction_duration = 0;

						$transaction_start_date = "0000-00-00 00:00:00";
						$transaction_end_date = "2070-01-01 00:00:00";
						$transaction_is_unlimited = 1;

						if (intval($_POST['access_type']) == 2)
						{
							$transaction_duration = intval($_POST['duration']);
							$transaction_end_date = "0000-00-00 00:00:00";
							$transaction_is_unlimited = 0;
						} elseif (intval($_POST['access_type']) == 3)
						{
							$transaction_status_id = 2;
							$transaction_type_id = 10;
							$transaction_start_date = date("Y-m-d H:i:s");
							$transaction_end_date = date("Y-m-d H:i:s");
							$transaction_is_unlimited = 0;
						}

						sql_pr("insert into $config[tables_prefix]bill_transactions set bill_type_id=1, status_id=?, type_id=?, user_id=?, duration_rebill=?, transaction_log=?, access_start_date=?, access_end_date=?, is_unlimited_access=?, tokens_granted=?",
							$transaction_status_id, $transaction_type_id, $user_id, $transaction_duration, "$user_name:$user_password", $transaction_start_date, $transaction_end_date, $transaction_is_unlimited, $user_tokens_assigned
						);

						$data['results'] .= "$user_name:$user_password\n";
					}
				}
			}

			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			return_ajax_success("$page_name?plugin_id=users_generator&amp;action=results");
		} else
		{
			return_ajax_errors($errors);
		}
	}

	if (is_file("$plugin_path/data.dat"))
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	} else
	{
		$_POST = array();
		$_POST['generate'] = 'access_codes';
		$_POST['access_type'] = '1';
		$_POST['username_length'] = '8';
		$_POST['password_length'] = '8';
		$_POST['access_code_length'] = '8';
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
