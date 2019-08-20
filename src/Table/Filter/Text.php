<?php

namespace Solsken\Table\Filter;

use Solsken\Table\Filter\FilterAbstract;

/**
 * Text Filter for Table
 */
class Text extends FilterAbstract {
    /**
     * Tries to set Filter to Where, support simple operator on search string
     * @param  array  $where
     * @param  string $key
     * @param  mixed  $value
     * @return array
     */
    public function apply($where, $key, $value) {
        $symbol = substr($value, 0, 1);

        switch ($symbol) {
            case '=':
                break;

            case '>':
            case '<':
                $key = "{$key}[{$symbol}]";
                break;

            default:
                $key = "{$key}[~]";
                break;
        }

        if (!isset($where[$key])) {
            $where[$key] = $value;
        } elseif (is_array($where[$key])) {
            $where[$key][] = $value;
        } else {
            $where[$key] = [$value, $where[$key]];
        }

        return $where;
    }
}
