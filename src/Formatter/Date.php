<?php

namespace Solsken\Formatter;

use Solsken\I18n;

/**
 * Format given date according to current locale
 */
class Date extends FormatterAbstract {
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
        $timeFmt = null;

        foreach (['date', 'time'] as $key) {
            $val = strtoupper($this->_config[$key]);
            $var = "{$key}Fmt";
            $$var = constant("\IntlDateFormatter::$val");
        }

        $formatter = new \IntlDateFormatter($i18n->getLocale(), $dateFmt, $timeFmt);

        return $formatter->format($value);
    }
}
