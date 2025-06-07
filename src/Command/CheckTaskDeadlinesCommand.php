<?php

namespace App\Command;

use App\Entity\Task;
use App\Message\TaskMessage;
use App\Repository\TaskRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'CheckTaskDeadlines',
    description: 'Add a short description for your command',
)]
class CheckTaskDeadlinesCommand extends Command
{
    public function __construct(
        readonly private TaskRepository $taskRepository,
        readonly private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var Array<Task> $tasks */
        $tasks = $this->taskRepository->findAll();

        $today = new \DateTimeImmutable('today');

        foreach ($tasks as $task) {
            $deadline = $task->getDeadline();

            if ($deadline && $deadline->format('Y-m-d') === $today->format('Y-m-d')) {
                $this->messageBus->dispatch(new TaskMessage($task->getId()));
            }
        }

        return Command::SUCCESS;
    }
}
