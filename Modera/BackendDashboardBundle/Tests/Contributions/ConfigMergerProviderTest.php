<?php

namespace Modera\BackendDashboardBundle\Tests\Contributions;

use Modera\BackendDashboardBundle\Contributions\ConfigMergersProvider;


/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergerProviderTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * @var \Sli\ExpanderBundle\Ext\ContributorInterface
     */
    private $contributor;
    /**
     * @var ConfigMergersProvider
     */
    private $provider;

    protected function setUp()
    {
        parent::setUp();

        $this->container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');
        $this->provider = new ConfigMergersProvider($this->container, $this->contributor);
    }


    public function testItems()
    {
        $result = $this->provider->getItems();

        $this->assertTrue(is_array($result));
        $this->assertSame($this->provider, $result[0]);
    }

    public function testMerge_NoItems()
    {
        \Phake::when($this->contributor)->getItems()->thenReturn(array());

        $result = $this->provider->merge(array('foo' => 'bar'));

        $this->assertArrayHasKey('foo', $result);
        $this->assertEquals('bar', $result['foo']);

        $this->assertArrayHasKey('dashboards', $result);
        $this->assertEquals(array(), $result['dashboards']);
    }

    public function testMerge_HasItems()
    {
        $item = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($item)->getName()->thenReturn('name1');
        \Phake::when($item)->getLabel()->thenReturn('label1');
        \Phake::when($item)->getUiClass()->thenReturn('class1');
        \Phake::when($item)->isAllowed($this->container)->thenReturn(true);

        \Phake::when($this->contributor)->getItems()->thenReturn(array($item));

        $result = $this->provider->merge(array('foo' => 'bar'));

        $this->assertArrayHasKey('foo', $result);
        $this->assertEquals('bar', $result['foo']);

        $this->assertArrayHasKey('dashboards', $result);

        $this->assertEquals(array(
                'name' => 'name1',
                'label' => 'label1',
                'uiClass' => 'class1',
                'default' => true
            ), $result['dashboards'][0]);
    }

    public function testMerge_HasItems_NotAllowed()
    {
        $itemAllowed = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($itemAllowed)->getName()->thenReturn('allowed_name1');
        \Phake::when($itemAllowed)->getLabel()->thenReturn('allowed_label1');
        \Phake::when($itemAllowed)->getUiClass()->thenReturn('allowed_class1');
        \Phake::when($itemAllowed)->isAllowed($this->container)->thenReturn(true);

        $itemNotAllowed = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($itemNotAllowed)->isAllowed($this->container)->thenReturn(false);

        \Phake::when($this->contributor)->getItems()->thenReturn(array($itemNotAllowed, $itemAllowed));

        $result = $this->provider->merge(array('foo' => 'bar'));

        $this->assertArrayHasKey('foo', $result);
        $this->assertEquals('bar', $result['foo']);

        $this->assertArrayHasKey('dashboards', $result);

        $this->assertEquals(array(
                'name' => 'allowed_name1',
                'label' => 'allowed_label1',
                'uiClass' => 'allowed_class1',
                'default' => true
            ), $result['dashboards'][0]);
    }

    public function testGetters()
    {
        $this->assertSame($this->container, $this->provider->getContainer());
        $this->assertSame($this->contributor, $this->provider->getDashboardProvider());
    }
} 