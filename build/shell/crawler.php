<?php

require_once 'abstract.php';

/**
 * Crawler shell script
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class CrawlerCompiler extends ShellAbstract
{

    /**
     * Table headers
     *
     * @var array
     */
    protected $_tableHeaders = array('URL', 'Count of image tags', 'Time');

    /**
     * Run script
     *
     */
    public function run()
    {
        try {
            $content = $this->_factory->getModel('crawlerModel')->crawl($this->_args);
            //array of data to render
            $result = array();
            $sortArr = array();
            foreach($content as $key => $data) {
                $time = $data['time'];
                Profiler::start($key);
                $renderData = TagCounterHelper::count($data['content'], 'img');
                Profiler::stop($key);
                $time += Profiler::fetch($key);
                array_push(
                    $result,
                    array($key, (int)$renderData['img'], $time)
                );
                array_push($sortArr, $renderData['img']);
            }
            array_multisort($sortArr, SORT_DESC, $result);
            if (!empty($result)) {
                array_unshift($result, $this->_tableHeaders);
            }
            ViewHelper::getRenderer()->process(array('grid' => $result));
        } catch( Exception $e ) {
            echo $e->getMessage();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f crawler.php -- [options]

  --depth <crawler>             Set depth of urls reading
  --threads <crawler>           Set count of threads
  --url <indexer>               Set url
  --render_mode <indexer>       Set renderer output (file, display)
  help                          This help

USAGE;
    }
}

$shell = new CrawlerCompiler();
$shell->run();
