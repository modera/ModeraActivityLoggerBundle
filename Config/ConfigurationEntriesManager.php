<?php

namespace Modera\ConfigBundle\Config;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Modera\ConfigBundle\Entity\ConfigurationEntry;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigurationEntriesManager implements ConfigurationEntriesManagerInterface
{
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $name
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByName($name)
    {
        return $this->em->getRepository(ConfigurationEntry::clazz())->findOneBy(array(
            'name' => $name,
        ));
    }

    /**
     * @throws \RuntimeException When requested configuration property with name $name is not found
     *
     * @param string $name
     *
     * @return ConfigurationEntryInterface
     */
    public function findOneByNameOrDie($name)
    {
        $result = $this->findOneByName($name);
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

        $this->em->persist($entry);
        $this->em->flush($entry);
    }

    /**
     * @return ConfigurationEntryInterface[]
     */
    public function findAllExposed()
    {
        return $this->em->getRepository(ConfigurationEntry::clazz())->findBy(array(
            'isExposed' => true,
        ));
    }
}
