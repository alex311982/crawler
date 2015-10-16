<?php

/**
 * Template model
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class TemplateModel extends ModelAbstract
{

    /**
     * Template settings
     */
    const TEMPLATE_HTTP_STATUS = '200';
    const TEMPLATE_NAME = 'overall';

    private $vars = array();

    public function __construct(){}

    public function sendHeaders()
    {
        header($_SERVER['SERVER_PROTOCOL'].' '.$this->http_status);
        header('Content-type: text/html; charset=utf-8', true);
    }

    public function fetch($template)
    {
        $template = !empty($template)
            ? (string)$template
            : self::TEMPLATE_NAME;

        if(isset($this->vars['http_status']))
        {
            $this->http_status = $this->vars['http_status'];
        }

        foreach($this->vars as $key=>&$var)
        {
            $$key = $var;
            unset($var);
        }
        unset($this->vars);

        $file = CORE_PTH_TPL . $template;
        ob_start();
        if(file_exists($file))
        {
            include_once($file);
        } else {
            include_once(CORE_PTH_TPL . self::TEMPLATE_NAME . '.tpl');
        }
        return ob_get_clean();
    }

    public function assign($name, $value)
    {
        $this->vars[$name] = $value;
    }

    public function setPlugin($name)
    {
        require_once(CORE_PTH_APP_PLUGIN . 'function.'.$name.'.php');
    }
}
?>