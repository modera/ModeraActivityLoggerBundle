<?php

namespace Modera\ConfigBundle\Tests\Functional\Entity;

use Modera\CoreBundle\Util\IntegrationTestCase;
use Modera\ConfigBundle\Entity\ConfigurationEntry as CE;
use Modera\FoundationBundle\Testing\FunctionalTestCase;

/**
 * @author Sergei Lissovski <sergei.lissovski@modera.org>
 */
class ConfigurationEntryTest extends FunctionalTestCase
{
    public function testSetClientValueAndGetClientValue()
    {
        $em = self::$em;

        $intEntry = new CE('entry1');
        $this->assertEquals('entry1', $intEntry->getName());
        $this->assertEquals(CE::TYPE_INT, $intEntry->setDenormalizedValue(123));
        $this->assertEquals(123, $intEntry->getDenormalizedValue());

        $stringValue = new CE('entry2');
        $this->assertEquals(CE::TYPE_STRING, $stringValue->setDenormalizedValue('blahblah'));
        $this->assertEquals('blahblah', $stringValue->getDenormalizedValue());

        $textValue = new CE('entry3');
        $this->assertEquals(CE::TYPE_TEXT, $textValue->setDenormalizedValue(str_repeat('foo', 100)));
        $this->assertEquals(str_repeat('foo', 100), $textValue->getDenormalizedValue());

        $arrayValue = new CE('entry4');
        $this->assertEquals(CE::TYPE_ARRAY, $arrayValue->setDenormalizedValue(array('foo')));
        $this->assertSame(array('foo'), $arrayValue->getDenormalizedValue());

        $floatValue = new CE('entry5');
        $this->assertEquals(CE::TYPE_FLOAT, $floatValue->setDenormalizedValue(1.2345));
        $this->assertEquals(1.2345, $floatValue->getDenormalizedValue());

        $floatValue2 = new CE('entry6');
        $this->assertEquals(CE::TYPE_FLOAT, $floatValue2->setDenormalizedValue(0.009));
        $this->assertEquals(0.009, $floatValue2->getDenormalizedValue());

        $boolValue = new CE('entry7');
        $this->assertEquals(CE::TYPE_BOOL, $boolValue->setDenormalizedValue(true));
        $this->assertTrue(true === $boolValue->getDenormalizedValue());

        foreach (array($intEntry, $stringValue, $textValue, $arrayValue, $floatValue, $floatValue2, $boolValue) as $ce) {
            $em->persist($ce);
            $em->flush();
            $this->assertNotNull($intEntry->getId());
        }

        $em->clear();

        /* @var CE $floatValue2 */
        $floatValue2 = self::$em->find(CE::clazz(), $floatValue2->getId());
        $this->assertEquals(CE::TYPE_FLOAT, $floatValue2->getSavedAs());
        $this->assertTrue(is_float($floatValue2->getValue()));
        $this->assertEquals(0.009, $floatValue2->getValue());
    }

    public function testInitialization()
    {
        $ce = new CE('greeting_msg');
        $ce->setDenormalizedValue('hello world');

        $em = self::$em;
        $em->persist($ce);
        $em->flush();
        $em->getUnitOfWork()->clear();

        $ce = $em->getRepository(CE::clazz())->findOneBy(array(
            'name' => 'greeting_msg'
        ));
        $this->assertInstanceOf(CE::clazz(), $ce);
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\ContainerInterface', $ce->getContainer());
    }

    private function createMockContainer($handlerId, $handlerInstance)
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->atLeastOnce())
            ->method('get')
            ->with($this->equalTo($handlerId))
            ->will($this->returnValue($handlerInstance));

        return $container;
    }

    public function testGetValue()
    {
        $handlerServiceId = 'foo_handler';
        $expectedValue = 'jfksdljfdks';

        $handler = $this->getMock('Modera\ConfigBundle\Config\HandlerInterface');
        $handler->expects($this->atLeastOnce())
            ->method('getValue')
            ->with($this->isInstanceOf(CE::clazz()))
            ->will($this->returnValue($expectedValue));

        $container = $this->createMockContainer($handlerServiceId, $handler);

        $ce = new CE('bar_prop');
        $ce->setServerHandlerConfig(array(
            'id' => $handlerServiceId
        ));
        $ce->init($container);
        $ce->setDenormalizedValue('foo_val');

        $this->assertEquals($expectedValue, $ce->getValue());
    }

    public function testSetValue()
    {
        $handlerServiceId = 'bar_handler';

        $clientValue = 'foo bar baz';
        $convertedValue = 'converted foo bar baz';

        $handler = $this->getMock('Modera\ConfigBundle\Config\HandlerInterface');
        $handler->expects($this->atLeastOnce())
               ->method('convertToStorageValue')
               ->with($this->equalTo($clientValue), $this->isInstanceOf(CE::clazz()))
               ->will($this->returnValue($convertedValue));

        $container = $this->createMockContainer($handlerServiceId, $handler);

        $ce = new CE('bar_prop');
        $ce->setServerHandlerConfig(array(
            'id' => $handlerServiceId
        ));
        $ce->init($container);

        $ce->setValue($clientValue);
        $this->assertEquals($convertedValue, $ce->getDenormalizedValue());
    }

    /**
     * It has to be the last test because after this exception EntityManager is closed.
     *
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testNoDuplicateConfigurationProperties()
    {
        $em = self::$em;

        $ce1 = new CE('prop1');
        $ce1->setDenormalizedValue('blah');

        $ce2 = new CE('prop1');
        $ce2->setDenormalizedValue('blah2');

        foreach (array($ce1, $ce2) as $entity) {
            $em->persist($entity);
        }
        $em->flush();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testSetClientValueWithBadValue()
    {
        $ce = new CE('blah');
        $ce->setDenormalizedValue(new \stdClass());
    }
}
