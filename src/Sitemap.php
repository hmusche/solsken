<?php

namespace Solsken;

class Sitemap {
    protected $_xml;
    protected $_rootName = 'urlset';
    protected $_rootSchema = "http://www.sitemaps.org/schemas/sitemap/0.9";
    protected $_linkNs = "http://www.w3.org/1999/xhtml";

    public function __construct() {
        $this->_xml = new \DOMDocument("1.0", "UTF-8");
        $this->_root = $this->_xml->createElement($this->_rootName);
        $this->_root->setAttribute("xmlns", $this->_rootSchema);
        $this->_root->setAttribute("xmlns:xhtml", $this->_linkNs);

        $this->_xml->appendChild($this->_root);
    }

    public function addMultiUrl(array $urls, int $lastmod, string $freq = 'always', float $prio = 0.5) {
        foreach ($urls as $language => $url) {
            $urlElement = $this->addUrl($url, $lastmod, $freq, $prio);

            foreach ($urls as $subLang => $subUrl) {
                if ($subLang != $language) {
                    $linkElement = $this->_xml->createElement('xhtml:link');
                    $linkElement->setAttribute('rel', 'alternate');
                    $linkElement->setAttribute('hreflang', $subLang);
                    $linkElement->setAttribute('href', $subUrl);
                    $urlElement->appendChild($linkElement);
                }
            }
        }
    }

    public function addUrl(string $url, int $lastmod = null, string $freq = 'always', float $prio = 0.5, array $optional = []) {
        $urlElement = $this->_xml->createElement('url');
        $lastmod    = $lastmod ?: time();
        $lastmod    = date('Y-m-d\TH:i:s\Z', $lastmod);

        $urlElement->appendChild($this->_xml->createElement('loc', $url));
        $urlElement->appendChild($this->_xml->createElement('lastmod', $lastmod));
        $urlElement->appendChild($this->_xml->createElement('changefreq', $freq));
        $urlElement->appendChild($this->_xml->createElement('priority', $prio));

        foreach ($optional as $key => $value) {
            $urlElement->appendChild($this->_xml->createElement($key, $value));
        }

        $this->_root->appendChild($urlElement);

        return $urlElement;
    }

    public function __toString() {
        $this->_xml->formatOutput = true;
        return $this->_xml->saveXml();
    }
}
