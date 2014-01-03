<?php

namespace Modera\FoundationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Controller provides a bunch of auxiliary methods.
 *
 * @copyright 2013 Modera Foundation
 * @author Sergei Lissovski <sergei.lissovski@modera.net>
 */
class AbstractBaseController extends Controller
{
    /**
     * Shortcut access to "doctrine.orm.entity_manager" service.
     *
     * @return \Doctrine\ORM\EntityManager $em
     */
    protected function em()
    {
        return $this->get('doctrine.orm.entity_manager');
    }

    /**
     * Shortcut access to "security.context" service.
     *
     * @return \Symfony\Component\Security\Core\SecurityContext
     */
    protected function sc()
    {
        return $this->get('security.context');
    }

    /**
     * @return string
     */
    private function getServicePrefix()
    {
        /* @var \Modera\FoundationBundle\Util\ControllerServiceNameFormatter $formatter */
        $formatter = $this->get('mf.foundation.util.controller_service_name_formatter');

        return $formatter->formatPrefix(get_class($this));
    }

    /**
     * Provides an easy way to access container services defined for this bundle.
     *
     * For a controller placed in MyCompany/Bundle/FooBundle or MyCompany/FooBundle for $id "my_service"
     * will return "mycompany.foo.my_service".
     *
     * @return Object
     */
    protected function service($id)
    {
        return $this->get($this->getServicePrefix().'.'.$id);
    }
}
