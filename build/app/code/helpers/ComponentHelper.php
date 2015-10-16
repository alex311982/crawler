<?php

class ComponentHelper
{
	public static function factory($className)
	{
		$className = ucwords($className);

        $object = new $className();

        return $object;
	 }
	 
    public function execute($action, &$data)
    {
        $this->action = strtolower($action);

        if(!$action)
        {
            $action = 'Index';
        }

        $this->tpl = '_component_'.strtolower(str_replace('component','',get_class($this))).'_'.strtolower($action).'.tpl';

        $act = explode('_',$action);
        $action = 'execute';
        foreach($act as $item)
        {
            $action .= ucwords($item);
        }
        unset($act);

        $this->$action($data);
    }
}