<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function categories_autogenerationInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins");
		chmod("$config[project_path]/admin/data/plugins", 0777);
	}
	$plugin_path = "$config[project_path]/admin/data/plugins/categories_autogeneration";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path);
		chmod($plugin_path, 0777);
	}
}

function categories_autogenerationIsEnabled()
{
	global $config;

	categories_autogenerationInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/categories_autogeneration";
	if (is_file("$plugin_path/data.dat"))
	{
		$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));
		if ($data['enabled'] == 1)
		{
			return true;
		}
	}
	return false;
}

function categories_autogenerationShow()
{
	global $config, $errors, $lang, $page_name;

	categories_autogenerationInit();
	$plugin_path = "$config[project_path]/admin/data/plugins/categories_autogeneration";

	$errors = null;

	if ($_POST['action'] == 'save')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (intval($_POST['lenient']) == 2)
		{
			validate_field('empty', $_POST['lenient_list'], $lang['plugins']['categories_autogeneration']['field_lenient']);
		}

		if (!is_array($errors))
		{
			$data = array();
			$data['enable_for_videos'] = intval($_POST['enable_for_videos']);
			$data['enable_for_albums'] = intval($_POST['enable_for_albums']);
			$data['lenient'] = intval($_POST['lenient']);
			$data['lenient_list'] = trim($_POST['lenient_list']);

			if (intval($_POST['enable_for_videos']) + intval($_POST['enable_for_albums']) > 0)
			{
				$data['enabled'] = 1;
			} else
			{
				$data['enabled'] = 0;
			}

			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			if (!is_file("$plugin_path/data.dat"))
			{
				$errors[] = get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path/data.dat"));
			}

			if (!is_array($errors))
			{
				return_ajax_success("$page_name?plugin_id=categories_autogeneration");
			} else
			{
				return_ajax_errors($errors);
			}
		} else
		{
			return_ajax_errors($errors);
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][] = bb_code_process(get_aa_error('filesystem_permission_write', str_replace("//", "/", "$plugin_path")));
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST = array();
		$_POST['enabled'] = 0;
		$_POST['enable_for_videos'] = 0;
		$_POST['enable_for_albums'] = 0;
		$_POST['lenient'] = 0;
		$_POST['lenient_list'] = '';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else
	{
		$_POST = @unserialize(@file_get_contents("$plugin_path/data.dat"));
	}
}

function categories_autogenerationGenerate($title, $description, $tags, $categories_all, $lenient, $lenient_list)
{
	$categories_found = array();

	$punkt = array(".", ",", ":", ";", "-", "+", "=", "'", "\"", "(", ")", "`");
	foreach ($categories_all as $k => $v)
	{
		if (strpos($k, '*') !== false)
		{
			$regexp = str_replace('\*', '\w*', preg_quote($k, "/"));
			if (preg_match("/$regexp/iu", $title) || preg_match("/$regexp/iu", $description))
			{
				$categories_found[$k] = $v;
			} else
			{
				$temp = explode(",", $tags);
				foreach ($temp as $tag)
				{
					if (preg_match("/^$regexp$/iu", $tag))
					{
						$categories_found[$k] = $v;
						break;
					}
				}
			}
		} elseif (strpos($k, ' ') !== false || strpos($k, '-') !== false)
		{
			if (strpos($title, $k) !== false || strpos($description, $k) !== false)
			{
				$categories_found[$k] = $v;
			} elseif ($lenient == 1 || ($lenient == 2 && is_array($lenient_list) && $lenient_list[$k] > 0))
			{
				$lenient_words = explode(' ', $k);
				$all_words_match = true;
				foreach ($lenient_words as $lenient_word)
				{
					if (strpos($title, $lenient_word) === false && strpos($description, $lenient_word) === false)
					{
						$all_words_match = false;
						break;
					}
				}
				if ($all_words_match)
				{
					$categories_found[$k] = $v;
				}
			}
		}
	}
	$title = str_replace($punkt, " ", $title);
	$description = str_replace($punkt, " ", $description);

	$temp = array_merge(explode(" ", $title), explode(" ", $description), explode(",", $tags));
	foreach ($temp as $candidate)
	{
		$candidate = trim($candidate);
		if ($candidate === '' || $categories_found[$candidate] > 0)
		{
			continue;
		}
		if ($categories_all[$candidate] > 0)
		{
			$categories_found[$candidate] = $categories_all[$candidate];
		}
	}
	return $categories_found;
}

if ($_SERVER['argv'][1] == 'exec' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	require_once 'setup.php';
	require_once 'functions_base.php';

	$object_type = $_SERVER['argv'][2];
	$object_id = intval($_SERVER['argv'][3]);

	$plugin_path = "$config[project_path]/admin/data/plugins/categories_autogeneration";
	$data = @unserialize(@file_get_contents("$plugin_path/data.dat"));

	$lenient_list = array();
	if ($data['lenient'] == 2)
	{
		$temp = explode(',', $data['lenient_list']);
		foreach ($temp as $list_item)
		{
			$lenient_list[mb_lowercase(trim($list_item))] = 1;
		}
	}

	if ($object_type == 'video' && $data['enable_for_videos'] > 0)
	{
		$video_id = $object_id;
		if ($data['enable_for_videos'] == 2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_videos where video_id=?", $video_id)) > 0)
			{
				echo "Video already has categories, generation skipped";
				return;
			}
		}
		$res_video = mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=$video_id"));
		$res_video['tags'] = implode(",", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_videos.tag_id) as tag from $config[tables_prefix]tags_videos where $config[tables_prefix]tags_videos.video_id=?", $video_id)));

		$categories_all = array();
		$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
		foreach ($temp as $category)
		{
			$categories_all[mb_lowercase($category['title'])] = $category['category_id'];
			$temp_syn = explode(",", $category['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn = trim($syn);
					if (strlen($syn) > 0)
					{
						$categories_all[mb_lowercase($syn)] = $category['category_id'];
					}
				}
			}
		}

		$categories_found = categories_autogenerationGenerate(mb_lowercase($res_video['title']), mb_lowercase($res_video['description']), mb_lowercase($res_video['tags']), $categories_all, $data['lenient'], $lenient_list);
		$categories_found_str = '';
		$categories_added = array();
		foreach ($categories_found as $k => $category_id)
		{
			if (in_array($category_id, $categories_added))
			{
				continue;
			}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_videos where category_id=? and video_id=?", $category_id, $video_id)) == 0)
			{
				sql_pr("insert into $config[tables_prefix]categories_videos set category_id=?, video_id=?", $category_id, $video_id);
				$categories_found_str .= "$k, ";
			}
			$categories_added[] = $category_id;
		}
		if ($categories_found_str <> '')
		{
			echo "Autogenerated categories: " . substr($categories_found_str, 0, -2);
		} else
		{
			echo "No autogenerated categories";
		}
	} elseif ($object_type == 'album' && $data['enable_for_albums'] > 0)
	{
		$album_id = $object_id;
		if ($data['enable_for_albums'] == 2)
		{
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_albums where album_id=?", $album_id)) > 0)
			{
				echo "Album already has categories, generation skipped";
				return;
			}
		}
		$res_album = mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));
		$res_album['tags'] = implode(",", mr2array_list(sql_pr("select (select tag from $config[tables_prefix]tags where tag_id=$config[tables_prefix]tags_albums.tag_id) as tag from $config[tables_prefix]tags_albums where $config[tables_prefix]tags_albums.album_id=?", $album_id)));

		$categories_all = array();
		$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
		foreach ($temp as $category)
		{
			$categories_all[mb_lowercase($category['title'])] = $category['category_id'];
			$temp_syn = explode(",", $category['synonyms']);
			if (is_array($temp_syn))
			{
				foreach ($temp_syn as $syn)
				{
					$syn = trim($syn);
					if (strlen($syn) > 0)
					{
						$categories_all[mb_lowercase($syn)] = $category['category_id'];
					}
				}
			}
		}

		$categories_found = categories_autogenerationGenerate(mb_lowercase($res_album['title']), mb_lowercase($res_album['description']), mb_lowercase($res_album['tags']), $categories_all, $data['lenient'], $lenient_list);
		$categories_found_str = '';
		$categories_added = array();
		foreach ($categories_found as $k => $category_id)
		{
			if (in_array($category_id, $categories_added))
			{
				continue;
			}
			if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories_albums where category_id=? and album_id=?", $category_id, $album_id)) == 0)
			{
				sql_pr("insert into $config[tables_prefix]categories_albums set category_id=?, album_id=?", $category_id, $album_id);
				$categories_found_str .= "$k, ";
			}
			$categories_added[] = $category_id;
		}
		if ($categories_found_str <> '')
		{
			echo "Autogenerated categories: " . substr($categories_found_str, 0, -2);
		} else
		{
			echo "No autogenerated categories";
		}
	}
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
