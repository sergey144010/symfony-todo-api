<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Entity\User;
use App\Service\Key\KeyManager;
use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TaskManagerCache implements TaskManagerInterface
{
    public function __construct(
        readonly private TaskManager $taskManager,
        readonly private CacheInterface $cache,
        readonly private KeyManager $keyManager,
        readonly private Security $security,
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
        $task = $this->taskManager->create($dto);
        $this->cache->delete(
            $this->keyManager->cacheListKey(
                $this->getUser()
            )
        );

        return $task;
    }

    public function listTasks(TaskListFilterDto $filter): array
    {
        return $this->cache->get(
            $this->keyManager->cacheListKey($this->getUser()),
            function (ItemInterface $item) use ($filter) {
                $item->expiresAfter(30);
                return $this->taskManager->listTasks($filter);
            }
        );
    }

    public function getById(int $taskId): Task|null
    {
        return $this->taskManager->getById($taskId);
    }

    public function deleteById(int $taskId): bool
    {
        if ($this->taskManager->deleteById($taskId)) {
            $this->cache->delete(
                $this->keyManager->cacheListKey($this->getUser())
            );

            return true;
        }

        return false;
    }

    public function updateById(int $taskId, UpdateTaskDto $dto): Task
    {
        try {
            $task = $this->taskManager->updateById($taskId, $dto);
        } catch (\Throwable $e) {
            throw $e;
        }

        $this->cache->delete(
            $this->keyManager->cacheListKey($this->getUser())
        );

        return $task;
    }
}
