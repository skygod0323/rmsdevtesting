<?php

/*
1) Choose block ID, e.g. "block_id"

2) Create /blocks/block_id folder

3) Copy this file into /blocks/block_id/block_id.php

4) Create /blocks/block_id/block_id.dat with the following content:

<block>
	<block_name>Your Block Name</block_name>
	<author>Block Author</author>
	<version>Block version</version>
</block>

5) Replace "block_template" with your block ID globally in this file

6) Add your logic

7) Create /blocks/block_id/langs/english.php with the following PHP code:

<?php

$lang['block_template']['block_short_desc'] = "Short desc";
$lang['block_template']['block_desc'] = "Long desc";

$lang['block_template']['params']['bool_parameter'] = "Bool parameter description";
$lang['block_template']['params']['int_parameter'] = "Int parameter description";
$lang['block_template']['params']['int_list_parameter'] = "Int list parameter description";
$lang['block_template']['params']['int_pair_parameter'] = "Int pair parameter description";
$lang['block_template']['params']['string_parameter'] = "String parameter description";
$lang['block_template']['params']['choice_parameter'] = "Choice parameter description";
$lang['block_template']['params']['sorting_parameter'] = "Sorting parameter description";

$lang['block_template']['values']['choice_parameter']['1'] = "Choice option 1";
$lang['block_template']['values']['choice_parameter']['2'] = "Choice option 2";
$lang['block_template']['values']['choice_parameter']['3'] = "Choice option 3";
$lang['block_template']['values']['sorting_parameter']['field1'] = "Sorting field 1";
$lang['block_template']['values']['sorting_parameter']['field2'] = "Sorting field 2";
$lang['block_template']['values']['sorting_parameter']['field3'] = "Sorting field 3";
$lang['block_template']['values']['sorting_parameter']['rand()'] = "Random sorting (auto added)";

*/

function block_templateShow($block_config,$object_id)
{
	global $config,$smarty,$storage,$page_id;

	// select data from database or process form post
	// ...

	// assign variable to smarty that can be used in block template:
	$smarty->assign('var_name','var_value');

	// assign variable to storage that can be used in page template, where this block is inserted:
	$storage[$object_id]['var_name']='var_value';

	// if success, return empty string
	// if block should trigger 404 error, return "status_404"
	// if block should trigger 301 redirect, return "status_301:http://domain.com/url/to/redirect"
	return '';
}

function block_templateAsync($block_config)
{
	// process any ajax call to this block, e.g.
	// http://domain.com/page.php?mode=async&action=action_name

	if ($_REQUEST['action'] == 'action_name')
	{
		// process action
		// ...

		// return result and finish engine processing
		header("Content-type: application/json");
		echo json_encode(array('status' => 'success'));
		die;
	}
}

function block_templateGetHash($block_config)
{
	// return list of values of all var-variables supported by this block, e.g.:
	// $var_category_dir=trim($_REQUEST[$block_config['var_category_dir']]);
	// $var_category_id=intval($_REQUEST[$block_config['var_category_id']]);
	// return "$var_category_dir|$var_category_id";

	// if block should be cached differently for different members, add $_SESSION[user_id], e.g.:
	// return "$_SESSION[user_id]|$var_category_dir|$var_category_id";

	// if block should be cached differently for members of different statuses, add $_SESSION[status_id], e.g.:
	// return "$_SESSION[status_id]|$var_category_dir|$var_category_id";

	// if block should not be cached (e.g. form processing), return "nocache"

	// you can define complex logic here, e.g. example for no caching for existing members:
	// if ($_SESSION['user_id']>0) {
	//     return "nocache";
	// } else {
	//     return "$var_category_dir|$var_category_id";
	// }
	return "";
}

function block_templateCacheControl($block_config)
{
	// return "nocache" if block is never cached
	// return "user_nocache" if block is never cached for existing members
	// return "user_specific" if block is cached differently for different members
	// return "status_specific" if block is cached differently for members of different statuses
	// return "default" in all other cases
	return "default";
}

function block_templateMetaData()
{
	// return list of supported block parameters that you can modify in admin panel
	return array(
		array("name"=>"bool_parameter",     "type"=>"",                              "is_required"=>0),
		array("name"=>"int_parameter",      "type"=>"INT",                           "is_required"=>0, "default_value"=>"1"),
		array("name"=>"int_list_parameter", "type"=>"INT_LIST",                      "is_required"=>0, "default_value"=>"1,2"),
		array("name"=>"int_pair_parameter", "type"=>"INT_PAIR",                      "is_required"=>0, "default_value"=>"1/2"),
		array("name"=>"string_parameter",   "type"=>"STRING",                        "is_required"=>0, "default_value"=>"string"),
		array("name"=>"choice_parameter",   "type"=>"CHOICE[1,2,3]",                 "is_required"=>0, "default_value"=>"2"),
		array("name"=>"sorting_parameter",  "type"=>"SORTING[field1,field2,field3]", "is_required"=>0, "default_value"=>"field1"),
	);
}

if ($_SERVER['argv'][1]=='test' && $_SERVER['DOCUMENT_ROOT']=='') {echo "OK";}