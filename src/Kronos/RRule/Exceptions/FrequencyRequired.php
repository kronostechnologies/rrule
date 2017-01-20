<?php

namespace Kronos\RRule\Exceptions;

class FrequencyRequired extends \Exception {
    public function __construct($message = "You are required to set a frequency.", $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}