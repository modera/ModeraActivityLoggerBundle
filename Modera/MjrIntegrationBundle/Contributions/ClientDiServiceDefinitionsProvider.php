<?php

namespace Modera\MjrIntegrationBundle\Contributions;

use Modera\MjrIntegrationBundle\Config\MainConfigInterface;
use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @internal
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class ClientDiServiceDefinitionsProvider implements ContributorInterface
{
    /**
     * @var MainConfigInterface
     */
    private $config;

    /**
     * @param MainConfigInterface $config
     */
    public function __construct(MainConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return array(
            'page_title_monitoring_plugin' => array(
                'className' => 'Modera.mjrintegration.runtime.titlehandling.MonitoringPlugin',
                'args' => [
                    array(
                        'pageTitleMgr' => '@page_title_mgr',
                    ),
                ],
                'tags' => ['runtime_plugin'],
            ),
            'page_title_mgr' => array(
                'className' => 'Modera.mjrintegration.runtime.titlehandling.PageTitleManager',
                'args' => [
                    array(
                        'application' => '@application',
                        'titlePattern' => $this->config->getTitle(),
                    ),
                ],
            ),
        );
    }
}
