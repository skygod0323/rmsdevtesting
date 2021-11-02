<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromAsp3 extends KvsDataMigrator
{
	public const OPT_DB_PREFIX = "asp3_db_prefix";
	public const OPT_USERS_BASE_URL = "asp3_users_base_url"; // http://domain.com/media/users
	public const OPT_CATEGORIES_VIDEOS_BASE_URL = "asp3_categories_videos_base_url"; // http://domain.com/media/videos/cat
	public const OPT_CATEGORIES_PHOTOS_BASE_URL = "asp3_categories_photos_base_url"; // http://domain.com/media/photos/cat
	public const OPT_MODELS_BASE_URL = "asp3_models_base_url"; // http://domain.com/media/pornstars
	public const OPT_CHANNELS_BASE_URL = "asp3_channels_base_url"; // http://domain.com/media/channels
	public const OPT_VIDEOS_BASE_URL = "asp3_videos_base_url"; // http://domain.com/media/videos/mp4
	public const OPT_PHOTOS_BASE_URL = "asp3_photos_base_url"; // http://domain.com/media/photos/orig
	public const OPT_SCREEN_BASE_URL = "asp3_screen_base_url"; // http://domain.com/media/videos/tmb

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "asp3";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Adult Script Pro v3";
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

		if ($p_object_type == self::OBJECT_TYPE_CATEGORY)
		{
			if ($p_object["synonyms"])
			{
				$synonyms = array_map("trim", explode(",", $p_object["synonyms"]));
				foreach ($synonyms as $k => $synonym)
				{
					if ($synonym == "" || $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}categories WHERE title='" . $this->escape_string($synonym) . "'") > 0)
					{
						unset($synonyms[$k]);
					}
				}
				$p_object["synonyms"] = implode(", ", $synonyms);
			}
		} elseif ($p_object_type == self::OBJECT_TYPE_CONTENT_SOURCE)
		{
			$content_source_id = intval($p_object["content_source_id"]);

			$p_object["tags"] = $this->query_text_source("SELECT group_concat(t.tag SEPARATOR '||') FROM {$db_prefix}tags t INNER JOIN {$db_prefix}channel_tags ct ON t.tag_id=ct.tag_id WHERE ct.channel_id=$content_source_id GROUP BY ct.channel_id");
		} elseif ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$video_id = intval($p_object["video_id"]);

			$p_object["categories"] = $this->query_text_source("SELECT group_concat(c.name SEPARATOR '||') FROM {$db_prefix}video_category vc INNER JOIN {$db_prefix}video_categories c ON c.cat_id=vc.cat_id WHERE vc.video_id=$video_id GROUP BY vc.video_id");
			$p_object["models"] = $this->query_text_source("SELECT group_concat(m.name SEPARATOR '||') FROM {$db_prefix}model_videos vm INNER JOIN {$db_prefix}model m ON m.model_id=vm.model_id WHERE vm.video_id=$video_id GROUP BY vm.video_id");
			$p_object["tags"] = $this->query_text_source("SELECT group_concat(t.tag SEPARATOR '||') FROM {$db_prefix}video_tags vt INNER JOIN {$db_prefix}tags t ON t.tag_id=vt.tag_id WHERE vt.video_id=$video_id GROUP BY vt.video_id");

			$file_download_urls = array();
			$max_filesize = 0;

			$video_data_result = $this->query_source("SELECT vs.base_url, v.video_id, vf.postfix, vf.resolution, vf.filesize FROM {$db_prefix}video v LEFT JOIN {$db_prefix}video_files vf ON v.video_id=vf.video_id LEFT JOIN {$db_prefix}server vs ON v.server_id=vs.server_id WHERE v.video_id=$video_id AND ext='mp4'");
			if ($video_data_result)
			{
				for ($i = 0; $i < $video_data_result->num_rows; $i++)
				{
					$video_data_result->data_seek($i);
					$video_data_item = $video_data_result->fetch_assoc();
					if (strlen($video_data_item["base_url"]) < 5)
					{
						$video_data_item["base_url"] = trim($this->get_option_value(self::OPT_VIDEOS_BASE_URL));
					} else
					{
						$video_data_item["base_url"] = trim($video_data_item["base_url"], "/") . "/media/videos";
					}
					if (strlen($video_data_item["base_url"]) > 10)
					{
						$base_url = trim($video_data_item["base_url"], "/");
						if ($video_data_item["postfix"] == "")
						{
							$file_download_urls[] = array("postfix" => ".mp4", "url" => "$base_url/mp4/$video_id.mp4", "filesize" => $video_data_item["filesize"]);
						} elseif ($this->query_num_target("SELECT count(*) from {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='_{$video_data_item['resolution']}.mp4'") == 1)
						{
							$file_download_urls[] = array("postfix" => "_{$video_data_item['resolution']}.mp4", "url" => "$base_url/mp4/$video_id{$video_data_item['postfix']}.mp4", "filesize" => $video_data_item["filesize"]);
						}
					}
					if ($video_data_item["filesize"] > $max_filesize)
					{
						$max_filesize = $video_data_item["filesize"];
					}
				}
			}

			if (count($file_download_urls) > 0)
			{
				$has_source = false;
				foreach ($file_download_urls as $k => $v)
				{
					if ($v["filesize"] == $max_filesize)
					{
						$file_download_urls[$k]["is_source"] = 1;
						$has_source = true;
					}
				}

				if (!$has_source && count($file_download_urls) > 0)
				{
					$file_download_urls[0]["is_source"] = 1;
				}
				$p_object["file_download_urls"] = $file_download_urls;
			}

		} elseif ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$album_id = intval($p_object["album_id"]);

			$p_object["categories"] = $this->query_text_source("SELECT group_concat(c.name SEPARATOR '||') FROM {$db_prefix}photo_category ac INNER JOIN {$db_prefix}photo_categories c ON c.cat_id=ac.cat_id WHERE ac.album_id=$album_id GROUP BY ac.album_id");
			$p_object["tags"] = $this->query_text_source("SELECT group_concat(t.tag SEPARATOR '||') FROM {$db_prefix}photo_tags pt INNER JOIN {$db_prefix}tags t ON t.tag_id=pt.tag_id WHERE pt.album_id=$album_id GROUP BY pt.album_id");
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
			$queries[] = "SELECT count(*) FROM {$db_prefix}channel";
			$queries[] = "SELECT count(*) FROM {$db_prefix}channel_networks";
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
				$queries[] = "SELECT count(*) FROM {$db_prefix}video WHERE `status` IN (0, 1, 2, 8)";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$db_prefix}photo_albums LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$db_prefix}photo_albums WHERE `status` IN (0, 1, 2, 8)";
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
			$queries[] = "SELECT count(*) FROM {$db_prefix}user_friends f1 LEFT JOIN {$db_prefix}user_friends f2 ON f1.user_id=f2.friend_id AND f2.user_id=f1.friend_id WHERE (f2.id IS NULL OR f2.id>f1.id) AND (f1.status=0 OR f1.status=1) AND (f2.status!=2 OR f2.status IS NULL)";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}message WHERE status in (1,2)";
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$queries[] = "SELECT count(*) FROM {$db_prefix}model_subscribers";
			$queries[] = "SELECT count(*) FROM {$db_prefix}user_subscribers";
			$queries[] = "SELECT count(*) FROM {$db_prefix}channel_subscribers";
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

		$selector = "u.user_id, u.join_ip AS ip, c.countryName as country, CASE u.gender WHEN 1 THEN 'male' WHEN 2 THEN 'female' WHEN 3 THEN 'trans' END AS gender, CASE u.relation WHEN 1 THEN 'single' WHEN 2 THEN 'taken' WHEN 3 THEN 'open' END AS relationship_status, CASE u.interested WHEN 1 THEN 'straight' WHEN 2 THEN 'gay' WHEN 3 THEN 'not_sure' END AS orientation, CASE WHEN u.status='1' THEN 2 ELSE 0 END AS status_id, u.username, u.password AS pass, u.email, CASE WHEN u.name='' THEN u.username ELSE u.name END AS display_name, u.birth_time AS birth_date, u.city, u.login_time AS last_login_date, CASE WHEN u.join_time=0 THEN (SELECT min(join_time) FROM {$db_prefix}user WHERE join_time>0) ELSE u.join_time END AS added_date, u.popularity AS profile_viewed, p.website, p.school AS education, p.occupation, p.about AS about_me, p.hobbies AS interests, p.movies AS favourite_movies, p.music AS favourite_music, p.books AS favourite_books, ";

		if ($base_url)
		{
			$selector .= "(CASE WHEN avatar!='' THEN concat(u.user_id, '.', avatar) ELSE '' END) AS avatar, ";
			$selector .= "(CASE WHEN avatar!='' THEN concat('$base_url/', u.user_id, '.', avatar) ELSE '' END) AS avatar_url, ";
		}

		$projector = "{$db_prefix}user u INNER JOIN {$db_prefix}user_profile p ON u.user_id=p.user_id LEFT JOIN {$db_prefix}countries c on u.country_id=c.country_id WHERE u.username!='anonymous' and u.user_id!=2";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM $projector) X", self::OBJECT_TYPE_USER, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["last_login_date"] > 0)
			{
				$p_data["last_login_date"] = date("Y-m-d H:i:s", $p_data["last_login_date"]);
				$p_data["last_online_date"] = date("Y-m-d H:i:s", $p_data["last_login_date"]);
			} else
			{
				$p_data["last_login_date"] = '0000-00-00 00:00:00';
				$p_data["last_online_date"] = '0000-00-00 00:00:00';
			}
			$p_data["ip"] = ip2int($p_data["ip"]);
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_CATEGORIES_VIDEOS_BASE_URL));

		$selector1 = "cat_id AS category_id, name AS title, slug AS dir, auto_terms AS synonyms, description, add_time AS added_date, meta_title AS custom1, meta_desc AS custom2, meta_keys AS custom4, ";
		if ($base_url)
		{
			$selector1 .= "concat(cat_id, '.jpg') AS screenshot1, ";
			$selector1 .= "concat('$base_url/', cat_id, '.', ext) AS screenshot1_url, ";
		}

		$base_url = trim($this->get_option_value(self::OPT_CATEGORIES_PHOTOS_BASE_URL));

		$selector2 = "100 + cat_id AS category_id, name AS title, slug AS dir, auto_terms AS synonyms, description, add_time AS added_date, meta_title AS custom1, meta_desc AS custom2, meta_keys AS custom4, ";
		if ($base_url)
		{
			$selector2 .= "concat((100 + cat_id), '.jpg') AS screenshot1, ";
			$selector2 .= "concat('$base_url/', cat_id, '.', ext) AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT " . trim($selector1, ", ") . " FROM {$db_prefix}video_categories UNION ALL SELECT " . trim($selector2, ", ") . " FROM {$db_prefix}photo_categories WHERE name NOT IN (SELECT name FROM {$db_prefix}video_categories) AND slug NOT IN (SELECT slug FROM {$db_prefix}video_categories)) AS X", self::OBJECT_TYPE_CATEGORY, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams("content_source_group_id", "SELECT * FROM (SELECT network_id AS content_source_group_id, name AS title, slug AS dir, description, add_time AS added_date FROM {$db_prefix}channel_networks) X", self::OBJECT_TYPE_CONTENT_SOURCE_GROUP, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_CHANNELS_BASE_URL));

		$selector = "channel_id AS content_source_id, network_id AS content_source_group_id, name AS title, slug AS dir, description, url, add_time AS added_date, likes * 5 AS rating, greatest(rated_by, 1) AS rating_amount, total_views AS cs_viewed, ";
		if ($base_url)
		{
			$selector .= "(CASE WHEN thumb!='' THEN concat(channel_id, '_1.', thumb) ELSE '' END) AS screenshot1, ";
			$selector .= "(CASE WHEN thumb!='' THEN concat('$base_url/', channel_id, '.', thumb) ELSE '' END) AS screenshot1_url, ";
			$selector .= "(CASE WHEN thumb!='' THEN concat(channel_id, '_2.', thumb) ELSE '' END) AS screenshot2, ";
			$selector .= "(CASE WHEN thumb!='' THEN concat('$base_url/', channel_id, '.', thumb) ELSE '' END) AS screenshot2_url, ";
		}
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM {$db_prefix}channel) X", self::OBJECT_TYPE_CONTENT_SOURCE, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$base_url = trim($this->get_option_value(self::OPT_MODELS_BASE_URL));

		$selector = "m.model_id, m.name AS title, m.auto_terms AS alias, m.slug AS dir, m.description, b.height, b.weight, b.measurements, CASE m.gender WHEN 2 THEN 'female' WHEN 3 THEN 'other' ELSE 'male' END AS gender, CASE m.eye_color WHEN 1 THEN 'blue' WHEN 2 THEN 'brown' WHEN 3 THEN 'green' WHEN 4 THEN 'grey' WHEN 5 THEN 'hazel' WHEN 6 THEN 'other' END AS eye_color, CASE m.hair_color WHEN 1 THEN 'auburn' WHEN 2 THEN 'bald' WHEN 3 THEN 'black' WHEN 4 THEN 'blonde' WHEN 5 THEN 'brown' WHEN 6 THEN 'brunette' WHEN 7 THEN 'gray' WHEN 8 THEN 'red' WHEN 9 THEN 'other' END AS hair, b.birth_date, CASE WHEN m.age > 100 THEN 0 ELSE m.age END AS age, m.likes * 5 AS rating, greatest(m.rated_by, 1) AS rating_amount, m.total_views AS model_viewed, m.add_time AS added_date, c.countryName as country, b.url AS custom1, b.nationality AS custom2, CASE m.ethnicity WHEN 1 THEN 'asian' WHEN 2 THEN 'black' WHEN 3 THEN 'creole' WHEN 4 THEN 'indian' WHEN 5 THEN 'latin' WHEN 6 THEN 'middle-eastern' WHEN 7 THEN 'native' WHEN 8 THEN 'white' WHEN 9 THEN 'caucasian' WHEN 10 THEN 'other' END AS custom3, ";
		if ($base_url)
		{
			$selector .= "(CASE WHEN m.ext!='' THEN concat(m.model_id, '.', m.ext) ELSE '' END) AS screenshot1, ";
			$selector .= "(CASE WHEN m.ext!='' THEN concat('$base_url/', m.model_id, '.', m.ext) ELSE '' END) AS screenshot1_url, ";
		}

		$projector = "{$db_prefix}model m LEFT JOIN {$db_prefix}model_bio b ON m.model_id=b.model_id LEFT JOIN {$db_prefix}countries c on m.country_id=c.country_id";

		return new KvsDataMigratorMigrationParams("model_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM $projector) X", self::OBJECT_TYPE_MODEL, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "video_id, user_id, channel_id AS content_source_id, title, description, likes * 5 AS rating, greatest(rated_by, 1) AS rating_amount, round(duration) AS duration, embed_code AS embed, total_views AS video_viewed, CASE WHEN type=1 THEN 1 ELSE 0 END AS is_private, add_time AS added_date, add_time AS post_date, view_time AS last_time_view_date, locked AS is_locked, CASE `status` WHEN 1 THEN 1 WHEN 8 THEN 5 ELSE 0 END AS status_id";
		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM {$db_prefix}video WHERE `status` IN (0, 1, 2, 8)) X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["post_date"] > 0)
			{
				$p_data["post_date"] = date("Y-m-d H:i:s", $p_data["post_date"]);
			} else
			{
				$p_data["post_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["last_time_view_date"] > 0)
			{
				$p_data["last_time_view_date"] = date("Y-m-d H:i:s", $p_data["last_time_view_date"]);
			} else
			{
				$p_data["last_time_view_date"] = "0000-00-00 00:00:00";
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "album_id, user_id, channel_id AS content_source_id, title, description, likes * 5 AS rating, greatest(rated_by, 1) AS rating_amount, cover_id AS main_photo_id, total_views AS album_viewed, CASE WHEN type=1 THEN 1 ELSE 0 END AS is_private, add_time AS added_date, add_time AS post_date, view_time AS last_time_view_date, locked AS is_locked, CASE `status` WHEN 1 THEN 1 WHEN 8 THEN 5 ELSE 0 END AS status_id";
		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM {$db_prefix}photo_albums WHERE `status` IN (0, 1, 2, 8)) X", self::OBJECT_TYPE_ALBUM, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["post_date"] > 0)
			{
				$p_data["post_date"] = date("Y-m-d H:i:s", $p_data["post_date"]);
			} else
			{
				$p_data["post_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["last_time_view_date"] > 0)
			{
				$p_data["last_time_view_date"] = date("Y-m-d H:i:s", $p_data["last_time_view_date"]);
			} else
			{
				$p_data["last_time_view_date"] = "0000-00-00 00:00:00";
			}
			return $p_data;
		});
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
		if ($base_url == "")
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
		return new KvsDataMigratorMigrationParams(null, "SELECT video_id, user_id, favorite_time AS added_date FROM {$db_prefix}video_favorites", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams(null, "SELECT album_id, user_id, added_date FROM (SELECT DISTINCT p.album_id, pf.user_id, pf.favorite_time AS added_date FROM {$db_prefix}photo_favorites pf INNER JOIN {$db_prefix}photo p ON pf.photo_id=p.photo_id) X", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "f1.user_id, f1.friend_id, f1.add_time AS added_date, f2.add_time AS approved_date, CASE WHEN f2.id IS NULL THEN 0 ELSE 1 END AS is_approved";
		$projector = "{$db_prefix}user_friends f1 LEFT JOIN {$db_prefix}user_friends f2 ON f1.user_id=f2.friend_id AND f2.user_id=f1.friend_id";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE (f2.id IS NULL OR f2.id>f1.id) AND (f1.status=0 OR f1.status=1) AND (f2.status!=2 OR f2.status IS NULL)) X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));
		return new KvsDataMigratorMigrationParams(null, "SELECT msg_id AS message_id, sender_id AS user_from_id, receiver_id AS user_id, message, send_time AS added_date, CASE WHEN status=2 THEN 1 ELSE 0 END AS is_read, CASE WHEN status=2 THEN send_time ELSE NULL END AS read_date FROM {$db_prefix}message WHERE status in (1,2) ORDER BY msg_id ASC", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_subscriptions_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$db_prefix = trim($this->get_option_value(self::OPT_DB_PREFIX));

		$selector = "";
		$selector .= "SELECT user_id AS subscribed_object_id, 1 AS subscribed_type_id, subscriber_id AS user_id, subscribe_time AS added_date FROM {$db_prefix}user_subscribers ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT model_id AS subscribed_object_id, 4 AS subscribed_type_id, user_id, subscribe_time AS added_date FROM {$db_prefix}model_subscribers ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT channel_id AS subscribed_object_id, 3 AS subscribed_type_id, user_id, subscribe_time AS added_date FROM {$db_prefix}channel_subscribers ";

		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM ($selector) X", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}
}