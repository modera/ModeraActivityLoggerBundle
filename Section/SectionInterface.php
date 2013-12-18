<?php

namespace Modera\BackendToolsBundle\Section;

/**
 * Represents an item that will be displayed in backend' Tools section.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface SectionInterface
{
    /**
     * @return string
     */
    public function getGlyph();

    /**
     * @return string
     */
    public function getIconSrc();

    /**
     * @return string
     */
    public function getIconClass();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return array  Optional metadata
     */
    public function getMeta();

    /**
     * @return string  ID of a section to activate
     */
    public function getSection();

    /**
     * @return array
     */
    public function getSectionActivationParams();
}