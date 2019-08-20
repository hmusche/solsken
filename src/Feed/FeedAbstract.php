<?php

namespace Solsken\Feed;

abstract class FeedAbstract extends NodeAbstract {
    protected $_xmlHeaderString = '<?xml version="1.0">';

    protected $_items = [];

    abstract function render();

    public function addItem($item) {
        $this->_items[] = new Item($item);

        return $this;
    }

    public function addItems($items) {
        foreach ($items as $item) {
            $this->addItem($item);
        }

        return $this;
    }

    public function getDom() {
        $this->_root->setAttribute('version', '2.0');
        $this->_root->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $feed = $this->_xml->createElement('channel') ;

        foreach ($this->_elements as $element => $value) {
            $element = $this->_xml->createElement($element, $value);
            $feed->appendChild($element);
        }

        foreach ($this->_items as $item) {
            $item = $this->_xml->importNode($item->getDom()->documentElement, true);
            $feed->appendChild($item);
        }

        $this->_root->appendChild($feed);
        $this->_xml->appendChild($this->_root);

        return $this->_xml;
    }
}
