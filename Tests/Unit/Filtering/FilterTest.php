<?php

namespace Modera\BackendTranslationsToolBundle\Tests\Unit\Filtering;

use Modera\BackendTranslationsToolBundle\Filtering\Filter;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    protected function setUp()
    {
        parent::setUp();

        $this->container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        \Phake::when($this->container)->get('modera_server_crud.persistence.default_handler')->thenReturn(new DummyDoctrinePersistenceHandler);
        \Phake::when($this->container)->get('doctrine.orm.entity_manager')->thenReturn(new DummyDoctrineEntityManager);
    }

    private function filterCheck($item, $id, $name)
    {
        $this->assertInstanceOf('Modera\BackendTranslationsToolBundle\Filtering\FilterInterface', $item);
        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals(true, $item->isAllowed());

        $result = $item->getResult(array());
        $this->assertTrue(is_array($result));

        $this->assertArrayHasKey('success', $result);
        $this->assertTrue($result['success']);

        $this->assertArrayHasKey('total', $result);
        $this->assertEquals(0, $result['total']);

        $this->assertArrayHasKey('items', $result);
        $this->assertTrue(is_array($result['items']));
    }

    public function testAllTranslationTokensFilter()
    {
        $item = new Filter\AllTranslationTokensFilter($this->container);
        $this->filterCheck($item, 'all', 'All');
    }

    public function testNewTranslationTokensFilter()
    {
        $item = new Filter\NewTranslationTokensFilter($this->container);
        $this->filterCheck($item, 'new', 'New');
    }

    public function testObsoleteTranslationTokensFilter()
    {
        $item = new Filter\ObsoleteTranslationTokensFilter($this->container);
        $this->filterCheck($item, 'obsolete', 'Obsolete');
    }
}

class DummyDoctrinePersistenceHandler
{
    public function getCount($className, array $params)
    {
        return 0;
    }

    public function query($className, array $params)
    {
        return array();
    }
}

class DummyDoctrineEntityManager
{
    public function createQuery()
    {
        return new DummyDoctrineQuery;
    }
}

class DummyDoctrineQuery
{
    public function getResult()
    {
        return array();
    }
}
