<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

function rotator_clicks_matrixInit()
{
	global $config;

	if (!is_dir("$config[project_path]/admin/data/plugins"))
	{
		mkdir("$config[project_path]/admin/data/plugins",0777);chmod("$config[project_path]/admin/data/plugins",0777);
	}
	$plugin_path="$config[project_path]/admin/data/plugins/rotator_clicks_matrix";
	if (!is_dir($plugin_path))
	{
		mkdir($plugin_path,0777);chmod($plugin_path,0777);
	}
}

function rotator_clicks_matrixShow()
{
	global $config,$lang,$errors,$page_name;

	rotator_clicks_matrixInit();
	$plugin_path="$config[project_path]/admin/data/plugins/rotator_clicks_matrix";

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

		validate_field('empty',$_POST['page_external_id'],$lang['plugins']['rotator_clicks_matrix']['field_page']);
		validate_field('empty_int',$_POST['items_in_row'],$lang['plugins']['rotator_clicks_matrix']['field_items_in_row']);

		if (!is_writable("$plugin_path/data.dat"))
		{
			$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat"));
		}

		if (!is_array($errors))
		{
			$save_data=@unserialize(@file_get_contents("$plugin_path/data.dat"));
			$save_data['items_in_row']=intval($_POST['items_in_row']);

			file_put_contents("$plugin_path/data.dat", serialize($save_data), LOCK_EX);

			return_ajax_success("$page_name?plugin_id=rotator_clicks_matrix&amp;page_external_id=$_POST[page_external_id]");
		} else {
			return_ajax_errors($errors);
		}
	} elseif ($_POST['action']=='reset_complete')
	{
		$page_id=$_POST['page_external_id'];
		if (is_array($_POST['reset_page']))
		{
			foreach ($_POST['reset_page'] as $place_id)
			{
				$matrix_key=md5("$page_id|$place_id");
				if (!is_writable("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"))
				{
					$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"));
				}
			}
		}
		if (is_array($_POST['reset_place']))
		{
			foreach ($_POST['reset_place'] as $place_id)
			{
				$matrix_key=md5("$page_id|$place_id");
				if (!is_writable("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}.dat"))
				{
					$errors[]=get_aa_error('filesystem_permission_write',str_replace("//","/","$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}.dat"));
				}
			}
		}
		if (!is_array($errors))
		{
			if (is_array($_POST['reset_page']))
			{
				foreach ($_POST['reset_page'] as $place_id)
				{
					$matrix_key=md5("$page_id|$place_id");
					unlink("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat");
				}
			}
			if (is_array($_POST['reset_place']))
			{
				foreach ($_POST['reset_place'] as $place_id)
				{
					$matrix_key=md5("$page_id|$place_id");
					unlink("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}.dat");
				}
			}

			return_ajax_success("$page_name?plugin_id=rotator_clicks_matrix&amp;page_external_id=$_POST[page_external_id]");
		} else {
			return_ajax_errors($errors);
		}
	}

	if (!is_file("$plugin_path/data.dat"))
	{
		$_POST=array();
		$_POST['items_in_row']=3;

		file_put_contents("$plugin_path/data.dat", serialize($_POST), LOCK_EX);
	} else {
		$_POST=@unserialize(@file_get_contents("$plugin_path/data.dat"));
	}

	if (!is_writable("$plugin_path"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path")));
	} elseif (!is_writable("$plugin_path/data.dat"))
	{
		$_POST['errors'][]=bb_code_process(get_aa_error('filesystem_permission_write',str_replace("//","/","$plugin_path/data.dat")));
	}

	$displayed_data=array();

	$pages=get_site_pages();
	$pages[]=array('external_id'=>'$global','title'=>$lang['plugins']['rotator_clicks_matrix']['field_page_global']);
	foreach ($pages as $k=>$v)
	{
		if (is_file("$config[project_path]/admin/data/config/$v[external_id]/config.dat"))
		{
			$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/config.dat"));
			$blocks_list=explode("|AND|",trim($temp[2]));
			$has_rotator_blocks=0;
			foreach ($blocks_list as $block_info)
			{
				$temp2=explode("[SEP]",trim($block_info));
				if (strpos($temp2[0],'list_videos')===0)
				{
					$matrix_key=md5("$v[external_id]|$temp2[0]_$temp2[1]");
					if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/$matrix_key.dat") || is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"))
					{
						$has_rotator_blocks=1;
						if ($_GET['page_external_id']==$v['external_id'])
						{
							$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$v[external_id]/$temp2[0]_$temp2[1].dat"));
							$items_per_page=0;
							if (trim($temp[1])<>'')
							{
								$temp_params=explode("&",$temp[1]);
								foreach ($temp_params as $temp_param)
								{
									$temp_param=explode("=",$temp_param,2);
									if (trim($temp_param[0])=='items_per_page')
									{
										$items_per_page=intval($temp_param[1]);
										break;
									}
								}
							}

							$block_rec=array();
							$block_rec['id']="$temp2[0]_$temp2[1]";

							if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/$matrix_key.dat"))
							{
								$block_rec['matrix']=@unserialize(file_get_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/$matrix_key.dat"));
								$block_rec['places_count']=$items_per_page;

								$block_rec['matrix_pc']=array();
								foreach ($block_rec['matrix'] as $k2=>$v2)
								{
									if ($k2==0) {continue;}
									$block_rec['matrix_pc'][$k2]=number_format($v2/$block_rec['matrix'][0]*100, 2);
									if ($v2>1000000)
									{
										$block_rec['matrix'][$k2]=number_format($v2/1000000,2).'M';
									} elseif ($v2>1000)
									{
										$block_rec['matrix'][$k2]=number_format($v2/1000,2).'K';
									} else
									{
										$block_rec['matrix'][$k2]=$v2;
									}
								}
							}
							if (is_file("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"))
							{
								$block_rec['page_matrix']=@unserialize(file_get_contents("$config[project_path]/admin/data/engine/rotator/videos/matrix/{$matrix_key}_page.dat"));
								$block_rec['page_matrix_pc']=array();
								foreach ($block_rec['page_matrix'] as $k2=>$v2)
								{
									if ($k2==0) {continue;}
									$block_rec['page_matrix_pc'][$k2]=number_format($v2/$block_rec['page_matrix'][0]*100, 2);
									if ($v2>1000000)
									{
										$block_rec['page_matrix'][$k2]=number_format($v2/1000000,2).'M';
									} elseif ($v2>1000)
									{
										$block_rec['page_matrix'][$k2]=number_format($v2/1000,2).'K';
									} else
									{
										$block_rec['page_matrix'][$k2]=$v2;
									}
								}
							}

							$displayed_data[]=$block_rec;
						}
					}
				}
			}
			if ($has_rotator_blocks==0)
			{
				unset($pages[$k]);
			}
		} else {
			unset($pages[$k]);
		}
	}
	$_POST['pages']=$pages;
	$_POST['displayed_data']=$displayed_data;
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
