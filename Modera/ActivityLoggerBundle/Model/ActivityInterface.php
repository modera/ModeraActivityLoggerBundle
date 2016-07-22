<?php

namespace Modera\ActivityLoggerBundle\Model;

/**
 * Declares a bunch of methods that all activities must have.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface ActivityInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * See \Psr\Log\LogLevel
     *
     * @return string
     */
    public function getLevel();

    /**
     * In other words - category.
     *
     * @return string
     */
    public function getType();

    /**
     * Returned value could be anything that your logic can understand later and figure out who originally created this
     * activity - for example, it could contain an ID of your USER entity.
     *
     * @return string
     */
    public function getAuthor();

    /**
     * @return array
     */
    public function getMeta();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}