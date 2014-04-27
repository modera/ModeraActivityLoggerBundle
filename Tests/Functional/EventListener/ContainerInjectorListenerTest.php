<?php

namespace Modera\FileRepositoryBundle\Tests\Functional\EventListener;

use Doctrine\ORM\Tools\SchemaTool;
use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Sli\AuxBundle\Util\Toolkit;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */ 
class ContainerInjectorListenerTest extends FunctionalTestCase
{
    /* @var SchemaTool */
    static private $st;

    // override
    static public function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema([self::$em->getClassMetadata(Repository::clazz())]);
    }

    // override
    static public function doTearDownAfterClass()
    {
        self::$st->dropSchema([self::$em->getClassMetadata(Repository::clazz())]);
    }

    public function testHowWellContainerIsInjected()
    {
        $repository = new Repository('test repo');

        self::$em->persist($repository);
        self::$em->flush();

        self::$em->clear();

        /* @var Repository $repository */
        $repository = self::$em->getRepository(Repository::clazz())->find($repository->getId());

        $this->assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerInterface',
            Toolkit::getPropertyValue($repository, 'container')
        );
    }
}