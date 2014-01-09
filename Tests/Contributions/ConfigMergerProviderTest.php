<?php

namespace Modera\BackendDashboardBundle\Tests\Contributions;

use Modera\BackendDashboardBundle\Contributions\ConfigMergersProvider;


/**
 * @author    Alex Rudakov <alexandr.rudakov@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ConfigMergerProviderTest extends \PHPUnit_Framework_TestCase {

    public function testItems()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');

        $provider = new ConfigMergersProvider($container, $contributor);

        $result = $provider->getItems();

        $this->assertTrue(is_array($result));
        $this->assertSame($provider, $result[0]);
    }

    public function testMerge_NoItems()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');

        \Phake::when($contributor)->getItems()->thenReturn(array());

        $provider = new ConfigMergersProvider($container, $contributor);

        $result = $provider->merge(array('foo' => 'bar'));

        $this->assertArrayHasKey('foo', $result);
        $this->assertEquals('bar', $result['foo']);

        $this->assertArrayHasKey('dashboards', $result);
        $this->assertEquals(array(), $result['dashboards']);
    }

    public function testMerge_HasItems()
    {
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');

        $item = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($item)->getName()->thenReturn('name1');
        \Phake::when($item)->getLabel()->thenReturn('label1');
        \Phake::when($item)->getUiClass()->thenReturn('class1');
        \Phake::when($item)->isAllowed($container)->thenReturn(true);

        \Phake::when($contributor)->getItems()->thenReturn(array($item));

        $provider = new ConfigMergersProvider($container, $contributor);

        $result = $provider->merge(array('foo' => 'bar'));

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
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');

        $itemAllowed = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($itemAllowed)->getName()->thenReturn('allowed_name1');
        \Phake::when($itemAllowed)->getLabel()->thenReturn('allowed_label1');
        \Phake::when($itemAllowed)->getUiClass()->thenReturn('allowed_class1');
        \Phake::when($itemAllowed)->isAllowed($container)->thenReturn(true);

        $itemNotAllowed = \Phake::mock('Modera\BackendDashboardBundle\Dashboard\DashboardInterface');
        \Phake::when($itemNotAllowed)->isAllowed($container)->thenReturn(false);

        \Phake::when($contributor)->getItems()->thenReturn(array($itemNotAllowed, $itemAllowed));

        $provider = new ConfigMergersProvider($container, $contributor);

        $result = $provider->merge(array('foo' => 'bar'));

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
        $container = \Phake::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $contributor = \Phake::mock('Sli\ExpanderBundle\Ext\ContributorInterface');

        $provider = new ConfigMergersProvider($container, $contributor);

        $this->assertSame($container, $provider->getContainer());
        $this->assertSame($contributor, $provider->getDashboardProvider());
    }
} 