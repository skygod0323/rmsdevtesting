<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
if (!isset($config))
{
	require_once("setup.php");
	$display_errors = 1;
}

if (isset($_SERVER['HTTP_ORIGIN']))
{
	header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	header("Access-Control-Allow-Credentials: true");

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
	{
		die;
	}
}

$file_upload_data = unserialize(@file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"));
if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] != 'public')
{
	session_start();
	if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'admins' && intval($_SESSION['userdata']['user_id']) < 1)
	{
		header('HTTP/1.0 403 Forbidden');
		die;
	}
	if (intval($_SESSION['userdata']['user_id']) < 1)
	{
		if ($file_upload_data['FILE_UPLOAD_DISK_OPTION'] == 'members' && intval($_SESSION['user_id']) < 1)
		{
			header('HTTP/1.0 403 Forbidden');
			die;
		}
	}
}

if (preg_match('/^([0-9A-Za-z]{32})$/', $_POST['filename']))
{
	if (intval($_FILES['content']['error']) > 0)
	{
		if (intval($_FILES['content']['error']) == 1)
		{
			file_put_contents("$config[temporary_path]/$_POST[filename].status", "<status><error>ktfudc_filesize_error</error></status>");
		} else
		{
			file_put_contents("$config[temporary_path]/$_POST[filename].status", "<status><error>ktfudc_unexpected_error</error></status>");
		}
		log_output("Filename $_POST[filename] uploading error: code " . $_FILES['content']['error']);
	} else
	{
		move_uploaded_file($_FILES['content']['tmp_name'], "$config[temporary_path]/$_POST[filename].tmp");
		if (is_file("$config[temporary_path]/$_POST[filename].tmp"))
		{
			chmod("$config[temporary_path]/$_POST[filename].tmp", 0666);
			$uploaded_file_size = sprintf("%.0f", filesize("$config[temporary_path]/$_POST[filename].tmp"));
			file_put_contents("$config[temporary_path]/$_POST[filename].status", "<status><loaded>$uploaded_file_size</loaded><total>$uploaded_file_size</total></status>");
		} else
		{
			file_put_contents("$config[temporary_path]/$_POST[filename].status", "<status><error>ktfudc_unexpected_error</error></status>");
		}
	}
} else
{
	if (preg_match('/^([0-9A-Za-z]{32})$/', $_GET['filename']))
	{
		file_put_contents("$config[temporary_path]/$_GET[filename].status", "<status><error>ktfudc_filesize_error</error></status>");
		log_output("Empty POST: " . print_r($_POST, true));
	} else
	{
		log_output("Filename $_GET[filename] is not accepted");
	}
}

function log_output($message)
{
	global $config, $display_errors;

	file_put_contents("$config[project_path]/admin/logs/uploader.txt", date("[Y-m-d H:i:s] ") . $message . "\n", FILE_APPEND | LOCK_EX);
	if ($display_errors == 1)
	{
		echo $message;
	}
}
