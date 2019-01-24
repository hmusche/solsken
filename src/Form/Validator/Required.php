<?php

namespace Solsken\Form\Validator;

use Solsken\Form\ValidatorAbstract;

class Required extends ValidatorAbstract {
    protected $_error = 'input.required';

    public function isValid($value) {
        return trim($value) !== '';
    }
}
