<?php

namespace Solsken\Form\Element;

use Solsken\Form\ElementAbstract;

class Select extends ElementAbstract {
    protected $_template = 'select';

    protected $_values = [];

    /**
     * Overwrite Parent select to prefill select option
     * @param string $name
     * @param array  $options [description]
     * @param string $value   [description]
     */
    public function __construct($name, $options, $value = null) {
        parent::__construct($name, $options, $value);

        $this->getSelect();
    }

    /**
     * Return all options of select
     * @return array
     */
    public function getSelect() {
        if ($this->_values) {
            return $this->_values;
        }

        $values = [];

        if (isset($this->_options['values'])) {
            $values = call_user_func($this->_options['values'], $this->_name);
        }

        $this->_values = $values;

        return $this->_values;
    }

    public function populate(Array $data) {
        $this->_value  = array_key_exists($this->_name, $data) && array_key_exists($data[$this->_name], $this->_values) ? $data[$this->_name] : null;
    }
}
