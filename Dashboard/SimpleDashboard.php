<?php
/**
 * @copyright 2013 Modera Foundation
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
 

namespace Modera\BackendDashboardBundle\Dashboard;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SimpleDashboard implements DashboardInterface
{
    private $name;
    private $label;
    private $uiClass;

    function __construct($name, $label, $uiClass)
    {
        $this->label = $label;
        $this->name = $name;
        $this->uiClass = $uiClass;
    }


    /**
     * Set name
     *
     * @param string $name
     *
     * @return SimpleDashboard
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * Return uiClass
     *
     * @return mixed
     */
    public function getUiClass()
    {
        return $this->uiClass;
    }

    /**
     * Set uiClass
     *
     * @param mixed $uiClass
     *
     * @return SimpleDashboard
     */
    public function setUiClass($uiClass)
    {
        $this->uiClass = $uiClass;

        return $this;
    }

    /**
     * Return label
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param mixed $label
     *
     * @return SimpleDashboard
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Checks if dashboard is allowed within given environment.
     * May be used as security check, but also may check for installed modules, settings etc.
     *
     * @param ContainerInterface $container
     *
     * @return bool
     */
    public function isAllowed(ContainerInterface $container)
    {
        return true; // whatever
    }
}