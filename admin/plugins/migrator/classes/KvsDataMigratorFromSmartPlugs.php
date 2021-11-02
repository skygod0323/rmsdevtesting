<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromSmartPlugs extends KvsDataMigrator
{
	public const OPT_IMAGES_BASE_URL = "smartplugs_images_base_url";
	public const OPT_VIDEOS_BASE_URL = "smartplugs_videos_base_url";
	public const OPT_UPLOAD_FILES = 'smartplugs_upload_files';

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "smartplugs";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Smart Plugs";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(false, true, false, true, false, true, true, true, true, false, false, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_IMAGES_BASE_URL, self::OPT_VIDEOS_BASE_URL, self::OPT_UPLOAD_FILES);
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

		foreach ($p_object as $key => $value)
		{
			$p_object[$key] = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
				return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
			}, $value);
		}

		if ($p_object_type == self::OBJECT_TYPE_VIDEO || $p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$categories = explode("|", trim($p_object["categories"], "|"));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM categories WHERE id=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);
		}

		return $p_object;
	}

	/**
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$queries = array();
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM categories WHERE active=1";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM sponsors WHERE active=1";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM hosted LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM hosted WHERE code NOT LIKE '%<img%'";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM hosted LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM hosted WHERE code LIKE '%<img%'";
			}
		}
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM comments";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS category_id, name AS title, now() AS added_date";
		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM categories WHERE active=1) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS content_source_id, name AS title, url AS url, 1 AS rating_amount, now() AS added_date";
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT $selector FROM sponsors WHERE active=1) X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$videos_url = $this->get_option_value(self::OPT_VIDEOS_BASE_URL);
		if ($videos_url == "")
		{
			$this->error("Videos base URL option is empty");
			return null;
		}

		$screen_url = $this->get_option_value(self::OPT_IMAGES_BASE_URL);
		if ($screen_url == "")
		{
			$this->error("Images base URL option is empty");
			return null;
		}

		$videos_url = trim($videos_url, "/") . "/";
		$screen_url = trim($screen_url, "/") . "/";

		$file_url_field = "file_url";
		if ($this->get_option_value(self::OPT_UPLOAD_FILES) != '')
		{
			$file_url_field = "file_download_url";
		}

		$selector = "id AS video_id, sponsor AS content_source_id, name AS title, descr AS description, 1 AS rating_amount, hits AS video_viewed, 0 AS is_private, date AS added_date, date AS post_date, CASE WHEN active=1 THEN 1 ELSE 0 END AS status_id, category AS categories, CASE WHEN image!='' THEN CONCAT('$screen_url', image) ELSE '' END AS screen_url, CASE WHEN code!='' THEN CONCAT('$videos_url', code) ELSE '' END AS $file_url_field";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM hosted WHERE code NOT LIKE '%<img%') X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
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

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS album_id, sponsor AS content_source_id, name AS title, descr AS description, 1 AS rating_amount, hits AS album_viewed, 0 AS is_private, date AS added_date, date AS post_date, CASE WHEN active=1 THEN 1 ELSE 0 END AS status_id, category AS categories";

		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM hosted WHERE code LIKE '%<img%') X", self::OBJECT_TYPE_ALBUM, function ($p_data) {
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
		return new KvsDataMigratorMigrationParams("image_id", "SELECT $p_album_id AS image_id, code AS image_code FROM hosted WHERE id=$p_album_id", 0);
	}

	/**
	 * @param string $p_code
	 *
	 * @return array
	 */
	protected function build_album_image_url_from_code(string $p_code): array
	{
		if (preg_match_all("|src\ *=\ *['\"\ ]*([^\"'<>]+?\.jpg[^\"'<>\ ]*)['\"\ ]*|is", $p_code, $temp))
		{
			return $temp[1];
		}
		return array();
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "c.id AS comment_id, c.plug AS object_id, CASE WHEN h.code LIKE '%<img%' THEN 2 ELSE 1 END AS object_type_id, h.name AS object_title, 0 AS user_id, c.user AS username, c.comment AS comment, c.date AS added_date, c.active AS is_approved";
		$projector = "comments c INNER JOIN hosted h ON c.plug=h.id";

		return new KvsDataMigratorMigrationParams("comment_id", "SELECT * FROM (SELECT $selector FROM $projector) X", self::OBJECT_TYPE_COMMENT, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["comment"] = str_replace(array("\\n", "\\r"), array("\n", ""), $p_data["comment"]);

			return $p_data;
		});
	}
}