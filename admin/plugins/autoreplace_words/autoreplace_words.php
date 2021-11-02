<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function autoreplace_wordsInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/autoreplace_words";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function autoreplace_wordsIsEnabled()
{
	global $config;

	autoreplace_wordsInit();
	$plugin_path="$config[project_path]/admin/data/plugins/autoreplace_words";
	if (is_file("$plugin_path/data.dat"))
	{
		$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
		if ($data['enabled']==1)
		{
			return true;
		}
	}
	return false;
}

function autoreplace_wordsShow()
{
	global $config,$lang,$errors,$page_name;

	autoreplace_wordsInit();
	$plugin_path="$config[project_path]/admin/data/plugins/autoreplace_words";

	$errors = null;

	if ($_POST['action']=='save')
	{
		foreach ($_POST as $post_field_name => $post_field_value)
		{
			if (!is_array($post_field_value))
			{
				$_POST[$post_field_name] = trim($post_field_value);
			}
		}

		if (validate_field('empty',$_POST['vocabulary'],$lang['plugins']['autoreplace_words']['divider_vocabulary']))
		{
			$rows=explode("\n",$_POST['vocabulary']);
			$existing_words=array();
			for ($i=0;$i<count($rows);$i++)
			{
				$row_items=explode(':',trim($rows[$i]),2);
				if (count($row_items)<2)
				{
					$row_items=explode(',',trim($rows[$i]));
					if (count($row_items)<2)
					{
						$errors[]=str_replace("%1%",$lang['plugins']['autoreplace_words']['divider_vocabulary'],str_replace("%2%",$i+1,$lang['plugins']['autoreplace_words']['error_row_format']));
					} else {
						foreach ($row_items as $row_item)
						{
							if (trim($row_item)<>'')
							{
								if (in_array(trim($row_item),$existing_words))
								{
									$errors[]=str_replace("%1%",$lang['plugins']['autoreplace_words']['divider_vocabulary'],str_replace("%2%",$i+1,str_replace("%3%",trim($row_item),$lang['plugins']['autoreplace_words']['error_word_duplicate'])));
								} else {
									$existing_words[]=trim($row_item);
								}
							}
						}
					}
				} else {
					if (trim($row_items[0])=='' || trim($row_items[1])=='')
					{
						$errors[]=str_replace("%1%",$lang['plugins']['autoreplace_words']['divider_vocabulary'],str_replace("%2%",$i+1,$lang['plugins']['autoreplace_words']['error_row_format']));
					} elseif (in_array(trim($row_items[0]),$existing_words)) {
						$errors[]=str_replace("%1%",$lang['plugins']['autoreplace_words']['divider_vocabulary'],str_replace("%2%",$i+1,str_replace("%3%",trim($row_items[0]),$lang['plugins']['autoreplace_words']['error_word_duplicate'])));
					} else {
						$existing_words[]=trim($row_items[0]);
					}
				}
			}
		}
		if (!is_array($errors))
		{
			$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
			$data['replace_videos_title']=intval($_POST['replace_videos_title']);
			$data['replace_videos_description']=intval($_POST['replace_videos_description']);
			$data['replace_albums_title']=intval($_POST['replace_albums_title']);
			$data['replace_albums_description']=intval($_POST['replace_albums_description']);
			$data['limit_feeds']=intval($_POST['limit_feeds']);
			$data['limit_grabbers']=intval($_POST['limit_grabbers']);
			$data['vocabulary']=$_POST['vocabulary'];

			if (intval($_POST['replace_videos_title'])+intval($_POST['replace_videos_description'])+intval($_POST['replace_albums_title'])+intval($_POST['replace_albums_description'])>0)
			{
				$data['enabled']=1;
			} else {
				$data['enabled']=0;
			}
			file_put_contents("$plugin_path/data.dat", serialize($data), LOCK_EX);

			if (!is_file("$plugin_path/data.dat"))
			{
				$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
			}

			if (!is_array($errors))
			{
				return_ajax_success("$page_name?plugin_id=autoreplace_words");
			} else {
				return_ajax_errors($errors);
			}
		} else {
			return_ajax_errors($errors);
		}
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		$_POST['enabled']=0;
		$_POST['replace_videos_title']=0;
		$_POST['replace_videos_description']=0;
		$_POST['replace_albums_title']=0;
		$_POST['replace_albums_description']=0;
		$_POST['limit_feeds']=0;
		$_POST['limit_grabbers']=0;
		$_POST['vocabulary']='';

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}
}

function autoreplace_wordsReplace($source,$vocabulary)
{
	$punkt=array(" ",".",",",":",";","-","+","=","'","\"","(",")","`","?","!");
	$result='';

	$current_word='';
	for ($i=0;$i<strlen($source);$i++)
	{
		if (!in_array($source[$i],$punkt))
		{
			$current_word.=$source[$i];
		} else {
			$test_word=$current_word;
			if (!isset($vocabulary[$test_word]))
			{
				if (function_exists('mb_convert_case'))
				{
					$test_word=mb_convert_case($current_word,MB_CASE_LOWER,"UTF-8");
				} else {
					$test_word=strtolower($current_word);
				}
			}
			if (!isset($vocabulary[$test_word]))
			{
				if (function_exists('mb_convert_case'))
				{
					$test_word=mb_convert_case($current_word,MB_CASE_UPPER,"UTF-8");
				} else {
					$test_word=strtoupper($current_word);
				}
			}
			if (!isset($vocabulary[$test_word]))
			{
				if (function_exists('mb_convert_case'))
				{
					$test_word=mb_convert_case($current_word,MB_CASE_TITLE,"UTF-8");
				} else {
					$test_word=ucfirst($current_word);
				}
			}
			if (isset($vocabulary[$test_word]) && count($vocabulary[$test_word])>0)
			{
				$old_word=$current_word;
				$current_word=$vocabulary[$test_word][mt_rand(0,count($vocabulary[$test_word])-1)];
				if ($old_word!=$test_word)
				{
					if (function_exists('mb_convert_case'))
					{
						if ($old_word==mb_convert_case($test_word,MB_CASE_LOWER,"UTF-8"))
						{
							$current_word=mb_convert_case($current_word,MB_CASE_LOWER,"UTF-8");
						} elseif ($old_word==mb_convert_case($test_word,MB_CASE_UPPER,"UTF-8"))
						{
							$current_word=mb_convert_case($current_word,MB_CASE_UPPER,"UTF-8");
						} elseif ($old_word==mb_convert_case($test_word,MB_CASE_TITLE,"UTF-8"))
						{
							$current_word=mb_convert_case($current_word,MB_CASE_TITLE,"UTF-8");
						}
					} else {
						if ($old_word==strtolower($test_word))
						{
							$current_word=strtolower($current_word);
						} elseif ($old_word==strtoupper($test_word))
						{
							$current_word=strtoupper($current_word);
						} elseif ($old_word==ucfirst($test_word))
						{
							$current_word=ucfirst($current_word);
						}
					}
				}
			}
			$result.=$current_word;
			$result.=$source[$i];
			$current_word='';
		}
	}
	if ($current_word<>'')
	{
		$test_word=$current_word;
		if (!isset($vocabulary[$test_word]))
		{
			if (function_exists('mb_convert_case'))
			{
				$test_word=mb_convert_case($current_word,MB_CASE_LOWER,"UTF-8");
			} else {
				$test_word=strtolower($current_word);
			}
		}
		if (!isset($vocabulary[$test_word]))
		{
			if (function_exists('mb_convert_case'))
			{
				$test_word=mb_convert_case($current_word,MB_CASE_UPPER,"UTF-8");
			} else {
				$test_word=strtoupper($current_word);
			}
		}
		if (!isset($vocabulary[$test_word]))
		{
			if (function_exists('mb_convert_case'))
			{
				$test_word=mb_convert_case($current_word,MB_CASE_TITLE,"UTF-8");
			} else {
				$test_word=ucfirst($current_word);
			}
		}
		if (isset($vocabulary[$test_word]) && count($vocabulary[$test_word])>0)
		{
			$old_word=$current_word;
			$current_word=$vocabulary[$test_word][mt_rand(0,count($vocabulary[$test_word])-1)];
			if ($old_word!=$test_word)
			{
				if (function_exists('mb_convert_case'))
				{
					if ($old_word==mb_convert_case($test_word,MB_CASE_LOWER,"UTF-8"))
					{
						$current_word=mb_convert_case($current_word,MB_CASE_LOWER,"UTF-8");
					} elseif ($old_word==mb_convert_case($test_word,MB_CASE_UPPER,"UTF-8"))
					{
						$current_word=mb_convert_case($current_word,MB_CASE_UPPER,"UTF-8");
					} elseif ($old_word==mb_convert_case($test_word,MB_CASE_TITLE,"UTF-8"))
					{
						$current_word=mb_convert_case($current_word,MB_CASE_TITLE,"UTF-8");
					}
				} else {
					if ($old_word==strtolower($test_word))
					{
						$current_word=strtolower($current_word);
					} elseif ($old_word==strtoupper($test_word))
					{
						$current_word=strtoupper($current_word);
					} elseif ($old_word==ucfirst($test_word))
					{
						$current_word=ucfirst($current_word);
					}
				}
			}
		}
		$result.=$current_word;
	}
	return $result;
}

if ($_SERVER['argv'][1]=='exec' && $_SERVER['DOCUMENT_ROOT']=='')
{
	require_once('setup.php');
	require_once('functions_base.php');

	$object_type=$_SERVER['argv'][2];
	$object_id=intval($_SERVER['argv'][3]);

	$plugin_path="$config[project_path]/admin/data/plugins/autoreplace_words";
	$data=@unserialize(@file_get_contents("$plugin_path/data.dat"));

	$rows=explode("\n",$data['vocabulary']);
	$vocabulary=array();
	for ($i=0;$i<count($rows);$i++)
	{
		$row_items=explode(':',trim($rows[$i]),2);
		if (count($row_items)>=2)
		{
			$word=trim($row_items[0]);
			$temp=explode(",",trim($row_items[1]));
			$synonyms=array();
			foreach ($temp as $synonym)
			{
				if (trim($synonym)<>'')
				{
					$synonyms[]=trim($synonym);
				}
			}
			$vocabulary[$word]=$synonyms;
		} else {
			$row_items=explode(',',trim($rows[$i]));
			foreach ($row_items as $row_item)
			{
				$word=trim($row_item);
				$synonyms=array();
				foreach ($row_items as $row_item2)
				{
					if (trim($row_item2)<>$word)
					{
						$synonyms[]=trim($row_item2);
					}
				}
				$vocabulary[$word]=$synonyms;
			}
		}
	}

	if ($object_type=='video' && ($data['replace_videos_title']>0 || $data['replace_videos_description']>0))
	{
		$video_id=$object_id;
		$res_video=mr2array_single(sql_pr("select * from $config[tables_prefix]videos where video_id=$video_id"));

		$should_replace=true;
		if ($data['limit_feeds']>0 || $data['limit_grabbers']>0)
		{
			$should_replace=false;
			if ($data['limit_feeds']>0 && $res_video['feed_id']>0)
			{
				$should_replace=true;
			}
			if ($data['limit_grabbers']>0 && $res_video['gallery_url']!='')
			{
				$should_replace=true;
			}
		}

		if ($should_replace)
		{
			$update_array=array();
			if ($data['replace_videos_title']>0)
			{
				echo "Title before replacing: $res_video[title]\n";
				$update_array['title']=autoreplace_wordsReplace($res_video['title'],$vocabulary);
				echo "Title after replacing: $update_array[title]\n";

				$dir=get_correct_dir_name($update_array['title']);
				if ($dir<>$res_video['dir'])
				{
					$temp_dir=$dir;
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where dir=?",$temp_dir))==0)
						{
							$dir=$temp_dir;break;
						}
						$temp_dir=$dir.$i;
					}
					$update_array['dir']=$dir;
				}
			}
			if ($data['replace_videos_description']>0)
			{
				echo "Description before replacing: $res_video[description]\n";
				$update_array['description']=autoreplace_wordsReplace($res_video['description'],$vocabulary);
				echo "Description after replacing: $update_array[description]\n";
			}
			sql_pr("update $config[tables_prefix]videos set ?% where video_id=?",$update_array,$video_id);
		}
	} elseif ($object_type=='album' && ($data['replace_albums_title']>0 || $data['replace_albums_description']>0))
	{
		$album_id=$object_id;
		$res_album=mr2array_single(sql_pr("select * from $config[tables_prefix]albums where album_id=$album_id"));

		$should_replace=true;
		if ($data['limit_feeds']>0 || $data['limit_grabbers']>0)
		{
			$should_replace=false;
			if ($data['limit_feeds']>0 && $res_album['feed_id']>0)
			{
				$should_replace=true;
			}
			if ($data['limit_grabbers']>0 && $res_album['gallery_url']!='')
			{
				$should_replace=true;
			}
		}

		if ($should_replace)
		{
			$update_array=array();
			if ($data['replace_albums_title']>0)
			{
				echo "Title before replacing: $res_album[title]\n";
				$update_array['title']=autoreplace_wordsReplace($res_album['title'],$vocabulary);
				echo "Title after replacing: $update_array[title]\n";

				$dir=get_correct_dir_name($update_array['title']);
				if ($dir<>$res_album['dir'])
				{
					$temp_dir=$dir;
					for ($i=2;$i<999999;$i++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where dir=?",$temp_dir))==0)
						{
							$dir=$temp_dir;break;
						}
						$temp_dir=$dir.$i;
					}
					$update_array['dir']=$dir;
				}
			}
			if ($data['replace_albums_description']>0)
			{
				echo "Description before replacing: $res_album[description]\n";
				$update_array['description']=autoreplace_wordsReplace($res_album['description'],$vocabulary);
				echo "Description after replacing: $update_array[description]\n";
			}
			sql_pr("update $config[tables_prefix]albums set ?% where album_id=?",$update_array,$album_id);
		}
	}
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
