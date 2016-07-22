<?php

namespace Modera\BackendToolsActivityLogBundle\AuthorResolving;

use Doctrine\Common\Persistence\ObjectManager;
use Modera\ActivityLoggerBundle\Model\ActivityInterface;
use Modera\SecurityBundle\Entity\User;
use Modera\FoundationBundle\Translation\T;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ActivityAuthorResolver
{
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * @param ActivityInterface $activity
     * @return array
     */
    public function resolve(ActivityInterface $activity)
    {
        $isId = preg_match('/^[0-9]+$/', $activity->getAuthor());

        if ($isId) {
            /* @var User $user */
            $user = $this->om->find(User::clazz(), $activity->getAuthor());
            if ($user) {
                return array(
                    'id' => $user->getId(),
                    'isUser' => true,
                    'fullName' => $user->getFullName(),
                    'username' => $user->getUsername()
                );
            } else {
                return array(
                    'isUser' => false,
                    'identity' => $activity->getAuthor()
                );
            }
        } else {
            return array(
                'isUser' => false,
                'identity' => $activity->getAuthor() ? $activity->getAuthor() : T::trans('Unknown')
            );
        }
    }
} 