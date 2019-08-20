<?php

namespace Solsken\Feed;

class Item extends NodeAbstract {
    protected $_rootName = 'item';

    protected $_elements = [
        'title'       => '',
        'link'        => '',
        'description' => ''
    ];

    protected $_optionalElements = [
        'author' => [
            'type' => 'single'
        ],
        'category' => [
            'type' => 'multiple'
        ],
        'enclosure' => [
            'type' => 'single'
        ],
        'guid' => [
            'type' => 'single'
        ],
        'pubDate' => [
            'type' => 'single'
        ],
        'source' => [
            'type' => 'single'
        ]
    ];

    public function getDom() {
        foreach ($this->_elements as $element => $value) {
            $element = $this->_xml->createElement($element, $value);
            $this->_root->appendChild($element);
        }

        $this->_xml->appendChild($this->_root);
        return $this->_xml;
    }
}
