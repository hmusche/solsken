<?php

namespace Solsken;

use Solsken\Table\Column;

class Table {
    protected $_columns = [];
    protected $_data = [];
    protected $_actions = [];
    protected $_rowAction = [];

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
        if (isset($action['row']) && $action['row']) {
            $this->_rowAction = $action;
        } else {
            $this->_actions[$key] = $action;
        }


        return $this;
    }

    public function hasActions() {
        return $this->_actions !== [];
    }

    public function hasRowAction() {
        return $this->_rowAction !== [];
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
            $row = [
                'actions' => [],
                'rowAction' => [],
                'columns' => []
            ];

            if ($this->_actions !== []) {
                $actions = [];

                foreach ($this->_actions as $key => $action) {
                    preg_match('#{(.+)}#', $action['href'], $match);

                    $action['href'] = isset($match[1]) && isset($dataRow[$match[1]])
                                    ? str_replace($match[0], $dataRow[$match[1]], $action['href'])
                                    : '#';

                    $actions[$key] = $action;
                }

                $row['actions'] = $actions;
            }

            if ($this->_rowAction) {
                $rowAction = $this->_rowAction;

                preg_match('#{(.+)}#', $this->_rowAction['href'], $match);

                $rowAction['href'] = isset($match[1]) && isset($dataRow[$match[1]])
                                   ? str_replace($match[0], $dataRow[$match[1]], $rowAction['href'])
                                   : '#';

                $row['rowAction'] = $rowAction;

            }

            foreach ($this->_columns as $column) {
                $row['columns'][] = $column->getValue($dataRow);
            }

            $return[] = $row;
        }

        return $return;
    }




}
