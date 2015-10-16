<?php
include_once('../app/code/core/Core.php');

/**
 * Shell scripts abstract class
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
abstract class ShellAbstract
{

    /**
     * Input arguments
     *
     * @var array
     */
    protected $_args = array();

    /**
     * Factory instance
     *
     * @var FactoryModel
     */
    protected $_factory;

    /**
     * Application instance
     *
     * @var Core
     */
    protected $_app;

    /**
     * Initialize application and parse input parameters
     *
     */
    public function __construct()
    {
        $this->_app = Core::getInstance(
            array('app' => 'shell'
                  , 'document_root' => dirname(__FILE__).'/'
                  , 'app_root' => dirname(__FILE__).'/../app/'
            )
        );
        $this->_factory = FactoryModel::getInstance();

        $this->_applyPhpVariables();
        $this->_parseArgs();
        $this->_validate();
        $this->_showHelp();
    }

    /**
     * Parse .htaccess file and apply php settings to shell script
     *
     */
    protected function _applyPhpVariables()
    {
        $htaccess = CORE_PTH_APP . '.htaccess';
        if (file_exists($htaccess)) {
            // parse htaccess file
            $data = file_get_contents($htaccess);
            $matches = array();
            preg_match_all('#^\s+?php_value\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
            preg_match_all('#^\s+?php_flag\s+([a-z_]+)\s+(.+)$#siUm', $data, $matches, PREG_SET_ORDER);
            if ($matches) {
                foreach ($matches as $match) {
                    @ini_set($match[1], str_replace("\r", '', $match[2]));
                }
            }
        }
    }

    /**
     * Parse input arguments
     *
     * @return ShellAbstract
     */
    protected function _parseArgs()
    {
        $current = null;
        foreach ($_SERVER['argv'] as $arg) {
            $match = array();
            if (preg_match('#^--([\w\d_-]{1,})$#', $arg, $match) || preg_match('#^-([\w\d_]{1,})$#', $arg, $match)) {
                $current = $match[1];
                $this->_args[$current] = true;
            } else {
                if ($current) {
                    $this->_args[$current] = $arg;
                } else if (preg_match('#^([\w\d_]{1,})$#', $arg, $match)) {
                    $this->_args[$match[1]] = true;
                }
            }

        }
        return $this;
    }

    /**
     * Validate arguments
     *
     */
    protected function _validate()
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            die('This script cannot be run from Browser. This is the shell script.');
        }
    }

    /**
     * Run script
     *
     */
    abstract public function run();

    /**
     * Check is show usage help
     *
     */
    protected function _showHelp()
    {
        if (isset($this->_args['h']) || isset($this->_args['help'])) {
            die($this->usageHelp());
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f script.php -- [options]

  -h            Short alias for help
  help          This help
USAGE;
    }

    /**
     * Retrieve argument value by name or false
     *
     * @param string $name the argument name
     * @return mixed
     */
    public function getArg($name)
    {
        if (isset($this->_args[$name])) {
            return $this->_args[$name];
        }
        return false;
    }
}
