<?php

namespace Vongola\Imgur\Exceptions;

use RuntimeException;

class MissingArgumentException extends RuntimeException
{
    public function __construct($required, $code = 0, $previous = null)
    {
        if (is_string($required)) {
            $required = [$required];
        }

        parent::__construct(
            sprintf('One or more of required ("%s") parameters is missing!', implode('", "', $required)),
            $code,
            $previous
        );
    }
}
