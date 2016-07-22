<?php

namespace Modera\TranslationsBundle\Service;

use Modera\TranslationsBundle\Handling\TranslationHandlerInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class TranslationHandlersChain
{
    /**
     * @var array
     */
    private $handlers = array();

    /**
     * @param $handler
     */
    public function addHandler($handler)
    {
        if ($handler instanceof TranslationHandlerInterface) {
            $this->handlers[] = $handler;
        }
    }

    /**
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
