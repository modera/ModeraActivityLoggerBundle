<?php

namespace Modera\FoundationBundle\Tests\Unit\Translation;

use Symfony\Component\Translation\TranslatorInterface;
use Modera\FoundationBundle\Translation\T;

class MockTranslator implements TranslatorInterface
{
    public function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        return array($id, $parameters, $domain, $locale);
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null)
    {
        return array($id, $number, $parameters, $domain, $locale);
    }

    public function setLocale($locale)
    {
    }

    public function getLocale()
    {
    }
}

/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TTest extends \PHPUnit_Framework_TestCase
{
    private $t;
    private $c;
    private $reflMethod;

    // override
    public function setUp()
    {
        $this->t = new MockTranslator() ;

        $this->c = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->c->expects($this->atLeastOnce())
             ->method('get')
             ->with($this->equalTo('translator'))
             ->will($this->returnValue($this->t));

        $reflClass = new \ReflectionClass(T::clazz());
        $this->reflMethod = $reflClass->getProperty('container');
        $this->reflMethod->setAccessible(true);
        $this->reflMethod->setValue(null, $this->c);
    }

    // override
    public function tearDown()
    {
        $this->reflMethod->setValue(null, null);
    }

    public function testTrans()
    {
        $expectedOutput = array(
            'foo id', array('params'), 'foo domain', 'foo locale'
        );

        $this->assertSame($expectedOutput, T::trans('foo id', array('params'), 'foo domain', 'foo locale'));
    }

    public function testTransChoice()
    {
        $expectedOutput = array(
            'foo id', 'foo number', array('params'), 'foo domain', 'foo locale'
        );

        $this->assertSame(
            $expectedOutput,
            T::transChoice('foo id', 'foo number', array('params'), 'foo domain', 'foo locale')
        );
    }
}