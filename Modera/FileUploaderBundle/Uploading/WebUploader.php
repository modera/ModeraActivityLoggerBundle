<?php

namespace Modera\FileUploaderBundle\Uploading;

use Sli\ExpanderBundle\Ext\ContributorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class WebUploader
{
    private $gatewaysProvider;

    /**
     * @param ContributorInterface $gatewaysProvider
     */
    public function __construct(ContributorInterface $gatewaysProvider)
    {
        $this->gatewaysProvider = $gatewaysProvider;
    }

    /**
     * @param Request $request
     */
    public function upload(Request $request)
    {
        foreach ($this->gatewaysProvider->getItems() as $gateway) {
            /* @var UploadGatewayInterface $gateway */

            if ($gateway->isResponsible($request)) {
                if ($result = $gateway->upload($request)) {
                    return $result;
                }
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public static function clazz()
    {
        return get_called_class();
    }
}
