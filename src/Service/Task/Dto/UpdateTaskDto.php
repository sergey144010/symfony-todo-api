<?php

namespace App\Service\Task\Dto;

use App\Entity\Task;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
class UpdateTaskDto implements GroupSequenceProviderInterface
{
    public function __construct(
        #[Assert\Length(min: 10, max: 100, groups: ['title'])]
        readonly public ?string $title = null,
        #[Assert\Length(min: 15, groups: ['description'])]
        readonly public ?string $description = null,
        #[Assert\NotBlank(groups: ['deadline'])]
        #[Assert\Date(groups: ['deadline'])]
        readonly public ?string $deadline = null,
        #[Assert\Choice([Task::STATUS_IN_PROGRESS, Task::STATUS_DONE], groups: ['status'])]
        readonly public ?int $status = null,
    ) {
    }

    /**
     * @return Array<int, Array<int, string>>
     */
    #[Ignore]
    public function getGroupSequence(): array
    {
        $groups = ['Default'];

        if (isset($this->title)) {
            $groups[] = 'title';
        }

        if (isset($this->description)) {
            $groups[] = 'description';
        }

        if (isset($this->deadline)) {
            $groups[] = 'deadline';
        }

        if (isset($this->status)) {
            $groups[] = 'status';
        }

        return [$groups];
    }
}
