<?php

namespace App\Exception;

use Exception;

class BreakpointFileNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct('File for breakpoint generation not found');
    }
}