<?php

namespace App\Controller;

use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;
use App\Service\Task\Exceptions\UpdateException;
use App\Service\Task\TaskManager;
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
        readonly private TaskManager $taskManager,
    ) {
    }

    #[Security(name: 'Bearer')]
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
        description: 'Get filtered tasks list',
        parameters: [
            new OA\QueryParameter(ref: '#/components/schemas/TaskListFilterDto')
        ],
        responses: [
            new OA\Response(response: 200, description: 'Success')
        ]
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
        return $this->respondSuccess(
            $this->taskManager->listTasks($filterDto)
        );
    }

    #[Security(name: 'Bearer')]
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
