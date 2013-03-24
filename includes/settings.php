<?php
	define('SMARTY_DIR', '/usr/local/lib/Smarty/libs/');
	require_once(SMARTY_DIR.'Smarty.class.php');

	class Smarty_Musshop extends Smarty
	{

		function Smarty_Musshop()
		{
			$dir = '/home/mark/development/web/musstock/';

			parent::__construct();

			$this->setTemplateDir($dir.'templates/');
			$this->setCompileDir($dir.'templates_c/');
			$this->setConfigDir($dir.'configs/');
			$this->setCacheDir($dir.'cache/');

			// $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
			$this->assign('app_name', 'Music Shop');
		}
	}

	class DBSettings
	{
		var $host = "localhost";
		var $user = "admin";
		var $password = "admin107";
		var $database = "musshop";
		var $encode = "utf8";
	}

	class Settings
	{
		var $db;


		function __construct()
		{
			$this->db = new DBSettings();
		}
	}

	$settings = new Settings();
?>