<?php

namespace Solsken;

class Cookie {
    static protected $_prefix = 'SLSKN';

    static public function set($name, $value, $expires = 0, $path = '/') {
        setcookie(self::getKey($name), $value, $expires, $path);
    }

    static public function get($name, $default = null) {
        return isset($_COOKIE[self::getKey($name)]) ? $_COOKIE['name'] : $default;
    }

    static public function getKey($name) {
        return self::$_prefix . '---' . $name;
    }
}
