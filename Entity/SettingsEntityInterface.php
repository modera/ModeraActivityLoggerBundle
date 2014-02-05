<?php


namespace Modera\BackendDashboardBundle\Entity;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface SettingsEntityInterface
{
    public function getId();

    /**
     * @param array $settings
     *
     * @return mixed
     */
    public function setDashboardSettings(array $settings);

    /**
     * @return array
     */
    public function getDashboardSettings();

    /**
     * @return array
     */
    public function describeEntity();
}