<?php

namespace Solsken;

use Solsken\Controller;
use Solsken\Registry;
use Medoo\Medoo;

/**
 * Base Application class to pull everything together, get Configuration, Controller, and dispatch the request
 */
class Application {
    protected $_controller;

    /**
     * Get Configuration and set in Registry, and create Controller class
     */
    public function __construct(array $config) {
        Registry::set('app.config', $config);
        Registry::set('app.db', new Medoo($config['db']));

        session_start();

        $this->_controller = Controller::getController($config['namespace']);
    }

    /**
     * Dispatch the controller
     */
    public function run() {
        $this->_controller->dispatch();
    }
}
