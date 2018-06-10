<?php

namespace Gearman;

class GearmanClient extends \GearmanClient {

    public  $job_name       = '';
    public  $try            = 0;
    private $push_method    = null;

    /**
     * construct
     *
     * @param  array $config
     * @return object
     * @access private
     */
    final public function __construct(array $config, string $job_name, $try = 0) {
        $this->job_name = $job_name;
        $this->try      = $try;

        $method     = 'do';
        if ( $config['priority'] !== 1 ) {
            $method    .= $config['priority'] ? 'High' : 'Low';
            $method    .= $config['background'] ? 'Background' : '';
        }
        else {
            $method    .= $config['background'] ? 'Background' : 'Normal';
        }
        $this->push_method  = $method;

        parent::__construct();
    }

    /**
    /**
     * Run a task
     *
     * @param  mixid $workload Data to be processed
     * @param  mixid $unique   A unique ID used to identify a particular task
     * @return boolean
     * @access public
     * @link   http://php.net/manual/ja/class.gearmanclient.php
     */
    public function push($workload, $unique = null) {
        $try    = $this->try;
        $method = $this->push_method;
        do {
            try {
                $try--;
                parent::{$method}($this->job_name, serialize($workload), $unique);
                break;
            } catch (\PhpErrorException $e) {
                if ( $try === 0 ) {
                    throw new GearmanException('Client failed to push.', $e->getCode(), $e->getPrevious(), $e->getFile(), $e->getLine());
                }
            }
        } while ( $try );

        if (parent::returnCode() !== GEARMAN_SUCCESS) {
            return  false;
        }
        return  true;
    }


}