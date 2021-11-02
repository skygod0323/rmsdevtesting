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

$languages = mr2array(sql("select code, title from $config[tables_prefix]languages order by language_id asc"));

$table_fields = array();
$table_fields[] = array('id' => 'external_id',  'title' => $lang['website_ui']['text_item_field_id'],           'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'text_default', 'title' => $lang['website_ui']['text_item_field_text_default'], 'is_default' => 1, 'type' => 'longtext');
foreach ($languages as $language)
{
	$table_fields[] = array('id' => "text_$language[code]", 'title' => str_replace("%1%", $language['title'] ,$lang['website_ui']['text_item_field_text_lang']), 'is_default' => 0, 'type' => 'longtext');
}

foreach ($table_fields as $k => $field)
{
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
$search_fields[] = array('id' => 'external_id',  'title' => $lang['website_ui']['text_item_field_id']);
$search_fields[] = array('id' => 'text_default', 'title' => $lang['website_ui']['text_item_field_text_default']);
foreach ($languages as $language)
{
	$search_fields[] = array('id' => "text_$language[code]", 'title' => str_replace("%1%", $language['title'] ,$lang['website_ui']['text_item_field_text_lang']));
}

$langs_dir = "$config[project_path]/langs";

$texts = array();
if (is_file("$langs_dir/default.lang"))
{
	$file = fopen("$langs_dir/default.lang", 'r');
	while (($row = fgets($file)) !== false)
	{
		$row = trim($row);
		if ($row == '' || substr($row, 0, 1) == '#')
		{
			continue;
		}

		$pair = explode('=', $row, 2);
		if (count($pair) == 2)
		{
			$texts[trim($pair[0])] = array('external_id' => trim($pair[0]), 'text_default' => trim($pair[1]));
		}
	}
	fclose($file);
}
ksort($texts);

foreach ($languages as $language)
{
	if (is_file("$langs_dir/$language[code].lang"))
	{
		$file = fopen("$langs_dir/$language[code].lang", 'r');
		while (($row = fgets($file)) !== false)
		{
			$row = trim($row);
			if ($row == '' || substr($row, 0, 1) == '#')
			{
				continue;
			}

			$pair = explode('=', $row, 2);
			if (count($pair) == 2)
			{
				if (isset($texts[trim($pair[0])]))
				{
					$texts[trim($pair[0])]["text_$language[code]"] = trim($pair[1]);
				}
			}
		}
		fclose($file);
	}
}

$errors = null;

// =====================================================================================================================
// filtering and sorting
// =====================================================================================================================

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
	$_SESSION['save'][$page_name]['se_missing_translation'] = '';
	$_SESSION['save'][$page_name]['se_prefix'] = '';
	$_SESSION['save'][$page_name]['se_page'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_missing_translation']))
	{
		$_SESSION['save'][$page_name]['se_missing_translation'] = trim($_GET['se_missing_translation']);
	}
	if (isset($_GET['se_prefix']))
	{
		$_SESSION['save'][$page_name]['se_prefix'] = trim($_GET['se_prefix']);
	}
	if (isset($_GET['se_page']))
	{
		$_SESSION['save'][$page_name]['se_page'] = trim($_GET['se_page']);
	}
}

if ($_SESSION['save'][$page_name]['se_text'] != '')
{
	foreach ($search_fields as $search_field)
	{
		if (isset($_GET["se_text_$search_field[id]"]))
		{
			$_SESSION['save'][$page_name]["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
		}
	}
}

$table_filtered = 0;
if ($_SESSION['save'][$page_name]['se_missing_translation'] != '')
{
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_prefix'] != '')
{
	$table_filtered = 1;
}
if ($_SESSION['save'][$page_name]['se_page'] != '')
{
	$table_filtered = 1;
}

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

	if ($_POST['action'] == 'add_new_complete')
	{
		if (validate_field('empty', $_POST['external_id'], $lang['website_ui']['text_item_field_id']))
		{
			if (!preg_match($regexp_valid_text_id, $_POST['external_id']))
			{
				$errors[] = get_aa_error('website_ui_invalid_text_id', $lang['website_ui']['text_item_field_id']);
			} elseif (isset($texts[$_POST['external_id']]))
			{
				$errors[] = get_aa_error('website_ui_duplicate_text_id', $lang['website_ui']['text_item_field_id']);
			}
		}
	}
	validate_field('empty', $_POST['text_default'], $lang['website_ui']['text_item_field_text_default']);

	if (!is_writable($langs_dir))
	{
		$errors[] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (!is_array($errors))
	{
		$rnd = mt_rand(10000000,99999999);

		if ($_POST['action'] == 'add_new_complete')
		{
			$temp_file = "$langs_dir/default-$rnd.lang";

			copy("$langs_dir/default.lang", $temp_file);
			file_put_contents($temp_file, "\n$_POST[external_id] = {$_POST["text_default"]}", FILE_APPEND);
			rename($temp_file, "$langs_dir/default.lang");

			foreach ($languages as $language)
			{
				if ($_POST["text_$language[code]"] != '')
				{
					$temp_file = "$langs_dir/$language[code]-$rnd.lang";

					copy("$langs_dir/$language[code].lang", $temp_file);
					file_put_contents($temp_file, "\n$_POST[external_id] = {$_POST["text_$language[code]"]}", FILE_APPEND);
					rename($temp_file, "$langs_dir/$language[code].lang");
				}
			}
			$_SESSION['messages'][] = $lang['common']['success_message_added'];
		} else
		{
			$temp_file = "$langs_dir/default-$rnd.lang";
			if (is_file("$langs_dir/default.lang"))
			{
				$file = fopen("$langs_dir/default.lang", 'r');
				while (($row = fgets($file)) !== false)
				{
					$row = trim($row);
					if ($row == '' || substr($row, 0, 1) == '#')
					{
						file_put_contents($temp_file, "$row\n", FILE_APPEND);
						continue;
					}

					$pair = explode('=', $row, 2);
					if (count($pair) == 2 && trim($pair[0]) == $_POST['item_id'])
					{
						file_put_contents($temp_file, "$_POST[item_id] = $_POST[text_default]\n", FILE_APPEND);
					} else
					{
						file_put_contents($temp_file, "$row\n", FILE_APPEND);
					}
				}
				fclose($file);
				rename($temp_file, "$langs_dir/default.lang");
			}

			foreach ($languages as $language)
			{
				$temp_file = "$langs_dir/$language[code]-$rnd.lang";
				if (isset($_POST["text_$language[code]"]))
				{
					if (is_file("$langs_dir/$language[code].lang"))
					{
						$has_key = false;

						$file = fopen("$langs_dir/$language[code].lang", 'r');
						while (($row = fgets($file)) !== false)
						{
							$row = trim($row);
							if ($row == '' || substr($row, 0, 1) == '#')
							{
								file_put_contents($temp_file, "$row\n", FILE_APPEND);
								continue;
							}

							$pair = explode('=', $row, 2);
							if (count($pair) == 2 && trim($pair[0]) == $_POST['item_id'])
							{
								if ($_POST["text_$language[code]"] != '')
								{
									file_put_contents($temp_file, "$_POST[item_id] = {$_POST["text_$language[code]"]}\n", FILE_APPEND);
								}
								$has_key = true;
							} else
							{
								file_put_contents($temp_file, "$row\n", FILE_APPEND);
							}
						}
						fclose($file);

						if (!$has_key && $_POST["text_$language[code]"] != '')
						{
							file_put_contents($temp_file, "$_POST[item_id] = {$_POST["text_$language[code]"]}\n", FILE_APPEND);
						}
						rename($temp_file, "$langs_dir/$language[code].lang");
					} elseif ($_POST["text_$language[code]"] != '')
					{
						file_put_contents("$langs_dir/$language[code].lang", "$_POST[item_id] = {$_POST["text_$language[code]"]}", FILE_APPEND);
					}
				}
			}

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
	$rnd = mt_rand(10000000,99999999);

	$temp_file = "$langs_dir/default-$rnd.lang";
	if (is_file("$langs_dir/default.lang"))
	{
		$file = fopen("$langs_dir/default.lang", 'r');
		while (($row = fgets($file)) !== false)
		{
			$row = trim($row);
			if ($row == '' || substr($row, 0, 1) == '#')
			{
				file_put_contents($temp_file, "$row\n", FILE_APPEND);
				continue;
			}

			$pair = explode('=', $row, 2);
			if (count($pair) == 2 && in_array(trim($pair[0]), $_REQUEST['row_select']))
			{
				continue;
			} else
			{
				file_put_contents($temp_file, "$row\n", FILE_APPEND);
			}
		}
		fclose($file);
		rename($temp_file, "$langs_dir/default.lang");
	}

	foreach ($languages as $language)
	{
		$temp_file = "$langs_dir/$language[code]-$rnd.lang";
		if (is_file("$langs_dir/$language[code].lang"))
		{
			$file = fopen("$langs_dir/$language[code].lang", 'r');
			while (($row = fgets($file)) !== false)
			{
				$row = trim($row);
				if ($row == '' || substr($row, 0, 1) == '#')
				{
					file_put_contents($temp_file, "$row\n", FILE_APPEND);
					continue;
				}

				$pair = explode('=', $row, 2);
				if (count($pair) == 2 && in_array(trim($pair[0]), $_REQUEST['row_select']))
				{
					continue;
				} else
				{
					file_put_contents($temp_file, "$row\n", FILE_APPEND);
				}
			}
			fclose($file);

			rename($temp_file, "$langs_dir/$language[code].lang");
		}
	}

	$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && $_GET['item_id'] != '')
{
	$_POST = $texts[$_GET['item_id']];
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
	}

	if (!is_writable($langs_dir))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (strpos($_POST['external_id'], 'urls.') === 0)
	{
		$_POST['is_url'] = 1;
	}

	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}
}

if ($_GET['action'] == 'add_new')
{
	if (!is_writable($langs_dir))
	{
		$_POST['errors'][] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (is_array($_POST['errors']))
	{
		$_POST['errors'] = array_unique($_POST['errors']);
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$site_pages = get_site_pages();
$only_page_texts_strict = array();
$only_page_texts_mask = array();
if ($_SESSION['save'][$page_name]['se_page'] != '')
{
	foreach ($site_pages as $page)
	{
		if ($page['external_id'] == $_SESSION['save'][$page_name]['se_page'])
		{
			$smarty_site = new mysmarty_site();
			$site_templates_path = rtrim($smarty_site->template_dir, '/');
			$page_template = @file_get_contents("$site_templates_path/$page[external_id].tpl");

			unset($temp);
			preg_match_all("|[\$]lang\.([^\}\|=<>!]+)|is", $page_template, $temp);
			settype($temp[1], 'array');
			foreach($temp[1] as $match)
			{
				if (strpos($match, '[') === false)
				{
					$only_page_texts_strict[] = $match;
				} else
				{
					$only_page_texts_mask[] = substr($match, 0, strpos($match, '['));
				}
			}

			break;
		}
	}
}

$total_num = 0;
foreach ($texts as $text)
{
	$is_skip = false;
	if (!$is_skip && $_SESSION['save'][$page_name]['se_text'] != '')
	{
		$is_skip = true;
		foreach ($search_fields as $search_field)
		{
			if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
			{
				if (mb_contains($text[$search_field['id']], $_SESSION['save'][$page_name]['se_text']))
				{
					$is_skip = false;
					break;
				}
			}
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_prefix'] != '')
	{
		$is_skip = true;
		if (strpos($text['external_id'], $_SESSION['save'][$page_name]['se_prefix'] . '.') === 0)
		{
			$is_skip = false;
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_page'] != '')
	{
		$is_skip = true;
		if (in_array($text['external_id'], $only_page_texts_strict))
		{
			$is_skip = false;
		}
		foreach ($only_page_texts_mask as $mask)
		{
			if (strpos($text['external_id'], $mask) === 0)
			{
				$is_skip = false;
				break;
			}
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_missing_translation'] != '')
	{
		$is_skip = true;
		if ($text['text_default'] != '' && $text["text_" . $_SESSION['save'][$page_name]['se_missing_translation']] == '')
		{
			$is_skip = false;
		}
	}
	if (!$is_skip)
	{
		$total_num++;
	}
}
if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
{
	$_SESSION['save'][$page_name]['from'] = 0;
}

$data = array();
$i = 0;
foreach ($texts as $text)
{
	$is_skip = false;
	if (!$is_skip && $_SESSION['save'][$page_name]['se_text'] != '')
	{
		$is_skip = true;
		foreach ($search_fields as $search_field)
		{
			if (intval($_SESSION['save'][$page_name]["se_text_$search_field[id]"]) == 1)
			{
				if (mb_contains($text[$search_field['id']], $_SESSION['save'][$page_name]['se_text']))
				{
					$is_skip = false;
					break;
				}
			}
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_prefix'] != '')
	{
		$is_skip = true;
		if (strpos($text['external_id'], $_SESSION['save'][$page_name]['se_prefix'] . '.') === 0)
		{
			$is_skip = false;
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_page'] != '')
	{
		$is_skip = true;
		if (in_array($text['external_id'], $only_page_texts_strict))
		{
			$is_skip = false;
		}
		foreach ($only_page_texts_mask as $mask)
		{
			if (strpos($text['external_id'], $mask) === 0)
			{
				$is_skip = false;
				break;
			}
		}
	}
	if (!$is_skip && $_SESSION['save'][$page_name]['se_missing_translation'] != '')
	{
		$is_skip = true;
		if ($text['text_default'] != '' && $text["text_" . $_SESSION['save'][$page_name]['se_missing_translation']] == '')
		{
			$is_skip = false;
		}
	}
	if ($is_skip)
	{
		continue;
	}
	if ($i >= $_SESSION['save'][$page_name]['from'] && $i < $_SESSION['save'][$page_name]['from'] + $_SESSION['save'][$page_name]['num_on_page'])
	{
		$data[] = $text;
	}
	$i++;
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');

if (in_array($_REQUEST['action'], array('change')))
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('languages', $languages);
$smarty->assign('pages', $site_pages);
$smarty->assign('table_key_name', 'external_id');
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs',1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['external_id'], $lang['website_ui']['text_item_edit']));
} elseif ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['website_ui']['text_item_add']);
} else
{
	$smarty->assign('page_title', $lang['website_ui']['submenu_option_text_items']);
}

$smarty->display("layout.tpl");
