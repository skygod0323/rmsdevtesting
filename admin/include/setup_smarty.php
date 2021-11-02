<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

if (!is_file("$config[project_path]/admin/smarty/Smarty.class.php"))
{
	die("[FATAL]: project_path directory is not specified correctly in /admin/include/setup.php");
}
require_once("$config[project_path]/admin/smarty/Smarty.class.php");

class mysmarty extends Smarty
{
	public function __construct()
	{
		global $config;

		parent::__construct();

		$this->template_dir="$config[project_path]/admin/template/";
		$this->compile_dir="$config[project_path]/admin/smarty/template-c/";
		$this->config_dir="$config[project_path]/admin/smarty/config/";
		$this->cache_dir="$config[project_path]/admin/smarty/cache/";
		$this->default_modifiers=array('escape_admin_area');
		$this->left_delimiter='{{';
		$this->right_delimiter='}}';
		$this->caching=false;
		$this->error_reporting=E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;

		if (!is_writable($this->compile_dir))
		{
			die("[FATAL]: smarty compile directory is not writable: /admin/smarty/template-c");
		}
	}
}