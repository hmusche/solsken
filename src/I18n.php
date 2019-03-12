<?php

namespace Solsken;

use Solsken\Util;
use Punic\Territory;

/**
 * Internationalization singleton
 */
class I18n {
    /**
     * Instance of class
     * @var Object
     */
    static private $_instance = null;

    /**
     * Currently used locale
     * @var String
     */
    protected $_locale;

    /**
     * Source of translation, currently can be either array or model
     * @var String
     */
    protected $_translationType = 'array';

    /**
     * Definition of translation source
     * @var Array
     */
    protected $_translationSource = [];

    /**
     * Supported locales
     * @var Array
     */
    protected $_supportedLocales = ['en'];

    /**
     * Private constructor. Gets Locale from Client
     * @param string $locale   Locale to set, if null, then ACCEPT_LANGUAGE header is parsed
     * @param string $timezone Name of TZ to set
     */
    private function __construct($locale = null, $timezone = 'Europe/Berlin') {
        if ($locale === null) {
            $locale = \Locale::acceptFromHttp(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en');
        }

        $this->_locale = $locale;
        date_default_timezone_set($timezone);
    }

    /**
     * Return instance of class, arguments are only honored on first call
     * @var string $locale   Locale to set
     * @var string $timezone Timezone to set
     * @return \Solsken\I18n
     */
    static public function getInstance($locale = null, $timezone = 'Europe/Berlin') {
        if (self::$_instance === null) {
            self::$_instance = new self($locale, $timezone);
        }

        return self::$_instance;
    }

    /**
     * Set type and source for translation
     *
     * Type = 'model' means an instance of \Solsken\Model, which provides a method "translate", which is called later
     * @param array $options
     */
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

    /**
     * Set the supported Locales
     * @param array $locales
     */
    public function setSupportedLocales(array $locales) {
        if ($locales === []) {
            throw new \Exception('Please provide at least one supported locale');
        }

        $this->_supportedLocales = $locales;
    }

    /**
     * Get the currently supported Locales
     * @return array
     */
    public function getSupportedLocales() {
        return $this->_supportedLocales;
    }

    /**
     * Wrapper class to call all format methods
     * @param  string $type       Type to call, ie "date" or "country"
     * @param  ...    $parameters Array of called parameter
     * @return Mixed              Return of called method
     */
    public function format($type, ... $parameters) {
        $method = 'format' . ucfirst(Util::toCamelCase($type));

        if (!method_exists($this, $method)) {
            throw new \Exception('Unknown method ' . $method);
        }

        return call_user_func_array([$this, $method], $parameters);
    }

    /**
     * Get the properly translated Name of a country code, according to current locale
     * @param  string $countryCode Country Code
     * @return string              Name of country in currently set language
     */
    public function formatCountry($countryCode) {
        return \Punic\Territory::getName(strtoupper($countryCode), $this->getLocale(false));
    }

    /**
     * Returns a formatted date string, according to format and current locale
     * @param  mixed  $dt     Either DateTime object or UNIX timestamp
     * @param  string $format Format to return
     * @return string
     */
    public function formatDate($dt, $format = 'medium') {
        if (!is_object($dt)) {
            $dt = \Punic\Calendar::toDateTime($dt);
        }

        return \Punic\Calendar::formatDatetime($dt, $format, $this->getLocale(true));
    }

    /**
     * Translate given key to current locale
     * @param  string $string  Key to translate
     * @param  array  $options Options for translated string, like "transform" operation
     * @return string          Translated String, or $string of not found
     */
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

    /**
     * Returns current locale.
     * As $this->_locale is set from ACCEPT_LANGUAGE header, we check that against the array
     * of supported locales, and return that only if in that array. Otherwise the first entry
     * of supported locales is returned
     *
     * @param  boolean $full If true, return the full locale, otherwise only the first part
     * @return string        Locale
     */
    public function getLocale($full = true) {
        $locale = $this->_supportedLocales[0];

        if ($this->_locale) {
            foreach ($this->_supportedLocales as $suppLocale) {
                if (substr($this->_locale, 0, 2) == substr($suppLocale, 0, 2)) {
                    $locale = $suppLocale;
                }
            }
        }

        return $full ? $locale : substr($locale, 0, 2);
    }
}
