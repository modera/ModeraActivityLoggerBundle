<?php

namespace Modera\ActivityLoggerBundle\Manager;

use Modera\ActivityLoggerBundle\Model\ActivityInterface;
use Psr\Log\LoggerInterface;

/**
 * One remark regarding $context parameter of all logging methods that {@class Psr\Log\LoggerInterface} provides:
 * when using $context array parameter you may use two special keys - 'type' and 'author'. 'type' parameter will
 * later be used to categorize activities and 'author' can contain identificator of a component or a physical user
 * who caused given activity. For example:
 *     $activityMgr->info('User has logged in', array('type' => 'security', 'author' => $user->getId()));
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ActivityManagerInterface extends LoggerInterface
{
    /**
     * Allows to query logged activities.
     *
     * @param array $query
     * @return ActivityInterface[]
     */
    public function query(array $query);
} 