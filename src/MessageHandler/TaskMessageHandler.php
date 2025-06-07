<?php

namespace App\MessageHandler;

use App\Message\TaskMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class TaskMessageHandler
{
    public function __invoke(TaskMessage $message): void
    {
        echo "Processing task ID: " . $message->getTaskId() . "\n";
    }
}
