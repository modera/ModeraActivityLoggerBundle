<?php

namespace Modera\FileRepositoryBundle\StoredFile;

use Symfony\Component\Routing\RouterInterface;
use Modera\FileRepositoryBundle\Entity\StoredFile;

/**
 * This implementation of url generator provide publicly accessible url through the controller action.
 *
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @param RouterInterface $router
     * @param string          $routeName
     */
    public function __construct(RouterInterface $router, $routeName)
    {
        $this->router = $router;
        $this->routeName = $routeName;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUrl(StoredFile $storedFile)
    {
        $storageKey = $storedFile->getStorageKey();
        $storageKey .= '/'.$storedFile->getRepository()->getName();
        $storageKey .= '/'.$storedFile->getFilename();

        return $this->router->generate($this->routeName, array(
            'storageKey' => $storageKey,
        ), RouterInterface::NETWORK_PATH);
    }
}
