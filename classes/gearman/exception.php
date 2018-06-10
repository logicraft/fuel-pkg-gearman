<?php

namespace Gearman;

class GearmanException extends \FuelException {

    function __construct($message, $code = 0, $previous = null, $file = null, $line = null) {
        parent::__construct($message, $code, $previous);
        if ( $file ) {  $this->file = $file;    }
        if ( $line ) {  $this->line = $line;    }
    }

}
