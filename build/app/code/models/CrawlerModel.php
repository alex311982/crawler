<?php

/**
 * Crawler site class
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class CrawlerModel extends ModelAbstract
{
    /**
     * Crawler settings
     */
    const XML_PATH_CRAWLER_URL = 'url';
    const XML_PATH_CRAWLER_THREADS = 'threads';
    const XML_PATH_CRAWLER_DEPTH = 'depth';
    const NOT_VALID_URL_MESSAGE     = 'Not valid Url';

    /**
     * Batch size
     */
    const BATCH_SIZE = 500;

    /**
     * Crawler user agent name
     */
    const USER_AGENT = 'InnovationCrawler';

    /**
     * Factory instance
     *
     * @var FactoryModel
     */
    protected $_factory;

    /**
     * Initialize application, adapter factory
     *
     */
    public function __construct()
    {
        $this->_factory = FactoryModel::getInstance();
    }

    /**
     * Crawl all system urls
     *
     * @param array $options
     * @return CrawlerModel
     */
    public function crawl($options = array())
    {
        $adapter = $this->_factory->getModel('curlAdapter');

        return $this->_executeRequests($options, $adapter);
    }

    /**
     * Prepares and executes requests by given request_paths values
     *
     * @param array $info
     * @param HttpClientAdapterInterface $adapter
     *
     * @throws Exception
     *
     * @return array
     */
    protected function _executeRequests(array $info, HttpClientAdapterInterface $adapter)
    {
        $options = array(
            CURLOPT_USERAGENT      => self::USER_AGENT,
            CURLOPT_SSL_VERIFYPEER => 0,
        );

        $configs = array();

        if (empty($info[self::XML_PATH_CRAWLER_URL])) {
            throw new Exception(self::NOT_VALID_URL_MESSAGE);
        } else {
            $configs['url'] = $info[self::XML_PATH_CRAWLER_URL];
        }

        if (!empty($info[self::XML_PATH_CRAWLER_THREADS])) {
            $configs['threads'] = $info[self::XML_PATH_CRAWLER_THREADS];
        } else {
            $configs['threads'] = 1;
        }

        if (!empty($info[self::XML_PATH_CRAWLER_DEPTH])) {
            $configs['depth'] = $info[self::XML_PATH_CRAWLER_DEPTH];
        } else {
            $configs['depth'] = 5;
        }

        $adapter->setConfig($configs);
        $adapter->setOptions($options);

        $adapter->multiRequest($configs);

        return $adapter->getData();
    }
}
