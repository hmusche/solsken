<?php

namespace Solsken\Form;

/**
 * Base class for Form validation
 */
abstract class ValidatorAbstract {
    /**
     * Error message
     * @var string
     */
    protected $_error;

    /**
     * Array of options
     * @var array
     */
    protected $_options;

    /**
     * Set options
     * @param array $options
     */
    public function __construct($options) {
        $this->_options = $options;
    }

    /**
     * Abstract class to check if given value is valid
     * @param  mixed   $value Value to check
     * @return boolean
     */
    abstract public function isValid($value);

    /**
     * Return error message
     * @return string
     */
    public function getError() {
        return $this->_error;
    }
}
