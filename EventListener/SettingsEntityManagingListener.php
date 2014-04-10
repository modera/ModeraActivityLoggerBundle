<?php

namespace Modera\BackendLanguagesBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Modera\SecurityBundle\Entity\User;
use Modera\BackendLanguagesBundle\Entity\UserSettings;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SettingsEntityManagingListener
{
    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $userSettings = new UserSettings();
                $userSettings->setUser($entity);

                $em->persist($userSettings);
                $uow->computeChangeSet($em->getClassMetadata(UserSettings::clazz()), $userSettings);
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            if ($entity instanceof User) {
                $query = $em->createQuery(sprintf('DELETE FROM %s us WHERE us.user = ?0', UserSettings::clazz()));
                $query->execute(array($entity));
            }
        }
    }
} 