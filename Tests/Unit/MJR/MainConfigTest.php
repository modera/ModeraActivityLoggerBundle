<?php

namespace Modera\DynamicallyConfigurableMJRBundle\Tests\Unit\MJR;

use Modera\DynamicallyConfigurableMJRBundle\MJR\MainConfig;
use Modera\DynamicallyConfigurableMJRBundle\ModeraDynamicallyConfigurableMJRBundle as Bundle;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class MainConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MainConfig
     */
    private $mc;

    private $mgr;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->mgr = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntriesManagerInterface');
        $this->mc = new MainConfig($this->mgr);
    }

    private function teachManager($expectedKey, $returnValue)
    {
        $entry = \Phake::mock('Modera\ConfigBundle\Config\ConfigurationEntryInterface');
        \Phake::when($entry)
            ->getValue()
            ->thenReturn($returnValue)
        ;

        \Phake::when($this->mgr)
            ->findOneByNameOrDie($expectedKey)
            ->thenReturn($entry)
        ;
    }

    public function testGetTitle()
    {
        $this->teachManager(Bundle::CONFIG_TITLE, 'footitle');

        $this->assertEquals('footitle', $this->mc->getTitle());
    }

    public function testGetUrl()
    {
        $this->teachManager(Bundle::CONFIG_URL, 'foourl');

        $this->assertEquals('foourl', $this->mc->getUrl());
    }

    public function testGetHomeSection()
    {
        $this->teachManager(Bundle::CONFIG_HOME_SECTION, 'foosection');

        $this->assertEquals('foosection', $this->mc->getHomeSection());
    }
}
