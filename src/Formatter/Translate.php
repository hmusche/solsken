<?php

namespace Solsken\Formatter;

use Solsken\I18n;

/**
 * Translate string
 */
class Translate extends FormatterAbstract {
    /**
     * Default options for translate formatter
     * @var array
     */
    protected $_config = [
        'prefix' => '',
        'suffix' => ''
    ];

    /**
     * Return given timestamp in properly formatted way
     * @param  int    $value UNIX timestamp
     * @return string
     */
    public function format($value) {
        $i18n = I18n::getInstance();

        $dateFmt = null;

        return $i18n->translate($this->_config['prefix'] . $value . $this->_config['suffix']);
    }
}
