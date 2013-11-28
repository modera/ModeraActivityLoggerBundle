<?php

namespace Modera\BackendModuleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Neton\DirectBundle\Annotation\Remote;

class DefaultController extends Controller
{
    /**
     * @Remote
     *
     * @param array $params
     */
    public function getInstalledModulesAction(array $params)
    {
        return array(
            array('id' => 1, 'name' => 'Foo module'),
            array('id' => 2, 'name' => 'Bar module')
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function getAvailableModulesAction(array $params)
    {
        return array(
            array('id' => 1, 'name' => 'Foo module'),
            array('id' => 2, 'name' => 'Bar module'),
            array('id' => 3, 'name' => 'Mega module')
        );
    }

    /**
     * @Remote
     *
     * @param array $params
     */
    public function getModuleDetailsAction(array $params)
    {
        return array(
            'id' => 1,
            'name' => 'Some module name'
        );
    }
}
