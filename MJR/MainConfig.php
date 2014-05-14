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
    private $mgr;

    /**
     * @param ConfigurationEntriesManagerInterface $mgr
     */
    public function __construct(ConfigurationEntriesManagerInterface $mgr)
    {
        $this->mgr = $mgr;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_TITLE)->getValue();
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_URL)->getValue();
    }

    /**
     * @inheritDoc
     */
    public function getHomeSection()
    {
        return $this->mgr->findOneByNameOrDie(Bundle::CONFIG_HOME_SECTION)->getValue();
    }
} 