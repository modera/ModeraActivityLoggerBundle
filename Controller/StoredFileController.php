<?php

namespace Modera\FileRepositoryBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\DependencyInjection\ModeraFileRepositoryExtension;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class StoredFileController extends Controller
{
    /**
     * @Route("/{storageKey}", name="modera_file_repository.get_file", requirements={"storageKey" = ".+"})
     *
     * @param Request $request
     * @param $storageKey
     *
     * @return Response
     */
    public function getAction(Request $request, $storageKey)
    {
        if (!$this->container->getParameter(ModeraFileRepositoryExtension::CONFIG_KEY.'.controller.is_enabled')) {
            throw $this->createAccessDeniedException();
        }

        return $this->createFileResponse($request, $storageKey);
    }

    /**
     * @param string $storageKey
     *
     * @return null|StoredFile
     */
    protected function getFile($storageKey)
    {
        /* @var ObjectRepository $repository */
        $repository = $this->getDoctrine()->getManager()->getRepository(StoredFile::clazz());

        return $repository->findOneBy(array(
            'storageKey' => $storageKey,
        ));
    }

    /**
     * @param Request $request
     * @param $storageKey
     *
     * @return Response
     */
    protected function createFileResponse(Request $request, $storageKey)
    {
        $response = new Response();

        $parts = explode('/', $storageKey);

        $file = $this->getFile($parts[0]);
        if (!$file) {
            return $response->setContent('File not found.')->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        if ($request->get('dl') !== null) {
            $filename = $file->getFilename();
            if (count($parts) > 1) {
                $filename = $parts[count($parts) - 1];
            }

            $filenameFallback = filter_var($filename, FILTER_SANITIZE_URL);
            if ($filenameFallback != $filename) {
                $guesser = ExtensionGuesser::getInstance();
                $extension = filter_var(
                    $guesser->guess($file->getMimeType()) ?: $file->getExtension(), FILTER_SANITIZE_URL
                );
                $filenameFallback = $file->getStorageKey().($extension ? '.'.$extension : '');
            }

            $response->headers->set(
                'Content-Disposition',
                $response->headers->makeDisposition(
                    ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename, $filenameFallback
                )
            );
        } else {
            $response->setCache(array(
                'etag' => $file->getStorageKey(),
                'last_modified' => $file->getCreatedAt(),
            ));

            if ($response->isNotModified($request)) {
                return $response;
            }
        }

        $response->setContent($file->getContents());
        $response->headers->set('Content-type', $file->getMimeType());
        $response->headers->set('Content-length', $file->getSize());

        return $response;
    }
}
