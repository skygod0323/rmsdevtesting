<?php
/* © Kernel Video Sharing
   https://kernel-video-sharing.com
*/

class KvsDataMigratorMigrationParams
{
	/** @var string */
	private $table_key_field;

	/** @var string */
	private $select_query;

	/** @var int */
	private $object_type;

	/** @var callable */
	private $callback;

	/**
	 * @param string|null $p_table_key_field
	 * @param string $p_select_query
	 * @param int $p_object_type
	 * @param callable $p_callback
	 */
	public function __construct(?string $p_table_key_field, string $p_select_query, int $p_object_type, callable $p_callback = null)
	{
		$this->table_key_field = $p_table_key_field;
		$this->select_query = $p_select_query;
		$this->object_type = $p_object_type;
		$this->callback = $p_callback;
	}

	/**
	 * @return string|null
	 */
	public function get_table_key_field(): ?string
	{
		return $this->table_key_field;
	}

	/**
	 * @return string
	 */
	public function get_select_query(): string
	{
		return $this->select_query;
	}

	/**
	 * @return int
	 */
	public function get_object_type(): int
	{
		return $this->object_type;
	}

	/**
	 * @return callable|null
	 */
	public function get_callback(): ?callable
	{
		return $this->callback;
	}
}

class KvsDataMigratorSummaryItem
{
	/** @var int */
	private $total;

	/** @var int */
	private $inserted;

	/** @var int */
	private $updated;

	/** @var int */
	private $errors;

	/**
	 * @param int $p_total
	 * @param int $p_inserted
	 * @param int $p_updated
	 * @param int $p_errors
	 */
	public function __construct(int $p_total, int $p_inserted, int $p_updated, int $p_errors)
	{
		$this->total = $p_total;
		$this->inserted = $p_inserted;
		$this->updated = $p_updated;
		$this->errors = $p_errors;
	}

	/**
	 * @return array
	 */
	public function to_array(): array
	{
		return array(
			"total" => $this->total,
			"inserted" => $this->inserted,
			"updated" => $this->updated,
			"errors" => $this->errors
		);
	}
}

class KvsDataMigratorDataToMigrate
{
	/** @var bool */
	private $tags;

	/** @var bool */
	private $categories;

	/** @var bool */
	private $models;

	/** @var bool */
	private $content_sources;

	/** @var bool */
	private $dvds;

	/** @var bool */
	private $videos;

	/** @var bool */
	private $videos_screenshots;

	/** @var bool */
	private $albums;

	/** @var bool */
	private $comments;

	/** @var bool */
	private $users;

	/** @var bool */
	private $favourites;

	/** @var bool */
	private $friends;

	/** @var bool */
	private $messages;

	/** @var bool */
	private $subscriptions;

	/** @var bool */
	private $playlists;

	/**
	 * @param bool $p_tags
	 * @param bool $p_categories
	 * @param bool $p_models
	 * @param bool $p_content_sources
	 * @param bool $p_dvds
	 * @param bool $p_videos
	 * @param bool $p_videos_screenshots
	 * @param bool $p_albums
	 * @param bool $p_comments
	 * @param bool $p_users
	 * @param bool $p_favourites
	 * @param bool $p_friends
	 * @param bool $p_messages
	 * @param bool $p_subscriptions
	 * @param bool $p_playlists
	 */
	public function __construct(
		bool $p_tags = false,
		bool $p_categories = false,
		bool $p_models = false,
		bool $p_content_sources = false,
		bool $p_dvds = false,
		bool $p_videos = false,
		bool $p_videos_screenshots = false,
		bool $p_albums = false,
		bool $p_comments = false,
		bool $p_users = false,
		bool $p_favourites = false,
		bool $p_friends = false,
		bool $p_messages = false,
		bool $p_subscriptions = false,
		bool $p_playlists = false)
	{
		$this->tags = $p_tags;
		$this->categories = $p_categories;
		$this->models = $p_models;
		$this->content_sources = $p_content_sources;
		$this->dvds = $p_dvds;
		$this->videos = $p_videos;
		$this->videos_screenshots = $p_videos_screenshots;
		$this->albums = $p_albums;
		$this->comments = $p_comments;
		$this->users = $p_users;
		$this->favourites = $p_favourites;
		$this->friends = $p_friends;
		$this->messages = $p_messages;
		$this->subscriptions = $p_subscriptions;
		$this->playlists = $p_playlists;

		if (!$this->users)
		{
			$this->favourites = false;
			$this->friends = false;
			$this->messages = false;
			$this->subscriptions = false;
			$this->playlists = false;
		}
	}

	/**
	 * @return bool
	 */
	public function is_tags(): bool
	{
		return $this->tags;
	}

	/**
	 * @return bool
	 */
	public function is_categories(): bool
	{
		return $this->categories;
	}

	/**
	 * @return bool
	 */
	public function is_models(): bool
	{
		return $this->models;
	}

	/**
	 * @return bool
	 */
	public function is_content_sources(): bool
	{
		return $this->content_sources;
	}

	/**
	 * @return bool
	 */
	public function is_dvds(): bool
	{
		return $this->dvds;
	}

	/**
	 * @return bool
	 */
	public function is_videos(): bool
	{
		return $this->videos;
	}

	/**
	 * @return bool
	 */
	public function is_videos_screenshots(): bool
	{
		return $this->videos_screenshots;
	}

	/**
	 * @return bool
	 */
	public function is_albums(): bool
	{
		return $this->albums;
	}

	/**
	 * @return bool
	 */
	public function is_comments(): bool
	{
		return $this->comments;
	}

	/**
	 * @return bool
	 */
	public function is_users(): bool
	{
		return $this->users;
	}

	/**
	 * @return bool
	 */
	public function is_favourites(): bool
	{
		return $this->favourites;
	}

	/**
	 * @return bool
	 */
	public function is_friends(): bool
	{
		return $this->friends;
	}

	/**
	 * @return bool
	 */
	public function is_messages(): bool
	{
		return $this->messages;
	}

	/**
	 * @return bool
	 */
	public function is_subscriptions(): bool
	{
		return $this->subscriptions;
	}

	/**
	 * @return bool
	 */
	public function is_playlists(): bool
	{
		return $this->playlists;
	}

	/**
	 * @return array
	 */
	public function to_array(): array
	{
		return array(
			"tags" => $this->is_tags() ? 1 : 0,
			"categories" => $this->is_categories() ? 1 : 0,
			"models" => $this->is_models() ? 1 : 0,
			"content_sources" => $this->is_content_sources() ? 1 : 0,
			"dvds" => $this->is_dvds() ? 1 : 0,
			"videos" => $this->is_videos() ? 1 : 0,
			"videos_screenshots" => $this->is_videos_screenshots() ? 1 : 0,
			"albums" => $this->is_albums() ? 1 : 0,
			"comments" => $this->is_comments() ? 1 : 0,
			"users" => $this->is_users() ? 1 : 0,
			"favourites" => $this->is_favourites() ? 1 : 0,
			"friends" => $this->is_friends() ? 1 : 0,
			"messages" => $this->is_messages() ? 1 : 0,
			"subscriptions" => $this->is_subscriptions() ? 1 : 0,
			"playlists" => $this->is_playlists() ? 1 : 0,
		);
	}
}

abstract class KvsDataMigrator
{
	public const OBJECT_TYPE_VIDEO = 1;
	public const OBJECT_TYPE_ALBUM = 2;
	public const OBJECT_TYPE_CONTENT_SOURCE = 3;
	public const OBJECT_TYPE_MODEL = 4;
	public const OBJECT_TYPE_DVD = 5;
	public const OBJECT_TYPE_CATEGORY = 6;
	public const OBJECT_TYPE_CATEGORY_GROUP = 7;
	public const OBJECT_TYPE_CONTENT_SOURCE_GROUP = 8;
	public const OBJECT_TYPE_TAG = 9;
	public const OBJECT_TYPE_DVD_GROUP = 10;
	public const OBJECT_TYPE_POST_TYPE = 11;
	public const OBJECT_TYPE_POST = 12;
	public const OBJECT_TYPE_PLAYLIST = 13;
	public const OBJECT_TYPE_MODEL_GROUP = 14;
	public const OBJECT_TYPE_USER = 20;
	public const OBJECT_TYPE_COMMENT = 101;

	public const RESULT_ADDED = 1;
	public const RESULT_UPDATED = 2;
	public const RESULT_ERROR = 3;
	public const RESULT_SKIPPED = 4;
	public const RESULT_SKIPPED_LIMIT = 5;

	public const USERS_CUSTOM_FIELDS_TEXT = 10;

	public const CATEGORY_GROUPS_CUSTOM_FIELDS_TEXT = 3;

	public const CATEGORIES_CUSTOM_FIELDS_TEXT = 10;
	public const CATEGORIES_CUSTOM_FIELDS_FILE = 5;

	public const TAGS_CUSTOM_FIELDS_TEXT = 5;

	public const CONTENT_SOURCE_GROUPS_CUSTOM_FIELDS_TEXT = 5;

	public const CONTENT_SOURCES_CUSTOM_FIELDS_TEXT = 10;
	public const CONTENT_SOURCES_CUSTOM_FIELDS_FILE = 10;

	public const MODELS_CUSTOM_FIELDS_TEXT = 10;
	public const MODELS_CUSTOM_FIELDS_FILE = 5;

	public const DVDS_CUSTOM_FIELDS_TEXT = 10;
	public const DVDS_CUSTOM_FIELDS_FILE = 5;

	/** @var array */
	protected $kvs_config;

	/** @var int */
	protected $admin_user_id;

	/** @var string */
	protected $old_path;

	/** @var string */
	protected $old_url;

	/** @var KvsDataMigratorDataToMigrate */
	protected $data_to_migrate;

	/** @var bool */
	private $override_existing_objects;

	/** @var bool */
	private $upload_hotlinked_videos;

	/** @var array */
	private $options;

	/** @var int */
	private $test_mode;

	/** @var int */
	private $test_mode_state_videos;

	/** @var int */
	private $test_mode_state_albums;

	/** @var callable */
	private $progress_callback;

	/** @var int */
	private $progress_current;

	/** @var int */
	private $progress_total;

	/** @var array */
	private $summary;

	/** @var mysqli */
	private $mysql_link_source;

	/** @var mysqli */
	private $mysql_link_target;

	/**
	 * @param array $p_kvs_config
	 */
	public function __construct(array $p_kvs_config)
	{
		$this->kvs_config = $p_kvs_config;
	}

	/**
	 * @return string
	 */
	abstract public function get_migrator_id(): string;

	/**
	 * @return string
	 */
	abstract public function get_migrator_name(): string;

	/**
	 * @return KvsDataMigratorDataToMigrate
	 */
	abstract public function get_migrator_supported_data(): KvsDataMigratorDataToMigrate;

	/**
	 * @return array
	 */
	public function get_migrator_additional_options(): array
	{
		return array();
	}

	/**
	 * @param string $p_option_name
	 *
	 * @return string
	 */
	public function get_option_value(string $p_option_name): string
	{
		if (is_array($this->options) && isset($this->options[$p_option_name]))
		{
			return $this->options[$p_option_name];
		}
		return "";
	}

	/**
	 * @return bool
	 */
	public function is_migrator_default(): bool
	{
		return false;
	}

	/**
	 * @param int $p_admin_user_id
	 * @param string $p_old_path
	 * @param string $p_old_url
	 * @param string $p_old_mysql_url
	 * @param string $p_old_mysql_port
	 * @param string $p_old_mysql_user
	 * @param string $p_old_mysql_pass
	 * @param string $p_old_mysql_name
	 * @param string $p_old_mysql_charset
	 * @param KvsDataMigratorDataToMigrate $p_data_to_migrate
	 * @param bool $p_override_existing_objects
	 * @param bool $p_upload_hotlinked_videos
	 * @param int $p_test_mode
	 * @param array $p_options
	 * @param callable $p_progress_callback
	 *
	 * @return array
	 */
	public function start(int $p_admin_user_id, string $p_old_path, string $p_old_url, string $p_old_mysql_url, string $p_old_mysql_port, string $p_old_mysql_user, string $p_old_mysql_pass, string $p_old_mysql_name, string $p_old_mysql_charset, KvsDataMigratorDataToMigrate $p_data_to_migrate, bool $p_override_existing_objects, bool $p_upload_hotlinked_videos, int $p_test_mode, array $p_options, callable $p_progress_callback): array
	{
		$this->admin_user_id = $p_admin_user_id;
		$this->old_path = $p_old_path;
		$this->old_url = $p_old_url;

		$this->data_to_migrate = $p_data_to_migrate;
		if (!$this->data_to_migrate)
		{
			$this->data_to_migrate = new KvsDataMigratorDataToMigrate();
		}
		$this->override_existing_objects = $p_override_existing_objects;
		$this->upload_hotlinked_videos = $p_upload_hotlinked_videos;
		$this->test_mode = intval($p_test_mode);
		$this->options = $p_options;

		$this->test_mode_state_videos = 0;
		$this->test_mode_state_albums = 0;

		$this->summary = array();
		$this->progress_callback = $p_progress_callback;
		$this->progress_total = 0;
		$this->progress_current = 0;

		$this->mysql_link_source = new mysqli($p_old_mysql_url, $p_old_mysql_user, $p_old_mysql_pass, $p_old_mysql_name, intval($p_old_mysql_port));
		if ($this->mysql_link_source->connect_error)
		{
			$error_message = $this->mysql_link_source->connect_error;
			$this->error("Source database connection error: $error_message");
			return $this->summary;
		}
		$this->mysql_link_source->set_charset($p_old_mysql_charset);
		$this->mysql_link_source->query("SET wait_timeout=86400");
		$this->mysql_link_source->query("SET SQL_BIG_SELECTS=1");
		$this->mysql_link_source->query("SET SESSION sql_mode=''");

		require_once "{$this->kvs_config["project_path"]}/admin/include/setup_db.php";
		$this->mysql_link_target = new mysqli(DB_HOST, DB_LOGIN, DB_PASS, DB_DEVICE);
		if ($this->mysql_link_target->connect_error)
		{
			$error_message = $this->mysql_link_target->connect_error;
			$this->error("Target database connection error: $error_message");
			return $this->summary;
		}
		$this->mysql_link_target->set_charset("utf8");
		$this->mysql_link_target->query("SET wait_timeout=86400");
		$this->mysql_link_target->query("SET SESSION sql_mode='NO_ENGINE_SUBSTITUTION'");

		if (!$this->pre_start_hook())
		{
			$this->error("Migration cancelled");
			return $this->summary;
		}

		$this->summary["start_time"] = time();
		$this->summary["start_memory"] = memory_get_peak_usage();
		$this->info("Migration started");

		if ($this->test_mode > 0)
		{
			$this->warning("Test mode is enabled: max $this->test_mode videos / albums");
		}

		foreach ($this->options as $option => $value)
		{
			$this->info("Additional option: $option = $value");
		}

		$this->calc_progress();
		if ($this->data_to_migrate->is_users())
		{
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users WHERE status_id=4");

			$this->migrate_users();

			$i = 1;
			while (true)
			{
				if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users where user_id=$i") == 0)
				{
					$this->query_target("INSERT INTO {$this->kvs_config["tables_prefix"]}users SET user_id=$i, status_id=4, email='email2', username='MigratorAnonymous', display_name='Anonymous', added_date=now()");
					break;
				}
				$i++;
			}
		}
		if ($this->data_to_migrate->is_categories())
		{
			$this->migrate_category_groups();
			$this->migrate_categories();
		}
		if ($this->data_to_migrate->is_tags())
		{
			$this->migrate_tags();
		}
		if ($this->data_to_migrate->is_content_sources())
		{
			$this->migrate_content_source_groups();
			$this->migrate_content_sources();
		}
		if ($this->data_to_migrate->is_models())
		{
			$this->migrate_model_groups();
			$this->migrate_models();
		}
		if ($this->data_to_migrate->is_dvds())
		{
			$this->migrate_dvd_groups();
			$this->migrate_dvds();
		}
		if ($this->data_to_migrate->is_videos())
		{
			$this->migrate_videos();
		}
		if ($this->data_to_migrate->is_albums())
		{
			$this->migrate_albums();
		}
		if ($this->data_to_migrate->is_favourites())
		{
			$this->migrate_favourites();
		}
		if ($this->data_to_migrate->is_friends())
		{
			$this->migrate_friends();
		}
		if ($this->data_to_migrate->is_messages())
		{
			$this->migrate_messages();
		}
		if ($this->data_to_migrate->is_playlists())
		{
			$this->migrate_playlists();
		}
		if ($this->data_to_migrate->is_subscriptions())
		{
			$this->migrate_subscriptions();
		}
		if ($this->data_to_migrate->is_comments())
		{
			$this->migrate_comments();
		}

		$this->info();
		$this->info("Migration finished");
		$this->summary["end_time"] = time();
		$this->summary["end_memory"] = memory_get_peak_usage();
		$this->summary["duration"] = $this->summary["end_time"] - $this->summary["start_time"];
		$this->summary["memory_usage"] = $this->summary["end_memory"] - $this->summary["start_memory"];

		$this->post_finish_hook();

		return $this->summary;
	}

	/**
	 * @return int
	 */
	protected function get_test_mode(): int
	{
		return $this->test_mode;
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return int
	 */
	protected function migrate_object(array $p_object, int $p_object_type): int
	{
		if ($p_object_type == self::OBJECT_TYPE_VIDEO)
		{
			if ($this->test_mode > 0)
			{
				if ($this->test_mode_state_videos >= $this->test_mode)
				{
					return self::RESULT_SKIPPED_LIMIT;
				}
				$this->test_mode_state_videos++;
			}
			return $this->migrate_video($p_object);
		}
		if ($p_object_type == self::OBJECT_TYPE_ALBUM)
		{
			if ($this->test_mode > 0)
			{
				if ($this->test_mode_state_albums >= $this->test_mode)
				{
					return self::RESULT_SKIPPED_LIMIT;
				}
				$this->test_mode_state_albums++;
			}
			return $this->migrate_album($p_object);
		}

		if (in_array($p_object_type, array(
				self::OBJECT_TYPE_CATEGORY,
				self::OBJECT_TYPE_CATEGORY_GROUP,
				self::OBJECT_TYPE_MODEL,
				self::OBJECT_TYPE_MODEL_GROUP,
				self::OBJECT_TYPE_CONTENT_SOURCE,
				self::OBJECT_TYPE_CONTENT_SOURCE_GROUP,
				self::OBJECT_TYPE_DVD,
				self::OBJECT_TYPE_DVD_GROUP,
				self::OBJECT_TYPE_TAG,
				self::OBJECT_TYPE_USER)
		))
		{
			return $this->migrate_standard_object($p_object, $p_object_type);
		}

		if ($p_object_type == self::OBJECT_TYPE_COMMENT)
		{
			return $this->migrate_comment($p_object);
		}

		if ($p_object_type == self::OBJECT_TYPE_PLAYLIST)
		{
			return $this->migrate_playlist($p_object);
		}

		$this->error("Unknown object type: $p_object_type");
		return self::RESULT_ERROR;
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return int
	 */
	protected function migrate_standard_object(array $p_object, int $p_object_type): int
	{
		$files = array();
		$unset = array();
		$resize = array();
		$tags = "";
		$tags_table_name = "";
		$categories = "";
		$categories_table_name = "";

		switch ($p_object_type)
		{
			case self::OBJECT_TYPE_USER:
				$key_field = "user_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}users";

				$dir_path = floor($p_object[$key_field] / 1000) * 1000;
				if ($p_object["avatar"] != "")
				{
					$p_object["avatar"] = "$dir_path/$p_object[avatar]";
				}

				$files["avatar"] = "{$this->kvs_config["content_path_avatars"]}";

				if ($p_object["country"] != "")
				{
					$country_title = $this->escape_string($p_object["country"]);
					$p_object["country_id"] = intval($this->query_num_target("SELECT country_id FROM {$this->kvs_config["tables_prefix"]}list_countries WHERE title='$country_title' LIMIT 1"));
					if ($p_object["country_id"] == 0 && strpos($country_title, ",") !== false)
					{
						$country_titles_list = explode(",", $country_title);
						foreach ($country_titles_list as $country_title)
						{
							$p_object["country_id"] = intval($this->query_num_target("SELECT country_id FROM {$this->kvs_config["tables_prefix"]}list_countries WHERE title='$country_title' LIMIT 1"));
							if ($p_object["country_id"] > 0)
							{
								break;
							}
						}
					}
					if ($p_object["country"] == "US" || $p_object["country"] == "USA")
					{
						$p_object["country_id"] = 2270;
					}
				}

				if ($p_object["gender"] != "")
				{
					switch (mb_convert_case($p_object["gender"], MB_CASE_LOWER, "UTF-8"))
					{
						case "m":
						case "male":
						case "man":
							$p_object["gender_id"] = 1;
							break;
						case "f":
						case "female":
						case "woman":
							$p_object["gender_id"] = 2;
							break;
						case "pair":
						case "couple":
							$p_object["gender_id"] = 3;
							break;
						case "t":
						case "trans":
						case "transsexual":
							$p_object["gender_id"] = 4;
							break;
					}
				}

				if ($p_object["relationship_status"] != "")
				{
					switch (mb_convert_case($p_object["relationship_status"], MB_CASE_LOWER, "UTF-8"))
					{
						case "single":
							$p_object["relationship_status_id"] = 1;
							break;
						case "married":
						case "taken":
							$p_object["relationship_status_id"] = 2;
							break;
						case "open":
							$p_object["relationship_status_id"] = 3;
							break;
						case "divorced":
							$p_object["relationship_status_id"] = 4;
							break;
						case "widow":
						case "widowed":
							$p_object["relationship_status_id"] = 5;
							break;
					}
				}

				if ($p_object["orientation"] != "")
				{
					switch (mb_convert_case($p_object["orientation"], MB_CASE_LOWER, "UTF-8"))
					{
						case "not_sure":
							$p_object["orientation_id"] = 1;
							break;
						case "straight":
						case "heterosexual":
							$p_object["orientation_id"] = 2;
							break;
						case "gay":
							$p_object["orientation_id"] = 3;
							break;
						case "lesbian":
							$p_object["orientation_id"] = 4;
							break;
						case "bisexual":
						case "boys+girls":
							$p_object["orientation_id"] = 5;
							break;
						case "boys":
							if ($p_object["gender_id"] == 2)
							{
								$p_object["orientation_id"] = 2;
							} else
							{
								$p_object["orientation_id"] = 3;
							}
							break;
						case "girls":
							if ($p_object["gender_id"] == 2)
							{
								$p_object["orientation_id"] = 4;
							} else
							{
								$p_object["orientation_id"] = 2;
							}
							break;
						case "homosexual":
							if ($p_object["gender_id"] == 2)
							{
								$p_object["orientation_id"] = 3;
							} else
							{
								$p_object["orientation_id"] = 4;
							}
							break;
					}
				}

				$unset = array("country", "gender", "relationship_status", "orientation");
				break;
			case self::OBJECT_TYPE_DVD_GROUP:
				$key_field = "dvd_group_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}dvds_groups";

				$files["cover1"] = "{$this->kvs_config["content_path_dvds"]}/groups/$p_object[$key_field]";
				$files["cover2"] = "{$this->kvs_config["content_path_dvds"]}/groups/$p_object[$key_field]";

				$tags = $p_object["tags"];
				$tags_table_name = "{$this->kvs_config["tables_prefix"]}tags_dvds_groups";
				$categories = $p_object["categories"];
				$categories_table_name = "{$this->kvs_config["tables_prefix"]}categories_dvds_groups";

				$unset = array("tags", "categories");

				break;
			case self::OBJECT_TYPE_TAG:
				$key_field = "tag_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}tags";

				break;
			case self::OBJECT_TYPE_CONTENT_SOURCE_GROUP:
				$key_field = "content_source_group_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}content_sources_groups";

				break;
			case self::OBJECT_TYPE_CATEGORY_GROUP:
				$key_field = "category_group_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}categories_groups";

				$files["screenshot1"] = "{$this->kvs_config["content_path_categories"]}/groups/$p_object[$key_field]";
				$files["screenshot2"] = "{$this->kvs_config["content_path_categories"]}/groups/$p_object[$key_field]";

				break;
			case self::OBJECT_TYPE_CATEGORY:
				$key_field = "category_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}categories";

				$files["screenshot1"] = "{$this->kvs_config["content_path_categories"]}/$p_object[$key_field]";
				$files["screenshot2"] = "{$this->kvs_config["content_path_categories"]}/$p_object[$key_field]";
				for ($i = 1; $i <= self::CATEGORIES_CUSTOM_FIELDS_FILE; $i++)
				{
					$files["custom_file$i"] = "{$this->kvs_config["content_path_categories"]}/$p_object[$key_field]";
				}

				break;
			case self::OBJECT_TYPE_DVD:
				$key_field = "dvd_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}dvds";

				$files["cover1_front"] = "{$this->kvs_config["content_path_dvds"]}/$p_object[$key_field]";
				$files["cover1_back"] = "{$this->kvs_config["content_path_dvds"]}/$p_object[$key_field]";
				$files["cover2_front"] = "{$this->kvs_config["content_path_dvds"]}/$p_object[$key_field]";
				$files["cover2_back"] = "{$this->kvs_config["content_path_dvds"]}/$p_object[$key_field]";
				for ($i = 1; $i <= self::DVDS_CUSTOM_FIELDS_FILE; $i++)
				{
					$files["custom_file$i"] = "{$this->kvs_config["content_path_dvds"]}/$p_object[$key_field]";
				}

				$tags = $p_object["tags"];
				$tags_table_name = "{$this->kvs_config["tables_prefix"]}tags_dvds";
				$categories = $p_object["categories"];
				$categories_table_name = "{$this->kvs_config["tables_prefix"]}categories_dvds";

				$unset = array("tags", "categories");

				break;
			case self::OBJECT_TYPE_MODEL:
				$key_field = "model_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}models";

				$files["screenshot1"] = "{$this->kvs_config["content_path_models"]}/$p_object[$key_field]";
				$files["screenshot2"] = "{$this->kvs_config["content_path_models"]}/$p_object[$key_field]";
				for ($i = 1; $i <= self::MODELS_CUSTOM_FIELDS_FILE; $i++)
				{
					$files["custom_file$i"] = "{$this->kvs_config["content_path_models"]}/$p_object[$key_field]";
				}

				$resize["screenshot1"] = $this->query_text_target("SELECT value FROM {$this->kvs_config["tables_prefix"]}options WHERE variable='MODELS_SCREENSHOT_1_SIZE'");
				if ($this->query_num_target("SELECT value FROM {$this->kvs_config["tables_prefix"]}options WHERE variable='MODELS_SCREENSHOT_OPTION'") > 0)
				{
					$resize["screenshot2"] = $this->query_text_target("SELECT value FROM {$this->kvs_config["tables_prefix"]}options WHERE variable='MODELS_SCREENSHOT_2_SIZE'");
				} else
				{
					unset($files["screenshot2"], $p_object["screenshot2"], $p_object["screenshot2_url"], $p_object["screenshot2_path"]);
				}

				if ($p_object["country"] != "")
				{
					$country_title = $this->escape_string($p_object["country"]);
					$p_object["country_id"] = intval($this->query_num_target("SELECT country_id FROM {$this->kvs_config["tables_prefix"]}list_countries WHERE title='$country_title' LIMIT 1"));
					if ($p_object["country_id"] == 0 && strpos($country_title, ",") !== false)
					{
						$country_titles_list = explode(",", $country_title);
						foreach ($country_titles_list as $country_title)
						{
							$p_object["country_id"] = intval($this->query_num_target("SELECT country_id FROM {$this->kvs_config["tables_prefix"]}list_countries WHERE title='$country_title' LIMIT 1"));
							if ($p_object["country_id"] > 0)
							{
								break;
							}
						}
					}
					if ($p_object["country"] == "US" || $p_object["country"] == "USA")
					{
						$p_object["country_id"] = 2270;
					}
				}

				if ($p_object["hair"] != "")
				{
					switch (mb_convert_case($p_object["hair"], MB_CASE_LOWER, "UTF-8"))
					{
						case "black":
						case "brunette":
						case "черные":
						case "чёрные":
						case "брюнетка":
						case "брюнет":
							$p_object["hair_id"] = 1;
							break;
						case "dark":
						case "темные":
						case "тёмные":
							$p_object["hair_id"] = 2;
							break;
						case "red":
						case "auburn":
						case "красные":
						case "рыжие":
						case "рыжая":
						case "рыжий":
							$p_object["hair_id"] = 3;
							break;
						case "brown":
						case "коричневые":
							$p_object["hair_id"] = 4;
							break;
						case "blond":
						case "blonde":
						case "белые":
						case "светлые":
						case "блондинка":
						case "блондин":
							$p_object["hair_id"] = 5;
							break;
						case "grey":
						case "gray":
						case "серые":
						case "пепельные":
							$p_object["hair_id"] = 6;
							break;
						case "bald":
						case "нет волос":
						case "лысый":
						case "лысая":
							$p_object["hair_id"] = 7;
							break;
						case "wig":
						case "парик":
							$p_object["hair_id"] = 8;
							break;
					}
				}

				if ($p_object["eye_color"] != "")
				{
					switch (mb_convert_case($p_object["eye_color"], MB_CASE_LOWER, "UTF-8"))
					{
						case "blue":
						case "blue/green":
						case "голубые":
						case "голубой":
							$p_object["eye_color_id"] = 1;
							break;
						case "brown":
						case "dark brown":
						case "brown-green":
						case "brownb":
						case "light brown":
						case "коричневые":
						case "коричневый":
						case "карие":
						case "карий":
							$p_object["eye_color_id"] = 5;
							break;
						case "green":
						case "gree":
						case "green/blue":
						case "зеленые":
						case "зелёные":
						case "зеленый":
						case "зелёный":
							$p_object["eye_color_id"] = 3;
							break;
						case "hazel":
						case "болотные":
						case "болотный":
							$p_object["eye_color_id"] = 6;
							break;
						case "black":
						case "черные":
						case "черный":
						case "чёрные":
						case "чёрный":
							$p_object["eye_color_id"] = 7;
							break;
						case "grey":
						case "серые":
						case "серый":
							$p_object["eye_color_id"] = 2;
							break;
						case "amber":
						case "янтарные":
						case "янтарный":
							$p_object["eye_color_id"] = 4;
							break;
					}
				}

				if ($p_object["gender"] != "")
				{
					switch (mb_convert_case($p_object["gender"], MB_CASE_LOWER, "UTF-8"))
					{
						case "f":
						case "female":
						case "woman":
						case "ж":
						case "женский":
						case "женщина":
							$p_object["gender_id"] = 0;
							break;
						case "m":
						case "male":
						case "man":
						case "м":
						case "мужской":
						case "мужчина":
							$p_object["gender_id"] = 1;
							break;
						case "o":
						case "other":
						case "trans":
						case "др":
						case "другой":
						case "транс":
							$p_object["gender_id"] = 2;
							break;
					}
				}

				$tags = $p_object["tags"];
				$tags_table_name = "{$this->kvs_config["tables_prefix"]}tags_models";
				$categories = $p_object["categories"];
				$categories_table_name = "{$this->kvs_config["tables_prefix"]}categories_models";

				$unset = array("country", "hair", "eye_color", "gender", "tags", "categories");

				break;
			case self::OBJECT_TYPE_MODEL_GROUP:
				$key_field = "model_group_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}models_groups";

				$files["screenshot1"] = "{$this->kvs_config["content_path_models"]}/groups/$p_object[$key_field]";
				$files["screenshot2"] = "{$this->kvs_config["content_path_models"]}/groups/$p_object[$key_field]";

				break;
			case self::OBJECT_TYPE_CONTENT_SOURCE:
				$key_field = "content_source_id";
				$table_name = "{$this->kvs_config["tables_prefix"]}content_sources";

				$files["screenshot1"] = "{$this->kvs_config["content_path_content_sources"]}/$p_object[$key_field]";
				$files["screenshot2"] = "{$this->kvs_config["content_path_content_sources"]}/$p_object[$key_field]";
				for ($i = 1; $i <= self::CONTENT_SOURCES_CUSTOM_FIELDS_FILE; $i++)
				{
					$files["custom_file$i"] = "{$this->kvs_config["content_path_content_sources"]}/$p_object[$key_field]";
				}

				$tags = $p_object["tags"];
				$tags_table_name = "{$this->kvs_config["tables_prefix"]}tags_content_sources";
				$categories = $p_object["categories"];
				$categories_table_name = "{$this->kvs_config["tables_prefix"]}categories_content_sources";

				$unset = array("tags", "categories");

				break;
		}

		if (!isset($key_field))
		{
			$this->error("Unknown object type: $p_object_type");
			return self::RESULT_ERROR;
		}
		if (!isset($table_name))
		{
			$this->error("No table name: $p_object_type");
			return self::RESULT_ERROR;
		}

		$temp_object = $p_object;

		foreach ($files as $key => $path)
		{
			unset($temp_object["{$key}_url"], $temp_object["{$key}_path"]);
		}
		foreach ($unset as $key)
		{
			unset($temp_object[$key]);
		}

		$op_result = $this->insert_or_update($table_name, $key_field, $temp_object, $p_object_type);
		if (($op_result == self::RESULT_ADDED || $op_result == self::RESULT_UPDATED) && $p_object[$key_field])
		{
			foreach ($files as $key => $path)
			{
				if ($p_object[$key])
				{
					$dir_path = dirname($path);
					if (!is_dir($dir_path) && !is_link($dir_path))
					{
						mkdir($dir_path, 0777);
						chmod($dir_path, 0777);
					}

					if (!$this->copy_or_download_file($p_object["{$key}_path"], $p_object["{$key}_url"], "$path/$p_object[$key]"))
					{
						$this->query_target("UPDATE $table_name SET $key='' WHERE $key_field=$p_object[$key_field]");
						$this->error("Failed to download {$p_object["{$key}_url"]}");
					} else
					{
						$ext = explode(".", $p_object[$key], 2);
						$ext = strtolower($ext[1]);
						if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif")
						{
							$size = getimagesize("$path/$p_object[$key]");
							if (intval($size[0]) == 0 || intval($size[1]) == 0)
							{
								unlink("$path/$p_object[$key]");
								$this->warning("Invalid image {$p_object["{$key}_url"]}");

								$this->query_target("UPDATE $table_name SET $key='' WHERE $key_field=$p_object[$key_field]");
							} elseif (isset($resize[$key]))
							{
								resize_image('need_size_no_composite', "$path/$p_object[$key]", "$path/$p_object[$key]", $resize[$key]);
							}
						}
					}
				}
			}
		}

		if ($op_result == self::RESULT_ADDED || $op_result == self::RESULT_UPDATED)
		{
			if ($tags_table_name)
			{
				$this->add_tags_to_object($tags, $tags_table_name, $key_field, $p_object[$key_field]);
			}
			if ($categories_table_name)
			{
				$this->add_categories_to_object($categories, $categories_table_name, $key_field, $p_object[$key_field]);
			}
		}

		return $op_result;
	}

	/**
	 * @param array $p_video
	 *
	 * @return int
	 */
	protected function migrate_video(array $p_video): int
	{
		$load_type_id = 1;
		if ($p_video["embed"] != "")
		{
			$load_type_id = 3;
		} elseif ($p_video["file_url"] != "")
		{
			$load_type_id = 2;
		} elseif ($p_video["pseudo_url"] != "")
		{
			$load_type_id = 5;
		}
		if ($this->upload_hotlinked_videos && $load_type_id == 2)
		{
			$p_video["file_download_url"] = $p_video["file_url"];
			unset($p_video["file_url"]);
			$load_type_id = 1;
		}
		$p_video["load_type_id"] = $load_type_id;

		$video_id = intval($p_video["video_id"]);
		$status_id = intval($p_video["status_id"]);
		$tags = $p_video["tags"];
		$categories = $p_video["categories"];
		$models = $p_video["models"];
		$screen_main = $p_video["screen_main"];
		$screen_url = $p_video["screen_url"];
		$screen_urls = $p_video["screen_urls"];
		$screen_paths = $p_video["screen_paths"];
		$video_file_ext = "";
		$video_file_url = "";
		$video_file_urls = "";

		if ($p_video["status_id"] != 5)
		{
			if ($this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE video_id=$video_id") == 0 ||
				$this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE video_id=$video_id AND status_id NOT IN (0, 1, 5)") == 1
			)
			{
				$p_video["status_id"] = 3;

				if ($load_type_id == 1)
				{
					if (is_array($p_video["file_download_urls"]))
					{
						$video_file_urls = $p_video["file_download_urls"];
					} else
					{
						if ($p_video["file_download_url"] != "")
						{
							$video_file_url = $p_video["file_download_url"];
						} else
						{
							if ($p_video["file_config_url"] == "" || $p_video["file_config_pattern"] == "")
							{
								$this->error("Video $video_id has no file download configuration");
								return self::RESULT_ERROR;
							}

							$video_config = file_get_contents("{$this->old_url}$p_video[file_config_url]");
							unset($video_config_matches);
							if (!preg_match($p_video["file_config_pattern"], $video_config, $video_config_matches))
							{
								$this->error("Video $video_id has no video file in configuration: {$this->old_url}$p_video[file_config_url]");
								return self::RESULT_ERROR;
							}
							$video_file_url = urldecode($video_config_matches[1]);
						}

						$video_file_ext = $video_file_url;
						if (strpos($video_file_ext, "?") !== false)
						{
							$video_file_ext = substr($video_file_ext, 0, strpos($video_file_ext, "?"));
						}
						$video_file_ext = strtolower(trim(end(explode(".", $video_file_ext)), "/"));
						if (strpos($video_file_ext, "?") !== false)
						{
							$video_file_ext = substr($video_file_ext, 0, strpos($video_file_ext, "?"));
						}
						if (!in_array($video_file_ext, explode(",", $this->kvs_config["video_allowed_ext"])))
						{
							$this->error("Video $video_id has invalid file extension: $video_file_url");
							return self::RESULT_ERROR;
						}

						$where_add_type = "AND video_type_id=0";
						if (intval($p_video["is_private"]) == 2)
						{
							$where_add_type = "AND video_type_id=1";
						}
						if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}formats_videos WHERE postfix='.$video_file_ext' $where_add_type") == 0)
						{
							$video_file_ext = "tmp";
						}
					}
				}
			} else
			{
				unset($p_video["duration"], $p_video["screen_main"], $p_video["load_type_id"]);
			}
		}

		unset($p_video["tags"], $p_video["categories"], $p_video["models"], $p_video["screen_amount"], $p_video["file_download_url"], $p_video["file_download_urls"], $p_video["file_config_url"], $p_video["file_config_pattern"], $p_video["screen_url"], $p_video["screen_urls"], $p_video["screen_paths"]);

		if ($this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE video_id=$video_id AND status_id NOT IN (0, 1, 5)") == 1)
		{
			if ($video_file_url || is_array($video_file_urls))
			{
				$background_task = @unserialize($this->query_text_target("SELECT data FROM {$this->kvs_config["tables_prefix"]}background_tasks WHERE type_id=1 AND status_id=2 AND video_id=$video_id"), ['allowed_classes' => false]);
				if (is_array($background_task) && $background_task["source_download"] && $video_file_url)
				{
					$background_task["source_download"] = $video_file_url;
				} elseif (is_array($background_task) && $background_task["sources_download"] && is_array($video_file_urls))
				{
					$background_task["sources_download"] = array();
					foreach ($video_file_urls as $video_file_urls_val)
					{
						if ($video_file_urls_val["postfix"] != "" && $video_file_urls_val["url"] != "")
						{
							$background_task["sources_download"][$video_file_urls_val["postfix"]] = $video_file_urls_val["url"];
						}
					}
				}
				if ($this->needs_send_referer())
				{
					$background_task["source_download_referer"] = $this->old_url;
				}
				$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}background_tasks SET status_id=0, server_id=0, data='" . $this->escape_string(serialize($background_task)) . "' WHERE type_id=1 AND status_id=2 AND video_id=$video_id");
			}
			return self::RESULT_SKIPPED;
		}

		$op_result = $this->insert_or_update("{$this->kvs_config["tables_prefix"]}videos", "video_id", $p_video, self::OBJECT_TYPE_VIDEO);
		if ($op_result == self::RESULT_ADDED && $p_video["status_id"] != 5)
		{
			$background_task = array();
			$background_task["status_id"] = $status_id;
			if ($load_type_id == 1)
			{
				if (is_array($video_file_urls))
				{
					$background_task["sources_download"] = array();
					foreach ($video_file_urls as $video_file_urls_val)
					{
						if ($video_file_urls_val["postfix"] != "" && $video_file_urls_val["url"] != "")
						{
							$background_task["sources_download"][$video_file_urls_val["postfix"]] = $video_file_urls_val["url"];
							if ($video_file_urls_val["is_source"])
							{
								$background_task["source"] = "{$video_id}{$video_file_urls_val["postfix"]}";
							}
						}
					}
				} elseif ($video_file_url && $video_file_ext)
				{
					$background_task["source"] = "{$video_id}.{$video_file_ext}";
					$background_task["source_download"] = $video_file_url;
				}
				if ($this->needs_send_referer())
				{
					$background_task["source_download_referer"] = $this->old_url;
				}
			} elseif ($load_type_id == 2)
			{
				$background_task["video_url"] = $p_video["file_url"];
				$background_task["duration"] = $p_video["duration"];
			} elseif ($load_type_id == 3)
			{
				$background_task["duration"] = $p_video["duration"];
			} elseif ($load_type_id == 5)
			{
				$background_task["duration"] = $p_video["duration"];
			}

			if ($this->data_to_migrate->is_videos_screenshots() || $load_type_id == 3 || $load_type_id == 5)
			{
				if (is_array($screen_urls) || is_array($screen_paths))
				{
					$dir_path = floor($video_id / 1000) * 1000;
					mkdir("{$this->kvs_config["content_path_videos_sources"]}/$dir_path", 0777);
					chmod("{$this->kvs_config["content_path_videos_sources"]}/$dir_path", 0777);
					mkdir("{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id", 0777);
					chmod("{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id", 0777);

					for ($i = 1; $i <= min(count($screen_urls), count($screen_paths)); $i++)
					{
						$this->copy_or_download_file($screen_paths[$i - 1], $screen_urls[$i - 1], "{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id/$i.jpg");
					}

					$zip_folder = "{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id";
					$zip_files_to_add = array();
					for ($i = 1; $i <= min(count($screen_urls), count($screen_paths)); $i++)
					{
						if (is_file("$zip_folder/$i.jpg") && filesize("$zip_folder/$i.jpg") > 0)
						{
							$img = getimagesize("$zip_folder/$i.jpg");
							if ($img[0] > 0 && $img[1] > 0)
							{
								$zip_files_to_add[] = "$zip_folder/$i.jpg";
							}
						}
					}
					$zip = new PclZip("$zip_folder/$video_id.zip");
					$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = $zip_folder);

					for ($i = 1; $i <= min(count($screen_urls), count($screen_paths)); $i++)
					{
						unlink("$zip_folder/$i.jpg");
					}
					$background_task["screen_main"] = intval($screen_main);
				} elseif ($screen_url)
				{
					$dir_path = floor($video_id / 1000) * 1000;
					mkdir("{$this->kvs_config["content_path_videos_sources"]}/$dir_path", 0777);
					chmod("{$this->kvs_config["content_path_videos_sources"]}/$dir_path", 0777);
					mkdir("{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id", 0777);
					chmod("{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id", 0777);
					$this->copy_or_download_file("", $screen_url, "{$this->kvs_config["content_path_videos_sources"]}/$dir_path/$video_id/$video_id.jpg");
				}
			}

			$this->query_target("INSERT INTO {$this->kvs_config["tables_prefix"]}background_tasks SET status_id=0, type_id=1, video_id=$video_id, data='" . $this->escape_string(serialize($background_task)) . "', added_date=now()");
		}

		if ($op_result == self::RESULT_ADDED || $op_result == self::RESULT_UPDATED)
		{
			$this->add_tags_to_object($tags, "{$this->kvs_config["tables_prefix"]}tags_videos", "video_id", $video_id);
			$this->add_categories_to_object($categories, "{$this->kvs_config["tables_prefix"]}categories_videos", "video_id", $video_id);
			$this->add_models_to_object($models, "{$this->kvs_config["tables_prefix"]}models_videos", "video_id", $video_id);
		}
		return $op_result;
	}

	/**
	 * @param array $p_album
	 *
	 * @return int
	 */
	protected function migrate_album(array $p_album): int
	{
		$album_id = intval($p_album["album_id"]);
		$status_id = intval($p_album["status_id"]);
		$image_main = $p_album["main_photo_id"];
		$tags = $p_album["tags"];
		$categories = $p_album["categories"];
		$models = $p_album["models"];

		if ($p_album["status_id"] != 5)
		{
			if ($this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}albums WHERE album_id=$album_id") == 0 ||
				$this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}albums WHERE album_id=$album_id AND status_id NOT IN (0, 1, 5)") == 1
			)
			{
				$p_album["status_id"] = 3;
			}
		}

		unset($p_album["tags"], $p_album["categories"], $p_album["models"], $p_album["photos_amount"], $p_album["main_photo_id"]);

		if ($this->query_num_target("SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}albums WHERE album_id=$album_id AND status_id NOT IN (0, 1, 5)") == 1)
		{
			return self::RESULT_SKIPPED;
		}

		$op_result = $this->insert_or_update("{$this->kvs_config["tables_prefix"]}albums", "album_id", $p_album, self::OBJECT_TYPE_ALBUM);
		if ($op_result == self::RESULT_ADDED && $p_album["status_id"] != 5)
		{
			$background_task = array();
			$background_task["status_id"] = $status_id;
			$background_task['image_main'] = intval($image_main);

			$images_params = $this->build_album_images_migration_params($album_id);
			if ($images_params)
			{
				$images_result = $this->query_source($images_params->get_select_query());
				if ($images_result)
				{
					for ($i = 0; $i < $images_result->num_rows; $i++)
					{
						$images_result->data_seek($i);
						$database_item = $images_result->fetch_assoc();
						if ($database_item["image_url"] != "")
						{
							$background_task["source_urls"][] = $database_item["image_url"];
						} elseif ($database_item["image_code"] != "")
						{
							$image_urls = $this->build_album_image_url_from_code($database_item["image_code"]);
							if (isset($image_urls) && is_array($image_urls))
							{
								foreach ($image_urls as $image_url)
								{
									$background_task["source_urls"][] = $image_url;
								}
							}
						}
					}
					$images_result->free();
				}
			}

			$this->query_target("INSERT INTO {$this->kvs_config["tables_prefix"]}background_tasks SET status_id=0, type_id=10, album_id=$album_id, data='" . $this->escape_string(serialize($background_task)) . "', added_date=now()");
		}

		if ($op_result == self::RESULT_ADDED || $op_result == self::RESULT_UPDATED)
		{
			$this->add_tags_to_object($tags, "{$this->kvs_config["tables_prefix"]}tags_albums", "album_id", $album_id);
			$this->add_categories_to_object($categories, "{$this->kvs_config["tables_prefix"]}categories_albums", "album_id", $album_id);
			$this->add_models_to_object($models, "{$this->kvs_config["tables_prefix"]}models_albums", "album_id", $album_id);
		}
		return $op_result;
	}

	/**
	 * @param array $p_comment
	 *
	 * @return int
	 */
	protected function migrate_comment(array $p_comment): int
	{
		switch ($p_comment["object_type_id"])
		{
			case self::OBJECT_TYPE_VIDEO:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}videos";
				$object_key_name = "video_id";
				$object_log_name = "video";
				break;
			case self::OBJECT_TYPE_ALBUM:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}albums";
				$object_key_name = "album_id";
				$object_log_name = "album";
				break;
			case self::OBJECT_TYPE_CONTENT_SOURCE:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}content_sources";
				$object_key_name = "content_source_id";
				$object_log_name = "content source";
				break;
			case self::OBJECT_TYPE_MODEL:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}models";
				$object_key_name = "model_id";
				$object_log_name = "model";
				break;
			case self::OBJECT_TYPE_DVD:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}dvds";
				$object_key_name = "dvd_id";
				$object_log_name = "DVD / channel / TV series";
				break;
			case self::OBJECT_TYPE_POST:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}posts";
				$object_key_name = "post_id";
				$object_log_name = "Post";
				break;
			case self::OBJECT_TYPE_PLAYLIST:
				$object_table_name = "{$this->kvs_config["tables_prefix"]}playlists";
				$object_key_name = "playlist_id";
				$object_log_name = "Playlist";
				break;
			default:
				$this->error("Unsupported object type for comment: $p_comment[object_type_id]");
				return self::RESULT_ERROR;
		}

		if ($p_comment['migrator_uid'] != '')
		{
			if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE migrator_uid='$p_comment[migrator_uid]'") == 1)
			{
				return self::RESULT_SKIPPED;
			}
		} elseif ($p_comment['comment_id'] != '')
		{
			if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE comment_id='$p_comment[comment_id]'") == 1)
			{
				return self::RESULT_SKIPPED;
			}
		}

		if ($this->query_num_target("SELECT count(*) FROM $object_table_name WHERE $object_key_name=$p_comment[object_id] and title='" . $this->escape_string($p_comment["object_title"]) . "'") == 0)
		{
			$this->warning("No $object_log_name exist for comment: $p_comment[object_id] / $p_comment[object_title]");
			return self::RESULT_ERROR;
		}

		if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users WHERE user_id=$p_comment[user_id] and username='" . $this->escape_string($p_comment["username"]) . "'") == 0)
		{
			$this->warning("No user exist for comment: $p_comment[user_id] / $p_comment[username]");
			$p_comment["user_id"] = intval($this->query_num_target("SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users WHERE status_id=4"));
			$p_comment["anonymous_username"] = $p_comment["username"];
			if ($p_comment["user_id"] == 0)
			{
				$this->warning("No anonymous user found");
				return self::RESULT_ERROR;
			}
		}

		unset($p_comment["object_title"], $p_comment["username"]);

		return $this->insert_or_fail("{$this->kvs_config["tables_prefix"]}comments", $p_comment);
	}

	/**
	 * @param array $p_playlist
	 *
	 * @return int
	 */
	protected function migrate_playlist(array $p_playlist): int
	{
		$playlist_id = $p_playlist["playlist_id"];
		$tags = $p_playlist["tags"];
		$categories = $p_playlist["categories"];

		unset($p_playlist["tags"], $p_playlist["categories"]);

		$op_result = $this->insert_or_update("{$this->kvs_config["tables_prefix"]}playlists", "playlist_id", $p_playlist, self::OBJECT_TYPE_PLAYLIST);

		if ($op_result == self::RESULT_ADDED || $op_result == self::RESULT_UPDATED)
		{
			$playlist_videos_params = $this->build_playlist_videos_migration_params($playlist_id);
			if ($playlist_videos_params)
			{
				$this->exec_simple_sql_loop($playlist_videos_params, "{$this->kvs_config["tables_prefix"]}fav_videos", "DELETE FROM {$this->kvs_config["tables_prefix"]}fav_videos WHERE playlist_id=$playlist_id");
			}

			$this->add_tags_to_object($tags, "{$this->kvs_config["tables_prefix"]}tags_playlists", "playlist_id", $playlist_id);
			$this->add_categories_to_object($categories, "{$this->kvs_config["tables_prefix"]}categories_playlists", "playlist_id", $playlist_id);
		}
		return $op_result;
	}

	/**
	 * @return bool
	 */
	protected function pre_start_hook(): bool
	{
		return true;
	}

	/**
	 *
	 */
	protected function post_finish_hook(): void
	{
	}

	/**
	 * @param array $p_object
	 * @param int $p_object_type
	 *
	 * @return array
	 */
	protected function pre_process_each_object_hook(array $p_object, int $p_object_type): array
	{
		return $p_object;
	}

	/**
	 * @return bool
	 */
	protected function needs_send_referer(): bool
	{
		return false;
	}

	/**
	 * @return array
	 */
	abstract protected function build_progress_queries(): array;

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_users_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_category_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_categories_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_tags_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_source_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_content_sources_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_model_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_models_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvd_groups_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_dvds_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @param int $p_album_id
	 *
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_album_images_migration_params(int $p_album_id): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @param string $p_code
	 *
	 * @return array
	 */
	protected function build_album_image_url_from_code(string $p_code): array
	{
		return array();
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_comments_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_videos_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_fav_albums_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_friends_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_messages_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_subscriptions_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_playlists_migration_params(): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @param int $p_playlist_id
	 *
	 * @return KvsDataMigratorMigrationParams|null
	 */
	protected function build_playlist_videos_migration_params(int $p_playlist_id): ?KvsDataMigratorMigrationParams
	{
		return null;
	}

	/**
	 * @param string $p_message
	 */
	protected function info(string $p_message = ""): void
	{
		$this->log("INFO ", $p_message);
	}

	/**
	 * @param string $p_message
	 */
	protected function warning(string $p_message = ""): void
	{
		$this->log("WARN ", $p_message);
	}

	/**
	 * @param string $p_message
	 */
	protected function error(string $p_message = ""): void
	{
		$this->log("ERROR", $p_message);
	}

	/**
	 * @param string $p_query
	 * @param string $p_error_code
	 * @param string $p_error_message
	 */
	protected function sqlerror(string $p_query, string $p_error_code, string $p_error_message): void
	{
		if ($p_error_code != 1060)
		{
			$this->log("ERROR", "SQL error $p_error_code: $p_error_message\n$p_query");
		}
	}

	/**
	 * @param string $p_level
	 * @param string $p_message
	 */
	protected function log(string $p_level, string $p_message): void
	{
		if ($p_message != "")
		{
			echo "$p_level " . date("[Y-m-d H:i:s]: ") . "$p_message\n";
		} else
		{
			echo "\n";
		}
	}

	/**
	 * @param string $p_query
	 *
	 * @return mysqli_result|bool
	 */
	protected function query_source(string $p_query)
	{
		if (!$this->mysql_link_source)
		{
			return false;
		}
		$mysql_result = $this->mysql_link_source->query($p_query);
		if (!$mysql_result)
		{
			$this->sqlerror($p_query, $this->mysql_link_source->errno, $this->mysql_link_source->error);
		}
		return $mysql_result;
	}

	/**
	 * @param string $p_query
	 *
	 * @return int
	 */
	protected function query_num_source(string $p_query): int
	{
		$mysql_result = $this->query_source($p_query);
		if ($mysql_result)
		{
			$row = $mysql_result->fetch_row();
			return intval($row[0]);
		}
		return 0;
	}

	/**
	 * @param string $p_query
	 *
	 * @return string
	 */
	protected function query_text_source(string $p_query): string
	{
		$mysql_result = $this->query_source($p_query);
		if ($mysql_result)
		{
			$row = $mysql_result->fetch_row();
			return trim($row[0]);
		}
		return "";
	}

	/**
	 * @param string $p_query
	 *
	 * @return mysqli_result|bool
	 */
	protected function query_target(string $p_query)
	{
		if (!$this->mysql_link_target)
		{
			return false;
		}
		$mysql_result = $this->mysql_link_target->query($p_query);
		if (!$mysql_result && !in_array($this->mysql_link_target->errno, array(1060, 1061)))
		{
			$this->sqlerror($p_query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
		}
		return $mysql_result;
	}

	/**
	 * @param string $p_query
	 *
	 * @return int
	 */
	protected function query_num_target(string $p_query): int
	{
		$mysql_result = $this->query_target($p_query);
		if ($mysql_result)
		{
			$row = $mysql_result->fetch_row();
			return intval($row[0]);
		}
		return 0;
	}

	/**
	 * @param string $p_query
	 *
	 * @return string
	 */
	protected function query_text_target(string $p_query): string
	{
		$mysql_result = $this->query_target($p_query);
		if ($mysql_result)
		{
			$row = $mysql_result->fetch_row();
			return trim($row[0]);
		}
		return "";
	}

	/**
	 * @param string $p_string
	 *
	 * @return string
	 */
	protected function escape_string(string $p_string): string
	{
		if (!$this->mysql_link_target)
		{
			return $p_string ?: "";
		}
		return $this->mysql_link_target->escape_string($p_string);
	}

	/**
	 * @param KvsDataMigratorMigrationParams $p_migration_params
	 *
	 * @return KvsDataMigratorSummaryItem
	 */
	private function exec_iterated_sql_loop(KvsDataMigratorMigrationParams $p_migration_params): KvsDataMigratorSummaryItem
	{
		$next_id = 0;
		$loop_speed = 100;

		$number_total = 0;
		$number_of_inserted = 0;
		$number_of_updated = 0;
		$number_of_errors = 0;

		$num_rows = $loop_speed;
		while ($num_rows == $loop_speed)
		{
			$looped_query = $p_migration_params->get_select_query() . " WHERE " . $p_migration_params->get_table_key_field() . ">$next_id ORDER BY " . $p_migration_params->get_table_key_field() . " ASC LIMIT $loop_speed";

			$mysql_result = $this->query_source($looped_query);
			if (!$mysql_result)
			{
				return new KvsDataMigratorSummaryItem($number_total, $number_of_inserted, $number_of_updated, $number_of_errors);
			}

			for ($i = 0; $i < $mysql_result->num_rows; $i++)
			{
				$mysql_result->data_seek($i);
				$database_item = $mysql_result->fetch_assoc();
				$next_id = $database_item[$p_migration_params->get_table_key_field()];

				$database_item = $this->pre_process_each_object_hook($database_item, $p_migration_params->get_object_type());
				$callback = $p_migration_params->get_callback();
				if ($callback)
				{
					$database_item = $callback($database_item);
				}

				$result = $this->migrate_object($database_item, $p_migration_params->get_object_type());
				switch ($result)
				{
					case self::RESULT_ADDED:
						$number_of_inserted++;
						break;
					case self::RESULT_UPDATED:
						$number_of_updated++;
						break;
					case self::RESULT_ERROR:
						$number_of_errors++;
						break;
					case self::RESULT_SKIPPED_LIMIT:
						$mysql_result->free();
						break 3;
				}
				$this->progress_current++;

				if ($this->progress_callback)
				{
					$progress_callback = $this->progress_callback;
					$progress_callback(floor(($this->progress_current / $this->progress_total) * 100));
				}

				$number_total++;
			}

			$num_rows = $mysql_result->num_rows;
			$mysql_result->free();

			usleep(50);
			if ($number_total % 1000 == 0)
			{
				$this->info("$number_total...");
			}
		}
		$this->info("Processed $number_total items: $number_of_inserted inserted, $number_of_updated updated, $number_of_errors errors");
		return new KvsDataMigratorSummaryItem($number_total, $number_of_inserted, $number_of_updated, $number_of_errors);
	}

	/**
	 * @param KvsDataMigratorMigrationParams $p_migration_params
	 * @param string $p_insert_to_table
	 * @param string $p_delete_from_table_query
	 *
	 * @return KvsDataMigratorSummaryItem
	 */
	private function exec_simple_sql_loop(KvsDataMigratorMigrationParams $p_migration_params, string $p_insert_to_table = "", string $p_delete_from_table_query = ""): KvsDataMigratorSummaryItem
	{
		$number_total = 0;
		$number_of_inserted = 0;
		$number_of_updated = 0;
		$number_of_errors = 0;

		$mysql_result = $this->query_source($p_migration_params->get_select_query());
		if (!$mysql_result)
		{
			return new KvsDataMigratorSummaryItem($number_total, $number_of_inserted, $number_of_updated, $number_of_errors);
		}

		if ($p_insert_to_table != "")
		{
			if ($p_delete_from_table_query != "")
			{
				$this->query_target($p_delete_from_table_query);
			} else
			{
				$this->query_target("DELETE FROM $p_insert_to_table");
			}
		}

		for ($i = 0; $i < $mysql_result->num_rows; $i++)
		{
			$mysql_result->data_seek($i);
			$database_item = $mysql_result->fetch_assoc();

			$database_item = $this->pre_process_each_object_hook($database_item, $p_migration_params->get_object_type());
			$callback = $p_migration_params->get_callback();
			if ($callback)
			{
				$database_item = $callback($database_item);
			}

			if ($p_insert_to_table != "")
			{
				$result = $this->insert_or_fail($p_insert_to_table, $database_item);
			} else
			{
				$result = $this->migrate_object($database_item, $p_migration_params->get_object_type());
			}
			switch ($result)
			{
				case self::RESULT_ADDED:
					$number_of_inserted++;
					break;
				case self::RESULT_UPDATED:
					$number_of_updated++;
					break;
				case self::RESULT_ERROR:
					$number_of_errors++;
					break;
			}
			$this->progress_current++;

			if ($this->progress_callback)
			{
				$progress_callback = $this->progress_callback;
				$progress_callback(floor(($this->progress_current / $this->progress_total) * 100));
			}

			$number_total++;
			if ($number_total % 1000 == 0)
			{
				$this->info("$number_total...");
			}
			usleep(50);
		}
		$mysql_result->free();

		$this->info("Processed $number_total items: $number_of_inserted inserted, $number_of_updated updated, $number_of_errors errors");
		return new KvsDataMigratorSummaryItem($number_total, $number_of_inserted, $number_of_updated, $number_of_errors);
	}

	/**
	 * @param string $p_table_name
	 * @param string $p_table_key_field
	 * @param array $p_data
	 * @param int $p_object_type_id
	 *
	 * @return int
	 */
	private function insert_or_update(string $p_table_name, string $p_table_key_field, array $p_data, int $p_object_type_id = 0): int
	{
		if ($p_table_key_field == "" || intval($p_data[$p_table_key_field]) <= 0)
		{
			$this->error("Object doesn't have ID key");
			return self::RESULT_ERROR;
		}

		if ($p_object_type_id > 0)
		{
			if ($p_data["dir"] == "" && $p_data["title"] != "")
			{
				$p_data["dir"] = get_correct_dir_name($p_data["title"]);

				$temp_dir = $p_data["dir"];
				for ($i = 2; $i < 9999; $i++)
				{
					$query = "SELECT count(*) FROM $p_table_name WHERE dir='" . $this->escape_string($temp_dir) . "' and $p_table_key_field!=$p_data[$p_table_key_field]";
					$mysql_result = $this->mysql_link_target->query($query);
					if (!$mysql_result)
					{
						$this->sqlerror($query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
						break;
					}

					$row = $mysql_result->fetch_row();
					if (intval($row[0]) == 0)
					{
						$p_data["dir"] = $temp_dir;
						break;
					}
					$temp_dir = $p_data["dir"] . $i;
				}
			}
		}

		$query = "INSERT INTO $p_table_name SET ";
		foreach ($p_data as $key => $value)
		{
			if ($value != "")
			{
				$query .= "$key = '" . $this->escape_string($value) . "', ";
			}
		}
		$query = trim($query, " ,");
		if (!$this->mysql_link_target->query($query))
		{
			if ($this->mysql_link_target->errno == 1062)
			{
				if ($this->override_existing_objects)
				{
					$query = "UPDATE $p_table_name SET ";
					foreach ($p_data as $key => $value)
					{
						if ($key != $p_table_key_field)
						{
							$query .= "$key = '" . $this->escape_string($value) . "', ";
						}
					}
					$query = trim($query, " ,") . " WHERE $p_table_key_field=$p_data[$p_table_key_field]";
					if (!$this->mysql_link_target->query($query))
					{
						$this->sqlerror($query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
						return self::RESULT_ERROR;
					}

					if ($p_object_type_id > 0)
					{
						$query = "INSERT INTO {$this->kvs_config["tables_prefix"]}admin_audit_log SET user_id=$this->admin_user_id, username='migrator', action_id=150, object_id=$p_data[$p_table_key_field], object_type_id=$p_object_type_id, added_date=now()";
						if (!$this->mysql_link_target->query($query))
						{
							$this->sqlerror($query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
						}
					}
					return self::RESULT_UPDATED;
				} else
				{
					return self::RESULT_SKIPPED;
				}
			} else
			{
				$this->sqlerror($query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
				return self::RESULT_ERROR;
			}
		}
		if ($p_object_type_id > 0)
		{
			$query = "INSERT INTO {$this->kvs_config["tables_prefix"]}admin_audit_log SET user_id=$this->admin_user_id, username='migrator', action_id=100, object_id=$p_data[$p_table_key_field], object_type_id=$p_object_type_id, added_date=now()";
			if (!$this->mysql_link_target->query($query))
			{
				$this->sqlerror($query, $this->mysql_link_target->errno, $this->mysql_link_target->error);
			}
		}
		return self::RESULT_ADDED;
	}

	/**
	 * @param string $p_table_name
	 * @param array $p_data
	 *
	 * @return int
	 */
	private function insert_or_fail(string $p_table_name, array $p_data): int
	{
		$query = "INSERT INTO $p_table_name SET ";
		foreach ($p_data as $key => $value)
		{
			if ($value != "")
			{
				$query .= "$key = '" . $this->escape_string($value) . "', ";
			}
		}
		if (!$this->query_target(trim($query, " ,")))
		{
			return self::RESULT_ERROR;
		}
		return self::RESULT_ADDED;
	}

	/**
	 * @param string $p_file_path
	 * @param string $p_file_url
	 * @param string $p_target_path
	 *
	 * @return bool
	 */
	private function copy_or_download_file(?string $p_file_path, ?string $p_file_url, string $p_target_path): bool
	{
		$p_file_url = trim($p_file_url);
		$p_file_path = trim($p_file_path);

		if ($p_file_path && is_file($p_file_path))
		{
			$dir_path = dirname($p_target_path);
			if (!is_dir($dir_path) && !is_link($dir_path))
			{
				mkdir($dir_path, 0777);
				chmod($dir_path, 0777);
			}

			return copy($p_file_path, $p_target_path);
		}
		if ($p_file_url)
		{
			$dir_path = dirname($p_target_path);
			if (!is_dir($dir_path) && !is_link($dir_path))
			{
				mkdir($dir_path, 0777);
				chmod($dir_path, 0777);
			}

			$url = str_replace(" ", "%20", $p_file_url);

			$rnd = mt_rand(1000000, 9999999);
			$headers_file_path = "{$this->kvs_config["temporary_path"]}/headers-$rnd.txt";

			$fp = fopen($p_target_path, "w");
			$fp_headers = fopen($headers_file_path, "w");

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_WRITEHEADER, $fp_headers);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.80 Safari/537.36");
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			if ($this->needs_send_referer())
			{
				curl_setopt($ch, CURLOPT_REFERER, $this->old_url);
			}
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);

			$expected_file_size = "";
			if (is_file($headers_file_path))
			{
				$headers = file_get_contents($headers_file_path);
				preg_match("/.*Content-Length: ([0-9]+)/is", $headers, $temp);
				$expected_file_size = trim($temp[1]);
				unlink($headers_file_path);
			}

			if (!is_file($p_target_path) || sprintf("%.0f", filesize($p_target_path)) == 0 || ($expected_file_size != "" && sprintf("%.0f", filesize($p_target_path)) != $expected_file_size))
			{
				return false;
			}
		}
		return true;
	}

	/**
	 *
	 */
	private function migrate_users(): void
	{
		$migration_params = $this->build_users_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating users...");

			$anonymous_id = $this->query_num_target("SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users where status_id=4");
			$this->add_to_summary("users", $this->exec_iterated_sql_loop($migration_params));

			if ($anonymous_id > 0)
			{
				$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users SET status_id=4, username='Anonymous', display_name='Anonymous' WHERE user_id=$anonymous_id");
			}
		}
	}

	/**
	 *
	 */
	private function migrate_category_groups(): void
	{
		$migration_params = $this->build_category_groups_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating category groups...");
			$this->add_to_summary("category_groups", $this->exec_iterated_sql_loop($migration_params));
		}
	}

	/**
	 *
	 */
	private function migrate_categories(): void
	{
		$migration_params = $this->build_categories_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating categories...");
			$this->add_to_summary("categories", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}categories WHERE category_group_id!=0 AND category_group_id NOT IN (SELECT category_group_id FROM {$this->kvs_config["tables_prefix"]}categories_groups)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs category groups are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}categories SET category_group_id=0 WHERE category_group_id NOT IN (SELECT category_group_id FROM {$this->kvs_config["tables_prefix"]}categories_groups)");
		}
	}

	/**
	 *
	 */
	private function migrate_tags(): void
	{
		$migration_params = $this->build_tags_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating tags...");
			$this->add_to_summary("tags", $this->exec_iterated_sql_loop($migration_params));
		}
	}

	/**
	 *
	 */
	private function migrate_content_source_groups(): void
	{
		$migration_params = $this->build_content_source_groups_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating content source groups...");
			$this->add_to_summary("content_source_groups", $this->exec_iterated_sql_loop($migration_params));
		}
	}

	/**
	 *
	 */
	private function migrate_content_sources(): void
	{
		$migration_params = $this->build_content_sources_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating content sources...");
			$this->add_to_summary("content_sources", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}content_sources WHERE content_source_group_id!=0 AND content_source_group_id NOT IN (SELECT content_source_group_id FROM {$this->kvs_config["tables_prefix"]}content_sources_groups)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs content source groups are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}content_sources SET content_source_group_id=0 WHERE content_source_group_id NOT IN (SELECT content_source_group_id FROM {$this->kvs_config["tables_prefix"]}content_sources_groups)");
		}
	}

	/**
	 *
	 */
	private function migrate_model_groups(): void
	{
		$migration_params = $this->build_model_groups_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating model groups...");
			$this->add_to_summary("model_groups", $this->exec_iterated_sql_loop($migration_params));
		}
	}

	/**
	 *
	 */
	private function migrate_models(): void
	{
		$migration_params = $this->build_models_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating models...");
			$this->add_to_summary("models", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}models WHERE model_group_id!=0 AND model_group_id NOT IN (SELECT model_group_id FROM {$this->kvs_config["tables_prefix"]}models_groups)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs model groups are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}models SET model_group_id=0 WHERE model_group_id NOT IN (SELECT model_group_id FROM {$this->kvs_config["tables_prefix"]}models_groups)");
		}
	}

	/**
	 *
	 */
	private function migrate_dvd_groups(): void
	{
		$migration_params = $this->build_dvd_groups_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating channel groups / DVD groups / TV series...");
			$this->add_to_summary("dvd_groups", $this->exec_iterated_sql_loop($migration_params));
		}
	}

	/**
	 *
	 */
	private function migrate_dvds(): void
	{
		$migration_params = $this->build_dvds_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating channels / DVDs / TV seasons...");
			$this->add_to_summary("dvds", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}dvds WHERE dvd_group_id!=0 AND dvd_group_id NOT IN (SELECT dvd_group_id FROM {$this->kvs_config["tables_prefix"]}dvds_groups)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs channel groups / DVD groups / TV series are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}dvds SET dvd_group_id=0 WHERE dvd_group_id NOT IN (SELECT dvd_group_id FROM {$this->kvs_config["tables_prefix"]}dvds_groups)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}dvds_groups SET total_dvds=(SELECT count(*) from {$this->kvs_config["tables_prefix"]}dvds WHERE {$this->kvs_config["tables_prefix"]}dvds.dvd_group_id={$this->kvs_config["tables_prefix"]}dvds_groups.dvd_group_id)");

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}dvds WHERE user_id!=0 AND user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs users are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}dvds SET user_id=0 WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
		}
	}

	/**
	 *
	 */
	private function migrate_videos(): void
	{
		$migration_params = $this->build_videos_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating videos...");
			$this->add_to_summary("videos", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE user_id!=0 AND user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs users are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos SET user_id=(SELECT min(user_id) FROM {$this->kvs_config["tables_prefix"]}users) WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE dvd_id!=0 AND dvd_id NOT IN (SELECT dvd_id FROM {$this->kvs_config["tables_prefix"]}dvds)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs channels / DVDs / TV seasons are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos SET dvd_id=0 WHERE dvd_id NOT IN (SELECT dvd_id FROM {$this->kvs_config["tables_prefix"]}dvds)");

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}videos WHERE content_source_id!=0 AND content_source_id NOT IN (SELECT content_source_id FROM {$this->kvs_config["tables_prefix"]}content_sources)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs content sources are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos SET content_source_id=0 WHERE content_source_id NOT IN (SELECT content_source_id FROM {$this->kvs_config["tables_prefix"]}content_sources)");
		}
	}

	/**
	 *
	 */
	private function migrate_albums(): void
	{
		$migration_params = $this->build_albums_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating albums...");
			$this->add_to_summary("albums", $this->exec_iterated_sql_loop($migration_params));

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}albums WHERE user_id!=0 AND user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs users are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}albums SET user_id=(SELECT min(user_id) FROM {$this->kvs_config["tables_prefix"]}users) WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");

			$missing_refs = $this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}albums WHERE content_source_id!=0 AND content_source_id NOT IN (SELECT content_source_id FROM {$this->kvs_config["tables_prefix"]}content_sources)");
			if ($missing_refs > 0)
			{
				$this->warning("$missing_refs content sources are missing");
			}
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}albums SET content_source_id=0 WHERE content_source_id NOT IN (SELECT content_source_id FROM {$this->kvs_config["tables_prefix"]}content_sources)");
		}
	}

	/**
	 *
	 */
	private function migrate_playlists(): void
	{
		$migration_params = $this->build_playlists_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating playlists...");
			$this->add_to_summary("playlists", $this->exec_simple_sql_loop($migration_params));

			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}playlists p INNER JOIN (SELECT playlist_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_videos GROUP BY playlist_id) fv ON p.playlist_id=fv.playlist_id SET total_videos=cnt");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_videos WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_videos WHERE video_id NOT IN (SELECT video_id FROM {$this->kvs_config["tables_prefix"]}videos)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos v INNER JOIN (SELECT video_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_videos GROUP BY video_id) fv ON v.video_id=fv.video_id SET favourites_count=cnt");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users u INNER JOIN (SELECT user_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_videos GROUP BY user_id) fv ON u.user_id=fv.user_id SET favourite_videos_count=cnt");
		}
	}

	/**
	 *
	 */
	private function migrate_comments(): void
	{
		$migration_params = $this->build_comments_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating comments...");
			$this->add_to_summary("comments", $this->exec_simple_sql_loop($migration_params));

			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users SET
					comments_videos_count   =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=1),
					comments_albums_count   =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=2),
					comments_cs_count       =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=3),
					comments_models_count   =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=4),
					comments_dvds_count     =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=5),
					comments_posts_count    =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=12),
					comments_playlists_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1 AND object_type_id=13),
					comments_total_count    =(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE user_id={$this->kvs_config["tables_prefix"]}users.user_id AND is_approved=1)
			");

			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos          SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}videos.video_id                   AND is_approved=1 AND object_type_id=1)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}albums          SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}albums.album_id                   AND is_approved=1 AND object_type_id=2)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}content_sources SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}content_sources.content_source_id AND is_approved=1 AND object_type_id=3)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}models          SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}models.model_id                   AND is_approved=1 AND object_type_id=4)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}dvds            SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}dvds.dvd_id                       AND is_approved=1 AND object_type_id=5)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}posts           SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}posts.post_id                     AND is_approved=1 AND object_type_id=12)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}playlists       SET comments_count=(SELECT COUNT(*) FROM {$this->kvs_config["tables_prefix"]}comments WHERE object_id={$this->kvs_config["tables_prefix"]}playlists.playlist_id             AND is_approved=1 AND object_type_id=13)");
		}
	}

	/**
	 *
	 */
	private function migrate_favourites(): void
	{
		$migration_params = $this->build_fav_videos_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating favourite videos...");
			$this->add_to_summary("fav_videos", $this->exec_simple_sql_loop($migration_params, "{$this->kvs_config["tables_prefix"]}fav_videos"));

			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_videos WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_videos WHERE video_id NOT IN (SELECT video_id FROM {$this->kvs_config["tables_prefix"]}videos)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}videos v INNER JOIN (SELECT video_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_videos GROUP BY video_id) fv ON v.video_id=fv.video_id SET favourites_count=cnt");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users u INNER JOIN (SELECT user_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_videos GROUP BY user_id) fv ON u.user_id=fv.user_id SET favourite_videos_count=cnt");
		}

		$migration_params = $this->build_fav_albums_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating favourite albums...");
			$this->add_to_summary("fav_albums", $this->exec_simple_sql_loop($migration_params, "{$this->kvs_config["tables_prefix"]}fav_albums"));

			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_albums WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}fav_albums WHERE album_id NOT IN (SELECT album_id FROM {$this->kvs_config["tables_prefix"]}albums)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}albums a INNER JOIN (SELECT album_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_albums GROUP BY album_id) fa ON a.album_id=fa.album_id SET favourites_count=cnt");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users u INNER JOIN (SELECT user_id, count(*) AS cnt FROM {$this->kvs_config["tables_prefix"]}fav_albums GROUP BY user_id) fa ON u.user_id=fa.user_id SET favourite_albums_count=cnt");
		}
	}

	/**
	 *
	 */
	private function migrate_friends(): void
	{
		$migration_params = $this->build_friends_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating friends...");
			$this->add_to_summary("friends", $this->exec_simple_sql_loop($migration_params, "{$this->kvs_config["tables_prefix"]}friends"));

			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}friends WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}friends WHERE friend_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
		}
	}

	/**
	 *
	 */
	private function migrate_messages(): void
	{
		$migration_params = $this->build_messages_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating messages...");
			$this->add_to_summary("messages", $this->exec_simple_sql_loop($migration_params, "{$this->kvs_config["tables_prefix"]}messages"));

			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}messages WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}messages WHERE user_from_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
		}
	}

	/**
	 *
	 */
	private function migrate_subscriptions(): void
	{
		$migration_params = $this->build_subscriptions_migration_params();

		if ($migration_params)
		{
			$this->info();
			$this->info("Migrating subscriptions...");
			$this->add_to_summary("subscriptions", $this->exec_simple_sql_loop($migration_params, "{$this->kvs_config["tables_prefix"]}users_subscriptions"));

			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE user_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=1 AND subscribed_object_id NOT IN (SELECT user_id FROM {$this->kvs_config["tables_prefix"]}users)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=3 AND subscribed_object_id NOT IN (SELECT content_source_id FROM {$this->kvs_config["tables_prefix"]}content_sources)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=4 AND subscribed_object_id NOT IN (SELECT model_id FROM {$this->kvs_config["tables_prefix"]}models)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=5 AND subscribed_object_id NOT IN (SELECT dvd_id FROM {$this->kvs_config["tables_prefix"]}dvds)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=6 AND subscribed_object_id NOT IN (SELECT category_id FROM {$this->kvs_config["tables_prefix"]}categories)");
			$this->query_target("DELETE FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=13 AND subscribed_object_id NOT IN (SELECT playlist_id FROM {$this->kvs_config["tables_prefix"]}playlists)");

			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}users SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=1 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}users.user_id)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}content_sources SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=3 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}content_sources.content_source_id)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}models SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=4 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}models.model_id)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}dvds SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=5 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}dvds.dvd_id)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}categories SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=6 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}categories.category_id)");
			$this->query_target("UPDATE {$this->kvs_config["tables_prefix"]}playlists SET subscribers_count=(SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}users_subscriptions WHERE subscribed_type_id=13 AND subscribed_object_id={$this->kvs_config["tables_prefix"]}playlists.playlist_id)");
		}
	}

	/**
	 *
	 */
	private function calc_progress(): void
	{
		$queries = $this->build_progress_queries();

		foreach ($queries as $query)
		{
			$this->progress_total += $this->query_num_source($query);
		}
		$this->info("Total items to migrate: $this->progress_total");
	}

	/**
	 * @param string|null $tags
	 * @param string $tags_table
	 * @param string $object_key_field
	 * @param int $object_id
	 */
	private function add_tags_to_object(?string $tags, string $tags_table, string $object_key_field, int $object_id): void
	{
		if (!$tags)
		{
			return;
		}
		if ($this->data_to_migrate->is_tags())
		{
			$tags_array = explode("||", $tags);
			$this->query_target("DELETE FROM $tags_table WHERE $object_key_field=$object_id");

			$tags_added = array();
			foreach ($tags_array as $tag)
			{
				$tag = trim($tag);
				if ($tag == "")
				{
					continue;
				}

				$tag_id = $this->query_num_target("SELECT tag_id FROM {$this->kvs_config["tables_prefix"]}tags WHERE tag='" . $this->escape_string($tag) . "' LIMIT 1");
				if ($tag_id == 0)
				{
					$tag_dir = get_correct_dir_name($tag);

					$temp_dir = $tag_dir;
					for ($i = 2; $i < 9999; $i++)
					{
						if ($this->query_num_target("SELECT count(*) FROM {$this->kvs_config["tables_prefix"]}tags WHERE tag_dir='" . $this->escape_string($temp_dir) . "'") == 0)
						{
							$tag_dir = $temp_dir;
							break;
						}
						$temp_dir = $tag_dir . $i;
					}
					if ($this->query_target("INSERT INTO {$this->kvs_config["tables_prefix"]}tags SET tag='" . $this->escape_string($tag) . "', tag_dir='" . $this->escape_string($tag_dir) . "', added_date=now()"))
					{
						$tag_id = intval($this->mysql_link_target->insert_id);
					}
				}
				if ($tag_id > 0 && !$tags_added[$tag_id])
				{
					$this->query_target("INSERT INTO $tags_table SET tag_id=$tag_id, $object_key_field=$object_id");
					$tags_added[$tag_id] = true;
				}
			}
		}
	}

	/**
	 * @param string|null $categories
	 * @param string $categories_table
	 * @param string $object_key_field
	 * @param int $object_id
	 */
	private function add_categories_to_object(?string $categories, string $categories_table, string $object_key_field, int $object_id): void
	{
		if (!$categories)
		{
			return;
		}

		$categories_array = explode("||", $categories);
		$this->query_target("DELETE FROM $categories_table WHERE $object_key_field=$object_id");
		foreach ($categories_array as $category)
		{
			$category = trim($category);
			$category_id = $this->query_num_target("SELECT category_id FROM {$this->kvs_config["tables_prefix"]}categories WHERE title='" . $this->escape_string($category) . "' LIMIT 1");

			if ($category_id > 0)
			{
				$this->query_target("INSERT INTO $categories_table SET category_id=$category_id, $object_key_field=$object_id");
			}
		}
	}

	/**
	 * @param string|null $models
	 * @param string $models_table
	 * @param string $object_key_field
	 * @param int $object_id
	 */
	private function add_models_to_object(?string $models, string $models_table, string $object_key_field, int $object_id): void
	{
		if (!$models)
		{
			return;
		}

		$models_array = explode("||", $models);
		$this->query_target("DELETE FROM $models_table WHERE $object_key_field=$object_id");
		foreach ($models_array as $model)
		{
			$model = trim($model);
			$model_id = $this->query_num_target("SELECT model_id FROM {$this->kvs_config["tables_prefix"]}models WHERE title='" . $this->escape_string($model) . "' LIMIT 1");

			if ($model_id > 0)
			{
				$this->query_target("INSERT INTO $models_table SET model_id=$model_id, $object_key_field=$object_id");
			}
		}
	}

	/**
	 * @param string $p_key
	 * @param KvsDataMigratorSummaryItem $p_summary
	 */
	private function add_to_summary(string $p_key, KvsDataMigratorSummaryItem $p_summary): void
	{
		$this->summary["migration"][$p_key] = $p_summary->to_array();
	}
}