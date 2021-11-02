<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	// under web
	session_start();
	if ($_SESSION['userdata']['user_id']<1)
	{
		header("HTTP/1.0 403 Forbidden");
		die('Access denied');
	}
	header("Content-Type: text/plain; charset=utf8");
}

require_once "setup.php";
require_once "functions_base.php";
require_once "functions_admin.php";
require_once "functions.php";

if (!is_file("$config[project_path]/admin/data/system/cron_optimize.lock"))
{
	file_put_contents("$config[project_path]/admin/data/system/cron_optimize.lock", "1", LOCK_EX);
}

$lock=fopen("$config[project_path]/admin/data/system/cron_optimize.lock","r+");
if (!flock($lock,LOCK_EX | LOCK_NB))
{
	die('Already locked');
}

ini_set('display_errors',1);

$stats_cron=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/cron_optimize.dat"));

$start_time=time();

$options=get_options();

$now_date=date("Y-m-d H:i:s");

if (time()-$stats_cron['categories_summary']>28800)
{
	// populate categories summary (every 8 hours)
	sql("set wait_timeout=86400");

	$categories=mr2array_list(sql("select category_id from $config[tables_prefix]categories"));
	foreach($categories as $category_id)
	{
		$videos_data=mr2array_single(sql("select count(*) as total_videos, sum(added_today) as today_videos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(video_viewed) as viewed, max(ctr) as ctr from (
				select
					$config[tables_prefix]videos.video_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					video_viewed,
					r_ctr as ctr
				from
					$config[tables_prefix]videos inner join $config[tables_prefix]categories_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]categories_videos.video_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and category_id=$category_id
		) x"));
		$albums_data=mr2array_single(sql("select count(*) as total_albums, sum(added_today) as today_albums, sum(photos_amount) as total_photos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(album_viewed) as viewed from (
				select
					$config[tables_prefix]albums.album_id,
					photos_amount,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					album_viewed
				from
					$config[tables_prefix]albums inner join $config[tables_prefix]categories_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]categories_albums.album_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and category_id=$category_id
		) x"));
		$posts_data=mr2array_single(sql("select count(*) as total_posts, sum(added_today) as today_posts, avg(case when rating_amount<=1 then null else rating end) as rating, avg(post_viewed) as viewed from (
				select
					$config[tables_prefix]posts.post_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					post_viewed
				from
					$config[tables_prefix]posts inner join $config[tables_prefix]categories_posts on $config[tables_prefix]posts.post_id=$config[tables_prefix]categories_posts.post_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and category_id=$category_id
		) x"));


		sql_pr("update $config[tables_prefix]categories
				set
					total_videos=?, today_videos=?, total_albums=?, today_albums=?, total_photos=?, total_posts=?, today_posts=?, avg_videos_rating=?, avg_videos_popularity=?, max_videos_ctr=?, avg_albums_rating=?, avg_albums_popularity=?, avg_posts_rating=?, avg_posts_popularity=?
				where category_id=$category_id",
			intval($videos_data['total_videos']),intval($videos_data['today_videos']),intval($albums_data['total_albums']),intval($albums_data['today_albums']),intval($albums_data['total_photos']),intval($posts_data['total_posts']),intval($posts_data['today_posts']),floatval($videos_data['rating']),floatval($videos_data['viewed']),floatval($videos_data['ctr']),floatval($albums_data['rating']),floatval($albums_data['viewed']),floatval($posts_data['rating']),floatval($posts_data['viewed'])
		);
		usleep(150000);
	}

	$stats_cron['categories_summary']=time();
	log_output("INFO  Updated categories summary");
} elseif (time()-$stats_cron['tags_summary']>28800)
{
	// populate tags summary (every 8 hours)
	sql("set wait_timeout=86400");

	$tags=mr2array_list(sql("select tag_id from $config[tables_prefix]tags"));
	foreach($tags as $tag_id)
	{
		$videos_data=mr2array_single(sql("select count(*) as total_videos, sum(added_today) as today_videos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(video_viewed) as viewed from (
				select
					$config[tables_prefix]videos.video_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					video_viewed
				from
					$config[tables_prefix]videos inner join $config[tables_prefix]tags_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]tags_videos.video_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and tag_id=$tag_id
		) x"));
		$albums_data=mr2array_single(sql("select count(*) as total_albums, sum(added_today) as today_albums, sum(photos_amount) as total_photos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(album_viewed) as viewed from (
				select
					$config[tables_prefix]albums.album_id,
					photos_amount,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					album_viewed
				from
					$config[tables_prefix]albums inner join $config[tables_prefix]tags_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]tags_albums.album_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and tag_id=$tag_id
		) x"));
		$posts_data=mr2array_single(sql("select count(*) as total_posts, sum(added_today) as today_posts, avg(case when rating_amount<=1 then null else rating end) as rating, avg(post_viewed) as viewed from (
				select
					$config[tables_prefix]posts.post_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					post_viewed
				from
					$config[tables_prefix]posts inner join $config[tables_prefix]tags_posts on $config[tables_prefix]posts.post_id=$config[tables_prefix]tags_posts.post_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and tag_id=$tag_id
		) x"));

		sql_pr("update $config[tables_prefix]tags
				set
					total_videos=?, today_videos=?, total_albums=?, today_albums=?, total_photos=?, total_posts=?, today_posts=?, avg_videos_rating=?, avg_videos_popularity=?, avg_albums_rating=?, avg_albums_popularity=?, avg_posts_rating=?, avg_posts_popularity=?
				where tag_id=$tag_id",
			intval($videos_data['total_videos']),intval($videos_data['today_videos']),intval($albums_data['total_albums']),intval($albums_data['today_albums']),intval($albums_data['total_photos']),intval($posts_data['total_posts']),intval($posts_data['today_posts']),floatval($videos_data['rating']),floatval($videos_data['viewed']),floatval($albums_data['rating']),floatval($albums_data['viewed']),floatval($posts_data['rating']),floatval($posts_data['viewed'])
		);
		usleep(150000);
	}

	$stats_cron['tags_summary']=time();
	log_output("INFO  Updated tags summary");
} elseif (time()-$stats_cron['models_summary']>28800)
{
	// populate models summary (every 8 hours)
	sql("set wait_timeout=86400");

	$models=mr2array_list(sql("select model_id from $config[tables_prefix]models"));
	foreach($models as $model_id)
	{
		$model_data=mr2array_single(sql("select age, birth_date, death_date from $config[tables_prefix]models where model_id=$model_id"));
		$videos_data=mr2array_single(sql("select count(*) as total_videos, sum(added_today) as today_videos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(video_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]videos.video_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					video_viewed,
					post_date
				from
					$config[tables_prefix]videos inner join $config[tables_prefix]models_videos on $config[tables_prefix]videos.video_id=$config[tables_prefix]models_videos.video_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and model_id=$model_id
		) x"));
		$albums_data=mr2array_single(sql("select count(*) as total_albums, sum(added_today) as today_albums, sum(photos_amount) as total_photos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(album_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]albums.album_id,
					photos_amount,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					album_viewed,
					post_date
				from
					$config[tables_prefix]albums inner join $config[tables_prefix]models_albums on $config[tables_prefix]albums.album_id=$config[tables_prefix]models_albums.album_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and model_id=$model_id
		) x"));
		$posts_data=mr2array_single(sql("select count(*) as total_posts, sum(added_today) as today_posts, avg(case when rating_amount<=1 then null else rating end) as rating, avg(post_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]posts.post_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					post_viewed,
					post_date
				from
					$config[tables_prefix]posts inner join $config[tables_prefix]models_posts on $config[tables_prefix]posts.post_id=$config[tables_prefix]models_posts.post_id
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and model_id=$model_id
		) x"));
		$last_content_date='0000-00-00 00:00:00';
		$last_videos_date=0;
		$last_albums_date=0;
		$last_posts_date=0;
		if ($videos_data['last_content_date']<>'')
		{
			$last_videos_date=strtotime($videos_data['last_content_date']);
		}
		if ($albums_data['last_content_date']<>'')
		{
			$last_albums_date=strtotime($albums_data['last_content_date']);
		}
		if ($posts_data['last_content_date']<>'')
		{
			$last_posts_date=strtotime($posts_data['last_content_date']);
		}
		if ($last_videos_date>0 || $last_albums_date>0 || $last_posts_date>0)
		{
			$last_content_date=date("Y-m-d H:i:s",max(array($last_videos_date,$last_albums_date,$last_posts_date)));
		}

		$age=$model_data['age'];
		if ($model_data['birth_date']<>'0000-00-00')
		{
			if ($model_data['death_date']<>'0000-00-00')
			{
				$age=get_age(strtotime($model_data['birth_date']),strtotime($model_data['death_date']));
			} else
			{
				$age=get_age(strtotime($model_data['birth_date']));
			}
		}

		sql_pr("update $config[tables_prefix]models
				set
					age=?, total_videos=?, today_videos=?, total_albums=?, today_albums=?, total_photos=?, total_posts=?, today_posts=?, avg_videos_rating=?, avg_videos_popularity=?, avg_albums_rating=?, avg_albums_popularity=?, avg_posts_rating=?, avg_posts_popularity=?, last_content_date=?
				where model_id=$model_id",
			$age,intval($videos_data['total_videos']),intval($videos_data['today_videos']),intval($albums_data['total_albums']),intval($albums_data['today_albums']),intval($albums_data['total_photos']),intval($posts_data['total_posts']),intval($posts_data['today_posts']),floatval($videos_data['rating']),floatval($videos_data['viewed']),floatval($albums_data['rating']),floatval($albums_data['viewed']),floatval($posts_data['rating']),floatval($posts_data['viewed']),$last_content_date
		);
		usleep(150000);
	}

	if (in_array(trim($options['MODELS_RANK_BY']),array('rating','model_viewed','comments_count','subscribers_count','total_videos','avg_videos_rating','avg_videos_popularity','total_albums','avg_albums_rating','avg_albums_popularity','added_date')))
	{
		$rank_by=trim($options['MODELS_RANK_BY']);
		if ($rank_by=='rating')
		{
			$rank_by='rating/rating_amount desc, rating_amount';
		}
		$models = mr2array(sql_pr("select model_id, @curRank := @curRank + 1 AS calc_rank from $config[tables_prefix]models, (select @curRank := 0) r order by $rank_by desc, model_id asc"));

		$model_ids = '';
		$model_update_sql = '';
		for ($i = 1; $i <= count($models); $i++)
		{
			$model = $models[$i-1];
			$model_ids .= ",$model[model_id]";
			$model_update_sql .= "when $model[model_id] then $model[calc_rank] ";

			if ($i % 100 == 0)
			{
				sql_update("update $config[tables_prefix]models set last_rank=`rank`, `rank`=(case model_id $model_update_sql end) where model_id in (0$model_ids)");
				$model_ids = '';
				$model_update_sql = '';
				usleep(150000);
			}
		}
		if ($model_update_sql)
		{
			sql_update("update $config[tables_prefix]models set last_rank=`rank`, `rank`=(case model_id $model_update_sql end) where model_id in (0$model_ids)");
		}
	}

	$stats_cron['models_summary']=time();
	log_output("INFO  Updated models summary");
} elseif (time()-$stats_cron['cs_summary']>28800)
{
	// populate content sources summary (every 8 hours)
	sql("set wait_timeout=86400");

	$content_sources=mr2array_list(sql("select content_source_id from $config[tables_prefix]content_sources"));
	foreach($content_sources as $content_source_id)
	{
		$videos_data=mr2array_single(sql("select count(*) as total_videos, sum(added_today) as today_videos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(video_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]videos.video_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					video_viewed,
					post_date
				from
					$config[tables_prefix]videos
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$content_source_id
		) x"));
		$albums_data=mr2array_single(sql("select count(*) as total_albums, sum(added_today) as today_albums, sum(photos_amount) as total_photos, avg(case when rating_amount<=1 then null else rating end) as rating, avg(album_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]albums.album_id,
					photos_amount,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					album_viewed,
					post_date
				from
					$config[tables_prefix]albums
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and content_source_id=$content_source_id
		) x"));

		$last_content_date='0000-00-00 00:00:00';
		$last_videos_date=0;
		$last_albums_date=0;
		if ($videos_data['last_content_date']<>'')
		{
			$last_videos_date=strtotime($videos_data['last_content_date']);
		}
		if ($albums_data['last_content_date']<>'')
		{
			$last_albums_date=strtotime($albums_data['last_content_date']);
		}
		if ($last_videos_date>0 || $last_albums_date>0)
		{
			$last_content_date=date("Y-m-d H:i:s",max($last_videos_date,$last_albums_date));
		}

		sql_pr("update $config[tables_prefix]content_sources set total_videos=?, today_videos=?, total_albums=?, today_albums=?, total_photos=?, avg_videos_rating=?, avg_videos_popularity=?, avg_albums_rating=?, avg_albums_popularity=?, last_content_date=? where content_source_id=$content_source_id",
		intval($videos_data['total_videos']),intval($videos_data['today_videos']),intval($albums_data['total_albums']),intval($albums_data['today_albums']),intval($albums_data['total_photos']),floatval($videos_data['rating']),floatval($videos_data['viewed']),floatval($albums_data['rating']),floatval($albums_data['viewed']),$last_content_date);
		usleep(150000);
	}

	if (in_array(trim($options['CS_RANK_BY']),array('rating','cs_viewed','comments_count','subscribers_count','total_videos','avg_videos_rating','avg_videos_popularity','total_albums','avg_albums_rating','avg_albums_popularity','added_date')))
	{
		$rank_by=trim($options['CS_RANK_BY']);
		if ($rank_by=='rating')
		{
			$rank_by='rating/rating_amount desc, rating_amount';
		}
		$content_sources = mr2array(sql_pr("select content_source_id, @curRank := @curRank + 1 AS calc_rank from $config[tables_prefix]content_sources, (select @curRank := 0) r order by $rank_by desc, content_source_id asc"));

		$content_source_ids = '';
		$content_source_update_sql = '';
		for ($i = 1; $i <= count($content_sources); $i++)
		{
			$content_source = $content_sources[$i-1];
			$content_source_ids .= ",$content_source[content_source_id]";
			$content_source_update_sql .= "when $content_source[content_source_id] then $content_source[calc_rank] ";

			if ($i % 100 == 0)
			{
				sql_update("update $config[tables_prefix]content_sources set last_rank=`rank`, `rank`=(case content_source_id $content_source_update_sql end) where content_source_id in (0$content_source_ids)");
				$content_source_ids = '';
				$content_source_update_sql = '';
				usleep(150000);
			}
		}
		if ($content_source_update_sql)
		{
			sql_update("update $config[tables_prefix]content_sources set last_rank=`rank`, `rank`=(case content_source_id $content_source_update_sql end) where content_source_id in (0$content_source_ids)");
		}
	}

	$stats_cron['cs_summary']=time();
	log_output("INFO  Updated content sources summary");
} elseif (time()-$stats_cron['dvds_summary']>28800)
{
	// populate dvds summary (every 8 hours)
	sql("set wait_timeout=86400");

	$dvds=mr2array_list(sql("select dvd_id from $config[tables_prefix]dvds"));
	foreach($dvds as $dvd_id)
	{
		$videos_data=mr2array_single(sql("select count(*) as total_videos, sum(added_today) as today_videos, sum(duration) as total_videos_duration, avg(case when rating_amount<=1 then null else rating end) as rating, avg(video_viewed) as viewed, max(post_date) as last_content_date from (
				select
					$config[tables_prefix]videos.video_id,
					case when timestampdiff(SECOND, post_date, '$now_date') between 0 and 86400 then 1 else 0 end as added_today,
					coalesce(rating/rating_amount,0) as rating,
					rating_amount,
					video_viewed,
					post_date,
					duration
				from
					$config[tables_prefix]videos
				where
					status_id=1 and relative_post_date>=0 and post_date<='$now_date' and dvd_id=$dvd_id
		) x"));

		$last_content_date='0000-00-00 00:00:00';
		if ($videos_data['last_content_date']<>'')
		{
			$last_content_date=$videos_data['last_content_date'];
		}

		sql_pr("update $config[tables_prefix]dvds set total_videos=?, today_videos=?, total_videos_duration=?, avg_videos_rating=?, avg_videos_popularity=?, last_content_date=? where dvd_id=$dvd_id",
			intval($videos_data['total_videos']),intval($videos_data['today_videos']),intval($videos_data['total_videos_duration']),floatval($videos_data['rating']),floatval($videos_data['viewed']),$last_content_date);
		usleep(150000);
	}

	$stats_cron['dvds_summary']=time();
	log_output("INFO  Updated DVDs summary");
} elseif (time()-$stats_cron['playlists_summary']>28800)
{
	// populate playlists summary (every 8 hours)
	sql("set wait_timeout=86400");

	sql_pr("update $config[tables_prefix]playlists set total_videos=(select count(*) from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $config[tables_prefix]videos.status_id=1 and $config[tables_prefix]videos.relative_post_date>=0 and $config[tables_prefix]videos.post_date<='$now_date' and $config[tables_prefix]playlists.playlist_id=$config[tables_prefix]fav_videos.playlist_id)");

	$stats_cron['playlists_summary']=time();
	log_output("INFO  Updated playlists summary");
} elseif (time()-$stats_cron['users_summary']>43200)
{
	$formula = $options['ACTIVITY_INDEX_FORMULA'];
	if ($formula != '')
	{
		log_output("INFO  Using formula for activity index: $formula");

		$users = explode(",", $options['ACTIVITY_INDEX_INCLUDES']);
		$users_ids = array(0);
		if (is_array($users))
		{
			$users = array_map("trim", $users);
			$users = array_unique($users);
			foreach ($users as $user)
			{
				if (strlen($user) > 0)
				{
					$user_id = mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $user));
					if ($user_id > 0)
					{
						$users_ids[] = $user_id;
					}
				}
			}
		}
		$users_ids_str = implode(",", $users_ids);
		log_output("INFO  Excluding users from activity index: $users_ids_str");

		$formula = transform_activity_index_formula($formula);
		sql("set low_priority_updates=1");
		sql("update $config[tables_prefix]users set activity=($formula) where status_id!=4 and user_id not in ($users_ids_str)");
		sql("update $config[tables_prefix]users set activity=0 where status_id=4 or user_id in ($users_ids_str)");
		sql("update $config[tables_prefix]users join (select user_id, @curRank := @curRank + 1 AS calc_rank from $config[tables_prefix]users, (select @curRank := 0) r order by activity desc) ranks on $config[tables_prefix]users.user_id=ranks.user_id set activity_last_rank=activity_rank, activity_rank=ranks.calc_rank");
	}

	$exclude_users = array_map('trim', explode(",", $options['TOKENS_SALE_EXCLUDES']));
	$users_ids = array(0);
	$users_ids[] = mr2number(sql("select user_id from $config[tables_prefix]users where status_id=4 limit 1"));
	if (is_array($exclude_users))
	{
		foreach ($exclude_users as $user)
		{
			if (strlen($user) > 0)
			{
				$user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?", $user));
				if ($user_id > 0)
				{
					$users_ids[] = $user_id;
				}
			}
		}
	}
	$users_ids_str = implode(",", $users_ids);
	log_output("INFO  Excluding users from tokens earning: $users_ids_str");

	$map_users_tokens = array();
	if (intval($options['ENABLE_TOKENS_TRAFFIC_VIDEOS']))
	{
		$traffic_tokens = intval($options['TOKENS_TRAFFIC_VIDEOS_TOKENS']);
		$traffic_views = intval($options['TOKENS_TRAFFIC_VIDEOS_UNIQUE']);

		if ($traffic_tokens > 0 && $traffic_views > 0)
		{
			$videos_to_pay = mr2array(sql("select video_id, user_id, video_viewed_unique, video_viewed_paid from $config[tables_prefix]videos where user_id not in ($users_ids_str) and video_viewed_unique-video_viewed_paid>=$traffic_views"));
			foreach ($videos_to_pay as $video_to_pay)
			{
				$multiplier = floor((intval($video_to_pay['video_viewed_unique']) - intval($video_to_pay['video_viewed_paid'])) / $traffic_views);
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=11, video_id=?, amount=?, user_id=?, tokens_granted=?, added_date='$now_date'",
					$video_to_pay['video_id'], $traffic_views * $multiplier, $video_to_pay['user_id'], $traffic_tokens * $multiplier
				);
				sql_pr("update $config[tables_prefix]videos set video_viewed_paid=video_viewed_paid+? where video_id=?", $traffic_views * $multiplier, $video_to_pay['video_id']);
				$map_users_tokens[$video_to_pay['user_id']] += $traffic_tokens * $multiplier;
			}
		}
	}

	if (intval($options['ENABLE_TOKENS_TRAFFIC_ALBUMS']))
	{
		$traffic_tokens = intval($options['TOKENS_TRAFFIC_ALBUMS_TOKENS']);
		$traffic_views = intval($options['TOKENS_TRAFFIC_ALBUMS_UNIQUE']);

		if ($traffic_tokens > 0 && $traffic_views > 0)
		{
			$albums_to_pay = mr2array(sql("select album_id, user_id, album_viewed_unique, album_viewed_paid from $config[tables_prefix]albums where user_id not in ($users_ids_str) and album_viewed_unique-album_viewed_paid>=$traffic_views"));
			foreach ($albums_to_pay as $album_to_pay)
			{
				$multiplier = floor((intval($album_to_pay['album_viewed_unique']) - intval($album_to_pay['album_viewed_paid'])) / $traffic_views);
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=12, album_id=?, amount=?, user_id=?, tokens_granted=?, added_date='$now_date'",
					$album_to_pay['album_id'], $traffic_views * $multiplier, $album_to_pay['user_id'], $traffic_tokens * $multiplier
				);
				sql_pr("update $config[tables_prefix]albums set album_viewed_paid=album_viewed_paid+? where album_id=?", $traffic_views * $multiplier, $album_to_pay['album_id']);
				$map_users_tokens[$album_to_pay['user_id']] += $traffic_tokens * $multiplier;
			}
		}
	}

	if (intval($options['ENABLE_TOKENS_TRAFFIC_EMBEDS']))
	{
		$traffic_tokens = intval($options['TOKENS_TRAFFIC_EMBEDS_TOKENS']);
		$traffic_views = intval($options['TOKENS_TRAFFIC_EMBEDS_UNIQUE']);

		if ($traffic_tokens > 0 && $traffic_views > 0)
		{
			$videos_to_pay = mr2array(sql("select video_id, user_id, embed_viewed_unique, embed_viewed_paid from $config[tables_prefix]videos where user_id not in ($users_ids_str) and embed_viewed_unique-embed_viewed_paid>=$traffic_views"));
			foreach ($videos_to_pay as $video_to_pay)
			{
				$multiplier = floor((intval($video_to_pay['embed_viewed_unique']) - intval($video_to_pay['embed_viewed_paid'])) / $traffic_views);
				sql_pr("insert into $config[tables_prefix]log_awards_users set award_type=17, video_id=?, amount=?, user_id=?, tokens_granted=?, added_date='$now_date'",
					$video_to_pay['video_id'], $traffic_views * $multiplier, $video_to_pay['user_id'], $traffic_tokens * $multiplier
				);
				sql_pr("update $config[tables_prefix]videos set embed_viewed_paid=embed_viewed_paid+? where video_id=?", $traffic_views * $multiplier, $video_to_pay['video_id']);
				$map_users_tokens[$video_to_pay['user_id']] += $traffic_tokens * $multiplier;
			}
		}
	}

	foreach ($map_users_tokens as $user_id => $tokens_granted)
	{
		sql_pr("update $config[tables_prefix]users set tokens_available=tokens_available+? where user_id=?", $tokens_granted, $user_id);
		log_output("INFO  Paid $tokens_granted tokens to user $user_id");
	}

	$stats_cron['users_summary']=time();
	log_output("INFO  Updated users summary");
} elseif (time()-$stats_cron['videos_pseudo_random']>3600)
{
	sql("set low_priority_updates=1");
	sql("update $config[tables_prefix]videos set random1=floor(rand()*100000)");
	$stats_cron['videos_pseudo_random']=time();
	log_output("INFO  Updated videos pseudo random index");
} elseif (time()-$stats_cron['albums_pseudo_random']>3600)
{
	sql("set low_priority_updates=1");
	sql("update $config[tables_prefix]albums set random1=floor(rand()*100000)");
	$stats_cron['albums_pseudo_random']=time();
	log_output("INFO  Updated albums pseudo random index");
}

$time=time()-$start_time;
log_output("INFO  Finished in $time seconds");

file_put_contents("$config[project_path]/admin/data/system/cron_optimize.dat", serialize($stats_cron), LOCK_EX);

flock($lock,LOCK_UN);
fclose($lock);

function log_output($message)
{
	if ($message=='')
	{
		$message="\n";
	} else {
		$message=date("[Y-m-d H:i:s] ").$message."\n";
	}

	echo $message;
}
