<?php

declare(strict_types=1);

namespace Engine\Exceptions;

use Throwable;

class UnknownClassException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}