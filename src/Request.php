<?php

namespace Solsken;

use Solsken\Registry;

/**
 * Parse current Request and pull all needed data
 */
class Request {
    /**
     * Instance
     * @var Request
     */
    static private $_instance = null;

    /**
     * Default shape of request object
     * @var Array
     */
    static private $_request = [
        'path' => '',
        'host' => '',
        'controller' => 'main',
        'action' => 'index',
        'get' => [],
        'post' => [],
        'params' => [],
        'is_xhr' => false,
        'method' => '',
        'headers' => []
    ];

    /**
     * Parses the request
     */
    protected function __construct() {
        $this->_parseRequest();
    }

    /**
     * Return Instance
     */
    static public function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Return single Param from request, can be GET, POST, or path part
     * @param  String $key     Param to return
     * @param  Mixed  $default Default value to return if param is not set, defaults to NULL
     * @return Mixed           Return value
     */
    public function getParam($key, $default = null) {
        return (array_key_exists($key, self::$_request['params'])) ? self::$_request['params'][$key] : $default;
    }

    /**
     * Get value from request array
     * @param  String $key
     * @return Mixed
     */
    public function get($key) {
        if (!array_key_exists($key, self::$_request)) {
            throw new \Exception('$key not found in request');
        }

        return self::$_request[$key];
    }

    /**
     * Builds array of request. POST, GET and path parts are merged to form params array
     */
    protected function _parseRequest() {
        $config = Registry::get('app.config');
    	$path   = explode('?', substr($_SERVER['REQUEST_URI'], strlen($config['path'])))[0];
    	$parts  = explode('/', $path);

    	if (trim($parts[0]) !== '') {
            self::$_request['controller'] = $parts[0];
        }

        if (isset($parts[1]) && trim($parts[1]) !== '') {
            self::$_request['action'] = $parts[1];
        }

        self::$_request['path']    = $path;
        self::$_request['is_xhr']  = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
        self::$_request['method']  = strtolower($_SERVER['REQUEST_METHOD']);
        self::$_request['get']     = $_GET;
        self::$_request['post']    = $_POST;
        self::$_request['raw']     = file_get_contents('php://input');
        self::$_request['params']  = array_merge($_GET, $_POST, $_FILES);
        self::$_request['headers'] = $_SERVER;

        if ($json = json_decode(self::$_request['raw'], true)) {
            self::$_request['params'] = array_merge(self::$_request['params'], $json);
        }

        if (count($parts) > 2) {
            $currKey = false;

            foreach (array_slice($parts, 2) as $part) {
                $part = urldecode($part);

                if ($currKey === false) {
                    $currKey = $part;
                } else {
                    self::$_request['params'][$currKey] = $part;
                    $currKey = false;
                }
            }
        }
    }
}
