<?php

namespace Modera\DynamicallyConfigurableMJRBundle\MJR;

use Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface;
use Modera\MjrIntegrationBundle\Config\MainConfigInterface;
use Modera\DynamicallyConfigurableMJRBundle\ModeraDynamicallyConfigurableMJRBundle as Bundle;

/**
 * This implementation read configuration properties stored in central settings storage provided by
 * {@class \Modera\ConfigBundle\ModeraConfigBundle}.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MainConfig implements MainConfigInterface
{
    /**
     * @var ConfigurationEntriesManagerInterface
     */
    private $mgr;

    /**
     * @param ConfigurationEntriesManagerInterface $mgr
     */
    public function __construct(ConfigurationEntriesManagerInterface $mgr)
    {
        $this->mgr = $mgr;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_TITLE)->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_URL)->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function getHomeSection()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_HOME_SECTION)->getValue();
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
