<?php

namespace Modera\AdminGeneratorBundle\Generation;

use Modera\AdminGeneratorBundle\Generation\Generators\Section;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
interface ViewInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * Create a fully qualified class name that this view generates.
     *
     * @return string
     */
    public function createClassName(Section $section);

    /**
     * What event will be used to activate this view.
     *
     * @return string
     */
    public function getActivationEventName();

    /**
     * @return string
     */
    public function isResponsibleForClass($className, Section $section);

    /**
     * @return GenerationResult
     */
    public function generate($className, Section $section);
}