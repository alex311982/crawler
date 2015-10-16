<?php

/**
 * Factory class
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class FactoryModel extends ModelAbstract
{

    /**
     * Factory instance
     *
     * @var FactoryModel
     */
    protected static $inst;

    /**
     * Call this method to get singleton
     *
     * @return FactoryModel
     */
    public static function getInstance()
    {
        if (self::$inst === null) {
            self::$inst = new self();
        }
        return self::$inst;
    }

    /**
     * Retrieve model object
     *
     * @param string        $modelClass
     * @param array|object  $arguments
     * @return bool|ModelAbstract
     */
    public function getModel($modelClass = '', $arguments = array())
    {
        if (class_exists($modelClass)) {
            $obj = new $modelClass($arguments);
            return $obj;
        } else {
            return false;
        }
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
