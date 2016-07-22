<?php

namespace Modera\BackendDashboardBundle\Entity;

use Modera\SecurityBundle\Entity\Group;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 *
 * @ORM\Entity
 * @ORM\Table(name="modera_dashboard_groupdashboardsettings")
 */
class GroupSettings implements SettingsEntityInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Group
     *
     * @Orm\OneToOne(targetEntity="Modera\SecurityBundle\Entity\Group")
     */
    private $group;

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
     * @param \Modera\SecurityBundle\Entity\Group $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return \Modera\SecurityBundle\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function describeEntity()
    {
        return sprintf('Group "%s"', $this->getGroup()->getName());
    }
}