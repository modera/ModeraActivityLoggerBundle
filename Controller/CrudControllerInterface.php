<?php

namespace Modera\ServerCrudBundle\Controller;

/**
 * Defines a set of methods that crud controllers must have. Expected structure of $params arguments is up to
 * implementations.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface CrudControllerInterface
{
    /**
     * Method must return an array that can be used on client-side as a template for creating a new record.
     *
     * @param array $params
     *
     * @return array
     */
    public function getNewRecordValuesAction(array $params);

    /**
     * Method is responsible for creating a new record and persisting it to database.
     *
     * @param array $params
     *
     * @return array
     */
    public function createAction(array $params);

    /**
     * Method is responsible for updating already existing in persistent storage piece of data.
     *
     * @param array $params
     *
     * @return array
     */
    public function updateAction(array $params);

    /**
     * Method must return hydrated instance of your record by querying database using query provided in
     * $params.
     *
     * @param array $params
     *
     * @return array
     */
    public function getAction(array $params);

    /**
     * Method must return many hydrated records by querying database using query defined in $params.
     *
     * @param array $params
     *
     * @return array
     */
    public function listAction(array $params);

    /**
     * Method is responsible for deleting one or many records by analyzing a query provided by $params.
     *
     * @param array $params
     *
     * @return array
     */
    public function removeAction(array $params);
}
