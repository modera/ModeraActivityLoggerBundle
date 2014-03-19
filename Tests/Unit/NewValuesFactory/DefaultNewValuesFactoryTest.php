<?php

namespace Modera\ServerCrudBundle\Tests\Unit\NewValuesFactory;

use Modera\ServerCrudBundle\NewValuesFactory\DefaultNewValuesFactory;

class DummyEntity
{
    static public function clazz()
    {
        return get_called_class();
    }
}

class AnotherDummyEntity
{
    static public function clazz()
    {
        return get_called_class();
    }

    static public function formatNewValues(array $params, array $config)
    {
        return array(
            'params' => $params,
            'config' => $config
        );
    }
}

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DefaultNewValuesFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetValues()
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');

        $nvf = new DefaultNewValuesFactory($container);

        $inputParams = array('input-params');
        $inputConfig = array('entity' => DummyEntity::clazz());

        $this->assertSame(array(), $nvf->getValues($inputParams, $inputConfig));

        // ---

        $inputConfig = array('entity' => AnotherDummyEntity::clazz());

        $expectedResult = array(
            'params' => $inputParams,
            'config' => $inputConfig
        );

        $this->assertSame($expectedResult, $nvf->getValues($inputParams, $inputConfig));
    }
} 