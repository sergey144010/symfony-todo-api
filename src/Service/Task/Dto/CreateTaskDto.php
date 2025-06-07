<?php

namespace App\Service\Task\Dto;

use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
final class CreateTaskDto implements GroupSequenceProviderInterface
{
    public function __construct(
        #[Assert\Length(min: 10, max: 100, groups: ['title'])]
        readonly public string $title,
        #[Assert\Length(min: 15, groups: ['description'])]
        readonly public ?string $description,
        #[Assert\NotBlank(groups: ['deadline'])]
        #[Assert\Date(groups: ['deadline'])]
        readonly public string $deadline,
    ) {
    }

    /**
     * @return Array<int, Array<int, string>>
     */
    #[Ignore]
    public function getGroupSequence(): array
    {
        $groups = ['Default'];
        $groups[] = 'title';
        $groups[] = 'deadline';

        if (isset($this->description)) {
            $groups[] = 'description';
        }

        return [$groups];
    }
}
