<?php

namespace Modera\FileUploaderBundle\Controller;

use Modera\FileUploaderBundle\Uploading\WebUploader;
use Modera\FoundationBundle\Translation\T;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class UniversalUploaderController extends Controller
{
    /**
     * @Route("%modera_file_uploader.uploader_url%", name="modera_file_uploader", options={"expose"=true})
     *
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        if (!$this->container->getParameter('modera_file_uploader.is_enabled')) {
            throw new \RuntimeException('Uploader is not enabled.');
        }

        /* @var WebUploader $webUploader */
        $webUploader = $this->get('modera_file_uploader.uploading.web_uploader');

        $result = $webUploader->upload($request);
        if (false === $result) {
            return new JsonResponse(array(
                'success' => false,
                'error' => T::trans('Unable to find an upload gateway that is able to process this file upload.')
            ));
        }

        return $result;
    }
} 