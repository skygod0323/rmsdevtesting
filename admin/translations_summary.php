<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once('include/setup.php');
require_once('include/setup_smarty.php');
require_once('include/functions_base.php');
require_once('include/functions.php');
require_once('include/check_access.php');

// =====================================================================================================================
// initialization
// =====================================================================================================================

$table_name = "$config[tables_prefix]languages";

$scopes = array(1 => 'videos', 2 => 'albums', 3 => 'content_sources', 4 => 'models', 5 => 'dvds', 6 => 'categories', 7 => 'categories_groups', 8 => 'content_sources_groups', 9 => 'tags', 10 => 'dvds_groups', 14 => 'models_groups');
$selectors = array();
$selector_top = '';
$list_languages = mr2array(sql("select * from $table_name"));
foreach ($list_languages as $language)
{
	foreach ($scopes as $k => $v)
	{
		if ($v == 'tags')
		{
			if ($language['is_directories_localize'] == 1)
			{
				$selectors[$k] .= ", case when tag_$language[code]='' or tag_dir_$language[code]='' then 0 else 1 end as `$language[code]`";
			} else
			{
				$selectors[$k] .= ", case when tag_$language[code]='' then 0 else 1 end as `$language[code]`";
			}
		} elseif ($language["translation_scope_$v"] == 0)
		{
			if ($language['is_directories_localize'] == 1)
			{
				$selectors[$k] .= ", case when title_$language[code]='' or dir_$language[code]='' or (description<>'' and description_$language[code]='') then 0 else 1 end as `$language[code]`";
			} else
			{
				$selectors[$k] .= ", case when title_$language[code]='' or (description<>'' and description_$language[code]='') then 0 else 1 end as `$language[code]`";
			}
		} else
		{
			if ($language['is_directories_localize'] == 1)
			{
				$selectors[$k] .= ", case when title_$language[code]='' or dir_$language[code]='' then 0 else 1 end as `$language[code]`";
			} else
			{
				$selectors[$k] .= ", case when title_$language[code]='' then 0 else 1 end as `$language[code]`";
			}
		}
	}
	$selector_top .= ", sum(`$language[code]`) as `$language[code]`";
}

// =====================================================================================================================
// list items
// =====================================================================================================================

$data = array();
$data['videos'] = mr2array_single(sql("select 1 as object_type, count(*) as total $selector_top from (select video_id $selectors[1] from $config[tables_prefix]videos) x"));
$data['albums'] = mr2array_single(sql("select 2 as object_type, count(*) as total $selector_top from (select album_id $selectors[2] from $config[tables_prefix]albums) x"));
$data['categories'] = mr2array_single(sql("select 6 as object_type, count(*) as total $selector_top from (select category_id $selectors[6] from $config[tables_prefix]categories) x"));
$data['categories_groups'] = mr2array_single(sql("select 7 as object_type, count(*) as total $selector_top from (select category_group_id $selectors[7] from $config[tables_prefix]categories_groups) x"));
$data['content_sources'] = mr2array_single(sql("select 3 as object_type, count(*) as total $selector_top from (select content_source_id $selectors[3] from $config[tables_prefix]content_sources) x"));
$data['content_sources_groups'] = mr2array_single(sql("select 8 as object_type, count(*) as total $selector_top from (select content_source_group_id $selectors[8] from $config[tables_prefix]content_sources_groups) x"));
$data['models'] = mr2array_single(sql("select 4 as object_type, count(*) as total $selector_top from (select model_id $selectors[4] from $config[tables_prefix]models) x"));
$data['models_groups'] = mr2array_single(sql("select 14 as object_type, count(*) as total $selector_top from (select model_group_id $selectors[4] from $config[tables_prefix]models_groups) x"));
$data['tags'] = mr2array_single(sql("select 9 as object_type, count(*) as total $selector_top from (select tag_id $selectors[9] from $config[tables_prefix]tags) x"));
$data['dvds'] = mr2array_single(sql("select 5 as object_type, count(*) as total $selector_top from (select dvd_id $selectors[5] from $config[tables_prefix]dvds) x"));
$data['dvds_groups'] = mr2array_single(sql("select 10 as object_type, count(*) as total $selector_top from (select dvd_group_id $selectors[10] from $config[tables_prefix]dvds_groups) x"));
foreach ($data as $k => $v)
{
	if ($data[$k]['total'] > 0)
	{
		foreach ($list_languages as $language)
		{
			$data[$k]["$language[code]_pc"] = number_format(100 * floatval($data[$k]["$language[code]"]) / floatval($data[$k]['total']), 0);
		}
	} else
	{
		foreach ($list_languages as $language)
		{
			$data[$k]["$language[code]_pc"] = '0';
		}
	}
}

// =====================================================================================================================
// display
// =====================================================================================================================

$smarty = new mysmarty();
$smarty->assign('list_languages', $list_languages);
$smarty->assign('left_menu', 'menu_administration.tpl');

$smarty->assign('data', $data);
$smarty->assign('lang', $lang);
$smarty->assign('config', $config);
$smarty->assign('page_name', $page_name);
$smarty->assign('table_key_name', $table_key_name);
$smarty->assign('total_num', count($data));
$smarty->assign('template', str_replace(".php", ".tpl", $page_name));

$smarty->assign('page_title', $lang['settings']['submenu_option_translations_summary']);

$smarty->display("layout.tpl");
