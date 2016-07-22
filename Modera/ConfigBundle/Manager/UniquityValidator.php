<?php

namespace Modera\ConfigBundle\Manager;

use Doctrine\ORM\EntityManager;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * Depending on semantic configuration checks if given configuration property is unique in general or unique
 * for a specific user.
 *
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class UniquityValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $semanticConfig;

    /**
     * @param EntityManager $em
     * @param array         $semanticConfig
     */
    public function __construct(EntityManager $em, array $semanticConfig)
    {
        $this->em = $em;
        $this->semanticConfig = $semanticConfig;
    }

    /**
     * @param ConfigurationEntry $entry
     *
     * @return bool
     */
    public function isValidForSaving(ConfigurationEntry $entry)
    {
        $query = null;
        if ($this->semanticConfig['owner_entity'] && $entry->getOwner()) {
            $query = sprintf('SELECT e.id FROM %s e WHERE e.name = ?0 AND e.owner = ?1', get_class($entry));
            $query = $this->em->createQuery($query);

            $query->setParameters([$entry->getName(), $entry->getOwner()]);
        } else {
            $query = sprintf('SELECT e.id FROM %s e WHERE e.name = ?0', get_class($entry));
            $query = $this->em->createQuery($query);

            $query->setParameter(0, $entry->getName());
        }

        $result = $query->getArrayResult();
        $isNameInUse = count($result) > 0;

        if ($isNameInUse) {
            // if name is already in use then we will allow to save configuration property if it represents
            // the same records as in database
            return $entry->getId() == $result[0]['id'];
        }

        return true;
    }
}
