<?php

namespace Modera\FileUploaderBundle\Uploading;

use Modera\FileRepositoryBundle\Entity\StoredFile;
use Modera\FileRepositoryBundle\Repository\FileRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class RepositoryGateway implements UploadGatewayInterface
{
    private $fileRepository;
    private $repositoryName;

    /**
     * @param FileRepository $fileRepository
     * @param string         $repositoryName
     */
    public function __construct(FileRepository $fileRepository, $repositoryName)
    {
        $this->fileRepository = $fileRepository;
        $this->repositoryName = $repositoryName;
    }

    /**
     * {@inheritdoc}
     */
    public function isResponsible(Request $request)
    {
        $repository = $request->request->get('_repository');
        if (!$repository) {
            throw new \RuntimeException(
                'Unable to resolve what channel to use ( request parameter "_repository" is missing )'
            );
        }

        return $repository == $this->repositoryName;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(Request $request)
    {
        $this->beforeUpload($request);

        $storedFiles = $this->doUpload($request);

        $this->afterUpload($request, $storedFiles);

        return $this->formatResponse($request, $storedFiles);
    }

    /**
     * @param Request      $request
     * @param StoredFile[] $storedFiles
     *
     * @return JsonResponse|Response
     */
    protected function formatResponse(Request $request, array $storedFiles)
    {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'success' => true,
            ));
        } else {
            return new Response(true);
        }
    }

    /**
     * Method is invoked before files are uploaded, if you need to prevent files upload then you can throw an exception
     * in this method.
     *
     * Feel free to override this method in subclass.
     *
     * @param Request $request
     */
    protected function beforeUpload(Request $request)
    {
    }

    /**
     * Method is invoked when file(s) have been successfully uploaded.
     *
     * Feel free to override this method in subclass.
     *
     * @param Request      $request
     * @param StoredFile[] $storedFiles
     */
    protected function afterUpload(Request $request, array $storedFiles)
    {
    }

    /**
     * @param Request $request
     *
     * @return StoredFile[]
     */
    protected function doUpload(Request $request)
    {
        $storedFiles = array();

        foreach ($request->files as $file) {
            if ($file) {
                $storedFiles[] = $this->fileRepository->put($this->repositoryName, $file);
            }
        }

        return $storedFiles;
    }
}
