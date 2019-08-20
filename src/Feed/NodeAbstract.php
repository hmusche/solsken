<?php

namespace Solsken\Feed;

abstract class NodeAbstract {
    protected $_xml;
    protected $_root;

    protected $_elements = [
        'title'       => '',
        'link'        => '',
        'description' => ''
    ];

    public function __construct($elements) {
        $this->_xml = new \DOMDocument();
        $this->_root = $this->_xml->createElement($this->_rootName);

        foreach ($this->_elements as $key => $value) {
            if (!isset($elements[$key])) {
                throw new \Exception("Please set $key");
            }

            $this->_elements[$key] = $elements[$key];
            unset($elements[$key]);
        }

        foreach ($elements as $key => $value) {
            $method = 'set' . ucfirst($key);
            call_user_func([$this, $method], $value);
        }

    }

    public function __call($method, $value) {
        $type = substr($method, 0, 3);
        $element = lcfirst(str_replace($type, '', $method));

        $value = $value[0];

        if (!property_exists($this, '_optionalElements')
            || !in_array($type, ['get', 'set'])
            || !array_key_exists($element, $this->_optionalElements)) {
            throw new \Exception('Unknown method ' . $method);
        }

        if ($type == 'set') {
            switch ($this->_optionalElements[$element]['type']) {
                case 'empty':
                    $this->_elements[$element] = '';
                    break;
                case 'single':
                    $this->_elements[$element] = $value;
                    break;
                case 'multiple':
                    if (!isset($this->_elements[$element])) {
                        $this->_elements[$element] = [];
                    }

                    if (is_array($value)) {
                        $this->_elements[$element] = $value;
                    } else {
                        $this->_elements[$element][] = $value;
                    }
                    break;
            }

            if (isset($this->_optionalElements[$element]['attributes'])) {
                $this->_elements[$element] = [
                    'value' => $this->_elements[$element],
                    'attributes' => []
                ];

                foreach ($this->_optionalElements[$element]['attributes'] as $attribute) {
                    if (!isset($value[$attribute])) {
                        throw new \Exception("Please provide $attribute Attribute");
                    }

                    $this->_elements[$element]['attributes'][$attribute] = $value[$attribute];
                }
            }

            return $this;
        } elseif ($type == 'get') {
            return isset($this->_elements[$element]) ? $this->_elements[$element] : null;
        }
    }

    abstract public function getDom();
}
