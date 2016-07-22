<?php

namespace Modera\BackendDashboardBundle\Dashboard;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Implement this interface in case you want to provide your custom dashboard
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface DashboardInterface
{

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
     * Short dashboard description
     *
     * @return string
     */
    public function getDescription();

    /**
     * Icon cls
     *
     * @return string
     */
    public function getIcon();

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