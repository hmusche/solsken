<?php

namespace Solsken;

use Solsken\Registry;

/**
 * Helper class for all HTTP needs
 */
class Http {
    /**
     * Redirect to given $location.
     * @var String  $location    Can be full URL or path part
     * @var Integer $status      Returncode for Redirect, default = 302
     */
    static public function redirect($location, $status = 302) {
        if (!preg_match('#https?::#', $location)) {
            $config = Registry::get('app.config');

            $location = $config['host'] . $config['path'] . $location;
        }

        header('Location: ' . $location, true, $status);
        exit;
    }

    /**
     * Set some headers to ensure caching of content
     */
    static public function setCacheHeader() {
        header('Cache-Control: public, max-age: 86400');
        header('Pragma: cache');
        header('Expires: ' .  gmdate('D, d M Y H:i:s ', strtotime('+1week')) . 'GMT');
    }
}
