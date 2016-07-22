<?php

namespace Modera\ServerCrudBundle\Exceptions;

/**
 * Exception will be thrown when exactly one entity was expected to be returned from database but in fact several
 * entities have been returned.
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class MoreThanOneResultException extends \RuntimeException
{
}
