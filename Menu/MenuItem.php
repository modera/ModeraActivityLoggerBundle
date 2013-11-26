<?php

namespace Modera\JSRuntimeIntegrationBundle\Menu;

use Modera\JSRuntimeIntegrationBundle\Sections\Section;

/**
 * Default immutable implementation.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItem extends Section
{
    private $label;

    /**
     * @param string $label
     * @param string $controller
     * @param string $id
     * @param array  $metadata
     */
    public function __construct($label, $controller, $id, array $metadata = array())
    {
        $this->label = $label;

        parent::__construct($id, $controller, $metadata);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}