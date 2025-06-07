<?php

namespace App\Tests\Functional\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Repository\TaskRepository;

class TaskControllerTest extends ApiTestCase
{
    public function testCreate(): void
    {
        self::bootKernel();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' => [
                    "email" => "test@test.com",
                    "password" => "0123456789",
                    "name" => "Test123"
                ]
            ]
        );
        $client->getResponse()->getContent();

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/token',
            [
                'json' => [
                    "username" => "test@test.com",
                    "password" => "0123456789"
                ]
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);
        $token = $data['token'];

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/task',
            [
                'auth_bearer' => $token,
                'json' => [
                    "title" => "Task title 1",
                    "description" => "Task description 555",
                    "deadline" => "2025-12-12",
                ]
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('success', $data['status']);
        self::assertEquals('Task title 1', $data['data']['task']['title']);
        self::assertEquals('Task description 555', $data['data']['task']['description']);
        self::assertEquals('2025-12-12 00:00:00', $data['data']['task']['deadline']);
        self::assertEquals(0, $data['data']['task']['status']);

        $container = static::getContainer();
        /** @var TaskRepository $repository */
        $repository = $container->get(TaskRepository::class);
        $list = $repository->findAll();

        self::assertCount(1, $list);
        self::assertEquals('Task title 1', $list[0]->getTitle());
        self::assertEquals(0, $list[0]->getStatus());
    }
}
