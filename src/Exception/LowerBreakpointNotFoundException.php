<?php

namespace App\Exception;

use Exception;

class LowerBreakpointNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('Lower breakpoint not found in array');
    }
}