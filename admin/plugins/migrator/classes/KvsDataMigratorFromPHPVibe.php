<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromPHPVibe extends KvsDataMigrator
{
	public const OPT_TABLES_PREFIX = "phpvibe_tables_prefix";
	public const OPT_VIDEOS_BASE_URL = "phpvibe_videos_base_url";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "phpvibe";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "PHP Vibe";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, false, false, true, false, false, false, true, true, false, false, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_TABLES_PREFIX, self::OPT_VIDEOS_BASE_URL);
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
			$p_object[$key] = html_entity_decode(str_replace("\\'", "'", str_replace("\\\"", "\"", preg_replace_callback("/(&#[0-9]+;)/", function ($m)
			{
				return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
			}, $value))));
		}

		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		if ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$category = $this->query_text_source("SELECT cat_name FROM {$tables_prefix}channels WHERE cat_id=" . intval($p_object['category']));
			$p_object["categories"] = $category;

			unset($p_object['category']);

			$p_object["tags"] = str_replace(",", "||", $p_object["tags"]);
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
			$queries[] = "SELECT count(*) FROM {$tables_prefix}channels";
		}
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}users";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM {$tables_prefix}playlists WHERE picture='[likes]' OR picture='[later]'";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM {$tables_prefix}videos LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM {$tables_prefix}videos WHERE source!=''";
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

		$categories_base_url = trim($this->old_url, " /") . "/res.php?src=";
		$selector = "cat_id AS category_id, cat_name AS title, cat_desc AS description, now() AS added_date, CASE WHEN picture!='' THEN concat(cat_id, '.jpg') ELSE '' END AS screenshot1, CASE WHEN picture!='' THEN concat('$categories_base_url', picture) ELSE '' END AS screenshot1_url";

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}channels) X", self::OBJECT_TYPE_CATEGORY);
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

		$users_base_url = trim($this->old_url, " /") . "/res.php?src=";
		$selector = "id AS user_id, email AS username, email, pass, date_registered AS added_date, lastlogin AS last_login_date, name AS display_name, local AS city, country, bio AS about_me, views AS profile_viewed, 2 AS status_id, CASE WHEN avatar!='' THEN concat(id, '.jpg') ELSE '' END AS avatar, CASE WHEN avatar!='' THEN concat('$users_base_url', avatar) ELSE '' END AS avatar_url";
		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}users) X", self::OBJECT_TYPE_USER);
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

		$videos_base_url = $this->get_option_value(self::OPT_VIDEOS_BASE_URL);
		if ($videos_base_url == "")
		{
			$this->error("Videos base URL is empty");
			return null;
		}

		$selector = "id AS video_id, user_id, date AS added_date, date AS post_date, CASE WHEN pub=0 THEN 0 ELSE 1 END AS status_id, CASE WHEN private=0 THEN 0 ELSE 1 END AS is_private, title, duration, description, tags, category, views AS video_viewed, liked*5 AS rating, liked+disliked AS rating_amount, concat('$videos_base_url', '/', replace(source, 'localfile/', '')) AS file_download_url";
		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM {$tables_prefix}videos WHERE source!='') X", self::OBJECT_TYPE_VIDEO);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$tables_prefix = $this->get_option_value(self::OPT_TABLES_PREFIX);
		if ($tables_prefix == "")
		{
			$this->error("Tables prefix option is empty");
			return null;
		}

		return new KvsDataMigratorMigrationParams(null, "SELECT video_id, owner AS user_id, CASE WHEN picture='[later]' THEN 1 ELSE 0 END AS fav_type, now() AS added_date FROM {$tables_prefix}playlist_data pd INNER JOIN {$tables_prefix}playlists p ON pd.playlist=p.id WHERE p.picture='[likes]' OR p.picture='[later]'", 0);
	}
}