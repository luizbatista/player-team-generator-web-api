<?php

namespace App\Exceptions;

use Exception;

class PlayerNotFoundException extends Exception
{
    public function __construct($message = "Player not found", $code = 404)
    {
        parent::__construct($message, $code);
    }
}
