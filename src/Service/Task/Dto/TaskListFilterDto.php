<?php

namespace App\Service\Task\Dto;

use App\Entity\Task;
use Nelmio\ApiDocBundle\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
class TaskListFilterDto implements GroupSequenceProviderInterface
{
    public function __construct(
        #[Assert\NotBlank(groups: ['status'])]
        #[Assert\Choice([Task::STATUS_NEW, Task::STATUS_IN_PROGRESS, Task::STATUS_DONE], groups: ['status'])]
        public ?int $status,
        #[Assert\NotBlank(groups: ['deadline'])]
        #[Assert\Date(groups: ['deadline'])]
        public ?string $deadline,
    ) {
    }

    /**
     * @return Array<int, Array<int, string>>
     */
    #[Ignore]
    public function getGroupSequence(): array
    {
        $groups = ['Default'];

        if (isset($this->status)) {
            $groups[] = 'status';
        }

        if (isset($this->deadline)) {
            $groups[] = 'deadline';
        }

        return [$groups];
    }
}
