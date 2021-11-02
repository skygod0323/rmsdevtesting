<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function rss_parse_feed($url, $feed_config)
{
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<item>(.*?)</item>|is", $feed_contents, $temp);

	$result = [];
	if (is_array($temp[1]))
	{
		foreach ($temp[1] as $item)
		{
			$result[] = rss_parse_item($item);
		}
	}

	return $result;
}

function rss_check_feed_content($url, $feed_config)
{
	if (strpos($url, '?') === false)
	{
		$url .= '?kvs_test_feed=true';
	} else
	{
		$url .= '&kvs_test_feed=true';
	}
	$feed_contents = get_page('', $url, '', '', 1, 0, 600, '');
	if (!$feed_contents)
	{
		return null;
	}

	if ($feed_config['feed_charset'] && function_exists('iconv'))
	{
		$feed_contents = iconv($feed_config['feed_charset'], "utf8", $feed_contents);
	}

	preg_match_all("|<item>(.*?)</item>|is", $feed_contents, $temp);

	if (is_array($temp[1]) && count($temp[1]) > 0)
	{
		return rss_parse_item($temp[1][0]);
	}

	return null;
}

function rss_parse_item($item)
{
	$video_record = [];

	preg_match("|<title>(.*?)</title>|is", $item, $temp);
	$video_record['title'] = trim(rss_parse_feed_tag($temp[1]));

	preg_match("|<link>(.*?)</link>|is", $item, $temp);
	$video_record['website_link'] = trim(rss_parse_feed_tag($temp[1]));

	preg_match("|<pubDate>(.*?)</pubDate>|is", $item, $temp);
	$video_record['post_date'] = trim(rss_parse_feed_tag($temp[1]));

	if ($video_record['post_date'] == '')
	{
		$video_record['post_date'] = date("Y-m-d H:i:s");
	}

	$video_record['external_key'] = $video_record['website_link'];

	return $video_record;
}

function rss_parse_feed_tag($value)
{
	if (strpos($value, "<![CDATA[") !== false)
	{
		$value = str_replace(array("<![CDATA[", "]]>"), "", $value);
	}
	$value = str_replace(array("&lt;", "&gt;", "&amp;"), array("<", ">", "&"), $value);

	return $value;
}
