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

$languages = mr2array(sql("select * from $config[tables_prefix]languages order by title asc"));
$options = get_options();

for ($i = 1; $i <= 10; $i++)
{
	if ($options["TAG_FIELD_{$i}_NAME"] == '')
	{
		$options["TAG_FIELD_{$i}_NAME"] = $lang['settings']["custom_field_{$i}"];
	}
}

$list_status_values = array(
	0 => $lang['categorization']['tag_field_status_disabled'],
	1 => $lang['categorization']['tag_field_status_active'],
);

$table_fields = array();
$table_fields[] = array('id' => 'tag_id',        'title' => $lang['categorization']['tag_field_id'],         'is_default' => 1, 'type' => 'id');
$table_fields[] = array('id' => 'tag',           'title' => $lang['categorization']['tag_field_tag'],        'is_default' => 1, 'type' => 'text');
$table_fields[] = array('id' => 'tag_rename',    'title' => $lang['categorization']['tag_field_tag_rename'], 'is_default' => 1, 'type' => 'rename');
$table_fields[] = array('id' => 'tag_dir',       'title' => $lang['categorization']['tag_field_directory'],  'is_default' => 0, 'type' => 'text');
$table_fields[] = array('id' => 'synonyms',      'title' => $lang['categorization']['tag_field_synonyms'],   'is_default' => 0, 'type' => 'longtext');
$table_fields[] = array('id' => 'status_id',     'title' => $lang['categorization']['tag_field_status'],     'is_default' => 0, 'type' => 'choice', 'values' => $list_status_values);

for ($i = 1; $i <= 10; $i++)
{
	if ($options["ENABLE_TAG_FIELD_{$i}"] == 1)
	{
		$table_fields[] = array('id' => "custom{$i}", 'title' => $options["TAG_FIELD_{$i}_NAME"], 'is_default' => 0, 'type' => 'text');
	}
}

$table_fields[] = array('id' => 'videos_amount', 'title' => $lang['categorization']['tag_field_videos'],     'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'link' => 'videos.php?no_filter=true&se_tag=%id%', 'link_id' => 'tag', 'permission' => 'videos|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'albums_amount', 'title' => $lang['categorization']['tag_field_albums'],     'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'link' => 'albums.php?no_filter=true&se_tag=%id%', 'link_id' => 'tag', 'permission' => 'albums|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'posts_amount',  'title' => $lang['categorization']['tag_field_posts'],      'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'link' => 'posts.php?no_filter=true&se_tag=%id%', 'link_id' => 'tag', 'permission' => 'posts|view', 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'other_amount',  'title' => $lang['categorization']['tag_field_other'],      'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'all_amount',    'title' => $lang['categorization']['tag_field_all'],        'is_default' => 1, 'type' => 'number', 'show_in_sidebar' => 1, 'ifdisable_zero' => 1);
$table_fields[] = array('id' => 'added_date',    'title' => $lang['categorization']['tag_field_added_date'], 'is_default' => 0, 'type' => 'datetime', 'show_in_sidebar' => 1);

$sort_def_field = "tag_id";
$sort_def_direction = "desc";
$sort_array = array();
$sidebar_fields = array();
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
	if ($field['show_in_sidebar'] == 1)
	{
		$sidebar_fields[] = $field;
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
$search_fields[] = array('id' => 'tag_id',   'title' => $lang['categorization']['tag_field_id']);
$search_fields[] = array('id' => 'tag',      'title' => $lang['categorization']['tag_field_tag']);
$search_fields[] = array('id' => 'tag_dir',  'title' => $lang['categorization']['tag_field_directory']);
$search_fields[] = array('id' => 'synonyms', 'title' => $lang['categorization']['tag_field_synonyms']);
$search_fields[] = array('id' => 'custom',   'title' => $lang['common']['dg_filter_search_in_custom']);
if (count($languages) > 0)
{
	$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);
}

$table_name = "$config[tables_prefix]tags";
$table_key_name = "tag_id";

$table_selector_videos_count = "(select count(*) from $config[tables_prefix]tags_videos where $table_key_name=$table_name.$table_key_name)";
$table_selector_albums_count = "(select count(*) from $config[tables_prefix]tags_albums where $table_key_name=$table_name.$table_key_name)";
$table_selector_posts_count = "(select count(*) from $config[tables_prefix]tags_posts where $table_key_name=$table_name.$table_key_name)";
$table_selector_other_count = "($table_name.total_cs + $table_name.total_playlists + $table_name.total_models + $table_name.total_dvds + $table_name.total_dvd_groups)";
$table_selector_all_count = "($table_selector_videos_count + $table_selector_albums_count + $table_selector_posts_count + $table_selector_other_count)";

$table_selector = "$table_name.*";
$table_selector_single = "$table_selector, $table_selector_videos_count as videos_amount, $table_selector_albums_count as albums_amount, $table_selector_posts_count as posts_amount, $table_selector_other_count as other_amount, $table_selector_all_count as all_amount";

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
	$_SESSION['save'][$page_name]['se_text'] = '';
	$_SESSION['save'][$page_name]['se_status_id'] = '';
	$_SESSION['save'][$page_name]['se_field'] = '';
	$_SESSION['save'][$page_name]['se_usage'] = '';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text']))
	{
		$_SESSION['save'][$page_name]['se_text'] = trim($_GET['se_text']);
	}
	if (isset($_GET['se_status_id']))
	{
		$_SESSION['save'][$page_name]['se_status_id'] = trim($_GET['se_status_id']);
	}
	if (isset($_GET['se_field']))
	{
		$_SESSION['save'][$page_name]['se_field'] = trim($_GET['se_field']);
	}
	if (isset($_GET['se_usage']))
	{
		$_SESSION['save'][$page_name]['se_usage'] = trim($_GET['se_usage']);
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
			} elseif ($search_field['id'] == 'custom')
			{
				for ($i = 1; $i <= 10; $i++)
				{
					if ($options["ENABLE_TAG_FIELD_{$i}"] == 1)
					{
						$where_search .= " or $table_name.custom{$i} like '%$q%'";
					}
				}
			} elseif ($search_field['id'] == 'translations')
			{
				foreach ($languages as $language)
				{
					if (intval($_SESSION['save'][$page_name]["se_text_tag"]) == 1)
					{
						$where_search .= " or $table_name.tag_{$language['code']} like '%$q%'";
					}
					if (intval($_SESSION['save'][$page_name]["se_text_tag_dir"]) == 1)
					{
						$where_search .= " or $table_name.tag_dir_{$language['code']} like '%$q%'";
					}
				}
			} else
			{
				$where_search .= " or $table_name.$search_field[id] like '%$q%'";
			}
		}
	}
	$where .= " and ($where_search) ";
}

if ($_SESSION['save'][$page_name]['se_status_id'] == '0')
{
	$where .= " and $table_name.status_id=0";
	$table_filtered = 1;
} elseif ($_SESSION['save'][$page_name]['se_status_id'] == '1')
{
	$where .= " and $table_name.status_id=1";
	$table_filtered = 1;
}

switch ($_SESSION['save'][$page_name]['se_field'])
{
	case 'empty/synonyms':
	case 'empty/custom1':
	case 'empty/custom2':
	case 'empty/custom3':
	case 'empty/custom4':
	case 'empty/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 6) . "=''";
		$table_filtered = 1;
		break;
	case 'filled/synonyms':
	case 'filled/custom1':
	case 'filled/custom2':
	case 'filled/custom3':
	case 'filled/custom4':
	case 'filled/custom5':
		$where .= " and $table_name." . substr($_SESSION['save'][$page_name]['se_field'], 7) . "!=''";
		$table_filtered = 1;
		break;
}

switch ($_SESSION['save'][$page_name]['se_usage'])
{
	case 'used/videos':
		$where .= " and $table_selector_videos_count>0";
		$table_filtered = 1;
		break;
	case 'used/albums':
		$where .= " and $table_selector_albums_count>0";
		$table_filtered = 1;
		break;
	case 'used/posts':
		$where .= " and $table_selector_posts_count>0";
		$table_filtered = 1;
		break;
	case 'used/other':
		$where .= " and $table_selector_other_count>0";
		$table_filtered = 1;
		break;
	case 'used/all':
		$where .= " and $table_selector_all_count>0";
		$table_filtered = 1;
		break;
	case 'notused/videos':
		$where .= " and $table_selector_videos_count=0";
		$table_filtered = 1;
		break;
	case 'notused/albums':
		$where .= " and $table_selector_albums_count=0";
		$table_filtered = 1;
		break;
	case 'notused/posts':
		$where .= " and $table_selector_posts_count=0";
		$table_filtered = 1;
		break;
	case 'notused/other':
		$where .= " and $table_selector_other_count=0";
		$table_filtered = 1;
		break;
	case 'notused/all':
		$where .= " and $table_selector_all_count=0";
		$table_filtered = 1;
		break;
}

if ($where != '')
{
	$where = " where " . substr($where, 4);
}

$sort_by = $_SESSION['save'][$page_name]['sort_by'];
if ($sort_by == 'videos_amount')
{
	$sort_by = "$table_selector_videos_count";
} elseif ($sort_by == 'albums_amount')
{
	$sort_by = "$table_selector_albums_count";
} elseif ($sort_by == 'posts_amount')
{
	$sort_by = "$table_selector_posts_count";
} elseif ($sort_by == 'other_amount')
{
	$sort_by = "$table_selector_other_count";
} elseif ($sort_by == 'all_amount')
{
	$sort_by = "$table_selector_all_count";
} else
{
	$sort_by = "$table_name.$sort_by";
}
$sort_by .= ' ' . $_SESSION['save'][$page_name]['sort_direction'];

foreach ($table_fields as $k => $field)
{
	if ($field['is_enabled'] == 1)
	{
		if ($field['id'] == 'videos_amount')
		{
			$table_selector .= ", $table_selector_videos_count as videos_amount";
		}
		if ($field['id'] == 'albums_amount')
		{
			$table_selector .= ", $table_selector_albums_count as albums_amount";
		}
		if ($field['id'] == 'posts_amount')
		{
			$table_selector .= ", $table_selector_posts_count as posts_amount";
		}
		if ($field['id'] == 'other_amount')
		{
			$table_selector .= ", $table_selector_other_count as other_amount";
		}
		if ($field['id'] == 'all_amount')
		{
			$table_selector .= ", $table_selector_all_count as all_amount";
		}
	}
}

// =====================================================================================================================
// add new
// =====================================================================================================================

if ($_POST['action'] == 'add_new_complete')
{
	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	if (validate_field('empty', $_POST['tag'], $lang['categorization']['tag_field_tags']))
	{
		$tags_all = array();
		$synonyms_all = array();

		$temp = mr2array(sql("select tag_id, tag, synonyms from $table_name"));
		foreach ($temp as $tag)
		{
			$tags_all[mb_lowercase($tag['tag'])] = $tag['tag_id'];

			$temp_syn = explode(",", $tag['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn = trim($syn);
					if (strlen($syn) > 0)
					{
						$synonyms_all[mb_lowercase($syn)] = $tag['tag_id'];
					}
				}
			}
		}

		$lines = explode("\n", $_POST['tag']);
		foreach ($lines as $line)
		{
			if (strpos($line, ':') !== false)
			{
				$temp = explode(":", $line);

				$tag = trim($temp[0]);

				$duplicate_tag_id = $synonyms_all[mb_lowercase($tag)];
				if ($duplicate_tag_id > 0)
				{
					$errors[] = get_aa_error('tag_synonym_duplicated', $lang['categorization']['tag_field_tags'], $tag, $duplicate_tag_id);
				} else
				{
					$temp = explode(",", $temp[1]);
					if (is_array($temp))
					{
						$temp = array_map("trim", $temp);
						$temp = array_unique($temp);
						foreach ($temp as $synonym)
						{
							$duplicate_tag_id = $tags_all[mb_lowercase($synonym)];
							if ($duplicate_tag_id == 0)
							{
								$duplicate_tag_id = $synonyms_all[mb_lowercase($synonym)];
							}
							if ($duplicate_tag_id > 0)
							{
								$errors[] = get_aa_error('tag_synonym_duplicated', $lang['categorization']['tag_field_tags'], $synonym, $duplicate_tag_id);
							}
						}
					}
				}
			} else
			{
				$temp = explode(",", $line);
				if (is_array($temp))
				{
					$temp = array_map("trim", $temp);
					$temp = array_unique($temp);
					foreach ($temp as $synonym)
					{
						$duplicate_tag_id = $tags_all[mb_lowercase($synonym)];
						if ($duplicate_tag_id == 0)
						{
							$duplicate_tag_id = $synonyms_all[mb_lowercase($synonym)];
						}
						if ($duplicate_tag_id > 0)
						{
							$errors[] = get_aa_error('tag_synonym_duplicated', $lang['categorization']['tag_field_tags'], $synonym, $duplicate_tag_id);
						}
					}
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$inserted_synonyms = array();

		$lines = explode("\n", $_POST['tag']);
		foreach ($lines as $line)
		{
			if (strpos($line, ':') !== false)
			{
				$temp = explode(":", $line);
				$tag = trim($temp[0]);
				$synonyms = array();

				$temp = explode(",", $temp[1]);
				if (is_array($temp))
				{
					$temp = array_map("trim", $temp);
					$temp = array_unique($temp);
					foreach ($temp as $synonym)
					{
						if (in_array(mb_lowercase($synonym), $inserted_synonyms))
						{
							continue;
						}
						if (strlen($synonym) > 0)
						{
							$synonyms[] = $synonym;
							$inserted_synonyms[] = mb_lowercase($synonym);
						}
					}
				}

				$tag_info = mr2array_single(sql_pr("select * from $table_name where tag=?", $tag));
				if ($tag_info['tag_id'] > 0)
				{
					$tag_synonyms = array();
					if ($tag_info['synonyms'] != '')
					{
						$tag_synonyms = array_map("trim", explode(",", $tag_info['synonyms']));
					}
					foreach ($synonyms as $synonym)
					{
						if (in_array($synonym, $tag_synonyms))
						{
							continue;
						}
						$tag_synonyms[] = $synonym;
					}
					sql_pr("update $table_name set synonyms=? where tag_id=?", implode(', ', $tag_synonyms), $tag_info['tag_id']);
				} else
				{
					$tag_dir = get_correct_dir_name($tag);
					$temp_dir = $tag_dir;
					for ($i = 2; $i < 999999; $i++)
					{
						if (mr2number(sql_pr("select count(*) from $table_name where tag_dir=?", $temp_dir)) == 0)
						{
							$tag_dir = $temp_dir;
							break;
						}
						$temp_dir = $tag_dir . $i;
					}
					sql_pr("insert into $table_name set tag=?, tag_dir=?, synonyms=?, added_date=?", $tag, $tag_dir, implode(', ', $synonyms), date("Y-m-d H:i:s"));
				}
			} else
			{
				$temp = explode(",", $line);
				if (is_array($temp))
				{
					$temp = array_map("trim", $temp);
					$temp = array_unique($temp);
					$inserted_tags = array();
					foreach ($temp as $tag)
					{
						$tag = trim($tag);
						if (in_array(mb_lowercase($tag), $inserted_tags))
						{
							continue;
						}
						if (strlen($tag) > 0)
						{
							$result = sql_pr("select tag_id from $table_name where tag=?", $tag);
							if (mr2rows($result) == 0)
							{
								$tag_dir = get_correct_dir_name($tag);
								$temp_dir = $tag_dir;
								for ($i = 2; $i < 999999; $i++)
								{
									if (mr2number(sql_pr("select count(*) from $table_name where tag_dir=?", $temp_dir)) == 0)
									{
										$tag_dir = $temp_dir;
										break;
									}
									$temp_dir = $tag_dir . $i;
								}
								sql_pr("insert into $table_name set tag=?, tag_dir=?, added_date=?", $tag, $tag_dir, date("Y-m-d H:i:s"));
							}
							$inserted_tags[] = mb_lowercase($tag);
						}
					}
				}
			}
		}
		$_SESSION['messages'][] = $lang['common']['success_message_added'];
		return_ajax_success($page_name);
	} else
	{
		return_ajax_errors($errors);
	}
}

// =====================================================================================================================
// edit
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

	validate_field('empty', $_POST['tag'], $lang['categorization']['tag_field_tag']);
	if ($_POST['synonyms'] <> '')
	{
		$temp_syn = explode(",", $_POST['synonyms']);
		if (is_array($temp_syn))
		{
			foreach ($temp_syn as $syn)
			{
				$syn = trim($syn);
				if (strlen($syn) > 0)
				{
					$duplicate_tag_id = mr2number(sql_pr("select tag_id from $table_name where tag=? limit 1", $syn));
					if ($duplicate_tag_id > 0)
					{
						$errors[] = get_aa_error('tag_synonym_duplicated', $lang['categorization']['tag_field_synonyms'], $syn, $duplicate_tag_id);
					} else
					{
						$duplicate_tags = mr2array(sql_pr("select tag_id, synonyms from $table_name where tag_id<>? and synonyms like ?", intval($_POST['item_id']), "%$syn%"));
						foreach ($duplicate_tags as $duplicate_tag)
						{
							$duplicate_tag_synonyms = explode(",", $duplicate_tag['synonyms']);
							foreach ($duplicate_tag_synonyms as $duplicate_tag_synonym)
							{
								if (mb_lowercase($syn) == mb_lowercase(trim($duplicate_tag_synonym)))
								{
									$duplicate_tag_id = $duplicate_tag['tag_id'];
									break 2;
								}
							}
						}
						if ($duplicate_tag_id > 0)
						{
							$errors[] = get_aa_error('tag_synonym_duplicated', $lang['categorization']['tag_field_synonyms'], $syn, $duplicate_tag_id);
						}
					}
				}
			}
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['synonyms'] <> '')
		{
			$_POST['synonyms'] = mb_remove_duplicates($_POST['synonyms'], ',');
		}

		$item_id = intval($_POST['item_id']);
		$old_tag = mr2string(sql("select tag from $table_name where $table_key_name=$item_id"));

		$next_item_id = 0;
		if (isset($_POST['save_and_edit']))
		{
			$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where order by $sort_by, $table_name.$table_key_name"));
			$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
			if ($next_item_id == 0)
			{
				$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where order by $sort_by limit 1"));
			}
			if ($next_item_id == $item_id)
			{
				$next_item_id = 0;
			}
		}

		if ($old_tag <> $_POST['tag'])
		{
			$new_tag_id = mr2number(sql_pr("select tag_id from $table_name where tag=?", $_POST['tag']));
			if ($new_tag_id == 0)
			{
				if ($_POST['tag_dir'] == '')
				{
					$_POST['tag_dir'] = get_correct_dir_name($_POST['tag']);
				}
				$tag_dir = $_POST['tag_dir'];
				$temp_dir = $tag_dir;
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags where tag_dir=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
					{
						$tag_dir = $temp_dir;
						break;
					}
					$temp_dir = $tag_dir . $i;
				}
				if (intval($options['TAGS_ADD_SYNONYMS_ON_RENAME']) == 1)
				{
					$_POST['synonyms'] = mb_remove_duplicates($_POST['synonyms'] . ", $old_tag", ',');
				}
				sql_pr("update $table_name set tag=?, tag_dir=?, synonyms=?, status_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=? where $table_key_name=?",
					$_POST['tag'], $tag_dir, $_POST['synonyms'], intval($_POST['status_id']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $item_id);
			} else
			{
				if ($new_tag_id != $item_id)
				{
					$new_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $new_tag_id));

					if ($_POST['synonyms'] != '')
					{
						sql_pr("update $table_name set synonyms=? where tag_id=?", mb_remove_duplicates($_POST['synonyms'] . ', ' . $new_data['synonyms'], ','), $new_tag_id);
					}

					sql_pr("delete from $table_name where $table_key_name=?", $item_id);

					$data = mr2array_list(sql("select distinct video_id from $config[tables_prefix]tags_videos as p where exists (select * from $config[tables_prefix]tags_videos where video_id=p.video_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_videos where video_id=p.video_id and tag_id=$item_id)"));
					foreach ($data as $video_id)
					{
						sql("delete from $config[tables_prefix]tags_videos where video_id=$video_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct album_id from $config[tables_prefix]tags_albums as p where exists (select * from $config[tables_prefix]tags_albums where album_id=p.album_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_albums where album_id=p.album_id and tag_id=$item_id)"));
					foreach ($data as $album_id)
					{
						sql("delete from $config[tables_prefix]tags_albums where album_id=$album_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct post_id from $config[tables_prefix]tags_posts as p where exists (select * from $config[tables_prefix]tags_posts where post_id=p.post_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_posts where post_id=p.post_id and tag_id=$item_id)"));
					foreach ($data as $post_id)
					{
						sql("delete from $config[tables_prefix]tags_posts where post_id=$post_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct playlist_id from $config[tables_prefix]tags_playlists as p where exists (select * from $config[tables_prefix]tags_playlists where playlist_id=p.playlist_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_playlists where playlist_id=p.playlist_id and tag_id=$item_id)"));
					foreach ($data as $playlist_id)
					{
						sql("delete from $config[tables_prefix]tags_playlists where playlist_id=$playlist_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct content_source_id from $config[tables_prefix]tags_content_sources as p where exists (select * from $config[tables_prefix]tags_content_sources where content_source_id=p.content_source_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_content_sources where content_source_id=p.content_source_id and tag_id=$item_id)"));
					foreach ($data as $content_source_id)
					{
						sql("delete from $config[tables_prefix]tags_content_sources where content_source_id=$content_source_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct model_id from $config[tables_prefix]tags_models as p where exists (select * from $config[tables_prefix]tags_models where model_id=p.model_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_models where model_id=p.model_id and tag_id=$item_id)"));
					foreach ($data as $model_id)
					{
						sql("delete from $config[tables_prefix]tags_models where model_id=$model_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct dvd_id from $config[tables_prefix]tags_dvds as p where exists (select * from $config[tables_prefix]tags_dvds where dvd_id=p.dvd_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_dvds where dvd_id=p.dvd_id and tag_id=$item_id)"));
					foreach ($data as $dvd_id)
					{
						sql("delete from $config[tables_prefix]tags_dvds where dvd_id=$dvd_id and tag_id=$item_id");
					}
					$data = mr2array_list(sql("select distinct dvd_group_id from $config[tables_prefix]tags_dvds_groups as p where exists (select * from $config[tables_prefix]tags_dvds_groups where dvd_group_id=p.dvd_group_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_dvds_groups where dvd_group_id=p.dvd_group_id and tag_id=$item_id)"));
					foreach ($data as $dvd_group_id)
					{
						sql("delete from $config[tables_prefix]tags_dvds_groups where dvd_group_id=$dvd_group_id and tag_id=$item_id");
					}

					sql_pr("update $config[tables_prefix]tags_videos set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_albums set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_posts set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_playlists set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_content_sources set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_models set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_dvds set $table_key_name=$new_tag_id where $table_key_name=$item_id");
					sql_pr("update $config[tables_prefix]tags_dvds_groups set $table_key_name=$new_tag_id where $table_key_name=$item_id");
				} else
				{
					sql_pr("update $table_name set tag=?, synonyms=?, status_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=? where $table_key_name=?",
						$_POST['tag'], $_POST['synonyms'], intval($_POST['status_id']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $item_id);
				}
			}
		} else
		{
			if ($_POST['tag_dir'] == '')
			{
				$_POST['tag_dir'] = get_correct_dir_name($_POST['tag']);
			}
			$tag_dir = $_POST['tag_dir'];
			$temp_dir = $tag_dir;
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags where tag_dir=? and $table_key_name<>?", $temp_dir, $item_id)) == 0)
				{
					$tag_dir = $temp_dir;
					break;
				}
				$temp_dir = $tag_dir . $i;
			}
			sql_pr("update $table_name set synonyms=?, tag_dir=?, status_id=?, custom1=?, custom2=?, custom3=?, custom4=?, custom5=? where $table_key_name=?",
				$_POST['synonyms'], $tag_dir, intval($_POST['status_id']), $_POST['custom1'], $_POST['custom2'], $_POST['custom3'], $_POST['custom4'], $_POST['custom5'], $item_id);
		}

		$_SESSION['messages'][] = $lang['common']['success_message_modified'];

		if (isset($_POST['save_and_edit']))
		{
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
// rename
// =====================================================================================================================

if (isset($_REQUEST['save_rename']))
{
	$tag_ids = [0];
	foreach ($_REQUEST as $param => $value)
	{
		if (strpos($param, 'rename_') === 0)
		{
			$tag_ids[] = intval(substr($param, 7));
		}
	}
	$tag_ids_str = implode(',', $tag_ids);

	$tags = mr2array(sql("select tag_id, tag, synonyms from $table_name where $table_key_name in ($tag_ids_str)"));
	foreach ($tags as $tag)
	{
		$tag_id = $tag['tag_id'];
		$old_tag = $tag['tag'];
		$new_tag = trim($_REQUEST["rename_$tag_id"]);
		if (strlen($new_tag) > 0 && $new_tag <> $old_tag)
		{
			$new_tag_id = mr2number(sql_pr("select tag_id from $table_name where tag=?", $new_tag));
			if ($new_tag_id == 0)
			{
				$tag_dir = get_correct_dir_name($new_tag);
				$temp_dir = $tag_dir;
				for ($i = 2; $i < 999999; $i++)
				{
					if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags where tag_dir=? and $table_key_name<>?", $temp_dir, $tag_id)) == 0)
					{
						$tag_dir = $temp_dir;
						break;
					}
					$temp_dir = $tag_dir . $i;
				}
				if (intval($options['TAGS_ADD_SYNONYMS_ON_RENAME']) == 1)
				{
					$tag_synonyms = mb_remove_duplicates($tag['synonyms'] . ", $old_tag", ',');
					sql_pr("update $table_name set tag=?, tag_dir=?, synonyms=? where $table_key_name=?", $new_tag, $tag_dir, $tag_synonyms, $tag_id);
				} else
				{
					sql_pr("update $table_name set tag=?, tag_dir=? where $table_key_name=?", $new_tag, $tag_dir, $tag_id);
				}
			} else
			{
				if ($new_tag_id != $tag_id)
				{
					$new_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $new_tag_id));
					$old_data = mr2array_single(sql_pr("select * from $table_name where $table_key_name=?", $tag_id));

					if ($old_data['synonyms'] != '' || intval($options['TAGS_ADD_SYNONYMS_ON_RENAME']) == 1)
					{
						$new_tag_synonyms = mb_remove_duplicates($old_data['synonyms'] . ', ' . $new_data['synonyms'], ',');
						if (intval($options['TAGS_ADD_SYNONYMS_ON_RENAME']) == 1)
						{
							$new_tag_synonyms = mb_remove_duplicates($new_tag_synonyms . ", $old_tag", ',');
						}
						sql_pr("update $table_name set synonyms=? where tag_id=?", $new_tag_synonyms, $new_tag_id);
					}

					sql_pr("delete from $table_name where $table_key_name=?", $tag_id);

					$data = mr2array_list(sql("select distinct video_id from $config[tables_prefix]tags_videos as p where exists (select * from $config[tables_prefix]tags_videos where video_id=p.video_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_videos where video_id=p.video_id and tag_id=$tag_id)"));
					foreach ($data as $video_id)
					{
						sql("delete from $config[tables_prefix]tags_videos where video_id=$video_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct album_id from $config[tables_prefix]tags_albums as p where exists (select * from $config[tables_prefix]tags_albums where album_id=p.album_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_albums where album_id=p.album_id and tag_id=$tag_id)"));
					foreach ($data as $album_id)
					{
						sql("delete from $config[tables_prefix]tags_albums where album_id=$album_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct post_id from $config[tables_prefix]tags_posts as p where exists (select * from $config[tables_prefix]tags_posts where post_id=p.post_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_posts where post_id=p.post_id and tag_id=$tag_id)"));
					foreach ($data as $post_id)
					{
						sql("delete from $config[tables_prefix]tags_posts where post_id=$post_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct playlist_id from $config[tables_prefix]tags_playlists as p where exists (select * from $config[tables_prefix]tags_playlists where playlist_id=p.playlist_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_playlists where playlist_id=p.playlist_id and tag_id=$tag_id)"));
					foreach ($data as $playlist_id)
					{
						sql("delete from $config[tables_prefix]tags_playlists where playlist_id=$playlist_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct content_source_id from $config[tables_prefix]tags_content_sources as p where exists (select * from $config[tables_prefix]tags_content_sources where content_source_id=p.content_source_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_content_sources where content_source_id=p.content_source_id and tag_id=$tag_id)"));
					foreach ($data as $content_source_id)
					{
						sql("delete from $config[tables_prefix]tags_content_sources where content_source_id=$content_source_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct model_id from $config[tables_prefix]tags_models as p where exists (select * from $config[tables_prefix]tags_models where model_id=p.model_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_models where model_id=p.model_id and tag_id=$tag_id)"));
					foreach ($data as $model_id)
					{
						sql("delete from $config[tables_prefix]tags_models where model_id=$model_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct dvd_id from $config[tables_prefix]tags_dvds as p where exists (select * from $config[tables_prefix]tags_dvds where dvd_id=p.dvd_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_dvds where dvd_id=p.dvd_id and tag_id=$tag_id)"));
					foreach ($data as $dvd_id)
					{
						sql("delete from $config[tables_prefix]tags_dvds where dvd_id=$dvd_id and tag_id=$tag_id");
					}
					$data = mr2array_list(sql("select distinct dvd_group_id from $config[tables_prefix]tags_dvds_groups as p where exists (select * from $config[tables_prefix]tags_dvds_groups where dvd_group_id=p.dvd_group_id and tag_id=$new_tag_id) and exists (select * from $config[tables_prefix]tags_dvds_groups where dvd_group_id=p.dvd_group_id and tag_id=$tag_id)"));
					foreach ($data as $dvd_group_id)
					{
						sql("delete from $config[tables_prefix]tags_dvds_groups where dvd_group_id=$dvd_group_id and tag_id=$tag_id");
					}

					sql_pr("update $config[tables_prefix]tags_videos set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_albums set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_posts set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_playlists set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_content_sources set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_models set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_dvds set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
					sql_pr("update $config[tables_prefix]tags_dvds_groups set $table_key_name=$new_tag_id where $table_key_name=$tag_id");
				} else
				{
					sql_pr("update $table_name set tag=? where $table_key_name=?", $new_tag, $tag_id);
				}
			}
		}
	}
	$_SESSION['messages'][] = $lang['common']['success_message_completed'];
	return_ajax_success($page_name);
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
	$row_select = implode(",", array_map("intval", $_REQUEST['row_select']));
	if ($_REQUEST['batch_action'] == 'delete')
	{
		sql("delete from $table_name where $table_key_name in ($row_select)");
		sql("delete from $config[tables_prefix]tags_videos where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_albums where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_posts where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_playlists where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_content_sources where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_dvds where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_dvds_groups where tag_id in ($row_select)");
		sql("delete from $config[tables_prefix]tags_models where tag_id in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_removed'];
	} elseif ($_REQUEST['batch_action'] == 'deactivate')
	{
		sql("update $table_name set status_id=0 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_deactivated'];
	} elseif ($_REQUEST['batch_action'] == 'activate')
	{
		sql("update $table_name set status_id=1 where $table_key_name in ($row_select)");
		$_SESSION['messages'][] = $lang['common']['success_message_activated'];
	}
	return_ajax_success($page_name);
}

// =====================================================================================================================
// view item
// =====================================================================================================================

if ($_GET['action'] == 'change' && intval($_GET['item_id']) > 0)
{
	$_POST = mr2array_single(sql_pr("select $table_selector_single from $table_projector where $table_name.$table_key_name=?", intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}
}

// =====================================================================================================================
// list items
// =====================================================================================================================

if ($_GET['action'] == '')
{
	$total_num = mr2number(sql("select count(*) from $table_projector $where"));
	if (($_SESSION['save'][$page_name]['from'] >= $total_num || $_SESSION['save'][$page_name]['from'] < 0) || ($_SESSION['save'][$page_name]['from'] > 0 && $total_num <= $_SESSION['save'][$page_name]['num_on_page']))
	{
		$_SESSION['save'][$page_name]['from'] = 0;
	}

	$data = mr2array(sql("select $table_selector from $table_projector $where order by $sort_by limit " . $_SESSION['save'][$page_name]['from'] . ", " . $_SESSION['save'][$page_name]['num_on_page']));
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_categorization.tpl');
$smarty->assign('options', $options);

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
$smarty->assign('table_fields', $table_fields);
$smarty->assign('table_filtered', $table_filtered);
$smarty->assign('search_fields', $search_fields);
$smarty->assign('total_num', $total_num);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));
$smarty->assign('nav', get_navigation($total_num, $_SESSION['save'][$page_name]['num_on_page'], $_SESSION['save'][$page_name]['from'], "$page_name?", 14));

if ($_REQUEST['action'] == 'add_new')
{
	$smarty->assign('page_title', $lang['categorization']['tag_add']);
} elseif ($_REQUEST['action'] == 'change')
{
	$smarty->assign('page_title', str_replace("%1%", $_POST['tag'], $lang['categorization']['tag_edit']));
	$smarty->assign('sidebar_fields', $sidebar_fields);
} else
{
	$smarty->assign('page_title', $lang['categorization']['submenu_option_tags_list']);
}

$smarty->display("layout.tpl");
