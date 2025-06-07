<?php

namespace App\Service\Registration;

use Symfony\Component\Validator\Constraints as Assert;

final class RegistrationDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 6,
        max: 50,
    )]
    public string $password;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 100,
    )]
    public string $name;
}
