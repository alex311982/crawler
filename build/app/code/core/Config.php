<?php

/**
 * Config class
 *
 * @author Gubarev Alex
 */
class Config
{
    public $cache_data = array();

    protected static $inst;

    /**
     * Call this method to get singleton
     *
     * @return Config
     */
    public static function getInstance()
    {
        if (self::$inst === null) {
            self::$inst = new self();
        }
        return self::$inst;
    }
	
	public function get()
	{
		if(isset($this->cache_data[serialize(func_get_args())]))
		{
			return $this->cache_data[serialize(func_get_args())];
		}
		
		$args = func_get_args();
		$config_name = strtolower(array_shift($args));

        $result = NULL;
		if (file_exists(CORE_PTH_CONFIGS.$config_name.'.php'))
		{
			include(CORE_PTH_CONFIGS.$config_name.'.php');
            if (!isset($data)) return null;

			$result = $this->getVal($data, $args);
		}

        $this->cache_data[serialize(func_get_args())] = $result;
		return $result; 
	}

	protected function getVal($data, $keys)
	{
		while($key = array_shift($keys))
		{
			if(isset($data[$key]))
			{
				$data = $data[$key];
			} else
			{
				return null;
			}
		}
		
		return $data;
	}

    /**
     * Construct of class
     *
     */
    private function __construct() {}

    /**
     * Magic method of class
     *
     */
    private function __clone() {}
}
