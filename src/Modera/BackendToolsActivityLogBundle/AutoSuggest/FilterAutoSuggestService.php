<?php

namespace Modera\BackendToolsActivityLogBundle\AutoSuggest;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Modera\ActivityLoggerBundle\Manager\ActivityManagerInterface;
use Modera\ActivityLoggerBundle\Model\ActivityInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\FoundationBundle\Translation\T;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FilterAutoSuggestService
{
    private $em;
    private $activityManager;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, ActivityManagerInterface $activityManager)
    {
        $this->em = $em;
        $this->activityManager = $activityManager;
    }

    protected function stringifyUser(User $user)
    {
        return $user->getFullName()
              ? sprintf('%s (%s)', $user->getFullName(), $user->getUsername())
              : $user->getUsername();
    }

    /**
     * @param string $queryType
     * @param string $query
     * @return array[]
     */
    public function suggest($queryType, $query)
    {
        if ('user' == $queryType) {
            $dql = $this->em->createQuery(sprintf(
                'SELECT u FROM %s u WHERE u.firstName LIKE ?0 OR u.lastName LIKE ?0 OR u.username LIKE ?0 OR u.email LIKE ?0',
                User::clazz()
            ));
            $dql->setParameter(0, '%' . $query . '%');

            $rawResult = [];
            foreach ($dql->getResult() as $user) {
                /* @var User $user */

                $value = $this->stringifyUser($user);

                $rawResult[] = array(
                    'id' => $user->getId(),
                    'value' => $value,
                );
            }

            return $rawResult;
        } else if ('exact-user' == $queryType) { // find by ID
            $user = $this->em->find(User::clazz(), $query);

            if (!$user) {
                throw new \DomainException(T::trans('Unable to find a user "%username%"', array('%username%' => $query)));
            }

            return [
                array(
                    'id' => $user->getId(),
                    'value' => $this->stringifyUser($user)
                )
            ];
        } else if ('eventType' == $queryType) {
            $activities = $this->activityManager->query(array(
                'filter' => [
                    array('property' => 'type', 'value' => 'like:%' . $query . '%')
                ]
            ));

            $rawResult = [];
            foreach ($activities['items'] as $activity) {
                /* @var ActivityInterface $activity */
                $rawResult[] = $activity->getType();
            }

            $rawResult = array_values(array_unique($rawResult));

            $result = [];
            foreach ($rawResult as $item) {
                $result[] = array(
                    'id' => $item,
                    'value' => $item
                );
            }

            return $result;
        }
    }

    static public function clazz()
    {
        return get_called_class();
    }
}