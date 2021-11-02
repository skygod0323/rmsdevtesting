<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromStp extends KvsDataMigrator
{
	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "stp";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Smart Tube Pro";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, true, false, true, true, true, true, true, true, true, true, false, true);
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
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$queries = array();

		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM stp_users";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM stp_categories WHERE parent_id=0";
			$queries[] = "SELECT count(*) FROM stp_categories WHERE parent_id in (SELECT category_id FROM stp_categories WHERE parent_id=0)";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM stp_sponsors";
			$queries[] = "SELECT count(*) FROM stp_paysites";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM stp_videos LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM stp_videos where removed=0";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM stp_galleries LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM stp_galleries where removed=0";
			}
		}
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM stp_videos_comments WHERE spam=0";
			$queries[] = "SELECT count(*) FROM stp_galleries_comments WHERE spam=0";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM stp_videos_favorites";
			$queries[] = "SELECT count(*) FROM stp_galleries_favorites";
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM stp_users_friends f1 LEFT JOIN stp_users_friends f2 ON f1.member=f2.friend AND f2.member=f1.friend WHERE f2.invite_date IS NULL OR f1.invite_date<f2.invite_date";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM stp_users_messages WHERE folder='inbox' AND sender_id!=receiver_id";
		}
		if ($this->data_to_migrate->is_playlists())
		{
			$queries[] = "SELECT count(*) FROM stp_videos_playlists";
			$queries[] = "SELECT count(*) FROM stp_playlists_videos";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$base_url = $this->query_text_source("SELECT images_url FROM stp_settings LIMIT 1");
		$base_path = $this->query_text_source("SELECT images_dir FROM stp_settings LIMIT 1");

		if ($base_url == "")
		{
			$this->error("Images URL setting is empty");
			return null;
		}
		if ($base_path == "")
		{
			$this->error("Images DIR setting is empty");
			return null;
		}
		$base_url = "$base_url/avatars";
		$base_path = "$base_path/avatars";

		$selector = "id AS user_id, ip, country, gender, dating_status AS relationship_status, orientation, CASE WHEN account_status='disabled' THEN 0 ELSE 2 END AS status_id, username, password AS pass, email, username AS display_name, birth_date, city, profile_text_2 AS interests, profile_text_3 AS favourite_movies, profile_text_4 AS favourite_music, profile_text_1 AS favourite_books, views AS profile_viewed, reg_date AS added_date, last_login AS last_login_date";
		$selector .= ", (CASE WHEN avatar!='' THEN concat(id, '.jpg') ELSE '' END) AS avatar";
		$selector .= ", (CASE WHEN avatar!='' THEN concat('$base_url/', avatar, '/', id, '.jpg') ELSE '' END) AS avatar_url";
		$selector .= ", (CASE WHEN avatar!='' THEN concat('$base_path/', avatar, '/', id, '.jpg') ELSE '' END) AS avatar_path";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM stp_users) X", self::OBJECT_TYPE_USER, function ($p_data) {
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
			} else
			{
				$p_data["last_login_date"] = "0000-00-00 00:00:00";
			}

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_category_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "category_id AS category_group_id, concat(ucase(left(name, 1)), substring(name, 2)) AS title, now() AS added_date";
		return new KvsDataMigratorMigrationParams("category_group_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM stp_categories WHERE parent_id=0) X", self::OBJECT_TYPE_CATEGORY_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "category_id, parent_id AS category_group_id, concat(ucase(left(name, 1)), substring(name, 2)) AS title, now() AS added_date";
		return new KvsDataMigratorMigrationParams("category_group_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM stp_categories WHERE parent_id in (SELECT category_id FROM stp_categories WHERE parent_id=0)) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS content_source_group_id, name AS title, now() AS added_date";
		return new KvsDataMigratorMigrationParams("content_source_group_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM stp_sponsors) X", self::OBJECT_TYPE_CONTENT_SOURCE_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS content_source_id, sponsor_id AS content_source_group_id, name AS title, now() AS added_date, ";
		$selector .= "(SELECT html FROM stp_links WHERE paysite_id=stp_paysites.id LIMIT 1) AS link_code";
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM stp_paysites) X", self::OBJECT_TYPE_CONTENT_SOURCE, function ($p_data) {
			$link_code = $p_data["link_code"];
			preg_match("|href\ *=[\ '\"]*([^\ '\">]+)[\ '\">]*|is", $link_code, $temp);
			if ($temp[1] != "")
			{
				$p_data["url"] = $temp[1];
			}
			unset($p_data["link_code"]);
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS video_id, user_id, paysite_id AS content_source_id, title, description, cache_id AS dir, rating/20 * rating_count AS rating, greatest(rating_count, 1) AS rating_amount, runtime AS duration, views AS video_viewed, ip, CASE type WHEN 'membership' THEN 2 WHEN 'private' THEN 1 ELSE 0 END AS is_private, approval_date AS added_date, approval_date AS post_date, CASE WHEN approved=1 THEN 1 ELSE 0 END AS status_id, tags, category_1, category_2, category_3, thumbs_count AS screen_amount, default_thumb AS screen_main, files, videos_data, subdir";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM stp_videos WHERE removed=0) X", self::OBJECT_TYPE_VIDEO, function ($p_data, mysqli $mysql_link) {
			$video_id = intval($p_data["video_id"]);

			if ($p_data["title"] != "")
			{
				$map_translit = array();
				$map_default = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
				for ($i = 0; $i < strlen($map_default); $i++)
				{
					$map_translit[$map_default[$i]] = strtolower($map_default[$i]);
				}

				$directory = "";
				$characters = preg_split("//u", $p_data["title"], -1, PREG_SPLIT_NO_EMPTY);
				for ($i = 0; $i < count($characters); $i++)
				{
					if (isset($map_translit[$characters[$i]]))
					{
						$directory .= $map_translit[$characters[$i]];
					} else
					{
						$directory .= " ";
					}
				}

				$directory = preg_replace("|\ {1,999}|is", "-", $directory);
				$directory = trim($directory, "-");
				$p_data["dir"] = "$directory-$p_data[dir]";
			}

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

			$categories = array(intval($p_data["category_1"]), intval($p_data["category_2"]), intval($p_data["category_3"]));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$mysql_result = $mysql_link->query("SELECT concat(ucase(left(name, 1)), substring(name, 2)) FROM stp_categories WHERE category_id=" . intval($category_id));
					if ($mysql_result)
					{
						$row = $mysql_result->fetch_row();
						$new_categories[] = trim($row[0]);
					}
				}
			}
			$p_data["categories"] = implode("||", $new_categories);

			unset($p_data["category_1"], $p_data["category_2"], $p_data["category_3"]);

			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);

			if ($p_data["files"] != "")
			{
				$files = @unserialize($p_data["files"], ['allowed_classes' => false]);
				if (is_array($files))
				{
					$take_format_id = null;
					$take_format_size = 0;
					foreach ($files as $format_id => $format_info)
					{
						if (strpos($format_info["ext"], "mp4") !== false)
						{
							if (!$take_format_id)
							{
								$take_format_id = $format_id;
							}
							if (!$take_format_size)
							{
								$take_format_size = $format_info["size"];
							}
							if ($format_info["size"] > $take_format_size)
							{
								$take_format_id = $format_id;
							}
						}
					}

					if ($take_format_id)
					{
						$video_url = "";
						$mysql_result = $mysql_link->query("SELECT media_url FROM stp_videos_storage WHERE id=" . intval($files[$take_format_id]["storage_id"]));
						if ($mysql_result)
						{
							$row = $mysql_result->fetch_row();
							$video_url = rtrim(trim($row[0]), "/");
						}
						if ($p_data["subdir"] != "")
						{
							$video_url .= "/$p_data[subdir]";
						}
						$video_url .= "/$video_id/$take_format_id." . $files[$take_format_id]["ext"];
						$p_data["file_download_url"] = $video_url;
					}
				}
			} elseif ($p_data["videos_data"] != "")
			{
				$files = @unserialize($p_data["videos_data"], ['allowed_classes' => false]);
				if (is_array($files) && count($files) > 0)
				{
					$p_data["file_url"] = $files[0];
				}
			}

			$base_url = "";
			$base_path = "";

			$mysql_result = $mysql_link->query("SELECT images_url FROM stp_settings LIMIT 1");
			if ($mysql_result)
			{
				$row = $mysql_result->fetch_row();
				$base_url = rtrim(trim($row[0]), "/");
			}

			$mysql_result = $mysql_link->query("SELECT images_dir FROM stp_settings LIMIT 1");
			if ($mysql_result)
			{
				$row = $mysql_result->fetch_row();
				$base_path = rtrim(trim($row[0]), "/");
			}

			$screenshot_urls = array();
			if ($base_url != "")
			{
				$base_url = "$base_url/videos";
				if ($p_data["subdir"] != "")
				{
					$base_url .= "/$p_data[subdir]";
				}
				for ($i = 1; $i <= $p_data["screen_amount"]; $i++)
				{
					$screenshot_urls[] = "$base_url/$video_id/$i.jpg";
				}
				$p_data["screen_urls"] = $screenshot_urls;
			}

			$screenshot_paths = array();
			if ($base_path != "")
			{
				$base_path = "$base_path/videos";
				if ($p_data["subdir"] != "")
				{
					$base_path .= "/$p_data[subdir]";
				}
				for ($i = 1; $i <= $p_data["screen_amount"]; $i++)
				{
					$screenshot_paths[] = "$base_path/$video_id/$i.jpg";
				}
				$p_data["screen_paths"] = $screenshot_paths;
			}

			unset($p_data["files"], $p_data["videos_data"], $p_data["subdir"]);

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "g.id AS album_id, g.user_id, g.paysite_id AS content_source_id, g.title, g.description, g.cache_id AS dir, g.rating/20 * g.rating_count AS rating, greatest(g.rating_count, 1) AS rating_amount, g.views AS album_viewed, g.ip, CASE g.type WHEN 'membership' THEN 2 WHEN 'private' THEN 1 ELSE 0 END AS is_private, g.approval_date AS added_date, g.approval_date AS post_date, CASE WHEN g.approved=1 THEN 1 ELSE 0 END AS status_id, g.tags, g.category_1, g.category_2, g.category_3, r.rank2 AS main_photo_id";
		$projector = "stp_galleries g LEFT JOIN (SELECT r.gallery_id, r.rank2 FROM (SELECT id, gallery_id, picture, @curRank := CASE WHEN @gallery != gallery_id THEN 1 ELSE @curRank + 1 END AS rank2, @gallery := gallery_id FROM stp_galleries_pictures, (SELECT @curRank := 0) r, (SELECT @gallery := 0) g ORDER BY id ASC) r INNER JOIN stp_galleries g ON r.gallery_id=g.id AND g.default_thumb=r.picture) r ON g.id=r.gallery_id";

		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM $projector WHERE g.removed=0) X", self::OBJECT_TYPE_ALBUM, function ($p_data, mysqli $mysql_link) {
			if ($p_data["title"] != "")
			{
				$map_translit = array();
				$map_default = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
				for ($i = 0; $i < strlen($map_default); $i++)
				{
					$map_translit[$map_default[$i]] = strtolower($map_default[$i]);
				}

				$directory = "";
				$characters = preg_split("//u", $p_data["title"], -1, PREG_SPLIT_NO_EMPTY);
				for ($i = 0; $i < count($characters); $i++)
				{
					if (isset($map_translit[$characters[$i]]))
					{
						$directory .= $map_translit[$characters[$i]];
					} else
					{
						$directory .= " ";
					}
				}

				$directory = preg_replace("|\ {1,999}|is", "-", $directory);
				$directory = trim($directory, "-");
				$p_data["dir"] = "$directory-$p_data[dir]";
			}

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

			$categories = array(intval($p_data["category_1"]), intval($p_data["category_2"]), intval($p_data["category_3"]));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$mysql_result = $mysql_link->query("SELECT concat(ucase(left(name, 1)), substring(name, 2)) FROM stp_categories WHERE category_id=" . intval($category_id));
					if ($mysql_result)
					{
						$row = $mysql_result->fetch_row();
						$new_categories[] = trim($row[0]);
					}
				}
			}
			$p_data["categories"] = implode("||", $new_categories);

			unset($p_data["category_1"], $p_data["category_2"], $p_data["category_3"]);

			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);

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
		return new KvsDataMigratorMigrationParams("image_id", "SELECT id AS image_id, concat((SELECT media_url FROM stp_galleries_storage LIMIT 1), '/', subdir, '/', gallery_id, '/', picture, '.jpg') AS image_url FROM stp_galleries_pictures WHERE gallery_id=$p_album_id ORDER BY id ASC", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "";
		$selector .= "SELECT concat('video', c.id) AS migrator_uid, c.video_id AS object_id, 1 AS object_type_id, v.title AS object_title, c.poster_id AS user_id, c.poster_name AS username, 1 is_approved, c.comment, c.post_date AS added_date FROM stp_videos_comments c INNER JOIN stp_videos v ON c.video_id=v.id WHERE c.spam=0 ";
		$selector .= "UNION ALL ";
		$selector .= "SELECT concat('album', c.id) AS migrator_uid, c.gallery_id AS object_id, 2 AS object_type_id, g.title AS object_title, c.poster_id AS user_id, c.poster_name AS username, 1 is_approved, c.comment, c.post_date AS added_date FROM stp_galleries_comments c INNER JOIN stp_galleries g ON c.gallery_id=g.id WHERE c.spam=0 ";

		return new KvsDataMigratorMigrationParams("comment_id", "SELECT * FROM ($selector) X", self::OBJECT_TYPE_COMMENT, function ($p_data) {
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
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT video AS video_id, user AS user_id, now() AS added_date FROM stp_videos_favorites", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT gallery AS album_id, user AS user_id, now() AS added_date FROM stp_galleries_favorites", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "f1.member AS user_id, f1.friend AS friend_id, f1.invite_date AS added_date, CASE WHEN f2.invite_date IS NOT NULL THEN f2.invite_date ELSE 0 END AS approved_date, CASE WHEN f1.status='approved' THEN 1 ELSE 0 END AS is_approved";
		$projector = "stp_users_friends f1 LEFT JOIN stp_users_friends f2 ON f1.member=f2.friend AND f2.member=f1.friend";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE f2.invite_date IS NULL OR f1.invite_date<f2.invite_date) X", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["approved_date"] > 0)
			{
				$p_data["approved_date"] = date("Y-m-d H:i:s", $p_data["approved_date"]);
			} else
			{
				$p_data["approved_date"] = "0000-00-00 00:00:00";
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS message_id, body AS message, sender_id AS user_from_id, receiver_id AS user_id, sent_date AS added_date, seen AS is_read";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM stp_users_messages WHERE folder='inbox' AND sender_id!=receiver_id) X", 0, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["is_read"] == 1)
			{
				$p_data["read_date"] = date("Y-m-d H:i:s");
			} else
			{
				$p_data["read_date"] = "0000-00-00 00:00:00";
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_playlists_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "p.id AS playlist_id, p.user_id, p.name AS title, p.description, lower(p.url_key) AS dir, p.views AS playlist_viewed, 1 AS status_id, p.tags, u.reg_date AS added_date, u.reg_date+3600 AS last_content_date";
		return new KvsDataMigratorMigrationParams("playlist_id", "SELECT * FROM (SELECT $selector FROM stp_videos_playlists p INNER JOIN stp_users u ON p.user_id=u.id) X", self::OBJECT_TYPE_PLAYLIST, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			if ($p_data["last_content_date"] > 0)
			{
				$p_data["last_content_date"] = date("Y-m-d H:i:s", $p_data["last_content_date"]);
			} else
			{
				$p_data["last_content_date"] = date("Y-m-d H:i:s");
			}

			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);
			return $p_data;
		});
	}

	/**
	 * @param int $p_playlist_id
	 *
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_playlist_videos_migration_params(int $p_playlist_id): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT 10 AS fav_type, pv.playlist AS playlist_id, pv.video AS video_id, p.user_id, now() AS added_date FROM stp_playlists_videos pv INNER JOIN stp_videos_playlists p ON pv.playlist=p.id WHERE p.id=$p_playlist_id", 0);
	}
}