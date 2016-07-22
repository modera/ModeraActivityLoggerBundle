<?php

namespace Modera\FileRepositoryBundle\Util;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2015 Modera Foundation
 */
class StoredFileUtils
{
    /**
     * @param $size
     *
     * @return string
     */
    public static function formatFileSize($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[$power];
    }

    final private function __construct()
    {
    }
}
