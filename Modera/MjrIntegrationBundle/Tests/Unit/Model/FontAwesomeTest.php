<?php

namespace Modera\MjrIntegrationBundle\Tests\Unit\Config;

use Modera\MjrIntegrationBundle\Model\FontAwesome;
use Symfony\Component\Yaml\Yaml;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class FontAwesomeTest extends \PHPUnit_Framework_TestCase
{
    public function testHowWellItWorks()
    {
        $path = dirname(__DIR__).'/../../Resources/config/font-awesome-icons.yml';
        $data = Yaml::parse(file_get_contents($path));

        foreach ($data['icons'] as $icon) {
            $value = 'x'.$icon['unicode'].'@FontAwesome';

            $this->assertEquals(FontAwesome::resolve($icon['id']), $value);
            $this->assertEquals(FontAwesome::resolve('fa-'.$icon['id']), $value);
            $this->assertEquals(FontAwesome::resolve(strtoupper(str_replace('-', '_', $icon['id']))), $value);

            if (isset($icon['aliases'])) {
                foreach ($icon['aliases'] as $alias) {
                    $this->assertEquals(FontAwesome::resolve($alias), $value);
                    $this->assertEquals(FontAwesome::resolve('fa-'.$alias), $value);
                    $this->assertEquals(FontAwesome::resolve(strtoupper(str_replace('-', '_', $alias))), $value);
                }
            }
        }

        $this->assertEquals(FontAwesome::resolve('not-found'), null);
        $this->assertEquals(FontAwesome::resolve('fa-not-found'), null);
        $this->assertEquals(FontAwesome::resolve('NOT_FOUND'), null);
    }
}
