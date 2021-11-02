<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_base.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';
require_once 'include/database_selectors.php';

$table_name = "$config[tables_prefix]videos";
$table_key_name = "video_id";
$table_projector = "$table_name";

$table_name_categories = "$config[tables_prefix]categories_videos";
$table_name_tags = "$config[tables_prefix]tags_videos";
$table_name_models = "$config[tables_prefix]models_videos";

$errors = null;

$item_id = intval($_REQUEST['item_id']);
if ($item_id < 1)
{
	header("Location: videos.php");
	die;
}

$data_video = mr2array_single(sql_pr("select * from $table_name where status_id in (0,1) and video_id=?", $item_id));
if (count($data_video) == 0)
{
	header("Location: videos.php");
	die;
} else
{
	if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
	{
		if ($data_video['admin_user_id'] <> $_SESSION['userdata']['user_id'])
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
	{
		if ($data_video['status_id'] <> 0)
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
	if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
	{
		if ($data_video['admin_flag_id'] == 0 || !in_array($data_video['admin_flag_id'], array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with']))))
		{
			header("Location: error.php?error=permission_denied");
			die;
		}
	}
}

$list_formats_overview = mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=1"));
$list_formats_timeline = mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=2"));
$list_formats_posters = mr2array(sql("select *, concat(title, case when image_type=1 then ' - WebP' else ' - JPG' end) as title from $config[tables_prefix]formats_screenshots where status_id=1 and group_id=3"));
$list_formats_videos = mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2)"));

$list_formats_videos_timelined = array();
$video_formats = get_video_formats($data_video['video_id'], $data_video['file_formats']);
foreach ($video_formats as $format_rec)
{
	if ($format_rec['timeline_screen_amount'] > 0)
	{
		foreach ($list_formats_videos as $format_video)
		{
			if ($format_rec['postfix'] == $format_video['postfix'])
			{
				$format_video['timeline_screen_amount'] = $format_rec['timeline_screen_amount'];
				$format_video['timeline_screen_interval'] = $format_rec['timeline_screen_interval'];
				$list_formats_videos_timelined[] = $format_video;
				break;
			}
		}
	}
}

$options = get_options();

$website_ui_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/website_ui_params.dat"));
$languages = mr2array(sql("select * from $config[tables_prefix]languages"));

if ($_POST['action'] == 'change_screenshots')
{
	$group_id = intval($_POST['group_id']);

	$min_image_size = array(0 => 0, 1 => 0);
	if ($options['VIDEO_VALIDATE_SCREENSHOT_SIZES'] == 1)
	{
		$sizes = mr2array_list(sql_pr("select size from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=?", $group_id));
		foreach ($sizes as $size)
		{
			$temp_size = explode("x", $size);
			if (intval($temp_size[0]) > $min_image_size[0])
			{
				$min_image_size[0] = intval($temp_size[0]);
			}
			if (intval($temp_size[1]) > $min_image_size[1])
			{
				$min_image_size[1] = intval($temp_size[1]);
			}
		}
	}

	$screen_amount = 0;
	if ($group_id == 1)
	{
		$screen_amount = $data_video['screen_amount'];
	} elseif ($group_id == 2)
	{
		$screen_amount = $data_video['poster_amount'];
	}
	if ($_POST['replace_screenshots_hash'])
	{
		validate_field('archive_or_images', 'replace_screenshots', $lang['videos']['screenshots_mgmt_field_replace'], array('is_required' => 1, 'image_types' => 'jpg', 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
	} else
	{
		if (is_array($_REQUEST['delete']))
		{
			$delete_pos = array_map("intval", $_REQUEST['delete']);
		} else
		{
			$delete_pos = array();
		}

		for ($i = 1; $i <= $screen_amount; $i++)
		{
			if (!in_array($i, $delete_pos) && $_POST["file_$i"])
			{
				validate_field('file', "file_$i", str_replace("%1%", $i, $lang['videos']['screenshots_mgmt_file_title_screenshot']), array('is_image' => '1', 'min_image_size' => "$min_image_size[0]x$min_image_size[1]"));
			}
		}

		if ($group_id == 1)
		{
			if (count($delete_pos) >= $screen_amount)
			{
				$errors[] = get_aa_error('video_screenshot_delete_all_forbidded');
			}
		}
	}
	if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_status', $_SESSION['permissions']))
	{
		if (intval($_POST['status_id']) == 1)
		{
			if ($data_video['title'] == '')
			{
				$errors[] = get_aa_error('video_screenshot_activate');
			}
		}
	}

	if (!is_array($errors))
	{
		if (isset($_POST['save_and_edit']) || isset($_POST['delete_and_edit']))
		{
			$where = '';

			$search_fields = array();
			$search_fields[] = array('id' => 'video_id',     'title' => $lang['videos']['video_field_id']);
			$search_fields[] = array('id' => 'title',        'title' => $lang['videos']['video_field_title']);
			$search_fields[] = array('id' => 'dir',          'title' => $lang['videos']['video_field_directory']);
			$search_fields[] = array('id' => 'description',  'title' => $lang['videos']['video_field_description']);
			$search_fields[] = array('id' => 'website_link', 'title' => $lang['videos']['video_field_website_link']);
			$search_fields[] = array('id' => 'file_url',     'title' => $lang['videos']['video_field_video_url']);
			$search_fields[] = array('id' => 'embed',        'title' => $lang['videos']['video_field_embed_code']);
			$search_fields[] = array('id' => 'gallery_url',  'title' => $lang['videos']['video_field_gallery_url']);
			$search_fields[] = array('id' => 'pseudo_url',   'title' => $lang['videos']['video_field_pseudo_url']);
			$search_fields[] = array('id' => 'custom',       'title' => $lang['common']['dg_filter_search_in_custom']);
			$search_fields[] = array('id' => 'translations', 'title' => $lang['common']['dg_filter_search_in_translations']);

			if ($_SESSION['save']['videos.php']['se_text'] != '')
			{
				$q = sql_escape(str_replace('_', '\_', str_replace('%', '\%', $_SESSION['save']['videos.php']['se_text'])));
				$where_search = '1=0';
				foreach ($search_fields as $search_field)
				{
					if (isset($_GET["se_text_$search_field[id]"]))
					{
						$_SESSION['save']['videos.php']["se_text_$search_field[id]"] = $_GET["se_text_$search_field[id]"];
					}
					if (intval($_SESSION['save']['videos.php']["se_text_$search_field[id]"]) == 1)
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
						} elseif ($search_field['id'] == 'website_link')
						{
							if (is_url($q))
							{
								$search_id = 0;
								$search_dir = '';

								$pattern_check = str_replace(array('%DIR%', '%ID%'), array('(.*)', '([0-9]+)'), $website_ui_data['WEBSITE_LINK_PATTERN']);
								preg_match("|$pattern_check|is", $q, $temp);
								if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') !== false)
								{
									if (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') === false)
									{
										$search_id = intval($temp[1]);
									} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%ID%') > strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%'))
									{
										$search_id = intval($temp[2]);
									} else
									{
										$search_id = intval($temp[1]);
									}
								} elseif (strpos($website_ui_data['WEBSITE_LINK_PATTERN'], '%DIR%') !== false)
								{
									$search_dir = trim($temp[1]);
								}
								if ($search_id > 0)
								{
									$where_search .= " or $table_name.$table_key_name='$search_id'";
								} elseif ($search_dir != '')
								{
									$where_search .= " or $table_name.dir='$search_dir'";
								}
							}
						} elseif ($search_field['id'] == 'custom')
						{
							for ($i = 1; $i <= 3; $i++)
							{
								if ($options["ENABLE_VIDEO_FIELD_{$i}"] == 1)
								{
									$where_search .= " or $table_name.custom{$i} like '%$q%'";
								}
							}
						} elseif ($search_field['id'] == 'translations')
						{
							foreach ($languages as $language)
							{
								if (intval($_SESSION['save']['videos.php']["se_text_title"]) == 1)
								{
									$where_search .= " or $table_name.title_{$language['code']} like '%$q%'";
								}
								if (intval($_SESSION['save']['videos.php']["se_text_description"]) == 1)
								{
									$where_search .= " or $table_name.description_{$language['code']} like '%$q%'";
								}
								if (intval($_SESSION['save']['videos.php']["se_text_dir"]) == 1)
								{
									$where_search .= " or $table_name.dir_{$language['code']} like '%$q%'";
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

			if ($_SESSION['save']['videos.php']['se_ids'] != '')
			{
				$search_ids_array = array_map('intval', array_map('trim', explode(',', $_SESSION['save']['videos.php']['se_ids'])));
				$where .= "and $table_name.$table_key_name in (" . implode(',', $search_ids_array) . ")";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_user'] != '')
			{
				$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $_SESSION['save']['videos.php']['se_user']));
				if ($user_id == 0)
				{
					$where .= " and 0=1";
				} else
				{
					$where .= " and $table_name.user_id=$user_id";
				}
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_content_source'] != '')
			{
				$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $_SESSION['save']['videos.php']['se_content_source']));
				if ($content_source_id == 0)
				{
					$where .= " and 0=1";
				} else
				{
					$where .= " and $table_name.content_source_id=$content_source_id";
				}
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_dvd'] != '')
			{
				$dvd_id = mr2number(sql_pr("select dvd_id from $config[tables_prefix]dvds where title=?", $_SESSION['save']['videos.php']['se_dvd']));
				if ($dvd_id == 0)
				{
					$where .= " and 0=1";
				} else
				{
					$where .= " and $table_name.dvd_id=$dvd_id";
				}
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_tag'] != '')
			{
				$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?", $_SESSION['save']['videos.php']['se_tag']));
				$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name and tag_id=$tag_id)";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_category'] != '')
			{
				$category_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $_SESSION['save']['videos.php']['se_category']));
				$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name and category_id=$category_id)";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_model'] != '')
			{
				$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $_SESSION['save']['videos.php']['se_model']));
				$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name and model_id=$model_id)";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_is_private'] == '0')
			{
				$where .= " and $table_name.is_private=0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_is_private'] == '1')
			{
				$where .= " and $table_name.is_private=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_is_private'] == '2')
			{
				$where .= " and $table_name.is_private=2";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_access_level_id'] == '0')
			{
				$where .= " and $table_name.access_level_id=0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_access_level_id'] == '1')
			{
				$where .= " and $table_name.access_level_id=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_access_level_id'] == '2')
			{
				$where .= " and $table_name.access_level_id=2";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_access_level_id'] == '3')
			{
				$where .= " and $table_name.access_level_id=3";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_status_id'] == '0')
			{
				$where .= " and $table_name.status_id=0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_status_id'] == '1')
			{
				$where .= " and $table_name.status_id=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_status_id'] == '2')
			{
				$where .= " and $table_name.status_id=2";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_status_id'] == '3')
			{
				$where .= " and $table_name.status_id=3";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_status_id'] == '4')
			{
				$where .= " and $table_name.status_id=4";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_status_id'] == '5')
			{
				$where .= " and $table_name.status_id=5";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_load_type_id'] == '0')
			{
				$where .= " and $table_name.load_type_id=0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_load_type_id'] == '1')
			{
				$where .= " and $table_name.load_type_id=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_load_type_id'] == '2')
			{
				$where .= " and $table_name.load_type_id=2";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_load_type_id'] == '3')
			{
				$where .= " and $table_name.load_type_id=3";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_load_type_id'] == '5')
			{
				$where .= " and $table_name.load_type_id=5";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_review_flag'] == '1')
			{
				$where .= " and $table_name.is_review_needed=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_review_flag'] == '2')
			{
				$where .= " and $table_name.is_review_needed=0";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_admin_user_id'] > 0)
			{
				$where .= " and $table_name.admin_user_id=" . intval($_SESSION['save']['videos.php']['se_admin_user_id']);
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_is_locked'] == '1')
			{
				$where .= " and $table_name.is_locked=1";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_has_errors'] == '1')
			{
				$where .= " and $table_name.has_errors=1";
				$table_filtered = 1;
			}

			switch ($_SESSION['save']['videos.php']['se_field'])
			{
				case 'empty/title':
				case 'empty/description':
				case 'empty/gallery_url':
				case 'empty/custom1':
				case 'empty/custom2':
				case 'empty/custom3':
					$where .= " and $table_name." . substr($_SESSION['save']['videos.php']['se_field'], 6) . "=''";
					$table_filtered = 1;
					break;
				case 'empty/content_source':
					$where .= " and $table_name.content_source_id=0";
					$table_filtered = 1;
					break;
				case 'empty/dvd':
					$where .= " and $table_name.dvd_id=0";
					$table_filtered = 1;
					break;
				case 'empty/admin':
					$where .= " and $table_name.admin_user_id=0";
					$table_filtered = 1;
					break;
				case 'empty/admin_flag':
					$where .= " and $table_name.admin_flag_id=0";
					$table_filtered = 1;
					break;
				case 'empty/tokens_required':
					$where .= " and $table_name.tokens_required=0";
					$table_filtered = 1;
					break;
				case 'empty/video_viewed':
					$where .= " and $table_name.video_viewed=0";
					$table_filtered = 1;
					break;
				case 'empty/video_viewed_unique':
					$where .= " and $table_name.video_viewed_unique=0";
					$table_filtered = 1;
					break;
				case 'empty/comments':
					$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)=0";
					$table_filtered = 1;
					break;
				case 'empty/favourites':
					$where .= " and favourites_count=0";
					$table_filtered = 1;
					break;
				case 'empty/purchases':
					$where .= " and purchases_count=0";
					$table_filtered = 1;
					break;
				case 'empty/rating':
					$where .= " and ($table_name.rating=0 and $table_name.rating_amount=1)";
					$table_filtered = 1;
					break;
				case 'empty/tags':
					$where .= " and not exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
				case 'empty/categories':
					$where .= " and not exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
				case 'empty/models':
					$where .= " and not exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
				case 'filled/title':
				case 'filled/description':
				case 'filled/gallery_url':
				case 'filled/custom1':
				case 'filled/custom2':
				case 'filled/custom3':
					$where .= " and $table_name." . substr($_SESSION['save']['videos.php']['se_field'], 7) . "!=''";
					$table_filtered = 1;
					break;
				case 'filled/content_source':
					$where .= " and $table_name.content_source_id>0";
					$table_filtered = 1;
					break;
				case 'filled/dvd':
					$where .= " and $table_name.dvd_id>0";
					$table_filtered = 1;
					break;
				case 'filled/admin':
					$where .= " and $table_name.admin_user_id>0";
					$table_filtered = 1;
					break;
				case 'filled/admin_flag':
					$where .= " and $table_name.admin_flag_id>0";
					$table_filtered = 1;
					break;
				case 'filled/tokens_required':
					$where .= " and $table_name.tokens_required>0";
					$table_filtered = 1;
					break;
				case 'filled/video_viewed':
					$where .= " and $table_name.video_viewed>0";
					$table_filtered = 1;
					break;
				case 'filled/video_viewed_unique':
					$where .= " and $table_name.video_viewed_unique>0";
					$table_filtered = 1;
					break;
				case 'filled/comments':
					$where .= " and (select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)>0";
					$table_filtered = 1;
					break;
				case 'filled/favourites':
					$where .= " and favourites_count>0";
					$table_filtered = 1;
					break;
				case 'filled/purchases':
					$where .= " and purchases_count>0";
					$table_filtered = 1;
					break;
				case 'filled/rating':
					$where .= " and ($table_name.rating>0 or $table_name.rating_amount>1)";
					$table_filtered = 1;
					break;
				case 'filled/tags':
					$where .= " and exists (select tag_id from $table_name_tags where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
				case 'filled/categories':
					$where .= " and exists (select category_id from $table_name_categories where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
				case 'filled/models':
					$where .= " and exists (select model_id from $table_name_models where $table_key_name=$table_name.$table_key_name)";
					$table_filtered = 1;
					break;
			}

			if ($_SESSION['save']['videos.php']['se_show_id'] == 15)
			{
				$where .= " and $table_name.admin_user_id>0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 16)
			{
				$where .= " and $table_name.admin_user_id=0 and $table_name.feed_id=0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 17)
			{
				$where .= " and $table_name.admin_user_id=0 and $table_name.feed_id=0 and $config[tables_prefix]users.status_id=6";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 18)
			{
				$where .= " and $table_name.feed_id>0";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 21)
			{
				$where .= " and $table_name.screen_main=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 22)
			{
				$where .= " and $table_name.screen_main!=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 23)
			{
				$where .= " and $table_name.rs_completed=1";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_show_id'] == 24)
			{
				$where .= " and $table_name.rs_completed!=1";
				$table_filtered = 1;
			} elseif (strlen($_SESSION['save']['videos.php']['se_show_id']) > 0)
			{
				if (strpos($_SESSION['save']['videos.php']['se_show_id'], 'wf/') === 0)
				{
					$postfix = sql_escape(str_replace('wf/', '', $_SESSION['save']['videos.php']['se_show_id']));
					$where .= " and $table_name.file_formats like concat('%||$postfix|%') and load_type_id=1";
					$table_filtered = 1;
				} elseif (strpos($_SESSION['save']['videos.php']['se_show_id'], 'wof/') === 0)
				{
					$postfix = sql_escape(str_replace('wof/', '', $_SESSION['save']['videos.php']['se_show_id']));
					$where .= " and $table_name.file_formats not like concat('%||$postfix|%') and load_type_id=1";
					$table_filtered = 1;
				} elseif (strpos($_SESSION['save']['videos.php']['se_show_id'], 'wq/') === 0)
				{
					$quality = intval(str_replace('wq/', '', $_SESSION['save']['videos.php']['se_show_id']));
					$where .= " and $table_name.file_dimensions!='' and substring($table_name.file_dimensions, position('x' in $table_name.file_dimensions) + 1)>=$quality";
					$table_filtered = 1;
				} elseif (strpos($_SESSION['save']['videos.php']['se_show_id'], 'woq/') === 0)
				{
					$quality = intval(str_replace('woq/', '', $_SESSION['save']['videos.php']['se_show_id']));
					$where .= " and $table_name.file_dimensions!='' and substring($table_name.file_dimensions, position('x' in $table_name.file_dimensions) + 1)<$quality";
					$table_filtered = 1;
				} elseif (strpos($_SESSION['save']['videos.php']['se_show_id'], 'wl/') === 0)
				{
					$lang_existing = array();
					foreach ($languages as $language)
					{
						if (str_replace('wl/', '', $_SESSION['save']['videos.php']['se_show_id']) == $language['code'])
						{
							$lang_existing = $language;
							break;
						}
					}
					if ($lang_existing <> '')
					{
						if ($lang_existing['translation_scope_videos'] == 0)
						{
							if ($lang_existing['is_directories_localize'] == 1)
							{
								$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
							} else
							{
								$where .= " and ($table_name.title_$lang_existing[code]!='' and ($table_name.description='' or $table_name.description_$lang_existing[code]!=''))";
							}
						} else
						{
							if ($lang_existing['is_directories_localize'] == 1)
							{
								$where .= " and ($table_name.title_$lang_existing[code]!='' and $table_name.dir_$lang_existing[code]!='')";
							} else
							{
								$where .= " and ($table_name.title_$lang_existing[code]!='')";
							}
						}
						$table_filtered = 1;
					} else
					{
						$_SESSION['save']['videos.php']['se_show_id'] = '';
					}
				} elseif (strpos($_SESSION['save']['videos.php']['se_show_id'], 'wol/') === 0)
				{
					$lang_missing = array();
					foreach ($languages as $language)
					{
						if (str_replace('wol/', '', $_SESSION['save']['videos.php']['se_show_id']) == $language['code'])
						{
							$lang_missing = $language;
							break;
						}
					}
					if ($lang_missing <> '')
					{
						if ($lang_missing['translation_scope_videos'] == 0)
						{
							if ($lang_missing['is_directories_localize'] == 1)
							{
								$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
							} else
							{
								$where .= " and ($table_name.title_$lang_missing[code]='' or ($table_name.description<>'' and $table_name.description_$lang_missing[code]=''))";
							}
						} else
						{
							if ($lang_missing['is_directories_localize'] == 1)
							{
								$where .= " and ($table_name.title_$lang_missing[code]='' or $table_name.dir_$lang_missing[code]='')";
							} else
							{
								$where .= " and ($table_name.title_$lang_missing[code]='')";
							}
						}
						$table_filtered = 1;
					} else
					{
						$_SESSION['save']['videos.php']['se_show_id'] = '';
					}
				}
			}

			if ($_SESSION['save']['videos.php']['se_feed_id'] > 0)
			{
				$where .= " and $table_name.feed_id=" . intval($_SESSION['save']['videos.php']['se_feed_id']);
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_storage_group_id'] > 0)
			{
				$where .= " and $table_name.server_group_id=" . intval($_SESSION['save']['videos.php']['se_storage_group_id']);
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_posted'] == "yes")
			{
				$where .= " and $database_selectors[where_videos]";
				$table_filtered = 1;
			} elseif ($_SESSION['save']['videos.php']['se_posted'] == "no")
			{
				$where .= " and not ($database_selectors[where_videos])";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_post_date_from'] <> "")
			{
				$where .= " and $table_name.post_date>='" . $_SESSION['save']['videos.php']['se_post_date_from'] . "'";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_post_date_to'] <> "")
			{
				$where .= " and $table_name.post_date<='" . date("Y-m-d H:i", strtotime($_SESSION['save']['videos.php']['se_post_date_to']) + 86399) . "'";
				$table_filtered = 1;
			}

			if ($_SESSION['save']['videos.php']['se_flag_id'] > 0)
			{
				$flag_amount = max(1, intval($_SESSION['save']['videos.php']['se_flag_values_amount']));
				$where .= " and ($table_name.admin_flag_id=" . intval($_SESSION['save']['videos.php']['se_flag_id']) . " or (select sum(votes) from $config[tables_prefix]flags_videos where video_id=$table_name.video_id and flag_id=" . intval($_SESSION['save']['videos.php']['se_flag_id']) . ")>=$flag_amount)";
				$table_filtered = 1;
			}

			if ($_SESSION['userdata']['is_access_to_own_content'] == 1)
			{
				$admin_id = intval($_SESSION['userdata']['user_id']);
				$where .= " and $table_name.admin_user_id=$admin_id ";
			}
			if ($_SESSION['userdata']['is_access_to_disabled_content'] == 1)
			{
				$where .= " and $table_name.status_id=0 ";
			}
			if ($_SESSION['userdata']['is_access_to_content_flagged_with'] > 0)
			{
				$flags_access_limit = implode(',', array_map('intval', explode(',', $_SESSION['userdata']['is_access_to_content_flagged_with'])));
				$where .= " and $table_name.admin_flag_id>0 and $table_name.admin_flag_id in ($flags_access_limit)";
			}
			if ($where != '')
			{
				$where = " where " . substr($where, 4);
			}

			if ($where <> '')
			{
				$where2 = " and ($table_name.status_id=0 or $table_name.status_id=1)";
			} else
			{
				$where2 = " where ($table_name.status_id=0 or $table_name.status_id=1)";
			}

			$sort_by = $_SESSION['save']['videos.php']['sort_by'];
			if ($config['relative_post_dates'] == 'true' && $sort_by == 'post_date')
			{
				$sort_by = "$table_name.post_date " . $_SESSION['save']['videos.php']['sort_direction'] . ", $table_name.relative_post_date";
			} elseif ($sort_by == 'rating')
			{
				$sort_by = "$table_name.rating/$table_name.rating_amount " . $_SESSION['save']['videos.php']['sort_direction'] . ", $table_name.rating_amount";
			} elseif ($sort_by == 'user')
			{
				$sort_by = "$config[tables_prefix]users.username";
				$table_projector .= " left join $config[tables_prefix]users on $config[tables_prefix]users.user_id=$table_name.user_id";
			} elseif ($sort_by == 'admin_user')
			{
				$sort_by = "$config[tables_prefix]admin_users.login";
				$table_projector .= " left join $config[tables_prefix]admin_users on $config[tables_prefix]admin_users.user_id=$table_name.admin_user_id";
			} elseif ($sort_by == 'content_source')
			{
				$sort_by = "$config[tables_prefix]content_sources.title";
				$table_projector .= " left join $config[tables_prefix]content_sources on $config[tables_prefix]content_sources.content_source_id=$table_name.content_source_id";
			} elseif ($sort_by == 'dvd')
			{
				$sort_by = "$config[tables_prefix]dvds.title";
				$table_projector .= " left join $config[tables_prefix]dvds on $config[tables_prefix]dvds.dvd_id=$table_name.dvd_id";
			} elseif ($sort_by == 'admin_flag')
			{
				$sort_by = "$config[tables_prefix]flags.title";
				$table_projector .= " left join $config[tables_prefix]flags on $config[tables_prefix]flags.flag_id=$table_name.admin_flag_id";
			} elseif ($sort_by == 'server_group')
			{
				$sort_by = "$config[tables_prefix]admin_servers_groups.title";
				$table_projector .= " left join $config[tables_prefix]admin_servers_groups on $config[tables_prefix]admin_servers_groups.group_id=$table_name.server_group_id";
			} elseif ($sort_by == 'comments_count')
			{
				$sort_by = "(select count(*) from $config[tables_prefix]comments where object_id=$table_name.$table_key_name and object_type_id=1)";
			} else
			{
				$sort_by = "$table_name.$sort_by";
			}
			$sort_by .= ' ' . $_SESSION['save']['videos.php']['sort_direction'];

			$data_temp = mr2array_list(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by, $table_name.$table_key_name"));

			$next_item_id = intval($data_temp[@array_search($item_id, $data_temp) + 1]);
			if ($next_item_id == 0)
			{
				$next_item_id = mr2number(sql("select $table_name.$table_key_name from $table_projector $where $where2 order by $sort_by limit 1"));
			}
			if ($next_item_id == $item_id)
			{
				$next_item_id = 0;
			}

			if (isset($_POST['delete_and_edit']))
			{
				if (in_array('videos|delete', $_SESSION['permissions']))
				{
					sql("update $table_name set status_id=4 where $table_key_name=$item_id");
					sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=180, object_id=?, object_type_id=1, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $item_id, date("Y-m-d H:i:s"));
					sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=2, video_id=?, data=?, added_date=?", $item_id, serialize(array()), date("Y-m-d H:i:s"));
					$_SESSION['messages'][] = $lang['common']['success_message_removed_object'];

					if ($next_item_id == 0)
					{
						return_ajax_success($page_name, 1);
					} else
					{
						return_ajax_success($page_name . "?item_id=$next_item_id", 1);
					}
				} else
				{
					header("Location: error.php?error=permission_denied");
					die;
				}
			}
		}

		$video_id = $data_video['video_id'];
		$dir_path = get_dir_by_id($video_id);

		if ($group_id == 1)
		{
			$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/screenshots";
			$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id";

			$custom_crop_options = '';
			if (intval($options['SCREENSHOTS_CROP_CUSTOMIZE']) > 0 && $data_video['content_source_id'] > 0)
			{
				$data_content_source = mr2array_single(sql_pr("select * from $config[tables_prefix]content_sources where content_source_id=?", $data_video['content_source_id']));
				$custom_crop_options = $data_content_source["custom{$options['SCREENSHOTS_CROP_CUSTOMIZE']}"];
			}

			$screen_amount = $data_video['screen_amount'];
			$main = intval($_POST['main']);

			$rotator_data_changed = 0;
			if (is_file("$screen_source_dir/rotator.dat"))
			{
				$rotator_data = @unserialize(file_get_contents("$screen_source_dir/rotator.dat"));
			}

			$screenshots_changed = 0;
			$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];

			log_video("", $video_id);
			log_video("INFO  Saving overview screenshots in admin panel", $video_id);

			$list_formats_overview = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=1"));
			if ($_POST['replace_screenshots_hash'])
			{
				log_video("INFO  Replacing all screenshots", $video_id);

				if (!rmdir_recursive($screen_source_dir))
				{
					log_video("ERROR Failed to delete directory $screen_source_dir", $video_id);
					log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}
				if (!mkdir_recursive($screen_source_dir))
				{
					log_video("ERROR Failed to create directory $screen_source_dir", $video_id);
					log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}
				if (!mkdir_recursive($screen_target_dir))
				{
					log_video("ERROR Failed to create directory $screen_target_dir", $video_id);
					log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}

				$counter = 0;
				if (is_dir("$config[temporary_path]/$_POST[replace_screenshots_hash]"))
				{
					$data = get_contents_from_dir("$config[temporary_path]/$_POST[replace_screenshots_hash]", 1);
					sort($data);
					foreach ($data as $v)
					{
						$counter++;
						rename("$config[temporary_path]/$_POST[replace_screenshots_hash]/$v", "$screen_source_dir/$counter.jpg");
					}
					rmdir_recursive("$config[temporary_path]/$_POST[replace_screenshots_hash]");
				} else
				{
					$zip = new PclZip("$config[temporary_path]/$_POST[replace_screenshots_hash].tmp");
					if ($zip->properties()['status'] == 'ok')
					{
						$data = process_zip_images($zip->listContent());
						foreach ($data as $k => $v)
						{
							$counter++;
							$file_base_name = $v['filename'];
							$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
							$fstream = $content[0]['content'];
							$fp = fopen("$screen_source_dir/$counter.jpg", "w");
							fwrite($fp, $fstream);
							fclose($fp);
						}
					} else
					{
						$counter = 1;
						rename("$config[temporary_path]/$_POST[replace_screenshots_hash].tmp", "$screen_source_dir/$counter.jpg");
					}
				}
				for ($i = 1; $i <= $counter; $i++)
				{
					$exec_res = process_screen_source("$screen_source_dir/$i.jpg", $options, true, $custom_crop_options);
					if ($exec_res)
					{
						log_video("ERROR IM operation failed: $exec_res", $video_id);
						log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_format_error_source');
						return_ajax_errors($errors);
					}

					$screenshots_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$screen_source_dir/$i.jpg")];
				}
				log_video("INFO  Total screenshots uploaded: $counter", $video_id);

				foreach ($list_formats_overview as $format)
				{
					log_video("INFO  Creating screenshots for \"$format[title]\" format", $video_id);

					if (!rmdir_recursive("$screen_target_dir/$format[size]"))
					{
						log_video("ERROR Failed to delete directory $screen_target_dir/$format[size]", $video_id);
						log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
						return_ajax_errors($errors);
					}
					if (!mkdir_recursive("$screen_target_dir/$format[size]"))
					{
						log_video("ERROR Failed to create directory $screen_target_dir/$format[size]", $video_id);
						log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
						return_ajax_errors($errors);
					}

					for ($i = 1; $i <= $counter; $i++)
					{
						$exec_res = make_screen_from_source("$screen_source_dir/$i.jpg", "$screen_target_dir/$format[size]/$i.jpg", $format, $options, true);
						if ($exec_res)
						{
							log_video("ERROR IM operation failed: $exec_res", $video_id);
							log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
							return_ajax_errors($errors);
						}
					}
				}

				$screen_amount = $counter;
				$main = 1;

				$video_formats = get_video_formats($video_id, $data_video['file_formats']);
				copy("$screen_source_dir/$main.jpg", "$screen_target_dir/preview.jpg");
				foreach ($video_formats as $format)
				{
					resize_image('need_size_no_composite', "$screen_target_dir/preview.jpg", "$screen_target_dir/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
				}
				if (isset($rotator_data))
				{
					$rotator_data = [];
					$rotator_data_changed = 1;
				}
				$screenshots_changed = 1;
			} else
			{
				for ($i = 1; $i <= $data_video['screen_amount']; $i++)
				{
					if (in_array($i, $delete_pos))
					{
						if ($main == $i)
						{
							$main = 1;
						}
						@unlink("$screen_source_dir/$i.jpg");
						foreach ($list_formats_overview as $format)
						{
							@unlink("$screen_target_dir/$format[size]/$i.jpg");
						}
						if (isset($rotator_data[$i]))
						{
							unset($rotator_data[$i]);
							$rotator_data_changed = 1;
						}
						$screen_amount--;

						$screenshots_changed = 1;
						if (isset($screenshots_data[$i]))
						{
							unset($screenshots_data[$i]);
						}
					} elseif ($_POST["file_$i"])
					{
						log_video("INFO  Replacing screenshot #{$i}", $video_id);

						if (!mkdir_recursive($screen_source_dir))
						{
							log_video("ERROR Failed to create directory $screen_source_dir", $video_id);
							log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}
						if (!mkdir_recursive($screen_target_dir))
						{
							log_video("ERROR Failed to create directory $screen_target_dir", $video_id);
							log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}

						if (!transfer_uploaded_file("file_$i", "$screen_source_dir/$i.jpg"))
						{
							log_video("ERROR Failed to replace file $screen_source_dir/$i.jpg", $video_id);
							log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}

						$exec_res = process_screen_source("$screen_source_dir/$i.jpg", $options, true, $custom_crop_options);
						if ($exec_res)
						{
							log_video("ERROR IM operation failed: $exec_res", $video_id);
							log_video("ERROR Error during screenshots creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_source');
							return_ajax_errors($errors);
						}

						foreach ($list_formats_overview as $format)
						{
							log_video("INFO  Creating screenshots for \"$format[title]\" format", $video_id);
							if (!mkdir_recursive("$screen_target_dir/$format[size]"))
							{
								log_video("ERROR Failed to create directory $screen_target_dir/$format[size]", $video_id);
								log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
								$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
								return_ajax_errors($errors);
							}

							$exec_res = make_screen_from_source("$screen_source_dir/$i.jpg", "$screen_target_dir/$format[size]/$i.jpg", $format, $options, true);
							if ($exec_res)
							{
								log_video("ERROR IM operation failed: $exec_res", $video_id);
								log_video("ERROR Error during screenshots creation for \"$format[title]\" format, stopping further processing", $video_id);
								$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
								return_ajax_errors($errors);
							}
						}
						if (isset($rotator_data[$i]))
						{
							unset($rotator_data[$i]);
							$rotator_data_changed = 1;
						}

						$screenshots_changed = 1;
						$screenshots_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$screen_source_dir/$i.jpg")];
					}
				}

				if (count($delete_pos) > 0)
				{
					$cnt = count($delete_pos);
					log_video("INFO  Removing $cnt screenshots (#" . implode(", #", $delete_pos) . ")", $video_id);
					$last_index = 0;
					for ($i = 1; $i <= $data_video['screen_amount']; $i++)
					{
						if (is_file("$screen_source_dir/$i.jpg"))
						{
							if ($last_index == $i - 1)
							{
								$last_index++;
							} else
							{
								$last_index++;
								if ($i == $main)
								{
									$main = $last_index;
								}
								if (!rename("$screen_source_dir/$i.jpg", "$screen_source_dir/$last_index.jpg"))
								{
									log_video("ERROR Failed to replace file $screen_source_dir/$last_index.jpg", $video_id);
									log_video("ERROR Error during screenshots deletion, stopping further processing", $video_id);
									$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
									return_ajax_errors($errors);
								}
								foreach ($list_formats_overview as $format)
								{
									if (!rename("$screen_target_dir/$format[size]/$i.jpg", "$screen_target_dir/$format[size]/$last_index.jpg"))
									{
										log_video("ERROR Failed to replace file $screen_target_dir/$format[size]/$last_index.jpg", $video_id);
										log_video("ERROR Error during screenshots deletion, stopping further processing", $video_id);
										$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
										return_ajax_errors($errors);
									}
								}
								if (isset($rotator_data[$i]))
								{
									$rotator_data[$last_index] = $rotator_data[$i];
									unset($rotator_data[$i]);
									$rotator_data_changed = 1;
								}
								if (isset($screenshots_data[$i]))
								{
									$screenshots_data[$last_index] = $screenshots_data[$i];
									unset($screenshots_data[$i]);
								}
							}
						}
					}
				}
				if ($data_video['screen_main'] != $main)
				{
					log_video("INFO  Changing main screenshot from #{$data_video['screen_main']} to #$main", $video_id);
				}
				if ($data_video['screen_main'] != $main || in_array($data_video['screen_main'], $delete_pos) || $_POST["file_$main"])
				{
					$video_formats = get_video_formats($video_id, $data_video['file_formats']);
					copy("$screen_source_dir/$main.jpg", "$screen_target_dir/preview.jpg");
					foreach ($video_formats as $format)
					{
						resize_image('need_size_no_composite', "$screen_target_dir/preview.jpg", "$screen_target_dir/preview{$format['postfix']}.jpg", $format['dimensions'][0] . 'x' . $format['dimensions'][1]);
					}
				}
			}
			if (isset($rotator_data) && $rotator_data_changed == 1)
			{
				file_put_contents("$screen_source_dir/rotator.dat", serialize($rotator_data), LOCK_EX);
			}
			if ($screenshots_changed == 1)
			{
				file_put_contents("$screen_source_dir/info.dat", serialize($screenshots_data), LOCK_EX);
				foreach ($list_formats_overview as $format)
				{
					if ($format['is_create_zip'] == 1)
					{
						log_video("INFO  Replacing screenshots ZIP for \"$format[title]\" format", $video_id);
						$source_folder = "$screen_target_dir/$format[size]";
						@unlink("$source_folder/$video_id-$format[size].zip");

						$zip_files_to_add = [];
						for ($i = 1; $i <= $screen_amount; $i++)
						{
							$zip_files_to_add[] = "$source_folder/$i.jpg";
						}
						$zip = new PclZip("$source_folder/$video_id-$format[size].zip");
						$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
					}
				}
			}
			log_video("INFO  Done screenshots changes", $video_id);

			$update_array = [];
			$update_array['screen_amount'] = $screen_amount;
			$update_array['screen_main'] = $main;
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_status', $_SESSION['permissions']))
			{
				$update_array['status_id'] = intval($_POST['status_id']);
			}
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_admin_flag', $_SESSION['permissions']))
			{
				$update_array['admin_flag_id'] = intval($_POST['admin_flag_id']);
			}

			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $data_video['video_id']);

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($data_video[$k] != $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, -2);
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=151, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $data_video['video_id'], $update_details, date("Y-m-d H:i:s"));
		} elseif ($group_id == 2)
		{
			log_video("", $video_id);
			log_video("INFO  Saving timeline screenshots in admin panel", $video_id);

			$video_formats = get_video_formats($video_id, $data_video['file_formats']);
			foreach ($list_formats_videos_timelined as $timeline_format)
			{
				if ($timeline_format['format_video_id'] == intval($_POST['timeline_video_format_id']))
				{
					$timeline_titles = [];
					$timeline_pos = 1;
					for ($i = 1; $i <= $timeline_format['timeline_screen_amount']; $i++)
					{
						if (isset($_POST["title_$i"]) && trim($_POST["title_$i"]) != '')
						{
							$timeline_titles[$i] = array('time' => $timeline_pos, 'text' => trim($_POST["title_$i"]));
						}
						$timeline_pos = $i * $timeline_format['timeline_screen_interval'];
					}
					if (count($timeline_titles) > 0)
					{
						log_video("INFO  Specified " . count($timeline_titles) . " cuepoints", $video_id);
						file_put_contents("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_format[timeline_directory]/cuepoints.json", json_encode($timeline_titles), LOCK_EX);
					} elseif (is_file("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_format[timeline_directory]/cuepoints.json"))
					{
						log_video("INFO  Deleted cuepoints", $video_id);
						unlink("$config[content_path_videos_screenshots]/$dir_path/$video_id/timelines/$timeline_format[timeline_directory]/cuepoints.json");
					}

					$video_formats[$timeline_format['postfix']]['timeline_cuepoints'] = count($timeline_titles);
					break;
				}
			}

			log_video("INFO  Done screenshots changes", $video_id);

			$update_array = [];
			$update_array['file_formats'] = pack_video_formats($video_formats);
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_status', $_SESSION['permissions']))
			{
				$update_array['status_id'] = intval($_POST['status_id']);
			}
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_admin_flag', $_SESSION['permissions']))
			{
				$update_array['admin_flag_id'] = intval($_POST['admin_flag_id']);
			}

			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $data_video['video_id']);

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($data_video[$k] != $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, -2);
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=151, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $data_video['video_id'], $update_details, date("Y-m-d H:i:s"));
		} elseif ($group_id == 3)
		{
			$screen_source_dir = "$config[content_path_videos_sources]/$dir_path/$video_id/posters";
			$screen_target_dir = "$config[content_path_videos_screenshots]/$dir_path/$video_id/posters";

			$screen_amount = $data_video['poster_amount'];
			$main = intval($_POST['main']);

			$screenshots_changed = 0;
			$screenshots_data = @unserialize(file_get_contents("$screen_source_dir/info.dat")) ?: [];

			log_video("", $video_id);
			log_video("INFO  Saving posters in admin panel", $video_id);

			$list_formats_posters = mr2array(sql("select * from $config[tables_prefix]formats_screenshots where status_id in (0,1) and group_id=3"));
			if ($_POST['replace_screenshots_hash'])
			{
				log_video("INFO  Replacing all posters", $video_id);

				if (!rmdir_recursive($screen_source_dir))
				{
					log_video("ERROR Failed to delete directory $screen_source_dir", $video_id);
					log_video("ERROR Error during posters creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}
				if (!mkdir_recursive($screen_source_dir))
				{
					log_video("ERROR Failed to create directory $screen_source_dir", $video_id);
					log_video("ERROR Error during posters creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}
				if (!mkdir_recursive($screen_target_dir))
				{
					log_video("ERROR Failed to create directory $screen_target_dir", $video_id);
					log_video("ERROR Error during posters creation, stopping further processing", $video_id);
					$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
					return_ajax_errors($errors);
				}

				$counter = 0;
				if (is_dir("$config[temporary_path]/$_POST[replace_screenshots_hash]"))
				{
					$data = get_contents_from_dir("$config[temporary_path]/$_POST[replace_screenshots_hash]", 1);
					sort($data);
					foreach ($data as $v)
					{
						$counter++;
						rename("$config[temporary_path]/$_POST[replace_screenshots_hash]/$v", "$screen_source_dir/$counter.jpg");
					}
					rmdir_recursive("$config[temporary_path]/$_POST[replace_screenshots_hash]");
				} else
				{
					$zip = new PclZip("$config[temporary_path]/$_POST[replace_screenshots_hash].tmp");
					if ($zip->properties()['status'] == 'ok')
					{
						$data = process_zip_images($zip->listContent());
						foreach ($data as $k => $v)
						{
							$counter++;
							$file_base_name = $v['filename'];
							$content = $zip->extract(PCLZIP_OPT_BY_NAME, $file_base_name, PCLZIP_OPT_EXTRACT_AS_STRING);
							$fstream = $content[0]['content'];
							$fp = fopen("$screen_source_dir/$counter.jpg", "w");
							fwrite($fp, $fstream);
							fclose($fp);
						}
					} else
					{
						$counter = 1;
						rename("$config[temporary_path]/$_POST[replace_screenshots_hash].tmp", "$screen_source_dir/$counter.jpg");
					}
				}
				for ($i = 1; $i <= $counter; $i++)
				{
					$screenshots_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$screen_source_dir/$i.jpg")];
				}
				log_video("INFO  Total posters uploaded: $counter", $video_id);

				foreach ($list_formats_posters as $format)
				{
					log_video("INFO  Creating posters for \"$format[title]\" format", $video_id);

					if (!rmdir_recursive("$screen_target_dir/$format[size]"))
					{
						log_video("ERROR Failed to delete directory $screen_target_dir/$format[size]", $video_id);
						log_video("ERROR Error during posters creation for \"$format[title]\" format, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
						return_ajax_errors($errors);
					}
					if (!mkdir_recursive("$screen_target_dir/$format[size]"))
					{
						log_video("ERROR Failed to create directory $screen_target_dir/$format[size]", $video_id);
						log_video("ERROR Error during posters creation for \"$format[title]\" format, stopping further processing", $video_id);
						$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
						return_ajax_errors($errors);
					}

					for ($i = 1; $i <= $counter; $i++)
					{
						$exec_res = make_screen_from_source("$screen_source_dir/$i.jpg", "$screen_target_dir/$format[size]/$i.jpg", $format, $options, false);
						if ($exec_res)
						{
							log_video("ERROR IM operation failed: $exec_res", $video_id);
							log_video("ERROR Error during posters creation for \"$format[title]\" format, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
							return_ajax_errors($errors);
						}
					}
				}

				$screen_amount = $counter;
				$main = 1;

				$screenshots_changed = 1;
			} else
			{
				for ($i = 1; $i <= $data_video['poster_amount']; $i++)
				{
					if (in_array($i, $delete_pos))
					{
						if ($main == $i)
						{
							$main = 1;
						}
						@unlink("$screen_source_dir/$i.jpg");
						foreach ($list_formats_posters as $format)
						{
							@unlink("$screen_target_dir/$format[size]/$i.jpg");
						}
						$screen_amount--;

						$screenshots_changed = 1;
						if (isset($screenshots_data[$i]))
						{
							unset($screenshots_data[$i]);
						}
					} elseif ($_POST["file_$i"])
					{
						log_video("INFO  Replacing poster #{$i}", $video_id);

						if (!mkdir_recursive($screen_source_dir))
						{
							log_video("ERROR Failed to create directory $screen_source_dir", $video_id);
							log_video("ERROR Error during posters creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}
						if (!mkdir_recursive($screen_target_dir))
						{
							log_video("ERROR Failed to create directory $screen_target_dir", $video_id);
							log_video("ERROR Error during posters creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}

						if (!transfer_uploaded_file("file_$i", "$screen_source_dir/$i.jpg"))
						{
							log_video("ERROR Failed to replace file $screen_source_dir/$i.jpg", $video_id);
							log_video("ERROR Error during posters creation, stopping further processing", $video_id);
							$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
							return_ajax_errors($errors);
						}

						foreach ($list_formats_posters as $format)
						{
							log_video("INFO  Creating posters for \"$format[title]\" format", $video_id);
							if (!mkdir_recursive("$screen_target_dir/$format[size]"))
							{
								log_video("ERROR Failed to create directory $screen_target_dir/$format[size]", $video_id);
								log_video("ERROR Error during posters creation for \"$format[title]\" format, stopping further processing", $video_id);
								$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
								return_ajax_errors($errors);
							}

							$exec_res = make_screen_from_source("$screen_source_dir/$i.jpg", "$screen_target_dir/$format[size]/$i.jpg", $format, $options, false);
							if ($exec_res)
							{
								log_video("ERROR IM operation failed: $exec_res", $video_id);
								log_video("ERROR Error during posters creation for \"$format[title]\" format, stopping further processing", $video_id);
								$errors[] = get_aa_error('video_screenshot_format_error_format', $format['title']);
								return_ajax_errors($errors);
							}
						}

						$screenshots_changed = 1;
						$screenshots_data[$i] = ['type' => 'uploaded', 'filesize' => filesize("$screen_source_dir/$i.jpg")];
					}
				}

				if (count($delete_pos) > 0)
				{
					$cnt = count($delete_pos);
					log_video("INFO  Removing $cnt posters (#" . implode(", #", $delete_pos) . ")", $video_id);
					$last_index = 0;
					for ($i = 1; $i <= $data_video['poster_amount']; $i++)
					{
						if (is_file("$screen_source_dir/$i.jpg"))
						{
							if ($last_index == $i - 1)
							{
								$last_index++;
							} else
							{
								$last_index++;
								if ($i == $main)
								{
									$main = $last_index;
								}
								if (!rename("$screen_source_dir/$i.jpg", "$screen_source_dir/$last_index.jpg"))
								{
									log_video("ERROR Failed to replace file $screen_source_dir/$last_index.jpg", $video_id);
									log_video("ERROR Error during posters deletion, stopping further processing", $video_id);
									$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
									return_ajax_errors($errors);
								}
								foreach ($list_formats_posters as $format)
								{
									if (!rename("$screen_target_dir/$format[size]/$i.jpg", "$screen_target_dir/$format[size]/$last_index.jpg"))
									{
										log_video("ERROR Failed to replace file $screen_target_dir/$format[size]/$last_index.jpg", $video_id);
										log_video("ERROR Error during posters deletion, stopping further processing", $video_id);
										$errors[] = get_aa_error('video_screenshot_format_error_filesystem');
										return_ajax_errors($errors);
									}
								}
								if (isset($screenshots_data[$i]))
								{
									$screenshots_data[$last_index] = $screenshots_data[$i];
									unset($screenshots_data[$i]);
								}
							}
						}
					}
				}
				if ($screen_amount == 0)
				{
					$main = 0;
					log_video("INFO  Deleting all posters", $video_id);
				} elseif ($data_video['poster_main'] != $main)
				{
					log_video("INFO  Changing main poster from #{$data_video['poster_main']} to #$main", $video_id);
				}
			}
			if ($screenshots_changed == 1)
			{
				if ($screen_amount == 0)
				{
					foreach ($list_formats_posters as $format)
					{
						rmdir_recursive("$screen_target_dir/$format[size]");
					}
					rmdir_recursive("$screen_target_dir");
					rmdir_recursive("$screen_source_dir");
				} else
				{
					file_put_contents("$screen_source_dir/info.dat", serialize($screenshots_data), LOCK_EX);
					foreach ($list_formats_posters as $format)
					{
						if ($format['is_create_zip'] == 1)
						{
							log_video("INFO  Replacing posters ZIP for \"$format[title]\" format", $video_id);
							$source_folder = "$screen_target_dir/$format[size]";
							@unlink("$source_folder/$video_id-$format[size].zip");

							$zip_files_to_add = [];
							for ($i = 1; $i <= $screen_amount; $i++)
							{
								$zip_files_to_add[] = "$source_folder/$i.jpg";
							}
							$zip = new PclZip("$source_folder/$video_id-$format[size].zip");
							$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");
						}
					}
				}
			}
			log_video("INFO  Done posters changes", $video_id);

			$update_array = [];
			$update_array['poster_amount'] = $screen_amount;
			$update_array['poster_main'] = $main;
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_status', $_SESSION['permissions']))
			{
				$update_array['status_id'] = intval($_POST['status_id']);
			}
			if (in_array('videos|edit_all', $_SESSION['permissions']) || in_array('videos|edit_admin_flag', $_SESSION['permissions']))
			{
				$update_array['admin_flag_id'] = intval($_POST['admin_flag_id']);
			}

			sql_pr("update $table_name set ?% where $table_key_name=?", $update_array, $data_video['video_id']);

			$update_details = '';
			foreach ($update_array as $k => $v)
			{
				if ($data_video[$k] != $update_array[$k])
				{
					$update_details .= "$k, ";
				}
			}
			if (strlen($update_details) > 0)
			{
				$update_details = substr($update_details, 0, -2);
			}

			sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=151, object_id=?, object_type_id=1, action_details=?, added_date=?", $_SESSION['userdata']['user_id'], $_SESSION['userdata']['login'], $data_video['video_id'], $update_details, date("Y-m-d H:i:s"));
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
				return_ajax_success($page_name . "?item_id=$next_item_id", 1);
			}
		}
		return_ajax_success($page_name, 1);
	} else
	{
		return_ajax_errors($errors);
	}
}

$dir_path = get_dir_by_id($item_id);

$group_id = intval($_REQUEST['group_id']);
$overview_format_id = trim($_REQUEST['overview_format_id']);
$timeline_format_id = trim($_REQUEST['timeline_format_id']);
$timeline_video_format_id = intval($_REQUEST['timeline_video_format_id']);
$poster_format_id = trim($_REQUEST['poster_format_id']);

if ($group_id == 0)
{
	$group_id = $_SESSION['save'][$page_name]['group_id'] ?: 1;
}

if (!$overview_format_id)
{
	$overview_format_id = $_SESSION['save'][$page_name]['overview_format_id'];
}
if (!$timeline_format_id)
{
	$timeline_format_id = $_SESSION['save'][$page_name]['timeline_format_id'];
}
if (!$timeline_video_format_id)
{
	$timeline_video_format_id = $_SESSION['save'][$page_name]['timeline_video_format_id'];
}
if (!$poster_format_id)
{
	$poster_format_id = $_SESSION['save'][$page_name]['poster_format_id'];
}

if ($group_id == 1)
{
	$format_id = $overview_format_id;
	$list_formats_global = $list_formats_overview;
} elseif ($group_id == 2)
{
	$format_id = $timeline_format_id;
	$list_formats_global = $list_formats_timeline;
} elseif ($group_id == 3)
{
	$format_id = $poster_format_id;
	$list_formats_global = $list_formats_posters;
}

$data_format = [];
if ($format_id == '' || intval($format_id) > 0)
{
	$format_id = intval($format_id);
	if ($format_id == 0)
	{
		if ($_SESSION['save'][$page_name]['format_id'])
		{
			$format_id = $_SESSION['save'][$page_name]['format_id'];
		} else
		{
			$max_size = 999999999;
			foreach ($list_formats_global as $format)
			{
				$temp_size = explode("x", $format['size']);
				if ($temp_size[0] + $temp_size[1] < $max_size)
				{
					$max_size = $temp_size[0] + $temp_size[1];
					$format_id = $format['format_screenshot_id'];
				}
			}
		}
	}
	if ($format_id > 0)
	{
		foreach ($list_formats_global as $format)
		{
			if ($format['format_screenshot_id'] == $format_id)
			{
				$data_format = $format;
				break;
			}
		}
	}
}

$overview_amount = $data_video['screen_amount'];
$poster_amount = $data_video['poster_amount'];
$timeline_amount = 0;
foreach ($list_formats_videos_timelined as $format)
{
	if ($format['timeline_screen_amount'] > 0)
	{
		if ($timeline_amount == 0 || $format['timeline_screen_amount'] < $timeline_amount)
		{
			$timeline_amount = $format['timeline_screen_amount'];
		}
	}
}

$screen_amount = 0;
$screen_main = 0;
$source_folder = '';
$timeline_titles = [];
$screen_url = $config['content_url_videos_screenshots_admin_panel'] ?: $config['content_url_videos_screenshots'];

if ($group_id == 1)
{
	$screen_amount = $data_video['screen_amount'];
	$screen_main = $data_video['screen_main'];
	$source_folder = "$config[content_path_videos_sources]/$dir_path/$item_id/screenshots";
	if (isset($data_format['size']))
	{
		$screen_url = "$screen_url/$dir_path/$item_id/$data_format[size]";
	}
} elseif ($group_id == 2)
{
	if ($timeline_video_format_id == 0)
	{
		if (count($list_formats_videos_timelined) > 0)
		{
			$timeline_video_format_id = $list_formats_videos_timelined[0]['format_video_id'];
		}
	}
	if ($timeline_video_format_id > 0)
	{
		foreach ($list_formats_videos_timelined as $format)
		{
			if ($timeline_video_format_id == $format['format_video_id'])
			{
				$data_video_format = $format;
				$screen_amount = $format['timeline_screen_amount'];
				$source_folder = "$config[content_path_videos_sources]/$dir_path/$item_id/timelines/$format[timeline_directory]";
				if (isset($data_format['size']))
				{
					$screen_url = "$screen_url/$dir_path/$item_id/timelines/$format[timeline_directory]/$data_format[size]";
				}

				if (is_file("$config[content_path_videos_screenshots]/$dir_path/$item_id/timelines/$format[timeline_directory]/cuepoints.json"))
				{
					$timeline_titles = json_decode(file_get_contents("$config[content_path_videos_screenshots]/$dir_path/$item_id/timelines/$format[timeline_directory]/cuepoints.json"), true);
				}
				break;
			}
		}
	}
} elseif ($group_id == 3)
{
	$screen_amount = $data_video['poster_amount'];
	$screen_main = $data_video['poster_main'];
	$source_folder = "$config[content_path_videos_sources]/$dir_path/$item_id/posters";
	if (isset($data_format['size']))
	{
		$screen_url = "$screen_url/$dir_path/$item_id/posters/$data_format[size]";
	}
}

if (!isset($data_format['size']))
{
	$format_id = 'sources';
	$screen_url = '';
}

if ($_REQUEST['action'] == 'sources_zip')
{
	$video_dir = $data_video['dir'];
	if ($video_dir == '')
	{
		$video_dir = $data_video['video_id'];
	}
	$zip_files_to_add = [];
	for ($i = 1; $i <= $screen_amount; $i++)
	{
		$zip_files_to_add[] = "$source_folder/$i.jpg";
	}
	$rnd = mt_rand(10000000, 99999999);
	$zip = new PclZip("$config[temporary_path]/$rnd.zip");
	$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$source_folder");

	header("Content-Type: application/octet-stream");
	header("Content-Disposition: attachment; filename=$video_dir-sources.zip");
	header("Content-Length: " . filesize("$config[temporary_path]/$rnd.zip"));
	ob_end_clean();
	readfile("$config[temporary_path]/$rnd.zip");
	die;
} elseif ($_REQUEST['action'] == 'source')
{
	$dir_path = get_dir_by_id($item_id);
	$index = intval($_REQUEST['index']);
	if ($index == 0)
	{
		die;
	}

	$source_file = "$source_folder/$index.jpg";
	header("Content-Type: image/jpeg");
	header("Content-Length: " . filesize("$source_file"));
	ob_end_clean();
	readfile("$source_file");
	die;
}

$_SESSION['save'][$page_name]['group_id'] = $group_id;
$_SESSION['save'][$page_name]['overview_format_id'] = $overview_format_id;
$_SESSION['save'][$page_name]['timeline_format_id'] = $timeline_format_id;
$_SESSION['save'][$page_name]['timeline_video_format_id'] = $timeline_video_format_id;
$_SESSION['save'][$page_name]['poster_format_id'] = $poster_format_id;

$grabbing_possible = 1;
if (in_array($data_video['load_type_id'], [3, 5]) && $data_video['file_url'] == '')
{
	$grabbing_possible = 0;
}

$smarty = new mysmarty();
$smarty->assign('left_menu', 'menu_videos.tpl');
$smarty->assign('data_video', $data_video);
$smarty->assign('data_format', $data_format);
$smarty->assign('group_id', $group_id);
$smarty->assign('format_id', $format_id);
$smarty->assign('timeline_video_format_id', $timeline_video_format_id);
$smarty->assign('timeline_video_format_title', $data_video_format['title']);
$smarty->assign('timeline_titles', $timeline_titles);
$smarty->assign('screen_url', $screen_url);
$smarty->assign('screen_amount', $screen_amount);
$smarty->assign('screen_main', $screen_main);
$smarty->assign('overview_amount', $overview_amount);
$smarty->assign('timeline_amount', $timeline_amount);
$smarty->assign('poster_amount', $poster_amount);
$smarty->assign('options', $options);
$smarty->assign('list_formats_overview', $list_formats_overview);
$smarty->assign('list_formats_timeline', $list_formats_timeline);
$smarty->assign('list_formats_posters', $list_formats_posters);
$smarty->assign('list_formats_videos_timelined', $list_formats_videos_timelined);
$smarty->assign('grabbing_possible', $grabbing_possible);

if ($group_id == 1)
{
	if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/rotator.dat"))
	{
		$rotator_data = @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/rotator.dat"));
		foreach ($rotator_data as $k => $v)
		{
			$temp = explode("|", $v);
			$ctr = floatval($temp[0]);
			$rotator_data[$k] = array('ctr' => $ctr, 'clicks' => intval($temp[1]));
		}
		$smarty->assign('rotator_data', $rotator_data);
	}

	if (is_file("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/info.dat"))
	{
		$smarty->assign('screenshots_data', @unserialize(file_get_contents("$config[content_path_videos_sources]/$dir_path/$item_id/screenshots/info.dat")));
	}
}

$smarty->assign('supports_popups', 1);

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('list_messages', $list_messages);
$smarty->assign('list_flags_videos', mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 order by title asc")));
$smarty->assign('list_flags_admins', mr2array(sql("select * from $config[tables_prefix]flags where group_id=1 and is_admin_flag=1 order by title asc")));
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', str_replace("%1%", ($data_video['title'] ?: $data_video['video_id']), $lang['videos']['screenshots_header_mgmt']));

$smarty->display("layout.tpl");
