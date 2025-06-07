<?php

namespace App\Tests\Unit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Service\Registration\RegistrationDto;
use App\Service\Registration\RegistrationService;
use App\Service\Task\Dto\CreateTaskDto;
use App\Service\Task\Dto\TaskListFilterDto;
use App\Service\Task\Dto\UpdateTaskDto;
use App\Service\Task\TaskManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class TaskManagerTest extends ApiTestCase
{
    private readonly User $user;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->user = $this->createUser();
    }

    protected function createUser(): User
    {
        $container = static::getContainer();

        /** @var RegistrationService $service */
        $service = $container->get(RegistrationService::class);

        $dto = new RegistrationDto();
        $dto->email = 'test2@test2.com';
        $dto->password = '0123456789';
        $dto->name = 'TestName2';

        return $service->registration($dto);
    }

    protected function initTaskManager(): TaskManager
    {
        $security = $this->createMock(Security::class);
        $security->expects(self::any())
            ->method('getUser')
            ->willReturn($this->user)
        ;

        $container = static::getContainer();
        /** @var EntityManager $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var TaskRepository $repository */
        $repository = $container->get(TaskRepository::class);

        return new TaskManager(
            security: $security,
            entityManager: $em,
            taskRepository:  $repository
        );
    }

    protected function createTask(string $title, string $description, string $deadline): Task
    {
        $manager = $this->initTaskManager();

        $dto = new CreateTaskDto(
            title: $title,
            description: $description,
            deadline: $deadline
        );

        return $manager->create($dto);
    }

    public function testCreate(): void
    {
        $manager = $this->initTaskManager();

        $dto = new CreateTaskDto(
            title: 'Test task 1',
            description: 'One one one',
            deadline: '2025-10-10'
        );
        $task = $manager->create($dto);

        $container = static::getContainer();
        /** @var TaskRepository $repository */
        $repository = $container->get(TaskRepository::class);
        $taskRepo = $repository->find($task->getId());

        self::assertInstanceOf(Task::class, $taskRepo);
        self::assertEquals('Test task 1', $taskRepo->getTitle());
        self::assertEquals(0, $taskRepo->getStatus());
    }

    public function testListFilter(): void
    {
        $this->createTask('Title1', 'Description1', '2020-10-10');
        $this->createTask('Title2', 'Description2', '2020-11-11');
        $this->createTask('Title3', 'Description3', '2020-10-10');
        $this->createTask('Title4', 'Description4', '2020-10-10');

        $manager = $this->initTaskManager();

        $dto = new TaskListFilterDto(status: 0, deadline: '2020-10-10');
        $list = $manager->listTasks($dto);

        self::assertCount(3, $list);
        self::assertEquals('Title1', $list[0]->getTitle());
        self::assertEquals(0, $list[0]->getStatus());
    }

    public function testUpdate(): void
    {
        $task = $this->createTask('Title1', 'Description1', '2020-10-10');

        self::assertEquals(0, $task->getStatus());

        $id = $task->getId();

        $manager = $this->initTaskManager();

        $dto = new UpdateTaskDto(
            title: 'Title111',
            status: 1,
            description: 'Description111',
            deadline: '2020-11-11'
        );
        $task = $manager->updateById($id, $dto);

        self::assertEquals(1, $task->getStatus());
        self::assertEquals('Title111', $task->getTitle());
        self::assertEquals('Description111', $task->getDescription());
        self::assertEquals('2020-11-11', $task->getDeadline()->format('Y-m-d'));
    }
}
