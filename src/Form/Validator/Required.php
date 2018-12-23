<?php

namespace Solsken\Form\Validator;

use Solsken\Form\Validator;

class Required extends Validator {
    protected $_error = 'input.required';

    public function isValid($value) {
        return trim($value) !== '';
    }
}
