<?php
function paginationShow($block_config,$object_id)
{
	global $config,$smarty,$storage;

	$target_block_id_dir=str_replace("|","_",strtolower(str_replace(" ","_",$block_config['related_block_ext_id'])));

	if (isset($block_config['se_friendly']))
	{
		$block_config['se_friendly']=1;
	} else {
		$block_config['se_friendly']=0;
	}

	$var_from=$storage[$target_block_id_dir]['var_from'];
	$total_count=intval($storage[$target_block_id_dir]['total_count']);
	$items_per_page=intval($storage[$target_block_id_dir]['items_per_page']);
	$from=intval($_GET[$var_from]);
	if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*$items_per_page;}

	$data=get_site_pagination($object_id,$total_count,$items_per_page,$from,$block_config['url_prefix'],$block_config['links_per_page'],$var_from,$block_config['se_friendly']);
	$smarty->assign('data',$data);
	$smarty->assign('nav',$data);
}

function paginationGetHash($block_config)
{
	global $config,$storage,$page_id;

	$target_block_id=substr($block_config['related_block_ext_id'],0,strpos($block_config['related_block_ext_id'],"|"));

	$target_block_id_dir=str_replace("|","_",strtolower(str_replace(" ","_",$block_config['related_block_ext_id'])));
	$related_block_ext_id_hash=$storage[$target_block_id_dir]['hash'];

	if (is_file("$config[project_path]/blocks/$target_block_id/$target_block_id.php"))
	{
		require_once("$config[project_path]/blocks/$target_block_id/$target_block_id.php");
	}
	$hash_function="{$target_block_id}GetHash";
	if (function_exists($hash_function))
	{

		$temp=explode("||",file_get_contents("$config[project_path]/admin/data/config/$page_id/$target_block_id_dir.dat"));
		$config_params_temp=array();
		if (trim($temp[1])<>'')
		{
			$temp_params=explode("&",$temp[1]);
			foreach ($temp_params as $temp_param)
			{
				$temp_param=explode("=",$temp_param,2);
				$config_params_temp[trim($temp_param[0])]=trim($temp_param[1]);
			}
		}

		$related_block_ext_id_hash=$hash_function($config_params_temp);
	}

	return "$related_block_ext_id_hash";
}

function paginationCacheControl($block_config)
{
	return "default";
}

function paginationMetaData()
{
	return array(
		array("name"=>"links_per_page",       "type"=>"INT",        "is_required"=>1, "default_value"=>"10"),
		array("name"=>"related_block_ext_id", "type"=>"LIST_BLOCK", "is_required"=>1, "default_value"=>""),
		array("name"=>"url_prefix",           "type"=>"STRING",     "is_required"=>0, "default_value"=>"?"),
		array("name"=>"se_friendly",          "type"=>"",           "is_required"=>0)
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>