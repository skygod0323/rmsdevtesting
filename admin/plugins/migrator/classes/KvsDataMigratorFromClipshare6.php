<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorFromClipshare6 extends KvsDataMigrator
{
	public const OPT_USERS_BASE_URL = "clipshare6_users_base_url"; // http://domain.com/media/users
	public const OPT_CATEGORIES_BASE_URL = "clipshare6_categories_base_url"; // http://domain.com/media/categories/video
	public const OPT_VIDEOS_BASE_URL = "clipshare6_videos_base_url"; // http://domain.com/media/videos
	public const OPT_PHOTOS_BASE_URL = "clipshare6_photos_base_url"; // http://domain.com/media/photos
	public const OPT_SCREEN_BASE_URL = "clipshare6_screen_base_url"; // http://domain.com/media/videos/tmb

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "clipshare6";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "ClipShare v6";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, false, false, false, true, true, true, true, true, true, true, true, true, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_USERS_BASE_URL, self::OPT_CATEGORIES_BASE_URL, self::OPT_VIDEOS_BASE_URL, self::OPT_PHOTOS_BASE_URL, self::OPT_SCREEN_BASE_URL);
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
					$category = $this->query_text_source("SELECT name FROM channel WHERE CHID=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);

			unset($p_object['channel1'], $p_object['channel2'], $p_object['channel3']);

			$screen_base_url = $this->get_option_value(self::OPT_SCREEN_BASE_URL);
			if (trim($screen_base_url) == "")
			{
				$this->error("Screenshots base URL is not set");
				return null;
			}

			$p_object["screen_urls"] = array();
			for ($i = 1; $i <= $p_object["screen_amount"]; $i++)
			{
				$p_object["screen_urls"][] = "$screen_base_url/$p_object[video_id]/$i.jpg";
			}

			$base_url = trim($this->get_option_value(self::OPT_VIDEOS_BASE_URL));
			if ($base_url)
			{
				$file_download_urls = array();
				$file_download_urls[] = array('postfix' => '.mp4', 'url' => "$base_url/iphone/$p_object[video_id].mp4", 'is_source' => 1);
				if ($p_object["hd"] == 1)
				{
					unset($file_download_urls[0]['is_source']);
					$file_download_urls[] = array('postfix' => '_720p.mp4', 'url' => "$base_url/hd/$p_object[video_id].mp4", 'is_source' => 1);
				}
				$p_object['file_download_urls'] = $file_download_urls;
			}

			unset($p_object['hd'], $p_object['iphone'], $p_object['vkey'], $p_object['hd_filename']);
		} elseif ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$categories = array(intval($p_object['category1']), intval($p_object['category2']), intval($p_object['category3']));
			$new_categories = array();
			foreach ($categories as $category_id)
			{
				if (intval($category_id) > 0)
				{
					$category = $this->query_text_source("SELECT name FROM album_categories WHERE CID=" . intval($category_id));
					if ($category)
					{
						$new_categories[] = trim($category);
					}
				}
			}
			$p_object["categories"] = implode("||", $new_categories);

			unset($p_object['category1'], $p_object['category2'], $p_object['category3']);
		}

		return $p_object;
	}

	/**
	 * @return bool
	 */
	protected function needs_send_referer(): bool
	{
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
			$queries[] = "SELECT count(*) FROM signup";
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
			$queries[] = "SELECT count(*) FROM video_comments WHERE status='1'";
			$queries[] = "SELECT count(*) FROM photo_comments WHERE status='1'";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM favourite";
			$queries[] = "SELECT count(*) FROM playlist";
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM friends f1 INNER JOIN friends f2 ON f1.UID=f2.FID AND f2.UID=f1.FID WHERE f1.status='Confirmed' AND f2.status='Confirmed' AND f1.UID<f2.UID";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM mail WHERE subject not like 'Friendship invitation%'";
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$queries[] = "SELECT count(*) FROM video_subscribe";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$users_base_url = $this->get_option_value(self::OPT_USERS_BASE_URL);
		if (trim($users_base_url) == "")
		{
			$this->error("Users base URL is not set");
			return null;
		}

		$selector = "UID AS user_id, username, pwd AS pass, email, username AS display_name, photo AS avatar, bdate AS birth_date, gender, relation AS relationship_status, website, country, city, school AS education, occupation, aboutme AS about_me, interest_hobby AS interests, fav_movie_show AS favourite_movies, fav_music AS favourite_music, fav_book AS favourite_books, video_viewed, profile_viewed, watched_video AS video_watched, addtime AS added_date, logintime AS last_login_date, company AS custom1, user_ip AS ip, CASE WHEN account_status='Inactive' THEN 0 ELSE 2 END AS status_id,";

		$selector .= "(CASE WHEN photo!='' THEN concat('$users_base_url/', photo) ELSE '' END) AS avatar_url, ";

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM signup) X", self::OBJECT_TYPE_USER, function ($p_data) {
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

			$p_data["ip"] = ip2int($p_data["ip"]);

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
		$categories_base_url = $this->get_option_value(self::OPT_CATEGORIES_BASE_URL);

		$selector1 = "CHID AS category_id, name AS title, slug AS dir, now() AS added_date";
		if (trim($categories_base_url) != "")
		{
			$selector1 .= ", concat(CHID, '.jpg') AS screenshot1, concat('$categories_base_url/', CHID, '.jpg') AS screenshot1_url";
		}
		$selector2 = "100+CID AS category_id, name AS title, slug AS dir, now() AS added_date";
		if (trim($categories_base_url) != "")
		{
			$selector2 .= ", concat(CID, '.jpg') AS screenshot1, concat('$categories_base_url/', CID, '.jpg') AS screenshot1_url";
		}
		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT $selector1 FROM channel UNION ALL SELECT $selector2 FROM album_categories WHERE name NOT IN (SELECT name FROM channel) AND slug NOT IN (SELECT slug FROM channel)) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$categories_selector = 'channel AS channel1';
		if ($this->query_num_source("SELECT count(*) FROM video WHERE channel2!='' or channel3!=''") > 0)
		{
			$categories_selector = 'channel AS channel1, channel2, channel3';
		}

		$selector = "VID AS video_id, UID AS user_id, title, description, keyword AS tags, $categories_selector, duration, CASE WHEN type='private' THEN 1 ELSE 0 END AS is_private, addtime AS added_date, addtime AS post_date, viewnumber AS video_viewed, viewtime AS last_time_view_date, rate/100*5*(likes+dislikes) AS rating, greatest(likes+dislikes,1) AS rating_amount, CASE WHEN active='0' THEN 0 ELSE 1 END AS status_id, embed_code AS embed, thumb AS screen_main, thumbs AS screen_amount, hd, iphone, vkey";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM video) X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
			$p_data["tags"] = str_replace(array(",", " "), "||", $p_data["tags"]);

			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["post_date"] = $p_data["added_date"];

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$photos_base_url = $this->get_option_value(self::OPT_PHOTOS_BASE_URL);
		if (trim($photos_base_url) == '')
		{
			$this->error("Photos base URL option is empty");
			return null;
		}

		$categories_selector = 'category AS category1';
		if ($this->query_num_source("SELECT count(*) FROM albums WHERE category2!='' or category3!=''") > 0)
		{
			$categories_selector = 'category AS category1, category2, category3';
		}

		$selector = "AID AS album_id, UID AS user_id, name AS title, tags, $categories_selector, CASE WHEN type='private' THEN 1 ELSE 0 END AS is_private, addtime AS added_date, addtime AS post_date, total_views AS album_viewed, rate/100*5*(likes+dislikes) AS rating, greatest(likes+dislikes,1) AS rating_amount, CASE WHEN status='0' THEN 0 ELSE 1 END AS status_id";

		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM albums) X", self::OBJECT_TYPE_ALBUM, function ($p_data) {
			$p_data["tags"] = str_replace(array(",", " "), "||", $p_data["tags"]);

			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["post_date"] = $p_data["added_date"];

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
		$photos_base_url = $this->get_option_value(self::OPT_PHOTOS_BASE_URL);
		if (trim($photos_base_url) == '')
		{
			$this->error("Photos base URL option is empty");
			return null;
		}
		return new KvsDataMigratorMigrationParams("PID", "SELECT PID AS image_id, concat('$photos_base_url/', PID, '.jpg') AS image_url FROM photos WHERE AID=$p_album_id AND status='1' ORDER BY PID ASC", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "";
		$selector .= "SELECT concat('video', c.CID) AS migrator_uid, c.VID AS object_id, 1 AS object_type_id, v.title AS object_title, c.UID AS user_id, u.username AS username, c.status AS is_approved, c.comment, c.addtime AS added_date FROM video_comments c INNER JOIN video v ON c.VID=v.VID INNER JOIN signup u ON c.UID=u.UID";
		$selector .= " UNION ALL ";
		$selector .= "SELECT concat('album', c.CID) AS migrator_uid, a.AID AS object_id, 2 AS object_type_id, a.name AS object_title, c.UID AS user_id, u.username AS username, c.status AS is_approved, c.comment, c.addtime AS added_date FROM photo_comments c INNER JOIN photos p ON c.PID=p.PID INNER JOIN albums a ON p.AID=a.AID INNER JOIN signup u ON c.UID=u.UID";

		return new KvsDataMigratorMigrationParams("migrator_uid", "SELECT * FROM ($selector) X", self::OBJECT_TYPE_COMMENT, function ($p_data) {
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
		$projector = "friends f1 INNER JOIN friends f2 ON f1.UID=f2.FID AND f2.UID=f1.FID";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM (SELECT $selector FROM $projector WHERE f1.status='Confirmed' AND f2.status='Confirmed' AND f1.UID<f2.UID) X", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "pm.mail_id AS message_id, pm.body AS message, pm.send_date AS added_date, pm.readed AS is_read, CASE WHEN pm.readed='1' THEN pm.send_date ELSE '0000-00-00 00:00:00' END AS read_date, s1.UID AS user_from_id, s2.UID AS user_id";
		$projector = "mail pm INNER JOIN signup s1 ON pm.sender=s1.username INNER JOIN signup s2 ON pm.receiver=s2.username";
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
		$selector = "SELECT SUID AS user_id, UID AS subscribed_object_id, 1 AS subscribed_type_id, subscribe_date AS added_date FROM video_subscribe";
		return new KvsDataMigratorMigrationParams(null, "SELECT * FROM ($selector) X", 0);
	}
}