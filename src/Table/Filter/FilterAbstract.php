<?php

namespace Solsken\Table\Filter;

/**
 * Abstract class to define Filters for Table
 */
abstract class FilterAbstract {
    /**
     * Apply the filter to the given WHERE statement
     * @param array  $where
     * @param string $key   Key of Column
     * @param mixed  $value Value to Set
     */
    abstract public function apply(array $where, string $key, $value);
}
