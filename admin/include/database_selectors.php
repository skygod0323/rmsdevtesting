<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

$database_selectors=array();
$database_selectors['generic_selector_tag']=($config['locale']<>'' ? "(case when tag_$config[locale]<>'' then tag_$config[locale] else tag end)" : 'tag');
$database_selectors['generic_selector_tag_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when tag_dir_$config[locale]<>'' then tag_dir_$config[locale] else tag_dir end)" : 'tag_dir');
$database_selectors['generic_selector_title']=($config['locale']<>'' ? "(case when title_$config[locale]<>'' then title_$config[locale] else title end)" : 'title');
$database_selectors['generic_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when dir_$config[locale]<>'' then dir_$config[locale] else dir end)" : 'dir');
$database_selectors['generic_selector_description']=($config['locale']<>'' ? "(case when description_$config[locale]<>'' then description_$config[locale] else description end)" : 'description');
$database_selectors['locale_field_tag']=($config['locale']<>'' && !isset($config['locale_site_only']) ? "tag_$config[locale]" : 'tag');
$database_selectors['locale_field_title']=($config['locale']<>'' && !isset($config['locale_site_only']) ? "title_$config[locale]" : 'title');
$database_selectors['locale_field_description']=($config['locale']<>'' && !isset($config['locale_site_only']) ? "description_$config[locale]" : 'description');

$database_selectors['videos_selector_title']=($config['locale']<>'' ? "(case when $config[tables_prefix]videos.title_$config[locale]<>'' then $config[tables_prefix]videos.title_$config[locale] else $config[tables_prefix]videos.title end)" : "$config[tables_prefix]videos.title");
$database_selectors['albums_selector_title']=($config['locale']<>'' ? "(case when $config[tables_prefix]albums.title_$config[locale]<>'' then $config[tables_prefix]albums.title_$config[locale] else $config[tables_prefix]albums.title end)" : "$config[tables_prefix]albums.title");
$database_selectors['content_sources_selector_title']=($config['locale']<>'' ? "(case when $config[tables_prefix]content_sources.title_$config[locale]<>'' then $config[tables_prefix]content_sources.title_$config[locale] else $config[tables_prefix]content_sources.title end)" : "$config[tables_prefix]content_sources.title");
$database_selectors['models_selector_title']=($config['locale']<>'' ? "(case when $config[tables_prefix]models.title_$config[locale]<>'' then $config[tables_prefix]models.title_$config[locale] else $config[tables_prefix]models.title end)" : "$config[tables_prefix]models.title");
$database_selectors['dvds_selector_title']=($config['locale']<>'' ? "(case when $config[tables_prefix]dvds.title_$config[locale]<>'' then $config[tables_prefix]dvds.title_$config[locale] else $config[tables_prefix]dvds.title end)" : "$config[tables_prefix]dvds.title");
$database_selectors['posts_selector_title']="$config[tables_prefix]posts.title";
$database_selectors['playlists_selector_title']="$config[tables_prefix]playlists.title";

$database_selectors['videos_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when $config[tables_prefix]videos.dir_$config[locale]<>'' then $config[tables_prefix]videos.dir_$config[locale] else $config[tables_prefix]videos.dir end)" : "$config[tables_prefix]videos.dir");
$database_selectors['albums_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when $config[tables_prefix]albums.dir_$config[locale]<>'' then $config[tables_prefix]albums.dir_$config[locale] else $config[tables_prefix]albums.dir end)" : "$config[tables_prefix]albums.dir");
$database_selectors['content_sources_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when $config[tables_prefix]content_sources.dir_$config[locale]<>'' then $config[tables_prefix]content_sources.dir_$config[locale] else $config[tables_prefix]content_sources.dir end)" : "$config[tables_prefix]content_sources.dir");
$database_selectors['models_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when $config[tables_prefix]models.dir_$config[locale]<>'' then $config[tables_prefix]models.dir_$config[locale] else $config[tables_prefix]models.dir end)" : "$config[tables_prefix]models.dir");
$database_selectors['dvds_selector_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "(case when $config[tables_prefix]dvds.dir_$config[locale]<>'' then $config[tables_prefix]dvds.dir_$config[locale] else $config[tables_prefix]dvds.dir end)" : "$config[tables_prefix]dvds.dir");
$database_selectors['posts_selector_dir']="$config[tables_prefix]posts.dir";
$database_selectors['playlists_selector_dir']="$config[tables_prefix]playlists.dir";

$database_selectors['videos_selector_description']=($config['locale']<>'' ? "(case when $config[tables_prefix]videos.description_$config[locale]<>'' then $config[tables_prefix]videos.description_$config[locale] else $config[tables_prefix]videos.description end)" : "$config[tables_prefix]videos.description");
$database_selectors['albums_selector_description']=($config['locale']<>'' ? "(case when $config[tables_prefix]albums.description_$config[locale]<>'' then $config[tables_prefix]albums.description_$config[locale] else $config[tables_prefix]albums.description end)" : "$config[tables_prefix]albums.description");
$database_selectors['content_sources_selector_description']=($config['locale']<>'' ? "(case when $config[tables_prefix]content_sources.description_$config[locale]<>'' then $config[tables_prefix]content_sources.description_$config[locale] else $config[tables_prefix]content_sources.description end)" : "$config[tables_prefix]content_sources.description");
$database_selectors['models_selector_description']=($config['locale']<>'' ? "(case when $config[tables_prefix]models.description_$config[locale]<>'' then $config[tables_prefix]models.description_$config[locale] else $config[tables_prefix]models.description end)" : "$config[tables_prefix]models.description");
$database_selectors['dvds_selector_description']=($config['locale']<>'' ? "(case when $config[tables_prefix]dvds.description_$config[locale]<>'' then $config[tables_prefix]dvds.description_$config[locale] else $config[tables_prefix]dvds.description end)" : "$config[tables_prefix]dvds.description");
$database_selectors['posts_selector_description']="$config[tables_prefix]posts.description";
$database_selectors['playlists_selector_description']="$config[tables_prefix]playlists.description";

$database_selectors['generic_post_date_selector']='post_date';
if ($config['relative_post_dates']=="true")
{
	$relative_post_date=0;
	$relative_post_date_start="'".date("Y-m-d H:i:s")."'";
	if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
	{
		$registration_date=strtotime($_SESSION['added_date']);
		$relative_post_date=floor((time()-$registration_date)/86400);
		$relative_post_date_start="'".date("Y-m-d H:i:s",$registration_date)."'";
	}
	$database_selectors['generic_post_date_selector']="(case when relative_post_date!=0 then date_add($relative_post_date_start, interval relative_post_date-$relative_post_date day) else post_date end)";
}

$lang_selectors_dir="";
$lang_selectors_tag_dir="";

if ($config['locale_expose_translated_directories']=='true')
{
	require_once "$config[project_path]/admin/include/functions_base.php";

	$language_codes=mr2array_list(sql_pr("select code from $config[tables_prefix]languages"));
	if (count($language_codes)>0)
	{
		$database_selectors['locales']=array('default');

		$lang_selectors_dir.="dir as dir_default, ";
		$lang_selectors_tag_dir.="tag_dir as tag_dir_default, tag_dir as dir_default, ";
		foreach ($language_codes as $language_code)
		{
			if (strlen($language_code)==2)
			{
				$lang_selectors_dir.="dir_$language_code, ";
				$lang_selectors_tag_dir.="tag_dir_$language_code, tag_dir_$language_code as dir_$language_code, ";
				$database_selectors['locales'][]=$language_code;
			}
		}
	}
}

$database_selectors['tags']="$config[tables_prefix]tags.tag_id, $database_selectors[generic_selector_tag] as tag, $database_selectors[generic_selector_tag] as title, $database_selectors[generic_selector_tag_dir] as tag_dir, $database_selectors[generic_selector_tag_dir] as dir, $lang_selectors_tag_dir synonyms, total_videos, today_videos, total_albums, today_albums, total_photos, total_posts, today_posts, total_playlists, total_models, total_dvds, total_dvd_groups, total_cs, added_date, custom1, custom2, custom3, custom4, custom5, avg_videos_rating, avg_albums_rating, avg_videos_popularity, avg_albums_popularity, avg_posts_rating, avg_posts_popularity";
$database_selectors['categories']="$config[tables_prefix]categories.category_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir synonyms, screenshot1, screenshot2, case when screenshot1!='' then 1 else 0 end as is_avatar_available, category_group_id, subscribers_count, custom1, custom2, custom3, custom4, custom5, custom6, custom7, custom8, custom9, custom10, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, total_videos, today_videos, total_albums, today_albums, total_photos, total_posts, today_posts, total_playlists, total_models, total_dvds, total_dvd_groups, total_cs, added_date, avg_videos_rating, avg_albums_rating, avg_videos_popularity, avg_albums_popularity, avg_posts_rating, avg_posts_popularity";
$database_selectors['categories_groups']="$config[tables_prefix]categories_groups.category_group_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir external_id, screenshot1, screenshot2, case when screenshot1!='' then 1 else 0 end as is_avatar_available, custom1, custom2, custom3, added_date";
$database_selectors['content_sources']="$config[tables_prefix]content_sources.content_source_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir content_source_group_id, screenshot1, screenshot2, url, (rating/rating_amount) as rating, rating_amount, cs_viewed, comments_count, subscribers_count, custom1, custom2, custom3, custom4, custom5, custom6, custom7, custom8, custom9, custom10, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, custom_file6, custom_file7, custom_file8, custom_file9, custom_file10, total_videos, today_videos, total_albums, today_albums, total_photos, last_content_date, `rank`, last_rank, added_date, avg_videos_rating, avg_albums_rating, avg_videos_popularity, avg_albums_popularity";
$database_selectors['content_sources_groups']="$config[tables_prefix]content_sources_groups.content_source_group_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir external_id, custom1, custom2, custom3, custom4, custom5, added_date";
$database_selectors['dvds']="$config[tables_prefix]dvds.dvd_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir cover1_front, cover1_back, cover2_front, cover2_back, user_id, dvd_group_id, sort_id, is_video_upload_allowed, tokens_required, (rating/rating_amount) as rating, rating_amount, dvd_viewed, comments_count, subscribers_count, custom1, custom2, custom3, custom4, custom5, custom6, custom7, custom8, custom9, custom10, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, total_videos, today_videos, total_videos_duration, last_content_date, added_date, avg_videos_rating, avg_videos_popularity";
$database_selectors['dvds_groups']="$config[tables_prefix]dvds_groups.dvd_group_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir external_id, cover1, cover2, total_dvds, added_date, custom1, custom2, custom3, custom4, custom5";
$database_selectors['models']="$config[tables_prefix]models.model_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir alias, model_group_id, screenshot1, screenshot2, country_id, state, city, height, weight, hair_id, eye_color_id, measurements, gender_id, birth_date, death_date, age, (rating/rating_amount) as rating, rating_amount, model_viewed, comments_count, subscribers_count, access_level_id, custom1, custom2, custom3, custom4, custom5, custom6, custom7, custom8, custom9, custom10, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, total_videos, today_videos, total_albums, today_albums, total_photos, total_posts, today_posts, total_dvds, total_dvd_groups, last_content_date, `rank`, last_rank, added_date, avg_videos_rating, avg_albums_rating, avg_videos_popularity, avg_albums_popularity, avg_posts_rating, avg_posts_popularity";
$database_selectors['models_groups']="$config[tables_prefix]models_groups.model_group_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir external_id, screenshot1, screenshot2, added_date";
$database_selectors['albums']="$config[tables_prefix]albums.album_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir server_group_id, user_id, main_photo_id, zip_files, status_id, content_source_id, is_private, access_level_id, is_locked, photos_amount, delete_reason, (rating/rating_amount) as rating, rating_amount, album_viewed, album_viewed_unique, album_viewed_paid, comments_count, favourites_count, purchases_count, tokens_required, $database_selectors[generic_post_date_selector] as post_date, last_time_view_date, added_date, admin_flag_id, af_custom1, af_custom2, af_custom3, custom1, custom2, custom3, gallery_url";
$database_selectors['albums_images']="$config[tables_prefix]albums_images.image_id, album_id, title, (rating/rating_amount) as rating, rating_amount, image_viewed, image_formats, added_date";
$database_selectors['videos']="$config[tables_prefix]videos.video_id, $database_selectors[generic_selector_title] as title, $database_selectors[generic_selector_description] as description, $database_selectors[generic_selector_dir] as dir, $lang_selectors_dir server_group_id, load_type_id, user_id, content_source_id, dvd_id, status_id, is_hd, is_private, access_level_id, is_locked, duration, file_formats, file_size, file_dimensions, file_url, pseudo_url, screen_amount, screen_main, screen_main_temp, poster_amount, poster_main, embed, delete_reason, (rating/rating_amount) as rating, rating_amount, video_viewed, video_viewed_player, video_viewed_unique, video_viewed_paid, comments_count, favourites_count, purchases_count, tokens_required, release_year, $database_selectors[generic_post_date_selector] as post_date, last_time_view_date, added_date, rs_completed, admin_flag_id, af_custom1, af_custom2, af_custom3, custom1, custom2, custom3, gallery_url";
$database_selectors['posts']="$config[tables_prefix]posts.post_id, title, description, content, dir, post_type_id, connected_video_id, user_id, status_id, is_locked, (rating/rating_amount) as rating, rating_amount, post_viewed, $database_selectors[generic_post_date_selector] as post_date, comments_count, last_time_view_date, added_date, af_custom1, af_custom2, af_custom3, custom1, custom2, custom3, custom4, custom5, custom6, custom7, custom8, custom9, custom10, custom_file1, custom_file2, custom_file3, custom_file4, custom_file5, custom_file6, custom_file7, custom_file8, custom_file9, custom_file10";

$database_selectors['playlists']="$config[tables_prefix]playlists.*, ($config[tables_prefix]playlists.rating/$config[tables_prefix]playlists.rating_amount) as rating";

$relative_post_date_filter=10000;
if ($config['relative_post_dates']=="true")
{
	$relative_post_date_filter=0;
	if ($_SESSION['user_id']>0 && $_SESSION['added_date']<>'')
	{
		$registration_date=strtotime($_SESSION['added_date']);
		$relative_post_date_filter=floor((time()-$registration_date)/86400)+1;
	}
}
$now_date=date("Y-m-d H:i:s");
if (intval($config['post_dates_offset'])>0)
{
	$time=time();
	$offset=$time%(intval($config['post_dates_offset'])*60);
	if ($offset>600)
	{
		$offset%=600;
	}
	$time-=$offset;
	$now_date=date("Y-m-d H:i:s",$time-intval($config['post_dates_offset'])*60);
}
$database_selectors['where_videos']="$config[tables_prefix]videos.status_id=1 and $config[tables_prefix]videos.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]videos.post_date<='$now_date'";
$database_selectors['where_videos_active_deleted']="$config[tables_prefix]videos.status_id in (1,5) and $config[tables_prefix]videos.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]videos.post_date<='$now_date'";
$database_selectors['where_videos_active_disabled_deleted']="$config[tables_prefix]videos.status_id in (0,1,5) and $config[tables_prefix]videos.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]videos.post_date<='$now_date'";
$database_selectors['where_videos_all']="$config[tables_prefix]videos.status_id in (0,1,2,3,5) and $config[tables_prefix]videos.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]videos.post_date<='$now_date'";
$database_selectors['where_videos_internal']="$config[tables_prefix]videos.status_id in (0,1,2,3) and $config[tables_prefix]videos.relative_post_date>=0 and $config[tables_prefix]videos.post_date<='$now_date'";
$database_selectors['where_videos_future']="$config[tables_prefix]videos.status_id=1 and ($config[tables_prefix]videos.post_date>'$now_date' or $config[tables_prefix]videos.relative_post_date>$relative_post_date_filter)";
$database_selectors['where_videos_admin']="1=1";

$database_selectors['where_albums']="$config[tables_prefix]albums.status_id=1 and $config[tables_prefix]albums.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]albums.post_date<='$now_date'";
$database_selectors['where_albums_active_deleted']="$config[tables_prefix]albums.status_id in (1,5) and $config[tables_prefix]albums.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]albums.post_date<='$now_date'";
$database_selectors['where_albums_active_disabled_deleted']="$config[tables_prefix]albums.status_id in (0,1,5) and $config[tables_prefix]albums.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]albums.post_date<='$now_date'";
$database_selectors['where_albums_all']="$config[tables_prefix]albums.status_id in (0,1,2,3,5) and $config[tables_prefix]albums.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]albums.post_date<='$now_date'";
$database_selectors['where_albums_internal']="$config[tables_prefix]albums.status_id in (0,1,2,3) and $config[tables_prefix]albums.relative_post_date>=0 and $config[tables_prefix]albums.post_date<='$now_date'";
$database_selectors['where_albums_future']="$config[tables_prefix]albums.status_id=1 and ($config[tables_prefix]albums.post_date>'$now_date' or $config[tables_prefix]albums.relative_post_date>$relative_post_date_filter)";
$database_selectors['where_albums_admin']="1=1";

$database_selectors['where_posts']="$config[tables_prefix]posts.status_id=1 and $config[tables_prefix]posts.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]posts.post_date<='$now_date'";
$database_selectors['where_posts_active_disabled']="$config[tables_prefix]posts.status_id in (0,1) and $config[tables_prefix]posts.relative_post_date<=$relative_post_date_filter and $config[tables_prefix]posts.post_date<='$now_date'";
$database_selectors['where_posts_all']=$database_selectors['where_posts_active_disabled'];
$database_selectors['where_posts_admin']="1=1";

$database_selectors['where_playlists']="$config[tables_prefix]playlists.status_id=1 and $config[tables_prefix]playlists.is_private=0";
$database_selectors['where_playlists_active_disabled']="$config[tables_prefix]playlists.status_id in (0,1)";

$database_selectors['where_categories']="$config[tables_prefix]categories.status_id=1";
$database_selectors['where_categories_active_disabled']="1=1";
$database_selectors['where_categories_groups']="$config[tables_prefix]categories_groups.status_id=1";
$database_selectors['where_categories_groups_active_disabled']="1=1";
$database_selectors['where_tags']="$config[tables_prefix]tags.status_id=1";
$database_selectors['where_tags_active_disabled']="1=1";
$database_selectors['where_models']="$config[tables_prefix]models.status_id=1";
$database_selectors['where_models_active_disabled']="1=1";
$database_selectors['where_models_groups']="$config[tables_prefix]models_groups.status_id=1";
$database_selectors['where_models_groups_active_disabled']="1=1";
$database_selectors['where_content_sources']="$config[tables_prefix]content_sources.status_id=1";
$database_selectors['where_content_sources_active_disabled']="1=1";
$database_selectors['where_content_sources_groups']="$config[tables_prefix]content_sources_groups.status_id=1";
$database_selectors['where_content_sources_groups_active_disabled']="1=1";
$database_selectors['where_dvds']="$config[tables_prefix]dvds.status_id=1";
$database_selectors['where_dvds_active_disabled']="1=1";
$database_selectors['where_dvds_groups']="$config[tables_prefix]dvds_groups.status_id=1";
$database_selectors['where_dvds_groups_active_disabled']="1=1";

$database_selectors['where_locale_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "dir_$config[locale]=?" : '1=0');
$database_selectors['where_locale_tag_dir']=($config['locale']<>'' && $config['is_clone_db']=='true' ? "tag_dir_$config[locale]=?" : '1=0');

if (is_array($config['advanced_filtering']))
{
	foreach ($config['advanced_filtering'] as $advanced_filter)
	{
		if (strpos($advanced_filter, 'videos_custom_flag') === 0)
		{
			$temp_where_videos = '';
			unset($temp);
			preg_match("|videos_custom_flag(\d)\ *=\ *(\d+)|is", $advanced_filter, $temp);
			if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
			{
				$temp_af_custom_index = intval($temp[1]);
				$temp_af_custom_value = intval($temp[2]);
				$temp_where_videos = "$config[tables_prefix]videos.af_custom$temp_af_custom_index=$temp_af_custom_value";
			} else
			{
				unset($temp);
				preg_match("|videos_custom_flag(\d)\ *!=\ *(\d+)|is", $advanced_filter, $temp);
				if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
				{
					$temp_af_custom_index = intval($temp[1]);
					$temp_af_custom_value = intval($temp[2]);
					$temp_where_videos = "$config[tables_prefix]videos.af_custom$temp_af_custom_index!=$temp_af_custom_value";
				}
			}
			if ($temp_where_videos)
			{
				$database_selectors['where_videos'] .= " and $temp_where_videos";
				$database_selectors['where_videos_active_deleted'] .= " and $temp_where_videos";
				$database_selectors['where_videos_active_disabled_deleted'] .= " and $temp_where_videos";
				$database_selectors['where_videos_all'] .= " and $temp_where_videos";
				$database_selectors['where_videos_internal'] .= " and $temp_where_videos";
				$database_selectors['where_videos_future'] .= " and $temp_where_videos";
				$database_selectors['where_videos_admin'] .= " and $temp_where_videos";
			}
		} elseif (strpos($advanced_filter, 'albums_custom_flag') === 0)
		{
			$temp_where_albums = '';
			unset($temp);
			preg_match("|albums_custom_flag(\d)\ *=\ *(\d+)|is", $advanced_filter, $temp);
			if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
			{
				$temp_af_custom_index = intval($temp[1]);
				$temp_af_custom_value = intval($temp[2]);
				$temp_where_albums = "$config[tables_prefix]albums.af_custom$temp_af_custom_index=$temp_af_custom_value";
			} else
			{
				unset($temp);
				preg_match("|albums_custom_flag(\d)\ *!=\ *(\d+)|is", $advanced_filter, $temp);
				if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
				{
					$temp_af_custom_index = intval($temp[1]);
					$temp_af_custom_value = intval($temp[2]);
					$temp_where_albums = "$config[tables_prefix]albums.af_custom$temp_af_custom_index!=$temp_af_custom_value";
				}
			}
			if ($temp_where_albums)
			{
				$database_selectors['where_albums'] .= " and $temp_where_albums";
				$database_selectors['where_albums_active_deleted'] .= " and $temp_where_albums";
				$database_selectors['where_albums_active_disabled_deleted'] .= " and $temp_where_albums";
				$database_selectors['where_albums_all'] .= " and $temp_where_albums";
				$database_selectors['where_albums_internal'] .= " and $temp_where_albums";
				$database_selectors['where_albums_future'] .= " and $temp_where_albums";
				$database_selectors['where_albums_admin'] .= " and $temp_where_albums";
			}
		} elseif (strpos($advanced_filter, 'posts_custom_flag') === 0)
		{
			$temp_where_posts = '';
			unset($temp);
			preg_match("|posts_custom_flag(\d)\ *=\ *(\d+)|is", $advanced_filter, $temp);
			if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
			{
				$temp_af_custom_index = intval($temp[1]);
				$temp_af_custom_value = intval($temp[2]);
				$temp_where_posts = "$config[tables_prefix]posts.af_custom$temp_af_custom_index=$temp_af_custom_value";
			} else
			{
				unset($temp);
				preg_match("|posts_custom_flag(\d)\ *!=\ *(\d+)|is", $advanced_filter, $temp);
				if (in_array(intval($temp[1]), array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10)) && intval($temp[2]) > 0)
				{
					$temp_af_custom_index = intval($temp[1]);
					$temp_af_custom_value = intval($temp[2]);
					$temp_where_posts = "$config[tables_prefix]posts.af_custom$temp_af_custom_index!=$temp_af_custom_value";
				}
			}
			if ($temp_where_posts)
			{
				$database_selectors['where_posts'] .= " and $temp_where_posts";
				$database_selectors['where_posts_active_disabled'] .= " and $temp_where_posts";
				$database_selectors['where_posts_admin'] .= " and $temp_where_posts";
				$database_selectors['where_posts_all'] = $database_selectors['where_posts_active_disabled'];
			}
		}
	}
}

if ($config['locale_show_translated_only']=='true' && $config['locale']!='')
{
	$database_selectors['where_videos'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";
	$database_selectors['where_videos_active_deleted'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";
	$database_selectors['where_videos_active_disabled_deleted'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";
	$database_selectors['where_videos_all'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";
	$database_selectors['where_videos_future'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";
	$database_selectors['where_videos_admin'].=" and $config[tables_prefix]videos.title_$config[locale]!=''";

	$database_selectors['where_albums'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
	$database_selectors['where_albums_active_deleted'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
	$database_selectors['where_albums_active_disabled_deleted'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
	$database_selectors['where_albums_all'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
	$database_selectors['where_albums_future'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
	$database_selectors['where_albums_admin'].=" and $config[tables_prefix]albums.title_$config[locale]!=''";
}

if ($config['locale_show_translated_categorization_only']=='true' && $config['locale']!='')
{
	$database_selectors['where_categories'].=" and $config[tables_prefix]categories.title_$config[locale]!=''";
	$database_selectors['where_categories_active_disabled'].=" and $config[tables_prefix]categories.title_$config[locale]!=''";
	$database_selectors['where_categories_groups'].=" and $config[tables_prefix]categories_groups.title_$config[locale]!=''";
	$database_selectors['where_categories_groups_active_disabled'].=" and $config[tables_prefix]categories_groups.title_$config[locale]!=''";
	$database_selectors['where_tags'].=" and $config[tables_prefix]tags.tag_$config[locale]!=''";
	$database_selectors['where_tags_active_disabled'].=" and $config[tables_prefix]tags.tag_$config[locale]!=''";
	$database_selectors['where_models'].=" and $config[tables_prefix]models.title_$config[locale]!=''";
	$database_selectors['where_models_active_disabled'].=" and $config[tables_prefix]models.title_$config[locale]!=''";
	$database_selectors['where_models_groups'].=" and $config[tables_prefix]models.title_$config[locale]!=''";
	$database_selectors['where_models_groups_active_disabled'].=" and $config[tables_prefix]models.title_$config[locale]!=''";
	$database_selectors['where_content_sources'].=" and $config[tables_prefix]content_sources.title_$config[locale]!=''";
	$database_selectors['where_content_sources_active_disabled'].=" and $config[tables_prefix]content_sources.title_$config[locale]!=''";
	$database_selectors['where_content_sources_groups'].=" and $config[tables_prefix]content_sources_groups.title_$config[locale]!=''";
	$database_selectors['where_content_sources_groups_active_disabled'].=" and $config[tables_prefix]content_sources_groups.title_$config[locale]!=''";
	$database_selectors['where_dvds'].=" and $config[tables_prefix]dvds.title_$config[locale]!=''";
	$database_selectors['where_dvds_active_disabled'].=" and $config[tables_prefix]dvds.title_$config[locale]!=''";
	$database_selectors['where_dvds_groups'].=" and $config[tables_prefix]dvds_groups.title_$config[locale]!=''";
	$database_selectors['where_dvds_groups_active_disabled'].=" and $config[tables_prefix]dvds_groups.title_$config[locale]!=''";
}

if ($config['locale_expose_original_titles']=='true' && $config['locale']!='')
{
	$database_selectors['tags']="$database_selectors[tags], tag as tag_default";
	$database_selectors['categories']="$database_selectors[categories], title as title_default";
	$database_selectors['categories_groups']="$database_selectors[categories_groups], title as title_default";
	$database_selectors['content_sources']="$database_selectors[content_sources], title as title_default";
	$database_selectors['content_sources_groups']="$database_selectors[content_sources_groups], title as title_default";
	$database_selectors['dvds']="$database_selectors[dvds], title as title_default";
	$database_selectors['dvds_groups']="$database_selectors[dvds_groups], title as title_default";
	$database_selectors['models']="$database_selectors[models], title as title_default";
	$database_selectors['models_groups']="$database_selectors[models_groups], title as title_default";
	$database_selectors['albums']="$database_selectors[albums], title as title_default";
	$database_selectors['videos']="$database_selectors[videos], title as title_default";
}