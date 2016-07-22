<?php

namespace Modera\ServerCrudBundle\Exceptions;

/**
 * Exception can be thrown when we expect at least one result to be returned from database but in fact nothing has really
 * been fetched.
 *
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class NothingFoundException extends \RuntimeException
{
}
