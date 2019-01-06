<?php

namespace Solsken\Form\Element;

use Solsken\Form\ElementAbstract;

use Solsken\View;

class File extends ElementAbstract {
    protected $_template = 'file';

    public function populate(Array $data) {
        $this->_value = array_key_exists($this->_name, $data) ? $data[$this->_name] : null;
    }

    public function getPreview() {
        $type = isset($this->_options['preview']) ? $this->_options['preview'] : 'direct';
        $view = View::getInstance();

        return $view->partial('partial/element/file/' . $type . '.phtml', ['value' => $this->_value]);
    }
}
