<?php

namespace Modera\FileUploaderBundle\Uploading;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
interface UploadGatewayInterface
{
    /**
     * @param Request $request
     */
    public function isResponsible(Request $request);

    /**
     * @param Request $request
     */
    public function upload(Request $request);
}
