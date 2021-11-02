<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromAws extends KvsDataMigrator
{
	public const OPT_CATEGORIES_BASE_URL = "aws_categories_base_url";
	public const OPT_USERS_BASE_URL = "aws_users_base_url";
	public const OPT_THUMBS_BASE_URL = "aws_thumbs_base_url";
	public const OPT_VIDEOS_BASE_URL = "aws_videos_base_url";

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "aws";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Adult Watch Script";
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
		return array(self::OPT_CATEGORIES_BASE_URL, self::OPT_USERS_BASE_URL, self::OPT_THUMBS_BASE_URL, self::OPT_VIDEOS_BASE_URL);
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
			$categories = array(intval($p_object['channel1']), intval($p_object['channel2']), intval($p_object['channel3']));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM categories_videos WHERE CATID=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);

			unset($p_object['channel1'], $p_object['channel2'], $p_object['channel3']);

			$p_object["duration"] = intval($p_object["duration"]);

			$p_object["tags"] = implode("||", array_map("trim", explode(",", $p_object["tags"])));

			if ($p_object["added_date"] > 0)
			{
				$p_object["added_date"] = date("Y-m-d H:i:s", $p_object["added_date"]);
			} else
			{
				$p_object["added_date"] = date("Y-m-d H:i:s");
			}
			$p_object["post_date"] = $p_object["added_date"];
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
			$queries[] = "SELECT count(*) FROM members";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM categories_videos";
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
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM videos_comments";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM videos_favorited";
			$queries[] = "SELECT count(*) FROM videos_playlist";
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM friends f1 INNER JOIN friends f2 on f1.USERID=f2.FRIENDID AND f2.USERID=f1.FRIENDID WHERE f2.FID>f1.FID";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM messages_inbox";
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$queries[] = "SELECT count(*) FROM members_subscribers";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$users_base_url = $this->get_option_value(self::OPT_USERS_BASE_URL);

		$selector = "USERID AS user_id, email, username, username AS display_name, password AS pass, birthday AS birth_date, CASE WHEN gender=0 THEN 'f' ELSE 'm' END as gender, description AS about_me, city, country, yourvideosviewed AS video_viewed, profileviews AS profile_viewed, videosyouviewed AS video_watched, addtime AS added_date, lastlogin AS last_login_date, CASE WHEN premium = 1 THEN 3 ELSE 2 END AS status_id, job AS occupation, school AS education, interests, fav_movies AS favourite_movies, fav_music AS favourite_music, fav_books AS favourite_books, ip, ";
		if ($users_base_url != "")
		{
			$selector .= "profilepicture AS avatar, ";
			$selector .= "concat('$users_base_url/', profilepicture) AS avatar_url, ";
		}

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM members) X", self::OBJECT_TYPE_USER, function ($p_data) {
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
				$p_data["last_login_date"] = $p_data["added_date"];
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
		$categories_base_url = $this->get_option_value(self::OPT_CATEGORIES_BASE_URL);

		$selector = "CATID AS category_id, name AS title, details AS description, now() AS added_date, ";
		if ($categories_base_url != "")
		{
			$selector .= "concat(CATID, '.jpg') AS screenshot1, ";
			$selector .= "concat('$categories_base_url/', CATID, '.jpg') AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM categories_videos) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$videos_base_url = $this->get_option_value(self::OPT_VIDEOS_BASE_URL);
		if ($videos_base_url == "")
		{
			$this->error("Videos base URL option is empty");
			return null;
		}

		$images_base_url = $this->get_option_value(self::OPT_THUMBS_BASE_URL);

		$selector = "VIDEOID AS video_id, USERID AS user_id, title, description, tags, categories AS channel1, categories2 AS channel2, categories3 AS channel3, runtime AS duration, CASE WHEN public=0 THEN 1 ELSE 0 END AS is_private, time_added AS added_date, time_added AS post_date, viewcount AS video_viewed, last_viewed AS last_time_view_date, good*5 AS rating, GREATEST(good+bad, 1) AS rating_amount, CASE WHEN active=0 THEN 0 ELSE 1 END AS status_id, embedcode AS embed, concat('$images_base_url/', VIDEOID, '-1.jpg') AS screen_url, CASE WHEN embedcode='' THEN concat('$videos_base_url/', video_name) ELSE '' END AS file_download_url";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM videos) X", self::OBJECT_TYPE_VIDEO);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "c.CID AS comment_id, c.VIDEOID AS object_id, 1 AS object_type_id, v.title AS object_title, c.USERID AS user_id, u.username AS username, 1 AS is_approved, c.details AS comment, c.time_added AS added_date";
		$projector = "videos_comments c INNER JOIN videos v ON c.VIDEOID=v.VIDEOID INNER JOIN members u ON c.USERID=u.USERID";

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
		return new KvsDataMigratorMigrationParams(null, "SELECT VIDEOID AS video_id, USERID AS user_id, 0 AS fav_type, now() AS added_date FROM videos_favorited UNION ALL SELECT VIDEOID AS video_id, USERID AS user_id, 1 AS fav_type, now() AS added_date FROM videos_playlist", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "f1.USERID AS user_id, f1.FRIENDID AS friend_id, f1.time_added AS added_date, f1.time_added AS approved_date, 1 AS is_approved";
		$projector = "friends f1 INNER JOIN friends f2 on f1.USERID=f2.FRIENDID AND f2.USERID=f1.FRIENDID";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE f2.FID>f1.FID) X", 0, function ($p_data) {
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
				$p_data["approved_date"] = date("Y-m-d H:i:s");
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "MID AS message_id, message, time AS added_date, CASE WHEN unread=1 THEN 0 ELSE 1 END AS is_read, CASE WHEN unread!=1 THEN time ELSE '0000-00-00 00:00:00' END AS read_date, MSGFROM AS user_from_id, MSGTO AS user_id";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM messages_inbox) X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_subscriptions_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT SID AS subscription_id, subscriber AS user_id, subscribee AS subscribed_object_id, 1 AS subscribed_type_id, now() AS added_date FROM members_subscribers", 0);
	}
}