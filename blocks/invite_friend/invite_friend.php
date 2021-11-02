<?php
function invite_friendShow($block_config, $object_id)
{
	global $smarty;

	if ($_POST['action'] == 'send')
	{
		$errors = ['code' => 1];
		$errors_async = [['error_field_name' => 'code', 'error_code' => 'required', 'block' => 'invite_friend']];

		if ($_POST['mode'] == 'async')
		{
			async_return_request_status($errors_async);
		}

		$smarty->assign('errors', $errors);
	}
	return '';
}

function invite_friendGetHash($block_config)
{
	return "nocache";
}

function invite_friendCacheControl($block_config)
{
	return "nocache";
}

function invite_friendMetaData()
{
	return [];
}

if ($_SERVER['argv'][1] == 'test' && $_SERVER['DOCUMENT_ROOT'] == '')
{
	echo "OK";
}
