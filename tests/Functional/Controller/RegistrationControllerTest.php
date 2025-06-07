<?php

namespace App\Tests\Functional\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpClient\Exception\ClientException;

class RegistrationControllerTest extends ApiTestCase
{
    public function testRegisterSuccess(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' =>             [
                    "email" => "test@test.com",
                    "password" => "0123456789",
                    "name" => "Test123"
                ]
            ]
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        self::assertEquals('success', $data['status']);
        self::assertEquals('User registered successfully', $data['data']['message']);
    }

    public function testRegisterValidationEmailError(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' =>             [
                    "email" => "123",
                    "password" => "0123456789",
                    "name" => "Test123"
                ]
            ]
        );

        $client->getResponse()->getContent();
    }

    public function testRegisterValidationPassError(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' =>             [
                    "email" => "test@test.com",
                    "password" => "012",
                    "name" => "Test123"
                ]
            ]
        );

        $client->getResponse()->getContent();
    }

    public function testRegisterValidationNameError(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionCode(400);

        $client = static::createClient();
        $client->request(
            'POST',
            '/api/register',
            [
                'json' =>             [
                    "email" => "test@test.com",
                    "password" => "0123456789",
                    "name" => "T"
                ]
            ]
        );

        $client->getResponse()->getContent();
    }
}
