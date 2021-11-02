<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromAsp extends KvsDataMigrator
{
	public const OPT_DB_PREFIX = "asp_db_prefix";
	public const OPT_USERS_BASE_URL = "asp_users_base_url"; // http://domain.com/media/users
	public const OPT_CATEGORIES_VIDEOS_BASE_URL = "asp_categories_videos_base_url"; // http://domain.com/media/videos/cat
	public const OPT_CATEGORIES_PHOTOS_BASE_URL = "asp_categories_photos_base_url"; // http://domain.com/media/photos/cat
	public const OPT_MODELS_BASE_URL = "asp_models_base_url"; // http://domain.com/media/pornstars
	public const OPT_CHANNELS_BASE_URL = "asp_channels_base_url"; // http://domain.com/media/channels
	public const OPT_VIDEOS_BASE_URL = "asp_videos_base_url"; // http://domain.com/media/videos/mp4
	public const OPT_PHOTOS_BASE_URL = "asp_photos_base_url"; // http://domain.com/media/photos/orig
	public const OPT_SCREEN_BASE_URL = "asp_screen_base_url"; // http://domain.com/media/videos/tmb

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "asp";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Adult Script Pro";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, true, true, false, true, false, true, true, true, true, true, true, true, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_DB_PREFIX, self::OPT_USERS_BASE_URL, self::OPT_CATEGORIES_VIDEOS_BASE_URL, self::OPT_CATEGORIES_PHOTOS_BASE_URL, self::OPT_MODELS_BASE_URL, self::OPT_CHANNELS_BASE_URL, self::OPT_VIDEOS_BASE_URL, self::OPT_SCREEN_BASE_URL, self::OPT_PHOTOS_BASE_URL);
	}

	/**
	 * @return bool
	 */
	protected function pre_start_hook(): bool
	{
		if (!parent::pre_start_hook())
		{
			return false;
		}

		if ($this->data_to_migrate->is_comments())
		{
			$this->query_target("ALTER TABLE {$this->kvs_config["tables_prefix"]}comments ADD COLUMN migrator_uid VARCHAR(20) DEFAULT NULL");
			$this->query_target("ALTER TABLE {$this->kvs_config["tables_prefix"]}comments ADD UNIQUE migrator_uid(migrator_uid)");
		}

		return true;
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return array
	 */
	protected function pre_process_each_object_hook(array $p_object, int $p_object_type): array
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$p_object = parent::pre_process_each_object_hook($p_object, $p_object_type);

		foreach ($p_object as $key => $value)
		{
			$p_object[$key] = html_entity_decode($value);
		}

		if ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$video_id = intval($p_object["video_id"]);

			$p_object["categories"] = $this->query_text_source("SELECT group_concat(c.name SEPARATOR '||') FROM {$db_prefix}video_category vc INNER JOIN {$db_prefix}video_categories c ON c.cat_id=vc.cat_id WHERE vc.video_id=$video_id GROUP BY vc.video_id");
			$p_object["models"] = $this->query_text_source("SELECT group_concat(m.name SEPARATOR '||') FROM {$db_prefix}model_videos vm INNER JOIN {$db_prefix}model m ON m.model_id=vm.model_id WHERE vm.video_id=$video_id GROUP BY vm.video_id");
			$p_object["tags"] = $this->query_text_source("SELECT group_concat(name SEPARATOR '||') FROM {$db_prefix}video_tags WHERE video_id=$video_id GROUP BY video_id");

			$file_download_urls = array();

			$video_data_result = $this->query_source("SELECT vs.url, vt.mp4_trailer FROM {$db_prefix}video v LEFT JOIN {$db_prefix}video_trailer vt ON v.video_id=vt.video_id LEFT JOIN {$db_prefix}server vs ON v.server=vs.server_id WHERE v.video_id=$video_id");
			if ($video_data_result && $video_data_result->num_rows > 0)
			{
				$video_data_result->data_seek(0);
				$video_data_item = $video_data_result->fetch_assoc();
				if (strlen($video_data_item['url']) > 10)
				{
					$base_url = trim($video_data_item['url'], '/');
					$file_download_urls[] = array('postfix' => '.mp4', 'url' => "$base_url/mp4/$video_id.mp4", 'is_source' => 1);
					if ($p_object["hd"] == 1 && $this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_720p.mp4'") == 1)
					{
						unset($file_download_urls[0]['is_source']);
						$file_download_urls[] = array('postfix' => '_720p.mp4', 'url' => "$base_url/mp4/{$video_id}_hd.mp4", 'is_source' => 1);
					}
					if ($p_object["mobile"] == 1 && $this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_360p.mp4'") == 1)
					{
						$file_download_urls[] = array('postfix' => '_360p.mp4', 'url' => "$base_url/mobile/{$video_id}.mp4");
					}
					if ($video_data_item["mp4_trailer"] == 1 && $this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_trailer.mp4'") == 1)
					{
						$file_download_urls[] = array('postfix' => '_trailer.mp4', 'url' => "$base_url/mp4/{$video_id}.trailer.mp4");
					}
				}
			}

			if (count($file_download_urls) == 0)
			{
				$base_url = trim($this->get_option_value(self::OPT_VIDEOS_BASE_URL));
				if ($base_url)
				{
					$file_download_urls[] = array('postfix' => '.mp4', 'url' => "$base_url/mp4/$video_id.mp4", 'is_source' => 1);
					if ($p_object["hd"] == 1 && $this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_720p.mp4'") == 1)
					{
						unset($file_download_urls[0]['is_source']);
						$file_download_urls[] = array('postfix' => '_720p.mp4', 'url' => "$base_url/mp4/{$video_id}_hd.mp4", 'is_source' => 1);
					}
					if ($p_object["mobile"] == 1 && $this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_360p.mp4'") == 1)
					{
						$file_download_urls[] = array('postfix' => '_360p.mp4', 'url' => "$base_url/mobile/{$video_id}.mp4");
					}
				}
			}

			if (count($file_download_urls) > 0)
			{
				$p_object['file_download_urls'] = $file_download_urls;
			}

			unset($p_object["hd"], $p_object["mobile"]);
		} elseif ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$album_id = intval($p_object["album_id"]);

			$p_object["categories"] = $this->query_text_source("SELECT group_concat(c.name SEPARATOR '||') FROM {$db_prefix}photo_category ac INNER JOIN {$db_prefix}photo_categories c ON c.cat_id=ac.cat_id WHERE ac.album_id=$album_id GROUP BY ac.album_id");
			$p_object["tags"] = $this->query_text_source("SELECT group_concat(name SEPARATOR '||') FROM {$db_prefix}photo_tags WHERE album_id=$album_id GROUP BY album_id");
		}

		return $p_object;
	}

	/**
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$queries = array();
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}user";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}video_categories";
			$queries[] = "SELECT count(*) FROM {$db_prefix}photo_categories";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$new_channels = $this->query_num_source("SELECT count(*) FROM {$db_prefix}channel");
			if ($new_channels > 0)
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}channel";
				$queries[] = "SELECT count(*) FROM {$db_prefix}channel_networks";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}video_sponsors";
			}
		}
		if ($this->data_to_migrate->is_models())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}model";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$db_prefix}video LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}video";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$db_prefix}photo_albums LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}photo_albums";
			}
		}
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}video_comments";
			$queries[] = "SELECT count(*) FROM {$db_prefix}photo_comments";
			$queries[] = "SELECT count(*) FROM {$db_prefix}model_comments";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			if ($this->data_to_migrate->is_videos())
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}video_favorites";
			}
			if ($this->data_to_migrate->is_albums())
			{
				$queries[] = "SELECT count(*) FROM (SELECT DISTINCT p.album_id, pf.user_id FROM {$db_prefix}photo_favorites pf INNER JOIN {$db_prefix}photo p ON pf.photo_id=p.photo_id) X";
			}
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}user_friends f1 LEFT JOIN {$db_prefix}user_friends f2 ON f1.user_id=f2.friend_id AND f2.user_id=f1.friend_id WHERE (f2.request_id IS NULL OR f2.request_id>f1.request_id) AND (f1.status='approved' OR f1.status='pending') AND (f2.status!='denied' OR f2.status IS NULL)";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}message WHERE status in (1,2)";
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}model_favorites";
			$queries[] = "SELECT count(*) FROM {$db_prefix}user_subscriptions";

			$new_channels = $this->query_num_source("SELECT count(*) FROM {$db_prefix}channel");
			if ($new_channels > 0)
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}channel_subscribers";
			}
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_USERS_BASE_URL));

		$selector = "u.user_id, u.join_ip AS ip, u.country, u.gender, u.relation AS relationship_status, u.interested AS orientation, CASE WHEN u.status='1' THEN 2 ELSE 0 END AS status_id, u.username, u.password AS pass, u.email, CASE WHEN u.name='' THEN u.username ELSE u.name END AS display_name, u.birth_date, u.city, u.login_date AS last_login_date, CASE WHEN u.join_date='0000-00-00 00:00:00' THEN (SELECT min(join_date) FROM {$db_prefix}user WHERE join_date!='0000-00-00 00:00:00') ELSE u.join_date END AS added_date, u.popularity AS profile_viewed, p.website, p.school AS education, p.occupation, p.about AS about_me, p.hobbies AS interests, p.movies AS favourite_movies, p.music AS favourite_music, p.books AS favourite_books, a.total_video_views AS video_viewed, a.total_viewed_videos AS video_watched, ";

		if ($base_url)
		{
			$selector .= "(CASE WHEN avatar!='' THEN concat(u.user_id, '.', avatar) ELSE '' END) AS avatar, ";
			$selector .= "(CASE WHEN avatar!='' THEN concat('$base_url/', u.user_id, '.', avatar) ELSE '' END) AS avatar_url, ";
		}

		$projector = "{$db_prefix}user u INNER JOIN {$db_prefix}user_profile p ON u.user_id=p.user_id INNER JOIN {$db_prefix}user_activity a ON u.user_id=a.user_id WHERE u.username!='anonymous' and u.user_id!=2";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM $projector) X", self::OBJECT_TYPE_USER);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_CATEGORIES_VIDEOS_BASE_URL));

		$selector1 = "cat_id AS category_id, name AS title, slug AS dir, description, now() AS added_date, meta_title AS custom1, meta_desc AS custom2, meta_keys AS custom3, ";
		if ($base_url)
		{
			$selector1 .= "concat(cat_id, '.jpg') AS screenshot1, ";
			$selector1 .= "concat('$base_url/', cat_id, '.jpg') AS screenshot1_url, ";
		}

		$base_url = trim($this->get_option_value(self::OPT_CATEGORIES_PHOTOS_BASE_URL));

		$selector2 = "100 + cat_id AS category_id, name AS title, slug AS dir, description, now() AS added_date, '' AS custom1, '' AS custom2, '' AS custom3, ";
		if ($base_url)
		{
			$selector2 .= "concat((100 + cat_id), '.jpg') AS screenshot1, ";
			$selector2 .= "concat('$base_url/', cat_id, '.jpg') AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT " . trim($selector1, ", ") . " FROM {$db_prefix}video_categories UNION ALL SELECT " . trim($selector2, ", ") . " FROM {$db_prefix}photo_categories WHERE name NOT IN (SELECT name FROM {$db_prefix}video_categories) AND slug NOT IN (SELECT slug FROM {$db_prefix}video_categories)) AS X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		$new_channels = $this->query_num_source("SELECT count(*) FROM {$db_prefix}channel");
		if ($new_channels > 0)
		{
			return new KvsDataMigratorMigrationParams("content_source_group_id", "SELECT * FROM (SELECT network_id AS content_source_group_id, name AS title, slug AS dir, description, add_time AS added_date FROM {$db_prefix}channel_networks) X", self::OBJECT_TYPE_CONTENT_SOURCE_GROUP);
		}
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_CHANNELS_BASE_URL));

		$new_channels = $this->query_num_source("SELECT count(*) FROM {$db_prefix}channel");
		if ($new_channels > 0)
		{
			$selector = "channel_id AS content_source_id, network_id AS content_source_group_id, name AS title, slug AS dir, description, url, add_time AS added_date, total_likes * 5 AS rating, greatest(total_votes, 1) AS rating_amount, ";
			if ($base_url)
			{
				$selector .= "(CASE WHEN thumb!='' THEN concat(channel_id, '_1.', thumb) ELSE '' END) AS screenshot1, ";
				$selector .= "(CASE WHEN thumb!='' THEN concat('$base_url/', channel_id, '.thumb.', thumb) ELSE '' END) AS screenshot1_url, ";
				$selector .= "(CASE WHEN thumb!='' THEN concat(channel_id, '_2.', thumb) ELSE '' END) AS screenshot2, ";
				$selector .= "(CASE WHEN thumb!='' THEN concat('$base_url/', channel_id, '.thumb.', thumb) ELSE '' END) AS screenshot2_url, ";
			}
			return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM {$db_prefix}channel) X", self::OBJECT_TYPE_CONTENT_SOURCE);
		}
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT sponsor_id AS content_source_id, sponsor_name AS title, 1 AS rating_amount FROM {$db_prefix}video_sponsors) X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_MODELS_BASE_URL));

		$selector = "m.model_id, m.name AS title, b.aliases AS alias, m.slug AS dir, m.description, b.height, b.weight, b.hair_color AS hair, b.eye_color, b.measurements, m.gender, b.birth_date, b.age, m.rating * m.rated_by AS rating, greatest(m.rated_by, 1) AS rating_amount, m.total_views AS model_viewed, m.add_date AS added_date, b.url AS custom1, b.birth_location AS custom2, b.ethnicity AS custom3, b.performs AS custom4, b.nationality AS country, ";
		if ($base_url)
		{
			$selector .= "(CASE WHEN m.ext!='' THEN concat(m.model_id, '.', m.ext) ELSE '' END) AS screenshot1, ";
			$selector .= "(CASE WHEN m.ext!='' THEN concat('$base_url/', m.model_id, '.', m.ext) ELSE '' END) AS screenshot1_url, ";
		}

		$projector = "{$db_prefix}model m INNER JOIN {$db_prefix}model_bio b ON m.model_id=b.model_id";

		return new KvsDataMigratorMigrationParams("model_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM $projector) X", self::OBJECT_TYPE_MODEL);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$new_channels = trim($this->query_num_source("SELECT count(*) FROM {$db_prefix}channel"));

		$selector = "video_id, user_id, CASE WHEN $new_channels>0 THEN channel_id ELSE sponsor END AS content_source_id, title, description, rating * rated_by AS rating, greatest(rated_by, 1) AS rating_amount, round(duration) AS duration, embed_code AS embed, total_views AS video_viewed, CASE WHEN type='private' THEN 1 ELSE 0 END AS is_private, add_date AS added_date, add_date AS post_date, view_date AS last_time_view_date, locked AS is_locked, CASE WHEN status=1 THEN 1 ELSE 0 END AS status_id, url AS file_url, hd, mobile";
		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM {$db_prefix}video) X", self::OBJECT_TYPE_VIDEO);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "album_id, user_id, title, description, rating * rated_by AS rating, greatest(rated_by, 1) AS rating_amount, cover AS main_photo_id, total_views AS album_viewed, CASE WHEN type='private' THEN 1 ELSE 0 END AS is_private, add_date AS added_date, add_date AS post_date, view_date AS last_time_view_date, locked AS is_locked, CASE WHEN status='1' THEN 1 ELSE 0 END AS status_id";
		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM {$db_prefix}photo_albums) X", self::OBJECT_TYPE_ALBUM);
	}

	/**
	 * @param int $p_album_id
	 *
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_album_images_migration_params(int $p_album_id): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_PHOTOS_BASE_URL));
		if ($base_url == '')
		{
			$this->error("Photos base URL option is empty");
			return null;
		}

		return new KvsDataMigratorMigrationParams("photo_id", "SELECT photo_id AS image_id, concat('$base_url/', photo_id, '.', ext) AS image_url FROM {$db_prefix}photo WHERE album_id=$p_album_id ORDER BY photo_id ASC", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "";
		$selector .= "SELECT concat('video', c.comment_id) AS migrator_uid, c.video_id AS object_id, 1 AS object_type_id, v.title AS object_title, c.user_id, u.username AS username, CASE WHEN c.status='1' THEN 1 ELSE 0 END AS is_approved, c.comment, c.add_time AS added_date FROM {$db_prefix}video_comments c INNER JOIN {$db_prefix}video v ON c.video_id=v.video_id INNER JOIN {$db_prefix}user u ON c.user_id=u.user_id ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT concat('album', c.comment_id) AS migrator_uid, a.album_id AS object_id, 2 AS object_type_id, a.title AS object_title, c.user_id, u.username AS username, CASE WHEN c.status='1' THEN 1 ELSE 0 END AS is_approved, c.comment, c.add_time AS added_date FROM {$db_prefix}photo_comments c INNER JOIN {$db_prefix}photo p ON c.photo_id=p.photo_id INNER JOIN {$db_prefix}photo_albums a ON p.album_id=a.album_id INNER JOIN {$db_prefix}user u ON c.user_id=u.user_id ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT concat('model', c.comment_id) AS migrator_uid, c.model_id AS object_id, 4 AS object_type_id, m.name  AS object_title, c.user_id, u.username AS username, CASE WHEN c.status='1' THEN 1 ELSE 0 END AS is_approved, c.comment, c.add_time AS added_date FROM {$db_prefix}model_comments c INNER JOIN {$db_prefix}model m ON c.model_id=m.model_id INNER JOIN {$db_prefix}user u ON c.user_id=u.user_id ";

		return new KvsDataMigratorMigrationParams("comment_id", "SELECT * FROM ($selector) X", self::OBJECT_TYPE_COMMENT);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams(null, "SELECT video_id, user_id, now() AS added_date FROM {$db_prefix}video_favorites", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams(null, "SELECT album_id, user_id, now() AS added_date FROM (SELECT DISTINCT p.album_id, pf.user_id FROM {$db_prefix}photo_favorites pf INNER JOIN {$db_prefix}photo p ON pf.photo_id=p.photo_id) X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "f1.user_id, f1.friend_id, f1.add_date AS added_date, f2.add_date AS approved_date, CASE WHEN f2.request_id IS NULL THEN 0 ELSE 1 END AS is_approved";
		$projector = "{$db_prefix}user_friends f1 LEFT JOIN {$db_prefix}user_friends f2 ON f1.user_id=f2.friend_id AND f2.user_id=f1.friend_id";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE (f2.request_id IS NULL OR f2.request_id>f1.request_id) AND (f1.status='approved' OR f1.status='pending') AND (f2.status!='denied' OR f2.status IS NULL)) X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams(null, "SELECT msg_id AS message_id, sender_id AS user_from_id, receiver_id AS user_id, message, send_time AS added_date, CASE WHEN status=2 THEN 1 ELSE 0 END AS is_read, CASE WHEN status=2 THEN send_time ELSE NULL END AS read_date FROM {$db_prefix}message WHERE status in (1,2) ORDER BY msg_id ASC", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_subscriptions_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "";
		$selector .= "SELECT user_id AS subscribed_object_id, 1 AS subscribed_type_id, subscriber_id AS user_id, add_date AS added_date FROM {$db_prefix}user_subscriptions ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT model_id AS subscribed_object_id, 4 AS subscribed_type_id, user_id, now() AS added_date FROM {$db_prefix}model_favorites ";

		$new_channels = $this->query_num_source("SELECT count(*) FROM {$db_prefix}channel");
		if ($new_channels > 0)
		{
			$selector .= "UNION ALL ";
			$selector .= "SELECT channel_id AS subscribed_object_id, 3 AS subscribed_type_id, user_id, now() AS added_date FROM {$db_prefix}channel_subscribers ";
		}

		$new_models = $this->query_num_source("SELECT count(*) FROM {$db_prefix}model_subscribers");
		if ($new_models > 0)
		{
			$selector .= "UNION ALL ";
			$selector .= "SELECT model_id AS subscribed_object_id, 4 AS subscribed_type_id, user_id, now() AS added_date FROM {$db_prefix}model_subscribers ";
		}

		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM ($selector) X", 0);
	}
}