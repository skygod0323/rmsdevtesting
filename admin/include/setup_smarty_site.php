<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/
require_once("$config[project_path]/admin/smarty/Smarty.class.php");

class mysmarty_site extends Smarty
{
	public function __construct()
	{
		global $config;

		parent::__construct();

		$this->template_dir="$config[project_path]/template/";
		$this->compile_dir="$config[project_path]/admin/smarty/template-c-site/";
		$this->config_dir="$config[project_path]/admin/smarty/config/";
		$this->cache_dir="$config[project_path]/admin/smarty/cache/";
		$this->default_modifiers=array('escape_ss');
		$this->left_delimiter='{{';
		$this->right_delimiter='}}';
		$this->caching=false;
		$this->error_reporting=E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;

		if (!is_writable($this->compile_dir))
		{
			die("[FATAL]: smarty compile directory is not writable: /admin/smarty/template-c-site");
		}
		if (!is_writable($this->cache_dir))
		{
			die("[FATAL]: smarty cache directory is not writable: /admin/smarty/cache");
		}
	}
}