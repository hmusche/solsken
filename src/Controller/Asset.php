<?php

namespace Solsken\Controller;

use Solsken\Http;
use Solsken\Request;
use Solsken\Controller;
use Solsken\Registry;
use Solsken\Image;

use Leafo\ScssPhp\Compiler;

class Asset extends Controller {
    public function preDispatch() {
        Http::setCacheHeader();
    }

    public function postDispatch() {}

    public function jsAction() {
        header('Content-Type: application/javascript');

        $req   = Request::getInstance();
        $type  = $req->getParam('t', 'base');
        $parts = [
            $req->getParam('c'),
            $req->getParam('a')
        ];

        $assetPath = '';

        $jsFiles = Registry::get('app.config')['assets']['js'];

        foreach ($parts as $part) {
            $assetPath .= "$part/";
            $jsFiles['base'][] = $assetPath . '/script.js';
        }

        $output = '';

        $lastMTime = 0;

        if (isset($jsFiles[$type])) {
            foreach ($jsFiles[$type] as $file) {
                if ($type == 'base') {
                    if (file_exists('template/' . $file)) {
                        $output .= $this->_view->partial($file);
                    } else if (file_exists($file)) {
                        $output .= $this->_view->partial($file);
                    }
                } else {
                    if (file_exists($file)) {
                        $output .= "\n" . file_get_contents($file);

                        if ($lastMTime < filemtime($file)) {
                            $lastMTime = filemtime($file);
                        }
                    }
                }
            }
        }

        header('Last-modified: ' . gmdate('D, d M Y H:i:s ', $lastMTime) . 'GMT');
        header('Content-Length: ' . strlen($output));

        echo $output;
    }

    public function cssAction() {
        header('Content-Type: text/css');

        $output = '';

        $cssFiles = Registry::get('app.config')['assets']['css'];

        $lastMTime = 0;

        foreach ($cssFiles as $file) {
            $output .= file_get_contents($file);

            if ($lastMTime < filemtime($file)) {
                $lastMTime = filemtime($file);
            }
        }

        $scssPath = 'template/scss/';
        $scss     = new Compiler();
        $scss->setImportPaths($scssPath);

        $output .= $scss->compile('@import "main.scss";');

        header('Last-modified: ' . gmdate('D, d M Y H:i:s ', $lastMTime) . 'GMT');
        header('Content-Length: ' . strlen($output));

        echo $output;
    }
}
