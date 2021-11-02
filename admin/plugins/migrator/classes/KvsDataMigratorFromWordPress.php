<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromWordPress extends KvsDataMigrator
{
	public const OPT_TABLES_PREFIX = "wordpress_tables_prefix";
	public const OPT_TAGS_TAXONOMY = "wordpress_tags_taxonomy";
	public const OPT_CATEGORIES_TAXONOMY = "wordpress_categories_taxonomy";
	public const OPT_MODELS_TAXONOMY = "wordpress_models_taxonomy";
	public const OPT_CONTENT_SOURCES_TAXONOMY = "wordpress_content_sources_taxonomy";
	public const OPT_DVDS_TAXONOMY = "wordpress_dvds_taxonomy";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "wordpress";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Word Press";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, true, true, true, true, false, false, true, true, false, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_TABLES_PREFIX, self::OPT_TAGS_TAXONOMY, self::OPT_CATEGORIES_TAXONOMY, self::OPT_MODELS_TAXONOMY, self::OPT_CONTENT_SOURCES_TAXONOMY, self::OPT_DVDS_TAXONOMY);
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

		if ($p_object_type == self::OBJECT_TYPE_MODEL)
		{
			$model_id = intval($p_object["model_id"]);

			$model_data_result = $this->query_source("SELECT meta_key, meta_value FROM {$tables_prefix}termmeta WHERE term_id=" . $model_id);
			if ($model_data_result)
			{
				for ($i = 0; $i < $model_data_result->num_rows; $i++)
				{
					$model_data_result->data_seek($i);
					$model_data_item = $model_data_result->fetch_assoc();
					switch ($model_data_item["meta_key"])
					{
						case "aka":
							$p_object["alias"] = $model_data_item["meta_value"];
							break;
						case "born":
							$p_object["birth_date"] = date("Y-m-d", strtotime($model_data_item["meta_value"]));
							break;
						case "height":
							$p_object["height"] = $model_data_item["meta_value"];
							break;
						case "weight":
							$p_object["weight"] = $model_data_item["meta_value"];
							break;
						case "measurements":
							$p_object["measurements"] = $model_data_item["meta_value"];
							break;
						case "hair":
							$p_object["hair"] = $model_data_item["meta_value"];
							break;
						case "eye":
							$p_object["eye_color"] = $model_data_item["meta_value"];
							break;
						case "site":
						case "website":
							$p_object["custom1"] = $model_data_item["meta_value"];
							break;
						case "facebook":
							$p_object["custom2"] = $model_data_item["meta_value"];
							break;
						case "twitter":
							$p_object["custom3"] = $model_data_item["meta_value"];
							break;
						case "nationality":
						case "country":
							$p_object["country"] = $model_data_item["meta_value"];
							break;
					}
				}
				$model_data_result->free();
			}
		} elseif ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$video_id = intval($p_object["video_id"]);

			$video_url = null;
			$video_data_result = $this->query_source("SELECT meta_key, meta_value FROM {$tables_prefix}postmeta WHERE post_id=" . $video_id);
			if ($video_data_result)
			{
				for ($i = 0; $i < $video_data_result->num_rows; $i++)
				{
					$video_data_result->data_seek($i);
					$video_data_item = $video_data_result->fetch_assoc();
					switch ($video_data_item["meta_key"])
					{
						case "ratings_average":
							$p_object["rating"] = max(0, floatval($video_data_item["meta_value"]));
							break;
						case "ratings_users":
							$p_object["rating_amount"] = intval($video_data_item["meta_value"]);
							break;
					}
				}
				if (isset($p_object["rating"]))
				{
					$p_object["rating_amount"] = max(1, $p_object["rating_amount"]);
					$p_object["rating"] = intval($p_object["rating"] / 2 * 5 * $p_object["rating_amount"]);
				}
				$video_data_result->free();
			}
			//$p_object["video_viewed"] = $this->query_num_source("SELECT sum(`count`) FROM {$tables_prefix}post_views WHERE id=" . $video_id);
			if ($video_url)
			{
				$p_object["file_download_url"] = $video_url;
			}

			$categories_taxonomy = $this->get_option_value(self::OPT_CATEGORIES_TAXONOMY);
			if ($categories_taxonomy != "")
			{
				$categories_taxonomy = $this->escape_string($categories_taxonomy);
				$p_object["categories"] = $this->query_text_source("SELECT group_concat(t.name SEPARATOR '||') FROM {$tables_prefix}term_relationships tr INNER JOIN {$tables_prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id WHERE tt.taxonomy='$categories_taxonomy' AND tr.object_id=$video_id GROUP BY tr.object_id ORDER BY tr.term_order ASC, t.name ASC");
			}

			$models_taxonomy = $this->get_option_value(self::OPT_MODELS_TAXONOMY);
			if ($models_taxonomy != "")
			{
				$models_taxonomy = $this->escape_string($models_taxonomy);
				$p_object["models"] = $this->query_text_source("SELECT group_concat(t.name SEPARATOR '||') FROM {$tables_prefix}term_relationships tr INNER JOIN {$tables_prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id WHERE tt.taxonomy='$models_taxonomy' AND tr.object_id=$video_id GROUP BY tr.object_id ORDER BY tr.term_order ASC, t.name ASC");
			}

			$tags_taxonomy = $this->get_option_value(self::OPT_TAGS_TAXONOMY);
			if ($tags_taxonomy != "")
			{
				$tags_taxonomy = $this->escape_string($tags_taxonomy);
				$p_object["tags"] = $this->query_text_source("SELECT group_concat(t.name SEPARATOR '||') FROM {$tables_prefix}term_relationships tr INNER JOIN {$tables_prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id WHERE tt.taxonomy='$tags_taxonomy' AND tr.object_id=$video_id GROUP BY tr.object_id ORDER BY tr.term_order ASC, t.name ASC");
			}

			$content_sources_taxonomy = $this->get_option_value(self::OPT_CONTENT_SOURCES_TAXONOMY);
			if ($content_sources_taxonomy != "")
			{
				$content_sources_taxonomy = $this->escape_string($content_sources_taxonomy);
				$p_object["content_source_id"] = $this->query_num_source("SELECT tt.term_id FROM {$tables_prefix}term_relationships tr INNER JOIN {$tables_prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id WHERE tt.taxonomy='$content_sources_taxonomy' AND tr.object_id=$video_id ORDER BY tr.term_order ASC, tt.term_id ASC LIMIT 1");
			}

			$dvds_taxonomy = $this->get_option_value(self::OPT_DVDS_TAXONOMY);
			if ($dvds_taxonomy != "")
			{
				$dvds_taxonomy = $this->escape_string($dvds_taxonomy);
				$p_object["dvd_id"] = $this->query_num_source("SELECT tt.term_id FROM {$tables_prefix}term_relationships tr INNER JOIN {$tables_prefix}term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id WHERE tt.taxonomy='$dvds_taxonomy' AND tr.object_id=$video_id ORDER BY tr.term_order ASC, tt.term_id ASC LIMIT 1");
			}
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

		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}users";
		}

		if ($this->data_to_migrate->is_categories())
		{
			$categories_taxonomy = $this->get_option_value(self::OPT_CATEGORIES_TAXONOMY);
			if ($categories_taxonomy != "")
			{
				$categories_taxonomy = $this->escape_string($categories_taxonomy);
				$queries[] = "SELECT count(*) FROM {$tables_prefix}term_taxonomy WHERE taxonomy='$categories_taxonomy'";
			}
		}

		if ($this->data_to_migrate->is_models())
		{
			$models_taxonomy = $this->get_option_value(self::OPT_MODELS_TAXONOMY);
			if ($models_taxonomy != "")
			{
				$models_taxonomy = $this->escape_string($models_taxonomy);
				$queries[] = "SELECT count(*) FROM {$tables_prefix}term_taxonomy WHERE taxonomy='$models_taxonomy'";
			}
		}

		if ($this->data_to_migrate->is_content_sources())
		{
			$content_sources_taxonomy = $this->get_option_value(self::OPT_CONTENT_SOURCES_TAXONOMY);
			if ($content_sources_taxonomy != "")
			{
				$content_sources_taxonomy = $this->escape_string($content_sources_taxonomy);
				$queries[] = "SELECT count(*) FROM {$tables_prefix}term_taxonomy WHERE taxonomy='$content_sources_taxonomy'";
			}
		}

		if ($this->data_to_migrate->is_dvds())
		{
			$dvds_taxonomy = $this->get_option_value(self::OPT_DVDS_TAXONOMY);
			if ($dvds_taxonomy != "")
			{
				$dvds_taxonomy = $this->escape_string($dvds_taxonomy);
				$queries[] = "SELECT count(*) FROM {$tables_prefix}term_taxonomy WHERE taxonomy='$dvds_taxonomy'";
			}
		}

		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}comments";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$selector = "ID AS user_id, user_login AS username, user_pass AS pass, user_email AS email, user_registered AS added_date, 2 AS status_id, display_name";
		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}users) X", self::OBJECT_TYPE_USER);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$categories_taxonomy = $this->get_option_value(self::OPT_CATEGORIES_TAXONOMY);
		if ($categories_taxonomy == "")
		{
			$this->error("Categories taxonomy option is empty");
			return null;
		}

		$categories_taxonomy = $this->escape_string($categories_taxonomy);

		$selector = "t.term_id AS category_id, t.name AS title, t.slug AS dir, tt.description AS description, now() AS added_date";
		$projector = "{$tables_prefix}term_taxonomy tt INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id";
		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM $projector WHERE tt.taxonomy='$categories_taxonomy') X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$models_taxonomy = $this->get_option_value(self::OPT_MODELS_TAXONOMY);
		if ($models_taxonomy == "")
		{
			$this->error("Models taxonomy option is empty");
			return null;
		}

		$models_taxonomy = $this->escape_string($models_taxonomy);

		$selector = "t.term_id AS model_id, t.name AS title, t.slug AS dir, tt.description AS description, now() AS added_date, 1 AS rating_amount";
		$projector = "{$tables_prefix}term_taxonomy tt INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id";
		return new KvsDataMigratorMigrationParams("model_id", "SELECT * FROM (SELECT $selector FROM $projector WHERE tt.taxonomy='$models_taxonomy') X", self::OBJECT_TYPE_MODEL);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$content_sources_taxonomy = $this->get_option_value(self::OPT_CONTENT_SOURCES_TAXONOMY);
		if ($content_sources_taxonomy == "")
		{
			$this->error("Content sources taxonomy option is empty");
			return null;
		}

		$content_sources_taxonomy = $this->escape_string($content_sources_taxonomy);

		$selector = "t.term_id AS content_source_id, t.name AS title, t.slug AS dir, tt.description AS description, now() AS added_date, 1 AS rating_amount";
		$projector = "{$tables_prefix}term_taxonomy tt INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id";
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT $selector FROM $projector WHERE tt.taxonomy='$content_sources_taxonomy') X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvds_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$dvds_taxonomy = $this->get_option_value(self::OPT_DVDS_TAXONOMY);
		if ($dvds_taxonomy == "")
		{
			$this->error("Channels taxonomy option is empty");
			return null;
		}

		$dvds_taxonomy = $this->escape_string($dvds_taxonomy);

		$selector = "t.term_id AS dvd_id, t.name AS title, t.slug AS dir, tt.description AS description, now() AS added_date, 1 AS rating_amount";
		$projector = "{$tables_prefix}term_taxonomy tt INNER JOIN {$tables_prefix}terms t ON tt.term_id=t.term_id";
		return new KvsDataMigratorMigrationParams("dvd_id", "SELECT * FROM (SELECT $selector FROM $projector WHERE tt.taxonomy='$dvds_taxonomy') X", self::OBJECT_TYPE_DVD);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$selector = "id AS video_id, post_title AS title, post_content AS description, post_name AS dir, CASE WHEN post_status='publish' THEN 1 ELSE 0 END status_id, post_date, 1 AS rating_amount, post_date AS added_date";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}posts WHERE post_type='post') X", self::OBJECT_TYPE_VIDEO);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);

		$selector = "c.comment_id AS comment_id, c.comment_post_id AS object_id, 1 AS object_type_id, p.post_title AS object_title, 0 AS user_id, '' AS username, CASE WHEN c.comment_approved='1' THEN 1 ELSE 0 END AS is_approved, c.comment_content AS comment, c.comment_date AS added_date";
		$projector = "{$tables_prefix}comments c INNER JOIN {$tables_prefix}posts p ON c.comment_post_id=p.id";

		return new KvsDataMigratorMigrationParams("comment_id", "SELECT * FROM (SELECT $selector FROM $projector) X", self::OBJECT_TYPE_COMMENT, function ($p_data) {
			$p_data["comment"] = str_replace(array("\\n", "\\r"), array("\n", ""), $p_data["comment"]);

			return $p_data;
		});
	}
}