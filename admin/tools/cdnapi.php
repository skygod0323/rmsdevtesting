<?php

// Note on this API.
// This file when implementation is provided should be placed under /admin/cdn folder.
// All functions in this file should be prefixed with filename.
// For example if filename is ucdn.php, functions should be renamed to ucdn_get_file() and etc.

/**
 * Tests if API works correctly.
 *
 * @param $streaming_key   [String]   protection key entered in storage server configuration, example: abcdefg
 * @return null on success, error message / code on failure
 */
function cdnapi_test($streaming_key)
{
	// return success by default
	return null;
}

/**
 * Returns video file URL with all parameters configured.
 *
 * @param $file_path       [String]    relative file path, example: /contents/videos/2000/2122/2122.flv
 * @param $file_url        [String]    full file URL, example: http://cdn.domain.com/contents/videos/2000/2122/2122.flv
 * @param $start_str       [String]    video streaming offset value with parameter name, can be null, example: start=123
 * @param $limit           [Integer]   download speed limit in kilobits per second (0 means no limit), example: 100
 * @param $streaming_key   [String]    protection key entered in storage server configuration, example: abcdefg
 * @return String video file URL
 */
function cdnapi_get_video($file_path,$file_url,$start_str,$limit,$streaming_key)
{
	// example is to return URL with only start parameter
	if ($start_str<>'')
	{
		return "$file_url?$start_str";
	}
	return $file_url;
}

/**
 * Returns image / zip file URL with all parameters configured.
 *
 * @param $file_path       [String]   relative file path, example: /contents/albums/main/800x600/2000/2122/10982.jpg
 * @param $file_url        [String]   full file URL, example: http://cdn.domain.com/contents/albums/main/800x600/2000/2122/10982.jpg
 * @param $streaming_key   [String]   protection key entered in storage server configuration, example: abcdefg
 * @return String image / zip file URL
 */
function cdnapi_get_image($file_path,$file_url,$streaming_key)
{
	// example is to return URL without any parameters
	return $file_url;
}

/**
 * Invalidates the given folders or files on CDN.
 * Files are always provided as exact list of all affected files. Folders are provided in some cases where all files
 * in specific folders should be invalidated.
 *
 * Folders and files contain paths relative to the given $server_url.
 *
 * @param $server_url   [String]           CDN URL configured in storage server, example: http://cdn.domain.com/contents/videos
 * @param $folders      [Array of String]  list of related folder paths, which should be invalidated, can be null, example: array("2000/2122", "2000/2123")
 * @param $files        [Array of String]  list of related file paths, which should be invalidated, example: array("2000/2122/2122.flv", "2000/2122/2122.mp4", "2000/2123/2123.flv", "2000/2123/2123.mp4")
 * @param $operation    [String]           indicates operation on the above files, one if the following: "add", "change", "delete", "multiple"
 *                                         (multiple means that KVS cannot exactly tell operation, part of files may be updated and part deleted and etc.)
 */
function cdnapi_invalidate_resources($server_url,$folders,$files,$operation)
{
	// code invalidation logic here
}