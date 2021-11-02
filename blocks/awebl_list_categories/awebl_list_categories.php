<?php
function awebl_list_categoriesShow($block_config, $object_id)
{
	global $config, $smarty, $storage;

	if (!is_file("$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php"))
	{
		return '';
	}
	require_once "$config[project_path]/admin/plugins/awe_black_label/awe_black_label.php";

	$group_names = ['Category', 'Show type', 'Price', 'Willingness', 'Language', 'Age', 'Ethnicity', 'Appearance', 'Hair', 'Other'];
	$grouping_ref = json_decode('[["girl", "boy", "gay", "tranny", "amateur", "lesbian", "mature", "fetish", "couple", "group", "hot flirt", "birthday", "porn star", "newbie", "cosplay", "dancer", "dj", "fashion", "fitness", "love life adviser", "soul mate"],["hd quality", "with audio", "two-way audio", "vip show", "free chat", "private chat"],["sale", "1.99-2.99", "2.99-3.99", "3.99-4.99", "4.99-9.99"],["anal", "striptease", "smoking", "squirt", "close up", "fingering", "dom", "sub", "oil", "strapon", "vibrator", "snapshot", "swallow", "blowjob", "69", "double penetration", "love balls", "footjob", "roleplay", "dildo", "dancing", "cameltoe", "butt plug", "deepthroat", "toys", "live orgasm", "cumshot"],["english", "german", "spanish", "french", "italian"],["18 22", "twenties", "milf", "middle aged", "senior"],["asian", "ebony", "white", "latin"],["petite", "bbw", "shaved", "hairy", "piercing", "tattoo", "stockings", "leather", "tiny tits", "big tits", "skinny", "muscular", "small penis", "big penis"],["black hair", "blonde", "brunette", "redhead", "short hair", "long hair", "bald"]]', true);
	$grouping = [];
	foreach ($grouping_ref as $k => $group_categories)
	{
		foreach ($group_categories as $item)
		{
			$grouping[mb_lowercase($item)] = $group_names[$k];
		}
	}

	$awebl_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/awe_black_label/data.dat"));
	$awebl_data['language_code'] = 'en';
	$ref_filters = awe_black_labelQueryAPI('GET', 'filters', [], false, $awebl_data);
	foreach ($ref_filters['data']['categories'] as $niche => $categories)
	{
		foreach ($categories as $index => $category)
		{
			$ref_filters['data']['categories'][$niche][$index] = $grouping[mb_lowercase($category)] ?? '';
		}
	}

	$filters = awe_black_labelQueryAPI('GET', 'filters');

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

	$res_categories_grouped = [];
	foreach ($group_names as $group_title)
	{
		$res_categories_grouped[$group_title] = ['title' => $group_title, 'categories' => []];
	}
	foreach ($filters['data']['categories'][$niche] as $index => $category_title)
	{
		$group_title = $ref_filters['data']['categories'][$niche][$index] ?: 'Other';
		$res_categories_grouped[$group_title]['categories'][] = $category_title;
	}

	$smarty->assign('data', $res_categories_grouped);

	$smarty->assign('niche', $niche);
	$storage[$object_id]['niche'] = $niche;

	if (isset($block_config['disable_girls']) || ($awebl_data['niche'] && $awebl_data['niche'] != 'girls'))
	{
		$smarty->assign('disable_girls', 'true');
	}
	if (isset($block_config['disable_boys']) || ($awebl_data['niche'] && $awebl_data['niche'] != 'boys'))
	{
		$smarty->assign('disable_boys', 'true');
	}
	if (isset($block_config['disable_tranny']) || ($awebl_data['niche'] && $awebl_data['niche'] != 'tranny'))
	{
		$smarty->assign('disable_tranny', 'true');
	}
	if (isset($block_config['disable_celebrity']) || ($awebl_data['niche'] && $awebl_data['niche'] != 'celebrity'))
	{
		$smarty->assign('disable_celebrity', 'true');
	}

	return '';
}

function awebl_list_categoriesGetHash($block_config)
{
	global $config;

	$awebl_data = @unserialize(file_get_contents("$config[project_path]/admin/data/plugins/awe_black_label/data.dat"));

	$var_niche = trim($_REQUEST[$block_config['var_niche']]);
	$language = trim($awebl_data['language_code']);
	$prefix = trim($_SESSION['awebl_prefix']);
	return "$var_niche|$language|$prefix";
}

function awebl_list_categoriesCacheControl($block_config)
{
	return 'static';
}

function awebl_list_categoriesMetaData()
{
	return array(
		// static filters
			array("name" => "niche",             "group" => "static_filters", "type" => "CHOICE[girls,boys,tranny,celebrity]", "is_required" => 1, "default_value" => "girls"),
			array("name" => "disable_girls",     "group" => "static_filters", "type" => ""),
			array("name" => "disable_boys",      "group" => "static_filters", "type" => ""),
			array("name" => "disable_tranny",    "group" => "static_filters", "type" => ""),
			array("name" => "disable_celebrity", "group" => "static_filters", "type" => ""),

		// dynamic filters
			array("name" => "var_niche", "group" => "dynamic_filters", "type" => "STRING", "is_required" => 0, "default_value" => "niche"),
	);
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
