<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

/*
 * alter table `niches` add column `metatitle` varchar(255) not null default '';
 * alter table `content` add column `slug` varchar(255) not null default '';
 * alter table `content` add column `dvd` int(10) not null default 0;
 */

/*

# migration ------------------------------------------------------------------------------------------------------------

RewriteRule ^channels/$ /categories/ [R=301,L]
RewriteRule ^channels/(.+)/([^/]+)/$ /categories/$2/ [R=301,L]
RewriteRule ^channels/(.+)/$ /categories/$1/ [R=301,L]

RewriteRule ^paysites/(.+)/([^/]+)/$ /paysites/$2/ [R=301,L]

RewriteRule ^user/([^/]+)-([0-9]+)/$ /members/$2/ [R=301,L]
RewriteRule ^uploads-by-user/([0-9]+)/$ /members/$1/videos/ [R=301,L]

RewriteRule ^models/([^/]+)-([0-9]+)\.html$ /models/$1/ [R=301,L]
RewriteRule ^models/[a-z]/$ /models/ [R=301,L]
RewriteRule ^models/[a-z]/([0-9]+)/$ /models/$1/ [R=301,L]
RewriteRule ^models/rating/$ /models/ [R=301,L]
RewriteRule ^models/rating/([0-9]+)/$ /models/$1/ [R=301,L]

RewriteRule ^search/videos/([^/]+)/$ /search/$1/ [R=301,L]
RewriteRule ^search/videos/([^/]+)/([0-9]+)/$ /search/$1/$2/ [R=301,L]
RewriteRule ^search/photos/([^/]+)/$ /search/$1/ [R=301,L]
RewriteRule ^search/photos/([^/]+)/([0-9]+)/$ /search/$1/$2/ [R=301,L]
RewriteRule ^search/members/([^/]+)/$ /search/$1/ [R=301,L]
RewriteRule ^search/members/([^/]+)/([0-9]+)/$ /search/$1/$2/ [R=301,L]

RewriteRule ^contact/$ /feedback/ [R=301,L]
RewriteRule ^static/dmca\.html$ /dmca/ [R=301,L]
RewriteRule ^static/tos\.html$ /terms/ [R=301,L]
RewriteRule ^static/2257\.html$ /2257/ [R=301,L]

RewriteRule ^(.+)/day/page([0-9]+)\.html$ /$1/$2/ [R=301,L]
RewriteRule ^(.+)/week/page([0-9]+)\.html$ /$1/$2/ [R=301,L]
RewriteRule ^(.+)/month/page([0-9]+)\.html$ /$1/$2/ [R=301,L]
RewriteRule ^(.+)/day/$ /$1/ [R=301,L]
RewriteRule ^(.+)/week/$ /$1/ [R=301,L]
RewriteRule ^(.+)/month/$ /$1/ [R=301,L]
RewriteRule ^(.+)/page([0-9]+)\.html$ /$1/$2/ [R=301,L]

*/

class KvsDataMigratorFromMechbunny extends KvsDataMigrator
{
	public const OPT_CATEGORIES_BASE_URL = "mechbunny_categories_base_url"; //media/misc/cat1.jpg
	public const OPT_MODELS_BASE_URL = "mechbunny_models_base_url";         //media/misc/avatar.jpg
	public const OPT_CONTENT_SOURCES_BASE_URL = "mechbunny_cs_base_url";    // media/misc/paysite1.jpg
	public const OPT_DVDS_BASE_URL = "mechbunny_dvds_base_url";             // media/misc/dvd1.jpg
	public const OPT_THUMBS_BASE_URL = "mechbunny_thumbs_base_url";         // media/thumbs
	public const OPT_VIDEOS_BASE_URL = "mechbunny_videos_base_url";         // media/videos
	public const OPT_ALBUMS_BASE_URL = "mechbunny_albums_base_url";         // media/galleries
	public const OPT_USERS_BASE_URL = "mechbunny_users_base_url";           // media/misc/avatar.jpg

	/**
	 * @return string
	 */
	public function get_migrator_id(): string
	{
		return "mechbunny";
	}

	/**
	 * @return string
	 */
	public function get_migrator_name(): string
	{
		return "Mechbunny";
	}

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate
	{
		return new KvsDataMigratorDataToMigrate(true, true, true, true, true, true, true, true, true, true, true, true, true, false, false);
	}

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array(self::OPT_CATEGORIES_BASE_URL, self::OPT_MODELS_BASE_URL, self::OPT_CONTENT_SOURCES_BASE_URL, self::OPT_DVDS_BASE_URL, self::OPT_VIDEOS_BASE_URL, self::OPT_THUMBS_BASE_URL, self::OPT_ALBUMS_BASE_URL, self::OPT_USERS_BASE_URL);
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

		if ($this->data_to_migrate->is_dvds())
		{
			$this->query_source("ALTER TABLE dvd ADD COLUMN enabled TINYINT(1) DEFAULT '1'");
		}
		if ($this->data_to_migrate->is_videos())
		{
			$this->query_source("ALTER TABLE content ADD COLUMN dvd INT(10) DEFAULT '0'");
		}
		if ($this->data_to_migrate->is_users())
		{
			$this->query_target("ALTER TABLE {$this->kvs_config["tables_prefix"]}users ADD COLUMN migrator_salt VARCHAR(255) DEFAULT ''");
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
			$video_id = intval($p_object["video_id"]);
			$p_object["categories"] = $this->query_text_source("SELECT group_concat(n.name SEPARATOR '||') FROM content_niches cn INNER JOIN niches n ON n.record_num=cn.niche WHERE cn.content=$video_id GROUP BY cn.content");
			$p_object["models"] = $this->query_text_source("SELECT group_concat(p.name SEPARATOR '||') FROM content_pornstars cp INNER JOIN pornstars p ON p.record_num=cp.pornstar WHERE cp.content=$video_id GROUP BY cp.content");
			if (intval($p_object["user_id"]) == 0)
			{
				$p_object["user_id"] = $this->query_num_target("SELECT user_id from {$this->kvs_config["tables_prefix"]}users WHERE status_id=4 LIMIT 1");
			}
		} elseif ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			$album_id = intval($p_object["album_id"]);
			$p_object["categories"] = $this->query_text_source("SELECT group_concat(n.name SEPARATOR '||') FROM content_niches cn INNER JOIN niches n ON n.record_num=cn.niche WHERE cn.content=$album_id GROUP BY cn.content");
			$p_object["models"] = $this->query_text_source("SELECT group_concat(p.name SEPARATOR '||') FROM content_pornstars cp INNER JOIN pornstars p ON p.record_num=cp.pornstar WHERE cp.content=$album_id GROUP BY cp.content");
			if (intval($p_object["user_id"]) == 0)
			{
				$p_object["user_id"] = $this->query_num_target("SELECT user_id from {$this->kvs_config["tables_prefix"]}users WHERE status_id=4 LIMIT 1");
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
		if ($this->data_to_migrate->is_users())
		{
			$queries[] = "SELECT count(*) FROM users";
		}
		if ($this->data_to_migrate->is_categories())
		{
			$queries[] = "SELECT count(*) FROM niches WHERE enabled=1";
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$queries[] = "SELECT count(*) FROM paysites WHERE enabled=1";
		}
		if ($this->data_to_migrate->is_models())
		{
			$queries[] = "SELECT count(*) FROM pornstars";
		}
		if ($this->data_to_migrate->is_dvds())
		{
			$queries[] = "SELECT count(*) FROM dvd WHERE enabled=1";
		}
		if ($this->data_to_migrate->is_videos())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM content LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM content WHERE photos=0";
			}
		}
		if ($this->data_to_migrate->is_albums())
		{
			if ($this->get_test_mode() > 0)
			{
				$queries[] = "SELECT {$this->get_test_mode()} FROM content LIMIT 1";
			} else
			{
				$queries[] = "SELECT count(*) FROM content WHERE photos=1";
			}
		}
		if ($this->data_to_migrate->is_comments())
		{
			$queries[] = "SELECT count(*) FROM comments";
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$queries[] = "SELECT count(*) FROM favorites";
		}
		if ($this->data_to_migrate->is_messages())
		{
			$queries[] = "SELECT count(*) FROM mail WHERE recipient_deleted+sender_deleted<2";
		}
		if ($this->data_to_migrate->is_friends())
		{
			$queries[] = "SELECT count(*) FROM friends";
		}

		return $queries;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$users_base_url = $this->get_option_value(self::OPT_USERS_BASE_URL);

		$selector = "record_num AS user_id, username, username AS display_name, password AS pass, email, gender, date_joined AS added_date, description AS about_me, CASE WHEN enabled=1 THEN 2 ELSE 0 END AS status_id, country, lastlogin AS last_login_date, custom, registration_ip AS ip, salt AS migrator_salt, ";
		if ($users_base_url)
		{
			$selector .= "avatar AS avatar, ";
			$selector .= "(CASE WHEN avatar!='' THEN concat('$users_base_url/', avatar) ELSE '' END) AS avatar_url, ";
		}

		return new KvsDataMigratorMigrationParams("user_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM users) X", self::OBJECT_TYPE_USER, function ($p_data) {
			$custom = @unserialize($p_data["custom"], ['allowed_classes' => false]);
			if ($custom && is_array($custom))
			{
				$p_data['city'] = trim($custom["City"]);
				$p_data['orientation'] = trim($custom["Sexual Orientation"]);
			}
			unset($p_data["custom"]);

			$p_data["ip"] = ip2int($p_data["ip"]);

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
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$categories_base_url = $this->get_option_value(self::OPT_CATEGORIES_BASE_URL);

		$selector = "record_num AS category_id, name AS title, description, metatitle AS custom1, metadesc AS custom2, metakw AS custom3, CASE WHEN enabled=1 THEN 1 ELSE 0 END AS status_id, now() AS added_date, ";
		if ($categories_base_url)
		{
			$selector .= "concat(record_num, '.jpg') AS screenshot1, ";
			$selector .= "concat('$categories_base_url/cat', record_num, '.jpg') AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("category_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM niches WHERE enabled=1) X", self::OBJECT_TYPE_CATEGORY);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$content_sources_base_url = $this->get_option_value(self::OPT_CONTENT_SOURCES_BASE_URL);

		$selector = "record_num AS content_source_id, name AS title, url, CASE WHEN enabled=1 THEN 1 ELSE 0 END AS status_id, now() AS added_date, ";
		if ($content_sources_base_url)
		{
			$selector .= "concat(record_num, '.jpg') AS screenshot1, ";
			$selector .= "concat('$content_sources_base_url/paysite', record_num, '.jpg') AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("content_source_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM paysites WHERE enabled=1) X", self::OBJECT_TYPE_CONTENT_SOURCE);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$models_base_url = $this->get_option_value(self::OPT_MODELS_BASE_URL);

		$selector = "record_num AS model_id, name AS title, aka AS alias, biography AS description, height, weight, hair, eyes AS eye_color, measurements, views AS model_viewed, custom, now() AS added_date, ";
		if ($models_base_url)
		{
			$selector .= "thumb AS screenshot1, ";
			$selector .= "(CASE WHEN thumb!='' THEN concat('$models_base_url/', thumb) ELSE '' END) AS screenshot1_url, ";
		}

		return new KvsDataMigratorMigrationParams("model_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM pornstars) X", self::OBJECT_TYPE_MODEL, function ($p_data) {
			$custom = @unserialize($p_data["custom"], ['allowed_classes' => false]);
			if ($custom && is_array($custom))
			{
				$p_data['city'] = trim($custom["City"]);
				$p_data['state'] = trim($custom["State"]);
			}

			unset($p_data["custom"]);
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvds_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$dvds_base_url = $this->get_option_value(self::OPT_DVDS_BASE_URL);

		$selector = "record_num AS dvd_id, name AS title, description, CASE WHEN enabled=1 THEN 1 ELSE 0 END AS status_id, now() AS added_date, ";
		if ($dvds_base_url)
		{
			$selector .= "concat(record_num, '.jpg') AS cover1_front, ";
			$selector .= "concat('$dvds_base_url/dvd', record_num, '.jpg') AS cover1_front_url, ";
		}

		return new KvsDataMigratorMigrationParams("dvd_id", "SELECT * FROM (SELECT " . trim($selector, ", ") . " FROM dvd WHERE enabled=1) X", self::OBJECT_TYPE_DVD);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$videos_base_url = $this->get_option_value(self::OPT_VIDEOS_BASE_URL);
		if (!$videos_base_url)
		{
			$this->error("Videos base URL option is empty");
			return null;
		}

		$thumbs_base_url = $this->get_option_value(self::OPT_THUMBS_BASE_URL);

		$selector = "record_num AS video_id, submitter AS user_id, title, description, slug AS dir, paysite AS content_source_id, dvd AS dvd_id, keywords AS tags, length AS duration, date_added AS added_date, scheduled_date AS post_date, (SELECT max(views) FROM content_views where content=record_num) AS video_viewed, (SELECT floor(max(total_value)/100*5) FROM ratings where content=record_num) AS rating, greatest((SELECT max(total_votes) FROM ratings where content=record_num),1) AS rating_amount, CASE WHEN enabled=1 THEN 1 ELSE 0 END AS status_id, embed, CASE WHEN source_thumb_url!='' THEN source_thumb_url WHEN embed!='' THEN concat('$thumbs_base_url', '/embedded/', record_num, '.jpg') ELSE concat('$thumbs_base_url', '/', substring(orig_filename,1,1), '/', substring(orig_filename,2,1), '/', substring(orig_filename,3,1), '/', substring(orig_filename,4,1), '/', substring(orig_filename,5,1), '/', orig_filename, '/', orig_filename, '-', main_thumb, 'b.jpg') END AS screen_url, CASE WHEN filename!='' THEN concat('$videos_base_url', '/', substring(filename,1,1), '/', substring(filename,2,1), '/', substring(filename,3,1), '/', substring(filename,4,1), '/', substring(filename,5,1), '/', filename) ELSE '' END AS file_download_url";

		return new KvsDataMigratorMigrationParams("video_id", "SELECT * FROM (SELECT $selector FROM content WHERE photos=0) X", self::OBJECT_TYPE_VIDEO, function ($p_data) {
			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);
			if ($p_data["post_date"] == "0000-00-00" || $p_data["post_date"] == "0000-00-00 00:00:00")
			{
				$p_data["post_date"] = $p_data["added_date"];
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$albums_base_url = $this->get_option_value(self::OPT_ALBUMS_BASE_URL);
		if (!$albums_base_url)
		{
			$this->error("Albums base URL option is empty");
			return null;
		}

		$selector = "record_num AS album_id, submitter AS user_id, title, description, slug AS dir, paysite AS content_source_id, keywords AS tags, date_added AS added_date, scheduled_date AS post_date, (SELECT max(views) FROM content_views where content=record_num) AS album_viewed, (SELECT floor(max(total_value)/100*5) FROM ratings where content=record_num) AS rating, greatest((SELECT max(total_votes) FROM ratings where content=record_num),1) AS rating_amount, CASE WHEN enabled=1 THEN 1 ELSE 0 END AS status_id";

		return new KvsDataMigratorMigrationParams("album_id", "SELECT * FROM (SELECT $selector FROM content WHERE photos=1) X", self::OBJECT_TYPE_ALBUM, function ($p_data) {
			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);
			if ($p_data["post_date"] == "0000-00-00" || $p_data["post_date"] == "0000-00-00 00:00:00")
			{
				$p_data["post_date"] = $p_data["added_date"];
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
		$albums_base_url = $this->get_option_value(self::OPT_ALBUMS_BASE_URL);
		if (!$albums_base_url)
		{
			$this->error("Albums base URL option is empty");
			return null;
		}

		$album_base_url = $this->query_text_source("SELECT CASE WHEN filename!='' THEN concat('$albums_base_url', '/', filename) ELSE '' END FROM content WHERE record_num=$p_album_id");
		if (!$album_base_url)
		{
			$this->error("Album $p_album_id has no filename");
			return null;
		}

		return new KvsDataMigratorMigrationParams("image_id", "SELECT record_num AS image_id, concat('$album_base_url/', filename) AS image_url FROM images WHERE gallery=$p_album_id", self::OBJECT_TYPE_ALBUM, function ($p_data) {
			$p_data["tags"] = str_replace(",", "||", $p_data["tags"]);
			if ($p_data["post_date"] == "0000-00-00" || $p_data["post_date"] == "0000-00-00 00:00:00")
			{
				$p_data["post_date"] = $p_data["added_date"];
			}
			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		$selector = "c.record_num AS comment_id, c.content AS object_id, CASE WHEN content.photos=1 THEN 2 ELSE 1 END AS object_type_id, content.title AS object_title, c.userid AS user_id, u.username AS username, 1 AS is_approved, c.comment, c.timestamp AS added_date, c.ip";
		$projector = "comments c INNER JOIN content ON c.content=content.record_num INNER JOIN users u ON c.userid=u.record_num";

		return new KvsDataMigratorMigrationParams("comment_id", "SELECT * FROM (SELECT $selector FROM $projector) X", self::OBJECT_TYPE_COMMENT, function ($p_data) {
			if ($p_data["added_date"] > 0)
			{
				$p_data["added_date"] = date("Y-m-d H:i:s", $p_data["added_date"]);
			} else
			{
				$p_data["added_date"] = date("Y-m-d H:i:s");
			}
			$p_data["comment"] = str_replace(array("\\n", "\\r"), array("\n", ""), $p_data["comment"]);
			$p_data["ip"] = ip2int($p_data["ip"]);

			return $p_data;
		});
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT f.content AS video_id, f.user AS user_id, now() AS added_date FROM favorites f INNER JOIN content content ON f.content=content.record_num WHERE content.photos=0", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT f.content AS album_id, f.user AS user_id, now() AS added_date FROM favorites f INNER JOIN content content ON f.content=content.record_num WHERE content.photos=1", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT record_num AS message_id, from_user AS user_from_id, to_user AS user_id, body AS message, date_sent AS added_date, recipient_read AS is_read, CASE WHEN recipient_read=1 THEN date_sent ELSE '0000-00-00 00:00:00' END AS read_date, recipient_deleted AS is_hidden_from_user_id, sender_deleted AS is_hidden_from_user_from_id FROM mail WHERE recipient_deleted+sender_deleted<2 ORDER BY record_num ASC", 0);
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return new KvsDataMigratorMigrationParams(null, "SELECT user AS user_id, friend AS friend_id, date_added AS added_date, approved AS is_approved, CASE WHEN approved=1 THEN date_added ELSE '0000-00-00 00:00:00' END AS approved_date FROM friends", 0);
	}
}