<?php

namespace Solsken;

/**
 * Helper Class
 */
class Util {
    /**
     * Transform string to camelCase, first letter is lowercase
     * @var     String
     * @return  String
     */
    static public function toCamelCase($string) {
        $string = str_replace(['_', '-'], ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return lcfirst($string);
    }

    static public function getSlug($string, $length = 0) {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);

        if ($length > 0 && strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }

        $string = trim(str_replace(' ', '-', $string));

        return $string;
    }

    static public function getUniqueId() {
        return bin2hex(openssl_random_pseudo_bytes(8));
    }

    static public function isLoggedIn() {
        return isset($_SESSION['user']);
    }
}
