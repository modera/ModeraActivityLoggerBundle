<?php

namespace Modera\TranslationsBundle\Tests\Unit\Service;

use Modera\TranslationsBundle\Handling\TranslationHandlerInterface;
use Modera\TranslationsBundle\Service\TranslationHandlersChain;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationHandlersChainTest extends \PHPUnit_Framework_TestCase
{
    public function testHandlers()
    {
        $handlersChain = new TranslationHandlersChain;
        $this->assertEquals(0, count($handlersChain->getHandlers()));

        $handlersChain->addHandler(new DummyHandler('test1'));
        $this->assertEquals(1, count($handlersChain->getHandlers()));

        $handlersChain->addHandler(new \stdClass('test2'));
        $this->assertEquals(1, count($handlersChain->getHandlers()));

        $handlersChain->addHandler(new DummyHandler('test3'));
        $this->assertEquals(2, count($handlersChain->getHandlers()));
    }
}

class DummyHandler implements TranslationHandlerInterface
{
    private $bundle;

    public function __construct($bundle)
    {
        $this->bundle = $bundle;
    }

    public function getBundleName()
    {
        return $this->bundle;
    }

    public function getSources()
    {
        return array('test');
    }

    public function extract($source, $locale)
    {
        return null;
    }
}
