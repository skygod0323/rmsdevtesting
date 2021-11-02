<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromDle extends KvsDataMigrator
{
	public const OPT_TABLES_PREFIX = "dle_tables_prefix";
	public const OPT_USERS_BASE_URL = "dle_users_base_url";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "dle";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "DLE";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, false, false, true, true, true, false, true, false, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_TABLES_PREFIX, self::OPT_USERS_BASE_URL);
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return array
	 */
	protected function pre_process_each_object_hook(array $p_object, int $p_object_type): array
	{
		$p_object = parent::pre_process_each_object_hook($p_object, $p_object_type);

		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		if ($p_object_type == self::OBJECT_TYPE_USER)
		{
			$users_base_url = $this->get_option_value(self::OPT_USERS_BASE_URL);
			if ($p_object["avatar"] != "")
			{
				if (strpos($p_object["avatar"], "//") === 0 || strpos($p_object["avatar"], "http://") === 0 || strpos($p_object["avatar"], "https://") === 0)
				{
					$p_object["avatar"] = end(explode("/", $p_object["avatar"]));
				}
				if ($users_base_url)
				{
					$p_object["avatar_url"] = "$users_base_url/$p_object[avatar]";
				} else
				{
					unset($p_object["avatar"]);
				}
			}
		} elseif ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$p_object["user_id"] = $this->query_num_source("SELECT user_id FROM {$tables_prefix}users WHERE name='" . $this->escape_string($p_object["user_id"]) . "' LIMIT 1");

			$p_object["tags"] = implode("||", array_map("trim", explode(",", $p_object["tags"])));

			$categories = array_map("trim", explode(",", $p_object["categories"]));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM {$tables_prefix}category WHERE id=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);

			unset($temp);
			preg_match("/<\!--dle_video_begin:([^>]+mp4)-->/i", $p_object["full_story"], $temp);
			if ($temp[1])
			{
				$p_object["file_url"] = $temp[1];
			}

			$p_object["description"] = trim(strip_tags($p_object["full_story"]));

			unset($temp);
			preg_match("/<\!--dle_image_begin:([^|]+)|/i", $p_object["short_story"], $temp);
			if ($temp[1])
			{
				$p_object["screen_url"] = $temp[1];
			} else
			{
				unset($temp);
				preg_match("/<\!--TBegin:([^|]+)|/i", $p_object["short_story"], $temp);
				if ($temp[1])
				{
					$p_object["screen_url"] = $temp[1];
				} else
				{
					unset($temp);
					preg_match("/<\!--MBegin:([^|]+)|/i", $p_object["short_story"], $temp);
					if ($temp[1])
					{
						$p_object["screen_url"] = $temp[1];
					} else
					{
						echo "No screenshot for video $p_object[video_id]\n";
					}
				}
			}

			unset($p_object["full_story"], $p_object["short_story"]);
		} elseif ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$p_object["user_id"] = $this->query_num_source("SELECT user_id FROM {$tables_prefix}users WHERE name='" . $this->escape_string($p_object["user_id"]) . "' LIMIT 1");

			$p_object["tags"] = implode("||", array_map("trim", explode(",", $p_object["tags"])));

			$categories = array_map("trim", explode(",", $p_object["categories"]));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM {$tables_prefix}category WHERE id=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);

			$p_object["description"] = trim(strip_tags($p_object["full_story"]));

			unset($p_object["full_story"]);
		}

		return $p_object;
	}

	/**
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$queries = array();

		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return $queries;
		}

		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}category";
		}
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}users";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$tables_prefix}post LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$tables_prefix}post WHERE full_story LIKE '%<!--dle_video_begin%'";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$tables_prefix}post LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$tables_prefix}post WHERE full_story NOT LIKE '%<!--dle_video_begin%' AND full_story LIKE '%<!--MBegin%'";
			}
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		$selector = "id AS category_id, name AS title, alt_name AS dir, descr AS description, now() AS added_date, keywords AS custom4, metatitle AS custom1";

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}category c1 WHERE NOT EXISTS (SELECT id FROM {$tables_prefix}category c2 WHERE c1.name=c2.name AND c1.id>c2.id)) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		$selector = "user_id, email AS username, email, md5(password) AS pass, name AS display_name, lastdate AS last_login_date, reg_date AS added_date, info AS about_me, land AS city, foto AS avatar, logged_ip AS ip, 2 AS status_id";
		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}users) X", self::OBJECT_TYPE_USER, function ($p_data) {
			$p_data["ip"] = ip2int($p_data["ip"]);

			if ($p_data["last_login_date"] > 0)
			{
				$p_data["last_login_date"] = date("Y-m-d H:i:s", $p_data["last_login_date"]);
			} else
			{
				$p_data["last_login_date"] = "0000-00-00 00:00:00";
			}
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = "0000-00-00 00:00:00";
			}

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		$selector = "p.id AS video_id, p.autor AS user_id, p.date AS added_date, p.date AS post_date, CASE WHEN p.approve=0 THEN 0 ELSE 1 END AS status_id, p.title, p.descr AS description, p.keywords AS tags, p.category AS categories, p.alt_name AS dir, pe.news_read AS video_viewed, pe.rating AS rating, greatest(1, pe.vote_num) AS rating_amount, short_story, full_story";
		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}post p LEFT JOIN {$tables_prefix}post_extras pe ON p.id=pe.news_id WHERE p.full_story LIKE '%<!--dle_video_begin%') X", self::OBJECT_TYPE_VIDEO);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		$selector = "p.id AS album_id, p.autor AS user_id, p.date AS added_date, p.date AS post_date, CASE WHEN p.approve=0 THEN 0 ELSE 1 END AS status_id, p.title, p.descr AS description, p.keywords AS tags, p.category AS categories, p.alt_name AS dir, pe.news_read AS album_viewed, pe.rating AS rating, greatest(1, pe.vote_num) AS rating_amount, full_story";
		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}post p LEFT JOIN {$tables_prefix}post_extras pe ON p.id=pe.news_id WHERE p.full_story NOT LIKE '%<!--dle_video_begin%' AND full_story LIKE '%<!--MBegin%') X", self::OBJECT_TYPE_ALBUM);
	}

	/**
	 * @param int $p_album_id
	 *
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_album_images_migration_params(int $p_album_id): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		return new KvsDataMigratorMigrationParams("image_id", "SELECT $p_album_id AS image_id, full_story AS image_code FROM {$tables_prefix}post WHERE id=$p_album_id", 0);
	}

	/**
	 * @param string $p_code
	 *
	 * @return array
	 */
	protected function build_album_image_url_from_code(string $p_code): array
	{
		unset($temp);
		preg_match_all("/<\!--MBegin:([^|]+)/i", $p_code, $temp);
		if ($temp[1])
		{
			return $temp[1];
		}

		return array();
	}
}