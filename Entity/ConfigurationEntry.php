<?php

namespace Modera\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Modera\ConfigBundle\Config\ConfigurationEntryDefinition;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Modera\ConfigBundle\Config\HandlerInterface;
use Modera\ConfigBundle\Config\ConfigurationEntryInterface;

/**
 * Do no rely on methods exposed by this class outside this bundle, instead use methods declared by
 * {@class ConfigurationEntryInterface}.
 *
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 *
 * @ORM\Entity
 * @ORM\Table(name="modera_config_configurationproperty")
 * @ORM\HasLifecycleCallbacks
 */
class ConfigurationEntry implements ConfigurationEntryInterface
{
    const TYPE_STRING = 0;
    const TYPE_TEXT = 1;
    const TYPE_INT = 2;
    const TYPE_FLOAT = 3;
    const TYPE_ARRAY = 4;

    static private $fieldsMapping = array(
        self::TYPE_INT => 'int',
        self::TYPE_STRING => 'string',
        self::TYPE_TEXT => 'text',
        self::TYPE_ARRAY => 'array',
        self::TYPE_FLOAT => 'float'
    );

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Technical name that you will use in your code to reference this configuration entry.
     *
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * User understandable name for configuration-entry.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $readableName;

    /**
     * @ORM\Column(type="array")
     */
    private $serverHandlerConfig = array();

    /**
     * @ORM\Column(type="array")
     */
    private $clientHandlerConfig = array();

    /**
     * @ORM\Column(type="string")
     */
    private $savedAs;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $stringValue;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textValue;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $intValue;

    /**
     * @ORM\Column(type="decimal", columnDefinition="DECIMAL(20,4)")
     */
    private $floatValue;

    /**
     * @ORM\Column(type="array")
     */
    private $arrayValue = array();

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Only those configuration properties will be shown in UI which have this property set to TRUE.
     *
     * @ORM\Column(type="boolean")
     */
    private $isExposed = true;

    /**
     * We won't allow to edit configuration properties whose isReadOnly field is set to FALSE.
     *
     * @ORM\Column(type="boolean")
     */
    private $isReadOnly = false;

    /**
     * @param string $name
     * @param string $serverHandlerServiceId
     */
    public function __construct($name)
    {
        $this->setName($name);
    }

    /**
     * @param ConfigurationEntryDefinition $def
     * @return ConfigurationEntry
     */
    static public function createFromDefinition(ConfigurationEntryDefinition $def)
    {
        $me = new self($def->getName());
        $me->setReadableName($def->getReadableName());
        $me->setValue($def->getValue());
        $me->setServerHandlerConfig($def->getServerHandlerConfig());
        $me->setClientHandlerConfig($def->getClientHandlerConfig());
        $me->setExposed($def->isExposed());

        return $me;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    static public function clazz()
    {
        return get_called_class();
    }

    public function setExposed($isExposed)
    {
        $this->isExposed = $isExposed;
    }

    /**
     * @inheritDoc
     */
    public function isExposed()
    {
        return $this->isExposed;
    }

    public function setReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;
    }

    public function isReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * @param ContainerInterface $container
     */
    public function init(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    private function updateUpdatedAt()
    {
        if (null !== $this->id) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function validate()
    {
        if (null === $this->getSavedAs()) {
            throw new \DomainException(sprintf(
                'ConfigurationProperty "%s" is not fully configured ( did you set a value for it ? )', $this->getName()
            ));
        }
    }

    /**
     * @return bool
     */
    private function hasServerHandler()
    {
        return count($this->serverHandlerConfig) > 0;
    }

    /**
     * @return \Modera\ConfigBundle\Config\HandlerInterface
     */
    public function getHandler()
    {
        if (!$this->hasServerHandler()) {
            throw new \RuntimeException(sprintf(
                'Configuration-entry "%s" is not configured to use handlers, serverHandlerServiceId has not been specified!',
                $this->getName()
            ));
        } else if (null === $this->getContainer()) {
            throw new \RuntimeException(sprintf(
                'Configuration property "%s" is not initialized yet, use init() method.', $this->getName()
            ));
        }

        if (!isset($this->serverHandlerConfig['id'])) {
            throw new \RuntimeException();
        }

        $handlerServiceId = $this->serverHandlerConfig['id'];

        $handler = $this->getContainer()->get($handlerServiceId);
        if (!($handler instanceof HandlerInterface)) {
            throw new \RuntimeException(sprintf(
                "Handler '%s' doesn't implement HandlerInterface! ( configuration-entry: %s )",
                $handlerServiceId, $this->getName()
            ));
        }

        return $handler;
    }

    /**
     * @inheritDoc
     */
    public function setDenormalizedValue($value)
    {
        $this->{$this->getStorageFieldNameFromValue($value)} = $value;

        return $this->savedAs = $this->getFieldType($value);
    }

    /**
     * @inheritDoc
     */
    public function getDenormalizedValue()
    {
        if (!isset(self::$fieldsMapping[$this->getSavedAs()])) {
            throw new \RuntimeException(sprintf(
                'Unable to resolve storage type "%s" for configuration-entry "%s"',
                $this->getSavedAs(), $this->getName()
            ));
        }

        $fieldName = self::$fieldsMapping[$this->getSavedAs()] . 'Value';

        // doctrine hydrates decimal from database as strings
        // to avoid returning non identical value that was initially
        // saved we will manually cast it to float
        $result = $this->$fieldName;
        if ($this->getSavedAs() == self::TYPE_FLOAT) {
            $result = floatval($result);
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setValue($value)
    {
        $this->reset();

        if ($this->hasServerHandler()) {
            $value = $this->getHandler()->convertToStorageValue($value, $this);
        }

        return $this->setDenormalizedValue($value);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->hasServerHandler()) {
            return $this->getHandler()->getValue($this);
        } else {
            return $this->getDenormalizedValue();
        }
    }

    /**
     * @inheritDoc
     */
    public function getReadableValue()
    {
        if ($this->hasServerHandler()) {
            return $this->getHandler()->getReadableValue($this);
        } else {
            return $this->getDenormalizedValue();
        }
    }

    public function reset()
    {
        foreach (self::$fieldsMapping as $type=>$name) {
            $this->{$name.'Value'} = null;
        }
        $this->arrayValue = array();
    }

    /**
     * @throws \RuntimeException
     * @param mixed $value
     * @return int
     */
    public function getFieldType($value)
    {
        if (is_string($value)) {
            if (mb_strlen($value) <= 254) {
                return self::TYPE_STRING;
            } else {
                return self::TYPE_TEXT;
            }
        } else if (is_float($value) || is_double($value)) {
            return self::TYPE_FLOAT;
        } else if (is_int($value)) {
            return self::TYPE_INT;
        } else if (is_array($value)) {
            return self::TYPE_ARRAY;
        }

        throw new \RuntimeException('Unable to guess type of provided value!');
    }

    /**
     * @return string
     */
    private function getStorageFieldNameFromValue($value)
    {
        return self::$fieldsMapping[$this->getFieldType($value)] . 'Value';
    }

    // boilerplate:

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getSavedAs()
    {
        return $this->savedAs;
    }

    public function setServerHandlerConfig(array $serverHandlerConfig)
    {
        $this->serverHandlerConfig = $serverHandlerConfig;
    }

    public function getServerHandlerConfig()
    {
        return $this->serverHandlerConfig;
    }

    public function setReadableName($readableName)
    {
        $this->readableName = $readableName;
    }

    public function getReadableName()
    {
        return $this->readableName;
    }

    public function setClientHandlerConfig($clientConfiguratorConfig)
    {
        $this->clientHandlerConfig = $clientConfiguratorConfig;
    }

    public function getClientHandlerConfig()
    {
        return $this->clientHandlerConfig;
    }
}
