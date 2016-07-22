<?php

namespace Modera\ActivityLoggerBundle\Manager;

use Doctrine\Common\Persistence\ObjectManager;
use Modera\ActivityLoggerBundle\Entity\Activity;
use Psr\Log\AbstractLogger;
use Sli\ExtJsIntegrationBundle\QueryBuilder\ExtjsQueryBuilder;

/**
 * This implementation uses Doctrine's ORM to store activities to database.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DoctrineOrmActivityManager extends AbstractLogger implements ActivityManagerInterface
{
    private $om;
    private $queryBuilder;

    /**
     * @param ObjectManager     $om
     * @param ExtjsQueryBuilder $queryBuilder
     */
    public function __construct(ObjectManager $om, ExtjsQueryBuilder $queryBuilder)
    {
        $this->om = $om;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @return Activity
     */
    protected function createActivity()
    {
        return new Activity();
    }

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = array())
    {
        $activity = $this->createActivity();
        $activity->setLevel($level);
        $activity->setMessage($message);

        if (isset($context['author'])) {
            $activity->setAuthor($context['author']);
        }
        if (isset($context['type'])) {
            $activity->setType($context['type']);
        }
        if (isset($context['meta']) && is_array($context)) {
            $activity->setMeta($context['meta']);
        }

        $this->om->persist($activity);
        $this->om->flush();
    }

    /**
     * @inheritDoc
     */
    public function query(array $query)
    {
        $qb = $this->queryBuilder->buildQueryBuilder(Activity::clazz(), $query);

        return array(
            'items' => $qb->getQuery()->getResult(),
            'total' => $this->queryBuilder->buildCountQueryBuilder($qb)->getQuery()->getSingleScalarResult()
        );
    }
} 