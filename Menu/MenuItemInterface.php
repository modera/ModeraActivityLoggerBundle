<?php

namespace Modera\JSRuntimeIntegrationBundle\Menu;

/**
 * Represents a menu item which will be rendered on client-side. All META_* constants are just a recommendation
 * you may or may not opt to.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface MenuItemInterface
{
    /**
     * A CSS icon class which may be used to render an icon in frontend.
     */
    const META_ICON = 'icon';
    /**
     * A javascript namespace that all JS classes will reside in.
     */
    const META_NAMESPACE = 'namespace';
    /**
     * A path where JavaScript files mapped by META_NAMESPACE could be loaded from.
     */
    const META_NAMESPACE_PATH = 'namespace_mapping_path';
    /**
     * A security role that authenticated user must have in order to access a section represented by the menu-item
     */
    const META_REQUIRED_ROLE_TO_ACCESS = 'required_role_to_access';

    /**
     * @return string  A label that will be shown in UI
     */
    public function getLabel();

    /**
     * @return string  A javascript controller class name which will server as entry point to a section represented
     *                 by this menu item.
     */
    public function getController();

    /**
     * @return string  A short string which will be used to reference a section represented by this menu item.
     */
    public function getId();

    /**
     * @return array  Optional additional metadata that may be used by some server-side/client-side runtime
     *                components to treat this menu item in some special way. For example, you may add an css icon
     *                class that client-side UI will used to display a nice icon next to the label.
     */
    public function getMetadata();
}