<?php

namespace Modera\TranslationsBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Modera\TranslationsBundle\Entity\LanguageTranslationToken;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageTranslationTokenListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->updateTranslationToken($args);
    }
    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->updateTranslationToken($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function updateTranslationToken(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $entity = $args->getEntity();

        if ($entity instanceof LanguageTranslationToken) {
            $translationToken = $entity->getTranslationToken();
            $translations = $translationToken->getTranslations();
            $translations[$entity->getLanguage()->getId()] = array(
                'id'          => $entity->getId(),
                'isNew'       => $entity->isNew(),
                'translation' => $entity->getTranslation(),
                'locale'      => $entity->getLanguage()->getLocale(),
                'language'    => $entity->getLanguage()->getName(),
            );
            $translationToken->setTranslations($translations);
            $em->persist($translationToken);
            $em->flush();
        }
    }
} 