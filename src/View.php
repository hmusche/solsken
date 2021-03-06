<?php

namespace Solsken;

use Solsken\Request;

/**
 * View class
 */
class View {
    /**
     * Instance of class
     * @var View
     */
    static private $_instance = null;

    /**
     * Current Template for View
     * @var string
     */
    protected $_template;

    /**
     * Data to be used in View
     * @var array
     */
    protected $_data = [];

    /**
     * Array of helper calls
     */
    protected $_helpers = [];

    public $webhost;
    public $cdnhost;
    public $path;
    public $config;


    /**
     * Private constructor
     */
    private function __construct() {
        $this->webhost  = Registry::get('app.config')['host'] . Registry::get('app.config')['path'];
        $this->cdnhost  = Registry::get('app.config')['cdnhost'] . Registry::get('app.config')['path'];
        $this->path     = Request::getInstance()->get('path');
        $this->config   = Registry::get('app.config');
    }

    /**
     * Set Template for View
     * @param string $template
     */
    public function setTemplate($template) {
        $this->_template = $template;
    }

    /**
     * Set array of data directly to View
     * @param array $values
     */
    public function setData(Array $values) {
        foreach ($values as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Render current template with current data
     * @param  string $template Optional, if null current set template is used
     * @return string           Rendered template
     */
    public function render($template = null) {
        if ($template === null) {
            $template = 'template/main.phtml';
        } else if (file_exists('template/' . $template)) {
            $template = 'template/' . $template;
        }

        if (!file_exists($template)) {
            throw new \Exception('Template file ' . $template . ' not found.');
        }

        ob_start();

        require $template;

        return ob_get_clean();
    }

    /**
     * Renders given template with subset of data
     * @param  string $template Template name to use
     * @param  array  $data     Data for template
     * @return string           Rendered partial
     */
    public function partial($template, array $data = []) {
        $partialView = clone self::$_instance;
        $partialView->clearData();
        $partialView->setData($data);

        return $partialView->render($template);
    }

    /**
     * Clear data
     */
    public function clearData() {
        $this->_data = [];
    }

    /**
     * Add helper function to class
     * @param string $key
     * @param mixed  $helper
     */
    public function addHelper($key, $helper) {
        $this->_helpers[$key] = $helper;
    }

    /**
     * Magic setter to set values in view
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value) {
        $this->_data[$key] = $value;
    }

    /**
     * Magic method to return data from View object
     * @param  string $key
     * @return mixed
     */
    public function __get($key) {
        return array_key_exists($key, $this->_data) ? $this->_data[$key] : null;
    }

    /**
     * Magic method to call defined helpers
     * @param  string $method name of helper
     * @param  array  $args
     * @return mixed
     */
    public function __call($method, $args) {
        if (isset($this->_helpers[$method])) {
            return call_user_func_array($this->_helpers[$method], $args);
        }

        throw new \Exception("Unknown method $method called");
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
}
