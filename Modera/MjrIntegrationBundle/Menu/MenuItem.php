<?php

namespace Modera\MjrIntegrationBundle\Menu;

use Modera\MjrIntegrationBundle\Sections\Section;

/**
 * Default immutable implementation.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItem extends Section
{
    private $glyph;
    private $label;

    /**
     * @param string $label
     * @param string $controller
     * @param string $id
     * @param array  $metadata
     * @param string $glyph
     */
    public function __construct($label, $controller, $id, array $metadata = array(), $glyph = null)
    {
        $this->glyph = $glyph;
        $this->label = $label;

        parent::__construct($id, $controller, $metadata);
    }

    /**
     * @return string
     */
    public function getGlyph()
    {
        return $this->glyph;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
