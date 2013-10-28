<?php

namespace Modera\JSRuntimeIntegrationBundle\Menu;

/**
 * Standard immutable implementation ( only metadata can be modified after an instance of this class is created ).
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class MenuItem implements MenuItemInterface
{
    private $label;
    private $controller;
    private $id;
    private $metadata = array();

    /**
     * @param string $label
     * @param string $controller
     * @param string $id
     * @param array  $metadata
     */
    public function __construct($label, $controller, $id, array $metadata = array())
    {
        $this->label = $label;
        $this->controller = $controller;
        $this->id = $id;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}