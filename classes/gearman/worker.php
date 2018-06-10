<?php

namespace Gearman;

class GearmanWorker extends \GearmanWorker {

    public $job_name    = '';

    /**
     * construct
     *
     * @param  string $name job name
     * @return object
     * @access private
     */
    final public function __construct() {
        parent::__construct();
    }

    /**
    /**
     * Ailias of addFunction
     *   Register and add callback function
     *
     * @param  callable $function A callback that gets called when a job for the registered function name is submitted
     * @param  mixid    $context  A reference to arbitrary application context data that can be modified by the worker function
     * @param  integer  $timeout  An interval of time in seconds
     * @return boolean
     * @access public
     * @link   http://php.net/manual/ja/gearmanworker.addfunction.php
     */
    public function process(callable $function, &$context = null, $timeout = null) {
        return  parent::addFunction($this->job_name, $function, $context, $timeout);
    }


}