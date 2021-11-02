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

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_filename'] = '';
	$_SESSION['save'][$page_name]['se_contents'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_filename']))
	{
		$_SESSION['save'][$page_name]['se_filename'] = trim($_GET['se_filename']);
	}
	if (isset($_GET['se_contents']))
	{
		$_SESSION['save'][$page_name]['se_contents'] = trim($_GET['se_contents']);
	}
}

$data = array();
if (strlen($_SESSION['save'][$page_name]['se_filename']) > 0 || strlen($_SESSION['save'][$page_name]['se_contents']) > 0)
{
	$smarty_site = new mysmarty_site();
	$site_templates_path = $smarty_site->template_dir;
	$pages = get_site_pages();
	$page_ids = array();
	foreach ($pages as $page)
	{
		$page_ids[$page['external_id']] = $page;
	}

	$templates = get_contents_from_dir($site_templates_path, 1);
	foreach ($templates as $template)
	{
		if (strtolower(end(explode(".", $template))) !== 'tpl')
		{
			continue;
		}

		$found = 1;
		if (strlen($_SESSION['save'][$page_name]['se_filename']) > 0 && strpos($template, $_SESSION['save'][$page_name]['se_filename']) === false)
		{
			$found = 0;
		}

		$contents = @file_get_contents("$site_templates_path/$template");
		if (strlen($_SESSION['save'][$page_name]['se_contents']) > 0 && !mb_contains($contents, $_SESSION['save'][$page_name]['se_contents']))
		{
			$found = 0;
		}

		$item = array();
		$item['filename'] = $template;

		$temp = explode(".", $template);
		$page_external_id = $temp[0];
		if (isset($page_ids[$page_external_id]))
		{
			$item['type'] = 'page';
			$item['page_name'] = $page_ids[$page_external_id]['title'];
			$item['external_id'] = $page_ids[$page_external_id]['external_id'];
		} else
		{
			$item['type'] = 'component';
		}
		if ($found == 1)
		{
			$data[] = $item;
		}

		if ($item['type'] == 'page')
		{
			preg_match_all($regexp_insert_block, $contents, $temp);
			settype($temp[1], "array");
			if (count($temp[1]) > 0)
			{
				foreach ($temp[1] as $k1 => $v1)
				{
					$block_id = trim($temp[1][$k1]);
					$block_name = trim($temp[2][$k1]);
					if (!preg_match($regexp_valid_external_id, $block_id) || !preg_match($regexp_valid_block_name, $block_name))
					{
						continue;
					}
					$block_internal_name = strtolower(str_replace(" ", "_", $block_name));

					$block_template = "{$block_id}_$block_internal_name.tpl";
					if (strlen($_SESSION['save'][$page_name]['se_filename']) > 0 && strpos($block_template, $_SESSION['save'][$page_name]['se_filename']) === false)
					{
						continue;
					}

					if (strlen($_SESSION['save'][$page_name]['se_contents']) > 0)
					{
						$block_contents = @file_get_contents("$site_templates_path/blocks/$page_external_id/$block_template");
						if (mb_contains($block_contents, $_SESSION['save'][$page_name]['se_contents']))
						{
							$item_block = array();
							$item_block['filename'] = $block_template;
							$item_block['type'] = 'block_template';
							$item_block['page_name'] = $page_ids[$page_external_id]['title'];
							$item_block['page_external_id'] = $page_ids[$page_external_id]['external_id'];
							$item_block['block_name'] = $block_name;
							$item_block['block_id'] = $block_id;
							$item_block['block_internal_name'] = $block_internal_name;
							$data[] = $item_block;
						}

						$block_params = @file_get_contents("$config[project_path]/admin/data/config/$page_external_id/{$block_id}_$block_internal_name.dat");
						if (mb_contains($block_params, $_SESSION['save'][$page_name]['se_contents']))
						{
							$item_block = array();
							$item_block['filename'] = $block_template;
							$item_block['type'] = 'block_params';
							$item_block['page_name'] = $page_ids[$page_external_id]['title'];
							$item_block['page_external_id'] = $page_ids[$page_external_id]['external_id'];
							$item_block['block_name'] = $block_name;
							$item_block['block_id'] = $block_id;
							$item_block['block_internal_name'] = $block_internal_name;
							$data[] = $item_block;
						}
					}
				}
			}
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
			$block_name_mod = substr($global_block, strpos($global_block, "[SEP]") + 5);
			$block_name = ucwords(str_replace('_', ' ', $block_name_mod));

			$file_name = "$site_templates_path/blocks/\$global/{$block_id}_$block_name_mod.tpl";
			$template = "{$block_id}_$block_name_mod.tpl";
			if (is_file($file_name))
			{
				if (strlen($_SESSION['save'][$page_name]['se_filename']) > 0 && strpos($template, $_SESSION['save'][$page_name]['se_filename']) === false)
				{
					continue;
				}

				if (strlen($_SESSION['save'][$page_name]['se_contents']) > 0)
				{
					$contents = @file_get_contents($file_name);
					if (mb_contains($contents, $_SESSION['save'][$page_name]['se_contents']))
					{
						$item = array();
						$item['filename'] = $template;
						$item['type'] = 'global_block_template';
						$item['block_name'] = $block_name;
						$item['block_id'] = $block_id;
						$item['block_internal_name'] = $block_name_mod;
						$data[] = $item;
					}

					$params = @file_get_contents("$config[project_path]/admin/data/config/\$global/{$block_id}_$block_name_mod.dat");
					if (mb_contains($params, $_SESSION['save'][$page_name]['se_contents']))
					{
						$item = array();
						$item['filename'] = $template;
						$item['type'] = 'global_block_params';
						$item['block_name'] = $block_name;
						$item['block_id'] = $block_id;
						$item['block_internal_name'] = $block_name_mod;
						$data[] = $item;
					}
				}
			}
		}
	}

	if (strlen($_SESSION['save'][$page_name]['se_contents']) > 0)
	{
		$spots = get_site_spots();
		foreach ($spots as $external_id => $spot)
		{
			if (mb_contains($external_id, $_SESSION['save'][$page_name]['se_contents']))
			{
				$item = array();
				$item['type'] = 'ad_spot';
				$item['external_id'] = $external_id;
				$item['filename'] = "spot_{$external_id}.dat";
				$item['spot_name'] = $spot['title'];
				$data[] = $item;
			}
			foreach ($spot['ads'] as $advertisement_id => $ad)
			{
				if (mb_contains($ad['title'], $_SESSION['save'][$page_name]['se_contents']) || mb_contains($ad['code'], $_SESSION['save'][$page_name]['se_contents']))
				{
					$item = array();
					$item['type'] = 'ad';
					$item['advertisement_id'] = $advertisement_id;
					$item['filename'] = "spot_{$external_id}.dat";
					$item['advertisement_name'] = $ad['title'];
					$data[] = $item;
				}
			}
		}

		$langs_dir = "$config[project_path]/langs";
		if (is_dir($langs_dir))
		{
			$languages = mr2array(sql("select code, title from $config[tables_prefix]languages order by language_id asc"));
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

			foreach ($texts as $text)
			{
				if (mb_contains($text['external_id'], $_SESSION['save'][$page_name]['se_contents']))
				{
					$item = array();
					$item['type'] = 'lang_text';
					$item['external_id'] = $text['external_id'];
					$data[] = $item;
					continue;
				}
				if (mb_contains($text['text_default'], $_SESSION['save'][$page_name]['se_contents']))
				{
					$item = array();
					$item['type'] = 'lang_text';
					$item['filename'] = 'default.lang';
					$item['language_code'] = 'default';
					$item['external_id'] = $text['external_id'];
					$data[] = $item;
					continue;
				}
				foreach ($languages as $language)
				{
					if (mb_contains($text["text_$language[code]"], $_SESSION['save'][$page_name]['se_contents']))
					{
						$item = array();
						$item['type'] = 'lang_text';
						$item['filename'] = "$language[code].lang";
						$item['language_code'] = $language['code'];
						$item['language_title'] = $language['title'];
						$item['external_id'] = $text['external_id'];
						$data[] = $item;
						break;
					}
				}
			}
		}
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('total_num', count($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

$smarty->assign('page_title', $lang['website_ui']['submenu_option_template_search']);

$smarty->display("layout.tpl");
?>