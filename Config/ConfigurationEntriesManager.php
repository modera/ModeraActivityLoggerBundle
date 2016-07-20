<?php

namespace Modera\ConfigBundle\Config;

use Doctrine\ORM\EntityManager;
use Modera\ConfigBundle\Manager\ConfigurationEntryAlreadyExistsException;
use Modera\ConfigBundle\Manager\UniquityValidator;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @deprecated Use \Modera\ConfigBundle\Manager\ConfigurationEntriesManager instead
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigurationEntriesManager implements ConfigurationEntriesManagerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $semanticConfig = array();

    /**
     * @var UniquityValidator
     */
    private $uniquityValidator;

    /**
     * @param EntityManager          $em
     * @param array                  $semanticConfig
     * @param UniquityValidator|null $uniquityValidator
     */
    public function __construct(EntityManager $em, array $semanticConfig = array(), UniquityValidator $uniquityValidator = null)
    {
        $this->em = $em;
        $this->semanticConfig = $semanticConfig;
        $this->uniquityValidator = $uniquityValidator;
    }

    /**
     * @param string $name
     * @param object $owner
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByName($name, $owner = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('e')
            ->from(ConfigurationEntry::clazz(), 'e')
            ->andWhere(
                $qb->expr()->eq('e.name', '?1')
            )
            ->setMaxResults(1)
        ;

        $qb->setParameter(1, $name);

        if ($this->isOwnerConfigured()) {
            $qb->andWhere(
                $owner ? $qb->expr()->eq('e.owner', '?2') : $qb->expr()->isNull('e.owner')
            );

            if ($owner) {
                $qb->setParameter(2, $owner);
            }
        }

        $result = $qb->getQuery()->getResult();

        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * @return bool
     */
    private function isOwnerConfigured()
    {
        return isset($this->semanticConfig['owner_entity']) && null !== $this->semanticConfig['owner_entity'];
    }

    /**
     * @throws \RuntimeException When requested configuration property with name $name is not found
     *
     * @param string $name
     * @param object $owner
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByNameOrDie($name, $owner = null)
    {
        $result = $this->findOneByName($name, $owner);
        if (!$result) {
            throw new \RuntimeException(sprintf(
                'Unable to find required configuration property %s', $name
            ));
        }

        return $result;
    }

    /**
     * @param ConfigurationEntryInterface $entry
     */
    public function save(ConfigurationEntryInterface $entry)
    {
        if (!($entry instanceof ConfigurationEntry)) {
            throw new InvalidArgumentException(
                '$entry must be an instance of '.ConfigurationEntry::clazz()
            );
        }

        if ($this->uniquityValidator) {
            if (!$this->uniquityValidator->isValidForSaving($entry)) {
                throw new ConfigurationEntryAlreadyExistsException(
                    sprintf('Configuration property with name "%s" already exists.', $entry->getName())
                );
            }
        }

        $this->em->persist($entry);
        $this->em->flush($entry);
    }

    /**
     * @param object $owner
     *
     * @return ConfigurationEntryInterface[]
     */
    public function findAllExposed($owner = null)
    {
        $qb = $this->em->createQueryBuilder();

        $qb
            ->select('e')
            ->from(ConfigurationEntry::clazz(), 'e')
            ->andWhere(
                $qb->expr()->eq('e.isExposed', '?1')
            )
        ;

        $qb->setParameter(1, true);

        if ($this->isOwnerConfigured()) {
            $qb->andWhere(
                $owner ? $qb->expr()->eq('e.owner', '?2') : $qb->expr()->isNull('e.owner')
            );

            if ($owner) {
                $qb->setParameter(2, $owner);
            }
        }

        return $qb->getQuery()->getResult();
    }
}
