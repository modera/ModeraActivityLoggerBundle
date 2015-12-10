<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Entity;

use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\Exceptions\InvalidRepositoryConfig;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $thrownException = null;

        try {
            new Repository('foo', array());
        } catch (InvalidRepositoryConfig $e) {
            $thrownException = $e;
        }

        $this->assertNotNull($thrownException);
        $this->assertEquals('filesystem', $thrownException->getMissingConfigurationKey());
        $this->assertEquals(array(), $thrownException->getConfig());
    }

    private function createInterceptor()
    {
        $incp = \Phake::mock('Modera\FileRepositoryBundle\Intercepting\OperationInterceptorInterface');

        return $incp;
    }

    public function testInterceptors()
    {
        $interceptors = array(
            $this->createInterceptor(),
        );

        $interceptorsProvider = \Phake::mock('Modera\FileRepositoryBundle\Intercepting\InterceptorsProviderInterface');
        \Phake::when($interceptorsProvider)
            ->getInterceptors($this->isInstanceOf(Repository::clazz()))
            ->thenReturn($interceptors)
        ;

        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        \Phake::when($container)
            ->get('modera_file_repository.intercepting.interceptors_provider')
            ->thenReturn($interceptorsProvider)
        ;

        $repository = new Repository('foo', array('filesystem' => 'foo'));
        $repository->init($container);

        $splFile = new \SplFileInfo(__FILE__);
        $storedFile = \Phake::mock(StoredFile::clazz());

        // ---

        $repository->beforePut($splFile);

        \Phake::verify($interceptors[0])->beforePut($splFile, $repository);

        // ---

        $repository->onPut($storedFile, $splFile, $repository);

        \Phake::verify($interceptors[0])->onPut($storedFile, $splFile, $repository);

        // ---

        $repository->afterPut($storedFile, $splFile, $repository);

        \Phake::verify($interceptors[0])->afterPut($storedFile, $splFile, $repository);
    }
}
