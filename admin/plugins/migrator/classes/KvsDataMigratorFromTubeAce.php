<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromTubeAce extends KvsDataMigrator
{
	public const OPT_UPLOAD_FILES = "tubeace_upload_files";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "tubeace";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Tube Ace";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(false, true, false, true, false, true, true, false, false, false, false, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_UPLOAD_FILES);
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

		if ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$categories = explode(";", trim($p_object["categories"], ";"));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM channels WHERE id=" . intval($category_id));
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
			$queries[] = "SELECT count(*) FROM channels";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM sponsors";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM videos LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM videos";
			}
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS category_id, name AS title, description, now() AS added_date";
		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM channels) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "id AS content_source_id, name AS title, url_2257 AS url, 1 AS rating_amount, now() AS added_date";
		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT $selector FROM sponsors) X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$file_url_field = "file_url";
		if ($this->get_option_value(self::OPT_UPLOAD_FILES) != "")
		{
			$file_url_field = "file_download_url";
		}

		$selector = "id AS video_id, 1 AS user_id, sponsor AS content_source_id, title, description, rating_total AS rating, num_ratings AS rating_amount, GREATEST(duration, 1) AS duration, CASE WHEN embed_code='' AND LOCATE('.jpg', video_url)>0 THEN CONCAT('<img alt=\"\" src=\"', video_url, '\">') ELSE embed_code END AS embed, video_url AS $file_url_field, CASE WHEN LOCATE('.jpg', video_url)>0 THEN video_url ELSE thumb_url END AS screen_url, views AS video_viewed, 0 AS is_private, added AS added_date, added AS post_date, CASE WHEN online=1 THEN 1 ELSE 0 END AS status_id, channels AS categories";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM videos) X", self::OBJECT_TYPE_VIDEO);
	}
}