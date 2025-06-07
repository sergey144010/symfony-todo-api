<?php

namespace App\Service\Task\Exceptions;

class UpdateException extends TaskManagerException
{
    public function __construct(
        readonly public ExceptionContext $context
    ) {
        parent::__construct();
    }
}
