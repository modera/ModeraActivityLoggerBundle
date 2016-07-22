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
    private static $st;

    /**
     * {@inheritdoc}
     */
    public static function doSetUpBeforeClass()
    {
        self::$st = new SchemaTool(self::$em);
        self::$st->createSchema(array(self::$em->getClassMetadata(Repository::clazz())));
    }

    /**
     * {@inheritdoc}
     */
    public static function doTearDownAfterClass()
    {
        self::$st->dropSchema(array(self::$em->getClassMetadata(Repository::clazz())));
    }

    public function testHowWellContainerIsInjected()
    {
        $repository = new Repository('test repo', array(
            'filesystem' => '',
            'storage_key_generator' => '',
        ));

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
