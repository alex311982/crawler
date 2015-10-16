<?php

/**
 * View helper
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class ViewHelper
{

    /**
     * Get renderer object
     *
     * @param array $options
     *
     * @return RendererAbstract|bool
     */
    static public function getRenderer($options = array())
    {
        $configObj = Config::getInstance();
        $renderData = $configObj->get('main', 'render');

        $rendererType = !empty($renderData['renderer_type'])
            ? $renderData['renderer_type']
            : (!empty($options['renderer_type'])
                ? $options['renderer_type']
                : RendererAbstract::RENDERER_TYPE
            );

        $factory = FactoryModel::getInstance();
        switch ((string)$rendererType) {
                case 'file':
                    $renderType = 'fileRenderer';
                break;
                default:
                    $renderType = RendererAbstract::RENDERER_TYPE . 'Renderer';
        }

        return $factory->getModel($renderType, $renderData);
    }
}
