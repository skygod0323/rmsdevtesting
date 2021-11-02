<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromPredatorCms extends KvsDataMigrator
{
	public const OPT_VIDEOS_BASE_URL = 'predator_videos_base_url';

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "predator";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Predator CMS";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, true, false, true, true, false, false, true, false, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_VIDEOS_BASE_URL);
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return array
	 */
	protected function pre_process_each_object_hook(array $p_object, int $p_object_type): array
	{
		foreach ($p_object as $key => $value)
		{
			$p_object[$key] = preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
				return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
			}, $value);
		}
		return $p_object;
	}

	/**
	 * @return array
	 */
	protected function build_progress_queries(): array
	{
		$queries = array();
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM predator_users";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM predator_cats";
		}
		if ($this->data_to_migrate->is_tags())
		{
			$queries[] = "SELECT count(*) FROM predator_tags";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM predator_sponsors";
			$queries[] = "SELECT count(*) FROM predator_sponsors_sites";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM predator_plugs LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM predator_plugs";
			}
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "user_id, username, username AS display_name, md5(password) AS pass, email, joined AS added_date, CASE WHEN validated=1 THEN 2 ELSE 0 END AS status_id";
		return new KvsDataMigratorMigrationParams("user_id", "SELECT $selector FROM predator_users", self::OBJECT_TYPE_USER, function ($p_data) {
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
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "cat_id AS category_id, cat_name AS title, now() AS added_date";
		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM predator_cats) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_tags_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "tag_id, tag_word AS tag, tag_word AS tag_dir, added AS added_date";
		return new KvsDataMigratorMigrationParams("tag_id", "SELECT $selector FROM predator_tags", self::OBJECT_TYPE_TAG, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["tag"] = str_replace("-", " ", $p_data["tag"]);
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "sponsor_id AS content_source_group_id, sponsor_name AS title, sponsor_description AS description, now() AS added_date, sponsor_url AS custom1";
		return new KvsDataMigratorMigrationParams("content_source_group_id", "SELECT * FROM (SELECT $selector FROM predator_sponsors) X", self::OBJECT_TYPE_CONTENT_SOURCE_GROUP);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "sponsor_site_id AS content_source_id, sponsor_site_parent AS content_source_group_id, sponsor_site_name AS title, sponsor_site_url AS url, 1 AS rating_amount, now() AS added_date";
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT $selector FROM predator_sponsors_sites) X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$cluster_settings = $this->query_text_source("SELECT cluster_settings FROM predator_settings");
		if ($cluster_settings == "")
		{
			$this->error("Cluster settings do not exist in database");
			return null;
		}

		$cluster_settings = @unserialize($cluster_settings, ['allowed_classes' => false]);
		if (!is_array($cluster_settings))
		{
			$this->error("Cluster settings are not serialized array");
			return null;
		}

		$videos_url = $this->get_option_value(self::OPT_VIDEOS_BASE_URL);
		if ($videos_url == '')
		{
			$this->error("Videos base URL option is empty");
			return null;
		}

		$screen_url = $cluster_settings["thumb_server_http"];
		if ($screen_url == '')
		{
			$this->error("Screenshots base URL option is empty");
			return null;
		}

		$selector = "p.plug_id AS video_id, p.poster_id AS user_id, ifnull(v.sponsor_site_id, 0) AS content_source_id, ifnull(p.title, '') AS title, ifnull(p.description, '') AS description, ifnull(p.seo_url, '') AS dir, CASE WHEN p.rating>0 THEN p.rating * 5 ELSE 0 END AS rating, greatest(p.rating, 1) AS rating_amount, CASE WHEN v.html_code IS NOT NULL THEN 0 ELSE 1 END AS duration, ifnull(b.body, '') AS embed, ifnull(p.url, '') AS pseudo_url, CASE WHEN v.html_code IS NOT NULL THEN concat('$videos_url/', v.html_code) ELSE '' END AS file_url, concat('$screen_url/', p.thumb) AS screen_url, p.views AS video_viewed, 0 AS is_private, p.posted AS added_date, p.posted AS post_date, CASE WHEN p.approved='1' THEN 1 ELSE 0 END AS status_id, p.category AS categories, p.tags";
		$projector = "predator_plugs p LEFT JOIN predator_videos v ON p.plug_id=v.plug_id LEFT JOIN predator_blog_articles b ON p.plug_id=b.plug_id";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM $projector) X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
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
			$p_data["tags"] = str_replace(" ", "||", $p_data["tags"]);
			$p_data["dir"] = end(explode("/", $p_data["dir"]));

			if (preg_match("|src\ *=\ *['\"\ ]*([^\"'<>]+?\.jpg[^\"'<>\ ]*)['\"\ ]*|is", $p_data['embed'], $temp))
			{
				$p_data["screen_url"] = trim($temp[1]);
			}

			return $p_data;
		});
	}
}