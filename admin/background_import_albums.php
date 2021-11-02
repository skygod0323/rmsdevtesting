<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if ($_SERVER['DOCUMENT_ROOT']<>'')
{
	header("HTTP/1.0 403 Forbidden");
	die('Access denied');
}

require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_admin.php';
require_once 'include/functions_screenshots.php';
require_once 'include/functions.php';
require_once 'include/pclzip.lib.php';

ini_set('display_errors',1);

$options=get_options();

$memory_limit=intval($options['LIMIT_MEMORY']);
if ($memory_limit==0)
{
	$memory_limit=512;
}
ini_set('memory_limit',"{$memory_limit}M");

$table_name="$config[tables_prefix]albums";
$table_key_name="album_id";

$import_id=intval($_SERVER['argv'][1]);
if ($import_id<1) {die;}

if (is_file("$config[temporary_path]/import-$import_id.dat"))
{
	$_POST=unserialize(file_get_contents("$config[temporary_path]/import-$import_id.dat"), ['allowed_classes' => false]);
} else
{
	$_POST=unserialize(mr2string(sql_pr("select options from $config[tables_prefix]background_imports where import_id=$import_id")), ['allowed_classes' => false]);
}
if (!is_array($_POST)) {die;}

$action=$_SERVER['argv'][2];
if (!in_array($action,array('validation','import'))) {die;}

$language=$_SERVER['argv'][3];
if (!is_file("$config[project_path]/admin/langs/$language.php"))
{
	$language="english";
}
$admin_id=intval($_SERVER['argv'][4]);
$background_task_id=intval($_SERVER['argv'][5]);
$background_thread_id=intval($_SERVER['argv'][6]);
if ($background_thread_id==0)
{
	$background_thread_id=1;
}

$config['sql_safe_mode'] = 1;

$admin_data=mr2array_single(sql_pr("select * from $config[tables_prefix]admin_users where user_id=?",$admin_id));
$admin_username=trim($admin_data['login']);
if ($admin_username=='')
{
	$admin_username='system';
}

if ($admin_data['is_superadmin']==0)
{
	if ($admin_data['group_id']>0)
	{
		$admin_permissions=mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_groups_permissions where group_id=?)",$admin_data['group_id']));
	} else {
		$admin_permissions=array();
	}
	$admin_permissions=array_unique(array_merge($admin_permissions,mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions where permission_id in (select permission_id from $config[tables_prefix_multi]admin_users_permissions where user_id=?)",$admin_id))));
} else {
	$admin_permissions=mr2array_list(sql_pr("select title from $config[tables_prefix_multi]admin_permissions"));
}

unset($config['sql_safe_mode']);

require_once "$config[project_path]/admin/langs/english.php";
if (is_file("$config[project_path]/admin/langs/$language.php"))
{
	require_once "$config[project_path]/admin/langs/$language.php";
}
if (is_file("$config[project_path]/admin/langs/$language/custom.php"))
{
	require_once "$config[project_path]/admin/langs/$language/custom.php";
}

$list_flags_admins=mr2array(sql_pr("select * from $config[tables_prefix]flags where group_id=2 and is_admin_flag=1"));
$list_server_groups=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers_groups where content_type_id=2"));
$list_categories_groups=mr2array(sql_pr("select * from $config[tables_prefix]categories_groups"));

$is_post_date_randomization=intval($_POST['is_post_date_randomization']);
$is_post_date_randomization_days=intval($_POST['is_post_date_randomization_days']);
$is_post_time_randomization=intval($_POST['is_post_time_randomization']);
$post_date_randomization_option=intval($_POST['post_date_randomization_option']);
$is_make_directories=intval($_POST['is_make_directories']);
$is_use_rename_as_copy=intval($_POST['is_use_rename_as_copy']);
$is_review_needed=intval($_POST['is_review_needed']);
$is_skip_duplicate_titles=intval($_POST['is_skip_duplicate_titles']);
$is_skip_duplicate_urls=intval($_POST['is_skip_duplicate_urls']);
$status_after_import_id=intval($_POST['status_after_import_id']);
$title_limit=intval($_POST['title_limit']);
$title_limit_type_id=intval($_POST['title_limit_type_id']);
$description_limit=intval($_POST['description_limit']);
$description_limit_type_id=intval($_POST['description_limit_type_id']);
$default_album_type=$_POST['default_album_type'];
$is_validate_image_urls=$_POST['is_validate_image_urls'];
$is_validate_grabber_urls=$_POST['is_validate_grabber_urls'];
$is_skip_new_categories=intval($_POST['is_skip_new_categories']);
$is_skip_new_models=intval($_POST['is_skip_new_models']);
$is_skip_new_content_sources=intval($_POST['is_skip_new_content_sources']);
$is_skip_new_dvds=intval($_POST['is_skip_new_dvds']);
$global_content_source_id=intval($_POST['content_source_id']);

if ($is_post_time_randomization==1)
{
	$post_time_from=array(0,0);
	if (strpos($_POST['post_time_randomization_from'],":")!==false)
	{
		$temp=explode(":",$_POST['post_time_randomization_from']);
		if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_from[0]=$temp[0];}
		if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_from[1]=$temp[1];}
	}
	$post_time_from=$post_time_from[0]*3600+$post_time_from[1]*60;

	$post_time_to=array(0,0);
	if (strpos($_POST['post_time_randomization_to'],":")!==false)
	{
		$temp=explode(":",$_POST['post_time_randomization_to']);
		if (intval($temp[0])>=0 && intval($temp[0])<24) {$post_time_to[0]=$temp[0];}
		if (intval($temp[1])>=0 && intval($temp[1])<60) {$post_time_to[1]=$temp[1];}
	}
	$post_time_to=$post_time_to[0]*3600+$post_time_to[1]*60;
}

$separator=$_POST['separator_modified'];
$line_separator=$_POST['line_separator_modified'];

if ($_POST['separator']=='\r\n') {$separator="\n";}
if ($_POST['line_separator']=='\r\n') {$line_separator="\n";}

for ($i=1;$i<=999;$i++)
{
	if (!isset($_POST["field$i"]))
	{
		$fields_amount=$i-1;break;
	}
}
$import_fields=array();
$index=1;
for ($i=1;$i<=$fields_amount;$i++)
{
	if ($_POST["field$i"]<>'')
	{
		$import_fields["field$index"]=trim($_POST["field$i"]);
		$index++;
	}
}

$categories_all = [];
$categories_regexp = [];
$temp = mr2array(sql_pr("select category_id, title, synonyms from $config[tables_prefix]categories"));
foreach ($temp as $category)
{
	$categories_all[mb_lowercase($category['title'])] = $category['category_id'];
	$temp_syn = explode(",", $category['synonyms']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn = trim($syn);
			if ($syn !== '')
			{
				if (strpos($syn, '*') !== false)
				{
					$categories_regexp[$syn] = $category['category_id'];
				} else
				{
					$categories_all[mb_lowercase($syn)] = $category['category_id'];
				}
			}
		}
	}
}
$models_all=array();
$temp=mr2array(sql_pr("select model_id, title, alias from $config[tables_prefix]models"));
foreach ($temp as $model)
{
	$models_all[mb_lowercase($model['title'])]=$model['model_id'];
	$temp_syn=explode(",",$model['alias']);
	if (is_array($temp_syn))
	{
		foreach ($temp_syn as $syn)
		{
			$syn=trim($syn);
			if (strlen($syn)>0)
			{
				$models_all[mb_lowercase($syn)]=$model['model_id'];
			}
		}
	}
}

if ($action=='validation')
{
	sql("set wait_timeout=86400");

	$album_id_array=array();
	$album_dir_array=array();
	$album_title_array=array();
	$album_gallery_array=array();
	$album_url_array=array();
	$created_categories_array=array();
	$created_models_array=array();
	$created_cs_array=array();
	$created_cs_groups_array=array();
	$import_result=array();
	$import_ok_lines=0;

	$lines_counter=0;
	$lines=explode($line_separator,$_POST['import_data']);
	$total=count($lines);

	foreach ($lines as $line)
	{
		$lines_counter++;

		if (trim($line)=='')
		{
			$import_result[$lines_counter]['skipped']=1;
			continue;
		}
		if (function_exists('str_getcsv') && strlen($separator)==1)
		{
			$res=str_getcsv($line,$separator);
		} else {
			$res=explode($separator,$line);
		}

		$value_title="";
		$value_directory="";
		$value_status=($status_after_import_id==0?"active":"disabled");
		$value_categories="";
		$value_models="";
		$value_content_source="";
		$value_images="";
		$value_gallery_url="";

		$value_gallery_grabber = null;
		$value_gallery_grabber_settings = null;
		$value_gallery_grabber_album_info = null;

		$named_fields=array();
		for ($i=0;$i<count($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);
			$named_fields[$import_fields["field$i1"]]=$value;
		}

		for ($i=0;$i<count($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);
			switch ($import_fields["field$i1"])
			{
				case 'album_id':
					settype($value,"integer");
					if ($value<1)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('required_field',$lang['albums']['import_export_field_id']));
					} elseif (in_array($value,$album_id_array) || mr2number(sql_pr("select count(*) from $table_name where album_id=?",$value))>0)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_unique_field',$lang['albums']['import_export_field_id']));
					}
					if ($value>0)
					{
						$album_id_array[]=$value;
					}
				break;
				case 'title':
					if ($title_limit>0)
					{
						$value=truncate_text($value,$title_limit,$title_limit_type_id);
					}
					$value_title=$value;
					if (strlen($value)>0 && (in_array(mb_lowercase($value),$album_title_array) || mr2number(sql_pr("select count(*) from $table_name where title=?",$value))>0))
					{
						if ($is_skip_duplicate_titles==1)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_unique_field',$lang['albums']['import_export_field_title']));
						} else {
							$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('import_unique_field',$lang['albums']['import_export_field_title']));
						}
					}
					if (strlen($value)>0)
					{
						$album_title_array[]=mb_lowercase($value);
					}
				break;
				case 'directory':
					$value_directory=$value;
					if (strlen($value)>0 && (in_array($value,$album_dir_array) || mr2number(sql_pr("select count(*) from $table_name where dir=?",$value))>0))
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_unique_field',$lang['albums']['import_export_field_directory']));
					}
					if (strlen($value)>0)
					{
						$album_dir_array[]=$value;
					}
				break;
				case 'categories':
					$value_categories=$value;
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $cat_title)
					{
						$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
						if ($cat_title=='') {continue;}
						if (!in_array(mb_lowercase($cat_title), $created_categories_array) && $categories_all[mb_lowercase($cat_title)] < 1)
						{
							$is_existing_synonym = false;
							foreach ($categories_regexp as $regexp => $category_id)
							{
								$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
								if (preg_match("/^$regexp$/iu", $cat_title))
								{
									$is_existing_synonym = true;
									break;
								}
							}
							if (!$is_existing_synonym)
							{
								if (!in_array('categories|add', $admin_permissions))
								{
									$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_categories'], $cat_title));
								} elseif ($is_skip_new_categories)
								{
									$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_categories'], $cat_title));
								} else
								{
									$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_categories'], $cat_title));
								}
								$created_categories_array[] = mb_lowercase($cat_title);
							}
						}
					}
				break;
				case 'models':
					$value_models=$value;
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $model_title)
					{
						$model_title=trim(str_replace("[KT_COMMA]",",",$model_title));
						if ($model_title=='') {continue;}
						if (!in_array(mb_lowercase($model_title),$created_models_array) && $models_all[mb_lowercase($model_title)]<1)
						{
							if (!in_array('models|add',$admin_permissions))
							{
								$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_error_create_object',$lang['albums']['import_export_field_models'],$model_title));
							} elseif ($is_skip_new_models)
							{
								$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('import_warning_create_object2',$lang['albums']['import_export_field_models'],$model_title));
							} else {
								$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('import_warning_create_object',$lang['albums']['import_export_field_models'],$model_title));
							}
							$created_models_array[]=mb_lowercase($model_title);
						}
					}
				break;
				case 'content_source':
					$value_content_source = $value;
					if (strlen($value) > 0 && $global_content_source_id == 0 && !in_array(mb_lowercase($value), $created_cs_array) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $value)) == 0)
					{
						if (!in_array('content_sources|add', $admin_permissions))
						{
							$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_content_source'], $value));
						} elseif ($is_skip_new_content_sources)
						{
							$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_content_source'], $value));
						} else
						{
							$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_content_source'], $value));

							if ($named_fields['content_source/group'] && !in_array(mb_lowercase($named_fields['content_source/group']), $created_cs_groups_array) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where title=?", $named_fields['content_source/group'])) == 0)
							{
								if (!in_array('content_sources_groups|add', $admin_permissions))
								{
									$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_content_source_group'], $named_fields['content_source/group']));
								} else
								{
									$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_content_source_group'], $named_fields['content_source/group']));
								}
								$created_cs_groups_array[] = mb_lowercase($value);
							}
						}
						$created_cs_array[] = mb_lowercase($value);
					}
					break;
				case 'gallery_url':
					$value_gallery_url = $value;
					if (strlen($value) > 0)
					{
						if (!is_url($value))
						{
							$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('invalid_url', $lang['albums']['import_export_field_gallery_url']));
						} else
						{
							$is_gallery_duplicate_by_url = 0;
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where gallery_url=?", $value)) > 0 || in_array($value, $album_gallery_array))
							{
								$is_gallery_duplicate_by_url = 1;
								if ($is_skip_duplicate_urls == 1)
								{
									$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('albums_import_duplicate_url', $lang['albums']['import_export_field_gallery_url']));
								} else
								{
									$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('albums_import_duplicate_url', $lang['albums']['import_export_field_gallery_url']));
								}
							}

							if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
							{
								require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
								$grabber_gunction = "grabbersFindGrabber";
								if (function_exists($grabber_gunction))
								{
									$value_gallery_grabber = $grabber_gunction($value, 'albums');
									if ($value_gallery_grabber instanceof KvsGrabberAlbum)
									{
										if ($value_gallery_grabber->is_content_url($value))
										{
											if ($is_validate_grabber_urls == 1)
											{
												$value_gallery_grabber_settings = $value_gallery_grabber->get_settings();
												$value_gallery_grabber_album_info = $value_gallery_grabber->grab_album_data($value, "$config[temporary_path]");

												if ($value_gallery_grabber_album_info)
												{
													if ($value_gallery_grabber_album_info->get_canonical())
													{
														if ($is_gallery_duplicate_by_url == 0)
														{
															if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?", md5($value_gallery_grabber_album_info->get_canonical()))) > 0 || in_array(md5($value_gallery_grabber_album_info->get_canonical()), $album_gallery_array))
															{
																if ($is_skip_duplicate_urls == 1)
																{
																	$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('albums_import_duplicate_url', $lang['albums']['import_export_field_gallery_url']));
																} else
																{
																	$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('albums_import_duplicate_url', $lang['albums']['import_export_field_gallery_url']));
																}
															}
														}
														$album_gallery_array[] = md5($value_gallery_grabber_album_info->get_canonical());
													}
													switch ($value_gallery_grabber_album_info->get_error_code())
													{
														case KvsGrabberAlbumInfo::ERROR_CODE_PAGE_UNAVAILABLE:
															$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_page_unavailable', $lang['albums']['import_export_field_gallery_url']));
															break;
														case KvsGrabberAlbumInfo::ERROR_CODE_PAGE_ERROR:
															$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_page_error', $lang['albums']['import_export_field_gallery_url'], substr($value_gallery_grabber_album_info->get_error_message(), 0, 200)));
															break;
														case KvsGrabberAlbumInfo::ERROR_CODE_PARSING_ERROR:
															$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_parsing_error', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
															break;
														case KvsGrabberAlbumInfo::ERROR_CODE_UNEXPECTED_ERROR:
															$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_unexpected_error', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
															break;
													}
												} else
												{
													$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_unexpected_error', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
												}
											} else
											{
												$value_title = $value;
											}
										} else
										{
											$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_no_grabber_url', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
										}
									} else
									{
										$host = str_replace('www.', '', parse_url($value, PHP_URL_HOST));
										$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_no_grabber', $lang['albums']['import_export_field_gallery_url'], $host));
									}
								}
							}
							$album_gallery_array[] = $value;
						}
					}
				break;
				case 'post_date':
					if (strlen($value)!=0 && strtotime($value)<strtotime("1980-01-01"))
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_date',$lang['albums']['import_export_field_post_date']));
					}
				break;
				case 'relative_post_date':
					if (strlen($value)!=0 && intval($value)==0)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('integer_field',$lang['albums']['import_export_field_post_date']));
					}
				break;
				case 'rating':
					if (strlen($value)>0)
					{
						settype($value,"float");
						if ($value>10 || $value<0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_rating',$lang['albums']['import_export_field_rating']));
						}
					}
				break;
				case 'rating_percent':
					if (strlen($value)>0)
					{
						settype($value,"integer");
						if ($value>100 || $value<0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_rating_percent',$lang['albums']['import_export_field_rating_percent']));
						}
					}
				break;
				case 'rating_amount':
					if (strlen($value)!=0 && intval($value)==0)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('integer_field',$lang['albums']['import_export_field_rating_amount']));
					}
				break;
				case 'album_viewed':
					if (strlen($value)!=0 && intval($value)==0)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('integer_field',$lang['albums']['import_export_field_visits']));
					}
				break;
				case 'user':
					if (strlen($value)==0)
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('required_field',$lang['albums']['import_export_field_user']));
					} elseif (mr2number(sql_pr("select count(*) from $config[tables_prefix]users where username=?",$value))==0)
					{
						if (!in_array('users|add',$admin_permissions))
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_error_create_object',$lang['albums']['import_export_field_user'],$value));
						} else {
							$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('import_warning_create_object',$lang['albums']['import_export_field_user'],$value));
						}
					}
				break;
				case 'status':
					$value=mb_lowercase($value);
					$value_status=$value;
					if (!in_array($value,array('active','disabled')))
					{
						$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_status',$lang['albums']['import_export_field_status'],'active','disabled'));
					}
				break;
				case 'type':
					if (strlen($value)>0)
					{
						$value=mb_lowercase($value);
						if (!in_array($value,array('private','public','premium')))
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_type',$lang['albums']['import_export_field_type'],'private','public','premium'));
						}
					}
				break;
				case 'access_level':
					if (strlen($value)>0)
					{
						$value=mb_lowercase($value);
						if (!in_array($value,array('inherit','all','members','premium')))
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_access_level',$lang['albums']['import_export_field_access_level'],'inherit','all','members','premium'));
						}
					}
				break;
				case 'tokens':
					if (strlen($value)>0 && $value<>'0')
					{
						settype($value,"integer");
						if ($value<1)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('integer_field',$lang['albums']['import_export_field_tokens_cost']));
						}
					}
				break;
				case 'admin_flag':
					if (strlen($value)>0)
					{
						$found=0;
						foreach ($list_flags_admins as $flag)
						{
							if ($flag['title']==$value)
							{
								$found=1;
								break;
							}
						}
						if ($found==0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_flag',$lang['albums']['import_export_field_admin_flag'],$value));
						}
					}
				break;
				case 'server_group':
					if (strlen($value)>0)
					{
						$found=0;
						foreach ($list_server_groups as $server_group)
						{
							if ($server_group['title']==$value)
							{
								$found=1;
								break;
							}
						}
						if ($found==0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_server_group',$lang['albums']['import_export_field_server_group'],$value));
						}
					}
				break;
				case 'images_zip':
					if (strlen($value)>0)
					{
						if (!is_url($value) && strpos($value,'/')!==0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_url',$lang['albums']['import_export_field_images_zip']));
						} else
						{
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?",md5($value)))>0 || in_array($value,$album_url_array))
							{
								if ($is_skip_duplicate_urls==1)
								{
									$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('albums_import_duplicate_url',$lang['albums']['import_export_field_images_zip']));
								} else {
									$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('albums_import_duplicate_url',$lang['albums']['import_export_field_images_zip']));
								}
							}
							if ($is_validate_image_urls==1)
							{
								if (strpos($value,'/')===0)
								{
									if (!is_file($value) || !is_readable($value))
									{
										$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_remote_file',$lang['albums']['import_export_field_images_zip']));
									}
								} else {
									if (!is_binary_file_url($value))
									{
										$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_remote_file',$lang['albums']['import_export_field_images_zip']));
									}
								}
							}
							$album_url_array[]=$value;
						}
						$value_images=$value;
					}
				break;
				case 'images_sources':
					if (strlen($value)>0)
					{
						$value_temp=explode(",",$value);
						if (count($value_temp)>0)
						{
							$first_image_url=trim($value_temp[0]);
							if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where external_key=?",md5($first_image_url)))>0 || in_array($first_image_url,$album_url_array))
							{
								if ($is_skip_duplicate_urls==1)
								{
									$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('albums_import_duplicate_url',$lang['albums']['import_export_field_images_sources']));
								} else {
									$import_result[$lines_counter]['warnings'][]=bb_code_process(get_aa_error('albums_import_duplicate_url',$lang['albums']['import_export_field_images_sources']));
								}
							}
							$album_url_array[]=$first_image_url;
						}

						$url_errors_count=0;
						$availability_errors_count=0;
						$total_urls_count=0;

						foreach ($value_temp as $image_url)
						{
							$image_url=trim($image_url);
							if ($image_url=='') {continue;}

							$total_urls_count++;
							if (!is_url($image_url) && strpos($image_url,'/')!==0)
							{
								$url_errors_count++;
							} elseif ($is_validate_image_urls==1)
							{
								if (strpos($image_url,'/')===0)
								{
									if (!is_file($image_url) || !is_readable($image_url))
									{
										$availability_errors_count++;
									}
								} else {
									if (!is_binary_file_url($image_url))
									{
										$availability_errors_count++;
									}
								}
							}
						}
						if ($availability_errors_count+$url_errors_count>0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('import_error_urls_not_valid',$lang['albums']['import_export_field_images_sources'],$availability_errors_count+$url_errors_count,$total_urls_count));
						}
						$value_images=$value;
					}
				break;
				case 'image_preview':
					if (strlen($value)>0)
					{
						if (!is_url($value) && strpos($value,'/')!==0)
						{
							$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_url',$lang['albums']['import_export_field_image_preview_source']));
						} elseif ($is_validate_image_urls==1)
						{
							if (strpos($value,'/')===0)
							{
								if (!is_file($value) || !is_readable($value))
								{
									$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_remote_file',$lang['albums']['import_export_field_image_preview_source']));
								}
							} else {
								if (!is_binary_file_url($value))
								{
									$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('invalid_remote_file',$lang['albums']['import_export_field_image_preview_source']));
								}
							}
						}
					}
				break;
			}

			foreach ($list_categories_groups as $category_group)
			{
				if ($import_fields["field$i1"]=="categoty_group_{$category_group['category_group_id']}")
				{
					if (strlen($value)>0)
					{
						$value_temp=str_replace("\\,","[KT_COMMA]",$value);
						$value_temp=explode(",",$value_temp);
						foreach ($value_temp as $cat_title)
						{
							$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
							if ($cat_title=='') {continue;}
							if (!in_array(mb_lowercase($cat_title), $created_categories_array) && $categories_all[mb_lowercase($cat_title)] < 1)
							{
								$is_existing_synonym = false;
								foreach ($categories_regexp as $regexp => $category_id)
								{
									$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
									if (preg_match("/^$regexp$/iu", $cat_title))
									{
										$is_existing_synonym = true;
										break;
									}
								}
								if (!$is_existing_synonym)
								{
									if (!in_array('categories|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_categories'] . " ($category_group[title])", $cat_title));
									} elseif ($is_skip_new_categories)
									{
										$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_categories'] . " ($category_group[title])", $cat_title));
									} else
									{
										$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_categories'] . " ($category_group[title])", $cat_title));
									}
									$created_categories_array[] = mb_lowercase($cat_title);
								}
							}
						}
					}
					break;
				}
			}
		}

		if (strlen($value_images)==0 && strlen($value_gallery_url)==0)
		{
			$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('required_field',$lang['albums']['import_export_field_images_sources']));
		}

		if ($value_gallery_grabber_album_info && $value_gallery_grabber_album_info->get_error_code() == 0)
		{
			$quantity_filter_ok = true;
			if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0 || $value_gallery_grabber_settings->get_filter_quantity_to() > 0)
			{
				$quantity_filter_ok_from = true;
				$quantity_filter_ok_to = true;
				if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0)
				{
					if (count($value_gallery_grabber_album_info->get_image_files()) < $value_gallery_grabber_settings->get_filter_quantity_from())
					{
						$quantity_filter_ok_from = false;
					}
				}
				if ($value_gallery_grabber_settings->get_filter_quantity_to() > 0)
				{
					if (count($value_gallery_grabber_album_info->get_image_files()) > $value_gallery_grabber_settings->get_filter_quantity_to())
					{
						$quantity_filter_ok_to = false;
					}
				}
				$quantity_filter_ok = $quantity_filter_ok_from && $quantity_filter_ok_to;
			}

			$rating_filter_ok = true;
			if ($value_gallery_grabber_settings->get_filter_rating_from() > 0 || $value_gallery_grabber_settings->get_filter_rating_to() > 0)
			{
				if ($value_gallery_grabber->can_grab_rating())
				{
					$rating_filter_ok_from = true;
					$rating_filter_ok_to = true;
					if ($value_gallery_grabber_settings->get_filter_rating_from() > 0)
					{
						if ($value_gallery_grabber_album_info->get_rating() < $value_gallery_grabber_settings->get_filter_rating_from())
						{
							$rating_filter_ok_from = false;
						}
					}
					if ($value_gallery_grabber_settings->get_filter_rating_to() > 0)
					{
						if ($value_gallery_grabber_album_info->get_rating() > $value_gallery_grabber_settings->get_filter_rating_to())
						{
							$rating_filter_ok_to = false;
						}
					}
					$rating_filter_ok = $rating_filter_ok_from && $rating_filter_ok_to;
				}
			}

			$views_filter_ok = true;
			if ($value_gallery_grabber_settings->get_filter_views_from() > 0 || $value_gallery_grabber_settings->get_filter_views_to() > 0)
			{
				if ($value_gallery_grabber->can_grab_views())
				{
					$views_filter_ok_from = true;
					$views_filter_ok_to = true;
					if ($value_gallery_grabber_settings->get_filter_views_from() > 0)
					{
						if ($value_gallery_grabber_album_info->get_views() < $value_gallery_grabber_settings->get_filter_views_from())
						{
							$views_filter_ok_from = false;
						}
					}
					if ($value_gallery_grabber_settings->get_filter_views_to() > 0)
					{
						if ($value_gallery_grabber_album_info->get_views() > $value_gallery_grabber_settings->get_filter_views_to())
						{
							$views_filter_ok_to = false;
						}
					}
					$views_filter_ok = $views_filter_ok_from && $views_filter_ok_to;
				}
			}

			$date_filter_ok = true;
			if ($value_gallery_grabber_settings->get_filter_date_from() > 0 || $value_gallery_grabber_settings->get_filter_date_to() > 0)
			{
				if ($value_gallery_grabber->can_grab_date() && $value_gallery_grabber_album_info->get_date() > 0)
				{
					$date_filter_value = floor((time() - $value_gallery_grabber_album_info->get_date()) / 86400);
					$date_filter_ok_from = true;
					$date_filter_ok_to = true;
					if ($value_gallery_grabber_settings->get_filter_date_from() > 0)
					{
						if ($date_filter_value < $value_gallery_grabber_settings->get_filter_date_from())
						{
							$date_filter_ok_from = false;
						}
					}
					if ($value_gallery_grabber_settings->get_filter_date_to() > 0)
					{
						if ($date_filter_value > $value_gallery_grabber_settings->get_filter_date_to())
						{
							$date_filter_ok_to = false;
						}
					}
					$date_filter_ok = $date_filter_ok_from && $date_filter_ok_to;
				}
			}

			$terminology_filter_ok = true;
			if ($value_gallery_grabber_settings->get_filter_terminology())
			{
				$terminology_filter_value = array_map('trim', explode(',', mb_lowercase($value_gallery_grabber_settings->get_filter_terminology())));
				$terminology_filter_title = mb_lowercase($value_gallery_grabber_album_info->get_title());

				unset($terminology_filter_words_in_title);
				preg_match_all('/([\p{N}\p{L}-_#@]+)/u', $terminology_filter_title, $terminology_filter_words_in_title);

				foreach ($terminology_filter_words_in_title[0] as $word)
				{
					if (in_array($word, $terminology_filter_value))
					{
						$terminology_filter_ok = false;
						break;
					}
				}

				if ($terminology_filter_ok)
				{
					foreach ($terminology_filter_value as $word)
					{
						if (strpos($word, ' ') && strpos($terminology_filter_title, $word))
						{
							$terminology_filter_ok = false;
							break;
						}
					}
				}
			}

			if (!$quantity_filter_ok)
			{
				$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_album_images_filter', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), count($value_gallery_grabber_album_info->get_image_files())));
			} elseif (!$rating_filter_ok)
			{
				$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_rating_filter', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_album_info->get_rating()));
			} elseif (!$views_filter_ok)
			{
				$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_views_filter', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_album_info->get_views()));
			} elseif (!$date_filter_ok)
			{
				$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_date_filter', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $date_filter_value));
			} elseif (!$terminology_filter_ok)
			{
				$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_terminology_filter', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name(), $value_gallery_grabber_album_info->get_title()));
			} elseif (@count($import_result[$lines_counter]['errors'])==0)
			{
				foreach ($value_gallery_grabber_settings->get_data() as $grabber_settings_data_item)
				{
					switch ($grabber_settings_data_item)
					{
						case KvsGrabberSettings::DATA_FIELD_TITLE:
							if (strlen($value_title) == 0)
							{
								$value_title = $value_gallery_grabber_album_info->get_title();
								if ($title_limit > 0)
								{
									$value_title = truncate_text($value_title, $title_limit, $title_limit_type_id);
								}

								if (strlen($value_title) > 0 && (in_array(mb_lowercase($value_title), $album_title_array) || mr2number(sql_pr("select count(*) from $table_name where title=?", $value_title)) > 0))
								{
									if ($is_skip_duplicate_titles == 1)
									{
										$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_unique_field', $lang['albums']['import_export_field_title']));
									} else
									{
										$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_unique_field', $lang['albums']['import_export_field_title']));
									}
								}
								$album_title_array[] = mb_lowercase($value_title);
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CATEGORIES:
							if (strlen($value_categories) == 0)
							{
								$value_categories = $value_gallery_grabber_album_info->get_categories();
								foreach ($value_categories as $cat_title)
								{
									$cat_title = trim($cat_title);
									if ($cat_title == '')
									{
										continue;
									}
									if (!in_array(mb_lowercase($cat_title), $created_categories_array) && $categories_all[mb_lowercase($cat_title)] < 1)
									{
										$is_existing_synonym = false;
										foreach ($categories_regexp as $regexp => $category_id)
										{
											$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
											if (preg_match("/^$regexp$/iu", $cat_title))
											{
												$is_existing_synonym = true;
												break;
											}
										}
										if (!$is_existing_synonym)
										{
											if (!in_array('categories|add', $admin_permissions))
											{
												$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_categories'], $cat_title));
											} elseif ($is_skip_new_categories)
											{
												$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_categories'], $cat_title));
											} else
											{
												$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_categories'], $cat_title));
											}
											$created_categories_array[] = mb_lowercase($cat_title);
										}
									}
								}
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_MODELS:
							if (strlen($value_models) == 0)
							{
								$value_models = $value_gallery_grabber_album_info->get_models();
								foreach ($value_models as $model_title)
								{
									$model_title = trim($model_title);
									if ($model_title == '')
									{
										continue;
									}
									if (!in_array(mb_lowercase($model_title), $created_models_array) && $models_all[mb_lowercase($model_title)] < 1)
									{
										if (!in_array('models|add', $admin_permissions))
										{
											$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_models'], $model_title));
										} elseif ($is_skip_new_models)
										{
											$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_models'], $model_title));
										} else
										{
											$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_models'], $model_title));
										}
										$created_models_array[] = mb_lowercase($model_title);
									}
								}
							}
							break;
						case KvsGrabberSettings::DATA_FIELD_CONTENT_SOURCE:
							if (strlen($value_content_source) == 0 && $global_content_source_id == 0)
							{
								$value_content_source = $value_gallery_grabber_album_info->get_content_source();
								if ($value_content_source!='' && !in_array(mb_lowercase($value_content_source), $created_cs_array) && mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where title=?", $value_content_source)) == 0)
								{
									if (!in_array('content_sources|add', $admin_permissions))
									{
										$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('import_error_create_object', $lang['albums']['import_export_field_content_source'], $value_content_source));
									} elseif ($is_skip_new_content_sources)
									{
										$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object2', $lang['albums']['import_export_field_content_source'], $value_content_source));
									} else
									{
										$import_result[$lines_counter]['warnings'][] = bb_code_process(get_aa_error('import_warning_create_object', $lang['albums']['import_export_field_content_source'], $value_content_source));
									}
									$created_cs_array[] = mb_lowercase($value_content_source);
								}
							}
							break;
					}
				}

				switch ($value_gallery_grabber_settings->get_mode())
				{
					case KvsGrabberSettings::GRAB_MODE_DOWNLOAD:
						if (count($value_gallery_grabber_album_info->get_image_files()) == 0)
						{
							$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_missing_image_files', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
						} else
						{
							$import_result[$lines_counter]['info'][] = bb_code_process(get_aa_error('grabbers_album_info_download', $lang['albums']['import_export_field_gallery_url'], $value_title, count($value_gallery_grabber_album_info->get_image_files())));
						}
						break;
					case KvsGrabberSettings::GRAB_MODE_EMBED:
					case KvsGrabberSettings::GRAB_MODE_PSEUDO:
						$import_result[$lines_counter]['errors'][] = bb_code_process(get_aa_error('grabbers_albums_mode_not_supported', $lang['albums']['import_export_field_gallery_url'], $value_gallery_grabber->get_grabber_name()));
						break;
				}
			}
		}

		if (@count($import_result[$lines_counter]['errors'])==0)
		{
			if ($value_status=="active" && strlen($value_title)==0)
			{
				$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('required_field',$lang['albums']['import_export_field_title']));
			} elseif ($value_status=="active" && strlen($value_directory)==0)
			{
				if ($is_make_directories!=1)
				{
					$import_result[$lines_counter]['errors'][]=bb_code_process(get_aa_error('required_field',$lang['albums']['import_export_field_directory']));
				}
			}
		}

		if (!isset($import_result[$lines_counter]['errors']))
		{
			$import_ok_lines++;
		}

		$pc=floor(($lines_counter/$total)*100);
		file_put_contents("$config[temporary_path]/import-progress-$import_id.dat","$pc",LOCK_EX);

		if ($lines_counter % 10 == 0)
		{
			$la = get_LA();
			if ($la > 5)
			{
				usleep(50000);
			} elseif ($la > 1)
			{
				usleep(5000);
			}
		}
	}

	$lines_with_errors=array();
	foreach ($import_result as $counter=>$res)
	{
		if (is_array($res['errors']))
		{
			$lines_with_errors[]=$counter;
		}
	}
	$lines_with_errors=array_unique($lines_with_errors);
	$_POST['lines_with_errors']=$lines_with_errors;
	$_POST['lines_to_import']=$import_ok_lines;
	$_POST['import_result']=$import_result;

	file_put_contents("$config[temporary_path]/import-$import_id.dat",serialize($_POST),LOCK_EX);
	file_put_contents("$config[temporary_path]/import-progress-$import_id.dat","100",LOCK_EX);
} elseif ($action=='import')
{
	$global_lock_file = "$config[project_path]/admin/data/system/background_import.lock";
	if (!is_file($global_lock_file))
	{
		file_put_contents($global_lock_file, "1", LOCK_EX);
	}
	$global_lock = fopen($global_lock_file, "r");

	if (!mkdir_recursive("$config[project_path]/admin/data/engine/import"))
	{
		die("Failed to create directory: $config[project_path]/admin/data/engine/import");
	}

	$lock_file = "$config[project_path]/admin/data/engine/import/import_{$import_id}_{$background_thread_id}.lock";
	if (!is_file($lock_file))
	{
		file_put_contents($lock_file, "1", LOCK_EX);
	}

	$lock = fopen($lock_file, "r+");
	if (!flock($lock, LOCK_EX | LOCK_NB))
	{
		die('Already locked');
	}

	log_import("Started import $import_id");

	sql("set wait_timeout=86400");

	$users=explode(",",$_POST['users']);
	$users_ids=array();
	if (is_array($users))
	{
		$users=array_map("trim",$users);
		$users=array_unique($users);
		foreach ($users as $user)
		{
			if (strlen($user)>0)
			{
				$user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$user));
				if ($user_id>0)
				{
					$users_ids[]=$user_id;
				}
			}
		}
	}

	$languages=mr2array(sql_pr("select * from $config[tables_prefix]languages order by title asc"));

	$lines=mr2array(sql_pr("select * from $config[tables_prefix]background_imports_data where import_id=? and thread_id=? and status_id=0 order by line_id asc", $import_id, $background_thread_id));

	$last_line_id=0;
	$total=count($lines);
	foreach ($lines as $line)
	{
		if ($last_line_id > 0)
		{
			sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and line_id=?", $import_id, $last_line_id);
		}
		$last_line_id = $line['line_id'];

		if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?",$background_task_id))==0)
		{
			log_import("Interrupted by user");
			break;
		}
		if (min(@disk_free_space($config['project_path']),@disk_free_space($config['content_path_albums_sources']))<$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
		{
			while (true)
			{
				$message="Server free space is lower than $options[MAIN_SERVER_MIN_FREE_SPACE_MB]M, waiting 10 minutes for the next try";
				log_import($message);
				sql_pr("update $config[tables_prefix]background_tasks set message=? where task_id=?",$message,$background_task_id);
				sleep(600);
				if (mr2number(sql_pr("select count(*) from $config[tables_prefix]background_tasks where task_id=?",$background_task_id))==0)
				{
					log_import("Interrupted by user");
					break 2;
				}
				$options['MAIN_SERVER_MIN_FREE_SPACE_MB']=mr2number(sql_pr("select value from $config[tables_prefix]options where variable='MAIN_SERVER_MIN_FREE_SPACE_MB'"));
				if (min(@disk_free_space($config['project_path']),@disk_free_space($config['content_path_albums_sources']))>=$options['MAIN_SERVER_MIN_FREE_SPACE_MB']*1024*1024)
				{
					sql_pr("update $config[tables_prefix]background_tasks set message='' where task_id=?",$background_task_id);
					break;
				}
			}
		}

		log_import("Started line #$line[line_id]");
		if (function_exists('str_getcsv') && strlen($separator)==1)
		{
			$res=str_getcsv($line['data'],$separator);
		} else {
			$res=explode($separator,$line['data']);
		}

		$insert_data=array();
		$value_gallery_grabber_album_info=null;
		$value_status_id=($status_after_import_id==0?1:0);
		$value_images_zip='';
		$value_images_list=array();
		$value_images_referer='';
		$value_image_preview='';
		$value_main_image_number=1;
		$value_server_group_id=0;
		$category_ids=array();
		$model_ids=array();
		$tag_ids=array();

		if ($default_album_type=='private')
		{
			$insert_data['is_private']=1;
		} elseif ($default_album_type=='premium')
		{
			$insert_data['is_private']=2;
		} else {
			$insert_data['is_private']=0;
		}

		$named_fields=array();
		for ($i=0;$i<count($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);
			$named_fields[$import_fields["field$i1"]]=$value;
		}

		flock($global_lock, LOCK_EX);
		for ($i=0;$i<count($res);$i++)
		{
			$i1=$i+1;
			$value=trim($res[$i]);

			switch ($import_fields["field$i1"])
			{
				case 'album_id':
					$insert_data['album_id']=$value;
				break;
				case 'title':
					if ($title_limit>0)
					{
						$value=truncate_text($value,$title_limit,$title_limit_type_id);
					}
					$insert_data['title']=$value;
				break;
				case 'directory':
					$insert_data['dir']=$value;
				break;
				case 'description':
					if ($description_limit>0)
					{
						$value=truncate_text($value,$description_limit,$description_limit_type_id);
					}
					$insert_data['description']=$value;
				break;
				case 'categories':
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $cat_title)
					{
						$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
						if ($cat_title=='') {continue;}

						if ($categories_all[mb_lowercase($cat_title)]>0)
						{
							$cat_id=$categories_all[mb_lowercase($cat_title)];
						} else {
							$cat_id=mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?",$cat_title));
							if ($cat_id == 0)
							{
								foreach ($categories_regexp as $regexp => $category_id)
								{
									$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
									if (preg_match("/^$regexp$/iu", $cat_title))
									{
										$cat_id = $category_id;
										break;
									}
								}
							}
							if ($cat_id==0 && !$is_skip_new_categories)
							{
								$cat_dir=get_correct_dir_name($cat_title);
								$temp_dir=$cat_dir;
								for ($it=2;$it<999999;$it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?",$temp_dir))==0)
									{
										$cat_dir=$temp_dir;break;
									}
									$temp_dir=$cat_dir.$it;
								}
								$cat_id=sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?",$cat_title,$cat_dir,date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?",$admin_id,$admin_username,$cat_id,date("Y-m-d H:i:s"));
							}
							if ($cat_id>0)
							{
								$categories_all[mb_lowercase($cat_title)]=$cat_id;
							}
						}
						if ($cat_id>0)
						{
							$category_ids[]=$cat_id;
						}
					}
				break;
				case 'models':
					$value_temp=str_replace("\\,","[KT_COMMA]",$value);
					$value_temp=explode(",",$value_temp);
					foreach ($value_temp as $model_title)
					{
						$model_title=trim(str_replace("[KT_COMMA]",",",$model_title));
						if ($model_title=='') {continue;}

						if ($models_all[mb_lowercase($model_title)]>0)
						{
							$model_id=$models_all[mb_lowercase($model_title)];
						} else {
							$model_id=mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?",$model_title));
							if ($model_id==0 && !$is_skip_new_models)
							{
								$model_dir=get_correct_dir_name($model_title);
								$temp_dir=$model_dir;
								for ($it=2;$it<999999;$it++)
								{
									if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?",$temp_dir))==0)
									{
										$model_dir=$temp_dir;break;
									}
									$temp_dir=$model_dir.$it;
								}
								$model_id=sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?",$model_title,$model_dir,date("Y-m-d H:i:s"));
								sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=4, added_date=?",$admin_id,$admin_username,$model_id,date("Y-m-d H:i:s"));
							}
							if ($model_id>0)
							{
								$models_all[mb_lowercase($model_title)]=$model_id;
							}
						}
						if ($model_id>0)
						{
							$model_ids[]=$model_id;
						}
					}
				break;
				case 'tags':
					$value_temp=explode(",",$value);
					$inserted_tags=array();
					foreach ($value_temp as $tag_title)
					{
						$tag_title=trim($tag_title);
						if ($tag_title=='') {continue;}
						if (in_array(mb_lowercase($tag_title),$inserted_tags)) {continue;}

						$tag_id=find_or_create_tag($tag_title, $options);
						if ($tag_id>0)
						{
							$inserted_tags[]=mb_lowercase($tag_title);
							$tag_ids[]=$tag_id;
						}
					}
				break;
				case 'content_source':
					$content_source_id = 0;
					if ($global_content_source_id > 0)
					{
						$content_source_id = $global_content_source_id;
					} elseif (strlen($value) > 0)
					{
						$content_source_id = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $value));
						if ($content_source_id == 0 && !$is_skip_new_content_sources)
						{
							$cs_dir = get_correct_dir_name($value);
							$temp_dir = $cs_dir;
							for ($it = 2; $it < 999999; $it++)
							{
								if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
								{
									$cs_dir = $temp_dir;
									break;
								}
								$temp_dir = $cs_dir . $it;
							}
							$content_source_id = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, url=?, rating_amount=1, added_date=?", $value, $cs_dir, trim($named_fields['content_source/url']), date("Y-m-d H:i:s"));
							sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=3, added_date=?", $admin_id, $admin_username, $content_source_id, date("Y-m-d H:i:s"));

							if ($named_fields['content_source/group'])
							{
								$content_source_group_id = mr2number(sql_pr("select content_source_group_id from $config[tables_prefix]content_sources_groups where title=?", $named_fields['content_source/group']));
								if ($content_source_group_id == 0)
								{
									$cs_group_dir = get_correct_dir_name($named_fields['content_source/group']);
									$temp_dir = $cs_group_dir;
									for ($it = 2; $it < 999999; $it++)
									{
										if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources_groups where dir=?", $temp_dir)) == 0)
										{
											$cs_group_dir = $temp_dir;
											break;
										}
										$temp_dir = $cs_group_dir . $it;
									}
									$content_source_group_id = sql_insert("insert into $config[tables_prefix]content_sources_groups set title=?, dir=?, added_date=?", $named_fields['content_source/group'], $cs_group_dir, date("Y-m-d H:i:s"));
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=8, added_date=?", $admin_id, $admin_username, $content_source_group_id, date("Y-m-d H:i:s"));
								}
								if ($content_source_group_id > 0)
								{
									sql_pr("update $config[tables_prefix]content_sources set content_source_group_id=? where content_source_id=?", $content_source_group_id, $content_source_id);
								}
							}
						}
					}
					$insert_data['content_source_id'] = $content_source_id;
					break;
				case 'post_date':
					if (strlen($value)<>0)
					{
						$insert_data['post_date']=date("Y-m-d",strtotime($value));
						if ($is_post_time_randomization==1)
						{
							$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
						} else {
							$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($value));
						}
					}
				break;
				case 'relative_post_date':
					if (strlen($value)<>0)
					{
						$insert_data['post_date']='1971-01-01 00:00:00';
						$insert_data['relative_post_date']=intval($value);
					}
				break;
				case 'rating':
					$insert_data['rating']=floatval($value);
					if (intval($insert_data['rating_amount'])==0)
					{
						$insert_data['rating_amount']=1;
					}
				break;
				case 'rating_percent':
					$insert_data['rating']=intval($value) / 20;
					if (intval($insert_data['rating_amount'])==0)
					{
						$insert_data['rating_amount']=1;
					}
				break;
				case 'rating_amount':
					if (intval($value)>0)
					{
						$insert_data['rating_amount']=intval($value);
					}
				break;
				case 'album_viewed':
					$insert_data['album_viewed']=intval($value);
				break;
				case 'user':
					$user_id=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$value));
					if ($user_id==0)
					{
						$email=$value;
						if (!preg_match($regexp_check_email,$email))
						{
							$email=generate_email($value);
						}
						$user_id=sql_insert("insert into $config[tables_prefix]users set username=?, status_id=2, display_name=?, email=?, added_date=?",$value,$value,$email,date("Y-m-d H:i:s"));
					}
					$insert_data['user_id']=$user_id;
				break;
				case 'status':
					if (mb_lowercase($value)=='active')
					{
						$value_status_id=1;
					} else {
						$value_status_id=0;
					}
				break;
				case 'type':
					if (mb_lowercase($value)=='private')
					{
						$insert_data['is_private']=1;
					} elseif (mb_lowercase($value)=='premium')
					{
						$insert_data['is_private']=2;
					} elseif (mb_lowercase($value)=='public')
					{
						$insert_data['is_private']=0;
					}
				break;
				case 'access_level':
					if (mb_lowercase($value)=='inherit')
					{
						$insert_data['access_level_id']=0;
					} elseif (mb_lowercase($value)=='all')
					{
						$insert_data['access_level_id']=1;
					} elseif (mb_lowercase($value)=='members')
					{
						$insert_data['access_level_id']=2;
					} elseif (mb_lowercase($value)=='premium')
					{
						$insert_data['access_level_id']=3;
					}
				break;
				case 'tokens':
					$insert_data['tokens_required']=intval($value);
				break;
				case 'admin_flag':
					if (strlen($value)>0)
					{
						foreach ($list_flags_admins as $flag)
						{
							if ($flag['title']==$value)
							{
								$insert_data['admin_flag_id']=$flag['flag_id'];
								break;
							}
						}
					}
				break;
				case 'server_group':
					if (strlen($value)>0)
					{
						foreach ($list_server_groups as $server_group)
						{
							if ($server_group['title']==$value)
							{
								$value_server_group_id=$server_group['group_id'];
								break;
							}
						}
					}
				break;
				case 'custom_1':
					$insert_data['custom1']=$value;
				break;
				case 'custom_2':
					$insert_data['custom2']=$value;
				break;
				case 'custom_3':
					$insert_data['custom3']=$value;
				break;
				case 'gallery_url':
					$insert_data['gallery_url']=$value;
				break;
				case 'images_zip':
					$value_images_zip=$value;
				break;
				case 'images_sources':
					$value_images_list=explode(",",$value);
				break;
				case 'image_preview':
					$value_image_preview=$value;
				break;
				case 'image_main_number':
					$value_main_image_number=intval($value);
				break;
			}

			foreach ($languages as $language)
			{
				if ($import_fields["field$i1"]=="title_{$language['code']}")
				{
					if ($title_limit>0)
					{
						$value=truncate_text($value,$title_limit,$title_limit_type_id);
					}
					$insert_data["title_{$language['code']}"]=$value;
				}
				if ($import_fields["field$i1"]=="description_{$language['code']}")
				{
					if ($description_limit>0)
					{
						$value=truncate_text($value,$description_limit,$description_limit_type_id);
					}
					$insert_data["description_{$language['code']}"]=$value;
				}
				if ($import_fields["field$i1"]=="directory_{$language['code']}")
				{
					$insert_data["dir_{$language['code']}"]=$value;
				}
			}

			foreach ($list_categories_groups as $category_group)
			{
				if ($import_fields["field$i1"]=="categoty_group_{$category_group['category_group_id']}")
				{
					if (strlen($value)>0)
					{
						$value_temp=str_replace("\\,","[KT_COMMA]",$value);
						$value_temp=explode(",",$value_temp);
						foreach ($value_temp as $cat_title)
						{
							$cat_title=trim(str_replace("[KT_COMMA]",",",$cat_title));
							if ($cat_title=='') {continue;}

							if ($categories_all[mb_lowercase($cat_title)]>0)
							{
								$cat_id=$categories_all[mb_lowercase($cat_title)];
							} else {
								$cat_id=mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?",$cat_title));
								if ($cat_id == 0)
								{
									foreach ($categories_regexp as $regexp => $category_id)
									{
										$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
										if (preg_match("/^$regexp$/iu", $cat_title))
										{
											$cat_id = $category_id;
											break;
										}
									}
								}
								if ($cat_id==0 && !$is_skip_new_categories)
								{
									$cat_dir=get_correct_dir_name($cat_title);
									$temp_dir=$cat_dir;
									for ($it=2;$it<999999;$it++)
									{
										if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?",$temp_dir))==0)
										{
											$cat_dir=$temp_dir;break;
										}
										$temp_dir=$cat_dir.$it;
									}
									$cat_id=sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, category_group_id=?, added_date=?",$cat_title,$cat_dir,$category_group['category_group_id'],date("Y-m-d H:i:s"));
									sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?",$admin_id,$admin_username,$cat_id,date("Y-m-d H:i:s"));
								}
								if ($cat_id>0)
								{
									$categories_all[mb_lowercase($cat_title)]=$cat_id;
								}
							}
							if ($cat_id>0)
							{
								$category_ids[]=$cat_id;
							}
						}
					}
					break;
				}
			}
		}
		flock($global_lock, LOCK_UN);

		if ($insert_data['gallery_url']!='')
		{
			if ($is_skip_duplicate_urls==1)
			{
				$duplicate_album_id = mr2number(sql_pr("select album_id from $config[tables_prefix]albums where gallery_url=? limit 1", $insert_data['gallery_url']));
				if ($duplicate_album_id > 0)
				{
					log_import("ERROR: duplicate gallery, already added into album $duplicate_album_id");
					continue;
				} else
				{
					$duplicate_import_id = mr2number(sql_pr("select i.import_id from $config[tables_prefix]background_imports i inner join $config[tables_prefix]background_imports_data d on i.import_id=d.import_id where i.status_id in (0,1) and d.data=? and i.import_id<$import_id order by import_id asc limit 1", $insert_data['gallery_url']));
					if ($duplicate_import_id > 0)
					{
						log_import("ERROR: duplicate gallery, already added into import $duplicate_import_id");
						continue;
					}
				}
			}

			if (is_file("$config[project_path]/admin/plugins/grabbers/grabbers.php"))
			{
				require_once "$config[project_path]/admin/plugins/grabbers/grabbers.php";
				$grabber_gunction = "grabbersFindGrabber";
				if (function_exists($grabber_gunction))
				{
					log_import("Grabbing gallery $insert_data[gallery_url]...");
					$value_gallery_grabber = $grabber_gunction($insert_data['gallery_url'], 'albums');
					if ($value_gallery_grabber instanceof KvsGrabberAlbum)
					{
						log_import("Using grabber " . $value_gallery_grabber->get_grabber_name());
						$value_gallery_grabber_settings = $value_gallery_grabber->get_settings();
						$value_gallery_grabber_album_info = $value_gallery_grabber->grab_album_data($insert_data['gallery_url'], "$config[temporary_path]");

						if ($value_gallery_grabber_settings->get_content_source_id() > 0)
						{
							if (intval($insert_data['content_source_id']) == 0)
							{
								$insert_data['content_source_id'] = $value_gallery_grabber_settings->get_content_source_id();
							}
						}

						if ($value_gallery_grabber_album_info->get_error_code() == 0)
						{
							$quantity_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0 || $value_gallery_grabber_settings->get_filter_quantity_to() > 0)
							{
								$quantity_filter_ok_from = true;
								$quantity_filter_ok_to = true;
								if ($value_gallery_grabber_settings->get_filter_quantity_from() > 0)
								{
									if (count($value_gallery_grabber_album_info->get_image_files()) < $value_gallery_grabber_settings->get_filter_quantity_from())
									{
										$quantity_filter_ok_from = false;
									}
								}
								if ($value_gallery_grabber_settings->get_filter_quantity_to() > 0)
								{
									if (count($value_gallery_grabber_album_info->get_image_files()) > $value_gallery_grabber_settings->get_filter_quantity_to())
									{
										$quantity_filter_ok_to = false;
									}
								}
								$quantity_filter_ok = $quantity_filter_ok_from && $quantity_filter_ok_to;
							}

							$rating_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_rating_from() > 0 || $value_gallery_grabber_settings->get_filter_rating_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_rating())
								{
									$rating_filter_ok_from = true;
									$rating_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_rating_from() > 0)
									{
										if ($value_gallery_grabber_album_info->get_rating() < $value_gallery_grabber_settings->get_filter_rating_from())
										{
											$rating_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_rating_to() > 0)
									{
										if ($value_gallery_grabber_album_info->get_rating() > $value_gallery_grabber_settings->get_filter_rating_to())
										{
											$rating_filter_ok_to = false;
										}
									}
									$rating_filter_ok = $rating_filter_ok_from && $rating_filter_ok_to;
								}
							}

							$views_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_views_from() > 0 || $value_gallery_grabber_settings->get_filter_views_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_views())
								{
									$views_filter_ok_from = true;
									$views_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_views_from() > 0)
									{
										if ($value_gallery_grabber_album_info->get_views() < $value_gallery_grabber_settings->get_filter_views_from())
										{
											$views_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_views_to() > 0)
									{
										if ($value_gallery_grabber_album_info->get_views() > $value_gallery_grabber_settings->get_filter_views_to())
										{
											$views_filter_ok_to = false;
										}
									}
									$views_filter_ok = $views_filter_ok_from && $views_filter_ok_to;
								}
							}

							$date_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_date_from() > 0 || $value_gallery_grabber_settings->get_filter_date_to() > 0)
							{
								if ($value_gallery_grabber->can_grab_date() && $value_gallery_grabber_album_info->get_date() > 0)
								{
									$date_filter_value = floor((time() - $value_gallery_grabber_album_info->get_date()) / 86400);
									$date_filter_ok_from = true;
									$date_filter_ok_to = true;
									if ($value_gallery_grabber_settings->get_filter_date_from() > 0)
									{
										if ($date_filter_value < $value_gallery_grabber_settings->get_filter_date_from())
										{
											$date_filter_ok_from = false;
										}
									}
									if ($value_gallery_grabber_settings->get_filter_date_to() > 0)
									{
										if ($date_filter_value > $value_gallery_grabber_settings->get_filter_date_to())
										{
											$date_filter_ok_to = false;
										}
									}
									$date_filter_ok = $date_filter_ok_from && $date_filter_ok_to;
								}
							}

							$terminology_filter_ok = true;
							if ($value_gallery_grabber_settings->get_filter_terminology())
							{
								$terminology_filter_value = array_map('trim', explode(',', mb_lowercase($value_gallery_grabber_settings->get_filter_terminology())));
								$terminology_filter_title = mb_lowercase($value_gallery_grabber_album_info->get_title());

								unset($terminology_filter_words_in_title);
								preg_match_all('/([\p{N}\p{L}-_#@]+)/u', $terminology_filter_title, $terminology_filter_words_in_title);

								foreach ($terminology_filter_words_in_title[0] as $word)
								{
									if (in_array($word, $terminology_filter_value))
									{
										$terminology_filter_ok = false;
										break;
									}
								}

								if ($terminology_filter_ok)
								{
									foreach ($terminology_filter_value as $word)
									{
										if (strpos($word, ' ') && strpos($terminology_filter_title, $word))
										{
											$terminology_filter_ok = false;
											break;
										}
									}
								}
							}

							if ($quantity_filter_ok && $rating_filter_ok && $views_filter_ok && $date_filter_ok && $terminology_filter_ok)
							{
								switch ($value_gallery_grabber_settings->get_mode())
								{
									case KvsGrabberSettings::GRAB_MODE_DOWNLOAD:
										$value_images_referer = $value_gallery_grabber_album_info->get_canonical();
										$grabber_image_files = $value_gallery_grabber_album_info->get_image_files();
										if (count($grabber_image_files) == 0 || strpos($grabber_image_files[0], '/get_image/') !== false)
										{
											log_import("ERROR: grabber was not able to grab image files");
											continue 2;
										} else
										{
											$value_images_list = $grabber_image_files;
											$value_images_zip = '';
										}
										break;
									case KvsGrabberSettings::GRAB_MODE_EMBED:
									case KvsGrabberSettings::GRAB_MODE_PSEUDO:
										log_import("ERROR: albums grabber does not support embed codes or pseudo albums");
										continue 2;
										break;
								}

								if ($value_gallery_grabber_album_info->get_canonical())
								{
									$insert_data['external_key'] = md5($value_gallery_grabber_album_info->get_canonical());
								}

								flock($global_lock, LOCK_EX);
								foreach ($value_gallery_grabber_settings->get_data() as $grabber_settings_data_item)
								{
									switch ($grabber_settings_data_item)
									{
										case KvsGrabberSettings::DATA_FIELD_TITLE:
											if (strlen($insert_data['title']) == 0)
											{
												$insert_data['title'] = $value_gallery_grabber_album_info->get_title();
												if ($title_limit > 0)
												{
													$insert_data['title'] = truncate_text($insert_data['title'], $title_limit, $title_limit_type_id);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_DESCRIPTION:
											if (strlen($insert_data['description']) == 0)
											{
												$insert_data['description'] = $value_gallery_grabber_album_info->get_description();
												if ($description_limit > 0)
												{
													$insert_data['description'] = truncate_text($insert_data['description'], $description_limit, $description_limit_type_id);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_DATE:
											if (strlen($insert_data['post_date']) == 0 && $value_gallery_grabber_album_info->get_date() > 0)
											{
												$insert_data['post_date'] = date("Y-m-d", $value_gallery_grabber_album_info->get_date());
												if ($is_post_time_randomization == 1)
												{
													$insert_data['post_date'] = date("Y-m-d H:i:s", strtotime($insert_data['post_date']) + mt_rand($post_time_from, $post_time_to));
												} else
												{
													$insert_data['post_date'] = date("Y-m-d", $value_gallery_grabber_album_info->get_date()) . date(" H:i:s");
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_RATING:
											if (intval($insert_data['rating']) < 1)
											{
												$insert_data['rating_amount'] = max(1, intval($value_gallery_grabber_album_info->get_votes()));
												$insert_data['rating'] = $value_gallery_grabber_album_info->get_rating() / 100 * 5;
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_VIEWS:
											if (intval($insert_data['album_viewed']) < 1)
											{
												$insert_data['album_viewed'] = intval($value_gallery_grabber_album_info->get_views());
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CUSTOM:
											for ($i = 1; $i <= 3; $i++)
											{
												if ($value_gallery_grabber_album_info->get_custom_field($i))
												{
													$insert_data["custom$i"] = $value_gallery_grabber_album_info->get_custom_field($i);
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CATEGORIES:
											if (count($category_ids) == 0)
											{
												$value_temp = $value_gallery_grabber_album_info->get_categories();
												foreach ($value_temp as $cat_title)
												{
													if ($cat_title == '')
													{
														continue;
													}

													if ($categories_all[mb_lowercase($cat_title)] > 0)
													{
														$cat_id = $categories_all[mb_lowercase($cat_title)];
													} else
													{
														$cat_id = mr2number(sql_pr("select category_id from $config[tables_prefix]categories where title=?", $cat_title));
														if ($cat_id == 0)
														{
															foreach ($categories_regexp as $regexp => $category_id)
															{
																$regexp = str_replace('\*', '\w*', preg_quote($regexp, "/"));
																if (preg_match("/^$regexp$/iu", $cat_title))
																{
																	$cat_id = $category_id;
																	break;
																}
															}
														}
														if ($cat_id == 0 && !$is_skip_new_categories)
														{
															$cat_dir = get_correct_dir_name($cat_title);
															$temp_dir = $cat_dir;
															for ($it = 2; $it < 999999; $it++)
															{
																if (mr2number(sql_pr("select count(*) from $config[tables_prefix]categories where dir=?", $temp_dir)) == 0)
																{
																	$cat_dir = $temp_dir;
																	break;
																}
																$temp_dir = $cat_dir . $it;
															}
															$cat_id = sql_insert("insert into $config[tables_prefix]categories set title=?, dir=?, added_date=?", $cat_title, $cat_dir, date("Y-m-d H:i:s"));
															sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=6, added_date=?", $admin_id, $admin_username, $cat_id, date("Y-m-d H:i:s"));
														}
														if ($cat_id > 0)
														{
															$categories_all[mb_lowercase($cat_title)] = $cat_id;
														}
													}
													if ($cat_id > 0)
													{
														$category_ids[] = $cat_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_MODELS:
											if (count($model_ids) == 0)
											{
												$value_temp = $value_gallery_grabber_album_info->get_models();
												foreach ($value_temp as $model_title)
												{
													$model_title = trim($model_title);
													if ($model_title == '')
													{
														continue;
													}

													if ($models_all[mb_lowercase($model_title)] > 0)
													{
														$model_id = $models_all[mb_lowercase($model_title)];
													} else
													{
														$model_id = mr2number(sql_pr("select model_id from $config[tables_prefix]models where title=?", $model_title));
														if ($model_id == 0 && !$is_skip_new_models)
														{
															$model_dir = get_correct_dir_name($model_title);
															$temp_dir = $model_dir;
															for ($it = 2; $it < 999999; $it++)
															{
																if (mr2number(sql_pr("select count(*) from $config[tables_prefix]models where dir=?", $temp_dir)) == 0)
																{
																	$model_dir = $temp_dir;
																	break;
																}
																$temp_dir = $model_dir . $it;
															}
															$model_id = sql_insert("insert into $config[tables_prefix]models set title=?, dir=?, rating_amount=1, added_date=?", $model_title, $model_dir, date("Y-m-d H:i:s"));
															sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=4, added_date=?", $admin_id, $admin_username, $model_id, date("Y-m-d H:i:s"));
														}
														if ($model_id > 0)
														{
															$models_all[mb_lowercase($model_title)] = $model_id;
														}
													}
													if ($model_id > 0)
													{
														$model_ids[] = $model_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_TAGS:
											if (count($tag_ids) == 0)
											{
												$inserted_tags = array();
												$value_temp = $value_gallery_grabber_album_info->get_tags();
												foreach ($value_temp as $tag_title)
												{
													$tag_title = trim($tag_title);
													if ($tag_title == '')
													{
														continue;
													}
													if (in_array(mb_lowercase($tag_title), $inserted_tags))
													{
														continue;
													}

													$tag_id = find_or_create_tag($tag_title, $options);
													if ($tag_id > 0)
													{
														$inserted_tags[] = mb_lowercase($tag_title);
														$tag_ids[] = $tag_id;
													}
												}
											}
											break;
										case KvsGrabberSettings::DATA_FIELD_CONTENT_SOURCE:
											if ($insert_data['content_source_id'] == 0 && $value_gallery_grabber_album_info->get_content_source() != '')
											{
												$insert_data['content_source_id'] = mr2number(sql_pr("select content_source_id from $config[tables_prefix]content_sources where title=?", $value_gallery_grabber_album_info->get_content_source()));
												if ($insert_data['content_source_id'] == 0 && !$is_skip_new_content_sources)
												{
													$cs_dir = get_correct_dir_name($value_gallery_grabber_album_info->get_content_source());
													$temp_dir = $cs_dir;
													for ($it = 2; $it < 999999; $it++)
													{
														if (mr2number(sql_pr("select count(*) from $config[tables_prefix]content_sources where dir=?", $temp_dir)) == 0)
														{
															$cs_dir = $temp_dir;
															break;
														}
														$temp_dir = $cs_dir . $it;
													}
													$insert_data['content_source_id'] = sql_insert("insert into $config[tables_prefix]content_sources set title=?, dir=?, rating_amount=1, added_date=?", $value_gallery_grabber_album_info->get_content_source(), $cs_dir, date("Y-m-d H:i:s"));
													sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=3, added_date=?", $admin_id, $admin_username, $insert_data['content_source_id'], date("Y-m-d H:i:s"));
												}
											}
											break;
									}
								}
								flock($global_lock, LOCK_UN);
								log_import("Done");

								if ($is_skip_duplicate_titles == 1)
								{
									if ($insert_data['title']!='')
									{
										$duplicate_album_id = mr2number(sql_pr("select album_id from $config[tables_prefix]albums where title=? limit 1", $insert_data['title']));
										if ($duplicate_album_id > 0)
										{
											log_import("ERROR: duplicate title, already added into album $duplicate_album_id");
											continue;
										}
									}
								}
							} else
							{
								if (!$quantity_filter_ok)
								{
									log_import("WARNING: album with " . count($value_gallery_grabber_album_info->get_image_files()) . " images will be skipped");
								} elseif (!$rating_filter_ok)
								{
									log_import("WARNING: album with rating " . $value_gallery_grabber_album_info->get_rating() . "% will be skipped");
								} elseif (!$views_filter_ok)
								{
									log_import("WARNING: album with " . $value_gallery_grabber_album_info->get_views() . " views will be skipped");
								} elseif (!$date_filter_ok)
								{
									log_import("WARNING: album with date " . date("Y-m-d", $value_gallery_grabber_album_info->get_date()) . " will be skipped");
								} elseif (!$terminology_filter_ok)
								{
									log_import("WARNING: album with title \"" . $value_gallery_grabber_album_info->get_title() . "\" will be skipped");
								}
								continue;
							}
						} else
						{
							if ($value_gallery_grabber->get_log())
							{
								log_import("\n" . $value_gallery_grabber->get_log());
							}
							switch ($value_gallery_grabber_album_info->get_error_code())
							{
								case KvsGrabberAlbumInfo::ERROR_CODE_PAGE_UNAVAILABLE:
									log_import("ERROR: album page is not available");
									break;
								case KvsGrabberAlbumInfo::ERROR_CODE_PAGE_ERROR:
									log_import("ERROR: album page gives error: " . $value_gallery_grabber_album_info->get_error_message());
									break;
								case KvsGrabberAlbumInfo::ERROR_CODE_PARSING_ERROR:
									log_import("ERROR: grabber was not able to parse album page");
									break;
								case KvsGrabberAlbumInfo::ERROR_CODE_UNEXPECTED_ERROR:
									log_import("ERROR: album page gives unexpected error: " . $value_gallery_grabber_album_info->get_error_message());
									break;
							}
							continue;
						}
					} else
					{
						log_import("ERROR: no grabber found for " . str_replace('www.', '', parse_url($insert_data['gallery_url'], PHP_URL_HOST)));
						continue;
					}
				}
			}
		}

		flock($global_lock, LOCK_UN);

		if ($insert_data['external_key'] == '')
		{
			if ($value_images_zip <> '')
			{
				$insert_data['external_key'] = md5($value_images_zip);
			} elseif (is_array($value_images_list) && count($value_images_list) > 0)
			{
				$insert_data['external_key'] = md5(trim($value_images_list[0]));
			}
		}

		if (!isset($insert_data['post_date']))
		{
			if ($is_post_date_randomization==1)
			{
				if ($post_date_randomization_option==0)
				{
					$post_date_randomization_from=intval($_POST["post_date_randomization_from_Year"])."-".intval($_POST["post_date_randomization_from_Month"])."-".intval($_POST["post_date_randomization_from_Day"]);
					$post_date_randomization_to=intval($_POST["post_date_randomization_to_Year"])."-".intval($_POST["post_date_randomization_to_Month"])."-".intval($_POST["post_date_randomization_to_Day"]);

					$days=ceil((strtotime($post_date_randomization_to)-strtotime($post_date_randomization_from))/86400);
					$insert_data['post_date']=date("Y-m-d",mktime(0,0,0,intval($_POST["post_date_randomization_from_Month"]),intval($_POST["post_date_randomization_from_Day"])+mt_rand(0,$days),intval($_POST["post_date_randomization_from_Year"])));
					if ($is_post_time_randomization==1)
					{
						$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
					}
				} else {
					$post_date_randomization_from=intval($_POST["relative_post_date_randomization_from"]);
					$post_date_randomization_to=intval($_POST["relative_post_date_randomization_to"]);
					$relative_post_date=intval(mt_rand($post_date_randomization_from,$post_date_randomization_to));
					if ($relative_post_date==0)
					{
						for ($i=0;$i<9999;$i++)
						{
							$relative_post_date=intval(mt_rand($post_date_randomization_from,$post_date_randomization_to));
							if ($relative_post_date<>0)
							{
								break;
							}
						}
					}
					if ($relative_post_date<>0)
					{
						$insert_data['post_date']='1971-01-01 00:00:00';
						$insert_data['relative_post_date']=$relative_post_date;
					} else {
						$insert_data['post_date']=date("Y-m-d H:i:s");
					}
				}
			} elseif ($is_post_date_randomization_days==1)
			{
				$days=intval($_POST['post_date_randomization_days'])-1;
				$insert_data['post_date']=date("Y-m-d",mktime(0,0,0,date("m"),intval(date("d"))+mt_rand(0,$days),date("Y")));
				if ($is_post_time_randomization==1)
				{
					$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
				}
			} else {
				$insert_data['post_date']=date("Y-m-d");
				if ($is_post_time_randomization==1)
				{
					$insert_data['post_date']=date("Y-m-d H:i:s",strtotime($insert_data['post_date'])+mt_rand($post_time_from,$post_time_to));
				} elseif ($value_gallery_grabber)
				{
					$insert_data['post_date'] .= date(" H:i:s");
				}
			}
		}

		if (intval($insert_data['user_id'])<1)
		{
			if (count($users_ids)>0)
			{
				$idx=mt_rand(1,count($users_ids))-1;
				$insert_data['user_id']=$users_ids[$idx];
			} else {
				$insert_data['user_id']=mr2number(sql_pr("select user_id from $config[tables_prefix]users where username=?",$options['DEFAULT_USER_IN_ADMIN_ADD_ALBUM']));
			}
		}

		if ($global_content_source_id>0)
		{
			$insert_data['content_source_id']=$global_content_source_id;
		}

		if (isset($insert_data['content_source_id']))
		{
			if (intval($_POST['content_source_categories_id'])==1)
			{
				if (count($category_ids)==0)
				{
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_content_sources where content_source_id=?",$insert_data['content_source_id']));
				}
			} elseif (intval($_POST['content_source_categories_id'])==2)
			{
				$category_ids=array_merge($category_ids,mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_content_sources where content_source_id=?",$insert_data['content_source_id'])));
			}
		}
		if (count($model_ids)>0)
		{
			if (intval($_POST['model_categories_id'])==1)
			{
				if (count($category_ids)==0)
				{
					$category_ids=mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_models where model_id in (".implode(',',$model_ids).")"));
				}
			} elseif (intval($_POST['model_categories_id'])==2)
			{
				$category_ids=array_merge($category_ids,mr2array_list(sql_pr("select category_id from $config[tables_prefix]categories_models where model_id in (".implode(',',$model_ids).")")));
			}
		}

		$download_temp_folder = $config['temporary_path'] . "/import_" . $import_id . mt_rand(10000000, 99999999);
		for ($i = 0; $i < 100; $i++)
		{
			if (is_dir($download_temp_folder))
			{
				$download_temp_folder = $config['temporary_path'] . "/import_" . $import_id . mt_rand(10000000, 99999999);
			} else {
				break;
			}
		}
		if (!mkdir_recursive($download_temp_folder))
		{
			log_import("ERROR: failed to create temp directory: $download_temp_folder");
			continue;
		}

		$has_download_issue = false;

		if ($value_images_zip)
		{
			$download_path = "$download_temp_folder/source.zip";
			import_download_file($value_images_zip, $download_path, $is_use_rename_as_copy, true);

			$zip = new PclZip($download_path);
			if (!is_array($zip->properties()))
			{
				$downloaded_filesize = filesize($download_path);
				log_import("ERROR: invalid images ZIP after download: $value_images_zip ($downloaded_filesize bytes)");
				$has_download_issue = true;
			}
		} elseif (is_array($value_images_list))
		{
			if (!mkdir_recursive("$download_temp_folder/temp"))
			{
				log_import("ERROR: failed to create temp directory: $download_temp_folder/temp");
				continue;
			}

			$zip_files_to_add = [];
			$zip_index = 1;
			foreach ($value_images_list as $image_url)
			{
				$image_url = trim($image_url);
				if ($image_url == '')
				{
					continue;
				}

				$download_path = "$download_temp_folder/temp/$zip_index.jpg";
				import_download_file($image_url, $download_path, $is_use_rename_as_copy, true, 20, $value_images_referer);

				$img_size = getimagesize($download_path);
				if ($img_size[0] > 0 && $img_size[1] > 0)
				{
					$zip_files_to_add[] = $download_path;
					$zip_index++;
				} else
				{
					log_import("ERROR: failed to download source image: $image_url");
					$has_download_issue = true;
					break;
				}
			}
			if (!$has_download_issue)
			{
				$zip = new PclZip("$download_temp_folder/source.zip");
				$zip->create($zip_files_to_add, $p_add_dir = "", $p_remove_dir = "$download_temp_folder/temp");
			}
			rmdir_recursive("$download_temp_folder/temp");
		}

		if (!$has_download_issue && $value_image_preview)
		{
			$download_path = "$download_temp_folder/preview.jpg";
			import_download_file($value_image_preview, $download_path, $is_use_rename_as_copy, true, 20);

			$img_size = getimagesize($download_path);
			if ($img_size[0] == 0 || $img_size[1] == 0)
			{
				$downloaded_filesize = filesize($download_path);
				log_import("ERROR: invalid preview image after download: $value_image_preview ($downloaded_filesize bytes)");
				$has_download_issue = true;
			}
		}

		if ($has_download_issue)
		{
			rmdir_recursive($download_temp_folder);
			continue;
		}

		flock($global_lock, LOCK_EX);

		if ($is_skip_duplicate_urls == 1)
		{
			if ($insert_data['gallery_url'])
			{
				$duplicate_album_id = mr2number(sql_pr("select album_id from $config[tables_prefix]albums where gallery_url=? limit 1", $insert_data['gallery_url']));
				if ($duplicate_album_id > 0)
				{
					flock($global_lock, LOCK_UN);
					log_import("ERROR: duplicate gallery, already added into album $duplicate_album_id");
					rmdir_recursive($download_temp_folder);
					continue;
				}
			}
			if ($insert_data['external_key'])
			{
				$duplicate_album_id = mr2number(sql_pr("select album_id from $config[tables_prefix]albums where external_key=? limit 1", $insert_data['external_key']));
				if ($duplicate_album_id > 0)
				{
					flock($global_lock, LOCK_UN);
					log_import("ERROR: duplicate external key, already added into album $duplicate_album_id");
					rmdir_recursive($download_temp_folder);
					continue;
				}
			}
		}
		if ($is_skip_duplicate_titles == 1)
		{
			if ($insert_data['title'])
			{
				$duplicate_album_id = mr2number(sql_pr("select album_id from $config[tables_prefix]albums where title=? limit 1", $insert_data['title']));
				if ($duplicate_album_id > 0)
				{
					flock($global_lock, LOCK_UN);
					log_import("ERROR: duplicate title, already added into album $duplicate_album_id");
					rmdir_recursive($download_temp_folder);
					continue;
				}
			}
		}

		if (intval($insert_data['rating_amount']) < 1)
		{
			$insert_data['rating_amount'] = 1;
		}
		if (floatval($insert_data['rating']) < 0.1)
		{
			$insert_data['rating'] = intval($options['ALBUM_INITIAL_RATING']);
			$insert_data['rating_amount'] = 1;
		}
		$insert_data['rating'] = intval($insert_data['rating'] * $insert_data['rating_amount']);

		$insert_data['last_time_view_date'] = date("Y-m-d H:i:s");
		$insert_data['admin_user_id'] = $admin_id;

		if (intval($is_review_needed) == 1)
		{
			$insert_data['is_review_needed'] = 1;
		}

		if ($is_make_directories == 1 && $insert_data['dir'] == '' && $insert_data['title'])
		{
			$dir = get_correct_dir_name($insert_data['title']);
			$temp_dir = $dir;
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $table_name where dir=?", $temp_dir)) == 0)
				{
					$dir = $temp_dir;
					break;
				}
				$temp_dir = $dir . $i;
			}
			$insert_data['dir'] = $dir;
		} elseif ($is_make_directories == 1 && $insert_data['dir'])
		{
			$dir = $insert_data['dir'];
			$temp_dir = $dir;
			for ($i = 2; $i < 999999; $i++)
			{
				if (mr2number(sql_pr("select count(*) from $table_name where dir=?", $temp_dir)) == 0)
				{
					$dir = $temp_dir;
					break;
				}
				$temp_dir = $dir . $i;
			}
			$insert_data['dir'] = $dir;
		}

		if ($is_make_directories == 1)
		{
			foreach ($languages as $language)
			{
				if ($insert_data["title_$language[code]"] && $insert_data["dir_$language[code]"] == '')
				{
					$dir = get_correct_dir_name($insert_data["title_$language[code]"], $language);
					$temp_dir = $dir;
					for ($it = 2; $it < 99999; $it++)
					{
						if (mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where dir_$language[code]=?", $temp_dir)) == 0)
						{
							$dir = $temp_dir;
							break;
						}
						$temp_dir = $dir . $it;
					}
					$insert_data["dir_$language[code]"] = $dir;
				}
			}
		}
		flock($global_lock, LOCK_UN);

		$insert_data['added_date'] = date("Y-m-d H:i:s");

		$item_id = sql_insert("insert into $table_name set ?%, status_id=3", $insert_data);
		if ($item_id == 0)
		{
			log_import("ERROR: failed to insert album info into database");
			rmdir_recursive($download_temp_folder);
			continue;
		}

		$tag_ids = array_unique($tag_ids);
		foreach ($tag_ids as $tag_id)
		{
			sql_pr("insert into $config[tables_prefix]tags_albums set tag_id=?, album_id=?", $tag_id, $item_id);
		}
		$category_ids = array_unique($category_ids);
		foreach ($category_ids as $category_id)
		{
			sql_pr("insert into $config[tables_prefix]categories_albums set category_id=?, album_id=?", $category_id, $item_id);
		}
		$model_ids = array_unique($model_ids);
		foreach ($model_ids as $model_id)
		{
			sql_pr("insert into $config[tables_prefix]models_albums set model_id=?, album_id=?", $model_id, $item_id);
		}

		$background_task = [];
		$background_task['status_id'] = intval($value_status_id);
		$background_task['source_file'] = "source.zip";
		if (intval($value_main_image_number) > 1)
		{
			$background_task['image_main'] = intval($value_main_image_number);
		}
		if (intval($value_server_group_id) > 0)
		{
			$background_task['server_group_id'] = intval($value_server_group_id);
		}

		$dir_path = get_dir_by_id($item_id);
		if (!mkdir_recursive("$config[content_path_albums_sources]/$dir_path/$item_id"))
		{
			log_video("ERROR  Failed to create directory: $config[content_path_albums_sources]/$dir_path/$item_id", $item_id);
		}

		if (is_file("$download_temp_folder/source.zip"))
		{
			if (!rename("$download_temp_folder/source.zip", "$config[content_path_albums_sources]/$dir_path/$item_id/source.zip") || filesize("$config[content_path_albums_sources]/$dir_path/$item_id/source.zip") == 0)
			{
				log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/source.zip", $item_id);
			}
		}
		if (is_file("$download_temp_folder/preview.jpg"))
		{
			if (!rename("$download_temp_folder/preview.jpg", "$config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg") || filesize("$config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg") == 0)
			{
				log_album("ERROR  Failed to move file to directory: $config[content_path_albums_sources]/$dir_path/$item_id/preview.jpg", $item_id);
			}
		}
		rmdir_recursive("$download_temp_folder");

		$background_task['import_data'] = $line['data'];

		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, type_id=10, album_id=?, data=?, added_date=?", $item_id, serialize($background_task), date("Y-m-d H:i:s"));
		sql_pr("insert into $config[tables_prefix]users_events set event_type_id=2, user_id=?, album_id=?, added_date=?", $insert_data['user_id'], $item_id, $insert_data['post_date']);
		sql_pr("insert into $config[tables_prefix]admin_audit_log set user_id=?, username=?, action_id=110, object_id=?, object_type_id=2, added_date=?", $admin_id, $admin_username, $item_id, date("Y-m-d H:i:s"));

		if ($value_gallery_grabber instanceof KvsGrabber)
		{
			$value_gallery_grabber->post_process_inserted_object($item_id, $insert_data['gallery_url']);
		}

		log_import("Imported album $item_id");

		sql_pr("update $config[tables_prefix]background_imports_data set object_id=? where import_id=? and line_id=?", $item_id, $import_id, $line['line_id']);
		usleep(50000);
	}

	sql_pr("update $config[tables_prefix]background_imports_data set status_id=1 where import_id=? and thread_id=?", $import_id, $background_thread_id);

	flock($lock, LOCK_UN);
	fclose($lock);
	unlink($lock_file);

	flock($global_lock, LOCK_UN);
	fclose($global_lock);

	log_import("Finished");
}

function log_import($message)
{
	global $background_thread_id;

	if ($background_thread_id > 0)
	{
		$background_thread_id_str = "$background_thread_id";
		if ($background_thread_id < 10)
		{
			$background_thread_id_str = " $background_thread_id";
		}
		echo "[Thread $background_thread_id_str] " . date("[Y-m-d H:i:s] ") . $message . "\n";
	} else
	{
		echo date("[Y-m-d H:i:s] ") . $message . "\n";
	}
}

function import_download_file($url, $path, $is_use_rename_as_copy, $is_log_download_info = false, $download_timeout = 0, $download_referer = '')
{
	if (strpos($url, '/') === 0)
	{
		if ($is_use_rename_as_copy == 1)
		{
			if (!rename($url, $path))
			{
				copy($url, $path);
			}
		} else
		{
			copy($url, $path);
		}
		$downloaded_filesize = sprintf("%.0f", filesize($path));
	} else
	{
		if ($is_log_download_info)
		{
			log_import("Downloading image file $url...");
		}
		save_file_from_url($url, $path, $download_referer, $download_timeout);

		$downloaded_filesize = sprintf("%.0f", filesize($path));
		if ($is_log_download_info)
		{
			log_import("Done ($downloaded_filesize bytes)");
		}
	}
	return $downloaded_filesize;
}