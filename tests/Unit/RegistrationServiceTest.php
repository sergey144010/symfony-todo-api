<?php

namespace App\Tests\Unit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\Registration\RegistrationDto;
use App\Service\Registration\RegistrationService;

class RegistrationServiceTest extends ApiTestCase
{
    public function testRegistration(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        /** @var RegistrationService $service */
        $service = $container->get(RegistrationService::class);

        $dto = new RegistrationDto();
        $dto->email = 'test@test1.com';
        $dto->password = '0123456789';
        $dto->name = 'TestName';

        $service->registration($dto);

        /** @var UserRepository $repository */
        $repository = $container->get(UserRepository::class);
        $user = $repository->findOneBy(['email' => $dto->email]);

        self::assertInstanceOf(User::class, $user);
        self::assertEquals($dto->email, $user->getEmail());
        self::assertEquals($dto->name, $user->getFirstName());
    }
}
