<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/setup_smarty_site.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

$langs_dir = "$config[project_path]/langs";

$texts = array();
if (is_file("$langs_dir/default.lang"))
{
	$file = fopen("$langs_dir/default.lang", 'r');
	while (($row = fgets($file)) !== false)
	{
		$row = trim($row);
		if ($row == '' || strpos($row, '#') === 0)
		{
			continue;
		}

		$pair = explode('=', $row, 2);
		if (count($pair) == 2)
		{
			$texts[trim($pair[0])] = trim($pair[1]);
		}
	}
	fclose($file);
}

$htaccess_contents = file_get_contents("$config[project_path]/.htaccess");

$formats_videos = mr2array(sql("select postfix as id, title from $config[tables_prefix]formats_videos where status_id in (1,2)"));
$formats_screenshots_overview = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1 and image_type=0"));
$formats_screenshots_overview_webp = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1 and image_type=1"));
$formats_screenshots_timeline = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2 and image_type=0"));
$formats_screenshots_timeline_webp = mr2array(sql("select size as id, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2 and image_type=1"));
$formats_albums_main = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=1"));
$formats_albums_preview = mr2array(sql("select size as id, title from $config[tables_prefix]formats_albums where status_id=1 and group_id=2"));

$flags_videos = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=1"));
$flags_albums = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=2"));
$flags_dvds = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=3"));
$flags_posts = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=4"));
$flags_playlists = mr2array(sql("select external_id as id, title from $config[tables_prefix]flags where group_id=5"));

$errors = null;

function project_theme_parse_field(SimpleXMLElement $xml_section_field)
{
	global $config, $formats_videos, $formats_screenshots_overview, $formats_screenshots_overview_webp, $formats_screenshots_timeline, $formats_screenshots_timeline_webp, $formats_albums_main, $formats_albums_preview, $flags_videos, $flags_albums, $flags_dvds, $flags_posts, $flags_playlists, $texts, $htaccess_contents;

	$field = array();

	$json_section_field = json_decode(json_encode($xml_section_field), true);
	$field['id'] = str_replace('.', '_dot_', $json_section_field['@attributes']['id']);
	$field['type'] = $json_section_field['@attributes']['type'];
	$field['package'] = intval($json_section_field['@attributes']['package']);
	$field['required'] = ($json_section_field['@attributes']['required'] == 'true' ? 1 : 0);
	$field['title'] = $json_section_field['title'][$_SESSION['userdata']['lang']];
	$field['hint'] = $json_section_field['hint'][$_SESSION['userdata']['lang']];

	switch ($field['type'])
	{
		case 'checkbox':
			$field['label'] = $json_section_field['label'][$_SESSION['userdata']['lang']];
			break;
		case 'select':
		case 'multiselect':
			$field['options'] = array();

			$xml_section_field_options_source = $xml_section_field->xpath('options/@source');
			if (is_array($xml_section_field_options_source) && count($xml_section_field_options_source) > 0)
			{
				$json_section_field_options_source = json_decode(json_encode($xml_section_field_options_source[0]), true);
				switch ($json_section_field_options_source['@attributes']['source'])
				{
					case 'formats_videos':
						$field['options'] = $formats_videos;
						break;
					case 'formats_screenshots_overview':
						$field['options'] = $formats_screenshots_overview;
						break;
					case 'formats_screenshots_overview_webp':
						$field['options'] = $formats_screenshots_overview_webp;
						break;
					case 'formats_screenshots_timeline':
						$field['options'] = $formats_screenshots_timeline;
						break;
					case 'formats_screenshots_timeline_webp':
						$field['options'] = $formats_screenshots_timeline_webp;
						break;
					case 'formats_albums_main':
						$field['options'] = $formats_albums_main;
						break;
					case 'formats_albums_preview':
						$field['options'] = $formats_albums_preview;
						break;
					case 'flags_videos':
						$field['options'] = $flags_videos;
						break;
					case 'flags_albums':
						$field['options'] = $flags_albums;
						break;
					case 'flags_dvds':
						$field['options'] = $flags_dvds;
						break;
					case 'flags_posts':
						$field['options'] = $flags_posts;
						break;
					case 'flags_playlists':
						$field['options'] = $flags_playlists;
						break;
				}
			}

			$xml_section_field_options = $xml_section_field->xpath('options/option');
			foreach ($xml_section_field_options as $xml_section_field_option)
			{
				$option = array();

				$json_section_field_option = json_decode(json_encode($xml_section_field_option), true);
				$option['id'] = $json_section_field_option['@attributes']['id'];
				$option['title'] = $json_section_field_option['title'][$_SESSION['userdata']['lang']];

				$field['options'][] = $option;
			}
			break;
		case 'group':
			$xml_section_field_fields = $xml_section_field->xpath('fields/field');

			$field['group'] = array();
			foreach ($xml_section_field_fields as $xml_section_field_field)
			{
				$field['group'][] = project_theme_parse_field($xml_section_field_field);
			}
			break;
		case 'block':
			$xml_section_field_blocks = $xml_section_field->xpath('blocks/block');
			foreach ($xml_section_field_blocks as $xml_section_field_block)
			{
				$block = array();

				$json_section_field_block = json_decode(json_encode($xml_section_field_block), true);
				$block['id'] = $json_section_field_block['@attributes']['id'];
				$block['package'] = intval($json_section_field_block['@attributes']['package']);

				$block_uid = explode('||', $block['id']);
				$block_name = ucwords(str_replace('_', ' ', $block_uid[2]));

				$block['title'] = $block_name;
				$block['link'] = "project_pages.php?action=change_block&amp;item_id=$block[id]&amp;item_name=$block_name";

				if ($block['package'] > 0 && $block['package'] > $config['installation_type'])
				{
					$block['unsupported'] = 1;
				}

				$field['blocks'][] = $block;
			}
			break;
	}

	$xml_section_field_value = $xml_section_field->xpath('value');
	if ($xml_section_field_value[0])
	{
		$json_section_field_value = json_decode(json_encode($xml_section_field_value[0]), true);

		$field['value_type'] = $json_section_field_value['@attributes']['type'];
		switch ($field['value_type'])
		{
			case 'langfile':
				if (!isset($texts[str_replace('_dot_', '.', $field['id'])]))
				{
					$field['hidden'] = 1;
				} else
				{
					$field['value'] = $texts[str_replace('_dot_', '.', $field['id'])];
					if (strpos($field['value'], 'array(') === 0)
					{
						if (trim(substr($field['value'], 6, -1)) == '')
						{
							$field['value'] = array();
						} else
						{
							$field['value'] = array_map('trim', explode(',', substr($field['value'], 6, -1)));
						}
					}
				}
				break;
			case 'htaccess':
				foreach ($field['blocks'] as $key => $block)
				{
					$block_uid = explode('||', $block['id']);
					if (strpos($htaccess_contents, "block_id=$block_uid[1]_$block_uid[2]") === false)
					{
						$field['blocks'][$key]['unused'] = 1;
					}
				}
				break;
		}
	}

	if ($field['package'] > 0 && $field['package'] > $config['installation_type'])
	{
		$field['unsupported'] = 1;
	}

	return $field;
}

$theme = [];
if (!function_exists('simplexml_load_string'))
{
	$_POST['errors'][] = get_aa_error('website_ui_project_theme_simplexml', "$config[project_path]/admin/data/config/theme.xml");
} elseif (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	/* @var $xml SimpleXMLElement */
	$xml = simplexml_load_string(file_get_contents("$config[project_path]/admin/data/config/theme.xml"));

	if ($xml)
	{
		$json = json_decode(json_encode($xml), true);
		$theme['name'] = $json['name'];
		$theme['version'] = $json['version'];
		$theme['developer'] = $json['developer'];
		if (is_url($theme['developer']))
		{
			$theme['developer_url'] = $theme['developer'];
			$theme['developer'] = parse_url($theme['developer'], PHP_URL_HOST);
		}
		$theme['forum'] = $json['forum'];
		$theme['header'] = $json['header'];
		$theme['footer'] = $json['footer'];

		$xml_sections = $xml->xpath('/theme/sections/section');

		$theme['sections'] = array();
		foreach ($xml_sections as $xml_section)
		{
			$section = array();

			$json_section = json_decode(json_encode($xml_section), true);
			$section['title'] = $json_section['title'][$_SESSION['userdata']['lang']];

			$xml_section_fields = $xml_section->xpath('fields/field');

			$has_supported_fields = false;

			$section['fields'] = array();
			foreach ($xml_section_fields as $xml_section_field)
			{
				$field = project_theme_parse_field($xml_section_field);
				$section['fields'][] = $field;

				if ($field['unsupported'] != 1)
				{
					$has_supported_fields = true;
				}
			}

			if ($has_supported_fields)
			{
				$theme['sections'][] = $section;
			}
		}
	} else
	{
		$_POST['errors'][] = get_aa_error('website_ui_project_theme_xml_error', "$config[project_path]/admin/data/config/theme.xml");
	}
}

if ($_POST['action'] == 'change_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$lang_file_fields = array();
	foreach ($theme['sections'] as $section)
	{
		foreach ($section['fields'] as $field)
		{
			if ($field['hidden'] != 1 && $field['unsupported'] != 1)
			{
				if ($field['value_type'] == 'langfile')
				{
					$lang_file_fields[str_replace('_dot_', '.', $field['id'])] = $field;
				}
				if ($field['required'] == 1)
				{
					validate_field('empty', $_POST[$field['id']], $field['title']);
				}
				if ($field['type'] == 'select' && $_POST[$field['id']] == '__INVALID__')
				{
					$errors[] = get_aa_error('website_ui_theme_select_setting_invalid', $field['title']);
				}
			}

			if ($field['type'] == 'group')
			{
				foreach ($field['group'] as $field_inner)
				{
					if ($field_inner['hidden'] != 1 && $field_inner['unsupported'] != 1)
					{
						if ($field_inner['value_type'] == 'langfile')
						{
							$lang_file_fields[str_replace('_dot_', '.', $field_inner['id'])] = $field_inner;
						}
						if ($field_inner['required'] == 1)
						{
							validate_field('empty', $_POST[$field_inner['id']], $field_inner['title']);
						}
						if ($field_inner['type'] == 'select' && $_POST[$field_inner['id']] == '__INVALID__')
						{
							$errors[] = get_aa_error('website_ui_theme_select_setting_invalid', $field_inner['title']);
						}
					}
				}
			}
		}
	}

	if (!is_writable($langs_dir))
	{
		$errors[] = get_aa_error('filesystem_permission_write', $langs_dir);
	}

	if (!is_array($errors))
	{
		$temp_file = "$langs_dir/default-$rnd.lang";
		if (is_file("$langs_dir/default.lang"))
		{
			$file = fopen("$langs_dir/default.lang", 'r');
			while (($row = fgets($file)) !== false)
			{
				$row = trim($row);
				if ($row == '' || strpos($row, '#') === 0)
				{
					file_put_contents($temp_file, "$row\n", FILE_APPEND);
					continue;
				}

				$pair = explode('=', $row, 2);
				if (count($pair) == 2 && isset($lang_file_fields[trim($pair[0])]))
				{
					$field = $lang_file_fields[trim($pair[0])];
					$field_value = '';
					if ($field['type'] == 'multiselect')
					{
						$field_value = 'array(' . implode(', ', $_POST[$field['id']]) . ')';
					} elseif ($field['type'] == 'checkbox')
					{
						$field_value = ($_POST[$field['id']] == 'true' ? 'true' : 'false');
					} else
					{
						$field_value = $_POST[$field['id']];
					}
					file_put_contents($temp_file, trim($pair[0]) . " = $field_value\n", FILE_APPEND);
				} else
				{
					file_put_contents($temp_file, "$row\n", FILE_APPEND);
				}
			}
			fclose($file);
			rename($temp_file, "$langs_dir/default.lang");
		}

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_website_ui.tpl');
$smarty->assign('theme', $theme);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

if (is_dir("$config[project_path]/langs"))
{
	$smarty->assign('supports_langs', 1);
}
if (is_file("$config[project_path]/admin/data/config/theme.xml"))
{
	$smarty->assign('supports_theme', 1);
}

$smarty->assign('page_title', $lang['website_ui']['submenu_option_theme_settings']);

$smarty->display("layout.tpl");
