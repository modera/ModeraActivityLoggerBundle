<?php

namespace Modera\BackendToolsBundle\Section;

/**
 * A basic immutable implementation of {@class SectionInterface}.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class Section implements SectionInterface
{
    private $glyph;
    private $iconSrc;
    private $iconClass;
    private $name;
    private $description;
    private $meta;
    private $section;
    private $sectionActivationParams;

    /**
     * @param string $name
     * @param string $section
     * @param string $description
     * @param string $glyph
     * @param string $iconClass
     * @param array $sectionActivationParams
     * @param array $meta
     */
    public function __construct($name, $section, $description = '', $glyph = '', $iconSrc = '', $iconClass = '', array $sectionActivationParams = array(), array $meta = array())
    {
        $this->name = $name;
        $this->section = $section;
        $this->description = $description;
        $this->glyph = $glyph;
        $this->iconSrc = $iconSrc;
        $this->iconClass = $iconClass;
        $this->sectionActivationParams = $sectionActivationParams;
        $this->meta = $meta;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getGlyph()
    {
        return $this->glyph;
    }

    /**
     * @inheritDoc
     */
    public function getIconSrc()
    {
        return $this->iconSrc;
    }

    /**
     * @inheritDoc
     */
    public function getIconClass()
    {
        return $this->iconClass;
    }

    /**
     * @inheritDoc
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * @inheritDoc
     */
    public function getSectionActivationParams()
    {
        return $this->sectionActivationParams;
    }
}