<?php
function tags_cloud_v2Show($block_config,$object_id)
{
	global $config,$smarty,$storage,$database_selectors;

	$where='';
	if (isset($block_config['mode_categories']))
	{
		if (isset($block_config['show_only_with_avatar']))
		{
			$where=" and screenshot1!='' ";
		} elseif (isset($block_config['show_only_without_avatar'])) {
			$where=" and screenshot1='' ";
		}
	}

	$limit='';
	if ($block_config['items_per_page']>0) {$limit=" limit $block_config[items_per_page]";}

	if ($block_config['content']=='0|1')
	{
		// both videos and albums
		if (isset($block_config['mode_categories']))
		{
			$data_tags=mr2array(sql("select category_id as tag_id, $database_selectors[generic_selector_title] as tag, $database_selectors[generic_selector_dir] as tag_dir, total_videos + total_albums as amount from $config[tables_prefix]categories where $database_selectors[where_categories] $where having amount>0 order by amount desc $limit"));
		} else {
			$data_tags=mr2array(sql("select tag_id, $database_selectors[generic_selector_tag] as tag, $database_selectors[generic_selector_tag_dir] as tag_dir, total_videos + total_albums as amount from $config[tables_prefix]tags where $database_selectors[where_tags] having amount>0 order by amount desc $limit"));
		}
	} elseif ($block_config['content']=='1')
	{
		// albums
		if (isset($block_config['mode_categories']))
		{
			$data_tags=mr2array(sql("select category_id as tag_id, $database_selectors[generic_selector_title] as tag, $database_selectors[generic_selector_dir] as tag_dir, total_albums as amount from $config[tables_prefix]categories where $database_selectors[where_categories] $where having amount>0 order by amount desc $limit"));
		} else {
			$data_tags=mr2array(sql("select tag_id, $database_selectors[generic_selector_tag] as tag, $database_selectors[generic_selector_tag_dir] as tag_dir, total_albums as amount from $config[tables_prefix]tags where $database_selectors[where_tags] having amount>0 order by amount desc $limit"));
		}
	} else {
		// videos
		if (isset($block_config['mode_categories']))
		{
			$data_tags=mr2array(sql("select category_id as tag_id, $database_selectors[generic_selector_title] as tag, $database_selectors[generic_selector_dir] as tag_dir, total_videos as amount from $config[tables_prefix]categories where $database_selectors[where_categories] $where having amount>0 order by amount desc $limit"));
		} else {
			$data_tags=mr2array(sql("select tag_id, $database_selectors[generic_selector_tag] as tag, $database_selectors[generic_selector_tag_dir] as tag_dir, total_videos as amount from $config[tables_prefix]tags where $database_selectors[where_tags] having amount>0 order by amount desc $limit"));
		}
	}

	if (@count($data_tags)<1) {return '';}

	$groups_amount=$block_config['size_to']-$block_config['size_from']+1;
	if ($groups_amount<1) {$groups_amount=1;}
	$step=count($data_tags)/$groups_amount;

	$size=$block_config['size_to'];
	$tag_in_group_amount=$step;

	$data=array();
	foreach ($data_tags as $v)
	{
		$temp['size']=$size;
		$temp['size_percent']=round(100*(1+($size-$block_config['size_from'])/$block_config['size_from']));
		$temp['tag_id']=$v['tag_id'];
		$temp['tag']=$v['tag'];
		$temp['title']=$v['tag'];
		$temp['tag_dir']=$v['tag_dir'];
		$temp['dir']=$v['tag_dir'];
		$temp['amount']=$v['amount'];
		if ($size>=intval($block_config['bold_from']))
		{
			$temp['is_bold']=1;
		} else {
			$temp['is_bold']=0;
		}

		$data[]=$temp;
		if (count($data)>=$tag_in_group_amount)
		{
			$size--;
			$tag_in_group_amount+=$step;
		}
	}

	$block_config['sort_by']=trim(strtolower($block_config['sort_by']));
	if ($block_config['sort_by']=='title asc' || $block_config['sort_by']=='title')
	{
		$data=kt_array_multisort($data,array(array('key'=>'tag','sort'=>'asc')));
	} elseif ($block_config['sort_by']=='title desc')
	{
		$data=kt_array_multisort($data,array(array('key'=>'tag','sort'=>'desc')));
	} elseif ($block_config['sort_by']=='amount asc' || $block_config['sort_by']=='amount')
	{
		$data=kt_array_multisort($data,array(array('key'=>'amount','sort'=>'asc')));
	} elseif ($block_config['sort_by']=='amount desc')
	{
		$data=kt_array_multisort($data,array(array('key'=>'amount','sort'=>'desc')));
	} else {
		shuffle($data);
	}
	$smarty->assign("data",$data);
	return '';
}

function tags_cloud_v2GetHash($block_config)
{
	return "";
}

function tags_cloud_v2CacheControl($block_config)
{
	return "default";
}

function tags_cloud_v2MetaData()
{
	return array(
		// pagination
		array("name"=>"items_per_page", "group"=>"pagination", "type"=>"INT", "is_required"=>1, "default_value"=>"0"),

		// sorting
		array("name"=>"sort_by", "group"=>"sorting", "type"=>"SORTING[title,amount]", "is_required"=>1, "default_value"=>"rand()"),

		// sizes
		array("name"=>"size_from", "group"=>"sizes", "type"=>"INT", "is_required"=>1, "default_value"=>"12"),
		array("name"=>"size_to",   "group"=>"sizes", "type"=>"INT", "is_required"=>1, "default_value"=>"19"),
		array("name"=>"bold_from", "group"=>"sizes", "type"=>"INT", "is_required"=>0, "default_value"=>"16"),

		// display modes
		array("name"=>"content", "group"=>"display_modes", "type"=>"CHOICE[0,1,0|1]", "is_required"=>0),

		// categories
		array("name"=>"mode_categories",          "group"=>"categories", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_with_avatar",    "group"=>"categories", "type"=>"", "is_required"=>0),
		array("name"=>"show_only_without_avatar", "group"=>"categories", "type"=>"", "is_required"=>0),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}
