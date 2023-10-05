<?php

namespace App\Exception;

use Exception;

class WrongFileExtensionException extends Exception
{

    public function __construct(string $extension, string $expectedExtension)
    {
        parent::__construct('File extension '.$extension.' is wrong. Expected: '.$expectedExtension);
    }
}