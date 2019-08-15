<?php

namespace Solsken\Table;

use Solsken\Util;

/**
 * Column class for Table
 */
class Column {
    /**
     * Default config for column
     * @var array
     */
    protected $_config = [
        'key'        => '',
        'label'      => '',
        'formatters' => []
    ];

    /**
     * Array of formatters for this column
     * @var array
     */
    protected $_formatters = [];

    /**
     * Set config, see $this->_config for options
     * @param array $config
     */
    public function __construct(array $config = []) {
        $this->_config = array_merge($this->_config, $config);
    }

    /**
     * Get Label of column
     * @return string
     */
    public function getLabel() {
        return $this->_config['label'] ?: $this->_config['key'];
    }

    /**
     * Return the key of column
     * @return string
     */
    public function getKey() {
        return $this->_config['key'];
    }

    /**
     * Get formatted value for this column from given row
     * @param  array $row  Row of table data to parse
     * @return mixed
     */
    public function getValue($row) {
        $value = isset($row[$this->_config['key']]) ? $row[$this->_config['key']] : '-';

        foreach ($this->_config['formatters'] as $formatter => $formatterConfig) {
            if (is_string($formatterConfig)) {
                $formatter = $formatterConfig;
                $formatterConfig = [];
            }

            $hash = $formatter . serialize($formatterConfig);

            if (!isset($this->_formatters[$hash])) {
                $formatter = '\\Solsken\\Formatter\\' . ucfirst(Util::toCamelCase($formatter));

                $this->_formatters[$hash] = new $formatter($formatterConfig);
            }

            $value = $this->_formatters[$hash]->format($value);
        }

        return $value;
    }
}
