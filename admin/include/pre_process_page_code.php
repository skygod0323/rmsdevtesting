<?php

/* Developed by Kernel Team.

   http://kernel-team.com



   All php code within this file will be executed before every website page is requested.

*/

global $smarty, $list_countries;

// Smarty modifiers

$smarty->register_modifier('country', 'country');

function country($id)
{
    global $list_countries;

    return $list_countries['name'][$id];
}

// Smarty variables

$availableCountries = mr2array_list(sql_pr("select af_custom1 from $config[tables_prefix]videos where af_custom1 <> 0 group by af_custom1"));
$availableCountries = array_filter(
    $list_countries['name'],
    function ($id) use ($availableCountries) {
        return in_array($id, $availableCountries);
    },
    ARRAY_FILTER_USE_KEY
);
define('list_available_countries', $availableCountries);

(function() use($config) {
	$idField = 'content_source_id';
	$titleField = 'title';
	$tokensAmountField = 'custom1';
	$statusField = 'status_id';
	$result = mr2array(sql_pr("select $idField, $titleField, $tokensAmountField from $config[tables_prefix]content_sources where $statusField <> 0"));
	$availableCharities = [];

	foreach ($result as $i) {
		$availableCharities[$i[$idField]] = $i[$titleField];
	}

	define('list_available_charities', $availableCharities);
})();
