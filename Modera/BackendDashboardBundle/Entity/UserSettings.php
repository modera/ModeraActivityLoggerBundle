<?php

namespace Modera\BackendDashboardBundle\Entity;

use Modera\SecurityBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 *
 * @ORM\Entity
 * @ORM\Table(name="modera_dashboard_userdashboardsettings")
 */
class UserSettings implements SettingsEntityInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @Orm\OneToOne(targetEntity="Modera\SecurityBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="array")
     */
    private $dashboardSettings = array(
        'defaultDashboard' => null,
        'hasAccess' => []
    );

    static public function clazz()
    {
        return get_called_class();
    }

    /**
     * @param string $dashboardId
     * @return bool
     */
    public function hasAccessToDashboard($dashboardId)
    {
        $bs = $this->getDashboardSettings();

        return isset($bs['hasAccess']) && is_array($bs['hasAccess']) && in_array($dashboardId, $bs['hasAccess']);
    }

    /**
     * @return string|null
     */
    public function getDefaultDashboardId()
    {
        $bs = $this->getDashboardSettings();

        return isset($bs['defaultDashboard']) ? $bs['defaultDashboard'] : null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $dashboardSettings
     */
    public function setDashboardSettings(array $dashboardSettings)
    {
        $this->dashboardSettings = $dashboardSettings;
    }

    /**
     * @return array
     */
    public function getDashboardSettings()
    {
        return $this->dashboardSettings;
    }

    /**
     * @param \Modera\SecurityBundle\Entity\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \Modera\SecurityBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function describeEntity()
    {
        return sprintf('User "%s"', $this->getUser()->getUsername());
    }
}