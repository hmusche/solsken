<?php

namespace Solsken\Table;

use Solsken\Model;

/**
 * Optional Data class to provide row data for Table from Model
 * Implements Iterator so foreach can be used directly on instance
 */
class Data implements \Iterator {
    /**
     * Instance of Solsken\Model
     * @var Solsken\Model
     */
    protected $_model;

    /**
     * Basically the SELECT condition for the wanted columns
     * @var mixed
     */
    protected $_columns = '*';

    /**
     * WHERE condition to pass to model
     * @var array
     */
    protected $_where = [];

    /**
     * LIMIT to pass to model
     * @var array
     */
    protected $_limit = null;

    /**
     * ORDER condition to pass to model
     * @var array
     */
    protected $_order = null;

    /**
     * Container for fetched data
     * @var array
     */
    protected $_data = null;

    /**
     * Position of iterator
     * @var int
     */
    protected $_position = 0;

    /**
     * Construct class with model
     * @param Model $model Instance of Solsken\Model
     */
    public function __construct(Model $model) {
        $this->_model = $model;
    }

    /**
     * Fetch Data from model with given conditions
     * @return array
     */
    public function getData() {
        if ($this->_data === null) {
            $where = $this->_where;

            if ($this->_limit) {
                $where['LIMIT'] = $this->_limit;
            }

            if ($this->_order) {
                $where['ORDER'] = $this->_order;
            }

            $this->_data = $this->_model->select($this->_columns, $where);
        }

        return $this->_data;
    }

    /**
     * Return the total number of results from current conditions
     * @return int
     */
    public function getTotalResults() {
        return $this->_model->count($this->_columns, $this->_where);
    }

    /**
     * Set Columns
     * @param array|string $columns
     */
    public function setColumns($columns) {
        $this->_columns = $columns;
    }

    /**
     * Set WHERE
     * @param array $where
     */
    public function setWhere($where) {
        $this->_where = $where;
    }

    /**
     * Set LIMIT
     * @param array $limit
     */
    public function setLimit($limit) {
        $this->_limit = $limit;
    }

    /**
     * Set ORDER
     * @param array $order
     */
    public function setOrder($order) {
        $this->_order = $order;
    }

    /**
     * Return current row
     * @return array
     */
    public function current() {
        return $this->getData()[$this->_position];
    }

    /**
     * Return current iterator position
     * @return int
     */
    public function key() {
        return $this->_position;
    }

    /**
     * Go to next iterator position
     */
    public function next() {
        $this->_position++;
    }

    /**
     * Reset Iterator to beginning
     */
    public function rewind() {
        $this->_position = 0;
    }

    /**
     * Check if there is data at current iterator position
     * @return bool
     */
    public function valid() {
        return array_key_exists($this->_position, $this->getData());
    }
}
