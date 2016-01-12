<?php

namespace Modera\ConfigBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Modera\ConfigBundle\Config\ConfigurationEntryDefinition;
use Modera\ConfigBundle\Config\ValueUpdatedHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Modera\ConfigBundle\Config\HandlerInterface;
use Modera\ConfigBundle\Config\ConfigurationEntryInterface;

/**
 * Do no rely on methods exposed by this class outside this bundle, instead use methods declared by
 * {@class ConfigurationEntryInterface}.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
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
    const TYPE_BOOL = 5;

    private static $fieldsMapping = array(
        self::TYPE_INT => 'int',
        self::TYPE_STRING => 'string',
        self::TYPE_TEXT => 'text',
        self::TYPE_ARRAY => 'array',
        self::TYPE_FLOAT => 'float',
        self::TYPE_BOOL => 'bool',
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
     * User understandable name for this configuration-entry.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $readableName;

    /**
     * Optional name of category this configuration property should belong to.
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $category;

    /**
     * Optional configuration that will be used to configure implementation of
     * {@class \Modera\ConfigBundle\Config\HandlerInterface}.
     *
     * Available configuration properties:
     *
     *  * update_handler  -- DI service ID that implements {@class ValueUpdatedHandlerInterface} that must be invoked
     *                       when configuration entry is updated
     * * handler -- DI service ID of a class that implements {@class \Modera\ConfigBundle\Config\HandlerInterface}
     *
     * @ORM\Column(type="array")
     */
    private $serverHandlerConfig = array();

    /**
     * Optional configuration that will be used on client-side ( frontend ) to configure editor for this configuration
     * entry.
     *
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
     * @ORM\Column(type="decimal", precision=20, scale=4, nullable=true)
     */
    private $floatValue;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $boolValue;

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
     *
     * @return ConfigurationEntry
     */
    public static function createFromDefinition(ConfigurationEntryDefinition $def)
    {
        $me = new self($def->getName());
        $me->setReadableName($def->getReadableName());
        $me->setValue($def->getValue());
        $me->setServerHandlerConfig($def->getServerHandlerConfig());
        $me->setClientHandlerConfig($def->getClientHandlerConfig());
        $me->setExposed($def->isExposed());
        $me->setCategory($def->getCategory());

        return $me;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    public static function clazz()
    {
        return get_called_class();
    }

    public function setExposed($isExposed)
    {
        $this->isExposed = $isExposed;
    }

    /**
     * {@inheritdoc}
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
     * @private
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateUpdatedAt()
    {
        if (null !== $this->id) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function invokeUpdateHandler()
    {
        if (isset($this->serverHandlerConfig['update_handler'])) {
            /* @var ValueUpdatedHandlerInterface $updateHandler */
            $updateHandler = $this->getContainer()->get($this->serverHandlerConfig['update_handler']);
            $updateHandler->onUpdate($this);
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
        return isset($this->serverHandlerConfig['handler']);
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
        } elseif (null === $this->getContainer()) {
            throw new \RuntimeException(sprintf(
                'Configuration property "%s" is not initialized yet, use init() method.', $this->getName()
            ));
        }

        if (!isset($this->serverHandlerConfig['handler'])) {
            throw new \RuntimeException(sprintf(
                "Configuration property '%s' doesn't have handler configured!", $this->getName()
            ));
        }

        $handlerServiceId = $this->serverHandlerConfig['handler'];

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
     * {@inheritdoc}
     */
    public function setDenormalizedValue($value)
    {
        $this->{$this->getStorageFieldNameFromValue($value)} = $value;

        return $this->savedAs = $this->getFieldType($value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDenormalizedValue()
    {
        if (!isset(self::$fieldsMapping[$this->getSavedAs()])) {
            throw new \RuntimeException(sprintf(
                'Unable to resolve storage type "%s" for configuration-entry "%s"',
                $this->getSavedAs(), $this->getName()
            ));
        }

        $fieldName = self::$fieldsMapping[$this->getSavedAs()].'Value';

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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getReadableValue()
    {
        if ($this->hasServerHandler()) {
            return $this->getHandler()->getReadableValue($this);
        } else {
            return $this->getDenormalizedValue();
        }
    }

    /**
     * Resets value of this configuration entry.
     */
    public function reset()
    {
        foreach (self::$fieldsMapping as $type => $name) {
            $this->{$name.'Value'} = null;
        }
        $this->arrayValue = array();
    }

    /**
     * @throws \RuntimeException
     *
     * @param mixed $value
     *
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
        } elseif (is_float($value)) {
            return self::TYPE_FLOAT;
        } elseif (is_int($value)) {
            return self::TYPE_INT;
        } elseif (is_array($value)) {
            return self::TYPE_ARRAY;
        } elseif (is_bool($value)) {
            return self::TYPE_BOOL;
        }

        throw new \RuntimeException(sprintf(
            'Unable to guess type of provided value! ( %s )', $this->getName()
        ));
    }

    /**
     * @param mixed $value
     *
     * @return string
     */
    private function getStorageFieldNameFromValue($value)
    {
        return self::$fieldsMapping[$this->getFieldType($value)].'Value';
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

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getCategory()
    {
        return $this->category;
    }
}
