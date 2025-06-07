<?php

namespace App\Service\Registration;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationService
{
    public function __construct(
        readonly private EntityManagerInterface $entityManager,
        readonly private UserPasswordHasherInterface $passwordHasher,
        readonly private UserRepository $userRepository,
    ) {
    }

    public function registration(RegistrationDto $dto): User
    {
        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if ($user instanceof User) {
            throw new RegistrationServiceException('Email already registered');
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $dto->password
            )
        );
        $user->setFirstName($dto->name);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
