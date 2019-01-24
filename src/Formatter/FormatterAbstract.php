<?php

namespace Solsken\Formatter;

/**
 * Abstract class for formatters
 */
abstract class FormatterAbstract {
    /**
     * Array of possible options
     * @var array
     */
    protected $_config;

    /**
     * Basic constructor, set options
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->_config = array_merge($this->_config, $config);
    }

    /**
     * Abstract method to format given value
     * @var mixed $value
     * @return mixed
     */
    abstract public function format($value);
}
