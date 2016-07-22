<?php

namespace Modera\BackendLanguagesBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class LanguageListener
{
    public function setLocale(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ('/route' == $request->getPathInfo()) {
            $session = $request->getSession();
            $locale = $session->get('_backend_locale', $request->getLocale());
            $request->setLocale($locale);
        }
    }
}
