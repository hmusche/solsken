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
            'type' => 'empty',
            'attributes' => [
                'url',
                'type',
                'length'
            ]
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

    public function setPubDate($time) {
        $this->_elements['pubDate'] = gmdate("D, d M Y H:m:s \G\M\T", $time);
    }

    public function getDom() {
        foreach ($this->_elements as $element => $value) {
            if (is_array($value)) {
                if (isset($value['attributes'])) {
                    $element = $this->_xml->createElement($element, $value['value']);

                    foreach ($value['attributes'] as $attribute => $value) {
                        $element->setAttribute($attribute, $value);
                    }

                    $this->_root->appendChild($element);
                } else {
                    foreach ($value as $val) {
                        $element = $this->_xml->createElement($element, $val);
                        $this->_root->appendChild($element);
                    }
                }
            } else {
                $element = $this->_xml->createElement($element, $value);
                $this->_root->appendChild($element);
            }
        }

        $this->_xml->appendChild($this->_root);
        return $this->_xml;
    }
}
