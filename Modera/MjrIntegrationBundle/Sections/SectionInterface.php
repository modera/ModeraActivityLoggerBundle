<?php

namespace Modera\MjrIntegrationBundle\Sections;

/**
 * Represents a section which can be loaded in backend.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface SectionInterface
{
    /**
     * A javascript namespace that all JS classes will reside in.
     */
    const META_NAMESPACE = 'namespace';
    /**
     * A path where JavaScript files mapped by META_NAMESPACE could be loaded from.
     */
    const META_NAMESPACE_PATH = 'namespace_mapping_path';
    /**
     * A security role that authenticated user must have in order to access a section represented by the menu-item.
     */
    const META_REQUIRED_ROLE_TO_ACCESS = 'required_role_to_access';
    /**
     * Activation parameters for section.
     */
    const META_ACTIVATION_PARAMS = 'activation_params';

    /**
     * @return string A short string which will be used to reference a section represented by this menu item.
     */
    public function getId();

    /**
     * @return string A javascript controller class name which will serve as entry point to a section represented
     *                by this menu item.
     */
    public function getController();

    /**
     * @return array Optional additional metadata that may be used by some server-side/client-side runtime
     *               components to treat this menu item in some special way.
     */
    public function getMetadata();
}
