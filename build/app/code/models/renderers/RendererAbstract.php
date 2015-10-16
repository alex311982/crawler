<?php

/**
 * Renderer abstract model
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
abstract class RendererAbstract extends ModelAbstract
{
    /**
     * Renderer settings
     */
    const RENDERER_TYPE = 'file';
    const RENDERER_OUTPUT_FORMAT = 'html';
    const RENDERER_OUTPUT_BASE_TEMPLATE = 'overall';

    /**
     * Renderer type
     *
     * @var string
     */
    protected $_rendererType;

    /**
     * Output format
     *
     * @var string
     */
    protected $_outputFormat;

    /**
     * Settings
     *
     * @var array
     */
    protected $_settings;

    /**
     * Factory instance
     *
     * @var Factory
     */
    protected $_factory;

    public function __construct($settings = array()) {
        $this->_settings = $settings;
        $this->_rendererType = !empty($settings['type']) ? $settings['type'] : self::RENDERER_TYPE;
        $this->_outputFormat = !empty($settings['output_format']) ? $settings['output_format'] : self::RENDERER_OUTPUT_FORMAT;
        $this->_factory = FactoryModel::getInstance();
    }

    abstract public function process();
    abstract protected function render();
}