<?php

namespace Modera\MJRCacheAwareClassLoaderBundle\Controller;

use Modera\MJRCacheAwareClassLoaderBundle\VersionResolving\VersionResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class DefaultController extends Controller
{
    /**
     * @Route(path="%modera_mjr_cache_aware_class_loader.route%", name="modera_mjr_cache_aware_class_loader")
     */
    public function classLoaderAction()
    {
        /* @var VersionResolverInterface $versionProvider */
        $versionProvider = $this->get('modera_mjr_cache_aware_class_loader.version_resolver');

        $content = $this->renderView('ModeraMJRCacheAwareClassLoaderBundle:Default:class-loader.html.twig', array(
            'version' => trim($versionProvider->resolve()),
        ));

        return new Response($content, 200, array(
            'Content-Type' => 'application/javascript',
        ));
    }
}
