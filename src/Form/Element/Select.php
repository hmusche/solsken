<?php

namespace Solsken\Form\Element;

use Solsken\Form\ElementAbstract;

class Select extends ElementAbstract {
    protected $_template = 'select';

    protected $_values = [];

    public function getSelect() {
        return $this->_values;
    }

    public function populate(Array $data) {
        $values = [];

        if (isset($this->_options['values'])) {
            $values = call_user_func($this->_options['values'], $this->_name);
        }

        $this->_values = $values;
        $this->_value  = array_key_exists($this->_name, $data) && array_key_exists($data[$this->_name], $values) ? $data[$this->_name] : null;
    }
}
