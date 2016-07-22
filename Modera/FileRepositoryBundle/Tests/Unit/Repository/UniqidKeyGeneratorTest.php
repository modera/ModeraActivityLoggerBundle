<?php

namespace Modera\FileRepositoryBundle\Tests\Unit\Repository;

use Modera\FileRepositoryBundle\Repository\UniqidKeyGenerator;

/**
 * @author Sergei Lissovski <sergei.lissovski@gmail.com>
 */
class UniqidKeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \SplFileInfo
     */
    private $file;

    public function setUp()
    {
        $pathname = sys_get_temp_dir().'/foo.txt';
        file_put_contents($pathname, '');

        $this->file = new \SplFileInfo($pathname);
    }

    public function testWithExtension()
    {
        $g = new UniqidKeyGenerator(true);

        $generatedFilename = $g->generateStorageKey($this->file);

        $this->assertEquals('.txt', substr($generatedFilename, -1 * strlen('.txt')));
    }

    public function testWithoutExtension()
    {
        $g = new UniqidKeyGenerator();

        $filename = $g->generateStorageKey($this->file);

        $this->assertTrue('.'.substr($filename, -1 * strlen($this->file->getExtension())) != '.'.$this->file->getExtension());
    }
}
