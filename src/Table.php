<?php

namespace Solsken;

use Solsken\Table\Column;
use Solsken\Table\Data;
use Solsken\Request;
use Solsken\View;
use Solsken\Registry;
use Solsken\Cookie;

/**
 * Generates Table of information from array
 */
class Table {
    /**
     * Identifier for this table
     * @var string
     */
    protected $_identifier;

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
     * Current configuration of table
     * @var array $_tableConfig
     */
    protected $_tableConfig = [];

    /**
     * Where condition for data Model
     * @var array
     */
    protected $_where = [];

    /**
     * Construct Table instance, optionally named for uniqueness
     * @param string $identifier
     */
    public function __construct($identifier = null) {
        if ($identifier) {
            $this->_identifier = $identifier;
        } else {
            $this->_identifier = uniqid();
        }
    }

    /**
     * Return identifier
     * @return string
     */
    public function getIdentifier() {
        return $this->_identifier;
    }

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
     * @return bool
     */
    public function hasActions() {
        return $this->_actions !== [];
    }

    /**
     * Check if table has row action
     * @return bool
     */
    public function hasRowAction() {
        return $this->_rowAction !== [];
    }

    /**
     * Set data to table
     * @param array $data Data is collected by names of columns, and should be present in nested array
     */
    public function setData($data) {
        if ($data instanceof Model) {
            $data = new Data($data);
        }

        $this->_data = $data;
    }

    /**
     * Set WHERE condition for data Model
     * @param array $where
     */
    public function setWhere($where) {
        if ($this->_data instanceof Model) {
            $this->_where = $where;
        }
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

    /**
     * Returns total number of rows
     * @return int
     */
    public function getTableConfig() {
        return $this->_tableConfig;
    }

    /**
     * Get the data from the Data Model according to set filters, and return the proper output
     * @return string
     */
    public function handle() {
        if ($this->_data instanceof Data) {
            $req       = Request::getInstance();
            $config    = Registry::get('app.config');
            $perPage   = isset($config['table']['per_page']) ? $config['table']['per_page'] : 20;
            $maxPages  = isset($config['table']['max_pages']) ? $config['table']['max_pages'] : 9;

            if ($req->getParam('table_identifier', $this->getIdentifier()) != $this->getIdentifier()) {
                return;
            }

            $defaults = [
                'filter' => [],
                'order'  => [],
                'page'   => 1
            ];

            $tableConfig = json_decode(Cookie::get($this->_identifier . '_config', '[]'), true);

            foreach ($defaults as $key => $value) {
                if (isset($tableConfig[$this->_identifier . '_' . $key])) {
                    $defaults[$key] = $tableConfig[$this->_identifier . '_' . $key];
                }

                $$key = $req->getParam($this->_identifier . '_' . $key, $defaults[$key]);
            }

            $where  = $this->_where;

            foreach ($this->getColumns() as $column) {
                if (($filterClass = $column->getFilter()) && isset($filter[$column->getKey()])) {
                    $filterClass = '\\Solsken\\Table\\Filter\\'. ucfirst($filterClass);
                    $filterClass = new $filterClass;

                    $where = $filterClass->apply($where, $column->getKey(), $filter[$column->getKey()]);
                }

                if (!$order && $columnOrder = $column->getOrder()) {
                    $order[$column->getKey()] = $columnOrder;
                }
            }

            $this->_data->setOrder($order);
            $this->_data->setWhere($where);

            $totalRows = $this->_data->getTotalResults();

            $totalPages = ceil($totalRows / $perPage);

            if ($page > $totalPages) {
                $page = $totalPages;
            }

            $limit = [($page - 1) * $perPage, $perPage];
            $this->_data->setLimit($limit);

            if ($page <= ($maxPages - 1) / 2) {
                $fromPage = 1;
            } else if ($page > ($totalPages - (($maxPages - 1) / 2))) {
                $fromPage = $totalPages - $maxPages + 1;
            } else {
                $fromPage = $page - (($maxPages - 1) / 2);
            }

            $this->_tableConfig = [
                'pagination' => [
                    'per_page'    => $perPage,
                    'total_rows'  => $totalRows,
                    'total_pages' => $totalPages,
                    'max_pages'   => $maxPages,
                    'page'        => $page,
                    'pages_from'  => $fromPage
                ],
                'filter'      => $filter,
                'order'       => $order
            ];
        }

        if ($req->get('is_xhr')) {
            header('Content-Type: application/javascript');
            echo json_encode([
                'status' => 'success',
                'html' => $this->render()
            ]);

            exit;
        } else {
            return $this->render();
        }
    }

    /**
     * Returns a rendered string of current table
     * @return string
     */
    public function render() {
        $view = View::getInstance();

        return $view->partial('template/partial/table.phtml', ['table' => $this]);
    }

}
