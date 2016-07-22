<?php

namespace Modera\SecurityBundle\Model;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class PermissionCategory implements PermissionCategoryInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $technicalName;

    /**
     * @param string $name
     * @param string $technicalName
     */
    public function __construct($name, $technicalName)
    {
        $this->name = $name;
        $this->technicalName = $technicalName;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTechnicalName()
    {
        return $this->technicalName;
    }
}
