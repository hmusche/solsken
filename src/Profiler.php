<?php

namespace Solsken;

class Profiler {
    /**
     * microtime timestamp of start of profiling
     * @var int
     */
    static protected $_start;

    /**
     * Helper var to store last breakpoint
     * @var int
     */
    static protected $_lastBreakpoint;

    /**
     * Array of breakpoints with theit runtime
     * @var array
     */
    static protected $_breakpoints = [];

    /**
     * Set start of profiling
     */
    static public function start() {
        self::$_start = microtime(true);
        self::$_lastBreakpoint = self::$_start;
    }

    /**
     * Add Breakpoint with given key
     * @param string $key
     */
    static public function addBreakpoint($key) {
        if (!self::$_start) {
            self::start();
        }

        $time                     = microtime(true);
        self::$_breakpoints[$key] = round(($time - self::$_lastBreakpoint) * 1000, 2);
        self::$_lastBreakpoint    = $time;
    }

    /**
     * Return array of breakpoints
     */
    static public function getBreakpoints() {
        return self::$_breakpoints;
    }

    /**
     * Returns current runtime
     */
    static public function getRuntime() {
        if (!self::$_start) {
            self::$_start = microtime(true);
        }

        return round((microtime(true) - self::$_start) * 1000, 2);
    }
}
