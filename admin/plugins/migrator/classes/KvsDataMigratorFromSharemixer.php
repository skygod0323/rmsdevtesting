<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromSharemixer extends KvsDataMigrator
{
	public const OPT_UPLOADS_FOLDER_NAME = "sharemixer_uploads_folder_name";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "sharemixer";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Sharemixer";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, false, false, true, true, false, true, true, true, true, true, true, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_UPLOADS_FOLDER_NAME);
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
			$p_object[$key] = str_replace(array("\\\"", "\\'"), array("\"", "'"), preg_replace_callback("/(&#[0-9]+;)/", function ($m) {
				return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES");
			}, $value));
		}

		if ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			$categories = explode("|", trim($p_object["categories"], "|"));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM channel WHERE CHID=" . intval($category_id));
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
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM signup where is_deleted='0'";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM channel";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM video LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM video";
			}
		}
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM comments";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM favourite";
			$queries[] = "SELECT count(*) FROM playlist";
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM friends f1 INNER JOIN friends f2 on f1.UID=f2.FID AND f2.UID=f1.FID WHERE f2.id>f1.id AND f1.friends_status='Confirmed' AND f2.friends_status='Confirmed'";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM pm WHERE subject not like 'Friendship invitation%'";
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$queries[] = "SELECT count(*) FROM subscribe_video";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$uploads_folder_name = $this->get_option_value(self::OPT_UPLOADS_FOLDER_NAME);
		if ($uploads_folder_name == "")
		{
			$this->error("Uploads folder name option is empty");
			return null;
		}

		$base_url = "$this->old_url/$uploads_folder_name/avatars";
		$base_path = "$this->old_path/$uploads_folder_name/avatars";

		$selector = "UID AS user_id, username, pwd AS pass, email, username AS display_name, photo AS avatar, bdate AS birth_date, gender, relation AS relationship_status, website, country, city, school AS education, occupation, aboutme AS about_me, interest_hobby AS interests, fav_movie_show AS favourite_movies, fav_music AS favourite_music, fav_book AS favourite_books, video_viewed, profile_viewed, watched_video AS video_watched, addtime AS added_date, logintime AS last_login_date, company AS custom1, language AS custom2, CASE WHEN account_status='Inactive' THEN 0 ELSE 2 END AS status_id,";

		$selector .= "(CASE WHEN photo!='' THEN concat('$base_url/', photo) ELSE '' END) AS avatar_url, ";
		$selector .= "(CASE WHEN photo!='' THEN concat('$base_path/', photo) ELSE '' END) AS avatar_path, ";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM signup WHERE is_deleted='0') X", self::OBJECT_TYPE_USER, function ($p_data) {
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

			$p_data["about_me"] = str_replace("&nbsp;", " ", $p_data["about_me"]);
			for ($i = 0; $i < 20; $i++)
			{
				if (strpos($p_data["about_me"], "<") !== false)
				{
					$p_data["about_me"] = preg_replace("|<br[^>]*[/]?>|is", "\n", $p_data["about_me"]);
					$p_data["about_me"] = preg_replace("|<p[^>]*>([^<]*)</p>|is", "\${1}\n", $p_data["about_me"]);
					$p_data["about_me"] = preg_replace("|<a[ ]+href=['\"]?([^<'\"]*)['\"]?[^>]*>([^<]*)</a>|is", " \${2}: \${1} ", $p_data["about_me"]);
					$p_data["about_me"] = preg_replace("|<[^>]*>([^<]*)</[^>]*>|is", " \${1} ", $p_data["about_me"]);
				}
			}
			if (strpos($p_data["about_me"], "<") !== false)
			{
				$p_data["about_me"] = preg_replace("|<[^>]*>|is", " ", $p_data["about_me"]);
			}
			$p_data["about_me"] = preg_replace("|[\r\n]+|", "\n", $p_data["about_me"]);
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$uploads_folder_name = $this->get_option_value(self::OPT_UPLOADS_FOLDER_NAME);
		if ($uploads_folder_name == "")
		{
			$this->error("Uploads folder name option is empty");
			return null;
		}

		$base_url = "$this->old_url/$uploads_folder_name/chimg";
		$base_path = "$this->old_path/$uploads_folder_name/chimg";

		$selector = "CHID AS category_id, name AS title, descrip AS description, now() AS added_date, ";
		$selector .= "concat(CHID, '.jpg') AS screenshot1, ";
		$selector .= "concat('$base_url/', CHID, '.jpg') AS screenshot1_url, ";
		$selector .= "concat('$base_path/', CHID, '.jpg') AS screenshot1_path, ";

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM channel) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$uploads_folder_name = $this->get_option_value(self::OPT_UPLOADS_FOLDER_NAME);
		if ($uploads_folder_name == "")
		{
			$this->error("Uploads folder name option is empty");
			return null;
		}

		$base_url = "$this->old_url/$uploads_folder_name/thumbs";

		$selector = "VID AS video_id, UID AS user_id, title, description, keyword AS tags, channel AS categories, duration, CASE WHEN type='private' THEN 1 ELSE 0 END AS is_private, addtime AS added_date, addtime AS post_date, vkey AS dir, viewnumber AS video_viewed, viewtime AS last_time_view_date, rate/2*ratedby AS rating, greatest(ratedby,1) AS rating_amount, CASE WHEN active='0' THEN 0 ELSE 1 END AS status_id, embed_code AS embed, concat('$base_url/1_', VID, '.jpg') AS screen_url, record_date AS custom1, location AS custom2, country AS custom3";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM video) X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
			if ($p_data["embed"] == "")
			{
				$p_data["file_config_url"] = "/includes/common/player_pf.php?vid=$p_data[video_id]";
				$p_data["file_config_pattern"] = "|<f1>([^<]*)</f1>|is";
			}

			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);

			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["post_date"] = $p_data["added_date"];

			if ($p_data["custom1"] == "0000-00-00")
			{
				$p_data["custom1"] = "";
			}

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "c.COMID AS comment_id, c.VID AS object_id, 1 AS object_type_id, v.title AS object_title, c.UID AS user_id, u.username AS username, 1 AS is_approved, c.commen AS comment, c.addtime AS added_date";
		$projector = "comments c INNER JOIN video v ON c.VID=v.VID INNER JOIN signup u ON c.UID=u.UID";

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

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT VID AS video_id, UID AS user_id, 0 AS fav_type, now() AS added_date FROM favourite UNION ALL SELECT VID AS video_id, UID AS user_id, 1 AS fav_type, now() AS added_date FROM playlist", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "f1.UID AS user_id, f1.FID AS friend_id, f1.invite_date AS added_date, f1.invite_date AS approved_date, 1 AS is_approved";
		$projector = "friends f1 INNER JOIN friends f2 on f1.UID=f2.FID AND f2.UID=f1.FID";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE f2.id>f1.id AND f1.friends_status='Confirmed' AND f2.friends_status='Confirmed') X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "pm.pm_id AS message_id, pm.body AS message, pm.date AS added_date, pm.seen AS is_read, CASE WHEN pm.seen='1' THEN pm.date ELSE '0000-00-00 00:00:00' END AS read_date, s1.UID AS user_from_id, s2.UID AS user_id";
		$projector = "pm INNER JOIN signup s1 ON pm.sender=s1.username INNER JOIN signup s2 ON pm.receiver=s2.username";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE pm.subject not like 'Friendship invitation%') X", 0, function ($p_data) {
			$p_data["message"] = str_replace("&nbsp;", " ", $p_data["message"]);
			for ($i = 0; $i < 20; $i++)
			{
				if (strpos($p_data["message"], "<") !== false)
				{
					$p_data["message"] = preg_replace("|<br[^>]*[/]?>|is", "\n", $p_data["message"]);
					$p_data["message"] = preg_replace("|<p[^>]*>([^<]*)</p>|is", "\${1}\n", $p_data["message"]);
					$p_data["message"] = preg_replace("|<a[ ]+href=['\"]?([^<'\"]*)['\"]?[^>]*>([^<]*)</a>|is", " \${2}: \${1} ", $p_data["message"]);
					$p_data["message"] = preg_replace("|<[^>]*>([^<]*)</[^>]*>|is", " \${1} ", $p_data["message"]);
				}
			}
			if (strpos($p_data["message"], "<") !== false)
			{
				$p_data["message"] = preg_replace("|<[^>]*>|is", " ", $p_data["message"]);
			}
			$p_data["message"] = preg_replace("|[\r\n]+|", "\n", $p_data["message"]);

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_subscriptions_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT sl AS subscription_id, subscribe_from AS user_id, subscribe_to AS subscribed_object_id, 1 AS subscribed_type_id, now() AS added_date FROM subscribe_video", 0);
	}
}