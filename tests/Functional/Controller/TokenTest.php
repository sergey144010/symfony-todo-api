<?php

namespace App\Tests\Functional\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class TokenTest extends ApiTestCase
{
    public function testTakeToken(): void
    {
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
        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('success', $data['status']);
        self::assertEquals('User registered successfully', $data['data']['message']);

        $client2 = static::createClient();
        $client2->request(
            'POST',
            '/api/token',
            [
                'json' => [
                    "username" => "test@test.com",
                    "password" => "0123456789"
                ]
            ]
        );

        $data = json_decode($client2->getResponse()->getContent(), true);

        self::assertNotEmpty($data['token']);
    }
}
