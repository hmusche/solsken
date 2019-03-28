<?php

namespace Solsken;

use Solsken\Controller;
use Solsken\Registry;
use Solsken\I18n;
use Solsken\Cookie;
use Solsken\View;

use Medoo\Medoo;

/**
 * Base Application class to pull everything together, get Configuration, Controller, and dispatch the request
 */
class Application {
    protected $_controller;
    protected $_config;

    /**
     * Get Configuration and set in Registry, and create Controller class
     */
    public function __construct(array $config) {
        $this->_config = $config;
        Registry::set('app.config', $config);

        if (isset($config['db'])) {
            Registry::set('app.db', new Medoo($config['db']));
        }

        $locale   = Cookie::get('locale_settings');
        $timezone = Cookie::get('timezone', isset($config['default_timezone']) ? $config['default_timezone'] : null);

        $i18n = I18n::getInstance($locale, $timezone);

        if (isset($config['translation'])) {
            $i18n->setTranslationOptions($config['translation']);

            View::getInstance()->addHelper('t', [$i18n, 'translate']);
            View::getInstance()->addHelper('date', [$i18n, 'formatDate']);
            View::getInstance()->addHelper('i18n', [$i18n, 'format']);
        }
    }

    /**
     * Dispatch the controller
     */
    public function run() {
        try {
            $this->_controller = Controller::getController($this->_config['namespace']);
            $this->_controller->dispatch();
        } catch (\Exception $e) {
            if (isset($this->_config['throw_exceptions']) && $this->_config['throw_exceptions']) {
                throw $e;
            }

            if ($e->getCode() >= 400) {
                http_response_code($e->getCode());
            } else {
                http_response_code(500);
            }
        }
    }
}
