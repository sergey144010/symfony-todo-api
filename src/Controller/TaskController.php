<?php

namespace App\Controller;

use App\Entity\User;
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
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TaskController extends JsendController
{
    public const CACHE_LIST = 'tasks_list';

    public function __construct(
        readonly private TaskManager $taskManager,
        readonly private CacheInterface $cache,
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
        #[CurrentUser] User $user,
    ): JsonResponse {
        $task = $this->taskManager->create($dto);
        $this->cache->delete($this->cacheListKey($user));

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
        #[CurrentUser] User $user,
    ): JsonResponse {
        $list = $this->cache->get(
            $this->cacheListKey($user),
            function (ItemInterface $item) use ($filterDto) {
                $item->expiresAfter(30);
                return $this->taskManager->listTasks($filterDto);
            }
        );

        return $this->respondSuccess($list);
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
        #[CurrentUser] User $user,
    ): JsonResponse {
        if ($this->taskManager->deleteById($id)) {
            $this->cache->delete($this->cacheListKey($user));

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
        #[CurrentUser] User $user,
    ): JsonResponse {
        try {
            $task = $this->taskManager->updateById($id, $dto);
        } catch (UpdateException $e) {
            return $this->respondFail($e->context->context);
        }
        $this->cache->delete($this->cacheListKey($user));

        return $this->respondSuccess(
            [
                'task' => $task
            ]
        );
    }

    private function cacheListKey(User $user): string
    {
        return self::CACHE_LIST . '_' . $user->getId();
    }
}
