<?php

namespace Modera\FileRepositoryBundle\Tests\Functional\DependencyInjection;

use Modera\FileUploaderBundle\DependencyInjection\ModeraFileUploaderExtension;
use Modera\FoundationBundle\Testing\FunctionalTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class ModeraFileUploaderExtensionTest extends FunctionalTestCase
{
    private $ext;
    /* @var ContainerBuilder */
    private $cb;

    public function doSetUp()
    {
        $this->ext = new ModeraFileUploaderExtension();
        $this->cb = new ContainerBuilder();
    }

    public function testLoad()
    {
        $this->ext->load(array(), $this->cb);

        $cfg = $this->cb->getParameter(ModeraFileUploaderExtension::CONFIG_KEY);
        $this->assertTrue(is_array($cfg));
        $this->assertArrayHasKey('is_enabled', $cfg);
        $this->assertArrayHasKey('url', $cfg);
        $this->assertArrayHasKey('expose_all_repositories', $cfg);
        $this->assertTrue($cfg['expose_all_repositories']);

        $gateway = $this->cb->getDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway');
        $this->assertNotNull($gateway);

        $provider = $this->cb->getDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway_provider');
        $this->assertNotNull($provider);
    }

    public function testLoadWhenRepositoriesAreNotExposed()
    {
        $config = array(
            array(
                'expose_all_repositories' => false,
            ),
        );

        $this->ext->load($config, $this->cb);

        $this->assertFalse(
            $this->cb->hasDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway')
        );
        $this->assertFalse(
            $this->cb->hasDefinition('modera_file_uploader.uploading.all_exposed_repositories_gateway_provider')
        );
    }
}
