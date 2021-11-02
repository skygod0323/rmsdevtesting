<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function external_searchInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/external_search";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
	if (!is_file("$plugin_path/data.dat"))
	{
		$data=array();
		$data['enable_external_search_condition']=20;

		file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);
	}
}

function external_searchIsEnabled()
{
	external_searchInit();
	$external_search_options=external_searchGetOptions();
	return ($external_search_options['enable_external_search']>0);
}

function external_searchShow()
{
	global $config,$lang,$errors,$page_name;

	external_searchInit();
	$plugin_path="$config[project_path]/admin/data/plugins/external_search";

	$errors = null;

	if ($_POST['action']=='change_complete')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
		}
		if (intval($_POST['enable_external_search'])==2)
		{
			validate_field('empty_int',$_POST['enable_external_search_condition'],$lang['plugins']['external_search']['field_enable_external_search']);
		}
		if (intval($_POST['enable_external_search'])>0)
		{
			if (validate_field('empty',$_POST['api_call'],$lang['plugins']['external_search']['field_api_call']))
			{
				if (strpos($_POST['api_call'], '%QUERY%')===false)
				{
					$errors[]=get_aa_error('token_required',$lang['plugins']['external_search']['field_api_call'],'%QUERY%');
				} else {
					$query_url=str_replace('%LIMIT%',1,str_replace('%FROM%',0,str_replace('%QUERY%','video',$_POST['api_call'])));
					if (!external_searchTest($query_url))
					{
						$errors[]=str_replace("%1%",$lang['plugins']['external_search']['field_api_call'],$lang['plugins']['external_search']['validation_error_api_invalid']);
					}
				}
			}
			validate_field('url',$_POST['outgoing_url'],$lang['plugins']['external_search']['field_outgoing_url']);
		}

		if (!is_array($errors))
		{
			$save_data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
			$save_data['enable_external_search']=intval($_POST['enable_external_search']);
			$save_data['display_results']=intval($_POST['display_results']);
			if (intval($_POST['enable_external_search_condition'])>0)
			{
				$save_data['enable_external_search_condition']=intval($_POST['enable_external_search_condition']);
			}
			$save_data['api_call']=$_POST['api_call'];
			$save_data['outgoing_url']=$_POST['outgoing_url'];

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			return_ajax_success("$page_name?plugin_id=external_search");
		} else {
			return_ajax_errors($errors);
		}
	}

	$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	$_POST['performance']=@unserialize(@file_get_contents("$plugin_path/performance.dat"));

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat")));
	}
}

function external_searchGetOptions()
{
	global $config;

	$plugin_path="$config[project_path]/admin/data/plugins/external_search";

	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return null;
	}
	$result=array();
	$result['enable_external_search']=intval($data['enable_external_search']);
	$result['enable_external_search_condition']=intval($data['enable_external_search_condition']);
	$result['display_results']=intval($data['display_results']);
	return $result;
}

function external_searchDoSearch($search_string,$from,$limit)
{
	global $config;

	$plugin_path="$config[project_path]/admin/data/plugins/external_search";

	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	if (!is_array($data))
	{
		return array('total_count'=>0,'from'=>0,'data'=>array());
	}

	$query_url=str_replace('%LIMIT%',intval($limit),str_replace('%FROM%',intval($from),str_replace('%QUERY%',urlencode($search_string),$data['api_call'])));
	$click_url=str_replace('%LIMIT%',intval($limit),str_replace('%FROM%',intval($from),str_replace('%QUERY%',urlencode($search_string),$data['outgoing_url'])));
	if ($data['enable_external_search']>0)
	{
		foreach ($_GET as $k=>$v)
		{
			if (strpos($query_url,"&$k=")===false)
			{
				$query_url.="&$k=$v";
			}
		}
		return external_searchParse($query_url,$click_url);
	}
	return array('total_count'=>0,'from'=>0,'data'=>array());
}

function external_searchTest($url)
{
	$search_result=get_page('',$url,'','',1,0,30,'');
	if (strpos($search_result,'<search_feed')!==false || strpos($search_result,'<feed')!==false)
	{
		return true;
	}
	return false;
}

function external_searchParse($url,$click_url)
{
	global $config,$website_ui_data,$database_selectors;

	$plugin_path="$config[project_path]/admin/data/plugins/external_search";

	$start_time=microtime(true);

	$search_result=get_page('',$url,'','',1,0,30,'');
	if (strlen($search_result)==0)
	{
		return array('total_count'=>0,'from'=>0,'data'=>array());
	}
	$query_time=microtime(true)-$start_time;

	preg_match_all("|<gallery>(.*?)</gallery>|is",$search_result,$temp);
	$items=$temp[1];

	$result=array();
	$kvs_video_ids=array();
	foreach ($items as $item)
	{
		$video_record=array();
		$video_record['is_external']=1;
		$video_record['view_page_url']=$click_url;

		preg_match("|<gallery_hum_date>(.*?)</gallery_hum_date>|is",$item,$temp);
		$video_record['post_date']=external_searchParseTag($temp[1]);

		preg_match("|<gallery_tube_duration>(.*?)</gallery_tube_duration>|is",$item,$temp);
		$video_record['duration']=intval(external_searchParseTag($temp[1]));

		preg_match("|<name>(.*?)</name>|is",$item,$temp);
		$video_record['title']=trim(external_searchParseTag($temp[1]));

		preg_match("|<thumb>(.*?)</thumb>|is",$item,$temp);
		if ($temp[1]<>'')
		{
			$video_record['external_screenshot']=trim(external_searchParseTag($temp[1]));
		}

		preg_match("|<rating>(.*?)</rating>|is",$item,$temp);
		if ($temp[1]<>'')
		{
			$video_record['rating']=floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<popularity>(.*?)</popularity>|is",$item,$temp);
		if ($temp[1]<>'')
		{
			$video_record['video_viewed']=floatval(trim(external_searchParseTag($temp[1])));
		}

		preg_match("|<kvs_data>(.*?)</kvs_data>|is",$item,$temp);
		if ($temp[1]<>'')
		{
			$temp_data=explode(',',trim(external_searchParseTag($temp[1])));

			$video_id=$temp_data[0];
			$video_record['video_id']=$video_id;
			$kvs_video_ids[]=$video_id;
		}

		$result[]=$video_record;
	}

	if (count($kvs_video_ids)>0)
	{
		$kvs_video_ids=implode(',',$kvs_video_ids);
		$local_videos=mr2array(sql("select $database_selectors[videos] from $config[tables_prefix]videos where video_id in ($kvs_video_ids)"));
		foreach ($local_videos as $video)
		{
			$local_videos_temp[$video['video_id']]=$video;
		}
		foreach ($result as $k=>$video)
		{
			$video_id=$video['video_id'];
			$dir_path=get_dir_by_id($video_id);

			if (isset($local_videos_temp[$video_id]))
			{
				$result[$k]=$local_videos_temp[$video_id];
			}

			$result[$k]['formats']=get_video_formats($result[$k]['video_id'],$result[$k]['file_formats']);
			$result[$k]['dir_path']=$dir_path;

			$screen_url_base=load_balance_screenshots_url();
			$result[$k]['screen_url']="$screen_url_base/$dir_path/$video_id";
			$result[$k]['poster_url']="$screen_url_base/$dir_path/$video_id/posters";

			$pattern=str_replace("%ID%",$video_id,str_replace("%DIR%",$result[$k]['dir'],$website_ui_data['WEBSITE_LINK_PATTERN']));
			$result[$k]['view_page_url']="$config[project_url]/$pattern";

			if (isset($result[$k]['status_id']) && $result[$k]['status_id']!=1)
			{
				$result[$k]['status_id']=1;
			}
		}
	}

	preg_match("|<search_feed total_count=\"(.*?)\" from=\"(.*?)\">|is",$search_result,$temp);
	$total_count=intval(trim($temp[1]));
	$from=intval(trim($temp[2]));
	if ($total_count==0)
	{
		$total_count=count($result);
	}

	$parse_time=microtime(true)-$start_time-$query_time;

	$fp=fopen("$plugin_path/performance.dat","a+");
	flock($fp,LOCK_EX);

	$performance_data=@unserialize(@file_get_contents("$plugin_path/performance.dat"));
	if (!is_array($performance_data))
	{
		$performance_data=array();
		$performance_data['query_time']=$query_time;
		$performance_data['parse_time']=$parse_time;
	}
	$performance_data['query_time']=number_format(($performance_data['query_time']*10+$query_time)/11,4);
	$performance_data['parse_time']=number_format(($performance_data['parse_time']*10+$parse_time)/11,4);

	ftruncate($fp,0);
	fwrite($fp,serialize($performance_data));
	flock($fp,LOCK_UN);
	fclose($fp);

	return array('total_count'=>$total_count,'from'=>$from,'data'=>$result);
}

function external_searchParseTag($value)
{
	if (strpos($value,"<![CDATA[")!==false)
	{
		$value=str_replace("<![CDATA[","",$value);
		$value=str_replace("]]>","",$value);
	}
	$value=str_replace("&lt;","<",$value);
	$value=str_replace("&gt;",">",$value);
	$value=str_replace("&amp;","&",$value);
	$value=strip_tags($value);
	return $value;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
