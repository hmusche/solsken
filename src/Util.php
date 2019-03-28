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

    /**
     * Get Slug of given string
     * @var string $string
     * @var int    $length  If greater than zero, the slug will be cut
     * @return string
     */
    static public function getSlug($string, $length = 0) {
        $string = transliterator_transliterate("Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();", $string);

        if ($length > 0 && strlen($string) > $length) {
            $string = substr($string, 0, $length);
        }

        $string = trim(str_replace(' ', '-', $string));

        return $string;
    }

    /**
     * Get hopefully really random good unique ID
     * @return string
     */
    static public function getUniqueId() {
        return bin2hex(openssl_random_pseudo_bytes(8));
    }

    /**
     * Check if user currently logged in in session
     * @return bool
     */
    static public function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    /**
     * Get merged array of an array of files, each returning an array
     */
    static public function fileMerge($files) {
        $result = [];
        $dir    = '';

        if (is_string($files)) {
            $dir = "$files/";
            $files = scandir($files);
        }

        foreach ($files as $file) {
            if (substr($file, -4) == '.php' && is_readable($dir . $file)) {
                $result = array_replace_recursive($result, require($dir . $file));
            }
        }

        return $result;
    }
}
