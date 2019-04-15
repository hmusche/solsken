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
    static public function setCacheHeader($maxAge = null) {
        if ($maxAge === null) {
            $maxAge = 30 * 86400;
        }

        header('Cache-Control: public, max-age=' . $maxAge);
        header('Expires: ' .  gmdate('D, d M Y H:i:s ', time() + $maxAge) . 'GMT');
    }
}
