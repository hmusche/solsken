<?php

namespace Solsken;

use Solsken\Util;
use Punic\Territory;

class I18n {
    static private $_instance = null;

    protected $_locale;

    protected $_translationType = 'array';
    protected $_translationSource = [];

    protected $_supportedLocales = ['en'];

    private function __construct($locale = null, $timezone = 'Europe/Berlin') {
        if ($locale === null) {
            $locale = \Locale::acceptFromHttp($_SERVER['HTTP_ACCEPT_LANGUAGE']);
        }

        $this->_locale = $locale;
        date_default_timezone_set('Europe/Berlin');
    }

    static public function getInstance($locale = null, $timezone = 'Europe/Berlin') {
        if (self::$_instance === null) {
            self::$_instance = new self($locale, $timezone);
        }

        return self::$_instance;
    }

    public function setTranslationOptions(array $options) {
        $type   = isset($options['type']) ? $options['type'] : 'array';
        $source = isset($options['source']) ? $options['source'] : [];

        if (isset($options['supported_locales'])) {
            $this->setSupportedLocales($options['supported_locales']);
        }

        switch ($type) {
            case 'model':
                if (is_string($source)) {
                    $source = new $source();
                }

                if (is_object($source)) {
                    $this->_translationSource = $source;
                } else {
                    throw new \Exception('Please provide proper translation source');
                }
            case 'array':
                $this->_translationSource = $source;
                break;

            default:
                throw new \Exception('Unknown Translation Source');
        }

        $this->_translationType = $type;
    }

    public function setSupportedLocales(array $locales) {
        if ($locales === []) {
            throw new \Exception('Please provide at least one supported locale');
        }

        $this->_supportedLocales = $locales;
    }

    public function getSupportedLocales() {
        return $this->_supportedLocales;
    }

    public function format($type, ... $parameters) {
        $method = 'format' . ucfirst(Util::toCamelCase($type));

        if (!method_exists($this, $method)) {
            throw new \Exception('Unknown method ' . $method);
        }

        return call_user_func_array([$this, $method], $parameters);
    }

    public function formatCountry($countryCode) {
        return \Punic\Territory::getName(strtoupper($countryCode), $this->getLocale(false));
    }

    public function formatDate($dt, $format = 'medium') {
        if (!is_object($dt)) {
            $dt = \Punic\Calendar::toDateTime($dt);
        }

        return \Punic\Calendar::formatDatetime($dt, $format, $this->getLocale(false));
    }

    public function translate($string, $options = []) {
        $locale = $this->getLocale(false);

        switch ($this->_translationType) {
            case 'array':
                if (isset($this->_translationSource[$locale]) && array_key_exists($string, $this->_translationSource[$locale])) {
                    $string = $this->_translationSource[$locale][$string];
                }

                break;

            case 'model':
                $string = $this->_translationSource->translate($string, $locale);
                break;
        }

        if (isset($options['transform']) && is_array($options['transform'])) {
            foreach ($options['transform'] as $function => $args) {
                $string = call_user_func_array($function, $args);
            }
        }

        return $string;
    }

    public function getLocale($full = true) {
        if ($this->_locale && in_array($this->_locale, $this->_supportedLocales)) {
            return $full ? $this->_locale : substr($this->_locale, 0, 2);
        }

        return $this->_supportedLocales[0];
    }
}
