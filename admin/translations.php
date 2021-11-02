<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';

// =====================================================================================================================
// initialization
// =====================================================================================================================

$options = get_options();

$languages = mr2array(sql("select * from $config[tables_prefix]languages"));

$item_type = intval($_REQUEST['item_type']);
if ($item_type == 0)
{
	$item_type = intval($_GET['se_object_type']);
}
if (!isset($_GET['se_object_type']) && $item_type == 0)
{
	$item_type = intval($_SESSION['save'][$page_name]['se_object_type']);
}
$list_grouping = $item_type;

$title_selector = "title";
$dir_selector = "dir";
$desc_selector = "description";
$page_title_key = "";
$tiny_mce_key = "is_wysiwyg_enabled_other";
$allow_title_duplicates = false;
if ($item_type == 1)
{
	$table_name = "$config[tables_prefix]videos";
	$table_key_name = "video_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_video'];
	$tiny_mce_key = "is_wysiwyg_enabled_videos";
	if (intval($options['VIDEO_CHECK_DUPLICATE_TITLES']) == 0)
	{
		$allow_title_duplicates = true;
	}
} elseif ($item_type == 2)
{
	$table_name = "$config[tables_prefix]albums";
	$table_key_name = "album_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_album'];
	$tiny_mce_key = "is_wysiwyg_enabled_albums";
	if (intval($options['ALBUM_CHECK_DUPLICATE_TITLES']) == 0)
	{
		$allow_title_duplicates = true;
	}
} elseif ($item_type == 3)
{
	$table_name = "$config[tables_prefix]content_sources";
	$table_key_name = "content_source_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_content_source'];
} elseif ($item_type == 4)
{
	$table_name = "$config[tables_prefix]models";
	$table_key_name = "model_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_model'];
} elseif ($item_type == 5)
{
	$table_name = "$config[tables_prefix]dvds";
	$table_key_name = "dvd_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_dvd'];
} elseif ($item_type == 6)
{
	$table_name = "$config[tables_prefix]categories";
	$table_key_name = "category_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_category'];
} elseif ($item_type == 7)
{
	$table_name = "$config[tables_prefix]categories_groups";
	$table_key_name = "category_group_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_category_group'];
} elseif ($item_type == 8)
{
	$table_name = "$config[tables_prefix]content_sources_groups";
	$table_key_name = "content_source_group_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_content_source_group'];
} elseif ($item_type == 9)
{
	$table_name = "$config[tables_prefix]tags";
	$table_key_name = "tag_id";
	$title_selector = "tag";
	$dir_selector = "tag_dir";
	$desc_selector = "";
	$page_title_key = $lang['settings']['translation_edit_object_type_tag'];
} elseif ($item_type == 10)
{
	$table_name = "$config[tables_prefix]dvds_groups";
	$table_key_name = "dvd_group_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_dvd_group'];
} elseif ($item_type == 14)
{
	$table_name = "$config[tables_prefix]models_groups";
	$table_key_name = "model_group_id";
	$page_title_key = $lang['settings']['translation_edit_object_type_model_group'];
}

$scopes = array(1 => 'videos', 2 => 'albums', 3 => 'content_sources', 4 => 'models', 5 => 'dvds', 6 => 'categories', 7 => 'categories_groups', 8 => 'content_sources_groups', 9 => 'tags', 10 => 'dvds_groups', 14 => 'models_groups');
if ($item_type > 0)
{
	$supports_desc_translation = false;
	foreach ($languages as $k => $language)
	{
		$languages[$k]['translation_scope'] = $language["translation_scope_$scopes[$item_type]"];
		if ($language["translation_scope_$scopes[$item_type]"] == 0)
		{
			$supports_desc_translation = true;
		}
	}
	if (!$supports_desc_translation)
	{
		$desc_selector = '';
	}
}

$table_fields = array();
$table_fields[] = array('id' => 'object_id', 'title' => $lang['settings']['translation_field_object_id'], 'is_default' => 1, 'type' => 'text', 'link' => 'translations.php?action=change&item_id=%id%&item_type=' . $item_type, 'link_id' => 'object_id');
$table_fields[] = array('id' => 'title',     'title' => $lang['settings']['translation_field_title'],     'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'dir',       'title' => $lang['settings']['translation_field_directory'], 'is_default' => 0, 'type' => 'text');
if ($desc_selector != '')
{
	$table_fields[] = array('id' => 'description', 'title' => $lang['settings']['translation_field_description'], 'is_default' => 0, 'type' => 'longtext');
}
foreach ($languages as $language)
{
	$table_fields[] = array('id' => "ok_{$language['code']}", 'title' => $language['title'], 'is_default' => 1, 'type' => 'text', 'is_nowrap' => 1);
	$table_fields[] = array('id' => "title_{$language['code']}", 'title' => "{$lang['settings']['translation_field_title']} ({$language['title']})", 'is_default' => 0, 'type' => 'text');
	if ($language['is_directories_localize'] == 1)
	{
		$table_fields[] = array('id' => "dir_{$language['code']}", 'title' => "{$lang['settings']['translation_field_directory']} ({$language['title']})", 'is_default' => 0, 'type' => 'text');
	}
	if ($desc_selector != '')
	{
		$table_fields[] = array('id' => "description_{$language['code']}", 'title' => "{$lang['settings']['translation_field_description']} ({$language['title']})", 'is_default' => 0, 'type' => 'longtext');
	}
}

$sort_def_field = "object_id";
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
$search_fields[] = array('id' => $table_key_name, 'title' => $lang['settings']['translation_field_object_id']);
$search_fields[] = array('id' => 'title',         'title' => $lang['settings']['translation_field_title']);
$search_fields[] = array('id' => 'dir',           'title' => $lang['settings']['translation_field_directory']);
if ($desc_selector != '')
{
	$search_fields[] = array('id' => 'description', 'title' => $lang['settings']['translation_field_description']);
}

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
	$_SESSION['save'][$page_name][$list_grouping]['from'] = intval($_GET['from']);
}
settype($_SESSION['save'][$page_name][$list_grouping]['from'], "integer");

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_translation_missing_for'] = '';
	$_SESSION['save'][$page_name]['se_translation_having_for'] = '';
	$_SESSION['save'][$page_name]['se_translated_date_from'] = '';
	$_SESSION['save'][$page_name]['se_translated_date_to'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = $_GET['se_text'];
	}
	if (isset($_GET['se_object_type']))
	{
		$_SESSION['save'][$page_name]['se_object_type'] = intval($_GET['se_object_type']);
	}
	if (isset($_GET['se_translation_missing_for']))
	{
		$_SESSION['save'][$page_name]['se_translation_missing_for'] = $_GET['se_translation_missing_for'];
	}
	if (isset($_GET['se_translation_having_for']))
	{
		$_SESSION['save'][$page_name]['se_translation_having_for'] = $_GET['se_translation_having_for'];
	}
	if (isset($_GET['se_translated_date_from_Day'], $_GET['se_translated_date_from_Month'], $_GET['se_translated_date_from_Year']))
	{
		if (intval($_GET['se_translated_date_from_Day']) > 0 && intval($_GET['se_translated_date_from_Month']) > 0 && intval($_GET['se_translated_date_from_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_translated_date_from'] = intval($_GET['se_translated_date_from_Year']) . "-" . intval($_GET['se_translated_date_from_Month']) . "-" . intval($_GET['se_translated_date_from_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_translated_date_from'] = "";
		}
	}
	if (isset($_GET['se_translated_date_to_Day'], $_GET['se_translated_date_to_Month'], $_GET['se_translated_date_to_Year']))
	{
		if (intval($_GET['se_translated_date_to_Day']) > 0 && intval($_GET['se_translated_date_to_Month']) > 0 && intval($_GET['se_translated_date_to_Year']) > 0)
		{
			$_SESSION['save'][$page_name]['se_translated_date_to'] = intval($_GET['se_translated_date_to_Year']) . "-" . intval($_GET['se_translated_date_to_Month']) . "-" . intval($_GET['se_translated_date_to_Day']);
		} else
		{
			$_SESSION['save'][$page_name]['se_translated_date_to'] = "";
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
			} elseif ($search_field['id'] == 'title')
			{
				$where_search .= " or $table_name.$title_selector like '%$q%'";
			} elseif ($search_field['id'] == 'dir')
			{
				$where_search .= " or $table_name.$dir_selector like '%$q%'";
			} elseif ($search_field['id'] == 'description')
			{
				if ($desc_selector)
				{
					$where_search .= " or $table_name.$desc_selector like '%$q%'";
				}
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

unset($missing_lang, $having_lang);
foreach ($languages as $language)
{
	if ($_SESSION['save'][$page_name]['se_translation_missing_for'] == $language['code'])
	{
		$missing_lang = $language;
	}
	if ($_SESSION['save'][$page_name]['se_translation_having_for'] == $language['code'])
	{
		$having_lang = $language;
	}
}
if (isset($missing_lang))
{
	if ($desc_selector <> '' && $missing_lang['translation_scope'] == 0)
	{
		if ($missing_lang['is_directories_localize'] == 1)
		{
			$where .= " and ({$title_selector}_$missing_lang[code]='' or {$dir_selector}_$missing_lang[code]='' or ($desc_selector<>'' and {$desc_selector}_$missing_lang[code]=''))";
		} else
		{
			$where .= " and ({$title_selector}_$missing_lang[code]='' or ($desc_selector<>'' and {$desc_selector}_$missing_lang[code]=''))";
		}
	} else
	{
		if ($missing_lang['is_directories_localize'] == 1)
		{
			$where .= " and ({$title_selector}_$missing_lang[code]='' or {$dir_selector}_$missing_lang[code]='')";
		} else
		{
			$where .= " and ({$title_selector}_$missing_lang[code]='')";
		}
	}
	$table_filtered = 1;
} else
{
	$_SESSION['save'][$page_name]['se_translation_missing_for'] = '';
}
if (isset($having_lang))
{
	if ($desc_selector <> '' && $having_lang['translation_scope'] == 0)
	{
		if ($having_lang['is_directories_localize'] == 1)
		{
			$where .= " and ({$title_selector}_$having_lang[code]!='' and {$dir_selector}_$having_lang[code]!='' and ($desc_selector='' or {$desc_selector}_$having_lang[code]!=''))";
		} else
		{
			$where .= " and ({$title_selector}_$having_lang[code]!='' and ($desc_selector='' or {$desc_selector}_$having_lang[code]!=''))";
		}
	} else
	{
		if ($having_lang['is_directories_localize'] == 1)
		{
			$where .= " and ({$title_selector}_$having_lang[code]!='' and {$dir_selector}_$having_lang[code]!='')";
		} else
		{
			$where .= " and ({$title_selector}_$having_lang[code]!='')";
		}
	}
	$table_filtered = 1;
} else
{
	$_SESSION['save'][$page_name]['se_translation_having_for'] = '';
}
if ($_SESSION['save'][$page_name]['se_translated_date_from'] <> "" || $_SESSION['save'][$page_name]['se_translated_date_to'] <> "")
{
	unset($where_temp);
	if ($_SESSION['save'][$page_name]['se_translated_date_from'] <> "")
	{
		$where_temp .= " and added_date>='" . $_SESSION['save'][$page_name]['se_translated_date_from'] . "'";
	}
	if ($_SESSION['save'][$page_name]['se_translated_date_to'] <> "")
	{
		$where_temp .= " and added_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save'][$page_name]['se_translated_date_to']) + 86399) . "'";
	}
	$where .= " and $table_key_name in (select distinct object_id from $config[tables_prefix]admin_audit_log where action_id=200 and object_type_id=$item_type $where_temp)";
	$table_filtered = 1;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'object_id')
{
	$sort_by = $table_key_name;
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

// =====================================================================================================================
// add new and edit
// =====================================================================================================================

if ($_POST['action'] == 'change_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if ($table_name == '')
	{
		die;
	}

	$item_id = intval($_POST['item_id']);

	foreach ($languages as $language)
	{
		if (in_array("localization|$language[code]", $_SESSION['permissions']))
		{
			if (!$allow_title_duplicates)
			{
				if ($_POST["{$title_selector}_$language[code]"] != '' && mr2number(sql_pr("select count(*) from $table_name where {$title_selector}_$language[code]=? and $table_key_name<>?", $_POST["{$title_selector}_$language[code]"], $item_id)) > 0)
				{
					$errors[] = get_aa_error('unique_field', str_replace("%1%", $language['title'], $lang['settings']['translation_field_language_title']));
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$old_object_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $item_id));

		$update_array = array();
		foreach ($languages as $language)
		{
			if (in_array("localization|$language[code]", $_SESSION['permissions']))
			{
				$update_array["{$title_selector}_$language[code]"] = $_POST["{$title_selector}_$language[code]"];

				if ($language['is_directories_localize'] == 1)
				{
					if ($_POST["{$dir_selector}_$language[code]"] == '')
					{
						$_POST["{$dir_selector}_$language[code]"] = get_correct_dir_name($_POST["{$title_selector}_$language[code]"], $language);
					}
					if ($_POST["{$dir_selector}_$language[code]"] != '')
					{
						$temp_dir = $_POST["{$dir_selector}_$language[code]"];
						for ($i = 2; $i < 999999; $i++)
						{
							if (mr2number(sql_pr("select count(*) from $table_name where {$dir_selector}_$language[code]=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
							{
								$_POST["{$dir_selector}_$language[code]"] = $temp_dir;
								break;
							}
							$temp_dir = $_POST["{$dir_selector}_$language[code]"] . $i;
						}
					}
					$update_array["{$dir_selector}_$language[code]"] = $_POST["{$dir_selector}_$language[code]"];
				}

				if ($desc_selector <> '' && isset($_POST["{$desc_selector}_$language[code]"]))
				{
					$update_array["{$desc_selector}_$language[code]"] = $_POST["{$desc_selector}_$language[code]"];
				}
			}
		}
		if (count($update_array) > 0)
		{
			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $item_id);

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($old_object_data[$k] <> $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, -2);
				if ($item_type > 0)
				{
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=200, object_id=?, object_type_id=$item_type, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, $update_details, date("Y-m-d H:i:s"));
				}
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_modified'];

		if (isset($_POST['save_and_edit']))
		{
			$data_temp = mr2array_list(sql("select $table_key_name from $table_name $where order by $sort_by"));

			$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
			if ($next_item_id == 0)
			{
				$next_item_id = mr2number(sql("select $table_key_name from $table_name $where order by $sort_by limit 1"));
			}
			if ($next_item_id == 0)
			{
				$_POST['save_and_close'] = $_POST['save_and_edit'];
				return_ajax_success($page_name, 1);
			} else
			{
				return_ajax_success($page_name . "?action=change&amp;item_id=$next_item_id", 1);
			}
		}
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
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

	if ($item_type == 1)
	{
		if (intval($_POST['content_source_id']) > 0)
		{
			$_POST['content_source'] = mr2string(sql_pr("select title from $config[tables_prefix]content_sources where content_source_id=? limit 1", $_POST['content_source_id']));
		}
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_videos.category_id) as title from $config[tables_prefix]categories_videos where $config[tables_prefix]categories_videos.video_id=? order by id asc", $_POST['video_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) as tag from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=? order by id asc", $_POST['video_id']));
		$_POST['models'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_videos.model_id) as title from $config[tables_prefix]models_videos where $config[tables_prefix]models_videos.video_id=? order by id asc", $_POST['video_id']));

		if (intval($_SESSION['save']['options']['screenshots_on_video_edit']) > 0)
		{
			$dir_path = get_dir_by_id($item_id);

			$screen_format = mr2array_single(sql_pr("select * from $config[tables_prefix]formats_screenshots where format_screenshot_id=? and group_id in (1,3)", intval($_SESSION['save']['options']['screenshots_on_video_edit'])));
			$screen_url = $config['content_url_videos_screenshots_admin_panel'] ?: $config['content_url_videos_screenshots'];

			if ($screen_format['group_id'] == 1)
			{
				$_POST['screen_url'] = "$screen_url/$dir_path/$item_id/$screen_format[size]";
			} elseif ($screen_format['group_id'] == 3)
			{
				$_POST['screen_url'] = "$screen_url/$dir_path/$item_id/posters/$screen_format[size]";
				$_POST['screen_amount'] = $_POST['poster_amount'];
			}
		}
	} elseif ($item_type == 2)
	{
		if (intval($_POST['content_source_id']) > 0)
		{
			$_POST['content_source'] = mr2string(sql_pr("select title from $config[tables_prefix]content_sources where content_source_id=? limit 1", $_POST['content_source_id']));
		}
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_albums.category_id) as title from $config[tables_prefix]categories_albums where $config[tables_prefix]categories_albums.album_id=? order by id asc", $_POST['album_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_albums.tag_id) as tag from $config[tables_prefix]tags_albums where $config[tables_prefix]tags_albums.album_id=? order by id asc", $_POST['album_id']));
		$_POST['models'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_albums.model_id) as title from $config[tables_prefix]models_albums where $config[tables_prefix]models_albums.album_id=? order by id asc", $_POST['album_id']));

		if (preg_match("|^[1-9]+\d*x[1-9]+\d*$|is", $_SESSION['save']['options']['images_on_album_edit']))
		{
			$dir_path = get_dir_by_id($item_id);
			$size = $_SESSION['save']['options']['images_on_album_edit'];
			$temp_images = mr2array(sql("select * from $config[tables_prefix]albums_images where album_id=$item_id order by image_id asc"));
			$required_count = intval($_SESSION['save']['options']['images_on_album_edit_count']);

			$_POST['list_images'][] = $temp_images[0];
			if ($required_count == 2)
			{
				if (count($temp_images) > 1)
				{
					$_POST['list_images'][] = $temp_images[count($temp_images) - 1];
				}
			} elseif ($required_count > 2)
			{
				if (count($temp_images) > $required_count)
				{
					$step = floatval(count($temp_images)) / floatval($required_count);
					$it = $step + 1;
					while ($it < count($temp_images) - 1)
					{
						$_POST['list_images'][] = $temp_images[intval(round($it))];
						if (count($_POST['list_images']) == $required_count - 1)
						{
							break;
						}
						$it += $step;
					}
					$_POST['list_images'][] = $temp_images[count($temp_images) - 1];
				} else
				{
					$_POST['list_images'] = $temp_images;
				}
			}
			foreach ($_POST['list_images'] as $k => $v)
			{
				$file_path = "main/$size/$dir_path/$item_id/$v[image_id].jpg";
				$hash = md5($config['cv'] . $file_path);
				$file_path = "$hash/$file_path";
				$_POST['list_images'][$k]['file_path'] = $file_path;
			}
		}
	} elseif ($item_type == 3)
	{
		if (intval($_POST['content_source_group_id']) > 0)
		{
			$_POST['group'] = mr2string(sql_pr("select title from $config[tables_prefix]content_sources_groups where content_source_group_id=? limit 1", $_POST['content_source_group_id']));
		}
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_content_sources.category_id) as title from $config[tables_prefix]categories_content_sources where $config[tables_prefix]categories_content_sources.content_source_id=? order by id asc", $_POST['content_source_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_content_sources.tag_id) as tag from $config[tables_prefix]tags_content_sources where $config[tables_prefix]tags_content_sources.content_source_id=? order by id asc", $_POST['content_source_id']));
	} elseif ($item_type == 4)
	{
		if (intval($_POST['model_group_id']) > 0)
		{
			$_POST['group'] = mr2string(sql_pr("select title from $config[tables_prefix]models_groups where model_group_id=? limit 1", $_POST['model_group_id']));
		}
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_models.category_id) as title from $config[tables_prefix]categories_models where $config[tables_prefix]categories_models.model_id=? order by id asc", $_POST['model_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_models.tag_id) as tag from $config[tables_prefix]tags_models where $config[tables_prefix]tags_models.model_id=? order by id asc", $_POST['model_id']));
	} elseif ($item_type == 5)
	{
		if (intval($_POST['dvd_group_id']) > 0)
		{
			$_POST['group'] = mr2string(sql_pr("select title from $config[tables_prefix]dvds_groups where dvd_group_id=? limit 1", $_POST['dvd_group_id']));
		}
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_dvds.category_id) as title from $config[tables_prefix]categories_dvds where $config[tables_prefix]categories_dvds.dvd_id=? order by id asc", $_POST['dvd_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_dvds.tag_id) as tag from $config[tables_prefix]tags_dvds where $config[tables_prefix]tags_dvds.dvd_id=? order by id asc", $_POST['dvd_id']));
		$_POST['models'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_dvds.model_id) as title from $config[tables_prefix]models_dvds where $config[tables_prefix]models_dvds.dvd_id=? order by id asc", $_POST['dvd_id']));
	} elseif ($item_type == 6)
	{
		if (intval($_POST['category_group_id']) > 0)
		{
			$_POST['group'] = mr2string(sql_pr("select title from $config[tables_prefix]categories_groups where category_group_id=? limit 1", $_POST['category_group_id']));
		}
	} elseif ($item_type == 10)
	{
		$_POST['categories'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]categories where category_id=$config[tables_prefix]categories_dvds_groups.category_id) as title from $config[tables_prefix]categories_dvds_groups where $config[tables_prefix]categories_dvds_groups.dvd_group_id=? order by id asc", $_POST['dvd_group_id']));
		$_POST['tags'] = mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_dvds_groups.tag_id) as tag from $config[tables_prefix]tags_dvds_groups where $config[tables_prefix]tags_dvds_groups.dvd_group_id=? order by id asc", $_POST['dvd_group_id']));
		$_POST['models'] = mr2array_list(sql_pr("select (select title from $config[tables_prefix]models where model_id=$config[tables_prefix]models_dvds_groups.model_id) as title from $config[tables_prefix]models_dvds_groups where $config[tables_prefix]models_dvds_groups.dvd_group_id=? order by id asc", $_POST['dvd_group_id']));
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$selector = "$table_key_name as object_id, $title_selector as title, $dir_selector as dir";
if ($desc_selector != '')
{
	$selector .= ", $desc_selector as description";
}
foreach ($languages as $language)
{
	$selector .= ", {$title_selector}_$language[code] as title_$language[code], {$dir_selector}_$language[code] as dir_$language[code]";
	if ($desc_selector <> '')
	{
		$selector .= ", {$desc_selector}_$language[code] as description_$language[code]";
		if ($language['translation_scope'] == 0)
		{
			$selector .= ", case when {$title_selector}_$language[code]='' or ($desc_selector<>'' and {$desc_selector}_$language[code]='') then 0 else char_length({$title_selector}_$language[code]) + char_length({$desc_selector}_$language[code]) end as ok_$language[code]";
		} else
		{
			$selector .= ", case when {$title_selector}_$language[code]='' then 0 else char_length({$title_selector}_$language[code]) end as ok_$language[code]";
		}
	} else
	{
		$selector .= ", case when {$title_selector}_$language[code]='' then 0 else char_length({$title_selector}_$language[code]) end as ok_$language[code]";
	}
}

if ($table_name <> '')
{
	$total_num = mr2number(sql("select count(*) from $table_name $where"));
	if (($_SESSION['save'][$page_name][$list_grouping]['from'] >= $total_num || $_SESSION['save'][$page_name][$list_grouping]['from'] < 0) || ($_SESSION['save'][$page_name][$list_grouping]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
	{
		$_SESSION['save'][$page_name][$list_grouping]['from'] = 0;
	}
	$data = mr2array(sql("select $selector from $table_name $where order by $sort_by limit " . $_SESSION['save'][$page_name][$list_grouping]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
	foreach ($data as &$item)
	{
		foreach ($languages as $language)
		{
			if ($item["ok_$language[code]"] == 0)
			{
				$item["ok_$language[code]"] = $lang['common']['undefined'];
			} else {
				$item["ok_$language[code]"] = $lang['settings']['translation_field_language_status_ok'] . " (" . $item["ok_$language[code]"] . ")";
			}
		}
	}
	unset($item);
} else
{
	$total_num = 0;
	$data = array();
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_administration.tpl');
$smarty->assign('list_languages', $languages);

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('supports_popups', 1);
}

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('table_fields', $table_fields);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('title_selector', $title_selector);
$smarty->assign('dir_selector', $dir_selector);
$smarty->assign('desc_selector', $desc_selector);
$smarty->assign('tiny_mce_key', $tiny_mce_key);
$smarty->assign('item_type', $item_type);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name][$list_grouping]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST[$table_key_name], $page_title_key));
} else
{
	$smarty->assign('page_title', $lang['settings']['submenu_option_translations_list']);
}

$smarty->display("layout.tpl");
