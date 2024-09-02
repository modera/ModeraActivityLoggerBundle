<?php

namespace Modera\ActivityLoggerBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Modera\ActivityLoggerBundle\Entity\Activity;
use Modera\ServerCrudBundle\QueryBuilder\ArrayQueryBuilder;
use Psr\Log\AbstractLogger;

/**
 * This implementation uses Doctrine's ORM to store activities to database.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DoctrineOrmActivityManager extends AbstractLogger implements ActivityManagerInterface
{
    private EntityManagerInterface $om;
    private ArrayQueryBuilder $queryBuilder;

    public function __construct(EntityManagerInterface $om, ArrayQueryBuilder $queryBuilder)
    {
        $this->om = $om;
        $this->queryBuilder = $queryBuilder;
    }

    protected function createActivity(): Activity
    {
        return new Activity();
    }

    public function log($level, $message, array $context = []): void
    {
        $activity = $this->createActivity();
        $activity->setMessage($message);

        if (\is_string($level)) {
            $activity->setLevel($level);
        }

        if (isset($context['author']) && \is_string($context['author'])) {
            $activity->setAuthor($context['author']);
        }

        if (isset($context['type']) && \is_string($context['type'])) {
            $activity->setType($context['type']);
        }

        if (isset($context['meta']) && \is_array($context)) {
            $activity->setMeta($context['meta']);
        }

        $this->om->persist($activity);
        $this->om->flush($activity);
    }

    public function query(array $query): array
    {
        $qb = $this->queryBuilder->buildQueryBuilder(Activity::class, $query);

        /** @var int $total */
        $total = $this->queryBuilder->buildCountQueryBuilder($qb)->getQuery()->getSingleScalarResult();
        if ($total > 0) {
            /** @var Activity[] $items */
            $items = $qb->getQuery()->getResult();

            return [
                'items' => $items,
                'total' => $total,
            ];
        }

        return [
            'items' => [],
            'total' => 0,
        ];
    }
}
