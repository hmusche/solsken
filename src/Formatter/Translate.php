<?php

namespace Solsken\Formatter;

use Solsken\I18n;

/**
 * Format given date according to current locale
 */
class Translate extends FormatterAbstract {
    /**
     * Default options for date formatter
     * @var array
     */
    protected $_config = [
        'date' => 'short',
        'time' => 'short'
    ];

    /**
     * Return given timestamp in properly formatted way
     * @param  int    $value UNIX timestamp
     * @return string
     */
    public function format($value) {
        $i18n = I18n::getInstance();

        $dateFmt = null;

        return $i18n->translate($value);
    }
}
