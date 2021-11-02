<?php
function awebl_list_webcamsShow($block_config, $object_id)
{
	global $config, $smarty, $storage;

	if (!is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
	{
		return '';
	}
	require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";

	$items_per_page = intval($block_config['items_per_page']);
	if (isset($block_config['var_items_per_page']) && intval($_REQUEST[$block_config['var_items_per_page']]) > 0)
	{
		$items_per_page = intval($_REQUEST[$block_config['var_items_per_page']]);
	}

	$awebl_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/awe_black_label/data.dat"));
	if ($awebl_data['niche'])
	{
		$niche = $awebl_data['niche'];
	} else
	{
		$niche = $block_config['niche'];
		if (isset($_REQUEST[$block_config['var_niche']]) && trim($_REQUEST[$block_config['var_niche']]))
		{
			$niche = trim($_REQUEST[$block_config['var_niche']]);
		}
	}
	if (!in_array($niche, ['girls', 'boys', 'tranny', 'celebrity']))
	{
		$niche = 'girls';
	}

	if (isset($block_config['mode_related']))
	{
		if (trim($_REQUEST[$block_config['var_webcam_dir']]))
		{
			$params = ['category' => $niche, 'itemCount' => $items_per_page, 'currentPerformerNick' => trim($_REQUEST[$block_config['var_webcam_dir']])];
			$models = awe_black_labelQueryAPI('GET', 'recommendations', $params);

			$storage[$object_id]['list_type'] = "related";
			$smarty->assign('list_type', "related");
		} else
		{
			return '';
		}
	} else
	{
		$params = ['category' => $niche, 'pageSize' => $items_per_page];

		$list_page_id = '';
		if (isset($block_config['var_list_page_id']) && trim($_REQUEST[$block_config['var_list_page_id']]))
		{
			$list_page_id = trim($_REQUEST[$block_config['var_list_page_id']]);
		}

		if (isset($block_config['var_search']) && trim($_REQUEST[$block_config['var_search']]))
		{
			$q = trim(process_blocked_words(trim($_REQUEST[$block_config['var_search']]), false));
			$q = trim(str_replace('[dash]', '-', str_replace('-', ' ', str_replace('--', '[dash]', str_replace('?', '', $q)))));
			$params['searchText'] = strtolower($q);

			$storage[$object_id]['list_type'] = "search";
			$storage[$object_id]['search_keyword'] = $q;
			$smarty->assign('list_type', "search");
			$smarty->assign('search_keyword', $q);
		}

		if ($list_page_id)
		{
			$params['listPageId'] = $list_page_id;
			$models = awe_black_labelQueryAPI('GET', 'show-more', $params);
		} else
		{
			$models = awe_black_labelQueryAPI('GET', 'performers', $params);
		}
	}
	foreach ($models['data']['performers'] as $k => $v)
	{
		$models['data']['performers'][$k]['willingness'] = ($v['willingness'] ? array_map('trim', explode(',', $v['willingness'])) : []);
		$models['data']['performers'][$k]['language'] = ($v['language'] ? array_map('trim', explode(',', $v['language'])) : []);
		$models['data']['performers'][$k]['liveFeedPlayerUrl'] = str_replace('[--PERFORMER_NICK--]', $v['nick'], $models['data']['liveFeedPlayerUrl']);
	}

	$smarty->assign('data', $models['data']['performers']);

	$smarty->assign('niche', $niche);
	$smarty->assign('list_page_id', $models['data']['listPageId']);
	$smarty->assign('is_last_page', intval($models['data']['isLastPage']));
	$smarty->assign("items_per_page", $block_config['items_per_page']);
	$smarty->assign("var_list_page_id", $block_config['var_list_page_id']);

	$storage[$object_id]['niche'] = $niche;
	$storage[$object_id]['list_page_id'] = $models['data']['listPageId'];
	$storage[$object_id]['is_last_page'] = intval($models['data']['isLastPage']);
	$storage[$object_id]['items_per_page'] = $block_config['items_per_page'];
	$storage[$object_id]['var_list_page_id'] = $block_config['var_list_page_id'];

	header('X-BL-Type: KVS');
	return '';
}

function awebl_list_webcamsGetHash($block_config)
{
	return "nocache";
}

function awebl_list_webcamsCacheControl($block_config)
{
	return "nocache";
}

function awebl_list_webcamsMetaData()
{
	return array(
		// pagination
			array("name" => "items_per_page",     "group" => "pagination", "type" => "INT",    "is_required" => 1, "default_value" => "24"),
			array("name" => "var_list_page_id",   "group" => "pagination", "type" => "STRING", "is_required" => 0, "default_value" => "list_page_id"),
			array("name" => "var_items_per_page", "group" => "pagination", "type" => "STRING", "is_required" => 0, "default_value" => "items_per_page"),

		// static filters
			array("name" => "niche", "group" => "static_filters", "type" => "CHOICE[girls,boys,tranny,celebrity]", "is_required" => 1, "default_value" => "girls"),

		// dynamic filters
			array("name" => "var_niche",  "group" => "dynamic_filters", "type" => "STRING", "is_required" => 0, "default_value" => "niche"),
			array("name" => "var_search", "group" => "dynamic_filters", "type" => "STRING", "is_required" => 0, "default_value" => "q"),

		// related
			array("name" => "mode_related",   "group" => "related", "type" => "",       "is_required" => 0),
			array("name" => "var_webcam_dir", "group" => "related", "type" => "STRING", "is_required" => 0, "default_value" => "dir"),
	);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
