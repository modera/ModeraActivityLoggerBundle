<?php
/**
 * @copyright 2013 Modera Foundation
 * @author Alex Rudakov <alexandr.rudakov@modera.net>
 */
 

namespace Modera\BackendDashboardBundle\Dashboard;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implement this interface in case you want to provide your custom dashboard
 *
 * @package Modera\BackendDashboardBundle\Dashboard
 * @copyright 2013 Modera Foundation
 * @author    Alex Rudakov <alexandr.rudakov@modera.net>
 */
interface DashboardInterface {

    /**
     * Technical name of dashboard. Used as a key in arrays/db/forms
     *
     * @return string
     */
    public function getName();

    /**
     * Human readable name of dashboard.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Extjs ui class that will serve the dashboard.
     *
     * @return string
     */
    public function getUiClass();

    /**
     * Checks if dashboard is allowed within given environment.
     * May be used as security check, but also may check for installed modules, settings etc.
     *
     * @param ContainerInterface $container
     *
     * @return bool
     */
    public function isAllowed(ContainerInterface $container);
} 