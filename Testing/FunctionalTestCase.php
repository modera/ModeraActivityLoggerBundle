<?php

namespace Modera\FoundationBundle\Testing;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * A base test case that you may extend when writing your functional tests, it allows you
 * to configure so-called isolation level of your test methods ( override getIsolationLevel() method ). Isolation level controls:
 * - at which point database transaction is discarded
 * - if your test has authenticated a user, then at which moment it will be automatically logged out
 * Two isolation levels are available:
 * - method -- After every test method transaction is discarded and user is logged out. This option is used by default.
 * - class -- Transaction will be discarded and user logged out only when last test method has finished its execution.
 *
 * This class has marked methods "setUp", "tearDown", "setUpBeforeClass", "tearDownAfterClass" as final and if you still
 * need to use them then you need to use "template methods" instead, just add "do" prefix to a method you need,
 * for instance, if you want to override "setUp" method then use "doSetUp" method instead.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
class FunctionalTestCase extends WebTestCase
{
    const IM_METHOD = 'method';
    const IM_CLASS = 'class';

    /* @var \Doctrine\ORM\EntityManager */
    static protected $em;
    /* @var \Symfony\Component\DependencyInjection\ContainerInterface */
    static protected $container;

    static private function rollbackTransaction()
    {
        $c = static::$em->getConnection();
        // having this check if there's an active transaction will let us
        // to use this TC as parent-class for integration test cases
        // even if they don't use EM
        if ($c->isTransactionActive()) {
            $c->rollback();
        }
    }

    static private function emExists()
    {
        return self::$container->has('doctrine.orm.entity_manager');
    }

    /**
     * {@inheritDoc}
     */
    final static public function setUpBeforeClass()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        static::$container = static::$kernel->getContainer();

        if (self::emExists()) {
            static::$em = static::$container->get('doctrine.orm.entity_manager');
        }

        if (static::getIsolationLevel() == self::IM_CLASS && self::emExists()) {
            static::$em->getConnection()->beginTransaction();
        }

        static::doSetUpBeforeClass();
    }

    /**
     * Template method.
     */
    static public function doSetUpBeforeClass()
    {

    }

    /**
     * {@inheritDoc}
     */
    final static public function tearDownAfterClass()
    {
        if (static::getIsolationLevel() == self::IM_CLASS && self::emExists()) {
            static::rollbackTransaction();
        }

        static::doTearDownAfterClass();
    }

    /**
     * Template method.
     */
    static public function doTearDownAfterClass()
    {

    }

    /**
     * Override this method to change isolation level of the test.
     *
     * @return string
     */
    static protected function getIsolationLevel()
    {
        return self::IM_METHOD;
    }

    /**
     * {@inheritDoc}
     */
    final public function setUp()
    {
        if ($this->getIsolationLevel() == self::IM_METHOD && $this->emExists()) {
            self::$em->getConnection()->beginTransaction();
        }

        $this->doSetUp();
    }

    /**
     * Template method
     */
    public function doSetUp()
    {
    }

    /**
     * {@inheritDoc}
     */
    final public function tearDown()
    {
        if ($this->getIsolationLevel() == self::IM_METHOD && $this->emExists()) {
            self::rollbackTransaction();
        }

        if (self::$container->has('security.context')) {
            $this->logoutUser();
        }

        $this->doTearDown();
    }

    /**
     * Template method
     */
    public function doTearDown()
    {
    }

    /**
     * Will logout currently authenticated user.
     */
    public function logoutUser()
    {
        /* @var SecurityContextInterface $securityContext */
        $securityContext = self::$container->get('security.context');
        $securityContext->setToken(null);
    }
}