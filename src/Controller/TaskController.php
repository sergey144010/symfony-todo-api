<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;
use App\Service\Task\Exceptions\UpdateException;
use App\Service\Task\TaskManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Nelmio\ApiDocBundle\Attribute\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

class TaskController extends JsendController
{
    public function __construct(
        readonly private TaskManagerInterface $taskManager,
    ) {
    }

    #[OA\Post(
        summary: 'Создать новую задачу',
        tags: ['Задача']
    )]
    #[Security(name: 'Bearer')]
    #[OA\Response(
        response: 200,
        description: 'Успешное создание задачи',
        content: new OA\JsonContent(
            properties:
            [
                new OA\Property(
                    property: 'status',
                    type: 'string',
                    example: 'success'
                ),
                new OA\Property(
                    property: 'data',
                    type: 'object',
                    properties:
                        [
                            new OA\Property(
                                property: 'task',
                                ref: new Model(type: Task::class)
                            )
                        ]
                )
            ]
        )
    )]
    #[Route(
        '/api/task',
        name: 'api_task_create',
        methods: [Request::METHOD_POST]
    )]
    public function createTask(
        #[MapRequestPayload] CreateTaskDto $dto,
    ): JsonResponse {
        $task = $this->taskManager->create($dto);

        return $this->respondSuccess(
            [
                'task' => $task
            ]
        );
    }

    #[OA\Get(
        summary: 'Список задач',
        tags: ['Задача'],
    )]
    #[Security(name: 'Bearer')]
    #[Route(
        '/api/task',
        name: 'api_task_get_list',
        methods: [Request::METHOD_GET]
    )]
    public function getTaskList(
        #[MapQueryString] TaskListFilterDto $filterDto,
    ): JsonResponse {
        $list = $this->taskManager->listTasks($filterDto);

        return $this->respondSuccess($list);
    }

    #[OA\Get(
        summary: 'Одна задача',
        tags: ['Задача'],
    )]
    #[Security]
    #[Route(
        '/api/task/{id}',
        name: 'api_task_get_one',
        methods: [Request::METHOD_GET],
        requirements: ['id' => '\d+']
    )]
    public function getTask(
        int $id,
    ): JsonResponse {
        $task = $this->taskManager->getById($id);

        if (!isset($task)) {
            return $this->respondFail(
                [
                    'message' => 'Task not found',
                ]
            );
        }

        return $this->respondSuccess(
            [
                'task' => $task
            ]
        );
    }

    #[OA\Delete(
        summary: 'Удалить задачу',
        tags: ['Задача'],
    )]
    #[Security(name: 'Bearer')]
    #[Route(
        '/api/task/{id}',
        name: 'api_task_delete_one',
        methods: [Request::METHOD_DELETE],
        requirements: ['id' => '\d+']
    )]
    public function deleteTask(
        int $id,
    ): JsonResponse {
        if ($this->taskManager->deleteById($id)) {
            return $this->respondSuccess(
                [
                    'message' => 'Success deleted',
                ]
            );
        }

        return $this->respondFail(
            [
                'message' => 'Task not found',
            ]
        );
    }

    #[OA\Patch(
        summary: 'Обновить задачу',
        tags: ['Задача'],
    )]
    #[Security(name: 'Bearer')]
    #[Route(
        '/api/task/{id}',
        name: 'api_task_update',
        methods: [Request::METHOD_PATCH],
        requirements: ['id' => '\d+']
    )]
    public function updateTask(
        int $id,
        #[MapRequestPayload] UpdateTaskDto $dto,
    ): JsonResponse {
        try {
            $task = $this->taskManager->updateById($id, $dto);
        } catch (UpdateException $e) {
            return $this->respondFail($e->context->context);
        }

        return $this->respondSuccess(
            [
                'task' => $task
            ]
        );
    }
}
