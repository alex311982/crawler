<?php

/**
 * HTTP CURL Adapter
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
class CurlAdapter implements HttpClientAdapterInterface
{
    /**
     * Parameters array
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Parameters array
     *
     * @var array
     */
    public $_result = array();

    /**
     * Curl handle
     *
     * @var resource
     */
    protected $_resource;

    /**
     * Allow parameters
     *
     * @var array
     */
    protected $_allowedParams = array(
        'timeout'       => CURLOPT_TIMEOUT,
        'maxredirects'  => CURLOPT_MAXREDIRS,
        'proxy'         => CURLOPT_PROXY,
        'ssl_cert'      => CURLOPT_SSLCERT,
        'userpwd'       => CURLOPT_USERPWD
    );

    /**
     * Array of CURL options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Apply current configuration array to transport resource
     *
     * @return CurlAdapter
     */
    protected function _applyConfig()
    {
        curl_setopt_array($this->_getResource(), $this->_options);

        if (empty($this->_config)) {
            return $this;
        }

        $verifyPeer = isset($this->_config['verifypeer']) ? $this->_config['verifypeer'] : 0;
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYPEER, $verifyPeer);

        $verifyHost = isset($this->_config['verifyhost']) ? $this->_config['verifyhost'] : 0;
        curl_setopt($this->_getResource(), CURLOPT_SSL_VERIFYHOST, $verifyHost);

        foreach ($this->_config as $param => $curlOption) {
            if (array_key_exists($param, $this->_allowedParams)) {
                curl_setopt($this->_getResource(), $this->_allowedParams[$param], $this->_config[$param]);
            }
        }
        return $this;
    }

    /**
     * Set array of additional cURL options
     *
     * @param array $options
     * @return CurlAdapter
     */
    public function setOptions(array $options = array())
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Add additional option to cURL
     *
     * @param  int $option      the CURLOPT_* constants
     * @param  mixed $value
     * @return CurlAdapter
     */
    public function addOption($option, $value)
    {
        $this->_options[$option] = $value;
        return $this;
    }

    /**
     * Add additional options list to curl
     *
     * @param array $options
     *
     * @return CurlAdapter
     */
    public function addOptions(array $options)
    {
        $this->_options = $options + $this->_options;
        return $this;
    }

    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     * @return CurlAdapter
     */
    public function setConfig($config = array())
    {
        $this->_config = $config;
        return $this;
    }

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     * @return CurlAdapter
     */
    public function connect($host, $port = 80, $secure = false)
    {
        return $this->_applyConfig();
    }

    /**
     * Send request to the remote server
     *
     * @param string        $method
     * @param string        $url
     * @param string        $http_ver
     * @param array         $headers
     * @param string        $body
     * @return string Request as text
     */
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $body = '')
    {

    }

    /**
     * Read response from server
     *
     * @return string
     */
    public function read()
    {

    }

    /**
     * Close the connection to the server
     *
     * @return CurlAdapter
     */
    public function close()
    {
        curl_close($this->_getResource());
        $this->_resource = null;
        return $this;
    }

    /**
     * Returns a cURL handle on success
     *
     * @return resource
     */
    protected function _getResource()
    {
        if (is_null($this->_resource)) {
            $this->_resource = curl_init();
        }
        return $this->_resource;
    }

    /**
     * Get last error number
     *
     * @return int
     */
    public function getErrno()
    {
        return curl_errno($this->_getResource());
    }

    /**
     * Get string with last error for the current session
     *
     * @return string
     */
    public function getError()
    {
        return curl_error($this->_getResource());
    }

    /**
     * Get information regarding a specific transfer
     *
     * @param int $opt CURLINFO option
     * @return mixed
     */
    public function getInfo($opt = 0)
    {
        if (!$opt) {
            return curl_getinfo($this->_getResource());
        }

        return curl_getinfo($this->_getResource(), $opt);
    }

    public function getData()
    {
        return $this->_result;
    }

    /**
     * curl_multi_* requests support
     *
     * @param array $config
     *
     */
    public function multiRequest($config)
    {
        $depth = $config['depth'];
        if ($depth == 0) {
            return;
        }
        $threads = (int)$config['threads'];
        !is_array($config['url']) ? $config['url'] = array($config['url']) : '';
        $handles = array();
        $result  = array();
        $multihandle = curl_multi_init();
        $nextConfigs = array();
        $i = 1;
        foreach ($config['url'] as $key => $url) {
            if ($i <= $threads) {
                $handles[$url] = curl_init();
                curl_setopt($handles[$url], CURLOPT_URL, $url);
                curl_setopt($handles[$url], CURLOPT_HEADER, 0);
                curl_setopt($handles[$url], CURLOPT_RETURNTRANSFER, 1);
                if (!empty($config)) {
                    curl_setopt_array($handles[$url], $this->_options);
                }
                curl_multi_add_handle($multihandle, $handles[$url]);
                $i++;
            }
            $i--;
            if (($i == $threads)
                || (count($config['url']) -1 == $key)
            ) {
                $i = 1;
                do {
                    curl_multi_exec($multihandle, $process);
                    usleep(100);
                } while ($process>0);

                foreach ($handles as $key => $handle) {
                    $result[$key]['content'] = curl_multi_getcontent($handle);
                    $result[$key]['time'] = curl_getinfo($handle, CURLINFO_TOTAL_TIME);
                    curl_multi_remove_handle($multihandle, $handle);
                    $hrefs = $this->getHrefs($result[$key]['content']);
                    if (empty($hrefs)) {
                        continue;
                    }
                    $copiedOptions = array_slice($config, 0, count($config));
                    $copiedOptions['url'] = $hrefs;
                    $copiedOptions['depth'] = $copiedOptions['depth'] - 1;
                    $nextConfigs[] = $copiedOptions;
                }
                $handles = array();
                $this->_result = array_merge($this->_result, $result);
            }
        }
        curl_multi_close($multihandle);
        foreach($nextConfigs as $nextConfig) {
            $this->multiRequest($nextConfig);
        }
    }

    protected function getHrefs($content) {
        $baseUrl = $this->_config['url'];
        $host = parse_url($baseUrl, PHP_URL_HOST);
        $result = array();
        $dom = new DOMDocument();
        @$dom->loadHTML($content);
        $xpath = new DOMXPath($dom);
        $hrefs = $xpath->evaluate("/html/body//a");
        for ($i = 0; $i < $hrefs->length; $i++ ) {
            $href = $hrefs->item($i)->getAttribute('href');
            if (preg_match("{((https?://)|(www.))([a-zA-Z0-9-])+\.([a-zA-Z0-9\/?=#!-\._])+([a-zA-Z0-9\/=#])}", $href)
                && (strpos($href, $host) !== false)
            ) {
                $result[] = $href;
            }
        }

        return $result;
    }
}
