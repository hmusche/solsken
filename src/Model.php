<?php

namespace Solsken;

use Solsken\Registry;

/**
 * Base class for Database Models
 */
class Model {
    /**
     * Name of current DB table
     * @var String
     */
    protected $_name;

    /**
     * Instance of database connection
     * @var Medoo\Medoo
     */
    protected $_db;

    /**
     * Constructor, get DB instance
     */
    public function __construct() {
        $this->_db = Registry::get('app.db');
    }

    /**
     * Magic method to call methods in Medoo Database connection. Table is prepended to arguments
     *
     * @param  String $method
     * @param  Array $args
     * @return Mixed
     */
    public function __call($method, $args) {
        if (method_exists($this->_db, $method)) {
            if ($method != 'query') {
                $args = array_merge([$this->_name], $args);
            }

            return call_user_func_array([$this->_db, $method], $args);
        } else {
            throw new \Exception('Unknown DB method ' . $method);
        }
    }

    /**
     * Helper class to get a Select array from an ENUM column
     * @param  string $column Name of ENUM column
     * @return array          Array of enum values and theit translation keys
     */
    public function getEnumSelect($column) {
        $result = $this->query("SHOW COLUMNS FROM {$this->_name} WHERE FIELD = '{$column}'")->fetch();
        $return = [];

        if ($result && strpos($result['Type'], 'enum') === 0) {
            $enum = substr($result['Type'], 5, -1);

            if ($result['Null'] != 'NO') {
                $return[''] = 'please.select';
            }

            foreach (explode(',', $enum) as $value) {
                $value = str_replace('\'', '', $value);
                $return[$value] = $this->_name . '.' . $column . '.' . $value;
            }
        }

        return $return;
    }
}
