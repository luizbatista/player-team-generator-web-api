<?php

namespace App\Exceptions;

use Exception;

class ProcessTeamSelectionException extends Exception
{
    public function __construct($message = "Failed to process team selection", $code = 500)
    {
        parent::__construct($message, $code);
    }
}
