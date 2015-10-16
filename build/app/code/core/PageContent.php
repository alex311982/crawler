<?php

class PageContent
{
    /**
     * Component settings
     */
    const COMPONENT_NAME = 'default';

	public function execute($vars)
	{
        foreach($vars as $name => &$val)
        {
            $$name = $val;
        }
        unset($vars);

        $tpl = !empty($component) ? $component : self::COMPONENT_NAME;

        include(CORE_PTH_TPL.$tpl.'.tpl');
	}
}