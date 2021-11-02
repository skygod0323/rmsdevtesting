<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once 'include/setup.php';
require_once 'include/setup_smarty.php';
require_once 'include/functions_base.php';
require_once 'include/functions_servers.php';
require_once 'include/functions.php';
require_once 'include/check_access.php';
require_once 'include/pclzip.lib.php';

$secret_remote_key = $config['cv'];
if ($config['cvr'])
{
	$secret_remote_key = $config['cvr'];
}

$options=get_options();

$errors = null;

$latest_api_version=$options['SYSTEM_STORAGE_API_VERSION'];

if (isset($_GET['reset_filter']) || isset($_GET['no_filter']))
{
	$_SESSION['save'][$page_name]['se_text']='';
}

if (!isset($_GET['reset_filter']))
{
	if (isset($_GET['se_text'])) {$_SESSION['save'][$page_name]['se_text']=$_GET['se_text'];}
}

if ($_REQUEST['action'] == 'download_api')
{
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="remote_control.php"');
	$api_file = trim(file_get_contents("$config[project_path]/admin/tools/remote_control.php"));
	$api_file = preg_replace("|[\$]config\[['\"]cv['\"]\][ ]*=[ ]*['\"][^'\"]+['\"];|is", "\$config['cv']=\"{$secret_remote_key}\";", $api_file);
	echo $api_file;
	die;
}
if ($_REQUEST['action'] == 'download_api_cdn')
{
	header('Content-Type: text/plain');
	header('Content-Disposition: attachment; filename="cdnapi.php"');
	echo trim(file_get_contents("$config[project_path]/admin/tools/cdnapi.php"));
	die;
}

if ($_REQUEST['action'] == 'view_debug_log' && intval($_REQUEST['id']) > 0)
{
	$id = intval($_REQUEST['id']);
	$log_file = "debug_storage_server_$id.txt";
	$log_path = "$config[project_path]/admin/logs/$log_file";

	header("Content-Type: text/plain; charset=utf8");
	if (intval($_REQUEST['conversion_id']) > 0)
	{
		$rnd = mt_rand(10000000, 99999999);
		$log_path = "$config[temporary_path]/$rnd/$log_file";

		$conversion_server = mr2array_single(sql_pr("select * from $config[tables_prefix]admin_conversion_servers where server_id=?", intval($_REQUEST['conversion_id'])));
		if ($conversion_server)
		{
			if (mkdir_recursive("$config[temporary_path]/$rnd") && check_file($log_file, '', $conversion_server) > 0)
			{
				get_file($log_file, '', "$config[temporary_path]/$rnd", $conversion_server);
			}
		} else
		{
			echo "No conversion server with ID: $_REQUEST[conversion_id]";
		}
	}
	if (is_file($log_path))
	{
		$log_size = sprintf("%.0f", filesize($log_path));
		if ($log_size > 1024 * 1024 && !isset($_REQUEST['download']))
		{
			$fh = fopen($log_path, "r");
			fseek($fh, $log_size - 1024 * 1024);
			header("Content-Length: " . (1024 * 1024 + 29));
			echo "Showing last 1MB of file...\n\n";
			echo fread($fh, 1024 * 1024 + 1);
		} else
		{
			if (isset($_REQUEST['download']))
			{
				header("Content-Disposition: attachment; filename=\"$log_file\"");
			}
			header("Content-Length: $log_size");
			readfile($log_path);
		}
	}
	die;
}

if (in_array($_POST['action'],array('add_new_group_complete','change_group_complete')))
{
	if ($_POST['action']=='add_new_group_complete') {$_POST['action']="add_new_complete";} else {$_POST['action']="change_complete";}
	$table_name="$config[tables_prefix]admin_servers_groups";
	$table_key_name="group_id";

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$item_id=intval($_REQUEST['item_id']);

	validate_field('uniq',$_POST['title'],$lang['settings']['server_group_field_title'],array('field_name_in_base'=>'title'));

	$servers=mr2array(sql("select * from $config[tables_prefix]admin_servers where group_id=$item_id"));
	if (count($servers)>1)
	{
		$has_weight_error=0;
		$has_countries_error=1;
		$has_status_error=1;
		foreach ($servers as $server)
		{
			$server_id=$server['server_id'];
			if (trim(intval(trim($_POST["weight_$server_id"])))<>trim($_POST["weight_$server_id"]) && $has_weight_error==0)
			{
				$errors[]=get_aa_error('server_group_sub_field_integer',$lang['settings']['server_group_servers_weight']);
				$has_weight_error=1;
			}
			if (intval($_POST["status_id_$server_id"])==1)
			{
				$has_status_error=0;
			}
			if ($_POST["countries_$server_id"]=='' && intval($_POST["status_id_$server_id"])==1)
			{
				$has_countries_error=0;
			}
		}
		if ($has_status_error==1)
		{
			$errors[]=get_aa_error('server_group_sub_field_status',$lang['settings']['server_group_servers_status']);
		} elseif ($has_countries_error==1)
		{
			$errors[]=get_aa_error('server_group_sub_field_countries',$lang['settings']['server_group_servers_countries']);
		}
	}

	if (!is_array($errors))
	{
		if ($_POST['action']=='add_new_complete')
		{
			sql_pr("insert into $table_name set title=?, content_type_id=?, status_id=?, added_date=?",$_POST['title'],intval($_POST['content_type_id']),intval($_POST['status_id']),date("Y-m-d H:i:s"));
			$_SESSION['messages'][]=$lang['common']['success_message_added'];
		} else {
			sql_pr("update $table_name set title=?, status_id=? where $table_key_name=?",$_POST['title'],intval($_POST['status_id']),$item_id);

			if (count($servers)>1)
			{
				foreach ($servers as $server)
				{
					$server_id=$server['server_id'];
					sql_pr("update $config[tables_prefix]admin_servers set status_id=?, lb_weight=?, lb_countries=? where server_id=?",intval($_POST["status_id_$server_id"]),intval($_POST["weight_$server_id"]),$_POST["countries_$server_id"],$server_id);
				}
			}
			$_SESSION['messages'][]=$lang['common']['success_message_modified'];
			update_cluster_data();
		}

		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if (in_array($_POST['action'],array('add_new_complete','change_complete')))
{
	$table_name="$config[tables_prefix]admin_servers";
	$table_key_name="server_id";

	foreach ($_POST as $post_field_name => $post_field_value)
	{
		if (!is_array($post_field_value))
		{
			$_POST[$post_field_name] = trim($post_field_value);
		}
	}

	$_POST['path']=rtrim($_POST['path'],"/");

	$item_id=intval($_REQUEST['item_id']);
	if ($_POST['action']=='change_complete' && $_POST['ftp_pass']=='' && intval($_POST['connection_type_id'])==2)
	{
		$_POST['ftp_pass']=mr2string(sql("select ftp_pass from $config[tables_prefix]admin_servers where $table_key_name=$item_id and connection_type_id=2"));
	}

	$group_id=intval($_POST['group_id']);
	if ($group_id==0)
	{
		$group_id=mr2number(sql_pr("select group_id from $table_name where $table_key_name=?",$item_id));
		$_POST['group_id']=$group_id;
	}
	$content_type_id=mr2number(sql_pr("select content_type_id from $config[tables_prefix]admin_servers_groups where group_id=?",$group_id));

	validate_field('uniq',$_POST['title'],$lang['settings']['server_field_title'],array('field_name_in_base'=>'title'));
	validate_field('file_separator',$_POST['title'],$lang['settings']['server_field_title']);
	if ($_POST['action']=='add_new_complete')
	{
		validate_field('empty',$_POST['group_id'],$lang['settings']['server_field_group']);
	}
	if (validate_field('empty',$_POST['urls'],$lang['settings']['server_field_urls']))
	{
		if (validate_field('file_separator',$_POST['urls'],$lang['settings']['server_field_urls']))
		{
			validate_field('url',$_POST['urls'],$lang['settings']['server_field_urls']);
		}
	}
	if ($_POST['streaming_type_id']==4)
	{
		if (validate_field('empty',$_POST['streaming_script'],$lang['settings']['server_field_streaming_script']))
		{
			$cdn_api_script=$_POST['streaming_script'];
			if (!preg_match("|^[A-Za-z0-9_]+\.php$|is",$cdn_api_script))
			{
				$errors[]=get_aa_error('server_cdn_api_script_name',$lang['settings']['server_field_streaming_script']);
			} else {
				if (!is_file("$config[project_path]/admin/cdn/$cdn_api_script"))
				{
					$errors[]=get_aa_error('server_cdn_api_script_missing',$lang['settings']['server_field_streaming_script'],$cdn_api_script);
				} else {
					require_once "$config[project_path]/admin/cdn/$cdn_api_script";
					$cdn_api_name=str_replace(".php","",$cdn_api_script);
					if (!function_exists("{$cdn_api_name}_test") || !function_exists("{$cdn_api_name}_get_video") || !function_exists("{$cdn_api_name}_get_image") || !function_exists("{$cdn_api_name}_invalidate_resources"))
					{
						$errors[]=get_aa_error('server_cdn_api_script_invalid',$lang['settings']['server_field_streaming_script'],$cdn_api_script);
					} else {
						$test_function="{$cdn_api_name}_test";
						$ret=$test_function($_POST['streaming_key']);
						if ($ret<>'')
						{
							$errors[]=get_aa_error('server_cdn_api_error',$lang['settings']['server_field_streaming_script'],$ret);
						}
					}
				}
			}
		}
		validate_field('empty',$_POST['streaming_key'],$lang['settings']['server_field_streaming_secret_key']);
	}

	$connection_data_valid=1;
	if (intval($_POST['connection_type_id'])==0 || intval($_POST['connection_type_id'])==1)
	{
		if (!validate_field('path',$_POST['path'],$lang['settings']['server_field_path']))
		{
			$connection_data_valid=0;
		} elseif (!validate_field('file_separator',$_POST['path'],$lang['settings']['server_field_path']))
		{
			$connection_data_valid=0;
		}
		$_POST['ftp_host']='';
		$_POST['ftp_port']='';
		$_POST['ftp_user']='';
		$_POST['ftp_pass']='';
		$_POST['ftp_timeout']='';
	} elseif (intval($_POST['connection_type_id'])==2)
	{
		if (!validate_field('empty',$_POST['ftp_host'],$lang['settings']['server_field_ftp_host'])) {$connection_data_valid=0;}
		if (!validate_field('empty',$_POST['ftp_port'],$lang['settings']['server_field_ftp_port'])) {$connection_data_valid=0;}
		if (!validate_field('empty',$_POST['ftp_user'],$lang['settings']['server_field_ftp_user'])) {$connection_data_valid=0;}
		if (!validate_field('empty',$_POST['ftp_pass'],$lang['settings']['server_field_ftp_password'])) {$connection_data_valid=0;}
		if (!validate_field('empty',$_POST['ftp_timeout'],$lang['settings']['server_field_ftp_timeout'])) {$connection_data_valid=0;}
		$_POST['path']='';
	}

	if ($connection_data_valid==1)
	{
		$other_servers=mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $table_name"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['server_id']!=$item_id)
			{
				if ($other_server['connection_type_id']==0 || $other_server['connection_type_id']==1)
				{
					if ($other_server['path']==$_POST['path'])
					{
						$errors[]=get_aa_error('server_duplicate_connection',$lang['settings']['server_field_path'],$other_server['title']);
						$connection_data_valid=0;
						break;
					}
				} elseif ($other_server['connection_type_id']==2)
				{
					if ($other_server['ftp_host']==$_POST['ftp_host'] && $other_server['ftp_user']==$_POST['ftp_user'] && $other_server['ftp_folder']==$_POST['ftp_folder'])
					{
						$errors[]=get_aa_error('server_duplicate_connection',$lang['settings']['server_field_ftp_folder'],$other_server['title']);
						$connection_data_valid=0;
						break;
					}
				}
			}
		}
		$other_servers=mr2array(sql("select server_id, title, connection_type_id, path, ftp_host, ftp_user, ftp_folder from $config[tables_prefix]admin_conversion_servers"));
		foreach ($other_servers as $other_server)
		{
			if ($other_server['connection_type_id']==0 || $other_server['connection_type_id']==1)
			{
				if ($other_server['path']==$_POST['path'])
				{
					$errors[]=get_aa_error('server_duplicate_connection',$lang['settings']['server_field_path'],$other_server['title']);
					$connection_data_valid=0;
					break;
				}
			} elseif ($other_server['connection_type_id']==2)
			{
				if ($other_server['ftp_host']==$_POST['ftp_host'] && $other_server['ftp_user']==$_POST['ftp_user'] && $other_server['ftp_folder']==$_POST['ftp_folder'])
				{
					$errors[]=get_aa_error('server_duplicate_connection',$lang['settings']['server_field_ftp_folder'],$other_server['title']);
					$connection_data_valid=0;
					break;
				}
			}
		}
	}

	if ($connection_data_valid==1)
	{
		$test_result=test_connection_detailed($_POST);
		if ($test_result==1)
		{
			$errors[]=get_aa_error('server_invalid_connection1',$_POST['ftp_host'],$_POST['ftp_port']);
		} elseif ($test_result==2)
		{
			$errors[]=get_aa_error('server_invalid_connection2');
		} elseif ($test_result==3)
		{
			$errors[]=get_aa_error('server_invalid_connection3');
		} elseif ($test_result==4)
		{
			$errors[]=get_aa_error('server_no_ftp_extension',$lang['settings']['server_field_connection_type']);
		}
	}

	if ((intval($_POST['connection_type_id'])==1 || intval($_POST['connection_type_id'])==2) && (intval($_POST['streaming_type_id'])==0 || intval($_POST['streaming_type_id'])==1))
	{
		$is_remote=1;
		if (validate_field('url',$_POST['control_script_url'],$lang['settings']['server_field_control_script_url']))
		{
			if (get_page('',$_POST['control_script_url'],'','',1,0,60,'')<>'connected.')
			{
				$errors[]=get_aa_error('server_invalid_script',$lang['settings']['server_field_control_script_url']);
			} else
			{
				$remote_time = intval(get_page('', "$_POST[control_script_url]?action=time", '', '', 1, 0, 60, ''));
				if ($remote_time > 0)
				{
					if ($remote_time < time() + floatval($_POST['time_offset']) * 3600 - 240 || $remote_time > time() + floatval($_POST['time_offset']) * 3600 + 240)
					{
						$errors[] = get_aa_error('server_time_sync', $lang['settings']['server_field_time_offset'], date("Y-m-d H:i:s", $remote_time), date("Y-m-d H:i:s"));
					}
				}
				$remote_path=get_page('',"$_POST[control_script_url]?action=path&cv=$secret_remote_key",'','',1,0,60,'');
				if (strpos($remote_path,'Access denied') !== false)
				{
					$errors[]=get_aa_error('server_wrong_script',$lang['settings']['server_field_control_script_url'],$secret_remote_key);
				}
			}
		}
		if ($_POST['time_offset']<>'' && $_POST['time_offset']<>'0')
		{
			validate_field('empty_float',$_POST['time_offset'],$lang['settings']['server_field_time_offset']);
		}
	}

	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/cluster.dat"));
	}

	$content_check_error=false;
	if (!is_array($errors))
	{
		if ($content_type_id==1)
		{
			$videos=mr2array(sql_pr("select video_id, file_formats from $config[tables_prefix]videos where server_group_id=? and status_id in (0,1) order by rand() limit 10",$group_id));
			if (@count($videos)>0)
			{
				if (validate_server_videos($_POST,$videos)<>1)
				{
					$content_check_error=true;
				}
			}
		} else {
			$images=mr2array(sql_pr("select $config[tables_prefix]albums.album_id, image_id, image_formats from $config[tables_prefix]albums inner join $config[tables_prefix]albums_images on $config[tables_prefix]albums.album_id=$config[tables_prefix]albums_images.album_id where $config[tables_prefix]albums.server_group_id=? and $config[tables_prefix]albums.status_id in (0,1) order by rand() limit 10",$group_id));
			if (@count($images)>0)
			{
				if (validate_server_images($_POST,$images)<>1)
				{
					$content_check_error=true;
				}
			}
		}
	}

	if (!is_array($errors))
	{
		$rnd=mt_rand(10000000,99999999);
		mkdir("$config[temporary_path]/$rnd");
		chmod("$config[temporary_path]/$rnd",0777);
		if (check_file('status.dat','/',$_POST)>0)
		{
			get_file('status.dat','/',"$config[temporary_path]/$rnd",$_POST);
		}

		if (is_file("$config[temporary_path]/$rnd/status.dat"))
		{
			$data=explode("|",file_get_contents("$config[temporary_path]/$rnd/status.dat"));
			$load=trim($data[0]);
			$total_space=$data[1];
			$free_space=$data[2];
			@unlink("$config[temporary_path]/$rnd/status.dat");
		} elseif (intval($_POST['streaming_type_id'])==4)
		{
			if (intval($_POST['connection_type_id'])==0 || intval($_POST['connection_type_id'])==1)
			{
				$load=get_LA();
				$total_space=@disk_total_space($_POST['path']);
				$free_space=@disk_free_space($_POST['path']);
			} else {
				$load=0;
				$total_space=1000*1024*1024*1024;
				$free_space=1000*1024*1024*1024;
			}
		} elseif (intval($_POST['connection_type_id'])==1 || intval($_POST['connection_type_id'])==2)
		{
			$temp=explode("/",truncate_to_domain($_POST['urls']),2);
			$content_path=$temp[1];
			$content_path=trim($content_path,"/");
			$data=explode("|",get_page('',$_POST['control_script_url']."?action=status&content_path=".urlencode($content_path),'','',1,0,60,''));
			$load=$data[0];
			$total_space=$data[1];
			$free_space=$data[2];
		} else {
			$load=get_LA();
			$total_space=@disk_total_space($_POST['path']);
			$free_space=@disk_free_space($_POST['path']);
		}

		if ($total_space<1 || $free_space<1)
		{
			$load=0;
			$total_space="0";
			$free_space="0";
		}

		$remote_version='';
		if (intval($_POST['streaming_type_id'])!=4)
		{
			if (intval($_POST['connection_type_id'])==1 || intval($_POST['connection_type_id'])==2)
			{
				$remote_version=get_page('',"$_POST[control_script_url]?action=version",'','',1,0,60,'');
				if ($remote_version=='')
				{
					$remote_version='3.4.0';
				}
			}
		}

		if ($_POST['action']=='add_new_complete')
		{
			$videos_count=mr2number(sql_pr("select count(*) from $config[tables_prefix]videos where server_group_id=?",$group_id));
			$albums_count=mr2number(sql_pr("select count(*) from $config[tables_prefix]albums where server_group_id=?",$group_id));
			if ($videos_count+$albums_count==0)
			{
				$status_id=1;
			} else {
				$status_id=0;
			}
			$item_id=sql_insert("insert into $table_name set group_id=?, content_type_id=?, title=?, status_id=?, connection_type_id=?, streaming_type_id=?, streaming_script=?, streaming_key=?, is_replace_domain_on_satellite=?, is_remote=?, path=?, remote_path=?, urls=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, control_script_url=?, control_script_url_version=?, control_script_url_lock_ip=?, time_offset=?, $table_name.load=?, total_space=?, free_space=?, lb_weight=1, added_date=?",
			$group_id,$content_type_id,$_POST['title'],intval($status_id),intval($_POST['connection_type_id']),intval($_POST['streaming_type_id']),$_POST['streaming_script'],$_POST['streaming_key'],intval($_POST['is_replace_domain_on_satellite']),intval($is_remote),$_POST['path'],nvl($remote_path),$_POST['urls'],$_POST['ftp_host'],$_POST['ftp_port'],$_POST['ftp_user'],$_POST['ftp_pass'],$_POST['ftp_folder'],$_POST['ftp_timeout'],$_POST['control_script_url'],$remote_version,intval($_POST['control_script_url_lock_ip']),str_replace(",",".",floatval($_POST['time_offset'])),$load,$total_space,$free_space,date("Y-m-d H:i:s"));
			$_SESSION['messages'][]=$lang['common']['success_message_added'];
		} else {
			sql_pr("update $table_name set title=?, connection_type_id=?, streaming_type_id=?, streaming_script=?, streaming_key=?, is_replace_domain_on_satellite=?, is_remote=?, path=?, remote_path=?, urls=?, ftp_host=?, ftp_port=?, ftp_user=?, ftp_pass=?, ftp_folder=?, ftp_timeout=?, control_script_url=?, control_script_url_version=?, control_script_url_lock_ip=?, time_offset=?, $table_name.load=?, total_space=?, free_space=? where $table_key_name=?",
			$_POST['title'],intval($_POST['connection_type_id']),intval($_POST['streaming_type_id']),$_POST['streaming_script'],$_POST['streaming_key'],intval($_POST['is_replace_domain_on_satellite']),intval($is_remote),$_POST['path'],nvl($remote_path),$_POST['urls'],$_POST['ftp_host'],$_POST['ftp_port'],$_POST['ftp_user'],$_POST['ftp_pass'],$_POST['ftp_folder'],$_POST['ftp_timeout'],$_POST['control_script_url'],$remote_version,intval($_POST['control_script_url_lock_ip']),str_replace(",",".",floatval($_POST['time_offset'])),$load,$total_space,$free_space,$item_id);
			$_SESSION['messages'][]=$lang['common']['success_message_modified'];
		}

		if ($content_check_error)
		{
			sql_pr("update $table_name set error_id=5, error_iteration=2 where $table_key_name=?",$item_id);
		}

		update_cluster_data();
		return_ajax_success($page_name);
	} else {
		return_ajax_errors($errors);
	}
}

if ($_GET['action']=='delete' && intval($_GET['g_id'])>0)
{
	if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where group_id=".intval($_GET['g_id'])))==0)
	{
		sql("delete from $config[tables_prefix]admin_servers_groups where group_id=".intval($_GET['g_id']));

		if (mr2number(sql("select value from $config[tables_prefix]options where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'"))==intval($_GET['g_id']))
		{
			sql("update $config[tables_prefix]options set value='auto' where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_VIDEO'");
		}
		if (mr2number(sql("select value from $config[tables_prefix]options where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM'"))==intval($_GET['g_id']))
		{
			sql("update $config[tables_prefix]options set value='auto' where variable='DEFAULT_SERVER_GROUP_IN_ADMIN_ADD_ALBUM'");
		}
		$_SESSION['messages'][]=$lang['common']['success_message_removed'];
	}
	return_ajax_success($page_name);
}

if ($_GET['action']=='delete' && intval($_GET['id'])>0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/cluster.dat"));
		return_ajax_errors($errors);
	}

	$server_id=intval($_GET['id']);
	$group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers where server_id=?",$server_id));
	$videos_count=mr2number(sql("select count(*) from $config[tables_prefix]videos where server_group_id=$group_id"));
	$albums_count=mr2number(sql("select count(*) from $config[tables_prefix]albums where server_group_id=$group_id"));
	if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and group_id=$group_id and server_id<>$server_id"))>0 || $videos_count+$albums_count==0)
	{
		if ($videos_count+$albums_count>0)
		{
			if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and group_id=$group_id and server_id<>$server_id and lb_countries=''"))==0)
			{
				$errors[]=get_aa_error('server_default_lb_countries');
				return_ajax_errors($errors);
			}
		}

		sql("delete from $config[tables_prefix]admin_servers where server_id=".intval($_GET['id']));
		@unlink("$config[project_path]/admin/logs/debug_storage_server_".intval($_GET['id']).".txt");
		update_cluster_data();
		$_SESSION['messages'][]=$lang['settings']['success_message_server_removed'];
	}
	return_ajax_success($page_name);
} elseif ($_GET['action']=='activate' && intval($_GET['id'])>0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/cluster.dat"));
		return_ajax_errors($errors);
	}

	sql("update $config[tables_prefix]admin_servers set status_id=1 where server_id=".intval($_GET['id']));
	update_cluster_data();
	$_SESSION['messages'][]=$lang['common']['success_message_activated'];
	return_ajax_success($page_name);
} elseif ($_GET['action']=='deactivate' && intval($_GET['id'])>0)
{
	if (!is_writable("$config[project_path]/admin/data/system/cluster.dat"))
	{
		$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/system/cluster.dat"));
		return_ajax_errors($errors);
	}

	$server_id=intval($_GET['id']);
	$group_id=mr2number(sql_pr("select group_id from $config[tables_prefix]admin_servers where server_id=?",$server_id));
	if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and group_id=$group_id and server_id<>$server_id"))>0)
	{
		if (mr2number(sql("select count(*) from $config[tables_prefix]admin_servers where status_id=1 and group_id=$group_id and server_id<>$server_id and lb_countries=''"))==0)
		{
			$errors[]=get_aa_error('server_default_lb_countries');
			return_ajax_errors($errors);
		}

		sql("update $config[tables_prefix]admin_servers set status_id=0 where server_id=".intval($_GET['id']));
		update_cluster_data();
		$_SESSION['messages'][]=$lang['common']['success_message_deactivated'];
	}
	return_ajax_success($page_name);
} elseif ($_GET['action']=='sync' && intval($_GET['id'])>0)
{
	if (mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where type_id=27")) == 0)
	{
		$server_id = intval($_GET['id']);

		sql_pr("insert into $config[tables_prefix]background_tasks set status_id=0, priority=0, type_id=27, data=?, added_date=?", serialize(array('server_id' => $server_id)), date("Y-m-d H:i:s"));
		$_SESSION['messages'][] = $lang['settings']['success_message_sync_started'];
	}
	return_ajax_success($page_name);
} elseif ($_GET['action']=='enable_debug' && intval($_GET['id'])>0)
{
	sql("update $config[tables_prefix]admin_servers set is_logging_enabled=1 where server_id=".intval($_GET['id']));
	$_SESSION['messages'][]=$lang['common']['success_message_debug_enabled'];
	return_ajax_success($page_name);
} elseif ($_GET['action']=='disable_debug' && intval($_GET['id'])>0)
{
	sql("update $config[tables_prefix]admin_servers set is_logging_enabled=0 where server_id=".intval($_GET['id']));
	@unlink("$config[project_path]/admin/logs/debug_storage_server_".intval($_GET['id']).".txt");
	$_SESSION['messages'][]=$lang['common']['success_message_debug_disabled'];
	return_ajax_success($page_name);
}

if ($_GET['action']=='change_group' && intval($_GET['item_id'])>0)
{
	$_POST=mr2array_single(sql_pr("select * from $config[tables_prefix]admin_servers_groups where group_id=?",intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	$_POST['servers']=mr2array(sql_pr("select * from $config[tables_prefix]admin_servers where group_id=? order by title asc",intval($_GET['item_id'])));

	$min_free_space=0;
	foreach ($_POST['servers'] as $server)
	{
		if ($min_free_space==0 || $min_free_space>$server['free_space'])
		{
			$min_free_space=$server['free_space'];
		}
	}

	if ($min_free_space<$options['SERVER_GROUP_MIN_FREE_SPACE_MB']*1024*1024)
	{
		$_POST['errors'][]=$lang['settings']['dg_servers_warning_free_space'];
	}
}

if ($_GET['action']=='add_new_group')
{
	$_POST['status_id']=1;
}

if ($_GET['action']=='change' && intval($_GET['item_id'])>0)
{
	$_POST=mr2array_single(sql_pr("select *, (select title from $config[tables_prefix]admin_servers_groups where $config[tables_prefix]admin_servers_groups.group_id=$config[tables_prefix]admin_servers.group_id) as group_title from $config[tables_prefix]admin_servers where server_id=?",intval($_GET['item_id'])));
	if (count($_POST) == 0)
	{
		header("Location: $page_name");
		die;
	}

	if ($_POST['error_id']==1 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_write'];}
	if ($_POST['error_id']==2 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_control_script'];}
	if ($_POST['error_id']==3 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_control_script_key'];}
	if ($_POST['error_id']==4 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_time_sync'];}
	if ($_POST['error_id']==5 && $_POST['error_iteration']>1) {$_POST['errors'][]=str_replace("%1%",$lang['settings']['dg_servers_error_content_availability2'],$lang['settings']['dg_servers_error_content_availability']);}
	if ($_POST['error_id']==6 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_cdn_api'];}
	if ($_POST['error_id']==7 && $_POST['error_iteration']>1) {$_POST['errors'][]=$lang['settings']['dg_servers_error_https'];}

	if ($_POST['streaming_type_id']==0)
	{
		$nginx_config_rules='';
		if ($_POST['connection_type_id']==0)
		{
			$storage_url="";
			if (strpos($_POST['urls'],'/',8)!==false)
			{
				$storage_url=trim(substr($_POST['urls'],strpos($_POST['urls'],'/',8)),'/');
			}
			$storage_path=rtrim(str_replace($storage_url,'',$_POST['path']),'/');
		} elseif ($_POST['remote_path']!='')
		{
			$storage_url="";
			if (strpos($_POST['urls'],'/',8)!==false)
			{
				$storage_url=trim(substr($_POST['urls'],strpos($_POST['urls'],'/',8)),'/');
			}
			$storage_path=rtrim(str_replace($storage_url,'',$_POST['remote_path']),'/');
		}
		if ($storage_path!='')
		{
			if ($_POST['content_type_id']==1)
			{
				$formats_videos=mr2array(sql("select * from $config[tables_prefix]formats_videos where status_id in (0,1,2) order by video_type_id, title"));
				$has_mp4=false;
				foreach ($formats_videos as $format)
				{
					if (substr($format['postfix'],strlen($format['postfix'])-4)=='.mp4')
					{
						$has_mp4=true;
					}
				}

				$nginx_config_rules.="    # protect videos from direct access\n";
				if ($storage_url=="")
				{
					$nginx_config_rules.="    location / {\n";
				} else {
					$nginx_config_rules.="    location /$storage_url/ {\n";
				}
				$nginx_config_rules.="        root $storage_path;\n";
				$nginx_config_rules.="        limit_rate_after 2m;\n";
				$nginx_config_rules.="        internal;\n";
				$nginx_config_rules.="    }";
			} elseif ($_POST['content_type_id']==2)
			{
				$formats_albums=mr2array(sql("select * from $config[tables_prefix]formats_albums where status_id in (0,1) and access_level_id>0 order by title"));
				foreach ($formats_albums as $format)
				{
					if ($nginx_config_rules!='') {$nginx_config_rules.="\n\n";}
					$nginx_config_rules.="    # protect images of $format[size] format from direct access\n";
					$format_group_folder='main';
					if ($format['group_id']==2)
					{
						$format_group_folder='preview';
					}
					if ($storage_url=="")
					{
						$nginx_config_rules.="    location /$format_group_folder/$format[size]/ {\n";
					} else {
						$nginx_config_rules.="    location /$storage_url/$format_group_folder/$format[size]/ {\n";
					}
					$nginx_config_rules.="        root $storage_path;\n";
					$nginx_config_rules.="        internal;\n";
					$nginx_config_rules.="    }";
				}
				$album_sources_access=mr2number(sql("select value from $config[tables_prefix]options where variable='ALBUMS_SOURCE_FILES_ACCESS_LEVEL'"));
				if ($album_sources_access>0)
				{
					if ($nginx_config_rules!='') {$nginx_config_rules.="\n\n";}
					$nginx_config_rules.="    # protect source images from direct access\n";
					if ($storage_url=="")
					{
						$nginx_config_rules.="    location /sources/ {\n";
					} else {
						$nginx_config_rules.="    location /$storage_url/sources/ {\n";
					}
					$nginx_config_rules.="        root $storage_path;\n";
					$nginx_config_rules.="        internal;\n";
					$nginx_config_rules.="    }";
				}
			}
			$_POST['nginx_config_rules']=$nginx_config_rules;
			$_POST['nginx_config_rules_rows']= substr_count($nginx_config_rules, "\n") + 1;
		}
	}

	$_POST['numeric_control_script_url_version']=intval(str_replace('.','',$_POST['control_script_url_version']));
}

$servers_count=0;
$data=mr2array(sql("select sg.*, coalesce(vc.videos_amount, 0) as videos_amount, coalesce(ac.albums_amount, 0) as albums_amount from $config[tables_prefix]admin_servers_groups sg left join (select server_group_id, count(*) as videos_amount from $config[tables_prefix]videos group by server_group_id) vc on sg.group_id=vc.server_group_id left join (select server_group_id, count(*) as albums_amount from $config[tables_prefix]albums group by server_group_id) ac on sg.group_id=ac.server_group_id order by sg.title asc"));
foreach ($data as $kg=>$vg)
{
	if ($_SESSION['save'][$page_name]['se_text']<>'' && !mb_contains($vg['title'], $_SESSION['save'][$page_name]['se_text']))
	{
		unset($data[$kg]);
		continue;
	}

	$group_load=0;
	$group_load_amount=0;
	$min_total_space=0;
	$min_free_space=0;
	$data_temp=mr2array(sql("select * from $config[tables_prefix]admin_servers where group_id='$vg[group_id]' order by title asc"));
	$data[$kg]['total_servers_amount']=0;
	$data[$kg]['active_servers_amount']=0;
	foreach ($data_temp as $ks=>$vs)
	{
		$temp=$vs;

		$temp['total_space_string']=sizeToHumanString($temp['total_space'],2);
		$temp['free_space_string']=sizeToHumanString($temp['free_space'],2);
		if ($temp['total_space']>0)
		{
			$temp['free_space_percent']=round(($temp['free_space']/$temp['total_space'])*100,2);
		} else {
			$temp['free_space_percent']=0;
		}

		if (is_file("$config[project_path]/admin/logs/debug_storage_server_$temp[server_id].txt"))
		{
			$temp['has_debug_log']=1;
		}

		if ($min_total_space==0 || $min_total_space>$temp['total_space']) {$min_total_space=$temp['total_space'];}
		if ($min_free_space==0 || $min_free_space>$temp['free_space']) {$min_free_space=$temp['free_space'];}

		$group_load+=$temp['load'];
		$group_load_amount++;

		$data[$kg]['servers'][]=$temp;
		$data[$kg]['total_servers_amount']++;
		if ($temp['status_id']==1) {$data[$kg]['active_servers_amount']++;}
		$servers_count++;
	}
	$data[$kg]['servers_amount']=count($data[$kg]['servers']);
	if ($group_load_amount>0)
	{
		$data[$kg]['load']=ceil(($group_load/$group_load_amount)*100)/100;
	}
	$data[$kg]['total_space_string']=sizeToHumanString($min_total_space,2);
	$data[$kg]['free_space']=$min_free_space;
	$data[$kg]['free_space_string']=sizeToHumanString($min_free_space,2);
	if ($min_total_space>0)
	{
		$data[$kg]['free_space_percent']=round(($min_free_space/$min_total_space)*100,2);
	} else {
		$data[$kg]['free_space_percent']=0;
	}
}

$smarty=new mysmarty();
$smarty->assign('list_groups',mr2array(sql("select * from $config[tables_prefix]admin_servers_groups order by title asc")));
$smarty->assign('left_menu',"menu_options.tpl");

if (in_array($_REQUEST['action'],array('change','change_group'))) {$smarty->assign('supports_popups',1);}

$smarty->assign('options',$options);
$smarty->assign('data',$data);
$smarty->assign('lang',$lang);
$smarty->assign('config',$config);
$smarty->assign('page_name',$page_name);
$smarty->assign('list_messages',$list_messages);
$smarty->assign('table_key_name',$table_key_name);
$smarty->assign('total_num',$servers_count);
$smarty->assign('latest_api_version',$latest_api_version);
$smarty->assign('sync_tasks_count',mr2number(sql("select count(*) from $config[tables_prefix]background_tasks where type_id=27")));
$smarty->assign('template',str_replace(".php",".tpl",$page_name));

if ($_REQUEST['action']=='change')
{
	$smarty->assign('page_title',str_replace("%2%",$_POST['group_title'],str_replace("%1%",$_POST['title'],$lang['settings']['server_edit'])));
} elseif ($_REQUEST['action']=='change_group')
{
	$smarty->assign('page_title',str_replace("%1%",$_POST['title'],$lang['settings']['server_group_edit']));
} elseif ($_REQUEST['action']=='add_new')
{
	$smarty->assign('page_title',$lang['settings']['server_add']);
} elseif ($_REQUEST['action']=='add_new_group')
{
	$smarty->assign('page_title',$lang['settings']['server_group_add']);
} else {
	$smarty->assign('page_title',$lang['settings']['submenu_option_storage_servers_list']);
}

$smarty->display("layout.tpl");
