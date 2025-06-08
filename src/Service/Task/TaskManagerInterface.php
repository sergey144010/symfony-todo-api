<?php

namespace App\Service\Task;

use App\Entity\Task;
use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;

interface TaskManagerInterface
{
    public function create(CreateTaskDto $dto): Task;

    /**
     * @return Task[]
     */
    public function listTasks(TaskListFilterDto $filter): array;

    public function getById(int $taskId): Task|null;

    public function deleteById(int $taskId): bool;

    public function updateById(int $taskId, UpdateTaskDto $dto): Task;
}
