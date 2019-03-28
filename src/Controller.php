<?php

namespace Solsken;

use Solsken\Request;
use Solsken\Util;
use Solsken\View;

/**
 * Controller class to control Action
 */
class Controller {
    protected $_request;
    protected $_view;

    /**
     * Constructor, get Request and View Instances
     */
    public function __construct() {
        $this->_request = Request::getInstance();
        $this->_view = View::getInstance();
    }

    /**
     * Static method to get Instance of COntroller class fitting to current request
     *
     * @return Solsken\Controller
     */
    static public function getController($namespace) {
        $request = Request::getInstance();

        $controller = $namespace . '\\Controller\\' . ucfirst(Util::toCamelCase($request->get('controller')));

        if (!class_exists($controller)) {
            $namespace = 'Solsken';
            $controller = $namespace . '\\Controller\\' . ucfirst(Util::toCamelCase($request->get('controller')));
        }

        if (!class_exists($controller)) {
            throw new \Exception('Unknown Controller ' . $controller, 404);
        }

        $controller = new $controller;

        return $controller;
    }

    /**
     * Get Action from request object and call preDispatch, Action, and postDispatch
     */
    public function dispatch() {
        $action = Util::toCamelCase($this->_request->get('action'));
        $method = $action . 'Action';

        if (!is_callable([$this, $method])) {
            throw new \Exception("Unknown Action $action", 404);
        }

        $this->preDispatch();
        $this->$method();
        $this->postDispatch();
    }

    /**
     * Build default template. Method can be overriden oder extended in sub controller classes
     */
    public function preDispatch() {
        $defaultTemplate = $this->_request->get('controller') . DIRECTORY_SEPARATOR . $this->_request->get('action') . '.phtml';

        $this->_view->template = $defaultTemplate;
        $this->_view->request  = $this->_request;
    }

    /**
     * Render Template with all data. Can be overriden on sub controller classes
     */
    public function postDispatch() {
        echo $this->_view->render();
    }
}
