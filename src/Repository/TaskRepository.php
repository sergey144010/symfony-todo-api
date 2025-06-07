<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use App\Serializer\DateTimeCallback;
use App\Service\Task\Dto\TaskListFilterDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByTaskFilter(User $user, TaskListFilterDto $filter): array
    {
        $builder = $this->createQueryBuilder('t')
            ->andWhere('t.userEntity = :user')
            ->setParameter('user', $user);

        if (isset($filter->status)) {
            $builder
                ->andWhere('t.status = :status')
                ->setParameter('status', $filter->status);
        }
        if (isset($filter->deadline)) {
            $dateFrom = \DateTimeImmutable::createFromFormat(DateTimeCallback::FORMAT_DAY, $filter->deadline)
                ->setTime(0, 0, 0);
            $dateTo = $dateFrom->add(new \DateInterval('P1D'));

            $builder
                ->andWhere('t.deadline BETWEEN :from AND :to')
                -> setParameter('from', $dateFrom)
                -> setParameter('to', $dateTo);
        }

        return $builder->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneById(User $user, int $id): Task|null
    {
        $builder = $this->createQueryBuilder('t')
            ->andWhere('t.userEntity = :user')
            ->setParameter('user', $user);

        $builder
            ->andWhere('t.id = :id')
            ->setParameter('id', $id);

        $task = $builder->getQuery()->getOneOrNullResult();

        if (!$task instanceof Task) {
            return null;
        }

        return $task;
    }
}
