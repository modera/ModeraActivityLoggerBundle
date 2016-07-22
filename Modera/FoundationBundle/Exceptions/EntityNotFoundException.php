<?php

namespace Modera\FoundationBundle\Exceptions;

/**
 * Exception can be thrown when you expect to have entity returned when you queried database but nothing
 * was really found.
 *
 * @copyright 2013 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.net>
 */
class EntityNotFoundException extends \RuntimeException
{
    /**
     * Fully qualified class name of exception
     *
     * @var string
     */
    private $entityClass;

    /**
     * A query/criteria/dql/sql/you name it you used when tried to find the entity. For example:
     * - array('id' => 5)
     * - array('fistname' => 'John', 'lastname' => 'Doe')
     * - SELECT u FROM MyCompanyFooBundle:User u WHERE u.id = ?0
     *
     * @var mixed
     */
    private $query;

    private $queryParams = array();

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $entityClass
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setQueryParams(array $queryParams)
    {
        $this->queryParams = $queryParams;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }
}
