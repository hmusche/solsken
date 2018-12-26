<?php

namespace Solsken;

use Solsken\Table\Column;

class Table {
    protected $_columns = [];
    protected $_data = [];
    protected $_actions = [];

    public function __construct() {

    }

    public function addColumns(array $columns = []) {
        foreach ($columns as $key => $column) {
            if (!is_numeric($key) && !isset($column['key'])) {
                $column['key'] = $key;
            }

            $this->addColumn($column);
        }

        return $this;
    }

    public function addColumn(array $column) {
        $this->_columns[] = new Column($column);

        return $this;
    }

    public function addAction(string $key, array $action) {
        $this->_actions[$key] = $action;

        return $this;
    }

    public function hasActions() {
        return $this->_actions !== [];
    }

    public function setData($data) {
        $this->_data = $data;
    }

    public function getColumns() {
        return $this->_columns;
    }

    public function getRows() {
        $return = [];

        foreach ($this->_data as $dataRow) {
            $row = [];

            if ($this->_actions !== []) {
                $actions = [];

                foreach ($this->_actions as $key => $action) {
                    preg_match('#{(.+)}#', $action['href'], $match);

                    $action['href'] = isset($match[1]) && isset($dataRow[$match[1]])
                                    ? str_replace($match[0], $dataRow[$match[1]], $action['href'])
                                    : '#';

                    $actions[$key] = $action;
                }

                $row[] = $actions;
            }

            foreach ($this->_columns as $column) {
                $row[] = $column->getValue($dataRow);
            }

            $return[] = $row;
        }

        return $return;
    }




}
