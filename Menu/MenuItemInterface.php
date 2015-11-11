<?php

namespace Modera\MjrIntegrationBundle\Menu;

use Modera\MjrIntegrationBundle\Sections\SectionInterface;

/**
 * Represents a menu item which will be rendered on client-side. All META_* constants are just a recommendation
 * you may or may not opt to.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface MenuItemInterface extends SectionInterface
{
    /**
     * A CSS icon class which may be used to render an icon in frontend.
     */
    const META_ICON = 'icon';

    /**
     * @return string A label that will be shown in UI
     */
    public function getLabel();

    /**
     * @see \Modera\MjrIntegrationBundle\Model\FontAwesome
     *
     * @return string A glyph to display in menu
     */
    public function getGlyph();
}
