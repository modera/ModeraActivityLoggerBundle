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
    public function getMessage(): string;

    /**
     * See \Psr\Log\LogLevel.
     */
    public function getLevel(): string;

    /**
     * In other words - category.
     */
    public function getType(): string;

    /**
     * Returned value could be anything that your logic can understand later and figure out who originally created this
     * activity - for example, it could contain an ID of your USER entity.
     */
    public function getAuthor(): string;

    /**
     * @return array<mixed>
     */
    public function getMeta(): array;

    public function getCreatedAt(): \DateTimeInterface;
}
