<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Serializer\DateTimeCallback;
use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;
use App\Service\Task\Exceptions\ExceptionContext;
use App\Service\Task\Exceptions\UpdateException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class TaskManager
{
    public function __construct(
        readonly private Security $security,
        readonly private EntityManagerInterface $entityManager,
        readonly private TaskRepository $taskRepository,
    ) {
    }

    public function getUser(): User
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $user;
    }

    public function create(CreateTaskDto $dto): Task
    {
        $task = new Task();
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setDeadline(
            \DateTimeImmutable::createFromFormat(DateTimeCallback::FORMAT_DAY, $dto->deadline)
                ->setTime(0, 0, 0)
        );
        $task->setStatus(Task::STATUS_NEW);
        $task->setUserEntity($this->getUser());

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @return Task[]
     */
    public function listTasks(TaskListFilterDto $filter): array
    {
        return $this->taskRepository
            ->findByTaskFilter(
                user: $this->getUser(),
                filter: $filter
            );
    }

    public function getById(int $taskId): Task|null
    {
        return $this->taskRepository->findOneById(
            user: $this->getUser(),
            id: $taskId
        );
    }

    public function deleteById(int $taskId): bool
    {
        $task = $this->getById($taskId);
        if (!isset($task)) {
            return false;
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return true;
    }

    public function updateById(int $taskId, UpdateTaskDto $dto): Task
    {
        $task = $this->getById($taskId);

        if (!isset($task)) {
            throw new UpdateException(new ExceptionContext(['message' => 'Task not found']));
        }

        if (isset($dto->title)) {
            $task->setTitle($dto->title);
        }
        if (isset($dto->description)) {
            $task->setDescription($dto->description);
        }
        if (isset($dto->deadline)) {
            $task->setDeadline(
                \DateTimeImmutable::createFromFormat(DateTimeCallback::FORMAT_DAY, $dto->deadline)
                    ->setTime(0, 0, 0)
            );
        }
        if (isset($dto->status)) {
            $task->setStatus($dto->status);
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }
}
