<?php

namespace Modera\ConfigBundle\Twig;

use Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface;

/**
 * You may rely on functions exposed by this class but the class itself may be moved or renamed later.
 *
 * @private
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class TwigExtension extends \Twig_Extension
{
    /**
     * @var \Modera\ConfigBundle\Manager\ConfigurationEntriesManagerInterface
     */
    private $configEntriesManager;

    /**
     * @param ConfigurationEntriesManagerInterface $configEntriesManager
     */
    public function __construct(ConfigurationEntriesManagerInterface $configEntriesManager)
    {
        $this->configEntriesManager = $configEntriesManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'modera_config';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('modera_config_value', array($this, 'twigModeraConfigValue')),
            new \Twig_SimpleFunction('modera_config_owner_value', array($this, 'getModeraConfigOwnerValue')),
        ];
    }

    /**
     * Gets values of a configuration property.
     *
     * @private
     *
     * @param string $propertyName "name" of ConfigurationEntry.
     * @param bool   $strict       If FALSE is given and property is not found then no exception will be thrown
     *
     * @return mixed|null
     */
    public function twigModeraConfigValue($propertyName, $strict = true)
    {
        return $this->getModeraConfigOwnerValue($propertyName, null, $strict);
    }

    /**
     * @private
     *
     * @param string $propertyName
     * @param object $owner
     * @param bool   $strict
     *
     * @return mixed|null
     */
    public function getModeraConfigOwnerValue($propertyName, $owner = null, $strict = true)
    {
        $mgr = $this->configEntriesManager;

        if ($strict) {
            return $mgr->findOneByNameOrDie($propertyName, $owner)->getValue();
        } else {
            $property = $mgr->findOneByName($propertyName, $owner);

            return $property ? $property->getValue() : null;
        }
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
