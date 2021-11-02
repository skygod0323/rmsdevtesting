<?php
function list_albums_imagesShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$website_ui_data,$database_selectors;

	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']])>0){$block_config['items_per_page']=intval($_REQUEST[$block_config['var_items_per_page']]);}

	$where='';
	if (intval($block_config['is_private'])==1)
	{
		$where.=" and is_private=0";
	} elseif (intval($block_config['is_private'])==2)
	{
		$where.=" and is_private=1";
	}

	$format_filter = strtolower(trim($block_config['format']));
	if (in_array($format_filter, array('gif', 'png', 'jpg')))
	{
		$where.=" and format='" . sql_escape($format_filter) . "'";
	}

	$metadata=list_albums_imagesMetaData();
	foreach ($metadata as $res)
	{
		if ($res['name']=='sort_by')
		{
			preg_match("|SORTING\[(.*?)\]|is",$res['type'],$temp);
			$sorting_available=explode(",",$temp[1]);
			break;
		}
	}
	$sorting_available[]="rand()";

	$sort_by=trim(strtolower($_REQUEST[$block_config['var_sort_by']]));
	if ($sort_by=='') {$sort_by=trim(strtolower($block_config['sort_by']));}
	if (strpos($sort_by," asc")!==false) {$direction="asc";} else {$direction="desc";}
	$sort_by_clear=str_replace(" desc","",str_replace(" asc","",$sort_by));
	if ($sort_by_clear=='' || !in_array($sort_by_clear,$sorting_available)) {$sort_by_clear="";}
	if ($sort_by_clear=='') {$sort_by_clear="category_id";}

	$storage[$object_id]['sort_by']=$sort_by_clear;
	$smarty->assign("sort_by",$sort_by_clear);

	$sort_by="$sort_by_clear $direction";
	if (isset($block_config['var_from']))
	{
		$from=intval($_REQUEST[$block_config['var_from']]);
		$total_count=mr2number(sql("select count(*) from $config[tables_prefix]albums_images inner join $config[tables_prefix]albums on $config[tables_prefix]albums_images.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] $where"));
		if ($config['is_pagination_2.0']=='true') {if ($from>0) $from=($from-1)*intval($block_config['items_per_page']);}
		if ($config['is_pagination_3.0']=="true") {if (($from>0 && ($from>=$total_count || $total_count==0)) || $from<0) {return 'status_404';}} else {if ($from>$total_count || $from<0) {$from=0;}}

		$data=mr2array(sql("SELECT $config[tables_prefix]albums_images.image_id,
									$config[tables_prefix]albums_images.album_id,
									$config[tables_prefix]albums_images.title,
									($config[tables_prefix]albums_images.rating/$config[tables_prefix]albums_images.rating_amount) as rating,
									$config[tables_prefix]albums_images.image_viewed,
									$config[tables_prefix]albums_images.image_formats,
									$config[tables_prefix]albums_images.added_date,
									$database_selectors[albums_selector_dir] as album_dir,
									$database_selectors[albums_selector_title] as album_title,
									($config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount) as album_rating,
									$config[tables_prefix]albums.server_group_id as album_server_group_id
							from $config[tables_prefix]albums_images inner join $config[tables_prefix]albums on $config[tables_prefix]albums_images.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] $where
							order by $sort_by LIMIT $from, $block_config[items_per_page]"));

		$storage[$object_id]['total_count']=$total_count;
		$storage[$object_id]['showing_from']=$from;
		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$storage[$object_id]['var_from']=$block_config['var_from'];
		$smarty->assign("total_count",$total_count);
		$smarty->assign("showing_from",$from);
		$smarty->assign("items_per_page",$block_config['items_per_page']);
		$smarty->assign("var_from",$block_config['var_from']);

		$smarty->assign("nav",get_site_pagination($object_id,$total_count,$block_config['items_per_page'],$from,'',$block_config['links_per_page'],$block_config['var_from'],1));
	} else {
		$data=mr2array(sql("SELECT $config[tables_prefix]albums_images.image_id,
									$config[tables_prefix]albums_images.album_id,
									$config[tables_prefix]albums_images.title,
									($config[tables_prefix]albums_images.rating/$config[tables_prefix]albums_images.rating_amount) as rating,
									$config[tables_prefix]albums_images.image_viewed,
									$config[tables_prefix]albums_images.image_formats,
									$config[tables_prefix]albums_images.added_date,
									$database_selectors[albums_selector_dir] as album_dir,
									$database_selectors[albums_selector_title] as album_title,
									($config[tables_prefix]albums.rating/$config[tables_prefix]albums.rating_amount) as album_rating,
									$config[tables_prefix]albums.server_group_id as album_server_group_id
							from $config[tables_prefix]albums_images inner join $config[tables_prefix]albums on $config[tables_prefix]albums_images.album_id=$config[tables_prefix]albums.album_id where $database_selectors[where_albums] $where
							order by $sort_by LIMIT $block_config[items_per_page]"));

		$storage[$object_id]['items_per_page']=$block_config['items_per_page'];
		$smarty->assign("items_per_page",$block_config['items_per_page']);
	}

	$cluster=unserialize(file_get_contents("$config[project_path]/admin/data/system/cluster.dat"));
	$cluster_servers=array();
	$cluster_servers_weights=array();
	foreach ($cluster as $server)
	{
		if ($server['status_id']==1)
		{
			$cluster_servers[intval($server['group_id'])][]=$server;
			$cluster_servers_weights[intval($server['group_id'])]+=$server['lb_weight'];
		}
	}

	$formats_albums=@unserialize(@file_get_contents("$config[project_path]/admin/data/system/formats_albums.dat"));
	foreach ($data as $k=>$v)
	{
		$pattern=str_replace("%IMG%",$v['image_id'],str_replace("%ID%",$v['album_id'],str_replace("%DIR%",$v['album_dir'],$website_ui_data['WEBSITE_LINK_PATTERN_IMAGE'])));
		$data[$k]['view_page_url']="$config[project_url]/$pattern";

		$lb_server=load_balance_server($v['album_server_group_id'],$cluster_servers,$cluster_servers_weights);

		$album_id=$v['album_id'];
		$dir_path=get_dir_by_id($album_id);
		$formats=array();
		$image_formats=get_image_formats($album_id,$v['image_formats']);
		foreach ($formats_albums as $format)
		{
			if ($format['group_id']==1)
			{
				$format_item=array();
				$file_path="main/$format[size]/$dir_path/$album_id/$v[image_id].jpg";
				$hash=md5($config['cv'].$file_path);

				$format_item['direct_url']="$lb_server[urls]/$file_path";
				$format_item['protected_url']="$config[project_url]/get_image/$v[album_server_group_id]/$hash/$file_path/";

				foreach ($image_formats as $format_rec)
				{
					if ($format_rec['size']==$format['size'])
					{
						$format_item['dimensions']=$format_rec['dimensions'];
						$format_item['filesize']=$format_rec['file_size_string'];
						break;
					}
				}

				$formats[$format['size']]=$format_item;
			}
		}

		$format_item=array();
		$file_path="sources/$dir_path/$album_id/$v[image_id].jpg";
		$hash=md5($config['cv'].$file_path);
		$format_item['direct_url']="$lb_server[urls]/$file_path";
		$format_item['protected_url']="$config[project_url]/get_image/$v[album_server_group_id]/$hash/$file_path/";
		foreach ($image_formats as $format_rec)
		{
			if ($format_rec['size']=='source')
			{
				$format_item['dimensions']=$format_rec['dimensions'];
				$format_item['filesize']=$format_rec['file_size_string'];
				break;
			}
		}
		$formats['source']=$format_item;

		$data[$k]['formats']=$formats;
	}

	$smarty->assign("data",$data);

	return '';
}

function list_albums_imagesGetHash($block_config)
{
	$from=intval($_REQUEST[$block_config['var_from']]);
	$items_per_page=intval($_REQUEST[$block_config['var_items_per_page']]);

	$var_sort_by=trim($_REQUEST[$block_config['var_sort_by']]);

	return "$from|$items_per_page|$var_sort_by";
}

function list_albums_imagesCacheControl($block_config)
{
	return "default";
}

function list_albums_imagesMetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>1, "default_value"=>"10"),
		array("name"=>"links_per_page",     "group"=>"pagination", "type"=>"INT",    "is_required"=>0, "default_value"=>"10"),
		array("name"=>"var_from",           "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"from"),
		array("name"=>"var_items_per_page", "group"=>"pagination", "type"=>"STRING", "is_required"=>0, "default_value"=>"items_per_page"),

		// sorting
		array("name"=>"sort_by",     "group"=>"sorting", "type"=>"SORTING[image_id,title,rating,image_viewed,added_date]", "is_required"=>1, "default_value"=>"added_date"),
		array("name"=>"var_sort_by", "group"=>"sorting", "type"=>"STRING", "is_required"=>0, "default_value"=>"sort_by"),

		// static filters
		array("name"=>"is_private", "group"=>"static_filters", "type"=>"CHOICE[0,1,2]", "is_required"=>0, "default_value"=>"0"),
		array("name"=>"format",     "group"=>"static_filters", "type"=>"STRING",        "is_required"=>0, "default_value"=>"gif"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
?>