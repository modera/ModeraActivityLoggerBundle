<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Repository;

use Modera\FileRepositoryBundle\Repository\UniqidKeyGenerator;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class UniqidKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function testWithExtension()
    {
        $g = new UniqidKeyGenerator(true);

        $filename = sys_get_temp_dir().'/foo.txt';
        file_put_contents($filename, '');

        $generatedFilename = $g->generateStorageKey(new \SplFileInfo($filename));

        $this->assertEquals('.txt', substr($generatedFilename, -1 * strlen('.txt')));
    }
}
