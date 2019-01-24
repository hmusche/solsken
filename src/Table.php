<?php

namespace Solsken;

use Solsken\Table\Column;

/**
 * Generates Table of information from array
 */
class Table {
    /**
     * Array of columns in table
     * @var array
     */
    protected $_columns = [];

    /**
     * Data in table set
     * @var array
     */
    protected $_data = [];

    /**
     * Array of action URLs for each row
     * @var array
     */
    protected $_actions = [];

    /**
     * Single action which is performed on the whole row
     * @var array
     */
    protected $_rowAction = [];

    /**
     * Add several Columns to table
     * @param array $columns array of $column definitions
     * @return self
     */
    public function addColumns(array $columns = []) {
        foreach ($columns as $key => $column) {
            if (!is_numeric($key) && !isset($column['key'])) {
                $column['key'] = $key;
            }

            $this->addColumn($column);
        }

        return $this;
    }

    /**
     * Add a single column to table
     * @param array $column Column definition
     * @return self
     */
    public function addColumn(array $column) {
        $this->_columns[] = new Column($column);

        return $this;
    }

    /**
     * Add action to table
     * @param string $key    Name of action
     * @param array  $action Definition of action
     * @return self
     */
    public function addAction(string $key, array $action) {
        if (isset($action['row']) && $action['row']) {
            $this->_rowAction = $action;
        } else {
            $this->_actions[$key] = $action;
        }


        return $this;
    }

    /**
     * Check if table has actions
     * @return boolean
     */
    public function hasActions() {
        return $this->_actions !== [];
    }

    /**
     * Check if table has row action
     * @return boolean
     */
    public function hasRowAction() {
        return $this->_rowAction !== [];
    }

    /**
     * Set data to table
     * @param array $data Data is collected by names of columns, and should be present in nested array
     */
    public function setData($data) {
        $this->_data = $data;
    }

    /**
     * Get array of defined columns
     * @return array
     */
    public function getColumns() {
        return $this->_columns;
    }

    /**
     * Get array of all rows for this table
     * @return array 
     */
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
