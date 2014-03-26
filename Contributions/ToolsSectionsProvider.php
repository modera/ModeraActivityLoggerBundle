<?php

namespace Modera\BackendModuleBundle\Contributions;

use Modera\BackendModuleBundle\ModeraBackendModuleBundle;
use Modera\BackendToolsBundle\Section\Section;
use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Contributes a section to Backend/Tools
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class ToolsSectionsProvider implements ContributorInterface
{
    private $securityContext;

    private $items;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @inheritDoc
     */
    public function getItems()
    {
        if (!$this->items) {
            $this->items = [];

            if ($this->securityContext->isGranted(ModeraBackendModuleBundle::ROLE_ACCESS_BACKEND_TOOLS_MODULES_SECTION)) {
                $this->items[] = new Section(
                    'Modules',
                    'tools.modules',
                    'Modules management.',
                    '', '',
                    'modera-backend-module-tools-icon'
                );
            }
        }

        return $this->items;
    }
}