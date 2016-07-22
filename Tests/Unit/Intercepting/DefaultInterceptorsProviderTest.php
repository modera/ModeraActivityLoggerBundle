<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Intercepting;

use Modera\FileRepositoryBundle\Entity\Repository;
use Modera\FileRepositoryBundle\Intercepting\DefaultInterceptorsProvider;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2015 Modera Foundation
 */
class DefaultInterceptorsProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInterceptors()
    {
        $wannabeInterceptor = new \stdClass();
        $anotherWannabeInterceptor = new \stdClass();

        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        \Phake::when($container)
            ->get('modera_file_repository.validation.file_properties_validation_interceptor')
            ->thenReturn($wannabeInterceptor)
        ;
        \Phake::when($container)
            ->get('foo_interceptor')
            ->thenReturn($anotherWannabeInterceptor)
        ;

        $repository = \Phake::mock(Repository::clazz());

        $provider = new DefaultInterceptorsProvider($container);

        $result = $provider->getInterceptors($repository);

        $this->assertEquals(1, count($result));
        $this->assertSame($wannabeInterceptor, $result[0]);

        // and now with a "interceptors" config:

        \Phake::when($repository)
            ->getConfig()
            ->thenReturn(array('interceptors' => array('foo_interceptor')))
        ;

        $result = $provider->getInterceptors($repository);

        $this->assertEquals(2, count($result));
        $this->assertSame($wannabeInterceptor, $result[0]);
        $this->assertSame($anotherWannabeInterceptor, $result[1]);
    }
}
