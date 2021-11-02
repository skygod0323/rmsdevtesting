<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if (isset($_SERVER['HTTP_ORIGIN']))
{
	header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
	header("Access-Control-Allow-Credentials: true");

	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
	{
		die;
	}
}

require_once "setup.php";

$file_name = $_REQUEST['file'];
if (!preg_match('/^([0-9A-Za-z]{32})$/', $file_name))
{
	header('HTTP/1.0 404 Not found');
	die;
}

function get_upload_status_format_response(array $response): void
{
	if (!is_array($response))
	{
		$response = ['error' => 'ktfudc_unexpected_error'];
	}
	if ($_REQUEST['format'] == 'json')
	{
		$response['status'] = $response['error'] ? 'failure' : 'success';
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($response);
	} else
	{
		header('Content-Type: text/xml; charset=utf8');
		echo '<status>';
		if (isset($response['error']))
		{
			echo '<error>' . trim($response['error']) . '</error>';
		} else
		{
			echo '<loaded>' . trim($response['loaded']) . '</loaded>';
			echo '<total>' . trim($response['total']) . '</total>';
			if (isset($response['filename']))
			{
				echo '<filename>' . trim(str_replace('&', '&amp;', $response['filename'])) . '</filename>';
			}
		}
		echo '</status>';
	}
	die;
}


if (is_file("$config[temporary_path]/$file_name.status"))
{
	$response = file_get_contents("$config[temporary_path]/$file_name.status");
	if (strpos($response, '<status>') !== false)
	{
		header('Content-Type: text/xml; charset=utf8');
	} else
	{
		header('Content-Type: application/json; charset=utf8');
	}
	echo $response;
	die;
} elseif (is_file("$config[temporary_path]/{$file_name}_wget_log.txt"))
{
	$wget_log = file_get_contents("$config[temporary_path]/{$file_name}_wget_log.txt");
	if (strpos($wget_log, 'ERROR 4') !== false || strpos($wget_log, 'ERROR 5') !== false || strpos($wget_log, 'unable to resolve host address') !== false)
	{
		get_upload_status_format_response(['error' => 'ktfudc_url_error']);
		die;
	}

	unset($temp);
	preg_match("|Content-Disposition[^;]*?;\ *filename\ *=\ *['\"]*(.*?)['\"]*(\r?\n)|is", $wget_log, $temp);
	$filename = trim($temp[1]);
	if ($filename == '')
	{
		$filename = substr($wget_log, 0, strpos($wget_log, "\n"));
		$filename = pathinfo(trim($filename, '/'), PATHINFO_BASENAME);
		if (strpos($filename, '.') === false)
		{
			unset($temp);
			if (preg_match("|Location:\ *(.*?)\r?\n|is", $wget_log, $temp))
			{
				$filename = $temp[1];
				$filename = pathinfo(trim($filename, '/'), PATHINFO_BASENAME);
			}
		}
		if (strpos($filename, "?") !== false)
		{
			$filename = substr($filename, 0, strpos($filename, "?"));
		}
		$filename = urldecode($filename);
	} else
	{
		$filename = preg_replace_callback('/\\\\[0-7]{3}/', static function ($x) {
			return chr(octdec($x[0]));
		}, $filename);
	}

	$last_dot_pos = strrpos($filename, '.');
	if ($last_dot_pos !== false)
	{
		$ext = substr($filename, $last_dot_pos + 1);
		$filename = str_replace('.', '_', substr($filename, 0, $last_dot_pos));
		$filename = "$filename.$ext";
	}

	unset($temp);
	$total = 0;
	preg_match_all('/Length: ([0-9]+)/', $wget_log, $temp);
	settype($temp[1], "array");
	if (count($temp[1]) > 0)
	{
		foreach ($temp[1] as $v)
		{
			if (intval($v) > $total)
			{
				$total = intval($v);
			}
		}
	}
	if ($total > 0)
	{
		$loaded = 0;
		$wget_log = strrev($wget_log);
		unset($temp);
		preg_match('/%([0-9]+)/', $wget_log, $temp);
		if (intval(strrev($temp[1])) == 100)
		{
			$file_upload_data = unserialize(file_get_contents("$config[project_path]/admin/data/system/file_upload_params.dat"), ['allowed_classes' => false]);
			if (intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) > 0 && $total > intval($file_upload_data['FILE_UPLOAD_SIZE_LIMIT']) * 1024 * 1024)
			{
				get_upload_status_format_response(['error' => 'ktfudc_filesize_error']);
				die;
			}
			get_upload_status_format_response(['loaded' => $total, 'total' => $total, 'filename' => $filename]);
			die;
		} else
		{
			unset($temp);
			preg_match('/K([0-9]+)/', $wget_log, $temp);
			if (intval(strrev($temp[1])) > 0)
			{
				$loaded = intval(strrev($temp[1])) * 1024;
			}
			get_upload_status_format_response(['loaded' => $loaded, 'total' => $total, 'filename' => $filename]);
			die;
		}
	} else
	{
		get_upload_status_format_response(['loaded' => 0, 'total' => 1]);
		die;
	}
}

get_upload_status_format_response(['loaded' => 0, 'total' => 1]);
