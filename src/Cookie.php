<?php

namespace Solsken;

/**
 * Wrapper class for $_COOKIE
 */
class Cookie {
    static protected $_prefix = 'SLSKN';

    /**
     * Set Cookie with names value
     * @var String $name
     * @var String $value
     * @var Int    $expires
     * @var String $path
     */
    static public function set($name, $value, $expires = 0, $path = '/') {
        setcookie(self::getKey($name), $value, $expires, $path);
    }

    /**
     * Returns value for named cookie, or $default if not found
     * @var String $name
     * @var mixed  $default
     * @return Mixed
     */
    static public function get($name, $default = null) {
        return isset($_COOKIE[self::getKey($name)]) ? $_COOKIE[self::getKey($name)] : $default;
    }

    /**
     * Get Cookie key
     * @var String $name
     * @return String
     */
    static public function getKey($name) {
        return self::$_prefix . '---' . $name;
    }
}
