<?php

class Core 
{
	private static $instance;
	
	private function __construct(){}

	public function proccess()
 	{
        //to implement for http routing
	}
	
	
	public static function getInstance($configuration)
	{
		if(!self::$instance)
		{
			define('CORE_PTH', str_replace('\\', DIRECTORY_SEPARATOR , dirname(__FILE__)).DIRECTORY_SEPARATOR);
			define('CORE_PTH_APP_PLUGIN', CORE_PTH.'..' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR);
			
			define('CORE_PTH_DOMAIN', str_replace('\\', DIRECTORY_SEPARATOR, $configuration['document_root']));
			define('CORE_PTH_APP',str_replace('\\', DIRECTORY_SEPARATOR, $configuration['app_root']));

            define('CORE_PTH_CONFIGS', CORE_PTH_DOMAIN. '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR);

            define('CORE_PTH_TPL', CORE_PTH_APP  . 'skin' . DIRECTORY_SEPARATOR);
			
			if(!isset($configuration['core_dir_prefix']))
			{
				$configuration['core_dir_prefix'] = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname($_SERVER['SCRIPT_FILENAME'])).DIRECTORY_SEPARATOR;
			}

            include_once(CORE_PTH_APP . 'autoloader.php');

			self::$instance = new Core();
		}
		
		return self::$instance;
	}
}