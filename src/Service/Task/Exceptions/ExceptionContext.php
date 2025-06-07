<?php

namespace App\Service\Task\Exceptions;

class ExceptionContext
{
    /**
     * @param Array<string, string> $context
     */
    public function __construct(
        readonly public array $context,
    ) {
    }
}
