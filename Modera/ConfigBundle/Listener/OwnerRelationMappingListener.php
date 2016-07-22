<?php

namespace Modera\ConfigBundle\Listener;

use Doctrine\Common\Persistence\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @internal
 *
 * @author Sergei Lissovski <sergei.lissovski@gmail.org>
 */
class OwnerRelationMappingListener
{
    /**
     * @var array
     */
    private $semanticConfig = array();

    /**
     * @param array $semanticConfig
     */
    public function __construct(array $semanticConfig)
    {
        $this->semanticConfig = $semanticConfig;
    }

    /**
     * @param LoadClassMetadataEventArgs $args
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $args)
    {
        /* @var ClassMetadataInfo $mapping */
        $mapping = $args->getClassMetadata();

        if ($mapping->getName() == ConfigurationEntry::clazz()) {
            $mapping->mapManyToOne(array(
                'fieldName' => 'owner',
                'type' => ClassMetadataInfo::MANY_TO_ONE,
                'isOwningSide' => true,
                'targetEntity' => $this->semanticConfig['owner_entity'],
            ));
        }
    }
}
