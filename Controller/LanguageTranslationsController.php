<?php

namespace Modera\BackendTranslationsToolBundle\Controller;

use Modera\TranslationsBundle\Entity\LanguageTranslationToken;
use Modera\ServerCrudBundle\Controller\AbstractCrudController;
use Modera\ServerCrudBundle\DataMapping\DataMapperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageTranslationsController extends AbstractCrudController
{
    /**
     * @return array
     */
    public function getConfig()
    {
        return array(
            'entity' => LanguageTranslationToken::clazz(),
            'hydration' => array(
                'groups' => array(
                    'main-form' => function (LanguageTranslationToken $ltt) {
                            return array(
                                'id' => $ltt->getId(),
                                'translation' => $ltt->getTranslation(),
                                'languageName' => $ltt->getLanguage()->getName(),
                                'bundleName' => $ltt->getTranslationToken()->getBundleName(),
                                'tokenName' => $ltt->getTranslationToken()->getTokenName(),
                            );
                        },
                ),
                'profiles' => array(
                    'main-form',
                ),
            ),
            'map_data_on_update' => function (array $params, LanguageTranslationToken $entity, DataMapperInterface $defaultMapper, ContainerInterface $container) {
                $defaultMapper->mapData($params, $entity);

                $key = 'modera_backend_translations_tool';
                /* @var \Doctrine\Common\Cache\Cache $cache */
                $cache = $container->get($key.'.cache');

                $data = array('isCompileNeeded' => true);
                if ($string = $cache->fetch($key)) {
                    $data = array_merge(unserialize($string), $data);
                }
                $cache->save($key, serialize($data));
            },
        );
    }
}
