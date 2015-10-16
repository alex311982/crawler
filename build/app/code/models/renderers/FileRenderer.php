<?php

/**
 * Renderer abstract model
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class FileRenderer extends RendererAbstract
{

    /**
     * File renderer settings
     */
    const RENDERER_OUTPUT_FILE = 'exported_%d.%m.%Y.html';
    const RENDERER_OUTPUT_FILE_PATH = '/var/export/';

    /**
     * Renderer type
     *
     * @var string
     */
    protected $_outputFileName;

    public function __construct($settings = array()) {
        parent::__construct($settings);
        $this->_outputFileName = !empty($settings['base_template'])
            ? $settings['base_template']
            : self::RENDERER_OUTPUT_FILE;

    }

    public function process($data = array()) {
        $this->rendererData = $data;
        $output = $this->render();
        //save output to file
        if ($output) {
            $this->saveToFile($output);
        }
    }

    protected function render() {
        $content = '';
        switch($this->_outputFormat) {
            case 'html':
                $templateObj = $this->_factory->getModel('templateModel');
                if (is_array($this->rendererData)) {
                    foreach($this->rendererData as $key => $value) {
                        $templateObj->assign($key, $value);
                    }
                }
                $templateObj->assign('component', $this->_settings['component']);
                $content = $templateObj->fetch($this->_outputFileName);
        }

        return $content;
    }

    protected function saveToFile($output = '') {
        $formatedOutputFile = strftime($this->_outputFileName);
        $outputFile = CORE_PTH_APP . '..' . '/var/export/' . $formatedOutputFile;
        $fp = fopen($outputFile, "w");
        if ( !$fp ) return null;
        fwrite($fp, $output);
        fclose( $fp );
    }

}