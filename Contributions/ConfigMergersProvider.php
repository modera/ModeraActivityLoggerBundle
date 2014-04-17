<?php

namespace Modera\BackendToolsSettingsBundle\Contributions;

use Sli\ExpanderBundle\Ext\ContributorInterface;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergersProvider implements ContributorInterface
{
    private $merger;

    /**
     * @param SectionsConfigMerger $merger
     */
    public function __construct(SectionsConfigMerger $merger)
    {
        $this->merger = $merger;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        return array(
            $this->merger
        );
    }
}