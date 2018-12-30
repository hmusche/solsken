<?php

namespace Solsken\Form\Element;

use Solsken\Form\ElementAbstract;

class File extends ElementAbstract {
    protected $_template = 'file';

    public function populate(Array $data) {
        $this->_value = array_key_exists($this->_name, $data) ? $data[$this->_name] : null;
    }
}
