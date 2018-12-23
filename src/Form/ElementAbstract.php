<?php

namespace Solsken\Form;

use Solsken\Util;
use Solsken\View;

abstract class ElementAbstract {
    protected $_name;
    protected $_attributes = [];
    protected $_validators = [];
    protected $_errors     = [];
    protected $_value;
    protected $_label;

    abstract public function populate(Array $data);

    public function __construct($name, $options, $value = null) {
        $defaultOptions = [
            'attributes' => [
                'type' => 'text'
            ],
            'validators' => [
                'required' => []
            ]
        ];

        $options = array_merge_recursive($defaultOptions, $options);

        $this->_name = $name;
        $this->_value = $value;
        $this->_label = isset($options['label']) ? $options['label'] : ucfirst($name);
        $this->_attributes = array_merge($options['attributes'], $this->_attributes);

        foreach ($options['validators'] as $validator => $validatorOptions) {
            $validatorClass = "\\Solsken\\Form\\Validator\\" . ucfirst(Util::toCamelCase($validator));
            $validatorHash = serialize($validatorOptions) . $validator;

            $this->_validators[$validatorHash] = new $validatorClass($validatorOptions);
        }
    }


    public function isValid() {
        $valid = true;

        foreach ($this->_validators as $name => $validator) {
            if (!$validator->isValid($this->_value)) {
                $this->_errors[] = $validator->getError();
                $valid = false;
            }
        }

        return $valid;
    }

    public function getErrors() {
        return $this->_errors;
    }

    public function hasErrors() {
        return $this->_errors !== [];
    }

    public function getName() {
        return $this->_name;
    }

    public function getValue() {
        return $this->_value;
    }

    public function getLabel() {
        /**
         * @todo: translate
         */
        return $this->_label;
    }

    public function getAttributes() {
        return $this->_attributes;
    }

    public function getAttributeString() {
        $parts = [];

        foreach ($this->_attributes as $key => $value) {
            $parts[] = "$key=\"$value\"";
        }

        return implode(" ", $parts);
    }

    public function __toString() {
        $view = View::getInstance();

        return $view->partial("partial/element/{$this->_template}.phtml", [
            'element' => $this
        ]);
    }
}