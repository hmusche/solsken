<?php

namespace Solsken\Form\Validator;

use Solsken\Form\Validator;
use Solsken\Request;

class Match extends Validator {
    protected $_error = 'not.matching';

    public function isValid($value) {
        $against = is_array($this->_options) ? $this->_options['against'] : $this->_options;
        $request = Request::getInstance();

        return trim($value) === trim($request->getParam($against));
    }
}
