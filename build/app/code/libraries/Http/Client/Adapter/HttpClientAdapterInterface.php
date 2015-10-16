<?php

/**
 * An interface description for http client adapter classes.
 *
 * @author Alex Gubrev <gubarev311982@yandex.ru>
 */
interface HttpClientAdapterInterface
{
    /**
     * Set the configuration array for the adapter
     *
     * @param array $config
     */
    public function setConfig($config = array());

    /**
     * Connect to the remote server
     *
     * @param string  $host
     * @param int     $port
     * @param boolean $secure
     */
    public function connect($host, $port = 80, $secure = false);

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
    public function write($method, $url, $http_ver = '1.1', $headers = array(), $body = '');

    /**
     * Read response from server
     *
     * @return string
     */
    public function read();

    /**
     * Close the connection to the server
     *
     */
    public function close();
}
