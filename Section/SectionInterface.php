<?php

namespace Modera\BackendToolsSettingsBundle\Section;

/**
 * A section contributed to "Backend / Tools / Settings" must implement this interface.
 *
 * @see Modera.backend.configutils.runtime.SettingsListActivity javascript class
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface SectionInterface
{
    /**
     * @return string  A technical ID that will be used in URL.
     */
    public function getId();

    /**
     * @return string  A human readable name of the section.
     */
    public function getName();

    /**
     * See http://fortawesome.github.io/Font-Awesome/icons/
     *
     * @return string  An optional glyph name that will be used as an icon for this section.
     */
    public function getGlyph();

    /**
     * @return string  Fully qualified javascript class name of activity class that will represent this section.
     */
    public function getActivityClass();

    /**
     * If your javascript activity class needs to accept some activation parameters then you can use 'activationParams'
     * configuration key in meta information. For example:
     *
     *     array(
     *         'activationParams' => array(
     *             'category' => 'my-fancy-settings'
     *         )
     *     )
     *
     * @return array Optional metadata this this section may have.
     */
    public function getMeta();
} 