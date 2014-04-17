<?php

namespace Modera\BackendToolsSettingsBundle\Section;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class StandardSection implements SectionInterface
{
    private $id;
    private $name;
    private $activityClass;
    private $glyph;

    /**
     * @param string $id
     * @param string $name
     * @param string $activityClass
     */
    public function __construct($id, $name, $activityClass, $glyph = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->activityClass = $activityClass;
        $this->glyph = $glyph;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
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
    public function getActivityClass()
    {
       return $this->activityClass;
    }

    /**
     * @inheritDoc
     */
    public function getGlyph()
    {
        return $this->glyph;
    }
}