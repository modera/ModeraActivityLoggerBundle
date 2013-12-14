<?php

namespace Modera\AdminGeneratorBundle\Controller;

use Modera\FoundationBundle\Controller\AbstractBaseController;
use Neton\DirectBundle\Annotation\Remote;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class DataController extends AbstractBaseController
{
    private function getRecord()
    {
        return array(
            'id' => 1,
            'firstname' => 'Sergei',
            'lastname' => 'Lissovski',
            'personalCode' => '38812210283'
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function listAction(array $params)
    {
        return array(
            $this->getRecord()
        );
    }

    /**
     * @Remote
     */
    public function createAction(array $params)
    {
        return array(
            'updated_models' => array(),
            'created_models' => array()
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function removeAction(array $params)
    {
        return array(
            'records_removed' => array('customer'),
            'success' => true
        );
    }

    /**
     * @Remote
     */
    public function getNewRecordValuesAction(array $params)
    {
        return array(
            'firstname' => '?'
        );
    }

    /**
     * @Remote
     */
    public function updateAction(array $params)
    {
        return array(
            'success' => true,
            'updated_models' => array(
                'modera.ecommerce.customer'
            )
        );
    }

    /**
     * @Remote
     */
    public function getAction(array $params)
    {
        return array(
            'success' => true,
            'record' => $this->getRecord()
        );
    }
}