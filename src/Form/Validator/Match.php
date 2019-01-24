<?php

namespace Solsken\Form\Validator;

use Solsken\Form\ValidatorAbstract;
use Solsken\Request;

class Match extends ValidatorAbstract {
    protected $_error = 'not.matching';

    public function isValid($value) {
        $against = is_array($this->_options) ? $this->_options['against'] : $this->_options;
        $request = Request::getInstance();

        return trim($value) === trim($request->getParam($against));
    }
}
