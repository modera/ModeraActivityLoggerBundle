<?php
/**
 * @copyright 2013 Modera Foundation
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */

namespace Modera\BackendDashboardBundle\Dashboard;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SimpleDashboard
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class SimpleDashboard implements DashboardInterface
{
    private $name;
    private $label;
    private $uiClass;

    /**
     * @param string $name    Technical name of dashboard
     * @param string $label   Human readable label
     * @param string $uiClass ExtJs class that provide ui (Derivative of Ext.container.Container or similar)
     * @param string $description
     * @param string $icon
     */
    public function __construct($name, $label, $uiClass, $description='', $icon='modera-backend-dashboard-default-icon')
    {
        $this->label = $label;
        $this->name = $name;
        $this->uiClass = $uiClass;
        $this->description = $description;
        $this->icon = $icon;
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
     * Short dashboard description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Short dashboard description
     *
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
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
     * Return label
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
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