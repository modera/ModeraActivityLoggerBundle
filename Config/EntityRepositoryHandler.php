<?php

namespace Modera\ConfigBundle\Config;

use Modera\ConfigBundle\Entity\ConfigurationEntry;
use Doctrine\ORM\EntityManager;

/**
 * Allows to store/retrieve entities for {@class ConfigurationEntry}. In order this class to work,
 * instance of {@class ConfigurationEntry} must have two keys defined in its "serverHandlerConfig":
 * - entityFqcn : Fully qualified class name of an entity this handler will be working with
 * - toStringMethodName : A method name of the specified entity class that will be used
 *                        to get a string representation of it ( used in (#getReadableValue()) method )
 * - clientValueMethodName : Default value is 'getId', a method name to use to get a value
 *                           that will be stored in {@class ConfigurationEntry}.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class EntityRepositoryHandler implements HandlerInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function getEntityFqcn(ConfigurationEntry $entry)
    {
        $cfg = $entry->getServerHandlerConfig();
        if (!isset($cfg['entityFqcn'])) {
            throw MissingConfigurationParameterException::create($entry, 'entityFqcn');
        }

        return $cfg['entityFqcn'];
    }

    /**
     * {@inheritdoc}
     */
    public function getReadableValue(ConfigurationEntry $entry)
    {
        $cfg = $entry->getServerHandlerConfig();
        if (!isset($cfg['toStringMethodName'])) {
            throw MissingConfigurationParameterException::create($entry, 'toStringMethodName');
        }
        $entity = $this->em->find($this->getEntityFqcn($entry), $entry->getDenormalizedValue());
        if (!$entity) {
            throw new \RuntimeException(sprintf(
                'Unable to find entity "%s" with id "%s"',
                $this->getEntityFqcn($entry), $entry->getDenormalizedValue()
            ));
        }

        return $entity->{$cfg['toStringMethodName']}();
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(ConfigurationEntry $entry)
    {
        return $this->em->find($this->getEntityFqcn($entry), $entry->getDenormalizedValue());
    }

    /**
     * {@inheritdoc}
     */
    public function convertToStorageValue($object, ConfigurationEntry $entry)
    {
        if (!is_a($object, $this->getEntityFqcn($entry))) {
            throw new \RuntimeException(sprintf(
                "Only instances of '%s' class can be persisted for configuration property '%s'.",
                $this->getEntityFqcn($entry), $entry->getName()
            ));
        }

        $cfg = $entry->getServerHandlerConfig();
        $methodName = isset($cfg['clientValueMethodName']) ? $cfg['clientValueMethodName'] : 'getId';
        if (!in_array($methodName, get_class_methods(get_class($object)))) {
            throw new \RuntimeException(sprintf("%s must have $methodName() method!", get_class($object)));
        }

        return $object->$methodName();
    }
}
