<?php

namespace Modera\BackendLanguagesBundle\Contributions;

use Doctrine\ORM\EntityManager;
use Modera\LanguagesBundle\Entity\Language;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Modera\MjrIntegrationBundle\Config\ConfigMergerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface, ConfigMergerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param array $currentConfig
     *
     * @return array
     */
    public function merge(array $currentConfig)
    {
        $languages = array();
        $dbLanguages = $this->em->getRepository(Language::clazz())->findBy(array('isEnabled' => true));
        foreach ($dbLanguages as $dbLanguage) {
            $languages[] = array(
                'id' => $dbLanguage->getId(),
                'name' => $dbLanguage->getName(),
            );
        }

        return array_merge($currentConfig, array(
            'modera_backend_languages' => array(
                'languages' => $languages,
            ),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array($this);
    }
}
