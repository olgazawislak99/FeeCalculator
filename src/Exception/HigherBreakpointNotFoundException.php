<?php

namespace App\Exception;

use Exception;

class HigherBreakpointNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Higher breakpoint not found in array');
    }
}