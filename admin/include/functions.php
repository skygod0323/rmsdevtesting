<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function is_video_secure($file)
{
	if (is_file($file))
	{
		$fh=fopen($file,"rb");
		if ($fh)
		{
			$contents=fread($fh,100);
			if (strpos($contents,'EXTM3U')===false)
			{
				return true;
			}
		}
	}
	return false;
}

function is_audio($file)
{
	global $config;

	if (is_file($file))
	{
		if ($config['ffmpeg_path']<>'' && is_video_secure($file))
		{
			unset($res);
			exec("$config[ffmpeg_path] -i \"$file\"  2>&1",$res);
			if (preg_match("|Video:|is",implode("\r\n",$res)))
			{
				return false;
			}
			if (preg_match("|Audio:|is",implode("\r\n",$res)))
			{
				return true;
			}
		}
	}
	return false;
}

function get_video_duration($file)
{
	global $config;
	$duration=0;
	if (is_file($file))
	{
		if ($config['ffmpeg_path']<>'' && is_video_secure($file))
		{
			unset($res);
			exec("$config[ffmpeg_path] -i \"$file\"  2>&1",$res);
			preg_match("|Duration: (\d+:\d+:[0-9\.]+)|is",implode("\r\n",$res),$temp);
			$temp[1]=explode(":",$temp[1]);
			$duration=round($temp[1][0])*3600+round($temp[1][1])*60+round($temp[1][2]);
		}
	}
	return $duration;
}

function get_video_dimensions($file)
{
	global $config;
	$video_width=0;
	$video_height=0;

	if (is_file($file))
	{
		if ($config['ffmpeg_path']<>'' && is_video_secure($file))
		{
			$video_rotation=0;

			$ffprobe_path=str_replace("ffmpeg","ffprobe",$config['ffmpeg_path']);
			if (strpos($ffprobe_path,' -')>0)
			{
				$ffprobe_path=substr($ffprobe_path,0,strpos($ffprobe_path,' -'));
			}
			unset($res);
			exec("$ffprobe_path \"$file\" -show_streams 2>&1",$res);
			foreach ($res as $output_row)
			{
				if (strpos($output_row,'TAG:rotate=')===0)
				{
					$video_rotation=intval(substr($output_row,11));
					break;
				}
			}

			$rnd=mt_rand(1000000,999999999);
			if (!is_dir("$config[temporary_path]/$rnd")) {mkdir("$config[temporary_path]/$rnd",0777);chmod("$config[temporary_path]/$rnd",0777);}
			exec("$config[ffmpeg_path] -ss 0 -i \"$file\" -vframes 1 -y -f mjpeg -vf \"scale=trunc(iw*sar/2)*2:ih\" $config[temporary_path]/$rnd/00000001.jpg");
			if (is_file("$config[temporary_path]/$rnd/00000001.jpg"))
			{
				$size=getimagesize("$config[temporary_path]/$rnd/00000001.jpg");
				@unlink("$config[temporary_path]/$rnd/00000001.jpg");
				@rmdir("$config[temporary_path]/$rnd");
				$video_width=$size[0];
				$video_height=$size[1];
				if ($video_width>0 && $video_height>0)
				{
					@rmdir("$config[temporary_path]/$rnd");
					return array($video_width,$video_height);
				} else {
					@rmdir("$config[temporary_path]/$rnd");
				}
			}

			unset($res);
			exec("$config[ffmpeg_path] -i \"$file\"  2>&1",$res);
			preg_match_all("|\d+x\d+|is",implode("\r\n",$res),$temp);
			foreach ($temp[0] as $potential_size)
			{
				$potential_size=explode("x",$potential_size);
				if (intval($potential_size[0])>0 && intval($potential_size[1])>0)
				{
					if ($video_rotation==90)
					{
						return array($potential_size[1],$potential_size[0]);
					} else {
						return array($potential_size[0],$potential_size[1]);
					}
				}
			}
		}
	}
	return array($video_width,$video_height);
}

function get_video_formats($video_id,$formats_data,$server_group_id=0)
{
	global $config;

	$result=array();
	$dir_path=get_dir_by_id($video_id);

	$temp=explode('||',$formats_data);
	foreach ($temp as $temp_format)
	{
		if (strlen($temp_format)>0)
		{
			$temp_format=explode('|',$temp_format);
			$format_item=array();
			$format_item['postfix']=$temp_format[0];
			$format_item['dimensions']=explode("x",$temp_format[1]);
			$format_item['duration']=$temp_format[2];
			$format_item['duration_string']=durationToHumanString($temp_format[2]);
			$format_item['duration_array']=get_duration_splitted($temp_format[2]);
			$format_item['file_size']=$temp_format[3];
			$format_item['file_size_string']=sizeToHumanString($temp_format[3],2);
			$format_item['timeline_screen_amount']=intval($temp_format[4]);
			$format_item['timeline_screen_interval']=intval($temp_format[5]);
			$format_item['timeline_cuepoints']=intval($temp_format[6]);

			$file_path="$dir_path/$video_id/$video_id{$temp_format[0]}";
			$hash=md5($config['cv'].$file_path);
			$format_item['file_name']="$video_id{$temp_format[0]}";
			$format_item['file_path']="$hash/$file_path";
			if ($server_group_id>0)
			{
				$format_item['file_url']="$config[project_url]/get_file/$server_group_id/$hash/$file_path/";
				if ($config['omit_slash_video_files'] == 'true')
				{
					$format_item['file_url'] = rtrim($format_item['file_url'], '/');
				}
			}

			$result[$temp_format[0]]=$format_item;
		}
	}
	return $result;
}

function pack_video_formats($formats)
{
	$result='';
	foreach ($formats as $format)
	{
		$dimensions=$format['dimensions'];
		$timeline_screen_amount=intval($format['timeline_screen_amount']);
		$timeline_screen_interval=intval($format['timeline_screen_interval']);
		$timeline_cuepoints=intval($format['timeline_cuepoints']);
		$result.="||$format[postfix]|$dimensions[0]x$dimensions[1]|$format[duration]|$format[file_size]|$timeline_screen_amount|$timeline_screen_interval|$timeline_cuepoints";
	}
	return $result;
}

function get_video_source_url($video_id, $filename)
{
	global $config;

	$dir_path = get_dir_by_id($video_id);

	if ($config['server_type'] != 'nginx' || strpos($config['content_url_videos_sources'], $config['project_url']) === false)
	{
		$result = "$config[content_url_videos_sources]/$dir_path/$video_id/$filename";
	} else
	{
		$hash = md5($config['cv'] . "$dir_path/$video_id/$filename");
		$result = "$config[project_url]/get_file/0/$hash/$dir_path/$video_id/$filename/";
		if ($config['omit_slash_video_files'] == 'true')
		{
			$result = rtrim($result, '/');
		}
	}
	return $result;
}

function get_video_source_base_url()
{
	global $config;

	if ($config['server_type'] != 'nginx' || strpos($config['content_url_videos_sources'], $config['project_url']) === false)
	{
		return $config['content_url_videos_sources'];
	} else
	{
		return "$config[project_url]/get_file/0/";
	}
}

function get_image_formats($album_id,$formats_data)
{
	$result=array();

	$temp=explode('||',$formats_data);
	foreach ($temp as $temp_format)
	{
		if (strlen($temp_format)>0)
		{
			$temp_format=explode('|',$temp_format);
			$format_item=array();
			$format_item['size']=$temp_format[0];
			$format_item['dimensions']=explode("x",$temp_format[1]);
			$format_item['file_size']=$temp_format[2];
			$format_item['file_size_string']=sizeToHumanString($temp_format[2],2);

			$result[$temp_format[0]]=$format_item;
		}
	}
	return $result;
}

function pack_image_formats($formats)
{
	$result='';
	foreach ($formats as $format)
	{
		$dimensions=$format['dimensions'];
		$result.="||$format[size]|$dimensions[0]x$dimensions[1]|$format[file_size]";
	}
	return $result;
}

function get_album_zip_files($album_id,$zip_data,$server_group_id=0)
{
	global $config;

	$result=array();

	$dir_path=get_dir_by_id($album_id);
	$temp=explode('||',$zip_data);
	foreach ($temp as $temp_zip)
	{
		if (strlen($temp_zip)>0)
		{
			$temp_zip=explode('|',$temp_zip);
			$zip_item=array();
			$zip_item['size']=$temp_zip[0];
			$zip_item['file_size']=$temp_zip[1];
			$zip_item['file_size_string']=sizeToHumanString($temp_zip[1],2);

			if ($temp_zip[0]=='source')
			{
				$file_path="sources/$dir_path/$album_id/$album_id.zip";
				$file_name="$album_id.zip";
			} else {
				$file_path="main/$temp_zip[0]/$dir_path/$album_id/$album_id-$temp_zip[0].zip";
				$file_name="$album_id-$temp_zip[0].zip";
			}
			$hash=md5($config['cv'].$file_path);
			$zip_item['file_name']="$file_name";
			$zip_item['file_path']="$hash/$file_path";
			if ($server_group_id>0)
			{
				$zip_item['file_url']="$config[project_url]/get_image/$server_group_id/$hash/$file_path/";
			}

			$result[$temp_zip[0]]=$zip_item;
		}
	}
	return $result;
}

function pack_album_zip_files($zip_files)
{
	$result='';
	foreach ($zip_files as $zip_file)
	{
		$result.="||$zip_file[size]|$zip_file[file_size]";
	}
	return $result;
}

function load_balance_screenshots_url()
{
	global $config;

	if (is_array($config['alt_urls_videos_screenshots']) && count($config['alt_urls_videos_screenshots'])>0)
	{
		$alt_urls_videos_screenshots=$config['alt_urls_videos_screenshots'];
		$alt_urls_videos_screenshots[]=$config['content_url_videos_screenshots'];

		$i=mt_rand(0,count($alt_urls_videos_screenshots)-1);
		return $alt_urls_videos_screenshots[$i];
	}
	return $config['content_url_videos_screenshots'];
}

function load_balance_categories_url()
{
	global $config;

	if (is_array($config['alt_urls_categories']) && count($config['alt_urls_categories'])>0)
	{
		$alt_urls_categories=$config['alt_urls_categories'];
		$alt_urls_categories[]=$config['content_url_categories'];

		$i=mt_rand(0,count($alt_urls_categories)-1);
		return $alt_urls_categories[$i];
	}
	return $config['content_url_categories'];
}

function load_balance_server($server_group_id,$cluster_servers,$cluster_servers_weights)
{
	global $config;

	if (intval($server_group_id)==0)
	{
		return array();
	}

	$lb_servers=$cluster_servers[$server_group_id];
	if ($config['is_clone_db']=="true" && $config['satellite_for']!='')
	{
		foreach ($lb_servers as $k=>$v)
		{
			if (intval($lb_servers[$k]['is_replace_domain_on_satellite'])==1)
			{
				$lb_servers[$k]['urls']=str_replace($config['satellite_for'],$config['project_licence_domain'],$lb_servers[$k]['urls']);
				if (strpos($lb_servers[$k]['urls'],'https://')!==false && strpos($config['project_url'],'https://')===false)
				{
					$lb_servers[$k]['urls']=str_replace('https://','http://',$lb_servers[$k]['urls']);
				}
			}
		}
	}
	if ($config['mirror_for']!='')
	{
		foreach ($lb_servers as $k=>$v)
		{
			if (intval($lb_servers[$k]['is_replace_domain_on_satellite'])==1)
			{
				$lb_servers[$k]['urls']=str_replace($config['mirror_for'],$config['project_licence_domain'],$lb_servers[$k]['urls']);
				if (strpos($lb_servers[$k]['urls'],'https://')!==false && strpos($config['project_url'],'https://')===false)
				{
					$lb_servers[$k]['urls']=str_replace('https://','http://',$lb_servers[$k]['urls']);
				}
			}
		}
	}

	$lb_weight=$cluster_servers_weights[$server_group_id];
	if ($lb_weight>0)
	{
		$lb_value=mt_rand(1,$lb_weight);
	} else
	{
		$lb_value=0;
	}
	$cur_value=0;
	foreach ($lb_servers as $server)
	{
		if ($lb_value<=$cur_value+$server['lb_weight'])
		{
			return $server;
		}
		$cur_value+=$server['lb_weight'];
	}
	return $lb_servers[0];
}

function get_time_passed($date)
{
	$interval=time()-strtotime($date);

	if ($interval<0) {$interval=0;}
	if ($interval<60)
	{
		$range['value']=$interval;
		$range['type']="seconds";
	} else {
		$temp_interval=floor($interval/60);
		if ($temp_interval<60)
		{
			$range['value']=$temp_interval;
			$range['type']="minutes";
		} else {
			$temp_interval=floor($interval/(60*60));
			if ($temp_interval<24)
			{
				$range['value']=$temp_interval;
				$range['type']="hours";
			} else {
				$temp_interval=floor($interval/(60*60*24));
				if ($temp_interval<7)
				{
					$range['value']=$temp_interval;
					$range['type']="days";
				} else {
					$temp_interval=floor($interval/(60*60*24*7));
					if ($temp_interval<5)
					{
						$range['value']=$temp_interval;
						$range['type']="weeks";
					} else {
						$temp_interval=floor($interval/(60*60*24*30));
						if ($temp_interval<12)
						{
							$range['value']=$temp_interval;
							$range['type']="months";
						} else {
							$temp_interval=floor($interval/(60*60*24*365));
							$range['value']=$temp_interval;
							$range['type']="years";
						}
					}
				}
			}
		}
	}
	return $range;
}

function get_duration_splitted($duration)
{
	$res=array();
	$res['minutes']=ceil($duration/60);
	$res['seconds']=ceil($duration-($res['minutes']*60));

	if ($res['seconds']<0)
	{
		$res['minutes']=$res['minutes']-1;
		$res['seconds']=$res['seconds']+60;
	}

	if ($res['minutes']<1) {$res['minutes']=0;}
	if ($res['seconds']<1) {$res['seconds']=0;}
	if ($res['seconds']<10) {$res['seconds']="0".$res['seconds'];}

	$res['text']=durationToHumanString($duration);
	return $res;
}

function process_blocked_words($string, $should_replace = false)
{
	global $config;

	if (!$string)
	{
		return '';
	}

	$blocked_words = @unserialize(@file_get_contents("$config[project_path]/admin/data/system/blocked_words.dat"));
	if (is_array($blocked_words))
	{
		$regex_replacements = explode("\n", trim($blocked_words['REGEX_REPLACEMENTS']));
		foreach ($regex_replacements as $regex_replacement)
		{
			$regex_replacement = trim($regex_replacement);
			if ($regex_replacement)
			{
				$regex_replacement_last_separator = strrpos($regex_replacement, ':');
				if ($regex_replacement_last_separator)
				{
					$regex_replacement_value = trim(substr($regex_replacement, $regex_replacement_last_separator + 1));
					if ($regex_replacement_value == ';')
					{
						$regex_replacement_value = ' ';
					}
					$string = preg_replace(trim(substr($regex_replacement, 0, $regex_replacement_last_separator)), $regex_replacement_value, $string);
				}
			}
		}

		if ($blocked_words['BLOCKED_WORDS'] != '')
		{
			$blocked_words['BLOCKED_WORDS'] = str_replace(array("\n", "\r"), ',', $blocked_words['BLOCKED_WORDS']);
			$source = array_map('trim', explode(',', $blocked_words['BLOCKED_WORDS']));
			$regexps = array();
			foreach ($source as $blocked_word)
			{
				if ($blocked_word != '')
				{
					if (strpos($blocked_word, '*') !== false)
					{
						$regexps[] = str_replace('\*', '\w*', preg_quote($blocked_word, "/"));
					} else
					{
						$regexps[] = '\b' . preg_quote($blocked_word, "/") . '\b';
					}
				}
			}

			foreach ($regexps as $regexp)
			{
				if ($should_replace)
				{
					$string = preg_replace("/$regexp/iu", trim($blocked_words['BLOCKED_WORDS_REPLACEMENT']), $string);
				} else
				{
					$string = preg_replace("/$regexp/iu", '', $string);
				}
			}
			if ($should_replace && trim($blocked_words['BLOCKED_WORDS_REPLACEMENT']))
			{
				$repeating_regexp = preg_quote(trim($blocked_words['BLOCKED_WORDS_REPLACEMENT']), "/");
				$string = preg_replace("/($repeating_regexp(\s*)){2,}/iu", trim($blocked_words['BLOCKED_WORDS_REPLACEMENT']) . "\\2", $string);
			}
		}
	}

	return trim($string);
}

function find_or_create_tag($tag, $options)
{
	global $config, $database_selectors;

	$tag = trim($tag);
	if ($tag === '')
	{
		return 0;
	}

	require_once "$config[project_path]/admin/include/database_selectors.php";
	if ($database_selectors["locale_field_tag"] !== "tag")
	{
		$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=? or $database_selectors[locale_field_tag]=?", $tag, $tag));
	} else
	{
		$tag_id = mr2number(sql_pr("select tag_id from $config[tables_prefix]tags where tag=?", $tag));
	}
	if ($tag_id == 0)
	{
		$synonym_tags = mr2array(sql_pr("select tag_id, synonyms from $config[tables_prefix]tags where synonyms like ?", "%$tag%"));
		foreach ($synonym_tags as $synonym_tag)
		{
			$temp_syn_list = explode(",", $synonym_tag['synonyms']);
			foreach ($temp_syn_list as $syn)
			{
				if (mb_lowercase($tag) == mb_lowercase(trim($syn)))
				{
					$tag_id = $synonym_tag['tag_id'];
					break 2;
				}
			}
		}

		if ($tag_id == 0)
		{
			if (intval($options['TAGS_DISABLE_ALL']) == 1)
			{
				return 0;
			}

			if (intval($options['TAGS_DISABLE_COMPOUND']) > 0)
			{
				$words = explode(' ', $tag);
				if (count($words) > intval($options['TAGS_DISABLE_COMPOUND']))
				{
					return 0;
				}
			}

			$tag_length = strlen($tag);
			if (function_exists('mb_detect_encoding'))
			{
				$tag_length = mb_strlen($tag, mb_detect_encoding($tag));
			}
			if (intval($options['TAGS_DISABLE_LENGTH_MIN']) > 0 && $tag_length < intval($options['TAGS_DISABLE_LENGTH_MIN']))
			{
				return 0;
			}
			if (intval($options['TAGS_DISABLE_LENGTH_MAX']) > 0 && $tag_length > intval($options['TAGS_DISABLE_LENGTH_MAX']))
			{
				return 0;
			}

			if ($options['TAGS_DISABLE_CHARACTERS'])
			{
				foreach (str_split($options['TAGS_DISABLE_CHARACTERS']) as $char)
				{
					if (mb_contains($tag, $char, true))
					{
						return 0;
					}
				}
			}

			if (intval($options['TAGS_DISABLE_LIST_ENABLED']) == 1)
			{
				unset($words);
				preg_match_all('/[\p{N}\p{L}-_#@\']+/u', $tag, $words);
				$words = $words[0];

				$checks = array_map('trim', preg_split('/(,|\\n)/', $options['TAGS_DISABLE_LIST']));
				foreach ($checks as $check)
				{
					if (strpos($check, '*') !== false)
					{
						$regexp = str_replace('\*','\w*', preg_quote($check, "/"));
						foreach ($words as $word)
						{
							unset($temp);
							if (preg_match("/$regexp/iu", $word, $temp))
							{
								if ($temp[0] == $word)
								{
									return 0;
								}
							}
						}
					} elseif ($check == $tag)
					{
						return 0;
					}
				}
			}

			if (intval($options['TAGS_FORCE_LOWERCASE']) == 1)
			{
				$tag = mb_lowercase($tag);
			}

			$tag_status_id = 1;
			if (intval($options['TAGS_FORCE_DISABLED']) == 1)
			{
				$tag_status_id = 0;
			}

			$tag_dir = get_correct_dir_name($tag);
			$temp_dir = $tag_dir;
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]tags where tag_dir=?", $temp_dir)) == 0)
				{
					$tag_dir = $temp_dir;
					break;
				}
				$temp_dir = $tag_dir . $i;
			}
			return sql_insert("insert into $config[tables_prefix]tags set tag=?, tag_dir=?, status_id=?, added_date=?", $tag, $tag_dir, $tag_status_id, date("Y-m-d H:i:s"));
		}
	}

	return $tag_id;
}

function fav_videos_changed($video_ids_str,$fav_type)
{
	global $config,$database_selectors;

	$user_id=intval($_SESSION['user_id']);
	if ($user_id==0 || $video_ids_str=='')
	{
		return;
	}

	require_once("$config[project_path]/admin/include/database_selectors.php");
	$videos_changed=mr2array(sql("select video_id, $database_selectors[generic_selector_dir] as dir from $config[tables_prefix]videos where video_id in ($video_ids_str)"));
	foreach ($videos_changed as $video)
	{
		inc_block_version('videos_info','video',$video['video_id'],$video['dir'],$user_id);
	}

	sql_pr("update $config[tables_prefix]videos v inner join (select video_id, count(*) as cnt from $config[tables_prefix]fav_videos where video_id in ($video_ids_str) group by video_id) x on v.video_id=x.video_id set v.favourites_count=x.cnt");
	sql_pr("update $config[tables_prefix]users set favourite_videos_count=(select count(*) from $config[tables_prefix]fav_videos where user_id=$config[tables_prefix]users.user_id) where user_id=$user_id");

	$temp_summary=array();
	$_SESSION['favourite_videos_summary']=mr2array(sql("select $config[tables_prefix]fav_videos.fav_type, count(*) as amount from $config[tables_prefix]fav_videos inner join $config[tables_prefix]videos on $config[tables_prefix]fav_videos.video_id=$config[tables_prefix]videos.video_id where $database_selectors[where_videos] and $config[tables_prefix]fav_videos.user_id=$user_id group by $config[tables_prefix]fav_videos.fav_type order by $config[tables_prefix]fav_videos.fav_type desc"));
	$_SESSION['favourite_videos_amount']=0;
	foreach ($_SESSION['favourite_videos_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_videos_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_videos_summary']=$temp_summary;

	if ($fav_type==10)
	{
		sql_pr("update $config[tables_prefix]playlists set total_videos=(select count(*) from $config[tables_prefix]fav_videos where $config[tables_prefix]playlists.playlist_id=$config[tables_prefix]fav_videos.playlist_id) where user_id=$user_id");
		$_SESSION['playlists']=mr2array(sql("select $database_selectors[playlists] from $config[tables_prefix]playlists where user_id=$user_id order by title asc"));
		$_SESSION['playlists_amount']=count($_SESSION['playlists']);
	}
}

function fav_albums_changed($album_ids_str)
{
	global $config,$database_selectors;

	$user_id=intval($_SESSION['user_id']);
	if ($user_id==0)
	{
		return;
	}

	require_once("$config[project_path]/admin/include/database_selectors.php");
	$albums_changed=mr2array(sql("select album_id, $database_selectors[generic_selector_dir] as dir from $config[tables_prefix]albums where album_id in ($album_ids_str)"));
	foreach ($albums_changed as $album)
	{
		inc_block_version('albums_info','album',$album['album_id'],$album['dir'],$user_id);
	}

	sql_pr("update $config[tables_prefix]albums a inner join (select album_id, count(*) as cnt from $config[tables_prefix]fav_albums where album_id in ($album_ids_str) group by album_id) x on a.album_id=x.album_id set a.favourites_count=x.cnt");
	sql_pr("update $config[tables_prefix]users set favourite_albums_count=(select count(*) from $config[tables_prefix]fav_albums where user_id=$config[tables_prefix]users.user_id) where user_id=$user_id");

	$temp_summary=array();
	$_SESSION['favourite_albums_summary']=mr2array(sql("select $config[tables_prefix]fav_albums.fav_type, count(*) as amount from $config[tables_prefix]fav_albums inner join $config[tables_prefix]albums on $config[tables_prefix]fav_albums.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] and $config[tables_prefix]fav_albums.user_id=$user_id group by $config[tables_prefix]fav_albums.fav_type order by $config[tables_prefix]fav_albums.fav_type desc"));
	$_SESSION['favourite_albums_amount']=0;
	foreach ($_SESSION['favourite_albums_summary'] as $summary_item)
	{
		$temp_summary[$summary_item['fav_type']]=$summary_item;
		$_SESSION['favourite_albums_amount']+=$summary_item['amount'];
	}
	$_SESSION['favourite_albums_summary']=$temp_summary;
}

function friends_changed($user_ids)
{
	global $config;

	if (count($user_ids) > 0)
	{
		$user_ids_str = implode(',', array_map('intval', $user_ids));
		sql_pr("update $config[tables_prefix]users inner join (select user_id, sum(friends) as friends from (
				select user_id, 0 as friends from $config[tables_prefix]users where user_id in ($user_ids_str)
				union all
				select user_id, count(*) as friends from $config[tables_prefix]friends where (user_id in ($user_ids_str) and is_approved=1) group by user_id
				union all
				select friend_id as user_id, count(*) as friends from $config[tables_prefix]friends where (friend_id in ($user_ids_str) and is_approved=1) group by friend_id
			) x group by user_id) y on $config[tables_prefix]users.user_id=y.user_id set $config[tables_prefix]users.friends_count=y.friends"
		);

		if ($_SESSION['user_id'] > 0)
		{
			$_SESSION['user_info']['friends_count'] = mr2number(sql_pr("select friends_count from $config[tables_prefix]users where user_id=?", $_SESSION['user_id']));
		}
	}
}

function messages_changed()
{
	global $config;

	if ($_SESSION['user_id'] > 0)
	{
		$_SESSION['unread_messages'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and is_read=0 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
		$_SESSION['unread_invites'] = mr2number(sql_pr("select count(*) from $config[tables_prefix]messages where user_id=? and is_hidden_from_user_id=0 and type_id=1 and user_from_id not in (select ignored_user_id from $config[tables_prefix]users_ignores where user_id=?)", $_SESSION['user_id'], $_SESSION['user_id']));
		$_SESSION['unread_non_invites'] = $_SESSION['unread_messages'] - $_SESSION['unread_invites'];
	}
}

function process_antispam_rules($object_type_id, $check_text = '')
{
	global $config;

	if (intval(@$_SESSION['is_trusted'])>0)
	{
		return '';
	}

	$antispam_options_prefix = '';
	$count_query_user = '';
	$count_query_all = '';
	$duplicate_query = '';
	switch ($object_type_id)
	{
		case 1:
			$antispam_options_prefix = 'ANTISPAM_VIDEOS';
			$count_query_user = "select count(*) from $config[tables_prefix]videos where added_date>? and (user_id=? or ip=?)";
			$count_query_all = "select count(*) from $config[tables_prefix]videos where added_date>?";
			break;
		case 2:
			$antispam_options_prefix = 'ANTISPAM_ALBUMS';
			$count_query_user = "select count(*) from $config[tables_prefix]albums where added_date>? and (user_id=? or ip=?)";
			$count_query_all = "select count(*) from $config[tables_prefix]albums where added_date>?";
			break;
		case 12:
			$antispam_options_prefix = 'ANTISPAM_POSTS';
			$count_query_user = "select count(*) from $config[tables_prefix]posts where added_date>? and (user_id=? or ip=?)";
			$count_query_all = "select count(*) from $config[tables_prefix]posts where added_date>?";
			break;
		case 13:
			$antispam_options_prefix = 'ANTISPAM_PLAYLISTS';
			$count_query_user = "select count(*) from $config[tables_prefix]playlists where added_date>? and user_id=?";
			$count_query_all = "select count(*) from $config[tables_prefix]playlists where added_date>?";
			break;
		case 5:
			$antispam_options_prefix = 'ANTISPAM_DVDS';
			$count_query_user = "select count(*) from $config[tables_prefix]dvds where added_date>? and user_id=?";
			$count_query_all = "select count(*) from $config[tables_prefix]dvds where added_date>?";
			break;
		case 15:
			$antispam_options_prefix = 'ANTISPAM_COMMENTS';
			$count_query_user = "select count(*) from $config[tables_prefix]comments where added_date>? and (user_id=? or ip=?)";
			$count_query_all = "select count(*) from $config[tables_prefix]comments where added_date>?";
			$duplicate_query = "select count(*) from $config[tables_prefix]comments where comment_md5=md5(?)";
			break;
		case 21:
			$antispam_options_prefix = 'ANTISPAM_MESSAGES';
			$count_query_user = "select count(*) from $config[tables_prefix]messages where added_date>? and (user_id=? or ip=?)";
			$count_query_all = "select count(*) from $config[tables_prefix]messages where added_date>?";
			$duplicate_query = "select count(*) from $config[tables_prefix]messages where message_md5=md5(?)";
			break;
	}

	$result = '';
	if ($antispam_options_prefix)
	{
		$antispam_options = get_options(["{$antispam_options_prefix}_FORCE_CAPTCHA", "{$antispam_options_prefix}_FORCE_DISABLED", "{$antispam_options_prefix}_AUTODELETE", "{$antispam_options_prefix}_ERROR", "{$antispam_options_prefix}_DUPLICATES", "{$antispam_options_prefix}_ANALYZE_HISTORY", 'ANTISPAM_BLACKLIST_WORDS', 'ANTISPAM_BLACKLIST_ACTION']);
		if ($antispam_options['ANTISPAM_BLACKLIST_WORDS'] && $check_text)
		{
			$antispam_options['ANTISPAM_BLACKLIST_WORDS'] = str_replace(array("\n", "\r"), ',', $antispam_options['ANTISPAM_BLACKLIST_WORDS']);
			$antispam_source = array_map('trim', explode(',', $antispam_options['ANTISPAM_BLACKLIST_WORDS']));
			foreach ($antispam_source as $word)
			{
				if ($word != '')
				{
					if (preg_match('|[\|/\\\\&%$]|', substr($word, 0, 1)))
					{
						$regexp = $word;
					} elseif (strpos($word, '*') !== false)
					{
						$regexp = '|' . str_replace('\*', '\w*', preg_quote($word, "/")) . '|iu';
					} else
					{
						$regexp = '|\b' . preg_quote($word, "/") . '\b|iu';
					}
					if (preg_match($regexp, $check_text))
					{
						switch (intval($antispam_options['ANTISPAM_BLACKLIST_ACTION']))
						{
							case 0:
								$result .= 'delete,';
								break;
							case 1:
								if ($object_type_id == 21)
								{
									$result .= 'delete,';
								} else
								{
									$result .= 'deactivate,';
								}
								break;
						}
					}
				}
			}
		}

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]users_blocked_ips where ip=? or ip=? or ip=?", nvl($_SERVER['REMOTE_ADDR']), nvl(int2ip(ip2int($_SERVER['REMOTE_ADDR']))), ip2mask(int2ip(ip2int($_SERVER['REMOTE_ADDR']))))) > 0)
		{
			switch (intval($antispam_options['ANTISPAM_BLACKLIST_ACTION']))
			{
				case 0:
					$result .= 'delete,';
					break;
				case 1:
					if ($object_type_id == 21)
					{
						$result .= 'delete,';
					} else
					{
						$result .= 'deactivate,';
					}
					break;
			}
		}

		$count_query = $count_query_all;
		if ($antispam_options["{$antispam_options_prefix}_ANALYZE_HISTORY"] == 1)
		{
			$count_query = $count_query_user;
		}

		unset($antispam_options['ANTISPAM_BLACKLIST_WORDS'], $antispam_options['ANTISPAM_BLACKLIST_ACTION'], $antispam_options["{$antispam_options_prefix}_ANALYZE_HISTORY"]);
		foreach ($antispam_options as $key => $value)
		{
			$value_array = array_map('intval', explode('/', $value));
			if ($value_array[0] > 0)
			{
				if (strpos($key, '_DUPLICATES') !== false)
				{
					if ($duplicate_query && $check_text)
					{
						if (mr2number(sql_pr($duplicate_query, $check_text)) > 0)
						{
							$result .= 'delete,';
						}
					}
				} elseif ($value_array[1] > 0)
				{
					$check_date = date("Y-m-d H:i:s", time() - $value_array[1]);
					if (mr2number(sql_pr($count_query, $check_date, intval(@$_SESSION['user_id']), ip2int($_SERVER['REMOTE_ADDR']))) >= $value_array[0])
					{
						switch ($key)
						{
							case "{$antispam_options_prefix}_FORCE_CAPTCHA":
								$result .= 'captcha,';
								break;
							case "{$antispam_options_prefix}_FORCE_DISABLED":
								$result .= 'deactivate,';
								break;
							case "{$antispam_options_prefix}_AUTODELETE":
								$result .= 'delete,';
								break;
							case "{$antispam_options_prefix}_ERROR":
								$result .= 'error,';
								break;
						}
					}
				}
			}
		}
	}
	if (strpos($result, 'delete') !== false)
	{
		sleep(mt_rand(0, 10));
	} elseif (strpos($result, 'deactivate') !== false)
	{
		sleep(mt_rand(0, 5));
	}
	return $result;
}

function get_site_pagination($object_id,$total_num,$num_on_page,$from,$str,$count,$var_from,$se_friendly)
{
	global $config,$storage,$runtime_params,$page_id;

	if (intval($count)==0)
	{
		$count=10;
	}
	$res['var_from']=$var_from;

	$page_prefix="";
	$page_postfix="";
	$url_prefix=$str;
	if ($str=='' && $se_friendly==1)
	{
		$temp=explode("?",$_SERVER['REQUEST_URI'],2);
		$last_word=end(explode("/",$temp[0]));
		if (strpos($last_word,".")>0)
		{
			$ext=substr($temp[0],strpos($temp[0],".")+1);
			$str=substr($temp[0],0,strpos($temp[0],"."));
			$str=preg_replace("|-\d+$|is","",$str);
			$page_prefix="-";
			$page_postfix=".$ext";
		} else
		{
			if ($from>0 || ($from==0 && intval($_REQUEST[$var_from]))==1)
			{
				$str=preg_replace("|/\d+/$|is","/",$temp[0]);
			} else {
				$str=$temp[0];
			}
			$str=rtrim($str,"/");
			$page_prefix="/";
			$page_postfix="/";
			if ($config['is_pagination_5.0']=='true' && substr($temp[0],-1,1)!='/')
			{
				if ($from>0 || ($from==0 && intval($_REQUEST[$var_from]))==1)
				{
					$str=preg_replace("|/\d+$|is","",$temp[0]);
				} else {
					$str=$temp[0];
				}
				$page_postfix="";
			}
		}
	} elseif ($str<>'' && $se_friendly==1)
	{
		if (strpos($_SERVER['REQUEST_URI'],$str)===false)
		{
			$temp=explode("?",$str,2);
			$last_word=end(explode("/",$temp[0]));
			if (strpos($last_word,".")>0)
			{
				$ext=substr($temp[0],strpos($temp[0],".")+1);
				$str=substr($temp[0],0,strpos($temp[0],"."));
				$page_prefix="-";
				$page_postfix=".$ext";
			} else
			{
				$str=rtrim($str,"/");
				$page_prefix="/";
				$page_postfix="/";
				if ($config['is_pagination_5.0']=='true' && substr($temp[0],-1,1)!='/')
				{
					$page_postfix="";
				}
			}
		} else {
			$temp=explode("?",$_SERVER['REQUEST_URI'],2);
			$last_word=end(explode("/",$temp[0]));
			if (strpos($last_word,".")>0)
			{
				$ext=substr($temp[0],strpos($temp[0],".")+1);
				$str=substr($temp[0],0,strpos($temp[0],"."));
				$str=preg_replace("|-\d+$|is","",$str);
				$page_prefix="-";
				$page_postfix=".$ext";
			} else
			{
				$str=preg_replace("|/\d+/$|is","/",$temp[0]);
				$str=rtrim($str,"/");
				$page_prefix="/";
				$page_postfix="/";
			}
		}
	} elseif ($str<>'' && $se_friendly==0)
	{
		$str=rtrim($str,"?")."?";
	}

	$str_add='';
	if ($config['is_pagination_4.0']=='true')
	{
		$temp=explode("||",@file_get_contents("$config[project_path]/admin/data/config/$page_id/$object_id.dat"));
		if (strpos($object_id, 'pagination') === 0 && trim($temp[1])<>'')
		{
			$temp_params=explode("&",$temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param=explode("=",$temp_param,2);
				if ($temp_param[0] == 'related_block_ext_id')
				{
					$target_block_id_dir=str_replace("|","_",strtolower(str_replace(" ","_",$temp_param[1])));
					$temp=explode("||",@file_get_contents("$config[project_path]/admin/data/config/$page_id/$target_block_id_dir.dat"));
					break;
				}
			}
		}
		if (trim($temp[1])<>'')
		{
			$request_uri_params=explode("&",end(explode("?",$_SERVER['REQUEST_URI'],2)));

			$temp_params=explode("&",$temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param=explode("=",$temp_param,2);
				if (strpos($temp_param[0],"var_")===0 && $temp_param[0]!='var_from')
				{
					foreach ($request_uri_params as $v)
					{
						$v=explode("=",$v,2);
						if ($v[0]==$temp_param[1])
						{
							$str_add.="$v[0]=$v[1]&";
						}
					}
				}
			}
			if (is_array($config['is_pagination_4.0_whitelist']))
			{
				foreach ($config['is_pagination_4.0_whitelist'] as $temp_param)
				{
					foreach ($request_uri_params as $v)
					{
						$v=explode("=",$v,2);
						if ($v[0]==$temp_param)
						{
							$str_add.="$v[0]=$v[1]&";
						}
					}
				}
			}
		}
	} else {
		$temp=explode("&",end(explode("?",$_SERVER['REQUEST_URI'],2)));
		foreach ($temp as $v)
		{
			$v=explode("=",$v,2);
			$var=$v[0];
			$val=$v[1];
			if (is_array($runtime_params))
			{
				foreach ($runtime_params as $param)
				{
					if ($var==trim($param['name']))
					{
						continue 2;
					}
				}
			}
			if ($var<>$var_from && $var<>'' && $val<>'')
			{
				$str_add.="$var=$val&";
			}
		}
	}

	$str=trim($str,"?");
	if ($se_friendly==1)
	{
		if ($str_add<>'')
		{
			$str_add="?".trim(rtrim($str_add,"&"));
		}
	} else {
		if (strpos($str,"?")!==false)
		{
			$str.="&".$str_add;
		} else {
			$str.="?".$str_add;
		}
	}

	$count--;
	if ($total_num>$num_on_page)
	{
		$page_left=ceil($from/$num_on_page);
		$page_right=floor(($total_num-$from-1)/$num_on_page);

		$page_left_real=$page_left;
		$page_right_real=$page_right;
		$page_left_min=0;
		$page_right_min=0;

		if ($page_left>$count/2) {$page_left_min=$page_left-ceil($count/2);$page_left=ceil($count/2);}
		if ($page_right>$count/2) {$page_right_min=$page_right-ceil($count/2);$page_right=ceil($count/2);}
		if ($page_left<$count/2) {$page_right=$page_right+$page_right_min;
			if (($page_right+$page_left)>$count) {$page_right=$count-$page_left;}}
		if ($page_right<$count/2) {$page_left=$page_left+$page_left_min;
			if (($page_left+$page_right)>$count) {$page_left=$count-$page_right;}}
		$page_start=$from/$num_on_page-$page_left;settype($page_start,"integer");
		$page_start++;

		$from_first=0;
		$from_last=floor(($total_num-1)/$num_on_page)*$num_on_page;
		$from_current=$from;
		if ($config['is_pagination_2.0']=='true')
		{
			$from_first=1;
			$from_last=ceil($total_num/$num_on_page);
			$from_current=floor($from/$num_on_page)+1;
		}

		if ($page_left_real>$page_left)
		{
			$url=($page_start-1)*$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$page_start;
			}
			$res['page_str_left_jump_number']="$url";
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['page_str_left_jump']="$str$url$str_add";
			} else {
				$res['page_str_left_jump']="{$str}$var_from=$url";
			}
			$page_start+=1;$page_left--;
		}

		$i1=0;
		for ($i=0;$i<$page_left;$i++)
		{
			$url=($page_start-1)*$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$page_start;
			}
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['page_str'][$i1]="$str$url$str_add";
			} else {
				$res['page_str'][$i1]="{$str}$var_from=$url";
			}
			$res['page_num'][$i1]="$page_start";

			$page_start++;
			$i1++;
		}

		$res['page_str'][$i1]="";
		$res['page_num'][$i1]="$page_start";
		$page_start++;

		if ($page_right_real>$page_right)
		{
			$page_right--;
		}

		for ($i=0;$i<$page_right;$i++)
		{
			$i1++;
			$url=($page_start-1)*$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$page_start;
			}
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['page_str'][$i1]="$str$url$str_add";
			} else {
				$res['page_str'][$i1]="{$str}$var_from=$url";
			}
			$res['page_num'][$i1]="$page_start";
			$page_start++;
		}

		if ($page_right_real>$page_right)
		{
			$url=($page_start-1)*$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$page_start;
			}
			if ($url>$from_last) {$url=$from_last;}
			$res['page_str_right_jump_number']="$url";
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['page_str_right_jump']="$str$url$str_add";
			} else {
				$res['page_str_right_jump']="{$str}$var_from=$url";
			}
		}

		if ($page_right>0)
		{
			$url=$from_current+$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$from_current+1;
			}
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['next']="$str$url$str_add";
			} else {
				$res['next']="{$str}$var_from=$url";
			}
		}
		if ($page_left>0)
		{
			$url=$from_current-$num_on_page;
			if ($config['is_pagination_2.0']=='true')
			{
				$url=$from_current-1;
			}
			if ($se_friendly==1)
			{
				if ($config['is_pagination_2.0']=='true')
				{
					if ($url>1) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				} else {
					if ($url>0) {$url="$page_prefix$url$page_postfix";} else {$url="$page_postfix";}
				}
				$res['previous']="$str$url$str_add";
			} else {
				$res['previous']="{$str}$var_from=$url";
			}
		}
		if (count($res['page_str'])>1)
		{
			if ($se_friendly==1)
			{
				$res['first']="{$str}$page_postfix$str_add";
				if ($config['is_pagination_2.0']=='true')
				{
					if ($from_last>1) {$url_last="$page_prefix$from_last$page_postfix";} else {$url_last="$page_postfix";}
				} else {
					if ($from_last>0) {$url_last="$page_prefix$from_last$page_postfix";} else {$url_last="$page_postfix";}
				}
				$res['last']="$str$url_last$str_add";
			} else {
				$res['first']="{$str}$var_from=$from_first";
				$res['last']="{$str}$var_from=$from_last";
			}
			if ($from==0) {$res['is_first']=1;}
			$res['last_from']=ceil($from_last/$num_on_page+1);
			if ($from==$from_last) {$res['is_last']=1;}

			if ($config['is_pagination_2.0']=='true')
			{
				if ($from==1) {$res['is_first']=1;}
				$res['last_from']=intval($from_last);
				if ($from>=($from_last-1)*$num_on_page)
				{
					$res['is_last']=1;
				} else {
					unset($res['is_last']);
				}
			}

			$res['show']=1;
		}
		$res['from_now']=$from;

		for ($i=0;$i<count($res['page_num']);$i++)
		{
			if ($url_prefix<>'' && ltrim($url_prefix,'/')==$url_prefix)
			{
				if ($res['page_num'][$i]==1)
				{
					$temp=explode("?",$res['page_str'][$i],2);
					if (strpos($temp[0],$url_prefix)===strlen($temp[0])-strlen($url_prefix))
					{
						if ($temp[1]<>'')
						{
							$res['page_str'][$i]=str_replace($url_prefix,'',$temp[0]).'?'.$temp[1];
						} else {
							$res['page_str'][$i]=str_replace($url_prefix,'',$temp[0]);
						}
					}
				}
			}
			if (strlen($res['page_num'][$i])==1) {$res['page_num'][$i]="0".$res['page_num'][$i];}
		}
		if (strlen($res['last_from'])==1) {$res['last_from']="0".$res['last_from'];}

		if ($url_prefix<>'' && ltrim($url_prefix,'/')==$url_prefix)
		{
			$temp=explode("?",$res['first'],2);
			if (strpos($temp[0],$url_prefix)===strlen($temp[0])-strlen($url_prefix))
			{
				if ($temp[1]<>'')
				{
					$res['first']=str_replace($url_prefix,'',$temp[0]).'?'.$temp[1];
				} else {
					$res['first']=str_replace($url_prefix,'',$temp[0]);
				}
			}
			$temp=explode("?",$res['previous'],2);
			if (strpos($temp[0],$url_prefix)===strlen($temp[0])-strlen($url_prefix))
			{
				if ($temp[1]<>'')
				{
					$res['previous']=str_replace($url_prefix,'',$temp[0]).'?'.$temp[1];
				} else {
					$res['previous']=str_replace($url_prefix,'',$temp[0]);
				}
			}
		}
	}

	if ($num_on_page>0)
	{
		if ($from>0)
		{
			$res['page_now']=floor($from/$num_on_page)+1;
		} else {
			$res['page_now']=1;
		}
		$res['page_total']=ceil($total_num/$num_on_page);
	} else {
		$res['page_now']=1;
		$res['page_total']=1;
	}
	$storage[$object_id]['page_now']=$res['page_now'];
	$storage[$object_id]['page_total']=$res['page_total'];
	if ($res['next']!='')
	{
		$storage[$object_id]['page_next']=$res['next'];
	}
	if ($res['previous']!='')
	{
		$storage[$object_id]['page_prev']=$res['previous'];
	}

	return $res;
}
